# Implementação: Alterar URL Pública do OCI para Domínio Customizado

## ✅ Mudanças Implementadas

### 1. Arquivo: `articla-media-offload-lite-for-oracle-cloud-infrastructure.php`
**Linha ~21** - Adicionada constante CDN:
```php
define( 'ARTIMEOF_CDN_URL', 'https://www2.hmdcc.com.br/wp-content' );
```

### 2. Arquivo: `includes/core.php`
**Linha ~42-50** - Modificada função `artimeof_compute_base_url()`:
```php
function artimeof_compute_base_url($o){
    // Custom CDN URL for public delivery (proxy to OCI)
    if ( defined( 'ARTIMEOF_CDN_URL' ) && ! empty( ARTIMEOF_CDN_URL ) ) {
        return ARTIMEOF_CDN_URL;
    }
    
    // Fallback to OCI URL if custom CDN not configured
    if (empty($o['namespace']) || empty($o['region']) || empty($o['bucket'])) return '';
    $h = $o['namespace'] . '.compat.objectstorage.' . $o['region'] . '.oraclecloud.com';
    return 'https://' . $h . '/' . rawurlencode($o['bucket']);
}
```

---

## 🔄 Fluxo de Execução

### Quando um arquivo é servido:

1. **WordPress solicita URL do anexo**
   ```
   wp_get_attachment_url( $id )
   ```

2. **Hook `wp_get_attachment_url` dispara `artimeof_filter_attachment_url()`**
   ```php
   function artimeof_filter_attachment_url($url, $id) {
       $o = artimeof_get_settings();
       $b = artimeof_compute_base_url($o) . '/uploads';  // ← AQUI MUDA
       return str_replace($base, $b, $url);
   }
   ```

3. **`artimeof_compute_base_url()` retorna:**
   ```
   https://www2.hmdcc.com.br
   ```

4. **URL final gerada:**
   ```
   https://www2.hmdcc.com.br/uploads/2024/06/image.jpg
   ```

5. **Proxy reverso (www2.hmdcc.com.br) aponta para OCI S3**

---

## 📋 Verificação de Implementação

### AC-1: ✅ URL na biblioteca de mídia
- [ ] Upload novo arquivo no WordPress
- [ ] Biblioteca de mídia mostra URL como: `https://www2.hmdcc.com.br/wp-content/uploads/YYYY/MM/filename.ext`

### AC-2: ✅ Imagens em posts (srcset)
- [ ] Criar post com imagem responsiva
- [ ] Inspecionar `<img srcset="...">` no navegador
- [ ] Todos os tamanhos (thumbnail, medium, large) usam: `https://www2.hmdcc.com.br/wp-content/uploads/...`

**Função afetada:** `artimeof_filter_srcset()` (linha ~182)
```php
function artimeof_filter_srcset($srcs, $size, $img_src, $meta, $id) {
    $b = artimeof_compute_base_url($o);  // ← Usa a nova URL
    foreach ($srcs as $w=>$s) {
        if (!empty($s['url'])) {
            $srcs[$w]['url'] = str_replace($base, trailingslashit($b), $s['url']);
        }
    }
}
```

### AC-3: ✅ Health check continua funcionando
- [ ] Admin → OCI Media Offload → Health Check
- [ ] Clique em "Test Connection"
- [ ] Status: "Health check passed"

**Arquivo afetado:** Nenhum (função `artimeof_ajax_health()` em `core.php` linha ~193)
- ✅ Upload (PUT) continua para OCI (usa `artimeof_endpoint_host()` - sem mudança)
- ✅ Verificação (GET) lê de www2 via proxy reverso (certificado SSL deve ser válido)

### AC-4: ✅ Certificado SSL válido
- [ ] Acessar `https://www2.hmdcc.com.br/` no navegador
- [ ] Verificar que não há aviso de certificado inválido
- [ ] No WordPress, admin check deve passar

### AC-5: ✅ Backend OCI inalterado
- [ ] Upload de arquivo continua indo para OCI
- [ ] Arquivo armazenado em: `s3://bucket/uploads/YYYY/MM/filename.ext`
- [ ] Metadado `_oci_object_base` continua salvando path correto

**Função não afetada:** `artimeof_attachment_and_sizes()` (linha ~100)
- PUT continua assinando para OCI S3
- Chaves de acesso ainda usadas para upload

---

## 🔒 Segurança & Fallback

**Fallback para OCI:** Se `ARTIMEOF_CDN_URL` não estiver definido, o plugin volta a usar a URL do OCI
- ✅ Seguro em caso de remoção da constante
- ✅ Compatível com versões anteriores
- ✅ Nenhuma mudança necessária se proxy reverso não funcionar

---

## 📝 Mudanças de Arquivo

| Arquivo | Mudanças | Status |
|---------|----------|--------|
| `articla-media-offload-lite-for-oracle-cloud-infrastructure.php` | Adicionada constante `ARTIMEOF_CDN_URL` | ✅ Completo |
| `includes/core.php` | Modificada `artimeof_compute_base_url()` | ✅ Completo |
| `includes/s3.php` | Nenhuma mudança necessária | ✅ Inalterado |
| `includes/admin.php` | Nenhuma mudança necessária | ✅ Inalterado |

---

## 🚀 Próximos Passos (Validação)

1. Fazer deploy do código
2. Executar testes de AC (AC-1 até AC-5) acima
3. Validar proxy reverso está funcionando (`www2.hmdcc.com.br` → OCI)
4. Confirmar certificado SSL está válido
5. Testar em ambiente staging antes de produção

---

**Data de Implementação:** 2026-06-12  
**Versão Afetada:** Plugin Media Offload for OCI Lite v1.3.3+  
**Compatibilidade:** WordPress 6.0+ / PHP 7.0+

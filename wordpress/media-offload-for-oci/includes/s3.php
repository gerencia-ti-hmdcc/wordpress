<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

function artimeof_public_url($o,$k){
    $b = artimeof_compute_base_url($o);
    return trailingslashit($b) . ltrim($k,'/');
}

function artimeof_endpoint_host($o){
    return $o['namespace'] . '.compat.objectstorage.' . $o['region'] . '.oraclecloud.com';
}

function artimeof_sign_headers($o,$method,$canon_uri,$payload_hash,$extra=array()){
    $service='s3'; $region=$o['region']; $access=$o['access_key']; $secret=$o['secret_key'];
    $amz_date=gmdate('Ymd\THis\Z'); $date_stamp=gmdate('Ymd'); $host=artimeof_endpoint_host($o);
    $h = array_change_key_case($extra, CASE_LOWER);
    $h['host'] = $host;
    $h['x-amz-date'] = $amz_date;
    $h['x-amz-content-sha256'] = $payload_hash;
    ksort($h);
    $canon=''; $signed=array();
    foreach($h as $k=>$v){ $canon .= $k . ':' . trim($v) . "\n"; $signed[] = $k; }
    $signed_str = implode(';',$signed);
    $canonical_request = implode("\n", array($method,$canon_uri,'',$canon,$signed_str,$payload_hash));
    $algorithm='AWS4-HMAC-SHA256'; $scope=$date_stamp.'/'.$region.'/'.$service.'/aws4_request';
    $string_to_sign = implode("\n", array($algorithm,$amz_date,$scope,hash('sha256',$canonical_request)));
    $kDate=hash_hmac('sha256',$date_stamp,'AWS4'.$secret,true);
    $kRegion=hash_hmac('sha256',$region,$kDate,true);
    $kService=hash_hmac('sha256',$service,$kRegion,true);
    $kSigning=hash_hmac('sha256','aws4_request',$kService,true);
    $sig=hash_hmac('sha256',$string_to_sign,$kSigning);
    $h['authorization'] = $algorithm.' Credential='.$access.'/'.$scope.', SignedHeaders='.$signed_str.', Signature='.$sig;
    return $h;
}

function artimeof_put_file($o,$key,$file,$ctype='application/octet-stream'){
    if (!file_exists($file)) return new WP_Error('file_missing','Local file not found');
    $body = file_get_contents($file);
    if ($body === false) return new WP_Error('read_failed','Failed to read file');
    return artimeof_put_data($o,$key,$body,$ctype);
}

function artimeof_put_data($o,$key,$data,$ctype='application/octet-stream'){
    $hash = hash('sha256',$data);
    $uri = '/' . rawurlencode($o['bucket']) . '/uploads/' . implode('/', array_map('rawurlencode', explode('/', $key)));
    $headers = array('content-type'=>$ctype,'content-length'=>strlen($data));
    $signed = artimeof_sign_headers($o,'PUT',$uri,$hash,$headers);
    $url = 'https://' . artimeof_endpoint_host($o) . $uri;
    $resp = wp_remote_request($url, array('method'=>'PUT','timeout'=>30,'headers'=>$signed,'body'=>$data));
    if (is_wp_error($resp)) return $resp;
    $code = wp_remote_retrieve_response_code($resp);
    if ($code>=200 && $code<300) return true;
    return new WP_Error('put_failed','PUT failed with HTTP '.$code.' — '.substr(wp_remote_retrieve_body($resp),0,200));
}

<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

function artimeof_get_settings(){
    $o = get_option(ARTIMEOF_LITE_OPT, array());
    return is_array($o) ? $o : array();
}

function artimeof_save_settings($r){
    $c = array();
    $c['region']      = sanitize_text_field( isset($r['region']) ? $r['region'] : '' );
    $c['namespace']   = sanitize_text_field( isset($r['namespace']) ? $r['namespace'] : '' );
    $c['access_key']  = sanitize_text_field( isset($r['access_key']) ? $r['access_key'] : '' );
    if ( isset($r['secret_key']) && $r['secret_key'] !== '' ) $c['secret_key'] = $r['secret_key'];
    $c['bucket']      = sanitize_text_field( isset($r['bucket']) ? $r['bucket'] : '' );
    $c['offload_new'] = !empty($r['offload_new']) ? 1 : 0;
    $c['keep_local']  = !empty($r['keep_local']) ? 1 : 0;
    $fs               = isset($r['folder_style']) ? $r['folder_style'] : '';
    $c['folder_style']= in_array($fs, array('yearmonth','flat'), true) ? $fs : 'yearmonth';
    $c['configured']  = !empty($r['configured']) ? 1 : 0;

    $e = get_option(ARTIMEOF_LITE_OPT, array());
    $m = array_merge($e, $c);
    update_option(ARTIMEOF_LITE_OPT, $m);
    return $m;
}

function artimeof_log($m,$l='info'){
    $logs = get_option(ARTIMEOF_LITE_LOG, array());
    $logs[] = array('t'=>gmdate('c'),'level'=>$l,'msg'=>wp_strip_all_tags($m));
    if (count($logs) > 200) $logs = array_slice($logs, -200);
    update_option(ARTIMEOF_LITE_LOG, $logs, false);
}


function artimeof_config_ready($o){
    // Require namespace, region, bucket, access & secret keys for signing
    return ( !empty($o['namespace']) && !empty($o['region']) && !empty($o['bucket']) && !empty($o['access_key']) && !empty($o['secret_key']) );
}


function artimeof_compute_base_url($o){
    if (empty($o['namespace']) || empty($o['region']) || empty($o['bucket'])) return '';
    $h = $o['namespace'] . '.compat.objectstorage.' . $o['region'] . '.oraclecloud.com';
    return 'https://' . $h . '/' . rawurlencode($o['bucket']);
}

function artimeof_object_key_for_attachment($id,$rel){
    $rel = ltrim($rel,'/');
    return str_replace(' ','%20',$rel);
}

function artimeof_maybe_queue_new_attachment($id){
    set_transient('artimeof_new_'.$id, 1, 5*MINUTE_IN_SECONDS);
}

function artimeof_on_metadata_update($d,$id){
    $o = artimeof_get_settings();
    if (empty($o['configured']) || empty($o['offload_new'])) return $d;
    if (get_post_meta($id,'_artimeofed',true)) return $d;

    $r = artimeof_attachment_and_sizes($id,$d,$o);
    if ($r === true) {
        update_post_meta($id,'_artimeofed',1);
        artimeof_log('Offloaded attachment ID '.$id);
        if (empty($o['keep_local'])) {
            artimeof_delete_local_files($id,$d);
            artimeof_log('Deleted local files for ID '.$id);
        }
    } else if (is_wp_error($r)) {
        artimeof_log('Failed offload ID '.$id.': '.$r->get_error_message(), 'error');
    }
    return $d;
}

function artimeof_delete_local_files($id,$d){
    $u = wp_get_upload_dir();
    $b = trailingslashit($u['basedir']);
    $p = array();
    if (!empty($d['file'])) $p[] = $b.$d['file'];
    if (!empty($d['sizes']) && is_array($d['sizes'])) {
        $dir = pathinfo($d['file'], PATHINFO_DIRNAME);
        foreach ($d['sizes'] as $s) if (!empty($s['file'])) $p[] = $b.trailingslashit($dir).$s['file'];
    }
    $orig = get_attached_file($id);
    if ($orig && !in_array($orig,$p,true)) $p[] = $orig;
    foreach ($p as $x) { if ( file_exists($x) ) { wp_delete_file( $x ); } }
}

function artimeof_attachment_and_sizes($id,$d,$o){
    if (empty($d['file'])) return true;
    $u = wp_get_upload_dir();
    $bd = trailingslashit($u['basedir']);
    $files = array();
    $main = $d['file'];
    $files[] = $bd.$main;
    $dir = pathinfo($main, PATHINFO_DIRNAME);
    if (!empty($d['sizes']) && is_array($d['sizes'])) {
        foreach ($d['sizes'] as $inf) if (!empty($inf['file'])) $files[] = $bd.trailingslashit($dir).$inf['file'];
    }
    foreach ($files as $fp) {
        if (!file_exists($fp)) continue;
        $rel = ltrim(str_replace($bd,'',$fp),'/');
        $key = artimeof_object_key_for_attachment($id,$rel);
        $mime = 'application/octet-stream';
        if (function_exists('wp_check_filetype')) {
            $_ft = wp_check_filetype($fp);
            if (is_array($_ft) && !empty($_ft['type'])) $mime = $_ft['type'];
        }
        $put = artimeof_put_file($o,$key,$fp,$mime);
        if (is_wp_error($put)) return $put;
    }
    update_post_meta($id,'_oci_object_base',dirname($d['file']));
    return true;
}

function artimeof_filter_attachment_url($url,$id){
    if (!get_post_meta($id,'_artimeofed',true)) return $url;
    $o = artimeof_get_settings();
    $b = artimeof_compute_base_url($o).'/uploads';
    if (empty($b)) return $url;
    $u = wp_get_upload_dir();
    $base = untrailingslashit($u['baseurl']);
    return str_replace($base,$b,$url);
}

function artimeof_filter_srcset($srcs,$size,$img_src,$meta,$id){
    if (!get_post_meta($id,'_artimeofed',true)) return $srcs;
    $o = artimeof_get_settings();
    $b = artimeof_compute_base_url($o);
    if (empty($b)) return $srcs;
    $u = wp_get_upload_dir();
    $base = trailingslashit($u['baseurl']);
    foreach ($srcs as $w=>$s) if (!empty($s['url'])) $srcs[$w]['url'] = str_replace($base, trailingslashit($b), $s['url']);
    return $srcs;
}

function artimeof_ajax_health(){
    if (!current_user_can('manage_options')) wp_send_json_error(array('msg'=>'forbidden'),403);
    check_ajax_referer('artimeof_health');
    $o = artimeof_get_settings();

    // Validate config before attempting
    if ( ! artimeof_config_ready($o) ) {
        artimeof_log('Health check failed: incomplete config (namespace/region/bucket/keys)', 'error');
        wp_send_json_error(array('msg'=>'incomplete_config'),400);
    }
    artimeof_log('Health check: starting');
    $key = 'artimeof-health-'.( function_exists('wp_generate_password') ? wp_generate_password(8,false) : substr(md5(uniqid('',true)),0,8) ).'.txt';
    $data = 'ok:'.time();
    $put = artimeof_put_data($o,$key,$data,'text/plain');
    if (is_wp_error($put)) {
        artimeof_log('Health check PUT failed: '.$put->get_error_message(), 'error');
        wp_send_json_error(array('msg'=>$put->get_error_message()));
    }
    artimeof_log('Health check: PUT ok');
    $url = artimeof_public_url($o,$key);
    $r = wp_remote_get($url,array('timeout'=>10));
    $ok = (!is_wp_error($r) && wp_remote_retrieve_response_code($r) === 200);
    if ($ok) { artimeof_log('Health check GET ok'); wp_send_json_success(array('ok'=>true,'url'=>$url)); }
    $msg = is_wp_error($r) ? $r->get_error_message() : ('HTTP '.wp_remote_retrieve_response_code($r).' '.substr(wp_remote_retrieve_body($r),0,200));
    artimeof_log('Health check GET failed: '.$msg,'error');
    wp_send_json_error(array('msg'=>$msg));

}

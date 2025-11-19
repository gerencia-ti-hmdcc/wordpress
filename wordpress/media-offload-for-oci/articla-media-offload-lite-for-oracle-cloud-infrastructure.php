<?php
/**
 * Plugin Name: Articla media offload lite for oracle cloud infrastructure
 * Description: Offload WordPress media to Oracle Cloud (OCI) Object Storage via the S3-compatible path-style endpoint. One-page setup. (CDN/PAR & Backfill available in PRO)
 * Version: 1.3.3
 * Requires at least: 6.0
 * Requires PHP: 7.0
 * Author: Articla79
 * License: GPL-2.0+
 * Text Domain: articla-media-offload-lite-for-oracle-cloud-infrastructure
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
if (version_compare(PHP_VERSION, '7.0', '<')) { add_action('admin_notices', function(){ echo '<div class="notice notice-error"><p>OCI Media Offload requires PHP 7.0+. Current: ' . esc_html(PHP_VERSION) . '</p></div>'; }); return; }
if (!function_exists('hash_hmac')) { add_action('admin_notices', function(){ echo '<div class="notice notice-error"><p>OCI Media Offload: PHP hash extension is missing.</p></div>'; }); return; }

define( 'ARTIMEOF_LITE_VER', '1.3.3' );
define( 'ARTIMEOF_LITE_DIR', plugin_dir_path( __FILE__ ) );
define( 'ARTIMEOF_LITE_URL', plugin_dir_url( __FILE__ ) );
define( 'ARTIMEOF_LITE_OPT', 'artimeof_settings' );
define( 'ARTIMEOF_LITE_LOG', 'artimeof_logs' );





require_once ARTIMEOF_LITE_DIR . 'includes/core.php';
require_once ARTIMEOF_LITE_DIR . 'includes/admin.php';
require_once ARTIMEOF_LITE_DIR . 'includes/s3.php';

// Back-compat: migrate old option names to new ones on load
add_action('plugins_loaded', function(){
    $old = get_option('oci_offload_settings');
    if ( $old && ! get_option( ARTIMEOF_LITE_OPT ) ) {
        update_option( ARTIMEOF_LITE_OPT, $old, false );
    }
    $oldlog = get_option('oci_offload_logs');
    if ( $oldlog && ! get_option( ARTIMEOF_LITE_LOG ) ) {
        update_option( ARTIMEOF_LITE_LOG, $oldlog, false );
    }
});

register_activation_hook(__FILE__, function(){
    if (!get_option(ARTIMEOF_LITE_OPT)) {
        update_option(ARTIMEOF_LITE_OPT, array(
            'region'=>'','namespace'=>'','access_key'=>'','secret_key'=>'','bucket'=>'',
            'offload_new'=>1,'keep_local'=>1,'folder_style'=>'yearmonth','configured'=>0
        ), false);
    }
    if (!get_option(ARTIMEOF_LITE_LOG)) update_option(ARTIMEOF_LITE_LOG, array(), false);
});

add_action('admin_enqueue_scripts', function($hook){
    // Load assets only on our admin screen
    if ('toplevel_page_artimeof' === $hook) {
        wp_enqueue_style('artimeof-admin', ARTIMEOF_LITE_URL . 'assets/admin.css', array(), ARTIMEOF_LITE_VER);
        wp_enqueue_script('artimeof-admin', ARTIMEOF_LITE_URL . 'assets/admin.js', array(), ARTIMEOF_LITE_VER, true);
        wp_localize_script('artimeof-admin', 'artimeof', array(
            'nonce'   => wp_create_nonce('artimeof_health'),
            'ajaxUrl' => admin_url('admin-ajax.php'),
        ));
    }
});

add_filter('plugin_action_links_' . plugin_basename(__FILE__), function($links){
    $links[] = '<a href="'.esc_url(admin_url('admin.php?page=artimeof')).'">'.esc_html__('Settings','articla-media-offload-lite-for-oracle-cloud-infrastructure').'</a>';
    return $links;
});

add_action('add_attachment','artimeof_maybe_queue_new_attachment');
add_filter('wp_update_attachment_metadata','artimeof_on_metadata_update',10,2);
add_filter('wp_get_attachment_url','artimeof_filter_attachment_url',10,2);
add_filter('wp_calculate_image_srcset','artimeof_filter_srcset',10,5);
add_action('wp_ajax_artimeof_health','artimeof_ajax_health');
add_action('wp_ajax_oci_offload_health','artimeof_ajax_health');

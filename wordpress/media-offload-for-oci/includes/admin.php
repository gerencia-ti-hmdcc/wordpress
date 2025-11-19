<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Admin menu
 */
add_action( 'admin_menu', function () {
    add_menu_page(
        __( 'OCI Offload', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ),
        __( 'OCI Offload', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ),
        'manage_options',
        'artimeof',
        'artimeof_admin_page',
        'dashicons-cloud',
        82
    );
} );

/**
 * Admin notice when not configured
 */
add_action( 'admin_notices', function () {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    $o = artimeof_get_settings();
    if ( empty( $o['configured'] ) ) {
        echo '<div class="notice notice-warning"><p>' . esc_html__( 'OCI Media Offload is not configured. Open the wizard to finish setup.', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ) . '</p></div>';
    }
} );

/**
 * Regions list
 */
function artimeof_regions() {
    return array(
        ''                 => __( 'Select a region', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ),
        // Americas
        'us-ashburn-1'     => 'us-ashburn-1',
        'us-phoenix-1'     => 'us-phoenix-1',
        'ca-toronto-1'     => 'ca-toronto-1',
        'ca-montreal-1'    => 'ca-montreal-1',
        'sa-saopaulo-1'    => 'sa-saopaulo-1',
        'sa-santiago-1'    => 'sa-santiago-1',
        // EMEA
        'uk-london-1'      => 'uk-london-1',
        'eu-frankfurt-1'   => 'eu-frankfurt-1',
        'eu-paris-1'       => 'eu-paris-1',
        'eu-zurich-1'      => 'eu-zurich-1',
        'eu-milan-1'       => 'eu-milan-1',
        'eu-madrid-1'      => 'eu-madrid-1',
        'eu-stockholm-1'   => 'eu-stockholm-1',
        // Middle East
        'me-dubai-1'       => 'me-dubai-1',
        'me-abudhabi-1'    => 'me-abudhabi-1',
        'me-jeddah-1'      => 'me-jeddah-1',
        'me-riyadh-1'      => 'me-riyadh-1',
        'il-jerusalem-1'   => 'il-jerusalem-1',
        // APAC
        'ap-singapore-1'   => 'ap-singapore-1',
        'ap-mumbai-1'      => 'ap-mumbai-1',
        'ap-hyderabad-1'   => 'ap-hyderabad-1',
        'ap-seoul-1'       => 'ap-seoul-1',
        'ap-tokyo-1'       => 'ap-tokyo-1',
        'ap-osaka-1'       => 'ap-osaka-1',
        'ap-sydney-1'      => 'ap-sydney-1',
        'ap-melbourne-1'   => 'ap-melbourne-1',
        'ap-jakarta-1'     => 'ap-jakarta-1',
        'custom'           => __( 'Other (type manually)', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ),
    );
}

/**
 * Admin page (tabs: setup, logs)
 */
function artimeof_admin_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only view state, not persisted
    $tab     = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'setup';
    $o       = artimeof_get_settings();
    $regions = artimeof_regions();
    settings_errors( 'artimeof' );
    ?>
    <div class="wrap artimeof-wrap">
        <h1>OCI Media Offload</h1>

        <h2 class="nav-tab-wrapper">
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=artimeof&tab=setup' ) ); ?>" class="nav-tab <?php echo ( 'setup' === $tab ) ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Setup', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ); ?></a>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=artimeof&tab=logs' ) ); ?>" class="nav-tab <?php echo ( 'logs' === $tab ) ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Status & Logs', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ); ?></a>
        </h2>

        <?php if ( 'logs' === $tab ) { artimeof_view_logs(); } else { ?>
        <form method="post">
            <?php wp_nonce_field( 'artimeof_save_all' ); ?>
            <input type="hidden" name="artimeof_action" value="save_all" />

            <div class="card">
                <h2><?php esc_html_e( 'Connection', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ); ?></h2>
                <table class="form-table">
                    <tr>
                        <th><label for="region"><?php esc_html_e( 'Region', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ); ?></label></th>
                        <td>
                            <select name="region" id="region">
                                <?php foreach ( $regions as $k => $label ) : ?>
                                    <option value="<?php echo esc_attr( $k ); ?>" <?php selected( isset( $o['region'] ) ? $o['region'] : '', $k ); ?>><?php echo esc_html( $label ); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div id="region_custom_wrap" style="display: <?php echo ( isset( $o['region'] ) && ( ! isset( $regions[ $o['region'] ] ) || 'custom' === $o['region'] ) ) ? 'block' : 'none'; ?>; margin-top:8px;">
                                <input type="text" name="region_custom" placeholder="e.g. me-riyadh-1" value="<?php echo ( isset( $o['region'] ) && ! isset( $regions[ $o['region'] ] ) ) ? esc_attr( $o['region'] ) : ''; ?>" class="regular-text" />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="namespace"><?php esc_html_e( 'Namespace', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ); ?></label></th>
                        <td><input type="text" name="namespace" id="namespace" class="regular-text" value="<?php echo isset( $o['namespace'] ) ? esc_attr( $o['namespace'] ) : ''; ?>" required /></td>
                    </tr>
                    <tr>
                        <th><label for="access_key"><?php esc_html_e( 'Access Key ID', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ); ?></label></th>
                        <td><input type="text" name="access_key" id="access_key" class="regular-text" value="<?php echo isset( $o['access_key'] ) ? esc_attr( $o['access_key'] ) : ''; ?>" required /></td>
                    </tr>
                    <tr>
                        <th><label for="secret_key"><?php esc_html_e( 'Secret Key', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ); ?></label></th>
                        <td><input type="password" name="secret_key" id="secret_key" class="regular-text" value="" placeholder="<?php echo ! empty( $o['secret_key'] ) ? esc_attr__( '(stored)', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ) : ''; ?>" /></td>
                    </tr>
                    <tr>
                        <th><label for="bucket"><?php esc_html_e( 'Bucket', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ); ?></label></th>
                        <td><input type="text" name="bucket" id="bucket" class="regular-text" value="<?php echo isset( $o['bucket'] ) ? esc_attr( $o['bucket'] ) : ''; ?>" required /></td>
                    </tr>
                </table>
            </div>

            <div class="card">
                <h2><?php esc_html_e( 'Delivery (CDN / PAR)', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ); ?></h2>
                <table class="form-table">
                    <tr><th><?php esc_html_e( 'Custom CDN URL', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ); ?></th><td><input type="url" class="regular-text" placeholder="https://media.example.com" disabled /></td></tr>
                    <tr><th><?php esc_html_e( 'PAR base', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ); ?></th><td><input type="url" class="regular-text" placeholder="https://objectstorage.&lt;region&gt;.oraclecloud.com/p/&lt;token&gt;/n/&lt;ns&gt;/b/&lt;bucket&gt;" disabled /></td></tr>
                </table>
                <p>
                    <a class="button" style="background:#16a34a;border-color:#15803d;color:#fff" href="<?php echo esc_url( 'https://oci-media-offload.net' ); ?>" target="_blank" rel="noopener">
                        <?php esc_html_e( 'Upgrade to PRO →', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ); ?>
                    </a>
                </p>
            </div>

            <div class="card">
                <h2><?php esc_html_e( 'Behavior', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ); ?></h2>
                <table class="form-table">
                    <tr>
                        <th><?php esc_html_e( 'Offload new uploads', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ); ?></th>
                        <td><label><input type="checkbox" name="offload_new" value="1" <?php checked( ! empty( $o['offload_new'] ) ); ?> /> <?php esc_html_e( 'Enable', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ); ?></label></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Keep local copy', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ); ?></th>
                        <td><label><input type="checkbox" name="keep_local" value="1" <?php checked( ! empty( $o['keep_local'] ) ); ?> /> <?php esc_html_e( 'Keep files after offload', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ); ?></label></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Folder style', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ); ?></th>
                        <td>
                            <label><input type="radio" name="folder_style" value="yearmonth" <?php checked( isset( $o['folder_style'] ) ? $o['folder_style'] : '', 'yearmonth' ); ?> /> <?php esc_html_e( 'Year/Month', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ); ?></label><br />
                            <label><input type="radio" name="folder_style" value="flat" <?php checked( isset( $o['folder_style'] ) ? $o['folder_style'] : '', 'flat' ); ?> /> <?php esc_html_e( 'Flat', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ); ?></label>
                        </td>
                    </tr>
                </table>
            </div>

            <?php submit_button( __( 'Save All Settings', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ) ); ?>
        </form>

        <div class="card">
            <h2><?php esc_html_e( 'Health Check', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ); ?></h2>
            <button class="button" id="artimeof-btn" type="button"><?php esc_html_e( 'Run Health Check', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ); ?></button>
            <span id="artimeof-out"></span>
        </div>

        <div class="card">
            <h2><?php esc_html_e( 'Backfill existing media', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ); ?></h2>
            <p><?php esc_html_e( 'Offload attachments in batches.', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ); ?></p>
            <button class="button button-primary" disabled><?php esc_html_e( 'Start backfill', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ); ?></button>
            <button class="button" disabled><?php esc_html_e( 'Stop', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ); ?></button>
            <span id="oci-backfill-status"></span>
            <p>
                <a class="button" style="background:#16a34a;border-color:#15803d;color:#fff" href="<?php echo esc_url( 'https://oci-media-offload.net' ); ?>" target="_blank" rel="noopener">
                    <?php esc_html_e( 'Upgrade to PRO →', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ); ?>
                </a>
            </p>
        </div>
        <?php } // end setup ?>
    </div>
    <?php
}

/**
 * Logs table
 */
function artimeof_view_logs() {
    $logs = get_option( ARTIMEOF_LITE_LOG, array() );
    echo '<div class="card"><h2>' . esc_html__( 'Status & Logs', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ) . '</h2>';
    echo '<table class="widefat striped"><thead><tr><th>' . esc_html__( 'Time (UTC)', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ) . '</th><th>' . esc_html__( 'Level', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ) . '</th><th>' . esc_html__( 'Message', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ) . '</th></tr></thead><tbody>';
    if ( ! $logs ) {
        echo '<tr><td colspan="3">' . esc_html__( 'No logs yet.', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ) . '</td></tr>';
    } else {
        foreach ( array_reverse( $logs ) as $row ) {
            $t   = isset( $row['t'] ) ? $row['t'] : '';
            $lv  = isset( $row['level'] ) ? $row['level'] : 'info';
            $msg = isset( $row['msg'] ) ? $row['msg'] : '';
            echo '<tr><td>' . esc_html( $t ) . '</td><td>' . esc_html( $lv ) . '</td><td>' . esc_html( $msg ) . '</td></tr>';
        }
    }
    echo '</tbody></table></div>';
}

/**
 * Save (Lite)
 */
add_action( 'admin_init', function () {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    if ( ! isset( $_SERVER['REQUEST_METHOD'] ) || 'POST' !== $_SERVER['REQUEST_METHOD'] ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
        return;
    }
    if ( empty( $_POST['artimeof_action'] ) ) {
        return;
    }

    $action = isset( $_POST['artimeof_action'] ) ? sanitize_key( wp_unslash( $_POST['artimeof_action'] ) ) : '';
$nonce  = isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '';

if ( 'save_all' !== $action || ! wp_verify_nonce( $nonce, 'artimeof_save_all' ) ) {
    return;
}

$current = artimeof_get_settings();
$region       = isset( $_POST['region'] )       ? sanitize_text_field( wp_unslash( $_POST['region'] ) )       : '';
$namespace    = isset( $_POST['namespace'] )    ? sanitize_text_field( wp_unslash( $_POST['namespace'] ) )    : '';
$access_key   = isset( $_POST['access_key'] )   ? sanitize_text_field( wp_unslash( $_POST['access_key'] ) )   : '';
$secret_key   = isset( $_POST['secret_key'] )   ? sanitize_text_field( wp_unslash( $_POST['secret_key'] ) )   : '';
$bucket       = isset( $_POST['bucket'] )       ? sanitize_text_field( wp_unslash( $_POST['bucket'] ) )       : '';
$folder_style = isset( $_POST['folder_style'] ) ? sanitize_key(       wp_unslash( $_POST['folder_style'] ) ) : '';

$folder_style = in_array( $folder_style, array( 'yearmonth', 'flat' ), true ) ? $folder_style : 'yearmonth';

$save = array(
    'region'       => $region,
    'namespace'    => $namespace,
    'access_key'   => $access_key,
    // Only persist secret if non-empty (avoids blanking on Save):
    'secret_key'   => ( '' !== $secret_key ) ? $secret_key : ( isset( $current['secret_key'] ) ? $current['secret_key'] : '' ),
    'bucket'       => $bucket,
    'offload_new'  => ! empty( $_POST['offload_new'] ) ? 1 : 0,
    'keep_local'   => ! empty( $_POST['keep_local'] ) ? 1 : 0,
    'folder_style' => $folder_style,
    'configured'   => 1,
);

    artimeof_save_settings( $save );
    add_settings_error( 'artimeof', 'saved', __( 'Settings saved.', 'articla-media-offload-lite-for-oracle-cloud-infrastructure' ), 'updated' );
} );

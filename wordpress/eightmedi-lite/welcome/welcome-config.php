<?php
/**
 * Welcome Page Initiation
*/

include get_template_directory() . '/welcome/welcome.php';

/** Plugins **/
$plugins = array(
	// *** Companion Plugins
	'companion_plugins' => array(
		'8degreethemes-demo-importer' => array(
			'slug' 				=> '8degreethemes-demo-importer',
			'name' 				=> esc_html__('Instant Demo Importer', 'eightmedi-lite'),
			'filename' 			=>'8degreethemes-demo-importer.php',
 			// Use either bundled, remote, wordpress
			'host_type' 		=> 'remote',
			'location' 		=> egtdi_get_plugin_remote_url('https://demo.8degreethemes.com/wp-content/theme-demos/','8degreethemes-demo-importer.zip'),
			'class' 			=> 'EGTDI_Demo_Importer',
			'info' => __('8Degree Demo Importer Plugin adds the feature to Import the Demo Conent with a single click.', 'eightmedi-lite'),
		)
	),

	// *** Required Plugins
	'required_plugins' 			=> array(),

	// *** Recommended Plugins
	'recommended_plugins' => array(
			// Free Plugins
		'free_plugins' => array(
			'woocommerce' => array(
				'slug' 		=> 'woocommerce',
				'filename' 	=> 'woocommerce.php',
				'class' 	=> 'WooCommerce'
			),
			'ultimate-form-builder-lite' => array(
				'slug' 		=> 'ultimate-form-builder-lite',
				'filename' 	=> 'ultimate-form-builder-lite.php',
				'class' 	=> 'UFBL_Class'
			),
			'8-degree-coming-soon-page' => array(
				'slug' 		=> '8-degree-coming-soon-page',
				'filename' 	=> '8-degree-coming-soon-page.php',
				'class' 	=> 'Eight_Degree_Coming_Soon_Page'
			),
			'8-degree-notification-bar' => array(
				'slug' 		=> '8-degree-notification-bar',
				'filename' 	=> '8degree-notification.php',
				'class' 	=> 'Edn_Class'
			)
		),
		// Pro Plugins
		'pro_plugins' => array()
	),
);

$strings = array(
		// Welcome Page General Texts
	'welcome_menu_text' => esc_html__( 'Eightmedi Lite Setup', 'eightmedi-lite' ),
	'theme_short_description' => esc_html__( 'EightMedi Lite is free responsive medical WordPress theme ideal for creating websites for doctors, surgeons, medical personnels, health centres, hospitals, clinics, pharmacies etc. EightMedi Lite comes with a lot of awesome of features: Clean and elegant design, Full-width or Boxed layout, Beautifully designed homepage sections - Featured posts section, News/blog section, Appointment form section, Team section , Slider options, Sidebar options, Social icons, Google fonts, Header configuration, Advanced typography, etc.', 'eightmedi-lite' ),

	// Plugin Action Texts
	'install_n_activate' => esc_html__('Install and Activate', 'eightmedi-lite'),
	'deactivate' => esc_html__('Deactivate', 'eightmedi-lite'),
	'activate' => esc_html__('Activate', 'eightmedi-lite'),

	// Recommended Plugins Section
	'pro_plugin_title' => esc_html__( 'Pro Plugins', 'eightmedi-lite' ),
	'pro_plugin_description' => esc_html__( 'Take Advantage of some of our Premium Plugins.', 'eightmedi-lite' ),
	'free_plugin_title' => esc_html__( 'Free Plugins', 'eightmedi-lite' ),
	'free_plugin_description' => esc_html__( 'These Free Plugins might be handy for you.', 'eightmedi-lite' ),

	// Demo Actions
	'installed_btn' => esc_html__('Activated', 'eightmedi-lite'),
	'deactivated_btn' => esc_html__('Activated', 'eightmedi-lite'),
	'demo_installing' => esc_html__('Installing Demo', 'eightmedi-lite'),
	'demo_installed' => esc_html__('Demo Installed', 'eightmedi-lite'),
	'demo_confirm' => esc_html__('Are you sure to import demo content ?', 'eightmedi-lite'),

	// Actions Required
	'req_plugins_installed' => esc_html__( 'All Recommended action has been successfully completed.', 'eightmedi-lite' ),
	'customize_theme_btn' => esc_html__( 'Customize Theme', 'eightmedi-lite' ),
);

function egtdi_get_plugin_remote_url( $base,$filename ) {

	return trailingslashit($base) . $filename;
}

/**
 * Initiating Welcome Page
*/
$my_theme_wc_page = new eightmedi_Lite_Welcome( $plugins, $strings );

		
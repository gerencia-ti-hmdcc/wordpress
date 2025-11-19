<?php
/**
 * 
 * Theme Info EightMedi Lite
 * 
 */

function eightmedi_lite_customizer_themeinfo( $wp_customize ) {
	$wp_customize->add_section( 'theme_info' , array(
		'title'       => __( 'Theme Information' , 'eightmedi-lite' ),
		'priority'    => 500,
		));

	$wp_customize->add_setting('theme_info_theme',array(
		'default' => '',
		'sanitize_callback' => 'eightmedi_sanitize_text',
		));

	$eightmedi_lite_desc_theme_opt = "";
	$eightmedi_lite_desc_theme_opt .= "<strong>".__('Need help?','eightmedi-lite')."</strong><br />";
	$eightmedi_lite_desc_theme_opt .= "<span>".__('View documentation','eightmedi-lite').' : </span> <a target="_blank" href="'.esc_url('http://8degreethemes.com/documentation/8medi-lite/').'">'.__('here','eightmedi-lite').'</a> <br />';
	$eightmedi_lite_desc_theme_opt .= "<span>".__('Support forum','eightmedi-lite').' : </span><a target="_blank" href="'.esc_url('https://8degreethemes.com/support/forum/free-themes/eightmedi-lite/').'">'.__('here','eightmedi-lite').'</a> <br />';
	$eightmedi_lite_desc_theme_opt .= "<span>".__('View Video tutorials','eightmedi-lite').' : </span><a target="_blank" href="'.esc_url('https://www.youtube.com/watch?list=PLyv2_zoytm1ifr1RwkKCsePhS6v5ynylV&v=HhSeA4TyvXQ').'">'.__('here','eightmedi-lite').'</a> <br />';
	$eightmedi_lite_desc_theme_opt .= "<span>".__('Email us','eightmedi-lite').' : </span><a target="_blank" href="'.esc_url('mailto:support@8degreethemes.com').'">support@8degreethemes.com</a> <br />';
	$eightmedi_lite_desc_theme_opt .= "<span>".__('More Details','eightmedi-lite').' : </span><a target="_blank" href="'.esc_url('http://8degreethemes.com/').'">'.__('here','eightmedi-lite').'</a><a class="upgrade-pro" target="_blank" href="https://8degreethemes.com/wordpress-themes/maggie-pro/"><img src="http://8degreethemes.com/demo/upgrade-eightmedi-lite.jpg" alt="UPGRADE TO EightMedi PRO" /></a>';

	$wp_customize->add_control( new eightmedi_lite_Theme_Info_Custom_Control( $wp_customize ,'theme_info_theme',array(
		'label' => __( 'About Eightmedi Lite' , 'eightmedi-lite' ),
		'section' => 'theme_info',
		'description' => $eightmedi_lite_desc_theme_opt
		)));

	$wp_customize->add_setting('theme_info_useful_plugins',array(
		'default' => '',
		'sanitize_callback' => 'eightmedi_sanitize_text',
		));

	$eightmedi_lite_desc_theme_opt = '<a class="red" target="_blank" href="'.esc_url('https://wpall.club/').'">'.__('WordPress Tutorials and Resources','eightmedi-lite').'</a> <br />';

	$wp_customize->add_control( new eightmedi_lite_Theme_Info_Custom_Control( $wp_customize ,'theme_info_useful_plugins',array(
		'label' => __( 'WordPress Resources' , 'eightmedi-lite' ),
		'section' => 'theme_info',
		'description' => $eightmedi_lite_desc_theme_opt
		)));

}
add_action( 'customize_register', 'eightmedi_lite_customizer_themeinfo' );


if(class_exists( 'WP_Customize_control')){

	class eightmedi_lite_Theme_Info_Custom_Control extends WP_Customize_Control
	{
    	/**
       	* Render the content on the theme customizer page
       	*/
       	public function render_content()
       	{
       		?>
       		<label>
       			<strong class="customize-text_editor"><?php echo esc_html( $this->label ); ?></strong>
       			<br />
       			<span class="customize-text_editor_desc">
       				<?php echo wp_kses_post( $this->description ); ?>
       			</span>
       		</label>
       		<?php
       	}
    }//editor close
}//class close
<?php
/**
 * 8Medi Lite functions and definitions
 *
 * @package 8Medi Lite
 */
/**
 * My Functions
 */

//adding custom scripts and styles in header for favicon and other
function eightmedi_lite_header_scripts(){
	$header_bg_v = get_header_image();
	$header_bg_c = get_background_color();
	$appointment_bg_v = get_theme_mod('eightmedi_lite_appointment_bkgimage');
	$eightmedi_dynamic_css = '';
	if(!empty($appointment_bg_v)){
		$eightmedi_dynamic_css .=   '.appointment .custom-appointment-form { background: url("'.esc_url($appointment_bg_v).'") no-repeat scroll right bottom rgba(0, 0, 0, 0); }';
		$eightmedi_dynamic_css .= "\n";
	}
	if(($header_bg_v)){
		$eightmedi_dynamic_css .= '.site-header { background: url("'.esc_url($header_bg_v).'") no-repeat scroll left top rgba(0, 0, 0, 0); position: relative; z-index: 1;background-size: cover; }';
		$eightmedi_dynamic_css .= "\n";
		$eightmedi_dynamic_css .= '.site-header .ed-container-home:before {
			content: "";
			position: absolute;
			top: 0;
			bottom: 0;
			left: 0;
			right: 0;
			background: '.eightmedi_lite_hex2rgba($header_bg_c,'0.7').';
			z-index: -1;
		}';
	}
	wp_add_inline_style( 'eightmedi-lite-style', $eightmedi_dynamic_css );
}
add_action('wp_enqueue_scripts','eightmedi_lite_header_scripts');

function eightmedi_lite_web_layout($classes){
	$web_layout = get_theme_mod('eightmedi_lite_webpage_layout','fullwidth');
	if($web_layout == 'boxed'){
		$classes[]= 'boxed-layout';
	}
	elseif($web_layout == 'fullwidth'){
		$classes[]='fullwidth';
	}
	
	return $classes;
}
add_filter( 'body_class', 'eightmedi_lite_web_layout' );

add_action('eightmedi_lite_homepage_slider_config','eightmedi_lite_homepage_slider_config');
//homepage slider configuration settings
function eightmedi_lite_homepage_slider_config(){
	$display_slider = (get_theme_mod('eightmedi_lite_display_slider'))?get_theme_mod('eightmedi_lite_display_slider'):"1";
	
	$display_pager = absint( get_theme_mod('eightmedi_lite_display_pager','0') );
	($display_pager=='1')?$display_pager='true':$display_pager='false';
	
	$display_controls = absint ( get_theme_mod('eightmedi_lite_display_controls','0'));
	($display_controls=='1')?$display_controls='true':$display_controls='false';
	
	$auto_transition = absint ( get_theme_mod('eightmedi_lite_auto_transition','0'));
	($auto_transition=='1')?$auto_transition='true':$auto_transition='false';
	
	$transition_type = absint ( get_theme_mod('eightmedi_lite_transition_type','0'));
	($transition_type=='1')?$transition_type='horizontal':$transition_type='fade';

	$transition_speed = absint( get_theme_mod( 'eightmedi_lite_transition_speed', 5000 ) );	

	$transition_pause = absint( get_theme_mod( 'eightmedi_lite_transition_pause', 1000 ) );	

	// Send data to client
	wp_localize_script('eightmedi-lite-custom-scripts', 'SliderData', array(
		'mode' => $transition_type,
		'controls' => $display_controls,
		'speed' => $transition_speed,
		'pause' => $transition_pause,
		'pager' => $display_pager,
		'auto' => $auto_transition
	));
}


add_action('eightmedi_lite_homepage_slider','eightmedi_lite_homepage_slider_content', 10);
//homepage slider content
function eightmedi_lite_homepage_slider_content(){
	$display_slider = absint ( get_theme_mod('eightmedi_lite_display_slider','1') );

	$display_captions = absint ( get_theme_mod('eightmedi_lite_slider_text', '1') );
	
	if( $display_slider == "1"){
		?>
		<div id="home-slider">
			<?php 
			$slider_category = get_theme_mod('eightmedi_lite_slider_category');
			if( !empty($slider_category)) {
				$loop = new WP_Query(
					array(
						'category_name' => $slider_category,
						'posts_per_page' => -1    
					)
				);
				?>
				<div class="em-slider">
					<?php
					if($loop->have_posts()) {
						while($loop->have_posts()) {
							$loop->the_post();
							$image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full', false );
							?>
							<div class="slides">
								<img src="<?php echo esc_url($image[0]); ?>" alt="<?php the_title(); ?>" />
								<?php
								if($display_captions == '1'){
									?>
									<div class="caption-wrapper">  
										<div class="em-container">
											<div class="slider-caption">
												<div class="mid-content">
													<div class="slider-title"> <?php the_title(); ?> </div>
													<div class="slider-content"> <?php the_content(); ?> </div>
												</div>
											</div>
										</div>
									</div>
									<?php
								}
								?>
							</div>
							<?php 
						}
					}
					?>
				</div>
				<a href="#featured-content" class="home-slider-pointer"><i class="fa fa-angle-double-down"></i></a>
				<?php $btntext = get_theme_mod('eightmedi_lite_slider_cta_btntext','');
				if($btntext!=''){
					?>
					<a class="home-slider-pointer cta-btn" href="<?php echo esc_url(get_theme_mod('eightmedi_lite_slider_cta_btnlink','#book-an-appointment'));?>"><?php echo esc_html($btntext);?></a>
					<?php
				}
			}
			?>
		</div>
		<?php
	}
}

	//Social Icons Settings
add_action('eightmedi_lite_social_links','eightmedi_lite_social_links', 10);
function eightmedi_lite_social_links(){
	$facebooklink = esc_url( get_theme_mod('eightmedi_lite_social_facebook') );
	$twitterlink = esc_url( get_theme_mod('eightmedi_lite_social_twitter') );
	$google_pluslink = esc_url( get_theme_mod('eightmedi_lite_social_googleplus') );
	$youtubelink = esc_url( get_theme_mod('eightmedi_lite_social_youtube') );
	$pinterestlink = esc_url( get_theme_mod('eightmedi_lite_social_pinterest') );
	$linkedinlink = esc_url( get_theme_mod('eightmedi_lite_social_linkedin') );
	$vimeolink = esc_url( get_theme_mod('eightmedi_lite_social_vimeo') );
	$instagramlink = esc_url( get_theme_mod('eightmedi_lite_social_instagram') );
	$skypelink = get_theme_mod('eightmedi_lite_social_skype');
	?>
	<div class="social-icons">
		<?php 
		if(!empty($facebooklink)){ ?>
			<a href="<?php echo esc_url($facebooklink); ?>" class="facebook" data-title="Facebook" target="_blank"><i class="fa fa-facebook"></i></a>
			<?php 
		}
		if(!empty($twitterlink)){ ?>
			<a href="<?php echo esc_url($twitterlink); ?>" class="twitter" data-title="Twitter" target="_blank"><i class="fa fa-twitter"></i></a>
			<?php 
		}
		if(!empty($google_pluslink)){ ?>
			<a href="<?php echo esc_url($google_pluslink); ?>" class="gplus" data-title="Google Plus" target="_blank"><i class="fa fa-google-plus"></i></a>
			<?php 
		}
		if(!empty($youtubelink)){ ?>
			<a href="<?php echo esc_url($youtubelink); ?>" class="youtube" data-title="Youtube" target="_blank"><i class="fa fa-youtube"></i></a>
			<?php 
		}
		if(!empty($pinterestlink)){ ?>
			<a href="<?php echo esc_url($pinterestlink); ?>" class="pinterest" data-title="Pinterest" target="_blank"><i class="fa fa-pinterest"></i></a>
			<?php 
		}
		if(!empty($linkedinlink)){ ?>
			<a href="<?php echo esc_url($linkedinlink); ?>" class="linkedin" data-title="Linkedin" target="_blank"><i class="fa fa-linkedin"></i></a>
			<?php 
		}
		if(!empty($vimeolink)){ ?>
			<a href="<?php echo esc_url($vimeolink); ?>" class="vimeo" data-title="Vimeo" target="_blank"><i class="fa fa-vimeo-square"></i></a>
			<?php 
		}
		if(!empty($instagramlink)){ ?>
			<a href="<?php echo esc_url($instagramlink); ?>" class="instagram" data-title="instagram" target="_blank"><i class="fa fa-instagram"></i></a>
			<?php 
		}
		if(!empty($skypelink)){ ?>
			<a href="<?php echo esc_attr('skype:'.$skypelink); ?>" class="skype" data-title="Skype"><i class="fa fa-skype"></i></a>
			<?php
		} ?>
	</div>
	<?php
}

	/** 
	 * Truncates text without breaking HTML Code
	 */
	function eightmedi_lite_excerpt($eightmedi_lite_text, $eightmedi_lite_length = 100, $eightmedi_lite_ending = '...', $eightmedi_lite_exact = true, $eightmedi_lite_considerHtml = true) {
		if ($eightmedi_lite_considerHtml) {
  // if the plain text is shorter than the maximum length, return the whole text
			if (strlen(preg_replace('/<.*?>/', '', $eightmedi_lite_text)) <= $eightmedi_lite_length) {
				return $eightmedi_lite_text;
			}

  // splits all html-tags to scanable lines
			preg_match_all('/(<.+?>)?([^<>]*)/s', $eightmedi_lite_text, $eightmedi_lite_lines, PREG_SET_ORDER);

			$eightmedi_lite_total_length = strlen($eightmedi_lite_ending);
			$eightmedi_lite_open_tags = array();
			$eightmedi_lite_truncate = '';

			foreach ($eightmedi_lite_lines as $eightmedi_lite_line_matchings) {
   // if there is any html-tag in this line, handle it and add it (uncounted) to the output
				if (!empty($eightmedi_lite_line_matchings[1])) {
    // if it’s an “empty element” with or without xhtml-conform closing slash (f.e.)
					if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $eightmedi_lite_line_matchings[1])) {
    // do nothing
    // if tag is a closing tag (f.e.)
					} else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $eightmedi_lite_line_matchings[1], $eightmedi_lite_tag_matchings)) {
     // delete tag from $open_tags list
						$eightmedi_lite_pos = array_search($eightmedi_lite_tag_matchings[1], $eightmedi_lite_open_tags);
						if ($eightmedi_lite_pos !== false) {
							unset($eightmedi_lite_open_tags[$eightmedi_lite_pos]);
						}
     // if tag is an opening tag (f.e. )
					} else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $eightmedi_lite_line_matchings[1], $eightmedi_lite_tag_matchings)) {
     // add tag to the beginning of $open_tags list
						array_unshift($eightmedi_lite_open_tags, strtolower($eightmedi_lite_tag_matchings[1]));
					}
    // add html-tag to $truncate’d text
					$eightmedi_lite_truncate .= $eightmedi_lite_line_matchings[1];
				}

   // calculate the length of the plain text part of the line; handle entities as one character
				$eightmedi_lite_content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $eightmedi_lite_line_matchings[2]));
				if ($eightmedi_lite_total_length+$eightmedi_lite_content_length > $eightmedi_lite_length) {
    // the number of characters which are left
					$eightmedi_lite_left = $eightmedi_lite_length - $eightmedi_lite_total_length;
					$eightmedi_lite_entities_length = 0;
    // search for html entities
					if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $eightmedi_lite_line_matchings[2], $eightmedi_lite_entities, PREG_OFFSET_CAPTURE)) {
     // calculate the real length of all entities in the legal range
						foreach ($eightmedi_lite_entities[0] as $eightmedi_lite_entity) {
							if ($eightmedi_lite_entity[1]+1-$eightmedi_lite_entities_length <= $eightmedi_lite_left) {
								$eightmedi_lite_left--;
								$eightmedi_lite_entities_length += strlen($eightmedi_lite_entity[0]);
							} else {
       // no more characters left
								break;
							}
						}
					}
					$eightmedi_lite_truncate .= substr($eightmedi_lite_line_matchings[2], 0, $eightmedi_lite_left+$eightmedi_lite_entities_length);
    // maximum lenght is reached, so get off the loop
					break;
				} else {
					$eightmedi_lite_truncate .= $eightmedi_lite_line_matchings[2];
					$eightmedi_lite_total_length += $eightmedi_lite_content_length;
				}

   // if the maximum length is reached, get off the loop
				if($eightmedi_lite_total_length >= $eightmedi_lite_length) {
					break;
				}
			}
		} else {
			if (strlen($eightmedi_lite_text) <= $eightmedi_lite_length) {
				return $eightmedi_lite_text;
			} else {
				$eightmedi_lite_truncate = substr($eightmedi_lite_text, 0, $eightmedi_lite_length - strlen($eightmedi_lite_ending));
			}
		}

 // if the words shouldn't be cut in the middle...
		if (!$eightmedi_lite_exact) {
  // ...search the last occurance of a space...
			$eightmedi_lite_spacepos = strrpos($eightmedi_lite_truncate, ' ');
			if (isset($eightmedi_lite_spacepos)) {
   // ...and cut the text in this position
				$eightmedi_lite_truncate = substr($eightmedi_lite_truncate, 0, $eightmedi_lite_spacepos);
			}
		}

 // add the defined ending to the text
		$eightmedi_lite_truncate .= $eightmedi_lite_ending;

		if($eightmedi_lite_considerHtml) {
  // close all unclosed html-tags
			foreach ($eightmedi_lite_open_tags as $eightmedi_lite_tag) {
				$eightmedi_lite_truncate .= '';
			}
		}

		return $eightmedi_lite_truncate;

	}


	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

	function eightmedi_lite_wrapper_start() {
		echo '<div class="ed-container"><div id="primary" class="right-sidebar">';
	}
	add_action('woocommerce_before_main_content', 'eightmedi_lite_wrapper_start', 10);

	function eightmedi_lite_wrapper_end() {
		echo '</div>';
		do_action( 'woocommerce_sidebar' );
		echo '</div>';
	}
	add_action('woocommerce_after_main_content','eightmedi_lite_wrapper_end',9);

	//add_filter( 'loop_shop_per_page', create_function( '$cols', 'return 8;' ), 20 );
	add_filter( 'loop_shop_per_page', function(){ $cols = 8; return $cols; });

if ( is_admin() ) : // Load only if we are viewing an admin page
function eightmedi_lite_admin_scripts() {
	wp_enqueue_media();
	wp_enqueue_script( 'eightmedi-lite-custom', get_template_directory_uri().'/inc/admin-panel/admin.js', array( 'jquery' ),'',true );
	wp_enqueue_style( 'eightmedi-lite-admin-style',get_template_directory_uri().'/inc/admin-panel/admin.css', '1.0', 'screen' );
}
add_action('admin_enqueue_scripts', 'eightmedi_lite_admin_scripts');
endif;

/** rgb from hex color code */
/* Convert hexdec color string to rgb(a) string */ 
function eightmedi_lite_hex2rgba($color, $opacity = false) {

	$default = 'rgb(0,0,0)';

     //Return default if no color provided
	if(empty($color))
		return $default; 

            //Sanitize $color if "#" is provided 
	if ($color[0] == '#' ) {
		$color = substr( $color, 1 );
	}

            //Check if color has 6 or 3 characters and get values
	if (strlen($color) == 6) {
		$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
	} elseif ( strlen( $color ) == 3 ) {
		$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
	} else {
		return $default;
	}

            //Convert hexadec to rgb
	$rgb =  array_map('hexdec', $hex);

            //Check if opacity is set(rgba or rgb)
	if($opacity){
		if(abs($opacity) > 1)
			$opacity = 1.0;
		$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
	} else {
		$output = 'rgb('.implode(",",$rgb).')';
	}

            //Return rgb(a) color string
	return $output;
}

/** adding ocdi compatibility */
function eightmedi_lite_ocdi_import_files() {
	return array(
		array(
			'import_file_name'             => 'EightMedi Lite Demo',
			//'categories'                   => array( 'Category 1', 'Category 2' ),
			'local_import_file'            => trailingslashit( get_template_directory() ) . 'welcome/demo/eightmedi-lite/content.xml',
			'local_import_widget_file'     => trailingslashit( get_template_directory() ) . 'welcome/demo/eightmedi-lite/widgets.wie',
			'local_import_customizer_file' => trailingslashit( get_template_directory() ) . 'welcome/demo/eightmedi-lite/customizer_options.dat',
			'import_preview_image_url'     => get_template_directory_uri().'/screenshot.png',
			'import_notice'                => __( 'After you import this demo, you might have to setup the menu separately.', 'eightmedi-lite' ),
			'preview_url'                  => 'https://8degreethemes.com/demo/8medi-lite/',
		)
	);
}
add_filter( 'pt-ocdi/import_files', 'eightmedi_lite_ocdi_import_files' );


function eightmedi_lite_ocdi_after_import( $selected_import ) {
	// Assign menus to their locations.
	$main_menu = get_term_by( 'name', 'Menu 1', 'nav_menu' );

	set_theme_mod( 'nav_menu_locations', array(
		'primary' => $main_menu->term_id,
	));

	// Assign front page and posts page (blog page).
	$front_page_id = get_page_by_title( 'Homepage' );

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $front_page_id->ID );
}
add_action( 'pt-ocdi/after_import', 'eightmedi_lite_ocdi_after_import' );
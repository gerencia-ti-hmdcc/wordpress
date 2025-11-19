<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package 8Medi Lite
 */

?>

</div><!-- #content -->

<footer id="colophon" class="site-footer" role="contentinfo">
	<div class="footer-wrap">
		<div class="ed-container-home">
			<?php
			if(is_active_sidebar('eightmedi-lite-widget-footer-1')){
				$top_footer_column = get_theme_mod('eightmedi_lite_footer_widgets_layout','4');
				?>
				<div class="top-footer wow fadeInLeft columns-<?php echo esc_attr($top_footer_column);?>">
					<?php
					dynamic_sidebar('eightmedi-lite-widget-footer-1');
					?>
				</div>
				<?php
			}
			?>
		</div>
		<?php
		$eightmedi_lite_fl = get_theme_mod('eightmedi_lite_footer_layout','0');
		?>
		<div class="main-footer layout-<?php echo esc_attr($eightmedi_lite_fl);?>">
			<div class="ed-container-home">
				<div class="site-info">
					<?php esc_html_e( 'WordPress Theme : ', 'eightmedi-lite' );  ?><a  title="<?php esc_attr_e('Free WordPress Theme','eightmedi-lite');?>" href="<?php echo esc_url( __( 'https://demo.8degreethemes.com/eightmedi-lite/', 'eightmedi-lite' ) ); ?>"><?php esc_html_e( 'EightMedi Lite', 'eightmedi-lite' ); ?> </a>
					<span><?php esc_html_e(' by 8Degree Themes','eightmedi-lite');?></span>
				</div><!-- .site-info -->
				<?php if(get_theme_mod('eightmedi_lite_social_icons_in_footer','1')==1){ ?>
					<div class="footer-social social-links">
						<?php do_action('eightmedi_lite_social_links');?>
					</div>
					<?php }?>
				</div>
			</div>
		</div>
	</footer><!-- #colophon -->
</div><!-- #page -->
<div id="es-top"></div>
<?php wp_footer(); ?>

</body>
</html>
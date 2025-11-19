<?php
/**
 * Template part for displaying single posts.
 *
 * @package 8Medi Lite
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	
	<header class="page-header">
		<?php the_title( '<h1 class="page-title">', '</h1>' ); ?>

		<div class="entry-meta">
			<?php eightmedi_lite_posted_on(); ?>
		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->
	
	<div class="entry-content">
	<?php $show_img = get_theme_mod('eightmedi_lite_post_featured_img',1);
		if($show_img == '1'){ ?>
			<?php if (has_post_thumbnail()): ?>
			<figure>
				<?php the_post_thumbnail('full');?>
			</figure>
			<?php endif; ?>
		<?php } ?>
		<?php the_content(); ?>
		<?php
		wp_link_pages( array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'eightmedi-lite' ),
			'after'  => '</div>',
			) );
			?>
		</div><!-- .entry-content -->

		<footer class="entry-footer">
			<?php eightmedi_lite_entry_footer(); ?>
		</footer><!-- .entry-footer -->
	</article><!-- #post-## -->


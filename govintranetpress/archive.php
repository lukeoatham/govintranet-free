<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 */

get_header(); ?>

<?php
	/* Queue the first post, that way we know
	 * what date we're dealing with (if that is the case).
	 *
	 * We reset this later so we can run the loop
	 * properly with a call to rewind_posts().
	 */
	 //query_posts('post_type=news');
	if ( have_posts() )
		the_post();
?>
	
		<div class="col-lg-8 col-md-8 white">
			<div class="row">
				<div class='breadcrumbs'>
					<?php if(function_exists('bcn_display') && !is_front_page()) {
						bcn_display();
						}?>
				</div>
			</div>
			<h1>
		<?php if ( is_day() ) : ?>
						<?php printf( __( 'Daily archives: %s', 'twentyten' ), get_the_date() ); ?>
		<?php elseif ( is_month() ) : ?>
						<?php printf( __( 'Monthly archives: %s', 'twentyten' ), get_the_date('F Y') ); ?>
		<?php elseif ( is_year() ) : ?>
						<?php printf( __( 'Yearly archives: %s', 'twentyten' ), get_the_date('Y') ); ?>
		<?php else : ?>
						<?php _e( 'Archives', 'twentyten' ); ?>
		<?php endif; ?>
					</h1>
		
		<?php
			/* Since we called the_post() above, we need to
			 * rewind the loop back to the beginning that way
			 * we can run the loop properly, in full.
			 */
			rewind_posts();
		
			/* Run the loop for the archives page to output the posts.
			 * If you want to overload this in a child theme then include a file
			 * called loop-archives.php and that will be used instead.
			 */
			 get_template_part( 'loop', 'archive' );
?>
		</div>

	<div class="col-lg-4 col-md-4" id="sidebar">
		<div id="related">
			<?php dynamic_sidebar('news-widget-area');  ?>
		</div>
	</div>


<?php get_footer(); ?>
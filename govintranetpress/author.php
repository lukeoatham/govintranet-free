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
 * @subpackage Starkers
 * @since Starkers 3.0
 */
 

get_header(); ?>



<?php
	/* Queue the first post, that way we know
	 * what date we're dealing with (if that is the case).
	 *
	 * We reset this later so we can run the loop
	 * properly with a call to rewind_posts().
	 */
	 $curauth = (isset($_GET['author_name'])) ? get_user_by('slug',$author_name):
	 get_userdata(intval($author));
	 
	 query_posts( 'post_type=blog' );
	 
	if ( have_posts() )
		the_post();

?>		
					<div class="col-lg-12 white ">
						<div class="row">
							<div class='breadcrumbs'>
								<?php if(function_exists('bcn_display') && !is_front_page()) {
									bcn_display();
									}?>
							</div>
						</div>
					<h1>Blog posts by <?php echo $curauth->display_name   ?></h1>
					<p>Email: <a href="mailto:<?php echo $curauth->user_email ; ?>"><?php echo $curauth->display_name ; ?></a></p>
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
				</div>
				<div class="col-lg-4 last">

				</div>	

				</div>

<?php get_footer(); ?>
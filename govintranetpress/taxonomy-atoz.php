<?php
/**
 * The template for displaying A-Z pages.
 *
 * Uses atoz taxonomy to display lists of pages and tasks
 *
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

					<div class="col-lg-12 col-md-12 white">
						<div class="row">
							<div class='breadcrumbs'>
								<?php if(function_exists('bcn_display') && !is_front_page()) {
									bcn_display();
									}?>
							</div>
						</div>
						<h1>A to Z</h1>
						<ul class="pagination">

						<?php 
						//$slug = pods_url_variable(1);
						
						$slugtitle = single_cat_title();

						$slugref = get_term_by('name', $slugtitle, 'atoz', ARRAY_A);
						$slug = $slugref['slug'];
						
						//fill the default a to z array
						$letters = range('a','z');
						$letterlink=array();
						$hasentries = array();
						
						foreach($letters as $l) { 
							$letterlink[$l] = "<li class='disabled'><a>".strtoupper($l)."</a></li>";
						}				


						$terms = get_terms('atoz'); 
						if ($terms) {
							foreach ((array)$terms as $taxonomy ) {
				
							$letterlink[$taxonomy->slug] = "<li";
							if (strtolower($slug)==strtolower($taxonomy->slug)) $letterlink[$taxonomy->slug] .=  " class='active'";
								$letterlink[$taxonomy->slug] .=  "><a href='/atoz/".$taxonomy->slug."/'>".strtoupper($taxonomy->name)."</a></li>";
							}
						}

						echo @implode("",$letterlink); 
?>
						</ul>
						<h2><?php echo single_cat_title(); ?></h2>
						<?php

						/* Since we called the_post() above, we need to
						 * rewind the loop back to the beginning that way
						 * we can run the loop properly, in full.
						 */
						rewind_posts();
					
						global $query_string;
						query_posts( $query_string . "&orderby=title&order=ASC");
						
						/* Run the loop for the archives page to output the posts.
						 * If you want to overload this in a child theme then include a file
						 * called loop-archives.php and that will be used instead.
						 */
						 echo "<ul>";
						 get_template_part( 'loop', 'atoz' );
						 echo "</ul>";
?>
					</div>

<?php get_footer(); ?>
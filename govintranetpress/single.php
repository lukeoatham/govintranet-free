<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */

$slug = pods_url_variable(0);
if ($slug=="homepage"){
	wp_redirect('/');
};
if ($slug=="control"){
	wp_redirect('/');
};

get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

	
					<div class="col-lg-8 col-md-8 col-sm-8 white ">
						<div class="row">
							<div class='breadcrumbs'>
								<?php if(function_exists('bcn_display') && !is_front_page()) {
									bcn_display();
									}?>
							</div>
						</div>
			<h1><?php the_title(); ?></h1>

			<?php
			$article_date=get_the_date();
			$article_date = date("j F Y",strtotime($article_date));	?>
			<?php echo the_date('j M Y', '<p class=news_date>', '</p>') ?>

			<?php the_content(); ?>

			<?php
			wp_reset_query();
				?>
			</div>
		</div> <!--end of first column-->
		
		<div class="col-lg-3 last" id="sidebar">	
			
			</div> <!--end of second column-->


				

			
<?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>

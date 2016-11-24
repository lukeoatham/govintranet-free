<?php

/* Template name: Full-width page */

get_header(); 

wp_register_script( 'match_heights', get_template_directory_uri() . "/js/jquery.matchHeight-min.js");
wp_enqueue_script( 'match_heights' );

?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

	<div class="col-lg-12 white">
		<div class="row">
			<div class='breadcrumbs'>
				<?php if(function_exists('bcn_display') && !is_front_page()) {
					bcn_display();
					}?>
			</div>
		</div>
		<h1><?php the_title(); ?></h1>
		<?php the_content(); ?>
	
	</div> 

<?php endwhile; ?>

<?php get_footer(); ?>
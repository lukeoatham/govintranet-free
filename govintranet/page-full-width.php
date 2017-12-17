<?php

/* 
	Template name: Full-width page 
	Template Post Type: page, task
*/

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

		<?php 
		if ( is_singular('task') ):	

			get_template_part("part", "task");	

		else: 

			echo '<article class="clearfix">';

			the_title('<h1>','</h1>'); 

			the_content(); 
			
			echo '</article>';
			
			get_template_part("part", "downloads"); 
			
			if ('open' == $post->comment_status) {
					 comments_template( '', true ); 
			}
			
		endif; 
		?>
	
	</div> 

<?php endwhile; ?>

<?php get_footer(); ?>
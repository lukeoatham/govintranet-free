<?php
/**
 * The template for displaying Category Archive pages.
 *
 * @package WordPress
 */

get_header(); 

?>
<?php
	if ( have_posts() )
		the_post();
?>
		
			<div class="col-lg-9 col-md-9 white">
				<h1><?php
					printf( __( 'Category: %s', 'govintranetpress' ), '' . single_tag_title( '', false ) . '' );
				?></h1>
		
				<?php

				 rewind_posts();
				 
				 get_template_part( 'loop', 'category' );

				 wp_reset_query();				

				?>
			</div>
		
<?php get_footer(); ?>

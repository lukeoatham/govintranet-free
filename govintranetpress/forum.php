<?php
/**
 * The template for displaying forum pages. Excludes regular breakcrumb.
 *
 */

get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); 
?>

	<div class="row">
		<div class="twelvecol white  last" id='content'>

			<div class="content-wrapper">
					<h1><?php the_title(); ?></h1>
					<?php the_content(); ?>
			 </div>
		</div> 
	</div> 

<?php endwhile; ?>

<?php get_footer(); ?>
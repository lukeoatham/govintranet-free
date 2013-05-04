<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the wordpress construct of pages
 * and that other 'pages' on your wordpress site will use a
 * different template.
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */

get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); 
?>

	<div class="row">
		<div class="twelvecol white  last" id='content'>
			<div class="row">
				<div class='breadcrumbs'>
					<?php if(function_exists('bcn_display') && !is_front_page()) {
						bcn_display();
					}?>
				</div>
			</div>

			<div class="content-wrapper">
					<h1><?php the_title(); ?></h1>
					<?php the_content(); ?>
			 </div>
		</div> 
	</div> 

<?php endwhile; ?>

<?php get_footer(); ?>
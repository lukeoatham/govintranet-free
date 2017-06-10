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
 */

get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>


		<div class="col-sm-12 white">
			<div class="row">
				<div class='breadcrumbs'>
				<?php 
				if ( bbp_is_single_user() ):
					$home = "<a href='" . get_home_url() . "'>" . __(get_bloginfo('name'), 'govintranet') . "</a>";
					$sd = get_option('options_module_staff_directory_page');
					$sd = $sd[0];
					$directory = "<a href='" . get_permalink($sd) . "'>" . get_the_title($sd) . "</a>";
					if ( function_exists('bcn_display') ){
						$bcn = get_option('bcn_options');
						$sep = $bcn['hseparator'];
					} else {
						$sep = " > ";
					}
					echo $home; 
					echo $sep;
					echo $directory;
					echo $sep; 
					the_title(); 
				elseif (function_exists('bcn_display') && !is_front_page()) :
					bcn_display();
				endif; 
				?>
				</div>
			</div>
			<?php the_content(); ?>
	</div> 

<?php endwhile; ?>

<?php get_footer(); ?>
<?php
/* Template name: Page with left nav  */

get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); 
?>

	<div class="row">
		<div class="twelvecol white  last">

				<div class='breadcrumbs'>
					<?php if(function_exists('bcn_display') && !is_front_page()) {
						bcn_display();
					}?>
				</div>


					<div class="threecol" id='secondarynav'>
				
							<?php renderLeftNav(); ?>
												
					</div>

					<div class="sixcol" id='content'>

						<?php if ( ! is_front_page() ) { ?>
							<h1><?php the_title(); ?></h1>
						<?php } ?>				
														
							<?php the_content(); ?>
	
					</div>

					<div class="threecol last clearfix" id='sidebar'>
							
						<?php dynamic_sidebar('inside-sidebar-widget-area');  ?>
						</ul>
						
					</div>




<?php endwhile; ?>

<?php get_footer(); ?>
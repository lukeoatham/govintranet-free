<?php
/* Template name: Page with left nav wide */

get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

	<div class="col-lg-12 white ">
		<div class="row">
			<div class='breadcrumbs'>
				<?php if(function_exists('bcn_display') && !is_front_page()) {
					bcn_display();
					}?>
			</div>
		</div>

		<div class="col-lg-3 col-md-3 col-sm-12" id='secondarynav'>
	
			<?php renderLeftNav(); ?>
									
		</div>

		<div class="col-lg-9 col-md-9 col-sm-12">
			<article class="clearfix">
			<?php if ( ! is_front_page() ) { ?>
				<h1><?php the_title(); ?></h1>
			<?php } ?>				
											
			<?php the_content(); ?>
			</article>
			<?php get_template_part("part", "downloads"); 
			if ('open' == $post->comment_status) {
				 comments_template( '', true ); 
			}
			?>			
		</div>

	</div>

<?php endwhile; ?>

<?php get_footer(); ?>
<?php
/* Template name: Page with left nav  */

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

		<div class="col-lg-6 col-md-6 col-sm-12">

			<?php the_title("<h1>","</h1>"); ?>
										
			<?php the_content(); ?>
	
		</div>

		<div class="col-lg-3 col-md-3 col-sm-12" id="sidebar">
			<h2 class="sr-only">Sidebar</h2>
			<?php 
			if ( has_post_thumbnail( $id )){	
				the_post_thumbnail('large', array('class'=>'img img-responsive')); 
				echo wpautop( "<span class='news_date'>".get_post_thumbnail_caption()."</span>" );
			}
			get_template_part("part", "sidebar");
			get_template_part("part", "related");
			?>		
		</div>

	</div>

<?php endwhile; ?>

<?php get_footer(); ?>
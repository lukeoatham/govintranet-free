<?php
/* Template name: Page with left nav wide */

get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); 
?>

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
	
							<?php if ( ! is_front_page() ) { ?>
								<h1><?php the_title(); ?></h1>
							<?php } ?>				
															
								<?php the_content(); ?>
		
						</div>
	

					</div>



<?php endwhile; ?>

<?php get_footer(); ?>
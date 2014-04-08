<?php
/* Template name: Page with left nav  */

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

					<div class="col-lg-6 col-md-6 col-sm-12">

						<?php if ( ! is_front_page() ) { ?>
							<h1><?php the_title(); ?></h1>
						<?php } ?>				
														
							<?php the_content(); ?>
	
					</div>

					<div class="col-lg-3 col-md-3 col-sm-12">
					<?php 
						the_post_thumbnail('large', array('class'=>'img img-responsive')); 
						echo wpautop( "<p class='news_date'>".get_post_thumbnail_caption()."</p>" );
						$thispage = new Pod('page',$id);
						$relatedpages = $thispage->get_field('page_related_pages');
						$relatedtasks = $thispage->get_field('page_related_tasks');
						if (taxonomy_exists('team')) $relatedteams = get_the_terms( $id, 'team' );
						if ($relatedpages){
							foreach ((array)$relatedpages as $r){
								if ($r['post_status'] == 'publish' && $r['ID'] != $id){
									$html.= "<li><a href='".$r['guid']."'>".$r['post_title']."</a></li>";
								}
							}
						}
						if ($relatedtasks){
							foreach ((array)$relatedtasks as $r){
								if ($r['post_status'] == 'publish' && $r['ID'] != $id){
									$html.= "<li><a href='".site_url()."/tasks/".$r['post_name']."'>".$r['post_title']."</a></li>";
								}
							}
						}
						if ($relatedteams){
							foreach ($relatedteams as $r){
									$html.= "<li><a href='".site_url()."/team/".$r->slug."'>".$r->name."</a>&nbsp;<span class='glyphicon glyphicon-list-alt'></span></li>";
							}
						}
						if ($html){
							echo "<div class='widget-box'>
							<h3 class='widget-title'>Related</h3>
							<ul>".$html."
							</ul>
							</div>";
						}						
						?>		
						
					</div>

		</div>



<?php endwhile; ?>

<?php get_footer(); ?>
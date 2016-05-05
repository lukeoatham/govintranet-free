<?php
/* Template name: News, updates and blogs  */

get_header(); 

?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>


			<div class="col-lg-5 col-md-5 col-sm-12 white">
				<div class="row">
					<div class='breadcrumbs'>
						<?php if(function_exists('bcn_display') && !is_front_page()) {
							bcn_display();
							}?>
					<?php
					$thistitle = get_the_title();
					echo "<h1>".$thistitle."</h1>";
					the_content();
					?>
					</div>
				</div>
				
				 <div role="tabpanel" data-example-id="togglable-news-tabs">
				    <ul id="newsTabs" class="nav nav-tabs" role="tablist">
				      <li role="presentation" class="active"><a href="#home" id="home-tab" role="tab" data-toggle="tab" aria-controls="home" aria-expanded="true"><?php _e('News' , 'govintranet'); ?></a></li>
				      <li role="presentation"><a href="#updates" role="tab" id="udpates-tab" data-toggle="tab" aria-controls="updates"><?php _e('Updates','govintranet');?></a></li>
					        <?php
						    $newsTypes = get_terms( 'news-type', array('hide_empty'=>true) );
						    if ( $newsTypes ):
					        	?>
								<li role="presentation" class="dropdown">
						        <a href="#" id="newsTabsDrop1" class="dropdown-toggle" data-toggle="dropdown" aria-controls="newsTabsDrop1-contents"><?php _e('Category','govintranet');?> <span class="caret"></span></a>					        
						        <ul class="dropdown-menu" role="menu" aria-labelledby="newsTabsDrop1" id="newsTabsDrop1-contents"><?php
							     foreach ( $newsTypes as $n){ 
								    $term_link = get_term_link($n->slug, 'news-type');
								  	echo '<li><a href="'.$term_link.'" tabindex="-1">'.$n->name.'</a></li>';   
							     }
						        ?></ul></li><?php
							endif;
							?>
					</ul>
				    <div id="newsTabsContent" class="tab-content">
				      	<div role="tabpanel" class="tab-pane fade in active" id="home" aria-labelledBy="home-tab">
				
							<?php
			
							$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
							$showitems = 10;
							if ( $paged > 1) $showitems = 10;
							$counter = 0;	
							
							$offset = get_post_meta($post->ID,'news_offset',true);
			    
						    //Next, determine how many posts per page you want (we'll use WordPress's settings)
						    $ppp = get_option('posts_per_page');
						
						    //Next, detect and handle pagination...
						    if ( $paged > 1 ) {
						
						        //Manually determine page query offset (offset + current page (minus one) x posts per page)
						        $page_offset = $offset + ( ($paged-1) * $ppp );
						
						    }
						    else {
						
						        //This is the first page. Just use the offset...
						        $page_offset = $offset;
						
						    }
			
							$cquery = array(
								'orderby' => 'post_date',
							    'order' => 'DESC',
							    'post_type' => 'news',
							    'posts_per_page' => $ppp,
							    'paged' => $paged,
							    'offset' => $page_offset												
								);							

							$qposts = new WP_Query($cquery);
							global $k; 
							$k = 0;
							while ($qposts->have_posts()) : $qposts->the_post();
								get_template_part( 'loop', 'newstwitter' );
							endwhile;
							if (  $qposts->max_num_pages > 1 ) : ?>
							<?php if (function_exists('wp_pagenavi')) : ?>
								<?php wp_pagenavi(array('query' => $qposts)); ?>
								<?php else : ?>
								<?php next_posts_link(__('&larr; Older items','govintranet'), $qposts->max_num_pages); ?>
								<?php previous_posts_link(__('Newer items &rarr;','govintranet'), $qposts->max_num_pages); ?>						
							<?php endif; 
								endif;
							?>							
			      
						</div>
						<div role="tabpanel" class="tab-pane fade" id="updates" aria-labelledBy="udpates-tab">
				        <?php
							$cquery = array(
							'orderby' => 'post_date',
						    'order' => 'DESC',
						    'post_type' => 'news-update',
						    'posts_per_page' => 10,
							);	        
				
							$qposts = new WP_Query($cquery);
								global $k; 
								$k = 0;
								while ($qposts->have_posts()) : $qposts->the_post();
									echo "<h4><a href='".get_permalink($post_>ID)."'>".get_the_title()."</a></h4>";
									echo '<span class="listglyph">'.get_the_date(); 
									echo '</span> ';
									if ( get_comments_number() ){
										echo "<a href='".get_permalink($qposts->ID)."#comments'>";
										printf( _n( '<span class="badge">1 comment</span>', '<span class="badge">%d comments</span>', get_comments_number(), 'govintranet' ), get_comments_number() );
										echo "</a>";
									}
									
									the_excerpt();
								endwhile;
								echo '<hr><p class="more-updates"><a class="small" href="'.site_url().'/news-update/">' . __("All updates" , "govintranet") . '</a> <span class="dashicons dashicons-arrow-right-alt2"></span></p>';	
								
					        	?>
					        
				      	</div>
				    </div>
				  </div><!-- /example -->
  			</div>
			<div class="col-lg-4 col-md-4 col-sm-6 white">
				<div class="widget-box">
				<h3>Blog posts</h3>
				<?php
					$cquery = array(
					'orderby' => 'post_date',
				    'order' => 'DESC',
				    'post_type' => 'blog',
				    'posts_per_page' => 5,
					);					
			       $qposts = new WP_Query($cquery);
				   global $k; 
				   $k = 0;
			       while ($qposts->have_posts()) : $qposts->the_post();
				   		$thistitle = get_the_title($post->ID);
						$edate = $post->post_date;
						$edate = date(get_option('date_format'),strtotime($edate));
						$thisURL=get_permalink($ID); 
						echo "<div class='media'>";
						if (true){
							$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $ID ), 'thumbnail' ); 
							if (!$image_uri){
								$image_uri = get_avatar($post->post_author,72);
								$image_uri = str_replace("alignleft", "alignleft tinyblogthumb", $image_uri);
								echo "<a class='pull-left' href='".get_permalink($post->ID)."'>{$image_uri}</a>";		
							} else {
								echo "<a class='pull-left' href='".get_permalink($post->ID)."'><img class='tinyblogthumb alignleft' src='{$image_uri[0]}' alt='".$thistitle."' /></a>";		
							}
						}
						echo "<div class='media-body'><a href='{$thisURL}'><strong>".$thistitle."</strong></a>";
						echo "<br><span class='news_date'>".$edate." by ";
						echo get_the_author();
						echo "</span>";
						if (true) the_excerpt();
						echo "</div></div>";
			        endwhile;
					if ($qposts->have_posts()){
						$landingpage = get_option('options_module_blog_page'); 
						if ( !$landingpage ):
							$landingpage_link_text = 'blogposts';
							$landingpage = site_url().'/blogposts/';
						else:
							$landingpage_link_text = get_the_title( $landingpage[0] );
							$landingpage = get_permalink( $landingpage[0] );
						endif;
						echo '<hr><p><strong><a title="{$landingpage_link_text}" class="small" href="'.$landingpage.'">'.$landingpage_link_text.'</a></strong> <span class="dashicons dashicons-arrow-right-alt2"></span></p>';
					} 			       
				   ?>
				</div>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-6 white">
			<?php 		if (is_active_sidebar('newslanding-widget-area')) dynamic_sidebar('newslanding-widget-area'); ?>
			</div>			

<?php endwhile; ?>

<?php get_footer(); ?>
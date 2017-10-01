<?php
/* Template name: News  */

get_header(); 

if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

		<div class="col-lg-7 col-md-8 col-sm-12 white">
			<div class="row">
				<div class='breadcrumbs'>
					<?php if(function_exists('bcn_display') && !is_front_page()) {
						bcn_display();
						}?>
				</div>
			</div>
			<?php
			$thistitle = get_the_title();
			echo "<h1>".$thistitle."</h1>";
			the_content();
			$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
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
					
			$newspost = new WP_Query($cquery);
			global $k; 
			$k = 0;
			while ($newspost->have_posts()) : $newspost->the_post();
				get_template_part( 'loop', 'newstwitter' );
			endwhile;

			if (  $newspost->max_num_pages > 1 ) : 
				if (function_exists('wp_pagenavi')) : 
					wp_pagenavi(array('query' => $newspost)); 
				else : 
					next_posts_link(__('&larr; Older items','govintranet'), $newspost->max_num_pages); 
					previous_posts_link(__('Newer items &rarr;','govintranet'), $newspost->max_num_pages); 
				endif; 
			endif;
			wp_reset_query(); ?>							
		</div>

		<div class="col-lg-4 col-lg-offset-1 col-md-4 col-sm-12" id="sidebar">
			<?php 
			get_template_part("part", "sidebar"); 
			get_template_part("part", "related");
			$taxonomies=array();
			$post_type = array();
			$taxonomies[] = 'news-type';
			$post_type[] = 'news';
			$post_cat = get_terms_by_post_type( $taxonomies, $post_type);
			if ($post_cat){
				echo "<div class='widget-box news-type-wrapper'><h3 class='widget-title'>" . __('Categories' , 'govintranet') . "</h3>";
				echo "<p class='taglisting " . $post->post_type . "'>";
				foreach($post_cat as $cat){
					if ($cat->name){
						$newname = str_replace(" ", "&nbsp;", $cat->name );
						echo "<span><a  class='wptag t".$cat->term_id."' href='".get_term_link($cat->slug,'news-type')."'>".$newname."</a></span> ";
					}
				}
				echo "</p></div>";
			}
			$tagcloud = gi_howto_tag_cloud('news');
			if ( $tagcloud != '' ) :   
				echo "<div class='widget-box tagcloud-wrapper'>";
				echo "<h3 class='widget-title'>".__('Search by tag','govintranet')."</h3>";
				echo "<div class='tagcloud'>";
				echo $tagcloud; 
				echo "</div>";
				echo "<br>";
				echo "</div>";					
			endif;
			if (is_active_sidebar('newslanding-widget-area')) dynamic_sidebar('newslanding-widget-area'); 
			?>
		</div>

<?php endwhile; ?>

<?php get_footer(); ?>
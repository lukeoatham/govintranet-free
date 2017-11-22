<?php
/*
Template Name: Newsboard
*/

function ht_newsboard_head_style(){
	$head_comp = get_option('options_complementary_colour', '#0b2d49');
	$head_back = get_theme_mod('header_background', '#0b2d49');
	$head_text = get_option('options_btn_text_colour','#ffffff');
	$custom_css = "
	#newsboardTabs.nav>li>a { background: ".$head_comp."; color: white; }
	#newsboardTabs.nav>li.active>a, #newsboardTabs.nav>li.active>a:focus { color: #555; background-color: #ffffff; border: 1px solid ".$head_back."; border-bottom: 1px solid #ffffff; }
	#newsboardTabs.nav>li>a:hover { color: #555; background-color: #ffffff; border: 1px solid ".$head_comp."; border-bottom: 1px solid #ffffff; }
	.nav-tabs { border-bottom: 1px solid ".$head_comp."; }
	.page-template-newsboard .calbox .cal-dow {
		background: ".$head_back.";
		color: ".$head_text.";
		font-size: 16px;
	}
	.page-template-newsboard .calbox { 
		width: 4.4em;
		border: 3px solid ".$head_back.";
		text-align: center;
		border-radius: 3px;
		background: #fff;
		box-shadow: 0 2px 3px rgba(0,0,0,.2);
		
	}

	.page-template-newsboard .calbox .calmonth {
		color: ".$head_comp.";
		text-transform: uppercase;
		font-weight: 800;
		font-size: 22px;
		line-height: 24px;
		padding-bottom: 5px;
	}
    ";
	wp_enqueue_style( 'ht_newsboard_head', get_template_directory_uri() . "/newsboard/style-newsboard.css");
	wp_add_inline_style('ht_newsboard_head' , $custom_css);

}
add_action('wp_head','ht_newsboard_head_style',4);

get_header(); 

if (!wp_script_is('jquery', 'queue')){
 	wp_enqueue_script('jquery');
}
wp_register_script( 'newsboard-js', get_template_directory_uri() . '/newsboard/newsboard.js', array('jquery','bootstrap-min'),'',true );
wp_enqueue_script( 'newsboard-js' );

if ( have_posts() ) : 
	while ( have_posts() ) : the_post(); ?>
		<div class="col-lg-8 col-md-8 col-sm-7 col-xs-12 white ">
			<div class="row">
				<div class='breadcrumbs'>
					<?php if(function_exists('bcn_display') && !is_front_page()) {
						bcn_display();
						}?>
				</div>
			</div>


		<?php if (the_content()) : ?>
		<div class="lead">  
			<?php the_content(); ?>
		</div>
		<?php endif; ?>
		
		<div id="newsboard" class="newsboard_<?php echo $id; ?>">
			
			<div role="tabpanel" data-example-id="togglable-newsboard-tabs">
				<ul id="newsboardTabs" class="nav nav-tabs" role="tablist">
				<?php
				if( have_rows('newsboard_tabs') ):
			
					// Build individual tabs
					
					$rowcount = 0;
	
				    while ( have_rows('newsboard_tabs') ) : the_row();
				
						$rowcount++;
						// get tab
	
						$title = get_sub_field('newsboard_tab_title'); 
						$active = "";
						if ($rowcount === 1) $active="active";
						
						/*
							CONTENT TYPES
							1 : News
							2 : News updates
							3 : Blog posts
							4 : Events
							5 : News type dropdown
							6 : News update type dropdown
							7 : Blog category dropdown
							8 : Event type dropdown
							*/
						
				        if ( get_sub_field('newsboard_tab_content_type') < 5 ) :	
					      	echo '<li role="presentation" class="'.$active.'">
					      	<a href="#ntab-'.$rowcount.'" id="newsboard-tab-'.$rowcount.'" role="tab" data-toggle="tab" aria-controls="ntab-'. $rowcount.'" aria-expanded="true">'.$title.'</a></li>';
					    endif;

				        if ( get_sub_field('newsboard_tab_content_type') == 5 ) :	

							$tax_terms = get_terms( 'news-type', array('hide_empty'=>true) );
							if ( $tax_terms ):
								echo'
								<li role="presentation" class="dropdown">
							    <a href="#" id="newsboard-droptab-'.$rowcount.'" class="dropdown-toggle" data-toggle="dropdown" aria-controls="newsboard-droptab-contents-'.$rowcount.'">'.$title.' <span class="caret"></span></a>					        
							    <ul class="dropdown-menu" role="menu" aria-labelledby="newsboard-droptab-'.$rowcount.'" id="newsboard-droptab-contents-'.$rowcount.'">';
							    
							     foreach ( $tax_terms as $n){ 
								    $term_link = get_term_link($n, 'news-type');
								  	echo '<li><a href="'.$term_link.'" tabindex="-1">'.$n->name.'</a></li>';   
							     }
							    echo '</ul></li>';
							endif;

						endif;

				        if ( get_sub_field('newsboard_tab_content_type') == 6 ) :	

							$tax_terms = get_terms( 'news-update-type', array('hide_empty'=>true) );
							if ( $tax_terms ):
								echo'
								<li role="presentation" class="dropdown">
							    <a href="#" id="newsboard-droptab-'.$rowcount.'" class="dropdown-toggle" data-toggle="dropdown" aria-controls="newsboard-droptab-contents-'.$rowcount.'">'.$title.' <span class="caret"></span></a>					        
							    <ul class="dropdown-menu" role="menu" aria-labelledby="newsboard-droptab-'.$rowcount.'" id="newsboard-droptab-contents-'.$rowcount.'">';
							    
							     foreach ( $tax_terms as $n){ 
								    $term_link = get_term_link($n, 'news-update-type');
								  	echo '<li><a href="'.$term_link.'" tabindex="-1">'.$n->name.'</a></li>';   
							     }
							    echo '</ul></li>';
							endif;

						endif;

				        if ( get_sub_field('newsboard_tab_content_type') == 7 ) :	

							$tax_terms = get_terms( 'blog-category', array('hide_empty'=>true) );
							if ( $tax_terms ):
								echo'
								<li role="presentation" class="dropdown">
							    <a href="#" id="newsboard-droptab-'.$rowcount.'" class="dropdown-toggle" data-toggle="dropdown" aria-controls="newsboard-droptab-contents-'.$rowcount.'">'.$title.' <span class="caret"></span></a>					        
							    <ul class="dropdown-menu" role="menu" aria-labelledby="newsboard-droptab-'.$rowcount.'" id="newsboard-droptab-contents-'.$rowcount.'">';
							    
							     foreach ( $tax_terms as $n){ 
								    $term_link = get_term_link($n, 'blog-category');
								  	echo '<li><a href="'.$term_link.'" tabindex="-1">'.$n->name.'</a></li>';   
							     }
							    echo '</ul></li>';
							endif;

						endif;
						
						if ( get_sub_field('newsboard_tab_content_type') == 8 ) :	

							$tax_terms = get_terms( 'event-type', array('hide_empty'=>true) );
							if ( $tax_terms ):
								echo'
								<li role="presentation" class="dropdown">
							    <a href="#" id="newsboard-droptab-'.$rowcount.'" class="dropdown-toggle" data-toggle="dropdown" aria-controls="newsboard-droptab-contents-'.$rowcount.'">'.$title.' <span class="caret"></span></a>					        
							    <ul class="dropdown-menu" role="menu" aria-labelledby="newsboard-droptab-'.$rowcount.'" id="newsboard-droptab-contents-'.$rowcount.'">';
							    
							     foreach ( $tax_terms as $n){ 
								    $term_link = get_term_link($n, 'event-type');
								  	echo '<li><a href="'.$term_link.'" tabindex="-1">'.$n->name.'</a></li>';   
							     }
							    echo '</ul></li>';
							endif;

						endif;
						
					endwhile;
					echo "</ul>";

					/************************
					// Build content areas
					************************/

					echo 
					'<div id="newsTabsContent" class="tab-content">
					';

					$rowcount = 0;
					wp_reset_postdata();
				    while ( have_rows('newsboard_tabs') ) : the_row();

						$output = "";
						$rowcount++;
						// get tab
	
						$active = "";
						if ($rowcount === 1) $active=" in active";
						
				        if ( get_sub_field('newsboard_tab_content_type') == 1 ) :	
       						$feature_first = get_sub_field('newsboard_feature_first'); 
							$tax_terms = get_sub_field('newsboard_news_type'); 
							echo '<div role="tabpanel" class="tab-pane fade'.$active.'" id="ntab-'.$rowcount.'" aria-labelledBy="newsboard-tab-'.$rowcount.'">';
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
							if ($tax_terms):
								$cquery['tax_query'] = array(array(
								'taxonomy' => 'news-type',
								'field' => 'term_id',
								'terms' => $tax_terms,
								));
							endif;
							$qposts = new WP_Query($cquery);
							global $k; 
							$k = 1;
							if ( $feature_first ) $k = 0;
							while ($qposts->have_posts()) : $qposts->the_post();
								get_template_part( 'loop', 'newstwitter' );
							endwhile;
							wp_reset_postdata();
							if ( get_sub_field('newsboard_link_to_more') ):
								$linkto = get_sub_field('newsboard_link_to');
								if ( $linkto == "1"):
									$pageid = get_sub_field('newsboard_page_link'); 
									$pageid = $pageid[0];
									echo "<a href='".get_permalink($pageid)."' class='more'>".get_the_title($pageid)."<span class='dashicons dashicons-arrow-right-alt2'></span></a>";
								elseif ( $linkto == "2" ):
									echo "<a href='".site_url('/news/')."' class='more'>More <span class='dashicons dashicons-arrow-right-alt2'></span></a>";
								elseif ( $linkto == "3" && $tax_terms[0] ):
									echo "<a href='".get_term_link($tax_terms[0], 'news-type')."' class='more'>More <span class='dashicons dashicons-arrow-right-alt2'></span></a>";
								endif;
							endif;		
							echo "</div>";
						endif;
						
				        if ( get_sub_field('newsboard_tab_content_type') == 2 ) :
							$tax_terms = get_sub_field('newsboard_news_update_type');
							echo '<div role="tabpanel" class="tab-pane fade'.$active.'" id="ntab-'.$rowcount.'" aria-labelledBy="newsboard-tab-'.$rowcount.'">';
							$counter = 0;	
							$cquery = array(
							    'post_type' => 'news-update',
								);
							if ($tax_terms):
								$cquery['tax_query'] = array(array(
								'taxonomy' => 'news-update-type',
								'field' => 'term_id',
								'terms' => $tax_terms,
								));
							endif;
							$qposts = new WP_Query($cquery);
							global $k; 
							$k = 1;
							while ($qposts->have_posts()) : $qposts->the_post();
								get_template_part( 'loop', 'newstwitter' );
							endwhile;
							wp_reset_postdata();
							if ( get_sub_field('newsboard_link_to_more') ):
								$linkto = get_sub_field('newsboard_link_to');
								if ( $linkto == "1"):
									$pageid = get_sub_field('newsboard_page_link'); 
									$pageid = $pageid[0];
									echo "<a href='".get_permalink($pageid)."' class='more'>".get_the_title($pageid)."<span class='dashicons dashicons-arrow-right-alt2'></span></a>";
								elseif ( $linkto == "2" ):
									echo "<a href='".site_url('/news-update/')."' class='more'>More <span class='dashicons dashicons-arrow-right-alt2'></span></a>";
								elseif ( $linkto == "3" && $tax_terms[0] ):
									echo "<a href='".get_term_link($tax_terms[0], 'news-update-type')."' class='more'>More <span class='dashicons dashicons-arrow-right-alt2'></span></a>";
								endif;
							endif;									
							echo "</div>";
						endif;
				        if ( get_sub_field('newsboard_tab_content_type') == 3 ) :
   				        	$feature_first = get_sub_field('newsboard_feature_first'); 
							$tax_terms = get_sub_field('newsboard_blog_category');
							echo '<div role="tabpanel" class="tab-pane fade'.$active.'" id="ntab-'.$rowcount.'" aria-labelledBy="newsboard-tab-'.$rowcount.'">';
							$counter = 0;	
							$cquery = array(
							    'post_type' => 'blog',
								);
							if ($tax_terms):
								$cquery['tax_query'] = array(array(
								'taxonomy' => 'blog-category',
								'field' => 'term_id',
								'terms' => $tax_terms,
								));
							endif;
							$qposts = new WP_Query($cquery);
							global $k; 
							global $feature_first;
							$k = 1;
							if ( $feature_first ) $k = 0;
							while ($qposts->have_posts()) : $qposts->the_post();
								get_template_part( 'newsboard/part', 'blog-listing' );
							endwhile;
							wp_reset_postdata();
							if ( get_sub_field('newsboard_link_to_more') ):
								$linkto = get_sub_field('newsboard_link_to');
								if ( $linkto == "1"):
									$pageid = get_sub_field('newsboard_page_link'); 
									$pageid = $pageid[0];
									echo "<a href='".get_permalink($pageid)."' class='more'>".get_the_title($pageid)."<span class='dashicons dashicons-arrow-right-alt2'></span></a>";
								elseif ( $linkto == "2" ):
									echo "<a href='".site_url('/blog/')."' class='more'>More <span class='dashicons dashicons-arrow-right-alt2'></span></a>";
								elseif ( $linkto == "3" && $tax_terms[0] ):
									echo "<a href='".get_term_link($tax_terms[0], 'blog-category')."' class='more'>More <span class='dashicons dashicons-arrow-right-alt2'></span></a>";
								endif;
							endif;		
							echo "</div>";
						endif;
				        if ( get_sub_field('newsboard_tab_content_type') == 4 ) :
							$tax_terms = get_sub_field('newsboard_event_types');
							echo '<div role="tabpanel" class="tab-pane fade'.$active.'" id="ntab-'.$rowcount.'" aria-labelledBy="newsboard-tab-'.$rowcount.'">';
							$tzone = get_option('timezone_string');
							if ( $tzone ) date_default_timezone_set($tzone);
							$sdate = date('Ymd');
							$stime = date('H:i');
							$counter = 0;	
							$cquery = array(
						    	'meta_query' => array(
								'relation' => 'OR',
							       array(
							           'key' => 'event_end_date',
							           'value' => $sdate,
							           'compare' => '>',
							       ),
							       array(
								       'relation' => 'AND',
								       array(
								           'key' => 'event_end_date',
								           'value' => $sdate,
								           'compare' => '=',
								       ),
								       array(
								           'key' => 'event_end_time',
								           'value' => $stime,
								           'compare' => '>',
							           ),
							       ),
								),
							    'orderby' => 'meta_value',
							    'meta_key' => 'event_start_date',
							    'order' => 'ASC',
							    'post_type' => 'event',
								'fields' => "id",
								);
							if ($tax_terms):
								$cquery['tax_query'] = array(array(
								'taxonomy' => 'event-type',
								'field' => 'term_id',
								'terms' => $tax_terms,
								));
							endif;

							$qposts = new WP_Query($cquery);
							global $k; 
							$k = 1;
							while ($qposts->have_posts()) : $qposts->the_post();
								get_template_part( 'newsboard/part', 'event-listing' );
							endwhile;
							wp_reset_postdata();
							if ( get_sub_field('newsboard_link_to_more') ):
								$linkto = get_sub_field('newsboard_link_to');
								if ( $linkto == "1"):
									$pageid = get_sub_field('newsboard_page_link'); 
									$pageid = $pageid[0];
									echo "<a href='".get_permalink($pageid)."' class='more'>".get_the_title($pageid)."<span class='dashicons dashicons-arrow-right-alt2'></span></a>";
								elseif ( $linkto == "2" ):
									echo "<a href='".site_url('/event/')."' class='more'>More <span class='dashicons dashicons-arrow-right-alt2'></span></a>";
								elseif ( $linkto == "3" && $tax_terms[0] ):
									echo "<a href='".get_term_link($tax_terms[0], 'event-type')."' class='more'>More <span class='dashicons dashicons-arrow-right-alt2'></span></a>";
								endif;
							endif;		
							echo "</div>";
						endif;
						
					endwhile;
				
					echo "</div>";					
					
				endif;
				?>

			</div>
		
		</div>

	</div>

	<div class="col-lg-4 col-md-4 col-sm-5 col-xs-12 white" id="sidebar">
		<h2 class="sr-only">Sidebar</h2>
		<?php 
		get_template_part("part", "sidebar");
		if (is_active_sidebar('newslanding-widget-area')) dynamic_sidebar('newslanding-widget-area'); 
		?>
	</div>		

	<?php 
	endwhile; 
endif; 

get_footer(); ?>
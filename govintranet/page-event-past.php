<?php
/* Template name: Events (inc past) page */

$cdir = '';
$eventcat = '';
if ( isset($_GET['cdir'])) $cdir = $_GET['cdir'];
if (isset($_GET['cat'])) $eventcat = $_GET['cat'];
$tzone = get_option('timezone_string');
date_default_timezone_set($tzone);

get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

	<div class="col-md-8 white ">
		<div class="row">
			<div class='breadcrumbs'>
				<?php if(function_exists('bcn_display') && !is_front_page()) {
					bcn_display();
					}?>
			</div>
		</div>

		<h1><?php the_title(); 
		$pub = get_terms( 'event-type', 'orderby=count&hide_empty=1' );
		$cat_id = $_GET['cat'];
		if (count($pub)>0 and $cat_id!=''){
			foreach ($pub as $sc) { 
				if ($cat_id == $sc->slug) { echo ' - '.$sc->name; } 
			}
		}
		if ($cdir=='b') {
			echo " <small>";
			printf( __('Past %s', 'govintranet') , strtolower(get_the_title() ) ); 
			echo "</small>" ;
		}
		?>
		</h1>
			
		<?php 
		if ($eventcat==""){
			if ($cdir=="b"){
				$timetravel = "<div class='futureevents'><p><a href='".get_permalink(get_the_id())."?cdir=f'>".sprintf(__('Future %s' , 'govintranet' ) , strtolower(get_the_title() ) )." &raquo;</a></p></div>";
			} else {
				$timetravel = "<div class='pastevents'><p><a href='".get_permalink(get_the_id())."?cdir=b'>&laquo;".sprintf(__('Past %s' , 'govintranet' ) , strtolower(get_the_title() ) )."</a></p></div>";
			}
		} else {
			if ($cdir=="b"){
				$timetravel = "<div class='futureevents'><p><a href='".get_permalink(get_the_id())."?cdir=f&cat=".$eventcat."'>".sprintf(__('Future %s' , 'govintranet' ) , strtolower(get_the_title() ) )." &raquo;</a></p></div>";
			} else {
				$timetravel = "<div class='pastevents'><p><a href='".get_permalink(get_the_id())."?cdir=b&cat=".$eventcat."'>&laquo; ".sprintf(__('Past %s' , 'govintranet' ) , strtolower(get_the_title() ) )."</a></p></div>";
			}																
		}

		the_content(); 
		
		echo "<br>".$timetravel;							
		$sdate= date('Ymd');
		$stime= date('H:i');

		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

		if ($cat_id!=''){ // events for individual event type

			if ($cdir=="b"){  //past events

				$cquery = array(
			    'tax_query' => array(
			        array(
			            'taxonomy' => 'event-type',
			            'field' => 'slug',
			            'terms' => $cat_id,
				       ),
				    ),
		  			'meta_query' => array(
					'relation' => 'OR',
				       array(
				           'key' => 'event_end_date',
				           'value' => $sdate,
				           'compare' => '<',
			    	       'type' => 'DATE',
				       ),
				       array(
					       'relation' => 'AND',
					       array(
					           'key' => 'event_end_date',
					           'value' => $sdate,
					           'compare' => '=',
							   'type' => 'DATE',
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
				    'order' => 'DESC',
				    'post_type' => 'event',
					'posts_per_page' => 10,
				    'paged' => $paged												
				);

			} else { //future events, single event type

				$cquery = array(
			    'tax_query' => array(
			        array(
			            'taxonomy' => 'event-type',
			            'field' => 'slug',
			            'terms' => $cat_id,
				       ),
				    ),	
		   			'meta_query' => array(
					'relation' => 'OR',
				       array(
				           'key' => 'event_end_date',
				           'value' => $sdate,
				           'compare' => '>',
			    	       'type' => 'DATE',
				       ),
				       array(
					       'relation' => 'AND',
					       array(
					           'key' => 'event_end_date',
					           'value' => $sdate,
					           'compare' => '=',
							   'type' => 'DATE',
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
					'posts_per_page' => 10,
				    'paged' => $paged												
				);

			}	
				
		} else { //all events

			if ($cdir=="b"){ //past events all event types
				$cquery = array(
					'post_type' => 'event',
					'posts_per_page' => 10,
		  			'meta_query' => array(
					'relation' => 'OR',
				       array(
				           'key' => 'event_end_date',
				           'value' => $sdate,
				           'compare' => '<',
			    	       'type' => 'DATE',
				       ),
				       array(
					       'relation' => 'AND',
					       array(
					           'key' => 'event_end_date',
					           'value' => $sdate,
					           'compare' => '=',
							   'type' => 'DATE',
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
				    'order' => 'DESC',
				    'paged' => $paged
					);
					
			} else { // future events, all event types

				$cquery = array(
					'post_type' => 'event',
					'posts_per_page' => 10,
		  			'meta_query' => array(
					'relation' => 'OR',
				       array(
				           'key' => 'event_end_date',
				           'value' => $sdate,
				           'compare' => '>',
			    	       'type' => 'DATE',
				       ),
				       array(
					       'relation' => 'AND',
					       array(
					           'key' => 'event_end_date',
					           'value' => $sdate,
					           'compare' => '=',
							   'type' => 'DATE',
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
				    'paged' => $paged
					);
			
			}
		}

		$customquery = new WP_Query($cquery);
		
		if (!$customquery->have_posts()){
			echo "<p>" . __('Nothing to show' , 'govintranet') . ".</p>";
		}
			
		if ( $customquery->have_posts() ) {
			while ( $customquery->have_posts() ) {
				$customquery->the_post();
				echo "<div class='media'>";
				if ( has_post_thumbnail( $post->ID ) ) {
					echo "<a href='" .get_permalink() . "'>";
					the_post_thumbnail('thumbnail',"class=alignleft");
					echo  "</a>";
				}	
				echo "<div class='media-body'><h3><a href='" .get_permalink() . "'>" . get_the_title() . "</a></h3>";
				$thisdate =  get_post_meta($post->ID,'event_start_date',true); 
				echo "<strong>".date(get_option('date_format'),strtotime($thisdate))."</strong>";
				the_excerpt();
				echo "</div></div>";
			}
		}
		
		wp_reset_query();
		
		echo $timetravel;
		?>
	
		<?php if (  $customquery->max_num_pages > 1 ) : ?>
			<?php if (function_exists('wp_pagenavi')) : ?>
				<?php wp_pagenavi(array('query' => $customquery)); ?>
				<?php else : ?>
				<?php next_posts_link(__('&larr; Older items','govintranet'), $customquery->max_num_pages); ?>
				<?php previous_posts_link(__('Newer items &rarr;','govintranet'), $customquery->max_num_pages); ?>						
			<?php endif; ?>
		<?php endif; ?>

	</div>
	
	<div class="col-md-4 last">
		<?php 
		dynamic_sidebar('eventslanding-widget-area'); 
		$taxonomies=array();
		$post_type = array();
		$taxonomies[] = 'event-type';
		$post_type[] = 'event';
		$post_cat = get_terms_by_post_type( $taxonomies, $post_type);
		if ($post_cat){
			echo "<div class='widget-box'><h3 class='widget-title'>" . __('Categories' , 'govintranet') . "</h3>";
			echo "<p class='taglisting {$post->post_type}'>";
			echo "<span><a class='wptag t' href='".site_url()."/events/?cdir=".$cdir."'>" . __('All' , 'govintranet') . "</a></span> ";
			foreach($post_cat as $cat){
				if ($cat->term_id > 1 && $cat->name){
					$newname = str_replace(" ", "&nbsp;", $cat->name );
					echo "<span><a  class='wptag t".$cat->term_id."' href='".get_permalink(get_the_id())."?cat=".$cat->slug."&cdir=".$cdir."'>".$newname."</a></span> ";
				}
			}
			echo "</p></div>";
		}
		if ( gi_howto_tag_cloud('event') ) :
			echo "<div class='widget-box'>";
			echo  "<h3 class='widget-title'>" . __('Browse by tag' , 'govintranet') . "</h3>";
			echo gi_howto_tag_cloud('event'); 
			echo "<br>";
			echo "</div>";
		endif;
		?>
	
	 	
	
	</div>

<?php endwhile; ?>

<?php get_footer(); ?>
<?php
/* Template name: Event page */

$cdir=$_GET['cdir'];
$eventcat = $_GET['cat'];

get_header(); 

$tzone = get_option('timezone_string');
date_default_timezone_set($tzone);
$sdate=date('Ymd');

//CHANGE CLOSED EVENTS TO DRAFT STATUS
$oldvacs = query_posts(array(
'post_type'=>'event',
'meta_query'=>array(array(
'key'=>'event_end_date',
'value'=>$sdate,
'compare'=>'<='
))));

if ( count($oldvacs) > 0 ){
	foreach ($oldvacs as $old) {
		if ($sdate == date('Ymd',strtotime(get_post_meta($old->ID,'event_end_date',true)) )): // if expiry today, check the time
			if (date('H:i:s',strtotime(get_post_meta($old->ID,'event_end_time',true))) > date('H:i:s') ) continue;
		endif;
	  $my_post = array();
	  $my_post['ID'] = $old->ID;
	  $my_post['post_status'] = 'draft';
	  wp_update_post( $my_post );
	  if (function_exists('wp_cache_post_change')) wp_cache_post_change( $old->ID ) ;
	}	
}
wp_reset_query();
?>
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
		<div class="col-lg-8 col-md-8 col-sm-7 col-sx-12 white ">
			<div class="row">
				<div class='breadcrumbs'>
					<?php if(function_exists('bcn_display') && !is_front_page()) {
						bcn_display();
						}?>
				</div>
			</div>
			<h1><?php the_title(); ?>
			<?php
			$pub = get_terms( 'event-type', 'orderby=count&hide_empty=1' );
			$cat_id = $_GET['cat'];;
			if (count($pub)>0 and $cat_id!=''){
				foreach ($pub as $sc) { 
					if ($cat_id == $sc->slug) { echo ' - '.$sc->name; } 
				}
			}
			?>
			</h1>
			<?php the_content(); ?>
			<?php						
			$sdate=date('Ymd');
			$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

			if ($cat_id!=''){ // show events by type
					$cquery = array(

				    'tax_query' => array(
				        array(
				            'taxonomy' => 'event-type',
				            'field' => 'slug',
				            'terms' => $cat_id,
					       ),
					    ),	

				   'meta_query' => array(
				       array(
			           		'key' => 'event_start_date',
			        	   'value' => $sdate,
			    	       'compare' => '>=',
			    	       'type' => 'DATE',
			    	       ) 
		    	        ),   
					    'orderby' => 'meta_value',
					    'meta_key' => 'event_start_date',
					    'order' => 'ASC',
					    'post_type' => 'event',
						'posts_per_page' => 10,
					    'paged' => $paged												
					);
				}	else { //all events
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
				$customquery = new WP_Query($cquery);
				
				if (!$customquery->have_posts()){
					echo "<p>" . __('Nothing to show' ,'govintranet') . ".</p>";
				}
				if ( $customquery->have_posts() ) {
	
					while ( $customquery->have_posts() ) {
						$customquery->the_post();
						echo "<div class='media'>";
						if ( has_post_thumbnail( $post->ID ) ) {
							the_post_thumbnail('thumbnail',"class=alignleft");
						}	
						echo "<div class='media-body'><h3><a href='" .get_permalink() . "'>" . get_the_title() . "</a></h3>";
						$thisdate =  get_post_meta($post->ID,'event_start_date',true); 
						$thistime =  get_post_meta($post->ID,'event_start_time',true); 
						echo "<strong>".date(get_option('date_format'),strtotime($thisdate))." ".date(get_option('time_format'),strtotime($thistime))."</strong>";
						the_excerpt();
						echo "</div></div>";
					}
				}
							
				wp_reset_query();
				echo $timetravel;
				?>
				<?php if (  $customquery->max_num_pages > 1 ) : ?>
					<?php if (function_exists(wp_pagenavi)) : ?>
						<?php wp_pagenavi(array('query' => $customquery)); ?>
						<?php else : ?>
						<?php next_posts_link(__('&larr; Older items','govintranet'), $customquery->max_num_pages); ?>
						<?php previous_posts_link(__('Newer items &rarr;','govintranet'), $customquery->max_num_pages); ?>						
					<?php endif; ?>
				<?php endif; ?>
			</div>
			<div class="col-lg-4 col-md-4 col-sm-5 col-sx-12">
				<?php
				$taxonomies=array();
				$post_type = array();
				$taxonomies[] = 'event-type';
				$post_type[] = 'event';
				$post_cat = get_terms_by_post_type( $taxonomies, $post_type);
				if ($post_cat){
					echo "<div class='widget-box'><h3 class='widget-title'>". __('Categories' , 'govintranet') . "</h3>";
					echo "<p class='taglisting {$post->post_type}'>";
					echo "<span><a  class='wptag t' href='".get_permalink(get_the_id())."/?cdir=".$cdir."'>" . __('All' , 'govintranet') . "</a></span> ";
					foreach($post_cat as $cat){
						if ($cat->name!='Uncategorized' && $cat->name){
							$newname = str_replace(" ", "&nbsp;", $cat->name );
							echo "<span><a  class='wptag t".$cat->term_id."' href='".get_permalink(get_the_id())."?cat=".$cat->slug."&cdir=".$cdir."'>".$newname."</a></span> ";
						}
					}
					echo "</p></div>";
				}

				$eventcloud = gi_howto_tag_cloud('event'); 
				if ($eventcloud) :?>
					<div class="widget-box">
					<h3 class='widget-title'><?php _e('Browse by tag' , 'govintranet'); ?></h3>
					<?php echo $eventcloud; ?>
					</div>
					<br>			
				<?php	
				endif;
				?>

				<?php dynamic_sidebar('eventslanding-widget-area'); ?> 
			
			</div>
		</div>
<?php endwhile; ?>

<?php get_footer(); ?>
<?php
/* Template name: News  */

get_header(); 


?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>


					<div class="col-lg-7 col-md-8 col-sm-12 white ">
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

$removenews = get_transient('cached_removenews'); 
if (!$removenews || !is_array($removenews)){

//remove old news

							$gis = "general_intranet_time_zone";
							$tzone = get_option($gis);
							date_default_timezone_set($tzone);
							$tdate= getdate();
							$tdate = $tdate['year']."-".$tdate['mon']."-".$tdate['mday'];
							$tday = date( 'd' , strtotime($tdate) );
							$tmonth = date( 'm' , strtotime($tdate) );
							$tyear= date( 'Y' , strtotime($tdate) );
							$sdate=$tyear."-".$tmonth."-".$tday;
							$stime=date('H:i');

$oldnews = query_posts(array(
'post_type'=>'news',
'meta_query'=>array(array(
'relation'=>'AND',
'key'=>'expiry_date',
'value'=>$sdate,
'compare'=>'<='
),
array(
'key'=>'expiry_time',
'value'=>$stime,
'compare'=>'<='
))));

if ( count($oldnews) > 0 ){
foreach ($oldnews as $old) {
	$expiryaction = get_post_meta($old->ID,'expiry_action',true);
	if ($expiryaction=='Revert to draft status'){
		  $my_post = array();
		  $my_post['ID'] = $old->ID;
		  $my_post['post_status'] = 'draft';
		  wp_update_post( $my_post );
		  delete_post_meta($old->ID, 'expiry_date');
		  delete_post_meta($old->ID, 'expiry_time');
		  delete_post_meta($old->ID, 'expiry_action');
		  wp_cache_post_change( $old->ID ) ;
		  wp_cache_post_change( $my_post ) ;		  
	}	
	if ($expiryaction=='Change to regular news'){
		update_post_meta($old->ID, 'news_listing_type', 'Regular', 'Need to know'); 
		  delete_post_meta($old->ID, 'expiry_date');
		  delete_post_meta($old->ID, 'expiry_time');
		  delete_post_meta($old->ID, 'expiry_action');
		  wp_cache_post_change( $old->ID ) ;
	}	
	if ($expiryaction=='Move to trash'){
		  $my_post = array();
		  $my_post['ID'] = $old->ID;
		  $my_post['post_status'] = 'trash';
		  delete_post_meta($old->ID, 'expiry_date');
		  delete_post_meta($old->ID, 'expiry_time');
		  delete_post_meta($old->ID, 'expiry_action');
		  wp_update_post( $my_post );
		  wp_cache_post_change( $old->ID ) ;
		  wp_cache_post_change( $my_post ) ;		  
	}	
}
}
$timer=array();
$timer[]='last_removed';
$gi = "general_intranet_expired_news_cache";
$expirednewscache = get_option($gi);
if ($expirednewscache <= 0 ) {
	$expirednewscache = 60*8;
}
set_transient('cached_removenews',$timer,60*$expirednewscache); // customised cache period
wp_reset_query();
}

							$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
							$counter = 0;	
							if(function_exists('genarate_ajax_pagination') && $_SERVER['SERVER_NAME'] != 'intranet2.culture.gov.uk' ) {
							$cquery = array(
								'orderby' => 'post_date',
							    'order' => 'DESC',
							    'post_type' => 'news',
							    'posts_per_page' => 10,
							    'post_status' => 'publish'
									
								);
								}
								else
								{
							$cquery = array(
								'orderby' => 'post_date',
							    'order' => 'DESC',
							    'post_type' => 'news',
							    'posts_per_page' => 10,
							    'paged' => $paged												
								);
									
								}

       $projectspost = new WP_Query($cquery);
global $k; 
$k = 0;
       while ($projectspost->have_posts()) : $projectspost->the_post();
         get_template_part( 'loop', 'newstwitter' );
       endwhile;
    ?>
    <?php 
        if(function_exists('genarate_ajax_pagination') && $_SERVER['SERVER_NAME'] != 'intranet2.culture.gov.uk' ) {
        genarate_ajax_pagination('Load more news', 'blue', 'loop-newstwitter', $cquery); 
        }
        else {
	        						if (  $projectspost->max_num_pages > 1 ) : ?>
						<?php if (function_exists(wp_pagenavi)) : ?>
							<?php wp_pagenavi(array('query' => $projectspost)); ?>
 						<?php else : ?>
							<?php next_posts_link('&larr; Older items', $projectspost->max_num_pages); ?>
							<?php previous_posts_link('Newer items &rarr;', $projectspost->max_num_pages); ?>						
						<?php endif; 
						?>
					<?php endif; 
			wp_reset_query();								
							
        }
    ?>							
				</div>

			<div class="col-lg-4 col-lg-offset-1 col-md-4 col-sm-12">
			<?php
				$taxonomies=array();
				$post_type = array();
				$taxonomies[] = 'category';
				$post_type[] = 'news';
				$post_cat = get_terms_by_post_type( $taxonomies, $post_type);
				if ($post_cat){
					echo "<div class='widget-box'><h3 class='widget-title'>Categories</h3>";
					echo "<p class='taglisting {$post->post_type}'>";
					foreach($post_cat as $cat){
						if ($cat->name!='Uncategorized' && $cat->name){
						$newname = str_replace(" ", "&nbsp;", $cat->name );
						echo "<span class='wptag t".$cat->term_id."'><a href='/news-by-category/?cat=".$cat->slug."'>".$newname."</a></span> ";
					}
					}
					echo "</p></div>";
				}
				
				if ( my_colorful_tag_cloud('', 'category' , 'news') != '' ) :   
			
					echo "<div class='widget-box'>";
					echo "<h3 class='widget-title'>Search by tag</h3>";
					echo "<div class='tagcloud'>";
					echo my_colorful_tag_cloud('', 'category' , 'news'); 
					echo "</div>";
					echo "<br>";
					echo "</div>";					

				endif;
				
				dynamic_sidebar('newslanding-widget-area'); 
			?>

			</div>

<?php endwhile; ?>

<?php get_footer(); ?>
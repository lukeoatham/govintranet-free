<?php
/* Template name: Home page */

get_header(); ?>



<?php if ( have_posts() ) while ( have_posts() ) : the_post(); 

	// Load intranet homepage settings
	$hc = "homepage_control_campaign_message";
	$hcitem = get_option($hc);
	$campaign_message = $hcitem; 

	$hc = new Pod ('homepage_control');
	$top_pages =  $hc->get_field('top_pages');
	

	$hc = "homepage_control_emergency_message";
	$hcitem = get_option($hc);
	$homecontent =  $hcitem;

	$hc = "homepage_control_emergency_message_style";
	$hcitem = get_option($hc);
	$homecontentcolour =  strtolower($hcitem);


	$hc = "homepage_control_homepage_column_layout";
	$hcitem = get_option($hc);
	$homecols =  $hcitem;
	if ($homecols){
		$col1 = substr($homecols, 0,1); 
		$col2 = substr($homecols, 4,1); 
		$col3 = substr($homecols, 8,1); 
	} else {
		$col1 = 6; 
		$col2 = 3; 
		$col3 = 3; 
		
	}
	$gis = "general_intranet_forum_support";
	$forumsupport = get_option($gis);

	if ($homecontent ): //Display emergency message 
	?>
		<div class="col-lg-12">
			<div class="alert alert-dismissable alert-<?php echo $homecontentcolour; ?>">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<?php	echo apply_filters('the_content', $homecontent, true);	 ?>
				</div>
		</div>	
<?php endif; ?>


		<div class="col-lg-<?php echo $col1; ?> col-md-<?php echo $col1; ?> col-sm-7">
			<?php 	dynamic_sidebar('home-widget-area0'); ?>	
		</div>
		<div class="col-lg-<?php echo $col2; ?> col-md-<?php echo $col2; ?> col-sm-5">
			<?php 	dynamic_sidebar('home-widget-area1'); ?>
			<?php 	dynamic_sidebar('home-widget-area2'); ?>
		</div>
		<div class="col-lg-<?php echo $col3; ?> col-md-<?php echo $col3; ?> col-sm-5">
		<?php
	$gis = "general_intranet_forum_support";
	$forumsupport = get_option($gis);

	if ($forumsupport && is_active_sidebar('login-widget-area') ) : 
			$current_user = wp_get_current_user();
			?>
		<div id="loginrow" class="category-block">
			<div id="loginaccordion">
			<h3 class="widget-title">
			        <a class="accordion-toggle" data-toggle="collapse" data-parent="#loginaccordion" href="#logincollapseOne">
				<?php if (is_user_logged_in()):?>
					      <?php 
					      if (function_exists('get_wp_user_avatar')){
						      echo get_wp_user_avatar(intval($current_user->id),32); 
					      }
					      echo " ".$current_user->display_name; ?>
				<?php else :?>
					       Login <i class="glyphicon glyphicon-chevron-down"></i>
				<?php endif; ?>
			        </a>
			</h3>
			    </div>
			    <div id="logincollapseOne" class="xpanel-collapse collapse out">
			      <div class="xpanel-body">
					<?php dynamic_sidebar('login-widget-area');	?> 
				</div>
<!-- 				<h3 class="widget-title>">Personalisation</h3> -->
				
			</div>
		</div>
<?php endif; ?>

			<?php 	dynamic_sidebar('home-widget-area3'); 
				if ($forumsupport):			?>	
					<div class="category-block"><hr><p><strong><a title="More in forums" class="small" href="<?php echo site_url();?>/forums/">More in forums</a></strong> <i class='glyphicon glyphicon-chevron-right small'></i></p></div>
			<?php 	
				endif;
				dynamic_sidebar('home-widget-area4'); ?>	
		</div>

		<?php	if ($campaign_message) :  //Display campaign message ?>
		<div class="clearfix"></div>
		<div class="col-lg-12">
			<?php 	echo apply_filters('the_content', $campaign_message, true);	 ?>
			<br>
		</div>

		<?php endif;?>
	


<?php endwhile; ?>
<?php

$removenews = get_transient('cached_removenews'); 
if (!$removenews || !is_array($removenews)){

//process expired news

$tzone = get_option('timezone_string');
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
			  if (function_exists('wp_cache_post_change')) wp_cache_post_change( $old->ID ) ;
			  if (function_exists('wp_cache_post_change')) wp_cache_post_change( $my_post ) ;		  
		}	
		if ($expiryaction=='Change to regular news'){
			update_post_meta($old->ID, 'news_listing_type', 'Regular', 'Need to know'); 
			  delete_post_meta($old->ID, 'expiry_date');
			  delete_post_meta($old->ID, 'expiry_time');
			  delete_post_meta($old->ID, 'expiry_action');
			  if (function_exists('wp_cache_post_change')) wp_cache_post_change( $old->ID ) ;
		}	
		if ($expiryaction=='Move to trash'){
			  $my_post = array();
			  $my_post['ID'] = $old->ID;
			  $my_post['post_status'] = 'trash';
			  delete_post_meta($old->ID, 'expiry_date');
			  delete_post_meta($old->ID, 'expiry_time');
			  delete_post_meta($old->ID, 'expiry_action');
			  wp_update_post( $my_post );
			  if (function_exists('wp_cache_post_change')) wp_cache_post_change( $old->ID ) ;
			  if (function_exists('wp_cache_post_change')) wp_cache_post_change( $my_post ) ;		  
		}	
	}
}
$timer=array();
$timer[]='last_removed';
$gi = "general_intranet_expired_news_cache";
$expirednewscache = get_option($gi);
if ($expirednewscache <= 0 ) {
	$expirednewscache = 8;//default to 8 hours for checking expired news
}

set_transient('cached_removenews',$timer,60*$expirednewscache); // customised cache period
wp_reset_query();
}


//
?>
<?php get_footer(); ?>

<?php
/* Template name: Home page */

get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); 
?>
<div class="row white">

<?php
	// Load intranet homepage settings
	$hc = "homepage_control_campaign_message";
	$hcitem = get_option($hc);
	$campaign_message = $hcitem; 

	$hc = new Pod ('homepage_control');
	$top_pages =  $hc->get_field('top_pages');
	

	$hc = "homepage_control_emergency_message";
	$hcitem = get_option($hc);
	$homecontent =  $hcitem;

	if ($homecontent ): //Display emergency message
	?>
	<div class="twelvecol last">
		<div class="content-wrapper">
			<div id='intranet-announcement' class="bbp-template-notice">
				<?php	echo wpautop($homecontent); ?>
			</div>
		</div>
	</div>	
	<?php endif; ?>

	<div class="content-wrapper">
		<div class="sixcol">
			<?php 	dynamic_sidebar('home-widget-area0'); ?>	
		</div>
		<div class="threecol">
			<?php 	dynamic_sidebar('home-widget-area1'); ?>
			<?php 	dynamic_sidebar('home-widget-area2'); ?>
		</div>
		<div class="threecol last">
			<?php 	dynamic_sidebar('home-widget-area3'); ?>	
			<div class="category-block"><p><a class="small" href="/about/forums/">More in forums</a></p></div>
			<?php 	dynamic_sidebar('home-widget-area4'); ?>	
		</div>
		<div class="twelvecol white last">
			<?php	if ($campaign_message) { //Display campaign message
			echo "<div class='content-wrapper'>".wpautop($campaign_message)."</div>"; 
			}
			?>
			<br>
		</div>
	</div>
</div>

<?php endwhile; ?>
<?php

$removenews = get_transient('cached_removenews'); 
if (!$removenews || !is_array($removenews)){

//process expired news

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
			  delete_post_meta($old->ID, 'expiry_action');
		}	
		if ($expiryaction=='Change to regular news'){
			update_post_meta($old->ID, 'news_listing_type', 'Regular', 'Need to know'); 
			  delete_post_meta($old->ID, 'expiry_date');
			  delete_post_meta($old->ID, 'expiry_action');
		}	
		if ($expiryaction=='Move to trash'){
			  $my_post = array();
			  $my_post['ID'] = $old->ID;
			  $my_post['post_status'] = 'trash';
			  delete_post_meta($old->ID, 'expiry_date');
			  delete_post_meta($old->ID, 'expiry_action');
			  wp_update_post( $my_post );
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
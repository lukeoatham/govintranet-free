<?php
global $k;
$k++;
$thistitle = get_the_title($ID);
$thisURL=get_permalink($ID);
$vidpod = new Pod ( 'news' , $post->ID );

$videostill = $vidpod->get_field('video_still');		
$videoguid = $videostill[0]['guid'];
$vidid = $videostill[0]['ID'];
$videoimg = wp_get_attachment_image( $vidid, 'thumbnail');
if ($k==1 && $paged<2){
		$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $ID ), 'large' ); 
		if ($image_uri!="" && $videostill==''){
			echo "<a href='/news/content/".$post->post_name."/'><img class='size-full' src='{$image_uri[0]}' alt='".govintranetpress_custom_title($post->title)."' /></a>";																			} 
		if ($videostill){
			echo "<a href='/news/content/".$post->post_name."/'>".$videoimg."</a>";
		} 					
}
else {
	$image_url = "<a href='/news/content/".$post->post_name."/'>".get_the_post_thumbnail($ID, 'thumbnail', array('class' => 'alignright','width'=>'{$image_uri[1]}','height'=>'{$image_uri[2]}'))."</a>";
	if ($videostill){
		$videoimg = wp_get_attachment_image( $vidid, 'thumbnail', false, array('class'=>'alignright','width'=>'{$image_uri[1]}','height'=>'{$image_uri[2]}') );
		$image_url= "<a href='/news/content/".$post->post_name."/'>".$videoimg."</a>";
	} 					
}
$thisexcerpt= get_the_excerpt();
$thisdate= $post->post_date;
$thisdate=date("j M Y",strtotime($thisdate));

echo "<div class='newsitem'>";
	$needtoknow = 0;get_post_meta($post->ID,'news_listing_type',true) ;
if (get_post_meta($post->ID,'news_listing_type',true) == 1 ) {
	$needtoknow = " class='need-to-know'";
}
echo "<h2><a {$needtoknow} href='{$thisURL}'> ".$thistitle."</a></h2>".$image_url."";
echo "<div class='taglisting news'>";
echo "<p><span class='news_date'>".$thisdate."</span>";
$post_type = '';
$thistype='';
$thistypeid = '';
$post_type = get_the_category();
foreach ($post_type as $p) {
	$thistype = $p->name;
	$thistypeid = $p->cat_ID;
}
if ($thistype!='Uncategorized' && $thistype){

echo "&nbsp;<span class='brdall b".$p->term_id."'>".$thistype."</span>";
}
echo "</div>";
the_excerpt();
echo "<p class='news_date'><a class='more' href='{$thisURL}' title='{$thistitle}' >Read more</a></p>";
echo "</div><div class='clearfix'></div><hr class='light' />";
?>

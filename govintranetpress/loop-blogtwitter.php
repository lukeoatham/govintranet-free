<?php
global $k;
$k++;
$thistitle = get_the_title($ID);
$thisURL=get_permalink($ID);
$vidpod = new Pod ( 'news' , $post->ID );

$videostill = $vidpod->get_field('video_still');		
$videoguid = $videostill[0]['guid'];
$vidid = $videostill[0]['ID'];
$videoimg = wp_get_attachment_image( $vidid, 'single-post-thumbnail');
if ($k==1 && $paged<2){
		$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $ID ), 'homethumb' ); 
		if ($image_uri!="" && $videostill==''){
			echo "<a href='/blog/".$post->post_name."/'><img class='size-full' src='{$image_uri[0]}' alt='".govintranetpress_custom_title($post->title)."' /></a>";																			} 
		if ($videostill){
			echo "<a href='/blog/".$post->post_name."/'>".$videoimg."</a>";
		} 					
}
else {
	$image_url = "<a href='/blog/".$post->post_name."/'>".get_the_post_thumbnail($ID, 'thumbnail', array('class' => 'alignright'))."</a>";
	if ($videostill){
		$videoimg = wp_get_attachment_image( $vidid, 'single-post-thumbnail', false, array('class'=>'alignright') );
		$image_url= "<a href='/blog/".$post->post_name."/'>".$videoimg."</a>";
	} 					
}
$thisexcerpt= get_the_excerpt();
$thisdate= $post->post_date;
$thisdate=date("j M Y",strtotime($thisdate));

echo "<div class='newsitem'>".$image_url;
echo "<a href='{$thisURL}'><h2>".$thistitle."</h2></a>";
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
echo "&nbsp;<span class='wptag t".$p->term_id."'><a href='/news-by-category/?cat=".$p->slug."'>".$thistype."</a></span>";
}
the_excerpt();
echo "<p class='news_date'><a class='more' href='{$thisURL}' title='{$thistitle}' >Read more</a></p>";
echo "</div><div class='clearfix'></div><hr class='light' />";
?>

<?php

/*
Plugin Name: HT List Tags
Plugin URI: http://www.helpfultechnology.com
Description: Create simple listings of tagged posts and pages
Author: Luke Oatham
Version: 0.1
Author URI: http://www.helpfultechnology.com
*/

function ht_listtags_shortcode($atts,$content){
    //get any attributes that may have been passed; override defaults
    $opts=shortcode_atts( array(
        'tag' => '',
        ), $atts );
	
	global $wp_query;

	$tag = $opts['tag'];
	$q = 'tag='.$tag;
	$query = get_posts( $q );
		
	foreach ($query as $list){		
		$thistitle = get_the_title($list->ID);
		$thisURL=get_permalink($list->ID);
		$thisexcerpt= $list->post_excerpt;
		$thisdate= $list->post_date;
		$thisdate=date("j M Y",strtotime($thisdate));
	
		$output.="<h3><a href=\"".$thisURL."\" title=\"".$thistitle." ".$title_context."\">".$thistitle."</a></h3><div class='media'>";
		$image_url = get_the_post_thumbnail($list->ID, 'thumbnail', array('class' => 'alignright'));
		$output.="<a href='".$thisURL."'><div class='hidden-xs'>".$image_url."</div></a><div class='media-body'><div><p><span class='listglyph'><i class='glyphicon glyphicon-calendar'></i> ".$thisdate."</span> </p></div>".$thisexcerpt."</div></div><hr>";
	
	}

	wp_reset_query();
    return $output;
}

add_shortcode("listtags", "ht_listtags_shortcode");

?>
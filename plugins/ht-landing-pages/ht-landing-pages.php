<?php

/*
Plugin Name: HT Landing Pages
Plugin URI: http://www.helpfultechnology.com
Description: Create simple listings of child pages to create a landing page effect
Author: Steph Gray
Version: 0.1
Author URI: http://www.helpfultechnology.com
*/

function hp_landingpages_shortcode($atts,$content){
    //get any attributes that may have been passed; override defaults
    $opts=shortcode_atts( array(
        'id' => '',
        'exclude' => ''
        ), $atts );

	// get child pages
	
	global $wp_query;
	$id = ($opts['id'] == "") ? $wp_query->post->ID : $opts['id'];
	
	$children = get_pages("child_of=".$id."&parent=".$id."&hierarchical=0&exclude=".$opts['exclude']."&posts_per_page=-1&post_type=page&sort_column=menu_order&sort_order=ASC");

	foreach((array)$children as $c) {
		
		if ($c->post_excerpt) {
			$excerpt = $c->post_excerpt;
		} else {
			if (strlen($c->post_content)>200) {
				$excerpt = substr(strip_tags($c->post_content),0,200) . "&hellip;";
			} elseif ($c->post_content == "" || $c->post_content == 0) {
				$excerpt = "";
			} else {			
				$excerpt = strip_tags($c->post_content);
			}
		}
		
		$output .= "
		<div class='htlandingpage clearfix'>
		  ".get_the_post_thumbnail($c->ID,"listingthumb","class=listingthumb")."
		  <h2><a href='".get_permalink($c->ID)."'>".get_the_title($c->ID)."</a></h2>
		  <p>".$excerpt."</p>
		</div>
		";
	}

	$html = "<div class='htlandingpageblock'>" . $output . "</div>";

    return $html;
}

add_shortcode("landingpage", "hp_landingpages_shortcode");

?>
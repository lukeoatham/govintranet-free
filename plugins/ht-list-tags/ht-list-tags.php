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

	 wp_register_script( 'masonry.pkgd.min', get_stylesheet_directory_uri() . "/js/masonry.pkgd.min.js");
	 wp_enqueue_script( 'masonry.pkgd.min',95 );

    //get any attributes that may have been passed; override defaults
    $opts=shortcode_atts( array(
        'tag' => '',
        'format' => '',
        
        ), $atts );
	
	global $wp_query;

	$tag = $opts['tag'];
	$format = $opts['format'];
	$q = 'posts_per_page=-1&tag='.$tag;
	$query = get_posts( $q );
	
	$output='';
	foreach ($query as $list){		
		$thisexcerpt='';
		$thistitle = get_the_title($list->ID);
		$thisURL=get_permalink($list->ID);
		$thisexcerpt= $list->post_excerpt;
		$thisdate= $list->post_date;
		$thisdate=date("j M Y",strtotime($thisdate));
		$image_url = get_the_post_thumbnail($list->ID, 'medium', array("class"=>"img img-responsive","width"=>175,"height"=>175));	
		
		$output.="
		<div class='grid-item well well-sm'>
			<div class='itemimage'><a href=\"".$thisURL."\" title=\"".$thistitle." ".$title_context."\">".$image_url."</a></div>
				<p><a href=\"".$thisURL."\" title=\"".$thistitle." ".$title_context."\">".$thistitle."</a></p>";
				if ($format=="full"){
					$output.="<p><span class='listglyph'><i class='glyphicon glyphicon-calendar'></i> ".$thisdate."</span> </p>".wpautop($thisexcerpt);
				}
		$output.="</div>";
	
	}
		$output=
		
		'<div id="container" class="js-masonry"
  data-masonry-options=\'{ "columnWidth": ".grid-sizer", "itemSelector": ".grid-item", "gutter": 10 }\'><div class="grid-sizer"></div>'.$output."</div>";
	wp_reset_query();
    return $output;
}

add_shortcode("listtags", "ht_listtags_shortcode");

?>
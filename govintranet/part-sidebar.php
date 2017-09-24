<?php
global $post;
wp_reset_postdata();
$html =  get_post_meta ( $post->ID, 'ht_sidebar_content', true ) ;
if ( $html ){
	echo "<div class='widget-box'>";
	echo apply_filters('the_content',$html);
	echo "</div>";
}

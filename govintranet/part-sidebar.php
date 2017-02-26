<?php
global $post;
wp_reset_postdata();
$html = apply_filters( "the_content", get_post_meta ( $post->ID, 'ht_sidebar_content', true ) );

if ( get_post_meta ( $post->ID, 'ht_sidebar_content', true ) ){
	echo "<div class='widget-box'>";
	echo $html;
	echo "</div>";
}

?>	
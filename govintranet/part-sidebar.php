<?php
global $post;

$html = apply_filters( "the_content", get_post_meta ( $post->ID, 'ht_sidebar_content', true ) );

if ( $html ){
	echo "<div class='widget-box'>";
	echo $html;
	echo "</div>";
}

	
?>	
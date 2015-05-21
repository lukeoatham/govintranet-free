<?php
global $post;
global $title;
global $link;
echo "<div class='widget-box'>";
if ( $title ) echo "<h3>".esc_attr($title)."</h3>";
$linkarray = array();
foreach ($link as $l){
	$linkarray[] = "<li><a href='".get_permalink($l)."'>".get_the_title($l)."</a></li>";
}
if ( count($linkarray) > 0 ):
	echo "<ul>".implode("", $linkarray)."</ul>";
endif;
echo "</div>";
wp_reset_postdata();
?>			
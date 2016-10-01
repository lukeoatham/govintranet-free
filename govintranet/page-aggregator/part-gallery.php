<?php
	global $post;
 	global $gallery;
 	global $title;
	echo "<div class='widget-box'><div class='aggregator gallery'>";
	if ( $title ) echo "<h3>".esc_attr($title)."</h3>";
	$gtemp = array();
	foreach ( $gallery as $g ){ 
		$html='';
		$img = wp_get_attachment_image_src($g['ID'],'full');
		$html.="<dl class='gallery-item'>";
		$html.="<dt class='gallery-icon'>";
		$html.="<a rel='prettyPhoto[gallery-".$g->ID."]' href='".$img[0]."'>";
		$istyle='';
		$img = wp_get_attachment_image_src($g['ID'],'thumbnail');
		$html.="<img src='".$img[0]."' class='gallery-item' width='".$img[1]."' height='".$img[2]."' alt='".esc_attr(get_post_thumbnail_caption($g['ID']))."' ".$istyle."/>";
		$html.="</a>";
		$html.="</dt>";
		$html.="</dl>";
		$grids[]=$html;					
	}
	echo implode("", $grids);
	echo "</div>";
	echo "</div>";
	echo "<div class='clearfix'></div>";
	wp_reset_postdata();
?>			
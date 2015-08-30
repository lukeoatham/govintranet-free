<?php
global $post;
$id = $post->ID;
$alreadydone = array();
$related = get_post_meta($id,'related',true);

$html='';
if ($related){
	foreach ($related as $r){ 
		$title_context="";
		$rlink = get_post($r);
		if ($rlink->post_status == 'publish' && $rlink->ID != $id ) {
			$taskparent=$rlink->post_parent; 
			if ($taskparent){
				$taskparent = get_post($taskparent);
				$title_context=" (".govintranetpress_custom_title($taskparent->post_title).")";
			}		
			$html.= "<li><a href='".get_permalink($rlink->ID)."'>".govintranetpress_custom_title($rlink->post_title).$title_context."</a></li>";
			$alreadydone[] = $r;
		}
	}
}

if ( !get_option("options_hide_reciprocal_related_links") ):
	//get anything related to this post
	$otherrelated = get_posts(array('post_type'=>array('task','news','project','vacancy','blog','team','event'),'posts_per_page'=>-1,'exclude'=>$related,'meta_query'=>array(array('key'=>'related','compare'=>'LIKE','value'=>'"'.$id.'"')))); 
	foreach ($otherrelated as $o){
		if ($o->post_status == 'publish' && $o->ID != $id ) {
			$taskparent=$o->post_parent; 
			$title_context='';
			if ($taskparent){
				$taskparent = get_post($taskparent);
				$title_context=" (".govintranetpress_custom_title($taskparent->post_title).")";
			}		
			$html.= "<li><a href='".get_permalink($o->ID)."'>".govintranetpress_custom_title($o->post_title).$title_context."</a></li>";
			$alreadydone[] = $o->ID;
		}
	}
endif;

if ( $html ){
	echo "<div class='widget-box list'>";
	echo "<h3 class='widget-title'>Related</h3>";
	echo "<ul>";
	echo $html;
	echo "</ul></div>";
}

	
?>	
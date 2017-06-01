<?php
global $post;
if ( function_exists('get_field')) $current_attachments = get_field('document_attachments');
if ( is_array($current_attachments) ) {
	$html = '';
	foreach ($current_attachments as $ca){
		$c = $ca['document_attachment'];
		if ( $c['title'] ) $html.= "<li><a class='alert-link' href='".$c['url']."'>".esc_html($c['title'])."</a></li>";
	}
	if ( $html ){
		echo "<div class='document-download'>";
		echo "<h3>" . _x('Documents' , 'Documents to download' , 'govintranet') . " <span class='pull-right small'><span class='glyphicon glyphicon-save'></span></span></h3>";
		echo "<ul class='document-download-list'>";
		echo $html;
		echo "</ul></div>";
	}
}	
?>	
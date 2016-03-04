<?php
global $post;
$current_attachments = get_field('document_attachments');
if ($current_attachments){
	echo "<div class='alert alert-info'>";
	echo "<h3>" . _x('Downloads' , 'Documents to download' , 'govintranet') . " <span class='dashicons dashicons-download'></span></h3>";
	foreach ($current_attachments as $ca){
		$c = $ca['document_attachment'];
		if ( isset($c['title']) ) echo "<p><a class='alert-link' href='".$c['url']."'>".$c['title']."</a></p>";
	}
	echo "</div>";
}	
?>	
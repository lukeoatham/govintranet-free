	<?php
	global $post;
	global $freeformat;
	echo "<div class='widget-box'>";
	echo apply_filters('the_content', $freeformat);
	echo "</div>";
	wp_reset_postdata();
	?>			
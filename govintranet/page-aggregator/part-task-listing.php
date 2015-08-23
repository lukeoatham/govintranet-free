	<?php
	global $post;
	global $title;
	global $team;
	global $checkteam;
	global $tax;
	global $tag;
	global $compact;
	
	$query = array(
			"post_type" => "task",
			"posts_per_page" => -1,
			"order_by" => "menu_order, title",
			"order" => "ASC",
		);

	$taxarray='';
	$taxarray3='';

	if ($team):
		$teamarray = array(
		"key" => "related_team",
		"value" => '"'.$checkteam.'"',
		"compare" => "LIKE",
		);
		$query['meta_query']=array($teamarray);
	endif;

			
	if ($tax) {
		$t = array();
		foreach ($tax as $tt){
			$t[] = $tt;
		}
		$t = implode(", ", $t);
		$taxarray = array();
		$taxarray['taxonomy']="category";
		$taxarray['field']="id";
		$taxarray['terms']=$tax;
	}
	  
	if ($tag) {
		$taxarray3 = array();
		$taxarray3['taxonomy']="post_tag";
		$taxarray3['field']="id";
		$taxarray3['terms']=$tag;
	}

	if ( $tax ) $query['tax_query']=array($taxarray);
	if ( $tag ) $query['tax_query']=array($taxarray3);
	if ( $tax && $tag ) $query['tax_query']=array("relation"=>"AND",$taxarray,$taxarray3);
	echo "<div class='widget-box'>";
	if ( $title ) echo "<h3>".esc_attr($title)."</h3>";

	add_filter('pre_get_posts', 'filter_tasks');

	$listquery = new wp_query($query); 
	if ($listquery->have_posts()) while ($listquery->have_posts()):
		$listquery->the_post();
		if ( !$compact ):
			$excerpt = get_the_excerpt();
			$taskparent=get_post($post->post_parent);
			$title_context='';
			if ($taskparent){
				$parent_guide_id = $taskparent->ID; 		
				$title_context=govintranetpress_custom_title($taskparent->post_title); 
			}
			echo "<h3 class='postlist'><a href='".get_permalink( $post->ID )."' title='".get_the_title($post->ID).$title_context;
			echo "' rel='bookmark'>".get_the_title($post->ID)."</a></h3>";
			echo wpautop( $excerpt ); 
		else:
			$larray[] = "<li><a href='".get_permalink($post->ID)."'>".get_the_title($post->ID)."</a> <span class='small'>".$title_context."</span></li>";
		endif;
	endwhile;
	if ( $compact ) echo "<ul>".implode( '', $larray )."</ul>";
	echo "</div>";
	remove_filter('pre_get_posts', 'filter_tasks');
	wp_reset_postdata();

	?>			
	<?php
	global $post;
	global $title;
	global $team;
	global $checkteam;
	global $tax;
	global $tag;
	global $n2n;
	global $freshness;
	global $num;
	global $compact;
	
	$query = array(
			"post_type" => "news",
			"posts_per_page" => $num,
		);

	if ($freshness):
		$freshness = "-".$freshness." day";
	    $tdate = date ( 'F jS, Y', strtotime ( $freshness . $tdate ) );   
	 	$query['date_query'] = array(
					array(
						'after'     => $tdate,
						'inclusive' => true,
					)
				);
	endif;
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
		$taxarray['taxonomy']="news-type";
		$taxarray['field']="id";
		$taxarray['terms']=$tax;
		$taxarray['compare']="IN";
	}
	  
	if ($tag) {
		$taxarray3 = array();
		$taxarray3['taxonomy']="post_tag";
		$taxarray3['field']="id";
		$taxarray3['terms']=$tag;
		$taxarray3['compare']="IN";
	}
	
	if ( $n2n == "Only need to know" ) {
		$taxarray4 = array();
		$taxarray4['taxonomy']="post_format";
		$taxarray4['field']="slug";
		$taxarray4['terms']='post-format-status';
	} elseif ( $n2n == "Exclude need to know" ) {
		$taxarray4 = array();
		$taxarray4['taxonomy']="post_format";
		$taxarray4['field']="slug";
		$taxarray4['terms']='post-format-status';
		$taxarray4['operator']='NOT IN';
	} else {
		$n2n = 0;
	}
	
	//setup tax queries - need different code for single and multiple tax queries
	$fcount = 0;
	if ( $tax ) $fcount++;
	if ( $tag ) $fcount++;
	if ( $n2n ) $fcount++;
	if ( $tax ) $query['tax_query']=array($taxarray);
	if ( $tag ) $query['tax_query']=array($taxarray3);
	if ( $n2n ) $query['tax_query']=array($taxarray4);
	if ( $fcount > 1 ) $query['tax_query']=array("relation"=>"AND",$taxarray,$taxarray3,$taxarray4);
	
	
	add_filter('pre_get_posts', 'filter_news');
	
	$listquery = new wp_query($query);
	if ($listquery->have_posts()):
	echo "<div class='widget-box'>";
	if ( $title ) echo "<h3>".esc_attr($title)."</h3>";
	 while ($listquery->have_posts()):
		$listquery->the_post();
		if ( !$compact ):
			echo "<div class='row'>";
			if ( has_post_thumbnail($post->ID)):
				echo "<div class='col-sm-9'>";
			else:
				echo "<div class='col-sm-12'>";
			endif;
			echo "<h3 class='postlist'>";
			echo "<a href='".get_permalink($post->ID)."'>";
			the_title();
			echo "</a>";
			echo "</h3>";
			the_excerpt();
			echo "</div>";
			if ( has_post_thumbnail($post->ID)):
				echo "<div class='col-sm-3 xs-hidden'>";
				the_post_thumbnail(array(150,150), array('class'=>'img-responsive'));
				echo "</div>";
			endif;
			echo "</div>";
			echo "<hr>";
		else:
			$larray[] = "<li><a href='".get_permalink($post->ID)."'>".get_the_title($post->ID)."</a> <span class='small'>".get_the_date()."</span></li>";
		endif;
	endwhile;
	if ( $compact ) echo "<ul>".implode( '', $larray )."</ul>";
	echo "</div>";
	endif;
	remove_filter('pre_get_posts', 'filter_news');
	wp_reset_postdata();
	?>			
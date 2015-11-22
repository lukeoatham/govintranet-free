<?php
global $post;
global $title;
global $team;
global $checkteam;
global $freshness;
global $num;
global $compact;

$query = array(
		"post_type" => "blog",
		"posts_per_page" => $num,
	);

if ( $freshness ):
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

if ($team):
	$teamarray = array(
	"key" => "related_team",
	"value" => '"'.$checkteam.'"',
	"compare" => "LIKE",
	);
	$query['meta_query']=array($teamarray);
endif;
		

$fcount = 0;

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
			the_date();
			the_excerpt();
			echo "</div>";
			if ( has_post_thumbnail($post->ID)):
				echo "<div class='col-sm-3 xs-hidden'>";
				the_post_thumbnail("thumbnail", array('class'=>'img-responsive'));
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
wp_reset_postdata();
?>			
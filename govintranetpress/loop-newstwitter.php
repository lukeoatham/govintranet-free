<?php

//this replaces the regular loop for feeding the news page 

global $k;
$k++;
$thistitle = get_the_title($ID);
$thisURL=get_permalink($ID);
$vidpod = new Pod ( 'news' , $post->ID );
$videoimg = get_the_post_thumbnail($post->ID, 'thumbnail', array('class' => 'alignright'));
$thisexcerpt= get_the_excerpt();
$thisdate= $post->post_date;
$thisdate=date("j M Y",strtotime($thisdate));
$needtoknow = '';


//determine news type
if (get_post_meta($post->ID,'news_listing_type',true) == 1 ) {
	$needtoknow = "<i class='glyphicon glyphicon-bell'></i> "; 
}




if ($k==1 && $paged<2){ 

//headline images

	echo "<div class='row'>";
	echo "<div class='col-lg-12'>";
	$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'newshead' ); 
	if ($image_uri!="" ){
		echo "<a href='".site_url()."/news/".$post->post_name."/'><img class='img img-responsive' src='{$image_uri[0]}' width='{$image_uri[1]}' height='{$image_uri[2]}' alt='".$thistitle."' /></a>";																			} 
	echo "<h3 class='postlist'><a href='".site_url()."/news/".$post->post_name."'>".$needtoknow.$thistitle."</a></h3>";
	echo "<div class='media-body'>";
	
		echo "<div><p>";
	$post_cat = get_the_category();		
				foreach($post_cat as $cat){
			if ($cat->name != 'Uncategorized' ){
				echo "<span class='listglyph'><span class='glyphicon glyphicon-stop gb".$cat->term_id."'></span>&nbsp;".$cat->name;
			echo "</span>&nbsp;&nbsp;";
			}
		}

			   $thisdate= $post->post_modified;
			   $thisdate=date("j M Y",strtotime($thisdate));
			   echo "<span class='listglyph'><i class='glyphicon glyphicon-calendar'></i> ".$thisdate."</span> ";
		echo "</p></div>";

		the_excerpt(); 
?>
	</div></div></div>
	<?php
} else 
{ 

//regular listing *********************
?>

<h3 class='postlist'><?php echo $needtoknow; ?>				
	<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( '%s %s', 'govintranetpress' ), the_title_attribute( 'echo=0' ), " (" . $context . ")" ); ?>" rel="bookmark"><?php the_title(); echo $title_context; ?></a></h3>
<?php
	echo "<div class='media'>" ;
	$image_url = get_the_post_thumbnail($ID, 'thumbnail', array('class' => 'alignright'));

	echo "<a href='";
	$userurl = get_permalink();
	echo $userurl;
	echo "'><div class='hidden-xs'>".$image_url."</div></a>" ;

	echo "<div class='media-body'>";
	
		echo "<div><p>";
			$post_cat = get_the_category();		
				foreach($post_cat as $cat){
					if ($cat->name != 'Uncategorized' ){
						echo "<span class='listglyph'><span class='glyphicon glyphicon-stop gb".$cat->term_id."'></span>&nbsp;".$cat->name;
						echo "</span>&nbsp;&nbsp;";
					}
				}

			   $thisdate= $post->post_modified;
			   $thisdate=date("j M Y",strtotime($thisdate));
			   echo "<span class='listglyph'><i class='glyphicon glyphicon-calendar'></i> ".$thisdate."</span> ";

		echo "</p></div>";

		the_excerpt(); 
?>
	</div></div>
<?php
}

echo "<hr>";

?>



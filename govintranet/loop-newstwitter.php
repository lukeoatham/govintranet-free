<?php

//this replaces the regular loop for feeding the news page 

global $k;
$k++;
$thistitle = get_the_title($id);
$thisURL=get_permalink($id);
//$vidpod = new Pod ( 'news' , $post->ID );
$videoimg = get_the_post_thumbnail($post->ID, 'thumbnail', array('class' => 'alignright'));
$thisexcerpt= get_the_excerpt();
$thisdate= $post->post_date;
$thisdate=date("j M Y",strtotime($thisdate));
$needtoknow = '';


//determine news type
$icon = get_option('options_need_to_know_icon');
if ($icon=='') $icon = "flag";
			   
if (has_post_format('status', $post->ID)) {
	$needtoknow = "<i class='glyphicon glyphicon-".$icon."'></i> "; 
}
$video = 0;
if ( has_post_format('video', $post->ID) ):
	$video = apply_filters('the_content', get_post_meta( $post->ID, 'news_video_url', true));
endif;

$headclass = "class='postlist'";

if ($k==1 && $paged<2){ 

//headline images
	$headclass = "";
	echo "<div class='row'>";
	echo "<div class='col-lg-12'>";
	$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'newshead' ); 
	if ($video){
		echo $video;
	} elseif ($image_uri!="" ){
		echo "<a href='".site_url()."/news/".$post->post_name."/'><img class='img img-responsive' src='{$image_uri[0]}' width='{$image_uri[1]}' height='{$image_uri[2]}' alt='".$thistitle."' /></a>";																			} 
	$ext_icon = '';
	if ( get_post_format($post->ID) == 'link' ) $ext_icon = "<i class='dashicons dashicons-migrate'></i> ";
	echo "<h3".$headclass.">".$ext_icon."<a href='".site_url()."/news/".$post->post_name."'>".$needtoknow.$thistitle."</a></h3>";
	echo "<div class='media-body'>";
	
	echo "<div><p>";
   $thisdate= $post->post_date;
   $thisdate=date("j M Y",strtotime($thisdate));
   echo "<span class='listglyph'></i> ".$thisdate."</span> ";

	$post_cat = get_the_terms($post->ID,'news-type');		
	if ( $post_cat ) foreach($post_cat as $cat){
		if ($cat->name != 'Uncategorized' ){
			echo "<span class='listglyph'><span class='dashicons dashicons-category gb".$cat->term_id."'></span>&nbsp;".$cat->name;
			echo "</span>&nbsp;&nbsp;";
		}
	}

	echo "</p></div>";

	the_excerpt(); 
	?>
	</div></div></div>
	<?php
} else 
{ 
	$ext_icon = '';
	if ( get_post_format($post->ID) == 'link' ) $ext_icon = "<span class='dashicons dashicons-migrate'></span> ";

//regular listing *********************
	echo "<div class='media'>" ;
	$image_url = get_the_post_thumbnail($id, 'thumbnail', array('class' => 'alignright'));
	
	echo "<a href='";
	$userurl = get_permalink();
	echo $userurl;
	echo "'><div class='hidden-xs'>".$image_url."</div></a>" ;

	echo "<div class='media-body'>";
?>

<h3 class='postlist'><?php echo $needtoknow.$ext_icon;  ?>				
	<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( '%s %s', 'govintranetpress' ), the_title_attribute( 'echo=0' ), " (" . $context . ")" ); ?>" rel="bookmark"><?php the_title(); ?></a><?php echo $ext_icon; ?></h3>
<?php
	
		echo "<div><p>";

			   $thisdate= $post->post_date;
			   $thisdate=date("j M Y",strtotime($thisdate));
			   echo "<span class='listglyph'>".$thisdate."</span> ";

		echo "</p></div>";

		the_excerpt(); 
?>
	</div></div>
<?php
}

echo "<hr>";

?>
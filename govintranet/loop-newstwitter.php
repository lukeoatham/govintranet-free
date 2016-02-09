<?php

//this replaces the regular loop for feeding the news page 

global $k;
$k++;
$thistitle = get_the_title($id);
$thisURL=get_permalink($id);
$videoimg = get_the_post_thumbnail($post->ID, 'thumbnail', array('class' => 'alignright'));
$thisexcerpt= get_the_excerpt();
$thisdate= $post->post_date;
$thisdate=date(get_option('date_format'),strtotime($thisdate));
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
	$img_srcset = wp_get_attachment_image_srcset( get_post_thumbnail_id( $post->ID ), 'large' );
	$img_sizes = wp_get_attachment_image_sizes(get_post_thumbnail_id( $post->ID ), 'newshead' ); 
	if ($video){
		echo $video;
	} elseif ($image_uri!="" ){
		echo "<a href='".get_permalink($post->ID)."/'>";
		echo get_the_post_thumbnail($post->ID, 'newshead', array('class'=>'img-responsive','srcset'=>$img_srcset, 'sizes'=>$img_sizes));
		echo "</a>";																		
	} 
	$ext_icon = '';
	if ( get_post_format($post->ID) == 'link' ) $ext_icon = "<i class='dashicons dashicons-migrate'></i> ";
	echo "<h3".$headclass.">".$ext_icon."<a href='".get_permalink($post->ID)."'>".$needtoknow.$thistitle."</a></h3>";
	echo "<div class='media-body'>";
	echo "<div><p>";
	$thisdate= $post->post_date;
	$thisdate=date(get_option('date_format'),strtotime($thisdate));
	echo '<span class="listglyph">'.get_the_date(); 
	echo '</span> ';
	if ( get_comments_number() ){
		echo " <a href='".$thisURL."#comments'>";
		printf( _n( '<span class="badge">1 comment</span>', '<span class="badge">%d comments</span>', get_comments_number(), 'govintranet' ), get_comments_number() );
		echo "</a>";
	}

	$post_cat = get_the_terms($post->ID,'news-type');		
	if ( $post_cat ) foreach($post_cat as $cat){
		if ($cat->term_id > 1 ){
			echo "<span class='listglyph'><i class='dashicons dashicons-category gb".$cat->term_id."'></i>&nbsp;".$cat->name;
			echo "</span>&nbsp;&nbsp;";
		}
	}
	echo "</p></div>";
	the_excerpt(); 
	?>
	</div></div></div>
	<?php
} else { 
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
	<h3 class='postlist'><?php echo $needtoknow;  ?>				
	<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( '%s %s', 'govintranet' ), the_title_attribute( 'echo=0' ), " (" . $context . ")" ); ?>" rel="bookmark"><?php the_title(); ?></a><?php echo $ext_icon; ?></h3>
	<?php
	echo "<div><p>";
	$thisdate= $post->post_date;
	$thisdate=date(get_option('date_format'),strtotime($thisdate));
	echo '<span class="listglyph">'.get_the_date(); 
	echo '</span> ';
	if ( get_comments_number() ){
		echo " <a href='".$thisURL."#comments'>";
		printf( _n( '<span class="badge">1 comment</span>', '<span class="badge">%d comments</span>', get_comments_number(), 'govintranet' ), get_comments_number() );
		echo "</a>";
	}
	echo "</p></div>";
	the_excerpt(); 
	?>
	</div></div>
	<?php
}

echo "<hr>";

?>
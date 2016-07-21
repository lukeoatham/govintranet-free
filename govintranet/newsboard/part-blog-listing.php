<?php
global $k;
$k++;
$showthumb = true;
if ($k==1 && has_post_thumbnail($post->ID)){ 
	//headline images
	$showthumb = false;
	$headclass = "";
	if ($video){
		echo $video;
	} elseif (has_post_thumbnail($post->ID)){
		echo "<a href='".get_permalink($post->ID)."' title='$thistitle'>";
		echo get_the_post_thumbnail($post->ID, 'newshead', array('class'=>'img-responsive'));
		echo "</a>";
		echo wpautop( "<p class='news_date'>".get_post_thumbnail_caption()."</p>" );
	} 
}
			
$user = get_userdata($post->post_author);
$gis = "options_forum_support";
$staffdirectory = get_option('options_module_staff_directory');
$user_info = get_userdata($post->post_author);
$displayname = get_user_meta($post->post_author ,'first_name',true )." ".get_user_meta($post->post_author ,'last_name',true );		
$directorystyle = get_option('options_staff_directory_style'); // 0 = squares, 1 = circles
$avstyle="alignright";
$image_url = "";
if ( $directorystyle==1 ) $avstyle.= " img-circle";
echo "<div class='media'>" ;
if ( $showthumb ):
	$image_url = get_the_post_thumbnail($id, 'thumbnail', array('class' => 'alignright'));
	if ( !$image_url ):
		$image_url = get_avatar($post->post_author );
		$image_url = str_replace(" photo", " photo ".$avstyle, $image_url);
	endif;	
endif;
$userurl = get_permalink();
echo "<a href='" . $userurl . "'><div class='hidden-xs'>".$image_url."</div></a>" . "<div class='media-body'>";
?>
<h3 class='postlist'>		
<a href="<?php the_permalink(); ?>" title="<?php  the_title_attribute( 'echo=1' ); ?>" rel="bookmark"><?php the_title(); ?></a></h3>
<?php
echo "<p>";
$post_cat = get_the_category();		
if ( $post_cat ) foreach($post_cat as $cat){
		if ($cat->term_id > 1 ){
			echo "<span class='listglyph'><span class='dashicons dashicons-category gb".$cat->term_id."'></span>&nbsp;".$cat->name;
			echo "</span>&nbsp;&nbsp;";
		}
	}
$thisdate= $post->post_date;
$thisdate=date(get_option('date_format'),strtotime($thisdate));
echo "<span class='listglyph'>".get_the_date()."</span>&nbsp;";
$gis = "options_forum_support";
$forumsupport = get_option($gis);
if ($forumsupport){	
   	$authorlink = "<a href='" . get_author_posts_url( $post->post_author, $user->user_nicename ) . "'>";
	if (function_exists('bp_activity_screen_index')){ // if using BuddyPress - link to the members page
		$authorlink = "<a href='".site_url()."/members/" . $user->user_nicename . "/'>";
	} 
	if (function_exists('bbp_user_profile_url') && $staffdirectory ){ // if using bbPress - link to the staff page
		echo "<a href='";
		bbp_user_profile_url( $post->post_author );
		echo "'>";
	} else {
		echo $authorlink;	
	}
	$user_info = get_userdata($post->post_author);
	$displayname = get_user_meta($post->post_author ,'first_name',true )." ".get_user_meta($post->post_author ,'last_name',true );		
	$directorystyle = get_option('options_staff_directory_style'); // 0 = squares, 1 = circles
	$avstyle="";
	if ( $directorystyle==1 ) $avstyle = " img-circle";
	$image_url = get_avatar($post->post_author , 32);
	$image_url = str_replace(" photo", " photo ".$avstyle, $image_url);
	echo $image_url;
	echo "</a>&nbsp;";
	if (function_exists('bbp_user_profile_url') && $staffdirectory ){ // if using bbPress - link to the staff page
		echo "<a href='";
		bbp_user_profile_url( $post->post_author );
		echo "'>";
	} else {
		echo $authorlink;	
	}
	$auth = get_the_author();
	echo "<span class='listglyph'>".$auth."</span>";
	echo "</a> ";
} else {
    echo " <a href='".site_url()."/author/" . $user->user_nicename . "/'>" . $user->display_name . "</a>";			   
}
if ( get_comments_number() ){
	echo " <a href='".get_permalink($post->ID)."#comments'>";
	echo '<span class="badge badge-comment">' . sprintf( _n( '1 comment', '%d comments', get_comments_number(), 'govintranet' ), get_comments_number() ) . '</span>';
	echo "</a>";
}
echo "</p>";

the_excerpt(); 

?>
	</div>
</div>

<hr>
<?php wp_reset_postdata();?>
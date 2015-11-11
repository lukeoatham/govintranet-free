<?php

//this replaces the regular loop for feeding the news page 

global $k;
$k++;
$thistitle = get_the_title($id);
$thisURL=get_permalink($id);
$thisexcerpt= get_the_excerpt();
$thisdate= $post->post_date;
$thisdate=date("j M Y",strtotime($thisdate));

$ext_icon = '';
if ( get_post_format($post->ID) == 'link' ) $ext_icon = "<span class='dashicons dashicons-migrate'></span> ";

?>

<h3 class='postlist'>		
	<a href="<?php the_permalink(); ?>" title="<?php  the_title_attribute( 'echo=1' ); ?>" rel="bookmark"><?php the_title(); echo $title_context; ?></a><?php echo $ext_icon; ?></h3>
<?php
	echo "<div class='media'>" ;
	$image_url = get_the_post_thumbnail($id, 'thumbnail', array('class' => 'alignright'));

	echo "<a href='";
	$userurl = get_permalink();
	echo $userurl;
	echo "'><div class='hidden-xs'>".$image_url."</div></a>" ;

	echo "<div class='media-body'>";
	
		echo "<p>";
			$post_cat = get_the_category();		
				if ( $post_cat ) foreach($post_cat as $cat){
					if ($cat->term_id > 1 ){
						echo "<span class='listglyph'><span class='dashicons dashicons-category gb".$cat->term_id."'></span>&nbsp;".$cat->name;
						echo "</span>&nbsp;&nbsp;";
					}
				}

			   $thisdate= $post->post_date;
			   $thisdate=date("j M Y",strtotime($thisdate));
			   echo "<span class='listglyph'>".get_the_date('j M Y')."</span>&nbsp;";
			   
               $user = get_userdata($post->post_author);
               $gis = "options_forum_support";
			   $forumsupport = get_option($gis);

			   if ($forumsupport){	
				   		$authorlink = "<a href='".site_url()."/author/" . $user->user_nicename . "/'>";
						if (function_exists('bp_activity_screen_index')){ // if using BuddyPress - link to the members page
							$authorlink = "<a href='".site_url()."/members/" . $user->user_nicename . "/'>";
							} 
						elseif (function_exists('bbp_get_displayed_user_field')){ // if using bbPress - link to the staff page
							$authorlink = "<a href='".site_url()."/staff/" . $user->user_nicename . "/'>";
							}
						echo $authorlink;
						$user_info = get_userdata($post->post_author);
						$userurl = site_url().'/staff/'.$user_info->user_nicename;
						$displayname = get_user_meta($post->post_author ,'first_name',true )." ".get_user_meta($post->post_author ,'last_name',true );		
						$directorystyle = get_option('options_staff_directory_style'); // 0 = squares, 1 = circles
						$avstyle="";
						if ( $directorystyle==1 ) $avstyle = " img-circle";
						$image_url = get_avatar($post->post_author , 32);
						$image_url = str_replace(" photo", " photo ".$avstyle, $image_url);
						echo $image_url;
						echo "</a>&nbsp;";
						$auth = get_the_author();
						echo $authorlink . "<span class='listglyph'>".$auth."</a></span>&nbsp";
	           } else {
	                echo " <span class='listglyph'><a href='".site_url()."/author/" . $user->user_nicename . "/'>" . $user->display_name . "</a></span>&nbsp";			   
			   }
			   if ( get_comments_number() ){
						echo "<a href='".get_permalink($post->ID)."#comments'>";
						printf( _n( '<span class="badge">1 comment</span>', '<span class="badge">%d comments</span>', get_comments_number(), 'govintranet' ), get_comments_number() );
						echo "</a>";
					}

		echo "</p>";

		the_excerpt(); 
?>
	</div>
</div>

<hr>
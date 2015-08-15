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
	<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( '%s %s', 'govintranetpress' ), the_title_attribute( 'echo=0' ), " (" . $context . ")" ); ?>" rel="bookmark"><?php the_title(); echo $title_context; ?></a><?php echo $ext_icon; ?></h3>
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
						if (function_exists('bp_activity_screen_index')){ // if using BuddyPress - link to the members page
							echo "<a class='' href='".site_url()."/members/" . $user->user_nicename . "/'>";
							} 
						elseif (function_exists('bbp_get_displayed_user_field')){ // if using bbPress - link to the staff page
							echo "<a class='' href='".site_url()."/staff/" . $user->user_nicename . "/'>";
							}
						echo get_avatar($user->ID, 32);
						echo "</a>&nbsp;";
						if (function_exists('bp_activity_screen_index')){ // if using BuddyPress - link to the members page
							echo "<a class='' href='".site_url()."/members/" . $user->user_nicename . "/'>";
							} 
						elseif (function_exists('bbp_get_displayed_user_field')){ // if using bbPress - link to the staff page
							echo "<a class='' href='".site_url()."/staff/" . $user->user_nicename . "/'>";
							}
						$auth = get_the_author();
						echo "<span class='listglyph'>".$auth."</span>";
						echo "</a>";
	           } else {
	                echo " <a href='".site_url()."/author/" . $user->user_nicename . "/'>" . $user->display_name . "</a>";			   
			   }

		echo "</p>";

		the_excerpt(); 
?>
	</div>
</div>

<hr>
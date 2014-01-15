<?php

//this replaces the regular loop for feeding the news page 

global $k;
$k++;
$thistitle = get_the_title($ID);
$thisURL=get_permalink($ID);
$vidpod = new Pod ( 'blog' , $post->ID );
$thisexcerpt= get_the_excerpt();
$thisdate= $post->post_date;
$thisdate=date("j M Y",strtotime($thisdate));

?>

<h3 class='postlist'>				
	<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( '%s %s', 'govintranetpress' ), the_title_attribute( 'echo=0' ), " (" . $context . ")" ); ?>" rel="bookmark"><?php the_title(); echo $title_context; ?></a></h3>
<?php
	echo "<div class='media'>" ;
	$image_url = get_the_post_thumbnail($ID, 'thumbnail', array('class' => 'alignright'));

	echo "<a href='";
	$userurl = get_permalink();
	echo $userurl;
	echo "'><div class='hidden-xs'>".$image_url."</div></a>" ;

	echo "<div class='media-body'>";
	
		echo "<p>";
			$post_cat = get_the_category();		
				foreach($post_cat as $cat){
					if ($cat->name != 'Uncategorized' ){
						echo "<span class='listglyph'><span class='glyphicon glyphicon-stop gb".$cat->term_id."'></span>&nbsp;".$cat->name;
						echo "</span>&nbsp;&nbsp;";
					}
				}

			   $thisdate= $post->post_modified;
			   $thisdate=date("j M Y",strtotime($thisdate));
			   echo "<span class='listglyph'><i class='glyphicon glyphicon-calendar'></i> ".$thisdate."</span>&nbsp;";
			   
               $user = get_userdata($post->post_author);
               	$gis = "general_intranet_forum_support";
			   	$forumsupport = get_option($gis);

			   if ($forumsupport){			   
						if (function_exists('bp_activity_screen_index')){ // if using BuddyPress - link to the members page
							echo "<a class='' href='".site_url()."/members/" . $user->user_login . "/'>";
							} 
						elseif (function_exists('bbp_get_displayed_user_field')){ // if using bbPress - link to the staff page
							echo "<a class='' href='".site_url()."/staff/" . $user->user_login . "/'>";
							}
						echo get_avatar($user->ID, 16);
						echo "</a>";
	           } else {
	                echo " <a href='".site_url()."/author/" . $user->user_login . "/'>" . $user->display_name . "</a>";			   
			   }

		echo "</p>";

		the_excerpt(); 
?>
	</div>
	</div>

	<hr>

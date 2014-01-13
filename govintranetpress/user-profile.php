<?php

/**
 * User Profile
 *
 * @package bbPress
 * @subpackage Theme
 */


?>
</div></div></div> <!-- close bbPress divs so that we can hide the element -->

<div class="row">

	<div class="col-lg-2 col-md-2 col-sm-3">

	<?php
	$user_id = bbp_get_displayed_user_field( 'id' ); 
	$poduser = new Pod ('user' , $user_id);

 	if (function_exists('get_wp_user_avatar')){	
			$imgsrc = get_wp_user_avatar_src($user_id,145);				
			echo "<img class='img' src='".$imgsrc."' width='145'  />";
		}
	if (current_user_can('edit_themes')) echo "<br><p><a href='".site_url()."/wp-admin/user-edit.php?user_id=".$user_id."'>Edit profile</a></p>";
	elseif ( is_user_logged_in() && get_current_user_id() == $user_id ) echo "<br><p><a href='".site_url()."/wp-admin/profile.php'>Edit profile</a></p>";
?>
	</div>
		<div class="col-lg-5 col-md-5 col-sm-9">
	<?php// do_action( 'bbp_template_before_user_profile' ); ?>

				<h3 class="contacthead" >Contact</h3>
				

			<?php if ( bbp_get_displayed_user_field( 'user_telephone' ) ) : ?>

				<p class="bbp-user-description"><i class="glyphicon glyphicon-earphone"></i> <a href="tel:<?php str_replace(" ", "",  bbp_displayed_user_field( 'user_telephone' ) ); ?>"><?php bbp_displayed_user_field( 'user_telephone' ); ?></a></p>

			<?php endif; ?>

			<?php if ( bbp_get_displayed_user_field( 'user_mobile' ) ) : ?>

				<p class="bbp-user-description"><i class="glyphicon glyphicon-phone"></i> <a href="tel:<?php str_replace(" ", "", bbp_displayed_user_field( 'user_mobile' ) ); ?>"><?php bbp_displayed_user_field( 'user_mobile' ); ?></a></p>

			<?php endif; ?>

			<?php if ( bbp_get_displayed_user_field( 'user_email' ) ) : ?>

				<p class="bbp-user-description"><a href="mailto:<?php bbp_displayed_user_field( 'user_email' ); ?>">Email <?php bbp_displayed_user_field( 'first_name' ); echo " "; bbp_displayed_user_field( 'last_name' ); ?></a></p>

			<?php endif; ?>

			<?php if ( bbp_get_displayed_user_field( 'user_working_pattern' ) ) : ?>

				<h3 class="contacthead" >Working pattern</h3>
				<?php bbp_displayed_user_field( 'user_working_pattern' ); ?>

			<?php endif; ?>

<?
				
			  $terms = $poduser->get_field('user_team');
				if ($terms) {
				echo "<h3 class='contacthead'>Team</h3>";
			  		foreach ($terms as $taxonomy ) {
			  		    $themeid = $taxonomy['term_id'];
			  		    $themeURL= $taxonomy['slug'];
			   		    if ($themeURL == 'uncategorized') {
				  		    continue;
			  		    }
			  			echo "
							<p><a href='".site_url()."/team/{$themeURL}'>".$taxonomy['name']."</a></p>";
					}

				}  
?>


			<?php if ( bbp_get_displayed_user_field( 'description' ) ) : ?>

				<h3 class="contacthead" >About me</h3>
				<?php bbp_displayed_user_field( 'description' ); ?>
			<?php endif; 


			  $skills = $poduser->get_field('user_key_skills');
			  if ($skills){
				  echo "<h3 class='contacthead'>Key skills and experience</h3>";
				  echo $skills;
			  }
?>
<!--

			<p class="bbp-user-forum-role"><?php  printf( __( 'Forum Role: %s',      'bbpress' ), bbp_get_user_display_role()    ); ?></p>
			<p class="bbp-user-topic-count"><?php printf( __( 'Topics Started: %s',  'bbpress' ), bbp_get_user_topic_count_raw() ); ?></p>
			<p class="bbp-user-reply-count"><?php printf( __( 'Replies Created: %s', 'bbpress' ), bbp_get_user_reply_count_raw() ); ?></p>

-->
		</div>
		<div class="clearfix col-lg-5 col-md-5 col-sm-12">

<script>
jQuery('.tlink').tooltip();
</script>

		<?php 
			
				$poduserparent = get_user_meta( $user_id , 'user_line_manager', true); //print_r($poduserparent);
				echo "<div class='panel panel-default'>

				<div class='panel-heading oc'>Organisation tree</div>
				<div class='panel-body'>
				<div class='oc'>";
				if ($poduserparent){
					if (function_exists('get_wp_user_avatar')){
					echo "<a title='".$poduserparent['display_name']."' href='".site_url()."/staff/".$poduserparent['user_nicename']."/'>".get_wp_user_avatar($poduserparent['ID'],60,'centre')."</a>";						
					} else {
					echo "<a title='".$poduserparent['display_name']."' href='".site_url()."/staff/".$poduserparent['user_nicename']."/'></a>";						
					}
					echo "<p><a href='".site_url()."/staff/".$poduserparent['user_nicename']."/'>".$poduserparent['display_name']."</a><br>";
					echo get_user_meta($poduserparent['ID'],'user_job_title',true);
					echo "</p>";
					echo "<p><i class='glyphicon glyphicon-chevron-up'></i></p>";
				}

//				echo "<div class='alert alert-success'>";
//				if (function_exists('get_wp_user_avatar')){					
//					echo get_wp_user_avatar($user_id, 72);
//				}
				echo "<p><strong>";
				bbp_displayed_user_field( 'display_name' );
					echo "<br>".get_user_meta($user_id,'user_job_title',true);
				echo "</strong></p>";
//				echo "</div>";

				$q = "select meta_value as ID, user_id, display_name from wp_users join wp_usermeta on wp_users.ID = wp_usermeta.user_id where wp_usermeta.meta_key='user_line_manager' and wp_usermeta.meta_value = ".$user_id;
				
				global $wpdb;
				
				$poduserreports = $wpdb->get_results($q,ARRAY_A);
				
				if (count($poduserreports)>0){
				
					echo "<p><i class='glyphicon glyphicon-chevron-down'></i></p>";
						echo "<p class='directreports'>";

					foreach ($poduserreports as $p){ //print_r($p);
//					 if (get_user_meta($u->user_id, 'user_visible', true)==1){
					$pid = $p['user_id'];
                    $u = get_userdata($pid);

					if (function_exists('get_wp_user_avatar')){						
										
						echo "<a class='tlink' data-placement='right' data-original-title = '".$u->user_nicename."' title='".$u->display_name."' href='".site_url()."/staff/".$u->user_nicename."'>".get_wp_user_avatar($pid,50)."</a>";
						} else {
						echo "<a title='".$u->display_name."' href='".site_url()."/staff/".$u->user_nicename."'></a>";
							
						}

					}
						echo "</p>";
				}

				echo "</div></div>";
	
		?></div>

	</div><!-- #bbp-author-topics-started -->

	<?php do_action( 'bbp_template_after_user_profile' ); ?>
		</div>
		
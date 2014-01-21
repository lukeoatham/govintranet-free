<?php

/**
 * User Profile
 *
 * @package bbPress
 * @subpackage Theme
 */

$directorystyle = get_option('general_intranet_staff_directory_style'); // 0 = squares, 1 = circles

?>
</div></div></div> <!-- close bbPress divs so that we can hide the element -->

<div class="row">

	<div class="col-lg-2 col-md-2 col-sm-3">

		<?php
		$user_id = bbp_get_displayed_user_field( 'id' ); 
		$poduser = new Pod ('user' , $user_id);
	
	 	if (function_exists('get_wp_user_avatar')){	
				$imgsrc = get_wp_user_avatar_src($user_id,'thumbnail');				
				if ($directorystyle==1){
					echo "<img class='img img-responsive img-circle' src='".$imgsrc."' width='145'  height='145' />";
				}else{
					echo "<img class='img img-responsive' src='".$imgsrc."' width='145'  height='145' />";
				}
		}
		if (current_user_can('edit_themes')) echo "<br><p><a href='".site_url()."/wp-admin/user-edit.php?user_id=".$user_id."'>Edit profile</a></p>";
		elseif ( is_user_logged_in() && get_current_user_id() == $user_id ) echo "<br><p><a href='".site_url()."/wp-admin/profile.php'>Edit profile</a></p>";
	?>
	</div>
	<div class="col-lg-5 col-md-5 col-sm-9">
		<?
		  $terms = $poduser->get_field('user_team');
			if ($terms) {
			echo '<h3 class="contacthead">Team</h3>';

				$teamlist = array(); //build array of hierarchical teams
			  		foreach ($terms as $taxonomy ) {
			  		    $themeid = $taxonomy['term_id'];
			  		    $themeURL= $taxonomy['slug'];
			  		    $themeparent = $taxonomy['parent']; 
			   		    if ($themeURL == 'uncategorized') {
				  		    continue;
			  		    }
			  		    while ($themeparent!=0){
			  		    	$parentteam = get_term_by('id', $themeparent, 'team'); 
			  		    	$parentURL = $parentteam->slug;
			  		    	$parentname =$parentteam->name; 
				  			$teamlist[]= " <a href='".site_url()."/team/{$parentURL}'>".$parentname."</a>";   
				  			$themeparent = $parentname['parent']; 
			  		    }
			  		    
			  			$teamlist[]= " <a href='".site_url()."/team/{$themeURL}'>".$taxonomy['name']."</a>";
			  			echo "<strong>Team:</strong> ".implode(" &raquo; ", $teamlist)."<br>";

					}
			}  
			echo "<p><strong>Job title: </strong>";
			bbp_displayed_user_field( 'user_job_title' );
			echo "</p>";

?>
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

		<?php if ( bbp_get_displayed_user_field( 'description' ) ) : ?>

			<h3 class="contacthead" >About me</h3>
			<?php bbp_displayed_user_field( 'description' ); ?>
		<?php endif; 


		  $skills = $poduser->get_field('user_key_skills');
		  if ($skills){
			  echo "<h3 class='contacthead'>Key skills and experience</h3>";
			  echo $skills;
		  }
		  $poduser->get_field('user_team');

		  $uqblog = $wpdb->get_results("select ID from wp_posts where post_author = ".$author." and post_type='blog' and post_status='publish';",ARRAY_A);
		  $uqforum = $wpdb->get_results("select ID from wp_posts where post_author = ".$author." and (post_type='topic' or post_type='forum' or post_type='reply') and post_status='publish';",ARRAY_A);
if (count($uqblog)>0 || count($uqforum) > 0):
?>
			<h3 class="contacthead" >On the intranet</h3>
			<ul>
<?php if (count($uqblog)>0):?>
			<li>
				<span class='bbp-user-topics-created-link'>
					<a href="/author/<?php echo $poduser->get_field('login'); ?>" title="<?php printf( esc_attr__( "%s's Blog posts", 'bbpress' ), bbp_get_displayed_user_field( 'display_name' ) ); ?>"><?php _e( 'Blog posts', 'bbpress' ); ?></a>
				</span>
			</li>
<?php endif; ?>
<?php if (count($uqforum)>0):?>
			<li>
				<span class='bbp-user-topics-created-link'>
					<a href="/author/<?php echo $poduser->get_field('login'); ?>/?show=forum" title="<?php printf( esc_attr__( "%s's Blog posts", 'bbpress' ), bbp_get_displayed_user_field( 'display_name' ) ); ?>"><?php _e( 'Forum posts', 'bbpress' ); ?></a>
				</span>
			</li>
<?php endif; ?>
			</ul>
<?php endif; ?>

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
						$imgsrc = get_wp_user_avatar_src($poduserparent['ID'],'thumbnail');				
						if ($directorystyle==1){
								$avatarhtml = str_replace('avatar-60', 'avatar-60  indexcard-avatar img img-circle', get_avatar($poduserparent['ID'],60));						
								} else {
								$avatarhtml = str_replace('avatar-60', 'avatar-60 indexcard-avatar img', get_avatar($poduserparent['ID'],60));	
						}
						echo "<a title='".$poduserparent['display_name']."' href='".site_url()."/staff/".$poduserparent['user_nicename']."/'>".$avatarhtml."</a>";						
						} else {
						echo "<a title='".$poduserparent['display_name']."' href='".site_url()."/staff/".$poduserparent['user_nicename']."/'></a>";						
					}
					echo "<p><a href='".site_url()."/staff/".$poduserparent['user_nicename']."/'>".$poduserparent['display_name']."</a><br>";
					echo get_user_meta($poduserparent['ID'],'user_job_title',true);
					echo "</p>";
					echo "<p><i class='glyphicon glyphicon-chevron-up'></i></p>";
				}

				echo "<p><strong>";
				bbp_displayed_user_field( 'display_name' );
				echo "<br>".get_user_meta($user_id,'user_job_title',true);
				echo "</strong></p>";

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
					$imgstyle='';
					if (function_exists('get_wp_user_avatar')){						
						$imgsrc = get_wp_user_avatar_src($pid,'thumbnail','left');		
						$imgstyle.=" width='50' height='50'";
						if ($directorystyle==1){
							 $imgstyle.=" class='img img-circle'";
						}else{
							 $imgstyle.=" class='img'";
						}
						
						echo "<a class='tlink' data-placement='right' data-original-title = '".$u->user_nicename."' title='".$u->display_name."' href='".site_url()."/staff/".$u->user_nicename."'><img src='".$imgsrc."' ".$imgstyle."/></a>";
						} else {
						echo "<a title='".$u->display_name."' href='".site_url()."/staff/".$u->user_nicename."'></a>";
							
						}

					}
						echo "</p>";
				}

				echo "</div></div>";
	
		?></div>

	</div>

	<?php do_action( 'bbp_template_after_user_profile' ); ?>
		</div>
		
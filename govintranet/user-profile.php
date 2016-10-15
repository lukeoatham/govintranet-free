<?php

/**
 * User Profile
 *
 * @package bbPress
 * @subpackage Theme
 */

$directorystyle = get_option('options_staff_directory_style'); // 0 = squares, 1 = circles
$staffdirectory = get_option('options_module_staff_directory');
global $wpdb;
$user_id = bbp_get_displayed_user_field( 'id' ); 
$poduser = get_userdata($user_id);		
$avstyle="";
if ( $directorystyle==1 ) $avstyle = " img-circle";
$imgsrc = get_avatar($user_id , 150);
$imgsrc = str_replace(" photo", " photo ".$avstyle, $imgsrc);

do_action( 'bbp_template_before_user_profile' ); 
?>

	<div class="col-lg-6 col-md-6 col-sm-12">
		<?php
		if ( is_user_logged_in() && $user_id == get_current_user_id() ):
			$userid = get_current_user_id();
			$favs = get_user_meta($userid, 'user_favourites', true );
			$favli = "";
			if ( $favs ):
				foreach ( $favs as $f ){
					$favli.= "<li><a href='".get_permalink($f)."'>".get_the_title($f)."</a></li>";
				}
				echo '<h3 class="contacthead">' . __('Intranet favourites' , 'govintranet') . '</h3><ul class="bullets">'.$favli.'</ul>';
			endif;
		endif;
			
		if ( $staffdirectory ):
	  	$teams = get_user_meta($user_id,'user_team',true);
		if ($teams) {
			echo '<h3 class="contacthead">' . __('Team' , 'govintranet') . '</h3>';
			$teamlist = array(); //build array of hierarchical teams
		  		foreach ((array)$teams as $t ) { 
		  			$team = get_post($t);
		  		    $teamid = $team->ID;
		  		    $teamparent = $team->post_parent; 
		  		    while ($teamparent!=0){
		  		    	$parentteam = get_post($teamparent); 
		  		    	$parentURL = $parentteam->post_name;
		  		    	$parentname =govintranetpress_custom_title($parentteam->post_title); 
			  			$teamlist[]= " <a href='".get_permalink($teamparent)."'>".$parentname."</a>";   
			  			$teamparent = $parentteam->post_parent; 
		  		    }
		  			$teamlist[]= " <a href='".get_permalink($team->ID)."'>".govintranetpress_custom_title($team->post_title)."</a>";
		  			echo "<p><strong>" . __('Team' , 'govintranet'). ":</strong> ".implode(" &raquo; ", $teamlist)."</p>";
		  			$teamlist=array();
				}
		}  
		$jt = get_user_meta($user_id, 'user_job_title',true );
		$ug = get_user_meta( $user_id, 'user_grade',true ); 
		if (!$teams && ($jt || $ug)) echo '<h3 class="contacthead">' . __("Role" , "govintranet") . '</h3>';
		if ($jt) echo "<p><strong>" . __('Job title' , 'govintranet' ) . ": </strong>".$jt."</p>";
		if ($ug) {
			$ug = get_term($ug, 'grade', ARRAY_A);
			if ($ug['name']) echo "<p><strong>" . __('Grade' , 'govintranet') . ": </strong>".$ug['name']."</p>";
		}
		endif;
		?>
		<h3 class="contacthead"><?php echo _x('Contact' , 'Address book details' , 'govintranet'); ?></h3>
		<?php 
		if ( $staffdirectory ):
		if ( bbp_get_displayed_user_field( 'user_telephone' ) ) : ?>
			<p class="bbp-user-description"><i class="dashicons dashicons-phone"></i> <a href="tel:<?php echo str_replace(" ", "",  get_user_meta($user_id, 'user_telephone',true ) ); ?>"><?php bbp_displayed_user_field( 'user_telephone' ); ?></a></p>
		<?php 
		endif; 
		if ( bbp_get_displayed_user_field( 'user_mobile' ) ) : 
		?>
			<p class="bbp-user-description"><i class="dashicons dashicons-smartphone"></i> <a href="tel:<?php echo str_replace(" ", "", get_user_meta($user_id, 'user_mobile',true ) ); ?>"><?php bbp_displayed_user_field( 'user_mobile' ); ?></a></p>
		<?php 
		endif;
		endif;
		if ( bbp_get_displayed_user_field( 'user_email' ) ) : 
		?>
			<p class="bbp-user-description"><a href="mailto:<?php bbp_displayed_user_field( 'user_email' ); ?>"><?php echo _x('Email' , 'verb' , 'govintranet'); ?> <?php bbp_displayed_user_field( 'first_name' ); echo " "; bbp_displayed_user_field( 'last_name' ); ?></a></p>
			<?php 
		endif; 
		if ( $staffdirectory ):
		if ( bbp_get_displayed_user_field( 'user_twitter_handle' ) ) : 
		?>
			<p class="bbp-user-description"><i class="dashicons dashicons-twitter"></i> <a href="https://twitter.com/<?php bbp_displayed_user_field( 'user_twitter_handle' ); ?>"><?php bbp_displayed_user_field( 'user_twitter_handle' ); ?></a></p>
			<?php 
		endif; 

		if ( bbp_get_displayed_user_field( 'user_linkedin_url' ) ) : 
		?>
			<p class="bbp-user-description"><i class="dashicons dashicons-admin-site"></i> <a href="<?php bbp_displayed_user_field( 'user_linkedin_url' ); ?>">LinkedIn</a></p>
			<?php 
		endif; 


		if ( bbp_get_displayed_user_field( 'user_working_pattern' ) ) : 
		?>
			<h3 class="contacthead"><?php echo _x('Working pattern' , 'Hours of work' , 'govintranet'); ?></h3>
			<?php echo wpautop(get_user_meta($user_id,'user_working_pattern',true)); ?>
			<?php 
		endif;
		if ( bbp_get_displayed_user_field( 'description' ) ) : 
		?>
			<h3 class="contacthead"><?php _e('About me' , 'govintranet'); ?></h3>
			<p><?php bbp_displayed_user_field( 'description' ); ?></p>
			<?php
		endif;
		
		$skills = get_user_meta($user_id,'user_key_skills',true);
		if ($skills){
		  echo "<h3 class='contacthead'>" . _x('Key skills and experience' , 'Job skills' , 'govintranet'). "</h3>";
		  echo wpautop($skills);
		}
		$poduser = get_user_meta($user_id,'user_team',true);
		endif;

		?>
	</div>

	<div class="clearfix col-lg-6 col-md-6 col-sm-12">
		<?php if ( get_option('options_module_staff_directory') ){ ?>
			<script>
			jQuery('.tlink').tooltip();
			</script>
			<?php 
			$poduserparent = get_user_meta( $user_id , 'user_line_manager', true); 
			$poduserparent = get_userdata($poduserparent);
			echo "<div class='panel panel-default'>
			<div class='panel-heading oc'>" . __('Organisation tree' , 'govintranet') . "</div>
			<div class='panel-body'>
			<div class='oc'>";
			if ($poduserparent){
				$avstyle="";
				if ( $directorystyle==1 ) $avstyle = " img-circle";
				$avatarhtml = get_avatar($poduserparent->ID , 150,'',$poduserparent->display_name);
				$avatarhtml = str_replace(" photo", " photo ".$avstyle, $avatarhtml);
				$avatarhtml = str_replace('"150"', '"96"', $avatarhtml);
				$avatarhtml = str_replace("'150'", "'96'", $avatarhtml);
				echo "<a title='".esc_attr($poduserparent->display_name)."' href='".site_url()."/staff/".$poduserparent->user_nicename."/'>".$avatarhtml."</a>";										
				echo "<p><a href='".site_url()."/staff/".$poduserparent->user_nicename."/'>".$poduserparent->display_name."</a><br>";
				echo get_user_meta($poduserparent->ID,'user_job_title',true);
				echo "</p>";
				echo "<p><i class='dashicons dashicons-arrow-up-alt2'></i></p>";
			}
			echo "<p><strong>";
			bbp_displayed_user_field( 'display_name' );
			echo "<br>".get_user_meta($user_id,'user_job_title',true);
			echo "</strong></p>";
			$q = "select meta_value as ID, user_id, display_name from $wpdb->users join $wpdb->usermeta on $wpdb->users.ID = $wpdb->usermeta.user_id where $wpdb->usermeta.meta_key='user_line_manager' and $wpdb->usermeta.meta_value = ".$user_id;
			$poduserreports = $wpdb->get_results($q,ARRAY_A);
			if (count($poduserreports)>0){
				echo "<p><i class='dashicons dashicons-arrow-down-alt2'></i></p>";
				echo "<p id='directreports'>";
				foreach ($poduserreports as $p){ 
					$pid = $p['user_id'];
	                $u = get_userdata($pid);
	                $jobtitle = get_user_meta($pid, 'user_job_title', true);
	                if ($jobtitle) $jobtitle = " - ".$jobtitle;
					$imgstyle='';
					$avstyle="";
					if ( $directorystyle==1 ) $avstyle = " img-circle";
					$imgsrc = get_avatar($pid, 66,'',$u->display_name);				
					$imgsrc = str_replace(" photo", " photo ".$avstyle, $imgsrc);
					echo "<a title='".esc_attr( $u->display_name )."' href='".site_url()."/staff/".$u->user_nicename."'>".$imgsrc."</a>";
				}
				echo "</p>";
			}
			echo "</div></div>";
			?>
			</div>

	<?php } ?>
	</div>
	<?php do_action( 'bbp_template_after_user_profile' ); ?>

			

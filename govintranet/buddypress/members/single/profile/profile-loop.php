<?php
/**
 * BuddyPress - Members Profile Loop
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

/** This action is documented in bp-templates/bp-legacy/buddypress/members/single/profile/profile-wp.php */
do_action( 'bp_before_profile_loop_content' ); ?>
<?php
$directorystyle = get_option('options_staff_directory_style'); // 0 = squares, 1 = circles
$staffdirectory = get_option('options_module_staff_directory');
global $wpdb;
$user_id = bp_displayed_user_id(); 
$poduser = get_userdata($user_id);		
$avstyle="";
if ( $directorystyle==1 ) $avstyle = " img-circle";
$imgsrc = get_avatar($user_id , 150);
$imgsrc = str_replace(" photo", " photo ".$avstyle, $imgsrc);
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
  		    	$parentname = govintranetpress_custom_title($parentteam->post_title); 
	  			$teamlist[] = " <a href='".get_permalink($teamparent)."'>".$parentname."</a>";   
	  			$teamparent = $parentteam->post_parent; 
  		    }
  			array_unshift( $teamlist, " <a href='".get_permalink($team->ID)."'>".govintranetpress_custom_title($team->post_title)."</a>" );
  			$teamlist = array_reverse( $teamlist );
  			echo "<p class='contactteam'><strong>" . __('Team' , 'govintranet'). ":</strong> ".implode(" &raquo; ", $teamlist)."</p>";
  			$teamlist=array();
		}
}  
$jt = get_user_meta($user_id, 'user_job_title',true );
$ug = get_user_meta( $user_id, 'user_grade',true ); 
if (!$teams && ($jt || $ug)) echo '<h3 class="contacthead">' . __("Role" , "govintranet") . '</h3>';
if ($jt) echo "<p class='contactjobtitle'><strong>" . __('Job title' , 'govintranet' ) . ": </strong>".$jt."</p>";
if ($ug) {
	$ug = get_term($ug, 'grade', ARRAY_A);
	if ($ug['name']) echo "<p class='contactgrade'><strong>" . __('Grade' , 'govintranet') . ": </strong>".$ug['name']."</p>";
}
endif;
?>
<h3 class="contacthead"><?php echo _x('Contact' , 'Address book details' , 'govintranet'); ?></h3>
<?php 
if ( $staffdirectory ):
	if ( get_user_meta($user_id,  'user_telephone', true ) ) : 
		$callno = get_user_meta($user_id, 'user_telephone',true );
		?>
		<p class="bbp-user-description"><i class="dashicons dashicons-phone"></i> <a href="<?php echo govintranet_get_call_permalink($callno); ?>"><?php echo get_user_meta($user_id, 'user_telephone', true ); ?></a></p>
	<?php 
	endif; 
	if (  get_user_meta($user_id,  'user_mobile', true ) ) : 
		$callno = get_user_meta($user_id, 'user_mobile',true );
		?>
		<p class="bbp-user-description"><i class="dashicons dashicons-smartphone"></i> <a href="<?php echo govintranet_get_call_permalink($callno); ; ?>"><?php echo get_user_meta($user_id, 'user_mobile', true ); ?></a></p>
	<?php 
	endif;
endif;
if ( get_user_meta($user_id,  'user_email', true ) ) : 
?>
	<p class="bbp-user-description"><a href="mailto:<?php echo get_user_meta($user_id,  'user_email', true ); ?>"><?php echo _x('Email' , 'verb' , 'govintranet'); ?> <?php echo get_user_meta($user_id, 'first_name', true ); echo " "; echo get_user_meta($user_id,  'last_name', true ); ?></a></p>
	<?php 
endif; 
if ( $staffdirectory ):
	if (  get_user_meta($user_id,  'user_twitter_handle', true ) ) : 
	?>
		<p class="bbp-user-description"><i class="dashicons dashicons-twitter"></i> <a href="https://twitter.com/<?php bbp_displayed_user_field( 'user_twitter_handle' ); ?>"><?php echo get_user_meta($user_id,  'user_twitter_handle', true ); ?></a></p>
		<?php 
	endif; 

	if ( get_user_meta($user_id,  'user_linkedin_url', true ) ) : 
	?>
		<p class="bbp-user-description"><i class="dashicons dashicons-admin-site"></i> <a href="<?php echo get_user_meta($user_id,  'user_linkedin_url', true ); ?>">LinkedIn</a></p>
		<?php 
	endif; 


	if ( get_user_meta($user_id,  'user_working_pattern', true ) ) : 
	?>
		<h3 class="contacthead"><?php echo _x('Working pattern' , 'Hours of work' , 'govintranet'); ?></h3>
		<?php echo wpautop(get_user_meta($user_id,'user_working_pattern',true)); ?>
		<?php 
	endif;
	if ( get_user_meta($user_id,  'description', true ) ) : 
	?>
		<h3 class="contacthead"><?php _e('About me' , 'govintranet'); ?></h3>
		<p><?php echo get_user_meta($user_id,  'description', true ); ?></p>
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
<?php if ( bp_has_profile() ) : ?>

	<?php while ( bp_profile_groups() ) : bp_the_profile_group(); ?>

		<?php if ( bp_profile_group_has_fields() ) : ?>

			<?php
			/** This action is documented in bp-templates/bp-legacy/buddypress/members/single/profile/profile-wp.php */
			do_action( 'bp_before_profile_field_content' ); ?>

			<div class="bp-widget <?php bp_the_profile_group_slug(); ?>">

				<h2><?php bp_the_profile_group_name(); ?></h2>

				<table class="profile-fields">

					<?php while ( bp_profile_fields() ) : bp_the_profile_field(); ?>

						<?php if ( bp_field_has_data()  ) : 
							if ( bp_get_the_profile_field_name() == "Name" ) continue;
							?>

							<tr<?php bp_field_css_class(); ?>>

								<td class="bp_label"><?php bp_the_profile_field_name(); ?></td>

								<td class="bp_data"><?php bp_the_profile_field_value(); ?></td>

							</tr>

						<?php endif; ?>

						<?php

						/**
						 * Fires after the display of a field table row for profile data.
						 *
						 * @since 1.1.0
						 */
						do_action( 'bp_profile_field_item' ); ?>

					<?php endwhile; ?>

				</table>
			</div>

			<?php

			/** This action is documented in bp-templates/bp-legacy/buddypress/members/single/profile/profile-wp.php */
			do_action( 'bp_after_profile_field_content' ); ?>

		<?php endif; ?>

	<?php endwhile; ?>

	<?php

	/** This action is documented in bp-templates/bp-legacy/buddypress/members/single/profile/profile-wp.php */
	do_action( 'bp_profile_field_buttons' ); ?>

<?php endif; ?>

<?php

/** This action is documented in bp-templates/bp-legacy/buddypress/members/single/profile/profile-wp.php */
do_action( 'bp_after_profile_loop_content' ); ?>

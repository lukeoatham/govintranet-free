<?php
/*
Plugin Name: HT Profile Nudge
Plugin URI: http://www.helpfultechnology.com
Description: Widget to display reminders to complete the staff profile
Author: Luke Oatham
Version: 1.1
Author URI: http://www.helpfultechnology.com
*/

class htProfileNudge extends WP_Widget {

	function __construct() {
		
		parent::__construct(
			'htProfileNudge',
			__( 'HT Profile Nudge' , 'govintranet'),
			array( 'description' => __( 'Display reminders to complete missing staff profile entries.' , 'govintranet') )
		);   
    }

    function widget($args, $instance) {
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        $telephone = ($instance['telephone']);
        $mobile = ($instance['mobile']);
        $job_title = ($instance['job_title']);
        $grade = ($instance['grade']);
        $team = ($instance['team']);
        $key_skills = ($instance['key_skills']);
        $bio = ($instance['bio']);
        $linemanager = ($instance['linemanager']);
        $photo = ($instance['photo']);


		 wp_register_script( 'pauseNudge', plugins_url("/ht-profile-nudge/pauseNudge.js"));
		 wp_enqueue_script( 'pauseNudge' );


		 echo "<div id='ht_profile_nudge'>";



	$current_user = wp_get_current_user();
	if ($current_user->ID):
		$userid = $current_user->ID;
		
		if (!get_user_meta($userid,'user_telephone',true) &&  !isset($_COOKIE['ht_profile_nudge_telephone']) && $telephone=='on'):
			/******************************************
			*
			* UPDATE TELEPHONE NUMBER
			*
			*******************************************/
			echo $before_widget; ?>
			<h3><?php echo sprintf( __('%s, your number is not listed' , 'govintranet' ) , $current_user->user_firstname ) ?></h3>
			<p><?php echo __('We noticed that your telephone number is missing from your staff profile. You can add it now if you like?','govintranet') ; ?>
			</p>
			<form class="form-horizontal" role="form" name="update-profile" action="<?php echo plugins_url('/ht-profile-nudge/update_profile.php'); ?>" method="post">
			<label for"phone"><?php echo __('Telephone','govintranet'); ?></label>
			<input class="form-control" name="phone" id="phone" placeholder="e.g. 0203 459 8765" /><br>
			<button type="submit" class="btn btn-primary"><?php _e('Update now','govintranet'); ?></button> <a href="#" onclick="javascript:pauseProfileNudge('ht_profile_nudge_telephone');"><small><?php _e('I\'ll do it later','govintranet');?></small></a><br>
			<?php $nonce = wp_create_nonce( 'update-profile_'.$userid );?>
			<input type="hidden" name="_wpnonce" value="<?php echo $nonce; ?>" />
			<input type="hidden" name="userid" value="<?php echo $userid; ?>" />
			<input type="hidden" name="type" value="add-phone" />
			</form>		
	<?php
			echo $after_widget; 
	
		elseif (!get_user_meta($userid,'user_mobile',true) &&  !isset($_COOKIE['ht_profile_nudge_mobile']) && $mobile=='on'):
			/******************************************
			*
			* UPDATE MOBILE NUMBER
			*
			*******************************************/
			echo $before_widget; ?>
			<h3><?php echo  sprintf( __('%s, did you know?' , 'govintranet' ) , $current_user->user_firstname ) ?></h3>
			<p><?php echo __('Your mobile number is missing. You can add it now if you like?','govintranet'); ?></p>
			<form class="form-horizontal" role="form" name="update-profile" action="<?php echo plugins_url('/ht-profile-nudge/update_profile.php'); ?>" method="post">
			<label for"mobile"><?php echo __('Mobile','govintranet'); ?></label>
			<input class="form-control" name="mobile" id="mobile" /><br>
			<button type="submit" class="btn btn-primary"><?php _e('Update now','govintranet'); ?></button> <a href="#" onclick="javascript:pauseProfileNudge('ht_profile_nudge_mobile');"><small><?php _e('I\'ll do it later','govintranet');?></small></a><br>
			<?php $nonce = wp_create_nonce( 'update-profile_'.$userid );?>
			<input type="hidden" name="_wpnonce" value="<?php echo $nonce; ?>" />
			<input type="hidden" name="userid" value="<?php echo $userid; ?>" />
			<input type="hidden" name="type" value="add-mobile" />
			</form>		
	<?php
			echo $after_widget; 
	
		elseif (!get_user_meta($userid,'user_job_title',true) &&  !isset($_COOKIE['ht_profile_nudge_job_title']) && $job_title=='on'):
			/******************************************
			*
			* UPDATE JOB TITLE
			*
			*******************************************/
			echo $before_widget; ?>
			<h3><?php echo  sprintf( __('%s, did you know?' , 'govintranet' ) , $current_user->user_firstname ) ?></h3>
			<p><?php echo __('Your job title is missing from your staff profile. You can add it now if you like?','govintranet'); ?></p>
			<form class="form-horizontal" role="form" name="update-profile" action="<?php echo plugins_url('/ht-profile-nudge/update_profile.php'); ?>" method="post">
			<label for"job_title"><?php echo  __('Job title','govintranet'); ?></label>
			<input class="form-control" name="job_title" id="job_title" placeholder="e.g. Data entry clerk" /><br>
			<button type="submit" class="btn btn-primary"><?php _e('Update now','govintranet'); ?></button> <a href="#" onclick="javascript:pauseProfileNudge('ht_profile_nudge_job_title');"><small><?php _e('I\'ll do it later','govintranet');?></small></a><br>
			<?php $nonce = wp_create_nonce( 'update-profile_'.$userid );?>
			<input type="hidden" name="_wpnonce" value="<?php echo $nonce; ?>" />
			<input type="hidden" name="userid" value="<?php echo $userid; ?>" />
			<input type="hidden" name="type" value="add-job-title" />
			</form>		
	<?php
			echo $after_widget; 
		
		elseif (!get_user_meta($userid,'user_grade',true) &&  !isset($_COOKIE['ht_profile_nudge_grade']) && $grade=='on'):
			/******************************************
			*
			* UPDATE GRADE
			*
			*******************************************/
			echo $before_widget; ?>
			<h3><?php echo sprintf( __('Pssst, %s' , 'govintranet' ) , $current_user->user_firstname ); ?></h3> <p><?php echo __('Please enter your grade so that you appear correctly in team listings.','govintranet'); ?></p>
			<form class="form-inline" role="form" name="update-profile" id="update-profile" action="<?php echo plugins_url('/ht-profile-nudge/update_profile.php'); ?>" method="post">
			<select class="form-control" name="grade" id="grade">
						  <?php
						  	echo "<option value='0'>" .  __('Choose your grade','govintranet') . "</option>";
							$terms = get_terms('grade',array('hide_empty'=>false,'orderby'=>'slug','order'=>'ASC',"parent"=>0));
							if ($terms) {
						  		foreach ((array)$terms as $taxonomy ) {
						  		    $themeid = $taxonomy->term_id;
						  		    $themeURL= $taxonomy->slug;		  		    			  		    
						  			echo "<option value='{$themeid}'>{$taxonomy->name}</option>";
								}
							}  
							?>
			</select>
			<button type="submit" class="btn btn-primary"><?php _e('Update','govintranet');?></button><br>
			<a href="#" onclick="javascript:pauseProfileNudge('ht_profile_nudge_grade');"><small><?php _e('I\'ll do it later','govintranet');?></small></a>
			<?php $nonce = wp_create_nonce( 'update-profile_'.$userid );?>
			<input type="hidden" name="_wpnonce" value="<?php echo $nonce; ?>" />
			<input type="hidden" name="userid" value="<?php echo $userid; ?>" />
			<input type="hidden" name="type" value="add-grade" />
			</form>
	<?php
			echo $after_widget; 
		
		elseif (!get_user_meta($userid,'user_team',true) &&  !isset($_COOKIE['ht_profile_nudge_team']) && $team=='on'):
			/******************************************
			*
			* UPDATE TEAM
			*
			*******************************************/
			echo $before_widget; ?>
			<h3><?php echo  sprintf( __('%s, join a team!' , 'govintranet' ) , $current_user->user_firstname );?></h3>
			<p><?php echo __('To be listed correctly in the staff directory, please choose your team:','govintranet'); ?></p>
			<form class="form-horizontal" role="form" name="update-profile" id="update-profile" action="<?php echo plugins_url('/ht-profile-nudge/update_profile.php'); ?>" method="post">
			<label for="team"><?php echo __('Team','govintranet'); ?></label>
			<select class="form-control" name="team" id="team">
			  <?php
			  	echo "<option value='0'>" . __('Choose your team','govintranet') . "</option>";
				$teams = get_posts('post_type=team&orderby=title&order=ASC&posts_per_page=-1');
				if ($teams) {
			  		foreach ((array)$teams as $team ) {
			  		    $themeid = $team->ID;
			  			echo "<option value='".$themeid."'>".get_the_title($team->ID)."</option>";
					}
				}  
				?>
			</select><br>
			<div class="input-group">
			<button type="submit" class="btn btn-primary"><?php _e('Update','govintranet');?></button><br>
			<a href="#" onclick="javascript:pauseProfileNudge('ht_profile_nudge_team');"><small><?php _e('I\'ll do it later','govintranet');?></small></a>
			</div>
			<?php $nonce = wp_create_nonce( 'update-profile_'.$userid );?>
			<input type="hidden" name="_wpnonce" value="<?php echo $nonce; ?>" />
			<input type="hidden" name="userid" value="<?php echo $userid; ?>" />
			<input type="hidden" name="type" value="add-team" />
			</form>
	<?php
			echo $after_widget; 
		
		elseif (!get_user_meta($userid,'user_key_skills',true) &&  !isset($_COOKIE['ht_profile_nudge_skills']) && $key_skills=='on'):
			/******************************************
			*
			* UPDATE SKILLS AND EXPERIENCE
			*
			*******************************************/
			echo $before_widget; ?>
			<h3><?php echo  sprintf( __('%s, get listed!' , 'govintranet' ) , $current_user->user_firstname ); ?></h3>
			<p><?php echo __('Make sure you appear in staff directory search results by adding your skills and experience.','govintranet');?></p>
			<form class="form" role="form" name="update-profile" id="update-profile" action="<?php echo plugins_url('/ht-profile-nudge/update_profile.php'); ?>" method="post">
			<label for"key_skills"><?php echo __('Skills and experience','govintranet');?></label>
			<textarea class="form-control" rows="3" name="key_skills" id="key_skills" placeholder="<?php _e('e.g. I can help with budget codes, procurement and invoices.','govintranet');?>"></textarea><br>
			<button type="submit" class="btn btn-primary"><?php _e('Update now','govintranet'); ?></button> <a href="#" onclick="javascript:pauseProfileNudge('ht_profile_nudge_skills');"><small><?php _e('I\'ll do it later','govintranet');?></small></a><br>
			<?php $nonce = wp_create_nonce( 'update-profile_'.$userid );?>
			<input type="hidden" name="_wpnonce" value="<?php echo $nonce; ?>" />
			<input type="hidden" name="userid" value="<?php echo $userid; ?>" />
			<input type="hidden" name="type" value="add-skills" />
			</form>		
	<?php
			echo $after_widget; 
		
		elseif (!get_user_meta($userid,'description',true) &&  !isset($_COOKIE['ht_profile_nudge_bio']) && $bio=='on'):
			/******************************************
			*
			* UPDATE BIO
			*
			*******************************************/
			echo $before_widget; ?>
			<h3><?php echo  sprintf( __('About %s' , 'govintranet' ) , $current_user->user_firstname ); ?></h3>
			<p><?php echo __('Your staff bio is empty. Do you want to tell us a little about yourself now?','govintranet');?></p>
			<form class="form" role="form" name="update-profile" id="update-profile" action="<?php echo plugins_url('/ht-profile-nudge/update_profile.php'); ?>" method="post">
			<label for"bio"><?php echo __('Short bio' , 'govintranet');?></label>
			<textarea class="form-control" rows="3" name="bio" id="bio" placeholder="<?php echo _x('I have worked in the Civil Service for 3 years...' , 'Example biography' , 'govintranet' ) ;?>"></textarea><br>
			<button type="submit" class="btn btn-primary"><?php _e('Update now','govintranet'); ?></button> <a href="#" onclick="javascript:pauseProfileNudge('ht_profile_nudge_bio');"><small><?php _e('I\'ll do it later','govintranet');?></small></a><br>
			<?php $nonce = wp_create_nonce( 'update-profile_'.$userid );?>
			<input type="hidden" name="_wpnonce" value="<?php echo $nonce; ?>" />
			<input type="hidden" name="userid" value="<?php echo $userid; ?>" />
			<input type="hidden" name="type" value="add-bio" />
			</form>		
	<?php
			echo $after_widget; 
	
		elseif (!get_user_meta($userid,'user_line_manager',true) &&  !isset($_COOKIE['ht_profile_nudge_linemanager']) && $linemanager=='on'):
			/******************************************
			*
			* UPDATE LINE MANAGER
			*
			*******************************************/
			echo $before_widget; 
			$userurl = "";
			$userurl = get_author_posts_url( $current_user->ID); 
			if (function_exists('bp_activity_screen_index')){ // if using BuddyPress - link to the members page
				$userurl=str_replace('/author', '/members', $userurl."edit/"); }
			elseif (function_exists('bbp_get_displayed_user_field')){ // if using bbPress - link to the staff page
				$userurl=str_replace('/author', '/staff', $userurl."edit/");
			} else {
				$userurl = admin_url('/profile.php');
			}    
			?>
			<h3><?php echo  $current_user->user_firstname; ?></h3>
			<p><?php _e('Please set your line manager in your staff profile so that you appear correctly in the staff directory.','govintranet');?></p>
			<a class="btn btn-primary" href="<?php echo $userurl; ?>"><?php _e('Update now','govintranet');?></a> <a href="#" onclick="javascript:pauseProfileNudge('ht_profile_nudge_linemanager');"><small><?php _e('I\'ll do it later','govintranet');?></small></a><br>
	<?php
			echo $after_widget; 

		elseif ( !get_user_meta($userid,'wp_user_avatar',true) && !get_user_meta($userid,'simple_local_avatar',true) && !isset($_COOKIE['ht_profile_nudge_photo']) && $photo=='on' ):
			/******************************************
			*
			* UPDATE AVATAR
			*
			*******************************************/
			echo $before_widget; 
			$userurl = "";
			$userurl = get_author_posts_url( $current_user->ID); 
			if (function_exists('bp_activity_screen_index')){ // if using BuddyPress - link to the members page
				$userurl=str_replace('/author', '/members', $userurl."edit/"); }
			elseif (function_exists('bbp_get_displayed_user_field')){ // if using bbPress - link to the staff page
				$userurl=str_replace('/author', '/staff', $userurl."edit/");
			} else {
				$userurl = admin_url('/profile.php');
			}    			
			?>
			<h3><?php echo  $current_user->user_firstname; ?></h3>
			<p><?php _e('There is no photo of you on your staff profile. Do you want to add one now?','govintranet');?></p>
			<a class="btn btn-primary" href="<?php echo $userurl; ?>"><?php _e('Update now','govintranet'); ?></a> <a href="#" onclick="javascript:pauseProfileNudge('ht_profile_nudge_photo');"><small><?php _e('I\'ll do it later','govintranet');?></small></a><br>
	<?php
			echo $after_widget; 
	
	
		endif;
	endif;
	
	echo "</div>";
    }


    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['telephone'] = strip_tags($new_instance['telephone']);
		$instance['mobile'] = strip_tags($new_instance['mobile']);
		$instance['job_title'] = strip_tags($new_instance['job_title']);
		$instance['grade'] = strip_tags($new_instance['grade']);
		$instance['team'] = strip_tags($new_instance['team']);
		$instance['key_skills'] = strip_tags($new_instance['key_skills']);
		$instance['bio'] = strip_tags($new_instance['bio']);
		$instance['linemanager'] = strip_tags($new_instance['linemanager']);
		$instance['photo'] = strip_tags($new_instance['photo']);
       return $instance;
    }


    function form($instance) {
        $title = esc_attr($instance['title']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','govintranet'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />

          <input id="<?php echo $this->get_field_id('telephone'); ?>" name="<?php echo $this->get_field_name('telephone'); ?>" type="checkbox" <?php checked((bool) $instance['telephone'], true ); ?> />
          <label for="<?php echo $this->get_field_id('telephone'); ?>"><?php _e('Telephone number','govintranet'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('mobile'); ?>" name="<?php echo $this->get_field_name('mobile'); ?>" type="checkbox" <?php checked((bool) $instance['mobile'], true ); ?> />
          <label for="<?php echo $this->get_field_id('mobile'); ?>"><?php _e('Mobile phone','govintranet'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('job_title'); ?>" name="<?php echo $this->get_field_name('job_title'); ?>" type="checkbox" <?php checked((bool) $instance['job_title'], true ); ?> />
          <label for="<?php echo $this->get_field_id('job_title'); ?>"><?php _e('Job title','govintranet'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('grade'); ?>" name="<?php echo $this->get_field_name('grade'); ?>" type="checkbox" <?php checked((bool) $instance['grade'], true ); ?> />
          <label for="<?php echo $this->get_field_id('grade'); ?>"><?php _e('Grade','govintranet'); ?></label> <br>
          
          <input id="<?php echo $this->get_field_id('team'); ?>" name="<?php echo $this->get_field_name('team'); ?>" type="checkbox" <?php checked((bool) $instance['team'], true ); ?> />
          <label for="<?php echo $this->get_field_id('team'); ?>"><?php _e('Team','govintranet'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('key_skills'); ?>" name="<?php echo $this->get_field_name('key_skills'); ?>" type="checkbox" <?php checked((bool) $instance['key_skills'], true ); ?> />
          <label for="<?php echo $this->get_field_id('key_skills'); ?>"><?php _e('Skills and experience','govintranet'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('bio'); ?>" name="<?php echo $this->get_field_name('bio'); ?>" type="checkbox" <?php checked((bool) $instance['bio'], true ); ?> />
          <label for="<?php echo $this->get_field_id('bio'); ?>"><?php _e('Biography','govintranet'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('linemanager'); ?>" name="<?php echo $this->get_field_name('linemanager'); ?>" type="checkbox" <?php checked((bool) $instance['linemanager'], true ); ?> />
          <label for="<?php echo $this->get_field_id('linemanager'); ?>"><?php _e('Line manager','govintranet'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('photo'); ?>" name="<?php echo $this->get_field_name('photo'); ?>" type="checkbox" <?php checked((bool) $instance['photo'], true ); ?> />
          <label for="<?php echo $this->get_field_id('photo'); ?>"><?php _e('Profile photo','govintranet'); ?></label> <br>

        </p>

        <?php 
    }

}

add_action('widgets_init', create_function('', 'return register_widget("htProfileNudge");'));

?>
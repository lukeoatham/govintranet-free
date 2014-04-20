<?php
/*
Plugin Name: HT Profile Nudge
Plugin URI: http://www.helpfultechnology.com
Description: Widget to display reminders to complete the staff profile
Author: Luke Oatham
Version: 0.1
Author URI: http://www.helpfultechnology.com
*/

class htProfileNudge extends WP_Widget {
    function htProfileNudge() {
        parent::WP_Widget(false, 'HT Profile Nudge', array('description' => 'Display reminders to complete missing staff profile entries.'));
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
			echo $before_widget; ?>
			<h3><?php echo  $current_user->first_name; ?>, your number is not listed</h3>
			<p>We noticed that your telephone number is missing from your staff profile. You can add it now if you like?</p>
			<form class="form-horizontal" role="form" name="update-profile" action="<?php echo plugins_url('/ht-profile-nudge/update_profile.php'); ?>" method="post">
			<label for"phone">Telephone</label>
			<input class="form-control" name="phone" id="phone" placeholder="e.g. 0203 459 8765" /><br>
			<button type="submit" class="btn btn-primary">Update now</button> <a href="#" onclick="javascript:pauseProfileNudge('ht_profile_nudge_telephone');"><small>I'll do it later</small></a><br>
			<?php $nonce = wp_create_nonce( 'update-profile' );?>
			<input type="hidden" name="_wpnonce" value="<? echo $nonce; ?>" />
			<input type="hidden" name="userid" value="<? echo $userid; ?>" />
			<input type="hidden" name="type" value="add-phone" />
			</form>		
	<?php
			echo $after_widget; 
	
		elseif (!get_user_meta($userid,'user_mobile',true) &&  !isset($_COOKIE['ht_profile_nudge_mobile']) && $mobile=='on'):
			echo $before_widget; ?>
			<h3><?php echo  $current_user->first_name; ?>, did you know?</h3>
			<p>Your mobile number is missing. You can add it now if you like?</p>
			<form class="form-horizontal" role="form" name="update-profile" action="<?php echo site_url('/wp-content/plugins/ht-profile-nudge/update_profile.php'); ?>" method="post">
			<label for"mobile">Mobile number</label>
			<input class="form-control" name="mobile" id="mobile" /><br>
			<button type="submit" class="btn btn-primary">Update now</button> <a href="#" onclick="javascript:pauseProfileNudge('ht_profile_nudge_mobile');"><small>I'll do it later</small></a><br>
			<?php $nonce = wp_create_nonce( 'update-profile' );?>
			<input type="hidden" name="_wpnonce" value="<? echo $nonce; ?>" />
			<input type="hidden" name="userid" value="<? echo $userid; ?>" />
			<input type="hidden" name="type" value="add-mobile" />
			</form>		
	<?php
			echo $after_widget; 
	
		elseif (!get_user_meta($userid,'user_job_title',true) &&  !isset($_COOKIE['ht_profile_nudge_job_title']) && $job_title=='on'):
		echo isset($_COOKIE['ht_profile_nudge_job_title']);
			echo $before_widget; ?>
			<h3><?php echo  $current_user->first_name; ?>, did you know?</h3>
			<p>Your job title is missing from your staff profile. You can add it now if you like?</p>
			<form class="form-horizontal" role="form" name="update-profile" action="<?php echo site_url('/wp-content/plugins/ht-profile-nudge/update_profile.php'); ?>" method="post">
			<label for"job_title">Job title</label>
			<input class="form-control" name="job_title" id="job_title" placeholder="e.g. Data entry clerk" /><br>
			<button type="submit" class="btn btn-primary">Update now</button> <a href="#" onclick="javascript:pauseProfileNudge('ht_profile_nudge_job_title');"><small>I'll do it later</small></a><br>
			<?php $nonce = wp_create_nonce( 'update-profile' );?>
			<input type="hidden" name="_wpnonce" value="<? echo $nonce; ?>" />
			<input type="hidden" name="userid" value="<? echo $userid; ?>" />
			<input type="hidden" name="type" value="add-job-title" />
			</form>		
	<?php
			echo $after_widget; 
		
		elseif (!get_user_meta($userid,'user_grade',true) &&  !isset($_COOKIE['ht_profile_nudge_grade']) && $grade=='on'):
			echo $before_widget; ?>
			<h3>Pssst, <?php echo  $current_user->first_name; ?>!</h3> <p>Please enter your grade so that you appear correctly in team listings.</p>
			<form class="form-inline" role="form" name="update-profile" id="update-profile" action="<?php echo site_url('/wp-content/plugins/ht-profile-nudge/update_profile.php'); ?>" method="post">
			<select class="form-control" name="grade" id="grade">
						  <?php
						  	echo "<option value='0'>Choose your grade</option>";
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
			<button type="submit" class="btn btn-primary">Update</button><br>
			<a href="#" onclick="javascript:pauseProfileNudge('ht_profile_nudge_grade');"><small>I'll do it later</small></a>
			<?php $nonce = wp_create_nonce( 'update-profile' );?>
			<input type="hidden" name="_wpnonce" value="<? echo $nonce; ?>" />
			<input type="hidden" name="userid" value="<? echo $userid; ?>" />
			<input type="hidden" name="type" value="add-grade" />
			</form>
	<?php
			echo $after_widget; 
		
		elseif (!get_user_meta($userid,'user_team',true) &&  !isset($_COOKIE['ht_profile_nudge_team']) && $team=='on'):
			echo $before_widget; ?>
			<h3><?php echo  $current_user->first_name; ?>, join a team!</h3>
			<p>To be listed correctly in the staff directory, please choose your team:</p>
			<form class="form-horizontal" role="form" name="update-profile" id="update-profile" action="<?php echo site_url('/wp-content/plugins/ht-profile-nudge/update_profile.php'); ?>" method="post">
			<label for="team">Teams</label>
			<select class="form-control" name="team" id="team">
			  <?php
			  	echo "<option value='0'>Pick your local team</option>";
				$terms = get_terms('team',array('hide_empty'=>false,'orderby'=>'slug','order'=>'ASC'));
				if ($terms) {
			  		foreach ((array)$terms as $taxonomy ) {
			  		    $themeid = $taxonomy->term_id;
			  		    $themeURL= $taxonomy->slug;		  		  
			  			echo "<option value='{$themeid}'>{$taxonomy->name}</option>";
					}
				}  
				?>
			</select><br>
			<div class="input-group">
			<button type="submit" class="btn btn-primary">Update</button><br>
			<a href="#" onclick="javascript:pauseProfileNudge('ht_profile_nudge_team');"><small>I'll do it later</small></a>
			</div>
			<?php $nonce = wp_create_nonce( 'update-profile' );?>
			<input type="hidden" name="_wpnonce" value="<? echo $nonce; ?>" />
			<input type="hidden" name="userid" value="<? echo $userid; ?>" />
			<input type="hidden" name="type" value="add-team" />
			</form>
	<?php
			echo $after_widget; 
		
		elseif (!get_user_meta($userid,'user_key_skills',true) &&  !isset($_COOKIE['ht_profile_nudge_skills']) && $key_skills=='on'):
			echo $before_widget; ?>
			<h3><?php echo  $current_user->first_name; ?>, get listed!</h3>
			<p>Make sure you appear in staff directory search results by adding your skills and experience.</p>
			<form class="form" role="form" name="update-profile" id="update-profile" action="<?php echo site_url('/wp-content/plugins/ht-profile-nudge/update_profile.php'); ?>" method="post">
			<label for"key_skills">Skills and experience</label>
			<textarea class="form-control" rows="3" name="key_skills" id="key_skills" placeholder="e.g. I work in finance and can help with budget codes, procurement and invoices."></textarea><br>
			<button type="submit" class="btn btn-primary">Update now</button> <a href="#" onclick="javascript:pauseProfileNudge('ht_profile_nudge_skills');"><small>I'll do it later</small></a><br>
			<?php $nonce = wp_create_nonce( 'update-profile' );?>
			<input type="hidden" name="_wpnonce" value="<? echo $nonce; ?>" />
			<input type="hidden" name="userid" value="<? echo $userid; ?>" />
			<input type="hidden" name="type" value="add-skills" />
			</form>		
	<?php
			echo $after_widget; 
		
		elseif (!get_user_meta($userid,'description',true) &&  !isset($_COOKIE['ht_profile_nudge_bio']) && $bio=='on'):
			echo $before_widget; ?>
			<h3>About <?php echo  $current_user->first_name; ?></h3>
			<p>Your staff bio is empty. Do you want to tell us a little about yourself now?</p>
			<form class="form" role="form" name="update-profile" id="update-profile" action="<?php echo site_url('/wp-content/plugins/ht-profile-nudge/update_profile.php'); ?>" method="post">
			<label for"bio">Short bio</label>
			<textarea class="form-control" rows="3" name="bio" id="bio" placeholder="I have been in Government 10 years..."></textarea><br>
			<button type="submit" class="btn btn-primary">Update now</button> <a href="#" onclick="javascript:pauseProfileNudge('ht_profile_nudge_bio');"><small>I'll do it later</small></a><br>
			<?php $nonce = wp_create_nonce( 'update-profile' );?>
			<input type="hidden" name="_wpnonce" value="<? echo $nonce; ?>" />
			<input type="hidden" name="userid" value="<? echo $userid; ?>" />
			<input type="hidden" name="type" value="add-bio" />
			</form>		
	<?php
			echo $after_widget; 
	
		elseif (!get_user_meta($userid,'user_line_manager',true) &&  !isset($_COOKIE['ht_profile_nudge_linemanager']) && $linemanager=='on'):
			echo $before_widget; ?>
			<h3><?php echo  $current_user->first_name; ?></h3>
			<p>Please set your line manager in your staff profile so that you appear correctly in the staff directory.</p>
			<a class="btn btn-primary" href="<?php echo admin_url('/profile.php'); ?>">Update now</a> <a href="#" onclick="javascript:pauseProfileNudge('ht_profile_nudge_linemanager');"><small>I'll do it later</small></a><br>
	<?php
			echo $after_widget; 

		elseif (!get_user_meta($userid,'wp_user_avatar',true) &&  !isset($_COOKIE['ht_profile_nudge_photo']) && $photo=='on'):
			echo $before_widget; ?>
			<h3><?php echo  $current_user->first_name; ?></h3>
			<p>There is no photo of you on your staff profile. Do you want to add one now?</p>
			<a class="btn btn-primary" href="<?php echo admin_url('/profile.php'); ?>">Update now</a> <a href="#" onclick="javascript:pauseProfileNudge('ht_profile_nudge_photo');"><small>I'll do it later</small></a><br>
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
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />

          <input id="<?php echo $this->get_field_id('telephone'); ?>" name="<?php echo $this->get_field_name('telephone'); ?>" type="checkbox" <?php checked((bool) $instance['telephone'], true ); ?> />
          <label for="<?php echo $this->get_field_id('telephone'); ?>"><?php _e('Telephone number'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('mobile'); ?>" name="<?php echo $this->get_field_name('mobile'); ?>" type="checkbox" <?php checked((bool) $instance['mobile'], true ); ?> />
          <label for="<?php echo $this->get_field_id('mobile'); ?>"><?php _e('Mobile phone'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('job_title'); ?>" name="<?php echo $this->get_field_name('job_title'); ?>" type="checkbox" <?php checked((bool) $instance['job_title'], true ); ?> />
          <label for="<?php echo $this->get_field_id('job_title'); ?>"><?php _e('Job title'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('grade'); ?>" name="<?php echo $this->get_field_name('grade'); ?>" type="checkbox" <?php checked((bool) $instance['grade'], true ); ?> />
          <label for="<?php echo $this->get_field_id('grade'); ?>"><?php _e('Grade'); ?></label> <br>
          
          <input id="<?php echo $this->get_field_id('team'); ?>" name="<?php echo $this->get_field_name('team'); ?>" type="checkbox" <?php checked((bool) $instance['team'], true ); ?> />
          <label for="<?php echo $this->get_field_id('team'); ?>"><?php _e('Team'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('key_skills'); ?>" name="<?php echo $this->get_field_name('key_skills'); ?>" type="checkbox" <?php checked((bool) $instance['key_skills'], true ); ?> />
          <label for="<?php echo $this->get_field_id('key_skills'); ?>"><?php _e('Skills and experience'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('bio'); ?>" name="<?php echo $this->get_field_name('bio'); ?>" type="checkbox" <?php checked((bool) $instance['bio'], true ); ?> />
          <label for="<?php echo $this->get_field_id('bio'); ?>"><?php _e('Biography'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('linemanager'); ?>" name="<?php echo $this->get_field_name('linemanager'); ?>" type="checkbox" <?php checked((bool) $instance['linemanager'], true ); ?> />
          <label for="<?php echo $this->get_field_id('linemanager'); ?>"><?php _e('Line manager'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('photo'); ?>" name="<?php echo $this->get_field_name('photo'); ?>" type="checkbox" <?php checked((bool) $instance['photo'], true ); ?> />
          <label for="<?php echo $this->get_field_id('photo'); ?>"><?php _e('Profile photo'); ?></label> <br>

        </p>

        <?php 
    }

}

add_action('widgets_init', create_function('', 'return register_widget("htProfileNudge");'));

?>
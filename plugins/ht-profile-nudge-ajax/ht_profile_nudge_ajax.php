<?php
/*
Plugin Name: HT Profile Nudge AJAX
Plugin URI: http://www.helpfultechnology.com
Description: Widget to display reminders to complete the staff profile using AJAX
Author: Luke Oatham
Version: 1.1
Author URI: http://www.helpfultechnology.com
*/

class htProfileNudgeajax extends WP_Widget {

	function __construct() {
		
		parent::__construct(
			'htProfileNudgeajax',
			__( 'HT Profile Nudge AJAX' , 'govintranet'),
			array( 'description' => __( 'Display reminders to complete missing staff profile entries using AJAX.' , 'govintranet') )
		);   
    }

    function widget($args, $instance) {
        extract( $args );
        global $current_user;
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
        $widget_id = $id. "-" . $this->number;
        $first_last_name = ($instance['first_last_name']);
        $telephone = ($instance['telephone']);
        $mobile = ($instance['mobile']);
        $job_title = apply_filters('widget_title', $instance['job_title']);
        $grade = ($instance['grade']);
        $team = ($instance['team']);
        $key_skills = ($instance['key_skills']);
        $aboutbio = ($instance['bio']); 
        $linemanager = ($instance['linemanager']);
        $photo = ($instance['photo']);

        $path = plugin_dir_url( __FILE__ );

        wp_enqueue_script( 'pauseNudgeajax', $path.'pauseNudgeajax.js' );
        $protocol = isset( $_SERVER["HTTPS"]) ? 'https://' : 'http://';
        $params = array(
        'ajaxurl' => admin_url( 'admin-ajax.php', $protocol ),
		'before_widget' => stripcslashes($before_widget),
		'after_widget' => stripcslashes($after_widget),
        'user_id' => $user_id,
        'widget_id' => $widget_id,
        'first_last_name' => $first_last_name,
        'telephone' => $telephone,
        'mobile' => $mobile,
        'job_title' => $job_title,
        'grade' => $grade,
        'team' => $team,
        'key_skills' => $key_skills,
        'bio' => $aboutbio,
        'linemanager' => $linemanager,
        'photo' => $photo,
        );
        wp_localize_script( 'pauseNudgeajax', 'pauseNudgeajax', $params );
        
		echo "<div id='ht_profilenudge_".$widget_id."' class='ht_profilenudge'></div>";

    }


    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['first_last_name'] = strip_tags($new_instance['first_last_name']);
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

          <input id="<?php echo $this->get_field_id('first_last_name'); ?>" name="<?php echo $this->get_field_name('first_last_name'); ?>" type="checkbox" <?php checked((bool) $instance['first_last_name'], true ); ?> />
          <label for="<?php echo $this->get_field_id('first_last_name'); ?>"><?php _e('First and last name','govintranet'); ?></label> <br>

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

add_action( 'wp_ajax_ht_profile_nudge_ajax_show', 'ht_profile_nudge_ajax_show' );
add_action( 'wp_ajax_nopriv_ht_profile_nudge_ajax_show', 'ht_profile_nudge_ajax_show' );
function ht_profile_nudge_ajax_show() {
	$before_widget = stripcslashes($_POST['before_widget']);
	$after_widget = stripcslashes($_POST['after_widget']);
	$user_id = $_POST['user_id'];
	$widget_id = $_POST['widget_id'];
	$first_last_name = $_POST['first_last_name'];
	$telephone = $_POST['telephone'];
	$mobile = $_POST['mobile'];
	$job_title = $_POST['job_title'];
	$grade = $_POST['grade'];
	$team = $_POST['team'];
	$key_skills = $_POST['key_skills'];
	$aboutbio = $_POST['bio'];
	$linemanager = $_POST['linemanager'];
	$photo = $_POST['photo'];
    $response = new WP_Ajax_Response;
	$html = "";
	global $current_user;
	$current_user = wp_get_current_user();
	if ($current_user->ID):
		$userid = $current_user->ID; 
		if ( ( trim( $current_user->user_firstname ) == "" || trim( $current_user->user_lastname ) == "" ) &&  !isset($_COOKIE['ht_profile_nudge_first_last_name']) && $first_last_name=='on'):
			/******************************************
			*
			* UPDATE FIRST AND LAST NAMES
			*
			*******************************************/
			$html.= $before_widget; 
			$nonce = wp_create_nonce ('update_profile_add_first_last_name_'.$widget_id);
			$html.= "
			<div id='ht_profilenudge_success_".$widget_id."'>
			<h3>";
			$html.= __('Please introduce yourself' , 'govintranet' );
			$html.="</h3>
			<p>";
			$html.= __('We noticed that your name is missing from your staff profile. You can add it now if you like?','govintranet');
			$html.="</p>
			<form id='update-profile-form' class='form-horizontal' role='form' name='update-profile' method='post'>
			<label for='first_name'>" . __('First name','govintranet') . "</label>
			<input class='form-control' name='first_name' id='first_name_".$widget_id."' value='".trim( $current_user->user_firstname ) ."' /><br>
			<label for='last_name'>" . __('Last name','govintranet') . "</label>
			<input class='form-control' name='last_name' id='last_name_".$widget_id."' value='".trim( $current_user->user_lastname ) ."' /><br>
			<a  id='profilebutton'  onclick='javascript:update_profile_action_add_first_last_name();' class='profilebutton btn btn-primary'>" . __('Update now','govintranet') . "</a> <a class='linkpointer' onclick='javascript:pauseProfileNudgeAJAX(\"ht_profile_nudge_first_last_name\");'><small>" . __('I\'ll do it later','govintranet') . "</small></a><br>
			<input type='hidden' name='_wpnonce_add_first_last_name_".$widget_id."' value='" . $nonce ."' />
			</form>	
			<div id='ht_profilenudge_errmsg_".$widget_id."'></div>
			</div>
			";	
			$html.= $after_widget; 	
		elseif (!get_user_meta($userid,'user_telephone',true) &&  !isset($_COOKIE['ht_profile_nudge_telephone']) && $telephone=='on'):
			/******************************************
			*
			* UPDATE TELEPHONE NUMBER
			*
			*******************************************/
			$html.= $before_widget; 
			$nonce = wp_create_nonce ('update_profile_add_phone_'.$widget_id);
			$html.= "
			<div id='ht_profilenudge_success_".$widget_id."'>
			<h3>";
			$html.= sprintf( __('%s, your number is not listed' , 'govintranet' ) , $current_user->user_firstname );
			$html.="</h3>
			<p>";
			$html.= __('We noticed that your telephone number is missing from your staff profile. You can add it now if you like?','govintranet');
			$html.="</p>
			<form id='update-profile-form' class='form-horizontal' role='form' name='update-profile' method='post'>
			<label for='phone'>" . __('Telephone','govintranet') . "</label>
			<input class='form-control' name='phone' id='phone_".$widget_id."' placeholder='" . __('e.g. 0203 459 8765','govintranet') . "' /><br>
			<a  id='profilebutton'  onclick='javascript:update_profile_action_add_phone();' class='profilebutton btn btn-primary'>" . __('Update now','govintranet') . "</a> <a class='linkpointer' onclick='javascript:pauseProfileNudgeAJAX(\"ht_profile_nudge_telephone\");'><small>" . __('I\'ll do it later','govintranet') . "</small></a><br>
			<input type='hidden' name='_wpnonce_add_phone_".$widget_id."' value='" . $nonce ."' />
			</form>	
			<div id='ht_profilenudge_errmsg_".$widget_id."'></div>
			</div>
			";	
			$html.= $after_widget; 	
		elseif (!get_user_meta($userid,'user_mobile',true) &&  !isset($_COOKIE['ht_profile_nudge_mobile']) && $mobile=='on'):
			/******************************************
			*
			* UPDATE MOBILE NUMBER
			*
			*******************************************/
			$html.= $before_widget; 
			$nonce = wp_create_nonce ('update_profile_add_mobile_'.$widget_id);
			$html.= "
			<div id='ht_profilenudge_success_".$widget_id."'>
			<h3>";
			$html.= sprintf( __('%s, did you know?' , 'govintranet' ) , $current_user->user_firstname );
			$html.="</h3>
			<p>";
			$html.= __('Your mobile number is missing. You can add it now if you like?','govintranet');
			$html.="</p>
			<form id='update-profile-form' class='form-horizontal' role='form' name='update-profile' method='post'>
			<label for='mobile'>" . __('Mobile','govintranet') . "</label>
			<input class='form-control' name='mobile' id='mobile_".$widget_id."' placeholder='" . __('e.g. 07771 999 888' , 'govintranet') . "' /><br>
			<a  id='profilebutton'  onclick='javascript:update_profile_action_add_mobile();' class='profilebutton btn btn-primary'>" . __('Update now','govintranet') . "</a> <a class='linkpointer'  onclick='javascript:pauseProfileNudgeAJAX(\"ht_profile_nudge_mobile\");'><small>" . __('I\'ll do it later','govintranet') . "</small></a><br>
			<input type='hidden' name='_wpnonce_add_mobile_".$widget_id."' value='" . $nonce ."' />
			</form>	
			<div id='ht_profilenudge_errmsg_".$widget_id."'></div>
			</div>
			";	
			$html.= $after_widget; 	
		elseif (!get_user_meta($userid,'user_job_title',true) &&  !isset($_COOKIE['ht_profile_nudge_job_title']) && $job_title=='on'):
			/******************************************
			*
			* UPDATE JOB TITLE
			*
			*******************************************/
			$html.= $before_widget; 
			$nonce = wp_create_nonce ('update_profile_add_job_title_'.$widget_id);
			$html.= "
			<div id='ht_profilenudge_success_".$widget_id."'>
			<h3>";
			$html.= sprintf( __('%s, did you know?' , 'govintranet' ) , $current_user->user_firstname );
			$html.="</h3>
			<p>";
			$html.= __('Your job title is missing from your staff profile. You can add it now if you like?','govintranet');
			$html.="</p>
			<form id='update-profile-form' class='form-horizontal' role='form' name='update-profile' method='post'>
			<label for='job_title'>" . __('Job title','govintranet') . "</label>
			<input class='form-control' name='job_title' id='job_title_".$widget_id."' placeholder='" . __('e.g. Data entry clerk' , 'govintranet') . "' /><br>
			<a  id='profilebutton'  onclick='javascript:update_profile_action_add_job_title();' class='profilebutton btn btn-primary'>" . __('Update now','govintranet') . "</a> <a class='linkpointer'  onclick='javascript:pauseProfileNudgeAJAX(\"ht_profile_nudge_job_title\");'><small>" . __('I\'ll do it later','govintranet') . "</small></a><br>
			<input type='hidden' name='_wpnonce_add_job_title_".$widget_id."' value='" . $nonce ."' />
			</form>	
			<div id='ht_profilenudge_errmsg_".$widget_id."'></div>
			</div>
			";	
			$html.= $after_widget; 	
		elseif (!get_user_meta($userid,'user_grade',true) &&  !isset($_COOKIE['ht_profile_nudge_grade']) && $grade=='on'):
			/******************************************
			*
			* UPDATE GRADE
			*
			*******************************************/
			$html.= $before_widget; 
			$nonce = wp_create_nonce ('update_profile_add_grade_'.$widget_id);
			$html.= "
			<div id='ht_profilenudge_success_".$widget_id."'>
			<h3>";
			$html.= sprintf( __('Pssst, %s' , 'govintranet' ) , $current_user->user_firstname );
			$html.="</h3>
			<p>";
			$html.= __('Please enter your grade so that you appear correctly in team listings.','govintranet');
			$html.="</p>
			<form id='update-profile-form' class='form-horizontal' role='form' name='update-profile' method='post'>
			<label for='grade'>" . __('Grade','govintranet') . "</label>
			<select class='form-control' name='grade' id='grade_".$widget_id."'>
			<option value='0'>" . __('Choose your grade','govintranet') . "</option>";
			$terms = get_terms('grade',array('hide_empty'=>false,'orderby'=>'slug','order'=>'ASC',"parent"=>0));
			if ($terms) {
		  		foreach ((array)$terms as $taxonomy ) {
		  		    $themeid = $taxonomy->term_id;
		  		    $themeURL= $taxonomy->slug;		  		    			  		    
		  			$html.=  "<option value='{$themeid}'>{$taxonomy->name}</option>";
				}
			}
			$html.="
			</select><br>
			<a  id='profilebutton'  onclick='javascript:update_profile_action_add_grade();' class='profilebutton btn btn-primary'>" . __('Update now' , 'govintranet') . "</a> <a class='linkpointer'  onclick='javascript:pauseProfileNudgeAJAX(\"ht_profile_nudge_grade\");'><small>" . __('I\'ll do it later','govintranet') . "</small></a><br>
			<input type='hidden' name='_wpnonce_add_grade_".$widget_id."' value='" . $nonce ."' />
			</form>	
			<div id='ht_profilenudge_errmsg_".$widget_id."'></div>
			</div>
			";	
			$html.= $after_widget; 	
		elseif (!get_user_meta($userid,'user_team',true) &&  !isset($_COOKIE['ht_profile_nudge_team']) && $team=='on'):
			/******************************************
			*
			* UPDATE TEAM
			*
			*******************************************/
			$html.= $before_widget; 
			$nonce = wp_create_nonce ('update_profile_add_team_'.$widget_id);
			$html.= "
			<div id='ht_profilenudge_success_".$widget_id."'>
			<h3>";
			$html.= sprintf( __('%s, join a team!' , 'govintranet' ) , $current_user->user_firstname );
			$html.="</h3>
			<p>";
			$html.= __('To be listed correctly in the staff directory, please choose your team:','govintranet');
			$html.="</p>
			<form id='update-profile-form' class='form-horizontal' role='form' name='update-profile' method='post'>
			<label for='team'>" . __('Team','govintranet') . "</label>
			<select class='form-control' name='team' id='team_".$widget_id."'>
			<option value='0'>" . __('Choose your team','govintranet') . "</option>";
			$teams = get_posts('post_type=team&orderby=title&order=ASC&posts_per_page=-1');
			if ($teams) {
		  		foreach ((array)$teams as $team ) {
		  			$html.=  "<option value='".$team->ID."'>".get_the_title($team->ID)."</option>";
				}
			}
			$html.="
			</select><br>
			<a  id='profilebutton'  onclick='javascript:update_profile_action_add_team();' class='profilebutton btn btn-primary'>" . __('Update now','govintranet') . "</a> <a class='linkpointer'  onclick='javascript:pauseProfileNudgeAJAX(\"ht_profile_nudge_team\");'><small>" . __('I\'ll do it later','govintranet') . "</small></a><br>
			<input type='hidden' name='_wpnonce_add_team_".$widget_id."' value='" . $nonce ."' />
			</form>	
			<div id='ht_profilenudge_errmsg_".$widget_id."'></div>
			</div>
			";	
			$html.= $after_widget; 	
		elseif (!get_user_meta($userid,'user_key_skills',true) &&  !isset($_COOKIE['ht_profile_nudge_skills']) && $key_skills=='on'):
			/******************************************
			*
			* UPDATE SKILLS AND EXPERIENCE
			*
			*******************************************/
			$html.= $before_widget; 
			$nonce = wp_create_nonce ('update_profile_add_skills_'.$widget_id);
			$html.= "
			<div id='ht_profilenudge_success_".$widget_id."'>
			<h3>";
			$html.= sprintf( __('%s, get listed!' , 'govintranet' ) , $current_user->user_firstname );
			$html.="</h3>
			<p>";
			$html.= __('Make sure you appear in staff directory search results by adding your skills and experience.','govintranet');
			$html.="</p>
			<form id='update-profile-form' class='form-horizontal' role='form' name='update-profile' method='post'>
			<label for='skills'>" . __('Skills and experience','govintranet') . "</label>
			<textarea rows='3'  class='form-control' name='skills' id='skills_".$widget_id."' placeholder='" . __('e.g. I can help with budget codes, procurement and invoices.','govintranet') . "' /></textarea><br>
			<a  id='profilebutton'  onclick='javascript:update_profile_action_add_skills();' class='profilebutton btn btn-primary'>" . __('Update now','govintranet') . "</a> <a class='linkpointer'  onclick='javascript:pauseProfileNudgeAJAX(\"ht_profile_nudge_skills\");'><small>" . __('I\'ll do it later','govintranet') . "</small></a><br>
			<input type='hidden' name='_wpnonce_add_skills_".$widget_id."' value='" . $nonce ."' />
			</form>	
			<div id='ht_profilenudge_errmsg_".$widget_id."'></div>
			</div>
			";	
			$html.= $after_widget; 	
		elseif ( !get_user_meta($userid,'description',true) && !isset($_COOKIE['ht_profile_nudge_bio']) && $aboutbio == 'on' ):
			/******************************************
			*
			* UPDATE BIO
			*
			*******************************************/
			$html.= $before_widget; 
			$nonce = wp_create_nonce ('update_profile_add_bio_'.$widget_id);
			$html.= "
			<div id='ht_profilenudge_success_".$widget_id."'>
			<h3>";
			$html.= sprintf( __('About %s' , 'govintranet' ) , $current_user->user_firstname );
			$html.="</h3>
			<p>";
			$html.= __('Your staff bio is empty. Do you want to tell us a little about yourself now?','govintranet');
			$html.="</p>
			<form id='update-profile-form' class='form-horizontal' role='form' name='update-profile' method='post'>
			<label for='bio'>" . __('Short bio' , 'govintranet') . "</label>
			<textarea rows='3' class='form-control' name='bio' id='bio_".$widget_id."' placeholder='" . _x('I have worked in the Civil Service for 3 years...' , 'Example biography' , 'govintranet' ) . "' /></textarea><br>
			<a  id='profilebutton'  onclick='javascript:update_profile_action_add_bio();' class='profilebutton btn btn-primary'>" . __('Update now','govintranet') . "</a> <a class='linkpointer'  onclick='javascript:pauseProfileNudgeAJAX(\"ht_profile_nudge_bio\");'><small>" . __('I\'ll do it later','govintranet') . "</small></a><br>
			<input type='hidden' name='_wpnonce_add_bio_".$widget_id."' value='" . $nonce ."' />
			</form>	
			<div id='ht_profilenudge_errmsg_".$widget_id."'></div>
			</div>
			";	
			$html.= $after_widget; 	
		elseif ( !get_user_meta($userid,'user_line_manager',true) && !isset($_COOKIE['ht_profile_nudge_linemanager']) && $linemanager == 'on' ):
			/******************************************
			*
			* UPDATE LINE MANAGER
			*
			*******************************************/
			$html.= $before_widget; 
			$nonce = wp_create_nonce ('update_profile_add_line_manager_'.$widget_id);
			$userurl = "";
			$userurl = get_author_posts_url( $current_user->ID); 
			if (function_exists('bp_activity_screen_index')){ // if using BuddyPress - link to the members page
				$userurl=str_replace('/author', '/members', $userurl."edit/"); }
			elseif (function_exists('bbp_get_displayed_user_field')){ // if using bbPress - link to the staff page
				$userurl=str_replace('/author', '/staff', $userurl."edit/");
			} else {
				$userurl = admin_url('/profile.php');
			}    
			$html.= "
			<div id='ht_profilenudge_success_".$widget_id."'>
			<h3>" . $current_user->user_firstname . "</h3>
			<p>";
			$html.= __('Please set your line manager in your staff profile so that you appear correctly in the staff directory.','govintranet');
			$html.="</p>
			<a  id='profilebutton' href='".$userurl."' class='profilebutton btn btn-primary'>" . __('Edit my profile','govintranet') . "</a> <a class='linkpointer'  onclick='javascript:pauseProfileNudgeAJAX(\"ht_profile_nudge_linemanager\");'><small>" . __('I\'ll do it later','govintranet') . "</small></a><br>
			</div>
			";	
			$html.= $after_widget; 	
		elseif ( !get_user_meta($userid,'wp_user_avatar',true) && !get_user_meta($userid,'simple_local_avatar',true) && !isset($_COOKIE['ht_profile_nudge_photo']) && $photo == 'on' ):
			/******************************************
			*
			* UPDATE AVATAR
			*
			*******************************************/
			$html.= $before_widget; 
			$userurl = "";
			$userurl = get_author_posts_url( $current_user->ID); 
			if (function_exists('bp_activity_screen_index')){ // if using BuddyPress - link to the members page
				$userurl=str_replace('/author', '/members', $userurl."edit/"); }
			elseif (function_exists('bbp_get_displayed_user_field')){ // if using bbPress - link to the staff page
				$userurl=str_replace('/author', '/staff', $userurl."edit/");
			} else {
				$userurl = admin_url('/profile.php');
			}    
			$html.= "
			<div id='ht_profilenudge_success_".$widget_id."'>
			<h3>" . $current_user->user_firstname . "</h3>
			<p>" . __('There is no photo of you on your staff profile. Do you want to add one now?','govintranet') . "</p>
			<a  id='profilebutton' href='".$userurl."' class='profilebutton btn btn-primary'>" . __('Edit my profile' , 'govintrnaet') . "</a> <a class='linkpointer'  onclick='javascript:pauseProfileNudgeAJAX(\"ht_profile_nudge_photo\");'><small>" . __('I\'ll do it later','govintranet') . "</small></a><br>
			</div>
			";	
			$html.= $after_widget; 	
		endif;
	endif;
    if( $html ) {
        // Request successful
        $response->add( array(
            'data' => 'success',
            'supplemental' => array(
                'message' => $html
            ),
        ) );
    } else {
        // Request failed
        $response->add( array(
            'data' => 'error',
            'supplemental' => array(
                'message' => 'an error occurred'
            ),
        ) );
    }
    $response->send();
    exit();
}

add_action( 'wp_ajax_ht_profile_nudge_ajax_action_add_first_last_name', 'ht_profile_nudge_ajax_action_add_first_last_name' );
function ht_profile_nudge_ajax_action_add_first_last_name() {
	$nonce = $_POST['nonce']; 	
	$itext = $_POST['itext']; 
	$itext2 = $_POST['itext2']; 
	$widget_id = $_POST['widget_id'];
	global $current_user;
	$success = false;
	$current_user = wp_get_current_user();
	if ($current_user->ID) $userid = $current_user->ID;
	if ( ! wp_verify_nonce( $nonce,  'update_profile_add_first_last_name_'.$widget_id ) ) {
	    // This nonce is not valid.
	    $html =  __("Security check - there is something wrong","govintranet") ; 
	} else {
	    // The nonce was valid.
	    // Do stuff here.
			$response = new WP_Ajax_Response;
			$userid = $_POST['userid'];
			$current_user = wp_get_current_user();
			$current_userid = $current_user->ID; 
			//
			if ($itext=='' || $itext2==''){
				$html = __('Enter your first and last name','govintranet');
			} elseif ($userid!=$current_userid){
			    $html =  __("Security check - can\'t check your identity","govintranet") ; 	
			} else {
				$itext = sanitize_text_field($itext);
			    update_user_meta($current_userid,'first_name',$itext, ''); 
				$itext2 = sanitize_text_field($itext2);
			    update_user_meta($current_userid,'last_name',$itext2, ''); 
				$html = __('<strong>Updated.</strong> Thank you','govintranet')  . ' <span class="dashicons dashicons-smiley"></span>';
				$success = true;
			}
	}
   if( $success  ) {
        // Request successful
        $response->add( array(
            'data' => 'success',
            'supplemental' => array(
                'message' => '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>' . $html . '</div>',
            ),
        ) );
    } else {
        // Request failed
        $response->add( array(
            'data' => 'error',
            'supplemental' => array(
                'message' => '<div class="alert alert-danger">' . $html . '</div>',
            ),
        ) );
    }
    $response->send();
    exit();
}

add_action( 'wp_ajax_ht_profile_nudge_ajax_action_add_phone', 'ht_profile_nudge_ajax_action_add_phone' );
function ht_profile_nudge_ajax_action_add_phone() {
	$nonce = $_POST['nonce']; 	
	$itext = $_POST['itext']; 
	$widget_id = $_POST['widget_id'];
    $response = new WP_Ajax_Response;
	global $current_user;
	$success = false;
	$current_user = wp_get_current_user();
	if ($current_user->ID) $userid = $current_user->ID;
	if ( ! wp_verify_nonce( $nonce,  'update_profile_add_phone_'.$widget_id ) ) {
	    // This nonce is not valid.
	    $html =  __("Security check - there is something wrong","govintranet") ; 
	} else {
	    // The nonce was valid.
	    // Do stuff here.
			$userid = $_POST['userid'];
			$current_user = wp_get_current_user();
			$current_userid = $current_user->ID; 
			//
			if ($itext==''){
				$html = __('Enter your telephone number','govintranet');
			} elseif ($userid!=$current_userid){
			    $html =  __("Security check - can\'t check your identity","govintranet") ; 	
			} else {
				$itext = sanitize_text_field($itext);
			    update_user_meta($current_userid,'user_telephone',$itext, ''); 
				$html = __('<strong>Updated.</strong> Thank you','govintranet') . ' <span class="dashicons dashicons-smiley"></span>';
				$success = true;
			}
	}
   if( $success  ) {
        // Request successful
        $response->add( array(
            'data' => 'success',
            'supplemental' => array(
                'message' => '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>' . $html . '</div>',
            ),
        ) );
    } else {
        // Request failed
        $response->add( array(
            'data' => 'error',
            'supplemental' => array(
                'message' => '<div class="alert alert-danger">' . $html . '</div>',
            ),
        ) );
    }
    $response->send();
    exit();
}

add_action( 'wp_ajax_ht_profile_nudge_ajax_action_add_mobile', 'ht_profile_nudge_ajax_action_add_mobile' );
function ht_profile_nudge_ajax_action_add_mobile() {
	$nonce = $_POST['nonce']; 	
	$itext = $_POST['itext']; 
	$widget_id = $_POST['widget_id'];
	global $current_user;
	$success = false;
	$current_user = wp_get_current_user();
	if ($current_user->ID) $userid = $current_user->ID;
	if ( ! wp_verify_nonce( $nonce,  'update_profile_add_mobile_'.$widget_id ) ) {
	    // This nonce is not valid.
	    $html =  __("Security check - there is something wrong","govintranet") ; 
	} else {
	    // The nonce was valid.
	    // Do stuff here.
			$response = new WP_Ajax_Response;
			$userid = $_POST['userid'];
			$current_user = wp_get_current_user();
			$current_userid = $current_user->ID; 
			//
			if ($itext==''){
				$html = __('Enter your mobile number','govintranet');
			} elseif ($userid!=$current_userid){
			    $html =  __("Security check - can\'t check your identity","govintranet") ; 	
			} else {
				$itext = sanitize_text_field($itext);
			    update_user_meta($current_userid,'user_mobile',$itext, ''); 
				$html = __('<strong>Updated.</strong> Thank you','govintranet') . ' <span class="dashicons dashicons-smiley"></span>';
				$success = true;
			}
	}
   if( $success  ) {
        // Request successful
        $response->add( array(
            'data' => 'success',
            'supplemental' => array(
                'message' => '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>' . $html . '</div>',
            ),
        ) );
    } else {
        // Request failed
        $response->add( array(
            'data' => 'error',
            'supplemental' => array(
                'message' => '<div class="alert alert-danger">' . $html . '</div>',
            ),
        ) );
    }
    $response->send();
    exit();
}


add_action( 'wp_ajax_ht_profile_nudge_ajax_action_add_job_title', 'ht_profile_nudge_ajax_action_add_job_title' );
function ht_profile_nudge_ajax_action_add_job_title() {
	$nonce = $_POST['nonce']; 	
	$itext = $_POST['itext']; 
	$widget_id = $_POST['widget_id'];
	global $current_user;
	$success = false;
	$current_user = wp_get_current_user();
	if ($current_user->ID) $userid = $current_user->ID;
	if ( ! wp_verify_nonce( $nonce,  'update_profile_add_job_title_'.$widget_id ) ) {
	    // This nonce is not valid.
	    $html =  __("Security check - there is something wrong","govintranet") ; 
	} else {
	    // The nonce was valid.
	    // Do stuff here.
			$response = new WP_Ajax_Response;
			$userid = $_POST['userid'];
			$current_user = wp_get_current_user();
			$current_userid = $current_user->ID; 
			//
			if ($itext==''){
				$html = __('Enter your job title','govintranet');
			} elseif ($userid!=$current_userid){
			    $html =  __("Security check - can\'t check your identity","govintranet") ; 	
			} else {
				$itext = sanitize_text_field($itext);
			    update_user_meta($current_userid,'user_job_title',$itext, ''); 
				$html = __('<strong>Updated.</strong> Thank you','govintranet')  . ' <span class="dashicons dashicons-smiley"></span>';
				$success = true;
			}
	}
   if( $success  ) {
        // Request successful
        $response->add( array(
            'data' => 'success',
            'supplemental' => array(
                'message' => '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>' . $html . '</div>',
            ),
        ) );
    } else {
        // Request failed
        $response->add( array(
            'data' => 'error',
            'supplemental' => array(
                'message' => '<div class="alert alert-danger">' . $html . '</div>',
            ),
        ) );
    }
    $response->send();
    exit();
}

add_action( 'wp_ajax_ht_profile_nudge_ajax_action_add_grade', 'ht_profile_nudge_ajax_action_add_grade' );
function ht_profile_nudge_ajax_action_add_grade() {
	$nonce = $_POST['nonce']; 	
	$itext = $_POST['itext']; 
	$widget_id = $_POST['widget_id'];
	global $current_user;
	$success = false;
	$current_user = wp_get_current_user();
	if ($current_user->ID) $userid = $current_user->ID;
	if ( ! wp_verify_nonce( $nonce,  'update_profile_add_grade_'.$widget_id ) ) {
	    // This nonce is not valid.
	    $html =  __("Security check - there is something wrong","govintranet") ; 
	} else {
	    // The nonce was valid.
	    // Do stuff here.
			$response = new WP_Ajax_Response;
			$userid = $_POST['userid'];
			$current_user = wp_get_current_user();
			$current_userid = $current_user->ID; 
			//
			if ($itext==0){
				$html = __('Enter your grade','govintranet');
			} elseif ($userid!=$current_userid){
			    $html =  __("Security check - can\'t check your identity","govintranet") ; 	
			} else {
				$itext = sanitize_text_field($itext);
			    update_user_meta($current_userid,'user_grade',$itext, ''); 
				$html = __('<strong>Updated.</strong> Thank you','govintranet')  . ' <span class="dashicons dashicons-smiley"></span>';
				$success = true;
			}
	}
   if( $success  ) {
        // Request successful
        $response->add( array(
            'data' => 'success',
            'supplemental' => array(
                'message' => '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>' . $html . '</div>',
            ),
        ) );
    } else {
        // Request failed
        $response->add( array(
            'data' => 'error',
            'supplemental' => array(
                'message' => '<div class="alert alert-danger">' . $html . '</div>',
            ),
        ) );
    }
    $response->send();
    exit();
}

add_action( 'wp_ajax_ht_profile_nudge_ajax_action_add_team', 'ht_profile_nudge_ajax_action_add_team' );
function ht_profile_nudge_ajax_action_add_team() {
	$nonce = $_POST['nonce']; 	
	$itext = $_POST['itext']; 
	$widget_id = $_POST['widget_id'];
	global $current_user;
	$success = false;
	$current_user = wp_get_current_user();
	if ($current_user->ID) $userid = $current_user->ID;
	if ( ! wp_verify_nonce( $nonce,  'update_profile_add_team_'.$widget_id ) ) {
	    // This nonce is not valid.
	    $html =  __("Security check - there is something wrong","govintranet") ; 
	} else {
	    // The nonce was valid.
	    // Do stuff here.
			$response = new WP_Ajax_Response;
			$userid = $_POST['userid'];
			$current_user = wp_get_current_user();
			$current_userid = $current_user->ID; 
			//
			if ($itext==0){
				$html = __('Enter your team','govintranet');
			} elseif ($userid!=$current_userid){
			    $html =  __("Security check - can\'t check your identity","govintranet") ; 	
			} else {
				delete_user_meta($current_userid,'user_team'); 
			    update_user_meta($current_userid,'user_team',array($itext), ''); 
				$html = __('<strong>Updated.</strong> Thank you','govintranet')  . ' <span class="dashicons dashicons-smiley"></span>';
				$success = true;
			}
	}
   if( $success  ) {
        // Request successful
        $response->add( array(
            'data' => 'success',
            'supplemental' => array(
                'message' => '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>' . $html . '</div>',
            ),
        ) );
    } else {
        // Request failed
        $response->add( array(
            'data' => 'error',
            'supplemental' => array(
                'message' => '<div class="alert alert-danger">' . $html . '</div>',
            ),
        ) );
    }
    $response->send();
    exit();
}

add_action( 'wp_ajax_ht_profile_nudge_ajax_action_add_skills', 'ht_profile_nudge_ajax_action_add_skills' );
function ht_profile_nudge_ajax_action_add_skills() {
	$nonce = $_POST['nonce']; 	
	$itext = $_POST['itext']; 
	$widget_id = $_POST['widget_id'];
	global $current_user;
	$success = false;
	$current_user = wp_get_current_user();
	if ($current_user->ID) $userid = $current_user->ID;
	if ( ! wp_verify_nonce( $nonce,  'update_profile_add_skills_'.$widget_id ) ) {
	    // This nonce is not valid.
	    $html =  __("Security check - there is something wrong","govintranet") ; 
	} else {
	    // The nonce was valid.
	    // Do stuff here.
			$response = new WP_Ajax_Response;
			$userid = $_POST['userid'];
			$current_user = wp_get_current_user();
			$current_userid = $current_user->ID; 
			//
			if ($itext==''){
				$html = __('Enter your skills and experience','govintranet');
			} elseif ($userid!=$current_userid){
			    $html =  __("Security check - can\'t check your identity","govintranet") ; 	
			} else {
				$itext = sanitize_text_field($itext);
			    update_user_meta($current_userid,'user_key_skills',$itext, ''); 
				$html = __('<strong>Updated.</strong> Thank you','govintranet')  . ' <span class="dashicons dashicons-smiley"></span>';
				$success = true;
			}
	}
   if( $success  ) {
        // Request successful
        $response->add( array(
            'data' => 'success',
            'supplemental' => array(
                'message' => '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>' . $html . '</div>',
            ),
        ) );
    } else {
        // Request failed
        $response->add( array(
            'data' => 'error',
            'supplemental' => array(
                'message' => '<div class="alert alert-danger">' . $html . '</div>',
            ),
        ) );
    }
    $response->send();
    exit();
}

add_action( 'wp_ajax_ht_profile_nudge_ajax_action_add_bio', 'ht_profile_nudge_ajax_action_add_bio' );
function ht_profile_nudge_ajax_action_add_bio() {
	$nonce = $_POST['nonce']; 	
	$itext = $_POST['itext']; 
	$widget_id = $_POST['widget_id'];
	global $current_user;
	$success = false;
	$current_user = wp_get_current_user();
	if ($current_user->ID) $userid = $current_user->ID;
	if ( ! wp_verify_nonce( $nonce,  'update_profile_add_bio_'.$widget_id ) ) {
	    // This nonce is not valid.
	    $html =  __("Security check - there is something wrong","govintranet") ; 
	} else {
	    // The nonce was valid.
	    // Do stuff here.
			$response = new WP_Ajax_Response;
			$userid = $_POST['userid'];
			$current_user = wp_get_current_user();
			$current_userid = $current_user->ID; 
			//
			if ($itext==''){
				$html = __('Tell us a little more','govintranet');
			} elseif ($userid!=$current_userid){
			    $html =  __("Security check - can\'t check your identity","govintranet") ; 	
			} else {
				$itext = sanitize_text_field($itext);
			    update_user_meta($current_userid,'description',$itext, ''); 
				$html = __('<strong>Updated.</strong> Thank you','govintranet')  . ' <span class="dashicons dashicons-smiley"></span>';
				$success = true;
			}
	}
   if( $success  ) {
        // Request successful
        $response->add( array(
            'data' => 'success',
            'supplemental' => array(
                'message' => '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>' . $html . '</div>',
            ),
        ) );
    } else {
        // Request failed
        $response->add( array(
            'data' => 'error',
            'supplemental' => array(
                'message' => '<div class="alert alert-danger">' . $html . '</div>',
            ),
        ) );
    }
    $response->send();
    exit();
}

add_action('widgets_init', create_function('', 'return register_widget("htProfileNudgeajax");'));

?>
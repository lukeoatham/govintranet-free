<?php
/*
Profile Nudge Update 
*/


require_once('../../../wp-blog-header.php');

// We need to verify the nonce.
$nonce = $_POST['_wpnonce'];
$current_user = wp_get_current_user();
if ($current_user->ID) $userid = $current_user->ID;

if ( ! wp_verify_nonce( $nonce, 'update-profile_'.$userid ) ) {
    // This nonce is not valid.
    die( __('Security check - there is something wrong' ,'govintranet' )); 
} else {
    // The nonce was valid.
    // Do stuff here.
    
    if ($_POST['type']=='add-grade'){
		$userid = $_POST['userid'];
		$current_user = wp_get_current_user();
		$current_userid = $current_user->ID; 
		$usergrade = $_POST['grade']; 
		if ($usergrade==0){
			$referer = $_SERVER['HTTP_REFERER'];
			wp_redirect($referer);
		}
		if ($userid!=$current_userid){
		    die( __("Security check - can\'t check your identity.",'govintranet') ); 	
		}
	    update_user_meta($current_userid,'user_grade',$usergrade, ''); 
		$referer = $_SERVER['HTTP_REFERER'];
		wp_redirect($referer);
	}

    if ($_POST['type']=='add-team'){
		$userid = $_POST['userid'];
		$current_user = wp_get_current_user();
		$current_userid = $current_user->ID; 
		$team = $_POST['team']; 
		if ($team==0){
			$referer = $_SERVER['HTTP_REFERER'];
			wp_redirect($referer);
		}
		if ($userid!=$current_userid){
		    die( __("Security check - can\'t check your identity.",'govintranet') ); 	
		}
		delete_user_meta($current_userid,'user_team'); 
	    update_user_meta($current_userid,'user_team',array($team), ''); 
		$referer = $_SERVER['HTTP_REFERER'];
		wp_redirect($referer);
	}

    if ($_POST['type']=='add-skills'){
		$userid = $_POST['userid'];
		$current_user = wp_get_current_user();
		$current_userid = $current_user->ID; 
		$skills = $_POST['key_skills']; 
		if ($skills==''){
			$referer = $_SERVER['HTTP_REFERER'];
			wp_redirect($referer);
		}
		if ($userid!=$current_userid){
		    die( __("Security check - can\'t check your identity.",'govintranet') ); 	
		}
		$skills = sanitize_text_field($skills);
	    update_user_meta($current_userid,'user_key_skills',$skills, ''); 
		$referer = $_SERVER['HTTP_REFERER'];
		wp_redirect($referer);
	}

    if ($_POST['type']=='add-job-title'){
		$userid = $_POST['userid'];
		$current_user = wp_get_current_user();
		$current_userid = $current_user->ID; 
		$jobtitle = $_POST['job_title']; 
		if ($jobtitle==''){
			$referer = $_SERVER['HTTP_REFERER'];
			wp_redirect($referer);
		}
		if ($userid!=$current_userid){
		    die( __("Security check - can\'t check your identity.",'govintranet') ); 	
		}
		$jobtitle = sanitize_text_field($jobtitle);
	    update_user_meta($current_userid,'user_job_title',$jobtitle, ''); 
		$referer = $_SERVER['HTTP_REFERER'];
		wp_redirect($referer);
	}
	

    if ($_POST['type']=='add-bio'){
		$userid = $_POST['userid'];
		$current_user = wp_get_current_user();
		$current_userid = $current_user->ID; 
		$bio = $_POST['bio']; 
		if ($bio==''){
			$referer = $_SERVER['HTTP_REFERER'];
			wp_redirect($referer);
		}
		if ($userid!=$current_userid){
		    die( __("Security check - can\'t check your identity.",'govintranet') ); 	
		}
		$bio = sanitize_text_field($bio);
	    update_user_meta($current_userid,'description',$bio); 
		$referer = $_SERVER['HTTP_REFERER'];
		wp_redirect($referer);
	}
	
    if ($_POST['type']=='add-phone'){
		$userid = $_POST['userid'];
		$current_user = wp_get_current_user();
		$current_userid = $current_user->ID; 
		$phone = $_POST['phone']; 
		if ($phone==''){
			$referer = $_SERVER['HTTP_REFERER'];
			wp_redirect($referer);
		}
		if ($userid!=$current_userid){
		    die( __("Security check - can\'t check your identity.",'govintranet') ); 	
		}
		//$phone = sanitize_text_field($phone);
	    update_user_meta($current_userid,'user_telephone',$phone, ''); 
		$referer = $_SERVER['HTTP_REFERER'];
		wp_redirect($referer);
	}

    if ($_POST['type']=='add-mobile'){ 
		$userid = $_POST['userid'];
		$current_user = wp_get_current_user();
		$current_userid = $current_user->ID; 
		$phone = $_POST['mobile']; 
		if ($phone==''){
			$referer = $_SERVER['HTTP_REFERER'];
			wp_redirect($referer);
		}
		if ($userid!=$current_userid){
		    die( __("Security check - can\'t check your identity.",'govintranet') ); 	
		}
		$phone = sanitize_text_field($phone);
	    update_user_meta($current_userid,'user_mobile',$phone, ''); 
		$referer = $_SERVER['HTTP_REFERER'];
		wp_redirect($referer);
	}
		
	$referer = $_SERVER['HTTP_REFERER'];
	wp_redirect($referer);
	
}

?>
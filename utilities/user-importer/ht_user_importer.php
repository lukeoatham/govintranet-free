<?php
/*
Plugin Name: User Importer
Plugin URI: https://help.govintra.net
Description: Creates user accounts from CSV list
Author: Luke Oatham
Version: 0.1
Author URI: https://www.agentodigital.com
*/

if(!function_exists('str_getcsv')) {
    function str_getcsv($input, $delimiter = ",", $enclosure = '"', $escape = "\\") {
        $fp = fopen("php://memory", 'r+');
        fputs($fp, $input);
        rewind($fp);
        $data = fgetcsv($fp, null, $delimiter, $enclosure); // $escape only got added in 5.3.0
        fclose($fp);
        return $data;
    }
}

add_action('admin_menu', 'ht_userimporter_menu');

function ht_userimporter_menu() {
  add_submenu_page('tools.php','User Importer', 'User Importer', 'manage_options', 'user-importer', 'ht_userimporter_options');
}

function ht_userimporter_options() {

  if (!current_user_can('manage_options'))  {
	wp_die( __('You do not have sufficient permissions to access this page.') );
  }

  ob_start();
  
	echo "<div class='wrap'>";
	echo "<h2>" . __( ' User Importer' ) . "</h2>";
	
  if ($_REQUEST['action'] == "processimport") {
    
    $contentarray = explode("\n",$_REQUEST['rawcontent']);
    
    foreach((array)$contentarray as $f) {
    	$content[] = str_getcsv(stripslashes($f));
    }
      
	foreach((array)$content as $parsedf) {
	
	
		// [0] first_name
		// [1] last_name
		// [2] Nickname
		// [3] Team
		// [4] Email
		// [5] Phone
		// [6] Mobile
		// [7] Working pattern
		// [8] Line manager
		// [9] Grade
		// [10] Hide

		echo "<br>Processing ".$parsedf[4];
		// see if user already exists on email address 
		$user = email_exists( strtolower($parsedf[4]) );
		$user_id = 0;
		$user_login='';
		if ($user) { echo " email exists";
			$user = get_user_by('email',strtolower($parsedf[4]));
			$user_id = $user->ID; 
			$user_login = $user->user_login;
			$results.="<li>".$parsedf[4]." - email already registered</li>";
			continue;
		}
		// insert user
		if ($user_login==''){
			$user_login = $parsedf[2]; //check login name doesn't exist
			if (username_exists($user_login)) {
				$results.="<li>".$parsedf[2]." ".$user_login." - problem with username - skipping</li>";
				continue;
			}
		}
	    $userdata = array(
	        'ID' => $user_id,
	        'user_login' => $user_login,
	        'first_name' => trim($parsedf[0]),
	        'last_name' => trim($parsedf[1]),
	        'nickname' => trim($parsedf[2]),
	        'display_name' => trim($parsedf[0]) . " " . trim($parsedf[1]),
	        'user_email' => strtolower(trim($parsedf[4])),
	        'user_url' => '',
	        'user_pass' => '',
	        'show_admin_bar_front' => 0,
	    );
        $random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
        $userdata['user_pass'] = $random_password; 
        if ($user_id==0){ //print_r($userdata);
	        $user_id = wp_insert_user($userdata); //echo $user_id." inserted";
	        if ($user_id==0){
		        echo " - couldn't create";
	        } else { 
	        	echo " - created ".$user_id;
				$user = new WP_User( $user_id ); 
				$user_id = $user->ID;
				if (!$user_id){
					continue;
				}
				$user->add_cap('subscriber');
				$user->add_cap('bbp_participant');
				$results.="<li>".$parsedf[4]." ".$user_login."</li>";
			}
		}
		if ($user_id!=0){

		// if tel exists, add meta
			if ($parsedf[5]){
				add_user_meta($user_id,'user_telephone',trim($parsedf[5]));
			}
		
		// if grade, add meta
			if ($parsedf[9]){
				$grade = get_term_by("slug", $parsedf[9], "grade", OBJECT);
				add_user_meta($user_id,'user_grade',$grade->term_id);
			}

		// if status, add meta
			if ($parsedf[10]){
				add_user_meta($user_id,'user_hide',1);
			}
		
		// if team exists, add meta
			if ($parsedf[3]!=''){
				$team = $parsedf[3];
				$tterm = get_page_by_path( $team, OBJECT, 'team' );
				if ($tterm){
					$tterm_id = $tterm->ID; 
					add_user_meta($user_id,'user_team',array($tterm_id));
				} else {
				    $msg .= "<li>".$parsedf[4]." - Can't find team ".(trim($parsedf[3]))."</li>";
				}
			}
		
		
		// mobile exists, add meta
			if ($parsedf[6]){
				add_user_meta($user_id,'user_mobile',$parsedf[6]);
			}
		
		// if working pattern exists, add meta
			if ($parsedf[7]){
				add_user_meta($user_id,'user_working_pattern',trim($parsedf[7]));
			}
		
	
		}
		sleep(0.5); 	
		ob_flush();

	}

	
	echo "<h1>Summary</h1>";    	
	echo "<h2>Progress</h2>";
	echo "<ul>".$results."</ul>";
	echo "<h2>Errors</h2>";
	echo "<ul>".$msg."</ul>";
	    
  } elseif ($_REQUEST['action'] == "showinfo") {
	
  } else {
	
	echo "
		<p></p> 
		 <form method='post'>
		 	<p><label for='rawcontent'>Paste CSV file contents here:</label></p>
			<p><textarea class='widefat' rows='20' cols='50' name='rawcontent' id='rawcontent'></textarea></p>
			<p><input type='submit' value='Import content' class='button-primary' /></p>
			<input type='hidden' name='page' value='user-importer' />
			<input type='hidden' name='action' value='processimport' />
		  </form><br />
		"; 		
  }

	echo "</div>";  

 	ob_end_flush();
}

?>

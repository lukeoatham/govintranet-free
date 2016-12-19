<?php
/*
Plugin Name: Manager Importer
Plugin URI: http://www.helpfultechnology.com
Description: Assigns user managers from CSV
Author: Luke Oatham
Version: 0.1
Author URI: http://www.helpfultechnology.com
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

add_action('admin_menu', 'ht_manimporter_menu');

function ht_manimporter_menu() {
  add_submenu_page('tools.php','Manager Importer', 'Manager Importer', 'manage_options', 'man-importer', 'ht_manimporter_options');
}

function ht_manimporter_options() {

  if (!current_user_can('manage_options'))  {
	wp_die( __('You do not have sufficient permissions to access this page.') );
  }

  ob_start();
  
	echo "<div class='wrap'>";

	echo "<h2>" . __( ' Manager Importer' ) . "</h2>";
	
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
		// [10] Status

			 echo $parsedf[8] . "<br>";

		// if manager exists, add meta
		if ($parsedf[8]){
			if (email_exists(strtolower(trim($parsedf[8])))){
				$manager = get_user_by('email',strtolower(trim($parsedf[8])));
				$manager_id = $manager->ID; echo $manager_id . "<br>";
				$userx = get_user_by('email',strtolower(trim($parsedf[4])));
				$user_id = $userx->ID;
				if ( !get_user_meta($user_id,'user_line_manager',true) ) {
					add_user_meta($user_id,'user_line_manager',$manager_id);
					$results .= "<li>".$parsedf[4]." - added manager ".$parsedf[8]."</li>";
				}
			} else {
			    $msg .= "<li>".$parsedf[4]." - Can't find manager ".$parsedf[8]."</li>";
			}
		}
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
			<input type='hidden' name='page' value='man-importer' />
			<input type='hidden' name='action' value='processimport' />
		  </form><br />
		"; 		
  }

	echo "</div>";  

 	ob_end_flush();
}

?>
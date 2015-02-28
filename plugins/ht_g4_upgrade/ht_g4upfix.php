<?php
/*
Plugin Name: GovIntranet 4 upgrade fix
Plugin URI: http://www.helpfultechnology.com
Description: Upgrades old vacancy and jargonbuster post types
Author: Luke Oatham
Version: 1.0
Author URI: http://www.helpfultechnology.com
*/

add_action('admin_menu', 'ht_g4upfix_menu');



function ht_g4upfix_menu() {
  add_submenu_page('tools.php','GovIntranet 4 upgrade fix', 'GovIntranet 4 upgrade fix', 'manage_options', 'g4upfix', 'ht_g4upfix_options');
}

function ht_g4upfix_options() {

  if (!current_user_can('manage_options'))  {
	wp_die( __('You do not have sufficient permissions to access this page.') );
  }

  ob_start();
  
	echo "<div class='wrap'>";
	echo "<h2>" . __( ' GovIntranet 4 upgrade fix' ) . "</h2>";
	
  if ($_REQUEST['action'] == "processimport") {

  		global $wpdb;
		$query = $wpdb->query("UPDATE $wpdb->posts set post_type = 'vacancy' WHERE post_type = 'vacancies';");
		echo "<br>Upgraded ".$query." vacancy post types";
		$query = $wpdb->query("UPDATE $wpdb->posts set post_type = 'jargon-buster' WHERE post_type = 'glossaryitem';");
		echo "<br>Upgraded ".$query." jargon buster post types";

	echo "<h1>Finished</h1>";    	
	
  } else {
	
		echo "
		<p></p> 
		 <form method='post'>
		 	<p>This action will upgrade your vacancies and jargonbusters to GovIntranet 4.</p>
			<p><input type='submit' value='Upgrade now' class='button-primary' /></p>
			<input type='hidden' name='page' value='g4upfix' />
			<input type='hidden' name='action' value='processimport' />
		  </form><br />
		"; 		
  }

	echo "</div>";  

 	ob_end_flush();
}

?>
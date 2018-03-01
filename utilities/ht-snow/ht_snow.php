<?php
/*
Plugin Name: HT Snow
Plugin URI: https://help.govintra.net
Description: Add snow effect
Author: Luke Oatham
Version: 1.0
Author URI: https://www.agentodigital.com
*/

$showtop = get_option('options_snow_top');	
$showside = get_option('options_snow_side');	
$showfooter = get_option('options_snow_footer');	

if ( $showtop == "on" && !is_admin() ) add_action( 'wp_enqueue_scripts', 'enqueue_top_snow' );
if ( $showside == "on" && !is_admin() ) add_action( 'wp_enqueue_scripts', 'enqueue_side_snow' );
if ( $showfooter == "on" && !is_admin() ) add_action( 'wp_enqueue_scripts', 'enqueue_footer_snow' );

add_action('admin_menu', 'ht_snow_menu');

function ht_snow_menu() {
  add_submenu_page('options-general.php', __('Snow','govintranet'), __('Snow','govintranet'), 'manage_options', 'ht_snow', 'ht_snow_options');
}

function enqueue_top_snow() {
	wp_enqueue_style( 'httopsnow', plugins_url("/ht-snow/css/ht_top_snow.css"));
}        
function enqueue_side_snow() {
	wp_enqueue_style( 'htsidesnow', plugins_url("/ht-snow/css/ht_side_snow.css"));
}        
function enqueue_footer_snow() {
	wp_enqueue_style( 'htfootersnow', plugins_url("/ht-snow/css/ht_footer_snow.css"));
}        

  
function ht_snow_options(){
	echo "<div class='wrap'>";

	echo "<h2>Snow settings</h2>"; 		

	$showtop = get_option('options_snow_top');	
	$showside = get_option('options_snow_side');	
	$showfooter = get_option('options_snow_footer');	

	if ($_REQUEST['action'] == "saveoptions") {

		$showside = $_REQUEST['snow_side'];
		$showtop = $_REQUEST['snow_top'];
		$showfooter = $_REQUEST['snow_footer'];
		update_option('options_snow_side', $showside);
		update_option('options_snow_top', $showtop);
		update_option('options_snow_footer', $showfooter);
		
		echo "<p>Settings updated!</p>";
		
	}	
	echo "
		 <form method='post'>
		 	<p><label for='ptype'>Snow on</label></p>
		";			 	

		echo'<p><label class="checkbox"><input type="checkbox" name="snow_top" value="on"';
		if($showtop == "on"){ 
			echo" checked=\"checked\"";
		}
		echo'> <span class="labelForCheck">Header</span></label></p>';		

		echo'<p><label class="checkbox"><input type="checkbox" name="snow_side" value="on"';
		if($showside == "on"){ 
			echo" checked=\"checked\"";
		}
		echo'> <span class="labelForCheck">Background sidebars</span></label></p>';		

		echo'<p><label class="checkbox"><input type="checkbox" name="snow_footer" value="on"';
		if($showfooter == "on"){ 
			echo" checked=\"checked\"";
		}
		echo'> <span class="labelForCheck">Footer</span></label></p>';		

		echo "	
	 	<p></p>
		<p><input type='submit' value='Save' class='button-primary' /></p>
		<input type='hidden' name='page' value='ht_snow' />
		<input type='hidden' name='action' value='saveoptions' />
	  </form><br />
		  
	"; 					
		

	echo "</div>";  
  
}   
?>
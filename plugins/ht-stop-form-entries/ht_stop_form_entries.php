<?php
/*
Plugin Name: HT Stop GravityForms entries
Plugin URI: https://help.govintra.net
Description: Remove entries for specific forms before they are saved. Works with GravityForms 1.8 or higher.
Author: Luke Oatham
Version: 1.1
Author URI: https://www.agentodigital.com
*/

/*
Add existing form ids to form removal hooks
*/
add_action('init', 'ht_queue_entry_removals');
function ht_queue_entry_removals(){
	$stopforms = get_option('options_ht_stop_forms');
	if ( $stopforms ) foreach($stopforms as $f){
		add_action( 'gform_after_submission_' . $f, 'remove_form_entry' );
	}
}
			 	
function remove_form_entry( $entry ) {
    GFAPI::delete_entry( $entry['id'] );
}

add_action('admin_menu', 'ht_stop_entries_menu');

function ht_stop_entries_menu() {
  add_submenu_page('tools.php', __('Stop GravityForms','govintranet'), __('Stop GravityForms','govintranet'), 'manage_options', 'stop_entries', 'ht_stop_entries_options');
}

function ht_stop_entries_options() {

	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.','govintranet') );
	}	

	
	/*
	Save options if form submitted
	*/

	if ($_REQUEST['action'] == "saveoptions") {
		$nonce = $_REQUEST['nonce'];
		if ( !wp_verify_nonce($nonce, 'ht_stop_entries') ) { // not verified
			wp_die( __('You do not have sufficient permissions to access this page.','govintranet') );	
		}
		$formids = $_REQUEST['formid'];
		if ( $formids ) {
			update_option('options_ht_stop_forms', $formids);
			echo "<div id='setting-stopforms-settings_updated' class='updated settings-error notice is-dismissible'><p>Options saved</p></div>";
		}
	}


	echo "<div class='wrap'>";
	echo "<h2>" . __( 'Stop GravityForms entries' ,'govintranet') . "</h2>";

	$baseurl = site_url();

	/*
	Load array of Gravity Forms and display as form options
	*/

	global $wpdb;
	$prefix = $wpdb->prefix;
	$allforms = $wpdb->get_results("select ID, title from " . $prefix. "gf_form");
	$stopforms = get_option('options_ht_stop_forms');
	$newnonce = wp_create_nonce('ht_stop_entries');
	echo "
		<p><strong>" . __('Do not save entries for the following forms:','govintranet') . "</strong></p> 
		 <form method='post'>
		";			 	
		 	foreach($allforms as $pt){
				echo'<p><label class="checkbox"><input type="checkbox" name="formid[]" value="'. $pt->ID .'"';
				if(in_array($pt->ID, $stopforms)){ 
					echo" checked=\"checked\"";
				}
				echo'> <span class="labelForCheck">['. $pt->ID . "] " . $pt->title .'</span></label></p>';
		 	}
			echo "	
		 	<p></p>
			<p><input type='submit' value='" . __('Save','govintranet') . "' class='button-primary' /></p>
			<input type='hidden' name='page' value='stop_entries' />
			<input type='hidden' name='action' value='saveoptions' />
			<input type='hidden' name='nonce' value='".$newnonce."' />
		  </form><br />
		  <small>" . __('Only affects future form submissions. This will not delete existing entries.','govintranet') . "</small>
		"; 					

	echo "</div>";  
  
}

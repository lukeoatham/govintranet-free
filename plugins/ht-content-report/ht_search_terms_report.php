<?php
/*
Plugin Name: HT Search Terms Report
Plugin URI: https://help.govintra.net
Description: Excel export of content and authors
Author: Luke Oatham
Version: 1.0
Author URI: https://www.agentodigital.com
*/

add_action('admin_menu', 'ht_search_terms_report_menu');

function ht_search_terms_report_menu() {
  add_submenu_page('tools.php', __('Search Terms Report','govintranet'), __('Search Terms Report','govintranet'), 'manage_options', 'search_terms_report', 'ht_search_terms_report_options');
}

function ht_search_terms_report_options() {
	wp_enqueue_script('jquery-ui-datepicker');
	wp_register_style( 'search-terms-report-style2',  plugin_dir_url("/") . "ht-content-report/ht_content_report.css" );
	wp_enqueue_style( 'search-terms-report-style2' );
	wp_register_script( 'search-terms-report-script',  plugin_dir_url("/") . "ht-content-report/ht_content_report.js" );
	wp_enqueue_script( 'search-terms-report-script' );

	echo "<div class='wrap'>";

	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.','govintranet') );
	}

	$baseurl = site_url();
	
	global $wpdb;
	$nonce = wp_create_nonce('ht_search_terms_report');
	echo "<div class='postbox  acf-postbox normal'><div class='inside acf-fields -top'>";
	
	echo "<h2>".__('Search terms report','govintranet')."</h2> ";
	
	echo "
		<p>Leave blank for all search terms.</p>
		 <form name='search_terms_report' method='post' action='" . plugin_dir_url("/") . "ht-content-report/ht_search_terms_report_download.php'>
		 	<p><label for='numterms'>".__('Number of terms','govintranet')."</label></p>
			<p><input type='text' name='numterms' id='numterms' /></p>
		 	<p><label for='startdate'>".__('Start date','govintranet')."</label></p>
			<p><input type='text' name='startdate' id='startdatepicker' /></p>
		 	<p><label for='enddate'>".__('End date','govintranet')."</label></p>
			<p><input type='text' name='enddate' id='enddatepicker' /></p>
			<p class='clearfix'></p>
			<p><input  id='content_download_button' type='submit' value='".__('Download search terms report','govintranet')."' class='button-primary' /></p>
			<input type='hidden' name='page' value='search_terms_report' />
			<input type='hidden' name='nonce' value='".$nonce."' />
		  </form>
		  </div>
		  </div>
		  <p class='clearfix'></p>
		"; 			
	
}



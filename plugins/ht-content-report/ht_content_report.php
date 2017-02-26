<?php
/*
Plugin Name: HT Content Report
Plugin URI: http://www.helpfultechnology.com
Description: Excel export of content and authors
Author: Luke Oatham
Version: 1.0
Author URI: http://www.helpfultechnology.com
*/

add_action('admin_menu', 'ht_content_report_menu');

function ht_content_report_menu() {
	add_submenu_page('tools.php', __('Content Report','govintranet'), __('Content Report','govintranet'), 'manage_options', 'content_report', 'ht_content_report_options');
}

function ht_content_report_options() {
	wp_enqueue_script('jquery-ui-datepicker');
	wp_register_style( 'content-report-style2',  plugin_dir_url("/") . "ht-content-report/ht_content_report.css" );
	wp_enqueue_style( 'content-report-style2' );
	wp_register_script( 'content-report-script',  plugin_dir_url("/") . "ht-content-report/ht_content_report.js" );
	wp_enqueue_script( 'content-report-script' );

	echo "<div class='wrap'>";

	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.','govintranet') );
	}

	$baseurl = site_url();

	$ptargs = array( '_builtin' => false, 'public' => true, 'exclude_from_search' => false );
	$postTypes = get_post_types($ptargs, 'objects');
	$posttypeoptions = array();
	global $wpdb;
	$nonce = wp_create_nonce('ht_content_report');
	echo "<div class='postbox  acf-postbox normal'><div class='inside acf-fields -top'>";

	echo "<h2>".__('Content report','govintranet')."</h2> ";
	echo "
		 <form name='content_report' method='post' action='" . plugin_dir_url("/") . "ht-content-report/ht_content_report_download.php'>

		 	<p><label for='ptype'>".__('Content types','govintranet')."</label></p>
		";
	echo'<p><label class="checkbox cr-check"><input type="checkbox" name="ptype[]" value="page"';
	if(in_array('page', $posttypeoptions)){
		echo" checked=\"checked\"";
	}
	echo'> <span class="labelForCheck">'.__("Pages","govintranet").'</span></label></p>';
	foreach($postTypes as $pt){
		if( $pt->rewrite["slug"] != "spot" ){
			echo'<p><label class="checkbox cr-check"><input type="checkbox" name="ptype[]" value="'. $pt->query_var .'"';
			if(in_array($pt->query_var, $posttypeoptions)){
				echo" checked=\"checked\"";
			}
			echo'> <span class="labelForCheck">'. $pt->labels->name .'</span></label></p>';
		}
	}
	echo "
		 	<p class='clearfix'></p>
			<hr>
		 	<p><label for='pstat'>".__('Status','govintranet')."</label></p>";
	echo'<p><label class="checkbox cr-check"><input type="checkbox" name="pstat[]" value="publish"> <span class="labelForCheck">'.__("Published","govintranet").'</span></label></p>';
	echo'<p><label class="checkbox cr-check"><input type="checkbox" name="pstat[]" value="draft"> <span class="labelForCheck">'.__("Draft","govintranet").'</span></label></p>';
	echo'<p><label class="checkbox cr-check"><input type="checkbox" name="pstat[]" value="pending"> <span class="labelForCheck">'.__("Pending","govintranet").'</span></label></p>';
	echo'<p><label class="checkbox cr-check"><input type="checkbox" name="pstat[]" value="future"> <span class="labelForCheck">'.__("Scheduled","govintranet").'</span></label></p>
		 	<p class="clearfix"></p>
			<hr>
		 	';
	echo "
		 	<p><label for='startdate'>".__('Start date','govintranet')."</label></p>
			<p><input type='text' name='startdate' id='startdatepicker' /></p>
		 	<p><label for='enddate'>".__('End date','govintranet')."</label></p>
			<p><input type='text' name='enddate' id='enddatepicker' /></p>";
	echo '
			<div class="radio">
			  <label>
			    <input type="radio" name="dates" id="optionsRadiosDates1" value="published" checked>
			    ' . __("Date published","govintranet") . '
			  </label>
			</div>
			<div class="radio">
			  <label>
			    <input type="radio" name="dates" id="optionsRadiosDates2" value="modified">
			    ' . __("Date modified","govintranet") . '
			  </label>
			';
	echo "
			<p class='clearfix'></p>
			</div>
			<hr>
			<h3><strong>" . __('Download in parts','govintranet') . "</strong></h3>
			<p>" . __("If you have trouble with a full report, download a limited number of posts in separate parts <br>e.g. 1000 posts in parts 1 and 2, otherwise leave blank.","govintranet") . "</p>
			<p><label for='ppp'>".__('Posts per download','govintranet')."</label></p>
			<p><input name='ppp' /></p>
			<p><label for='paged'>".__('Part','govintranet')."</label></p>
			<p><input name='paged' /></p>
			<hr>
			<p><input  id='content_download_button' type='submit' value='".__('Download content report','govintranet')."' class='button-primary' /></p>
			<input type='hidden' name='page' value='content_report' />
			<input type='hidden' name='nonce' value='".$nonce."' />
		  </form>
		  </div>
		  </div>
		  <p class='clearfix'></p>
		";

	$nonce = wp_create_nonce('ht_document_report');
	echo "<div class='postbox  acf-postbox normal'><div class='inside acf-fields -top'>";
	echo "<h2>" . __("Document report","govintranet") . "</h2> ";
	echo "<p>" . __("Download a spreadsheet of all documents." , "govintranet" ). "</p>";
	echo "
		 <form name='docs_report' method='post' action='" . plugin_dir_url("/") . "ht-content-report/ht_docs_report_download.php'>
			<p><input id='doc_download_button' type='submit' value='".__('Download documents report','govintranet')."' class='button-primary' /></p>
			<input type='hidden' name='page' value='content_report' />
			<input type='hidden' name='nonce' value='".$nonce."' />
		  </form><br />

		";

	echo "</div>";
	echo "</div>";

}

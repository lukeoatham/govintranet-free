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
	wp_register_style( 'content-report-style2',  plugin_dir_url("/") . "ht-content-report/ht_content_report.css" );
	wp_enqueue_style( 'content-report-style2' );

	echo "<div class='wrap'>";

	echo "<h2>" . __( 'Content Report' ,'govintranet') . "</h2>";

	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.','govintranet') );
	}

	$baseurl = site_url();
	
	$ptargs = array( '_builtin' => false, 'public' => true, 'exclude_from_search' => false );
	$postTypes = get_post_types($ptargs, 'objects');
	$posttypeoptions = array();
	$nonce = wp_create_nonce('ht_content_report');
	echo "<h2>Posts and pages</h2> ";
	echo "
		 <form name='content_report' method='post' action='" . plugin_dir_url("/") . "ht-content-report/ht_content_report_download.php'>
		 	<p><label for='ptype'>Content types</label></p>
		";			 	
			echo'<p><label class="checkbox cr-check"><input type="checkbox" name="ptype[]" value="page"';
			if(in_array('page', $posttypeoptions)){ 
				echo" checked=\"checked\"";
			}
			echo'> <span class="labelForCheck">Pages</span></label></p>';		
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
		 	<p><label for='pstat'>Status</label></p>";
		 	echo'<p><label class="checkbox cr-check"><input type="checkbox" name="pstat[]" value="publish"> <span class="labelForCheck">Published</span></label></p>';
		 	echo'<p><label class="checkbox cr-check"><input type="checkbox" name="pstat[]" value="draft"> <span class="labelForCheck">Draft</span></label></p>';
		 	echo'<p><label class="checkbox cr-check"><input type="checkbox" name="pstat[]" value="pending"> <span class="labelForCheck">Pending</span></label></p>';
		 	echo'<p><label class="checkbox cr-check"><input type="checkbox" name="pstat[]" value="future"> <span class="labelForCheck">Scheduled</span></label></p>';
		 	echo "
			<p class='clearfix'></p>
			<p><input  id='content_download_button' type='submit' value='Download' class='button-primary' /></p>
			<input type='hidden' name='page' value='content_report' />
			<input type='hidden' name='nonce' value='".$nonce."' />
		  </form><br />
		  
		"; 			
	
	$nonce = wp_create_nonce('ht_document_report');
	echo "<h2>" . __("Documents","govintranet") . "</h2> ";
	echo "<p>" . __("Download a spreadsheet of all documents." , "govintranet" ). "</p>";
	echo "
		 <form name='docs_report' method='post' action='" . plugin_dir_url("/") . "ht-content-report/ht_docs_report_download.php'>
			<p><input id='doc_download_button' type='submit' value='Download' class='button-primary' /></p>
			<input type='hidden' name='page' value='content_report' />
			<input type='hidden' name='nonce' value='".$nonce."' />
		  </form><br />
		  
		"; 			

	echo "</div>";  

}


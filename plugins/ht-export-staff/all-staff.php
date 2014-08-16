 <?php
/*
Plugin Name: HT Export staff
Plugin URI: http://govintranetters.helpfulclients.com
Description: Enables easy spreadsheet export of users
Author: Luke Oatham
Version: 0.1
Author URI: http://www.helpfultechnology.com
*/

add_action('admin_menu', 'ht_downloadallstaff_pluginmenu');

if (!function_exists(downloadAllStaffCSV)) {

	function downloadAllStaffCSV($csv,$filename) {
		$now = gmdate('D, d M Y H:i:s') . ' GMT';
		
		header('Content-Type: application/x-msdownload');
		header('Expires: ' . $now);
		header('Content-Disposition: attachment; filename="'.$filename.'.csv"');
		header('Pragma: no-cache');
        header("Expires: 0");
        
		echo $csv;
		exit();
	}

}


if (!class_exists('downloadAllStaffCSV')) {
  class downloadAllStaffCSV {
    static function on_load() {
      add_action('plugins_loaded',array(__CLASS__,'plugins_loaded'));
      register_activation_hook(__FILE__,array(__CLASS__,'activate'));
    }
    static function activate() {
      $role = get_role('administrator');
      $role->add_cap('download_csv');
    }
    static function plugins_loaded($csvdata) {
      global $pagenow;
      if ($pagenow=='users.php' && 
          current_user_can('download_csv') && 
          isset($_GET['downloadAllStaffCSV'])  && 
          $_GET['downloadAllStaffCSV']=='1') {

			global $wpdb;
			$tdate= getdate();
			$tdate = $tdate['year']."-".$tdate['mon']."-".$tdate['mday'];
			
			//$comments = "select ID, user_login, user_email from wp_users ;";	
			
			
			$comments = "SELECT DISTINCT ID, user_login, user_email FROM wp_users order by id asc;";
			
			
			$userrows = $wpdb->get_results($comments,ARRAY_A);
				
				$topline="ID\tFirst name\tLast name\tEmail\tJob title\tPhone\tTeam\r\n";			  
			
			  if (count($userrows)>0) {
					$fieldcount = 1;  
					$outputcsvline = array();
					$outputcsvtmp=array();
			  
				  foreach($userrows as $user) {
						$fieldcount++;
						$outputcsvline=null;
						$f = $user['ID'];
						$outputcsvline[] = '"' . str_replace('"','""',$f) . '"';
						
						$f = get_user_meta($user['ID'], 'first_name',true);
						$outputcsvline[] = '"' . str_replace('"','""',$f) . '"';
						
						$f = get_user_meta($user['ID'], 'last_name',true);
						$outputcsvline[] = '"' . str_replace('"','""',$f) . '"';
						
						$f = $user['user_email'];
						$outputcsvline[] = '"' . str_replace('"','""',$f) . '"';

						$f = get_user_meta($user['ID'], 'user_job_title',true);
						$outputcsvline[] = '"' . str_replace('"','""',$f) . '"';

						$f = get_user_meta($user['ID'], 'user_telephone',true);
						$outputcsvline[] = '"' . str_replace('"','""',$f) . '"';

//xxx
						$f = get_user_meta($user['ID'], 'user_team',true);

						$temp=array();

						foreach ((array)$f as $tt){
							$sql = "select name from wp_terms where term_id = ".$tt;
							$tt = get_term($tt, 'team', OBJECT);
							$temp[] = $wpdb->get_var($sql);
						}

						$temp = implode(", ", $temp);
						$outputcsvline[] = '"' . str_replace('"','""',$temp) . '"';

						$outputcsvtmp[] = implode("\t",$outputcsvline)."";						

					}

					$outputcsv[] = implode("\r\n",$outputcsvtmp);						

					$outputcsv2 = $topline . "\r\n" . implode("\r\n",array_reverse($outputcsv)); // oldest first
					 
				  $filename = "all-staff-".date("d-m-Y");
				}

        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=".$filename.".xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo $outputcsv2;
        exit();
      }
    }
  }
  downloadAllStaffCSV::on_load();
}


function ht_downloadallstaff_pluginmenu() {
  add_users_page('Download all staff', 'Download all staff', 'edit_posts', 'ht_downloadallstaff', 'ht_downloadallstaff_plugin_options');
}

function ht_downloadallstaff_plugin_options() {

  if (!current_user_can('edit_posts'))  {
	wp_die( __('You do not have sufficient permissions to access this page.') );
  }

  
  // grab list of posts to help filter comment export (save in a variable so we can repeat it later, under the table of comments)



  echo '<div class="wrap">';
  echo "<h2>Download all staff</h2>";
	
	 
		  $filename = "comments-".date("d-m-Y");
			
		  echo "<p><i class='glyphicon glyphicon-chevron-down'></i> <a href='" . $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING'] . "&downloadAllStaffCSV=1'>Download in tab-separated Excel spreadsheet format (.xls)</a></p>";

echo "</div>";   
}

?>

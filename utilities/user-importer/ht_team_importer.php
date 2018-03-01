<?php
/*
Plugin Name: Team Importer
Plugin URI: https://help.govintra.net
Description: Creates teams from CSV list
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

add_action('admin_menu', 'ht_teamimporter_menu');

function ht_teamimporter_menu() {
  add_submenu_page('tools.php','Team Importer', 'Team Importer', 'manage_options', 'team-importer', 'ht_teamimporter_options');
}

function ht_teamimporter_options() {

  if (!current_user_can('manage_options'))  {
	wp_die( __('You do not have sufficient permissions to access this page.') );
  }

  ob_start();
  
	echo "<div class='wrap'>";
	echo "<h2>" . __( ' Team Importer' ) . "</h2>";
	
  if ($_REQUEST['action'] == "processimport") {
    
    $contentarray = explode("\n",$_REQUEST['rawcontent']);
    
    foreach((array)$contentarray as $f) {
    	$content[] = str_getcsv(stripslashes($f));
    }
      
	foreach((array)$content as $parsedf) {
	
	
		// [0] team name
		// [1] slug
		// [2] parent slug
	
		$parent_team_id=0;
		if ($parsedf[2] != ""){
			$parent_team = get_page_by_path( $parsedf[2], OBJECT, 'team'); 
			$parent_team_id = $parent_team->ID;
		}
		$post = array(
		  'post_name'      =>  sanitize_file_name( $parsedf[1] ),
		  'post_title'     => sanitize_title( $parsedf[0] ),
		  'post_status'    => 'publish',
		  'post_type'      => 'team',
		  'post_parent'    => $parent_team_id,
		  'comment_status' => 'closed',
		); 
		wp_insert_post($post);
        $results.="<li>".$parsedf[0]." ".$parsedf[1]." ".$parent_team_id."</li>";
	}
	    	
	echo "<ul>".$results."</ul>";
	    
  } elseif ($_REQUEST['action'] == "showinfo") {
	
  } else {
	
	echo "
		<p></p> 
		 <form method='post'>
		 	<p><label for='rawcontent'>Paste CSV file contents here. Format: team title,team-slug,parent-slug</label></p>
			<p><textarea class='widefat' rows='20' cols='50' name='rawcontent' id='rawcontent'></textarea></p>
			<p><input type='submit' value='Import content' class='button-primary' /></p>
			<input type='hidden' name='page' value='team-importer' />
			<input type='hidden' name='action' value='processimport' />
		  </form><br />
		"; 		
  }

	echo "</div>";  

 	ob_end_flush();
}

?>

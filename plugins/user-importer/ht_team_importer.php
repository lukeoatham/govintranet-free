<?php
/*
Plugin Name: Team Importer
Plugin URI: http://www.helpfultechnology.com
Description: Creates user accounts from CSV list
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
	screen_icon(); 
	echo "<h2>" . __( ' Team Importer' ) . "</h2>";
	
  if ($_REQUEST['action'] == "processimport") {
    
    $contentarray = explode("\n",$_REQUEST['rawcontent']);
    
    foreach((array)$contentarray as $f) {
    	$content[] = str_getcsv(stripslashes($f));
    }
      
	foreach((array)$content as $parsedf) {
	
	
		// [0] team
		// [1] parent

/*
		$k = 12;
		$other ='';
		while ( $k < 65 ){		
			$other .= $parsedf[$k];
			$k++;
		}
*/

	
		$parent_term_id=0;
		if ($parsedf[1]){
			$parent_term = get_term_by('name', $parsedf[1], 'team'); 
			$parent_term_id = $parent_term->term_id; 
		}
		
		wp_insert_term(
		  $parsedf[0], // the term 
		  'team', // the taxonomy
		  array(
		    'parent'=> $parent_term_id
		  )
		);
		sleep(0.5);
        $results.="<li>".$parsedf[0]."</li>";
	}
	    	
	echo "<ul>".$results."</ul>";
	    
  } elseif ($_REQUEST['action'] == "showinfo") {
	
  } else {
	
	echo "
		<p></p> 
		 <form method='post'>
		 	<p><label for='rawcontent'>Paste CSV file contents here:</label></p>
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

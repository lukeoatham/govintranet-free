<?php
/*
Plugin Name: (Govintra) Accessibility checker
Plugin URI: https://help.govintra.net
Description: Display accessibility status
Author: Luke Oatham
Version: 1.1
Author URI: https://www.agentodigital.com
*/

/**
 * Avoid direct calls
 */
defined('ABSPATH') or die("No direct requests for security reasons.");


add_action('admin_menu', 'ht_a11y_report_menu');

function ht_a11y_report_menu() {
	add_submenu_page('tools.php', __('Accessibility Report','govintranet'), __('Accessibility Report','govintranet'), 'manage_options', 'a11y_report', 'ht_a11y_monitor');
}

function ht_a11y_monitor() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.','govintranet') );
	}
	wp_enqueue_style( "bootstrap3", get_template_directory_uri() .  "/css/bootstrap.min.css" );
	global $wpdb;
	echo "<div class='wrap'>";
	echo "<div id='hta11ymon'>";
	echo "<h2>".__('Accessibility report','govintranet')."</h2> ";
	
	// the array for elements to be checked
	$delights = array();
	
	$link_color = get_theme_mod('link_color', '#0054ba');
	$link_visited_color = get_theme_mod('link_visited_color', '#7303aa');
	$header_text = get_theme_mod('header_textcolor', '#ffffff'); if ( substr($header_text, 0 , 1 ) != "#") $header_text="#".$header_text;
	$btn_text = get_option('options_btn_text_colour','#ffffff');
	$header_background = get_option('header_background', '#0054ba'); 
	if ( $btn_text == "#" ) $btn_text = "#ffffff";
	if ( $header_background == "#" ) $header_background = "#0054ba";
	if ( substr($header_background, 0 , 1 ) != "#") $header_background="#".$header_background;
	if ( $header_text == "#" || $header_text == "#blank" ) $header_text = "#ffffff";
	if ( get_option('options_complementary_colour') ){
		$complementary_color = get_option('options_complementary_colour');
	} else {
		$complementary_color = $header_background; 
	}
	
	// fill array with each colour combination to be checked
	
	$delights[] = array(
			'name'=>'Link colour',
			"fcolor"=>ltrim($link_color,"#"),
			"bcolor"=>"FFFFFF",
			"fixurl"=> get_admin_url( null, "customize.php"),
		);

	$delights[] = array(
			'name'=>'Visited link colour',
			"fcolor"=>ltrim($link_visited_color,"#"),
			"bcolor"=>"FFFFFF",
			"fixurl"=> get_admin_url( null, "customize.php"),
		);

	$delights[] = array(
			'name'=>'Header background and text colour',
			"fcolor"=>ltrim($header_text,"#"),
			"bcolor"=>ltrim($header_background,"#"),
			"fixurl"=> get_admin_url( null, "customize.php"),
		);

	$delights[] = array(
			'name'=>'Complementary colour and button text colour',
			"fcolor"=>ltrim($btn_text,"#"),
			"bcolor"=>ltrim($complementary_color,"#"),
			"fixurl"=> get_admin_url( null, "customize.php"),
		);
		
		
	// load category colour combinations
			
	$terms = get_terms('category',array('hide_empty'=>false));
	if ($terms) {
			foreach ((array)$terms as $taxonomy ) {
	  		if ( $taxonomy->term_id < 2 ) continue;
			    $themeid = $taxonomy->term_id;
			    $themeURL= $taxonomy->slug;
			if ( version_compare( get_option('acf_version','1.0'), '5.5', '>' ) && function_exists('get_term_meta') ) {
				$background = get_term_meta($themeid, "cat_background_colour", true);
				$foreground = get_term_meta($themeid, 'cat_foreground_colour',true);
			} else {
				$background = get_option("category_".$themeid."_cat_background_colour");
				$foreground = get_option('category_'.$themeid.'_cat_foreground_colour');
			}
			$delights[] = array(
					'name'=>'Category: ' . $taxonomy->name,
					"fcolor"=>ltrim($foreground,"#"),
					"bcolor"=>ltrim($background,"#"),
					"fixurl"=> get_edit_term_link( $themeid, "category" ), 
				);

		}
	}		
	
	// load any wonderwall category colours
	$objectives = 0;
	if ( taxonomy_exists( "objective" )) $objectives = get_terms('objective', array('hide_empty'=>false) );
	if ($objectives) foreach ( $objectives as $o ){
		$colour = get_term_meta($o->term_id,'objective_colour');
		$css.= ".objective".$o->term_id." { background-color: ".$colour[0]." !important; }"; 
		$delights[] = array(
				'name'=>'Wall category: ' . $o->name,
				"fcolor"=>"ffffff",
				"bcolor"=>ltrim($colour[0],"#"),
				"fixurl"=> get_edit_term_link( $o->term_id, "objective" )
			);
	}
	
	// find 'click here' links in content 
	
	$q1 = "select ID from $wpdb->posts WHERE 
	( post_content LIKE '%<a%>click here<\/a>%' OR
	post_content LIKE '%<a%>here<\/a>%' OR 
	post_content LIKE '%<a%>download<\/a>%' OR
	post_content LIKE '%<a%>link<\/a>%' )
	AND post_status='publish'
	GROUP BY ID
	;
	";
	
	$click1 = $wpdb->get_col($q1);
	
	if ($click1) foreach ($click1 as $c){
			$delights[] = array(
					'name'=>'Click alert: ' . get_the_title($c) . " [" . get_post_type( $c ) . " " .  get_the_date( "d-m-Y", $c ) . "]",
					"fcolor"=>"",
					"bcolor"=>"",
					"fixurl"=> get_edit_post_link( $c ), 
				);
		
	}
	
	// create array for final elements output
	
	$tocheck = array();
	
	
	// loop through elements, check colours where available, build final array for output
	
	foreach ( $delights as $d ){

		if ($d['fcolor']){
			$feed_url = "https://webaim.org/resources/contrastchecker/?fcolor=".$d['fcolor']."&bcolor=".$d['bcolor']."&api";
			$response = wp_remote_get($feed_url);
			if( is_wp_error( $response ) ) {
				return false; // Bail early
			}		
			$app_list= array();
			if ( is_array( $response ) ) {
				$body = wp_remote_retrieve_body( $response );
				$header = $response['headers']; // array of http header lines
				$app_list = json_decode($body);
			}

			$tocheck[] = array(
			'AA' => ($app_list->AA == "pass") ? '<span class="dashicons dashicons-yes"></span><span class="sr-only">Pass</span>' : '<span class="dashicons dashicons-no"></span><span class="sr-only">Fail</span>',
			'AALg' => ($app_list->AALarge == "pass") ? '<span class="dashicons dashicons-yes"></span><span class="sr-only">Pass</span>' : '<span class="dashicons dashicons-no"></span><span class="sr-only">Fail</span>',
			'AAA' => ($app_list->AAA == "pass") ? '<span class="dashicons dashicons-yes"></span><span class="sr-only">Pass</span>' : '<span class="dashicons dashicons-no"></span><span class="sr-only">Fail</span>',
			'AAALg' => ($app_list->AAALarge == "pass") ? '<span class="dashicons dashicons-yes"></span><span class="sr-only">Pass</span>' : '<span class="dashicons dashicons-no"></span><span class="sr-only">Fail</span>',
			'name' => $d['name'],
			'fcolor' => $d['fcolor'],
			'bcolor' => $d['bcolor'],
			'fixurl' => $d['fixurl'],
			);
		} else {
			$tocheck[] = array(
			'name' => $d['name'],
			'fixurl' => $d['fixurl'],
			'AA' => '<span class="dashicons dashicons-no"></span><span class="sr-only">Fail</span>',
			'AALg' => '<span class="dashicons dashicons-no"></span><span class="sr-only">Fail</span>',
			'AAA' => '<span class="dashicons dashicons-no"></span><span class="sr-only">Fail</span>',
			'AAALg' => '<span class="dashicons dashicons-no"></span><span class="sr-only">Fail</span>',
			
			);
		}
				
	}


	// build table structure
	
	echo '	<table class="widefat gf_dashboard_view table" cellspacing="0" style="border:0px;">
				<thead>
				<tr>
					<td class="gf_dashboard_form_title_header" style="text-align:left; padding:8px 18px!important; font-weight:bold;">
						Element</td>
					<td class="gf_dashboard_form_title_header" style="text-align:left; padding:8px 18px!important; font-weight:bold;">
						Fix</td>
					<td class="gf_dashboard_form_title_header" style="text-align:center; padding:8px 18px!important; font-weight:bold;">
						AA</td>
					<td class="gf_dashboard_form_title_header" style="text-align:center; padding:8px 18px!important; font-weight:bold;">
						AA Large</td>
					<td class="gf_dashboard_form_title_header" style="text-align:center; padding:8px 18px!important; font-weight:bold;">
						AAA</td>
					<td class="gf_dashboard_form_title_header" style="text-align:center; padding:8px 18px!important; font-weight:bold;">
						AAA Large</td>
				</tr>
				</thead>
				';
	echo '	<tbody class="list:user user-list">';

	// build table rows from final elements

	foreach ( $tocheck as $d ){
						
		echo '			<tr valign="top">
							<td class="gf_dashboard_form_title column-title" style="padding:8px 18px;">';
							if (isset($d['fcolor'])){
								echo '<a href="https://webaim.org/resources/contrastchecker/?fcolor='.$d['fcolor'].'&bcolor='.$d['bcolor'].'" target="_blank">'.$d['name'].'</a>';
							} else {
								echo $d['name'];
							}
		echo '				</td>
							
							<td class="gf_dashboard_entries_unread" style="padding:8px 18px; text-align:center;">';
							if (isset($d['fixurl'])) {
								echo '<a href="'.esc_url($d['fixurl']).'" target="_blank">Edit</a>';
							} 
		echo '				</td>
							<td class="gf_dashboard_entries_unread" style="padding:8px 18px; text-align:center;">
								'.$d['AA'].'
							</td>
							<td class="gf_dashboard_entries_total" style="padding:8px 18px; text-align:center;">
								'.$d['AALg'].'
							</td>
							<td class="gf_dashboard_entries_total" style="padding:8px 18px; text-align:center;">
								'.$d['AAA'].'
							</td>
							<td class="gf_dashboard_entries_total" style="padding:8px 18px; text-align:center;">
								'.$d['AAALg'].'
							</td>
						</tr>
						';
	}
	
	// close table and add final link

	echo '</tbody></table>';
	echo "</div>";
	echo '<p style="margin-top:20px;"><a href="https://webaim.org/resources/contrastchecker/" target="_blank">Visit the WebAIM Contrast checker</a></p>';
	echo "</div>";
	
}

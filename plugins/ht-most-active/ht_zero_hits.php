<?php
/*
Plugin Name: HT Zero Hits Monitor
Plugin URI: http://www.helpfultechnology.com
Description: Widget to display least active pages
Author: Luke Oatham
Version: 1.0
Author URI: http://www.helpfultechnology.com
*/

add_action('admin_menu', 'ht_zero_hits_menu');

function ht_zero_hits_menu() {
  add_submenu_page('tools.php', __('Zero Hits Monitor','govintranet'), __('Zero Hits Monitor','govintranet'), 'manage_options', 'zero_hits', 'ht_zero_hits_options');
}

/*
function diffInMonths( $date1,  $date2)
{
	$date1 = new DateTime($date1);
	$date2 = new DateTime($date2);
    $diff =  $date1->diff($date2);
    $months = $diff->y * 12 + $diff->m + $diff->d / 30;
    return (int) round($months);
}
*/


function ht_zero_hits_options() {
	wp_register_style( 'zero-hits-style2',  plugin_dir_url("/") . "ht-most-active/ht_zero_hits.css" );
	wp_enqueue_style( 'zero-hits-style2' );
	wp_register_script( 'zero-hits-script', plugin_dir_url("/") . "ht-most-active/ht_zero_hits.js" );
	wp_enqueue_script( 'zero-hits-script' );

	$viewid = get_option('options_zh_viewid');	
    $redirect_uri = 'urn:ietf:wg:oauth:2.0:oob';
    $account_id = 'ga:' . $viewid;

	echo "<div class='wrap'>";

	echo "<h2>" . __( 'Zero Hits Monitor' ,'govintranet') . "</h2>";

	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.','govintranet') );
	}
	if (!$viewid)  {
		 _e('You must set your Google Analytics View ID.','govintranet');
	}
	$client_id = '956426687308-20cs4la3m295f07f1njid6ttoeinvi92.apps.googleusercontent.com';
	$client_secret = 'yzrrxZgCPqIu2gaqqq-uzB4D';
	

	$baseurl = site_url();
	$to_fill = $items;
	$k = 0;
	$alreadydone = array();
	$html = '';
	    
    include_once('GoogleAnalyticsAPI.class.php');
	$ga = new GoogleAnalyticsAPI();
	$ga->auth->setClientId($client_id); // From the APIs console
	$ga->auth->setClientSecret($client_secret); // From the APIs console
	$ga->auth->setRedirectUri($redirect_uri); // Url to your app, must match one in the APIs console
	
	// Get the Auth-Url
	$url = $ga->auth->buildAuthUrl();

	ini_set('error_reporting','0'); // disable all, for security

	$gatoken 		= get_option('ga_token');
	$refreshToken 	= get_option('ga_refresh_token');
	$tokenExpires   = get_option('ga_token_expires');
    $tokenCreated   = get_option('ga_token_created');
	/*
     *  Step 1: Get a Token
     */
    if ($gatoken==''){ // if no token stored
    	if( !isset($_GET['code']) ) { // if we aren't submitting GET code to the db
			$url = $ga->auth->buildAuthUrl(); // give users a form to generate code ?>
			<a class="btn btn-primary" target="_blank" href="<?php echo $url; ?>"><?php _e('Authorise Google Analytics access','govintranet');?></a>
			<form>
				<label for="code"><?php _e('Enter your code from Google','govintranet');?></label></span>
				<input id="code" name="code" />
				<input type="hidden" name="page" value="zero_hits" />
				<button class="submit" type="submit"><?php _e('Save','govintranet');?></button>
			</form>
		<?php
		} else { // we are submitting GET code to the db
	        $auth = $ga->auth->getAccessToken($_GET['code']);
	        if ($auth['http_code'] == 200) {
	            $accessToken    = $auth['access_token'];
	            $refreshToken   = $auth['refresh_token'];
	            $tokenExpires   = $auth['expires_in'];
	            $tokenCreated   = time();

	            // Store the Tokens
	            update_option('ga_token', $accessToken );
	            $gatoken = $accessToken; // make token available for use
	            update_option('ga_refresh_token', $refreshToken );
	            update_option('ga_token_expires', $tokenExpires );
	            update_option('ga_token_created', $tokenCreated );
	        } else {
	            $url = $ga->auth->buildAuthUrl(); // give users a form to generate code ?>
	            <em><?php _e('Sorry, something went wrong accessing Google Analytics','govintranet');?>:<?php echo $auth['error_description']; ?></em>
				<a class="btn btn-primary" target="_blank" href="<?php echo $url; ?>"><?php _e('Authorise Google Analytics access','govintranet');?></a>
				<form>
					<label for="code"><?php _e('Enter your code from Google','govintranet');?></label></span>
					<input id="code" name="code" type="text"/>
					<input type="hidden" name="page" value="zero_hits" />
					<button class="button" type="submit"><?php _e('Save','govintranet');?></button>
				</form>
	        <?php
	        }
		}
		/*
	     *  Step 2: Validate Token
	     */
	    } elseif ($gatoken!='' && (time() - $tokenCreated) >= $tokenExpires) { // We've got a token stored but it's expired
		    $auth = $ga->auth->refreshAccessToken($refreshToken);
		    $accessToken    = $auth['access_token'];
		    $tokenExpires   = $auth['expires_in'];
		    $tokenCreated   = time();
	        update_option('ga_token', $accessToken );
	        $gatoken = $accessToken; // make new token available for use
	        update_option('ga_token_expires', $tokenExpires );
	        update_option('ga_token_created', $tokenCreated );
		/*
	     *  Step 3: Do real stuff!
	     *          If we're here, we sure we've got an access token and it's valid
	     */
		}	

	if ($_REQUEST['action'] == "generate") {

		$tzone = get_option('tim ezone_string');
		date_default_timezone_set($tzone);
    
		echo "<a class='btn btn-primary' href='".admin_url('/tools.php?page=zero_hits')."'>".__('Dashboard','govintranet')."</a></td>";
		
		$ptypes = $_REQUEST['ptype'];
		$show = $_REQUEST['show'];

		if ( $ptypes ) :

			$englishtypes = array();
			foreach ( $ptypes as $pt){
				$obj = get_post_type_object( $pt );
				$pt_plural = $obj->labels->name;
				$englishtypes[] = strtolower( $pt_plural );
			}
	
			echo "<h2>".ucfirst( implode(", ", $englishtypes) );
			if ( $show == "all" ) echo " " . __("full report","govintranet");
			if ( $show == "6m" ) echo " " . __("not viewed in last 6 months","govintranet");
			if ( $show == "1y" ) echo " " . __("not viewed in last year","govintranet");
			echo "</h2>"; 		
	
			echo "
			<table class='table' id='zerohits'>
			<thead>
			<tr>
			";
			$months = array();
			for ( $i=1; $i<=12; $i++ ){
				$months[] = date('M',strtotime('2015-'.$i.'-01'));
			}
			$current_month = date('m');
			$last_month = $current_month -1 ;
			if ( $last_month > 11 ) $last_month = 0;
			for ($i = 1; $i <= 12; $i++) {
				echo "<th>".$months[$last_month]."</th>";
				$last_month ++;
				if ( $last_month > 11 ) $last_month = 0;
			} 
			if ( $show != "6m" && $show != "1y" ) echo "<th id='l6m'><span class='badge'>" . __('6M','govintranet') . " <span class='dashicons dashicons-sort'></span></span></th>";
			if ( $show != "1y" ) echo "<th id='l1y'><span class='badge'>" . __('1Y','govintranet') . " <span class='dashicons dashicons-sort'></span></span></th>";
			echo "<th id='url'><span class='badge'>" . __('Title','govintranet') . " <span class='dashicons dashicons-sort'></span></span></th>
			<th id='mdate'><span class='badge'>" . __('Modified','govintranet') . " <span class='dashicons dashicons-sort'></span></span></th>
			</tr>
			</thead>
			";
	
			$analyse = array(
					'post_type' => $ptypes,
					'posts_per_page' => -1,
					'post_status' => 'publish',
					'date_query' => array(
						array(
							'before' => array('year'=>date('Y') , 'month'=> date('n'), 'day'=> 1),
							'inclusive' => false,
						),
					),
				);
	
			if ( $show == "6m" ):
				$analyse['meta_query'] =  array(
						array(
						'key' => 'zh_total_6m',
						'value' => 0,
						),
					);
				$analyse['order_by'] = 'meta_value';
				$analyse['order'] = 'ASC';
			endif;
	
			if ( $show == "1y" ):
				$analyse['meta_query'] =  array(
						array(
						'key' => 'zh_total_1y',
						'value' => 0,
						),
					);
				$analyse['order_by'] = 'meta_value';
				$analyse['order'] = 'ASC';
			endif;
			
			$allposts = new WP_Query( $analyse );
	
			if ( $allposts->have_posts()) while ( $allposts->have_posts()){
				$allposts->the_post(); 
				$style = " class='success'";
				$style6m = " class='text-success cellcenter'";
				$style1y = " class='text-success cellcenter'";
				$stylemdate = " class='success'";
				if ( get_post_meta(get_the_id(), 'zh_total_6m', true) == 0 ) { $style6m = " class='text-danger cellcenter'"; $style = " class='warning text-warning'"; }
				if ( get_post_meta(get_the_id(), 'zh_total_1y', true) == 0 ) { $style1y = " class='text-danger cellcenter'"; $style = " class='danger text-danger'"; }
				$tdate = date('Ymd');
				if ( get_the_modified_date('Ymd') < date('Ymd', strtotime('-6 months ' . $tdate))) { $stylemdate = " class='warning cellcenter'"; }
				if ( get_the_modified_date('Ymd') < date('Ymd', strtotime('-12 months ' . $tdate))) { $stylemdate = " class='danger cellcenter'"; }
				echo "<tr>";
				$fig=" class='success cellcenter'";
				if ( !get_post_meta(get_the_id(), 'zh_month_12', true) ) $fig = " class='danger cellcenter'";
				echo "<td".$fig.">".get_post_meta(get_the_id(), 'zh_month_12', true)."</td>";
				$fig=" class='success cellcenter'";
				if ( !get_post_meta(get_the_id(), 'zh_month_11', true) ) $fig = " class='danger cellcenter'";
				echo "<td".$fig.">".get_post_meta(get_the_id(), 'zh_month_11', true)."</td>";
				$fig=" class='success cellcenter'";
				if ( !get_post_meta(get_the_id(), 'zh_month_10', true) ) $fig = " class='danger cellcenter'";
				echo "<td".$fig.">".get_post_meta(get_the_id(), 'zh_month_10', true)."</td>";
				$fig=" class='success cellcenter'";
				if ( !get_post_meta(get_the_id(), 'zh_month_9', true) ) $fig = " class='danger cellcenter'";
				echo "<td".$fig.">".get_post_meta(get_the_id(), 'zh_month_9', true)."</td>";
				$fig=" class='success cellcenter'";
				if ( !get_post_meta(get_the_id(), 'zh_month_8', true) ) $fig = " class='danger cellcenter'";
				echo "<td".$fig.">".get_post_meta(get_the_id(), 'zh_month_8', true)."</td>";
				$fig=" class='success cellcenter'";
				if ( !get_post_meta(get_the_id(), 'zh_month_7', true) ) $fig = " class='danger cellcenter'";
				echo "<td".$fig.">".get_post_meta(get_the_id(), 'zh_month_7', true)."</td>";
				$fig=" class='success cellcenter'";
				if ( !get_post_meta(get_the_id(), 'zh_month_6', true) ) $fig = " class='danger cellcenter'";
				echo "<td".$fig.">".get_post_meta(get_the_id(), 'zh_month_6', true)."</td>";
				$fig=" class='success cellcenter'";
				if ( !get_post_meta(get_the_id(), 'zh_month_5', true) ) $fig = " class='danger cellcenter'";
				echo "<td".$fig.">".get_post_meta(get_the_id(), 'zh_month_5', true)."</td>";
				$fig=" class='success cellcenter'";
				if ( !get_post_meta(get_the_id(), 'zh_month_4', true) ) $fig = " class='danger cellcenter'";
				echo "<td".$fig.">".get_post_meta(get_the_id(), 'zh_month_4', true)."</td>";
				$fig=" class='success cellcenter'";
				if ( !get_post_meta(get_the_id(), 'zh_month_3', true) ) $fig = " class='danger cellcenter'";
				echo "<td".$fig.">".get_post_meta(get_the_id(), 'zh_month_3', true)."</td>";
				$fig=" class='success cellcenter'";
				if ( !get_post_meta(get_the_id(), 'zh_month_2', true) ) $fig = " class='danger cellcenter'";
				echo "<td".$fig.">".get_post_meta(get_the_id(), 'zh_month_2', true)."</td>";
				$fig=" class='success cellcenter'";
				if ( !get_post_meta(get_the_id(), 'zh_month_1', true) ) $fig = " class='danger cellcenter'";
				echo "<td".$fig.">".get_post_meta(get_the_id(), 'zh_month_1', true)."</td>";
				if ( $show != "6m" && $show != "1y" ) echo "<td".$style6m.">".get_post_meta(get_the_id(), 'zh_total_6m', true)."</td>";
				if ( $show != "1y" ) echo "<td".$style1y.">".get_post_meta(get_the_id(), 'zh_total_1y', true)."</td>";
				echo "<td".$style."><a href='".admin_url('/post.php?post='.get_the_id().'&action=edit')."'>".get_the_title(get_the_id())."</a></td>";
				echo "<td".$stylemdate.">".get_the_modified_date(get_option('options_zh_date_format','Y-m-d'))."</td>";
				echo "</tr>";
			}
			echo "</table>";	
	
			echo "<a class='btn btn-primary' href='".admin_url('/tools.php?page=zero_hits')."'>".__('Dashboard','govintranet')."</a></td>";
			
	
		endif;

	}	

	elseif ($_REQUEST['action'] == "options") {
		echo "<a class='btn btn-primary' href='".admin_url('/tools.php?page=zero_hits')."'>".__('Dashboard','govintranet')."</a></td>";
		$ptargs = array( '_builtin' => false, 'public' => true, 'exclude_from_search' => false );
		$postTypes = get_post_types($ptargs, 'objects');
		$posttypeoptions = get_option('options_zh_post_types');
		echo "
			<h2>Settings</h2> 
			 <form method='post'>
			 	<p><label for='url'>View ID</label> <input type='text' name='viewid' value='".get_option('options_zh_viewid')."'></p>
			 	<p><label for='zh_date_format'>Date format</label> <input type='text' name='zh_date_format' value='".get_option('options_zh_date_format')."'></p>
			 	<p><label for='ptype'>Content types</label></p>
			";			 	
				echo'<p><label class="checkbox"><input type="checkbox" name="ptype[]" value="page"';
				if(in_array('page', $posttypeoptions)){ 
					echo" checked=\"checked\"";
				}
				echo'> <span class="labelForCheck">Pages</span></label></p>';		
			 	foreach($postTypes as $pt){
			 		if( $pt->rewrite["slug"] != "spot" ){
							echo'<p><label class="checkbox"><input type="checkbox" name="ptype[]" value="'. $pt->query_var .'"';
							if(in_array($pt->query_var, $posttypeoptions)){ 
								echo" checked=\"checked\"";
							}
							echo'> <span class="labelForCheck">'. $pt->labels->name .'</span></label></p>';
						}
			 	}
			 	echo'<p><label for="reset">Reset</label></p><p><label class="checkbox"><input type="checkbox" name="reset" value="reset"';
				echo'> <span class="labelForCheck">Reset patrol</span></label></p>';		
				echo "	
			 	<p></p>
				<p><input type='submit' value='Save' class='button-primary' /></p>
				<input type='hidden' name='page' value='zero_hits' />
				<input type='hidden' name='action' value='saveoptions' />
			  </form><br />
			  
			"; 					
		
	} 

	elseif ($_REQUEST['action'] == "saveoptions") {

		echo "<a class='btn btn-primary' href='".admin_url('/tools.php?page=zero_hits')."'>".__('Dashboard','govintranet')."</a></td>";
		echo "
			<h2>Settings</h2> 
			"; 		
		$viewid = $_REQUEST['viewid'];
		$posttypes = $_REQUEST['ptype'];
		$zh_date_format = $_REQUEST['zh_date_format'];
		$reset = $_REQUEST['reset'];
		update_option('options_zh_viewid', $viewid);
		update_option('options_zh_post_types', $posttypes);
		update_option('options_zh_date_format', $zh_date_format);
		
		echo "<p>Settings updated!</p>";
		
		if ($reset == "reset"):
			delete_zh_meta('0');
			echo "<p>" . __('The Zero Hits report has been reset','govintranet') . "</p>";
		endif;
				
	} 
		
	else {

		echo "<a class='btn btn-primary' href='".admin_url('/tools.php?page=zero_hits&action=options')."'>".__('Settings','govintranet')."</a></td>";
		echo "<h2>Dashboard</h2> ";
		zh_show_dashboard();
	}

	echo "</div>";  
  
}

function delete_zh_meta($postid){
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.','govintranet') );
	}
	global $wpdb;
	if ( !$postid > 0 ):
		$wpdb->query("DELETE from $wpdb->postmeta WHERE meta_key = 'zh_last_processed';");
		$wpdb->query("DELETE from $wpdb->postmeta WHERE meta_key = 'zh_month_1';");
		$wpdb->query("DELETE from $wpdb->postmeta WHERE meta_key = 'zh_month_2';");
		$wpdb->query("DELETE from $wpdb->postmeta WHERE meta_key = 'zh_month_3';");
		$wpdb->query("DELETE from $wpdb->postmeta WHERE meta_key = 'zh_month_4';");
		$wpdb->query("DELETE from $wpdb->postmeta WHERE meta_key = 'zh_month_5';");
		$wpdb->query("DELETE from $wpdb->postmeta WHERE meta_key = 'zh_month_6';");
		$wpdb->query("DELETE from $wpdb->postmeta WHERE meta_key = 'zh_month_7';");
		$wpdb->query("DELETE from $wpdb->postmeta WHERE meta_key = 'zh_month_8';");
		$wpdb->query("DELETE from $wpdb->postmeta WHERE meta_key = 'zh_month_9';");
		$wpdb->query("DELETE from $wpdb->postmeta WHERE meta_key = 'zh_month_10';");
		$wpdb->query("DELETE from $wpdb->postmeta WHERE meta_key = 'zh_month_11';");
		$wpdb->query("DELETE from $wpdb->postmeta WHERE meta_key = 'zh_month_12';");
		$wpdb->query("DELETE from $wpdb->postmeta WHERE meta_key = 'zh_total_1y';");
		$wpdb->query("DELETE from $wpdb->postmeta WHERE meta_key = 'zh_total_6m';");
	else:
		$wpdb->query("DELETE from $wpdb->postmeta WHERE meta_key = 'zh_last_processed' and post_id = ".$postid);	 
		$wpdb->query("DELETE from $wpdb->postmeta WHERE meta_key = 'zh_month_1' and post_id = ".$postid);	
		$wpdb->query("DELETE from $wpdb->postmeta WHERE meta_key = 'zh_month_2' and post_id = ".$postid);	
		$wpdb->query("DELETE from $wpdb->postmeta WHERE meta_key = 'zh_month_3' and post_id = ".$postid);	
		$wpdb->query("DELETE from $wpdb->postmeta WHERE meta_key = 'zh_month_4' and post_id = ".$postid);	
		$wpdb->query("DELETE from $wpdb->postmeta WHERE meta_key = 'zh_month_5' and post_id = ".$postid);	
		$wpdb->query("DELETE from $wpdb->postmeta WHERE meta_key = 'zh_month_6' and post_id = ".$postid);	
		$wpdb->query("DELETE from $wpdb->postmeta WHERE meta_key = 'zh_month_7' and post_id = ".$postid);	
		$wpdb->query("DELETE from $wpdb->postmeta WHERE meta_key = 'zh_month_8' and post_id = ".$postid);	
		$wpdb->query("DELETE from $wpdb->postmeta WHERE meta_key = 'zh_month_9' and post_id = ".$postid);	
		$wpdb->query("DELETE from $wpdb->postmeta WHERE meta_key = 'zh_month_10' and post_id = ".$postid);	 
		$wpdb->query("DELETE from $wpdb->postmeta WHERE meta_key = 'zh_month_11' and post_id = ".$postid);	
		$wpdb->query("DELETE from $wpdb->postmeta WHERE meta_key = 'zh_month_12' and post_id = ".$postid);	
		$wpdb->query("DELETE from $wpdb->postmeta WHERE meta_key = 'zh_total_1y' and post_id = ".$postid);	
		$wpdb->query("DELETE from $wpdb->postmeta WHERE meta_key = 'zh_total_6m' and post_id = ".$postid);	 
	endif;
}

function zero_hits_monitor(){
	update_option('zh_patrol_start', date('H:i:s') );
	
	$viewid = get_option('options_zh_viewid'); 
	$ptype = get_option('options_zh_post_types'); 

    $client_id = '660382727637-9a6j2f87ba86mross0rvi9jr37vb28h4.apps.googleusercontent.com';
    $client_secret = 'BuRLl-SduOLag_6BQGB38WNi';

	// PUBLIC
	//	    $client_id = '956426687308-20cs4la3m295f07f1njid6ttoeinvi92.apps.googleusercontent.com';
	//	    $client_secret = 'yzrrxZgCPqIu2gaqqq-uzB4D';
	
	$viewid = get_option('options_zh_viewid');	
    $redirect_uri = 'urn:ietf:wg:oauth:2.0:oob';
    $account_id = 'ga:' . $viewid;

	include_once( 'GoogleAnalyticsAPI.class.php' );
	$ga = new GoogleAnalyticsAPI();
	$ga->auth->setClientId($client_id); // From the APIs console
	$ga->auth->setClientSecret($client_secret); // From the APIs console
	$ga->auth->setRedirectUri($redirect_uri); // Url to your app, must match one in the APIs console
	
	// Get the Auth-Url
	$url = $ga->auth->buildAuthUrl();

	ini_set('error_reporting','0'); // disable all, for security

	$gatoken 		= get_option('ga_token');
	$refreshToken 	= get_option('ga_refresh_token');
	$tokenExpires   = get_option('ga_token_expires');
    $tokenCreated   = get_option('ga_token_created');
	   		
	if ($gatoken!='') {

	    $ga->setAccessToken($gatoken);
	    $ga->setAccountId($account_id);

		date_default_timezone_set('timezone_string');
	    $params = array(
	        'metrics'    => 'ga:uniquePageviews',
	        'dimensions' => 'ga:pagePath,ga:month',
	        'sort'		 => '-ga:uniquePageviews',
	    );

		$finalset = array();
		
		foreach ( $ptype as $pt ){
		
			$allposts = new WP_Query(array(
				'post_type' => $pt,
				'posts_per_page' => -1,
				'post_status' => 'publish',
				'date_query' => array(
					array(
					'before' => array('year'=>date('Y') , 'month'=> date('n'), 'day'=> 1),	
					'inclusive' => false,
					),
				),
				'meta_query' => array(
					'relation' => 'OR',
					array(
					'key' => 'zh_last_processed',
					'value' => date('Ym01'),
					'compare' => "<",
					'type' => 'DATETIME',
					),
					array(
					'key' => 'zh_last_processed',
					'compare' => "NOT EXISTS",
					),
				),
			)
			); 
			if ( $allposts->have_posts()) while ( $allposts->have_posts()){
				$allposts->the_post(); 
				sleep(1); // allow for 10 calls per second limit
				$u = get_permalink(get_the_id());
				$u = str_replace(site_url(), "", $u );
				
				$month_slot = 1;
				$last_processed = get_post_meta(get_the_id(), 'zh_last_processed' , true );
			 	$months_to_do = 12;
				$date = date ( 'Y-m-01 00:00:00' ); 
				$end_date = date ( 'Y-m-d', strtotime ( '-1 day ' . $date ) );
				$lastyear = date('Y');
				$lastyear--;
				$sdate = $lastyear."-".date('m')."-01";
				$start_date = date('Y-m-01',strtotime ( $sdate ) ); 
				$curmonth = date('m');
				$filter='ga:pagePath=~'.$u.'$';
					
			    // Set the default params. For example the start/end dates and max-results
			    $defaults = array(
			        'start-date' => $start_date,
			        'end-date'   => $end_date,
			        'filters' 	 => $filter,
			    ); print_r($defaults);
			    $ga->setDefaultQueryParams($defaults);

			    $visits = $ga->query($params); 
			
				if ( $visits ) foreach($visits as $r=>$result) {
					if ( $r == "rows" ) {
						foreach ($result as $res){ 
							$input_month = $res[1];
							$output_box = $curmonth - $input_month;
							if ( $output_box <= 0 ) $output_box = $output_box + 12;
							echo $outputbox;
							$t = $finalset[get_the_id()][$output_box];
							$finalset[get_the_id()][$output_box] = $t + $res[2];
						}			
					} else {
						$finalset[get_the_id()][$output_box] = 0;
					}
				} else {
					$finalset[get_the_id()][$output_box] = 0; // CHECK FOR GA ERROR - IF SO, DON'T SAVE
				}
				// check for blank months
				for ( $i=1; $i<=12; $i++){
					if ( !$finalset[get_the_id()][$i] ) $finalset[get_the_id()][$i] = 0;
				}
				$end_date = date ( 'Y-m-d', strtotime ( '-1 day' . $start_date ) );
				$month_slot ++;
				$finalset[get_the_id()][13] = $u;
	
				//tot up figures for the past 6 months
				$finalset[get_the_id()][14] = $finalset[get_the_id()][1]+$finalset[get_the_id()][2]+$finalset[get_the_id()][3]+$finalset[get_the_id()][4]+$finalset[get_the_id()][5]+$finalset[get_the_id()][6];

				//tot up figures for the past 12 months
				$finalset[get_the_id()][0] = $finalset[get_the_id()][14]+$finalset[get_the_id()][7]+$finalset[get_the_id()][8]+$finalset[get_the_id()][9]+$finalset[get_the_id()][10]+$finalset[get_the_id()][11]+$finalset[get_the_id()][12];

				delete_zh_meta(get_the_id());
				update_post_meta(get_the_id() , 'zh_last_processed', date('Ymd') );
				update_post_meta(get_the_id() , 'zh_month_1', $finalset[get_the_id()][1] );
				update_post_meta(get_the_id() , 'zh_month_2', $finalset[get_the_id()][2] );
				update_post_meta(get_the_id() , 'zh_month_3', $finalset[get_the_id()][3] );
				update_post_meta(get_the_id() , 'zh_month_4', $finalset[get_the_id()][4] );
				update_post_meta(get_the_id() , 'zh_month_5', $finalset[get_the_id()][5] );
				update_post_meta(get_the_id() , 'zh_month_6', $finalset[get_the_id()][6] );
				update_post_meta(get_the_id() , 'zh_month_7', $finalset[get_the_id()][7] );
				update_post_meta(get_the_id() , 'zh_month_8', $finalset[get_the_id()][8] );
				update_post_meta(get_the_id() , 'zh_month_9', $finalset[get_the_id()][9] );
				update_post_meta(get_the_id() , 'zh_month_10', $finalset[get_the_id()][10] );
				update_post_meta(get_the_id() , 'zh_month_11', $finalset[get_the_id()][11] );
				update_post_meta(get_the_id() , 'zh_month_12', $finalset[get_the_id()][12] );
				update_post_meta(get_the_id() , 'zh_total_1y', $finalset[get_the_id()][0] );
				update_post_meta(get_the_id() , 'zh_total_6m', $finalset[get_the_id()][14] );
			}
		}
		update_option('zh_patrol_end', date('H:i') );
	} else {
		update_option('zh_patrol_end', 'an error. Google Analytics authentication needs updating!');
	}
}

register_deactivation_hook(__FILE__, 'my_deactivation');

function my_deactivation() {
	wp_clear_scheduled_hook('zh_zero_hits_monitor');
}

add_action('wp_dashboard_setup' , 'zh_dashboard');
function zh_dashboard() {
	if ( current_user_can('manage_options') ):
		wp_register_style( 'zero-hits-style2',  plugin_dir_url("/") . "ht-most-active/ht_zero_hits.css" );
		wp_enqueue_style( 'zero-hits-style2' );	
	 	wp_add_dashboard_widget( 'zero_hits_dashboard' , 'Zero Hits Monitor' , 'zh_show_dashboard');
	 endif;
}

function zh_show_dashboard() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.','govintranet') );
	}

	$posttypes = get_option('options_zh_post_types');
	$inq = 0;
	echo "<table class='table table-striped'>
	<thead>
	<tr>
	<td>" . __('Content type','govintranet') . "</td>
	<td>" . __('6 months','govintranet') . "</td>
	<td>" . __('1 year','govintranet') . "</td>
	<td>" . __('Patrol','govintranet') . "</td>
	</tr>
	</thead>
	<tbody>";
	if ( $posttypes ) foreach ( $posttypes as $pt ){
			
			echo "<tr>";
			$inq = 0;
			$obj = get_post_type_object( $pt );
			$pt_singular = $obj->labels->singular_name;
			$pt_plural = $obj->labels->name;
			echo "<td><a href='".admin_url('/tools.php?page=zero_hits&action=generate&ptype[]='.$pt.'&show=all')."'>".$pt_plural."</a></td>";

			$allposts = new WP_Query(array(
				'post_type' => $pt,
				'posts_per_page' => -1,
				'post_status' => 'publish',
				'date_query' => array(array(
					'before' => array('year'=>date('Y') , 'month'=> date('n'), 'day'=> 1),
					'inclusive' => false,
								)),
				'meta_query' => array(
					array(
					'key' => 'zh_total_6m',
					'value' => 0,
					),
					),
				)
			);

			$inq.=$allposts->found_posts;
			
			echo "<td";
			if ( $allposts->found_posts > 0 ):
					echo " class='text-warning'>";
					echo "<a href='".admin_url('/tools.php?page=zero_hits&action=generate&ptype[]='.$pt.'&show=6m')."'>";
					echo sprintf(__('%d not viewed','govintranet') , $allposts->found_posts );
					echo "</a>";

			else:
				echo " class='text-success'>".__("Active","govintranet");
			endif;
			echo "</td>";
			
			$allposts = new WP_Query(array(
				'post_type' => $pt,
				'posts_per_page' => -1,
				'post_status' => 'publish',
				'date_query' => array(array(
					'before' => array('year'=>date('Y') , 'month'=> date('n'), 'day'=> 1),
					'inclusive' => false,
					)),
				'meta_query' => array(
					array(
					'key' => 'zh_total_1y',
					'value' => 0,
					),
					),
				)
			);

			$inq.=$allposts->found_posts;

			echo "<td";
			if ( $allposts->found_posts > 0 ):
					echo " class='text-danger'>";
					echo "<a href='".admin_url('/tools.php?page=zero_hits&action=generate&ptype[]='.$pt.'&show=1y')."'>";
					echo sprintf(__('%d not viewed','govintranet') , $allposts->found_posts );
					echo "</a>";
			else:
				echo " class='text-success'>".__("Active","govintranet");
			endif;
			echo "</td>";
			
			$args = array(
				'post_type' => $pt,
				'posts_per_page' => -1,
				'post_status' => 'publish',
				'date_query' => array(array(
					'before' => array('year'=>date('Y') , 'month'=> date('n'), 'day'=> 1),
					'inclusive' => false,
				)),
				'meta_query' => array(
					'relation' => 'OR',
					array(
					'key' => 'zh_last_processed',
					'value' => date('Ym01'),
					'compare' => "<",
					'type' => 'DATETIME',
					),
					array(
					'key' => 'zh_last_processed',
					'compare' => "NOT EXISTS",
					),
				)
			);
			$allposts = new WP_Query($args);

			$inq.=$allposts->found_posts;			

			echo "<td>";
			if ( $allposts->found_posts > 0 ):
				echo '<span class="dashicons dashicons-update"></span> ' . sprintf(__('%d queued','govintranet') , $allposts->found_posts );
			else:
				echo '<span class="dashicons dashicons-yes"></span> ' . __("Up to date","govintranet");
			endif;
			echo "</td>";			
			echo "</tr>";
								
	}
	
	echo "</tbody></table>";
	
	if ( get_option('zh_patrol_end') != "" ) echo "<p>" . sprintf( __('Last patrol finished at %s', 'govintranet') , get_option('zh_patrol_end') ) . "</p>"; 		

}

function my_activation(){
	if ( ! wp_next_scheduled( 'zh_zero_hits_monitor' ) ) {
	  wp_schedule_event( time(), 'hourly', 'zh_zero_hits_monitor' );
	}
}

register_activation_hook(__FILE__, 'my_activation');
add_action( 'zh_zero_hits_monitor', 'zero_hits_monitor' );

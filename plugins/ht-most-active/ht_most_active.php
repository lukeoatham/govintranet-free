<?php
/*
Plugin Name: HT Most active
Plugin URI: http://www.helpfultechnology.com
Description: Widget to display most active pages
Author: Luke Oatham
Version: 2.2.1
Author URI: http://www.helpfultechnology.com
*/

class htMostActive extends WP_Widget {

	function __construct() {
		
		parent::__construct(
			'htMostActive',
			__( 'HT Most active' , 'govintranet'),
			array( 'description' => __( 'Display pages with most pageviews' , 'govintranet') )
		);   
		
		if( function_exists('register_field_group') ):

			register_field_group(array (
				'key' => 'group_54c3150a2b558',
				'title' => __('Most active widget','govintranet'),
				'fields' => array (
					array (
						'key' => 'field_55327bd9f4f3d',
						'label' => __('Show guide chapters','govintranet'),
						'name' => 'show_guide_chapters',
						'prefix' => '',
						'ui' => 1,
						'ui_on_text' => __('Yes','govintranet'),
						'ui_off_text' => __('No','govintranet'),
						'type' => 'true_false',
						'instructions' => __('If enabled, this option will show individual guide chapters. If disabled, only main guide pages will appear.','govintranet'),
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array (
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'message' => '',
						'default_value' => 0,
					),
					array (
						'key' => 'field_54c31510b4670',
						'label' => __('Exclude','govintranet'),
						'name' => 'exclude_posts',
						'prefix' => '',
						'type' => 'relationship',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array (
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'post_type' => '',
						'taxonomy' => '',
						'filters' => array (
							0 => 'search',
							1 => 'post_type',
						),
						'elements' => '',
						'max' => '',
						'return_format' => 'id',
					),
				),
				'location' => array (
					array (
						array (
							'param' => 'widget',
							'operator' => '==',
							'value' => 'htmostactive',
						),
					),
				),
				'menu_order' => 0,
				'position' => 'normal',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
			));

		endif;

    }

    function widget($args, $instance) {
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        $items = intval($instance['items']);
        $pages = ($instance['pages']);
        $tasks = ($instance['tasks']);
        $projects = ($instance['projects']);
        $vacancies = ($instance['vacancies']);
        $news = ($instance['news']);
        $blog = ($instance['blog']);
        $events = ($instance['events']);
        $trail = intval($instance['trail']);
		$ga_viewid = ($instance['ga_viewid']);
        $cache = intval($instance['cache']);
        if ( !$cache ) $cache = 1;
		$widget_id = $id;
		$acf_key = "widget_" . $this->id_base . "-" . $this->number . "_exclude_posts" ;
		$exclude = get_option($acf_key);
		$stoppages = array('how-do-i','task-by-category','news-by-category','newspage','tagged','atoz','about','home','blogs','events','category','news-type');
		if ($exclude) foreach ($exclude as $sp){
			$stop = get_page($sp);
			if ($stop) $stoppages[] = $stop->post_name;
		}
		$acf_key = "widget_" . $this->id_base . "-" . $this->number . "_show_guide_chapters" ;
		$showchapters = get_option($acf_key);

	    $client_id = '956426687308-20cs4la3m295f07f1njid6ttoeinvi92.apps.googleusercontent.com';
	    $client_secret = 'yzrrxZgCPqIu2gaqqq-uzB4D';
		
	    $redirect_uri = 'urn:ietf:wg:oauth:2.0:oob';
	    $account_id = 'ga:'.$ga_viewid; 

		$baseurl = site_url();
		$to_fill = $items;
		$k = 0;
		$alreadydone = array();
		$html = '';
		$gatransient = substr( 'cached_ga_'.$widget_id.'_'.sanitize_file_name( $title ) , 0, 45 );
		$cachedga = get_transient( $gatransient );
		if ($cachedga != "") { // if we have a fresh cache

			foreach($cachedga as $result) {

				if ($k>$items-1){
					break;
				}
				$k++;
				$html .= $result;

			}

		} else { //load fresh analytics

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
		    	if( !isset($_GET['code']) ) { // if we arn't submitting GET code to the db
					$url = $ga->auth->buildAuthUrl(); // give users a form to generate code ?>
					<a class="btn btn-primary" target="_blank" href="<?php echo $url; ?>"><?php _e('Click to get code','govintranet');?></a>
					<form id="" class="">
						<label for="code"><?php _e('Enter your code from Google','govintranet');?></label></span>
						<input id="code" name="code" />
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
						<a class="btn btn-primary" target="_blank" href="<?php echo $url; ?>"><?php _e('Click to get code','govintranet');?></a>
						<form id="" class="">
							<label for="code"><?php _e('Enter your code from Google','govintranet');?></label></span>
							<input id="code" name="code" type="text"/>
							<button class="submit" type="submit"><?php _e('Save','govintranet');?></button>
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
			if ($gatoken!='') {
			    $ga->setAccessToken($gatoken);
			    $ga->setAccountId($account_id);

				$days_to_trail = $trail;
				if ($days_to_trail < 1) $days_to_trail = 1;
				$tzone = get_option('timezone_string');
				date_default_timezone_set($tzone);
				$start_date= date("Y-m-d",time()-(86400*$days_to_trail)); // last x days
				$donefilter = false;
				$filter = '';
				
				if ($projects=='on'){
					$filter.='ga:pagePath=~/project/';
					$donefilter=true;
				}
				if ($tasks=='on'){
					if ($donefilter) $filter.= "||";
					$filter.='ga:pagePath=~/task/';
					$donefilter=true;
				}
				if ($vacancies=='on'){
					if ($donefilter) $filter.= "||";
					$filter.='ga:pagePath=~/vacancy/';
					$donefilter=true;
				}
				if ($news=='on'){
					if ($donefilter) $filter.= "||";
					$filter.='ga:pagePath=~/news/';
					$donefilter=true;
				}
				if ($blog=='on'){
					if ($donefilter) $filter.= "||";
					$filter.='ga:pagePath=~/blog/';
					$donefilter=true;
				}
				if ($events=='on'){
					if ($donefilter) $filter.= "||";
					$filter.='ga:pagePath=~/event/';
					$donefilter=true;
				}
				if ($pages=='on'){
					if ($donefilter) $filter.= "||";
					$filter.='ga:pagePath=~/';
					$donefilter=true;
					if ($projects&&$tasks&&$vacancies&&$news&&$blog&&$events) $filter='ga:pagePath=~/';
				}
								
				//check length of regular express; GA has a 128 character limit. If we're over, query everything and we'll filter results later.
				if ( strlen($filter) > 128 ) $filter='ga:pagePath=~/';

			    // Set the default params. For example the start/end dates and max-results
			    $defaults = array(
			        'start-date' => $start_date,
			        'end-date'   => date('Y-m-d'),
			        'filters' 	 => $filter,
			    );
			    $ga->setDefaultQueryParams($defaults);

			    $params = array(
			        'metrics'    => 'ga:uniquePageviews',
			        'dimensions' => 'ga:pagePath',
			        'sort'		 => '-ga:uniquePageviews',
			    );
			    $visits = $ga->query($params); 
				if ( $visits ) foreach($visits as $r=>$result) {
					if ( $r == "rows" ) {
						foreach ($result as $res){
							$found = false;

							if (strpos($res[0], "show=") ) continue;

							if ($k>($items-1)) break;

							$tasktitle = '';
							$tasktitlecontext = '';
							$filtered_pagepath = $res[0];
							$ext = '';

							$path = "/task/";
							$pathlen = strlen($path);

							if ( substr( $filtered_pagepath,0,$pathlen ) == $path && $tasks == 'on' ){ 
								$pathparts = explode("/", $res[0]);
								if ( is_array($pathparts)){
									if ( end($pathparts) == '' ) array_pop($pathparts);
									$thistask = end($pathparts);								
									if ( in_array( $thistask, $stoppages ) ) continue;
									$tasktitle = false;
									$check = array_shift($pathparts);
									$check = array_shift($pathparts);
									$path = implode("/",$pathparts); 
									$taskpod = get_page_by_path( $path, OBJECT, 'task');
									if ("publish" != $taskpod->post_status) continue;
									$tasktitle =  govintranetpress_custom_title($taskpod->post_title);
									$taskid = $taskpod->ID;
									if ( $taskpod->post_parent ){ 
										$taskpod2 = get_post($taskpod->post_parent);
										if ( $showchapters != 1 ): // hide individual chapters
											$taskid = $taskpod2->ID;
											$tasktitle =  govintranetpress_custom_title($taskpod2->post_title);
										else: // show individual chapters
											$tasktitlecontext = " <small>(".govintranetpress_custom_title($taskpod2->post_title).")</small>";
										endif;
									}
									if (!$tasktitle) continue;
									if (in_array($taskid, $alreadydone )) continue;
									if ( get_post_meta($taskid,'external_link',true) ) $ext="class='external-link' ";
									$found = true;
									$k++;
								}
							}

							$path = "/news/"; 
							$pathlen = strlen($path); 
									
							if (substr( $filtered_pagepath,0,$pathlen ) == $path && $news == 'on' ){ 
								$pathparts = explode("/", $res[0]); 
								if ( is_array($pathparts) ){
									if ( end($pathparts) == '' ) array_pop($pathparts); 
									$thistask = end($pathparts); 
									if ( in_array( $thistask, $stoppages ) ) continue;
									$tasktitle=false;
									$path = 'news/'.$thistask;
									$taskpod = get_page_by_path( $thistask, OBJECT, 'news'); 
									if ("publish" != $taskpod->post_status) continue;
									$tasktitle=  $taskpod->post_title;
									$taskid = $taskpod->ID;
									if (!$tasktitle) continue;
									if (in_array($taskid, $alreadydone )) continue;
									if ( get_post_meta($taskpod->ID,'external_link',true) ) $ext="class='external-link' ";
									$found = true;
									$k++;
								}
							}	

							$path = "/project/"; 
							$pathlen = strlen($path); 
								
							if (substr( $filtered_pagepath,0,$pathlen ) == $path && $projects == 'on' ){ 
								$pathparts = explode("/", $res[0]); 
								if ( is_array($pathparts) ){
									if ( end($pathparts) == '' ) array_pop($pathparts); 
									$thistask = end($pathparts); 
									if ( in_array( $thistask, $stoppages ) ) continue;
									$tasktitle=false;
									$path = 'project/'.$thistask;
									$taskpod = get_page_by_path( $thistask, OBJECT, 'project'); 
									if ("publish" != $taskpod->post_status) continue;
									$tasktitle=  $taskpod->post_title;
									$taskid = $taskpod->ID;
									if ( $taskpod->post_parent ){
										$taskpod = get_post($taskpod->post_parent);
										$taskid = $taskpod->ID;
										$tasktitle=  $taskpod->post_title ;
									}
									if (!$tasktitle) continue;
									if (in_array($taskid, $alreadydone )) continue;
									if ( get_post_meta($taskpod->ID,'external_link',true) ) $ext="class='external-link' ";
									$found = true;
									$k++;
								}
							}			
							
							$path = "/vacancy/"; 
							$pathlen = strlen($path); 
								
							if (substr( $filtered_pagepath,0,$pathlen ) == $path && $vacancies == 'on' ){ 
								$pathparts = explode("/", $res[0]); 
								if ( is_array($pathparts) ){
									if ( end($pathparts) == '' ) array_pop($pathparts); 
									$thistask = end($pathparts); 
									if ( in_array( $thistask, $stoppages ) ) continue;
									$tasktitle=false;
									$path = 'vacancy/'.$thistask;
									$taskpod = get_page_by_path( $thistask, OBJECT, 'vacancy'); 
									if ("publish" != $taskpod->post_status) continue;
									$tasktitle=  $taskpod->post_title;
									$taskid = $taskpod->ID;
									if (!$tasktitle) continue;
									if (in_array($taskid, $alreadydone )) continue;
									if ( get_post_meta($taskpod->ID,'external_link',true) ) $ext="class='external-link' ";
									$found = true;
									$k++;
								}
							}			
					
							$path = "/event/"; 
							$pathlen = strlen($path); 
							
							if (substr( $filtered_pagepath,0,$pathlen ) == $path && $events == 'on' ){ 
								$pathparts = explode("/", $res[0]); 
								if ( is_array($pathparts) ){
									if ( end($pathparts) == '' ) array_pop($pathparts); 
									$thistask = end($pathparts); 
									if ( in_array( $thistask, $stoppages ) ) continue;
									$tasktitle=false;
									$path = 'event/'.$thistask;
									$taskpod = get_page_by_path( $thistask, OBJECT, 'event'); 
									if ("publish" != $taskpod->post_status) continue;
									$tasktitle=  $taskpod->post_title;
									$taskid = $taskpod->ID;
									if (!$tasktitle) continue;
									if (in_array($taskid, $alreadydone )) continue;
									if ( get_post_meta($taskpod->ID,'external_link',true) ) $ext="class='external-link' ";
									$found = true;
									$k++;
								}
							}	
							
							$path = "/blog/"; 
							$pathlen = strlen($path); 
							
							if (substr( $filtered_pagepath,0,$pathlen ) == $path && $blog == 'on' ){ 
								$pathparts = explode("/", $res[0]); 
								if ( is_array($pathparts) ){
									if ( end($pathparts) == '' ) array_pop($pathparts); 
									$thistask = end($pathparts); 
									if ( in_array( $thistask, $stoppages ) ) continue;
									$tasktitle=false;
									$path = 'blog/'.$thistask;
									$taskpod = get_page_by_path( $thistask, OBJECT, 'blog'); 
									if ("publish" != $taskpod->post_status) continue;
									$tasktitle=  $taskpod->post_title;
									$taskid = $taskpod->ID;
									if (!$tasktitle) continue;
									if (in_array($taskid, $alreadydone )) continue;
									if ( get_post_meta($taskpod->ID,'external_link',true) ) $ext="class='external-link' ";
									$found = true;
									$k++;
								}
							}	

							$path = "/"; 
							$pathlen = strlen($path); 
							
							if ( $pages == 'on' && !$found ){ // show pages		
								$pathparts = explode("/", $res[0]); 
								if ( is_array($pathparts) ){
									if ( end($pathparts) == '' ) array_pop($pathparts); 
									$thistask = end($pathparts); 
									if ( in_array( $thistask, $stoppages ) ) continue;
									$path = $res[0];
									$taskpod = get_page_by_path( $thistask, OBJECT, 'page'); 
									if ( $taskpod ):
										if ("publish" != $taskpod->post_status) continue;
										$tasktitle=  $taskpod->post_title;
										$taskid = $taskpod->ID;
										if (!$tasktitle) continue;
										if (in_array($taskid, $alreadydone )) continue;
										if ( get_post_meta($taskpod->ID,'external_link',true) ) $ext="class='external-link' ";
										$found = true;
										$k++;
									endif;
								}
							}		
							if ($tasktitle!='' ){
								$html .= "<li><a ".$ext."href='" . get_permalink($taskid) . "'>" . $tasktitle . "</a>" . $tasktitlecontext . "</li>";
								$transga[] = "<li><a ".$ext."href='" .  get_permalink($taskid) . "'>" . $tasktitle . "</a>" . $tasktitlecontext . "</li>";
								$alreadydone[] = $taskid;
							} 				
						}
					}
				}
			}

			if ( count($transga) > 0 ) {
				$transga[] = "<!-- Cached by GovIntranet at ".date('Y-m-d H:i:s')." -->";
				set_transient($gatransient,$transga,$cache * 60 * 60); // set cache period
			}
		}

		if ($k){
			echo $before_widget; 
	        if ( $title ) echo $before_title . $title . $after_title; 
			echo ("<ul>".$html."</ul>");
			echo $after_widget;
		}

		// end of popular pages

		wp_reset_query();
    }

    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['items'] = strip_tags($new_instance['items']);
		$instance['pages'] = strip_tags($new_instance['pages']);
		$instance['tasks'] = strip_tags($new_instance['tasks']);
		$instance['projects'] = strip_tags($new_instance['projects']);
		$instance['vacancies'] = strip_tags($new_instance['vacancies']);
		$instance['news'] = strip_tags($new_instance['news']);
		$instance['blog'] = strip_tags($new_instance['blog']);
		$instance['events'] = strip_tags($new_instance['events']);
		$instance['ga_viewid'] = strip_tags($new_instance['ga_viewid']);
		$instance['trail'] = strip_tags($new_instance['trail']);
		$instance['cache'] = strip_tags($new_instance['cache']);
		if ( $new_instance['reset'] == "on" ) delete_option('ga_token');
/*
		global $wpdb;
		$wpdb->query("DELETE from $wpdb->options WHERE option_name LIKE '_transient_cached_ga_%".sanitize_file_name( $new_instance['title'] )."'");
		$wpdb->query("DELETE from $wpdb->options WHERE option_name LIKE '_transient_timeout_cached_ga_%".sanitize_file_name( $new_instance['title'] )."'");
*/
       return $instance;
    }

    function form($instance) {
        $title = esc_attr($instance['title']);
        $items = esc_attr($instance['items']);
        $pages = esc_attr($instance['pages']);
        $tasks = esc_attr($instance['tasks']);
        $projects = esc_attr($instance['projects']);
        $vacancies = esc_attr($instance['vacancies']);
        $news = esc_attr($instance['news']);
        $blog = esc_attr($instance['blog']);
        $events = esc_attr($instance['events']);
        $ga_viewid = esc_attr($instance['ga_viewid']);
        $trail = esc_attr($instance['trail']);
        $cache = esc_attr($instance['cache']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','govintranet'); ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /><br><br>

          <label for="<?php echo $this->get_field_id('items'); ?>"><?php _e('Number of items:','govintranet'); ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('items'); ?>" name="<?php echo $this->get_field_name('items'); ?>" type="text" value="<?php echo $items; ?>" /><br><br>

          <label for="<?php echo $this->get_field_id('trail'); ?>"><?php _e('Days to trail:','govintranet'); ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('trail'); ?>" name="<?php echo $this->get_field_name('trail'); ?>" type="text" value="<?php echo $trail; ?>" /><br><br>

          <label for="<?php echo $this->get_field_id('ga_viewid'); ?>"><?php _e('GA View ID:','govintranet'); ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('ga_viewid'); ?>" name="<?php echo $this->get_field_name('ga_viewid'); ?>" type="text" value="<?php echo $ga_viewid; ?>" /><br><br>

          <label for="<?php echo $this->get_field_id('cache'); ?>"><?php _e('Hours to cache:','govintranet'); ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('cache'); ?>" name="<?php echo $this->get_field_name('cache'); ?>" type="text" value="<?php echo $cache; ?>" /><br><br>
          <label>Include:</label><br>

          <input id="<?php echo $this->get_field_id('tasks'); ?>" name="<?php echo $this->get_field_name('tasks'); ?>" type="checkbox" <?php checked((bool) $instance['tasks'], true ); ?> />
          <label for="<?php echo $this->get_field_id('tasks'); ?>"><?php _e('Tasks and guides','govintranet'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('projects'); ?>" name="<?php echo $this->get_field_name('projects'); ?>" type="checkbox" <?php checked((bool) $instance['projects'], true ); ?> />
          <label for="<?php echo $this->get_field_id('projects'); ?>"><?php _e('Projects','govintranet'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('vacancies'); ?>" name="<?php echo $this->get_field_name('vacancies'); ?>" type="checkbox" <?php checked((bool) $instance['vacancies'], true ); ?> />
          <label for="<?php echo $this->get_field_id('vacancies'); ?>"><?php _e('Vacancies','govintranet'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('news'); ?>" name="<?php echo $this->get_field_name('news'); ?>" type="checkbox" <?php checked((bool) $instance['news'], true ); ?> />
          <label for="<?php echo $this->get_field_id('news'); ?>"><?php _e('News','govintranet'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('blog'); ?>" name="<?php echo $this->get_field_name('blog'); ?>" type="checkbox" <?php checked((bool) $instance['blog'], true ); ?> />
          <label for="<?php echo $this->get_field_id('blog'); ?>"><?php _e('Blog posts','govintranet'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('events'); ?>" name="<?php echo $this->get_field_name('events'); ?>" type="checkbox" <?php checked((bool) $instance['events'], true ); ?> />
          <label for="<?php echo $this->get_field_id('events'); ?>"><?php _e('Events','govintranet'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('pages'); ?>" name="<?php echo $this->get_field_name('pages'); ?>" type="checkbox" <?php checked((bool) $instance['pages'], true ); ?> />
          <label for="<?php echo $this->get_field_id('pages'); ?>"><?php _e('Pages','govintranet'); ?></label>  <br>

          <input id="<?php echo $this->get_field_id('reset'); ?>" name="<?php echo $this->get_field_name('reset'); ?>" type="checkbox"  />
          <label for="<?php echo $this->get_field_id('reset'); ?>"><?php _e('Reset authentification','govintranet'); ?></label>  <br>

        </p>

        <?php
    }

}

add_action('widgets_init', create_function('', 'return register_widget("htMostActive");'));

?>
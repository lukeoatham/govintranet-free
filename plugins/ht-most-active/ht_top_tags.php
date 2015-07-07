<?php
/*
Plugin Name: HT Top tags
Plugin URI: http://www.helpfultechnology.com
Description: Widget to display top tags from live Google Analytics feed
Author: Luke Oatham
Version: 1.1
Author URI: http://www.helpfultechnology.com
*/
 
class htTopTags extends WP_Widget {
    function htTopTags() {
        parent::WP_Widget(false, 'HT Top tags', array('description' => 'Display top tags from pages with most pageviews'));
    }
    
    function widget($args, $instance) {
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        $items = intval($instance['items']);
        $chart = $instance['chart']; 
        $task = $instance['task'];
        $news = $instance['news'];
        $blog = $instance['blog'];
        $events = $instance['events'];
        $project = $instance['project'];
        $vacancy = $instance['vacancy'];
        $trail = intval($instance['trail']);
        $topnumber = intval($instance['topnumber']);
		$ga_viewid = $instance['ga_viewid'];
        $cache = intval($instance['cache']);
        if ( !isset($cache) || $cache == 0) $cache = 1;
		$widget_id = $id;
		
		// PUBLIC
	    $client_id = '956426687308-20cs4la3m295f07f1njid6ttoeinvi92.apps.googleusercontent.com';
	    $client_secret = 'yzrrxZgCPqIu2gaqqq-uzB4D';
	    $redirect_uri = 'urn:ietf:wg:oauth:2.0:oob';
	    $account_id = 'ga:'.$ga_viewid; // 95422553
		
		wp_register_style( 'ht_top_tags', plugin_dir_url("/") . "ht-most-active/ht_top_tags.css");
		wp_enqueue_style( 'ht_top_tags' );


		global $post;
		wp_reset_postdata();
		global $id; 
		$to_fill = $items;
		$k=0;
		$alreadydone= array();
				
		//display manual overrides first
		
		
		$html='';
	
		//check to see if we have saved a cache of popular pages
	
		$cachedga = get_transient('cached_ga_tags_'.$widget_id.'_'.sanitize_file_name( $title ));

		if ($cachedga) { // if we have a fresh cache just display immediately
			foreach($cachedga as $result) { 
				$k++;
				$html.=($result);
				$toptagsslug[]="cached";
				if ($k>$items-1){
					break;
				}								
			}
			$manual = $k;
	
		} else { // ******************* LOAD FRESH ANALYTICS *******************************	

		    include_once('GoogleAnalyticsAPI.class.php');
			$ga = new GoogleAnalyticsAPI();
			$ga->auth->setClientId($client_id); // From the APIs console
			$ga->auth->setClientSecret($client_secret); // From the APIs console
			$ga->auth->setRedirectUri($redirect_uri); // Url to your app, must match one in the APIs console
	
			// Get the Auth-Url
			$url = $ga->auth->buildAuthUrl();

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
					<a class="btn btn-primary" target="_blank" href="<?php echo $url; ?>">Authorise Google Analytics access</a>
					<form id="" class="">
						<label for="code">Enter your code from Google</label></span>
						<input id="code" name="code" />
						<button class="submit" type="submit">Save</button>
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
			            <em>Sorry, something went wrong accessing Google Analytics :<?php echo $auth['error_description']; ?></em>
						<a class="btn btn-primary" target="_blank" href="<?php echo $url; ?>">Authorise Google Analytics access</a>
						<form id="" class="">
							<label for="code">Enter your code from Google</label></span>
							<input id="code" name="code" type="text"/>
							<button class="submit" type="submit">Save</button>
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

				date_default_timezone_set('timezone_string');
				$start_date= date("Y-m-d",time()-(86400*$days_to_trail)); // last x days

				$count = $topnumber;
				$donefilter=false;
				$filter='';
				
				$toptags=array();

				//setup variables for the GA query
				
				if ($news=='on'){
					$filter.='ga:pagePath=~/news/';
					$donefilter=true;
				}
				if ($blog=='on'){
					if ($donefilter) { $filter.= "||"; }
					$filter.='ga:pagePath=~/blog/';
					$donefilter=true;
				}
				if ($task=='on'){
					if ($donefilter) { $filter.= "||"; }
					$filter.='ga:pagePath=~/task/';
					$donefilter=true;
				}
				if ($events=='on'){
					if ($donefilter) { $filter.= "||"; }
					$filter.='ga:pagePath=~/event/';
					$donefilter=true;
				}
				if ($project=='on'){
					if ($donefilter) { $filter.= "||"; }
					$filter.='ga:pagePath=~/project/';
					$donefilter=true;
				}
				if ($vacancy=='on'){
					if ($donefilter) { $filter.= "||"; }
					$filter.='ga:pagePath=~/vacancy/';
					$donefilter=true;
				}

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

				foreach($visits as $r=>$result) {
					if ( $r == "rows") { 
						foreach ($result as $res){ 
							if (strpos($res[0], "show=") ) continue;
							if (strpos($res[0], "code=") ) continue;

							if ($k>($items-1)) break;

							$tasktitle = '';
							$tasktitlecontext = '';
							$filtered_pagepath = $res[0];

							$pathparts = explode("/", $filtered_pagepath);
							if ( end($parthparts) == '' ) array_pop($pathparts);
							$thistask = end($pathparts);
							if ( in_array( $thistask, $stoppages ) ) continue;
							$tasktitle = false;
							$check = array_shift($pathparts);
							$check = array_shift($pathparts);
							$taskslug = implode("/",$pathparts);

							if (strstr($filtered_pagepath,'/news/') && $news == 'on'  ){ 
								$customquery = get_page_by_path( $taskslug, OBJECT, "news"); 
								if (!$customquery || $customquery->post_status!="publish") continue;	
								$taskid = 	$customquery->ID;
								$post_tags = get_the_tags($taskid);
								$pageviews = $res[1];			
								if ( $post_tags ) foreach ($post_tags as $pt){ 
									if ( isset( $toptagsviews[$pt->slug] )):
										$toptagsviews[$pt->slug]+=$pageviews;
									else:
										$toptagsviews[$pt->slug]=$pageviews;
									endif;
									$toptags[$pt->slug]=$pt->name;
									$toptagsslug[$pt->slug]=$pt->slug;
								}
								$alreadydone[] = $taskid;			
								$k++;
							}		
			
							if (strstr($filtered_pagepath,'/blog/') && $blog == 'on'  ){
								$customquery = get_page_by_path( $taskslug, OBJECT, "blog"); 
								if ($customquery->post_status!="publish") continue;	
								$taskid = 	$customquery->ID;
								$post_tags = get_the_tags($taskid); 
								$pageviews = $res[1];			
								foreach ($post_tags as $pt){ 
									$toptagsviews[$pt->slug]+=$pageviews;
									$toptags[$pt->slug]=$pt->name;
									$toptagsslug[$pt->slug]=$pt->slug;
								}
								$alreadydone[] = $taskid;			
								$k++;
							}	
									
							if (strstr($filtered_pagepath,'/task/') && $task == 'on'  ){
								$customquery = get_page_by_path( $taskslug, OBJECT, "task"); 
								if ($customquery->post_status!="publish") continue;	
								$taskid = 	$customquery->ID;
								$post_tags = get_the_tags($taskid);
								$pageviews = $res[1];			
								foreach ($post_tags as $pt){ 
									$toptagsviews[$pt->slug]+=$pageviews;
									$toptags[$pt->slug]=$pt->name;
									$toptagsslug[$pt->slug]=$pt->slug;
								}
								$alreadydone[] = $taskid;			
								$k++;
							}		
					
							if (strstr($filtered_pagepath,'/event/') && $events == 'on'  ){
								$customquery = get_page_by_path( $taskslug, OBJECT, "event"); 
								if ($customquery->post_status!="publish") continue;	
								$taskid = 	$customquery->ID;
								$post_tags = get_the_tags($taskid);
								$pageviews = $res[1];			
								foreach ($post_tags as $pt){ 
									$toptagsviews[$pt->slug]+=$pageviews;
									$toptags[$pt->slug]=$pt->name;
									$toptagsslug[$pt->slug]=$pt->slug;
								}
								$alreadydone[] = $taskid;			
								$k++;
							}		
							
							if (strstr($filtered_pagepath,'/project/') && $project == 'on'  ){
								$customquery = get_page_by_path( $taskslug, OBJECT, "project"); 
								if ($customquery->post_status!="publish") continue;		
								$tasktitle =  $customquery->post_title ; 
								$taskid = 	$customquery->ID;
								if (!$tasktitle){
									continue;
								}	
								if (in_array($taskid, $alreadydone )) {
									continue;
								}
								$taskslug = $customquery->post_name;
								$post_tags = get_the_tags($customquery->ID);
								$pageviews = $res[1];			
								foreach ($post_tags as $pt){
									$toptagsviews[$pt->slug]+=$pageviews;
									$toptags[$pt->slug]=$pt->name;
									$toptagsslug[$pt->slug]=$pt->slug;
								}
								$alreadydone[] = $taskid;			
								$k++;
							}				
							
							if (strstr($filtered_pagepath,'/vacancy/') && $vacancy == 'on'  ){
								$customquery = get_page_by_path( $taskslug, OBJECT, "vacancy"); 
								if ($customquery->post_status!="publish") continue;		
								$tasktitle =  $customquery->post_title ; 
								$taskid = 	$customquery->ID;
								if (!$tasktitle){
									continue;
								}	
								if (in_array($taskid, $alreadydone )) {
									continue;
								}
								$taskslug = $customquery->post_name;
								$post_tags = get_the_tags($customquery->ID);
								$pageviews = $res[1];			
								foreach ($post_tags as $pt){
									$toptagsviews[$pt->slug]+=$pageviews;
									$toptags[$pt->slug]=$pt->name;
									$toptagsslug[$pt->slug]=$pt->slug;
								}
								$alreadydone[] = $taskid;			
								$k++;
							}	

						}
					}
				}

				//sort the arrays of tags and pageviews
				array_multisort($toptagsviews,SORT_DESC,$toptags,$toptagsslug);		
				$grandtotal = 0;
				$k=0;
				$manual = 0;
				// work out the grand total
				foreach ($toptagsslug as $tt){ 
					$k++;
					$grandtotal=$grandtotal + intval($toptagsviews[$tt]); 
					if ($k>($items-$manual)){
						break;
					}		    
				} 
		
				//output each tag
				$k = 0;		
				
				$q1 = 1/4*log($grandtotal); 
				$q2 = 1/2*log($grandtotal);
				$q3 = 3/4*log($grandtotal);
				foreach ($toptagsslug as $tt){ 
					$k++;
					if ($k>$items-$manual){
						break;
					}		    
		
					//work out the log of the page count so that we distribute evenly across the 4 hotness brackets			
					$percent = log($toptagsviews[$tt]);
		
					//place into 1 of 4 brackets
					if ( $percent >= $q3 ) $percentile = "p4";
					if ( $percent < $q3 && $percent >= $q2) $percentile = "p3";
					if ( $percent < $q2 && $percent >= $q1 ) $percentile = "p2";
					if ( $percent < $q1 ) $percentile = "p1";
		
	
					if ( "on" == $chart ): 
						$temphtml='
						<div class="progress">
						  <div class="progress-bar progress-bar-title" style="width: 70%">
						    <a href="'.site_url().'/tag/'.$tt.'/">'.str_replace(' ', '&nbsp' , ucfirst($toptags[$tt])).'</a>  </div>
						    <span class="sr-only"> '.ucfirst($toptags[$tt]).'</span>
						  <div id="chart-'.$toptagsslug[$tt].'-'.$percentile.'" class="progress-bar wptag '.$percentile.'" style="width: ';
						  if ( "p4" == $percentile) $temphtml.= "30";
						  if ( "p3" == $percentile) $temphtml.= "22.5";
						  if ( "p2" == $percentile) $temphtml.= "15";
						  if ( "p1" == $percentile) $temphtml.= "7.5";
						  $temphtml.='%">
						  </div>
						</div>
						';
						$html.=$temphtml;
						$transga[]=$temphtml;
					else:
						$html.='<span><a class="wptag '.$percentile.'" href="'.site_url().'/tag/'.$tt.'/">'.str_replace(' ', '&nbsp' , ucfirst($toptags[$tt])).'</a></span> ';
						$transga[]='<span><a  class="wptag '.$percentile.'" href="'.site_url().'/tag/'.$tt.'/">'.str_replace(' ', '&nbsp' , ucfirst($toptags[$tt])).'</a></span> ';
					endif;
					
				}
				
			}

			//cache new tags if we are using caching
			set_transient('cached_ga_tags_'.$widget_id.'_'.sanitize_file_name( $title ),$transga,$cache * HOUR_IN_SECONDS); // set cache period

		}

		if ($toptagsslug){
			echo $before_widget; 
			if ( $title ) echo $before_title . $title . $after_title; 
			echo "<style>";
			echo $styles;
			echo "</style>";
			echo '<div id="ht-top-tags">';
			echo '<div class="descr">';
			echo $html;
			echo "</div>";
			echo "</div>";
			echo $after_widget; 
		}

		wp_reset_query();								
		// end of trending

	}

    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['items'] = strip_tags($new_instance['items']);
		$instance['chart'] = strip_tags($new_instance['chart']);
		$instance['news'] = strip_tags($new_instance['news']);
		$instance['blog'] = strip_tags($new_instance['blog']);
		$instance['task'] = strip_tags($new_instance['task']);
		$instance['events'] = strip_tags($new_instance['events']);
		$instance['project'] = strip_tags($new_instance['project']);
		$instance['vacancy'] = strip_tags($new_instance['vacancy']);
		$instance['trail'] = strip_tags($new_instance['trail']);
		$instance['topnumber'] = strip_tags($new_instance['topnumber']);
		$instance['ga_viewid'] = strip_tags($new_instance['ga_viewid']);
		$instance['cache'] = strip_tags($new_instance['cache']);
		if ( $new_instance['reset'] == "on" ) delete_option('ga_token');
		global $wpdb;
		$wpdb->query("DELETE from $wpdb->options WHERE option_name LIKE '_transient_cached_ga_tags_%".sanitize_file_name( $new_instance['title'] )."'");
		$wpdb->query("DELETE from $wpdb->options WHERE option_name LIKE '_transient_timeout_cached_ga_tags_%".sanitize_file_name( $new_instance['title'] )."'");
       return $instance;
    }

    function form($instance) {
        $title = esc_attr($instance['title']);
        $items = esc_attr($instance['items']);
        $chart = esc_attr($instance['chart']);
        $news = esc_attr($instance['news']);
        $blog = esc_attr($instance['blog']);
        $task = esc_attr($instance['task']);
        $events = esc_attr($instance['events']);
        $project = esc_attr($instance['project']);
        $vacancy = esc_attr($instance['vacancy']);
        $trail = esc_attr($instance['trail']);
        $topnumber = esc_attr($instance['topnumber']);
        $ga_viewid = esc_attr($instance['ga_viewid']);
        $cache = esc_attr($instance['cache']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /><br><br>

          <label for="<?php echo $this->get_field_id('items'); ?>"><?php _e('Number of tags:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('items'); ?>" name="<?php echo $this->get_field_name('items'); ?>" type="text" value="<?php echo $items; ?>" /><br><br>
          
          <label for="<?php echo $this->get_field_id('trail'); ?>"><?php _e('Days to trail:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('trail'); ?>" name="<?php echo $this->get_field_name('trail'); ?>" type="text" value="<?php echo $trail; ?>" /><br><br>
          
          <label for="<?php echo $this->get_field_id('cache'); ?>"><?php _e('Hours to cache:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('cache'); ?>" name="<?php echo $this->get_field_name('cache'); ?>" type="text" value="<?php echo $cache; ?>" /><br><br>


          <label for="<?php echo $this->get_field_id('ga_viewid'); ?>"><?php _e('GA View ID:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('ga_viewid'); ?>" name="<?php echo $this->get_field_name('ga_viewid'); ?>" type="text" value="<?php echo $ga_viewid; ?>" /><br><br>

          <input id="<?php echo $this->get_field_id('chart'); ?>" name="<?php echo $this->get_field_name('chart'); ?>" type="checkbox" <?php checked((bool) $instance['chart'], true ); ?> />
          <label for="<?php echo $this->get_field_id('chart'); ?>"><?php _e('Show chart'); ?></label> <br><br>

          <label for="<?php echo $this->get_field_id('topnumber'); ?>"><?php _e('Include content from top #'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('topnumber'); ?>" name="<?php echo $this->get_field_name('topnumber'); ?>" type="text" value="<?php echo $topnumber; ?>" /><br><br>

          <label>Include:</label><br>

          <input id="<?php echo $this->get_field_id('news'); ?>" name="<?php echo $this->get_field_name('news'); ?>" type="checkbox" <?php checked((bool) $instance['news'], true ); ?> />
          <label for="<?php echo $this->get_field_id('news'); ?>"><?php _e('News'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('blog'); ?>" name="<?php echo $this->get_field_name('blog'); ?>" type="checkbox" <?php checked((bool) $instance['blog'], true ); ?> />
          <label for="<?php echo $this->get_field_id('blog'); ?>"><?php _e('Blog posts'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('task'); ?>" name="<?php echo $this->get_field_name('task'); ?>" type="checkbox" <?php checked((bool) $instance['task'], true ); ?> />
          <label for="<?php echo $this->get_field_id('task'); ?>"><?php _e('Tasks'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('events'); ?>" name="<?php echo $this->get_field_name('events'); ?>" type="checkbox" <?php checked((bool) $instance['events'], true ); ?> />
          <label for="<?php echo $this->get_field_id('events'); ?>"><?php _e('Events'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('vacancy'); ?>" name="<?php echo $this->get_field_name('vacancy'); ?>" type="checkbox" <?php checked((bool) $instance['vacancy'], true ); ?> />
          <label for="<?php echo $this->get_field_id('vacancy'); ?>"><?php _e('Vacancies'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('project'); ?>" name="<?php echo $this->get_field_name('project'); ?>" type="checkbox" <?php checked((bool) $instance['project'], true ); ?> />
          <label for="<?php echo $this->get_field_id('project'); ?>"><?php _e('Projects'); ?></label> <br><br>

          <input id="<?php echo $this->get_field_id('reset'); ?>" name="<?php echo $this->get_field_name('reset'); ?>" type="checkbox"  />
          <label for="<?php echo $this->get_field_id('reset'); ?>"><?php _e('Reset authentification'); ?></label>  <br>

        </p>

	<?php 
    }
}

add_action('widgets_init', create_function('', 'return register_widget("htTopTags");'));

?>
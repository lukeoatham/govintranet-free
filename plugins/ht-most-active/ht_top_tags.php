<?php
/*
Plugin Name: HT Top tags
Plugin URI: http://www.helpfultechnology.com
Description: Widget to display top tags from live Google Analytics feed
Author: Luke Oatham
Version: 0.1
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
		$ga_email = $instance['ga_email'];
		$ga_key = $instance['ga_key'];
		$ga_viewid = $instance['ga_viewid'];
        $cache = intval($instance['cache']);
		$widget_id = $id;
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
	
		$cachedga = get_transient('cached_ga_tags_'.$widget_id);

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
			// Developed by Steph Gray http://helpfultechnology.com, December 2009
			// Uses the GAPI library by Stig Manning <stig@sdm.co.nz>, version 1.3
			$google_analytics_username = $ga_email; 
			$google_analytics_password = $ga_key; 
			$google_analytics_profileid =$ga_viewid; 
			$days_to_trail = $trail;
			if ($days_to_trail < 1){
				$days_to_trail = 1;
			}
		
			$tzone = get_option('timezone_string');
			date_default_timezone_set($tzone);
			$start_date= date("Y-m-d",time()-(86400*$days_to_trail)); // last x days
			$enddate = '';
			define('ga_email',$google_analytics_username);
			define('ga_password',$google_analytics_password);
			define('ga_profile_id',$google_analytics_profileid);	
			$count = $topnumber;
			if ( isset( $conf['tab1']['garesultcount'] )) $count = $conf['tab1']['garesultcount'];
			require_once 'gapi.class.php';
			$ga = new gapi(ga_email,ga_password);
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
		
			$ga->requestReportData(ga_profile_id,array('pagePath'),array('uniquePageviews'),"-uniquePageviews",$filter,$start_date,$enddate,"1",$count);
			$transga=array();
	
			
			// process each result and filter for specific post types
			// for each result, we retrieve the WordPress tags for that post and build an array of tags and pageviews
			foreach($ga->getResults() as $result) { 
				if (strpos($result->getPagePath(), "show=") ){
					continue;	
				} 		    
				$tasktitle='';
				$filtered_pagepath = str_replace(@explode(",",$conf['tab1']['gatidypaths']),"",$result->getPagePath());
				$pathparts = explode("/", $result->getPagePath()); 
				$e = '';
				if ( is_array( $pathparts ) ) $e = end( $pathparts ) ;
				if ( is_array( $pathparts ) && $e == '' ) array_pop( $pathparts ); 
				$thistask = end( $pathparts ); 
				
				if (strstr($filtered_pagepath,'/news/') && $news == 'on'  ){ 
					$pathparts = explode("/", $result->getPagePath()); 
					if ( end($pathparts) == '' ) array_pop($pathparts); 
					$taskslug = end($pathparts); 
					$customquery = get_page_by_path( $taskslug, OBJECT, "news"); 
					if (!$customquery || $customquery->post_status!="publish") continue;	
					$taskid = 	$customquery->ID;
					$post_tags = get_the_tags($taskid);
					$pageviews = $result->getUniquePageviews();			
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
				}		

				if (strstr($filtered_pagepath,'/blog/') && $blog == 'on'  ){
					$pathparts = explode("/", $result->getPagePath()); 
					if ( end($pathparts) == '' ) array_pop($pathparts); 
					$taskslug = end($pathparts); 
					$customquery = get_page_by_path( $taskslug, OBJECT, "blog"); 
					if ($customquery->post_status!="publish") continue;	
					$taskid = 	$customquery->ID;
					$post_tags = get_the_tags($taskid); 
					$pageviews = $result->getUniquePageviews();			
					foreach ($post_tags as $pt){ 
						$toptagsviews[$pt->slug]+=$pageviews;
						$toptags[$pt->slug]=$pt->name;
						$toptagsslug[$pt->slug]=$pt->slug;
					}
					$alreadydone[] = $taskid;			
				}	
						
				if (strstr($filtered_pagepath,'/task/') && $task == 'on'  ){
					$pathparts = explode("/", $result->getPagePath());
					if ( end($pathparts) == '' ) array_pop($pathparts); 
					$taskslug = end($pathparts); 
					$customquery = get_page_by_path( $taskslug, OBJECT, "task"); 
					if ($customquery->post_status!="publish") continue;	
					$taskid = 	$customquery->ID;
					$post_tags = get_the_tags($taskid);
					$pageviews = $result->getUniquePageviews();			
					foreach ($post_tags as $pt){ 
						$toptagsviews[$pt->slug]+=$pageviews;
						$toptags[$pt->slug]=$pt->name;
						$toptagsslug[$pt->slug]=$pt->slug;
					}
					$alreadydone[] = $taskid;			
				}		
		
				if (strstr($filtered_pagepath,'/event/') && $events == 'on'  ){
					$pathparts = explode("/", $result->getPagePath());
					if ( end($pathparts) == '' ) array_pop($pathparts); 
					$taskslug = end($pathparts); 
					$customquery = get_page_by_path( $taskslug, OBJECT, "event"); 
					if ($customquery->post_status!="publish") continue;	
					$taskid = 	$customquery->ID;
					$post_tags = get_the_tags($taskid);
					$pageviews = $result->getUniquePageviews();			
					foreach ($post_tags as $pt){ 
						$toptagsviews[$pt->slug]+=$pageviews;
						$toptags[$pt->slug]=$pt->name;
						$toptagsslug[$pt->slug]=$pt->slug;
					}
					$alreadydone[] = $taskid;			
				}		
				
				if (strstr($filtered_pagepath,'/project/') && $project == 'on'  ){
					$pathparts = explode("/", $result->getPagePath());
					if ( end($pathparts) == '' ) array_pop($pathparts); 
					$taskslug = end($pathparts); 
					$customquery = get_page_by_path( $thistask, OBJECT, "project"); 
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
					$pageviews = $result->getUniquePageviews();			
					foreach ($post_tags as $pt){
						$toptagsviews[$pt->slug]+=$pageviews;
						$toptags[$pt->slug]=$pt->name;
						$toptagsslug[$pt->slug]=$pt->slug;
					}
					$alreadydone[] = $taskid;			
				}				
				
				if (strstr($filtered_pagepath,'/vacancy/') && $vacancy == 'on'  ){
					$pathparts = explode("/", $result->getPagePath());
					if ( end($pathparts) == '' ) array_pop($pathparts); 
					$taskslug = end($pathparts); 
					$tasktitle = false; 
					$customquery = get_page_by_path( $thistask, OBJECT, "vacancy"); 
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
					$pageviews = $result->getUniquePageviews();			
					foreach ($post_tags as $pt){
						$toptagsviews[$pt->slug]+=$pageviews;
						$toptags[$pt->slug]=$pt->name;
						$toptagsslug[$pt->slug]=$pt->slug;
					}
					$alreadydone[] = $taskid;			
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
		if ($cache != 0 && $cache){
			set_transient('cached_ga_tags_'.$widget_id,$transga,60*60*$cache); // set cache period
		} else {
			delete_transient('cached_ga_tags_'.$widget_id); 
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
		// end of popular pages

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
		$instance['ga_email'] = strip_tags($new_instance['ga_email']);
		$instance['ga_key'] = strip_tags($new_instance['ga_key']);
		$instance['ga_viewid'] = strip_tags($new_instance['ga_viewid']);
		
		$instance['cache'] = strip_tags($new_instance['cache']);
		delete_transient('cached_ga_tags_'.$widget_id);
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
        $ga_email = esc_attr($instance['ga_email']);
        $ga_key = esc_attr($instance['ga_key']);
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

          <label for="<?php echo $this->get_field_id('ga_email'); ?>"><?php _e('GA email:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('ga_email'); ?>" name="<?php echo $this->get_field_name('ga_email'); ?>" type="text" value="<?php echo $ga_email; ?>" /><br><br>

          <label for="<?php echo $this->get_field_id('ga_key'); ?>"><?php _e('GA key:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('ga_key'); ?>" name="<?php echo $this->get_field_name('ga_key'); ?>" type="text" value="<?php echo $ga_key; ?>" /><br><br>

          <label for="<?php echo $this->get_field_id('ga_viewid'); ?>"><?php _e('GA View ID:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('ga_viewid'); ?>" name="<?php echo $this->get_field_name('ga_viewid'); ?>" type="text" value="<?php echo $ga_viewid; ?>" /><br><br>

        </p>

	<?php 
    }
}

add_action('widgets_init', create_function('', 'return register_widget("htTopTags");'));

?>
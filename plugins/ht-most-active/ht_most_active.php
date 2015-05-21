<?php
/*
Plugin Name: HT Most active
Plugin URI: http://www.helpfultechnology.com
Description: Widget to display most active pages
Author: Luke Oatham
Version: 0.3
Author URI: http://www.helpfultechnology.com
*/
 
class htMostActive extends WP_Widget {
    function htMostActive() {
        parent::WP_Widget(false, 'HT Most active', array('description' => 'Display pages with most pageviews'));

		if( function_exists('register_field_group') ):
		
			register_field_group(array (
				'key' => 'group_54c3150a2b558',
				'title' => 'Most active widget',
				'fields' => array (
					array (
						'key' => 'field_55327bd9f4f3d',
						'label' => 'Show guide chapters',
						'name' => 'show_guide_chapters',
						'prefix' => '',
						'type' => 'true_false',
						'instructions' => 'If enabled, this option will show individual guide chapters. If disabled, only main guide pages will appear.',
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
						'label' => 'Exclude',
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
		$ga_email = ($instance['ga_email']);
		$ga_key = ($instance['ga_key']);
		$ga_viewid = ($instance['ga_viewid']);
        $cache = intval($instance['cache']);
		$widget_id = $id;
		$acf_key = "widget_" . $this->id_base . "-" . $this->number . "_exclude_posts" ;  
		$exclude = get_option($acf_key); 
		$stoppages = array('how-do-i','task-by-category','news-by-category','newspage','tagged','atoz','about','home','blogs','events','category','news-type'); 
		if ($exclude) foreach ($exclude as $sp){
			$stop = get_page($sp);
			if ($stop) $stoppages[] = $stop->post_name;
		}
		$acf_key = "widget_" . $this->id_base . "-" . $this->number . "_show_guide_chapters" ;  
		$showchapters = get_option($acf_key); echo "<!--". $showchapters. "-->";

       ?>
	   <?php echo $before_widget; ?>
       <?php if ( $title ) echo $before_title . $title . $after_title; ?>
	   <?php
		$baseurl = site_url();
		$to_fill = $items;
		$k = 0;
		$alreadydone = array();
		$hmtl = '';
		
		$cachedga = get_transient('cached_ga_'.$widget_id);
		if ($cachedga) { // if we have a fresh cache

			$html='';
			foreach($cachedga as $result) {

				if ($k>$items-1){
					break;
				}	
				$k++;
				$html .= $result;		

			}
	
		} else { //load fresh analytics

			ini_set('error_reporting','0'); // disable all, for security


			// Developed by Steph Gray http://helpfultechnology.com, December 2009
			// Uses the GAPI library by Stig Manning <stig@sdm.co.nz>, version 1.3
			$google_analytics_username = $ga_email; 
			$google_analytics_password = $ga_key; 
			$google_analytics_profileid =$ga_viewid;
			$days_to_trail = $trail;
			if ($days_to_trail < 1){
				$days_to_trail=1;
			}
		
			date_default_timezone_set('timezone_string');
			$start_date= date("Y-m-d",time()-(86400*$days_to_trail)); // last x days
			define('ga_email',$google_analytics_username);
			define('ga_password',$google_analytics_password);
			define('ga_profile_id',$google_analytics_profileid);	
			$count = ($conf['tab1']['garesultcount']) ? $conf['tab1']['garesultcount'] : 500;
			require_once 'gapi.class.php';
			$ga = new gapi(ga_email,ga_password);
			$donefilter=false;
			$filter='';
			
			if ($projects=='on'){
				$filter.='ga:pagePath=~/projects/';
				$donefilter=true;
			}
			if ($tasks=='on'){
				if ($donefilter) { $filter.= "||"; 
					
				}
				$filter.='ga:pagePath=~/task/';
				$donefilter=true;
			}
			if ($vacancies=='on'){
				if ($donefilter) { $filter.= "||"; 
					
				}
				$filter.='ga:pagePath=~/vacancies/';
				$donefilter=true;
			}
			if ($news=='on'){
				if ($donefilter) { $filter.= "||"; 
					
				}
				$filter.='ga:pagePath=~/news/';
				$donefilter=true;
			}
			if ($blog=='on'){
				if ($donefilter) { $filter.= "||"; 
					
				}
				$filter.='ga:pagePath=~/blog/';
				$donefilter=true;
			}
			if ($events=='on'){
				if ($donefilter) { $filter.= "||"; 
					
				}
				$filter.='ga:pagePath=~/event/';
				$donefilter=true;
			}
			if ($pages=='on'){ 
				if ($donefilter) { $filter.= "||"; 
					
				}
				$filter.='ga:pagePath=~/';
				$donefilter=true;
			}
		
		
			$ga->requestReportData(ga_profile_id,array('pagePath'),array('uniquePageviews'),"-uniquePageviews",$filter,$start_date,$enddate,"1",$count);
			$transga=array();
			$html='';
			foreach($ga->getResults() as $result) { 
				if (strpos($result->getPagePath(), "show=") ){
			continue;	
		}
		
				if ($k>($items-1)){
			break;
		}	
				
				$tasktitle = '';
				$tasktitlecontext = '';
				$filtered_pagepath = str_replace(@explode(",",$conf['tab1']['gatidypaths']),"",$result->getPagePath());
		
				$path = "/task/"; 
				$pathlen = strlen($path); 
						
				if (substr( $filtered_pagepath,0,$pathlen ) == $path && $tasks == 'on' ){ // only show tasks, but not the tasks landing page
					$pathparts = explode("/", $result->getPagePath()); 
					if ( end($parthparts) == '' ) array_pop($pathparts); 
					$thistask = end($pathparts); 
					if ( in_array( $thistask, $stoppages ) ) continue;
					$tasktitle = false; 
					$check = array_shift($pathparts); 
					$check = array_shift($pathparts); 
					$path = implode("/",$pathparts);
					$taskpod = get_page_by_path( $path, OBJECT, 'task'); 
					if ("publish" != $taskpod->post_status) continue;
					$tasktitle=  govintranetpress_custom_title($taskpod->post_title);
					$taskid = $taskpod->ID;
					$taskslug = $taskpod->post_name;
					if ( $taskpod->post_parent ){
						$taskpod = get_post($taskpod->post_parent);
						if ( $showchapters != 1 ):
							$taskid = $taskpod->ID;
							$taskslug = $taskpod->post_name;
						else:
							$tasktitlecontext = " <small>(".govintranetpress_custom_title($taskpod->post_title).")</small>";
						endif;
					}
		
					if (!$tasktitle){
						continue;
					}	
					if (in_array($taskid, $alreadydone )) {
						continue;
					}
					
					$k++;
				
				}		
				
		
				$path = "/news/"; 
				$pathlen = strlen($path); 
						
				if (substr( $filtered_pagepath,0,$pathlen ) == $path && $news == 'on' ){ // only show tasks, but not the tasks landing page
					$pathparts = explode("/", $result->getPagePath()); 
					if ( end($parthparts) == '' ) array_pop($pathparts); 
					$thistask = end($pathparts); 
					if ( in_array( $thistask, $stoppages ) ) continue;
					$tasktitle=false;
					$path = 'news/'.$thistask;
					$taskpod = get_page_by_path( $thistask, OBJECT, 'news'); 
					if ("publish" != $taskpod->post_status) continue;
					$tasktitle=  $taskpod->post_title;
					$taskid = $taskpod->ID;
					$taskslug = $taskpod->post_name;
		
					if (!$tasktitle){
						continue;
					}	
					if (in_array($taskid, $alreadydone )) {
						continue;
					}
					
					$k++;
				}	
		
		
				$path = "/project/"; 
				$pathlen = strlen($path); 
					
				if (substr( $filtered_pagepath,0,$pathlen ) == $path && $projects == 'on' ){ // only show tasks, but not the tasks landing page
					$pathparts = explode("/", $result->getPagePath()); 
					if ( end($parthparts) == '' ) array_pop($pathparts); 
					$thistask = end($pathparts); 
					if ( in_array( $thistask, $stoppages ) ) continue;
					$tasktitle=false;
					$path = 'project/'.$thistask;
					$taskpod = get_page_by_path( $thistask, OBJECT, 'project'); 
					if ("publish" != $taskpod->post_status) continue;
					$tasktitle=  $taskpod->post_title;
					$taskid = $taskpod->ID;
					$taskslug = $taskpod->post_name;
					if ( $taskpod->post_parent ){
						$taskpod = get_post($taskpod->post_parent);
						$taskid = $taskpod->ID;
						$taskslug = $taskpod->post_name;
						$tasktitle=  $taskpod->post_title ;
					}
					if (!$tasktitle){
						continue;
					}	
					if (in_array($taskid, $alreadydone )) {
						continue;
					}
					$k++;
				
				}			
				
				$path = "/vacancy/"; 
				$pathlen = strlen($path); 
					
				if (substr( $filtered_pagepath,0,$pathlen ) == $path && $vacancies == 'on' ){ // only show tasks, but not the tasks landing page
		
					$pathparts = explode("/", $result->getPagePath()); 
					if ( end($parthparts) == '' ) array_pop($pathparts); 
					$thistask = end($pathparts); 
					if ( in_array( $thistask, $stoppages ) ) continue;
					$tasktitle=false;
					$path = 'vacancy/'.$thistask;
					$taskpod = get_page_by_path( $thistask, OBJECT, 'vacancy'); 
					if ("publish" != $taskpod->post_status) continue;
					$tasktitle=  $taskpod->post_title;
					$taskid = $taskpod->ID;
					$taskslug = $taskpod->post_name;
					if (!$tasktitle){
						continue;
					}	
					if (in_array($taskid, $alreadydone )) {
						continue;
					}
					$k++;
				}			
		
				$path = "/event/"; 
				$pathlen = strlen($path); 
				
				if (substr( $filtered_pagepath,0,$pathlen ) == $path && $events == 'on' ){ 
		
					$pathparts = explode("/", $result->getPagePath()); 
					if ( end($parthparts) == '' ) array_pop($pathparts); 
					$thistask = end($pathparts); 
					if ( in_array( $thistask, $stoppages ) ) continue;
					$tasktitle=false;
					$path = 'event/'.$thistask;
					$taskpod = get_page_by_path( $thistask, OBJECT, 'event'); 
					if ("publish" != $taskpod->post_status) continue;
					$tasktitle=  $taskpod->post_title;
					$taskid = $taskpod->ID;
					$taskslug = $taskpod->post_name;
					if (!$tasktitle){
						continue;
					}	
					if (in_array($taskid, $alreadydone )) {
						continue;
					}
					$k++;
		
				}	
				
				$path = "/blog/"; 
				$pathlen = strlen($path); 
				
				if (substr( $filtered_pagepath,0,$pathlen ) == $path && $blog == 'on' ){ 
		
					$pathparts = explode("/", $result->getPagePath()); 
					if ( end($parthparts) == '' ) array_pop($pathparts); 
					$thistask = end($pathparts); 
					if ( in_array( $thistask, $stoppages ) ) continue;
					$tasktitle=false;
					$path = 'blog/'.$thistask;
					$taskpod = get_page_by_path( $thistask, OBJECT, 'blog'); 
					if ("publish" != $taskpod->post_status) continue;
					$tasktitle=  $taskpod->post_title;
					$taskid = $taskpod->ID;
					$taskslug = $taskpod->post_name;
					if (!$tasktitle){
						continue;
					}	
					if (in_array($taskid, $alreadydone )) {
						continue;
					}
					$k++;
		
				}	
				
				if ( $pages == 'on' ){ // show pages		
					
					$pathparts = explode("/", $result->getPagePath()); 
					$countparts = count($pathparts)-2; 
					$thistask = $pathparts[intval($countparts)]; 
		
					if ( !in_array( $thistask, $stoppages ) ){
			
						global $wpdb;
						$q = "select post_title, ID from $wpdb->posts where post_name = '".$thistask."' and post_status='publish' and post_type='page';";
			
						$thispage = $wpdb->get_results($q, ARRAY_A);
						foreach ($thispage as $t){ 
							$tasktitle=  $t['post_title'] ;
							$taskid = $t['ID'];
						}
			
						if (!$tasktitle){
							continue;
						}	
						if (in_array($taskid, $alreadydone )) {
							continue;
						}
						$k++;
					} else {
						continue;
					}
				}			
						
		
				if ($tasktitle!='' ){
					$html .= "<li><a href='" . $baseurl . $result->getPagePath() . "'>" . $tasktitle . "</a>" . $tasktitlecontext . "</li>";		
					$transga[] = "<li><a href='" . $baseurl . $result->getPagePath() . "'>" . $tasktitle . "</a>" . $tasktitlecontext . "</li>";		
					$alreadydone[] = $taskid;
				}

			}

		}
		if ($cache):
		 	set_transient('cached_ga_'.$widget_id,$transga,60*60*$cache); // set cache period
		else:
			delete_transient('cached_ga_'.$widget_id);
		endif;

		if ($k==0){
			$html="<li>Looks like nobody has visited the intranet recently.</li>";
		}
		// end of popular pages

	
		echo ("<ul id='ga_".$_REQUEST['mode']."'>".$html."</ul>");
	
		wp_reset_query(); 
		
		echo $after_widget; 
	
	
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
		$instance['ga_email'] = strip_tags($new_instance['ga_email']);
		$instance['ga_key'] = strip_tags($new_instance['ga_key']);
		$instance['ga_viewid'] = strip_tags($new_instance['ga_viewid']);
		$instance['trail'] = strip_tags($new_instance['trail']);
		$instance['cache'] = strip_tags($new_instance['cache']);
		delete_transient( 'cached_ga_'.$widget_id );
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
        $ga_email = esc_attr($instance['ga_email']);
        $ga_key = esc_attr($instance['ga_key']);
        $ga_viewid = esc_attr($instance['ga_viewid']);
        $trail = esc_attr($instance['trail']);
        $cache = esc_attr($instance['cache']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /><br><br>

          <label for="<?php echo $this->get_field_id('items'); ?>"><?php _e('Number of items:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('items'); ?>" name="<?php echo $this->get_field_name('items'); ?>" type="text" value="<?php echo $items; ?>" /><br><br>
          
          <label for="<?php echo $this->get_field_id('trail'); ?>"><?php _e('Days to trail:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('trail'); ?>" name="<?php echo $this->get_field_name('trail'); ?>" type="text" value="<?php echo $trail; ?>" /><br><br>
          
          <label for="<?php echo $this->get_field_id('cache'); ?>"><?php _e('Hours to cache:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('cache'); ?>" name="<?php echo $this->get_field_name('cache'); ?>" type="text" value="<?php echo $cache; ?>" /><br><br>
          <label>Include:</label><br>

          <input id="<?php echo $this->get_field_id('tasks'); ?>" name="<?php echo $this->get_field_name('tasks'); ?>" type="checkbox" <?php checked((bool) $instance['tasks'], true ); ?> />
          <label for="<?php echo $this->get_field_id('tasks'); ?>"><?php _e('Tasks and guides'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('projects'); ?>" name="<?php echo $this->get_field_name('projects'); ?>" type="checkbox" <?php checked((bool) $instance['projects'], true ); ?> />
          <label for="<?php echo $this->get_field_id('projects'); ?>"><?php _e('Projects'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('vacancies'); ?>" name="<?php echo $this->get_field_name('vacancies'); ?>" type="checkbox" <?php checked((bool) $instance['vacancies'], true ); ?> />
          <label for="<?php echo $this->get_field_id('vacancies'); ?>"><?php _e('Vacancies'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('news'); ?>" name="<?php echo $this->get_field_name('news'); ?>" type="checkbox" <?php checked((bool) $instance['news'], true ); ?> />
          <label for="<?php echo $this->get_field_id('news'); ?>"><?php _e('News'); ?></label> <br>
          
          <input id="<?php echo $this->get_field_id('blog'); ?>" name="<?php echo $this->get_field_name('blog'); ?>" type="checkbox" <?php checked((bool) $instance['blog'], true ); ?> />
          <label for="<?php echo $this->get_field_id('blog'); ?>"><?php _e('Blog posts'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('events'); ?>" name="<?php echo $this->get_field_name('events'); ?>" type="checkbox" <?php checked((bool) $instance['events'], true ); ?> />
          <label for="<?php echo $this->get_field_id('events'); ?>"><?php _e('Events'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('pages'); ?>" name="<?php echo $this->get_field_name('pages'); ?>" type="checkbox" <?php checked((bool) $instance['pages'], true ); ?> />
          <label for="<?php echo $this->get_field_id('pages'); ?>"><?php _e('Pages'); ?></label>  <br>

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

add_action('widgets_init', create_function('', 'return register_widget("htMostActive");'));

?>
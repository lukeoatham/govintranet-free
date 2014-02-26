<?php
/*
Plugin Name: HT Most active
Plugin URI: http://www.helpfultechnology.com
Description: Widget to display most active pages
Author: Luke Oatham
Version: 0.2
Author URI: http://www.helpfultechnology.com
*/
 
class htMostActive extends WP_Widget {
    function htMostActive() {
        parent::WP_Widget(false, 'HT Most active', array('description' => 'Display pages with most pageviews'));
    }
    
    function widget($args, $instance) {
    	$siteurl = site_url();
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
        $cache = intval($instance['cache']);

		echo $before_widget; 
		if ( $title )
			echo $before_title . $title . $after_title; 
			$baseurl = home_url();
			$hc = new Pod ('homepage_control');
			$top_pages =  $hc->get_field('top_pages');
		
			$num_top_slots = count($top_pages);
			$to_fill = $items - $num_top_slots;
			$k=0;
			$alreadydone= array();
			//display top news stories
			$hmtl = '';
			if ($top_pages){
			foreach ($top_pages as $slot){
				$k++;
				$alreadydone[] = $slot['ID'];
				$thistitle = govintranetpress_custom_title($slot['post_title']);
				$thisURL=$slot['post_name'];
				$taskpod = new Pod('task', $thistask);
				$tasktitle= govintranetpress_custom_title( $slot['post_title'] );			
				$html.= "<li><a href='".$siteurl."/task/" . $thisURL . "'>" . $tasktitle . "</a></li>";
			}
			echo ("<ul>".$html."</ul>");
	}
$cachedga = get_transient('cached_ga');
if ($cachedga) { // if we have a fresh cache


$html='';
	foreach($cachedga as $result) {
		if (strpos($result['pagePath'], "show=") ){
		continue;	
		}

		if ($k>$items-1){
			break;
		}	
		
		$tasktitle='';
		
		if (substr( $result['pagePath'],0,6)=='/task/' && $result['pagePath']!='/task/' && $tasks == 'on' ){ // only show tasks, but not the tasks landing page
			$pathparts = explode("/", $result['pagePath']);
			$thistask = $pathparts[2];
			$tasktitle=false;
			$taskpod = new Pod('task', $thistask);
			$tasktitle= govintranetpress_custom_title( $taskpod->get_field('title') );
			if ( $taskpod->get_field('parent_guide') ){ //if this is a chapter in a guide, we'll display the parent page
				continue;			
			}

			if (!$tasktitle){
				continue;
			}	
			if (in_array($taskpod->get_field('ID'), $alreadydone )) {
				continue;
			}
			
			$k++;
		
		}		
			
		if (substr( $result['pagePath'],0,10)=='/projects/' && $result['pagePath']!='/about/projects/' && $projects == 'on'  ){ // only show projects, but not the projects landing page
			$pathparts = explode("/", $result['pagePath']);
			$thistask = $pathparts[2];		
			$taskpod = new Pod('projects', $thistask);
			$tasktitle= govintranetpress_custom_title( $taskpod->get_field('title') );
			if ( $taskpod->get_field('parent_project')  ){
				continue;			
			}
			if (!$tasktitle){
				continue;
			}	
			if (in_array($taskpod->get_field('ID'), $alreadydone )) {
				continue;
			}
			$k++;

		
		}			

		if (substr( $result['pagePath'],0,11)=='/vacancies/' && $result['pagePath']!='/about/vacancies/' && $vacancies == 'on'  ){ // only show vacancies, but not the vacancies landing page

			$pathparts = explode("/", $result['pagePath']);
			$thistask = $pathparts[2];
			$taskpod = new Pod('vacancies', $thistask);
			$tasktitle= govintranetpress_custom_title( $taskpod->get_field('title') );
			if (!$tasktitle){
				continue;
			}	
			if (in_array($taskpod->get_field('ID'), $alreadydone )) {
				continue;
			}
			$k++;
		
		}			

		if (substr( $result['pagePath'],0,6)=='/news/' && $result['pagePath']!='/news/' && $news == 'on'  ){ // only show news, but not the news landing page
			$pathparts = explode("/", $result['pagePath']);
			$thistask = $pathparts[2];
			$taskpod = new Pod('news', $thistask);
			$tasktitle= govintranetpress_custom_title( $taskpod->get_field('title') );
			if (!$tasktitle){
				continue;
			}	
			if (in_array($taskpod->get_field('ID'), $alreadydone )) {
				continue;
			}
			$k++;
			
		}
		
		if (substr( $result['pagePath'],0,6)=='/blog/' && $result['pagePath']!='/blog/' && $blog == 'on' ){ // only show news, but not the news landing page
			$pathparts = explode("/", $result['pagePath']);
			$thistask = $pathparts[2];
			$taskpod = new Pod('blog', $thistask);
			$tasktitle= govintranetpress_custom_title( $taskpod->get_field('title') );
			if (!$tasktitle){
				continue;
			}	
			if (in_array($taskpod->get_field('ID'), $alreadydone )) {
				continue;
			}
			$k++;
			
		}			

		if (substr( $result['pagePath'],0,7)=='/event/' && $result['pagePath']!='/events/' && $events == 'on' ){ // only show news, but not the news landing page
			$pathparts = explode("/", $result['pagePath']);
			$thistask = $pathparts[2];
			$taskpod = new Pod('event', $thistask);
			$tasktitle= govintranetpress_custom_title( $taskpod->get_field('title') );
			if (!$tasktitle){
				continue;
			}	
			if (in_array($taskpod->get_field('ID'), $alreadydone )) {
				continue;
			}
			$k++;
			
		}			
					
		if ( $pages == 'on' && !$tasktitle ){ // show pages		
			$pathparts = explode("/", $result['pagePath']);
			$countparts = count($pathparts)-2; 
			if ( $countparts < 1 ) $countparts = 0;
			$thistask = $pathparts[intval($countparts)];

			if ( !in_array( $thistask, array('how-do-i','task-by-category','news-by-category','newspage','tagged','atoz','about','home','events','blog','activate','register','members','activity','forums','jargon-buster') ) ){
	
				$q = "select post_title, ID from wp_posts where post_name = '".$thistask."' and post_status='publish' and post_type='page';";
	
				global $wpdb;
				
				$thispage = $wpdb->get_results($q, ARRAY_A);
				foreach ($thispage as $t){ //echo $t['post_title'];
				
					$tasktitle= govintranetpress_custom_title( $t['post_title'] ); 
					$taskid = $t['ID']; 
				}
	
				if (!$tasktitle){
					continue;
				}	
				if (in_array($taskid, $alreadydone )) {
					continue;
				}
				$k++;
			}
		}			


		if ($tasktitle!='' ){
			$html .= "<li><a href='" . $baseurl . $result['pagePath'] . "'>" . $tasktitle . "</a></li>";		
		}

	}
	
}
else // load fresh analytics
{

	ini_set('error_reporting','0'); // disable all, for security


// Developed by Steph Gray http://helpfultechnology.com, December 2009
// Uses the GAPI library by Stig Manning <stig@sdm.co.nz>, version 1.3
	$gs = new Pod ('general_intranet');
	$google_analytics_username = $gs->get_field('google_analytics_email');
	$google_analytics_password = $gs->get_field('google_password');
	$google_analytics_profileid = $gs->get_field('google_analytics_profile_id');
	$days_to_trail = $trail;
	if ($days_to_trail < 1){
		$days_to_trail=1;
	}

	$gis = "general_intranet_time_zone";
	$tzone = get_option($gis);
	date_default_timezone_set($tzone);
	$start_date= date("Y-m-d",time()-(86400*$days_to_trail)); // last x days
	define('ga_email',$google_analytics_username);
	define('ga_password',$google_analytics_password);
	define('ga_profile_id',$google_analytics_profileid);	
	$count = ($conf['tab1']['garesultcount']) ? $conf['tab1']['garesultcount'] : 50;
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
		
		$tasktitle='';
		$filtered_pagepath = str_replace(@explode(",",$conf['tab1']['gatidypaths']),"",$result->getPagePath());
		if (substr( $result->getPagePath(),0,6)=='/task/' && $result->getPagePath()!='/task/' && $tasks == 'on'  ){ // only show tasks, but not the tasks landing page
			$pathparts = explode("/", $result->getPagePath());
			$thistask = $pathparts[2];
			$tasktitle=false;
			$taskpod = new Pod('task', $thistask);
			$taskid = $taskpod->get_field('ID');
			$taskslug = $taskpod->get_field('post_name');
			$tasktitle= govintranetpress_custom_title( $taskpod->get_field('title') );
			if ( $taskpod->get_field('parent_guide') ){
				$parentpod = $taskpod->get_field('parent_guide'); 
				$taskpod = new Pod('task', $parentpod[0]['ID']);
				$taskid = $taskpod->get_field('ID');
				$taskslug = $taskpod->get_field('post_name');
				$tasktitle= govintranetpress_custom_title( $taskpod->get_field('title') );
			}

			if (!$tasktitle){
				continue;
			}	
			if (in_array($taskid, $alreadydone )) {
				continue;
			}
			
			$k++;
			$transga[]['uniquePageviews']=$result->getUniquePageviews();
			$transga[]['pagePath']="/task/".$taskslug;
		
		}		
			
		if (substr( $result->getPagePath(),0,10)=='/projects/' && $result->getPagePath()!='/about/projects/'  && $projects == 'on' ){ // only show projects, but not the projects landing page
			$pathparts = explode("/", $result->getPagePath());
			$thistask = $pathparts[2];		
			$taskpod = new Pod('projects', $thistask);
			$taskid = $taskpod->get_field('ID');
			$taskslug = $taskpod->get_field('post_name');
			$tasktitle= govintranetpress_custom_title( $taskpod->get_field('title') );
			if ( $taskpod->get_field('parent_project')  ){
				$parentpod = $taskpod->get_field('parent_project'); 
				$taskpod = new Pod('projects', $parentpod[0]['ID']);
				$taskid = $taskpod->get_field('ID');
				$taskslug = $taskpod->get_field('post_name');
				$tasktitle= govintranetpress_custom_title( $taskpod->get_field('title') );
			}
			if (!$tasktitle){
				continue;
			}	
			if (in_array($taskid, $alreadydone )) {
				continue;
			}
			$k++;
			$transga[]['uniquePageviews']=$result->getUniquePageviews();
			$transga[]['pagePath']="/projects/".$taskslug;
		
		}			

		if (substr( $result->getPagePath(),0,11)=='/vacancies/' && $result->getPagePath()!='/about/vacancies/'  && $vacancies == 'on' ){ // only show vacancies, but not the vacancies landing page

			$pathparts = explode("/", $result->getPagePath());
			$thistask = $pathparts[2];
			$taskpod = new Pod('vacancies', $thistask);
			$taskid = $taskpod->get_field('ID');			
			$tasktitle= govintranetpress_custom_title( $taskpod->get_field('title') );
			if (!$tasktitle){
				continue;
			}	
			if (in_array($taskpod->get_field('ID'), $alreadydone )) {
				continue;
			}
			$k++;
			$transga[]['uniquePageviews']=$result->getUniquePageviews();
			$transga[]['pagePath']=$result->getPagePath();
		
		}			

		if (substr( $result->getPagePath(),0,6)=='/news/' && $result->getPagePath()!='/news/'  && $news == 'on' ){ // only show news, but not the news landing page
			$pathparts = explode("/", $result->getPagePath());
			$thistask = $pathparts[2];
			$taskpod = new Pod('news', $thistask);
			$taskid = $taskpod->get_field('ID');			
			$tasktitle= govintranetpress_custom_title( $taskpod->get_field('title') );
			if (!$tasktitle){
				continue;
			}	
			if (in_array($taskpod->get_field('ID'), $alreadydone )) {
				continue;
			}
			$k++;
			$transga[]['uniquePageviews']=$result->getUniquePageviews();
			$transga[]['pagePath']=$result->getPagePath();

		}			

		if (substr( $result->getPagePath(),0,6)=='/blog/' && $result->getPagePath()!='/blog/'  && $blog == 'on'  ){ // only show news, but not the news landing page
			$pathparts = explode("/", $result->getPagePath());
			$thistask = $pathparts[2];
			$taskpod = new Pod('blog', $thistask);
			$taskid = $taskpod->get_field('ID');			
			$tasktitle= govintranetpress_custom_title( $taskpod->get_field('title') );
			if (!$tasktitle){
				continue;
			}	
			if (in_array($taskpod->get_field('ID'), $alreadydone )) {
				continue;
			}
			$k++;
			$transga[]['uniquePageviews']=$result->getUniquePageviews();
			$transga[]['pagePath']=$result->getPagePath();

		}			

		if (substr( $result->getPagePath(),0,7)=='/event/' && $result->getPagePath()!='/events/'  && $events == 'on' ){ // only show news, but not the news landing page
			$pathparts = explode("/", $result->getPagePath());
			$thistask = $pathparts[2];
			$taskpod = new Pod('event', $thistask);
			$taskid = $taskpod->get_field('ID');			
			$tasktitle= govintranetpress_custom_title( $taskpod->get_field('title') );
			if (!$tasktitle){
				continue;
			}	
			if (in_array($taskpod->get_field('ID'), $alreadydone )) {
				continue;
			}
			$k++;
			$transga[]['uniquePageviews']=$result->getUniquePageviews();
			$transga[]['pagePath']=$result->getPagePath();

		}			
		
		if ( $pages == 'on' && !$tasktitle ){ // show pages		
			
			$pathparts = explode("/", $result->getPagePath()); 
			$countparts = count($pathparts)-2; 
			$thistask = $pathparts[intval($countparts)];


			if ( !in_array( $thistask, array('how-do-i','task-by-category','news-by-category','newspage','tagged','atoz','about','home','events','blog','activate','register','members','activity','forums','jargon-buster' ) ) ){
	
				$q = "select post_title, ID from wp_posts where post_name = '".$thistask."' and post_status='publish' and post_type='page';";
	
				global $wpdb;
				
				$thispage = $wpdb->get_results($q, ARRAY_A);
				foreach ($thispage as $t){ 
					$tasktitle= govintranetpress_custom_title( $t['post_title'] );
					$taskid = $t['ID'];
				}
	
				if (!$tasktitle){
					continue;
				}	
				if (in_array($taskid, $alreadydone )) {
					continue;
				}
				$k++;
				$transga[]['uniquePageviews']=$result->getUniquePageviews();
				$transga[]['pagePath']=$result->getPagePath();
			} else {
				continue;
			}
		}			
				

		if ($tasktitle!='' ){
			$html .= "<li><a href='" . $baseurl . $result->getPagePath() . "'>" . $tasktitle . "</a></li>";		
			$alreadydone[] = $taskid;
		}

	}
	
	set_transient('cached_ga',$transga,60*60*$cache); // set cache period

}

if ($k==0){
	$html="<li>Looks like nobody has visited the intranet recently.</li>";
}
// end of popular pages


	echo ("<ul id='ga_".$_REQUEST['mode']."'>".$html."</ul>");

							wp_reset_query();								

?>

              <?php echo $after_widget; ?>
        <?php
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
		$instance['trail'] = strip_tags($new_instance['trail']);
		$instance['cache'] = strip_tags($new_instance['cache']);
		delete_transient( 'cached_ga' );
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
          <label for="<?php echo $this->get_field_id('pages'); ?>"><?php _e('Pages'); ?></label> 


        </p>

        <?php 
    }

}

add_action('widgets_init', create_function('', 'return register_widget("htMostActive");'));

?>
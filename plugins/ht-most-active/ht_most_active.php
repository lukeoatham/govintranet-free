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
    }
    
    function widget($args, $instance) {
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        $items = intval($instance['items']);
        $tasks = ($instance['tasks']);
        $projects = ($instance['projects']);
        $vacancies = ($instance['vacancies']);
        $news = ($instance['news']);
        $blog = ($instance['blog']);
        $events = ($instance['events']);
        $trail = intval($instance['trail']);
        $cache = intval($instance['cache']);

       ?>
              <?php echo $before_widget; ?>
                  <?php if ( $title )
                        echo $before_title . $title . $after_title; ?>

<?php
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
				
		$html.= "<li><a href='/task/" . $thisURL . "'>" . $tasktitle . "</a></li>";
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
		
		if (substr( $result['pagePath'],0,6)=='/task/' && $result['pagePath']!='/task/' ){ // only show tasks, but not the tasks landing page
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
			
		if (substr( $result['pagePath'],0,29)=='/about/projects/content/' && $result['pagePath']!='/about/projects/' ){ // only show projects, but not the projects landing page
			$pathparts = explode("/", $result['pagePath']);
			$thistask = $pathparts[4];		
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

		if (substr( $result['pagePath'],0,30)=='/about/vacancies/content/' && $result['pagePath']!='/about/vacancies/' ){ // only show vacancies, but not the vacancies landing page

			$pathparts = explode("/", $result['pagePath']);
			$thistask = $pathparts[4];
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

		if (substr( $result['pagePath'],0,14)=='/news/content/' && $result['pagePath']){ // only show news, but not the news landing page
			$pathparts = explode("/", $result['pagePath']);
			$thistask = $pathparts[3];
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

		if (substr( $result['pagePath'],0,6)=='/blog/' && $result['pagePath']){ // only show blog posts, but not the blog landing page
			$pathparts = explode("/", $result['pagePath']);
			$thistask = $pathparts[2];
			$taskpod = new Pod('blogs', $thistask);
			$tasktitle= govintranetpress_custom_title( $taskpod->get_field('title') );
			if (!$tasktitle){
				continue;
			}	
			if (in_array($taskpod->get_field('ID'), $alreadydone )) {
				continue;
			}
			$k++;

		}			

		if (substr( $result['pagePath'],0,7)=='/event/' && $result['pagePath']){ // only show events, but not the events landing page
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
		$filter.='ga:pagePath=@/about/projects/content/';
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
		$filter.='ga:pagePath=~/about/vacancies/content/';
		$donefilter=true;
	}
	if ($news=='on'){
		if ($donefilter) { $filter.= "||"; 
			
		}
		$filter.='ga:pagePath=~/news/content/';
		$donefilter=true;
	}
	if ($events=='on'){
		if ($donefilter) { $filter.= "||"; 
			
		}
		$filter.='ga:pagePath=~/event/';
		$donefilter=true;
	}
	if ($blog=='on'){
		if ($donefilter) { $filter.= "||"; 
			
		}
		$filter.='ga:pagePath=~/blog/';
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
		
		if (substr( $result->getPagePath(),0,6)=='/task/' && $result->getPagePath()!='/task/' ){ // only show tasks, but not the tasks landing page
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
			
		if (substr( $result->getPagePath(),0,29)=='/about/projects/content/' && $result->getPagePath()!='/about/projects/' ){ // only show projects, but not the projects landing page
			$pathparts = explode("/", $result->getPagePath());
			$thistask = $pathparts[4];		//echo $thistask;
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
		$transga[]['pagePath']="/about/projects/content/".$taskslug;
		
		}			

		if (substr( $result->getPagePath(),0,30)=='/about/vacancies/content/' && $result->getPagePath()!='/about/vacancies/' ){ // only show vacancies, but not the vacancies landing page

			$pathparts = explode("/", $result->getPagePath());
			$thistask = $pathparts[4];
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

		if (substr( $result->getPagePath(),0,14)=='/news/content/' && $result->getPagePath()!='/news/' ){ // only show news, but not the news landing page
			$pathparts = explode("/", $result->getPagePath());
			$thistask = $pathparts[3];
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
		if (substr( $result->getPagePath(),0,6)=='/blog/' && $result->getPagePath()!='/blogs/' ){ // only show news, but not the news landing page
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

		if (substr( $result->getPagePath(),0,7)=='/event/' && $result->getPagePath()!='/events/' ){ // only show news, but not the news landing page
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
//xxx end of popular pages


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
          <label for="<?php echo $this->get_field_id('events'); ?>"><?php _e('Events'); ?></label> 

        </p>

        <?php 
    }

}

add_action('widgets_init', create_function('', 'return register_widget("htMostActive");'));

?>
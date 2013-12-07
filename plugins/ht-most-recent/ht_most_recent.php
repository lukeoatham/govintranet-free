<?php
/*
Plugin Name: HT Most recent
Plugin URI: http://www.helpfultechnology.com
Description: Widget to display most recent pages
Author: Luke Oatham
Version: 0.1
Author URI: http://www.helpfultechnology.com
*/

class htMostRecent extends WP_Widget {
    function htMostRecent() {
        parent::WP_Widget(false, 'HT MostRecent', array('description' => 'Display most recent posts'));

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
        $events = ($instance['event']);
        $lastupdated = ($instance['lastupdated']);

       ?>
              <?php echo $before_widget; ?>
                  <?php if ( $title )
                        echo $before_title . $title . $after_title; ?>



<?php

	$donefilter=false;
	$filter='';
	$siteurl = site_url();
	
	if ($projects=='on'){
		$filter.="post_type = 'projects'";
		$donefilter=true;
	}
	if ($pages=='on'){
		if ($donefilter) { $filter.= " or "; 
			
		}
		$filter.="post_type = 'page'";
		$donefilter=true;
	}
	if ($tasks=='on'){
		if ($donefilter) { $filter.= " or "; 
			
		}
		$filter.="post_type = 'task'";
		$donefilter=true;
	}
	if ($vacancies=='on'){
		if ($donefilter) { $filter.= " or "; 
			
		}
		$filter.="post_type = 'vacancies'";
		$donefilter=true;
	}
	if ($news=='on'){
		if ($donefilter) { $filter.= " or "; 
			
		}
		$filter.="post_type = 'news'";
		$donefilter=true;
	}
	if ($blog=='on'){
		if ($donefilter) { $filter.= " or "; 
			
		}
		$filter.="post_type = 'blog'";
		$donefilter=true;
	}
	if ($events=='on'){
		if ($donefilter) { $filter.= " or "; 
			
		}
		$filter.="post_type = 'event'";
		$donefilter=true;
	}
	if ($lastupdated=='on'){
		$checkdate = 'post_modified';
	} else {	
		$checkdate = 'post_date';
	}
	

	$q = "
	select * 
	from wp_posts 
	where (".$filter.") and post_status = 'publish'
	order by ".$checkdate." desc
	limit 50;
	";
	global $wpdb;
	$rpublished = $wpdb->get_results( $q );
															
	echo "<ul>";
	$k = 0;
	foreach ($rpublished as $r ) {
		if ($r->post_type=='page'){
			if ( in_array( $r->post_name, array('how-do-i','task-by-category','news-by-category','newspage','tagged','atoz','about','home','blogs','events') ) ){
				continue;
			} 
			$k++;
			echo "<li><a href='".$r->guid."'>".govintranetpress_custom_title(get_the_title($r->ID))."</a></li>";
		}
		if ($r->post_type=='task'){
			$taskpod = new Pod('task', $r->ID); 
			if ($taskpod->get_field('page_type') == 'Task'){ 
				$k++;
				echo "<li><a href='".$siteurl."/task/".$taskpod->get_field('slug')."/'>".govintranetpress_custom_title($taskpod->get_field('post_title'))."</a></li>";
				} else 	{
				if ($taskpod->get_field('page_type') == 'Guide header'){
				$k++;
				echo "<li><a href='".$siteurl."/task/".$taskpod->get_field('slug')."/'>".govintranetpress_custom_title($taskpod->get_field('post_title'))."</a></li>";
				} else {
				$k++;
				$pguide = $taskpod->get_field('parent_guide'); 
				$ptaskpod = new Pod ('task', $pguide[0]['ID']);
				echo "<li><a href='".$siteurl."/task/".$taskpod->get_field('slug')."/'>".govintranetpress_custom_title($taskpod->get_field('post_title'))." <small>(".govintranetpress_custom_title($ptaskpod->get_field('post_title')).")</small></a></li>";
					
				}
			}
		}								
		if ($r->post_type=='projects'){
			$projpod = new Pod('projects', $r->ID);
			if (!$projpod->get_field('parent_project')){
				$k++;
				echo "<li><a href='".$siteurl."/projects/".$projpod->get_field('slug')."/'>".govintranetpress_custom_title($projpod->get_field('post_title'))."</a></li>";
			}
		}
		if ($r->post_type=='vacancies'){
			$k++;
			$vacpod = new Pod('vacancies', $r->ID);
			echo "<li><a href='".$siteurl."/vacancies/".$vacpod->get_field('slug')."/'>".govintranetpress_custom_title($vacpod->get_field('post_title'))."</a></li>";
		}
		if ($r->post_type=='news'){
			$k++;
			$vacpod = new Pod('news', $r->ID);
			echo "<li><a href='".$siteurl."/news/".$vacpod->get_field('slug')."/'>".govintranetpress_custom_title($vacpod->get_field('post_title'))."</a></li>";
		}
		if ($r->post_type=='blog'){
			$k++;
			$vacpod = new Pod('blog', $r->ID);
			echo "<li><a href='".$siteurl."/blog/".$vacpod->get_field('slug')."/'>".govintranetpress_custom_title($vacpod->get_field('post_title'))."</a></li>";
		}
		if ($r->post_type=='event'){
			$k++;
			$vacpod = new Pod('event', $r->ID);
			echo "<li><a href='".$siteurl."/event/".$vacpod->get_field('slug')."/'>".govintranetpress_custom_title($vacpod->get_field('post_title'))."</a></li>";
		}
		
		if ($k == $items) {
			break;
		}			
	}
echo "</ul>";
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
		$instance['lastupdated'] = strip_tags($new_instance['lastupdated']);
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
        $lastupdated = esc_attr($instance['lastupdated']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /><br><br>

          <label for="<?php echo $this->get_field_id('items'); ?>"><?php _e('Number of items:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('items'); ?>" name="<?php echo $this->get_field_name('items'); ?>" type="text" value="<?php echo $items; ?>" /><br><br>
          
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
          <label for="<?php echo $this->get_field_id('pages'); ?>"><?php _e('Pages'); ?></label> <br><br>


<label>Updates:</label><br>
        
          <input id="<?php echo $this->get_field_id('lastupdated'); ?>" name="<?php echo $this->get_field_name('lastupdated'); ?>" type="checkbox" <?php checked((bool) $instance['lastupdated'], true ); ?> />
          <label for="<?php echo $this->get_field_id('lastupdated'); ?>"><?php _e('Show again when updated'); ?></label> 

        </p>

        <?php 
    }

}

add_action('widgets_init', create_function('', 'return register_widget("htMostRecent");'));

?>
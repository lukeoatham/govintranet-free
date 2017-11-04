<?php
/*
Plugin Name: HT Most recent
Plugin URI: http://www.helpfultechnology.com
Description: Widget to display most recent pages
Author: Luke Oatham
Version: 1.2
Author URI: http://www.helpfultechnology.com
*/

class htMostRecent extends WP_Widget {

	function __construct() {
		
		parent::__construct(
			'htMostRecent',
			__( 'HT Most Recent' , 'govintranet'),
			array( 'description' => __( 'Display most recent posts' , 'govintranet') )
		);   
		
		if( function_exists('register_field_group') ):
		
		register_field_group(array (
			'key' => 'group_54c30b1243bd6',
			'title' => _x('Most recent widget','Widget that lists the most recent posts','govintranet'),
			'fields' => array (
				array (
					'key' => 'field_54c30b1d69d24',
					'label' => __('Exclude','govintranet'),
					'name' => 'exclude_posts',
					'prefix' => '',
					'type' => 'relationship',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
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
						'value' => 'htmostrecent',
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
        $pages = $instance['pages'];
        $tasks = $instance['tasks'];
        $projects = $instance['projects'];
        $vacancies = $instance['vacancies'];
        $news = $instance['news'];
        $blog = $instance['blog'];
        $events = $instance['events'];
        $lastupdated = $instance['lastupdated'];

		$acf_key = "widget_" . $this->id_base . "-" . $this->number . "_exclude_posts" ;  
		$excludeposts = get_option($acf_key);  
		$exclude=array();
		if ($excludeposts) foreach ($excludeposts as $sp){
			$exclude[] = "'" . $sp . "'";
		}
	             
		$donefilter=false;
		$filter='';
		
		if ($projects=='on'){
			$filter.="post_type = 'project'";
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
			$filter.="post_type = 'vacancy'";
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
		
		if ( !$filter ) $filter="post_type = 'task'";
	
		$stoppages = array('how-do-i','task-by-category','news-by-category','newspage','tagged','atoz','about','home','blogs','events','category','news-type',); 
		foreach ($stoppages as $sp){
			$stop = get_page_by_path($sp, ARRAY_A, 'page');
			if ($stop) $exclude[] = "'" . $stop['ID'] . "'";
		}
		$getitems = $items + count($exclude); 
		global $wpdb;
		$q = "
		select ID, post_parent, post_type, post_title, post_name, post_date, post_modified, post_status from $wpdb->posts where (".$filter.") and post_status = 'publish'";
		
		if ( $exclude ) $q.=" and ID NOT IN (" . implode(',' ,$exclude) . ") "; 
	
		$q.= "order by ".$checkdate." desc
		limit ".$getitems.";"; 
		$rpublished = $wpdb->get_results( $q );
																
		$k = 0;
		$alreadydone=array();
		if ( $rpublished ):
			echo $before_widget;
			if ( $title ) echo $before_title . $title . $after_title; 
			echo "<ul>";
			foreach ($rpublished as $r ) {
				if (in_array($r->ID, $alreadydone)) continue;
				$alert='';
				if ( $lastupdated=='on' && date("Ymd",strtotime($r->post_date)) == date("Ymd",strtotime($r->post_modified)) ) $alert = " <span class='badge badge-new'>" . __('NEW','govintranet') . "</span>";
				if ($r->post_type=='page'){
					$k++; 
					echo "<li><a href='".get_permalink($r->ID)."'>".govintranetpress_custom_title(get_the_title($r->ID))."</a>".$alert."</li>";
					$alreadydone[]=$r->ID;
				} elseif ($r->post_type=='task'){
					$title_context='';
					if ($r->post_parent){ // child chapter
						$icon = "book";
						$taskparent=get_post($r->post_parent);
						$title_context='';
						if ($taskparent){
							$parent_guide_id = $taskparent->ID; 		
							$title_context=" <small>(".govintranetpress_custom_title($taskparent->post_title).")</small>"; 
						}
					} elseif ( get_posts ("post_type=task&posts_per_page=-1&post_status=publish&post_parent=".$r->ID."&orderby=menu_order&order=ASC") ){
						$icon = "book";
					} else {
						$icon = "question-sign";
					}			
		
					$k++;
					echo "<li><a href='".get_permalink($r->ID)."'>".govintranetpress_custom_title($r->post_title)."</a>".$title_context.$alert."</li>";
					$alreadydone[]=$r->ID;
				} elseif ($r->post_type=='project'){
					if (!$r->post_parent){
						$k++;
						echo "<li><a href='".get_permalink($r->ID)."'>".govintranetpress_custom_title($r->post_title)."</a>".$alert."</li>";
						$alreadydone[]=$r->ID;
					}
				} else {
					$k++;
					echo "<li><a href='".get_permalink($r->ID)."'>".govintranetpress_custom_title($r->post_title)."</a>".$alert."</li>";
					$alreadydone[]=$r->ID;
				}
				
				if ($k == $items) {
					break;
				}			
			}
			echo "</ul>";
			wp_reset_query();								
			echo $after_widget; 
		endif;
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
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','govintranet'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /><br><br>

          <label for="<?php echo $this->get_field_id('items'); ?>"><?php _e('Number of items:','govintranet'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('items'); ?>" name="<?php echo $this->get_field_name('items'); ?>" type="text" value="<?php echo $items; ?>" /><br><br>
          
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
          <label for="<?php echo $this->get_field_id('pages'); ?>"><?php _e('Pages','govintranet'); ?></label> <br><br>


<label><?php _ex('Updates','noun','govintranet')?>:</label><br>
        
          <input id="<?php echo $this->get_field_id('lastupdated'); ?>" name="<?php echo $this->get_field_name('lastupdated'); ?>" type="checkbox" <?php checked((bool) $instance['lastupdated'], true ); ?> />
          <label for="<?php echo $this->get_field_id('lastupdated'); ?>"><?php _e('Show again when updated','govintranet'); ?></label> 

        </p>

        <?php 
    }

}

add_action('widgets_init', create_function('', 'return register_widget("htMostRecent");'));

?>
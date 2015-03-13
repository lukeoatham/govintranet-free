<?php
/*
Plugin Name: HT About this page
Plugin URI: http://www.helpfultechnology.com
Description: Widget to display page information in the footer
Author: Luke Oatham
Version: 1.2
Author URI: http://www.helpfultechnology.com
*/

class htAboutThisPage extends WP_Widget {
    function htAboutThisPage() {
        parent::WP_Widget(false, 'HT About this page', array('description' => 'Display page metadata'));

		if( function_exists('register_field_group') ):
		
		register_field_group(array (
			'key' => 'group_54c8168128e72',
			'title' => 'About this page widget',
			'fields' => array (
				array (
					'key' => 'field_54c816872d1e0',
					'label' => 'Display also on children of:',
					'name' => 'about_this_page_children',
					'prefix' => '',
					'type' => 'relationship',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'post_type' => array (
						0 => 'page',
					),
					'taxonomy' => '',
					'filters' => array (
						0 => 'search',
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
						'value' => 'htaboutthispage',
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
        $show_modified_date = ($instance['show_modified_date']);
        $show_published_date = ($instance['show_published_date']);
        $show_author = ($instance['show_author']);
	        
		$showabout = false;
		if ( is_single() ) $showabout = true; 		
		if ( is_singular(array('forum','topic','reply'))) $showabout = false;

		if ( is_page() ):
			$acf_key = "widget_" . $this->id_base . "-" . $this->number . "_about_this_page_children" ;  
			$aboutChildren = get_option($acf_key); 
			global $post; 
			$my_wp_query = new WP_Query();
			$all_wp_pages = $my_wp_query->query(array('post_type' => 'page','posts_per_page'=>-1));

			if ($aboutChildren) foreach ($aboutChildren as $a){ 
								
				// Filter through all pages and find Portfolio's children
				$children = get_page_children( $a, $all_wp_pages );

				if ($children) foreach ($children as $c){ 
					$child[]=$c->ID; if ($post->ID == $c->ID) $showabout = true; 
				}
				
			}
		endif;

		if ($showabout) {

			echo $before_widget; 
			echo "<div id='about-this-widget'>";
			if ( $title )
				echo $before_title . $title . $after_title; 
				$tzone = get_option('timezone_string');
				date_default_timezone_set($tzone);

			if ($show_modified_date=='on'){
				$sdate = human_time_diff_plus(get_the_modified_time('U'));
				if ($sdate=="0 mins") {
					$sdate=" just now";
				} else {
					$sdate = $sdate." ago";
				}

				echo "Updated <time datetime='".$sdate."'>".$sdate."</time><br>";
			}

			if ($show_published_date=='on'){
				$sdate= date( "j M Y",strtotime(get_the_date() )); 
				$sdate = human_time_diff_plus(get_the_time('U'));
				if ($sdate=="0 mins") {
					$sdate=" just now";
				} else {
					$sdate = $sdate." ago";
				}
				echo "Published <time datetime='".$sdate."'>".$sdate."</time><br>";
			}

			if ($show_author=='on'){
				$useremail = get_the_author_meta('user_email');
				echo "<a href='mailto:".$useremail."'>";
				the_author();
				echo "</a>";
			}

		echo "</div>";
		echo $after_widget; 
		}						

    }

    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['show_modified_date'] = strip_tags($new_instance['show_modified_date']);
		$instance['show_published_date'] = strip_tags($new_instance['show_published_date']);
		$instance['show_author'] = strip_tags($new_instance['show_author']);

       return $instance;
    }

    function form($instance) {
        $title = esc_attr($instance['title']);
        $show_modified_date = esc_attr($instance['show_modified_date']);
        $show_published_date = esc_attr($instance['show_published_date']);
        $show_author = esc_attr($instance['show_author']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />

          
          <label>Include:</label><br>

          <input id="<?php echo $this->get_field_id('show_modified_date'); ?>" name="<?php echo $this->get_field_name('show_modified_date'); ?>" type="checkbox" <?php checked((bool) $instance['show_modified_date'], true ); ?> />
          <label for="<?php echo $this->get_field_id('show_modified_date'); ?>"><?php _e('Modified date'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('show_published_date'); ?>" name="<?php echo $this->get_field_name('show_published_date'); ?>" type="checkbox" <?php checked((bool) $instance['show_published_date'], true ); ?> />
          <label for="<?php echo $this->get_field_id('show_published_date'); ?>"><?php _e('Published date'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('show_author'); ?>" name="<?php echo $this->get_field_name('show_author'); ?>" type="checkbox" <?php checked((bool) $instance['show_author'], true ); ?> />
          <label for="<?php echo $this->get_field_id('show_author'); ?>"><?php _e('Author'); ?></label> <br>

        </p>

        <?php 
    }

}

add_action('widgets_init', create_function('', 'return register_widget("htAboutThisPage");'));

?>
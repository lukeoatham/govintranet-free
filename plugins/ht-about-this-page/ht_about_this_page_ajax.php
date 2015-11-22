<?php
/*
Plugin Name: HT About this page AJAX
Plugin URI: http://www.helpfultechnology.com
Description: Widget to display page owner information in the footer
Author: Luke Oatham
Version: 1.0
Author URI: http://www.helpfultechnology.com
*/

class htaboutthispageajax extends WP_Widget {
    function htaboutthispageajax() {
        parent::WP_Widget(false, __('HT About this page AJAX','govintranet'), array('description' => __('Display page metadata','govintranet')));

		if( function_exists('register_field_group') ):
		
		register_field_group(array (
			'key' => 'group_54c8168128e72',
			'title' => __('About this page widget','govintranet'),
			'fields' => array (
				array (
					'key' => 'field_54c816872d1e0',
					'label' => __('Display also on children of:','govintranet'),
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
						'value' => 'htaboutthispageajax',
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
        global $post;

		$tzone = get_option('timezone_string');
		date_default_timezone_set($tzone);
		
		$sdate = human_time_diff_plus(get_the_modified_time('U'));
		$pdate = human_time_diff_plus(date("U",strtotime($post->post_date)));
		
		$userid = $post->post_author;
		$acf_key = "widget_" . $this->id_base . "-" . $this->number . "_about_this_page_children" ;  
		$aboutChildren = get_option($acf_key); 

		if ( is_single() ) $single = 'true'; 		
		if ( is_singular(array('forum','topic','reply'))) $single_forum = 'true';
		$page = is_page();
		$showabout = false;
		if ( $single ) $showabout = true; 		
		if ( $single_forum ) $showabout = false;

		if ( $page ):
			$my_wp_query = new WP_Query();
			$all_wp_pages = $my_wp_query->query(array('post_type' => 'page','posts_per_page'=>-1));
			if ($aboutChildren) foreach ((array)$aboutChildren as $a){ 
								
				// Filter through all pages and find Portfolio's children
				$children = get_page_children( $a, $all_wp_pages );
				if ($children) foreach ($children as $c){ 
					$child[]=$c->ID; if ($post->ID == $c->ID) $showabout = true; 
				}
				
			}
		endif;

        $path = plugin_dir_url( __FILE__ );

        wp_enqueue_script( 'ht_about_this_page_ajax', $path.'ht_about_this_page_ajax.js' );
        $protocol = isset( $_SERVER["HTTPS"]) ? 'https://' : 'http://';
        $params = array(
        'ajaxurl' => admin_url( 'admin-ajax.php', $protocol ),
		'title' => $title,
		'before_widget' => stripcslashes($before_widget),
		'after_widget' => stripcslashes($after_widget),
		'before_title' => stripcslashes($before_title),
		'after_title' => stripcslashes($after_title),
		'show_modified_date' => $show_modified_date,
		'show_published_date' => $show_published_date,
		'show_author' => $show_author,
		'single' => $single,
		'single_forum' => $single_forum,
		'page' => $page,
		'sdate' => $sdate,
		'pdate' => $pdate,
		'userid' => $userid,
		'showabout' => $showabout,
          
        );
        wp_localize_script( 'ht_about_this_page_ajax', 'ht_about_this_page_ajax', $params );

		echo "<div id='ht_about_this_page_ajax' class='ht_about_this_page_ajax'></div>";

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
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','govintranet'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
          
          <label><?php _e('Include','govintranet'); ?>:</label><br>

          <input id="<?php echo $this->get_field_id('show_modified_date'); ?>" name="<?php echo $this->get_field_name('show_modified_date'); ?>" type="checkbox" <?php checked((bool) $instance['show_modified_date'], true ); ?> />
          <label for="<?php echo $this->get_field_id('show_modified_date'); ?>"><?php _e('Modified date','govintranet'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('show_published_date'); ?>" name="<?php echo $this->get_field_name('show_published_date'); ?>" type="checkbox" <?php checked((bool) $instance['show_published_date'], true ); ?> />
          <label for="<?php echo $this->get_field_id('show_published_date'); ?>"><?php _e('Published date','govintranet'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('show_author'); ?>" name="<?php echo $this->get_field_name('show_author'); ?>" type="checkbox" <?php checked((bool) $instance['show_author'], true ); ?> />
          <label for="<?php echo $this->get_field_id('show_author'); ?>"><?php _e('Author','govintranet'); ?></label> <br>

        </p>

        <?php 
    }

}

add_action( 'wp_ajax_ht_about_this_page_ajax_show', 'ht_about_this_page_ajax_show' );
add_action( 'wp_ajax_nopriv_ht_about_this_page_ajax_show', 'ht_about_this_page_ajax_show' );
function ht_about_this_page_ajax_show() {

	$title = esc_attr($_POST['title']);
	$before_widget = stripcslashes($_POST['before_widget']);
	$after_widget = stripcslashes($_POST['after_widget']);
	$before_title = stripcslashes($_POST['before_title']);
	$after_title = stripcslashes($_POST['after_title']);
    $show_modified_date = $_POST['show_modified_date'];
    $show_published_date = $_POST['show_published_date'];
    $show_author = $_POST['show_author'];
    $single = $_POST['single'];
    $single_forum = $_POST['single_forum'];
	$page = $_POST['page'];
	$sdate = $_POST['sdate'];
	$pdate = $_POST['pdate'];
	$userid = $_POST['userid'];
	$showabout = $_POST['showabout'];
	
    $response = new WP_Ajax_Response;

	if ($showabout) {

		$html.= $before_widget; 
		$html.= "<div id='about-this-widget'>";
		if ( $title )
			$html.= $before_title . $title . $after_title; 

		if ($show_modified_date=='on'){
			if ($sdate=="0 mins") {
				$sdate=" " . __("just now","govintranet");
			} else {
				$sdate = sprintf( __('%s ago','govintranet'), $sdate );
			}

			$html.= __('Updated','govintranet') . " <time datetime='".$sdate."'>".$sdate."</time><br>";
		}

		if ($show_published_date=='on'){
			if ($pdate=="0 mins") {
				$pdate=" " . __("just now","govintranet");
			} else {
				$pdate = sprintf( __('%s ago','govintranet'), $pdate );
			}
			$html.= __('Published','govintranet') . " <time datetime='".$pdate."'>".$pdate."</time><br>";
		}

		if ($show_author=='on'){
			$user = get_user_by("id", $userid); 
			$html.= "<span class='dashicons dashicons-email-alt'></span> <a href='mailto:".$user->user_email."'>";
			$html.= $user->display_name;
			$html.= "</a>";
		}

		$html.= "</div>";
		$html.= $after_widget; 
		}						

    if ( $html ) {
        // Request successful
        $response->add( array(
            'data' => 'success',
            'supplemental' => array(
                'message' => $html
            ),
        ) );
    } else {
        // Request failed
        $response->add( array(
            'data' => 'error',
            'supplemental' => array(
                'message' => __('Error - no data','govintranet')
            ),
        ) );
    }

    $response->send();
    
    exit();
}

add_action('widgets_init', create_function('', 'return register_widget("htaboutthispageajax");'));

?>
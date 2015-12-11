<?php
/*
Plugin Name: HT Favourites
Plugin URI: http://www.helpfultechnology.com
Description: Manage favourites in staff profiles
Author: Luke Oatham
Version: 1.0
Author URI: http://www.helpfultechnology.com
*/

class htfavourites_add extends WP_Widget {
    function htfavourites_add() {
        parent::WP_Widget(false, __('HT Add to favourites','govintranet'), array('description' => __('Displays a button to add to favourites','govintranet')));

		/*
		Load css
		*/
		wp_enqueue_style( 'favourites', plugins_url("/ht-favourites/css/ht_favourites.css"));
        
    }

    function widget($args, $instance) {
	    
	    if ( is_user_logged_in() ):

		    global $post;
		    $post_id = $post->ID;
	        extract( $args ); 
	        $widget_id = $id. "-" . $this->number;
			$user_id = get_current_user_id();
			$faves = get_user_meta($user_id, 'user_favourites', true);
			$nonce = wp_create_nonce ('ht_favourites_add_'.$widget_id);
	        
	        $path = plugin_dir_url( __FILE__ );
	
	        wp_enqueue_script( 'ht_favourites', $path.'js/ht_favourites.js' );
	        $protocol = isset( $_SERVER["HTTPS"]) ? 'https://' : 'http://';
	        $params = array(
	        'ajaxurl' => admin_url( 'admin-ajax.php', $protocol ),
			'before_widget' => stripcslashes($before_widget),
			'after_widget' => stripcslashes($after_widget),
	        'widget_id' => $widget_id,
	        'post_id' => $post_id,
	        'user_id' => $user_id,
	        'nonce' => $nonce,
	        'spinner' => $path.'images/small-squares.gif',
	        );
	        wp_localize_script( 'ht_favourites', 'ht_favourites', $params );
	
			echo "<div id='ht_favourites_".$widget_id."' class='ht_favourites'>";
			if ( !in_array($post->ID, $faves) ):
				$html = "<a onclick='javascript:addtofav();' class='ht_addtofav btn btn-default'>".__('Add to favourites','govintranet')."</a>";
			else:
				$html = "<a onclick='javascript:delfav();' class='ht_addtofav btn btn-default'>".__('Remove from favourites','govintranet')."</a>";
			endif;
			echo $html;
			echo "</div>";
	
			wp_reset_postdata();		

		endif;				

    }

    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		return $instance;
    }

    function form($instance) {
        echo "<p>" . __('No options','govintranet') . "</p>";
    }
}

class htfavourites_display extends WP_Widget {
    function htfavourites_display() {
        parent::WP_Widget(false, __('HT Display favourites','govintranet'), array('description' => __('Display favourites from staff profile','govintranet')));

		/*
		Load css
		*/
		wp_enqueue_style( 'favourites', plugins_url("/ht-favourites/css/ht_favourites.css"));
        
    }

    function widget($args, $instance) { 
	    
		if ( is_user_logged_in() ): 
		    $user_id = get_current_user_id();
		    $user_info = get_userdata($user_id);
	        extract( $args ); 
			$title = apply_filters('widget_title', $instance['title']);	        
	        $widget_id = $id. "-" . $this->number;
			$path = plugin_dir_url( __FILE__ );

	        wp_enqueue_script( 'ht_favourites', $path.'js/ht_favourites.js' );
	        $protocol = isset( $_SERVER["HTTPS"]) ? 'https://' : 'http://';
	        $params = array(
	        'ajaxurl' => admin_url( 'admin-ajax.php', $protocol ),
			'before_widget' => stripcslashes($before_widget),
			'after_widget' => stripcslashes($after_widget),
			'before_title' => stripcslashes($before_title),
			'after_title' => stripcslashes($after_title),
	        'user_id' => $user_id,
	        'widget_id' => $widget_id,
	        'title' => $title,
	        );
	        wp_localize_script( 'ht_favourites', 'ht_favourites', $params );

	        
			echo "<div id='ht_favourites_display_".$widget_id."' class='ht_favourites'></div>";
				        
		endif;
    }

    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
    }

    function form($instance) {
        $title = esc_attr($instance['title']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','govintranet'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /><br>
        </p>
        <?php 
    }
}
add_action('widgets_init', create_function('', 'return register_widget("htfavourites_add");'));
add_action('widgets_init', create_function('', 'return register_widget("htfavourites_display");'));

add_action( 'wp_ajax_ht_favourites_ajax_add', 'ht_favourites_ajax_add' );
add_action( 'wp_ajax_nopriv_ht_favourites_ajax_add', 'ht_favourites_ajax_add' );
function ht_favourites_ajax_add() {

	$before_widget = stripcslashes($_POST['before_widget']);
	$after_widget = stripcslashes($_POST['after_widget']);
	$before_title = stripcslashes($_POST['before_title']);
	$after_title = stripcslashes($_POST['after_title']);
	$widget_id = $_POST['widget_id'];
	$post_id = $_POST['post_id'];
	$user_id = $_POST['user_id'];
	$nonce = $_POST['nonce'];
    $response = new WP_Ajax_Response;
			
	
	if ($html){
		$finalhtml = "";//$before_widget;
		$finalhtml.= $html;
		$finalhtml.= "<div class='clearfix'></div>";
		$finalhtml.= "";//$after_widget;
	}

    if( $finalhtml  ) {
        // Request successful
        $response->add( array(
            'data' => 'success',
            'supplemental' => array(
                'message' => $finalhtml
            ),
        ) );
    } else {
        // Request failed
        $response->add( array(
            'data' => 'error',
            'supplemental' => array(
                'message' => 'an error occurred'
            ),
        ) );
    }

    $response->send();
    
    exit();
}
  

add_action('widgets_init', 'ht_favourites_register');

function ht_favourites_register() {
		/*
		Register custom user fields
		*/
        
		if( function_exists('acf_add_local_field_group') ):
		
		acf_add_local_field_group(array (
			'key' => 'group_5669acf63093d',
			'title' => 'Favourites',
			'fields' => array (
				array (
					'key' => 'field_5669ad29841d0',
					'label' => 'My favourites',
					'name' => 'user_favourites',
					'type' => 'relationship',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'post_type' => array (
						0 => 'page',
						1 => 'team',
						2 => 'project',
						3 => 'task',
						4 => 'forum',
					),
					'taxonomy' => array (
					),
					'filters' => array (
						0 => 'search',
						1 => 'post_type',
					),
					'elements' => '',
					'min' => '',
					'max' => '',
					'return_format' => 'id',
				),
			),
			'location' => array (
				array (
					array (
						'param' => 'user_form',
						'operator' => '==',
						'value' => 'edit',
					),
				),
			),
			'menu_order' => 100,
			'position' => 'normal',
			'style' => 'default',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => '',
			'active' => 1,
			'description' => '',
		));
		
		endif;

}

add_action( 'wp_ajax_ht_favourites_ajax_action_add', 'ht_favourites_ajax_action_add' );
function ht_favourites_ajax_action_add() {
	$nonce = $_POST['nonce']; 	
	$widget_id = $_POST['widget_id'];
	$post_id = $_POST['post_id'];
    $response = new WP_Ajax_Response;
	global $current_user;
	$success = false;
	$current_user = wp_get_current_user();
	if ($current_user->ID) $user_id = $current_user->ID;
	if ( ! wp_verify_nonce( $nonce,  'ht_favourites_add_'.$widget_id ) ) {
	    // This nonce is not valid.
	    $html =  __("Security check - there is something wrong","govintranet") ; 
	} else {
	    // The nonce was valid.
	    // Do stuff here.
			$user_id = $_POST['user_id'];
			$current_user = wp_get_current_user();
			$current_user_id = $current_user->ID; 
			//
			if ($user_id!=$current_user_id){
			    $html =  __("Security check - can\'t check your identity","govintranet") ; 	
			} else {
				$faves = get_user_meta($user_id, 'user_favourites', true);
				if ( !in_array($post_id, $faves ) ) $faves[] = $post_id;
			    update_user_meta($current_user_id,'user_favourites', $faves); 
				$html = '<div class="ht_addtofav btn btn-default">' . __('Added','govintranet') . '</div>';
				$success = true;
			}
	}
   if( $success  ) {
        // Request successful
        $response->add( array(
            'data' => 'success',
            'supplemental' => array(
                'message' => $html,
            ),
        ) );
    } else {
        // Request failed
        $response->add( array(
            'data' => 'error',
            'supplemental' => array(
                'message' => '<div class="alert alert-danger">' . $html . '</div>',
            ),
        ) );
    }
    $response->send();
    exit();
}

add_action( 'wp_ajax_ht_favourites_ajax_action_del', 'ht_favourites_ajax_action_del' );
function ht_favourites_ajax_action_del() {
	$nonce = $_POST['nonce']; 	
	$widget_id = $_POST['widget_id'];
	$post_id = $_POST['post_id'];
    $response = new WP_Ajax_Response;
	global $current_user;
	$success = false;
	$current_user = wp_get_current_user();
	if ($current_user->ID) $user_id = $current_user->ID;
	if ( ! wp_verify_nonce( $nonce,  'ht_favourites_add_'.$widget_id ) ) {
	    // This nonce is not valid.
	    $html =  __("Security check - there is something wrong","govintranet") ; 
	} else {
	    // The nonce was valid.
	    // Do stuff here.
			$user_id = $_POST['user_id'];
			$current_user = wp_get_current_user();
			$current_user_id = $current_user->ID; 
			//
			if ($user_id!=$current_user_id){
			    $html =  __("Security check - can\'t check your identity","govintranet") ; 	
			} else {
				$faves = get_user_meta($user_id, 'user_favourites', true);
				$newfaves = array();
				$fcount = 0;
				foreach ( $faves as $f ){
					$fcount++;
					if ( $f == $post_id ) continue;
					$newfaves[] = $f;
				}
			    update_user_meta($current_user_id,'user_favourites', $newfaves); 
				$html = '<div class="ht_addtofav btn btn-default">' . __('Removed','govintranet') . '</div>';
				$success = true;
			}
	}
   if( $success  ) {
        // Request successful
        $response->add( array(
            'data' => 'success',
            'supplemental' => array(
                'message' => $html,
            ),
        ) );
    } else {
        // Request failed
        $response->add( array(
            'data' => 'error',
            'supplemental' => array(
                'message' => '<div class="alert alert-danger">' . $html . '</div>',
            ),
        ) );
    }
    $response->send();
    exit();
}


function ht_favourites_ajax_show() {

	$before_widget = stripcslashes($_POST['before_widget']);
	$after_widget = stripcslashes($_POST['after_widget']);
	$before_title = stripcslashes($_POST['before_title']);
	$after_title = stripcslashes($_POST['after_title']);
	$title = stripcslashes($_POST['title']);
	$user_id = $_POST['user_id'];
	$widget_id = $_POST['widget_id'];
	$faves = get_user_meta($user_id, 'user_favourites', true); 
    $response = new WP_Ajax_Response;

	$html = "";
	if ( count($faves) > 0 && is_array($faves) ):			
		$html.= $before_widget; 
		$userurl = site_url().'/staff/'.$user_info->user_nicename;
		$userurl = get_author_posts_url( $user_id ); 
		$gis = "options_forum_support";
		$forumsupport = get_option($gis);
		$staffdirectory = get_option('options_module_staff_directory');
		if (function_exists('bp_activity_screen_index')){ // if using BuddyPress - link to the members page
			$userurl=str_replace('/author', '/members', $userurl); }
		elseif (function_exists('bbp_get_displayed_user_field') && $staffdirectory ){ // if using bbPress - link to the staff page
			$userurl=str_replace('/author', '/staff', $userurl);
		}
		$userurl.="edit/#acf-field_5669ad29841d0";
		$html.= "<a href='".$userurl."' class='btn btn-sm btn-default pull-right editfav'>Edit</a>";
		if ( $title ) $html.= $before_title . $title . $after_title; 
		$html.="<ul>";
		foreach ( $faves as $r ){
			$title_context="";
			$rlink = get_post($r);
			if ($rlink->post_status == 'publish' ) {
				$taskparent=$rlink->post_parent; 
				if ($taskparent){
					$taskparent = get_post($taskparent);
					$title_context=" (".govintranetpress_custom_title($taskparent->post_title).")";
				}		
				$ext_icon = '';
				$ext = '';
				if ( get_post_format($r) == 'link' ):
					$ext_icon = " <span class='dashicons dashicons-migrate'></span> ";
					$ext="class='external-link' ";
				endif;
				$html.= "<li><a href='".get_permalink($rlink->ID)."'".$ext.">".govintranetpress_custom_title($rlink->post_title).$title_context."</a>".$ext_icon."</li>";
				$alreadydone[] = $r;
			}
		}
		$html.= "</ul>";
		$html.= $after_widget; 	
		wp_reset_postdata();		
	endif;
	

	if( $html ) {
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
                'message' => 'an error occurred'
            ),
        ) );
    }
    $response->send();
    exit();

}

add_action( 'wp_ajax_ht_favourites_ajax_show', 'ht_favourites_ajax_show' );
add_action( 'wp_ajax_nopriv_ht_favourites_ajax_show', 'ht_favourites_ajax_show' );

?>
<?php
/*
Plugin Name: HT Notify
Plugin URI: http://www.helpfultechnology.com
Description: Manage notifications in staff profiles. Includes a widget to add a Notify button, extra fields in the staff profile, and a cron job to send notifications.
Author: Luke Oatham
Version: 1.0
Author URI: http://www.helpfultechnology.com
*/

class htnotify_add extends WP_Widget {

	function __construct() {
		
		parent::__construct(
			'htnotify_add',
			__( 'HT Add to notifications' , 'govintranet'),
			array( 'description' => __( 'Displays a button to add to notifications' , 'govintranet') )
		);   
		
		/*
		Load css
		*/
		wp_enqueue_style( 'notify', plugins_url("/ht-notify/css/ht_notify.css"));
        
    }

    function widget($args, $instance) {
	    
	    if ( is_user_logged_in() ):

		    global $post;
		    $post_id = $post->ID;
	        extract( $args ); 
	        $widget_id = $id. "-" . $this->number;
			$user_id = get_current_user_id();
			$notes = get_user_meta($user_id, 'user_notifications', true);
			$nonce = wp_create_nonce ('ht_notify_add_'.$widget_id);
	        
	        $path = plugin_dir_url( __FILE__ );
	
	        wp_enqueue_script( 'ht_notify', $path.'js/ht_notify.js' );
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
	        wp_localize_script( 'ht_notify', 'ht_notify', $params );
	
			echo "<div id='ht_notify_".$widget_id."' class='ht_notify'>";
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

add_action('widgets_init', create_function('', 'return register_widget("htnotify_add");'));
add_action( 'wp_ajax_ht_notify_ajax_add', 'ht_notify_ajax_add' );
add_action( 'wp_ajax_nopriv_ht_notify_ajax_add', 'ht_notify_ajax_add' );

function ht_notify_ajax_add() {

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
                'message' => __('Error. Not added.','govintranet')
            ),
        ) );
    }

    $response->send();
    
    exit();
}
  

add_action('widgets_init', 'ht_notify_register');

function ht_notify_register() {
		/*
		Register custom user fields
		*/
        
		if( function_exists('acf_add_local_field_group') ):
		
		acf_add_local_field_group(array (
			'key' => 'group_ht_notifications',
			'title' => __('Notifications','govintranet'),
			'fields' => array (
				array (
					'key' => 'field_my_subs',
					'label' => __('My notifications','govintranet'),
					'name' => 'user_notifications',
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

add_action( 'wp_ajax_ht_notify_ajax_action_add', 'ht_notify_ajax_action_add' );
function ht_notify_ajax_action_add() {
	$nonce = $_POST['nonce']; 	
	$widget_id = $_POST['widget_id'];
	$post_id = $_POST['post_id'];
    $response = new WP_Ajax_Response;
	global $current_user;
	$success = false;
	$current_user = wp_get_current_user();
	if ($current_user->ID) $user_id = $current_user->ID;
	if ( ! wp_verify_nonce( $nonce,  'ht_notify_add_'.$widget_id ) ) {
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
				$notes = get_user_meta($user_id, 'user_notifications', true);
				if ( isset($notes) ):
					if ( !in_array($post_id, (array)$notes ) ) $notes[] = $post_id;
					update_user_meta($current_user_id,'user_notifications', $notes); 
				endif;
				$html = '<div class="ht_addtonotifications btn btn-sm btn-primary">' . __('Notifications started','govintranet') . '</div>';
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

add_action( 'wp_ajax_ht_notify_ajax_action_del', 'ht_notify_ajax_action_del' );
function ht_notify_ajax_action_del() {
	$nonce = $_POST['nonce']; 	
	$widget_id = $_POST['widget_id'];
	$post_id = $_POST['post_id'];
    $response = new WP_Ajax_Response;
	global $current_user;
	$success = false;
	$current_user = wp_get_current_user();
	if ($current_user->ID) $user_id = $current_user->ID;
	if ( ! wp_verify_nonce( $nonce,  'ht_notify_add_'.$widget_id ) ) {
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
				$notes = get_user_meta($user_id, 'user_notifications', true);
				$newfaves = array();
				$fcount = 0;
				foreach ( $notes as $f ){
					$fcount++;
					if ( $f == $post_id ) continue;
					$newfaves[] = $f;
				}
			    update_user_meta($current_user_id,'user_notifications', $newfaves); 
				$html = '<div class="ht_addtonotifications btn btn-sm btn-default">' . __('Notification removed','govintranet') . '</div>';
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

function my_notify_activation(){
	if ( ! wp_next_scheduled( 'ht_notify_monitor' ) ) {
	  wp_schedule_event( time(), 'hourly', 'ht_notify_monitor' );
	}
}

register_activation_hook(__FILE__, 'my_notify_activation');
add_action( 'ht_notify_monitor', 'notify_monitor' );

function notify_monitor() {
	global $wpdb;
	// get last run date
	$last_run = get_option("ht_notify_last_run");
	$this_run = date('Y-m-d H:i:s');
	// if no last run date, set to now
	if ( !$last_run ) $last_run = $this_run;
	// query all posts for modified date since the last run
	$q = "SELECT ID from $wpdb->posts where post_status = 'publish' and post_modified > '".$last_run."'";
	$new_mods = $wpdb->get_results($q); 
	// create array to store users and posts matrix
	$to_notify = array();
	// for each post
	if ( count($new_mods) > 0 ):
		foreach ( (array)$new_mods as $n ){
	//		find the users who have subscribed to this post
			$user_query = new WP_User_Query(array('meta_query'=>array(array('key'=>'user_notifications','value'=>'%"'.$n->ID.'"%','compare'=>'LIKE'))));
 			if ( $user_query ) foreach ($user_query->results as $u){ 
	 			$temp = $to_notify[$u->ID];
	 			if ( !$temp ) $temp = array();
	 			$temp[] = $n;
	 			$to_notify[$u->ID] = $temp;
 			}
		}
	endif;

	// run through the users/posts matrix and email notifications
	if ( count(!$to_notify) > 0 ):
		foreach ( $to_notify as $userid=>$posts ){ 
			// get user details
			$user = get_userdata($userid); 
			$to = $user->user_email;
			// compose email
			$sitename = get_option("blogname", __("Intranet","govintranet"));
			$admin_email = get_option("admin_email", __("Intranet","govintranet"));
			$subject = __('Intranet updates','govintranet');
			$headers = array('Content-Type: text/html; charset=UTF-8','From: '.$sitename.' <'.$admin_email.'>' . "\r\n");
			$body = "<p>" . _n('The following page has been updated', 'The following pages have been updated', count($posts), 'govintranet') . "</p>";
			$body.="<ul>";
			foreach ( $posts as $p ){
				$body.="<li><a href='".get_permalink($p->ID)."'>".get_the_title($p->ID)."</a></li>";
			}
			$body.= "</ul>";
			// send email to user
			wp_mail( $to, $subject, $body, $headers ); 
		}
	endif;

	update_option("ht_notify_last_run", $this_run );	
}

/*
function flag_subs( $post_id ) {

    $slug = array('news-update','news','page','task','blogpost','project','vacancy','team','event');

    if ( isset( $_POST['post_type'] ) && !in_array( $_POST['post_type'] , $slug ) ) {
        return;
    }

	// check if any subscribers
	

    // - Update the post's metadata.
	add_post_meta( $post_id, 'ht_subcribe_flag', 1 );
	return;
}
add_action( 'save_post', 'flag_subs' );
*/

function ht_notify_ajax_show() {

	$before_widget = stripcslashes($_POST['before_widget']);
	$after_widget = stripcslashes($_POST['after_widget']);
	$post_id = $_POST['post_id'];
	$user_id = $_POST['user_id'];
	$widget_id = $_POST['widget_id'];
    $response = new WP_Ajax_Response;
    $notes = get_user_meta($user_id, 'user_notifications', true );

	$html = "";
	if ( isset($notes) && !in_array($post_id, (array)$notes) ):
		$html.= "<a onclick='javascript:addtonotifications();' class='ht_addtonotifications btn btn-sm btn-primary'>".__('Get notifications','govintranet')."</a>";
	else:
		$html.= "<a onclick='javascript:delnotifications();' class='ht_addtonotifications btn btn-sm btn-default'>".__('Stop notifications','govintranet')."</a>";
	endif;
			
	$html.= $after_widget; 	
	wp_reset_postdata();		
	

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

add_action( 'wp_ajax_ht_notify_ajax_show', 'ht_notify_ajax_show' );
add_action( 'wp_ajax_nopriv_ht_notify_ajax_show', 'ht_notify_ajax_show' );

register_deactivation_hook(__FILE__, 'ht_notify_deactivation');

function ht_notify_deactivation() {
	wp_clear_scheduled_hook('ht_notify_monitor');
}

<?php
/*
Plugin Name: HT Intraverts
Plugin URI: http://www.helpfultechnology.com
Description: Displays promotional adverts using AJAX
Author: Luke Oatham
Version: 2.0
Author URI: http://www.helpfultechnology.com
*/

class htIntraverts extends WP_Widget {

	function __construct() {
		
		parent::__construct(
			'htIntraverts',
			__( 'HT Intraverts' , 'govintranet'),
			array( 'description' => __( 'Displays an individual intravert from a bank of intraverts and hides from user if already viewed' , 'govintranet') )
		);   

		/*
		Load css
		*/
		wp_enqueue_style( 'intraverts', plugins_url("/ht-intraverts-ajax/css/ht_intraverts.css"));
        
		/*
		Register intravert custom post type
		*/
        
		add_action('init', 'cptui_register_my_cpt_intravert');
		function cptui_register_my_cpt_intravert() {
		register_post_type('intravert', array(
		'label' => 'Intraverts',
		'description' => '',
		'public' => false,
		'show_ui' => true,
		'show_in_menu' => true,
		'capability_type' => 'post',
		'map_meta_cap' => true,
		'hierarchical' => false,
		'rewrite' => array('slug' => 'intravert', 'with_front' => true),
		'query_var' => true,
		'has_archive' => true,
		'exclude_from_search' => true,
		'menu_position' => '30',
		'menu_icon' => 'dashicons-welcome-view-site',
		'supports' => array('title','editor','excerpt','revisions','thumbnail','author','page-attributes'),
		'labels' => array (
		  'name' => 'Intraverts',
		  'singular_name' => 'Intravert',
		  'menu_name' => 'Intraverts',
		  'add_new' => __('Add Intravert','govintranet'),
		  'add_new_item' => __('Add New Intravert','govintranet'),
		  'edit' => __('Edit','govintranet'),
		  'edit_item' => __('Edit Intravert','govintranet'),
		  'new_item' => __('New Intravert','govintranet'),
		  'view' => __('View Intravert','govintranet'),
		  'view_item' => __('View Intravert','govintranet'),
		  'search_items' => __('Search Intraverts','govintranet'),
		  'not_found' => __('No Intraverts Found','govintranet'),
		  'not_found_in_trash' => __('No Intraverts Found in Trash','govintranet'),
		  'parent' => __('Parent Intravert','govintranet'),
		)
		) ); }
		
		/*
		Register Advanced Custom Fields for the intraverts custom post type
		*/
		
		if( function_exists('register_field_group') ):
		

			register_field_group(array (
				'key' => 'group_5494c172a5fb9',
				'title' => 'Intraverts',
				'fields' => array (
					array (
						'key' => 'field_5494c635e6e0e',
						'label' => __('Link text','govintranet'),
						'name' => 'intravert_link_text',
						'prefix' => '',
						'type' => 'text',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'maxlength' => '',
						'readonly' => 0,
						'disabled' => 0,
					),
					array (
						'key' => 'field_5494c648e6e0f',
						'label' => __('Intranet destination page','govintranet'),
						'name' => 'intravert_destination_page',
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
							2 => 'taxonomy',
						),
						'elements' => array (
							0 => 'featured_image',
						),
						'max' => 1,
						'return_format' => 'id',
					),
					array (
						'key' => 'field_54bc153f9a49c',
						'label' => 'Cookie period',
						'name' => 'intravert_cookie_period',
						'prefix' => '',
						'type' => 'number',
						'instructions' => 'The number of days before the intravert will reappear after being viewed. Default is 14 days.',
						'required' => 0,
						'conditional_logic' => 0,
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'min' => '',
						'max' => '',
						'step' => '',
						'readonly' => 0,
						'disabled' => 0,
					),
					array (
						'key' => 'field_5494c1c796832',
						'label' => __('Date range','govintranet'),
						'name' => 'intravert_date_range',
						'prefix' => '',
						'type' => 'true_false',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'message' => '',
						'default_value' => 0,
					),
					array (
						'key' => 'field_5494c21a96833',
						'label' => __('Start date','govintranet'),
						'name' => 'intravert_start_date',
						'prefix' => '',
						'type' => 'date_picker',
						'instructions' => '',
						'required' => 1,
						'conditional_logic' => array (
							array (
								'rule_rule_rule_0' => array (
									'field' => 'field_5494c1c796832',
									'operator' => '==',
									'value' => '1',
								),
							),
						),
						'display_format' => 'd/m/Y',
						'return_format' => 'd/m/Y',
						'first_day' => 1,
					),
					array (
						'key' => 'field_5494c25596834',
						'label' => __('End date','govintranet'),
						'name' => 'intravert_end_date',
						'prefix' => '',
						'type' => 'date_picker',
						'instructions' => '',
						'required' => 1,
						'conditional_logic' => array (
							array (
								'rule_rule_rule_0' => array (
									'field' => 'field_5494c1c796832',
									'operator' => '==',
									'value' => '1',
								),
							),
						),
						'display_format' => 'd/m/Y',
						'return_format' => 'd/m/Y',
						'first_day' => 1,
					),
					array (
						'key' => 'field_5494c18696831',
						'label' => __('Target logged in users','govintranet'),
						'name' => 'intravert_logged_in_only',
						'prefix' => '',
						'type' => 'true_false',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'message' => '',
						'default_value' => 0,
					),
					array (
						'key' => 'field_5494d7fd784af',
						'label' => __('Contributors and above','govintranet'),
						'name' => 'intravert_contributors',
						'prefix' => '',
						'type' => 'true_false',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => array (
							array (
								'rule_0' => array (
									'field' => 'field_5494c18696831',
									'operator' => '==',
									'value' => '1',
								),
							),
						),
						'message' => '',
						'default_value' => 0,
					),
					array (
						'key' => 'field_5494c2af96836',
						'label' => __('Teams','govintranet'),
						'name' => 'intravert_teams',
						'prefix' => '',
						'type' => 'relationship',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => array (
							array (
								'rule_rule_rule_0' => array (
									'field' => 'field_5494c18696831',
									'operator' => '==',
									'value' => '1',
								),
							),
						),
						'post_type' => array (
							0 => 'team',
						),
						'taxonomy' => '',
						'filters' => array (
							0 => 'search',
						),
						'elements' => '',
						'max' => '',
						'return_format' => 'object',
					),
					array (
						'key' => 'field_5494c30696837',
						'label' => __('Grades','govintranet'),
						'name' => 'intravert_grades',
						'prefix' => '',
						'type' => 'taxonomy',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => array (
							array (
								'rule_rule_rule_0' => array (
									'field' => 'field_5494c18696831',
									'operator' => '==',
									'value' => '1',
								),
							),
						),
						'taxonomy' => 'grade',
						'field_type' => 'checkbox',
						'allow_null' => 0,
						'load_save_terms' => 0,
						'return_format' => 'object',
						'multiple' => 0,
					),
					array (
						'key' => 'field_5494c29196835',
						'label' => __('Target content','govintranet'),
						'name' => 'intravert_target_content',
						'prefix' => '',
						'type' => 'select',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'choices' => array (
							'All' => 'All',
							'Task category' => 'Task category',
							'News type' => 'News type',
						),
						'default_value' => array (
						),
						'allow_null' => 0,
						'multiple' => 0,
						'ui' => 0,
						'ajax' => 0,
						'placeholder' => '',
						'disabled' => 0,
						'readonly' => 0,
					),
					array (
						'key' => 'field_5494d3330049d',
						'label' => __('Task category','govintranet'),
						'name' => 'intravert_category',
						'prefix' => '',
						'type' => 'taxonomy',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => array (
							array (
								'rule_rule_rule_rule_0' => array (
									'field' => 'field_5494c29196835',
									'operator' => '==',
									'value' => 'Task category',
								),
							),
						),
						'taxonomy' => 'category',
						'field_type' => 'checkbox',
						'allow_null' => 0,
						'load_save_terms' => 0,
						'return_format' => 'id',
						'multiple' => 0,
					),
					array (
						'key' => 'field_5494d775784ae',
						'label' => __('News type','govintranet'),
						'name' => 'intravert_news_type',
						'prefix' => '',
						'type' => 'taxonomy',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => array (
							array (
								'rule_rule_0' => array (
									'field' => 'field_5494c29196835',
									'operator' => '==',
									'value' => 'News type',
								),
							),
						),
						'taxonomy' => 'news-type',
						'field_type' => 'checkbox',
						'allow_null' => 0,
						'load_save_terms' => 0,
						'return_format' => 'id',
						'multiple' => 0,
					),
				),
				'location' => array (
					array (
						array (
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'intravert',
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
			
			
			/*
			Register Advanced Custom Fields for the intraverts widget
			*/
			
			register_field_group(array (
				'key' => 'group_54c2f059881dc',
				'title' => __('Intraverts widget','govintranet'),
				'fields' => array (
					array (
						'key' => 'field_54c2f09fa63e3',
						'label' => __('Eligible intraverts','govintranet'),
						'name' => 'eligible_intraverts',
						'prefix' => '',
						'type' => 'relationship',
						'instructions' => __('Choose which intraverts are eligible to appear in this widget area.','govintranet'),
						'required' => 0,
						'conditional_logic' => 0,
						'post_type' => array (
							0 => 'intravert',
						),
						'taxonomy' => '',
						'filters' => array (
							0 => 'search',
						),
						'elements' => array (
							0 => 'featured_image',
						),
						'max' => '',
						'return_format' => 'id',
					),
				),
				'location' => array (
					array (
						array (
							'param' => 'widget',
							'operator' => '==',
							'value' => 'htintraverts',
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
	    global $post;
	    $post_id = $post->ID;
        extract( $args ); 
        $widget_id = $id. "-" . $this->number;
		$acf_key = "widget_" . $this->id_base . "-" . $this->number . "_eligible_intraverts" ;  
		$intravertToShow = get_option($acf_key); 
        
        $path = plugin_dir_url( __FILE__ );

        wp_enqueue_script( 'ht_intraverts', $path.'js/ht_intraverts.js' );
        $protocol = isset( $_SERVER["HTTPS"]) ? 'https://' : 'http://';
        $params = array(
        'ajaxurl' => admin_url( 'admin-ajax.php', $protocol ),
		'before_widget' => stripcslashes($before_widget),
		'after_widget' => stripcslashes($after_widget),
        'intravertToShow' => $intravertToShow,  
        'widget_id' => $widget_id,
        'post_id' => $post_id,
        );
        wp_localize_script( 'ht_intraverts', 'ht_intraverts', $params );
		//write script for google analytics if on homepage and not set
		$gistrackhome = get_option('options_track_homepage');
		$gisgtc = get_option('options_google_tracking_code');
		if ( is_front_page() || is_search() ){
			if ( !$gistrackhome || is_search() ){
				echo $gisgtc;
			}
		}		

		echo "<div id='intraverts_".$widget_id."' class='ht_intraverts'></div>";

		wp_reset_postdata();						

    }

    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		return $instance;
    }

    function form($instance) {
        echo "
         <p>
		 " . __('Choose all intraverts eligible to appear in this widget.','govintranet') . "
        </p>
		";
    }
}

add_action( 'wp_ajax_ht_intraverts_ajax_show', 'ht_intraverts_ajax_show' );
add_action( 'wp_ajax_nopriv_ht_intraverts_ajax_show', 'ht_intraverts_ajax_show' );
function ht_intraverts_ajax_show() {

	$before_widget = stripcslashes($_POST['before_widget']);
	$after_widget = stripcslashes($_POST['after_widget']);
	$before_title = stripcslashes($_POST['before_title']);
	$after_title = stripcslashes($_POST['after_title']);
	$intravertToShow = $_POST['intravertToShow'];
	$widget_id = $_POST['widget_id'];
	$post_id = $_POST['post_id'];
    $response = new WP_Ajax_Response;
			
	global $post;
	$pt = get_post_type($post_id);
	$html = "";
	$finalhtml = "";
	$originaltitle = str_replace(site_url(), "",  get_permalink($post_id) );
	$currentpostterms = get_the_terms($post_id, 'category');
	$currentnewsterms = get_the_terms($post_id, 'news-type');
	$temp = array();
	if ( $currentpostterms ) foreach ($currentpostterms as $c){
		$temp[] = $c->term_id;
	}
	$currentpostterms = $temp;
	$temp = array();
	if ( $currentnewsterms ) foreach ($currentnewsterms as $c){
		$temp[] = $c->term_id;
	}
	$currentnewsterms = $temp;

	/*
	Get eligible intraverts to display and build intravertToShow array
	*/

	if ( count($intravertToShow) > 0 && $intravertToShow ):
		$cquery = array(
		    'post_type' => 'intravert',
		    'posts_per_page' => -1,
		    'post__in'=>$intravertToShow,
		    'orderby'=>'menu_order',
		    'order' => 'ASC',
			);
			$eligibles =new WP_Query($cquery);
	else:
		$eligibles =new WP_Query();
	endif;
	$read = 0;
	$alreadydone= array();
	if ($eligibles->have_posts()) while ($eligibles->have_posts()) {
		$eligibles->the_post();  
		if (isset($_COOKIE['ht_intravert_'.get_the_id()])) { $read++; $alreadydone[] = get_the_id(); }
	}
	$k = 0;
	while ($eligibles->have_posts()) {
		$eligibles->the_post();
		if (in_array(get_the_id(), $alreadydone )) continue;

		$icookie = get_post_meta(get_the_id(), 'intravert_cookie_period', true); 
		if (!$icookie) $icookie = 14;

		// check logged on?
		if ( get_post_meta(get_the_id(), 'intravert_logged_in_only', true) ): 
			if ( !is_user_logged_in() ) continue;
			// contributors or above?
			if ( get_post_meta(get_the_id(), 'intravert_contributors', true) ):				
				global $wp_roles;
				$current_user = wp_get_current_user();
				$roles = $current_user->roles;
				$role = array_shift($roles);
				$crole = isset($wp_roles->role_names[$role]) ? translate_user_role($wp_roles->role_names[$role] ) : false;
				if (!in_array($crole, array('Administrator','Editor','Author','Contributor'))) continue;
			endif;
			
			// target a team?
			if ( $teams = get_post_meta(get_the_id(), 'intravert_teams', true) ):			
				$teamcheck = false;
				$userteams = get_user_meta(get_current_user_id(), 'user_team', true);
				if ($userteams) foreach ((array)$userteams as $u){
					if (in_array($u,$teams)) $teamcheck = true;
				}
				if (!$teamcheck) continue;
			endif;

			// target a grade?
			if ( $grades = get_post_meta(get_the_id(), 'intravert_grades', true) ):			
				$gradecheck = false;
				$usergrades = get_user_meta(get_current_user_id(), 'user_grade', true);
				if ($usergrades) foreach ((array)$usergrades as $u){
					if (in_array($u,$grades)) $gradecheck = true;
				}
				if (!$gradecheck) continue;
			endif;

		endif;
		
		// date range?
		$sdate = date('Ymd');
		if ( get_post_meta(get_the_id(), 'intravert_date_range', true) && ( $sdate < get_post_meta(get_the_id(), 'intravert_start_date', true) || $sdate > get_post_meta(get_the_id(), 'intravert_end_date', true) ) ) continue;
		$catcheck = false;

		// target content?
		$targetcontent = get_post_meta(get_the_id(), 'intravert_target_content', true); 
		if ( $targetcontent == "Task category" && $pt == "task"): 
			if ( $icategory = get_post_meta(get_the_id(), 'intravert_category', true) ): 
				if ($icategory) foreach ((array)$icategory as $u){ 
					if (in_array($u,$currentpostterms)) $catcheck = true; 
				}
			endif;
		endif;

		if ( $targetcontent == "News type"  && $pt == "news" ): 
			if ( $icategory = get_post_meta(get_the_id(), 'intravert_news_type', true) ): 
				if ($icategory) foreach ((array)$icategory as $u){
					if (in_array($u,$currentnewsterms)) $catcheck = true; 
				}
			endif;
		endif;

		if ($targetcontent == "Task category"  && !$catcheck) continue;
		if ($targetcontent == "News type"  && !$catcheck) continue;
		
		/*
		Display intravert
		*/
		$k++;
		$thistitle = get_the_title($post->ID);
		$thisURL = get_permalink($post->ID);
		$destination = get_post_meta(get_the_id(),'intravert_destination_page',true);
		if ($destination) { $destination = get_permalink($destination[0]); } else { $destination="#nowhere"; }
		$ititle = esc_attr($post->post_title);
		if ( !$ititle ) $ititle = $widget_id;
		if (has_post_thumbnail($post->ID)):
			$html.= "<a href='".$destination."' onclick='pauseIntravert(\"ht_intravert_".get_the_id()."\",".$icookie.",\"".$ititle."\",\"".esc_attr($originaltitle)."\");'> ";
			$html.= get_the_post_thumbnail(get_the_id(),'full',array('class'=>'img-responsive'));
			$html.= "</a>";
		endif;
		$html.= apply_filters("the_content",get_the_content());
		if (get_post_meta(get_the_id(),'intravert_link_text',true)):
			$html.= "<a id='intravert_hook_".$widget_id."' class='btn btn-info filter_results' href='".$destination."' onclick='pauseIntravert(\"ht_intravert_".get_the_id()."\",".$icookie.",\"".$ititle."\",\"".esc_attr($originaltitle)."\");'> ";
			$html.= get_post_meta(get_the_id(),'intravert_link_text',true);
			if ( $destination != '#nowhere' ) $html.= " <span class='dashicons dashicons-arrow-right-alt2'></span>";
			$html.= "</a> ";
		endif;
		break;
	}


	if ($k){
		$finalhtml = $before_widget;
		$finalhtml.= $html;
		$finalhtml.= "<div class='clearfix'></div>";
		$finalhtml.= $after_widget;
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
  
add_action('widgets_init', create_function('', 'return register_widget("htIntraverts");'));

?>
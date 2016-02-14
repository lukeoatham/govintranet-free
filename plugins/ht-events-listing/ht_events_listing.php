<?php
/*
Plugin Name: HT Events listing
Plugin URI: http://www.helpfultechnology.com
Description: Display future events
Author: Luke Oatham
Version: 4.1
Author URI: http://www.helpfultechnology.com
*/



class htEventsListing extends WP_Widget {

	function __construct() {
		
		parent::__construct(
			'htEventsListing',
			__( 'HT Events listing' , 'govintranet'),
			array( 'description' => __( 'Events listing widget' , 'govintranet') )
		);
		
	}
		
    function widget($args, $instance) {
	    extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        $items = intval($instance['items']);
        $cacheperiod = intval($instance['cacheperiod']);
        if ( isset($cacheperiod) && $cacheperiod ){ $cacheperiod = 60 * $cacheperiod; } 
        if ( !intval($cacheperiod) ) $cacheperiod = 60 * 60;
        $calendar = ($instance['calendar']);
        $thumbnails = ($instance['thumbnails']);
        $excerpt = ($instance['excerpt']);
        $location = ($instance['location']);
        $recent = ($instance['recent']);
		$widget_id = $id;
        $output = '';

		$gatransient = substr( 'event_'.$widget_id.'_'.sanitize_file_name( $title ) , 0, 45 );
		$output = get_transient( $gatransient );
		
		if ( empty( $output ) ): 

			$acf_key = "widget_" . $this->id_base . "-" . $this->number . "_event_listing_event_types" ;
			$etypes = get_option($acf_key);
			$eventtypes =$etypes;


			//display forthcoming events
			$tzone = get_option('timezone_string');
			date_default_timezone_set($tzone);
			$sdate = date('Ymd');
			$stime = date('H:i');
	
			$cquery = array(
				'meta_query' => array(
					'relation' => 'OR',
				       array(
				           'key' => 'event_end_date',
				           'value' => $sdate,
				           'compare' => '>',
				       ),
				       array(
					       'relation' => 'AND',
					       array(
					           'key' => 'event_end_date',
					           'value' => $sdate,
					           'compare' => '=',
					       ),
					       array(
					           'key' => 'event_end_time',
					           'value' => $stime,
					           'compare' => '>',
				           ),
				       ),
					),
			    'orderby' => 'meta_value',
			    'meta_key' => 'event_start_date',
			    'order' => 'ASC',
			    'post_type' => 'event',
				'posts_per_page' => $items,
				'fields' => "id",
				
			);
			
			if ( $eventtypes ) $cquery['tax_query'] = array(array(
					'taxonomy' => 'event-type',
					'terms' => $eventtypes,
					'field' => 'id',	
				));
			
	
			$events =new WP_Query($cquery);
			$output.= "<div class='widget-area widget-events'><div class='upcoming-events'>";
			$output.= "
		    <style>
			.calbox .cal-dow {
				background: ".get_theme_mod('header_background', '0b2d49').";
				color: #".get_header_textcolor().";
				font-size: 16px;
			}
			.calbox { 
				width: 3.5em; 
				border: 3px solid ".get_theme_mod('header_background', '0b2d49').";
				text-align: center;
				border-radius: 3px;
				background: #fff;
				box-shadow: 0 2px 3px rgba(0,0,0,.2);
				
			}
			.calbox .caldate {
				font-size: 25px;
				padding: 0;
				margin: 0;
				font-weight: 800;
			}
			.calbox .calmonth {
				color: ".get_theme_mod('header_background', '0b2d49').";
				text-transform: uppercase;
				font-weight: 800;
				font-size: 18px;
				line-height: 20px;
			}
			a.calendarlink:hover { text-decoration: none; }
			a.calendarlink:hover .calbox .caldate { background: #eee; }
			a.calendarlink:hover .calbox .calmonth { background: #eee; }
			a.calendarlink:hover .calbox  { background: #eee; }
			.eventslisting h3 { border-top: 0 !important; padding-top: 0 !important; margin-top: 0 !important; }
			.eventslisting .alignleft { margin: 0 0 0.5em 0 !important; }
			.eventslisting p { margin-bottom: 0 !important; }
		    </style>
		    ";			
			if ($events->post_count!=0){
				$wtitle = "upcoming";

	
				$output.= $before_widget; 
	
				if ( $title ) {
					$output.= $before_title . $title . $after_title;
				}
			} elseif ( 'on' == $recent) {
					$wtitle = "recent";
					$cquery = array(
					'meta_query' => array(
						'relation' => 'OR',
					       array(
					           'key' => 'event_end_date',
					           'value' => $sdate,
					           'compare' => '<',
					       ),
					       array(
						       'relation' => 'AND',
						       array(
						           'key' => 'event_end_date',
						           'value' => $sdate,
						           'compare' => '=',
						       ),
						       array(
						           'key' => 'event_end_time',
						           'value' => $stime,
						           'compare' => '<',
					           ),
					       ),
						),
				    'orderby' => 'meta_value',
				    'meta_key' => 'event_end_date',
				    'order' => 'DESC',
				    'post_type' => 'event',
					'posts_per_page' => $items,
					'fields' => "id",
				);
				
				if ( $eventtypes ) $cquery['tax_query'] = array(array(
					'taxonomy' => 'event-type',
					'terms' => $eventtypes,
					'field' => 'id',	
				));
				
				$events =new WP_Query($cquery);
					if ($events->post_count!=0){
						$output.= "
						    <style>
							.calbox .cal-dow {
								background: ".get_theme_mod('header_background', '0b2d49').";
								color: #".get_header_textcolor().";
							}
							.calbox { 
								width: 3.5em; 
								border: 3px solid ".get_theme_mod('header_background', '0b2d49').";
								text-align: center;
								border-radius: 3px;
								background: #fff;
								box-shadow: 0 2px 3px rgba(0,0,0,.2);
								
							}
							.calbox .caldate {
								font-size: 2em;
								padding: 0;
								margin: 5px 0;
								font-weight: 800;
							}
							.calbox .calmonth {
								color: ".get_theme_mod('header_background', '0b2d49').";
								text-transform: uppercase;
								font-weight: 800;
							}
							a.calendarlink:hover { text-decoration: none; }
							a.calendarlink:hover .calbox .caldate { background: #eee; }
							a.calendarlink:hover .calbox .calmonth { background: #eee; }
							a.calendarlink:hover .calbox  { background: #eee; }
							.eventslisting h3 { border-top: 0 !important; padding-top: 0 !important; margin-top: 0 !important; }
							.eventslisting .alignleft { margin: 0 0 0.5em 0 !important; }							
							.eventslisting p { margin-bottom: 0 !important; }
						    </style>
					    ";
						$output.= $before_widget; 
			
						if ( $title ) {
							$output.= $before_title . $title . $after_title;
						}
					}
			}
			$k=0;
			$alreadydone= array();
	
			while ($events->have_posts()) {
				global $post;//required for access within widget	
				if ( 'recent' == $wtitle ) $output.= "<small><strong>" . __("Nothing coming up. Here's the most recent:","govintranet") . "</strong></small><br>";
				$wtitle = '';
				$events->the_post();
				if (in_array($post->ID, $alreadydone )) { //don't show if already in stickies
					continue;
				}
				$k++;
				if ($k > $items){
					break;
				}

				$thistitle = get_the_title($post->ID);
				$edate = get_post_meta($post->ID,'event_start_date',true);
				$etime = get_post_meta($post->ID,'event_start_time',true);
				$edate = date("D",strtotime($edate))." ".date(get_option('date_format'),strtotime($edate));
				$edate.= " ".date(get_option('time_format'),strtotime($etime));
				$thisURL = get_permalink($post->ID); 
				
				$output.= "<div class='row'><div class='col-sm-12'>";
				
				if ($thumbnails=='on'){
					$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'newsmedium' ); 
					if ($image_uri != "" ){
						$output.= "<a href='".$thisURL."'><img class='img img-responsive' src='{$image_uri[0]}' alt='".$thistitle."' /></a>";		
					}
				} 
	
				if ( 'on' == $calendar ) {

					$output.= "<div class='media eventslisting'>";
					$output.= "<div class='media-left alignleft'>";
					$output.= "<a class='calendarlink' href='".$thisURL."'>";
					$output.= "<div class='calbox'>";
					$output.= "<div class='cal-dow'>".date('D',strtotime(get_post_meta($post->ID,'event_start_date',true)))."</div>";
					$output.= "<div class='caldate'>".date('d',strtotime(get_post_meta($post->ID,'event_start_date',true)))."</div>";
					$output.= "<div class='calmonth'>".date('M',strtotime(get_post_meta($post->ID,'event_start_date',true)))."</div>";
					$output.= "</div>";
					$output.= "</a>";
					$output.= "</div>";
				
					$output.= "<div class='media-body'>";
					$output.= "<p class='media-heading'>";
					$output.= "<a href='".$thisURL."'>";
					$output.= $thistitle;
					$output.= "</a>";
					$output.= "</p>";
					$output.= "<small><strong>".$edate."</strong></small>";
					if ( $location == 'on' && get_post_meta($post->ID,'event_location',true) ) $output.= "<br><span><small>".get_post_meta($post->ID,'event_location',true)."</small></span>";
					$output.= "</div></div>";
					

				} else {
					$output.= "<p><a href='{$thisURL}'> ".$thistitle."</a></p>";
					$output.= "<small><strong>".$edate."</strong></small>";
					if ( $location == 'on' && get_post_meta($post->ID,'event_location',true) ) $output.= "<br><span><small>".get_post_meta($post->ID,'event_location',true)."</small></span>";
				} 
	
				if ( $excerpt == 'on' && get_the_excerpt() ){
						$output.= "<p class='eventclear'><span>".get_the_excerpt()."</span></p>";
				}
	
				$output.= "</div>";
				$output.= "</div><hr>";
			}
	
			if ($events->post_count!=0){
	
				$landingpage = get_option('options_module_events_page'); 
				if ( !$landingpage ):
					$landingpage_link_text = 'events';
					$landingpage = site_url().'/events/';
				else:
					$landingpage_link_text = get_the_title( $landingpage[0] );
					$landingpage = get_permalink( $landingpage[0] );
				endif;
	
				$output.= '<p class="events-more"><strong><a title="'.$landingpage_link_text.'" class="small" href="'.$landingpage.'">'.$landingpage_link_text.'</a></strong> <span class="dashicons dashicons-arrow-right-alt2"></span></p>';
				$output.= $after_widget;
			}
			$output.= "</div>";
			$output.= "</div>";
			set_transient($gatransient,$output,$cacheperiod); // set cache period 60 minutes default
		endif;
		echo $output;
		
		wp_reset_query();								

    }

    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['items'] = strip_tags($new_instance['items']);
		$instance['cacheperiod'] = strip_tags($new_instance['cacheperiod']);
		$instance['calendar'] = strip_tags($new_instance['calendar']);
		$instance['thumbnails'] = strip_tags($new_instance['thumbnails']);
		$instance['excerpt'] = strip_tags($new_instance['excerpt']);
		$instance['location'] = strip_tags($new_instance['location']);
		$instance['recent'] = strip_tags($new_instance['recent']);
		global $wpdb;
		$wpdb->query("DELETE from $wpdb->options WHERE option_name LIKE '_transient_event_%".sanitize_file_name( $new_instance['title'] )."'");
		$wpdb->query("DELETE from $wpdb->options WHERE option_name LIKE '_transient_timeout_event_%".sanitize_file_name( $new_instance['title'] )."'");
       return $instance;
    }

    function form($instance) {
        $title = esc_attr($instance['title']);
        $items = esc_attr($instance['items']);
        $cacheperiod = esc_attr($instance['cacheperiod']);
        $calendar = esc_attr($instance['calendar']);
        $thumbnails = esc_attr($instance['thumbnails']);
        $excerpt = esc_attr($instance['excerpt']);
        $location = esc_attr($instance['location']);
        $recent = esc_attr($instance['recent']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','govintranet'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /><br><br>

          <label for="<?php echo $this->get_field_id('items'); ?>"><?php _e('Number of items:','govintranet'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('items'); ?>" name="<?php echo $this->get_field_name('items'); ?>" type="text" value="<?php echo $items; ?>" /><br><br>
          
          <input id="<?php echo $this->get_field_id('calendar'); ?>" name="<?php echo $this->get_field_name('calendar'); ?>" type="checkbox" <?php checked((bool) $instance['calendar'], true ); ?> />
          <label for="<?php echo $this->get_field_id('calendar'); ?>"><?php _e('Show calendar','govintranet'); ?></label> <br><br>

          <input id="<?php echo $this->get_field_id('thumbnails'); ?>" name="<?php echo $this->get_field_name('thumbnails'); ?>" type="checkbox" <?php checked((bool) $instance['thumbnails'], true ); ?> />
          <label for="<?php echo $this->get_field_id('thumbnails'); ?>"><?php _e('Show letterbox image','govintranet'); ?></label> <br><br>

          <input id="<?php echo $this->get_field_id('location'); ?>" name="<?php echo $this->get_field_name('location'); ?>" type="checkbox" <?php checked((bool) $instance['location'], true ); ?> />
          <label for="<?php echo $this->get_field_id('location'); ?>"><?php _e('Show location','govintranet'); ?></label> <br><br>

          <input id="<?php echo $this->get_field_id('excerpt'); ?>" name="<?php echo $this->get_field_name('excerpt'); ?>" type="checkbox" <?php checked((bool) $instance['excerpt'], true ); ?> />
          <label for="<?php echo $this->get_field_id('excerpt'); ?>"><?php _e('Show excerpt','govintranet'); ?></label> <br><br>

          <input id="<?php echo $this->get_field_id('recent'); ?>" name="<?php echo $this->get_field_name('recent'); ?>" type="checkbox" <?php checked((bool) $instance['recent'], true ); ?> />
          <label for="<?php echo $this->get_field_id('recent'); ?>"><?php _e('Show recent','govintranet'); ?></label> <br><br>
          <label for="<?php echo $this->get_field_id('cacheperiod'); ?>"><?php _e('Cache (minutes):','govintranet'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('cacheperiod'); ?>" name="<?php echo $this->get_field_name('cacheperiod'); ?>" type="text" value="<?php echo $cacheperiod; ?>" /><br>
        </p>

        <?php 
    }

}
if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array (
	'key' => 'group_55ee1a9ecbd0d',
	'title' => __('Limit event types','govintranet'),
	'fields' => array (
		array (
			'key' => 'field_55ee1ab48bb29',
			'label' => __('Event types','govintranet'),
			'name' => 'event_listing_event_types',
			'type' => 'taxonomy',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'taxonomy' => 'event-type',
			'field_type' => 'checkbox',
			'allow_null' => 1,
			'add_term' => 0,
			'save_terms' => 0,
			'load_terms' => 0,
			'return_format' => 'id',
			'multiple' => 0,
		),
	),
	'location' => array (
		array (
			array (
				'param' => 'widget',
				'operator' => '==',
				'value' => 'hteventslisting',
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

add_action('widgets_init', create_function('', 'return register_widget("htEventsListing");'));

?>
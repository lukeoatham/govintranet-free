<?php
/*
Plugin Name: HT Calendar Events listing 
Plugin URI: http://www.helpfultechnology.com
Description: Display future events from The Events Calendar.
Author: Luke Oatham
Version: 1.0
Author URI: http://www.helpfultechnology.com
*/

if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array (
	'key' => 'group_55ee1a9ecbd0d',
	'title' => 'Limit event types',
	'fields' => array (
		array (
			'key' => 'field_55ee1ab48bb29',
			'label' => 'Event types',
			'name' => 'event_listing_event_types',
			'type' => 'taxonomy',
			'instructions' => '',
			'required' => 1,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'taxonomy' => 'tribe_events_cat',
			'field_type' => 'checkbox',
			'allow_null' => 0,
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
				'value' => 'hteventslistingec',
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

class hteventslistingec extends WP_Widget {
    function hteventslistingec() {
        parent::WP_Widget(false, 'HT Calendar Events listing', array('description' => 'Calendar Events listing'));
    }


    function widget($args, $instance) {
	    extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        $items = intval($instance['items']);
        $calendar = ($instance['calendar']);
        $thumbnails = ($instance['thumbnails']);
        $excerpt = ($instance['excerpt']);
        $recent = ($instance['recent']);
		$widget_id = $id;
        $output = '';
		wp_register_style( 'ht-events-listing-ec', plugin_dir_url("/") ."ht-events-listing-ec/ht_events_listing_ec.css" );
		wp_enqueue_style( 'ht-events-listing-ec' );
		$acf_key = "widget_" . $this->id_base . "-" . $this->number . "_event_listing_event_types" ;
		$etypes = get_option($acf_key);
		$eventtypes =$etypes;
		if ( !$eventtypes ) $eventtypes = array();
		if ( $output == '' || !$output ):
			//display forthcoming events
			$tzone = get_option('timezone_string');
			date_default_timezone_set($tzone);
			$sdate = date('Ymd');
			$stime = date('H:i');
	
			$cquery = array(
				'meta_query' => array(
					'relation' => 'OR',
				       array(
				           'key' => '_EventStartDate',
				           'value' => $sdate,
				           'compare' => '>',
				       ),
					),
			    'orderby' => 'meta_value',
			    'meta_key' => '_EventStartDate',
			    'order' => 'ASC',
			    'post_type' => 'tribe_events',
				'posts_per_page' => $items,
				'fields' => "id",
				'tax_query' => array(array(
					'taxonomy' => 'tribe_events_cat',
					'terms' => $eventtypes,
					'field' => 'id',	
				)),
				
				
			);
	
			$news =new WP_Query($cquery);
			$output.= "<div class='widget-area widget-events-ec'><div class='upcoming-events-ec'>";
			
			if ($news->post_count!=0){
				$wtitle = "upcoming";
				$output.= "
			    <style>
				.upcoming-events-ec .date-stamp-ec {
					border: 3px solid ".get_theme_mod('header_background', '0b2d49').";
				}
				.upcoming-events-ec .date-stamp-ec em {
					background: ".get_theme_mod('header_background', '0b2d49').";
					color: #".get_header_textcolor().";
				}
			    </style>
			    ";
	
				$output.= $before_widget; 
	
				if ( $title ) {
					$output.= $before_title . $title . $after_title;
				}
				//if ($thumbnails!='on' || $calendar=='on') echo "<div><ul>";
			} elseif ( 'on' == $recent) {
					$wtitle = "recent";
					$cquery = array(
					'meta_query' => array(
						'relation' => 'OR',
					       array(
					           'key' => '_EventEndDate',
					           'value' => $sdate,
					           'compare' => '<',
					       ),
						),
				    'orderby' => 'meta_value',
				    'meta_key' => '_EventEndDate',
				    'order' => 'DESC',
					'post_type' => 'tribe_events',
					'posts_per_page' => $items,
					'fields' => "id",
					'tax_query' => array(array(
						'taxonomy' => 'tribe_events_cat',
						'terms' => $eventtypes,
						'field' => 'id',	
					)),
					
				);
				$news =new WP_Query($cquery);
					if ($news->post_count!=0){
						$output.= "
						    <style>
							.upcoming-events-ec .date-stamp-ec {
								border: 3px solid ".get_theme_mod('header_background', '0b2d49').";
							}
							.upcoming-events-ec .date-stamp-ec em {
								background: ".get_theme_mod('header_background', '0b2d49').";
								color: #".get_header_textcolor().";
							}
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
	
			while ($news->have_posts()) {
	
				if ( 'recent' == $wtitle ) $output.= "<small><strong>Nothing coming up. Here's the most recent:</strong></small><br>";
				$wtitle = '';
				$news->the_post();
				if (in_array($post->ID, $alreadydone )) { //don't show if already in stickies
					continue;
				}
				$k++;
				if ($k > $items){
					break;
				}
				global $post;//required for access within widget
				$thistitle = get_the_title($post->ID);
				$edate = get_post_meta($post->ID,'_EventStartDate',true);
				$edate = date('D j M',strtotime($edate));
				$thisURL=get_permalink($ID); 
				
				$output.= "<div class='row'><div class='col-sm-12'>";
				
				if ($thumbnails=='on'){
					$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $ID ), 'newsmedium' ); 
					if ($image_uri != "" ){
						$output.= "<a href='".$thisURL."'><img class='img img-responsive' src='{$image_uri[0]}' alt='".$thistitle."' /></a>";		
					}
				} 
	
				if ( 'on' == $calendar ) {
					$output.= "<a class='calendarlink' href='".$thisURL."'><span class='date-stamp-ec'><em>".date('M',strtotime(get_post_meta($post->ID,'event_start_date',true)))."</em>".date('d',strtotime(get_post_meta($post->ID,'_EventStartDate',true)))."</span><span class='event-title-ec'>".$thistitle."</span></a>";
				} else {
					$output.= "<a href='{$thisURL}'> ".$thistitle."</a>";
				} 
	
				if (!$calendar == 'on') $output.= "<br><small><strong>".$edate."</strong></small>";
	
				if ( $location == 'on' && get_post_meta($post->ID,'event_location',true) ) $output.= "<span><small><strong>".get_post_meta($post->ID,'event_location',true)."</strong></small></span>";
	
				if ( $excerpt == 'on' && get_the_excerpt() ){
						$output.= "<p class='eventclear'><span>".get_the_excerpt()."</span></p>";
				}
	
	
				$output.= "</div>";
				$output.= "</div><hr>";
			}
	
			if ($news->post_count!=0){
				$landingpage="";
				if ( get_option('options_module_events') ) $landingpage = get_option('options_module_events_page'); 

				if ( is_array($landingpage) ):
					$landingpage_link_text = get_the_title($landingpage[0]);
					$landingpage = get_permalink($landingpage[0]);
				else:
					$landingpage_link_text = "More events";
					$landingpage = site_url().'/events/';
				endif;
				if ( count($eventtypes) == 1 ):
					$landingpage_link_text = $title;
					$eventtype = get_term( $eventtypes[0] , "tribe_events_cat", OBJECT ); 
					$landingpage = site_url()."/events/category/".$eventtype->slug;
				endif;
	
				$output.= '<p class="events-more"><strong><a title="'.$landingpage_link_text.'" class="small" href="'.$landingpage.'">'.$landingpage_link_text.'</a></strong> <span class="dashicons dashicons-arrow-right-alt2"></span></p>';
				$output.= $after_widget;
			}
			$output.= "</div>";
			$output.= "</div>";
		
		endif;
		echo $output;
		
		wp_reset_query();								

    }

    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['items'] = strip_tags($new_instance['items']);
		$instance['calendar'] = strip_tags($new_instance['calendar']);
		$instance['thumbnails'] = strip_tags($new_instance['thumbnails']);
		$instance['excerpt'] = strip_tags($new_instance['excerpt']);
		$instance['recent'] = strip_tags($new_instance['recent']);
       return $instance;
    }

    function form($instance) {
        $title = esc_attr($instance['title']);
        $items = esc_attr($instance['items']);
        $calendar = esc_attr($instance['calendar']);
        $thumbnails = esc_attr($instance['thumbnails']);
        $excerpt = esc_attr($instance['excerpt']);
        $recent = esc_attr($instance['recent']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /><br><br>

          <label for="<?php echo $this->get_field_id('items'); ?>"><?php _e('Number of items:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('items'); ?>" name="<?php echo $this->get_field_name('items'); ?>" type="text" value="<?php echo $items; ?>" /><br><br>
          
          <input id="<?php echo $this->get_field_id('calendar'); ?>" name="<?php echo $this->get_field_name('calendar'); ?>" type="checkbox" <?php checked((bool) $instance['calendar'], true ); ?> />
          <label for="<?php echo $this->get_field_id('calendar'); ?>"><?php _e('Show calendar'); ?></label> <br><br>

          <input id="<?php echo $this->get_field_id('thumbnails'); ?>" name="<?php echo $this->get_field_name('thumbnails'); ?>" type="checkbox" <?php checked((bool) $instance['thumbnails'], true ); ?> />
          <label for="<?php echo $this->get_field_id('thumbnails'); ?>"><?php _e('Show letterbox image'); ?></label> <br><br>

          <input id="<?php echo $this->get_field_id('excerpt'); ?>" name="<?php echo $this->get_field_name('excerpt'); ?>" type="checkbox" <?php checked((bool) $instance['excerpt'], true ); ?> />
          <label for="<?php echo $this->get_field_id('excerpt'); ?>"><?php _e('Show excerpt'); ?></label> <br><br>

          <input id="<?php echo $this->get_field_id('recent'); ?>" name="<?php echo $this->get_field_name('recent'); ?>" type="checkbox" <?php checked((bool) $instance['recent'], true ); ?> />
          <label for="<?php echo $this->get_field_id('recent'); ?>"><?php _e('Show recent'); ?></label> <br><br>
        </p>

        <?php 
    }

    

}

add_action('widgets_init', create_function('', 'return register_widget("hteventslistingec");'));

?>
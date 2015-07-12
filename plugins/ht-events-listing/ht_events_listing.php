<?php
/*
Plugin Name: HT Events listing
Plugin URI: http://www.helpfultechnology.com
Description: Display future events
Author: Luke Oatham
Version: 4.0.1.5
Author URI: http://www.helpfultechnology.com
*/


class htEventsListing extends WP_Widget {
    function htEventsListing() {
        parent::WP_Widget(false, 'HT Events listing', array('description' => 'Events listing widget'));
    }


    function widget($args, $instance) {
	    extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        $items = intval($instance['items']);
        $cacheperiod = intval($instance['cacheperiod']);
        if ( isset($cacheperiod) && $cacheperiod ){ $cacheperiod = 60 * $cacheperiod; } 
        $calendar = ($instance['calendar']);
        $thumbnails = ($instance['thumbnails']);
        $excerpt = ($instance['excerpt']);
        $location = ($instance['location']);
        $recent = ($instance['recent']);
		$widget_id = $id;
        $output = '';
		wp_register_style( 'ht-events-listing', plugin_dir_url("/") ."ht-events-listing/ht_events_listing.css" );
		wp_enqueue_style( 'ht-events-listing' );


		$output = get_transient('events_'.$widget_id); 

		if ( $output == '' ):

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
	
			$news =new WP_Query($cquery);
				$output.= "<div class='widget-area widget-events'><div class='upcoming-events'>";
			
			if ($news->post_count!=0){
				$wtitle = "upcoming";
				$output.= "
			    <style>
				.upcoming-events .date-stamp {
					border: 3px solid ".get_theme_mod('header_background', '0b2d49').";
				}
				.upcoming-events .date-stamp em {
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
				$news =new WP_Query($cquery);
					if ($news->post_count!=0){
						$output.= "
						    <style>
							.upcoming-events .date-stamp {
								border: 3px solid ".get_theme_mod('header_background', '0b2d49').";
							}
							.upcoming-events .date-stamp em {
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
				$edate = get_post_meta($post->ID,'event_start_date',true);
				$etime = get_post_meta($post->ID,'event_start_time',true);
				$edate = date('D j M',strtotime($edate));
				$edate .= " ".date('g:ia',strtotime($etime));
				$thisURL=get_permalink($ID); 
				
				$output.= "<div class='row'><div class='col-sm-12'>";
				
				if ($thumbnails=='on'){
					$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $ID ), 'newsmedium' ); 
					if ($image_uri != "" ){
						$output.= "<a href='".$thisURL."'><img class='img img-responsive' src='{$image_uri[0]}' alt='".$thistitle."' /></a>";		
					}
				} 
	
				if ( 'on' == $calendar ) {
					$output.= "<a class='calendarlink' href='".$thisURL."'><span class='date-stamp'><em>".date('M',strtotime(get_post_meta($post->ID,'event_start_date',true)))."</em>".date('d',strtotime(get_post_meta($post->ID,'event_start_date',true)))."</span><span class='event-title'>".$thistitle."</span></a>";
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
	 	set_transient('events_'.$widget_id,$output,$cacheperiod); // set cache period 5 minutes 
		
		endif;
		echo $output;

		if ( !$cacheperiod ) delete_transient('events_'.$widget_id);
		
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
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /><br><br>

          <label for="<?php echo $this->get_field_id('items'); ?>"><?php _e('Number of items:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('items'); ?>" name="<?php echo $this->get_field_name('items'); ?>" type="text" value="<?php echo $items; ?>" /><br><br>
          
          <input id="<?php echo $this->get_field_id('calendar'); ?>" name="<?php echo $this->get_field_name('calendar'); ?>" type="checkbox" <?php checked((bool) $instance['calendar'], true ); ?> />
          <label for="<?php echo $this->get_field_id('calendar'); ?>"><?php _e('Show calendar'); ?></label> <br><br>

          <input id="<?php echo $this->get_field_id('thumbnails'); ?>" name="<?php echo $this->get_field_name('thumbnails'); ?>" type="checkbox" <?php checked((bool) $instance['thumbnails'], true ); ?> />
          <label for="<?php echo $this->get_field_id('thumbnails'); ?>"><?php _e('Show letterbox image'); ?></label> <br><br>

          <input id="<?php echo $this->get_field_id('location'); ?>" name="<?php echo $this->get_field_name('location'); ?>" type="checkbox" <?php checked((bool) $instance['location'], true ); ?> />
          <label for="<?php echo $this->get_field_id('location'); ?>"><?php _e('Show location'); ?></label> <br><br>

          <input id="<?php echo $this->get_field_id('excerpt'); ?>" name="<?php echo $this->get_field_name('excerpt'); ?>" type="checkbox" <?php checked((bool) $instance['excerpt'], true ); ?> />
          <label for="<?php echo $this->get_field_id('excerpt'); ?>"><?php _e('Show excerpt'); ?></label> <br><br>

          <input id="<?php echo $this->get_field_id('recent'); ?>" name="<?php echo $this->get_field_name('recent'); ?>" type="checkbox" <?php checked((bool) $instance['recent'], true ); ?> />
          <label for="<?php echo $this->get_field_id('recent'); ?>"><?php _e('Show recent'); ?></label> <br><br>
          <label for="<?php echo $this->get_field_id('cacheperiod'); ?>"><?php _e('Cache (minutes):'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('cacheperiod'); ?>" name="<?php echo $this->get_field_name('cacheperiod'); ?>" type="text" value="<?php echo $cacheperiod; ?>" /><br>
        </p>

        <?php 
    }

}

add_action('widgets_init', create_function('', 'return register_widget("htEventsListing");'));

?>
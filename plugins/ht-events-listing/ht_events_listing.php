<?php
/*
Plugin Name: HT Events listing
Plugin URI: http://www.helpfultechnology.com
Description: Display future events
Author: Luke Oatham
Version: 4.0
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
        $calendar = ($instance['calendar']);
        $thumbnails = ($instance['thumbnails']);
        $excerpt = ($instance['excerpt']);
        $recent = ($instance['recent']);
		wp_register_style( 'ht-events-listing', plugin_dir_url("/") ."ht-events-listing/ht_events_listing.css" );
		wp_enqueue_style( 'ht-events-listing' );

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
		);

		$news =new WP_Query($cquery);
		if ($news->post_count!=0){
			$wtitle = "upcoming";
			echo "
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

			echo $before_widget; 

			if ( $title ) {
				echo $before_title . $title . $after_title;
			}
			echo "<div class='widget-area widget-events upcoming-events'>";
			if ($thumbnails!='on' || $calendar=='on') echo "<div><ul>";
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
			);
			$news =new WP_Query($cquery);
				if ($news->post_count!=0){
					echo "
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
					echo $before_widget; 
		
					if ( $title ) {
						echo $before_title . $title . $after_title;
					}
					echo "<div class='widget-area widget-events'>";
					if ( 'recent' == $wtitle ) echo "<small><strong>Most recent</strong></small>";
					if ( 'on' == $calendar ) echo "<div class='upcoming-events'><ul>";
				}
		}
		$k=0;
		$alreadydone= array();

		while ($news->have_posts()) {
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
			
			if ($thumbnails=='on'){
				$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $ID ), 'newsmedium' ); 
				if ($image_uri != "" ){
					echo "<hr><a href='".$thisURL."'><img class='img img-responsive' src='{$image_uri[0]}' alt='".$thistitle."' /></a>";		
					if (!$calendar == 'on') echo "<div class='media-body'><a href='{$thisURL}'> ".$thistitle."</a>";
					if (!$calendar == 'on') echo "<br><small>".$edate."</small>";
					if (!$calendar == 'on') echo "<br><span><small><strong>".get_post_meta($post->ID,'event_location',true)."</strong></small></span>";
					if ( $excerpt == 'on' && !$calendar == 'on' ){
						echo "<br><span>".get_the_excerpt()."</span>";
					}
					if (!$calendar == 'on') echo "</div>";
				}
			} 

			if ( 'on' == $calendar ) {
				echo "<li><a href='".$thisURL."'><span class='date-stamp'><em>".date('M',strtotime(get_post_meta($post->ID,'event_start_date',true)))."</em>".date('d',strtotime(get_post_meta($post->ID,'event_start_date',true)))."</span>".$thistitle."</a>";
				echo "<span><small><strong>".get_post_meta($post->ID,'event_location',true)."</strong></small></span>";
				if ($excerpt == 'on'){
					echo "<br><span>".get_the_excerpt()."</span>";
				}
				echo "</li>";
			} 

			if (!$thumbnails == 'on' && !$calendar == 'on') {
				echo "<div class='media'><a href='{$thisURL}'> ".$thistitle."</a><br><small>".$edate."</small><br></div>";
				echo "<span><small><strong>".get_post_meta($post->ID,'event_location',true)."</strong></small></span>";
				if ($excerpt == 'on'){
					echo "<br><br><span>".get_the_excerpt()."</span>";
				}
			} 

		}

		if ($news->post_count!=0){
			if ( 'on' == $calendar ) echo "</ul></div>";

			$landingpage = get_option('options_module_events_page'); 
			if ( !$landingpage ):
				$landingpage_link_text = 'events';
				$landingpage = site_url().'/events/';
			else:
				$landingpage_link_text = get_the_title( $landingpage[0] );
				$landingpage = get_permalink( $landingpage[0] );
			endif;

			echo '<hr><p><strong><a title="{$landingpage_link_text}" class="small" href="'.$landingpage.'">'.$landingpage_link_text.'</a></strong> <span class="dashicons dashicons-arrow-right-alt2"></span></p>';
			echo $after_widget;
		}

		if ($news->post_count!=0){
			echo "</div>";
		}
				
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
          <label for="<?php echo $this->get_field_id('recent'); ?>"><?php _e('Show recent'); ?></label> <br>
        </p>

        <?php 
    }

}
function enqueueEventScripts() {
	}
add_action('wp_enqueue_scripts','enqueueEventScripts');

add_action('widgets_init', create_function('', 'return register_widget("htEventsListing");'));

?>
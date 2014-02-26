<?php
/*
Plugin Name: HT Events lisiting
Plugin URI: http://www.helpfultechnology.com
Description: Display future events
Author: Luke Oatham
Version: 0.1
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
        $thumbnails = ($instance['thumbnails']);

//display forthcoming events
		$tdate= getdate();
		$tdate = $tdate['year']."-".$tdate['mon']."-".$tdate['mday'];
		$tday = date( 'd' , strtotime($tdate) );
		$tmonth = date( 'm' , strtotime($tdate) );
		$tyear= date( 'Y' , strtotime($tdate) );
		$sdate=$tyear."-".$tmonth."-".$tday." 00:00";

		$cquery = array(

	   'meta_query' => array(
				       array(
			           		'key' => 'event_start_date',
			        	   'value' => $sdate,
			    	       'compare' => '>=',
			    	       'type' => 'DATE' ) 
		    	        ),   
					    'orderby' => 'meta_value',
					    'meta_key' => 'event_start_date',
					    'order' => 'ASC',
					    'post_type' => 'event',
						'posts_per_page' => $items,
					)
					;

				$news =new WP_Query($cquery);
				if ($news->post_count!=0){
				echo $before_widget; 


				if ( $title ) {
					echo $before_title . $title . $after_title;}
					echo "
					<div class='widget-area widget-events'>";
				}
					$k=0;
					$alreadydone= array();

				while ($news->have_posts()) {
						$news->the_post();
					if (in_array($post->ID, $alreadydone )) { //don't show if already in stickies
						continue;
					}
					$k++;
					if ($k > 5){
						break;
					}
					global $post;//required for access within widget
					$thistitle = get_the_title($post->ID);
					$newspod = new Pod ( 'event' , $post->ID );
					$edate = get_post_meta($post->ID,'event_start_date',true);
					$edate = date('D j M Y g:ia',strtotime($edate));
					$thisURL=get_permalink($ID); 
					echo "<div class='media'>";
					$siteurl = site_url();
					if ($thumbnails=='on'){
						$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $ID ), 'thumbnail' ); 
						if ($image_uri!="" ){
							echo "<a class='pull-right' href='".$thisURL."'><img class='tinythumb' src='{$image_uri[0]}' alt='".$thistitle."' /></a>";		
						}
					}
					echo "<div class='media-body'><a href='{$thisURL}'> ".$thistitle."</a><br><small>".$edate."</small>";
					echo "</div></div>";
		}
				if ($news->post_count!=0){
					echo '<hr><p><strong><a title="More in events" class="small" href="'.$siteurl.'/events/">More in events</a></strong> <i class="glyphicon glyphicon-chevron-right small"></i></p></div>';
					echo $after_widget;
				}
				
				wp_reset_query();								
    }

    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['items'] = strip_tags($new_instance['items']);
		$instance['thumbnails'] = strip_tags($new_instance['thumbnails']);
       return $instance;
    }

    function form($instance) {
        $title = esc_attr($instance['title']);
        $items = esc_attr($instance['items']);
        $thumbnails = esc_attr($instance['thumbnails']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /><br><br>

          <label for="<?php echo $this->get_field_id('items'); ?>"><?php _e('Number of items:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('items'); ?>" name="<?php echo $this->get_field_name('items'); ?>" type="text" value="<?php echo $items; ?>" /><br><br>
          
          <input id="<?php echo $this->get_field_id('thumbnails'); ?>" name="<?php echo $this->get_field_name('thumbnails'); ?>" type="checkbox" <?php checked((bool) $instance['thumbnails'], true ); ?> />
          <label for="<?php echo $this->get_field_id('thumbnails'); ?>"><?php _e('Show thumbnails'); ?></label> <br>

        </p>

        <?php 
    }

}

add_action('widgets_init', create_function('', 'return register_widget("htEventsListing");'));

?>
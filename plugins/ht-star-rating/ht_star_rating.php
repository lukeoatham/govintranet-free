<?php
/*
Plugin Name: HT Star rating
Plugin URI: http://www.helpfultechnology.com
Description: Displays basic star rating using GD Star Rating plugin
Author: Luke Oatham
Version: 0.1
Author URI: http://www.helpfultechnology.com
*/

class htStarRating extends WP_Widget {
    function htStarRating() {
        parent::WP_Widget(false, 'HT Star rating', array('description' => 'Allows star ratings on a post'));
    }

    function widget($args, $instance) {
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);

							echo $before_widget; ?>
							                  <?php if ( $title )
                        echo $before_title . $title . $after_title; ?>
<?php
wp_gdsr_render_article(10, false, 'soft', 24);
						echo $after_widget; 

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
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />

          
        </p>

        <?php 
    }

}

add_action('widgets_init', create_function('', 'return register_widget("htStarRating");'));

?>
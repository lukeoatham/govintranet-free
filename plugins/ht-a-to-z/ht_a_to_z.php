<?php
/*
Plugin Name: HT A to Z
Plugin URI: http://www.helpfultechnology.com
Description: Widget to display the A to Z 
Author: Luke Oatham
Version: 0.1
Author URI: http://www.helpfultechnology.com
*/

class htAtoZ extends WP_Widget {
    function htAtoZ() {
        parent::WP_Widget(false, 'HT A to Z', array('description' => 'A to Z box'));

    } 

    function widget($args, $instance) {
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
		wp_enqueue_style( 'atoz', plugins_url("/ht-a-to-z/ht_a_to_z.css"));

       ?>
              <?php echo $before_widget; ?>
                  <?php if ( $title )
                        echo $before_title . $title . $after_title; ?>

						<ul id="a-to-z-widget" class="pagination">
			
						<?php 
						//fill the default a to z array
						$letters = range('a','z');
						$letterlink=array();
						$hasentries = array();
						
						foreach($letters as $l) { 
							$letterlink[$l] = "<li class='disabled'><a href='#'>".strtoupper($l)."</a></li>";
						}				
			
						$terms = get_terms('a-to-z'); 
						if ($terms) {
							foreach ((array)$terms as $taxonomy ) {
				
							$letterlink[$taxonomy->slug] = "<li";
							if (strtolower($slug)==strtolower($taxonomy->slug)) $letterlink[$taxonomy->slug] .=  " class='active'";
								$letterlink[$taxonomy->slug] .=  "><a href='".site_url()."/a-to-z/".$taxonomy->slug."/'>".strtoupper($taxonomy->name)."</a></li>";
							}
						}
			
						echo @implode("",$letterlink); 
						?>
						</ul>

              <?php echo $after_widget; ?>
        <?php
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
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /><br>

        </p>

        <?php 
    }

}

add_action('widgets_init', create_function('', 'return register_widget("htAtoZ");'));

?>
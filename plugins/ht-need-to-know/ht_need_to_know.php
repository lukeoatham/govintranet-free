<?php
/*
Plugin Name: HT Need to know
Plugin URI: http://www.helpfultechnology.com
Description: Display need to know news stories
Author: Luke Oatham
Version: 0.1
Author URI: http://www.helpfultechnology.com
*/

class htNeedToKnow extends WP_Widget {
    function htNeedToKnow() {
        parent::WP_Widget(false, 'HT Need to know', array('description' => 'Need to know news widget'));
    }

    function widget($args, $instance) {
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        $items = intval($instance['items']);

//display need to know stories
	$cquery = array(
								'orderby' => 'post_date',
							    'order' => 'DESC',
							    'post_type' => 'news',
							    'posts_per_page' => $items,
							    'meta_key'=>'news_listing_type',
							    'meta_value'=>'1'
								);

							$news =new WP_Query($cquery);
							if ($news->post_count!=0){
							echo $before_widget; 


							if ( $title ) {
                        echo $before_title . $title . $after_title;}
								echo "
								<div id='need-to-know'>
								<ul class='need'>";
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
								$thistitle = get_the_title($post->ID);
								$newspod = new Pod ( 'news' , $post->ID );

								$thisURL=get_permalink($ID);
								$icon = get_option('general_intranet_need_to_know_icon');
								if ($icon=='') $icon = "flag";
								echo "<li><a href='{$thisURL}'><span class='glyphicon glyphicon-".$icon."'></span> ".$thistitle."</a></li>";
					}
							if ($news->post_count!=0){
								echo "</ul></div>";
								echo $after_widget;
							}
							
							wp_reset_query();								

?>

        <?php
    }

    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['items'] = strip_tags($new_instance['items']);
       return $instance;
    }

    function form($instance) {
        $title = esc_attr($instance['title']);
        $items = esc_attr($instance['items']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /><br><br>

          <label for="<?php echo $this->get_field_id('items'); ?>"><?php _e('Number of items:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('items'); ?>" name="<?php echo $this->get_field_name('items'); ?>" type="text" value="<?php echo $items; ?>" /><br><br>
          

        </p>

        <?php 
    }

}

add_action('widgets_init', create_function('', 'return register_widget("htNeedToKnow");'));

?>
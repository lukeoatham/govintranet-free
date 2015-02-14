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
        $hide = $instance['hide'];

		 wp_register_script( 'needtoknow', plugins_url("/ht-need-to-know/ht_need_to_know.js"));
		 wp_enqueue_script( 'needtoknow' );

		//display need to know stories
		$cquery = array(
			'orderby' => 'post_date',
		    'order' => 'DESC',
		    'post_type' => 'news',
		    'posts_per_page' => -1,
		    'tax_query' => array(array(
		    'taxonomy'=>'post_format',
		    'field'=>'slug',
		    'terms'=>array('post-format-status')
		    ))
			);

		$news =new WP_Query($cquery);
		$read = 0;
		$show = 0;
		$alreadydone= array();
		if ($hide):
			while ($news->have_posts()) {
				$news->the_post();  
				if (isset($_COOKIE['ht_need_to_know_'.get_the_id()])) {$read++; $alreadydone[]=get_the_id();} else { $show++; }
			}
		else:
			$show=1;
		endif;
		if ($news->post_count!=0 && $news->post_count <> $read && $show){
			echo $before_widget; 
			if ( $title ) echo $before_title . $title . $after_title;
			echo "<div id='need-to-know'><ul class='need'>";
		}
		$k=0;
		while ($news->have_posts()) {
			$news->the_post();
			if (in_array(get_the_id(), $alreadydone ) || $k > $items) continue;  //don't show if already read
			$k++;
			if ($k > $items) break;
			$thistitle = get_the_title();
			$thisURL=get_permalink();
			$icon = get_option('options_need_to_know_icon');
			if ($icon=='') $icon = "flag";
			echo "<li><a href='{$thisURL}' onclick='Javascript:pauseNeedToKnow(\"ht_need_to_know_".get_the_id()."\");'><span class='glyphicon glyphicon-".$icon."'></span> ".$thistitle."</a></li>";
		}
		if ($news->post_count!=0 && $news->post_count <> $read && $show){
			echo "</ul></div>";
			echo $after_widget;
		}
		
		wp_reset_query();								

    }

    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['items'] = strip_tags($new_instance['items']);
		$instance['hide'] = strip_tags($new_instance['hide']);
       return $instance;
    }

    function form($instance) {
        $title = esc_attr($instance['title']);
        $items = esc_attr($instance['items']);
        $hide = esc_attr($instance['hide']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /><br><br>
          <label for="<?php echo $this->get_field_id('items'); ?>"><?php _e('Number of items:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('items'); ?>" name="<?php echo $this->get_field_name('items'); ?>" type="text" value="<?php echo $items; ?>" /><br><br>
          <input id="<?php echo $this->get_field_id('hide'); ?>" name="<?php echo $this->get_field_name('hide'); ?>" type="checkbox" <?php checked((bool) $instance['hide'], true ); ?> />
          <label for="<?php echo $this->get_field_id('hide'); ?>"><?php _e('Hide if already read'); ?></label> <br>
        </p>

        <?php 
    }

}

add_action('widgets_init', create_function('', 'return register_widget("htNeedToKnow");'));

?>
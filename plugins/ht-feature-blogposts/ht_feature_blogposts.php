<?php
/*
Plugin Name: HT Feature blogposts
Plugin URI: http://www.helpfultechnology.com
Description: Display blogposts
Author: Luke Oatham
Version: 0.1
Author URI: http://www.helpfultechnology.com
*/

class htFeatureBlogposts extends WP_Widget {
    function htFeatureBlogposts() {
        parent::WP_Widget(false, 'HT Feature blogposts', array('description' => 'Blogpost listing widget'));
    }

    function widget($args, $instance) {
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        $items = intval($instance['items']);
        $thumbnails = ($instance['thumbnails']);
        $freshness = ($instance['freshness']);
        $more = ($instance['more']);
        
		$tdate= getdate();
		$tdate = $tdate['year']."-".$tdate['mon']."-".$tdate['mday'];
		$tday = date( 'd' , strtotime($tdate) );
		$tmonth = date( 'm' , strtotime($tdate) );
		$tyear= date( 'Y' , strtotime($tdate) );
		$tdate=$tyear."-".$tmonth."-".$tday." 00:00";
		$freshness = "-".$freshness." day";
        $tdate = date ( 'F jS, Y', strtotime ( $freshness . $tdate ) );  
        
//fetch fresh blogposts 

		$cquery = array(

					    'post_type' => 'blog',
						'posts_per_page' => $items,
						'date_query' => array(
								array(
									'after'     => $tdate,
									'inclusive' => true,
								),
							),
					)
					;

		$news =new WP_Query($cquery);
		if ($news->post_count!=0){
			if ( $title ) {
				echo $before_widget; 
				echo $before_title . $title . $after_title;
			}
			echo "<div class='widget-area widget-blogposts'>";
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
			$newspod = new Pod ( 'blog' , $post->ID );
			$edate = $post->post_date;
			$edate = date('j M Y',strtotime($edate));
			$thisURL=get_permalink($ID); 
			echo "<div class='media'>";
			if ($thumbnails=='on'){
				$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $ID ), 'thumbnail' ); 
				if (!$image_uri){
					$image_uri = get_avatar($post->post_author,72);
					$image_uri = str_replace("alignleft", "alignleft tinyblogthumb", $image_uri);
					echo "<a class='pull-left' href='".site_url()."/blog/".$post->post_name."/'>{$image_uri}</a>";		
				} else {
					echo "<a class='pull-left' href='".site_url()."/blog/".$post->post_name."/'><img class='tinyblogthumb alignleft' src='{$image_uri[0]}' alt='".$thistitle."' /></a>";		
				}
			}
			echo "<div class='media-body'><a href='{$thisURL}'><strong>".$thistitle."</strong></a><span class='small'> by ";
			echo get_the_author();
			echo "</span><br><span class='news_date'>".$edate."</span>";
			echo "</div></div>";
		}
		if ($news->have_posts() && $more){
			echo '<hr><strong><a title="More blog posts" class="small" href="'.site_url().'/blog/">More blog posts</a></strong> <i class="glyphicon glyphicon-chevron-right small"></i>';
		} 
		if ($news->have_posts()){
			echo '</div>';
			echo $after_widget;
		}
		
		wp_reset_query();								
    }

    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['items'] = strip_tags($new_instance['items']);
		$instance['thumbnails'] = strip_tags($new_instance['thumbnails']);
		$instance['freshness'] = strip_tags($new_instance['freshness']);
		$instance['more'] = strip_tags($new_instance['more']);
       return $instance;
    }

    function form($instance) {
        $title = esc_attr($instance['title']);
        $items = esc_attr($instance['items']);
        $thumbnails = esc_attr($instance['thumbnails']);
        $freshness = esc_attr($instance['freshness']);
        $more = esc_attr($instance['more']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /><br><br>

          <label for="<?php echo $this->get_field_id('items'); ?>"><?php _e('Number of items:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('items'); ?>" name="<?php echo $this->get_field_name('items'); ?>" type="text" value="<?php echo $items; ?>" /><br><br>
          
          <label for="<?php echo $this->get_field_id('freshness'); ?>"><?php _e('Freshness (days)'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('freshness'); ?>" name="<?php echo $this->get_field_name('freshness'); ?>" type="text" value="<?php echo $freshness; ?>" /><br><br>

          <input id="<?php echo $this->get_field_id('thumbnails'); ?>" name="<?php echo $this->get_field_name('thumbnails'); ?>" type="checkbox" <?php checked((bool) $instance['thumbnails'], true ); ?> />
          <label for="<?php echo $this->get_field_id('thumbnails'); ?>"><?php _e('Show thumbnails'); ?></label> <br><br>


          <input id="<?php echo $this->get_field_id('more'); ?>" name="<?php echo $this->get_field_name('more'); ?>" type="checkbox" <?php checked((bool) $instance['more'], true ); ?> />
          <label for="<?php echo $this->get_field_id('more'); ?>"><?php _e('Show link to more'); ?></label> <br><br>

        </p>

        <?php 
    }

}

add_action('widgets_init', create_function('', 'return register_widget("htFeatureBlogposts");'));

?>
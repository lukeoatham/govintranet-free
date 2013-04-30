<?php
/*
Plugin Name: HT Feature news
Plugin URI: http://www.helpfultechnology.com
Description: Display feature news 
Author: Luke Oatham
Version: 0.1
Author URI: http://www.helpfultechnology.com
*/

class htFeatureNews extends WP_Widget {
    function htFeatureNews() {
        parent::WP_Widget(false, 'HT Feature news', array('description' => 'Display feature news stories'));
    }

    function widget($args, $instance) {
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        $primaryitems = intval($instance['primaryitems']);
        $secondaryitems = intval($instance['secondaryitems']);
        $tertiaryitems = intval($instance['tertiaryitems']);
        global $post;
       ?>
              <?php echo $before_widget; ?>
                  <?php if ( $title )
                        echo $before_title . $title . $after_title; ?>
<?php
							$hc = new Pod ('homepage_control');
							$top_slot =  $hc->get_field('top_news_story');

	$totalstories =  $primaryitems + $secondaryitems + $tertiaryitems; 

//manual override news stories
	$num_top_slots = count($top_slot);
	$to_fill = $totalstories - $num_top_slots;
	$k=0;
	$alreadydone= array();
	//display sticky top news stories
	if ($top_slot){
	foreach ($top_slot as $slot){
		$k++;
		$alreadydone[] = $slot['ID'];
		$newspod = new Pod ( 'news' , $slot['ID'] );
		$videostill = $newspod->get_field('video_still');								
		$videoguid = $videostill[0]['guid']; 
		$vidid = $videostill[0]['ID']; 
		$videostill = wp_get_attachment_image( $vidid, 'large');
		$thistitle = govintranetpress_custom_title($slot['post_title']);
		$thisURL=$slot['post_name'];
				if ($k<=$primaryitems){ //manual primary news
					$videostill = wp_get_attachment_image( $vidid, 'large');
					$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $slot['ID'] ), 'large' );
					if ($image_uri!="" && $videostill==''){
						echo "<a href='/news/content/".$slot['post_name']."/'><img class='size-full' src='{$image_uri[0]}' alt='".govintranetpress_custom_title($slot['post_title'])."' /></a>";									
					} 
					if ($videostill){
						echo "<a href='/news/content/".$slot['post_name']."/'>".$videostill."</a>";
					} 					
					}
				else {
					$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $slot['ID'] ), 'thumbnail' );
					$image_url = "<a href='/news/content/".$slot['post_name']."/'>".get_the_post_thumbnail($slot['ID'], 'thumbnail', array('class' => 'alignleft','width'=>'{$image_uri[1]}','height'=>'{$image_uri[2]}'))."</a>";
					if ($videostill){
						$image_url= "<a href='/news/content/".$slot['post_name']."/'>".$videostill."</a>";
					} 					
				}

		$thisexcerpt= $newspod->get_field('post_excerpt');
		$thisdate= $slot['post_date'];
		$thisdate=date("j M Y",strtotime($thisdate));

		echo "<div class='newsitem'>";
		echo "<a href='/news/content/{$thisURL}'><h2>".$thistitle."</h2></a>";
		echo "<p><span class='news_date'>".$thisdate."</span>";
		if ($k < ($primaryitems + $secondaryitems + 1)) {
		echo "<p>".$image_url."</p><div class='clearfix'></div><p>".$thisexcerpt."</p>";
		echo "<p class='news_date'><a class='more' href='/news/content/{$thisURL}' title='{$thistitle}' >Read more</a></p>";
		}
		echo "</div><div class='clearfix'></div><hr class='light' />";

	}
	}


							wp_reset_query();								

	//display remaining stories
	$cquery = array(
								'orderby' => 'post_date',
							    'order' => 'DESC',
							    'post_type' => 'news',
							    'posts_per_page' => $totalstories,
							    'meta_query'=>array(array(
							    'key'=>'news_listing_type',
							    'value'=>'0',
							    'compare'=>'NOT EQUAL'
								
							    ))
							    				
								);

							$news =new WP_Query($cquery);
							if ($news->post_count==0){
								echo "Nothing to show.";
							}

							while ($news->have_posts()) {
									$news->the_post();
								if (in_array($post->ID, $alreadydone )) { //don't show if already in stickies
									continue;
								}
								$k++;
								if ($k > $totalstories){
									break;
								}
								$thistitle = get_the_title($post->ID);
								$newspod = new Pod ( 'news' , $post->ID );
								$videostill = $newspod->get_field('video_still');								
								$newspod->display('title');
								$videoguid = $videostill[0]['guid']; 
								$vidid = $videostill[0]['ID']; 
								$videostill = wp_get_attachment_image( $vidid, 'large');
								$thisURL=get_permalink($ID); 
										if ($k <= $primaryitems){
												$videostill = wp_get_attachment_image( $vidid, 'large');
												$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'large' );
												if ($image_uri!="" && $videostill==''){
													echo "<a href='{$thisURL}'><img class='size-full' src='{$image_uri[0]}' width='{$image_uri[1]}' height='{$image_uri[2]}' alt='".govintranetpress_custom_title($slot)."' /></a>";									
												} 
												if ($videostill){
													echo "<a href='{$thisURL}'>".$videostill."</a>";
												} 					
												}
											else {
												$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'thumbnail' );
												$image_url = "<a href='{$thisURL}'>".get_the_post_thumbnail($post->ID, 'thumbnail', array('class' => 'alignleft','width'=>'{$image_uri[1]}','height'=>'{$image_uri[2]}'))."</a>";
												if ($videostill){
													$image_url= "<a href='{$thisURL}'>".$videostill."</a>";
												} 					
										}

								$thisdate= get_the_date();
								$thisexcerpt= get_the_excerpt();
								$thisdate=date("j M Y",strtotime($thisdate));
								echo "<div class='newsitem'>";
								echo "<a href='{$thisURL}'><h2>".$thistitle."</h2></a>";
								echo "<p><span class='news_date'>".$thisdate."</span>";
								if ($k < ($primaryitems + $secondaryitems + 1)) {
								echo "<p>".$image_url."</p><div class='clearfix'></div><p>".$thisexcerpt."</p>";
								echo "<p class='news_date'><a class='more' href='{$thisURL}' title='{$thistitle}' >Read more</a></p>";
								}
								echo "</div><div class='clearfix'></div><hr class='light' />";
					}
							wp_reset_query();								

?>
		<div class="category-block"><p><a class="small" href="/news/">More in news</a></p></div>





              <?php echo $after_widget; ?>
        <?php
    }

    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['primaryitems'] = strip_tags($new_instance['primaryitems']);
		$instance['secondaryitems'] = strip_tags($new_instance['secondaryitems']);
		$instance['tertiaryitems'] = strip_tags($new_instance['tertiaryitems']);
       return $instance;
    }

    function form($instance) {
        $title = esc_attr($instance['title']);
        $primaryitems = esc_attr($instance['primaryitems']);
        $secondaryitems = esc_attr($instance['secondaryitems']);
        $tertiaryitems = esc_attr($instance['tertiaryitems']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /><br><br>
          <label>Number of stories</label><br>
          <label for="<?php echo $this->get_field_id('primaryitems'); ?>"><?php _e('Primary:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('primaryitems'); ?>" name="<?php echo $this->get_field_name('primaryitems'); ?>" type="text" value="<?php echo $primaryitems; ?>" /><br><br>
          <label for="<?php echo $this->get_field_id('secondaryitems'); ?>"><?php _e('Secondary:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('secondaryitems'); ?>" name="<?php echo $this->get_field_name('secondaryitems'); ?>" type="text" value="<?php echo $secondaryitems; ?>" /><br><br>
          <label for="<?php echo $this->get_field_id('tertiaryitems'); ?>"><?php _e('Tertiary:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('tertiaryitems'); ?>" name="<?php echo $this->get_field_name('tertiaryitems'); ?>" type="text" value="<?php echo $tertiaryitems; ?>" /><br><br>
          

        </p>

        <?php 
    }

}

add_action('widgets_init', create_function('', 'return register_widget("htFeatureNews");'));

?>
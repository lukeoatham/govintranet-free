<?php
/*
Plugin Name: HT Feature news
Plugin URI: http://www.helpfultechnology.com
Description: Display feature news 
Author: Luke Oatham
Version: 3.0
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
                        	echo $before_title . $title . $after_title; 
							echo "<div id='ht-feature-news'>";
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
		if ($newspod->get_field('post_status')!='publish') {
			$k--;
			continue;
		}
		if (function_exists('get_video_thumbnail')){
			$videostill = get_video_thumbnail(); 
		}

		$thistitle = govintranetpress_custom_title($slot['post_title']);
		$thisURL=$slot['post_name'];
				if ($k<=$primaryitems){ //manual primary news
					$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $slot['ID'] ), 'large' );
					if ($image_uri!="" && $videostill==''){
						echo "<div><a href='" . site_url() . "/news/".$slot['post_name']."/'><img class='img img-responsive' src='{$image_uri[0]}' alt='".govintranetpress_custom_title($slot['post_title'])."' /></a>";									
					} 
					if ($videostill){
						echo "<div><a href='" . site_url() . "/news/".$slot['post_name']."/'>".$videostill."</a>";
					} 					
					}
				else {
					$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $slot['ID'] ), 'thumbnail' );
					$image_url = "<div><a  href='" . site_url() . "/news/".$slot['post_name']."/'>".get_the_post_thumbnail($slot['ID'], 'thumbnail', array('class' => 'media-object pull-right'))."</a>";
					if ($videostill){
						$image_url= "<a href='" . site_url() . "/news/".$slot['post_name']."/'>".$videostill."</a>";
					} 					
				}

		$thisexcerpt= $newspod->get_field('post_excerpt');
		$thisdate= $slot['post_date'];
		$thisdate=date("j M Y",strtotime($thisdate));
		echo "<h2><a href='" . site_url() . "/news/{$thisURL}'>".$thistitle."</a></h2>";
		echo "<div class='media'>";
		echo $image_url;
		echo "<div class='media-body'>";
		echo "<p><span class='news_date'>".$thisdate."</span></p>";
		if ($k < ($primaryitems + $secondaryitems + 1)) {
		echo "".$thisexcerpt."";
		echo "<p class='news_date'><a class='more' href='" . site_url() . "/news/{$thisURL}' title='{$thistitle}' >Read more</a></p>";
		}
		echo "</div></div></div><hr class='light' />\n";

	}
	}


							

	//display remaining stories
	$cquery = array(
								'orderby' => 'post_date',
							    'order' => 'DESC',
							    'post_type' => 'news',
							    'posts_per_page' => $totalstories,
							    'meta_query'=>array(array(
							    'key'=>'news_listing_type',
							    'value'=>0,
								
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
								$thistitle = get_the_title($news->ID);
								$newspod = new Pod ( 'news' , $news->ID );
								$newspod->display('title');
								$thisURL=get_permalink($news->ID); 
										if ($k <= $primaryitems){
												$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $news->ID ), 'large' );
												if ($image_uri!="" && $videostill==''){
													echo "<a href='{$thisURL}'><img class='img img-responsive' src='{$image_uri[0]}' width='{$image_uri[1]}' height='{$image_uri[2]}' alt='".govintranetpress_custom_title($slot)."' /></a>";									
												} 
										} else {
												$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $news->ID ), 'thumbnail' );
												$image_url = "<a class='pull-right' href='{$thisURL}'>".get_the_post_thumbnail($news->ID, 'thumbnail', array('class' => 'media-object'))."</a>";
										}

								$thisdate= get_the_date();
								$thisexcerpt= get_the_excerpt();
								$thisdate=date("j M Y",strtotime($thisdate));
								echo "<h2><a class='' href='".$thisURL."'>".$thistitle."</a></h2>";
								if ($k >= ($primaryitems + $secondaryitems + 1)) {
									echo "<p><span class='news_date'>".$thisdate."";
									echo " <a class='more' href='{$thisURL}' title='{$thistitle}'>Read more</a></span></p>";
								} else {
									echo "<p><span class='news_date'>".$thisdate."</span></p>";									
								}
								if ($k < ($primaryitems + $secondaryitems + 1)) {
									echo "<div class='media'>".$image_url;
								}
								echo "<div class='media-body'>";
								if ($k < ($primaryitems + $secondaryitems + 1)) {
									echo "".$thisexcerpt."<p class='news_date'>";
								echo "<a class='more' href='{$thisURL}' title='{$thistitle}'>Read more</a></p>";
								}
								
								
								if ($k < ($primaryitems + $secondaryitems + 1)) {
									echo "</div>";
								}
								echo "</div><hr class='light' />\n";
					}
							wp_reset_query();								
							echo "</div>";
?>
		<div class="category-block"><p><strong><a title='More in news' class="small" href="<?php echo site_url(); ?>/newspage/">More in news</a></strong> <i class='glyphicon glyphicon-chevron-right small'></i></p></div>





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
          <label for="<?php echo $this->get_field_id('primaryitems'); ?>"><?php _e('Large format'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('primaryitems'); ?>" name="<?php echo $this->get_field_name('primaryitems'); ?>" type="text" value="<?php echo $primaryitems; ?>" /><br><br>
          <label for="<?php echo $this->get_field_id('secondaryitems'); ?>"><?php _e('Thumbnail format'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('secondaryitems'); ?>" name="<?php echo $this->get_field_name('secondaryitems'); ?>" type="text" value="<?php echo $secondaryitems; ?>" /><br><br>
          <label for="<?php echo $this->get_field_id('tertiaryitems'); ?>"><?php _e('Mini format (no photos)'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('tertiaryitems'); ?>" name="<?php echo $this->get_field_name('tertiaryitems'); ?>" type="text" value="<?php echo $tertiaryitems; ?>" /><br><br>
          

        </p>

        <?php 
    }

}

add_action('widgets_init', create_function('', 'return register_widget("htFeatureNews");'));

?>
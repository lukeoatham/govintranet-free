<?php
/*
Plugin Name: HT Feature news
Plugin URI: http://www.helpfultechnology.com
Description: Display feature news 
Author: Luke Oatham
Version: 3.0.7
Author URI: http://www.helpfultechnology.com
*/

class htFeatureNews extends WP_Widget {
    function htFeatureNews() {
        parent::WP_Widget(false, 'HT Feature news', array('description' => 'Display feature news stories'));
}

function widget($args, $instance) {
    extract( $args );
    $title = apply_filters('widget_title', $instance['title']);
    $largeitems = intval($instance['largeitems']);
    $mediumitems = intval($instance['mediumitems']);
    $thumbnailitems = intval($instance['thumbnailitems']);
    $listitems = intval($instance['listitems']);

    global $post;
    echo $before_widget; 
    if ( $title ) echo $before_title . $title . $after_title; 
	echo "<div id='ht-feature-news'>";

	//load manual sticky news stories
	$hc = new Pod ('homepage_control');
	$top_slot =  $hc->get_field('top_news_story');


	//forumalate grid of news stories and formats
	$totalstories =  $largeitems + $mediumitems + $thumbnailitems + $listitems; 

	$newsgrid=array();
	
	for ($i = 1; $i <= $totalstories; $i++) {
			if ($i <= $largeitems) {
				$newsgrid[] = "L";
			} 
			elseif ($i <= $largeitems + $mediumitems) {
				$newsgrid[] = "M";			
			}
			elseif ($i <= $largeitems + $mediumitems + $thumbnailitems) {
				$newsgrid[] = "T";			
			}
			elseif ($i <= $largeitems + $mediumitems + $thumbnailitems + $listitems) {
				$newsgrid[] = "Li";			
			}
		}	

	$siteurl = site_url();

	//manual override news stories
	//display sticky top news stories

	$num_top_slots = count($top_slot);
	$to_fill = $totalstories - $num_top_slots;
	$k=-1;
	$alreadydone= array();

	if ( $num_top_slots > 0 ){ 
			foreach ((array)$top_slot as $slot){
				$newspod = new Pod ( 'news' , $slot['ID'] );
				if ($newspod->get_field('post_status')!='publish') {
					continue;
				}
				$k++;
				$alreadydone[] = $slot['ID'];
				if (function_exists('get_video_thumbnail')){
					$videostill = get_video_thumbnail( $slot['ID'] ); 
				}
		
				$thistitle = govintranetpress_custom_title($slot['post_title']);
				$thisURL=$slot['post_name'];

				if ($newsgrid[$k]=="L"){
					$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $slot['ID'] ), 'newshead' );
					if ($image_uri!="" && $videostill==''){
						echo "<a href='".$siteurl."/news/".$slot['post_name']."/'><img class='img img-responsive' src='{$image_uri[0]}' width='{$image_uri[1]}' height='{$image_uri[2]}' alt='".govintranetpress_custom_title($slot)."' /></a>";									
					} 
				} 
	
				if ($newsgrid[$k]=="M"){
					$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $slot['ID'] ), 'medium' );
					if ($image_uri!="" && $videostill==''){
						echo "<a href='".$siteurl."/news/".$slot['post_name']."/'><img class='img img-responsive' src='{$image_uri[0]}' width='{$image_uri[1]}' height='{$image_uri[2]}' alt='".govintranetpress_custom_title($slot)."' /></a>";									
					} 
				} 
	
				if ($newsgrid[$k]=="T"){
					$image_uri = "<a class='pull-right' href='".$siteurl."/news/".$slot['post_name']."/'>".get_the_post_thumbnail($slot['ID'], 'thumbnail', array('class' => 'media-object hidden-xs'))."</a>";
					if ($image_uri!="" && $videostill==''){
						$image_url = "<a href='".$siteurl."/news/".$slot['post_name']."/'>".$image_uri."</a>";									
					} 
				} 

			$thisdate= $slot['post_date'];
			$post = get_post( $slot['ID'] );
			setup_postdata( $post );
			$thisexcerpt= get_the_excerpt();
			$thisdate=date("j M Y",strtotime($thisdate));
			echo "<h3 class='noborder'><a class='' href='".$thisURL."'>".$thistitle."</a></h3>";

			if ($newsgrid[$k]=="Li"){
				echo "<p><span class='news_date'>".$thisdate."";
				echo " <a class='more' href='{$thisURL}' title='{$thistitle}'>Read more</a></span></p>";
			} else {
				echo "<p><span class='news_date'>".$thisdate."</span></p>";									
			}

			if ($newsgrid[$k]=="T"){
				echo "<div class='media'>".$image_url;
			}

			echo "<div class='media-body'>";

			if ($newsgrid[$k]!="Li"){
				echo $thisexcerpt."<p class='news_date'>";
				echo "<a class='more' href='{$thisURL}' title='{$thistitle}'>Read more</a></p>";
			}

				echo "</div>";

			if ($newsgrid[$k]=="T"){
				echo "</div>";
			}

			echo "<hr class='light' />\n";

		
			}
		} //end of stickies

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
			if ($k >= $totalstories){
				break;
			}
			$thistitle = get_the_title($news->ID);
			$newspod = new Pod ( 'news' , $news->ID );
			$newspod->display('title');
			$thisURL=get_permalink($news->ID); 

			if ($newsgrid[$k]=="L"){
				$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $news->ID ), 'newshead' );
				if ($image_uri!="" && $videostill==''){
					echo "<a href='{$thisURL}'><img class='img img-responsive' src='{$image_uri[0]}' width='{$image_uri[1]}' height='{$image_uri[2]}' alt='".govintranetpress_custom_title($slot)."' /></a>";									
				} 
			} 

			if ($newsgrid[$k]=="M"){
				$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $news->ID ), 'medium' );
				if ($image_uri!="" && $videostill==''){
					echo "<a href='{$thisURL}'><img class='img' src='{$image_uri[0]}' width='{$image_uri[1]}' height='{$image_uri[2]}' alt='".govintranetpress_custom_title($slot)."' /></a>";									
				} 
			} 

			if ($newsgrid[$k]=="T"){
				$image_uri = "<a class='pull-right' href='{$thisURL}'>".get_the_post_thumbnail($news->ID, 'thumbnail', array('class' => 'media-object hidden-xs'))."</a>";
				if ($image_uri!="" && $videostill==''){
					$image_url = "<a href='{$thisURL}'>".$image_uri."</a>";									
				} 
			} 
			
			$thisdate= get_the_date();
			$thisexcerpt= get_the_excerpt();
			$thisdate=date("j M Y",strtotime($thisdate));
			echo "<h3 class='noborder'><a class='' href='".$thisURL."'>".$thistitle."</a></h3>";

			if ($newsgrid[$k]=="Li"){
				echo "<p><span class='news_date'>".$thisdate."";
				echo " <a class='more' href='{$thisURL}' title='{$thistitle}'>Read more</a></span></p>";
			} else {
				echo "<p><span class='news_date'>".$thisdate."</span></p>";									
			}

			if ($newsgrid[$k]=="T"){
				echo "<div class='media'>".$image_url;
			}

			echo "<div class='media-body'>";

			if ($newsgrid[$k]!="Li"){
				echo $thisexcerpt."<p class='news_date'>";
				echo "<a class='more' href='{$thisURL}' title='{$thistitle}'>Read more</a></p>";
			}

				echo "</div>";

			if ($newsgrid[$k]=="T"){
				echo "</div>";
			}

			echo "<hr class='light' />\n";
		}
		echo "</div>";
		wp_reset_query();								
?>
		<div class="category-block"><p><strong><a title='More in news' class="small" href="<?php echo $siteurl; ?>/newspage/">More in news</a></strong> <i class='glyphicon glyphicon-chevron-right small'></i></p></div>


<?php echo $after_widget; 
    
    }	

    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['largeitems'] = strip_tags($new_instance['largeitems']);
		$instance['mediumitems'] = strip_tags($new_instance['mediumitems']);
		$instance['thumbnailitems'] = strip_tags($new_instance['thumbnailitems']);
		$instance['listitems'] = strip_tags($new_instance['listitems']);
       return $instance;
    }

    function form($instance) {
        $title = esc_attr($instance['title']);
        $largeitems = esc_attr($instance['largeitems']);
        $mediumitems = esc_attr($instance['mediumitems']);
        $thumbnailitems = esc_attr($instance['thumbnailitems']);
        $listitems = esc_attr($instance['listitems']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /><br><br>
          <label>Number of stories</label><br>
          <label for="<?php echo $this->get_field_id('largeitems'); ?>"><?php _e('Large'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('largeitems'); ?>" name="<?php echo $this->get_field_name('largeitems'); ?>" type="text" value="<?php echo $largeitems; ?>" /><br><br>
          
          <label for="<?php echo $this->get_field_id('mediumitems'); ?>"><?php _e('Medium'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('mediumitems'); ?>" name="<?php echo $this->get_field_name('mediumitems'); ?>" type="text" value="<?php echo $mediumitems; ?>" /><br><br>

          <label for="<?php echo $this->get_field_id('thumbnailitems'); ?>"><?php _e('Thumbnail'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('thumbnailitems'); ?>" name="<?php echo $this->get_field_name('thumbnailitems'); ?>" type="text" value="<?php echo $thumbnailitems; ?>" /><br><br>
          
          <label for="<?php echo $this->get_field_id('listitems'); ?>"><?php _e('List format (no photos)'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('listitems'); ?>" name="<?php echo $this->get_field_name('listitems'); ?>" type="text" value="<?php echo $listitems; ?>" /><br><br>

        </p>

        <?php 
    }

}

add_action('widgets_init', create_function('', 'return register_widget("htFeatureNews");'));

?>
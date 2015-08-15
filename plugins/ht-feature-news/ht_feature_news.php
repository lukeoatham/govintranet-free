<?php
/*
Plugin Name: HT Feature news
Plugin URI: http://www.helpfultechnology.com
Description: Display feature news 
Author: Luke Oatham
Version: 4.0.2
Author URI: http://www.helpfultechnology.com
*/

class htFeatureNews extends WP_Widget {
    function htFeatureNews() {
        parent::WP_Widget(false, 'HT Feature news', array('description' => 'Display feature news stories'));
		if( function_exists('register_field_group') ):
		
		register_field_group(array (
			'key' => 'group_54bfacd48f6e7',
			'title' => 'Feature news widget',
			'fields' => array (
				array (
					'key' => 'field_54c03e5d0f3f4',
					'label' => 'Pin stories',
					'name' => 'pin_stories',
					'prefix' => '',
					'type' => 'relationship',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'post_type' => array (
						0 => 'news',
						1 => 'blog',
						2 => 'event',
					),
					'taxonomy' => '',
					'filters' => array (
						0 => 'search',
						1 => 'post_type',
					),
					'elements' => '',
					'max' => '',
					'return_format' => 'id',
				),
				array (
					'key' => 'field_54bfacd9a9fbb',
					'label' => 'Exclude stories',
					'name' => 'exclude_stories',
					'prefix' => '',
					'type' => 'relationship',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'post_type' => array (
						0 => 'news',
					),
					'taxonomy' => '',
					'filters' => array (
						0 => 'search',
					),
					'elements' => '',
					'max' => '',
					'return_format' => 'id',
				),
			),
			'location' => array (
				array (
					array (
						'param' => 'widget',
						'operator' => '==',
						'value' => 'htfeaturenews',
					),
				),
			),
			'menu_order' => 0,
			'position' => 'normal',
			'style' => 'seamless',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => '',
		));
		
		endif;
}

function widget($args, $instance) {
    extract( $args );
    $title = apply_filters('widget_title', $instance['title']);
    $largeitems = intval($instance['largeitems']);
    $mediumitems = intval($instance['mediumitems']);
    $thumbnailitems = intval($instance['thumbnailitems']);
    $listitems = intval($instance['listitems']);
	$showexcerpt = $instance['showexcerpt'];
	$acf_key = "widget_" . $this->id_base . "-" . $this->number . "_pin_stories" ;  
	$top_slot = get_option($acf_key); 
	$acf_key = "widget_" . $this->id_base . "-" . $this->number . "_exclude_stories" ;  
	$exclude = get_option($acf_key); 
    global $post;
	$removenews = get_transient('cached_removenews'); 
	if (!$removenews || !is_array($removenews)){
	
		//process expired news
		
		$tzone = get_option('timezone_string'); 
		date_default_timezone_set($tzone);
		$tdate= date('Ymd');
		
		$oldnews = query_posts(array(
		'post_type'=>'news',
		'meta_query'=>array(array(
		'key'=>'news_expiry_date',
		'value'=>$tdate,
		'compare'=>'<='
		))));
		
		if ( count($oldnews) > 0 ){
			foreach ($oldnews as $old) {
				if ($tdate == date('Ymd',strtotime(get_post_meta($old->ID,'news_expiry_date',true)) )): // if expiry today, check the time
					if (date('H:i:s',strtotime(get_post_meta($old->ID,'news_expiry_time',true))) > date('H:i:s') ) continue;
				endif;
				
				$expiryaction = get_post_meta($old->ID,'news_expiry_action',true);
				if ($expiryaction=='Revert to draft status'){
					  $my_post = array();
					  $my_post['ID'] = $old->ID;
					  $my_post['post_status'] = 'draft';
					  wp_update_post( $my_post );
					  delete_post_meta($old->ID, 'news_expiry_date');
					  delete_post_meta($old->ID, 'news_expiry_time');
					  delete_post_meta($old->ID, 'news_expiry_action');
					  delete_post_meta($old->ID, 'news_auto_expiry');
					  if (function_exists('wp_cache_post_change')) wp_cache_post_change( $old->ID ) ;
					  if (function_exists('wp_cache_post_change')) wp_cache_post_change( $my_post ) ;		  
				}	
				if ($expiryaction=='Change to regular news'){
					set_post_format($old->ID, ''); 
					delete_post_meta($old->ID, 'news_expiry_date');
					delete_post_meta($old->ID, 'news_expiry_time');
					delete_post_meta($old->ID, 'news_expiry_action');
					  delete_post_meta($old->ID, 'news_auto_expiry');
					if (function_exists('wp_cache_post_change')) wp_cache_post_change( $old->ID ) ;
				}	
				if ($expiryaction=='Move to trash'){
					  $my_post = array();
					  $my_post['ID'] = $old->ID;
					  $my_post['post_status'] = 'trash';
					  delete_post_meta($old->ID, 'news_expiry_date');
					  delete_post_meta($old->ID, 'news_expiry_time');
					  delete_post_meta($old->ID, 'news_expiry_action');
					  delete_post_meta($old->ID, 'news_auto_expiry');
					  wp_update_post( $my_post );
					  if (function_exists('wp_cache_post_change')) wp_cache_post_change( $old->ID ) ;
					  if (function_exists('wp_cache_post_change')) wp_cache_post_change( $my_post ) ;		  
				}	
			}
		}
		wp_reset_query();
	}
	
    echo $before_widget; 

    if ( $title ) :
	    echo $before_title;
		echo $title;
	    echo $after_title; 
	endif;

	echo '<div id="ht-feature-news">';

	//load manual sticky news stories
	//	$home = get_page_by_path('/home/',OBJECT,'page'); 
	//$top_slot =  get_post_meta($home->ID,'top_news_stories'); 
	
	//forumalate grid of news stories and formats
	$totalstories =  $largeitems + $mediumitems + $thumbnailitems + $listitems; 

	$newsgrid = array();
	
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
	$k = -1;
	$alreadydone = array();

	if ( $num_top_slots > 0 ){ 
		foreach ((array)$top_slot as $thisslot){ 
			if (!$thisslot) continue;
			$slot = get_post($thisslot); 
			if ($slot->post_status != 'publish') {
				continue;
			}
			$k++;
			$alreadydone[] = $slot->ID;
			if (function_exists('get_video_thumbnail')){
				$videostill = get_video_thumbnail( $slot->ID ); 
			}
			$thistitle = $slot->post_title;
			$thisURL=get_permalink($slot->ID); 
			$video = 0;
			if ( has_post_format('video', $slot->ID) ):
				$video = apply_filters('the_content', get_post_meta( $slot->ID, 'news_video_url', true));
			endif;

			if ($newsgrid[$k]=="L"){
				$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $slot->ID ), 'newshead' );
				if ($video){
					echo $video;
				} elseif ($image_uri!="" ){
					echo "<a href='{$thisURL}'><img class='img img-responsive' src='{$image_uri[0]}' width='{$image_uri[1]}' height='{$image_uri[2]}' alt='".govintranetpress_custom_title($slot->post_title)."' /></a>";									
				} 
			} 

			if ($newsgrid[$k]=="M"){
				$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $slot->ID ), 'newsmedium' );
				if ($image_uri!="" ){
					echo "<a href='{$thisURL}'><img class='img img-responsive' src='{$image_uri[0]}' width='{$image_uri[1]}' height='{$image_uri[2]}' alt='".govintranetpress_custom_title($slot->post_title)."' /></a>";									
				} 
			} 
				
			if ($newsgrid[$k]=="T"){
				$image_uri = "<a class='pull-right' href='".$thisURL."'>".get_the_post_thumbnail($slot->ID, 'thumbnail', array('class' => 'media-object hidden-xs'))."</a>";
				if ($image_uri!="" ){
					$image_url = $image_uri;
				} 
			} 
	
			$thisdate= $slot->post_date;
			$post = get_post( $slot->ID );
			setup_postdata( $post );
			
			$thisexcerpt= get_the_excerpt();
			$thisdate=date("j M Y",strtotime($thisdate));
			$ext_icon = '';
			if ( get_post_format($slot->ID) == 'link' ) $ext_icon = "<span class='dashicons dashicons-migrate'></span> ";
	
	
			if ($newsgrid[$k]=="T"){
				echo "<div class='media'>".$image_url;
			}
	
			echo "<div class='media-body'>";
			echo "<h3 class='noborder'>".$ext_icon."<a href='".$thisURL."'>".$thistitle."</a>".$ext_icon."</h3>";

			if ($newsgrid[$k]=="Li"){
				echo "<p>";
				echo '<span class="listglyph">'.get_the_date("j M Y"); 
				comments_number( '', ' <span class="badge">1 comment</span>', ' <span class="badge">% comments</span>' );
				echo '</span> ';
				echo " <a class='more' href='{$thisURL}' title='{$thistitle}'>Full story <span class='dashicons dashicons-arrow-right-alt2'></span></a></span></p>";
			} else {
				if ($showexcerpt == 'on') {
					echo "<p>";
					echo '<span class="listglyph">'.get_the_date("j M Y"); 
					comments_number( '', ' <span class="badge">1 comment</span>', ' <span class="badge">% comments</span>' );
					echo '</span> ';
					echo "</p>";									
					echo $thisexcerpt;
					echo "<p class='news_date'><a class='more' href='{$thisURL}' title='{$thistitle}'>Full story <span class='dashicons dashicons-arrow-right-alt2'></span></a></p>";
				} else {
					echo "<p>";
					echo '<span class="listglyph">'.get_the_date("j M Y"); 
					comments_number( '', ' <span class="badge">1 comment</span>', ' <span class="badge">% comments</span>' );
					echo '</span> ';
					echo " <a class='more' href='{$thisURL}' title='{$thistitle}'>Full story <span class='dashicons dashicons-arrow-right-alt2'></span></a></span></p>";
				}
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
			    'post_type' => 'news',
			    'posts_per_page' => $totalstories,
			    'post__not_in'=>$exclude,
			    'tax_query' => array(array(
			    'taxonomy'=>'post_format',
			    'field'=>'slug',
			    'terms'=>array('post-format-status'),
			    "operator"=>"NOT IN"
			    ))
				);
	
		$news =new WP_Query($cquery);
		if ($news->post_count==0){
			echo "Nothing to show.";
		} 
		global $post;
		while ($news->have_posts()) {
				$news->the_post();
				$theid = get_the_id();
			if (in_array($theid, $alreadydone )) { //don't show if already in stickies
				continue;
			}
			$k++;
			if ($k >= $totalstories){
				break;
			}
			$thistitle = get_the_title($theid);
			$thisURL=get_permalink($theid); 
	
			$video = 0;
			if ( has_post_format('video', $theid) ):
				$video = apply_filters('the_content', get_post_meta( $theid, 'news_video_url', true));
			endif;
		
			if ($newsgrid[$k]=="L"){
				$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $theid ), 'newshead' );
				if ($video){
					echo $video;
				} elseif ($image_uri!="" ){
					echo "<a href='{$thisURL}'><img class='img img-responsive' src='{$image_uri[0]}' width='{$image_uri[1]}' height='{$image_uri[2]}' alt='".govintranetpress_custom_title($post->post_title)."' /></a>";									
				} 
			} 
	
			if ($newsgrid[$k]=="M"){
				$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $theid ), 'newsmedium' );
				if ($image_uri!="" ){
					echo "<a href='{$thisURL}'><img class='img img-responsive' src='{$image_uri[0]}' width='{$image_uri[1]}' height='{$image_uri[2]}' alt='".govintranetpress_custom_title($post->post_title)."' /></a>";									
				} 
			} 
	
			if ($newsgrid[$k]=="T"){
				$image_uri = "<a class='pull-right' href='{$thisURL}'>".get_the_post_thumbnail($theid, 'thumbnail', array('class' => 'media-object hidden-xs'))."</a>";
				if ($image_uri!="" ){
					$image_url = $image_uri;
				} 
			} 
			
			$thisdate= get_the_date("j M Y"); 
			$thisexcerpt= get_the_excerpt();
			$ext_icon = '';
	
	
			if ($newsgrid[$k]=="T"){
				echo "<div class='media'>".$image_url;
			}
	
			echo "<div class='media-body feature-news-".strtolower($newsgrid[$k])."'>";
			if ( get_post_format($theid) == 'link' ) $ext_icon = "<i class='dashicons dashicons-migrate'></i> ";
			echo "<h3 class='noborder'><a href='".$thisURL."'>".$thistitle."</a> ".$ext_icon."</h3>";

			if ($newsgrid[$k]=="Li"){
					echo "<p>";
					echo '<span class="listglyph">'.get_the_date("j M Y"); 
					comments_number( '', ' <span class="badge">1 comment</span>', ' <span class="badge">% comments</span>' );
					echo '</span> ';
					echo "</p>";									
			} else {
				if ($showexcerpt == 'on') {
					echo "<p>";
					echo '<span class="listglyph">'.get_the_date("j M Y"); 
					comments_number( '', ' <span class="badge">1 comment</span>', ' <span class="badge">% comments</span>' );
					echo '</span> ';
					echo "</p>";									
					echo $thisexcerpt;
					echo "<p class='news_date'><a class='more' href='{$thisURL}' title='{$thistitle}'>Full story <span class='dashicons dashicons-arrow-right-alt2'></span></a></p>";
				} else {
					echo "<p>";
					echo '<span class="listglyph">'.get_the_date("j M Y"); 
					comments_number( '', ' <span class="badge">1 comment</span>', ' <span class="badge">% comments</span>' );
					echo " <a class='more' href='{$thisURL}' title='{$thistitle}'>Full story <span class='dashicons dashicons-arrow-right-alt2'></span></a></span></p>";
				}
			}
	
			echo "</div>";
	
			if ($newsgrid[$k]=="T"){
				echo "</div>";
			}

			echo "<hr class='light' />\n";
		}
		wp_reset_query();								
		$landingpage = get_option('options_module_news_page'); 
		if ( !$landingpage ):
			$landingpage_link_text = 'news';
			$landingpage = site_url().'/newspage/';
		else:
			$landingpage_link_text = get_the_title( $landingpage[0] );
			$landingpage = get_permalink( $landingpage[0] );
		endif;
		
		echo '<p><strong><a title="'.$landingpage_link_text.'" class="small" href="'.$landingpage.'">'.$landingpage_link_text.'</a></strong> <span class="dashicons dashicons-arrow-right-alt2"></span></p>';

		echo "<div class='clearfix'></div>";
		echo "</div>";
		echo $after_widget; 
    
    }	

    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['largeitems'] = strip_tags($new_instance['largeitems']);
		$instance['mediumitems'] = strip_tags($new_instance['mediumitems']);
		$instance['thumbnailitems'] = strip_tags($new_instance['thumbnailitems']);
		$instance['listitems'] = strip_tags($new_instance['listitems']);
		$instance['showexcerpt'] = strip_tags($new_instance['showexcerpt']);
       return $instance;
    }

    function form($instance) {
        $title = esc_attr($instance['title']);
        $largeitems = esc_attr($instance['largeitems']);
        $mediumitems = esc_attr($instance['mediumitems']);
        $thumbnailitems = esc_attr($instance['thumbnailitems']);
        $listitems = esc_attr($instance['listitems']);
        $showexcerpt = esc_attr($instance['showexcerpt']);
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

          <input id="<?php echo $this->get_field_id('showexcerpt'); ?>" name="<?php echo $this->get_field_name('showexcerpt'); ?>" type="checkbox" <?php checked((bool) $instance['showexcerpt'], true ); ?> />
          <label for="<?php echo $this->get_field_id('showexcerpt'); ?>"><?php _e('Show excerpt'); ?></label> <br>
        </p>
        <?php 
    }

}

add_action('widgets_init', create_function('', 'return register_widget("htFeatureNews");'));

?>
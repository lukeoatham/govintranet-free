<?php
/*
Plugin Name: HT Feature news
Plugin URI: http://www.helpfultechnology.com
Description: Display feature news 
Author: Luke Oatham
Version: 4.4
Author URI: http://www.helpfultechnology.com
*/

class htFeatureNews extends WP_Widget {

	function __construct() {
		
		parent::__construct(
			'htFeatureNews',
			__( 'HT Feature news' , 'govintranet'),
			array( 'description' => __( 'Display feature news stories' , 'govintranet') )
		);        
		
		if( function_exists('acf_add_local_field_group') ):
		
		acf_add_local_field_group(array (
			'key' => 'group_54bfacd48f6e7',
			'title' => __('Feature news widget','govintranet'),
			'fields' => array (
				array (
					'key' => 'field_560c502fb460c',
					'label' => _x('News type' , 'Categories for news' , 'govintranet'),
					'name' => 'news_listing_news_type',
					'type' => 'taxonomy',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'taxonomy' => 'news-type',
					'field_type' => 'checkbox',
					'allow_null' => 1,
					'add_term' => 0,
					'save_terms' => 0,
					'load_terms' => 0,
					'return_format' => 'id',
					'multiple' => 0,
				),
				array (
					'key' => 'field_54c03e5d0f3f4',
					'label' => _x('Pin stories','Make news stories sticky and appear at the top','govintranet'),
					'name' => 'pin_stories',
					'type' => 'relationship',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'post_type' => array (
						0 => 'news',
						1 => 'blog',
						2 => 'event',
					),
					'taxonomy' => array (
					),
					'filters' => array (
						0 => 'search',
						1 => 'post_type',
					),
					'elements' => '',
					'max' => '',
					'return_format' => 'id',
					'min' => 0,
				),
				array (
					'key' => 'field_54bfacd9a9fbb',
					'label' => __('Exclude stories','govintranet'),
					'name' => 'exclude_stories',
					'type' => 'relationship',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'post_type' => array (
						0 => 'news',
					),
					'taxonomy' => array (
					),
					'filters' => array (
						0 => 'search',
					),
					'elements' => '',
					'max' => '',
					'return_format' => 'id',
					'min' => 0,
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
			'active' => 1,
			'description' => '',
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
		$acf_key = "widget_" . $this->id_base . "-" . $this->number . "_news_listing_news_type" ;  
		$newstypes = get_option($acf_key); 
	    if ( !$title ) $title = "no_title_" . $id;
	    $moretitle = $instance['moretitle'];
	    $cache = intval($instance['cache']);
	
	    global $post;
	    
		$removenews = get_transient('cached_removenews'); 
		
		if ( !$removenews ){

			set_transient('cached_removenews',"wait",60*3); 

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
					} elseif ($expiryaction=='Change to regular news'){
						set_post_format($old->ID, ''); 
						delete_post_meta($old->ID, 'news_expiry_date');
						delete_post_meta($old->ID, 'news_expiry_time');
						delete_post_meta($old->ID, 'news_expiry_action');
						  delete_post_meta($old->ID, 'news_auto_expiry');
						if (function_exists('wp_cache_post_change')) wp_cache_post_change( $old->ID ) ;
					} elseif ($expiryaction=='Move to trash'){
						  $my_post = array();
						  $my_post['ID'] = $old->ID;
						  $my_post['post_status'] = 'trash';
						  delete_post_meta($old->ID, 'news_expiry_date');
						  delete_post_meta($old->ID, 'news_expiry_time');
						  delete_post_meta($old->ID, 'news_expiry_action');
						  delete_post_meta($old->ID, 'news_auto_expiry');
						  wp_update_post( $my_post );
						  if (function_exists('wp_cache_post_change')) wp_cache_post_change( $old->ID ) ;
					} elseif ($expiryaction=='Change tax'){
						$acf_key = "widget_" . $this->id_base . "-" . $this->number . "_news_listing_news_expiry_type" ;  
						$new_tax = get_post_meta($old->ID,'news_expiry_type',true); 
						$new_tax = intval($new_tax);
						wp_delete_object_term_relationships( $old->ID, 'news-type' );
						if ( $new_tax ) wp_set_object_terms( $old->ID, $new_tax, 'news-type', false );
						delete_post_meta($old->ID, 'news_expiry_date');
						delete_post_meta($old->ID, 'news_expiry_time');
						delete_post_meta($old->ID, 'news_expiry_action');
						delete_post_meta($old->ID, 'news_auto_expiry');
						delete_post_meta($old->ID, 'news_expiry_type');
						if (function_exists('wp_cache_post_change')) wp_cache_post_change( $old->ID ) ;
					}	
				}
			}
			wp_reset_query();
		}
		
		$newstransient = $widget_id;
		$html = "";
		$blank = true;
		if ( $cache > 0 ):
		 	$html = get_transient( $newstransient );
		 	if ( $html ) $blank = false;
		endif;
		
		if ( !$html ){
			
		    $html.= $before_widget; 
			$html.= '<div id="'.$widget_id.'">';
		
		    if ( $title && $title != "no_title_" . $id) :
			    $html.= $before_title;
				$html.= $title;
			    $html.= $after_title; 
			endif;
		
			//formulate grid of news stories and formats
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
			if ( $top_slot ):
				$num_top_slots = count($top_slot);
			else:
				$num_top_slots = 0;
			endif;
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
						if ($video){
							$html.= $video;
						} elseif (has_post_thumbnail($slot->ID)){
							//$img_srcset = wp_get_attachment_image_srcset( get_post_thumbnail_id( $post->ID ), array('newshead','large','medium','thumbnail') );
							//$img_sizes = wp_get_attachment_image_sizes(get_post_thumbnail_id( $post->ID ), 'newshead' ); 
							$html.= "<a href='{$thisURL}'>" . get_the_post_thumbnail($slot->ID, 'newshead', array('class'=>'img-responsive')) . "</a>";
							$html.= wpautop( "<p class='news_date'>".get_post_thumbnail_caption()."</p>" );
						} 
					} 
		
					if ($newsgrid[$k]=="M"){
						$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $slot->ID ), 'newsmedium' );
						if ($image_uri!="" ){
							$html.= "<a href='{$thisURL}'><img class='img img-responsive' src='{$image_uri[0]}' width='{$image_uri[1]}' height='{$image_uri[2]}' alt='".govintranetpress_custom_title($slot->post_title)."' /></a>";									
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
					$thisdate=date(get_option('date_format'),strtotime($thisdate));
					$ext_icon = '';
					if ( get_post_format($slot->ID) == 'link' ) $ext_icon = "<span class='dashicons dashicons-migrate'></span> ";
			
					if ($newsgrid[$k]=="T"){
						$html.= "<div class='media'>".$image_url;
					}
			
					$html.= "<div class='media-body'>";
					$html.= "<h3 class='noborder'>".$ext_icon."<a href='".$thisURL."'>".$thistitle."</a>".$ext_icon."</h3>";
		
					if ($newsgrid[$k]=="Li"){
						$html.= "<p>";
						$html.= '<span class="listglyph">'.get_the_date(get_option('date_format')); 
						$html.= '</span> ';
						$html.= " <span class='badge badge-featured'>Featured</span>";
						if ( get_comments_number() ){
							$html.= "<a href='".$thisURL."#comments'>";
							$html.= '<span class="badge badge-comment">' . sprintf( _n( '1 comment', '%d comments', get_comments_number(), 'govintranet' ), get_comments_number() ) . '</span>';
							 $html.= "</a>";
						}
						$html.= " <a class='news_date more' href='{$thisURL}' title='{$thistitle}'>" . __('Full story' , 'govintranet') . " <span class='dashicons dashicons-arrow-right-alt2'></span></a></span></p>";
					} else {
						if ($showexcerpt == 'on') {
							$html.= "<p>";
							$html.= '<span class="listglyph">'.get_the_date(get_option('date_format')); 
							$html.= '</span> ';
							$html.= " <span class='badge badge-featured'>" . __('Featured','govintranet') . "</span>";
							if ( get_comments_number() ){
								$html.= "<a href='".$thisURL."#comments'>";
								$html.= '<span class="badge badge-comment">' . sprintf( _n( '1 comment', '%d comments', get_comments_number(), 'govintranet' ), get_comments_number() ) . '</span>';
								 $html.= "</a>";
							}
							$html.= "</p>";									
							$html.= $thisexcerpt;
							$html.= "<p class='news_date'><a class='more' href='{$thisURL}' title='{$thistitle}'>" . __('Full story' , 'govintranet') . " <span class='dashicons dashicons-arrow-right-alt2'></span></a></p>";
						} else {
							$html.= "<p>";
							$html.= '<span class="listglyph">'.get_the_date(get_option('date_format')); 
							$html.= '</span> ';
							$html.= " <span class='badge'>" . __('Featured','govintranet') . "</span>";
							if ( get_comments_number() ){
								$html.= "<a href='".$thisURL."#comments'>";
								$html.= '<span class="badge badge-comment">' . sprintf( _n( '1 comment', '%d comments', get_comments_number(), 'govintranet' ), get_comments_number() ) . '</span>';
								 $html.= "</a>";
							}
							$html.= " <a class='news_date more' href='{$thisURL}' title='{$thistitle}'>" . __('Full story' , 'govintranet') . " <span class='dashicons dashicons-arrow-right-alt2'></span></a></span></p>";
						}
					}
			
					$html.= "</div>";
			
					if ($newsgrid[$k]=="T"){
						$html.= "</div>";
					}
			
					$html.= "<hr class='light' />\n";
			
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
			if ( $newstypes ) $cquery['tax_query'] = array(
					"relation"=>"AND",
					array(
					'taxonomy' => 'news-type',
					'terms' => $newstypes,
					'field' => 'id',	
					),
					array(
					'taxonomy'=>'post_format',
				    'field'=>'slug',
				    'terms'=>array('post-format-status'),
				    "operator"=>"NOT IN"
	
					),
					);
			
				$news = new WP_Query($cquery);
				$blank = true;
				if ( $news->have_posts() || $num_top_slots ){
					$blank = false;
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
						if ($video){
							$html.= $video;
						} elseif (has_post_thumbnail($post->ID)){
							//$img_srcset = wp_get_attachment_image_srcset( get_post_thumbnail_id( $post->ID ), array('newshead','large','medium','thumbnail') );
							//$img_sizes = wp_get_attachment_image_sizes(get_post_thumbnail_id( $post->ID ), 'newshead' ); 
							$html.= "<a href='{$thisURL}'>" . get_the_post_thumbnail($post->ID, 'newshead', array('class'=>'img-responsive')) . "</a>";
							$html.= wpautop( "<p class='news_date'>".get_post_thumbnail_caption()."</p>" );
						} 
					} 
			
					if ($newsgrid[$k]=="M"){
						$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $theid ), 'newsmedium' );
						if ($image_uri!="" ){
							$html.= "<a href='{$thisURL}'><img class='img img-responsive' src='{$image_uri[0]}' width='{$image_uri[1]}' height='{$image_uri[2]}' alt='".govintranetpress_custom_title($post->post_title)."' /></a>";									
						} 
					} 
			
					if ($newsgrid[$k]=="T"){
						$image_uri = "<a class='pull-right' href='{$thisURL}'>".get_the_post_thumbnail($theid, 'thumbnail', array('class' => 'media-object hidden-xs'))."</a>";
						if ($image_uri!="" ){
							$image_url = $image_uri;
						} 
					} 
					
					$thisdate= get_the_date(get_option('date_format')); 
					$thisexcerpt= get_the_excerpt();
					$ext_icon = '';
			
					if ($newsgrid[$k]=="T"){
						$html.= "<div class='media'>".$image_url;
					}
			
					$html.= "<div class='media-body feature-news-".strtolower($newsgrid[$k])."'>";
					if ( get_post_format($theid) == 'link' ) $ext_icon = "<i class='dashicons dashicons-migrate'></i> ";
					$html.= "<h3 class='noborder'><a href='".$thisURL."'>".$thistitle."</a> ".$ext_icon."</h3>";
		
					if ($newsgrid[$k]=="Li"){
							$html.= "<p>";
							$html.= '<span class="listglyph">'.get_the_date(get_option('date_format')); 
							$html.= '</span> ';
							if ( get_comments_number() ){
								$html.= "<a href='".$thisURL."#comments'>";
								$html.= '<span class="badge badge-comment">' . sprintf( _n( '1 comment', '%d comments', get_comments_number(), 'govintranet' ), get_comments_number() ) . '</span>';
								$html.= "</a>";
							}
							$html.= "</p>";									
					} else {
						if ($showexcerpt == 'on') {
							$html.= "<p>";
							$html.= '<span class="listglyph">'.get_the_date(get_option('date_format')); 
							$html.= '</span> ';
							if ( get_comments_number() ){
								$html.= " <a href='".$thisURL."#comments'>";
								$html.= '<span class="badge badge-comment">' . sprintf( _n( '1 comment', '%d comments', get_comments_number(), 'govintranet' ), get_comments_number() ) . '</span>';
								 $html.= "</a>";
							}
							$html.= "</p>";									
							$html.= $thisexcerpt;
							$html.= "<p class='news_date'><a class='more' href='{$thisURL}' title='{$thistitle}'>" . __('Full story' , 'govintranet') . " <span class='dashicons dashicons-arrow-right-alt2'></span></a></p>";
						} else {
							$html.= "<p>";
							$html.= '<span class="listglyph">'.get_the_date(get_option('date_format')); 
							$html.= '</span> ';
						if ( get_comments_number() ){
							$html.= " <a href='".$thisURL."#comments'>";
							$html.= '<span class="badge badge-comment">' . sprintf( _n( '1 comment', '%d comments', get_comments_number(), 'govintranet' ), get_comments_number() ) . '</span>';
							 $html.= "</a>";
						}
							$html.= " <a class='news_date more' href='{$thisURL}' title='{$thistitle}'>" . __('Full story' , 'govintranet') . " <span class='dashicons dashicons-arrow-right-alt2'></span></a></p>";
						}
					}
			
					$html.= "</div>";
			
					if ($newsgrid[$k]=="T"){
						$html.= "</div>";
					}
		
					$html.= "<hr class='light' />\n";
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
		
				if ( !$moretitle ) $moretitle = $title;
				if ( $moretitle = "no_title_" . $id ) $moretitle = __("More","govintranet");
				if ( is_array($newstypes) && count($newstypes) < 2 ): 
					$term = intval($newstypes[0]); 
					$landingpage = get_term_link($term, 'news-type'); 
					$html.= '<p class="more-updates"><strong><a title="'.$landingpage_link_text.'" class="small" href="'.$landingpage.'">'.$moretitle.'</a></strong> <span class="dashicons dashicons-arrow-right-alt2"></span></p>';	
				else: 
					$landingpage_link_text = $moretitle;
					$html.= '<p class="more-updates"><strong><a title="'.$landingpage_link_text.'" class="small" href="'.$landingpage.'">'.$landingpage_link_text.'</a></strong> <span class="dashicons dashicons-arrow-right-alt2"></span></p>';	
				endif;
		
				$html.= "<div class='clearfix'></div>";
				$html.= "</div>";
				$html.= $after_widget; 
				if ( $cache > 0 ) {
					$lock_name = "widget_" . $this->id_base . "_" . $this->number . ".lock" ;  
					global $wpdb;
				    // Try to lock.
				    $lock_result = $wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO `$wpdb->options` ( `option_name`, `option_value`, `autoload` ) VALUES (%s, %s, 'no') /* LOCK */", $lock_name, time() ) );
				 
				    if ( ! $lock_result ) {
				        if ( !$blank ) echo $html;
			            return;
				    }
				 
					set_transient($newstransient,$html."<!-- Cached by GovIntranet at ".date('Y-m-d H:i:s')." -->",$cache * 60 ); // set cache period
					delete_option( $lock_name );
				}
			} 
		if ( !$blank ) echo $html;
    }	

    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['largeitems'] = strip_tags($new_instance['largeitems']);
		$instance['mediumitems'] = strip_tags($new_instance['mediumitems']);
		$instance['thumbnailitems'] = strip_tags($new_instance['thumbnailitems']);
		$instance['listitems'] = strip_tags($new_instance['listitems']);
		$instance['showexcerpt'] = strip_tags($new_instance['showexcerpt']);
		$instance['moretitle'] = strip_tags($new_instance['moretitle']);
		$instance['cache'] = strip_tags($new_instance['cache']);		
       return $instance;
    }

    function form($instance) {
        $title = esc_attr($instance['title']);
        $largeitems = esc_attr($instance['largeitems']);
        $mediumitems = esc_attr($instance['mediumitems']);
        $thumbnailitems = esc_attr($instance['thumbnailitems']);
        $listitems = esc_attr($instance['listitems']);
        $showexcerpt = esc_attr($instance['showexcerpt']);
        $moretitle = esc_attr($instance['moretitle']);
        $cache = esc_attr($instance['cache']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /><br><br>
          <label><?php __('Number of stories','govintranet'); ?></label><br>
          <label for="<?php echo $this->get_field_id('largeitems'); ?>"><?php _e('Large','govintranet'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('largeitems'); ?>" name="<?php echo $this->get_field_name('largeitems'); ?>" type="text" value="<?php echo $largeitems; ?>" /><br><br>
          
          <label for="<?php echo $this->get_field_id('mediumitems'); ?>"><?php _e('Medium','govintranet'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('mediumitems'); ?>" name="<?php echo $this->get_field_name('mediumitems'); ?>" type="text" value="<?php echo $mediumitems; ?>" /><br><br>

          <label for="<?php echo $this->get_field_id('thumbnailitems'); ?>"><?php _e('Thumbnail','govintranet'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('thumbnailitems'); ?>" name="<?php echo $this->get_field_name('thumbnailitems'); ?>" type="text" value="<?php echo $thumbnailitems; ?>" /><br><br>
          
          <label for="<?php echo $this->get_field_id('listitems'); ?>"><?php _e('List format (no photos)','govintranet'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('listitems'); ?>" name="<?php echo $this->get_field_name('listitems'); ?>" type="text" value="<?php echo $listitems; ?>" /><br><br>

          <input id="<?php echo $this->get_field_id('showexcerpt'); ?>" name="<?php echo $this->get_field_name('showexcerpt'); ?>" type="checkbox" <?php checked((bool) $instance['showexcerpt'], true ); ?> />
          <label for="<?php echo $this->get_field_id('showexcerpt'); ?>"><?php _e('Show excerpt','govintranet'); ?></label> <br>
        </p>
          <label for="<?php echo $this->get_field_id('moretitle'); ?>"><?php _e('Title for more:','govintranet'); ?></label> <br>
          <input class="widefat" id="<?php echo $this->get_field_id('moretitle'); ?>" name="<?php echo $this->get_field_name('moretitle'); ?>" type="text" value="<?php echo $moretitle; ?>" /><br><?php _e('Leave blank for the default title','govintranet');?><br><br>
          <label for="<?php echo $this->get_field_id('cache'); ?>"><?php _e('Minutes to cache:','govintranet'); ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('cache'); ?>" name="<?php echo $this->get_field_name('cache'); ?>" type="text" value="<?php echo $cache; ?>" /><br><br>

        <?php 
    }

}

add_action('widgets_init', create_function('', 'return register_widget("htFeatureNews");'));

?>
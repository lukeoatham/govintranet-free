<?php
/*
Plugin Name: HT Feature blogposts
Plugin URI: https://help.govintra.net
Description: Display blogposts
Author: Luke Oatham
Version: 1.5.4
Author URI: https://www.agentodigital.com

*/

class htFeatureBlogposts extends WP_Widget {

	function __construct() {
		
		parent::__construct(
			'htFeatureBlogposts',
			__( 'HT Feature blogposts' , 'govintranet'),
			array( 'description' => __( 'Blogpost listing widget' , 'govintranet') )
		);        
		
		if( function_exists('acf_add_local_field_group') ):

			acf_add_local_field_group(array (
				'key' => 'group_562a555eac132',
				'title' => __('Feature blog widget','govintranet'),
				'fields' => array (
					array(
						'key' => 'field_5aac4cad2be0f',
						'label' => __('Show thumbnails','govintranet'),
						'name' => 'thumbnails',
						'type' => 'true_false',
						'instructions' => __('Displays the featured image if present, otherwise the author avatar.','govintranet'),
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'message' => '',
						'default_value' => 0,
						'ui' => 1,
						'ui_on_text' => '',
						'ui_off_text' => '',
					),
					array(
						'key' => 'field_5aac4cbd2be10',
						'label' => __('Force author avatar','govintranet'),
						'name' => 'force_author_avatar',
						'type' => 'true_false',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_5aac4cad2be0f',
									'operator' => '==',
									'value' => '1',
								),
							),
						),
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'message' => '',
						'default_value' => 0,
						'ui' => 1,
						'ui_on_text' => '',
						'ui_off_text' => '',
					),
					array (
						'key' => 'field_562a555eb8501',
						'label' => __('Pin posts','govintranet'),
						'name' => 'pin_posts',
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
						'min' => '',
						'max' => '',
						'return_format' => 'id',
					),
					array (
						'key' => 'field_56b9356df717b',
						'label' => __('Blog categories','govintranet'),
						'name' => 'blog_categories',
						'type' => 'taxonomy',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array (
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'taxonomy' => 'blog-category',
						'field_type' => 'checkbox',
						'allow_null' => 1,
						'add_term' => 0,
						'save_terms' => 0,
						'load_terms' => 0,
						'return_format' => 'id',
						'multiple' => 0,
					),
				),
				'location' => array (
					array (
						array (
							'param' => 'widget',
							'operator' => '==',
							'value' => 'htfeatureblogposts',
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
        $items = intval($instance['items']);
        $thumbnails = get_field('thumbnails', 'widget_' . $widget_id); 
        $forceavatar = get_field('force_author_avatar', 'widget_' . $widget_id); 
        $freshness = intval($instance['freshness']);
        if ( !$freshness ) $freshness = 14;
        $more = $instance['more'];
        $moretitle = $instance['moretitle'];
        $excerpt = $instance['excerpt'];
        $cache = intval($instance['cache']);
		$tzone = get_option('timezone_string');
		if ( $tzone ) date_default_timezone_set($tzone);
		$tdate=date('Y-m-d')." 00:00:00";
		$freshness = "-".$freshness." day ";
        $tdate = date ( 'F jS, Y', strtotime ( $freshness . $tdate ) );  
		$top_slot = get_field('pin_posts', 'widget_' . $widget_id); 
		$blog_categories = get_field('blog_categories', 'widget_' . $widget_id); 
		$num_top_slots = 0;
		if ( is_array($top_slot) ) $num_top_slots = count($top_slot); 
		$to_fill = $items - $num_top_slots; 
		$k = -1;
		$alreadydone = array();
		$titledone = 0;
		$directorystyle = get_option('options_staff_directory_style'); // 0 = squares, 1 = circles
		$avstyle = "";
		if ( $directorystyle==1 ) $avstyle = " img-circle ";
		$blogstransient = $widget_id;
		$html = "";
		if ( $cache > 0 ) $html = get_transient( $blogstransient );

		if ( !$html ){

			if ( $num_top_slots > 0 ){ 
				$html.= $before_widget; 
				if ( $title ) {
					$html.= $before_title . $title . $after_title;
				}
				$html.= "<div class='widget-area widget-blogposts'>";
				$titledone = 1;
				$cquery = array(
			    'post_type' => array('blog','news','event'),
				'posts_per_page' => -1,
				'post__in' => $top_slot,
				);
				
				$blogs = new WP_Query($cquery);
				if ( $blogs->have_posts() ) while ( $blogs->have_posts() ):
					$blogs->the_post(); 
					$k++;
					$alreadydone[] = get_the_id();
					$thistitle = get_the_title();
					$edate = $post->post_date; 
					if (!$edate) $edate = get_the_date();
					$edate = date(get_option('date_format'),strtotime($edate));
					$thisURL = get_permalink();
					$html.= "<div class='media'>";
					if ($thumbnails){
						$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( ), 'thumbnail' ); 
						if (!$image_uri || $forceavatar){
							$image_uri = get_avatar($post->post_author , 72, "", get_user_meta( $post->post_author, 'display_name', true), array('class'=>$avstyle));
							$image_uri = str_replace("avatar ", "avatar ".$avstyle, $image_uri);
							$html.= "<a class='pull-left' href='".get_permalink(get_the_id())."'>{$image_uri}</a>";		
						} else {
							$html.= "<a class='pull-left' href='".get_permalink(get_the_id())."'><img class='tinyblogthumb alignleft' src='{$image_uri[0]}' alt='".esc_attr($thistitle)."' /></a>";					}
					}
					$html.= "<div class='media-body'><a href='{$thisURL}'>".$thistitle."</a>";
					$html.= "<br><span class='news_date'>".$edate." by ";
					$html.= get_the_author();
					$html.= "</span>";
					$html.= " <span class='badge badge-featured'>" . __('Featured','govintranet') . "</span>"; 
					if ( get_comments_number() ){
						$html.= " <a href='".$thisURL."#comments'>";
						$html.= ' <span class="badge badge-comment">' . sprintf( _n( '1 comment', '%d comments', get_comments_number(), 'govintranet' ), get_comments_number() ) . '</span>';
						$html.= "</a>";
					}
					if ($excerpt == 'on') $html.=get_the_excerpt();
					$html.= "</div></div><hr class='light'>";
				endwhile;		
			};
			
			//fetch fresh blogposts 
	
			$cquery = array(
			    'post_type' => 'blog',
				'posts_per_page' => $to_fill,
				'post__not_in' => $alreadydone,
				'date_query' => array(
						array(
							'after'     => date('Ymd',strtotime($tdate)),
							'inclusive' => true,
						)
					)
			);
			
			//restrict to chosen categories, if any
			if ( is_array($blog_categories) )
					$cquery['tax_query'] = array(array(
					    'taxonomy' => 'blog-category',
					    'field' => 'id',
					    'terms' => (array)$blog_categories,
					    'compare' => "IN",
				    ));
	
			$blogs = new WP_Query($cquery);
			if ($blogs->post_count!=0 && !$titledone ) {
				$html.= $before_widget; 
				if ( $title ) {
					$html.= $before_title . $title . $after_title;
				}
				$html.= "<div class='widget-area widget-blogposts'>";
			}
			$k = 0;
			if ($blogs->have_posts()) while ($blogs->have_posts()) {
				$blogs->the_post();
				global $post;//required for access within widget
				$k++;
				if ($k > $to_fill){
					break;
				}
				$thistitle = get_the_title();
				$edate = $post->post_date;
				if (!$edate) {
					$edate = get_the_date();
				}
				$edate = date(get_option('date_format'),strtotime($edate));
				$thisURL=get_permalink(); 
				$html.= "<div class='media'>";
				if ($thumbnails){
					$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( ), 'thumbnail' ); 
					if (!$image_uri || $forceavatar){
						$image_uri = get_avatar($post->post_author , 72, "", get_user_meta( $post->post_author, 'display_name', true), array('class'=>$avstyle));
						$image_uri = str_replace("avatar ", "avatar ".$avstyle, $image_uri);
						$html.= "<a class='pull-left' href='".get_permalink()."'>{$image_uri}</a>";		
					} else {
						$html.= "<a class='pull-left' href='".get_permalink()."'><img class='tinyblogthumb alignleft' src='{$image_uri[0]}' alt='".esc_attr($thistitle)."' /></a>";						
					}
				}
				$html.= "<div class='media-body'><a href='{$thisURL}'>".$thistitle."</a>";
				$html.= "<br><span class='news_date'>".$edate." by ";
				$html.= get_the_author();
				$html.= "</span>";
				if ( get_comments_number() ){
					$html.= " <a href='".$thisURL."#comments'>";
					$html.= '<span class="badge badge-comment">' . sprintf( _n( '1 comment', '%d comments', get_comments_number(), 'govintranet' ), get_comments_number() ) . '</span>';
					$html.= "</a>";
				}
				if ($excerpt == 'on') $html.=wpautop(get_the_excerpt());
				$html.= "</div></div><hr class='light'>";
			}
			if ($blogs->have_posts() && $more){

				$landingpage = get_option('options_module_blog_page'); 
				if ( !$landingpage ):
					$landingpage_link_text = 'blogposts';
					if ( $moretitle ) $landingpage_link_text = $moretitle;
					$landingpage = site_url().'/blogposts/';
				else:
					$landingpage_link_text = get_the_title( $landingpage[0] );
					if ( $moretitle ) $landingpage_link_text = $moretitle;
					$landingpage = get_permalink( $landingpage[0] );
				endif;
				
				if ( is_array($blog_categories) && count($blog_categories) < 2 ): 
					$term = intval($blog_categories[0]); 
					$landingpage = get_term_link($term, 'blog-category'); 
				endif;
				
				$html.= '<p><strong><a title="' . $landingpage_link_text . '" class="small" href="'.$landingpage.'">'.$landingpage_link_text.'</a></strong> <span class="dashicons dashicons-arrow-right-alt2"></span></p>';
			} 
			if ( $blogs->have_posts() || $num_top_slots > 0 ){
				$html.= '</div>';
				$html.= $after_widget;
			}
			if ( $cache > 0 ) {
				if ( $blogs->have_posts() ){
					set_transient($blogstransient,$html."<!-- Cached by GovIntranet at ".date('Y-m-d H:i:s')." -->",$cache * 60 ); // set cache period	
				} else {
					set_transient($blogstransient,"<!-- Cached by GovIntranet at ".date('Y-m-d H:i:s')." -->",$cache * 60 ); // set cache period
				}
			}
			wp_reset_query();								
		}
		
		echo $html;
    }

    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['items'] = strip_tags($new_instance['items']);
		$instance['freshness'] = strip_tags($new_instance['freshness']);
		$instance['more'] = strip_tags($new_instance['more']);
		$instance['moretitle'] = strip_tags($new_instance['moretitle']);
		$instance['excerpt'] = strip_tags($new_instance['excerpt']);
		$instance['cache'] = intval($new_instance['cache']);	
       return $instance;
    }

    function form($instance) {
		$title = esc_attr($instance['title']);
		$items = esc_attr($instance['items']);
		$freshness = esc_attr($instance['freshness']);
		$excerpt = esc_attr($instance['excerpt']);
		$more = esc_attr($instance['more']);
		$moretitle = esc_attr($instance['moretitle']);
		$cache = intval($instance['cache']);
		?>
		<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','govintranet'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /><br><br>
		<label for="<?php echo $this->get_field_id('items'); ?>"><?php _e('Number of items:','govintranet'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('items'); ?>" name="<?php echo $this->get_field_name('items'); ?>" type="text" value="<?php echo $items; ?>" /><br><br>
		<label for="<?php echo $this->get_field_id('freshness'); ?>"><?php _e('Freshness (days)','govintranet'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('freshness'); ?>" name="<?php echo $this->get_field_name('freshness'); ?>" type="text" value="<?php echo $freshness; ?>" /><br><br>
		<input id="<?php echo $this->get_field_id('excerpt'); ?>" name="<?php echo $this->get_field_name('excerpt'); ?>" type="checkbox" <?php checked((bool) $instance['excerpt'], true ); ?> />
		<label for="<?php echo $this->get_field_id('excerpt'); ?>"><?php _e('Show excerpt','govintranet'); ?></label> <br><br>
		<input id="<?php echo $this->get_field_id('more'); ?>" name="<?php echo $this->get_field_name('more'); ?>" type="checkbox" <?php checked((bool) $instance['more'], true ); ?> />
		<label for="<?php echo $this->get_field_id('more'); ?>"><?php _e('Show link to more','govintranet'); ?></label> <br><br>
		<label for="<?php echo $this->get_field_id('moretitle'); ?>"><?php _e('Title for more','govintranet'); ?></label> <br>
          <input class="widefat" id="<?php echo $this->get_field_id('moretitle'); ?>" name="<?php echo $this->get_field_name('moretitle'); ?>" type="text" value="<?php echo $moretitle; ?>" /><br><?php _e('Leave blank for the default title','govintranet');?><br><br>
          <label for="<?php echo $this->get_field_id('cache'); ?>"><?php _e('Minutes to cache:','govintranet'); ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('cache'); ?>" name="<?php echo $this->get_field_name('cache'); ?>" type="text" value="<?php echo $cache; ?>" /><br>

        </p>
        <?php 
    }
}

add_action('widgets_init', create_function('', 'return register_widget("htFeatureBlogposts");'));

?>
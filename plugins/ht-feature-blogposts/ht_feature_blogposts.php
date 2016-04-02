<?php
/*
Plugin Name: HT Feature blogposts
Plugin URI: http://www.helpfultechnology.com
Description: Display blogposts
Author: Luke Oatham
Version: 1.1
Author URI: http://www.helpfultechnology.com

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
				'title' => 'Feature blog widget',
				'fields' => array (
					array (
						'key' => 'field_562a555eb8501',
						'label' => 'Pin posts',
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
						'label' => 'Blog categories',
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
        $thumbnails = $instance['thumbnails'];
        $freshness = $instance['freshness'];
        if ( !$freshness ) $freshness = 14;
        $more = $instance['more'];
        $excerpt = $instance['excerpt'];
		$tdate=date('Y-m-d')." 00:00";
		$freshness = "-".$freshness." day";
        $tdate = date ( 'F jS, Y', strtotime ( $freshness . $tdate ) );  
		$acf_key = "widget_" . $this->id_base . "-" . $this->number . "_pin_posts" ;  
		$top_slot = get_option($acf_key); 
		$acf_key = "widget_" . $this->id_base . "-" . $this->number . "_blog_categories" ;  
		$blog_categories = get_option($acf_key); 
		$num_top_slots = 0;
		if ( is_array($top_slot) ) $num_top_slots = count($top_slot); 
		$to_fill = $items - $num_top_slots;
		$k = -1;
		$alreadydone = array();
		$titledone = 0;
	
		if ( $num_top_slots > 0 ){ 
			if ( $title ) {
				echo $before_widget; 
				echo $before_title . $title . $after_title;
			}
			echo "<div class='widget-area widget-blogposts'>";
			$titledone = 1;
			$cquery = array(
		    'post_type' => 'blog',
			'posts_per_page' => -1,
			'post__in' => $top_slot,
			);
			
			$news =new WP_Query($cquery);
			if ( $news->have_posts() ) while ( $news->have_posts() ):
				$news->the_post(); 
				$k++;
				$alreadydone[] = get_the_id();
				$thistitle = get_the_title();
				$edate = get_the_date($post->ID);
				$edate = date(get_option('date_format'),strtotime($edate));
				$thisURL=get_permalink();
				echo "<div class='media'>";
				if ($thumbnails=='on'){
					$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'thumbnail' ); 
					if (!$image_uri){
						$image_uri = get_avatar(get_the_author_id(),72);
						$image_uri = str_replace("alignleft", "alignleft tinyblogthumb", $image_uri);
						echo "<a class='pull-left' href='".get_permalink(get_the_id())."'>{$image_uri}</a>";		
					} else {
						echo "<a class='pull-left' href='".get_permalink(get_the_id())."'><img class='tinyblogthumb alignleft' src='{$image_uri[0]}' alt='".$thistitle."' /></a>";					}
				}
				echo "<div class='media-body'><a href='{$thisURL}'>".$thistitle."</a>";
				echo "<br><span class='news_date'>".$edate." by ";
				echo get_the_author();
				echo "</span>";
				echo " <span class='badge'>" . __('Featured','govintranet') . "</span>"; 
				if ( get_comments_number() ){
					echo " <a href='".$thisURL."#comments'>";
					printf( _n( '<span class="badge">1 comment</span>', '<span class="badge">%d comments</span>', get_comments_number(), 'govintranet' ), get_comments_number() );
					echo "</a>";
				}
				if ($excerpt == 'on') the_excerpt();
				echo "</div></div>";
			endwhile;		
		};
		
		//fetch fresh blogposts 

		$cquery = array(
		    'post_type' => 'blog',
			'posts_per_page' => $to_fill,
			'post__not_in' => $alreadydone,
			'date_query' => array(
					array(
						'after'     => $tdate,
						'inclusive' => true,
					)
				)
		);
		
		//restrict to chosen categories, if any
		if ( $blog_categories )
				$cquery['tax_query'] = array(array(
				    'taxonomy' => 'blog-category',
				    'field' => 'id',
				    'terms' => (array)$blog_categories,
				    'compare' => "IN",
			    ));

		$news =new WP_Query($cquery);
		if ($news->post_count!=0 && !$titledone ) {
			if ( $title ) {
				echo $before_widget; 
				echo $before_title . $title . $after_title;
			}
			echo "<div class='widget-area widget-blogposts'>";
		}
		$k=0;
		while ($news->have_posts()) {
			$news->the_post();
			$k++;
			if ($k > $to_fill){
				break;
			}
			global $post;//required for access within widget
			$thistitle = get_the_title($post->ID);
			$edate = $post->post_date;
			$edate = date(get_option('date_format'),strtotime($edate));
			$thisURL=get_permalink(); 
			echo "<div class='media'>";
			if ($thumbnails=='on'){
				$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'thumbnail' ); 
				if (!$image_uri){
					$image_uri = get_avatar($post->post_author,72);
					$image_uri = str_replace("alignleft", "alignleft tinyblogthumb", $image_uri);
					echo "<a class='pull-left' href='".get_permalink($post->ID)."'>{$image_uri}</a>";		
				} else {
					echo "<a class='pull-left' href='".get_permalink($post->ID)."'><img class='tinyblogthumb alignleft' src='{$image_uri[0]}' alt='".$thistitle."' /></a>";				}
			}
			echo "<div class='media-body'><a href='{$thisURL}'>".$thistitle."</a>";
			echo "<br><span class='news_date'>".$edate." by ";
			echo get_the_author();
			echo "</span>";
			if ( get_comments_number() ){
				echo " <a href='".$thisURL."#comments'>";
				printf( _n( '<span class="badge">1 comment</span>', '<span class="badge">%d comments</span>', get_comments_number(), 'govintranet' ), get_comments_number() );
				echo "</a>";
			}
			if ($excerpt == 'on') the_excerpt();
			echo "</div></div>";
		}
		if ($news->have_posts() && $more){
			$landingpage = get_option('options_module_blog_page'); 
			if ( !$landingpage ):
				$landingpage_link_text = 'blogposts';
				$landingpage = site_url().'/blogposts/';
			else:
				$landingpage_link_text = get_the_title( $landingpage[0] );
				$landingpage = get_permalink( $landingpage[0] );
			endif;
			echo '<hr><p><strong><a title="{$landingpage_link_text}" class="small" href="'.$landingpage.'">'.$landingpage_link_text.'</a></strong> <span class="dashicons dashicons-arrow-right-alt2"></span></p>';
		} 
		if ($news->have_posts() || $num_top_slots > 0 ){
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
		$instance['excerpt'] = strip_tags($new_instance['excerpt']);
       return $instance;
    }

    function form($instance) {
		$title = esc_attr($instance['title']);
		$items = esc_attr($instance['items']);
		$thumbnails = esc_attr($instance['thumbnails']);
		$freshness = esc_attr($instance['freshness']);
		$more = esc_attr($instance['more']);
		$more = esc_attr($instance['excerpt']);
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
		<input id="<?php echo $this->get_field_id('thumbnails'); ?>" name="<?php echo $this->get_field_name('thumbnails'); ?>" type="checkbox" <?php checked((bool) $instance['thumbnails'], true ); ?> />
		<label for="<?php echo $this->get_field_id('thumbnails'); ?>"><?php _e('Show thumbnails','govintranet'); ?></label> 
		<p><?php _e('Displays the featured image if present, otherwise the author avatar','govintranet'); ?>.</p>
		<br><br>
		<input id="<?php echo $this->get_field_id('more'); ?>" name="<?php echo $this->get_field_name('more'); ?>" type="checkbox" <?php checked((bool) $instance['more'], true ); ?> />
		<label for="<?php echo $this->get_field_id('more'); ?>"><?php _e('Show link to more','govintranet'); ?></label> <br><br>
        </p>
        <?php 
    }
}

add_action('widgets_init', create_function('', 'return register_widget("htFeatureBlogposts");'));

?>
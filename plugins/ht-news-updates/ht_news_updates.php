<?php
/*
Plugin Name: HT News updates
Plugin URI: http://www.helpfultechnology.com
Description: Display news updates configurable by type
Author: Luke Oatham
Version: 1.0
Author URI: http://www.helpfultechnology.com
*/

class htNewsUpdates extends WP_Widget {
    function htNewsUpdates() {
        parent::WP_Widget(false, 'HT News Updates', array('description' => 'News Updates widget'));
    }

    function widget($args, $instance) {
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        $moretitle = $instance['moretitle'];
        $items = intval($instance['items']);

		//process expired news
		
		$tzone = get_option('timezone_string'); 
		date_default_timezone_set($tzone);
		$tdate= date('Ymd');
		
		$oldnews = query_posts(array(
		'post_type'=>'news-update',
		'meta_query'=>array(array(
		'key'=>'news_update_expiry_date',
		'value'=>$tdate,
		'compare'=>'<='
		))));
		if ( count($oldnews) > 0 ){
			foreach ($oldnews as $old) {
				if ($tdate == date('Ymd',strtotime(get_post_meta($old->ID,'news_update_expiry_date',true)) )): // if expiry today, check the time
					if (date('H:i:s',strtotime(get_post_meta($old->ID,'news_update_expiry_time',true))) > date('H:i:s') ) continue;
				endif;
				
				$expiryaction = get_post_meta($old->ID,'news_update_expiry_action',true);
				if ($expiryaction=='Revert to draft status'){
					  $my_post = array();
					  $my_post['ID'] = $old->ID;
					  $my_post['post_status'] = 'draft';
					  wp_update_post( $my_post );
					  delete_post_meta($old->ID, 'news_update_expiry_date');
					  delete_post_meta($old->ID, 'news_update_expiry_time');
					  delete_post_meta($old->ID, 'news_update_expiry_action');
					  delete_post_meta($old->ID, 'news_auto_expiry');
					  if (function_exists('wp_cache_post_change')) wp_cache_post_change( $old->ID ) ;
					  if (function_exists('wp_cache_post_change')) wp_cache_post_change( $my_post ) ;		  
				}	
				if ($expiryaction=='Move to trash'){
					  $my_post = array();
					  $my_post['ID'] = $old->ID;
					  $my_post['post_status'] = 'trash';
					  delete_post_meta($old->ID, 'news_update_expiry_date');
					  delete_post_meta($old->ID, 'news_update_expiry_time');
					  delete_post_meta($old->ID, 'news_update_expiry_action');
					  delete_post_meta($old->ID, 'news_auto_expiry');
					  wp_update_post( $my_post );
					  if (function_exists('wp_cache_post_change')) wp_cache_post_change( $old->ID ) ;
					  if (function_exists('wp_cache_post_change')) wp_cache_post_change( $my_post ) ;		  
				}	
			}
		wp_reset_query();
		}

		//display need to know stories
		$acf_key = "widget_" . $this->id_base . "-" . $this->number . "_news_update_widget_include_type" ;  
		$news_update_types = get_option($acf_key); 
		if ($icon=='') $icon = get_option('options_need_to_know_icon');
		if ($icon=='') $icon = "flag";

		$acf_key = "widget_" . $this->id_base . "-" . $this->number . "_news_update_background_colour" ;  
		$background_colour = get_option($acf_key); 
		$acf_key = "widget_" . $this->id_base . "-" . $this->number . "_news_update_text_colour" ;  
		$text_colour = get_option($acf_key); 
		$acf_key = "widget_" . $this->id_base . "-" . $this->number . "_news_update_border_colour" ;  
		$border_colour = get_option($acf_key); 
		$border_height = get_option('options_widget_border_height','5');


		if ( !$news_update_types || $news_update_types=="None") :
			$cquery = array(
				'orderby' => 'post_date',
			    'order' => 'DESC',
			    'post_type' => 'news-update',
			    'posts_per_page' => $items,
				);
		else:
			$cquery = array(
				'orderby' => 'post_date',
			    'order' => 'DESC',
			    'post_type' => 'news-update',
			    'posts_per_page' => $items,
			    'tax_query' => array(array(
				    'taxonomy' => 'news-update-type',
				    'field' => 'id',
				    'terms' => $news_update_types,
			    ))
				);
		endif;


		$news =new WP_Query($cquery);
		if ($news->post_count!=0){
		echo "<style>";
		if ( $border_colour ):
			echo ".need-to-know-container.".sanitize_file_name($title)." { background-color: ".$background_colour."; color: ".$text_colour."; padding: 1em; margin-top: 16px; border-top: ".$border_height."px solid ".$border_colour." ; }\n";
		else:
			echo ".need-to-know-container.".sanitize_file_name($title)." { background-color: ".$background_colour."; color: ".$text_colour."; padding: 1em; margin-top: 16px; border-top: 5px solid rgba(0, 0, 0, 0.45); }\n";
		endif;
		echo ".need-to-know-container.".sanitize_file_name($title)." h3 { background-color: ".$background_colour."; color: ".$text_colour."; }\n";
		echo "#content .need-to-know-container.".sanitize_file_name($title)." h3 { background-color: ".$background_colour."; color: ".$text_colour."; }\n";
		echo ".need-to-know-container.".sanitize_file_name($title)." a { color: ".$text_colour."; }\n";
		echo ".need-to-know-container.".sanitize_file_name($title)." .category-block { background: ".$background_colour."; }\n";
		echo ".need-to-know-container.".sanitize_file_name($title)." .category-block h3 { padding: 0 0 10px 0; margin-top: 0; border: none ; color: ".$text_colour."; }\n";
		echo ".need-to-know-container.".sanitize_file_name($title)." .category-block ul li { border-top: 1px solid rgba(255, 255, 255, 0.45); }\n";
		echo "#content .need-to-know-container.".sanitize_file_name($title)." .category-block ul li { border-top: 1px solid rgba(255, 255, 255, 0.45); }\n";
		echo ".need-to-know-container.".sanitize_file_name($title)." .category-block p.more-updates { margin-bottom: 0 !important; margin-top: 10px; font-weight: bold; }\n";
		echo "#content .need-to-know-container .widget-box { background-color: transparent;}";
		echo "#content .need-to-know-container .widget-box { border-top: 0; margin-top: 0;}";
		echo "</style>";	


		if ( $title ) {
		echo "<div class='need-to-know-container ".sanitize_file_name($title)."'>";
		echo $before_widget; 

		echo $before_title . $title . $after_title;}
			echo "
			<div class='need-to-know'>
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
			if ($k > $items){
				break;
			}
			$thistitle = get_the_title($post->ID);

			$thisURL=get_permalink($post->ID);
			$display_types = '';
			$display_types = array();
			$types_array = get_the_terms($post->ID, 'news-update-type'); 
			if ( $types_array) foreach ( $types_array as $t){
					$display_types[] = $t->name;
					$icon = get_option('news-update-type_'.$t->term_id.'_news_update_icon'); 
					if ($icon=='') $icon = get_option('options_need_to_know_icon');
					if ($icon=='') $icon = "flag";
					$display_types = implode(", ", $display_types).""; 
				}
			echo "<li><a href='{$thisURL}' title='".$display_types." update'><span class='glyphicon glyphicon-".$icon."'></span> ".$thistitle."</a>";
			comments_number( '', ' <span class="badge">1 comment</span>', ' <span class="badge">% comments</span>' );
			
			echo "</li>";
		}
		if ($news->post_count!=0){
			echo "</ul></div>";
			if ( !$moretitle ) $moretitle = $title;
			if ( $news_update_types ): 
				$landingpage = get_term_link($types_array[0]->term_id, 'news-update-type');
				echo '<p class="more-updates"><a title="'.$landingpage_link_text.'" class="small" href="'.$landingpage.'">'.$moretitle.'</a> <span class="dashicons dashicons-arrow-right-alt2"></span></p>';	
			else:
				$landingpage_link_text = $moretitle;
				$landingpage = site_url().'/news-update/';
				echo '<p class="more-updates"><a title="'.$landingpage_link_text.'" class="small" href="'.$landingpage.'">'.$landingpage_link_text.'</a> <span class="dashicons dashicons-arrow-right-alt2"></span></p>';	
			endif;
			echo "<div class='clearfix'></div>";			
			echo $after_widget;
			echo "</div>";
		}
		
		wp_reset_query();								

    }

    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['items'] = strip_tags($new_instance['items']);
		$instance['moretitle'] = strip_tags($new_instance['moretitle']);
       return $instance;
    }

    function form($instance) {
        $title = esc_attr($instance['title']);
        $items = esc_attr($instance['items']);
        $moretitle = esc_attr($instance['moretitle']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /><br><br>

          <label for="<?php echo $this->get_field_id('items'); ?>"><?php _e('Number of items:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('items'); ?>" name="<?php echo $this->get_field_name('items'); ?>" type="text" value="<?php echo $items; ?>" /><br><br>

          <label for="<?php echo $this->get_field_id('moretitle'); ?>"><?php _e('Title for more:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('mroetitle'); ?>" name="<?php echo $this->get_field_name('moretitle'); ?>" type="text" value="<?php echo $moretitle; ?>" /><br>Leave blank for the default title<br>
          
        </p>

        <?php 
    }

}

add_action('widgets_init', create_function('', 'return register_widget("htnewsupdates");'));


?>
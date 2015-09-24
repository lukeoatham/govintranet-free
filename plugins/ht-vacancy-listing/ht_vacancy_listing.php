<?php
/*
Plugin Name: HT Vacancy listing
Plugin URI: http://www.helpfultechnology.com
Description: Display closing vacancies
Author: Luke Oatham
Version: 0.1
Author URI: http://www.helpfultechnology.com
*/

class htVacancyListing extends WP_Widget {
    function htVacancyListing() {
        parent::WP_Widget(false, 'HT Vacancy listing', array('description' => 'Vacancy listing widget'));
    }

    function widget($args, $instance) {
	    extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        $items = intval($instance['items']);
        $days = intval($instance['days']);
        $cacheperiod = intval($instance['cacheperiod']);
        if ( isset($cacheperiod) && $cacheperiod ){ $cacheperiod = 60 * $cacheperiod; } 
        if ( !intval($cacheperiod) ) $cacheperiod = 60 * 60;
        
		wp_register_style( 'ht-vacancy-listing', plugin_dir_url("/") . "ht-vacancy-listing/ht_vacancy_listing.css" );
		wp_enqueue_style( 'ht-vacancy-listing' );

		$gatransient = substr( 'vacancy_'.$widget_id.'_'.sanitize_file_name( $title ) , 0, 45 );
		$output = get_transient( $gatransient );

		if ( $output == '' ):

			$tzone = get_option('timezone_string');
			date_default_timezone_set($tzone);
	
			//display forthcoming events
			$checkdate = date('Ymd'); 
			$numdays = "+".$days." day";
			$sdate = date('Ymd', date( strtotime( $numdays, strtotime( $checkdate ) ) ) ); 
			$stime = date('H:i'); 
			$cquery = array(
	
			   'meta_query' => array(
				   'relation' => 'OR',
			       array(
				       'relation' => 'AND',
				       array(
				           'key' => 'vacancy_closing_date',
				           'value' => $checkdate,
				           'compare' => '=',
					   		'type' => 'DATE' 
				       ),
				       array(
				           'key' => 'vacancy_closing_time',
				           'value' => $stime,
				           'compare' => '>',
			           ),
			       ),
			       array(
				       'relation' => 'AND',
				       array(
			           		'key' => 'vacancy_closing_date',
					   		'value' => $sdate,
					   		'compare' => '<',
					   		'type' => 'DATE' 
					   		),
				       array(
			           		'key' => 'vacancy_closing_date',
					   		'value' => $checkdate,
					   		'compare' => '>',
					   		'type' => 'DATE' 
					   		) 
				        ),   
			        ),   
				    'orderby' => 'meta_value',
				    'meta_key' => 'vacancy_closing_date',
				    'order' => 'ASC',
				    'post_type' => 'vacancy',
					'posts_per_page' => $items,
			);
	
			$news =new WP_Query($cquery);
			if ($news->post_count!=0){
				echo "
			    <style>
				.upcoming-vacancies .date-stamp {
					border: 3px solid ".get_theme_mod('header_background', '0b2d49').";
				}
				.upcoming-vacancies .date-stamp em {
					background: ".get_theme_mod('header_background', '0b2d49').";
					color: #".get_header_textcolor().";
				}
			    </style>
			    ";
	
				echo $before_widget; 
	
	
				if ( $title ) {
					echo $before_title . $title . $after_title;
				}
				echo "<div class='widget-area widget-vacancies'>";
				if ($thumbnails!='on') echo "<div class='upcoming-vacancies'><ul>";
			}
			$k=0;
			$alreadydone= array();
	
			while ($news->have_posts()) {
				$news->the_post();
				//don't show if already in stickies
				if (in_array($post->ID, $alreadydone )) continue;
				$k++;
				if ($k > $items) break;
				global $post; //required for access within widget
				$thistitle = get_the_title($post->ID);
				$edate = get_post_meta($post->ID,'vacancy_closing_date',true);
				$etime = date('H:i',strtotime(get_post_meta($post->ID,'vacancy_closing_time',true))); 
				$edate = date('D j M',strtotime($edate));
	
				$thisURL=get_permalink($ID); 
				if ($thumbnails=='on'){
					$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $ID ), 'thumbnail' ); 
					if ($image_uri!="" ){
						echo "<div class='media'>";
						echo "<a class='pull-right' href='".get_permalink($post->ID)."'><img class='tinythumb' src='{$image_uri[0]}' alt='".$thistitle."' /></a>";		
						echo "<div class='media-body'><a href='{$thisURL}'> ".$thistitle."</a><br><small>".$edate."</small>";
						echo "</div></div>";
					} else {
						echo "<div class='media'><a href='{$thisURL}'> ".$thistitle."</a><br><small>".$edate."</small></div>";
					} 
				} else {
					echo "<li><a href='".get_permalink($post->ID)."'><span class='date-stamp'><em>".date('M',strtotime(get_post_meta($post->ID,'vacancy_closing_date',true)))."</em>".date('d',strtotime(get_post_meta($post->ID,'vacancy_closing_date',true)))."</span>".$thistitle;
					echo "<span></span>";
					if ( date('Ymd') == date('Ymd',strtotime(get_post_meta($post->ID,'vacancy_closing_date',true)))) echo "<span class='alert-vacancy' >Closing at ".date('H:i',strtotime($etime))."</span>";
					echo "</a></li>";
				}
			}
	
			if ($news->post_count!=0){
				if ($thumbnails!='on') echo "</ul></div>";
	
				$landingpage = get_option('options_module_vacancies_page'); 
				if ( !$landingpage ):
					$landingpage_link_text = 'vacancies';
					$landingpage = site_url().'/vacancies/';
				else:
					$landingpage_link_text = get_the_title( $landingpage[0] );
					$landingpage = get_permalink( $landingpage[0] );
				endif;
				
				echo '<hr><p><strong><a title="{$landingpage_link_text}" class="small" href="'.$landingpage.'">'.$landingpage_link_text.'</a></strong> <span class="dashicons dashicons-arrow-right-alt2"></span></p></div>';
				echo $after_widget;
			}
			set_transient($gatransient,$output,$cacheperiod); // set cache period 60 minutes default

		endif;
		echo $output;
		
		
		wp_reset_query();								
    }

    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['items'] = strip_tags($new_instance['items']);
		$instance['days'] = strip_tags($new_instance['days']);
		$instance['cacheperiod'] = strip_tags($new_instance['cacheperiod']);
       return $instance;
    }

    function form($instance) {
        $title = esc_attr($instance['title']);
        $items = esc_attr($instance['items']);
        $days = esc_attr($instance['days']);
        $cacheperiod = esc_attr($instance['cacheperiod']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /><br><br>

          <label for="<?php echo $this->get_field_id('items'); ?>"><?php _e('Number of items:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('items'); ?>" name="<?php echo $this->get_field_name('items'); ?>" type="text" value="<?php echo $items; ?>" /><br><br>

          <label for="<?php echo $this->get_field_id('days'); ?>"><?php _e('Days to look forward:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('days'); ?>" name="<?php echo $this->get_field_name('days'); ?>" type="text" value="<?php echo $days; ?>" /><br><br>
          <label for="<?php echo $this->get_field_id('cacheperiod'); ?>"><?php _e('Cache (minutes):'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('cacheperiod'); ?>" name="<?php echo $this->get_field_name('cacheperiod'); ?>" type="text" value="<?php echo $cacheperiod; ?>" /><br>

        </p>

        <?php 
    }

}


add_action('widgets_init', create_function('', 'return register_widget("htVacancyListing");'));

?>
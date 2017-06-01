<?php
/*
Plugin Name: HT About this page
Plugin URI: http://www.helpfultechnology.com
Description: Widget to display page information in the footer
Author: Luke Oatham
Version: 1.8.3
Author URI: http://www.helpfultechnology.com
*/

class htAboutThisPage extends WP_Widget {

	function __construct() {
		
		parent::__construct(
			'htAboutThisPage',
			__( 'HT About this page' , 'govintranet'),
			array( 'description' => __( 'Display page metadata' , 'govintranet') )
		);

	}

    function widget($args, $instance) {
		global $post;
	    wp_reset_postdata();
	    if ( !is_object($post) ) return true;
		$user = get_userdata($post->post_author);
		if ( !is_object($user) ) return;
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        $show_modified_date = ($instance['show_modified_date']);
        $show_published_date = ($instance['show_published_date']);
        $show_author = ($instance['show_author']);
		$showabout = true;
		if ( is_singular(array('forum','topic','reply'))) $showabout = false;
		if ( is_search() ) $showabout = false;
		if ( is_archive() ) $showabout = false;
		if ( is_404() ) $showabout = false;
		if ( is_front_page() ) $showabout = false;
		if ( is_home() ) $showabout = false;
		if ( is_page() ):
			$landing_pages = array(
			"page-about.php",
			"page-aggregator.php",
			"newsboard/page-newsboard.php",
			"page-blog.php",
			"page-doc-finder.php",
			"template-media-atoz.php",
			"page-event-past.php",
			"page-event.php",
			"page-forum-simple.php",
			"page-forum.php",
			"page-how-do-i-alt-classic.php",
			"page-how-do-i-alt.php",
			"page-how-do-i.php",
			"page-home.php",
			"page-jargon-buster.php",
			"page-news-multi.php",
			"page-news-updates.php",
			"page-news.php",
			"page-projects.php",
			"page-staff-directory-masonry.php",
			"page-vacancies.php",
			"search.php"
			);
			if ( in_array( get_page_template_slug($post->ID), $landing_pages ) ) $showabout = false;
		endif;

		if ($showabout) {
			$path = plugin_dir_url( __FILE__ );
	
			if (!wp_script_is('jquery', 'queue')){
				wp_enqueue_script('jquery');
			}
	        wp_enqueue_script( 'ht_about_this_page', $path.'js/ht_about_this_page.js' );
	        wp_enqueue_script( 'timeago', $path.'js/jquery.timeago.js' );

			echo $before_widget; 
			echo "<div id='about-this-widget'>";
			if ( $title ) echo $before_title . $title . $after_title; 
			
			$tzone = get_option('timezone_string');
			$date_format = get_option('date_format');
			date_default_timezone_set($tzone);

			if ($show_modified_date=='on'){
				$mod = date('Y-m-d',(get_post_modified_time())) . "T" . date('H:i:s',(get_post_modified_time()));
				echo __('Updated','govintranet') . ' <time class="timeago" datetime="'.$mod.'">'.date($date_format,(get_post_modified_time())).'</time><br>';
			}

			if ($show_published_date=='on'){
				$pub = get_the_date('Y-m-d',$post->ID) . "T" . date('H:i:s',strtotime(get_the_time())) ;
				echo __('Published','govintranet') . ' <time class="timeago" datetime="'.$pub.'">'.get_the_date($date_format,$post->ID).'</time><br>';
			}

			if ($show_author=='on'){
				global $post;
				$user = get_userdata($post->post_author);
				$displayname = get_user_meta($post->post_author ,'first_name',true )." ".get_user_meta($post->post_author ,'last_name',true );	
				$profile_url = gi_get_user_url($userid); 
				$authorlink = "<a href='" . $profile_url . "'>";
				echo $authorlink; 
				$directorystyle = get_option('options_staff_directory_style'); // 0 = squares, 1 = circles
				$avstyle = "";
				if ( $directorystyle==1 ) $avstyle = " img-circle";
				$image_url = get_avatar($post->post_author , 32, '', $displayname);
				if ( $avstyle ) $image_url = str_replace(" photo", " photo ".$avstyle, $image_url);
				echo $image_url;
				echo "</a>&nbsp;";
				echo "<a href='" . $profile_url . "'>";
				$auth = get_the_author();
				echo "<span class='listglyph'>".$auth."</span>";
				echo "</a> ";
			}

			echo "</div>";
			echo $after_widget; 
		}						

    }

    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['show_modified_date'] = strip_tags($new_instance['show_modified_date']);
		$instance['show_published_date'] = strip_tags($new_instance['show_published_date']);
		$instance['show_author'] = strip_tags($new_instance['show_author']);
       return $instance;
    }

    function form($instance) {
        $title = esc_attr($instance['title']);
        $show_modified_date = esc_attr($instance['show_modified_date']);
        $show_published_date = esc_attr($instance['show_published_date']);
        $show_author = esc_attr($instance['show_author']);
        ?>
		<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','govintranet'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		
		<label><?php _e('Include','govintranet');?>:</label><br>
		
		<input id="<?php echo $this->get_field_id('show_modified_date'); ?>" name="<?php echo $this->get_field_name('show_modified_date'); ?>" type="checkbox" <?php checked((bool) $instance['show_modified_date'], true ); ?> />
		<label for="<?php echo $this->get_field_id('show_modified_date'); ?>"><?php _e('Modified date','govintranet'); ?></label> <br>
		
		<input id="<?php echo $this->get_field_id('show_published_date'); ?>" name="<?php echo $this->get_field_name('show_published_date'); ?>" type="checkbox" <?php checked((bool) $instance['show_published_date'], true ); ?> />
		<label for="<?php echo $this->get_field_id('show_published_date'); ?>"><?php _e('Published date','govintranet'); ?></label> <br>
		
		<input id="<?php echo $this->get_field_id('show_author'); ?>" name="<?php echo $this->get_field_name('show_author'); ?>" type="checkbox" <?php checked((bool) $instance['show_author'], true ); ?> />
		<label for="<?php echo $this->get_field_id('show_author'); ?>"><?php _e('Author','govintranet'); ?></label> <br>
		
		</p>
        <?php 
    }

}

add_action('widgets_init', create_function('', 'return register_widget("htAboutThisPage");'));
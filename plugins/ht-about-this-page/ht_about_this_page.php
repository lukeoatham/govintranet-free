<?php
/*
Plugin Name: HT About this page
Plugin URI: http://www.helpfultechnology.com
Description: Widget to display page information in the footer
Author: Luke Oatham
Version: 0.1
Author URI: http://www.helpfultechnology.com
*/

class htAboutThisPage extends WP_Widget {
    function htAboutThisPage() {
        parent::WP_Widget(false, 'HT About this page', array('description' => 'Display page metadata in the footer'));
    }

    function widget($args, $instance) {
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        $show_modified_date = ($instance['show_modified_date']);
        $show_published_date = ($instance['show_published_date']);
        $show_author = ($instance['show_author']);
        $realtime = ($instance['realtime']);
	        
						$showabout = false;
						if ( is_single() && pods_url_variable(0) != 'forums' ) { 
						$showabout = true; }
						if ( is_page() && pods_url_variable(0) == 'about' ) { 
						$showabout = true; }
						if ( pods_url_variable(0) == 'about' && pods_url_variable(1) == 'forums' ) {
						$showabout = false; }
						if ( pods_url_variable(0) == 'forum' ) {
						$showabout = false; }
						if ( pods_url_variable(0) == 'topic' ) {
						$showabout = false; }
						if ( pods_url_variable(0) == 'reply' ) {
						$showabout = false; }
						if ( pods_url_variable(0) == 'about' && pods_url_variable(1) == 'who-we-are' ) {
						$showabout = true; }
						if ( pods_url_variable(0) == 'about' && pods_url_variable(1) == 'vacancies' && !pods_url_variable(2) ) {
						$showabout = false; }
						if ( pods_url_variable(0) == 'about' && pods_url_variable(1) == 'projects' && !pods_url_variable(2) ) {
						$showabout = false; }
							
						
						if ($showabout) {

							echo $before_widget; 
							if ( $title )
								echo $before_title . $title . $after_title; 
							$gis = "general_intranet_time_zone";
							$tzone = get_option($gis);
							date_default_timezone_set($tzone);

							if ($show_modified_date=='on'){
								if ($realtime=='on'){
									$tdate= get_the_modified_date(); 
									$ttime = get_the_modified_time();
									$sdate=$tdate." ".$ttime."";
									echo "Updated <time datetime='".$sdate."'>".$sdate."</time><br>";
								} else {
									$sdate = human_time_diff_plus(get_the_modified_time('U'));
									if ($sdate=="0 mins") {
										$sdate=" just now";
									} else {
										$sdate = $sdate." ago";
									}
	
									echo "Updated <time datetime='".$sdate."'>".$sdate."</time><br>";
								}
							}

							if ($show_published_date=='on'){
								if ($realtime=='on'){
									$tdate= get_the_date(); 
									$ttime = get_the_time();
									$sdate=$tdate." ".$ttime."";
									echo "Published <time datetime='".$sdate."'>".$sdate."</time><br>";
								} else {
									$sdate= date( "j M Y",strtotime(get_the_date() )); 
									$sdate = human_time_diff_plus(get_the_time('U'));
									if ($sdate=="0 mins") {
										$sdate=" just now";
									} else {
										$sdate = $sdate." ago";
									}
									echo "Published <time datetime='".$sdate."'>".$sdate."</time><br>";
									
								}
							}

							if ($show_author=='on'){
								$useremail = get_the_author_meta('user_email');
								echo "<a href='mailto:".$useremail."'>";
								the_author();
								echo "</a>";
							}
if ($realtime){
	echo "
	<script type=\"text/javascript\">  
	
	jQuery(document).ready(function () {
		jQuery('time').timediff();
	
	});  
	</script> 
	";
}
						echo $after_widget; 
						}						

    }

    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['show_modified_date'] = strip_tags($new_instance['show_modified_date']);
		$instance['show_published_date'] = strip_tags($new_instance['show_published_date']);
		$instance['show_author'] = strip_tags($new_instance['show_author']);
		$instance['realtime'] = strip_tags($new_instance['realtime']);

       return $instance;
    }

    function form($instance) {
        $title = esc_attr($instance['title']);
        $show_modified_date = esc_attr($instance['show_modified_date']);
        $show_published_date = esc_attr($instance['show_published_date']);
        $show_author = esc_attr($instance['show_author']);
        $realtime = esc_attr($instance['realtime']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />

          
          <label>Include:</label><br>

          <input id="<?php echo $this->get_field_id('show_modified_date'); ?>" name="<?php echo $this->get_field_name('show_modified_date'); ?>" type="checkbox" <?php checked((bool) $instance['show_modified_date'], true ); ?> />
          <label for="<?php echo $this->get_field_id('show_modified_date'); ?>"><?php _e('Modified date'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('show_published_date'); ?>" name="<?php echo $this->get_field_name('show_published_date'); ?>" type="checkbox" <?php checked((bool) $instance['show_published_date'], true ); ?> />
          <label for="<?php echo $this->get_field_id('show_published_date'); ?>"><?php _e('Published date'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('show_author'); ?>" name="<?php echo $this->get_field_name('show_author'); ?>" type="checkbox" <?php checked((bool) $instance['show_author'], true ); ?> />
          <label for="<?php echo $this->get_field_id('show_author'); ?>"><?php _e('Author'); ?></label> <br>

          <input id="<?php echo $this->get_field_id('realtime'); ?>" name="<?php echo $this->get_field_name('realtime'); ?>" type="checkbox" <?php checked((bool) $instance['show_author'], true ); ?> />
          <label for="<?php echo $this->get_field_id('realtime'); ?>"><?php _e('Realtime updates (required IE9+)'); ?></label> <br>


        </p>

        <?php 
    }

}

add_action('widgets_init', create_function('', 'return register_widget("htAboutThisPage");'));

?>
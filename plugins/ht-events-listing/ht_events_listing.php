<?php
/*
Plugin Name: HT Events listing
Plugin URI: https://help.govintra.net
Description: Display future events
Author: Luke Oatham
Version: 4.10.1
Author URI: https://www.agentodigital.com
*/



class htEventsListing extends WP_Widget {

	function __construct() {

		parent::__construct(
			'htEventsListing',
			__( 'HT Events listing' , 'govintranet'),
			array( 'description' => __( 'Events listing widget' , 'govintranet') )
		);

	}

	function widget($args, $instance) {
		extract( $args );
		$title = apply_filters('widget_title', $instance['title']);
		$items = intval($instance['items']);
		$cacheperiod = intval($instance['cacheperiod']);
		if ( isset($cacheperiod) && $cacheperiod ){ $cacheperiod = 60 * $cacheperiod; }
		$calendar = ($instance['calendar']);
		$thumbnails = ($instance['thumbnails']);
		$excerpt = ($instance['excerpt']);
		$location = ($instance['location']);
		$recent = ($instance['recent']);
		$output = '';
		$gatransient = $widget_id;
		$output = "";
		if ( $cacheperiod > 0 ) $output = get_transient( $gatransient );

		if ( empty( $output ) ){

			global $wpdb;

			$etypes = get_field('event_listing_event_types', 'widget_' . $widget_id);
			$eventtypes = $etypes;

			$textdate = get_field('event_listing_text_date_format', 'widget_' . $widget_id);
			if ( !$textdate ) $textdate = "g.ia D jS";

			//display forthcoming events
			$tzone = get_option('timezone_string');
			if ( $tzone ) date_default_timezone_set($tzone);
			$sdate = date('Ymd');
			$stime = date('H:i');

			$cquery = $wpdb->prepare("
			SELECT OI.ID, OI.post_name, OO1.meta_value AS event_start_date, OO2.meta_value AS event_start_time, OO3.meta_value AS event_end_date, OO4.meta_value AS event_end_time

			FROM $wpdb->posts OI
			    LEFT JOIN $wpdb->postmeta OO1 ON ( OO1.post_id = OI.ID AND OO1.meta_key='event_start_date' )
			    LEFT JOIN $wpdb->postmeta OO2 ON ( OO2.post_id = OI.ID AND OO2.meta_key='event_start_time' )
			    LEFT JOIN $wpdb->postmeta OO3 ON ( OO3.post_id = OI.ID AND OO3.meta_key='event_end_date' )
			    LEFT JOIN $wpdb->postmeta OO4 ON ( OO4.post_id = OI.ID AND OO4.meta_key='event_end_time' )
				WHERE OI.post_type = 'event' AND OI.post_status = 'publish' AND ( (OO3.meta_value > '%s') OR (OO3.meta_value = '%s' AND OO4.meta_value > '%s') )

			GROUP BY OI.ID, OI.post_name
			ORDER BY event_start_date ASC, event_start_time ASC
			",$sdate,$sdate,$stime);

			$allevents = $wpdb->get_results($cquery);

			// restrict to chosen team if available
			$events_to_show = array();
			$alreadydone = array();
			if ( count($allevents) ) foreach ($allevents as $a){
					if ( $eventtypes ){
						$eventterm = get_the_terms($a->ID, 'event-type');
						if ( $eventterm ) foreach ($eventterm as $e) {
								if ( in_array($e->term_id, $eventtypes) && !in_array($a->ID, $alreadydone)){
									$events_to_show[] = array('ID'=>$a->ID);
									$alreadydone[] = $a->ID;
								}
							}
					} else {
						$events_to_show[] = array('ID'=>$a->ID);
					}
				}

			$output.= "<div class='widget-area widget-events'><div class='upcoming-events'>";
			if ( count($events_to_show) != 0 ){
				$wtitle = "upcoming";
				$output.= $before_widget;
				if ( $title ) {
					$output.= $before_title . $title . $after_title;
				}
			} elseif ( 'on' == $recent) {
				$wtitle = "recent";
				$cquery = $wpdb->prepare("
				SELECT OI.ID, OI.post_name, OO1.meta_value AS event_start_date, OO2.meta_value AS event_start_time, OO3.meta_value AS event_end_date, OO4.meta_value AS event_end_time

				FROM $wpdb->posts OI
				    LEFT JOIN $wpdb->postmeta OO1 ON ( OO1.post_id = OI.ID AND OO1.meta_key='event_start_date' )
				    LEFT JOIN $wpdb->postmeta OO2 ON ( OO2.post_id = OI.ID AND OO2.meta_key='event_start_time' )
				    LEFT JOIN $wpdb->postmeta OO3 ON ( OO3.post_id = OI.ID AND OO3.meta_key='event_end_date' )
				    LEFT JOIN $wpdb->postmeta OO4 ON ( OO4.post_id = OI.ID AND OO4.meta_key='event_end_time' )
					WHERE OI.post_type = 'event' AND OI.post_status = 'publish' AND ( (OO3.meta_value < '%s') OR (OO3.meta_value = '%s' AND OO4.meta_value < '%s') )

				GROUP BY OI.ID, OI.post_name
				ORDER BY event_start_date DESC, event_start_time DESC
				",$sdate,$sdate,$stime);

				$allevents = $wpdb->get_results($cquery);

				// restrict to chosen event type if available
				$events_to_show = array();
				$alreadydone = array();
				if ( count($allevents) != 0 ) foreach ($allevents as $a){
						if ( $eventtypes ){
							$eventterm = get_the_terms($a->ID, 'event-type');
							if ( $eventterm ) foreach ($eventterm as $e) {
									if ( in_array($e->term_id, $eventtypes) && !in_array($a->ID, $alreadydone)){
										$events_to_show[] = array('ID'=>$a->ID);
										$alreadydone[] = $a->ID;
									}
								}
						} else {
							$events_to_show[] = array('ID'=>$a->ID);
						}
					}

				if ( count($events_to_show) == 0 ){
					if ( $cacheperiod ) {
						set_transient($gatransient,"<!-- Cached by GovIntranet at ".date('Y-m-d H:i:s')." -->",$cacheperiod); // set cache period 60 minutes default
					}
					return;
				}

				if ( count($events_to_show) != 0 ){
					$output.= $before_widget;
					if ( $title ) $output.= $before_title . $title . $after_title;
				}
			}

			$k=0;
			$alreadydone= array();

			if ( count($events_to_show) == 0 ){
				if ( $cacheperiod ) {
					set_transient($gatransient,"<!-- Cached by GovIntranet at ".date('Y-m-d H:i:s')." -->",$cacheperiod); // set cache period 60 minutes default
				}
				return;
			}

			foreach ($events_to_show as $event) {
				global $post;//required for access within widget
				if ( 'recent' == $wtitle ) $output.= "<div class='small past_events'><strong>" . __("Nothing coming up. Here's the most recent:","govintranet") . "</strong></div>";
				$wtitle = '';

				if (in_array($event['ID'], $alreadydone )) { //don't show if already in stickies
					continue;
				}
				$k++;
				if ($k > $items){
					break;
				}

				$thistitle = get_the_title($event['ID']);
				$edate = get_post_meta($event['ID'],'event_start_date',true);
				$etime = get_post_meta($event['ID'],'event_start_time',true);
				$edate = date($textdate,strtotime($edate." ".$etime));
				$thisURL = get_permalink($event['ID']);

				$output.= "<div class='row'><div class='col-sm-12'>";

				if ($thumbnails=='on'){
					$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $event['ID'] ), 'newsmedium' );
					if ($image_uri != "" ){
						$output.= "<a href='".$thisURL."'><img class='img img-responsive' src='{$image_uri[0]}' alt='".esc_attr($thistitle)."' /></a>";
					}
				}

				if ( 'on' == $calendar ) {

					$output.= "<div class='media eventslisting event-".$event['ID']."'>";
					$output.= "<div class='media-left alignleft'>";
					$output.= "<a class='calendarlink' href='".$thisURL."'>";
					$output.= "<div class='calbox'>";
					$output.= "<div class='cal-dow'>".date('D',strtotime(get_post_meta($event['ID'],'event_start_date',true)))."</div>";
					$output.= "<div class='caldate'>".date('d',strtotime(get_post_meta($event['ID'],'event_start_date',true)))."</div>";
					$output.= "<div class='calmonth'>".date('M',strtotime(get_post_meta($event['ID'],'event_start_date',true)))."</div>";
					$output.= "</div>";
					$output.= "</a>";
					$output.= "</div>";

					$output.= "<div class='media-body'>";
					$output.= "<p class='media-heading'>";
					$output.= "<a href='".$thisURL."'>";
					$output.= $thistitle;
					$output.= "</a>";
					$output.= "</p>";
					$output.= "<small><strong>".$edate."</strong></small>";
					if ( $location == 'on' && get_post_meta($event['ID'],'event_location',true) ) $output.= "<br><span><small>".get_post_meta($event['ID'],'event_location',true)."</small></span>";
					$output.= "</div></div>";

				} else {
					$output.= "<p><a href='{$thisURL}'> ".$thistitle."</a></p>";
					$output.= "<small><strong>".$edate."</strong></small>";
					if ( $location == 'on' && get_post_meta($event['ID'],'event_location',true) ) $output.= "<br><span><small>".get_post_meta($event['ID'],'event_location',true)."</small></span>";
				}

				if ( $excerpt == 'on' && get_the_excerpt($event['ID']) ){
					$output.= "<p class='eventclear'><span>".get_the_excerpt($event['ID'])."</span></p>";
				}

				$output.= "<hr class='light'></div>";
				$output.= "</div>";
			}

			if (count($events_to_show)!=0){

				$landingpage = get_option('options_module_events_page');
				if ( !$landingpage ):
					$landingpage_link_text = 'events';
				$landingpage = site_url().'/events/';
				else:
					$landingpage_link_text = get_the_title( $landingpage[0] );
				$landingpage = get_permalink( $landingpage[0] );
				endif;

				if ( is_array($eventtypes) && count($eventtypes) < 2 ):
					$term = intval($eventtypes[0]);
				$termobj = get_term($term, 'event-type', OBJECT);
				if ( $termobj ) {
					$landingpage_link_text = $termobj->name;
					$landingpage.="?cat=" . $termobj->slug;
				}
				$output.= '<p class="more-updates"><strong><a title="'.esc_attr($landingpage_link_text).'" class="small" href="'.$landingpage.'">'.esc_html($landingpage_link_text).'</a></strong> <span class="dashicons dashicons-arrow-right-alt2"></span></p>';
				else:
					$output.= '<p class="more-updates"><strong><a title="'.esc_attr($landingpage_link_text).'" class="small" href="'.$landingpage.'">'.esc_html($landingpage_link_text).'</a></strong> <span class="dashicons dashicons-arrow-right-alt2"></span></p>';
				endif;

				$output.= $after_widget;
			}
			$output.= "</div>";
			$output.= "</div>";
			if ( $cacheperiod ) {
				if (count($events_to_show)!=0){
					set_transient($gatransient,$output."<!-- Cached by GovIntranet at ".date('Y-m-d H:i:s')." -->",$cacheperiod);
				} else {
					set_transient($gatransient,"<!-- Cached by GovIntranet at ".date('Y-m-d H:i:s')." -->",$cacheperiod);
				}
			}

		}

		echo $output;

		wp_reset_query();

	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['items'] = strip_tags($new_instance['items']);
		$instance['cacheperiod'] = strip_tags($new_instance['cacheperiod']);
		$instance['calendar'] = strip_tags($new_instance['calendar']);
		$instance['thumbnails'] = strip_tags($new_instance['thumbnails']);
		$instance['excerpt'] = strip_tags($new_instance['excerpt']);
		$instance['location'] = strip_tags($new_instance['location']);
		$instance['recent'] = strip_tags($new_instance['recent']);
		return $instance;
	}

	function form($instance) {
		$title = esc_attr($instance['title']);
		$items = esc_attr($instance['items']);
		$cacheperiod = esc_attr($instance['cacheperiod']);
		$calendar = esc_attr($instance['calendar']);
		$thumbnails = esc_attr($instance['thumbnails']);
		$excerpt = esc_attr($instance['excerpt']);
		$location = esc_attr($instance['location']);
		$recent = esc_attr($instance['recent']);
?>
		<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','govintranet'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /><br><br>

		<label for="<?php echo $this->get_field_id('items'); ?>"><?php _e('Number of items:','govintranet'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('items'); ?>" name="<?php echo $this->get_field_name('items'); ?>" type="text" value="<?php echo $items; ?>" /><br><br>

		<input id="<?php echo $this->get_field_id('calendar'); ?>" name="<?php echo $this->get_field_name('calendar'); ?>" type="checkbox" <?php checked((bool) $instance['calendar'], true ); ?> />
		<label for="<?php echo $this->get_field_id('calendar'); ?>"><?php _e('Show calendar','govintranet'); ?></label> <br><br>

		<input id="<?php echo $this->get_field_id('thumbnails'); ?>" name="<?php echo $this->get_field_name('thumbnails'); ?>" type="checkbox" <?php checked((bool) $instance['thumbnails'], true ); ?> />
		<label for="<?php echo $this->get_field_id('thumbnails'); ?>"><?php _e('Show letterbox image','govintranet'); ?></label> <br><br>

		<input id="<?php echo $this->get_field_id('location'); ?>" name="<?php echo $this->get_field_name('location'); ?>" type="checkbox" <?php checked((bool) $instance['location'], true ); ?> />
		<label for="<?php echo $this->get_field_id('location'); ?>"><?php _e('Show location','govintranet'); ?></label> <br><br>

		<input id="<?php echo $this->get_field_id('excerpt'); ?>" name="<?php echo $this->get_field_name('excerpt'); ?>" type="checkbox" <?php checked((bool) $instance['excerpt'], true ); ?> />
		<label for="<?php echo $this->get_field_id('excerpt'); ?>"><?php _e('Show excerpt','govintranet'); ?></label> <br><br>

		<input id="<?php echo $this->get_field_id('recent'); ?>" name="<?php echo $this->get_field_name('recent'); ?>" type="checkbox" <?php checked((bool) $instance['recent'], true ); ?> />
		<label for="<?php echo $this->get_field_id('recent'); ?>"><?php _e('Show recent','govintranet'); ?></label> <br><br>
		<label for="<?php echo $this->get_field_id('cacheperiod'); ?>"><?php _e('Cache (minutes):','govintranet'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('cacheperiod'); ?>" name="<?php echo $this->get_field_name('cacheperiod'); ?>" type="text" value="<?php echo $cacheperiod; ?>" /><br>
		</p>

        <?php
	}

}


if( function_exists('acf_add_local_field_group') ):

	acf_add_local_field_group(array (
			'key' => 'group_55ee1a9ecbd0d',
			'title' => __('Limit event types','govintranet'),
			'fields' => array (
				array (
					'key' => 'field_56c7ba2023d97',
					'label' => 'Text date format',
					'name' => 'event_listing_text_date_format',
					'type' => 'text',
					'instructions' => 'See http://php.net/manual/en/function.date.php',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => 'g.ia D jS',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
					'readonly' => 0,
					'disabled' => 0,
				),
				array (
					'key' => 'field_55ee1ab48bb29',
					'label' => __('Event types','govintranet'),
					'name' => 'event_listing_event_types',
					'type' => 'taxonomy',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'taxonomy' => 'event-type',
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
						'value' => 'hteventslisting',
					),
				),
			),
			'menu_order' => 0,
			'position' => 'normal',
			'style' => 'default',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => '',
		));

endif;

function ht_events_head(){
	$head_back = get_option('header_background', '#0b2d49');
	$head_text = get_option('options_btn_text_colour','#ffffff');		
	$custom_css = "
	.calbox .cal-dow {
		background: ".$head_back.";
		color: ".$head_text.";
		font-size: 16px;
	}
	.calbox { 
		width: 3.5em; 
		border: 3px solid ".$head_back.";
		text-align: center;
		border-radius: 3px;
		background: #fff;
		box-shadow: 0 2px 3px rgba(0,0,0,.2);
	}
	.calbox .caldate {
		font-size: 25px;
		padding: 0;
		margin: 0;
		font-weight: 800;
	}
	.calbox .calmonth {
		color: ".$head_back.";
		text-transform: uppercase;
		font-weight: 800;
		font-size: 18px;
		line-height: 20px;
	}
    ";
	wp_enqueue_style( 'govintranet_event_styles', plugins_url("/ht-events-listing/ht_events_listing.css"));
	wp_add_inline_style('govintranet_event_styles' , $custom_css);

}

add_action('wp_head', 'ht_events_head', 4);
add_action('widgets_init', create_function('', 'return register_widget("htEventsListing");'));

?>
<?php
global $colid;
global $title;
$output = get_transient('aggregator_events_'.$colid.'_'.sanitize_title_for_query($title)); 
if ( $output == '' ):

	global $team;
	global $checkteam;
	global $num;
	global $showthumbnail;
	global $showcalendar;
	global $showlocation;

	$items = $num;
		
	//display forthcoming events
	$tzone = get_option('timezone_string');
	date_default_timezone_set($tzone);
	$sdate = date('Ymd');
	$stime = date('H:i');

	$cquery = array(
		'meta_query' => array(
			'relation' => 'OR',
			array(
				"relation"=>"AND",
		       array(
		           'key' => 'event_end_date',
		           'value' => $sdate,
		           'compare' => '>',
		       ),
				array(
				"key" => "related_team",
				"value" => '"'.$checkteam.'"',
				"compare" => "LIKE",
				),
		       ),
		       array(
			       'relation' => 'AND',
			       array(
			           'key' => 'event_end_date',
			           'value' => $sdate,
			           'compare' => '=',
			       ),
			       array(
			           'key' => 'event_end_time',
			           'value' => $stime,
			           'compare' => '>',
		           ),
					array(
					"key" => "related_team",
					"value" => '"'.$checkteam.'"',
					"compare" => "LIKE",
					),
		       ),
			),
	    'orderby' => 'meta_value',
	    'meta_key' => 'event_start_date',
	    'order' => 'ASC',
	    'post_type' => 'event',
		'posts_per_page' => $items,
		'fields' => "id",
		
	);

	$news =new WP_Query($cquery); 
		$output.= "<div class='widget-area widget-events'><div class='upcoming-events'>";
	
	if ($news->post_count!=0){
		$wtitle = "upcoming";
		$output.= "
		    <style>
			.calbox .cal-dow {
				background: ".get_theme_mod('header_background', '0b2d49').";
				color: #".get_header_textcolor().";
				font-size: 16px;
			}
			.calbox { 
				width: 3.5em; 
				border: 3px solid ".get_theme_mod('header_background', '0b2d49').";
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
				color: ".get_theme_mod('header_background', '0b2d49').";
				text-transform: uppercase;
				font-weight: 800;
				font-size: 18px;
				line-height: 20px;
			}
			a.calendarlink:hover { text-decoration: none; }
			a.calendarlink:hover .calbox .caldate { background: #eee; }
			a.calendarlink:hover .calbox .calmonth { background: #eee; }
			a.calendarlink:hover .calbox  { background: #eee; }
			.eventslisting h3 { border-top: 0 !important; padding-top: 0 !important; margin-top: 0 !important; }
			.eventslisting .alignleft { margin: 0 0 0.5em 0 !important; }
			.eventslisting p { margin-bottom: 0 !important; }
		    </style>
		    ";	

	}
	$k=0;
	while ($news->have_posts()) {

		$wtitle = '';
		$news->the_post();

		$k++; 
		if ($k > $items && $items != -1){
			break;
		}
				$thistitle = get_the_title($post->ID);
				$edate = get_post_meta($post->ID,'event_start_date',true);
				$etime = get_post_meta($post->ID,'event_start_time',true);
				$edate = date($textdate,strtotime($edate." ".$etime));
				$thisURL = get_permalink($post->ID); 
				
				$output.= "<div class='row'><div class='col-sm-12'>";
				
				if ($showthumbnail=='on'){
					$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'newsmedium' ); 
					if ($image_uri != "" ){
						$output.= "<a href='".$thisURL."'><img class='img img-responsive' src='{$image_uri[0]}' alt='".$thistitle."' /></a>";		
					}
				} 
	
				if ( 'on' == $showcalendar ) {

					$output.= "<div class='media eventslisting'>";
					$output.= "<div class='media-left alignleft'>";
					$output.= "<a class='calendarlink' href='".$thisURL."'>";
					$output.= "<div class='calbox'>";
					$output.= "<div class='cal-dow'>".date('D',strtotime(get_post_meta($post->ID,'event_start_date',true)))."</div>";
					$output.= "<div class='caldate'>".date('d',strtotime(get_post_meta($post->ID,'event_start_date',true)))."</div>";
					$output.= "<div class='calmonth'>".date('M',strtotime(get_post_meta($post->ID,'event_start_date',true)))."</div>";
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
					if ( $location == 'on' && get_post_meta($post->ID,'event_location',true) ) $output.= "<br><span><small>".get_post_meta($post->ID,'event_location',true)."</small></span>";
					$output.= "</div></div>";
					

				} else {
					$output.= "<p><a href='{$thisURL}'> ".$thistitle."</a></p>";
					$output.= "<small><strong>".$edate."</strong></small>";
					if ( $showlocation == 'on' && get_post_meta($post->ID,'event_location',true) ) $output.= "<br><span><small>".get_post_meta($post->ID,'event_location',true)."</small></span>";
				} 
	
				if ( get_the_excerpt() ){
						$output.= "<p class='eventclear'><span>".get_the_excerpt()."</span></p>";
				}
	
				$output.= "</div>";
				$output.= "</div><hr>";
				
	}

	if ($news->post_count!=0){

		$landingpage = get_option('options_module_events_page'); 
		if ( !$landingpage ):
			$landingpage_link_text = 'events';
			$landingpage = site_url().'/events/';
		else:
			$landingpage_link_text = get_the_title( $landingpage[0] );
			$landingpage = get_permalink( $landingpage[0] );
		endif;

		$output.= '<p class="events-more"><strong><a title="'.$landingpage_link_text.'" class="small" href="'.$landingpage.'">'.$landingpage_link_text.'</a></strong> <span class="dashicons dashicons-arrow-right-alt2"></span></p>';
		$output.= $after_widget;
	}
	$output.= "</div>";
	$output.= "</div>";

 	set_transient('aggregator_events_'.$colid.'_'.sanitize_title_for_query($title),$output,5*60); // set cache period 5 minutes 

endif;


if ( $output ):
	echo "<div class='widget-box'>";
	if ( $title ) echo "<h3>".esc_attr($title)."</h3>";
	echo $output;
	echo "</div>";
endif;

wp_reset_postdata();
?>			
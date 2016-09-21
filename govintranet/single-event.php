<?php
/**
 * The Template for displaying all single event posts.
 *
 * @package WordPress
 */

?>
<?php wp_enqueue_script('ouical', get_stylesheet_directory_uri()  . '/add-to-calendar/ouical.js'); ?>
<?php wp_enqueue_style('ouical', get_stylesheet_directory_uri()  . '/add-to-calendar/main.css'); ?>
<?php get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); 
	
$mainid=$post->ID;
?>

<div class="col-lg-7 col-md-8 col-sm-7 col-xs-12 white ">
	<div class="row">
		<div class='breadcrumbs'>
			<?php if(function_exists('bcn_display') && !is_front_page()) {
				bcn_display();
				}?>
		</div>
	</div>

	<h1><?php the_title(); ?></h1>
    
	<?php 
	$sm_dates = get_post_meta($post->ID,'event_end_date',TRUE);
	$addToCalendar = '';	
	if ($sm_dates >= date('Ymd')) {
		$addToCalendar = " <div class='new-cal'></div>";
	}
  
	echo '<h3>' . __('Date' , 'govintranet') . '</h3>';
	echo "<p>".date(get_option('date_format'),strtotime(get_post_meta($post->ID,'event_start_date',true)));
	if (get_post_meta($post->ID,'event_start_time',true)):
		echo " <i class='dashicons dashicons-clock'></i> ".date(get_option('time_format'),strtotime(get_post_meta($post->ID,'event_start_time',true))). " - ";
	else:
		echo "&nbsp;";
	endif;
	if (date(get_option('date_format'),strtotime(get_post_meta($post->ID,'event_start_date',true)))==date(get_option('date_format'),strtotime(get_post_meta($post->ID,'event_end_date',true))) ) {
		if (get_post_meta($post->ID,'event_end_time',true)):
			echo date(get_option('time_format'),strtotime(get_post_meta($post->ID,'event_end_time',true)));
		endif;
	} else {
		echo date(get_option('date_format'),strtotime(get_post_meta($post->ID,'event_end_date',true)));	
		if (get_post_meta($post->ID,'event_end_time',true)):
			echo " <i class='dashicons dashicons-clock'></i> ".date(get_option('time_format'),strtotime(get_post_meta($post->ID,'event_end_time',true)));	
		endif;
	}
	
	echo " " . $addToCalendar . "</p>";

	$locationname = get_post_meta($post->ID,'event_location',true);
	if ( $locationname ):
		echo "<h3>" . __('Location' , 'govintranet') . "</h3>";
		echo wpautop(esc_attr($locationname));
	endif;
	
	$userrec = wp_get_current_user();
	$userid = $userrec->ID;
	$formfields = get_post_meta($post->ID, 'event_gravity_forms_id', true ); 
	$alreadybooked='';
	if ($formfields){
		$formid = $formfields;
		$formtitle = __('Event booking','govintranet');
		global $wpdb;
		$q = $wpdb->prepare("select distinct $wpdb->rg_lead.id from $wpdb->rg_lead join $wpdb->rg_lead_detail on $wpdb->rg_lead_detail.form_id = $wpdb->rg_lead.form_id where $wpdb->rg_lead.form_id = ".$formid." and created_by = ".$userid." and $wpdb->rg_lead_detail.field_number = 1 and $wpdb->rg_lead_detail.value = %d and $wpdb->rg_lead.status = 'active'",$post->ID); 
		$alreadybooked = $wpdb->get_results($q,"ARRAY_A");
	} 
	
	if ($alreadybooked) echo "<strong>* " . __('You have made a booking for this event') . " *</strong>";
	echo '<h3>' . __('Details' , 'govintranet') . '</h3>';
	the_content(); 
	$sdate= date('Ymd');
	
	//booking form ***************
	//only display if future event and not already booked
	if (date('Ymd',strtotime(get_post_meta($post->ID,'event_start_date',true))) >= $sdate && !$alreadybooked && get_post_meta($post->ID, 'event_gravity_forms_id', true) ) {
		gravity_form($formtitle, $display_title=true, $display_description=true, $display_inactive=false, $field_values=$params, $ajax=false, $formid);
	}

	$sdate = date('Ymd');
	$ticketid = get_post_meta($post->ID,'eventbrite_ticket',true);
	if ($ticketid && $sdate <= date('Ymd',strtotime(get_post_meta($post->ID,'event_end_date',true))) ) : ?>
		<h3><?php _e('Tickets and registration' , 'govintranet') ;?></h3>
		<div style="width:100%; text-align:left;" >
			<iframe src="https://www.eventbrite.com/tickets-external?eid=<?php echo $ticketid; ?>" frameborder="0" height="256" width="100%" vspace="0" hspace="0" marginheight="5" marginwidth="5" scrolling="auto" allowtransparency="true"></iframe>
		</div>
	<?php 
	endif;
	?>
	<?php get_template_part("part", "downloads"); ?>			
	<?php
	if ('open' == $post->comment_status) {
		 comments_template( '', true ); 
	}
	?>

</div>

<div class="col-lg-4 col-lg-offset-1 col-md-4 col-sm-5 col-xs-12">
	<?php
	$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'newshead' );
	if ($image_uri!=""){
		echo "<img class='img img-responsive' src='{$image_uri[0]}'  alt='".get_the_title()."' />";
		echo wpautop( "<p class='news_date'>".get_post_thumbnail_caption()."</p>" );
	}
	
	$map= get_field('event_map_location');
	$text = esc_attr(get_post_meta($post->ID,'event_location',true));
	
	if (isset($map['lat'])):
		$loc = ($map['lat'].",".$map['lng']);
		$text = $map['address'];
		?>	
		<div class="widget-box">
			<h3><?php _e('Location' , 'govintranet') ;?></h3>
			<div class="acf-map google_map" id="map-canvas">
			<script src="//maps.googleapis.com/maps/api/js?key=<?php echo get_option('options_google_api_key', ''); ?>"></script>
			<script>
				var map;
				function initialize() {
					var mapOptions = {
						zoom: 15,
						center: new google.maps.LatLng(<?php echo $loc; ?>),
						mapTypeId: google.maps.MapTypeId.ROADMAP
					};
					map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
					
					var marker = new google.maps.Marker({
						position: new google.maps.LatLng(<?php echo $loc; ?>),
						map: map,
						title: '<?php the_title(); ?>',
						animation: google.maps.Animation.DROP
					});
				}
	
				google.maps.event.addDomListener(window, 'load', initialize);
			</script>
			</div>
			<div class="alert alert-info" id="map-location"><?php echo wpautop($text); ?></div>
		</div>
	<?php endif; 
	
	get_template_part("part", "sidebar");

 	dynamic_sidebar('events-widget-area'); 

	get_template_part("part", "related");
 	
	$post_cat = get_the_terms($post->ID, 'event-type' ); 
	if ($post_cat){
		$html='';
		foreach($post_cat as $cat){
			if ($cat->term_id > 1){
				$html.= "<span><a class='wptag t".$cat->term_id."' href='".site_url()."/events/?cat=".$cat->slug."'>".str_replace(" ","&nbsp;",$cat->name)."</a></span> ";
			}
		}	
		if ($html){
			echo "<div class='widget-box'><h3>" . __('Categories' , 'govintranet') . "</h3>".$html."</div>";
		}
	}
	$posttags = get_the_tags();
	if ($posttags) {
		$foundtags=false;	
		$tagstr="";
	  	foreach($posttags as $tag) { 
	  			$foundtags=true;
	  			$tagurl = $tag->term_id;
		    	$tagstr=$tagstr."<span><a class='label label-default' href='".get_tag_link($tagurl) . "?type=event'>" . str_replace(' ', '&nbsp' , $tag->name) . '</a></span> '; 
	  	}
	  	if ($foundtags){
		  	echo "<div class='widget-box'><h3>" . __('Tags' , 'govintranet') . "</h3><p> "; 
		  	echo $tagstr;
		  	echo "</p></div>";
	  	}
	}


 	
 	wp_reset_postdata();
	?>
</div> <!--end of second column-->

<?php 
function escapeString($string) {
return preg_replace('/([\,\'\";])/','\\\$1', $string);
}
$ex = escapeString(get_the_excerpt());
if ( !$ex ) $ex = substr( strip_tags( $post->post_content ), 0 ); 
if ($sm_dates >= date('Ymd')):
?>
  <script>
      var myCalendar = createCalendar({
        options: {
          class: 'addto-calendar-link',
          id: 'add-to-calendar',                               // You need to pass an ID. If you don't, one will be generated for you.
        },
        data: {
          title: '<?php echo escapeString(get_the_title()); ?>',
          start: new Date('<?php echo date('F d, Y ',strtotime(get_post_meta($post->ID,'event_start_date',TRUE))); echo date('H:i',strtotime(get_post_meta($post->ID,'event_start_time',TRUE))); ?>'),
          end: new Date('<?php echo date('F d, Y ',strtotime(get_post_meta($post->ID,'event_end_date',TRUE))); echo date('H:i',strtotime(get_post_meta($post->ID,'event_end_time',TRUE))); ?>'),
          address: '<?php echo escapeString(get_post_meta($post->ID,'event_location',TRUE)); ?>',
          description: '<?php echo $ex; ?>',
        }
      });

      document.querySelector('.new-cal').appendChild(myCalendar);

  </script>
			
<?php endif; ?>
			
<?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>
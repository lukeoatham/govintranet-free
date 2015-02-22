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
	$sm_dates = get_post_meta($post->ID,'event_start_date',TRUE);
	$addToCalendar = '';	
	if ($sm_dates >= date('Ymd')) {
		$addToCalendar = " <div class='new-cal'></div>";
	}
  
	echo '<h3>Date</h3>';
	echo "<p>".date('l j M Y ',strtotime(get_post_meta($post->ID,'event_start_date',true)));
	if (get_post_meta($post->ID,'event_start_time',true)):
		echo "<i class='dashicons dashicons-clock'></i> ".date('g:ia',strtotime(get_post_meta($post->ID,'event_start_time',true))). " - ";
	endif;
	if (date('j M Y',strtotime(get_post_meta($post->ID,'event_start_date',true)))==date('j M Y',strtotime(get_post_meta($post->ID,'event_end_date',true))) ) {
		if (get_post_meta($post->ID,'event_end_time',true)):
			echo date('g:ia',strtotime(get_post_meta($post->ID,'event_end_time',true)));
		endif;
	} else {
		echo date('l j M Y, ',strtotime(get_post_meta($post->ID,'event_end_date',true)));	
		if (get_post_meta($post->ID,'event_end_time',true)):
			echo "<i class='dashicons dashicons-clock'></i> ".date('g:ia',strtotime(get_post_meta($post->ID,'event_end_time',true)));	
		endif;
	}
	
	echo " " . $addToCalendar . "</p>";
	
	$userrec = wp_get_current_user();
	$userid = $userrec->ID;
	$formfields = get_post_meta($post->ID, 'event_gravity_forms_id', true ); 
	$alreadybooked='';
	if ($formfields){
		$formid = $formfields;
		$formtitle = 'Event booking';
		global $wpdb;
		$q = $wpdb->prepare("select distinct $wpdb->rg_lead.id from $wpdb->rg_lead join $wpdb->rg_lead_detail on $wpdb->rg_lead_detail.form_id = $wpdb->rg_lead.form_id where $wpdb->rg_lead.form_id = ".$formid." and created_by = ".$userid." and $wpdb->rg_lead_detail.field_number = 1 and $wpdb->rg_lead_detail.value = %d and $wpdb->rg_lead.status = 'active'",$post->ID); 
		$alreadybooked = $wpdb->get_results($q,"ARRAY_A");
	} 
	
	if ($alreadybooked) echo "<strong>* You have made a booking for this event *</strong>";
	echo '<h3>Details</h3>';
	the_content(); 
	$sdate= date('Y-m-d');
	
	//booking form ***************
	//only display if future event and not already booked
	if (date('Y-m-d',strtotime(get_post_meta($post->ID,'event_start_date',true))) > $sdate && !$alreadybooked && get_post_meta($post->ID, 'event_gravity_forms_id', true) ) {
		gravity_form($formtitle, $display_title=true, $display_description=true, $display_inactive=false, $field_values=$params, $ajax=false, $formid);
	}

	$current_attachments = get_field('document_attachments');
	if ($current_attachments){
		echo "<div class='alert alert-info'>";
		echo "<h3>Downloads <i class='glyphicon glyphicon-download'></i></h3>";
		foreach ($current_attachments as $ca){
			$c = $ca['document_attachment'];
			echo "<p><a class='alert-link' href='".$c['url']."'>".$c['title']."</a></p>";
		}
		echo "</div>";
	}				
	
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

	$sdate = date('Y-m-d');
	$ticketid = get_post_meta($post->ID,'eventbrite_ticket',true);
	if ($ticketid && $sdate < date('Y-m-d',strtotime(get_post_meta($post->ID,'event_start_date',true))) ) : ?>
		<div class="widget-box">
			<h3>Tickets and registration</h3>
			<div style="width:100%; text-align:left;" >
				<iframe src="https://www.eventbrite.com/tickets-external?eid=<?php echo $ticketid; ?>" frameborder="0" height="256" width="100%" vspace="0" hspace="0" marginheight="5" marginwidth="5" scrolling="auto" allowtransparency="true"></iframe>
			</div>
		</div>
	<?php 
	endif;
	
	$map = get_post_meta($post->ID,'event_map_location',true);
	$text = esc_attr(get_post_meta($post->ID,'event_location',true));

	if ( isset( $text ) ): 
		echo'
		<div class="widget-box">
		<h3>Location</h3>
		';
	endif;

	if ($map['lat']):
		$loc = ($map['lat'].",".$map['lng']);
		?>	
		<script src="//maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
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
		<div id="map-canvas" class="google_map"></div>
	<?php 
	endif; 
		
	if ( isset( $text ) ): 
		echo wpautop($text); 
		echo "</div>";
	endif;
	
	$related = get_post_meta($id,'related',true);

	if ($related){
		$html='';
		foreach ($related as $r){ 
			$title_context="";
			$rlink = get_post($r);
			if ($rlink->post_status == 'publish' && $rlink->ID != $id ) {
				$taskparent=$rlink->post_parent; 
				if ($taskparent){
					$tparent_guide_id = $taskparent->ID; 		
					$taskparent = get_post($tparent_guide_id);
					$title_context=" (".govintranetpress_custom_title($taskparent->post_title).")";
				}		
				$html.= "<li><a href='".get_permalink($rlink->ID)."'>".govintranetpress_custom_title($rlink->post_title).$title_context."</a></li>";
			}
		}
	}
	
	//get anything related to this post
	$otherrelated = get_posts(array('post_type'=>array('task','news','project','vacancy','blog','team','event'),'posts_per_page'=>-1,'exclude'=>$related,'meta_query'=>array(array('key'=>'related','compare'=>'LIKE','value'=>'"'.$id.'"')))); 
	foreach ($otherrelated as $o){
		if ($o->post_status == 'publish' && $o->ID != $id ) {
					$taskparent=$o->post_parent; 
					$title_context='';
					if ($taskparent){
						$taskparent = get_post($taskparent);
						$title_context=" (".govintranetpress_custom_title($taskparent->post_title).")";
					}		
					$html.= "<li><a href='".get_permalink($o->ID)."'>".govintranetpress_custom_title($o->post_title).$title_context."</a></li>";
			}
	}

	if ($related || $otherrelated){
		echo "<div class='widget-box list'>";
		echo "<h3 class='widget-title'>Related</h3>";
		echo "<ul>";
		echo $html;
		echo "</ul></div>";
	}
	$post_cat = get_the_terms($post->ID, 'event-type' ); 
	if ($post_cat){
		$html='';
		foreach($post_cat as $cat){
			if ($cat->slug != 'uncategorized'){
				$html.= "<span><a class='wptag t".$cat->term_id."' href='".site_url()."/events/?cat=".$cat->slug."'>".str_replace(" ","&nbsp;",$cat->name)."</a></span> ";
			}
		}	
		if ($html){
			echo "<div class='widget-box'><h3>Categories</h3>".$html."</div>";
		}
	}
	$posttags = get_the_tags();
	if ($posttags) {
		$foundtags=false;	
		$tagstr="";
	  	foreach($posttags as $tag) { 
	  			$foundtags=true;
	  			$tagurl = $tag->slug;
		    	$tagstr=$tagstr."<span><a class='label label-default' href='".site_url()."/tag/{$tagurl}/?type=event'>" . str_replace(' ', '&nbsp' , $tag->name) . '</a></span> '; 
	  	}
	  	if ($foundtags){
		  	echo "<div class='widget-box'><h3>Tags</h3><p> "; 
		  	echo $tagstr;
		  	echo "</p></div>";
	  	}
	}

 	dynamic_sidebar('events-widget-area'); 
 	
 	wp_reset_postdata();
	?>
</div> <!--end of second column-->

<?php 
function escapeString($string) {
return preg_replace('/([\,\'\";])/','\\\$1', $string);
}
$ex = escapeString(get_the_excerpt());
if ( !$ex ) $ex = substr( strip_tags( $post->post_content ), 0 ); 
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
			
<?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>
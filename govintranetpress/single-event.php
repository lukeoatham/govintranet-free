<?php
/**
 * The Template for displaying all single event posts.
 *
 * @package WordPress
 */

get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); 
	
	$mainid=$post->ID;
?>

	<div class="col-lg-8 col-md-8 white ">
		<div class="row">
			<div class='breadcrumbs'>
				<?php if(function_exists('bcn_display') && !is_front_page()) {
					bcn_display();
					}?>
			</div>
		</div>

	<h1><?php the_title(); ?></h1>

	<?php 
 
	echo '<h3>Date</h3>';
	echo "<p>".date('l j M Y, ',strtotime(get_post_meta($post->ID,'event_start_date',true)));
	echo "<i class='glyphicon glyphicon-time'></i> ".date('g:ia',strtotime(get_post_meta($post->ID,'event_start_date',true))). " - ";
	if (date('j M Y',strtotime(get_post_meta($post->ID,'event_start_date',true)))==date('j M Y',strtotime(get_post_meta($post->ID,'event_end_date',true)))) {
		echo date('g:ia',strtotime(get_post_meta($post->ID,'event_end_date',true)));
	} else {
		echo date('l j M Y, ',strtotime(get_post_meta($post->ID,'event_end_date',true)));	
		echo "<i class='glyphicon glyphicon-time'></i> ".date('g:ia',strtotime(get_post_meta($post->ID,'event_end_date',true)));	
	}
	
	echo "</p>";
	
	$userrec = wp_get_current_user();
	$userid = $userrec->ID;
	$formfields = get_post_meta($post->ID, 'event_booking_form_id', true ); 
	if ($formfields){
		$formid = $formfields['id'];
		$formtitle = $formfields['title'];
		$formactive = $formfields['is_active']; 
		$q = "select distinct wp_rg_lead.id from wp_rg_lead join wp_rg_lead_detail on wp_rg_lead_detail.form_id = wp_rg_lead.form_id where wp_rg_lead.form_id = ".$formid." and created_by = ".$userid." and wp_rg_lead_detail.field_number = 1 and wp_rg_lead_detail.value = ".$post->ID." and wp_rg_lead.status = 'active'";
		$alreadybooked = $wpdb->get_results($q,"ARRAY_A");
	}
		if ($alreadybooked)echo "<strong>* You have made a booking for this event *</strong>";
			echo '<h3>Details</h3>';
			the_content(); 
			$tdate= getdate();
			$tdate = $tdate['year']."-".$tdate['mon']."-".$tdate['mday'];
			$tday = date( 'd' , strtotime($tdate) );
			$tmonth = date( 'm' , strtotime($tdate) );
			$tyear= date( 'Y' , strtotime($tdate) );
			$sdate=$tyear."-".$tmonth."-".$tday." 00:00";
		
		//booking form ***************
		//only display if future event and not already booked
		if (date('Y-m-d',strtotime(get_post_meta($post->ID,'event_start_date',true))) > $sdate && !$alreadybooked && get_post_meta($post->ID, 'event_booking_form_id', true) ) {
			gravity_form($formtitle, $display_title=true, $display_description=true, $display_inactive=false, $field_values=$params, $ajax=false, $formid);
		}

		?>

		</div>
			<div class="col-lg-4 col-md-4">	

				<?php
				$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' );
				if ($image_uri!=""){
					echo "<img class='img img-responsive pull-right' src='{$image_uri[0]}'  alt='".get_the_title()."' />";
					echo wpautop( "<p class='news_date'>".get_post_thumbnail_caption()."</p>" );
				}

				$post_cat = get_the_terms($post->ID, 'event_type' ); 
				if ($post_cat){
					$html='';
					foreach($post_cat as $cat){
						if ($cat->slug != 'uncategorized'){
							$html.= "<span class='wptag t".$cat->term_id."'><a href='".site_url()."/events/?cat=".$cat->slug."'>".str_replace(" ","&nbsp;",$cat->name)."</a></span> ";
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
					    	$tagstr=$tagstr."<span><a class='label label-default' href='".site_url()."/tagged/?tag={$tagurl}&amp;posttype=event'>" . str_replace(' ', '&nbsp' , $tag->name) . '</a></span> '; 
				  	}
				  	if ($foundtags){
					  	echo "<div class='widget-box'><h3>Tags</h3><p> "; 
					  	echo $tagstr;
					  	echo "</p></div>";
				  	}
				}

				$tdate= getdate();
				$tdate = $tdate['year']."-".$tdate['mon']."-".$tdate['mday'];
				$tday = date( 'd' , strtotime($tdate) );
				$tmonth = date( 'm' , strtotime($tdate) );
				$tyear= date( 'Y' , strtotime($tdate) );
				$sdate=$tyear."-".$tmonth."-".$tday;	
				$ticketid = get_post_meta($post->ID,'eventbrite_ticket',true);
				if ($ticketid && $sdate < date('Y-M-d',strtotime(get_post_meta($post->ID,'event_start_date',true))) ) : ?>
					<div class="widget">
						<h3>Tickets and registration</h3>
						<div style="width:100%; text-align:left;" >
							<iframe src="https://www.eventbrite.com/tickets-external?eid=<?php echo $ticketid; ?>" frameborder="0" height="256" width="100%" vspace="0" hspace="0" marginheight="5" marginwidth="5" scrolling="auto" allowtransparency="true"></iframe>
						</div>
					</div>
			<?php endif; ?>					
<?php				
		 	dynamic_sidebar('events-widget-area'); 
				?>
		</div> <!--end of second column-->
	</div> 
			
<?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>
<?php
/**
 * The Template for displaying all single event posts.
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */

get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); 
	
	$mainid=$post->ID;
?>
	<div class="row">
		<div class="eightcol white last" id='content'>
			<div class="row">
				<div class='breadcrumbs'>
				<?php if(function_exists('bcn_display') && !is_front_page()) {
							bcn_display();
						}?>
				</div>
			</div>
			<div class="content-wrapper">
<?php
							$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' );
				if ($image_uri!=""){
					echo "<img class='alignright' src='{$image_uri[0]}' width='{$image_uri[1]}' height='{$image_uri[2]}' alt='".get_the_title()."' />";
					echo wpautop( "<p class='news_date'>".get_post_thumbnail_caption()."</p>" );
				}
?>

					<h1><?php the_title(); ?></h1>
					
					<?php 
 
// display repeater fields for dates
 
	echo '<h3>Date</h3>';
		echo "<p>".date('j M Y, H:i',strtotime(get_post_meta($post->ID,'event_start_date',true))). " - " . date('H:i',strtotime(get_post_meta($post->ID,'event_end_time',true)))."</p>";
 $userrec = wp_get_current_user();
$userid = $userrec->ID;
$q = "select distinct wp_rg_lead.id from wp_rg_lead join wp_rg_lead_detail on wp_rg_lead_detail.form_id = wp_rg_lead.form_id where wp_rg_lead.form_id = 4 and created_by = ".$userid." and wp_rg_lead_detail.field_number = 4 and value = ".$post->ID;
$alreadybooked = $wpdb->get_results($q,"ARRAY_A");
if ($alreadybooked)echo "<strong>* You have made a booking for this event *</strong>";

?>
	<?php
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
if (date('Y-m-d',strtotime(get_post_meta($post->ID,'event_start_date',true))) > $sdate && !$alreadybooked ) {
	gravity_form('Event booking', $display_title=true, $display_description=true, $display_inactive=false, $field_values=$params, $ajax=false, 4);
}
	?>
				</div>
		</div>
				<div class="fourcol last">	
				
				
			<?php

				$post_cat = get_the_category();
				if ($post_cat){
					$html='';
					foreach($post_cat as $cat){
					if ($cat->slug != 'uncategorized'){
						if (!$catTitlePrinted){
							$catTitlePrinted = true;
						}
						$html.= "<span class='wptag t".$cat->term_id."'><a href='/event-by-category/?cat=".$cat->slug."'>".str_replace(" ","&nbsp;",$cat->name)."</a></span> ";
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
				  		if (substr($tag->name,0,9)!="carousel:"){
				  			$foundtags=true;
				  			$tagurl = $tag->slug;
					    	$tagstr=$tagstr."<span class='wptag'><a href='/tagged/?tag={$tagurl}&amp;posttype=event'>" . str_replace(' ', '&nbsp;' , $tag->name) . '</a></span> '; 
				    	}
				  	}
				  	if ($foundtags){
					  	echo "<div class='widget-box'><h3>Tags</h3> "; 
					  	echo $tagstr;
					  	echo "</div>";
				  	}

				}
		 	dynamic_sidebar('news-widget-area'); 
				?>
		</div> <!--end of second column-->
	</div> 
			
<?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>
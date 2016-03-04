<?php
/**
 * The Template for displaying all single vacancy posts.
 *
 * @package WordPress
 */

get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

<?php
$chapterheader = false;
$singletask = false;
$pagetype = "";
$current_vac = $id;
?>
	
		<div class="col-lg-8 col-md-7 col-sm-7 white ">
			<div class="row">
				<div class='breadcrumbs'>
					<?php if(function_exists('bcn_display') && !is_front_page()) {
						bcn_display();
						}?>
				</div>
			</div>
			
			<h1><?php the_title();?></h1>
			<div class='well'>
			<h3><?php _e('Job details' , 'govintranet') ; ?></h3> 
			<?php

			$job_reference = get_post_meta($current_vac, 'vacancy_reference', true);

			$team = get_post_meta($current_vac, 'vacancy_team', true);
			if ($team) {
				$teamtemp = array();
				foreach ((array)$team as $t){
					$teamtemp[] = get_the_title($t);
				}
				$team = implode(", " , $teamtemp);
			}

			$grade = wp_get_post_terms( $post->ID, 'grade' );
			if ( $grade ) $grade = $grade[0]->name;

			$closing_date = get_post_meta($current_vac, 'vacancy_closing_date', true);
			$closing_time = get_post_meta($current_vac, 'vacancy_closing_time', true);
			if ($closing_date) $closing_date = date(get_option('date_format'), strtotime($closing_date))." ".date(get_option('time_format'), strtotime($closing_time));

			$projects = get_post_meta($current_vac, 'vacancy_project', true);

			$current_attachments = get_post_meta($current_vac, 'document_attachments', true);
			
			if ( $job_reference ) echo "<strong>" . __('Job reference' , 'govintranet') . ": </strong>".esc_attr($job_reference)."<br>";
			if ( $team ) echo "<strong>" . __('Team' , 'govintranet') . ": </strong>".$team."<br>";
			if ( $grade ) echo "<strong>" . __('Grade' , 'govintranet') . ": </strong>".$grade."<br>";			
			if ( $closing_date ) echo "<strong>" . __('Closing date' , 'govintranet') . ": </strong>".$closing_date;

			$sdate=date('Y-m-d');;
			if ( date('l j F, Y', strtotime($closing_date)) == date('l j F, Y', strtotime($sdate ) ) ){
				echo " (" . __("That's today!" , "govintranet") . ")";
			}		

			echo "</div>";
			the_content(); 		
						
			if ($projects){
				echo "<div id='projects'><h2>" . _x('Project' , 'noun' , 'govintranet') . "</h2><ul>";
				foreach ((array)$projects as $t){
					if (get_post_status($t) == 'publish' ){
						echo "<li><a href='".get_permalink($t)."'>".get_the_title($t)."</a></li>";
					}
				}
				echo "</ul></div>";
			}
			?>
			<?php get_template_part("part", "downloads"); ?>			
			<?php
			if ('open' == $post->comment_status) {
				 comments_template( '', true ); 
			}
			
			 ?>
			
		</div> <!--end of first column-->
		
		<div class="col-lg-4 col-md-5 col-sm-6" >	

			<?php

			get_template_part("part", "related");

			get_template_part("part", "sidebar");
						
			$post_cat = get_the_category();
			if ($post_cat){
				echo "<div class='widget-box x'>
					<h3>" . __('Categories' , 'govintranet') . "</h3><p>";
				foreach($post_cat as $cat){
					echo "<span><a  class='wptag' href='" . get_term_link($cat->slug, 'category') . "/?type=vacancy'>".$cat->name."</a></span> ";
				}
				echo "</p></div>";
			}
			$posttags = '';
			if ( isset( $parent_guide ) ) $posttags = get_the_tags($parent_guide);
			if ($posttags) {
				$foundtags=false;	
				$tagstr="";
			  	foreach($posttags as $tag) {
			  		if (substr($tag->name,0,9)!="carousel:"){
			  			$foundtags=true;
			  			$tagurl = $tag->term_id;
				    	$tagstr=$tagstr."<span><a class='label label-default' href='".get_tag_link($tagurl)."/?type=vacancy'>" . str_replace(' ', '&nbsp' , $tag->name) . '</a></span> '; 
			    	}
			  	}
			  	if ($foundtags){
				  	echo "<div class='widget-box'><h3>" . __('Tags' , 'govintranet') . "</h3><p> "; 
				  	echo $tagstr;
				  	echo "</p></div>";
			  	}
			}

			?>						

			</div>
			
<?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>
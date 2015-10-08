<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
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
			<h3>Job details</h3> 
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
			if ($closing_date) $closing_date = date('j F Y', strtotime($closing_date))." ".date('G:i', strtotime($closing_time));

			$projects = get_post_meta($current_vac, 'vacancy_project', true);

			$current_attachments = get_post_meta($current_vac, 'document_attachments', true);
			
			if ( $job_reference ) echo "<strong>Job reference: </strong>".esc_attr($job_reference)."<br>";
			if ( $team ) echo "<strong>Team: </strong>".$team."<br>";
			if ( $grade ) echo "<strong>Grade: </strong>".$grade."<br>";			
			if ( $closing_date ) echo "<strong>Closing date: </strong>".$closing_date;

			$tdate= getdate();
			$tdate = $tdate['year']."-".$tdate['mon']."-".$tdate['mday'];
			$tday = date( 'd' , strtotime($tdate) );
			$tmonth = date( 'm' , strtotime($tdate) );
			$tyear= date( 'Y' , strtotime($tdate) );
			$sdate=$tyear."-".$tmonth."-".$tday;
			if ( date('l j F, Y', strtotime($closing_date)) == date('l j F, Y', strtotime($sdate ) ) ){
				echo " (That's today!)";
			}		

			echo "</div>";
			the_content(); 		
						
			if ($projects){
				echo "<div id='projects'><h2>Project</h2><ul>";
				foreach ((array)$projects as $t){
					if (get_post_status($t) == 'publish' ){
						echo "<li><a href='".get_permalink($t)."'>".get_the_title($t)."</a></li>";
					}
				}
				echo "</ul></div>";
			}
	
			
			$current_attachments = get_field('document_attachments');
			if ($current_attachments){
				echo "<div class='alert alert-info'>";
				echo "<h3>Downloads <span class='dashicons dashicons-download'></span></h3>";
				foreach ($current_attachments as $ca){
					$c = $ca['document_attachment'];
					if ( isset($c['title']) ) echo "<p><a class='alert-link' href='".$c['url']."'>".$c['title']."</a></p>";
				}
				echo "</div>";
			}	


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
					<h3>Categories</h3><p>";
				foreach($post_cat as $cat){
					echo "<span><a  class='wptag' href='".site_url()."/category/".$cat->slug."/?type=vacancy'>".$cat->name."</a></span> ";
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
			  			$tagurl = $tag->slug;
				    	$tagstr=$tagstr."<span><a class='label label-default' href='".site_url()."/tag/{$tagurl}/?type=vacancy'>" . str_replace(' ', '&nbsp' , $tag->name) . '</a></span> '; 
			    	}
			  	}
			  	if ($foundtags){
				  	echo "<div class='widget-box'><h3>Tags</h3><p> "; 
				  	echo $tagstr;
				  	echo "</p></div>";
			  	}
			}

			?>						

			</div>
			
<?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>
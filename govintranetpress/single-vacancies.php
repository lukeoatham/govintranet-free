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
	
	$vacancypod = new Pod('vacancies', $id);
	$current_task = $id;
	$current_attachments = $vacancypod->get_field('document_attachments');
	
?>
	
					<div class="col-lg-8 col-md-7 col-sm-7 white ">
						<div class="row">
							<div class='breadcrumbs'>
								<?php if(function_exists('bcn_display') && !is_front_page()) {
									bcn_display();
									}?>
							</div>
						</div>
			
			<h1><?php the_title();?> <small><i class="glyphicon glyphicon-random"></i>&nbsp;Vacancy</small></h1>
			<div class='well'>
			<p><h3>Job details</h3> 
			<?php

			$job_reference = $vacancypod->get_field('vacancy_reference');
			if (!$job_reference){
				$job_reference = 'n/a';
			}
			$team = $vacancypod->get_field('team');
			if (!$team){
				$team = 'No team specified.';
			}
			$grade = wp_get_post_terms( $post->ID, 'grade' );
			$grade = $grade[0]->name;
			if (!$grade){
				$grade = 'No grade specified.';
			}
			$closing_date = $vacancypod->get_field('closing_date');
			if (!$closing_date){
				$closing_date = 'No closing date specified.';
			}
			$eligibility_and_terms = $vacancypod->get_field('eligibility_and_terms');
			$background = $vacancypod->get_field('background');
			$requirements = $vacancypod->get_field('requirements');
			$job_specification = $vacancypod->get_field('job_specification');
			$how_to_apply = $vacancypod->get_field('how_to_apply');
			$project = $vacancypod->get_field('project');
			
			echo "<p><strong>Job reference: </strong>".$job_reference;					
			echo "<br><strong>Team: </strong>".$team;				
			echo "<br><strong>Grade: </strong>".$grade;				
			echo "<br><strong>Closing date: </strong>".date('l j F, Y', strtotime($closing_date));		
			$tdate= getdate();
			$tdate = $tdate['year']."-".$tdate['mon']."-".$tdate['mday'];
			$tday = date( 'd' , strtotime($tdate) );
			$tmonth = date( 'm' , strtotime($tdate) );
			$tyear= date( 'Y' , strtotime($tdate) );

			$sdate=$tyear."-".$tmonth."-".$tday;

			if ( date('l j F, Y', strtotime($closing_date)) == date('l j F, Y', strtotime($sdate ) ) ){
				echo " (That's today!)";
			}		

			echo "</div>			";
			the_content(); 		
			
		if ($eligibility_and_terms){
			echo "<h2>Eligibility and terms</h2>". wpautop($eligibility_and_terms);
		}	
		if ($background){
			echo "<h2>Background</h2>". wpautop($background);
		}	
		if ($requirements){
			echo "<h2>Requirements</h2>". wpautop($requirements);
		}	
		if ($job_specification){
			echo "<h2>Job specification</h2>". wpautop($job_specification);
		}	
		if ($how_to_apply){
			echo "<h2>How to apply</h2>". wpautop($how_to_apply);
		}	
			
		$projects = $vacancypod->get_field('project');
		if ($projects[0]['post_status'] == 'publish' ){
//		print_r($projects);
		if ($projects){
			echo "<div id='projects'><hr><h2>Project</h2><ul>";
				echo "<li><a href='".site_url()."/projects/{$projects[0]['post_name']}/'>".$projects[0]['post_title']."</a></li>";
			echo "</ul></div>";
		}
		}
	
			
			if ($current_attachments){
				echo "<hr><h2>Downloads</h2>";
				foreach ($current_attachments as $a){
				echo "<div class='downloadbox'><div class='downloadicon'>";
				echo "<p><a href='".$a['guid']."'>".$a['post_title']."</a></p>";
				echo "</div></div>";
				}
			}


			if ('open' == $post->comment_status) {
				 comments_template( '', true ); 
			}


			
			 ?>

			
		</div> <!--end of first column-->
		
		<div class="col-lg-4 col-md-5 col-sm-6" >	

			<?php
			
				$post_cat = get_the_category();
			if ($post_cat){
			echo "<div class='widget-box x'>
					<h3>Categories</h3><p>";
	foreach($post_cat as $cat){
		echo "<span class='wptag'><a href='".site_url()."/category/".$cat->slug."/?post_type=vacancies'>".$cat->name."</a></span> ";
	}
echo "</p></div>";
			}
				$posttags = get_the_tags($parent_guide);
				if ($posttags) {
					$foundtags=false;	
					$tagstr="";
				  	foreach($posttags as $tag) {
				  		if (substr($tag->name,0,9)!="carousel:"){
				  			$foundtags=true;
				  			$tagurl = $tag->slug;
					    	$tagstr=$tagstr."<span><a class='label label-default' href='".site_url()."/tagged/?tag={$tagurl}&amp;posttype=vacancies'>" . str_replace(' ', '&nbsp' , $tag->name) . '</a></span> '; 
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
				<?php 
 ?>

			
<?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>
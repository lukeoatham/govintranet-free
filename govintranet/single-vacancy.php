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
			<p>
				<?php
			$job_reference = get_post_meta($current_vac, 'vacancy_reference', true);
			if ($job_reference) echo "<strong>Job reference: </strong>".$job_reference."<br>";				

			$team = get_post_meta($current_vac, 'vacancy_team', true);
			if ($team) {
				$teamtemp = array();
				foreach ((array)$team as $t){
					$teamtemp[] = get_the_title($t);
				}
				$team = implode(", " , $teamtemp);
				echo "<strong>Team: </strong>".$team."<br>";
			}

			$grade = wp_get_post_terms( $post->ID, 'grade' );
			if ( $grade ) {
				$grade = $grade[0]->name;
				echo "<strong>Grade: </strong>".$grade."<br>";		
			}

			$closing_date = get_post_meta($current_vac, 'vacancy_closing_date', true);
			$closing_time = get_post_meta($current_vac, 'vacancy_closing_time', true);
			if ($closing_date){
				$closing_date = date('j F Y', strtotime($closing_date))." ".date('G:i', strtotime($closing_time));
				echo "<strong>Closing date: </strong>".$closing_date;
				if ( date('l j F, Y', strtotime($closing_date)) == date('l j F, Y', strtotime($sdate ) ) ) echo " (That's today!)";
				echo "<br>";
			}

			$projects = get_post_meta($current_vac, 'vacancy_project', true);

			$current_attachments = get_post_meta($current_vac, 'document_attachments', true);

			echo "</p>";
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
					echo "<p><a class='alert-link' href='".$c['url']."'>".$c['title']."</a></p>";
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
			$related = get_post_meta($id,'related',true);

				if ($related){
					$html='';
					foreach ($related as $r){ 
						$title_context="";
						$rlink = get_post($r);
						if ($rlink->post_status == 'publish' && $rlink->ID != $id ) {
							$taskparent=$rlink->post_parent; 
							if ($taskparent && in_array($rlink->post_type, array('task','project','team') ) ){
								$tparent_guide_id = $taskparent->ID; 		
								if ( $tparent_guide_id ) $taskparent = get_post($tparent_guide_id);
								if ( $taskparent ) $title_context=" (".govintranetpress_custom_title($taskparent->post_title).")";
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
<?php
/**
 * The Template for displaying all single task posts.
 *
 * @package WordPress
 */
 
	$external_link = get_post_meta($post->ID,'external_link',true); 
	if ($external_link){
		wp_redirect($external_link); 
		exit;
	}	

get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
<?php
	$chapter_header = false;
	$singletask = false;
	$pagetype = "";
	$taskpod = new Pod('task', $id); 
	$current_task = $id;
	$parent_guide = $taskpod->get_field('parent_guide'); 
	$parent_guide_id = $parent_guide[0]['ID']; 
	if (!$parent_guide_id){
				$parent_guide_id = $post->ID;
	}
	$children_chapters = $taskpod->get_field('children_chapters'); 
	$current_attachments = $taskpod->get_field('document_attachments');
	
	if (!$parent_guide && !$children_chapters){
		$singletask=true;
		$pagetype = "task";
		$icon = "question-sign";
	}
	else {
		$pagetype = "guide";
		$icon = "book";
	};

	if ($children_chapters && $parent_guide==''){
		$chapter_header=true;
	}

	if ($parent_guide){

	$parent_slug=$parent_guide[0]['post_name'];
	$parent_name=govintranetpress_custom_title($parent_guide[0]['post_title']); 
	$guidetitle =$parent_name;	
	}
	if (!$parent_guide){
	$guidetitle = govintranetpress_custom_title($taskpod->get_field("post_title"));
	}	
?>
		<div class="col-lg-7 col-md-8 col-sm-8 white">
			<div class="row">
				<div class='breadcrumbs'>
					<?php if(function_exists('bcn_display') && !is_front_page()) {
						bcn_display();
						}?>
				</div>
			</div>						
<?php 
if ($pagetype=="guide"):

?>
		<div>
			<h1><?php echo $guidetitle; ?> <small><i class="glyphicon glyphicon-<?php echo $icon; ?>"></i> <?php echo ucwords($pagetype); ?></small></h1>
<?php 
				$podchap = new Pod('task', $parent_guide_id); 
				$children_chapters = $podchap->get_field('children_chapters'); 				
				$totalchapters = count($children_chapters) + 1;
				$halfchapters = round($totalchapters/2,0,PHP_ROUND_HALF_UP); 
				?>
				<div class="chapters-container">
					<div class="col-lg-6 col-md-6">
						<div class="chapters">
							<nav role="navigation" class="page-navigation">
								<ol>
<?php
				if ($chapter_header){
										echo "<li class='active'>";
										echo "<span class='part-label part-title'>".$guidetitle."</span>";
										}
						else {
								$chapname = $parent_name;
								$chapslug = $parent_slug;
								echo "<li><a href='".site_url()."/task/{$chapslug}'><span class='part-title'>{$chapname}</span></a>";
								}
								echo "</li>";
							$carray = array();
							$k=1; 
							foreach ($children_chapters as $chapt)
							{
								if ($chapt['post_status']=='publish'){
								$k++;
									if (($k == $halfchapters + 1) && ($totalchapters > 3) ):?>
												</ol>
											</nav>
										</div>
									</div>
									<div class="col-lg-6 col-md-6">
										<div class="chapters">
											<nav role="navigation" class="page-navigation">
												<ol start='<?php echo $k;?>'>
									
				<?php				endif;
				
								echo "<li ";
								if (pods_url_variable(-1) == $chapt['post_name']){
									 echo "class='active'";
									 $current_chapter=$k;
								}
								echo ">";
								$chapname = govintranetpress_custom_title($chapt['post_title']);
								$chapslug = $chapt['post_name']; 
								$carray[$k]['chapter_number']=$k;
								$carray[$k]['slug']=$chapslug;
								$carray[$k]['name']=$chapname;
								if ($chapt['ID']==$current_task){
								echo "<span class='part-label part-title'>{$chapname}</span>";
								}
								else {
								echo "<a href='".site_url()."/task/{$chapslug}'><span class='part-label part-title'>{$chapname}</span></a>";
									
								}
								echo "</li>";
								}
							}
?>
								</ol>
							</nav>
						</div>
					</div>
				</div>
	</div>
	
	<?php
				
	endif;
	if ($pagetype=="guide"){
		echo "<div>";

		echo "<div class='content-wrapper-notop'>";
			if ($current_chapter>1){
				echo "<h2>".$current_chapter.". ".get_the_title()."</h2>";
			}
			else {
				echo "<h2>Overview</h2>";
			}

			the_content(); 		

			if ($current_attachments){
				echo "<div class='alert alert-info'>";
				echo "<h3>Downloads <i class='glyphicon glyphicon-download'></i></h3>";
				foreach ($current_attachments as $a){
					echo "<p><a class='alert-link' href='".$a['guid']."'>".$a['post_title']."</a></p>";
				}
				echo "</div>";
				echo "</div>";
			}

			if ('open' == $post->comment_status) {
				 comments_template( '', true ); 
			}
			echo "</div>";
			
	        echo '<div class="row">';
	
	        if ($chapter_header){ // if on chapter 1
				
				echo '<div class="col-lg-12 chapterr"><a href="'.site_url().'/task/'.$carray[2]["slug"].'">'.$carray[2]["name"].'&nbsp;<i class="glyphicon glyphicon-chevron-right"></i></a>';
				echo "</div>";
	        } 
	        elseif ($current_chapter==2) { // if on chapter 2
				echo '<div class="col-lg-6 col-md-6 chapterl"><a href="'.site_url().'/task/'.$parent_slug.'" title="Navigate to previous part"><i class="glyphicon glyphicon-chevron-left"></i>&nbsp;Overview</a></div>';
	            if ($carray[3]['slug']){
					echo '<div class="col-lg-6 col-md-6 chapterr"><a href="'.site_url().'/task/'.$carray[3]["slug"].'">'.$carray[3]["name"].'&nbsp;<i class="glyphicon glyphicon-chevron-right"></i></a></div>';
		        }
	
	        }   else { // we're deep in the middle somewhere
        	$previous_chapter = $current_chapter-1; 
        	$next_chapter = $current_chapter+1;

			echo '<div class="col-lg-6 col-md-6 chapterl"><a href="'.site_url().'/task/'.$carray[$previous_chapter]["slug"].'" title="Navigate to previous part"><i class="glyphicon glyphicon-chevron-left"></i>&nbsp;'.govintranetpress_custom_title($carray[$previous_chapter]["name"]).'</a></div>';
            if ($carray[$next_chapter]['slug']){
				echo '<div class="col-lg-6 col-md-6 chapterr"><a href="'.site_url().'/task/'.$carray[$next_chapter]["slug"].'">'.govintranetpress_custom_title($carray[$next_chapter]["name"]).'&nbsp;<i class="glyphicon glyphicon-chevron-right"></i></a></div>';
	        }
        }
		echo "</div>";
		echo "</div>";

	} else { ?>
			<h1><?php echo $guidetitle; ?> <small><i class="glyphicon glyphicon-<?php echo $icon; ?>"></i> <?php echo ucwords($pagetype); ?></small></h1>
<?php
		the_content(); 

			if ($current_attachments){
					echo "<div class='alert alert-info'>";
					echo "<h3>Downloads <i class='glyphicon glyphicon-download'></i></h3>";
					foreach ($current_attachments as $a){
						echo "<p><a class='alert-link' href='".$a['guid']."'>".$a['post_title']."</a></p>";
					}
					echo "</div>";
					echo "</div>";
			}

			if ('open' == $post->comment_status) {
				 comments_template( '', true ); 
			}
		}
			 ?>

			</div>

		<div class="col-lg-4 col-lg-offset-1 col-md-4 col-sm-4">	

				<?php 
				$podtask = new Pod('task', $id);
				$related_links = $podtask->get_field('related_tasks');
				$related_pages = $podtask->get_field('related_pages');
				if (taxonomy_exists('team')) $relatedteams = get_the_terms( $id, 'team' );

				if ($related_links || $related_pages || $relatedteams){
					$html='';
					if ($related_links){
						foreach ($related_links as $rlink){ 
							if ($rlink['post_status'] == 'publish' && $rlink['ID'] != $id ) {
									$taskpod = new Pod ('task' , $rlink['ID']);
									$taskparent=$taskpod->get_field('parent_guide');
									$title_context="";
									if ($taskparent){
										$tparent_guide_id = $taskparent[0]['ID']; 		
										$taskparent = get_post($tparent_guide_id);
										$title_context=" (".govintranetpress_custom_title($taskparent->post_title).")";
									}		
							$html.= "<li><a href='".site_url()."/task/".$rlink['post_name']."'>".govintranetpress_custom_title($rlink['post_title']).$title_context."</a></li>";
							}
						}
					}
					if ($related_pages){
						foreach ($related_pages as $rlink){ 
							if ($rlink['post_status'] == 'publish' && $rlink['ID'] != $id ) {
									$taskpod = new Pod ('page' , $rlink['ID']);
									$html.= "<li><a href='".$rlink['guid']."'>".govintranetpress_custom_title($rlink['post_title']).$title_context."</a></li>";
							}
						}
					}
					if ($relatedteams){
						foreach ($relatedteams as $r){
									$html.= "<li><a href='".site_url()."/team/".$r->slug."'>".$r->name."</a>&nbsp;<span class='glyphicon glyphicon-list-alt'></span></li>";
						}
					}
					echo "<div class='widget-box list'>";
					echo "<h3 class='widget-title'>Related</h3>";
					echo "<ul>";
					echo $html;
					echo "</ul></div>";
				}

				$post_categories = wp_get_post_categories( $post->ID );
				$cats = array();
				$catsfound = false;	
				$catshtml='';
				if ($post_categories){
					foreach($post_categories as $c){
						$cat = get_category( $c );
						$catsfound = true;
						$catshtml.= "<span class='wptag t".$cat->term_id."'><a  href='".site_url()."/task-by-category/?cat=".$cat->slug."'>".str_replace(" ","&nbsp;",$cat->name)."</a></span> ";
					}
				}
					
				if ($catsfound){
					echo "<div class='widget-box'><h3>Categories</h3><p class='taglisting page'>".$catshtml."</p></div>";
				}
				
				$posttags = get_the_tags();
				if ($posttags) {
					$foundtags=false;	
					$tagstr="";
				  	foreach($posttags as $tag) {
				  		if (substr($tag->name,0,9)!="carousel:"){
				  			$foundtags=true;
				  			$tagurl = $tag->slug;
					    	$tagstr=$tagstr."<span><a class='label label-default' href='".site_url()."/tagged/?tag={$tagurl}&amp;posttype=task'>" . str_replace(' ', '&nbsp' , $tag->name) . '</a></span> '; 
				    	}
				  	}
				  	if ($foundtags){
					  	echo "<div class='widget-box'><h3>Tags</h3><p> "; 
					  	echo $tagstr;
					  	echo "</p></div>";
				  	}
				}

		 	dynamic_sidebar('task-widget-area'); 
?>			
			</div> 
			
<?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>

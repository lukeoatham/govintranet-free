<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 */

get_header(); ?>

<?php 

if ( have_posts() ) while ( have_posts() ) : the_post(); 
	$chapter_header = false;
	$singletask = false;
	$pagetype = "";
	$taskpod = get_post($id);
	$current_task = $id;
	$parent_guide = $parent_guide_id = 0;
	if ( $taskpod->post_parent ):
		$parent_guide = get_post($taskpod->post_parent);
		$parent_guide_id = $taskpod->post_parent; 	
	endif;
	if (!$parent_guide_id){
		$parent_guide_id = $post->ID;
	}	
	$parentpod = get_post($parent_guide_id);
	$children_chapters = get_post_meta($parent_guide_id,'children_pages',true);
	$current_attachments = get_post_meta($parent_guide_id,'document_attachments',true);
	if (!$parent_guide && !$children_chapters){
		$singletask=true;
		$pagetype = "task";
	} else {
		$pagetype = "guide";
	};
	if ($children_chapters && $parent_guide==''){
		$chapter_header=true;
	}
	if ($parent_guide){
		$parent_slug=$parent_guide->post_name;
		$parent_name=govintranetpress_custom_title($parent_guide->post_title);
		$guidetitle =$parent_name;	
	}
	if (!$parent_guide){
		$guidetitle = govintranetpress_custom_title($taskpod->post_title);
	}	
	?>

	<div class="col-lg-8 col-md-8 col-sm-8">
		<div class="row">
			<div class='breadcrumbs'>
				<?php if(function_exists('bcn_display') && !is_front_page()) {
					bcn_display();
					}?>
			</div>
		</div>
		<?php 
		if ($pagetype=="guide"):
		
			if (!$chapter_header){
				echo "<h1>".$guidetitle."</h1>";
			} else {?>
				<h1><?php the_title();?> <small><i class="glyphicon glyphicon-road"></i>&nbsp;Project</small></h1>
			<?php
			}
			?>
			<div class="row">
				<div class="col-lg-6">
					<div class="chapters">
						<nav role="navigation" class="page-navigation">
							<ol>
								<?php
								if ($chapter_header){
									echo "<li class='active'>";
									echo "<span class=' part-title-label'>".$guidetitle."</span>";
								} else {
									$chapname = $parent_name;
									$chapslug = $parent_slug;
									echo "<li><a href='".site_url()."/project/{$chapslug}'><span class='part-title'>{$chapname}</span></a>";
								}
								echo "</li>";
								$carray = array();
								$k=1; 
								foreach ($children_chapters as $chaptmast){
									$chapt = get_post($chaptmast);
									if ($chapt->post_status=='publish'){
										$k++;
										if (($k == round(count($children_chapters)/2,0) + 1) && (count($children_chapters) > 3) ):?>
												</ol>
											</nav>
										</div>
									</div>
									<div class="col-lg-6">
										<div class="chapters">
											<nav role="navigation" class="page-navigation">
												<ol start='<?php echo $k;?>'>
												<?php
									endif;
									echo "<li ";
									if (pods_url_variable(-1) == $chapt->post_name){
										 echo "class='active'";
										 $current_chapter=$k;
									}
									echo ">";
									$chapname = govintranetpress_custom_title($chapt->post_title);
									$chapslug = $chapt->post_name; 
									$carray[$k]['chapter_number']=$k;
									$carray[$k]['slug']=$chapslug;
									$carray[$k]['name']=$chapname;
									if ($chapt['ID']==$current_task){
										echo "<span class='part-label part-title'>{$chapname}</span>";
									} else {
										echo "<a href='".site_url()."/project/{$chapslug}'><span class='part-label part-title'>{$chapname}</span></a>";
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
			<hr>
			<?php
			endif;
			if ($pagetype=="guide"){
				if (!$chapter_header){
					echo "<h2><strong>".get_the_title()."</strong></h2>";
				} else {
					$poverview = $taskpod->get_field('project_overview');
					if ($poverview) : 
						echo "<div class='well'><p><h3>Overview</h3><p>";
						echo $poverview;
						echo "</div>";
					endif;
					$policylink = get_post_meta($taskpod->ID,'policy_link',true);
					if ($policylink){
						echo "<p><a href ='{$policylink}'>View the associated policy on GOV.UK</a></p>"; 
					}
				}
			} 
			if ($pagetype=='task'){
			?>
				<h1><?php the_title();?> <small>Project</small></h1>
			<?php
				$poverview = get_post_meta($taskpod->ID,'project_overview',true);
				if ($poverview) : 
					echo "<div class='well'><p><h3>Overview</h3>";
					echo $poverview;
					echo "</div>";
				endif; 
				$policylink = get_post_meta($taskpod->ID,'policy_link',true);
				if ($policylink){
					echo "<p><a href ='{$policylink}'>View the associated policy on GOV.UK</a></p>"; 
				}
			}		

			the_content(); 		
			
			$team = get_post_meta($taskpod->ID,'project_team_members',true);
			if ($team){
				echo "<div id='team'><hr><h2>Team members</h2>";
				foreach ($team as $t){
					echo do_shortcode('[people id="'.$t.'"]');
				}
				echo "</div><div class='clearfix'></div>";
			}
			
			$vacancies = get_post_meta($taskpod->ID,'project_vacancies',true);
			if ($vacancies){
				$html = "<div id='vacancies'><hr><h2>Project vacancies</h2><ul>";
				$k==0;
				foreach ($vacancies as $vmast){
					$v = get_post($vmast);
					if ($v->post_status=='publish') {
						$k++;
						$html.= "<li><a href='".site_url()."/vacancy/{$v->post_name}'>".$v->post_title."</a></li>";
					}
				}
				$html .= "</ul></div>";
				if ($k>0) {
					echo $html;
				}
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
		</div> <!--end of first column-->

		<div class="col-lg-4 col-md-4 col-sm-4">	
		
			<?php 
				$html='';
				$related = get_post_meta($id,'related',true);

				if ($related){
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
								$html.= "<li><a href='".get_permalink($rlink->ID)."'>".govintranetpress_custom_title($rlink->post_title).$title_context."</a></li>";
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
		
			$posttags = get_the_tags($parent_guide_id);
			if ($posttags) {
				$foundtags=false;	
				$tagstr="";
			  	foreach($posttags as $tag) {
			  		if (substr($tag->name,0,9)!="carousel:"){
			  			$foundtags=true;
			  			$tagurl = $tag->slug;
				    	$tagstr=$tagstr."<span><a class='label label-default' href='".site_url()."/tag/{$tagurl}/?type=project'>" . str_replace(' ', '&nbsp' , $tag->name) . '</a></span> '; 
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

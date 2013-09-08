<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */

$slug = pods_url_variable(0);
if ($slug=="homepage"){
	wp_redirect('/');
};
if ($slug=="control"){
	wp_redirect('/');
};

get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
<?php
	
	$chapter_header = false;
	$singletask = false;
	$pagetype = "";
	
	$taskpod = new Pod('projects', $id);
	$current_task = $id;
	$parent_guide = $taskpod->get_field('parent_project'); 	
	$parent_guide_id = $parent_guide[0]['ID']; 	
	if (!$parent_guide_id){
				$parent_guide_id = $post->ID;
	}	
	$parentpod = new Pod ('projects' , $parent_guide_id);
	$children_chapters = $parentpod->get_field('children_pages'); 	
	$current_attachments = $taskpod->get_field('document_attachments');
	

	if (!$parent_guide && !$children_chapters){
		$singletask=true;
		$pagetype = "task";
	}
	else {
		$pagetype = "guide";
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
			<h1 class="taglisting project"><?php echo $guidetitle; ?> <span class="title-cat">Project</span></h1>

			</div>
<?php 
if ($pagetype=="guide"):

?>
		<div class="threecol">
			<div class="chapters">
    <nav role="navigation" class="page-navigation">
    <ol>
<?php
if ($chapter_header){
						echo "<li class='active'>";
						echo "<span class='part-title'>".get_the_title()."</span>";
						}
				else {
						$chapname = $parent_name;
						$chapslug = $parent_slug;
						echo "<li><a href='/about/projects/content/{$chapslug}'><span class='part-title'>{$chapname}</span></a>";
						}
						echo "</li>";
        

			$totalchapters = count($children_chapters);
			$carray = array();
			$k=1; 
			foreach ($children_chapters as $chapt)
			{
			if ($chapt['post_status']=='publish'){
				$k++;
				echo "<li ";
				if (pods_url_variable(3) == $chapt['post_name']){
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
				echo "<span class='part-title'>{$chapname}</span>";
				}
				else {
				echo "<a href='/about/projects/content/{$chapslug}'><span class='part-title'>{$chapname}</span></a>";
					
				}
						echo "</li>";
						}
						
					}
?>



	  </ol>
	  </nav>
</div>
		</div>
<?php
endif;
	if ($pagetype=="guide"){
		echo "<div class='ninecol last'>";
		echo "<div class='content-wrapper-notop'>";
				if ($current_chapter>1){
					echo "<h2>".get_the_title()."</h2>";
				}
				else {
			
			$poverview = $taskpod->get_field('project_overview');
			if ($poverview) : ?>

				<?php 
				echo "<div class='alert green'><p><h3>Overview</h3><p>";
				echo $poverview;
		

$policylink = $taskpod->get_field('policy_link');
if ($policylink){
	echo "<p><a href ='{$policylink}'>View the associated policy on GOV.UK</a></p>"; 
}
?>
			</div>

<?php endif; ?>

<?php
				}
	} 
	else {
		echo "<div class='twelvecol last'>";		
		echo "<div class='content-wrapper'>";
	}
	if ($pagetype=='task'){
	
			$poverview = $taskpod->get_field('project_overview');
			if ($poverview) : ?>
		
			<?php
				echo "<div class='alert green'><p><h3>Overview</h3>";
			
			echo $poverview;
		


$policylink = $taskpod->get_field('policy_link');
if ($policylink){
	echo "<p><a href ='{$policylink}'>View the associated policy on GOV.UK</a></p>"; 
}
?>
			</div>

			<?php endif; ?>

			<?php	
			}		
			the_content(); 		
			
		$team = $taskpod->get_field('team_members');
		if ($team){
			echo "<div id='team'><hr><h2>Team members</h2>";
			echo wpautop($team)."</div>";
		}

		
		$vacancies = $taskpod->get_field('project_vacancies');
		if ($vacancies){
			$html = "<div id='vacancies'><hr><h2>Project vacancies</h2><ul>";
			$k==0;
			foreach ($vacancies as $v){
				if ($v['post_status']=='publish') {
					$k++;
					$html.= "<li><a href='/projects/{$v['post_name']}'>".$v['post_title']."</a></li>";
				}
			}
			$html .= "</ul></div>";
			if ($k>0) {
				echo $html;
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
		echo "</div>";

			
			 ?>

			</div>
			
		</div> <!--end of first column-->
		
		<div class="fourcol last" >	

				<?php 
				$podtask = new Pod('projects', $id);
				$related_links = $podtask->get_field('related_projects');
				if ($related_links){
				echo "<div class='widget-box list'>";
				echo "<h3 class='widget-title'>Related projects</h3>";
				echo "<ul>";
				foreach ($related_links as $rlink){
					if ($rlink['post_status'] == 'publish') {
					echo "<li><a href='/about/projects/content/".$rlink['post_name']."'>".govintranetpress_custom_title($rlink['post_title'])."</a></li>";
					}
				}
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
					    	$tagstr=$tagstr."<span class='wptag'><a href='/tagged/?tag={$tagurl}&amp;posttype=projects'>" . str_replace(' ', '&nbsp;' , $tag->name) . '</a></span> '; 
				    	}
				  	}
				  	if ($foundtags){
					  	echo "<div class='widget-box'><h3>Tags</h3><p>"; 
					  	echo $tagstr;
					  	echo "</p></div>";
				  	}
		echo "</div>";

				}
?>
				</div>
				
			
<?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>
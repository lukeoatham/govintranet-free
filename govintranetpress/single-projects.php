<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */

get_header(); ?>

<?php 

if ( have_posts() ) while ( have_posts() ) : the_post(); 
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
	} else {
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
		echo "<h1>".$guidetitle." <small><i class='glyphicon glyphicon-road'></i>&nbsp;Project</small></h1>";
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
						echo "<li><a href='".site_url()."/projects/{$chapslug}'><span class='part-title'>{$chapname}</span></a>";
					}
					echo "</li>";
					$carray = array();
					$k=1; 
					foreach ($children_chapters as $chapt){
						if ($chapt['post_status']=='publish'){
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
						} else {
							echo "<a href='".site_url()."/projects/{$chapslug}'><span class='part-label part-title'>{$chapname}</span></a>";
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
		$policylink = $taskpod->get_field('policy_link');
		if ($policylink){
			echo "<p><a href ='{$policylink}'>View the associated policy on GOV.UK</a></p>"; 
		}
	}
} 
if ($pagetype=='task'){
?>
	<h1><?php the_title();?> <small><i class="glyphicon glyphicon-road"></i>&nbsp;Project</small></h1>
<?php
	$poverview = $taskpod->get_field('project_overview');
	if ($poverview) : 
		echo "<div class='well'><p><h3>Overview</h3>";
		echo $poverview;
		echo "</div>";
	endif; 
	$policylink = $taskpod->get_field('policy_link');
	if ($policylink){
		echo "<p><a href ='{$policylink}'>View the associated policy on GOV.UK</a></p>"; 
	}
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
			$html.= "<li><a href='".site_url()."/vacancies/{$v['post_name']}'>".$v['post_title']."</a></li>";
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

			
			 ?>
</div> <!--end of first column-->

<div class="col-lg-4 col-md-4 col-sm-4">	

	<?php 
	$podtask = new Pod('projects', $id);
	$related_links = $podtask->get_field('related_projects');
	$html='';
	foreach ($related_links as $rlink){
		if ($rlink['post_status'] == 'publish' && $rlink['ID'] != $id ) {
			$html.= "<li><a href='".site_url()."/projects/".$rlink['post_name']."'>".govintranetpress_custom_title($rlink['post_title'])."</a></li>";
		}
	}
	if (taxonomy_exists('team')) $relatedteams = get_the_terms( $id, 'team' );
	if ($relatedteams){
		foreach ($relatedteams as $r){
			$html.= "<li><a href='".site_url()."/team/".$r->slug."'>".$r->name."</a>&nbsp;<span class='glyphicon glyphicon-list-alt'></span></li>";
		}
	}
	if ($html){
		echo "<div class='widget-box list'>";
		echo "<h3 class='wid		get-title'>Related</h3>";
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
		    	$tagstr=$tagstr."<span><a class='label label-default' href='".site_url()."/tagged/?tag={$tagurl}&amp;posttype=projects'>" . str_replace(' ', '&nbsp' , $tag->name) . '</a></span> '; 
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

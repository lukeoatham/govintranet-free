<?php
/* Template name: Tagged  */

get_header(); 

?>
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

				<div class="row">

					
					<div class="eightcol white last" id="content">
						<div class="row">
							<div class='breadcrumbs'>
							<?php if(function_exists('bcn_display') && !is_front_page()) {
								bcn_display();
							}?>
							</div>
							
				</div>
					<div class="content-wrapper">
				<h1><?php
				$thistag = $_GET['tag'];
				$thistag = get_term_by('slug',$thistag,'post_tag');
				$thistagid = $thistag->term_id; //echo $thistagid;
				$thistagslug = $thistag->slug; //echo $thistagslug;
				$t=$_GET['tag'];
				$thistag = $thistag->name;
				if ($_GET['posttype'] == 'task'){
					printf( __( 'Tasks/guides tagged: %s', 'twentyten' ), '' . $thistag. '' );
				}
				elseif ($_GET['posttype'] == 'projects'){
					printf( __( 'Projects tagged: %s', 'twentyten' ), '' . $thistag . '' );
				}
				elseif ($_GET['posttype'] == 'vacancies'){
					printf( __( 'Job vacancies tagged: %s', 'twentyten' ), '' . $thistag . '' );
				}
				elseif ($_GET['posttype'] == 'news'){
					printf( __( 'News tagged: %s', 'twentyten' ), '' . $thistag . '' );
				}
				else
				{
					printf( __( 'Everything tagged: %s', 'twentyten' ), '' . $thistag . '' );
				}
				?></h1>

<?php
/* Run the loop for the tag archive to output the posts
 * If you want to overload this in a child theme then include a file
 * called loop-tag.php and that will be used instead.
 */
//$paged=$_GET['paged'];
$pt=$_GET['posttype'];
if (!$pt){
	$pt='any';
}
$tq =  						array(
 						'tax_query'=> array (array(
 						'terms'=>$thistagid,
 						'taxonomy'=>'post_tag',
 						'field'=>'term_id',
 						)),
 						'post_type'=>$pt,
 						'paged'=>$paged,
 						'posts_per_page'=> 10,
 						'orderby'=>'title',
 						'order'=>'ASC'
 						);
 						if ($pt=='any'){
 				$tagquery=
				"select object_id from wp_term_relationships , wp_term_taxonomy, wp_terms, wp_posts
where wp_term_taxonomy.term_taxonomy_id = wp_term_relationships.term_taxonomy_id AND
wp_term_taxonomy.term_id = wp_terms.term_id and 
wp_terms.term_id = ".$thistagid." and
wp_term_relationships.object_id = wp_posts.id and
wp_posts.post_status = 'publish'
					";
	 						
 						} else {
 				$tagquery=
				"select object_id from wp_term_relationships , wp_term_taxonomy, wp_terms, wp_posts
where wp_term_taxonomy.term_taxonomy_id = wp_term_relationships.term_taxonomy_id AND
wp_term_taxonomy.term_id = wp_terms.term_id and 
wp_terms.term_id = ".$thistagid." and
wp_term_relationships.object_id = wp_posts.id and
wp_posts.post_status = 'publish' and
wp_posts.post_type='" . $pt . "'";
	 						
 						}
				$testtag = $wpdb->get_results($tagquery);
				if (count($testtag) > 0){
				$postsfound=true;
				$carray = array();
					foreach ($testtag as $tt){
						$carray[]=$tt->object_id;
					}
					}
					else {$postsfound=false;
						echo "<h2>Nothing on the intranet with this tag.</h2>";
						
					}
 $tagged = new WP_Query(array(
 			'post_type'=>array("task","vacancies","projects","news"),
 			'post__in'=>$carray,
 			'paged'=>$paged,
 			'posts_per_page'=>10,
 			'orderby'=>'title',
 			'order'=>'ASC'
 			));
 			
 							$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
							$counter = 0;	

 			while ($tagged->have_posts() && $postsfound ) {
								$tagged->the_post();

				$post_type = ucwords($post->post_type);
				$post_cat = get_the_category();
	echo "<hr/>";
		
$image_url = get_the_post_thumbnail($ID, 'thumbnail', array('class' => 'alignright'));
echo "<div class='newsitem'><a href='";
the_permalink();
echo "'>".$image_url."</a>" ;

?>

	<h3 
	<?php 		
	if ($post_type=='Task'){
		$taskpod = new Pod ('task' , $post->ID); 
		if ( !$taskpod->get_field('parent_guide')  && !$taskpod->get_field('children_chapters')){
		echo " class='taglisting task'";
				} else {
		echo " class='taglisting guide'";	
		}
			$taskparent=$taskpod->get_field('parent_guide');
	$title_context="";
	if ($taskparent){
	$parent_guide_id = $taskparent[0]['ID']; 		
	$taskparent = get_post($parent_guide_id);
	$title_context=" (".govintranetpress_custom_title($taskparent->post_title).")";
	}			

		}
	if ($post_type=='Projects'){
		echo " class='taglisting project'";
			$taskpod = new Pod ('projects' , $post->ID); 
	$projparent=$taskpod->get_field('parent_project');
	$title_context="";
	if ($projparent){
	$parent_guide_id = $projparent[0]['ID']; 		
	$projparent = get_post($parent_guide_id);
	$title_context=" (".govintranetpress_custom_title($projparent->post_title).")";
	}			

		}
	if ($post_type=='News'){
		echo " class='taglisting news'";
		}
	if ($post_type=='Vacancies'){
		echo " class='taglisting vacancies'";
		}
		?>
	><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'govintranetpress' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); echo $title_context;?></a></h3>
	<?php 


	if ($post_type=="Task" ){
		$taskpod = new Pod ('task' , $post->ID); 
		if ( !$taskpod->get_field('parent_guide')  && !$taskpod->get_field('children_chapters')){
		echo "<p class='taglisting task'>";
				} else {
		echo "<p class='taglisting guide'>";	
		}
		foreach($post_cat as $cat){
					if ($cat->name != 'Uncategorized' ){

			echo "<span class='brdall b".$cat->term_id."'> ".$cat->name;
				if ( !$taskpod->get_field('parent_guide')  && !$taskpod->get_field('children_chapters') ){
				echo " task ";
				} else {
				echo " guide ";					
				}
			}
			echo "</span>&nbsp;";
			}
		echo "</p>";
	}

	?>
	<?php 
	if ($post_type=="News" ){
		echo "<div class='taglisting {$post->post_type}'>";
		foreach($post_cat as $cat){
			if ($cat->name != 'Uncategorized' ){
			echo "<span class='brdall".$cat->term_id."'>".$cat->name;
			echo "</span>&nbsp;";
			}
			}
		echo "</div>";
	}
		
	   if ($post_type=="News" ) {
	   $thisdate= $post->post_date;
		$thisdate=date("j M Y",strtotime($thisdate));
		echo "<p class='news_date'>".$thisdate."</p>";
			}
	
				{ 
				the_excerpt(); 
				};

			?>

	</div><div class="clearfix"></div>

	<?php 
			}

						 if (  $tagged->max_num_pages > 1 && $postsfound ) : ?>
						<?php if (function_exists(wp_pagenavi)) : ?>
							<?php wp_pagenavi(array('query' => $tagged)); ?>
 						<?php else : ?>
							<?php next_posts_link('&larr; Older items', $tagged->max_num_pages); ?>
							<?php previous_posts_link('Newer items &rarr;', $tagged->max_num_pages); ?>						
						<?php endif; 
						?>
					<?php endif; 
							wp_reset_query();								
							?>
					</div>
					</div>

					<div class="fourcol last" id='sidebar'>
					<div class="widget-box list"><h3 class="widget-title">
					<?php if (!$_GET['posttype']){
						echo "Filter";
					}
					else {echo "More";}
					?>
					</h3>
					<ul>
					<?php					
				$t=get_tag($thistagid);
				$t=$_GET['tag'];
				if ($_GET['posttype'] == 'task'){
				echo "<li><a href='/tasks/'>All tasks and guides</a></li>";
				}
				if ($_GET['posttype'] == 'projects'){
				echo "<li><a href='/about/projects/'>All projects</a></li>";
				}
				if ($_GET['posttype'] == 'vacancies'){
				echo "<li><a href='/about/vacancies/'>All job vacancies</a></li>";
				}
				if ($_GET['posttype'] != ''){
				echo "<li><a href='/tagged/?tag=".$_GET['tag']."'>";
					printf( __( 'Everything tagged: %s', 'twentyten' ), '' . $thistag . '' );
				echo "</a></li>";		
				}
				if ($_GET['posttype'] != 'task'){
				$tagquery=
				"select count(distinct wp_posts.id) as numtags from wp_posts
 join wp_term_relationships on wp_term_relationships.object_id = wp_posts.id
 join wp_term_taxonomy on wp_term_taxonomy.term_taxonomy_id = wp_term_relationships.term_taxonomy_id
 join wp_terms on wp_terms.term_id = wp_term_taxonomy.term_id
where wp_terms.slug='".$t."' AND
wp_posts.post_type='task' AND
wp_posts.post_status = 'publish'
					";
				$testtag = $wpdb->get_results($tagquery);
				if ($testtag[0]->numtags > 0){
				echo "<li><a href='/tagged/?tag=".$thistagslug."&amp;posttype=task'>Tasks and guides</a></li>";		
				}
				}
				if ($_GET['posttype'] != 'projects'){
				$tagquery=
				"select count(distinct wp_posts.id) as numtags from wp_posts
 join wp_term_relationships on wp_term_relationships.object_id = wp_posts.id
 join wp_term_taxonomy on wp_term_taxonomy.term_taxonomy_id = wp_term_relationships.term_taxonomy_id
 join wp_terms on wp_terms.term_id = wp_term_taxonomy.term_id
where wp_terms.slug='".$t."' AND
wp_posts.post_type='projects' AND
wp_posts.post_status = 'publish'
					";
				$testtag = $wpdb->get_results($tagquery);
				if ($testtag[0]->numtags > 0){
				echo "<li><a href='/tagged/?tag=".$thistagslug."&amp;posttype=projects'>Projects</a></li>";		
				}
				}
				if ($_GET['posttype'] != 'vacancies'){
				$tagquery=
				"select count(distinct wp_posts.id) as numtags from wp_posts
 join wp_term_relationships on wp_term_relationships.object_id = wp_posts.id
 join wp_term_taxonomy on wp_term_taxonomy.term_taxonomy_id = wp_term_relationships.term_taxonomy_id
 join wp_terms on wp_terms.term_id = wp_term_taxonomy.term_id
where wp_terms.slug='".$t."' AND
wp_posts.post_type='vacancies' AND
wp_posts.post_status = 'publish'					";
				$testtag = $wpdb->get_results($tagquery);
				if ($testtag[0]->numtags > 0){
				
				echo "<li><a href='/tagged/?tag=".$thistagslug."&amp;posttype=vacancies'>Job vacancies</a></li>";		
				}
				}
				if ($_GET['posttype'] != 'news'){
				$tagquery=
				"select count(distinct wp_posts.id) as numtags from wp_posts
 join wp_term_relationships on wp_term_relationships.object_id = wp_posts.id
 join wp_term_taxonomy on wp_term_taxonomy.term_taxonomy_id = wp_term_relationships.term_taxonomy_id
 join wp_terms on wp_terms.term_id = wp_term_taxonomy.term_id
where wp_terms.slug='".$t."' AND
wp_posts.post_type='news' AND
wp_posts.post_status = 'publish'
					";
				$testtag = $wpdb->get_results($tagquery);
				if ($testtag[0]->numtags > 0){
				
				echo "<li><a href='/tagged/?tag=".$thistagslug."&amp;posttype=news'>News</a></li>";		
				}
				}
							?>
					</ul>
					</div>					
					</div>

				</div>
<?php endwhile; ?>

<?php get_footer(); ?>
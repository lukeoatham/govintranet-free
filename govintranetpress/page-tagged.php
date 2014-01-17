<?php
/* Template name: Tagged  */

get_header(); 

?>
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

					<div class="col-lg-8 col-md-8 white ">
						<div class="row">
							<div class='breadcrumbs'>
								<?php if(function_exists('bcn_display') && !is_front_page()) {
									bcn_display();
									}?>
							</div>
						</div>
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
				elseif ($_GET['posttype'] == 'event'){
					printf( __( 'Events tagged: %s', 'twentyten' ), '' . $thistag . '' );
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
					else { $postsfound=false;
						echo "<h2>Nothing on the intranet with this tag.</h2>";
						
					}
 $tagged = new WP_Query(array(
 			'post_type'=>array("task","vacancies","projects","news","event"),
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
				$image_url = get_the_post_thumbnail($ID, 'thumbnail', array('class' => 'alignright'));

echo "<div class='media'>" ;
?>


	<?php 	
	$contexturl=$post->guid;
	$context='';
	$titlecontext='';
	if ($post_type=='Post_tag') { 
		$icon = "tag"; 
	}	
	if ($post_type=='Task'){
		$contexturl = "/tasks/";
		$taskpod = new Pod ('task' , $post->ID); 
		if ( $taskpod->get_field('page_type') == 'Task'){		
			$context = "task";
			$icon = "question-sign";
			$title_context='';
		} else {
			$context = "guide";
			$icon = "book";
			$taskparent=$taskpod->get_field('parent_guide');
			$title_context='';
			if ($taskparent){
				$parent_guide_id = $taskparent[0]['ID']; 		
				$taskparent = get_post($parent_guide_id);
				$title_context=" (".govintranetpress_custom_title($taskparent->post_title).")"; 
			}
		}			
	}
	if ($post_type=='Projects'){
		$context = "project";
		$contexturl = "/about/projects/";
		$icon = "road";
		$taskpod = new Pod ('projects' , $post->ID); 
		$projparent=$taskpod->get_field('parent_project');
		$title_context='';	
		if ($projparent){
			$parent_guide_id = $projparent[0]['ID']; 		
			$projparent = get_post($parent_guide_id);
			$title_context=" (".govintranetpress_custom_title($projparent->post_title).")";
		}			
	}
	if ($post_type=='News'){
			$context = "news";
	$contexturl = "/news/";
			$icon = "star-empty";			
	}
	if ($post_type=='Vacancies'){
			$context = "job vacancy";
		$contexturl = "/about/vacancies/";
			$icon = "random";			
	}
	if ($post_type=='Blog'){
			$context = "blog";
		$contexturl = "/blog/";
			$icon = "comment";			
	}
	if ($post_type=='Event'){
			$context = "event";
			$contexturl = "/events/";
			$icon = "calendar";			
	}
	if ($post_type=='Glossaryitem'){
			$context = "jargon buster";
			$contexturl = "/glossaryitem/";
			$icon = "th-list";			
	}
	if ($post_type=='User'){
			$context = "staff";
			$contexturl = "/staff/";
			$icon = "user";			
	}
	if ($post_type=='Attachment'): 
	$context='download';
	$icon = "download";			
?>
	<h3>				
	<a href="<?php echo wp_get_attachment_url( $post->id ); ?>" title="<?php printf( esc_attr__( '%s %s', 'govintranetpress' ), the_title_attribute( 'echo=0' ), " (" . $context . ")" ); ?>" rel="bookmark"><?php the_title();  ?></a></h3>


<?php 
elseif ($post_type=='User'): 

?>
	<h3>				
	<a href="<?php echo $userurl; ?>" title="<?php printf( esc_attr__( '%s %s', 'govintranetpress' ), the_title_attribute( 'echo=0' ), " (" . $context . ")" ); ?>" rel="bookmark"><?php the_title();  ?></a></h3>

<?php 
else: ?>
	<h3>				
	<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( '%s %s', 'govintranetpress' ), the_title_attribute( 'echo=0' ), " (" . $context . ")" ); ?>" rel="bookmark"><?php the_title(); echo "<small>".$title_context."</small>"; ?></a></h3>

<?php
endif;
	

	echo "<a href='";
	the_permalink();
	echo "'><div class='hidden-xs'>".$image_url."</div></a>" ;

	echo "<div class='media-body'>";
	
	if (($post_type=="Task" && $pageslug!="category")){
		$taskpod = new Pod ('task' , $post->ID); 
		echo "<p>";
		echo '<span class="listglyph"><i class="glyphicon glyphicon-'.$icon.'"></i>&nbsp;'.ucfirst($context).'</span>&nbsp;&nbsp;';
		foreach($post_cat as $cat){
			if ($cat->name != 'Uncategorized' ){
				echo "<span class='listglyph'><span class='glyphicon glyphicon-stop gb".$cat->term_id."'></span>&nbsp;".$cat->name;
			echo "</span>&nbsp;&nbsp;";
			}
			}
		echo "</p>";
	}

	elseif ($post_type=="News" && $pageslug!="category"){
		echo "<div><p>";
		echo '<span class="listglyph"><i class="glyphicon glyphicon-'.$icon.'"></i>&nbsp;'.ucfirst($context).'</span>&nbsp;&nbsp;';
		foreach($post_cat as $cat){
			if ($cat->name != 'Uncategorized' ){
				echo "<span class='listglyph'><span class='glyphicon glyphicon-stop gb".$cat->term_id."'></span>&nbsp;".$cat->name;
			echo "</span>&nbsp;&nbsp;";
			}
		}
			   $thisdate= $post->post_date;
			   $thisdate=date("j M Y",strtotime($thisdate));
			   echo "<span class='listglyph'><i class='glyphicon glyphicon-calendar'></i> ".$thisdate."</span> ";


		echo "</p></div>";
	}

	elseif ($post_type=="Event" && $pageslug!="category"){
		echo "<div><p>";
			   $thisdate= get_post_meta($post->ID, 'event_start_date', true);
			   $thisdate=date("j M Y",strtotime($thisdate));
		echo '<span class="listglyph"><i class="glyphicon glyphicon-'.$icon.'"></i>&nbsp;'.ucfirst($context).'&nbsp;'.$thisdate.'</span>&nbsp;&nbsp;';
		foreach($post_cat as $cat){
			if ($cat->name != 'Uncategorized' ){
				echo "<span class='listglyph'><span class='glyphicon glyphicon-stop gb".$cat->term_id."'></span>&nbsp;".$cat->name;
			echo "</span>&nbsp;&nbsp;";
			}
		}
		echo "</p></div>";
	} else {
		echo "<div><p>";
		echo '<span class="listglyph"><i class="glyphicon glyphicon-'.$icon.'"></i>&nbsp;'.ucfirst($context).'</span>&nbsp;&nbsp;';
			   $thisdate= $post->post_modified;
			   $thisdate=date("j M Y",strtotime($thisdate));
			   echo "<span class='listglyph'><i class='glyphicon glyphicon-calendar'></i> Updated ".$thisdate."</span> ";
		foreach($post_cat as $cat){
			if ($cat->name != 'Uncategorized' ){
				echo "<span class='listglyph'><span class='glyphicon glyphicon-stop gb".$cat->term_id."'></span>&nbsp;".$cat->name;
			echo "</span>&nbsp;&nbsp;";
			}
		}
		echo "</p></div>";
		
	}
	
?></div>
	<?php if ( true ) : // Only display excerpts for archives and search. ?>
	<?php	

		if ($post_type=='Post_tag') { 
			echo "All intranet pages tagged with \"". get_the_title() ."\""; 
		}
		else if ($post_type=='Category') 
		{ 
			echo "All intranet pages categorised as \"". get_the_title() ."\""; 
		}

		the_excerpt(); 				

		
		//for rating stories
 		if (function_exists('wp_gdsr_render_article')){
	 		wp_gdsr_render_article(44, true, 'soft', 16);
		}
		
			?>
			</div>
	<?php 
				echo "<hr>";

	endif; 
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

					<div class="col-lg-4 col-md-4" id='sidebar'>
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
				echo "<li><a href='".site_url()."/how-do-i/'>All tasks and guides</a></li>";
				}
				if ($_GET['posttype'] == 'projects'){
				echo "<li><a href='".site_url()."/about/projects/'>All projects</a></li>";
				}
				if ($_GET['posttype'] == 'event'){
				echo "<li><a href='".site_url()."/events/'>All events</a></li>";
				}
				if ($_GET['posttype'] == 'vacancies'){
				echo "<li><a href='".site_url()."/about/vacancies/'>All job vacancies</a></li>";
				}
				if ($_GET['posttype'] != ''){
				echo "<li><a href='".site_url()."/tagged/?tag=".$_GET['tag']."'>";
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
				echo "<li><a href='".site_url()."/tagged/?tag=".$thistagslug."&amp;posttype=task'>Tasks and guides</a></li>";		
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
				echo "<li><a href='".site_url()."/tagged/?tag=".$thistagslug."&amp;posttype=projects'>Projects</a></li>";		
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
				
				echo "<li><a href='".site_url()."/tagged/?tag=".$thistagslug."&amp;posttype=vacancies'>Job vacancies</a></li>";		
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
				
				echo "<li><a href='".site_url()."/tagged/?tag=".$thistagslug."&amp;posttype=news'>News</a></li>";		
				}
				}
				if ($_GET['posttype'] != 'event'){
				$tagquery=
				"select count(distinct wp_posts.id) as numtags from wp_posts
 join wp_term_relationships on wp_term_relationships.object_id = wp_posts.id
 join wp_term_taxonomy on wp_term_taxonomy.term_taxonomy_id = wp_term_relationships.term_taxonomy_id
 join wp_terms on wp_terms.term_id = wp_term_taxonomy.term_id
where wp_terms.slug='".$t."' AND
wp_posts.post_type='event' AND
wp_posts.post_status = 'publish'
					";
				$testtag = $wpdb->get_results($tagquery);
				if ($testtag[0]->numtags > 0){
				
				echo "<li><a href='".site_url()."/tagged/?tag=".$thistagslug."&amp;posttype=event'>Events</a></li>";		
				}
				}
							?>
					</ul>
					</div>					
				</div>
<?php endwhile; ?>

<?php get_footer(); ?>
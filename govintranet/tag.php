<?php
get_header(); 
?>
<?php if ( have_posts() )  : the_post(); ?>

			<div class="col-lg-7 col-md-8 col-sm-12 white">
				<div class="row">
					<div class='breadcrumbs'>
						<?php if(function_exists('bcn_display') && !is_front_page()) {
							bcn_display();
							}?>
					</div>
				</div>
				<h1><?php
				$posttype = '';
				if ( isset( $_GET['type'] ) ) $posttype = $_GET['type'];
				$thistagid = get_queried_object()->term_id;
				$thistagslug = get_queried_object()->slug; //echo $thistagslug;
				$thistag = get_queried_object()->name;
				if ($posttype == 'task'){
					printf( __( 'Tasks/guides tagged: %s', 'govintranet' ), '' . $thistag. '' );
				}
				elseif ($posttype == 'project'){
					printf( __( 'Projects tagged: %s', 'govintranet' ), '' . $thistag . '' );
				}
				elseif ($posttype == 'vacancy'){
					printf( __( 'Job vacancies tagged: %s', 'govintranet' ), '' . $thistag . '' );
				}
				elseif ($posttype == 'news'){
					printf( __( 'News tagged: %s', 'govintranet' ), '' . $thistag . '' );
				}
				elseif ($posttype == 'blog'){
					printf( __( 'Blog posts tagged: %s', 'govintranet' ), '' . $thistag . '' );
				}
				elseif ($posttype == 'event'){
					printf( __( 'Events tagged: %s', 'govintranet' ), '' . $thistag . '' );
				}
				else
				{
					printf( __( 'Everything tagged: %s', 'govintranet' ), '' . $thistag . '' );
				}
				?></h1>

				<?php
				/* Run the loop for the tag archive to output the posts
				 * If you want to overload this in a child theme then include a file
				 * called loop-tag.php and that will be used instead.
				 */
				//$paged=$_GET['paged'];
				$pt=$posttype;
				if (!$pt){
					$pt='any';
				}
				$tq =  	array(
 						'tax_query'=> array (array(
 						'terms'=>$thistagid,
 						'taxonomy'=>'post_tag',
 						'field'=>'term_id',
 						)),
 						'post_type'=>$pt,
 						'paged'=>$paged,
 						'posts_per_page'=> 10,
 						'orderby'=>'name',
 						'order'=>'ASC'
 						);
 				if ($pt=='any'){
	 				$tagquery=
					"select object_id from $wpdb->term_relationships , $wpdb->term_taxonomy, $wpdb->terms, $wpdb->posts
	where $wpdb->term_taxonomy.term_taxonomy_id = $wpdb->term_relationships.term_taxonomy_id AND
	$wpdb->term_taxonomy.term_id = $wpdb->terms.term_id and 
	$wpdb->terms.term_id = ".$thistagid." and
	$wpdb->term_relationships.object_id = $wpdb->posts.id and
	$wpdb->posts.post_status = 'publish'
						";
	 						
 				} else {
	 				$tagquery=
					"select post_title,object_id from $wpdb->term_relationships , $wpdb->term_taxonomy, $wpdb->terms, $wpdb->posts
	where $wpdb->term_taxonomy.term_taxonomy_id = $wpdb->term_relationships.term_taxonomy_id AND
	$wpdb->term_taxonomy.term_id = $wpdb->terms.term_id and 
	$wpdb->terms.term_id = ".$thistagid." and
	$wpdb->term_relationships.object_id = $wpdb->posts.id and
	$wpdb->posts.post_status = 'publish' and
	$wpdb->posts.post_type='" . $pt . " '";
				}
				$testtag = $wpdb->get_results($tagquery);
				if (count($testtag) > 0){
					$postsfound=true;
					$carray = array();
					foreach ($testtag as $tt){
						$carray[]=$tt->object_id;
					}
				} else { $postsfound=false;
					echo "<h2>Nothing on the intranet with this tag.</h2>";
				}
				$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
				if ($pt =='task'): // tasks sorted alphabetically
					$tagged = new WP_Query(array(
		 			'post_type'=>array("task"),
		 			'post__in'=>$carray,
		 			'paged'=>$paged,
		 			'posts_per_page'=>10,
		 			'orderby'=>'name',
		 			'order'=>'ASC',
		 			)); 
				elseif ($pt =='event'): // tasks sorted alphabetically
					$tagged = new WP_Query(array(
		 			'post_type'=>array("event"),
		 			'post__in'=>$carray,
		 			'paged'=>$paged,
		 			'posts_per_page'=>10,
		 			'orderby'=>'meta_value',
		 			'order'=>'ASC',
		 			'meta_key' => 'event_start_date',
		 			)); 
				else: // everything else sorted by date
					$tagged = new WP_Query(array(
		 			'post_type'=>array("task","vacancy","project","news","event","blog"),
		 			'post__in'=>$carray,
		 			'paged'=>$paged,
		 			'posts_per_page'=>10,
		 			'orderby'=>'date',
		 			'order'=>'DESC'
		 			));
	 			endif;
				$counter = 0;	
	
	 			while ($tagged->have_posts() && $postsfound ) {
					$tagged->the_post();
					$post_type = ucwords($post->post_type);
					$post_cat = get_the_category();
					$image_url = get_the_post_thumbnail($id, 'thumbnail', array('class' => 'alignright'));

					echo "<div class='media'>" ;
					$contexturl=$post->guid;
					$context='';
					$titlecontext='';
					if ($post_type=='Post_tag') { 
						$icon = "tag"; 
					}	
					if ($post_type=='Task'){
						$contexturl = "/tasks/";
						$taskpod = $post->post_parent; 
						if ( !$taskpod ){		
							$context = "task";
							$icon = "question-sign";
							$title_context='';
						} else {
							$context = "guide";
							$icon = "book";
							$taskparent=get_post($taskpod);
							$title_context='';
							if ($taskparent){
								$title_context=" (".govintranetpress_custom_title($taskparent->post_title).")";  
							}
						}			
					}
					if ($post_type=='Project'){
						$context = "project";
						$contexturl = "/about/projects/";
						$icon = "road";
						$projparent=$post->post_parent;
						$title_context='';	
						if ($projparent){
							$projparent = get_post($projparent);
							$title_context=" (".govintranetpress_custom_title($projparent->post_title).")";
						}			
					}
					if ($post_type=='News'){
							$context = "news";
							$contexturl = "/news/";
							$icon = "star-empty";			
					}
					if ($post_type=='Vacancy'){
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
						<a href="<?php echo get_the_permalink(get_the_id()); ?>" title="<?php printf( esc_attr__( '%s %s', 'govintranet' ), the_title_attribute( 'echo=0' ), " (" . $context . ")" ); ?>" rel="bookmark"><?php echo get_the_title($post->ID); echo "</a> <small>".$title_context."</small>"; ?><?php echo $ext_icon; ?>
						</h3>
					
					<?php
					endif;
	

					echo "<a href='";
					the_permalink();
					echo "'><div class='hidden-xs'>".$image_url."</div></a>" ;
				
					echo "<div class='media-body'>";
					
					if (($post_type=="Task")){
						echo "<p>";
						echo '<span class="listglyph">'.ucfirst($context).'</span>&nbsp;&nbsp;';
						foreach($post_cat as $cat){
							if ($cat->term_id != 1 ){
								echo "<span class='listglyph'><span class='dashicons dashicons-category gb".$cat->term_id."'></span><a href='".get_term_link($cat->slug,$cat->taxonomy)."'>".$cat->name;
								echo "</a></span>&nbsp;";							}
							}
						echo "</p>";
					} elseif (($post_type=="News" || $post_type=="Blog")){
						echo "<div><p>";
						echo '<span class="listglyph">'.ucfirst($context).'</span>&nbsp;&nbsp;';
						foreach($post_cat as $cat){
							if ($cat->term_id != 1 ){
								echo "<span class='listglyph'><span class='dashicons dashicons-category gb".$cat->term_id."'></span><a href='".get_term_link($cat->slug,$cat->taxonomy)."'>".$cat->name;
							}
						}
					   $thisdate= $post->post_date;
					   $thisdate=date("j M Y",strtotime($thisdate));
					   echo "<span class='listglyph'>".$thisdate."</span> ";
					   echo "</p></div>";
					} elseif ($post_type=="Event" ){ 
						echo "<div><p>";
						$thisdate= get_post_meta($post->ID, 'event_start_date', true);
						$thisdate=date("j M Y",strtotime($thisdate));
						echo '<span class="listglyph">'.ucfirst($context).'&nbsp;'.$thisdate.'</span>&nbsp;&nbsp;';
						foreach($post_cat as $cat){
							if ($cat->term_id != 1 ){
								echo "<span class='listglyph'><span class='dashicons dashicons-category gb".$cat->term_id."'></span><a href='".get_term_link($cat->slug,$cat->taxonomy)."'>".$cat->name;
							}
						}
						echo "</p></div>";
					} else {
						echo "<div><p>";
						echo '<span class="listglyph">'.ucfirst($context).'</span>&nbsp;&nbsp;';
						$thisdate= $post->post_modified;
						$thisdate=date("j M Y",strtotime($thisdate));
						echo "<span class='listglyph'>Updated ".$thisdate."</span> ";
						foreach($post_cat as $cat){
							if ($cat->term_id != 1 ){
								echo "<span class='listglyph'><span class='dashicons dashicons-category gb".$cat->term_id."'></span><a href='".get_term_link($cat->slug,$cat->taxonomy)."'>".$cat->name;
							}
						}
						echo "</p></div>";
					}
	
					?>
					</div>
				<?php	
			
					if ($post_type=='Post_tag') { 
						echo "All intranet pages tagged with \"". get_the_title() ."\""; 
					} else if ($post_type=='Category') { 
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

				}
				if (  $tagged->max_num_pages > 1 && $postsfound ) : ?>
				<?php if (function_exists('wp_pagenavi')) : ?>
					<?php wp_pagenavi(array('query' => $tagged)); ?>
					<?php else : ?>
					<?php next_posts_link('&larr; Older items', $tagged->max_num_pages); ?>
					<?php previous_posts_link('Newer items &rarr;', $tagged->max_num_pages); ?>						
				<?php endif; ?>
				<?php endif; 
				wp_reset_query();								
				?>

				</div>

				<div class="col-lg-4 col-lg-offset-1 col-md-4 col-sm-12">
					<div class="widget-box list">
						<h3 class="widget-title">
						<?php if (!$posttype){
							echo "Filter";
						} else {
							echo "More";
						}
						?>
						</h3>
						<ul>
						<?php					
						$t=get_tag($thistagid);
						$t=$thistagslug;
						if ($posttype == 'task'){
							$landingpage = get_option('options_module_tasks_page'); 
							if ( !$landingpage ):
								$landingpage_link_text = 'tasks and guides';
								$landingpage = site_url().'/how-do-i/';
							else:
								$landingpage_link_text = get_the_title( $landingpage[0] );
								$landingpage = get_permalink( $landingpage[0] );
							endif;
							echo "<li><a href='".$landingpage."'>Go to ".$landingpage_link_text."</a></li>";
						}
						if ($posttype == 'project'){
							$landingpage = get_option('options_module_projects_page'); 
							if ( !$landingpage ):
								$landingpage_link_text = 'projects';
								$landingpage = site_url().'/projects/';
							else:
								$landingpage_link_text = get_the_title( $landingpage[0] );
								$landingpage = get_permalink( $landingpage[0] );
							endif;
							echo "<li><a href='".$landingpage."'>Go to ".$landingpage_link_text."</a></li>";
						}
						if ($posttype == 'event'){
							$landingpage = get_option('options_module_events_page'); 
							if ( !$landingpage ):
								$landingpage_link_text = 'events';
								$landingpage = site_url().'/events/';
							else:
								$landingpage_link_text = get_the_title( $landingpage[0] );
								$landingpage = get_permalink( $landingpage[0] );
							endif;
							echo "<li><a href='".$landingpage."'>Go to ".$landingpage_link_text."</a></li>";
						}
						if ($posttype == 'blog'){
							$landingpage = get_option('options_module_blog_page'); 
							if ( !$landingpage ):
								$landingpage_link_text = 'blogposts';
								$landingpage = site_url().'/blogs/';
							else:
								$landingpage_link_text = get_the_title( $landingpage[0] );
								$landingpage = get_permalink( $landingpage[0] );
							endif;
							echo "<li><a href='".$landingpage."'>Go to ".$landingpage_link_text."</a></li>";
						}
						if ($posttype == 'news'){
							$landingpage = get_option('options_module_news_page'); 
							if ( !$landingpage ):
								$landingpage_link_text = 'news';
								$landingpage = site_url().'/newspage/';
							else:
								$landingpage_link_text = get_the_title( $landingpage[0] );
								$landingpage = get_permalink( $landingpage[0] );
							endif;
							echo "<li><a href='".$landingpage."'>Go to ".$landingpage_link_text."</a></li>";
						}
						if ($posttype == 'vacancy'){
							$landingpage = get_option('options_module_vacancies_page'); 
							if ( !$landingpage ):
								$landingpage_link_text = 'vacancies';
								$landingpage = site_url().'/vacancies/';
							else:
								$landingpage_link_text = get_the_title( $landingpage[0] );
								$landingpage = get_permalink( $landingpage[0] );
							endif;
							echo "<li><a href='".$landingpage."'>Go to ".$landingpage_link_text."</a></li>";
						}
						if ($posttype != ''){
						echo "<li><a href='".site_url()."/tag/".$thistagslug."'>";
							printf( __( '<strong>Everything</strong> tagged: %s', 'govintranet' ), '' . $thistag . '' );
						echo "</a></li>";		
						}
						if ($posttype != 'task'){
							$tagquery=
								"select count(distinct $wpdb->posts.id) as numtags from $wpdb->posts
								 join $wpdb->term_relationships on $wpdb->term_relationships.object_id = $wpdb->posts.id
								 join $wpdb->term_taxonomy on $wpdb->term_taxonomy.term_taxonomy_id = $wpdb->term_relationships.term_taxonomy_id
								 join $wpdb->terms on $wpdb->terms.term_id = $wpdb->term_taxonomy.term_id
								where $wpdb->terms.slug='".$t."' AND
								$wpdb->posts.post_type='task' AND
								$wpdb->posts.post_status = 'publish'
							";
							$testtag = $wpdb->get_results($tagquery);
							if ($testtag[0]->numtags > 0){
								echo "<li><a href='".site_url()."/tag/".$thistagslug."/?type=task'><strong>Tasks and guides</strong> tagged: ".$thistag."</a></li>";		
							}
						}
						if ($posttype != 'project'){
							$tagquery=
							"select count(distinct $wpdb->posts.id) as numtags from $wpdb->posts
							 join $wpdb->term_relationships on $wpdb->term_relationships.object_id = $wpdb->posts.id
							 join $wpdb->term_taxonomy on $wpdb->term_taxonomy.term_taxonomy_id = $wpdb->term_relationships.term_taxonomy_id
							 join $wpdb->terms on $wpdb->terms.term_id = $wpdb->term_taxonomy.term_id
							where $wpdb->terms.slug='".$t."' AND
							$wpdb->posts.post_type='project' AND
							$wpdb->posts.post_status = 'publish'
								";
							$testtag = $wpdb->get_results($tagquery);
							if ($testtag[0]->numtags > 0){
								echo "<li><a href='".site_url()."/tag/".$thistagslug."/?type=project'><strong>Projects</strong> tagged: ".$thistag."</a></li>";		
							}
						}
						if ($posttype != 'vacancy'){
							$tagquery=
							"select count(distinct $wpdb->posts.id) as numtags from $wpdb->posts
							 join $wpdb->term_relationships on $wpdb->term_relationships.object_id = $wpdb->posts.id
							 join $wpdb->term_taxonomy on $wpdb->term_taxonomy.term_taxonomy_id = $wpdb->term_relationships.term_taxonomy_id
							 join $wpdb->terms on $wpdb->terms.term_id = $wpdb->term_taxonomy.term_id
							where $wpdb->terms.slug='".$t."' AND
							$wpdb->posts.post_type='vacancy' AND
							$wpdb->posts.post_status = 'publish'					";
							$testtag = $wpdb->get_results($tagquery);
							if ($testtag[0]->numtags > 0){
								echo "<li><a href='".site_url()."/tag/".$thistagslug."/?type=vacancy'><strong>Job vacancies</strong> tagged: ".$thistag."</a></li>";		
							}
						}
						if ($posttype != 'news'){
							$tagquery=
							"select count(distinct $wpdb->posts.id) as numtags from $wpdb->posts
							 join $wpdb->term_relationships on $wpdb->term_relationships.object_id = $wpdb->posts.id
							 join $wpdb->term_taxonomy on $wpdb->term_taxonomy.term_taxonomy_id = $wpdb->term_relationships.term_taxonomy_id
							 join $wpdb->terms on $wpdb->terms.term_id = $wpdb->term_taxonomy.term_id
							where $wpdb->terms.slug='".$t."' AND
							$wpdb->posts.post_type='news' AND
							$wpdb->posts.post_status = 'publish'
								";
							$testtag = $wpdb->get_results($tagquery);
							if ($testtag[0]->numtags > 0){
								echo "<li><a href='".site_url()."/tag/".$thistagslug."/?type=news'><strong>News</strong> tagged: ".$thistag."</a></li>";		
							}
						}
						if ($posttype != 'blog'){
							$tagquery=
							"select count(distinct $wpdb->posts.id) as numtags from $wpdb->posts
							 join $wpdb->term_relationships on $wpdb->term_relationships.object_id = $wpdb->posts.id
							 join $wpdb->term_taxonomy on $wpdb->term_taxonomy.term_taxonomy_id = $wpdb->term_relationships.term_taxonomy_id
							 join $wpdb->terms on $wpdb->terms.term_id = $wpdb->term_taxonomy.term_id
							where $wpdb->terms.slug='".$t."' AND
							$wpdb->posts.post_type='blog' AND
							$wpdb->posts.post_status = 'publish'
								";
							$testtag = $wpdb->get_results($tagquery);
							if ($testtag[0]->numtags > 0){
								echo "<li><a href='".site_url()."/tag/".$thistagslug."/?type=blog'><strong>Blog posts</strong> tagged: ".$thistag."</a></li>";		
							}
						}
						if ($posttype != 'event'){
							$tagquery=
							"select count(distinct $wpdb->posts.id) as numtags from $wpdb->posts
							 join $wpdb->term_relationships on $wpdb->term_relationships.object_id = $wpdb->posts.id
							 join $wpdb->term_taxonomy on $wpdb->term_taxonomy.term_taxonomy_id = $wpdb->term_relationships.term_taxonomy_id
							 join $wpdb->terms on $wpdb->terms.term_id = $wpdb->term_taxonomy.term_id
							where $wpdb->terms.slug='".$t."' AND
							$wpdb->posts.post_type='event' AND
							$wpdb->posts.post_status = 'publish'
								";
							$testtag = $wpdb->get_results($tagquery);
							if ($testtag[0]->numtags > 0){
								echo "<li><a href='".site_url()."/tag/".$thistagslug."/?type=event'><strong>Events</strong> tagged: ".$thistag."</a></li>";		
							}
						}
						?>
					</ul>
				</div>					
			</div>
<?php endif; ?>

<?php get_footer(); ?>
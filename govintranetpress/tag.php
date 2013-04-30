<?php
/**
 * The template for displaying Tag Archive pages.
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */

get_header(); ?>

				<div class="row white">

					
					<div class="ninecol white" id="content">
							
					<div class="content-wrapper">
				<h1><?php
				$pt=$_GET['pt'];
				if ($_GET['pt'] == 'task'){

					printf( __( 'Tasks/guides tagged: %s', 'twentyten' ), '' . single_tag_title( '', false ) . '' );
				}
				elseif ($_GET['pt'] == 'projects'){
					printf( __( 'Projects tagged: %s', 'twentyten' ), '' . single_tag_title( '', false ) . '' );
				}
				elseif ($_GET['pt'] == 'vacancies'){
					printf( __( 'Job vacancies tagged: %s', 'twentyten' ), '' . single_tag_title( '', false ) . '' );
				}
				elseif ($_GET['pt'] == 'news'){
					printf( __( 'News tagged: %s', 'twentyten' ), '' . single_tag_title( '', false ) . '' );
				}
				else
				{
					printf( __( 'Everything tagged: %s', 'twentyten' ), '' . single_tag_title( '', false ) . '' );
				}
				?></h1>

<?php
/* Run the loop for the tag archive to output the posts
 * If you want to overload this in a child theme then include a file
 * called loop-tag.php and that will be used instead.
 */
 
 query_posts($query_string. '&orderby=title&order=asc');
 get_template_part( 'loop', 'tag' );
?>
					</div>
					</div>

					<div class="threecol last" id='sidebar'>
					<div id='related'><h3 class="widget-title">More</h3>
					<ul>
					<?php					
				$t=get_tag($tag_id);
				$t=$t->slug;
				if ($_GET['pt'] == 'task'){
				echo "<li><a href='/tasks/'>All tasks and guides</a></li>";
				}
				if ($_GET['pt'] == 'projects'){
				echo "<li><a href='/about/projects/'>All projects</a></li>";
				}
				if ($_GET['pt'] == 'vacancies'){
				echo "<li><a href='/about/vacancies/'>All job vacancies</a></li>";
				}
				if ($_GET['pt'] != ''){
				echo "<li><a href='".get_tag_link($tag_id)."'>";
					printf( __( 'Everything tagged: %s', 'twentyten' ), '' . single_tag_title( '', false ) . '' );
				echo "</a></li>";		
				}
				if ($_GET['pt'] != 'task'){
				$tagquery=
				"select count(distinct id) as numtags from wp_posts, wp_terms, wp_term_taxonomy, wp_term_relationships
					where wp_terms.slug='".$t."' AND
					wp_terms.term_id = wp_term_taxonomy.term_id AND
					wp_term_taxonomy.term_id = wp_term_relationships.term_taxonomy_id AND
					wp_term_relationships.object_id = wp_posts.id AND
					wp_posts.post_type='task' AND
					wp_posts.post_status = 'publish'
					";
				$testtag = $wpdb->get_results($tagquery);
				if ($testtag[0]->numtags > 0){
				echo "<li><a href='".get_tag_link($tag_id)."?pt=task'>";
					printf( __( 'Tasks and guides tagged: %s', 'twentyten' ), '' . single_tag_title( '', false ) . '' );
				echo "</a></li>";		
				}
				}
				if ($_GET['pt'] != 'projects'){
				$tagquery=
				"select count(distinct id) as numtags from wp_posts, wp_terms, wp_term_taxonomy, wp_term_relationships
					where wp_terms.slug='".$t."' AND
					wp_terms.term_id = wp_term_taxonomy.term_id AND
					wp_term_taxonomy.term_id = wp_term_relationships.term_taxonomy_id AND
					wp_term_relationships.object_id = wp_posts.id AND
					wp_posts.post_type='projects' AND
					wp_posts.post_status = 'publish'
					";
				$testtag = $wpdb->get_results($tagquery);
				if ($testtag[0]->numtags > 0){
				echo "<li><a href='".get_tag_link($tag_id)."?pt=projects'>";
					printf( __( 'Projects tagged: %s', 'twentyten' ), '' . single_tag_title( '', false ) . '' );
				echo "</a></li>";		
				}
				}
				if ($_GET['pt'] != 'vacancies'){
				$tagquery=
				"select count(distinct id) as numtags from wp_posts, wp_terms, wp_term_taxonomy, wp_term_relationships
					where wp_terms.slug='".$t."' AND
					wp_terms.term_id = wp_term_taxonomy.term_id AND
					wp_term_taxonomy.term_id = wp_term_relationships.term_taxonomy_id AND
					wp_term_relationships.object_id = wp_posts.id AND
					wp_posts.post_type='vacancies' AND
					wp_posts.post_status = 'publish'
					";
				$testtag = $wpdb->get_results($tagquery);
				if ($testtag[0]->numtags > 0){
				
				echo "<li><a href='".get_tag_link($tag_id)."?pt=vacancies'>";
					printf( __( 'Job vacancies tagged: %s', 'twentyten' ), '' . single_tag_title( '', false ) . '' );
				echo "</a></li>";		
				}
				}
				if ($_GET['pt'] != 'news'){
				$tagquery=
				"select count(distinct id) as numtags from wp_posts, wp_terms, wp_term_taxonomy, wp_term_relationships
					where wp_terms.slug='".$t."' AND
					wp_terms.term_id = wp_term_taxonomy.term_id AND
					wp_term_taxonomy.term_id = wp_term_relationships.term_taxonomy_id AND
					wp_term_relationships.object_id = wp_posts.id AND
					wp_posts.post_type='news' AND
					wp_posts.post_status = 'publish'
					";
				$testtag = $wpdb->get_results($tagquery);
				if ($testtag[0]->numtags > 0){
				
				echo "<li><a href='".get_tag_link($tag_id)."?pt=news'>";
					printf( __( 'News tagged: %s', 'twentyten' ), '' . single_tag_title( '', false ) . '' );
				echo "</a></li>";		
				}
				}
							?>
					</ul>
					</div>					
						<?php get_sidebar('inside'); ?>
					</div>

				</div>
<?php get_footer(); ?>
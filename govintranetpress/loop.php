<?php
/**
 * The loop that displays posts.
 *
 * The loop displays the posts and the post content.  See
 * http://codex.wordpress.org/The_Loop to understand it and
 * http://codex.wordpress.org/Template_Tags to understand
 * the tags used in it.
 *
 * This can be overridden in child themes with loop.php or
 * loop-template.php, where 'template' is the loop context
 * requested by a template. For example, loop-index.php would
 * be used if it exists and we ask for the loop with:
 * <code>get_template_part( 'loop', 'index' );</code>
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */
?>

<?php /* If there are no posts to display, such as an empty archive page */ ?>

<?php 
		$pageslug = pods_url_variable(0);

		if ( ! have_posts() ) { 
		
				echo "<h1>";
				_e( 'Not found', 'twentyten' );
				echo "</h1>";
				echo "<p>";
				_e( 'Apologies, there\'s nothing to show.', 'govintranetpress' );
				echo "</p>";
				get_search_form(); 
		};



		?>
		<div id="infinite-scroll">
		
<?php 



	while ( have_posts() ) : the_post(); 
	
	$post_type = ucwords($post->post_type);
	$post_cat = get_the_category();
	echo "<hr/>";
	$title_context='';		
	$image_url = get_the_post_thumbnail($ID, 'thumbnail', array('class' => 'alignright'));
	echo "<div class='newsitem'><a href='";
	the_permalink();
	echo "'>".$image_url."</a>" ;
?>

	<h3<?php if ($post_type=='Post_tag') { echo " class='posttag'"; }?>
	<?php 		
	if ($post_type=='Task'){
		$context = "task";
		$taskpod = new Pod ('task' , $post->ID); 
		if ( $taskpod->get_field('page_type') == 'Task'){		echo " class='taglisting task'";
				} else {
		echo " class='taglisting guide'";	
			$context = "guide";
		}
		$taskparent=$taskpod->get_field('parent_guide');
		if ($taskparent){
			$parent_guide_id = $taskparent[0]['ID']; 		
			$taskparent = get_post($parent_guide_id);
			$title_context=" (".govintranetpress_custom_title($taskparent->post_title).")";
		}			
	}
	if ($post_type=='Projects'){
		$context = "project";
		$taskpod = new Pod ('projects' , $post->ID); 
		$projparent=$taskpod->get_field('parent_project');
		if ($projparent){
			$parent_guide_id = $projparent[0]['ID']; 		
			$projparent = get_post($parent_guide_id);
			$title_context=" (".govintranetpress_custom_title($projparent->post_title).")";
		}			
		echo " class='taglisting project'";
	}
	if ($post_type=='News'){
		echo " class='taglisting news'";
			$context = "news";
	}
	if ($post_type=='Vacancies'){
		echo " class='taglisting vacancies'";
			$context = "job vacancy";
	}
	if ($post_type=='Blog'){
		echo " class='taglisting news'";
			$context = "blog";
	}
				?>
	><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( '%s %s', 'govintranetpress' ), the_title_attribute( 'echo=0' ), " (" . $context . ")" ); ?>" rel="bookmark"><?php the_title(); echo $title_context; ?></a></h3>
	<?php 

	if (($post_type=="Task" && $pageslug!="category")){
		$taskpod = new Pod ('task' , $post->ID); 
		if ( $taskpod->get_field('page_type') == 'Task'){		echo "<p class='taglisting task'>";
				} else {
		echo "<p class='taglisting guide'>";	
		}
		foreach($post_cat as $cat){
			if ($cat->name != 'Uncategorized' ){
			echo "<span class='brdall b".$cat->term_id."'>".$cat->name;
			}
			echo "</span>&nbsp;";
			}
		echo "</p>";
	}

	if ($post_type=="News" && $pageslug!="category"){
		echo "<div class='taglisting {$post->post_type}'>";
		foreach($post_cat as $cat){
			if ($cat->name != 'Uncategorized' ){
				echo "<span class='brdall b".$cat->term_id."'>".$cat->name;
				echo "</span>&nbsp;";
			}
		}
		echo "</div>";
	}

	?>
	<?php if ( is_archive() || is_search() ) : // Only display excerpts for archives and search. ?>
	<?php	
	   if ($post_type=="News" || $post_type == "Topic" || $post_type == "Reply") {
		   $thisdate= $post->post_date;
		   $thisdate=date("j M Y",strtotime($thisdate));
		   echo "<p class='news_date'>".$thisdate."</p>";
		}

		if ($post_type=='Post_tag') { 
			echo "All intranet pages tagged with \"". get_the_title() ."\""; 
		}
		else if ($post_type=='Category') 
		{ 
			echo "All intranet pages categorised as \"". get_the_title() ."\""; 
		}

		the_excerpt(); 
 		if (function_exists('wp_gdsr_render_article')){
	 		wp_gdsr_render_article(44, true, 'soft', 16);
		}
		
			?>
			
	<?php else : ?>
			<?php the_content( __( 'Continue reading &rarr;', 'govintranetpress' ) ); ?>
			<?php wp_link_pages( array( 'before' => '' . __( 'Pages:', 'govintranetpress' ), 'after' => '' ) ); ?>
	<?php endif; ?>
	

	</div><div class="clearfix"></div>


	
	<?php comments_template( '', true ); ?>

<?php endwhile; // End the loop. Whew. ?>
		</div>
<?php if (  $wp_query->max_num_pages > 1 ) : ?>
	<?php if (function_exists(wp_pagenavi)) : ?>
		<?php wp_pagenavi(); ?>
	<?php else : ?>
		<?php next_posts_link('&larr; Older items', $wp_query->max_num_pages); ?>
		<?php previous_posts_link('Newer items &rarr;', $wp_query->max_num_pages); ?>						
		
	<?php endif; ?>
<?php endif; ?>

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
			_e( 'There\'s nothing to show.', 'govintranetpress' );
			echo "</p>";
			get_search_form(); 
	};


	while ( have_posts() ) : the_post(); //echo $post->post_type;

	$post_type = ucwords($post->post_type);
	$post_cat = get_the_category();
	$title_context='';		
	$context='';
	$icon='';
	$image_url = get_the_post_thumbnail($ID, 'thumbnail', array('class' => 'alignright'));
	if ($post_type=='User'){
		global $foundstaff;
		$foundstaff++;
		$image_url = get_avatar($post->user_id);
		$image_url = str_replace('avatar-96 ', 'avatar-96 img img-responsive img-circle ' , $image_url);
		$userurl = get_author_posts_url( $post->user_id); 
		$gis = "general_intranet_forum_support";
		$forumsupport = get_option($gis);
	   if ($forumsupport){
			if (function_exists('bp_activity_screen_index')){ // if using BuddyPress - link to the members page
				$userurl=str_replace('/author', '/members', $userurl); }
			elseif (function_exists('bbp_get_displayed_user_field')){ // if using bbPress - link to the staff page
				$userurl=str_replace('/author', '/staff', $userurl);
			}

		} 
	} else {
		$userurl = get_permalink();
	}
	$contexturl=$post->guid;
	$context='';
	if ($post_type=='Post_tag') { 
		$icon = "tag"; 
	}	
	if ($post_type=='Task'){
		$contexturl = "/tasks/";
		$taskpod = new Pod ('task' , $post->ID); 
		if ( $taskpod->get_field('page_type') == 'Task'){		
			$context = "task";
			$icon = "question-sign";
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
			$context = get_user_meta($post->user_id,'user_job_title',true);
			if ($context=='') $context="staff";
			$contexturl = "/staff/";
			$icon = "user";			
	}
	if ($post_type=='Team'){
			$context = "team";
			$icon = "list-alt";			
	}

	if ($post_type=='Page'){
			$context = "page";
			$icon = "file";
	}

	if ($post_type=='Forum'||$post_type=='Reply'||$post_type=='Topic'){
			$context = "forum";
			$icon = "comment";
	}

	if ($post_type=='Attachment'): 
		$context='document download';
		$icon = "download";			
	?>
		<h3 class='postlist'>				
		<a href="<?php echo wp_get_attachment_url( $post->id ); ?>" title="<?php printf( esc_attr__( '%s %s', 'govintranetpress' ), the_title_attribute( 'echo=0' ), " (" . $context . ")" ); ?>" rel="bookmark"><?php the_title();  ?></a></h3>
	
	
	<?php 
	elseif ($post_type=='User'): 
	
	?>			<div class="row"><div class="col-lg-12">
	
		<h3 class='postlist'>				
		<a href="<?php echo $userurl; ?>" title="<?php printf( esc_attr__( '%s %s', 'govintranetpress' ), the_title_attribute( 'echo=0' ), " (" . $context . ")" ); ?>" rel="bookmark"><?php the_title();  ?></a></h3>
	
	<?php 
	else: 
		echo "<div class='media'>" ;

	?>
	
		<h3 class='postlist'>				
		<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( '%s %s', 'govintranetpress' ), the_title_attribute( 'echo=0' ), " (" . $context . ")" ); ?>" rel="bookmark"><?php the_title(); echo "</a> <small>".$title_context."</small>"; ?></h3>
	
	<?php
	endif;
	
	echo "<a href='";
	echo $userurl;
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
	}
	elseif (($post_type=="News" || $post_type =="Blog" || $post_type=='Forum' || $post_type=='Topic' || $post_type=='Reply' ) && $pageslug!="category"){
		echo "<div><p>";
		echo '<span class="listglyph"><i class="glyphicon glyphicon-'.$icon.'"></i>&nbsp;'.ucfirst($context).'</span>&nbsp;&nbsp;';
		foreach($post_cat as $cat){
			if ($cat->name != 'Uncategorized' ){
				echo "<span class='listglyph'><span class='glyphicon glyphicon-stop gb".$cat->term_id."'></span>&nbsp;".$cat->name;
			echo "</span>&nbsp;&nbsp;";
			}
		}
		if ( is_archive() || is_search() || is_author() ){
			   $thisdate= $post->post_date;
			   $thisdate=date("j M Y",strtotime($thisdate));
			   echo "<span class='listglyph'><i class='glyphicon glyphicon-calendar'></i> ".$thisdate."</span> ";
		}
		if ($post_type=="Blog"){
			$image_url = get_avatar($post->post_author,32);
			$image_url = str_replace('avatar ', 'avatar32 ' , $image_url);
			echo "&nbsp;";
			echo $image_url;
			$auth = get_the_author();
			echo "<span class='listglyph'>&nbsp;".$auth."</span>";
		}
		echo "</p></div>";
	}
	elseif ($post_type=="Team" ){
		echo "<div><p>";
		echo '<span class="listglyph"><i class="glyphicon glyphicon-'.$icon.'"></i>&nbsp;'.ucfirst($context).'</span>&nbsp;&nbsp;';
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
		foreach($post_cat as $cat){
			if ($cat->name != 'Uncategorized' ){
				echo "<span class='listglyph'><span class='glyphicon glyphicon-stop gb".$cat->term_id."'></span>&nbsp;".$cat->name;
			echo "</span>&nbsp;&nbsp;";
			}
		}
		echo "</p></div>";
		
	}

	if ( is_archive() || is_search() ) : // Only display excerpts for archives and search. 

		if ($post_type=='Post_tag') { 
			echo "All intranet pages tagged with \"". get_the_title() ."\""; 
		}
		else if ($post_type=='Category') 
		{ 
			echo "All intranet pages categorised as \"". get_the_title() ."\""; 
		}


		if ($post_type!='User' ){
			the_excerpt(); 
		}
		
		if ($post_type=='User'){
			$user_info = get_userdata($post->user_id);?>
			<?php if ( get_user_meta($post->user_id ,'user_telephone',true )) : ?>

				<p><i class="glyphicon glyphicon-earphone"></i> <a href="tel:<?php echo str_replace(" ", "", get_user_meta($post->user_id ,'user_telephone',true )) ; ?>"><?php echo get_user_meta($post->user_id ,'user_telephone',true ); ?></a></p>

			<?php endif; ?>

			<?php if ( get_user_meta($post->user_id ,'user_mobile',true ) ) : ?>

				<p><i class="glyphicon glyphicon-phone"></i> <a href="tel:<?php echo str_replace(" ", "", get_user_meta($post->user_id ,'user_mobile',true )) ; ?>"><?php echo get_user_meta($post->user_id ,'user_mobile',true ); ?></a></p>

			<?php endif; ?>

				<p><a href="mailto:<?php echo $user_info->user_email; ?>">Email <?php echo $user_info->user_email; ?></a></p>
			</div>
			<br class="clearfix">
			<?php
		}
		
		//for rating stories
 		if (function_exists('wp_gdsr_render_article')){
	 		wp_gdsr_render_article(44, true, 'soft', 16);
		}
		
			?>
			
	<?php else : 
		if ($post_type=='Blog'){
			the_excerpt();
		} else {
		
	?>
			<?php the_content( __( 'Continue reading &rarr;', 'govintranetpress' ) ); ?>
			<?php wp_link_pages( array( 'before' => '' . __( 'Pages:', 'govintranetpress' ), 'after' => '' ) ); ?>
	<?php }
	endif; ?>
	

	</div></div>


	
	<?php comments_template( '', true ); ?>

<?php endwhile; // End the loop. Whew. ?>
<hr>
<?php if (  $wp_query->max_num_pages > 1  ) : ?>
	<?php if (function_exists(wp_pagenavi)) : ?>
		<?php wp_pagenavi(); ?>
	<?php else : ?>
		<?php next_posts_link('&larr; Older items', $wp_query->max_num_pages); ?>
		<?php previous_posts_link('Newer items &rarr;', $wp_query->max_num_pages); ?>						
		
	<?php endif; ?>
<?php endif; ?>

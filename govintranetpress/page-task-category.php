<?php
/* Template name: Task category page */

$catquery = @explode("=",$query_string);

get_header(); 

$cat_name = $_GET['cat'];
$catpod = new Pod ('category' , $cat_name);
$catname = $catpod->get_field('name');				
$catid = $catpod->get_field('id');		
$catslug = $catpod->get_field('slug');		
$catdesc = $catpod->get_field('category_page_description');				
?>

					<div class="col-lg-12 white ">
						<div class="row">
							<div class='breadcrumbs'>
								<a title="Go to Home." href="<?php echo site_url(); ?>/" class="site-home">Home</a> &raquo; 
								<a title="Go to How do I?" href="<?php echo site_url(); ?>/how-do-i/">How do I?</a> &raquo; <?php echo $catname; ?>
							</div>
							<div class="col-lg-8 col-md-8 col-sm-12 notop">
						<?php		
								echo "<h1 class='h1_" . $catid . "'>".$catname."</h1>";
								?>
								<?php echo $catdesc; ?>
									<div class="well well-sm">
									<form class="form-horizontal" role="form" method="get" id="sbc-search" action="/">
										<label for="sbc-s"><?php echo $catname;?></label>
										<div class="form-group input-md">
											<input type="text" value="" name="s" id="sbc-s" placeholder="How do I..." class="multi-cat form-control input-md" onblur="if (this.value == '') {this.value = '';}"  onfocus="if (this.value == '') {this.value = '';}" />
										</div>
										<div class="form-group input-md">
											<button type="submit" class="btn btn-primary input-md">Search</button>
										</div>
										<input type="hidden" value="<?php echo $catslug; ?> " name = "cat" />
										<input type="hidden" value="task" name = "post_type" />
									</form>
									</div>
<script>
jQuery(function(){
jQuery("#sbc-s").focus();
});
</script>
						
<?php
/* Run the loop for the category page to output the posts.
 * If you want to overload this in a child theme then include a file
 * called loop-category.php and that will be used instead.
 */

		$taskitems = new WP_Query(
				array (
		'post_type'=>'task',
		'posts_per_page'=>-1,
		'cat'=>$catid,
		'posts_per_page' => 25,
		'paged' => $paged,												
		'orderby'=>'name',
		'order'=>'ASC',
		'meta_key'=>'page_type',
		'meta_value'=>array('Task','Guide header')
		)
		);
		if ($taskitems->post_count==0){
			echo "<p>Nothing to show.</p>";
		}
		while ($taskitems->have_posts()) {
			$taskitems->the_post();
			$image_url = get_the_post_thumbnail($ID, 'thumbnail', array('class' => 'alignright'));
			echo "<div class='newsitem'>".$image_url ;
			echo "<hr>";
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


			?>
<h3>				
	<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( '%s %s', 'govintranetpress' ), the_title_attribute( 'echo=0' ), " (" . $context . ")" ); ?>" rel="bookmark"><?php the_title(); echo $title_context; ?></a>&nbsp;<small><i class="glyphicon glyphicon-<?php echo $icon; ?>"></i>&nbsp;<?php echo ucfirst($context); ?></small></h3>

<?php
			the_excerpt(); 
			echo "</div>";
		 }
?>
<?php 	if (  $taskitems->max_num_pages > 1 ) : ?>
<?php 		if (function_exists(wp_pagenavi)) : ?>
			<?php wp_pagenavi(array('query' => $taskitems)); ?>
			<?php else : ?>
			<?php next_posts_link('&larr; Older items', $taskitems->max_num_pages); ?>
			<?php previous_posts_link('Newer items &rarr;', $taskitems->max_num_pages); ?>						
	<?php 
			endif; 
		?>
	<?php 
		endif; 
		wp_reset_query();								
?>
	</div>

<div class="col-lg-4 col-md-4 col-sm-12" id='sidebar'>

	<div class='widget-box'>
		<h3 class='widget-title'>Search by tag</h3>
	<?php 
		$slug = $_GET['cat'];
		$thisterm = get_term_by('slug', $slug, 'category');
		$varcat = $thisterm->term_id;
		echo "<div class='tagcloud'>";
		echo my_colorful_tag_cloud($varcat, 'category' , 'task'); 
		echo "</div>";
		?>
	</div>

<?php
				$taxonomies=array();
				$post_type = array();
				$taxonomies[] = 'category';
				$post_type[] = 'task';
				$post_cat = get_terms_by_post_type( $taxonomies, $post_type);
				if ($post_cat){
					echo "<div class='widget-box'><h3 class='widget-title'>Other categories</h3>";
					echo "<p class='taglisting {$post->post_type}'>";
					foreach($post_cat as $cat){
						if ($cat->name!='Uncategorized' && $cat->name && $cat->term_id != $catid){
						$newname = str_replace(" ", "&nbsp;", $cat->name );
						echo "<span class='wptag t".$cat->term_id."'><a href='".site_url()."/task-by-category/?cat=".$cat->slug."'>".$newname."</a></span><br>";
					}
					}
					echo "</p></div>";
				}
				?>	
	
</div></div></div>

<?php get_footer(); ?>
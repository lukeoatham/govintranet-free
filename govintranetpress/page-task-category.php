<?php
/* Template name: Task category page */

$catquery = @explode("=",$query_string);



get_header(); 

				$cat_name = $_GET['cat'];
				$catpod = new Pod ('category' , $cat_name);
				$catname = $catpod->get_field('name');				
				$catid = $catpod->get_field('term_id');		
				$catdesc = $catpod->get_field('category_page_description');				
				?>

				<div class="row">
					<div class="eightcol white last" id="content">
														<div class="row">
							<div class='breadcrumbs'>
								<a title="Go to Home." href="/" class="site-home">Home</a> > 
								<a title="Go to How do I?" href="/tasks/">How do I?</a> > <?php echo $catname; ?>

							</div>
							
				</div>
					<div class="content-wrapper">
<?php


				echo "<h1 class='h1_" . $catid . "'>".$catname."</h1>";

						?>
					<p><?php echo $catdesc; ?></p>
	<div class="category-search t<?php echo $catid; ?>">
		<div id="sbc">
		<form method="get" id="sbc-search" action="<?php echo home_url( '/' ); ?>">
			<input type="hidden" value="<?php echo $catid; ?> " name = "cat" />
			<input type="hidden" value="task" name = "post_type" />
			<input type="text" value="" name="s" id="s2" class="multi-cat" onblur="if (this.value == '') {this.value = '';}"  onfocus="if (this.value == '') {this.value = '';}" />
			<input type="submit" id="sbc-submitx" class="small awesome blue" value="Search" />
		</form>
	</div>

</div>


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
			    'posts_per_page' => 10,
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
								if ( $taskpod->get_field('page_type') == 'Guide header'){
									echo "<h3 class='taglisting guide'>";
								} ;
								if ( $taskpod->get_field('page_type') == 'Guide chapter'){
								echo "<h3 class='taglisting chapter'>";
								} ;
								if ( $taskpod->get_field('page_type') == 'Task'){
								echo "<h3 class='taglisting task'>";
								} ;
								
								?>
								
									<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'govintranetpress' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h3>

<?php
								the_excerpt(); 
								echo "</div>";
							 }
				?>
				
						<?php if (  $taskitems->max_num_pages > 1 ) : ?>
						<?php if (function_exists(wp_pagenavi)) : ?>
							<?php wp_pagenavi(array('query' => $taskitems)); ?>
 						<?php else : ?>
							<?php next_posts_link('&larr; Older items', $taskitems->max_num_pages); ?>
							<?php previous_posts_link('Newer items &rarr;', $taskitems->max_num_pages); ?>						
						<?php endif; 
						?>
					<?php endif; 
							wp_reset_query();								

							?>
				
</div>
</div>
					<div class="fourcol last" id='sidebar'>
					<div class='widget-box'>
					
					<h3 class='widget-title'>Search by tag</h3>
						<?php 
					$slug = $_GET['cat'];
					$thisterm = get_term_by('slug', $slug, 'category');
					$varcat = $thisterm->term_id;
					echo my_colorful_tag_cloud($varcat, 'category' , 'task'); 
					?>

					</div>
					</div>
				</div>
				
<?php get_footer(); ?>
<?php
/* Template name: Projects */

get_header(); ?>

<?php 
if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
			<div class="col-lg-8 col-md-8 col-sm-8 white ">
				<div class="row">
					<div class='breadcrumbs'>
						<?php if(function_exists('bcn_display') && !is_front_page()) {
							bcn_display();
							}?>
					</div>
				</div>

				<?php
				echo "<h1>";
				the_title();
				echo "</h1>";
				the_content();
				?>

			<!-- category search box -->
			<div>

			<div class="well">
				<form class="form-horizontal" role="form" method="get" id="sbc-search" action="<?php echo site_url('/'); ?>">
					<label for="s">Search</label>
					<div class="form-group input-md">
						<input type="text" value="" name="s" id="sbc-s" class="form-control input-md" onblur="if (this.value == '') {this.value = '';}"  onfocus="if (this.value == '') {this.value = '';}" />
					</div>
					<div class="form-group input-md">
						<button type="submit" class="btn btn-primary input-md">Search</button>
					<input type="hidden" value="project" name = "post_type[]" />
					</div>
				</form>
			</div>

			<?php
				$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
				$counter = 0;	
				$sdate = date('Ymd');
				$news =new WP_Query( array ( 
							'post_type' => 'project',
							'posts_per_page' => -1,
							'orderby' => 'name', 
							'order' => 'ASC',
					       'paged' => $paged,
						   'post_parent' => 0,
							'meta_query' => array(
							       array(
						           		'key' => 'project_end_date',
						        	   'value' => $sdate,
						    	       'compare' => '>=',
						    	       'type' => 'DATE' )  
							       ),
								 )
							);
				if ($news->post_count==0){
					echo "Nothing to show.";
				}
				while ($news->have_posts()) {
						$news->the_post();
				$image_url = get_the_post_thumbnail($id, 'thumbnail', array('class' => 'alignright'));
				echo "<div class='newsitem'>".$image_url ;
				echo "<hr>";
				$taskpod = get_post($post->ID); 
				$context = "project";
				$icon = "road";
			?>
			<h3>				
				<a href="<?php the_permalink(); ?>" title="<?php echo get_the_title(); ?>" rel="bookmark"><?php the_title(); ?></a></h3>

			<?php
			the_excerpt(); 
			echo "</div>";
		}
				?>
			<?php if (  $news->max_num_pages > 1 ) : ?>
			<?php if (function_exists('wp_pagenavi')) : ?>
				<?php wp_pagenavi(array('query' => $news)); ?>
				<?php else : ?>
				<?php next_posts_link('&larr; Older items', $news->max_num_pages); ?>
				<?php previous_posts_link('Newer items &rarr;', $news->max_num_pages); ?>						
			<?php endif; 
			?>
		<?php endif; 
				wp_reset_query();								

				?>
			</div>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-4">
		<?php if ( $cloud = gi_howto_tag_cloud('project') ): ?>
		<div class='widget-box'>
			<h3 class='widget-title'>Browse by tag</h3>
			<?php echo $cloud; ?>
		</div>
		<?php endif; ?>
		</div>


<?php endwhile; ?>

<?php get_footer(); ?>
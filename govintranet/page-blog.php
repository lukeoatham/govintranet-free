<?php
/* Template name: Blog   */

get_header(); 
	 if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

	<div class="col-lg-7 col-md-8 col-sm-12 white">
		<div class="row">
			<div class='breadcrumbs'>
				<?php if(function_exists('bcn_display') && !is_front_page()) {
					bcn_display();
					}?>
			</div>
		</div>
		<?php
		$thistitle = get_the_title();
		echo "<h1>".$thistitle."</h1>";
		the_content();

		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		$counter = 0;	
		$cquery = array(
			'orderby' => 'post_date',
		    'order' => 'DESC',
		    'post_type' => 'blog',
		    'posts_per_page' => 10,
		    'paged' => $paged												
			);

       $projectspost = new WP_Query($cquery);
	   global $k; 
	   $k = 0;
       while ($projectspost->have_posts()) : $projectspost->the_post();
         get_template_part( 'loop', 'blogtwitter' );
       endwhile;
	    ?>
	    <?php wp_reset_query();   //Restore global post data stomped by the_post(). ?>
	    <?php 
			if (  $projectspost->max_num_pages > 1 ) : ?>
			<?php if (function_exists('wp_pagenavi')) : ?>
				<?php wp_pagenavi(array('query' => $projectspost)); ?>
				<?php else : ?>
				<?php next_posts_link(__('&larr; Older items','govintranet'), $projectspost->max_num_pages); ?>
				<?php previous_posts_link(__('Newer items &rarr;','govintranet'), $projectspost->max_num_pages); ?>						
			<?php endif; 
			?>
		<?php endif; 
		wp_reset_query();								
		?>							
	</div>
	<div class="col-lg-4 col-lg-offset-1 col-md-4 col-sm-12">
		
		<?php dynamic_sidebar('bloglanding-widget-area'); ?>
		<?php
		$taxonomies=array();
		$post_type = array();
		$taxonomies[] = 'blog-category';
		$post_type[] = 'blog';
		$post_cat = get_terms_by_post_type( $taxonomies, $post_type);
		if ($post_cat){
			echo "<div class='widget-box'><h3 class='widget-title'>" . __('Categories' , 'govintranet') . "</h3>";
			echo "<p class='taglisting " . $post->post_type . "'>";
			foreach($post_cat as $cat){
				if ($cat->name){
					$newname = str_replace(" ", "&nbsp;", $cat->name );
					echo "<span><a class='wptag t".$cat->term_id."' href='".get_term_link($cat->slug,'blog-category')."'>".$newname."</a></span> ";
				}
			}
			echo "</p></div>";
		}
		?>
	</div>

<?php endwhile; 
	get_footer(); ?>
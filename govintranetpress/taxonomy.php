	<?php
	/**
	 * The template for displaying team pages.
	 *
	 */
	
	get_header(); ?>
		<div class="col-lg-8 col-md-8">
	
		<div class='breadcrumbs'>
			<a href="<?php echo site_url(); ?>">Home</a>
			&raquo; <a href="<?php echo site_url(); ?>/staff-directory/">Staff directory</a>
			&raquo; <?php single_cat_title(); ?>
		</div>

	<?php
		 
		if ($_GET['post_type'] == 'news'){
		$args = array( 
	'post_type' => 'NEWS',
	'posts_per_page' => 10,
	'post_status'=>'publish',
	'paged'=>$paged,
			'tax_query' => array(
		array(
			'taxonomy' => $term->taxonomy,
			'field' => 'slug',
			'terms' => $term->slug,
			
		)
	)

			query_posts('post_type=news');
		} 
		 
		if ( have_posts() )
			the_post();
			
			get_template_part('loop','taxonomy');
	 				 			
		endif;
			?>
		</div>
		<div class="col-lg-4 col-md-4">
		
<?php				$terms = get_terms('category',array('hide_empty'=>false,'parent' => $termid));
			if ($terms) {
				echo "<div class='widget-box list'><h2>Sub-teams</h2>";
		  		foreach ((array)$terms as $taxonomy ) {
		  		    $themeid = $taxonomy->term_id;
		  		    $themeURL= $taxonomy->slug;
			  		    $desc = "<p class='howdesc'>".$taxonomy->description."</p>";
		   		    if ($themeURL == 'uncategorized') {
			  		    continue;
		  		    }
		  			echo "
						<li><a href='".site_url()."/team/{$themeURL}/'>".$taxonomy->name."</a></li>";
				}
				echo "</div>";
			}  


//display dropdown of all top-level teams
		echo "<div class='widget-box'></div>";
	  	$terms = get_terms('category',array('hide_empty'=>false,'parent' => '0',));
		if ($terms) {
			$otherteams='';
	  		foreach ((array)$terms as $taxonomy ) {
	  		    $themeid = $taxonomy->term_id;
	  		    $themeURL= $taxonomy->slug;
	  			$otherteams.= " <li><a href='".site_url()."/team/{$themeURL}/'>".$taxonomy->name."</a></li>";
	  		}  
	  		echo "<div class='btn-group'><button type='button' class='btn btn-default dropdown-toggle4' data-toggle='dropdown'>Other teams <span class='caret'></span></button><ul class='dropdown-menu' role='menu'>".$otherteams."</ul></div>";
		}

	?>

		</div>
	
	<?php get_footer(); ?>
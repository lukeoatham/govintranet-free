<?php
/* Template name: About page */
					
get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
	
	<div class="col-lg-12 white ">
		<div class="row">
			<div class='breadcrumbs'>
				<?php if(function_exists('bcn_display') && !is_front_page()) {
					bcn_display();
					}?>
			</div>
		</div>

		<h1><?php the_title(); ?></h1>
		<?php the_content(); ?>
		<?php

		$id = (!isset($opts['id'])) ? $wp_query->post->ID : $opts['id'];
			
		$children = get_pages("child_of=".$id."&parent=".$id."&hierarchical=0&posts_per_page=-1&post_type=page&sort_column=menu_order&sort_order=ASC");
		$catcount = 0;

		echo "<div class='row white'>";

		foreach((array)$children as $c) {
	
			if ($c->post_excerpt) {
				$excerpt = $c->post_excerpt;
			} else {
				if (strlen($c->post_content)>128) {
					$excerpt = substr(strip_tags($c->post_content),0,128) . "&hellip;";
				} elseif (!$c->post_content) {
					$excerpt = "";
				} else {			
					$excerpt = strip_tags($c->post_content);				
				}
			}
			$excerpt = str_replace('[bbp-forum-index]', '', $excerpt);
		    $themeid = $c->id;
		    $themeURL= $c->post_name;
		    $catcount++;
		    if ($catcount==3)
		    {
			    $catcount=1;
		    }
			echo "<div class='col-lg-3 col-md-6 col-sm-6 white'>
				<div class='category-block'>
					<h2><a href='".get_permalink($c->ID)."'>".govintranetpress_custom_title($c->post_title)."</a></h2>
					<p>".$excerpt."</p>
				</div>
			</div>";
		}

		echo '</div>'; ?>

	</div>

<?php endwhile; 
	 get_footer(); ?>
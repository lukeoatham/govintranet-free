<?php
/* Template name: About page */
					
get_header(); 

wp_register_script( 'match_heights', get_template_directory_uri() . "/js/jquery.matchHeight-min.js");
wp_register_script( 'match_heights_load', get_template_directory_uri() . "/js/ht-scripts-match-heights.js", array('jquery','match_heights'), false, true);
wp_enqueue_script( 'match_heights' );
wp_enqueue_script( 'match_heights_load' );

?>

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
			
		$children = get_pages(array(
			"child_of" => $id,
			"parent" => $id,
			"hierarchical" =>0,
			"post_type" => "page",
			"sort_column" => "menu_order,post_title",
			"sort_order" => "ASC"
			));
		
		$html = '';

		$html.= "<div class='row white'>";

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
			if ( get_post_meta($id, 'ht_about_restrict', true)):
				$html.= "<div class='col-lg-4 col-md-4 col-sm-6 white'>";
			else:
				$html.= "<div class='col-lg-3 col-md-4 col-sm-6 white'>";
			endif;
			$html.= "
				<div class='category-block match-height'>
				<h2><a href='".get_permalink($c->ID)."'>
				";
			if ( get_post_meta( $id, 'show_featured_images', true )){
				$html.= get_the_post_thumbnail($c->ID, 'large', array('class'=>'img-responsive'));
			}
			$html.= "
					".get_the_title($c->ID)."</a></h2>
					<p>".$excerpt."</p>
				</div>
			</div>";		}

		$html.= '</div>'; 
		
		echo $html;
		?>

	</div>
<?php endwhile; ?>
<?php get_footer(); ?>
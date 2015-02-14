<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the wordpress construct of pages
 * and that other 'pages' on your wordpress site will use a
 * different template.
 *
 * @package WordPress
 */

get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); 
?>


					<div class="col-lg-8 col-md-8 col-sm-8 white">
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
				if ('open' == $post->comment_status && $_GET['action']=='discussion') {
					 comments_template( '', true ); 
				}
			 ?>
					
	</div> 
	<div class="col-lg-4 col-md-4 col-sm-4">
	<?php
		the_post_thumbnail('medium', array('class'=>'img img-responsive')); 
		echo wpautop( "<p class='news_date'>".get_post_thumbnail_caption()."</p>" );
		$thispage = get_page($id);
		$relatedteams = '';
		if (taxonomy_exists('team')) $relatedteams = get_the_terms( $id, 'team' );
		$related = get_post_meta($id,'related',true);

				if ($related){
					$html='';
					foreach ($related as $r){ 
						$title_context="";
						$rlink = get_post($r);
						if ($rlink->post_status == 'publish' && $rlink->ID != $id ) {
							$taskparent=$rlink->post_parent; 
							if ($taskparent){
								$tparent_guide_id = $taskparent->ID; 		
								$taskparent = get_post($tparent_guide_id);
								$title_context=" (".govintranetpress_custom_title($taskparent->post_title).")";
							}		
							$html.= "<li><a href='".get_permalink($rlink->ID)."'>".govintranetpress_custom_title($rlink->post_title).$title_context."</a></li>";
						}
					}
				}
				
				//get anything related to this post
				$otherrelated = get_posts(array('post_type'=>array('task','news','project','vacancy','blog','team','event'),'posts_per_page'=>-1,'exclude'=>$related,'meta_query'=>array(array('key'=>'related','compare'=>'LIKE','value'=>'"'.$id.'"')))); 
				foreach ($otherrelated as $o){
					if ($o->post_status == 'publish' && $o->ID != $id ) {
								$taskparent=$o->post_parent; 
								if ($taskparent){
									$tparent_guide_id = $taskparent->ID; 		
									$taskparent = get_post($tparent_guide_id);
									$title_context=" (".govintranetpress_custom_title($taskparent->post_title).")";
								}		
								$html.= "<li><a href='".get_permalink($rlink->ID)."'>".govintranetpress_custom_title($rlink->post_title).$title_context."</a></li>";
						}
				}

				if ($related || $otherrelated){
					echo "<div class='widget-box list'>";
					echo "<h3 class='widget-title'>Related</h3>";
					echo "<ul>";
					echo $html;
					echo "</ul></div>";
				}	?>
	</div>
	

<?php endwhile; ?>

<?php get_footer(); ?>
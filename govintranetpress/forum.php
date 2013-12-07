<?php
/**
 * The template for displaying forum pages. Excludes regular breakcrumb.
 *
 */
get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); 
?>
					<div class="col-lg-12 white ">
						<div class="row">
							<div class='breadcrumbs'>
								<?php if(function_exists('bcn_display') && !is_front_page()) {
									bcn_display();
									}?>
							</div>
						</div>
				

					<h1><?php the_title();  //echo $post->post_type;
						if ($post->post_type != "forum" && $post->post_type != "topic" && $post->post_type != "reply"){
							echo " <small>";
							bbp_displayed_user_field( 'user_job_title' );
							echo "</small>";
						}
						
					?>
					
					</h1>
					<?php the_content(); ?>
			 </div>

<script type='text/javascript'>	

//hide user profile fields
jQuery(".bbp-user-edit #url").parent().parent().hide();
jQuery(".bbp-user-edit h2:contains('Contact Info')").hide();


</script>

	

<?php endwhile; ?>

<?php get_footer(); ?>
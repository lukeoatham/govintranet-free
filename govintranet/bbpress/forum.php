<?php
/**
 * The template for displaying user profile pages. 
 *
 */
get_header(); 
	if ( have_posts() ) while ( have_posts() ) : the_post(); 
?>
<div class="col-lg-12 white ">
	<div class="row">
		<div class='breadcrumbs'>
		<?php 
		$r = $_SERVER['REQUEST_URI']; 
		$r = explode('/', $r);
		if ( in_array('staff',$r) && get_option('options_module_staff_directory') ):
			$sd = get_option('options_module_staff_directory_page');
			$sd = $sd[0];
			?>
			<a href="<?php echo site_url(); ?>"><?php _ex('Home','The site homepage','govintranet'); ?></a>
			> <a href="<?php echo get_permalink($sd); ?>"><?php echo get_the_title($sd); ?></a>
			> <?php the_title(); 
		elseif ( in_array('users',$r) ):?>
				<a href="<?php echo site_url(); ?>"><?php _ex('Home','The site homepage','govintranet'); ?></a>
				> <?php the_title(); 
		elseif (function_exists('bcn_display') && !is_front_page()) :
				bcn_display();
		endif;
		?>
		</div>
	</div>
	<h1><?php the_title(); ?></h1>
	<?php the_content(); ?>
</div>

<?php endwhile; 
	 get_footer(); ?>
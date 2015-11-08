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
		if ($r[1] == 'staff'):?>
				<a href="<?php echo site_url(); ?>"><?php _ex('Home','The site homepage','govintranet'); ?></a>
				> <a href="<?php echo site_url(); ?>/staff-directory/"><?php _e('Staff directory','govintranet'); ?></a>
				> <?php the_title(); ?>
		<?php
		 elseif ($r[1] == 'users'):?>
				<a href="<?php echo site_url(); ?>"><?php _ex('Home','The site homepage','govintranet'); ?></a>
				> <?php the_title(); ?>
		<?php
		else: 
		 if (function_exists('bcn_display') && !is_front_page()) {
				bcn_display();
				}
		endif;
		?>
		</div>
	</div>
	<h1><?php the_title(); ?></h1>
	<?php the_content(); ?>
</div>

<script type='text/javascript'>	

//hide user profile fields
jQuery(".bbp-user-edit #url").parent().parent().hide();
jQuery(".bbp-user-edit h2:contains('Contact Info')").hide();
</script>

<?php endwhile; 
	 get_footer(); ?>
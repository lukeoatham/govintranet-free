<?php
/* Template name: Home page */

get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); 

	// Load intranet homepage settings
	$campaign_message = get_post_meta($post->ID,'campaign_message',true);

	$top_pages =  get_post_meta($post->ID,'top_pages',true);

	$homecontent =  get_post_meta($post->ID,'emergency_message',true);

	$homecontentcolour =  strtolower(get_post_meta($post->ID,'emergency_message_style',true)); 

	$forumsupport = get_option('options_forum_support');

	if ($homecontentcolour != "none" && $homecontentcolour): //Display emergency message 
		?>
		<div class="col-lg-12">
			<div class="alert alert-dismissable alert-<?php echo $homecontentcolour; ?>">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<?php	echo apply_filters('the_content', $homecontent, true);	 ?>
			</div>
		</div>	
		<?php 
	endif; ?>

	<div id="home-col-1" class="col-lg-6 col-md-6 col-sm-7">
		<?php 	if (is_active_sidebar('home-widget-area1')) dynamic_sidebar('home-widget-area1'); ?>	
	</div>

	<?php 	if (is_active_sidebar('home-widget-area-hero')): ?>
		<div id="home-hero" class="col-lg-6 col-md-6 col-sm-5">
		<?php 	dynamic_sidebar('home-widget-area-hero'); ?>
		</div>
	<?php endif; ?>

	<div  id="home-col-2"  class="col-lg-3 col-md-3 col-sm-5">
		<?php 	if (is_active_sidebar('home-widget-area2')) dynamic_sidebar('home-widget-area2'); ?>
	</div>
	
	<div  id="home-col-3" class="col-lg-3 col-md-3 col-sm-5">
	<?php
	if (is_active_sidebar('login-widget-area') ) : 
			$current_user = wp_get_current_user();
			?>
			<div id="loginrow" class="category-block">
				<div id="loginaccordion">
					<h3 class="widget-title">
				    <a class="accordion-toggle" data-toggle="collapse" data-parent="#loginaccordion" href="#logincollapselogin">
					<?php if (is_user_logged_in()):?>
						      <?php 
						      echo get_avatar(intval($current_user->id),32); 
						      echo " ".$current_user->display_name; ?>
					<?php else :?>
						       <?php _e('Login' , 'govintranet'); ?> <i class="glyphicon glyphicon-chevron-down"></i>
					<?php endif; ?>
				        </a>
					</h3>
			    </div>
			    <div id="logincollapselogin" class="xpanel-collapse collapse out">
					<div class="xpanel-body">
					<?php if (is_active_sidebar('login-widget-area')) dynamic_sidebar('login-widget-area');	?> 
					</div>
				</div>
			</div>
	<?php endif; ?>

	<?php 
	if (is_active_sidebar('home-widget-area3t')) dynamic_sidebar('home-widget-area3t'); 
	echo "</div>";
	//if (is_active_sidebar('home-widget-area-hero')) echo "</div>";

	if ($campaign_message) :  //Display campaign message ?>
		<div class="clearfix"></div>
		<div class="col-lg-12">
			<?php 	echo apply_filters('the_content', $campaign_message, true);	 ?>
			<br>
		</div>
		<?php 
	endif; 
	?>

<?php endwhile; ?>
<?php get_footer(); ?>
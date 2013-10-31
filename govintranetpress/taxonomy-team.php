<?php
/**
 * The template for displaying team pages.
 *
 */

get_header(); ?>



<?php
	/* Queue the first post, that way we know
	 * what date we're dealing with (if that is the case).
	 *
	 * We reset this later so we can run the loop
	 * properly with a call to rewind_posts().
	 */
	 //query_posts('post_type=news');
	if ( have_posts() )
		the_post();

		
?>

					<div class="col-lg-8 col-md-8 white">
						<div class="row">
							<div class='breadcrumbs'>
								<?php if(function_exists('bcn_display') && !is_front_page()) {
									bcn_display();
									}?>
							</div>
						</div>
			<h1>Teams</h1>

<?php 
$slug = pods_url_variable(1);


		$terms = get_term_by('slug',$slug,'team',ARRAY_A); 

?>
<h2><?php echo ($terms['name']); ?></h2>

<?php


	/* Since we called the_post() above, we need to
	 * rewind the loop back to the beginning that way
	 * we can run the loop properly, in full.
	 */



	 $termid = $terms['term_id'];
	 
	 $taxteam = new Pod ('team', $termid);
	 
	 //output team head first!
	 $teamleader = $taxteam->get_field('team_head');
	 $teamleaderid = $teamleader[0]['ID'];

	 			$context = get_user_meta($teamleaderid,'user_job_title',true);
			if ($context=='') $context="staff";
			$icon = "user";			
			$user_info = get_userdata($teamleaderid);
			$userurl = '/staff/'.$user_info->user_login;
			$image_url = get_wp_user_avatar($teamleaderid,130,'left');
			$image_url = str_replace('avatar ', 'avatar img img-circle ' , $image_url);
			echo "<div class='media'>" ;
			echo "<div class='hidden-xs'><a href='";
			echo $userurl;
			echo "'>".$image_url."</a></div>" ;
			echo "<div class='media-body'>";
		?>
			<h3>Team head				
			<a href="<?php echo $userurl; ?>" title="<?php echo $user_info->display_name ; ?>" rel="bookmark"><?php echo $user_info->display_name ; ?></a></h3>
			<p><i class="glyphicon glyphicon-user"></i> <?php echo get_user_meta($teamleaderid,'user_job_title',true); ?></p>
			<?php if ( get_user_meta($teamleaderid ,'user_telephone',true )) : ?>

				<p><i class="glyphicon glyphicon-earphone"></i> <?php echo get_user_meta($teamleaderid ,'user_telephone',true ); ?></p>

			<?php endif; ?>

			<?php if ( get_user_meta($userid,'user_mobile',true ) ) : ?>

				<p><i class="glyphicon glyphicon-phone"></i> <?php echo get_user_meta($teamleaderid,'user_mobile',true ); ?></p>

			<?php endif; ?>

				<p><a href="mailto:<?php echo $user_info->user_email; ?>">Email <?php echo $user_info->user_email; ?></a></p>

			<?php
	echo "</div></div>";

//custom sql query returns users in the current team sorted by grade

	 $q = "select user_id, slug from wp_usermeta join wp_terms on wp_terms.term_id = wp_usermeta.meta_value where user_id in (select user_id from wp_usermeta as a where a.meta_key = 'user_team' and a.meta_value = ".$termid.") and meta_key = 'user_grade' order by slug asc
 ";
$user_query = $wpdb ->get_results($q);
	 
//	 $user_query = new WP_User_Query( array( 'meta_key' => 'user_team', 'meta_value' => $termid, 'orderby' => 'user_grade', 'order' => 'ASC'  ) );

	 	foreach ($user_query as $u){//print_r($u);
			$userid =  $u->user_id;//echo $userid;
			if ($userid ==  $teamleaderid) continue; //don't output if this person is the team head and already displayed
			$context = get_user_meta($userid,'user_job_title',true);
			if ($context=='') $context="staff";
			$icon = "user";			
			$user_info = get_userdata($userid);
			$userurl = '/staff/'.$user_info->user_login;
			$image_url = get_wp_user_avatar($userid,130,'left');
			$image_url = str_replace('avatar ', 'avatar img img-circle ' , $image_url);
			echo "<hr><div class='media'>" ;
			echo "<div class='hidden-xs'><a href='"; //don't show on mobile phones
			echo $userurl;
			echo "'>".$image_url."</a></div>" ;
			echo "<div class='media-body'>";
		?>
			<h3>				
			<a href="<?php echo $userurl; ?>" title="<?php echo $user_info->display_name ; ?>" rel="bookmark"><?php echo $user_info->display_name ; ?></a></h3>
			<p><i class="glyphicon glyphicon-user"></i> <?php echo get_user_meta($userid,'user_job_title',true); ?></p>
			<?php if ( get_user_meta($userid ,'user_telephone',true )) : ?>

				<p><i class="glyphicon glyphicon-earphone"></i> <?php echo get_user_meta($userid ,'user_telephone',true ); ?></p>

			<?php endif; ?>

			<?php if ( get_user_meta($userid,'user_mobile',true ) ) : ?>

				<p><i class="glyphicon glyphicon-phone"></i> <?php echo get_user_meta($userid,'user_mobile',true ); ?></p>

			<?php endif; ?>

				<p><a href="mailto:<?php echo $user_info->user_email; ?>">Email <?php echo $user_info->user_email; ?></a></p>

			<?php
	echo "</div></div>";
	}

	
?>


				</div>
				<div class="col-lg-4">
				<div class="widget-box list">
				<h3 class="widget-title">Other teams</h3>
				<?php
				$terms = get_terms('team',array('hide_empty'=>false));
	if ($terms) {
	echo "<ul>";
  		foreach ((array)$terms as $taxonomy ) {
  		    $themeid = $taxonomy->term_id;
  		    $themeURL= $taxonomy->slug;
	  		    $desc = "<p class='howdesc'>".$taxonomy->description."</p>";
   		    if ($themeURL == 'uncategorized') {
	  		    continue;
  		    }
  			echo "
				<li><a href='/team/{$themeURL}/'>".$taxonomy->name."</a></li>";
		}
		echo "</ul>";
	}  
?>
				</div>
				</div>

<?php get_footer(); ?>
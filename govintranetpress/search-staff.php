<?php
/* Template name: Staff search */
 		
 //*****************************************************				

get_header(); ?>

	<div class="col-lg-8 col-md-9 white ">
		<div class="row">
			<div class='breadcrumbs'>
				<?php if(function_exists('bcn_display') && !is_front_page()) {
						bcn_display();
					}?>
			</div>
		</div>
	

<?php 
$s = sanitize_text_field($_GET['q']);
if ($s) {
	$q = "
	SELECT distinct user_id from wp_usermeta WHERE 
	(meta_value like '%".$s."%' and meta_key='user_job_title') OR
	(meta_value like '%".$s."%' and meta_key='first_name') OR
	(meta_value like '%".$s."%' and meta_key='last_name') OR
	(meta_value like '%".$s."%' and meta_key='nickname') OR
	(meta_value like '%".$s."%' and meta_key='description') OR
	(meta_value like '%".$s."%' and meta_key='user_job_title') OR
	(meta_value like '%".$s."%' and meta_key='user_telephone') OR
	(meta_value like '%".$s."%' and meta_key='user_mobile') OR
	(meta_value like '%".$s."%' and meta_key='user_key_skills') 
	";
	$q2 = "SELECT distinct wp_terms.term_id, wp_terms.slug, wp_terms.name, wp_term_taxonomy.description from wp_term_taxonomy join wp_terms on wp_terms.term_id = wp_term_taxonomy.term_id WHERE 
	((wp_term_taxonomy.description like '%".$s."%') OR (wp_terms.name like '%".$s."%')) and wp_term_taxonomy.taxonomy = 'team'";
	
	
	$sr = $wpdb->get_results($q,ARRAY_A); //print_r($sr);
	$sr2 = $wpdb->get_results($q2,ARRAY_A); //print_r($sr);
}
?>
	<h1><?php printf( __( 'Search results for: %s', 'twentyten' ), '' . $s . '' ); ?></h1>
	<form class="form-horizontal" role="form" id="searchform2" name="searchform2" action="<?php echo home_url( '/search-staff/' ); ?>">
	  <div class="col-lg-12">
		<div id="staff-search" class="well well-sm">
				<div class="input-group">
			    	 <input type="text" class="form-control" placeholder="Search for a name, job title, skills, phone number..." name="q" id="s2" value="<?php echo $_GET['s'];?>">
					 <span class="input-group-btn">
						 <button class="btn btn-primary" type="submit"><i class="glyphicon glyphicon-search"></i></button>
					 </span>
				</div><!-- /input-group -->
		  </div>
	  </div>
	</form>
<script>
jQuery("#s2").focus();
</script>							

	<?php
	if (count($sr) > 0 || count($sr2) > 0){
		$totfound = count($sr)+count($sr2);
		echo "<p class='news_date'>Found ".$totfound." result";
		if ($totfound > 1) echo "s";
		echo "</p>";
	}

?>
	
<?php
foreach ((array)$sr as $post){
 
	$image_url = get_avatar($post['user_id']);
	$image_url = str_replace('avatar ', 'avatar img img-responsive ' , $image_url);
	$userurl = get_author_posts_url( $post['user_id']); 
	$gis = "general_intranet_forum_support";
	$forumsupport = get_option($gis);
	if ($forumsupport){
		if (function_exists('bp_activity_screen_index')){ // if using BuddyPress - link to the members page
			$userurl=str_replace('/author', '/members', $userurl); }
		elseif (function_exists('bbp_get_displayed_user_field')){ // if using bbPress - link to the staff page
			$userurl=str_replace('/author', '/staff', $userurl);
		}
	} 
?>

	<div class="row"><div class="col-lg-12"><br>
		<div class="panel panel-default">
			<div class="panel-heading">
				<a href="<?php echo $userurl; ?>" rel="bookmark"><?php echo get_user_meta($post['user_id'],'first_name',true)." ".get_user_meta($post['user_id'],'last_name',true);  ?></a>
			</div>
			<div class="panel-body">
				<div class='media'>
					<h3 class='postlist'>				
						<a href=""><?php  echo "</a> <small>".$title_context."</small>"; ?></h3>
	
<?php
						echo "<a class='pull-left' href='";
						echo $userurl;
						echo "'><div class='hidden-xs'>".$image_url."</div></a>" ;
						echo "<div class='media-body'>";
						$user_info = get_userdata($post['user_id']);?>
						<?php if ( get_user_meta($post['user_id'] ,'user_job_title',true )) : ?>
							<?php echo "<strong>".get_user_meta($post['user_id'] ,'user_job_title',true )."</strong>"; ?></br>
						<?php endif; ?>
						
						<?php if ( get_user_meta($post['user_id'] ,'user_telephone',true )) : ?>
							<i class="glyphicon glyphicon-earphone"></i> <a href="tel:<?php echo str_replace(" ", "", get_user_meta($post['user_id'] ,'user_telephone',true )) ; ?>"><?php echo get_user_meta($post['user_id'] ,'user_telephone',true ); ?></a><br>
						<?php endif; ?>
						<?php if ( get_user_meta($post['user_id'] ,'user_mobile',true ) ) : ?>
							<i class="glyphicon glyphicon-phone"></i> <a href="tel:<?php echo str_replace(" ", "", get_user_meta($post['user_id'] ,'user_mobile',true )) ; ?>"><?php echo get_user_meta($post['user_id'] ,'user_mobile',true ); ?></a><br>
							<?php endif; ?>
							<a href="mailto:<?php echo $user_info->user_email; ?>">Email <?php echo $user_info->user_email; ?></a></p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	
	<?php
	
	}


foreach ((array)$sr2 as $post){
?>
<div class="row"><div class="col-lg-12"><br>
		<div class="panel panel-default">
			<div class="panel-heading">
				<a href="<?php echo site_url()."/team/"; echo $post['slug']; ?>" rel="bookmark"><?php echo $post['name'];  ?> (Team)</a>
			</div>
			<div class="panel-body">
				<div class='media'>
					<h3 class='postlist'>				
						<a href=""><?php  echo "</a> <small>".$title_context."</small>"; ?></h3>
<?php
						echo "<div class='media-body'>";
						echo $post['description'];
?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
<?php
}

	if ($totfound == 0):
		echo "<h2>Not on the directory</h2><p>Try searching again or go back to the <a href='".site_url()."/staff-directory'>staff directory</a></p><br>";
	endif;
	
?>
		</div>

	</div>
	<div class="col-lg-4 col-md-3" id='sidebar'>
	<?php 	dynamic_sidebar('serp-widget-area'); ?>	
	</div>


<?php get_footer(); ?>

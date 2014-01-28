<?php
/* Template name: Staff search */
 		
 //*****************************************************				

$directorystyle = get_option('general_intranet_staff_directory_style'); // 0 = squares, 1 = circles
$showgrade = get_option('general_intranet_show_grade_on_staff_cards'); // 1 = show 
$showmobile = get_option('general_intranet_show_mobile_on_staff_cards'); // 1 = show

get_header(); ?>

	<div class="col-lg-8 col-md-9 white">
		<div class='breadcrumbs'>
			<a href="<?php echo site_url(); ?>">Home</a>
			&raquo; <a href="<?php echo site_url(); ?>/staff-directory/">Staff directory</a>
			&raquo; Search results
		</div>

<?php 
$s = sanitize_text_field($_GET['q']);
if ($s) {
	// search user meta
	$sr = $wpdb->get_results($wpdb->prepare("
	SELECT distinct user_id from wp_usermeta WHERE 
	(meta_value like '%%%s%%' and meta_key='user_job_title') OR
	(meta_value like '%%%s%%' and meta_key='first_name') OR
	(meta_value like '%%%s%%' and meta_key='last_name') OR
	(meta_value like '%%%s%%' and meta_key='nickname') OR
	(meta_value like '%%%s%%' and meta_key='description') OR
	(meta_value like '%%%s%%' and meta_key='user_job_title') OR
	(meta_value like '%%%s%%' and meta_key='user_telephone') OR
	(meta_value like '%%%s%%' and meta_key='user_mobile') OR
	(meta_value like '%%%s%%' and meta_key='user_key_skills') 	
	",$s,$s,$s,$s,$s,$s,$s,$s,$s),ARRAY_A); 

	//search team taxonomy
	$sr2 = $wpdb->get_results($wpdb->prepare("
	SELECT distinct wp_terms.term_id, wp_terms.slug, wp_terms.name, wp_term_taxonomy.description from wp_term_taxonomy join wp_terms on wp_terms.term_id = wp_term_taxonomy.term_id WHERE 
	((wp_term_taxonomy.description like '%%%s%%') OR (wp_terms.name like '%%%s%%')) and wp_term_taxonomy.taxonomy = 'team';",$s,$s),ARRAY_A); 
}
?>
	<h1><?php printf( __( 'Search results for: %s', 'twentyten' ), '' . $s . '' ); ?></h1>
	<form class="form-horizontal" role="form" id="searchform2" name="searchform2" action="<?php echo site_url( '/search-staff/' ); ?>">
	  <div class="col-lg-12">
		<div id="staff-search" class="well well-sm">
				<div class="input-group">
			    	 <input type="text" class="form-control" placeholder="Search for a name, job title, skills, phone number..." name="q" id="s2" value="<?php echo $_GET['q'];?>">
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
<div id="peoplenav" class="search-staff row">	
<?php
foreach ((array)$sr as $u){

					$g = get_user_meta($u['user_id'],'user_grade',false);
	 				$l = get_user_meta($u['user_id'],'last_name',false);
	 				//echo $u." ".$g[0]['name']." ".$l[0]."<br> ";	
	 				
	 				
	 				$userid =  $u['user_id'];//echo $userid;


					$context = get_user_meta($userid,'user_job_title',true);
					if ($context=='') $context="staff";
					$icon = "user";			
					$user_info = get_userdata($userid);
					$userurl = site_url().'/staff/'.$user_info->user_login;
					$displayname = get_user_meta($userid ,'first_name',true )." ".get_user_meta($userid ,'last_name',true );					
					if ( function_exists('get_wp_user_avatar')){
						$image_url = get_wp_user_avatar($userid,130,'left');
					} else {
						$image_url = get_avatar($userid,130);
					}
					$image_url = str_replace('avatar ', 'avatar img ' , $image_url);

					if ($directorystyle==1){
						$avatarhtml = str_replace('avatar-66', 'avatar-66 pull-left indexcard-avatar img img-circle', get_avatar($userid,66));
					}else{
						$avatarhtml = str_replace('avatar-66', 'avatar-66 pull-left indexcard-avatar img ', get_avatar($userid,66));
					}

					$gradedisplay='';
					if ($showgrade){
						$gradecode = get_user_meta($userid,'user_grade',true);
						$gradecode = $gradecode['grade_code'];
						$gradedisplay = "<span class='badge pull-right'>".$gradecode."</span>";
					}

					if ($fulldetails){
							
							echo "<div class='col-lg-6 col-md-6 col-sm-6'><div class='media well well-sm'><a href='".site_url()."/staff/".$user_info->user_nicename."/'>".$avatarhtml."</a><div class='media-body'><p><a href='".site_url()."/staff/".$user_info->user_nicename."/'><strong>".$displayname."</strong>".$gradedisplay."</a><br>";

							// display team name(s)
							$poduser = new Pod ('user' , $userid);
							$terms = $poduser->get_field('user_team');
							if ($terms) {				
								$teamlist = array();
						  		foreach ($terms as $taxonomy ) {
						  			$teamlist[]= $taxonomy['name'];
						  			echo implode(" &raquo; ", $teamlist)."<br>";
		
								}
							}  

							if ( get_user_meta($userid ,'user_job_title',true )) : 
				
								echo get_user_meta($userid ,'user_job_title',true )."<br>";
				
							endif;
	
							
							if ( get_user_meta($userid ,'user_telephone',true )) : 
				
								echo '<i class="glyphicon glyphicon-earphone"></i> <a href="tel:'.str_replace(" ", "", get_user_meta($userid ,"user_telephone",true )).'">'.get_user_meta($userid ,'user_telephone',true )."</a><br>";
				
							endif; 
				
							if ( get_user_meta($userid ,'user_mobile',true ) && $showmobile ) : 
				
								echo '<i class="glyphicon glyphicon-phone"></i> <a href="tel:'.str_replace(" ", "", get_user_meta($userid ,"user_mobile",true )).'">'.get_user_meta($userid ,'user_mobile',true )."</a><br>";
				
							 endif;
				
								echo  '<a href="mailto:'.$user_info->user_email.'">Email '. $user_info->first_name. '</a></p></div></div></div>';
								
								$counter++;	

						
					} //end full details
					else {  
						echo "<div class='col-lg-6 col-md-6 col-sm-6'><div class='indexcard'><a href='".site_url()."/staff/".$user_info->user_nicename."/'><div class='media'>".$avatarhtml."<div class='media-body'><strong>".$displayname."</strong>".$gradedisplay."<br>";
							// display team name(s)
							$poduser = new Pod ('user' , $userid);
							$terms = $poduser->get_field('user_team');
							if ($terms) {				
								$teamlist = array();
						  		foreach ($terms as $taxonomy ) {
						  			$teamlist[]= $taxonomy['name'];
						  			echo implode(" &raquo; ", $teamlist)."<br>";
		
								}
							}  
							
								if ( get_user_meta($userid ,'user_job_title',true )) echo '<span class="small">'.get_user_meta($userid ,'user_job_title',true )."</span><br>";

								if ( get_user_meta($userid ,'user_telephone',true )) echo '<span class="small"><i class="glyphicon glyphicon-earphone"></i> '.get_user_meta($userid ,'user_telephone',true )."</span><br>";
								if ( get_user_meta($userid ,'user_mobile',true ) && $showmobile ) echo '<span class="small"><i class="glyphicon glyphicon-phone"></i> '.get_user_meta($userid ,'user_mobile',true )."</span>";
												
								echo "</div></div></div></div></a>";
								$counter++;	
					}	

	
	}



foreach ((array)$sr2 as $post){
?>
<div class="row"><div class="col-lg-12"><br>

		<h3 class='postlist'>				
				<a href="<?php echo site_url()."/team/"; echo $post['slug']; ?>" rel="bookmark"><?php echo $post['name'];  ?> (Team)</a></h3>
				<div class='media'>
<?php
						echo "<div class='media-body'>";
						echo $post['description'];
?>
					</div>
				</div>
			</div>
		</div>
<br class="clearfix">
<?php
}
	
	if ($totfound == 0):
		echo "<h2>Not on the directory</h2><p>Try searching again or go back to the <a href='".site_url()."/staff-directory'>staff directory</a></p><br>";
	endif;
	
?></div>
		</div>

	</div>
	<div class="col-lg-4 col-md-3" id='sidebar'>
	<?php 	dynamic_sidebar('serp-widget-area'); ?>	
	</div>


<?php get_footer(); ?>

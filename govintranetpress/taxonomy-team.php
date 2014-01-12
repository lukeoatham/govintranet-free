	<?php
	/**
	 * The template for displaying team pages.
	 *
	 */
	
	get_header(); ?>
	
	<div class="row">
		<div class='breadcrumbs'>
			<a href="<?php echo site_url(); ?>">Home</a>
			> <a href="<?php echo site_url(); ?>/staff-directory/">Staff directory</a>
			> <?php single_cat_title(); ?>
		</div>
	</div>

	<?php
		/* Queue the first post, that way we know
		 * what date we're dealing with (if that is the case).
		 *
		 * We reset this later so we can run the loop
		 * properly with a call to rewind_posts().
		 */
		 
		if ( have_posts() )
			the_post();
	
			$slug = pods_url_variable(-1);
			$terms = get_term_by('slug',$slug,'team',ARRAY_A); 
			$teamname = $terms['name'];
			$termid = $terms['term_id'];
			$teamparent = $terms['parent'];
			$teamdesc = $terms['description'];
			
			$taxteam = new Pod ('team', $termid);
			
	?>
			<div class="col-lg-8 col-md-8 white">
	<?php
				if ($teamparent){
					$parentteam = get_term_by('id',$teamparent,'team');
					echo "<h2><i class='glyphicon glyphicon-chevron-left'></i> <a href='".site_url()."/team/".$parentteam->slug."'>".$parentteam->name."</a></h2>";
				}
?>	
				<h1>Team: <?php echo $teamname; ?></h1>
<?php	
		echo wpautop($teamdesc);
		/* Since we called the_post() above, we need to
		 * rewind the loop back to the beginning that way
		 * we can run the loop properly, in full.
		 */
	
		 //output team head first!
				$teamleader = $taxteam->get_field('team_head');
				if ($teamleader){
					$teamleaderid = $teamleader[0]['ID'];
					$newgrade = get_user_meta($teamleaderid,'user_grade',true);
					$gradehead=$newgrade['slug'];
					if ($gradehead){
						echo "<div class='home page'><div class='category-block'><h3>".$newgrade['name']."</h3></div></div>";
						}
		 			$context = get_user_meta($teamleaderid,'user_job_title',true);
		
					if ($context=='') $context="staff";
					$icon = "user";			
					$user_info = get_userdata($teamleaderid);
					$userurl = site_url().'/staff/'.$user_info->user_login;
					if ( function_exists('get_wp_user_avatar')){
						$image_url = get_wp_user_avatar($teamleaderid,130,'left');
					} else {
						$image_url = get_avatar($teamleaderid,130);
					}
					$image_url = str_replace('avatar ', 'avatar img img-responsive ' , $image_url);
					echo "<div class='media'>" ;
					echo "<div class='hidden-xs'><a href='";
					echo $userurl;
					echo "'>".$image_url."</a></div>" ;
					echo "<div class='media-body'>";
				?>
					<h3><a href="<?php echo $userurl; ?>" title="<?php echo $user_info->display_name ; ?>" rel="bookmark"><?php echo $user_info->display_name ; ?></a></h3>
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
				}
	//custom sql query returns users in the current team sorted by grade
	
		 		$q = "select term_id from wp_term_taxonomy where parent = ".$termid;
	
	 			$term_query = $wpdb ->get_results($q,ARRAY_A);
	 			$allterms= array();
	 			$multipleteams = false;
	 			foreach ($term_query as $tq){
		 			$allterms[] = $tq['term_id'];
		 			$multipleteams = true;
	 			}
	 			$allterms[] = $termid;
	 			$allterms = implode(",", $allterms);
	
	
		 		$q = "select user_id, slug from wp_usermeta join wp_terms on wp_terms.term_id = wp_usermeta.meta_value where user_id in (select user_id from wp_usermeta as a where a.meta_key = 'user_team' and a.meta_value IN (".$allterms.") ) and meta_key = 'user_grade' order by slug asc
	 "; 
	 			$user_query = $wpdb ->get_results($q);
	 			query_posts('order=ASC,orderby=display_name');
			 	foreach ($user_query as $u){//print_r($u);
					$userid =  $u->user_id;//echo $userid;
					$newgrade = get_user_meta($userid,'user_grade',true);
					if ($newgrade['slug']!=$gradehead) {
						$gradehead=$newgrade['slug'];
						echo "<div class='home page'><div class='category-block'><h3>".$newgrade['name']."</h3></div></div>";
					}
					if ($userid ==  $teamleaderid) continue; //don't output if this person is the team head and already displayed
					$context = get_user_meta($userid,'user_job_title',true);
					if ($context=='') $context="staff";
					$icon = "user";			
					$user_info = get_userdata($userid);
					$userurl = site_url().'/staff/'.$user_info->user_login;
					if ( function_exists('get_wp_user_avatar')){
						$image_url = get_wp_user_avatar($userid,130,'left');
					} else {
						$image_url = get_avatar($userid,130);
					}
					$image_url = str_replace('avatar ', 'avatar img img-responsive ' , $image_url);
					echo "<hr><div class='media'>" ;
					echo "<div class='hidden-xs'><a href='"; //don't show on mobile phones
					echo $userurl;
					echo "'>".$image_url."</a></div>" ;
					echo "<div class='media-body'>";
				?>
					<h3>				
					<a href="<?php echo $userurl; ?>" title="<?php echo $user_info->display_name ; ?>" rel="bookmark"><?php echo $user_info->display_name ; ?></a></h3>
					<p><i class="glyphicon glyphicon-user"></i> <?php echo get_user_meta($userid,'user_job_title',true); ?>
					<?php
					if($multipleteams){
						$userteams = get_user_meta($userid,'user_team',false); 
						foreach ((array)$userteams as $userteam){
							echo " (" . $userteam['name'] . ")";
						}
					}
	
					?>
					</p>
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
					<div class="col-lg-4 col-md-4">
					
	<?php				$terms = get_terms('team',array('hide_empty'=>false,'parent' => $termid));
						if ($terms) {
							echo "<div class='widget-box list'><h2>Sub-teams</h2>";
					  		foreach ((array)$terms as $taxonomy ) {
					  		    $themeid = $taxonomy->term_id;
					  		    $themeURL= $taxonomy->slug;
						  		    $desc = "<p class='howdesc'>".$taxonomy->description."</p>";
					   		    if ($themeURL == 'uncategorized') {
						  		    continue;
					  		    }
					  			echo "
									<li><a href='".site_url()."/team/{$themeURL}/'>".$taxonomy->name."</a></li>";
							}
							echo "</div>";
						}  


//display dropdown of all top-level teams
		echo "<div class='widget-box'></div>";
	  	$terms = get_terms('team',array('hide_empty'=>false,'parent' => '0',));
		if ($terms) {
			$otherteams='';
	  		foreach ((array)$terms as $taxonomy ) {
	  		    $themeid = $taxonomy->term_id;
	  		    $themeURL= $taxonomy->slug;
	  			$otherteams.= " <li><a href='".site_url()."/team/{$themeURL}/'>".$taxonomy->name."</a></li>";
	  		}  
	  		echo "<div class='btn-group'><button type='button' class='btn btn-default dropdown-toggle4' data-toggle='dropdown'>Other teams <span class='caret'></span></button><ul class='dropdown-menu' role='menu'>".$otherteams."</ul></div>";
		}

	?>

		</div>
	
	<?php get_footer(); ?>
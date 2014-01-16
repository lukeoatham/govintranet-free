	<?php
	/**
	 * The template for displaying team pages.
	 *
	 */
	
	get_header(); ?>
	
		<div class='breadcrumbs'>
			<a href="<?php echo site_url(); ?>">Home</a>
			> <a href="<?php echo site_url(); ?>/staff-directory/">Staff directory</a>
			> <?php single_cat_title(); ?>
		</div>

	<?php
		 $fulldetails=get_option('general_intranet_full_detail_staff_cards');
		 
		if ( have_posts() )
			the_post();
	
			$slug = pods_url_variable(-1);
			$terms = get_term_by('slug',$slug,'team',ARRAY_A); 
			$teamname = $terms['name'];
			$termid = $terms['term_id'];
			$teamparent = $terms['parent'];
			$teamdesc = $terms['description'];
			
			$taxteam = new Pod ('team', $termid);
			$teamleader = $taxteam->get_field('team_head');
			
	?>
			<div class="col-lg-8 col-md-8 white">
			<h1>Staff directory</h1>
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
				if ($teamparent){
					$parentteam = get_term_by('id',$teamparent,'team');
					echo "<h3><i class='glyphicon glyphicon-chevron-left'></i> <a href='".site_url()."/team/".$parentteam->slug."'>".$parentteam->name."</a></h3>";
				}
?>	<div id="peoplenav">
				<h2>Team: <?php echo $teamname; ?></h2>
<?php	
		echo wpautop($teamdesc);
	
		 //output team head first!
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
	
				//query all sub teams for this team
		 		$q = "select term_id from wp_term_taxonomy where parent = ".$termid;
	 			$term_query = $wpdb ->get_results($q,ARRAY_A);
	 			$allterms= array();
	 			$multipleteams = false;
	 			foreach ($term_query as $tq){
		 			$allterms[] = $tq['term_id'];
		 			$multipleteams = true;
	 			}
	 			$allterms[] = $termid; //add current team onto the the array
	 			$allterms = implode(",", $allterms);//prepare for sql query
	
	//custom sql query returns users in the current team sorted by grade
	 			
		 		$q = "select user_id from wp_usermeta join wp_terms on wp_terms.term_id = wp_usermeta.meta_value where user_id in (select user_id from wp_usermeta as a where a.meta_key = 'user_team' and a.meta_value IN (".$allterms.") ) and meta_key = 'user_grade' ;
	 "; 
	 			$user_query = $wpdb ->get_results($q);
	 			$counter=0;
	 			$uid = array();
	 			$ugrade = array();
	 			$ulastname = array();
	 			foreach ($user_query as $u){//print_r($u);
		 			$uid[] = $u->user_id;
		 			$ulastname[] = get_user_meta($u->user_id,'last_name',true);
		 			$g = get_user_meta($u->user_id,'user_grade',true); 
		 			$ugrade[] =  $g['slug'];		 			
	 			}
	 			
	 			array_multisort($ugrade, $ulastname, $uid);
	 			foreach ($uid as $u){//print_r($u);
	 				$g = get_user_meta($u,'user_grade',false);
	 				$l = get_user_meta($u,'last_name',false);
	 				//echo $u." ".$g[0]['name']." ".$l[0]."<br> ";	
	 				
	 				
	 				$userid =  $u;//echo $userid;

					if ($userid ==  $teamleaderid) continue; //don't output if this person is the team head and already displayed


					$newgrade = get_user_meta($userid,'user_grade',true);
					if ($newgrade['slug']!=$gradehead) {
						$gradehead=$newgrade['slug'];
						if ($counter!=0) echo "</div>";
						echo "<div class='home page'><div class='category-block'><h3>".$newgrade['name']."</h3></div></div><div class='row'>";
					} else {
						if ($counter==0){
							echo "<div class='row'>";	
						}
					}

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
					$image_url = str_replace('avatar ', 'avatar img img-responsive' , $image_url);

					if ($fulldetails){
							$gradedisplay='';
							if ( function_exists('get_wp_user_avatar')){
							echo "<div class='col-lg-6 col-md-6 col-sm-6'><div class='media well well-sm'><a href='".site_url()."/staff/".$user_info->user_nicename."/'>".get_wp_user_avatar($u['user_id'],66,'left')."</a><div class='media-body'><p><a href='".site_url()."/staff/".$user_info->user_nicename."/'><strong>".$displayname."</strong>".$gradedisplay."</a><br>";
							} else {
							echo "<div class='col-lg-6 col-md-6 col-sm-6'><div class='media well well-sm'><a href='".site_url()."/staff/".$user_info->user_nicename."/'>".str_replace('avatar-66', 'avatar-66 pull-left indexcard-avatar', get_avatar($u['user_id'],66))."</a><div class='media-body'><p><a href='".site_url()."/staff/".$user_info->user_nicename."/'><strong>".$displayname."</strong>".$gradedisplay."</a><br>";
							}

							if ( get_user_meta($userid ,'user_job_title',true )) : 
				
								echo get_user_meta($userid ,'user_job_title',true )."<br>";
				
							endif;
	
							
							if ( get_user_meta($userid ,'user_telephone',true )) : 
				
								echo '<i class="glyphicon glyphicon-earphone"></i> <a href="tel:'.str_replace(" ", "", get_user_meta($userid ,"user_telephone",true )).'">'.get_user_meta($userid ,'user_telephone',true )."</a><br>";
				
							endif; 
				
							if ( get_user_meta($userid ,'user_mobile',true ) ) : 
				
								echo '<i class="glyphicon glyphicon-phone"></i> <a href="tel:'.str_replace(" ", "", get_user_meta($userid ,"user_mobile",true )).'">'.get_user_meta($userid ,'user_mobile',true )."</a><br>";
				
							 endif;
				
								echo  '<a href="mailto:'.$user_info->user_email.'">Email '. $user_info->first_name. '</a></p></div></div></div>';
								
								$counter++;	

						
					} //end full details
					else { 
								if ( function_exists('get_wp_user_avatar')){
								echo "<div class='col-lg-6 col-md-6 col-sm-6'><div class='indexcard'><a href='".site_url()."/staff/".$user_info->user_nicename."/'>".get_wp_user_avatar($userid,66,'left')."<strong>".$displayname."</strong>".$gradedisplay."<br>";
								} else {
								echo "<div class='col-lg-6 col-md-6 col-sm-6'><div class='indexcard'><a href='".site_url()."/staff/".$user_info->user_nicename."/'>".str_replace('avatar-66', 'avatar-66 pull-left indexcard-avatar', get_avatar($userid,66))."<strong>".$displayname."</strong>".$gradedisplay."<br>";
								}
							
								if ( get_user_meta($userid ,'user_job_title',true )) echo '<span class="small">'.get_user_meta($userid ,'user_job_title',true )."</span><br>";

								if ( get_user_meta($userid ,'user_telephone',true )) echo '<span class="small"><i class="glyphicon glyphicon-earphone"></i> '.get_user_meta($userid ,'user_telephone',true )."</span><br>";
								if ( get_user_meta($userid ,'user_mobile',true ) ) echo '<span class="small"><i class="glyphicon glyphicon-phone"></i> '.get_user_meta($userid ,'user_mobile',true )."</span>";
												
								echo "</div></div></a></i>";
								$counter++;	
					}	
	 				 			
	 			}
			?>
			</div>
</div>
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
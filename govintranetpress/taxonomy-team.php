<?php
/**
 * The template for displaying team pages.
 *
 */

$directorystyle = get_option('general_intranet_staff_directory_style'); // 0 = squares, 1 = circles
$showmobile = get_option('general_intranet_show_mobile_on_staff_cards'); // 1 = show
$fulldetails=get_option('general_intranet_full_detail_staff_cards');

get_header(); ?>
<div class="row">

<?php

	 
	if ( have_posts() )
		the_post();

		$slug = pods_url_variable(-1);
		$terms = get_term_by('slug',$slug,'team',ARRAY_A); 
		$teamname = govintranetpress_custom_title($terms['name']);
		$termid = $terms['term_id'];
		$teamparent = $terms['parent'];
		
		$taxteam = new Pod ('team', $termid);
		$teamleader = $taxteam->get_field('team_head');
		
		$alreadyshown=array();
		
?>
	<div class='breadcrumbs'>
		<a href="<?php echo site_url(); ?>">Home</a>
		&raquo; <a href="<?php echo site_url(); ?>/staff-directory/">Staff directory</a>
		&raquo; <?php echo $teamname; ?>
	</div>


		<div class="col-lg-12 col-md-12 col-sm-12">
		<div class="col-lg-8 col-md-8 col-sm-12">
			<h1>Staff directory</h1>
			<form class="form-horizontal" role="form" id="searchform2" name="searchform2" action="<?php echo site_url( '/search-staff/' ); ?>">
			  <div class="col-lg-12 col-md-12 col-sm-12">
				<div id="staff-search" class="well well-sm">
						<div class="input-group">
					    	 <input type="text" class="form-control pull-left" placeholder="Name, job title, skills, team, number..." name="q" id="s2" value="<?php echo $_GET['s'];?>">
							 <span class="input-group-btn">
								 <button class="btn btn-primary" type="submit"><i class="glyphicon glyphicon-search"></i></button>
							 </span>
							<?php
						  	$terms = get_terms('team',array('hide_empty'=>false,'parent' => '0',));
							if ($terms) {
								$otherteams='';
						  		foreach ((array)$terms as $taxonomy ) {
						  		    $themeid = $taxonomy->term_id;
						  		    $themeURL= $taxonomy->slug;
						  			$otherteams.= " <li><a href='".site_url()."/team/{$themeURL}/'>".govintranetpress_custom_title($taxonomy->name)."</a></li>";
						  		}  
						  		$teamdrop = get_option('general_intranet_team_dropdown_name');
						  		if ($teamdrop=='') $teamdrop = "Browse teams";
						  		echo "<div class='btn-group pull-right'><button type='button' class='btn btn-info dropdown-toggle4' data-toggle='dropdown'>".$teamdrop." <span class='caret'></span></button><ul class='dropdown-menu' role='menu'>".$otherteams."</ul></div><div class='btn-group pull-right'><button class='btn btn-link disabled'>Or&nbsp;</button></div>";
							}
							?>
						</div><!-- /input-group -->
				  </div>
				</div>
			</form>
		</div>
		<div class="row">
		<!--left blank-->
		</div>
<script>
jQuery("#s2").focus();
</script>							
	
<?php
		if ($teamparent){
			$parentteam = get_term_by('id',$teamparent,'team');
			echo "<div class='col-lg-12'><h3><i class='glyphicon glyphicon-chevron-left'></i> <a href='".site_url()."/team/".$parentteam->slug."'>".govintranetpress_custom_title($parentteam->name)."</a></h3></div>";
		}
?>	<div id="peoplenav">
			<div class='col-lg-12'><h2><?php echo $teamname; ?></h2><a id='teamtop' name='teamtop'>&nbsp;</a>
	</div>
		<?php
		$terms = get_terms('team',array('hide_empty'=>false,'parent' => $termid));
		if ($terms) {
			$teamstr = '';
	  		foreach ((array)$terms as $taxonomy ) {
	  		    $themeid = $taxonomy->term_id;
	  		    $themeURL= $taxonomy->slug;
	  			$teamstr.= "<a href='#{$themeURL}'>".govintranetpress_custom_title($taxonomy->name)."</a> <span class='glyphicon glyphicon-stop light small'> </span> ";
			}
			$teamstr=substr($teamstr, 0, -60);
			echo "<div class='col-lg-12 col-md-12 col-sm-12'><strong>Sub-teams: </strong><br>".$teamstr."</div>";
		}  

			//***********************************************************************************************
			//query all sub teams for this team
	 		$term_query = get_terms('team',array('hide_empty'=>false,'parent' => $termid,));	 		
 			$iteams = array();
 			$iteams[] = $termid;
 			$multipleteams = false;
 			foreach ($term_query as $tq){
	 			$iteams[] = $tq->term_id;
	 			$multipleteams = true;
 			}

 			//custom sql query returns users in the current team sorted by grade
			$chevron=0;
 			foreach ($iteams as $tq){
	 		
	 		$gradehead='';

			$newteam = get_term_by('id', $tq, 'team');//print_r($newteam);

			if ($chevron!=0){
				echo "<div class='col-lg-12 col-md-12 col-sm-12  home page'><div class='category-block'><h3>".govintranetpress_custom_title($newteam->name);
				echo " <a href='#teamtop'><span class='glyphicon glyphicon-chevron-up'></span></a>";	
				echo "<a id='{$newteam->slug}' name='{$newteam->slug}'>&nbsp;</a></h3></div></div>";
			} 
			$chevron=1;
 			
	 		$q = "select user_id from wp_usermeta join wp_terms on wp_terms.term_id = wp_usermeta.meta_value where user_id in (select user_id from wp_usermeta as a where a.meta_key = 'user_team' and a.meta_value = ".$tq." ) and meta_key = 'user_grade' ;
 "; 
 			$user_query = $wpdb ->get_results($q);
 			$counter=0;
 			$tcounter=0;
 			$uid = array();
 			$ugrade = array();
 			$uorder = array();
 			$ulastname = array();
 			
 			
 			foreach ($user_query as $u){//print_r($u);
	 			$uid[] = $u->user_id;
	 			$ulastname[] = get_user_meta($u->user_id,'last_name',true);
	 			$uorder[] = get_user_meta($u->user_id,'user_order',true);
	 			$g = get_user_meta($u->user_id,'user_grade',true); 
		 			$ugrade[] =  $g['slug'];		 			
	 			if ($g['parent']!=0){
		 			$g = get_term($g['parent'],'grade');
		 			$ugradeorig[] =  $g->slug;		 		
	 			}
 			}
 			
				
 			array_multisort($ugrade, $uorder, $ulastname, $uid);
 			
 			foreach ($uid as $u){//print_r($u);
 				$g = get_user_meta($u,'user_grade',false);
 				$l = get_user_meta($u,'last_name',false);
 				$alreadyshown[$u]=true;
 				
 				$userid =  $u;

				if ($userid ==  $teamleaderid) continue; //don't output if this person is the team head and already displayed


				$newgrade = get_user_meta($userid,'user_grade',true);

				if ($newgrade['slug']!=$gradehead) {
					$gradehead=$newgrade['slug'];
					echo "<div class='col-lg-12 col-md-12 col-sm-12 '><div class='home page'><h3 class='light'>".$newgrade['name']."</h3></div></div>";
					$counter=0;
				}
				

				$context = get_user_meta($userid,'user_job_title',true);
				if ($context=='') $context="staff";
				$icon = "user";			
				$user_info = get_userdata($userid);
				$userurl = site_url().'/staff/'.$user_info->user_nicename;
				$displayname = get_user_meta($userid ,'first_name',true )." ".get_user_meta($userid ,'last_name',true );					

				if (function_exists('get_wp_user_avatar_src')){
					$image_url_src = get_wp_user_avatar_src($userid, 'thumbnail'); 
					$avatarhtml = "<img src=".$image_url_src." width='66' height='66' alt='".$user_info->display_name."' class='img";
					$directorystyle = get_option('general_intranet_staff_directory_style'); // 0 = squares, 1 = circles
					if ($directorystyle==1){
						$avatarhtml.= ' img-circle';
					} 
					$avatarhtml.=" alignleft' />";
				} else {
						$avatarhtml = get_avatar($post->user_id,66);
						$avatarhtml = str_replace("photo", "photo alignleft", $avatarhtml);
				}
				if ($fulldetails){
						
						echo "<div class='col-lg-4 col-md-4 col-sm-6'><div class='media well well-sm'><a href='".site_url()."/staff/".$user_info->user_nicename."/'>".$avatarhtml."</a><div class='media-body'><p><a href='".site_url()."/staff/".$user_info->user_nicename."/'><strong>".$displayname."</strong></a><br>";

						// display team name(s)
						$poduser = new Pod ('user' , $userid);
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
							$tcounter++;	
					
				} //end full details
				else { 
					echo "<div class='col-lg-4 col-md-4 col-sm-6'><div class='indexcard'><a href='".site_url()."/staff/".$user_info->user_nicename."/'><div class='media'>".$avatarhtml."<div class='media-body'><strong>".$displayname."</strong><br>";
						// display team name(s)
						$poduser = new Pod ('user' , $userid);
						
							if ( get_user_meta($userid ,'user_job_title',true )) echo '<span class="small">'.get_user_meta($userid ,'user_job_title',true )."</span><br>";

							if ( get_user_meta($userid ,'user_telephone',true )) echo '<span class="small"><i class="glyphicon glyphicon-earphone"></i> '.get_user_meta($userid ,'user_telephone',true )."</span><br>";
							if ( get_user_meta($userid ,'user_mobile',true ) && $showmobile ) echo '<span class="small"><i class="glyphicon glyphicon-phone"></i> '.get_user_meta($userid ,'user_mobile',true )."</span>";
											
							echo "</div></div></div></div></a>";
							$counter++;	
				}	
 				 			
 			}
echo "<div class='col-lg-12 col-md-12 col-sm-12'>";
		//retrieve all staff for the team and sub teams including those without a grade
		//then display those not already shown as part of a grade		



//*** find ungraded staff

$q = "select distinct t1.user_id from wp_usermeta as t1
left outer join wp_terms on wp_terms.term_id = t1.meta_value 
WHERE t1.user_id in (select a.user_id from wp_usermeta as a where a.meta_key = 'user_team' and a.meta_value = ".$newteam->term_id." ) ";
		
		 $user_query = $wpdb ->get_results($q);

		 $oktoshow=false;
		 foreach ($user_query as $u){ // check for those already displayed
			 $uu = $u->user_id;
			 if (!$alreadyshown[$uu]){
			 	$oktoshow=true;
			 }
		 }
		 
		 if ($oktoshow){
					echo "<div class='col-lg-12 col-md-12 col-sm-12  home page'><div class='row'><h3 class='light'>Other</h3></div></div><div class='row'>";
		 }
		 foreach ($user_query as $u){ 
			 $uu = $u->user_id;
		 	if (!$alreadyshown[$uu]){
			 	
			 	//show remaining
 				
 				$userid =  $uu;

				if ($userid ==  $teamleaderid) continue; //don't output if this person is the team head and already displayed


				$context = get_user_meta($userid,'user_job_title',true);
				if ($context=='') $context="staff";
				$icon = "user";			
				$user_info = get_userdata($userid);
				$userurl = site_url().'/staff/'.$user_info->user_nicename;
				$displayname = get_user_meta($userid ,'first_name',true )." ".get_user_meta($userid ,'last_name',true );					


				if (function_exists('get_wp_user_avatar_src')){
					$image_url_src = get_wp_user_avatar_src($userid, 'thumbnail'); 
					$avatarhtml = "<img src=".$image_url_src." width='66' height='66' alt='".$post->title."' class='img";
					if ($directorystyle==1){
						$avatarhtml.= ' img-circle';
					} 
					$avatarhtml.=" alignleft' />";
				} else {
						$avatarhtml = get_avatar($userid,66);
						$avatarhtml = str_replace("photo", "photo alignleft", $avatarhtml);
					
				}


				if ($fulldetails){ //******************** INDEX CARD WITH INDIVIDUAL CLICKABLE LINKS
						
						echo "<div class='col-lg-4 col-md-4 col-sm-6'><div class='media well well-sm'><a href='".site_url()."/staff/".$user_info->user_nicename."/'>".$avatarhtml."</a><div class='media-body'><p><a href='".site_url()."/staff/".$user_info->user_nicename."/'><strong>".$displayname."</strong></a><br>";

						// display team name(s)

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
							$tcounter++;	
					
				} else { //******************** INDEX CARD ALONE IS CLICKABLE
					echo "<div class='col-lg-4 col-md-4 col-sm-6'><div class='indexcard'><a href='".site_url()."/staff/".$user_info->user_nicename."/'><div class='media'>".$avatarhtml."<div class='media-body'><strong>".$displayname."</strong><br>";
						// display team name(s)
						
							if ( get_user_meta($userid ,'user_job_title',true )) echo '<span class="small">'.get_user_meta($userid ,'user_job_title',true )."</span><br>";

							if ( get_user_meta($userid ,'user_telephone',true )) echo '<span class="small"><i class="glyphicon glyphicon-earphone"></i> '.get_user_meta($userid ,'user_telephone',true )."</span><br>";
							if ( get_user_meta($userid ,'user_mobile',true ) && $showmobile ) echo '<span class="small"><i class="glyphicon glyphicon-phone"></i> '.get_user_meta($userid ,'user_mobile',true )."</span>";
											
							echo "</div></div></div></div></a>";
							$counter++;	
				}	
 				 		 	}//endif


		 }
if ($oktoshow){
	echo "</div>";
}


//***



echo "</div>"; 			
		}//end for 			
		?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php get_footer(); ?>
<?php
/**
 * The template for displaying team pages.
 *
 */
$directory = get_option('options_module_staff_directory'); 
$directorystyle = get_option('options_staff_directory_style'); // 0 = squares, 1 = circles
$showmobile = get_option('options_show_mobile_on_staff_cards'); // 1 = show
$fulldetails = get_option('options_full_detail_staff_cards');

wp_register_script( 'masonry.pkgd.min', get_stylesheet_directory_uri() . "/js/masonry.pkgd.min.js");
wp_enqueue_script( 'masonry.pkgd.min',95 );

wp_register_script( 'imagesloaded.pkgd.min', get_stylesheet_directory_uri() . "/js/imagesloaded.pkgd.min.js");
wp_enqueue_script( 'imagesloaded.pkgd.min',94 );


get_header(); 

?>
<div class="row">

	<?php
	if ( have_posts() )
		the_post();

		$teamname = $post->post_title;
		$termid = $post->ID;
		$teamparent = $post->post_parent;
		$alreadyshown = array();
		
		?>

		<div class="col-lg-12 col-md-12 col-sm-12">
		<div class='breadcrumbs'>
			<?php if(function_exists('bcn_display') && !is_front_page()) {
				bcn_display();
				}?>
		</div>

			<div class="col-md-8 col-sm-12">
					<?php
					if ($teamparent){
						$parentteam = get_post($teamparent);
						echo "<h3><i class='dashicons dashicons-arrow-left-alt2'></i> <a href='".get_permalink($parentteam->ID)."'>".govintranetpress_custom_title($parentteam->post_title)."</a></h3>";
					}
					?>
					
					<?php if ( $directory ): ?>
					<h2><?php echo govintranetpress_custom_title($teamname); ?></h2>
					<?php endif; ?>
			
					<?php 
					wp_reset_postdata();
					the_content(); ?>
	
					<!--left blank-->

				
				<?php if ( $directory ): ?>
				<form class="form-horizontal" role="form" id="searchform2" name="searchform2" action="<?php echo site_url( '/search-staff/' ); ?>">
				  <div class="col-lg-12 col-md-12 col-sm-12">
					<div id="staff-search" class="well well-sm">
						<div class="input-group">
						    <input type="text" class="form-control pull-left" placeholder="Name, job title, skills, team, number..." name="q" id="s2" value="<?php echo the_search_query();?>">
							<span class="input-group-btn">
							<button class="btn btn-primary" type="submit"><i class="dashicons dashicons-search"></i></button>
							 </span>
							<?php
							$terms = get_posts('post_type=team&posts_per_page=-1&post_parent=0&orderby=title&order=ASC');
							if ($terms) {
								$otherteams='';
						  		foreach ((array)$terms as $taxonomy ) {
						  		    $themeid = $taxonomy->ID;
						  		    $themeURL= $taxonomy->post_name;
						  			$otherteams.= " <li><a href='".site_url()."/team/".$themeURL."/'>".govintranetpress_custom_title($taxonomy->post_title)."</a></li>";
						  		}  
						  		$teamdrop = get_option('options_team_dropdown_name');
						  		if ($teamdrop=='') $teamdrop = "Browse teams";
						  		echo "<div class='btn-group pull-right'><button type='button' class='btn btn-info dropdown-toggle4' data-toggle='dropdown'>".$teamdrop." <span class='caret'></span></button><ul class='dropdown-menu' role='menu'>".$otherteams."</ul></div><div class='btn-group pull-right'><button class='btn btn-link disabled'></button></div>";
							}
								?>
						</div><!-- /input-group -->
					  </div>
					</div>
				</form>

				<script type='text/javascript'>
				    jQuery(document).ready(function(){
						jQuery('#searchform2').submit(function(e) {
						    if (jQuery.trim(jQuery("#s2").val()) === "") {
						        e.preventDefault();
						        jQuery('#s2').focus();
						    }
						});	
					});	
				</script>

				<?php endif; ?>
		</div>

		<div class="col-md-4 col-sm-12">
			<?php
			wp_reset_postdata();
			$terms = get_posts('post_type=team&posts_per_page=-1&orderby=title&order=ASC&post_parent='.$post->ID);
			if ($terms) {
				$teamstr = '';
		  		foreach ((array)$terms as $taxonomy ) {
		  		    $themeid = $taxonomy->ID;
		  		    $themeURL= $taxonomy->post_name;
		  			$teamstr.= "<li><a href='/team/".$themeURL."/'>".govintranetpress_custom_title($taxonomy->post_title)."</a></li>";
				}
				echo "<div class='widget-box'><h3 class='widget-title'>Sub-teams</h3><ul>".$teamstr."</ul></div>";
			}  
			?>
		
		</div>
					<?php
						
					$teamleaderid = get_post_meta($id, 'team_lead', true);
					$counter=0;
					$tcounter=0;
					if ( $teamleaderid ):
						echo "<div class='col-sm-12'></div>";
					
						foreach ($teamleaderid as $userid){
							$alreadyshown[$userid] = true;	
							$context = get_user_meta($userid,'user_job_title',true);
							if ($context=='') $context="staff";
							$icon = "user";			
							$user_info = get_userdata($userid);
							$userurl = site_url().'/staff/'.$user_info->user_nicename;
							$displayname = get_user_meta($userid ,'first_name',true )." ".get_user_meta($userid ,'last_name',true );		
							if (function_exists('get_wp_user_avatar_src')){
								$image_url_src = get_wp_user_avatar_src($userid, 'thumbnail'); 
								$avatarhtml = "<img src=".$image_url_src." width='66' height='66' alt='".$user_info->display_name."' class='img";
								if ($directorystyle==1){
									$avatarhtml.= ' img-circle';
								} 
								$avatarhtml.=" alignleft' />";
							} else {
									$avstyle="";
									if ( $directorystyle==1 ) $avstyle = " img-circle";
									$avatarhtml = get_avatar($userid ,66);
									$avatarhtml = str_replace("photo", "photo alignleft".$avstyle, $avatarhtml);
							}
							if ($fulldetails){
									
								echo "<div class='col-lg-4 col-md-4 col-sm-6'><div class='media well well-sm'><a href='".site_url()."/staff/".$user_info->user_nicename."/'>".$avatarhtml."</a><div class='media-body'><p><a href='".site_url()."/staff/".$user_info->user_nicename."/'><strong>".$displayname."</strong></a><br>";
		
								// display team name(s)
								if ( get_user_meta($userid ,'user_job_title',true )) : 
									echo get_user_meta($userid ,'user_job_title',true )."<br>";
								endif;
								
								if ( get_user_meta($userid ,'user_telephone',true )) : 
					
									echo '<i class="dashicons dashicons-phone"></i> <a href="tel:'.str_replace(" ", "", get_user_meta($userid ,"user_telephone",true )).'">'.get_user_meta($userid ,'user_telephone',true )."</a><br>";
					
								endif; 
					
								if ( get_user_meta($userid ,'user_mobile',true ) && $showmobile ) : 
					
									echo '<i class="dashicons dashicons-smartphone"></i> <a href="tel:'.str_replace(" ", "", get_user_meta($userid ,"user_mobile",true )).'">'.get_user_meta($userid ,'user_mobile',true )."</a><br>";
					
								 endif;
					
								echo  '<a href="mailto:'.$user_info->user_email.'">Email '. $user_info->first_name. '</a></p></div></div></div>';
								
								$counter++;	
								$tcounter++;	
								
							 //end full details
							} else { 
								echo "<div class='col-lg-4 col-md-4 col-sm-6'><div class='indexcard'><a href='".site_url()."/staff/".$user_info->user_nicename."/'><div class='media'>".$avatarhtml."<div class='media-body'><strong>".$displayname."</strong><br>";
									
								if ( get_user_meta($userid ,'user_job_title',true )) echo '<span class="small">'.get_user_meta($userid ,'user_job_title',true )."</span><br>";
		
								if ( get_user_meta($userid ,'user_telephone',true )) echo '<span class="small"><i class="dashicons dashicons-phone"></i> '.get_user_meta($userid ,'user_telephone',true )."</span><br>";
								if ( get_user_meta($userid ,'user_mobile',true ) && $showmobile ) echo '<span class="small"><i class="dashicons dashicons-smartphone"></i> '.get_user_meta($userid ,'user_mobile',true )."</span>";
												
								echo "</div></div></div></div></a>";
								$counter++;	
							}
						}
						echo "<div class='col-sm-12'><hr></div>";
					else:
						$teamleaderid = array();
					endif;
					?>		
		<div class="col-sm-12" >
			<div id="gridcontainer" class="row">
				<div class="grid-sizer"></div>

					<?php
					echo "<div class='col-sm-12'>";
					//***********************************************************************************************
					//query all sub teams for this team
			 		$term_query = get_posts('post_type=team&posts_per_page=-1&orderby=title&order=ASC&post_parent='.$post->ID);	 		
		 			$iteams = array();
		 			$iteams[] = $post->ID;
		 			$multipleteams = false;
		 			if ( $term_query ) foreach ($term_query as $tq){
			 			$iteams[] = $tq->ID;
			 			$multipleteams = true;
		 			}
		
					$chevron=0;
		 			$counter=0;
		 			$tcounter=0;
		 			$uid = array();
		 			$ugrade = array();
		 			$uorder = array();
		 			$ulastname = array(); 			

		 			if ( $iteams ) foreach ($iteams as $tq){
				 		$gradehead='';
						$newteam = get_post( $tq ); 
						$chevron=1;
			 			$user_query = new WP_User_Query(array('meta_query'=>array(array('key'=>'user_team','value'=>$tq,'compare'=>'LIKE'))));
			 			if ( $user_query ) foreach ($user_query->results as $u){ 
				 			$uid[] = $u->ID;
				 			$ulastname[] = get_user_meta($u->ID,'last_name',true);
				 			$uorder[] = intval(get_user_meta($u->ID,'user_order',true));
			 			}
		 			}

		 			array_multisort( $uorder, $ulastname, $uid);
		 			if ( $uid ) foreach ($uid as $u){ 
		 				if ( isset( $alreadyshown[$u] ) ) continue;
		 				$alreadyshown[$u] = true;
		 				$userid = $u;
		
						if ( isset( $teamleaderid ) && in_array( $userid, $teamleaderid ) ) continue; //don't output if this person is the team head and already displayed
		
						$context = get_user_meta($userid,'user_job_title',true);
						if ($context=='') $context="staff";
						$icon = "user";			
						$user_info = get_userdata($userid);
						$userurl = site_url().'/staff/'.$user_info->user_nicename;
						$displayname = get_user_meta($userid ,'first_name',true )." ".get_user_meta($userid ,'last_name',true );		
						if (function_exists('get_wp_user_avatar_src')){
							$image_url_src = get_wp_user_avatar_src($userid, 'thumbnail'); 
							$avatarhtml = "<img src=".$image_url_src." width='66' height='66' alt='".$user_info->display_name."' class='img";
							$directorystyle = get_option('options_staff_directory_style'); // 0 = squares, 1 = circles
							if ($directorystyle==1){
								$avatarhtml.= ' img-circle';
							} 
							$avatarhtml.=" alignleft' />";
						} else {
								$avstyle="";
								if ( $directorystyle==1 ) $avstyle = " img-circle";
								$avatarhtml = get_avatar($userid ,66);
								$avatarhtml = str_replace("photo", "photo alignleft".$avstyle, $avatarhtml);
						}
						if ($fulldetails){
								
							echo "<div class='col-lg-4 col-md-4 col-sm-6'><div class='media well well-sm'><a href='".site_url()."/staff/".$user_info->user_nicename."/'>".$avatarhtml."</a><div class='media-body'><p><a href='".site_url()."/staff/".$user_info->user_nicename."/'><strong>".$displayname."</strong></a><br>";
	
							// display team name(s)
							if ( get_user_meta($userid ,'user_job_title',true )) : 
								echo get_user_meta($userid ,'user_job_title',true )."<br>";
							endif;
							
							if ( get_user_meta($userid ,'user_telephone',true )) : 
				
								echo '<i class="dashicons dashicons-phone"></i> <a href="tel:'.str_replace(" ", "", get_user_meta($userid ,"user_telephone",true )).'">'.get_user_meta($userid ,'user_telephone',true )."</a><br>";
				
							endif; 
				
							if ( get_user_meta($userid ,'user_mobile',true ) && $showmobile ) : 
				
								echo '<i class="dashicons dashicons-smartphone"></i> <a href="tel:'.str_replace(" ", "", get_user_meta($userid ,"user_mobile",true )).'">'.get_user_meta($userid ,'user_mobile',true )."</a><br>";
				
							 endif;
				
							echo  '<a href="mailto:'.$user_info->user_email.'">Email '. $user_info->first_name. '</a></p></div></div></div>';
							
							$counter++;	
							$tcounter++;	
							
						 //end full details
						} else { 
							echo "<div class='col-lg-4 col-md-4 col-sm-6 pgrid-item'><div class='indexcard'><a href='".site_url()."/staff/".$user_info->user_nicename."/'><div class='media'>".$avatarhtml."<div class='media-body'><strong>".$displayname."</strong><br>";
								
							if ( get_user_meta($userid ,'user_job_title',true )) echo '<span class="small">'.get_user_meta($userid ,'user_job_title',true )."</span><br>";
	
							if ( get_user_meta($userid ,'user_telephone',true )) echo '<span class="small"><i class="dashicons dashicons-phone"></i> '.get_user_meta($userid ,'user_telephone',true )."</span><br>";
							if ( get_user_meta($userid ,'user_mobile',true ) && $showmobile ) echo '<span class="small"><i class="dashicons dashicons-smartphone"></i> '.get_user_meta($userid ,'user_mobile',true )."</span>";
											
							echo "</div></div></div></div></a>";
							$counter++;	
						}	
						
					}			
		 			?>

				</div>
			</div>
		</div>
	</div>
</div>
<script>
jQuery(document).ready(function($){
var $container = jQuery('#gridcontainer');
$container.imagesLoaded(function(){
$container.masonry({
		itemSelector: '.pgrid-item',
		gutter: 0,
		isAnimated: true
});
});
});
</script>

<?php get_footer(); ?>
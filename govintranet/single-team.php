<?php
/**
 * The template for displaying team pages.
 *
 */
get_header(); 
$directory = get_option('options_module_staff_directory'); 
$directorystyle = get_option('options_staff_directory_style'); // 0 = squares, 1 = circles
$showmobile = get_option('options_show_mobile_on_staff_cards'); // 1 = show
$fulldetails = get_option('options_full_detail_staff_cards');

wp_register_script( 'masonry.pkgd.min', get_stylesheet_directory_uri() . "/js/masonry.pkgd.min.js");
wp_enqueue_script( 'masonry.pkgd.min',95 );

wp_register_script( 'imagesloaded.pkgd.min', get_stylesheet_directory_uri() . "/js/imagesloaded.pkgd.min.js");
wp_enqueue_script( 'imagesloaded.pkgd.min',94 );

?>

<a class="sr-only sr-only-focusable" href="#gridcontainer"><?php _e('Skip to staff' , 'govintranet'); ?></a>

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
					
					<h2><?php echo govintranetpress_custom_title($teamname); ?></h2>
		
				<?php 
				wp_reset_postdata();
				the_content(); ?>

				<!--left blank-->

				<?php if ( $directory ): ?>
				<div class="col-sm-12 well well-sm">
					<div id="staff-search">
						<div class="col-sm-8">
							<form class="form-horizontal" role="form" id="searchform2" name="searchform2" action="<?php if ( function_exists('relevanssi_do_query') ) { echo "/"; } else { echo site_url( '/search-staff/' ); } ?>">
								<div class="input-group">
								<label for="s2" class="sr-only"><?php _e('Search staff' , 'govintranet'); ?></label>
								<input type="text" class="form-control pull-left" placeholder="<?php _e('Name, job title, skills, team, number...' , 'govintranet'); ?>" name="<?php if ( function_exists('relevanssi_do_query') ) { echo "s"; } else { echo "q"; } ?>" id="s2">
								<input type="hidden" name="include" value="user">
								<input type="hidden" name="post_types[]" value="team">
								<span class="input-group-btn">
								<label for="searchbutton2" class="sr-only"><?php _e('Search' , 'govintranet'); ?></label>
								<button class="btn btn-primary" type="submit" id="searchbutton2"><i class="dashicons dashicons-search"></i></button>
							 	</span>
								</div><!-- /input-group -->
							</form>
						</div>
						<div class="col-sm-4">
						<?php
							$teams = get_posts('post_type=team&posts_per_page=-1&post_parent=0&orderby=title&order=ASC');
							if ($teams) {
								$otherteams='';
						  		foreach ((array)$teams as $team ) {
						  		    $themeid = $team->ID;
						  		    $themeURL= $team->post_name;
						  			$otherteams.= " <li><a href='".get_permalink($team->ID)."'>".govintranetpress_custom_title($team->post_title)."</a></li>";
						  		}  
						  		$teamdrop = get_option('options_team_dropdown_name');
						  		if ($teamdrop=='') $teamdrop = __("Browse teams" , "govintranet");
						  		echo '
								<div class="dropdown">
								  <button class="btn btn-info pull-right dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
								    ' . $teamdrop . '
								    <span class="caret"></span>
								  </button>
								  <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1">' . $otherteams . '
								  </ul>
								</div>					
								';
							}
							?>
						</div>
				  	</div>
				</div>

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
				$teams = get_posts('post_type=team&posts_per_page=-1&orderby=menu_order , title&order=ASC&post_parent='.$post->ID);
				if ($teams) {
					$teamstr = '';
			  		foreach ((array)$teams as $team ) {
			  			$teamstr.= "<li><a href='".get_permalink($team->ID)."'>".govintranetpress_custom_title($team->post_title)."</a></li>";
					}
					echo "<div class='widget-box'><h3 class='widget-title'>" . __('Sub-teams','govintranet') . "</h3><ul>".$teamstr."</ul></div>";
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
					$userurl = site_url().'/users/'.$user_info->user_nicename;
					$displayname = get_user_meta($userid ,'first_name',true )." ".get_user_meta($userid ,'last_name',true );		
					$staffdirectory = get_option('options_module_staff_directory');
					if (function_exists('bp_activity_screen_index')){ // if using BuddyPress - link to the members page
						$userurl=str_replace('/author', '/members', $userurl); }
					elseif (function_exists('bbp_get_displayed_user_field') && $staffdirectory ){ // if using bbPress - link to the staff page
						$userurl=str_replace('/author', '/staff', $userurl);
					}
					$avstyle="";
					if ( $directorystyle==1 ) $avstyle = " img-circle";
					$avatarhtml = get_avatar($userid ,66);
					$avatarhtml = str_replace("photo", "photo alignleft".$avstyle, $avatarhtml);
					if ($fulldetails){
							
						echo "<div class='col-lg-4 col-md-6 col-sm-6'><div class='media well well-sm'><a href='".$userurl."'>".$avatarhtml."</a><div class='media-body'><p><a href='".$userurl."'><strong>".$displayname."</strong></a><br>";

						if ( get_user_meta($userid ,'user_job_title',true )) : 
							$meta = get_user_meta($userid ,'user_job_title',true );
							echo '<span class="small">'.$meta."</span><br>";
						endif; 
						
						if ( get_user_meta($userid ,'user_telephone',true )) echo '<span class="small"><i class="dashicons dashicons-phone"></i> '.get_user_meta($userid ,'user_telephone',true )."</span><br>";
						if ( get_user_meta($userid ,'user_mobile',true ) && $showmobile ) echo '<span class="small"><i class="dashicons dashicons-smartphone"></i> '.get_user_meta($userid ,'user_mobile',true )."</span><br>";
						echo '<span class="small"><a href="mailto:'.$user_info->user_email.'">Email '. $user_info->first_name. '</a></small></p></div></div></div>';
						
						$counter++;	
						$tcounter++;	
						
						//end full details

					} else { 

						echo "<div class='col-lg-4 col-md-4 col-sm-6'><div class='indexcard'><a href='".$userurl."'><div class='media'>".$avatarhtml."<div class='media-body'><strong>".$displayname."</strong><br>";
							
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
						if ( isset( $teamleaderid ) && in_array( $userid, $teamleaderid ) ) continue; 
						//don't output if this person is the team head and already displayed
						$context = get_user_meta($userid,'user_job_title',true);
						if ($context=='') $context="staff";
						$icon = "user";			
						$user_info = get_userdata($userid);
						$userurl = site_url().'/users/'.$user_info->user_nicename;
						$displayname = get_user_meta($userid ,'first_name',true )." ".get_user_meta($userid ,'last_name',true );		
						$avstyle="";
						if ( $directorystyle==1 ) $avstyle = " img-circle";
						$avatarhtml = get_avatar($userid ,66);
						$avatarhtml = str_replace(" photo", " photo alignleft".$avstyle, $avatarhtml);
						if ($fulldetails) {
								
							echo "<div class='col-lg-4 col-md-6 col-sm-6 col-xs-12 pgrid-item'><div class='media well well-sm'><a href='".$userurl."'>".$avatarhtml."</a><div class='media-body'><p><a href='".$userurl."'><strong>".$displayname."</strong>".$gradedisplay."</a><br>";

							// display team name(s)
							$team = get_user_meta($userid ,'user_team',true );
							if ($team) {				
								foreach ((array)$team as $t ) { 
									if ( $t == $post->ID ) continue;
						  		    $theme = get_post($t);
									echo "<a href='".get_permalink($theme->ID)."'>".govintranetpress_custom_title($theme->post_title)."</a><br>";
						  		}
							}  
							?>

						<?php if ( get_user_meta($userid ,'user_job_title',true )) : 
									$meta = get_user_meta($userid ,'user_job_title',true );
									echo '<span class="small">'.$meta."</span><br>";
							endif; 
							
							if ( get_user_meta($userid ,'user_telephone',true )) echo '<span class="small"><i class="dashicons dashicons-phone"></i> '.get_user_meta($userid ,'user_telephone',true )."</span><br>";
							if ( get_user_meta($userid ,'user_mobile',true ) && $showmobile ) echo'<span class="small"><i class="dashicons dashicons-smartphone"></i> '.get_user_meta($userid ,'user_mobile',true )."</span><br>";
				
							echo '<span class="small"><a href="mailto:'.$user_info->user_email.'">' . __('Email' , 'govintranet') . ' ' . $user_info->first_name. '</a></small></p></div></div></div>';

							$counter++;	
							$tcounter++;	
							
						 //end full details
						} else { 

							echo "<div class='col-lg-4 col-md-4 col-sm-6 pgrid-item'><div class='indexcard'><a href='".$userurl."'><div class='media'>".$avatarhtml."<div class='media-body'><strong>".$displayname."</strong><br>";

							// display team name(s)
							$team = get_user_meta($userid ,'user_team',true );
							if ($team) {				
								foreach ((array)$team as $t ) { 
									if ( $t == $post->ID ) continue;
						  		    $theme = get_post($t);
									echo govintranetpress_custom_title($theme->post_title)."<br>";
		
						  		}
							}  
								
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
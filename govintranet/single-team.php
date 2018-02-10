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

wp_enqueue_script( 'jquery-masonry','',array('jquery'),'',true );
wp_register_script( 'scripts_search', get_template_directory_uri() . '/js/ht-scripts-search.js','' ,'' ,true );
wp_enqueue_script( 'scripts_search' );
wp_register_script( 'scripts_grid', get_template_directory_uri() . '/js/ht-scripts-grid.js',array('jquery-masonry'),'' ,true );
wp_enqueue_script( 'scripts_grid' );

?>

<a class="sr-only sr-only-focusable" href="#gridcontainer"><?php _e('Skip to staff' , 'govintranet'); ?></a>

<div class="row">

	<?php
	if ( have_posts() )
		the_post();

		$teamname = get_the_title($post->ID);
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
				<article class="clearfix">	
				<?php
				if ($teamparent){
					$parentteam = get_post($teamparent);
					echo "<h3><i class='dashicons dashicons-arrow-left-alt2'></i> <a href='".get_permalink($parentteam->ID)."'>".get_the_title($parentteam->ID)."</a></h3>";
				}
				?>
				<h2><?php echo $teamname; ?></h2>
		
				<?php 
				wp_reset_postdata();
				the_content(); ?>
				</article>
				<!--left blank-->

				<?php if ( $directory ): ?>
				<div class="col-sm-12 well well-sm" id="staff-search">
					<div >
						<div class="col-sm-8">
							<form class="form-horizontal" id="searchform2" name="searchform2" action="<?php if ( function_exists('relevanssi_do_query') ) { echo site_url("/"); } else { echo site_url( '/search-staff/' ); } ?>">
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
						  			$otherteams.= " <li><a href='".get_permalink($team->ID)."'>".get_the_title($team->ID)."</a></li>";
						  		}  
						  		$teamdrop = get_option('options_team_dropdown_name');
						  		if ($teamdrop=='') $teamdrop = __("Browse teams" , "govintranet");
						  		echo '
								<div class="dropdown">
								  <button class="btn btn-primary pull-right dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
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
			<?php endif; ?>

			</div>

			<div class="col-md-4 col-sm-12" id="sidebar">
				<h2 class="sr-only">Sidebar</h2>
				<?php
				wp_reset_postdata();
				
				$parents = array();
				$pid = $post->ID;
				while ( $pid ){
					$parents[] = $pid;
					$newp = get_post($pid);
					$pid = $newp->post_parent;
				}
				
				$teams = get_posts(array(
					'post_type'=>'team',
					'posts_per_page'=>-1,
					'orderby'=>'menu_order , title',
					'order'=>'ASC',
					'post_parent'=>$post->ID
					)
				);
					
				if ($teams) {
					$teamstr = '';
			  		foreach ((array)$teams as $team ) {
			  			$teamstr.= "<li><a href='".get_permalink($team->ID)."'>".get_the_title($team->ID)."</a></li>";
					}
					echo "<div class='widget-box'><h3 class='widget-title'>" . __('Sub-teams','govintranet') . "</h3><ul>".$teamstr."</ul></div>";
				}  
				?>
			</div>
			
			<?php
			$teamleaderid = get_post_meta($id, 'team_lead', true);
			$counter=0;
			$tcounter=0;
			$tl_text = '';
			$tl_valid = false;
			
			if ( $teamleaderid ):
				$tl_text.= "<div class='col-sm-12'></div>";
			
				foreach ($teamleaderid as $userid){
					$live_user = get_user_by( 'ID', $userid );
					if (!$live_user ) continue;
					$alreadyshown[$userid] = true;	
					$tl_valid = true;
					$context = get_user_meta($userid,'user_job_title',true);
					if ($context=='') $context="staff";
					$icon = "user";			
					$user_info = get_userdata($userid);
					$userurl = gi_get_user_url($userid);
					$displayname = get_user_meta($userid ,'first_name',true )." ".get_user_meta($userid ,'last_name',true );		
					$staffdirectory = get_option('options_module_staff_directory');
					$avstyle="";
					if ( $directorystyle==1 ) $avstyle = " img-circle";
					$avatarhtml = get_avatar($userid ,66);
					$avatarhtml = str_replace("photo", "photo alignleft".$avstyle, $avatarhtml);
					if ($fulldetails){
							
						$tl_text.= "<div class='col-lg-4 col-md-6 col-sm-6'><div class='media well well-sm'><a href='".$userurl."'>".$avatarhtml."</a><div class='media-body'><p><a href='".$userurl."'><strong>".$displayname."</strong></a><br>";

						if ( get_user_meta($userid ,'user_job_title',true )) : 
							$meta = get_user_meta($userid ,'user_job_title',true );
							$tl_text.= '<span class="small">'.$meta."</span><br>";
						endif; 
						
						if ( get_user_meta($userid ,'user_telephone',true )) $tl_text.= '<span class="small"><i class="dashicons dashicons-phone"></i> '.get_user_meta($userid ,'user_telephone',true )."</span><br>";
						if ( get_user_meta($userid ,'user_mobile',true ) && $showmobile ) $tl_text.= '<span class="small"><i class="dashicons dashicons-smartphone"></i> '.get_user_meta($userid ,'user_mobile',true )."</span><br>";
						$tl_text.= '<span class="small"><a href="mailto:'.$user_info->user_email.'">Email '. $user_info->first_name. '</a></span></div></div></div>';
						
						$counter++;	
						$tcounter++;	
						
						//end full details

					} else { 

						$tl_text.= "<div class='col-lg-4 col-md-4 col-sm-6'><div class='indexcard'><a href='".$userurl."'><div class='media'>".$avatarhtml."<div class='media-body'><strong>".$displayname."</strong><br>";
							
						if ( get_user_meta($userid ,'user_job_title',true )) $tl_text.= '<span class="small">'.get_user_meta($userid ,'user_job_title',true )."</span><br>";

						if ( get_user_meta($userid ,'user_telephone',true )) $tl_text.= '<span class="small"><i class="dashicons dashicons-phone"></i> '.get_user_meta($userid ,'user_telephone',true )."</span><br>";
						if ( get_user_meta($userid ,'user_mobile',true ) && $showmobile ) $tl_text.= '<span class="small"><i class="dashicons dashicons-smartphone"></i> '.get_user_meta($userid ,'user_mobile',true )."</span>";
										
						$tl_text.= "</div></div></div></div></a>";
						$counter++;	
					}
				}
				$tl_text.= "<div class='col-sm-12'><hr></div>";
				if ( $tl_valid === true ):
					 echo $tl_text; 
				endif;

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
					$term_query = get_pages(array(
						'post_type'=>'team',
						'post_status'=>'publish',
						'child_of'=> $post->ID,
						'hierarchical'=>true,
						'posts_per_page'=>-1
						)
					);

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
		 			$ufname = array(); 			

		 			if ( $iteams ) foreach ($iteams as $tq){
				 		$gradehead='';
						$newteam = get_post( $tq ); 
						$chevron=1;
			 			$user_query = new WP_User_Query(array('fields'=>'ID','meta_query'=>array(array('key'=>'user_team','value'=>'.*\"'.$tq.'\".*','compare'=>'REGEXP'))));
			 			if ( $user_query ) foreach ($user_query->results as $u){ 
				 			$uid[] = $u;
				 			$ufname[] = get_user_meta($u,'first_name',true);
				 			$ulastname[] = get_user_meta($u,'last_name',true);
				 			$uorder[] = intval(get_user_meta($u,'user_order',true));
			 			}
		 			}
		 			
		 			array_multisort( $uorder, $ulastname, $ufname, $uid);
		 			if ( $uid ) foreach ($uid as $u){ 
		 				if ( isset( $alreadyshown[$u] ) ) continue;
		 				if ( get_user_meta($u, 'user_hide', true ) ) continue; 
		 				$alreadyshown[$u] = true;
		 				$userid = $u;
						if ( isset( $teamleaderid ) && in_array( $userid, $teamleaderid ) ) continue; 
						//don't output if this person is the team head and already displayed
						$context = get_user_meta($userid,'user_job_title',true);
						if ($context=='') $context="staff";
						$icon = "user";			
						$user_info = get_userdata($userid);
						$userurl = gi_get_user_url($userid);
						$displayname = get_user_meta($userid ,'last_name',true ).", ".get_user_meta($userid ,'first_name',true );		
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
									echo "<a href='".get_permalink($theme->ID)."'>".get_the_title($theme->ID)."</a><br>";
						  		}
							}  

							if ( get_user_meta($userid ,'user_job_title',true )) : 
									$meta = get_user_meta($userid ,'user_job_title',true );
									echo '<span class="small">'.$meta."</span><br>";
							endif; 
							
							if ( get_user_meta($userid ,'user_telephone',true )) echo '<span class="small"><i class="dashicons dashicons-phone"></i> '.get_user_meta($userid ,'user_telephone',true )."</span><br>";
							if ( get_user_meta($userid ,'user_mobile',true ) && $showmobile ) echo'<span class="small"><i class="dashicons dashicons-smartphone"></i> '.get_user_meta($userid ,'user_mobile',true )."</span><br>";
				
							echo '<span class="small"><a href="mailto:'.$user_info->user_email.'">' . __('Email' , 'govintranet') . ' ' . $user_info->first_name. '</a></span></p></div></div></div>';

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
									echo get_the_title($theme->ID)."<br>";
						  		}
							}  
							if ( get_user_meta($userid ,'user_job_title',true )) echo '<span class="small">'.get_user_meta($userid ,'user_job_title',true )."</span><br>";
							if ( get_user_meta($userid ,'user_telephone',true )) echo '<span class="small"><i class="dashicons dashicons-phone"></i> '.get_user_meta($userid ,'user_telephone',true )."</span><br>";
							if ( get_user_meta($userid ,'user_mobile',true ) && $showmobile ) echo '<span class="small"><i class="dashicons dashicons-smartphone"></i> '.get_user_meta($userid ,'user_mobile',true )."</span>";
							echo "</div></div></a></div></div>";
							$counter++;	
						}	
					}			
		 			?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php get_footer(); ?>
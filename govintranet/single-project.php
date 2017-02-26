<?php
/**
 * The Template for displaying all single projects.
 *
 * @package WordPress
 */

get_header(); 

if ( have_posts() ) while ( have_posts() ) : 
	the_post(); 
	$chapter_header = false;
	$singletask = false;
	$pagetype = "";
	$parent_guide = "";
	$taskpod = get_post($id);
	$current_task = $id;
	if ($post->post_parent != 0){
		$parent_guide = get_post($post->post_parent); 
		if ($parent_guide){
			$parent_guide_id = $parent_guide->ID; 
			if (!$parent_guide_id){
				$parent_guide_id = $post->ID;
			}
		}
	} else {
		$parent_guide_id = $post->ID;
	}
	$children_chapters = get_posts ("post_type=project&posts_per_page=-1&post_status=publish&post_parent=".$parent_guide_id."&orderby=menu_order&order=ASC");
	
	if (!$parent_guide && !$children_chapters){
		$singletask=true;
		$pagetype = "task";
		$icon = "chart-bar";
	} else {
		$pagetype = "guide";
		$icon = "chart-bar";
	};

	if ($children_chapters && !$parent_guide){
		$chapter_header=true;
	}

	if ($parent_guide){
		$parent_slug=$parent_guide->post_name;
		$parent_name=govintranetpress_custom_title($parent_guide->post_title); 
		$guidetitle =$parent_name;	
	}

	if (!$parent_guide){
		$guidetitle = govintranetpress_custom_title($post->post_title);
	}	
	?>

	<div class="col-lg-8 col-md-8 col-sm-8">
		<div class="row">
			<div class='breadcrumbs'>
				<?php if(function_exists('bcn_display') && !is_front_page()) {
					bcn_display();
					}?>
			</div>
		</div>
		<?php 

		if ($pagetype=="guide"):

		?>
		<div>
			<h1><?php echo $guidetitle; ?> <small><span class="dashicons dashicons-<?php echo $icon; ?>"></span> <?php echo _x('Project' , 'noun' , 'govintranedt'); ?></small></h1>
			<?php 
			$podchap = get_post($parent_guide_id); 
			$alreadydone[]=$parent_guide_id;
			$children_chapters = get_posts("post_type=project&posts_per_page=-1&post_status=publish&post_parent=".$parent_guide_id."&orderby=menu_order&order=ASC");
			$totalchapters = count($children_chapters) + 1;
			$halfchapters = round($totalchapters/2,0,PHP_ROUND_HALF_UP); 
			?>
			<div class="chapters-container">
				<div class="col-lg-6 col-md-6">
					<div class="chapters">
						<nav role="navigation" class="page-navigation">
							<ol>
								<?php
									if ($chapter_header){
										echo "<li class='active'>";
										echo "<span class='part-label part-title'>".$guidetitle."</span>";
									} else {
										$chapname = $parent_name;
										$chapslug = $parent_slug;
										echo "<li><a href='".get_permalink($parent_guide_id)."'><span class='part-title'>{$chapname}</span></a>";
									}
									echo "</li>";
									$carray = array();
									$k=1; 
									foreach ($children_chapters as $chapt){
										if ($chapt->post_status=='publish'){
										$k++;
									if (($k == $halfchapters + 1) && ($totalchapters > 3) ):?>
							</ol>
						</nav>
					</div>
				</div>
				<div class="col-lg-6 col-md-6">
					<div class="chapters">
						<nav role="navigation" class="page-navigation">
							<ol start='<?php echo $k;?>'>
									
								<?php
								endif;
				
								echo "<li ";
								if ($id == $chapt->ID){
									 echo "class='active'";
									 $current_chapter=$k;
								}
								echo ">";
								$chapname = govintranetpress_custom_title($chapt->post_title);
								$chapslug = $chapt->post_name; 
								$carray[$k]['chapter_number']=$k;
								$carray[$k]['slug']=$chapslug;
								$carray[$k]['name']=$chapname;
								$carray[$k]['id']=$chapt->ID;
								$alreadydone[]=$chapt->ID;
								if ($chapt->ID==$current_task){
									echo "<span class='part-label part-title'>{$chapname}</span>";
								} else {
									echo "<a href='".get_permalink($chapt->ID)."'><span class='part-label part-title'>{$chapname}</span></a>";
								}
								echo "</li>";
								}
							}
							?>
						</ol>
					</nav>
				</div>
			</div>
		</div>
	</div>
	
	<?php
				
	endif;
	
	if ($pagetype=="guide"){
		echo "<div>";

		echo "<div class='content-wrapper-notop'>";
		if ($current_chapter>1){
			echo "<h2>".$current_chapter.". ".get_the_title()."</h2>";
		} else {
			echo "<h2>" . __('Overview' , 'govintranet') . "</h2>";
		}

		the_content(); 		
		?>
		<?php get_template_part("part", "downloads"); ?>			

		<?php
		if ('open' == $post->comment_status) {
			 comments_template( '', true ); 
		}
		echo "</div>";
		
        echo '<div class="row">';

        if ($chapter_header){ // if on chapter 1
			
			echo '<div class="col-lg-12 chapterr"><a href="'.get_permalink($carray[2]["id"]).'" title="'. esc_attr__("Navigate to next part" , "govintranet") .'">'.$carray[2]["name"].'&nbsp;<span class="dashicons dashicons-arrow-right-alt2"></span></a></div>';
			
        } elseif ($current_chapter==2) { // if on chapter 2

			echo '<div class="col-lg-6 col-md-6 chapterl"><a href="'.get_permalink($parent_guide_id).'" title="' . esc_attr__("Navigate to previous part" ,"govintranet") . '"><span class="dashicons dashicons-arrow-left-alt2"></span>&nbsp;' . __("Overview" , "govintranet") . '</a></div>';
            if ($carray[3]['slug']){
				echo '<div class="col-lg-6 col-md-6 chapterr"><a href="'.get_permalink($carray[3]["id"]).'" title="'.esc_attr__( "Navigate to next part" , "govintranet").'">'.$carray[3]["name"].'&nbsp;<span class="dashicons dashicons-arrow-right-alt2"></span></a></div>';
	        }
        }   else { // we're deep in the middle somewhere
        	$previous_chapter = $current_chapter-1; 
			$next_chapter = $current_chapter+1;

			echo '<div class="col-lg-6 col-md-6 chapterl"><a href="'.get_permalink($carray[$previous_chapter]["id"]).'" title="'. esc_attr__("Navigate to previous part" , "govintranet") .'"><span class="dashicons dashicons-arrow-left-alt2"></span>&nbsp;'.govintranetpress_custom_title($carray[$previous_chapter]["name"]).'</a></div>';

            if ($carray[$next_chapter]['slug']){
				echo '<div class="col-lg-6 col-md-6 chapterr"><a href="'.get_permalink($carray[$next_chapter]["id"]).'" title="'. esc_attr__("Navigate to next part" , "govintranet") .'">'.govintranetpress_custom_title($carray[$next_chapter]["name"]).'&nbsp;<span class="dashicons dashicons-arrow-right-alt2"></span></a></div>';
			}
		}
		echo "</div>";
		echo "</div>";

		} else { ?>
			<h1><?php echo $guidetitle; ?> <small><span class="dashicons dashicons-<?php echo $icon; ?>"></span> <?php echo _x('Project' , 'noun' , 'govintranet') ; ?></small></h1>
			<?php
			the_content(); 

			$members = get_post_meta($post->ID, 'project_team_members', true);
			
			if ( $members ) {
				$directorystyle = get_option('options_staff_directory_style'); // 0 = squares, 1 = circles
				$showmobile = get_option('options_show_mobile_on_staff_cards'); // 1 = show
				echo "<div id='project_teams' class='row'><div class='col-lg-12 col-md-12 col-sm-12'><h2>" . __('Project team','govintranet') . "</h2></div>";
				foreach ($members as $userid){
					$context = get_user_meta($userid,'user_job_title',true);
					if ($context=='') $context="staff";
					$icon = "user";			
					$user_info = get_userdata($userid);
					$userurl = site_url().'/users/'.$user_info->user_nicename;
					$displayname = get_user_meta($userid ,'first_name',true )." ".get_user_meta($userid ,'last_name',true );		
					$staffdirectory = get_option('options_module_staff_directory');
					if (function_exists('bp_activity_screen_index')){ // if using BuddyPress - link to the members page
						$userurl=str_replace('/users', '/members', $userurl); }
					elseif (function_exists('bbp_get_displayed_user_field') && $staffdirectory ){ // if using bbPress - link to the staff page
						$userurl=str_replace('/users', '/staff', $userurl);
					}
					$avstyle="";
					if ( $directorystyle==1 ) $avstyle = " img-circle";
					$avatarhtml = get_avatar($userid ,66);
					$avatarhtml = str_replace("photo", "photo alignleft".$avstyle, $avatarhtml);
	
					echo "<div class='col-lg-6 col-md-6 col-sm-12'><div class='indexcard'><a href='".$userurl."'><div class='media'>".$avatarhtml."<div class='media-body'><strong>".$displayname."</strong><br>";
						
					if ( get_user_meta($userid ,'user_job_title',true )) echo '<span class="small">'.esc_html(get_user_meta($userid ,'user_job_title',true ))."</span><br>";
		
					if ( get_user_meta($userid ,'user_telephone',true )) echo '<span class="small"><i class="dashicons dashicons-phone"></i> '.esc_html(get_user_meta($userid ,'user_telephone',true ))."</span><br>";
					if ( get_user_meta($userid ,'user_mobile',true ) && $showmobile ) echo '<span class="small"><i class="dashicons dashicons-smartphone"></i> '.esc_html(get_user_meta($userid ,'user_mobile',true ))."</span>";
									
					echo "</div></div></div></div></a>";
					$counter++;	
				}
				echo "</div>";
			}

			get_template_part('part', 'downloads');
			
			if ('open' == $post->comment_status) {
				 comments_template( '', true ); 
			}
		}
		?>
		</div> <!--end of first column-->

		<div class="col-lg-4 col-md-4 col-sm-4" id="sidebar">	
		
			<?php 

			get_template_part("part", "sidebar");

			get_template_part("part", "related");

			$posttags = get_the_tags($parent_guide_id);
			if ($posttags) {
				$foundtags=false;	
				$tagstr="";
			  	foreach($posttags as $tag) {
		  			$foundtags=true;
		  			$tagurl = $tag->term_id;
			    	$tagstr=$tagstr."<span><a class='label label-default' href='".get_tag_link($tagurl)."?type=project'>" . str_replace(' ', '&nbsp' , $tag->name) . '</a></span> '; 
			  	}
			  	if ($foundtags){
				  	echo "<div class='widget-box'><h3>" . __('Tags' , 'govintranet') . "</h3><p> "; 
				  	echo $tagstr;
				  	echo "</p></div>";
			  	}
			}
			?>
		</div>	
			
<?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>
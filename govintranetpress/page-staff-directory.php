<?php
/* Template name: Staff directory */
					
get_header(); ?>

<?php 
$sort = $_GET['sort'];
if (!$sort) $sort='first';
if ( have_posts() ) while ( have_posts() ) : the_post(); 
?>
					<div class="col-lg-12 white ">
						<div class="row">
							<div class='breadcrumbs'>
								<?php if(function_exists('bcn_display') && !is_front_page()) {
									bcn_display();
									}?>
							</div>
						</div>

				<h1><?php the_title(); ?></h1>
					<?php the_content(); ?>

				<ul class="pagination">
				  <li<?php if ($sort=="first") echo " class='active'"; ?>><a href="<?php the_permalink(); ?>/?sort=first">First name</a></li>
				  <li <?php if ($sort=="last") echo " class='active'"; ?>><a href="<?php the_permalink(); ?>/?sort=last">Last name</a></li>
				  <li <?php if ($sort=="team") echo " class='active'"; ?>><a href="<?php the_permalink(); ?>/?sort=team">Team</a></li>
				  <li <?php if ($sort=="grade") echo " class='active'"; ?>><a href="<?php the_permalink(); ?>/?sort=grade">Grade</a></li>
				</ul>

<?php
				if ($sort == 'last' || $sort == 'first'){
					if ($_REQUEST['show'] === null) $_REQUEST['show'] = "A";

					$letters = range('A','Z');
					
					foreach($letters as $l) {
						
						$letterlink[$l] = "<li class='disabled {$l}'><a>".$l."</a></li>";
					}				
					
					?>				
					<div class="col-lg-12">
					<ul id='atozlist' class="pagination">
					
					<?php
					if ($sort == 'last'){
						$q = "select user_id, meta_value as name from wp_usermeta where meta_key = 'last_name' order by meta_value asc";
					} elseif ($sort == "first"){
						$q = "select user_id, meta_value as name from wp_usermeta where meta_key = 'first_name' order by meta_value asc";
					}					
					$userq = $wpdb->get_results($q,ARRAY_A);

					foreach ($userq as $u){ //print_r($g);
						$title = $u['name'];
						$userid = $u['user_id'];
						$thisletter = strtoupper(substr($title,0,1));	
						$user_info = get_userdata($userid);
													
						$hasentries[$thisletter] = $hasentries[$thisletter] + 1;
						
						if (!$_REQUEST['show'] || (strtoupper($thisletter) == strtoupper($_REQUEST['show']) ) ) {

							if ($sort == 'last'){
							$displayname = get_user_meta($userid ,'last_name',true ).", ".get_user_meta($userid ,'first_name',true );
							} else {
							$displayname = get_user_meta($userid ,'first_name',true )." ".get_user_meta($userid ,'last_name',true );
								
							}
							$html .= "<div class='col-lg-4 col-md-4 col-sm-6'><div class='media'><a href='/staff/".$user_info->user_nicename."/'>".get_wp_user_avatar($u['user_id'],84,'left')."</a><div class='media-body'><a href='/staff/".$user_info->user_nicename."/'>".$displayname."</a><br>";

							?>

						<?php if ( get_user_meta($userid ,'user_job_title',true )) : 
			
							$html .= '<i class="glyphicon glyphicon-user"></i> '.get_user_meta($userid ,'user_job_title',true )."<br>";
			
						endif; ?>

						
						<?php if ( get_user_meta($userid ,'user_telephone',true )) : 
			
							$html .= '<i class="glyphicon glyphicon-earphone"></i> '.get_user_meta($userid ,'user_telephone',true )."<br>";
			
						endif; ?>
			
						<?php if ( get_user_meta($userid ,'user_mobile',true ) ) : 
			
							$html .= '<i class="glyphicon glyphicon-phone"></i> '.get_user_meta($userid,'user_mobile',true )."<br>";
			
						 endif;
			
							$html .= '<a href="mailto:'.$user_info->user_email.'">Email '. $user_info->first_name. '</a><br><br></div></div></div>';
							
							$counter++;
																														
						}
						
						$activeletter = ($_REQUEST['show'] == strtoupper($thisletter)) ? "active" : null;

						$letterlink[$thisletter] = ($hasentries[$thisletter] > 0) ? "<li  class='{$thisletter} {$activeletter}'><a href='?show=".$thisletter."&sort={$sort}'>".$thisletter."</a></li>" : "<li class='{$thisletter} {$activeletter}'><a>".$thisletter."</a></li>";

					}
											echo @implode("",$letterlink); 
					?>
					
					</ul></div>
					
					<?php 
					echo $html; 
					}

				if ($sort == 'team'){
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

				}

?>
			</div>
		




<?php endwhile; ?>

<?php get_footer(); ?>
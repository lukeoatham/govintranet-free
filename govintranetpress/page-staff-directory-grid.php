<?php
/* Template name: Staff directory grid*/
					
get_header(); ?>

<?php 
$fulldetails=get_option('general_intranet_full_detail_staff_cards'); // 1 = show
$directorystyle = get_option('general_intranet_staff_directory_style'); // 0 = squares, 1 = circles
$showgrade = get_option('general_intranet_show_grade_on_staff_cards'); // 1 = show 
$showmobile = get_option('general_intranet_show_mobile_on_staff_cards'); // 1 = show

$sort = $_GET["sort"]; 
if (!$sort) $sort = "first";

if ($_REQUEST['show'] == null) $_REQUEST['show'] = "A";

$grade = $_GET["grade"];  

if ( have_posts() ) while ( have_posts() ) : the_post(); 
?>
	<div class="row">
		<div class="col-lg-8 col-md-8 col-sm-8">
			<div class="breadcrumbs">
				<a href="<?php echo site_url(); ?>">Home</a>
				&raquo; <?php the_title(); ?>
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12">
				<h1><?php the_title(); ?></h1>
			</div>
		<div>
		<form class="form-horizontal" role="form" id="searchform2" name="searchform2" action="<?php echo site_url( '/search-staff/' ); ?>">

	  <div class="col-lg-12 col-md-12 col-sm-12 ">
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
			  			$otherteams.= " <li><a href='".site_url()."/team/{$themeURL}/'>".$taxonomy->name."</a></li>";
			  		}  
			  		
			  		$teamdrop = get_option('general_intranet_team_dropdown_name');
			  		if ($teamdrop=='') $teamdrop = "Browse teams";
			  		echo "<div class='btn-group pull-right'><button type='button' class='btn btn-info dropdown-toggle4' data-toggle='dropdown'>".$teamdrop." <span class='caret'></span></button><ul class='dropdown-menu' role='menu'>".$otherteams."</ul></div><div class='btn-group pull-right'><button class='btn btn-link disabled'>Or&nbsp;</button></div>";
				}

				?>
				</div><!-- /input-group -->
				
			  </div>
		</div>
	</form>		</div><!--end-->	
		<script>
		jQuery("#s2").focus();
		</script>							
	
		<div class="col-lg-4 col-md-4 col-sm-4">
		<!-- intentionally left blank -->
		</div>
	</div>
	<div class="col-lg-12 col-md-12 col-sm-12">
<?php 
			if ($sort == 'last' || $sort == 'first'){

				$letters = range('A','Z');
				
				foreach($letters as $l) {
					
					$letterlink[$l] = "<li class='disabled {$l}'><a>".$l."</a></li>";
				}				
				
				?>	

			<div class="col-lg-12 col-md-12 col-sm-12">
					<ul id='atozlist' class="pagination">
					
					<?php
					if ($sort == 'last'){
						$q = "select user_id, meta_value as name from $wpdb->usermeta where meta_key = 'last_name' order by meta_value asc";
					} elseif ($sort == "first"){
						$q = "select user_id, meta_value as name from $wpdb->usermeta where meta_key = 'first_name' order by meta_value asc";
					}					
					$userq = $wpdb->get_results($q,ARRAY_A);
					$html="<div class='col-lg-12 col-md-12 col-sm-12'>";
					foreach ($userq as $u){ 
						$usergrade = get_user_meta($u['user_id'],'user_grade',true); 
						$title = $u['name'];
						$userid = $u['user_id'];
						$thisletter = strtoupper(substr($title,0,1));	
						$user_info = get_userdata($userid);
						$gradecode = $usergrade['grade_code']; 
													
						$hasentries[$thisletter] = $hasentries[$thisletter] + 1;
						
						if (!$_REQUEST['show'] || (strtoupper($thisletter) == strtoupper($_REQUEST['show']) ) ) {

							if ($sort == 'last'){
							$displayname = get_user_meta($userid ,'last_name',true ).", ".get_user_meta($userid ,'first_name',true );
							} else {
							$displayname = get_user_meta($userid ,'first_name',true )." ".get_user_meta($userid ,'last_name',true );
							} 

						if ( ( ($usergrade['slug'] == $grade) && ($grade ) ) || (!$grade)  ) {
								$gradedisplay='';
								if ($gradecode && $showgrade){
									$gradedisplay = "<span class='badge pull-right'>".$gradecode."</span>";
								}
							 	if (function_exists('get_wp_user_avatar_src')){	
										$imgsrc = get_wp_user_avatar_src($userid,'thumbnail');				
										if ($directorystyle==1){
											$avatarhtml = "<img class='img img-circle alignleft' src='".$imgsrc."' width='66'  height='66' alt='".$displayname."' />";
										}else{
											$avatarhtml = "<img class='img alignleft' src='".$imgsrc."' width='66'  height='66' alt='".$displayname."' />";
										}
								} else {
									$avatarhtml = get_avatar($userid,66);
									$avatarhtml = str_replace("photo", "photo alignleft", $avatarhtml);
								}
							if ($fulldetails){
								$html .= "<div class='col-lg-4 col-md-4 col-sm-6'><div class='media well well-sm'><a href='".site_url()."/staff/".$user_info->user_nicename."/'>".$avatarhtml."</a><div class='media-body'><p><a href='".site_url()."/staff/".$user_info->user_nicename."/'><strong>".$displayname."</strong>".$gradedisplay."</a><br>";

								// display team name(s)
								$poduser = new Pod ('user' , $userid);
								$terms = $poduser->get_field('user_team');
								if ($terms) {				
									foreach ($terms as $taxonomy ) { //print_r($taxonomy);
							  		    $themeid = $taxonomy['term_id'];
							  		    $themeURL= $taxonomy['slug'];
							  		    $themeparent = $taxonomy['parent']; 
							   		    if ($themeURL == 'uncategorized') {
								  		    continue;
							  		    }
							  		    while ($themeparent!=0){
							  		    	$parentteam = get_term_by('id', $themeparent, 'team'); 
								  			$themeparent = $parentname->parent; 
							  		    }
							  		    
							  		    $parentslug = $parentteam->slug;
							  			$teamlist= $parentteam->name;
							  			$html.= "".$teamlist."<br>";
			
							  		}
								}  
								
								?>

						<?php if ( get_user_meta($userid ,'user_job_title',true )) : 
									$meta = get_user_meta($userid ,'user_job_title',true );
									$meta = str_replace(" ", "&nbsp;", $meta);
									$html.=$meta."<br>";
							endif; ?>
	
							
							<?php if ( get_user_meta($userid ,'user_telephone',true )) : 
				
								$html .= '<i class="glyphicon glyphicon-earphone"></i> <a href="tel:'.str_replace(" ", "", get_user_meta($userid ,"user_telephone",true )).'">'.get_user_meta($userid ,'user_telephone',true )."</a><br>";
				
							endif; ?>
				
							<?php if ( get_user_meta($userid ,'user_mobile',true ) && $showmobile ) : 
				
								$html .= '<i class="glyphicon glyphicon-phone"></i> <a href="tel:'.str_replace(" ", "", get_user_meta($userid ,"user_mobile",true )).'">'.get_user_meta($userid ,'user_mobile',true )."</a><br>";
				
							 endif;
				
								$html .= '<a href="mailto:'.$user_info->user_email.'">Email '. $user_info->first_name. '</a></p></div></div></div>';
								
								$counter++;
								
							} else {
							 	if (function_exists('get_wp_user_avatar')){	
										$imgsrc = get_wp_user_avatar_src($u['user_id'],'thumbnail');				
										if ($directorystyle==1){
											$avatarhtml = "<img class='img img-circle alignleft' src='".$imgsrc."' width='66'  height='66' alt='".$displayname."' />";
										}else{
											$avatarhtml = "<img class='img alignleft' src='".$imgsrc."' width='66'  height='66' alt='".$displayname."' />";
										}
								} else {
									$avatarhtml = get_avatar($u['user_id'],66,array('class'=>'alignleft'));
									$avatarhtml = str_replace("photo", "photo alignleft", $avatarhtml);
								}							
								$html .= "<div class='col-lg-4 col-md-4 col-sm-6'><div class='indexcard'><a href='".site_url()."/staff/".$user_info->user_nicename."/'><div class='media'>".$avatarhtml."<div class='media-body'><strong>".$displayname."</strong>".$gradedisplay."<br>";
								// display team name(s)
								$poduser = new Pod ('user' , $userid);
								$terms = $poduser->get_field('user_team');

								foreach ($terms as $taxonomy ) { 
						  		    $themeid = $taxonomy['term_id'];
						  		    $themeparent = $taxonomy['parent']; 

						  			
						  			if ( $themeparent == 0 ) { 
							  			$teamlist = $taxonomy['name']; 
							  			$html.= "".$teamlist."<br>"; //echo $teamlist;
						  			} else {
							  		    while ($themeparent!=0){
							  		    	$newteam = get_term_by('id', $themeparent, 'team'); 
								  			$themeparent = $newteam->parent; 
							  		    }
							  			$teamlist= $newteam->name;
							  			$html.= "".$teamlist."<br>";
						  			}
						  		}
							
								if ( get_user_meta($userid ,'user_job_title',true )) {
									$meta = get_user_meta($userid ,'user_job_title',true );
									$meta = str_replace(" ", "&nbsp;", $meta);
									$html .= '<span class="small">'.$meta."</span><br>";
								}

								if ( get_user_meta($userid ,'user_telephone',true )) $html .= '<span class="small"><i class="glyphicon glyphicon-earphone"></i> '.get_user_meta($userid ,'user_telephone',true )."</span><br>";
								if ( get_user_meta($userid ,'user_mobile',true ) && $showmobile ) $html .= '<span class="small"><i class="glyphicon glyphicon-phone"></i> '.get_user_meta($userid ,'user_mobile',true )."</span>";
												
								$html .= "</div></div></a></div></div>";
							}																							
						}
					}
						$activeletter = ($_REQUEST['show'] == strtoupper($thisletter)) ? "active" : null;

						$letterlink[$thisletter] = ($hasentries[$thisletter] > 0) ? "<li  class='{$thisletter} {$activeletter}'><a href='?grade=".$grade."&amp;show=".$thisletter."&amp;sort={$sort}'>".$thisletter."</a></li>" : "<li class='{$thisletter} {$activeletter}'><a>".$thisletter."</a></li>";

					}
											echo @implode("",$letterlink); 
															
					?>
					
					</ul>

					</div>
</div>
<div class="col-lg-12 col-md-12 col-sm-12">
<div id="sortfilter">
<div class="col-lg-4 col-md-4 col-sm-6">
<strong>Sort by:&nbsp;</strong>
  <?php if ($sort=="first") : ?>
  <button type="button" class="btn btn-primary">
    First name
  </button>
  <?php else : ?>
    <a class='btn btn-default' href="<?php the_permalink(); ?>?grade=<?php echo $grade; ?>&amp;sort=first&amp;show=<?php echo $_REQUEST['show'] ?>">First name</a>
  <?php endif; ?>

  <?php if ($sort=="last") : ?>
  <button type="button" class="btn btn-primary">
  	Last name
  </button>
  <?php else : ?>
  	<a class='btn btn-default' href="<?php the_permalink(); ?>?grade=<?php echo $grade; ?>&amp;sort=last&amp;show=<?php echo $_REQUEST['show'] ?>">Last name</a>
  <?php endif; ?>
</div>
<div class="col-lg-8 col-md-8 col-sm-6">
<strong>Filter by:&nbsp;</strong>
	<div class="btn-group">
	  <button type="button" class="btn btn-primary dropdown-toggle2" data-toggle="dropdown">
	  <?php 
		  if ($grade){
			  $sgrade=get_term_by( 'slug', $grade, 'grade' ) ;
			  $sgrade=$sgrade->name;
		  } else {
			  $sgrade='All grades';
		  }
		  echo $sgrade; ?>
		  <span class="caret"></span>
	  </button>
	  <ul class="dropdown-menu" role="menu">
		  <?php
			$terms = get_terms('grade',array('hide_empty'=>false,'orderby'=>'slug','order'=>'ASC',"parent"=>0));
			echo "<li><a href='".site_url()."/staff-directory/?sort={$sort}&amp;show=".$_REQUEST['show']."'>All grades</a></li>";
			if ($terms) {
		  		foreach ((array)$terms as $taxonomy ) {
		  		    $themeid = $taxonomy->term_id;
		  		    $themeURL= $taxonomy->slug;
			  		$desc = "<p class='howdesc'>".$taxonomy->description."</p>";
		   		    if ($themeURL == 'uncategorized') {
			  		    continue;
		  		    }
		  			echo "<li><a href='".site_url()."/staff-directory/?grade={$themeURL}&amp;sort={$sort}&amp;show=".$_REQUEST['show']."'>".$taxonomy->name."</a></li>";
				}
			}  
?>
	  	</ul>
	  	</div>
  	</div>
</div>
</div>	
<?php 
	echo '<div id="peoplenav">'.$html."</div>";
	
	}
?>

<div class="col-lg-12 col-md-12 col-sm-12">
	<div class="col-lg-12 col-md-12 col-sm-12">
		<?php the_content(); ?>
	</div>
</div>

	</div>
</div>

<?php endwhile; ?>

<?php get_footer(); ?>
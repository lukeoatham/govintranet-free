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
		<div class="col-lg-12">
		<h1><?php the_title(); ?></h1>
		</div>
		<div>
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
		</div>
	</div>	
<script>
jQuery("#s2").focus();
</script>							

	<div class="col-lg-4 col-md-4">
	<!-- intentionally left blank -->
	</div>
</div>
<div class="row">
<?php
			if ($sort == 'last' || $sort == 'first'){

				$letters = range('A','Z');
				
				foreach($letters as $l) {
					
					$letterlink[$l] = "<li class='disabled {$l}'><a>".$l."</a></li>";
				}				
				
				?>	


			<div class="col-lg-12">
			<?php the_content(); ?>
					<ul id='atozlist' class="pagination">
					
					<?php
					if ($sort == 'last'){
						$q = "select user_id, meta_value as name from wp_usermeta where meta_key = 'last_name' order by meta_value asc";
					} elseif ($sort == "first"){
						$q = "select user_id, meta_value as name from wp_usermeta where meta_key = 'first_name' order by meta_value asc";
					}					
					$userq = $wpdb->get_results($q,ARRAY_A);

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
							if ($directorystyle==1){
							$avatarhtml = str_replace('avatar-66', 'avatar-66 pull-left indexcard-avatar img img-responsive img-circle', get_avatar($u['user_id'],66));
							} else {
							$avatarhtml = str_replace('avatar-66', 'avatar-66 pull-left indexcard-avatar img img-responsive ', get_avatar($u['user_id'],66));
							}
							if ($fulldetails){
								$html .= "<div class='col-lg-4 col-md-4 col-sm-6'><div class='media well well-sm'><a href='".site_url()."/staff/".$user_info->user_nicename."/'>".$avatarhtml."</a><div class='media-body'><p><a href='".site_url()."/staff/".$user_info->user_nicename."/'><strong>".$displayname."</strong>".$gradedisplay."</a><br>";

								// display team name(s)
								$poduser = new Pod ('user' , $userid);
								$terms = $poduser->get_field('user_team');
								if ($terms) {				
									$teamlist = array();
							  		foreach ($terms as $taxonomy ) {
							  			$teamlist[]= $taxonomy['name'];
							  			$html.= implode(" &raquo; ", $teamlist)."<br>";
									}
								}  
								
								?>

							<?php if ( get_user_meta($userid ,'user_job_title',true )) : 
				
								$html .= ''.get_user_meta($userid ,'user_job_title',true )."<br>";
				
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
							if ($directorystyle==1){
							$avatarhtml = str_replace('avatar-66', 'avatar-66 pull-left indexcard-avatar img img-responsive img-circle', get_avatar($u['user_id'],66));
							} else {
							$avatarhtml = str_replace('avatar-66', 'avatar-66 pull-left indexcard-avatar img img-responsive ', get_avatar($u['user_id'],66));
								
							}
							
								$html .= "<div class='col-lg-4 col-md-4 col-sm-6'><div class='indexcard'><a href='".site_url()."/staff/".$user_info->user_nicename."/'><div class='media'>".$avatarhtml."<div class='media-body'><strong>".$displayname."</strong>".$gradedisplay."<br>";
								// display team name(s)
								$poduser = new Pod ('user' , $userid);
								$terms = $poduser->get_field('user_team');
								if ($terms) {				
									$teamlist = array();
							  		foreach ($terms as $taxonomy ) {
							  			$teamlist[]= $taxonomy['name'];
							  			$html.= implode(" &raquo; ", $teamlist)."<br>";
			
									}
								}  
							
								if ( get_user_meta($userid ,'user_job_title',true )) $html .= '<span class="small">'.get_user_meta($userid ,'user_job_title',true )."</span><br>";

							if ( get_user_meta($userid ,'user_telephone',true )) $html .= '<span class="small"><i class="glyphicon glyphicon-earphone"></i> '.get_user_meta($userid ,'user_telephone',true )."</span><br>";
							if ( get_user_meta($userid ,'user_mobile',true ) && $showmobile ) $html .= '<span class="small"><i class="glyphicon glyphicon-phone"></i> '.get_user_meta($userid ,'user_mobile',true )."</span>";
												
								$html .= "</div></div></div></div></a>";
								
							}																							
						}
						}
						$activeletter = ($_REQUEST['show'] == strtoupper($thisletter)) ? "active" : null;

						$letterlink[$thisletter] = ($hasentries[$thisletter] > 0) ? "<li  class='{$thisletter} {$activeletter}'><a href='?grade=".$grade."&show=".$thisletter."&sort={$sort}'>".$thisletter."</a></li>" : "<li class='{$thisletter} {$activeletter}'><a>".$thisletter."</a></li>";

					}
											echo @implode("",$letterlink); 
					?>
					
					</ul>

					</div>
</div>
<div class="col-lg-4 col-md-4">
<strong>Sort by:&nbsp;</strong>
  <?php if ($sort=="first") : ?>
  <button type="button" class="btn btn-primary">
    First name
  </button>
  <?php else : ?>
    <a class='btn btn-default' href="<?php the_permalink(); ?>?grade=<?php echo $grade; ?>&sort=first&show=<?php echo $_REQUEST['show'] ?>">First name</a>
  <?php endif; ?>

  <?php if ($sort=="last") : ?>
  <button type="button" class="btn btn-primary">
  	Last name
  </button>
  <?php else : ?>
  	<a class='btn btn-default' href="<?php the_permalink(); ?>?grade=<?php echo $grade; ?>&sort=last&show=<?php echo $_REQUEST['show'] ?>">Last name</a>
  <?php endif; ?>
</div>
<div class="col-lg-8 col-md-8">
<strong>Browse by:&nbsp;</strong>
	<div class="btn-group">
	<?php if ($_REQUEST['grade']) : ?>
	  <button type="button" class="btn btn-primary dropdown-toggle2" data-toggle="dropdown">
	<?php else : ?>
	  <button type="button" class="btn btn-default dropdown-toggle2" data-toggle="dropdown">
	<?php endif; ?>  
	  <?php 
	  if ($grade){
		  $sgrade=get_term_by( 'slug', $grade, 'grade' ) ;
		  $sgrade=$sgrade->name;
	  } else {
		  $sgrade='Grade';
	  }
	  echo $sgrade; ?>
	  <span class="caret"></span>
	  </button>
	  <ul class="dropdown-menu" role="menu">
		  <?php
			$terms = get_terms('grade',array('hide_empty'=>false,'orderby'=>'slug','order'=>'ASC'));
			echo "<li><a href='".site_url()."/staff-directory/?sort={$sort}&show=".$_REQUEST['show']."'>All grades</a></li>";
			if ($terms) {
		  		foreach ((array)$terms as $taxonomy ) {
		  		    $themeid = $taxonomy->term_id;
		  		    $themeURL= $taxonomy->slug;
			  		$desc = "<p class='howdesc'>".$taxonomy->description."</p>";
		   		    if ($themeURL == 'uncategorized') {
			  		    continue;
		  		    }
		  			echo "<li><a href='".site_url()."/staff-directory/?grade={$themeURL}&sort={$sort}&show=".$_REQUEST['show']."'>".$taxonomy->name."</a></li>";
				}

			}  

  ?>
	  	</ul>
	  	</div>
  				<?php
		$terms = get_terms('team',array('hide_empty'=>false,'parent' => '0',));
		if ($terms) {
			$otherteams="";
			foreach ((array)$terms as $taxonomy ) {
			    $themeid = $taxonomy->term_id;
			    $themeURL= $taxonomy->slug;
				$otherteams.= " <li><a href='".site_url()."/team/{$themeURL}/'>".$taxonomy->name."</a></li>";
			}
			echo "<div class='btn-group'><button type='button' class='btn btn-default dropdown-toggle3' data-toggle='dropdown'>Team <span class='caret'></span></button><ul class='dropdown-menu' role='menu'>".$otherteams."</ul></div>";
		}  

?>
  	</div>
<?php 
	echo '<div id="peoplenav">'.$html."</div>";
	
	}
?>
	</div>
</div>

<?php endwhile; ?>

<?php get_footer(); ?>
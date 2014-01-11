<?php
/* Template name: Staff directory grid*/
					
get_header(); ?>

<?php 
$fulldetails=get_option('general_intranet_full_detail_staff_cards');
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
			> <?php the_title(); ?>
		</div>
		<div class="col-lg-12">
		<h1><?php the_title(); ?></h1>
		</div>
		<form class="form-horizontal" role="form" id="searchform2" name="searchform2" action="<?php echo home_url( '/' ); ?>">
	
		  <div class="col-lg-12">
			<div id="staff-search" class="well">
					<div class="input-group">
				    	 <input type="text" class="form-control typeahead" placeholder="Search" name="s" id="s2" value="<?php echo $_GET['s'];?>">
				    	 <input type="hidden" name="pt" value="user">
						 <span class="input-group-btn">
						 <button class="btn btn-primary" type="submit"><i class="glyphicon glyphicon-search"></i></button>
						 </span>
					</div><!-- /input-group -->
				  </div>
			</div>
		</form>
	</div>	
	
	<div class="col-lg-4 col-md-4 col-sm-4">
		<div class="widget-box">
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
				echo "<div class='btn-group'><button type='button' class='btn btn-default dropdown-toggle3' data-toggle='dropdown'>Browse by team <span class='caret'></span></button><ul class='dropdown-menu' role='menu'>".$otherteams."</ul></div>";
			}  
			?>
		</div>
	</div>

<div class="col-lg-12">

<?php the_content(); ?>

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
		  $sgrade='All grades';
	  }
	  echo $sgrade; ?>
	  <span class="caret"></span>
	  </button>
	  <ul class="dropdown-menu" role="menu">
		  <?php
  					$terms = get_terms('grade',array('hide_empty'=>false));
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
							echo "<li><a href='".site_url()."/staff-directory/?sort={$sort}&show=".$_REQUEST['show']."'>All grades</a></li>";

					}  

  ?>
  </ul>
</div>
<?php


				if ($sort == 'last' || $sort == 'first'){

					$letters = range('A','Z');
					
					foreach($letters as $l) {
						
						$letterlink[$l] = "<li class='disabled {$l}'><a>".$l."</a></li>";
					}				
					
					?>	
					</div>

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
						$usergrade = get_user_meta($u['user_id'],'user_grade',true); //echo $usergrade['slug'];
						
						$title = $u['name'];
						$userid = $u['user_id'];
						$thisletter = strtoupper(substr($title,0,1));	
						$user_info = get_userdata($userid);
//						$thisgrade = get_user_meta($userid ,'user_grade',true ); 
						$gradecode = $usergrade['grade_code']; 
													
						$hasentries[$thisletter] = $hasentries[$thisletter] + 1;
						
						if (!$_REQUEST['show'] || (strtoupper($thisletter) == strtoupper($_REQUEST['show']) ) ) {

							if ($sort == 'last'){
							$displayname = get_user_meta($userid ,'last_name',true ).", ".get_user_meta($userid ,'first_name',true );
							} else {
							$displayname = get_user_meta($userid ,'first_name',true )." ".get_user_meta($userid ,'last_name',true );
							} 

						if ( ( ($usergrade['slug'] == $grade) && ($grade ) ) || (!$grade)  ) {

							if ($fulldetails){

								$html .= "<div class='col-lg-4 col-md-4 col-sm-6'><div class='media well well-sm'><a href='".site_url()."/staff/".$user_info->user_nicename."/'>".get_wp_user_avatar($u['user_id'],66,'left')."</a><div class='media-body'><p><a href='".site_url()."/staff/".$user_info->user_nicename."/'><strong>".$displayname."</strong><span class='badge pull-right'>".$gradecode."</span></a><br>";
								?>
	
							<?php if ( get_user_meta($userid ,'user_job_title',true )) : 
				
								$html .= ''.get_user_meta($userid ,'user_job_title',true )."<br>";
				
							endif; ?>
	
							
							<?php if ( get_user_meta($userid ,'user_telephone',true )) : 
				
								$html .= '<i class="glyphicon glyphicon-earphone"></i> <a href="tel:'.str_replace(" ", "", get_user_meta($userid ,"user_telephone",true )).'">'.get_user_meta($userid ,'user_telephone',true )."</a><br>";
				
							endif; ?>
				
							<?php if ( get_user_meta($userid ,'user_mobile',true ) ) : 
				
								$html .= '<i class="glyphicon glyphicon-phone"></i> <a href="tel:'.str_replace(" ", "", get_user_meta($userid ,"user_mobile",true )).'">'.get_user_meta($userid ,'user_mobile',true )."</a><br>";
				
							 endif;
				
								$html .= '<a href="mailto:'.$user_info->user_email.'">Email '. $user_info->first_name. '</a></p></div></div></div>';
								
								$counter++;
								
							} else {
							
								$html .= "<div class='col-lg-4 col-md-4 col-sm-6'><div class='indexcard'><a href='".site_url()."/staff/".$user_info->user_nicename."/'>".get_wp_user_avatar($u['user_id'],66,'left')."<strong>".$displayname."</strong><span class='badge pull-right'>".$gradecode."</span><br>";
								if ( get_user_meta($userid ,'user_job_title',true )) $html .= '<span class="small">'.get_user_meta($userid ,'user_job_title',true )."</span><br>";

							if ( get_user_meta($userid ,'user_telephone',true )) $html .= '<span class="small"><i class="glyphicon glyphicon-earphone"></i> '.get_user_meta($userid ,'user_telephone',true )."</span><br>";
							if ( get_user_meta($userid ,'user_mobile',true ) ) $html .= '<span class="small"><i class="glyphicon glyphicon-phone"></i> '.get_user_meta($userid ,'user_mobile',true )."</span>";
												
								$html .= "</div></div></a></i>";
								
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

					<?php 
					if ($fulldetails){
					echo '<div id="peoplenav" class="">'.$html."</div>";
 
					} else {
					echo '<div id="peoplenav" class="">'.$html."</div>";
					}
						
					}


?>

</div>
		


</div>

<?php endwhile; ?>

<?php get_footer(); ?>
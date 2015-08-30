<?php
/* Template name: Staff directory flexible */
					
get_header(); ?>

<?php 

	 wp_register_script( 'masonry.pkgd.min', get_stylesheet_directory_uri() . "/js/masonry.pkgd.min.js");
	 wp_enqueue_script( 'masonry.pkgd.min',95 );

	 wp_register_script( 'imagesloaded.pkgd.min', get_stylesheet_directory_uri() . "/js/imagesloaded.pkgd.min.js");
	 wp_enqueue_script( 'imagesloaded.pkgd.min',94 );


$fulldetails=get_option('options_full_detail_staff_cards'); // 1 = show
$directorystyle = get_option('options_staff_directory_style'); // 0 = squares, 1 = circles
$showgrade = get_option('options_show_grade_on_staff_cards'); // 1 = show 
$showmobile = get_option('options_show_mobile_on_staff_cards'); // 1 = show
$sort = '';
if ( isset( $_GET["sort"] ) ) $sort = $_GET["sort"]; 
if (!$sort) $sort = "first";
$requestshow = "A";
if ( isset( $_REQUEST['show'] ) ) $requestshow = $_REQUEST['show'];

$grade = '';
if ( isset( $_GET["grade"] ) ) $grade = $_GET["grade"];  

if ( have_posts() ) while ( have_posts() ) : the_post(); 
?>
	<div class="row">
		<div class="col-lg-8 col-md-8 col-sm-12">
			<div class='breadcrumbs'>
				<?php if(function_exists('bcn_display') && !is_front_page()) {
					bcn_display();
					}?>
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12">
				<h1><?php the_title(); ?></h1>
			</div>
		<div>
			<form class="form-horizontal" role="form" id="searchform2" name="searchform2" action="<?php if ( function_exists('relevanssi_do_query') ) { echo "/"; } else { echo site_url( '/search-staff/' ); } ?>">
			  <div class="col-lg-12 col-md-12 col-sm-12">
				<div id="staff-search" class="well well-sm">
						<div class="input-group">
					    	 <input type="text" class="form-control pull-left" placeholder="Name, job title, skills, team, number..." name="<?php if ( function_exists('relevanssi_do_query') ) { echo "s"; } else { echo "q"; } ?>" id="s2">
					    	 <input type="hidden" name="include" value="user">
					    	 <input type="hidden" name="post_type[]" value="team">
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
	</div><!--end-->	
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
		
		<div class="col-lg-4 col-md-4 col-sm-4">


		<!-- intentionally left blank -->
		</div>
	</div>
	<div class="col-lg-12 col-md-12 col-sm-12">
<?php 
			global $wpdb;
			if ($sort == 'last' || $sort == 'first'){
				if ($sort == 'first') :
					$q = "select distinct left(meta_value,1) as letter from $wpdb->usermeta where meta_key = 'first_name' group by meta_value;";
					$liveletters = $wpdb->get_results($q,ARRAY_A);
				endif;
				if ($sort == 'last') :
					$q = "select distinct left(meta_value,1) as letter from $wpdb->usermeta where meta_key = 'last_name' group by meta_value;";
					$liveletters = $wpdb->get_results($q,ARRAY_A);
				endif;
				
				$live = array();
				foreach ($liveletters as $ll){
					$live[] = $ll['letter'];
				}
				$letters = range('A','Z');
				$activeletter = $requestshow;
				
				foreach($letters as $l) {
					
					
					if ($l == $activeletter) {
						$letterlink[$l] = "<li  class='{$l} active'><a href='?grade=".$grade."&amp;show=".$l."&amp;sort={$sort}'>".$l."</a></li>";
					} else {
						if (in_array($l, $live)){
							$letterlink[$l] = "<li  class='{$l}'><a href='?grade=".$grade."&amp;show=".$l."&amp;sort={$sort}'>".$l."</a></li>";
						} else {
							$letterlink[$l] = "<li  class='{$l} disabled'><a href='?grade=".$grade."&amp;show=".$l."&amp;sort={$sort}'>".$l."</a></li>";
						}
					}						
				}				
				
				?>	


			<div class="col-lg-12 col-md-12 col-sm-12">
					<ul id='atozlist' class="pagination">
					
					<?php
					if ($sort == 'last'){
						$q = "select user_id, meta_value as name from $wpdb->usermeta where meta_key = 'last_name' and ucase(left(meta_value,1)) = '".strtoupper($requestshow)."' order by meta_value asc";
					} elseif ($sort == "first"){
						$q = "select user_id, meta_value as name from $wpdb->usermeta where meta_key = 'first_name' and ucase(left(meta_value,1)) = '".strtoupper($requestshow)."' order by meta_value asc";
					}					
					$userq = $wpdb->get_results($q,ARRAY_A);
					$html="<div class='row'>";
					foreach ((array)$userq as $u){ 
						$usergrade = get_user_meta($u['user_id'],'user_grade',true); 
						$gradecode = '';
						if ( $usergrade ) $gradecode = get_option('grade_'.$usergrade.'_grade_code', '');
						$title = $u['name'];
						$userid = $u['user_id'];
						$thisletter = strtoupper(substr($title,0,1));	
						$user_info = get_userdata($userid);
													
						if ( isset( $hasentries[$thisletter] ) ):
							$hasentries[$thisletter] = $hasentries[$thisletter] + 1;
						else: 
							$hasentries[$thisletter] = 1;
						endif;
						if (!$requestshow || (strtoupper($thisletter) == strtoupper($requestshow) ) ) {

							if ($sort == 'last'){
							$displayname = get_user_meta($userid ,'last_name',true ).", ".get_user_meta($userid ,'first_name',true );
							} else {
							$displayname = get_user_meta($userid ,'first_name',true )." ".get_user_meta($userid ,'last_name',true );
							} 

						if ( ( ( isset( $usergrade['slug'] ) && $usergrade['slug'] == $grade) && ($grade ) ) || (!$grade)  ) {
								$gradedisplay='';
								if ($gradecode && $showgrade){
									$gradedisplay = "<span class='badge pull-right'>".$gradecode."</span>";
								}
								$avstyle="";
								if ( $directorystyle==1 ) $avstyle = " img-circle ";
								$avatarhtml = get_avatar($userid,66);
								$avatarhtml = str_replace(" photo", " photo alignleft ".$avstyle, $avatarhtml);
							if ($fulldetails){
								$html .= "<div class='col-lg-4 col-md-6 col-sm-6 col-xs-12 pgrid-item'><div class='media well well-sm'><a href='".site_url()."/staff/".$user_info->user_nicename."/'>".$avatarhtml."</a><div class='media-body'><p><a href='".site_url()."/staff/".$user_info->user_nicename."/'><strong>".$displayname."</strong>".$gradedisplay."</a><br>";

								// display team name(s)
								$poduser = get_userdata($userid);
								$team = get_user_meta($userid ,'user_team',true );
								if ($team) {				
									foreach ((array)$team as $t ) { 
							  		    $theme = get_post($t);
										$html.= "<a href='".get_permalink($theme->ID)."'>".govintranetpress_custom_title($theme->post_title)."</a><br>";
			
							  		}
								}  
								
								?>

						<?php if ( get_user_meta($userid ,'user_job_title',true )) : 
									$meta = get_user_meta($userid ,'user_job_title',true );
									$html .= '<span class="small">'.$meta."</span><br>";
							endif; 
	
							
							if ( get_user_meta($userid ,'user_telephone',true )) $html.= '<span class="small"><i class="dashicons dashicons-phone"></i> '.get_user_meta($userid ,'user_telephone',true )."</span><br>";
							if ( get_user_meta($userid ,'user_mobile',true ) && $showmobile ) $html.= '<span class="small"><i class="dashicons dashicons-smartphone"></i> '.get_user_meta($userid ,'user_mobile',true )."</span><br>";
				
								$html .= '<span class="small"><a href="mailto:'.$user_info->user_email.'">Email '. $user_info->first_name. '</a></small></p></div></div></div>';
								
								$counter++;
								
							} else {
								$avstyle="";
								if ( $directorystyle==1 ) $avstyle = " img-circle ";
								$avatarhtml = get_avatar($userid,66);
								$avatarhtml = str_replace(" photo", " photo alignleft ".$avstyle, $avatarhtml);
								$html .= "<div class='col-lg-4 col-md-6 col-sm-6 col-xs-12 pgrid-item'><div class='indexcard'><a href='".site_url()."/staff/".$user_info->user_nicename."/'><div class='media'>".$avatarhtml."<div class='media-body'><strong>".$displayname."</strong>".$gradedisplay."<br>";
								// display team name(s)
								$team = get_user_meta($userid,'user_team',true);
								if ($team){
									foreach ((array)$team as $t ) { 
							  		    $theme = get_post($t);
										$html.= govintranetpress_custom_title($theme->post_title)."<br>";
							  			
						  			}
	
						  		}
							
								if ( get_user_meta($userid ,'user_job_title',true )) {
									$meta = get_user_meta($userid ,'user_job_title',true );
									$html .= '<span class="small">'.$meta."</span><br>";
								}

							if ( get_user_meta($userid ,'user_telephone',true )) $html.= '<span class="small"><i class="dashicons dashicons-phone"></i> '.get_user_meta($userid ,'user_telephone',true )."</span><br>";
							if ( get_user_meta($userid ,'user_mobile',true ) && $showmobile ) $html.= '<span class="small"><i class="dashicons dashicons-smartphone"></i> '.get_user_meta($userid ,'user_mobile',true )."</span>";
												
								$html .= "</div></div></a></div></div>";

								
							}																							
						}
						}


					}
											echo @implode("",$letterlink); 
															
					?>
					
					</ul>

					</div>
</div>
<div class="col-lg-12 col-md-12 col-sm-12">
<div id="sortfilter">
<div class="col-lg-4 col-md-5 col-sm-6">
<strong>Sort by:&nbsp;</strong>
  <?php if ($sort=="first") : ?>
  <button type="button" class="btn btn-primary">
    First name
  </button>
  <?php else : ?>
    <a class='btn btn-default' href="<?php the_permalink(); ?>?sort=first&amp;show=<?php echo $requestshow ?>">First name</a>
  <?php endif; ?>

  <?php if ($sort=="last") : ?>
  <button type="button" class="btn btn-primary">
  	Last name
  </button>
  <?php else : ?>
  	<a class='btn btn-default' href="<?php the_permalink(); ?>?sort=last&amp;show=<?php echo $requestshow ?>">Last name</a>
  <?php endif; ?>
</div>

</div>
</div>	
	<div class="col-lg-12 col-md-12 col-sm-12">
  	<?php 
	
	$output=
		'<div id="gridcontainer"><div class="grid-sizer"></div>'.$html."</div>";
	echo $output;
	echo "</div>";
	}
?>

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
<div class="col-lg-12 col-md-12 col-sm-12">
	<div class="col-lg-12 col-md-12 col-sm-12">
		<?php the_content(); ?>
	</div>
</div>

	</div>
</div>

<?php endwhile; ?>

<?php get_footer(); ?>
<?php
/* Template name: Staff search */
 		
 //*****************************************************				

$directorystyle = get_option('options_staff_directory_style'); // 0 = squares, 1 = circles
$showgrade = get_option('options_show_grade_on_staff_cards'); // 1 = show 
$showmobile = get_option('options_show_mobile_on_staff_cards'); // 1 = show
$fulldetails = get_option('options_full_detail_staff_cards'); // 1 = show

get_header(); ?>

<div class="col-lg-8 col-md-9 white">
	<div class='breadcrumbs'>
		<?php if(function_exists('bcn_display') && !is_front_page()) {
			bcn_display();
			}?>
	</div>

	<?php 
	global $wpdb;
	$sw = sanitize_text_field($_GET['q']);
	if ($sw) {
		$searchmasterstaff = array();
		$searchmasterteam = array();
		$searchwords = explode(" ", $sw)	;
		foreach ( $searchwords as $s ){
			if ( strlen($s) < 3 ) continue;
			// search user meta
			$sr = $wpdb->get_results($wpdb->prepare("
			SELECT distinct user_id from $wpdb->usermeta WHERE 
			(meta_value like '%%%s%%' and meta_key='user_job_title') OR
			(meta_value like '%%%s%%' and meta_key='first_name') OR
			(meta_value like '%%%s%%' and meta_key='last_name') OR
			(meta_value like '%%%s%%' and meta_key='nickname') OR
			(meta_value like '%%%s%%' and meta_key='description') OR
			(meta_value like '%%%s%%' and meta_key='user_job_title') OR
			(meta_value like '%%%s%%' and meta_key='user_telephone') OR
			(meta_value like '%%%s%%' and meta_key='user_mobile') OR
			(meta_value like '%%%s%%' and meta_key='user_key_skills') 	
			",$s,$s,$s,$s,$s,$s,$s,$s,$s),ARRAY_A); 
		
			//search team taxonomy
			$sr2 = $wpdb->get_results($wpdb->prepare("
			SELECT distinct ID, post_name, post_title from $wpdb->posts WHERE 
			((post_title like '%%%s%%') OR (post_content like '%%%s%%')) and post_type = 'team';",$s,$s),ARRAY_A); 	
			
			$searchmasterstaff = array_merge($searchmasterstaff, $sr);	
			$searchmasterteam = array_merge($searchmasterteam, $sr2);	
		}
	}
	?>
	<h1><?php printf( __( 'Search results for: %s', 'govintranet' ),  $sw ); ?></h1>
	<form class="form-horizontal" id="searchform2" name="searchform2" action="<?php echo get_permalink(get_the_id()); ?>">
	  <div class="col-lg-12">
		<div id="staff-search" class="well well-sm">
			<div class="input-group">
		    	 <input type="text" class="form-control" placeholder="<?php _e('Search for a name, job title, skills, phone number...' , 'govintranet'); ?>" name="q" id="s2" value="<?php echo $sw;?>">
				 <label for="searchbutton" class="sr-only"><?php _e('Search' , 'govingtranet'); ?></label>	 
				 <span class="input-group-btn">
					 <button class="btn btn-primary" type="submit"><i class="glyphicon glyphicon-search"></i></button>
				 </span>
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

	<?php
	if (count($searchmasterstaff) > 0 || count($searchmasterteam) > 0){
		$totfound = count($searchmasterstaff)+count($searchmasterteam);
		echo "<p class='news_date'>";
		printf( _n('Found 1 result' , 'Found %d results', $totfound, 'govintranet') , $totfound );
		echo "</p>";
	}
	?>

	<div id="peoplenav" class="search-staff row">	
	<?php
	foreach ((array)$searchmasterstaff as $u){

		$g = get_user_meta($u['user_id'],'user_grade',false);
		$l = get_user_meta($u['user_id'],'last_name',false);
			
		$userid =  $u['user_id'];

		$context = get_user_meta($userid,'user_job_title',true);
		if ($context=='') $context="staff";
		$icon = "user";			
		$user_info = get_userdata($userid);
		$userurl = site_url().'/staff/'.$user_info->user_nicename;
		$displayname = get_user_meta($userid ,'first_name',true )." ".get_user_meta($userid ,'last_name',true );
		if ( $displayname == " " ) $displayname = $user_info->display_name;

		$avstyle="";
		if ( $directorystyle==1 ) $avstyle = " img-circle";
		$avatarhtml = get_avatar($userid,66);
		$avatarhtml = str_replace(" photo", " photo alignleft".$avstyle, $avatarhtml);

		$gradedisplay='';
		if ($showgrade){
			$gradecode = get_user_meta($userid,'user_grade',true);
			$gradecode = $gradecode['grade_code'];
			$gradedisplay = "<span class='badge pull-right'>".$gradecode."</span>";
		}

		if ($fulldetails){
				
				echo "<div class='col-lg-6 col-md-6 col-sm-6'><div class='media well well-sm'><a href='".site_url()."/staff/".$user_info->user_nicename."/'>".$avatarhtml."</a><div class='media-body'><p><a href='".site_url()."/staff/".$user_info->user_nicename."/'><strong>".$displayname."</strong>".$gradedisplay."</a><br>";

				$terms = get_user_meta($userid ,'user_team',true ); 
				if ($terms) {				
					foreach ((array)$terms as $t ) { 
			  		    $theme = get_post($t);
						echo govintranetpress_custom_title($theme->post_title)."<br>";
			  		}
				}  

				if ( get_user_meta($userid ,'user_job_title',true )) : 
	
					echo esc_attr(get_user_meta($userid ,'user_job_title',true ))."<br>";
	
				endif;
				
				if ( get_user_meta($userid ,'user_telephone',true )) : 
	
					echo '<i class="glyphicon glyphicon-earphone"></i> <a href="tel:'.str_replace(" ", "", get_user_meta($userid ,"user_telephone",true )).'">'.esc_attr(get_user_meta($userid ,'user_telephone',true ))."</a><br>";
	
				endif; 
	
				if ( get_user_meta($userid ,'user_mobile',true ) && $showmobile ) : 
	
					echo '<i class="glyphicon glyphicon-phone"></i> <a href="tel:'.str_replace(" ", "", get_user_meta($userid ,"user_mobile",true )).'">'.get_user_meta($userid ,'user_mobile',true )."</a><br>";
	
				 endif;
	
				echo  '<a href="mailto:'.$user_info->user_email.'">' . __("Email" , "govintranet") . ' ' . $user_info->first_name. '</a></p></div></div></div>';
				
				$counter++;	

			
		} //end full details
		else {  
			echo "<div class='col-lg-6 col-md-6 col-sm-6'><div class='indexcard'><a href='".site_url()."/staff/".$user_info->user_nicename."/'><div class='media'>".$avatarhtml."<div class='media-body'><strong>".$displayname."</strong>".$gradedisplay."<br>";
				$terms = get_user_meta($userid ,'user_team',true ); 
				if ($terms) {				
					foreach ((array)$terms as $t ) { 
			  		    $theme = get_post($t);
						echo govintranetpress_custom_title($theme->post_title)."<br>";
			  		}
				}  
			
			if ( get_user_meta($userid ,'user_job_title',true )) echo '<span class="small">'.get_user_meta($userid ,'user_job_title',true )."</span><br>";

			if ( get_user_meta($userid ,'user_telephone',true )) echo '<span class="small"><i class="glyphicon glyphicon-earphone"></i> '.get_user_meta($userid ,'user_telephone',true )."</span><br>";
			if ( get_user_meta($userid ,'user_mobile',true ) && $showmobile ) echo '<span class="small"><i class="glyphicon glyphicon-phone"></i> '.get_user_meta($userid ,'user_mobile',true )."</span>";
							
			echo "</div></div></div></div></a>";
			$counter++;	
		}	
	
	}


	foreach ((array)$searchmasterteam as $post){
		?>
		<div>
			<div class="col-lg-12"><br>
				<h3 class='postlist'>				
				<a href="<?php echo get_permalink($post['ID']); ?>" rel="bookmark"><?php echo govintranetpress_custom_title($post['post_title']);  ?> (<?php _e('Team' , 'govintranet') ;?>)</a></h3>
				<div class='media'>
					<div class='media-body'>
					<?php echo $post['post_content']; ?>
					</div>
				</div>
			</div>
		</div>
		<br class="clearfix">
		<?php
	}
	
	if ($totfound == 0):
		$landingpage = get_option('options_module_staff_directory_page'); 
		if ( !$landingpage ):
			$landingpage_link_text = 'staff directory';
			$landingpage = site_url().'/staff-directory/';
		else:
			$landingpage_link_text = get_the_title( $landingpage[0] );
			$landingpage = get_permalink( $landingpage[0] );
		endif;

		echo "<h2>" . __('Not on the directory' , 'govintranet' ) . "</h2>";
		echo "<p>";
		printf( _x('Try searching again or go back to the <a href="%1$s">%2$s</a>' , 'param 1 is the URL, param 2 is the title of the staff directory page', 'govintranet') , $landingpage, $landingpage_link_text );
		echo "</p><br>";
	endif;
	
	?>
	</div>
	</div>
	</div>
	<div class="col-lg-4 col-md-3" id='sidebar'>
	<?php 	dynamic_sidebar('serp-widget-area'); ?>	
	</div>

<?php get_footer(); ?>
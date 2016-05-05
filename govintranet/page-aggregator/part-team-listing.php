<?php
	global $post;
	global $title;
	global $alreadyshown;
	global $directorystyle;
	global $showmobile;
	global $teamid;
	global $teamleaderid;
	echo "<div class='widget-box'>";
	if ( $title ) echo "<h3>".esc_attr($title)."</h3>";
	$counter=0;
	$tcounter=0;
	if ( $teamleaderid ):
		foreach ((array)$teamleaderid as $userid){
			$alreadyshown[$userid] = true;	
			$context = get_user_meta($userid,'user_job_title',true);
			if ($context=='') $context="staff";
			$icon = "user";			
			$user_info = get_userdata($userid);
			$userurl = site_url().'/staff/'.$user_info->user_nicename;
			$displayname = get_user_meta($userid ,'first_name',true )." ".get_user_meta($userid ,'last_name',true );		
			$avatarhtml = get_avatar($userid,66); 
			if ($directorystyle==1){
				$avatarhtml = str_replace(" photo", " photo alignleft img-circle", $avatarhtml);
			} else {
				$avatarhtml = str_replace(" photo", " photo alignleft", $avatarhtml);
			}
			echo "<a href='".site_url()."/staff/".$user_info->user_nicename."/'><div class='media'>".$avatarhtml."<div class='media-body'><strong>".$displayname."</strong><br>";
			if ( get_user_meta($userid ,'user_job_title',true )) echo '<span class="small">'.get_user_meta($userid ,'user_job_title',true )."</span><br>";
			if ( get_user_meta($userid ,'user_telephone',true )) echo '<span class="small"><i class="dashicons dashicons-phone"></i> '.get_user_meta($userid ,'user_telephone',true )."</span><br>";
			if ( get_user_meta($userid ,'user_mobile',true ) && $showmobile ) echo '<span class="small"><i class="dashicons dashicons-smartphone"></i> '.get_user_meta($userid ,'user_mobile',true )."</span>";
			echo "</div></div></a><hr>";
			$counter++;	
		}
	else:
		$teamleaderid = array();
	endif;
	//***********************************************************************************************
	$iteams = array();
	$iteams[] = $teamid;
	$multipleteams = false;

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
		foreach ($user_query->results as $u){ 
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
		$avatarhtml = get_avatar($userid,66);
		if ($directorystyle==1):
			$avatarhtml = str_replace(" photo", " photo alignleft img-circle", $avatarhtml);
		else:
			$avatarhtml = str_replace(" photo", " photo alignleft", $avatarhtml);
		endif;
		echo "<a href='".site_url()."/staff/".$user_info->user_nicename."/'><div class='media'>".$avatarhtml."<div class='media-body'><strong>".$displayname."</strong><br>";
		if ( get_user_meta($userid ,'user_job_title',true )) echo '<span class="small">'.get_user_meta($userid ,'user_job_title',true )."</span><br>";
		if ( get_user_meta($userid ,'user_telephone',true )) echo '<span class="small"><i class="dashicons dashicons-phone"></i> '.get_user_meta($userid ,'user_telephone',true )."</span><br>";
		if ( get_user_meta($userid ,'user_mobile',true ) && $showmobile ) echo '<span class="small"><i class="dashicons dashicons-smartphone"></i> '.get_user_meta($userid ,'user_mobile',true )."</span>";
		echo "</div></div></a><hr>";
		$counter++;	
	}	
	echo "</div>";
	wp_reset_postdata();
?>			
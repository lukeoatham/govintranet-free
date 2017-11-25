<?php
		
	$my_theme = wp_get_theme();
	$theme_version = $my_theme->get('Version');
	$database_version = get_option("govintranet_db_version");
	$update_okay = 1;
	$reason = '';
	$updated_to = '';
	$tzone = get_option('timezone_string'); 
	if ( $tzone ) date_default_timezone_set($tzone);
	
	/************************************************
	
	Process any version-specific database updates
	
	************************************************/
	
	// LESS THAN 4.19 - clean up old metadata
	
	if ( version_compare( $database_version, "4.19", '<' ) ):
		
		global $wpdb;
		
		// Tidy old Pods meta
		$wpdb->query("delete from $wpdb->postmeta where meta_key='_pods_children_chapters';");
		$wpdb->query("delete from $wpdb->postmeta where meta_key='_pods_children_chapters';");
		$wpdb->query("delete from $wpdb->postmeta where meta_key='_pods_document_attachments';");
		$wpdb->query("delete from $wpdb->postmeta where meta_key='_pods_expiry_action';");
		$wpdb->query("delete from $wpdb->postmeta where meta_key='_pods_page_related_tasks';");
		$wpdb->query("delete from $wpdb->postmeta where meta_key='_pods_page_type';");
		$wpdb->query("delete from $wpdb->postmeta where meta_key='_pods_parent_guide';");
		$wpdb->query("delete from $wpdb->postmeta where meta_key='_pods_related_tasks';");
		$wpdb->query("delete from $wpdb->postmeta where meta_key='_pods_video_still';");
		$wpdb->query("delete from $wpdb->postmeta where meta_key='_pods_project';");
		$wpdb->query("delete from $wpdb->postmeta where meta_key='_pods_project_vacancies';");
		$wpdb->query("delete from $wpdb->postmeta where meta_key='_pods_related_pages';");
		$wpdb->query("delete from $wpdb->postmeta where meta_key='_pods_related_stories';");
		$wpdb->query("delete from $wpdb->postmeta where meta_key='_pods_related_projects';");

		//Tidy old Pods user meta
		$wpdb->query("delete from $wpdb->usermeta where meta_key='_pods_user_line_manager';");
		$wpdb->query("delete from $wpdb->usermeta where meta_key='_pods_user_grade';");
		$wpdb->query("delete from $wpdb->usermeta where meta_key='_pods_user_team';");
		
		//Update user team meta
		$team_meta = $wpdb->get_results("select user_id, meta_value from $wpdb->usermeta where meta_key='user_team';");
		if ( count($team_meta) > 0 ) foreach ( $team_meta as $tm ){
			if ( $tm->meta_value == "" ) continue;
			$current_team = array();
			if ( is_numeric( $tm->meta_value ) ):
				$current_team[] = $tm->meta_value;
			else:
				$current_team = get_user_meta($tm->user_id,"user_team",true);
			endif;
			$new_team = array();
			foreach ( $current_team as $ct ){
				$new_team[] = (string)$ct;
			}
			if ( count($new_team) > 0 ){
				$update_status = update_user_meta($tm->user_id, "user_team", $new_team, $tm->meta_value );
			}
		}
		$updated_to = "4.19";
		update_option("govintranet_db_version", $updated_to );
		
	endif;

	// LESS THAN 4.19.1 - update search placeholder to use pipes instead of comma
	
	if ( version_compare( $database_version, "4.19.1", '<' ) ):
		
		$placeholder = get_option('options_search_placeholder'); //get search placeholder text and variations
		if ( $placeholder && strpos($placeholder, ",") ){
			$placeholder = explode(",", $placeholder);
			$placeholder = implode("||", $placeholder);
			update_option('options_search_placeholder', $placeholder);
		}
		$updated_to = "4.19.1";
		update_option("govintranet_db_version", $updated_to );
			
	endif;

	/******************************************************************
		
	 LESS THAN 4.19.4 
	 
	 - store complementary colour in prep for removal in 4.20
	 - move homepage col 3 bottom widget items to end of homepage col 3 top in prep for new widget area 4.20
	 
	 ******************************************************************/
	
	if ( version_compare( $database_version, "4.19.4", '<' ) ):

		if ( function_exists("RGBToHTML") && get_option('options_enable_automatic_complementary_colour') ):
			$headcol = get_theme_mod('header_background', '#0b2d49');
			$basecol = HTMLToRGB(substr($headcol,1,6));
			$basecol = ChangeLuminosity($basecol, 33);
			$comp = RGBToHTML($basecol); 
			update_option('options_complementary_colour', $comp);
		else:
			// missed an incremental update and the color functions are no longer available so default to header colour 
			$comp = get_option('options_complementary_colour'); 
			if ( !$comp ) $comp = get_theme_mod('header_background', '#0b2d49');
			update_option('options_complementary_colour', $comp);
		endif;
		$sidebar = get_option('sidebars_widgets');
		$col3top = $sidebar['home-widget-area3t'];
		if ( !$col3top ) $col3top = array();
		$col3bot = $sidebar['home-widget-area3b'];
		if ( $col3bot ):
			foreach ( $col3bot as $c){
				$col3top[] = $c;
			}
			$sidebar['home-widget-area3t'] = $col3top;
			$sidebar['home-widget-area3b'] = array();
			update_option('sidebars_widgets', $sidebar);
		endif;
		$updated_to = "4.19.4";
		update_option("govintranet_db_version", $updated_to );
		
	endif;

	// LESS THAN 4.20 remove complementary colour option
	
	if ( version_compare( $database_version, "4.20", '<' ) ):

		delete_option('options_enable_automatic_complementary_colour');
		$updated_to = "4.20";
		update_option("govintranet_db_version", $updated_to );

	endif;

	// LESS THAN 4.27 deactivate media categories plugin, move document_type post meta to taxonomy

	if ( version_compare( $database_version, "4.27", '<' ) ):

		if ( is_plugin_active( '/media-categories/media-categories.php' ) ):
			deactivate_plugins( '/media-categories/media-categories.php' ); 
		endif;

		//Update document types
		global $wpdb;
		$doctypes = $wpdb->get_results("select post_id, meta_value from $wpdb->postmeta where meta_key='document_type' and meta_value <> '';");
		if ( count($doctypes) > 0 ) {
			if ( taxonomy_exists('document-type') ){
				foreach ( $doctypes as $doc ){
					if ( is_array($doc->meta_value) ) {
						foreach ( $doc->meta_value as $d){
							if ( get_the_terms($doc->post_id, 'document-type') ) {
								$term_taxonomy_ids = wp_set_object_terms($doc->post_id, $d, 'document-type', true ); 	
							} else {
								$term_taxonomy_ids = wp_set_object_terms($doc->post_id, $d, 'document-type', false ); 
							}
							if ( is_wp_error( $term_taxonomy_ids ) ) {
								// There was an error somewhere and the terms couldn't be set.
								$update_okay = 0;
								$reason = __("Document type","govintranet");
							} else {
								// Success! These categories were added to the post.
								// Tidy old doc type meta
								delete_post_meta($doc->post_id, 'document_type', $d);
							}
						}
					}
				}
			} else {
				// document types existed but new plugin not activated to register document type taxonomy
				$update_okay = 0;
				$reason = __("Activate HT Media A to Z plugin","govintranet");
			}
			if ( $update_okay ) {
				$updated_to = "4.27";
				update_option("govintranet_db_version", $updated_to );
			}
		} else {
			$updated_to = "4.27";
			update_option("govintranet_db_version", $updated_to );
		}

	endif;

	if ( version_compare( $database_version, "4.32", '<' ) && $update_okay ):

		// Add H1 tags to existing not found text
		
		$search = get_option('options_search_not_found');
		if ( $search ) update_option('options_search_not_found','<h1>'.$search.'</h1><p>'. __( 'The page that you are trying to reach doesn\'t exist. <br><br>Please go back or try searching.','govintranet') . '</p>');
		$fof = get_option('options_page_not_found');
		if ( $fof ) update_option('options_page_not_found','<h1>'.$fof.'</h1>');
		
		$updated_to = "4.32";
		update_option("govintranet_db_version", $updated_to );

	endif;

	if ( version_compare( $database_version, "4.33", '<' ) && $update_okay ):

		// Reschedule expiry cron 

	    $timestamp = wp_next_scheduled( "govintranet_expiry_patrol" );
		if ( $timestamp ) wp_unschedule_event( $timestamp, "govintranet_expiry_patrol" );

		global $wpdb;
		$tdate = date('Ymd');
		
		$expiry = $wpdb->get_results("select post_id from $wpdb->postmeta where meta_key='news_expiry_date' and meta_value >= '" . $tdate . "';");
		if ( $expiry ) foreach ( $expiry as $exp ){
			$post_id = intval($exp->post_id);
			if ( in_array( get_post_status($post_id), array("publish","future") )){
			    $prev = get_post_meta( $post_id, 'news_expiry_time',true );
		    	$exptime = date('H:i',strtotime($prev));
			    $expdate = get_post_meta( $post_id, 'news_expiry_date',true );
			    $timestamp = wp_next_scheduled( "gi_autoexpiry", array($post_id) );
				if ( $timestamp ) wp_unschedule_event( $timestamp, "gi_autoexpiry", array($post_id) );
				$timestamp = strtotime($expdate."T".$exptime.":00");
				wp_schedule_single_event( $timestamp, "gi_autoexpiry", array($post_id) );
			}
		}

		$expiry = $wpdb->get_results("select post_id from $wpdb->postmeta where meta_key='news_update_expiry_date' and meta_value >= '" . $tdate . "';");
		if ( $expiry ) foreach ( $expiry as $exp ){
			$post_id = intval($exp->post_id);
			if ( in_array( get_post_status($post_id), array("publish","future") )){ 
			    $prev = get_post_meta( $post_id, 'news_update_expiry_time',true );
		    	$exptime = date('H:i',strtotime($prev));
			    $expdate = get_post_meta( $post_id, 'news_update_expiry_date',true );
			    $timestamp = wp_next_scheduled( "gi_autoexpiry", array($post_id) );
				if ( $timestamp ) wp_unschedule_event( $timestamp, "gi_autoexpiry", array($post_id) );
				$timestamp = strtotime($expdate."T".$exptime.":00");
				wp_schedule_single_event( $timestamp, "gi_autoexpiry", array($post_id) );
			}
		}
		
		if ( get_option('options_module_events_draft') ){

			$expiry = $wpdb->get_results("select post_id from $wpdb->postmeta where meta_key='event_end_date' and meta_value >= '" . $tdate . "';");
			if ( $expiry ) foreach ( $expiry as $exp ){
				$post_id = intval($exp->post_id);
				if ( in_array( get_post_status($post_id), array("publish","future") ) ){
				    $prev = get_post_meta( $post_id, 'event_end_time',true );
			    	$exptime = date('H:i',strtotime($prev));
				    $expdate = get_post_meta( $post_id, 'event_end_date',true );
				    $timestamp = wp_next_scheduled( "gi_autoexpiry", array($post_id) );
					if ( $timestamp ) wp_unschedule_event( $timestamp, "gi_autoexpiry", array($post_id) );
					$timestamp = strtotime($expdate."T".$exptime.":00");
					wp_schedule_single_event( $timestamp, "gi_autoexpiry", array($post_id) );
				}
			}

		}
		
		$expiry = $wpdb->get_results("select post_id from $wpdb->postmeta where meta_key='vacancy_closing_date' and meta_value >= '" . $tdate . "';");
		if ( $expiry ) foreach ( $expiry as $exp ){
			$post_id = intval($exp->post_id);
			if ( in_array( get_post_status($post_id), array("publish","future") ) ){
			    $prev = get_post_meta( $post_id, 'vacancy_closing_time',true );
		    	$exptime = date('H:i',strtotime($prev));
			    $expdate = get_post_meta( $post_id, 'vacancy_closing_date',true );
			    $timestamp = wp_next_scheduled( "gi_autoexpiry", array($post_id) );
				if ( $timestamp ) wp_unschedule_event( $timestamp, "gi_autoexpiry", array($post_id) );
				$timestamp = strtotime($expdate."T".$exptime.":00");
				wp_schedule_single_event( $timestamp, "gi_autoexpiry", array($post_id) );
			}
		}

		$updated_to = "4.33";
		update_option("govintranet_db_version", $updated_to );

	endif;

	if ( version_compare( $database_version, "4.33.2", '<' ) && $update_okay ):

		// Reschedule expiry cron 

	    $timestamp = wp_next_scheduled( "govintranet_expiry_patrol" );
		if ( $timestamp ) wp_unschedule_event( $timestamp, "govintranet_expiry_patrol" );

		$updated_to = "4.33.2";
		update_option("govintranet_db_version", $updated_to );

	endif;

	if ( version_compare( $database_version, "4.34", '<' ) && $update_okay ):

		// Tidy document attachments
		global $wpdb;
		$blankdocs = $wpdb->get_results("select post_id from $wpdb->postmeta join $wpdb->posts on $wpdb->posts.ID = $wpdb->postmeta.post_id where meta_key = 'document_attachments' and meta_value = 0 and post_status = 'publish';");
		if ( $blankdocs ) foreach ( $blankdocs as $b ){
			delete_post_meta(intval($b->post_id), 'document_attachments');
		}
		
		// Remove old project metadata
		$wpdb->query("delete from $wpdb->postmeta where meta_key='project_policy_link';");
		$wpdb->query("delete from $wpdb->postmeta where meta_key='_project_policy_link';");
		$wpdb->query("delete from $wpdb->postmeta where meta_key='project_start_time';");
		$wpdb->query("delete from $wpdb->postmeta where meta_key='_project_start_time';");
		$wpdb->query("delete from $wpdb->postmeta where meta_key='project_end_time';");
		$wpdb->query("delete from $wpdb->postmeta where meta_key='_project_end_time';");
		$wpdb->query("delete from $wpdb->postmeta where meta_key='project_chapter_number';");
		$wpdb->query("delete from $wpdb->postmeta where meta_key='_project_chapter_number';");
		$wpdb->query("delete from $wpdb->postmeta where meta_key='team_members';");
		$wpdb->query("delete from $wpdb->postmeta where meta_key='_team_members';");

		$updated_to = "4.34";
		update_option("govintranet_db_version", $updated_to );

	endif;

	if ( version_compare( $database_version, "4.34.2", '<' ) && $update_okay ):

		// Tidy forum templates
		global $wpdb;
		$forums = $wpdb->query("update $wpdb->postmeta set meta_value = 'bbpress/page-forum.php' where meta_key = '_wp_page_template' and meta_value = 'page-forum.php';");
		$forums = $wpdb->query("update $wpdb->postmeta set meta_value = 'bbpress/page-forum-simple.php' where meta_key = '_wp_page_template' and meta_value = 'page-forum-simple.php';");
		
		// Reschedule patrol job
	    $timestamp = wp_next_scheduled( "govintranet_expiry_patrol" );
		if ( $timestamp ) wp_unschedule_event( $timestamp, "govintranet_expiry_patrol" );

		$updated_to = "4.34.2";
		update_option("govintranet_db_version", $updated_to );

	endif;

	if ( version_compare( $database_version, "4.36", '<' ) && $update_okay ):

		// PROCESS NEWS
		if ( get_option('options_module_news') ){
			$oldnews = query_posts(array(
				'post_type'=>'news',
				'meta_query'=>array(
					'relation' => 'AND',
					array(
					'key'=>'news_auto_expiry',
					'value'=>1,
					),
					array(
					'key'=>'news_expiry_date',
					'value'=>$tdate,
					'compare'=>'>='
					),
				)));
			if ( count($oldnews) > 0 ){
				foreach ($oldnews as $old) {
					wp_clear_scheduled_hook( 'gi_autoexpiry', $old->ID );
				}
			}
		}
		// PROCESS NEWS UPDATES
		if ( get_option('options_module_news_updates') ){
			$oldnews = query_posts(array(
				'post_type'=>'news-update',
				'meta_query'=>array(
					"relation" => "AND",
					array(
					'key'=>'news_update_auto_expiry',
					'value'=>1,
					),
					array(
					'key'=>'news_update_expiry_date',
					'value'=>$tdate,
					'compare'=>'>='
					)
				)));
			if ( count($oldnews) > 0 ){
				foreach ($oldnews as $old) {
					wp_clear_scheduled_hook( 'gi_autoexpiry', $old->ID );
				}
			}	
		}
		// PROCESS VACANCIES
		if ( get_option('options_module_vacancies') ){
			$ttime = date('H:i'); 
			$oldvacs = query_posts(array(
			'post_type'=>'vacancy',
			'meta_query'=>array(array(
			'key'=>'vacancy_closing_date',
			'value'=>$tdate,
			'compare'=>'>=',
			))));
			if ( count($oldvacs) > 0 ){
				foreach ($oldvacs as $old) {
					wp_clear_scheduled_hook( 'gi_autoexpiry', $old->ID );
				}	
			}
		}
		// PROCESS EVENTS
		if ( get_option('options_module_events_draft') ){
			$oldvacs = query_posts(array(
				'post_type'=>'event',
				'meta_query'=>array(array(
				'key'=>'event_end_date',
				'value'=>$tdate,
				'compare'=>'>='
				))));
			if ( count($oldvacs) > 0 ){
				foreach ($oldvacs as $old) {
					wp_clear_scheduled_hook( 'gi_autoexpiry', $old->ID );
				}	
			}
		}
	
		$updated_to = "4.36";
		update_option("govintranet_db_version", $updated_to );
		
	endif;
	
	if ( version_compare( $database_version, "4.38", '<' ) && $update_okay ):
		
		// Move header text color option to new btn text colour option, remove option to hide sitename 
		
		$head_text = get_theme_mod( "header_textcolor", "ffffff" );
		if ( $head_text == "blank" ) $head_text = "ffffff";
		update_option("options_btn_text_colour", "#" . $head_text );
		if ( get_option( "options_hide_sitename" ) ) set_theme_mod( "header_textcolor", "blank" );
		delete_option( "options_hide_sitename" );

		$updated_to = "4.38";
		update_option("govintranet_db_version", $updated_to );
		
	endif;

	if ( version_compare( $database_version, "4.38.1", '<' ) && $update_okay ):
	
		// Reschedule patrol job
		wp_clear_scheduled_hook( 'govintranet_expiry_patrol' );

		$updated_to = "4.38.1";
		update_option("govintranet_db_version", $updated_to );
		
	endif;


	// UPDATE DATABASE VERSION
	
	if ( $update_okay ):
		// Update the database version
		update_option("govintranet_db_version", $theme_version );
		$class = 'notice notice-info is-dismissible';
		$message = sprintf( __( 'Updated to GovIntranet version %1$s', 'govintranet' ), $theme_version );
		$links = __("visit <a href='https://help.govintra.net/'>GovIntranetters</a> for latest features.","govintranet");
		printf( '<div class="%1$s"><p><strong>%2$s</strong> &raquo; %3$s</p></div>', $class, $message, $links ); 
	else:
		$class = 'notice notice-error is-dismissible';
		$message = sprintf( __( 'Error updating version %2$s database to GovIntranet version %1$s', 'govintranet' ), $theme_version, $database_version );
		if ( $updated_to ) {
			$message.= ". " . __("Current database version: ","govintranet") . $updated_to;
			update_option("govintranet_db_version", $updated_to );
		}
		if ( $reason ) $message.= "[" . $reason . "]";
		printf( '<div class="%1$s"><p><strong>%2$s</strong></p></div>', $class, $message ); 
	endif;

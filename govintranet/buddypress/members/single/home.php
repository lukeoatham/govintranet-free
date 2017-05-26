<?php
/**
 * BuddyPress - Members Home (public profile)
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

?>

<div id="buddypress">

	<?php

	/**
	 * Fires before the display of member home content.
	 *
	 * @since 1.2.0
	 */
	do_action( 'bp_before_member_home_content' ); ?>

	<div id="item-header" role="complementary">

		<?php
		/**
		 * If the cover image feature is enabled, use a specific header
		 */
		if ( bp_displayed_user_use_cover_image_header() ) :
			bp_get_template_part( 'members/single/cover-image-header' );
		else :
			bp_get_template_part( 'members/single/member-header' );
		endif;
		?>

	</div><!-- #item-header -->
	<div class="col-sm-12">
	<div id="item-nav">
		<div class="item-list-tabs no-ajax" id="object-nav" aria-label="<?php esc_attr_e( 'Member primary navigation', 'buddypress' ); ?>" role="navigation">
			<ul>

				<?php bp_get_displayed_user_nav(); ?>

				<?php

				/**
				 * Fires after the display of member options navigation.
				 *
				 * @since 1.2.4
				 */
				do_action( 'bp_member_options_nav' ); ?>

			</ul>
		</div>
	</div><!-- #item-nav -->
	</div>
	<div  class="col-sm-8">

	<div id="item-body">

		<?php

		/**
		 * Fires before the display of member body content.
		 *
		 * @since 1.2.0
		 */
		do_action( 'bp_before_member_body' );

		if ( bp_is_user_front() ) :
			bp_displayed_user_front_template_part();

		elseif ( bp_is_user_activity() ) :
			bp_get_template_part( 'members/single/activity' );

		elseif ( bp_is_user_blogs() ) :
			bp_get_template_part( 'members/single/blogs'    );

		elseif ( bp_is_user_friends() ) :
			bp_get_template_part( 'members/single/friends'  );

		elseif ( bp_is_user_groups() ) :
			bp_get_template_part( 'members/single/groups'   );

		elseif ( bp_is_user_messages() ) :
			bp_get_template_part( 'members/single/messages' );

		elseif ( bp_is_user_profile() ) :
			bp_get_template_part( 'members/single/profile'  );

		elseif ( bp_is_user_forums() ) :
			bp_get_template_part( 'members/single/forums'   );

		elseif ( bp_is_user_notifications() ) :
			bp_get_template_part( 'members/single/notifications' );

		elseif ( bp_is_user_settings() ) :
			bp_get_template_part( 'members/single/settings' );

		// If nothing sticks, load a generic template
		else :
			bp_get_template_part( 'members/single/plugins'  );

		endif;

		/**
		 * Fires after the display of member body content.
		 *
		 * @since 1.2.0
		 */
		do_action( 'bp_after_member_body' ); ?>

	</div><!-- #item-body -->
	</div>
	<?php

	/**
	 * Fires after the display of member home content.
	 *
	 * @since 1.2.0
	 */
	do_action( 'bp_after_member_home_content' ); ?>
	<div class="col-sm-4">
		<?php if ( get_option('options_module_staff_directory') ){ ?>
			<?php 
			$user_id = bp_displayed_user_id();
			$poduserparent = get_user_meta( $user_id , 'user_line_manager', true); 
			$poduserparent = get_userdata($poduserparent);
			echo "<div class='panel panel-default'>
			<div class='panel-heading oc'>" . __('Organisation tree' , 'govintranet') . "</div>
			<div class='panel-body'>
			<div class='oc'>";
			if ($poduserparent){
				$avstyle="";
				if ( $directorystyle==1 ) $avstyle = " img-circle";
				$avatarhtml = get_avatar($poduserparent->ID , 150,'',$poduserparent->display_name);
				$avatarhtml = str_replace(" photo", " photo ".$avstyle, $avatarhtml);
				$avatarhtml = str_replace('"150"', '"96"', $avatarhtml);
				$avatarhtml = str_replace("'150'", "'96'", $avatarhtml);
				echo "<a title='".esc_attr($poduserparent->display_name)."' href='".site_url()."/members/".$poduserparent->user_nicename."/profile/'>".$avatarhtml."</a>";										
				echo "<p><a href='".site_url()."/members/".$poduserparent->user_nicename."/profile/'>".$poduserparent->display_name."</a><br>";
				echo get_user_meta($poduserparent->ID,'user_job_title',true);
				echo "</p>";
				echo "<p><i class='dashicons dashicons-arrow-up-alt2'></i></p>";
			}
			echo "<p><strong>";
			bbp_displayed_user_field( 'display_name' );
			echo "<br>".get_user_meta($user_id,'user_job_title',true);
			echo "</strong></p>";
			$q = "select meta_value as ID, user_id, display_name from $wpdb->users join $wpdb->usermeta on $wpdb->users.ID = $wpdb->usermeta.user_id where $wpdb->usermeta.meta_key='user_line_manager' and $wpdb->usermeta.meta_value = ".$user_id;
			$poduserreports = $wpdb->get_results($q,ARRAY_A);
			if (count($poduserreports)>0){
				echo "<p><i class='dashicons dashicons-arrow-down-alt2'></i></p>";
				echo "<p id='directreports'>";
				foreach ($poduserreports as $p){ 
					$pid = $p['user_id'];
	                $u = get_userdata($pid);
	                $jobtitle = get_user_meta($pid, 'user_job_title', true);
	                if ($jobtitle) $jobtitle = " - ".$jobtitle;
					$imgstyle='';
					$avstyle="";
					if ( $directorystyle==1 ) $avstyle = " img-circle";
					$imgsrc = get_avatar($pid, 66,'',$u->display_name);				
					$imgsrc = str_replace(" photo", " photo ".$avstyle, $imgsrc);
					echo "<a title='".esc_attr( $u->display_name )."' href='".site_url()."/members/".$u->user_nicename."/profile/'>".$imgsrc."</a>";
				}
				echo "</p>";
			}
			echo "</div></div></div>";
		} ?>		
	</div>

</div><!-- #buddypress -->

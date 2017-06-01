<?php
/**
 * BuddyPress - User Profile personal options
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

?>

<div class="item-list-tabs no-ajax" id="subnav" aria-label="<?php esc_attr_e( 'Member secondary navigation', 'buddypress' ); ?>" role="navigation">
	<ul>
		<?php bp_get_options_nav(); ?>
		<?php if ( bbp_is_user_home() || current_user_can( 'edit_users' ) ) : ?>
		<li class="<?php if ( bbp_is_single_user_edit() ) :?>current<?php endif; ?>">
				<a href="<?php bbp_user_profile_edit_url(); ?>" title="<?php printf( esc_attr__( "Edit %s's profile", 'govintranet' ), bbp_get_displayed_user_field( 'display_name' ) ); ?>"><?php _e( 'Edit profile', 'govintranet' ); ?></a>
		</li>
		<?php endif; ?>
	</ul>
</div><!-- .item-list-tabs -->

<?php

/**
 * Fires before the display of member profile content.
 *
 * @since 1.1.0
 */
do_action( 'bp_before_profile_content' ); ?>

<div class="profile">

<?php switch ( bp_current_action() ) :

	// Edit
	case 'edit'   :
		bp_get_template_part( 'members/single/profile/edit' );
		break;

	// Change Avatar
	case 'change-avatar' :
		bp_get_template_part( 'members/single/profile/change-avatar' );
		break;

	// Change Cover Image
	case 'change-cover-image' :
		bp_get_template_part( 'members/single/profile/change-cover-image' );
		break;

	// Compose
	case 'public' :

		// Display XProfile
		if ( bp_is_active( 'xprofile' ) )
			bp_get_template_part( 'members/single/profile/profile-loop' );

		// Display WordPress profile (fallback)
		else
			bp_get_template_part( 'members/single/profile/profile-wp' );

		break;

	// Any other
	default :
		bp_get_template_part( 'members/single/plugins' );
		break;
endswitch; ?>
</div><!-- .profile -->

<?php

/**
 * Fires after the display of member profile content.
 *
 * @since 1.1.0
 */
do_action( 'bp_after_profile_content' ); ?>

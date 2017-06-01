<?php
/**
 * BuddyPress - Members Single Profile Edit
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

/**
 * Fires after the display of member profile edit content.
 *
 * @since 1.1.0
 */
 
?>
 

<?php 
 
do_action( 'bp_before_profile_edit_content' );
?>
<form class="gi_profile_fields" id="bbp-your-profile" action="<?php bp_the_profile_group_edit_form_action(); ?>" method="post" enctype="multipart/form-data">

	<h2 class="entry-title gi-name"><?php _e( 'Name', 'govintranet' ) ?></h2>

	<?php do_action( 'bbp_user_edit_before' ); ?>

	<fieldset class="bbp-form gi-name">
		<legend><?php _e( 'Name', 'govintranet' ) ?></legend>

		<?php do_action( 'bbp_user_edit_before_name' ); ?>

		<div>
			<label for="first_name"><?php _e( 'First name', 'govintranet' ) ?></label>
			<input type="text" name="first_name" id="first_name" value="<?php bbp_displayed_user_field( 'first_name', 'edit' ); ?>" class="regular-text" tabindex="<?php bbp_tab_index(); ?>" />
		</div>

		<div>
			<label for="last_name"><?php _e( 'Last name', 'govintranet' ) ?></label>
			<input type="text" name="last_name" id="last_name" value="<?php bbp_displayed_user_field( 'last_name', 'edit' ); ?>" class="regular-text" tabindex="<?php bbp_tab_index(); ?>" />
		</div>

		<div>
			<label for="nickname"><?php _e( 'Nickname', 'govintranet' ); ?></label>
			<input type="text" name="nickname" id="nickname" value="<?php bbp_displayed_user_field( 'nickname', 'edit' ); ?>" class="regular-text" tabindex="<?php bbp_tab_index(); ?>" />
		</div>

		<div>
			<label for="display_name"><?php _e( 'Display name', 'govintranet' ) ?></label>

			<?php bbp_edit_user_display_name(); ?>

		</div>

		<?php do_action( 'bbp_user_edit_after_name' ); ?>

	</fieldset>

	<h2 class="entry-title gi-about"><?php  _e( 'About', 'govintranet' ) ; ?></h2>

	<fieldset class="bbp-form gi-about">
		<legend><?php _e( 'About', 'govintranet' ) ; ?></legend>

		<?php do_action( 'bbp_user_edit_before_about' ); ?>

		<div>
			<label for="description"><?php _e( 'About me', 'govintranet' ); ?></label>
			<textarea name="description" id="description" rows="5" cols="30" tabindex="<?php bbp_tab_index(); ?>"><?php bbp_displayed_user_field( 'description', 'edit' ); ?></textarea>
		</div>

		<?php do_action( 'bbp_user_edit_after_about' ); ?>

	</fieldset>



	<h2 class="entry-title gi-account"><?php _e( 'Account', 'govintranet' ) ?></h2>

	<fieldset class="bbp-form gi-account">
		<legend><?php _e( 'Account', 'govintranet' ) ?></legend>

		<?php do_action( 'bbp_user_edit_before_account' ); ?>

		<div>
			<label for="user_login"><?php _e( 'Username', 'govintranet' ); ?></label>
			<input type="text" name="user_login" id="user_login" value="<?php bbp_displayed_user_field( 'user_login', 'edit' ); ?>" disabled="disabled" class="regular-text" tabindex="<?php bbp_tab_index(); ?>" />
		</div>

		<div class="gi-email">
			<label for="email"><?php _e( 'Email', 'govintranet' ); ?></label>

			<input type="text" name="email" id="email" value="<?php bbp_displayed_user_field( 'user_email', 'edit' ); ?>" class="regular-text" tabindex="<?php bbp_tab_index(); ?>" />

			<?php

			// Handle address change requests
			$new_email = get_option( bbp_get_displayed_user_id() . '_new_email' );
			if ( !empty( $new_email ) && $new_email !== bbp_get_displayed_user_field( 'user_email', 'edit' ) ) : ?>

				<span class="updated inline">

					<?php printf( __( 'There is a pending email address change to <code>%1$s</code>. <a href="%2$s">Cancel</a>', 'govintranet' ), $new_email['newemail'], esc_url( self_admin_url( 'user.php?dismiss=' . bbp_get_current_user_id()  . '_new_email' ) ) ); ?>

				</span>

			<?php endif; ?>

		</div>
	<?php if ( !function_exists("GoogleAppsLogin")): ?>

		<div id="password">
			<label for="pass1"><?php _e( 'New password', 'govintranet' ); ?></label>
			<fieldset class="bbp-form password">
				<input type="password" name="pass1" id="pass1" size="16" value="" autocomplete="off" tabindex="<?php bbp_tab_index(); ?>" />
				<span class="description"><?php _e( 'If you would like to change the password type a new one. Otherwise leave this blank.', 'govintranet' ); ?></span>

				<input type="password" name="pass2" id="pass2" size="16" value="" autocomplete="off" tabindex="<?php bbp_tab_index(); ?>" />
				<span class="description"><?php _e( 'Type your new password again.', 'govintranet' ); ?></span><br />

				<div id="pass-strength-result"></div>
				<span class="description indicator-hint"><?php _e( 'Your password should be at least ten characters long. Phrases with numbers and symbols are easier to remember, e.g. "Her Imperial Highness has 30 handbags!"', 'govintranet' ); ?></span>
			</fieldset>
		</div>
	<?php endif; ?>
		
		<?php do_action( 'bbp_user_edit_after_account' ); ?>

	</fieldset>



	<?php if ( current_user_can( 'edit_users' ) && ! bbp_is_user_home_edit() ) : ?>

		<h2 class="entry-title gi-role"><?php _e( 'Role', 'govintranet' ) ?></h2>

		<fieldset class="bbp-form gi-role">
			<legend><?php _e( 'Role', 'govintranet' ); ?></legend>

			<?php do_action( 'bbp_user_edit_before_role' ); ?>

			<?php if ( is_multisite() && is_super_admin() && current_user_can( 'manage_network_options' ) ) : ?>

				<div>
					<label for="super_admin"><?php _e( 'Network role', 'govintranet' ); ?></label>
					<label>
						<input class="checkbox" type="checkbox" id="super_admin" name="super_admin"<?php checked( is_super_admin( bbp_get_displayed_user_id() ) ); ?> tabindex="<?php bbp_tab_index(); ?>" />
						<?php _e( 'Grant this user super admin privileges for the Network.', 'govintranet' ); ?>
					</label>
				</div>

			<?php endif; ?>

			<?php bbp_get_template_part( 'form', 'user-roles' ); ?>

			<?php do_action( 'bbp_user_edit_after_role' ); ?>

		</fieldset>

	<?php endif; ?>

	<?php if ( get_option("options_module_staff_directory")): ?>
	<h2 class="entry-title"><?php _e('Staff directory','govintranet'); ?></h2>
	<?php endif; ?>

	<?php 
	do_action( 'bbp_user_edit_after' ); 
	?>

	<fieldset class="submit">
		<legend><?php _e( 'Save changes', 'govintranet' ); ?></legend>
		<div>

			<?php bbp_edit_user_form_fields(); ?>

			<label for="bbp_user_edit_submit" class="sr-only"><?php bbp_is_user_home_edit() ? _e( 'Update profile', 'govintranet' ) : _e( 'Update user', 'govintranet' ); ?></label>
			<input type="submit" id="bbp_user_edit_submit" name="bbp_user_edit_submit" class="button submit user-submit" value="<?php bbp_is_user_home_edit() ? _e( 'Update profile', 'govintranet' ) : _e( 'Update user', 'govintranet' ); ?>" />
		</div>
	</fieldset>

</form>
<?php

if ( bp_has_profile( 'profile_group_id=' . bp_get_current_profile_group_id() ) ) :
	while ( bp_profile_groups() ) : bp_the_profile_group(); ?>

<form action="<?php bp_the_profile_group_edit_form_action(); ?>" method="post" id="profile-edit-form" class="standard-form <?php bp_the_profile_group_slug(); ?>">

	<?php

		/** This action is documented in bp-templates/bp-legacy/buddypress/members/single/profile/profile-wp.php */
		do_action( 'bp_before_profile_field_content' ); ?>

		<h2><?php printf( __( "Editing '%s' Profile Group", 'buddypress' ), bp_get_the_profile_group_name() ); ?></h2>

		<?php if ( bp_profile_has_multiple_groups() ) : ?>
			<ul class="button-nav" aria-label="<?php esc_attr_e( 'Profile field groups', 'buddypress' ); ?>" role="navigation">

				<?php bp_profile_group_tabs(); ?>

			</ul>
		<?php endif ;?>

		<div class="clear"></div>

		<?php while ( bp_profile_fields() ) : bp_the_profile_field(); ?>

			<div<?php bp_field_css_class( 'editfield' ); ?>>

				<?php
				$field_type = bp_xprofile_create_field_type( bp_get_the_profile_field_type() );
				$field_type->edit_field_html();

				/**
				 * Fires before the display of visibility options for the field.
				 *
				 * @since 1.7.0
				 */
				do_action( 'bp_custom_profile_edit_fields_pre_visibility' );
				?>

				<?php if ( bp_current_user_can( 'bp_xprofile_change_field_visibility' ) ) : ?>
					<p class="field-visibility-settings-toggle" id="field-visibility-settings-toggle-<?php bp_the_profile_field_id() ?>">
						<?php
						printf(
							__( 'This field can be seen by: %s', 'buddypress' ),
							'<span class="current-visibility-level">' . bp_get_the_profile_field_visibility_level_label() . '</span>'
						);
						?>
						<a href="#" class="visibility-toggle-link"><?php _e( 'Change', 'buddypress' ); ?></a>
					</p>

					<div class="field-visibility-settings" id="field-visibility-settings-<?php bp_the_profile_field_id() ?>">
						<fieldset>
							<legend><?php _e( 'Who can see this field?', 'buddypress' ) ?></legend>

							<?php bp_profile_visibility_radio_buttons() ?>

						</fieldset>
						<a class="field-visibility-settings-close" href="#"><?php _e( 'Close', 'buddypress' ) ?></a>
					</div>
				<?php else : ?>
					<div class="field-visibility-settings-notoggle" id="field-visibility-settings-toggle-<?php bp_the_profile_field_id() ?>">
						<?php
						printf(
							__( 'This field can be seen by: %s', 'buddypress' ),
							'<span class="current-visibility-level">' . bp_get_the_profile_field_visibility_level_label() . '</span>'
						);
						?>
					</div>
				<?php endif ?>

				<?php

				/**
				 * Fires after the visibility options for a field.
				 *
				 * @since 1.1.0
				 */
				do_action( 'bp_custom_profile_edit_fields' ); ?>

				<p class="description"><?php bp_the_profile_field_description(); ?></p>
			</div>

		<?php endwhile; ?>

	<?php

	/** This action is documented in bp-templates/bp-legacy/buddypress/members/single/profile/profile-wp.php */
	do_action( 'bp_after_profile_field_content' ); ?>

	<div class="submit">
		<input type="submit" name="profile-group-edit-submit" id="profile-group-edit-submit" value="<?php esc_attr_e( 'Save Changes', 'buddypress' ); ?> " />
	</div>

	<input type="hidden" name="field_ids" id="field_ids" value="<?php bp_the_profile_field_ids(); ?>" />

	<?php wp_nonce_field( 'bp_xprofile_edit' ); ?>

</form>

<?php endwhile; endif; ?>

<?php

/**
 * Fires after the display of member profile edit content.
 *
 * @since 1.1.0
 */
do_action( 'bp_after_profile_edit_content' ); ?>

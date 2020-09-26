<?php

/**
 * bbPress User Profile Edit Part
 *
 * @package bbPress
 * @subpackage Theme
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

?>

<form class="col-lg-10 col-md-10 col-sm-9" id="bbp-your-profile" action="<?php bbp_user_profile_edit_url( bbp_get_displayed_user_id() ); ?>" method="post" enctype="multipart/form-data">

	<h2 class="entry-title gi-name"><?php esc_html_e( 'Name', 'govintranet' ) ?></h2>

	<?php do_action( 'bbp_user_edit_before' ); ?>

	<fieldset class="bbp-form gi-name">
		<legend><?php esc_html_e( 'Name', 'govintranet' ) ?></legend>

		<?php do_action( 'bbp_user_edit_before_name' ); ?>

		<div>
			<label for="first_name"><?php esc_html_e( 'First name', 'govintranet' ) ?></label>
			<input type="text" name="first_name" id="first_name" value="<?php bbp_displayed_user_field( 'first_name', 'edit' ); ?>" class="regular-text" />
		</div>

		<div>
			<label for="last_name"><?php esc_html_e( 'Last name', 'govintranet' ) ?></label>
			<input type="text" name="last_name" id="last_name" value="<?php bbp_displayed_user_field( 'last_name', 'edit' ); ?>" class="regular-text" />
		</div>

		<div>
			<label for="nickname"><?php esc_html_e( 'Nickname', 'govintranet' ); ?></label>
			<input type="text" name="nickname" id="nickname" value="<?php bbp_displayed_user_field( 'nickname', 'edit' ); ?>" class="regular-text" />
		</div>

		<div>
			<label for="display_name"><?php esc_html_e( 'Display name', 'govintranet' ) ?></label>

			<?php bbp_edit_user_display_name(); ?>

		</div>

		<?php do_action( 'bbp_user_edit_after_name' ); ?>

	</fieldset>

	<h2 class="entry-title gi-about"><?php  _e( 'About', 'govintranet' ) ; ?></h2>

	<fieldset class="bbp-form gi-about">
		<legend><?php esc_html_e( 'About', 'govintranet' ) ; ?></legend>

		<?php do_action( 'bbp_user_edit_before_about' ); ?>

		<div>
			<label for="description"><?php esc_html_e( 'About me', 'govintranet' ); ?></label>
			<textarea name="description" id="description" rows="5" cols="30"><?php bbp_displayed_user_field( 'description', 'edit' ); ?></textarea>
		</div>

		<?php do_action( 'bbp_user_edit_after_about' ); ?>

	</fieldset>



	<h2 class="entry-title gi-account"><?php esc_html_e( 'Account', 'govintranet' ) ?></h2>

	<fieldset class="bbp-form">
		<legend><?php esc_html_e( 'Account', 'govintranet' ) ?></legend>

		<?php do_action( 'bbp_user_edit_before_account' ); ?>

		<div>
			<label for="user_login"><?php esc_html_e( 'Username', 'govintranet' ); ?></label>
			<input type="text" name="user_login" id="user_login" value="<?php bbp_displayed_user_field( 'user_login', 'edit' ); ?>" maxlength="100" disabled="disabled" class="regular-text" />
		</div>

		<div>
			<label for="email"><?php esc_html_e( 'Email', 'govintranet' ); ?></label>
			<input type="text" name="email" id="email" value="<?php bbp_displayed_user_field( 'user_email', 'edit' ); ?>" maxlength="100" class="regular-text" autocomplete="off" />
		</div>

		<?php if ( !function_exists("GoogleAppsLogin")): ?>

		<?php bbp_get_template_part( 'form', 'user-passwords' ); ?>

		<?php endif; ?>

		<?php if ( function_exists('bbp_edit_user_language') ) : ?>

		<div>
			<label for="url"><?php esc_html_e( 'Language', 'govintranet' ) ?></label>
				
			<?php bbp_edit_user_language(); ?>

		</div>
			
		<?php endif; ?>


		<?php do_action( 'bbp_user_edit_after_account' ); ?>

	</fieldset>

	<?php if ( current_user_can( 'edit_users' ) && ! bbp_is_user_home_edit() ) : ?>

		<h2 class="entry-title gi-role"><?php esc_html_e( 'Role', 'govintranet' ) ?></h2>

		<fieldset class="bbp-form gi-role">
			<legend><?php esc_html_e( 'Role', 'govintranet' ); ?></legend>

			<?php do_action( 'bbp_user_edit_before_role' ); ?>

			<?php if ( is_multisite() && is_super_admin() && current_user_can( 'manage_network_options' ) ) : ?>

				<div>
					<label for="super_admin"><?php esc_html_e( 'Network role', 'govintranet' ); ?></label>
					<label>
						<input class="checkbox" type="checkbox" id="super_admin" name="super_admin"<?php checked( is_super_admin( bbp_get_displayed_user_id() ) ); ?> />
						<?php esc_html_e( 'Grant this user super admin privileges for the Network.', 'govintranet' ); ?>
					</label>
				</div>

			<?php endif; ?>

			<?php bbp_get_template_part( 'form', 'user-roles' ); ?>

			<?php do_action( 'bbp_user_edit_after_role' ); ?>

		</fieldset>

	<?php endif; ?>

	<?php if ( get_option("options_module_staff_directory")): ?>
	<h2 class="entry-title"><?php esc_html_e('Staff directory','govintranet'); ?></h2>
	<?php endif; ?>

	<?php 
	do_action( 'bbp_user_edit_after' ); 
	?>

	<fieldset class="submit">
		<legend><?php esc_html_e( 'Save changes', 'govintranet' ); ?></legend>
		<div>

			<?php bbp_edit_user_form_fields(); ?>

			<label for="bbp_user_edit_submit" class="sr-only"><?php bbp_is_user_home_edit() ? _e( 'Update profile', 'govintranet' ) : _e( 'Update user', 'govintranet' ); ?></label>
			<input type="submit" id="bbp_user_edit_submit" name="bbp_user_edit_submit" class="button submit user-submit" value="<?php bbp_is_user_home_edit() ? _e( 'Update profile', 'govintranet' ) : _e( 'Update user', 'govintranet' ); ?>" />
		</div>
	</fieldset>

</form>
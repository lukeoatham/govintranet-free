<?php

//add_action( 'admin_init', 'theme_options_init' );
//add_action( 'admin_menu', 'theme_options_add_page' );

/**
 * Init plugin options to white list our options
 */
function theme_options_init(){
	register_setting( 'govintranetpress_options', 'govintranetpress_theme_options', 'theme_options_validate' );
}

/**
 * Load up the menu page
 */
function theme_options_add_page() {
	add_theme_page( __( 'Theme Options' ), __( 'Theme Options' ), 'edit_theme_options', 'theme_options', 'theme_options_do_page' );
}

/**
 * Create arrays for our select and radio options
 */



/**
 * Create the options page
 */
function theme_options_do_page() {
	//global $radio_headerblock_options; // header block layout

	if ( ! isset( $_REQUEST['updated'] ) )
		$_REQUEST['updated'] = false;

	?>
	<div class="wrap">
		<?php screen_icon(); echo "<h2>intranet " . __( ' Site Options' ) . "</h2>"; ?>

		<?php if ( false !== $_REQUEST['updated'] ) : ?>
		<div class="updated fade"><p><strong><?php _e( 'Options saved' ); ?></strong></p></div>
		<?php endif; ?>

		<!--
		<script type="text/javascript" src='http://www.pentri.com/assets/jscolor/jscolor.js'></script>
		-->
		
		<form method="post" action="options.php">
			<?php settings_fields( 'govintranetpress_options' ); ?>
			<?php $options = get_option( 'govintranetpress_theme_options' ); ?>			

			<?php //set up defaults for some fields 
			
			?>

			<table class="form-table">

				<!-- 	Google Analytics tracking code -->
	
				<tr valign="top"><th scope="row"><?php _e( 'Google Analytics tracking code' ); ?></th>
					<td>
						<textarea id="govintranetpress_theme_options[analyticscode]" class="large-text" cols="50" rows="8" name="govintranetpress_theme_options[analyticscode]"><?php echo stripslashes( $options['analyticscode'] ); ?></textarea>
						<label class="description" for="govintranetpress_theme_options[analyticscode]"><?php _e( 'Tracking code for analytics. To avoid conflicts, do not use this if you are using the Google Analyticator plugin.' ); ?></label>
					</td>
				</tr>

				<!-- -->

				<!-- 	Custom CSS  -->
	
				<tr valign="top"><th scope="row"><?php _e( 'Advanced: custom CSS rules' ); ?></th>
					<td>
						<textarea id="govintranetpress_theme_options[customcss]" class="large-text" cols="50" rows="10" name="govintranetpress_theme_options[customcss]"><?php echo stripslashes( $options['customcss'] ); ?></textarea>
						<label class="description" for="govintranetpress_theme_options[customcss]"><?php _e( 'Custom CSS rules (advanced users only)' ); ?></label>
					</td>
				</tr>

				<!-- -->
				
				</table>

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e( 'Save Options' ); ?>" />
			</p>
		</form>
	</div>
		
	<?php
}

/**
 * Sanitize and validate input. Accepts an array, return a sanitized array.
 */
function theme_options_validate( $input ) {
	//global $radio_headerblock_options; // homepage layout


	// Our checkbox value is either 0 or 1
	if ( ! isset( $input['option1'] ) )
		$input['option1'] = null;
	$input['option1'] = ( $input['option1'] == 1 ? 1 : 0 );

	// Say our text option must be safe text with no HTML tags
	$input['sometext'] = wp_filter_nohtml_kses( $input['sometext'] );

	/*
	// Our select option must actually be in our array of select options
	if ( ! array_key_exists( $input['selectinput'], $select_options ) )
		$input['selectinput'] = null;
	*/

	// Our radio option must actually be in our array of radio options
	//if ( ! isset( $input['radio_headerblock_input'] ) )
	//	$input['radio_headerblock_input'] = null;
	//if ( ! array_key_exists( $input['radio_headerblock_input'], $radio_headerblock_options ) )
	//	$input['radio_headerblock_input'] = null;


	// Say our textarea option must be safe text with the allowed tags for posts
	// $input['analyticscode'] = wp_filter_post_kses( $input['analyticscode'] ); // disabled, as we want to allow any code there

	return $input;
}

// adapted from http://planetozh.com/blog/2009/05/handling-plugins-options-in-wordpress-28-with-register_setting/
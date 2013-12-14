<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package WordPress
 */

get_header(); ?>

	<div class="row">
		<div class='col-lg-12 white'>
			<div class="content-wrapper">
				<h1 class="entry-title"><?php _e( 'That\'s an error', 'twentyten' ); ?></h1>
				<p><?php _e( 'The page that you are trying to reach doesn\'t exist. <br><br>Please go back.', 'twentyten' ); ?></p><br>
			</div>
		</div>
	</div>

<?php get_footer(); ?>

<?php
/**
 * The Footer widget areas.
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */
?>

<?php
	/* The footer widget area is triggered if any of the areas
	 * have widgets. So let's check that first.
	 *
	 * If none of the sidebars have widgets, then let's bail early.
	 */
	if (   ! is_active_sidebar( 'first-footer-widget-area'  )
		&& ! is_active_sidebar( 'second-footer-widget-area' )

	)
		return;
	// If we get this far, we have widgets. Let do this.
?>

<?php if ( is_active_sidebar( 'first-footer-widget-area' ) ) : ?>
					<div id='footer-left' class="ninecol">

						<ul class="xoxo">
							<?php dynamic_sidebar( 'first-footer-widget-area' ); ?>
						</ul>
					
					</div>
<?php endif; ?>

<?php if ( is_active_sidebar( 'second-footer-widget-area' ) ) : ?>

					<div id='footer-right' class="threecol last">
	
						<ul class="xoxo">
							<?php dynamic_sidebar( 'second-footer-widget-area' ); ?>
						</ul>
					
					</div>
<?php endif; ?>

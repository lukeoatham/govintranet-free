<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content
 * after.  Calls sidebar-footer.php for bottom widgets.
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */
?>
</div>
</div>
				<div id='footerwrapper'>
						<div class="container">
					<div id='footer' class="row">
							<div id='footer-left' class="col-lg-6  col-md-6 col-sm-8">
								<?php if ( is_active_sidebar( 'first-footer-widget-area' ) ) : ?>
									<?php dynamic_sidebar( 'first-footer-widget-area' ); ?>
								<?php endif; ?>
							</div>
							<div id='footer-3' class="col-lg-3 col-md-3 col-sm-4">
							<?php if ( is_active_sidebar( 'right1-footer-widget-area' ) ) : ?>
								<?php dynamic_sidebar( 'right1-footer-widget-area' ); ?>
							<?php endif; ?>
							</div>
							<div id='footer-right' class="col-lg-3 col-md-3 col-sm-4">
							<?php if ( is_active_sidebar( 'right2-footer-widget-area' ) ) : ?>
								<?php dynamic_sidebar( 'right2-footer-widget-area' ); ?>
							<?php endif; ?>
							</div>
						</div>
					</div>
				

</div><!-- container -->


<script type='text/javascript'>
    jQuery(document).ready(function(){
//         jQuery("#primarynav").Touchdown();
//         jQuery("#feedback").Touchdown();

 		// add dynamic classes for IE8 last-child support
 		jQuery('#menu-utilities li').last().addClass('last-link');
 		jQuery('#menu-footer-left li').last().addClass('last-link');
 		jQuery('#primarynav li').last().addClass('last-link');
 		jQuery('.category-block ul').addClass('first-link');
    });

    markDocumentLinks();
	gaTrackDownloadableFiles();

</script>


	

<?php
	wp_footer();
?>
</body>
</html>
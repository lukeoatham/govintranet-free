<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */

get_header(); ?>


				<div class="row">
					
					<div class='twelvecol white last'>
						<div class="content-wrapper">

							<h1 class="entry-title"><?php _e( 'This is somewhat embarrassing, isn&rsquo;t it?', 'twentyten' ); ?></h1>

							<p><?php _e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching, or one of the links below, can help.', 'twentyten' ); ?></p><br>

							<form role="search" method="get" id="searchformnf" action="<?php echo home_url( '/' ); ?>">
							    <div><label class="screen-reader-text hiddentext" for="snf">Search for:</label>
							        <input type="text" value="<?php echo $q; ?>" name="s" id="snf" accesskey='4' />
							        <input type="submit" id="searchsubmitnf" value="Search" />
							    </div>
							</form>

	<script type="text/javascript">
		// focus on search field after it has loaded
		document.getElementById('s') && document.getElementById('s').focus();
	</script>

					</div>
				</div>

<?php get_footer(); ?>
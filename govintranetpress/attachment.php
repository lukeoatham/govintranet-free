<?php
/**
 * The template for displaying attachments.
 *
 * @package WordPress
 */

get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

		<div class="col-lg-12 white">
			<div class="row">
				<div class='breadcrumbs'>
					<?php if(function_exists('bcn_display') && !is_front_page()) {
						bcn_display();
						}?>
				</div>
			</div>
			<h2><?php the_title(); ?></h2>

		<?php if ( wp_attachment_is_image() ) :
					$attachments = array_values( get_children( array( 'post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID' ) ) );
					foreach ( $attachments as $k => $attachment ) {
						if ( $attachment->ID == $post->ID )
							break;
					}
					$k++;
					// If there is more than 1 image attachment in a gallery
					if ( count( $attachments ) > 1 ) {
						if ( isset( $attachments[ $k ] ) )
							// get the URL of the next image attachment
							$next_attachment_url = get_attachment_link( $attachments[ $k ]->ID );
						else
							// or get the URL of the first image attachment
							$next_attachment_url = get_attachment_link( $attachments[ 0 ]->ID );
					} else {
						// or, if there's only 1 image attachment, get the URL of the image
						$next_attachment_url = wp_get_attachment_url();
					}
	?>
					<p><a href="<?php echo $next_attachment_url; ?>" title="<?php echo esc_attr( get_the_title() ); ?>" ><?php
						$attachment_size = apply_filters( 'twentyten_attachment_size', 900 );
						echo wp_get_attachment_image( $post->ID, array( $attachment_size, 9999 ) ); // filterable image width with, essentially, no limit for image height.
					?></a></p>

<?php 			else : ?>
					<a href="<?php echo wp_get_attachment_url(); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" ><?php echo esc_attr( get_the_title() ); ?></a>
<?php 			endif; ?>
				<?php if ( !empty( $post->post_excerpt ) ) the_excerpt(); ?>
				<?php the_content( __( 'Continue reading &rarr;', 'govintranetpress' ) ); ?>
			</div>

<?php endwhile; ?>

<?php get_footer(); ?>
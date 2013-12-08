<?php
/**
 * The template for displaying Tag Archive pages.
 *
 * @package WordPress
 */

get_header(); 

?>

<div class="row white content-wrapper">
	<div class="col-lg-9 white">
		<h1><?php
			printf( __( 'Tag: %s', 'twentyten' ), '' . single_tag_title( '', false ) . '' );
		?></h1>

<?php
/* Run the loop for the tag archive to output the posts
 * If you want to overload this in a child theme then include a file
 * called loop-tag.php and that will be used instead.
 */
 
 get_template_part( 'loop', 'tag' );

?>
	</div>

</div>

<?php get_footer(); ?>

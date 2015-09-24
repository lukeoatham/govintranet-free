<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form.  The actual display of comments is
 * handled by a callback to govintranet_comment which is
 * located in the functions.php file.
 *
 * @package WordPress
 */

	if ( post_password_required() ) : ?>
		<p><?php _e( 'This post is password protected. Enter the password to view any comments.', 'govintranet' ); ?></p>
		<?php
		/* Stop the rest of comments.php from being processed,
		 * but don't kill the script entirely -- we still have
		 * to fully load the template.
		 */
		return;
	endif;

		// You can start editing here -- including this comment!

	if ( have_comments() ) : ?>
		<div id="comments">
		<h3 id="comments-title"><?php
		printf( _n( 'One response to %2$s', '%1$s responses to %2$s', get_comments_number(), 'govintranet' ),
		number_format_i18n( get_comments_number() ), '' . get_the_title() . '' );
		?></h3>

		<?php 
		if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? 
			previous_comments_link( __( '&larr; Older comments', 'govintranet' ) ); 
			next_comments_link( __( 'Newer comments &rarr;', 'govintranet' ) ); 
		endif; // check for comment navigation ?>

		<ol>
			<?php
				/* Loop through and list the comments. Tell wp_list_comments()
				 * to use govintranet_comment() to format the comments.
				 * If you want to overload this in a child theme then you can
				 * define govintranet_comment() and that will be used instead.
				 * See govintranet_comment() in govintranet/functions.php for more.
				 */
				wp_list_comments( array( 'callback' => 'govintranet_comment' ) );
			?>
		</ol>
		</div>
		<?php 
		if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? 
			previous_comments_link( __( '&larr; Older comments', 'govintranet' ) ); 
			next_comments_link( __( 'Newer comments &rarr;', 'govintranet' ) ); 
		endif; // check for comment navigation 

	else : // or, if we don't have comments:

		/* If there are no comments and comments are closed,
		 * let's leave a little note, shall we?
		 */
		if ( ! comments_open() ) :
			?>
			<p><?php _e( '', 'govintranet' ); ?></p>
			<?php 
		endif; // end ! comments_open() 
		
	endif; // end have_comments()

	if ( is_user_logged_in() ):
		$custom_comment_text = get_option("options_comment_instructions_logged_in", "");	
	else:
		$custom_comment_text = get_option("options_comment_instructions_logged_out", "Your email address will not be published. Name, email address and comment are required fields.");	
	endif;
	$args = array(
		'comment_notes_before' => wpautop($custom_comment_text),
		'comment_notes_after' => '',
		'title_reply' => 'Leave a comment',
	);
	echo "<div class='well'>";
	comment_form($args); 
	echo "</div>";
?>
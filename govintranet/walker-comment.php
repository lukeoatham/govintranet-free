<?php
/**
 * Custom comment walker for this theme.
 *
 */

if ( ! class_exists( 'govintranet_Walker_Comment' ) ) {
	/**
	 * CUSTOM COMMENT WALKER
	 * A custom walker for comments, based on the walker in Twenty Nineteen.
	 */
	class govintranet_Walker_Comment extends Walker_Comment {

		/**
		 * Outputs a comment in the HTML5 format.
		 *
		 * @see wp_list_comments()
		 * @see https://developer.wordpress.org/reference/functions/get_comment_author_url/
		 * @see https://developer.wordpress.org/reference/functions/get_comment_author/
		 * @see https://developer.wordpress.org/reference/functions/get_avatar/
		 * @see https://developer.wordpress.org/reference/functions/get_comment_reply_link/
		 * @see https://developer.wordpress.org/reference/functions/get_edit_comment_link/
		 *
		 * @param WP_Comment $comment Comment to display.
		 * @param int        $depth   Depth of the current comment.
		 * @param array      $args    An array of arguments.
		 */
		protected function html5_comment( $comment, $depth, $args ) {

			$tag = ( 'div' === $args['style'] ) ? 'div' : 'li';

			?>
			<<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static output ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( $this->has_children ? 'parent well' : 'well', $comment ); ?>>
				<article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
					<footer class="comment-meta">
						<div class="comment-author vcard">
							<?php
							$comment_author_url = get_comment_author_url( $comment );
							$comment_author     = get_comment_author( $comment );
							$avatar             = get_avatar( $comment, $args['avatar_size'] );
							$directory 			= get_option('options_forum_support');
							$directorystyle 	= get_option('options_staff_directory_style'); // 0 = sxquares, 1 = circles
							$staffdirectory 	= get_option('options_module_staff_directory');
							$avstyle			= "";

							if ( isset($comment->user_id ) && get_user_by( "ID", $comment->user_id ) ){
								
								$comment_author_url = get_author_posts_url( $comment->user_id );  
								if ( $comment_author_url == site_url("author/") ) {
									$comment_author_url = "";
								}
								if (function_exists('bp_activity_screen_index')){ // if using BuddyPress - link to the members page
									$comment_author_url=str_replace('/author', '/members', $comment_author_url); }
								elseif (function_exists('bbp_get_displayed_user_field') && $staffdirectory ){ // if using bbPress - link to the staff page
									$comment_author_url=str_replace('/author', '/staff', $comment_author_url); }
								elseif (function_exists('bbp_get_displayed_user_field')  ){ // if using bbPress - link to the staff page
									$comment_author_url=str_replace('/author', '/users', $comment_author_url);
								} 
								$userdisplay = "";
								$user_object = get_userdata( $comment->user_id );
								if ( $user_object ) $userdisplay = $user_object->display_name;
								if ( !$comment_author_url ){
									$comment_author_url = get_comment_author_link();
								}
							} 

							if ( $directorystyle == 1 ) {
								$avstyle = "img-circle ";
							}

							$avatar = str_replace("avatar avatar", $avstyle ." avatar alignleft avatar" , $avatar);

							if ( 0 !== $args['avatar_size'] ) {
								echo wp_kses_post( $avatar );
							}

							if ( ! empty( $comment_author_url ) ) {
								printf( '<a href="%s" rel="external nofollow" class="url">', $comment_author_url ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Escaped in https://developer.wordpress.org/reference/functions/get_comment_author_url/
							}
							
							printf(
								'<span class="fn">%1$s</span><span class="screen-reader-text says">%2$s</span>',
								esc_html( $comment_author ),
								__( 'says:', 'govintranet' )
								);

							if ( ! empty( $comment_author_url ) ) {

								echo '</a>';
							}
							
							?>
						</div><!-- .comment-author -->

						<div class="comment-metadata">
								<?php
								/* translators: 1: Comment date, 2: Comment time. */
								$comment_timestamp = sprintf( __( '%1$s at %2$s', 'govintranet' ), get_comment_date( '', $comment ), get_comment_time() );
								?>
								<time datetime="<?php comment_time( 'c' ); ?>" title="<?php echo esc_attr( $comment_timestamp ); ?>">
									<?php echo esc_html( $comment_timestamp ); ?>
								</time>
							<?php
							if ( get_edit_comment_link() ) {
								echo ' <span aria-hidden="true">&bull;</span> <a class="comment-edit-link" href="' . esc_url( get_edit_comment_link() ) . '">' . __( 'Edit', 'govintranet' ) . '</a>';
							}
							?>
						</div><!-- .comment-metadata -->

					</footer><!-- .comment-meta -->

					<div class="comment-content entry-content">

						<?php

						comment_text();

						if ( '0' === $comment->comment_approved ) {
							?>
							<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'govintranet' ); ?></p>
							<?php
						}

						?>

					</div><!-- .comment-content -->

					<?php

				    // If comment author is blank, use 'Anonymous'
				    if ( empty($comment->comment_author) ) {
				        if ( !empty($comment->user_id) ){
				            $user = get_userdata($comment->user_id);
				            $author = $user->user_login;
				        } else {
				            $author = __('Anonymous','govintranet');
				        }
				    } else {
				        $author = $comment->comment_author;
				    }
				 
				    // If the user provided more than a first name, use only first name
				    if ( strpos($author, ' ') ){
				        $author = substr($author, 0, strpos($author, ' '));
				    }
				 
				    // Replace Reply Link with "Reply to &lt;Author First Name>"
					$replyto = sprintf( __('Reply to %s', 'govintranet'), esc_html($author) );

					$comment_reply_link = get_comment_reply_link(
						array_merge(
							$args,
							array(
								'add_below' => 'div-comment',
								'depth'     => $depth,
								'max_depth' => $args['max_depth'],
								'before'    => '<span class="comment-reply">',
								'after'     => '</span>',
								'reply_text'=> $replyto,
								'reply_to_text'=> __('Leave a reply','govintranet'),
							)
						)
					);

					$by_post_author = govintra_is_comment_by_post_author( $comment );

					if ( $comment_reply_link || $by_post_author ) {
						?>

						<footer class="comment-footer-meta">

							<?php
							if ( $comment_reply_link ) {
								echo $comment_reply_link; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Link is escaped in https://developer.wordpress.org/reference/functions/get_comment_reply_link/
							}
							if ( $by_post_author ) {
								echo '<span class="by-post-author pull-right small">' . __( 'By the author', 'govintranet' ) . '</span>';
							}
							?>

						</footer>

						<?php
					}
					?>

				</article><!-- .comment-body -->

			<?php
		}
	}
}

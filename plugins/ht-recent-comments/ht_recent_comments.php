<?php

/**
Plugin Name: HT Recent comments
Plugin URI: https://agentodigital.com
Description: Displays most recent comments
Author: Luke Oatham
Version: 2.0
Author URI: http://intranetdiary.co.uk
 */
 

class WP_Widget_HT_Recent_Comments extends WP_Widget {

	public function __construct() {
		$widget_ops = array('classname' => 'widget_HT_Recent_Comments', 'description' => __( 'Your site&#8217;s most recent comments.' ,'govintranet' ));
		parent::__construct('ht-recent-comments', __('HT Recent Comments','govintranet'), $widget_ops);
		$this->alt_option_name = 'widget_HT_Recent_Comments';

		if ( is_active_widget(false, false, $this->id_base) )
			add_action( 'wp_head', array($this, 'HT_Recent_Comments_style') );

		add_action( 'comment_post', array($this, 'flush_widget_cache') );
		add_action( 'edit_comment', array($this, 'flush_widget_cache') );
		add_action( 'transition_comment_status', array($this, 'flush_widget_cache') );
	}

	/**
	 * @access public
	 */
	public function HT_Recent_Comments_style() {
		/**
		 * Filter the Recent Comments default widget styles.
		 *
		 * @since 3.1.0
		 *
		 * @param bool   $active  Whether the widget is active. Default true.
		 * @param string $id_base The widget ID.
		 */
		if ( ! current_theme_supports( 'widgets' ) // Temp hack #14876
			|| ! apply_filters( 'show_HT_Recent_Comments_widget_style', true, $this->id_base ) )
			return;
		?>
	<style type="text/css">.recentcomments a{display:inline !important;padding:0 !important;margin:0 !important;}</style>
<?php
	}

	/**
	 * @access public
	 */
	public function flush_widget_cache() {
		wp_cache_delete('widget_HT_Recent_Comments', 'widget');
	}

	/**
	 * @global array  $comments
	 * @global object $comment
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		global $comments, $comment;

		$cache = array();
		if ( ! $this->is_preview() ) {
			$cache = wp_cache_get('widget_HT_Recent_Comments', 'widget');
		}
		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		$output = '';

		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Recent Comments' ,'govintranet') ;

		/** This filter is documented in wp-includes/default-widgets.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
		if ( ! $number )
			$number = 5;

		$post_type = ( ! empty( $instance['post_type'] ) ) ? trim( $instance['post_type'] ) : '';
		
		/**
		 * Filter the arguments for the Recent Comments widget.
		 *
		 * @since 3.4.0
		 *
		 * @see WP_Comment_Query::query() for information on accepted arguments.
		 *
		 * @param array $comment_args An array of arguments used to retrieve the recent comments.
		 */
		 
		$comment_query = array(
			'number'      => $number,
			'status'      => 'approve',
			'post_status' => 'publish'
			);
		
		if ( in_array( $post_type, array('post','page','task','news','news-update','blog','event') ) ) $comment_query['post_type'] = $post_type;
		
		$comments = get_comments( apply_filters( 'widget_comments_args', $comment_query ) );

		if ( is_array( $comments ) && count($comments) > 0 ){
			$output .= $args['before_widget'];
			if ( $title ) {
				$output .= $args['before_title'] . $title . $args['after_title'];
			}
	
			$output .= '<ul id="recentcomments">';
			if ( is_array( $comments ) && $comments ) {
				// Prime cache for associated posts. (Prime post term cache if we need it for permalinks.)
				$post_ids = array_unique( wp_list_pluck( $comments, 'comment_post_ID' ) );
				_prime_post_caches( $post_ids, strpos( get_option( 'permalink_structure' ), '%category%' ), false );
	
				foreach ( (array) $comments as $comment) {
					$output .= '<li class="recentcomments">';
					/* translators: comments widget: 1: comment author, 2: post link */
					$userid = $comment->user_id; 
					$user = get_userdata( $userid );
					if ( $user === false ) {
					    //user id does not exist
						$userurl = $comment->comment_author;
						$displayname = $userurl;
					}					
					if ( $userid > 0 && $user ){
						$userurl = get_author_posts_url( $userid ); 
						$displayname = get_user_meta($userid ,'first_name',true )." ".get_user_meta($userid ,'last_name',true );		
						$gis = "options_forum_support";
						$forumsupport = get_option($gis);
						$staffdirectory = get_option('options_module_staff_directory');
						if (function_exists('bp_activity_screen_index')){ // if using BuddyPress - link to the members page
							$userurl=str_replace('/author', '/members', $userurl); }
						elseif (function_exists('bbp_get_displayed_user_field') && $staffdirectory ){ // if using bbPress - link to the staff page
							$userurl=str_replace('/author', '/staff', $userurl); } 
						elseif (function_exists('bbp_get_displayed_user_field') ){ // if using bbPress - link to the staff page
							$userurl=str_replace('/author', '/users', $userurl);
						}
						$userurl = "<a href='".$userurl."'>".$displayname."</a>";
					} else {
						$userurl = $comment->comment_author;
						$displayname = $userurl;
					}
					$output .= sprintf( _x( '%1$s on %2$s', 'widgets' ),
						'<span class="comment-author-link">' . $userurl . '</span>',
						'<a href="' . esc_url( get_comment_link( $comment->comment_ID ) ) . '">' . get_the_title( $comment->comment_post_ID ) . '</a>'
					);
					$output .= '</li>';
				}
			}
			$output .= '</ul>';
			$output .= $args['after_widget'];
			echo $output;
		}

		if ( ! $this->is_preview() ) {
			$cache[ $args['widget_id'] ] = $output;
			wp_cache_set( 'widget_HT_Recent_Comments', $cache, 'widget' );
		}
	}

	/**
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = absint( $new_instance['number'] );
		$instance['post_type'] = strip_tags($new_instance['post_type']);
		$this->flush_widget_cache();
		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['widget_HT_Recent_Comments']) )
			delete_option('widget_HT_Recent_Comments');

		return $instance;
	}

	/**
	 * @param array $instance
	 */
	public function form( $instance ) {
		$title  = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$post_type  = isset( $instance['post_type'] ) ? esc_attr( $instance['post_type'] ) : '';
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ,'govintranet' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of comments to show:' ,'govintranet' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" />
		</p>


		<fieldset>
		<legend><?php _e("Post types:","govintranet");?></legend>
		<label for="<?php echo $this->get_field_id( 'post_type' ); ?>all">
		<input type="radio" value="all" name="<?php echo $this->get_field_name( 'post_type' ); ?>" <?php checked( $post_type, 'all' ); ?> id="<?php echo $this->get_field_id( 'post_type' ); ?>all" /><?php _e( 'All' ,'govintranet' ); ?></label><br>

		<label for="<?php echo $this->get_field_id( 'post_type' ); ?>post">
		<input type="radio" value="post" name="<?php echo $this->get_field_name( 'post_type' ); ?>" <?php checked( $post_type, 'post' ); ?> id="<?php echo $this->get_field_id( 'post_type' ); ?>post" /><?php _e( 'Post' ,'govintranet' ); ?></label><br>

		<label for="<?php echo $this->get_field_id( 'post_type' ); ?>page">
		<input type="radio" value="page" name="<?php echo $this->get_field_name( 'post_type' ); ?>" <?php checked( $post_type, 'page' ); ?> id="<?php echo $this->get_field_id( 'post_type' ); ?>page" /><?php _e( 'Page' ,'govintranet' ); ?></label><br>

		<label for="<?php echo $this->get_field_id( 'post_type' ); ?>task">
		<input type="radio" value="task" name="<?php echo $this->get_field_name( 'post_type' ); ?>" <?php checked( $post_type, 'task' ); ?> id="<?php echo $this->get_field_id( 'post_type' ); ?>task" /><?php _e( 'Task' ,'govintranet' ); ?></label><br>

		<label for="<?php echo $this->get_field_id( 'post_type' ); ?>news">
		<input type="radio" value="news" name="<?php echo $this->get_field_name( 'post_type' ); ?>" <?php checked( $post_type, 'news' ); ?> id="<?php echo $this->get_field_id( 'post_type' ); ?>news" /><?php _e( 'News' ,'govintranet' ); ?></label><br>

		<label for="<?php echo $this->get_field_id( 'post_type' ); ?>newsupdate">
		<input type="radio" value="news-update" name="<?php echo $this->get_field_name( 'post_type' ); ?>" <?php checked( $post_type, 'news-update' ); ?> id="<?php echo $this->get_field_id( 'post_type' ); ?>newsupdate" /><?php _e( 'News update' ,'govintranet' ); ?></label><br>

		<label for="<?php echo $this->get_field_id( 'post_type' ); ?>blog">
		<input type="radio" value="blog" name="<?php echo $this->get_field_name( 'post_type' ); ?>" <?php checked( $post_type, 'blog' ); ?> id="<?php echo $this->get_field_id( 'post_type' ); ?>blog" /><?php _e( 'Blog' ,'govintranet' ); ?></label><br>

		<label for="<?php echo $this->get_field_id( 'post_type' ); ?>event">
		<input type="radio" value="event" name="<?php echo $this->get_field_name( 'post_type' ); ?>" <?php checked( $post_type, 'event' ); ?> id="<?php echo $this->get_field_id( 'post_type' ); ?>event" /><?php _e( 'Event' ,'govintranet' ); ?></label><br>

          </fieldset>


<?php
	}
}

add_action('widgets_init', create_function('', 'return register_widget("WP_Widget_HT_Recent_Comments");'));

?>
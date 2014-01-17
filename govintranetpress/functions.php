<?php
/**
 * TwentyTen functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * The first function, twentyten_setup(), sets up the theme by registering support
 * for various features in WordPress, such as post thumbnails, navigation menus, and the like.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook. The hook can be removed by using remove_action() or
 * remove_filter() and you can attach your own function to the hook.
 *
 * We can remove the parent theme's hook only after it is attached, which means we need to
 * wait until setting up the child theme:
 *
 * <code>
 * add_action( 'after_setup_theme', 'my_child_theme_setup' );
 * function my_child_theme_setup() {
 *     // We are providing our own filter for excerpt_length (or using the unfiltered value)
 *     remove_filter( 'excerpt_length', 'twentyten_excerpt_length' );
 *     ...
 * }
 * </code>
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * Used to set the width of images and content. Should be equal to the width the theme
 * is designed for, generally via the style.css stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 640;


/** Tell WordPress to run twentyten_setup() when the 'after_setup_theme' hook is run. */
add_action( 'after_setup_theme', 'twentyten_setup' );

if ( ! function_exists( 'twentyten_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * To override twentyten_setup() in a child theme, add your own twentyten_setup to your child theme's
 * functions.php file.
 *
 * @uses add_theme_support() To add support for post thumbnails and automatic feed links.
 * @uses register_nav_menus() To add support for navigation menus.
 * @uses add_custom_background() To add support for a custom background.
 * @uses add_editor_style() To style the visual editor.
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_custom_image_header() To add support for a custom header.
 * @uses register_default_headers() To register the default custom header images provided with the theme.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * @since Twenty Ten 1.0
 */
function twentyten_setup() {

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// This theme uses post thumbnails
	add_theme_support( 'post-thumbnails' );

	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	// Make theme available for translation
	// Translations can be filed in the /languages/ directory
	load_theme_textdomain( 'twentyten', TEMPLATEPATH . '/languages' );

	$locale = get_locale();
	$locale_file = TEMPLATEPATH . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'twentyten' ),
	) );

	// This theme allows users to set a custom background
	add_custom_background();

	// Your changeable header business starts here
	define( 'HEADER_TEXTCOLOR', '' );
	// No CSS, just IMG call. The %s is a placeholder for the theme template directory URI.
	define( 'HEADER_IMAGE', '%s/images/headers/path.jpg' );

	// The height and width of your custom header. You can hook into the theme's own filters to change these values.
	// Add a filter to twentyten_header_image_width and twentyten_header_image_height to change these values.
	define( 'HEADER_IMAGE_WIDTH', apply_filters( 'twentyten_header_image_width', 940 ) );
	define( 'HEADER_IMAGE_HEIGHT', apply_filters( 'twentyten_header_image_height', 198 ) );

	// We'll be using post thumbnails for custom header images on posts and pages.
	// We want them to be 940 pixels wide by 198 pixels tall.
	// Larger images will be auto-cropped to fit, smaller ones will be ignored. See header.php.
	set_post_thumbnail_size( HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true );

	// Don't support text inside the header image.
	define( 'NO_HEADER_TEXT', true );

	// Add a way for the custom header to be styled in the admin panel that controls
	// custom headers. See twentyten_admin_header_style(), below.
	add_custom_image_header( '', 'twentyten_admin_header_style' );

	// ... and thus ends the changeable header business.

	// Default custom headers packaged with the theme. %s is a placeholder for the theme template directory URI.
	register_default_headers( array(
		'berries' => array(
			'url' => '%s/images/headers/starkers.png',
			'thumbnail_url' => '%s/images/headers/starkers-thumbnail.png',
			/* translators: header image description */
			'description' => __( 'Starkers', 'twentyten' )
		)
	) );
}
endif;

if ( ! function_exists( 'twentyten_admin_header_style' ) ) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * Referenced via add_custom_image_header() in twentyten_setup().
 *
 * @since Twenty Ten 1.0
 */
function twentyten_admin_header_style() {
?>
<style type="text/css">
/* Shows the same border as on front end */
#headimg {
	border-bottom: 1px solid #000;
	border-top: 4px solid #000;
}
/* If NO_HEADER_TEXT is false, you would style the text with these selectors:
	#headimg #name { }
	#headimg #desc { }
*/
</style>
<?php
}
endif;

/**
 * Makes some changes to the <title> tag, by filtering the output of wp_title().
 *
 * If we have a site description and we're viewing the home page or a blog posts
 * page (when using a static front page), then we will add the site description.
 *
 * If we're viewing a search result, then we're going to recreate the title entirely.
 * We're going to add page numbers to all titles as well, to the middle of a search
 * result title and the end of all other titles.
 *
 * The site title also gets added to all titles.
 *
 * @since Twenty Ten 1.0
 *
 * @param string $title Title generated by wp_title()
 * @param string $separator The separator passed to wp_title(). Twenty Ten uses a
 * 	vertical bar, "|", as a separator in header.php.
 * @return string The new title, ready for the <title> tag.
 */
function twentyten_filter_wp_title( $title, $separator ) {
	// Don't affect wp_title() calls in feeds.
	if ( is_feed() )
		return $title;

	// The $paged global variable contains the page number of a listing of posts.
	// The $page global variable contains the page number of a single post that is paged.
	// We'll display whichever one applies, if we're not looking at the first page.
	global $paged, $page;

	if ( is_search() ) {
		// If we're a search, let's start over:
		$title = sprintf( __( 'Search results for %s', 'twentyten' ), '"' . get_search_query() . '"' );
		// Add a page number if we're on page 2 or more:
		if ( $paged >= 2 )
			$title .= " $separator " . sprintf( __( 'Page %s', 'twentyten' ), $paged );
		// Add the site name to the end:
		//$title .= " $separator " . get_bloginfo( 'name', 'display' );
		// We're done. Let's send the new title back to wp_title():
		return $title;
	}

	// Otherwise, let's start by adding the site name to the end:
	
	if ( is_front_page() ){
		$title .= get_bloginfo( 'name', 'display' );
	}
	
	$slug = pods_url_variable(0);
	$slug2 = pods_url_variable(1);
	if ($slug == "task"  ) {

		$taskpod = new Pod ('task' , pods_url_variable(1)); 
		$taskparent=$taskpod->get_field('parent_guide');
		$title_context='';
		if ($taskparent){
			$parent_guide_id = $taskparent[0]['ID']; 		
			$taskparent = get_post($parent_guide_id);
			$title_context=" (".govintranetpress_custom_title($taskparent->post_title).")";
		}			

	
	
		$title .= $title_context. " - tasks and guides" ;
	}
	else if ($slug2 == "projects"  ) {
		$title .= " - projects" ;
	}
	else if ($slug2 == "vacancies"  ) {
		$title .= " - job vacancies" ;
	}
	else if ($slug == "staff"  ) {
		global $post;
		$u = $post->post_title;
		$title .= $u." - staff profile" ;
	}
	else if ($slug == "events"  ) {
		$title .= " - events" ;
	}
	else if ($slug == "glossary"  ) {
		$title .= " - jargon buster" ;
	}
	else if ($slug == "atoz"  ) {
		$title .= " - A to Z" ;
	}
	else if ($slug == "forums"  ) {
		$title .= " - forums" ;
	}
	else if ($slug == "topics"  ) {
		$title .= " - forum topics" ;
	}
	else if ($slug == "replies"  ) {
		$title .= " - forum replies" ;
	}
	else if ($slug == "news"  ) {
		$title .= " - news" ;
	}
	else if ($slug == "blog"  ) {
		$title .= " - blog" ;
	}



	// If we have a site description and we're on the home/front page, add the description:
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title .= " $separator " . $site_description;

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		$title .= " $separator " . sprintf( __( 'Page %s', 'twentyten' ), max( $paged, $page ) );

	// Return the new title to wp_title():
	return trim(preg_replace('/\[.*\]/i','',$title));

}
add_filter( 'wp_title', 'twentyten_filter_wp_title', 10, 2 );

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 *
 * To override this in a child theme, remove the filter and optionally add
 * your own function tied to the wp_page_menu_args filter hook.
 *
 * @since Twenty Ten 1.0
 */
function twentyten_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'twentyten_page_menu_args' );

/**
 * Sets the post excerpt length to 40 characters.
 *
 * To override this length in a child theme, remove the filter and add your own
 * function tied to the excerpt_length filter hook.
 *
 * @since Twenty Ten 1.0
 * @return int
 */
function twentyten_excerpt_length( $length ) {
	return 30;
}
add_filter( 'excerpt_length', 'twentyten_excerpt_length' );

/**
 * Returns a "Continue Reading" link for excerpts
 *
 * @since Twenty Ten 1.0
 * @return string "Continue Reading" link
 */
function twentyten_continue_reading_link() {
	return ' <a href="'. get_permalink() . '">' . __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentyten' ) . '</a>';
}

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and twentyten_continue_reading_link().
 *
 * To override this in a child theme, remove the filter and add your own
 * function tied to the excerpt_more filter hook.
 *
 * @since Twenty Ten 1.0
 * @return string An ellipsis
 */
function twentyten_auto_excerpt_more( $more ) {
	return ' &hellip;' . twentyten_continue_reading_link();
}
add_filter( 'excerpt_more', 'twentyten_auto_excerpt_more' );

/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 *
 * To override this link in a child theme, remove the filter and add your own
 * function tied to the get_the_excerpt filter hook.
 *
 * @since Twenty Ten 1.0
 * @return string Excerpt with a pretty "Continue Reading" link
 */
function twentyten_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= twentyten_continue_reading_link();
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'twentyten_custom_excerpt_more' );

/**
 * Remove inline styles printed when the gallery shortcode is used.
 *
 * Galleries are styled by the theme in Twenty Ten's style.css.
 *
 * @since Twenty Ten 1.0
 * @return string The gallery style filter, with the styles themselves removed.
 */
function twentyten_remove_gallery_css( $css ) {
	return preg_replace( "#<style type='text/css'>(.*?)</style>#s", '', $css );
}
add_filter( 'gallery_style', 'twentyten_remove_gallery_css' );

if ( ! function_exists( 'twentyten_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own twentyten_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Twenty Ten 1.0
 */
function twentyten_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case '' :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>">
		<div class="comment-author vcard">
			<?php echo get_avatar( $comment, 40 ); ?>
			<?php printf( __( '%s <span class="says">says:</span>', 'twentyten' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
		</div><!-- .comment-author .vcard -->
		<?php if ( $comment->comment_approved == '0' ) : ?>
			<em><?php _e( 'Your comment is awaiting moderation.', 'twentyten' ); ?></em>
			<br />
		<?php endif; ?>

		<div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
			<?php
				/* translators: 1: date, 2: time */
				printf( __( '%1$s at %2$s', 'twentyten' ), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)', 'twentyten' ), ' ' );
			?>
		</div><!-- .comment-meta .commentmetadata -->

		<div class="comment-body"><?php comment_text(); ?></div>

		<div class="reply">
			<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
		</div><!-- .reply -->
	</div><!-- #comment-##  -->

	<?php
			break;
		case 'pingback'  :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'twentyten' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __('(Edit)', 'twentyten'), ' ' ); ?></p>
	<?php
			break;
	endswitch;
}
endif;

/**
 * Register widgetized areas, including two sidebars and four widget-ready columns in the footer.
 *
 * To override twentyten_widgets_init() in a child theme, remove the action hook and add your own
 * function tied to the init hook.
 *
 * @since Twenty Ten 1.0
 * @uses register_sidebar
 */
function twentyten_widgets_init() {

	register_sidebar( array(
		'name' => __( 'Homepage first column', 'twentyten' ),
		'id' => 'home-widget-area0',
		'description' => __( 'Homepage 1st column', 'twentyten' ),
		'before_widget' => '<div class="category-block">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="noborder">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Homepage widget box 1', 'twentyten' ),
		'id' => 'home-widget-area1',
		'description' => __( 'Homepage top left', 'twentyten' ),
		'before_widget' => '<div class="category-block">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	) );
	register_sidebar( array(
		'name' => __( 'Homepage widget box2', 'twentyten' ),
		'id' => 'home-widget-area2',
		'description' => __( 'Homepage bottom left', 'twentyten' ),
		'before_widget' => '<div class="category-block">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	) );
	register_sidebar( array(
		'name' => __( 'Homepage widget box 3', 'twentyten' ),
		'id' => 'home-widget-area3',
		'description' => __( 'Homepage top right', 'twentyten' ),
		'before_widget' => '<div class="category-block">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	) );
	register_sidebar( array(
		'name' => __( 'Homepage widget box 4', 'twentyten' ),
		'id' => 'home-widget-area4',
		'description' => __( 'Homepage bottom right', 'twentyten' ),
		'before_widget' => '<div class="category-block">',
		'after_widget' => '</div>',
		'before_title' => '<h3><i class="foundicon-twitter"></i>',
		'after_title' => '</h3>',
	) );	
	register_sidebar( array(
		'name' => __( 'Utility widget box', 'twentyten' ),
		'id' => 'utility-widget-area',
		'description' => __( 'The utility widget area', 'twentyten' ),
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	) );
	
	register_sidebar( array(
		'name' => __( 'Left footer', 'twentyten' ),
		'id' => 'first-footer-widget-area',
		'description' => __( 'The main footer widget area', 'twentyten' ),
		'before_widget' => '<div class="widget-box">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Right footer 1', 'twentyten' ),
		'id' => 'right1-footer-widget-area',
		'description' => __( 'The 1st right footer widget area', 'twentyten' ),
		'before_widget' => '<div class="widget-box">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Right footer 2', 'twentyten' ),
		'id' => 'right2-footer-widget-area',
		'description' => __( 'The 2nd right footer widget area', 'twentyten' ),
		'before_widget' => '<div class="widget-box">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	) );
	register_sidebar( array(
		'name' => __( 'Tasks sidebar', 'twentyten' ),
		'id' => 'task-widget-area',
		'description' => __( 'Tasks widget area', 'twentyten' ),
		'before_widget' => '<div class="widget-box">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	) );
	register_sidebar( array(
		'name' => __( 'News landing page', 'twentyten' ),
		'id' => 'newslanding-widget-area',
		'description' => __( 'The right-hand col on the news page', 'twentyten' ),
		'before_widget' => '<div class="widget-box">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );	
	register_sidebar( array(
		'name' => __( 'News sidebar', 'twentyten' ),
		'id' => 'news-widget-area',
		'description' => __( 'News widget area', 'twentyten' ),
		'before_widget' => '<div class="widget-box">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	) );
	register_sidebar( array(
		'name' => __( 'Blog landing page', 'twentyten' ),
		'id' => 'bloglanding-widget-area',
		'description' => __( 'Blog landing page widget area', 'twentyten' ),
		'before_widget' => '<div class="widget-box">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	) );
	register_sidebar( array(
		'name' => __( 'Blog sidebar', 'twentyten' ),
		'id' => 'blog-widget-area',
		'description' => __( 'Blog posts widget area', 'twentyten' ),
		'before_widget' => '<div class="widget-box">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	) );
	register_sidebar( array(
		'name' => __( 'Search results page', 'twentyten' ),
		'id' => 'serp-widget-area',
		'description' => __( 'Search results page widget area', 'twentyten' ),
		'before_widget' => '<div class="widget-box">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	) );
	register_sidebar( array(
		'name' => __( 'Login area', 'twentyten' ),
		'id' => 'login-widget-area',
		'description' => __( 'Login widget area', 'twentyten' ),
		'before_widget' => '',
		'after_widget' => '',
		'before_title' => '',
		'after_title' => '',
	) );
	
}

function govintranetpress_custom_title( $output ) {
	if (!is_admin()) {
		return trim(preg_replace('/\[.*\]/i','',$output));
	} else {
		return $output;
	}
}
add_filter( 'the_title', 'govintranetpress_custom_title' );


/** Register sidebars by running twentyten_widgets_init() on the widgets_init hook. */
add_action( 'widgets_init', 'twentyten_widgets_init' );

/**
 * Removes the default styles that are packaged with the Recent Comments widget.
 *
 * To override this in a child theme, remove the filter and optionally add your own
 * function tied to the widgets_init action hook.
 *
 * @since Twenty Ten 1.0
 */
function twentyten_remove_recent_comments_style() {
	global $wp_widget_factory;
	remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
}
add_action( 'widgets_init', 'twentyten_remove_recent_comments_style' );

if ( ! function_exists( 'twentyten_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post—date/time and author.
 *
 * @since Twenty Ten 1.0
 */
function twentyten_posted_on() {
	printf( __( '<span class="%1$s">Published:</span> %2$s', 'twentyten' ),
		'meta-prep meta-prep-author',
		sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><span class="entry-date">%3$s</span></a>',
			get_permalink(),
			esc_attr( get_the_time() ),
			get_the_date()
		),
		sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
			get_author_posts_url( get_the_author_meta( 'ID' ) ),
			sprintf( esc_attr__( 'View all posts by %s', 'twentyten' ), get_the_author() ),
			get_the_author()
		)
	);
}
endif;

if ( ! function_exists( 'twentyten_posted_in' ) ) :
/**
 * Prints HTML with meta information for the current post (category, tags and permalink).
 *
 * @since Twenty Ten 1.0
 */
function twentyten_posted_in() {
	// Retrieves tag list of current post, separated by commas.
	$tag_list = get_the_tag_list( '', ', ' );
	if ( $tag_list ) {
		$posted_in = __( 'In: %1$s. Tags: %2$s', 'twentyten' );
	} elseif ( is_object_in_taxonomy( get_post_type(), 'category' ) ) {
		$posted_in = __( 'In: %1$s.', 'twentyten' );
	} else {
		$posted_in = __( '', 'twentyten' );
	}
	// Prints the string, replacing the placeholders.
	printf(
		$posted_in,
		get_the_category_list( ', ' ),
		$tag_list,
		get_permalink(),
		the_title_attribute( 'echo=0' )
	);
}
endif;

// theme options functions:

require_once ( get_stylesheet_directory() . '/theme-options.php'  );

function remove_themeoptions_menu() { // needed to hide TwentyTen options that we're overriding 
	global $submenu;
	if ( is_super_admin() ) {
	foreach($submenu['themes.php'] as $k => $m) {
		if ($m[2] == "custom-background" || $m[2] == "custom-header") {
			unset($submenu['themes.php'][$k]);
		}
	}
	}	
}

/*
add_action('admin_head', 'remove_themeoptions_menu');


function remove_contactmethods($contactmthods) {
	unset($contactmethods['aim']);
	unset($contactmethods['jabber']);
	unset($contactmethods['yim']);
	return $contactmethods;
	}

add_filter('user_contactmethods','remove_contactmethods' ,10,1);
*/


// check jQuery is available

function enqueueThemeScripts() {
	 wp_enqueue_script( 'jquery' );
	 wp_enqueue_script( 'jquery-ui' );
	 
	 wp_register_script( 'bootstrap.min', get_stylesheet_directory_uri() . "/js/bootstrap.min.js");
	 wp_enqueue_script( 'bootstrap.min' );

	 wp_register_script( 'ht-scripts', get_stylesheet_directory_uri() . "/js/ht-scripts.js");
	 wp_enqueue_script( 'ht-scripts' );

	 wp_register_script( 'jquery.ht-timediff', get_stylesheet_directory_uri() . "/js/jquery.ht-timediff.js");
	 wp_enqueue_script( 'jquery.ht-timediff',90 );

}
add_action('wp_enqueue_scripts','enqueueThemeScripts');


function govintranetpress_custom_excerpt_more( $output ) {
	return preg_replace('/<a[^>]+>Continue reading.*?<\/a>/i','',$output);
}
add_filter( 'get_the_excerpt', 'govintranetpress_custom_excerpt_more', 20 );


function get_post_thumbnail_caption() {
	if ( $thumb = get_post_thumbnail_id() )
		return get_post( $thumb )->post_excerpt;
}


// shorten cache lifetime for blog aggregators to keep it fresh
add_filter( 'wp_feed_cache_transient_lifetime', create_function( '$a', 'return 900;' ) ); // 15 mins

function renderLeftNav($outputcontent="TRUE") {
		global $post;
		$temppost = $post;
		$parent = $post->post_parent;
		$mainid = $post->ID;
		$navarray = array();
		$currentpost = get_post($mainid);
		$currenttitle = get_the_title();
					
		while (true){
			//iteratively get the post's parent until we reach the top of the hierarchy
			$post_parent = $currentpost->post_parent; 
			if ($post_parent!=0){	//if found a parent
				$navarray[] = $post_parent;
				$currentpost = get_post($post_parent);
				continue; //loop round again
			}
			break; //we're done with the parents
		};
		
		$navarray = array_reverse($navarray);
		
		foreach ($navarray as $nav){ //loop through nav array outputting menu options as appropriate (parent, current or child)
			$currentpost = get_post($nav);
			$subnavString .= "<li class='page_item menu-item-ancestor'>"; //parent page
			$subnavString .=  "<a href='".$currentpost->guid."'>".$currentpost->post_title."</a></li>";
		}
										


		if (!is_search() ) {
		
			$output = "
				<div id='sectionnav'>
				<ul>
				{$subnavString}";
		if (pageHasChildren($mainid)){
		$subpages = wp_list_pages("echo=0&title_li=&depth=3&child_of=". $mainid);
			$output .="	
				<li class='current_page_item'><a href='#'>{$currenttitle}</a></li>
				<ul class='submenu'>{$subpages}</ul>";
		} else {
		$subpages = wp_list_pages("echo=0&title_li=&depth=3&child_of=". $parent);
			$output .="	
				<ul class='submenu'>{$subpages}</ul>";
		}
			$output .="	
				</ul>
				</div>	
			";
	
			if ($outputcontent == "TRUE") { 
				echo $output; 
			} else {
				return true;
			}
			
		} else {
			$output = "
				<div id='spacernav'>
				</div>	
			";
	
			if ($outputcontent == "TRUE") { 
				echo $output; 
			} else {
				return false;
			}
			
		}			

}

function pageHasChildren($id="") {

	global $post;
	
	if ($id) {
		$children = get_pages('child_of='.$id);	
	} else {
		$children = get_pages('child_of='.$post->ID);
	}

	if (count($children) != 0) {
		return true;
	} else {
		return false;
	}
}

function my_custom_login_logo() {
	$hc = "general_intranet_login_logo";
	$hcitem = get_option($hc);
	$loginimage =  wp_get_attachment_image_src( $hcitem[0], 'large' );
	if ($hcitem){
    echo '<style type="text/css">
           h1 a { background-image:url('.$loginimage[0].') !important; 
           width: auto !important;
           background-size: auto !important;
           }
        
    </style>';
    } else {
    echo '<style type="text/css">
	h1 a { background-image:url('.get_stylesheet_directory_uri().'/images/loginbranding.png) !important; 
	       width: auto !important;
           background-size: auto !important;
           }
    </style>';
    }
}
add_action('login_head', 'my_custom_login_logo');



// add accesskeys to menus

class HT_Walker extends Walker_Nav_Menu {
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		global $wp_query;
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $value . $class_names .'>';

		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! strlen( $item->xfn ) == 0        ? ' accesskey="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}

// add custom styles from editor-style.css to TinyMCE menu

function add_my_editor_style() {
	add_editor_style();
}

add_action( 'admin_init', 'add_my_editor_style' );


// Customize the format dropdown items
if( !function_exists('base_custom_mce_format') ){
	function base_custom_mce_format($init) {
		// Add block format elements you want to show in dropdown
		$init['theme_advanced_blockformats'] = 'p,h2,h3,h4,h5,h6,pre,blockquote';
		//$init['extended_valid_elements'] = 'code[*]';
		return $init;
	}
	add_filter('tiny_mce_before_init', 'base_custom_mce_format' );
}


// extra warning bar (managed as a widget area) about cookies -- but could also use for other alerting

function ht_cookiebar_widget() {

	register_sidebar( array(
		'name' => __( 'Cookie warning bar', 'govintranetpress' ),
		'id' => 'cookiebar',
		'description' => __( 'The cookie warning bar', 'govintranetpress' ),
		'before_widget' => '',
		'after_widget' => '',
		'before_title' => '',
		'after_title' => '',
	) );

}

function filter_search($query) {
    if ($query->is_search) {
		if ( $_GET['pt'] == 'forums'  ){
		        $query->set('post_type', array('topic', 'reply', 'forum'));
		}
    };
/*
    if ($query->is_tag) {
		        $query->set('post_type', array('any'));
    }
    if ($query->is_category) {
		        $query->set('post_type', array('any'));
    }
*/
    
    return $query;
}; 
add_filter('pre_get_posts', 'filter_search');

function my_colorful_tag_cloud( $cat_id, $tc_tax, $tc_post_type ) {
    $defaults = array(
        'smallest' => 12, 'largest' => 24, 'unit' => 'pt', 'number' => 45,
        'format' => 'flat', 'separator' => "\n", 'orderby' => 'name', 'order' => 'ASC',
        'exclude' => '', 'include' => '', 'link' => 'view', 'taxonomy' => 'post_tag', 'echo' => true
    );
if ((pods_url_variable(0)=='tasks')||(pods_url_variable(0)=='how-do-i')){
    $defaults = array(
        'smallest' => 12, 'largest' => 28, 'unit' => 'pt', 'number' => 120,
        'format' => 'flat', 'separator' => "\n", 'orderby' => 'name', 'order' => 'ASC',
        'exclude' => '', 'include' => '', 'link' => 'view', 'taxonomy' => 'post_tag', 'echo' => true
    );
	
}
    $args = wp_parse_args( $args, $defaults );
    global $wpdb;
    if ( $cat_id != "" ){
    $tquery = $wpdb->prepare("SELECT DISTINCT terms2.term_id as term_id, terms2.name as name, terms2.slug as link, t2.count as count, t2.term_taxonomy_id as term_taxonomy_id, 0 as term_group, 'post_tag' as taxonomy FROM $wpdb->posts as p1 LEFT JOIN $wpdb->term_relationships as r1 ON p1.ID = r1.object_ID LEFT JOIN $wpdb->term_taxonomy as t1 ON r1.term_taxonomy_id = t1.term_taxonomy_id LEFT JOIN $wpdb->terms as terms1 ON t1.term_id = terms1.term_id, $wpdb->posts as p2 LEFT JOIN $wpdb->term_relationships as r2 ON p2.ID = r2.object_ID LEFT JOIN $wpdb->term_taxonomy as t2 ON r2.term_taxonomy_id = t2.term_taxonomy_id LEFT JOIN $wpdb->terms as terms2 ON t2.term_id = terms2.term_id WHERE ( t1.taxonomy = '%s' AND p1.post_status = 'publish' AND p1.post_type = '%s' AND terms1.term_id = '%s' AND t2.taxonomy = 'post_tag' AND p2.post_status = 'publish' AND p1.ID = p2.ID  ) ORDER BY t2.count desc limit 90",$tc_tax,$tc_post_type,$cat_id);

} else {

   $tquery = $wpdb->prepare("SELECT DISTINCT terms2.term_id as term_id, terms2.name as name, terms2.slug as link, t2.count as count, t2.term_taxonomy_id as term_taxonomy_id, 0 as term_group, 'post_tag' as taxonomy FROM $wpdb->posts as p1 LEFT JOIN $wpdb->term_relationships as r1 ON p1.ID = r1.object_ID LEFT JOIN $wpdb->term_taxonomy as t1 ON r1.term_taxonomy_id = t1.term_taxonomy_id LEFT JOIN $wpdb->terms as terms1 ON t1.term_id = terms1.term_id, $wpdb->posts as p2 LEFT JOIN $wpdb->term_relationships as r2 ON p2.ID = r2.object_ID LEFT JOIN $wpdb->term_taxonomy as t2 ON r2.term_taxonomy_id = t2.term_taxonomy_id LEFT JOIN $wpdb->terms as terms2 ON t2.term_id = terms2.term_id WHERE ( t1.taxonomy = '%s' AND p1.post_status = 'publish' AND p1.post_type = '%s' AND t2.taxonomy = 'post_tag' AND p2.post_status = 'publish' AND p1.ID = p2.ID  ) ORDER BY t2.count desc limit 90",$tc_tax,$tc_post_type);

}

					
					if ($tc_post_type=='projects'){
						$tquery="
						SELECT DISTINCT
						wp_terms.term_id,
						wp_terms.name,
						wp_terms.slug,
						wp_term_taxonomy.count,
						wp_term_taxonomy.term_taxonomy_id,
						0 as term_group,
						'post_tag' as taxonomy
FROM				wp_posts, wp_term_taxonomy, wp_term_relationships, wp_terms
WHERE				wp_posts.post_type = 'projects' AND
					wp_posts.post_status = 'publish' AND
					wp_posts.id = wp_term_relationships.object_id AND
					wp_term_relationships.term_taxonomy_id = wp_term_taxonomy.term_taxonomy_id AND
					wp_term_taxonomy.taxonomy = 'post_tag' AND
					wp_terms.term_id = wp_term_taxonomy.term_id AND
					wp_term_taxonomy.count > 0
					limit 45
					
						";
					}

					if ($tc_post_type=='vacancies'){
						$tquery="
						SELECT DISTINCT
						wp_terms.term_id,
						wp_terms.name,
						wp_terms.slug,
						wp_term_taxonomy.count,
						wp_term_taxonomy.term_taxonomy_id,
						0 as term_group,
						'post_tag' as taxonomy
FROM				wp_posts, wp_term_taxonomy, wp_term_relationships, wp_terms
WHERE				wp_posts.post_type = 'vacancies' AND
					wp_posts.post_status = 'publish' AND
					wp_posts.id = wp_term_relationships.object_id AND
					wp_term_relationships.term_taxonomy_id = wp_term_taxonomy.term_taxonomy_id AND
					wp_term_taxonomy.taxonomy = 'post_tag' AND
					wp_terms.term_id = wp_term_taxonomy.term_id AND
					wp_term_taxonomy.count > 0
					limit 45
					
						";
					}


		$tags = $wpdb->get_results($tquery);			

//    $tags = get_terms( $args['taxonomy'], array_merge( $args, array( 'orderby' => 'count', 'order' => 'DESC' ) ) ); // Always query top tags
//print_r($tags);
    if ( empty( $tags ) || is_wp_error( $tags ) )
        return;

    foreach ( $tags as $key => $tag ) {
        $link = get_term_link( intval($tag->term_id), $tag->taxonomy );

        if ( is_wp_error( $link ) )
            return false;

        $tags[ $key ]->link = $link;
        $tags[ $key ]->id = $tag->term_id;
    }
    $defaults = array(
        'smallest' => 12, 'largest' => 24, 'unit' => 'pt', 'number' => 0,
        'format' => 'flat', 'separator' => "\n", 'orderby' => 'name', 'order' => 'ASC',
        'topic_count_text_callback' => 'default_topic_count_text',
        'topic_count_scale_callback' => 'default_topic_count_scale', 'filter' => 1,
    );

    if ( !isset( $args['topic_count_text_callback'] ) && isset( $args['single_text'] ) && isset( $args['multiple_text'] ) ) {
        $body = 'return sprintf (
            _n(' . var_export($args['single_text'], true) . ', ' . var_export($args['multiple_text'], true) . ', $count),
            number_format_i18n( $count ));';
        $args['topic_count_text_callback'] = create_function('$count', $body);
    }

    $args = wp_parse_args( $args, $defaults );
    extract( $args );

    if ( empty( $tags ) )
        return;

    $tags_sorted = apply_filters( 'tag_cloud_sort', $tags, $args );
    if ( $tags_sorted != $tags  ) { // the tags have been sorted by a plugin
        $tags = $tags_sorted;
        unset($tags_sorted);
    } else {
        if ( 'RAND' == $order ) {
            shuffle($tags);
        } else {
            // SQL cannot save you; this is a second (potentially different) sort on a subset of data.
            if ( 'name' == $orderby )
                uasort( $tags, '_wp_object_name_sort_cb' );
            else
                uasort( $tags, '_wp_object_count_sort_cb' );

            if ( 'DESC' == $order )
                $tags = array_reverse( $tags, true );
        }
    }

    if ( $number > 0 )
        $tags = array_slice($tags, 0, $number);

    $counts = array();
    $real_counts = array(); // For the alt tag
    foreach ( (array) $tags as $key => $tag ) {
        $real_counts[ $key ] = $tag->count;
        $counts[ $key ] = $topic_count_scale_callback($tag->count);
    }

    $min_count = min( $counts );
    $spread = max( $counts ) - $min_count;
    if ( $spread <= 0 )
        $spread = 1;
    $font_spread = $largest - $smallest;
    if ( $font_spread < 0 )
        $font_spread = 1;
    $font_step = $font_spread / $spread;

    $a = array();
    $colors = 6;

    foreach ( $tags as $key => $tag ) {
        $count = $counts[ $key ];
        $real_count = $real_counts[ $key ];
        $pstyp='';
        if (pods_url_variable(1) == 'projects'){
        $pstyp='?posttype=projects';
        }
        if (pods_url_variable(1) == 'vacancies'){
        $pstyp='?posttype=vacancies';
        }
        if (pods_url_variable(0) == 'task-by-category'){
        $pstyp='?posttype=task';
        }
        if (pods_url_variable(0) == 'news-by-category'){
        $pstyp='?posttype=news';
        }
        if (pods_url_variable(0) == 'tasks'){
        $pstyp='?posttype=task';
        }
        if (pods_url_variable(0) == 'how-do-i'){
        $pstyp='?posttype=task';
        }
        if (pods_url_variable(0) == 'newspage'){
        $pstyp='?posttype=news';
        }
        $tag_link = '#' != $tag->link ? esc_url( $tag->link ).$pstyp : '#';
        $tag_id = isset($tags[ $key ]->id) ? $tags[ $key ]->id : $key;
        $tag_name = $tags[ $key ]->name;
        $min_color = "#5679b9";
        $max_color ="#af1410";
        
        $color = round( ( $smallest + ( ( $count - $min_count ) * $font_step ) ) - ( $smallest - 1 ) ) ;
		$basecol=HTMLToRGB('#3a6f9e');
        
        $scolor = ChangeLuminosity($basecol, 60-($color*3.3));
        $scolor=RGBToHTML($scolor);
        $class = 'color-' . ( round( ( $smallest + ( ( $count - $min_count ) * $font_step ) ) - ( $smallest - 1 ) ) );
        $tag_link = explode("/", $tag_link);
        $tag_link=$tag_link[4];
        $a[] = "<a href='".site_url()."/tagged/?tag=".$tag_link."'  style='font-size: " .
            str_replace( ',', '.', ( $smallest + ( ( $count - $min_count ) * $font_step ) ) )
            . "$unit; color: ".$scolor.";'>$tag_name</a>";
    }

    $return = join( $separator, $a );

    return apply_filters( 'wp_generate_tag_cloud', $return, $tags, $args );
}


function add_pagination_to_author_page_query_string($query_string){
    if (isset($query_string['author_name'])) $query_string['post_type'] = array('topic','reply');
    return $query_string;
}
add_filter('request', 'add_pagination_to_author_page_query_string');


function get_terms_by_post_type( $taxonomies, $post_types ) {
	if (!$taxonomies || !$post_types) {
		$results = "";
		return $results;
	}

    global $wpdb;

    $query = "SELECT t.*, COUNT(*) from $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id INNER JOIN $wpdb->term_relationships AS r ON r.term_taxonomy_id = tt.term_taxonomy_id INNER JOIN $wpdb->posts AS p ON p.ID = r.object_id WHERE p.post_status = 'publish' AND p.post_type IN('".join( "', '", $post_types )."') AND tt.taxonomy IN('".join( "', '", $taxonomies )."') GROUP BY t.term_id order by t.name";

    $results = $wpdb->get_results( $query );

    return $results;

}

function get_terms_by_media_type( $taxonomies, $post_types ) {
	if (!$taxonomies || !$post_types) {
		$results = "";
		return $results;
	}

    global $wpdb;

    $query = "SELECT t.*, COUNT(*) from $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id INNER JOIN $wpdb->term_relationships AS r ON r.term_taxonomy_id = tt.term_taxonomy_id INNER JOIN $wpdb->posts AS p ON p.ID = r.object_id WHERE p.post_status = 'inherit' AND p.post_type IN('".join( "', '", $post_types )."') AND tt.taxonomy IN('".join( "', '", $taxonomies )."') GROUP BY t.term_id order by t.name";

    $results = $wpdb->get_results( $query );

    return $results;

}

if (!current_user_can('level_1')){
	add_action( 'admin_menu', 'my_remove_menu_pages' );
	function my_remove_menu_pages() {
	    remove_menu_page('edit.php?post_type=incsub_wiki');  
	    remove_menu_page('video-user-manuals/plugin.php');  
	    remove_menu_page('edit.php?post_type=task');  
	    remove_menu_page('edit.php?post_type=projects');  
	    remove_menu_page('edit.php?post_type=news');  
	    remove_menu_page('edit.php?post_type=blog');  
	    remove_menu_page('edit.php?post_type=vacancies');  
	    remove_menu_page('index.php');  
	}
}

//remove settings for search to allow for things like T&S
/*
remove_filter('relevanssi_remove_punctuation', 'relevanssi_remove_punct');
add_filter('relevanssi_remove_punctuation', 'your_relevanssi_remove_punct');

function your_relevanssi_remove_punct($a) {
	$a = strip_tags($a);
	$a = stripslashes($a);
	$a = str_replace('&#8217;', '', $a);
	$a = str_replace("'", '', $a);
	$a = str_replace("´", '', $a);
	$a = str_replace("’", '', $a);
	$a = str_replace("‘", '', $a);
	$a = str_replace("„", '', $a);
	$a = str_replace("·", '', $a);
	$a = str_replace("”", '', $a);
	$a = str_replace("“", '', $a);
	$a = str_replace("…", '', $a);
	$a = str_replace("€", '', $a);
	$a = str_replace("&shy;", '', $a);
	$a = str_replace("—", ' ', $a);
	$a = str_replace("–", ' ', $a);
	$a = str_replace("×", ' ', $a);
    $a = preg_replace('/[[:space:]]+/', ' ', $a);	
	$a = trim($a);
        return $a;
}
*/

	$gis = "general_intranet_enable_search_stemmer";
	$stemmer = get_option($gis);
	if ($stemmer) {
		add_filter('relevanssi_stemmer', 'relevanssi_simple_english_stemmer');
	}

add_filter('relevanssi_remove_punctuation', 'saveampersands_1', 9);
function saveampersands_1($a) {
    $a = str_replace('&', 'AMPERSAND', $a);
    return $a;
}
 
add_filter('relevanssi_remove_punctuation', 'saveampersands_2', 11);
function saveampersands_2($a) {
    $a = str_replace('AMPERSAND', '&', $a);
    return $a;
}

/*
add_filter('relevanssi_user_profile_to_post', 'users_to_posts');
function users_to_posts(){}			
*/

// Added to extend allowed file types in Media upload 
add_filter('upload_mimes', 'custom_upload_mimes'); 
function custom_upload_mimes ( $existing_mimes=array() ) { 
	// Add *.RDP files to Media upload 
	$existing_mimes['rdp'] = 'application/rdp'; 
	return $existing_mimes; 
}

//remove title functionality in bbPress which interferes with our custom page titles
remove_action('wp_title', 'bbp_title');

 function HTMLToRGB($htmlCode)
  {
    if($htmlCode[0] == '#')
      $htmlCode = substr($htmlCode, 1);

    if (strlen($htmlCode) == 3)
    {
      $htmlCode = $htmlCode[0] . $htmlCode[0] . $htmlCode[1] . $htmlCode[1] . $htmlCode[2] . $htmlCode[2];
    }
    
    $r = hexdec($htmlCode[0] . $htmlCode[1]);
    $g = hexdec($htmlCode[2] . $htmlCode[3]);
    $b = hexdec($htmlCode[4] . $htmlCode[5]);

    return $b + ($g << 0x8) + ($r << 0x10);
  }

  function RGBToHTML($RGB)
  {
    $r = 0xFF & ($RGB >> 0x10);
    $g = 0xFF & ($RGB >> 0x8);
    $b = 0xFF & $RGB;

    $r = dechex($r);
    $g = dechex($g);
    $b = dechex($b);
    
    return "#" . str_pad($r, 2, "0", STR_PAD_LEFT) . str_pad($g, 2, "0", STR_PAD_LEFT) . str_pad($b, 2, "0", STR_PAD_LEFT);
  }
  
  function ChangeLuminosity($RGB, $LuminosityPercent)
  {
    $HSL = RGBToHSL($RGB);
    $NewHSL = (int)(((float)$LuminosityPercent / 100) * 255) + (0xFFFF00 & $HSL);
    return HSLToRGB($NewHSL);
  }

  function RGBToHSL($RGB)
  {
    $r = 0xFF & ($RGB >> 0x10);
    $g = 0xFF & ($RGB >> 0x8);
    $b = 0xFF & $RGB;

    $r = ((float)$r) / 255.0;
    $g = ((float)$g) / 255.0;
    $b = ((float)$b) / 255.0;

    $maxC = max($r, $g, $b);
    $minC = min($r, $g, $b);

    $l = ($maxC + $minC) / 2.0;

    if($maxC == $minC)
    {
      $s = 0;
      $h = 0;
    }
    else
    {
      if($l < .5)
      {
        $s = ($maxC - $minC) / ($maxC + $minC);
      }
      else
      {
        $s = ($maxC - $minC) / (2.0 - $maxC - $minC);
      }
      if($r == $maxC)
        $h = ($g - $b) / ($maxC - $minC);
      if($g == $maxC)
        $h = 2.0 + ($b - $r) / ($maxC - $minC);
      if($b == $maxC)
        $h = 4.0 + ($r - $g) / ($maxC - $minC);

      $h = $h / 6.0; 
    }

    $h = (int)round(255.0 * $h);
    $s = (int)round(255.0 * $s);
    $l = (int)round(255.0 * $l);

    $HSL = $l + ($s << 0x8) + ($h << 0x10);
    return $HSL;
  }

  function HSLToRGB($HSL)
  {
    $h = 0xFF & ($HSL >> 0x10);
    $s = 0xFF & ($HSL >> 0x8);
    $l = 0xFF & $HSL;

    $h = ((float)$h) / 255.0;
    $s = ((float)$s) / 255.0;
    $l = ((float)$l) / 255.0;

    if($s == 0)
    {
      $r = $l;
      $g = $l;
      $b = $l;
    }
    else
    {
      if($l < .5)
      {
        $t2 = $l * (1.0 + $s);
      }
      else
      {
        $t2 = ($l + $s) - ($l * $s);
      }
      $t1 = 2.0 * $l - $t2;

      $rt3 = $h + 1.0/3.0;
      $gt3 = $h;
      $bt3 = $h - 1.0/3.0;

      if($rt3 < 0) $rt3 += 1.0;
      if($rt3 > 1) $rt3 -= 1.0;
      if($gt3 < 0) $gt3 += 1.0;
      if($gt3 > 1) $gt3 -= 1.0;
      if($bt3 < 0) $bt3 += 1.0;
      if($bt3 > 1) $bt3 -= 1.0;

      if(6.0 * $rt3 < 1) $r = $t1 + ($t2 - $t1) * 6.0 * $rt3;
      elseif(2.0 * $rt3 < 1) $r = $t2;
      elseif(3.0 * $rt3 < 2) $r = $t1 + ($t2 - $t1) * ((2.0/3.0) - $rt3) * 6.0;
      else $r = $t1;

      if(6.0 * $gt3 < 1) $g = $t1 + ($t2 - $t1) * 6.0 * $gt3;
      elseif(2.0 * $gt3 < 1) $g = $t2;
      elseif(3.0 * $gt3 < 2) $g = $t1 + ($t2 - $t1) * ((2.0/3.0) - $gt3) * 6.0;
      else $g = $t1;

      if(6.0 * $bt3 < 1) $b = $t1 + ($t2 - $t1) * 6.0 * $bt3;
      elseif(2.0 * $bt3 < 1) $b = $t2;
      elseif(3.0 * $bt3 < 2) $b = $t1 + ($t2 - $t1) * ((2.0/3.0) - $bt3) * 6.0;
      else $b = $t1;
    }

    $r = (int)round(255.0 * $r);
    $g = (int)round(255.0 * $g);
    $b = (int)round(255.0 * $b);

    $RGB = $b + ($g << 0x8) + ($r << 0x10);
    return $RGB;
  }

// listing page thumbnail sizes, e.g. home page

add_image_size( "newshead", "726", "353", true );
add_image_size( "homehead", "544", "307", true );

/**
 * Determines the difference between two timestamps.
 *
 * The difference is returned in a human readable format such as "1 hour",
 * "5 mins", "2 days".
 *
 * @since 1.5.0
 *
 * @param int $from Unix timestamp from which the difference begins.
 * @param int $to Optional. Unix timestamp to end the time difference. Default becomes time() if not set.
 * @return string Human readable time difference.
 * Taken from formatting.php to include months and years - Luke Oatham 
 */
function human_time_diff_plus( $from, $to = '' ) {
	$gis = "general_intranet_time_zone";
	$tzone = get_option($gis);
	date_default_timezone_set($tzone);

	$MONTH_IN_SECONDS = DAY_IN_SECONDS * 30;
     if ( empty( $to ) )
          $to = time();
     $diff = (int) abs( $to - $from );
     if ( $diff <= HOUR_IN_SECONDS ) {
          $mins = round( $diff / MINUTE_IN_SECONDS );
          if ( $mins <= 1 ) {
               $mins = 0;
          }
          /* translators: min=minute */
          $since = sprintf( _n( '%s min', '%s mins', $mins ), $mins );
     } elseif ( ( $diff <= DAY_IN_SECONDS ) && ( $diff > HOUR_IN_SECONDS ) ) {
          $hours = round( $diff / HOUR_IN_SECONDS );
          if ( $hours <= 1 ) {
               $hours = 1;
          }
          $since = sprintf( _n( '%s hour', '%s hours', $hours ), $hours );
     } elseif ( $diff >= YEAR_IN_SECONDS ) {
          $years = round( $diff / YEAR_IN_SECONDS );
          if ( $years <= 1 ) {
               $years = 1;
          }
          $since = sprintf( _n( '%s year', '%s years', $years ), $years );
     } elseif ( ( $diff >= $MONTH_IN_SECONDS ) && ( $diff < YEAR_IN_SECONDS ) ) {
          $months = round( $diff / $MONTH_IN_SECONDS );
          if ( $months <= 1 ) {
               $months = 1;
          }
          $since = sprintf( _n( '%s month', '%s months', $months ), $months );
     } elseif ( $diff >= DAY_IN_SECONDS ) {
          $days = round( $diff / DAY_IN_SECONDS );
          if ( $days <= 1 ) {
               $days = 1;
          }
          $since = sprintf( _n( '%s day', '%s days', $days ), $days );
     }
     return $since;
}

//Embed Video Fix
function add_secure_video_options($html) {
   if (strpos($html, "<iframe" ) !== false  && is_ssl() ) {
        $search = array('src="http://www.youtu','src="http://youtu');
        $replace = array('src="https://www.youtu','src="https://youtu');
        $html = str_replace($search, $replace, $html);

        return $html;
   } else {
        return $html;
   }
}
add_filter('the_content', 'add_secure_video_options', 10);


function relevanssi_user_filter($hits) {
    global $wp_query;
    if (isset($wp_query->query_vars['post_type'])) {
    	$correct_colour = array();
    	$tothits = 0;
    	foreach ($hits[0] as $hit) {
    		if ($hit->post_type == 'user') {
				$tothits++;
    		}
    	}
    }
    return $tothits;
}


?>

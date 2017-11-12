<?php
/*
Plugin Name: Hashtags
Plugin URI: http://www.helpfultechnology.com
Description: Hashtag aggregator
Author: Luke Oatham
Version: 1.3
Author URI: http://www.helpfultechnology.com
*/

add_action( 'init', 'register_hastags_templates' );
function register_hastags_templates() {

	if( function_exists('acf_add_local_field_group') ):
	
	acf_add_local_field_group(array (
		'key' => 'group_594803c774d3b',
		'title' => 'Hashtags',
		'fields' => array (
			array (
				'key' => 'field_594804965b9dd',
				'label' => __('Hashtag','govintranet'),
				'name' => 'ht_hashtag',
				'type' => 'taxonomy',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'taxonomy' => 'post_tag',
				'field_type' => 'select',
				'allow_null' => 1,
				'add_term' => 0,
				'save_terms' => 0,
				'load_terms' => 0,
				'return_format' => 'id',
				'multiple' => 0,
			),
			array (
				'key' => 'field_594803e70d0f7',
				'label' => __('Number of posts','govintranet'),
				'name' => 'ht_number_of_news_stories',
				'type' => 'text',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array (
				'key' => 'field_594804220d0f8',
				'label' => __('Highlight pages','govintranet'),
				'name' => 'ht_highlight_pages',
				'type' => 'relationship',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'post_type' => array (
					0 => 'page',
					1 => 'task',
					2 => 'event',
					3 => 'blog',
				),
				'taxonomy' => array (
				),
				'filters' => array (
					0 => 'search',
					1 => 'post_type',
				),
				'elements' => array (
					0 => 'featured_image',
				),
				'min' => '',
				'max' => '',
				'return_format' => 'id',
			),
			array (
				'key' => 'field_5948046a0d0f9',
				'label' => __('Spots','govintranet'),
				'name' => 'ht_spots',
				'type' => 'relationship',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'post_type' => array (
					0 => 'spot',
				),
				'taxonomy' => array (
				),
				'filters' => array (
					0 => 'search',
				),
				'elements' => '',
				'min' => '',
				'max' => '',
				'return_format' => 'id',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'page_template',
					'operator' => '==',
					'value' => 'template-hashtags.php',
				),
			),
		),
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
		'active' => 1,
		'description' => '',
	));
	
	endif;
	
}

/****************************************************************************
*
*	Custom page template within plugin
*
*   https://github.com/wpexplorer/page-templater/blob/master/pagetemplater.php
*
****************************************************************************/


class Hashtags {

	/**
     * A Unique Identifier
     */
	 protected $plugin_slug;

    /**
     * A reference to an instance of this class.
     */
    private static $instance;

    /**
     * The array of templates that this plugin tracks.
     */
    protected $templates;


    /**
     * Returns an instance of this class. 
     */
    public static function get_instance() {

            if( null == self::$instance ) {
                    self::$instance = new Hashtags();
            } 

            return self::$instance;

    } 

    /**
     * Initializes the plugin by setting filters and administration functions.
     */
    private function __construct() {

            $this->templates = array();

			// Add a filter to the attributes metabox to inject template into the cache.
        	if ( version_compare( floatval( get_bloginfo( 'version' ) ), '4.7', '<' ) ) { // 4.6 and older
            		add_filter(
                		'page_attributes_dropdown_pages_args',
                		array( $this, 'register_hashtags_templates' )
            		);
        	} else { // Add a filter to the wp 4.7 version attributes metabox
            		add_filter(
                		'theme_page_templates', array( $this, 'add_new_template' )
            		);
        	}

            // Add a filter to the save post to inject out template into the page cache
            add_filter(
				'wp_insert_post_data', 
				array( $this, 'register_hashtags_templates' ) 
			);


            // Add a filter to the template include to determine if the page has our 
			// template assigned and return it's path
            add_filter(
				'template_include', 
				array( $this, 'view_hashtags_template') 
			);


            // Add your templates to this array.
            $this->templates = array(
                    'template-hashtags.php'     => __('Hashtags','govintranet'),
            );
			
    } 

    /**
 	 * Adds our template to the page dropdown for v4.7+
 	 *
 	 */
	public function add_new_template( $posts_templates ) {
    	$posts_templates = array_merge( $posts_templates, $this->templates );
    	return $posts_templates;
	}

    /**
     * Adds our template to the pages cache in order to trick WordPress
     * into thinking the template file exists where it doens't really exist.
     *
     */

    public function register_hashtags_templates( $atts ) {

            // Create the key used for the themes cache
            $cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

            // Retrieve the cache list. 
			// If it doesn't exist, or it's empty prepare an array
            $templates = wp_get_theme()->get_page_templates();
            if ( empty( $templates ) ) {
                    $templates = array();
            } 

            // New cache, therefore remove the old one
            wp_cache_delete( $cache_key , 'themes');

            // Now add our template to the list of templates by merging our templates
            // with the existing templates array from the cache.
            $templates = array_merge( $templates, $this->templates );

            // Add the modified cache to allow WordPress to pick it up for listing
            // available templates
            wp_cache_add( $cache_key, $templates, 'themes', 1800 );

            return $atts;

    } 

    /**
     * Checks if the template is assigned to the page
     */
    public function view_hashtags_template( $template ) {

            global $post;
			if ( !is_object($post) ) return $template;
            if (!isset($this->templates[get_post_meta( 
				$post->ID, '_wp_page_template', true 
			)] ) || is_tax()  || is_search() ) {
				
                    return $template;
					
            } 

            $file = plugin_dir_path(__FILE__). get_post_meta( 
				$post->ID, '_wp_page_template', true 
			);
			
            // Just to be safe, we check if the file exist first
            if( file_exists( $file ) ) {
                    return $file;
            } 
			else { echo $file; }

            return $template;

    } 

}
add_action( 'plugins_loaded', array( 'Hashtags', 'get_instance' ) );


<?php
/*
Plugin Name: HT Media A to Z
Plugin URI: http://www.helpfultechnology.com
Description: Adds A to Z functionality to the document finder
Author: Luke Oatham
Version: 2.8.1
Author URI: http://www.helpfultechnology.com
*/

add_action( 'init', 'cptui_register_my_media_atoz' );
function cptui_register_my_media_atoz() {

	$labels = array(
		"name" => __("Media A to Z","govintranet"),
		"label" => __("Media A to Z","govintranet"),
		);

	$args = array(
		"labels" => $labels,
		"hierarchical" => true,
		"label" => __("Media A to Z","govintranet"),
		"show_ui" => true,
		"query_var" => true,
		"rewrite" => array( 'slug' => 'media-a-to-z', 'with_front' => true ),
		"show_admin_column" => true,
	);
	register_taxonomy( "media-a-to-z", array( "attachment" ), $args );

	register_taxonomy_for_object_type( 'category', 'attachment' );

	register_taxonomy( 'document-type',array (
		  0 => 'attachment',
		),
		array( 'hierarchical' => true,
			'label' => __('Document types','govintranet'),
			'show_ui' => true,
			'query_var' => true,
			'show_admin_column' => true,
			'labels' => array (
				'search_items' => __('Document type','govintranet'),
				'popular_items' => __('Popular types','govintranet'),
				'all_items' => __('All types','govintranet'),
				'parent_item' => __('Parent document type','govintranet'),
				'parent_item_colon' => '',
				'edit_item' => __('Edit document type','govintranet'),
				'update_item' => __('Update document type','govintranet'),
				'add_new_item' => __('Add document type','govintranet'),
				'new_item_name' => __('New document type','govintranet'),
				'separate_items_with_commas' => '',
				'add_or_remove_items' => __('Add or remove a document type','govintranet'),
				'choose_from_most_used' => __('Most used','govintranet'),
			)
		) 
	); 
	if( function_exists('acf_add_local_field_group') ):

		acf_add_local_field_group(array (
			'key' => 'group_58210edf3cca3',
			'title' => __('Filters','govintranet'),
			'fields' => array (
				array (
					'key' => 'field_58210ef9d8ca5',
					'label' => __('Show filters','govintranet'),
					'name' => 'matoz_show_filters',
					'type' => 'checkbox',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'choices' => array (
						'Search' => __('Search','govintranet'),
						'A to Z' => __('A to Z','govintranet'),
						'Document type' => __('Document type','govintranet'),
						'Category' => __('Category','govintranet'),
					),
					'default_value' => array (
					),
					'layout' => 'horizontal',
					'toggle' => 0,
					'return_format' => 'value',
				),
				array (
					'key' => 'field_582e02d4c19ee',
					'label' => __('Show page links','govintranet'),
					'name' => 'matoz_show_page_links',
					'ui' => 1,
					'ui_on_text' => __('ON','govintranet'),
					'ui_off_text' => __('OFF','govintranet'),
					'type' => 'true_false',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'message' => __('Show links to posts and pages where documents are attached','govintranet'),
					'default_value' => 0,
				),
				array (
					'default_value' => 'View in context',
					'maxlength' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'key' => 'field_5901cae453771',
					'label' => __('Link text','govintranet'),
					'name' => 'gi_docfinder_context_text',
					'type' => 'text',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => array (
						array (
							array (
								'field' => 'field_582e02d4c19ee',
								'operator' => '==',
								'value' => '1',
							),
						),
					),
				),
			),
			'location' => array (
				array (
					array (
						'param' => 'page_template',
						'operator' => '==',
						'value' => 'template-media-atoz.php',
					),
				),
				array (
					array (
						'param' => 'page_template',
						'operator' => '==',
						'value' => 'template-document-finder.php',
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


class MediaAtoZ {

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
                    self::$instance = new MediaAtoZ();
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
                		array( $this, 'register_media_atoz_templates' )
            		);
        	} else { // Add a filter to the wp 4.7 version attributes metabox
            		add_filter(
                		'theme_page_templates', array( $this, 'add_new_template' )
            		);
        	}

            // Add a filter to the save post to inject out template into the page cache
            add_filter(
				'wp_insert_post_data', 
				array( $this, 'register_media_atoz_templates' ) 
			);


            // Add a filter to the template include to determine if the page has our 
			// template assigned and return it's path
            add_filter(
				'template_include', 
				array( $this, 'view_media_atoz_template') 
			);


            // Add your templates to this array.
            $this->templates = array(
                    'template-media-atoz.php'     => __('Media A to Z','govintranet'),
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

    public function register_media_atoz_templates( $atts ) {

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
    public function view_media_atoz_template( $template ) {

            global $post;
			if ( !is_object($post) || is_tax() || is_search() ) {
				return $template;
			}
            if (!isset($this->templates[get_post_meta( 
				$post->ID, '_wp_page_template', true 
			)] ) ) {
				
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

class DocumentFinder {

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
                    self::$instance = new DocumentFinder();
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
                		array( $this, 'register_document_finder_templates' )
            		);
        	} else { // Add a filter to the wp 4.7 version attributes metabox
            		add_filter(
                		'theme_page_templates', array( $this, 'add_new_template' )
            		);
        	}

            // Add a filter to the save post to inject out template into the page cache
            add_filter(
				'wp_insert_post_data', 
				array( $this, 'register_document_finder_templates' ) 
			);


            // Add a filter to the template include to determine if the page has our 
			// template assigned and return it's path
            add_filter(
				'template_include', 
				array( $this, 'view_document_finder_template') 
			);


            // Add your templates to this array.
            $this->templates = array(
                    'template-document-finder.php'     => __('Document finder','govintranet'),
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

    public function register_document_finder_templates( $atts ) {

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
    public function view_document_finder_template( $template ) {

            global $post;
			if ( !is_object($post) || is_tax() || is_search() ) {
				return $template;
			}

            if (!isset($this->templates[get_post_meta( 
				$post->ID, '_wp_page_template', true 
			)] ) ) {
				
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
add_action( 'plugins_loaded', array( 'MediaAtoZ', 'get_instance' ) );
add_action( 'plugins_loaded', array( 'DocumentFinder', 'get_instance' ) );
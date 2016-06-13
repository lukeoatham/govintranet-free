<?php
/*
Plugin Name: HT Media A to Z
Plugin URI: http://www.helpfultechnology.com
Description: Adds A to Z functionality to the document finder
Author: Luke Oatham
Version: 1.0
Author URI: http://www.helpfultechnology.com
*/

add_action( 'init', 'cptui_register_my_media_atoz' );
function cptui_register_my_media_atoz() {

	$labels = array(
		"name" => "Media A to Z",
		"label" => "Media A to Z",
		);

	$args = array(
		"labels" => $labels,
		"hierarchical" => true,
		"label" => "Media A to Z",
		"show_ui" => true,
		"query_var" => true,
		"rewrite" => array( 'slug' => 'media-a-to-z', 'with_front' => true ),
		"show_admin_column" => true,
	);
	register_taxonomy( "media-a-to-z", array( "attachment" ), $args );


}

class MediaAtoZ {

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
                add_filter(
					'page_attributes_dropdown_pages_args',
					 array( $this, 'register_media_atoz_templates' ) 
				);


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
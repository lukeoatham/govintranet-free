<?php
/*
Plugin Name: HT Timeline
Plugin URI: http://www.helpfultechnology.com
Description: Create timeline walls with an optional filter
Author: Luke Oatham
Version: 1.1.3
Author URI: http://www.helpfultechnology.com
*/

add_action( 'init', 'ht_register_timeline_custom_fields' );
function ht_register_timeline_custom_fields() {

	/*
	Register timeline custom post type
	*/

	if( function_exists('acf_add_local_field_group') ):
	acf_add_local_field_group(array (
		'key' => 'group_56c3389135759',
		'title' => 'Timeline',
		'fields' => array (
			array (
				'key' => 'field_56c338ad4eb09',
				'label' => 'Timeline items',
				'name' => 'ht_timeline_items',
				'type' => 'repeater',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'collapsed' => '',
				'min' => '',
				'max' => '',
				'layout' => 'block',
				'button_label' => 'Add another item',
				'sub_fields' => array (
					array (
						'key' => 'field_56c3459a78a28',
						'label' => 'Title',
						'name' => 'ht_timeline_title',
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
						'readonly' => 0,
						'disabled' => 0,
					),
					array (
						'key' => 'field_56c3397c4eb0b',
						'label' => 'Content',
						'name' => 'ht_timeline_content',
						'type' => 'wysiwyg',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array (
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'tabs' => 'all',
						'toolbar' => 'full',
						'media_upload' => 1,
					),
					array (
						'key' => 'field_56c346f8f7bbf',
						'label' => 'Icon',
						'name' => 'ht_timeline_icon',
						'type' => 'text',
						'instructions' => 'See http://getbootstrap.com/components/
	Enter just the last part e.g. hand-up, pushpin',
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
						'readonly' => 0,
						'disabled' => 0,
					),
					array (
						'key' => 'field_56c3495012209',
						'label' => 'Colour',
						'name' => 'ht_timeline_colour',
						'type' => 'radio',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array (
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'choices' => array (
							'info' => 'Light blue',
							'primary' => 'Dark blue',
							'danger' => 'Red',
							'warning' => 'Orange',
							'success' => 'Green',
							'default' => 'Grey',
						),
						'other_choice' => 0,
						'save_other_choice' => 0,
						'default_value' => '',
						'layout' => 'vertical',
					),
				),
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'page_template',
					'operator' => '==',
					'value' => 'template-timelines.php',
				),
			),
			array (
				array (
					'param' => 'page_template',
					'operator' => '==',
					'value' => 'template-timelines-filter.php',
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

class Timelines {

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
                        self::$instance = new Timelines();
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
	                		array( $this, 'register_timelines_simple_templates' )
	            		);
	        	} else { // Add a filter to the wp 4.7 version attributes metabox
	            		add_filter(
	                		'theme_page_templates', array( $this, 'add_new_template' )
	            		);
	        	}


                // Add a filter to the save post to inject out template into the page cache
                add_filter(
					'wp_insert_post_data',
					array( $this, 'register_timelines_simple_templates' )
				);


                // Add a filter to the template include to determine if the page has our
				// template assigned and return it's path
                add_filter(
					'template_include',
					array( $this, 'view_timelines_simple_template')
				);


                // Add your templates to this array.
                $this->templates = array(
                        'template-timelines.php'     => __('Timeline','govintranet'),
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

        public function register_timelines_simple_templates( $atts ) {

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
        public function view_timelines_simple_template( $template ) {

                global $post;
				if ( !is_object($post) || is_tax() || is_search() || is_tag() ) {
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

class TimelinesFilter {

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
                        self::$instance = new TimelinesFilter();
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
	                		array( $this, 'register_timelines_filter_templates' )
	            		);
	        	} else { // Add a filter to the wp 4.7 version attributes metabox
	            		add_filter(
	                		'theme_page_templates', array( $this, 'add_new_template' )
	            		);
	        	}


                // Add a filter to the save post to inject out template into the page cache
                add_filter(
					'wp_insert_post_data',
					array( $this, 'register_timelines_filter_templates' )
				);


                // Add a filter to the template include to determine if the page has our
				// template assigned and return it's path
                add_filter(
					'template_include',
					array( $this, 'view_timelines_filter_template')
				);


                // Add your templates to this array.
                $this->templates = array(
                        'template-timelines-filter.php'     => __('Timeline with filter','govintranet'),
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

        public function register_timelines_filter_templates( $atts ) {

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
        public function view_timelines_filter_template( $template ) {

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

add_action( 'plugins_loaded', array( 'TimelinesFilter', 'get_instance' ) );

add_action( 'plugins_loaded', array( 'Timelines', 'get_instance' ) );

<?php

function govintranet_customize_register ( $wp_customize ){

	$wp_customize->add_setting( 'header_background', array (
		'default' => '0b2d49',
		'type' => 'theme_mod',
		'transport' => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	));

	$wp_customize->add_setting( 'link_color', array (
		'default' => '428bca',
		'transport' => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	));

	$wp_customize->add_setting( 'link_visited_color', array (
		'default' => '7303aa',
		'transport' => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	));
	
/*
	$wp_customize->add_section( 'colors', array (
		'title' => __('Intranet colors', 'govintranet'),
		'description' => __('Change link colours', 'govintranet'),
		'priority' => 30,
	));
*/
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'header_color_control', array (
		'label' => __('Header background colour', 'govintranet'),
		'section' => 'colors',
		'settings' => 'header_background',
	)));

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'link_color_control', array (
		'label' => __('Link colour', 'govintranet'),
		'section' => 'colors',
		'settings' => 'link_color',
	)));

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'link_visited_color_control', array (
		'label' => __('Visited colour', 'govintranet'),
		'section' => 'colors',
		'settings' => 'link_visited_color',
	)));

	
}

add_action ( 'customize_register', 'govintranet_customize_register' );




function govintranet_customizer_script() {
	wp_enqueue_script( 'govintranet-customizer-script', get_template_directory_uri().'/js/theme-options.js', array ( 'jquery', 'customize-preview'), '', true );
}	
add_action ( 'customize_preview_init', 'govintranet_customizer_script' );


?>
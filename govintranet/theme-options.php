<?php

function govintranet_customize_register ( $wp_customize ){
	
	/* SETTINGS */

	$wp_customize->add_setting( 'options_widget_border_height', array(
		'default' => '8',
		'type' => 'option',
		'transport' => 'postMessage',
	) );
	
	$wp_customize->add_setting( 'header_background', array(
		'default' => '#000000',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport' => 'postMessage',
		'type' => 'option',
	) );

	$wp_customize->add_setting( 'options_btn_text_colour', array(
		'default' => '#ffffff',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport' => 'postMessage',
		'type' => 'option',
	) );

	$wp_customize->add_setting( 'options_complementary_colour', array(
		'default' => '#f72525',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport' => 'postMessage',
		'type' => 'option',
	) );

	$wp_customize->add_setting( 'link_color', array (
		'default' => '#428bca',
		'transport' => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	));

	$wp_customize->add_setting( 'link_visited_color', array (
		'default' => '#7303aa',
		'transport' => 'postMessage',
		'sanitize_callback' => 'sanitize_hex_color',
	));

	/* CONTROLS */

	$wp_customize->add_control( 'options_widget_border_height', array(
		'type' => 'range',
		'section' => 'title_tagline',
		'priority'   => 11,
		'label' => __( 'Border height' ),
		'description' => __( '1 to 15' ),
		'input_attrs' => array(
			'min' => 1,
			'max' => 15,
			'step' => 1,
	)));
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'header_color_control', array (
		'label' => __('Header background colour', 'govintranet'),
		'section' => 'colors',
		'settings' => 'header_background',
	)));
	
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'btn_color_control', array(
		'label' => __( 'Button text colour', 'govintranet' ),
		'section' => 'colors',
		'settings' => 'options_btn_text_colour'
	)));

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'color_control', array(
		'label' => __( 'Complementary colour', 'govintranet' ),
		'section' => 'colors',
		'settings' => 'options_complementary_colour'
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
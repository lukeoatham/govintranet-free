<?php
/*
Plugin Name: HT Staging Beware!
Plugin URI: https://help.govintra.net
Description: Shows a warning bar on a site, to help avoid confusing staging and live environments
Author: Steph Gray
Version: 0.1
Author URI: https://www.agentodigital.com
*/

function showBewareBar(){
	wp_enqueue_script( 'govintranet_beware_script', plugins_url("/ht-staging-beware/beware.js"));
	wp_enqueue_style( 'govintranet_beware_styles', plugins_url("/ht-staging-beware/beware.css"));
	return true;
}

add_action("wp_head", "showBewareBar");

?>
<?php
/*
Plugin Name: HT Remove post menu
Plugin URI: https://help.govintra.net
Description: Removes the Post option from menus.
Author: Luke Oatham
Version: 1.0
Author URI: http://intranetdiary.co.uk
*/

/********************************************/
/* HIDE post POST TYPE IN ADMIN INTERFACE  */
/********************************************/


function gi_post_menu_remove() {
	// remove Posts from left hand menu
    remove_menu_page( 'edit.php');
}

function gi_post_admin_menu_remove( $wp_admin_bar ) {
	// remove Post from +New menu
	$wp_admin_bar->remove_node( 'new-post' );
	
	// get +New menu and alter default option to news
	$new = $wp_admin_bar->get_node( 'new-content' );
	$href = $new->href . "?post_type=news";
	$new->href = $href;	
	
	// remove menu and replace with new
	$wp_admin_bar->remove_node( 'new-content' );
	$wp_admin_bar->add_node( $new );
}

add_action( 'admin_menu', 'gi_post_menu_remove' );
add_action( 'admin_bar_menu', 'gi_post_admin_menu_remove', 999 );
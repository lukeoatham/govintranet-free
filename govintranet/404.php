<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package WordPress
 */

get_header(); 

$notfound = trim(get_option('options_page_not_found', ''));
if ( !$notfound ) {
	$notfound = "<h1>" . __("Not found","govintranet") . "</h1><p>" . __( 'The page that you are trying to reach doesn\'t exist. <br><br>Please go back or try searching.','govintranet') . "</p>";
}
echo "<div class='col-lg-12 white'>";
echo wpautop($notfound);  
echo "</div>";

get_footer(); ?>
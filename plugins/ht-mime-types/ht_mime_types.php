<?php
/*
Plugin Name: HT Mime Types
Plugin URI: https://help.govintra.net
Description: Extends allowed file types in Media upload 
Author: Luke Oatham
Version: 1.0
Author URI: httpa://www.agentodigital.com
*/

add_filter('upload_mimes', 'custom_upload_mimes'); 
function custom_upload_mimes ( $existing_mimes=array() ) { 
	$existing_mimes['rdp'] = 'application/rdp'; 
	$existing_mimes['eps'] = 'application/eps'; 
	$existing_mimes['oft'] = 'application/vnd.ms-outlook'; 
	$existing_mimes['one'] = 'application/onenote'; 
	return $existing_mimes; 
}

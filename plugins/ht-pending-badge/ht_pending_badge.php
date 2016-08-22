<?php
/*
Plugin Name: HT Pending badge
Plugin URI: http://www.helpfultechnology.com
Description: Display number of pending posts in admin menus
Author: Luke Oatham
Version: 1.0
Author URI: http://www.helpfultechnology.com
*/

add_action('admin_menu', 'notification_bubble_in_admin_menu');

function notification_bubble_in_admin_menu() {
    global $menu; 

    $pending = get_posts(array('post_type'=>'page','post_status'=>'pending','posts_per_page'=>-1));
    $pages = count($pending);
    $pending = get_posts(array('post_type'=>'task','post_status'=>'pending','posts_per_page'=>-1));
    $tasks = count($pending);
    $pending = get_posts(array('post_type'=>'news','post_status'=>'pending','posts_per_page'=>-1));
    $news = count($pending);
    $pending = get_posts(array('post_type'=>'news-update','post_status'=>'pending','posts_per_page'=>-1));
    $newsupdates = count($pending);
    $pending = get_posts(array('post_type'=>'blog','post_status'=>'pending','posts_per_page'=>-1));
    $blogs = count($pending);
    $pending = get_posts(array('post_type'=>'vacancy','post_status'=>'pending','posts_per_page'=>-1));
    $vacancies = count($pending);
    $pending = get_posts(array('post_type'=>'project','post_status'=>'pending','posts_per_page'=>-1));
    $projects = count($pending);
    $pending = get_posts(array('post_type'=>'event','post_status'=>'pending','posts_per_page'=>-1));
    $events = count($pending);
    $pending = get_posts(array('post_type'=>'jargon-buster','post_status'=>'pending','posts_per_page'=>-1));
    $jargonbusters = count($pending);

    $count = 0;
    
    foreach ($menu as $key=>$m){ 
	    if ( $m[5] == "menu-pages" )$menu[$key][0] .= $pages ? " <span class='update-plugins count-1'><span class='update-count'>$pages </span></span>" : '';
	    if ( $m[5] == "menu-posts-task" )$menu[$key][0] .= $tasks ? " <span class='update-plugins count-1'><span class='update-count'>$tasks </span></span>" : '';
	    if ( $m[5] == "menu-posts-news" ) $menu[$key][0] .= $news ? " <span class='update-plugins count-1'><span class='update-count'>$news </span></span>" : '';
	    if ( $m[5] == "menu-posts-news-update" ) $menu[$key][0] .= $newsupdates ? " <span class='update-plugins count-1'><span class='update-count'>$newsupdates </span></span>" : '';
	    if ( $m[5] == "menu-posts-blog" ) $menu[$key][0] .= $blogs ? " <span class='update-plugins count-1'><span class='update-count'>$blogs </span></span>" : '';
	    if ( $m[5] == "menu-posts-vacancy" ) $menu[$key][0] .= $vacancies ? " <span class='update-plugins count-1'><span class='update-count'>$vacancies </span></span>" : '';
	    if ( $m[5] == "menu-posts-project" ) $menu[$key][0] .= $projects ? " <span class='update-plugins count-1'><span class='update-count'>$projects </span></span>" : '';
	    if ( $m[5] == "menu-posts-event" ) $menu[$key][0] .= $events ? " <span class='update-plugins count-1'><span class='update-count'>$events </span></span>" : '';
	    if ( $m[5] == "menu-posts-jargon-buster" ) $menu[$key][0] .= $jargonbusters ? " <span class='update-plugins count-1'><span class='update-count'>$jargonbusters </span></span>" : '';

	    $count++;
    }
}
?>
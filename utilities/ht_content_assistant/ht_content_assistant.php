<?php
/*
Plugin Name: Content assistant
Plugin URI: https://help.govintra.net
Description: Provides advice and tips for content editors on tasks and news.
Author: Luke Oatham
Version: 1.1
Author URI: https://www.agentodigital.com
*/
 
add_filter('post_updated_messages', 'task_updated_messages');
function task_updated_messages( $messages ) {
  global $post, $post_ID;
  $titlewarning = "";

  // check for long titles
  if ( strlen ( get_the_title($post_ID) ) > 124 ) $titlewarning.= "<span class='dashicons dashicons-info' style='color: red;'></span> <strong>Could you shorten your title?</strong><br>";
 
  // check for 'ing' in the first word
  if ( ( strpos( get_the_title($post_ID), "ing " ) != 0 && strpos( get_the_title($post_ID), " " ) > strpos( get_the_title($post_ID), "ing " ) ) || strpos( strtolower( get_the_title($post_ID) ), "ing " ) === 0  ) $titlewarning.= "<span class='dashicons dashicons-info' style='color: red;'></span> <strong>Could you shorten your title by removing 'ing'? e.g. instead of 'Writing a speech' change to 'Write a speech'</strong><br>";

  // nominalisation
  if ( ( strpos( get_the_title($post_ID), "ing of " ) != 0 && strpos( get_the_title($post_ID), " " ) > strpos( get_the_title($post_ID), "ing " ) ) || strpos( strtolower( get_the_title($post_ID) ), "ing of " ) === 0  ) $titlewarning.= "<span class='dashicons dashicons-info' style='color: red;'></span> <strong>Could you make your title more active?'</strong><br>";
  
  // check for 'how to' at the start
  if ( strpos( strtolower( get_the_title($post_ID) ), "how to" ) === 0 ) $titlewarning.= "<span class='dashicons dashicons-info' style='color: red;'></span> <strong>Could you shorten your title by removing 'how to'?</strong><br>";

  // check for 'my' at the start
  if ( strpos( strtolower( get_the_title($post_ID) ), "my " ) === 0 ) $titlewarning.= "<span class='dashicons dashicons-info' style='color: red;'></span> <strong>Are you talking to your reader? Could you use 'your' instead of 'my'?</strong><br>";

  if ( strpos( strtolower( get_the_title($post_ID) ), " my " ) != 0 ) $titlewarning.= "<span class='dashicons dashicons-info' style='color: red;'></span> <strong>Are you talking to your reader? Could you use 'your' instead of 'my'?</strong><br>";

  // check init caps
  if  ( strtoupper( get_the_title($post_ID) ) ==  get_the_title($post_ID) && strlen(get_the_title($post_ID)) > 5 ) :
  	$titlewarning.= "<span class='dashicons dashicons-info' style='color: red;'></span> <strong>Your title is all capitals.</strong><br>";
  elseif ( ucwords( get_the_title($post_ID) ) ==  get_the_title($post_ID) ) :
  	$titlewarning.= "<span class='dashicons dashicons-info' style='color: red;'></span> <strong>Only capitalise each word if your title is a proper noun.</strong><br>";
  endif;

  // check for unsightly html at the start
  $unwanted = strpos( strtolower( $post->post_content ), "<ul" );
  if ( $unwanted === 0 ) $unwanted = 1;
  if ( $unwanted && $unwanted < 140 && !$post->post_excerpt ) $titlewarning.= "<span class='dashicons dashicons-info' style='color: red;'></span> <strong>There is a list near the start of your content. Consider adding a custom excerpt.</strong><br>";
  $unwanted = strpos( strtolower( $post->post_content ), "<ol" );
  if ( $unwanted === 0 ) $unwanted = 1;
  if ( $unwanted && $unwanted < 140 && !$post->post_excerpt ) $titlewarning.= "<span class='dashicons dashicons-info' style='color: red;'></span> <strong>There is a list near the start of your content. Consider adding a custom excerpt.</strong><br>";
  $unwanted = strpos( strtolower( $post->post_content ), "<table" );
  if ( $unwanted === 0 ) $unwanted = 1;
  if ( $unwanted && $unwanted < 140 && !$post->post_excerpt ) $titlewarning.= "<span class='dashicons dashicons-info' style='color: red;'></span> <strong>There is a table near the start of your content. Consider adding a custom excerpt.</strong><br>";

  // check empty / parent terms
  if ( !get_the_terms( $post_ID, 'category' ) ) $titlewarning.= "<span class='dashicons dashicons-info' style='color: red;'></span> <strong>Remember to assign a category.</strong><br>";

  /* if this is a chapter and the parent has a category 
	  and we've tagged this chapter: 
	  check parent post category term is also applied here
	  else the chapter won't appear in the category template when filtered by tag
  */
  if ( $post->post_parent && get_the_terms( $post->post_parent, 'category' ) && wp_get_post_tags( $post_ID ) ) :
  	$post_tags = get_the_terms( $post_ID, 'category' ) ;
  	$parent_tags = get_the_terms( $post->post_parent, 'category' );
  	$post_array = array();
  	$parent_array = array();
  	foreach ( $post_tags as $p ){
	  	$post_array[] = $p->term_id;
  	} 
  	foreach ( $parent_tags as $p ){
	  	if ( !in_array( $p->term_id, $post_array ) ) $titlewarning.= "<span class='dashicons dashicons-info' style='color: red;'></span> <strong>Use the  " . $p->name . " category if you want this chapter to appear in tag listings.</strong><br>";
  	}
  	
  endif;


  if ( $post->post_parent && wp_get_post_tags( $post_ID )) :
  	$post_tags = wp_get_post_tags( $post_ID );
  	$parent_tags = wp_get_post_tags( $post->post_parent );
  	$post_array = array();
  	$parent_array = array();
  	foreach ( $parent_tags as $p ){
	  	$parent_array[] = $p->term_id;
  	} 
  	foreach ( $post_tags as $p ){
	  	if ( !in_array( $p->term_id, $parent_array ) ) $titlewarning.= "<span class='dashicons dashicons-info' style='color: red;'></span> <strong>Remember to include the  " . $p->name . " tag on the <a href='".get_edit_post_link($post->post_parent)."'>parent task</a>.</strong><br>";
  	}
  	
  endif;

  $unwanted = strpos( strtolower( $post->post_content ), "<h1" );
  if ( $unwanted === 0 ) $unwanted = 1;
  if ( !$unwanted ) $unwanted = strpos( strtolower( $post->post_content ), "<h2" );
  if ( $unwanted === 0 ) $unwanted = 1;
  if ( !$unwanted ) $unwanted = strpos( strtolower( $post->post_content ), "<h3" );
  if ( $unwanted === 0 ) $unwanted = 1;
  if ( $unwanted && $unwanted < 140 && !$post->post_excerpt ) $titlewarning.= "<span class='dashicons dashicons-info' style='color: red;'></span> <strong>There is a heading near the start of your content. Consider adding a custom excerpt.</strong><br>";
  
  if ( $titlewarning ) $titlewarning = "<br><h4>Content assistant</h4>" . $titlewarning;

  $messages['task'] = array(
    0 => '', // Unused. Messages start at index 1.
    1 => sprintf( __('Task updated. <a href="%s">View task</a>%s'), esc_url( get_permalink($post_ID) ), $titlewarning ),
    2 => __('Custom field updated.'),
    3 => __('Custom field deleted.'),
    4 => __('Task updated.','govintranet'),
    /* translators: %s: date and time of the revision */
    5 => isset($_GET['revision']) ? sprintf( __('Task restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ), $titlewarning ) : false,
    6 => sprintf( __('Task published. <a href="%s">View task</a>%s'), esc_url( get_permalink($post_ID) ), $titlewarning ),
    7 => __('Task saved.'),
    8 => sprintf( __('Task submitted. <a target="_blank" href="%s">Preview task</a>%s'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ), $titlewarning ),
    9 => sprintf( __('Task scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview task</a>%s'),
      // translators: Publish box date format, see http://php.net/date
      date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ), $titlewarning ),
    10 => sprintf( __('Task draft updated. <a target="_blank" href="%s">Preview task</a>%s'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ), $titlewarning ),
  );

  return $messages;
}

add_filter('post_updated_messages', 'news_updated_messages');
function news_updated_messages( $messages ) {
  global $post, $post_ID;
  $titlewarning = "";

  // check for long titles
  if ( strlen ( get_the_title($post_ID) ) > 124 ) $titlewarning.= "<span class='dashicons dashicons-info' style='color: red;'></span> <strong>Could you shorten your title?</strong><br>";
 
  // check init caps
  if  ( strtoupper( get_the_title($post_ID) ) ==  get_the_title($post_ID) && strlen(get_the_title($post_ID)) > 5 ) :
  	$titlewarning.= "<span class='dashicons dashicons-info' style='color: red;'></span> <strong>Your title is all capitals.</strong><br>";
  elseif ( ucwords( get_the_title($post_ID) ) ==  get_the_title($post_ID) ) :
  	$titlewarning.= "<span class='dashicons dashicons-info' style='color: red;'></span> <strong>Only capitalise each word if your title is a proper noun.</strong><br>";
  endif;

  // check for unsightly html at the start
  $unwanted = strpos( strtolower( $post->post_content ), "<ul" );
  if ( $unwanted === 0 ) $unwanted = 1;
  if ( $unwanted && $unwanted < 140 && !$post->post_excerpt ) $titlewarning.= "<span class='dashicons dashicons-info' style='color: red;'></span> <strong>There is a list near the start of your content. Consider adding a custom excerpt.</strong><br>";
  $unwanted = strpos( strtolower( $post->post_content ), "<ol" );
  if ( $unwanted === 0 ) $unwanted = 1;
  if ( $unwanted && $unwanted < 140 && !$post->post_excerpt ) $titlewarning.= "<span class='dashicons dashicons-info' style='color: red;'></span> <strong>There is a list near the start of your content. Consider adding a custom excerpt.</strong><br>";
  $unwanted = strpos( strtolower( $post->post_content ), "<table" );
  if ( $unwanted === 0 ) $unwanted = 1;
  if ( $unwanted && $unwanted < 140 && !$post->post_excerpt ) $titlewarning.= "<span class='dashicons dashicons-info' style='color: red;'></span> <strong>There is a table near the start of your content. Consider adding a custom excerpt.</strong><br>";
  $unwanted = strpos( strtolower( $post->post_content ), "<h1" );
  if ( $unwanted === 0 ) $unwanted = 1;
  if ( !$unwanted ) $unwanted = strpos( strtolower( $post->post_content ), "<h2" );
  if ( $unwanted === 0 ) $unwanted = 1;
  if ( !$unwanted ) $unwanted = strpos( strtolower( $post->post_content ), "<h3" );
  if ( $unwanted === 0 ) $unwanted = 1;
  if ( $unwanted && $unwanted < 140 && !$post->post_excerpt ) $titlewarning.= "<span class='dashicons dashicons-info' style='color: red;'></span> <strong>There is a heading near the start of your content. Consider adding a custom excerpt.</strong><br>";

  if ( $titlewarning ) $titlewarning = "<br><h4>Content assistant</h4>" . $titlewarning;

  $messages['news'] = array(
    0 => '', // Unused. Messages start at index 1.
    1 => sprintf( __('News updated. <a href="%s">View news</a>%s'), esc_url( get_permalink($post_ID) ), $titlewarning ),
    2 => __('Custom field updated.'),
    3 => __('Custom field deleted.'),
    4 => __('News updated.','govintranet'),
    /* translators: %s: date and time of the revision */
    5 => isset($_GET['revision']) ? sprintf( __('News restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ), $titlewarning ) : false,
    6 => sprintf( __('News published. <a href="%s">View news</a>%s'), esc_url( get_permalink($post_ID) ), $titlewarning ),
    7 => __('News saved.'),
    8 => sprintf( __('News submitted. <a target="_blank" href="%s">Preview news</a>%s'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ), $titlewarning ),
    9 => sprintf( __('News scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview news</a>%s'),
      // translators: Publish box date format, see http://php.net/date
      date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ), $titlewarning ),
    10 => sprintf( __('News draft updated. <a target="_blank" href="%s">Preview news</a>%s'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ), $titlewarning ),
  );

  return $messages;
}


?>
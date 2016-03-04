<?php
/*
Plugin Name: Content assistant
Plugin URI: http://www.helpfultechnology.com
Description: Provides advice and tips for content editors.
Author: Luke Oatham
Version: 1.0
Author URI: http://www.helpfultechnology.com
*/
 
add_filter('post_updated_messages', 'task_updated_messages');
function task_updated_messages( $messages ) {
  global $post, $post_ID;
  $titlewarning = "";
  
  // check for 'ing' in the first word
  if ( ( strpos( get_the_title($post_ID), "ing " ) != 0 && strpos( get_the_title($post_ID), " " ) > strpos( get_the_title($post_ID), "ing " ) ) || strpos( strtolower( get_the_title($post_ID) ), "ing " ) === 0  ) $titlewarning.= "<br><span class='dashicons dashicons-info' style='color: red;'></span> <strong>Could you shorten your title by removing 'ing'? e.g. instead of 'Writing a speech' change to 'Write a speech'</strong>";
  
  // check for 'how to' at the start
  if ( strpos( strtolower( get_the_title($post_ID) ), "how to" ) === 0 ) $titlewarning.= "<br><span class='dashicons dashicons-info' style='color: red;'></span> <strong>Could you shorten your title by removing 'how to'?</strong>";

  // check for 'my' at the start
  if ( strpos( strtolower( get_the_title($post_ID) ), "my " ) === 0 ) $titlewarning.= "<br><span class='dashicons dashicons-info' style='color: red;'></span> <strong>Are you talking to your reader? Could you use 'your' instead of 'my'?</strong>";

  if ( strpos( strtolower( get_the_title($post_ID) ), " my " ) != 0 ) $titlewarning.= "<br><span class='dashicons dashicons-info' style='color: red;'></span> <strong>Are you talking to your reader? Could you use 'your' instead of 'my'?</strong>";

  if ( !get_the_terms( $id, 'category' ) ) $titlewarning.= "<br><span class='dashicons dashicons-info' style='color: red;'></span> <strong>Remember to assign a category.</strong>";

  if ( $post->post_parent && wp_get_post_tags( $post_ID )) :
  	$post_tags = wp_get_post_tags( $post_ID );
  	$parent_tags = wp_get_post_tags( $post->post_parent );
  	$post_array = array();
  	$parent_array = array();
  	foreach ( $parent_tags as $p ){
	  	$parent_array[] = $p->term_id;
  	} 
  	foreach ( $post_tags as $p ){
	  	if ( !in_array( $p->term_id, $parent_array ) ) $titlewarning.= "<br><span class='dashicons dashicons-info' style='color: red;'></span> <strong>Remember to include the  " . $p->name . " tag on the <a href='".get_permalink($post->post_parent)."'>parent task</a>.</strong>";
  	}
  	
  endif;

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


?>
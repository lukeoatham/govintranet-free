<?php
/*
Plugin Name: GovIntranet 4.2 upgrade
Plugin URI: http://www.helpfultechnology.com
Description: Upgrades need to know news from version 4+ to 4.2 updates
Author: Luke Oatham
Version: 1.0
Author URI: http://www.helpfultechnology.com
*/

add_action('admin_menu', 'ht_g42up_menu');



function ht_g42up_menu() {
  add_submenu_page('tools.php','GovIntranet 4.2 upgrade', 'GovIntranet 4.2 upgrade', 'manage_options', 'g42up', 'ht_g42up_options');
}

function ht_g42up_options() {

  if (!current_user_can('manage_options'))  {
	wp_die( __('You do not have sufficient permissions to access this page.') );
  }

  ob_start();
  
	echo "<div class='wrap'>";
	echo "<h2>" . __( ' GovIntranet 4.2 upgrade' ) . "</h2>";
	
  if ($_REQUEST['action'] == "processimport") {


  		global $wpdb;
  		global $post;
  		$need_to_know_news = new WP_Query(
	  		array(
		  		'post_type' => 'news',
		  		'post_status' => 'any',
		  		'post_format' => 'post-format-status',
		  		'posts_per_page' => -1,
	  		)
  		);
  		if ( $need_to_know_news->have_posts() ) while ( $need_to_know_news->have_posts() ):
  			$need_to_know_news->the_post(); 
  			wp_remove_object_terms( $post->ID, 'post-format-status', 'post_format' );
  			$newsterms = get_the_terms($post->ID, 'news-type');
  			$new = '';
			if (count($newsterms) > 0):
				foreach ($newsterms as $nt){  
					if ($nt->term_id == 0 ) continue;
					$term_title = $nt->slug; 
					$term_desc = $nt->description;
					unset($new);
					if ($term_title && strtolower($term_title) != 'uncategorized'): 
						$termslug = "update-".$term_title;
						$new = term_exists( $termslug, 'update-type'); 
					endif;
					if (!is_array($new)): 
						$new = wp_insert_term( $nt->name, 'update-type', array('slug'=>$termslug,'description'=>$term_desc ) ); 
						$newid = $new['term_id'];
					else:
						$newid = $new['term_id'];
					endif;
					wp_set_object_terms($post->ID, $termslug, 'update-type', true);
					wp_remove_object_terms( $post->ID, $term_title, 'news-type' );
					sleep(0.1);
				}
			endif;
  			$newsterms = get_the_terms($post->ID, 'post_tag');
  			$new = '';
			if (count($newsterms) > 0):
				foreach ($newsterms as $nt){  
					$term_title = $nt->slug; 
					wp_remove_object_terms( $post->ID, $term_title, 'post_tag' );
				}
			endif;

			$d = get_post_meta($post->ID, 'news_expiry_date', true);
			add_post_meta($post->ID, 'update_expiry_date', $d);
			$d = get_post_meta($post->ID, 'news_expiry_time', true);
			add_post_meta($post->ID, 'update_expiry_time', $d);
			$d = get_post_meta($post->ID, 'news_expiry_action', true);
			add_post_meta($post->ID, 'update_expiry_action', $d);
			$d = get_post_meta($post->ID, 'news_auto_expiry', true);
			add_post_meta($post->ID, 'update_auto_expiry', $d);

			delete_post_meta($post->ID, 'news_expiry_date');
			delete_post_meta($post->ID, 'news_expiry_time');
			delete_post_meta($post->ID, 'news_expiry_action');
			delete_post_meta($post->ID, 'news_auto_expiry');

			delete_post_meta($post->ID, 'related');
			delete_post_meta($post->ID, 'related_team');
			delete_post_meta($post->ID, 'keywords');
			delete_post_meta($post->ID, 'document_attachments');
			delete_post_meta($post->ID, '_news_expiry_date');
			delete_post_meta($post->ID, '_news_expiry_time');
			delete_post_meta($post->ID, '_news_expiry_action');
			delete_post_meta($post->ID, '_news_auto_expiry');
			delete_post_meta($post->ID, '_related');
			delete_post_meta($post->ID, '_related_team');
			delete_post_meta($post->ID, '_keywords');
			delete_post_meta($post->ID, '_document_attachments');
  			if ( set_post_type( $post->ID, 'update'  ) ):
  				echo "<BR> Upgraded ". esc_attr($post->post_title);
  			else:
  				echo "<BR> Skipped ". esc_attr($post->post_title);
  			endif;
  		endwhile;

	echo "<h1>Finished</h1>";    	
	
  } else {
	
		echo "
		<p></p> 
		 <form method='post'>
		 	<p>This action will upgrade your need to know news posts to GovIntranet 4.2 updates</p>
		 	<p>It is not essential to perform this upgrade. 'Need to know' news will still work.</p>
		 	<p>Only proceed if you wish to change 'Need to know' news to Updates.</p>
			<p><input type='submit' value='Upgrade now' class='button-primary' /></p>
			<input type='hidden' name='page' value='g42up' />
			<input type='hidden' name='action' value='processimport' />
		  </form><br />
		"; 		
  }

	echo "</div>";  

 	ob_end_flush();
}

?>
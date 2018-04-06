<?php
/**
 * The Template for displaying all single blogposts.
 *
 * @package WordPress
 */

if ( get_post_format($post->ID) == 'link' ){
	$external_link = get_post_meta($post->ID,'external_link',true);
	if ($external_link){
		wp_redirect($external_link); 
		exit;
	}	
}

function filter_blogs($query) {
    if ($query->is_tag && !is_admin()) {
		$query->set('post_type', array('blog'));
    }
    return $query;
}; 

get_header(); 

remove_filter('pre_get_posts', 'ht_filter_search');

?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
	
		<div class="col-lg-7 col-md-8 col-sm-12 white">

			<div class="row">
				<div class='breadcrumbs'>
					<?php if(function_exists('bcn_display') && !is_front_page()) {
						bcn_display();
						}?>
				</div>
			</div>
			<article class="clearfix">
			<?php 
			$video = null;
			//check if a video thumbnail exists, if so we won't use it to display as a headline image
			if (function_exists('get_video_thumbnail')){
				$video = get_video_thumbnail(); 
			}
			if (!$video){
				$ts = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'newshead' ); 
				$tt = get_the_title();
				$tn = "<img src='".$ts[0]."' width='".$ts[1]."' height='".$ts[2]."' class='img img-responsive' alt='".esc_attr($tt)."' />";
				if ($ts){
					echo $tn;
					echo wpautop( "<p class='news_date'>".get_post_thumbnail_caption()."</p>" );
				}
			}
			the_title("<h1>","</h1>");
			$mainid = $post->ID;
			$article_date = $post->post_date;
			if ( !$article_date ) {
				$article_date = get_the_date();
			}
			$article_date = date(get_option('date_format'),strtotime($article_date));	
			echo '<p class=news_date>' . $article_date . '</p>'; 
			if ( has_post_format('video', $post->ID) ){
				echo apply_filters('the_content', get_post_meta( $post->ID, 'news_video_url', true));
			}
			the_content(); 
			?>
			</article>
			<?php
			get_template_part("part", "downloads"); 
			if ('open' == $post->comment_status) {
				 comments_template( '', true ); 
			}
		 	?>
		</div> <!--end of first column-->

		<div class="col-lg-4 col-lg-offset-1 col-md-4 col-sm-12" id="sidebar">
			<h2 class="sr-only">Sidebar</h2>
			<?php
			$sidehtml = '';
            $user = get_userdata($post->post_author);
            
            $sidehtml.= "<div class='widget-box'><h3>" . __('Author' , 'govintranet') . "</h3><div class='well'><div class='media'>";
            
            $gis = "options_module_staff_directory";
			$forumsupport = get_option($gis); 
			if ($forumsupport){
				$profile_url = gi_get_user_url($post->post_author); 
                $sidehtml.= "<a class='pull-left' href='" . $profile_url . "'>";
            } else {
                $sidehtml.= "<a class='pull-left' href='".site_url()."/author/" . $user->user_nicename . "/'>";	                        
            }
			$user_info = get_userdata($post->post_author);
			$displayname = get_user_meta($post->post_author ,'first_name',true )." ".get_user_meta($post->post_author ,'last_name',true );		
			$directorystyle = get_option('options_staff_directory_style'); // 0 = squares, 1 = circles
			$avstyle = "";
			if ( $directorystyle==1 ) $avstyle = " img-circle";
			$image_url = get_avatar($post->post_author , 150, "", $user_info->display_name);
			$image_url = str_replace(" photo", " photo alignleft".$avstyle, $image_url);
			$image_url = str_replace('"150"', '"96"', $image_url);
			$image_url = str_replace("'150'", "'96'", $image_url);
            $sidehtml.= $image_url;
            $sidehtml.= "</a>";
            $sidehtml.= "<div class='media-body'><p class='media-heading'>";
            $sidehtml.= "<strong>".$user->display_name."</strong><br>";                        
            $jobtitle = get_user_meta($user->ID, 'user_job_title',true);
            $bio = get_user_meta($user->ID,'description',true);                        
			$sidehtml.= "<strong>".$jobtitle."</strong><br class='blog-staff-profile-link'>";
            if ($forumsupport){
                $sidehtml.= "<a class='blog-staff-profile-link' href='" . $profile_url . "' title='".esc_attr($user->display_name)."'>".__('Staff profile','govintranet')."</a><br>";
            }
            $sidehtml.= "<a class='blog-author-link'  href='".site_url()."/author/";
			$sidehtml.= $user->user_nicename . "/' title='".esc_attr($user->display_name)."'>".__('Blog posts','govintranet')."</a><br class='blog-author-link'>";
			$sidehtml.= "</div></div></div></div>";
			echo $sidehtml;
			
			get_template_part("part", "sidebar");

		 	dynamic_sidebar('blog-widget-area'); 

			get_template_part("part", "related");

			wp_reset_query();
			$post_cat = get_the_terms($post->ID,'blog-category');
			if ($post_cat){
				$html = '';
				$catTitlePrinted = false;
				foreach($post_cat as $cat){
				if ( $cat->term_id > 0 ){
					if ( !$catTitlePrinted ){
						$catTitlePrinted = true;
					}
					$html.= "<span><a class='wptag t".$cat->term_id."' href='".get_term_link($cat->slug , 'blog-category') . "'>".str_replace(" ","&nbsp;",$cat->name)."</a></span> ";
					}
				}	
				if ( $html ){
					echo "<div class='widget-box'><h3>" . __('Categories' , 'govintranet') . "</h3>".$html."</div>";
				}
			}
		 	wp_reset_query();
		 	
			$posttags = get_the_tags();
			$blogtags = array();
			if ( $posttags ) {
				$foundtags = false;	
				$tagstr = "";
			  	foreach( $posttags as $tag ) {
		  			$foundtags = true;
		  			$tagurl = $tag->term_id;
		  			$blogtags[] = $tagurl;
			    	$tagstr.= "<span><a class='label label-default' href='".get_tag_link($tagurl) . "?type=blog'>" . str_replace(' ', '&nbsp' , $tag->name) . '</a></span> '; 
			  	}
			  	if ( $foundtags ){
				  	echo "<div class='widget-box'><h3>" . __('Tags','govintranet') . "</h3><p> "; 
				  	echo $tagstr;
				  	echo "</p></div>";
			  	}
			}
		 	
			/* AUTORELATED */
			
			$alreadydone = array($mainid);
			$autos_to_show = 5;
			$final_cut = array();
			$relateditems = new WP_Query();
			$blog_categories = array();
			
			// BOTH CATEGORIES AND TAGS
			
			if ( $post_cat && $posttags ):
				foreach ( $post_cat as $cat){
					$blog_categories[] = $cat->term_id;
				}
				add_filter('pre_get_posts', 'filter_blogs');
				$relateditems = new WP_Query(array(
					'post_type' => 'blog',
					'posts_per_page' => $autos_to_show,
					'no_found_rows' => true,
					'post__not_in' => $alreadydone,
					'tax_query' => array(
						'relation' => 'AND',
						array(
						'taxonomy' => 'blog-category',
						'field' => 'id',
						'terms' => (array)$blog_categories,
						'operator' => 'IN'
						),
						array(
						'taxonomy' => 'post_tag',
						'field' => 'term_id',
						'terms' => $blogtags,
						'operator' => 'IN'
						),
						),
					));			
				remove_filter('pre_get_posts', 'filter_blogs');
			endif;
			
			// JUST CATEGORY or JUST TAG
			
			if ( !$relateditems->have_posts() && $post_cat ):
				foreach ( $post_cat as $cat){
					$blog_categories[] = $cat->term_id;
				}
				$relateditems = new WP_Query(array(
					'post_type' => 'blog',
					'posts_per_page' => $autos_to_show,
					'no_found_rows' => true,
					'post__not_in' => $alreadydone,
					'tax_query' => array(array(
					    'taxonomy' => 'blog-category',
					    'field' => 'id',
					    'terms' => $blog_categories,
					    'compare' => "IN",
						))
					));			
			elseif ( !$relateditems->have_posts() && $posttags ):
				add_filter('pre_get_posts', 'filter_blogs');
				$relateditems = new WP_Query(array(
					'post_type' => 'blog',
					'posts_per_page' => $autos_to_show,
					'no_found_rows' => true,
					'post__not_in' => $alreadydone,
					'tax_query' => array(array(
				    'taxonomy' => 'post_tag',
				    'field' => 'id',
				    'terms' => (array)$blogtags,
				    'compare' => "IN",
					))
					));			
				remove_filter('pre_get_posts', 'filter_blogs');
			endif;

			if ( $relateditems->have_posts() ) :
				while ( $relateditems->have_posts() ) : 
					$score = 0;
					$relateditems->the_post(); 
					if ($mainid!=$post->ID) {
						$candidate_terms = get_the_terms($post->ID, 'blog-category');
						if ( $candidate_terms ) foreach ( $candidate_terms as $t ){
							if ( in_array($t->term_id, $blog_categories) ) $score++;
						}
						$candidate_tags = get_the_tags($post->ID);
						if ( $candidate_tags ) foreach ( $candidate_tags as $t ){
							if ( in_array($t->term_id, $blogtags) ) $score++;
						}
						if ( $post->post_author == $user->ID ) $score++;
						$final_cut[] = array( 'score'=>$score, 'date'=>$post->post_date, 'ID' => $post->ID, 'post_author'=>$post->post_author );
						$alreadydone[] = $post->ID;
						$autos_to_show--;
					}
				endwhile;
			endif;

			$related_title = __("Recommended","govintranet");
			
			// get most recent posts for this author
			if ( $autos_to_show ):
				$recent_author = new WP_Query(array(
					'post_type' => 'blog',
					'posts_per_page' =>$autos_to_show,
					'no_found_rows' => true,
					'post__not_in' => $alreadydone,
					'author' => $user->ID,
					));			
	
				if ( $recent_author->have_posts() ):
					while ( $recent_author->have_posts() ) : 
						$recent_author->the_post(); 
						if ($mainid!=$post->ID) {
							$final_cut[] = array( 'score'=>1, 'date'=>$post->post_date, 'ID' => $post->ID, 'post_author'=>$post->post_author );
							$alreadydone[] = $post->ID;
							$autos_to_show--;
						}
					endwhile; 
				endif;
			endif;
			
			/* If nothing found, show recent */
			$recentitems = new WP_Query();
			if ( 5 == $autos_to_show ):
				$recentitems = new WP_Query(array(
				'post_type'=>'blog',
				'posts_per_page' => $autos_to_show,
				'no_found_rows' => true,
				'post__not_in' => $alreadydone,
				));	
			endif;

			if ( 5 == $autos_to_show ) $related_title = __("Recent","govintranet");

			if ( $recentitems->have_posts() ) :
				while ( $recentitems->have_posts() ) : 
					$recentitems->the_post(); 
					if ($mainid!=$post->ID) {
						$final_cut[] = array( 'score'=>0, 'date'=>$post->post_date, 'ID' => $post->ID, 'post_author'=>$post->post_author );
						$alreadydone[] = $post->ID;
						$autos_to_show--;
					}
				endwhile;
			endif;

			array_multisort($final_cut, SORT_DESC);
			$html = "";
			if ( $final_cut ) :
				$html.= "<div class='widget-box nobottom'>";
				$html.= "<h3>" . $related_title . "</h3>";
				foreach ( $final_cut as $slot ) { 
					$post = get_post($slot['ID']);
					setup_postdata($post);
					if ($mainid != $slot['ID']) {
						$thistitle = get_the_title($slot['ID']);
						$thisURL = get_permalink($slot['ID']);
						$thisdate = $slot['date'];
						$thisdate = date(get_option('date_format'),strtotime($thisdate));
						$html.= "<div class='widgetnewsitem'>";
						$image_url = get_the_post_thumbnail($slot['ID'], 'thumbnail', array('class' => 'alignright'));
						$html.= "<h3><a href='{$thisURL}'>".$thistitle."</a></h3>";
						$html.= "<span class='news_date'>".$thisdate."</span>&nbsp;";
						$user = get_userdata($slot['post_author']);
						$gis = "options_forum_support";
						$staffdirectory = get_option('options_module_staff_directory');
						$user_info = get_userdata($slot['post_author']);
						$displayname = get_user_meta($slot['post_author'] ,'first_name',true )." ".get_user_meta($slot['post_author'] ,'last_name',true );							
						$forumsupport = get_option($gis);
						$html.= "<span class='nowrap'>";
						if ($forumsupport){	
							$profile_url = gi_get_user_url($slot['post_author']); 
					 		$authorlink = "<a href='".$profile_url . "'>";
							$html.= $authorlink;
							$directorystyle = get_option('options_staff_directory_style'); // 0 = squares, 1 = circles
							$avstyle = "";
							if ( $directorystyle==1 ) $avstyle = " img-circle";
							$image_url = get_avatar($slot['post_author'] , 32);
							$image_url = str_replace(" photo", " photo ".$avstyle, $image_url);
							$html.= $image_url;
							$html.= "</a>&nbsp;";
							$html.= $authorlink;
							$auth = get_the_author();
							$html.= "<span class='listglyph'>".$auth."</span>";
							$html.= "</a> ";
						} else {
			                $html.= " <a href='".site_url()."/author/" . $user->user_nicename . "/'>" . $user->display_name . "</a>";						   	}
					   	$html.= "</span>";
						$html.= "<br>".get_the_excerpt()."<br><span class='news_date'><a class='more' href='{$thisURL}' title='".esc_attr(get_the_title())."'>" . __('Read more' , 'govintranet') . "</a></span></div><div class='clearfix'></div><hr class='light' />";
					}
				}; 
				$html.= "</div>";
			endif;
			echo $html;
			?>
		</div> <!--end of second column-->
			
<?php endwhile; // end of the loop. 

add_filter('pre_get_posts', 'ht_filter_search');

wp_reset_query();

get_footer(); ?>
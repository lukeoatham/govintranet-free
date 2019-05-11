<?php
/*
Template Name: Hashtags
*/

get_header(); 

remove_filter('pre_get_posts', 'ht_filter_search');

$header_text_color = get_option('options_btn_text_colour','#ffffff');
$custom_css = "
.metro-list .featuredpage{
	background:".get_option('header_background','#0b2d49').";
	padding:0;
	color:".$header_text_color." !important;
}
.metro-list .featuredpage p, .metro-list .featuredpage h4{
	color:".$header_text_color." !important;
	padding:0 16px;
}
.metro-list .featuredpage a h4{
	color:".$header_text_color." !important;
	padding:8px 16px 0;
}
";			
wp_enqueue_style( 'hashtags', plugin_dir_url('/') . 'ht-hashtags/css/hashtags.min.css' );
wp_add_inline_style( 'hashtags' , $custom_css );
wp_enqueue_script( 'masonry' );
wp_enqueue_script( 'imagesloaded' );
wp_enqueue_script( 'ht_hashtags', plugin_dir_url('/') . 'ht-hashtags/js/ht_hashtags.js' );

if ( have_posts() ) : 
	while ( have_posts() ) : the_post(); ?>
<div class="col-sm-12 white">	
	<div class="row">
		<div class='breadcrumbs'>
			<?php if(function_exists('bcn_display') && !is_front_page()) {
				bcn_display();
				}?>
		</div>
	</div>

	<?php if (get_the_content()) : ?>
		<?php the_content(); ?>
	<?php endif; ?>
</div>

<div class="row">		
	<div class="col-lg-12 col-md-12 col-sm-12">		
		<div id="grimason">
		<?php
		$pid = $post->ID;
		$html = '';
		$hashtag = '';
		$hashterm = '';
		$hashtagslug = '';
		$counter = 0;
		$grids = array();
		$numnews = get_post_meta($pid, 'ht_number_of_news_stories',true);
		if (!$numnews) $numnews = 12;
		$hashtag = get_post_meta($pid, 'ht_hashtag',true);
		$hashterm = get_tag($hashtag);
		$hashtagslug = $hashterm->slug;
		$hashtagid = $hashterm->term_id;
		$promos = get_post_meta($pid,'ht_highlight_pages',true); 

		// get news
		if ( $hashtagslug ) $homefeed = get_posts(array(
				'post_type' => array('news','blog','news-update','event','tribe_event'),
				"posts_per_page"=>$numnews,
				"post_status"=>"publish",
				'tax_query' => array(array(
					'terms' => $hashtagslug,	
					'field' => 'slug',
					'taxonomy' => 'post_tag'
				)),
				'post__not_in' => $promos,
			));
	
		if ( $homefeed ) foreach ($homefeed as $h){
			$html='';
			$html.="<div class='w2 grid-item'>";
			$html.="<div class='inner-grid anarticle'>";
			$html.="<div class='grid-footer'>";
			$html.="<p class='type-icon hashtag-link'><a href='" . get_tag_link( $hashtagid ) . "'>#" . $hashtagslug . "</a></p>";	
			setup_postdata($h);
			if ( get_comments_number($h->ID) ){
				$html.= "<a class='hashtags-comments' href='".get_permalink($h->ID)."#comments'>";
				$html.= '<span class="badge badge-comment">' . sprintf( _n( '1 comment', '%d comments', get_comments_number($h->ID), 'govintranet' ), get_comments_number($h->ID) ) . '</span>';
				 $html.= "</a>";
			}
			$html.="</div>";

			$img = wp_get_attachment_image_src(get_post_thumbnail_id($h->ID),'large');
			$video = 0;
			if ( has_post_format('video', $h->ID) || has_post_format('audio', $h->ID) ):
				$video_embed= '<div class="embed-container">';
				$video_embed.= get_field( 'news_video_url', $h->ID);
				$video_embed.= '</div>';
				$video = 1;
			endif;
			if ( $video ){
				$html.=$video_embed;
				$html.="<a href='".get_permalink($h->ID)."'>";
				$html.="<h4>".get_the_title($h->ID)."</h4></a>";
			} else {
				$html.="<a href='".get_permalink($h->ID)."'>";
				if ( $img ) $html.="<img src='".$img[0]."' class='img img-responsive' width='".$img[1]."' height='".$img[2]."' alt='' />";
				$html.="<h4>".get_the_title($h->ID)."</h4></a>";
			}
			if ( 'blog' == $h->post_type  ) { 
	            $gis = "options_module_staff_directory";
				$forumsupport = get_option($gis); 
				$user_info = get_userdata($h->post_author);
				$displayname = get_user_meta($h->post_author ,'first_name',true )." ".get_user_meta($h->post_author ,'last_name',true );		
				$directorystyle = get_option('options_staff_directory_style'); // 0 = squares, 1 = circles
				$avstyle = "";
				if ( $directorystyle==1 ) $avstyle = " img-circle";
				$image_url = get_avatar($h->post_author , 36, "", $displayname); 
				$image_url = str_replace(" photo", " photo ".$avstyle, $image_url);
				$image_url = str_replace('"150"', '"36"', $image_url);
				$image_url = str_replace("'150'", "'36'", $image_url);
				$image_url = str_replace("alignnone", "", $image_url);
				$html.="<p class='user-icon'>";
	            $html.= $image_url;
	            $html.= "<span class='user-icon-text'>" . $displayname . "</span>";
	            $html.="</p>";
			}
			if ( trim($h->post_excerpt) ) $html.="<p class='grid-excerpt'>".wp_trim_words($h->post_excerpt, 20)."</p>";
			$html.="</div></div>";
			$grids[]=$html;
		}
		
		// load manual entries
		$extragrids = array();
		if ( $promos ) foreach ($promos as $p){ 
			$html='';
			$html.= "<div class='w2 grid-item'>";
			$html.="<div class='inner-grid anarticle featuredpage'>";
			$manualpost = get_post($p);
			$img = wp_get_attachment_image_src(get_post_thumbnail_id($manualpost->ID),'large');
			$video = 0;
			if ( has_post_format('video', $manualpost->ID) || has_post_format('audio', $manualpost->ID) ):
				$video_embed= '<div class="embed-container">';
				$video_embed.= get_field( 'news_video_url', $manualpost->ID);
				$video_embed.= '</div>';
				$video = 1;
			endif;
			if ( $video ){
				$html.=$video_embed;
				$html.="<a href='".get_permalink($manualpost->ID)."'>";
				$html.="<h4>".get_the_title($manualpost->ID)."</h4></a>";
			} else {
				$html.="<a href='".get_permalink($manualpost->ID)."'>";
				if ( $img ) $html.="<img src='".$img[0]."' class='img img-responsive' width='".$img[1]."' height='".$img[2]."' alt='' />";
				$html.="<h4>".get_the_title($manualpost->ID)."</h4></a>";
			}
			if ( 'blog' == get_post_type( $p )  ) { 
	            $gis = "options_module_staff_directory";
				$forumsupport = get_option($gis); 
				$user_info = get_userdata($manualpost->post_author);
				$displayname = get_user_meta($manualpost->post_author,'first_name',true )." ".get_user_meta($manualpost->post_author,'last_name',true );		
				$directorystyle = get_option('options_staff_directory_style'); // 0 = squares, 1 = circles
				$avstyle = "";
				if ( $directorystyle==1 ) $avstyle = " img-circle";
				$image_url = get_avatar($manualpost->post_author, 36, "", $displayname); 
				$image_url = str_replace(" photo", " photo ".$avstyle, $image_url);
				$image_url = str_replace('"150"', '"36"', $image_url);
				$image_url = str_replace("alignnone", "", $image_url);
				$image_url = str_replace("'150'", "'36'", $image_url);
				$html.="<p class='user-icon'>";
	            $html.= $image_url;
	            $html.= "<span class = 'user-icon-text'>" . $displayname . "</span>";
	            $html.="</p>";
			}
			
			if ( trim($manualpost->post_excerpt) ) $html.="<p class='grid-excerpt-manual'>".wp_trim_words($manualpost->post_excerpt, 20)."</p>";
			if ( get_comments_number($manualpost->ID) ){
				$html.= "<a class='hashtag-manual-comments' href='".get_permalink($manualpost->ID)."#comments'>";
				$html.= '<span class="badge badge-comment">' . sprintf( _n( '1 comment', '%d comments', get_comments_number($manualpost->ID), 'govintranet' ), get_comments_number($manualpost->ID) ) . '</span>';
				 $html.= "</a>";
			}
			$html.= "</div></div>";
			$extragrids[]=$html;	
		}

		// load spots
		$promos = get_post_meta($pid,'ht_spots',true); //print_r($promos);
		if ( $promos ) foreach ($promos as $p){ //echo $p['ID'];
			$html='';
			$html.= "<div class='w2 grid-item'>";
			$html.="<div class='inner-grid anarticle featured-spot'>";
			$spot = get_post($p);
			$html.= apply_filters('the_content',$spot->post_content);
			$html.= "</div></div>";
			$extragrids[]=$html;	
		}

		// attempt to spread extra items evenly through posts
		$fill = 2;
		$gridcount = count($grids);
		$extracount = count($extragrids);
		if ( ( $gridcount && $extracount ) && ( $gridcount > $extracount ) ) $fill = intval( $gridcount / $extracount );
		if ( $fill < 2 ) $fill = 2;
		$count = $fill * -1;
		if ( count($extragrids) > 0 ) foreach ($extragrids as $e){ 
			$count=$count+$fill;
			array_splice($grids,$count,0,$e); // insert in slot
		}

		$html =  implode("",$grids);
		$output = '<div class="metro-list">'.$html.'</div>';
		echo $output;
		add_filter('pre_get_posts', 'ht_filter_search');
		?>
		</div>
	</div>
</div>
<?php endwhile; ?>
<?php endif; ?> 
<?php get_footer(); ?>
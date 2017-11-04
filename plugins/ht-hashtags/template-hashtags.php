<?php
/*
Template Name: Hashtags
*/

get_header(); 

$header_text_color = get_header_textcolor();
if ( !$header_text_color ) $header_text_color = "FFFFFF";
$custom_css = "
.metro-list .featuredpage {
	background: ".get_theme_mod('header_background', '0b2d49').";
	padding: 0;
	color: #".$header_text_color." !important;
}
.metro-list .featuredpage p, .metro-list .featuredpage h4 {
	color: #".$header_text_color." !important;
	padding: 0 1em;
}
.metro-list .featuredpage a h4 {
	color: #".$header_text_color." !important;
	padding: 0 1em;
}

";			
wp_enqueue_style( 'hashtags', plugin_dir_url('/') . 'ht-hashtags/css/hashtags.css' );
wp_add_inline_style('hashtags' , $custom_css);
wp_enqueue_script( 'masonry');
wp_enqueue_script( 'imagesloaded');
wp_enqueue_script( 'ht_hastags', plugin_dir_url('/') . 'ht-hashtags/js/ht_hashtags.js' );

if ( have_posts() ) : 
	while ( have_posts() ) : the_post(); ?>
<div class="row">
	<div class="col-lg-12 white ">
		<div class='breadcrumbs'>
			<?php if(function_exists('bcn_display') && !is_front_page()) {
				bcn_display();
				}?>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-xs-12">

		<?php if (the_content()) : ?>
		<div class="lead">  
			<?php the_content(); ?>
		</div>
		<?php endif; ?>
		
		<div id="grimason">
		<?php

		$pid = $post->ID;
		
		//retrieve latest media feed posts
	
		$html ='';
		$counter=0;
		$grids=array();
		
		$numnews = get_post_meta($pid, 'ht_number_of_news_stories',true);
		if (!$numnews) $numnews = 10;
		$hashtag = get_post_meta($pid, 'ht_hashtag',true);
		$hashterm = get_tag($hashtag);
		$hashtagslug = $hashterm->slug;
		$promos = get_post_meta($pid,'ht_highlight_pages',true); 


		// get news

		$homefeed = get_posts(array(
				'post_type' => array('news','blog','news-update','event','tribe_event'),
				"posts_per_page"=>$numnews,
				"post_status"=>"publish",
				'tag' => $hashtagslug,
				'post__not_in' => $promos,
			
			));
	
		if ( $homefeed ) foreach ($homefeed as $h){
			$html='';
			$html.="<div class='w2 grid-item'>";
			$html.="<div class='inner-grid anarticle'>";
			$html.="<div class='grid-footer'>";
			$html.="<p class='type-icon'><a href='/tag/".$hashtagslug."'>#".$hashtagslug."</a></p>";	
			setup_postdata($h);
			$html.="</div>";
			$img = wp_get_attachment_image_src(get_post_thumbnail_id($h->ID),'homepage');
			$html.="<a href='".get_permalink($h->ID)."'>";
			if ( $img ) $html.="<img src='".$img[0]."' class='img img-responsive' width='".$img[1]."' height='".$img[2]."' alt='' />";
			$html.="<h4>".$h->post_title."</h4></a>";
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
				$html.="<p class='user-icon'>";
	            $html.= $image_url;
	            $html.= "<span class = 'user-icon-text'>" . $displayname . "</span>";
	            $html.="</p>";
			}
			$html.="<p class='grid-excerpt'>".wp_trim_words($h->post_excerpt, 20)."</p>";
			$html.="</div></div>";
			$grids[]=$html;
		}
		
		// load manual entries
		$extragrids = array();
		if ( $promos ) foreach ($promos as $p){ 
			$html='';
			$html.= "<div class='w2 grid-item'>";
			$html.="<div class='inner-grid anarticle featuredpage'>";
			$html.="<a href='".get_permalink($p)."'>";
			$html.= get_the_post_thumbnail($p, 'large', array('class'=>'img-responsive'));
			$html.="<h4>".get_the_title($p)."</h4>";
			$html.="</a>";
			$manualpost = get_post($p);
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
				$image_url = str_replace("'150'", "'36'", $image_url);
				$html.="<p class='user-icon'>";
	            $html.= $image_url;
	            $html.= "<span class = 'user-icon-text'>" . $displayname . "</span>";
	            $html.="</p>";
			}
			
			$html.="<p class='grid-excerpt-manual'>".wp_trim_words($manualpost->post_excerpt, 20)."</p>";
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
			$html.= $spot->post_content;
			$html.= "</div></div>";
			$extragrids[]=$html;	
		}
	
		shuffle($grids);
		$count=-4;
		foreach ($extragrids as $e){ 
			$count=$count+4;
			array_splice($grids,$count,0,$e); // insert in slot
		}

		$html =  implode("",$grids);
		
		$output = '<div class="metro-list">'.$html.'</div>';
		echo $output;
		?>
		</div>
	</div>
</div>

<?php endwhile; ?>
<?php endif; ?> 
<?php get_footer(); ?>
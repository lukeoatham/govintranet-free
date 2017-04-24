<?php
/**
 * The Template for displaying all single posts.
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

function filter_news($query) {
    if ($query->is_tag && !is_admin()) {
		$query->set('post_type', array('news'));
    }
    return $query;
}; 

get_header(); 

remove_filter('pre_get_posts', 'ht_filter_search');
?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

			<div class="col-lg-7 col-md-8 col-sm-8 white ">
				<div class="row">
					<div class='breadcrumbs'>
						<?php if(function_exists('bcn_display') && !is_front_page()) {
							bcn_display();
							}?>
					</div>
				</div>
				<?php 
				$video=null;
				//check if a video thumbnail exists, if so we won't use it to display as a headline image
				if (function_exists('get_video_thumbnail')){
					$video = get_video_thumbnail(); 
				}

				if (!$video){
					$img_srcset = wp_get_attachment_image_srcset( get_post_thumbnail_id( $post->ID ), array('newshead','large','medium','thumbnail') );
					$img_sizes = wp_get_attachment_image_sizes(get_post_thumbnail_id( $post->ID ), 'newshead' ); 
					if (has_post_thumbnail($post->ID)){
						echo get_the_post_thumbnail($post->ID, 'newshead', array('class'=>'img-responsive'));
						echo wpautop( "<p class='news_date'>".get_post_thumbnail_caption()."</p>" );
					} 
				}
				?>

				<h1><?php the_title(); ?></h1>
				<?php
				$article_date=get_the_date();
				$mainid=$post->ID;
				$article_date = date(get_option('date_format'),strtotime($article_date));	?>
				<?php echo the_date(get_option('date_format'), '<p class="news_date">', '</p>') ?>
				<?php 
					if ( has_post_format('video', $post->ID) ):
						echo apply_filters('the_content', get_post_meta( $post->ID, 'news_video_url', true));
					endif;
					?>
				<?php the_content(); ?>
				<?php get_template_part("part", "downloads"); ?>			
				<?php
				if ('open' == $post->comment_status) {
					 comments_template( '', true ); 
				}
			 ?>

		</div> <!--end of first column-->
		<div class="col-lg-4  col-md-4 col-sm-4 col-lg-offset-1" id="sidebar">	
				<?php

				get_template_part("part", "sidebar");
	 	
				dynamic_sidebar('news-widget-area'); 

				get_template_part("part", "related");

				$post_cat = get_the_terms($post->ID,'news-type');
				if ($post_cat){
					$html='';
					$catTitlePrinted=false;
					foreach($post_cat as $cat){
					if ( $cat->term_id > 1 ){
						if ( !$catTitlePrinted ){
							$catTitlePrinted = true;
						}
						$html.= "<span><a class='wptag t".$cat->term_id."' href='".get_term_link($cat->slug , 'news-type') . "'>".str_replace(" ","&nbsp;",$cat->name)."</a></span> ";
						}
					}	
					if ( $html ){
						echo "<div class='widget-box'><h3>" . __('Categories' , 'govintranet') . "</h3>".$html."</div>";
					}
					$html='';
				}
				$posttags = get_the_tags();
				if ( $posttags ) {
					$foundtags=false;	
					$tagstr="";
				  	foreach( $posttags as $tag ) {
			  			$foundtags=true;
			  			$tagurl = $tag->term_id;
				    	$tagstr=$tagstr."<span><a class='label label-default' href='".get_tag_link($tagurl) . "?type=news'>" . str_replace(' ', '&nbsp' , $tag->name) . '</a></span> '; 
				  	}
				  	if ( $foundtags ){
					  	echo "<div class='widget-box'><h3>" . __('Tags' , 'govintranet') . "</h3><p> "; 
					  	echo $tagstr;
					  	echo "</p></div>";
				  	}
				}

		 	wp_reset_postdata();
		 	
			/*****************
			
			AUTOMATED RELATED NEWS
			
			Show 5 latest news stories, excluding the current post and any posts already manually entered as related 
			If this post is a need to know story, show other need to know stories.
			Otherwise check for recent news stories in the same categories as this post.
			If still nothing found, show the latest news stories excluding need to know items
				
			******************/
	
		 	// get meta to use for displaying related news

		 	$alreadydone[] = $post->ID;
			$update = has_post_format( 'status' , $post->ID ); 
			$newstype = get_the_terms( $post->ID , 'news-type' ); 
			$ntags = array();
			$nidtags = array();
			$newstags = get_the_tags( $post->ID); 
			$recentitems = new WP_Query(); 

			if ($newstype):
				$terms = array();
				foreach ( $newstype as $n ){
					$terms[] = $n->slug;
				}
			endif;
			
			if ($newstags):
				foreach ( $newstags as $n ){
					$ntags[] = $n->slug; 
					$nidtags[] = $n->term_id;
				}
			endif;

			// try to find other need to know stories
			$subhead = __('Other updates', 'govintranet');
			if ($update) { 
				$recentitems = new WP_Query(array(
					'post_type'	=>	'news',
					'posts_per_page'	=>	5,
					'post__not_in'	=> $alreadydone,
					'tax_query' => array(array(
						'taxonomy' => 'post_format',
						'field' => 'slug',
						'terms' => 'post-format-status'
						)),
					 ) );			
			}
			
			if ( $recentitems->found_posts == 0 && isset($terms) && isset($ntags) && $terms && $ntags ):
			// no need to know stories so we'll look for others with the same tags AND category
				add_filter('pre_get_posts', 'filter_news');
				$subhead = __('Similar news','govintranet');
				$recentitems = new WP_Query(array(
						'post_type'	=>	'news',
						'posts_per_page'	=>	5,
						'post__not_in'	=> $alreadydone,
						'tax_query' => array(
							'relation' => 'AND',
							array(
							'taxonomy' => 'news-type',
							'field' => 'slug',
							'terms' => $terms,
							'operator' => 'IN'
							),
							array(
							'taxonomy' => 'post_tag',
							'field' => 'term_id',
							'terms' => $nidtags,
							'operator' => 'IN'
							),
							),
						 ) );		
				remove_filter('pre_get_posts', 'filter_news');
			endif;			
			
			if ( $recentitems->found_posts == 0 && isset($ntags) && $ntags ): 
			// no stories with same tags and cats so we'll look for others with just the same tags
				add_filter('pre_get_posts', 'filter_news');
				$subhead = __('Similar news' , 'govintranet');
				$recentitems = new WP_Query(array(
						'post_type'	=>	'news',
						'posts_per_page'	=>	5,
						'post__not_in'	=> $alreadydone,
						'tax_query' => array(
							array(
							'taxonomy' => 'post_tag',
							'field' => 'term_id',
							'terms' => $nidtags,
							'operator' => 'IN'
							),
							),
						 ) );			
				remove_filter('pre_get_posts', 'filter_news');
			endif;			
			
			if ( $recentitems->found_posts == 0 && isset($terms) && $terms ): 
			// still nothing found, we'll look for other stories in the same news categories as this story
				$subhead = __('Other related news','govintranet');
				if ($newstype):
					$recentitems = new WP_Query(array(
						'post_type'	=>	'news',
						'posts_per_page'	=>	5,
						'post__not_in'	=> $alreadydone,
						'tax_query' => array(array(
							'taxonomy' => 'news-type',
							'field' => 'slug',
							'terms' => $terms,
							)),
						 ) );	
				endif;
			endif;
			
			if ( $recentitems->found_posts == 0 ): 
			// still nothing found, we'll load the latest 5 stories excluding any need to know
				$subhead = __('Recent news','govintranet');
				$recentitems = new WP_Query(array(
					'post_type'	=>	'news',
					'posts_per_page'	=>	5,
					'post__not_in'	=> $alreadydone,
					'tax_query' => array(array(
						'taxonomy' => 'post_format',
						'field' => 'slug',
						'terms' => 'post-format-status',
						'operator' => 'NOT IN',
						)),
					 ) );			
			endif;

			// If something to show, create HTML

			if ( $recentitems->have_posts() ):
				$html = "";
				$html.= "<div class='widget-box nobottom'>";
				$html.= "<h3>".$subhead."</h3>";
				while ( $recentitems->have_posts() ) : $recentitems->the_post(); 
					if ($mainid!=$post->ID) {
						$thistitle = get_the_title($id);
						$thistitleatt = the_title_attribute('echo=0');
						$thisURL = get_permalink($id);
						$html.= "<div class='widgetnewsitem'>";
						$image_url = get_the_post_thumbnail($id, 'thumbnail', array('class' => 'alignright'));
						$html.= "<h3><a href='{$thisURL}'>".$thistitle."</a></h3>";
						$thisdate = $post->post_date;
						$thisdate = date(get_option('date_format'),strtotime($thisdate));
						$html.= "<span class='news_date'>".$thisdate;
						$html.= "</span><br>".get_the_excerpt()."<br><span class='news_date'><a class='more' href='{$thisURL}' title='{$thistitleatt}' >" . __('Read more' , 'govintranet') . "</a></span></div><div class='clearfix'></div><hr class='light' />";
					}
				endwhile; 
				$html.= "</div>";
				echo $html;
			endif;

			add_filter('pre_get_posts', 'ht_filter_search');
			wp_reset_query();
			?>
		</div> <!--end of second column-->
			
<?php endwhile; // end of the loop. ?>
<script>
	if (location.protocol === 'https:') {
		var contype = 1;
	}else{
		var contype = 0;
	}
	setCookie('ht_need_to_know_<?php echo $post->ID; ?>','closed','10080','/',0,contype); 
</script>

<?php get_footer(); ?>
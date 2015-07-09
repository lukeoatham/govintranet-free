<?php
/**
 * The Template for displaying all update posts.
 *
 * @package WordPress
 */


function filter_news($query) {
    if ($query->is_tag && !is_admin()) {
		$query->set('post_type', array('news-update'));
    }
    return $query;
}; 

get_header(); 

remove_filter('pre_get_posts', 'filter_search');
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
					$ts = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'newshead' ); 
					$tt = get_the_title();
					$tn = "<img src='".$ts[0]."' width='".$ts[1]."' height='".$ts[2]."' class='img img-responsive' alt='".$tt."' />";
					if ($ts){
						echo $tn;
						echo wpautop( "<p class='news_date'>".get_post_thumbnail_caption()."</p>" );
					}
				}
				?>

				<h1><?php the_title(); ?></h1>
				<?php
				$article_date=get_the_date();
				$mainid=$post->ID;
				$article_date = date("j F Y",strtotime($article_date));	?>
				<?php echo the_date('j M Y', '<p class="news_date">', '</p>') ?>
				<?php 
					if ( has_post_format('video', $post->ID) ):
						echo apply_filters('the_content', get_post_meta( $post->ID, 'news_video_url', true));
					endif;
					?>
				<?php the_content(); ?>
				<?php
				$current_attachments = get_field('document_attachments');
				if ($current_attachments){
					echo "<div class='alert alert-info'>";
					echo "<h3>Downloads <i class='glyphicon glyphicon-download'></i></h3>";
					foreach ($current_attachments as $ca){
						$c = $ca['document_attachment'];
						echo "<p><a class='alert-link' href='".$c['url']."'>".$c['title']."</a></p>";
					}
					echo "</div>";
				}				
				
				if ('open' == $post->comment_status) {
					 comments_template( '', true ); 
				}
			 ?>

		</div> <!--end of first column-->
		<div class="col-lg-4  col-md-4 col-sm-4 col-lg-offset-1">	
			<?php
				$post_cat = get_the_terms($post->ID,'news-update-type');
				if ($post_cat){
					$html='';
					$catTitlePrinted=false;
					foreach($post_cat as $cat){
					if ($cat->term_id){
						if ( !$catTitlePrinted ){
							$catTitlePrinted = true;
						}
						$html.= "<span><a class='wptag t".$cat->term_id."' href='".get_term_link($cat->slug,'news-update-type')."'>".str_replace(" ","&nbsp;",$cat->name)."</a></span> ";
						}
					}	
					if ( $html ){
						echo "<div class='widget-box'><h3>Categories</h3>".$html."</div>";
					}
				}
		 	dynamic_sidebar('news-widget-area'); 
		 	wp_reset_postdata();
			wp_reset_query();
			/*****************
			
			AUTOMATED RELATED POSTS
			
			Show 5 latest news stories, excluding the current post and any posts already manually entered as related 
			If this post is a need to know story, show other need to know stories.
			Otherwise check for recent news stories in the same categories as this post.
			If still nothing found, show the latest news stories excluding need to know items
				
			******************/
	
		 	// get meta to use for displaying related news

		 	$alreadydone[] = $post->ID;
			
			$newstype = get_the_terms( $post->ID , 'news-update-type' ); 
			if ($newstype):
				$terms = array();
				foreach ( $newstype as $n ){
					$terms[] = $n->slug;
				}
			endif;
			
			$recentitems = new WP_Query(); 
			
			// try to find other need to know stories
			$subhead = 'Other updates';
						
			if ( $terms): 
			// still nothing found, we'll look for other stories in the same news categories as this story
				$subhead = 'Related updates';
				if ($newstype): 
					$recentitems = new WP_Query(array(
						'post_type'	=>	'news-update',
						'posts_per_page'	=>	5,
						'post__not_in'	=> $alreadydone,
						'tax_query' => array(array(
							'taxonomy' => 'news-update-type',
							'field' => 'slug',
							'terms' => $terms,
							)),
						 ) );	
				endif;
			endif;
			
			if ( $recentitems->found_posts == 0 ): 
			// still nothing found, we'll load the latest 5 stories excluding any need to know
				$subhead = 'Recent updates';
				$recentitems = new WP_Query(array(
					'post_type'	=>	'news-update',
					'posts_per_page'	=>	5,
					'post__not_in'	=> $alreadydone,
					 ) );			
			endif;

			if ( $recentitems->have_posts() ):
				echo "<div class='widget-box nobottom'>";
				echo "<h3>".$subhead."</h3>";
				while ( $recentitems->have_posts() ) : $recentitems->the_post(); 
					if ($mainid!=$post->ID) {
						$thistitle = get_the_title($id);
						$thisURL=get_permalink($id);
						echo "<div class='widgetnewsitem'>";
						$image_url = get_the_post_thumbnail($id, 'thumbnail', array('class' => 'alignright'));
						echo "<h3><a href='{$thisURL}'>".$thistitle."</a></h3>";
						$thisdate= $post->post_date;
						$thisdate=date("j M Y",strtotime($thisdate));
						echo "<span class='news_date'>".$thisdate;
						echo "</span><br>".get_the_excerpt()."<br><span class='news_date'><a class='more' href='{$thisURL}' title='{$thistitle}' >Read more</a></span></div><div class='clearfix'></div><hr class='light' />";
					}
				endwhile; 
				echo "</div>";
			endif;
			add_filter('pre_get_posts', 'filter_search');
			wp_reset_query();
			?>
		</div> <!--end of second column-->
			
<?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>
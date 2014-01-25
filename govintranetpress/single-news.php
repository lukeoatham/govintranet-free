<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */

get_header(); ?>

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
				<?php the_content(); ?>
				<?php
				if ('open' == $post->comment_status) {
					 comments_template( '', true ); 
				}
			 ?>

		</div> <!--end of first column-->
		<div class="col-lg-4  col-md-4 col-sm-4 col-lg-offset-1">	
			<?php
			$relnews = new Pod('news', $id);
				$related_links = $relnews->get_field('related_stories');
				if ($related_links){
					echo "<div class='widget-box list'>";
					echo "<h3 class='widget-title'>Related news</h3>";
					echo "<ul>";
					foreach ($related_links as $rlink){
						if ($rlink['post_status'] == 'publish' && $rlink['ID'] != $id ) {
							echo "<li><a href='".$rlink['guid']."'>".govintranetpress_custom_title($rlink['post_title'])."</a></li>";
						}
					}
					echo "</ul></div>";
				}
				$post_cat = get_the_category();
				if ($post_cat){
					$html='';
					foreach($post_cat as $cat){
					if ($cat->slug != 'uncategorized'){
						if (!$catTitlePrinted){
							$catTitlePrinted = true;
						}
						$html.= "<span class='wptag t".$cat->term_id."'><a href='".site_url()."/news-by-category/?cat=".$cat->slug."'>".str_replace(" ","&nbsp;",$cat->name)."</a></span> ";
						}
					}	
					if ($html){
						echo "<div class='widget-box'><h3>Categories</h3>".$html."</div>";
					}
				}
				$posttags = get_the_tags();
				if ($posttags) {
					$foundtags=false;	
					$tagstr="";
				  	foreach($posttags as $tag) {
				  		if (substr($tag->name,0,9)!="carousel:"){
				  			$foundtags=true;
				  			$tagurl = $tag->slug;
					    	$tagstr=$tagstr."<span><a class='label label-default' href='".site_url()."/tagged/?tag={$tagurl}&amp;posttype=news'>" . str_replace(' ', '&nbsp' , $tag->name) . '</a></span> '; 
				    	}
				  	}
				  	if ($foundtags){
					  	echo "<div class='widget-box'><h3>Tags</h3><p> "; 
					  	echo $tagstr;
					  	echo "</p></div>";
				  	}
				}
		 	dynamic_sidebar('news-widget-area'); 
		//if we're looking at a news story, show recently published news
			echo "<div class='widget-box nobottom'>";
			$category = get_the_category(); 
			$recentitems = new WP_Query('post_type=news&posts_per_page=5');			
			echo "<h3>Recent news</h3>";
			if ($recentitems->post_count==0 || ($recentitems->post_count==1 && $mainid==$post->ID)){
				echo "<p>Nothing to show yet.</p>";
			}
			if ( $recentitems->have_posts() ) while ( $recentitems->have_posts() ) : $recentitems->the_post(); 
				if ($mainid!=$post->ID) {
					$thistitle = get_the_title($ID);
					$thisURL=get_permalink($ID);
					echo "<div class='widgetnewsitem'>";
					$image_url = get_the_post_thumbnail($ID, 'thumbnail', array('class' => 'alignright'));
					echo "<h3><a href='{$thisURL}'>".$thistitle."</a></h3>";
					$thisdate= $post->post_date;
					$thisdate=date("j M Y",strtotime($thisdate));
					echo "<span class='news_date'>".$thisdate;
					echo "</span><br>".get_the_excerpt()."<br><span class='news_date'><a class='more' href='{$thisURL}' title='{$thistitle}' >Read more</a></span></div><div class='clearfix'></div><hr class='light' />";
				}
			endwhile; 
			echo "</div>";
			wp_reset_query();
				?>
		</div> <!--end of second column-->
			
<?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>
<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */

$slug = pods_url_variable(0);
if ($slug=="homepage"){
	wp_redirect('/');
};
if ($slug=="control"){
	wp_redirect('/');
};

get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

	
	<div class="row">
		<div class="ninecol white" id='content'>
			<div class="content-wrapper">
			<h1><?php the_title(); ?></h1>

			<?php
			$article_date=get_the_date();
			$article_date = date("j F Y",strtotime($article_date));	?>
			<?php echo the_date('j M Y', '<p class=news_date>', '</p>') ?>
			<div id='printsm'>
			<?php
				$posttags = get_the_tags();
				if ($posttags) {
					$foundtags=false;	
					$tagstr="";
				  	foreach($posttags as $tag) {
				  		if (substr($tag->name,0,9)!="carousel:"){
				  			$foundtags=true;
				  			$tagurl = str_replace(' ','-',$tag->name);
					    	$tagstr=$tagstr."<span class='wptag'><a href='/tag/{$tagurl}'>" . str_replace(' ', '&nbsp' , $tag->name) . '</a></span> '; 
				    	}
				  	}
				  	if ($foundtags){
					  	echo "<strong>Tags:</strong> "; 
					  	echo $tagstr;
					  	echo "<br /><br />";
				  	}
				}
				?>						
			</div>

			<?php the_content(); ?>

			<?php
			if ('open' == $post->comment_status) {
				 comments_template( '', true ); 
			}
			 ?>
			</div>
		</div> <!--end of first column-->
		
		<div class="threecol last" id="sidebar">	

			<?php 
			$image_url = get_the_post_thumbnail($ID, 'large');
			$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );
			if ($image_uri!=""){
			echo "<img class='size-full' src='{$image_uri[0]}' alt='".get_the_title()."' />";
			echo wpautop( "<p class='news_date'>".get_post_thumbnail_caption()."</p>" );
			echo "<hr class='light'>";
			}
			?>

		<?php
		
		//if we're looking at a news story, show recently published news

			echo "<div id='newsposts'>";
			$category = get_the_category(); 
			$recentitems = new WP_Query('post_type=post&posts_per_page=5');			
			echo "<h3>Recently published</h3><ul>";

			if ($recentitems->post_count==0 || ($recentitems->post_count==1 && $mainid==$post->ID)){
				echo "<p>Nothing to show yet.</p>";
			}

			if ( $recentitems->have_posts() ) while ( $recentitems->have_posts() ) : $recentitems->the_post(); 
				if ($mainid!=$post->ID) {
					$thistitle = get_the_title($ID);
					$thisURL=get_permalink($ID);
					echo "<div class='newsitem'>";
					$image_url = get_the_post_thumbnail($ID, 'thumbnail', array('class' => 'alignright'));
					echo "<p class='avatar alignright'><a href='{$thisURL}'>".$image_url."</a><a href='{$thisURL}'><h3>".$thistitle."</h3></a>";
					$thisdate= $post->post_date;
					$thisdate=date("j M Y",strtotime($thisdate));
					echo "<span class='news_date'>".$thisdate;
					echo "</span></p><p class='news_date'><a class='more' href='{$thisURL}' title='{$thistitle}' >Read more</a></p></div><div class='clearfix'></div><hr class='light' />";
				}
			endwhile; 

			echo "</ul>

					
			</div>";
			

			wp_reset_query();
				?>

			</div> <!--end of second column-->


				
	</div> 

			
<?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>
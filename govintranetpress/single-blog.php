<?php
/**
 * The Template for displaying all single blogposts.
 *
 * @package WordPress
 */

get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
		
		<div class="col-lg-8 col-md-8 col-sm-8 white ">
				<div class="row">
					<div class='breadcrumbs'>
						<?php if(function_exists('bcn_display') && !is_front_page()) {
							bcn_display();
							}?>
					</div>
				</div>
<?php 
				$image_uri =  wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'newshead' );
				if ($image_uri!=""){
					echo "<img class='img img-responsive' src='{$image_uri[0]}' width='{$image_uri[1]}' height='{$image_uri[2]}' alt='".get_the_title()."' />";
					echo wpautop( "<p class='news_date'>".get_post_thumbnail_caption()."</p>" );
				}
				?>
				<h1><?php the_title(); ?></h1>
				<?php
				$article_date=get_the_date();
				$mainid=$post->ID;
				$article_date = date("j F Y",strtotime($article_date));	?>
				<?php echo the_date('j M Y', '<p class=news_date>', '</p>') ?>
				<?php the_content(); ?>
				<?php
				if ('open' == $post->comment_status) {
					 comments_template( '', true ); 
				}
			 ?>

		</div> <!--end of first column-->
		<div class="col-lg-4  col-md-4 col-sm-4 last">	
			<?php
            $user = get_userdata($post->post_author);
            echo "<div class='well'><div class='media'>";
            
            $gis = "general_intranet_forum_support";
			$forumsupport = get_option($gis); //echo $forumsupport;
			if ($forumsupport){
                echo "<a class='pull-left' href='".site_url()."/staff/" . $user->user_login . "/'>";
            } else {
                echo "<a class='pull-left' href='".site_url()."/author/" . $user->user_login . "/'>";	                        
            }
            echo get_avatar($user->ID, 96);
            echo "</a>";
            echo "<div class='media-body'><p class='media-heading'>";
            echo "<strong>".$user->display_name."</strong><br>";                        
            $jobtitle = get_user_meta($user->ID, 'user_job_title',true);
            $bio = get_user_meta($user->ID,'description',true);                        
			echo "<strong>".$jobtitle."</strong><br>";
            if ($forumsupport){
                echo "<a href='".site_url()."/staff/";
				echo $user->user_login . "/' title='{$user->display_name}'>Staff profile</a><br>";
            }
            echo "<a href='".site_url()."/author/";
			echo $user->user_login . "/' title='{$user->display_name}'>Blog posts</a><br>";
//						echo "<br>".$bio;
			echo "</div></div></div>";
			
			$relnews = new Pod('blog', $id);
				$posttags = get_the_tags();
				if ($posttags) {
					$foundtags=false;	
					$tagstr="";
				  	foreach($posttags as $tag) {
				  		if (substr($tag->name,0,9)!="carousel:"){
				  			$foundtags=true;
				  			$tagurl = $tag->slug;
					    	$tagstr=$tagstr."<span><a class='label label-default' href='".site_url()."/tagged/?tag={$tagurl}&amp;posttype=blog'>" . str_replace(' ', '&nbsp' , $tag->name) . '</a></span> '; 
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
			$recentitems = new WP_Query('post_type=blog&posts_per_page=5');			
			echo "<h3>Recent posts</h3>";
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
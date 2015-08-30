<?php
/**
 * The Template for displaying all single blogposts.
 *
 * @package WordPress
 */

get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
		
			<div class="col-lg-7 col-md-8 col-sm-12 white">
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
				<?php echo the_date('j M Y', '<p class=news_date>', '</p>') ?>
				<?php the_content(); ?>
				<?php
				if ('open' == $post->comment_status) {
					 comments_template( '', true ); 
				}
			 ?>

		</div> <!--end of first column-->
		<div class="col-lg-4 col-lg-offset-1 col-md-4 col-sm-12">
			<?php
            $user = get_userdata($post->post_author);
            
            echo "<div class='widget-box'><h3>Author</h3><div class='well'><div class='media'>";
            
            $gis = "options_forum_support";
			$forumsupport = get_option($gis); 
			if ($forumsupport){
                echo "<a class='pull-left' href='".site_url()."/staff/" . $user->user_nicename . "/'>";
            } else {
                echo "<a class='pull-left' href='".site_url()."/author/" . $user->user_nicename . "/'>";	                        
            }
			$user_info = get_userdata($post->post_author);
			$userurl = site_url().'/staff/'.$user_info->user_nicename;
			$displayname = get_user_meta($post->post_author ,'first_name',true )." ".get_user_meta($post->post_author ,'last_name',true );		
			$directorystyle = get_option('options_staff_directory_style'); // 0 = squares, 1 = circles
			$avstyle="";
			if ( $directorystyle==1 ) $avstyle = " img-circle";
			$image_url = get_avatar($post->post_author , 150, "", $user_info->display_name);
			$image_url = str_replace(" photo", " photo alignleft".$avstyle, $image_url);
			$image_url = str_replace('"150"', '"96"', $image_url);
			$image_url = str_replace("'150'", "'96'", $image_url);
            echo $image_url;
            echo "</a>";
            echo "<div class='media-body'><p class='media-heading'>";
            echo "<strong>".$user->display_name."</strong><br>";                        
            $jobtitle = get_user_meta($user->ID, 'user_job_title',true);
            $bio = get_user_meta($user->ID,'description',true);                        
			echo "<strong>".$jobtitle."</strong><br>";
            if ($forumsupport){
                echo "<a href='".site_url()."/staff/";
				echo $user->user_nicename . "/' title='{$user->display_name}'>Staff profile</a><br>";
            }
            echo "<a href='".site_url()."/author/";
			echo $user->user_nicename . "/' title='{$user->display_name}'>Blog posts</a><br>";
			echo "</div></div></div></div>";
			
				$alreadydone = array();
				$related = get_post_meta($id,'related',true);
	
				$html='';
				if ($related){
					foreach ($related as $r){ 
						$title_context="";
						$rlink = get_post($r);
						if ($rlink->post_status == 'publish' && $rlink->ID != $id ) {
							$taskparent=$rlink->post_parent; 
							if ($taskparent){
								$taskparent = get_post($taskparent);
								$title_context=" (".govintranetpress_custom_title($taskparent->post_title).")";
							}		
							$html.= "<li><a href='".get_permalink($rlink->ID)."'>".govintranetpress_custom_title($rlink->post_title).$title_context."</a></li>";
							$alreadydone[] = $r;
						}
					}
				}
				
				//get anything related to this post
				$otherrelated = get_posts(array('post_type'=>array('task','news','project','vacancy','blog','team','event'),'posts_per_page'=>-1,'exclude'=>$related,'meta_query'=>array(array('key'=>'related','compare'=>'LIKE','value'=>'"'.$id.'"')))); 
				foreach ($otherrelated as $o){
					if ($o->post_status == 'publish' && $o->ID != $id ) {
								$taskparent=$o->post_parent; 
								$title_context='';
								if ($taskparent){
									$taskparent = get_post($taskparent);
									$title_context=" (".govintranetpress_custom_title($taskparent->post_title).")";
								}		
								$html.= "<li><a href='".get_permalink($o->ID)."'>".govintranetpress_custom_title($o->post_title).$title_context."</a></li>";
								$alreadydone[] = $o->ID;
						}
				}
	
				if ( $html ){
					echo "<div class='widget-box list'>";
					echo "<h3 class='widget-title'>Related</h3>";
					echo "<ul>";
					echo $html;
					echo "</ul></div>";
				}

				$posttags = get_the_tags();
				if ($posttags) {
					$foundtags=false;	
					$tagstr="";
				  	foreach($posttags as $tag) {
				  		if (substr($tag->name,0,9)!="carousel:"){
				  			$foundtags=true;
				  			$tagurl = $tag->slug;
					    	$tagstr=$tagstr."<span><a class='label label-default' href='".site_url()."/tag/{$tagurl}/?post_type=blog'>" . str_replace(' ', '&nbsp' , $tag->name) . '</a></span> '; 
				    	}
				  	}
				  	if ($foundtags){
					  	echo "<div class='widget-box'><h3>Tags</h3><p> "; 
					  	echo $tagstr;
					  	echo "</p></div>";
				  	}
				}
		 	dynamic_sidebar('blog-widget-area'); 
		 	
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
			wp_reset_query();
				?>
		</div> <!--end of second column-->
			
<?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>
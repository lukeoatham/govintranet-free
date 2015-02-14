<?php
/* Template name: Forum */


get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>


					<div class="col-lg-12 white ">
						<div class="row">
							<div class='breadcrumbs'>
								<?php if(function_exists('bcn_display') && !is_front_page()) {
									bcn_display();
									}?>
							</div>
						</div>
					<?php
						echo "<h1>".get_the_title()."</h1>";
						
						
						the_content();
if (is_user_logged_in()){
	get_currentuserinfo();
	echo "<br><p>Logged in as: ".$current_user->display_name." | <a href='".wp_logout_url(site_url().'/about/forums/')."'>Logout</a></p><br>";
	}
	else {
	echo "<p><a href='".wp_login_url(site_url().'/about/forums/')."'>Login</a> | <a href='".site_url()."/wp-login.php?action=register'>Register</a></p>";
}

?>				

				<div id='bbpress-forums'>
				
				<?php
				$allforums = get_posts(
					array('post_type'=>'forum',
					'posts_per_page'=>-1,
					'post_parent'=>0,
					'orderby'=>'menu_order',
					'order'=>'ASC',
					'post_status'=>array('publish')
					)
				);
				foreach ($allforums as $a) {
					//print_r(get_post_meta($post->ID,'visibility'));
					if ($a->post_status=='publish'){
					$forumtitle = get_the_title($a->ID);
					$parentforum = $a->post_name;

					echo "<div><hr>";
					echo "<h3><a href='".site_url()."/forums/{$a->post_name}/'>".$forumtitle."</a></h3>";
					echo wpautop($a->post_content)."</div>";
					
					echo "<ul class='bbp-forums'>
					<li class='bbp-header'>
					<ul class='forum-titles'>
					<li class='bbp-forum-info'>&nbsp;</li>
					<li class='bbp-forum-topic-count'>Topics</li>
					<li class='bbp-forum-reply-count'>Posts</li>			
					<li class='bbp-forum-freshness'>Freshness</li>												
					</ul>
					</li>";
					
					$subforums = get_posts(
						array('post_type'=>'forum',
						'posts_per_page'=>-1,
						'post_parent'=>$a->ID,
					'orderby'=>'menu_order',
					'order'=>'ASC'
						)
					);
						$latestdate = 0;
						$odd=true;
					 foreach ($subforums as $subf) {


						$sfpost = get_post( $subf->ID );
						$sfslug = $subf->post_name;
						$forumtitle = $sfpost->post_title;
			

						$topics = get_posts(
							array('post_type'=>'topic',
							'posts_per_page'=>-1,
							'post_parent'=>$subf->ID,
							'post_status'=>array('publish','closed'))
							);
						$replycount = 0;
						$topiccount = count($topics);

						
						foreach ($topics as $top){	
							$replies = get_posts(
								array('post_type'=>'reply',
								'posts_per_page'=>-1,
								'post_parent'=>$top->ID)
								);
								if ($top->post_modified > $latestdate){
									$latestdate=$top->post_modified;
								}
							foreach ($replies as $rep){
								if ($rep->post_modified > $latestdate){
									$latestdate=$rep->post_modified;
								}
							}	
								
							$replycount+=count($replies);
						}
						$replycount+=$topiccount;
					
					echo "<li class='bbp-body'>
						<ul class='forum type-forum  ";
						if ($odd){
							echo "odd";
							$odd=false;
							} else {
							echo "even";
							$odd=true;
						}
						
					if ($latestdate==0){
							$latest='No activity';
						}
						else
						{
					$latest = human_time_diff( date('U',strtotime($latestdate,TRUE)), current_time('timestamp') ). " ago";
					}
						echo "'>
					<li class='bbp-forum-info'>
					<a class='bbp-forum-title' href='".site_url()."/forums/{$parentforum}/{$sfslug}/'>".$forumtitle."</a>
					<div class='bbp-forum-content'>".wpautop($subf->post_content)."</div>
					</li>
					<li class='bbp-forum-topic-count'>".$topiccount."</li>
					<li class='bbp-forum-reply-count'>".$replycount."</li>			
					<li class='bbp-forum-freshness'>".$latest."</li>												
					</ul>
					</li>";
					$latestdate=0;
					}					

					echo "
					</ul>
					";


				}//if publish status
				}
				?>
				
				</div>
		</div>

<?php endwhile; ?>

<?php get_footer(); ?>
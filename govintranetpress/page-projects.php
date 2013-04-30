<?php
/* Template name: Projects */

get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>


		<div class="row">

			<div class="eightcol white last" id='content'>
									<div class="row">
							<div class='breadcrumbs'>
							<?php if(function_exists('bcn_display') && !is_front_page()) {
								bcn_display();
							}?>
							</div>
				</div>
				<div class="content-wrapper">
					<?php
						echo "<h1>Projects</h1>";
						the_content();
						?>
				</div>
				<!-- category search box -->
				
				<div class="content-wrapper">
	<div class="category-search">
		<div id="sbc">
		<form method="get" id="sbc-search" action="<?php echo home_url( '/' ); ?>">
			<input type="hidden" value="projects" name = "post_type" />
			<input type="text" value="" name="s" id="s2" class="multi-cat" onblur="if (this.value == '') {this.value = '';}"  onfocus="if (this.value == '') {this.value = '';}" />
			<input type="submit" id="sbc-submitx" class="small awesome blue" value="Search" />
		</form>
	</div>
	</div>
<!--				Show: <a href='#'>All projects</a> | <a href='#'>Only open projects</a> -->
<?php
							$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
							$counter = 0;	
							$tdate= getdate();
							$tdate = $tdate['year']."-".$tdate['mon']."-".$tdate['mday'];
							$tday = date( 'd' , strtotime($tdate) );
							$tmonth = date( 'm' , strtotime($tdate) );
							$tyear= date( 'Y' , strtotime($tdate) );
							$sdate=$tyear."-".$tmonth."-".$tday;
							$news =new WP_Query( array ( 
										'post_type'=>'projects',
										'posts_per_page'=>-1,
										'orderby' => 'name', 
										'order' => 'ASC',
								       'paged' => $paged,

										'meta_query' => array(
												       array(
												           'key' => 'parent_project',
												           'compare' => 'NOT EXISTS',
												       ),
												       array(
											           		'key' => 'project_end_date',
											        	   'value' => $sdate,
											    	       'compare' => '>=',
											    	       'type' => 'DATE' )  
												       ),
												 )
										);

							if ($news->post_count==0){
								echo "Nothing to show.";
							}
							while ($news->have_posts()) {
									$news->the_post();
										$thistitle = get_the_title($ID);
										$thisURL=get_permalink($ID);
										$image_url = get_the_post_thumbnail($ID, 'thumbnail', array('class' => 'alignright'));
										$thisexcerpt= get_the_excerpt();
										$thisdate= $post->post_date;
										$thisdate=date("j M Y",strtotime($thisdate));
										echo "<div class='newsitem'><p><a href='{$thisURL}'>".$image_url;
										echo "<h3>".$thistitle."</h3></a>";
										$post_type = get_the_category();
										if ($post_type){
										$thistype='';
										$thistypeid='';
										foreach ($post_type as $p) {
											$thistype = $p->name;
											$thistypeid = $p->cat_ID;
										}
										echo "&nbsp;<span class='wptagsinfo news'>".$thistype."</span></p>";
										}
										echo "<p>".$thisexcerpt."</p>";
										echo "<p class='news_date'><a class='more' href='{$thisURL}' title='{$thistitle}' >Read more</a></p>";
										echo "</div><div class='clearfix'></div><hr class='light' />";
							}
							?>
						<?php if (  $news->max_num_pages > 1 ) : ?>
						<?php if (function_exists(wp_pagenavi)) : ?>
							<?php wp_pagenavi(array('query' => $news)); ?>
 						<?php else : ?>
							<?php next_posts_link('&larr; Older items', $news->max_num_pages); ?>
							<?php previous_posts_link('Newer items &rarr;', $news->max_num_pages); ?>						
						<?php endif; 
						?>
					<?php endif; 
							wp_reset_query();								

							?>
				

				</div>
			</div>
			<div class="fourcol last">
			<div class='widget-box'>
					<h3 class='widget-title'>Search by tag</h3>

			<?php		echo my_colorful_tag_cloud('', '' , 'projects'); 

			?>

			</div>
			
			</div>
		</div>

<?php endwhile; ?>

<?php get_footer(); ?>
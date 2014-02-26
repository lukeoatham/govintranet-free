<?php
/* Template name: Projects */

get_header(); ?>

<?php 
if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
					<div class="col-lg-8 white ">
						<div class="row">
							<div class='breadcrumbs'>
								<?php if(function_exists('bcn_display') && !is_front_page()) {
									bcn_display();
									}?>
							</div>
						</div>

				<?php
					echo "<h1>";
					the_title();
					echo "</h1>";
					the_content();
					?>

			<!-- category search box -->
			<div>

	<div class="well">
									<form class="form-horizontal" role="form" method="get" id="sbc-search" action="<?php echo site_url('/'); ?>">
										<label for="s">Search</label>
										<div class="form-group input-md">
											<input type="text" value="" name="s" id="sbc-s" class="form-control input-md" onblur="if (this.value == '') {this.value = '';}"  onfocus="if (this.value == '') {this.value = '';}" />
										</div>
										<div class="form-group input-md">
											<button type="submit" class="btn btn-primary input-md">Search</button>
										<input type="hidden" value="projects" name = "post_type" />
										</div>
									</form>
	</div>

<script>
jQuery(function(){
jQuery("#sbc-s").focus();
});
</script>

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
				$image_url = get_the_post_thumbnail($ID, 'thumbnail', array('class' => 'alignright'));
			echo "<div class='newsitem'>".$image_url ;
			echo "<hr>";
			$taskpod = new Pod ('project' , $post->ID); 
			
			$context = "project";
			$icon = "road";
					


			?>
<h3>				
	<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( '%s %s', 'govintranetpress' ), the_title_attribute( 'echo=0' ), " (" . $context . ")" ); ?>" rel="bookmark"><?php the_title(); echo $title_context; ?></a></h3>

<?php
			the_excerpt(); 
			echo "</div>";
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
		<div class="col-lg-4 last">
			<div class='widget-box'>
				<h3 class='widget-title'>Search by tag</h3>
			<?php		echo my_colorful_tag_cloud('', '' , 'projects'); 
			?>
			</div>
		</div>


<?php endwhile; ?>

<?php get_footer(); ?>
<?php
/* Template name: Projects */

get_header(); 
wp_register_script( 'scripts_search', get_template_directory_uri() . '/js/ht-scripts-search.js','' ,'' ,true );
wp_enqueue_script( 'scripts_search' );

if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
		<div class="col-lg-8 col-md-8 col-sm-8 white ">
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
				<form class="form-horizontal" method="get" id="sbc-search" action="<?php echo site_url('/'); ?>">
					<label for="s"><?php _e('Search' , 'govintranet'); ?></label>
					<div class="form-group input-md">
						<input type="text" value="" name="s" id="sbc-s" class="form-control input-md" onblur="if (this.value == '') {this.value = '';}"  onfocus="if (this.value == '') {this.value = '';}" />
					</div>
					<div class="form-group input-md">
						<button type="submit" class="btn btn-primary input-md"><?php _e('Search' , 'govintranet'); ?></button>
					<input type="hidden" value="project" name = "post_types[]" />
					</div>
				</form>
			</div>
			<?php
			$show = ''; 
			if ( isset($_GET['show']) ) $show = $_GET['show'];
			if ( $show != "all" ):
				echo '<p><a href="?show=all">' . __("Show past projects","govintranet") . '</a></p>';
			else:
				echo '<p><a href="?show=">' . __("Only show open projects","govintranet") . '</a></p>';
			endif;
			
			$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
			$counter = 0;	
			$tzone = get_option('timezone_string');
			if ( $tzone ) date_default_timezone_set($tzone);
			$sdate = date('Ymd');
			$pquery = array ( 
						'post_type' => 'project',
						'posts_per_page' => -1,
						'orderby' => 'name', 
						'order' => 'ASC',
				       'paged' => $paged,
					   'post_parent' => 0,
						 );

			if ( $show != "all" ) $pquery['meta_query']  = array(
						       array(
					           		'key' => 'project_end_date',
					        	   'value' => $sdate,
					    	       'compare' => '>=',
					    	       'type' => 'DATE' 
					    	       )  
						       );
			
			$projects =new WP_Query( $pquery );
			if ($projects->post_count==0){
				echo __("Nothing to show","govintranet") . ".";
			}
			while ($projects->have_posts()) {
				$projects->the_post();
				$image_url = get_the_post_thumbnail($id, 'thumbnail', array('class' => 'alignright'));
				echo "<div class='newsitem'>".$image_url ;
				echo "<hr>";
				$taskpod = get_post($post->ID); 
				$context = "project";
				$icon = "road";
				?>
				<h3>				
				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute( 'echo=1' ); ?>" rel="bookmark"><?php the_title(); ?></a></h3>

				<?php
				the_excerpt(); 
				echo "</div>";
			}
			if (  $projects->max_num_pages > 1 ) : 
				if (function_exists('wp_pagenavi')) : 
					wp_pagenavi(array('query' => $projects)); 
				else : 
					next_posts_link(__('&larr; Older items','govintranet'), $projects->max_num_pages); 
					previous_posts_link(__('Newer items &rarr;','govintranet'), $projects->max_num_pages); 
				endif; 
			endif; 
			wp_reset_query();								
			?>
			</div>
		</div>

		<div class="col-lg-4 col-md-4 col-sm-4" id="sidebar">
			<h2 class="sr-only">Sidebar</h2>
			<?php 
			get_template_part("part", "sidebar"); 
			get_template_part("part", "related");
			if ( $cloud = gi_howto_tag_cloud('project') ): ?>
			<div class='widget-box'>
				<h3 class='widget-title'><?php _e('Browse by tag' , 'govintranet') ; ?></h3>
				<?php echo $cloud; ?>
			</div>
			<?php endif; ?>
		</div>

<?php endwhile; ?>

<?php get_footer(); ?>
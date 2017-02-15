<?php
/* Template name: Vacancies */

get_header(); 

$tzone = get_option('timezone_string');
date_default_timezone_set($tzone);
$sdate = date('Ymd');
$stime = date('H:i'); 

if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

	<div class="col-lg-8 col-md-8 white ">
		<div class="row">
			<div class='breadcrumbs'>
				<?php if(function_exists('bcn_display') && !is_front_page()) {
					bcn_display();
					}?>
			</div>
		</div>
		<?php
		the_title('<h1>','</h1>');
		the_content();
		?>
		<!-- category search box -->
		<div class="well">
			<form class="form-horizontal" method="get" id="sbc-search" action="<?php echo site_url('/'); ?>">
				<label for="sbc-s"><?php printf( __('In %s', 'govintranet') , strtolower(get_the_title()) ) ; ?></label>
				<div class="form-group input-md">
					<input type="text" value="" name="s" id="sbc-s" class="form-control input-md" onblur="if (this.value == '') {this.value = '';}"  onfocus="if (this.value == '') {this.value = '';}" />
				</div>
				<div class="form-group input-md">
					<button type="submit" class="btn btn-primary input-md"><?php _e('Search' , 'govintranet'); ?></button>
					<input type="hidden" value="vacancy" name = "post_type" />
				</div>
			</form>
		</div>
	
		<?php 
		//SHOW FILTERING OPTIONS
		$jobgrade='';
		if ( isset( $_GET['grade'] ) ) $jobgrade = $_GET['grade'];
		if ( $terms = get_terms('grade') ): 
			echo "<p>" . __('Grades','govintranet'). ": ";
			if ($jobgrade=='all' || !$jobgrade) : ?>
					<strong><?php _e('All grades' , 'govintranet'); ?></strong> | 
			<?php else: ?>
					<a href='?grade=all&paged=1'><?php _e('All grades' , 'govintranet'); ?></a> | 
			<?php endif; 
			if ($terms) {
		  		foreach ((array)$terms as $taxonomy ) {
		  		    $themeid = $taxonomy->term_id;
		  		    $themeURL= $taxonomy->slug;
		  			if ($jobgrade == $themeURL) {
			  			echo "<strong>" . $taxonomy->name . "</strong> | ";
			  		} else {
			  			echo "<a href='?grade=".$themeURL."&paged=1'>" . $taxonomy->name . "</a> | ";
			  		}
				}
			}  
		echo "</p>";
		endif;

		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		$posts_per_page = get_option('posts_per_page',10);
		$counter = 0;	

		if ( $jobgrade!='all' && $jobgrade!='' ){ 
			
			// showing all vacancies for a specific grade

			$vacancies =new WP_Query(array ( 
			'orderby' => 'vacancy_closing_date', 
			'order' => 'ASC',
			'post_type'=>'vacancy',
			'posts_per_page'=>$posts_per_page,
			'paged' => $paged,
			'tax_query' => array(
					array(
						'taxonomy' => 'grade',
						'field' => 'slug',
						'terms' => $jobgrade
					)
				),
			'meta_query' => array(
						'relation' => 'OR',
					       array(
					           'key' => 'vacancy_closing_date',
					           'value' => $sdate,
					           'compare' => '>',
					       ),
					       array(
						       'relation' => 'AND',
						       array(
						           'key' => 'vacancy_closing_date',
						           'value' => $sdate,
						           'compare' => '=',
						       ),
						       array(
						           'key' => 'vacancy_closing_time',
						           'value' => $stime,
						           'compare' => '<',
					           ),
					       ),
					       
				       )
					)
			);

		} else {

			// showing everything

			$vacancies =new WP_Query(array ( 
				'orderby' => 'vacancy_closing_date', 
				'order' => 'ASC',
				'post_type'=>'vacancy',
				'posts_per_page'=>$posts_per_page,
				'paged' => $paged,
				'meta_query' => array(
						'relation' => 'OR',
					       array(
					           'key' => 'vacancy_closing_date',
					           'value' => $sdate,
					           'compare' => '>',
					       ),
					       array(
						       'relation' => 'AND',
						       array(
						           'key' => 'vacancy_closing_date',
						           'value' => $sdate,
						           'compare' => '=',
						       ),
						       array(
						           'key' => 'vacancy_closing_time',
						           'value' => $stime,
						           'compare' => '>',
					           ),
					       ),
					       
				       )
					)
				);
		}
		if ($vacancies->post_count==0){
			echo __("Nothing to show","govintranet") . ".";
		}

		if (  $vacancies->max_num_pages > 1 && $paged > 1 ) : 
			if (function_exists('wp_pagenavi')) : 
				wp_pagenavi(array('query' => $vacancies)); 
			else : 
				next_posts_link(__('&larr; Older items','govintranet'), $vacancies->max_num_pages); 
				previous_posts_link(__('Newer items &rarr;','govintranet'), $vacancies->max_num_pages); 
			endif; 
		endif; 

		if ($vacancies->have_posts()) while ($vacancies->have_posts()) {
			$vacancies->the_post();
			$thistitle = get_the_title($id);
			$thisURL=get_permalink($id);
			$image_url = get_the_post_thumbnail($id, 'thumbnail', array('class' => 'alignright'));
			$thisexcerpt= get_the_excerpt();
			$thisdate= get_post_meta($post->ID, 'vacancy_closing_date', true);
			$thisdate=date(get_option('date_format'),strtotime($thisdate));
			$thistime= get_post_meta($post->ID, 'vacancy_closing_time', true);
			$thistime=date(get_option('time_format'),strtotime($thistime));
			echo "<div class='newsitem'><a href='{$thisURL}'>".$image_url."</a>";
			echo "<h3><a href='{$thisURL}'>".$thistitle."</a></h3>";
			echo "<p><span class='news_date'>" . __('Closing','govintranet') .": ".$thisdate." ".$thistime."</span>";
			$post_type = get_the_category();
			$thistype = '';
			if ( $post_type ) foreach ($post_type as $p) {
				$thistype = $p->name;
				$thistypeid = $p->cat_ID;
			}
			echo "&nbsp;<span class='wptagsinfo news'>".$thistype."</span></p>";
			echo "<p>".$thisexcerpt."</p>";
			echo "<p class='news_date'><a class='more' href='{$thisURL}' title='{$thistitle}'>" . __('Read more' , 'govintranet') . "</a></p>";
			echo "</div><div class='clearfix'></div><hr class='light' />";
		}
		if (  $vacancies->max_num_pages > 1 ) : 
			if (function_exists('wp_pagenavi')) : 
				wp_pagenavi(array('query' => $vacancies)); 
			else : 
				next_posts_link(__('&larr; Older items','govintranet'), $vacancies->max_num_pages); 
				previous_posts_link(__('Newer items &rarr;','govintranet'), $vacancies->max_num_pages); 
			endif; 
		endif; 		
		wp_reset_query();					
		?>
	</div>

	<div class="col-lg-4 col-md-4" id="sidebar">

		<?php if ( $cloud = gi_howto_tag_cloud('vacancy') ): ?>
		<div class='widget-box'>
			<h3 class='widget-title'><?php _e('Browse by tag' , 'govintranet'); ?></h3>
			<?php echo $cloud; ?>
		</div>
		<?php endif; ?>
		
	</div>

<?php endwhile; ?>

<?php get_footer(); ?>
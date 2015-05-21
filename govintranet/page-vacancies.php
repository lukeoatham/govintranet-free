<?php
/* Template name: Vacancies */

get_header(); 

$tzone = get_option('timezone_string');
date_default_timezone_set($tzone);
$sdate = date('Ymd');
$stime = date('H:i'); 

//CHANGE CLOSED VACANCIES TO DRAFT STATUS
//$wpdb->query( "update $wpdb->posts, $wpdb->postmeta set $wpdb->posts.post_status='draft' where $wpdb->postmeta.meta_key='vacancy_closing_date' and $wpdb->postmeta.meta_value < '".$sdate."' and $wpdb->postmeta.post_id = $wpdb->posts.id and $wpdb->posts.post_status='publish';");

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
			<form class="form-horizontal" role="form" method="get" id="sbc-search" action="<?php echo site_url('/'); ?>">
				<label for="cat">In <?php echo strtolower(get_the_title()); ?> </label>
				<div class="form-group input-md">
					<input type="text" value="" name="s" id="sbc-s" class="form-control input-md" onblur="if (this.value == '') {this.value = '';}"  onfocus="if (this.value == '') {this.value = '';}" />
				</div>
				<div class="form-group input-md">
					<button type="submit" class="btn btn-primary input-md">Search</button>
					<input type="hidden" value="vacancy" name = "post_type" />
				</div>
			</form>
		</div>
	
		<?php 
		//SHOW FILTERING OPTIONS
		$jobgrade='';
		if ( isset( $_GET['grade'] ) ) $jobgrade = $_GET['grade'];
		if ( $terms = get_terms('grade') ): 
			echo "<p>Grades: ";
			if ($jobgrade=='all' || !$jobgrade) : ?>
					<strong>All grades</strong> | 
			<?php else: ?>
					<a href='?grade=all'>All grades</a> | 
			<?php endif; 
			if ($terms) {
		  		foreach ((array)$terms as $taxonomy ) {
		  		    $themeid = $taxonomy->term_id;
		  		    $themeURL= $taxonomy->slug;
		  			if ($jobgrade == $themeURL) {
			  			echo "<strong>" . $taxonomy->name . "</strong> | ";
			  		} else {
			  			echo "<a href='?grade=".$themeURL."'>" . $taxonomy->name . "</a> | ";
			  		}
				}
			}  
		echo "</p>";
		endif;

		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		$counter = 0;	

		if ( $jobgrade!='all' && $jobgrade!='' ){ 
			
			// showing all vacancies for a specific grade

			$vacancies =new WP_Query(array ( 
			'orderby' => 'title', 
			'order' => 'ASC',
			'post_type'=>'vacancy',
			'posts_per_page'=>10,
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
				'orderby' => 'title', 
				'order' => 'ASC',
				'post_type'=>'vacancy',
				'posts_per_page'=>10,
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
			echo "Nothing to show.";
		}

		if (  $vacancies->max_num_pages > 1 ) : 
			if (function_exists(wp_pagenavi)) : 
				wp_pagenavi(array('query' => $vacancies)); 
			else : 
				next_posts_link('&larr; Older items', $vacancies->max_num_pages); 
				previous_posts_link('Newer items &rarr;', $vacancies->max_num_pages); 
			endif; 
		endif; 

		if ($vacancies->have_posts()) while ($vacancies->have_posts()) {
			$vacancies->the_post();
  		    //print_R( $vacancies);
			$thistitle = get_the_title($id);
			$thisURL=get_permalink($id);
			$image_url = get_the_post_thumbnail($id, 'thumbnail', array('class' => 'alignright'));
			$thisexcerpt= get_the_excerpt();
			$thisdate= get_post_meta($post->ID, 'vacancy_closing_date', true);
			$thisdate=date("j M Y",strtotime($thisdate));
			$thistime= get_post_meta($post->ID, 'vacancy_closing_time', true);
			$thistime=date("H:i",strtotime($thistime));
			echo "<div class='newsitem'><a href='{$thisURL}'>".$image_url."</a>";
			echo "<h3><a href='{$thisURL}'>".$thistitle."</a></h3>";
			echo "<p><span class='news_date'>Closing: ".$thisdate." ".$thistime."</span>";
			$post_type = get_the_category();
			$thistype = '';
			if ( $post_type ) foreach ($post_type as $p) {
				$thistype = $p->name;
				$thistypeid = $p->cat_ID;
			}
			echo "&nbsp;<span class='wptagsinfo news'>".$thistype."</span></p>";
			echo "<p>".$thisexcerpt."</p>";
			echo "<p class='news_date'><a class='more' href='{$thisURL}' title='{$thistitle}' >Read more</a></p>";
			echo "</div><div class='clearfix'></div><hr class='light' />";
		}
		wp_reset_query();								
		?>
	</div>

	<div class="col-lg-4 col-md-4">
		<?php if ( $cloud = gi_howto_tag_cloud('vacancy') ): ?>
		<div class='widget-box'>
			<h3 class='widget-title'>Browse by tag</h3>
			<?php echo $cloud; ?>
		</div>
		<?php endif; ?>
		<?php
		wp_reset_postdata();
		the_post_thumbnail('medium', array('class'=>'img img-responsive')); 
		echo wpautop( "<p class='news_date'>".get_post_thumbnail_caption()."</p>" );
		$thispage = get_page($id);
		$relatedteams = '';
		if (taxonomy_exists('team')) $relatedteams = get_the_terms( $id, 'team' );
		$related = get_post_meta($id,'related',true);

				if ($related){
					$html='';
					foreach ($related as $r){ 
						$title_context="";
						$rlink = get_post($r);
						if ($rlink->post_status == 'publish' && $rlink->ID != $id ) {
							$taskparent=$rlink->post_parent; 
							if ($taskparent && in_array($rlink->post_type, array('task','project','team') ) ){
								$tparent_guide_id = $taskparent; 		
								if ( $tparent_guide_id ) $taskparent = get_post($tparent_guide_id);
								if ( $taskparent ) $title_context=" (".govintranetpress_custom_title($taskparent->post_title).")";
							}		
							$html.= "<li><a href='".get_permalink($rlink->ID)."'>".govintranetpress_custom_title($rlink->post_title).$title_context."</a></li>";
						}
					}
				}
				
				//get anything related to this post
				$otherrelated = get_posts(array('post_type'=>array('task','news','project','vacancy','blog','team','event'),'posts_per_page'=>-1,'exclude'=>$related,'meta_query'=>array(array('key'=>'related','compare'=>'LIKE','value'=>'"'.$id.'"')))); 
				foreach ($otherrelated as $o){
					if ($o->post_status == 'publish' && $o->ID != $id ) {
								$taskparent=$o->post_parent; 
								if ($taskparent && in_array($rlink->post_type, array('task','project','team') ) ){
									$tparent_guide_id = $taskparent; 		
									if ( $tparent_guide_id ) $taskparent = get_post($tparent_guide_id);
									if ( $taskparent ) $title_context=" (".govintranetpress_custom_title($taskparent->post_title).")";
								}		
								$html.= "<li><a href='".get_permalink($rlink->ID)."'>".govintranetpress_custom_title($rlink->post_title).$title_context."</a></li>";
						}
				}

				if ($related || $otherrelated){
					echo "<div class='widget-box list'>";
					echo "<h3 class='widget-title'>Related</h3>";
					echo "<ul>";
					echo $html;
					echo "</ul></div>";
				}	?>
	</div>

<?php endwhile; ?>

<?php get_footer(); ?>
<?php
/* Template name: Vacancies */

get_header(); 
$gis = "general_intranet_time_zone";
$tzone = get_option($gis);
date_default_timezone_set($tzone);
$tdate= getdate();
$tdate = $tdate['year']."-".$tdate['mon']."-".$tdate['mday'];
$tday = date( 'd' , strtotime($tdate) );
$tmonth = date( 'm' , strtotime($tdate) );
$tyear= date( 'Y' , strtotime($tdate) );
$sdate=$tyear."-".$tmonth."-".$tday;

$wpdb->query(
	"update wp_posts, wp_postmeta set wp_posts.post_status='draft' where wp_postmeta.meta_key='closing_date' and wp_postmeta.meta_value < '".$sdate."' and wp_postmeta.post_id = wp_posts.id and wp_posts.post_status='publish' and wp_posts.post_type='vacancies';"
	);

?>

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
					
						echo "<h1>Job vacancies</h1>";
						the_content();
						?>
				</div>
				
				<div class="content-wrapper">
	<div class="category-search blue">
		<div id="sbc">
		<form method="get" id="sbc-search" action="<?php echo home_url( '/' ); ?>">
			<input type="hidden" value="vacancies" name = "post_type" />
			<input type="text" value="" name="s" id="s2" class="multi-cat" onblur="if (this.value == '') {this.value = '';}"  onfocus="if (this.value == '') {this.value = '';}" />
			<input type="submit" id="sbc-submixt" class="small awesome blue" value="Search" />
		</form>
	</div>
	</div>
					<?php $jobtype = $_GET['show']; ?>
					<?php $jobgrade = $_GET['grade']; ?>

				<p>Show: 
				<?php if ($jobtype=='all' or !$jobtype) : ?>
				<strong>All vacancies</strong>
				<?php else : ?>
				<a href='/about/vacancies/?show=all&amp;grade=<?php echo $jobgrade ;?>'>All vacancies</a> 
				<?php endif; ?>
				<?php if ($jobtype=='projects') : ?>
				| <strong>Only project vacancies</strong> 
				<?php else : ?>
				<?php //if ($jobgrade=='all') : ?>
				| <a href='/about/vacancies/?show=projects&amp;grade=<?php echo $jobgrade ;?>'>Only project vacancies</a> 
				<?php// endif; ?>
				<?php endif; ?>
				</p>				
				<p>Grades: 
				<?php if ($jobgrade=='all' || !$jobgrade) : ?>
				<strong>All grades</strong> | 
				<?php else : 
					if ($jobtype=='projects') :
				?>
				<a href='/about/vacancies/?grade=all&show=projects'>All grades</a> | 
				<?php else : ?>
				<a href='/about/vacancies/?grade=all'>All grades</a> | 
				<?php endif; ?>
				<?php endif; ?>
				<?php
				 				$terms = get_terms('grade');
								if ($terms) {
							  		foreach ((array)$terms as $taxonomy ) {
							  		    $themeid = $taxonomy->term_id;
							  		    $themeURL= $taxonomy->slug;
							  			$thistheme = new pod('grade', $themeid);
							  			if ($jobgrade == $themeURL) {
								  			echo "<strong>" . $taxonomy->name . "</strong> | ";
								  			} else {
								  			echo "<a href='/about/vacancies/?grade=".$themeURL."&amp;show={$jobtype}'>" . $taxonomy->name . "</a> | ";
								  		}
									}
								}  
				?>
				</p>				
				
<?php
							$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
							$counter = 0;	

							if ($jobtype=='projects'){
									$project_id = $wpdb->get_results( 'select id from wp_posts where post_name = "project"' );
									$project_id = $project_id[0]->id;
									$q = "
									select distinct post_id from wp_postmeta, wp_posts, wp_podsrel
									where post_type = 'vacancies' AND
									wp_podsrel.field_id = ".$project_id." AND
									wp_podsrel.item_id = wp_posts.id AND
									wp_postmeta.post_id = wp_posts.id AND
									wp_postmeta.meta_key = 'closing_date' AND
									wp_postmeta.meta_value >= '" . $sdate . "'AND
									post_status = 'publish'
									order by post_title asc
									"; 
									$vacancies = $wpdb->get_results( $q );
									$varray=array();
									if ( $vacancies )
									{
										
										foreach ( $vacancies as $post )
										{
										$varray[]= $post->post_id;	
										}

									}
									if ($jobgrade!='all' && $jobgrade != ''){
									$vquery = array(
													'post__in' => $varray,
												    'orderby' => 'title',
												    'order' => 'ASC',
												    'post_type' => 'vacancies',
												    'posts_per_page' => -1,
												    'paged' => $paged,
												    'tax_query' => array(
														array(
															'taxonomy' => 'grade',
															'field' => 'slug',
															'terms' => $jobgrade
														)
													)
												)
												;
										}
										else {
									$vquery = array(
													'post__in' => $varray,
												    'orderby' => 'title',
												    'order' => 'ASC',
												    'post_type' => 'vacancies',
												    'posts_per_page' => -1,
												    'paged' => $paged												
												)
												;
										}		
												
							$pagedvacs = new WP_Query( $vquery );		
									
							if ($pagedvacs->post_count==0){
								echo "<p>Nothing to show.</p>";
							}
							else {
							echo "Showing ". $pagedvacs->found_posts . " job ";
							if ($pagedvacs->found_posts == 1) {
								echo "vacancy.";
							}
							else
							{
								echo "vacancies.";
							}

							}
							while ($pagedvacs->have_posts()) {
								$pagedvacs->the_post();
							  		    $projectvac = new Pod('vacancies', $post->ID);
										$thistitle = govintranetpress_custom_title($projectvac->get_field('title'));
										$thisURL=$projectvac->get_field('guid');
										$image_url = get_the_post_thumbnail($projectvac->get_field('ID'), 'thumbnail', array('class' => 'alignright'));
										$thisexcerpt= $projectvac->get_field('excerpt');
										$thisdate= $projectvac->get_field('post_date');
										$thisdate=date("j M Y",strtotime($thisdate));
										echo "<div class='newsitem'><p><a href='{$thisURL}'>".$image_url;
										echo "<h2>".$thistitle."</h2></a>";
										echo "<p><span class='news_date'>".$thisdate."</span>";
										$post_type = get_the_category();
										foreach ($post_type as $p) {
											$thistype = $p->name;
											$thistypeid = $p->cat_ID;
										}
										echo "&nbsp;<span class='wptagsinfo news'>".$thistype."</span></p>";
										echo "<p>".$thisexcerpt."</p>";
										echo "<p class='news_date'><a class='more' href='{$thisURL}' title='{$thistitle}' >Read more</a></p>";
										echo "</div><div class='clearfix'></div><hr class='light' />";
							}
							?>
						<?php if (  $pagedvacs->max_num_pages > 1 ) : ?>
						<?php if (function_exists(wp_pagenavi)) : ?>
							<?php wp_pagenavi(array('query' => $pagedvacs)); ?>
 						<?php else : ?>
							<?php next_posts_link('&larr; Older items', $pagedvacs->max_num_pages); ?>
							<?php previous_posts_link('Newer items &rarr;', $pagedvacs->max_num_pages); ?>						
						<?php endif; 
						?>
					<?php endif; 

							}
							if ( $jobtype=='all' || $jobtype == '' ){
							if ($jobgrade!='all' && $jobgrade!=''){
							$vacancies =new WP_Query(array ( 
										'orderby' => 'title', 
										'order' => 'ASC',
										'post_type'=>'vacancies',
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
												       array(
												           'key' => 'closing_date',
												           'value' => $sdate,
												           'compare' => '>=',
												       ),
												      $jobtypefilter, 
											       )
											 )
										);
									}
									else
									{
							$vacancies =new WP_Query(array ( 
										'orderby' => 'title', 
										'order' => 'ASC',
										'post_type'=>'vacancies',
										'posts_per_page'=>10,
										'paged' => $paged,
										'meta_query' => array(
												       array(
												           'key' => 'closing_date',
												           'value' => $sdate,
												           'compare' => '>=',
												       ),
												      $jobtypefilter, 
											       )
											 )
										);
									
									}
							if ($vacancies->post_count==0){
								echo "Nothing to show.";
							}
							else
							{
								echo "Showing ". $vacancies->found_posts . " job ";
							if ($vacancies->found_posts == 1) {
								echo "vacancy.";
							}
							else
							{
								echo "vacancies.";
							}
							}
							while ($vacancies->have_posts()) {
									$vacancies->the_post();
							  		    //print_R( $vacancies);
										$thistitle = get_the_title($ID);
										$thisURL=get_permalink($ID);
										$image_url = get_the_post_thumbnail($ID, 'thumbnail', array('class' => 'alignright'));
										$thisexcerpt= get_the_excerpt();
										$thisdate= get_post_meta($post->ID, 'closing_date', true);
										$thisdate=date("j M Y",strtotime($thisdate));
										echo "<div class='newsitem'><a href='{$thisURL}'>".$image_url;
										echo "<h2>".$thistitle."</h2></a>";
										echo "<p><span class='news_date'>Closing: ".$thisdate."</span>";
										$post_type = get_the_category();
										foreach ($post_type as $p) {
											$thistype = $p->name;
											$thistypeid = $p->cat_ID;
										}
										echo "&nbsp;<span class='wptagsinfo news'>".$thistype."</span></p>";
										echo "<p>".$thisexcerpt."</p>";
										echo "<p class='news_date'><a class='more' href='{$thisURL}' title='{$thistitle}' >Read more</a></p>";
										echo "</div><div class='clearfix'></div><hr class='light' />";
							}
							?>
						<?php if (  $vacancies->max_num_pages > 1 ) : ?>
						<?php if (function_exists(wp_pagenavi)) : ?>
							<?php wp_pagenavi(array('query' => $vacancies)); ?>
 						<?php else : ?>
							<?php next_posts_link('&larr; Older items', $vacancies->max_num_pages); ?>
							<?php previous_posts_link('Newer items &rarr;', $vacancies->max_num_pages); ?>						
						<?php endif; 
						?>
					<?php endif; 								
					}
							wp_reset_query();								

							?>
				</div>
			</div>

			<div class="fourcol last">
			<div class='widget-box'>
					<h3 class='widget-title'>Search by tag</h3>

			<?php		echo my_colorful_tag_cloud('', '' , 'vacancies'); 

			?>
			</div>

			</div>
		</div>

<?php endwhile; ?>

<?php get_footer(); ?>
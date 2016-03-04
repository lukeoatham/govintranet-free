<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package WordPress
 * @subpackage Bootstrap 3
 */
 
 //****************************************************
 // if only one result found, zoom straight to the page

$gishelpfulsearch = get_option("options_enable_helpful_search");
if ($gishelpfulsearch == 1){
	if ($wp_query->found_posts==1){
		$location='';
		while ( have_posts() ) : the_post(); 
			if ($post->post_type == 'user' ){
				$location=$post->link;
				$location=str_replace('/author', '/staff', $location);
			} else {
					if ($_GET['pt'] != 'user') {
						$location=$post->guid; 
					}
			}
		endwhile;
		if ($location){
			$location = str_replace( "#038;", "&", $location);
			$location = str_replace( "&amp;", "&", $location);
			header('Location: '.$location);
			exit;
		}
	}
}
				
 //*****************************************************				

get_header(); ?>

	<div class="col-lg-8 col-md-8 col-sm-12 white">
		<div class="row">
			<div class='breadcrumbs'>
				<?php if(function_exists('bcn_display') && !is_front_page()) {
						bcn_display();
					}?>
			</div>
		</div>
	<?php 
	$s=get_search_query();

	if ( !have_posts() ) : 

		$searchnotfound=get_option('options_search_not_found');
		if (!$searchnotfound) $searchnotfound = _x("Nope","search not found","govintranet");
		?>
		<h1><?php echo $searchnotfound; ?></h1>
		<?php 
		if ( isset( $_GET['post_type'] ) ):
			$pt = $_GET['post_type']; 
			$ct = $_GET['cat'];
			$ct = get_category($ct);
			$ct = $ct->name;
			if ($ct){
				$searchcon=$ct;
			}
			if (in_array('blog', $pt)){
				if ($searchcon) $searchcon.=" or";
				$searchcon.= " " . __('blog posts','govintranet');
			}
			if (in_array('event', $pt)){
				if ($searchcon) $searchcon.=" or";
				$searchcon.=" " . __('events', 'govintranet');
			}
			if ( in_array('forum', $pt) || in_array('topic', $pt) || in_array('reply', $pt) ){
				if ($searchcon) $searchcon.=" or";
				$searchcon.=" " . __('the forums','govintranet');
			}	
			if (in_array('jargon-buster', $pt)){
				if ($searchcon) $searchcon.=" or";
				$searchcon.=" " . __('jargon busters','govintranet');
			}
			if (in_array('news', $pt)){
				if ($searchcon) $searchcon.=" or";
				$searchcon.=" " . __('news','govintranet');
			}
			if (in_array('project', $pt)){
				if ($searchcon) $searchcon.=" or";
				$searchcon.=" " . __('projects','govintranet');
			}
			if ($_GET['include']=='user'){
				if ($searchcon) $searchcon.=" or";
				$searchcon.=" " . __('the staff directory','govintranet');
			}					 
			if (in_array('task', $pt)){
				if ($searchcon) $searchcon.=" or";
				$searchcon.=" " . __('tasks','govintranet');
			}
			if (in_array('team', $pt)){
				if ($searchcon) $searchcon.=" or";
				$searchcon.=" " . __('teams','govintranet');
			}
			if (in_array('vacancy', $pt)){
				if ($searchcon) $searchcon.=" or";
				$searchcon.=" " . __('vacancies','govintranet');
			}
		endif; 
		
		echo "<p>";
		if ($pt){
			 printf(__( 'Couldn\'t find anything in %s. Try searching the whole intranet.', 'govintranet' ) , $searchcon) ; 
		} else {
			_e( 'Couldn\'t find anything on the intranet like that. Sometimes using fewer words can help.', 'govintranet' ); 				
		}
		echo "</p>";

		if (function_exists('relevanssi_didyoumean')) { 
			relevanssi_didyoumean(get_search_query(), "<div class='did_you_mean'><h2>" . __('Did you mean?','govintranet') . "</h2><p> ", "</p></div>", 5);
		}

		?>
		<div class="well">
		<form class="form" role="form" id="serps_search" action="<?php echo site_url( '/' ); ?>">
			<div class="form-group">
		    <label for="snf"><?php _e('Search again','govintranet'); ?></label>
			<input type="text" class="form-control" placeholder="Search again" name="s" id="snf" value="<?php echo the_search_query();?>">
			</div>
			<div class="form-group">
			<button type="submit" class="btn btn-primary"><?php _e('Search again','govintranet'); ?></button>
			</div>
		</form>
		</div>

		<script type='text/javascript'>
		    jQuery(document).ready(function(){
				jQuery('#serps_search').submit(function(e) {
				    if (jQuery.trim(jQuery("#snf").val()) === "") {
				        e.preventDefault();
				        jQuery('#snf').focus();
				    }
				});	
				_gaq.push(['_trackEvent', 'Search', 'Empty results', '<?php echo the_search_query();?>']);
			});	
		
		</script>
		<?php
				
	else:

		?>
		<h1><?php printf( __( 'Search results for: %s', 'govintranet' ), '' . $s . '' ); ?></h1>
		<?php
		if ( isset( $_GET['pt'] ) && $_GET['pt'] =='forums'){
			echo "<p class='news_date'>" . __('Showing results from forums' , 'govintranet') . ". <a href='".site_url()."/?s=";
			echo the_search_query();
			echo "'>" . __('Search the intranet' , 'govintranet') . "</a></p>";
		}

		if ($wp_query->found_posts > 1 && isset($_GET['include']) && $_GET['include'] != 'user' ){
			echo "<p class='news_date'>";
			printf( __('Found %d results' , 'govintranet' ) , $wp_query->found_posts );
			echo "</p>";
		}

	
		/* Run the loop for the search to output the results.
		 * If you want to overload this in a child theme then include a file
		 * called loop-search.php and that will be used instead.
		 */
		 get_template_part( 'loop', 'search' );
	
	endif;
	
	?>
	<div class="wp_pagenavi">
	<?php if (  $wp_query->max_num_pages > 1  && isset( $_GET['pt'] ) && $_GET['pt'] != 'user'  ) : ?>
		<?php if (function_exists('wp_pagenavi')) : ?>
			<?php wp_pagenavi(array('query' => $wp_query)); ?>
			<?php else : ?>
			<?php next_posts_link(__('&larr; Older items','govintranet'), $wp_query->max_num_pages); ?>
			<?php previous_posts_link(__('Newer items &rarr;','govintranet'), $wp_query->max_num_pages); ?>						
		<?php endif; ?>
	<?php endif; ?>
	<?php 
	//wp_reset_postdata(); 
	    wp_reset_query();
	?>
	</div>

</div>

<div class="col-lg-4 col-md-4 col-sm-12">
	<div id="search_filter">
		<div id="accordion">
	      <h3>
	        <a class="accordion-toggle btn btn-success dropdown filter_results" data-toggle="collapse" data-parent="#accordion" href="#collapseFilter">
				<?php _ex('Filter results', 'action', 'govintranet');?> <span class="caret"></span>
	        </a>
	      </h3>
	    </div>
	    <div id="collapseFilter" class="xpanel-collapse out in">
	      	<div class="xpanel-body">
				<form role="search" method="get" id="searchfilter" action="<?php echo home_url('/'); ?>">
					<input type="hidden" name="s" value="<?php echo get_search_query(); ?>">
					<input type="hidden" name="paged" value="1">
					<input type="hidden" name="cat" value="any">
					<?php
					$ptargs = array( '_builtin' => false, 'public' => true, 'exclude_from_search' => false );
					$postTypes = get_post_types($ptargs, 'objects');
					$checkbox = "";
					ksort($postTypes); 
					$sposttype = array();
					$showusers = false; 
					if ( isset( $_GET['post_type'] ) ) $sposttype = $_GET['post_type'];
					if ( get_option('options_forum_support') ) $showforums = true;
					if ( get_option('options_module_staff_directory') ) $showusers = true;
					foreach($postTypes as $pt){
						if( $pt->labels->name > "Staff profiles" && $showusers){
							$showusers = false;
							$checkbox .= '<label class="checkbox"><input type="checkbox" name="include" value="user"';
							if( isset( $_GET['include'] ) && $_GET['include'] == 'user'){ 
								$checkbox .= " checked=\"checked\"";
							}
							$checkbox .= '> <span class="labelForCheck">' . __("Staff profiles" , "govintranet") . '</span></label>';
						}
						if( $pt->labels->name > "Forums" && $showforums){
							$showforums = false;
							$checkbox .= '<label class="checkbox"><input type="checkbox" name="post_type[]" value="forum"';
							if(in_array('forum', $sposttype)){ 
								$checkbox .= " checked=\"checked\"";
							}
							$checkbox .= '> <span class="labelForCheck">' . __("Forums" , "govintranet") . '</span></label>';
							$checkbox .= '<label class="checkbox"><input type="checkbox" name="post_type[]" value="topic"';
							if(in_array('topic', $sposttype)){ 
								$checkbox .= " checked=\"checked\"";
							}
							$checkbox .= '> <span class="labelForCheck">' . __("Forum topics" , "govintranet") . '</span></label>';
							$checkbox .= '<label class="checkbox"><input type="checkbox" name="post_type[]" value="reply"';
							if(in_array('reply', $sposttype)){ 
								$checkbox .= " checked=\"checked\"";
							}
							$checkbox .= '> <span class="labelForCheck">' . __("Forum replies" , "govintranet") . '</span></label>';
						}
						if( $pt->rewrite["slug"] != "spot" ){
							$checkbox .= '<label class="checkbox"><input type="checkbox" name="post_type[]" value="'. $pt->query_var .'"';
							if(in_array($pt->query_var, $sposttype)){ 
								$checkbox .= " checked=\"checked\"";
							}
							$checkbox .= '> <span class="labelForCheck">'. $pt->labels->name .'</span></label>';
						}
					}
					$checkbox .= '<label class="checkbox"><input type="checkbox" name="orderby" value="date"';
					if( isset( $_REQUEST['orderby'] ) && $_REQUEST['orderby'] ){ 
						$checkbox .= " checked=\"checked\"";
					}
					$checkbox .= '<span class="labelForCheck">' . __("Recently published first","govintranet") . '</span></label>';
					echo $checkbox;
					?>
					<br>
					<button  class="btn btn-primary"><?php _e('Refine search','govintranet');?> <i class="dashicons dashicons-search"></i></button>
				</form>
			 </div>
		</div>
	</div>
	<?php wp_reset_postdata(); ?>
	<?php dynamic_sidebar('serp-widget-area'); ?>	
</div>

<?php get_footer(); ?>
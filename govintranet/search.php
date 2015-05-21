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

	<div class="col-lg-7 col-md-8 col-sm-12 white">
		<div class="row">
			<div class='breadcrumbs'>
				<?php if(function_exists('bcn_display') && !is_front_page()) {
						bcn_display();
					}?>
			</div>
		</div>

	<?php 
	$s=get_search_query();
	if ( have_posts() ) : 
		?>
		<h1><?php printf( __( 'Search results for: %s', 'govintranet' ), '' . $s . '' ); ?></h1>
		<?php
		if ( isset( $_GET['pt'] ) && $_GET['pt'] =='forums'){
			echo "<p class='news_date'>Showing results from forums. <a href='".site_url()."/?s=";
			echo the_search_query();
			echo "'>Search the intranet</a></p>";
		}
		if ($wp_query->found_posts>1  && $_GET['include'] != 'user' ){
			echo "<p class='news_date'>Found ".$wp_query->found_posts." results</p>";
		}
	
		/* Run the loop for the search to output the results.
		 * If you want to overload this in a child theme then include a file
		 * called loop-search.php and that will be used instead.
		 */
		 get_template_part( 'loop', 'search' );
	
	 else : 

			$searchnotfound=get_option('options_search_not_found');
			if (!$searchnotfound) $searchnotfound = "Nope";
			?>
			<h1><?php echo $searchnotfound; ?></h1>
			<script type="text/javascript">
			_gaq.push(['_trackEvent', 'Search', 'Empty results', '<?php echo the_search_query();?>']);
			</script>
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
				$searchcon.=' blog posts';
			}
			if (in_array('event', $pt)){
				if ($searchcon) $searchcon.=" or";
				$searchcon.=' events';
			}
			if ( in_array('forum', $pt) || in_array('topic', $pt) || in_array('reply', $pt) ){
				if ($searchcon) $searchcon.=" or";
				$searchcon.=' the forums';
			}	
			if (in_array('jargon-buster', $pt)){
				if ($searchcon) $searchcon.=" or";
				$searchcon.=' jargon busters';
			}
			if (in_array('news', $pt)){
				if ($searchcon) $searchcon.=" or";
				$searchcon.=' news';
			}
			if (in_array('project', $pt)){
				if ($searchcon) $searchcon.=" or";
				$searchcon.=' projects';
			}
			if ($_GET['include']=='user'){
				if ($searchcon) $searchcon.=" or";
				$searchcon.=' the staff directory';
			}					 
			if (in_array('task', $pt)){
				if ($searchcon) $searchcon.=" or";
				$searchcon.=' tasks';
			}
			if (in_array('team', $pt)){
				if ($searchcon) $searchcon.=" or";
				$searchcon.=' teams';
			}
			if (in_array('vacancy', $pt)){
				if ($searchcon) $searchcon.=" or";
				$searchcon.=' vacancies';
			}
		endif; 
			
			?>
			<p>
			<?php 
			if ($pt){
				_e( 'Couldn\'t find anything in ' . $searchcon . '. Try searching the whole intranet.', 'govintranet' ); 
			} else {
				_e( 'Couldn\'t find anything on the intranet like that. Sometimes using fewer words can help.', 'govintranet' ); 				
			}
			?>
			</p>
			<?php
			// add did you mean function
			 if (function_exists('relevanssi_didyoumean')) { 
			 	relevanssi_didyoumean(get_search_query(), "<p>Did you mean: ", "?</p>", 5);
			 }
			?>
			<div class="content-wrapper">
				<div class="col-lg-6">
					<form class="form-horizontal" role="form" id="serps_search" action="<?php echo site_url( '/' ); ?>">
					  <div class="col-lg-12">
					    <div class="input-group">
							<input type="text" class="form-control" placeholder="Search again" name="s" id="snf" value="<?php echo the_search_query();?>">
							<div class="input-group-btn">
								<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span></button>
					      </div><!-- /btn-group -->
					    </div><!-- /input-group -->
					  </div><!-- /.col-lg-6 -->
					</form>
				</div>
			</div>
			<script type='text/javascript'>
			    jQuery(document).ready(function(){
					jQuery('#serps_search').submit(function(e) {
					    if (jQuery.trim(jQuery("#snf").val()) === "") {
					        e.preventDefault();
					        jQuery('#snf').focus();
					    }
					});	
				});	
			
			</script>
	
			<?php 
	endif;
	
	?>
	<div class="wp_pagenavi">
	<?php if (  $wp_query->max_num_pages > 1  && isset( $_GET['pt'] ) && $_GET['pt'] != 'user'  ) : ?>
		<?php if (function_exists('wp_pagenavi')) : ?>
			<?php wp_pagenavi(array('query' => $wp_query)); ?>
			<?php else : ?>
			<?php next_posts_link('&larr; Older items', $wp_query->max_num_pages); ?>
			<?php previous_posts_link('Newer items &rarr;', $wp_query->max_num_pages); ?>						
		<?php endif; ?>
	<?php endif; ?>
	<?php 
	//wp_reset_postdata(); 
	    wp_reset_query();
	?>
	</div>

</div>
<div class="col-lg-4 col-lg-offset-1 col-md-4 col-sm-12">
	<div id="search_filter">
		<div id="accordion">
	      <h3>
	        <a class="accordion-toggle btn btn-success dropdown filter_results" data-toggle="collapse" data-parent="#accordion" href="#collapseFilter">
				Filter results <span class="caret"></span>
	        </a>
	      </h3>
	    </div>
	    <div id="collapseFilter" class="xpanel-collapse<?php echo ' out in';
		    //if (!$_REQUEST['include'] && !$_REQUEST['post_type'] && !$_REQUEST['orderby']) { echo 'collapse out'; } else { echo "out in";}
			    ?>
			    ">
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
							$checkbox .= '> <span class="labelForCheck">Staff profiles</span></label>';
						}
						if( $pt->labels->name > "Forums" && $showforums){
							$showforums = false;
							$checkbox .= '<label class="checkbox"><input type="checkbox" name="post_type[]" value="forum"';
							if(in_array('forum', $sposttype)){ 
								$checkbox .= " checked=\"checked\"";
							}
							$checkbox .= '> <span class="labelForCheck">Forums</span></label>';
							$checkbox .= '<label class="checkbox"><input type="checkbox" name="post_type[]" value="topic"';
							if(in_array('topic', $sposttype)){ 
								$checkbox .= " checked=\"checked\"";
							}
							$checkbox .= '> <span class="labelForCheck">Forum topics</span></label>';
							$checkbox .= '<label class="checkbox"><input type="checkbox" name="post_type[]" value="reply"';
							if(in_array('reply', $sposttype)){ 
								$checkbox .= " checked=\"checked\"";
							}
							$checkbox .= '> <span class="labelForCheck">Forum replies</span></label>';
						}
						if( $pt->rewrite["slug"] != "spot" ){
							$checkbox .= '<label class="checkbox"><input type="checkbox" name="post_type[]" value="'. $pt->query_var .'"';
							if(in_array($pt->query_var, $sposttype)){ 
								$checkbox .= " checked=\"checked\"";
							}
							
							$checkbox .= '> <span class="labelForCheck">'. $pt->labels->name .'</span></label>';// print_r($pt);
						}
					}
					$checkbox .= '<label class="checkbox"><input type="checkbox" name="orderby" value="date"';
					if( isset( $_REQUEST['orderby'] ) && $_REQUEST['orderby'] ){ 
						$checkbox .= " checked=\"checked\"";
					}
					$checkbox .= '<span class="labelForCheck">Recently published first</span></label>';
					echo $checkbox;
					?>
					<br>
					<button  class="btn btn-primary">Refine search <i class="glyphicon glyphicon-search"></i></button>
				</form>
			 </div>
		</div>
	</div>
	<?php wp_reset_postdata(); ?>
	<?php dynamic_sidebar('serp-widget-area'); ?>	
</div>

<?php get_footer(); ?>
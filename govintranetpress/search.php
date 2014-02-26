<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package WordPress
 * @subpackage Bootstrap 3
 */
 
 //****************************************************
 // if only one result found, zoom straight to the page



$gis = "general_intranet_enable_helpful_search";
$gishelpfulsearch = get_option($gis);
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
			header('Location: '.$location);
			exit;
		}
	}
}
				
 //*****************************************************				

get_header(); 

?>

	<div class="col-lg-7 col-md-8 col-sm-12 white">
		<div class="row">
			<div class='breadcrumbs'>
				<?php if(function_exists('bcn_display') && !is_front_page()) {
						bcn_display();
					}?>
			</div>
		</div>
	

<?php 
	$s=$_GET['s'];
	if ( have_posts() ) : 
	
		if ($_GET['pt']=='forums'){
			$query->query_vars['s'] = $s;
			$query->query_vars['posts_per_page'] = 10;
			$query->query_vars['post_type'] = array('forum','topic','reply');
			if (function_exists('relevanssi_do_query')){
				relevanssi_do_query($query);
			}

		}

		if ($_GET['pt']=='user'):
			global $foundstaff;
			$foundstaff = 0;
			$query->query_vars['s'] = $s;
			$query->query_vars['posts_per_page'] = -1;
			$query->query_vars['post_type'] = array('user','team');
			if (function_exists('relevanssi_do_query')){
				relevanssi_do_query($query);
			}
		endif;
	
?>
	<h1><?php printf( __( 'Search results for: %s', 'twentyten' ), '' . $s . '' ); ?></h1>
	<?php
	if ($_GET['pt']=='forums'){
		echo "<p class='news_date'>Showing results from forums. <a href='".site_url()."/?s=".$_GET['s']."'>Search the intranet</a></p>";
	}
	if ($wp_query->found_posts>1  && $_GET['pt'] != 'user' ){
		echo "<p class='news_date'>Found ".$wp_query->found_posts." results</p>";
	}

	if ($_GET['pt'] == 'user' ):

?>
		<form class="form-horizontal" role="form" id="searchform2" name="searchform2" action="<?php echo site_url( '/' ); ?>">
		  <div class="col-lg-12">
			<div id="staff-search" class="well">
					<div class="input-group">
				    	 <input type="text" class="form-control typeahead" placeholder="Search" name="s" id="s2" value="<?php echo $_GET['s'];?>">
				    	 <input type="hidden" name="pt" value="user">
						 <span class="input-group-btn">
						 <button class="btn btn-primary" type="submit"><i class="glyphicon glyphicon-search"></i></button>
						 </span>
					</div><!-- /input-group -->
			  </div>
		  </div>
		</form>
<?php

	endif;
	
/* Run the loop for the search to output the results.
 * If you want to overload this in a child theme then include a file
 * called loop-search.php and that will be used instead.
 */
 get_template_part( 'loop', 'search' );
?>
<?php else : 
	if ($_GET['pt'] != 'user'):
	
		$searchnotfound=get_option('general_intranet_search_not_found');
		if (!$searchnotfound) $searchnotfound = "Nope";
	
?>
	<h1><?php echo $searchnotfound; ?></h1>
	<script type="text/javascript">
	_gaq.push(['_trackEvent', 'Search', 'Empty results', '<?php echo $_GET['s'];?>']);
	</script>
	<?php 
	$pt = $_GET['post_type'];
	$ct = $_GET['cat'];
	$ct = get_category($ct);
	$ct = $ct->name;
	if ($ct){
		$searchcon=$ct;
	}
	if ($pt=='task'){
		$searchcon.=' tasks';
	}
	if ($pt=='projects'){
		$searchcon.=' projects';
	}
	if ($pt=='vacancies'){
		$searchcon.=' vacancies';
	}
	if ($_GET['pt']=='forums'){
		$searchcon.=' the forums';
		$pt=$_GET['pt'];
	}					 ?>
	<p><?php 
	if ($pt){
	_e( 'Couldn\'t find anything in ' . $searchcon . ' like that. Try searching the whole intranet.', 'twentyten' ); 
	}
	else
	{
	_e( 'Couldn\'t find anything on the intranet like that. Sometimes using less words can help.', 'twentyten' ); 				
	}
	?>
	
	</p>
	

<?php
$q = $_GET['s'];

// add did you mean function
 if (function_exists('relevanssi_didyoumean')) { 
 	relevanssi_didyoumean(get_search_query(), "<p>Did you mean: ", "?</p>", 5);
 }

?>
	<div class="content-wrapper">
		<div class="col-lg-6">
			<form class="form-horizontal" role="form" action="<?php echo site_url( '/' ); ?>">
			  <div class="col-lg-12">
			    <div class="input-group">
					<input type="text" class="form-control" placeholder="Search again" name="s" id="snf" value="<?php echo $_GET['s'];?>">
					<div class="input-group-btn">
						<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span></button>
			      </div><!-- /btn-group -->
			    </div><!-- /input-group -->
			  </div><!-- /.col-lg-6 -->
			</form>
		</div>
	</div>
	<script>
	jQuery("#snf").focus();
	</script>							

<?php endif; 
endif;
	if ($_GET['pt'] == 'user' && $foundstaff == 0):
		echo "<h2>Not on the directory</h2><p>Try searching again or go back to the <a href='".site_url()."/staff-directory'>staff directory</a></p><br>";
	endif;
	
	
?>
		<div class="wp_pagenavi">
<?php if (  $items->max_num_pages > 1  && $_GET['pt'] != 'user'  ) : ?>
	<?php if (function_exists(wp_pagenavi)) : ?>
		<?php wp_pagenavi(array('query' => $items)); ?>
		<?php else : ?>

		<?php next_posts_link('&larr; Older items', $items->max_num_pages); ?>
		<?php previous_posts_link('Newer items &rarr;', $items->max_num_pages); ?>						
	
	<?php endif; ?>
<?php endif; ?>

<?php 
//wp_reset_postdata(); 
    wp_reset_query();
?>

<?php
	$gis = "general_intranet_forum_support";
	$forumsupport = get_option($gis);
	if ( $_GET['pt'] != 'forums' && $forumsupport && $_GET['pt'] != 'user' ){
		echo "<br><br><p class='news_date'><a href='".site_url()."/?s={$s}&pt=forums'>Search in forums</a></p>";
	}
?>
		</div>

</div>
<div class="col-lg-4 col-lg-offset-1 col-md-4 col-sm-12">
	<?php 	dynamic_sidebar('serp-widget-area'); ?>	
</div>

<?php get_footer(); ?>

<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package WordPress
 * @subpackage Bootstrap 3
 *
 */
 
 //****************************************************
 // if only one result found, zoom straight to the page

$gishelpfulsearch = get_option("options_enable_helpful_search");
if ($gishelpfulsearch == 1):
	if ($wp_query->found_posts==1):
		$location='';
		while ( have_posts() ) : 
			the_post(); 
			if ($post->post_type == 'user' ):
				$userid = $post->user_id;
				$location = get_author_posts_url( $post->user_id ); 
				$staffdirectory = get_option('options_module_staff_directory');
				if (function_exists('bp_activity_screen_index')): // if using BuddyPress - link to the members page
					$location=str_replace('/author', '/members', $location); 
				elseif (function_exists('bbp_get_displayed_user_field') && $staffdirectory ): // if using bbPress - link to the staff page
					$location=str_replace('/author', '/staff', $location); 
				elseif (function_exists('bbp_get_displayed_user_field') ): // if using bbPress - link to the staff page
					$location=str_replace('/author', '/users', $location);
				endif;
			elseif ($_GET['pt'] != 'user'):
					$location=get_permalink($post->ID); 
			endif;
		endwhile;
		if ($location):
			$location = str_replace( "#038;", "&", $location);
			$location = str_replace( "&amp;", "&", $location);
			header('Location: '.$location);
			exit;
		endif;
	endif;
endif;
$include_attachments = get_option("options_enable_include_attachments");
$include_forums = get_option("options_enable_include_forums");				
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
	$ptargs = array( '_builtin' => false, 'public' => true, 'exclude_from_search' => false );
	$postTypes = get_post_types($ptargs, 'objects');
	$checkbox = "";
	ksort($postTypes); 
	$sposttype = array();
	$showusers = false; 
	$showforums = false;
	$is_filtered = false;
	if ( isset( $_GET['post_types'] ) ) $sposttype = $_GET['post_types'];
	if ( get_option('options_forum_support') ) $showforums = true;
	if ( get_option('options_module_staff_directory') ) $showusers = true;
	foreach($postTypes as $pt){
		if( $pt->rewrite["slug"] != "spot" ){
			$checkbox .= '<label class="checkbox filter-'. $pt->query_var .'"><input type="checkbox" name="post_types[]" value="'. $pt->query_var .'" id="filter-check-'. $pt->query_var .'"';
			if(in_array($pt->query_var, $sposttype)){ 
				$checkbox .= " checked=\"checked\"";
				$is_filtered = true;
			}
			$checkbox .= '> <span class="labelForCheck">'. $pt->labels->name .'</span></label>';
			$hidden.='<input type="hidden" name="post_types[]" id="search-filter-'. $pt->query_var .'">';
		}
	}
	if( $showusers){
			$showusers = false;
			$checkbox .= '<label class="checkbox filter-user"><input type="checkbox" name="include" value="user" id="filter-check-include"';
			if( isset( $_GET['include'] ) && $_GET['include'] == 'user'){ 
				$checkbox .= " checked=\"checked\"";
				$is_filtered = true;
			}
			$checkbox .= '> <span class="labelForCheck">' . __("Staff profiles" , "govintranet") . '</span></label>';
			$hidden.='<input type="hidden" name="include" id="search-filter-include">';
	}
	if( $pt->labels->name > "Forums" && $showforums && $include_forums ){
			$showforums = false;
			$checkbox .= '<label class="checkbox filter-forum"><input type="checkbox" name="post_types[]" value="forum" id="filter-check-forum"';
			if(in_array('forum', $sposttype)){ 
				$checkbox .= " checked=\"checked\"";
				$is_filtered = true;
			}
			$checkbox .= '> <span class="labelForCheck">' . __("Forums" , "govintranet") . '</span></label>';
			$hidden.='<input type="hidden" name="post_types[]" id="search-filter-forum">';
			
			$checkbox .= '<label class="checkbox filter-topic"><input type="checkbox" name="post_types[]" value="topic" id="filter-check-topic"';
			if(in_array('topic', $sposttype)){ 
				$checkbox .= " checked=\"checked\"";
				$is_filtered = true;
			}
			$checkbox .= '> <span class="labelForCheck">' . __("Forum topics" , "govintranet") . '</span></label>';
			$hidden.='<input type="hidden" name="post_types[]" id="search-filter-topic">';
			
			$checkbox .= '<label class="checkbox filter-reply"><input type="checkbox" name="post_types[]" value="reply" id="filter-check-reply"';
			if(in_array('reply', $sposttype)){ 
				$checkbox .= " checked=\"checked\"";
				$is_filtered = true;
			}
			$checkbox .= '> <span class="labelForCheck">' . __("Forum replies" , "govintranet") . '</span></label>';
			$hidden.='<input type="hidden" name="post_types[]" id="search-filter-reply">';
	}
	if ( $include_attachments ){
		$checkbox .= '<label class="checkbox filter-attachment"><input type="checkbox" name="post_types[]" value="attachment" id="filter-check-attachment"';
		if(in_array('attachment', $sposttype)){ 
			$checkbox .= " checked=\"checked\"";
			$is_filtered = true;
		}
		$checkbox .= '> <span class="labelForCheck">' . __("Media" , "govintranet") . '</span></label>';
		$hidden.='<input type="hidden" name="post_types[]" id="search-filter-attachment">';
	}
	$checkbox .= '<label class="checkbox"><input type="checkbox" name="orderby" value="date" id="filter-check-orderby"';
	if( isset( $_REQUEST['orderby'] ) && $_REQUEST['orderby'] ){ 
		$checkbox .= " checked=\"checked\"";
		$is_filtered = true;
	}
	$checkbox .= '><span class="labelForCheck">' . __("Recently published first","govintranet") . '</span></label>';
	$hidden.='<input type="hidden" name="orderby" id="search-filter-orderby">';

	if ( !have_posts() ) : 

		$searchnotfound = get_option('options_search_not_found');
		$track_homepage = get_option('options_track_homepage');
		if ( !$track_homepage ) echo get_option('options_google_tracking_code');
		if ( !$searchnotfound ) $searchnotfound = _x("Nope","search not found","govintranet");
		?>
		<h1><?php echo $searchnotfound; ?></h1>
		<?php 
		$pt="";
		$ct = $_GET['cat'];
		$ct = get_category($ct);
		$ct = $ct->name;
		$searchcon = "";
		if ($ct) $searchcon=$ct;
		
		if ( isset( $_GET['post_types'] ) ):
			$pt = $_GET['post_types']; 
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
			if (in_array('news-update', $pt)){
				if ($searchcon) $searchcon.=" or";
				$searchcon.=" " . __('news updates','govintranet');
			}
			if (in_array('project', $pt)){
				if ($searchcon) $searchcon.=" or";
				$searchcon.=" " . __('projects','govintranet');
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
			if (in_array('attachment', $pt)){
				if ($searchcon) $searchcon.=" or";
				$searchcon.=" " . __('media','govintranet');
			}
		endif; 
		if ($_GET['include']=='user'){
			if ($searchcon) $searchcon.=" or";
			$searchcon.=" " . __('the staff directory','govintranet');
		}					 
		
		echo "<p>";
		if ($pt && !$searchcon){
			 _e( 'Couldn\'t find anything like that. Try searching the whole intranet.', 'govintranet' ) ; 
		} elseif ($pt) {
			 printf(__( 'Couldn\'t find anything in %s. Try searching the whole intranet.', 'govintranet' ) , $searchcon) ; 
		} else {
			_e( 'Couldn\'t find anything on the intranet like that. Sometimes using fewer words can help.', 'govintranet' ); 				
		}
		echo "</p>";

		if ( function_exists('relevanssi_didyoumean') && !get_option("options_disable_search_did_you_mean", true ) ) { 
			relevanssi_didyoumean(get_search_query(), "<div class='did_you_mean'><h2>" . __('Did you mean?','govintranet') . "</h2><p> ", "</p></div>", 5);
		}

		?>
		<div class="well well-sm search-again-wrapper-not-found">
		<form class="form" role="form" id="serps_search" action="<?php echo site_url( '/' ); ?>">
			<div class="form-group">
		    <label for="nameSearch"><?php _e('Search again','govintranet'); ?></label>
			<input type="text" class="form-control" placeholder="<?php _e('Search again','govintranet'); ?>" name="s" id="nameSearch" value="<?php echo the_search_query();?>">
			</div>
			<div class="form-group">
			<button id="search-again-button" type="submit" class="btn btn-primary"><?php _e('Search again','govintranet'); ?></button>
			</div>
			<input type="hidden" name="cat" value="<?php echo esc_attr($_GET['cat']); ?>" id="search-filter-cat">
			<?php echo $hidden; ?>
		</form>
		</div>

		<script type='text/javascript'>
		    jQuery(document).ready(function(){
				if (typeof(_gaq) !== 'undefined') {
					_gaq.push(['_trackEvent', 'Search', 'Empty results', '<?php echo the_search_query();?>']);
				}
				if (typeof(ga) !== 'undefined') { 
					ga('send', 'event', 'Search', 'Empty results', '<?php echo the_search_query();?>');
				}
			});	
		
		</script>
		<?php
				
	else:

		if ( $is_filtered ): ?>
			<div class="well well-sm search-again-wrapper-filtered">
			<form class="form-inline" role="form" id="serps_search" action="<?php echo site_url( '/' ); ?>">
				<div class="form-group">
			    <label class="sr-only" for="nameSearch"><?php _e('Search again','govintranet'); ?></label>
				<input type="text" class="form-control" placeholder="<?php _e('Search again','govintranet'); ?>" name="s" id="nameSearch" value="<?php echo the_search_query();?>">
				</div>
				<button id="search-again-button" type="submit" class="btn btn-primary"><?php _e('Search again','govintranet'); ?></button>
				<input type="hidden" name="cat" value="<?php echo esc_attr($_GET['cat']); ?>" id="search-filter-cat">
				<?php echo $hidden; ?>
			</form>
			</div>
			<?php		
		endif;
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
		} elseif ($wp_query->found_posts > 1 ){
			echo "<p class='news_date'>";
			printf( __('Found %d results' , 'govintranet' ) , $wp_query->found_posts );
			echo "</p>";
		}
				
		if ( $paged > 1 ):
			?>
			<div class="wp_pagenavi">
			<?php if (  $wp_query->max_num_pages > 1  ) : ?>
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
			
			<?php
		endif;
			
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
					<input type="hidden" name="s" value="<?php echo get_search_query(); ?>" id = "search-filter-s">
					<input type="hidden" name="paged" value="1">
					<input type="hidden" name="cat" value="<?php echo esc_attr($_GET['cat']); ?>" id="filter-check-cat">
					<?php echo $checkbox; ?>
					<br>
					<button  class="btn btn-primary"><?php _e('Refine search','govintranet');?> <i class="dashicons dashicons-search"></i></button>
				</form>
			 </div>
		</div>
	</div>
	<?php wp_reset_postdata(); ?>
	<?php dynamic_sidebar('serp-widget-area'); ?>	
</div>
<script type='text/javascript'>
    jQuery(document).ready(function(){

		/* pull filter checkboxes on requery form load */

		<?php
		foreach($postTypes as $pt){
			echo '
			if ( jQuery( "#filter-check-'.$pt->rewrite["slug"].'" ).attr("checked")){
				jQuery("#search-filter-'.$pt->rewrite["slug"].'").val("'.$pt->rewrite["slug"].'");	
			} else {
				jQuery("#search-filter-'.$pt->rewrite["slug"].'").val("");
			}
			';
		}
		?>				
		if ( jQuery( "#filter-check-include" ).attr("checked")){
			jQuery("#search-filter-include").val("user");	
		} else {
			jQuery("#search-filter-include").val("");
		}
		if ( jQuery( "#filter-check-page" ).attr("checked")){
			jQuery("#search-filter-page").val("page");	
		} else {
			jQuery("#search-filter-page").val("");
		}
		if ( jQuery( "#filter-check-orderby" ).attr("checked")){
			jQuery("#search-filter-orderby").val("date");	
		} else {
			jQuery("#search-filter-orderby").val("");
		}

		/* push filter checkboxes to search requery form when clicked */
		<?php
		foreach($postTypes as $pt){
			echo '
			jQuery( "#filter-check-' . $pt->rewrite['slug'] . '" ).click(function() {
				if ( jQuery( "#filter-check-' . $pt->rewrite['slug'] . '" ).attr("checked")){
					jQuery("#search-filter-' . $pt->rewrite['slug'] . '").val("' . $pt->rewrite['slug'] . '");	
				} else {
					jQuery("#search-filter-' . $pt->rewrite['slug'] . '").val("");
				}';
			if ( $pt->rewrite['slug'] == "task" ){
				echo 'if ( jQuery( "#filter-check-task" ).attr("checked") ){
					return; }
					else {
				jQuery("#search-filter-cat").remove();
				jQuery("#filter-check-cat").remove();
				}';
			}
			echo '
			});
			';
		}
		?>
		jQuery( "#filter-check-include" ).click(function() {
			if ( jQuery( "#filter-check-include" ).attr("checked")){
				jQuery("#search-filter-include").val("user");	
			} else {
				jQuery("#search-filter-include").val("");
			}
		});
		jQuery( "#filter-check-page" ).click(function() {
			if ( jQuery( "#filter-check-page" ).attr("checked")){
				jQuery("#search-filter-page").val("page");	
			} else {
				jQuery("#search-filter-page").val("");
			}
		});
		jQuery( "#filter-check-orderby" ).click(function() {
			if ( jQuery( "#filter-check-orderby" ).attr("checked")){
				jQuery("#search-filter-orderby").val("date");	
			} else {
				jQuery("#search-filter-orderby").val("");
			}
		});
		jQuery( "#filter-check-forum" ).click(function() {
			if ( jQuery( "#filter-check-forum" ).attr("checked")){
				jQuery("#search-filter-forum").val("forum");	
			} else {
				jQuery("#search-filter-forum").val("");
			}
		});
		jQuery( "#filter-check-topic" ).click(function() {
			if ( jQuery( "#filter-check-topic" ).attr("checked")){
				jQuery("#search-filter-topic").val("topic");	
			} else {
				jQuery("#search-filter-topic").val("");
			}
		});
		jQuery( "#filter-check-reply" ).click(function() {
			if ( jQuery( "#filter-check-reply" ).attr("checked")){
				jQuery("#search-filter-reply").val("reply");	
			} else {
				jQuery("#search-filter-reply").val("");
			}
		});
		jQuery( "#filter-check-attachment" ).click(function() {
			if ( jQuery( "#filter-check-attachment" ).attr("checked")){
				jQuery("#search-filter-attachment").val("attachment");	
			} else {
				jQuery("#search-filter-attachment").val("");
			}
		});

		/* push search query back to filter box */
		jQuery( "#nameSearch" ).change(function() {
			sq = jQuery( "#nameSearch" ).val();
			jQuery("#search-filter-s").val(sq);
		});
		
		/* remove fields if all blank */
		jQuery( "#search-again-button" ).click(function() {
			<?php
			foreach($postTypes as $pt){
				echo '
				if ( jQuery( "#filter-check-'.$pt->rewrite["slug"].'" ).attr("checked") != "checked" ){ jQuery( "#search-filter-'.$pt->rewrite["slug"].'" ).remove();	}
				';
			}	
			?>		
			if ( jQuery( "#filter-check-page" ).attr("checked") != "checked"){jQuery( "#search-filter-page" ).remove();	}
			if ( jQuery( "#filter-check-forum" ).attr("checked") != "checked"){ jQuery( "#search-filter-forum" ).remove();}
			if ( jQuery( "#filter-check-topic" ).attr("checked") != "checked"){ jQuery( "#search-filter-topic" ).remove();}
			if ( jQuery( "#filter-check-reply" ).attr("checked") != "checked"){ jQuery( "#search-filter-reply" ).remove();}
			if ( jQuery( "#filter-check-include" ).attr("checked") != "checked"){ jQuery("#search-filter-include").remove(); }
			if ( jQuery( "#filter-check-media" ).attr("checked") != "checked"){ jQuery("#search-filter-media").remove(); }
			if ( jQuery( "#filter-check-attachment" ).attr("checked") != "checked"){ jQuery("#search-filter-attachment").remove(); }
			if ( jQuery( "#filter-check-orderby" ).attr("checked") != "checked"){ jQuery("#search-filter-orderby").remove(); }
		});

		/* stop a blank search */
		jQuery('#serps_search').submit(function(e) {
		    if (jQuery.trim(jQuery("#nameSearch").val()) === "") {
		        e.preventDefault();
		        jQuery('#nameSearch').focus();
		    }
		});	
	});
</script>
<?php get_footer(); ?>
<?php

/* Template name: Aggregator page */
/* Allows 3 columns of custom placeholder areas for displaying categorised listings and free-format content */

get_header(); 

if ( have_posts() ) while ( have_posts() ) : the_post(); 

$maincontent = get_the_content($id); 
global $id;
global $colid;	
global $post;
global $title;
global $team;
global $checkteam;
global $tax;
global $tag;
global $n2n;
global $freshness;
global $num;
global $compact;
global $freeformat;
global $alreadyshown;
global $directorystyle;
global $showmobile;
global $teamid;
global $teamleaderid;
global $link;
global $cat_id;
global $doctyp;
global $gallery; 
global $showthumbnail;
global $showcalendar;
global $showlocation;

remove_filter('pre_get_posts', 'filter_search');

function filter_news($query) {
    if ($query->is_tag && !is_admin()) {
		$query->set('post_type', array('news'));
    }
    return $query;
}; 

function filter_tasks($query) {
    if ($query->is_tag && !is_admin()) {
		$query->set('post_type', array('task'));
    }
    return $query;
}; 

?>
<div id="home-col-1" class="col-lg-6 col-md-6 col-sm-7">

<?php
// COLUMN 1
// check if the flexible content field has rows of data
if( have_rows('aggregator_column_1') ):
	?>

	<?php if ( $maincontent ): ?>
		<div class="category-block">
		<?php echo apply_filters("the_content", $maincontent); ?>
		</div>
	<?php endif; ?>

	<?php
	// loop through the rows of data
    while ( have_rows('aggregator_column_1') ) : 
    the_row();


		$title = '';
		$link = '';
		$gallery = '';
		$team = '';
		$freeformat = '';
		$num = '';
		$tag = '';
		$type = '';
		$freshness = '';
		$n2n = '';
		$tax = '';
		$compact = '';
		$aoptions='';
		$colid = 1;
		

		// NEWS LISTING
		
        if ( get_row_layout() == 'aggregator_news_listing' ) :
			$title = get_sub_field('aggregator_listing_title');
			$team = get_sub_field('aggregator_listing_team');
			$checkteam = $team[0]; 
			$tax = get_sub_field('aggregator_listing_tax');
			$tag = get_sub_field('aggregator_listing_tag');
			$n2n = get_sub_field('aggregator_listing_need_to_know');
			$freshness = intval(get_sub_field('aggregator_listing_freshness'));
			$num = intval(get_sub_field('aggregator_listing_number')); 
			if ( !$num ) $num = -1; 
			$compact = get_sub_field('aggregator_listing_compact_list');
			if ( $compact ) $larray = array();
        	get_template_part('page-aggregator/part-news-listing');
		
		// TASK LISTING
		
        elseif ( get_row_layout() == 'aggregator_task_listing' ): 
        	$title = get_sub_field('aggregator_listing_title');
			$type = get_sub_field('aggregator_listing_type');
			$team = get_sub_field('aggregator_listing_team');
			$checkteam = $team[0]; 
			$tax =  get_sub_field('aggregator_listing_tax');
			$tag =  get_sub_field('aggregator_listing_tag');
			$compact = get_sub_field('aggregator_listing_compact_list');
			if ( $compact ) $larray = array();
        	get_template_part('page-aggregator/part-task-listing');

		// BLOG LISTING
		
        elseif ( get_row_layout() == 'aggregator_blog_listing' ) :
			$title = get_sub_field('aggregator_listing_title');
			$team = get_sub_field('aggregator_listing_team');
			$checkteam = $team[0]; 
			$freshness = intval(get_sub_field('aggregator_listing_freshness'));
			$num = intval(get_sub_field('aggregator_listing_number')); 
			if ( !$num ) $num = -1; 
			$compact = get_sub_field('aggregator_listing_compact_list');
			if ( $compact ) $larray = array();
        	get_template_part('page-aggregator/part-blog-listing');


		// FREE-FORMAT AREA

        elseif ( get_row_layout() == 'aggregator_free_format_area' ): 
        	$freeformat = get_sub_field('aggregator_free_format_area_content');
        	get_template_part('page-aggregator/part-free-format');


		// TEAM LISTING

        elseif ( get_row_layout() == 'aggregator_team_listing' ): 
			$alreadyshown = array();
			$directorystyle = get_option('options_staff_directory_style'); // 0 = squares, 1 = circles
			$showmobile = get_option('options_show_mobile_on_staff_cards'); // 1 = show
			$team = get_sub_field('aggregator_listing_team');
        	$title = get_sub_field('aggregator_listing_title');
			$teamid = $team[0]; 
			$teamleaderid = get_post_meta($teamid, 'team_lead', true);
        	get_template_part('page-aggregator/part-team-listing');


		// LINKS

        elseif ( get_row_layout() == 'aggregator_link_listing' ): 
			$link = get_sub_field('aggregator_listing_link');
        	$title = get_sub_field('aggregator_listing_title');
        	get_template_part('page-aggregator/part-links-listing');


		// DOCUMENT LINKS

        elseif ( get_row_layout() == 'aggregator_document_listing' ): 
			$cat_id = get_sub_field('aggregator_listing_category'); 
			if ( !$cat_id ) $cat_id = "any";
			$doctyp = get_sub_field('aggregator_listing_doctype');
			if ( !$doctyp ) $doctyp = "any";
        	$title = get_sub_field('aggregator_listing_title');
        	get_template_part('page-aggregator/part-document-listing');


		// GALLERY

        elseif ( get_row_layout() == 'aggregator_gallery' ): 
			$gallery = get_sub_field('aggregator_gallery_images');
        	$title = get_sub_field('aggregator_listing_title');
        	get_template_part('page-aggregator/part-gallery');
        	

		// EVENT LISTING
		
        elseif ( get_row_layout() == 'aggregator_event_listing' ) : 
			$title = get_sub_field('aggregator_listing_title');
			$team = get_sub_field('aggregator_listing_team');
			$checkteam = $team[0]; 
			$num = (string)get_sub_field('aggregator_listing_number'); 
			if ( !$num ) $num = -1; 
			$aoptions = get_sub_field('aggregator_listing_options'); 
			if ( in_array( "Calendar" , $aoptions )) $showcalendar = "on";
			if ( in_array( "Thumbnail" , $aoptions )) $showthumbnail = "on";
			if ( in_array( "Location" , $aoptions )) $showlocation = "on";
        	get_template_part('page-aggregator/part-event-listing');
        	
		endif;

	endwhile;?>
	<?php
endif;
?>
</div>
<div id="home-col-span-2" class="col-lg-6 col-md-6 col-sm-5">

<?php

// HERO COLUMN 
// check if the flexible content field has rows of data
if( have_rows('aggregator_column_hero') ):
	?>
	<div id="home-col-hero" class="col-lg-12 col-md-12 col-sm-12">

	<?php
	// loop through the rows of data
	
    while ( have_rows('aggregator_column_hero') ) : the_row();

		$title = '';
		$link = '';
		$gallery = '';
		$team = '';
		$freeformat = '';
		$num = '';
		$tag = '';
		$type = '';
		$freshness = '';
		$n2n = '';
		$tax = '';

		// NEWS LISTING

        if ( get_row_layout() == 'aggregator_news_listing' ) :
			$title = get_sub_field('aggregator_listing_title');
			$team = get_sub_field('aggregator_listing_team');
			$checkteam = $team[0]; 
			$tax = get_sub_field('aggregator_listing_tax');
			$tag = get_sub_field('aggregator_listing_tag');
			$n2n = get_sub_field('aggregator_listing_need_to_know');
			$freshness = intval(get_sub_field('aggregator_listing_freshness'));
			$num = intval(get_sub_field('aggregator_listing_number')); 
			if ( !$num ) $num = -1; 
			$compact = get_sub_field('aggregator_listing_compact_list');
			if ( $compact ) $larray = array();
        	get_template_part('page-aggregator/part-news-listing');
		
		
		// TASK LISTING
		
        elseif ( get_row_layout() == 'aggregator_task_listing' ): 
        	$title = get_sub_field('aggregator_listing_title');
			$type = get_sub_field('aggregator_listing_type');
			$team = get_sub_field('aggregator_listing_team');
			$checkteam = $team[0]; 
			$tax =  get_sub_field('aggregator_listing_tax');
			$tag =  get_sub_field('aggregator_listing_tag');
			$compact = get_sub_field('aggregator_listing_compact_list');
			if ( $compact ) $larray = array();
        	get_template_part('page-aggregator/part-task-listing');


		// BLOG LISTING
		
        elseif ( get_row_layout() == 'aggregator_blog_listing' ) :
			$title = get_sub_field('aggregator_listing_title');
			$team = get_sub_field('aggregator_listing_team');
			$checkteam = $team[0]; 
			$freshness = intval(get_sub_field('aggregator_listing_freshness'));
			$num = intval(get_sub_field('aggregator_listing_number')); 
			if ( !$num ) $num = -1; 
			$compact = get_sub_field('aggregator_listing_compact_list');
			if ( $compact ) $larray = array();
        	get_template_part('page-aggregator/part-blog-listing');


		// FREE-FORMAT AREA

        elseif ( get_row_layout() == 'aggregator_free_format_area' ): 
        	$freeformat = get_sub_field('aggregator_free_format_area_content');
        	get_template_part('page-aggregator/part-free-format');


		// TEAM LISTING

        elseif ( get_row_layout() == 'aggregator_team_listing' ): 
			$alreadyshown = array();
			$directorystyle = get_option('options_staff_directory_style'); // 0 = squares, 1 = circles
			$showmobile = get_option('options_show_mobile_on_staff_cards'); // 1 = show
			$team = get_sub_field('aggregator_listing_team');
        	$title = get_sub_field('aggregator_listing_title');
			$teamid = $team[0]; 
			$teamleaderid = get_post_meta($teamid, 'team_lead', true);
        	get_template_part('page-aggregator/part-team-listing');


		// LINKS

        elseif ( get_row_layout() == 'aggregator_link_listing' ): 
			$link = get_sub_field('aggregator_listing_link');
        	$title = get_sub_field('aggregator_listing_title');
        	get_template_part('page-aggregator/part-links-listing');


		// DOCUMENT LINKS

        elseif ( get_row_layout() == 'aggregator_document_listing' ): 
			$cat_id = get_sub_field('aggregator_listing_category'); 
			if ( !$cat_id ) $cat_id = "any";
			$doctyp = get_sub_field('aggregator_listing_doctype');
			if ( !$doctyp ) $doctyp = "any";
        	$title = get_sub_field('aggregator_listing_title');
        	get_template_part('page-aggregator/part-document-listing');


		// GALLERY

        elseif ( get_row_layout() == 'aggregator_gallery' ): 
			$gallery = get_sub_field('aggregator_gallery_images');
        	$title = get_sub_field('aggregator_listing_title');
        	get_template_part('page-aggregator/part-gallery');


		// EVENT LISTING
		
        elseif ( get_row_layout() == 'aggregator_event_listing' ) :
			$title = get_sub_field('aggregator_listing_title');
			$colid = "hero";
			$team = get_sub_field('aggregator_listing_team');
			$checkteam = $team[0]; 
			$num = intval(get_sub_field('aggregator_listing_number')); 
			if ( !$num ) $num = -1; 
			$aoptions = get_sub_field('aggregator_listing_options');
			if ( in_array( "Calendar" , $aoptions )) $showcalendar = "on";
			if ( in_array( "Thumbnail" , $aoptions )) $showthumbnail = "on";
			if ( in_array( "Location" , $aoptions )) $showlocation = "on";
        	get_template_part('page-aggregator/part-event-listing');


		endif;


	endwhile;?>
	</div>
	<?php
endif;


// COLUMN 2
// check if the flexible content field has rows of data
if( have_rows('aggregator_column_2') ):
	?>
	<div id="home-col-2" class="col-lg-6 col-md-6 col-sm-12">

	<?php
	// loop through the rows of data
	
    while ( have_rows('aggregator_column_2') ) : the_row();

		$title = '';
		$link = '';
		$gallery = '';
		$team = '';
		$freeformat = '';
		$num = '';
		$tag = '';
		$type = '';
		$freshness = '';
		$n2n = '';
		$tax = '';

		// NEWS LISTING

        if ( get_row_layout() == 'aggregator_news_listing' ) :
			$title = get_sub_field('aggregator_listing_title');
			$team = get_sub_field('aggregator_listing_team');
			$checkteam = $team[0]; 
			$tax = get_sub_field('aggregator_listing_tax');
			$tag = get_sub_field('aggregator_listing_tag');
			$n2n = get_sub_field('aggregator_listing_need_to_know');
			$freshness = intval(get_sub_field('aggregator_listing_freshness'));
			$num = intval(get_sub_field('aggregator_listing_number')); 
			if ( !$num ) $num = -1; 
			$compact = get_sub_field('aggregator_listing_compact_list');
			if ( $compact ) $larray = array();
        	get_template_part('page-aggregator/part-news-listing');
		
		
		// TASK LISTING
		
        elseif ( get_row_layout() == 'aggregator_task_listing' ): 
        	$title = get_sub_field('aggregator_listing_title');
			$type = get_sub_field('aggregator_listing_type');
			$team = get_sub_field('aggregator_listing_team');
			$checkteam = $team[0]; 
			$tax =  get_sub_field('aggregator_listing_tax');
			$tag =  get_sub_field('aggregator_listing_tag');
			$compact = get_sub_field('aggregator_listing_compact_list');
			if ( $compact ) $larray = array();
        	get_template_part('page-aggregator/part-task-listing');


		// BLOG LISTING
		
        elseif ( get_row_layout() == 'aggregator_blog_listing' ) :
			$title = get_sub_field('aggregator_listing_title');
			$team = get_sub_field('aggregator_listing_team');
			$checkteam = $team[0]; 
			$freshness = intval(get_sub_field('aggregator_listing_freshness'));
			$num = intval(get_sub_field('aggregator_listing_number')); 
			if ( !$num ) $num = -1; 
			$compact = get_sub_field('aggregator_listing_compact_list');
			if ( $compact ) $larray = array();
        	get_template_part('page-aggregator/part-blog-listing');


		// FREE-FORMAT AREA

        elseif ( get_row_layout() == 'aggregator_free_format_area' ): 
        	$freeformat = get_sub_field('aggregator_free_format_area_content');
        	get_template_part('page-aggregator/part-free-format');


		// TEAM LISTING

        elseif ( get_row_layout() == 'aggregator_team_listing' ): 
			$alreadyshown = array();
			$directorystyle = get_option('options_staff_directory_style'); // 0 = squares, 1 = circles
			$showmobile = get_option('options_show_mobile_on_staff_cards'); // 1 = show
			$team = get_sub_field('aggregator_listing_team');
        	$title = get_sub_field('aggregator_listing_title');
			$teamid = $team[0]; 
			$teamleaderid = get_post_meta($teamid, 'team_lead', true);
        	get_template_part('page-aggregator/part-team-listing');


		// DOCUMENT LINKS

        elseif ( get_row_layout() == 'aggregator_document_listing' ): 
			$cat_id = get_sub_field('aggregator_listing_category'); 
			if ( !$cat_id ) $cat_id = "any";
			$doctyp = get_sub_field('aggregator_listing_doctype');
			if ( !$doctyp ) $doctyp = "any";
        	$title = get_sub_field('aggregator_listing_title');
        	get_template_part('page-aggregator/part-document-listing');


		// LINKS

        elseif ( get_row_layout() == 'aggregator_link_listing' ): 
			$link = get_sub_field('aggregator_listing_link');
        	$title = get_sub_field('aggregator_listing_title');
        	get_template_part('page-aggregator/part-links-listing');


		// EVENT LISTING
		
        elseif ( get_row_layout() == 'aggregator_event_listing' ) :
			$title = get_sub_field('aggregator_listing_title');
			$team = get_sub_field('aggregator_listing_team');
			$checkteam = $team[0]; 
			$colid = 2;
			$num = intval(get_sub_field('aggregator_listing_number')); 
			if ( !$num ) $num = -1; 
			$aoptions = get_sub_field('aggregator_listing_options');
			if ( in_array( "Calendar" , $aoptions )) $showcalendar = "on";
			if ( in_array( "Thumbnail" , $aoptions )) $showthumbnail = "on";
			if ( in_array( "Location" , $aoptions )) $showlocation = "on";
        	get_template_part('page-aggregator/part-event-listing');


		endif;


	endwhile;?>
	</div>
	<?php
endif;

// COLUMN 3
// check if the flexible content field has rows of data
if( have_rows('aggregator_column_3') ):
	?>
	<div id="home-col-3" class="col-lg-6 col-md-6 col-sm-12">

	<?php
	// loop through the rows of data
	
    while ( have_rows('aggregator_column_3') ) : the_row();

		$title = '';
		$link = '';
		$gallery = '';
		$team = '';
		$freeformat = '';
		$num = '';
		$tag = '';
		$type = '';
		$freshness = '';
		$n2n = '';
		$tax = '';

		// NEWS LISTING

        if ( get_row_layout() == 'aggregator_news_listing' ) :
			$title = get_sub_field('aggregator_listing_title');
			$team = get_sub_field('aggregator_listing_team');
			$checkteam = $team[0]; 
			$tax = get_sub_field('aggregator_listing_tax');
			$tag = get_sub_field('aggregator_listing_tag');
			$n2n = get_sub_field('aggregator_listing_need_to_know');
			$freshness = intval(get_sub_field('aggregator_listing_freshness'));
			$num = intval(get_sub_field('aggregator_listing_number')); 
			if ( !$num ) $num = -1; 
			$compact = get_sub_field('aggregator_listing_compact_list');
			if ( $compact ) $larray = array();
        	get_template_part('page-aggregator/part-news-listing');
		
		
		// TASK LISTING
		
        elseif ( get_row_layout() == 'aggregator_task_listing' ): 
        	$title = get_sub_field('aggregator_listing_title');
			$type = get_sub_field('aggregator_listing_type');
			$team = get_sub_field('aggregator_listing_team');
			$checkteam = $team[0]; 
			$tax =  get_sub_field('aggregator_listing_tax');
			$tag =  get_sub_field('aggregator_listing_tag');
			$compact = get_sub_field('aggregator_listing_compact_list');
			if ( $compact ) $larray = array();
        	get_template_part('page-aggregator/part-task-listing');


		// BLOG LISTING
		
        elseif ( get_row_layout() == 'aggregator_blog_listing' ) :
			$title = get_sub_field('aggregator_listing_title');
			$team = get_sub_field('aggregator_listing_team');
			$checkteam = $team[0]; 
			$freshness = intval(get_sub_field('aggregator_listing_freshness'));
			$num = intval(get_sub_field('aggregator_listing_number')); 
			if ( !$num ) $num = -1; 
			$compact = get_sub_field('aggregator_listing_compact_list');
			if ( $compact ) $larray = array();
        	get_template_part('page-aggregator/part-blog-listing');


		// FREE-FORMAT AREA

        elseif ( get_row_layout() == 'aggregator_free_format_area' ): 
        	$freeformat = get_sub_field('aggregator_free_format_area_content');
        	get_template_part('page-aggregator/part-free-format');


		// TEAM LISTING

        elseif ( get_row_layout() == 'aggregator_team_listing' ): 
			$alreadyshown = array();
			$directorystyle = get_option('options_staff_directory_style'); // 0 = squares, 1 = circles
			$showmobile = get_option('options_show_mobile_on_staff_cards'); // 1 = show
			$team = get_sub_field('aggregator_listing_team');
        	$title = get_sub_field('aggregator_listing_title');
			$teamid = $team[0]; 
			$teamleaderid = get_post_meta($teamid, 'team_lead', true);
        	get_template_part('page-aggregator/part-team-listing');


		// DOCUMENT LINKS

        elseif ( get_row_layout() == 'aggregator_document_listing' ): 
			$cat_id = get_sub_field('aggregator_listing_category'); 
			if ( !$cat_id ) $cat_id = "any";
			$doctyp = get_sub_field('aggregator_listing_doctype');
			if ( !$doctyp ) $doctyp = "any";
        	$title = get_sub_field('aggregator_listing_title');
        	get_template_part('page-aggregator/part-document-listing');


		// LINKS

        elseif ( get_row_layout() == 'aggregator_link_listing' ): 
			$link = get_sub_field('aggregator_listing_link');
        	$title = get_sub_field('aggregator_listing_title');
        	get_template_part('page-aggregator/part-links-listing');

		// EVENT LISTING
		
        elseif ( get_row_layout() == 'aggregator_event_listing' ) :
			$title = get_sub_field('aggregator_listing_title');
			$team = get_sub_field('aggregator_listing_team');
			$checkteam = $team[0]; 
			$colid = 3;
			$num = intval(get_sub_field('aggregator_listing_number')); 
			if ( !$num ) $num = -1; 
			$aoptions = get_sub_field('aggregator_listing_options');
			if ( in_array( "Calendar" , $aoptions )) $showcalendar = "on";
			if ( in_array( "Thumbnail" , $aoptions )) $showthumbnail = "on";
			if ( in_array( "Location" , $aoptions )) $showlocation = "on";
        	get_template_part('page-aggregator/part-event-listing');


		endif;


	endwhile;?>
	</div>
	<?php
endif;
?>
</div>
<?php remove_filter('pre_get_posts', 'filter_search'); ?>
<?php endwhile; ?>
<?php get_footer(); ?>
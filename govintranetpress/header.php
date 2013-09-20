<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */


if ( pods_url_variable(2) == 'users' ) {
	wp_redirect( '/author/' . pods_url_variable(3) . '/' );
} 

// prevent clickjacking, advised by Context security review
header('X-Frame-Options: SAMEORIGIN');

?><!DOCTYPE html>

<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<title><?php
		/*
		 * Print the <title> tag based on what is being viewed.
		 * We filter the output of wp_title() a bit -- see
		 * twentyten_filter_wp_title() in functions.php.
		 */
		wp_title( '', true, 'right' );
	
		?></title>

	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="profile" href="https://gmpg.org/xfn/11" />

	<!-- 1140px Grid styles for IE -->
	<!--[if lte IE 9]>
		<link rel="stylesheet" href="<?php bloginfo( 'template_url' ); ?>/css/ie.css" type="text/css" media="screen" />
	<![endif]-->
	<!--[if lte IE 8]>
		<link rel="stylesheet" href="<?php bloginfo( 'template_url' ); ?>/css/ie8.css" type="text/css" media="screen" />
	<![endif]-->
	
	<!-- The 1140px Grid - https://cssgrid.net/ -->
	<link rel="stylesheet" href="<?php bloginfo( 'template_url' ); ?>/css/1140.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="<?php bloginfo( 'template_url' ); ?>/css/general_enclosed_foundicons.css" type="text/css" media="all" />
	<link rel="stylesheet" href="<?php bloginfo( 'template_url' ); ?>/css/social_foundicons.css" type="text/css" media="all" />
	<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
	<link rel="stylesheet" type="text/css" media="print" href="<?php echo get_stylesheet_directory_uri(); ?>/print.css" />

	<!--css3-mediaqueries-js - https://code.google.com/p/css3-mediaqueries-js/ - Enables media queries in some unsupported browsers-->


 
	<!-- [if lte IE 8]>
		<script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/js/ie7/IE8.js"></script>
	<![endif]-->

	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
			
		<?php
		/* We add some JavaScript to pages with the comment form
		 * to support sites with threaded comments (when in use).
		 */
		if ( is_singular() && get_option( 'thread_comments' ) )
			wp_enqueue_script( 'comment-reply' );
	
		/* Always have wp_head() just before the closing </head>
		 * tag of your theme, or you will break many plugins, which
		 * generally use this hook to add elements to <head> such
		 * as styles, scripts, and meta tags.
		 */
		wp_head();
	?>

	<style type='text/css'>
	/* Custom CSS rules below: */
	<?php
	

		$gis = "general_intranet_custom_css_code";
		$giscss = get_option($gis);
		echo $giscss;
		
		// write custom css for background header colour
		$gis = "general_intranet_header_background";
		$gishex = get_option($gis);		
		$basecol=HTMLToRGB($gishex);
		$topborder = ChangeLuminosity($basecol, 33);
		echo "
		#topstrip  {
		background: ".$gishex.";
		border-top: 7px solid ".RGBToHTML($topborder).";
		padding-top: 10px;
		}
		";
		echo "
		.home.page .category-block h3 {
			border-bottom: 3px solid ".$gishex.";
		}
		.h3border {
		border-bottom: 3px solid ".$gishex.";
		}
		";


		//write custom css for logo
		$gis = "general_intranet_header_logo";
		$gisid = get_option($gis); 
		$gislogow = wp_get_attachment_image_src( $gisid[0] ); 
		$gislogo = $gislogow[0] ;
		$gisw = $gislogow[1] + 10;
		echo "
		#crownlink  {
		background: url('".$gislogo."') no-repeat;	 
		background-position:left 10px;
		padding: 16px 0 0 ".$gisw."px;
		height: 50px;
		}
		";
		


		
		$terms = get_terms('category');
		if ($terms) {
	  		foreach ((array)$terms as $taxonomy ) {
	  		    $themeid = $taxonomy->term_id;
	  		    $themeURL= $taxonomy->slug;
	  			$thistheme = new pod('category', $themeid);
	  			$background=$thistheme->get_field('cat_background_colour');
	  			$foreground=$thistheme->get_field('cat_foreground_colour');
	  			echo ".t" . $themeid . "{color: " . $foreground . "; background: " . $background . ";} \n";
	  			echo ".t" . $themeid . " a {color: " . $foreground . " !important;} \n";
	  			echo ".brd" . $themeid . "{border-top: 2px solid " . $background . ";} \n";
	  			echo ".hr" . $themeid . "{border-bottom: 1px solid " . $background . ";} \n";
	  			echo ".h1_" . $themeid . "{border-bottom: 3px solid " . $background . "; margin-bottom: 0.5em;} \n";
	  			echo ".b" . $themeid . "{border-left: 20px solid " . $background . ";} \n";

			}
		}  
	?>
	</style>
	<!--Google Analytics-->
<?php	
		//write script for google analytics (only do on homepage if homepage tracking is set)
		$gis = "general_intranet_track_homepage";
		$gistrackhome = get_option($gis); 
		$gis = "general_intranet_google_tracking_code";
		$gisgtc = get_option($gis);
		if ( is_front_page() ){
			if ($gistrackhome == 1){
				echo $gisgtc;
			}
		}
		else {
			echo $gisgtc;
		}
?>
	


</head>

<?php 
	$parentpageclass = (renderLeftNav("FALSE")) ? "parentpage" : "notparentpage"; 

	if ($govintranetpress_options['leftSubNav'] == "1" && is_page() ) { // check if left nav is on, on a page
		$leftnavflag = TRUE;
	}
	
?>

<body <?php body_class($parentpageclass); ?>>

		 <?php // include(get_stylesheet_directory() . "/sidebar-cookiebar.php"); ?>

			<div class="container">								

				<div id='topstrip'>

					<div class="row" id='masthead'>
						
						<div class="sevencol" id="crownlogo">

							<div id="crownlink"><a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>"  rel="home"><?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?></a>
							</div>

						</div>
						
						<div class="fivecol last">
							<div id='searchformdiv'>
								<?php get_search_form(true); ?>
							</div>
						</div>
						
					</div>
	
					<div class="row">
						<div class="twelvecol last">
	
							  <?php /*  Allow screen readers / text browsers to skip the navigation menu and get right to the good stuff */ ?>
								<a href="#maincontent" class='hiddentext' accesskey='s' title="<?php esc_attr_e( 'Skip to content', 'twentyten' ); ?>"><?php _e( 'Skip to content', 'twentyten' ); ?></a>
	

						</div>
					</div>
				
						<div class="row" id="topnav">
						<div class="eightcol">
							<div id="primarynav" role="navigation">
									<?php /* Our navigation menu.  If one isn't filled out, wp_nav_menu falls back to wp_page_menu.  The menu assiged to the primary position is the one used.  If none is assigned, the menu with the lowest ID is used.  */ ?>
									<?php 
							wp_nav_menu( array( 'container_class' => 'menu-header', 'theme_location' => 'primary' ) ); ?>
							</div>
						</div>
						<div class="fourcol last">
						<?php

							echo "<div id='utilitybar'>";?>
								<?php if ( is_active_sidebar( 'utility-widget-area' ) ) : ?>

									<?php dynamic_sidebar( 'utility-widget-area' ); ?>


									<?php endif; ?>
<?php
	echo "</div>";
						?>
						</div>
						</div>				
				
				
				</div>
				

				     
<div id="wrapper">			
<?php if (pods_url_variable(1) == 'forums' && !pods_url_variable(2)) :?>	
				<div class="row white">
				<div class="twelvecol last">
				<div class="row">
					<div class='breadcrumbs'>
							<?php if(function_exists('bcn_display') && !is_front_page()) {
								bcn_display();
							}?>
					</div>
				</div>
				</div></div>
<?php endif; ?>
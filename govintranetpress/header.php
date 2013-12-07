<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div class="content-wrapper">
 *
 * @package WordPress
 * @package Bootstrap
 */


// prevent clickjacking, advised by Context security review
header('X-Frame-Options: SAMEORIGIN');

?><!DOCTYPE html>

<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<title><?php
		/*
		 * Print the <title> tag based on what is being viewed. sdfffdf		 * We filter the output of wp_title() a bit -- see
		 * twentyten_filter_wp_title() in functions.php.
		 */
		wp_title( '', true, 'right' );
	
		?></title>

	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="profile" href="https://gmpg.org/xfn/11" />

	<!--[if lte IE 9]>
		<link rel="stylesheet" href="<?php bloginfo( 'template_url' ); ?>/css/ie.css" type="text/css" media="screen" />
	<![endif]-->
	<!--[if lte IE 8]>
		<link rel="stylesheet" href="<?php bloginfo( 'template_url' ); ?>/css/ie8.css" type="text/css" media="screen" />
	<![endif]-->

	<link rel="stylesheet" href="<?php bloginfo( 'template_url' ); ?>/css/social_foundicons.css" type="text/css" media="all" />
	<link href="<?php echo get_stylesheet_directory_uri(); ?>/css/bootstrap.min.css" rel="stylesheet">
	<link href="<?php echo get_stylesheet_directory_uri(); ?>/css/prettyPhoto.css" rel="stylesheet">

	<!--[if lt IE 9]>
	 <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/js/html5-shiv.min.js"></script>
	 <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/js/respond.min.js"></script>
	 <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<![endif]-->
	
	<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
	<link rel="stylesheet" type="text/css" media="print" href="<?php echo get_stylesheet_directory_uri(); ?>/print.css" />
 
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

		$gis = "general_intranet_enable_automatic_complementary_colour";
		$giscc = get_option($gis);
		
		// write custom css for background header colour
		$gis = "general_intranet_header_background";
		$gishex = get_option($gis);		
		$basecol=HTMLToRGB($gishex);
		$topborder = ChangeLuminosity($basecol, 33);
		echo "
		#topstrip  {
		background: ".$gishex.";
		}
		";

		echo "
		#footerwrapper  {";
		if ($giscc==1){
		echo "border-top: 7px solid ".RGBToHTML($topborder).";";
		} else {
		echo "border-top: 7px solid ".$gishex.";";
		}
		echo "}";

		echo "
		.page-template-page-about-php .category-block h2 {";
		if ($giscc==1){
		echo "border-top: 7px solid ".RGBToHTML($topborder).";";
		} else {
		echo "border-top: 7px solid ".$gishex.";";
		}

		echo "padding: 0.6em 0;
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
		
		echo "
		#content .widget-box {
		padding: .1em .4em .7em 0;
		font-size: .9em;
		background: #fff;";
		if ($giscc==1){
			echo "border-top: 7px solid ".RGBToHTML($topborder).";";
		} else {
			echo "border-top: 7px solid ".$gishex.";";
		}
		echo "margin-top: .7em;
		}
		";

		echo "
		.home.page .category-block h3 {";
		if ($giscc==1){
			echo "border-top: 7px solid ".RGBToHTML($topborder).";";
		} else {
			echo "border-top: 7px solid ".$gishex.";";
		}
		echo "border-bottom: none;
		padding-top: .7em;
		margin-top: .7em;
		}
		";
		echo "
		.page-template-page-news-php h1 {
		border-bottom: 7px solid ".RGBToHTML($topborder).";
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
		height: auto;
		min-height: 50px;
		margin-bottom: 0.6em;
		}
		";
		echo "
		#primarynav ul li  {
		border-bottom: 1px solid ".$gishex.";
		border-top: 1px solid ".$gishex.";
		border-right: 1px solid ".$gishex.";
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
	  			echo ".brd" . $themeid . "{border-left: 1.2em solid " . $background . ";} \n";
	  			echo ".hr" . $themeid . "{border-bottom: 1px solid " . $background . ";} \n";
	  			echo ".h1_" . $themeid . "{border-bottom: 7px solid " . $background . "; margin-bottom: 0.4em; padding-bottom: 0.3em;} \n";
	  			echo ".b" . $themeid . "{border-left: 20px solid " . $background . ";} \n";
	  			echo ".glyphicon.glyphicon-stop.gb" . $themeid . "{color: " . $background . ";} \n";

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
	<div id='topstrip'>
				
		<nav class="navbar navbar-inverse" role="navigation">
		  <!-- Brand and toggle get grouped for better mobile display -->
		  <div class="navbar-header">
		    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
		      <span class="sr-only">Toggle navigation</span>
		      <span class="icon-bar"></span>
		      <span class="icon-bar"></span>
		      <span class="icon-bar"></span>
		    </button>
		    <p><a class='navbar-brand visible-xs pull-left' href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>"  rel="home"><i class="glyphicon glyphicon-home"></i> Home</a></p>
		  </div>

		  <!-- Collect the nav links, forms, and other content for toggling -->
		  <div class="collapse navbar-collapse navbar-ex1-collapse">				
				
				<div class="row" id="masthead">	

					<div class="container">
						<a class="sr-only" href="#content">Skip to content</a>
													
						<!--logo and name-->
							<div class="col-lg-8 col-md-7 col-sm-6 hidden-xs" id="crownlogo">
								<div id="crownlink"><a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>"  rel="home"><?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?></a>
								</div>
							</div>
						
						<!--search box-->
							<div class="col-lg-4 col-md-5 col-sm-6 col-xs-12">
								<div id='searchformdiv' class=''>
										<?php get_search_form(true); ?>
								</div>
							</div>

<script>
jQuery("#s").focus();
</script>							
							<div class="sr-only" id="access">	
							  <?php /*  Allow screen readers / text browsers to skip the navigation menu and get right to the good stuff */ ?>
								<a href="#content" class='hiddentext' accesskey='s' title="<?php esc_attr_e( 'Skip to content', 'twentyten' ); ?>"><?php _e( 'Skip to content', 'twentyten' ); ?></a>
							</div>

						<!--utility menu-->
							<div class="col-lg-12">
								<div id="utilities" class="pull-right">
									<?php if ( is_active_sidebar( 'utility-widget-area' ) ) : ?>
										<div id='utilitybar'>
											<ul class="menu">
											<?php dynamic_sidebar( 'utility-widget-area' ); ?>
											</ul>
										</div>
									<?php endif; ?>
								</div>

								<div  id="mainnav" class="pull-left">		

									<div id="primarynav" role="navigation">
											<?php /* Our navigation menu.  If one isn't filled out, wp_nav_menu falls back to wp_page_menu.  The menu assiged to the primary position is the one used.  If none is assigned, the menu with the lowest ID is used.  */ ?>
											<?php 
									wp_nav_menu( array( 'container_class' => 'menu-header', 'theme_location' => 'primary' ) ); ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div><!-- /.navbar-collapse -->
			</nav>						
		</div>				
				     
		<div id="content" class="container">			
			<div class="content-wrapper">

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
		 * Print the <title> tag based on what is being viewed.
		 * We filter the output of wp_title() a bit -- see
		 * govintranet_filter_wp_title() in functions.php.
		 */
		wp_title( '', true, 'right' );
	
		?></title>
	
	<?php if ( is_home() || is_front_page() ): 
			if (intval(get_option('options_homepage_auto_refresh'))):
			?>
			<meta http-equiv="refresh" content="<?php echo intval(get_option('options_homepage_auto_refresh'))*60;?>">
			<?php 
			endif;
		endif; ?>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="profile" href="https://gmpg.org/xfn/11" />

	<!--[if lte IE 8]>
		<link rel="stylesheet" href="<?php bloginfo( 'template_url' ); ?>/css/ie8.css" type="text/css" media="screen" />
	<![endif]-->
	<!--[if IE 7]>
		<link rel="stylesheet" href="<?php bloginfo( 'template_url' ); ?>/css/ie7.css" type="text/css" media="screen" />
	<![endif]-->

	<link href="<?php echo get_stylesheet_directory_uri(); ?>/css/bootstrap.min.css" rel="stylesheet">
	<!--[if (IE)&(lt IE 9) ]>
	        <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" />
	<![endif]-->
	<!--[if (IE)&(gt IE 8) ]>
	        <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
	<![endif]-->

	<!--[if lt IE 9]>
	 <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/js/html5-shiv.min.js"></script>
	 <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/js/respond.min.js"></script>
	<![endif]-->


	<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
	<link rel="stylesheet" type="text/css" media="print" href="<?php echo get_stylesheet_directory_uri(); ?>/print.css" />
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo get_stylesheet_directory_uri(); ?>/css/custom.css">

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
	
		$giscss = get_option('options_custom_css_code');
		echo $giscss;
		
		// write custom css for background header colour

		$bg = get_option('options_page_background');
		echo "
		.custom-background  {
		background-color: ".$bg.";
		}
		";

		$bg = get_theme_mod('link_color', '#428bca');
		echo "
		a  {
		color: ".$bg.";
		}
		";

		$bg = get_theme_mod('link_visited_color', '#7303aa');
		echo "
		a:visited  {
		color: ".$bg.";
		}
		";
		
		$gisheight = get_option('options_widget_border_height');
		if (!$gisheight) $gisheight = 7;
		$gis = "options_header_background";
		$gishex = get_theme_mod('header_background', '#0b2d49'); 
		$headtext = get_theme_mod('header_textcolor', '#ffffff');
		$headimage = get_theme_mod('header_image', '');
		$basecol=HTMLToRGB($gishex);
		$topborder = ChangeLuminosity($basecol, 33);

		// set bar colour
		// if using automatic complementary colour then convert header color
		// otherwise use specified colour

		$giscc = get_option('options_enable_automatic_complementary_colour'); 
		if ($giscc):
			$giscc = RGBToHTML($topborder);
		elseif (get_option('options_complementary_colour')):
			$giscc = get_option('options_complementary_colour');
		else:
			 $giscc = $gishex; 
		endif;
		
		if ($headimage != 'remove-header' ):
			echo "
			#topstrip  {
			background: ".$gishex." url(".get_header_image().");
			color: #".get_header_textcolor().";
			}
			";
		else:
			echo "
			#topstrip  {
			background: ".$gishex.";
			color: #".get_header_textcolor().";
			}
			";
		endif;

		echo "
		@media only screen and (max-width: 767px)  {
			#masthead  {
			background: ".$gishex." !important;
			color: #".get_header_textcolor().";
			padding: 0 1em;
			}
			#primarynav ul li a {
			background: ".$gishex.";
			color: #".get_header_textcolor().";
			}	
			#primarynav ul li a:hover {
			color: ".$gishex." !important;
			background: #".get_header_textcolor().";
			}	
		}
		";

		echo "
		.btn-primary, .btn-primary a  {
		background: ".$giscc.";
		border: 1px solid ".$giscc.";
		color: #".get_header_textcolor().";
		}
		";
		echo "
		
		.btn-primary a:hover  {
		background: ".$gishex.";
		}
		";

		echo "
		#topstrip a {
		color: #".get_header_textcolor().";
		}
		";
		echo "
		#utilitybar ul#menu-utilities li a, #menu-utilities {
		color: #".get_header_textcolor().";
		}
		";

		echo "
		#footerwrapper  {";
		echo "border-top: ".$gisheight."px solid ".$giscc.";";
		echo "}";

		echo "
		.page-template-page-about-php .category-block h2 {";
		echo "border-top: ".$gisheight."px solid ".$giscc.";";

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
		echo "border-top: ".$gisheight."px solid ".$giscc.";";
		echo "margin-top: .7em;
		}
		";

		echo "
		.home.page .category-block h3 {";
		echo "border-top: ".$gisheight."px solid ".$giscc.";";
		echo "border-bottom: none;
		padding-top: 16px;
		margin-top: 16px;
		}
		";

		$directorystyle = get_option('options_staff_directory_style'); // 0 = squares, 1 = circles
		if ( $directorystyle ):
			echo "
			.bbp-user-page.single #bbp-user-avatar img.avatar   {
				border-radius: 50%;
			}
			";
		endif;
		
		echo "
		.bbp-user-page .panel-heading {";
		echo "border-top: ".$gisheight."px solid ".$giscc.";";
		echo "
		}
		";
		
		echo "
		.page-template-page-news-php h1 {
		border-bottom: ".$gisheight."px solid ".$giscc.";
		} 
		.tax-team h2 {
		border-bottom: ".$gisheight."px solid ".$giscc.";
		} 
		";

		//write custom css for logo
		$gisid = get_option('options_header_logo'); 
		$gislogow = wp_get_attachment_image_src( $gisid ); 
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
		#primarynav ul li:last-child,  #primarynav ul li.last-link  {
		border-right: 1px solid ".$gishex.";
		}

		#primarynav ul li:first-child,  #primarynav ul li.first-link  {
		border-left: 1px solid ".$gishex.";
		}

		";		


		echo "a.wptag {color: #".$headtext."; background: ".$gishex.";} \n";
		echo "a.:visited.wptag {color: #".$headtext."; background: ".$gishex.";} \n";



		if ($headimage != 'remove-header' && $headimage) echo '#utilitybar ul#menu-utilities li a, #menu-utilities, #crownlink { text-shadow: 1px 1px #333; }'; 
		
		$terms = get_terms('category',array('hide_empty'=>false));
		if ($terms) {
	  		foreach ((array)$terms as $taxonomy ) {
	  		    $themeid = $taxonomy->term_id;
	  		    $themeURL= $taxonomy->slug;
	  			$background=get_option('category_'.$themeid.'_cat_background_colour');
	  			$foreground=get_option('category_'.$themeid.'_cat_foreground_colour');
	  			echo "button.btn.t" . $themeid . ", a.btn.t" . $themeid . " {color: " . $foreground . "; background: " . $background . "; border: 1px solid ".$background.";} \n";
	  			echo ".category-block .t" . $themeid . ", .category-block .t" . $themeid . " a  {color: " . $foreground . "; background: " . $background . "; border: 1px solid ".$background."; width: 100%; padding: 0.5em; } \n";
	  			echo "button:hover.btn.t" . $themeid . ", a:hover.btn.t" . $themeid . "{color: white; background: #333; border: 1px solid ".$background.";} \n";
	  			echo "a.t" . $themeid . "{color: " . $foreground . "; background: " . $background . ";} \n";
	  			echo "a.t" . $themeid . " a {color: " . $foreground . " !important;} \n";
	  			echo ".brd" . $themeid . "{border-left: 1.2em solid " . $background . ";} \n";
	  			echo ".hr" . $themeid . "{border-bottom: 1px solid " . $background . ";} \n";
	  			echo ".h1_" . $themeid . "{border-bottom: ".$gisheight."px solid " . $background . "; margin-bottom: 0.4em; padding-bottom: 0.3em;} \n";
	  			echo ".b" . $themeid . "{border-left: 20px solid " . $background . ";} \n";
	  			echo ".dashicons.dashicons-category.gb" . $themeid . "{color: " . $background . ";} \n";
	  			echo "a:visited.wptag.t". $themeid . "{color: " . $foreground . ";} \n";
			}
		}  
	?>
	</style>
	<!--Google Analytics-->
	<?php	
		//write script for google analytics (only do on homepage if homepage tracking is set)
		$gistrackhome = get_option('options_track_homepage');
		$gisgtc = get_option('options_google_tracking_code');
		if ( is_front_page() || is_search() ){
			if ($gistrackhome == 1 || is_search() ){
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
$parentpageclass.=" custom-background"
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
						<div class="row">							
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
						</div>

							<div class="sr-only" id="access">	
							  <?php /*  Allow screen readers / text browsers to skip the navigation menu and get right to the good stuff */ ?>
								<a href="#content" class='hiddentext' accesskey='s' title="<?php esc_attr_e( 'Skip to content', 'govintranet' ); ?>"><?php _e( 'Skip to content', 'govintranet' ); ?></a>
							</div>

						<!--utility menu-->
							<div class="row">
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
									wp_nav_menu( array( 
										'container_class' => 'menu-header', 
										'theme_location' => 'primary' , 
										'depth' 		=> 2,
										'walker' => new wp_bootstrap_navwalker(),
										) ); ?>
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
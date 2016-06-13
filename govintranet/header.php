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
	
	<!--[if (IE)&(lt IE 9) ]>
	        <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" />
	<![endif]-->
	<!--[if (IE)&(gt IE 8) ]>
	        <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
	<![endif]-->
	<?php 
		if ( (is_home() || is_front_page()) && !is_search() ): 
			if (intval(get_option('options_homepage_auto_refresh')) > 0):
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

	<!--[if lt IE 9]>
	 <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/js/html5-shiv.js"></script>
	 <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/js/respond.min.js"></script>
	<![endif]-->

	<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
	<link rel="stylesheet" type="text/css" media="print" href="<?php echo get_stylesheet_directory_uri(); ?>/print.css" />

	<!-- [if lte IE 8]>
		<script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/js/ie7/IE8.js"></script>
	<![endif]-->

	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

	<!--Google Analytics-->
	<?php	
	//write script for google analytics (only do on homepage if homepage tracking is set)
	$gistrackhome = get_option('options_track_homepage');
	$gisgtc = get_option('options_google_tracking_code');
	if ( is_front_page() || is_search() ){
		if ($gistrackhome == 1 || is_search() ){
			echo $gisgtc;
		}
	} else {
		echo $gisgtc;
	}

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
</head>

<?php 
$tzone = get_option('timezone_string');
date_default_timezone_set($tzone);
$parentpageclass = (renderLeftNav("FALSE")) ? "parentpage" : "notparentpage"; 
$parentpageclass.=" custom-background";
?>

<body <?php body_class($parentpageclass); ?>>
	<div class="sr-only" id="access">	
	  <?php /*  Allow screen readers / text browsers to skip the navigation menu and get right to the good stuff */ ?>
		<a href="#content" class='hiddentext' accesskey='s' title="<?php esc_attr_e( 'Skip to content', 'govintranet' ); ?>"><?php _e( 'Skip to content', 'govintranet' ); ?></a>
		<a href="#primarynav" class='hiddentext' accesskey='2' title="<?php esc_attr_e( 'Main menu', 'govintranet' ); ?>"><?php _e( 'Skip to main menu', 'govintranet' ); ?></a>
		<a href="#utilitybar" class='hiddentext' accesskey='3' title="<?php esc_attr_e( 'Utility menu', 'govintranet' ); ?>"><?php _e( 'Skip to utility menu', 'govintranet' ); ?></a>
	</div>

	<div id='topstrip'>
				
		<nav class="navbar navbar-inverse" role="navigation">
		  <!-- Brand and toggle get grouped for better mobile display -->
		  <div class="navbar-header">
		    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
		      <span class="sr-only"><?php _e('Toggle navigation' , 'govintranet'); ?></span>
		      <span class="icon-bar"></span>
		      <span class="icon-bar"></span>
		      <span class="icon-bar"></span>
		    </button>
		    <p><a class='navbar-brand visible-xs pull-left' href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>"  rel="home"><i class="glyphicon glyphicon-home"></i> <?php _e('Home' , 'govintranet'); ?></a></p>
		  </div>

		  <!-- Collect the nav links, forms, and other content for toggling -->
		  <div class="collapse navbar-collapse navbar-ex1-collapse">				
				
				<div class="row" id="masthead">	

					<div class="container">
						<a class="sr-only" href="#content"><?php _e('Skip to content' , 'govintranet');?></a>
						<div class="row">							
						<!--logo and name-->
							<div class="col-lg-8 col-md-7 col-sm-6 hidden-xs" id="crownlogo">
								<div id="crownlink">
									<a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>"  rel="home" accesskey="1"><?php if ( !get_option('options_hide_sitename') ) echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?></a>
								</div>
							</div>
						
						<?php  $jumbo_searchbox = get_option("options_search_jumbo_searchbox", false);	?>

						<?php if ( $jumbo_searchbox != 1 || ( !is_home() && !is_front_page() ) ) : ?>

							<!--search box-->
							<div id="headsearch" class="col-lg-4 col-md-5 col-sm-6 col-xs-12">
								<div id='searchformdiv'>
									<?php get_search_form(true); ?>
								</div>
							</div>
							
						<?php endif; ?>

						</div>

						<!--utility menu-->
							<div class="row">
								<div id="utilities" class="pull-right">
									<?php
										
										if ( has_nav_menu( 'secondary' ) ) :?>
											<div id='utilitybar'>
												<?php										
												wp_nav_menu( array( 
												'container_class' => 'utilities', 
												'theme_location' => 'secondary' , 
												'depth' 		=> 2,
												'walker' => new wp_bootstrap_navwalker(),
												) ); ?>
											</div>
										<?php
										else:
										?>
										<?php if ( is_active_sidebar( 'utility-widget-area' ) ) : ?>
												<div id='utilitybar'>	
													<ul class="menu">
													<?php dynamic_sidebar( 'utility-widget-area' ); ?>
													</ul>
												</div>
										<?php endif; ?>
										<?php
										endif;
										?>

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

		<?php if ( $jumbo_searchbox == 1 && ( is_home() || is_front_page() ) ) : ?>

		<!--search box-->
			<div class="altsearch-container">
				<div id='searchformdiv' class='altsearch'>
						<?php get_search_form(true); ?>
				</div>
			</div>
			
		<?php endif; ?>

		<div class="content-wrapper">
			

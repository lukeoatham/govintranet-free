(function (jQuery) {

	wp.customize( 'header_background', function ( value ) {
		value.bind( function (to) {
			jQuery('#topstrip').css('background', to );
			jQuery('a.wptag').css('background', to );
		});
	});
	wp.customize( 'display_header_text', function ( value ) {
		value.bind( function (to) {
			if ( !to ) jQuery('.site-title').addClass('hidden');
			if ( to ) jQuery('.site-title').removeClass('hidden');
			jQuery('.site-title').removeClass('hidden');
		});
	});
	wp.customize( 'options_btn_text_colour', function ( value ) {
		value.bind( function (to) {
			jQuery('.btn').css('color', to );
		});
	});
	wp.customize( 'options_complementary_colour', function ( value ) {
		value.bind( function (to) {
			jQuery('.category-block h2').css('border-top-color', to );
			jQuery('.category-block h3').css('border-top-color', to );
			jQuery('.btn-primary').css('background', to );
			jQuery('.btn-primary a').css('background', to );
			jQuery('#footerwrapper').css('border-top-color', to );
			jQuery('#content .widget-box').css('border-top-color', to );
			jQuery('#newsboardTabs.nav>li>a').css('background', to );
		});
	});
	wp.customize( 'options_widget_border_height', function ( value ) {
		value.bind( function (to) {
			jQuery('.category-block h2').css('border-width', to );
			jQuery('.category-block h3').css('border-width', to );
			jQuery('#footerwrapper').css('border-width', to );		
			jQuery('.need-to-know-container.news-updates-widget').css('border-width', to );		
			jQuery('#need-to-know .category-block').css('border-width', to );
			jQuery('#content .widget-box').css('border-width', to );
		});
	});
	wp.customize( 'link_color', function ( value ) {
		value.bind( function (to) {
			jQuery('#content a').css('color', to );
		});
	});
	wp.customize( 'link_visited_color', function ( value ) {
		value.bind( function (to) {
			jQuery('#content a:visited').css('color', to );
		});
	});
	
}) ( jQuery );

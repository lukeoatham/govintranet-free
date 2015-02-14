(function (jQuery) {
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
	wp.customize( 'header_background', function ( value ) {
		value.bind( function (to) {
			jQuery('#topstrip').css('background', to );
		});
	});
}) ( jQuery );
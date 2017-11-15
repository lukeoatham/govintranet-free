jQuery(document).ready(function () {
    if(location.hash != null && location.hash != ""){
        jQuery('.collapse').removeClass('in');
        jQuery(location.hash + '.collapse').collapse('show');
		var target = location.hash;
	    var $target = jQuery(target);
	    jQuery('html, body').stop().animate({
	        'scrollTop': $target.offset().top
	    }, 900, 'swing', function () {
	        window.location.hash = target;
	    });    
	}
    jQuery('#manualaccordion .panel h4 a').click(function (e) {
		var scrollmem = jQuery(e).scrollTop();
		window.location.hash = this.hash;
		jQuery('html,body').scrollTop(scrollmem);
	});
});



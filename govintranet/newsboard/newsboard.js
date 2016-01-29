jQuery(document).ready(function() {
	    // on load of the page: switch to the currently selected tab
    var hash = window.location.hash;
    jQuery('#newsboardTabs.nav.nav-tabs > li > a[href="' + hash + '"]').tab('show');
    
    // Append URL with tab's ID #
	jQuery('#newsboardTabs.nav.nav-tabs > li > a').click(function (e) {
		var scrollmem = jQuery('body').scrollTop();
		window.location.hash = this.hash;
		jQuery('html,body').scrollTop(scrollmem);
	});
});	
jQuery(document).ready(function(){
	jQuery('#menu-utilities li').last().addClass('last-link');
	jQuery('#menu-footer-left li').last().addClass('last-link');
	jQuery('#primarynav li').last().addClass('last-link');
	jQuery('.category-block ul').addClass('first-link');
    markDocumentLinks();
	gaTrackDownloadableFiles();
	jQuery('#searchform').submit(function(e) {
	    if (jQuery.trim(jQuery("#s").val()) === "") {
	        e.preventDefault();
	        jQuery('#s').focus();
	    }
	});	
});


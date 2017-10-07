jQuery(document).ready(function(){
	jQuery('#category-search').submit(function(e) {
	    if (jQuery.trim(jQuery("#sbc-s").val()) === "") {
	        e.preventDefault();
	        jQuery('#sbc-s').focus();
	    }
	});	
	jQuery('#task-search').submit(function(e) {
	    if (jQuery.trim(jQuery("#sbc-s").val()) === "") {
	        e.preventDefault();
	        jQuery('#sbc-s').focus();
	    }
	});	
	jQuery('#task-alt-search').submit(function(e) {
	    if (jQuery.trim(jQuery("#sbc-s").val()) === "") {
	        e.preventDefault();
	        jQuery('#sbc-s').focus();
	    }
	});	
	jQuery('#searchform2').submit(function(e) {
	    if (jQuery.trim(jQuery("#s2").val()) === "") {
	        e.preventDefault();
	        jQuery('#s2').focus();
	    }
	});	
	jQuery('#sbc-search').submit(function(e) {
	    if (jQuery.trim(jQuery("#sbc-s").val()) === "") {
	        e.preventDefault();
	        jQuery('#sbc-s').focus();
	    }
	});	
	jQuery('#serps_search').submit(function(e) {
	    if (jQuery.trim(jQuery("#nameSearch").val()) === "") {
	        e.preventDefault();
	        jQuery('#nameSearch').focus();
	    }
	});	
});	


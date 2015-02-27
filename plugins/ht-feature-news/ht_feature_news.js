jQuery(document).ready(function(){	
	if (jQuery("body").css("left") == "5px" ){
		alert('5');
		jQuery("#ht-feature-news").parent().addClass('collapse');
		jQuery("#ht-feature-news").addClass('collapse');
		jQuery("#ht-feature-news").slideUp();
	}
	return true;
});
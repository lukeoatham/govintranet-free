jQuery(document).ready(function($) {

	var disclaimerBarDiv = '<div id="disclaimerBarDiv" >BETA VERSION</div>';
	
	jQuery('body').prepend(disclaimerBarDiv);

	jQuery('#disclaimerBarDiv').fadeTo(600,1.0);
        
});


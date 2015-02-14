function pauseProfileNudge(name) { // set a short cookie if user hides nudge
	if (location.protocol === 'https:') {
		var contype = 1;
	}else{
		var contype = 0;
	}
	setCookie(name,'closed','10080','/',0,contype); 
	jQuery(document).ready(function() { 
	                jQuery('#ht_profile_nudge').delay(0).slideUp();
	});
	return true;
}

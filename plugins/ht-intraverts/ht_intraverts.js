function pauseIntravert(name,expires,ititle,isource) { // set a short cookie if user clicks the link
	if (location.protocol === 'https:') {
		var contype = 1;
	}else{
		var contype = 0;
	}
	expires = expires * 60 * 24;
	setCookie(name,'closed',expires,'/',0,contype); 
	jQuery("#intraverts").parent().slideUp();
	if  (typeof _gaq != 'undefined') 	_gaq.push(['_trackEvent', 'Intraverts', ititle, isource]);
    if (jQuery('a[href="#nowhere"]')) { 
		return false;
    }
	return true;
}
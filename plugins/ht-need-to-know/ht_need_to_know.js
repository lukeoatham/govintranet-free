function pauseNeedToKnow(name) { // set a short cookie if user hides nudge
	if (location.protocol === 'https:') {
		var contype = 1;
	}else{
		var contype = 0;
	}
	setCookie(name,'closed','10080','/',0,contype); 
	return true;
}

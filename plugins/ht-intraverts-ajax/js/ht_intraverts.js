function pauseIntravert(name,expires,ititle,isource) { // set a short cookie if user clicks the link
	if (location.protocol === 'https:') {
		var contype = 1;
	}else{
		var contype = 0;
	}
	expires = expires * 60 * 24;
	setCookie(name,'closed',expires,'/',0,contype); 
	var ipanel = '#intraverts_' + ht_intraverts.widget_id;
	jQuery(ipanel).slideUp();
	if  (typeof _gaq != 'undefined') 	_gaq.push(['_trackEvent', 'Intraverts', ititle, isource]);
    if (jQuery('a[href="#nowhere"]')) { 
		return false;
    }
	return true;
}

jQuery(document).ready(function($) {
    	var ipanel = '#intraverts_' + ht_intraverts.widget_id;
		jQuery(ipanel).slideUp();
    	
    
        var data = {
            action: 'ht_intraverts_ajax_show',
			before_widget: ht_intraverts.before_widget,
			title: ht_intraverts.title,
			after_widget: ht_intraverts.after_widget,
			before_title: ht_intraverts.before_title,
			after_title: ht_intraverts.after_title,
			intravertToShow: ht_intraverts.intravertToShow,
			widget_id: ht_intraverts.widget_id,
			post_id: ht_intraverts.post_id,

        }
        
        $.post( ht_intraverts.ajaxurl, data, function(data){
            var status  = $(data).find('response_data').text();
            var message = $(data).find('supplemental message').text();

            if( status == 'success' ) {
                jQuery(ipanel).html(message);
				jQuery(ipanel).slideDown(400);
            }
        });

        return false;
        
});
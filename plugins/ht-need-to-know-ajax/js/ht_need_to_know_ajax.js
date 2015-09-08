
function pauseNeedToKnowAJAX(name) { // set a short cookie if user hides nudge
	if (location.protocol === 'https:') {
		var contype = 1;
	}else{
		var contype = 0;
	}
	setCookie(name,'closed','10080','/',0,contype); 
	return true;
}

jQuery(document).ready(function($) {
    
		jQuery('.ht_need_to_know_ajax').slideUp();
    	
    
        var data = {
            action: 'ht_need_to_know_ajax_show',
            items: ht_need_to_know.items,
			before_widget: ht_need_to_know.before_widget,
			title: ht_need_to_know.title,
			after_widget: ht_need_to_know.after_widget,
			before_title: ht_need_to_know.before_title,
			after_title: ht_need_to_know.after_title,
			hide: ht_need_to_know.hide,

        }
        
        $.post( ht_need_to_know.ajaxurl, data, function(data){
            var status  = $(data).find('response_data').text();
            var message = $(data).find('supplemental message').text();
            if( status == 'success' ) {
                jQuery('.ht_need_to_know_ajax').html(message);
				jQuery('.ht_need_to_know_ajax').slideDown(768);
            }
        });

        return false;
        
});

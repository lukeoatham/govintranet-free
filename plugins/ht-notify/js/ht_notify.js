jQuery.fn.fadeThenSlideToggle = function(speed, easing, callback) {
  if (this.is(":hidden")) {
    return this.slideDown(speed, easing).fadeTo(speed, 1, easing, callback);
  } else {
    return this.fadeTo(speed, 0, easing).slideUp(speed, easing, callback);
  }
};

function addtonotifications(){
	var ipanel = '#ht_notify_' + ht_notify.widget_id;
	jQuery(ipanel).html('<div class="ht_addtonotifications btn btn-sm btn-default"><img id="doccatspinner" src="' +  ht_notify.spinner + '" /></div>');
	var user_id = ht_notify.user_id;
	var data = {
		action: 'ht_notify_ajax_action_add',
		user_id: user_id,
		nonce: ht_notify.nonce,
		widget_id: ht_notify.widget_id,
		post_id: ht_notify.post_id,
	};
	jQuery.post(ht_notify.ajaxurl, data, function(data){
		var status = jQuery(data).find('response_data').text();
		var message = jQuery(data).find('supplemental message').text();

        if( status == 'success' ) {
			jQuery(ipanel).html(message);
			jQuery(ipanel).fadeThenSlideToggle(3000,'easeInOutCubic');
		} else {
			jQuery(ipanel).html(message);			
		}
	});
	return false;
}

function delnotifications(){
	var ipanel = '#ht_notify_' + ht_notify.widget_id;
	jQuery(ipanel).html('<div class="ht_addtonotifications btn btn-sm btn-default"><img id="doccatspinner" src="' +  ht_notify.spinner + '" /></div>');
	var user_id = ht_notify.user_id;
	var data = {
		action: 'ht_notify_ajax_action_del',
		user_id: user_id,
		nonce: ht_notify.nonce,
		widget_id: ht_notify.widget_id,
		post_id: ht_notify.post_id,
	};
	jQuery.post(ht_notify.ajaxurl, data, function(data){
		var status = jQuery(data).find('response_data').text();
		var message = jQuery(data).find('supplemental message').text();

        if( status == 'success' ) {
			jQuery(ipanel).html(message);
			jQuery(ipanel).fadeThenSlideToggle(3000,'easeInOutCubic');
		} else {
			jQuery(ipanel).html(message);			
		}
	});
	return false;
}

jQuery(document).ready(function($) {	
	var user_id = ht_notify.user_id; 
	var ipanel = '#ht_notify_' + ht_notify.widget_id;
    var data = {
        action: 'ht_notify_ajax_show',
		before_widget: ht_notify.before_widget,
		after_widget: ht_notify.after_widget,
		post_id: ht_notify.post_id,
		user_id: user_id,
		widget_id: ht_notify.widget_id,
    }
    $.post( ht_notify.ajaxurl, data, function(data){
        var status  = $(data).find('response_data').text();
        var message = $(data).find('supplemental message').text();
        if( status == 'success' ) {
            jQuery(ipanel).html(message);
        }
    });
    
    return false;
});


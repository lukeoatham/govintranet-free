jQuery.fn.fadeThenSlideToggle = function(speed, easing, callback) {
  if (this.is(":hidden")) {
    return this.slideDown(speed, easing).fadeTo(speed, 1, easing, callback);
  } else {
    return this.fadeTo(speed, 0, easing).slideUp(speed, easing, callback);
  }
};

function addtofav(){
	var ipanel = '#ht_favourites_add_' + ht_favourites_add.widget_id;
	jQuery(ipanel).html('<div class="ht_addtofav btn btn-sm btn-default"><img id="doccatspinner" src="' +  ht_favourites_add.spinner + '" /></div>');
	var user_id = ht_favourites_add.user_id;
	var data = {
		action: 'ht_favourites_ajax_action_add',
		user_id: user_id,
		nonce: ht_favourites_add.nonce,
		widget_id: ht_favourites_add.widget_id,
		post_id: ht_favourites_add.post_id,
	};
	jQuery.post(ht_favourites_add.ajaxurl, data, function(data){
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

function delfav(){
	var ipanel = '#ht_favourites_add_' + ht_favourites_add.widget_id;
	jQuery(ipanel).html('<div class="ht_addtofav btn btn-sm btn-default"><img id="doccatspinner" src="' +  ht_favourites_add.spinner + '" /></div>');
	var user_id = ht_favourites_add.user_id;
	var data = {
		action: 'ht_favourites_ajax_action_del',
		user_id: user_id,
		nonce: ht_favourites_add.nonce,
		widget_id: ht_favourites_add.widget_id,
		post_id: ht_favourites_add.post_id,
	};
	jQuery.post(ht_favourites_add.ajaxurl, data, function(data){
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
	var user_id = ht_favourites_add.user_id; 

	var ipanel = '#ht_favourites_add_' + ht_favourites_add.widget_id;
    var data = {
        action: 'ht_favourites_btn_ajax_show',
		before_widget: ht_favourites_add.before_widget,
		after_widget: ht_favourites_add.after_widget,
		post_id: ht_favourites_add.post_id,
		user_id: ht_favourites_add.user_id,
		spinner: ht_favourites_add.spinner,
		nonce: ht_favourites_add.nonce,
		widget_id: ht_favourites_add.widget_id,
    }
    $.post( ht_favourites_add.ajaxurl, data, function(data){
        var status  = $(data).find('response_data').text();
        var message = $(data).find('supplemental message').text();
        if( status == 'success' ) {
            jQuery(ipanel).html(message);
        }
    });    
    return false;
});


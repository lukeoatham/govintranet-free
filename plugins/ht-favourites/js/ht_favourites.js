function addtofav(){
	var ipanel = '#ht_favourites_' + ht_favourites.widget_id;
	jQuery(ipanel).html('<div class="ht_addtofav btn btn-default"><img id="doccatspinner" src="' +  ht_favourites.spinner + '" /></div>');
	var user_id = ht_favourites.user_id;
	var data = {
		action: 'ht_favourites_ajax_action_add',
		user_id: user_id,
		nonce: ht_favourites.nonce,
		widget_id: ht_favourites.widget_id,
		post_id: ht_favourites.post_id,
	};
	jQuery.post(ht_favourites.ajaxurl, data, function(data){
		var status = jQuery(data).find('response_data').text();
		var message = jQuery(data).find('supplemental message').text();

        if( status == 'success' ) {
			jQuery(ipanel).addClass("hidden");
			jQuery(ipanel).html(message);
			jQuery(ipanel).removeClass("hidden");
			jQuery(ipanel).slideDown();
			jQuery(ipanel).delay(1500);
			jQuery(ipanel).fadeTo('slow',0);
		} else {
			jQuery(ipanel).html(message);			
		}
	});
	return false;
}

function delfav(){
	var ipanel = '#ht_favourites_' + ht_favourites.widget_id;
	jQuery(ipanel).html('<div class="ht_addtofav btn btn-default"><img id="doccatspinner" src="' +  ht_favourites.spinner + '" /></div>');
	var user_id = ht_favourites.user_id;
	var data = {
		action: 'ht_favourites_ajax_action_del',
		user_id: user_id,
		nonce: ht_favourites.nonce,
		widget_id: ht_favourites.widget_id,
		post_id: ht_favourites.post_id,
	};
	jQuery.post(ht_favourites.ajaxurl, data, function(data){
		var status = jQuery(data).find('response_data').text();
		var message = jQuery(data).find('supplemental message').text();

        if( status == 'success' ) {
			jQuery(ipanel).addClass("hidden");
			jQuery(ipanel).html(message);
			jQuery(ipanel).removeClass("hidden");
			jQuery(ipanel).slideDown();
			jQuery(ipanel).delay(1500);
			jQuery(ipanel).fadeTo('slow',0);
		} else {
			jQuery(ipanel).html(message);			
		}
	});
	return false;
}

jQuery(document).ready(function($) {	
	var user_id = ht_favourites.user_id; 
	var ipanel = '#ht_favourites_display_' + ht_favourites.widget_id;
    var data = {
        action: 'ht_favourites_ajax_show',
		before_widget: ht_favourites.before_widget,
		after_widget: ht_favourites.after_widget,
		before_title: ht_favourites.before_title,
		after_title: ht_favourites.after_title,
		title: ht_favourites.title,
		user_id: user_id,
		widget_id: ht_favourites.widget_id,
    }
    $.post( ht_favourites.ajaxurl, data, function(data){
        var status  = $(data).find('response_data').text();
        var message = $(data).find('supplemental message').text();
        if( status == 'success' ) {
            jQuery(ipanel).html(message);
        }
    });
    
    return false;
});


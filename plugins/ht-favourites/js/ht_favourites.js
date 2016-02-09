
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


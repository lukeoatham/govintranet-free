jQuery(document).ready(function($) {
    
        var data = {
            action: 'ht_news_updates_restricted_ajax_show',
            items: ht_news_updates_restricted.items,
			before_widget: ht_news_updates_restricted.before_widget,
			title: ht_news_updates_restricted.title,
			after_widget: ht_news_updates_restricted.after_widget,
			before_title: ht_news_updates_restricted.before_title,
			after_title: ht_news_updates_restricted.after_title,
			news_update_types: ht_news_updates_restricted.news_update_types,
			background_colour: ht_news_updates_restricted.background_colour,
			text_colour: ht_news_updates_restricted.text_colour,
			border_colour: ht_news_updates_restricted.border_colour,
			border_height: ht_news_updates_restricted.border_height,
			path: ht_news_updates_restricted.path,
			moretitle: ht_news_updates_restricted.moretitle,
			widget_id: ht_news_updates_restricted.widget_id

        }

		jQuery('.ht_news_updates_restricted_'+ht_news_updates_restricted.widget_id).slideUp();
        
        $.post( ht_news_updates_restricted.ajaxurl, data, function(data){
            var status  = $(data).find('response_data').text();
            var message = $(data).find('supplemental message').text();
            if( status == 'success' ) {
                jQuery('.ht_news_updates_restricted_'+ht_news_updates_restricted.widget_id).html(message);
				jQuery('.ht_news_updates_restricted_'+ht_news_updates_restricted.widget_id).slideDown(200);
            }
        });

        return false;
        
});

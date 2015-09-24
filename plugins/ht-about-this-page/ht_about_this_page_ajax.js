
jQuery(document).ready(function($) {
    
		jQuery('.ht_about_this_page_ajax').slideUp();
    
        var data = {
            action: 'ht_about_this_page_ajax_show',
			before_widget: ht_about_this_page_ajax.before_widget,
			title: ht_about_this_page_ajax.title,
			after_widget: ht_about_this_page_ajax.after_widget,
			before_title: ht_about_this_page_ajax.before_title,
			after_title: ht_about_this_page_ajax.after_title,
			show_modified_date: ht_about_this_page_ajax.show_modified_date,
			show_published_date: ht_about_this_page_ajax.show_published_date,
			show_author: ht_about_this_page_ajax.show_author,
			single: ht_about_this_page_ajax.single,
			single_forum: ht_about_this_page_ajax.single_forum,
			page: ht_about_this_page_ajax.page,
			sdate:ht_about_this_page_ajax.sdate, 
			pdate:ht_about_this_page_ajax.pdate, 
			userid:ht_about_this_page_ajax.userid, 
			showabout:ht_about_this_page_ajax.showabout, 

			
        }
        
        $.post( ht_about_this_page_ajax.ajaxurl, data, function(data){
            var status  = $(data).find('response_data').text();
            var message = $(data).find('supplemental message').text();
            if( status == 'success' ) {
                jQuery('.ht_about_this_page_ajax').html(message);
				jQuery('.ht_about_this_page_ajax').slideDown();
            }
        });

        return false;
        
});

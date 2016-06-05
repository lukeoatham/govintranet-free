jQuery(document).ready(function($) {
    
        var data = {
            action: 'ht_account_links',
            items: encodeURI( ht_account_links.items ),
        }
        
        $.post( ht_account_links.ajaxurl, data, function(data){
            var status  = $(data).find('response_data').text();
            var message = $(data).find('supplemental message').text();
            if( status == 'success' ) {
	            jQuery('#utilitybar ul.menu li.last-link').removeClass('last-link');
                jQuery('#utilitybar ul.menu').prepend( decodeURI( message ) ).html();
            }
        });

        return false;
        
});

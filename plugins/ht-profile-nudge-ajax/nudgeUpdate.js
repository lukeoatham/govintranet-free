
	function update_profile_action(type){
		alert("xxx");
		var ipanel = '#ht_profilenudge_' + nudgeUpdate.widget_id;
		var userid = nudgeUpdate.user_id;
		var type = 'add-phone';

//		var nonce = pauseNudgeajax.nonce;
		var href= jQuery('input#phone').value();
		var data = {
			action: 'ht_profile_nudge_ajax_action',
			userid: userid,
			type: type,
//			nonce: nonce
		}

		jQuery.post(nudgeUpdate.ajaxurl, data, function(data){
			var status = jQuery(data).find('response_data').text();
			var message = jQuery(data).find('supplemental message').text();
			alert("clicked");

				
			jQuery(ipanel).html(nessage);
		});
		return false;
	}

function pauseProfileNudgeAJAX(name) { // set a short cookie if user hides nudge
   	var ipanel = '#ht_profilenudge_' + pauseNudgeajax.widget_id;

	if (location.protocol === 'https:') {
		var contype = 1;
	}else{
		var contype = 0;
	}
	setCookie(name,'closed','10080','/',0,contype); 
	jQuery(ipanel).delay(0).slideUp();
	return false;
}

jQuery(document).ready(function($) {	
	
	var userid = pauseNudgeajax.user_id;
	var ipanel = '#ht_profilenudge_' + pauseNudgeajax.widget_id;
	jQuery(ipanel).slideUp();
    var data = {
        action: 'ht_profile_nudge_ajax_show',
		before_widget: pauseNudgeajax.before_widget,
		after_widget: pauseNudgeajax.after_widget,
		before_title: pauseNudgeajax.before_title,
		after_title: pauseNudgeajax.after_title,
		user_id: pauseNudgeajax.user_id,
		widget_id: pauseNudgeajax.widget_id,
		telephone: pauseNudgeajax.telephone,
		mobile: pauseNudgeajax.mobile,
		job_title: pauseNudgeajax.job_title,
		grade: pauseNudgeajax.grade,
		team: pauseNudgeajax.team,
		key_skills: pauseNudgeajax.key_skills,
		bio: pauseNudgeajax.bio,
		linemanager: pauseNudgeajax.linemanager,
		photo: pauseNudgeajax.photo,
    }
    $.post( pauseNudgeajax.ajaxurl, data, function(data){
        var status  = $(data).find('response_data').text();
        var message = $(data).find('supplemental message').text();
        if( status == 'success' ) {
            jQuery(ipanel).html(message);
			jQuery(ipanel).slideDown(600);
        }
    });
    
    return false;
});


/******************************************
*
* UPDATE TELEPHONE NUMBER
*
*******************************************/
	
function update_profile_action_add_phone(){
	var itext = jQuery('#phone_' + pauseNudgeajax.widget_id).val();
	var ipanel = '#ht_profilenudge_' + pauseNudgeajax.widget_id;
	var ipanelerr = '#ht_profilenudge_errmsg_' + pauseNudgeajax.widget_id;
	var userid = pauseNudgeajax.user_id;
	var nonce = jQuery("[name='_wpnonce_add_phone_"+ pauseNudgeajax.widget_id+"']").val();
	var data = {
		action: 'ht_profile_nudge_ajax_action_add_phone',
		userid: userid,
		itext: itext,
		nonce: nonce,
		widget_id: pauseNudgeajax.widget_id,
	};
	jQuery.post(pauseNudgeajax.ajaxurl, data, function(data){
		var status = jQuery(data).find('response_data').text();
		var message = jQuery(data).find('supplemental message').text();

        if( status == 'success' ) {
			jQuery(ipanel).addClass("hidden");
			jQuery(ipanel).html(message);
			jQuery(ipanel).removeClass("hidden");
			jQuery(ipanel).slideDown();
		} else {
			jQuery(ipanelerr).html(message);			
		}
	});
	return false;
}


/******************************************
*
* UPDATE MOBILE NUMBER
*
*******************************************/

function update_profile_action_add_mobile(){
	var itext = jQuery('#mobile_' + pauseNudgeajax.widget_id).val();
	var ipanel = '#ht_profilenudge_' + pauseNudgeajax.widget_id;
	var ipanelerr = '#ht_profilenudge_errmsg_' + pauseNudgeajax.widget_id;
	var userid = pauseNudgeajax.user_id;
	var nonce = jQuery("[name='_wpnonce_add_mobile_"+ pauseNudgeajax.widget_id+"']").val();
	var data = {
		action: 'ht_profile_nudge_ajax_action_add_mobile',
		userid: userid,
		itext: itext,
		nonce: nonce,
		widget_id: pauseNudgeajax.widget_id,
	};
	jQuery.post(pauseNudgeajax.ajaxurl, data, function(data){
		var status = jQuery(data).find('response_data').text();
		var message = jQuery(data).find('supplemental message').text();

        if( status == 'success' ) {
			jQuery(ipanel).addClass("hidden");
			jQuery(ipanel).html(message);
			jQuery(ipanel).removeClass("hidden");
			jQuery(ipanel).slideDown();
		} else {
			jQuery(ipanelerr).html(message);			
		}
	});
	return false;
}

/******************************************
*
* UPDATE JOB TITLE
*
*******************************************/

function update_profile_action_add_job_title(){
	var itext = jQuery('#job_title_' + pauseNudgeajax.widget_id).val();
	var ipanel = '#ht_profilenudge_' + pauseNudgeajax.widget_id;
	var ipanelerr = '#ht_profilenudge_errmsg_' + pauseNudgeajax.widget_id;
	var userid = pauseNudgeajax.user_id;
	var nonce = jQuery("[name='_wpnonce_add_job_title_"+ pauseNudgeajax.widget_id+"']").val();
	var data = {
		action: 'ht_profile_nudge_ajax_action_add_job_title',
		userid: userid,
		itext: itext,
		nonce: nonce,
		widget_id: pauseNudgeajax.widget_id,
	};
	jQuery.post(pauseNudgeajax.ajaxurl, data, function(data){
		var status = jQuery(data).find('response_data').text();
		var message = jQuery(data).find('supplemental message').text();

        if( status == 'success' ) {
			jQuery(ipanel).addClass("hidden");
			jQuery(ipanel).html(message);
			jQuery(ipanel).removeClass("hidden");
			jQuery(ipanel).slideDown();
		} else {
			jQuery(ipanelerr).html(message);			
		}
	});
	return false;
}

/******************************************
*
* UPDATE GRADE
*
*******************************************/

function update_profile_action_add_grade(){
	var itext = jQuery('#grade_' + pauseNudgeajax.widget_id).val();
	var ipanel = '#ht_profilenudge_' + pauseNudgeajax.widget_id;
	var ipanelerr = '#ht_profilenudge_errmsg_' + pauseNudgeajax.widget_id;
	var userid = pauseNudgeajax.user_id;
	var nonce = jQuery("[name='_wpnonce_add_grade_"+ pauseNudgeajax.widget_id+"']").val();
	var data = {
		action: 'ht_profile_nudge_ajax_action_add_grade',
		userid: userid,
		itext: itext,
		nonce: nonce,
		widget_id: pauseNudgeajax.widget_id,
	};
	jQuery.post(pauseNudgeajax.ajaxurl, data, function(data){
		var status = jQuery(data).find('response_data').text();
		var message = jQuery(data).find('supplemental message').text();

        if( status == 'success' ) {
			jQuery(ipanel).addClass("hidden");
			jQuery(ipanel).html(message);
			jQuery(ipanel).removeClass("hidden");
			jQuery(ipanel).slideDown();
		} else {
			jQuery(ipanelerr).html(message);			
		}
	});
	return false;
}

/******************************************
*
* UPDATE TEAM
*
*******************************************/

function update_profile_action_add_team(){
	var itext = jQuery('#team_' + pauseNudgeajax.widget_id).val();
	var ipanel = '#ht_profilenudge_' + pauseNudgeajax.widget_id;
	var ipanelerr = '#ht_profilenudge_errmsg_' + pauseNudgeajax.widget_id;
	var userid = pauseNudgeajax.user_id;
	var nonce = jQuery("[name='_wpnonce_add_team_"+ pauseNudgeajax.widget_id+"']").val();
	var data = {
		action: 'ht_profile_nudge_ajax_action_add_team',
		userid: userid,
		itext: itext,
		nonce: nonce,
		widget_id: pauseNudgeajax.widget_id,
	};
	jQuery.post(pauseNudgeajax.ajaxurl, data, function(data){
		var status = jQuery(data).find('response_data').text();
		var message = jQuery(data).find('supplemental message').text();

        if( status == 'success' ) {
			jQuery(ipanel).addClass("hidden");
			jQuery(ipanel).html(message);
			jQuery(ipanel).removeClass("hidden");
			jQuery(ipanel).slideDown();
		} else {
			jQuery(ipanelerr).html(message);			
		}
	});
	return false;
}

/******************************************
*
* UPDATE SKILL AND EXPERIENCE
*
*******************************************/

function update_profile_action_add_skills(){
	var itext = jQuery('#skills_' + pauseNudgeajax.widget_id).val();
	var ipanel = '#ht_profilenudge_' + pauseNudgeajax.widget_id;
	var ipanelerr = '#ht_profilenudge_errmsg_' + pauseNudgeajax.widget_id;
	var userid = pauseNudgeajax.user_id;
	var nonce = jQuery("[name='_wpnonce_add_skills_"+ pauseNudgeajax.widget_id+"']").val();
	var data = {
		action: 'ht_profile_nudge_ajax_action_add_skills',
		userid: userid,
		itext: itext,
		nonce: nonce,
		widget_id: pauseNudgeajax.widget_id,
	};
	jQuery.post(pauseNudgeajax.ajaxurl, data, function(data){
		var status = jQuery(data).find('response_data').text();
		var message = jQuery(data).find('supplemental message').text();

        if( status == 'success' ) {
			jQuery(ipanel).addClass("hidden");
			jQuery(ipanel).html(message);
			jQuery(ipanel).removeClass("hidden");
			jQuery(ipanel).slideDown();
		} else {
			jQuery(ipanelerr).html(message);			
		}
	});
	return false;
}

/******************************************
*
* UPDATE BIO
*
*******************************************/

function update_profile_action_add_bio(){
	var itext = jQuery('#bio_' + pauseNudgeajax.widget_id).val();
	var ipanel = '#ht_profilenudge_' + pauseNudgeajax.widget_id;
	var ipanelerr = '#ht_profilenudge_errmsg_' + pauseNudgeajax.widget_id;
	var userid = pauseNudgeajax.user_id;
	var nonce = jQuery("[name='_wpnonce_add_bio_"+ pauseNudgeajax.widget_id+"']").val();
	var data = {
		action: 'ht_profile_nudge_ajax_action_add_bio',
		userid: userid,
		itext: itext,
		nonce: nonce,
		widget_id: pauseNudgeajax.widget_id,
	};
	jQuery.post(pauseNudgeajax.ajaxurl, data, function(data){
		var status = jQuery(data).find('response_data').text();
		var message = jQuery(data).find('supplemental message').text();

        if( status == 'success' ) {
			jQuery(ipanel).addClass("hidden");
			jQuery(ipanel).html(message);
			jQuery(ipanel).removeClass("hidden");
			jQuery(ipanel).slideDown();
		} else {
			jQuery(ipanelerr).html(message);			
		}
	});
	return false;
}




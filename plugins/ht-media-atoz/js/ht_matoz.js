jQuery(document).ready(function($) {
	jQuery('#docspinner').addClass('hidden');
	jQuery('.matoz').click(function(){
		jQuery('#docspinner').removeClass('hidden');
		jQuery('#docresults').slideUp();
		jQuery('#docsearchbutton').addClass('disabled');
		jQuery('#doctypebutton').addClass('disabled');
		jQuery('#doccatbutton').addClass('disabled');
		jQuery('.btn.btn-primary.btn-sm.pull-right.matoz').addClass('disabled');
		jQuery('.wp-pagenavi').addClass('hidden');
		return;
	});
	jQuery('.wp-pagenavi').click(function(){
		jQuery('#docspinner').removeClass('hidden');
		jQuery('#docresults').slideUp();
		jQuery('#docsearchbutton').addClass('disabled');
		jQuery('#doctypebutton').addClass('disabled');
		jQuery('#doccatbutton').addClass('disabled');
		jQuery('.btn.btn-primary.btn-sm.pull-right.matoz').addClass('disabled');
		jQuery('.wp-pagenavi').addClass('hidden');
		return;
	});
	jQuery('.docresult a[href*=".jpg"]').addClass('imgdocument').append(' (JPG)');
	jQuery('.docresult a[href*=".jpeg"]').addClass('imgdocument').append(' (JPG)');
	jQuery('.docresult a[href*=".png"]').addClass('imgdocument').append(' (PNG)');
	jQuery('.docresult a[href*=".gif"]').addClass('imgdocument').append(' (GIF)');
	jQuery('.docresult a[href*=".bmp"]').addClass('imgdocument').append(' (BMP)');
	jQuery('.docresult a[href*=".tiff"]').addClass('imgdocument').append(' (TIFF)');
	jQuery('#docsearchform').submit(function(e) {
	    if (jQuery.trim(jQuery("#q").val()) === "") {
	        e.preventDefault();
	        jQuery('#docspinner').addClass('hidden');
	        jQuery('#docresults').slideDown();
	        jQuery('#docsearchbutton').removeClass('disabled');
			jQuery('#doctypebutton').removeClass('disabled');
			jQuery('#doccatbutton').removeClass('disabled');
			jQuery('.btn.btn-primary.btn-sm.pull-right.matoz').removeClass('disabled');
			jQuery('.wp-pagenavi').removeClass('hidden');
	        jQuery('#q').focus();
	    }
	});	
});
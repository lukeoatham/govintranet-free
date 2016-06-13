jQuery(document).ready(function($) {
	jQuery('.matoz').click(function(){
		jQuery('#docspinner').removeClass('hidden');
		jQuery('#docresults').slideUp();
		jQuery('.wp-pagenavi').addClass('hidden');
		return;
	});
	jQuery('.docresult a[href*=".jpg"]').addClass('imgdocument').append(' (JPG)');
	jQuery('.docresult a[href*=".jpeg"]').addClass('imgdocument').append(' (JPG)');
	jQuery('.docresult a[href*=".png"]').addClass('imgdocument').append(' (PNG)');
	jQuery('.docresult a[href*=".gif"]').addClass('imgdocument').append(' (GIF)');
	jQuery('.docresult a[href*=".bmp"]').addClass('imgdocument').append(' (BMP)');
	jQuery('.docresult a[href*=".tiff"]').addClass('imgdocument').append(' (TIFF)');
});
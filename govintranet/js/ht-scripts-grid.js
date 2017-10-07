jQuery(document).ready(function($){
	var container = jQuery('#gridcontainer');
	container.imagesLoaded(function(){
		container.masonry({
			itemSelector: '.pgrid-item',
			gutter: 0,
			isAnimated: true
		});
	});
});
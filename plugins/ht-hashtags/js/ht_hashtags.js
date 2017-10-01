jQuery(document).ready(function($){
	var container = jQuery('#grimason');
	container.imagesLoaded(function(){
		container.masonry({
			itemSelector: '.grid-item',
			gutter: 5,
			isAnimated: true
		});
	});
});
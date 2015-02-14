(function($) {
	// $('.searchautocomplete-settings .sortable').sortable();
	var $settings = $('.searchautocomplete-settings');
	$settings.on('click.search-autocomplete', '.revert', function(e) {
		e.preventDefault();
		for(var key in SearchAutocompleteAdmin.defaults) {
			$('#' + key, $settings).val(SearchAutocompleteAdmin.defaults[key]);
		}
		$('#autocomplete_hotlink_titles', $settings).prop('checked', true);
		$('#autocomplete_hotlink_keywords', $settings).prop('checked', true);
		$('.autocomplete_taxonomies', $settings).prop('checked', false);
		$('.autocomplete_posttypes', $settings).prop('checked', false);
		$('#autocomplete_posttypes-post', $settings).prop('checked', true);
	});
})(jQuery);
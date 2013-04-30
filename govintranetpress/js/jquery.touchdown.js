/**
* jQuery Touchdown Plugin <https://github.com/samuelcotterall/Touchdown>
* 
* By Samuel Cotterall <http://samuelcotterall.com>
*/
(function($) {

	$.fn.Touchdown = function() {

		return this.each(function() {

			$this = $(this);
			
			var listDepth = $this.parents().length,
				anchor = $this.find('a'),
				title = 'Navigate: ',
				optionList;
			
			// Create a default `<option>` for the list - If this is missing, fall back to 'Select'
			if ($this.attr('title')) {
				title = $this.attr('title');
			} 
			
			optionList += '<option value="">' + title + '</option>';																	
			
			// Convert each anchor to an `<option>`
			for (var i=0; i < anchor.length; i++) {
				
				var a = $(anchor[i]), 										// Current <a>
					linkDepth = ((a.parents().length - listDepth) / 2) - 1, // Current <a>'s depth minus main list's depth divided by 2 (account for both <ul> and <li> parents) minus 1
					indent = '';											// Reset indent
					
				while (linkDepth > 0){										// Append a space for each level
					indent += '\u00a0 ';
					linkDepth--;
				}

				optionList += '<option value="' + a.attr('href') + '">' + indent + a.text() + '</option>';				
			
			}

			// DOM manipulation
			$this.addClass('touchdown-list').after('<select class="touchdown"> ' + optionList +'</select>');

			// Event handler
			$this.next('select').change(function(){
				window.location = $(this).val();
			});

		});
		
	};
	
})(jQuery);

(function($) {
	$(document).ready(function() {
		var selector = 'li.content-type-source-code ';

		$('div.field.field-content')
			.on('change keypress', selector + 'label.tab-size-toggle select', function() {
				var $textarea = $(this).closest('li').find('textarea');

				$textarea.removeClass('tab-size-2 tab-size-3 tab-size-4 tab-size-8');

				$textarea.addClass('tab-size-' + $(this).val());
			});
	});
})(jQuery);
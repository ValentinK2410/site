(function($) {
	'use strict';
	$(function() {
		$('#aft-preview-btn').on('click', function() {
			var text = $('#aft_raw_text').val();
			if (!text.trim()) {
				$('#aft-preview').hide();
				return;
			}
			$.post(aftData.ajaxUrl, {
				action: 'aft_preview',
				nonce: aftData.nonce,
				text: text
			}).done(function(r) {
				if (r.success && r.data && r.data.html) {
					$('#aft-preview-content').html(r.data.html);
					$('#aft-preview').show();
				}
			}).fail(function() {
				$('#aft-preview-content').html('<p>Ошибка загрузки предпросмотра.</p>');
				$('#aft-preview').show();
			});
		});
	});
})(jQuery);

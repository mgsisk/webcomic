jQuery(function($){
	if ($('[data-webcomic-infinite]').length) {
		if (undefined === $('[data-webcomic-infinite]').data('webcomic-offset')) {
			var $url_offset = document.location.search.match(/^\?offset=(\d+)$/);
			
			$('[data-webcomic-infinite]').data('webcomic-offset', (!$url_offset || undefined === $url_offset[1]) ? 0 : parseInt($url_offset[1]));
		}
		
		$(window).on('scroll', function() {
			if ($('[data-webcomic-infinite-end]').length || undefined !== $('[data-webcomic-infinite]').data('webcomic-loading')) {
				return;
			}
			
			if ( ! $('[data-webcomic-infinite]').children().length || $('[data-webcomic-infinite]').children().last().offset().top < $(window).scrollTop() + $(window).height()) {
				$('[data-webcomic-infinite]').data('webcomic-loading', true);
				
				var $offset = parseInt($('[data-webcomic-infinite]').children().length + $('[data-webcomic-infinite]').data('webcomic-offset')),
					$data ={
						'webcomic-infinite': true,
						page: $('[data-webcomic-infinite]').data('webcomic-infinite'),
						order: $('[data-webcomic-infinite]').data('webcomic-order'),
						offset: $offset,
						collection: $('[data-webcomic-infinite]').data('webcomic-collection')
					},
					$request = {
						url: window.location.href,
						type: 'post',
						data: $.param($data),
						success: function($data) {
							$('[data-webcomic-infinite]').append($data);
							
							history.replaceState($data, '', window.location.href.split('?')[0] + '?offset=' + $offset);
						},
						complete: function($object, $status) {
							$('[data-webcomic-infinite]').removeData('webcomic-loading');
							
							if ($('[data-webcomic-infinite]').children().length && $('[data-webcomic-infinite]').children().last().offset().top < $(window).scrollTop() + $(window).height()) {
								console.log('test');
								$(window).trigger('scroll');
							}
						}
							
					}
				
				$.ajax($request);
			}
		}).trigger('scroll');
	}
});
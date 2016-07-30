(function($){
	$(function(){
		$(document ).bind('onp-sl-filter-preview-options', function(){

			if ( !window.bizpanda ) window.bizpanda = {};
			if ( !window.bizpanda.socialOptions ) window.bizpanda.socialOptions = {};

			window.bizpanda.socialOptions.filterOptions = function(options) {
				options = $.extend(true, {
					/*locker: {
						loader: $('#opanda_addon_loader' ).is('checked')
					},*/
					socialButtons: {
						livejournal: {
							userName: $("#opanda_livejournal_user_name").val()
						},
                        instagram: {
                            userName: $("#opanda_instagram_user_name").val()
                        }
					}
				}, options);
				return options;
			}

		});
		/*opToggleOptionsCloseButton('#opanda_rotate_close_button_available');

		$('#opanda_rotate_close_button_available' ).change(function(){
			opToggleOptionsCloseButton(this);
		});

		function opToggleOptionsCloseButton(selector) {
			if( $(selector).is(':checked') ) {
				$('.factory-control-interval_show_button,' +
				' .factory-control-interval_hide_button' ).fadeIn();
			} else {
				$('.factory-control-interval_show_button,' +
				' .factory-control-interval_hide_button' ).fadeOut();
			}
		}*/
	});
})(jQuery);
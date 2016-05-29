(function($){
	$(function(){
		$(document ).bind('onp-sl-filter-preview-options', function(){

			if ( !window.bizpanda ) window.bizpanda = {};
			if ( !window.bizpanda.socialOptions ) window.bizpanda.socialOptions = {};

			window.bizpanda.socialOptions.filterOptions = function(options) {
				options = $.extend(true, {
					socialButtons: {
						loader: {
							timer: $("#opanda_loader_timer").val()
						}
					}
				}, options);
				return options;
			}

		});
	});
})(jQuery);
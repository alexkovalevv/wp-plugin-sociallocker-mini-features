( function($) {
	$(document ).ready(
		function(){
			tinymce.PluginManager.add( 'ylocker', function( editor, url ) {
				var menuCreated = false;

				var menu = [];

				editor.addButton( 'ylocker', {
					title: 'Добавить youtube видео с соц. замком ',
					//type: 'menubutton',
					icon: 'icon onp-sl-addon-ylocker-shortcode-icon',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Вставка youtube видео с соц. замком',
							body: [
								{
									type: 'textbox',
									name: 'onp_sl_addon_ylocker_video_id',
									label: 'Введите ID видео:'
								},
								{
									type: 'textbox',
									name: 'onp_sl_addon_ylocker_video_height',
									label: 'Высота видео:',
									value: 400

								},
								{
									type: 'textbox',
									name: 'onp_sl_addon_ylocker_video_width',
									label: 'Ширина видео:',
									value: 600
								},
								{
									type: 'listbox',
									name: 'onp_sl_addon_ylocker_locker_id',
									label: 'Выберите замок',
									values: menu
								}
							],
							onsubmit: function( e ) {
								editor.insertContent( '[ylocker video_id="' + e.data.onp_sl_addon_ylocker_video_id + '" video_height="' + e.data.onp_sl_addon_ylocker_video_height + '" video_width="' + e.data.onp_sl_addon_ylocker_video_width + '" locker_id="' + e.data.onp_sl_addon_ylocker_locker_id + '"]');
							}
						});
					},

					/*
					 * After rendeing contol, starts to load manu items (locker shortcodes).
					 */
					onpostrender: function(e) {
						if ( menuCreated ) return;
						menuCreated = true;

						var self = this;

						var req = $.ajax({
							url: ajaxurl,
							type: 'post',
							dataType: 'json',
							data: {
								action: 'get_opanda_lockers'
							},
							success: function(data, textStatus, jqXHR) {

								$.each(data, function(index, item) {

									menu.push({
										text: item.title,
										value: item.id
									});
								});
							}
						});
					}
				});
			});
		}
	);
} )(jQuery);

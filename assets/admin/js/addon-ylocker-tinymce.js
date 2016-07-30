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
									label: 'Высота видео (если 0, то высота 100%):',
									value: 0

								},
								{
									type: 'textbox',
									name: 'onp_sl_addon_ylocker_video_width',
									label: 'Ширина видео (если 0, то ширина 100%):',
									value: 0
								},
								{
									type: 'textbox',
									name: 'onp_sl_addon_ylocker_video_start',
									label: 'Начало видео:',
									value: 0
								},
								{
									type: 'textbox',
									name: 'onp_sl_addon_ylocker_video_end',
									label: 'Конец видео:',
									value: 0
								},
								{
									type: 'listbox',
									name: 'onp_sl_addon_ylocker_locker_id',
									label: 'Выберите замок',
									values: menu
								}
							],
							onsubmit: function( e ) {
								editor.insertContent( '[ylocker' +
								' video_id="' + e.data.onp_sl_addon_ylocker_video_id + '"' +
								(e.data.onp_sl_addon_ylocker_video_height != 0
									? ' video_height="' + e.data.onp_sl_addon_ylocker_video_height + '"'
									: ''
								) +
								(e.data.onp_sl_addon_ylocker_video_width != 0
									? ' video_width="' + e.data.onp_sl_addon_ylocker_video_width + '"'
									: ''
								) +
								(e.data.onp_sl_addon_ylocker_video_start != 0
									? ' video_start="' + e.data.onp_sl_addon_ylocker_video_start + '"'
									: ''
								) +
								(e.data.onp_sl_addon_ylocker_video_end != 0
									? ' video_end="' + e.data.onp_sl_addon_ylocker_video_end + '"'
									: ''
								) +
								' locker_id="' + e.data.onp_sl_addon_ylocker_locker_id +
								'"]');
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

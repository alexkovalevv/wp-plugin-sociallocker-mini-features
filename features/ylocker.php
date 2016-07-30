<?php
// Запрет на выполнение скрипта, если у пользователя нет прав к TinyMCE
if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') && get_user_option('rich_editing') == 'true')
	return;


function onp_sl_addon_register_tinymce_d8gs8($plugin_array) {
	$plugin_array['ylocker'] = OPANDA_SLA_PLUGIN_URL . '/assets/admin/js/addon-ylocker-tinymce.js';
	return $plugin_array;
}
// Регистрируем наш tinimce плагин
add_filter("mce_external_plugins", "onp_sl_addon_register_tinymce_d8gs8");


function onp_sl_addon_add_tinymce_button_d8gs8($buttons) {
	$buttons[] = "ylocker";

	return $buttons;
}
// Добавляем кнопку в панели TinyMCE
add_filter('mce_buttons', 'onp_sl_addon_add_tinymce_button_d8gs8');

function onp_sl_addon_s4j0d_enqueue_scripts_to_post($hook) {

	if ( !in_array( $hook, array('post.php', 'post-new.php')) ) return;
	if( get_post_type() === 'opanda-item') return;

	wp_enqueue_style( 'onp-sl-addon-post-edit-s4j0d', OPANDA_SLA_PLUGIN_URL . '/assets/admin/css/addon-post-edit.css' );
}
add_action( 'admin_enqueue_scripts', 'onp_sl_addon_s4j0d_enqueue_scripts_to_post' );

// Регистрируем api youtube
wp_register_script('youtube-api', '//www.youtube.com/iframe_api');
// Регистрируем скрипты и стили расширения
wp_register_script('onp-sl-addon-ylocker', OPANDA_SLA_PLUGIN_URL . '/assets/frontend/js/addon-ylocker.js', array('opanda-lockers'));
wp_register_style('onp-sl-addon-ylocker', OPANDA_SLA_PLUGIN_URL . '/assets/frontend/css/addon-ylocker.css');

function onp_sl_addon_ylocker_head_scripts() {
	echo <<<HTML
	<!-- Ylocker расширение для Социального замка -->
	<script>
		var onp_sl_addon_ylocker_players = [],
		    onp_sl_addon_ylocker_lockers = {},
		    onp_sl_addon_ylocker_youtube_api_load = false;

		function onYouTubeIframeAPIReady() {
			onp_sl_addon_ylocker_youtube_api_load = true;
		}
	</script>
	<!-- /Ylocker расширение для Социального замка -->
HTML;

}
add_action('wp_head', 'onp_sl_addon_ylocker_head_scripts');

function onp_sl_addon_ylocker_shortcode( $atts ) {

	$video_height=$video_width=$video_start=$video_end = 0;

	extract($atts);

	if( !isset($video_id) || !isset($locker_id) ) {
		return "<strong style='color:red'>[Ошибка: не установлен id видео или id замка]</strong>";
	}

	if( strlen($video_id) !== 11 ) {
		return "<strong style='color:red'>[Ошибка: не корректно введен id видео]</strong>";
	}

	$full_width_class = "";

	if( empty($video_height) || empty($video_width) ) {
		$full_width_class = " full-width";
	}

	wp_enqueue_script('youtube-api');
	wp_enqueue_script('onp-sl-addon-ylocker');
	wp_enqueue_style('onp-sl-addon-ylocker');

	$rand = rand(2,99999);
	$output  = <<<HTML
		<!-- Ylocker #{$rand} видео блок -->
		<script>
			onp_sl_addon_ylocker_lockers[{$rand}] = {$locker_id};

			var interation_count_{$rand} = 0, onp_sl_addon_ylocker_{$rand}_interval = setInterval(
				function(){
					if( interation_count_{$rand} > 20 || onp_sl_addon_ylocker_youtube_api_load ) {
						clearInterval(onp_sl_addon_ylocker_{$rand}_interval);
					}
					if( onp_sl_addon_ylocker_youtube_api_load ) {
						onp_sl_addon_ylocker_players[$rand] = new YT.Player('onp-sl-addon-ylocker-{$rand}', {
							width: {$video_width},
					        height: {$video_height},
					        videoId: '{$video_id}',
					        playerVars: {
					            color: 'white',
					            start: {$video_start},
               					end: {$video_end}
					        },
					        events: {
					            onStateChange: on_player_{$rand}_state_change
					        }
						});
					}

					function on_player_{$rand}_state_change(responce) {
						if( responce['data'] === 0 ) {
							if( onp_sl_addon_ylocker_lockers[{$rand}] && typeof onp_sl_addon_ylocker_lockers[{$rand}] === 'object' ) {
								onp_sl_addon_ylocker_lockers[{$rand}]._oldLock();
							} else {
								console.log('%c[Ошибка: замок не существует]', 'color:red');
							}
						}
					}

					interation_count_{$rand}++;
				},100);
		</script>
		<div class="onp-sl-addon-ylocker-wrap{$full_width_class}">
			<div id="onp-sl-addon-ylocker-{$rand}" class="onp-sl-addon-ylocker-item{$full_width_class}"></div>
		</div>
		<!-- /Ylocker #{$rand} видео блок -->
HTML;

	return do_shortcode('[sociallocker id="' . $locker_id . '"]' . $output . '[/sociallocker]');
}
add_shortcode( 'ylocker', 'onp_sl_addon_ylocker_shortcode' );
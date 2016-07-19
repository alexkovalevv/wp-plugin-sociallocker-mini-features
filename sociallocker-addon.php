<?php
/**
Plugin Name: Расширение для Социального замка
Plugin URI: https://sociallocker.ru
Description: Плагин расширяет стандартные возможности Социального замка.
Author: Alex Kovalevv <alex.kovalevv@gmail.com>
Version: 1.0.4
Author URI: http://byoneress.com/
*/

define('OPANDA_SLA_PLUGIN_URL', plugins_url( null, __FILE__ ));
define('OPANDA_SLA_PLUGIN_DIR', dirname(__FILE__));

function onp_sl_addon_init() {

	// Если социальный замок не установлен, останавливаем расширение
	if( !defined('OPTINPANDA_PLUGIN_ACTIVE') && !defined('SOCIALLOCKER_PLUGIN_ACTIVE') ) return false;
	if( !defined('OPANDA_ACTIVE') ) return false;

	// Ротация кнопки закрыть
	//include_once OPANDA_SLA_PLUGIN_DIR . '/features/rotate-close-button.php';
	//include_once OPANDA_SLA_PLUGIN_DIR . '/features/other-buttons.php';

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

	//Подключаем jQuery расширение для фронтенда
	function onp_sl_addon_ylocker_enqueue_scripts() {
		wp_enqueue_script( 'onp-sl-addon-ylocker', OPANDA_SLA_PLUGIN_URL . '/assets/frontend/js/addon-ylocker.js', array('opanda-lockers'), false, true );
	}
	add_action( 'opanda_connect_locker_assets', 'onp_sl_addon_ylocker_enqueue_scripts' );

	// Регистрируем api youtube
	wp_register_script('youtube-api', 'https://www.youtube.com/iframe_api');

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
		function initialize(){
		    updateTimerDisplay && updateTimerDisplay();
		    updateProgressBar && updateProgressBar();

		    clearInterval(time_update_interval);

		    time_update_interval = setInterval(function () {
		        updateTimerDisplay && updateTimerDisplay();
		        updateProgressBar && updateProgressBar();
		    }, 1000);
		}
	</script>
	<!-- /Ylocker расширение для Социального замка -->
HTML;

	}
	add_action('wp_head', 'onp_sl_addon_ylocker_head_scripts');

	function onp_sl_addon_ylocker_shortcode( $atts ) {
		extract($atts);

		if( !isset($video_id) || !isset($locker_id) ) {
			return "<strong style='color:red'>[Ошибка: не установлен id видео или id замка]</strong>";
		}

		if( strlen($video_id) !== 11 ) {
			return "<strong style='color:red'>[Ошибка: не корректно введен id видео]</strong>";
		}

		if( !isset($video_height) || !isset($video_width) ) {
			$video_height = 400;
			$video_width = 600;
		}

		wp_enqueue_script('youtube-api');

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
					            color: 'white'
					        },
					        events: {
					            onReady: initialize,
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
		<div id="onp-sl-addon-ylocker-{$rand}"></div>
		<!-- /Ylocker #{$rand} видео блок -->
HTML;

		return do_shortcode('[sociallocker id="' . $locker_id . '"]' . $output . '[/sociallocker]');
	}
	add_shortcode( 'ylocker', 'onp_sl_addon_ylocker_shortcode' );
}
add_action( 'init', 'onp_sl_addon_init' );

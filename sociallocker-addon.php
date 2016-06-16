<?php
/**
Plugin Name: Расширение для Социального замка
Plugin URI: https://sociallocker.ru
Description: Плагин расширяет стандартные возможности Социального замка.
Author: OnePress
Version: 1.0.2
Author URI: http://byoneress.com/
*/

define('OPANDA_SLA_PLUGIN_URL', plugins_url( null, __FILE__ ));
define('OPANDA_SLA_PLUGIN_DIR', dirname(__FILE__));

function onp_sl_addon_init() {

	//Если социальный замок не установлен, останавливаем расширение
	if( !defined('OPTINPANDA_PLUGIN_ACTIVE') && !defined('SOCIALLOCKER_PLUGIN_ACTIVE') ) return false;
	if( !defined('OPANDA_ACTIVE') ) return false;

	/**
	 * Данная модификация позволяет создавать ротацию кнопки "закрыть" для солиального замка
	*/
	$lockers = get_posts([
		'post_type' => 'opanda-item'
	]);

	foreach( $lockers as $locker ) {
		$rotate_close_button_available = get_post_meta($locker->ID, 'opanda_rotate_close_button_available', true);
		$interval_show_button = get_post_meta($locker->ID, 'opanda_interval_show_button', true) * 60;
		$interval_hide_button = get_post_meta($locker->ID, 'opanda_interval_hide_button', true) * 60;

		if( $rotate_close_button_available ) {
			$mark_show_button = get_post_meta($locker->ID, 'opanda_mark_show_close_button', true);
			$mark_hide_button = get_post_meta($locker->ID, 'opanda_mark_hide_close_button', true);
			$time_rotate_button = get_post_meta($locker->ID, 'opanda_time_rotate_close_button', true);

			if( ($mark_show_button && time() > $time_rotate_button) || (!$mark_show_button && !$mark_hide_button) ) {
				delete_post_meta($locker->ID, 'opanda_mark_show_close_button');
				delete_post_meta($locker->ID, 'opanda_close');

				update_post_meta($locker->ID, 'opanda_mark_hide_close_button', true);
				update_post_meta($locker->ID, 'opanda_time_rotate_close_button', time() + $interval_hide_button);

			} else if( $mark_hide_button && time() > $time_rotate_button ) {
				delete_post_meta($locker->ID, 'opanda_mark_hide_close_button');

				update_post_meta($locker->ID, 'opanda_close', true);
				update_post_meta($locker->ID, 'opanda_mark_show_close_button', true);
				update_post_meta($locker->ID, 'opanda_time_rotate_close_button', time() + $interval_show_button);
			}
		}
	}

	//Подключем файл стилей расширения для метабокса кнопок в админке
	function onp_sl_addon_enqueue_scripts_to_post($hook) {
		if ( !in_array( $hook, array('post.php', 'post-new.php')) ) return;
		if( get_post_type() !== 'opanda-item') return;

		wp_enqueue_style( 'onp-sl-addon-item-edit', OPANDA_SLA_PLUGIN_URL . '/assets/admin/css/addon-item-edit.css' );
		wp_enqueue_script( 'onp-sl-addon-social-options', OPANDA_SLA_PLUGIN_URL . '/assets/admin/js/addon-social-options.js');
	}
	add_action( 'admin_enqueue_scripts', 'onp_sl_addon_enqueue_scripts_to_post' );

	//Подключаем jQuery расширение для превью в админке
	//function onp_sl_addon_enqueue_scripts_to_preview() {
		?>
		<!--<script type="text/javascript" src="<?php echo OPANDA_SLA_PLUGIN_URL ?>/assets/frontend/js/addon-locker-loader.js"></script>
		<script type="text/javascript" src="<?php echo OPANDA_SLA_PLUGIN_URL ?>/assets/frontend/js/addon-button.livejournal.js"></script>
		<link rel="stylesheet" href="<?php echo OPANDA_SLA_PLUGIN_URL ?>/assets/frontend/css/addon-button.livejournal.css" type='text/css' media='all'>-->
		<?php
	//}
	//add_action( 'onp_sl_preview_head', 'onp_sl_addon_enqueue_scripts_to_preview' );

	//Подключаем jQuery расширение для фронтенда
	//function onp_sl_addon_enqueue_scripts_to_frontend() {
		//wp_enqueue_script( 'onp-sl-button-loader', OPANDA_SLA_PLUGIN_URL . '/assets/frontend/js/addon-locker-loader.js', array('opanda-lockers'), false, true );
		//wp_enqueue_script( 'onp-sl-button-livejournal', OPANDA_SLA_PLUGIN_URL . '/assets/frontend/js/addon-button.livejournal.js', array('opanda-lockers'), false, true );
		//wp_enqueue_style( 'onp-sl-button-livejournal', OPANDA_SLA_PLUGIN_URL . '/assets/frontend/css/addon-button.livejournal.css' );
	//}
	//add_action( 'opanda_connect_locker_assets', 'onp_sl_addon_enqueue_scripts_to_frontend' );

	//Добавляем опции на панель дополнительных опций
	function onp_sl_addon_advanced_options($options) {
		/*$options[] = array(
			'type'      => 'checkbox',
			'way'       => 'buttons',
			'name'      => 'addon_loader',
			'title'     => 'Загрузка',
			'hint'      => 'Если Вкл, для пользователей подписанных на страницу фейсбук, будет показываться полоса загрузки.',
			'icon'      => OPANDA_BIZPANDA_URL . '/assets/admin/img/ajax-icon.png',
			'default'   => false
		);*/
		$options[] = array(
			'type'      => 'checkbox',
			'way'       => 'buttons',
			'name'      => 'rotate_close_button_available',
			'title'     => 'Ротация кнопки "Закрыть"',
			'hint'      => 'Если Вкл, кнопка "закрыть" будет показываться и скрываться через интервалы времени.',
			'default'   => false
		);
		$options[] = array(
			'type'      => 'textbox',
			'name'      => 'interval_show_button',
			'title'     => 'Показать',
			'hint'      => 'Сколько времени показывать кнопку в минутах.',
			'default'   => '60'
		);
		$options[] = array(
			'type'      => 'textbox',
			'name'      => 'interval_hide_button',
			'title'     => 'Скрыть',
			'hint'      => 'Сколько времени скрывать кнопку в минутах.',
			'default'   => '30'
		);
		return $options;
	}
	add_filter('opanda_advanced_options', 'onp_sl_addon_advanced_options', 10, 1);

	//Добавляем кнопку в метабокс кнопок
	/*function onp_sl_addon_add_buttons($tabs) {
		$tabs['items'][] = array(
			'type'  => 'tab-item',
			'name'  => 'livejournal',
			'items' => array(
				array(
					'type'    => 'checkbox',
					'way'     => 'buttons',
					'title'   => 'Активировать',
					'hint'    => 'Если Вкл, кнопка будет активирована в вашем замке.',
					'name'    => 'livejournal_available',
					'default' => false
				),
				array(
					'type'  => 'textbox',
					'title' => 'Имя пользователя',
					'hint'  => 'Установите ваше имя пользователя в livejournal.',
					'name'  => 'livejournal_user_name'
				)
			)
		);

		return $tabs;
	}*/
	//add_filter( 'onp_sl_social_options', 'onp_sl_addon_add_buttons' );

	//Печатаем опции для кнопки на фронтенд
	//function onp_sl_addon_locker_options( $options, $id ) {

		//$options['locker']['loader'] = opanda_get_item_option($id, 'addon_loader', false );

		/*$options['socialButtons']['livejournal'] = array(
			'userName' => opanda_get_item_option($id, 'livejournal_user_name', 50 )
		);*/

		//return $options;
	//}
	//add_filter('opanda_social-locker_item_options', 'onp_sl_addon_locker_options', 10, 2);

	//Добавляем кнопку список разрешенных кнопок
	/* onp_sl_addon_locker_allow_buttons($allowedButtons) {
		//$allowedButtons[] = 'livejournal';
		//return $allowedButtons;
	}*/
	//add_filter('opanda_social-locker_allowed_buttons', 'onp_sl_addon_locker_allow_buttons', 10, 1);

	//Добавлям метку в статистику
	/*function onp_sl_addon_detailed_stats_table($table) {
		$table['channels']['columns']['unlock-via-livejournal'] = array(
			'title' => 'livejournal',
			'cssClass' => 'opanda-col-number'
		);
		return $table;
	}*/
	//add_filter('onp_sl_detailed_stats_table', 'onp_sl_addon_detailed_stats_table', 10, 1);

	//Добавляем столбец в таблицу статистики
	/*function onp_sl_addon_detailed_stats_chart($channels) {
		$channels['unlock-via-livejournal'] = array(
			'title' => 'livejournal',
			'color' => '#21A5D8'
		);
		return $channels;
	}*/
	//add_filter('onp_sl_detailed_stats_chart', 'onp_sl_addon_detailed_stats_chart', 10, 1);
}
add_action( 'init', 'onp_sl_addon_init' );

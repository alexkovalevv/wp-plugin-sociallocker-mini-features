<?php
/**
Plugin Name: Расширение для Социального замка
Plugin URI: https://sociallocker.ru
Description: Плагин расширяет стандартные возможности Социального замка.
Author: OnePress
Version: 1.0.0
Author URI: http://byoneress.com/
*/

define('OPANDA_SLA_PLUGIN_URL', plugins_url( null, __FILE__ ));
define('OPANDA_SLA_PLUGIN_DIR', dirname(__FILE__));

function onp_sl_addon_init() {

	//Если социальный замок не установлен, останавливаем расширение
	if( !defined('OPTINPANDA_PLUGIN_ACTIVE') && !defined('SOCIALLOCKER_PLUGIN_ACTIVE') ) return false;
	if( !defined('OPANDA_ACTIVE') ) return false;

	//Подключем файл стилей расширения для метабокса кнопок в админке
	function onp_sl_addon_enqueue_scripts_to_post($hook) {
		if ( !in_array( $hook, array('post.php', 'post-new.php')) ) return;
		if( get_post_type() !== 'opanda-item') return;

		wp_enqueue_style( 'onp-sl-addon-item-edit', OPANDA_SLA_PLUGIN_URL . '/assets/admin/css/addon-item-edit.css' );
		wp_enqueue_script( 'onp-sl-addon-social-options', OPANDA_SLA_PLUGIN_URL . '/assets/admin/js/addon-social-options.js');
	}
	add_action( 'admin_enqueue_scripts', 'onp_sl_addon_enqueue_scripts_to_post' );

	//Подключаем jQuery расширение для превью в админке
	function onp_sl_addon_enqueue_scripts_to_preview() {
		?>
		<script type="text/javascript" src="<?php echo OPANDA_SLA_PLUGIN_URL ?>/assets/frontend/js/addon-button.loader.js"></script>
		<?php
	}
	add_action( 'onp_sl_preview_head', 'onp_sl_addon_enqueue_scripts_to_preview' );

	//Подключаем jQuery расширение для фронтенда
	function onp_sl_addon_enqueue_scripts_to_frontend() {
		wp_enqueue_script( 'onp-sl-button-loader-load', OPANDA_SLA_PLUGIN_URL . '/assets/frontend/js/addon-button.loader.js', array('opanda-lockers'), false, true );
	}
	add_action( 'opanda_connect_locker_assets', 'onp_sl_addon_enqueue_scripts_to_frontend' );

	//Добавляем кнопку в метабокс кнопок
	function onp_sl_addon_add_buttons($tabs) {
		$tabs['items'][] = array(
			'type'  => 'tab-item',
			'name'  => 'loader',
			'items' => array(
				array(
					'type'    => 'checkbox',
					'way'     => 'buttons',
					'title'   => 'Активировать',
					'hint'    => 'Если Вкл, кнопка будет активирована в вашем замке.',
					'name'    => 'loader_available',
					'default' => false
				),
				array(
					'type'  => 'textbox',
					'title' => 'Таймер',
					'hint'  => 'Установите интервал времени в секундах, через который будет открыт замок.',
					'name'  => 'loader_timer'
				)
			)
		);

		return $tabs;
	}
	add_filter( 'onp_sl_social_options', 'onp_sl_addon_add_buttons' );

	//Печатаем опции для кнопки на фронтенд
	function onp_sl_addon_locker_options( $options, $id ) {
		$options['socialButtons']['loader'] = array(
			'timer' => opanda_get_item_option($id, 'loader_timer' )
		);

		return $options;
	}
	add_filter('opanda_social-locker_item_options', 'onp_sl_addon_locker_options', 10, 2);

	//Добавляем кнопку список разрешенных кнопок
	function onp_sl_addon_locker_allow_buttons($allowedButtons) {
		$allowedButtons[] = 'loader';
		return $allowedButtons;
	}
	add_filter('opanda_social-locker_allowed_buttons', 'onp_sl_addon_locker_allow_buttons', 10, 1);

	//Добавлям метку в статистику
	function onp_sl_addon_detailed_stats_table($table) {
		$table['channels']['columns']['unlock-via-loader'] = array(
			'title' => 'Загрузка',
			'cssClass' => 'opanda-col-number'
		);
		return $table;
	}
	add_filter('onp_sl_detailed_stats_table', 'onp_sl_addon_detailed_stats_table', 10, 1);

	//Добавляем столбец в таблицу статистики
	function onp_sl_addon_detailed_stats_chart($channels) {
		$channels['unlock-via-loader'] = array(
			'title' => 'Загрузка',
			'color' => '#222'
		);
		return $channels;
	}
	add_filter('onp_sl_detailed_stats_chart', 'onp_sl_addon_detailed_stats_chart', 10, 1);
}
add_action( 'init', 'onp_sl_addon_init' );


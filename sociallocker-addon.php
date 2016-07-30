<?php
/**
Plugin Name: Расширение для Социального замка
Plugin URI: https://sociallocker.ru
Description: Плагин расширяет стандартные возможности Социального замка.
Author: Alex Kovalevv <alex.kovalevv@gmail.com>
Version: 1.0.6s
Author URI: http://byoneress.com/
*/

define('OPANDA_SLA_PLUGIN_URL', plugins_url( null, __FILE__ ));
define('OPANDA_SLA_PLUGIN_DIR', dirname(__FILE__));

function onp_sl_addon_init() {

	// Если социальный замок не установлен, останавливаем расширение
	if( !defined('OPTINPANDA_PLUGIN_ACTIVE') && !defined('SOCIALLOCKER_PLUGIN_ACTIVE') ) return false;
	if( !defined('OPANDA_ACTIVE') ) return false;

	// Ротация кнопки закрыть
	// include_once OPANDA_SLA_PLUGIN_DIR . '/features/rotate-close-button.php';
	// include_once OPANDA_SLA_PLUGIN_DIR . '/features/ylocker.php';
	 include_once OPANDA_SLA_PLUGIN_DIR . '/features/other-buttons.php';

}
add_action( 'init', 'onp_sl_addon_init' );

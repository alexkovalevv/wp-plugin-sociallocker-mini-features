<?php
/**
 * Данная модификация добавяет новые кнопки для солиального замка
 * @author Alex Kovalev <alex.kovalevv@gmail.com>
 */


//Подключем файл стилей расширения для метабокса кнопок в админке
function onp_sl_addon_enqueue_scripts_to_post($hook) {
	wp_enqueue_script( 'onp-sl-addon-tiny-mce', OPANDA_SLA_PLUGIN_URL . '/assets/admin/js/addon-tiny-mce.js');

	if ( !in_array( $hook, array('post.php', 'post-new.php')) ) return;
	if( get_post_type() !== 'opanda-item') return;

	wp_enqueue_style( 'onp-sl-addon-item-edit', OPANDA_SLA_PLUGIN_URL . '/assets/admin/css/addon-item-edit.css' );
	wp_enqueue_script( 'onp-sl-addon-social-options', OPANDA_SLA_PLUGIN_URL . '/assets/admin/js/addon-social-options.js');

}
add_action( 'admin_enqueue_scripts', 'onp_sl_addon_enqueue_scripts_to_post' );

//Подключаем jQuery расширение для превью в админке
function onp_sl_addon_enqueue_scripts_to_preview() {
	?>
	<!--<script type="text/javascript" src="<?php echo OPANDA_SLA_PLUGIN_URL ?>/assets/frontend/js/addon-locker-loader.js"></script>-->
	<script type="text/javascript" src="<?php echo OPANDA_SLA_PLUGIN_URL ?>/assets/frontend/js/addon-button.livejournal.js"></script>
	<link rel="stylesheet" href="<?php echo OPANDA_SLA_PLUGIN_URL ?>/assets/frontend/css/addon-button.livejournal.css" type='text/css' media='all'>
	<script type="text/javascript" src="<?php echo OPANDA_SLA_PLUGIN_URL ?>/assets/frontend/js/addon-button.instagram.js"></script>
	<link rel="stylesheet" href="<?php echo OPANDA_SLA_PLUGIN_URL ?>/assets/frontend/css/addon-button.instagram.css" type='text/css' media='all'>
	<?php
}
add_action( 'onp_sl_preview_head', 'onp_sl_addon_enqueue_scripts_to_preview' );

//Подключаем jQuery расширение для фронтенда
function onp_sl_addon_enqueue_scripts_to_frontend() {
	//wp_enqueue_script( 'onp-sl-button-loader', OPANDA_SLA_PLUGIN_URL . '/assets/frontend/js/addon-locker-loader.js', array('opanda-lockers'), false, true );
	wp_enqueue_script( 'onp-sl-button-livejournal', OPANDA_SLA_PLUGIN_URL . '/assets/frontend/js/addon-button.livejournal.js', array('opanda-lockers'), false, true );
	wp_enqueue_style( 'onp-sl-button-livejournal', OPANDA_SLA_PLUGIN_URL . '/assets/frontend/css/addon-button.livejournal.css' );
	wp_enqueue_script( 'onp-sl-button-instagram', OPANDA_SLA_PLUGIN_URL . '/assets/frontend/js/addon-button.instagram.js', array('opanda-lockers'), false, true );
	wp_enqueue_style( 'onp-sl-button-instagram', OPANDA_SLA_PLUGIN_URL . '/assets/frontend/css/addon-button.instagram.css' );
}
add_action( 'opanda_connect_locker_assets', 'onp_sl_addon_enqueue_scripts_to_frontend' );

//Добавляем опции на панель дополнительных опций
/*function onp_sl_addon_advanced_options($options) {
	$options[] = array(
		'type'      => 'checkbox',
		'way'       => 'buttons',
		'name'      => 'addon_loader',
		'title'     => 'Загрузка',
		'hint'      => 'Если Вкл, для пользователей подписанных на страницу фейсбук, будет показываться полоса загрузки.',
		'icon'      => OPANDA_BIZPANDA_URL . '/assets/admin/img/ajax-icon.png',
		'default'   => false
	);
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
add_filter('opanda_advanced_options', 'onp_sl_addon_advanced_options', 10, 1);*/

//Добавляем кнопку в метабокс кнопок
function onp_sl_addon_add_buttons($tabs) {
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

	$tabs['items'][] = array(
		'type'  => 'tab-item',
		'name'  => 'instagram',
		'items' => array(
			array(
				'type'    => 'checkbox',
				'way'     => 'buttons',
				'title'   => 'Активировать',
				'hint'    => 'Если Вкл, кнопка будет активирована в вашем замке.',
				'name'    => 'instagram_available',
				'default' => false
			),
			array(
				'type'  => 'textbox',
				'title' => 'Имя пользователя',
				'hint'  => 'Установите ваше имя пользователя в Instagram.',
				'name'  => 'instagram_user_name'
			)
		)
	);

	return $tabs;
}
add_filter( 'onp_sl_social_options', 'onp_sl_addon_add_buttons' );

//Печатаем опции для кнопки на фронтенд
function onp_sl_addon_locker_options( $options, $id ) {

	//$options['locker']['loader'] = opanda_get_item_option($id, 'addon_loader', false );

	$options['socialButtons']['livejournal'] = array(
		'userName' => opanda_get_item_option($id, 'livejournal_user_name', 50 )
	);

	$options['socialButtons']['instagram'] = array(
		'userName' => opanda_get_item_option($id, 'instagram_user_name', 50 )
	);

	return $options;
}
add_filter('opanda_social-locker_item_options', 'onp_sl_addon_locker_options', 10, 2);

//Добавляем кнопку список разрешенных кнопок
function onp_sl_addon_locker_allow_buttons($allowedButtons) {
	$allowedButtons[] = 'livejournal';
	$allowedButtons[] = 'instagram';
	return $allowedButtons;
}
add_filter('opanda_social-locker_allowed_buttons', 'onp_sl_addon_locker_allow_buttons', 10, 1);

//Добавляем столбец в таблицу статистики
function onp_sl_addon_detailed_stats_table($table) {
	$table['channels']['columns']['unlock-via-livejournal'] = array(
		'title' => 'livejournal',
		'cssClass' => 'opanda-col-number'
	);
	$table['channels']['columns']['unlock-via-instagram'] = array(
		'title' => 'instagram',
		'cssClass' => 'opanda-col-number'
	);
	return $table;
}
add_filter('onp_sl_detailed_stats_table', 'onp_sl_addon_detailed_stats_table', 10, 1);

//Добавлям метку в статистику
function onp_sl_addon_detailed_stats_chart($channels) {
	$channels['unlock-via-livejournal'] = array(
		'title' => 'livejournal',
		'color' => '#21A5D8'
	);
	$channels['unlock-via-instagram'] = array(
		'title' => 'instagram',
		'color' => '#527fa4'
	);
	return $channels;
}
add_filter('onp_sl_detailed_stats_chart', 'onp_sl_addon_detailed_stats_chart', 10, 1);

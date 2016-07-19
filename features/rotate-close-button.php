<?php
/**
 * Данная модификация позволяет создавать ротацию кнопки "закрыть" для солиального замка
 * @author Alex Kovalev <alex.kovalevv@gmail.com>
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

			//Сбрасываем кеш
			if(function_exists('w3tc_pgcache_flush')) {  w3tc_pgcache_flush();  }

		} else if( $mark_hide_button && time() > $time_rotate_button ) {
			delete_post_meta($locker->ID, 'opanda_mark_hide_close_button');

			update_post_meta($locker->ID, 'opanda_close', true);
			update_post_meta($locker->ID, 'opanda_mark_show_close_button', true);
			update_post_meta($locker->ID, 'opanda_time_rotate_close_button', time() + $interval_show_button);

			//Сбрасываем кеш
			if(function_exists('w3tc_pgcache_flush')) {  w3tc_pgcache_flush();  }
		}
	}
}

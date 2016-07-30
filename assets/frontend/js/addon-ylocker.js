/**
 * Created by Александр on 19.07.2016.
 */

(function ($) {
	$.pandalocker.hooks.add('opanda-init', function(e, locker){

		var lock = true;
		locker.options.demo = true;

		locker._oldLock = locker._lock;
		locker._lock = function() {
			return false;
		};

		for( k in onp_sl_addon_ylocker_lockers ) {
			if( locker.options.lockerId === onp_sl_addon_ylocker_lockers[k]
				&& locker.element.find('#onp-sl-addon-ylocker-' + k ).length
			) onp_sl_addon_ylocker_lockers[k] = locker;
		}
	});
})(jQuery);
/*!
 * Loader Button
 * Copyright 2016, OnePress, http://byonepress.com
 */
(function ($) {
	'use strict';

	var button = $.pandalocker.tools.extend( $.pandalocker.entity.socialButton );

	button.name = "loader";

	button.verification = {
		container: 'div',
		timeout: 5000
	};

	button._defaults = {
		timer: 50
	};

	button.prepareOptions = function() {
		this.url = this._extractUrl();
	};

	/**
	 * Setups hooks.
	 */
	button.setupHooks = function() {
		var self = this;

		this.addHook('init', function(){
				self.locker._registerScreen('loader-button-processing',
				function( $holder, options ) {

					var progressBar = $('<progress value="0" max="' + self.options.timer + '">'+
					'Загружено <span class="percent">0</span>%'+
					'</progress>');

					progressBar.css({
						width:"100%",
						height: "7px",
						margin: "20px 0"
					});

					$holder.append( $('<div style="text-align:center;font-size: 18px;font-weight: bold;color:#1653B7;margin-bottom: 15px;">' + self.locker.options.text.header + '</div>') );
					$holder.append( $('<div>' + self.locker.options.text.message + '</div>') );
					$holder.append( progressBar );

					var countInteration = 0;
					var progressBarTimer = setInterval(function(){
						if( self.options.timer > countInteration ) {
							progressBar.val( progressBar.val() + 1 );
							progressBar.find('.percent' ).text( progressBar.val() + 1 );
						} else {
							clearInterval(progressBarTimer);
							self.unlock("button", self.name, self.url );
						}
						countInteration++;
					}, 1000);
				}
			);
		});

		this.addHook('markup-created', function(){
			self.locker._showScreen('loader-button-processing');
		});
	};

	/**
	 * Setups events.
	 */
	button.setupEvents = function () {};

	/**
	 * Renders the button.
	 */
	button.renderButton = function( $holder ) {
		this.button = $("<div></div>").appendTo( $holder );
	};

	$.pandalocker.controls["social-buttons"]["loader"] = button;

})(jQuery);
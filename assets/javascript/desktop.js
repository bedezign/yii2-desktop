var desktop = (function ($, window, document, undefined) {
	return {
		// Start the desktop
		boot: function(settings) {
			// Define default callbacks
			if (!desktop._settings.callbacks)
				desktop._settings.callbacks = {};

			desktop._settings = $.extend(desktop._settings, settings);

			for(var component in desktop._bootstraps)
				desktop._bootstraps[component]();
		},

		launch_application: function(link) {
			var $link = $(link), $dock = $($link.attr('href')), $application = $($dock.attr('href'));

			// Show the taskbar button.
			if ($dock.is(':hidden'))
				// Move the li entry to the end of the list and show it
				$dock.parents('li').appendTo('.desktop_wrapper .dock ul').show('fast');

			desktop._helpers.activate($application).show();
			desktop._helpers.iframe_launched($application);
			desktop._helpers.event_dispatch('application.launched', $application);
		},

		close_application: function(link) {
			var $link = $(link), $application = $link.closest('div.application_window');
			$application.hide();
			$($link.attr('href')).parents('li').hide('fast');
			desktop._helpers.event_dispatch('application.closed', $application);
		},

		/**
		 * Sets or retrieves application session data.
		 * Since this can be serialized between calls and needs to be restored, please refrain from trying to store functions
		 * in here etc.
		 *
		 * @param application      ID of the application to use
		 * @param data             If not empty, the data to store. If empty the existing data will be recovered
		 */
		session_data: function(application, data) {
			var $application = $('#' + application);

			if (!$application.length) return;

			if (typeof data =='undefined') {
				return $application.data('desktop-session');
			}
			else {
				$application.data('desktop-session', data);
				desktop._helpers.event_dispatch('application.session-updated', $application, {application: application, session: data});
			}
		},

		minimize_application: function(link) {
			var $link = $(link), $application = $link.closest('div.application_window');
			$application.hide();
			desktop._helpers.event_dispatch('window.minimized', $application);
		},

		toggle_fullscreen: function(link) {
			// Nearest parent window.
			var $window = $(link).closest('div.application_window');

			// Is it maximized already?
			if ($window.hasClass('window_maximized'))
				// Restore window position to previous.
				$window.removeClass('window_maximized').css($window.data('desktop-position'));
			else {
				// Before going fullscreen, store the window position
				desktop._helpers.store_window_position($window);
				$window.addClass('window_maximized').css({top: 0, left: 0, right: 0, bottom: 0, width: '100%', height: '100%'});
			}

			desktop._helpers.activate($window);
			desktop._helpers.on_window_changed($window);
		},

		_bootstraps : {
			frame_breaker: function() {
				if (window.location !== window.top.location) window.top.location = window.location;
			},

			windows: function() {
				$(document)
					// Intercept mouse-down
					.on('mousedown', function(e) {
						var tags = ['a', 'button', 'input', 'select', 'textarea', 'tr'], $target = $(e.target);
						if ($target.hasClass('application'))
							desktop.launch_application($target);
						if (!$target.closest(tags).length) {
							desktop._helpers.clear_active();
							e.preventDefault();
							e.stopPropagation();
						}
					})
					.on('mousedown', 'div.application_window', function () {
						// Bring window to front.
						desktop._helpers.activate($(this));
					})
					.on('click', 'a', function (e){
						var url = $(this).attr('href');
						this.blur();

						if (url.match(/^#/)) {
							e.preventDefault();
							e.stopPropagation();
						}
						else
							$(this).attr('target', '_blank');
					})
					// Prevent context menu
					.on('contextmenu', function () { return false; })
					.on('mouseenter', 'div.application_window', function () {
						$(this)
							.off('mouseenter')
							.draggable({cancel: 'a', handle: 'div.window_titlebar',
								stop: function(e, ui) {
									desktop._helpers.store_window_position(ui.helper);
									desktop._helpers.on_window_changed(ui.helper);
								}
							})
							.resizable({minWidth: desktop._settings.minWidth, minHeight: desktop._settings.minHeight,
								stop: function(e, ui) {
									desktop._helpers.store_window_position(ui.helper);
									desktop._helpers.on_window_changed(ui.element);
								}
							});
					})
					// Double click on the title bar maximizes the window
					.on('dblclick', 'div.window_titlebar', function () { desktop.toggle_fullscreen(this); })
					.on('click', 'a.window_button.minimize', function () { desktop.minimize_application(this); })
					.on('click', 'a.window_button.maximize', function () { desktop.toggle_fullscreen(this); })
					.on('click', 'a.window_button.close', function () { desktop.close_application(this); });
			},

			icons : function() {
				$(document)
					// Make icons draggable
					.on('mouseenter', 'a.application_shortcut', function () {
						$(this)
							.off('mouseenter')
							.draggable({ stop: function(e, ui) {desktop._helpers.on_shortcut_moved(ui.helper); } });
					})
					// Double click icon opens the application
					.on('dblclick', 'a.application_shortcut', function(e) { desktop.launch_application(e.currentTarget); })
			},

			dock : function () {
				$(document)
					.on('click', '.dock a', function () {
						// Get the link's target.
						var $application = $($(this).attr('href'));

						// Hide, if visible.
						if ($application.is(':visible'))
							$application.hide();
						else
							desktop._helpers.activate($application).show();
					})
				;
			},

			menu : function() {

			},

			restore_state: function() {
				// enumerate "started" and boot those
			}
		},

		_helpers: {

			clear_active: function() {
				$('a.active, tr.active').removeClass('active');
				$('ul.desktop-menu').hide();
			},

			activate: function($window) {
				$('div.application_window').removeClass('window_topmost'); $window.addClass('window_topmost'); return $window;
			},

			store_window_position: function($window) {
				var data = {top: 0, left: 0, right: 0, bottom: 0, width: '100%', height: '100%'};
				for (var css in data) data[css] = $window.css(css);
				$window.data('desktop-position', data);
			},

			/**
			 * Handler for application window operations. It is triggered for moves, size changes and maximize toggles
			 * @param $window
			 */
			on_window_changed: function($window) {
				// Fetch whatever position was stored and optionally add the maximized state
				var data = $.extend($window.data('desktop-position'), {maximized: $window.hasClass('window_maximized')});

				// Make sure to update the iframe too, if needed
				desktop._helpers.iframe_maximize($window);
				desktop._helpers.event_dispatch('window.changed', $window, data);
			},

			on_shortcut_moved: function($shortcut) {
				// Todo: shortcut id?
				var data = $.extend({shortcut: $shortcut.prop('id')}, $shortcut.position());
				desktop._helpers.event_dispatch('shortcut.moved', $shortcut, data);
			},

			/**
			 * Retrieve the dock-anchor for the given window
			 * @param $window
			 */
			window_to_dock: function($window) {
				return $($('div.window_titlebar', $window).find('a.window_button.close').attr('href'));
			},

			/**
			 * Fetch the window application identifier linked to the given source
			 * @param $source    Application window, shortcut or dock button
			 * @returns string   The related application ID
			 */
			source_to_application_identifier: function($source) {
				if ($source.hasClass('application_window')) {
					var $dock = desktop._helpers.window_to_dock($source);
					if ($dock.length) return $dock.prop('id');
				}

				if ($source.hasClass('application_shortcut')) {
					var $dock = $($source.attr('href'));
					if ($dock.length) return $dock.prop('id');
				}

				if ($source.hasClass('application_dock_button'))
					return $source.prop('id');

				return null;
			},

			event_dispatch: function(event, $source, data) {
				data = $.extend({}, data);
				data.application = desktop._helpers.source_to_application_identifier($source);
				data.action = event;

				// Notify the backend
				if (desktop._settings.eventUrl)
					$.post(desktop._settings.eventUrl, data);
/*
				var event = options.event, id = desktop._helpers.source_to_identifier(options.target);
				console.log('Event: ', options);

				if (typeof desktop._settings.callbacks[event] == 'undefined')
					return;

				var callbacks = desktop._settings.callbacks[event];
				if (typeof callbacks[id] != 'undefined')
					callbacks[id](options);

				if (typeof callbacks['_'] != 'undefined')
					callbacks['_'](options); */
			},

			iframe_maximize: function($window) {
				var $iframe = $window.find('iframe');
				if ($iframe) {
					var edges = 2 * desktop._settings.windowFrame;
					$iframe.height($window.height() - desktop._settings.titlebarHeight - desktop._settings.footerHeight - edges);
					$iframe.width($window.width() - edges);
				}
			},

			iframe_launched: function($window) {
				// Default behavior is supporting IFrames
				var $iframe = $window.find('iframe');
				if ($iframe && $iframe.data('url')) {
					desktop._helpers.iframe_maximize($window);
					// Update the location via the DOM
					var d = new Date();
					window.frames[$iframe.attr('name')].location = $iframe.data('url') + '?' + d.getTime();
				}
			},
		},

		_settings : {
			clock24h: true,

			titlebarHeight: 30,
			footerHeight: 20,
			windowFrame: 2,

			minHeight: 150,
			minWidth: 350,
			eventUrl: null,
			started: [],
			callbacks: null
		}
	};
})(jQuery, this, this.document);
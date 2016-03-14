/**
 * Ilmenite Visitor Consent
 */
(function ($) {

	$.ilvc = function() {

		var settings = {
			overlayClass : 'ilvc-overlay',
			consentBoxClass : 'ilvc-consent-box',
			jContainerClass : '.ilvc-consent-box',
			jOverlayClass : '.ilvc-overlay',
			opacity : ilvc_settings.opacity,
			title : ilvc_settings.title,
			description : ilvc_settings.description,
			acceptText : ilvc_settings.accept,
			sessionStorageKey : 'ilvcVerified'
		}

		var _this = {
			absCenter : function (element){
				var el = jQuery(element)
				el.css("top", Math.max(0, (($(window).height() - (el.outerHeight() + 150)) / 2) +
				             $(window).scrollTop()) + "px");
				el.css("left", Math.max(0, (($(window).width() - el.outerWidth()) / 2) +
				              $(window).scrollLeft()) + "px");
			},
			buildHTML : function() {

				// Build the HTML
				var html = '';
				html += '<div class="' + settings.overlayClass + '"></div>';
				html += '<div class="' + settings.consentBoxClass + '">';
					html += '<h2 class="' + settings.consentBoxClass + '-title">' + settings.title + '</h2>';
					html += '<p class="' + settings.consentBoxClass + '-description">' + settings.description + '</p>';
					html += '<p class="' + settings.consentBoxClass + '-agree"><button>' + settings.acceptText + '</button></p>';
				html += '</div>';

				// Append the HTML to body
				$('body').append(html);

				// Animate the overlay and consent on page load
				$(settings.jOverlayClass).animate({
					opacity: settings.opacity
				}, 500, function() {
					_this.absCenter(settings.jContainerClass);
					$(settings.jContainerClass).css({
						opacity: 1
					});
				});

				// Set focus to the button
				$(settings.jContainerClass + ' a').focus();

			},
			setSessionStorage : function(key, val){
				try {
					sessionStorage.setItem(key,val);
					return true;
				} catch (e) {
					return false;
				}
			},
			onSuccess : function() {

				// Fade/slide out and remove
				setTimeout(function() {
					$(settings.jContainerClass).animate({'top':'-50rem'}, 500, function() {
						$(settings.jOverlayClass).animate({'opacity': '0'}, 750, function() {
							$(settings.jOverlayClass, settings.jContainerClass).remove();
						});
					});
				}, 100);

			}
		}; // End _this

		// Quit if we have already done this
		if(sessionStorage.getItem(settings.sessionStorageKey) == "true"){
			return false;
		}

		// Build the HTML
		_this.buildHTML();

		// When the user users clicks to accept...
		$(settings.jContainerClass).find('button').on('click', function() {
			if(!_this.setSessionStorage(settings.sessionStorageKey, "true")){
				console.log('Unfortunately we could not store your consent because sessionStorage is not supported by your browser.');
			};
			_this.onSuccess();
		});

		// Absolute Center When We Resize
		$(window).resize(function() {
			_this.absCenter($(settings.jContainerClass));
		});

	}; // End $.ilvc

}(jQuery));

// Run Plugin
jQuery.ilvc();
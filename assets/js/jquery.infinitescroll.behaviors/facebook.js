(function($, undefined) {
	$.extend($.infinitescroll.prototype, {
		_setup_facebook: function infscr_setup_facebook() {
			var opts = this.options, instance = this;

			// Default to 5 scrolls
			opts.maxScrollsBeforeManual = (opts.maxScrollsBeforeManual == undefined) ? 5 : opts.maxScrollsBeforeManual;
			// Create a manual trigger element if its not defined
			opts.msgManualSelector = (opts.msgManualSelector == undefined) ? '<span>Load more posts</span>' : opts.msgManualSelector;
			opts.imgManualSelectorLoading = (opts.imgManualSelector == undefined) ? 'data:image/gif;base64,R0lGODlhEAALAPQAAP///7kyB/Th2vHZ0fjt6ro2DLkyB8VWM9yahdN/ZOvHu8JNKM1tTt6gjdSCaOzKv8NQK7o1Cs5wUvfq5vPf2fv19MhePfTi3Pr08urEt+Syou/Uy/nx7gAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh/hpDcmVhdGVkIHdpdGggYWpheGxvYWQuaW5mbwAh+QQJCwAAACwAAAAAEAALAAAFLSAgjmRpnqSgCuLKAq5AEIM4zDVw03ve27ifDgfkEYe04kDIDC5zrtYKRa2WQgAh+QQJCwAAACwAAAAAEAALAAAFJGBhGAVgnqhpHIeRvsDawqns0qeN5+y967tYLyicBYE7EYkYAgAh+QQJCwAAACwAAAAAEAALAAAFNiAgjothLOOIJAkiGgxjpGKiKMkbz7SN6zIawJcDwIK9W/HISxGBzdHTuBNOmcJVCyoUlk7CEAAh+QQJCwAAACwAAAAAEAALAAAFNSAgjqQIRRFUAo3jNGIkSdHqPI8Tz3V55zuaDacDyIQ+YrBH+hWPzJFzOQQaeavWi7oqnVIhACH5BAkLAAAALAAAAAAQAAsAAAUyICCOZGme1rJY5kRRk7hI0mJSVUXJtF3iOl7tltsBZsNfUegjAY3I5sgFY55KqdX1GgIAIfkECQsAAAAsAAAAABAACwAABTcgII5kaZ4kcV2EqLJipmnZhWGXaOOitm2aXQ4g7P2Ct2ER4AMul00kj5g0Al8tADY2y6C+4FIIACH5BAkLAAAALAAAAAAQAAsAAAUvICCOZGme5ERRk6iy7qpyHCVStA3gNa/7txxwlwv2isSacYUc+l4tADQGQ1mvpBAAIfkECQsAAAAsAAAAABAACwAABS8gII5kaZ7kRFGTqLLuqnIcJVK0DeA1r/u3HHCXC/aKxJpxhRz6Xi0ANAZDWa+kEAA7AAAAAAAAAAAA' : opts.imgManualSelectorLoading;
			opts.manualSelector = (opts.manualSelector == undefined) ? $('<div id="infscr-manualtrigger"><a href="#"><img alt="Loading..." src="' + opts.imgManualSelectorLoading + '"></img>' + opts.msgManualSelector + '</a></div>').insertAfter(opts.navSelector) : opts.manualSelector;
			$(opts.manualSelector).hide().click(function(e) {
				if (opts.state.isDuringAjax) {
					return false;
				}
			
				e.preventDefault();
				$('#infscr-manualtrigger a img').show();
				instance.retrieve();
			});

			this._binding('bind');
			this._numScrolls = 0; // Register a scroll counter

			this.options.loading.finished = function()
			{
				$('#infscr-manualtrigger a img').hide();
			
                if (!opts.state.isBeyondMaxPage) {
                    opts.loading.msg.fadeOut(opts.loading.speed);
				}

				if (++instance._numScrolls > opts.maxScrollsBeforeManual - 1) {
					instance.unbind();
					$(opts.manualSelector).fadeIn(opts.loading.speed);
					// remove the paginator when we are done.
					$(document).ajaxError(function(e,xhr,opt) {
						if (xhr.status == 404) $(opts.nextSelector).remove();
					});
				}
			}

			return false;
		},
        // Show done message
        _showdonemsg_facebook: function infscr_showdonemsg_facebook() {
			var opts = this.options;

            opts.loading.msg
            .find('img')
            .hide()
            .parent()
            .find('div').html(opts.loading.finishedMsg).animate({ opacity: 1 }, 2000, function () {
                $(this).parent().fadeOut(opts.loading.speed);
            });
			
			// Remove a manual trigger element
			$(opts.manualSelector).remove();

            // user provided callback when done
            opts.errorCallback.call($(opts.contentSelector)[0],'done');
        }
	});
})(jQuery);
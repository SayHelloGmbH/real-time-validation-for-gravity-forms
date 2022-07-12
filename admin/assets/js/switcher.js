/* jquery.switcher - 1.2.4
 * Copyright (c) 2014-11-04 Janic Beauchemin - https://github.com/djanix/ */

 ;(function ($, undefined) {
    var pluginName = "switcher";

    var defaults = {
        className: "switcher",
        selected: null,
        language: "en",
        disabled: null,
        style: "default",
        copy: {
            en: {
                yes: 'yes',
                no: 'no'
            },
            fr: {
                yes: 'oui',
                no: 'non'
            }
        }
    };

    function Plugin(element, options) {
        this.input = element;
        this.container = null;
        this.settings = jQuery.extend({}, defaults, jQuery(element).data(), options);
        this._defaults = defaults;
        this._name = pluginName;
        this.init();
    }

    jQuery.extend(Plugin.prototype, {
        init: function () {
            this.buildHtml(this.input, this.settings);
            this.bindEvents(this.container, this.settings);
        },

        buildHtml: function (input, settings) {
            var self = this;
            var $input = jQuery(input);

            $input.wrap('<div class="' + settings.className + ' ' + settings.style + '"></div>');
            $input.after(
                '<div class="content clearfix">' +
                    '<div class="slider"></div>' +
                    '<span class="text textYes"></span>' +
                    '<span class="text textNo"></span>' +
                '</div>'
            );

            this.container = jQuery($input).parent('.' + settings.className)[0];

            self.setLanguage(settings.language);
            self.setValue(settings.selected);
            self.setDisabled(settings.disabled);
        },

        bindEvents: function (container, settings) {
            var self = this;
            var $container = jQuery(container);
            var $input =  $container.find('input');

            $container.on('click', function (e) {
                if (settings.disabled) { return; }

                if ($input.attr('type') == 'radio') {
                    self.setValue(true);
                } else {
                    if ($container.closest('label').length) {
                        self.setValue($input.prop("checked"));
                    } else {
                        self.setValue(!$input.prop("checked"));
                    }
                }
            });

            $container.on('swipeleft', function () {
                if (settings.disabled) { return; }
                self.setValue(false);
            });

            $container.on('swiperight', function () {
                if (settings.disabled) { return; }
                self.setValue(true);
            });
        },

        setValue: function (val) {
            var self = this;
            var $input = jQuery(self.input);
            var $container = jQuery(self.container);

            if (val === null) {
                if ($input.attr('type') == 'radio') {
                    val = typeof $input.filter(':checked').val()!=='undefined';
                } else {
                    val = $input.prop('checked');
                }
            }

            if (typeof val != 'boolean') {
                return console.log('The parameter need to be true or false as a boolean');
            }

            self.settings.selected = val;
            $input.prop("checked", val).trigger('change');

            if ($input.attr('type') == 'radio') {
                var name = $input.attr('name');
                var $inputGroup = jQuery('input[name="' + name + '"]');
                var $containerGroup = $inputGroup.parent('.' + self.settings.className);

                if (val === true) {
                    $containerGroup.removeClass('is-active');
                    $inputGroup.prop("checked", false);

                    $container.addClass('is-active');
                    $input.prop("checked", true);
                }
            } else {
                if (val === true) {
                    $container.addClass('is-active');
                } else {
                    $container.removeClass('is-active');
                }
            }
        },

        setDisabled: function (val) {
            var self = this;
            var $input = jQuery(self.input);
            var $container = jQuery(self.container);

            if (val === null) {
                if ($input.attr('disabled')) {
                    val = true;
                } else {
                    val = false;
                }
            }

            if (typeof val != 'boolean') {
                return console.log('The parameter need to be true or false as a boolean');
            }

            self.settings.disabled = val;

            if (val === true) {
                $container.addClass('is-disabled');
            } else {
                $container.removeClass('is-disabled');
            }
        },

        setLanguage: function (language) {
            var self = this;
            var $container = jQuery(self.container);

            self.settings.language = language;

            $container.find('.textYes').text(self.settings.copy[language].yes);
            $container.find('.textNo').text(self.settings.copy[language].no);
        },

        getLanguage: function (callback) {
            var self = this;
            return callback(self.settings.language);
        },

        importLanguage: function (languageObj) {
            var self = this;
            self.settings.copy = languageObj;
        }
    });

    jQuery .fn[ pluginName ] = function (options) {
        // http://stackoverflow.com/questions/12880256/jquery-plugin-creation-and-public-facing-methods
        var args = Array.prototype.slice.call(arguments, 1);
        this.each(function () {
            var $item = jQuery(this);
            var instance = $item.data("plugin_" + pluginName);

            if (!instance) {
                $item.data("plugin_" + pluginName, new Plugin(this, options));
            } else {
                if(typeof options === 'string') {
                    instance[options].apply(instance, args);
                }
            }
        });

        return this;
    };

//  SWIPE EVENTS
//  -----------------------------------------------------------------------
    var startX = 0;
    var startY = 0;
    var moving = false;
    var threshold = 30;

    function onTouchEnd() {
        this.removeEventListener('touchmove', onTouchMove);
        this.removeEventListener('touchend', onTouchEnd);
        moving = false;
    }

    function onTouchMove(e) {
        e.preventDefault();

        if (moving) {
            var x = e.touches[0].pageX;
            var y = e.touches[0].pageY;
            var dx = startX - x;
            var dy = startY - y;
            var direction = null;

            if(Math.abs(dx) >= threshold) {
                direction = dx > 0 ? 'left' : 'right';
            } else if (Math.abs(dy) >= threshold) {
                direction = dy > 0 ? 'down' : 'up';
            }

            if(direction) {
                onTouchEnd.call(this);
                jQuery(this).trigger('swipe', direction).trigger('swipe' + direction);
            }
        }
    }

    function onTouchStart(e) {
        if (e.touches.length == 1) {
            startX = e.touches[0].pageX;
            startY = e.touches[0].pageY;
            moving = true;
            this.addEventListener('touchmove', onTouchMove, false);
            this.addEventListener('touchend', onTouchEnd, false);
        }
    }

    function setup() {
        this.addEventListener && this.addEventListener('touchstart', onTouchStart, false);
    }

    jQuery.event.special.swipe = { setup: setup };

    jQuery.each(['left', 'up', 'down', 'right'], function () {
        jQuery.event.special['swipe' + this] = { setup: function(){
            jQuery(this).on('swipe', jQuery.noop);
        } };
    });
//  END SWIPE EVENTS
//  -----------------------------------------------------------------------
})($);
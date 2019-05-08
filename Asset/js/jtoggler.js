/*
    Prevzaté z: https://github.com/sinneren/jToggler
    License: MIT License

Copyright (c) 2018 Serj

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

    Kód bol prevzatý a upravený
*/
;( function( $, window, document, undefined ) {

    "use strict";

        var pluginName = "jtoggler",
            defaults = {
                className: "",
            };

        function Toggler ( element, options ) {
            this.element = element;

            this.settings = $.extend( {}, defaults, options );
            this._defaults = defaults;
            this._name = pluginName;

            this.init();
            this.events();
        }

        $.extend( Toggler.prototype, {
            init: function() {
                var $element = $(this.element);

                if ($element.data('jtmulti-state') != null) {
                    this.generateThreeStateHTML();
                } else {
                    this.generateTwoStateHTML();
                }
            },
            events: function() {
                var $element = $(this.element);
                var instance = this;

                $element.on('change', this, function (event) {
                    if ($element.data('jtlabel')) {
                        if ($element.data('jtlabel-success')) {
                            if ($element.prop('checked')) {
                                $element.next().next().text($element.data('jtlabel-success'));
                            } else {
                                $element.next().next().text($element.data('jtlabel'));
                            }
                        } else {
                            instance.setWarningLabelMessage();
                        }
                    }

                    $(document).trigger('jt:toggled', [event.target]);
                });

                if (!$element.prop('disabled')) {
                    var $control = $element.next('.jtoggler-control');
                    $control
                        .find('.jtoggler-radio')
                        .on('click', this, function (event) {
                            $(this)
                                .parents('.jtoggler-control')
                                .find('.jtoggler-btn-wrapper')
                                .removeClass('is-active');

                            $(this)
                                .parent()
                                .addClass('is-active');

                            if ($(event.currentTarget).parent().index() === 2) {
                                $control.addClass('is-fully-active');
                            } else {
                                $control.removeClass('is-fully-active');
                            }

                            $(document).trigger('jt:toggled:multi', [event.target]);
                        });
                }
            },
            generateThreeStateHTML: function() {
                var $element = $(this.element);

                var $wrapper = $('<div />', {
                    class: $.trim("jtoggler-wrapper jtoggler-wrapper-multistate " + this._defaults.className),
                });
                var $control = $('<div />', {
                    class: 'jtoggler-control',
                });
                var $handle = $('<div />', {
                    class: 'jtoggler-handle',
                });
                for (var i = 0; i < 3; i++) {
                    var $label = $('<label />', {
                        class: 'jtoggler-btn-wrapper',
                    });
                    var $btn = $('<input />', {
                        type: 'radio',
                        name: 'options',
                        class: 'jtoggler-radio',
                    });

                    $label.append($btn);
                    $control.prepend($label);
                }
                $control.append($handle);
                $element.wrap($wrapper).after($control);
                if ($element.data('current-value'))
                $control.find('.jtoggler-btn-wrapper:first').addClass('is-active');

            },
            setWarningLabelMessage: function() {
                console.warn('Data attribute "jtlabel-success" is not set');
            },
        } );

        $.fn[ pluginName ] = function( options ) {
            return this.each( function() {
                if ( !$.data( this, "plugin_" + pluginName ) ) {
                    $.data( this, "plugin_" +
                        pluginName, new Toggler( this, options ) );
                }
            } );
        };

} )( jQuery, window, document );
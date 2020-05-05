/*!
 * Validator v0.11.9 for Bootstrap 3, by @1000hz - Hack by Elvis Chen<elvis.chen@tripleonetech.net>
 * Copyright 2017 Cina Saffary
 * Licensed under http://opensource.org/licenses/MIT
 *
 * https://github.com/1000hz/bootstrap-validator
 */

+function ($) {
    'use strict';

    // VALIDATOR CLASS DEFINITION
    // ==========================

    function getValue($el){
        var val = null;
        val = $el.is('[type="checkbox"]') ? $el.prop('checked') : val;
        val = $el.is('[type="radio"]') ? !!$('[name="' + $el.attr('name') + '"]:checked').length : val;
        val = $el.is('select[multiple]') ? ($el.val() || []).length : val;
        val = $el.val();
        return val;
    }

    function getRemoteData($el){
        var data = {};
        var callback = $el.data('remote-data');
        if(typeof callback === "function"){
            data = callback();
        }else{
            data[$el.attr('name')] = getValue($el);
        }
        return data;
    }

    var Validator = function(element, options){
        var self = this;
        this.options = options;
        this.validators = $.extend({}, Validator.VALIDATORS, options.custom);
        this.$element = $(element);
        this.$btn = $('button[type="submit"], input[type="submit"]')
            .filter('[form="' + this.$element.attr('id') + '"]')
            .add(this.$element.find('input[type="submit"], button[type="submit"]'));

        this.update();

        this.$element.on('input.bs.validator change.bs.validator focusout.bs.validator', $.proxy(this.onInput, this));
        this.$element.on('focusin.bs.validator', $.proxy(this.onFocus, this));
        this.$element.on('submit.bs.validator', $.proxy(this.onSubmit, this));
        this.$element.on('reset.bs.validator', $.proxy(this.reset, this));

        this.$element.find('[data-match]').each(function(){
            var $this = $(this);
            var target = $this.attr('data-match');

            $(target).on('input.bs.validator', function(e){
                getValue($this) && $this.trigger('input.bs.validator')
            })
        });

        // run validators for fields with values, but don't clobber server-side errors
        this.$inputs.filter(function(){
            return getValue($(this)) && !$(this).closest('.has-error').length
        }).trigger('focusout');

        this.$element.attr('novalidate', true) // disable automatic native validation
    };

    Validator.VERSION = '0.11.9';

    Validator.INPUT_SELECTOR = ':input:not([type="hidden"], [type="submit"], [type="reset"], button)';

    Validator.FOCUS_OFFSET = 20;

    Validator.DEFAULTS = {
        delay: 500,
        html: false,
        disable: true,
        focus: true,
        focus_offset: false,
        custom: {},
        errors: {
            match: 'Does not match',
            minlength: 'Not long enough',
            maxlength: 'Not long enough',
            regex: 'wrong format',
            required: 'This is a required field',
            remote: 'Something\'s wrong.'
        },
        feedback: {
            success: 'glyphicon-ok',
            error: 'glyphicon-remove'
        }
    };

    Validator.VALIDATORS = {
        'native': function($el){
            var el = $el[0];
            if(el.checkValidity){
                return !el.checkValidity() && !el.validity.valid && (el.validationMessage || "error!");
            }
        },
        'match': function($el){
            var target = $el.attr('data-match');
            return $el.val() !== $(target).val() && Validator.DEFAULTS.errors.match;
        },
        'minlength': function($el){
            var minlength = $el.attr('data-minlength');
            return $el.val().length < minlength && Validator.DEFAULTS.errors.minlength;
        },
        'maxlength': function($el){
            var maxlength = $el.attr('data-maxlength');
            return $el.val().length > maxlength && Validator.DEFAULTS.errors.maxlength;
        },
        'regex': function($el){
            var regex_rule = $el.attr('data-regex');
            var re;
            switch(regex_rule){
                case 'email':
                    re = new RegExp(/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/);
                    break;
                case 'chinese_only':
                    re = new RegExp(/^[u4e00-u9fa5]+$/gi);
                    return (re.test($el.val())) && Validator.DEFAULTS.errors.regex;
                    break;
                default:
                    re = new RegExp(regex_rule, "g");
            }
            return (!re.test($el.val())) && Validator.DEFAULTS.errors.regex;
        }
    };

    Validator.prototype.update = function(){
        var self = this;

        this.$inputs = this.$element.find(Validator.INPUT_SELECTOR)
            .add(this.$element.find('[data-validate="true"]'))
            .not(this.$element.find('[data-validate="false"]')
                .each(function(){
                    self.clearErrors($(this))
                })
            );

        this.toggleSubmit();

        return this
    };

    Validator.prototype.onInput = function(e){
        var self = this;
        var $el = $(e.target);
        var deferErrors = e.type !== 'focusout';

        if(!this.$inputs.is($el)) return;

        if(e.type === 'focusout'){
            var $group = $el.closest('.form-group');
            $group.removeClass('validator-focus');
        }

        this.validateInput($el, deferErrors, true).done(function(){
            self.toggleSubmit()
        })
    };

    Validator.prototype.onFocus = function(e){
        var self = this;
        var $el = $(e.target);
        var deferErrors = e.type !== 'focusout';

        if(!this.$inputs.is($el)) return;

        var $group = $el.closest('.form-group');
        $group.addClass('validator-focus');

        this.validateInput($el, false, false).done(function(errors, error_types){
        });
    };

    Validator.prototype.validateInput = function($el, deferErrors, isErrors){
        var value = getValue($el);
        var prevErrors = $el.data('bs.validator.errors');

        if($el.is('[type="radio"]')) $el = this.$element.find('input[name="' + $el.attr('name') + '"]');

        var e = $.Event('validate.bs.validator', {relatedTarget: $el[0]});
        this.$element.trigger(e);
        if(e.isDefaultPrevented()) return;

        var self = this;

        return this.runValidators($el).done(function(errors, error_types){
            $el.data('bs.validator.errors', errors);
            $el.data('bs.validator.error_types', error_types);

            if(errors.length){
                if(!!isErrors){
                    if(deferErrors){
                        self.defer($el, self.showErrors);
                    }else{
                        self.showErrors($el);
                    }
                }
            }else{
                self.clearErrors($el);
            }
            self.showInputTip($el);

            if(!prevErrors || errors.toString() !== prevErrors.toString()){
                if(errors.length){
                    e = $.Event('invalid.bs.validator', {relatedTarget: $el[0], detail: errors});
                }else{
                    e = $.Event('valid.bs.validator', {relatedTarget: $el[0], detail: prevErrors});
                }

                self.$element.trigger(e)
            }

            self.toggleSubmit();

            self.$element.trigger($.Event('validated.bs.validator', {relatedTarget: $el[0]}))
        });
    };


    Validator.prototype.runValidators = function($el){
        var self = this;
        var errors = [];
        var error_types = [];
        var deferred = $.Deferred();

        $el.data('bs.validator.deferred') && $el.data('bs.validator.deferred').reject();
        $el.data('bs.validator.deferred', deferred);

        function getValidatorSpecificError(key){
            return $el.attr('data-' + key + '-error')
        }

        function getValidityStateError(){
            var validity = $el[0].validity;
            var error = null;
            switch(true){
                case validity.badInput:
                    error = $el.attr('data-type-error');
                    error_types.push('type');
                    break;
                case validity.customError:
                    error = $el.attr('data-custom-error');
                    error_types.push('custom');
                    break;
                case validity.patternMismatch:
                    error = $el.attr('data-pattern-error');
                    error_types.push('pattern');
                    break;
                case validity.rangeOverflow:
                    error = $el.attr('data-max-error');
                    error_types.push('max');
                    break;
                case validity.rangeUnderflow:
                    error = $el.attr('data-min-error');
                    error_types.push('min');
                    break;
                case validity.stepMismatch:
                    error = $el.attr('data-step-error');
                    error_types.push('step');
                    break;
                case validity.tooLong:
                    error = $el.attr('data-maxlength-error');
                    error_types.push('maxlength');
                    break;
                case validity.tooShort:
                    error = $el.attr('data-minlength-error');
                    error_types.push('minlength');
                    break;
                case validity.typeMismatch:
                    error = $el.attr('data-type-error');
                    error_types.push('type');
                    break;
                case validity.valid:
                    error = $el.attr('data-valid-error');
                    error_types.push('unknown');
                    break;
                case validity.valueMissing:
                    error = ($el.attr('data-required-error') || getDefaultError('required'));
                    error_types.push('required');
                    break;
            }
            return error;
        }

        function getGenericError(){
            return $el.attr('data-error')
        }

        function getDefaultError(key){
            return (self.options.errors.hasOwnProperty(key)) ? self.options.errors[key] : null;
        }

        function getErrorMessage(key){
            return getValidatorSpecificError(key) || getValidityStateError() || getGenericError() || getDefaultError(key);
        }

        if(!$el.is(':disabled')){
            $.each(this.validators, $.proxy(function(key, validator){
                var error = null;
                if((getValue($el) || $el.attr('required')) && ($el.attr('data-' + key) !== undefined || key == 'native') && (error = validator.call(this, $el))){
                    error = getErrorMessage(key) || error;
                    if(!~errors.indexOf(error)){
                        errors.push(error);
                        if(key !== "native") error_types.push(key);
                    }
                }
            }, this));

            if(!errors.length && getValue($el) && $el.attr('data-remote')){
                this.defer($el, function(){
                    var data = getRemoteData($el);
                    $.getJSON($el.attr('data-remote'), data)
                        .fail(function(jqXHR, textStatus, error){
                            errors.push(getErrorMessage('remote') || error);
                            error_types.push('remote');
                            deferred.resolve(errors, error_types)
                        })
                        .done(function(response){
                            var e = $.Event('validated.bs.remote.' + $el.attr('name'), {relatedTarget: $el[0]});
                            self.$element.trigger(e, [data, response, errors, deferred]);
                            var error = e.result;
                            if(error && !~errors.indexOf(error)){
                                errors.push(error);
                                error_types.push('remote');
                            }

                            if(!e.isDefaultPrevented()){
                                deferred.resolve(errors, error_types)
                            }
                        })
                });
            }else deferred.resolve(errors, error_types);
        }else{
            deferred.resolve(errors, error_types);
        }

        return deferred.promise();
    };

    Validator.prototype.validate = function(callback){
        var self = this;

        $.when(this.$inputs.map(function(el){
            return self.validateInput($(this), false);
        })).then(function(){
            self.toggleSubmit();
            self.focusError();

            var e = $.Event('validate.bs.validator', {relatedTarget: self.$element, validator: self});
            self.$element.trigger(e, self);

            if(typeof callback === "function"){
                callback(e, self);
            }
        });

        return this
    };

    Validator.prototype.focusError = function(){
        if(!this.options.focus) return;

        var $input = this.$element.find(".has-error:first :input");
        if($input.length === 0){
            return;
        }

        var $label = this.$element.find(".has-error:first .control-label");

        var focus_offset = (typeof this.options.focus_offset === "boolean" && !this.options.focus_offset) ? Validator.FOCUS_OFFSET : parseInt(this.options.focus_offset);
        focus_offset = isNaN(focus_offset) ? 0 : focus_offset;

        if(!$input.is(":visible") && $label.length > 0){
            if(focus_offset){
                $('html, body').animate({scrollTop: $label.offset().top - focus_offset}, 250);
            }
            $label.focus();
        }else{
            if(focus_offset){
                $('html, body').animate({scrollTop: $input.offset().top - focus_offset}, 250);
            }
            $input.focus();
        }
    };

    Validator.prototype.showErrors = function ($el) {
        var method = this.options.html ? 'html' : 'text';
        var errors = $el.data('bs.validator.errors');
        var $group = $el.closest('.form-group');
        var $block = $group.find('.help-block.with-errors');
        var $feedback = $group.find('.form-control-feedback');

        if (!errors.length) return;

        errors = $('<ul/>').addClass('list-unstyled').append($.map(errors, function (error) {
            return $('<li/>')[method](error);
        }));

        $block.data('bs.validator.originalContent') === undefined && $block.data('bs.validator.originalContent', $block.html());
        $block.empty().append(errors);
        
        $group.addClass('has-error has-danger');

        $group.hasClass('has-feedback')
                && $feedback.removeClass(this.options.feedback.success)
                && $feedback.addClass(this.options.feedback.error)
                && $group.removeClass('has-success');
    };

    Validator.prototype.showInputTip = function($el){
        var $group = $el.closest('.form-group');

        var help_block = $group.find('.help-block.with-input-tip');

        if($group.hasClass('has-input-tip') && help_block.length){
            var error_types = $el.data('bs.validator.error_types');

            help_block.find('.invalid').removeClass('invalid');

            if(!!error_types){
                $.each(error_types, function(idx, error_type){
                    help_block.find('.with_' + error_type).addClass('invalid');
                });
            }
        }
    };

    Validator.prototype.clearErrors = function ($el) {
        var $group = $el.closest('.form-group');
        var $block = $group.find('.help-block.with-errors');
        var $feedback = $group.find('.form-control-feedback');

        $block.html($block.data('bs.validator.originalContent'));
        $group.removeClass('has-error has-danger has-success');

        $group.hasClass('has-feedback')
                && $feedback.removeClass(this.options.feedback.error)
                && $feedback.removeClass(this.options.feedback.success)
                && getValue($el)
                && $feedback.addClass(this.options.feedback.success)
                && $group.addClass('has-success');
    };

    Validator.prototype.hasErrors = function(){
        function fieldErrors(){
            return !!($(this).data('bs.validator.errors') || []).length
        }

        return !!this.$inputs.filter(fieldErrors).length
    };

    Validator.prototype.isIncomplete = function(){
        function fieldIncomplete(){
            var value = getValue($(this));
            return !(typeof value == "string" ? $.trim(value) : value);
        }

        return !!this.$inputs.filter('[required]').filter(fieldIncomplete).length;
    };

    Validator.prototype.onSubmit = function(e){
        this.validate();
        if(this.isIncomplete() || this.hasErrors()) e.preventDefault();
    };

    Validator.prototype.toggleSubmit = function(){
        var e = $.Event('done.bs.validator', {relatedTarget: this.$element, validator: this});
        this.$element.trigger(e, this);

        if(!this.options.disable) return;
        this.$btn.toggleClass('disabled', this.isIncomplete() || this.hasErrors())
    };

    Validator.prototype.defer = function($el, callback){
        callback = $.proxy(callback, this, $el);
        if(!this.options.delay) return callback();
        window.clearTimeout($el.data('bs.validator.timeout'));
        $el.data('bs.validator.timeout', window.setTimeout(callback, this.options.delay));
    };

    Validator.prototype.reset = function(){
        this.$element.find('.form-control-feedback')
            .removeClass(this.options.feedback.error)
            .removeClass(this.options.feedback.success);

        this.$inputs
            .removeData(['bs.validator.errors', 'bs.validator.error_types', 'bs.validator.deferred'])
            .each(function(){
                var $this = $(this);
                var timeout = $this.data('bs.validator.timeout');
                window.clearTimeout(timeout) && $this.removeData('bs.validator.timeout')
            });

        this.$element.find('.help-block.with-errors')
            .each(function(){
                var $this = $(this);
                var originalContent = $this.data('bs.validator.originalContent');

                $this
                    .removeData('bs.validator.originalContent')
                    .html(originalContent)
            });

        this.$btn.removeClass('disabled');

        this.$element.find('.has-error, .has-danger, .has-success').removeClass('has-error has-danger has-success');

        return this;
    };

    Validator.prototype.destroy = function(){
        this.reset();

        this.$element
            .removeAttr('novalidate')
            .removeData('bs.validator')
            .off('.bs.validator');

        this.$inputs
            .off('.bs.validator');

        this.options = null;
        this.validators = null;
        this.$element = null;
        this.$btn = null;
        this.$inputs = null;

        return this
    };

  // VALIDATOR PLUGIN DEFINITION
  // ===========================
    function Plugin(option){
        var args = Array.prototype.slice.call(arguments, 1);
        return this.each(function(){
            var $this = $(this);
            var options = $.extend({}, Validator.DEFAULTS, $this.data(), typeof option == 'object' && option);
            var data = $this.data('bs.validator');

            if(!data && option == 'destroy') return;
            if(!data) $this.data('bs.validator', (data = new Validator(this, options)));
            if(typeof option == 'string'){
                data[option].apply(data, args);
            }
        });
    }

    var old = $.fn.validator;

    $.fn.validator = Plugin;
    $.fn.validator.Constructor = Validator;

    // VALIDATOR NO CONFLICT
    // =====================

    $.fn.validator.noConflict = function(){
        $.fn.validator = old;
        return this;
    };

    // VALIDATOR DATA-API
    // ==================

    $(window).on('load', function(){
        $('form[data-toggle="validator"]').each(function(){
            var $form = $(this);
            Plugin.call($form, $form.data());
        });
    });
}(jQuery);

(function(){
    var template = '<div class="t1t-ui modal _player_message_reply_modal" tabindex="-1" role="dialog">\n' +
        '    <div class="modal-dialog" role="document">\n' +
        '        <div class="modal-content">\n' +
        '            <div class="modal-header">\n' +
        '              <h5 class="modal-title"></h5>\n' +
        '              <button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>\n' +
        '            </div>\n' +
        '            <div class="modal-body">\n' +
        '            </div>\n' +
        '            <div class="modal-footer">\n' +
        '              <button type="button" class="btn btn-close" aria-label="Close">Close</button>\n' +
        '              <button type="button" class="btn btn-primary btn-reply" aria-label="Submit">Submit</button>\n' +
        '            </div>\n' +
        '        </div>\n' +
        '    </div>\n' +
        '</div>';

    var request_form_template = '<div class="t1t-ui modal _player_message_request_form_modal" tabindex="-1" data-backdrop="static" data-keyboard="false" role="dialog">\n' +
        '    <div class="modal-dialog" role="document">\n' +
        '        <div class="modal-content">\n' +
        '            <div class="modal-header">\n' +
        '              <h5 class="modal-title"></h5>\n' +
        '              <button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>\n' +
        '            </div>\n' +
        '            <div class="modal-body">\n' +
        '                <form enctype="multipart/form-data" action="javascript: void(0);" class="form-inline" method="post" autocomplete="off">\n' +
        '                </form>\n' +
        '            </div>\n' +
        '            <div class="modal-footer">\n' +
        '              <button type="button" class="btn btn-close" aria-label="Close">Close</button>\n' +
        '              <button type="button" class="btn btn-primary btn-submit" aria-label="Submit">Submit</button>\n' +
        '            </div>\n' +
        '        </div>\n' +
        '    </div>\n' +
        '</div>';

    var request_form_floating_button = '<div class="t1t-widget widget-request-form">\n' +
        '    <div class="request-form-floating-button-sets">\n' +
        '        <button class="btn btn-info _show_player_message_request_form"><span class="glyphicon glyphicon-comment"></span></button>' +
        '    </div>' +
        '</div>';

    function T1T_PlayerMessage(){
        this.name = 'player_message';

        this.messages = {};

        this.autoRefreshTimer = null;
        this.initialized_request_form = false;
    }

    T1T_PlayerMessage.prototype.updateMessages = function(callback) {
        var self = this;

        utils.getJSONP(variables.message.refreshInternalMessageUrl, null, function(result) {
            if (result.status === "success") {
                var data = result.data;

                $('._player_internal_message_count').text(data.chatUnread);

                if(typeof callback === "function") callback();
            }
        }, null);

        return self;
    };

    T1T_PlayerMessage.prototype.refreshInternalMessage = function() {
        var self = this;

        if (!variables.message.enabled_refresh_message_on_player || !variables.logged) {
            return;
        }

        if(!utils.isInActiveWindow()){
            utils.safelog('ignore get_unread_messages on hidden window');
            return;
        }

        if(!!this.autoRefreshTimer){
            clearTimeout(this.autoRefreshTimer);
        }

        self.updateMessages(function(){
            self.autoRefreshTimer = setTimeout(function() {
                self.refreshInternalMessage();
            }, variables.message.refreshInternalMessageTimeInterval);
        });

        return this;
    };

    T1T_PlayerMessage.prototype.stopAutoRefresh = function(){
        if(!!this.autoRefreshTimer){
            clearTimeout(this.autoRefreshTimer);
        }

        return this;
    };

    T1T_PlayerMessage.prototype.loadMessage = function(message_id, callback) {
        var self = this;

        utils.getJSONP(variables.message.loadMessageUrl + '/' + message_id, null, function(result) {
            if (result.status === "success") {
                var data = result.data;

                if(typeof callback === "function") callback(data);
            }else{
                if(typeof callback === "function") callback(false, result.message);
            }
        }, null);

        return self;
    };

    T1T_PlayerMessage.prototype.reply = function(message_id, message, callback){
        utils.getJSONP(variables.message.replyMessageUrl + '/' + message_id, {
            "message": message
        }, function(data) {
            if (data['status'] === 'success') {
                if(typeof callback === "function") callback(true, data['message']);
            }else{
                if(typeof callback === "function") callback(false, data['message']);
            }
        }, function(){
            if(typeof callback === "function") callback(false, 'Something\'s wrong');
        });
    };

    T1T_PlayerMessage.prototype.render = function(message, on_show, on_hide){
        var self = this;

        var topic = message['topic'];
        var message_id = topic['messageId'];

        var modal = $(template);
        $('.modal-title', modal).append($('<span class="message-subject">').html(topic['subject']));

        $('.modal-footer button.btn-reply', modal).html(lang('cs.reply'));
        $('.modal-footer button.btn-close', modal).html(lang('close_button_text'));

        $('button.close, button.btn-close', modal).on('click', function(){
            modal.modal('hide');
        });

        $('button.btn-submit', modal).on('click', function(){
            var message = $('textarea', modal).val();

            if(message.length <= 0){
                return false;
            }

            $(this).prop('disabled', true);

            self.reply(message_id, message, function(result, message){
                if(result){
                    MessageBox.info(message);
                }else{
                    MessageBox.danger(message);
                }
                modal.modal('hide');
            });
        });

        $('.modal-body', modal).append(this.generatorContent(message_id, message));
        if(!message['flags']['is_disabled_reply']){
            $('.modal-body', modal).append(this.generatorReply(message_id, message));
        }else{
            $('.modal-footer button.btn-reply', modal).hide();
            $(modal).addClass('is_disabled_reply').hide();
        }

        $('.modal-footer button.btn-reply', modal).on('click', function(){
            Loader.show();
            self.reply(message_id, $('.modal-body .message-reply-container textarea', modal).val(), function(result, message){
                Loader.hide();

                if(result){
                    MessageBox.success(message);

                    if(self.messages.hasOwnProperty(message_id)){
                        delete self.messages[message_id];
                    }
                }else{
                    MessageBox.danger(message);
                }

                modal.modal('hide');
            });
        });

        modal.on('show.t1t.ui.modal', function(){
            if(typeof on_show === "function") on_show();
        });

        modal.on('shown.t1t.ui.modal', function(){
            $('.message-list', modal).scrollTop($('.message-list', modal)[0].scrollHeight);
        });

        modal.on('hidden.t1t.ui.modal', function(){
            modal.remove();

            if(typeof on_hide === "function") on_hide();
        });

        modal.modal('show');
    };

    T1T_PlayerMessage.prototype.generatorContent = function(message_id, message){
        var container = $('<div class="message-list">');
        container.attr('data-message-id', message_id);

        $.each(message['messages'], function(key, message_content){
            var entry = $('<div class="message-entry">');
            entry.attr('data-message-detail-id', message_content['messageDetailsId']);

            var author = $('<div class="message-author">').appendTo(entry);
            if(message_content['flag'] === 'player'){
                entry.addClass('message-author-player');
                author.html(message_content['sender']);
            }else{
                entry.addClass('message-author-admin');
                author.html(message_content['sender']);
            }

            var datetime = $('<div class="message-datetime">').appendTo(entry);
            datetime.html(message_content['date']);

            var detail = $('<div class="message-detail">').appendTo(entry);
            detail.html(message_content['detail']);

            entry.appendTo(container);
        });

        return container;
    };

    T1T_PlayerMessage.prototype.generatorReply = function(){
        var container = $('<div class="message-reply-container">');

        var textarea = $('<textarea>').appendTo(container);

        return container;
    };

    T1T_PlayerMessage.prototype.show = function(message_id, on_show, on_hide){
        var self = this;
        message_id = (!!message_id) ? message_id : this.message_ids.shift();

        if(this.messages.hasOwnProperty(message_id)){
            self.render(this.messages[message_id], on_show, on_hide);
        }else{
            Loader.show();
            self.loadMessage(message_id, function(data, error_message){
                Loader.hide();
                if(!!data){
                    self.messages[message_id] = data;
                    self.render(data, on_show, on_hide);
                }else{
                    MessageBox.danger(error_message);
                }
            });
        }
    };

    T1T_PlayerMessage.prototype.initRequestForm = function(){
        var self = this;

        if(self.initialized_request_form){
            return this;
        }

        self.initialized_request_form = true;

        if(variables.message.request_form_settings.enable_floating_button){
            $('body').append(request_form_floating_button);

            $('.t1t-widget.widget-request-form button._show_player_message_request_form').on('click', function(){
                var $self = $(this);

                $self.hide();
                self.showRequestForm(function(){
                    $self.show();
                });
            });
        }
    };

    T1T_PlayerMessage.prototype.destroyRequestForm = function(){
        $('.widget-request-form').remove();
    };

    T1T_PlayerMessage.prototype.showRequestForm = function(callback){
        var modal = $(request_form_template);

        var form = $('form', modal);

        $('.modal-footer button.btn-close', modal).html(lang('close_button_text'));
        $('.modal-footer button.btn-submit', modal).html(lang('submit_button_text'));

        $('.modal-header .modal-title', modal).html(lang(variables.message.request_form_settings.window_title));

        if(variables.message.request_form_settings.real_name_enable){
            (function(){
                var field_name = lang('player.first_name');
                var form_group = $('<div class="form-group has-feedback rf-real-name-container"></div>');
                var help_block = $('<p class="help-block"></p>');
                var label = $('<label for="rf-real-name">' + field_name + '</label>');
                var required = !!variables.message.request_form_settings.real_name_required;
                var rule = (variables.message.request_form_settings.request_form_rules.hasOwnProperty('first_name')) ? variables.message.request_form_settings.request_form_rules.first_name : null;

                var real_name_input = $('<input type="text" name="rf-real_name" id="rf-real_name">');

                label.appendTo(form_group);
                real_name_input.appendTo(form_group);
                help_block.appendTo(form_group);

                if(required){
                    form_group.addClass('required');
                    real_name_input.attr('required', 'required');
                    $('<span class="label-required">*</span>').appendTo(label);
                }

                form_group.appendTo($('form', modal));
                $('<div class="clearfix"></div>').appendTo($('form', modal));

                real_name_input.on('validator.t1t.rf', function(e){
                    form_group.removeClass('has-error').removeClass('has-success');
                    help_block.empty();
                    var ul = $('<ul></ul>');

                    var value = $(this).val();
                    value = (!!value) ? value : "";
                    var validator = true;

                    if(required && value.length <= 0){
                        validator = false;
                    }
                    if((required || value.length > 0) && !!rule){
                        if(rule.hasOwnProperty('min') && (value.length < rule.min)){
                            validator = false;
                            $('<li></li>').html(lang('form.validation.invalid_minlength', field_name, rule.min)).appendTo(ul);
                        }
                        if(rule.hasOwnProperty('max') && (value.length > rule.max)){
                            validator = false;
                            $('<li></li>').html(lang('form.validation.invalid_maxlength', field_name, rule.max)).appendTo(ul);
                        }
                    }

                    ul.appendTo(help_block);
                    form_group.addClass((validator) ? 'has-success' : 'has-error');

                    e.result = {
                        "validator": validator
                    };
                });
            })();
        }

        if(variables.message.request_form_settings.username_enable){
            (function(){
                var field_name = lang('player.username');
                var form_group = $('<div class="form-group has-feedback rf-username-container"></div>');
                var help_block = $('<p class="help-block"></p>');
                var label = $('<label for="rf-username">' + field_name + '</label>');
                var required = !!variables.message.request_form_settings.username_required;
                var rule = (variables.message.request_form_settings.request_form_rules.hasOwnProperty('username')) ? variables.message.request_form_settings.request_form_rules.username : null;

                var username_input = $('<input type="text" name="rf-username" id="rf-username">');

                label.appendTo(form_group);
                username_input.appendTo(form_group);
                help_block.appendTo(form_group);

                if(required){
                    form_group.addClass('required');
                    username_input.attr('required', 'required');
                    $('<span class="label-required">*</span>').appendTo(label);
                }

                form_group.appendTo($('form', modal));
                $('<div class="clearfix"></div>').appendTo($('form', modal));

                username_input.on('validator.t1t.rf', function(e){
                    form_group.removeClass('has-error').removeClass('has-success');
                    help_block.empty();
                    var ul = $('<ul></ul>');

                    var value = $(this).val();
                    value = (!!value) ? value : "";
                    var validator = true;
                    var messages = [];

                    if(required && value.length <= 0){
                        validator = false;
                    }
                    if((required || value.length > 0) && !!rule){
                        if(rule.hasOwnProperty('min') && (value.length < rule.min)){
                            validator = false;
                            $('<li></li>').html(lang('form.validation.invalid_minlength', field_name, rule.min)).appendTo(ul);
                        }
                        if(rule.hasOwnProperty('max') && (value.length > rule.max)){
                            validator = false;
                            $('<li></li>').html(lang('form.validation.invalid_maxlength', field_name, rule.max)).appendTo(ul);
                        }

                        var regex = (rule.hasOwnProperty('regex') && !!rule.regex) ? new RegExp(rule.regex, "i") : null;
                        if(!!regex && !regex.test(value)){
                            validator = false;
                            $('<li></li>').html(lang('form.validation.invalid_regex', field_name)).appendTo(ul);
                        }
                    }

                    ul.appendTo(help_block);
                    form_group.addClass((validator) ? 'has-success' : 'has-error');

                    e.result = {
                        "validator": validator
                    };
                });
            })();
        }

        if(variables.message.request_form_settings.contact_method_enable){
            (function(){
                var field_name = null;
                var label_text = null;
                var rule = null;
                var regex = null;
                switch(variables.message.request_form_settings.contact_method){
                    case 'email':
                        field_name = lang('player.email_address');
                        regex = /^\w+([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})$/;
                        break;
                    case 'mobile_phone':
                    default:
                        field_name = lang('player.contact_number');
                        rule = (variables.message.request_form_settings.request_form_rules.hasOwnProperty('contact_number')) ? variables.message.request_form_settings.request_form_rules.contact_number : null;
                        regex = (rule.hasOwnProperty('regex') && !!rule.regex) ? new RegExp(rule.regex, "i") : /^[0-9]+$/;
                        break;
                }

                var form_group = $('<div class="form-group has-feedback rf-contact_method-container"></div>');
                var help_block = $('<p class="help-block"></p>');
                var label = $('<label for="rf-contact_method">' + field_name + '</label>');
                var required = !!variables.message.request_form_settings.contact_method_required;

                var contact_method_input = $('<input type="text" name="rf-contact_method" id="rf-contact_method">');

                label.appendTo(form_group);
                contact_method_input.appendTo(form_group);
                help_block.appendTo(form_group);

                if(required){
                    form_group.addClass('required');
                    contact_method_input.attr('required', 'required');
                    $('<span class="label-required">*</span>').appendTo(label);
                }

                form_group.appendTo($('form', modal));
                $('<div class="clearfix"></div>').appendTo($('form', modal));

                contact_method_input.on('validator.t1t.rf', function(e){
                    form_group.removeClass('has-error').removeClass('has-success');
                    help_block.empty();
                    var ul = $('<ul></ul>');

                    var value = $(this).val();
                    value = (!!value) ? value : "";
                    var validator = true;
                    var messages = [];

                    if(required && value.length <= 0){
                        validator = false;
                    }

                    if((required || value.length > 0) && !!rule){
                        if(rule.hasOwnProperty('min') && (value.length < rule.min)){
                            validator = false;
                            $('<li></li>').html(lang('form.validation.invalid_minlength', field_name, rule.min)).appendTo(ul);
                        }
                        if(rule.hasOwnProperty('max') && (value.length > rule.max)){
                            validator = false;
                            messages.push(lang('form.validation.invalid_maxlength', field_name, rule.max));
                            $('<li></li>').html(lang('form.validation.invalid_maxlength', field_name, rule.max)).appendTo(ul);
                        }
                    }

                    if((required || value.length > 0) && !!regex && !regex.test(value)){
                        validator = false;
                        $('<li></li>').html(lang('form.validation.invalid_regex', field_name)).appendTo(ul);
                    }

                    ul.appendTo(help_block);
                    form_group.addClass((validator) ? 'has-success' : 'has-error');

                    e.result = {
                        "validator": validator
                    };
                });
            })();
        }

        if(!!variables.message.request_form_settings.footer_notice){
            $('<p>').html(variables.message.request_form_settings.footer_notice).appendTo($('form', modal));
        }

        $('button.close, button.btn-close', modal).on('click', function(){
            modal.modal('hide');
        });

        var elements = $('input, select, textarea', form);
        var validator_result = true;
        var validator_cb = function(){
            var e = $.Event('validator.t1t.rf');
            $(this).trigger(e);
            if(e.hasOwnProperty('result') && !!e.result && e.result.hasOwnProperty('validator')){
                if(e.result.validator){

                }else{
                    validator_result = false;
                }
            }
        };

        elements.on('keyup', validator_cb);

        modal.on('hidden.t1t.ui.modal', function(){
            modal.remove();

            if(typeof callback == 'function') callback();
        });

        form.on('submit', function(){
            $('.btn-submit', modal).addClass('disabled').prop('disabled', true).attr('disabled', 'disabled');

            validator_result = true;

            elements.each(validator_cb);

            $('.has-error:first', form).focus();

            if(!validator_result){
                $('.btn-submit', modal).removeClass('disabled').prop('disabled', false).removeAttr('disabled');
                return false;
            }

            var post_name_maps = {
                "rf-real_name": "real_name",
                "rf-username": "username",
                "rf-contact_method": "contact_method"
            };
            var data = form.serializeArray().reduce(function(obj, item) {
                if(post_name_maps.hasOwnProperty(item.name)){
                    obj[post_name_maps[item.name]] = item.value;
                }else{
                    obj[item.name] = item.value;
                }
                return obj;
            }, {});

            Loader.show();

            var callback = function(jsonData){
                Loader.hide();
                modal.modal('hide');
                $('.btn-submit', modal).removeClass('disabled').prop('disabled', false).removeAttr('disabled');

                if(jsonData.status === "success"){
                    MessageBox.success(jsonData.message);
                }else{
                    MessageBox.danger(jsonData.message);
                }
            };
            utils.postJSONWithIframe(variables.message.request_form_settings.request_form_url, data, callback, function(){
                Loader.hide();
                modal.modal('hide');
                $('.btn-submit', modal).removeClass('disabled').prop('disabled', false).removeAttr('disabled');

                MessageBox.danger('Something\'s went wrong, please contact administrator.');
            });

            return false;
        });

        $('.btn-submit', modal).on('click', function(){
            form.submit();
        });

        modal.modal('show');
    };

    var player_message = new T1T_PlayerMessage();
    smartbackend.addAddons(player_message.name, player_message);

    smartbackend.on('not_login.t1t.player', function(){
        if(variables.message.hasOwnProperty('request_form_settings') && variables.message.request_form_settings.enable_for_guest){
            player_message.initRequestForm();
        }else{
            player_message.destroyRequestForm();
        }
    });

    smartbackend.on('logged.t1t.player', function(){
        utils.safelog('actived, refresh internal message');
        player_message.refreshInternalMessage();

        if(variables.message.hasOwnProperty('request_form_settings') && variables.message.request_form_settings.enable_for_player){
            player_message.initRequestForm();
        }else{
            player_message.destroyRequestForm();
        }
    });

    return player_message;
})();
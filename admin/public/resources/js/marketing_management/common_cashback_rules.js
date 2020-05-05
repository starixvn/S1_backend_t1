(function(){
    var COMMON_CASHBACK_MULTIPLE_RANGE_TYPE_GAME_PLATFORM = 'game_platform';
    var COMMON_CASHBACK_MULTIPLE_RANGE_TYPE_GAME_TYPE = 'game_type';
    var COMMON_CASHBACK_MULTIPLE_RANGE_TYPE_GAME = 'game';

    function Common_Cashback_Multiple_Range_Rules(){
        this.options = {
            "container": ".common_cashback_rules_by_multiple_range",
            "urls": {
                "get_active_rules": "/marketing_management/getCommonCashbackRuleByMultipleRangeRules",
                "save_settings": "/marketing_management/saveCommonCashbackRuleByMultipleRangeSettings",
                "create_rule": "/marketing_management/createCommonCashbackRuleByMultipleRangeRule",
                "update_rule": "/marketing_management/updateCommonCashbackRuleByMultipleRangeRule",
                "delete_rule": "/marketing_management/deleteCommonCashbackRuleByMultipleRangeRule"
            },
            "text": {
                "common_cashback_settings_of_multiple_range": "Common cashback settings of multiple range",
                "unknown_error": "Sorry, something wrong",
                "settings": "Settings",
                "others": "Others",
                "add": "Add",
                "close": "Close",
                "save": "Save",
                "delete": "Delete",
                "status": "Status",
                "active": "Active",
                "inactive": "Inactive",
                "active_childs": "Childs active",
                "rules": "Rules",
                "game_type": "Game Type",
                "game_list": "Game List",
                "total_games": "Total Games",
                "total_enabled_cashback_games": "Total Enabled Cashback Games",
                "total_new_games": "Total New Games",
                "min_bet_amount": "Min Bet Amount",
                "max_bet_amount": "Max Bet Amount",
                "cashback_percentage": "Percentage",
                "max_cashback_amount": "Max Cashback Amount",
                "cashbackSettings": "Cashback Settings",
                "min_bet_amount_format_error": "Min bet amount is not valid",
                "max_bet_amount_format_error": "Max bet amount is not valid",
                "cashback_percentage_format_error": "Cashback percentage is not valid",
                "max_cashback_amount_format_error": "Max cashback amount is not valid",
                "cashback_percentage_is_required": "Cashback percentage is required",
                "max_bet_ammount_must_be_greater_than_min_bet_amount": "Min bet amount must be greater than Min bet amount",
                "save_success": "Successfully saved",
                "save_failed": "Sorry, save failed",
                "confirm_delete_rule": "Do you really want to delete this cashback rule?",
                "deleted_successfully": "Successfully deleted",
                "deleted_failed": "Failed to delete",
                "active_cashback_to_all_games_msg": "Are you sure you want to active cashback to all games?",
                "inactive_cashback_to_all_games_msg": "Are you sure you want to inactive cashback to all games?",
                "active_cashback": "Active cashback",
                "inactive_cashback": "Inactive cashback"
            }
        };

        this.container = null;
        this.template_settings = null;
    }

    Common_Cashback_Multiple_Range_Rules.prototype.getGamePlatofrmData = function(game_platform_id){
        if(!this.template_settings.settings.hasOwnProperty(game_platform_id)){
            return false;
        }

        return this.template_settings.settings[game_platform_id];
    };

    Common_Cashback_Multiple_Range_Rules.prototype.getGameTypeData = function(game_platform_id, game_type_id){
        var game_platform_data = this.getGamePlatofrmData(game_platform_id);
        if(!game_platform_data){
            return false;
        }

        if(!game_platform_data.types.hasOwnProperty(game_type_id)){
            return false;
        }

        return game_platform_data.types[game_type_id];
    };

    Common_Cashback_Multiple_Range_Rules.prototype.getGameDescriptionData = function(game_platform_id, game_type_id, game_description_id){
        var game_type_data = this.getGameTypeData(game_platform_id, game_type_id);
        if(!game_type_data){
            return false;
        }

        if(!game_type_data.game_list.hasOwnProperty(game_description_id)){
            return false;
        }

        return game_type_data.game_list[game_description_id];
    };

    Common_Cashback_Multiple_Range_Rules.prototype.getDataByTypeMap = function(type, type_map_id){
        var game_platform_id, game_platform_data, game_type_id, game_type_data;

        if(type == COMMON_CASHBACK_MULTIPLE_RANGE_TYPE_GAME_PLATFORM){
            return this.getGamePlatofrmData(type_map_id);
        }else if(type === COMMON_CASHBACK_MULTIPLE_RANGE_TYPE_GAME_TYPE){
            for(game_platform_id in this.template_settings.settings){
                game_platform_data = this.template_settings.settings[game_platform_id];

                if(game_platform_data.types.hasOwnProperty(type_map_id)){
                    return game_platform_data.types[type_map_id];
                }
            }
        }else{
            for(game_platform_id in this.template_settings.settings){
                game_platform_data = this.template_settings.settings[game_platform_id];

                for(game_type_id in game_platform_data.types){
                    game_type_data = game_platform_data.types[game_type_id];

                    if(game_type_data.game_list.hasOwnProperty(type_map_id)){
                        return game_type_data.game_list[type_map_id];
                    }
                }
            }
        }

        return false;
    };

    Common_Cashback_Multiple_Range_Rules.prototype.init = function(options){
        this.options = $.extend(true,  this.options, options);

        this.loader = $('<div class="common_cashback_multiple_range_raules_loader"><div class="loader_instance"></div></div>');

        this.container = $(this.options.container);

        this.container.off('show.bs.collapse hide.bs.collapse').on('show.bs.collapse hide.bs.collapse', function(e) {
            var element = $(e.target);
            var targetId = element.attr('id');
            var targetElement = $('button[data-target="#' + targetId + '"] .glyphicon');

            targetElement.toggleClass('glyphicon-chevron-up glyphicon-chevron-down');
        });

        this.container.empty().html(this.loader[0].outerHTML);

        this.initEvents();
    };

    Common_Cashback_Multiple_Range_Rules.prototype.initEvents = function(){
        var self = this;

        var reloadAndApplySettings = function(trigger_element, callback){
            self.reloadSettings(function(){
                var game_platform_entry_container = trigger_element.closest('.game_platform_entry_container');
                if(game_platform_entry_container.length <= 0){
                    self.showUnknownError();
                    return true;
                }

                var game_platform_id = game_platform_entry_container.data('game_platform_id');
                var game_platform_data = self.getGamePlatofrmData(game_platform_id);

                if(!game_platform_data){
                    self.showUnknownError();
                    return true;
                }

                self.applyGamePlatformSettings(game_platform_id, game_platform_data);
                self.applyAllGameTypeSettings(game_platform_id, game_platform_data);
                self.applyAllGameSettingsWithGamePlatform(game_platform_id, game_platform_data);

                if(typeof callback === "function") callback();
            });
        };

        var reload_rules = function(trigger_element, type, type_map_id){
            var rules, container, game_platform_id, game_platform_data, game_type_id, game_type_data, game_description_id, game_description_data;

            if(type == COMMON_CASHBACK_MULTIPLE_RANGE_TYPE_GAME_PLATFORM){
                var game_platform_entry_container = trigger_element.closest('.game_platform_entry_container');
                if(game_platform_entry_container.length <= 0){
                    self.showUnknownError();
                    return true;
                }

                game_platform_id = type_map_id;
                game_platform_data = self.getGamePlatofrmData(game_platform_id);

                if(!game_platform_data){
                    self.showUnknownError();
                    return true;
                }

                container = $('.game_platform_entry_rules_container .rules_list', game_platform_entry_container);
                rules = game_platform_data.cashback_rules;

                self.renderRulesEntry(container, rules);
            }else if(type == COMMON_CASHBACK_MULTIPLE_RANGE_TYPE_GAME_TYPE){
                var game_type_entry_container = trigger_element.closest('.game_type_entry_container');
                if(game_type_entry_container.length <= 0){
                    self.showUnknownError();
                    return true;
                }

                game_platform_id = game_type_entry_container.data('game_platform_id');
                game_type_id = type_map_id;
                game_type_data = self.getGameTypeData(game_platform_id, game_type_id);

                if(!game_type_data){
                    self.showUnknownError();
                    return true;
                }

                container = $('.game_type_entry_rules_container .rules_list', game_type_entry_container);
                rules = game_type_data.cashback_rules;

                self.renderRulesEntry(container, rules);
            }else{
                var game_description_entry_container = trigger_element.closest('.game_description_entry_container');
                if(game_description_entry_container.length <= 0){
                    self.showUnknownError();
                    return true;
                }

                game_platform_id = game_description_entry_container.data('game_platform_id');
                game_type_id = game_description_entry_container.data('game_type_id');
                game_description_id = type_map_id;
                game_description_data = self.getGameDescriptionData(game_platform_id, game_type_id, game_description_id);

                if(!game_description_data){
                    self.showUnknownError();
                    return true;
                }

                container = $('.game_description_entry_rules_container .rules_list', game_description_entry_container);
                rules = game_description_data.cashback_rules;

                self.renderRulesEntry(container, rules);
            }
        };

        $(self.container).on('click', '.game_platform_entry_body', function(){
            var game_platform_entry_details = $(this).siblings('.game_platform_entry_details');

            if(game_platform_entry_details.is(':visible')){
                game_platform_entry_details.collapse('hide');
                return true;
            }

            game_platform_entry_details.collapse('toggle');
        });

        $(self.container).on('shown.bs.collapse', '.game_platform_entry_details', function(e){
            var game_platform_entry_container = $(this).closest('.game_platform_entry_container');

            var game_platform_id = game_platform_entry_container.data('game_platform_id');
            var game_platform_data = self.getGamePlatofrmData(game_platform_id);

            if(!game_platform_data){
                self.showUnknownError();
                return true;
            }

            self.applyAllGameTypeSettings(game_platform_id, game_platform_data);
            self.renderRulesEntry($('.game_platform_entry_rules_container .rules_list', game_platform_entry_container), game_platform_data.cashback_rules);
        });

        $(self.container).on('click', '.game_type_entry_body', function(){
            var game_type_entry_details = $(this).siblings('.game_type_entry_details');

            if(game_type_entry_details.is(':visible')){
                game_type_entry_details.collapse('hide');
                return true;
            }

            game_type_entry_details.collapse('toggle');
        });

        $(self.container).on('shown.bs.collapse', '.game_type_entry_details', function(e){
            var game_type_entry_details = $(this);

            var game_type_entry_container = $(this).closest('.game_type_entry_container');

            var game_platform_id = game_type_entry_container.data('game_platform_id');
            var game_type_id = game_type_entry_container.data('game_type_id');
            var game_type_data = self.getGameTypeData(game_platform_id, game_type_id);

            if(!game_type_data){
                self.showUnknownError();
                return true;
            }

            if(!game_type_entry_container.data('has-game-list')){
                game_type_entry_container.data('has-game-list', true);

                var game_list_container = $(self.renderGameListContainer(game_type_id, game_type_data));
                game_type_entry_details.append(game_list_container);
            }

            self.applyAllGameSettings(game_type_id, game_type_data);
            self.renderRulesEntry($('.game_type_entry_rules_container .rules_list', game_type_entry_container), game_type_data.cashback_rules);
        });

        $(self.container).on('click', '.game_description_entry_body', function(){
            var game_description_entry_details = $(this).siblings('.game_description_entry_details');

            if(game_description_entry_details.is(':visible')){
                game_description_entry_details.collapse('toggle');
                return true;
            }

            var game_description_entry_container = $(this).closest('.game_description_entry_container');
            if(game_description_entry_container.length <= 0){
                self.showUnknownError();
                return true;
            }

            var game_platform_id = game_description_entry_container.data('game_platform_id');
            var game_type_id = game_description_entry_container.data('game_type_id');
            var game_description_id = game_description_entry_container.data('game_description_id');
            var game_description_data = self.getGameDescriptionData(game_platform_id, game_type_id, game_description_id);

            if(!game_description_data){
                self.showUnknownError();
                return true;
            }

            self.applyGameSettings(game_description_id, game_description_data, game_description_entry_container);
            self.renderRulesEntry($('.game_description_entry_rules_container .rules_list', game_description_entry_container), game_description_data.cashback_rules);

            game_description_entry_details.collapse('toggle');
        });

        $(self.container).on('click', '.switch_cashback input[type=radio]', function(){
            if($(this).is(':disabled')){
                return false;
            }

            var type = $(this).data('type');
            var type_map_id = $(this).data('type_map_id');
            var type_data = self.getDataByTypeMap(type, type_map_id);

            if(!type_data){
                self.showUnknownError();
                return false;
            }

            var value = $(this).val();

            var option = $(this);

            var bootstrap_dialog_btn_confirm_action = function(dialog){
                var $button = this;
                $button.disable();
                $button.spin();

                var $closeButton = dialog.getButton('common_cashback_rule_close_btn');
                $closeButton.disable();

                // option.prop('checked', true);

                self.saveSettings(self.template_settings.cb_mr_tpl_id, type, type_map_id, value, function(){
                    reloadAndApplySettings(option, function(){
                        dialog.close();
                    });
                });
            };

            var bootstrap_dialog_toggle_game_action = function(dialog){
                var $closeButton = dialog.getButton('common_cashback_rule_close_btn');
                $closeButton.spin();
                $closeButton.disable();

                self.saveSettings(self.template_settings.cb_mr_tpl_id, type, type_map_id, value, function(){
                    reloadAndApplySettings(option, function(){
                        dialog.close();
                    });
                });
            };

            var bootstrap_dialog_btn_confirm_active = {
                "id": 'common_cashback_rule_confirm_btn',
                "label": self.options.text.active,
                "cssClass": 'btn-warning',
                "action": bootstrap_dialog_btn_confirm_action
            };

            var bootstrap_dialog_btn_confirm_inactive = {
                "id": 'common_cashback_rule_confirm_btn',
                "label": self.options.text.inactive,
                "cssClass": 'btn-warning',
                "action": bootstrap_dialog_btn_confirm_action
            };

            var bootstrap_dialog_btn_close = {
                "id": 'common_cashback_rule_close_btn',
                "hotkey": 13, // Enter.
                "label": self.options.text.close,
                "action": function(dialog){
                    var $button = this;
                    dialog.close();
                }
            };

            var bootstrap_dialog_options = {
                "closable": false,
                "closeByBackdrop": false,
                "closeByKeyboard": false,
                "title": self.options.text.common_cashback_settings_of_multiple_range,
                "onshown": function(dialog){
                    var $closeButton = dialog.getButton('common_cashback_rule_close_btn');
                    $closeButton.focus();
                }
            };

            if(value == "2"){
                return false;
            }

            if(type != COMMON_CASHBACK_MULTIPLE_RANGE_TYPE_GAME){
                if(value == "1"){
                    bootstrap_dialog_options['message'] = self.options.text.active_cashback_to_all_games_msg;
                    bootstrap_dialog_options['buttons'] = [bootstrap_dialog_btn_confirm_active, bootstrap_dialog_btn_close];
                    BootstrapDialog.show(bootstrap_dialog_options);
                }else{
                    bootstrap_dialog_options['message'] = self.options.text.inactive_cashback_to_all_games_msg;
                    bootstrap_dialog_options['buttons'] = [bootstrap_dialog_btn_confirm_inactive, bootstrap_dialog_btn_close];
                    BootstrapDialog.show(bootstrap_dialog_options);
                }
            }else{
                if(value == "1"){
                    BootstrapDialog.show({
                        "title": self.options.text.common_cashback_settings_of_multiple_range,
                        "message": self.options.text.active_cashback,
                        "buttons": [bootstrap_dialog_btn_close],
                        "onshown": bootstrap_dialog_toggle_game_action
                    });
                }else{
                    BootstrapDialog.show({
                        "title": self.options.text.common_cashback_settings_of_multiple_range,
                        "message": self.options.text.inactive_cashback,
                        "buttons": [bootstrap_dialog_btn_close],
                        "onshown": bootstrap_dialog_toggle_game_action
                    });
                }
                return false;
            }

            return false;
        });

        $(self.container).on('click', '.create_rule', function(){
            var button = $(this);
            var type = $(this).data('type');
            var type_map_id = $(this).data('type_map_id');
            var modal = self.getCreateRuleModal(type, type_map_id);

            modal.on('valid.t1t.cashback.multiple-range.rules.form', function(e, data){
                modal.modal('hide');
                self.loader.show();
                self.createRule(self.template_settings.cb_mr_tpl_id, data.type, data.type_map_id, data.min_bet_amount, data.max_bet_amount, data.cashback_percentage, data.max_cashback_amount, function(result){
                    reloadAndApplySettings(button, function(){
                        self.loader.hide();
                        reload_rules(button, type, type_map_id);
                        if(result.status === "success"){
                            BootstrapDialog.show({
                                "title": self.options.text.common_cashback_settings_of_multiple_range,
                                "message": self.options.text.save_success
                            });
                        }else{
                            BootstrapDialog.show({
                                "title": self.options.text.common_cashback_settings_of_multiple_range,
                                "message": self.options.text.save_failed
                            });
                        }
                    });
                });
            });
            modal.modal('show');
        });

        $(self.container).on('click', '.edit_rule', function(){
            var button = $(this);
            var rule_id = $(this).data('rule_id');
            var type = $(this).data('type');
            var type_map_id = $(this).data('type_map_id');
            var min_bet_amount = $(this).data('min_bet_amount');
            var max_bet_amount = $(this).data('max_bet_amount');
            var cashback_percentage = $(this).data('cashback_percentage');
            var max_cashback_amount = $(this).data('max_cashback_amount');
            var modal = self.getEditRuleModal(rule_id, type, type_map_id, min_bet_amount, max_bet_amount, cashback_percentage, max_cashback_amount);

            modal.on('valid.t1t.cashback.multiple-range.rules.form', function(e, data){
                modal.modal('hide');
                self.loader.show();
                self.updateRule(data.rule_id, data.min_bet_amount, data.max_bet_amount, data.cashback_percentage, data.max_cashback_amount, function(result){
                    self.reloadSettings(function(){
                        self.loader.hide();
                        reload_rules(button, type, type_map_id);
                        if(result.status === "success"){
                            BootstrapDialog.show({
                                "title": self.options.text.common_cashback_settings_of_multiple_range,
                                "message": self.options.text.save_success
                            });
                        }else{
                            BootstrapDialog.show({
                                "title": self.options.text.common_cashback_settings_of_multiple_range,
                                "message": self.options.text.save_failed
                            });
                        }
                    });
                });
            });
            modal.modal('show');
        });

        $(self.container).on('click', '.delete_rule', function(){
            var button = $(this);
            var type = $(this).data('type');
            var type_map_id = $(this).data('type_map_id');

            var data = $(this).data();

            BootstrapDialog.show({
                title: self.options.text.common_cashback_settings_of_multiple_range,
                message: self.options.text.confirm_delete_rule,
                buttons: [{
                    "id": 'common_cashback_rule_delete_btn',
                    "label": self.options.text.delete,
                    "cssClass": 'btn-danger',
                    "action": function(dialog){
                        var $button = this;
                        $button.disable();
                        $button.spin();
                        dialog.setClosable(false);

                        var $closeButton = dialog.getButton('common_cashback_rule_close_btn');
                        $closeButton.disable();

                        self.deleteRule(data.rule_id, function(result){
                            reloadAndApplySettings(button, function(){
                                dialog.close();

                                if(result.status === "success"){
                                    BootstrapDialog.show({
                                        "title": self.options.text.common_cashback_settings_of_multiple_range,
                                        "message": self.options.text.deleted_successfully,
                                        "onhide": function(dialog){
                                            reload_rules(button, type, type_map_id);
                                        }
                                    });
                                }else{
                                    BootstrapDialog.show({
                                        "title": self.options.text.common_cashback_settings_of_multiple_range,
                                        "message": self.options.text.deleted_failed,
                                        "onhide": function(dialog){
                                            reload_rules(button, type, type_map_id);
                                        }
                                    });
                                }
                            });
                        });
                    }
                }, {
                    "id": 'common_cashback_rule_close_btn',
                    "label": self.options.text.close,
                    "action": function(dialog){
                        var $button = this;
                        $button.disable();
                        $button.spin();
                        dialog.close();
                    }
                }]
            });
        });
    };

    Common_Cashback_Multiple_Range_Rules.prototype.run = function(){
        var self = this;

        this.reloadSettings(function(){
            self.render();
            self.applyAllGamePlatformSettings();
        });
    };

    Common_Cashback_Multiple_Range_Rules.prototype.reloadSettings = function(callback){
        var self = this;

        this.getActiveRules(function(settings){
            self.template_settings = settings;
            self.checkSettings();
            if(typeof callback === "function") callback(settings);
        });
    };

    Common_Cashback_Multiple_Range_Rules.prototype.getActiveRules = function(callback){
        $.ajax({
            "contentType": "application/json; charset=utf-8",
            "dataType": "json",
            "url": this.options.urls.get_active_rules,
            "type": "GET",
            "success": function(result){
                if(result.status === "success"){
                    if(typeof callback === "function") callback(result.data);
                }
            },
            "error": function(){
            }
        });
    };

    Common_Cashback_Multiple_Range_Rules.prototype.saveSettings = function(tpl_id, type, type_map_id, enabled_cashback, callback){
        var data = {
          "tpl_id": tpl_id,
          "type": type,
          "type_map_id": type_map_id,
          "enabled_cashback": enabled_cashback
        };

        $.ajax({
            "contentType": "application/x-www-form-urlencoded",
            "url": this.options.urls.save_settings,
            "type": "POST",
            "data": data,
            "success": function(result){
                if(typeof callback === "function") callback(result);
            },
            "error": function(){
                if(typeof callback === "function") callback();
            }
        });
    };

    Common_Cashback_Multiple_Range_Rules.prototype.createRule = function(tpl_id, type, type_map_id, min_bet_amount, max_bet_amount, cashback_percentage, max_cashback_amount, callback){
        var data = {
            "tpl_id": tpl_id,
            "type": type,
            "type_map_id": type_map_id,
            "min_bet_amount": min_bet_amount,
            "max_bet_amount": max_bet_amount,
            "cashback_percentage": cashback_percentage,
            "max_cashback_amount": max_cashback_amount
        };

        $.ajax({
            "contentType": "application/x-www-form-urlencoded",
            "url": this.options.urls.create_rule,
            "type": "POST",
            "data": data,
            "success": function(result){
                if(typeof callback === "function") callback(result);
            },
            "error": function(){
                if(typeof callback === "function") callback();
            }
        });
    };

    Common_Cashback_Multiple_Range_Rules.prototype.updateRule = function(rule_id, min_bet_amount, max_bet_amount, cashback_percentage, max_cashback_amount, callback){
        var data = {
            "rule_id": rule_id,
            "min_bet_amount": min_bet_amount,
            "max_bet_amount": max_bet_amount,
            "cashback_percentage": cashback_percentage,
            "max_cashback_amount": max_cashback_amount
        };

        $.ajax({
            "contentType": "application/x-www-form-urlencoded",
            "url": this.options.urls.update_rule,
            "type": "POST",
            "data": data,
            "success": function(result){
                if(typeof callback === "function") callback(result);
            },
            "error": function(){
                if(typeof callback === "function") callback();
            }
        });
    };

    Common_Cashback_Multiple_Range_Rules.prototype.deleteRule = function(rule_id, callback){
        var data = {
            "rule_id": rule_id
        };

        $.ajax({
            "contentType": "application/x-www-form-urlencoded",
            "url": this.options.urls.delete_rule,
            "type": "POST",
            "data": data,
            "success": function(result){
                if(typeof callback === "function") callback(result);
            },
            "error": function(){
                if(typeof callback === "function") callback();
            }
        });
    };

    Common_Cashback_Multiple_Range_Rules.prototype.checkSettings = function(){
        var self = this;

        $.each(this.template_settings.settings, function(game_platform_id, game_platform_data){
            var game_platform_other_settings = {
                "has_cashback_rules_error": false,
                "childs_has_cashback_rules_error": false,
                "childs_has_enabled_cashback": false,
                "total_enabled_cashback_games": 0,
                "total_games": 0,
                "total_new_games": 0
            };

            game_platform_other_settings.has_cashback_rules_error = $.isEmptyObject(game_platform_data.cashback_rules);

            $.each(game_platform_data.types, function(game_type_id, game_type_data){
                var game_type_other_settings = {
                    "has_cashback_rules_error": false,
                    "childs_has_cashback_rules_error": false,
                    "childs_has_enabled_cashback": false,
                    "total_enabled_cashback_games": 0,
                    "total_games": 0,
                    "total_new_games": 0
                };

                game_type_other_settings.has_cashback_rules_error = $.isEmptyObject(game_type_data.cashback_rules);
                if(game_type_other_settings.has_cashback_rules_error){
                    game_type_other_settings.has_cashback_rules_error = game_platform_other_settings.has_cashback_rules_error;
                }

                $.each(game_type_data.game_list, function(game_description_id, game_description_data){
                    var game_other_settings = {
                        "has_cashback_rules_error": $.isEmptyObject(game_description_data.cashback_rules)
                    };

                    if(game_description_data.cashback_settings.enabled_cashback){
                        if(game_other_settings.has_cashback_rules_error){
                            game_other_settings.has_cashback_rules_error = game_type_other_settings.has_cashback_rules_error;

                            if(game_other_settings.has_cashback_rules_error){
                                game_type_other_settings.childs_has_cashback_rules_error = true;
                            }
                        }

                        game_type_other_settings.childs_has_enabled_cashback = true;
                        game_type_other_settings.total_enabled_cashback_games = game_type_other_settings.total_enabled_cashback_games + 1;
                    }

                    game_type_other_settings.total_games = game_type_other_settings.total_games + 1;

                    if($.parseJSON(game_description_data.details.flag_new_game)){
                        game_type_other_settings.total_new_games = game_type_other_settings.total_new_games + 1;
                    }

                    game_description_data.others = game_other_settings;
                });

                game_platform_other_settings.childs_has_cashback_rules_error = (game_type_other_settings.childs_has_cashback_rules_error) ? true : game_platform_other_settings.childs_has_cashback_rules_error;
                game_platform_other_settings.childs_has_enabled_cashback = (game_type_other_settings.childs_has_enabled_cashback) ? true : game_platform_other_settings.childs_has_enabled_cashback;
                game_platform_other_settings.total_enabled_cashback_games = game_platform_other_settings.total_enabled_cashback_games + game_type_other_settings.total_enabled_cashback_games;
                game_platform_other_settings.total_games = game_platform_other_settings.total_games + game_type_other_settings.total_games;
                game_platform_other_settings.total_new_games = game_platform_other_settings.total_new_games + game_type_other_settings.total_new_games;

                game_type_data.others = game_type_other_settings;
            });

            game_platform_data.others = game_platform_other_settings;
        });
    };

    Common_Cashback_Multiple_Range_Rules.prototype.applyAllGamePlatformSettings = function(){
        var self = this;

        $.each(this.template_settings.settings, function(game_platform_id, game_platform_data){
            self.applyGamePlatformSettings(game_platform_id, game_platform_data);
        });
    };

    Common_Cashback_Multiple_Range_Rules.prototype.applyAllGameTypeSettings = function(game_platform_id, game_platform_data){
        var self = this;

        $.each(game_platform_data.types, function(game_type_id, game_type_data){
            self.applyGameTypeSettings(game_type_id, game_type_data);
        });
    };

    Common_Cashback_Multiple_Range_Rules.prototype.applyAllGameSettingsWithGamePlatform = function(game_platform_id, game_platform_data){
        var self = this;

        $.each(game_platform_data.types, function(game_type_id, game_type_data){
            var game_type_entry_container = $('#game_type_' + game_type_id);

            var game_description_entry_container_list = $('.game_list_container .game_description_entry_container', game_type_entry_container);

            game_description_entry_container_list.each(function(){
                var game_description_id = $(this).data('game_description_id');
                if(!game_type_data.game_list.hasOwnProperty(game_description_id)){
                    return;
                }
                var game_description_data = game_type_data.game_list[game_description_id];
                self.applyGameSettings(game_description_id, game_description_data, $(this));
            });
        });
    };

    Common_Cashback_Multiple_Range_Rules.prototype.applyAllGameSettings = function(game_type_id, game_type_data){
        var self = this;

        var game_type_entry_container = $('#game_type_' + game_type_id);

        var game_description_entry_container_list = $('.game_list_container .game_description_entry_container', game_type_entry_container);

        game_description_entry_container_list.each(function(){
            var game_description_id = $(this).data('game_description_id');
            if(!game_type_data.game_list.hasOwnProperty(game_description_id)){
                return;
            }
            var game_description_data = game_type_data.game_list[game_description_id];
            self.applyGameSettings(game_description_id, game_description_data, $(this));
        });
    };

    Common_Cashback_Multiple_Range_Rules.prototype.applyGamePlatformSettings = function(game_platform_id, game_platform_data){
        var self = this;

        var container = $('#game_platform_' + game_platform_id, self.container);
        var body_container = $('#game_platform_' + game_platform_id + '_body', container);
        var details_container = $('#game_platform_' + game_platform_id + '_details', container);

        var summary_html = '';

        body_container.removeClass('warning_logical_problem');

        if(game_platform_data.cashback_settings.enabled_cashback){
            if(game_platform_data.others.childs_has_enabled_cashback){
                summary_html += '<div class="entry"><span class="status_label">' + self.options.text.status + ':</span><span class="status_value childs_active">' + self.options.text.active_childs + '</span></div>';

                $('.game_platform_entry_settings_container .switch_cashback [value=2]', details_container).prop('checked', true);
            }else{
                summary_html += '<div class="entry"><span class="status_label">' + self.options.text.status + ':</span><span class="status_value active">' + self.options.text.active + '</span></div>';

                $('.game_platform_entry_settings_container .switch_cashback [value=1]', details_container).prop('checked', true);
            }

            if(game_platform_data.others.childs_has_cashback_rules_error){
                body_container.addClass('warning_logical_problem');
            }
        }else{
            if(game_platform_data.others.childs_has_enabled_cashback){
                summary_html += '<div class="entry"><span class="status_label">' + self.options.text.status + ':</span><span class="status_value childs_active">' + self.options.text.active_childs + '</span></div>';

                $('.game_platform_entry_settings_container .switch_cashback [value=2]', details_container).prop('checked', true);

                if(game_platform_data.others.childs_has_cashback_rules_error){
                    body_container.addClass('warning_logical_problem');
                }
            }else{
                summary_html += '<div class="entry"><span class="status_label">' + self.options.text.status + ':</span><span class="status_value inactive">' + self.options.text.inactive + '</span></div>';

                $('.game_platform_entry_settings_container .switch_cashback [value=0]', details_container).prop('checked', true);
            }
        }

        summary_html += '<div class="entry"><span class="total_games_label">' + self.options.text.total_games + ':</span><span class="total_games_value">' + game_platform_data.others.total_games + '</span></div>';
        summary_html += '<div class="entry"><span class="enabled_cashback_games_label">' + self.options.text.total_enabled_cashback_games + ':</span><span class="enabled_cashback_games_value">' + game_platform_data.others.total_enabled_cashback_games + '</span></div>';
        summary_html += '<div class="entry"><span class="total_new_games_label">' + self.options.text.total_new_games + ':</span><span class="total_new_games_value">' + game_platform_data.others.total_new_games + '</span></div>';

        $('.game_platform_entry_summary', body_container).html(summary_html);
    };

    Common_Cashback_Multiple_Range_Rules.prototype.applyGameTypeSettings = function(game_type_id, game_type_data){
        var self = this;

        var container = $('#game_type_' + game_type_id, self.container);
        var body_container = $('#game_type_' + game_type_id + '_body', container);
        var details_container = $('#game_type_' + game_type_id + '_details', container);

        var summary_html = '';

        body_container.removeClass('warning_logical_problem');

        if(game_type_data.cashback_settings.enabled_cashback){
            summary_html += '<div class="entry"><span class="status_label">' + self.options.text.status + ':</span><span class="status_value active">' + self.options.text.active + '</span></div>';

            $('.game_type_entry_settings_container .switch_cashback [value=1]', details_container).prop('checked', true);

            if(game_type_data.others.childs_has_cashback_rules_error){
                body_container.addClass('warning_logical_problem');
            }
        }else{
            if(game_type_data.others.childs_has_enabled_cashback){
                summary_html += '<div class="entry"><span class="status_label">' + self.options.text.status + ':</span><span class="status_value childs_active">' + self.options.text.active_childs + '</span></div>';

                $('.game_type_entry_settings_container .switch_cashback [value=2]', details_container).prop('checked', true);

                if(game_type_data.others.childs_has_cashback_rules_error){
                    body_container.addClass('warning_logical_problem');
                }
            }else{
                summary_html += '<div class="entry"><span class="status_label">' + self.options.text.status + ':</span><span class="status_value inactive">' + self.options.text.inactive + '</span></div>';

                $('.game_type_entry_settings_container .switch_cashback [value=0]', details_container).prop('checked', true);
            }
        }

        summary_html += '<div class="entry"><span class="total_games_label">' + self.options.text.total_games + ':</span><span class="total_games_value">' + game_type_data.others.total_games + '</span></div>';
        summary_html += '<div class="entry"><span class="enabled_cashback_games_label">' + self.options.text.total_enabled_cashback_games + ':</span><span class="enabled_cashback_games_value">' + game_type_data.others.total_enabled_cashback_games + '</span></div>';
        summary_html += '<div class="entry"><span class="total_new_games_label">' + self.options.text.total_new_games + ':</span><span class="total_new_games_value">' + game_type_data.others.total_new_games + '</span></div>';

        $('.game_type_entry_summary', body_container).html(summary_html);
    };

    Common_Cashback_Multiple_Range_Rules.prototype.applyGameSettings = function(game_description_id, game_description_data, container){
        var self = this;

        var body_container = $('#game_description_' + game_description_id + '_body', container);
        var details_container = $('#game_description_' + game_description_id + '_details', container);

        var summary_html = '';

        body_container.removeClass('warning_logical_problem');

        if(game_description_data.cashback_settings.enabled_cashback){
            summary_html += '<div class="entry"><span class="status_label">' + self.options.text.status + ':</span><span class="status_value active">' + self.options.text.active + '</span></div>';

            $('.game_description_entry_settings_container .switch_cashback [value=1]', details_container).prop('checked', true);

            if(game_description_data.others.has_cashback_rules_error){
                body_container.addClass('warning_logical_problem');
            }
        }else{
            summary_html += '<div class="entry"><span class="status_label">' + self.options.text.status + ':</span><span class="status_value inactive">' + self.options.text.inactive + '</span></div>';

            $('.game_description_entry_settings_container .switch_cashback [value=0]', details_container).prop('checked', true);
        }

        $('.game_description_entry_summary', body_container).html(summary_html);
    };

    Common_Cashback_Multiple_Range_Rules.prototype.render = function(){
        var self = this;
        var container = this.container;
        container.empty();

        var html = '';
        $.each(this.template_settings.settings, function(game_platform_id, game_platform_data){
            html += '<div id="game_platform_' + game_platform_id + '" data-game_platform_id="' + game_platform_id + '" class="game_platform_entry_container">';

            html += self.renderGamePlatformEntryBody(game_platform_id, game_platform_data);

            html += self.renderGamePlatformEntryDetails(game_platform_id, game_platform_data);

            html += '</div>';
        });
        container.html(html);

        container.append(self.loader);
        self.loader.hide();
    };

    Common_Cashback_Multiple_Range_Rules.prototype.renderGamePlatformEntryBody = function(game_platform_id, game_platform_data){
        var html = '';

        html += '<div id="game_platform_' + game_platform_id + '_body" class="game_platform_entry_body">';

        html += '<div class="game_platform_details_toggle">';
        html += '<button data-toggle="collapse" data-target="#game_platform_' + game_platform_id + '_details"><i class="glyphicon glyphicon-chevron-up"></i></button>';
        html += '</div>'; // game_platform_details_toggle

        html += '<div class="game_platform_entry_content">';
        html += '<div class="game_platform_entry_title">' + game_platform_data.name + '</div>';
        html += '<div class="game_platform_entry_summary">';
        html += '</div>'; // game_platform_entry_summary
        html += '</div>'; // game_platform_entry_content

        html += '<div class="clearfix"></div>';
        html += '</div>'; // game_platform_entry_body

        return html;
    };

    Common_Cashback_Multiple_Range_Rules.prototype.renderGamePlatformEntryDetails = function(game_platform_id, game_platform_data){
        var html = '';

        html += '<div id="game_platform_' + game_platform_id + '_details" class="game_platform_entry_details collapse">';

        html += this.renderGamePlatformEntrySettings(game_platform_id, game_platform_data);

        html += this.renderRules(game_platform_data.cashback_rules, COMMON_CASHBACK_MULTIPLE_RANGE_TYPE_GAME_PLATFORM, game_platform_id, 'game_platform_entry_rules_container');

        html += this.renderGameTypeContainer(game_platform_id, game_platform_data);

        html += '</div>';

        return html;
    };

    Common_Cashback_Multiple_Range_Rules.prototype.renderGamePlatformEntrySettings = function(game_platform_id, game_platform_data){
        var html = '';

        html += '<div class="game_platform_entry_settings_container">';

        html += '<div class="title"><span>' + this.options.text.settings + '</span></div>';

        html += '<div class="settings_body">';

        html += '<div class="form-group radio-group switch_cashback">';

        var radio_attrs = 'name="game_platform_' + game_platform_id + '_cashback_switch" data-type="' + COMMON_CASHBACK_MULTIPLE_RANGE_TYPE_GAME_PLATFORM + '" data-type_map_id="' + game_platform_id + '"';

        html += '<div class="radio-inline">';
        html += '<label><input type="radio" class="enable_cashback" ' + radio_attrs + ' value="1">' + this.options.text.active + '</label>';
        html += '</div>';

        html += '<div class="radio-inline">';
        html += '<label><input type="radio" class="disable_cashback" ' + radio_attrs + ' value="0" checked="checked">' + this.options.text.inactive + '</label>';
        html += '</div>';

        html += '<div class="radio-inline disabled">';
        html += '<label><input type="radio" class="child_cashback" ' + radio_attrs + ' value="2" disabled="disabled">' + this.options.text.active_childs + '</label>';
        html += '</div>';

        html += '</div>'; // switch_cashback

        html += '</div>'; // settings_body

        html += '</div>'; // game_platform_entry_settings_container

        return html;
    };

    Common_Cashback_Multiple_Range_Rules.prototype.renderGameTypeContainer = function(game_platform_id, game_platform_data){
        var self = this;
        var html = '';

        html += '<div class="game_platform_types_container">';
        html += '<div class="title"><span>' + this.options.text.game_type + '</span></div>';

        html += '<div class="game_platform_types_body">';
        $.each(game_platform_data.types, function(game_type_id, game_type_data){
            html += '<div id="game_type_' + game_type_id + '" data-game_platform_id="' + game_platform_id + '" data-game_type_id="' + game_type_id + '" class="game_type_entry_container">';

            html += self.renderGameTypeEntryBody(game_type_id, game_type_data);

            html += self.renderGameTypeEntryDetails(game_type_id, game_type_data);

            html += '</div>'; // game_type_entry_container
        });
        html += '</div>'; // game_platform_types_body

        html += '</div>'; // game_platform_types_container

        return html;
    };

    Common_Cashback_Multiple_Range_Rules.prototype.renderGameTypeEntryBody = function(game_type_id, game_type_data){
        var html = '';

        html += '<div id="game_type_' + game_type_id + '_body" class="game_type_entry_body">';

        html += '<div class="game_type_details_toggle">';
        html += '<button data-toggle="collapse" data-target="#game_type_' + game_type_id + '_details"><i class="glyphicon glyphicon-chevron-up"></i></button>';
        html += '</div>'; // game_type_details_toggle

        html += '<div class="game_type_entry_content">';
        html += '<div class="game_type_entry_title">' + game_type_data.name + '</div>';
        html += '<div class="game_type_entry_summary">';
        html += '</div>'; // game_type_entry_summary
        html += '</div>'; // game_type_entry_content

        html += '<div class="clearfix"></div>';
        html += '</div>'; // game_type_entry_body

        return html;
    };

    Common_Cashback_Multiple_Range_Rules.prototype.renderGameTypeEntryDetails = function(game_type_id, game_type_data){
        var html = '';

        html += '<div id="game_type_' + game_type_id + '_details" class="game_type_entry_details collapse">';

        html += this.renderGameTypeEntrySettings(game_type_id, game_type_data);

        html += this.renderRules(game_type_data.cashback_rules, COMMON_CASHBACK_MULTIPLE_RANGE_TYPE_GAME_TYPE, game_type_id, 'game_type_entry_rules_container');

        // html += this.renderGameListContainer(game_type_id, game_type_data); // performance issue

        html += '</div>'; // game_type_entry_details collapse

        return html;
    };

    Common_Cashback_Multiple_Range_Rules.prototype.renderGameTypeEntrySettings = function(game_type_id, game_type_data){
        var html = '';

        html += '<div class="game_type_entry_settings_container">';

        html += '<div class="title"><span>' + this.options.text.settings + '</span></div>';

        html += '<div class="settings_body">';

        html += '<div class="form-group radio-group switch_cashback">';

        var radio_attrs = 'name="game_type_' + game_type_id + '_cashback_switch" data-type="' + COMMON_CASHBACK_MULTIPLE_RANGE_TYPE_GAME_TYPE + '" data-type_map_id="' + game_type_id + '"';

        html += '<div class="radio-inline">';
        html += '<label><input type="radio" class="enable_cashback" ' + radio_attrs + ' value="1">' + this.options.text.active + '</label>';
        html += '</div>';

        html += '<div class="radio-inline">';
        html += '<label><input type="radio" class="disable_cashback" ' + radio_attrs + ' value="0" checked="checked">' + this.options.text.inactive + '</label>';
        html += '</div>';

        html += '<div class="radio-inline disabled">';
        html += '<label><input type="radio" class="child_cashback" ' + radio_attrs + ' value="2" disabled="disabled">' + this.options.text.active_childs + '</label>';
        html += '</div>';

        html += '</div>'; // switch_cashback

        html += '</div>'; // settings_body

        html += '</div>'; // game_type_entry_settings_container

        return html;
    };

    Common_Cashback_Multiple_Range_Rules.prototype.renderGameListContainer = function(game_type_id, game_type_data){
        var self = this;
        var html = '';

        html += '<div class="game_list_container">';
        html += '<div class="title"><span>' + this.options.text.game_list + '</span></div>';

        html += '<div class="game_list_body">';
        $.each(game_type_data.game_list, function(game_description_id, game_description_data){
            html += '<div id="game_description_' + game_description_id + '" data-game_platform_id="' + game_description_data.game_platform_id + '" data-game_type_id="' + game_type_id + '" data-game_description_id="' + game_description_id + '" class="game_description_entry_container">';

            html += self.renderGameDescriptionEntryBody(game_description_id, game_description_data);

            html += self.renderGameDescriptionEntryDetails(game_description_id, game_description_data);

            html += '</div>'; // game_description_entry_container
        });
        html += '</div>'; // game_list_body

        html += '</div>'; // game_list_container

        return html;
    };

    Common_Cashback_Multiple_Range_Rules.prototype.renderGameDescriptionEntryBody = function(game_description_id, game_description_data){
        var html = '';

        html += '<div id="game_description_' + game_description_id + '_body" class="game_description_entry_body">';

        html += '<div class="game_details_toggle">';
        html += '<button data-toggle="collapse" data-target="#game_description_' + game_description_id + '_details"><i class="glyphicon glyphicon-chevron-up"></i></button>';
        html += '</div>'; // game_details_toggle

        html += '<div class="game_description_entry_content">';
        html += '<div class="game_description_entry_title">' + game_description_data.name + '</div>';
        html += '<div class="game_description_entry_summary">';
        html += '</div>'; // game_description_entry_summary
        html += '</div>'; // game_description_entry_content

        html += '<div class="clearfix"></div>';
        html += '</div>'; // game_description_entry_body

        return html;
    };

    Common_Cashback_Multiple_Range_Rules.prototype.renderGameDescriptionEntryDetails = function(game_description_id, game_description_data){
        var html = '';

        html += '<div id="game_description_' + game_description_id + '_details" class="game_description_entry_details collapse">';

        html += this.renderGameDescriptionEntrySettings(game_description_id, game_description_data);

        html += this.renderRules(game_description_data.cashback_rules, COMMON_CASHBACK_MULTIPLE_RANGE_TYPE_GAME, game_description_id, 'game_description_entry_rules_container');

        html += '</div>'; // game_description_entry_details collapse

        return html;
    };

    Common_Cashback_Multiple_Range_Rules.prototype.renderGameDescriptionEntrySettings = function(game_description_id, game_description_data){
        var html = '';

        html += '<div class="game_description_entry_settings_container">';
        html += '<div class="title"><span>' + this.options.text.settings + '</span></div>';

        html += '<div class="settings_body">';

        html += '<div class="form-group radio-group switch_cashback">';

        var radio_attrs = 'name="game_description_' + game_description_id + '_cashback_switch" data-type="' + COMMON_CASHBACK_MULTIPLE_RANGE_TYPE_GAME + '" data-type_map_id="' + game_description_id + '"';

        html += '<div class="radio-inline">';
        html += '<label><input type="radio" class="enable_cashback" ' + radio_attrs + ' value="1">' + this.options.text.active + '</label>';
        html += '</div>';

        html += '<div class="radio-inline">';
        html += '<label><input type="radio" class="disable_cashback" ' + radio_attrs + ' value="0" checked="checked">' + this.options.text.inactive + '</label>';
        html += '</div>';

        html += '</div>'; // switch_cashback

        html += '</div>'; // settings_body

        html += '</div>'; // game_description_entry_settings_container

        return html;
    };

    Common_Cashback_Multiple_Range_Rules.prototype.renderRules = function(cashback_rules, type, type_map_id, container_class){
        var self = this;

        var default_attrs = 'data-type="' + type + '" data-type_map_id="' + type_map_id + '"';

        var html = '';

        html += '<div class="' + container_class + '">';
        html += '<div class="title"><span>' + this.options.text.rules + '</span></div>';
        html += '<div class="rules_body">';

        html += '<div class="rules_actions">';
        html += '<a href="javascript: void(0);" class="btn btn-primary create_rule" ' + default_attrs + '><i class="glyphicon glyphicon-plus"></i>' + this.options.text.add + '</a>';
        html += '</div>'; // rules_actions

        html += '<table class="rules_list">';
        html += '<thead>';
        html += '<tr class="rule_entry_header">' +
            '<th>&nbsp;</th>' +
            '<th>&nbsp;</th>' +
            '<th>' + this.options.text.min_bet_amount + '</th>' +
            '<th>' + this.options.text.max_bet_amount + '</th>' +
            '<th>' + this.options.text.cashback_percentage + '</th>' +
            '<th>' + this.options.text.max_cashback_amount + '</th>' +
        '</tr>';
        html += '</thead>';
        html += '<tbody>';
        html += '</tbody>';

        html += '</table>'; // rules_list

        html += '</div>'; // rules_body

        html += '</div>';

        return html;
    };

    Common_Cashback_Multiple_Range_Rules.prototype.renderRulesEntry = function(container, cashback_rules){
        $('tbody', container).empty();

        $.each(cashback_rules, function(idx, rule_data){
            var btn_attrs = '';
            btn_attrs += 'data-rule_id="' + rule_data.cb_mr_rule_id + '"';
            btn_attrs += 'data-type="' + rule_data.type + '"';
            btn_attrs += 'data-type_map_id="' + rule_data.type_map_id + '"';
            btn_attrs += 'data-min_bet_amount="' + rule_data.min_bet_amount + '"';
            btn_attrs += 'data-max_bet_amount="' + rule_data.max_bet_amount + '"';
            btn_attrs += 'data-cashback_percentage="' + rule_data.cashback_percentage + '"';
            btn_attrs += 'data-max_cashback_amount="' + rule_data.max_cashback_amount + '"';

            var row_html = '';
            row_html += '<tr class="rule_entry">';
            row_html += '<td>' + rule_data.cb_mr_rule_id + '</td>';
            row_html += '<td class="rule_entry_actions">';
            row_html += '<button class="edit_rule" ' + btn_attrs + '><i class="glyphicon glyphicon-pencil"></i></button>';
            row_html += '<button class="delete_rule" ' + btn_attrs + '><i class="glyphicon glyphicon-trash"></i></button>';
            row_html += '</td>';
            row_html += '<td class="min_bet_amount"><span class="value">' + rule_data.min_bet_amount_text + '</span></td>';
            row_html += '<td class="max_bet_amount"><span class="value">' + rule_data.max_bet_amount_text + '</span></td>';
            row_html += '<td class="cashback_percentage"><span class="value">' + rule_data.cashback_percentage + '%</span></td>';
            row_html += '<td class="max_cashback_amount"><span class="value">' + rule_data.max_cashback_amount_text + '</span></td>';
            row_html += '</tr>';

            $('tbody', container).append(row_html);
        });
    };

    Common_Cashback_Multiple_Range_Rules.prototype.getCreateRuleModal = function(type, type_map_id, min_bet_amount, max_bet_amount, cashback_percentage, max_cashback_amount){
        return this.createRuleModal(null, type, type_map_id, min_bet_amount, max_bet_amount, cashback_percentage, max_cashback_amount);
    };

    Common_Cashback_Multiple_Range_Rules.prototype.getEditRuleModal = function(rule_id, type, type_map_id, min_bet_amount, max_bet_amount, cashback_percentage, max_cashback_amount){
        return this.createRuleModal(rule_id, type, type_map_id, min_bet_amount, max_bet_amount, cashback_percentage, max_cashback_amount);
    };

    Common_Cashback_Multiple_Range_Rules.prototype.createRuleModal = function(rule_id, type, type_map_id, min_bet_amount, max_bet_amount, cashback_percentage, max_cashback_amount){
        var self = this;
        var modal = null;

        if($('#common_cashback_multiple_range_rules_modal').length){
            modal = $('#common_cashback_multiple_range_rules_modal');
        }else{
            var modal_html = '<div id="common_cashback_multiple_range_rules_modal" class="modal fade" role="dialog">\n' +
                '  <div class="modal-dialog">\n' +
                '    <!-- Modal content-->\n' +
                '    <div class="modal-content">\n' +
                '      <div class="modal-header">\n' +
                '        <button type="button" class="close" data-dismiss="modal">&times;</button>\n' +
                '        <h4 class="modal-title"><span class="modal_default_title">' + this.options.text.cashbackSettings + '</span></h4>\n' +
                '      </div>\n' +
                '      <div class="modal-body">\n' +
                '      </div>\n' +
                '      <div class="modal-footer">\n' +
                '        <button type="button" class="btn btn-primary btn-submit">' + this.options.text.save + '</button>\n' +
                '        <button type="button" class="btn btn-default" data-dismiss="modal">' + this.options.text.close + '</button>\n' +
                '      </div>\n' +
                '    </div>\n' +
                '  </div>\n' +
                '</div>';

            modal = $(modal_html).appendTo($('body'));
        }

        $('.modal-body', modal).empty();

        var form_html = '<form id="common_cashback_multiple_range_rules_form">' +
            '    <input type="hidden" name="rule_id">' +
            '    <input type="hidden" name="type">' +
            '    <input type="hidden" name="type_map_id">' +
            '    <div class="form-group">\n' +
            '        <label class="control-label" for="min_bet_amount_field">' + this.options.text.min_bet_amount + '</label>\n' +
            '        <input type="number" class="form-control" step="1" min="1" id="min_bet_amount_field" name="min_bet_amount">' +
            '        <p class="help-block"></p>\n' +
            '    </div>' +
            '    <div class="form-group">\n' +
            '        <label class="control-label" for="max_bet_amount_field">' + this.options.text.max_bet_amount + '</label>\n' +
            '        <input type="number" class="form-control" step="1" min="1" id="max_bet_amount_field" name="max_bet_amount">\n' +
            '        <p class="help-block"></p>\n' +
            '    </div>' +
            '    <div class="form-group">\n' +
            '        <label class="control-label" for="cashback_percentage_field">' + this.options.text.cashback_percentage + '<em style="color:red">*</em></label>\n' +
            '        <div class="input-group">' +
            '            <input type="number" class="form-control" step="0.001" min="0.001" id="cashback_percentage_field" name="cashback_percentage">\n' +
            '            <span class="input-group-addon">%</span>\n' +
            '        </div>' +
            '        <p class="help-block"></p>\n' +
            '    </div>' +
            '    <div class="form-group">\n' +
            '        <label class="control-label" for="max_cashback_amount_field">' + this.options.text.max_cashback_amount + '</label>\n' +
            '        <input type="number" class="form-control" step="1" min="1" id="max_cashback_amount_field" name="max_cashback_amount">\n' +
            '        <p class="help-block"></p>\n' +
            '    </div>' +
            '</form>';

        var form = $(form_html).appendTo($('.modal-body', modal));

        var rule_id_field = $('[name="rule_id"]', form);
        var type_field = $('[name="type"]', form);
        var type_map_id_field = $('[name="type_map_id"]', form);
        var min_bet_amount_field = $('[name="min_bet_amount"]', form);
        var max_bet_amount_field = $('[name="max_bet_amount"]', form);
        var cashback_percentage_field = $('[name="cashback_percentage"]', form);
        var max_cashback_amount_field = $('[name="max_cashback_amount"]', form);

        var submit_btn = $('.btn-submit', modal);

        rule_id_field.val(rule_id);
        type_field.val(type);
        type_map_id_field.val(type_map_id);
        min_bet_amount_field.val((min_bet_amount) ? min_bet_amount : '');
        max_bet_amount_field.val((max_bet_amount) ? max_bet_amount : '');
        cashback_percentage_field.val((cashback_percentage) ? cashback_percentage : '');
        max_cashback_amount_field.val((max_cashback_amount) ? max_cashback_amount : '');

        form.on('keydown', '[type=number]', function(e) {
            // Allow: backspace, delete, tab, escape, enter and .
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                // Allow: Ctrl+A
                (e.keyCode == 65 && e.ctrlKey === true) ||
                // Allow: home, end, left, right, down, up
                (e.keyCode >= 35 && e.keyCode <= 40)) {
                // let it happen, don't do anything
                return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });

        min_bet_amount_field.on('change', function(){
            if($(this).val().length){
                $(this).val(parseFloat($(this).val()).toFixed(0));
            }
        });

        max_bet_amount_field.on('change', function(){
            if($(this).val().length){
                $(this).val(parseFloat($(this).val()).toFixed(0));
            }
        });

        cashback_percentage_field.on('change', function(){
            if($(this).val().length){
                $(this).val(parseFloat($(this).val()).toFixed(3));
            }
        });

        max_cashback_amount_field.on('change', function(){
            if($(this).val().length){
                $(this).val(parseFloat($(this).val()).toFixed(0));
            }
        });

        submit_btn.on('click', function(){
            var data = form.serializeArray().reduce(function(obj, item) {
                obj[item.name] = item.value;
                return obj;
            }, {});

            // clear class
            form.find('.has-success, .has-error').each(function(){
                $(this).removeClass('has-success has-error');
            });
            form.find('.help-block').each(function(){
                $(this).empty();
            });

            var min_bet_amount = min_bet_amount_field.val();
            if(min_bet_amount.length && isNaN(parseFloat(min_bet_amount))){
                min_bet_amount_field.closest('.form-group').addClass('has-error').find('.help-block').html(self.options.text.min_bet_amount_format_error);
            }

            var max_bet_amount = max_bet_amount_field.val();
            if(max_bet_amount.length && isNaN(parseFloat(max_bet_amount))){
                max_bet_amount_field.closest('.form-group').addClass('has-error').find('.help-block').html(self.options.text.max_bet_amount_format_error);
            }

            var cashback_percentage = cashback_percentage_field.val();
            if(cashback_percentage.length <= 0){
                cashback_percentage_field.closest('.form-group').addClass('has-error').find('.help-block').html(self.options.text.cashback_percentage_is_required);
            }else{
                if(isNaN(parseFloat(cashback_percentage))){
                    cashback_percentage_field.closest('.form-group').addClass('has-error').find('.help-block').html(self.options.text.cashback_percentage_format_error);
                }
            }

            var max_cashback_amount = max_cashback_amount_field.val();
            if(max_cashback_amount.length && isNaN(parseFloat(max_cashback_amount))){
                max_cashback_amount_field.closest('.form-group').addClass('has-error').find('.help-block').html(self.options.text.max_cashback_amount_format_error);
            }

            if(
                (min_bet_amount.length && !isNaN(parseFloat(min_bet_amount))) &&
                (max_bet_amount.length && !isNaN(parseFloat(max_bet_amount))) &&
                (parseFloat(min_bet_amount) > parseFloat(max_bet_amount))
            ){
                max_bet_amount_field.closest('.form-group').addClass('has-error').find('.help-block').html(self.options.text.max_bet_ammount_must_be_greater_than_min_bet_amount);
            }

            if($('.has-error', form).length){
                $('.has-error input:first', form).focus();
            }else{
                modal.trigger($.Event('valid.t1t.cashback.multiple-range.rules.form', {}), data);
            }
        });

        modal.off('hide.bs.modal').on('hide.bs.modal', function(){
            modal.remove();
        });

        return modal;
    };

    Common_Cashback_Multiple_Range_Rules.prototype.showUnknownError = function(callback){
        var self = this;
        BootstrapDialog.show({
            "title": self.options.text.common_cashback_settings_of_multiple_range,
            "message": self.options.text.unknown_error,
            "onhide": function(dialog){
                if(typeof callback === "function"){
                    callback();
                }else{
                    window.location.reload(true)
                }
            }
        });
    };

    window['Common_Cashback_Multiple_Range_Rules'] = new Common_Cashback_Multiple_Range_Rules();
})();
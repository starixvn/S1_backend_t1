(function(){
    var CONFIRM_TRANSFER = true;
    var CANCEL_TRANSFER = false;

    var UI_TEMPLATE = {};
    UI_TEMPLATE['wallet_item_main_wallet'] = '<div class="wallet-item main_wallet_item"><div class="wallet-item-row"><div class="wallet_name"><span class="wallet_name_text"></span></div><div class="wallet_balance"></div><div class="wallet_action"></div></div></div>';
    UI_TEMPLATE['wallet_item_wallet_total'] = '<div class="wallet-item wallet_total_item"><div class="wallet-item-row"><div class="wallet_name"><span class="wallet_name_text"></span></div><div class="wallet_balance"></div><div class="wallet_action"></div></div></div>';
    UI_TEMPLATE['wallet_item_pending_balance'] = '<div class="wallet-item pending_balance_item"><div class="wallet-item-row"><div class="wallet_name"><span class="wallet_name_text"></span></div><div class="wallet_balance"></div><div class="wallet_action"></div></div></div>';
    UI_TEMPLATE['wallet_item_total_balance'] = '<div class="wallet-item total_balance_item"><div class="wallet-item-row"><div class="wallet_name"><span class="wallet_name_text"></span></div><div class="wallet_balance"></div><div class="wallet_action"></div></div></div>';
    UI_TEMPLATE['subwallet_container'] = '<fieldset class="subwallet_container"><legend><span class="header_subwallet_name_text"></span></legend><div class="subwallet_list_container"></div></fieldset>';
    UI_TEMPLATE['subwallet_item'] = '<div class="wallet-item"><div class="wallet-item-row"><div class="wallet_name"><span class="wallet_name_text"></span></div><div class="wallet_balance"></div><div class="wallet_action"><button class="wallet_transfer"><span class="glyphicon glyphicon-transfer"></span></button></div></div></div>';
    UI_TEMPLATE['transfer_wallet_modal'] = '<div class="t1t-ui modal _player_transfer_wallet_modal"><div class="modal-dialog"><div class="modal-content"><div class="modal-heading"><h4 class="modal-title"></h4><button type="button" class="close" data-dismiss="t1t-ui-modal">&times;</button></div><div class="modal-body"></div></div></div></div>';
    UI_TEMPLATE['transfer_wallet_info'] = '<div class="player_transfer_wallet_info_header">\n' +
        '<div class="info_item_container main_total_item"><div class="info_item"><span class="info_item_header_text"></span><div class="wallet_balance"></div></div></div>\n' +
        '<div class="info_item_container game_total_item"><div class="info_item"><span class="info_item_header_text"></span><div class="wallet_balance"></div></div></div>\n' +
        '<div class="info_item_container wallet_total_item"><div class="info_item"><span class="info_item_header_text"></span><div class="wallet_balance"></div></div></div>\n' +
        '<div class="info_item_container transfer_wallet_actions"><div class="action_item"><button type="button" class="btn btn-primary btn-refresh-balance"></button></div><div class="action_item"><button type="button" class="btn btn-primary btn-transfer-all-to-main"></button></div></div>\n' +
        '</div>';
    UI_TEMPLATE['transfer_wallet_body'] = '<div class="transfer_wallet_body">' +
        '  <ul class="nav nav-tabs nav-justified">' +
        '    <li class="nav-item"><a data-toggle="t1t-ui-tab" class="nav-link" href="javascript: void(0);" data-target=".transfer_wallet_body .player_transfer_modal_quick_mode" data-mode="quick"></a></li>' +
        '    <li class="nav-item"><a data-toggle="t1t-ui-tab" class="nav-link" href="javascript: void(0);" data-target=".transfer_wallet_body .player_transfer_modal_pro_mode" data-mode="pro"></a></li>' +
        '  </ul>' +
        '  <div class="tab-content">\n' +
        '    <div class="tab-pane player_transfer_modal_quick_mode">\n' +
        '      <form name="transfer_wallet_quick_mode_form" action="javascript: void(0);" novalidate>\n' +
        '        <div class="transfer_help_block_container">\n' +
        '        </div>\n' +
        '        <div class="form-group-container">\n' +
        '          <div class="form-group">\n' +
        '            <select name="transfer_from" class="form-control transfer_from">\n' +
        '            </select>\n' +
        '          </div>\n' +
        '          <div class="form-group">\n' +
        '            <select name="transfer_to" class="form-control transfer_to">\n' +
        '            </select>\n' +
        '          </div>\n' +
        '          <div class="form-group">\n' +
        '            <input type="number" step="0.01" min="0" name="amount" class="form-control amount_only transfer_amount">\n' +
        '          </div>\n' +
        '          <div class="form-group">\n' +
        '            <button type="button" class="btn btn-primary transfer_button"></button>\n' +
        '          </div>\n' +
        '          <div class="form-group">\n' +
        '            <button type="button" class="btn btn-primary transfer_all_button"></button>\n' +
        '          </div>\n' +
        '        </div>\n' +
        '      </form>\n' +
        '    </div>\n' +
        '    <div class="tab-pane player_transfer_modal_pro_mode">\n' +
        '      <div class="subwallet_container"></div>' +
        '    </div>' +
        '  </div>' +
        '</div>';
    UI_TEMPLATE['transfer_pro_mode_subwallet'] = '<div class="subwallet_entry_container">\n' +
        '  <div class="subwallet_entry">\n' +
        '      <div class="subwallet_entry_field game_name"><span class="game_name_text"></span></div>\n' +
        '      <div class="subwallet_entry_field game_balance"></div>\n' +
        '      <div class="subwallet_entry_field game_transfer_balance_field">\n' +
        '        <input class="form-control amount number_only" type="number">' +
        '      </div>\n' +
        '      <div class="subwallet_entry_field game_actions">\n' +
        '        <button class="btn btn-primary reset"></button>\n' +
        '        <button class="btn btn-primary game_transfer_all_in"></button>\n' +
        '        <button class="btn btn-primary game_transfer_all_out"></button>\n' +
        '      </div>\n' +
        '    </div>\n' +
        '    <div class="subwallet_note"></div>\n' +
        '</div>\n';
    UI_TEMPLATE['transfer_help_block'] = '<div class="transfer_help_block">\n' +
    '  <div class="transfer_min_hint"><span class="transfer_min_hint_text"></span><span class="transfer_min_amount"></span></div>' +
    '  <div class="transfer_max_hint"><span class="transfer_max_hint_text"></span><span class="transfer_max_amount"></span></div>' +
    '  <div class="transfer_allow_float"><span class="transfer_allow_float_text"></span><span class="transfer_allow_float_icon"></span></div>' +
    '  <div class="transfer_input_invalid"><span class="transfer_input_invalid_text"></span></div>' +
    '</div>';

    function T1T_PlayerWallet(){
        this.name = 'player_wallet';

        this.hack_class = {
            "refresh_button_legacy": "_player_balance",
            "refresh_button": "_player_balance_refresh",
            "wallet_list_container": "_player_wallet_list",
            "wallet_list_toggle": "_player_wallet_list_toggle",
            "wallet_transfer_window_toggle": "_player_wallet_transfer_window_toggle"
        };

        this.default_transfer_limit = {
            "transfer_min_limit": 0.01,
            "transfer_min_limit_value": 0.01,
            "transfer_max_limit": "unlimited",
            "transfer_max_limit_value": "unlimited",
            "amount_step": 0.01
        };
    }

    T1T_PlayerWallet.prototype.init = function(){
        var self = this;

        this.template = UI_TEMPLATE;

        // init hack class
        this.refresh_button = $('.' + this.hack_class.refresh_button_legacy + ', .' + this.hack_class.refresh_button);
        this.wallet_list_container = $('.' + this.hack_class.wallet_list_container);
        this.wallet_list_toggle = $('.' + this.hack_class.wallet_list_toggle);

        this.game_transfer_limit = (variables.hasOwnProperty('player_wallet') && variables['player_wallet'].hasOwnProperty('game_transfer_limit')) ? variables['player_wallet']['game_transfer_limit'] : {};
        this.game_with_fixed_currency = (variables.hasOwnProperty('player_wallet') && variables['player_wallet'].hasOwnProperty('game_with_fixed_currency')) ? variables['player_wallet']['game_with_fixed_currency'] : {};

        // auto refresh balance
        if (variables.ui.enabled_refresh_balance_on_player) {
            this.autoRefreshPlayerBalance();
        }

        var last_time = (new Date()).getTime();

        utils.isInActiveWindow(function(_sbe_window_status){
            if(_sbe_window_status == 'visible'){
                utils.safelog('actived, refresh balance');

                var current_time = (new Date()).getTime();
                if(current_time - last_time > variables.auto_refresh_cold_down_time_milliseconds){
                    player_wallet.autoRefreshPlayerBalance();
                    last_time = (new Date()).getTime();
                }else{
                    utils.safelog('active too fast, current_time:' + current_time + ', last_time:' + last_time);
                }
            }
        });
    };

    T1T_PlayerWallet.prototype.autoRefreshPlayerBalance = function(){
        var self = this;

        self.stopAutoRefreshPlayerBalance();
        self.refreshPlayerBalance(function(){
            self.stopAutoRefreshPlayerBalance();
            self.autoRefreshPlayerBalanceTimer = setTimeout(function(){
                self.autoRefreshPlayerBalance();
            }, variables.refresh_balance_interval_millisecond);
        }, true);
    };

    T1T_PlayerWallet.prototype.stopAutoRefreshPlayerBalance = function(){
        if(this.hasOwnProperty('autoRefreshPlayerBalanceTimer') && !!this.autoRefreshPlayerBalanceTimer){
            clearTimeout(this.autoRefreshPlayerBalanceTimer);
        }
    };

    T1T_PlayerWallet.prototype.manuallyRefreshPlayerBalance = function(callback) {
        this.autoRefreshPlayerBalance(callback);
    };

    T1T_PlayerWallet.prototype.refreshPlayerBalance = function(callback, ignore_0) {
        var self = this;

        if(!utils.isInActiveWindow()){
            utils.safelog('not active: ' + _sbe_window_status);
            return;
        }

        ignore_0 = (ignore_0 === undefined) ? true : !!ignore_0;

        $.ajax({
            url: utils.getApiUrl('available_subwallet_list/' + ((ignore_0) ? 'true' : 'false')),
            type: 'GET',
            dataType: 'jsonp',
            cache: false,
            success: function(result){
                // utils.safelog(result);
                if(result['status'] !== "success"){
                    if(typeof callback === "function") callback();
                    return;
                }

                var data = result['data'];

                if(data.hasOwnProperty('refresh_subwallet_list') && data['refresh_subwallet_list'].length > 0){
                    var refresh_subwallet_list = data['refresh_subwallet_list'];
                    self.getSubWalletData(refresh_subwallet_list, function(){
                        if(typeof callback === "function") callback();
                        self.afterBalance(); //refresh wallet done
                    });
                }else{
                    self.afterBalance((data.hasOwnProperty('walletInfo')) ? data['walletInfo'] : null);
                    if(typeof callback === "function") callback();
                }
            }
        });
    };

    T1T_PlayerWallet.prototype.getSubWalletData = function(walletList, callback){
        var self = this;

        if(walletList.length <= 0){
            if(typeof callback === "function") callback();
            return;
        }

        var game_platform_id = walletList[0];
        $.ajax({
            url: utils.getApiUrl('player_query_all_balance/' + game_platform_id + '/true'),
            type: 'GET',
            dataType: 'jsonp',
            cache: false,
            success: function(subwallet){
                walletList.shift();
                self.afterBalance(subwallet);
                if(walletList.length > 0){
                    self.getSubWalletData(walletList, callback);
                }else{
                    if(typeof callback === "function") callback();
                }
            }
        });
    };

    T1T_PlayerWallet.prototype.afterBalance = function(jsonData) {
        this.updateWalletInfo(jsonData);

        this.renderWalletInfo();

        if (variables.disable_account_transfer_when_balance_check_fails > 0) {
            this.transferLockReview(jsonData);
        }
    };

    T1T_PlayerWallet.prototype.isSupportWalletAdvanced = function(){
        return (this.wallet_list_container.length > 0);
    };

    T1T_PlayerWallet.prototype.initUI = function(){
        var self = this;

        self.refresh_button.click(function() {
            self.manuallyRefreshPlayerBalance();
        });

        $(document).on('click', '.' + this.hack_class.wallet_transfer_window_toggle, function(){
            self.showTransferWalletModal();
        });

        if(self.isSupportWalletAdvanced()){
            self.wallet_list_container.addClass('t1t-ui dropdown');
            self.wallet_list_container.on('mouseenter', function(){
                if(self.wallet_list_container.hasClass('show') || self.wallet_list_container.hasClass('open')){
                    return;
                }
                self.wallet_list_toggle.trigger('click');
            });
            self.wallet_list_container.on('mouseleave', function(){
                if(!self.wallet_list_container.hasClass('show') && !self.wallet_list_container.hasClass('open')){
                    return;
                }
                setTimeout(function(){
                    self.wallet_list_toggle.trigger('click');
                }, 250); // Avoid margin value too big.
            });
            self.wallet_list_toggle.addClass('dropdown-toggle').attr('id', self.hack_class.wallet_list_toggle).attr('data-toggle', 't1t-ui-dropdown');

            this.renderWalletList();

            utils.events.on('updated.t1t.player_wallet', function(){
                self.renderWalletList();
            });
        }
    };

    T1T_PlayerWallet.prototype.renderWalletInfo = function(){
        if ($('._player_balance_span').length) {
            if ($('._player_balance_span').hasClass('nofrozen')) {
                $('._player_balance_span').html(utils.displayCurrency(variables.walletInfo.total_balance.balance));
            } else {
                $('._player_balance_span').html(utils.displayCurrency(variables.walletInfo.total_withfrozen));
            }
        } else {
            if ($('._player_balance').hasClass('nofrozen')) {
                $('._player_balance').html(utils.displayCurrency(variables.walletInfo.total_balance.balance));
            } else {
                $('._player_balance').html(utils.displayCurrency(variables.walletInfo.total_withfrozen));
            }
        }
    };

    T1T_PlayerWallet.prototype.updateWalletInfo = function(jsonData){
        var self = this;
        var wallet_info = variables.walletInfo;

        var trigger_event_args;
        if(!!jsonData){
            if(jsonData.hasOwnProperty('api_id') && jsonData.hasOwnProperty('success') && jsonData.success){ // sub wallet result
                var totalbal = utils.parseFloat(jsonData.mainwallet) + utils.parseFloat(jsonData.frozen);
                var nofrozenTotal = utils.parseFloat(jsonData.mainwallet);
                var gameTotalbal = 0;

                if(wallet_info.hasOwnProperty('main_wallet')){
                    wallet_info.main_wallet['balance'] = nofrozenTotal;
                }

                $.each(jsonData.subwallets, function(sub_wallet_id, subwallet_balance) {
                    subwallet_balance = utils.parseFloat(subwallet_balance);
                    totalbal = totalbal + subwallet_balance;
                    nofrozenTotal = nofrozenTotal + subwallet_balance;
                    gameTotalbal = gameTotalbal + subwallet_balance;

                    if(wallet_info.hasOwnProperty('sub_wallets')){
                        $.each(wallet_info.sub_wallets, function(key, subwallet){
                            if(sub_wallet_id == subwallet['sub_wallet_id']){
                                subwallet['balance'] = subwallet_balance;
                            }

                            wallet_info.sub_wallets[key] = subwallet;
                        });
                    }
                });

                if(wallet_info.hasOwnProperty('total_balance')){
                    wallet_info.total_balance['balance'] = nofrozenTotal;
                }

                wallet_info.total_withfrozen = totalbal;
                wallet_info.game_total = gameTotalbal;

                trigger_event_args = [$.Event("updated.t1t.player_wallet.subwallet", {}), wallet_info, jsonData];
            }else if(jsonData.hasOwnProperty('main_wallet')){ // simple wallet formate
                wallet_info = jsonData;

                trigger_event_args = [$.Event("updated.t1t.player_wallet", {}), wallet_info];
            }
        }else{
            trigger_event_args = [$.Event("updated.t1t.player_wallet", {}), wallet_info];
        }

        // add transfer limit
        if(wallet_info.hasOwnProperty('sub_wallets')){
            $.each(wallet_info.sub_wallets, function(key, subwallet){
                subwallet['transfer_limit'] = (self.game_transfer_limit.hasOwnProperty(subwallet['sub_wallet_id'])) ? self.game_transfer_limit[subwallet['sub_wallet_id']] : this.default_transfer_limit;

                wallet_info.sub_wallets[key] = subwallet;
            });
        }

        variables.walletInfo = wallet_info;

        utils.safelog("wallet data updated", trigger_event_args);
        utils.events.trigger.apply(utils.events, trigger_event_args);

        return wallet_info;
    };

    T1T_PlayerWallet.prototype.getWalletInfo = function(){
        return variables.walletInfo;
    };

    // OGP-1093
    // @TODO
    T1T_PlayerWallet.prototype.transferLockReview = function (resp) {
        var any_type_success = false ,success = resp.success;
        $('div.subwallet').each( function () {
            var typeid = $(this).data('typeid');
            var sel_option = 'option[value="' + typeid + '"]';
            var transfer_from = $('div#quickTransfer #transfer_from');
            var transfer_to = $('div#quickTransfer #transfer_to');
            var sel_from = $(transfer_from).find(sel_option);
            var sel_to = $(transfer_to).find(sel_option);

            if (success[typeid] != true) {
                // disable transfer button
                $(this).find('.transfer-fund-btn').addClass('disabled');

                // disable quick transfer select option
                $(sel_from).attr('disabled', 1);
                $(sel_to).attr('disabled', 1);

                // Reset select if selected this typeid
                if ($(transfer_from).val() == typeid) { $(transfer_from).val(''); }
                if ($(transfer_to).val() == typeid) { $(transfer_to).val(''); }
            }
            else {
                // Enable subwallet transfer button
                $(this).find('.transfer-fund-btn').removeClass('disabled').removeAttr('disabled');

                // Enable quickTransfer from/to option
                $(sel_from).removeAttr('disabled');
                $(sel_to).removeAttr('disabled');

                any_type_success = true;
            }
        });

        var subwallet_total = 0.0;
        for (var i in resp.subwallets) {
            subwallet_total = subwallet_total + utils.parseFloat(resp.subwallets[i]);
        }

        // Enable/disable transferAll button
        if (!any_type_success || resp.subWallet_total <= 0) {
            $('#transferAllToMainBtn').addClass('disabled');
        }
        else {
            $('#transferAllToMainBtn').removeClass('disabled');
        }
    }; // End of transferLockReview

    T1T_PlayerWallet.prototype.renderWalletList = function(){
        var self = this;

        var wallet_info = variables.walletInfo;

        var wallet_item_list_container = null;
        if($('.dropdown-menu', self.wallet_list_container).length){
            wallet_item_list_container = $('.dropdown-menu', self.wallet_list_container);
            wallet_item_list_container.empty();
        }else{
            wallet_item_list_container = $('<div>').appendTo(self.wallet_list_container);
            wallet_item_list_container.addClass('dropdown-menu').attr('aria-labelledby', self.hack_class.wallet_list_toggle);
        }

        wallet_item_list_container.on('click doubleclick', function(event){
            event.preventDefault();
            event.stopPropagation();
        });

        // for designer.
        $('<div class="pointer"><i></i></div>').appendTo(wallet_item_list_container);

        // main wallet
        var main_wallet_item = $(self.template.wallet_item_main_wallet).addClass('wallet_id_0').appendTo(wallet_item_list_container);
        $('.wallet_name_text', main_wallet_item).html(wallet_info.main_wallet.language);
        $('.wallet_balance', main_wallet_item).html(utils.displayCurrency(wallet_info.main_wallet.balance));

        var subwallet_container = $(self.template.subwallet_container).appendTo(wallet_item_list_container);
        $('.header_subwallet_name_text', subwallet_container).html(variables.langText.subwallet);

        var subwallet_list_container = $('.subwallet_list_container', subwallet_container);
        $.each(wallet_info.sub_wallets, function(index, subwallet){
            var subwallet_entry = $(self.template.subwallet_item).addClass('sub_wallet_id_' + subwallet['sub_wallet_id']).appendTo(subwallet_list_container);
            $('.wallet_name_text', subwallet_entry).html(subwallet['sub_wallet']);
            $('.wallet_balance', subwallet_entry).html(utils.displayCurrency(subwallet['balance']));
            $('.wallet_transfer', subwallet_entry).attr('data-subwallet-id', subwallet['sub_wallet_id']);

            $('.wallet_transfer', subwallet_entry).off('click').on('click', function(){
                self.wallet_list_toggle.trigger('click');

                if(wallet_info.main_wallet.balance < subwallet['balance']){
                    self.showTransferWalletModal(subwallet['sub_wallet_id'], variables.main_wallet_id, subwallet['balance']);
                }else{
                    self.showTransferWalletModal(variables.main_wallet_id, subwallet['sub_wallet_id'], wallet_info.main_wallet.balance);
                }
            });
        });

        // wallet total
        var wallet_total_item = $(self.template.wallet_item_wallet_total).appendTo(wallet_item_list_container);
        $('.wallet_name_text', wallet_total_item).html(variables.langText.Wallet_Total);
        $('.wallet_balance', wallet_total_item).html(utils.displayCurrency(wallet_info.total_balance.balance));

        // pending withdraw balance
        var pending_balance_item = $(self.template.wallet_item_pending_balance).appendTo(wallet_item_list_container);
        $('.wallet_name_text', pending_balance_item).html(variables.langText.Pending_Balance);
        $('.wallet_balance', pending_balance_item).html(utils.displayCurrency(wallet_info.main_wallet.frozen));

        // wallet total
        var total_balance_item = $(self.template.wallet_item_total_balance).appendTo(wallet_item_list_container);
        $('.wallet_name_text', total_balance_item).html(wallet_info.total_balance.language);
        $('.wallet_balance', total_balance_item).html(utils.displayCurrency(wallet_info.total_withfrozen));
    };

    T1T_PlayerWallet.prototype.showTransferWalletModal = function(from_wallet_id, to_wallet_id, balance){
        var self = this;
        var modal = this.createTransferModal();

        var modal_body = $('.modal-body', modal);

        var refresh_page = false;

        modal_body.on('success.t1t.player_wallet.transfer', function(){
            self.renderTransferBody(modal_body);

            modal.trigger('show.t1t.ui.modal');

            refresh_page = true;
        });

        this.renderTransferBody(modal_body);

        $('.transfer_from', modal_body).val(from_wallet_id).trigger('change');
        $('.transfer_to', modal_body).val(to_wallet_id).trigger('change');
        $('.transfer_amount', modal_body).val(balance).trigger('change');

        modal.on('show.t1t.ui.modal', function(){
            $('[data-toggle="t1t-ui-tab"]', modal).on('click', function(){
                var mode = $(this).data('mode');

                modal.removeClass('mode_quick').removeClass('mode_pro');
                modal.addClass('mode_' + mode);

                $(this).tab('show');
            });

            $('[data-toggle="t1t-ui-tab"]:first', modal).tab('show').trigger('click');
        });

        modal.on('hidden.t1t.ui.modal', function(){
            modal.remove();
        });

        modal.modal('show');
    };

    T1T_PlayerWallet.prototype.createTransferModal = function(){
        var modal = $('._player_transfer_wallet_modal');
        if(modal.length <= 0){
            modal = $(this.template.transfer_wallet_modal);
            modal.appendTo($('body'));

            $('.modal-title', modal).html(variables.langText.transfer);

            if(utils.is_mobile()){
                modal.addClass('mobile');
            }
        }

        return modal;
    };

    T1T_PlayerWallet.prototype.getSubWalletTransferLimit = function(subwallet_data){
        var wallet_info = variables.walletInfo;

        var subwallet_transfer_limit = (this.game_transfer_limit.hasOwnProperty(subwallet_data['sub_wallet_id'])) ? this.game_transfer_limit[subwallet_data['sub_wallet_id']] : this.default_transfer_limit;

        subwallet_transfer_limit['amount_step'] = (subwallet_transfer_limit['amount_step'] <= 0) ? 0 : subwallet_transfer_limit['amount_step'];

        var transfer_min_limit = subwallet_transfer_limit['transfer_min_limit'];
        transfer_min_limit = (transfer_min_limit < subwallet_transfer_limit['amount_step']) ? subwallet_transfer_limit['amount_step'] : transfer_min_limit;
        subwallet_transfer_limit['transfer_min_limit_value'] = transfer_min_limit;

        var transfer_max_limit = subwallet_transfer_limit['transfer_max_limit'];
        transfer_max_limit = (transfer_max_limit === 'unlimited') ? wallet_info.total_balance.balance : transfer_max_limit;
        subwallet_transfer_limit['transfer_max_limit_value'] = transfer_max_limit;

        return subwallet_transfer_limit;
    };

    T1T_PlayerWallet.prototype.renderTransferBody = function(transfer_body){
        var self = this;
        var wallet_info = variables.walletInfo;

        transfer_body.empty();

        var transfer_wallet_info = $(self.template.transfer_wallet_info);
        $('button.btn-refresh-balance', transfer_wallet_info).html(lang('lang.refreshbalance'));
        $('button.btn-transfer-all-to-main', transfer_wallet_info).html(lang('Transfer Back All'));

        transfer_wallet_info.appendTo(transfer_body);
        $('.main_total_item .info_item_header_text', transfer_wallet_info).html(variables.langText.Main_Wallet_Total);
        $('.main_total_item .wallet_balance', transfer_wallet_info).html(utils.displayCurrency(wallet_info.main_wallet.balance));

        $('.game_total_item .info_item_header_text', transfer_wallet_info).html(variables.langText.Game_Wallet_Total);
        $('.game_total_item .wallet_balance', transfer_wallet_info).html(utils.displayCurrency(wallet_info.game_total));

        $('.pending_balance_item .info_item_header_text', transfer_wallet_info).html(variables.langText.Pending_Balance);
        $('.pending_balance_item .wallet_balance', transfer_wallet_info).html(utils.displayCurrency(wallet_info.main_wallet.frozen));

        $('.total_balance_item .info_item_header_text', transfer_wallet_info).html(variables.langText.Total_Balance);
        $('.total_balance_item .wallet_balance', transfer_wallet_info).html(utils.displayCurrency(wallet_info.total_withfrozen));

        $('.wallet_total_item .info_item_header_text', transfer_wallet_info).html(variables.langText.Wallet_Total);
        $('.wallet_total_item .wallet_balance', transfer_wallet_info).html(utils.displayCurrency(wallet_info.total_balance.balance));

        var transfer_wallet_body = $(self.template.transfer_wallet_body).appendTo(transfer_body);
        $('[data-mode="quick"]', transfer_wallet_body).html(lang('Quick Transfer Mode'));
        $('[data-mode="pro"]', transfer_wallet_body).html(lang('Pro Transfer Mode'));
        $('button.transfer_button', transfer_wallet_body).html(lang('Transfer'));
        $('button.transfer_all_button', transfer_wallet_body).html(lang('Transfer All'));

        this.renderTransferQuickModeForm(transfer_body, transfer_wallet_body);
        this.renderTransferProModeForm(transfer_body, transfer_wallet_body);

        $('button.btn-refresh-balance', transfer_wallet_info).off('click').on('click', function(){
            Loader.show('.transfer_wallet_body');

            var current_active_page = $('[data-toggle="t1t-ui-tab"].active', transfer_body);

            self.refreshPlayerBalance(function(){
                Loader.hide();

                var e = $.Event('success.t1t.player_wallet.transfer');
                transfer_body.trigger(e);
                utils.events.trigger(e);

                current_active_page.tab('show').trigger('click');
            });
        });

        $('button.btn-transfer-all-to-main', transfer_wallet_info).off('click').on('click', function(){
            Loader.show('.transfer_wallet_body');

            var current_active_page = $('[data-toggle="t1t-ui-tab"].active', transfer_body);

            self.transferAllBalance(variables.main_wallet_id, function(response){
                Loader.hide();

                if(!response){
                    return false;
                }

                if(response.status === 'success'){
                    self.updateWalletInfo(response.data.walletInfo);

                    var e = $.Event('success.t1t.player_wallet.transfer');
                    transfer_body.trigger(e);
                    utils.events.trigger(e);

                    current_active_page.tab('show').trigger('click');
                }
            });
        });

        return self;
    };

    T1T_PlayerWallet.prototype.renderTransferQuickModeForm = function(transfer_body, transfer_wallet_body){
        var self = this;
        var wallet_info = variables.walletInfo;

        var form_container = $('.player_transfer_modal_quick_mode>form', transfer_wallet_body);

        var transfer_from = $('.transfer_from', form_container);
        var transfer_to = $('.transfer_to', form_container);
        var transfer_amount = $('.transfer_amount', form_container);

        transfer_from.empty();
        transfer_to.empty();
        transfer_amount.val('');

        $('.transfer_help_block_container', transfer_wallet_body).html($(this.template.transfer_help_block));

        $('.transfer_help_block_container span.transfer_min_hint_text', transfer_wallet_body).html(lang('minimal_transfer_amount_label'));
        $('.transfer_help_block_container span.transfer_max_hint_text', transfer_wallet_body).html(lang('maxmal_transfer_amount_label'));
        $('.transfer_help_block_container span.transfer_allow_float_text', transfer_wallet_body).html(lang('whether_to_allow_decimals_to_transfer_label'));

        var empty_from_option = $('<option value="">').text(lang('Select game to transfer money from'));
        empty_from_option.attr({
            'data-min': 0,
            'data-max': wallet_info.total_balance.balance,
            'data-step': this.default_transfer_limit['amount_step'],
            'data-balance': 0
        });
        transfer_from.append(empty_from_option);
        var empty_to_option = $('<option value="">').text(lang('Select game to transfer money to'));
        empty_to_option.attr({
            'data-min': 0,
            'data-max': wallet_info.total_balance.balance,
            'data-step': this.default_transfer_limit['amount_step'],
            'data-balance': 0
        });
        transfer_to.append(empty_to_option);

        var option = $('<option value="">').text(wallet_info.main_wallet.language);
        option.attr('value', variables.main_wallet_id);
        option.attr('data-min', this.default_transfer_limit['transfer_min_limit_value']);
        option.attr('data-max', wallet_info.total_balance.balance);
        option.attr('data-step', this.default_transfer_limit['amount_step']);
        option.attr('data-balance', wallet_info.main_wallet.balance);

        transfer_from.append($(option[0].outerHTML));
        transfer_to.append($(option[0].outerHTML));

        $.each(wallet_info.sub_wallets, function(index, subwallet){
            var subwallet_transfer_limit = self.getSubWalletTransferLimit(subwallet);

            var option = $('<option value="">').text(subwallet.sub_wallet);
            option.attr('value', subwallet['sub_wallet_id']);
            option.attr('data-min', subwallet_transfer_limit['transfer_min_limit_value']);
            option.attr('data-max', subwallet_transfer_limit['transfer_max_limit_value']);
            option.attr('data-step', subwallet_transfer_limit['amount_step']);
            option.attr('data-balance', subwallet['balance']);

            transfer_from.append($(option[0].outerHTML));
            transfer_to.append($(option[0].outerHTML));
        });

        var set_transfer_limit = function(){
            var transfer_from_option, transfer_to_option;
            var transfer_from_min_limit, transfer_from_max_limit, transfer_from_step;
            var transfer_to_min_limit, transfer_to_max_limit, transfer_to_step;
            var transfer_min_limit, transfer_max_limit, transfer_step;

            transfer_from_option = $(':selected', transfer_from);
            transfer_to_option = $(':selected', transfer_to);

            transfer_from_step = transfer_from_option.data('step');
            transfer_from_min_limit = transfer_from_option.data('min');
            transfer_from_min_limit = (transfer_from_min_limit == 'unlimited') ? self.default_transfer_limit.amount_step : transfer_from_min_limit;
            transfer_from_max_limit = transfer_from_option.data('max');
            transfer_from_max_limit = (transfer_from_max_limit == 'unlimited') ? transfer_from_option.data('balance') : transfer_from_max_limit;

            transfer_to_step = transfer_to_option.data('step');
            transfer_to_min_limit = transfer_to_option.data('min');
            transfer_to_min_limit = (transfer_to_min_limit == 'unlimited') ? self.default_transfer_limit.transfer_min_limit_value : transfer_to_min_limit;
            transfer_to_max_limit = transfer_to_option.data('max');

            transfer_step = (transfer_from_step > transfer_to_step) ? transfer_from_step : transfer_to_step;

            transfer_min_limit = transfer_from_min_limit;
            transfer_min_limit = (transfer_min_limit > transfer_to_min_limit) ? transfer_min_limit : transfer_to_min_limit;
            transfer_min_limit = (transfer_min_limit > transfer_step) ? transfer_min_limit : transfer_step;

            transfer_max_limit = utils.parseFloat(transfer_from_max_limit);
            // transfer_max_limit = (transfer_max_limit < transfer_to_max_limit) ? transfer_max_limit : transfer_to_max_limit;

            if(transfer_max_limit > utils.parseFloat(transfer_from_option.data('balance'))){
                transfer_max_limit = utils.parseFloat(transfer_from_option.data('balance'));
            }

            if(transfer_max_limit <= 0){
                transfer_max_limit = transfer_from_option.data('balance');
                transfer_min_limit = (transfer_max_limit <= 0) ? 0 : transfer_min_limit;
            }

            $('.transfer_help_block_container .transfer_min_amount', transfer_wallet_body).html(utils.displayCurrency(transfer_min_limit));
            $('.transfer_help_block_container .transfer_max_amount', transfer_wallet_body).html(utils.displayCurrency(transfer_max_limit));

            $('.transfer_help_block_container .transfer_allow_float .transfer_allow_float_icon', transfer_wallet_body).removeClass('allow').removeClass('disallow');
            $('.transfer_help_block_container .transfer_allow_float .transfer_allow_float_icon', transfer_wallet_body).addClass((transfer_step < 1) ? 'allow' : 'disallow');

            transfer_amount.attr('min', transfer_min_limit);
            transfer_amount.attr('max', transfer_max_limit);
            transfer_amount.attr('step', transfer_step);

            if(transfer_amount.val() > transfer_max_limit){
                transfer_amount.val(transfer_max_limit);
            }
        };

        transfer_from.off('change').on('change', function(){
            if(transfer_from.val().toString().length <= 0){
                return false;
            }

            var transfer_from_val = transfer_from.val().toString();
            var transfer_to_val = transfer_to.val().toString();

            if(transfer_from_val === transfer_to_val){
                transfer_to.val('');
            }

            return set_transfer_limit();
        });

        transfer_to.off('change').on('change', function(){
            if(transfer_to.val().toString().length <= 0){
                return false;
            }

            var transfer_from_val = transfer_from.val().toString();
            var transfer_to_val = transfer_to.val().toString();

            if(transfer_from_val === transfer_to_val){
                transfer_from.val('');
            }

            return set_transfer_limit();
        });

        transfer_amount.off('keyup').on('keyup', function(){
            var amount = utils.parseFloat(transfer_amount.val());
            if(amount > utils.parseFloat(transfer_amount.attr('max'))){
                $('.transfer_help_block_container .transfer_input_invalid_text', transfer_wallet_body).html(lang('Transfer Amount Exceeding the max limit'));
            }else if(amount < utils.parseFloat(transfer_amount.attr('min'))){
                $('.transfer_help_block_container .transfer_input_invalid_text', transfer_wallet_body).html(lang('Transfer Amount Below the min limit'));
            }else{
                $('.transfer_help_block_container .transfer_input_invalid_text', transfer_wallet_body).html('');
            }

            $('.btn', form_container).removeClass('disabled').prop('disabled', false);

            if(transfer_amount.is(':valid')){
                $('.btn', form_container).removeClass('disabled').prop('disabled', false);
            }else{
                $('.btn', form_container).addClass('disabled').prop('disabled', true);
            }
        });

        var callback = function(response){
            Loader.hide();

            if(!response){
                return false;
            }

            if(response.status === 'success'){
                self.updateWalletInfo(response.data.walletInfo);

                var e = $.Event('success.t1t.player_wallet.transfer');
                transfer_body.trigger(e);
                utils.events.trigger(e);

                $('[data-toggle="t1t-ui-tab"][data-mode="quick"]', transfer_body).tab('show').trigger('click');
            }
        };

        $('.transfer_button', form_container).off('click').on('click', function(){
            var from_wallet_id = transfer_from.val();
            var to_wallet_id = transfer_to.val();
            var amount = transfer_amount.val();

            Loader.show('.transfer_wallet_body');

            self.transferBalance(from_wallet_id, to_wallet_id, amount, callback);
        });

        $('.transfer_all_button', form_container).off('click').on('click', function(){
            var to_wallet_id = transfer_to.val();

            Loader.show('.transfer_wallet_body');

            self.transferAllBalance(to_wallet_id, callback);
        })
    };

    T1T_PlayerWallet.prototype.renderTransferProModeForm = function(transfer_body, transfer_wallet_body){
        var self = this;
        var wallet_info = variables.walletInfo;

        var subwallet_container = $('.player_transfer_modal_pro_mode .subwallet_container', transfer_wallet_body);

        $.each(wallet_info.sub_wallets, function(index, subwallet){
            self.renderProModeEntry(transfer_body, subwallet_container, subwallet);
        });
    };

    T1T_PlayerWallet.prototype.renderProModeEntry = function(transfer_body, subwallet_container, subwallet){
        var self = this;
        var wallet_info = variables.walletInfo;

        var subwallet_transfer_limit = self.getSubWalletTransferLimit(subwallet);

        var subwallet_entry = $(self.template.transfer_pro_mode_subwallet).appendTo(subwallet_container);
        subwallet_entry.addClass('sub_wallet_id_' + subwallet['sub_wallet_id']);
        $('button.reset', subwallet_entry).html(lang('Reset'));
        $('button.game_transfer_all_in', subwallet_entry).html(lang('Transfer In'));
        $('button.game_transfer_all_out', subwallet_entry).html(lang('Transfer Out'));
        $('.game_name_text', subwallet_entry).html(subwallet.sub_wallet);
        $('.game_balance', subwallet_entry).html(utils.displayCurrency(subwallet.balance));

        var subwallet_note = $('.subwallet_note', subwallet_entry);
        subwallet_note.append($(self.template.transfer_help_block));
        $('span.transfer_min_hint_text', subwallet_note).html(lang('minimal_transfer_amount_label'));
        $('span.transfer_max_hint_text', subwallet_note).html(lang('maxmal_transfer_amount_label'));
        $('span.transfer_allow_float_text', subwallet_note).html(lang('whether_to_allow_decimals_to_transfer_label'));

        var subwallet_input = $('.game_transfer_balance_field input', subwallet_entry);
        var transfer_input_max;

        transfer_input_max = subwallet.balance;
        transfer_input_max = (transfer_input_max > wallet_info.main_wallet.balance) ? transfer_input_max : wallet_info.main_wallet.balance;
        transfer_input_max = (transfer_input_max <= 0) ? subwallet_transfer_limit['transfer_max_limit_value'] : transfer_input_max;

        subwallet_input.attr('min', subwallet_transfer_limit['transfer_min_limit_value']);
        subwallet_input.attr('max', transfer_input_max);
        subwallet_input.attr('step', subwallet_transfer_limit['amount_step']);

        subwallet_input.on('keyup change', function(){
            $('.game_actions .btn', subwallet_entry).removeClass('disabled').prop('disabled', false);

            if(subwallet_input.is(':valid')){
                $('.game_actions .btn', subwallet_entry).removeClass('disabled').prop('disabled', false);
            }else{
                $('.game_actions .btn', subwallet_entry).addClass('disabled').prop('disabled', true);
            }
        });

        $('.transfer_min_amount', subwallet_note).html(utils.displayCurrency(subwallet_transfer_limit['transfer_min_limit_value']));

        if(subwallet_transfer_limit['transfer_max_limit'] == 'unlimited'){
            $('.transfer_max_amount', subwallet_note).html(lang('Unlimited')).addClass('unlimited');
        }else{
            $('.transfer_max_amount', subwallet_note).removeClass('unlimited');

            $('.transfer_max_amount', subwallet_note).html(utils.displayCurrency(subwallet_transfer_limit['transfer_max_limit_value']));
        }

        $('.transfer_allow_float .transfer_allow_float_icon', subwallet_note).removeClass('allow').removeClass('disallow');
        $('.transfer_allow_float .transfer_allow_float_icon', subwallet_note).addClass((subwallet_transfer_limit['amount_step'] < 1) ? 'allow' : 'disallow');

        $('.reset', subwallet_entry).on('click', function(){
            subwallet_input.val('').val('').trigger('change');
        });

        var callback = function(response){
            Loader.hide();

            if(!response){
                return false;
            }

            if(response.status === 'success'){
                self.updateWalletInfo(response.data.walletInfo);

                var e = $.Event('success.t1t.player_wallet.transfer');
                transfer_body.trigger(e);
                utils.events.trigger(e);

                $('[data-toggle="t1t-ui-tab"][data-mode="pro"]', transfer_body).tab('show').trigger('click');
            }
        };

        $('.game_transfer_all_in', subwallet_entry).on('click', function(){
            Loader.show('.transfer_wallet_body');

            var amount = utils.parseFloat(subwallet_input.val(), 0);

            if(amount){
                self.transferBalance(variables.main_wallet_id, subwallet['sub_wallet_id'], amount, callback);
            }else{
                self.transferAllBalance(subwallet['sub_wallet_id'], callback);
            }
        });

        $('.game_transfer_all_out', subwallet_entry).on('click', function(){
            Loader.show('.transfer_wallet_body');

            var amount = utils.parseFloat(subwallet_input.val(), subwallet['balance']);

            self.transferBalance(subwallet['sub_wallet_id'], variables.main_wallet_id, amount, callback);
        });
    };

    T1T_PlayerWallet.prototype.transferBalance = function(from_wallet_id, to_wallet_id, amount, callback){
        var self = this;
        var wallet_info = variables.walletInfo;

        amount = utils.parseFloat(amount);

        if(!amount || amount <= 0){
            MessageBox.danger(lang('Invalid transfer amount'), null, function(){
                if(typeof callback === "function") callback();
            });

            return false;
        }

        var sub_wallet_data = null;
        if(from_wallet_id.toString() === variables.main_wallet_id.toString()){ // main to sub
            from_wallet_id = variables.main_wallet_id;
            sub_wallet_data = self.getSubWalletInfo(to_wallet_id);

            if(false === sub_wallet_data){
                MessageBox.danger(lang('Invalid product wallet'), null, function(){
                    if(typeof callback === "function") callback();
                });

                return false;
            }

            if(amount > wallet_info.main_wallet.balance || wallet_info.main_wallet.balance <= 0){
                MessageBox.danger(lang('No enough balance'), null, function(){
                    if(typeof callback === "function") callback();
                });

                return false;
            }

            amount = self.getAvailableTransferAmount(to_wallet_id, amount);
        }else if(to_wallet_id.toString() === variables.main_wallet_id.toString()){ // sub to main
            sub_wallet_data = self.getSubWalletInfo(from_wallet_id);
            to_wallet_id = variables.main_wallet_id;

            if(false === sub_wallet_data){
                MessageBox.danger(lang('Invalid product wallet'), null, function(){
                    if(typeof callback === "function") callback();
                });

                return false;
            }

            if(amount > sub_wallet_data.balance || sub_wallet_data.balance <= 0){
                MessageBox.danger(lang('No enough balance'), null, function(){
                    if(typeof callback === "function") callback();
                });

                return false;
            }

            amount = self.getAvailableTransferAmount(from_wallet_id, amount);
        }else{ // sub to sub
            var from_sub_wallet_data = self.getSubWalletInfo(from_wallet_id);
            var to_sub_wallet_data = self.getSubWalletInfo(to_wallet_id);

            if(false === from_sub_wallet_data){
                MessageBox.danger(lang('Invalid product wallet'), null, function(){
                    if(typeof callback === "function") callback();
                });

                return false;
            }

            if(false === to_sub_wallet_data){
                MessageBox.danger(lang('Invalid product wallet'), null, function(){
                    if(typeof callback === "function") callback();
                });

                return false;
            }

            if(amount > from_sub_wallet_data.balance || from_sub_wallet_data.balance <= 0){
                MessageBox.danger(lang('No enough balance'), null, function(){
                    if(typeof callback === "function") callback();
                });

                return false;
            }

            if(!self.game_with_fixed_currency.hasOwnProperty(from_wallet_id)){
                amount = self.getAvailableTransferAmount(from_wallet_id, amount);
            }else{
                amount = self.getAvailableTransferAmount(to_wallet_id, amount);
            }
        }

        if(!amount || amount > wallet_info.total_balance.balance || amount <= 0){
            MessageBox.danger(lang('Invalid transfer amount'), null, function(){
                if(typeof callback === "function") callback();
            });

            return false;
        }

        self.checkGameApiCurrencyRateWithConvertedAmount(from_wallet_id, to_wallet_id, amount, function(do_transfer){
            if(!do_transfer){
                return (typeof callback === "function") ? callback() : null;
            }

            var data = {
                "transfer_from": from_wallet_id,
                "transfer_to": to_wallet_id,
                "amount": amount
            };

            utils.getJSONP(utils.getApiUrl('player_transfer_balance'), data, function(result){
                if(result['status'] === "success"){
                    self.updateWalletInfo(result['data']['walletInfo']);
                }

                return MessageBox.ajax(result, function(){
                    if(typeof callback === "function"){
                        callback(result);
                    }else{
                        Loader.show();
                        window.location.reload(true);
                    }
                });
            }, function(result){
                MessageBox.danger('Unknown Error', null, function(){
                    if(typeof callback === "function"){
                        callback(result);
                    }else{
                        Loader.show();
                        window.location.reload(true);
                    }
                });

                return false;
            });
        });
    };

    T1T_PlayerWallet.prototype.transferAllBalance = function(to_wallet_id, callback){
        var self = this;
        var wallet_info = variables.walletInfo;

        if(to_wallet_id === undefined || to_wallet_id.toString().length <= 0){
            MessageBox.danger(lang('Invalid product wallet'), null, function(){
                if(typeof callback === "function") callback();
            });

            return false;
        }

        if(to_wallet_id.toString() === variables.main_wallet_id.toString()){ // all to main
            if(wallet_info.game_total <= 0){
                MessageBox.danger(lang('No enough balance'), null, function(){
                    if(typeof callback === "function") callback();
                });

                return false;
            }
        }else if (false !== self.getSubWalletInfo(to_wallet_id)){ // all to sub
            if(wallet_info.total_balance.balance <= 0){
                MessageBox.danger(lang('Invalid transfer amount'), null, function(){
                    if(typeof callback === "function") callback();
                });

                return false;
            }
        }else{
            MessageBox.danger(lang('Invalid product wallet'), null, function(){
                if(typeof callback === "function") callback();
            });

            return false;
        }

        self.checkGameApiCurrencyRateWithConvertedAmount(null, to_wallet_id, null, function(do_transfer){
            if(!do_transfer){
                return (typeof callback === "function") ? callback() : null;
            }

            var data = {
                "transfer_to": to_wallet_id
            };
            utils.getJSONP(utils.getApiUrl('player_transfer_all_balance'), data, function(result){
                if(result['status'] === "success"){
                    self.updateWalletInfo(result['data']['walletInfo']);
                }

                return MessageBox.ajax(result, function(){
                    if(typeof callback === "function"){
                        callback(result);
                    }else{
                        Loader.show();
                        window.location.reload(true);
                    }
                });
            }, function(result){
                MessageBox.danger('Unknown Error', null, function(){
                    if(typeof callback === "function"){
                        callback(result);
                    }else{
                        Loader.show();
                        window.location.reload(true);
                    }
                });

                return false;
            });
        });
    };

    T1T_PlayerWallet.prototype.getSubWalletInfo = function(sub_wallet_id){
        if(sub_wallet_id === undefined || !variables.hasOwnProperty('walletInfo') || !variables.walletInfo.hasOwnProperty('sub_wallets')){
            return false;
        }
        var result = false;
        $.each(variables.walletInfo.sub_wallets, function(index, sub_wallet_data){
            if(sub_wallet_data['sub_wallet_id'].toString() == sub_wallet_id.toString()){
                result = sub_wallet_data;
            }
        });

        return result;
    };

    T1T_PlayerWallet.prototype.getAvailableTransferAmount = function(to_wallet_id, amount){
        var self = this;
        var wallet_info = variables.walletInfo;

        sub_wallet_data = self.getSubWalletInfo(to_wallet_id);

        if(false === sub_wallet_data){
            return false;
        }

        amount = (!!amount) ? amount : wallet_info['total_balance']['balance'] - sub_wallet_data['balance'];
        if(amount <= 0){
            return false;
        }

        var amount_segments = amount.toString().split(variables.currency.currency_dec_point);

        var amount_step = sub_wallet_data['transfer_limit']['amount_step'];
        var amount_step_segments = amount_step.toString().split(variables.currency.currency_dec_point);
        if(amount_step_segments.length > 1){
            if(amount_segments.length > 1){
                amount = amount_segments[0] + variables.currency.currency_dec_point + amount_segments[1].substr(0, amount_step_segments[1].length);
            }else{
                amount = amount_segments[0];
            }
        }else{
            amount = amount_segments[0];
        }

        return utils.parseFloat(amount);
    };

    T1T_PlayerWallet.prototype.checkGameApiCurrencyRateWithConvertedAmount = function(from_wallet_id, to_wallet_id, amount, callback){
        var self = this;
        var wallet_info = variables.walletInfo;

        if(to_wallet_id.toString() === variables.main_wallet_id.toString()){ // sub to main
            if(!from_wallet_id){
                return (typeof callback === "function") ? callback(CONFIRM_TRANSFER) : null;
            }

            sub_wallet_data = self.getSubWalletInfo(from_wallet_id);

            if(false === sub_wallet_data){
                return (typeof callback === "function") ? callback(CONFIRM_TRANSFER) : null;
            }

            amount = (!!amount) ? amount : self.getAvailableTransferAmount(from_wallet_id, sub_wallet_data['balance']);

            if(!self.game_with_fixed_currency.hasOwnProperty(from_wallet_id)){
                return (typeof callback === "function") ? callback(CONFIRM_TRANSFER) : null;
            }
        }else{
            amount = (!!amount) ? amount : self.getAvailableTransferAmount(to_wallet_id, null);

            if(!self.game_with_fixed_currency.hasOwnProperty(to_wallet_id)){
                return (typeof callback === "function") ? callback(CONFIRM_TRANSFER) : null;
            }
        }

        if(!amount){
            return (typeof callback === "function") ? callback(CANCEL_TRANSFER) : null;
        }

        Loader.show();
        var data = {
            "transfer_from": from_wallet_id,
            "transfer_to": to_wallet_id,
            "amount": amount
        };
        utils.getJSONP(utils.getApiUrl('checkGameApiCurrencyRateWithConvertedAmount'), data, function(result){
            Loader.hide();
            if(result['status'] === "success"){
                if(result['data']['status']){
                    self.showGameApiCurrencyRateWithConvertedAmount(result['data'], function(modal_anwser){
                        return (typeof callback === "function") ? callback(modal_anwser) : null;
                    });
                }else{
                    return (typeof callback === "function") ? callback(CONFIRM_TRANSFER) : null;
                }
            }else{
                MessageBox.danger('Unknown Error', null, function(){
                    Loader.show();
                    window.location.reload(true);
                });
            }
        }, function(){
            MessageBox.danger('Unknown Error', null, function(){
                Loader.show();
                window.location.reload(true);
            });

            return false;
        });
    };

    T1T_PlayerWallet.prototype.showGameApiCurrencyRateWithConvertedAmount = function(response, callback){
        var modal = $('<div id="widget_modal_currency" class="t1t-ui modal game_converted_currency_modal" role="dialog" data-backdrop="static" data-keyboard="false">\
            <div class="modal-dialog">\
                <div class="modal-content">\
                    <div class="modal-body">\
                        <p>'+response.message+'</p>\
                        <div class="row modal-btn-wrapper">\
                            <a href="javascript:void(0)" data-dismiss="modal" class="modal-confirm-btn">' + lang('Confirm') + '</a>\
                            <a href="javascript:void(0)" data-dismiss="modal" class="modal-cancel-btn">' + lang('Cancel') + '</a>\
                        </div>\
                    </div>\
                </div>\
            </div>\
        </div>').appendTo($('body'));

        $('.modal-confirm-btn', modal).on('click', function(){
            modal.modal('hide');
            return (typeof callback === "function") ? callback(CONFIRM_TRANSFER) : null;
        });

        $('.modal-cancel-btn', modal).on('click', function(){
            modal.modal('hide');
            return (typeof callback === "function") ? callback(CANCEL_TRANSFER) : null;
        });

        modal.on('show.t1t.ui.modal', function(){
        });

        modal.off('shown.t1t.ui.modal').on('shown.t1t.ui.modal', function(){
            var instance = modal.data('t1t.ui.modal');

            $(instance._backdrop).addClass('game_converted_currency_modal');
        });

        modal.on('hidden.t1t.ui.modal', function(){
            modal.remove();
        });

        modal.modal('show');
    };

    var player_wallet = new T1T_PlayerWallet();
    smartbackend.addAddons(player_wallet.name, player_wallet);

    smartbackend.on('logged.t1t.player', function(){
        player_wallet.init();
        player_wallet.initUI();
        player_wallet.renderWalletInfo();
        player_wallet.updateWalletInfo();
    });

    smartbackend.on('success.t1t.player_notify.deposit', function(){
        player_wallet.autoRefreshPlayerBalance();
    });

    smartbackend.on('danger.t1t.player_notify.deposit', function(){
        player_wallet.autoRefreshPlayerBalance();
    });

    renderUI.manuallyRefreshPlayerBalance = $.proxy(player_wallet.manuallyRefreshPlayerBalance, player_wallet);
    renderUI.refreshPlayerBalance = $.proxy(player_wallet.refreshPlayerBalance, player_wallet);

    return player_wallet;
})();
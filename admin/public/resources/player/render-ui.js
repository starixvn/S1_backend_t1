//render JS UI
var renderUI = (function() {

    // utils.safelog(variables);
    var addEventToLogged = function() {
        var self = this;

        var container = self.getLoginContainer();

        container.find('._player_logout').click(function() {
            smartbackend.player.logout();
        });

        var player_center_controller = 'player_center';

        container.find('._player_username').click(function() {
            self.checkLoginStatus();
            window.location = '/' + player_center_controller + '/iframe_viewCashier';
        });
        container.find('._player_information').click(function() {
            self.checkLoginStatus();
            window.location = '/' + player_center_controller + '/iframe_playerSettings';
        });
        container.find('._player_memcashier').click(function() {
            self.checkLoginStatus();
            window.location = '/' + player_center_controller + '/iframe_viewCashier';
        });
        container.find('._player_memcenter').click(function() {
            self.checkLoginStatus();
            window.location = '/' + player_center_controller + '/iframe_viewCashier';
        });
        container.find('._player_deposit').click(function() {
            self.checkLoginStatus();
            window.location = '/player_center2/deposit';
        });
        container.find('._player_withdrawal').click(function() {
            self.checkLoginStatus();
            window.location = '/' + player_center_controller + '/iframe_viewWithdraw';
        });
        container.find('._player_report').click(function() {
            self.checkLoginStatus();
            window.location = '/' + player_center_controller + '/player_center2/report';
        });
        container.find('._player_messages').click(function() {
            self.checkLoginStatus();
            window.location = '/player_center2/messages';
        });
        container.find('._player_cashback').click(function() {
            self.checkLoginStatus();
            window.location = '/' + player_center_controller + '/cashback';
        });
        container.find('._select_currecny_on_logged, ._select_currecny_on_login').change(function() {
            var key=$(this).val();
            utils.safelog('change currency:'+key);
            self.switchPlayerCurrency(key);
        });
    };

    var checkLoginStatus = function() {
        var self = this;
        utils.getJSONP(utils.getApiUrl('check_login_status'), null, function(data) {
            if (data && data['success']) {
                //it's ok, do nothing
            } else {
                self.buildLoginForm();
            }
        }, function(jqXHR, textStatus, errorThrown) {
            //get error
            utils.safelog(jqXHR.status);
            if (jqXHR && jqXHR.status == 403) {
                //render by buildLoginForm
                self.buildLoginForm();
            }
        });
    };

    var check_required_login = function(){
        var link_to_player_center = variables.hosts.player;

        $('.required_login').off('click').on('click', function(){
            if (variables.logged){
                return true;
            }else{
                window.location.href = window.location.protocol + "//" + link_to_player_center;
                return false;
            }
        });
    };

    var buildCaptcha = function(captcha_input) {
        //	$('<div id="captcha_container" style="width:150px;height:40px;border:0px solid blue;float:right;margin-right:109px;margin-top:0px;" ></div>').insertAfter(captcha_input.parent());
        $('<div id="captcha_container" style="background:white;width:150px;height:40px;border:0px solid blue;position:absolute;right:107px;top:30px;z-index:10000;" ></div>').insertAfter(captcha_input.parent());
        var content = "<img src='" + variables.ui.captchaUrl + "?" + Math.random() + "' class='fn-left J-verify _img_captcha'  width='120' height='40'>";
        content += "<a href='javascript:void(0)' class='fn-left J-verify' onclick='_og_refresh_captcha(this)'><img src='/images/_og_refresh.png' style='margin-top:7px;' width='25' height='25' alt='refresh'></a>";
        $('#captcha_container').html(content).hide();
        $('#captcha_container').parent().css({ 'position': 'relative' });
    };

    var addEventToLogin = function() {
        //register form
        //_player_register
        var self = this;
        var container = self.getLoginContainer();

        // var register_url=variables.ui.register_url;
        var popup_new_window = (variables.is_mobile && variables.popup_window_on_player_center_for_mobile) || variables.popup_deposit_after_login;

        container.find('._player_register').click(function() {
            if ($(this).hasClass('without-popup')) {
                window.location = ($(this).attr('href').match(/^javascript:/)) ? utils.getIframeUrl(variables.ui.register_url) : $(this).attr('href');
            } else if (popup_new_window) {
                window.open(utils.getIframeUrl(variables.ui.register_url));
            }
        });

        container.find('a._register_player_link').attr('href', variables.ui.register_url);

        $('#forgot-password').click(function() {
            if ($(this).hasClass('without-popup')) {
                window.location = ($(this).attr('href').match(/^javascript:/)) ? utils.getIframeUrl(variables.ui.forgotPasswordUrl) : $(this).attr('href');
            } else if (popup_new_window) {
                //open new window
                window.open(utils.getIframeUrl(variables.ui.forgotPasswordUrl));
            }
        });

        this.buildCaptcha(container.find('._captcha_input'));

        // container.find('._captcha_input').focus(function(){
        // 	// $(this).data('tooltipsy').show();
        // 	// $("[id^=tooltipsy]").css({'position':'absolute'});
        // });

        var captchaInUse = false;

        container.find('._captcha_input').blur(function() {
            setTimeout(function() {
                if (!captchaInUse) {
                    $('#captcha_container').hide();
                    captchaInUse = false;
                }

            }, 500);

        });
        container.find('._captcha_input').focus(function() {
            //	captchaInUse = true;
            captchaInUse = false;
            $('#captcha_container').show();

        });

        window['_og_refresh_captcha'] = function(el) {
            // console.log(el);
            captchaInUse = true;
            var url = variables.ui.captchaUrl + '?' + Math.random();
            // console.log($(el).parent());
            $(el).parent().find('._img_captcha').attr('src', url);
            // captchaInUse = false;
        };

        container.find('._select_currecny_on_login, ._select_currecny_on_logged').change(function() {
            //call change active db
            var key=$(this).val();
            utils.safelog('change currency:'+key);
            self.switchPlayerCurrency(key);
        });
    };

    var _init_lock_page=function(){
        if($('#_lock_screen').length<=0){
            $('body').append('<div style="display: none" id="_lock_screen"></div>');
        }
    };

    var _lock_page=function(msg){
        this._init_lock_page();
        $('#_lock_screen').addClass('_overlay_screen').html(msg).fadeTo(0, 0.4).css('display', 'flex');
    };

    var _unlock_page=function(){
        this._init_lock_page();
        $('#_lock_screen').removeClass('_overlay_screen').html('').css('display', 'none');
    }

    var switchPlayerCurrency=function(key){
        var url= variables.logged ? variables.urls.player+'/iframe/auth/change_active_currency_for_logged/'+key+'/true' :
            variables.urls.player+'/iframe/auth/change_active_currency/true?__OG_TARGET_DB='+key;

        this._lock_page(variables.langText['Changing Currency']);

        utils.getJSONP(
            url,
            null,
            function(data){
                // utils.safelog(data);
                if(data && data['success']){
                    // changing_currency=false;
                    if(data['redirect_url'])                      {
                        window.location.href=data['redirect_url'];
                    }else{
                        window.location.reload();
                    }
                }else{
                    alert(variables.langText['Change Currency Failed']);
                    this._unlock_page();
                }
            },
            function(jqXHR, textStatus, errorThrown){
                // utils.safelog(jqXHR);
                // utils.safelog('call jsonp failed');
                alert(variables.langText['Change Currency Failed']);
                this._unlock_page();
            }
        );
    };

    var buildRegisterUrl = function() {
        var tracking = '';
        if (variables.trackingCode == undefined || variables.trackingCode == '') {
            //ignore
        } else {
            tracking = '/' + variables.trackingCode;
            if (variables.trackingSourceCode == undefined || variables.trackingSourceCode == '') {
                //ignore
            } else {
                tracking = tracking + '/' + variables.trackingSourceCode;
            }
        }

        var player_center_controller = '';
        if (variables.view_template == 'iframe') {
            player_center_controller = 'iframe_module';
        } else {
            player_center_controller = 'player_center';
        }

        var register_url = '/' + player_center_controller + '/iframe_register' + tracking;

        variables.ui.register_url = register_url;
        return variables.ui.register_url;
    };

    var getLoginContainer = function(){
        return $(variables.ui.loginContainer);
    };

    var buildLoginForm = function() {
        if($(variables.ui.loginContainer).length <= 0){
            return ;
        }

        var act = 'iframe_login';
        utils.buildTemplate(variables.ui.loginContainer, variables.templates.login_template, {
            ui: variables.ui,
            langText: variables.langText,
            hosts: variables.hosts,
            urls: variables.urls,
            act: act,
            formId: utils.generateId(),
            default_prefix_for_username: variables.default_prefix_for_username
        });
        this.addEventToLogin();

        if(typeof window['callback_after_build_login_form'] == 'function'){
            window['callback_after_build_login_form'](this.getLoginContainer(), $);
        }
    };

    var buildLoggedForm = function(player) {
        if($(variables.ui.loginContainer).length <= 0){
            return ;
        }

        utils.buildTemplate(variables.ui.loginContainer, variables.templates.logged_template, {
            ui: variables.ui,
            langText: variables.langText,
            hosts: variables.hosts,
            urls: variables.urls,
            playerName: variables.playerUsername,
            playerInfo: player.getPlayerInfo(),
            default_prefix_for_username: variables.default_prefix_for_username
        });
        this.addEventToLogged();

        if(typeof window['callback_after_build_logged_form'] == 'function'){
            window['callback_after_build_logged_form'](this.getLoginContainer(), $);
        }
    };

    var showError = function(message) {
        if ($('body').find('#login_errormsg').length > 0) {

            if ($('body').find('#show_captcha_on_error').length > 0 && $('body').find('#show_captcha_on_error').val() != 0) {
                $('.captcha').removeClass('hide');
                $('input[name="captcha"]').prop('disabled', false);
            }

            $('body').find('#login_errormsg').html(message);
            return;
        }

        alert(message);
    };

    var buildSlots = function(url) {
        $.getJSON(url, function(data) {
            data['apiPlayPT'] = variables.apiPlayPT;
            data['playerServerUri'] = '//' + variables.host;
            /** OG-610 Generate Static Slot Games HTML Block Remove Game type bar */
            //utils.buildTemplate(variables.ui.ptGameType, variables.templates.pt_game_type_template ,data );
            utils.buildTemplate(variables.ui.ptGame, variables.templates.pt_game_template, data);

            // $('.ptgame-titles').html(gameTypeTemplate(data));
            // $('.products').html(gameTemplate(data));
            $('.ui-pager').html(data.pagination);
        });
    };

    var initTicker = function(gameCodes) {
        var self = this;
        $.getScript(variables.pt_jackpot_ticker_js, function(data, textStatus, jqxhr) {
            $.each(gameCodes, function(k, v) {
                var id = '_pt_ticker_' + v;
                utils.safelog(id + " " + v);
                var ticker = new Ticker({ info: 1, casino: variables.pt_casino, game: v, currency: variables.pt_currency, root_url: variables.pt_ticker_server });
                ticker.attachToTextBox(id);
                ticker.tick();
            })

            var ticker = new Ticker({ info: 2, casino: variables.pt_casino, currency: variables.pt_currency, root_url: variables.pt_ticker_server });
            ticker.attachToTextBox('_pt_ticker_sum');
            ticker.tick();

        });
    };

    var bindSwitchLanguage = function(){
        $('._og_switch_lang').on('click', function(){
            var lang_id = $(this).data('lang-id');
            var lang_code = $(this).data('lang-code');
            var redirect_url = $(this).data('redirect-url');

            smartbackend.language.switch_language.call(this, lang_id, lang_code, redirect_url);

            return false;
        });
    };

    var renderUI = {
        "popupId": null,
        "popupHtmlId": null,
        "addEventToLogged": addEventToLogged,
        "addEventToLogin": addEventToLogin,
        "switchPlayerCurrency": switchPlayerCurrency,
        "getLoginContainer": getLoginContainer,
        "buildLoginForm": buildLoginForm,
        "buildLoggedForm": buildLoggedForm,
        "buildCaptcha": buildCaptcha,
        "showError": showError,
        "buildSlots": buildSlots,
        "checkLoginStatus": checkLoginStatus,
        "initTicker": initTicker,
        "buildRegisterUrl": buildRegisterUrl,
        "check_required_login": check_required_login,
        "_init_lock_page": _init_lock_page,
        "_lock_page": _lock_page,
        "_unlock_page": _unlock_page,
        "check_required_login": check_required_login,
        "bindSwitchLanguage": bindSwitchLanguage
    };

    smartbackend.on('run.t1t.smartbackend', function(){
        renderUI.check_required_login();
        renderUI.buildRegisterUrl();
        renderUI.bindSwitchLanguage();
    });

    smartbackend.on('not_login.t1t.player', function(e, player){
        utils.safelog('build login on ' + variables.ui.loginContainer);
        renderUI.buildLoginForm(player);
    });

    smartbackend.on('logged.t1t.player', function(e, player){
        utils.safelog('build logged on ' + variables.ui.loginContainer);
        renderUI.buildLoggedForm(player);
    });

    smartbackend.on('logout.t1t.player', function(e, player){
        utils.safelog('build logged on ' + variables.ui.loginContainer);
        renderUI.buildLoginForm(player);
    });

    smartbackend.on('failed.login.t1t.player', function(e, player, jsonData){
        // utils.safelog('success failed');
        //login error, clear tooltip
        var container = renderUI.getLoginContainer();
        var captcha_input = container.find('._captcha_input');
        // captcha_input.data('tooltipsy').destroy();
        captcha_input.val('');
        var url = variables.ui.captchaUrl + '?' + Math.random();
        // console.log($(el).parent());
        $('#captcha_container ._img_captcha').attr('src', url);
        // renderUI.buildCaptcha(captcha_input);
        // utils.safelog('show error');
        renderUI.showError(jsonData.message);
    });

    return renderUI;
})();

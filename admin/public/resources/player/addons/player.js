(function(){
    function T1T_Player(){
        this.name = 'player';

        this.player_auto_lock = null;
    }

    T1T_Player.prototype.reload = function(callback){
        var self = this;

        return this;
    };

    T1T_Player.prototype.isLogged = function(){
        return variables.logged;
    };

    T1T_Player.prototype.logout = function(callback){
        var self = this;

        $.ajax({
            "async": false,
            "dataType": "jsonp",
            "url": variables.apiBaseUrl + '/player_logout',
            "data": {},
            "success": function(result){
                if(typeof callback === "function"){
                    if(callback(result['data'])){
                        self.afterLogout(result['data']);
                    }else{
                        window.location.reload(true);
                    }
                }else{
                    self.afterLogout(result['data']);
                }
            },
            "error": function(){
                if(typeof callback === "function"){
                    callback();
                }
            }
        });
    };

    T1T_Player.prototype.afterLogin = function(jsonData){
        if(!!jsonData){
            //set cookie
            var playercookie = jsonData['playercookie'];
            if (playercookie && playercookie != '') {
                utils.safelog('set og_player to ' + playercookie);
                //set cookies to www
                cookies.set('og_player', playercookie, { expires: 1 });

                // document.cookie = [
                //     'og_player', '=', playercookie
                // ].join('');

                // utils.safelog(document.cookie);
            }

            variables.logged = true;
            variables.playerUsername = jsonData['playerName'];
            variables.playerId = jsonData['playerId'];
            //set token
            variables.token = jsonData['token'];
            variables.walletInfo = jsonData['walletInfo'];
            variables.ui.VIP_group = jsonData['VIP_group'];
            variables.ui.total_balance = jsonData['total_balance'];
        }

        utils.events.trigger($.Event("logged.t1t.player"), this);
    };

    T1T_Player.prototype.afterLogout = function(jsonData){
        variables.logged = false;
        variables.playerUsername = '';
        variables.playerId = '';

        var event = $.Event("logout.t1t.player");
        utils.events.trigger(event, this);

        if(event.isDefaultPrevented()) return;

        if(jsonData.hasOwnProperty('redirect_url')){
            window.location.href = jsonData['redirect_url'];
        }else{
            window.location.reload(true);
        }
    };

    T1T_Player.prototype.init = function(){
        var self = this;
        utils.events.trigger($.Event("init.t1t.player"), this);

        utils.registerMessageEvent('iframe_login', function(jsonData) {
            utils.safelog(jsonData);
            if (jsonData['success']) {
                if(utils.inHost('player')){
                    window.location.reload();
                    return;
                }

                self.afterLogin(jsonData);

                utils.buildPushMessage(variables.langText.message_success_login);
                if (typeof window['callback_after_login'] == 'function') {
                    window['callback_after_login'](this.getLoginContainer(), $);
                }else if (variables.popup_deposit_after_login) {
                    window.open("/iframe_module/iframe_makeDeposit", "_blank");
                }
            } else {
                utils.safelog('login failed');
                utils.events.trigger($.Event("failed.login.t1t.player"), self, jsonData);
            }
        });

        utils.registerMessageEvent('iframe_logout', function(jsonData) {
            if (jsonData['success']) {
                self.afterLogout(jsonData);

                utils.buildPushMessage(variables.langText.message_success_logout);
                if (typeof window['callback_after_logout'] == 'function') {
                    window['callback_after_logout'](this.getLoginContainer(), $);
                }
            } else {
                utils.safelog('logout failed');
                utils.events.trigger($.Event("failed.logout.t1t.player"), self, jsonData);
            }
        });

        if (variables.logged) {
            self.afterLogin();
        } else {
            utils.events.trigger($.Event("not_login.t1t.player"), this);
        }
    };

    T1T_Player.prototype.getPlayerToken = function(){
        return variables.token;
    };

    T1T_Player.prototype.getPlayerInfo = function(){
        if(!this.isLogged()){
            return false;
        }

        return {
            "player_id": variables.playerId,
            "username": variables.playerUsername,
            "realname": variables.firstName,
            "VIP_group": variables.VIP_group,
            "token": variables.token,
            "current_lang": variables.currentLang,
            "currentLangName": variables.currentLangName
        };
    };

    function T1T_Player_Auto_Lock(){
        this.options = {
            "player_auto_lock": 0,
            "player_auto_lock_time_limit": 600,
            "player_auto_lock_password_failed_attempt": 3
        };

        if(!variables.hasOwnProperty('player_center')){
            return this;
        }

        if(!variables.player_center.hasOwnProperty('player_auto_lock') || !variables.player_center.hasOwnProperty('player_auto_lock_time_limit')){
            return this;
        }
        this.options['player_auto_lock'] = parseInt(variables.player_center.player_auto_lock);
        this.options['player_auto_lock'] = isNaN(this.options['player_auto_lock']) ? 0 : this.options['player_auto_lock'];

        this.options['player_auto_lock_time_limit'] = parseInt(variables.player_center.player_auto_lock_time_limit);
        this.options['player_auto_lock_time_limit'] = isNaN(this.options['player_auto_lock_time_limit']) ? 600 : this.options['player_auto_lock_time_limit'];

        this.options['player_auto_lock_password_failed_attempt'] = parseInt(variables.player_center.player_auto_lock_password_failed_attempt);
        this.options['player_auto_lock_password_failed_attempt'] = isNaN(this.options['player_auto_lock_password_failed_attempt']) ? 3 : this.options['player_auto_lock_password_failed_attempt'];
    }

    T1T_Player_Auto_Lock.prototype.init = function(){
        if(!this.options.player_auto_lock){
            return this;
        }

        this.password_window_is_open = false;
        this.do_ajax_before_unload_enabled = false;
        this.is_listen_blur_event = false;

        localStorage.setItem('player.auto_lock.idle_start_time', this.getCurrentTime());
        localStorage.setItem('player.auto_lock.blur_start_time', this.getCurrentTime());

        return this;
    };

    T1T_Player_Auto_Lock.prototype.getCurrentTime = function(){
        return Math.round((new Date()).getTime() / 1000);
    };

    T1T_Player_Auto_Lock.prototype.start_idle_check = function(){
        var self = this;
        localStorage.setItem('player.auto_lock.idle_start_time', self.getCurrentTime());

        this.idle_timer = setInterval(function(){
            if(!self.check_idle()){
                self.show_password_window();
                self.stop_idle_check();
            }
        }, 1000);
    };

    T1T_Player_Auto_Lock.prototype.stop_idle_check = function(){
        if(!this.idle_timer){
            return this;
        }
        clearInterval(this.idle_timer);
    };

    T1T_Player_Auto_Lock.prototype.check_idle = function(){
        var current_time = this.getCurrentTime();

        var idle_start_time = parseInt(JSON.parse(localStorage.getItem('player.auto_lock.idle_start_time')));

        var time_limit = (this.options.player_auto_lock_time_limit - (current_time - idle_start_time));

        return (time_limit > 0);
    };

    T1T_Player_Auto_Lock.prototype.start_blur_check = function(){
        var self = this;

        if(this.is_listen_blur_event){
            return this;
        }

        $(window).on("blur", function(e) {
            localStorage.setItem('player.auto_lock.blur_start_time', self.getCurrentTime());
        });

        $(window).on("focus", function(e) {
            if(!self.check_blur()){
                self.show_password_window();
            }
        });

        this.is_listen_blur_event = true;
    };

    T1T_Player_Auto_Lock.prototype.check_blur = function(){
        var current_time = this.getCurrentTime();

        var blur_start_time = parseInt(JSON.parse(localStorage.getItem('player.auto_lock.blur_start_time')));

        var time_limit = (this.options.player_auto_lock_time_limit - (current_time - blur_start_time));

        return (time_limit > 0);
    };

    T1T_Player_Auto_Lock.prototype.show_password_window = function(){
        var self = this;
        if(this.password_window_is_open){
            return this;
        }

        this.password_window_is_open = true;
        localStorage.setItem('player_auto_lock_password_window_is_open', 1);

        var modal = $('<div class="t1t-ui modal auto_local_password_modal">\n' +
            '  <div class="modal-dialog" role="document">\n' +
            '    <form>\n' +
            '      <div class="modal-content">\n' +
            '        <div class="modal-header">\n' +
            '          <h5 class="modal-title">' + variables.player_center.locale.player_auto_lock_window_header + '</h5>\n' +
            '          <button type="button" class="close" aria-label="Close">\n' +
            '            <span aria-hidden="true">&times;</span>\n' +
            '          </button>' +
            '        </div>\n' +
            '        <div class="modal-body">\n' +
            '          <div class="form-group auto_local_password_field">\n' +
            '              <label for="auto_local_password">' + variables.langText.form_field_password + '</label>\n' +
            '              <input type="password" class="form-control" id="auto_local_password">\n' +
            '              <p class="help-block"></p>' +
            '          </div>\n' +
            '        </div>\n' +
            '        <div class="modal-footer">\n' +
            '          <button type="button" class="btn btn-primary auto_logout_btn disabled">' + variables.player_center.locale.player_auto_lock_window_auto_logout + '</button>\n' +
            '          <button type="submit" class="btn btn-primary">' + variables.player_center.locale.player_auto_lock_window_submit + '</button>\n' +
            '        </div>\n' +
            '      </div>\n' +
            '    </form>\n' +
            '  </div></div>');

        if($('.auto_local_password_modal').length <= 0){
            modal.appendTo($('body'));
        }

        modal.on('show.t1t.ui.modal', function(){
            var form = $('form', modal);

            form.on('submit', function(){
                form.find(':input[type=submit]').prop('disabled', true);

                var password = $('#auto_local_password', form).val();

                self.verifyPassword(password, function(data){
                    form.find(':input[type=submit]').prop('disabled', false);

                    if(data.status !== "success"){
                        if(parseInt(data.data.try_times) > self.options.player_auto_lock_password_failed_attempt){
                            confirm(data.message);

                            player.logout();
                        }else{
                            $('.auto_local_password_field .help-block', form).html(data.message);
                        }
                    }else{
                        window.onbeforeunload = old_onbeforeunload;

                        modal.modal('hide');

                        self.start();
                    }
                });
                return false;
            });
        });

        self.do_ajax_before_unload_enabled = true;

        var old_onbeforeunload = window.onbeforeunload;

        var player_logout = function(){
            self.password_window_is_open = false;
            localStorage.setItem('player_auto_lock_password_window_is_open', 0);

            self.do_ajax_before_unload_enabled = false;
            window.onbeforeunload = old_onbeforeunload;

            player.logout();
        };

        window.onbeforeunload = function(){
            if(!self.do_ajax_before_unload_enabled){
                return;
            }

            player_logout();
        };

        $('.close', modal).on('click', function(){
            modal.modal('hide');

            player_logout();
        });

        modal.on('hidden.t1t.ui.modal', function(){
            modal.remove();
            self.password_window_is_open = false;
            localStorage.setItem('player_auto_lock_password_window_is_open', 0);
        });

        modal.modal({
            "backdrop": 'static',
            "keyboard": false
        });

        var auto_logout_limit = parseInt(self.options.player_auto_lock_window_auto_logout);
        auto_logout_limit = (isNaN(auto_logout_limit)) ? 60 : auto_logout_limit;

        var run_auto_logout = function(){
            auto_logout_limit--;

            $('.auto_logout_btn', modal).html(variables.player_center.locale.player_auto_lock_window_auto_logout + '(' + auto_logout_limit + ')');

            if(auto_logout_limit < 0){
                $('.close', modal).trigger('click');
            }else{
                setTimeout(function(){
                    run_auto_logout();
                }, 1000);
            }
        };
        run_auto_logout();

        return this;
    };

    T1T_Player_Auto_Lock.prototype.verifyPassword = function(password, callback){
        $.ajax({
            "dataType": "jsonp",
            "url": variables.apiBaseUrl + '/AutoLockVerifyPassword',
            "data": {"password": password},
            "success": function(data){
                if(typeof callback === "function"){
                    callback(data);
                }
            },
            "error": function(){
                if(typeof callback === "function"){
                    player.logout();
                }
            }
        });
    };

    T1T_Player_Auto_Lock.prototype.start = function(){
        if(!this.options.player_auto_lock){
            return this;
        }

        this.do_ajax_before_unload_enabled = false;

        if(!!JSON.parse(localStorage.getItem('player_auto_lock_password_window_is_open'))){
            player.logout();
            return;
        }

        switch(this.options.player_auto_lock){
            case 2:
                this.start_blur_check();
                break;
            case 1:
                this.start_idle_check();
                break;
            default:
                break;
        }
    };

    T1T_Player_Auto_Lock.prototype.stop = function(){
        switch(this.options.player_auto_lock){
            case 2:
                break;
            case 1:
                this.stop_idle_check();
                break;
            default:
                break;
        }
    };

    var player = new T1T_Player();
    smartbackend.addAddons(player.name, player);

    smartbackend.on('run.t1t.smartbackend', function(){
        player.init();
    });

    smartbackend.on('not_login.t1t.player', function(e, player){
        localStorage.setItem('player_auto_lock_password_window_is_open', 0);

        utils.devicePostMessage('not_login.t1t.player', null);
    });

    smartbackend.on('logged.t1t.player', function(e, player){
        player.player_auto_lock = new T1T_Player_Auto_Lock();
        player.player_auto_lock.init();
        player.player_auto_lock.start();

        utils.devicePostMessage('logged.t1t.player', player.getPlayerInfo());
    });

    smartbackend.on('logout.t1t.player', function(){
        if(player.player_auto_lock){
            player.player_auto_lock.stop();
            player.player_auto_lock = null;
        }
    });

    return player;
})();
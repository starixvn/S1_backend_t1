(function(){
    var NOTIFICATION_TYPE_INFO = 1;
    var NOTIFICATION_TYPE_SUCCESS = 2;
    var NOTIFICATION_TYPE_WARNING = 3;
    var NOTIFICATION_TYPE_DANGER = 4;

    var SOURCE_TYPE_LAST_LOGIN = 1;
    var SOURCE_TYPE_DEPOSIT = 2;
    var SOURCE_TYPE_WITHDRAWAL = 3;
    var SOURCE_TYPE_VIP_UPGRADE = 4;

    var DEFAULT_OPTS = {
        "placement": {
            "from": "bottom",
            "align": "right"
        },
        "showProgressbar": true,
        "delay": 5000,
        "timer": 1000,
        "mouse_over": "pause",
        "template": '<div data-notify="container" class="t1t-ui t1t-notify alert alert-{0}" role="alert">' +
        '  <div class="t1t-notify-body">' +
        '    <button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
        '    <div class="t1t-notify-body-row">' +
        '      <div class="t1t-notify-icon"><span data-notify="icon"></span></div>' +
        '      <div class="t1t-notify-title"><span data-notify="title">{1}</span></div>' +
        '    </div>' +
        '    <div class="t1t-notify-body-row">' +
        '      <div class="t1t-notify-message"><span data-notify="message">{2}</span></div>' +
        '    </div>' +
        '  </div>' +
        '  <div class="progress" data-notify="progressbar">' +
        '    <div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
        '  </div>' +
        '  <a href="{3}" target="{4}" data-notify="url"></a>' +
        '</div>'
    };

    function T1T_PlayerNotification(){
        this.name = 'player_notification';

        this.on_notify_list = {};
    }

    T1T_PlayerNotification.prototype.notify = function(type, icon, title, message, url, target, custom_options, callback){
        var message_content = {};

        if(!!icon){
            message_content['icon'] = icon;
        }

        if(!!title){
            message_content['title'] = title;
        }

        if(!!message){
            message_content['message'] = message;
        }else{
            message_content['message'] = '';
        }

        if(!!url){
            message_content['url'] = url;
        }

        if(!!target){
            message_content['target'] = target;
        }

        var options = $.extend(true, DEFAULT_OPTS, custom_options);

        if(typeof callback === "function"){
            options['onClose'] = callback;
        }else if(!!callback && (callback.hasOwnProperty('show') || callback.hasOwnProperty('shown') || callback.hasOwnProperty('hide') || callback.hasOwnProperty('hidden'))){
            if(callback['show'] && typeof callback['show'] === "function"){
                options['onShow'] = callback['show'];
            }

            if(callback['shown'] && typeof callback['shown'] === "function"){
                options['onShown'] = callback['shown'];
            }

            if(callback['hide'] && typeof callback['hide'] === "function"){
                options['onClose'] = callback['hide'];
            }

            if(callback['hidden'] && typeof callback['hidden'] === "function"){
                options['onClosed'] = callback['hidden'];
            }
        }

        switch(type){
            case 'success':
            case 'info':
            case 'warning':
            case 'danger':
                options['type'] = type;
                break;
            default:
                options['type'] = 'info';
                break;
        }
        return $.notify(message_content, options);
    };

    T1T_PlayerNotification.prototype.info = function(title, message, url, target, custom_options, callback){
        return this.notify('info', 'glyphicon glyphicon-info-sign', title, message, url, target, custom_options, callback);
    };

    T1T_PlayerNotification.prototype.success = function(title, message, url, target, custom_options, callback){
        return this.notify('success', 'glyphicon glyphicon-ok-sign', title, message, url, target, custom_options, callback);
    };

    T1T_PlayerNotification.prototype.warning = function(title, message, url, target, custom_options, callback){
        return this.notify('warning', 'glyphicon glyphicon-exclamation-sign', title, message, url, target, custom_options, callback);
    };

    T1T_PlayerNotification.prototype.danger = function(title, message, url, target, custom_options, callback){
        return this.notify('danger', 'glyphicon glyphicon-remove-sign', title, message, url, target, custom_options, callback);
    };

    T1T_PlayerNotification.prototype.start = function(){
        var self = this;
        var check_interval = parseInt(variables.player_notification.check_interval);
        check_interval = (!!check_interval) ? check_interval : 60;

        var display_time = parseInt(variables.player_notification.display_time);
        display_time = (!!display_time) ? display_time : 5;

        DEFAULT_OPTS['delay'] = display_time * 1000;

        var check_player_notification = function(){
            self.check();

            setTimeout(check_player_notification, check_interval * 1000);
        };

        setTimeout(check_player_notification, 500);
        utils.safelog('actived, refresh player notification');
    };

    T1T_PlayerNotification.prototype.check = function(){
        var self = this;

        if(!utils.isInActiveWindow()){
            utils.safelog('ignore check player notification on hidden window');
            return false;
        }

        var data = {
        };

        utils.getJSONP(utils.getApiUrl('player_notify'), data, function(result){
            if(result['status'] !== "success"){
                return false;
            }

            var notify_contents = result['data'];

            if(!!notify_contents && (typeof notify_contents !== "object")){
                return false;
            }

            var loop = 0;
            $.each(notify_contents, function(notify_id, notify_content){
                if(!notify_content.hasOwnProperty('notify_type') ||
                    !notify_content.hasOwnProperty('title') ||
                    !notify_content.hasOwnProperty('message') ||
                    !notify_content.hasOwnProperty('url') ||
                    !notify_content.hasOwnProperty('url_target')
                ){
                    return;
                }

                if(self.on_notify_list.hasOwnProperty(notify_content['notify_id'])){
                    return;
                }

                var callback = {
                    "show": function(){
                        self.on_notify_list[notify_content['notify_id']] = notify_content;
                    },
                    "hide": function(){
                        var data = {
                            "notify_id": notify_content['notify_id']
                        };

                        utils.getJSONP(utils.getApiUrl('player_is_notify'), data, function(result){
                        });
                    }
                };

                var event_category = '';
                switch(notify_content['source_type']){
                    case SOURCE_TYPE_LAST_LOGIN:
                        event_category = 't1t.player_notify.last_login';
                        break;
                    case SOURCE_TYPE_DEPOSIT:
                        event_category = 't1t.player_notify.deposit';
                        break;
                    case SOURCE_TYPE_WITHDRAWAL:
                        event_category = 't1t.player_notify.withdrawal';
                        break;
                    case SOURCE_TYPE_VIP_UPGRADE:
                        event_category = 't1t.player_notify.vip.upgrade';
                        break;
                    default:
                        event_category = 't1t.player_notify';
                }

                loop++;
                setTimeout(function(){
                    switch(parseInt(notify_content['notify_type'])){
                        case NOTIFICATION_TYPE_SUCCESS:
                            self.success(notify_content['title'], notify_content['message'], notify_content['url'], notify_content['url_target'], {}, callback);

                            utils.events.trigger($.Event('success.' + event_category), notify_content);
                            break;
                        case NOTIFICATION_TYPE_WARNING:
                            self.warning(notify_content['title'], notify_content['message'], notify_content['url'], notify_content['url_target'], {}, callback);

                            utils.events.trigger($.Event('warning.' + event_category), notify_content);
                            break;
                        case NOTIFICATION_TYPE_DANGER:
                            self.danger(notify_content['title'], notify_content['message'], notify_content['url'], notify_content['url_target'], {}, callback);

                            utils.events.trigger($.Event('danger.' + event_category), notify_content);
                            break;
                        case NOTIFICATION_TYPE_INFO:
                        default:
                            self.info(notify_content['title'], notify_content['message'], notify_content['url'], notify_content['url_target'], {}, callback);

                            utils.events.trigger($.Event('info.' + event_category), notify_content);
                            break;
                    }
                }, 500 * loop);
            });

            utils.safelog('checked, player notification');
        }, function(result){
            return false;
        });
    };

    var player_notification = new T1T_PlayerNotification();
    smartbackend.addAddons(player_notification.name, player_notification);

    smartbackend.on('logged.t1t.player', function(){
        if(variables.player_notification.enabled){
            player_notification.start();
        }
    });

    return player_notification;
})();
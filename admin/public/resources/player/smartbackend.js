var smartbackend = window._export_smartbackend = window._export_sbe_t1t = (function(){
    /**
     * T1T_SmartBackend
     *
     * @class
     * @property {T1T_Utils} utils
     */
    function T1T_SmartBackend(){
        this.events = t1t_event;

        this.trigger = t1t_event.trigger.bind(t1t_event);
        this.on = t1t_event.on.bind(t1t_event);

        //import css
        $.each(common_variables.css, function(key, value){
            var html = '<style type="text/css" id="player_main_css_' + key + '">' + decodeURIComponent(value) + '</style>';
            document.writeln(html);
        });
    }

    T1T_SmartBackend.prototype.init = function(callback){
        var self = this;

        this.$ = $;
        this.underscore = underscore;
        this.variables = variables = $.extend(true, variables, common_variables);
        this.utils = utils;
        this.renderUI = renderUI;
        this.callApi = callApi;
        this.cookies = cookies;
        this.language = language;

        utils.events = t1t_event;

        $.ajax({
            "url": window.location.protocol + '//' + utils.getHost('player') + '/async/variables',
            "jsonp": "callback",
            "dataType": "jsonp",
            // "async": false,
            "success": function(response){
                variables = $.extend(true, variables, response.data);

                self.variables = variables;

                callback();

                utils.events.trigger($.Event('init.t1t.smartbackend', {
                    "repeat_trigger": false
                }), this);
            },
            "error": function(){
                MessageBox.danger('Something\'s went wrong on fetch variables');
            }
        });
    };

    T1T_SmartBackend.prototype.run = function(){
        var self = this;

        MessageBox.setOptions({
            'header_title_success': lang('alert-success'),
            'header_title_info': lang('alert-info'),
            'header_title_warning': lang('alert-warning'),
            'header_title_danger': lang('alert-danger'),
            'close_button_text': lang('close_button_text'),
            'confirm_button_text': lang('confirm_button_text')
        });

        self.initEvents();

        if(variables.enabled_check_frondend_block_status){
            utils.checkBlockStatus();
        }

        //call api
        callApi.init();
        //init webPush
        // if(variables.enabled_web_push){
        //     webPush.init();
        // }

        if(variables.enabled_switch_to_mobile_dir_on_www){
            utils.checkAndGoMobileDir($, variables.enabled_auto_switch_to_mobile_on_www);
        }else if(variables.enabled_switch_to_mobile_on_www){
            utils.checkAndGoMobile($, variables.enabled_auto_switch_to_mobile_on_www);
        }

        if(variables.enabled_switch_www_to_https){
            utils.checkAndGoHttps($);
        }

        utils.events.trigger($.Event('run.t1t.smartbackend', {
            "repeat_trigger": false
        }), smartbackend);
    };

    T1T_SmartBackend.prototype.addAddons = function(name, instance){
        this[name] = instance;
        window['t1t_' + name] = instance;

        return this;
    };

    T1T_SmartBackend.prototype.initEvents = function(){
        $('body').on('keydown', '.number_only', function (e) {
            var code = e.keyCode || e.which;
            if ($.inArray(code, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                // Allow: Ctrl+A
                (code == 65 && e.ctrlKey === true) ||
                // Allow: home, end, left, right, down, up
                (code >= 35 && code <= 40)) {
                // let it happen, don't do anything
                return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (code < 48 || code > 57)) && (code < 96 || code > 105)) {
                e.preventDefault();
            }
        });
    };

    /**
     * @typedef {T1T_SmartBackend}
     * @type {T1T_SmartBackend}
     */
    var smartbackend = new T1T_SmartBackend();

    return smartbackend;
})();
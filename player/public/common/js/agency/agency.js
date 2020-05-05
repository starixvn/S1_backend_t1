(function(window){
    function T1T_Event(relatedTarget){
        this.relatedTarget = (relatedTarget === undefined) ? window : relatedTarget;
        this.events = {};
    }

    T1T_Event.prototype.on = function(event, callback){
        var callbacks = (this.events.hasOwnProperty(event)) ? this.events[event] : [];

        callbacks.push(callback);

        this.events[event] = callbacks;
    };

    T1T_Event.prototype.off = function(event, callback){
        var callbacks = (this.events.hasOwnProperty(event)) ? this.events[event] : [];

        if(typeof callback === "undefined"){
            this.events[event] = [];

            return this;
        }

        while($.inArray(callback, callbacks) > -1){
            callbacks.splice($.inArray(callback, callbacks), 1);
        }

        this.events[event] = callbacks;

        return this;
    };

    T1T_Event.prototype.notify = function(event, data){
        if(!this.events.hasOwnProperty(event)){
            return this;
        }

        var callback = null;
        var callbacks = this.events[event];

        var e = $.Event(event, {"relatedTarget": this.relatedTarget, "data": data});

        for(var i = 0; i < callbacks.length; i++){
            if(e.isPropagationStopped() || e.isImmediatePropagationStopped() || e.isDefaultPrevented() || typeof callbacks[i] !== "function"){
                break;
            }
            callback = callbacks[i];

            callback.apply(this.relatedTarget, [e, data]);
        }

        return this;
    };

    function T1T_Agency(){
        this.options = {
            "base_url": null,
            "auto_logon": false,
            "player_token": false
        };

        this.events = new T1T_Event(this);
    }

    T1T_Agency.prototype.init = function(options){
        var self = this;

        this.options = $.extend({}, this.options, options);

        this.options.base_url = this.options.base_url.replace(/\/+$/,'');

        $.receiveMessage(function(e) {
            if(e === undefined || e.data.length <= 0){
                return false;
            }

            var jsonData = null;

            try{
                jsonData = $.parseJSON(e.data);
            }catch(ex){
                return false;
            }

            if(jsonData && typeof(jsonData['act']) !== 'undefined'){
                self.events.notify(jsonData['act'], (jsonData.hasOwnProperty('data')) ? jsonData['data'] : {});
            }
        });

        this.postInit();

        return this;
    };

    T1T_Agency.prototype.postInit = function(){
        if(this.options.auto_logon && this.options.player_token.length > 0){
            this.login_by_player_token();
        }

        return this;
    };

    T1T_Agency.prototype.login_by_player_token = function(token){
        var iframe = $('<iframe>');

        var iframe_name = 'agency_login_by_player_token';
        var src = this.options.base_url + '/agency/login_by_player_token';

        iframe.attr('name', iframe_name);
        iframe.attr('src', 'javascript: void(0);');
        iframe.hide();

        var form = $('<form>');
        form.attr('id', 'agency_auto_logon_form');
        form.attr('action', src);
        form.attr('method', 'post');
        form.attr('target', iframe_name);

        var form_data = {
            "iniframe": "1",
            "token": ((token === undefined) ? this.options.player_token : token)
        };

        $.each(form_data,function(n,v){
            form.append('<input type="hidden" name="'+n+'" value="'+v+'" />');
        });

        this.events.on('agency_login_response', function(){
            form.remove();
            iframe.remove();
        });

        $('body').append(iframe).append(form);

        form.submit();
    };

    T1T_Agency.prototype.logout = function(){
        var iframe = $('<iframe>');

        var iframe_name = 'agency_logout';
        var src = this.options.base_url + '/agency/logout';

        iframe.attr('name', iframe_name);
        iframe.attr('src', 'javascript: void(0);');
        iframe.hide();

        var form = $('<form>');
        form.attr('id', 'agency_auto_logout_form');
        form.attr('action', src);
        form.attr('method', 'get');
        form.attr('target', iframe_name);

        this.events.on('agency_login_response', function(){
            form.remove();
            iframe.remove();
        });

        $('body').append(iframe).append(form);

        form.submit();
    };
    var t1t_agency = window.t1t_agency = new T1T_Agency();
})(window);
var t1t_event = (function(){
    function Event(event_type){
        this.type = event_type;

        this.callbacks = [];
        this.is_repeated = true;
        this.is_fired = false;

        this.last_notify_arguments = null;
    }

    Event.prototype.on = function(callback){
        if(typeof callback !== "function"){
            return false;
        }

        this.callbacks.push(callback);
    };

    Event.prototype.off = function(){
        this.callbacks = [];
    };

    Event.prototype.notify = function(){
        var args = Array.prototype.slice.call(arguments);

        if(this.callbacks.length <= 0){
            return this;
        }

        this.callbacks.forEach(function(callback){
            callback.apply(null, args);
        });
    };

    Event.prototype.setRepeat = function(repeat){
        this.is_repeated = repeat;
    };

    function EventManager(){
        this.events = {};
    }

    EventManager.prototype.register = function(event_type){
        var event = null;
        if(!this.events.hasOwnProperty(event_type)){
            event = new Event(event_type);
            this.events[event_type] = event;
        }else{
            event = this.events[event_type];
        }

        return event;
    };

    EventManager.prototype.on = function(event_type, callback){
        var event = this.register(event_type);

        if(!event.is_repeated && event.is_fired){
            var args = Array.prototype.slice.call(event.last_notify_arguments);
            args[1] = callback;

            callback.apply(null, args);
            return event;
        }

        event.on(callback);

        return event;
    };

    EventManager.prototype.off = function(event_type){
        var event = this.register(event_type);

        event.off();

        return event;
    };

    EventManager.prototype.trigger = function(){
        var args = Array.prototype.slice.call(arguments);

        if(args.length <= 0){
            return this;
        }

        var event_handler = args[0];
        var event = this.register(event_handler.type);

        if(event_handler.hasOwnProperty('repeat_trigger')){
            event.setRepeat(event_handler['repeat_trigger']);
        }

        event.is_fired = true;

        event.last_notify_arguments = args;

        event.notify.apply(event, args);
    };

    return new EventManager();
})();
(function(){
    ///<editor-fold desc="T1T_GamePreloader">{{{
    /**
     *
     * T1T_GAME_PRELOADER_CONTAINER_APPEND_TO: for designer to used.
     *
     * @class
     * @constructor
     */
    function T1T_GamePreloader(){
        this.preloader_type = 'default';
        this.id = 't1t_game_preloader';
        this.base_class = 't1t_game_preloader';
        this.additional_class = '';
        this.options = {};

        this.flags = {
            "init": false
        };

        this.container = null;
        this.iframe = null;
        this.embedded_url = 'javascript: void(0);';
    }

    T1T_GamePreloader.prototype.addJS = function(url, force_load, callback){
        var embedded_url = url + ((force_load) ? ((url.indexOf('?') >= 0) ? '&v=' : '?v=') + ('' + Math.random()).substr(2, 16) : '');
        var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
        g.type = 'text/javascript';
        g.async = true;
        g.defer = true;
        g.src = embedded_url;
        g.onload = function(){
            if(typeof callback === "function") callback();
        };
        s.parentNode.insertBefore(g, s);

        return g;
    };

    T1T_GamePreloader.prototype.init = function(game_preloader_manager){
        var self = this;
        this.options = game_preloader_manager.options;

        if(this.options.hasOwnProperty(this.preloader_type)){
            $.each(this.options[this.preloader_type], function(key, value){
                self[key] = value;
            });
        }

        if(!self.flags.init){
            self.preInit();
            if(typeof this._init === "function"){
                self._init(function(){
                    self.postInit();
                    self.flags.init = true;
                });
            }else{
                self.postInit();
                self.flags.init = true;
            }
        }
    };

    T1T_GamePreloader.prototype.createPreloader = function(){
        if(this.container !== null) return this.container;

        var container, iframe;
        if($('#' + this.id).length){
            container = $('#' + this.id + "_container");
            iframe = $('iframe', container);
        }else{
            container = $('<div>');
            container.attr('id', this.id + "_container");
            container.addClass(this.base_class + "_container");
            container.addClass(this.additional_class + "_container");

            iframe = $('<iframe>').appendTo(container);
            iframe.attr('id', this.id);
            iframe.attr('src', 'javascript: void(0);');
            iframe.addClass(this.base_class);
            iframe.addClass(this.additional_class);

            if(typeof T1T_GAME_PRELOADER_CONTAINER_APPEND_TO !== "undefined"){
                container.appendTo($(T1T_GAME_PRELOADER_CONTAINER_APPEND_TO));
            }else{
                $('body').append(container);
            }
        }

        var inside_container_prepend = $('#inside_container_prepend');
        var inside_container_append = $('#inside_container_append');

        if(inside_container_prepend.length == 1) {
            inside_container_prepend.prependTo(container);
        }

        if(inside_container_append.length == 1) {
            inside_container_append.appendTo(container);
        }

        if(iframe.attr('src').indexOf('/goto_t1lottery') < 0){
            iframe.attr('src', this.embedded_url);
        }

        this.container = container;
        this.iframe = iframe;

        return this;
    };

    T1T_GamePreloader.prototype.preInit = function(){
        this.createPreloader();

        return this;
    };

    T1T_GamePreloader.prototype.postInit = function(){
        return this;
    };

    T1T_GamePreloader.prototype.show = function(){
        this.container.addClass('active');

        smartbackend.trigger($.Event('show.t1t.game-preloader'));
    };

    T1T_GamePreloader.prototype.hide = function(){
        this.container.removeClass('active');
    };
    ///}}}</editor-fold>

    ///<editor-fold desc="T1T_GamePreloader_Lottery">{{{
    /**
     * Game Preloader for t1t lottery
     *
     * Need ask designer to add the 'data-game-type="t1t_lottery', 'data-game-preloader="1"', 'data-lottery-id="[official|double]-{ID}"' attributes to the a tag.
     * <code>
     * <a href="{HREF}" data-game-type="t1t_lottery" data-game-preloader="1" data-lottery-id="double-1"></a>
     * </code>
     *
     * @class
     * @constructor
     */
    function T1T_GamePreloader_Lottery(){
        this.preloader_type = 'lottery';
        this.id = 't1t_game_preloader_lottery';
        this.additional_class = 't1t_game_preloader_lottery';

        this.lottery_play_url = '/player_center/goto_t1lottery/';
        this.lottery_sdk_url = '/player_center2/lottery/sdk?=v' + (new Date().getTime()) + Math.round(Math.random() * 1000000);

        this.init_callback = null;

        this.flags = {
            "game_load": false,
            "game_sdk_load": false
        };
    }

    T1T_GamePreloader_Lottery.prototype = new T1T_GamePreloader();
    T1T_GamePreloader_Lottery.prototype.constructor = T1T_GamePreloader_Lottery;

    T1T_GamePreloader_Lottery.prototype.getLotteryUrl = function(lottery_id){
        var tmp = lottery_id.split('-');
        var url = this.lottery_play_url + tmp[1] + '/' + tmp[0] + '/_null/_null/_null/iframe';

        return url;
    };

    T1T_GamePreloader_Lottery.prototype.preInit = function(){
        var selector = $('[data-game-type="t1t_lottery"][data-game-preloader="1"]');
        if(selector.length > 0){
            var element = $(selector[0]);

            var lottery_id = element.data('lottery-id');

            if(!lottery_id){
                return this;
            }

            this.embedded_url = this.getLotteryUrl(lottery_id);
        }

        return T1T_GamePreloader.prototype.preInit.call(this, Array.prototype.slice(arguments));
    };

    T1T_GamePreloader_Lottery.prototype.initEvent = function(){
        var self = this;

        smartbackend.on('load.t1t_lottery.sdk', function(){
            self.flags.game_sdk_load = true;

            if(typeof self.init_callback === "function"){
                self.init_callback();
                self.init_callback = null;
            }

            if(typeof t1t_lottery === "object" && typeof t1t_lottery.setGameContainerIframe === "function"){
                t1t_lottery.setGameContainerIframe(self.iframe[0]);
            }
        });

        smartbackend.on('back.t1t_lottery.game', function(){
            self.hide();
        });

        smartbackend.on('load.t1t_lottery.game', function(){
            self.flags.game_load = true;
            self.loader.hide();
        });

        $('[data-game-type="t1t_lottery"][data-game-preloader]').on('click', function(e){
            if(!$(this).data('game-preloader')){
                return true;
            }

            e.preventDefault();

            var lottery_id = $(this).data('lottery-id');
            if(self.isCompleteReady()){
                var tmp = lottery_id.split('-');
                t1t_lottery.navigateGame(tmp[1], tmp[0]);
            }else{
                self.iframe.attr('src', self.getLotteryUrl(lottery_id));
            }

            $("html, body").animate({ scrollTop: 0 }, "slow");

            if(!self.flags.game_load){
                self.loader.show();
            }

            self.show();

            return false;
        });
    };

    T1T_GamePreloader_Lottery.prototype._init = function(callback){
        var self = this;

        this.initLoader();
        this.initEvent();

        if(!window.hasOwnProperty('t1t_lottery')){
            if($('#t1t_lottery_sdk').length <= 0){
                var element = self.addJS(this.lottery_sdk_url, false);
                self.init_callback = callback;

                $(element).attr('id', 't1t_lottery_sdk');
            }else{
                self.init_callback = callback;
            }
        }else{
            self.flags.game_sdk_load = true;
            callback();
        }
    };

    T1T_GamePreloader_Lottery.prototype.isCompleteReady = function(){
        return (this.flags.game_sdk_load && this.flags.game_load);
    };

    T1T_GamePreloader_Lottery.prototype.initLoader = function(){
        var wrapper = $('<div>').addClass('t1t_game_preloader_loader_wrapper').appendTo(this.container);
        var loader = $('<div>').addClass('t1t_game_preloader_loader').appendTo(wrapper);

        $('<div>').appendTo(loader).addClass('ball ball-1').append($('<span>').html(Math.floor(Math.random() * 45) + 1));
        $('<div>').appendTo(loader).addClass('ball ball-2').append($('<span>').html(Math.floor(Math.random() * 45) + 1));
        $('<div>').appendTo(loader).addClass('ball ball-3').append($('<span>').html(Math.floor(Math.random() * 45) + 1));
        $('<div>').appendTo(loader).addClass('ball ball-4').append($('<span>').html(Math.floor(Math.random() * 45) + 1));
        $('<div>').appendTo(loader).addClass('ball ball-5').append($('<span>').html(Math.floor(Math.random() * 45) + 1));
        $('<div>').appendTo(loader).addClass('ball ball-6').append($('<span>').html(Math.floor(Math.random() * 45) + 1));

        this.loader = wrapper;

        return this;
    };
    ///}}}</editor-fold>

    ///<editor-fold desc="TTT_GamePreloaderManager">{{{
    /**
     * @class
     * @constructor
     *
     */
    function TTT_GamePreloaderManager(){
        this.name = 'game_preloader';

        this.options = {
        };

        this.flags = {
            "init": false
        };

        /**
         * @type {{lottery: T1T_GamePreloader_Lottery}}
         */
        this.game_providers = {
            "lottery": new T1T_GamePreloader_Lottery(this)
        };
    }

    TTT_GamePreloaderManager.prototype.setOptions = function(options){
        this.options = $.extend({}, this.options, options);
    };

    TTT_GamePreloaderManager.prototype.init = function(){
        if(this.flags.init) return this;

        // utils.safelog('--- Game Preloader run ---');
        var self = this;

        this.flags.init = true;

        /**
         * @callback game_providers_each
         * @param {string} game_type
         * @param {T1T_GamePreloader} game_preloader
         */

        /**
         * @external jQuery.each
         * @param {object}
         * @param {game_providers_each}
         */
        $.each(this.game_providers, function(game_type, game_preloader){
            game_preloader.init(self);
        });
    };
    ///}}}</editor-fold>

    var game_preloader = new TTT_GamePreloaderManager();
    smartbackend.addAddons(game_preloader.name, game_preloader);

    smartbackend.on('logged.t1t.player', function(){
        game_preloader.setOptions(variables['game_preloader']);
        game_preloader.init();
    });

    return game_preloader;
})();
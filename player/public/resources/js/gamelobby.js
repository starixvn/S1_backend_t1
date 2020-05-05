(function($) {
    $.gamelobby = function(site, gamePlatformId, options) {

        var plugin = this;
            plugin.settings = {};
            plugin.templates = {};
            plugin.cache = {};
        
        var defaults = {

            gameTypeContainer:      '#game-types',
            gameContainer:          '#games',
            
            gameTypesUrl:           '/game_description/gameTypes/',
            gamesUrl:               '/game_description/allGames/',
            
            gameTypeTarget:         'ul > li > a',
            activeGameTypeTarget:   'li',
            activeGameTypeClass:    'active',

        };

        plugin.init = function() {

            plugin.settings = $.extend({}, defaults, options);
            
            var gameTypesUrl = site + plugin.settings.gameTypesUrl + gamePlatformId;

            $.getJSON(gameTypesUrl, function(data) {
                
                if (plugin.templates['gameType'] == null) {
                    plugin.templates['gameType'] = _.template(plugin.settings.gameTypeTemplate);
                }
                
                var data = $.extend(data, plugin.settings);
                var html = plugin.templates['gameType'](data);

                $(plugin.settings.gameTypeContainer).html(html);

                $(plugin.settings.gameTypeContainer).on('click', plugin.settings.gameTypeTarget, function() {

                    switch (plugin.settings.activeGameTypeTarget) {
                        case 'li':
                            $(this).parent('li').siblings().removeClass(plugin.settings.activeGameTypeClass);
                            $(this).parent('li').addClass(plugin.settings.activeGameTypeClass);
                            break;

                        case 'a':
                            $(this).parent('li').siblings().children('a').removeClass(plugin.settings.activeGameTypeClass);
                            $(this).addClass(plugin.settings.activeGameTypeClass);
                            break;
                    }

                    var gameTypeId = $(this).data('gameTypeId');

                    plugin.load(gameTypeId);

                });

                plugin.load(data.list[0].id);

            });
        }

        plugin.load = function(gameTypeId) {

            if (plugin.cache[gameTypeId] == null) {

                var gamesUrl = site + plugin.settings.gamesUrl + gamePlatformId + '/' + gameTypeId;

                $.getJSON(gamesUrl, function(data) {
                    
                    plugin.cache[gameTypeId] = {
                        p: data.p,
                        list: data.l,
                    };

                    plugin.load(gameTypeId);
                });

            } else {

                if (plugin.templates['gameDescription'] == null) {
                    plugin.templates['gameDescription'] = _.template(plugin.settings.gameTemplate);
                }

                $(plugin.settings.gameContainer).html(plugin.templates['gameDescription'](plugin.cache[gameTypeId]));

            }
            
        }

        plugin.init();

    };

})(jQuery);
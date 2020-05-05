var lottery_container_name = 'embedded_lottery_game_container';
var lottery_iframe_name = 'embedded_lottery_game_iframe';
var history_pushstate_start = false;

function create_iframe_container(url){
    var iframe = $('<iframe>');
    iframe.attr('width', '100%');
    iframe.attr('height', '100%');
    iframe.addClass('iframe-container');
    iframe.attr('src', (url === undefined) ? 'javascript: void(0)' : url);

    return iframe;
}

function embed_lottery(url, callback){
    var container = null;
    if($('.' + lottery_container_name).length <= 0){
        container = $('<div>');
        container.addClass('col-md-12 nopadding ' + lottery_container_name);
        container.appendTo($('.dashboar-container .member-center'));
        container.hide();
    }else{
        container = $('.' + lottery_container_name);
    }

    var iframe;
    if($('#' + lottery_iframe_name).length){
        if(typeof callback === "function") callback(container, false);
    }else{
        iframe = create_iframe_container();
        iframe.attr('id', lottery_iframe_name);
        iframe.attr('src', url);

        container.empty().append(iframe);
        if(typeof callback === "function") callback(container, true);
    }
}

function embed_iframe(url, callback){
    var iframe = create_iframe_container(url);
    $('.dashboar-container .member-center .mc-content').empty().append(iframe);

    // Loader.show('.mc-content');
    iframe.on('load', function(){
        // Loader.hide();
        $("html, body").animate({ scrollTop: 0 });

        if(typeof callback === "function") callback.apply(window, [iframe]);
    });
}

function generate_embedded_url(target, origin_url, real_url){
    var url;
    var history_url;
    var base = '/player_center/embed';

    switch(target){
        case 'agency':
            history_url = base + '/agency' + origin_url;
            url = real_url;
            break;
        case 'lottery':
            history_url = origin_url;
            url = origin_url.replace(base + '/player', '');
            break;
        case 'player':
            history_url = base + '/player' + origin_url;
            url = real_url;
            break;
    }

    return [url, history_url];
}

function history_pushState(title, data, target, origin_url, real_url, callback){
    var urls = generate_embedded_url(target, origin_url, real_url);

    if(typeof window.history.pushState === "function"){
        if(!history_pushstate_start){
            window.history.pushState("refresh", null, window.location.href);
            history_pushstate_start = true;
        }

        if(typeof callback === "function") callback.apply(window, [data]);
        window.history.pushState(JSON.stringify(data), title, urls[1]);
    }else{
        if(typeof callback === "function") callback.apply(window, [false]);
        window.location.href = urls[1];
    }
}

function history_pushState_callback(data, target, origin_url, real_url){
    if(!data){
        return false;
    }

    if(!data.hasOwnProperty('data')){
        return false;
    }

    var embedded_data = data['data'];

    if(target === undefined){
        if(!embedded_data.hasOwnProperty('target')){
            return false;
        }
        target = embedded_data['target'];
    }

    var url = null;
    if(origin_url === undefined){
        if(!data.hasOwnProperty('url') || !data.hasOwnProperty('real_url')){
            return false;
        }

        var urls = generate_embedded_url(target, data['url'], data['real_url']);
        url = urls[0];
    }

    if(target === "lottery"){
        var lottery_id = (embedded_data.hasOwnProperty('lotteryId')) ? embedded_data['lotteryId'] : ((embedded_data.hasOwnProperty('lottery-id')) ? embedded_data['lottery-id'] : null);

        if(!lottery_id){
            return false;
        }

        $('.dashboar-container .member-center .mc-content').hide();

        embed_lottery(url, function(container, is_init){
            container.show();

            if(is_init){
                return;
            }

            if(!lottery_id){
                return;
            }
            var lottery_tmp = lottery_id.split('-');
            if(lottery_tmp.length < 2){
                return;
            }

            switch(lottery_tmp[0]){
                case 'official':
                    window.t1t_lottery.navigateGame(lottery_tmp[1], lottery_tmp[0]);
                    break;
                case 'double':
                    window.t1t_lottery.navigateGame(lottery_tmp[1], lottery_tmp[0]);
                    break;
                default:
                    window.t1t_lottery.navigateGame(lottery_tmp[1]);
                    break;
            }
        });
    }else{
        $('.dashboar-container .member-center .mc-content').show();
        $('.' + lottery_container_name).hide();
        embed_iframe(url, function(){
        });
    }
}

function load_lottery_game_on_document_ready(){
    var lottery_game_link_list = $('[data-lottery-id]');
    if(lottery_game_link_list.length <= 0){
        return false;
    }

    var lottery_game = lottery_game_link_list[0];

    var urls = generate_embedded_url($(lottery_game).data('target'), lottery_game.pathname, lottery_game.href);

    embed_lottery(urls[0]);
}

$(function(){
    $(window).on('beforeunload', function(){
        show_loading();
    });

    $(window).bind('popstate', function(e){
        var data = null;

        try {
            data = JSON.parse(e.originalEvent.state);
        }catch(e){
            switch(e.originalEvent.state){
                case 'refresh':
                default:
                    window.location.reload();
                    break;
            }
            return false;
        }

        if(!data){
            return false;
        }

        history_pushState_callback(data);
    });

    $('.agency-center-navigation a').each(function(){
        if($(this).data('embedded')){
            return true;
        }
        $(this).attr('data-embedded', '1');
        $(this).attr('data-target', 'agency');
    });

    $('.main-nav-toggle a').on('click', function(){
        $(this).toggleClass('active');
        $('.main-navigation-container').toggleClass('active');
        $('.player-center-main-content').toggleClass('main-navigation-collapse');
    });

    $('[data-embedded]').on('click', function(e){
        if(!$(this).data('embedded')){
            return true;
        }

        if($(this).prop('disabled')){
            return false;
        }

        e.stopPropagation();

        var target = $(this).data('target');

        history_pushState(null, {
            "url": $(this)[0].pathname,
            "real_url": $(this)[0].href,
            "data": $(this).data()
        }, target, $(this)[0].pathname, $(this)[0].href, history_pushState_callback);
        return false;
    });

    $('.player-stats-block .player-stats-refresh-balance').on('click', function(){
        var self = this;

        var refresh_balance_timer = $(this).data('refresh_balance_timer');
        var refresh_balance_retry_times = 10;
        var refresh_balance_retry_count = $(this).data('refresh_balance_retry_count');

        $(this).prop('disabled', true).addClass('disabled').attr('disabled', 'disabled');
        $('i', $(this)).addClass('fa-spin fa-fw');

        if(refresh_balance_timer){
            clearTimeout(refresh_balance_timer);
        }

        refresh_balance_timer = setTimeout(function(){
            if(typeof _export_sbe_t1t === "object"){
                _export_sbe_t1t.renderUI.refreshPlayerBalance(function(){
                    $(self).prop('disabled', false).removeClass('disabled').removeAttr('disabled');
                    $('i', $(self)).removeClass('fa-spin fa-fw');
                });
            }else{
                refresh_balance_retry_count++;
                $(this).data('refresh_balance_retry_count', refresh_balance_retry_count);

                if(refresh_balance_retry_count >= refresh_balance_retry_times){
                    clearTimeout(refresh_balance_timer);
                    $(self).prop('disabled', false).removeClass('disabled').removeAttr('disabled');
                    $('i', $(self)).removeClass('fa-spin fa-fw');
                }else{
                    $(self).trigger('click');
                }
            }
        }, 300);
    });

    $('header .dropdown .dropdown-menu').on('click', function(e){
        e.stopPropagation();
    });

    $('header .dropdown .dropdown-menu a').on('click', function(e){
        $('[data-toggle="dropdown"]', $(this).closest('.dropdown')).trigger('click');
    });

    $('body').on('t1t.member-center.load', function(){
        $('.dashboar-container .member-center .mc-content').show();
        $('.' + lottery_container_name).hide();
    });

    load_lottery_game_on_document_ready();
});
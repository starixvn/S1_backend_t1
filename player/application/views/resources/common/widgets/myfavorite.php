<div class="t1t-widget widget-myfavorite panel panel-default">
    <div class="panel-heading clearfix">
        <h4 class="panel-title pull-left"><?=lang('Favorite Games')?></h4>
        <div class="btn-group pull-right">
            <a href="javascript: void(0);" class="btn show_player_favorite_modal"><i class="fa fa-plus-square" aria-hidden="true"></i></a>
        </div>
    </div>
    <div class="panel-body">
        <?php if($favorites): ?>
            <ul class="list-unstyled">
                <?php foreach($favorites as $game): ?>
                <li>
                    <a href="<?=$game['url']?>">
                        <img src="<?=$game['image_url']?>">
                        <span class="text"><?=$game['game_name']?></span>
                    </a>
                </li>
                <?php endforeach ?>
            </ul>
        <?php else: ?>
        <?php endif ?>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="myfavoriteModal" tabindex="-1" role="dialog" aria-labelledby="myfavoriteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title inline-block"><?=lang('Favorite Games')?><span class="player-myfavorite-count-hint-text"><span class="current">0</span> / <span class="limit"><?=$player_myfavorite_limit_count?></span></span></h4>
            </div>
            <div class="modal-body">
                <div class="game-filter">

                </div>
                <div class="game-list">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?=lang('Cancel');?></button>
                <button type="button" class="btn btn-primary btn-save"><?=lang('Save')?></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(function(){
    var player_myfavorite_games = [];

    var player_allow_myfavorite_games_limit_count = <?=$player_myfavorite_limit_count;?>;

    var modal = $('#myfavoriteModal').modal({
        "show": false
    });

    function load_player_myfavorite_games(deferred){
        $.ajax('/api/playerFavoriteGames', {
            "success": function(data){
                if(!data.status){
                    return deferred.reject();
                }

                return deferred.resolve(data.game_list);
            },
            "error": function(){
                return deferred.reject();
            }
        });
    }

    function save_player_myfavorite_games(deferred, data){
        $.ajax('/api/playerSaveFavoriteGames', {
            "contentType": "application/x-www-form-urlencoded; charset=UTF-8",
            "type": "POST",
            "data": data,
            "success": function(data){
                if(!data.status){
                    return deferred.reject();
                }

                return deferred.resolve(data);
            },
            "error": function(){
                return deferred.reject();
            }
        });
    }

    function render_myfavorite_game_list(){
        var container = $('#myfavoriteModal .modal-body .game-list');

        container.empty();

        $.each(player_myfavorite_games, function(key, game){
            var element = render_game_entry(game);

            console.log(game);

            container.append(element);
        });

        $('.game-entry', container).on('click', function(){
            var player_myfavorite_count = get_player_myfavorite_count();
            if(player_myfavorite_count < player_allow_myfavorite_games_limit_count){
                $(this).toggleClass('active');
            }else{
                $(this).removeClass('active');
            }

            render_player_myfavorite_count();
        });

        render_player_myfavorite_count();
    }

    function get_player_myfavorite_count(){
        var container = $('#myfavoriteModal .modal-body .game-list .game-entry.active');

        return container.length;
    }

    function render_player_myfavorite_count(){
        $('.player-myfavorite-count-hint-text .current').html(get_player_myfavorite_count());
    }

    function render_game_entry(game){
        var element = $('<div>');
        element.addClass('game-entry');
        element.attr('data-game-id', game.id);

        var img_container = $('<div>').appendTo(element);
        img_container.addClass('game-icon');

        var img = $('<img>').appendTo(img_container);
        img.attr('src', game.image_url);

        var text_container = $('<div>').appendTo(element);
        text_container.addClass('game-text');
        text_container.append($('<span>').html(game.game_name));

        var flag_container = $('<div>').appendTo(element);
        flag_container.addClass('game-flag');

        flag_container.append($('<div>').addClass('game-flag-dlc').addClass((game.dlc_enabled) ? 'active' : ''));
        flag_container.append($('<div>').addClass('game-flag-new').addClass((game.flag_new_game) ? 'active' : ''));
        flag_container.append($('<div>').addClass('game-flag-flash').addClass((game.flash_enabled) ? 'active' : ''));
        flag_container.append($('<div>').addClass('game-flag-mobile').addClass((game.mobile_enabled) ? 'active' : ''));
        flag_container.append($('<div>').addClass('game-flag-offline').addClass((game.offline_enabled) ? 'active' : ''));
        flag_container.append($('<div>').addClass('game-flag-favorite').addClass((game.favorite) ? 'active' : ''));

        if(game.favorite){
            element.addClass('active');
        }

        return element;
    }

    modal.on('shown.bs.modal', function(){

        var deferred = $.Deferred();

        show_loading();

        $('#myfavoriteModal .modal-body').css('height', $(window).outerHeight() - $('#myfavoriteModal .modal-header').outerHeight() - $('#myfavoriteModal .modal-footer').outerHeight());

        deferred.done(function(result){
            player_myfavorite_games = result;

            render_myfavorite_game_list();
        }).fail(function(){
            console.log('fail');
            modal.modal('hide');
        }).always(function(){
            stop_loading();
        });

        if(player_myfavorite_games.length <= 0){
            load_player_myfavorite_games(deferred);
        }else{
            deferred.resolve(player_myfavorite_games);
        }
    });

    $('.show_player_favorite_modal').on('click', function(){
        modal.modal('show');
    });

    $('#myfavoriteModal .btn-save').on('click', function(){
        show_loading();

        var select_player_myfavorite_game_list = [];

        $('#myfavoriteModal .modal-body .game-list .game-entry.active').each(function(){
            var game_id = $(this).data('game-id');

            select_player_myfavorite_game_list.push(game_id);
        });

        var deferred = $.Deferred();

        deferred.done(function(result){
        }).always(function(){
            stop_loading();

            modal.modal('hide');

            window.location.reload();
        });

        var data = {
            'game_ids': select_player_myfavorite_game_list
        };

        save_player_myfavorite_games(deferred, data);
    });
});
</script>
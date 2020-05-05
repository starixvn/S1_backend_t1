
var host;
$("script").each(function(i,v) {
    var url = $(v).attr('src');
    if (url.match(/\/resources\/js\/.+lobby.js$/)) {
        host = url.replace(/\/resources\/js\/.+lobby.js$/, '');
        return false;
    }
})

$(function() {
    var GAMEPLAY_API = 24;
    loadGames($("#arcades_btn").attr('category'));

    $("#arcades_btn").click(function(){
       loadGames($("#arcades_btn").attr('category'));
    });

    $("#cards_game_btn").click(function(){
       loadGames($("#cards_game_btn").attr('category'));
    });

    $("#slots_btn").click(function(){
       loadGames($("#slots_btn").attr('category'));
    });

    $("#table_btn").click(function(){
       loadGames($("#table_btn").attr('category'));
    });

    $("#videopoker_btn").click(function(){
       loadGames($("#videopoker_btn").attr('category'));
    });

    $(document).ready(function(){
        $('#main-iframe').attr('url');
    });

    function loadGames(gameType){
        $.getJSON(host + '/game_description/allGames/'+GAMEPLAY_API+'/'+gameType, function(data) {
            localStorage['jsoncache'] = JSON.stringify(data);
            var templateStr = $('#game-template').html();
            var template = _.template(templateStr);
            var html = template(data);
            $('#game-list_'+gameType).html(html);
            $(parent.window.document.getElementById('main-iframe')).height($('#container').height() + 'px');
        });
    }
});
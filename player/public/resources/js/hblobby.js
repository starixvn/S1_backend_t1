
var host;
$("script").each(function(i,v) {
    var url = $(v).attr('src');
    if (url.match(/\/resources\/js\/.+lobby.js$/)) {
        host = url.replace(/\/resources\/js\/.+lobby.js$/, '');
        return false;
    }
})

$(parent.document).ready(function() {

    loadGames(140);

    $("#baccarat_btn").click(function(){
       loadGames(140);
    });

    $("#blackjack_btn").click(function(){
       loadGames(141);
    });

    $("#roulette_btn").click(function(){
       loadGames(144);
    });

    $("#videopoker_btn").click(function(){
       loadGames(146);
    });

    $("#gamble_btn").click(function(){
       loadGames(143);
    });

    $("#casinopoker_btn").click(function(){
       loadGames(142);
    });

    $("#videoslots_btn").click(function(){
       loadGames(139);
    });

    $("#sicbo_btn").click(function(){
       loadGames(145);
    });
    function loadGames(gameType){
        // console.log(host + '/game_description/allGames/38/'+gameType);
        $.getJSON(host + '/game_description/allGames/38/'+gameType, function(data) {
            var templateStr = $('#game-template').html();
            var template = _.template(templateStr);
            var html = template(data);
            $('#game-list_'+gameType).html(html);
            $(parent.window.document.getElementById('main-iframe')).height($('#container').height() + 'px');
        });
    }
});
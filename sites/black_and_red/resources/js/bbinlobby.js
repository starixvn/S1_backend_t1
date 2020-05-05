
var host;
$("script").each(function(i,v) {
    var url = $(v).attr('src');
    if (url.match(/\/resources\/js\/.+lobby.js$/)) {
        host = url.replace(/\/resources\/js\/.+lobby.js$/, '');
        return false;
    }
})

$(function() {
    var SPORTS_GAME_TYPE = 33;
    var LOTTERY_GAME_TYPE = 34;
    var THREE_D_HALL_GAME_TYPE = 35;
    var LIVE_GAME_TYPE = 36;
    var CASINO_GAME_TYPE = 37;

    loadGames(SPORTS_GAME_TYPE);

    $("#sports_btn").click(function(){
       loadGames(SPORTS_GAME_TYPE);
    });
    $("#lottery_btn").click(function(){
       loadGames(LOTTERY_GAME_TYPE);
    });
    $("#threedhall_btn").click(function(){
       loadGames(THREE_D_HALL_GAME_TYPE);
    });
    $("#live_btn").click(function(){
       loadGames(LIVE_GAME_TYPE);
    });
    $("#casino_btn").click(function(){
       loadGames(CASINO_GAME_TYPE);
    });   

    function loadGames(gameType){
        $.getJSON(host + '/game_description/allGames/8/'+gameType, function(data) {
            //localStorage['jsoncache'] = JSON.stringify(data);
            var templateStr = $('#game-template').html();
            var template = _.template(templateStr);
            var html = template(data);
            $('#game-list_'+gameType).html(html);
            $(parent.window.document.getElementById('main-iframe')).height($('#container').height() + 'px');
        });
    }
});

var host;
$("script").each(function(i,v) {
    var url = $(v).attr('src');
    if (url.match(/\/resources\/js\/.+lobby.js$/)) {
        host = url.replace(/\/resources\/js\/.+lobby.js$/, '');
        return false;
    }
})

$(function() {
    var SLOT_GAME_TYPE = 68;
    var TABLE_GAME_TYPE = 69;
    
    loadGames(SLOT_GAME_TYPE);

    $("#slot_btn").click(function(){
       loadGames(SLOT_GAME_TYPE);
    });

    $("#table_btn").click(function(){
       loadGames(TABLE_GAME_TYPE);
    });   

    function loadGames(gameType){
        $.getJSON(host + '/game_description/allGames/24/'+gameType, function(data) {
            //localStorage['jsoncache'] = JSON.stringify(data);
            var templateStr = $('#game-template').html();
            var template = _.template(templateStr);
            var html = template(data);
            $('#game-list_'+gameType).html(html);
            $(parent.window.document.getElementById('main-iframe')).height($('#container').height() + 'px');
        });
    }
});

var host;
$("script").each(function(i,v) {
    var url = $(v).attr('src');
    if (url.match(/\/resources\/js\/.+lobby.js$/)) {
        host = url.replace(/\/resources\/js\/.+lobby.js$/, '');
        return false;
    }
})
    
$(function() {
    var KENO_GAME_TYPE = 39;

    loadGames(KENO_GAME_TYPE);

    $("#lbkeno_btn").click(function(){
       loadGames(KENO_GAME_TYPE);
    });

    function loadGames(gameType){
        $.getJSON(host + '/game_description/allGames/10/'+gameType, function(data) {
            //localStorage['jsoncache'] = JSON.stringify(data);
            var templateStr = $('#game-template').html();
            var template = _.template(templateStr);
            var html = template(data);
            $('#game-list_'+gameType).html(html);
            $(parent.window.document.getElementById('main-iframe')).height($('#container').height() + 'px');
        });
    }
});
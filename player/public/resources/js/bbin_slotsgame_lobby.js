
var host;
$("script").each(function(i,v) {
    var url = $(v).attr('src');
    if (url.match(/\/resources\/js\/.+lobby.js$/)) {
        host = url.replace(/\/resources\/js\/.+lobby.js$/, '');
        return false;
    }
})

$(function() {
    var BBIN_GAME = 8;
    loadGames($("#sports_btn").attr('category'));

    $("#sports_btn").click(function(){
       loadGames($("#sports_btn").attr('category'));
    });
    $("#lottery_btn").click(function(){
       loadGames($("#lottery_btn").attr('category'));
    });
    $("#threedhall_btn").click(function(){
       loadGames($("#threedhall_btn").attr('category'));
    });
    $("#casino_btn").click(function(){
       loadGames($("#casino_btn").attr('category'));
    });   

    function loadGames(gameType){
        $.getJSON(host + '/game_description/allGames/'+BBIN_GAME+'/'+gameType, function(data) {
            //localStorage['jsoncache'] = JSON.stringify(data);
            var templateStr = $('#game-template').html();
            var template = _.template(templateStr);
            var html = template(data);
            $('#game-list_'+gameType).html(html);
            $(parent.window.document.getElementById('main-iframe')).height($('#container').height() + 'px');
        });
    }
});
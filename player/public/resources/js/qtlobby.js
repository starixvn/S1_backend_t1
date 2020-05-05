
var host;
$("script").each(function(i,v) {
    var url = $(v).attr('src');
    if (url.match(/\/resources\/js\/.+lobby.js$/)) {
        host = url.replace(/\/resources\/js\/.+lobby.js$/, '');
        return false;
    }
})

$(function() {
    var QT_API = 42;
    loadGames($("#slots_btn").attr('category'),"nomini");

    $("#slots_btn").click(function(){
       loadGames($("#slots_btn").attr('category'),"nomini");
    });
    $("#table_btn").click(function(){
       loadGames($("#table_btn").attr('category'),"");
    });
    $("#vidpoker_btn").click(function(){
       loadGames($("#vidpoker_btn").attr('category'),"");
    });
    $("#scratchcard_btn").click(function(){
       loadGames($("#scratchcard_btn").attr('category'),"");
    });
    $("#mini_btn").click(function(){
       loadGames($("#mini_btn").attr('category'),'mini');
    });

    $(document).ready(function(){
        // $('#main-iframe').attr('url',"");
    });

    function loadGames(gameType,extra){
        $.getJSON(host + '/game_description/allGames/'+QT_API+'/'+gameType+'/'+extra, function(data) {
            localStorage['jsoncache'] = JSON.stringify(data);
            var templateStr = $('#game-template').html();
            var template = _.template(templateStr);
            var html = template(data);
            if(extra == 'mini'){
                $('#game-list_0').html(html);
            }else{
                $('#game-list_'+gameType).html(html);
            }

            $(parent.window.document.getElementById('main-iframe')).height($('#container').height() + 'px');
        });
    }
});
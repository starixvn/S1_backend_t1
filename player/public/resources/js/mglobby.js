
var host;
$("script").each(function(i,v) {
    var url = $(v).attr('src');
    if (url.match(/\/resources\/js\/.+lobby.js$/)) {
        host = url.replace(/\/resources\/js\/.+lobby.js$/, '');
        return false;
    }
})
    
$(parent.document).ready(function() {
                
    loadGames(14);

    $("#slots_btn").click(function(){
       loadGames(14);
    });
    $("#tbgames_btn").click(function(){
       loadGames(13);
    });
    $("#vidpokers_btn").click(function(){
       loadGames(15);
    });
    $("#progressives_btn").click(function(){
       loadGames(16);
    });
    $("#scratchcards_btn").click(function(){
       loadGames(17);
    });
    $("#others_btn").click(function(){
       loadGames(18);
    });
    $("#livegames_btn").click(function(){
       loadGames(18);
    });

    function loadGames(gameType){
        $.getJSON(host + '/game_description/allGames/6/'+gameType, function(data) {
            var templateStr = $('#game-template').html();
            var template = _.template(templateStr);
            var html = template(data);
            $('#game-list_'+gameType).html(html);
            $(parent.window.document.getElementById('main-iframe')).height($('#container').height() + 'px');
        });
    }
});
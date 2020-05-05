
var host;
$("script").each(function(i,v) {
    var url = $(v).attr('src');
    if (url.match(/\/resources\/js\/.+lobby.js$/)) {
        host = url.replace(/\/resources\/js\/.+lobby.js$/, '');
        return false;
    }
})
    
$(function() {

    loadGames();

    $("#sports_btn").click(function(){
       loadGames();
    });

    function loadGames(){
        $.getJSON(host + '/game_description/allGames/11', function(data) {
            var templateStr = $('#game-template').html();
            var template = _.template(templateStr);
            var html = template(data);
            $('#game-list').html(html);
            $(parent.window.document.getElementById('main-iframe')).height($('#container').height() + 'px');
        });
    }
});
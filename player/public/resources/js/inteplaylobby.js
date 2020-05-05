
var host;
$("script").each(function(i,v) {
    var url = $(v).attr('src');
    if (url.match(/\/resources\/js\/.+lobby.js$/)) {
        host = url.replace(/\/resources\/js\/.+lobby.js$/, '');
        return false;
    }
})
    
$(function() {

    $.getJSON(host + '/game_description/allGames/19', function(data) {
        localStorage['jsoncache'] = JSON.stringify(data);
        var templateStr = $('#game-template').html();
        var template = _.template(templateStr);
        var html = template(data);
        $('#game-list').html(html);
        $(parent.window.document.getElementById('main-iframe')).height($('#container').height() + 'px');
    });
});
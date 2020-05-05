
var host;
$("script").each(function(i,v) {
    var url = $(v).attr('src');
    if (url.match(/\/resources\/js\/.+lobby.js$/)) {
        host = url.replace(/\/resources\/js\/.+lobby.js$/, '');
        return false;
    }
})
    
$(parent.document).ready(function() {
    
    $('body').on('click', '.category > a', function() {
        var id = $(this).attr('id');
        $(this).tab('show');
        loadGames(id);
    });

    $.getJSON(host + '/game_description/gameTypes/43', function(data) {
        var templateStr = $('#ttg-lobby').html();
        var template = _.template(templateStr);
        var html = template(data);
        $('body').html(html);
        loadGames(data.list[0].id);
    });

    function loadGames(gameType){
        $.getJSON(host + '/game_description/allGames/43/' + gameType, function(data) {
            var templateStr = $('#game-template').html();
            var template = _.template(templateStr);
            var html = template(data);
            $('#game-list').html(html);
            $(parent.window.document.getElementById('main-iframe')).height($('body').height() + 'px');
        });
    }
});
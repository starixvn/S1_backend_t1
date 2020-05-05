
var host;
$("script").each(function(i,v) {
    var url = $(v).attr('src');
    if (url.match(/\/resources\/js\/.+lobby.js$/)) {
        host = url.replace(/\/resources\/js\/.+lobby.js$/, '');
        return false;
    }
})
    
$(function() {
    loadGames($("#slots_btn").attr('category'));

    $("#slots_btn").click(function(){
       loadGames($("#slots_btn").attr('category'));
    });

    $("#table_btn").click(function(){
       loadGames($("#table_btn").attr('category'));
    });   

   
});

 function loadGames(gameType){
        $.getJSON(host + '/game_description/allGames/21/'+gameType, function(data) {
            //console.log(gameType);// console.log(data);
            localStorage['jsoncache'] = JSON.stringify(data);
            var templateStr = $('#game-template').html();
            var template = _.template(templateStr);
            var html = template(data);
            $('#game-list_'+gameType).html(html);
            $(parent.window.document.getElementById('main-iframe')).height($('#container').height() + 'px');
        });
}
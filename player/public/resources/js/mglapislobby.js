
var host;
$("script").each(function(i,v) {
    var url = $(v).attr('src');
    if (url.match(/\/resources\/js\/.+lobby.js$/)) {
        host = url.replace(/\/resources\/js\/.+lobby.js$/, '');
        return false;
    }
})

$(function() {

    loadGames($("#slot_btn").attr('category'));

    $("#slot_btn").click(function(){
       loadGames($("#slot_btn").attr('category'));
    });

    $("#classic_slot_btn").click(function(){
       loadGames($("#classic_slot_btn").attr('category'));
    });

    $("#video_slot_btn").click(function(){
       loadGames($("#video_slot_btn").attr('category'));
    });   

    $("#feature_slot_btn").click(function(){
       loadGames($("#feature_slot_btn").attr('category'));
    });

    $("#bonus_slot_btn").click(function(){
       loadGames($("#bonus_slot_btn").attr('category'));
    });

    $("#advanced_slot_btn").click(function(){
       loadGames($("#advanced_slot_btn").attr('category'));
    });

    $("#progressive_slot_btn").click(function(){
       loadGames($("#progressive_slot_btn").attr('category'));
    });

    $("#slot_html5_btn").click(function(){
       loadGames($("#slot_html5_btn").attr('category'));
    });

    $("#table_btn").click(function(){
       loadGames($("#table_btn").attr('category'));
    });

    $("#table_gold_btn").click(function(){
       loadGames($("#table_gold_btn").attr('category'));
    });

    $("#table_premier_btn").click(function(){
       loadGames($("#table_premier_btn").attr('category'));
    });   

    $("#live_dealer_btn").click(function(){
       loadGames($("#live_dealer_btn").attr('category'));
    });

    $("#live_dealer_html5_btn").click(function(){
       loadGames($("#live_dealer_html5_btn").attr('category'));
    });

    $("#video_poker_btn").click(function(){
       loadGames($("#video_poker_btn").attr('category'));
    });

    $("#poker_4play_btn").click(function(){
       loadGames($("#poker_4play_btn").attr('category'));
    });

    $("#scratch_card_btn").click(function(){
       loadGames($("#scratch_card_btn").attr('category'));
    });

    $("#casual_btn").click(function(){
       loadGames($("#casual_btn").attr('category'));
    });

    $("#multihand_gold_series_btn").click(function(){
       loadGames($("#multihand_gold_series_btn").attr('category'));
    });

    $("#parlor_btn").click(function(){
       loadGames($("#parlor_btn").attr('category'));
    });

    function loadGames(gameType){

        $.getJSON(host + '/game_description/allGamesByWhere/55/game_type_lang/'+gameType, function(data) {
            //localStorage['jsoncache'] = JSON.stringify(data);
            var templateStr = $('#game-template').html();
            var template = _.template(templateStr);
            var html = template(data);
            $('#game-list_'+gameType).html(html);
            $(parent.window.document.getElementById('main-iframe')).height($('#container').height() + 'px');
        });
    }
});
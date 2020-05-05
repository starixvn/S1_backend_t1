
var host;
$("script").each(function(i,v) {
    var url = $(v).attr('src');
    if (url.match(/\/resources\/js\/.+lobby.js$/)) {
        host = url.replace(/\/resources\/js\/.+lobby.js$/, '');
        return false;
    }
})

$(function() {

    loadGames($("#nyx_slot_btn").attr('category'));

    $("#nyx_slot_btn").click(function(){
       loadGames($("#nyx_slot_btn").attr('category'));
    });

    $("#nyx_minislot_btn").click(function(){
       loadGames($("#nyx_minislot_btn").attr('category'));
    });

    $("#nyx_table_btn").click(function(){
       loadGames($("#nyx_table_btn").attr('category'));
    });   

    $("#png_slot_btn").click(function(){
       loadGames($("#png_slot_btn").attr('category'));
    });

    $("#png_gridslot_btn").click(function(){
       loadGames($("#png_gridslot_btn").attr('category'));
    });

    $("#png_table_btn").click(function(){
       loadGames($("#png_table_btn").attr('category'));
    });

    $("#png_vid_poker_btn").click(function(){
       loadGames($("#png_vid_poker_btn").attr('category'));
    });

    $("#png_scratch_btn").click(function(){
       loadGames($("#png_scratch_btn").attr('category'));
    });

    $("#png_mini_btn").click(function(){
       loadGames($("#png_mini_btn").attr('category'));
    });

    $("#png_other_btn").click(function(){
       loadGames($("#png_other_btn").attr('category'));
    });

    $("#playson_slot_btn").click(function(){
       loadGames($("#playson_slot_btn").attr('category'));
    });   

    $("#playson_table_btn").click(function(){
       loadGames($("#playson_table_btn").attr('category'));
    });

    $("#playson_vid_poker_btn").click(function(){
       loadGames($("#playson_vid_poker_btn").attr('category'));
    });

    $("#playson_slot_mobile_btn").click(function(){
       loadGames($("#playson_slot_mobile_btn").attr('category'));
    });

    $("#playson_table_mobile_btn").click(function(){
       loadGames($("#playson_table_mobile_btn").attr('category'));
    });

    $("#playson_video_poker_mobile_btn").click(function(){
       loadGames($("#playson_video_poker_mobile_btn").attr('category'));
    });

    function loadGames(gameType){
        $.getJSON(host + '/game_description/allGames/51/'+gameType, function(data) {
            //localStorage['jsoncache'] = JSON.stringify(data);
            var templateStr = $('#game-template').html();
            var template = _.template(templateStr);
            var html = template(data);
            $('#game-list_'+gameType).html(html);
            $(parent.window.document.getElementById('main-iframe')).height($('#container').height() + 'px');
        });
    }
});
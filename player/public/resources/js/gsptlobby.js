
var host;
$("script").each(function(i,v) {
    var url = $(v).attr('src');
    if (url.match(/\/resources\/js\/.+lobby.js$/)) {
        host = url.replace(/\/resources\/js\/.+lobby.js$/, '');
        return false;
    }
})

$(function() {
           $.getJSON(host + '/game_description/allGames/22', function(data) {
          //  $.getJSON('http://og.local/game_description/allGames/22', function(data) { console.log(data);
            localStorage['jsoncache'] = JSON.stringify(data);
            var templateStr = $('#game-template').html();
            var template = _.template(templateStr);
            var html = template(data);
            $('#game-list').html(html);
              renderGames();
        });


    $('#category-list').on('click', '.category:not(.active) a', function() {
        $('.category').removeClass('active');
        $(this).parent('li').addClass('active');
          renderGames();
    });


//NOTE This is for #3TBet.com
$('.gsptid-cat').click(function(){

        var id= $(this).attr('gsptid');
      renderGames(id);
});




});








function renderGames(id) {
    // console.log('game typ id='+id);
    var start = Date.now(); // BENCHMARK
    var category = $('.category.active');
    var category_id='';
    var defaultCatID='87';

    if(id){
       category_id = id;
    }else{
      category_id = parseInt(category.prop('id'));
    }


    if(!category_id){
        category_id = defaultCatID;
    }

    $('.game-item').hide();
    $('.game-item').each( function(index, value) {

    var game = $(this);
    var gameTypeId = game.attr('game-type-id')

        if(category_id == gameTypeId){
            game.show();
        }
        if(gameTypeId == '56'){
            // console.log(value);
        }

    });


     // console.log('Rendering Time: ' + (Date.now() - start) + ' ms'); // BENCHMARK

    $(parent.window.document.getElementById('main-iframe')).height($('#container').height() + 'px');

};


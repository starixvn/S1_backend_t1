
var host;
$("script").each(function(i,v) {
    var url = $(v).attr('src');
    if (url.match(/\/resources\/js\/.+lobby.js$/)) {
        host = url.replace(/\/resources\/js\/.+lobby.js$/, '');
        return false;
    }
})

var constants = {
    'slots': 7,
};

$(function() {
    // if ( ! localStorage['jsoncache']) {
        $.getJSON(host + '/game_description/allGames/1', function(data) {
            localStorage['jsoncache'] = JSON.stringify(data);
            var templateStr = $('#game-template').html();
            var template = _.template(templateStr);
            var html = template(data);
            $('#game-list').html(html);
            renderCategories();
            renderGames();
        });
    // } else {
    //     var data = JSON.parse(localStorage['jsoncache']);
    //     var templateStr = $('#game-template-2').html();
    //     var template = _.template(templateStr);
    //     var html = template(data);
    //     $('#game-list').html(html);
    //     renderCategories();
    //     renderGames();
    // }

    $('#category-list').on('click', '.category:not(.active) a', function() {
        $('.category').removeClass('active');
        $(this).parent('li').addClass('active');
        renderCategories();
        renderGames();
    });

    $('#filter-1 input:checkbox').click(renderGames);

});

function renderCategories() {

    var category = $('.category.active');
    var category_id = parseInt(category.prop('id'));

    $('#filter-1 input:checkbox').attr('checked', false);

    if (category_id == constants.slots) {
        $('#filter-1').show();
    } else {
        $('#filter-1').hide();
    }

}

function renderGames() {
    var start = Date.now(); // BENCHMARK
    var category = $('.category.active');
    var category_id = parseInt(category.prop('id'));

    $('.game-item').hide();

    if (category_id == constants.slots) {

        var groupA = $('#groupA input:checkbox:checked').map(function() {
            return parseInt(this.value);
        }).get();

        var groupB = $('#groupB input:checkbox:checked').map(function() {
            return parseInt(this.value);
        }).get();

        var groupC = $('#groupC input:checkbox:checked').map(function() {
            return parseInt(this.value);
        }).get();

        var groupD = $('#groupD input:checkbox:checked').map(function() {
            return parseInt(this.value);
        }).get();

        var groupE = $('#groupE input:checkbox:checked').map(function() {
            return parseInt(this.value);
        }).get();

        $('.game-item').each( function(index, value) {
            game = $(value);
            var categories = game.data('g');
            if ($.inArray(category_id, categories) != -1 && $.arrayIntersect(game.data('a'), groupA) && $.arrayIntersect(game.data('b'), groupB) && $.arrayIntersect(game.data('c'), groupC) && $.arrayIntersect(game.data('d'), groupD) && $.arrayIntersect(game.data('e'), groupE)) {
                game.show();
            }
        });
    } else {
        $('.game-item').each( function(index, value) {
            game = $(value);
            if ($.inArray(category_id, game.data('g')) != -1) {
                game.show();
            }
        });
    }

    // console.log('Rendering Time: ' + (Date.now() - start) + ' ms'); // BENCHMARK

    $(parent.window.document.getElementById('main-iframe')).height($('#container').height() + 'px');

};

$.arrayIntersect = function(data, filter) {
    if (filter.length == 0) {
        return true; // IF NOTHING WAS CHECKED, SHOW ALL
    }

    if ( ! data) {
        return false;
    }

    for (var i = 0; i < filter.length; i++) {
        if(data.indexOf(filter[i]) != -1) {
            return true;
        }
    }

    return false;
};
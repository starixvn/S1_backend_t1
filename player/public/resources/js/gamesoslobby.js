
// var host;
// $("script").each(function(i,v) {
//     var url = $(v).attr('src');
//     if (url.match(/\/resources\/js\/.+lobby.js$/)) {
//         host = url.replace(/\/resources\/js\/.+lobby.js$/, '');
//         return false;
//     }
// })



$(document).ready(function() {

  loadGames(192,'#popular');


  $('#popular-btn').on('click', function(e){
   var sectionId =  $(this).attr('href');
     loadGames(192,sectionId);
     e.preventDefault();
  });

  $('#arcades-btn').on('click', function(e){
     var sectionId =  $(this).attr('href');
     loadGames(193,sectionId);
     e.preventDefault();
  });

  $('#card-games-btn').on('click', function(e){
     var sectionId =  $(this).attr('href');
     loadGames(194,sectionId);
     e.preventDefault();
  });

  $('#video-pokers-btn').on('click', function(e){
     var sectionId =  $(this).attr('href');
     loadGames(195,sectionId);
     e.preventDefault();
  });

  $('#table-games-btn').on('click', function(e){
     var sectionId =  $(this).attr('href');
     loadGames(196,sectionId);
     e.preventDefault();
  });

  $('#others-btn').on('click', function(e){
     var sectionId =  $(this).attr('href');
     loadGames(197,sectionId);
     e.preventDefault();
  });








    function loadGames(gameType,sectionId){
    //   $.getJSON(host + '/game_description/allGames/6/'+gameType, function(data) {
       $.getJSON('https://player.og.local/game_description/allGames/50/'+gameType, function(data) {
            // console.log(data)
            var templateStr = $('#game-template').html();
            var template = _.template(templateStr);
            var html = template(data);
        //  $('section').hide();
          $(sectionId).find('ul.games-list').html(html).show();
          $(parent.window.document.getElementById('main-iframe')).height(($(sectionId).height() +250) + 'px');
        });
    }
});

(function() {
    var urlName=document.location.hostname;
    var prefix= window.location.protocol+'//player.';
    var urlArr=urlName.split('.');
    if(urlArr.length>2){
        //remove first
        urlArr.shift();
        urlName=urlArr.join('.');
    }
    var player_js_url=prefix+urlName+'/pub/player_main_js/default/true/'+(''+Math.random()).substr(2,16);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.type='text/javascript'; g.async=true; g.defer=true; g.src=player_js_url; s.parentNode.insertBefore(g,s);

})();
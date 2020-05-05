var utils={
    safelog: function(msg){
        if(typeof(console)!='undefined' && typeof(console.log)!='undefined'){
            console.log(msg);
        }
    },
    buildErrorMessage:function(msg){
        alert(msg);
    }
};

$(function(){
    var currentgame = 'ngm';
    if(typeof mobile != 'undefined' && (mobile=='mobile' || mobile=='true')){
        // utils.safelog("7.) ismobile: "+mobile+"-"+gameCode);
        iapiSetClientPlatform("mobile&deliveryPlatform=HTML5");
    }

    iapiSetCallout('Login', calloutLogin);
    iapiSetCallout('GetTemporaryAuthenticationToken', calloutGetTemporaryAuthenticationToken);

    function calloutLogin(response){
        // utils.safelog("6.) response: "+response['errorCode']);

        if (response['errorCode']) {
            // utils.safelog(response);
            if(response['errorCode']==48){
                utils.buildErrorMessage('请在指定区域或使用VPN登录PT');
            }else if(response['errorCode']==12 && response['playerMessage']){
                utils.buildErrorMessage(response['playerMessage']);
            }else if(response['playerMessage']){
                utils.buildErrorMessage(response['playerMessage']);
            }else{
                utils.buildErrorMessage('登录PT失败');// = 'Login failed. ' + response.playerMessage;
            }
            $(".retry_link").show();
        } else {
            // var mobile=getParam('mobile');
            // var gameCode=getParam('game_code');
            if(typeof mobile != 'undefined' && (mobile=='mobile' || mobile=='true')){
                var realMode = 1;
                iapiRequestTemporaryToken(realMode, mobile_systemId, 'GamePlay');
            }else{
                window.location = api_play_pt +"/casinoclient.html?language="+lang+"&nolobby=1&game="+gameCode;
            }
        }
    }

    function calloutGetTemporaryAuthenticationToken(response) {
        if (response.errorCode) {
            // utils.safelog("8.) token failed!");
            alert("Token failed. " + response.playerMessage + " Error code: " + response.errorCode);
        }
        else {
            // utils.safelog("8.) sessionToken: "+response.sessionToken.sessionToken);
            launchMobileClient(response.sessionToken.sessionToken);
        }
    }

    function launchMobileClient(temptoken) {
        utils.safelog("temptoken: "+temptoken);
        $.ajax({
            url: player_url+'/async/get_user_info/1',
            type: 'GET',
            data: null,
            dataType: 'jsonp',
            cache: false
        }).done(
            function(data){
                //
                // utils.safelog("USER: "+data["key"]);
                // var lang=getParam('lang');
                // var clientUrl = mobile_launcher + '?gameId=' + currentgame + '&real=1' + '&username=' + data["key"].toUpperCase() + '&lang='+ lang + '&tempToken=' + temptoken + '&lobby=' + location.href.substring(0,location.href.lastIndexOf('/')+1) + '&support=' + location.href.substring(0,location.href.lastIndexOf('/')+1) + 'support.html' + '&logout=' + location.href.substring(0,location.href.lastIndexOf('/')+1) + 'logout.html';
                var clientUrl = mobile_launcher + 'igaming/?gameId=' + gameCode + '&real=1' + '&username=' + data["key"].toUpperCase() + '&lang='+ lang + '&tempToken=' + temptoken + '&lobby=' + location.href.substring(0,location.href.lastIndexOf('/')+1) + '&support=' + location.href.substring(0,location.href.lastIndexOf('/')+1) + 'support.html' + '&logout=' + location.href.substring(0,location.href.lastIndexOf('/')+1) + 'logout.html';
                utils.safelog("clientUrl: "+clientUrl);
                window.location = clientUrl;
            }
        );
    }

    function getParam(val) {
        var result = "Not found",
            tmp = [];
        var items = location.search.substr(1).split("&");
        for (var index = 0; index < items.length; index++) {
            tmp = items[index].split("=");
            if (tmp[0] === val) result = decodeURIComponent(tmp[1]);
        }
        return result;
    }

    function getUrlVars() {
        var vars = {};
        var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
        });
        return vars;

    }

    $.ajax({
        url: player_url+'/async/get_user_info/1',
        type: 'GET',
        data: null,
        dataType: 'jsonp',
        cache: false
    }).done(
        function(data){
        // utils.safelog("1.) ismobile: "+mobile);
        // utils.safelog("2.) data: "+data);
        // utils.safelog("3.) data['key']: "+data['key']);
        // utils.safelog("4.) data['secret']: "+data['secret']);
        // utils.safelog("5.) data['lang']: "+data['lang']);
        if(data && data['key'] && data['secret']){
            iapiLogin(data['key'].toUpperCase(), data['secret'], '1', data['lang']);
        }else{
            utils.buildErrorMessage('请重新登录');
        }
    }).fail(
        function(){
            utils.buildErrorMessage('加载PT失败');
        }
    );

});


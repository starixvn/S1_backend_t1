<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1,minimum-scale=1,maximum-scale=1" content="user-scalable=no">
    <title><?php echo @$platformName; ?></title>
    <?php if(empty($mobile_js_url)){ ?>
    <script type="text/javascript" src="<?php echo $api_play_pt_js; ?>"></script>
    <?php }else{?>
    <script type="text/javascript" src="<?php echo $mobile_js_url;?>"></script>
    <?php }?>

    <script type="text/javascript" src="<?php echo $this->utils->playerResUrl('jquery-1.11.3.min.js'); ?>"></script><link rel="shortcut icon" href="<?= !empty($favicon_brand) ? $favicon_brand : $this->utils->getPlayerCenterFaviconURL(); ?>" type="image/x-icon" />
    <style>
    *{padding:0;margin:0;}
    html , body {height:100%;overflow:hidden;}
    iframe{border:none;}
    #bodyMessage{padding:10px;}
    </style>
    <?php
    $min_width = !empty($set_min_width) ? 'style="min-width:'.$default_frame_min_width.';"' : '';
    $allow_fullscreen = ($allow_fullscreen) ? 'allowfullscreen="true"' : 'allowfullscreen="false"';
    $scrolling = !empty($no_scrolling) ? 'scrolling = "no"' : '';
    ?>
</head>
<body>
<div id="bodyMessage">正在载入... <a href="javascript:window.location.reload();" class='retry_link' style='display:none'>重试</a> </div>
<iframe id="bodyIframe"  <?php echo isset($iframeName) && !empty($iframeName) ? 'name="' . $iframeName . '"' : ''; ?> width="100%" height="100%" <?php echo $min_width; ?>  src="<?php echo isset($url)?$url:''; ?>" <?php echo $allow_fullscreen; ?> <?php echo $scrolling; ?> ></iframe>

<script type="text/javascript">
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

//console.log('Login url: <?php echo $api_play_pt_js; ?>');

var game_platform_id="<?php echo (isset($game_platform_id)?$game_platform_id:1); ?>";
var gameCode="<?php echo $game_code; ?>";
var lang="<?php echo $lang; ?>";
var api_play_pt="<?php echo $api_play_pt; ?>";
var player_url="<?php echo $player_url; ?>";
var mobile="<?php echo ( isset( $mobile ) ) ? $mobile : ''; ?>";
var mobile_systemId="<?php echo ( isset( $mobile_systemId ) && ! empty($mobile_systemId) ) ? $mobile_systemId : ''; ?>";

var mobile_launcher = "<?=( isset( $mobile_launcher ) && ! empty( $mobile_launcher ) ) ? $mobile_launcher : 'https://hub.ld177788.com/igaming/';?>";
var mobile_lobby = "<?= $mobile_lobby ?>";
var mobile_logout_url = "<?= $mobile_logout_url ?>";
var deposit_url = "<?= $deposit_url ?>";
var support_url = "<?= $support_button_link ?>";
var is_online = "<?php echo $is_online; ?>";
var do_launch_multiple_pt = "<?php echo $do_launch_multiple_pt; ?>";
var debug_javascript_response = "<?php echo $debug_javascript_response; ?>";

var pt_username = "";

var utils={
    safelog: function(msg){
        if(typeof(console)!='undefined' && typeof(console.log)!='undefined'){
            console.log(msg);
        }
    },
    buildErrorMessage:function(msg){
        alert(msg);
        // $.snackbar({content: msg, htmlAllowed: true, timeout: 4000});
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
        console.log("LOGIN RESPONSE =>" + JSON.stringify(response));

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
                //console.log('Is mobile launch.');                
                iapiRequestTemporaryToken(realMode, mobile_systemId, 'GamePlay');
            }else{
                //console.log('Launch by flash method');                
                var launch_url = api_play_pt +"/casinoclient.html?language="+lang+"&nolobby=1&game="+gameCode;
                //console.log('Launch url: '+launch_url);
                //window.location = launch_url;
                document.getElementById("bodyMessage").style.display = "none";
                document.getElementById("bodyIframe").style.display = "block";
                document.getElementById("bodyIframe").src=launch_url;                
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
            url: player_url+'/async/get_user_info/'+game_platform_id,
            type: 'GET',
            data: null,
            dataType: 'jsonp',
            cache: false
        }).done(
            function(data){     
                //console.log(data);           
                // var clientUrl = mobile_launcher + 'igaming/?gameId=' + gameCode + '&real=1' + '&username=' + data["key"].toUpperCase() + '&lang='+ lang + '&tempToken=' + temptoken + '&lobby=' + mobile_lobby + '&support=' + location.href.substring(0,location.href.lastIndexOf('/')+1) + 'support.html' + '&logout=' + location.href.substring(0,location.href.lastIndexOf('/')+1) + 'logout.html';
                var clientUrl = mobile_launcher + 'igaming/?gameId=' + gameCode + '&real=1' + '&username=' + data["key"].toUpperCase() + '&lang='+ lang + '&tempToken=' + temptoken + '&lobby=' + mobile_lobby + '&deposit=' + deposit_url + '&support=' + support_url + '&logout=' + mobile_logout_url;
                utils.safelog("clientUrl: "+clientUrl);
                //console.log('Mobile launch url: '+clientUrl);
                window.location = clientUrl;
                //document.getElementById("bodyMessage").style.display = "none";
                //document.getElementById("bodyIframe").style.display = "block";
                //document.getElementById("bodyIframe").src=clientUrl;
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

    function checkLogin() {
        iapiSetCallout('GetLoggedInPlayer', CheckIfPlayerLogin);
        iapiGetLoggedInPlayer(true);
    }    

    function CheckIfPlayerLogin(response) {
        if (debug_javascript_response === "1") {
            console.log("RESPONSE =>" + JSON.stringify(response));
        }
        if (response.errorCode == 0) {
            if (response.username === pt_username) {
                console.log("GOTOPT_V2 SUCCESS");
                if(typeof mobile != 'undefined' && (mobile=='mobile' || mobile=='true')){
                    var realMode = 1;
                    //console.log('Is mobile launch.');                
                    iapiRequestTemporaryToken(realMode, mobile_systemId, 'GamePlay');
                } else {
                    var launch_url = api_play_pt +"/casinoclient.html?language="+lang+"&nolobby=1&game="+gameCode;
                    document.getElementById("bodyMessage").style.display = "none";
                    document.getElementById("bodyIframe").style.display = "block";
                    document.getElementById("bodyIframe").src=launch_url;
                }
            } else if (response.username === "") {
                console.log("GOTOPT_V2 EMPTY_USERNAME");
                doLogin();
            } else if (response.username !== pt_username) {
                console.log("GOTOPT_V2 DIFFERENT_USERNAME");
                doLogin();
            } else {
                console.log("GOTOPT_V2 UNABLE TO CATCH");
                doLogin();
            }
        } else {
            utils.buildErrorMessage('请重新登录');
        }
    }
 

    function doLogin() {
        $.ajax({
            url: player_url+'/async/get_user_info/'+game_platform_id,
            type: 'GET',
            data: null,
            dataType: 'jsonp',
            cache: false
        }).done(
            function(data){
            if (data && data['key'] && data['secret']) {
                pt_username = data['key'].toUpperCase();
                iapiLogin(data['key'].toUpperCase(), data['secret'], 1, data['lang']);
            } else {
                utils.buildErrorMessage('请重新登录');
            }
        }).fail(
            function(){
                utils.buildErrorMessage('加载PT失败');
            }
        );
    }
      
    document.getElementById("bodyIframe").style.display = "none";

    $.ajax({
        url: player_url+'/async/get_user_info/'+game_platform_id,
        type: 'GET',
        data: null,
        dataType: 'jsonp',
        cache: false
    }).done(
        function(data){
            //console.log(data);
        // utils.safelog("1.) ismobile: "+mobile);
        // utils.safelog("2.) data: "+data);
        // utils.safelog("3.) data['key']: "+data['key']);
        // utils.safelog("4.) data['secret']: "+data['secret']);
        // utils.safelog("5.) data['lang']: "+data['lang']);
        if(data && data['key'] && data['secret']){
            pt_username = data['key'].toUpperCase();
            //console.log('iapiLogin');
            //console.log('params 1: '+data['key'].toUpperCase());
            //console.log('params 2: '+data['secret']);
            //console.log('params 3: '+data['lang']);
            //
            if (do_launch_multiple_pt === "1") {                
                checkLogin();
            } else {
                iapiLogin(data['key'].toUpperCase(), data['secret'], 1, data['lang']);
            }
        }else{
            utils.buildErrorMessage('请重新登录');
        }
    }).fail(
        function(){
            utils.buildErrorMessage('加载PT失败');
        }
    );
});

</script>



</body>
</html>
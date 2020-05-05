jsRequire('/JSComponents/jquery.js');
var GPInt = {
    setCookie: function (cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires = "expires=" + d.toGMTString();
        document.cookie = cname + "=" + cvalue + "; path=/; " + expires;
    },
    getCookie: function (cname) {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i].trim();
            if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
        }
        return "";
    }
}
var loginStatus = "empty";
//var strUrlToken = getUrlVars()["token"];
var strUrlToken = getUrlVars("token");
var strCookieToken = GPInt.getCookie("token");
var strSSToken = sessionStorage.getItem("token");

var strToken = '';

if (strUrlToken != '' && strUrlToken != undefined) {
    strToken = strUrlToken;
    GPInt.setCookie("token", strUrlToken, 1);
    sessionStorage.setItem("token", strUrlToken);
}
else if (strCookieToken != '' && strUrlToken != undefined) {
    strToken = strCookieToken;
    sessionStorage.setItem("token", strCookieToken);
}
else if (strSSToken != '' && strSSToken != undefined) {
    strToken = strSSToken;
    GPInt.setCookie("token", strSSToken, 1);
}

function whl() { }
whl.prototype.login = function (username, password, callback) { }
whl.prototype.status = function (callback) {
    this.status_callback = callback;
    var that = this;
    $.ajax({
        type: "POST",
        url: "http://admin.staging.scs188.t1t.in/callback/game/969/status?r=" + new Date().getTime(),
        data: { token: strToken },
        crossDomain: true,
        contentType: "jsonp",
        dataType: "jsonp",
        jsonp: false,
        jsonpCallback: 'jsoncb',
        success: function (data) {
            that.statusCallback(data);
        }
    });
    //setOddStyle(1);
}
whl.prototype.statusCallback = function (data, type) {
    if (this.status_callback) {
        var result = new Object();
        result.token = data.token;
        result.status = (data.status == 'True' || data.status == 'real' ? 'real' : 'anon');
        this.status_callback(result);
    }
}


whl.prototype.refreshSession = function (callback) {
    this.refresh_callback = callback;
    var that = this;
    var result = new Object();
    $.ajax({
        type: "POST",
        url: "http://admin.staging.scs188.t1t.in/callback/game/969/refresh_session?r=" + new Date().getTime(),
        data: { token: strToken },
        crossDomain: true,
        contentType: "jsonp",
        dataType: "jsonp",
        jsonp: false,
        jsonpCallback: 'jsoncb',
        success: function (data) {
            that.refreshCallback(data);
        }
    });
}

whl.prototype.refreshCallback = function (data) {
    if (this.refresh_callback) {
        var result = new Object();
        result.status = data.status;
        this.refresh_callback(result);
    }
}

whl.prototype.logout = function ()
{
//var result = new Object();
//result.status = 'OK';
//this.logout(result);
    window.location.reload();
}
whl.prototype.resetPassword = function () { }
whl.prototype.registrationForm = function () { }
whl.prototype.bank = function () { }
whl.prototype.formClose = function () { }

function getUrlVars(name) {
    var match = RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
    var myVar = match && decodeURIComponent(match[1].replace(/\+/g, ' '));
    if (myVar != null) {if (myVar.slice(-1) == '/') myVar = myVar.slice(0, -1);} else { myVar=''; }
    return myVar;
}
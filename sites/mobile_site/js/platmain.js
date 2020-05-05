//function ResumeError() { return true; } window.onerror = ResumeError;
//function click(e) { if (document.all) { if (event.button == 2 || event.button == 3) { oncontextmenu = 'return false'; } } }
//document.onmousedown = click; document.oncontextmenu = new Function("return false;");
$(function() {
    $.fn.numeral = function() {
        $(this).css("ime-mode", "disabled");
        this.bind("keypress", function(e) {
            if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode != 46 || /\.\d\d\$/.test($(this).val()))
                event.returnValue = false
        });
        this.bind("keydown", function() {
            if (window.event.keyCode == 13)
                window.event.keyCode = 9;
        });
        this.bind("keyup", function() {
            if ((/^((\d+)(\.\d{0,2})?)?$/).test(this.value)) this.oldValue = this.value; else this.value = this.oldValue;
        });
    };
    $.fn.OnlyInputNumber = function() {
        $(this).css("ime-mode", "disabled");
        this.bind("keypress", function(e) {
            if (event.keyCode < 48 || event.keyCode > 57)
                event.returnValue = false;
        });
        this.bind("keydown", function() {
            if (window.event.keyCode == 13)
                window.event.keyCode = 9;
        });
        this.bind("keyup", function() {
            this.value = this.value.replace(/\D/g, '')
        });
    };

    $.fn.PlatSelect = function() {
        var put = $(this);
        var putid = $(put).attr("id") + "_sele";
        put.focus(function() {

            $("#" + putid).css("display", "block");
        })
        $("#" + putid).find("dd").click(function() {
            put.val($.trim($(this).html()));
            $("#" + putid).css("display", "none");
        })
    };


    $.fn.Center = function() {
        var hcbox = $(this);
        $(hcbox).css({ left: ($(window).width() - $(hcbox).outerWidth()) / 2 });
        $(hcbox).css({ top: ($(window).height() - $(hcbox).outerHeight()) / 2  });
        $(window).resize(function() {
            $(hcbox).css({ left: ($(window).width() - $(hcbox).outerWidth()) / 2 });
            $(hcbox).css({ top: ($(window).height() - $(hcbox).outerHeight()) / 2 });
        });

        $(window).scroll(function() {
            $(hcbox).css({ left: ($(window).width() - $(hcbox).outerWidth()) / 2 });
            $(hcbox).css({ top: ($(window).height() - $(hcbox).outerHeight()) / 2 });
        });
    };

    $.fn.InputTip = function(v, ov) {
        var obj = $(this);
        var nbj = obj.next();
        if (ov == null) {
            ov = v;
        }
        if (nbj.attr("class").indexOf("page-tips") != -1) {
            nbj.addClass("page-tips-err").html(v);
        }
        else {
            obj.after("<span class=\"page-tips page-tips-err\">" + c + "</span>")
        }
        obj.keydown(function() {
            nbj.removeClass("page-tips-err").html(ov);
            obj.unbind("keypress");
        });
    }
})

function verifyEmail(emailVal) {
    return !($.trim(emailVal).length == 0 || !/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(emailVal));
}
function verifyDate(dateVal) {
    return !($.trim(dateVal).length == 0 || !/^(\d{4})([-])(\d{2})([-])(\d{2})/.test(dateVal));
}

function sliaonow() {
    var cookiename = "svidname";
    var cookievalue = getCookie(cookiename);
    if (cookievalue == "") {
        cookievalue = randomString(22);
        setCookie(cookiename, cookievalue, 365);
    }
    window.open('https://chat.bmwchat.net/?wgPortal=VONFpjLZtk', 'newwindow', 'height=auto,width=auto,top=0,left=0,toolbar=no,menubar=no,scrollbars=no,resizable=no,location=no,status=no');
}
//设置cookie
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}
//获取cookie
function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1);
        if (c.indexOf(name) != -1) return c.substring(name.length, c.length);
    }
    return "";
}
//清除cookie  
function clearCookie(name) {
    setCookie(name, "", -1);
}
//检查cookie
function checkCookie() {
    var user = getCookie("username");
    if (user != "") {
        alert("Welcome again " + user);
    } else {
        user = prompt("Please enter your name:", "");
        if (user != "" && user != null) {
            setCookie("username", user, 365);
        }
    }
}
function randomString(len) {
    len = len || 32;
    var $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';    /****默认去掉了容易混淆的字符oOLl,9gq,Vv,Uu,I1****/
    var maxPos = $chars.length;
    var pwd = '';
    for (i = 0; i < len; i++) {
        pwd += $chars.charAt(Math.floor(Math.random() * maxPos));
    }
    return pwd;
}


$(document).ready(function () {

    //加载......

    /*var url = $("#pageone").attr("url");
    if (url) {
        $.ajax({
            type: "get",
            url: url,
            beforeSend: function () {
                $("#loading_one").show();
            },
            success: function (data) {
                $("#loading_one").hide();
                $("#startmovie").show();
                $("#pageone .page").html(data);
            },
        });
    };*/


    //导航菜单

    var btn = $(".menu_btn,.menu_btn_two");
    var pageone = $("#pageone");
    var pagetwo = $("#pagetwo");
    var pagethree = $("#pagethree");
    var pagefour = $("#pagefour");
    var pagefive = $("#pagefive");
    var menu = $(".menu_right,.menu_left");
    var leftmove = "left_move";
    var mmove = "menu_move";
    var leftm = $(".menu_left");
    var rightm = $(".menu_right");
    var tap = "click";

    btn.on(tap, function () {

        var mmbtn = $(this).attr("class");

        if (mmbtn == "menu_btn") {
            pageone.addClass("blurry");
            rightm.addClass(mmove);
        }
    });

    menu.on(tap, function () {

        pageone.removeClass("blurry");
        pagetwo.removeClass("blurry");
        $(this).removeClass(mmove);

    });
//    $(document).on("click", ".btn-kf", function () {
//        $("#kf-btn div").click();
//    });
  
});


function IsLogged() {
    $.post("/Ajaxweb/AjaxIndex.aspx", { act: "islogin", m: Math.random() },
           function (data) {
               var arr = data.split("$");
               if (arr[0] == "1") {
                   Logged(arr[1]);
               }
               else {
                   Logout();
               }
           });
}

function Logged(username)
{
    $("#hreg").hide();
    $("#hlog").hide();
    $("#rreg").show();
    $("#rlog").show();
    $("#bf_login").hide();
    $("#after_login").show();
    $("#usertext").hide();
    $("#uesrheader a").html(username)
    $("#uesrheader").show();

}
function Logout() {
    $("#hreg").show();
    $("#hlog").show();
    $("#rreg").hide();
    $("#rlog").hide();
    $("#bf_login").show();
    $("#after_login").hide();
    $("#usertext").show();
    $("#uesrheader a").html("")
    $("#uesrheader").hide();
}


function verifyEmail(emailVal) {
    return !($.trim(emailVal).length == 0 || !/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(emailVal));
}
function verifyDate(dateVal) {
    return !($.trim(dateVal).length == 0 || !/^(\d{4})([-])(\d{2})([-])(\d{2})/.test(dateVal));
}
function getCookie(name)
{
var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
if(arr=document.cookie.match(reg))
return unescape(arr[2]);
else
return '';
}

function setCookie(name,value,ts)
{
var exp = new Date();
exp.setTime(exp.getTime() + ts*1000);
document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();
}
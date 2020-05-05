$(document).ready(function () {
    $("#tran-amt").OnlyInputNumber();
    var btn = $(".user_btn,.menu_btn,.menu_btn_two");
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


    $(".xlmenu").click(function () {

        var xlmenu = ".mmenu";
        var xlmovie = "mmenu_movie";

        $(xlmenu).addClass(xlmovie);

        var mmenu = $(this).attr("id");
        if (mmenu == "zh1")/*选择银行菜单*/ {
            choicemenu();

            var eput = $(this);
            var plat;

            plat = $("#zxzh").text();
            indexval = eput.text();

        }
        if (mmenu == "zh2")/*选择银行菜单*/ {
            choicemenu();

            var eput = $(this);
            var plat;

            plat = $("#zxzh").text();
            indexval = eput.text();

        }

        $(".mm_exit").click(function () {
            $(xlmenu).removeClass(xlmovie);
        });
        function choicemenu() {
            $(".mm_main li").unbind("click");
            $(".mm_main li").click(function () {
                $(".mm_main li i").removeClass("border");
                $(this).find("i").addClass("border");
                $(xlmenu).removeClass(xlmovie);
                var title = $(this).text();
                if (mmenu == "zh1" || "zh2") {
                    eput.text($(this).text());
                    if (eput.attr("id") == "zh1" && $(this).text() != plat) {
                        $("#zh2").text(plat);
                    } else if (eput.attr("id") == "zh2" && $(this).text() != plat) {
                        $("#zh1").text(plat);
                    } else if ($("#zh1").text() == plat && $("#zh2").text() == plat) {
                        $("#zh1").text("请选择转出账户");
                        $("#zh2").text("请选择转入账户");
                        eput.text($(this).text());
                    }
                }
            });
        };
    });

    $("#accqd").click(function () {
        var zha = $("#zh1").text();
        var zhb = $("#zh2").text();
        var zxzh = "众鑫账户";

        if (zha == "请选择转出账户") {
            $("#zztext1").show();
            return false;
        } else {
            $("#zztext1").hide()
        }

        if (zhb == "请选择转入账户") {
            $("#zztext2").show();
            return false;
        } else {
            $("#zztext2").hide()
        }

        if (zha == zxzh && zhb != zxzh) {
            TranToGame();
        }
        else if (zha != zxzh && zhb == zxzh) {
            TranToPlat();
        }

    });
});
var IsSubmit = false;
function TranToGame() {
    if (IsSubmit) {
        alert("正在转账中");
        return false;
    }
    var zha = $("#zh1").text();
    if (zha == "请选择转出账户") {
        $("#zztext1").show();
        return false;
    } else {
        $("#zztext1").hide()
    }
    var zhb = $("#zh2").text();
    if (zhb == "请选择转入账户") {
        $("#zztext2").show();
        return false;
    } else {
        $("#zztext2").hide()
    }

    var tga = $("#tran-amt");
    var amt = $.trim(tga.val());
    if (amt.length == 0) {
        $("#zztext3 li").html("<i></i>请输入转账金额")
        $("#zztext3").show();
        return false;
    } else {
        $("#zztext3").hide()
    }
    if (isNaN(amt)) {
        $("#zztext3 li").html("<i></i>输入的金额不合法")
        $("#zztext3").show();
        return false;
    } else {
        $("#zztext3").hide()
    }
    amt = parseFloat(amt);
    if (amt < 1) {
        $("#zztext3 li").html("<i></i>转账金额不能小于1元")
        $("#zztext3").show();
        return false;
    } else {
        $("#zztext3").hide()
    }

    var game = zhb;
    var tip = game;
    if (game == "众鑫账户") {
        alert("错误转账");
        return false;
    }
    if (game == "EA平台") {
        game = "EA";
    }
    else if (game == "BBIN平台") {
        game = "BBIN";
    }
    else if (game == "KG平台") {
        game = "KG";
    }
    else if (game == "LB平台") {
        game = "LB";
    }
    else if (game == "体育平台") {
        game = "Sports";
    }
    else if (game == "AMP平台") {
        game = "AMP";
    }
    else if (game == "AG平台") {
        game = "AG";
    }
    else if (game == "棋牌平台") {
        game = "CH";
    }
    else if (game == "EBET平台") {
        game = "EBET";
    }
    else if (game == "PT平台") {
        game = "PT";
    }
    if (confirm("您确定转入" + tip + "吗？")) {
        IsSubmit = true;
        $("#loading").show();

        $.ajax({
            type: "post",
            url: "Ajaxweb/AjaxMemberFunds_Cs.aspx",
            data: { act: "tran", c: "IN", game: game, amt: amt, t: Math.random() },
            timeout: 30000,
            success: function (data) {
                alert(data);
                if (data.indexOf('成功') != -1) {
                    tga.val("");
                }

                IsSubmit = false;
            },
            complete: function (XMLHttpRequest, status) {
                $("#loading").hide();
                if (status == 'timeout') {
                    alert("超时");
                }
            }
        });



    }
    return false;
}

function TranToPlat() {
    if (IsSubmit) {
        alert("正在转账中");
        return false;
    }
    var zha = $("#zh1").text();
    if (zha == "请选择转出账户") {
        $("#zztext1").show();
        return false;
    } else {
        $("#zztext1").hide()
    }
    var zhb = $("#zh2").text();
    if (zhb == "请选择转入账户") {
        $("#zztext2").show();
        return false;
    } else {
        $("#zztext2").hide()
    }

    var tga = $("#tran-amt");
    var amt = $.trim(tga.val());
    if (amt.length == 0) {
        $("#zztext3 li").html("<i></i>请输入转账金额")
        $("#zztext3").show();
        return false;
    } else {
        $("#zztext3").hide()
    }
    if (isNaN(amt)) {
        $("#zztext3 li").html("<i></i>输入的金额不合法")
        $("#zztext3").show();
        return false;
    } else {
        $("#zztext3").hide()
    }
    amt = parseFloat(amt);
    if (amt < 1) {
        $("#zztext3 li").html("<i></i>转账金额不能小于1元")
        $("#zztext3").show();
        return false;
    } else {
        $("#zztext3").hide()
    }

    var game = zha;
    var tip = game;
    if (game == "众鑫账户") {
        alert("错误转账");
        return false;
    }
    if (game == "EA平台") {
        game = "EA";
    }
    else if (game == "BBIN平台") {
        game = "BBIN";
    }
    else if (game == "KG平台") {
        game = "KG";
    }
    else if (game == "LB平台") {
        game = "LB";
    }
    else if (game == "体育平台") {
        game = "Sports";
    }
    else if (game == "AMP平台") {
        game = "AMP";
    }
    else if (game == "AG平台") {
        game = "AG";
    }
    else if (game == "棋牌平台") {
        game = "CH";
    }
    else if (game == "EBET平台") {
        game = "EBET";
    }
    else if (game == "PT平台") {
        game = "PT";
    }
    if (confirm("您确定转出" + tip + "吗？")) {
        IsSubmit = true;
        $("#loading").show();
        $.post("Ajaxweb/AjaxMemberFunds_Cs.aspx", { act: "tran", c: "OUT", game: game, amt: amt, t: Math.random() },
      function (data) {
          $("#loading").hide();
          alert(data);
          if (data.indexOf('成功') != -1) {
              tga.val("");
          }

          IsSubmit = false;
      });
    }
    return false;
}
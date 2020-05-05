$(document).ready(function () {

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
    var tap = "click"; //"touchstart";//	
    //换页效果

    $("#login").click(function () {
        var dltext1 = $("#dl1").val();
        var dltext2 = $("#dl2").val();

        if (dltext1.length == 0) {
            $("#dltext1").show();
            return false;
        } else {
            $("#dltext1").hide();
        }
        if (dltext2.length == 0) {
            $("#dltext2").show();
            return false;
        } else {
            $("#dltext2").hide();
        }
        $("#loading").show();
        $.ajax({
            type: "post",
            timeout: 30000,
            url: "/Ajaxweb/AjaxLogin.aspx",
            data: { user: dltext1, pwd: dltext2, act: "login", m: Math.random() },
            success: function (data) {
                if (data == "success") {
                    Logged(dltext1);
                    $.ajax({
                        type: "get",
                        url: "account.aspx",
                        beforeSend: function () {
                            $("#loading").show();
                        },
                        success: function (data) {
                            $("#loading").hide();
                            $("#ht").text("资金管理");
                            pagetwo.find(".page").html(data);
                        },
                    });
                }
                else {
                    alert(data);
                    $("#loading").hide();
                }
            },
            complete: function (XMLHttpRequest, status) {

                if (status == 'timeout') {//超时,status还有success,error等值的情况
                    $("#loading").hide();
                    alert("超时");
                }
            }
        });
    });

    $(".threepage").click(function () {

        var url_two = $(this).attr("url");
        var hdtext = $(this).find("a").text();

        setTimeout("$('#pagetwo .page').addClass('hide')", 300);

        pagetwo.addClass(leftmove);
        pagethree.addClass("move");

        $("#ht1").text(hdtext);

        if (url_two) {

            $.ajax({
                type: "get",
                url: url_two,
                timeout: 30000,
                beforeSend: function () {
                    $("#loading").show();
                },
                success: function (data) {
                    pagethree.find(".page").html(data);
                },
                complete: function (XMLHttpRequest, status) {
                    $("#loading").hide();
                    if (status == 'timeout') {
                        alert("超时");
                    }
                }
            });
        };
        $(".bb_three").click(function () {

            setTimeout("$('#pagethree .page').html('')", 300);
            pagetwo.find(".page").removeClass('hide');
            pagetwo.removeClass(leftmove);
            pagethree.removeClass("move");

        });
    });


});
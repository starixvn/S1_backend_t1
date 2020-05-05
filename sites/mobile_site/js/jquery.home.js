$(document).ready(function () {
	
    var picwidth = $(window).width();
    var picheight = $(window).height();
    if (picheight >= picwidth) {
        $(".swiper-container").height(picwidth * 0.6);
    }
    else {
        $(".swiper-container").height(picwidth * 0.28);
    }
    var swiper = new Swiper('.swiper-container', {
        pagination: '.swiper-pagination',
        slidesPerView: 1,
        paginationClickable: true,
        spaceBetween: 0,
        loop: true,
        autoplayDisableOnInteraction: false,
        autoplay: 5000,
    });

    //导航菜单

    var btn = $(".user_btn,.menu_btn,.menu_btn_two");
    var pageone = $("#pageone");
    var pagetwo = $("#pagetwo");
    var pagethree = $("#pagethree");
    var pagefour = $("#pagefour");
    var pagefive = $("#pagefive");
    var pagesix = $("#pagesix");
    var menu = $(".menu_right,.menu_left");
    var leftmove = "left_move";
    var mmove = "menu_move";
    var leftm = $(".menu_left");
    var rightm = $(".menu_right");
    var tap = "click"; //"touchstart";//	

    $("#lbgame").click(function () {
        $(".pop_lb").show();
        $(".pop_lb").click(function () {
            $(".pop_lb").hide();
        });
    });
    $("#aggame").click(function () {

        $(".pop_ag").show();
        $(".pop_ag").click(function () {
            $(".pop_ag").hide();
        });
    });
    $("#qpgame").click(function () {
        $(".pop_qp").show();
        $(".pop_qp").click(function () {
            $(".pop_qp").hide();
        });
    });
	
    $(".twopage").click(function () {
        var url = $(this).attr("url");
        var leftmm = $(".menu_right li");
        var homebtn = leftmm.attr("id");
        //setTimeout("$('#pageone').addClass('hide')", 300);//cole 20170323

        $("#homepage a").removeClass("menuhover");

        pagetwo.addClass("move");
        //pageone.addClass(leftmove);//cole 20170323

        var hdtext = $(this).find("a").text();
        $("#ht").text(hdtext);

        if (url) {
            $.ajax({
                type: "get",
                url: url+"?t="+Math.random(),
                timeout: 30000,
                beforeSend: function () {
                    $("#loading").show();
                },
                success: function (data) {
                    pagetwo.find(".page").html(data);
                }, complete: function (XMLHttpRequest, status) {
                    $("#loading").hide();
                    if (status == 'timeout') {
                        alert("加载超时");
                    }
                }
            });
        };
    });
	$(".back_btn").click(function () {
		setTimeout("$('#pagetwo .page').html('')", 300);
		menu.find("li a").removeClass("menuhover");
		$("#homepage a").addClass("menuhover");
		pageone.removeClass('hide').removeClass(leftmove);
		pagetwo.removeClass("move");

	});

     $(".notice_text li").click(function () {

        var picheight = $(window).height();

        $(".message_main").height(picheight - 60)

        var hdtext = $(this).text();

        $("#ht4").text(hdtext);

        var ntotext = $(this).attr("id");

        var page = $("#c" + ntotext).html();

        $(".message_main").html(page);

        pageone.addClass(leftmove);
        pagesix.addClass("move");

        $(".bb_six").click(function () {

            setTimeout("$('#pagesix'),find('.page').html('')", 300);
            pageone.find(".page").removeClass('hide');
            pageone.removeClass(leftmove);
            pagesix.removeClass("move");

        });
    });
    IsLogged();
   
});


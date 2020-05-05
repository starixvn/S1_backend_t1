
function dropmenu() {
    var container = $(".menu"),
        target = container.find($(".ui-nav-item"));
    target.hover(function () {
        if ($(this).children($(".ui-nav-child")).length != 0) {
            $(".ui-nav-child").hide();
            $(this).children($(".ui-nav-child")).show();
        }
    }, function () {
        $(".ui-nav-child").hide();
    });
}

function doNotice() {
    var container = $(".J-notice");        
    container.html(container.html() + container.html());
    
    var aLi = container.find("li"),
        aLih = aLi.eq(0).height();

    container.css({ height: aLi.length * aLih + "px", top: "0px" });

    timer = setInterval(function () {

        if (parseInt(container.css("top")) < -container.height() / 2) {
            container.css({ top: -aLih + "px" });
        }

        container.animate({top:parseInt(container.css("top"))-aLih+"px"},500);
    },3000);
}

function doAward() {
    var container = $(".J-award");
    container.html(container.html() + container.html());
    var aLi = container.find($(".J-luck-item")),
        aLiw = aLi.eq(0).width();

    container.css({ width: aLi.length * aLiw + "px", left: "0px" });

    timer = setInterval(function () {
        if (-parseInt(container.css("left")) > (container.width()/2)) {
            //alert(1)
            container.css({ left: -aLiw+"px" })
        }
        container.animate({ left: parseInt(container.css("left"))-aLiw + "px" });        
        
    },3000);
        
}

function popMsg() {
    var btn = $(".J-popmsg"),
        xbox = $(".J-xbox-msg");

    $.each(btn, function (index) {
        $(this).click(function () {
            var title = btn.eq(index).find($(".popmsg-title")).html(),
                sub = btn.eq(index).find($(".popmsg-subtitle")).html(),
                cont = btn.eq(index).find($(".popmsg-cont")).html();
            monse.xboxshow(xbox);
            xbox.find($(".xbox-title")).html(title);
            xbox.find($(".xbox-subtitle")).append(sub);
            xbox.find($(".xbox-cont")).html(cont);
        });
    });
}
function countDown() {    
    function toDouble(n) {
        if (n < 10) {
            return "0" + n;
        }
        else {
            return "" + n;
        }
    }
    function time() {
        var container = $(".J-countdown"),
        aImg = container.find("img");

        var d = container.attr("data-d"),
            h = container.attr("data-h"),
            m = container.attr("data-m"),
            s = container.attr("data-s");

    }
}

function doSeemore() {
    var cont = $(".J-seemore-cont"),
        btn = $(".J-seemore-btn");
    $.each(btn, function (index) {
        $(this).click(function () {
            if (cont.eq(index).css("display") == "none") {
                cont.eq(index).show();
            }
            else if (cont.eq(index).css("display") == "block") {
                cont.eq(index).hide();
            }
        });
    })
}

var monse = {

    xboxshow: function (xbox) {
        var xbox = xbox,
            mask = $(".J-mask");
        
        var pdt = parseInt(xbox.css("paddingTop").replace("px", "")),
            pdr = parseInt(xbox.css("paddingRight").replace("px", "")),
            pdb = parseInt(xbox.css("paddingBottom").replace("px", "")),
            pdl = parseInt(xbox.css("paddingLeft").replace("px", ""));

        var xboxw = xbox.width(),
            xboxh = xbox.height();

        mask.css({ display: "block" });
        xbox.css({display:"block",margin:-(xboxh+pdt+pdb)/2+"px 0 0 "+ "-"+ (xboxw+pdr+pdl)/2 + "px"});        
    },

    xboxclose: function () {
        var xbox = $(".ui-xbox"),
            mask = $(".J-mask"),
            closeBtn = xbox.find(".J-xbox-close");

        closeBtn.click(function () {
            mask.css({ display: "none" });
            xbox.css({ display: "none" });
        });

        mask.click(function () {
            mask.css({ display: "none" });
            xbox.css({ display: "none" });
        });
    },

    tab: function (target) {
        var trigger = $(target.trigger),
            cont = $(target.cont);

        $.each(trigger, function (index) {
            $(this).click(function () {
                trigger.removeClass("curr");
                trigger.eq(index).addClass("curr");
                cont.css({ position: "absolute", left: "-9999px" });
                cont.eq(index).css({ position: "static" });
            });
        });
    },

    circleProgressBar: function (circle) {
        var container = $('.' + circle.container),
            time = circle.time;
        var num = container.find($(".circle-num")),
            max = num.attr("data-num"),
            i = 0;
        if (isNaN(max)) {
            return false;
        }
        if (max > 100) {
            max = 100;
        }
        setInterval(function () {
            if (i > max) {
                return false;
            }
            num.html(i++);
            container.each(function (index, el) {
                var num = $(this).find('span').text() * 3.6;
                if (num <= 180) {
                    $(this).find('.right').css('transform', "rotate(" + num + "deg)");
                } else {
                    $(this).find('.right').css('transform', "rotate(180deg)");
                    $(this).find('.left').css('transform', "rotate(" + (num - 180) + "deg)");
                };
            });
        }, time);
    },

    diyselect: function (json) {
        var container = $(json.container);

        var show = container.find($(".select-show")),
            cont = container.find($(".select-cont")),
            list = container.find($(".select-list"));

        var listcont = '';
        if (json.data == "year") {

            var date = new Date(),
                curyear = date.getFullYear();

            for (var i = curyear; i >= 1900; i--) {
                listcont += "<li>" + i + "</li>";
            }
        }
        else if (json.data == "month") {
            for (var i = 1; i < 13; i++) {
                listcont += "<li>" + i + "</li>";
            }
        }
        else if (json.data == "day") {
            for (var i = 1; i < 31; i++) {
                listcont += "<li>" + i + "</li>";
            }
        }
        else {
            for (var i = 0; i < json.data.length; i++) {
                listcont += "<li>" + json.data[i] + "</li>";
            }
        }        
        list.html(listcont);
        list.css({ height: list.find("li").height() * list.find("li").length + "px" });

        show.click(function () {
            if (cont.css("display") == "none") {
                cont.css({ display: "block" });
                $(document).one("click", function () {//对document绑定一个影藏Div方法
                    cont.hide();
                });
                event.stopPropagation();
            }
            else {
                cont.css({ display: "none" });
            }
        });

        var aLi = list.find("li");
        
        $.each(aLi, function (index) {
            $(this).click(function () {
                container.attr("value", aLi.eq(index).html())
                show.find("span").html(aLi.eq(index).html());
                aLi.removeClass("curr");
                aLi.eq(index).addClass("curr");
                cont.css({ display: "none" });
            });
        });

    },

    diyradio: function (container) {
        var container = $(container),
            aItem = container.find($(".radio-item"));
        $.each(aItem, function (index) {
            $(this).click(function () {
                aItem.removeClass("curr");
                aItem.eq(index).addClass("curr");
                container.attr("value", aItem.eq(index).find("span").html());                
            });
        });
    },

    regist: function (target) {
        var btn = $(target.btn),
            xbox = $(target.xbox),
            submit = $(target.submit);
        btn.click(function () {
            monse.xboxshow(xbox);
        });
    },

    login: function () {
        var container = $(".J-login"),
            verify = container.find($(".J-verify")),
            submit = container.find($(".J-submit"));

        var xbox = $(".J-xbox-login"),
            xboxCont = xbox.find($(".xbox-cont"));

        submit.click(function () {

            for (var i = 0; i < verify.length; i++) {
                if ($.trim(verify.eq(i).val()) == "") {
                    monse.xboxshow(".J-xbox-login");
                    xboxCont.text(verify.eq(i).attr("data-tip"));
                    return false;
                }                
            }
            //alert("请程序员注释掉这段JS");
            //container.hide();
            //$(".afterlogin").show();
        });
        monse.xboxclose();
    },

    addNum: function (target) {
        var target = $(target);
        var str = target.attr("data-num");
        
        var html = "",
            timer,
            a = [];

        if (str.indexOf(".") > 0) {
            str = str.replace(".", "0")
        }

        if (str.length % 3 == 1) {
            str = "00" + str;
        }
        else if (str.length % 3 == 2) {
            str = "0" + str;
        }

        for (var i = 0; i < parseInt((str.length) / 3) ; i++) {
            html += ("<span data-num='" + parseInt(str.substring(3 * i, 3 * (i + 1))) + "'>" + parseInt(str.substring(3 * i, 3 * (i + 1))) + "</span>,");
            a[i] = 0;
        }

        html = html.substring(0, html.length - 1);

        var lastp = html.lastIndexOf(",");

        if (str.indexOf(".") > 0) {
            html = html.substring(0, lastp) + "." + html.substring(lastp + 1, html.length);
        }
        target.html(html);

        var aSpan = target.find("span");

        setInterval(function () {
            for (var i = 0; i < aSpan.length; i++) {
                if (a[i] > parseInt(aSpan[i].getAttribute("data-num"))) {
                    a[i] = parseInt(aSpan[i].getAttribute("data-num"));
                }
                aSpan[i].innerHTML = a[i]++;
            }
        }, 0.5);
    },

}

$(document).ready(function () {


    var btn = $(".user_btn,.menu_btn,.menu_btn_two"); 
    var menu = $(".menu_right,.menu_left");
    var leftmove = "left_move";
    var mmove = "menu_move";
    var leftm = $(".menu_left");
    var rightm = $(".menu_right");
    var tap = "click"; //"touchstart";//	
    $(".cpt4").hide();

    //下拉菜单
    $(".xlmenu").click(function () {

        var xlmenu = ".mmenu";
        var xlmovie = "mmenu_movie"

        $(xlmenu).addClass(xlmovie);

        var mmenu = $(this).attr("id");

        choicemenu()

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

                if (mmenu == "ptmenu") {

                    $("#title").text(title);
                    var gt = $(this).attr("data-type");
                    GetPTGameList(gt);
                }
            });
        };
    });

/*    $(document).on("click", ".ptgamebox", function () {
        $("#pop_gamename").text($(this).find("span").text());
        var gamecode = $(this).find("input").val();
        if ($("#hidusername").val() == 0) {
            // alert("请先登录");
            return;
        }
        askTempandLaunchGame('ngm', gamecode);
    });*/
 
    
});
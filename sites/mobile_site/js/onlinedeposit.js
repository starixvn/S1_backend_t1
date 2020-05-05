<script>
        $(document).ready(function () {   

        $(".xlmenu").click(function () {

        var xlmenu = ".mmenu";

        var xlmovie = "mmenu_movie";

        $(xlmenu).addClass(xlmovie);

        $(".mm_exit").click(function () {

            $(xlmenu).removeClass(xlmovie);

        });



        $(".mm_main li").click(function () {

            $(".mm_main li i").removeClass("border");

            $(this).find("i").addClass("border");

            $(xlmenu).removeClass(xlmovie);

            var title = $(this).text();

            $("#bankicon").text(title);

        });



    }); 
 
    $(".login_btn").click(function () {

               var amt = $.trim($("#ol_b_amt").val());
               if (amt.length == 0) {
                   $("#zxtext1").html("<i></i>请输入转账金额");
                   $("#zxtext1").show();
                   return false;
               }
               else {
                   $("#zxtext1").hide();
               }

               if (isNaN(amt)) {
                   $("#zxtext1").html("<i></i>金额非有效数字");
                   $("#zxtext1").show();
                   return false;
               }
               else {
                   $("#zfbtext1").hide();
               }

               var amount = parseFloat(amt, 10);

               if (amount < 100 || amount > 3000) {
                   $("#zxtext1").html("<i></i>转账金额不能小于 100元 或大于 3000元");
                   $("#zxtext1").show();
                   return false;
               }
               else {
                   $("#zxtext1").hide();
               }

                var bic = $("#bankicon").text();
                if (bic.indexOf("请选择银行") != -1) {
                    $("#zxtext2").show();
                    return false;
                }
                else {
                    $("#zxtext2").hide();
                }
        
               var pid = $("#pid").val(); 
               if (confirm("您确定提交吗？")) {
                   return true;
               }

               return false;
            });
     
});

function payol(url, minamt, maxamt, pr, username, pk) {
    var $payam = $("#ol_b_amt");
    var payamtval = $.trim($payam.val());

    if (payamtval.length == 0) {
        $("#zxtext1 li").html("<i></i>请输入存款金额");
        $("#zxtext1").show();
        return false;
    }
    else {
        $("#zxtext1").hide();
    }

    payamtval = parseFloat(payamtval);
    if (payamtval < minamt) {
        $("#zxtext1 li").html("<i></i>单笔充值金额不能小于" + minamt + "元");
        $("#zxtext1").show();
        return false;
    }
    else {
        $("#zxtext1").hide();
    }

    if (payamtval > maxamt) {
        $("#zxtext1 li").html("<i></i>单笔充值金额不能大于" + maxamt + "元");
        $("#zxtext1").show();
        return false;
    } else {
        $("#zxtext1").hide();
    }

    $("#linkpay").attr("target", "_blank");
    $("#linkpay").attr("href", url + "cashier.php?amt=" + payamtval + "&pb=" + pbval + "&pr=" + pr + "&u=" + username + "&gr=1");
    $payam.val("");
    return true;
}
    </script>
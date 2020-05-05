<form id="new__bankacct_form" action="<?=site_url('iframe_module/verifyWithdrawal/')?>" method="post">
    <input type="hidden" name="dw_ip" id="dw_ip" value="<?=$this->input->ip_address();?>">
    <input type="hidden" name="account_type" id="account_type" value="na" >
    <input type="hidden" name="bank_name" id="bank_name" value="" >
    <div class="mmenu">
    <div class="mm_header">
        <ul>
        <li><?=lang('player.ui59')?></li>
        </ul>
        <div class="mm_exit"></div>
    </div>
    <div class="mm_main">
        <ul>
        <?php
            foreach ($banks as $row){
        ?>
                <li id="<?=$row['bankTypeId']?>"><?=lang($row['bankName'])?><i></i></li>
        <?php
            }
        ?>

        </ul>
    </div>
    </div>
    <div class="input">
    <div  style="margin-top:100px;"></div>
    <label>账户余额: <strong><?=$this->utils->displayCurrency($player['totalBalanceAmount'])?></strong></label>
    <div id="addtkc" class="input_01 xlmenu"><a id="bankicon"><?=lang('Please Select Bank')?></a> </div>
    <ul id="bktx1">
        <li><i></i>银行不能为空</li>
    </ul>
    <input id="bc_name" name="bank_account_fullname" placeholder="持卡人姓名" type="text" class="input_01" value="<?=$realname?>" readonly="readonly">
    <ul id="adtext1">
        <li><i></i>持卡人姓名不能为空</li>
    </ul>
    <input id="bc_number" name="bank_account_number" placeholder="<?=lang('cashier.69')?>" type="text" class="input_01">
    <ul id="adtext2">
        <li><i></i>银行卡号不能为空</li>
    </ul>
    <!-- <input id="branch" name="branch" placeholder="开户网点" type="text" class="input_01">
    <ul id="adtext3">
        <li><i></i>开户网点不能为空</li>
    </ul> -->

    <input id="amount" name="amount" placeholder="<?=lang('pay.reqamt')?>" type="text" class="input_01">

    <?php
        if (@$showWithdrawPasswordField){
    ?>
            <input id="withdrawal_password" name="withdrawal_password" placeholder="<?=lang('a_reg.35')?>" type="password" class="input_01">
            <ul id="adtext4">
                <li><i></i>请输入提款密码</li>
            </ul>
    <?php
        }
    ?>

    <input id="lowest" type="hidden" value="<?=$min_withdraw_rule;?>">
    <input id="limit" type="hidden" value="<?=$max_withdraw_per_transaction;?>">
    <ul id="tkts2">
        <li><i></i>请输入提款金额</li>
    </ul>
    <ul id="max_amt">
        <li><i></i><?=lang('notify.56')?></li>
    </ul>
    <ul id="min_amt">
        <li><i></i><?=lang('notify.102')?> <?=$min_withdraw_rule;?></li>
    </ul>
    <div id="btn_bindcard" class="login_btn"><?=lang('Confirm')?></div>
    </div>

    <div class="blank3"></div>

</form>

<script>
    $(document).ready(function () {
      $('#ht').html('<?=lang('add_withdrawal_bank')?>');
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

    $("#btn_bindcard").click(function () {

        var bic = $("#bankicon").text();
        if (bic.indexOf("<?=lang('Please Select Bank')?>") != -1) {
            $("#bktx1").show();
            return false;
        }
        else {
            $("#bktx1").hide();
        }

        var bank = bic;
        // var name = $.trim($("#bc_name").val());
        // if (name.length == 0) {
        //     $("#adtext1 li").html("<i></i>持卡人姓名不能为空");
        //     $("#adtext1").show();
        //     return false;
        // } else {
        //     $("#adtext1").hide();
        // }

        // if (name.length < 2) {
        //     $("#adtext1 li").html("<i></i>请填写正确的持卡人姓名");
        //     $("#adtext1").show();
        //     return false;
        // } else {
        //     $("#adtext1").hide();
        // }

        var number = $.trim($("#bc_number").val());
        if (number.length == 0) {
            $("#adtext2 li").html("<i></i>银行卡号不能为空");
            $("#adtext2").show();
            return false;
        } else {
            $("#adtext2").hide();
        }

        // if (number.length < 15) {
        //     $("#adtext2 li").html("<i></i>请填写正确的银行卡号");
        //     $("#adtext2").show();
        //     return false;
        // } else {
        //     $("#adtext2").hide();
        // }

        var oper = $.trim($("#branch").val());
        if (oper.length == 0) {
            $("#adtext3").show();
            return false;
        } else {
            $("#adtext3").hide();
        }

        <?php
          if (@$showWithdrawPasswordField){
        ?>
        var anse = $.trim($("#withdrawal_password").val());
        if (anse.length == 0) {
            $("#adtext4").show();
            return false;
        } else {
            $("#adtext4").hide();
        }

        <?php
          }
        ?>

        var amount = $('#amount').val();
        var lowest = $('#lowest').val();
        var limit = $('#limit').val();

        if( amount == "" ){
          $("#tkts2").show();
          $("#max_amt").hide();
            $("#min_amt").hide();
            return false;
          }else if( limit != 0 && amount > limit ){
            $("#max_amt").show();
            $("#tkts2").hide();
            $("#min_amt").hide();
            return false;
          }else if( amount < lowest ){
            $("#min_amt").show();
            $("#tkts2").hide();
          $("#max_amt").hide();
            return false;
          }

        if (confirm("您确定提交吗？")) {

            $("#loading").removeClass('hide');

            $.ajax({
              url: $('#new__bankacct_form').attr('action'),
              type: 'POST',
              data: $('#new__bankacct_form').serialize(),
              success: function(data){

                  alert(data.msg);
                  $("#bankicon").html("<?=lang('Please Select Bank')?>");
                  $("#bc_number").val("");
                  $("#branch").val("");
                  $("#amount").val("");
                  $("#withdrawal_password").val("");
                  $("#loading").addClass('hide');

              }
            });

        }


        return false;


    });

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
            var id = $(this).attr('id');

            $("#bankicon").text(title);
            $("#bank_name").val(id);

        });



    });
});
</script>
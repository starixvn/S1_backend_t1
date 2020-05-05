<?php
$na = "";
$na_attr = "";
$pa = "";
$pa_attr = "";
$account_type = "na";
if (isset($_POST['account_type']) && $_POST['account_type'] == 'pa') {
    $account_type="pa";
    $pa = "checked";
    $pa_attr = " ";
    $na = " ";
    $na_attr = "disabled";
} else {
    $account_type="na";
    $pa = " ";
    $pa_attr = "disabled";
    $na = "checked";
    $na_attr = " ";
}
$bigWallet = json_decode($player['big_wallet'], TRUE);
$subwalletsBalance = array();
foreach ($bigWallet['sub'] as $apiId=>$subWallet) {
    $subwalletsBalance[$apiId]=$subWallet['total_nofrozen'];
}
$total_balance = $bigWallet['main']['total_nofrozen'] + array_sum($subwalletsBalance) + $bigWallet['main']['frozen'];

$bank_select_detail = [];
foreach($playerBankDetails as $bank_detail){
    $bank_select_detail[$bank_detail['playerBankDetailsId']] = [
        'bank_detail_id' => $bank_detail['playerBankDetailsId'],
        'bankAccountFullName' => $bank_detail['bankAccountFullName'],
        'bankAccountNumber' => $bank_detail['bankAccountNumber'],
        'bankName' => lang($bank_detail['bankName'])
    ];
}
?>
<div class="mmenu">
    <div class="mm_header">
        <ul>
            <li><?=lang('cashier.74')?></li>
        </ul>
        <div class="mm_exit"></div>
    </div>
    <div class="mm_main">
        <ul>
        <?php foreach ($playerBankDetails as $row):  ?>
            <li id="<?=$row['playerBankDetailsId']?>"><?php echo lang($row['bankName']) . ' - ' . $row['bankAccountFullName'] ?><i></i></li>
        <?php endforeach ?>
        </ul>
    </div>
</div>
<div id="addcard" class="fourpage" onclick="document.location='<?=site_url('/player_center/iframe_bankDetails#add_new_withdrawal_bank')?>'" style="cursor:pointer;">
    <ul>
        <li></li>
        <a><?=lang('add_withdrawal_bank')?></a> <i></i>
    </ul>
</div>
<form id="new_acct_form" action="<?=site_url('iframe_module/verifyWithdrawal/')?>" method="post" class="form-horizontal">
	<?=$double_submit_hidden_field?>
    <input type="hidden" name="redirect_url" id="redirect_url" value="player_center/menu/">
    <input type="hidden" name="dw_ip" id="dw_ip" value="<?=$this->input->ip_address();?>">
    <input type="hidden" name="account_type" id="account_type" value="<?=$account_type?>">
    <div class="input">
        <label><?=lang('account.balance')?>: <strong><?=$this->utils->displayCurrency($player['totalBalanceAmount'], $this->utils->isEnabledFeature('enable_currency_symbol_in_the_withdraw'))?></strong></label>
        <div id="xzyhk" class="input_01 xlmenu"><a id="drawbankicon"><?=lang('Please Select Bank')?></a></div>
        <input id="preferred_account" name="preferred_account" type="hidden" value="">
        <ul id="tkts1">
            <li><i></i><?=lang('cashier.selectAccount')?></li>
        </ul>

        <input id="bankName" class="input_01" type="text" placeholder="<?=lang('xpj.withdraw.bank_name');?>" disabled="disabled">
        <input id="bankAccountFullName" class="input_01" type="text" placeholder="<?=lang('xpj.withdraw.bank_account_name');?>" disabled="disabled">
        <input id="bankAccountNumber" class="input_01" type="text" placeholder="<?=lang('xpj.withdraw.bank_account_number');?>" disabled="disabled">

        <input id="amount" name="amount" placeholder="<?=lang('pay.reqamt')?>" type="text" class="input_01">
        <label>
            <?=lang('Min Withdrawal')?>:
            <strong><?=$this->utils->displayCurrency($min_withdraw_rule, $this->utils->isEnabledFeature('enable_currency_symbol_in_the_withdraw'))?></strong>
        </label>
        <br />
        <label>
            <?=lang('Max Withdrawal')?>:
            <strong><?=$this->utils->displayCurrency($max_withdraw_per_transaction, $this->utils->isEnabledFeature('enable_currency_symbol_in_the_withdraw'))?></strong>
        </label>

        <input id="drawCount" type="hidden" value="4">
        <input id="drawSum" type="hidden" value="200,000.00">
        <input id="lowest" type="hidden" value="<?=$min_withdraw_rule;?>">
        <input id="limit" type="hidden" value="<?=$max_withdraw_per_transaction;?>">

        <ul id="tkts2">
            <li><i></i><?=lang('Please Input Withdrawal Amount')?></li>
        </ul>

        <?php if ($this->utils->isEnabledFeature('enable_withdrawal_amount_note')) : ?>
            <div class="helper-content text-danger font-weight-bold">
                <p><?=lang('collection_withdrawal_amount')?></p>
            </div>
        <?php endif; ?>

        <?php if ($enabled_withdrawal_password && $showWithdrawPasswordField): ?>
        <input id="withdrawal_password" name="withdrawal_password" placeholder="<?=lang('Withdrawal Password')?>" type="password" class="input_01">
        <ul id="tkts3">
            <li><i></i><?=lang("Password can't be empty")?></li>
        </ul>
        <?php endif ?>
        <input type="submit" class="login_btn" id="actkbtn" value="<?=lang('lang.withdrawal.save')?>" />
    </div>
</form>
<!-- end of row-->

<div class="modal fade" tabindex="-1" data-backdrop="static" role="dialog" aria-hidden="true" id="summaryModal">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title summaryModal_title"></h4>
            </div>
            <div class="modal-body summaryModal_body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-close" data-dismiss="modal"><?php echo lang('Close'); ?></button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<script>
$(document).ready(function () {
    var has_bank_details = parseInt("<?php echo (empty($playerBankDetails)) ? 0 : 1 ?>");
    var player_bank_details = JSON.parse('<?=json_encode($bank_select_detail)?>');
    // var has_withdraw_password = <?php echo json_encode(!empty($withdraw_password));?>;
    var show_warning_for_one_withdraw = <?=json_encode(!!$show_warning_for_one_withdraw)?>;

    $('#ht').html('<?=lang('withdrawal')?>');
    // $("#amount").numeral();
    var newAcctForm = $('#new_acct_form');
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
    var preferredAcct = $('#preferred_account');

    var xlmenu = ".mmenu";
    var xlmovie = "mmenu_movie";

    var modal = $('#summaryModal').modal({
        'show': false
    });
    window.modal = modal;

    <?php if ( empty($realname) ): ?>
        modalConfirmation(null,'<?=lang('withdrawal.msg2')?>','/player_center/profile#usersettings');
    <?php elseif ($enabled_withdrawal_password && $showWithdrawPasswordField && empty($withdraw_password)): ?>
        modalConfirmation(null,'<?=lang('withdrawal.msg3')?>','/player_center/profile#usersettings');
    <?php endif ?>

    if(has_bank_details === 0){
        modal.off('show.bs.modal').on('show.bs.modal', function(){
            modal.find('.modal-title').html("<?=lang('xpj.withdraw.msg1')?>");
            modal.find('.modal-body').html("<?=lang('xpj.withdraw.msg2')?>");
            modal.find('.btn-close').html("<?=lang('xpj.withdraw.msg3')?>");
        });
        modal.off('hide.bs.modal').on('hide.bs.modal', function(){
            window.location.href = '<?=site_url('/player_center/iframe_bankDetails#add_new_withdrawal_bank')?>#main-content';
        });
        modal.modal('show');

        return;
    }

    if(show_warning_for_one_withdraw){
        newAcctForm.find('input, button, select, textarea, option').each(function(){
            $(this).attr('disabled', 'disabled').addClass('disabled');
        });

        var modal = $('#summaryModal').modal({
            'show': false
        });
        modal.off('show.bs.modal').on('show.bs.modal', function(){
            modal.find('.modal-title').html("<?=lang('alert-danger')?>");
            modal.find('.modal-body').html("<?=lang('Sorry, your last withdraw request is not done, so you can\'t start new request')?>");
            modal.find('.btn-close').html("<?=lang('Close')?>");
        });
        modal.off('hide.bs.modal').on('hide.bs.modal', function(){
            window.location.href = '<?=site_url('/player_center/menu')?>';
        });
        modal.modal('show');
    }

    $(".fourpage").click(function () {

        var url_three = $(this).attr("url");
        var hdtext = $(this).find("a").text();

        setTimeout("$('#pagethree .page').addClass('hide')", 300);

        pagethree.addClass(leftmove);
        pagefour.addClass("move");

        $("#ht2").text(hdtext);

        if (url_three) {

            $.ajax({
                type: "get",
                url: url_three,
                beforeSend: function () {
                    $("#loading").show();
                },
                success: function (data) {
                    $("#loading").hide();
                    pagefour.find(".page").html(data);
                },
            });
        };
        $(".bb_four").click(function () {

            setTimeout("$('#pagefour .page').html('')", 300);
            pagethree.find(".page").removeClass('hide');
            pagethree.removeClass(leftmove);
            pagefour.removeClass("move");

        });
    });

    $(xlmenu).hide();
    $(".xlmenu").click(function () {
        $(xlmenu).show();
        setTimeout(function(){
            $(xlmenu).addClass(xlmovie);
        }, 300);

        var mmenu = $(this).attr("id");

        if (mmenu == "xzyhk") { /*选择银行卡*/
            choicemenu(mmenu);
        }
        $(".mm_exit").click(function () {
            $(xlmenu).removeClass(xlmovie);
            setTimeout(function(){
                $(xlmenu).hide();
            }, 300);
        });
    });

    function choicemenu(mmenu) {
        $(".mm_main li").unbind("click");
        $(".mm_main li").click(function () {
            $(".mm_main li i").removeClass("border");
            $(this).find("i").addClass("border");
            var title = $(this).text();
            if (mmenu == "xzyhk")/*ATM选择方式菜单*/ {
                $("#drawbankicon").text(title);
                $("#preferred_account").val($(this).attr('id')).trigger('change');
                closemenu()
            }
        });
    };

    function closemenu() {
        $(xlmenu).removeClass(xlmovie);
        setTimeout(function(){
            $(xlmenu).hide();
        }, 300);
    };

    var submit_timer = null;
    $("#actkbtn").click(function () {
        var bank = $.trim($("#drawbankicon").text());
        if (bank.indexOf("<?=lang('Please Select Bank')?>") != -1) {
            $("#tkts1").show();
            return false;
        }
        else {
            $("#tkts1").hide();
        }
        bank = $("#bankval").val();

        var amt = $.trim($("#amount").val());
        if (amt.length == 0) {
            $("#tkts2 li").html("<i></i><?=lang('Please Input Withdrawal Amount')?>");
            $("#tkts2").show();
            return false;
        } else {
            $("#tkts2").hide();
        }

        if (isNaN(amt)) {
            $("#tkts2 li").html("<i></i><?=lang('Please Input Withdrawal Amount')?>");
            $("#tkts2").show();
            return false;
        }
        else {
            $("#tkts2").hide();
        }
        var minamt = $("#lowest").val();
        amt = parseFloat(amt);
        if (amt < parseFloat(minamt)) {
            $("#tkts2 li").html("<i></i><?=lang('withdrawal.msg6')?>" + number_format(minamt) + "<?=lang('currency_name')?>");
            $("#tkts2").show();
            return false;
        }
        else {
            $("#tkts2").hide();
        }

        var maxamt = $("#limit").val();
        if (maxamt != 0 && amt > parseFloat(maxamt)) {
            $("#tkts2 li").html("<i></i><?=lang('withdrawal.msg7')?>" + number_format(maxamt) + "<?=lang('currency_name')?>");
            $("#tkts2").show();
            return false;
        }
        else {
            $("#tkts2").hide();
        }

        if($("#withdrawal_password").length){
            var pwd = $.trim($("#withdrawal_password").val());
            if (pwd.length == 0) {
                $("#tkts3 li").html("<i></i><?=lang('Please Input Withdrawal Password')?>");
                $("#tkts3").show();
                return false;
            } else {
                $("#tkts3").hide();
            }

            if (pwd.length < 6) {
                $("#tkts3 li").html("<i></i><?=lang('Please check withdrawal password you entered')?>");
                $("#tkts3").show();
                return false;
            } else {
                $("#tkts3").hide();
            }
        }

        var submit_btn = $(this);
        // JIRA PRH-459: Delay popup of confirm dialog, so error messages can be hidden correctly
        setTimeout(function() {
            submit_btn.addClass('disabled').attr('disabled', 'disabled').prop('disabled', true);
            if (confirm("<?=lang('confirm_submit')?>")) {
                Loader.show();

                if(submit_timer !== null){
                    clearTimeout(submit_timer);
                }

                submit_timer = setTimeout(function(){
                    newAcctForm.submit();
                }, 300);
            }else{
                submit_btn.removeClass('disabled').removeAttr('disabled').prop('disabled', false);
            }
        }, 50);

        return false;

    });

    preferredAcct.on('change', function(){
        var bank_name, bank_account_name, bank_account_number;
        var select_val = $(this).val(), bank_detail;
        if(player_bank_details.hasOwnProperty(select_val)){
            bank_detail = player_bank_details[select_val];
            $("#account_type").val("pa");
        }else{
            bank_detail = {
                "bankAccountFullName": "",
                "bankAccountNumber": "",
                "bankName": ""
            };
        }
        $('#bankAccountFullName').val(bank_detail.bankAccountFullName).attr('value', bank_detail.bankAccountFullName);
        $('#bankAccountNumber').val(bank_detail.bankAccountNumber).attr('value', bank_detail.bankAccountNumber);
        $('#bankName').val(bank_detail.bankName).attr('value', bank_detail.bankName);
    });
});

function modalConfirmation(classTrigger,trigger_message,redirect_url){
    modal.off('show.bs.modal').on('show.bs.modal', function(){
        modal.find('.modal-title').html('<?=lang('alert-danger')?>');
        modal.find('.modal-body').html(trigger_message);
        modal.find('.btn-close').html('<?=lang('Close')?>');
    });
    modal.off('hide.bs.modal').on('hide.bs.modal', function(){
        if(redirect_url !== undefined){
            window.location.href = redirect_url;
        }else{
            window.location.href = '<?=site_url('/player_center/menu')?>';
        }
    });
    modal.modal('show');
}

</script>
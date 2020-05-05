<?php
$show_deposit_bank_details = $this->utils->isEnabledFeature('show_deposit_bank_details');

$payment_auto_account_list = [];
$payment_manual_min_deposit = NULL;
$payment_manual_max_deposit = NULL;

if(count($payment_auto_accounts) <= 0 && count($payment_manual_accounts)){
    redirect('/player_center/manual_payment/1');

    exit;
}

foreach ($payment_auto_accounts as $payment_account) {
    $payment_account_data = [
        'bankTypeId' => $payment_account->bankTypeId,
        'bankName' => lang($payment_account->payment_type),
        'minDeposit' => $payment_account->vip_rule_min_deposit_trans,
        'maxDeposit' => $payment_account->vip_rule_max_deposit_trans,
        'minDeposit_currency' => $this->utils->formatCurrency($payment_account->vip_rule_min_deposit_trans),
        'maxDeposit_currency' => $this->utils->formatCurrency($payment_account->vip_rule_max_deposit_trans),
        'flag' => $payment_account->flag,
        'payment_order'=> $payment_account->payment_order
    ];

    if($payment_account_data['maxDeposit'] <= 0){
        $payment_account_data['maxDeposit'] = $this->utils->getConfig('defaultMaxDepositDaily');
    }

    if($payment_account_data['minDeposit'] <= 0){
        $payment_account_data['minDeposit'] = 0;
    }

    if($show_deposit_bank_details){
        $payment_account_data['bankAccountName'] = $payment_account->payment_account_name;
        $payment_account_data['bankAccountNo'] = $payment_account->payment_account_number;
        $payment_account_data['bankCity'] = NULL;
        $payment_account_data['branchName'] = $payment_account->payment_branch_name;
    }

    if ($payment_account->flag == AUTO_ONLINE_PAYMENT) {
        $payment_auto_account_list['cid-' . $payment_account_data['bankTypeId']] = $payment_account_data;
    }
}

foreach ($payment_manual_accounts as $payment_manual) {
    $payment_manual_min_deposit = ($payment_manual_min_deposit === NULL) ? $payment_manual->vip_rule_min_deposit_trans : $payment_manual_min_deposit;
    $payment_manual_min_deposit = ($payment_manual->vip_rule_min_deposit_trans < $payment_manual_min_deposit) ? $payment_manual->vip_rule_min_deposit_trans : $payment_manual_min_deposit;

    $payment_manual_max_deposit = ($payment_manual_max_deposit === NULL) ? $payment_manual->vip_rule_max_deposit_trans : $payment_manual_max_deposit;
    $payment_manual_max_deposit = ($payment_manual->vip_rule_max_deposit_trans > $payment_manual_max_deposit) ? $payment_manual->vip_rule_max_deposit_trans : $payment_manual_max_deposit;
}
?>

<?php if(count($payment_auto_account_list)): ?>
    <div class="panel no-gutter">
        <div class="panel-title">
            <?=lang('deposit.onlinepayment')?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div id="deposit_bank_list" class="tabs_bank">
                    <?php /*
                <span class="tabs_bank_item">开通网银</span>
                <span class="tabs_bank_item tabs_bank_item_active">金付卡 - 支付宝</span>
                <span class="tabs_bank_item">金付卡 - 微信</span>
                */ ?>
                </div>
            </div>
            <div class="row">
                <div class="col col-sm-5 col-xs-5">
                    <label class="deposit-info label single_minimum_payment label_01"><?=lang('xpj.deposit.single_minimum_payment')?></label>
                </div>
                <div class="col col-sm-7 col-xs-7">
                    <span id="deposit-auto-single_minimum_payment" class="deposit-info field single_minimum_payment field_01"></span>
                </div>
            </div>
            <div class="row">
                <div class="col col-sm-5 col-xs-5">
                    <label class="deposit-info label single_highest_payment label_01"><?=lang('xpj.deposit.single_highest_payment')?></label>
                </div>
                <div class="col col-sm-7 col-xs-7">
                    <span id="deposit-auto-single_highest_payment" class="deposit-info field single_highest_payment field_01"></span>
                </div>
            </div>
            <?php if(!$this->utils->isEnabledFeature('hidden_mobile_deposit_MaxDailyDeposit_field')):?>
                <div class="row">
                    <div class="col col-sm-5 col-xs-5">
                        <label class="deposit-info label accumulated_payment_limit label_01"><?=lang('xpj.deposit.accumulated_payment_limit')?></label>
                    </div>
                    <div class="col col-sm-7 col-xs-7">
                        <span id="deposit-auto-accumulated_payment_limit" class="deposit-info field accumulated_payment_limit field_01"><?php echo lang('xpj.deposit.nolimit');?></span>
                    </div>
                </div>
            <?php endif;?>
            <?php if(!$this->utils->isEnabledFeature('hidden_mobile_deposit_TimeOfArrival_field')):?>
                <div class="row">
                    <div class="col col-sm-5 col-xs-5">
                        <label class="deposit-info label arrive_time label_01"><?=lang('xpj.deposit.arrive_time')?></label>
                    </div>
                    <div class="col col-sm-7 col-xs-7">
                        <span id="deposit-auto-arrive_time" class="deposit-info field arrive_time field_01"><?=lang('xpj.deposit.realtime')?></span>
                    </div>
                </div>
            <?php endif;?>
            <div class="row">
                <a id="deposit-auto-info-btn" class="mc-btn mc-btn-confirm" title="<?=lang('immediate.deposit')?>" href="javascript: void(0);"><?=lang('xpj.mobile.deposit.immediate_payment')?></a>
            </div>
        </div>
    </div>
<?php endif ?>
<?php if (!empty($payment_manual_accounts)) { ?>
    <div class="panel no-gutter">
        <div class="panel-title">
            <?=lang('Bank Deposit')?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col col-sm-5 col-xs-5">
                    <label class="deposit-info label payment_type label_01"><?=lang('xpj.deposit.payment_type')?></label>
                </div>
                <div class="col col-sm-7 col-xs-7">
                    <span id="deposit-manual-payment_type" class="deposit-info field payment_type field_01"><?=lang('xpj.deposit.manual');?></span>
                </div>
            </div>
            <?php if ($this->utils->isEnabledFeature('enable_manual_deposit_realname')) :?>

                <div class="row">
                    <div class="col col-sm-5 col-xs-5">
                        <label class="deposit-info label arrive_time label_01"><?=lang('report.in11')?></label>
                    </div>
                    <?php if(isset($firstNameflg) && $firstNameflg):?>
                        <div class="col col-sm-7 col-xs-7">
                            <span id="deposit-manual-realname" class="deposit-info field realname field_01"> <?php echo $firstName?></span>
                        </div>
                    <?php else:?>
                        <div class="col col-sm-7 col-xs-7">
                            <span id="deposit-manual-realname" class="deposit-info field realname field_01"> <?=lang('reg.firstName')?></span>
                        </div>
                    <?php endif;?>
                </div>
            <?php endif;?>
            <div class="row">
                <div class="col col-sm-5 col-xs-5">
                    <label class="deposit-info label single_minimum_payment label_01"><?=lang('xpj.deposit.single_minimum_payment')?></label>
                </div>
                <div class="col col-sm-7 col-xs-7">
                    <span id="deposit-manual-single_minimum_payment" class="deposit-info field single_minimum_payment field_01"><?=$this->utils->formatCurrency($payment_manual_min_deposit)?></span>
                </div>
            </div>
            <div class="row">
                <div class="col col-sm-5 col-xs-5">
                    <label class="deposit-info label single_highest_payment label_01"><?=lang('xpj.deposit.single_highest_payment')?></label>
                </div>
                <div class="col col-sm-7 col-xs-7">
                    <span id="deposit-manual-single_highest_payment" class="deposit-info field single_highest_payment field_01"><?=$this->utils->formatCurrency($payment_manual_max_deposit)?></span>
                </div>
            </div>
            <?php if(!$this->utils->isEnabledFeature('hidden_mobile_deposit_MaxDailyDeposit_field')):?>
                <div class="row">
                    <div class="col col-sm-5 col-xs-5">
                        <label class="deposit-info label accumulated_payment_limit label_01"><?=lang('xpj.deposit.accumulated_payment_limit')?></label>
                    </div>
                    <div class="col col-sm-7 col-xs-7">
                        <span id="deposit-manual-accumulated_payment_limit" class="deposit-info field accumulated_payment_limit field_01"><?php echo lang('xpj.deposit.nolimit');?></span>
                    </div>
                </div>
            <?php endif;?>
            <?php if(!$this->utils->isEnabledFeature('hidden_mobile_deposit_TimeOfArrival_field')):?>
                <div class="row">
                    <div class="col col-sm-5 col-xs-5">
                        <label class="deposit-info label arrive_time label_01"><?=lang('xpj.deposit.arrive_time')?></label>
                    </div>
                    <div class="col col-sm-7 col-xs-7">
                        <span id="deposit-manual-arrive_time" class="deposit-info field arrive_time field_01"><?=lang('xpj.deposit.minutes')?></span>
                    </div>
                </div>
            <?php endif;?>
            <div class="row">
                <a id="deposit-manual-info-btn" class="mc-btn mc-btn-confirm" title="<?=lang('immediate.deposit')?>" href="<?=site_url('iframe_module/iframe_makeDeposit/1');?>"><?=lang('xpj.mobile.deposit.immediate_payment')?></a>
            </div>
        </div>
    </div>
<?php } ?>


<script type="text/javascript">
    $(document).ready(function(){
        var payment_auto_account_list = JSON.parse('<?=json_encode($payment_auto_account_list);?>');

        var deposit_bank_list = $('#deposit_bank_list');

        deposit_bank_list.empty();
        $.each(payment_auto_account_list, function(key, value){
            var option = $('<span>').addClass('tabs_bank_item').text(value.bankName).data('bankTypeId', key);
            deposit_bank_list.append(option);
        });

        deposit_bank_list.find('span').off('click').on('click', function(){
            deposit_bank_list.find('span').removeClass('tabs_bank_item_active');
            $(this).addClass('tabs_bank_item_active');

            var select_val = $(this).data('bankTypeId');
            var auto_online_payment;
            if(payment_auto_account_list.hasOwnProperty(select_val)){
                auto_online_payment = payment_auto_account_list[select_val];
            }else{
                auto_online_payment = {
                    "bankTypeId": "",
                    "bankName": "",
                    "minDeposit": "",
                    "maxDeposit": "",
                    "minDeposit_currency": "",
                    "maxDeposit_currency": "",
                    "flag": "<?=AUTO_ONLINE_PAYMENT?>",
                };
            }

            $('#deposit-auto-single_minimum_payment').html(auto_online_payment.minDeposit_currency);
            $('#deposit-auto-single_highest_payment').html((auto_online_payment.maxDeposit <= 0) ? "<?=lang('xpj.deposit.nolimit')?>" :  auto_online_payment.maxDeposit_currency);
            $('#deposit-auto-info-btn').attr('href', '<?=site_url("iframe_module/iframe_makeDeposit")?>/' + auto_online_payment.flag + '/' + auto_online_payment.bankTypeId);
        });

        deposit_bank_list.find('span:first').addClass('tabs_bank_item_active').trigger('click');
    });
</script>
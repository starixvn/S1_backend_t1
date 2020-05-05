<?php if($system_feature_add_security_on_deposit_transaction && $system_feature_enabled_withdrawal_password && empty($player['withdraw_password'])): ?>
<script type="text/javascript">
    $(function(){
        MessageBox.danger("<?=lang('Withdrawal Password is Required!')?>", "<?=lang('Please set your withdraw password')?>", function(){
            Loader.show();
            window.location.href = '/player_center2/security#withdrawal';
        });
    });
</script>
<?php endif ?>

<div class="row form-group deposit-process-mode-<?=in_array($deposit_process_mode, array('2','3')) ? '2' : $deposit_process_mode ?> select-payment-account">
    <p class="step"><span class="step-icon"><?=$deposit_step++?></span>
        <?php if(!$hide_deposit_selected_bank_and_text_for_ole777): ?>
            <label class="control-label"><?=lang('Please Select Deposit Bank')?></label>
        <?php elseif($isAlipay): ?>
            <label class="control-label"><?=lang('Alipay Transfer Info')?></label>
        <?php elseif($isUnionpay): ?>
            <label class="control-label"><?=lang('Unionpay Transfer Info')?></label>
        <?php else:?>
            <label class="control-label"><?=lang('Deposit To')?></label>
        <?php endif;?>
    </p>
    <div class="input-group hidden">
        <input type="text" class="hidden" id="DepositPaymentAccount" name="DepositPaymentAccount" value="" required="required"
            data-required-error="<?=($system_feature_add_security_on_deposit_transaction && $system_feature_enabled_withdrawal_password) ? lang('Withdrawal Password Verify is Required!') : lang('Fields with (*) are required.')?>"
            />
    </div>

    <?php if ($system_feature_add_security_on_deposit_transaction && $system_feature_enabled_withdrawal_password) :?>
    <div id="deposit-payment-account-security-verify">
        <div class="see-deposit-btn" id="showDepositVerifyModal" onclick="showDepositVerifyModal()"><i class="fa fa-university"></i> <?= lang('See Deposit Bank') ?></div>
    </div>
    <?php endif ?>
    <div class="col-xs-12 deposit-payment-account-info nopadding <?=($system_feature_add_security_on_deposit_transaction && $system_feature_enabled_withdrawal_password) ? 'hidden' : '' ?>">
        <?php if(!$hide_deposit_selected_bank_and_text_for_ole777) {?>
        <p><strong><?=lang('Your current payment account') ?></strong></p>
        <?php }?>
        <div class="bank-list-container">
            <?php if(($deposit_process_mode === DEPOSIT_PROCESS_MODE2) || ($deposit_process_mode === DEPOSIT_PROCESS_MODE3) ): ?>
                <?php if($this->utils->isEnabledFeature('enable_deposit_category_view') === false) : ?>
                <button type="button" class="bank-entry active">
                    <a href="javascript: void(0);" class="active-payment-account-info">
                        <i class="fa fa-check-circle" aria-hidden="true"></i>
                        <span id="active-payment-account-id" class="b-icon">
                            &nbsp;<?=lang('No payment account selected'); ?>
                        </span>
                    </a>
                    <i class="fa fa-caret-down" aria-hidden="true"></i>
                </button>
                <?php endif ?>
            <?php endif ?>


            <ul id="bankDepositPanel" class="bank-list">
                <?php if (empty($payment_manual_accounts)):?>
                    <p><?=lang("No Availble Bank Deposit")?></p>
                <?php else:?>
                <?php foreach ($payment_manual_accounts as $key => $payment_account): ?>
                    <li data-payment_account_id="<?=$payment_account->payment_account_id?>"
                        data-bank="<?=$payment_account->bankTypeId?>"
                        data-flag="<?=$payment_account->flag?>"
                        data-min_deposit_trans="<?=$payment_account->vip_rule_min_deposit_trans?>"
                        data-max_deposit_trans="<?=$payment_account->vip_rule_max_deposit_trans?>">
                        <a href="" data-toggle="tab" title="<?=sprintf('%s - %s', lang($payment_account->payment_type), $payment_account->payment_account_name)?>">
                            <i class="fa fa-check-circle" aria-hidden="true"></i>
                            <?php if(empty($payment_account->account_icon_url)): ?>
                                <span class="b-icon bank_<?=$payment_account->bankTypeId?>"><?=sprintf('%s - %s', lang($payment_account->payment_type), $payment_account->payment_account_name)?></span>
                            <?php else: ?>
                                <span class="b-icon-custom"><img src="<?=$payment_account->account_icon_url?>" /><?=sprintf('%s - %s', lang($payment_account->payment_type), $payment_account->payment_account_name)?></span>
                            <?php endif ?>
                        </a>
                    </li>
                <?php endforeach;?>
                <?php endif?>
            </ul>
        </div>

        <?php if($deposit_process_mode === DEPOSIT_PROCESS_MODE1): ?>
        <div class="show-btn"><i class="fa fa-angle-down"></i> <?=lang('show Bank list')?></div>
        <div class="hide-btn" style="display: none;"><i class="fa fa-angle-up "></i> <?=lang('hide Bank list')?></div>
        <?php else: ?>
        <div class="payment-account-detail">
            <div class="col
                <?php
                    if($isUnionpay) {
                        echo'col-xs-12 ';
                    }else{
                        echo'col-xs-12 col-md-6 ';
                    }
                ?>nopadding">
                <?php if($this->utils->isEnabledFeature('enable_deposit_category_view') && !$isAlipay && !$isUnionpay): ?>
                <p id="active-payment-account-bank-name-block">
                    <strong><?=lang('Bank Name') ?>:</strong>
                    <span id="active-payment-account-bank-name"></span>
                </p>
                <?php endif;?>
                <p>
                    <!-- Bank Account # -->
                    <?php if($isAlipay):?>
                    <strong><?=lang('Alipay Account') ?>:</strong>
                    <span id="active-payment-account-number"></span>
                    <?php elseif($isUnionpay):?>
                    <strong></strong>
                    <span></span>
                    <?php elseif($isWechat):?>
                    <strong><?=lang('WeChat Bank Card Account') ?>:</strong>
                    <span id="active-payment-account-number"></span>
                        <?php if($this->CI->utils->is_mobile()):?>
                            <button type="button" class="btn btn-copy pull-right"
                            data-clipboard-action="copy"
                            data-clipboard-target="#active-payment-account-number"
                            title="<?=lang('Copied')?>"><?=lang('Copy')?></button>
                        <?php endif;?>
                    <?php else:?>
                    <strong><?=lang('pay.acctnumber') ?>:</strong>
                    <span id="active-payment-account-number"></span>
                    <button type="button" class="btn btn-copy pull-right"
                        data-clipboard-action="copy"
                        data-clipboard-target="#active-payment-account-number"
                        title="<?=lang('Copied')?>"><?=lang('Copy')?></button>
                    <?php endif;?>
                </p>
                <p>
                    <!-- Bank Account Name -->
                    <?php if($isAlipay):?>
                    <strong><?=lang('pay.acctname') ?>:</strong>
                    <span id="active-payment-account-name"></span>
                    <?php elseif($isUnionpay):?>
                    <strong></strong>
                    <span></span>
                        <?php if($this->CI->utils->is_mobile()):?>
                            <strong><?=lang('pay.acctname') ?>:</strong>
                            <span id="active-payment-account-name"></span>
                        <?php endif;?>
                    <?php elseif($isWechat):?>
                    <strong><?=lang('pay.acctname') ?>:</strong>
                    <span id="active-payment-account-name"></span>
                        <?php if($this->CI->utils->is_mobile()):?>
                            <button type="button" class="btn btn-copy pull-right"
                            data-clipboard-action="copy"
                            data-clipboard-target="#active-payment-account-number"
                            title="<?=lang('Copied')?>"><?=lang('Copy')?></button>
                        <?php endif;?>
                    <?php else:?>
                    <strong><?=lang('pay.acctname') ?>:</strong>
                    <span id="active-payment-account-name"></span>
                    <button type="button" class="btn btn-copy pull-right"
                        data-clipboard-action="copy"
                        data-clipboard-target="#active-payment-account-name"
                        title="<?=lang('Copied')?>"><?=lang('Copy')?></button>
                    <?php endif;?>
                </p>
                <?php if(!$hide_bank_branch_in_payment_account_detail_player_center && !$isAlipay && !$isUnionpay && !$isWechat):?>
                <p>
                    <strong><?=lang('pay.branchname') ?>:</strong>
                    <span id="active-payment-account-branch-name"></span>
                    <button type="button" class="btn btn-copy pull-right"
                        data-clipboard-action="copy"
                        data-clipboard-target="#active-payment-account-branch-name"
                        title="<?=lang('Copied')?>"><?=lang('Copy')?></button>
                </p>
                <?php endif;?>
            </div>
            <div class="col col-xs-6 nopadding">
                <span id="active-payment-account-image"><img src="" /></span>
            </div>
            <div class="col col-xs-6 text-danger qrcode-img-hint">
                <p>
                    <?php if($isAlipay && $this->CI->utils->is_mobile()):?>
                    <?=lang('manual.alipay.qrcodeimg.hint')?>
                    <?php endif;?>
                    <?php if($isUnionpay && $this->CI->utils->is_mobile()):?>
                    <?=lang('manual.unionpay.mobile.qrcodeimg.hint')?>
                    <?php elseif($isUnionpay):?>
                    <?=lang('manual.unionpay.pc.qrcodeimg.hint')?>
                    <?php else:?>
                    <?php endif;?>
                </p>
            </div>
        </div>
        <?php endif ?>
    </div>

    <div class="clearfix"></div>
    <div class="help-block with-errors"></div>
</div>

<hr/>
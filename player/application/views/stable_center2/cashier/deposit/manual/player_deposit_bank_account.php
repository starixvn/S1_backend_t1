<?php if($this->operatorglobalsettings->getSettingValueWithoutCache('financial_account_enable_deposit_bank')): ?>
<div class="row form-group deposit-process-mode-<?=in_array($deposit_process_mode, array('2','3')) ? '2' : $deposit_process_mode ?> select-player-deposit-bank-account">
    <p class="step"><span class="step-icon"><?=$deposit_step++?></span><label class="control-label"><?=lang('Please select deposit account') ?></label></p>
    <div class="input-group hidden">
        <input type="hidden" id="activeBankTypeIdField" name="bankTypeId" />
        <input type="text" class="form-control hidden" id="activeBankDetailsIdField" name="bankDetailsId" required="required"
            data-required-error="<?=lang('cashier.deposit.force_setup_player_deposit_bank_hint')?>" />
        <input type="hidden" id="activeAccNameField" name="bankAccName" />
        <input type="hidden" id="activeAccNumField" name="bankAccNum" />
        <input type="hidden" id="activeBankAddressField" name="bankAddress" />
        <input type="hidden" id="activeCityField" name="city" />
        <input type="hidden" id="activeProvinceField" name="province" />
        <input type="hidden" id="activeBranchField" name="branch" />
        <input type="hidden" id="activeMobileNumField" name="phone" />
    </div>
    <div class="player-deposit-bank-account-content">
        <div class="col col-xs-12 col-md-6 current-deposit-bank">
            <p><strong><?=lang('Your current deposit account') ?></strong></p>
            <ul class="bank-list">
                <li class="active">
                    <a class="bank-entry" href="javascript: void(0);" onclick="return false;">
                        <i class="fa fa-check-circle" aria-hidden="true"></i>
                        <span id="activeBankName" class="b-icon <?=!empty($bankTypeId) ? 'bank_'.$bankTypeId : '' ?>">
                            &nbsp;<?=!empty($bankName) ? lang($bankName) : lang('No deposit account selected'); ?>
                        </span>
                    </a>
                </li>
            </ul>
            <div>
                <div class="dispBankInfo">
                    <p>
                        <strong><?=lang('pay.acctnumber') ?>:</strong>
                        <span id="activeAccNum"></span>
                    </p>
                    <p>
                        <strong><?=lang('pay.acctname') ?>:</strong>
                        <span id="activeAccName"></span>
                    </p>
                    <p <?php if($hide_bank_branch_in_payment_account_detail_player_center){?> class="hidden"<?php } ?> >
                        <strong><?=lang('pay.branchname') ?>:</strong>
                        <span id="activeBranch"></span>
                    </p>
                    <p <?php if($hide_mobile_in_payment_account_detail_player_center){?> class="hidden"<?php } ?> >
                        <strong><?=lang('Mobile Number') ?>:</strong>
                        <span id="activeMobileNum"></span>
                    </p>
                </div>
            </div>
        </div>
        <div class="col col-xs-12 col-md-6 other-deposit-bank">
            <p class="hidden-xs hidden-sm"><?=lang('Change your current deposit account') ?></p>
            <div class="btn-container">
                <a class="mc-btn change-deposit-bank" href="javascript: void(0);" onclick="showSelectPlayerBankAccount();"><?=lang('Select other bank account') ?></a>
            </div>
<!--
            <?php if (Playerbankdetails::AllowAddBankDetail(Playerbankdetails::DEPOSIT_BANK, $player_bank_accounts)): ?>
            <div class="btn-container">
                <a class="mc-btn add-bank-account" href="javascript: void(0);" data-bank-type="deposit" data-payment-type-flag="<?=$payment_type_flag?>" data-callback="setupSelectPlayerDepositBank">
                    <?=lang('Add new bank deposit account') ?>
                </a>
            </div>
            <?php endif ?>
-->
        </div>
    </div>

    <div class="clearfix"></div>
    <div class="help-block with-errors"></div>

    <hr/>
</div>
<?php endif; ?>
<?php if($this->operatorglobalsettings->getSettingValueWithoutCache('financial_account_enable_deposit_bank')): ?>
<style type="text/css">
    .bank-info-row {
        padding-top: 10px;
    }
    #show-last-account-history {
        border: none;
        text-decoration: none;
        display: inline-block;
        font-weight: 700;
        font-family: initial;
        padding: 2px 10px;
        letter-spacing: 2px;
        border-radius: 6px;
        /*color: #bbb;*/
        background-color: #e6e6e6;
        border: 1px solid #adadad;
        margin: 0;
        width: auto;
        height: auto;
        line-height: inherit;
    }
</style>
<div class="row form-group deposit-process-mode-<?=in_array($deposit_process_mode, array('2','3')) ? '2' : $deposit_process_mode ?> select-player-deposit-bank-account">
    <p class="step"><span class="step-icon"><?=$deposit_step++?></span><label class=""><?=lang('Please select deposit account') ?></label></p>
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
        <div class="col col-xs-12 col-md-12 current-deposit-bank">
            <button type="button" id="show-last-account-history" class="">按此汇入上次数据</button>
            <div>
                <div class="dispBankInfo">
                    <div class="row bank-info-row form-group">
                        <div class="col-md-2">
                            <strong><label class="control-label"><?=lang('pay.bankname') ?></label></strong>
                        </div>
                        <div class="col-md-10">
                            <select class="form-control" name="activeBankName" id="activeBankName" required="required" data-required-error="<?=lang('Fields with (*) are required.')?>">
                                <option value="">-- <?=lang('cashier.74');?> --</option>
                                <?php foreach ($bankTypeList as $row): ?>
                                    <?php if ($row['enabled_deposit']): ?>
                                        <option value="<?=$row['bankTypeId']?>"><?=lang($row['bankName'])?></option>
                                    <?php endif?>
                                <?php endforeach ?>
                            </select>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="row bank-info-row form-group">
                        <div class="col-md-2">
                            <strong><label class="control-label"><?=lang('player.ui37');?></label></strong>
                        </div>
                        <div class="col-md-10">
                            <input type="text" class="form-control" style="width:100%" id="activeAccNum" name="activeAccNum" required="required" data-required-error="<?=lang('Fields with (*) are required.')?>">
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="row bank-info-row form-group">
                        <div class="col-md-2">
                            <strong><label class="control-label"><?=lang('pay.acctname') ?></label></strong>
                        </div>
                        <div class="col-md-10">
                            <input type="text" class="form-control" style="width:100%" id="activeAccName" name="activeAccName" required="required" data-required-error="<?=lang('Fields with (*) are required.')?>">
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="row bank-info-row form-group">
                        <div class="col-md-2">
                            <strong><?=lang('pay.branchname') ?></strong>
                        </div>
                        <div class="col-md-10">
                            <input type="text" class="form-control" style="width:100%" id="activeBranch" name="activeBranch">
                        </div>
                    </div>
                    <div class="row bank-info-row form-group">
                        <div class="col-md-2">
                            <strong><?=lang('Province') ?></strong>
                        </div>
                        <div class="col-md-10">
                            <input type="text" class="form-control" style="width:100%" id="activeProvince" name="activeProvince">
                        </div>
                    </div>
                    <div class="row bank-info-row form-group">
                        <div class="col-md-2">
                            <strong><?=lang('City') ?></strong>
                        </div>
                        <div class="col-md-10">
                            <input type="text" class="form-control" style="width:100%" id="activeCity" name="activeCity">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>
    <hr/>
</div>
<?php endif; ?>
<script type="text/javascript">
    $(function(){
        $('#show-last-account-history').on('click', function(){
            var default_bank_details = JSON.parse('<?=json_encode($default_bank_details);?>');

            if(jQuery.isEmptyObject(default_bank_details) === false) {
                $('#activeBankName').val(default_bank_details.bankTypeId).prop('selected', true);;
                $('#activeAccNum').val(default_bank_details.bankAccountNumber);
                $('#activeAccName').val(default_bank_details.bankAccountFullName);
                $('#activeBranch').val(default_bank_details.branch);
                $('#activeProvince').val(default_bank_details.province);
                $('#activeCity').val(default_bank_details.city);
            }
            else {
                MessageBox.info(lang('cashier.deposit.cannot_find_player_default_bank_details'));
            }
        });
    });
</script>
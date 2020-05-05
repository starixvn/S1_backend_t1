<style>
    /* remove input type=number spinner*/
    .remove_number_spinner::-webkit-outer-spin-button,
    .remove_number_spinner::-webkit-inner-spin-button{
        -webkit-appearance: none !important;
        margin: 0;
    }
    .remove_number_spinner{
        -moz-appearance: textfield;
    }
</style>
<input type="hidden" id="bankDetailId" name="input-bank-detail-id" value="<?=$playerBankDetail->playerBankDetailsId?>" />
<input type="hidden" id="bankTypeId" name="input-bank-type-id" value="<?=$playerBankDetail->bankTypeId?>" />

<div class="fmd-step1">
    <ul id="allBankList" class="bank-list bank-list-main">
        <?php if(!empty($preferredBankTypeList)) : ?>
            <?php foreach($preferredBankTypeList as $bankType) : ?>
                <?php if($bankType->payment_type_flag == Financial_account_setting::PAYMENT_TYPE_FLAG_BANK): ?>
                    <li data-bank-name="<?=lang($bankType->bankName)?>" data-bank-type-id="<?=$bankType->bankTypeId?>" class="<?=($bankType->bankTypeId == $playerBankDetail->bankTypeId) ? 'active': ''?>">
                        <a href="#<?=$bankType->bankTypeId?>" data-toggle="tab">
                            <i class="fa fa-check-circle" aria-hidden="true"></i>
                            <?=Banktype::renderBankEntry($bankType->bankTypeId, lang($bankType->bankName), $bankType->bankIcon)?>
                        </a>
                    </li>
                <?php endif;?>
            <?php endforeach ?>
        <?php endif ?>
    </ul>

    <div class="show-btn" onclick="expandBankList();"><i class="fa fa-angle-down"></i> <?= lang('show Bank list') ?></div>
    <div class="hide-btn" style="display: none;" onclick="shrinkBankList();"><i class="fa fa-angle-up"></i> <?= lang('hide Bank list') ?></div>

    <hr />
    <div class="selected-bank">
        <span><?=lang('Your current selected bank')?></span>
        <div class="bank-entry active d-inline">
            <span class="b-icon">
                &nbsp;<?=lang('No bank selected'); ?>
            </span>
        </div>
    </div>
    <div class="clearfix"></div>

    <hr />
</div>

<div id="fields" class="row">
    <!-- Account -->
    <div class="col-md-6 top-buffer form-group has-feedback has-input-tip">
        <label for="inputAccNum" class="control-label"><span><?= lang("financial_account.bankaccount") ?>:</span></label>
        <div class="input-group">
            <?php if($account_validator['only_allow_numeric']):?>
                <input type="number" class="form-control remove_number_spinner" name="input-acct-num" id="inputAccNum" value="<?=$playerBankDetail->bankAccountNumber?>"
                    <?=validator_rule_builder('bankAccountNumber', lang("financial_account.bankaccount"), $account_validator['bankAccountNumber'], 'html')?>
                />
            <?php else: ?>
                <input type="text" class="form-control" name="input-acct-num" id="inputAccNum" value="<?=$playerBankDetail->bankAccountNumber?>"
                    <?=validator_rule_builder('bankAccountNumber', lang("financial_account.bankaccount"), $account_validator['bankAccountNumber'], 'html')?>
                />
            <?php endif;?>
            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
        </div>
        <?=validator_input_tip_builder('bankAccountNumber', lang("financial_account.bankaccount"), $account_validator['bankAccountNumber'])?>
    </div>

    <!-- Name -->
    <?php if(in_array(Financial_account_setting::FIELD_NAME, $account_validator['field_show'])) :?>
        <div class="col-md-6 top-buffer form-group has-feedback has-input-tip">
            <label for="inputAccName" class="control-label"><span><?= lang("financial_account.name") ?>:</span></label>
            <div class="input-group">
                <input type="text" class="form-control" name="input-acct-name" id="inputAccName" value="<?=$playerBankDetail->bankAccountFullName?>"
                    <?=validator_rule_builder('bankAccountFullName', lang("financial_account.name"), $account_validator['bankAccountFullName'], 'html')?>
                    <?=($account_validator['allow_modify_name']) ? '' : 'readonly'?>
                    <?=(in_array(Financial_account_setting::FIELD_NAME, $account_validator['field_required'])) ? 'required' : ''?>
                />
            </div>
            <?=validator_input_tip_builder('bankAccountFullName', lang("financial_account.name"), $account_validator['bankAccountFullName'])?>
        </div>
    <?php endif;?>

    <!-- Area/Province -->
    <?php if(in_array(Financial_account_setting::FIELD_BANK_AREA, $account_validator['field_show'])) :?>
        <div class="col-md-6 top-buffer form-group has-feedback has-input-tip">
            <label for="inputProvince" class="control-label"><span><?= lang("financial_account.province") ?>:</span></label>
            <div class="input-group"></div>
            <select class="form-control province" name="input-province" id="inputProvince" data-value="<?=$playerBankDetail->province?>" <?=(in_array(Financial_account_setting::FIELD_BANK_AREA, $account_validator['field_required'])) ? 'required' : ''?>>
                <option value=""><?=lang('please_select')?></option>
            </select>
            <?=validator_input_tip_builder('province', lang("financial_account.province"), $account_validator['area'])?>
        </div>
    <?php endif;?>

    <!-- Area/City -->
    <?php if(in_array(Financial_account_setting::FIELD_BANK_AREA, $account_validator['field_show'])) :?>
        <div class="col-md-6 top-buffer form-group has-feedback has-input-tip">
            <label for="inputCity" class="control-label"><span><?= lang("financial_account.city") ?>:</span></label>
            <div class="input-group"></div>
            <select class="form-control city" name="input-city" id="inputCity" data-value="<?=$playerBankDetail->city?>" <?=(in_array(Financial_account_setting::FIELD_BANK_AREA, $account_validator['field_required'])) ? 'required' : ''?>>
                <option value=""><?=lang('please_select')?></option>
            </select>
            <?=validator_input_tip_builder('city', lang("financial_account.city"), $account_validator['area'])?>
        </div>
    <?php endif;?>

    <!-- Branch -->
    <?php if(in_array(Financial_account_setting::FIELD_BANK_BRANCH, $account_validator['field_show'])) :?>
        <div class="col-md-6 top-buffer form-group has-feedback has-input-tip">
            <label for="inputBranch" class="control-label"><span><?= lang("financial_account.branch") ?>:</span></label>
            <div class="input-group">
                <input type="text" class="form-control" name="input-branch" id="inputBranch" value="<?=$playerBankDetail->branch?>"
                    <?=validator_rule_builder('branch', lang("financial_account.branch"), $account_validator['branch'], 'html')?>
                    <?=(in_array(Financial_account_setting::FIELD_BANK_BRANCH, $account_validator['field_required'])) ? 'required' : ''?>
                />
            </div>
            <?=validator_input_tip_builder('branch', lang("financial_account.branch"), $account_validator['branch'])?>
        </div>
    <?php endif;?>

    <!-- Phone -->
    <?php if(in_array(Financial_account_setting::FIELD_PHONE, $account_validator['field_show'])) :?>
        <div class="col-md-6 top-buffer form-group has-feedback has-input-tip">
            <label for="inputMobileNum" class="control-label"><span><?= lang("financial_account.phone") ?>:</span></label>
            <div class="input-group">
                <input type="number" class="form-control remove_number_spinner" name="input-mobile-num" id="inputMobileNum" value="<?=$playerBankDetail->phone?>"
                    <?=validator_rule_builder('phone', lang("financial_account.phone"), $account_validator['phone'], 'html')?>
                    <?=(in_array(Financial_account_setting::FIELD_PHONE, $account_validator['field_required'])) ? 'required' : ''?>
                />
            </div>
            <?=validator_input_tip_builder('phone', lang("financial_account.phone"), $account_validator['phone'])?>
        </div>
    <?php endif;?>

    <!-- Bank Address -->
    <?php if(in_array(Financial_account_setting::FIELD_BANK_ADDRESS, $account_validator['field_show'])) :?>
        <div class="col-md-12 top-buffer form-group has-feedback has-input-tip">
            <label for="inputAddress" class="control-label"><span><?= lang("financial_account.address") ?>:</span></label>
            <div class="input-group">
                <input type="text" class="form-control" name="input-address" id="inputAddress" value="<?=$playerBankDetail->bankAddress?>"
                    <?=validator_rule_builder('bankAddress', lang("financial_account.address"), $account_validator['bankAddress'], 'html')?>
                    <?=(in_array(Financial_account_setting::FIELD_BANK_ADDRESS, $account_validator['field_required'])) ? 'required' : ''?>
                />
            </div>
            <?=validator_input_tip_builder('bankAddress', lang("financial_account.address"), $account_validator['bankAddress'])?>
        </div>
    <?php endif;?>
</div>
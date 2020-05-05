<style>
    .deposit_bank_group {
        border: 1px #e8e8e8 solid;
        padding: 10px;
        margin: 15px;
    }
    .right_group {
        padding: 10px;
        margin: 15px;
    }
    .mode_cell {
        padding-bottom: 20px;
        min-height: 68px;
    }
    .mode_count {
        float: left;
        padding-right: 5px;
    }
    .mode_description {
        float: left;
    }
</style>

<?php
    $is_enable_deposit_bank = $financial_account_enable_deposit_bank['value'];
    $mode = $bank_number_validator_mode['value'];
?>

<form action="<?='/payment_management/saveFinancialAccountSettingOthers'?>" method="POST">
    <div class="well well-sm" style="margin-bottom: 0;">
        <ul class="nav nav-pills">
            <li><div class="sub_title"><?=lang('financial_account.validator_mode')?></div></li>
        </ul>
    </div>

    <div class="tab-content" style="margin-bottom: 0;">
        <div role="tabpanel" style="padding-top: 0; padding-bottom: 0;">
            <div class="row">
                <div class="col-md-6">
                    <div class="radio">
                        <label class="mode_cell">
                            <input type="radio" name="bank_number_validator_mode" value="only_allow_duplicate_one_player" <?=($mode == "only_allow_duplicate_one_player") ? ' checked="checked"' : ''?>>
                            <div class="mode_count">1.</div>
                            <div class="mode_description"><?=lang('financial_account.bank_number_validator_mode.only_allow_duplicate_one_player')?></div>
                        </label>
                    </div>
                    <div class="radio">
                        <label class="mode_cell">
                            <input type="radio" name="bank_number_validator_mode" value="only_allow_duplicate_one_player_any" <?=($mode == "only_allow_duplicate_one_player_any") ? ' checked="checked"' : ''?>>
                            <div class="mode_count">2.</div>
                            <div class="mode_description"><?=lang('financial_account.bank_number_validator_mode.only_allow_duplicate_one_player_any')?></div>
                        </label>
                    </div>
                    <div class="radio">
                        <label class="mode_cell">
                            <input type="radio" name="bank_number_validator_mode" value="not_allow_duplicate_number" <?=($mode == "not_allow_duplicate_number") ? ' checked="checked"' : ''?>>
                            <div class="mode_count">3.</div>
                            <div class="mode_description"><?=lang('financial_account.bank_number_validator_mode.not_allow_duplicate_number')?></div>
                        </label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="radio">
                        <label class="mode_cell">
                            <input type="radio" name="bank_number_validator_mode" value="not_allow_duplicate_number_on_same_banktype" <?=($mode == "not_allow_duplicate_number_on_same_banktype") ? ' checked="checked"' : ''?>>
                            <div class="mode_count">4.</div>
                            <div class="mode_description"><?=lang('financial_account.bank_number_validator_mode.not_allow_duplicate_number_on_same_banktype')?></div>
                        </label>
                    </div>
                    <div class="radio">
                        <label class="mode_cell">
                            <input type="radio" name="bank_number_validator_mode" value="not_allow_duplicate_number_on_same_banktype_any" <?=($mode == "not_allow_duplicate_number_on_same_banktype_any") ? ' checked="checked"' : ''?>>
                            <div class="mode_count">5.</div>
                            <div class="mode_description"><?=lang('financial_account.bank_number_validator_mode.not_allow_duplicate_number_on_same_banktype_any')?></div>
                        </label>
                    </div>
                    <div class="radio">
                        <label class="mode_cell">
                            <input type="radio" name="bank_number_validator_mode" value="allow_any_duplicate_number" <?=($mode == "allow_any_duplicate_number") ? ' checked="checked"' : ''?>>
                            <div class="mode_count">6.</div>
                            <div class="mode_description"><?=lang('financial_account.bank_number_validator_mode.allow_any_duplicate_number')?></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="well well-sm" style="margin-bottom: 0;">
        <ul class="nav nav-pills">
            <li><div class="sub_title"><?=lang('financial_account.advance_setting')?></div></li>
        </ul>
    </div>
    <div class="tab-content" style="margin-bottom: 0;">
        <div role="tabpanel" style="padding-top: 0; padding-bottom: 0;">
            <div class="row">
                <div class="col-md-6">
                    <div class="deposit_bank_group">
                        <label for="enable_deposit_bank">
                            <input type="hidden" name="financial_account_enable_deposit_bank" value="0">
                            <input class="checkbox_align" type="checkbox" name="financial_account_enable_deposit_bank" id="enable_deposit_bank" <?=($financial_account_enable_deposit_bank['value']) ? 'checked' : ''?> value="1" onchange="toggleDepositBank();">
                            1. <?=lang('financial_account.enable_deposit_bank')?>
                        </label>
                        <ul>
                            <label for="require_deposit_bank_account">
                                <input type="hidden" name="financial_account_require_deposit_bank_account" value="0">
                                <input class="checkbox_align deposit_bank" type="checkbox" name="financial_account_require_deposit_bank_account" id="require_deposit_bank_account" <?=($financial_account_require_deposit_bank_account['value']) ? 'checked' : ''?> <?=($is_enable_deposit_bank) ? '' : 'disabled'?> value="1">
                                <?=lang('financial_account.require_deposit_bank_account')?>
                            </label>
                        </ul>
                        <ul>
                            <div>
                                <label for="deposit_account_limit">
                                    <input type="hidden" name="financial_account_deposit_account_limit" value="0">
                                    <input class="checkbox_align deposit_bank" type="checkbox" name="financial_account_deposit_account_limit" id="deposit_account_limit" <?=($financial_account_deposit_account_limit['value']) ? 'checked' : ''?> <?=($is_enable_deposit_bank) ? '' : 'disabled'?> onchange="toggleAccountLimit('deposit');" value="1">
                                    <?=lang('financial_account.deposit_account_limit')?>
                                </label>
                            </div>
                            <div style="padding-left:22px;">
                                <label for="max_deposit_account_number">
                                    <?=lang('financial_account.number')?>
                                    <input class="deposit_bank" type="number" min="1" name="financial_account_max_deposit_account_number" id="max_deposit_account_number" value="<?=($financial_account_max_deposit_account_number['value']) ? $financial_account_max_deposit_account_number['value'] : ''?>" <?=($financial_account_deposit_account_limit['value'] && $is_enable_deposit_bank) ? 'required="required"' : 'disabled'?>>
                                </label>
                            </div>
                        </ul>
                        <ul>
                            <label for="can_be_withdraw_and_deposit">
                                <input type="hidden" name="financial_account_can_be_withdraw_and_deposit" value="0">
                                <input class="checkbox_align deposit_bank" type="checkbox" name="financial_account_can_be_withdraw_and_deposit" id="can_be_withdraw_and_deposit" <?=($financial_account_can_be_withdraw_and_deposit['value']) ? 'checked' : ''?> <?=($is_enable_deposit_bank) ? '' : 'disabled'?> value="1">
                                <?=lang('financial_account.can_be_withdraw_and_deposit')?>
                            </label>
                        </ul>
                        <ul>
                            <label for="deposit_account_default_unverified">
                                <input type="hidden" name="financial_account_deposit_account_default_unverified" value="0">
                                <input class="checkbox_align deposit_bank" type="checkbox" name="financial_account_deposit_account_default_unverified" id="deposit_account_default_unverified" <?=($financial_account_deposit_account_default_unverified['value']) ? 'checked' : ''?> value="1">
                                <?=lang('financial_account.deposit_account_default_unverified')?>
                            </label>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="right_group">
                        <ul>
                            <div>
                                <label for="withdraw_account_limit">
                                    <input type="hidden" name="financial_account_withdraw_account_limit" value="0">
                                    <input class="checkbox_align" type="checkbox" name="financial_account_withdraw_account_limit" id="withdraw_account_limit" <?=($financial_account_withdraw_account_limit['value']) ? 'checked' : ''?> onchange="toggleAccountLimit('withdraw');" value="1">
                                    2. <?=lang('financial_account.withdraw_account_limit')?>
                                </label>
                            </div>
                            <div style="padding-left:35px;">
                                <label for="max_withdraw_account_number">
                                    <?=lang('financial_account.number')?>
                                    <input type="number" min="1" name="financial_account_max_withdraw_account_number" id="max_withdraw_account_number" value="<?=($financial_account_max_withdraw_account_number['value']) ? $financial_account_max_withdraw_account_number['value'] : ''?>" <?=($financial_account_withdraw_account_limit['value']) ? 'required="required"' : 'disabled'?>>
                                </label>
                            </div>
                        </ul>
                        <!-- in case client require this function
                        <ul>
                            <label for="allow_edit">
                                <input type="hidden" name="financial_account_allow_edit" value="0">
                                <input class="checkbox_align" type="checkbox" name="financial_account_allow_edit" id="allow_edit" <?=($financial_account_allow_edit['value']) ? 'checked' : ''?> value="1">
                                3. <?=lang('financial_account.allow_edit')?>
                            </label>
                        </ul>
                        -->
                        <ul>
                            <label for="one_account_per_institution">
                                <input type="hidden" name="financial_account_one_account_per_institution" value="0">
                                <input class="checkbox_align" type="checkbox" name="financial_account_one_account_per_institution" id="one_account_per_institution" <?=($financial_account_one_account_per_institution['value']) ? 'checked' : ''?> value="1">
                                3. <?=lang('financial_account.one_account_per_institution')?>
                            </label>
                        </ul>
                        <ul>
                            <label for="allow_delete">
                                <input type="hidden" name="financial_account_allow_delete" value="0">
                                <input class="checkbox_align" type="checkbox" name="financial_account_allow_delete" id="allow_delete" <?=($financial_account_allow_delete['value']) ? 'checked' : ''?> value="1">
                                4. <?=lang('financial_account.allow_delete')?>
                            </label>
                        </ul>
                        <ul>
                            <label for="withdraw_account_default_unverified">
                                <input type="hidden" name="financial_account_withdraw_account_default_unverified" value="0">
                                <input class="checkbox_align" type="checkbox" name="financial_account_withdraw_account_default_unverified" id="withdraw_account_default_unverified" <?=($financial_account_withdraw_account_default_unverified['value']) ? 'checked' : ''?> value="1">
                                5. <?=lang('financial_account.withdraw_account_default_unverified')?>
                            </label>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="text-center">
                    <button type="submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>"><?=lang('Save')?></button>
                </div>
            </div>
        </div>
    </div>
</form>
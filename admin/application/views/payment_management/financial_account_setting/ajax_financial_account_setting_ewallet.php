<div class="well well-sm" style="margin-bottom: 0;">
    <ul class="nav nav-pills">
        <li><div class="sub_title"><?=lang('financial_account.basic_setting')?></div></li>
    </ul>
</div>
<div class="tab-content" style="margin-bottom: 0;">
    <div role="tabpanel" style="padding-top: 0; padding-bottom: 0;">
        <form class="financial_account_setting_form" action="<?='/payment_management/saveFinancialAccountSetting/2'?>" method="POST">
            <table class="table table-hover table-bordered" style="width: 100%; float: left;">
                <tbody>
                    <tr>
                        <td style="vertical-align:middle;"><?=lang('financial_account.account')?></td>
                        <td colspan="2">
                            <div style="margin-bottom: 5px;">
                                <label for="length_min"><?=lang('financial_account.length_min')?></label>
                                <input type="number" min="1" name="length_min" id="length_min" value="<?=$account_number_min_length?>">
                                <label for="length_max"><?=lang('financial_account.length_max')?></label>
                                <input type="number" min="1" name="length_max" id="length_max" value="<?=$account_number_max_length?>">
                            </div>
                            <div>
                                <label for="number_only">
                                    <input type="hidden" name="number_only" value="0">
                                    <input class="checkbox_align" id="number_only" type="checkbox" name="number_only" <?=($account_number_only_allow_numeric) ? 'checked' : ''?> value="1">
                                    <?=lang('financial_account.number_only')?>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:middle;"><?=lang('financial_account.name')?></td>
                        <td colspan="2">
                            <input type="hidden" name="field_show[]" value="<?=Financial_account_setting::FIELD_NAME?>">
                            <input type="hidden" name="field_required[]" value="<?=Financial_account_setting::FIELD_NAME?>">
                            <label for="name_edit" style="margin-top: 5px;">
                                <input type="hidden" name="name_edit" value="0">
                                <input class="checkbox_align" id="name_edit" type="checkbox" name="name_edit" <?=($account_name_allow_modify_by_players) ? 'checked' : ''?> value="1">
                                <?=lang('financial_account.name_edit')?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td width=20% style="vertical-align:middle;"><?=lang('financial_account.phone')?></td>
                        <td width=40%>
                            <input class="switch_checkbox" type="checkbox" name="field_show[]" value="<?=Financial_account_setting::FIELD_PHONE?>" id="phone_visible" <?=(in_array(Financial_account_setting::FIELD_PHONE, $field_show)) ? 'checked' : ''?> onchange="toggleFieldVisibility('phone');" data-on-text="<?=lang('lang.show')?>" data-off-text="<?=lang('lang.hide')?>">
                        </td>
                        <td width=40%>
                            <input class="switch_checkbox" type="checkbox" name="field_required[]" value="<?=Financial_account_setting::FIELD_PHONE?>" id="phone_required" <?=(in_array(Financial_account_setting::FIELD_PHONE, $field_required)) ? 'checked' : ''?> <?=(in_array(Financial_account_setting::FIELD_PHONE, $field_show)) ? '' : 'disabled'?> data-on-text="<?=lang('Required')?>" data-off-text="<?=lang('Unrequired')?>">
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="text-center">
                <a class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>" onclick="checkMinMaxValue();"><?=lang('Save')?></a>
            </div>
        </form>
    </div>
</div>
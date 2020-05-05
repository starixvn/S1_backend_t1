<style type="text/css">
    .onoffswitch {
        position: relative;
        width: 120px;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
    }

    td .onoffswitch {
        margin: 0 auto;
    }

    .control-label {
        font-size: inherit;
    }

    .onoffswitch-checkbox {
        display: none;
    }

    .onoffswitch-label {
        display: block;
        overflow: hidden;
        cursor: pointer;
        border: 1px solid #999999;
        border-radius: 20px;
    }

    .onoffswitch-inner {
        display: block;
        width: 200%;
        margin-left: -100%;
        -moz-transition: margin 0.3s ease-in 0s;
        -webkit-transition: margin 0.3s ease-in 0s;
        -o-transition: margin 0.3s ease-in 0s;
        transition: margin 0.3s ease-in 0s;
    }

    .onoffswitch-inner:before,
    .onoffswitch-inner:after {
        display: block;
        float: left;
        width: 50%;
        height: 20px;
        padding: 0;
        line-height: 20px;
        font-size: 10px;
        color: white;
        font-family: Trebuchet, Arial, sans-serif;
        font-weight: bold;
        -moz-box-sizing: border-box;
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
    }

    .onoffswitch-inner:before {
        content: "<?= lang('ON') ?>";
        padding-left: 10px;
        background-color: #43ac6a;
        color: #FFFFFF;
    }

    .onoffswitch-inner:after {
        content: "<?= lang('OFF') ?>";
        padding-right: 10px;
        background-color: #EEEEEE;
        color: #999999;
        text-align: right;
    }

    .onoffswitch-default:after {
        content: "<?= lang('DEFAULT') ?>";
        padding-right: 10px;
        background-color: #EEEEEE;
        color: #999999;
        text-align: right;
    }

    .visible .onoffswitch-inner:before {
        content: "<?= lang('SHOW') ?>";
        padding-left: 10px;
        background-color: #43ac6a;
        color: #FFFFFF;
    }

    .visible .onoffswitch-inner:after {
        content: "<?= lang('HIDE') ?>";
        padding-right: 10px;
        background-color: #EEEEEE;
        color: #999999;
        text-align: right;
    }

    .required .onoffswitch-inner:before {
        content: "<?= lang('Required') ?>";
        padding-left: 10px;
        background-color: #43ac6a;
        color: #FFFFFF;
    }

    .required .onoffswitch-inner:after {
        content: "<?= lang('Unrequired') ?>";
        padding-right: 10px;
        background-color: #EEEEEE;
        color: #999999;
        text-align: right;
    }

    .edit .onoffswitch-inner:before {
        content: "<?= lang('Enable') ?>";
        padding-left: 10px;
        background-color: #43ac6a;
        color: #FFFFFF;
    }

    .edit .onoffswitch-inner:after {
        content: "<?= lang('Disable') ?>";
        padding-right: 10px;
        background-color: #EEEEEE;
        color: #999999;
        text-align: right;
    }

    .onoffswitch-switch {
        display: block;
        width: 18px;
        margin: 6px;
        background: #FFFFFF;
        border: 1px solid #999999;
        border-radius: 20px;
        position: absolute;
        top: 0;
        bottom: 0;
        right: 90px;
        -moz-transition: all 0.3s ease-in 0s;
        -webkit-transition: all 0.3s ease-in 0s;
        -o-transition: all 0.3s ease-in 0s;
        transition: all 0.3s ease-in 0s;
    }

    .onoffswitch-checkbox:checked+.onoffswitch-label .onoffswitch-inner {
        margin-left: 0;
    }

    .onoffswitch-checkbox:checked+.onoffswitch-label .onoffswitch-switch {
        right: 0px;
    }

    .onoffswitch-checkbox:disabled+.onoffswitch-label {
        background-color: #ffffff;
        cursor: not-allowed;
    }

    .note-tooltip+.tooltip .tooltip-inner {
        max-width: 400px;
    }
</style>

<div class="well well-sm" style="margin-bottom: 0;">
    <ul id="player-registration-settings" class="nav nav-pills">
        <li role="presentation" <?= $subtype == 'registration' ? 'class="active"' : '' ?>><a href="#registration" onclick="$(this).tab('show'); return false;"><?= lang('Registration') ?></a></li>
        <li role="presentation" <?= $subtype == 'account' ? 'class="active"' : '' ?>><a href="#account" onclick="$(this).tab('show'); return false;"><?= lang('Account Information') ?></a></li>
        <li role="presentation" <?= $subtype == 'login' ? 'class="active"' : '' ?>><a href="#login" onclick="$(this).tab('show'); return false;"><?= lang('Login') ?></a></li>
    </ul>
    <?php if ($this->utils->isEnabledMDB()) : ?>
        <a href="<?= site_url('/system_management/sync_player_reg_setting_to_mdb') ?>" class="btn btn-success btn-sm pull-right" style="margin-top: -40px;">
            <i class="fa fa-refresh"></i> <?= lang('Sync To Currency') ?>
        </a>
    <?php endif; ?>
</div>
<div class="tab-content" style="margin-bottom: 0;">
    <div role="tabpanel" class="tab-pane fade<?= $subtype == 'registration' ? 'in active' : '' ?>" id="registration" style="padding-top: 0; padding-bottom: 0;">
        <div style="margin-bottom: 20px">
            <?php if ($registration_captcha_enabled) : ?>
                <a href="<?= site_url('marketing_management/switchCaptcha/registration/0'); ?>" class="btn btn-portage">
                    <i class="fa fa-check-square-o fa-fw"></i> <?= lang('sys.captchaIsOn'); ?>
                </a>
            <?php else : ?>
                <a href="<?= site_url('marketing_management/switchCaptcha/registration/1'); ?>" class="btn btn-portage">
                    <i class="fa fa-square-o fa-fw"></i> <?= lang('sys.captchaIsOff'); ?>
                </a>
            <?php endif ?>

            <a href="/user_management/viewLogs" target="_blank" class="btn btn-scooter">
                <i class="fa fa-align-justify fa-fw"></i> <?= lang('View Log History'); ?>
            </a>
        </div>
        <form id="updateRegistrationSettingsForm" action="/marketing_management/saveRegistrationSettings/1" method="POST" onsubmit="return validatePlayerRegForm()">
            <table class="table table-hover table-bordered" style="width: 100%; float: left;">
                <thead>
                    <tr>
                        <th style="text-align: left; width: 50%;"><?= lang('mark.fields'); ?></th>
                        <th style="text-align: center; width: 25%;">
                            <?= lang('Show / Hide'); ?>
                        </th>
                        <th style="text-align: center; width: 25%;">
                            <?= lang('Required'); ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= lang('User Name') ?></td>
                        <td>
                            <div class="visible onoffswitch">
                                <input type="checkbox" class="onoffswitch-checkbox" checked disabled>
                                <label class="onoffswitch-label">
                                    <span class="onoffswitch-inner"></span>
                                    <span class="onoffswitch-switch"></span>
                                </label>
                            </div>
                        </td>
                        <td>
                            <div class="required onoffswitch">
                                <input type="checkbox" class="onoffswitch-checkbox" checked disabled>
                                <label class="onoffswitch-label">
                                    <span class="onoffswitch-inner"></span>
                                    <span class="onoffswitch-switch"></span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Password') ?></td>
                        <td>
                            <div class="visible onoffswitch">
                                <input type="checkbox" class="onoffswitch-checkbox" checked disabled>
                                <label class="onoffswitch-label">
                                    <span class="onoffswitch-inner"></span>
                                    <span class="onoffswitch-switch"></span>
                                </label>
                            </div>
                        </td>
                        <td>
                            <div class="required onoffswitch">
                                <input type="checkbox" class="onoffswitch-checkbox" checked disabled>
                                <label class="onoffswitch-label">
                                    <span class="onoffswitch-inner"></span>
                                    <span class="onoffswitch-switch"></span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <?php
                    if ($this->utils->isEnabledFeature('close_aff_and_agent')) {
                        $fields_to_hide = ['affiliateCode', 'agent_tracking_code', 'Affiliate Code', 'Agency Code'];

                        foreach ($registration_fields as $key => $value) {
                            if (in_array($value['field_name'], $fields_to_hide)) {
                                unset($registration_fields[$key]);
                            }
                            if (in_array($value['alias'], $fields_to_hide)) {
                                unset($registration_fields[$key]);
                            }
                        }
                    }

                    $excludedInRegistrationSettings = $this->utils->getConfig('excluded_in_registration_settings');
                    $feature_show_player_upload_realname_verification = $this->utils->isEnabledFeature('show_player_upload_realname_verification');

                    foreach ($registration_fields as $key => $value) {
                        if (in_array($value['alias'], $excludedInRegistrationSettings)) {
                            continue;
                        }

                        if ($value['registrationFieldId'] == 49 && !$feature_show_player_upload_realname_verification) {
                            continue;
                        }

                        if (!$this->utils->isEnabledFeature('enable_communication_preferences') || empty($this->utils->getConfig('communication_preferences'))) {
                            if (in_array($value['registrationFieldId'], [54, 55, 56, 57, 58])) {
                                continue;
                            }
                        }
                        $field = strtolower(str_replace(' ', '_', $value['field_name']));
                        $fieldName = $this->config->item($value['field_name'], 'cust_non_lang_translation');

                        $address_toggle = '';
                        if(in_array($value['alias'], $multi_rows_address_columns)){
                            $address_toggle = ($full_address_in_one_row) ? 'style="display:none;" class="registration_address_toggle"' : 'class="registration_address_toggle"';
                        }
                        # The checking of withdraw_verification == withdrawal_password is removed so that withdrawal_password is shown in the configurable list of options
                    ?>
                        <?php if ($value['alias'] == 'region'): ?>
                            <tr>
                                <td style="text-align: left;">
                                    <?= lang('Full Address in One Row') ?>
                                </td>
                                <td>
                                    <div class="onoffswitch">
                                        <input type="hidden" name="full_address_in_one_row" value="0">
                                        <input type="checkbox" name="full_address_in_one_row" class="onoffswitch-checkbox" id="full_address_in_one_row_registration" <?= $full_address_in_one_row ? 'checked' : '' ?> onchange="fullAddressToggle('registration')">
                                        <label class="onoffswitch-label" for="full_address_in_one_row_registration">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td style="text-align: left;">
                                    <?=lang('a_reg.24')?>
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        <?php endif;?>

                        <tr <?=$address_toggle;?> >
                            <td style="text-align: left;">
                                <?php
                                switch ($value['registrationFieldId']) {
                                    case '9':
                                        echo lang('Instant Message 1');
                                        break;
                                    case '10':
                                        echo lang('Instant Message 2');
                                        break;
                                    case '47':
                                        echo lang('Instant Message 3');
                                        break;
                                    default:
                                        echo ($fieldName) ? $fieldName : lang('a_reg.' . $value['registrationFieldId']);
                                        break;
                                }
                                ?>
                            </td>
                            <td>
                                <div class="visible onoffswitch">
                                    <input type="checkbox" name="<?= $value['registrationFieldId'] . '_visible'; ?>" class="onoffswitch-checkbox" id="<?= $value['registrationFieldId'] . '_visible'; ?>" <?= ($value['visible'] == 0) ? 'checked' : '' ?> onchange="toggleFieldVisibility('<?= $value['registrationFieldId'] ?>');" data-registration-field-id="<?= $value['registrationFieldId'] ?>" />
                                    <label class="onoffswitch-label" for="<?= $value['registrationFieldId'] . '_visible'; ?>">
                                        <span class="onoffswitch-inner"></span>
                                        <span class="onoffswitch-switch"></span>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <?php
                                //hide player preference's requires btn
                                if (in_array($value['registrationFieldId'], [54, 55, 56, 57, 58])) {
                                    continue;
                                }
                                ?>
                                <div class="required onoffswitch">
                                    <input type="checkbox" name="<?= $value['registrationFieldId'] . '_required'; ?>" class="onoffswitch-checkbox" id="<?= $value['registrationFieldId'] . '_required'; ?>" <?= ($value['required'] == 0) ? 'checked' : '' ?> <?= ($this->marketing_manager->checkRegisteredFieldsIfVisible($value['field_name'], 1) == 0) ? '' : 'disabled' ?> data-registration-field-id="<?= $value['registrationFieldId'] ?>" />
                                    <label class="onoffswitch-label" for="<?= $value['registrationFieldId'] . '_required'; ?>">
                                        <span class="onoffswitch-inner"></span>
                                        <span class="onoffswitch-switch"></span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <div class="pull-left" style="margin-right: 20px;">
                <?= lang('After Registration Login automatically') ?>
            </div>
            <div class="pull-left">
                <div class="onoffswitch">
                    <input type="hidden" name="login_after_registration_enabled" value="0">
                    <input type="checkbox" name="login_after_registration_enabled" class="onoffswitch-checkbox" id="registration-login" value="1" <?= $login_after_registration_enabled ? 'checked' : '' ?>>
                    <label class="onoffswitch-label" for="registration-login">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                    </label>
                </div>
            </div>

            <div class="clearfix"></div>
            <br>
            <div class="pull-left" style="margin-right: 20px;">
                <?= lang('Restrict Username') ?>
            </div>
            <div class="pull-left">
                <div class="onoffswitch">
                    <input type="hidden" name="restrict_username" value="0">
                    <input type="checkbox" name="restrict_username" class="onoffswitch-checkbox" id="restrict-username" value="1" <?= $restrict_username_enabled ? 'checked' : '' ?>>
                    <label class="onoffswitch-label" for="restrict-username">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                    </label>
                </div>
            </div>

            <div class="clearfix"></div>
            <br>
            <div class="pull-left" style="margin-right: 20px;">
                <?= lang('Set Min/Max Password') ?>
            </div>
            <div class="pull-left">
                <div class="onoffswitch">
                    <input type="hidden" name="set_min_max_password" value="0">
                    <input type="checkbox" name="set_min_max_password" class="onoffswitch-checkbox" id="set_min_max_password" <?= $password_min_max_enabled ? 'checked' : '' ?> onchange="setMinMaxPassword('<?= $min_password; ?>','<?= $max_password; ?>')">
                    <label class="onoffswitch-label" for="set_min_max_password">
                        <span class="onoffswitch-inner onoffswitch-default"></span>
                        <span class="onoffswitch-switch"></span>
                    </label>
                </div>
            </div>
            <div class="clearfix"></div>
            <br>
            <div class="set_min_max_password">
                <?php if ($password_min_max_enabled) { ?>
                    <div class="pull-left" style="margin-right: 20px;">
                        <?= lang('Minimum Password') ?>
                    </div>
                    <div class="pull-left">
                        <input type="number" id="min_password" name="min_password" style="width: 80px;" min="6" max="20" value="<?= $min_password; ?>" onkeydown="return false">
                    </div>
                    <div class="clearfix"></div>
                    <br>
                    <div class="pull-left" style="margin-right: 20px;">
                        <?= lang('Maximum Password') ?>
                    </div>
                    <div class="pull-left">
                        <input type="number" id="max_password" name="max_password" style="width: 80px;" min="6" max="20" value="<?= $max_password; ?>" onkeydown="return false">
                    </div>
                <?php  } ?>
            </div>

            <?php if ($this->utils->isEnabledFeature('enable_pep_gbg_api_authentication') && $this->utils->isEnabledFeature('show_pep_authentication')) : ?>
                <div class="clearfix"></div>
                <br>
                <div class="pull-left" style="margin-right: 20px;">
                    <?= lang('Generate PEP GBG Auth API After Registration') ?>
                </div>
                <div class="pull-left">
                    <div class="onoffswitch">
                        <input type="hidden" name="generate_pep_gbg_auth_after_registration_enabled" value="0">
                        <input type="checkbox" name="generate_pep_gbg_auth_after_registration_enabled" class="onoffswitch-checkbox" id="generate_pep" value="1" <?= $generate_pep_gbg_auth_after_registration_enabled ? 'checked' : '' ?>>
                        <label class="onoffswitch-label" for="generate_pep">
                            <span class="onoffswitch-inner"></span>
                            <span class="onoffswitch-switch"></span>
                        </label>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($this->utils->isEnabledFeature('enable_c6_acuris_api_authentication') && $this->utils->isEnabledFeature('show_c6_authentication')) : ?>
                <div class="clearfix"></div>
                <br>
                <div class="pull-left" style="margin-right: 20px;">
                    <?= lang('Generate C6 Auth API After Registration') ?>
                </div>
                <div class="pull-left">
                    <div class="onoffswitch">
                        <input type="hidden" name="generate_c6_acuris_auth_after_registration_enabled" value="0">
                        <input type="checkbox" name="generate_c6_acuris_auth_after_registration_enabled" class="onoffswitch-checkbox" id="generate_c6" value="1" <?= $generate_c6_acuris_auth_after_registration_enabled ? 'checked' : '' ?>>
                        <label class="onoffswitch-label" for="generate_c6">
                            <span class="onoffswitch-inner"></span>
                            <span class="onoffswitch-switch"></span>
                        </label>
                    </div>
                </div>
            <?php endif; ?>

            <div class="clearfix"></div>
            <hr />
            <div class="text-center">
                <input type="submit" class="btn btn-linkwater" value="<?= lang('Save'); ?>" />
                <input type="reset" class="btn btn-scooter" value="<?= lang('Reset'); ?>" />
            </div>
        </form>
    </div>
    <div role="tabpanel" class="tab-pane fade<?= $subtype == 'account' ? 'in active' : '' ?>" id="account" style="padding-top: 0; padding-bottom: 0;">
        <div style="margin-bottom: 20px">
            <a href="/user_management/viewLogs" target="_blank" class="btn btn-scooter">
                <i class="fa fa-align-justify fa-fw"></i> <?= lang('View Log History'); ?>
            </a>
        </div>
        <form id="updateAccountInformationForm" action="<?= BASEURL . 'marketing_management/saveAccountSettings' ?>" method="POST">
            <table class="table table-hover table-bordered" style="width: 100%; float: left;">
                <thead>
                    <tr>
                        <th style="text-align: left; width: 40%;"><?= lang('mark.fields'); ?></th>
                        <th style="text-align: center; width: 20%;">
                            <?= lang('Show / Hide'); ?>
                        </th>
                        <th style="text-align: center; width: 20%;">
                            <?= lang('Required'); ?>
                        </th>
                        <th style="text-align: center; width: 20%;">
                            <?= lang('Edit'); ?>
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $excludedInAccountSettings = $this->utils->getConfig('excluded_in_account_info_settings');

                    foreach ($registration_fields as $key => $value) {
                        if (in_array($value['alias'], $excludedInAccountSettings)) {
                            continue;
                        }

                        $field = strtolower(str_replace(' ', '_', $value['field_name']));
                        $fieldName = $this->config->item($value['field_name'], 'cust_non_lang_translation');
                        # The checking of withdraw_verification == withdrawal_password is removed so that withdrawal_password is shown in the configurable list of options

                        $address_toggle = '';
                        if(in_array($value['alias'], $multi_rows_address_columns)){
                            $address_toggle = ($full_address_in_one_row) ? 'style="display:none;" class="account_info_address_toggle"' : 'class="account_info_address_toggle"';
                        }
                    ?>
                        <?php if ($value['alias'] == 'region'): ?>
                            <tr>
                                <td style="text-align: left;">
                                    <?= lang('Full Address in One Row') ?>
                                </td>
                                <td>
                                    <div class="onoffswitch">
                                        <input type="hidden" name="full_address_in_one_row" value="0">
                                        <input type="checkbox" name="full_address_in_one_row" class="onoffswitch-checkbox" id="full_address_in_one_row_account_info" <?= $full_address_in_one_row ? 'checked' : '' ?> onchange="fullAddressToggle('account_info')">
                                        <label class="onoffswitch-label" for="full_address_in_one_row_account_info">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </td>
                                <td colspan="2"></td>
                            </tr>
                            <tr>
                                <td style="text-align: left;">
                                    <?=lang('a_reg.24')?>
                                </td>
                                <td colspan="3"></td>
                            </tr>
                        <?php endif;?>

                        <tr <?=$address_toggle;?> >
                            <td style="text-align: left;">
                                <?php
                                switch ($value['registrationFieldId']) {
                                    case '9':
                                        echo lang('Instant Message 1');
                                        break;
                                    case '10':
                                        echo lang('Instant Message 2');
                                        break;
                                    case '47':
                                        echo lang('Instant Message 3');
                                        break;
                                    default:
                                        echo ($fieldName) ? $fieldName : lang('a_reg.' . $value['registrationFieldId']);
                                        break;
                                }
                                ?>
                                <?php if (in_array($value['alias'], ['email', 'contactNumber'])) : ?>
                                    <i class="glyphicon glyphicon-exclamation-sign note-tooltip" data-toggle="tooltip" data-placement="right" title="<?= lang('players will unable to edit after verified') ?>"></i>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="visible onoffswitch">
                                    <input type="checkbox" name="<?= $value['registrationFieldId'] . '_visible'; ?>" class="onoffswitch-checkbox" id="<?= $value['registrationFieldId'] . '_account_visible'; ?>" <?= ($value['account_visible'] == 0) ? 'checked' : '' ?> onchange="toggleFieldVisibility('<?= $value['registrationFieldId'] ?>_account');" data-registration-field-id="<?= $value['registrationFieldId'] ?>">
                                    <label class="onoffswitch-label" for="<?= $value['registrationFieldId'] . '_account_visible'; ?>">
                                        <span class="onoffswitch-inner"></span>
                                        <span class="onoffswitch-switch"></span>
                                    </label>
                                </div>
                            </td>
                            <?php if (strpos($value['alias'], 'player_preference') !== false) : ?>
                                <td></td>
                                <td></td>
                                <?php continue; ?>
                            <?php endif; ?>
                            <td>
                                <div class="required onoffswitch">
                                    <input type="checkbox" name="<?= $value['registrationFieldId'] . '_required'; ?>" class="onoffswitch-checkbox" id="<?= $value['registrationFieldId'] . '_account_required'; ?>" <?= ($value['account_required'] == 0) ? 'checked' : '' ?> <?= ($value['account_visible'] == 0) ? '' : 'disabled' ?> data-registration-field-id="<?= $value['registrationFieldId'] ?>">
                                    <label class="onoffswitch-label" for="<?= $value['registrationFieldId'] . '_account_required'; ?>">
                                        <span class="onoffswitch-inner"></span>
                                        <span class="onoffswitch-switch"></span>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="edit onoffswitch">
                                    <input type="checkbox" name="<?= $value['registrationFieldId'] . '_edit'; ?>" class="onoffswitch-checkbox" id="<?= $value['registrationFieldId'] . '_account_edit'; ?>" <?= ($value['account_edit'] == 0) ? 'checked' : '' ?> <?= ($value['account_visible'] == 0) ? '' : 'disabled' ?> data-registration-field-id="<?= $value['registrationFieldId'] ?>">
                                    <label class="onoffswitch-label" for="<?= $value['registrationFieldId'] . '_account_edit'; ?>">
                                        <span class="onoffswitch-inner"></span>
                                        <span class="onoffswitch-switch"></span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <div class="clearfix"></div>
            <hr />
            <div class="text-center">
                <input type="submit" class="btn btn-linkwater" value="<?= lang('Save'); ?>" />
                <input type="reset" class="btn btn-scooter" value="<?= lang('Reset'); ?>" />
            </div>
        </form>
    </div>
    <div role="tabpanel" class="tab-pane fade<?= $subtype == 'login' ? 'in active' : '' ?>" id="login" style="padding-top: 0; padding-bottom: 0;">
        <div style="margin-bottom: 20px">
            <?php if ($login_captcha_enabled) : ?>
                <a href="<?= site_url('marketing_management/switchCaptcha/login/0'); ?>" class="btn btn-portage">
                    <i class="fa fa-check-square-o fa-fw"></i> <?= lang('sys.captchaIsOn'); ?>
                </a>
            <?php else : ?>
                <a href="<?= site_url('marketing_management/switchCaptcha/login/1'); ?>" class="btn btn-portage">
                    <i class="fa fa-square-o fa-fw"></i> <?= lang('sys.captchaIsOff'); ?>
                </a>
            <?php endif; ?>
            <a href="/user_management/viewLogs" target="_blank" class="btn btn-scooter">
                <i class="fa fa-align-justify fa-fw"></i> <?= lang('View Log History'); ?>
            </a>
        </div>
        <form id="updateLoginSettingsForm" action="<?= BASEURL . 'marketing_management/saveLoginSettings' ?>" method="POST">
            <table class="table table-hover table-bordered" style="width: 100%; float: left;">
                <thead>
                    <tr>
                        <th style="text-align: left; width: 50%;"><?= lang('mark.fields'); ?></th>
                        <th style="text-align: center; width: 25%;">
                            <?= lang('Show / Hide'); ?>
                        </th>
                        <th style="text-align: center; width: 25%;">
                            <?= lang('Required'); ?>
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td><?= lang('User Name') ?></td>
                        <td>
                            <div class="visible onoffswitch">
                                <input type="checkbox" class="onoffswitch-checkbox" checked disabled>
                                <label class="onoffswitch-label">
                                    <span class="onoffswitch-inner"></span>
                                    <span class="onoffswitch-switch"></span>
                                </label>
                            </div>
                        </td>
                        <td>
                            <div class="required onoffswitch">
                                <input type="checkbox" class="onoffswitch-checkbox" checked disabled>
                                <label class="onoffswitch-label">
                                    <span class="onoffswitch-inner"></span>
                                    <span class="onoffswitch-switch"></span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><?= lang('Password') ?></td>
                        <td>
                            <div class="visible onoffswitch">
                                <input type="checkbox" class="onoffswitch-checkbox" checked disabled>
                                <label class="onoffswitch-label">
                                    <span class="onoffswitch-inner"></span>
                                    <span class="onoffswitch-switch"></span>
                                </label>
                            </div>
                        </td>
                        <td>
                            <div class="required onoffswitch">
                                <input type="checkbox" class="onoffswitch-checkbox" checked disabled>
                                <label class="onoffswitch-label">
                                    <span class="onoffswitch-inner"></span>
                                    <span class="onoffswitch-switch"></span>
                                </label>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="pull-left" style="margin-top: 6px; margin-right: 20px;">
                <?= lang('Remember Password') ?>
            </div>
            <div class="pull-left">
                <div class="onoffswitch">
                    <input type="hidden" name="remember_password_enabled" value="0">
                    <input type="checkbox" name="remember_password_enabled" class="onoffswitch-checkbox" id="remember-password" value="1" <?= $remember_password_enabled ? 'checked' : '' ?>>
                    <label class="onoffswitch-label" for="remember-password">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                    </label>
                </div>
            </div>
            <div class="clearfix"></div>

            <fieldset class="panel-body">
                <legend><?= lang('Forget Password Settings') ?></legend>
                <div class="pull-left" style="margin-top: 6px; margin-right: 20px;">
                    <?= lang('Forget Password') ?>
                </div>
                <div class="pull-left">
                    <div class="onoffswitch">
                        <input type="hidden" name="forget_password_enabled" value="0">
                        <input type="checkbox" name="forget_password_enabled" class="onoffswitch-checkbox" id="forget-password" value="1" <?= $forget_password_enabled ? 'checked' : '' ?>>
                        <label class="onoffswitch-label" for="forget-password">
                            <span class="onoffswitch-inner"></span>
                            <span class="onoffswitch-switch"></span>
                        </label>
                    </div>
                </div>
                <div class="clearfix"></div>

                <div class="pull-left" style="margin-top: 6px; margin-right: 20px;">
                    <?= lang('password_recovery_note') ?>
                </div>
                <div class="clearfix"></div>
                <br>

                <div class="pull-left">
                    <table class="table table-hover table-bordered" style="width: 100%; float: left;">
                        <thead>
                            <tr>
                                <th colspan="2" style="text-align: left; width: 50%"><?= lang("Password Recovery Options") ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?= lang("Find password by security question") ?></td>
                                <td>
                                    <div class="onoffswitch">
                                        <input type="checkbox" name="password_recovery_option_1" value="1" class="onoffswitch-checkbox" id="password_recovery_option_1" <?= $password_recovery_option_1 ? "checked" : "" ?>>
                                        <label class="onoffswitch-label" for="password_recovery_option_1">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><?= lang("Find password by email") ?></td>
                                <td>
                                    <div class="onoffswitch">
                                        <input type="checkbox" name="password_recovery_option_3" value="1" class="onoffswitch-checkbox" id="password_recovery_option_3" <?= $password_recovery_option_3 ? "checked" : "" ?>>
                                        <label class="onoffswitch-label" for="password_recovery_option_3">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><?= lang("Find password by SMS") ?></td>
                                <td>
                                    <div class="onoffswitch">
                                        <input type="checkbox" name="password_recovery_option_2" value="1" class="onoffswitch-checkbox" id="password_recovery_option_2" <?= $password_recovery_option_2 ? "checked" : "" ?>>
                                        <label class="onoffswitch-label" for="password_recovery_option_2">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="clearfix"></div>
            </fieldset>

            <fieldset class="panel-body">
                <legend><label class="control-label" for="player_login_failed_attempt_block"><?= lang('Player Login Failed Attempt Settings') ?></label></legend>

                <div class="form-group row">
                    <div class="col col-md-4">
                    </div>
                    <div class="col col-md-8">
                        <div class="onoffswitch">
                            <input type="checkbox" name="player_login_failed_attempt_blocked" class="onoffswitch-checkbox" id="player_login_failed_attempt_blocked" value="1" <?= $player_login_failed_attempt_blocked ? 'checked' : '' ?>>
                            <label class="onoffswitch-label" for="player_login_failed_attempt_blocked">
                                <span class="onoffswitch-inner"></span>
                                <span class="onoffswitch-switch"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col col-md-4">
                        <label class="control-label" for="player_login_failed_attempt_times"><?= lang('Player Login Failed Attempt Times') ?></label>
                    </div>
                    <div class="col col-md-8">
                        <input type="number" min="0" step="1" name="player_login_failed_attempt_times" class="form-control" id="player_login_failed_attempt_times" value="<?= $player_login_failed_attempt_times ?>">
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col col-md-4">
                        <label class="control-label" for="player_login_failed_attempt_reset_timeout"><?= lang('Player Login Failed Attempt Reset Timeout') ?></label>
                    </div>
                    <div class="col col-md-8">
                        <div class="input-group">
                            <input type="number" min="0" step="1" name="player_login_failed_attempt_reset_timeout" class="form-control" id="player_login_failed_attempt_reset_timeout" value="<?= $player_login_failed_attempt_reset_timeout ?>">
                            <div class="input-group-addon"><?= lang('minute') ?></div>
                        </div>
                        <div class="help-block">
                            <?= lang('player_login_failed_attempt_reset_timeout_note') ?>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </fieldset>

            <hr />
            <div class="text-center">
                <input type="submit" class="btn btn-linkwater" value="<?= lang('Save'); ?>" />
                <input type="reset" class="btn btn-scooter" value="<?= lang('Reset'); ?>" />
            </div>
        </form>
    </div>
</div>
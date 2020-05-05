<?php
$feature_show_player_upload_realname_verification = $this->utils->isEnabledFeature('show_player_upload_realname_verification');
$address_title = FALSE;
foreach($registration_fields as $registration_field){
    if ($registration_field['registrationFieldId'] == 49 && !$feature_show_player_upload_realname_verification) {
        continue;
    }
    if (in_array($registration_field['registrationFieldId'], [54,55,56,57,58])) {
        continue;
    }
    if($registration_field['visible'] == Registration_setting::VISIBLE){
        $required_class = ($registration_field['required'] == Registration_setting::REQUIRED) ? 'field_required' : '';
?>
        <?php switch($registration_field['registrationFieldId']){
            case 3: # BIRTHDAY ?>
            <?php
            // lowest year wanted
            $cutoff = 1900;
            // current year
            $now = date('Y');

            $birth = set_value($registration_field['alias']);
            if(isset($birth) && !empty($birth)){
                $_dateList = explode("-", $birth);
                $_year = $_dateList[0];
                $_month = $_dateList[1];
                $_day = $_dateList[2];
                $_dayOfMonth = date("t", strtotime("$_year-$_month-01"));
            }
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="form-group form-inline relative birthday-option <?=$required_class?>">
                    <?=$this->registration_setting->displaySymbolHint($registration_field["field_name"])?>

                    <input type="hidden" class="registration-field" name="<?=$registration_field['alias']?>" value="<?=set_value($registration_field['alias'])?>" <?php if($registration_field['required'] == Registration_setting::REQUIRED) echo 'required'; ?>>
                    <div class="row">
                        <div class="col-md-4">
                            <span class="custom-label"><?=lang($registration_field['field_name'])?><?=$this->registration_setting->displayPlaceholderHint($registration_field["field_name"])?></span>
                        </div>
                        <div class="col-md-3">
                            <div class="custom-dropdown">
                                <select class="selectbox form-control registration-field" id="year" name="year" onchange="Register.validateDOB()">
                                    <option value=""><?=lang('reg.14')?></option>
                                    <?php for($y = ($now - $this->utils->getConfig('legal_age')); $y >= $cutoff; $y--): ?>
                                        <option value="<?=$y?>" <?php echo (isset($_year) && ($_year == $y)) ? "selected" : ""; ?>><?=$y?></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 lr-padding">
                            <div class="custom-dropdown">
                                <select class="selectbox form-control registration-field" id="month" name="month" onchange="Register.validateDOB()">
                                    <option value=""><?=lang('reg.59')?></option>
                                    <?php for($m = 1; $m <= 12; $m++): ?>
                                        <option value="<?=sprintf("%02d", $m)?>" <?php echo (isset($_month) && ($_month == $m)) ? "selected" : ""; ?>><?=sprintf("%02d", $m)?></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 lr-padding">
                            <div class="custom-dropdown">
                                <select class="selectbox form-control registration-field" id="day" name="day" onchange="Register.validateDOB()">
                                    <option value=""><?=lang('reg.13')?></option>
                                    <?php if(isset($_dayOfMonth)) : ?>
                                        <?php for($d = 1; $d <= $_dayOfMonth; $d++): ?>
                                            <option value="<?=sprintf("%02d", $d)?>" <?php echo (isset($_day) && ($_day == $d)) ? "selected" : ""; ?>><?=sprintf("%02d", $d)?></option>
                                        <?php endfor; ?>
                                    <?php else : ?>
                                        <?php for($d = 1; $d <= 31; $d++): ?>
                                            <option value="<?=sprintf("%02d", $d)?>"><?=sprintf("%02d", $d)?></option>
                                        <?php endfor; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if($registration_field['required'] == Registration_setting::REQUIRED): ?>
                    <div class="registration-field-note hide mb20">
                        <p class="pl15 mb0">
                            <i class="registration-field-required-icon icon-warning red f16 mr5" id="val_dob"></i>
                            <?=sprintf(lang('formvalidation.required'), lang($registration_field['field_name']))?>
                        </p>
                        <p class="pl15 mb0">
                            <i class="registration-field-required-icon icon-warning red f16 mr5" id="val_dob18"></i>
                            <?=sprintf(lang('mod.mustbe18YearsAbove'), lang($registration_field['field_name']))?>
                        </p>
                    </div>
                <?php endif ?>
            </div>
            <?php break; ?>

        <?php case 1: # FIRSTNAME ?>
            <div class="col-md-6 col-lg-4">
                <div class="form-group form-inline relative <?=$required_class?>">
                    <?=$this->registration_setting->displaySymbolHint($registration_field["field_name"])?>
                    <input type="text" class="form-control registration-field" name="<?=$registration_field['alias']?>" data-rule="<?= htmlentities(json_encode($firstNameRule)); ?>" placeholder="<?=lang('a_reg.1')?> <?=$this->registration_setting->displayPlaceholderHint($registration_field["field_name"])?>"
                        <?php if($registration_field['required'] == Registration_setting::REQUIRED) echo 'required'; ?> value="<?=set_value($registration_field['alias'])?>" onfocus="return Register.validateFirstName(this, this.getAttribute('data-rule'))" oninput="return Register.validateFirstName(this, this.getAttribute('data-rule'))"/>
                </div>
                <div class="registration-field-note hide mb20">
                    <?php if($registration_field['required'] == Registration_setting::REQUIRED): ?>
                        <p class="pl15 mb0">
                            <i class="registration-field-required-icon icon-warning red f16 mr5"></i>
                            <?=lang('validation.realname')?>
                        </p>
                    <?php endif ?>
                    <p class="pl15 mb0">
                        <i id="firstNameRegex" class="icon-warning red f16 mr5"></i>
                        <?=lang('validation.firstNameRegex')?>
                    </p>
                    <?php if($this->utils->isEnabledFeature('enabled_player_registration_restrict_min_length_on_first_name_field')):?>
                        <p class="pl15">
                            <i id="firstNameRestrictMinChars" class="icon-warning red f16 mr5"></i>
                            <?= sprintf(lang('validation.firstNameRestrictMinChars'), $min_first_name_length)?>
                        </p>
                    <?php endif;?>
                </div>
            </div>
            <?php break; ?>

        <?php case 2: # LASTNAME ?>
            <div class="col-md-6 col-lg-4">
                <div class="form-group form-inline relative <?=$required_class?>">
                    <?=$this->registration_setting->displaySymbolHint($registration_field["field_name"])?>
                    <input type="text" class="form-control registration-field" name="<?=$registration_field['alias']?>" placeholder="<?=lang('a_reg.' . $registration_field['registrationFieldId'])?> <?=$this->registration_setting->displayPlaceholderHint($registration_field["field_name"])?>" <?php if($registration_field['required'] == Registration_setting::REQUIRED) echo 'required'; ?> value="<?=set_value($registration_field['alias'])?>" onfocus="return Register.validateLastName(this)" oninput="return Register.validateLastName(this)"/>
                </div>
                <div class="registration-field-note hide mb20">
                    <?php if($registration_field['required'] == Registration_setting::REQUIRED): ?>
                        <p class="pl15 mb0">
                            <i class="registration-field-required-icon icon-warning red f16 mr5"></i>
                            <?=sprintf(lang('formvalidation.required'), lang('a_reg.' . $registration_field['registrationFieldId']))?>
                        </p>
                    <?php endif ?>
                    <p class="pl15">
                        <i id="lastNameRegex" class="icon-warning red f16 mr5"></i>
                        <?=lang('validation.lastNameRegex')?>
                    </p>
                </div>
            </div>
            <?php break; ?>

        <?php case 4: # GENDER ?>
            <div class="col-md-6 col-lg-4">
                <div class="form-group form-inline relative <?=$required_class?>">
                    <?=$this->registration_setting->displaySymbolHint($registration_field["field_name"])?>
                    <div class="custom-dropdown">
                        <select class="form-control registration-field" name="<?=$registration_field['alias']?>" <?php if($registration_field['required'] == Registration_setting::REQUIRED) echo 'required'; ?>>
                            <option value=""><?php echo lang('a_reg.' . $registration_field['registrationFieldId']); ?><?=$this->registration_setting->displayPlaceholderHint($registration_field["field_name"])?></option>
                            <option value="Male" <?=(set_value($registration_field['alias']) == 'Male') ? 'selected' : '';?>><?php echo lang('Male'); ?></option>
                            <option value="Female" <?=(set_value($registration_field['alias']) == 'Female') ? 'selected' : '';?>><?php echo lang('Female'); ?></option>
                        </select>
                    </div>
                </div>
                <?php if($registration_field['required'] == Registration_setting::REQUIRED): ?>
                    <div class="registration-field-note hide mb20">
                        <p class="pl15 mb0">
                            <i class="registration-field-required-icon icon-warning red f16 mr5"></i>
                            <?=sprintf(lang('formvalidation.required'), lang($registration_field['field_name']))?>
                        </p>
                    </div>
                <?php endif ?>
            </div>
            <?php break; ?>

        <?php case 5: # Nationality ?>
            <div class="col-md-6 col-lg-4">
                <div class="form-group form-inline relative <?=$required_class?> resident_country_option">
                    <?=$this->registration_setting->displaySymbolHint($registration_field["field_name"])?>
                    <div class="custom-dropdown">
                        <select id="nationality_list" class="form-control registration-field" name="<?=$registration_field['alias']?>" <?php if($registration_field['required'] == Registration_setting::REQUIRED) echo 'required'; ?> onchange="Register.chosenCountry(this)">
                            <option value=""><?php echo lang('reg.22'); ?><?=$this->registration_setting->displayPlaceholderHint($registration_field["field_name"])?></option>
                            <?php if(!$this->utils->isEnabledFeature('disable_frequently_use_country_in_registration')) : ?>
                                <optgroup label="<?=lang('lang.frequentlyUsed')?>">
                                    <?php foreach($this->utils->getCommonCountryList() as $key){ ?>
                                        <option value="<?=$key?>" <?=(set_value($registration_fields['Nationality']['alias']) == $key) ? "selected" : "";?>><?=lang('country.' . $key)?></option>
                                    <?php } ?>
                                </optgroup>
                            <?php endif; ?>
                            <optgroup label="<?=lang('lang.alphabeticalOrder')?>">
                                <?php foreach($this->utils->getCountryList() as $key){ ?>
                                    <option value="<?=$key?>" <?=(set_value($registration_fields['Nationality']['alias']) == $key) ? "selected" : "";?>><?=lang('country.' . $key)?></option>
                                <?php } ?>
                            </optgroup>
                        </select>
                    </div>
                </div>

                <?php if($registration_field['required'] == Registration_setting::REQUIRED): ?>
                    <div class="registration-field-note countryField hide mb20">
                        <p class="pl15 mb0">
                            <i class="registration-field-required-icon icon-warning red f16 mr5"></i>
                            <?=sprintf(lang('formvalidation.required'), lang($registration_field['field_name']))?>
                        </p>
                    </div>
                <?php endif ?>
            </div>
        <?php break; ?>

        <?php case 7: # LANGUAGE ?>
            <div class="col-md-6 col-lg-4">
                <div class="form-group form-inline relative <?=$required_class?>">
                    <?=$this->registration_setting->displaySymbolHint($registration_field["field_name"])?>
                    <div class="custom-dropdown">
                        <select class="form-control registration-field" name="<?=$registration_field['alias']?>" <?php if($registration_field['required'] == Registration_setting::REQUIRED) echo 'required'; ?>>
                            <option value=""><?php echo lang('a_reg.' . $registration_field['registrationFieldId']); ?><?=$this->registration_setting->displayPlaceholderHint($registration_field["field_name"])?></option>
                            <?php foreach(Language_function::PlayerSupportLanguageNames() as $lang_key => $lang_value): ?>
                                <option value="<?=$lang_key?>" <?=(set_value($registration_field['alias']) == $lang_key) ? 'selected' : '';?>><?php echo $lang_value; ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <?php if($registration_field['required'] == Registration_setting::REQUIRED): ?>
                    <div class="registration-field-note hide mb20">
                        <p class="pl15 mb0">
                            <i class="registration-field-required-icon icon-warning red f16 mr5"></i>
                            <?=sprintf(lang('formvalidation.required'), lang($registration_field['field_name']))?>
                        </p>
                    </div>
                <?php endif ?>
            </div>
            <?php break; ?>

        <?php case 8: # CONTACT NUMBER ?>
            <?php
            $countryNumList = unserialize(COUNTRY_NUMBER_LIST_FULL);
            $visibleDialingCode = ($registration_fields['Dialing Code']['visible'] == Registration_setting::VISIBLE) ? 1 : 0;
            $requireDialingCode = ($registration_fields['Dialing Code']['required'] == Registration_setting::REQUIRED) ? 1 : 0;
            $getDefaultDialingCode = $this->utils->getConfig('enable_default_dialing_code');
            ?>
            <div class="col-md-6 col-lg-4 contact-number relative <?=$required_class?>">
                <?php if ( !$this->utils->is_mobile() ) {?>
                    <?=$this->registration_setting->displaySymbolHint($registration_field["field_name"])?>
                <?php }?>
                <div class="form-group form-inline relative">
                    <?php if ( $this->utils->is_mobile() ) {?>
                        <?=$this->registration_setting->displaySymbolHint($registration_field["field_name"])?>
                    <?php }?>
                    <label class="<?=($visibleDialingCode) ? "bstselect" : "";?>"><i class="fa fa-phone"></i></label>
                    <?php if($visibleDialingCode) : ?>
                        <select id="dialing_code" class="selectpicker registration-field <?php if($requireDialingCode) echo 'required'; ?>" data-width="100px" name="dialing_code" data-requiredfield="dialing-code">
                            <?php if(!empty($getDefaultDialingCode)) : ?>
                                <?php foreach($getDefaultDialingCode as $country => $nums) : ?>
                                    <option title="(+<?=$nums?>)" country="<?=$country?>" value="<?=$nums?>" <?=(set_value('dialing_code') == $nums) ? 'selected' : '';?>><?=sprintf("%s (+%s)", lang('country.' . $country), $nums);?></option>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <option title="<?=lang('reg.77')?> <?=$this->registration_setting->displayPlaceholderHint($registration_fields['Dialing Code']["field_name"])?>" country="" value=""><?=lang('reg.77')?><?=$this->registration_setting->displayPlaceholderHint($registration_field["field_name"])?></option>
                            <?php endif; ?>

                            <?php foreach($countryNumList as $country => $nums) : ?>
                                <?php if(is_array($nums)) : ?>
                                    <?php foreach($nums as $_nums) : ?>
                                        <option title="(+<?=$_nums?>)" country="<?=$country?>" value="<?=$_nums?>" <?=(set_value('dialing_code') == $_nums) ? 'selected' : '';?>><?=sprintf("%s (+%s)", lang('country.' . $country), $_nums);?></option>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <option title="(+<?=$nums?>)" country="<?=$country?>" value="<?=$nums?>" <?=(set_value('dialing_code') == $nums) ? 'selected' : '';?>><?=sprintf("%s (+%s)", lang('country.' . $country), $nums);?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                    <input type="text" class="form-control registration-field fcmonu <?=($visibleDialingCode) ? "bstselect" : "";?>" name="<?=$registration_field['alias']?>"
                        <?php if($contactRule) : ?> data-rule="<?=htmlentities(json_encode($contactRule));?>" maxlength="<?php echo $contactRule['max'] ? $contactRule['max'] : 11; ?>" <?php endif; ?>
                           id="<?=$registration_field['alias']?>" onkeyup="return Register.validateContactNumber(this.value, this.getAttribute('data-rule'))"
                           placeholder="<?=lang($registration_field['field_name'])?> <?=$this->registration_setting->displayPlaceholderHint($registration_field["field_name"])?>"
                        <?php if($registration_field['required'] == Registration_setting::REQUIRED) echo 'required'; ?>
                           value="<?=set_value($registration_field['alias'])?>"
                           data-requiredfield="contact-number">
                        <?php if($this->utils->getConfig('enable_contact_number_custom_display')) : ?>
                        <p class="contact_number_notif"><?=lang('contact_number_notif')?></p>
                        <?php endif; ?>
                </div>
                <div class="fcmonu-note registration-field-note hide mb20">
                    <p class="pl15 mb0">
                        <i id="mobile_format" class="icon-warning red f16 mr5"></i>
                        <span class="validate-mesg format"><?=lang('validation.validateContactNumber')?></span>
                        <span class="validate-mesg in-use" style="display: none;"><?=lang('The number is in use')?></span>
                    </p>
                    <?php if($contactRule) : ?>
                        <?php if($this->utils->is_mobile() && isset($registration_template_for_mobile_reference) && $registration_template_for_mobile_reference == 4): ?>
                            <?php if(isset($contactRule['min']) && isset($contactRule['max']) && $contactRule['min'] != @$contactRule['max']) : ?>
                                <p class="pl15 mb0">
                                    <i id="contact_len_between" class="icon-warning red f16 mr5"></i> <?php echo sprintf(lang('validation.lengthStandard'), $contactRule['min'] . '-' . $contactRule['max']); ?>
                                </p>
                            <?php else: ?>
                                <?php if(isset($contactRule['min']) && $contactRule['min'] != @$contactRule['max']) : ?>
                                    <p class="pl15 mb0">
                                        <i id="contact_len_min" class="icon-warning red f16 mr5"></i> <?php echo sprintf(lang('validation.lengthTooShortStart'), $contactRule['min']); ?>
                                    </p>
                                <?php endif; ?>
                                <?php if(isset($contactRule['max']) && $contactRule['max'] != @$contactRule['min']) : ?>
                                    <p class="pl15 mb0">
                                        <i id="contact_len_max" class="icon-warning red f16 mr5"></i> <?php echo sprintf(lang('validation.lengthTooLongStart'), $contactRule['max']); ?>
                                    </p>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if(isset($contactRule['min'], $contactRule['max']) && $contactRule['min'] == $contactRule['max']) : ?>
                                <p class="pl15 mb0">
                                    <i id="contact_len_same" class="icon-warning red f16 mr5"></i> <?php echo sprintf(lang('validation.lengthStandard'), $contactRule['min']); ?>
                                </p>
                            <?php endif; ?>
                        <?php else: ?>
                            <?php if(isset($contactRule['min']) && $contactRule['min'] != @$contactRule['max']) : ?>
                                <p class="pl15 mb0">
                                    <i id="contact_len_min" class="icon-warning red f16 mr5"></i> <?php echo sprintf(lang('validation.lengthTooShortStart'), $contactRule['min']); ?>
                                </p>
                            <?php endif; ?>
                            <?php if(isset($contactRule['max']) && $contactRule['max'] != @$contactRule['min']) : ?>
                                <p class="pl15 mb0">
                                    <i id="contact_len_max" class="icon-warning red f16 mr5"></i> <?php echo sprintf(lang('validation.lengthTooLongStart'), $contactRule['max']); ?>
                                </p>
                            <?php endif; ?>
                            <?php if(isset($contactRule['min'], $contactRule['max']) && $contactRule['min'] == $contactRule['max']) : ?>
                                <p class="pl15 mb0">
                                    <i id="contact_len_same" class="icon-warning red f16 mr5"></i> <?php echo lang('validation.lengthStandard') . $contactRule['min'] ?></p>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if($requireDialingCode) : ?>
                        <p class="pl15 mb0">
                            <i class="dialing-code registration-field-required-icon icon-warning red f16 mr5"></i> <?php echo lang('validation.validateDialingCode'); ?></p>
                    <?php endif; ?>
                </div>
                <div class="fcmonu-note mb20 msg-container" style="display:none">
                    <p class="pl15"><i class="icon-warning red f16 mr5"></i><span id="sms_verification_msg"></span></p>
                </div>

                <?php if($showSMSField): ?>
                    <button type="button" id="send_sms_verification" class="btn btn-success"
                        <?php if(isset($registration_template_for_mobile_reference) && $registration_template_for_mobile_reference == 4): ?>
                            style=""
                        <?php else: ?>
                            style="position:absolute;right:10px;top:-2px"
                        <?php endif ?>
                    >
                        <?=lang('Send SMS')?>
                    </button>
                <?php endif ?>
            </div>
            <?php break; ?>

        <?php case 11: # SECURITY ?>
            <?php $securityList = ['reg.37', 'reg.38', 'reg.39', 'reg.40', 'reg.41'] ?>
            <div class="col-md-6 col-lg-4">
                <div class="form-group form-inline relative <?=$required_class?>">
                    <?=$this->registration_setting->displaySymbolHint($registration_field["field_name"])?>
                    <div class="custom-dropdown">
                        <select name="<?=$registration_field['alias']?>" class="form-control registration-field" <?php if($registration_field['required'] == Registration_setting::REQUIRED) echo 'required'; ?>>
                            <option value=""><?php echo lang('a_reg.' . $registration_field['registrationFieldId']); ?><?=$this->registration_setting->displayPlaceholderHint($registration_field["field_name"])?></option>
                            <?php foreach($securityList as $val) : ?>
                                <option value="<?=$val?>" <?=set_select('security_question', $val)?> <?=(set_value('secretQuestion') == $val) ? "selected" : ""?>><?=lang($val)?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <?php if($registration_field['required'] == Registration_setting::REQUIRED): ?>
                    <div class="registration-field-note hide mb20">
                        <p class="pl15 mb0">
                            <i class="registration-field-required-icon icon-warning red f16 mr5"></i>
                            <?=sprintf(lang('formvalidation.required'), lang($registration_field['field_name']))?>
                        </p>
                    </div>
                <?php endif ?>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="form-group form-inline relative <?=$required_class?>">
                    <?=$this->registration_setting->displaySymbolHint($registration_field["field_name"])?>
                    <input type="text" class="form-control registration-field" name="secretAnswer" placeholder="<?php echo lang('forgot.06'); ?> <?=$this->registration_setting->displayPlaceholderHint($registration_field["field_name"])?>" <?php if($registration_field['required'] == Registration_setting::REQUIRED) echo 'required'; ?> <?php if($registration_field['required'] == Registration_setting::REQUIRED) echo 'required'; ?> value="<?=set_value('secretAnswer')?>"/>
                </div>
                <?php if($registration_field['required'] == Registration_setting::REQUIRED): ?>
                    <div class="registration-field-note hide mb20">
                        <p class="pl15 mb0">
                            <i class="registration-field-required-icon icon-warning red f16 mr5"></i>
                            <?=sprintf(lang('formvalidation.required'), lang($registration_field['field_name']))?>
                        </p>
                    </div>
                <?php endif ?>
            </div>
            <?php break; ?>

        <?php case 13: # REFERRAL CODE ?>
            <?php if($displayReferralCode) : ?>
                <div class="col-md-6 col-lg-4">
                    <div class="form-group form-inline relative <?=$required_class?>">
                        <?=$this->registration_setting->displaySymbolHint($registration_field["field_name"])?>
                        <label><i class="glyphicon glyphicon-qrcode"></i></label>
                        <input type="text" class="form-control registration-field fcreco" name="<?=$registration_field['alias']?>" onkeyup="return Register.validateReferralCode(this.value)" placeholder="<?=lang($registration_field['field_name'])?> <?=$this->registration_setting->displayPlaceholderHint($registration_field["field_name"])?>" <?php if($registration_field['required'] == Registration_setting::REQUIRED) echo 'required'; ?> value="<?=set_value($registration_field['alias'], $referral_code)?>"/>
                    </div>
                    <?php if($registration_field['required'] == Registration_setting::REQUIRED): ?>
                        <div class="registration-field-note hide mb20">
                            <div class="fcreco-note registration-field-note hide mb20">
                                <p class="pl15 mb0">
                                    <i id="referral_code" class="registration-field-required-icon icon-warning red f16 mr5"></i>
                                    <?=sprintf(lang('formvalidation.required'), lang($registration_field['field_name']))?>
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <?php break; ?>

        <?php case 14: # AFFILIATE CODE ?>
            <?php if($displayAffilateCode): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="form-group form-inline relative <?=$required_class?>">
                        <?=$this->registration_setting->displaySymbolHint($registration_field["field_name"])?>
                        <label><i class="glyphicon glyphicon-qrcode"></i></label>
                        <input type="text" class="form-control registration-field fcreco" name="tracking_code" placeholder="<?=lang($registration_field['field_name'])?> <?=$this->registration_setting->displayPlaceholderHint($registration_field["field_name"])?>" <?php if($registration_field['required'] == Registration_setting::REQUIRED) echo 'required'; ?> value="<?=set_value('tracking_code');?>"/>
                    </div>

                    <?php if($registration_field['required'] == Registration_setting::REQUIRED): ?>
                        <div class="fcreco-note registration-field-note hide mb20">
                            <p class="pl15 mb0">
                                <i id="tracking-code" class="registration-field-required-icon icon-warning red f16 mr5"></i>
                                <?=sprintf(lang('formvalidation.required'), lang($registration_field['field_name']))?>
                            </p>
                        </div>
                    <?php endif ?>
                </div>
            <?php endif ?>
            <?php break; ?>

        <?php case 46: # AGENT TRACKING CODE ?>
            <?php if($displayAgencyCode): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="form-group form-inline relative <?=$required_class?>">
                        <?=$this->registration_setting->displaySymbolHint($registration_field["field_name"])?>
                        <label><i class="fa fa-qrcode"></i></label>
                        <input type="text" class="form-control registration-field fcreco" name="agent_tracking_code" placeholder="<?=lang('a_reg.' . $registration_field['registrationFieldId'])?> <?=$this->registration_setting->displayPlaceholderHint($registration_field["field_name"])?>" <?php if($registration_field['required'] == Registration_setting::REQUIRED) echo 'required'; ?> value="<?=set_value('agent_tracking_code', $agent_tracking_code);?>"/>
                    </div>

                    <?php if($registration_field['required'] == Registration_setting::REQUIRED): ?>
                        <div class="fcreco-note registration-field-note hide mb20">
                            <p class="pl15 mb0">
                                <i id="tracking-code" class="registration-field-required-icon icon-warning red f16 mr5"></i>
                                <?=sprintf(lang('formvalidation.required'), lang('a_reg.' . $registration_field['registrationFieldId']))?>
                            </p>
                        </div>
                    <?php endif ?>
                </div>
            <?php endif ?>
            <?php break; ?>

        <?php case 31: # TERMS OF SERVICE ; ?>
        <?php case 52: # NEWSLETTER SUBSCRIPTION ?>
            <?php break; ?>

        <?php case 33: # COUNTRY ?>
            <div class="col-md-6 col-lg-4">
                <div class="form-group form-inline relative <?=$required_class?> resident_country_option">
                    <?=$this->registration_setting->displaySymbolHint($registration_field["field_name"])?>
                    <div class="custom-dropdown">
                        <select id="country_list" class="form-control registration-field" name="<?=$registration_field['alias']?>" <?php if($registration_field['required'] == Registration_setting::REQUIRED) echo 'required'; ?> onchange="Register.chosenCountry(this)">
                            <option value=""><?php echo lang('reg.a42'); ?><?=$this->registration_setting->displayPlaceholderHint($registration_field["field_name"])?></option>
                            <?php if(!$this->utils->isEnabledFeature('disable_frequently_use_country_in_registration')) : ?>
                                <optgroup label="<?=lang('lang.frequentlyUsed')?>">
                                    <?php foreach($this->utils->getCommonCountryList() as $key){ ?>
                                        <option value="<?=$key?>" <?=(set_value($registration_fields['Resident Country']['alias']) == $key) ? "selected" : "";?>><?=lang('country.' . $key)?></option>
                                    <?php } ?>
                                </optgroup>
                            <?php endif; ?>
                            <optgroup label="<?=lang('lang.alphabeticalOrder')?>">
                                <?php foreach($this->utils->getCountryList() as $key){ ?>
                                    <option value="<?=$key?>" <?=(set_value($registration_fields['Resident Country']['alias']) == $key) ? "selected" : "";?>><?=lang('country.' . $key)?></option>
                                <?php } ?>
                            </optgroup>
                        </select>
                    </div>
                </div>

                <?php if($registration_field['required'] == Registration_setting::REQUIRED): ?>
                    <div class="registration-field-note countryField hide mb20">
                        <p class="pl15 mb0">
                            <i class="registration-field-required-icon icon-warning red f16 mr5"></i>
                            <?=sprintf(lang('formvalidation.required'), lang($registration_field['field_name']))?>
                        </p>
                    </div>
                <?php endif ?>
            </div>
            <?php break; ?>

        <?php case 34: # SMS VERIFICATION CODE ?>
            <div class="col-md-6 col-lg-4">
                <div class="form-group form-inline relative <?=$required_class?>">
                    <?=$this->registration_setting->displaySymbolHint($registration_field["field_name"])?>
                    <label><i class="glyphicon-phone"></i></label>
                    <input type="text" class="form-control registration-field" name="sms_verification_code" placeholder="<?=lang($registration_field['field_name'])?> <?=$this->registration_setting->displayPlaceholderHint($registration_field["field_name"])?>" <?php if($registration_field['required'] == Registration_setting::REQUIRED) echo 'required'; ?> value="<?=set_value('sms_verification_code')?>"/>
                </div>

                <?php if($registration_field['required'] == Registration_setting::REQUIRED): ?>
                    <div class="registration-field-note hide mb20">
                        <p class="pl15 mb0">
                            <i class="registration-field-required-icon icon-warning red f16 mr5"></i>
                            <?=sprintf(lang('formvalidation.required'), lang($registration_field['field_name']))?>
                        </p>
                    </div>
                <?php endif ?>
            </div>
            <?php break; ?>

        <?php case 35: # Withdrawal Password ?>
            <div class="col-md-6 col-lg-4">
                <div class="form-group form-inline relative <?=$required_class?>">
                    <?=$this->registration_setting->displaySymbolHint($registration_field["field_name"])?>
                    <label><i class="icon-pass"></i></label>
                    <input type="password" class="form-control registration-field" name="<?=$registration_field['alias']?>" placeholder="<?=lang($registration_field['field_name'])?> <?=$this->registration_setting->displayPlaceholderHint($registration_field["field_name"])?>" <?php if($registration_field['required'] == Registration_setting::REQUIRED) echo 'required'; ?> value="<?=set_value($registration_field['alias'])?>"/>
                </div>

                <?php if($registration_field['required'] == Registration_setting::REQUIRED): ?>
                    <div class="registration-field-note hide mb20">
                        <p class="pl15 mb0">
                            <i class="registration-field-required-icon icon-warning red f16 mr5"></i>
                            <?=sprintf(lang('formvalidation.required'), lang($registration_field['field_name']))?>
                        </p>
                    </div>
                <?php endif ?>
            </div>
            <?php break; ?>

        <?php case 40: # Bank Name ?>
            <div class="col-md-6 col-lg-4">
                <div class="form-group form-inline relative <?=$required_class?>">
                    <?=$this->registration_setting->displaySymbolHint($registration_field["field_name"])?>
                    <div class="custom-dropdown">
                        <select name="<?=$registration_field['alias']?>" class="form-control registration-field" <?php if($registration_field['required'] == Registration_setting::REQUIRED) echo 'required'; ?>>
                            <?php foreach($banks as $row): ?>
                                <?php if($row['enabled_withdrawal']): ?>
                                    <option value="<?=$row['bankTypeId']?>" <?=(set_value($registration_field['alias']) == $row['bankTypeId']) ? 'selected' : '';?>><?=lang($row['bankName'])?></option>
                                <?php endif ?>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>

                <?php if($registration_field['required'] == Registration_setting::REQUIRED): ?>
                    <div class="registration-field-note hide mb20">
                        <p class="pl15 mb0">
                            <i class="registration-field-required-icon icon-warning red f16 mr5"></i>
                            <?=sprintf(lang('formvalidation.required'), lang($registration_field['field_name']))?>
                        </p>
                    </div>
                <?php endif ?>
            </div>
            <?php break; ?>

        <?php case 36: # City     ?>
        <?php case 37: # Region   ?>
        <?php case 43: # Address  ?>
        <?php case 44: # Address 2?>
            <?php if(!$address_title):?>
                <?php $address_title = TRUE;?>
                <div class="col-md-12 col-lg-12" id="register-address-title" style="display: none;">
                    <?=lang('player.59');?>
                </div>
            <?php endif;?>
            <?php if($full_address_in_one_row): ?>
                <?php $placeholder = lang('Please input your full address'); ?>
            <?php else: ?>
                <?php $placeholder = lang('a_reg.'.$registration_field['registrationFieldId'].'.placeholder');?>
            <?php endif;?>
            <div class="col-md-6 col-lg-4">
                <div class="form-group form-inline relative <?=$required_class?>">
                    <?=$this->registration_setting->displaySymbolHint($registration_field["field_name"])?>
                    <input type="text"
                           class="form-control registration-field"
                           name="<?=$registration_field['alias']?>"
                           placeholder="<?=$placeholder;?> <?=$this->registration_setting->displayPlaceholderHint($registration_field["field_name"])?>"
                           <?php if($registration_field['required'] == Registration_setting::REQUIRED) echo 'required'; ?>
                           value="<?=set_value($registration_field['alias'])?>"
                           maxlength="120"
                    />
                </div>
                <div class="registration-field-note hide mb20">
                    <p class="pl15 mb0">
                        <i class="icon-checked green f16 mr5"></i> <?=sprintf(lang('validation.maxlength'), 120)?>
                    </p>
                    <?php if($registration_field['required'] == Registration_setting::REQUIRED): ?>
                        <p class="pl15 mb0">
                            <i class="registration-field-required-icon icon-warning red f16 mr5"></i>
                            <?=lang('a_reg.' . $registration_field['registrationFieldId'] . '.hint')?>
                        </p>
                    <?php endif ?>
                </div>
            </div>
            <?php break; ?>

        <?php case 45: # Email  ignore?>
        <?php case 50: # Dialing code use in case 8?>
            <?php break; ?>

        <?php default: ?>
            <?php $fieldName = $im1 = $this->config->item($registration_field['field_name'], 'cust_non_lang_translation'); ?>
            <div class="col-md-6 col-lg-4">
                <div class="form-group form-inline relative <?=$required_class?>">
                    <?=$this->registration_setting->displaySymbolHint($registration_field["field_name"])?>
                    <input type="text" class="form-control registration-field" name="<?=$registration_field['alias']?>" placeholder="<?=($fieldName) ? $fieldName : lang('a_reg.' . $registration_field['registrationFieldId'])?> <?=$this->registration_setting->displayPlaceholderHint($registration_field["field_name"])?>" <?php if($registration_field['required'] == Registration_setting::REQUIRED) echo 'required'; ?> value="<?=set_value($registration_field['alias'])?>"/>
                </div>

                <?php if($registration_field['required'] == Registration_setting::REQUIRED): ?>
                    <div class="registration-field-note hide mb20">
                        <p class="pl15 mb0">
                            <i class="registration-field-required-icon icon-warning red f16 mr5"></i>
                            <?=sprintf(lang('formvalidation.required'), (($fieldName) ? $fieldName : lang('a_reg.' . $registration_field['registrationFieldId'])))?>
                        </p>

                    </div>
                <?php endif ?>
            </div>
            <?php break; ?>

            <?php
        }
    }
}
?>
<script>
    //OGP-16006
    var getDefaultDialingCode = <?= !empty($this->utils->getConfig('enable_default_dialing_code')) ? 'true' : 'false' ?>;
    $(document).ready(function(){
        if(getDefaultDialingCode){
            Register.validateRequired($('#dialing_code'), $('#dialing_code').val());
        }
    });
    // export sys feature block_emoji_chars_in_real_name_field, OGP-12268
    var sys_feature_block_emoji = <?= $this->utils->isEnabledFeature('block_emoji_chars_in_real_name_field') ? 'true' : 'false' ?>;
</script>
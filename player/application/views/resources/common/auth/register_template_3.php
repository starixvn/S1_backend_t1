<div class="container">
    <div class="cstm-mod registration-mod" role="document">
        <div class="modal-content">
            <div class="modal-header text-center">
                <h4 class="modal-title f24" id="myModalLabel"><?=lang('Register Your Account'); ?></h4>
            </div>
            <div class="modal-body">
                <form action="<?=site_url('player_center/postRegisterPlayer')?>" method="post" id="registration_form">
                    <?php if(!$displayAffilateCode && (!empty($tracking_code) || !empty($tracking_source_code))): ?>
                        <input type="hidden" value="<?=set_value('tracking_code', $tracking_code)?>" name="tracking_code"/>
                        <input type="hidden" value="<?=set_value('tracking_source_code', $tracking_source_code)?>" name="tracking_source_code"/>
                    <?php endif ?>

                    <?php if(!$displayAgencyCode && (!empty($agent_tracking_code) || !empty($agent_tracking_source_code))): ?>
                        <input type="hidden" value="<?=set_value('agent_tracking_code', $agent_tracking_code)?>" name="agent_tracking_code"/>
                        <input type="hidden" value="<?=set_value('agent_tracking_source_code', $agent_tracking_source_code)?>" name="agent_tracking_source_code"/>
                    <?php endif ?>

                    <?php if(!$displayReferralCode && !empty($referral_code)): ?>
                        <input type="hidden" value="<?=set_value('invitationCode', $referral_code)?>" name="invitationCode"/>
                    <?php endif ?>

                    <?php if($this->utils->isEnabledFeature('enable_income_access') && isset($btag) && !empty($btag)) : ?>
                        <input type="hidden" name="btag" value="<?=set_value('btag', $btag)?>" />
                    <?php endif; ?>

                    <div class="row">
                        <?php // USERNAME ?>
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group form-inline relative field_required">
                                <?= $require_display_symbol?>
                                <label><i class="icon-user"></i></label>
                                <input type="text"
                                       class="form-control registration-field fcname custom-inline-block"
                                       name="username"
                                       id="username"
                                       placeholder="<?=lang('Username');?> <?=$require_placeholder_text?>"
                                       onKeyUp="Register.lowerCase(this)"
                                       onfocus="return Register.validateUsernameRequirements(this.value)"
                                       oninput="return Register.validateUsernameRequirements(this.value)"
                                       value="<?=set_value('username')?>"
                                />
                            </div>
                            <div class="fcname-note registration-field-note hide mb20">
                                <p class="pl15 mb0">
                                    <i id="username_len" class="icon-warning red f16 mr5"></i> <?php echo lang('Username') . sprintf(lang('validation.lengthRangeStandard'), $min_username_length, $max_username_length);?>
                                </p>
                                <?php if (!empty($this->utils->isRestrictUsernameEnabled())) : ?>
                                    <p class="pl15 mb0">
                                        <i id="username_charcombi" class="icon-warning red f16 mr5"></i> <?=lang('validation.validateUsername01')?>
                                    </p>
                                <?php else: ?>
                                    <p class="pl15 mb0">
                                        <i id="username_charcombi" class="icon-warning red f16 mr5"></i> <?=lang('validation.validateUsername02')?>
                                    </p>
                                <?php endif; ?>
                                <p class="pl15 mb0" id="username_exist_failed" >
                                    <i class="icon-warning red f16 mr5"></i> <?=lang('validation.availabilityUsername_2')?>
                                </p>
                                <p class="pl15 mb0" id="username_exist_available" style="display: none;">
                                    <i class="icon-checked green f16 mr5"></i> <?=lang('validation.availabilityUsername_available')?>
                                </p>
                                <p class="pl15">
                                    <i id="username_lan" class="icon-warning red f16 mr5 invisible"></i> <?=lang('username.lan')?>
                                </p>
                            </div>
                        </div>

                        <?php // PASSWORD ?>
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group form-inline relative field_required">
                                <?= $require_display_symbol?>
                                <label><i class="icon-pass"></i></label>
                                <input type="password"
                                       class="form-control registration-field fcpass custom-inline-block"
                                       name="password" id="password"
                                       onfocus="return Register.validatePasswordRequirements(this.value)"
                                       oninput="return Register.validatePasswordRequirements(this.value)"
                                       placeholder="<?=lang('Password'); ?> <?=$require_placeholder_text?>"
                                       value="<?=set_value('password')?>"
                                />
                            </div>
                            <div class="fcpass-note registration-field-note hide mb20">
                                <p class="pl15 mb0">
                                    <i id="password_len" class="icon-warning red f16 mr5"></i> <?php echo lang('Password') . ' ' . sprintf(lang('validation.lengthRangeStandard'), $min_password_length, $max_password_length);?>
                                </p>
                                <?php if (!empty($this->utils->isRestrictPasswordEnabled())) : ?>
                                    <p class="pl15 mb0"><i id="password_regex" class="icon-warning red f16 mr5"></i> <?=lang('validation.contentPassword01')?></p>
                                <?php else: ?>
                                    <p class="pl15 mb0"><i id="password_regex" class="icon-warning red f16 mr5"></i> <?=lang('validation.contentPassword03')?></p>
                                <?php endif; ?>
                                <p class="pl15"><i id="password_not_username" class="icon-warning red f16 mr5"></i> <?=lang('validation.contentPassword02')?></p>
                            </div>
                        </div>

                        <?php // CONFIRM PASSWORD ?>
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group form-inline relative field_required">
                                <?= $require_display_symbol?>
                                <label><i class="icon-pass"></i></label>
                                <input type="password"
                                       class="form-control registration-field fccpass custom-inline-block"
                                       name="cpassword"
                                       onfocus="return Register.validateConfirmPassword(this.value)"
                                       oninput="return Register.validateConfirmPassword(this.value)"
                                       placeholder="<?=lang('Confirm Password'); ?> <?=$require_placeholder_text?>"
                                       value="<?=set_value('cpassword')?>"
                                />
                            </div>
                            <div class="fccpass-note registration-field-note hide mb20">
                                <p class="pl15 mb0"><i id="cpassword_reenter" class="icon-warning red f16 mr5"></i> <span id="cpassword_reenter_msg"><?=lang('validation.retypePassword')?></span></p>
                            </div>
                        </div>

                        <?php if(in_array('email', $visibled_fields)){
                            $email_required = $registration_fields['Email']['required'];
                            // EMAIL ?>
                            <div class="col-md-12 col-lg-12">
                                <div class="form-group form-inline relative <?php if ($email_required == Registration_setting::REQUIRED) echo 'field_required';?> ">
                                <?php if ( $email_required == Registration_setting::REQUIRED ) {?>
                                    <?= $require_display_symbol?>
                                <?php }?>
                                <label><i class="glyphicon glyphicon-envelope"></i></label>
                                <input type="text"
                                       class="form-control registration-field fcemail"
                                       name="email"
                                       id="email"
                                       data-validateRequired="<?= ($email_required == Registration_setting::REQUIRED) ? 0 : 1 ?>"
                                       placeholder="<?=lang('Email Address');?> <?= $this->registration_setting->displayPlaceholderHint($registration_fields['Email']["field_name"])?>"
                                       value="<?=set_value('email')?>"
                                       onfocus="return Register.validateEmail(this.value)"
                                       oninput="return Register.validateEmail(this.value)"
                                       <?php if ($email_required == Registration_setting::REQUIRED) echo 'required';?>
                                />
                                </div>
                                <div class="fcemail-note registration-field-note hide mb20">
                                    <p class="pl15 mb0">
                                        <i id="email_required" class="registration-field-required-icon icon-warning red f16 mr5"></i> <?=lang('validation.requiredEmail')?>
                                    </p>
                                </div>
                            </div>
                        <?php } ?>

                        <?php // REGISTRATION FIELDS ?>
                        <?php
                            $feature_show_player_upload_realname_verification = $this->utils->isEnabledFeature('show_player_upload_realname_verification');
                            $address_title = FALSE;
                        ?>
                        <?php foreach ($registration_fields as $registration_field): ?>
                            <?php
                                if ($registration_field['registrationFieldId'] == 49 && !$feature_show_player_upload_realname_verification) {
                                    continue;
                                }
                                if (in_array($registration_field['registrationFieldId'], [54,55,56,57,58])) {
                                    continue;
                                }
                            ?>
                            <?php if ($registration_field['visible'] == Registration_setting::VISIBLE): ?>
                                <?php $required_class = ($registration_field['required'] == Registration_setting::REQUIRED) ? 'field_required' : ''; ?>
                                <?php switch ($registration_field['registrationFieldId']) {
                                    case 34: # SMS Verification Code ; Not displaying as it's included in contact number field
                                    case 31: # TERMS OF SERVICE
                                    case 45: # Email ignore
                                    case 50: # Dialing code use in case 8 ?>
                                    <?php break; ?>

                                    <?php case 1: # FirstName ?>
                                        <div class="col-md-12 col-lg-12">
                                            <div class="form-group form-inline relative <?= $required_class?>">
                                                <?=$this->registration_setting->displaySymbolHint($registration_field["field_name"])?>
                                                <input type="text"
                                                       class="form-control registration-field custom-inline-block"
                                                       name="<?=$registration_field['alias']?>"
                                                       data-rule="<?= htmlentities(json_encode($firstNameRule)); ?>"
                                                       placeholder="<?=lang('a_reg.'.$registration_field['registrationFieldId'])?> <?= $this->registration_setting->displayPlaceholderHint($registration_field["field_name"])?>"
                                                       <?php if ($registration_field['required'] == Registration_setting::REQUIRED) echo 'required';?>
                                                       value="<?=set_value($registration_field['alias'])?>"
                                                       onfocus="return Register.validateFirstName(this, this.getAttribute('data-rule'))"
                                                       oninput="return Register.validateFirstName(this, this.getAttribute('data-rule'))"
                                                />
                                            </div>
                                            <div class="registration-field-note hide mb20">
                                                <?php if ($registration_field['required'] == Registration_setting::REQUIRED): ?>
                                                    <p class="pl15 mb0">
                                                        <i class="registration-field-required-icon icon-warning red f16 mr5"></i>
                                                        <?php if (lang('notify.109')) : ?>
                                                            <?= lang('notify.109'); ?>
                                                        <?php elseif(lang('reg.notify.ole777.realname')): ?>
                                                            <?=lang('reg.notify.ole777.realname')?>
                                                        <?php else : ?>
                                                            <?= sprintf(lang('formvalidation.required'),lang('a_reg.'.$registration_field['registrationFieldId']))?>
                                                        <?php endif; ?>
                                                    </p>
                                                    <?php if (lang('notify.108')) : ?>
                                                        <p class="pl15 mb0">
                                                            <i class="red f16 mr5">※ </i><?=lang('notify.108');?>
                                                        </p>
                                                    <?php endif; ?>
                                                <?php endif ?>
                                                <p class="pl15 mb0">
                                                    <i id="firstNameRegex" class="icon-warning red f16 mr5"></i>
                                                    <?= lang('validation.firstNameRegex')?>
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
                                        <div class="col-md-12 col-lg-12">
                                            <div class="form-group form-inline relative <?= $required_class?>">
                                            <?=$this->registration_setting->displaySymbolHint($registration_field["field_name"])?>
                                                <input type="text"
                                                       class="form-control registration-field custom-inline-block"
                                                       name="<?=$registration_field['alias']?>"
                                                       placeholder="<?=lang('a_reg.'.$registration_field['registrationFieldId'])?> <?= $this->registration_setting->displayPlaceholderHint($registration_field["field_name"])?> "
                                                       <?php if ($registration_field['required'] == Registration_setting::REQUIRED) echo 'required';?>
                                                       value="<?=set_value($registration_field['alias'])?>"
                                                       onfocus="return Register.validateLastName(this)"
                                                       oninput="return Register.validateLastName(this)"
                                                />
                                            </div>
                                            <div class="registration-field-note hide mb20">
                                                <?php if ($registration_field['required'] == Registration_setting::REQUIRED): ?>
                                                <p class="pl15 mb0">
                                                    <i class="registration-field-required-icon icon-warning red f16 mr5"></i>
                                                    <?= sprintf(lang('formvalidation.required'),lang('a_reg.'.$registration_field['registrationFieldId']))?>
                                                </p>
                                                <?php endif ?>
                                                <p class="pl15">
                                                    <i id="lastNameRegex" class="icon-warning red f16 mr5"></i>
                                                    <?= lang('validation.lastNameRegex')?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php break; ?>

                                    <?php case 3: # BIRTHDAY ?>
                                        <?php
                                            // lowest year wanted
                                            $cutoff = 1900;
                                            // current year
                                            $now = date('Y');
                                        ?>
                                        <div class="col-md-12 col-lg-12">
                                            <div class="form-group form-inline relative birthday-option <?= $required_class?>">
                                                <?=$this->registration_setting->displaySymbolHint($registration_field["field_name"])?>
                                                <input type="hidden" class="registration-field" name="<?=$registration_field['alias']?>" value="<?=set_value($registration_field['alias']) ?>" <?php if ($registration_field['required'] == Registration_setting::REQUIRED) echo 'required';?>>
                                                <?php
                                                    $birth = set_value($registration_field['alias']);
                                                    if (isset($birth) && !empty($birth)) {
                                                        $_dateList = explode("-",$birth);
                                                        $_year = $_dateList[0];
                                                        $_month = $_dateList[1];
                                                        $_day = $_dateList[2];
                                                        $_dayOfMonth = date("t",strtotime("$_year-$_month-01"));
                                                    }
                                                ?>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <span class="custom-label"><?=lang($registration_field['field_name'])?><?= $this->registration_setting->displayPlaceholderHint($registration_field["field_name"])?></span>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="custom-dropdown">
                                                            <select class="selectbox form-control registration-field" id="year" name="year" onchange="Register.validateDOB()">
                                                                <option value=""><?= lang('reg.14') ?></option>
                                                                <?php $now = date('Y'); ?>
                                                                <?php for($y = ($now - $this->utils->getConfig('legal_age')); $y >= $cutoff; $y--): ?>
                                                                    <option value="<?=$y?>" <?php echo (isset($_year) && ($_year == $y)) ? "selected" : ""; ?>><?=$y?></option>
                                                                <?php endfor?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 lr-padding">
                                                        <div class="custom-dropdown">
                                                            <select class="selectbox form-control registration-field" id="month" name="month" onchange="Register.validateDOB()">
                                                                <option value=""><?= lang('reg.59') ?></option>
                                                                <?php for($m = 1; $m <= 12; $m++): ?>
                                                                <option value="<?=sprintf("%02d",$m)?>" <?php echo (isset($_month) && ($_month == $m)) ? "selected" : ""; ?>><?=sprintf("%02d",$m)?></option>
                                                                <?php endfor?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 lr-padding">
                                                        <div class="custom-dropdown">
                                                            <select class="selectbox form-control registration-field" id="day" name="day" onchange="Register.validateDOB()">
                                                                <option value=""><?= lang('reg.13') ?></option>
                                                                <?php if (isset($_dayOfMonth)) : ?>
                                                                    <?php for($d = 1; $d <= $_dayOfMonth; $d++): ?>
                                                                    <option value="<?=sprintf("%02d",$d)?>" <?php echo (isset($_day) && ($_day == $d)) ? "selected" : ""; ?>><?=sprintf("%02d",$d)?></option>
                                                                    <?php endfor?>
                                                                <?php else : ?>
                                                                    <?php for($d = 1; $d <=31; $d++): ?>
                                                                    <option value="<?= sprintf("%02d",$d) ?>"><?= sprintf("%02d",$d) ?></option>
                                                                    <?php endfor; ?>
                                                                <?php endif; ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php if ($registration_field['required'] == Registration_setting::REQUIRED): ?>
                                            <div class="registration-field-note hide mb20">
                                                <p class="pl15 mb0">
                                                    <i class="registration-field-required-icon icon-warning red f16 mr5" id="val_dob"></i>
                                                    <?= sprintf(lang('formvalidation.required'),lang($registration_field['field_name']))?>
                                                </p>
                                                <p class="pl15 mb0">
                                                    <i class="registration-field-required-icon icon-warning red f16 mr5" id="val_dob18"></i>
                                                    <?= sprintf(lang('mod.mustbe18YearsAbove'),lang($registration_field['field_name']))?>
                                                </p>
                                            </div>
                                            <?php endif ?>
                                        </div>
                                    <?php break; ?>

                                    <?php case 4: # GENDER ?>
                                        <div class="col-md-12 col-lg-12">
                                            <div class="form-group form-inline relative <?= $required_class?>">
                                                <?=$this->registration_setting->displaySymbolHint($registration_field["field_name"])?>
                                                <div class="custom-dropdown">
                                                    <select class="form-control registration-field" name="<?=$registration_field['alias']?>" <?php if ($registration_field['required'] == Registration_setting::REQUIRED) echo 'required';?>>
                                                        <option value=""><?=lang('a_reg.'.$registration_field['registrationFieldId']); ?><?= $this->registration_setting->displayPlaceholderHint($registration_field["field_name"])?></option>
                                                        <option value="Male" <?= (set_value($registration_field['alias']) == 'Male') ? 'selected' : '' ; ?>><?=lang('Male'); ?></option>
                                                        <option value="Female" <?= (set_value($registration_field['alias']) == 'Female') ? 'selected' : '' ; ?>><?=lang('Female'); ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <?php if ($registration_field['required'] == Registration_setting::REQUIRED): ?>
                                                <div class="registration-field-note hide mb20">
                                                    <p class="pl15 mb0">
                                                        <i class="registration-field-required-icon icon-warning red f16 mr5"></i>
                                                        <?= sprintf(lang('formvalidation.required'),lang($registration_field['field_name']))?>
                                                    </p>
                                                </div>
                                            <?php endif ?>
                                        </div>
                                    <?php break; ?>

                                    <?php case 7: # LANGUAGE ?>
                                        <div class="col-md-12 col-lg-12">
                                            <div class="form-group form-inline relative <?= $required_class?>">
                                                <?=$this->registration_setting->displaySymbolHint($registration_field["field_name"])?>
                                                <div class="custom-dropdown">
                                                    <select class="form-control registration-field" name="<?=$registration_field['alias']?>" <?php if ($registration_field['required'] == Registration_setting::REQUIRED) echo 'required';?>>
                                                        <option value=""><?=lang('a_reg.'.$registration_field['registrationFieldId']);?><?= $this->registration_setting->displayPlaceholderHint($registration_field["field_name"])?></option>
                                                        <?php foreach(Language_function::PlayerSupportLanguageNames() as $lang_key => $lang_value): ?>
                                                        <option value="<?=$lang_key?>" <?= (set_value($registration_field['alias']) == $lang_key) ? 'selected' : '' ; ?>><?=$lang_value;?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <?php if ($registration_field['required'] == Registration_setting::REQUIRED): ?>
                                                <div class="registration-field-note hide mb20">
                                                    <p class="pl15 mb0">
                                                        <i class="registration-field-required-icon icon-warning red f16 mr5"></i>
                                                        <?= sprintf(lang('formvalidation.required'),lang($registration_field['field_name']))?>
                                                    </p>
                                                </div>
                                            <?php endif ?>
                                        </div>
                                    <?php break; ?>

                                    <?php case 8: # CONTACT NUMBER ?>
                                        <div class="col-md-12 col-lg-12 contact-number relative <?= $required_class?>">
                                            <?=$this->registration_setting->displaySymbolHint($registration_field["field_name"])?>
                                            <?php
                                                $countryNumList = unserialize(COUNTRY_NUMBER_LIST_FULL);
                                                $visibleDialingCode = ($registration_fields['Dialing Code']['visible'] == Registration_setting::VISIBLE) ? 1 : 0 ;
                                                $requireDialingCode = ($registration_fields['Dialing Code']['required'] == Registration_setting::REQUIRED) ? 1 : 0;
                                            ?>
                                            <div class="form-group form-inline relative">
                                                <label class="<?= ($visibleDialingCode) ? "bstselect" : ""; ?>"><i class="icon-mobile"></i></label>
                                                <?php if($visibleDialingCode) : ?>
                                                    <select id="dialing_code" class="selectpicker registration-field <?php if ($requireDialingCode) echo 'required';?>" data-width="100px" name="dialing_code" data-requiredfield="dialing-code">
                                                        <option title="<?=lang('reg.77')?><?= $this->registration_setting->displayPlaceholderHint($registration_fields['Dialing Code']["field_name"])?>" country="" value=""><?=lang('reg.77')?>
                                                            <?= $this->registration_setting->displayPlaceholderHint($registration_field["field_name"])?>
                                                        </option>
                                                        <?php foreach ($countryNumList as $country => $nums) : ?>
                                                            <?php if (is_array($nums)) : ?>
                                                                <?php foreach ($nums as $_nums) : ?>
                                                                    <option title="(+<?=$_nums?>)" country="<?=$country?>" value="<?=$_nums?>" <?= (set_value('dialing_code') == $_nums) ? 'selected' : '' ; ?>>
                                                                        <?= sprintf("%s (+%s)", lang('country.'.$country), $_nums);?>
                                                                    </option>
                                                                <?php endforeach ; ?>
                                                            <?php else : ?>
                                                                <option title="(+<?=$nums?>)" country="<?=$country?>" value="<?=$nums?>" <?= (set_value('dialing_code') == $nums) ? 'selected' : '' ; ?>>
                                                                    <?= sprintf("%s (+%s)", lang('country.'.$country), $nums); ?>
                                                                </option>
                                                            <?php endif ; ?>
                                                        <?php endforeach ; ?>
                                                    </select>
                                                <?php endif ; ?>
                                                <input type="text"
                                                       class="form-control registration-field fcmonu custom-inline-block <?= ($visibleDialingCode) ? "bstselect" : ""; ?>"
                                                       name="<?=$registration_field['alias']?>"
                                                       <?php if ($contactRule) : ?>
                                                            data-rule="<?= htmlentities(json_encode($contactRule)); ?>"
                                                            maxlength="<?php echo $contactRule['max'] ? $contactRule['max'] : 11; ?>"
                                                       <?php endif; ?>
                                                       id="<?=$registration_field['alias']?>"
                                                       onkeyup="return Register.validateContactNumber(this.value, this.getAttribute('data-rule'))"
                                                       placeholder="<?=lang($registration_field['field_name'])?> <?= $this->registration_setting->displayPlaceholderHint($registration_field["field_name"])?>"
                                                       <?php if ($registration_field['required'] == Registration_setting::REQUIRED) echo 'required';?>
                                                       value="<?=set_value($registration_field['alias'])?>"
                                                       data-requiredfield="contact-number"
                                                />
                                            </div>
                                            <div class="fcmonu-note registration-field-note hide mb20">
                                                <p class="pl15 mb0">
                                                    <i id="mobile_format" class="icon-warning red f16 mr5"></i>
                                                    <span class="validate-mesg format"><?=lang('validation.validateContactNumber')?></span>
                                                    <span class="validate-mesg in-use" style="display: none;"><?=lang('The number is in use')?></span>
                                                </p>
                                                <?php if ($contactRule) : ?>
                                                    <?php if (isset($contactRule['min']) && $contactRule['min'] != @$contactRule['max']) : ?>
                                                        <p class="pl15 mb0"><i id="contact_len_min" class="icon-warning red f16 mr5"></i> <?php echo sprintf(lang('validation.lengthTooShortStart'), $contactRule['min']);?></p>
                                                    <?php endif; ?>
                                                    <?php if (isset($contactRule['max']) && $contactRule['max'] != @$contactRule['min']) : ?>
                                                        <p class="pl15 mb0"><i id="contact_len_max" class="icon-warning red f16 mr5"></i> <?php echo sprintf(lang('validation.lengthTooLongStart'),$contactRule['max']);?></p>
                                                    <?php endif; ?>
                                                    <?php if (isset($contactRule['min'], $contactRule['max']) && $contactRule['min'] == $contactRule['max']) : ?>
                                                        <p class="pl15 mb0"><i id="contact_len_same" class="icon-warning red f16 mr5"></i> <?php echo lang('validation.lengthStandard') . $contactRule['min']?></p>
                                                    <?php endif; ?>
                                                <?php endif; ?>

                                                <?php if ($requireDialingCode) : ?>
                                                    <p class="pl15 mb0"><i class="dialing-code registration-field-required-icon icon-warning red f16 mr5"></i> <?=lang('validation.validateDialingCode'); ?></p>
                                                <?php endif; ?>
                                            </div>
                                            <?php if($this->utils->getConfig('contact_number_note')): ?>
                                                <span class="help-block"><?= lang('phone_note') ?></span>
                                            <?php endif;?>
                                            <?php if ($showSMSField): ?>
                                                <button type="button" id="send_sms_verification" class="btn btn-success" style="position:absolute;right:10px;top:-2px">
                                                    <?=lang('Send SMS')?>
                                                </button>
                                            <?php endif ?>
                                            <div class="fcmonu-note mb20 msg-container" style="display:none">
                                                <p class="pl15"><i class="icon-warning red f16 mr5"></i><span id="sms_verification_msg"></span></p>
                                            </div>
                                        </div>

                                        <?php if($showSMSField) : # Need SMS VERIFICATION CODE ?>
                                            <div class="col-md-12 col-lg-12">
                                                <?php $required_class = ($registration_fields["SMS Verification Code"]['required'] == Registration_setting::REQUIRED) ? 'field_required' : '';?>
                                                <div class="form-group form-inline relative <?= $required_class?>">
                                                <?=$this->registration_setting->displaySymbolHint($registration_fields["SMS Verification Code"]["field_name"])?>
                                                    <input type="text"
                                                           class="form-control registration-field custom-inline-block"
                                                           name="sms_verification_code"
                                                           placeholder="<?=lang('SMS Verification Code')?> <?= $this->registration_setting->displayPlaceholderHint($registration_fields["SMS Verification Code"]["field_name"])?>"
                                                           <?php if ($registration_fields["SMS Verification Code"]['required'] == Registration_setting::REQUIRED) echo 'required';?>
                                                           value="<?=set_value('sms_verification_code')?>"
                                                    />
                                                </div>
                                                <?php if ($registration_fields["SMS Verification Code"]['required'] == Registration_setting::REQUIRED): ?>
                                                    <div class="registration-field-note hide mb20">
                                                        <p class="pl15 mb0">
                                                            <i class="registration-field-required-icon icon-warning red f16 mr5"></i>
                                                            <?= sprintf(lang('SMS Code'))?>
                                                        </p>
                                                    </div>
                                                <?php endif ?>
                                            </div>
                                        <?php endif;  ?>
                                    <?php break; ?>

                                    <?php case 11: # SECURITY ?>
                                        <?php $securityList = ['reg.37', 'reg.38', 'reg.39', 'reg.40', 'reg.41'] ?>
                                        <div class="col-md-12 col-lg-12">
                                            <div class="form-group form-inline relative <?= $required_class?>">
                                                <?=$this->registration_setting->displaySymbolHint($registration_field["field_name"])?>
                                                <div class="custom-dropdown">
                                                    <select name="<?=$registration_field['alias']?>" class="form-control registration-field" <?php if ($registration_field['required'] == Registration_setting::REQUIRED) echo 'required';?>>
                                                        <option value=""><?=lang('a_reg.'.$registration_field['registrationFieldId']);?><?= $this->registration_setting->displayPlaceholderHint($registration_field["field_name"])?></option>
                                                        <?php foreach($securityList as $val) : ?>
                                                        <option value="<?=$val?>" <?=set_select('security_question', $val)?> <?= (set_value('secretQuestion') == $val) ? "selected" : "" ?>><?=lang($val)?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <?php if ($registration_field['required'] == Registration_setting::REQUIRED): ?>
                                                <div class="registration-field-note hide mb20">
                                                    <p class="pl15 mb0">
                                                        <i class="registration-field-required-icon icon-warning red f16 mr5"></i>
                                                        <?= sprintf(lang('formvalidation.required'),lang($registration_field['field_name']))?>
                                                    </p>
                                                </div>
                                            <?php endif ?>
                                        </div>
                                        <div class="col-md-12 col-lg-12">
                                            <div class="form-group form-inline relative <?= $required_class?>">
                                                <?=$this->registration_setting->displaySymbolHint($registration_field["field_name"])?>
                                                <input type="text"
                                                       class="form-control registration-field"
                                                       name="secretAnswer"
                                                       placeholder="<?=lang('forgot.06');?> <?= $this->registration_setting->displayPlaceholderHint($registration_field["field_name"])?>"
                                                       <?php if ($registration_field['required'] == Registration_setting::REQUIRED) echo 'required';?>
                                                       value="<?=set_value('secretAnswer')?>"
                                                />
                                            </div>
                                            <?php if ($registration_field['required'] == Registration_setting::REQUIRED): ?>
                                                <div class="registration-field-note hide mb20">
                                                    <p class="pl15 mb0">
                                                        <i class="registration-field-required-icon icon-warning red f16 mr5"></i>
                                                        <?= sprintf(lang('formvalidation.required'),lang($registration_field['field_name']))?>
                                                    </p>
                                                </div>
                                            <?php endif ?>
                                        </div>
                                    <?php break; ?>

                                    <?php case 13: #REFERRAL CODE ?>
                                        <?php if ($displayReferralCode) : ?>
                                            <div class="col-md-12 col-lg-12">
                                                <div class="form-group form-inline relative <?= $required_class?>">
                                                    <?=$this->registration_setting->displaySymbolHint($registration_field["field_name"])?>
                                                    <label><i class="glyphicon glyphicon-qrcode"></i></label>
                                                    <input type="text"
                                                           class="form-control registration-field fcreco"
                                                           name="<?=$registration_field['alias']?>"
                                                           onkeyup="return Register.validateReferralCode(this.value)"
                                                           placeholder="<?=lang($registration_field['field_name'])?> <?= $this->registration_setting->displayPlaceholderHint($registration_field["field_name"])?>"
                                                           <?php if ($registration_field['required'] == Registration_setting::REQUIRED) echo 'required';?>
                                                           value="<?=set_value($registration_field['alias'])?>"
                                                    />
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

                                    <?php case 14: #AFFILIATE CODE ?>
                                        <?php if($displayAffilateCode): ?>
                                            <div class="col-md-12 col-lg-12">
                                                <div class="form-group form-inline relative <?= $required_class?>">
                                                    <?=$this->registration_setting->displaySymbolHint($registration_field["field_name"])?>
                                                    <label><i class="glyphicon glyphicon-qrcode"></i></label>
                                                    <input type="text"
                                                           class="form-control registration-field fcreco"
                                                           name="tracking_code"
                                                           placeholder="<?=lang($registration_field['field_name'])?> <?= $this->registration_setting->displayPlaceholderHint($registration_field["field_name"])?>"
                                                           <?php if ($registration_field['required'] == Registration_setting::REQUIRED) echo 'required';?>
                                                           value="<?=set_value('tracking_code');?>"
                                                    />
                                                </div>
                                                <?php if ($registration_field['required'] == Registration_setting::REQUIRED): ?>
                                                    <div class="fcreco-note registration-field-note hide mb20">
                                                        <p class="pl15 mb0">
                                                            <i id="affiliate-code" class="registration-field-required-icon icon-warning red f16 mr5"></i>
                                                            <?= sprintf(lang('formvalidation.required'),lang($registration_field['field_name']))?>
                                                        </p>
                                                    </div>
                                                <?php endif ?>
                                            </div>
                                        <?php endif ?>
                                    <?php break; ?>

                                    <?php case 33: # COUNTRY ?>
                                        <div class="col-md-12 col-lg-12">
                                            <div class="form-group form-inline relative <?= $required_class?>">
                                                <?=$this->registration_setting->displaySymbolHint($registration_field["field_name"])?>
                                                <div class="custom-dropdown">
                                                    <select id="country_list" class="form-control registration-field" name="<?=$registration_field['alias']?>" <?php if ($registration_field['required'] == Registration_setting::REQUIRED) echo 'required';?> onchange="Register.chosenCountry(this)">
                                                        <option value=""><?php echo lang('reg.a42');?><?= $this->registration_setting->displayPlaceholderHint($registration_field["field_name"])?></option>
                                                        <?php if(!$this->utils->isEnabledFeature('disable_frequently_use_country_in_registration')) : ?>
                                                            <optgroup label="<?=lang('lang.frequentlyUsed')?>">
                                                                <?php foreach ($this->utils->getCommonCountryList() as $key) {?>
                                                                    <option value="<?=$key?>"><?=lang('country.' . $key)?> <?= (set_value($registration_fields['Resident Country']['alias']) == $key) ? "selected" : ""; ?></option>
                                                                <?php } ?>
                                                            </optgroup>
                                                        <?php endif; ?>
                                                        <optgroup label="<?=lang('lang.alphabeticalOrder')?>">
                                                            <?php foreach ($this->utils->getCountryList() as $key) {?>
                                                                <option value="<?=$key?>" <?= (set_value($registration_fields['Resident Country']['alias']) == $key) ? "selected" : ""; ?>><?=lang('country.' . $key)?></option>
                                                            <?php } ?>
                                                        </optgroup>
                                                    </select>
                                                </div>
                                            </div>

                                            <?php if ($registration_field['required'] == Registration_setting::REQUIRED): ?>
                                                <div class="registration-field-note hide mb20">
                                                    <p class="pl15 mb0">
                                                        <i class="registration-field-required-icon icon-warning red f16 mr5"></i>
                                                        <?= sprintf(lang('formvalidation.required'),lang($registration_field['field_name']))?>
                                                    </p>
                                                </div>
                                            <?php endif ?>
                                        </div>
                                    <?php break; ?>

                                    <?php case 35: # Withdrawal Password ?>
                                        <div class="col-md-12 col-lg-12">
                                            <div class="form-group form-inline relative <?= $required_class?>">
                                                <?=$this->registration_setting->displaySymbolHint($registration_field["field_name"])?>
                                                <label><i class="icon-pass"></i></label>
                                                <input type="password" class="form-control registration-field" name="<?=$registration_field['alias']?>" placeholder="<?=lang($registration_field['field_name'])?> <?= $this->registration_setting->displayPlaceholderHint($registration_field["field_name"])?>" <?php if ($registration_field['required'] == Registration_setting::REQUIRED) echo 'required';?> value="<?=set_value($registration_field['alias'])?>"/>
                                            </div>

                                            <?php if ($registration_field['required'] == Registration_setting::REQUIRED): ?>
                                                <div class="registration-field-note hide mb20">
                                                    <p class="pl15 mb0">
                                                        <i class="registration-field-required-icon icon-warning red f16 mr5"></i>
                                                        <?= sprintf(lang('formvalidation.required'),lang($registration_field['field_name']))?>
                                                    </p>
                                                </div>
                                            <?php endif ?>
                                        </div>
                                    <?php break; ?>

                                    <?php case 37: # Region   ?>
                                    <?php case 36: # City     ?>
                                    <?php case 43: # Address  ?>
                                    <?php case 44: # Address 2?>
                                        <?php if(!$address_title): ?>
                                            <?php $address_title = TRUE;?>
                                            <div class="col-md-12 col-lg-12" id="register-address-title">
                                                <?=lang('player.59');?>
                                            </div>
                                        <?php endif;?>
                                        <?php if($full_address_in_one_row): ?>
                                            <?php $placeholder = lang('Please input your full address'); ?>
                                        <?php else: ?>
                                            <?php $placeholder = lang('a_reg.'.$registration_field['registrationFieldId'].'.placeholder');?>
                                        <?php endif;?>
                                        <div class="col-md-12 col-lg-12">
                                            <div class="form-group form-inline relative <?= $required_class?>">
                                                <?=$this->registration_setting->displaySymbolHint($registration_field["field_name"])?>
                                                <input type="text"
                                                       class="form-control registration-field custom-inline-block"
                                                       name="<?=$registration_field['alias']?>"
                                                       placeholder="<?=$placeholder?> <?= $this->registration_setting->displayPlaceholderHint($registration_field["field_name"])?>"
                                                       <?php if ($registration_field['required'] == Registration_setting::REQUIRED) echo 'required';?>
                                                       value="<?=set_value($registration_field['alias'])?>"
                                                       maxlength="120"
                                                />
                                            </div>
                                            <div class="registration-field-note hide mb20">
                                                <p class="pl15 mb0">
                                                    <i class="icon-checked green f16 mr5"></i> <?=sprintf(lang('validation.maxlength'), 120)?>
                                                </p>
                                                <?php if ($registration_field['required'] == Registration_setting::REQUIRED): ?>
                                                    <p class="pl15 mb0">
                                                        <i class="registration-field-required-icon icon-warning red f16 mr5"></i>
                                                        <?=lang('a_reg.'.$registration_field['registrationFieldId'].'.hint')?>
                                                    </p>
                                                <?php endif ?>
                                            </div>
                                        </div>
                                    <?php break; ?>

                                    <?php case 46: #agent tracking code ?>
                                        <?php if($displayAgencyCode): ?>
                                            <div class="col-md-12 col-lg-12">
                                                <div class="form-group form-inline relative <?= $required_class?>">
                                                    <?=$this->registration_setting->displaySymbolHint($registration_field["field_name"])?>
                                                    <label><i class="fa fa-qrcode"></i></label>
                                                    <input type="text"
                                                           class="form-control registration-field fcreco"
                                                           name="agent_tracking_code"
                                                           placeholder="<?=lang('a_reg.'.$registration_field['registrationFieldId'])?> <?= $this->registration_setting->displayPlaceholderHint($registration_field["field_name"])?>"
                                                           <?php if ($registration_field['required'] == Registration_setting::REQUIRED) echo 'required';?>
                                                           value="<?=set_value('agent_tracking_code');?>"
                                                    />
                                                </div>
                                                <?php if ($registration_field['required'] == Registration_setting::REQUIRED): ?>
                                                    <div class="fcreco-note registration-field-note hide mb20">
                                                        <p class="pl15 mb0">
                                                            <i id="tracking-code" class="registration-field-required-icon icon-warning red f16 mr5"></i>
                                                            <?= sprintf(lang('formvalidation.required'),lang('a_reg.'.$registration_field['registrationFieldId']))?>
                                                        </p>
                                                    </div>
                                                <?php endif ?>
                                            </div>
                                        <?php endif ?>
                                    <?php break; ?>

                                    <?php case 49: # ID Card Number ?>
                                        <div class="col-md-12 col-lg-12">
                                            <div class="form-group form-inline relative <?= $required_class?>">
                                                <?=$this->registration_setting->displaySymbolHint($registration_field["field_name"])?>
                                                <input type="text"
                                                       class="form-control registration-field"
                                                       name="<?=$registration_field['alias']?>"
                                                       placeholder="<?=lang($registration_field['field_name'])?> <?= $this->registration_setting->displayPlaceholderHint($registration_field["field_name"])?>"
                                                       <?php if ($registration_field['required'] == Registration_setting::REQUIRED) echo 'required';?>
                                                       value="<?=set_value($registration_field['alias'])?>"
                                                       onkeyup="return Register.validateIdCardNumber(this.value,<?=$registration_field['required']?>)"
                                                       maxlength="<?=$this->utils->getConfig('id_card_number_validator')['char_lenght']?>"
                                                />
                                            </div>
                                            <div class="registration-field-note hide mb20">
                                                <?php if ($registration_field['required'] == Registration_setting::REQUIRED): ?>
                                                    <p class="pl15 mb0">
                                                        <i class="registration-field-required-icon icon-warning red f16 mr5"></i>
                                                        <?= sprintf(lang('formvalidation.required'),lang($registration_field['field_name']))?>
                                                    </p>
                                                <?php endif; ?>
                                                <p class="pl15 mb0">
                                                    <i class="icon-warning red f16 mr5" id="val_id_card_num"></i>
                                                    <?= lang('Made up of letters and number only and must be 18 characters.')?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php break; ?>

                                    <?php case 9: # Instant Message 1 ?>
                                    <?php case 10: # Instant Message 2 ?>
                                    <?php case 47: # Instant Message 3 ?>
                                        <?php $im = $this->config->item($registration_field['field_name'], 'cust_non_lang_translation'); ?>
                                        <div class="col-md-12 col-lg-12">
                                            <div class="form-group form-inline relative <?= $required_class?>">
                                                <?=$this->registration_setting->displaySymbolHint($registration_field["field_name"])?>
                                                <input type="text"
                                                       class="form-control registration-field custom-inline-block"
                                                       name="<?=$registration_field['alias']?>"
                                                       placeholder="<?= ($im) ? $im : lang($registration_field['field_name'])?> <?= $this->registration_setting->displayPlaceholderHint($registration_field["field_name"])?>"
                                                       <?php if ($registration_field['required'] == Registration_setting::REQUIRED) echo 'required';?>
                                                       value="<?=set_value($registration_field['alias'])?>"
                                                />
                                            </div>

                                            <?php if ($registration_field['required'] == Registration_setting::REQUIRED): ?>
                                            <div class="registration-field-note hide mb20">
                                                <p class="pl15 mb0">
                                                    <i class="registration-field-required-icon icon-warning red f16 mr5"></i>
                                                    <?= sprintf(lang('formvalidation.required'), (($im) ? $im : lang('a_reg.'.$registration_field['registrationFieldId'])))?>
                                                </p>
                                            </div>
                                            <?php endif ?>
                                        </div>
                                    <?php break; ?>

                                    <?php default: ?>
                                        <div class="col-md-12 col-lg-12">
                                            <div class="form-group form-inline relative <?= $required_class?>">
                                                <?php $fieldName = $this->config->item($registration_field['field_name'], 'cust_non_lang_translation'); ?>
                                                <?=$this->registration_setting->displaySymbolHint($registration_field["field_name"])?>
                                                <input type="text"
                                                       class="form-control registration-field custom-inline-block"
                                                       name="<?=$registration_field['alias']?>"
                                                       placeholder="<?= ($fieldName) ? $fieldName : lang('a_reg.'.$registration_field['registrationFieldId'])?> <?= $this->registration_setting->displayPlaceholderHint($registration_field["field_name"])?>"
                                                       <?php if ($registration_field['required'] == Registration_setting::REQUIRED) echo 'required';?>
                                                       value="<?=set_value($registration_field['alias'])?>"
                                                />
                                            </div>
                                            <?php if ($registration_field['required'] == Registration_setting::REQUIRED): ?>
                                                <div class="registration-field-note hide mb20">
                                                    <p class="pl15 mb0">
                                                        <i class="registration-field-required-icon icon-warning red f16 mr5"></i>
                                                        <?= sprintf(lang('formvalidation.required'),(($fieldName) ? $fieldName : lang('a_reg.'.$registration_field['registrationFieldId'])))?>
                                                    </p>
                                                </div>
                                            <?php endif ?>
                                        </div>
                                    <?php break; ?>
                                <?php } ?>
                            <?php endif ?>
                        <?php endforeach ?>

                        <?php if ($this->operatorglobalsettings->getSettingJson('registration_captcha_enabled')):?>
                            <div class="col-md-12 col-lg-12">
                                <div class="form-group form-inline relative">
                                    <label><i class="glyphicon glyphicon-qrcode"></i></label>
                                    <input type="text"
                                           required
                                           class="form-control registration-field fcrecaptcha"
                                           name='captcha'
                                           id='captcha'
                                           placeholder="<?php echo lang('label.captcha'); ?> <?=$require_placeholder_text?>"
                                           maxlength="4"
                                           style="width:60%"
                                           oninput="return Register.validateVerificodeLength(this.value)"
                                    />
                                    <i class="fa fa-refresh" style="cursor:pointer; float: right; font-size: 1.4em; color: #888; margin-left: 3px; " aria-hidden="true" onclick="refreshCaptcha()"></i>
                                    <img class="captcha" id='image_captcha' src='<?php echo site_url('/iframe/auth/captcha?' . random_string('alnum')); ?>' onclick="refreshCaptcha()">
                                </div>
                                <div class="fcrecaptcha-note registration-field-note hide mb20">
                                    <p class="pl15 mb0">
                                        <i id="verifi_code_len" class="icon-warning red f16 mr5"></i>
                                        <?=lang('captcha.required')?>
                                    </p>
                                </div>
                            </div>
                       <?php endif; ?>
                    </div>

                    <div class="error-message <?=(! empty($this->session->userdata('result'))) ? '' : 'hide'?>">
                        <?=$this->session->userdata('message')?>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <?=lang('Register Now'); ?>
                    </button>

                    <?php // TERMS AND CONDITIONS ?>
                    <?php if ($registration_fields['At Least 18 Yrs. Old and Accept Terms and Conditions']['visible'] == Registration_setting::VISIBLE): ?>
                        <div class="checkbox pl10 pr10">
                            <input type="checkbox"
                                   name="terms"
                                   id="terms"
                                   <?=set_value('terms', $this->CI->config->item('terms', 'player_form_registration')) ? 'checked="checked"' : ''?>
                                   value="1"
                                   <?php if ($registration_fields['At Least 18 Yrs. Old and Accept Terms and Conditions']['required'] == Registration_setting::REQUIRED) echo 'required';?>
                            />
                            <label for="terms" class="lh24">
                                <?=$this->utils->renderLang('register.18age.hint', ["$web_user_terms_url","$web_privacy_policy_url"]);?>
                            </label>
                        </div>
                    <?php endif ?>

                    <p class="pl10 pr10 pt20">
                        <?= lang('Already have account'); ?>, <a href="<?=site_url('iframe/auth/login')?>"><?= lang('Please Login'); ?></a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    // export sys feature block_emoji_chars_in_real_name_field, OGP-12268
    var sys_feature_block_emoji = <?= $this->utils->isEnabledFeature('block_emoji_chars_in_real_name_field') ? 'true' : 'false' ?>;
</script>
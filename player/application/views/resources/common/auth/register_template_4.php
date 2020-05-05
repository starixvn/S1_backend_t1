<?php if (isset($preview) && $preview): ?>
    <style type="text/css">
        .mt100 {
            margin-top: 0 !important;
        }
    </style>
<?php endif ?>
<div  class="container">
    <div class="cstm-mod registration-mod" role="document">
        <div class="modal-content">

            <div class="modal-header text-center">
                <h4 class="modal-title f24" id="myModalLabel"><?php echo lang('Register Your Account'); ?></h4>
                <a href="iframe/auth/login" class="logintext"><?php echo lang('Login Now');?>!</a>
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
                        <input type="hidden" name="btag" value="<?=$btag?>" />
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6 col-lg-4">
                            <?php // USERNAME ?>
                            <div class="form-group form-inline relative field_required">
                                <?= $require_display_symbol?>
                                <label><i class="icon-user"></i></label>
                                <input type="text" class="form-control registration-field fcname" name="username" id="username" placeholder="<?php echo lang('Username');?> <?=$require_placeholder_text?>" onKeyUp="Register.lowerCase(this)" onfocus="return Register.validateUsernameRequirements(this.value)"  oninput="return Register.validateUsernameRequirements(this.value)" value="<?=set_value('username')?>">
                            </div>
                            <div class="fcname-note registration-field-note hide mb20">
                                <p class="pl15 mb0"><i id="username_len" class="icon-warning red f16 mr5"></i> <?php echo sprintf(lang('username_char_limit'),$min_username_length.'-'.$max_username_length); ?></p>
                                <?php if (!empty($this->utils->isRestrictUsernameEnabled())) : ?>
                                    <p class="pl15 mb0"><i id="username_charcombi" class="icon-warning red f16 mr5"></i> <?=lang('validation.validateUsername01')?></p>
                                <?php else: ?>
                                    <p class="pl15 mb0"><i id="username_charcombi" class="icon-warning red f16 mr5"></i> <?=lang('validation.validateUsername02')?></p>
                                <?php endif; ?>
                                <p id="username_exist_failed" class="pl15"><i class="icon-warning red f16 mr5"></i> <?=lang('validation.availabilityUsername_2')?></p>
                                <p id="username_exist_available" class="pl15" style="display: none;"><i class="icon-checked green f16 mr5"></i> <?=lang('validation.availabilityUsername_available')?></p>
                            </div>

                            <?php // PASSWORD ?>
                            <div class="form-group form-inline relative field_required">
                                <?= $require_display_symbol?>
                                <label><i class="icon-pass"></i></label>
                                <input type="password" class="form-control registration-field fcpass" name="password" id="password" onfocus="return Register.validatePasswordRequirements(this.value)"  oninput="return Register.validatePasswordRequirements(this.value)" placeholder="<?php echo lang('Password'); ?> <?=$require_placeholder_text?>" value="<?=set_value('password')?>">
                            </div>
                            <div class="fcpass-note registration-field-note hide mb20">
                                <p class="pl15 mb0"><i id="password_len" class="icon-warning red f16 mr5"></i> <?= sprintf(lang('validation.lengthPassword'),'6-20')?></p>
                                <?php if (!empty($this->utils->isRestrictPasswordEnabled())) : ?>
                                    <p class="pl15 mb0"><i id="password_regex" class="icon-warning red f16 mr5"></i> <?=lang('validation.contentPassword01')?></p>
                                <?php else: ?>
                                    <p class="pl15 mb0"><i id="password_regex" class="icon-warning red f16 mr5"></i> <?=lang('validation.contentPassword03')?></p>
                                <?php endif; ?>
                                <p class="pl15"><i id="password_not_username" class="icon-warning red f16 mr5"></i> <?=lang('validation.contentPassword02')?></p>
                            </div>

                            <?php // CONFIRM PASSWORD ?>
                            <div class="form-group form-inline relative field_required">
                                <?= $require_display_symbol?>
                                <label><i class="icon-pass"></i></label>
                                <input type="password" class="form-control registration-field fccpass" name="cpassword" onfocus="return Register.validateConfirmPassword(this.value)"  oninput="return Register.validateConfirmPassword(this.value)" placeholder="<?php echo lang('Confirm Password'); ?> <?=$require_placeholder_text?>" value="<?=set_value('cpassword')?>">
                            </div>
                            <div class="fccpass-note registration-field-note hide mb20">
                                <p class="pl15 mb0"><i id="cpassword_reenter" class="icon-warning red f16 mr5"></i><span id="cpassword_reenter_msg"><?=lang('validation.retypePassword')?></span></p>
                            </div>

                            <?php // EMAIL ?>
                            <?php if ($registration_fields['Email']['visible'] == Registration_setting::VISIBLE): ?>
                            <div class="form-group form-inline relative <?php if ($registration_fields['Email']['required'] == Registration_setting::REQUIRED) echo 'field_required';?> ">
                                <?php if ( $registration_fields['Email']['required'] == Registration_setting::REQUIRED ) {?>
                                <?= $this->registration_setting->displaySymbolHint($registration_fields['Email']["field_name"])?>
                                <?php }?>
                                <label><i class="glyphicon glyphicon-envelope"></i></label>
                                <input type="text" class="form-control registration-field fcemail" name="email" id="email" placeholder="<?php echo lang('Email Address');?> <?= $this->registration_setting->displayPlaceholderHint($registration_fields['Email']["field_name"])?>" value="<?=set_value('email')?>" onfocus="return Register.validateEmail(this.value)" oninput="return Register.validateEmail(this.value)">
                            </div>
                            <div class="fcemail-note registration-field-note hide mb20">
                                <p class="pl15 mb0"><i id="email_required" class="registration-field-required-icon icon-warning red f16 mr5"></i> <?=lang('validation.requiredEmail')?></p>
                            </div>

                            <?php endif; ?>

                            <?php // Contact Number ?>
                            <?php if ($registration_fields['Contact Number']['visible'] == Registration_setting::VISIBLE): ?>
                                <div class="form-group form-inline relative field_required mobile-field">
                                    <div class="mobile-field-container">
                                        <div class="mfc-box">
                                    <?php if ( $registration_fields['Contact Number']['required'] == Registration_setting::REQUIRED ):?>
                                            <?= $this->registration_setting->displaySymbolHint($registration_fields['Contact Number']["field_name"])?>
                                    <?php endif;?>
                                            <label><i class="glyphicon glyphicons-iphone"></i></label>
                                            <input type="text" class="form-control registration-field" name="contactNumber" id="contactNumber" placeholder="<?php echo lang('Mobile Number');?> <?= $this->registration_setting->displayPlaceholderHint($registration_fields['Contact Number']["field_name"])?>" value="<?=set_value('contactNumber')?>"
                                            <?php if ($contactRule) : ?> data-rule="<?= htmlentities(json_encode($contactRule)); ?>" maxlength="<?php echo $contactRule['max']; ?>" <?php endif; ?>
                                            onfocus="return Register.validateContactNumber(this.value, this.getAttribute('data-rule'))" oninput="return Register.validateContactNumber(this.value, this.getAttribute('data-rule'))" required>
                                        </div>

                                    <?php if ($showSMSField): ?>
                                        <div class="mfc-box">
                                            <button type="button" id="send_sms_verification" class="btn btn-success">
                                                <?=lang('Send SMS')?>
                                            </button>
                                        </div>
                                     <?php endif;?>
                                    </div>
                                </div>
                                <div class="registration-field-note hide mb20 contact-field-note">
                                    <p class="pl15 mb0">
                                        <i id="mobile_format" class="icon-warning red f16 mr5"></i>
                                        <span class="validate-mesg format"><?=lang('validation.validateContactNumber')?></span>
                                        <span class="validate-mesg in-use" style="display: none;"><?=lang('The number is in use')?></span>
                                    </p>

                                    <?php if ($contactRule) : ?>
                                    <?php if (isset($contactRule['min']) && isset($contactRule['max']) && $contactRule['min'] != @$contactRule['max'] ) : ?>
                                        <p class="pl15 mb0"><i id="contact_len_between" class="icon-warning red f16 mr5"></i> <?php echo sprintf(lang('validation.lengthStandard'),$contactRule['min'].'-'.$contactRule['max']);?></p>
                                    <?php else: ?>
                                        <?php if (isset($contactRule['min']) && $contactRule['min'] != @$contactRule['max']) : ?>
                                        <p class="pl15 mb0"><i id="contact_len_min" class="icon-warning red f16 mr5"></i> <?php echo sprintf(lang('validation.lengthTooShortStart'), $contactRule['min']);?></p>
                                        <?php endif; ?>
                                        <?php if (isset($contactRule['max']) && $contactRule['max'] != @$contactRule['min']) : ?>
                                        <p class="pl15 mb0"><i id="contact_len_max" class="icon-warning red f16 mr5"></i> <?php echo sprintf(lang('validation.lengthTooLongStart'),$contactRule['max']);?></p>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if (isset($contactRule['min'], $contactRule['max']) && $contactRule['min'] == $contactRule['max']) : ?>
                                    <p class="pl15 mb0"><i id="contact_len_same" class="icon-warning red f16 mr5"></i> <?php echo sprintf(lang('validation.lengthStandard'),$contactRule['min']);?></p>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <div class="fcmonu-note mb20 msg-container" style="display:none">
                                    <p class="pl15"><i class="icon-warning red f16 mr5"></i><span id="sms_verification_msg"></span></p>
                                </div>
                            <?php endif; ?>
                            <input type="hidden" name="language" value="<?= ucfirst($this->language_function->getCurrentLanguageName()) ?>">
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <?php // REGISTRATION FIELDS ?>

                            <div class="fullname">
                                <?php // First Name ?>
                                <?php if ($registration_fields['First Name']['visible'] == Registration_setting::VISIBLE): ?>
                                    <div class="form-group form-inline relative field_required">
                                        <?php if ( $registration_fields['First Name']['required'] == Registration_setting::REQUIRED ) {?><?= $this->registration_setting->displaySymbolHint($registration_fields['First Name']["field_name"])?><?php }?>
                                        <label><i class="icon-info"></i></label>
                                        <input type="text" class="form-control registration-field" name="<?=$registration_fields['First Name']['alias']?>" data-rule="<?= htmlentities(json_encode($firstNameRule)); ?>" placeholder="<?= lang('a_reg.1') ?> <?= $this->registration_setting->displayPlaceholderHint($registration_fields['First Name']["field_name"])?>"
                                            <?php if ($registration_fields['First Name']['required'] == Registration_setting::REQUIRED) echo 'required';?> value="<?=set_value($registration_fields['First Name']['alias'])?>" onfocus="return Register.validateFirstName(this, this.getAttribute('data-rule'))"  oninput="return Register.validateFirstName(this, this.getAttribute('data-rule'))"/>
                                    </div>
                                    <div class="registration-field-note hide mb20">
                                        <?php if ($registration_fields['First Name']['required'] == Registration_setting::REQUIRED): ?>
                                        <p class="pl15 mb0">
                                            <i class="registration-field-required-icon icon-warning red f16 mr5"></i>
                                            <?= lang('validation.realname')?>
                                        </p>
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
                                <?php endif; ?>

                                <?php // Last Name ?>
                                <?php if ($registration_fields['Last Name']['visible'] == Registration_setting::VISIBLE): ?>
                                    <div class="form-group form-inline relative field_required">
                                        <?php if ( $registration_fields['Last Name']['required'] == Registration_setting::REQUIRED ) {?><?= $this->registration_setting->displaySymbolHint($registration_fields['Last Name']["field_name"])?><?php }?>
                                        <input type="text" class="form-control registration-field" name="<?=$registration_fields['Last Name']['alias']?>" placeholder="<?= lang('a_reg.'.$registration_fields['Last Name']['registrationFieldId']) ?> <?= $this->registration_setting->displayPlaceholderHint($registration_fields['Last Name']["field_name"])?>" <?php if ($registration_fields['Last Name']['required'] == Registration_setting::REQUIRED) echo 'required';?> value="<?=set_value($registration_fields['Last Name']['alias'])?>" onfocus="return Register.validateLastName(this)"  oninput="return Register.validateLastName(this)"/>
                                    </div>

                                    <div class="registration-field-note hide mb20">
                                        <?php if ($registration_fields['Last Name']['required'] == Registration_setting::REQUIRED): ?>
                                        <p class="pl15 mb0">
                                            <i class="registration-field-required-icon icon-warning red f16 mr5"></i>
                                            <?= sprintf(lang('formvalidation.required'),lang('a_reg.'.$registration_fields['Last Name']['registrationFieldId']))?>
                                        </p>
                                        <?php endif ?>
                                        <p class="pl15">
                                            <i id="lastNameRegex" class="icon-warning red f16 mr5"></i>
                                            <?= lang('validation.lastNameRegex')?>
                                        </p>
                                    </div>
                                <?php endif; ?>
                            </div><!-- /fullname -->

                            <?php // BIRTHDAY ?>
                            <?php if ($registration_fields['Birthday']['visible'] == Registration_setting::VISIBLE): ?>
                                <?php
                                    // lowest year wanted
                                    $cutoff = 1900;
                                    // current year
                                    $now = date('Y');
                                ?>
                                    <div class="form-group form-inline relative birthday-option field_required">
                                    <?php if ( $registration_fields['Birthday']['required'] == Registration_setting::REQUIRED ) {?><?= $this->registration_setting->displaySymbolHint($registration_fields['Birthday']["field_name"])?><?php }?>
                                        <input type="hidden" class="registration-field" name="<?=$registration_fields['Birthday']['alias']?>" value="<?=set_value($registration_fields['Birthday']['alias']) ?>" <?php if ($registration_fields['Birthday']['required'] == Registration_setting::REQUIRED) echo 'required';?>>
                                        <?php
                                            $birth = set_value($registration_fields['Birthday']['alias']);
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
                                                <span class="custom-label"><?=lang($registration_fields['Birthday']['field_name'])?> <?= $this->registration_setting->displayPlaceholderHint($registration_fields['Birthday']["field_name"])?></span>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="custom-dropdown">
                                                    <div class="cutom-dropdown-ul" id="dropdown_year">
                                                        <input type="hidden" id="year" name="year" onchange="Register.validateDOB_dropdowns()" <?php if ($registration_fields['Birthday']['required'] == Registration_setting::REQUIRED) echo 'required';?>>
                                                        <span><?= lang('reg.14') ?></span>
                                                        <ul class="dropdown">
                                                            <?php $now = date('Y'); ?>
                                                            <?php for($y = ($now - $this->utils->getConfig('legal_age')); $y >= $cutoff; $y--): ?>
                                                                <li><a href="javascript:void(0)"><?=$y?></a></li>
                                                            <?php endfor?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 lr-padding">
                                                <div class="custom-dropdown">
                                                    <div class="cutom-dropdown-ul" id="dropdown_month">
                                                        <input type="hidden" id="month" name="month" onchange="Register.validateDOB_dropdowns()" <?php if ($registration_fields['Birthday']['required'] == Registration_setting::REQUIRED) echo 'required';?>>
                                                        <span><?= lang('reg.59') ?></span>
                                                        <ul class="dropdown">
                                                            <?php for($m = 1; $m <= 12; $m++): ?>
                                                                <li><a href="javascript:void(0)"><?=sprintf("%02d",$m)?></a></li>
                                                            <?php endfor?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 lr-padding">
                                                <div class="custom-dropdown">
                                                    <div class="cutom-dropdown-ul" id="dropdown_day">
                                                        <input type="hidden" id="day" name="day" onchange="Register.validateDOB_dropdowns()" <?php if ($registration_fields['Birthday']['required'] == Registration_setting::REQUIRED) echo 'required';?>>
                                                        <span><?= lang('reg.13') ?></span>
                                                        <ul class="dropdown">
                                                            <?php if (isset($_dayOfMonth)) : ?>
                                                                <?php if (isset($_dayOfMonth)) : ?>
                                                                    <?php for($d = 1; $d <= $_dayOfMonth; $d++): ?>
                                                                        <li><a href="#"><?=sprintf("%02d",$d)?></a></li>
                                                                    <?php endfor?>
                                                                <?php endif; ?>
                                                            <?php endif; ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <?php if ($registration_fields['Birthday']['required'] == Registration_setting::REQUIRED): ?>
                                        <div class="registration-field-note hide mb20">
                                                <p class="pl15 mb0">
                                                    <i class="registration-field-required-icon icon-warning red f16 mr5" id="val_dob"></i>
                                                    <?= lang('The Birthday field is required.')?>
                                                </p>
                                                <p class="pl15 mb0">
                                                    <i class="registration-field-required-icon icon-warning red f16 mr5" id="val_dob18"></i>
                                                    <?= lang('You must be 18 years old and above to sign up.')?>
                                                </p>
                                        </div>
                                    <?php endif ?>
                            <?php endif; ?>

                            <?php // SECURITY ?>
                            <?php $securityList = ['reg.37', 'reg.38', 'reg.39', 'reg.40', 'reg.41'] ?>
                            <?php if ($registration_fields['Security Question']['visible'] == Registration_setting::VISIBLE): ?>
                            <div class="securityqa">
                                <div class="form-group form-inline relative field_required">
                                <?php if ( $registration_fields['Security Question']['required'] == Registration_setting::REQUIRED ) {?><?= $this->registration_setting->displaySymbolHint($registration_fields['Security Question']["field_name"])?><?php }?>
                                    <?php if($registration_fields['Security Question']['required'] == Registration_setting::REQUIRED): ?>
                                        <label><i class="icon-question"></i></label>
                                    <?php endif; ?>
                                    <div class="cutom-dropdown-ul" id="dropdown_secret_question">
                                        <input type="hidden" id="<?=$registration_fields['Security Question']['alias']?>" name="<?=$registration_fields['Security Question']['alias']?>">
                                        <span><?php echo lang('a_reg.'.$registration_fields['Security Question']['registrationFieldId']);?><?= $this->registration_setting->displayPlaceholderHint($registration_fields['Security Question']["field_name"])?></span>
                                        <ul class="dropdown">
                                            <?php foreach($securityList as $val) : ?>
                                                <li><a href="javascript:void(0)"><?=lang($val)?></a></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>

                                <?php if ($registration_fields['Security Question']['required'] == Registration_setting::REQUIRED): ?>
                                    <div class="registration-field-note hide mb20">
                                        <p class="pl15 mb0">
                                            <i class="registration-field-required-icon icon-warning red f16 mr5"></i>
                                            <?= lang('The Security Question field is required.')?>
                                        </p>
                                    </div>
                                <?php endif ?>
                                <div class="form-group form-inline relative field_required">
                                    <?php if ( $registration_fields['Security Question']['required'] == Registration_setting::REQUIRED ) {?><?= $this->registration_setting->displaySymbolHint($registration_fields['Security Question']["field_name"])?><?php }?>
                                    <label><i class="icon-check2"></i></label>
                                    <input type="text" class="form-control registration-field" name="secretAnswer" placeholder="<?php echo lang('forgot.06');?> <?= $this->registration_setting->displayPlaceholderHint($registration_fields['Security Question']["field_name"])?>" <?php if ($registration_fields['Security Question']['required'] == Registration_setting::REQUIRED) echo 'required';?> <?php if ($registration_fields['Security Question']['required'] == Registration_setting::REQUIRED) echo 'required';?> value="<?=set_value('secretAnswer')?>"/>
                                </div>
                                <?php if ($registration_fields['Security Question']['required'] == Registration_setting::REQUIRED): ?>
                                    <div class="registration-field-note hide mb20">
                                        <p class="pl15 mb0">
                                            <i class="registration-field-required-icon icon-warning red f16 mr5"></i>
                                            <?= lang('The Security Question Answer field is required.')?>
                                        </p>
                                    </div>
                                <?php endif ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <?php // from Address -> Region  due to #Migration_update_address_on_registration_fields_20200120  ?>
                            <?php if ($registration_fields['Region']['visible'] == Registration_setting::VISIBLE): ?>
                                <div class="form-group form-inline relative field_required">
                                    <?php if ( $registration_fields['Region']['required'] == Registration_setting::REQUIRED ) {?><?= $this->registration_setting->displaySymbolHint($registration_fields['Region']["field_name"])?><?php }?>
                                    <label><i class="icon-address"></i></label>
                                    <input type="text" class="form-control registration-field" name="<?=$registration_fields['Region']['alias']?>" placeholder="<?= lang('player.59') ?> <?= $this->registration_setting->displayPlaceholderHint($registration_fields['Region']["field_name"])?>" <?php if ($registration_fields['Region']['required'] == Registration_setting::REQUIRED) echo 'required';?> value="<?=set_value($registration_fields['Region']['alias'])?>"/>
                                </div>
                                <?php if ($registration_fields['Region']['required'] == Registration_setting::REQUIRED): ?>
                                    <div class="registration-field-note hide mb20">
                                        <p class="pl15 mb0">
                                            <i class="registration-field-required-icon icon-warning red f16 mr5"></i>
                                            <?= lang('The address field is required.')?>
                                        </p>
                                    </div>
                                <?php endif ?>
                            <?php endif; ?>


                            <?php // Country ?>
                            <?php if ($registration_fields['Resident Country']['visible'] == Registration_setting::VISIBLE): ?>
                                <div class="form-group form-inline relative field_required resident_country_option">
                                    <?php if ( $registration_fields['Resident Country']['required'] == Registration_setting::REQUIRED ) {?><?= $this->registration_setting->displaySymbolHint($registration_fields['Resident Country']["field_name"])?><?php }?>
                                    <label><i class="icon-address"></i></label>
                                    <div class="cutom-dropdown-ul" id="dropdown_country">
                                        <input type="hidden" class="registration-field" id="<?=$registration_fields['Resident Country']['alias']?>" name="<?=$registration_fields['Resident Country']['alias']?>" onchange="Register.chosenCountry(this)" <?php if ($registration_fields['Resident Country']['required'] == Registration_setting::REQUIRED) echo 'required';?>>
                                        <span><?php echo lang('reg.a42');?><?= $this->registration_setting->displayPlaceholderHint($registration_fields['Resident Country']["field_name"])?></span>
                                        <ul class="dropdown">
                                            <?php if(!$this->utils->isEnabledFeature('disable_frequently_use_country_in_registration')) : ?>
                                                <li class="dtitle" disabled="disabled"><a href="#"><?=lang('lang.frequentlyUsed')?></a></li>
                                                <?php foreach ($this->utils->getCommonCountryList() as $key) {?>
                                                    <li><a href="#"><?=lang('country.' . $key)?></a></li>
                                                <?php } ?>
                                            <?php endif; ?>
                                            <li class="dtitle" disabled="disabled"><a href="#"><?=lang('lang.alphabeticalOrder')?></a></li>
                                            <?php foreach ($this->utils->getCountryList() as $key) {?>
                                                 <li><a href="#"><?=lang('country.' . $key)?></a></li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>

                                <?php if ($registration_fields['Resident Country']['required'] == Registration_setting::REQUIRED): ?>
                                    <div class="registration-field-note countryField hide mb20">
                                        <p class="pl15 mb0">
                                            <i class="registration-field-required-icon icon-warning red f16 mr5"></i>
                                            <?= lang('The resident country field is required.')?>
                                        </p>
                                    </div>
                                <?php endif ?>
                            <?php endif; ?>

                            <?php if ($showSMSField): ?>

                                <div class="form-group form-inline relative field_required">
                                    <?=$this->registration_setting->displaySymbolHint($registration_fields['SMS Verification Code']["field_name"])?>
                                    <label><i class="glyphicon glyphicons-iphone"></i></label>
                                    <input type="text" class="form-control registration-field" name="sms_verification_code" placeholder="<?=lang($registration_fields['SMS Verification Code']['field_name'])?> <?= $this->registration_setting->displayPlaceholderHint($registration_fields['SMS Verification Code']["field_name"])?>" <?php if ($registration_fields['SMS Verification Code']['required'] == Registration_setting::REQUIRED) echo 'required';?> value="<?=set_value('sms_verification_code')?>"/>
                                </div>

                                <?php if ($registration_fields['SMS Verification Code']['required'] == Registration_setting::REQUIRED): ?>
                                    <div class="registration-field-note hide mb20">
                                        <p class="pl15 mb0">
                                            <i class="registration-field-required-icon icon-warning red f16 mr5"></i>
                                            <?= sprintf(lang('formvalidation.required'),lang($registration_fields['SMS Verification Code']['field_name']))?>
                                        </p>
                                    </div>
                                <?php endif ?>

                            <?php endif ?>

                            <?php // ID CARD NUMBER ?>
                            <?php
                              $feature_show_player_upload_realname_verification = $this->utils->isEnabledFeature('show_player_upload_realname_verification');
                            ?>
                            <?php if (isset($registration_fields['ID Card Number']) == 49 && $feature_show_player_upload_realname_verification) :?>
                                <?php if ($registration_fields['ID Card Number']['visible'] == Registration_setting::VISIBLE): ?>
                                    <div class="form-group form-inline relative field_required">
                                        <?php if ( $registration_fields['ID Card Number']['required'] == Registration_setting::REQUIRED ) {?><?= $this->registration_setting->displaySymbolHint($registration_fields['ID Card Number']["field_name"])?><?php }?>
                                        <label><i class="icon-address"></i></label>
                                        <input type="text" class="form-control registration-field" name="<?=$registration_fields['ID Card Number']['alias']?>" placeholder="<?= lang('a_reg.'.$registration_fields['ID Card Number']['registrationFieldId']) ?> <?= $this->registration_setting->displayPlaceholderHint($registration_fields['ID Card Number']["field_name"])?>" <?php if ($registration_fields['ID Card Number']['required'] == Registration_setting::REQUIRED) echo 'required';?> value="<?=set_value($registration_fields['ID Card Number']['alias'])?>"/>
                                    </div>
                                    <?php if ($registration_fields['ID Card Number']['required'] == Registration_setting::REQUIRED): ?>
                                        <div class="registration-field-note hide mb20">
                                            <p class="pl15 mb0">
                                                <i class="registration-field-required-icon icon-warning red f16 mr5"></i>
                                                <?= sprintf(lang('formvalidation.required'),lang($registration_fields['ID Card Number']['field_name']))?>
                                            </p>
                                        </div>
                                    <?php endif ?>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php // Referral - Invitation code from player?>
                            <?php if ($registration_fields['Referral Code']['visible'] == Registration_setting::VISIBLE): ?>
                            <?php if ($displayReferralCode && empty($referral_code)) : ?>
                                <div class="form-group form-inline relative field_required">
                                    <?php if ( $registration_fields['Referral Code']['required'] == Registration_setting::REQUIRED ) {?><?= $this->registration_setting->displaySymbolHint($registration_fields['Referral Code']["field_name"])?><?php }?>
                                    <label><i class="icon-referral"></i></label>
                                    <input type="text" class="form-control registration-field"
                                        name="<?=$registration_fields['Referral Code']['alias']?>"
                                        placeholder="<?= lang('a_reg.'.$registration_fields['Referral Code']['registrationFieldId']) ?> <?= $this->registration_setting->displayPlaceholderHint($registration_fields['Referral Code']["field_name"])?>"
                                        <?php if ($registration_fields['Referral Code']['required'] == Registration_setting::REQUIRED) echo 'required';?>
                                        value="<?=set_value($registration_fields['Referral Code']['alias'])?>"/>
                                </div>
                                <?php if ($registration_fields['Referral Code']['required'] == Registration_setting::REQUIRED): ?>
                                    <div class="registration-field-note hide mb20">
                                        <p class="pl15 mb0">
                                            <i class="registration-field-required-icon icon-warning red f16 mr5"></i>
                                            <?= sprintf(lang('formvalidation.required'),lang($registration_fields['Referral Code']['field_name']))?>
                                        </p>
                                    </div>
                                <?php endif ?>
                            <?php endif ?>
                            <?php endif ?>

                            <?php // Affiliate - Invitation code from affiliate
                            if($registration_fields['Affiliate Code']['visible'] == Registration_setting::VISIBLE) : ?>
                                <div class="form-group form-inline relative field_required">
                                    <?php if ( $registration_fields['Affiliate Code']['required'] == Registration_setting::REQUIRED ) {?><?= $this->registration_setting->displaySymbolHint($registration_fields['Affiliate Code']["field_name"])?><?php }?>
                                    <label><i class="icon-affiliate2"></i></label>
                                    <input type="text" class="form-control registration-field"
                                        name="<?=$registration_fields['Affiliate Code']['alias']?>"
                                        placeholder="<?= lang("reg.62") ?> <?= $this->registration_setting->displayPlaceholderHint($registration_fields['Affiliate Code']["field_name"])?>"
                                        <?php if ($registration_fields['Affiliate Code']['required'] == Registration_setting::REQUIRED) echo 'required';?>
                                        value="<?=set_value($registration_fields['Affiliate Code']['alias'])?>"/>
                                </div>
                                <?php if ($registration_fields['Affiliate Code']['required'] == Registration_setting::REQUIRED): ?>
                                    <div class="registration-field-note hide mb20">
                                        <p class="pl15 mb0">
                                            <i class="registration-field-required-icon icon-warning red f16 mr5"></i>
                                            <?= sprintf(lang('formvalidation.required'),lang($registration_fields['Affiliate Code']['field_name']))?>
                                        </p>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php // CAPTCHA ?>
                            <?php if ($this->operatorglobalsettings->getSettingJson('registration_captcha_enabled')):?>
                                <div class="form-group form-inline relative">
                                    <label><i class="glyphicon glyphicon-qrcode"></i></label>
                                    <input required name='captcha' id='captcha' type="text" class="form-control registration-field fcrecaptcha" placeholder="<?php echo lang('label.captcha'); ?> <?=$require_placeholder_text?>" maxlength="4" style="width: 60%">
                                    <i class="fa fa-refresh" style="cursor:pointer; float: right; font-size: 1.4em; color: #888; margin-left: 3px; " aria-hidden="true" onclick="refreshCaptcha()"></i>
                                    <img class="captcha" id='image_captcha' src='<?php echo site_url('/iframe/auth/captcha?' . random_string('alnum')); ?>' onclick="refreshCaptcha()" />
                                </div>
                                <div class="fcrecaptcha-note registration-field-note hide mb20">
                                    <p class="pl15 mb0">
                                        <i id="captcha_code" class="registration-field-required-icon icon-warning red f16 mr5"></i>
                                        <?=lang('captcha.required')?>
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php // TERMS AND CONDITIONS ?>
                            <?php if ($registration_fields['At Least 18 Yrs. Old and Accept Terms and Conditions']['visible'] == Registration_setting::VISIBLE): ?>
                            <div class="col-md-12 col-lg-12">
                                <div class="checkbox">
                                    <input type="checkbox" name="terms" id="terms" <?=set_value('terms', $this->CI->config->item('terms', 'player_form_registration')) ? 'checked="checked"' : ''?> value="1" <?php if ($registration_fields['At Least 18 Yrs. Old and Accept Terms and Conditions']['required'] == Registration_setting::REQUIRED) echo 'required';?>
                                        style="position:relative;margin-left:0"
                                    />
                                    <label for="terms" class="lh24">
                                        <?php echo $this->utils->renderLang('register.18age.hint', ["$web_user_terms_url","$web_privacy_policy_url"]);?>
                                    </label>
                                </div>
                            </div>
                            <?php endif ?>
                        <div class="col-md-12 col-lg-12">
                            <button type="submit" class="btn btn-primary reg-btn"><i class="icon-check3"></i> <?=lang('Register Now'); ?></button>
                        </div>

                    </div>

                    <div class="error-message <?=(! empty($this->session->userdata('result'))) ? '' : 'hide'?>">
                        <?=$this->session->userdata('message')?>
                    </div>


                    <?php if($this->utils->isEnabledFeature('register_page_show_login_link')) : ?>
                    <p class="pt10 mb0">
                        <?= $this->utils->renderLang('register_page_login_link', site_url('iframe/auth/login')) ?>
                    </p>
                    <?php endif; ?>
                </form>
            </div>

        </div>
    </div>
    <div class="goback-home">
        <a href="<?=$this->utils->getSystemUrl('www')?>" class="goback-home-link"><i class="icon-home"></i> <?= lang('Go Back to Homepage') ?></a>
    </div>
</div>
<!-- Scroll -->
<script type="text/javascript" src="<?=$this->utils->getPlayerCmsUrl(base_url() . $this->utils->getPlayerCenterTemplate().'/js/perfect-scrollbar.js')?>"></script>

<script>
// ================== ScrollBar ================== //
$('.dropdown').each(function(){ const ps = new PerfectScrollbar($(this)[0]); });
// ================== End of ScrollBar ================== //

// export sys feature block_emoji_chars_in_real_name_field, OGP-12268
var sys_feature_block_emoji = <?= $this->utils->isEnabledFeature('block_emoji_chars_in_real_name_field') ? 'true' : 'false' ?>;
</script>

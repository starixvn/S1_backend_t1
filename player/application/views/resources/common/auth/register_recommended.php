<?php if (isset($preview) && $preview): ?>
    <style type="text/css">
        .mt100 {
            margin-top: 0 !important;
        }
    </style>
<?php endif ?>

<div  class="container mt100">
    <div class="cstm-mod registration-mod" role="document" style="max-width: 100% !important;">
        <div class="modal-content">

            <div class="modal-header text-center">
                <h4 class="modal-title f24" id="myModalLabel"><?php echo lang('Register Your Account'); ?></h4>
            </div>

            <div class="modal-body">

                <form action="<?=site_url('player_center/postRegisterPlayer')?>" method="post" id="registration_form">
                    <?php if(!$displayAffilateCode && (!empty($tracking_code) || !empty($tracking_source_code))): ?>
                        <input type="hidden" value="<?=set_value('tracking_code', $tracking_code)?>" name="tracking_code" />
                        <input type="hidden" value="<?=set_value('tracking_source_code', $tracking_source_code)?>" name="tracking_source_code" />
                    <?php endif ?>
                    <?php if(!$displayAgencyCode && (!empty($agent_tracking_code) || !empty($agent_tracking_source_code))): ?>
                        <input type="hidden" value="<?=set_value('agent_tracking_code', $agent_tracking_code)?>" name="agent_tracking_code" />
                        <input type="hidden" value="<?=set_value('agent_tracking_source_code', $agent_tracking_source_code)?>" name="agent_tracking_source_code" />
                    <?php endif ?>
                    <?php if(!$displayReferralCode && !empty($referral_code)): ?>
                        <input type="hidden" value="<?=set_value('invitationCode', $referral_code)?>" name="invitationCode" />
                    <?php endif ?>

                    <?php if($this->utils->isEnabledFeature('enable_income_access') && isset($btag) && !empty($btag)) : ?>
                        <input type="hidden" name="btag" value="<?=set_value('btag', $btag)?>" />
                    <?php endif; ?>


                    <div class='row'>
                        <?php
                            $item_sequence = $this->utils->getConfig('player_register_item_sequence_web');
                            foreach ($item_sequence as $item) {
                                switch ($item) {
                                    case 'USERNAME': ?>
                                        <?php // USERNAME ?>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group form-inline relative field_required">
                                                <?= $require_display_symbol?>
                                                <label><i class="icon-user"></i></label>
                                                <input type="text" class="form-control registration-field fcname" name="username" id="username" placeholder="<?php echo lang('Username');?> <?=$require_placeholder_text?>" onKeyUp="Register.lowerCase(this)" onfocus="return Register.validateUsernameRequirements(this.value)"  oninput="return Register.validateUsernameRequirements(this.value)" value="<?=set_value('username')?>">
                                            </div>
                                            <div class="fcname-note registration-field-note hide mb20">
                                                <p class="pl15 mb0"><i id="username_len" class="icon-warning red f16 mr5"></i> <?php echo lang('Username').' '. sprintf(lang('validation.lengthRangeStandard'), $min_username_length, $max_username_length);?></p>
                                                <?php if (!empty($this->utils->isRestrictUsernameEnabled())) : ?>
                                                    <p class="pl15 mb0"><i id="username_charcombi" class="icon-warning red f16 mr5"></i> <?=lang('validation.validateUsername01')?></p>
                                                <?php endif; ?>
                                                <p id="username_exist_failed" class="pl15"><i class="icon-warning red f16 mr5"></i> <?=lang('validation.availabilityUsername_2')?></p>
                                                <p id="username_exist_available" class="pl15" style="display: none;"><i class="icon-checked green f16 mr5"></i> <?=lang('validation.availabilityUsername_available')?></p>
                                            </div>
                                        </div>
                                        <?php break;
                                    case 'PASSWORD': ?>
                                        <?php // PASSWORD ?>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group form-inline relative field_required">
                                                <?= $require_display_symbol?>
                                                <label><i class="icon-pass"></i></label>
                                                <input type="password" class="form-control registration-field fcpass" name="password" id="password" onfocus="return Register.validatePasswordRequirements(this.value)"  oninput="return Register.validatePasswordRequirements(this.value)" placeholder="<?php echo lang('Password'); ?> <?=$require_placeholder_text?>" value="<?=set_value('password')?>">
                                            </div>
                                            <div class="fcpass-note registration-field-note hide mb20">
                                                <p class="pl15 mb0"><i id="password_len" class="icon-warning red f16 mr5"></i> <?php echo lang('Password') . ' ' . sprintf(lang('validation.lengthRangeStandard'), $min_password_length, $max_password_length);?></p>
                                                <p class="pl15 mb0"><i id="password_regex" class="icon-warning red f16 mr5"></i> <?=lang('validation.contentPassword01')?></p>
                                                <p class="pl15"><i id="password_not_username" class="icon-warning red f16 mr5"></i> <?=lang('validation.contentPassword02')?></p>
                                            </div>
                                        </div>

                                        <?php // CONFIRM PASSWORD ?>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group form-inline relative field_required">
                                                <?= $require_display_symbol?>
                                                <label><i class="icon-pass"></i></label>
                                                <input type="password" class="form-control registration-field fccpass" name="cpassword" onfocus="return Register.validateConfirmPassword(this.value)"  oninput="return Register.validateConfirmPassword(this.value)" placeholder="<?php echo lang('Confirm Password'); ?> <?=$require_placeholder_text?>" value="<?=set_value('cpassword')?>">
                                            </div>
                                            <div class="fccpass-note registration-field-note hide mb20">
                                                <p class="pl15 mb0"><i id="cpassword_reenter" class="icon-warning red f16 mr5"></i><span id="cpassword_reenter_msg"><?=lang('validation.retypePassword')?></span></p>
                                            </div>
                                        </div>
                                        <?php break;
                                    case 'EMAIL': ?>
                                        <?php
                                        if(in_array('email', $visibled_fields)){
                                            $email_required = $registration_fields['Email']['required'];
                                            // EMAIL ?>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group form-inline relative <?php if ($email_required == Registration_setting::REQUIRED) echo 'field_required';?> ">
                                                    <?php if ( $email_required == Registration_setting::REQUIRED ) {?>
                                                    <?= $require_display_symbol?>
                                                    <?php }?>
                                                    <label><i class="glyphicon glyphicon-envelope"></i></label>
                                                    <input type="text" class="form-control registration-field fcemail" name="email" id="email" data-validateRequired="<?= ($email_required == Registration_setting::REQUIRED) ? 0 : 1 ?>" placeholder="<?php echo lang('Email Address');?> <?= $this->registration_setting->displayPlaceholderHint($registration_fields['Email']["field_name"])?>" value="<?=set_value('email')?>" onfocus="return Register.validateEmail(this.value)" oninput="return Register.validateEmail(this.value)" <?php if ($email_required == Registration_setting::REQUIRED) echo 'required';?>>
                                                </div>
                                                <div class="fcemail-note registration-field-note hide mb20">
                                                    <p class="pl15 mb0"><i id="email_required" class="registration-field-required-icon icon-warning red f16 mr5"></i> <?=lang('validation.requiredEmail')?></p>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <?php break;
                                    case 'REGISTRATION_FIELDS': ?>
                                        <?php // REGISTRATION FIELDS ?>
                                        <?php include_once VIEWPATH . '/resources/common/includes/registration_fields.php'; ?>

                                        <?php if ($this->operatorglobalsettings->getSettingJson('registration_captcha_enabled')):?>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group form-inline relative">
                                                    <label><i class="glyphicon glyphicon-qrcode"></i></label>
                                                    <input required name='captcha' id='captcha' type="text" class="form-control registration-field fcrecaptcha" placeholder="<?php echo lang('label.captcha'); ?> <?=$require_placeholder_text?>" maxlength="4" style="width: 60%" oninput="return Register.validateVerificodeLength(this.value)">
                                                    <i class="fa fa-refresh" style="cursor:pointer; float: right; font-size: 1.4em; color: #888; margin-left: 3px; " aria-hidden="true" onclick="refreshCaptcha()"></i>
                                                    <img class="captcha" id='image_captcha' src='<?php echo site_url('/iframe/auth/captcha?' . random_string('alnum')); ?>' onclick="refreshCaptcha()" />
                                                </div>
                                                <div class="fcrecaptcha-note registration-field-note hide mb20">
                                                    <p class="pl15 mb0">
                                                        <i id="verifi_code_len" class="icon-warning red f16 mr5"></i>
                                                        <?=lang('captcha.required')?>
                                                    </p>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <?php break;
                                    case 'TERMS_AND_CONDITIONS': ?>
                                        <?php // TERMS AND CONDITIONS ?>
                                        <?php if ($registration_fields['At Least 18 Yrs. Old and Accept Terms and Conditions']['visible'] == Registration_setting::VISIBLE): ?>
                                        <div class="col-md-12 col-lg-12">
                                            <div class="checkbox pl10 pr10 reg-age-terms-field">
                                                <div class="form-group">
                                                    <input type="checkbox" class="registration-field" name="terms" id="terms" <?=set_value('terms', $this->CI->config->item('terms', 'player_form_registration')) ? 'checked="checked"' : ''?> value="1" <?php if ($registration_fields['At Least 18 Yrs. Old and Accept Terms and Conditions']['required'] == Registration_setting::REQUIRED) echo 'required';?>
                                                        style="position:relative;margin-left:0"
                                                        <?php if ($registration_fields['At Least 18 Yrs. Old and Accept Terms and Conditions']['required'] == Registration_setting::REQUIRED) echo 'required' ?>
                                                        onfocus="return Register.validateTerms(this.checked)"  onclick="return Register.validateTerms(this.checked)" data-validateRequired="undefined"
                                                    />
                                                    <label for="terms" class="lh24" style="position:relative;left:0;top:0;padding-left:0">
                                                        <?php echo $this->utils->renderLang('register.18age.hint', ["$web_user_terms_url","$web_privacy_policy_url"]);?>
                                                    </label>
                                                </div>
                                                <?php if ($registration_fields['At Least 18 Yrs. Old and Accept Terms and Conditions']['required'] == Registration_setting::REQUIRED): ?>
                                                <div class="registration-field-note hide mb20 fterms">
                                                    <p class="pl10 mb0">
                                                        <i class="registration-field-required-icon icon-checked green f16 mr5 terms_required"></i>
                                                        <?=sprintf(lang('formvalidation.required'), lang('I am at least 18 years of age'))?>
                                                    </p>
                                                </div>
                                                <?php endif ?>
                                            </div>
                                        </div>
                                        <?php endif ?>
                                        <?php break;
                                    case 'COMMUNICATION_PREFERENCES': ?>
                                        <?php // COMMUNICATION PREFERENCES ?>
                                        <?php if ($this->utils->isEnabledFeature('enable_communication_preferences') && !empty($this->utils->getConfig('communication_preferences')) && $registration_fields['Player Preference']['visible'] == Registration_setting::VISIBLE): ?>
                                        <div class="col-md-12 col-lg-12">
                                            <div class="pl10 pr10 reg-communication_preference-field">
                                                <p><?=sprintf(lang('pi.player_pref.hint1'), lang('pi.player_pref_custom_name'))?></p>
                                                <p><?=lang('pi.player_pref.hint2')?></p>
                                            </div>
                                            <?php
                                            $config_preferences = $this->utils->getConfig('communication_preferences');
                                            ?>
                                            <div class="checkbox pl10 pr10">
                                                <?php foreach ($config_preferences as $key => $config_preference): ?>
                                                    <?php
                                                        $player_pref_key = 'Player Preference '.lang($config_preference);
                                                        if($registration_fields[$player_pref_key]['visible'] != Registration_setting::VISIBLE){
                                                            continue;
                                                        }
                                                    ?>
                                                    <?php $genPlayerFromKet = 'communication_preferences_' . $key ?>
                                                    <input type="checkbox" name="pref-data-<?=$key?>" id="pref-data-<?=$key?>" value="true" style="position:relative;margin-left:15px" <?=set_value($genPlayerFromKet, $this->CI->config->item($genPlayerFromKet, 'player_form_registration')) ? 'checked="checked"' : ''?>/>
                                                    <label for="pref-data-<?=$key?>" class="lh24" style="position:relative;left:0;top:0;padding-left:0">
                                                        <?=lang($player_pref_key)?>
                                                    </label>
                                                <?php endforeach ?>
                                            </div>
                                        </div>
                                        <?php else: ?>

                                        <?php // NEWSLETTER SUBSCRIPTION ?>
                                        <?php if($registration_fields['Newsletter Subscription']['visible'] == Registration_setting::VISIBLE){ ?>
                                        <div class="col-md-12 col-lg-12">
                                            <div class="checkbox pl10 pr10 reg-newsletter-field">
                                                <input type="checkbox" name="newsletter_subscription" id="newsletter_subscription" <?=set_value('terms', $this->CI->config->item('newsletter_subscription', 'player_form_default')) ? 'checked="checked"' : ''?> value="1" <?php if ($registration_fields['Newsletter Subscription']['required'] == Registration_setting::REQUIRED) echo 'required';?>
                                                    style="position:relative;margin-left:0"
                                                />
                                                <label for="newsletter_subscription" class="lh24" style="position:relative;left:0;top:0;padding-left:0">
                                                    <?=lang('Newsletter Subscription')?>
                                                </label>
                                            </div>
                                        </div>
                                        <?php }?>

                                        <?php endif ?>
                                        <?php break;
                                    case 'REGISTER_BTN': ?>
                                        <div class="col-md-12 col-lg-12">
                                        <div class="error-message <?=(! empty($this->session->userdata('result'))) ? '' : 'hide'?>">
                                            <?=$this->session->userdata('message')?>
                                        </div>

                                        <?php // REGISTER BTN ?>
                                        <button type="submit" class="btn btn-primary"><?= lang('Register Now'); ?></button>
                                        </div>
                                        <?php break;
                                    case 'LOGIN_LINK': ?>
                                        <?php // LOGIN LINK ?>
                                        <?php if($this->utils->isEnabledFeature('register_page_show_login_link')) : ?>
                                        <div class="col-md-12 col-lg-12">
                                        <p class="pt10 mb0">
                                            <?= $this->utils->renderLang('register_page_login_link', site_url('iframe/auth/login')) ?>
                                        </p>
                                        </div>
                                        <?php endif; ?>
                                        <?php break;

                                    default:
                                        # code...
                                        break;
                                }
                            }
                        ?>
                    </div>
                </form>

            </div>

        </div>
    </div>
</div>
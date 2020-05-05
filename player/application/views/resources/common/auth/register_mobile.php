<style>
    .checkbox{
        margin-left: 36px;
    }
    .green {
        color:  green;
    }
    .required_hint{
        position: absolute;
        left: 0;
    }
</style>
<div class="register-form-container">
    <div class="cstm-mod registration-mod" role="document">
        <div class="modal-content">

            <div class="modal-header text-center">
                <h4 class="modal-title f24" id="myModalLabel"><?php echo lang('Register Your Account'); ?></h4>
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

                        <?php
                            $item_sequence = $this->utils->getConfig('player_register_item_sequence_mobile');
                            foreach ($item_sequence as $item) {
                                switch ($item) {
                                    case 'USERNAME': ?>
                                        <?php // USERNAME?>
                                        <div class="col-md-6 col-lg-12">
                                            <div class="form-group form-inline relative field_required">
                                                <?= $require_display_symbol?>
                                                <label><i class="icon-user"></i></label>
                                                <input type="text" class="form-control registration-field fcname" name="username" id="username" placeholder="<?php echo lang('Username');?> <?=$require_placeholder_text?>" onKeyUp="Register.lowerCase(this)" onfocus="return Register.validateUsernameRequirements(this.value)"  oninput="return Register.validateUsernameRequirements(this.value)" value="<?=set_value('username')?>">
                                            </div>
                                            <div class="fcname-note registration-field-note hide mb20">
                                                <p class="pl15 mb0">
                                                    <i id="username_len" class="icon-warning red f16 mr5"></i> <?php echo lang('Username') .' '. sprintf(lang('validation.lengthRangeStandard'), $min_username_length, $max_username_length);?>
                                                </p>
                                                <?php if (!empty($this->utils->isRestrictUsernameEnabled())) : ?>
                                                    <p class="pl15 mb0">
                                                        <i id="username_charcombi" class="icon-warning red f16 mr5"></i> <?=lang('validation.validateUsername01')?>
                                                    </p>
                                                <?php endif; ?>
                                                <p class="pl15 mb0" id="username_exist_failed">
                                                    <i class="icon-warning red f16 mr5"></i> <?=lang('validation.availabilityUsername_2')?>
                                                </p>
                                                <p class="pl15 mb0" id="username_exist_available" style="display: none;">
                                                    <i class="icon-checked green f16 mr5"></i> <?=lang('validation.availabilityUsername_available')?>
                                                </p>
                                                <p class="pl15">
                                                    <i id="username_lan" class="icon-warning red f16 mr5" style="visibility: hidden;"></i> <?php echo lang('username.lan')?>
                                                </p>
                                            </div>
                                        </div>
                                        <?php break;

                                    case 'PASSWORD': ?>
                                            <?php // PASSWORD?>
                                            <div class="col-md-6 col-lg-12">
                                                <div class="form-group form-inline relative field_required">
                                                    <?= $require_display_symbol?>
                                                    <label><i class="icon-pass"></i></label>
                                                    <input type="password" class="form-control registration-field fcpass" name="password" id="password" onfocus="return Register.validatePasswordRequirements(this.value)"  oninput="return Register.validatePasswordRequirements(this.value)" placeholder="<?php echo lang('Password'); ?> <?=$require_placeholder_text?>" value="<?=set_value('password')?>">
                                                </div>
                                                <div class="fcpass-note registration-field-note hide mb20">
                                                    <p class="pl15 mb0"><i id="password_len" class="icon-warning red f16 mr5"></i> <?php echo lang('Password') .' '. sprintf(lang('validation.lengthRangeStandard'), $min_password_length, $max_password_length) . ' ' .lang('validation.lengthRangeContent');?></p>
                                                    <p class="pl15 mb0"><i id="password_regex" class="icon-warning red f16 mr5"></i> <?=lang('validation.contentPassword01')?></p>
                                                    <p class="pl15"><i id="password_not_username" class="icon-warning red f16 mr5"></i> <?=lang('validation.contentPassword02')?></p>
                                                </div>
                                            </div>
                                            <?php // CONFIRM PASSWORD?>
                                            <div class="col-md-6 col-lg-12">
                                                <div class="form-group form-inline relative field_required">
                                                    <?= $require_display_symbol?>
                                                    <label><i class="icon-pass"></i></label>
                                                    <input type="password" class="form-control registration-field fccpass" name="cpassword" onfocus="return Register.validateConfirmPassword(this.value)"  oninput="return Register.validateConfirmPassword(this.value)" placeholder="<?php echo lang('Confirm Password'); ?> <?=$require_placeholder_text?>" value="<?=set_value('cpassword')?>">
                                                </div>
                                                <div class="fccpass-note registration-field-note hide mb20">
                                                    <p class="pl15 mb0"><i id="cpassword_reenter" class="icon-warning red f16 mr5"></i> <?=lang('validation.retypePassword')?></p>
                                                </div>
                                            </div>
                                        <?php break;
                                    case 'EMAIL': ?>
                                                <?php
                                                if (in_array('email', $visibled_fields)) {
                                                    $email_required = $registration_fields['Email']['required'];
                                                    // EMAIL?>
                                                    <div class="col-md-6 col-lg-12">
                                                        <div class="form-group form-inline relative <?php if ($registration_fields['Email']['required'] == Registration_setting::REQUIRED) {
                                                        echo 'field_required';
                                                    } ?> ">
                                                            <?php if ($registration_fields['Email']['required'] == Registration_setting::REQUIRED) {?>
                                                                <?= $this->registration_setting->displaySymbolHint($registration_fields['Email']["field_name"])?>
                                                            <?php } ?>
                                                            <label><i class="glyphicon glyphicon-envelope"></i></label>
                                                            <input type="text" class="form-control registration-field fcemail" name="email" id="email" data-validateRequired="<?= ($email_required == Registration_setting::REQUIRED) ? 0 : 1 ?>" placeholder="<?php echo lang('Email Address'); ?> <?= $this->registration_setting->displayPlaceholderHint($registration_fields['Email']["field_name"])?>" value="<?=set_value('email')?>" onfocus="return Register.validateEmail(this.value)" oninput="return Register.validateEmail(this.value)">
                                                        </div>
                                                        <div class="fcemail-note registration-field-note hide mb20">
                                                            <p class="pl15 mb0"><i id="email_required" class="registration-field-required-icon icon-warning red f16 mr5"></i> <?=lang('validation.requiredEmail')?></p>
                                                        </div>
                                                    </div>
                                                <?php
                                                }?>
                                        <?php break;
                                    case 'REGISTRATION_FIELDS': ?>
                                            <?php // REGISTRATION FIELDS?>
                                            <?php include_once VIEWPATH . '/resources/common/includes/registration_fields.php'; ?>

                                            <?php if ($this->operatorglobalsettings->getSettingJson('registration_captcha_enabled')):?>
                                                <div class="col-md-6 col-lg-12">
                                                    <div class="form-group form-inline relative">
                                                        <label><i class="glyphicon glyphicon-qrcode"></i></label>
                                                        <input required name='captcha' id='captcha' type="text" class="form-control registration-field fcrecaptcha" placeholder="<?php echo lang('label.captcha'); ?> <?=$require_placeholder_text?>" maxlength="4" style="width:60%">
                                                        <i class="fa fa-refresh" style="cursor:pointer; float: right; font-size: 1.4em; color: #888; margin-left: 3px; " aria-hidden="true" onclick="refreshCaptcha()"></i>
                                                        <img class="captcha" id='image_captcha' src='<?php echo site_url('/iframe/auth/captcha?' . random_string('alnum')); ?>' onclick="refreshCaptcha()">
                                                    </div>
                                                    <div class="fcrecaptcha-note registration-field-note hide mb20">
                                                        <p class="pl15 mb0">
                                                            <i id="referral_code" class="registration-field-required-icon icon-warning red f16 mr5"></i>
                                                            <?=lang('captcha.required')?>
                                                        </p>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php break;
                                    case 'COMMUNICATION_PREFERENCES': ?>
                                            <?php // COMMUNICATION PREFERENCES?>
                                            <?php if ($this->utils->isEnabledFeature('enable_communication_preferences') && !empty($this->utils->getConfig('communication_preferences')) && $registration_fields['Player Preference']['visible'] == Registration_setting::VISIBLE): ?>
                                            <div class="col-md-6 col-lg-12">
                                                <div class="checkbox pr10 reg-communication_preference-field">
                                                    <p><?=sprintf(lang('pi.player_pref.hint1'), lang('pi.player_pref_custom_name'))?></p>
                                                    <p><?=lang('pi.player_pref.hint2')?></p>
                                                </div>
                                                <?php
                                                $config_preferences = $this->utils->getConfig('communication_preferences');
                                                ?>
                                                <div class="checkbox pl10 pr10 comm_pref_mob_reg_items">
                                                    <?php foreach ($config_preferences as $key => $config_preference): ?>
                                                        <?php
                                                        $player_pref_key = 'Player Preference '.lang($config_preference);
                                                        if ($registration_fields[$player_pref_key]['visible'] != Registration_setting::VISIBLE) {
                                                            continue;
                                                        }
                                                        ?>
                                                    <?php $genPlayerFromKet = 'communication_preferences_' . $key ?>
                                                    <input type="checkbox" name="pref-data-<?=$key?>" id="pref-data-<?=$key?>" value="true" <?=set_value($genPlayerFromKet, $this->CI->config->item($genPlayerFromKet, 'player_form_registration')) ? 'checked="checked"' : ''?>/>
                                                    <div for="pref-data-<?=$key?>" class="lh24">
                                                        <?=lang($player_pref_key)?>
                                                    </div>
                                                    <?php endforeach ?>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                        <?php break;
                                    case 'TERMS_AND_CONDITIONS': ?>
                                        <?php // TERMS AND CONDITIONS?>
                                        <?php if ($registration_fields['At Least 18 Yrs. Old and Accept Terms and Conditions']['visible'] == Registration_setting::VISIBLE): ?>
                                        <div class="col-md-6 col-lg-12">
                                            <div class="checkbox pl10 pr10 reg-age-terms-field">
                                                <div class="form-group">
                                                    <input type="checkbox" class="registration-field" name="terms" id="terms" <?php if ($registration_fields['At Least 18 Yrs. Old and Accept Terms and Conditions']['required'] == Registration_setting::REQUIRED) {
                                                    echo 'required';
                                                }?>
                                                        onfocus="return Register.validateTerms(this.checked)" onclick="return Register.validateTerms(this.checked)"
                                                        data-validateRequired="undefined"
                                                    >
                                                    <div for="terms" class="lh24">
                                                    <?php echo $this->utils->renderLang('register.18age.hint', ["$web_user_terms_url","$web_privacy_policy_url"]);?>
                                                    </div>
                                                </div>
                                                <?php if ($registration_fields['At Least 18 Yrs. Old and Accept Terms and Conditions']['required'] == Registration_setting::REQUIRED): ?>
                                                <div class="registration-field-note hide mb20 fterms">
                                                    <p class="mb0">
                                                        <i class="registration-field-required-icon icon-warning red f16 mr5 terms_required"></i>
                                                        <?=sprintf(lang('formvalidation.required'), lang('I am at least 18 years of age'))?>
                                                    </p>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                        <?php break;
                                    case 'REGISTER_BTN': ?>
                                        <div class="col-md-6 col-lg-12">
                                            <div class="error-message <?=(! empty($this->session->userdata('result'))) ? '' : 'hide'?>">
                                                <?=$this->session->userdata('message')?>
                                            </div>

                                            <?php // REGISTER BTN ?>
                                            <div>
                                                <button type="submit" class="btn btn-primary" style="margin-left: 16px;"><?= lang('Register Now'); ?></button>
                                            </div>
                                        </div>
                                        <?php break;
                                    case 'HAVE_ACCOUNT': ?>
                                        <?php // HAVE ACCOUNT?>
                                        <div class="col-md-6 col-lg-12">
                                            <p class="pl10 pr10 pt20" style="margin-top: 15px">
                                                <?=sprintf(lang('Already have account and Please Login'), site_url('iframe/auth/login'))?>
                                            </p>
                                        </div>
                                        <?php break;
                                    case 'LIVE_CHAT': ?>
                                        <?php // LIVE CHAT?>
                                        <div class="col-md-6 col-lg-12">
                                            <div class="form-group form-link live-chat-box">
                                                <a id="contact_customer_service" href="javascript:void(0)" onclick="<?=$this->utils->getLiveChatOnClick();?>"><i class="icon-bubble"></i> <?=lang('Live Chat')?></a>
                                            </div>
                                        </div>
                                        <?php break;
                                    default:
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

<!-- The Modal -->
<div class="modal fade" id="summaryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="display: none;"></div>
            <div class="modal-body"></div>
            <div class="modal-footer">
            <button type="button" id="close_btn" class="btn btn-primary btn-close no" data-dismiss="modal"><?= lang('lang.cancel') ?></button>
            </div>
        </div>
    </div>
</div>

<?php if ( ! isset($preview) || ! $preview): ?>
<script>
var modal = $('#summaryModal').modal({
    "backdrop": "static",
    "keyboard": false,
    "show": false
});

modal.off('show.bs.modal').on('show.bs.modal', function(){
    $('button.btn-close', modal).prop('disabled', true).attr('disabled', 'disabled').addClass('disabled');
});

modal.off('hide.bs.modal').on('hide.bs.modal', function(){
    $('.modal-body', modal).html('');
});

</script>
<?php endif ?>
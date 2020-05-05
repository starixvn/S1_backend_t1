
<?php
    $placeholder_hint = $this->utils->isEnabledFeature('show_hint_in_box_on_registration');
?>

<style type="text/css">
   .form-group.required .control-label:after, .control-label.required:after { color: #ff6666; content: " *"; font-style: italic; }

   /* do not group these rules */
   #captcha::-webkit-input-placeholder, #affiliate_code::-webkit-input-placeholder, #referral_code::-webkit-input-placeholder, #im_account2::-webkit-input-placeholder, #im_account::-webkit-input-placeholder, #im_type::-webkit-input-placeholder, #security_question::-webkit-select-placeholder, #contact_number::-webkit-input-placeholder, #birthplace::-webkit-input-placeholder, #birthdate::-webkit-input-placeholder, #citizenship::-webkit-input-placeholder, #resident_country::-webkit-input-placeholder, #retyped_email::-webkit-input-placeholder, #email::-webkit-input-placeholder, #last_name::-webkit-input-placeholder, #first_name::-webkit-input-placeholder,#password::-webkit-input-placeholder, #cpassword::-webkit-input-placeholder, #username::-webkit-input-placeholder {
       color: #e74c3c;
   }

</style>
<div class="panel" style="margin-bottom: 0;">
    <div class="panel-heading">
        <p class="text-danger pull-right"><?=lang('reg.02')?></p>
        <?php $bootstrap_style = $this->config->item('default_player_bootstrap_css');
        if($bootstrap_style  == 'bootstrap.paper2.css'){ ?>

            <h1 class="panel-title"><?=lang('reg.01')?></h1>

        <?php } else { ?>

            <h1 class="panel-title"><?=lang('reg.01')?></h1>

        <?php } ?>
    </div>
    <div class="panel-body">

        <form action="<?=site_url('iframe_module/postRegisterPlayer')?>" method="post" id="registration_form">
            <input type="hidden" value="<?= set_value('tracking_code') ? : $tracking_code ?>" name="tracking_code" id="tracking_code"/>
            <input type="hidden" value="<?=(set_value('tracking_source_code') == null) ? $tracking_source_code : set_value('tracking_source_code')?>" name="tracking_source_code" id="tracking_source_code"/>
            <input type="hidden" name="level" value="normal">
            <input type="hidden" name="currency" value="CNY">

            <div class="clearfix"></div>

            <div class="row">



                <?php # USERNAME ?>
                <div style="height: 64px; overflow: visible;" class="form-group text-right col-md-3 required">
                    <?php if($bootstrap_style  == 'bootstrap.paper2.css'){ ?>

                        <label for="username" class="control-label pull-left">用户名 Username</label>

                    <?php } else { ?>

                        <label for="username" class="control-label pull-left"><?=lang('reg.03');?></label>

                    <?php } ?>
                    <div class="clearfix"></div>
                    <!---<div class="input-group">-->
                        <!--<span class="input-group-addon"><?=$default_prefix_for_username?></span>-->
                        <input id="username" name="username" <?=( $placeholder_hint ) ? 'placeholder="' . lang('reg.username') . '"' : 'placeholder = "'.lang('reg.03').'" title="' . sprintf(lang('reg.04'), $min_username_length, $max_username_length) . '"' ?> class="form-control text-lowercase" pattern="[a-zA-Z0-9]+" data-toggle="tooltip" type="text" value="<?=set_value('username')?>"/>
                    <!--</div>-->
                    <span class="help-block text-success" id="usernameAvailable" style="display: none;"></span>
                </div>



                <?php # PASSWORD ?>
                <div style="height: 64px; overflow: visible;" class="form-group text-right col-md-3 required">
                <?php

                    $note = sprintf(lang("gen.error.between.lowercase.alphanum"), lang("reg.05"), $min_password_length, $max_password_length);

                ?>
                    <?php if($bootstrap_style  == 'bootstrap.paper2.css'){ ?>

                        <label for="password" class="control-label pull-left">密码 Password</label>

                    <?php } else { ?>

                        <label for="password" class="control-label pull-left"><?=lang('reg.05')?></label>

                    <?php } ?>
                    <div class="clearfix"></div>
                    <input id="password" name="password" <?=( $placeholder_hint ) ? 'placeholder="' . lang('reg.password') . '"' : 'placeholder = "'.lang('reg.05').'" title="' . $note . '"' ?>  class="form-control" data-toggle="tooltip" type="password"/>
                </div>



                <?php # CONFIRM PASSWORD ?>
                <div style="height: 64px; overflow: visible;" class="form-group text-right col-md-3 required">
                    <?php if($bootstrap_style  == 'bootstrap.paper2.css'){ ?>

                        <label for="cpassword" class="control-label pull-left">确认密码 Confirm Password</label>

                    <?php } else { ?>

                        <label for="cpassword" class="control-label pull-left"><?=lang('reg.07')?></label>

                    <?php } ?>
                    <div class="clearfix"></div>
                    <input id="cpassword" name="cpassword" <?=( $placeholder_hint ) ? 'placeholder="' . lang('reg.08') . '"' : 'placeholder = "'.lang('reg.09').'" title="' . lang('reg.08') . '"'?> class="form-control" data-toggle="tooltip" type="password"/>
                </div>



                <?php # GENDER ?>
                <?php if ($this->registration_setting->isRegistrationFieldVisible('Gender')) { ?>
                    <div style="height: 64px; overflow: visible;" class="form-group text-right col-md-3 <?php if ($this->registration_setting->isRegistrationFieldRequired('Gender')) echo 'required'?>">
                        <?php if($bootstrap_style  == 'bootstrap.paper2.css'){ ?>

                            <label for="gender" class="control-label pull-left">性别 Gender</label>

                        <?php } else { ?>

                            <label for="gender" class="control-label pull-left"> <?=lang('reg.15')?></label>

                        <?php } ?>
                        <div class="clearfix"></div>
                        <div class="btn-group btn-group-justified" data-toggle="buttons">
                            <label class="btn btn-default btn-gender <?php if (set_radio('gender', 'Male')) echo 'active'; ?>">
                                <input type="radio" name="gender" class="gender" id="gender-male" value="Male" <?=set_radio('gender', 'Male')?>> <?=lang('reg.16')?>
                            </label>
                            <label class="btn btn-default btn-gender <?php if (set_radio('gender', 'Female')) echo 'active'; ?>">
                                <input type="radio" name="gender" class="gender" id="gender-female" value="Female" <?=set_radio('gender', 'Female')?>> <?=lang('reg.17')?>
                            </label>
                        </div>
                        <div class="help-block" id="gender-help-block"></div>
                    </div>
                <?php } ?>



                <?php # FIRST NAME ?>
                <?php if ($this->registration_setting->isRegistrationFieldVisible('First Name')) { ?>
                    <div style="height: 64px; overflow: visible;" class="form-group text-right col-md-3 <?php if ($this->registration_setting->isRegistrationFieldRequired('First Name')) echo 'required'?>">
                    <?php if($bootstrap_style  == 'bootstrap.paper2.css'){ ?>

                        <label for="first_name" class="control-label pull-left">名字 First Name</label>

                    <?php } else { ?>

                        <label for="first_name" class="control-label pull-left"><?=($this->utils->getConfig('real_name_reminder')) ? lang('Realname') : lang('reg.10') ?></label>

                    <?php } ?>

                        <div class="clearfix"></div>
                        <input id="first_name" name="first_name" <?=( $placeholder_hint ) ? 'placeholder="'. lang('reg.firstName') .'"' : 'placeholder = "'.(($this->utils->getConfig('real_name_reminder')) ? lang('Realname') : lang('reg.10')).'" title="' . ($this->utils->getConfig('real_name_reminder') ? lang('real_name_reminder') : lang('reg.10')). '"' ?> class="form-control" data-toggle="tooltip" type="text" value="<?=set_value('first_name')?>">
                    </div>
                <?php } ?>



                <?php # LAST NAME ?>
                <?php if ($this->registration_setting->isRegistrationFieldVisible('Last Name')) { ?>
                    <div style="height: 64px; overflow: visible;" class="form-group text-right col-md-3 <?php if ($this->registration_setting->isRegistrationFieldRequired('Last Name')) echo 'required'?>">
                    <?php if($bootstrap_style  == 'bootstrap.paper2.css'){ ?>

                        <label for="last_name" class="control-label pull-left">姓 Last Name</label>

                    <?php } else { ?>

                        <label for="last_name" class="control-label pull-left"><?=lang('reg.11')?></label>

                    <?php } ?>
                        <div class="clearfix"></div>
                        <input id="last_name" name="last_name" <?=( $placeholder_hint ) ? 'placeholder="'. lang('reg.lastName') .'"' : 'placeholder = "'.lang('reg.11').'" title="' . lang('reg.11') . '"' ?> class="form-control" data-toggle="tooltip" type="text" value="<?=set_value('last_name')?>">
                    </div>
                <?php } ?>



                <?php # BIRTHDAY ?>
                <?php if ($this->registration_setting->isRegistrationFieldVisible('Birthday')) { ?>
                    <div style="height: 64px; overflow: visible;" class="form-group text-right col-md-3 <?php if ($this->registration_setting->isRegistrationFieldRequired('Birthday')) echo 'required'?>">
                        <?php if($bootstrap_style  == 'bootstrap.paper2.css'){ ?>

                            <label for="birthdate" class="control-label pull-left">生日 Birthday</label>

                        <?php } else { ?>

                            <label for="birthdate" class="control-label pull-left"><?=lang('reg.12')?></label>

                        <?php } ?>
                        <div class="clearfix"></div>
                        <input id="birthdate" name="birthdate" placeholder="YYYY-MM-DD" class="form-control dateInput" type="text" value="<?=set_value('birthdate') ? : '1990-01-01'?>">
                    </div>
                <?php } ?>



                <?php # LANGUAGE ?>
                <?php if ($this->registration_setting->isRegistrationFieldVisible('Language')) { ?>
                    <div style="height: 64px; overflow: visible;" class="form-group text-right col-md-3 <?php if ($this->registration_setting->isRegistrationFieldRequired('Language')) echo 'required'?>">
                        <?php if($bootstrap_style  == 'bootstrap.paper2.css'){ ?>

                            <label for="language" class="control-label pull-left">语言 Language</label>

                        <?php } else { ?>

                            <label for="language" class="control-label pull-left"><?=lang('reg.26')?></label>

                        <?php } ?>
                        <div class="clearfix"></div>
                        <select id="language" name="language" title="<?=( $placeholder_hint ) ? '' : lang('reg.26')?>" class="form-control" data-toggle="tooltip">
                            <option value=""><?=lang('pi.18')?></option>
                            <option value="Chinese" <?=set_select('language', 'Chinese')?>><?=lang('reg.27')?></option>
                            <option value="English" <?=set_select('language', 'English')?>>English</option>
                            <option value="Indonesian" <?=set_select('language', 'Indonesian')?>>Indonesian</option>
                            <option value="Vietnamese" <?=set_select('language', 'Vietnamese')?>>Vietnamese</option>
                        </select>
                    </div>
                <?php } ?>



                <?php # EMAIL ?>
                <div style="height: 64px; overflow: visible;" class="form-group text-right col-md-3 required">
                    <?php if($bootstrap_style  == 'bootstrap.paper2.css'){ ?>

                        <label for="email" class="control-label pull-left">邮箱 Email</label>

                    <?php } else { ?>

                        <label for="email" class="control-label pull-left"><?=lang('reg.18')?></label>

                    <?php } ?>
                    <div class="clearfix"></div>
                    <input id="email" name="email" <?=( $placeholder_hint ) ? 'placeholder="'. lang('reg.email') .'"' : 'placeholder = "'.lang('reg.18').'" title="' . lang('reg.19') . '"' ?>  class="form-control" data-toggle="tooltip" type="email" value="<?=set_value('email')?>"/>
                </div>



                <?php # RESIDENT COUNTRY ?>
                <?php if ($this->registration_setting->isRegistrationFieldVisible('Resident Country')) { ?>
                    <div style="height: 64px; overflow: visible;" class="form-group text-right col-md-3 <?php if ($this->registration_setting->isRegistrationFieldRequired('Resident Country')) echo 'required'?>">
                        <?php if($bootstrap_style  == 'bootstrap.paper2.css'){ ?>

                            <label for="resident_country" class="control-label pull-left">现居国家 Resident Country</label>

                        <?php } else { ?>

                            <label for="resident_country" class="control-label pull-left"><?=($this->utils->getConfig('european_address_format')) ? lang('address_3') : lang('a_reg.33')?></label>

                        <?php } ?>
                        <div class="clearfix"></div>
                        <select id="resident_country" name="resident_country" title="<?=( $placeholder_hint ) ? '' : lang('a_reg.33')?>" class="form-control" data-toggle="tooltip">
                            <option value=""><?=lang('pi.20')?></option>
                            <optgroup label="<?=lang('lang.frequentlyUsed')?>">
                                <?php foreach (unserialize(COMMON_COUNTRY_LIST) as $key) {?>
                                    <option value="<?=$key?>"><?=lang('country.' . $key)?></option>
                                <?php } ?>
                            </optgroup>
                            <optgroup label="<?=lang('lang.alphabeticalOrder')?>">
                                <?php foreach (unserialize(COUNTRY_LIST) as $key) {?>
                                    <option value="<?=$key?>" <?php if ($this->input->post('resident_country') == $key) echo 'selected="selected"'; ?>><?=lang('country.' . $key)?></option>
                                <?php } ?>
                            </optgroup>
                        </select>
                    </div>
                <?php } ?>


                
                <?php # CITY ?>
                <?php if ($this->registration_setting->isRegistrationFieldVisible('City')) { ?>
                    <div style="height: 64px; overflow: visible;" class="form-group text-right col-md-3 <?php if ($this->registration_setting->isRegistrationFieldRequired('City')) echo 'required'?>">
                        <?php if($bootstrap_style  == 'bootstrap.paper2.css'){ ?>

                            <label for="city" class="control-label pull-left">城市 City</label>

                        <?php } else { ?>

                            <label for="city" class="control-label pull-left"><?=($this->utils->getConfig('european_address_format')) ? lang('address_2') : lang('a_reg.36')?></label>

                        <?php } ?>
                        <div class="clearfix"></div>
                        <input id="city" name="city" <?=( $placeholder_hint ) ? 'placeholder="'.(($this->utils->getConfig('european_address_format')) ? lang('eur.1') : lang('a_reg.36')) .'"' : 'placeholder = "'.(($this->utils->getConfig('european_address_format')) ? lang('eur.1') : lang('a_reg.36')).'" title="' .(($this->utils->getConfig('european_address_format')) ? lang('eur.1') : lang('a_reg.36')). '"' ?>  class="form-control" data-toggle="tooltip" type="text" value="<?=set_value('city')?>">
                    </div>
                <?php } ?>



                <?php # ADDRESS ?>
                <?php if ($this->registration_setting->isRegistrationFieldVisible('Address')) { ?>
                    <div style="height: 64px; overflow: visible;" class="form-group text-right col-md-3 <?php if ($this->registration_setting->isRegistrationFieldRequired('Address')) echo 'required'?>">
                        <?php if($bootstrap_style  == 'bootstrap.paper2.css'){ ?>

                            <label for="address" class="control-label pull-left">地址 Address</label>

                        <?php } else { ?>

                            <label for="address" class="control-label pull-left"><?=($this->utils->getConfig('european_address_format')) ? lang('address_1') : lang('a_reg.37')?></label>

                        <?php } ?>
                        <div class="clearfix"></div>
                        <input id="address" name="address" <?=( $placeholder_hint ) ? 'placeholder="'. (($this->utils->getConfig('european_address_format')) ? lang('eur.2') : lang('a_reg.37')) .'"' : 'placeholder = "'.(($this->utils->getConfig('european_address_format')) ? lang('eur.2') : lang('a_reg.37')).'" title="' . (($this->utils->getConfig('european_address_format')) ? lang('eur.2') : lang('a_reg.37')) . '"' ?>  class="form-control" data-toggle="tooltip" type="text" value="<?=set_value('address')?>">
                    </div>
                <?php } ?>



                <?php # NATIONALITY ?>
                <?php if ($this->registration_setting->isRegistrationFieldVisible('Nationality')) { ?>
                    <div style="height: 64px; overflow: visible;" class="form-group text-right col-md-3 <?php if ($this->registration_setting->isRegistrationFieldRequired('Nationality')) echo 'required'?>">
                        <?php if($bootstrap_style  == 'bootstrap.paper2.css'){ ?>

                            <label for="citizenship" class="control-label pull-left">国籍 Nationality</label>

                        <?php } else { ?>

                            <label for="citizenship" class="control-label pull-left"><?=lang('reg.22')?></label>

                        <?php } ?>
                        <div class="clearfix"></div>
                        <input id="citizenship" name="citizenship" <?=( $placeholder_hint ) ? 'placeholder="'. lang('reg.23') .'"' : 'placeholder = "'.lang('reg.22').'" title="' . lang('reg.23') . '"' ?>  class="form-control" data-toggle="tooltip" type="text" value="<?=set_value('citizenship')?>">
                    </div>
                <?php } ?>



                <?php # BIRTH PLACE ?>
                <?php if ($this->registration_setting->isRegistrationFieldVisible('BirthPlace')) { ?>
                    <div style="height: 64px; overflow: visible;" class="form-group text-right col-md-3 <?php if ($this->registration_setting->isRegistrationFieldRequired('BirthPlace')) echo 'required'?>">
                        <?php if($bootstrap_style  == 'bootstrap.paper2.css'){ ?>

                            <label for="birthplace" class="control-label pull-left">出生地 BirthPlace</label>

                        <?php } else { ?>

                            <label for="birthplace" class="control-label pull-left"><?=lang('reg.24')?></label>

                        <?php } ?>
                        <div class="clearfix"></div>
                        <input id="birthplace" name="birthplace" <?=( $placeholder_hint ) ? 'placeholder="'. lang('reg.25') .'"' : 'placeholder = "'.lang('reg.24').'" title="' . lang('reg.25') . '"' ?>  class="form-control" data-toggle="tooltip" type="text" value="<?=set_value('birthplace')?>">
                    </div>
                <?php } ?>



                <?php # CONTACT NUMBER ?>
                <?php if ($this->registration_setting->isRegistrationFieldVisible('Contact Number')) { ?>
                    <div style="height: 64px; overflow: visible;" class="form-group text-right col-md-3 <?php if ($this->registration_setting->isRegistrationFieldRequired('Contact Number')) echo 'required'?>">
                        <?php if($bootstrap_style  == 'bootstrap.paper2.css'){ ?>

                            <label for="contact_number" class="control-label pull-left">联系电话 Contact Number</label>

                        <?php } else { ?>

                            <label for="contact_number" class="control-label pull-left"><?=lang('reg.29')?></label>

                        <?php } ?>
                        <div class="clearfix"></div>
                            <?php if ($showSMSField): ?>
                            <div class="input-group">
                            <?php endif ?>
                                <input id="contact_number" name="contact_number" <?=( $placeholder_hint ) ? 'placeholder="'. lang('reg.contact') .'"' : 'placeholder = "'.lang('reg.29').'" title="' . lang('reg.30') . '"' ?> class="form-control" data-toggle="tooltip" type="text" value="<?=set_value('contact_number')?>">
                            <?php if ($showSMSField): ?>
                                <span class="input-group-btn">
                                    <button type="button" id="send_sms_verification" class="btn btn-success"><?=lang('Send SMS')?></button>
                                </span>
                            </div>
                            <?php endif ?>
                    </div>

                    <?php if ($showSMSField): ?>
                        <div style="height: 64px; overflow: visible;" class="form-group text-right col-md-3 <?php if ($this->registration_setting->isRegistrationFieldRequired('SMS Verification Code')) echo 'required'?>">
                            <label for="sms_verification" class="control-label pull-left"><?=lang('Verification Code')?></label>
                            <div class="clearfix"></div>
                            <input id="sms_verification" name="sms_verification_code" placeholder="<?=lang('Verification Code')?>" title="<?=lang('Verification Code')?>" class="form-control" data-toggle="tooltip" type="text" value="<?=set_value('sms_verification_code')?>">
                        </div>
                    <?php endif ?>
                <?php } ?>

                <?php # WITHDRAWAL PASSWORD
                if($showWithdrawalPasswordField) : ?>
                <div style="height: 64px; overflow: visible;" class="form-group text-right col-md-3 required">
                    <label for="withdrawal_password" class="control-label pull-left"><?=lang('Withdrawal Password')?></label>
                    <div class="clearfix"></div>
                    <input id="withdrawal_password" name="withdrawal_password" placeholder="<?=lang('Withdrawal Password')?>" title="<?=lang('Withdrawal Password')?>" class="form-control" data-toggle="tooltip" type="text" value="<?=set_value('withdrawal_password')?>">
                </div>
                <div style="height: 64px; overflow: visible;" class="form-group text-right col-md-3 required">
                    <label for="cwithdrawal_password" class="control-label pull-left"><?=lang('Confirm Withdrawal Password')?></label>
                    <div class="clearfix"></div>
                    <input id="cwithdrawal_password" name="cwithdrawal_password" placeholder="<?=lang('Confirm Withdrawal Password')?>" title="<?=lang('Confirm Withdrawal Password')?>" class="form-control" data-toggle="tooltip" type="text" value="<?=set_value('cwithdrawal_password')?>">
                </div>
                <?php endif; ?>


                <?php # SECURITY QUESTION / ANSWER ?>
                <?php if ($this->registration_setting->isRegistrationFieldVisible('Security Question')) { ?>
                    <div style="height: 64px; overflow: visible;" class="form-group text-right col-md-3 <?php if ($this->registration_setting->isRegistrationFieldRequired('Security Question')) echo 'required'?>">
                        <?php if($bootstrap_style  == 'bootstrap.paper2.css'){ ?>

                            <label for="security_question" class="control-label pull-left">安全问题 Security Question</label>

                        <?php } else { ?>

                            <label for="security_question" class="control-label pull-left"><?=lang('reg.35')?></label>

                        <?php } ?>
                        <div class="clearfix"></div>
                        <select id="security_question" name="security_question" title="<?=( $placeholder_hint ) ? '' : lang('reg.36')?>" class="form-control" data-toggle="tooltip" onchange="$('#security_answer').prop('disabled', $(this).val() == '');">
                            <option value=""><?=lang('reg.58')?></option>
                            <option value="reg.37" <?=set_select('security_question', 'reg.37')?>><?=lang('reg.37')?></option>
                            <option value="reg.38" <?=set_select('security_question', 'reg.38')?>><?=lang('reg.38')?></option>
                            <option value="reg.39" <?=set_select('security_question', 'reg.39')?>><?=lang('reg.39')?></option>
                            <option value="reg.40" <?=set_select('security_question', 'reg.40')?>><?=lang('reg.40')?></option>
                            <option value="reg.41" <?=set_select('security_question', 'reg.41')?>><?=lang('reg.41')?></option>
                        </select>
                    </div>

                    <div style="height: 64px; overflow: visible;" class="form-group text-right col-md-3 <?php if ($this->registration_setting->isRegistrationFieldRequired('Security Question')) echo 'required'?>">
                        <?php if($bootstrap_style  == 'bootstrap.paper2.css'){ ?>

                            <label for="security_answer" class="control-label pull-left">安全问题答案 Security Answer</label>

                        <?php } else { ?>

                            <label for="security_answer" class="control-label pull-left"><?=lang('reg.42')?></label>

                        <?php } ?>
                        <div class="clearfix"></div>
                        <input id="security_answer" name="security_answer" <?=( $placeholder_hint ) ? 'placeholder="'. lang('reg.security') .'"' : 'placeholder = "'.lang('reg.42').'" title="' . sprintf(lang('reg.43'), $min_security_answer_length, $max_security_answer_length) . '"' ?> class="form-control" data-toggle="tooltip" type="text" value="<?=set_value('security_answer')?>" <?php if ( ! set_value('security_question')) echo 'disabled="disabled"'; ?>>
                    </div>
                <?php } ?>



                <?php # IM 1 ?>
                <?php if ($this->registration_setting->isRegistrationFieldVisible('Instant Message 1')) { ?>
                    <div style="height: 64px; overflow: visible;" class="form-group text-right col-md-3 <?php if ($this->registration_setting->isRegistrationFieldRequired('Instant Message 1')) echo 'required'?>">
                        <?php if($bootstrap_style  == 'bootstrap.paper2.css'){ ?>

                            <label for="im_type" class="control-label pull-left">聊天软件类型 IM1 Type</label>

                        <?php } else { ?>

                            <label for="im_type" class="control-label pull-left"><?=lang('aff.ai16')?></label>

                        <?php } ?>
                        <div class="clearfix"></div>
                        <select id="im_type" name="im_type" title="<?=( $placeholder_hint ) ? '' : lang('reg.32')?>" class="form-control" data-toggle="tooltip" onchange="$('#im_account').prop('disabled', $(this).val() == '');">
                            <option value=""><?=lang('reg.58')?></option>
                            <option value="QQ" <?=set_select('im_type', 'QQ')?>>QQ</option>
                            <option value="Skype" <?=set_select('im_type', 'Skype')?>>Skype</option>
                            <option value="MSN" <?=set_select('im_type', 'MSN')?>>MSN</option>
                        </select>
                    </div>

                    <div style="height: 64px; overflow: visible;" class="form-group text-right col-md-3 <?php if ($this->registration_setting->isRegistrationFieldRequired('Instant Message 1')) echo 'required'?>">
                        <?php if($bootstrap_style  == 'bootstrap.paper2.css'){ ?>

                            <label for="im_account" class="control-label pull-left">聊天软件 IM1</label>

                        <?php } else { ?>

                            <label for="im_account" class="control-label pull-left"><?=lang('aff.ai17')?></label>

                        <?php } ?>
                        <div class="clearfix"></div>
                        <input id="im_account" name="im_account" <?=( $placeholder_hint ) ? 'placeholder="'. lang('reg.im') .'"' : 'placeholder = "'.lang('reg.34').'" title="' . lang('reg.33') . '"' ?>  class="form-control" data-toggle="tooltip" type="text" value="<?=set_value('im_account')?>" <?php if ( ! set_value('im_account')) echo 'disabled="disabled"'; ?>>
                    </div>
                <?php } ?>



                <?php # IM 2 ?>
                <?php if ($this->registration_setting->isRegistrationFieldVisible('Instant Message 2')) { ?>
                    <div style="height: 64px; overflow: visible;" class="form-group text-right col-md-3 <?php if ($this->registration_setting->isRegistrationFieldRequired('Instant Message 2')) echo 'required'?>">
                        <?php if($bootstrap_style  == 'bootstrap.paper2.css'){ ?>

                            <label for="im_type2" class="control-label pull-left">聊天软件2类型 IM2 Type</label>

                        <?php } else { ?>

                            <label for="im_type2" class="control-label pull-left"><?=lang('aff.ai18')?></label>

                        <?php } ?>
                        <div class="clearfix"></div>
                        <select id="im_type2" name="im_type2" title="<?=( $placeholder_hint ) ? '' : lang('reg.32')?>" class="form-control" data-toggle="tooltip" onchange="$('#im_account2').prop('disabled', $(this).val() == '');">
                            <option value=""><?=lang('reg.58')?></option>
                            <option value="QQ" <?=set_select('im_type2', 'QQ')?>>QQ</option>
                            <option value="Skype" <?=set_select('im_type2', 'Skype')?>>Skype</option>
                            <option value="MSN" <?=set_select('im_type2', 'MSN')?>>MSN</option>
                        </select>
                    </div>

                    <div style="height: 64px; overflow: visible;" class="form-group text-right col-md-3 <?php if ($this->registration_setting->isRegistrationFieldRequired('Instant Message 2')) echo 'required'?>">
                        <?php if($bootstrap_style  == 'bootstrap.paper2.css'){ ?>

                            <label for="im_account2" class="control-label pull-left">聊天软件2 IM2</label>

                        <?php } else { ?>

                            <label for="im_account2" class="control-label pull-left"><?=lang('aff.ai19')?></label>

                        <?php } ?>
                        <div class="clearfix"></div>
                        <input id="im_account2" name="im_account2" <?=( $placeholder_hint ) ? 'placeholder="'. lang('reg.im') .'"' : 'placeholder = "'.lang('reg.34').'" title="' . lang('reg.33') . '"' ?> class="form-control" data-toggle="tooltip" type="text" value="<?=set_value('im_account2')?>" <?php if ( ! set_value('im_account2')) echo 'disabled="disabled"'; ?>>
                    </div>
                <?php } ?>



                <?php # REFERRAL CODE ?>
                <?php if ($this->registration_setting->isRegistrationFieldVisible('Referral Code')) { ?>
                    <div style="height: 64px; overflow: visible;" class="form-group text-right col-md-3 <?php if ($this->registration_setting->isRegistrationFieldRequired('Referral Code')) echo 'required'?>">
                        <?php if($bootstrap_style  == 'bootstrap.paper2.css'){ ?>

                            <label for="referral_code" class="control-label pull-left">邀请码 Referral Code</label>

                        <?php } else { ?>

                            <label for="referral_code" class="control-label pull-left"><?=lang('reg.44')?></label>

                        <?php } ?>
                        <div class="clearfix"></div>
                        <input id="referral_code" name="referral_code" <?=( $placeholder_hint ) ? 'placeholder="'. lang('reg.referral') .'"' : 'placeholder = "'.lang('reg.44').'" title="' . lang('reg.60') . '"' ?>  class="form-control" data-toggle="tooltip" type="text" value="<?=set_value('referral_code')?>" onchange="checkReferral();">
                    </div>
                <?php } ?>



                <?php # AFFILIATE CODE ?>
                <?php if ($this->registration_setting->isRegistrationFieldVisible('Affiliate Code') && empty($tracking_code)) {?>
                    <div style="height: 64px; overflow: visible;" class="form-group text-right col-md-3 <?php if ($this->registration_setting->isRegistrationFieldRequired('Affiliate Code')) echo 'required'?>">
                        <?php if($bootstrap_style  == 'bootstrap.paper2.css'){ ?>

                            <label for="affiliate_code" class="control-label pull-left">代理code Affiliate Code</label>

                        <?php } else { ?>

                            <label for="affiliate_code" class="control-label pull-left"><?=lang('reg.62')?></label>

                        <?php } ?>
                        <div class="clearfix"></div>
                        <input id="affiliate_code" name="affiliate_code" <?=( $placeholder_hint ) ? 'placeholder="'. lang('reg.affiliate') .'"' : 'placeholder = "'.lang('reg.62').'" title="' . lang('reg.61') . '"' ?> class="form-control" data-toggle="tooltip" type="text" value="<?=set_value('affiliate_code')?>" onchange="checkAffiliate();">
                    </div>
                <?php } ?>



                <?php # CAPTCHA CODE ?>
                <?php if ($captcha_registration) {?>
                    <div class="form-group col-md-12">
                        <?php if($bootstrap_style  == 'bootstrap.paper2.css'){ ?>

                            <label for="captcha" class="control-label pull-left required">验证码 Captcha</label>

                        <?php } else { ?>

                            <label for="captcha" class="control-label pull-left required"><?=lang('label.captcha')?></label>

                        <?php } ?>
                        <div class="clearfix"></div>
                        <div class="form-inline">
                            <input id="captcha" name="captcha" placeholder="<?=lang('label.captcha')?>" title="<?=( $placeholder_hint ) ? '' : lang('label.captcha')?>" class="form-control" data-toggle="tooltip" type="text">
                            <a href="javascript:void(0)" onclick="refreshCaptcha()"><i class="glyphicon glyphicon-refresh"></i></a>
                            <span id="loading_captcha"><?=lang('text.loading')?></span>
                            <img id="image_captcha" src="<?=site_url('/iframe/auth/captcha?' . random_string('alnum'))?>" width="120" height="40" onload="$('#loading_captcha').hide()">
                        </div>
                    </div>
                <?php } ?>

            </div>

        </form>
        <div>
        <?php if($this->utils->getConfig('registration_hint')) echo $this->utils->getConfig('registration_hint'); ?>
        </div>
    </div>

    <div class="panel-footer text-right">
        <?php

         if ($this->registration_setting->isRegistrationFieldVisible('At Least 18 Yrs. Old and Accept Terms and Conditions')) { ?>
            <div class="form-group" style="margin-bottom: 0;">
                <div class="checkbox pull-left">
                    <label>
                        <input type="checkbox" name="terms" id="terms" form="registration_form" <?=set_checkbox('terms', 'on')?>>
                        <?=lang('reg.45')?>
                        <a href="<?=site_url('/pub/go_site/_terms_and_conditions.html')?>" target="_block"><?=lang('reg.46')?></a>
                        <div class="help-block" id="terms-help-block"></div>
                    </label>
                </div>
            </div>
        <?php } ?>
        <button type="submit" id="accept" class="btn btn-primary btn-sm" form="registration_form"><?=lang('reg.47')?></button>
    </div>

</div>

<script src="<?= $this->utils->jsUrl('player/player.js')?>"></script>
<script src="<?= $this->utils->jsUrl('moment.js')?>"></script>


<?php # VARIABLES ?>
<script type="text/javascript">
    var validation_rules = [

        <?php # USERNAME ?>
        {
            selector: '#username',
            triggerEvents: 'blur',
            validate: [
                'presence',
                'between-length:<?=$min_username_length?>:<?=$max_username_length?>',
                // new RegExp('^[a-z0-9.]+$'),
                new RegExp('^((?!(<?=implode("|", $forbidden_names)?>)).)*$'),
                'unique:username',
            ],
            errorMessage: [
                '<?php printf(lang("gen.error.required"), lang("reg.03"))?>',
                '<?php printf(lang("gen.error.between"), lang("reg.03"), $min_username_length, $max_username_length)?>',
                // '<?php printf(lang("gen.error.character"), lang("reg.03"))?>',
                '<?php printf(lang("gen.error.forbidden"), lang("reg.03"))?>',
                '<?php printf(lang("gen.error.exist"), lang("reg.03"))?>',
            ],
        },




        <?php # PASSWORD ?>
        {
            selector: '#password',
            triggerEvents: 'blur',
            validate: [
                'presence',
                'between-length:<?=$min_password_length?>:<?=$max_password_length?>',
                new RegExp('^[a-z0-9.]+$'),
            ],
            errorMessage: [
                '<?php printf(lang("gen.error.required"), lang("reg.05"))?>',
                '<?php printf(lang("gen.error.between"), lang("reg.05"), $min_password_length, $max_password_length)?>',
                '<?php printf(lang("gen.error.between.lowercase.alphanum"), lang("reg.05"), $min_password_length, $max_password_length)?>',
            ],
        },




        <?php # CONFIRM PASSWORD ?>
        {
            selector: '#cpassword',
            triggerEvents: 'blur',
            validate: [
                'presence',
                'same-as:#password',
            ],
            errorMessage: [
                '<?php printf(lang("gen.error.required"), lang("reg.07"))?>',
                '<?php printf(lang("gen.error.mismatch"), lang("reg.05"), lang("reg.07"))?>',
            ],
        },




        <?php # EMAIL ?>
        {
            selector: '#email',
            triggerEvents: 'blur',
            validate: [
                'presence',
                'email',
                'unique:email',
            ],
            errorMessage: [
                '<?php printf(lang("gen.error.required"), lang("reg.18"))?>',
                '<?php printf(lang("gen.error.invalid"), lang("reg.18"))?>',
                '<?php printf(lang("gen.error.exist"), lang("reg.18"))?>',
            ],
        },


        <?php # FIRST NAME ?>
        <?php if ($this->registration_setting->isRegistrationFieldRequired('First Name')) { ?>
            {
                selector: '#first_name',
                triggerEvents: 'blur',
                validate: [
                    'presence',
                    new RegExp("^[ -'a-zA-Z\u4e00-\u9fff]+$"),
                ],
                errorMessage: [
                    '<?php printf(lang("gen.error.required"), lang("reg.10"))?>',
                    '<?php printf(lang("gen.error.character"), lang("reg.03"))?>',
                ],
            },
        <?php } ?>




        <?php # LAST NAME ?>
        <?php if ($this->registration_setting->isRegistrationFieldRequired('Last Name')) { ?>
            {
                selector: '#last_name',
                triggerEvents: 'blur',
                validate: [
                    'presence',
                    new RegExp("^[ -'a-zA-Z\u4e00-\u9fff]+$"),
                ],
                errorMessage: [
                    '<?php printf(lang("gen.error.required"), lang("reg.11"))?>',
                    '<?php printf(lang("gen.error.character"), lang("reg.03"))?>',
                ],
            },
        <?php } ?>




        <?php # BIRTHDAY ?>
        <?php if ($this->registration_setting->isRegistrationFieldRequired('Birthday')) { ?>
             {
                selector: '#birthdate',
                validate: [
                    'presence',
                    'check-birthday',
                    new RegExp('^\\d{4}-\\d{2}-\\d{2}$'),
                ],
                errorMessage: [
                    '<?php printf(lang("gen.error.required"), lang("reg.12"))?>',
                    '<?php printf(lang("gen.error.invalid"), lang("reg.12"))?>',
                    '<?php printf(lang("gen.error.invalid"), lang("reg.12"))?>',
                ],
            },
        <?php } ?>




        <?php # GENDER ?>
        <?php if ($this->registration_setting->isRegistrationFieldRequired('Gender')) { ?>
            {
                selector: '.gender',
                triggeredBy: '.gender',
                validate: 'some-radio',
                errorMessage: '<?php printf(lang("gen.error.required"), lang("reg.15"))?>',
            },
        <?php } ?>




        <?php # NATIONALITY ?>
        <?php if ($this->registration_setting->isRegistrationFieldRequired('Nationality')) { ?>
            {
                selector: '#citizenship',
                triggerEvents: 'blur',
                validate: 'presence',
                errorMessage: '<?php printf(lang("gen.error.required"), lang('reg.22'))?>',
            },
        <?php } ?>




        <?php # BIRTH PLACE ?>
        <?php if ($this->registration_setting->isRegistrationFieldRequired('BirthPlace')) { ?>
            {
                selector: '#birthplace',
                triggerEvents: 'blur',
                validate: 'presence',
                errorMessage: '<?php printf(lang("gen.error.required"), lang('reg.24'))?>',
            },
        <?php } ?>




        <?php # LANGUAGE ?>
        <?php if ($this->registration_setting->isRegistrationFieldRequired('Language')) { ?>
            {
                selector: '#language',
                defaultStatus: 'valid',
                triggerEvents: 'blur',
                validate: 'presence',
                errorMessage: '<?php printf(lang("gen.error.required"), lang("reg.26"))?>',
            },
        <?php } ?>




        <?php # CONTACT NUMBER ?>
        <?php if ($this->registration_setting->isRegistrationFieldRequired('Contact Number')) { ?>
            {
                selector: '#contact_number',
                triggerEvents: 'blur',
                defaultStatus: 'valid',
                validate: [
                    'presence',
                    new RegExp('^\\d*$'),
                    'unique:contact_number'
                ],
                errorMessage: [
                    '<?php printf(lang("gen.error.required"), lang("reg.29"))?>',
                    '<?php printf(lang("gen.error.character"), lang("reg.29"))?>',
                    '<?php printf(lang("gen.error.exist"), lang("reg.29"))?>'
                ],
            },
        <?php } ?>




        <?php # IM 1 ?>
        <?php if ($this->registration_setting->isRegistrationFieldRequired('Instant Message 1')) { ?>
            {
                selector: '#im_account',
                triggerEvents: 'blur',
                defaultStatus: 'valid',
                triggeredBy: ['#im_type','#im_type2'],
                validate:[
                    'check-same-type',
                    'imType:#im_type:#im_account',
                    'qq:#im_type',
                    'skype:#im_type',
                    'msn:#im_type',
                ],
                errorMessage: [
                    '<?php printf(lang("gen.error.same"), lang("reg.31") . " 1", lang("reg.31") . " 2")?>',
                    '<?php printf(lang("gen.error.required"), lang("reg.34") . " 1")?>',
                    '<?php printf(lang("gen.error.character"), lang("reg.34") . " 1")?>',
                    '<?php printf(lang("gen.error.invalid"), lang("reg.34") . " 1")?>',
                    '<?php printf(lang("gen.error.invalid"), lang("reg.34") . " 1")?>',
                ],
            },
        <?php } ?>




        <?php # IM 2 ?>
        <?php if ($this->registration_setting->isRegistrationFieldRequired('Instant Message 2')) { ?>
            {
                selector: '#im_account2',
                triggerEvents: 'blur',
                defaultStatus: 'valid',
                triggeredBy: ['#im_type','#im_type2'],
                validate:[
                    'check-same-type',
                    'imType:#im_type2:#im_account2',
                    'qq:#im_type2',
                    'skype:#im_type2',
                    'msn:#im_type2',
                ],
                errorMessage: [
                    '<?php printf(lang("gen.error.same"), lang("reg.31") . " 2", lang("reg.31") . " 1")?>',
                    '<?php printf(lang("gen.error.required"), lang("reg.34") . " 2")?>',
                    '<?php printf(lang("gen.error.character"), lang("reg.34") . " 2")?>',
                    '<?php printf(lang("gen.error.invalid"), lang("reg.34") . " 2")?>',
                    '<?php printf(lang("gen.error.invalid"), lang("reg.34") . " 2")?>',
                ],
            },
        <?php } ?>

        <?php # WITHDRAWAL PASSWORD ?>
        {
            selector: '#withdrawal_password',
            triggerEvents: 'blur',
            validate: [
                'presence',
                'between-length:<?=$min_password_length?>:<?=$max_password_length?>',
                new RegExp('^[a-z0-9.]+$'),
            ],
            errorMessage: [
                '<?php printf(lang("gen.error.required"), lang("Withdrawal Password"))?>',
                '<?php printf(lang("gen.error.between"), lang("Withdrawal Password"), $min_password_length, $max_password_length)?>',
                '<?php printf(lang("gen.error.between.lowercase.alphanum"), lang("Withdrawal Password"), $min_password_length, $max_password_length)?>',
            ],
        },

        <?php # CONFIRM WITHDRAWAL PASSWORD ?>
        {
            selector: '#cwithdrawal_password',
            triggerEvents: 'blur',
            validate: [
                'presence',
                'same-as:#withdrawal_password',
            ],
            errorMessage: [
                '<?php printf(lang("gen.error.required"), lang("Confirm Withdrawal Password"))?>',
                '<?php printf(lang("gen.error.mismatch"), lang("Withdrawal Password"), lang("Confirm Withdrawal Password"))?>',
            ],
        },


        <?php # SECURITY QUESTION ?>
        <?php if ($this->registration_setting->isRegistrationFieldRequired('Security Question')) { ?>
            {
                selector: '#security_question',
                triggerEvents: 'blur',
                validate: 'presence',
                errorMessage: '<?php printf(lang("gen.error.required"), lang("reg.35"))?>',
            },
            {
                selector: '#security_answer',
                triggerEvents: 'blur',
                validate: 'presence',
                errorMessage: '<?php printf(lang("gen.error.required"), lang("reg.42"))?>',
            },
        <?php } ?>




        <?php # REFERRAL CODE ?>
        <?php if ($this->registration_setting->isRegistrationFieldRequired('Referral Code')) { ?>
            {
                selector: '#referral_code',
                triggerEvents: 'blur',
                defaultStatus: 'valid',
                validate: 'unique:referral_code',
                errorMessage: '<?php printf(lang("gen.error.not_exist"), lang("reg.44"))?>',
            },
        <?php } ?>




        <?php # AFFILIATE CODE ?>
        <?php if ($this->registration_setting->isRegistrationFieldRequired('Affiliate Code') && empty($tracking_code)) { ?>

        <?php } ?>




        <?php # TERMS ?>
        <?php if ($this->registration_setting->isRegistrationFieldRequired('At Least 18 Yrs. Old and Accept Terms and Conditions')) { ?>
            {
                selector: '#terms',
                validate: 'checked',
                errorMessage: '<?php printf(lang("gen.error.required"), lang("reg.46"))?>',
            },
        <?php } ?>




        <?php # RESIDENT COUNTRY ?>
        <?php if ($this->registration_setting->isRegistrationFieldRequired('Resident Country')) { ?>
            {
                selector: '#resident_country',
                triggerEvents: 'blur',
                validate: 'presence',
                errorMessage: '<?php printf(lang("gen.error.required"), lang("a_reg.33"))?>',
            },
        <?php } ?>




        <?php # VERIFICATION CODE ?>
        <?php if ($this->registration_setting->isRegistrationFieldVisible('Contact Number') && $this->registration_setting->isRegistrationFieldRequired('SMS Verification Code')  && ! $this->utils->getConfig('disabled_sms')) { ?>
            {
                selector: '#sms_verification',
                triggerEvents: 'blur',
                defaultStatus: 'valid',
                validate: 'presence',
                errorMessage: '<?php printf(lang("gen.error.required"), lang("SMS Verification Code"))?>',
            },
        <?php } ?>




        <?php # CAPTCHA CODE ?>
        <?php if ($this->registration_setting->isRegistrationFieldRequired('Captcha Code')) { ?>
            {
                selector: '#captcha',
                triggerEvents: 'blur',
                validate: [
                    'presence',
                    'between-length:<?=$captcha_length?>:<?=$captcha_length?>',
                ],
                errorMessage: [
                    '<?php printf(lang("gen.error.required"), lang("label.captcha"))?>',
                    '<?php printf(lang("gen.error.between"), lang("label.captcha"), $captcha_length, $captcha_length)?>',
                ],
            },
        <?php } ?>

    ];
</script>




<?php # FUNCTIONS ?>
<script type="text/javascript">
    function refreshCaptcha(){
        $("#loading_captcha").show();
        $('#image_captcha').attr('src','<?= site_url('/iframe/auth/captcha')?>?' + Math.random());
    }

    // Handling of SMS verification code
    var sendSmsVerification = function() {
        $('#sms_verification_msg').text('<?= lang('Please wait') . '...'; ?>');
        $('#sms_verification_msg').show();

        var mobileNumber = $('#contact_number').val();
        if(!mobileNumber || mobileNumber == '') {
            $('#sms_verification_msg').text('<?= lang('Please fill in mobile number')?>');
            $('#contact_number').focus();
            return;
        }

        $.getJSON('<?= site_url('iframe_module/iframe_register_send_sms_verification')?>/' + mobileNumber, function(data){
            if(data.success) {
                $('#sms_verification_msg').text('<?= lang('SMS sent')?>');
            }
            else {
                $('#sms_verification_msg').text('<?= lang('SMS failed')?>');
            }
        });
    }
</script>




<?php # INITIALIZATION ?>
<script type="text/javascript">
    var n;
    $(document).ready(function() {

        n               = nod(),
        checkEmail      = nod.checkFunctions.email(),
        checkInteger    = nod.checkFunctions.integer();

        checkReferral();
        checkAffiliate();

        n.configure({
            form:               '#registration_form',
            delay:              0,
            preventSubmit:      true,
            parentClass:        'form-group',
            errorClass:         'has-error',
            errorMessageClass:  'help-block'
        });

        nod.checkFunctions['unique'] = function (field) {
            return function (callback, value) {

                if (value.length) {
                    callback(true);
                }

                var params = {};
                    params[field] = value;

                $.getJSON('/iframe/auth/validate/' + field, params, function(data) {
                    callback(data.result);
                    // if(data.result && value.length >3 && value.length <10){
                    //     $("#usernameAvailable").text("<?=lang('reg.username.available')?>").show();
                    // } else {
                    //     $("#usernameAvailable").hide();
                    // }

                });
            };
        };

        nod.checkFunctions['qq'] = function (selector) {
            return function (callback, value) {
                var imType = $(selector).val();
                if (value && imType == 'QQ') {
                    checkInteger(callback, value);
                } else {
                    callback(true);
                }
            };
        };

        nod.checkFunctions['skype'] = function (selector) {
            return function (callback, value) {
                callback(true);
            };
        };

        nod.checkFunctions['msn'] = function (selector) {
            return function (callback, value) {
                var imType = $(selector).val();
                if (value && imType == 'MSN') {
                    checkEmail(callback, value);
                } else {
                    callback(true);
                }
            };
        };

        nod.checkFunctions['check-same-type'] = function () {
            return function (callback, value) {
                var imType1 = $('#im_type').val();
                var imType2 = $('#im_type2').val();
                callback( ! imType1 || ! imType2 || (imType1 && imType2 && imType1 != imType2));
            };
        };

        nod.checkFunctions['imType'] = function (typeSelector, acoountSelector) {
            return function (callback, value) {
                var imType = $(typeSelector).val();
                var imAcoount = $(acoountSelector).val();
                callback(imType == null || imType.length == 0 || (imType.length > 0 && imAcoount));
            };
        };

        nod.checkFunctions['check-birthday'] = function () {
            return function (callback, value) {
                var birthdate = moment(value);
                var age = moment().diff(birthdate, 'years');
                callback(age >= 18);
            };
        }

        n.add(validation_rules);

        n.setMessageOptions([
            <?php if ($this->registration_setting->isRegistrationFieldRequired('Gender')) { ?>
                {
                    selector: '.gender',
                    errorSpan: '#gender-help-block',
                },
            <?php } ?>

            <?php if ($this->registration_setting->isRegistrationFieldRequired('At Least 18 Yrs. Old and Accept Terms and Conditions')) { ?>
                {
                    selector: '#terms',
                    errorSpan: '#terms-help-block',
                }
            <?php } ?>
        ]);

        $('#birthdate').change( function() {
            n.performCheck('#birthdate');
        });

        $('.gender').change( function() {
            n.performCheck('.gender');
        });

        $('#send_sms_verification').click(sendSmsVerification);

        $('#accept').click( function() {
            if (n.areAll('valid')) {
                $(this).val('<?=lang("text.loading")?>').addClass('disabled');
                return true;
            }
        });

        <?php if ($this->input->post()) { ?>
            n.performCheck();
        <?php } ?>

    });
</script>
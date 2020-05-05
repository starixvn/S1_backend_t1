<?php
    $standard_js = [
        //for template public
        site_url('/pub/variables?v=' . PRODUCTION_VERSION),
        $this->utils->playerResUrl('template_header.js'),
    ];

    $standard_css = [
    ];

    $lang_now= $this->language_function->getCurrentLangForPromo();
?>
<?php if ($this->utils->isEnabledFeature('enable_communication_preferences') && !empty($this->utils->getConfig('communication_preferences'))) :?>
    <link rel="stylesheet" type="text/css" href="<?=$this->utils->thirdpartyUrl('bootstrap-switch/3.3.4/css/bootstrap3/bootstrap-switch.min.css')?>" />
    <script type="text/javascript" src="<?=$this->utils->thirdpartyUrl('bootstrap-switch/3.3.4/js/bootstrap-switch.min.js');?>"></script>
<?php endif;?>

<div id="accountInformation" class="tab-pane main-content">
    <h1><?=lang("Account Information") ?></h1>
    <?php if ($this->utils->isEnabledFeature('enable_communication_preferences') && !empty($this->utils->getConfig('communication_preferences')) && $registration_fields['Player Preference']['account_visible'] == Registration_setting::VISIBLE) :?>
        <ul class="row fm-ul display_tablist" role="tablist">
            <li class="col-xs-4 col-sm-3 active">
                <a data-toggle="tab" href="#basicInfoTab" id="basic_info_btn"  aria-expanded="false"><?=lang("Basic Information")?></a>
            </li>
            <li class="col-xs-4 col-sm-3">
                <a data-toggle="tab" href="#playerPrefTab" id="player_pref_btn"  aria-expanded="false"><?=lang("pi.player_pref")?></a>
            </li>
        </ul>
    <?php endif;?>

    <div class="tab-content">
        <!-- BASIC INFORMATION SECTION START -->
        <div id="basicInfoTab" class="tab-pane fade in active">
            <div class="row">
                <form id="frmEditPlayer" class="col-md-12" action="<?php echo site_url('/player_center/postEditPlayer'); ?>" method="post" enctype="multipart/form-data" >
                    <div class="form-group col-md-6 col-sm-6 info-left">
                        <?php if (!$this->utils->isEnabledFeature('hidden_avater_upload')) :?>
                            <p><?=lang("Profile Picture") ?>:</p>
                            <div class="avatar-container">
                                <div class="avatar">
                                    <?php  if (isset($profilePicture)) : ?>
                                        <img src="<?=$profilePicture?>" id="imgProfilePicture" alt="<?php echo $this->authentication->getUsername(); ?>"/>
                                    <?php  else : ?>
                                        <img src="<?=base_url() . $this->utils->getPlayerCenterTemplate() . '/img/default-profile.png' ?>" id="imgProfilePicture" alt="<?php echo $this->authentication->getUsername(); ?>"/>
                                    <?php  endif; ?>
                                </div>
                                <div class="upload-avatar">
                                    <label class="btn btn-default btn-file">
                                        <span><?=lang("Upload Image") ?></span>
                                        <input type="file" name="profileToUpload[]" id="profileToUpload" onchange="AccountInformation.previewProfilePicture(this);" accept="image/*" hidden>
                                        <input type="hidden" name="has_profileToUpload" value="0">
                                    </label>
                                    <br/>
                                    <span id="fileNameToUpload">
                                        <?=lang('Same dimension required')?>
                                        <br/>
                                        (<?=lang('e.g. 200x200, 350x350')?>)
                                    </span>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (in_array('FIRSTNAME', $fields)) : ?>
                            <p class="accountinfo field">
                                <?= $this->player_functions->checkAccount_displaySymbolHint('First Name')?>
                                <?=lang("First Name") ?>:
                                <?= $this->player_functions->checkAccount_displayInputHint('firstName')?>
                            </p>
                            <input type="text" class="form-control" name="name" id="name" class="form-control"
                                   value="<?= !empty($player['firstName']) ? ucfirst($player['firstName']) : '' ?>"
                                   placeholder="<?=lang('pi.1') . ' ' . $this->player_functions->checkAccount_displayPlaceholderHint('First Name')?>"
                                <?= $this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'firstName') ? '' : 'disabled' ; ?>
                            />
                            <?= form_error('name', '<p class="form__error">', '</p>'); ?>
                            <span class="help-block"><?=lang('notify.108');?></span>
                        <?php endif; ?>

                        <?php if (in_array('LASTNAME', $fields)) : ?>
                            <p class="accountinfo field">
                                <?= $this->player_functions->checkAccount_displaySymbolHint('Last Name')?>
                                <?=lang("Last Name") ?>:
                            </p>
                            <input type="text" class="form-control" name="lastname" id="lname" class="form-control"
                                   value="<?= !empty($player['lastName']) ? ucfirst($player['lastName']) : '' ?>"
                                   placeholder="<?=lang('Last Name') . ' ' . $this->player_functions->checkAccount_displayPlaceholderHint('Last Name')?>"
                                <?= $this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'lastName') ? '' : 'disabled' ; ?>
                            />
                            <?= form_error('lastName', '<p class="form__error">', '</p>'); ?>
                        <?php endif; ?>

                        <?php if (in_array('GENDER', $fields)) : ?>
                            <p class="accountinfo field">
                                <?= $this->player_functions->checkAccount_displaySymbolHint('Gender')?>
                                <?=lang("Gender") ?>:
                            </p>
                            <div class="radio">
                                <?php if (!$this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'gender')): ?>
                                    <input type="text" id="gender" class="form-control" value="<?=lang($player['gender'])?>" disabled>
                                <?php else: ?>
                                    <input type="hidden" name="gender" class="form-control" value="">
                                    <label>
                                        <input type="radio" name="gender" id="gender_m" value="Male" <?= ($player['gender'] == 'Male') ? 'checked' : '' ?>/><?=lang('Male')?>
                                    </label>
                                    <label>
                                        <input type="radio" name="gender" id="gender_f" value="Female" <?= ($player['gender'] == 'Female') ? 'checked' : '' ?>><?=lang('Female')?>
                                    </label>
                                <?php endif;?>
                                <?= form_error('gender', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (in_array('BIRTHDATE', $fields)) : ?>
                            <?php
                            $today = new DateTime('now');
                            $validBday = $today->modify('-18 year')->format('Y-m-d');
                            ?>
                            <p class="accountinfo field">
                                <?= $this->player_functions->checkAccount_displaySymbolHint('Birthday')?>
                                <?=lang("Birthday") ?>:
                            </p>
                            <?php if ($this->operatorglobalsettings->getSettingIntValue('birthday_option') == 2 || !empty($player['birthdate'])) : ?>
                                <input type="date" name="birthdate" id="birthdate" class="datepicker player-birthday form-control input-sm" max="<?=$validBday?>"
                                       value="<?=(set_value('birthdate') != null) ? set_value('birthdate') : $player['birthdate'];?>"
                                       oninvalid="setCustomValidity('<?= lang('validation.ageValidation') ?>')"
                                    <?= $this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'birthdate') ? '' : 'disabled' ; ?>
                                />
                            <?php else : ?>
                                <input type="<?= empty(trim($player['birthdate'])) ? "hidden" : "text" ?>" name="birthdate" id="birthdate" class="player-birthday form-control input-sm"
                                       value="<?=(set_value('birthdate') != null) ? set_value('birthdate') : $player['birthdate'];?>"
                                />

                                <div class="col-md-4 birthday-option">
                                    <div class="custom-dropdown">
                                        <select class="selectbox form-control registration-field" id="dob_year" name="dob_year" onchange="AccountInformation.validateDOB()">
                                            <option value=""><?= lang('reg.14') ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 lr-padding birthday-option">
                                    <div class="custom-dropdown ">
                                        <select class="selectbox form-control registration-field" id="dob_month" name="dob_month" onchange="AccountInformation.validateDOB()">
                                            <option value=""><?= lang('reg.59') ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 lr-padding birthday-option">
                                    <div class="custom-dropdown ">
                                        <select class="selectbox form-control registration-field" id="dob_day" name="dob_day" onchange="AccountInformation.validateDOB()">
                                            <option value=""><?= lang('reg.13') ?></option>
                                        </select>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?= form_error('birthdate', '<p class="form__error">', '</p>'); ?>
                            <div class="clearfix"></div>
                        <?php endif; ?>

                        <?php if (in_array('BIRTHPLACE', $fields)) : ?>
                            <p class="accountinfo field">
                                <?= $this->player_functions->checkAccount_displaySymbolHint('BirthPlace')?>
                                <?=lang("reg.24") ?>:
                            </p>
                            <input type="text" class="form-control"  name="birthplace" id="birthplace" class="form-control"
                                   value="<?= !empty($player['birthplace']) ? ucfirst($player['birthplace']) : "" ?>"
                                   placeholder="<?=lang('reg.24') . ' ' . $this->player_functions->checkAccount_displayPlaceholderHint('BirthPlace')?>"
                                <?= $this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'birthplace') ? '' : 'disabled' ; ?>
                            >
                            <?= form_error('birthplace', '<p class="form__error">', '</p>'); ?>
                        <?php endif; ?>

                        <?php if (in_array('CITIZENSHIP', $fields)) : ?>
                            <p class="accountinfo field">
                                <?= $this->player_functions->checkAccount_displaySymbolHint('Nationality')?>
                                <?=lang("player.61") ?>:
                            </p>
                            <div class="custom-dropdown">
                                <select id="nationality_list" class="form-control registration-field" name="citizenship"
                                    <?= $this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'citizenship') ? '' : 'disabled' ; ?>
                                >
                                    <option value=""><?= lang('reg.22') . ' ' . $this->registration_setting->displayPlaceholderHint('Nationality')?></option>
                                    <?php if(!$this->utils->isEnabledFeature('disable_frequently_use_country_in_registration')) : ?>
                                        <optgroup label="<?=lang('lang.frequentlyUsed')?>">
                                            <?php foreach ($this->utils->getCommonCountryList() as $key) {?>
                                                <option value="<?=$key?>" <?= ($player['citizenship'] == $key) ? "selected" : ""; ?>><?=lang('country.' . $key)?></option>
                                            <?php } ?>
                                        </optgroup>
                                    <?php endif; ?>
                                    <optgroup label="<?=lang('lang.alphabeticalOrder')?>">
                                        <?php foreach ($this->utils->getCountryList() as $key) {?>
                                            <option value="<?=$key?>" <?= ($player['citizenship'] == $key) ? "selected" : ""; ?>><?=lang('country.' . $key)?></option>
                                        <?php } ?>
                                    </optgroup>
                                </select>
                            </div>

                            <?= form_error('citizenship', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                        <?php endif; ?>

                        <?php if (in_array('LANGUAGE', $fields)) : ?>
                            <p class="accountinfo field">
                                <?= $this->player_functions->checkAccount_displaySymbolHint('Language')?>
                                <?=lang("player.62") ?>:
                            </p>
                            <select class="form-control input-sm" name="language" <?= $this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'language') ? '' : 'disabled' ; ?>>
                                <?php foreach(Language_function::PlayerSupportLanguageNames() as $lang_key => $lang_value): ?>
                                    <option value="<?=$lang_key?>" <?=(strtolower($player['language']) == strtolower($lang_key)) ? "selected":''?>><?php echo $lang_value;?></option>
                                <?php endforeach ?>
                            </select>
                            <?= form_error('language', '<p class="form__error">', '</p>'); ?>
                        <?php endif; ?>

                        <?php if (in_array('EMAIL', $fields)) : ?>
                            <?php
                            $obfuscatedEmail = '';
                            if(!empty($player['email']) && $this->utils->isEnabledFeature('enabled_show_player_obfuscated_email')){
                                $em   = explode("@",$player['email']);
                                $name = implode(array_slice($em, 0, count($em)-1), '@');
                                $len  = floor(strlen($name)/2);
                                $obfuscatedEmail = substr($name,0, $len) . str_repeat('*', $len) . "@" . end($em);
                            }

                            $_email = !empty($player['email']) ? (($obfuscatedEmail) ? $obfuscatedEmail : $player['email']) : "";
                            # Enable verification email and email verified
                            $_email_disable_edit = ($_email && $player['verified_email']);
                            ?>
                            <p class="accountinfo field">
                                <?= $this->player_functions->checkAccount_displaySymbolHint('Email')?>
                                <?=lang("Email") ?>:
                            </p>
                            <input type="email" name="email" class="form-control" id="fm-cn"
                                   value="<?= $_email ?>"
                                   placeholder="<?=lang('lang.email') . ' ' . $this->player_functions->checkAccount_displayPlaceholderHint('Email')?>"
                                <?= $this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'email', $_email_disable_edit) ? '' : 'disabled' ; ?>
                            />
                        <?php endif; ?>

                        <?php if (in_array('DIALING_CODE', $fields)) : ?>
                            <?php
                            $countryNumList = unserialize(COUNTRY_NUMBER_LIST_FULL);
                            $requireDialingCode = ($registration_fields['Dialing Code']['account_required'] == Registration_setting::REQUIRED) ? 1 : 0;
                            ?>
                            <p class="accountinfo field">
                                <?= $this->player_functions->checkAccount_displaySymbolHint($registration_fields['Dialing Code']['field_name'])?>
                                <?=lang("Dialing Code") ?>:
                            </p>
                            <select id="dialing_code" class="form-control input-sm <?=($requireDialingCode) ? 'required' : ''?>" name="dialing_code"
                                <?= $this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'dialing_code') ? '' : 'disabled' ; ?>
                            >
                                <option title="<?=lang('reg.77')?><?= $this->registration_setting->displayPlaceholderHint('Dialing Code')?>" country="" value="">
                                    <?=lang('reg.77') . ' ' . $this->registration_setting->displayPlaceholderHint('Dialing Code')?>
                                </option>
                                <?php foreach ($countryNumList as $country => $nums) : ?>
                                    <?php if (is_array($nums)) : ?>
                                        <?php foreach ($nums as $_nums) : ?>
                                            <option title="(+<?=$_nums?>)" country="<?=$country?>" value="<?=$_nums?>" <?= (set_value('dialing_code', $player['dialing_code']) == $_nums) ? 'selected' : '' ; ?>>
                                                <?= sprintf("%s (+%s)", lang('country.'.$country), $_nums);?>
                                            </option>
                                        <?php endforeach ; ?>
                                    <?php else : ?>
                                        <option title="(+<?=$nums?>)" country="<?=$country?>" value="<?=$nums?>" <?= (set_value('dialing_code', $player['dialing_code']) == $nums) ? 'selected' : '' ; ?>>
                                            <?= sprintf("%s (+%s)", lang('country.'.$country), $nums); ?>
                                        </option>
                                    <?php endif ; ?>
                                <?php endforeach ; ?>
                            </select>
                        <?php endif; ?>

                        <?php if (in_array('CONTACTNUMBER', $fields)) : ?>
                            <?php
                            $obfuscatedPhone = '';
                            if (!empty($player['contactNumber']) && $this->utils->isEnabledFeature('enabled_show_player_obfuscated_phone')) {
                                $obfuscatedPhone = $this->utils->keepOnlyString($player['contactNumber'], -4);
                            }
                            $_contactNumber = !empty($player['contactNumber']) ? (($obfuscatedPhone) ? $obfuscatedPhone : $player['contactNumber']) : '';
                            $_contact_number_disable_edit = ($_contactNumber && ($this->utils->isEnabledFeature('enabled_show_player_obfuscated_phone') || $player['verified_phone']));
                            ?>
                            <p class="accountinfo field">
                                <?= $this->player_functions->checkAccount_displaySymbolHint('Contact Number')?>
                                <?=lang("Contact No.") ?>:
                            </p>
                            <?php if($this->utils->getConfig('contact_number_note')): ?>
                                <span class="help-block <?= ($player['contactNumber']) ? 'hidden' : "" ?>" style="clear:left; padding-top:5px"><?= lang('phone_note') ?></span>
                            <?php endif; ?>
                            <input type="text" name="contact_number" id="contact_number" class="form-control input-sm" value="<?= $_contactNumber ?>"
                                   placeholder="<?=lang('Contact No.') . ' ' . $this->player_functions->checkAccount_displayPlaceholderHint('Contact Number')?>"
                                   onkeyup="this.value=this.value.replace(/[^(0-9)]/g,'');"
                                <?= $this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'contactNumber', $_contact_number_disable_edit) ? '' : 'disabled' ; ?>
                            />
                            <?php if(!$this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'contactNumber', $_contact_number_disable_edit)): ?>
                                <span class="help-block <?= empty($player['contactNumber']) ? 'hidden' : "" ?>" style="clear:left; padding-top:5px"><?= lang('Please contact our Customer Service if you would like to update your contact number.') ?></span>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if (in_array('IMACCOUNT', $fields)) : ?>
                            <?php
                            $obfuscatedIm = '';
                            if (!empty($player['imAccount']) && $this->utils->isEnabledFeature('enabled_show_player_obfuscated_im')) {
                                $obfuscatedIm = $this->utils->keepOnlyString($player['imAccount'], -4);
                            }
                            $_im = !empty($player['imAccount']) ? (($obfuscatedIm) ? $obfuscatedIm : $player['imAccount']) : '';
                            $_im_disable_edit = ($_im && $this->utils->isEnabledFeature('enabled_show_player_obfuscated_im'));
                            $im1 = $this->config->item('Instant Message 1', 'cust_non_lang_translation');
                            ?>
                            <p class="accountinfo field">
                                <?= $this->player_functions->checkAccount_displaySymbolHint('Instant Message 1')?>
                                <?= ($im1) ? $im1 : lang('Instant Message 1')?>:
                            </p>

                            <input type="text" name="im_account" id="im_account" class="form-control input-sm" value="<?= $_im ?>"
                                   placeholder="<?=(($im1) ? $im1 : lang('Instant Message 1')) . ' ' . $this->player_functions->checkAccount_displayPlaceholderHint('Instant Message 1')?>"
                                <?= $this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'imAccount', $_im_disable_edit) ? '' : 'disabled' ; ?>
                            >

                            <?= form_error('im_account', '<p class="form__error">', '</p>'); ?>
                        <?php endif; ?>

                        <?php if (in_array('IMACCOUNT2', $fields)) : ?>
                            <?php
                            $obfuscatedIm2 = '';
                            if (!empty($player['imAccount2']) && $this->utils->isEnabledFeature('enabled_show_player_obfuscated_im')) {
                                $obfuscatedIm2 = $this->utils->keepOnlyString($player['imAccount2'], -4);
                            }
                            $_im2 = !empty($player['imAccount2']) ? (($obfuscatedIm2) ? $obfuscatedIm2 : $player['imAccount2']) : '';
                            $_im2_disable_edit = ($_im2 && $this->utils->isEnabledFeature('enabled_show_player_obfuscated_im'));
                            $im2 = $this->config->item('Instant Message 2', 'cust_non_lang_translation');
                            ?>
                            <p class="accountinfo field">
                                <?= $this->player_functions->checkAccount_displaySymbolHint('Instant Message 2')?>
                                <?=($im2) ? $im2 : lang('Instant Message 2')?>:
                            </p>
                            <input type="text" name="im_account2" id="im_account2" class="form-control input-sm" value="<?= $_im2 ?>"
                                   placeholder="<?=(($im2) ? $im2 : lang('Instant Message 2')) . ' ' . $this->player_functions->checkAccount_displayPlaceholderHint('Instant Message 2')?>"
                                <?= $this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'imAccount2', $_im2_disable_edit) ? '' : 'disabled' ; ?>
                            />

                            <?= form_error('im_account2', '<p class="form__error">', '</p>'); ?>
                        <?php endif; ?>

                        <?php if (in_array('IMACCOUNT3', $fields)) : ?>
                            <?php
                            $obfuscatedIm3 = '';
                            if (!empty($player['imAccount3']) && $this->utils->isEnabledFeature('enabled_show_player_obfuscated_im')) {
                                $obfuscatedIm3 = $this->utils->keepOnlyString($player['imAccount3'], -4);
                            }
                            $_im3 = !empty($player['imAccount3']) ? (($obfuscatedIm3) ? $obfuscatedIm3 : $player['imAccount3']) : '';
                            $_im3_disable_edit = ($_im3 && $this->utils->isEnabledFeature('enabled_show_player_obfuscated_im'));
                            $im3 = $this->config->item('Instant Message 3', 'cust_non_lang_translation');
                            ?>
                            <p class="accountinfo field">
                                <?= $this->player_functions->checkAccount_displaySymbolHint('Instant Message 3')?>
                                <?=($im3) ? $im3 : lang('Instant Message 3')?>:
                            </p>
                            <input type="text" name="im_account3" id="im_account3" class="form-control input-sm" value="<?= $_im3 ?>"
                                   placeholder="<?=(($im3) ? $im3 : lang('Instant Message 3')) . ' ' . $this->player_functions->checkAccount_displayPlaceholderHint('Instant Message 3')?>"
                                <?= $this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'imAccount3', $_im3_disable_edit) ? '' : 'disabled' ; ?>
                            >

                            <?= form_error('im_account3', '<p class="form__error">', '</p>'); ?>
                        <?php endif; ?>

                        <?php if (in_array('ZIPCODE', $fields)) : ?>
                            <p class="accountinfo field">
                                <?= $this->player_functions->checkAccount_displaySymbolHint('Zip Code')?>
                                <?=lang("a_reg.48") ?>:
                            </p>
                            <input type="text" name="zipcode" id="zipcode" class="form-control letters_only"
                                   value="<?= !empty($player['zipcode']) ? $player['zipcode'] : '' ?>"
                                   placeholder="<?= lang("a_reg.48") . ' ' . $this->player_functions->checkAccount_displayPlaceholderHint('Zip Code')?>"
                                <?= $this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'zipcode') ? '' : 'disabled' ; ?>
                            >
                            <?= form_error('zipcode', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                        <?php endif; ?>

                        <?php if (in_array('RESIDENTCOUNTRY', $fields)) : ?>
                            <p class="accountinfo field">
                                <?= $this->player_functions->checkAccount_displaySymbolHint('Resident Country')?>
                                <?=lang("reg.a42") ?>:
                            </p>
                            <div class="custom-dropdown">
                                <select id="country_list" class="form-control registration-field" name="residentCountry"
                                    <?= $this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'residentCountry') ? '' : 'disabled' ; ?>
                                >
                                    <option value=""><?= lang('reg.a42') . ' ' . $this->registration_setting->displayPlaceholderHint('Resident Country')?></option>
                                    <?php if(!$this->utils->isEnabledFeature('disable_frequently_use_country_in_registration')) : ?>
                                        <optgroup label="<?=lang('lang.frequentlyUsed')?>">
                                            <?php foreach ($this->utils->getCommonCountryList() as $key) {?>
                                                <option value="<?=$key?>"><?=lang('country.' . $key)?> <?= ($player['residentCountry'] == $key) ? "selected" : ""; ?></option>
                                            <?php } ?>
                                        </optgroup>
                                    <?php endif; ?>
                                    <optgroup label="<?=lang('lang.alphabeticalOrder')?>">
                                        <?php foreach ($this->utils->getCountryList() as $key) {?>
                                            <option value="<?=$key?>" <?= ($player['residentCountry'] == $key) ? "selected" : ""; ?>><?=lang('country.' . $key)?></option>
                                        <?php } ?>
                                    </optgroup>
                                </select>
                            </div>
                            <?= form_error('residentCountry', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                        <?php endif; ?>

                        <?php $address_keys = ['REGION','CITY','ADDRESS','ADDRESS2'];?>
                        <?php if(array_intersect($address_keys, $fields)): ?>
                            <p class="accountinfo field">
                                <?=lang('player.59');?>:
                            </p>
                        <?php endif; ?>

                        <?php if (in_array('REGION', $fields)) : ?>
                            <?php $placeholder = ($full_address_in_one_row) ? lang('Please input your full address') : lang('a_reg.37.placeholder');?>
                            <?= $this->player_functions->checkAccount_displaySymbolHint('Region')?>
                            <input type="text" name="region" id="region" class="form-control" data-toggle="popover"
                                   value="<?= !empty($player['region']) ? $player['region'] : '' ?>"
                                   placeholder="<?=$placeholder;?> <?= $this->player_functions->checkAccount_displayPlaceholderHint('Region')?>"
                                   maxlength="120"
                                <?= $this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'region') ? '' : 'disabled' ; ?>
                            >
                            <?= form_error('region', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                        <?php endif; ?>

                        <?php if (in_array('CITY', $fields)) : ?>
                            <?= $this->player_functions->checkAccount_displaySymbolHint('City')?>
                            <input type="text" name="city" id="city" class="form-control letters_only"
                                   value="<?= !empty($player['city']) ? $player['city'] : '' ?>"
                                   placeholder="<?= lang("a_reg.36.placeholder") . ' ' . $this->player_functions->checkAccount_displayPlaceholderHint('City')?>"
                                   maxlength="120"
                                <?= $this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'city') ? '' : 'disabled' ; ?>
                            >
                            <?= form_error('city', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                        <?php endif; ?>

                        <?php if (in_array('ADDRESS', $fields)) : ?>
                            <?= $this->player_functions->checkAccount_displaySymbolHint('Address')?>
                            <input type="text" name="address" id="address" class="form-control" data-toggle="popover"
                                   value="<?= !empty($player['address']) ? $player['address'] : '' ?>"
                                   placeholder="<?=lang('a_reg.43.placeholder') . ' ' . $this->player_functions->checkAccount_displayPlaceholderHint('Address')?>"
                                   maxlength="120"
                                <?= $this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'address') ? '' : 'disabled' ; ?>
                            >
                            <?= form_error('address', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                        <?php endif; ?>

                        <?php if (in_array('ADDRESS2', $fields)) : ?>
                            <?= $this->player_functions->checkAccount_displaySymbolHint('Address2')?>
                            <input type="text" name="address2" id="address2" class="form-control" data-toggle="popover"
                                   value="<?= !empty($player['address2']) ? $player['address2'] : '' ?>"
                                   placeholder="<?=lang('a_reg.44.placeholder');?> <?= $this->player_functions->checkAccount_displayPlaceholderHint('Address2')?>"
                                   maxlength="120"
                                <?= $this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'address2') ? '' : 'disabled' ; ?>
                            >
                            <?= form_error('address2', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                        <?php endif; ?>

                        <?php if (in_array('ID_CARD_TYPE', $fields)) : ?>
                            <p class="accountinfo field">
                                <?= $this->player_functions->checkAccount_displaySymbolHint('ID Card Type')?>
                                <?=lang("a_reg.51") ?>:
                            </p>
                            <select class="form-control input-sm" name="id_card_type" <?= $this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'id_card_type') ? '' : 'disabled' ; ?>>
                                <option value=""><?= lang('Select ID Card Type')?></option>
                                <?php foreach($this->utils->idCardType() as $key => $value): ?>
                                    <option value="<?=$value['code_type']?>" <?=(strtolower($player['id_card_type']) == strtolower($value['code_type'])) ? "selected":''?>><?php echo $value['type_name'];?></option>
                                <?php endforeach ?>
                            </select>
                            <?= form_error('id_card_type', '<p class="form__error">', '</p>'); ?>
                        <?php endif; ?>

                        <?php if (in_array('ID_CARD_NUMBER', $fields)) : ?>
                            <?php
                            $_id_card_number_disable_edit = (!empty($player['id_card_number']) && !$player_verification['verified']);
                            ?>
                            <p class="accountinfo field">
                                <?= $this->player_functions->checkAccount_displaySymbolHint('ID Card Number')?>
                                <?=lang("a_reg.49") ?>:
                            </p>
                            <input type="text" name="id_card_number" id="id_card_number" class="form-control letters_only"
                                   value="<?= !empty($player['id_card_number']) ? $player['id_card_number'] : '' ?>"
                                   placeholder="<?= lang("a_reg.49") . ' ' . $this->player_functions->checkAccount_displayPlaceholderHint('ID Card Number')?>"
                                <?= $this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'id_card_number', $_id_card_number_disable_edit) ? '' : 'disabled' ; ?>
                            >
                            <?= form_error('id_card_number', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                        <?php endif; ?>

                        <?php if ($this->utils->isEnabledFeature('enable_player_prefs_auto_transfer')) : ?>
                            <p><?=lang("Auto-transfer all balance to game wallet on game launch") ?>:</p>
                            <div class="row">
                                <div class="col-sm-4">
                                    <label>
                                        <?= form_radio([ 'name' => 'enable_auto_transfer' ,
                                            'value' => 'yes' , 'checked' => $enable_auto_transfer ]) ?>
                                        <?= lang('Enable') ?>
                                    </label>
                                </div>
                                <div class="col-sm-4">
                                    <label>
                                        <?= form_radio([ 'name' => 'enable_auto_transfer' ,
                                            'value' => 'no' , 'checked' => !$enable_auto_transfer ]) ?>
                                        <?= lang('Disable') ?>
                                    </label>
                                </div>
                            </div>
                        <?php endif; ?>

                        <button id="save_btn" type="submit" class="btn form-control"><?=lang("Save") ?></button>
                    </div>
                    <div class="info-right col-md-6 col-sm-6">
                        <div class="circle">
                            <div class="pie_left">
                                <div class="left"></div>
                            </div>
                            <div class="pie_right">
                                <div class="right"></div>
                            </div>
                            <div class="mask"><span class="percent"><?=$profileProgress?></span>%</div>
                        </div>
                        <p><?=lang("Complete your information to secure your account and funds.") ?></p>
                        <img src="<?=base_url() . $this->utils->getPlayerCenterTemplate()?>/img/info-icon.png">
                    </div>
                </form>
            </div>
        </div>
        <!-- BASIC INFORMATION SECTION END -->

        <!-- PLAYER PREFERENCE SECTION START -->
        <div id="playerPrefTab" class="tab-pane fade">
            <div class="row">
                <div class="col-md-12">
                    <br>
                    <p><?=sprintf(lang('pi.player_pref.hint1'), lang('pi.player_pref_custom_name'))?></p>
                    <br>
                    <p><?=lang('pi.player_pref.hint2')?></p>
                    <br><br>
                    <table class="table table-sortable" id="player-preference" style="border:1px #eee solid;">
                        <div class="player-pref-table">
                            <?php foreach ($config_prefs as $key => $config_pref): ?>
                            <?php
                                $isChecked = '';
                                $pref_lang = 'Player Preference ' . lang($config_pref);

                                if($registration_fields[$pref_lang]['account_visible'] == Registration_setting::HIDDEN)
                                    continue;
                                if(!empty($current_preferences) && isset($current_preferences->$key) && $current_preferences->$key == 'true')
                                    $isChecked = 'checked';
                            ?>
                                <tr>
                                    <td><label class="checkbox-inline" for="pref-data-<?=$key?>"><?=lang($pref_lang)?></label></td>
                                    <td><input type="checkbox" name="pref-data-<?=$key?>" class="pref-data-<?=$key?>" value="<?=$key?>" <?=$isChecked?>></td>
                                </tr>
                            <?php endforeach ?>
                        </div>
                    </table>
                </div>
            </div>
        </div>
        <!-- PLAYER PREFERENCE SECTION END -->
    </div>
</div>

<?php include VIEWPATH . '/resources/third_party/DateRangePicker.php'; ?>
<?php
    foreach ($standard_css as $css_url) {
        echo '<link href="' . $css_url . '" rel="stylesheet"/>';
    }

    foreach ($standard_js as $js_url) {
        echo '<script type="text/javascript" src="' . $js_url . '"></script>';
    }
?>
<script type="text/javascript" src="<?=$this->utils->getPlayerCmsUrl('/common/js/player_center/account-information.js') ?>"></script>
 <?php if ($this->utils->isEnabledFeature('enable_communication_preferences') && !empty($this->utils->getConfig('communication_preferences'))) :?>
     <script type="text/javascript" src="<?=$this->utils->getPlayerCmsUrl('/common/js/player_center/player-preferences.js') ?>"></script>
 <?php endif;?>

<script type="text/javascript">
    $(function(){
        /** initialize status messages */
        AccountInformation.msgWait = "<?= lang('Please wait'); ?>";
        AccountInformation.msgSmsSent = "<?= lang('SMS sent'); ?>";
        AccountInformation.msgSmsFailed = "<?= lang('SMS failed'); ?>";
        AccountInformation.uploadLabel = "<?= lang('Upload: ') ?>";

        <?php if ($this->operatorglobalsettings->getSettingIntValue('birthday_option') == 2 || !empty($player['birthdate'])) : ?>
            $(".datepicker.player-birthday:enabled").datepicker({
                format: "yyyy-mm-dd",
                autoclose: true,
                endDate: "-<?= $this->config->item('legal_age') ?>y",
                clearBtn: true,
                calendarWeeks: true,
                language: "<?= $lang_now?>"
                //todayHighlight: true
            });
        <?php else : ?>
            AccountInformation.initDOB({
                yearSelector:  "#dob_year",
                monthSelector: "#dob_month",
                daySelector:   "#dob_day",
                inputDOB : $('input[name="birthdate"]'),
                legal_age : <?= $this->config->item('legal_age') ?>
            });
        <?php endif; ?>


        $('#frmEditPlayer').on('submit', function(){
            show_loading();

            // Client-side check for emoji and other invalid chars, OGP-12268
            var block_emoji = <?= $this->utils->isEnabledFeature('block_emoji_chars_in_real_name_field') ? 'true' : 'false' ?>;
            var fields = {
                'first_name': '<?= lang('First Name') ?>',
                'last_name': '<?= lang('Last Name') ?>'
            };

            var valid_names = AccountInformation.validateNames(this, block_emoji);

            if (valid_names.result == false) {
                stop_loading();
                var mesg_valid_error = '<?= lang('formvalidation.regex_match') ?>'.replace('%s', fields[valid_names.field]);
                MessageBox.danger(mesg_valid_error);
                return false;
            }

            var response = {
                "status": null,
                "msg": null
            };

            var formdata = new FormData(this);

            $.ajax({
                type: "POST",
                url: $(this).attr('action'),
                mimeTypes:"multipart/form-data",
                contentType: false,
                cache: false,
                processData: false,
                data: formdata
            }).done(function(data){
                response = data;
            }).fail(function(){
                response.status = 'error';
                response.msg = '<?=lang('con.cms02')?>';
            }).always(function(){
                stop_loading();
                if(response.status == 'success'){
                    MessageBox.success(response.msg, null, function(){
                        show_loading();
                        window.location.reload(true);
                    });
                } else {
                    MessageBox.danger(response.msg);
                }
            });
            return false;
        });

        $('.circle').each(function(index, el) {
            var num = $(this).find('span').text() * 3.6;
            if (num <= 180) {
                $(this).find('.right').css('transform', "rotate(" + num + "deg)");
            } else {
                $(this).find('.right').css('transform', "rotate(180deg)");
                $(this).find('.left').css('transform', "rotate(" + (num - 180) + "deg)");
            }
        });

        $('[data-toggle="tooltip"]').tooltip();

        <?php if ($this->utils->isEnabledFeature('enable_communication_preferences') && !empty($this->utils->getConfig('communication_preferences'))) :?>
            /** initialize player preference */
            $("[class^=pref-data-]").bootstrapSwitch();
            PlayerPreferences.initPlayerPreference();
        <?php endif;?>
    });
</script>

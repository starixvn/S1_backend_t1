<?php
    $birthday_MaxDate = date('Y-m-d', now() - (86400 * 365 * $this->utils->getConfig('legal_age')));
    $game_platforms = $this->external_system->getSystemCodeMapping();
?>

<style>
    .content .mmenu{
    	z-index: 10;
    }
</style>

<?php if ($this->utils->isEnabledFeature('enable_communication_preferences') && !empty($this->utils->getConfig('communication_preferences'))) :?>
    <link rel="stylesheet" type="text/css" href="<?=$this->utils->thirdpartyUrl('bootstrap-switch/3.3.4/css/bootstrap3/bootstrap-switch.min.css')?>" />
    <script type="text/javascript" src="<?=$this->utils->thirdpartyUrl('bootstrap-switch/3.3.4/js/bootstrap-switch.min.js');?>"></script>
<?php endif;?>

<div class="mmenu">
    <div class="mm_header">
        <ul>
            <li class="text"><?=lang('Compose Message')?></li>
        </ul>
        <div class="mm_exit" id="member_exit"></div>
    </div>
    <div class="mm_main">
        <form action="<?=site_url('player_center/postEditPlayer')?>" method="post" id="frm_profile">
            <ul class="name update-panel">
                <div class="inputtwo">
                    <input id="xm" name="name" placeholder="<?=lang('Your real name')?>" type="text" class="input_01" value="<?=$player['firstName']?>">
                    <?= $this->player_functions->checkAccount_displayInputHint('firstName')?>
                    <ul class="tsxm notify">
                        <i></i>
                        <a><?=lang('reg.firstName')?></a>
                    </ul>

                    <div class="login_btn" id="btn_Name"><a><?=lang('sys.vu34')?></a></div>
                </div>
            </ul>
            <ul class="lastName update-panel">
                <div class="inputtwo">
                    <input id="xm_lastname" name="lastName" placeholder="<?=lang('player.05')?>" type="text" class="input_01" value="<?=$player['lastName']?>">
                    <ul class="tsxm_lastname notify">
                        <i></i>
                        <a><?=lang('reg.lastName')?></a>
                    </ul>
                    <div class="login_btn" id="btn_LastName"><a><?=lang('sys.vu34')?></a></div>
                </div>
                <script type="text/javascript">
                    $(function(){
                        $('.lastName').hide();
                    });
                </script>
            </ul>
            <ul class="sex update-panel">
                <li val="Male" <?=( ! empty( $player['gender'] ) && ($player['gender'] == "<?=lang('reg.16')?>" || $player['gender'] == "Male" || $player['gender'] == "male" ) ) ? 'class="border"' : '';?>><?=lang('reg.16')?><i <?=( ! empty( $player['gender'] ) && ($player['gender'] == "<?=lang('reg.16')?>" || $player['gender'] == "Male" || $player['gender'] == "male" ) ) ? 'class="border"' : '';?>></i></li>
                <li val="Female" <?=( ! empty( $player['gender'] ) && ($player['gender'] == "<?=lang('reg.17')?>" || $player['gender'] == "Female" || $player['gender'] == "female" ) ) ? 'class="border"' : '';?>><?=lang('reg.17')?><i <?=( ! empty( $player['gender'] ) && ($player['gender'] == "<?=lang('reg.16')?>" || $player['gender'] == "Female" || $player['gender'] == "female" ) ) ? 'class="border"' : '';?>></i></li>
                <div class="input">
                    <ul id="tssex notify">
                        <i></i>
                        <a><?=lang('please_select')?><?=lang('player.57')?></a>
                    </ul>
                </div>
                <input type="hidden" name="gender" value="<?=$player['gender']?>" />
            </ul>
            <ul class="birthday update-panel">
                <div class="inputtwo">
                    <?php if ($this->operatorglobalsettings->getSettingIntValue('birthday_option') == 2 || !empty($player['birthdate'])) : ?>
                        <input id="brd" value="<?=$player['birthdate']?>" name="birthdate" placeholder="选择出生年月日" type="date" class="input_01 datetimepicker-date" data-maxDate="<?=$birthday_MaxDate?>">
                    <?php else : ?>
                        <input type="hidden" id="brd" value="<?=$player['birthdate']?>" name="birthdate" placeholder="选择出生年月日" type="date">
                        <?php
                            $now = date('Y');
                            $cutoff = $now - 100;
                            $legal_age = $this->config->item('legal_age');
                        ?>
                        <div class="col-md-4 birthday-option">
                            <div class="custom-dropdown">
                                <select class="selectbox form-control registration-field" id="year" name="year" onchange="validateDOB()">
                                    <option value=""><?= lang('reg.14') ?></option>
                                    <?php for($y = ($now - $legal_age); $y >= $cutoff; $y--): ?>
                                        <option value="<?=$y?>"><?=$y?></option>
                                    <?php endfor?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 lr-padding birthday-option">
                            <div class="custom-dropdown ">
                                <select class="selectbox form-control registration-field" id="month" name="month" onchange="validateDOB()">
                                    <option value=""><?= lang('reg.59') ?></option>
                                    <?php for($m = 1; $m <= 12; $m++): ?>
                                        <option value="<?=sprintf("%02d",$m)?>"><?=sprintf("%02d",$m)?></option>
                                    <?php endfor?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 lr-padding birthday-option">
                            <div class="custom-dropdown ">
                                <select class="selectbox form-control registration-field" id="day" name="day" onchange="validateDOB()">
                                    <option value=""><?= lang('reg.13') ?></option>
                                </select>
                            </div>
                        </div>
                    <?php endif;?>
                    <ul class="txsr notify">
                        <i></i>
                        <a><?=lang('wrong_format_for_birthdate')?></a>
                    </ul>
                    <div class="login_btn" id="btn_Birthday"><a><?=lang('sys.vu34')?></a></div>
                </div>
            </ul>
            <ul class="birthplace update-panel">
                <div class="inputtwo">
                    <input type="text" id="birthplace_field" name="birthplace" class="input_01" value="<?=$player['birthplace']?>" placeholder="<?=lang('reg.25')?>">
                    <ul class="txsr notify">
                        <i></i>
                        <a><?=lang('please_enter')?><?=lang('reg.24')?></a>
                    </ul>
                    <div class="login_btn" id="btn_Birthplace"><a><?=lang('sys.vu34')?></a></div>
                </div>
                <script type="text/javascript">
                    $(function(){
                        $('.birthplace').hide();
                    });
                </script>
            </ul>
            <ul class="citizenship update-panel">
                <div class="inputtwo">
                    <input type="text" id="citizenship_field" name="citizenship" class="input_01" value="<?=$player['citizenship']?>" placeholder="<?=lang('reg.23')?>">
                    <ul class="txsr notify">
                        <i></i>
                        <a><?=lang('please_enter')?><?=lang('reg.22')?></a>
                    </ul>
                    <div class="login_btn" id="btn_Citizenship"><a><?=lang('sys.vu34')?></a></div>
                </div>
                <script type="text/javascript">
                    $(function(){
                        $('.citizenship').hide();
                    });
                </script>
            </ul>
            <ul class="language update-panel">
                <div class="inputtwo">
                    <select name="language" title="<?=lang('reg.26')?>" class="form-control">
                        <option value=""><?=lang('pi.18')?></option>
                        <?php foreach(Language_function::PlayerSupportLanguageNames() as $lang_key => $lang_name): ?>
                            <option value="<?=$lang_key?>" <?=set_select('language', ucfirst($lang_key))?>><?=$lang_name?></option>
                        <?php endforeach ?>
                    </select>
                    <ul id="tslanguage">
                        <i></i>
                        <a><?=lang('please_select')?><?=lang('reg.26')?></a>
                    </ul>
                    <div class="login_btn" id="btn_Language"><a><?=lang('sys.vu34')?></a></div>
                </div>
                <script type="text/javascript">
                    $(function(){
                        $('select[name="language"]').children().each(function(){
                            if($(this).val() == "<?= $player['language'] ?>"){
                                $(this).attr("selected", true);
                            }
                        });
                        $('.language').hide();
                    });
                </script>
            </ul>
            <ul class="email update-panel">
                <div class="inputtwo">
                    <input name="email" id="em" placeholder="<?=lang('please_enter')?> <?=lang('reg.18')?>" type="email" class="input_01" value="<?=$player['email']?>">
                    <ul class="tsem notify">
                        <i></i>
                        <a><?=lang('please_enter')?> <?=lang('reg.18')?></a>
                    </ul>
                    <div class="login_btn" id="btn_email"><a><?=lang('sys.vu34')?></a></div>
                </div>
            </ul>
            <ul class="dialing_code update-panel">
                <?php
                $countryNumList = unserialize(COUNTRY_NUMBER_LIST_FULL);
                $requireDialingCode = ($registration_fields['Dialing Code']['account_required'] == Registration_setting::REQUIRED) ? 1 : 0;
                ?>
                <div class="inputtwo">
                    <select id="dialing_code" class="form-control input-sm <?=($requireDialingCode) ? 'required' : ''?>" name="<?=$registration_fields['Dialing Code']['alias']?>">
                        <option title="<?=lang('reg.77')?><?= $this->registration_setting->displayPlaceholderHint($registration_fields['Dialing Code']["field_name"])?>" country="" value=""><?=lang('reg.77')?><?= $this->registration_setting->displayPlaceholderHint($registration_fields['Dialing Code']["field_name"])?></option>
                        <?php foreach ($countryNumList as $country => $nums) : ?>
                            <?php if (is_array($nums)) : ?>
                                <?php foreach ($nums as $_nums) : ?>
                                    <option title="(+<?=$_nums?>)" country="<?=$country?>" value="<?=$_nums?>" <?= (set_value('dialing_code', $player['dialing_code']) == $_nums) ? 'selected' : '' ; ?>><?= sprintf("%s (+%s)", lang('country.'.$country), $_nums);?></option>
                                <?php endforeach ; ?>
                            <?php else : ?>
                                <option title="(+<?=$nums?>)" country="<?=$country?>" value="<?=$nums?>" <?= (set_value('dialing_code', $player['dialing_code']) == $nums) ? 'selected' : '' ; ?>><?= sprintf("%s (+%s)", lang('country.'.$country), $nums); ?></option>
                            <?php endif ; ?>
                        <?php endforeach ; ?>
                    </select>
                    <ul class="tsdc notify">
                        <i></i>
                        <a><?= sprintf(lang('formvalidation.required'),lang($registration_fields['Dialing Code']['field_name']))?></a>
                    </ul>
                    <div class="login_btn" id="btn_Dialing_Code"><a><?=lang('sys.vu34')?></a></div>
                </div>
                <script type="text/javascript">
                    $(function(){
                        $('.dialing_code').hide();
                    });
                </script>
            </ul>
            <ul class="phone update-panel">
                <div class="inputtwo">
                    <input name="contact_number" id="ph" placeholder="<?=lang('reg.30')?>" type="tel" class="input_01" min="11" max="11" value="<?=$player['contactNumber']?>" onkeyup="this.value=this.value.replace(/[^(0-9)]/g,'');">
                    <ul class="tsph">
                        <i></i>
                        <a><?=lang('reg.30')?></a>
                    </ul>
                    <div class="login_btn" id="btn_Tel"><a><?=lang('sys.vu34')?></a></div>
                </div>
            </ul>
            <ul class="im_account update-panel">
                <div class="inputtwo">
                    <input type="text" name="im_account" id="im_accnt" placeholder="<?=lang('Input your instance message 1')?>" class="input_01" value="<?=$player['imAccount']?>"/>
                    <div class="login_btn" id="btn_im_accnt"><a><?=lang('sys.vu34')?></a></div>
                </div>
            </ul>
            <ul class="im_account2 update-panel">
                <div class="inputtwo">
                    <input type="text" name="im_account2" id="im_accnt2" placeholder="<?=lang('Input your instance message 2')?>" class="input_01" value="<?=$player['imAccount2']?>"/>
                    <div class="login_btn" id="btn_im_accnt2"><a><?=lang('sys.vu34')?></a></div>
                </div>
            </ul>
            <ul class="im_account3 update-panel" style="display: none;">
                <div class="inputtwo">
                    <input type="text" name="im_account3" id="im_accnt3" placeholder="<?=lang('Input your instant message 3')?>" class="input_01" value="<?=$player['imAccount3']?>"/>
                    <div class="login_btn" id="btn_im_accnt3"><a><?=lang('sys.vu34')?></a></div>
                </div>
            </ul>
            <ul class="zipcode update-panel">
                <div class="inputtwo">
                    <input type="text" id="zipcode_field" name="zipcode" class="input_01" value="<?=$player['zipcode']?>" placeholder="<?=lang('a_reg.48')?>">
                    <ul class="txsr notify">
                        <i></i>
                        <a><?=lang('please_enter')?><?=lang('a_reg.48')?></a>
                    </ul>
                    <div class="login_btn" id="btn_Zipcode"><a><?=lang('sys.vu34')?></a></div>
                </div>
                <script type="text/javascript">
                    $(function(){
                        $('.zipcode').hide();
                    });
                </script>
            </ul>
            <ul class="residen_country update-panel">
                <div class="inputtwo">
                    <div class="custom-dropdown">
                        <select id="residentCountry_field" class="form-control registration-field" name="residentCountry">
                            <option value=""><?php echo lang('reg.a42');?><?= $this->registration_setting->displayPlaceholderHint('Resident Country')?></option>
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
                    <ul class="txsr notify">
                        <i></i>
                        <a><?=lang('please_enter')?><?=lang('reg.a42')?></a>
                    </ul>
                    <div class="login_btn" id="btn_ResidentCountry"><a><?=lang('sys.vu34')?></a></div>
                </div>
                <script type="text/javascript">
                    $(function(){
                        $('.residen_country').hide();
                    });
                </script>
            </ul>

            <?php $warning = ($full_address_in_one_row) ? lang('Please input your full address') : lang('please_enter').' '.lang('a_reg.37.placeholder');?>
            <?php $placeholder = ($full_address_in_one_row) ? lang('Please input your full address') : lang('a_reg.37.placeholder');?>
            <ul class="region update-panel">
                <div class="inputtwo">
                    <input type="text" id="region_field" name="region" class="input_01" value="<?=$player['region']?>" placeholder="<?=$placeholder;?>" maxlength="120"/>
                    <ul class="txsr notify">
                        <i></i>
                        <a><?=$warning;?></a>
                    </ul>
                    <div class="login_btn" id="btn_Region"><a><?=lang('sys.vu34')?></a></div>
                </div>
                <script type="text/javascript">
                    $(function(){
                        $('.region').hide();
                    });
                </script>
            </ul>
            <ul class="city update-panel">
                <div class="inputtwo">
                    <input type="text" id="city_field" name="city" class="input_01" value="<?=$player['city']?>" placeholder="<?=lang('a_reg.36.placeholder');?>" maxlength="120"/>
                    <ul class="txsr notify">
                        <i></i>
                        <a><?=lang('please_enter')?><?=lang('a_reg.36.placeholder');?></a>
                    </ul>
                    <div class="login_btn" id="btn_City"><a><?=lang('sys.vu34')?></a></div>
                </div>
                <script type="text/javascript">
                    $(function(){
                        $('.city').hide();
                    });
                </script>
            </ul>
            <ul class="address update-panel">
                <div class="inputtwo">
                    <input type="text" id="address_field" name="address" class="input_01" value="<?=$player['address']?>" placeholder="<?=lang('a_reg.43.placeholder')?>" maxlength="120"/>
                    <ul class="txsr notify">
                        <i></i>
                        <a><?=lang('please_enter')?><?=lang('a_reg.43.placeholder')?></a>
                    </ul>
                    <div class="login_btn" id="btn_addr"><a><?=lang('sys.vu34')?></a></div>
                </div>
                <script type="text/javascript">
                    $(function(){
                        $('.address').hide();
                    });
                </script>
            </ul>
            <ul class="address2 update-panel">
                <div class="inputtwo">
                    <input type="text" id="address2_field" name="address2" class="input_01" value="<?=$player['address2']?>" placeholder="<?=lang('a_reg.44.placeholder')?>" maxlength="120"/>
                    <ul class="txsr notify">
                        <i></i>
                        <a><?=lang('please_enter')?><?=lang('a_reg.44.placeholder')?></a>
                    </ul>
                    <div class="login_btn" id="btn_addr2"><a><?=lang('sys.vu34')?></a></div>
                </div>
                <script type="text/javascript">
                    $(function(){
                        $('.address2').hide();
                    });
                </script>
            </ul>
            <ul class="id_card_type update-panel">
                <div class="inputtwo">
                    <select name="id_card_type" id="id_card_type" title="<?=lang('a_reg.51')?>" class="form-control">
                        <option value=""><?=lang('Select ID Card Type')?></option>
                        <?php foreach($this->utils->idCardType() as $type) : ?>
                            <option value="<?= $type['code_type'] ?>" <?= $player['id_card_type'] == $type['code_type'] ? 'selected="1"' : '' ?> >
                                <?= $type['type_name'] ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                    <ul class="txsr notify">
                        <i></i>
                        <a><?=lang('Select ID Card Type')?></a>
                    </ul>
                    <div class="login_btn" id="btn_id_card_type"><a><?=lang('sys.vu34')?></a></div>
                </div>
                <script type="text/javascript">
                    $(function(){
                        $('.id_card_type').hide();
                    });
                </script>
            </ul>
            <ul class="id_card_number update-panel">
                <div class="inputtwo">
                    <input type="text" id="id_card_number_field" name="id_card_number" class="input_01" value="<?=$player['id_card_number']?>" placeholder="<?=lang('a_reg.49')?>">
                    <ul class="txsr notify">
                        <i></i>
                        <a><?=lang('please_enter')?><?=lang('a_reg.48')?></a>
                    </ul>
                    <div class="login_btn" id="btn_Id_card_number"><a><?=lang('sys.vu34')?></a></div>
                </div>
                <script type="text/javascript">
                    $(function(){
                        $('.id_card_number').hide();
                    });
                </script>
            </ul>
        </form>
    </div>
</div>

<div class="member">
    <?php if ($this->utils->isEnabledFeature('enable_communication_preferences') && !empty($this->utils->getConfig('communication_preferences')) && $registration_fields['Player Preference']['account_visible'] == Registration_setting::VISIBLE) :?>
        <ul class="row fm-ul profile-tab" role="tablist">
            <li class="col-xs-6 col-sm-6 active">
                <a data-toggle="tab" href="#basicInfoTab" id="basic_info_btn"  aria-expanded="false"><?php echo lang("Basic Information")?></a>
            </li>
            <li class="col-xs-6 col-sm-6">
                <a data-toggle="tab" href="#playerPrefTab" id="player_pref_btn"  aria-expanded="false"><?php echo lang("pi.player_pref")?></a>
            </li>
        </ul>
    <?php endif;?>

    <div class="tab-content">
        <!-- BASIC INFORMATION SECTION START -->
        <div id="basicInfoTab" class="tab-pane fade in active">
            <ul>
                <li class="username_info"><i><?=lang('sys.item1')?></i><?=$player['username']?></li>

                <?php if ($this->player_functions->checkAccountFieldsIfVisible('First Name')): ?>
                    <li class="firstname_info">
                        <i><?=lang('sys.item3')?></i>
                        <p id="xmm">
                            <a id="name" class="<?= ($this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'firstName')) ? "xlmenu" : '' ?>">
                                <?=(!empty($player['firstName']) ? $player['firstName'] : lang('not_filled_fill_now') )?>
                            </a>
                        </p>
                        <?php if (lang('notify.108')) : ?>
                            <?php $noteOffirstInto = explode('，', lang('notify.108')); ?>
                            <span><?= $noteOffirstInto[0] ?></span>
                        <?php endif;?>
                    </li>
                <?php endif;?>

                <?php if ($this->player_functions->checkAccountFieldsIfVisible('Last Name')): ?>
                    <li class="lastname_info">
                        <i><?=lang('player.05')?></i>
                        <p id="xmm">
                            <a id="lastName" class="<?= ($this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'lastName')) ? "xlmenu" : '' ?>">
                                <?=(!empty($player['lastName']) ? $player['lastName'] : lang('not_filled_fill_now') )?>
                            </a>
                        </p>
                    </li>
                <?php endif;?>

                <?php if ($this->player_functions->checkAccountFieldsIfVisible('Gender')): ?>
                    <li class="gender_info">
                        <i><?=lang('player.57')?></i>
                        <p id="sexx">
                            <a id="sex" class="<?= ($this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'gender')) ? "xlmenu" : '' ?>">
                                <?=(!empty($player['gender']) ? lang($player['gender']) : lang('not_filled_fill_now') )?>
                            </a>
                        </p>
                    </li>
                <?php endif ?>

                <?php if ($this->player_functions->checkAccountFieldsIfVisible('Birthday')): ?>
                    <li class="birthday_info">
                        <i><?=lang('player.17')?></i>
                        <p id="birthdayx">
                            <a id="birthday" class="<?= ($this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'birthdate')) ? "xlmenu" : '' ?>">
                                <?= ($player['birthdate']) ? $player['birthdate'] : lang('not_filled_fill_now') ?>
                            </a>
                        </p>
                    </li>
                <?php endif ?>

                <?php if ($this->player_functions->checkAccountFieldsIfVisible('BirthPlace')): ?>
                    <li class="birthplace_info">
                        <i><?=lang('reg.24')?></i>
                        <p id="birthplacex">
                            <a id="birthplace" class="<?= ($this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'birthplace')) ? "xlmenu" : '' ?>">
                                <?=(!empty($player['birthplace']) ? $player['birthplace'] : lang('not_filled_fill_now') )?>
                            </a>
                        </p>
                    </li>
                <?php endif ?>

                <?php if ($this->player_functions->checkAccountFieldsIfVisible('Nationality')): ?>
                    <li class="citizenship_info">
                        <i><?=lang('reg.22')?></i>
                        <p id="citizenshipx">
                            <a id="citizenship" class="<?= ($this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'citizenship')) ? "xlmenu" : '' ?>">
                                <?=(!empty($player['citizenship']) ? $player['citizenship'] : lang('not_filled_fill_now') )?>
                            </a>
                        </p>
                    </li>
                <?php endif ?>

                <?php if ($this->player_functions->checkAccountFieldsIfVisible('Language')): ?>
                    <li class="language_info">
                        <i><?=lang('player.62')?></i>
                        <p id="languagex">
                            <a id="language" class="<?= ($this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'language')) ? "xlmenu" : '' ?>">
                                <?=(!empty($player['language']) ? lang($player['language']) : lang('not_filled_fill_now') )?>
                            </a>
                        </p>
                    </li>
                <?php endif ?>

                <?php if ($this->player_functions->checkAccountFieldsIfVisible('Email')): ?>
                    <?php
                        $obfuscatedEmail = '';
                        if(!empty($player['email']) && $this->utils->isEnabledFeature('enabled_show_player_obfuscated_email')){
                            $em   = explode("@",$player['email']);
                            $name = implode(array_slice($em, 0, count($em)-1), '@');
                            $len  = floor(strlen($name)/2);
                            $obfuscatedEmail = substr($name,0, $len) . str_repeat('*', $len) . "@" . end($em);
                        }

                        $_email = !empty($player['email']) ? (($obfuscatedEmail) ? $obfuscatedEmail : $player['email']) : lang('not_filled_fill_now');
                        # Enable verification email and email verified
                        $_email_disable_edit = ($_email && $player['verified_email']);
                        $_email_allow_edit   = $this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'email', $_email_disable_edit);
                    ?>

                    <li class="email_info">
                        <i><?=lang('player.06')?></i>
                        <p id="emailx">
                            <a id="email" class="<?= ($_email_allow_edit) ? "xlmenu" : '' ?>">
                                <?= $_email ?>
                            </a>
                        </p>
                    </li>
                <?php endif ?>

                <?php if ($this->player_functions->checkAccountFieldsIfVisible('Dialing Code')): ?>
                    <li class="dialing_code_info">
                        <?php
                        $dialing_code='';
                        $dialing_code=!empty($player['dialing_code']) ? $player['dialing_code'] : lang('not_filled_fill_now');

                        $dialing_code_text = lang('not_filled_fill_now');
                        foreach ($countryNumList as $country => $nums){
                            if (is_array($nums)){
                                foreach ($nums as $_nums){
                                    $dialing_code_text = ($player['dialing_code'] == $_nums) ? sprintf("%s (+%s)", lang('country.'.$country), $_nums) : $dialing_code_text;
                                }
                            }else{
                                $dialing_code_text = ($player['dialing_code'] == $nums) ? sprintf("%s (+%s)", lang('country.'.$country), $nums) : $dialing_code_text;
                            }
                        }
                        ?>
                        <i><?=lang('Dialing Code')?></i>
                        <p id="dialing_codex">
                            <a id="dialing_code_txt" class="<?= ($this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'dialing_code')) ? "xlmenu" : '' ?>">
                                <?= $dialing_code_text ?>
                            </a>
                        </p>
                    </li>
                <?php endif ?>

                <?php if ($this->player_functions->checkAccountFieldsIfVisible('Contact Number')): ?>
                    <li class="contact_number_info">
                        <?php
                        $obfuscatedPhone = '';
                        if (!empty($player['contactNumber']) && $this->utils->isEnabledFeature('enabled_show_player_obfuscated_phone')) {
                            $obfuscatedPhone = $this->utils->keepOnlyString($player['contactNumber'], -4);
                        }
                        $_contact_number = !empty($player['contactNumber']) ? (($obfuscatedPhone) ? $obfuscatedPhone : $player['contactNumber']) : lang('not_filled_fill_now');
                        $_contact_number_disable_edit = ($_contact_number && ($this->utils->isEnabledFeature('enabled_show_player_obfuscated_phone') || $player['verified_phone']));
                        $_contact_allow_edit   = $this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'contactNumber', $_contact_number_disable_edit);
                        ?>
                        <i><?=lang('reg.29')?></i>
                        <p id="phonex">
                            <a id="phone" class="<?= ($_contact_allow_edit) ? "xlmenu" : '' ?>">
                                <?=$_contact_number?>
                            </a>
                        </p>
                    </li>
                <?php endif ?>

                <?php if ($this->player_functions->checkAccountFieldsIfVisible('Instant Message 1')): ?>
                    <li class="instant_message_1_info">
                        <?php
                        $obfuscatedIm = '';
                        if (!empty($player['imAccount']) && $this->utils->isEnabledFeature('enabled_show_player_obfuscated_im')) {
                            $obfuscatedIm = $this->utils->keepOnlyString($player['imAccount'], -4);
                        }
                        $im1 = $this->config->item('Instant Message 1', 'cust_non_lang_translation');
                        $_im = !empty($player['imAccount']) ? (($obfuscatedIm) ? $obfuscatedIm : $player['imAccount']) : lang('not_filled_fill_now');
                        $_im_disable_edit = ($_im && $this->utils->isEnabledFeature('enabled_show_player_obfuscated_im'));
                        $_im_allow_edit   = $this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'imAccount', $_im_disable_edit);
                        ?>
                        <i><?=($im1) ? $im1 : lang('Instant Message 1')?></i>
                        <p>
                            <a id="im_account" class="<?= ($_im_allow_edit) ? "xlmenu" : '' ?>">
                                <?=$_im?>
                            </a>
                        </p>
                    </li>
                <?php endif ?>

                <?php if ($this->player_functions->checkAccountFieldsIfVisible('Instant Message 2')): ?>
                    <li class="instant_message_2_info">
                        <?php
                        $obfuscatedIm2 = '';
                        if (!empty($player['imAccount2']) && $this->utils->isEnabledFeature('enabled_show_player_obfuscated_im')) {
                            $obfuscatedIm2 = $this->utils->keepOnlyString($player['imAccount2'], -4);
                        }
                        $im2 = $this->config->item('Instant Message 2', 'cust_non_lang_translation');
                        $_im2 = !empty($player['imAccount2']) ? (($obfuscatedIm2) ? $obfuscatedIm2 : $player['imAccount2']) : lang('not_filled_fill_now');
                        $_im2_disable_edit = ($_im2 && $this->utils->isEnabledFeature('enabled_show_player_obfuscated_im'));
                        $_im2_allow_edit   = $this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'imAccount2', $_im2_disable_edit);
                        ?>
                        <i><?=($im2) ? $im2 : lang('Instant Message 2')?></i>
                        <p>
                            <a id="im_account2" class="<?= ($_im2_allow_edit) ? "xlmenu" : '' ?>">
                                <?=$_im2?>
                            </a>
                        </p>
                    </li>
                <?php endif ?>

                <?php if ($this->player_functions->checkAccountFieldsIfVisible('Instant Message 3')): ?>
                    <li class="instant_message_3_info">
                        <?php
                        $obfuscatedIm3 = '';
                        if (!empty($player['imAccount3']) && $this->utils->isEnabledFeature('enabled_show_player_obfuscated_im')) {
                            $obfuscatedIm3 = $this->utils->keepOnlyString($player['imAccount3'], -4);
                        }
                        $im3 = $this->config->item('Instant Message 3', 'cust_non_lang_translation');
                        $_im3 = !empty($player['imAccount3']) ? (($obfuscatedIm3) ? $obfuscatedIm3 : $player['imAccount3']) : lang('not_filled_fill_now');
                        $_im3_disable_edit = ($_im3 && $this->utils->isEnabledFeature('enabled_show_player_obfuscated_im'));
                        $_im3_allow_edit   = $this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'imAccount3', $_im3_disable_edit);
                        ?>
                        <i><?=($im3) ? $im3 : lang('Instant Message 3')?></i>
                        <p>
                            <a id="im_account3" class="<?= ($_im3_allow_edit) ? "xlmenu" : '' ?>">
                                <?=$_im3?>
                            </a>
                        </p>
                    </li>
                <?php endif ?>

                <?php if ($this->player_functions->checkAccountFieldsIfVisible('Zip Code')): ?>
                    <li class="zipcode_info">
                        <i><?=lang('a_reg.48')?></i>
                        <p id="zipcodex">
                            <a id="zipcode" class="<?= ($this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'zipcode')) ? "xlmenu" : '' ?>">
                                <?=(!empty($player['zipcode']) ? $player['zipcode'] : lang('not_filled_fill_now') )?>
                            </a>
                        </p>
                    </li>
                <?php endif ?>

                <?php if ($this->player_functions->checkAccountFieldsIfVisible('Resident Country')): ?>
                    <li class="residen_country_info">
                        <i><?=lang('reg.a42')?></i>
                        <p id="residen_countryx">
                            <a id="residen_country" class="<?= ($this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'residentCountry')) ? "xlmenu" : '' ?>">
                                <?=(!empty($player['residentCountry']) ? lang('country.' . $player['residentCountry']) : lang('not_filled_fill_now') )?>
                            </a>
                        </p>
                    </li>
                <?php endif ?>

                <?php if ($this->player_functions->checkAccountFieldsIfVisible('Region')): ?>
                    <li class="region_info">
                        <?php $region_title = ($full_address_in_one_row) ? lang('player.59') : lang('Region');?>
                        <i><?=$region_title?></i>
                        <p id="regionx">
                            <a id="region" class="<?= ($this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'region')) ? "xlmenu" : '' ?>">
                                <?=(!empty($player['region']) ? $player['region'] : lang('not_filled_fill_now') )?>
                            </a>
                        </p>
                    </li>
                <?php endif ?>

                <?php if ($this->player_functions->checkAccountFieldsIfVisible('City')): ?>
                    <li class="city_info">
                        <i><?=lang('City')?></i>
                        <p id="cityx">
                            <a id="city" class="<?= ($this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'city')) ? "xlmenu" : '' ?>">
                                <?=(!empty($player['city']) ? $player['city'] : lang('not_filled_fill_now') )?>
                            </a>
                        </p>
                    </li>
                <?php endif ?>

                <?php if ($this->player_functions->checkAccountFieldsIfVisible('Address')): ?>
                    <li class="address_info">
                        <i><?=lang('Address')?></i>
                        <p id="addressx">
                            <a id="address" class="<?= ($this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'address')) ? "xlmenu" : '' ?>">
                                <?=(!empty($player['address']) ? $player['address'] : lang('not_filled_fill_now') )?>
                            </a>
                        </p>
                    </li>
                <?php endif ?>

                <?php if ($this->player_functions->checkAccountFieldsIfVisible('Address2')): ?>
                    <li class="address2_info">
                        <i><?=lang('Address2')?></i>
                        <p id="address2x">
                            <a id="address2" class="<?= ($this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'address2')) ? "xlmenu" : '' ?>">
                                <?=(!empty($player['address2']) ? $player['address2'] : lang('not_filled_fill_now') )?>
                            </a>
                        </p>
                    </li>
                <?php endif ?>

                <?php if ($this->player_functions->checkAccountFieldsIfVisible('ID Card Type')): ?>
                    <li class="id_card_number_info">
                        <i><?=lang('a_reg.51')?></i>
                        <p id="id_card_typex">
                            <a id="id_card_type" class="<?= ($this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'id_card_type')) ? "xlmenu" : '' ?>">
                                <?= (!empty($player['id_card_type']) ? $this->utils->id_card_type_to_text($player['id_card_type']) : lang('not_filled_fill_now') ) ?>
                            </a>
                        </p>
                    </li>
                <?php endif ?>

                <?php if ($this->player_functions->checkAccountFieldsIfVisible('ID Card Number')): ?>
                    <li class="id_card_number_info">
                        <i><?=lang('a_reg.49')?></i>
                        <p id="id_card_numberx">
                            <a id="id_card_number" class="<?= ($this->registration_setting->checkAccountInfoFieldAllowEdit($player, 'id_card_number')) ? "xlmenu" : '' ?>">
                                <?=(!empty($player['id_card_number']) ? $player['id_card_number'] : lang('not_filled_fill_now') )?>
                            </a>
                        </p>
                    </li>
                <?php endif ?>
            </ul>
        </div>
        <!-- BASIC INFORMATION SECTION END -->
        <!-- PLAYER PREFERENCE SECTION START -->
        <div id="playerPrefTab" class="tab-pane fade">
            <div class="col-xs-11 col-md-12">
                <br>
                <p><?=sprintf(lang('pi.player_pref.hint1'), lang('pi.player_pref_custom_name'))?></p>
                <br>
                <p><?=lang('pi.player_pref.hint2')?></p>
                <br><br>
                <table class="table table-sortable" style="border:1px #eee solid;">
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
                                <td><input type="checkbox" name="pref-data-<?=$key?>" class="pref-data-<?=$key?>" value="<?=$key?>" <?=$isChecked?> /></td>
                            </tr>
                        <?php endforeach ?>
                    </div>
                </table>
            </div>
        </div>
        <!-- PLAYER PREFERENCE SECTION END -->
    </div>
</div>

<?php if($this->utils->isEnabledFeature('enable_mobile_copyright_footer')): ?>
    <?=$this->load->view($this->utils->getPlayerCenterTemplate(FALSE) . '/mobile/includes/template_footer');?>
<?php endif; ?>

<!-- The Modal -->
<div class="modal fade" id="modal_confirmation" tabindex="-1" role="dialog" data-backdrop="false" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="display: none;">
            </div>
            <div class="modal-body">
                <center id="modal_content_msg"></center>
                <div id="game_platforms_list">
                    <div>
                        <span><?=lang('cp.changePassing')?></span>
                    </div>
                     <table class="listtable table" id="gamelist_table">
                        <?php foreach ($game_platforms as $key => $value):?>
                            <tr>
                                <td>
                                    <input readonly="readonly" class="game_platforms_value form-control" value="<?=$value?>" />
                                </td>
                                <td>
                                    <input readonly="readonly" class="game_platforms_key form-control" id="game_platforms_<?=$key?>" name="game_platforms_<?=$key?>" value="<?=lang('Pending')?>" />
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="submit_btn" class="btn btn-secondary confirmation_btn"><?=lang('lang.yes')?></button>
                <button type="button" id="close_btn" class="btn btn-primary confirmation_btn no"><?=lang('lang.cancel')?></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?=base_url()?>resources/js/jquery.mask.min.js"></script>
<?php if ($this->utils->isEnabledFeature('enable_communication_preferences') && !empty($this->utils->getConfig('communication_preferences'))) :?>
    <script type="text/javascript" src="<?=$this->utils->getPlayerCmsUrl('/common/js/player_center/player-preferences.js') ?>"></script>
<?php endif;?>

<script type="text/javascript">
    var profile_field_req = <?= json_encode($profile_field_req) ?>;

    $(document).ready(function () {
        // Get the modal
        var modal = $('#modal_confirmation').modal({
            "show": false
        });

        // Get the button that opens the modal
        // var btn = document.getElementById("btn_exit");

        // Get the <span> element that submit the modal
        var p_submit = $("#submit_btn");

        // Get the <span> element that submit the modal
        var p_close = $("#close_btn");

        var xlmenu = ".mmenu";
        var xlmovie = "mmenu_movie";

        var game_platforms = [];

        <?php foreach ($game_platforms as $key => $value): ?>
        game_platforms.push(<?=$key?>);
        <?php endforeach; ?>

        var game_count = game_platforms.length;

        var MAX_CHANGE_GAME_PLATFORM_COUNT = 50;
        var run_count = 0;
        var success_count = 0;
        var fail_count = 0;


        // When the user clicks on <span> (x), close the modal
        p_submit.on('click', function () {
            if (p_submit.hasClass('submit_modal_btn')) {
                p_submit.removeClass('submit_modal_btn');
                updateExit();
            } else {
                window.location = p_submit.attr('data-url');
                return true;
            }
        })

        p_close.on('click', function(){
            modal.modal('hide');
        });

        $(xlmenu).hide();

        game_platforms.sort(sortNumber);

        function sortNumber(a,b){
            return b-a;
        }

        function showChangeResult(){
            if((run_count -1) == game_count){
                $('#submit_btn').attr('disabled', false);
            }
        }

        function updateExit() {
            $("#loading").removeClass('hide');

            $.ajax({
                url: $('#frm_profile').attr('action'),
                type: 'POST',
                data: $('#frm_profile').serialize(),
                success: function (data) {
                    $("#loading").addClass('hide');
                }
            });
        }

        function UpdateName()
            { return profile_item_update('firstName', '#xm', "#name", '.tsxm', 'names'); }
        function UpdateLastName()
            { return profile_item_update('lastName', '#xm_lastname', "#lastName", '.tsxm_lastname', 'names'); }
        function UpdateBirthDay()
            { return profile_item_update('birthdate', "#brd", '#birthday', '.txsr', 'birthdate'); }
        function UpdateBirthplace()
            { return profile_item_update('birthplace', "#birthplace_field", '#birthplace', '.txsr'); }
        function UpdateCitizenship()
            { return profile_item_update('citizenship', "#citizenship_field", '#citizenship', '.txsr'); }
        function UpdateLanguage()
            { return profile_item_update('language', 'select[name="language"] option:selected', "#language", '.txsr'); }
        function UpdateEmail()
            { return profile_item_update('email', "#em", '#email', '.tsem', 'email');  }
        function UpdateDialingCode()
            { return profile_item_update('dialing_code', "#dialing_code", '#dialing_code_txt', '.tsdc'); }
        function UpdateTel()
            { return profile_item_update('contactNumber', "#ph", '#phone', '.tsph'); }
        function UpdateImAccount()
            { return profile_item_update('imAccount', "#im_accnt", '#im_account', '.txsr'); }
        function UpdateImAccount2()
            { return profile_item_update('imAccount2', "#im_accnt2", '#im_account2', '.txsr'); }
        function UpdateImAccount3()
            { return profile_item_update('imAccount3', "#im_accnt3", '#im_account3', '.txsr'); }

        function UpdateZipcode()
            { return profile_item_update('zipcode', "#zipcode_field", '#zipcode', '.txsr'); }
        function UpdateResidentCountry()
            { return profile_item_update('residentCountry', "#residentCountry_field", '#residen_country', '.txsr'); }

        function UpdateRegion()
            { return profile_item_update('region', "#region_field", '#region', '.txsr'); }
        function UpdateCity()
            { return profile_item_update('city', "#city_field", '#city', '.txsr'); }
        function UpdateAddress()
            { return profile_item_update('address', "#address_field", '#address', '.txsr'); }
        function UpdateAddress2()
            { return profile_item_update('address2', "#address2_field", '#address2', '.txsr'); }

        function UpdateIdCardType()
            { return profile_item_update('id_card_type', '#id_card_type', '#id_card_typex', '.txsr'); }
        function UpdateId_card_number()
            { return profile_item_update('id_card_number', "#id_card_number_field", '#id_card_number', '.txsr'); }

        function verify_common(field_value, field, source_el, alert_el, verify_type) {

            console.log(field +' (' + source_el + ') :', field_value);

            var verify_res = false;
            switch (verify_type) {
                case 'birthdate' :
                    var regex = /^\d+-\d+-\d+$/;
                    verify_res = field_value.length != 0 && regex.test(field_value);
                    break;
                case 'email' :
                    verify_res = field_value.length != 0 && verifyEmail(field_value);
                    break;
                case 'names' :
                    verify_res = field_value.length != 0 && verifyNames(field_value);
                    break;
                case 'default' : default :
                    verify_res = field_value.length != 0 && field_value.toString() != '0';
                    break;
            }

            if (!verify_res) {
                $(alert_el).css('display', 'block');
                $(alert_el).show();
                return false;
            }
            else {
                $(alert_el).hide();
            }

            return true;
        }

        function verifyEmail(email) {
            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(email);
        }

        /**
         * Check if s is clear of symbols (and emoji chars, if enabled), OGP-12268
         * @param   string  s       String to check
         * @return  bool    true if s is clear of symbols (and emojis), otherwise false
         */
        function verifyNames(s) {
            <?php if ($this->utils->isEnabledFeature('block_emoji_chars_in_real_name_field')) : ?>
                var emoji_regex = /(\u00a9|\u00ae|[\u2000-\u3300]|\ud83c[\ud000-\udfff]|\ud83d[\ud000-\udfff]|\ud83e[\ud000-\udfff])/;
                if (emoji_regex.test(s)) {
                    return false;
                }
            <?php endif; ?>

            var invalid_chars =  '~!@#$%^&*():<>{}();+-_0123456789[]/，、！：；？（）…｛｝‧．。\\.|\'"=?,`';
            if (s.length !=0 ){
                for (var i = 0; i < s.length; i++){
                    if (invalid_chars.indexOf(s[i]) >= 0) {
                        return false;
                    }
                }
            }

            return true;
        }

        function UpdateGender(field) {
            profile_item_update('gender', 'input[name="gender"]', '#sex');
        }

        // Bind handlers
        $("#btn_Name").click( UpdateName );
        $("#btn_LastName").click( UpdateLastName );
        $('#btn_Language').click( UpdateLanguage );
        $("#btn_Birthday").click( UpdateBirthDay );
        $('#btn_Citizenship').click( UpdateCitizenship );
        $('#btn_Birthplace').click( UpdateBirthplace );
        $('#btn_Region').click( UpdateRegion );
        $('#btn_City').click( UpdateCity );
        $('#btn_ResidentCountry').click( UpdateResidentCountry );
        $('#btn_Zipcode').click( UpdateZipcode );
        $('#btn_Id_card_number').click( UpdateId_card_number );
        $("#btn_Dialing_Code").click( UpdateDialingCode );
        $("#btn_Tel").click( UpdateTel );
        $("#btn_email").click( UpdateEmail );
        $("#btn_addr").click( UpdateAddress );
        $("#btn_addr2").click( UpdateAddress2 );
        $("#btn_im_accnt").click( UpdateImAccount );
        $("#btn_im_accnt2").click( UpdateImAccount2 );
        $("#btn_im_accnt3").click( UpdateImAccount3 );
        $('#btn_id_card_type').click( UpdateIdCardType );

        /**
         * Profile item update routine
         * @param   {string}    field       field in db table playerdetails to update
         * @param   {string}    source_el   input element that holds the value to update
         * @param   {string}    target_el   html element that encloses display value of the field
         * @param   {object}    extras      extra options, currently only one prop is supported
         *                                  verify {function} function for pre-update value check
         * @return  {none}
         */
        function profile_item_update(field, source_el, target_el, alert_el, verify_type) {
            // var value = $(source_el).is('input') ||  ? $(source_el).val() : $(source_el).text();
            var value = $(source_el).val();
            var mesg_update_failed = '<?= lang('Update Failed') ?> ';
            var alert_mesg = '';

            if (alert_el == undefined) {
                alert_el = '.txsr';
            }
            if (verify_type == undefined) {
                verify_type = 'default';
            }

            if (!verify_common(value, field, source_el, alert_el, verify_type)) {
                // console.log(field, profile_field_req[field]);
                if (profile_field_req[field] == false) {
                    // $(source_el).parents('.update-panel').hide();
                    clossmenu();
                }
                return;
            }

            $.ajax({
                url : '/player_center/ajax_player_profile_item/' + field ,
                data : { field: field, value: value } ,
                type : 'POST' ,
                dataType : 'json' ,
                xhrFields : {
                    withCredentials: true
                }
            })
            .done(function (resp) {
                if (resp.status == 'success') {
                    $(target_el).text(value);
                    alert_mesg=resp.mesg;
                }else {
                    alert_mesg = mesg_update_failed + resp.mesg;
                }
            })
            .fail(function () {
                alert_mesg = mesg_update_failed;
            })
            .always(function () {
                $(".mm_exit").trigger('click');
                if (alert_mesg != ''){
                    MessageBox.info(alert_mesg, null, function(){
                        Loader.show();
                        window.location.reload();
                    });
                }else{
                    window.location.reload();
                }
            });

            return false;
        }

        $('a.fa-pencil').click(function (e) {
            e.preventDefault();
            $(this).siblings(xlmenu).trigger('click');

            return false;
        });

        $('#brd').mask("9999-99-99", {
            placeholder: 'YYYY-MM-DD'
        });

        //下拉菜单
        $(".xlmenu").click(function () {
            $(xlmenu).find('.mm_header .text').html("<?=lang('pay.modify')?> " + $(this).closest('li').find('i').text());
            $(xlmenu).show();
            setTimeout(function(){
                $(xlmenu).addClass(xlmovie);
            }, 100);

            var mmenu = $(this).attr("id");

            switch (mmenu) {
                case "name"         : $(".name").show();        break;
                case "lastName"     : $(".lastName").show();    break;
                case "language"     : $('.language').show();    break;
                case "sex"          : $(".sex").show(); choicemenu(); break;
                case "dialing_code_txt" : $(".dialing_code").show(); break;
                case "phone"        : $(".phone").show();       break;
                case "email"        : $(".email").show();       break;
                case "region"       : $(".region").show();      break;
                case "city"         : $(".city").show();        break;
                case "address"      : $(".address").show();     break;
                case "address2"     : $(".address2").show();    break;
                case "residen_country" : $(".residen_country").show(); break;
                case "im_account"   : $(".im_account").show();  break;
                case "im_account2"  : $(".im_account2").show(); break;
                case "im_account3"  : $(".im_account3").show(); break;
                case "birthday"     : $(".birthday").show();    break;
                case "citizenship"  : $(".citizenship").show(); break;
                case "birthplace"   : $(".birthplace").show();  break;
                case "zipcode"      : $(".zipcode").show();     break;
                case "id_card_number" : $(".id_card_number").show(); break;
                case 'id_card_type' : $('.id_card_type').show(); break;
            }

            $(".mm_exit").click(function () {
                if ($('ul.phone').is(':visible')) {
                    var tel = $("#ph").val();
                    if (tel.length != 11) {
                        var old_tel = $('#phone').text();
                        if ($.isNumeric(old_tel))
                            $("#ph").val($('#phone').text());
                        else
                            $("#ph").val('');
                    }
                }

                $(xlmenu).removeClass(xlmovie);
                clossmenu();
            });

            function choicemenu() {
                $(".mm_main li").unbind("click");
                $(".mm_main li").click(function () {
                    $(".mm_main li i").removeClass("border");
                    $(this).find("i").addClass("border");
                    var title = $(this).text();
                    if (mmenu == "sex") {
                        $('input[name="gender"]').val($(this).attr('val'));
                        UpdateGender(this);
                        setTimeout("$('.sex').hide()", 300);
                    }
                    $(xlmenu).removeClass(xlmovie);
                });
            };
        });

        function clossmenu() {
            $(".mmenu").removeClass("mmenu_movie");
            setTimeout(function(){
                $('.update-panel').hide();
                $(".mmenu").hide();
                $('.update-panel .notify').hide();
            }, 300);
        };

        var birthInit =  function (options) {

            var date = new Date(),
    			DateY = date.getFullYear(),
    			DateM = date.getMonth(),
    			DateD = date.getDate(),
                minDOBDate = new Date(DateY - <?= $this->utils->getConfig('legal_age') ?>, DateM, DateD);

            var defaults = {
                    yearSelector:  "#year",
                    monthSelector: "#month",
                    daySelector:   "#day"
                },
                opts = $.extend({}, defaults, options),
                $yearSelector  = $(opts.yearSelector),
                $monthSelector = $(opts.monthSelector),
                $daySelector   = $(opts.daySelector),
                $dayDefaultOption = $daySelector.find("option:first").clone()
                $monthDefaultOption = $monthSelector.find("option:first").clone(),
                changeDay = function () {
    				var _year = $yearSelector.val(),
    					_month = $monthSelector.val();

    				$daySelector.html($dayDefaultOption);

    				if (_year == "" || _month == "") {
    					return;
    				}
    				var _curentDayOfMonth = new Date(_year, _month, 0).getDate(),
    					_isSameYear  = (Number(_year) == minDOBDate.getFullYear()),
    					_isSameMonth = (Number(_month) == (minDOBDate.getMonth() + 1)),
    					_isLessMonth = (Number(_month) > (minDOBDate.getMonth() + 1)),
    					_minDay 	 = minDOBDate.getDate();

    				for (var i = 1; i <= _curentDayOfMonth; i++) {
    					var dV = i.toString(),
    						d = (dV.length > 1) ? dV : "0" + dV ;
    					if ((_isSameYear && _isSameMonth && i > _minDay) || (_isSameYear && _isLessMonth)) {
    						$daySelector.append($("<option>").attr('disabled', 'disabled').val(d).text(d));
    					} else {
    						$daySelector.append($("<option>").val(d).text(d));
    					}
    				}
    			},changeMonth = function () {
    				var _year = $yearSelector.val(),
    					_month = $monthSelector.val();

    				$daySelector.html($dayDefaultOption);
    				$monthSelector.html($monthDefaultOption);

    				var _isSameYear = (Number(_year) == minDOBDate.getFullYear()),
    					_minMonth   = minDOBDate.getMonth() + 1;

    				for (var i = 1; i <= 12; i++) {
    					var mV = i.toString(),
    						m = (mV.length > 1) ? mV : "0" + mV;
    					if ((_isSameYear && i > _minMonth)) {
    						$monthSelector.append($("<option>").attr('disabled', 'disabled').val(m).text(m));
    					} else {
    						if (Number(_month) == i) {
    							$monthSelector.append($("<option>").attr('selected', 'selected').val(m).text(m));
    						} else {
    							$monthSelector.append($("<option>").val(m).text(m));
    						}
    					}
    				}
    			};

            $monthSelector.change(changeDay);
            $yearSelector.change(changeDay);
    		$yearSelector.change(changeMonth);
        }

        birthInit();

        <?php if ($this->utils->isEnabledFeature('enable_communication_preferences') && !empty($this->utils->getConfig('communication_preferences'))) :?>
            /** initialize player preference */
            $("[class^=pref-data-]").bootstrapSwitch();
            PlayerPreferences.initPlayerPreference();
        <?php endif;?>
    });

    function validateDOB() {
        var cdv = $("#day").val(),
            cmv = $("#month").val(),
            cyv = $("#year").val(),
            DOB = cyv + '-' + cmv + '-' + cdv;

        if (cdv != "" && cmv != "" && cyv != "") {
            $("#brd").val(DOB);
        } else {
            $("#brd").val("");
        }
    }
</script>
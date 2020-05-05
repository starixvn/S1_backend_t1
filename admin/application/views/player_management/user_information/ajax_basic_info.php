<div role="tabpanel" class="tab-pane active" id="basicInfo">
    <fieldset>
        <legend>
            <h4><b><?= lang('player.ui04') ?></b></h4>
        </legend>
        <!--        before click "Edit"          -->
        <div class="form-group form-group-sm clearfix personal-info-edit">
            <?php if ($this->permissions->checkPermissions('edit_player_personal_information')): ?>
                <div class="col-md-12">
                    <a class="btn btn-xs btn-portage pull-right" onclick="edit_player_personal_info()">
                        <i class="fa fa-edit"></i> <?=lang('lang.edit');?>
                    </a>
                </div>
            <?php endif; ?>
            <div class="col-md-3">
                <label><?=lang('player.05');?> :</label>
                <div class="form-control">
                    <?=empty($player['lastName']) ? lang('lang.norecord') : $player['lastName']; ?>
                </div>
                <label><?=lang('player.04');?> :</label>
                <div class="form-control">
                    <?=empty($player['firstName']) ? lang('lang.norecord') : $player['firstName']; ?>
                </div>
                <label><?=lang('player.57');?> :</label>
                <div class="form-control">
                    <?=empty($player['gender']) ? lang('lang.norecord') : lang($player['gender']); ?>
                </div>
                <label><?=lang('player.17');?> :</label>
                <div class="input-group">
                    <div class="form-control">
                        <?=empty($player['birthdate']) ? lang('lang.norecord') : $player['birthdate']; ?>
                    </div>
                    <div class="form-control">
                        <?=is_null($player['age']) ? lang('lang.norecord') : $player['age']; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <label><?=lang('player.58');?> :</label>
                <div class="form-control">
                    <?=empty($player['birthplace']) ? lang('lang.norecord') : $player['birthplace']; ?>
                </div>
                <label><?=lang('player.61');?> :</label>
                <div class ="form-control">
                    <?=empty($player['citizenship']) ? lang('lang.norecord') : lang("country.".$player['citizenship']); ?>
                </div>
                <label><?=lang('player.62');?> :</label>
                <div class="form-control">
                    <?=empty($player['language']) ? lang('lang.norecord') : lang(ucfirst($player['language'])); ?>
                </div>
            </div>
            <div class="col-md-3">
                <label><?=lang('player.60');?> :</label>
                <div class="form-control">
                    <?=empty($player['zipcode']) ? lang('lang.norecord') : $player['zipcode']; ?>
                </div>

                <label><?=lang('player.59');?> :</label>
                <div class="form-control">
                    <?=empty($player['residentCountry']) ? lang('lang.norecord') : lang('country.' . $player['residentCountry'])?>
                </div>
                <div class="form-control">
                    <?=empty($player['region']) ? lang('lang.norecord') : $player['region']; ?>
                </div>
                <?php if(!$full_address_in_one_row): ?>
                    <div class="form-control">
                        <?=empty($player['city']) ? lang('lang.norecord') : $player['city']; ?>
                    </div>
                    <div class="form-control">
                        <?=empty($player['address']) ? lang('lang.norecord') : $player['address']; ?>
                    </div>
                    <div class="form-control">
                        <?=empty($player['address2']) ? lang('lang.norecord') : $player['address2']; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-3">
                <label><?=lang('player.66');?> :</label>
                <div class="form-control">
                    <?php if ($this->permissions->checkPermissions('player_verification_question')): ?>
                        <?=empty($player['secretQuestion']) ? lang('lang.norecord') : (lang($player['secretQuestion']) ?: $player['secretQuestion']); ?>
                    <?php else : ?>
                        <?=empty($player['secretQuestion']) ? lang('lang.norecord') : $this->utils->keepOnlyString($player['secretQuestion'], 0); ?>
                    <?php endif; ?>
                </div>

                <label><?=lang('player.77');?> :</label>
                <div class="form-control">
                    <?php if ($this->permissions->checkPermissions('player_verification_questions_answer')): ?>
                        <?=empty($player['secretAnswer']) ? lang('lang.norecord') : str_replace('%20', ' ', $player['secretAnswer'])?>
                    <?php else : ?>
                        <?=empty($player['secretAnswer']) ? lang('lang.norecord') : $this->utils->keepOnlyString($player['secretAnswer'], 0); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!--        before click "Edit" end          -->
        <!--        after click "Edit"          -->
        <?php if ($this->permissions->checkPermissions('edit_player_personal_information')): ?>
            <?php $countryList = $this->utils->getCountryList();?>
            <form id="personal_information_form" action="/player_management/savePlayerPersonalInfo/<?=$player['playerId']?>" method="post">
                <div class="form-group form-group-sm clearfix personal-info-edit" style="display: none;">
                    <div class="col-md-3">
                        <label><?=lang('player.05');?> :</label>
                        <input name="lastName" class="form-control" value="<?=empty($player['lastName']) ? '' : $player['lastName'];?>" placeholder="<?=lang('player.05');?>" maxlength="48"/>
                        <div id="lastName_error" class="help-block text-danger"></div>

                        <label><?=lang('player.04');?> :</label>
                        <input name="firstName" class="form-control" value="<?=empty($player['firstName']) ? '' : $player['firstName'];?>" placeholder="<?=lang('player.04');?>" maxlength="48"/>
                        <div id="firstName_error" class="help-block text-danger"></div>

                        <label><?=lang('player.57');?> :</label>
                        <select name="gender" class="form-control">
                            <option value=""><?=lang('Please Select Gender');?></option>
                            <option value="Male" <?=($player['gender']=="Male") ? 'selected' : '';?>><?=lang('Male');?></option>
                            <option value="Female" <?=($player['gender']=="Female") ? 'selected' : '';?>><?=lang('Female');?></option>
                        </select>
                        <div id="gender_error" class="help-block text-danger"></div>

                        <label><?=lang('player.17');?> :</label>
                        <input name="birthdate" class="form-control datepicker" value="<?=$player['birthdate']?>" placeholder="<?=lang('birthday.format');?>">
                        <div id="birthdate_error" class="help-block text-danger"></div>
                    </div>
                    <div class="col-md-3">
                        <label><?=lang('player.58');?> :</label>
                        <input name="birthplace" class="form-control" value="<?=empty($player['birthplace']) ? '' : $player['birthplace'];?>" placeholder="<?=lang('Please Input Your Place of Birth')?>" maxlength="120"/>
                        <div id="birthplace_error" class="help-block text-danger"></div>

                        <label><?=lang('player.61');?> :</label>
                        <select name="citizenship" class="form-control">
                            <option value=""><?=lang('Please Input Your Nationality')?></option>
                            <?php foreach ($countryList as $key) :?>
                                <option value="<?=$key?>" <?=($player['citizenship'] == $key) ? 'selected' : ''?>><?=lang('country.' . $key)?></option>
                            <?php endforeach; ?>
                        </select>
                        <div id="citizenship_error" class="help-block text-danger"></div>

                        <label><?=lang('player.62');?> :</label>
                        <select name="player_language" class="form-control">
                            <option value=""><?=lang('Please Select Language')?></option>
                            <option value="English" <?=(ucfirst($player['language'])== 'English') ? "selected":''?>><?=lang('English')?></option>
                            <option value="Chinese" <?=(ucfirst($player['language'])== 'Chinese') ? "selected":''?>><?=lang('Chinese')?></option>
                            <option value="Indonesian" <?=(ucfirst($player['language'])== 'Indonesian') ? "selected":''?>><?=lang('Indonesian')?></option>
                            <option value="Vietnamese" <?=(ucfirst($player['language'])== 'Vietnamese') ? "selected":''?>><?=lang('Vietnamese')?></option>
                            <option value="Thai" <?=(ucfirst($player['language'])== 'Thai') ? "selected":''?>><?=lang('Thai')?></option>
                        </select>
                        <div id="player_language_error" class="help-block text-danger"></div>
                    </div>
                    <div class="col-md-3">
                        <label><?=lang('player.60');?> :</label>
                        <input name="zipcode" class="form-control" value="<?=empty($player['zipcode']) ? '' : $player['zipcode'];?>" placeholder="<?=lang('Please input ZIP code')?>" maxlength="36"/>
                        <div id="zipcode_error" class="help-block text-danger"></div>

                        <label><?=lang('player.59');?> :</label>
                        <select name="residentCountry" class="form-control">
                            <option value=""><?=lang('Please Select A Country')?></option>
                            <?php foreach ($countryList as $key) :?>
                                <option value="<?=$key?>" <?=($player['residentCountry'] == $key) ? 'selected' : ''?>><?=lang('country.' . $key)?></option>
                            <?php endforeach; ?>
                        </select>

                        <?php if($full_address_in_one_row): ?>
                            <input name="region" class="form-control" value="<?=empty($player['region']) ? '' : $player['region'];?>" placeholder="<?=lang('Please input your full address')?>" maxlength="120"/>
                            <div id="residentCountry_error" class="help-block text-danger"></div>
                            <div id="region_error" class="help-block text-danger"></div>
                        <?php else: ?>
                            <input name="region" class="form-control" value="<?=empty($player['region']) ? '' : $player['region'];?>" placeholder="<?=lang('a_reg.37.placeholder')?>" maxlength="120"/>
                            <input name="city" class="form-control" value="<?=empty($player['city']) ? '' : $player['city'];?>" placeholder="<?=lang('a_reg.36.placeholder')?>" maxlength="120"/>
                            <input name="address" class="form-control" value="<?=empty($player['address']) ? '' : $player['address'];?>" placeholder="<?=lang('a_reg.43.placeholder')?>" maxlength="120"/>
                            <input name="address2" class="form-control" value="<?=empty($player['address2']) ? '' : $player['address2'];?>" placeholder="<?=lang('a_reg.44.placeholder')?>" maxlength="120"/>
                            <div id="residentCountry_error" class="help-block text-danger"></div>
                            <div id="region_error" class="help-block text-danger"></div>
                            <div id="city_error" class="help-block text-danger"></div>
                            <div id="address_error" class="help-block text-danger"></div>
                            <div id="address2_error" class="help-block text-danger"></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-3">
                        <?php if ($this->permissions->checkPermissions('player_verification_question')): ?>
                            <label><?=lang('player.66');?> :</label>
                            <select name="secretQuestion" class="form-control">
                                <option value=""><?=lang('Please Select A Question')?></option>
                                <option value="reg.37" <?=($player['secretQuestion']== 'reg.37') ? "selected":''?>><?=lang('reg.37')?></option>
                                <option value="reg.38" <?=($player['secretQuestion']== 'reg.38') ? "selected":''?>><?=lang('reg.38')?></option>
                                <option value="reg.39" <?=($player['secretQuestion']== 'reg.39') ? "selected":''?>><?=lang('reg.39')?></option>
                                <option value="reg.40" <?=($player['secretQuestion']== 'reg.40') ? "selected":''?>><?=lang('reg.40')?></option>
                                <option value="reg.41" <?=($player['secretQuestion']== 'reg.41') ? "selected":''?>><?=lang('reg.41')?></option>
                            </select>
                            <div id="secretQuestion_error" class="help-block text-danger"></div>
                        <?php else : ?>
                            <div class="form-control">
                                <?=empty($player['secretQuestion']) ? lang('lang.norecord') : $this->utils->keepOnlyString($player['secretQuestion'], 0); ?>
                            </div>
                        <?php endif;?>
                        <label><?=lang('player.77');?> :</label>
                        <?php if ($this->permissions->checkPermissions('player_verification_questions_answer')): ?>
                            <input name="secretAnswer" class="form-control" value="<?=empty($player['secretAnswer']) ? '' : $player['secretAnswer'];?>" placeholder="<?=lang('player.77');?>" maxlength="36"/>
                            <div id="secretAnswer_error" class="help-block text-danger"></div>
                        <?php else : ?>
                            <div class="form-control">
                                <?=empty($player['secretAnswer']) ? lang('lang.norecord') : $this->utils->keepOnlyString($player['secretAnswer'], 0); ?>
                            </div>
                        <?php endif;?>
                    </div>
                    <div class="col-md-12">
                        <div class="pull-right">
                            <a class="btn btn-sm btn-linkwater" onclick="cancel_edit_player_personal_info()"><?=lang('Cancel');?></a>
                            <button type="submit" class="btn btn-sm btn-scooter"><?=lang('Save');?></button>
                        </div>
                    </div>
                </div>
            </form>
        <?php endif; ?>
        <!--        after click "Edit" end         -->
    </fieldset>
    <fieldset>
        <legend>
            <h4><b><?=lang('reg.74');?></b></h4>
        </legend>
        <!--        before click "Edit"          -->
        <div class="form-group form-group-sm clearfix contact-info-edit">
            <?php if ($this->permissions->checkPermissions('edit_player_contact_information')): ?>
                <div class="col-md-12">
                    <a class="btn btn-xs btn-portage pull-right" onclick="edit_player_contact_info()">
                        <i class="fa fa-edit"></i> <?=lang('lang.edit');?>
                    </a>
                </div>
            <?php endif; ?>
            <div class="col-md-3">
                <label><?=lang('player.06');?></label>
                <div class="form-control">
                    <?php if ($this->permissions->checkPermissions('player_contact_information_email')) : ?>
                        <?=empty($player['email']) ? lang('lang.norecord') : $player['email']; ?>
                    <?php else : ?>
                        <?=empty($player['email']) ? lang('lang.norecord') : $this->utils->keepHeadString($player['email'], 3); ?>
                    <?php endif; ?>
                </div>

                <?php if ($player['verified_email']): ?>
                    <div class="pull-right text-success f-10 p-5"><?=lang('Verified');?></div>
                <?php elseif (!empty($player['email']) && $player['verified_email'] == '0'): ?>
                    <div class="input-group verifie-group">
                        <div class="form-control input-group-btn"><?=lang('Unverified');?></div>
                        <div class="input-group-addon">
                            <div class="dropdown">
                                <a id="email_verifie" data-toggle="dropdown" href="javascript:void(0)">
                                    <i class="fa fa-ellipsis-v"></i>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="email_verifie">
                                    <li>
                                        <?php if($this->permissions->checkPermissions('verify_player_email')): ?>
                                            <a onclick="sendEmailVerification()"><?=lang('Send Verification Email')?></a>
                                        <?php else: ?>
                                            <a class="disabled" disabled="disabled"><?=lang('Send Verification Email')?></a>
                                        <?php endif; ?>
                                    </li>
                                    <li>
                                        <?php if($this->permissions->checkPermissions('verify_player_email')): ?>
                                            <a onclick="updateEmailStatusToVerified()"><?=lang('Set to Verified')?></a>
                                        <?php else: ?>
                                            <a class="disabled" disabled="disabled"><?=lang('Set to Verified')?></a>
                                        <?php endif; ?>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-3">
                <label><?=lang('Dialing Code');?> :</label>
                <div class="form-control">
                    <?php
                        $countryNumList = unserialize(COUNTRY_NUMBER_LIST_FULL);
                        if(empty($player['dialing_code'])) {
                            $formatted_dialing_code = lang('lang.norecord');
                        } else {
                            $country = array_keys($countryNumList, $player['dialing_code']);
                            if(empty($country)){ # some dialing code are in array
                                foreach ($countryNumList as $country => $nums){
                                    if (is_array($nums)) {
                                        foreach ($nums as $_nums) {
                                            if($_nums == $player['dialing_code']){
                                                $country_name = $country;
                                                break;
                                            }
                                        }
                                    }
                                }
                            } else {
                                $country_name = $country[0];
                            }

                            $formatted_dialing_code = sprintf("%s (+%s)", lang('country.'.$country_name), $player['dialing_code']);
                        }
                    ?>
                    <?=$formatted_dialing_code; ?>
                </div>
                <label><?=lang('player.63');?> :</label>
                <div class="form-control">
                    <?php if ($this->permissions->checkPermissions('player_contact_information_contact_number')): ?>
                        <?=empty($player['contactNumber']) ? lang('lang.norecord') : $player['contactNumber']; ?>
                    <?php else : ?>
                        <?=empty($player['contactNumber']) ? lang('lang.norecord') : $this->utils->keepTailString($player['contactNumber'], 3); ?>
                    <?php endif; ?>
                </div>

                <?php if ($player['verified_phone']): ?>
                    <div class="pull-right text-success f-10 p-5"><?=lang('Verified');?></div>
                <?php elseif (!empty($player['contactNumber']) && $player['verified_phone'] == '0'): ?>
                    <div class="input-group verifie-group">
                        <div class="form-control input-group-btn"><?=lang('Unverified');?></div>
                        <div class="input-group-addon">
                            <div class="dropdown">
                                <a id="contact_num_verifie" data-toggle="dropdown" href="javascript:void(0)">
                                    <i class="fa fa-ellipsis-v"></i>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="contact_num_verifie">
                                    <li>
                                        <?php if ($this->permissions->checkPermissions('verify_player_contact_number')): ?>
                                            <a onclick="sendSMSVerification()"><?=lang('Send Verification SMS');?></a>
                                        <?php else: ?>
                                            <a class="disabled" disabled="disabled"><?=lang('Send Verification SMS')?></a>
                                        <?php endif; ?>
                                    </li>
                                    <li>
                                        <?php if ($this->permissions->checkPermissions('verify_player_contact_number')): ?>
                                            <a onclick="updatePhoneStatusToVerified()"><?=lang('Set to Verified');?></a>
                                        <?php else: ?>
                                            <a class="disabled" disabled="disabled"><?=lang('Set to Verified')?></a>
                                        <?php endif; ?>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-3">
                <label><?=lang('Instant Message 1');?> :</label>
                <div class="form-control">
                    <?php if ($this->permissions->checkPermissions('player_contact_information_im_accounts')) : ?>
                        <?=empty($player['imAccount']) ? lang('lang.norecord') : $player['imAccount']; ?>
                    <?php else : ?>
                        <?=empty($player['imAccount']) ? lang('lang.norecord') : $this->utils->keepOnlyString($player['imAccount'], 0); ?>
                    <?php endif; ?>
                </div>
                <label><?=lang('Instant Message 2');?> :</label>
                <div class="form-control">
                    <?php if ($this->permissions->checkPermissions('player_contact_information_im_accounts')) : ?>
                        <?=empty($player['imAccount2']) ? lang('lang.norecord') : $player['imAccount2']; ?>
                    <?php else : ?>
                        <?=empty($player['imAccount2']) ? lang('lang.norecord') : $this->utils->keepOnlyString($player['imAccount2'], 0); ?>
                    <?php endif; ?>
                </div>
                <label><?=lang('Instant Message 3');?> :</label>
                <div class="form-control">
                    <?php if ($this->permissions->checkPermissions('player_contact_information_im_accounts')) : ?>
                        <?=empty($player['imAccount3']) ? lang('lang.norecord') : $player['imAccount3']; ?>
                    <?php else : ?>
                        <?=empty($player['imAccount3']) ? lang('lang.norecord') : $this->utils->keepOnlyString($player['imAccount3'], 0); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!--        before click "Edit" end          -->
        <!--        after click "Edit"          -->
        <?php if ($this->permissions->checkPermissions('edit_player_contact_information')): ?>
            <div class="form-group form-group-sm clearfix contact-info-edit" style="display: none;">
                <form id="contact_information_form" action="/player_management/savePlayerContactInfo/<?=$player['playerId']?>" method="post">
                    <div class="col-md-3">
                        <label><?=lang('player.06');?></label>
                        <?php if ($this->permissions->checkPermissions('player_contact_information_email')): ?>
                            <input type="email" name="email" class="form-control" value="<?=empty($player['email']) ? '' : $player['email'];?>" placeholder="<?=lang('player.06');?>" maxlength="60"/>
                            <div id="email_error" class="help-block text-danger"></div>
                        <?php else : ?>
                            <div class="form-control">
                                <?=empty($player['email']) ? lang('lang.norecord') : $this->utils->keepHeadString($player['email'], 3); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-3">
                        <label><?=lang('Dialing Code');?> :</label>
                        <select name="dialing_code" class="form-control">
                            <option value=""></option>
                            <?php foreach ($countryNumList as $country => $nums) : ?>
                                <?php if (is_array($nums)) : ?>
                                    <?php foreach ($nums as $_nums) : ?>
                                        <option value="<?=$_nums?>" <?= ($player['dialing_code'] == $_nums) ? 'selected' : '' ; ?>><?= sprintf("%s (+%s)", lang('country.'.$country), $_nums);?></option>
                                    <?php endforeach ; ?>
                                <?php else : ?>
                                    <option value="<?=$nums?>" <?= ($player['dialing_code'] == $nums) ? 'selected' : '' ; ?>><?= sprintf("%s (+%s)", lang('country.'.$country), $nums);?></option>
                                <?php endif ; ?>
                            <?php endforeach ; ?>
                        </select>
                        <div id="dialing_code_error" class="help-block text-danger"></div>

                        <label><?=lang('player.63');?> :</label>
                        <?php if ($this->permissions->checkPermissions('player_contact_information_contact_number')): ?>
                            <input name="contactNumber" class="form-control" value="<?=empty($player['contactNumber']) ? '' : $player['contactNumber'];?>" placeholder="<?=lang('player.63');?>" maxlength="24"/>
                            <div id="contactNumber_error" class="help-block text-danger"></div>
                        <?php else : ?>
                            <div class="form-control">
                                <?=empty($player['contactNumber']) ? lang('lang.norecord') : $this->utils->keepTailString($player['contactNumber'], 3); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-3">
                        <?php if ($this->permissions->checkPermissions('player_contact_information_im_accounts')) : ?>
                            <label><?=lang('Instant Message 1');?> :</label>
                            <input name="imAccount" class="form-control" value="<?=empty($player['imAccount']) ? '' : $player['imAccount'];?>" maxlength="60"/>
                            <div id="imAccount_error" class="help-block text-danger"></div>

                            <label><?=lang('Instant Message 2');?> :</label>
                            <input name="imAccount2" class="form-control" value="<?=empty($player['imAccount2']) ? '' : $player['imAccount2'];?>" maxlength="60"/>
                            <div id="imAccount2_error" class="help-block text-danger"></div>

                            <label><?=lang('Instant Message 3');?> :</label>
                            <input name="imAccount3" class="form-control" value="<?=empty($player['imAccount3']) ? '' : $player['imAccount3'];?>" maxlength="60"/>
                            <div id="imAccount3_error" class="help-block text-danger"></div>
                        <?php else : ?>
                            <label><?=lang('Instant Message 1');?> :</label>
                            <div class="form-control">
                                <?=empty($player['imAccount']) ? lang('lang.norecord') : $this->utils->keepOnlyString($player['imAccount'], 0); ?>
                            </div>

                            <label><?=lang('Instant Message 2');?> :</label>
                            <div class="form-control">
                                <?=empty($player['imAccount2']) ? lang('lang.norecord') : $this->utils->keepOnlyString($player['imAccount2'], 0); ?>
                            </div>

                            <label><?=lang('Instant Message 3');?> :</label>
                            <div class="form-control">
                                <?=empty($player['imAccount3']) ? lang('lang.norecord') : $this->utils->keepOnlyString($player['imAccount3'], 0); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-12">
                        <div class="pull-right">
                            <a class="btn btn-sm btn-linkwater" onclick="cancel_edit_player_contact_info()"><?=lang('Cancel');?></a>
                            <button type="submit" class="btn btn-sm btn-scooter"><?=lang('Save');?></button>
                        </div>
                    </div>
                </form>
            </div>
        <?php endif; ?>
        <!--        after click "Edit" end         -->
    </fieldset>
    <?php if ($this->utils->isEnabledFeature('enable_communication_preferences')): ?>
        <fieldset>
            <legend>
                <h4><b><?=lang('Communication Preference')?></b></h4>
            </legend>
            <div class="form-group form-group-sm clearfix m-t-20">

                <div class="col-md-3">
                    <div class="input-group">
                        <b>
                            <span class="f-14"><?=lang('Player Preference Email');?> :</span>
                            <span data-key="pref-data-email">
                                <?php if(!empty($current_comm_pref->email) && $current_comm_pref->email == "true"): ?>
                                    <span class="text-success"> <?=lang('Yes')?> </span>
                                    <?php $set_to = "false";?>
                                <?php else: ?>
                                    <span class="text-muted"> <?=lang('No')?> </span>
                                    <?php $set_to = "true";?>
                                <?php endif; ?>
                            </span>
                        </b>&nbsp;

                        <?php if ($this->permissions->checkPermissions('edit_player_communication_preference') && !$hide_comm_pref_btn): ?>
                            <?php
                                if($set_to == "false"){
                                    $btn_class = "btn-linkwater";
                                    $text = lang('Cancel');
                                } else {
                                    $btn_class = "btn-scooter";
                                    $text = lang('Add as preference');
                                }
                            ?>
                            <button class="btn <?=$btn_class?> pref-data-email" data-key="pref-data-email" data-value="<?=$set_to?>">
                                <?=$text?>
                            </button>
                        <?php endif ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <b>
                            <span class="f-14"><?=lang('Player Preference SMS');?> :</span>
                            <span data-key="pref-data-sms">
                                <?php if(!empty($current_comm_pref->sms) && $current_comm_pref->sms == "true"): ?>
                                    <span class="text-success"> <?=lang('Yes')?> </span>
                                    <?php $set_to = "false";?>
                                <?php else: ?>
                                    <span class="text-muted"> <?=lang('No')?> </span>
                                    <?php $set_to = "true";?>
                                <?php endif; ?>
                            </span>
                        </b>&nbsp;

                        <?php if ($this->permissions->checkPermissions('edit_player_communication_preference') && !$hide_comm_pref_btn): ?>
                            <?php
                                if($set_to == "false"){
                                    $btn_class = "btn-linkwater";
                                    $text = lang('Cancel');
                                } else {
                                    $btn_class = "btn-scooter";
                                    $text = lang('Add as preference');
                                }
                            ?>
                            <button class="btn <?=$btn_class?> pref-data-sms" data-key="pref-data-sms" data-value="<?=$set_to?>">
                                <?=$text?>
                            </button>
                        <?php endif ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <b>
                            <span class="f-14"><?=lang('Player Preference Phone Call');?> :</span>
                            <span data-key="pref-data-phone_call">
                                <?php if(!empty($current_comm_pref->phone_call) && $current_comm_pref->phone_call == "true"): ?>
                                    <span class="text-success"> <?=lang('Yes')?> </span>
                                    <?php $set_to = "false";?>
                                <?php else: ?>
                                    <span class="text-muted"> <?=lang('No')?> </span>
                                    <?php $set_to = "true";?>
                                <?php endif; ?>
                            </span>
                        </b>&nbsp;

                        <?php if ($this->permissions->checkPermissions('edit_player_communication_preference') && !$hide_comm_pref_btn): ?>
                            <?php
                                if($set_to == "false"){
                                    $btn_class = "btn-linkwater";
                                    $text = lang('Cancel');
                                } else {
                                    $btn_class = "btn-scooter";
                                    $text = lang('Add as preference');
                                }
                            ?>
                            <button class="btn <?=$btn_class?> pref-data-phone_call" data-key="pref-data-phone_call" data-value="<?=$set_to?>">
                                <?=$text?>
                            </button>
                        <?php endif ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <b>
                            <span class="f-14"><?=lang('Player Preference Post');?> :</span>
                            <span data-key="pref-data-post">
                                <?php if(!empty($current_comm_pref->post) && $current_comm_pref->post == "true"): ?>
                                    <span class="text-success"> <?=lang('Yes')?> </span>
                                    <?php $set_to = "false";?>
                                <?php else: ?>
                                    <span class="text-muted"> <?=lang('No')?> </span>
                                    <?php $set_to = "true";?>
                                <?php endif; ?>
                            </span>
                        </b>&nbsp;

                        <?php if ($this->permissions->checkPermissions('edit_player_communication_preference') && !$hide_comm_pref_btn): ?>
                            <?php
                                if($set_to == "false"){
                                    $btn_class = "btn-linkwater";
                                    $text = lang('Cancel');
                                } else {
                                    $btn_class = "btn-scooter";
                                    $text = lang('Add as preference');
                                }
                            ?>
                            <button class="btn <?=$btn_class?> pref-data-post" data-key="pref-data-post" data-value="<?=$set_to?>">
                                <?=$text?>
                            </button>
                        <?php endif ?>
                    </div>
                </div>
                <div class="col-md-12 f-12 m-t-30 text-right">
                    <span class="text-danger">
                        <?=lang('Communication Preference can\'t be changed when the player has Self-exclusion or Time out limit')?>
                    </span>
                </div>
            </div>
        </fieldset>
    <?php endif; ?>
</div>

<script type="text/javascript">
    var dialog_title = "<?=lang('userinfo.tab02');?>";
    var origin_personal_information_form = $("#personal_information_form").serialize();
    var origin_contact_information_form = $("#contact_information_form").serialize();

    function refresh_basic_info() {
        changeUserInfoTab(2);
        $('#simpleModal').modal('hide');
    }

    function edit_player_personal_info() {
        $(".personal-info-edit").toggle();
    }

    function cancel_edit_player_personal_info() {
        if(origin_personal_information_form != $("#personal_information_form").serialize()){
            var button = '';
            button += '<button class="btn btn-sm btn-linkwater" data-dismiss="modal" aria-label="Close"><?=lang('lang.keepEdit')?></button>'
            button += '<button class="btn btn-sm btn-scooter" onclick="refresh_basic_info()"><?=lang('lang.cancelEdit')?></button>';
            confirm_modal(dialog_title + ' > <?= lang("player.ui04") ?>', "<?=lang('con.plm80')?>", button);
        } else {
            edit_player_personal_info();
        }
    }

    $("#personal_information_form").submit(function(e) {
        var form = $(this);
        var url = form.attr('action');

        $.ajax({
            type: "POST",
            url: url,
            data: form.serialize(),
            dataType : "json",
            success: function(data) {
                if(data.status == 'success'){
                    refresh_basic_info();
                    success_modal(dialog_title + ' > <?= lang("player.ui04") ?>', "<?=lang('con.plm61')?>");
                } else if(data.status == 'failed') {
                    var errors = data.message;
                    for(var key in errors) {
                        $("#"+key+"_error").text(errors[key]);
                    }
                    error_modal(dialog_title + ' > <?= lang("player.ui04") ?>', "<?=lang('con.plm77')?>");
                }
            }
        });

        e.preventDefault(); // avoid to execute the actual submit of the form.
    });

    function edit_player_contact_info() {
        $(".contact-info-edit").toggle();
    }

    function cancel_edit_player_contact_info() {
        if(origin_contact_information_form != $("#contact_information_form").serialize()){
            var button = '';
            button += '<button class="btn btn-sm btn-linkwater" data-dismiss="modal" aria-label="Close"><?=lang('lang.keepEdit')?></button>'
            button += '<button class="btn btn-sm btn-scooter" onclick="refresh_basic_info()"><?=lang('lang.cancelEdit')?></button>';
            confirm_modal(dialog_title + ' > <?= lang("reg.74") ?>', "<?=lang('con.plm80')?>", button);
        } else {
            edit_player_contact_info();
        }
    }

    $("#contact_information_form").submit(function(e) {
        var form = $(this);
        var url = form.attr('action');

        $.ajax({
            type: "POST",
            url: url,
            data: form.serialize(),
            dataType : "json",
            success: function(data) {
                if(data.status == 'success'){
                    refresh_basic_info();
                    success_modal(dialog_title + ' > <?= lang("reg.74") ?>', "<?=lang('con.plm79')?>");
                } else if(data.status == 'failed') {
                    var errors = data.message;
                    for(var key in errors) {
                        $("#"+key+"_error").text(errors[key]);
                    }
                    error_modal(dialog_title + ' > <?= lang("reg.74") ?>', "<?=lang('con.plm78')?>");
                }
            }
        });

        e.preventDefault(); // avoid to execute the actual submit of the form.
    });

    <?php if($this->permissions->checkPermissions('verify_player_email')): ?>
        function updateEmailStatusToVerified(){
            var url="/player_management/updateEmailStatusToVerified/"+playerId;
            if(confirm("<?=lang('confirm.request'); ?>")){
                window.location.href=url;
            }
        }

        function sendEmailVerification(){
            var url="/player_management/sendEmailVerification/"+playerId;
            if(confirm("<?=lang('confirm.request'); ?>")){
                window.location.href=url;
            }
        }
    <?php endif; ?>

    <?php if ($this->permissions->checkPermissions('verify_player_contact_number')): ?>
        function updatePhoneStatusToVerified(){
            var url="/player_management/updatePhoneStatusToVerified/"+playerId;
            if(confirm("<?=lang('confirm.request'); ?>")){
                window.location.href=url;
            }
        }

        function sendSMSVerification(){
            var url="/player_management/sendSMSVerification/"+playerId;
            if(confirm("<?=lang('confirm.request'); ?>")){
                window.location.href=url;
            }
        }
    <?php endif; ?>
    $(document).ready(function(){
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            language: '<?=$this->language_function->convertToDatePickerLang($this->language_function->getCurrentLanguage())?>'
        });

        // -- Communication Preference button behavior
        $('button[data-key^="pref-data-"]').click(function(e){
            e.preventDefault();

            var key = $(this).data('key');
            var value = $(this).data('value');

            $('#comm_pref_notes_content').val('');
            $('#comm_pref_submit').data('value', value).data('key', key);
            $('#comm_pref_notes').modal('show');
        });

        $('#comm_pref_submit').click(function(e){
            if ($.trim($('#comm_pref_notes_content').val()) == '') {
                $('#comm_pref_notes_content').next('span').html('<?=lang("cu.7")?> <?=lang("is_required")?>');
                return false;
            } else {
                $('#comm_pref_notes_content').next('span').html('');
            }

            var key   = $(this).data('key');
            var value = $(this).data('value');

            $(this).prop('disabled', true);

            var post_data = {
                player_id: playerId,
                notes: $('#comm_pref_notes_content').val(),
            };
            post_data[key] = value;
            $.post("/player_management/updateCommunicationPreference", post_data, function(data){
                $('#comm_pref_submit').prop('disabled', false);

                if(data.status != "success"){
                    alert(data.message);
                    return false;
                }

                if(value == false || value == "false"){
                    var text_val = "<span class='text-muted'> <?=lang('No')?> </span>";
                    var button_val = "true";
                    var button_text = "<?=lang('Add as preference')?>";
                } else {
                    var text_val = "<span class='text-success'> <?=lang('Yes')?> </span>";
                    var button_val = "false";
                    var button_text = "<?=lang('Cancel')?>";
                }

                $('span[data-key="'+key+'"]').html(text_val);
                $('button[data-key="'+key+'"]').data('value', button_val);
                $('button[data-key="'+key+'"]').toggleClass('btn-scooter btn-linkwater');
                $('button[data-key="'+key+'"]').html(button_text);

                alert(data.message);
            });

            $('#comm_pref_notes').modal('hide');
        });
    });
</script>


<style type="text/css">
.tooltip-inner {
    /* If max-width does not work, try using width instead */
    width: 350px;
}

.popover {
    width: 250px;
}

.popover-footer {
  margin: 0;
  padding: 1px 14px;
  font-size: 14px;
  font-weight: 400;
  line-height: 18px;
  background-color: #F7F7F7;
  border-bottom: 1px solid #EBEBEB;
  border-radius: 5px 5px 0 0;
}

div.fields {
    height: 90px;
}

span.errors {
    padding: 0;
    margin: 0;
    float: left;
    font-size: 11px;
}
.panel{
    margin-bottom: 0px;
}
</style>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-og">
            <div class="panel-heading">
                <h4 class="panel-title pull-left"><?=lang('reg.01');?></h4>
                <div class="clearfix"></div>
            </div>

            <div class="panel panel-body" id="add_player_panel_body">
                <span class="help-block" style="color:#ff6666;"><?=lang('reg.02');?></span>

                <form method="post" action="<?=BASEURL . 'auth/postRegisterPlayer'?>" id="my_form" autocomplete="off" roel="form" class="form-vertical" name="form">
                    <input type="hidden" value="<?=(set_value('tracking_code') == null) ? $tracking_code : set_value('tracking_code')?>" name="tracking_code" id="tracking_code"/>
                    <input type="hidden" value="<?=(set_value('tracking_source_code') == null) ? $tracking_source_code : set_value('tracking_source_code')?>" name="tracking_source_code" id="tracking_source_code"/>
                    <input type="hidden" value="normal" name="level">

                    <div class="col-md-12">
                        <div class="col-md-3 fields">
                            <label for="username"><i style="color:#ff6666;">*</i> <?=lang('reg.03');?>: </label> <br/>

                            <input type="text" required name="username" id="username" class="form-control usernames_only" data-toggle="tooltip" title="<?=lang('reg.04');?>" value="<?php echo set_value('username')?>" placeholder="<?=lang('reg.03');?>" maxLength='9'>
                            <?php echo form_error('username', '<span class="help-block errors" style="color:#ff6666;">', '</span>');?>
                        </div>

                        <div id="passwordField">
                            <div class="col-md-3 fields">
                                <label for="password"><i style="color:#ff6666;">*</i> <?=lang('reg.05');?>: </label> <br/>
                                <input required type="password" name="password" id="password" class="form-control letters_numbers_only"  data-toggle="tooltip" title="<?=lang('reg.05');?>" placeholder="<?=lang('reg.05');?>" maxLength='12'>
                                <?php echo form_error('password', '<span class="help-block errors" style="color:#ff6666;">', '</span>');?>
                            </div>

                            <div class="col-md-3 fields">
                                <label for="cpassword"><i style="color:#ff6666;">*</i> <?=lang('reg.07');?>: </label> <br/>
                                <input required type="password" name="cpassword" id="cpassword" class="form-control letters_numbers_only" data-toggle="tooltip" title="<?=lang('reg.08');?>" placeholder="<?=lang('reg.09');?>" maxLength='12'>
                                <?php echo form_error('cpassword', '<span class="help-block errors" style="color:#ff6666;">', '</span>');?> <span class="help-block errors" id="lcpassword" style="display:none;"></span>
                            </div>
                        </div>

                        <?php if ($this->player_functions->checkRegisteredFieldsIfVisible('First Name') == 0) { ?>
                            <div class="col-md-3 fields">
                                <label for="first_name">
                                    <?php if ($this->player_functions->checkRegisteredFieldsIfRequired('First Name') == 0) {?>
                                        <i style="color:#ff6666;">*</i>
                                    <?php } ?>

                                    <?=lang('reg.10');?>:
                                </label> <br/>

                                <input required type="text" name="first_name" id="first_name" class="form-control letters_only" value="<?php echo set_value('first_name')?>" placeholder="<?=lang('reg.10');?>">
                                <?php echo form_error('first_name', '<span class="help-block errors" style="color:#ff6666;">', '</span>');?>
                            </div>
                        <?php } ?>

                        <?php if ($this->player_functions->checkRegisteredFieldsIfVisible('Last Name') == 0) { ?>
                            <div class="col-md-3 fields">
                                <label for="last_name">
                                    <?php if ($this->player_functions->checkRegisteredFieldsIfRequired('Last Name') == 0) {?>
                                        <i style="color:#ff6666;">*</i>
                                    <?php } ?>

                                    <?=lang('reg.11');?>:
                                </label> <br/>

                                <input required type="text" name="last_name" id="last_name" class="form-control letters_only" value="<?php echo set_value('last_name')?>" placeholder="<?=lang('reg.11');?>">
                                <?php echo form_error('last_name', '<span class="help-block errors" style="color:#ff6666;">', '</span>');?>
                            </div>
                        <?php } ?>

                        <?php if ($this->player_functions->checkRegisteredFieldsIfVisible('Birthday') == 0) { ?>
                            <div class="col-md-3 fields">
                                <label for="birthday">
                                    <?php if ($this->player_functions->checkRegisteredFieldsIfRequired('Birthday') == 0) {?>
                                        <i style="color:#ff6666;">*</i>
                                    <?php } ?>

                                    <?=lang('reg.12');?>:
                                </label> <br/>

                                <input required type="date" name="birthdate" class="form-control datePicker" value="<?=set_value('birthdate');?>" placeholder="YYYY-MM-DD"/>
                                <?php echo form_error('birthdate', '<span class="help-block errors" style="color:#ff6666;">', '</span>');?>
                            </div>
                        <?php } ?>

                        <?php if ($this->player_functions->checkRegisteredFieldsIfVisible('Gender') == 0) { ?>
                            <div class="col-md-3 fields">
                                <label for="gender">
                                    <?php if ($this->player_functions->checkRegisteredFieldsIfRequired('Gender') == 0) {?>
                                        <i style="color:#ff6666;">*</i>
                                    <?php } ?>

                                    <?=lang('reg.15');?>:
                                </label> <br/>

                                <?php
                                    $male = "";
                                    if (isset($_POST['gender']) && $_POST['gender'] == 'Male') {
                                        $male = 'checked';
                                    }

                                    $female = "";
                                    if (isset($_POST['gender']) && $_POST['gender'] == 'Female') {
                                        $female = 'checked';
                                    }
                                ?>

                                <input type="radio" name="gender" value="Male" <?=$male?>> <?=lang('reg.16');?>
                                <input type="radio" name="gender" value="Female" <?=$female?>> <?=lang('reg.17');?>
                                <?php echo form_error('gender', '<span class="help-block errors" style="color:#ff6666;">', '</span>');?>
                            </div>
                        <?php } ?>

                        <div class="col-md-3 fields">
                            <label for="email"><i style="color:#ff6666;">*</i> <?=lang('reg.18');?>: </label> <br/>

                            <input required type="email" name="email" id="email" class="form-control emails_only"  data-toggle="tooltip" title="<?=lang('reg.19');?>" value="<?php echo set_value('email')?>" placeholder="<?=lang('reg.18');?>">
                            <?php echo form_error('email', '<span class="help-block errors" style="color:#ff6666;">', '</span>');?>
                        </div>

                        <div class="col-md-3 fields">
                            <label for="retyped_email"><i style="color:#ff6666;">*</i> <?=lang('reg.20');?>: </label> <br/>

                            <input required type="email" name="retyped_email" id="retyped_email" class="form-control emails_only" data-toggle="tooltip" title="<?=lang('reg.21');?>" value="<?php echo set_value('retyped_email')?>" placeholder="<?=lang('reg.20');?>">
                            <?php echo form_error('retyped_email', '<span class="help-block errors" style="color:#ff6666;">', '</span>');?> <span class="help-block errors" id="lretype_email" style="display: none;"></span>
                        </div>

                        <?php if ($this->player_functions->checkRegisteredFieldsIfVisible('Nationality') == 0) { ?>
                            <div class="col-md-3 fields">
                                <label for="citizenship">
                                    <?php if ($this->player_functions->checkRegisteredFieldsIfRequired('Nationality') == 0) {?>
                                        <i style="color:#ff6666;">*</i>
                                    <?php } ?>

                                    <?=lang('reg.22');?>:
                                </label> <br/>

                                <input required type="text" name="citizenship" id="citizenship" class="form-control letters_only" value="<?php echo set_value('citizenship')?>" data-toggle="tooltip" title="<?=lang('reg.23');?>" placeholder="<?=lang('reg.22');?>">
                                <?php echo form_error('citizenship', '<span class="help-block errors" style="color:#ff6666;">', '</span>');?>
                            </div>
                        <?php } ?>

                        <?php if ($this->player_functions->checkRegisteredFieldsIfVisible('BirthPlace') == 0) { ?>
                            <div class="col-md-3">
                                <label for="birthplace">
                                    <?php if ($this->player_functions->checkRegisteredFieldsIfRequired('BirthPlace') == 0) {?>
                                        <i style="color:#ff6666;">*</i>
                                    <?php } ?>

                                    <?=lang('reg.24');?>:
                                </label> <br/>

                                <input type="text" name="birthplace" id="birthplace" class="form-control letters_numbers_only" data-toggle="tooltip" title="<?=lang('reg.25');?>" value="<?php echo set_value('birthplace')?>" placeholder="<?=lang('reg.24');?>">
                                <?php echo form_error('birthplace', '<span class="help-block errors" style="color:#ff6666;">', '</span>');?>
                            </div>
                        <?php } ?>

                        <?php if ($this->player_functions->checkRegisteredFieldsIfVisible('Language') == 0) { ?>
                            <div class="col-md-3 fields">
                                <label for="language">
                                    <?php if ($this->player_functions->checkRegisteredFieldsIfRequired('Language') == 0) {?>
                                        <i style="color:#ff6666;">*</i>
                                    <?php } ?>

                                    <?=lang('reg.26');?>:
                                </label> <br/>

                                <select required style="width:100%" name="language" id="language" class="form-control" value="<?php echo set_value('language');?>">
                                    <option value=""><?=lang('pi.18');?></option>
                                    <option value="Chinese" <?php echo set_select('language', 'Chinese');?>><?=lang('reg.27');?></option>
                                    <option value="English" <?php echo set_select('language', 'English');?>>English</option>
                                </select>
                                <?php echo form_error('language', '<span class="help-block errors" style="color:#ff6666;">', '</span>');?>
                            </div>
                        <?php } ?>

                        <div class="col-md-3 fields">
                            <label for="currency"><i style="color:#ff6666;">*</i> <?=lang('reg.28');?>: </label> <br/>

                            <?php if (!empty($currency)) {?>
                                <input required type="text" name="currency" id="currency" class="form-control" value="<?=$currency['currencyCode']?>" placeholder="<?=lang('reg.28');?>" readonly>
                            <?php } else {?>
                                <input required type="text" name="currency" id="currency" class="form-control" placeholder="<?=lang('reg.28');?>">
                            <?php } ?>
                            <?php echo form_error('currency', '<span class="help-block errors" style="color:#ff6666;">', '</span>');?>
                        </div>

                        <?php if ($this->player_functions->checkRegisteredFieldsIfVisible('Contact Number') == 0) { ?>
                            <div class="col-md-3 fields">
                                <label for="contact_number">
                                    <?php if ($this->player_functions->checkRegisteredFieldsIfRequired('Contact Number') == 0) {?>
                                        <i style="color:#ff6666;">*</i>
                                    <?php } ?>

                                    <?=lang('reg.29');?>:
                                </label> <br/>

                                <input type="text" name="contact_number" id="contact_number" class="form-control number_only" data-toggle="tooltip" title="<?=lang('reg.30');?>" value="<?php echo set_value('contact_number');?>" onkeypress="return isNumberKey(event);" placeholder="<?=lang('reg.29');?>">
                                <?php echo form_error('contact_number', '<span class="help-block errors" style="color:#ff6666;">', '</span>');?>
                            </div>
                        <?php } ?>

                        <?php if ($this->player_functions->checkRegisteredFieldsIfVisible('Instant Message 1') == 0) { ?>
                            <div class="col-md-3 fields">
                                <label for="im_type">
                                    <?php if ($this->player_functions->checkRegisteredFieldsIfRequired('Instant Message 1') == 0) {?>
                                        <i style="color:#ff6666;">*</i>
                                    <?php } ?>

                                    <?=lang('reg.31');?> 1:
                                </label>

                                <select style="width:100%" name="im_type" id="im_type" class="form-control" value="<?php echo set_value('im_type');?>" onchange="showDivRegistration(this);" data-toggle="tooltip" title="<?=lang('reg.32');?>">
                                    <option value=""><?=lang('reg.58');?></option>
                                    <option value="QQ" <?php echo set_select('im_type', 'QQ');?>>QQ</option>
                                    <option value="Skype" <?php echo set_select('im_type', 'Skype');?>>Skype</option>
                                    <option value="MSN" <?php echo set_select('im_type', 'MSN');?>>MSN</option>
                                </select>
                                <?php echo form_error('im_type', '<span class="help-block errors" style="color:#ff6666;">', '</span>');?>
                            </div>

                            <div class="col-md-3 fields">
                                <label>&nbsp;&nbsp;</label> <br/>
                                <input type="text" name="im_account" id="im_account" class="form-control" value="<?php echo set_value('im_account');?>" <?=set_value('im_type') ? '' : 'disabled="true"'?> placeholder="<?=lang('reg.34');?> 1" data-toggle="tooltip" title="<?=lang('reg.33');?>">
                                <?php echo form_error('im_account', '<span class="help-block errors" style="color:#ff6666;">', '</span>');?>
                            </div>
                        <?php } ?>

                        <?php if ($this->player_functions->checkRegisteredFieldsIfVisible('Instant Message 2') == 0) { ?>
                            <div class="col-md-3 fields">
                                <label for="im_type2">
                                    <?php if ($this->player_functions->checkRegisteredFieldsIfRequired('Instant Message 2') == 0) {?>
                                        <i style="color:#ff6666;">*</i>
                                    <?php } ?>

                                    <?=lang('reg.31');?> 2:
                                </label>

                                <select style="width:100%" name="im_type2" id="im_type2" class="form-control" value="<?php echo set_value('im_type2');?>" onchange="showDivRegistration2(this);" data-toggle="tooltip" title="<?=lang('reg.32');?>">
                                    <option value=""><?=lang('reg.58');?></option>
                                    <option value="QQ" <?php echo set_select('im_type2', 'QQ');?>>QQ</option>
                                    <option value="Skype" <?php echo set_select('im_type2', 'Skype');?>>Skype</option>
                                    <option value="MSN" <?php echo set_select('im_type2', 'MSN');?>>MSN</option>
                                </select>
                                <?php echo form_error('im_type2', '<span class="help-block errors" style="color:#ff6666;">', '</span>');?>
                            </div>

                            <div class="col-md-3 fields">
                                <label>&nbsp;&nbsp;</label> <br/>
                                <input type="text" name="im_account2" id="im_account2" class="form-control" value="<?php echo set_value('im_account2');?>" <?=set_value('im_type2') ? '' : 'disabled="true"'?> placeholder="<?=lang('reg.34');?> 2" data-toggle="tooltip" title="<?=lang('reg.33');?>">
                                <?php echo form_error('im_account2', '<span class="help-block errors" style="color:#ff6666;">', '</span>');?>
                            </div>
                        <?php } ?>

                        <?php if ($this->player_functions->checkRegisteredFieldsIfVisible('Security Question') == 0) { ?>
                            <div class="col-md-3 fields">
                                <label for="security_question">
                                    <?php if ($this->player_functions->checkRegisteredFieldsIfRequired('Security Question') == 0) {?>
                                        <i style="color:#ff6666;">*</i>
                                    <?php } ?>

                                    <?=lang('reg.35');?>:
                                </label> <br/>

                                <select required style="width:100%" name="security_question" id="security_question" class="form-control" data-toggle="tooltip" title="<?=lang('reg.36');?>">
                                        <option value="" selected><?=lang('reg.58');?></option>
                                        <option value="City of Birth" <?php echo set_select('security_question', 'City of Birth');?>><?=lang('reg.37');?></option>
                                        <option value="Favorite Sports Team" <?php echo set_select('security_question', 'Favorite Sports Team');?>><?=lang('reg.38');?></option>
                                        <option value="First School" <?php echo set_select('security_question', 'First School');?>><?=lang('reg.39');?></option>
                                        <option value="Mother's Maiden Name" <?php echo set_select('security_question', "Mother's Maiden Name");?>><?=lang('reg.40');?></option>
                                        <option value="Pet's Name" <?php echo set_select('security_question', "Pet's Name");?>><?=lang('reg.41');?></option>
                                </select>
                                <?php echo form_error('security_question', '<span class="help-block errors" style="color:#ff6666;">', '</span>');?>
                            </div>
                        <?php } ?>

                        <?php if ($this->player_functions->checkRegisteredFieldsIfVisible('Security Answer') == 0) { ?>
                            <div class="col-md-3 fields">
                                <label for="security_answer">
                                    <?php if ($this->player_functions->checkRegisteredFieldsIfRequired('Security Answer') == 0) {?>
                                        <i style="color:#ff6666;">*</i>
                                    <?php } ?>

                                    <?=lang('reg.42');?>:
                                </label> <br/>

                                <input required type="password" name="security_answer" id="security_answer" class="form-control"  data-toggle="tooltip" title="<?=lang('reg.43');?>"  value="<?php echo set_value('security_answer');?>" placeholder="<?=lang('reg.42');?>">
                                <?php echo form_error('security_answer', '<span class="help-block errors" style="color:#ff6666;">', '</span>');?>
                            </div>
                        <?php } ?>

                        <?php if ($this->player_functions->checkRegisteredFieldsIfVisible('Referral Code') == 0) {?>
                            <div class="col-md-3 fields">
                                <label for="referral_code"> <?=lang('reg.44');?>: </label> <br/>

                                <input type="text" name="referral_code" id="referral_code" class="form-control"  data-toggle="tooltip" title="<?=lang('reg.60');?>"  value="<?php echo set_value('referral_code');?>" placeholder="<?=lang('reg.44');?>" onchange="checkReferral();">
                                <?php echo form_error('referral_code', '<span class="help-block errors" style="color:#ff6666;">', '</span>');?>
                            </div>
                        <?php } ?>

                        <?php if ($this->player_functions->checkRegisteredFieldsIfVisible('Affiliate Code') == 0) { ?>
                            <?php if (empty($this->session->userdata('tracking_code'))) {?>
                                <div class="col-md-3 fields">
                                    <label for="affiliate_code"> <?=lang('reg.62');?>: </label> <br/>

                                    <input type="text" name="affiliate_code" id="affiliate_code" class="form-control"  data-toggle="tooltip" title="<?=lang('reg.61');?>"  value="<?php echo set_value('affiliate_code');?>" placeholder="<?=lang('reg.62');?>" onchange="checkAffiliate();">
                                    <?php echo form_error('affiliate_code', '<span class="help-block errors" style="color:#ff6666;">', '</span>');?>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <div class="row">
                                        <?php if ($this->player_functions->checkRegisteredFieldsIfVisible('At Least 18 Yrs. Old and Accept Terms and Conditions') == 0) { ?>
                                            <div class="col-md-6 col-md-offset-2">
                                                <input type="checkbox" name="terms" id="terms" onclick="checkAccept(this)">
                                                <?php if ($this->player_functions->checkRegisteredFieldsIfRequired('At Least 18 Yrs. Old and Accept Terms and Conditions') == 0) {?>
                                                    <i style="color:#ff6666;">*</i>
                                                <?php } ?>

                                                <?=lang('reg.45');?>
                                                <a href="<?=BASEURL . 'online/viewContentDetails/5'?>"><?=lang('reg.46');?></a>
                                                <?php echo form_error('terms', '<span class="help-block errors" style="color:#ff6666;">', '</span>');?>
                                            </div>
                                        <?php } ?>

                                        <div class="col-md-1 <?=($this->player_functions->checkRegisteredFieldsIfVisible('At Least 18 Yrs. Old and Accept Terms and Conditions') != 0) ? 'col-md-offset-5' : ''?>">
                                            <input type="submit" value="<?=lang('reg.47');?>" id="accept" class="btn btn-hotel btn-sm" <?=($this->player_functions->checkRegisteredFieldsIfRequired('At Least 18 Yrs. Old and Accept Terms and Conditions') == 0) ? 'disabled' : ''?> >
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<script>

    $(document).ready(function() {
        checkReferral();
        checkAffiliate();
    });

    $('#password').bind('keypress', function (event) {
        var regex = new RegExp("^[a-zA-Z0-9]+$");
        var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
        if (!regex.test(key)) {
           event.preventDefault();
           return false;
        }
    });
    $('#cpassword').bind('keypress', function (event) {
        var regex = new RegExp("^[a-zA-Z0-9]+$");
        var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
        if (!regex.test(key)) {
           event.preventDefault();
           return false;
        }
    });


</script>
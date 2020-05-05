<form action="<?='/player_management/savePlayerPersonalInfo_old/' . $player['playerId']?>" method="post">
    <div class="row" id="personal_form">
        <div class="col-md-12" id="toggleView">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <i class="icon-info"></i>
                        <strong><?=lang('player.ui04');?></strong>
                        <a href="<?='/player_management/userInformation/' . $player['playerId']?>" class="btn btn-sm btn-default pull-right" title="<?=lang('lang.close');?>">
                            <i class="glyphicon glyphicon-remove"></i>
                        </a>
                    </h4>
                </div>

                <div class="panel panel-body" id="personal_panel_body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <th class="active col-md-2"><?=lang('player.04');?>:</th>
                                        <td class="col-md-4">
                                            <input type="text" name="first_name" class="form-control input-sm letters_only" required value="<?=(set_value('first_name') != null) ? set_value('first_name') : $player['firstName'];?>" />
                                            <span style="color:red;"><?=form_error('first_name');?></span>
                                        </td>
                                        <th class="active col-md-2"><?=lang('player.20');?>:</th>
                                        <td>
                                            <select name="country" id="country" class="form-control input-sm">
                                                <option value=""><?=lang('player.21');?></option>
                                                <?php foreach ($this->utils->getCountryList() as $key) :?>
                                                    <option value="<?=$key?>" <?=($player['residentCountry'] == $key) ? 'selected' : ''?>><?=lang('country.' . $key)?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <span style="color:red;"><?=form_error('country');?></span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th class="active col-md-2"><?=lang('player.05');?>:</th>
                                        <td>
                                            <input type="text" name="last_name" class="form-control input-sm letters_only" value="<?=(set_value('last_name') != null) ? set_value('last_name') : $player['lastName'];?>" />
                                            <span style="color:red;"><?=form_error('last_name');?></span>
                                        </td>
                                        <th class="active col-md-2"><?=lang('player.61');?>:</th>
                                        <td>
                                            <input type="text" name="nationality" class="form-control input-sm" value="<?=(set_value('nationality') != null) ? set_value('nationality') : $player['citizenship'];?>" />
                                            <span style="color:red;"><?=form_error('nationality');?></span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th class="active col-md-2"><?=lang('player.57');?>:</th>
                                        <td>
                                            <div class="custom-dropdown">
                                                <select class="form-control input-sm" name="gender" >
                                                    <option value="" <?=($player['gender']== null ) ? "selected":''?>><?php echo lang('please_select'); ?></option>
                                                    <option value="Male" <?=($player['gender']== 'Male' || $player['gender']== '男') ? "selected":''?>><?php echo lang('Male'); ?></option>
                                                    <option value="Female" <?=($player['gender']== 'Female' || $player['gender']== '女') ? "selected":''?>><?php echo lang('Female'); ?></option>
                                                </select>
                                            </div>
                                            <span style="color:red;"><?=form_error('gender');?></span>
                                        </td>
                                        <th class="active col-md-2"><?=lang('player.62');?>:</th>
                                        <td>
                                            <select class="form-control input-sm" name="language">
                                                <option value="English" <?=(ucfirst($player['language'])== 'English') ? "selected":''?>><?=lang('reg.71')?></option>
                                                <option value="Chinese" <?=(ucfirst($player['language'])== 'Chinese') ? "selected":''?>><?=lang('reg.27')?></option>
                                                <option value="Indonesian" <?=(ucfirst($player['language'])== 'Indonesian') ? "selected":''?>><?=lang('Indonesian')?></option>
                                                <option value="Vietnamese" <?=(ucfirst($player['language'])== 'Vietnamese') ? "selected":''?>><?=lang('Vietnamese')?></option>
                                                <option value="Thai" <?=(ucfirst($player['language'])== 'Thai') ? "selected":''?>><?=lang('Thai')?></option>
                                            </select>
                                            <span style="color:red;"><?=form_error('language');?></span>

                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="active col-md-2"><?=lang('player.17');?>:</th>
                                        <td>
                                            <input type="text" name="birthdate" class="form-control input-sm datepicker" value="<?=(set_value('birthdate') != null) ? set_value('birthdate') : $player['birthdate'];?>" />
                                            <span style="color:red;"><?=form_error('birthdate');?></span>
                                        </td>
                                        <?php if ($this->permissions->checkPermissions('player_basic_info') && $this->permissions->checkPermissions('player_contact_information_contact_number')) {?>
                                            <th class="active col-md-2"><?=lang('player.63');?>:</th>
                                            <td>
                                                <input type="text" name="contactNumber" class="form-control input-sm number_only" value="<?=(set_value('contactNumber') != null) ? set_value('contactNumber') : $player['contactNumber'];?>" />
                                                <span style="color:red;"><?=form_error('contactNumber');?></span>
                                            </td>
                                        <?php } ?>
                                    </tr>

                                    <tr>
                                        <th class="active col-md-2"><?=lang('player.58');?>:</th>
                                        <td>
                                            <input type="text" name="birthplace" class="form-control input-sm" value="<?=(set_value('birthplace') != null) ? set_value('birthplace') : $player['birthplace'];?>" />
                                            <span style="color:red;"><?=form_error('birthplace');?></span>
                                        </td>
                                        <?php if ($this->permissions->checkPermissions('player_basic_info') && $this->permissions->checkPermissions('player_contact_information_email')) {?>
                                            <th class="active col-md-2"><?=lang('player.06');?>:</th>
                                            <td>
                                                <input type="text" name="email" class="form-control input-sm" value="<?=(set_value('email') != null) ? set_value('email') : $player['email'];?>"/>
                                                <span style="color:red;"><?=form_error('email');?></span>
                                            </td>
                                        <?php } ?>
                                    </tr>
                                    <tr>
                                        <th class="active col-md-2"><?=lang('player.60');?>:</th>
                                        <td>
                                            <input type="text" name="zipcode" class="form-control input-sm" value="<?=(set_value('zipcode') != null) ? set_value('zipcode') : $player['zipcode'];?>" />
                                        </td>
                                        <th class="active col-md-2"><?=lang('a_reg.37.placeholder')?>:</th>
                                        <td>
                                            <input type="text" name="region" class="form-control input-sm" value="<?=(set_value('region') != null) ? set_value('region') : $player['region'];?>" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="active col-md-2"><?=lang('player.59');?>:</th>
                                        <td>
                                            <input type="text" name="address" class="form-control input-sm" value="<?=(set_value('address') != null) ? set_value('address') : $player['address'];?>" />
                                            <span style="color:red;"><?=form_error('address');?></span>
                                        </td>
                                        <th class="active col-md-2"><?=lang('player.19');?>:</th>
                                        <td>
                                            <input type="text" name="city" class="form-control input-sm" value="<?=(set_value('city') != null) ? set_value('city') : $player['city'];?>" />
                                            <span style="color:red;"><?=form_error('city');?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="active col-md-2"><?=lang('address_2');?>:</th>
                                        <td>
                                            <input type="text" name="address2" class="form-control input-sm" value="<?=(set_value('address2') != null) ? set_value('address2') : $player['address2'];?>" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <?php if ($this->permissions->checkPermissions('player_verification_question')) {?>
                                            <th class="active col-md-2"><?=lang('player.66');?>:</th>
                                            <td>
                                                <select style="width:100%" name="secretQuestion" id="secretQuestion" class="form-control input-sm" data-toggle="tooltip" title="<?=lang('reg.36');?>" >
                                                    <option value="" selected><?=lang('player.90');?></option>
                                                    <option value="reg.37" <?php if ( $secretQuestion == 'reg.37' ) : ?>
                                                        selected
                                                    <?php endif ?>>
                                                        <?=lang('reg.37');?>
                                                    </option>
                                                    <option value="reg.38" <?php if ( $secretQuestion == 'reg.38' ): ?>
                                                        selected
                                                    <?php endif ?>>
                                                        <?=lang('reg.38');?>
                                                    </option>
                                                    <option value="reg.39" <?php if ( $secretQuestion == 'reg.39' ): ?>
                                                        selected
                                                    <?php endif ?>>
                                                        <?=lang('reg.39');?>
                                                    </option>
                                                    <option value="reg.40" <?php if ( $secretQuestion == 'reg.40' ): ?>
                                                        selected
                                                    <?php endif ?>>
                                                        <?=lang('reg.40');?>
                                                    </option>
                                                    <option value="reg.41" <?php if ( $secretQuestion == 'reg.41' ): ?>
                                                        selected
                                                    <?php endif ?>>
                                                        <?=lang('reg.41');?>
                                                    </option>
                                                </select>
                                                <span style="color:red;"><?=form_error('secretQuestion');?></span>
                                            </td>
                                        <?php } ?>
                                        <?php if ($this->permissions->checkPermissions('player_verification_questions_answer')) {?>
                                            <th class="active col-md-2"><?=lang('player.77');?>:</th>
                                            <td>
                                                <input type="text" name="secretAnswer" class="form-control input-sm" value="<?=(set_value('secretAnswer') != null) ? set_value('secretAnswer') : $player['secretAnswer'];?>" />
                                                <span style="color:red;"><?=form_error('secretAnswer');?></span>
                                            </td>
                                        <?php } ?>
                                    </tr>
                                    <tr>
                                        <?php if ($this->permissions->checkPermissions('player_basic_info') && $this->permissions->checkPermissions('player_contact_information_im_accounts')) {?>
                                            <th class="active col-md-2">
                                                <?php $im1 = $this->config->item('Instant Message 1', 'cust_non_lang_translation'); ?>
                                                <span class="active col-md-3" style="padding-left:0;"><?=($im1) ? $im1 : lang('Instant Message 1');?>:</span>
                                            </th>
                                            <td>
                                                <input type="text" name="imAccount" class="form-control input-sm" value="<?=(set_value('imAccount') != null) ? set_value('imAccount') : ($player['imAccount'] == '0') ? '' : $player['imAccount'];?>" />
                                                <span style="color:red;"><?=form_error('imAccount');?></span>
                                            </td>
                                        <?php } ?>
                                        <?php if ($this->permissions->checkPermissions('player_basic_info') && $this->permissions->checkPermissions('player_contact_information_im_accounts')) {?>
                                            <th class="active col-md-2">
                                                <?php $im2 = $this->config->item('Instant Message 2', 'cust_non_lang_translation'); ?>
                                                <span class="active col-md-3" style="padding-left:0;"><?=($im2) ? $im2 : lang('Instant Message 2');?>:</span>
                                            </th>
                                            <td>
                                                <input type="text" name="imAccount2" class="form-control input-sm" value="<?=(set_value('imAccount2') != null) ? set_value('imAccount2') : ($player['imAccount2'] == '0') ? '' : $player['imAccount2']?>" />
                                                <span style="color:red;"><?=form_error('imAccount2');?></span>
                                            </td>
                                            <tr>
                                            <?php $im3 = $this->config->item('Instant Message 3', 'cust_non_lang_translation'); ?>
                                            <th class="active col-md-2">
                                                <span class="active col-md-3" style="padding-left:0;"><?=($im3) ? $im3 : lang('Instant Message 3');?>:</span>
                                            </th>
                                            <td>
                                                <input type="text" name="imAccount3" class="form-control input-sm" value="<?=(set_value('imAccount3') != null) ? set_value('imAccount3') : ($player['imAccount3'] == '0') ? '' : $player['imAccount3']?>" />
                                                <span style="color:red;"><?=form_error('imAccount3');?></span>
                                            </td>
                                            </tr>
                                        <?php } ?>
                                    </tr>
                                    <?php if ( $this->utils->getConfig('multiple_currency_enabled') && !empty($this->config->item('kingrich_currency_branding'))  ) { ?>
                                        <tr>
                                            <th class="active col-md-2"><?=lang('Currency');?>:</th>
                                            <td>
                                                <select class="form-control input-sm" name="currency" required="required">
                                                    <option value="" ><?=lang('lang.select');?></option>
                                                    <?php foreach ($this->config->item('kingrich_currency_branding') as $key => $value) : ?>
                                                        <option value="<?=$key?>" <?=($player['playerCurrency'] == $key) ? "selected":''?>><?=$key?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <span style="color:red;"><?=form_error('currency');?></span>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <center>
                            <input type="submit" class="btn btn-info btn-md" value="<?=lang('lang.save');?>" />
                            <a href="<?=BASEURL . 'player_management/userInformation/' . $player['playerId']?>" class="btn btn-default btn-md"><?=lang('lang.cancel');?></a>
                        </center>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">
    $(".letters_only").keydown(function (e) {
            var code = e.keyCode || e.which;
            var white_key_code = [46, 8, 9, 27, 32, 13, 110, 190];
            var custom_key_code = JSON.parse('<?=json_encode($this->utils->getConfig('peronal_information_custom_key_code'))?>');

            if(custom_key_code){
                white_key_code = white_key_code.concat(custom_key_code);
            }

            // Allow: backspace, delete, tab, escape, enter and .
            if ($.inArray(code, white_key_code) !== -1 ||
                 // Allow: Ctrl+A
                ( e.ctrlKey === true) || ( e.metaKey === true) ||
                 // Allow: home, end, left, right, down, up
                (code >= 35 && code <= 40)) {
                     // let it happen, don't do anything
                     return;
            }
            // Ensure that it is a number and stop the keypress
            if (e.ctrlKey === true || code < 65 || code > 90) {
                e.preventDefault();
            }
        });

    $(function(){
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
        });
    })
</script>
 <div class="pub_main">
<div class="panel panel-og-green edit-player">
    <div class="panel-heading">
        <!-- <div class="btn-group pull-right" style="margin: 5px 0;">
            <a href="<?=BASEURL . 'messages'?>" class="btn btn-default btn-sm text-uppercase" style="font-weight: bold;"><?=lang('cashier.40');?> <span class="glyphicon glyphicon-comment"></span></a>
        </div> -->
        <h4 class="text-uppercase" style="font-weight:bold;"><?=lang('pi.16');?></h4>
    </div>
    <div class="panel-body ">
        <form action="<?=BASEURL . 'online/postEditPlayer/'?>" method="post" role="form" class="form-horizontal">
            <div class="row ">
                <div class ="col-md-12">
                    <div class="row">
                        <div class ="col-md-7">
                            <div class="form-group">
                                <label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;"><?=lang('pi.1');?></label>
                                <div class="custom-sm-7 custom-leftside custom-pdl-15 margin-left-right">
                                    <input type="text" name="name" id="name" class="form-control" value="<?= ucfirst($player['lastName']) . ' ' . ucfirst($player['firstName'])?>" placeholder="Name" readonly>
                                    <?php echo form_error('name', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                                </div>
                            </div>
                        </div>
                        <div class ="col-md-5">
                            <div class="form-group">
                                <label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;"><?=lang('pi.2');?></label>
                                <div class="custom-sm-7 custom-leftside custom-pdl-15 margin-left-right">
                                  <div class="input-group">
                                    <span class="input-group-addon"><?=$default_prefix_for_username?></span>
                                    <input type="text" name="username" id="username" class="form-control" value="<?=$player['username']?>" placeholder="Username" readonly>
                                    <?php echo form_error('username', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                                  </div>
                                </div>
                            </div>  
                        </div>      
                    </div>
                    <div class="row">
                        <div class ="col-md-4">
                            <div class="form-group">
                                <label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;"><?=lang('pi.3');?></label>
                                <div class="custom-sm-7 custom-leftside custom-pdl-15 margin-left-right">
                                    <input type="text" name="currency" id="currency" class="form-control" value="<?=$player['currency']?>" placeholder="Currency" readonly>
                                    <?php echo form_error('currency', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                                </div>
                            </div>
                        </div>
                        <div class ="col-md-4">
                            <div class="form-group">
                                <label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;"><?=lang('reg.44');?></label>
                                <div class="custom-sm-7 custom-leftside custom-pdl-15 margin-left-right">
                                    <input type="text" name="currency" id="currency" class="form-control" value="<?=$player['invitationCode']?>" readonly>
                                </div>
                            </div>
                        </div>          
                        <div class ="col-md-4">    
                            <div class="form-group fields">
                                <label for="language" class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;">
                                    <?=lang('pi.4');?>
                                    <?php if ($this->player_functions->checkRegisteredFieldsIfRequired('Language') == 0) {?>
                                        <i style="color:#ff6666;">*</i>
                                    <?php } ?>
                                </label>
                                <div class="custom-sm-7 custom-leftside custom-pdl-15 margin-left-right">
                                    <select name="language" id="language" class="form-control">
                                        <option value=""><?=lang('pi.18');?></option>
                                        <option value="Chinese" <?php echo set_select('language', 'Chinese');?> <?=$player['language'] == 'Chinese' ? 'selected' : ''?>><?=lang('reg.27');?></option>
                                        <option value="English" <?php echo set_select('language', 'English');?> <?=$player['language'] == 'English' ? 'selected' : ''?>>English</option>
                                    </select>
                                    <?php echo form_error('language', '<span class="help-block errors" style="color:#ff6666;">', '</span>');?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class ="col-md-4"> 
                            <div class="form-group">
                                <label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" for="contact_number" style="text-align: left;">
                                    <?=lang('pi.5');?>
                                    <?php if ($this->player_functions->checkRegisteredFieldsIfRequired('Contact Number') == 0) {?>
                                        <i style="color:#ff6666;">*</i>
                                    <?php } ?>
                                </label>
                                <div class="custom-sm-7 custom-leftside custom-pdl-15 margin-left-right">
                                    <input type="text" min='0' name="contact_number" id="contact_number" class="form-control number_only" value="<?=$player['contactNumber']?>" placeholder="<?=lang('reg.29');?>">
                                    <?php echo form_error('contact_number', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                                </div>
                            </div>
                        </div>
                        <div class ="col-md-4"> 
                            <div class="form-group">
                                <label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;"><?=lang('pi.6');?></label>
                                <div class="custom-sm-7 custom-leftside custom-pdl-15 margin-left-right ">
                                    <input type="text" class="form-control" value="<?=$player['email']?>" readonly/>
                                    <span class="help-block"><?=lang('notify.75');?></span>
                                </div>
                            </div>
                        </div>  
                        <div class ="col-md-4"> 
                            <div class="form-group">
                                <label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;"><?=lang('pi.7');?></label>
                                <div class="custom-sm-7 custom-leftside custom-pdl-15 margin-left-right">
                                    <input type="text" name="gender" id="gender" class="form-control" value="<?=$player['gender']?>" readonly>
                                    <?php echo form_error('gender', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                                </div>
                            </div>
                        </div>    
                    </div>  

                    <div class="row">
                        <div class ="col-md-6"> 
                            <div class="form-group fields">
                                <label for="im_type" class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;">
                                    <?=lang('pi.8');?>
                                    <?php if ($this->player_functions->checkRegisteredFieldsIfRequired('Instant Message 1') == 0) {?>
                                        <i style="color:#ff6666;">*</i>
                                    <?php }?>
                                </label>
                                <div class="custom-sm-7 custom-leftside custom-pdl-15 margin-left-right">
                                    <select name="im_type" id="im_type" class="form-control" onchange="showDiv(this);">
                                        <option value=""><?=lang('pi.19');?></option>
                                        <option value="QQ" <?php echo set_select('im_type', 'QQ');?> <?=$player['imAccountType'] == 'QQ' ? 'selected' : ''?>>QQ</option>
                                        <option value="Skype" <?php echo set_select('im_type', 'Skype');?> <?=$player['imAccountType'] == 'Skype' ? 'selected' : ''?>>Skype</option>
                                        <option value="MSN" <?php echo set_select('im_type', 'MSN');?> <?=$player['imAccountType'] == 'MSN' ? 'selected' : ''?>>MSN</option>
                                    </select>
                                    <?php echo form_error('im_type', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                                </div>
                            </div>
                            <div class="form-group" id="hide_im" <?=($player['imAccount'] && $player['imAccountType']) || (set_value('im_type')) ? '' : 'style="display: none;"'?>>
                                <label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;">
                                    <strong><span id="account_type"><?=$player['imAccountType'] ? $player['imAccountType'] : set_value('im_type')?></span></strong>
                                </label>
                                <div class="custom-sm-7 custom-leftside custom-pdl-15 margin-left-right">
                                    <input type="text" name="im_account" id="im_account" class="form-control" value="<?=$player['imAccount']?>" >
                                    <?php echo form_error('im_account', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group fields">
                                <label for="im_type2" class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;">
                                    <?=lang('pi.9');?>
                                    <?php if ($this->player_functions->checkRegisteredFieldsIfRequired('Instant Message 2') == 0) {?>
                                        <i style="color:#ff6666;">*</i>
                                    <?php }?>
                                </label>
                                <div class="custom-sm-7 custom-leftside custom-pdl-15 margin-left-right">
                                    <select name="im_type2" id="im_type2" class="form-control" onchange="showDiv2(this);" data-toggle="popover">
                                        <option value=""><?=lang('pi.19');?></option>
                                        <option value="QQ" <?php echo set_select('im_type2', 'QQ');?> <?=$player['imAccountType2'] == 'QQ' ? 'selected' : ''?>>QQ</option>
                                        <option value="Skype" <?php echo set_select('im_type2', 'Skype');?> <?=$player['imAccountType2'] == 'Skype' ? 'selected' : ''?>>Skype</option>
                                        <option value="MSN" <?php echo set_select('im_type2', 'MSN');?> <?=$player['imAccountType2'] == 'MSN' ? 'selected' : ''?>>MSN</option>
                                    </select>
                                    <?php echo form_error('im_type2', '<span class="help-block errors" style="color:#ff6666;">', '</span>');?>
                                </div>
                            </div>

                            <div class="form-group" id="hide_im2" <?=($player['imAccount2'] && $player['imAccountType2']) || (set_value('im_type2')) ? '' : 'style="display: none;"'?>>
                                <label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;">
                                    <strong><span id="account_type2"><?=$player['imAccountType2'] ? $player['imAccountType2'] : set_value('im_type2')?></span></strong>
                                </label>
                                <div class="custom-sm-7 custom-leftside custom-pdl-15 margin-left-right">
                                    <input type="text" name="im_account2" id="im_account2" class="form-control" value="<?=$player['imAccount2']?>">
                                    <?php echo form_error('im_account2', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8"> 
                            <div class="form-group">
                                <label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;"><?=lang('pi.11');?></label>
                                <div class="custom-sm-7 custom-leftside custom-pdl-15 margin-left-right">
                                    <input type="text" name="address" id="address" class="form-control" data-toggle="popover" value="<?=$player['address']?>"/>
                                    <?php echo form_error('address', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                                </div>
                            </div>
                        </div>
                        <div class ="col-md-4">
                            <div class="form-group">
                                <label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;"><?=lang('pi.12');?></label>
                                <div class="custom-sm-7 custom-leftside custom-pdl-15 margin-left-right">
                                    <input type="text" name="city" id="city" class="form-control letters_only" value="<?=$player['city']?>" >
                                    <?php echo form_error('city', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;"><?=lang('pi.10');?></label>
                                <div class="custom-sm-7 custom-leftside custom-pdl-15 margin-left-right">
                                    <select name="country" id="country" class="form-control" >
                                        <option value=""><?=lang('pi.20');?></option>
                                        <?php foreach (unserialize(COUNTRY_LIST) as $key): ?>
                                                <option value="<?=$key?>" <?=($player['country'] == $key) ? 'selected' : ''?> >
                                                    <?=lang('country.' . $key)?>
                                                </option>
                                        <?php endforeach;?>
                                    </select>
                                    <?php echo form_error('country', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;">
                                    <?=lang('pi.13');?>
                                    <?php if ($this->player_functions->checkRegisteredFieldsIfRequired('Nationality') == 0) {?>
                                        <i style="color:#ff6666;">*</i>
                                    <?php }?>
                                </label>
                                <div class="custom-sm-7 custom-leftside custom-pdl-15 margin-left-right">
                                    <input type="text" name="citizenship" id="citizenship" class="form-control" value="<?=$player['citizenship']?>">
                                    <?php echo form_error('citizenship', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;"><?=lang('reg.12');?></label>
                                <div class="custom-sm-7 custom-leftside custom-pdl-15 margin-left-right">
                                    <input type="text" name="birthdate" id="birthdate" class="form-control" value="<?=$player['birthdate']?>" readonly>
                                    <?php echo form_error('birthdate', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;">
                                    <?=lang('pi.14');?>
                                    <?php if ($this->player_functions->checkRegisteredFieldsIfRequired('BirthPlace') == 0) {?>
                                        <i style="color:#ff6666;">*</i>
                                    <?php }?>
                                </label>
                                <div class="custom-sm-7 custom-leftside custom-pdl-15 margin-left-right">
                                    <input type="text" name="birthplace" id="birthplace" class="form-control" value="<?=$player['birthplace']?>">
                                    <?php echo form_error('birthplace', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="form-group pull-right">
                        <div class="custom-offset-3 custom-sm-6 custom-pdl-15">
                            <button type="submit" class="btn btn-hotel"><?=lang('pi.17');?></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!--MODAL for edit column-->
<div id="customerService" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="customerService" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content panel-info">
    <div class="modal-header panel-heading">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">Customer Service</h3>
    </div>
    <div class="modal-body">
        <div class=" row">
            <div class="col-md-12">
                <div class="help-block">
                    You may contact or send an email to our customer service.
                </div>
            </div>
        </div>

        <div class="row">
            <form action="" method="post" role="form" id="modal_column_form">
                <div class="row">
                    <h6><label class="col-md-2 col-md-offset-1 control-label"  for="report_type">Type:<i style="color:#ff6666;">*</i> </label></h6>

                    <div class="col-md-4">
                            <select name="report_type" id="report_type" class="form-control">
                                <option value="">Select</option>
                                <option value="">Reports</option>
                                <option value="">Bugs/Errors</option>
                                <option value="">Comments/Suggestions</option>
                            </select>
                                <?php echo form_error('report_type', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                        <br/>
                    </div>
                </div>

                <div class="row">
                    <h6><label class="col-md-2 col-md-offset-1 control-label"  for="subject">Subject:<i style="color:#ff6666;">*</i> </label></h6>

                    <div class="col-md-4">
                        <input type="text" name="subject" id="subject" class="form-control">
                            <?php echo form_error('subject', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                        <br/>
                    </div>
                </div>

                <div class="row">
                    <h6><label class="col-md-2 col-md-offset-1 control-label"  for="subject">Desciption:<i style="color:#ff6666;">*</i> </label></h6>

                    <div class="col-md-4">
                        <textarea name="report" id="report" class="form-control" style="width: 255px; max-width: 255px; height: 90px;  max-height: 90px;" data-toggle="popover" data-placement="right" data-trigger="hover" title="Notice" data-content="State your report."></textarea>
                            <?php echo form_error('report', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                        <br/>
                    </div>
                </div>

            </form>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
        <button class="btn btn-primary" id="send" name="send">Send</button>
    </div>
</div>
</div>
</div>
</div>
<!--end of MODAL for edit column-->
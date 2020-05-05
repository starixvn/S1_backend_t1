<style type="text/css">
    .panel-heading a.btn{
        margin-right: 4px;
    }
</style>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="icon-coin-dollar"></i> <?=lang('pay.payment_account');?>  -  <span style ="font-size: small"><?=lang('pay.payment_account.desc');?><span>
            <a href="#" class="btn btn-xs pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-warning'?>" id="add_payment_account" style="color: #fff;">
                <span id="addPaymentAccountGlyhicon" class="fa fa-plus"></span>
                <?php echo lang('pay.add_payment_account'); ?>
            </a>
            <a href="<?php echo site_url('payment_management/defaultCollectionAccount'); ?>" class="btn btn-xs pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-info'?>">
                <span class="fa fa-cog"></span>
                <?php echo lang('Default Collection Account'); ?>
            </a>
            <?php if($tly_actived){ ?>
            <a href="<?php echo site_url('payment_account_management/api_bank_list/'.TLY_PAYMENT_API); ?>" class="btn btn-info btn-xs pull-right">
                <span class="fa fa-university"></span>
                <?php echo lang('TLY Bank List'); ?>
            </a>
            <?php } ?>
        </h4>
    </div>
    <div class="panel-body" id="details_panel_body">
        <!-- add payment account -->
        <div class="row add_payment_account_sec">
            <div class="col-md-12">
                <div class="well" style="overflow: auto;">
                    <form class="form-horizontal" id="form_add" action="<?=site_url('payment_account_management/add_edit_payment_account')?>" method="POST" role="form" enctype="multipart/form-data">
                        <div class="form-group" style="margin-left:0px;margin-right: 0px;">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="control-label" for="" style="font-size:11px;"><?=lang('pay.payment_name');?>:* </label>
                                    <p>
                                        <?php //echo form_dropdown('payment_type_id', $payment_types, '', ' required form-control');?>
                                        <select id="payment_type_id_for_add" data-flag='#flag_for_add' name="payment_type_id" class="form-control input-sm" required>
                                            <?php foreach ($payment_types as $key => $value) {?>
                                                <option value="<?=$key?>"><?=$value?></option>
                                            <?php } ?>
                                        </select>
                                        <?php echo form_error('payment_type_id', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                    </p>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label" for="" style="font-size:11px;"><?=lang('pay.payment_account_flag');?>:* </label>
                                    <p>
                                        <?php //echo form_dropdown('flag', $payment_account_flags, '', ' required ');?>
                                        <select id="flag_for_add" name="flag" class="form-control input-sm" required>
                                            <?php foreach ($payment_account_flags as $key => $value) {?>
                                                <option value="<?=$key?>"><?=$value?></option>
                                            <?php } ?>
                                        </select>
                                        <?php echo form_error('flag', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                    </p>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label" for="" style="font-size:11px;"><?=lang('pay.second_category_flag');?>:* </label>
                                    <p>
                                        <?php //echo form_dropdown('second_category_flag', $second_category_flags, '', ' required ');?>
                                        <select id="flag_for_add" name="second_category_flag" class="form-control input-sm" required>
                                            <?php foreach ($second_category_flags as $key => $value) {?>
                                                <option value="<?=$key?>"><?=$value?></option>
                                            <?php } ?>
                                        </select>
                                        <?php echo form_error('second_category_flag', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                    </p>
                                </div>
                                <div class="col-md-3">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="control-label" for="" style="font-size:11px;"><?=lang('pay.payment_account_name');?>:* </label>
                                    <input type="text" required name="payment_account_name" id="payment_account_name" class="form-control input-sm" >
                                    <?php echo form_error('payment_account_name', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label" for="" style="font-size:11px;"><?=lang('pay.payment_account_number');?>: </label>
                                    <!-- <input type="number" min="0"  data-number-nogrouping="true" id="payment_account_number" name="payment_account_number" class="form-control input-sm"> -->
                                    <?php if($this->utils->isEnabledFeature('allow_special_characters_on_account_number')) { ?>
                                        <input type="text" min="0"  data-number-nogrouping="true" id="payment_account_number" name="payment_account_number" class="form-control input-sm txt_account_number">
                                    <?php } else { ?>
                                        <input type="number" min="0"  data-number-nogrouping="true" id="payment_account_number" name="payment_account_number" class="form-control input-sm number_only">
                                    <?php } ?>
                                    <?php echo form_error('payment_account_number', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label" for="" style="font-size:11px;"><?=lang('pay.payment_branch_name');?>: </label>
                                    <input type="text"  name="payment_branch_name" id="payment_branch_name" class="form-control input-sm">
                                    <?php echo form_error('payment_branch_name', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="control-label" for="daily_max_depsit_amount" style="font-size:11px;"><?=lang('pay.daily_max_depsit_amount');?>:* </label>
                                    <input type="number" min="0" required value=""  id="daily_max_depsit_amount" data-number-to-fixed="2" maxlength="12" name="max_deposit_daily" class="form-control input-sm" >
                                    <p class="help-block dmda-help-block pull-left">
                                        <?php echo form_error('max_deposit_daily', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                    </p>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label" for="total_deposit_limit" style="font-size:11px;"><?=lang('pay.total_deposit_limit');?>:* </label>
                                    <input type="number" min="0" required value=""  id="total_deposit_limit" data-number-to-fixed="2" maxlength="12" name="total_deposit" class="form-control input-sm" >
                                    <p class="help-block tdl-help-block pull-left">
                                        <?php echo form_error('total_deposit', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                    </p>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label" for="min_deposit_trans" style="font-size:11px;"><?=lang('Min deposit per transaction');?>: </label>
                                    <input type="number" min="0" value=""  id="min_deposit_trans" data-number-to-fixed="2" maxlength="12" name="min_deposit_trans" class="form-control input-sm" >
                                    <?php echo form_error('min_deposit_trans', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label" for="max_deposit_trans" style="font-size:11px;"><?=lang('Max deposit per transaction');?>: </label>
                                    <input type="number" min="0" value=""  id="max_deposit_trans" data-number-to-fixed="2" maxlength="12" name="max_deposit_trans" class="form-control input-sm" >
                                    <?php echo form_error('max_deposit_trans', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="control-label" for="deposit_fee_percentage" style="font-size:11px;"><?=lang('Deposit Fee Percentage');?> % </label>
                                    <input type="number" min="0" step="any" id="deposit_fee_percentage" name="deposit_fee_percentage" class="form-control input-sm" >
                                    <?php echo form_error('deposit_fee_percentage', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label" for="min_deposit_fee" style="font-size:11px;"><?=lang('Min Deposit Fee');?></label>
                                    <input type="number" min="0" step="any" id="min_deposit_fee" name="min_deposit_fee" class="form-control input-sm" >
                                    <?php echo form_error('min_deposit_fee', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label" for="max_deposit_fee" style="font-size:11px;"><?=lang('Max Deposit Fee');?></label>
                                    <input type="number" min="0" step="any" id="max_deposit_fee" name="max_deposit_fee" class="form-control input-sm" >
                                    <?php echo form_error('max_deposit_fee', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                            </div>
                            <div class="row">
                                 <div class="col-md-3">
                                    <label class="control-label" for="" style="font-size:11px;"><?=lang('cms.06');?>(<?=lang('Only list Every Time Deposit')?>): </label>
                                    <select class="form-control" name="promocms_id">
                                        <option value="">-----<?php echo lang('N/A');?>-----</option>
                                    <?php
                                        if(!empty($promoCms)){
                                            foreach ($promoCms as $v): ?>
                                            <option value="<?php echo $v['promoCmsSettingId']; ?>"><?php echo $v['promoName'] ?></option>
                                            <?php endforeach;
                                        }
                                    ?>
                                    </select>
                                    <?php echo form_error('promocms_id', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label" for="" style="font-size:11px;"><?=lang('pay.payment_order');?>: </label>
                                    <input type="number" min="0" name="payment_order" id="payment_order" class="form-control input-sm" >
                                    <?php echo form_error('payment_order', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                            </div>
                            <?php ####################################################################################################### ?>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label class="control-label" for="" style="font-size:11px;"><?=lang('player.ui14');?>: </label>
                                    <input type="number" min="0" name="total_approved_deposit_count" id="total_approved_deposit_count" class="form-control input-sm" >
                                    <p class="help-block playerLevels-help-block pull-left"><i style="font-size:14px;color:#919191;"><?=lang('deposit.specific.count');?></i></p>
                                    <?php echo form_error('total_approved_deposit_count', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="control-label" class="control-label" style="font-size:12px;"><?=lang('pay.playerlev');?>:*</label>
                                    <?php echo form_multiselect('playerLevels[]',is_array( $levels) ?  $levels : array(), $form['playerLevels'], ' class="form-control input-sm chosen-select playerLevels" id="addPlayerLevels" data-placeholder="' . lang("cms.selectnewlevel") . '" data-untoggle="checkbox" data-target="#form_add .toggle-checkbox .playerLevels" ') ?>
                                    <p class="help-block playerLevels-help-block pull-left"><i style="font-size:12px;color:#919191;"><?=lang('pay.applevbankacct');?></i></p>
                                    <div class="checkbox pull-right" style="margin-top: 5px">
                                        <label><input type="checkbox" class="toggle-checkbox" data-toggle="checkbox" data-target="#form_add .playerLevels option"> <?=lang('lang.selectall');?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="control-label" class="control-label" style="font-size:12px;"><?=lang('Affiliates');?>:</label>
                                    <?php echo form_multiselect('affiliates[]', is_array($affiliates) ? $affiliates : array(), $form['affiliates'], ' class="form-control input-sm chosen-select affiliates" id="addAffiliates" data-placeholder="' . lang("Select new applicable affiliates") . '" data-untoggle="checkbox" data-target="#form_add .toggle-checkbox .affiliates" ') ?>
                                    <p class="help-block affiliates-help-block pull-left"><i style="font-size:12px;color:#919191;"><?=lang('Applicable Affiliates for this Bank Account');?></i></p>
                                    <div class="checkbox pull-right" style="margin-top: 5px">
                                        <label><input type="checkbox" class="toggle-checkbox" data-toggle="checkbox" data-target="#form_add .affiliates option"> <?=lang('lang.selectall');?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="control-label" class="control-label" style="font-size:12px;"><?=lang('Agents');?>:</label>
                                    <?php echo form_multiselect('agents[]', is_array($agents) ? $agents : array(), $form['agents'], ' class="form-control input-sm chosen-select agents" id="addagents" data-placeholder="' . lang("Select new applicable agents") . '" data-untoggle="checkbox" data-target="#form_add .toggle-checkbox .agents" ') ?>
                                    <p class="help-block agents-help-block pull-left"><i style="font-size:12px;color:#919191;"><?=lang('Applicable Agents for this Bank Account');?></i></p>
                                    <div class="checkbox pull-right" style="margin-top: 5px">
                                        <label><input type="checkbox" class="toggle-checkbox" data-toggle="checkbox" data-target="#form_add .agents option"> <?=lang('lang.selectall');?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="control-label" class="control-label" style="font-size:12px;"><?=lang('Players');?>:</label>
                                    <select class="js-data-example-ajax" id="addPlayers" name="players[]" multiple="multiple" style="width: 100%;"></select>
                                    <p class="help-block players-help-block pull-left"><i style="font-size:12px;color:#919191;"><?=lang('Applicable Players for this Bank Account');?></i></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="control-label" for=""><?=lang('player.upay05');?>: </label>
                                    <textarea name="notes" id="notes" class="form-control input-sm" cols="10" rows="5" style="height: auto;"></textarea>
                                    <?php echo form_error('notes', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                            </div>
                        </div>
                        <center>
                            <span class="btn btn-sm addpaymentaccount-cancel-btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default'?>" data-toggle="modal"><?=lang('lang.cancel');?></span>
                            <input type="submit" value="<?=lang('lang.add');?>" class="btn btn-sm review-btn custom-btn-size <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-info'?>" data-toggle="modal" />
                        </center>
                    </form>
                </div>
                <hr/>
            </div>
        </div>

        <!-- edit payment account -->
        <div class="row edit_payment_account_sec" id="edit_payment_account_sec">
            <div class="col-md-12">
                <div class="well" style="overflow:auto">
                    <form class="form-horizontal" id="form_edit" action="<?=site_url('payment_account_management/add_edit_payment_account')?>" method="POST" role="form">
                        <input type="hidden" name="payment_account_id" >
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="control-label" for="" style="font-size:11px;"><?=lang('pay.payment_name');?>:* </label>
                                    <p>
                                        <?php //echo form_dropdown('payment_type_id', $payment_types, '', ' required ');?>
                                        <select id="payment_type_id_for_edit" data-flag='#flag_for_edit' name="payment_type_id" class="form-control input-sm" required>
                                            <?php foreach ($payment_types as $key => $value) {?>
                                                <option value="<?=$key?>"><?=$value?></option>
                                            <?php } ?>
                                        </select>
                                        <?php echo form_error('payment_type_id', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                    </p>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label" for="" style="font-size:11px;"><?=lang('pay.payment_account_flag');?>:* </label>
                                    <p>
                                        <?php //echo form_dropdown('flag', $payment_account_flags, '', ' required ');?>
                                        <select id='#flag_for_edit' name="flag" class="form-control input-sm" required>
                                            <?php
                                            if(!empty($payment_account_flags)){
                                                foreach ($payment_account_flags as $key => $value) {?>
                                                <option value="<?=$key?>"><?=$value?></option>
                                            <?php
                                                }
                                            }?>
                                        </select>
                                        <?php echo form_error('flag', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                    </p>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label" for="" style="font-size:11px;"><?=lang('pay.second_category_flag');?>:* </label>
                                    <p>
                                        <?php //echo form_dropdown('second_category_flag', $second_category_flags, '', ' required ');?>
                                        <select id='#flag_for_edit' name="second_category_flag" class="form-control input-sm" required>
                                            <?php
                                            if(!empty($second_category_flags)){
                                                foreach ($second_category_flags as $key => $value) {?>
                                                <option value="<?=$key?>"><?=$value?></option>
                                            <?php
                                                }
                                            }?>
                                        </select>
                                        <?php echo form_error('second_category_flag', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                    </p>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label" for="" style="font-size:11px;"><?=lang('pay.external_system_id');?>: </label>
                                    <p class="external_system_id readonly"></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="control-label" for="" style="font-size:11px;"><?=lang('pay.payment_account_name');?>:* </label>
                                    <input type="text" name="payment_account_name" class="form-control input-sm" required>
                                    <?php echo form_error('payment_account_name', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label" for="" style="font-size:11px;"><?=lang('pay.payment_account_number');?>: </label>
                                    <!-- <input type="number" min="0"  data-number-nogrouping="true" name="payment_account_number" class="form-control input-sm" > -->
                                    <?php if($this->utils->isEnabledFeature('allow_special_characters_on_account_number')) { ?>
                                        <input type="text" min="0"  data-number-nogrouping="true" name="payment_account_number" class="form-control input-sm txt_account_number">
                                    <?php } else { ?>
                                        <input type="text" min="0"  data-number-nogrouping="true" name="payment_account_number" class="form-control input-sm number_only" >
                                    <?php } ?>
                                    <?php echo form_error('payment_account_number', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label" for="" style="font-size:11px;"><?=lang('pay.payment_branch_name');?>: </label>
                                    <input type="text"  name="payment_branch_name" class="form-control input-sm">
                                    <?php echo form_error('payment_branch_name', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="control-label" for="" style="font-size:11px;"><?=lang('pay.daily_max_depsit_amount');?>:* </label>
                                    <input type="number" min="0" required value=""  id="daily_max_depsit_amount"  maxlength="12" name="max_deposit_daily" class="form-control input-sm">
                                     <p class="help-block dmda-help-block pull-left">
                                        <?php echo form_error('max_deposit_daily', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                    </p>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label" for="" style="font-size:11px;"><?=lang('pay.total_deposit_limit');?>:* </label>
                                    <input type="number" min="0" required value=""  id="total_deposit_limit" maxlength="12" name="total_deposit" class="form-control input-sm" >
                                     <p class="help-block tdl-help-block pull-left">
                                        <?php echo form_error('total_deposit', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                    </p>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label" for="min_deposit_trans" style="font-size:11px;"><?=lang('Min deposit per transaction');?>: </label>
                                    <input type="number" min="0" value=""  id="min_deposit_trans" data-number-to-fixed="2" maxlength="12" name="min_deposit_trans" class="form-control input-sm" >
                                    <?php echo form_error('min_deposit_trans', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label" for="max_deposit_trans" style="font-size:11px;"><?=lang('Max deposit per transaction');?>: </label>
                                    <input type="number" min="0" value=""  id="max_deposit_trans" data-number-to-fixed="2" maxlength="12" name="max_deposit_trans" class="form-control input-sm" >
                                    <?php echo form_error('max_deposit_trans', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="control-label" for="deposit_fee_percentage" style="font-size:11px;"><?=lang('Deposit Fee Percentage');?> % </label>
                                    <input type="number" min="0" step="any" id="deposit_fee_percentage" name="deposit_fee_percentage" class="form-control input-sm" >
                                    <?php echo form_error('deposit_fee_percentage', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label" for="min_deposit_fee" style="font-size:11px;"><?=lang('Min Deposit Fee');?></label>
                                    <input type="number" min="0" step="any" id="min_deposit_fee" name="min_deposit_fee" class="form-control input-sm" >
                                    <?php echo form_error('min_deposit_fee', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label" for="max_deposit_fee" style="font-size:11px;"><?=lang('Max Deposit Fee');?></label>
                                    <input type="number" min="0" step="any" id="max_deposit_fee" name="max_deposit_fee" class="form-control input-sm" >
                                    <?php echo form_error('max_deposit_fee', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                                <!--
                                <div class="col-md-3">
                                    <label class="control-label" for="" style="font-size:11px;"><?=lang('pay.account_image');?>: </label>
                                    <?php echo form_error('account_image_filepath', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label" for="" style="font-size:11px;"><?=lang('pay.logo_link');?>: </label>
                                    <?php echo form_error('logo_link', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                                -->
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="control-label" for="" style="font-size:11px;"><?=lang('cms.06');?>(<?=lang('Only list Every Time Deposit')?>): </label>
                                    <select class="form-control" name="promocms_id">
                                        <option value="">-----<?php echo lang('N/A');?>-----</option>

                                    <?php
                                        if(!empty($promoCms)){
                                            foreach ($promoCms as $v): ?>
                                                <option value="<?php echo $v['promoCmsSettingId']; ?>"><?php echo $v['promoName'] ?></option>
                                            <?php endforeach;
                                        }
                                    ?>
                                    </select>
                                    <?php echo form_error('promocms_id', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label" for="" style="font-size:11px;"><?=lang('pay.payment_order');?>: </label>
                                    <input type="number" min="0" name="payment_order" class="form-control input-sm" >
                                    <?php echo form_error('payment_order', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                            </div>
                            <?php ####################################################################################################### ?>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label class="control-label" for="" style="font-size:11px;"><?=lang('player.ui14');?>: </label>
                                    <input type="number" min="0" name="total_approved_deposit_count"  class="form-control input-sm" >
                                    <p class="help-block playerLevels-help-block pull-left"><i style="font-size:14px;color:#919191;"><?=lang('deposit.specific.count');?></i></p>
                                    <?php echo form_error('total_approved_deposit_count', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="control-label" class="control-label" style="font-size:12px;"><?=lang('pay.playerlev');?>:*</label>
                                    <?php echo form_multiselect('playerLevels[]', is_array($levels) ? $levels : array(), $form['playerLevels'], ' class="form-control input-sm chosen-select playerLevels" id="editPlayerLevels" data-placeholder="' . lang("cms.selectnewlevel") . '" data-untoggle="checkbox" data-target="#form_edit .toggle-checkbox .playerLevels" ') ?>
                                    <p class="help-block playerLevels-help-block pull-left"><i style="font-size:12px;color:#919191;"><?=lang('pay.applevbankacct');?></i></p>
                                    <div class="checkbox pull-right" style="margin-top: 5px">
                                        <label><input type="checkbox" class="toggle-checkbox" data-toggle="checkbox" data-target="#form_edit .playerLevels option"> <?=lang('lang.selectall');?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="control-label" class="control-label" style="font-size:12px;"><?=lang('Affiliates');?>:</label>
                                    <?php echo form_multiselect('affiliates[]', is_array($affiliates) ? $affiliates : array(), $form['affiliates'], ' class="form-control input-sm chosen-select affiliates" id="editAffiliates" data-placeholder="' . lang("Select new applicable affiliates") . '" data-untoggle="checkbox" data-target="#form_edit .toggle-checkbox .affiliates" ') ?>
                                    <p class="help-block affiliates-help-block pull-left"><i style="font-size:12px;color:#919191;"><?=lang('Applicable Affiliates for this Bank Account');?></i></p>
                                    <div class="checkbox pull-right" style="margin-top: 5px">
                                        <label><input type="checkbox" class="toggle-checkbox" data-toggle="checkbox" data-target="#form_edit .affiliates option"> <?=lang('lang.selectall');?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="control-label" class="control-label" style="font-size:12px;"><?=lang('Agents');?>:</label>
                                    <?php echo form_multiselect('agents[]', is_array($agents) ? $agents : array(), $form['agents'], ' class="form-control input-sm chosen-select agents" id="editAgents" data-placeholder="' . lang("Select new applicable agents") . '" data-untoggle="checkbox" data-target="#form_edit .toggle-checkbox .agents" ') ?>
                                    <p class="help-block agents-help-block pull-left"><i style="font-size:12px;color:#919191;"><?=lang('Applicable Agents for this Bank Account');?></i></p>
                                    <div class="checkbox pull-right" style="margin-top: 5px">
                                        <label><input type="checkbox" class="toggle-checkbox" data-toggle="checkbox" data-target="#form_edit .agents option"> <?=lang('lang.selectall');?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="control-label" class="control-label" style="font-size:12px;"><?=lang('Players');?>:</label>
                                    <select class="js-data-example-ajax" id="editPlayers" name="players[]" multiple="multiple" style="width: 100%;"></select>
                                    <p class="help-block players-help-block pull-left"><i style="font-size:12px;color:#919191;"><?=lang('Applicable Players for this Bank Account');?></i></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="control-label" for=""><?=lang('player.upay05');?>: </label>
                                    <textarea name="notes" class="form-control input-sm" cols="10" rows="5" style="height: auto;"></textarea>
                                    <?php echo form_error('notes', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                            </div>
                        </div>
                        <center>
                            <input type="submit" value="<?=lang('lang.save');?>" class="btn btn-sm btn-info review-btn custom-btn-size" data-toggle="modal" />
                            <span class="btn btn-sm btn-default editpaymentaccount-cancel-btn custom-btn-size" data-toggle="modal" /><?=lang('lang.cancel');?></span>
                        </center>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <form action="<?=site_url('payment_account_management/delete_selected_payment_account')?>" method="POST" id="delete_form" role="form" onsubmit="">
                    <div id="payment_account_table">

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover table-condensed" id="my_table" style="margin: 0px 0 0 0; width: 100%;">
                                <div class="btn-action">
                                    <?php if(isset($delete_collection_account)){ ?>
                                        <button type="submit" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-chestnutrose' : 'btn-danger'?>" data-toggle="tooltip" data-placement="top" title="<?=lang('cms.deletesel');?>">
                                        <i class="glyphicon glyphicon-trash" style="color:white;"></i> <?=lang('cms.deletesel');?>
                                    </button>&nbsp;
                                    <?php } ?>
                                     <!--  <a href="<?=site_url('payment_account_management/export_to_excel')?>" class="btn btn-sm btn-success btn-sm" data-toggle="tooltip" title="<?=lang('lang.export');?>" data-placement="top">
                                        <i class="glyphicon glyphicon-share"></i> <?=lang('lang.export');?>
                                    </a> -->
                                </div>
                                <?php if(isset($delete_collection_account) || isset($export_report_permission)){ ?>
                                <br><br>
                                <?php } ?>
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/></th>
                                        <th><?=lang('lang.action');?></th>
                                        <th><?=lang('pay.payment_order');?></th>
                                        <th><?=lang('pay.payment_name');?></th>
                                        <th><?=lang('pay.payment_account_flag');?></th>
                                        <th><?=lang('pay.payment_account_name');?></th>
                                        <th><?=lang('pay.payment_account_number');?></th>
                                        <th><?=lang('pay.payment_branch_name');?></th>
                                        <th><?=lang('Min deposit per transaction');?></th>
                                        <th><?=lang('Max deposit per transaction');?></th>
                                        <th><?=lang('pay.daily_max_depsit_amount');?></th>
                                        <th><?=lang('pay.total_deposit_limit');?></th>
                                        <th><?=lang('pay.account_image');?></th>
                                        <th><?=lang('pay.logo_link');?></th>
                                        <th><?=lang('lang.status');?></th>
                                        <th><?=lang('cms.notes');?></th>
                                        <th><?=lang('cms.createdon');?></th>
                                        <th><?=lang('cms.createdby');?></th>
                                        <th><?=lang('cms.updatedon');?></th>
                                        <th><?=lang('cms.updatedby');?></th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
if (!empty($banks)) {
    foreach ($banks as $row) { ?>
                                          <tr class="<?php echo $row->status==Payment_account::STATUS_DISABLED ? 'danger' : '' ; ?>" >
                                            <td><?=$row->payment_account_id?></td>
                                            <td>
                                                <input type="checkbox" class="checkWhite" id="<?=$row->payment_account_id?>" name="payment_account[]" value="<?=$row->payment_account_id?>" />
                                            </td>
                                            <td>
                                                <div class="actionPaymentAccountGroup">
                                                    <span style="cursor:pointer;font-size: 15px;margin-top: 3px;width: 31px;" class="glyphicon glyphicon-edit editPaymentAccountBtn btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-info'?>"id="edit-row"  data-toggle="tooltip" title="<?=lang('lang.edit');?>" onclick="PaymentAccountManagementProcess.getPaymentAccountDetails(<?=$row->payment_account_id?>)" data-placement="right"></span>
                                                    <?php if($row->status==Payment_account::STATUS_DISABLED){ ?>
                                                        <br>
                                                        <span data-toggle="tooltip" title="<?=lang('Active');?>" class="fa fa-toggle-off btn btn-xs btn-info" style="font-size: 15px;margin-top: 5px;" data-placement="right" onclick="activePaymentAccount('<?=site_url('payment_account_management/active_payment_account_item/' . $row->payment_account_id)?>');">
                                                        </span>

                                                    <?php }else{?>
                                                            <br>
                                                            <span data-toggle="tooltip" onclick="inactivePaymentAccount('<?=site_url('payment_account_management/inactive_payment_account_item/' . $row->payment_account_id)?>');" title="<?=lang('Inactive');?>" class="fa fa-toggle-on btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-chestnutrose' : 'btn-info'?>" style="font-size: 15px;margin-top: 5px;" data-placement="right">
                                                            </span>

                                                    <?php }?>
                                                    <?php if($this->utils->isEnabledFeature('enable_collection_account_delete_button')) { ?>
                                                    <?php if(isset($delete_collection_account)){ ?>
                                                        <br>
                                                        <span style="cursor:pointer;font-size: 15px;margin-top: 3px;width: 31px;" class="glyphicon glyphicon-trash deletePaymentAccountBtn btn btn-xs btn-info" id="delete-row" data-toggle="tooltip" title="<?=lang('lang.delete');?>" onclick="deleteThisAccount('<?=site_url('payment_account_management/delete_selected_payment_account/' . $row->payment_account_id)?>')"  data-placement="right"></span>
                                                    <?php }?>
                                                    <?php }?>
                                                </div>
                                            </td>
                                            <td><?=$row->payment_order?>
                                                <td><?=empty($row->payment_type) ? '<i class="help-block">' . lang("lang.norecyet") . '</i>' : lang($row->payment_type)?></td>
                                                <td><?=empty($row->flag_name) ? '<i class="help-block">' . lang("lang.norecyet") . '</i>' : $row->flag_name?></td>
                                                <td><?=empty($row->payment_account_name) ? '<i class="help-block">' . lang("lang.norecyet") . '</i>' : $row->payment_account_name?></td>
                                                <td><?=empty($row->payment_account_number) ? '<i class="help-block">' . lang("lang.norecyet") . '</i>' : $row->payment_account_number?></td>
                                                <td><?=empty($row->payment_branch_name) ? '<i class="help-block">' . lang("lang.norecyet") . '</i>' : $row->payment_branch_name?></td>
                                             <td><?=$row->min_deposit_trans == 0 ? '<i class="help-block">' . lang("lang.norecyet") . '</i>' : number_format($row->min_deposit_trans, 2)?></td>
                                             <td><?=$row->max_deposit_trans == 0 ? '<i class="help-block">' . lang("lang.norecyet") . '</i>' : number_format($row->max_deposit_trans, 2)?></td>
                                             <td><?=number_format($row->daily_deposit_amount, 2)?> / <?=empty($row->max_deposit_daily) ? '<i class="help-block">' . lang("lang.norecyet") . '</i>' : number_format($row->max_deposit_daily, 2)?></td>
                                             <td><?=number_format($row->total_deposit_amount, 2)?> / <?=empty($row->total_deposit) ? '<i class="help-block">' . lang("lang.norecyet") . '</i>' : number_format($row->total_deposit, 2)?></td>
                                             <td><?php if (!$row->account_image_filepath) {?>
                                                    <a href="#" class="btn btn-xs btn-info" onclick="qrcode_upload('<?=$row->id?>')">Upload QR Code</a>
                                                <?php } else { ?>
                                            <img name = "<?=$row->account_image_filepath?>" src="<?=$this->utils->imageUrl('account/' . $row->account_image_filepath)?>" style="width=100px; height: 60px;"/>
                                            <a href="#" class="btn btn-xs btn-success" onclick="qrcode_upload('<?=$row->id?>')"><?=lang('payment.changeImage')?></a>
                                            <a href="#" class="btn btn-xs btn-success removeImage" onclick="qrcode_remove('<?=$row->id?>','<?=$row->account_image_filepath?>')"><?=lang('Remove')?></a>

                                            <?php }
                                            ?>
                                            </td>
                                            <td><?php if (!$row->account_icon_filepath) {?>

                                                    <a href="#" class="btn btn-xs btn-info" onclick="logo_upload('<?=$row->id?>')">Upload Logo</a>
                                                <?php } else {  ?>

                                            <img src="<?=$this->utils->imageUrl('account/' . $row->account_icon_filepath)?>" style="width=100px; height: 60px;"/>
                                            <a href="#" class="btn btn-xs btn-success" onclick="logo_upload('<?=$row->id?>')"><?=lang('payment.changeImage')?></a>
                                            <?php }
                                            ?>
                                            </td>
                                             <td><i class="help-block"><?=($row->status == Payment_account::STATUS_DISABLED) ? lang('status.disabled') : lang('status.normal');?></i></td>
                                             <td><?=empty($row->notes) ? '<i class="help-block">' . lang("lang.norecyet") . '</i>' : $row->notes?></td>
                                             <td><?=empty($row->created_at) ? '<i class="help-block">' . lang("lang.norecyet") . '</i>' : $row->created_at?></td>
                                             <td><?=empty($row->created_by) ? '<i class="help-block">' . lang("lang.norecyet") . '</i>' : $row->created_by?></td>
                                             <td><?=empty($row->updated_at) ? '<i class="help-block">' . lang("lang.norecyet") . '</i>' : $row->updated_at?></td>
                                             <td><?=empty($row->updated_by) ? '<i class="help-block">' . lang("lang.norecyet") . '</i>' : $row->updated_by?></td>
                                         </tr>
    <?php }
}
?>
                             </tbody>
                         </table>
                     </div>
            </form>

                     <div class="col-md-4" id="upload_sec" draggable="true" style="position: absolute;left: 0;top: 0;">
                        <!-- start upload qr code -->
                        <div class="panel panel-primary" id="upload_qrcode_sec">
                            <div class="panel-heading">
                                <h4 class="panel-title pull-left"><i class="icon-upload"></i>&nbsp;<?=lang('pay.account_image')?></h4>
                                <a href="#close" class="btn btn-default btn-sm pull-right" onclick="closeUpload()"><span class="glyphicon glyphicon-remove"></span></a>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel panel-body">
                                <form class="form-horizontal" id="form_add" action="<?=site_url('payment_account_management/upload_image/' . Payment_account_management::IMG_TYPE_QRCODE)?>" method="POST" role="form" enctype="multipart/form-data">
                                    <div class="form-group" style="margin-left:0px;margin-right: 0px;">
                                         <div class="col-md-12">
                                                <h4><?=lang('con.aff46');?></h4>
                                                <!-- <input type="text" name="account_image_filepath" class="form-control input-sm" > -->
                                                <input type="hidden" name="account_image_filepath" id="account_image_filepath" class="form-control" readonly>
                                                <input type="hidden" id ="qrcode_account_id" name="payment_account_id"  class="form-control" readonly>
                                                <input type="file" id="qrcodeImage" name="qrcodeImageName" class="form-control input-md" onchange="setQRCodeURL(this.value);" value="<?=set_value('qrcodeImageName');?>" required>
                                                <br/><input type="submit" value="Submit" class="btn btn-sm btn-primary">
                                                <?php echo form_error('account_image_filepath', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                         </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- end upload qr code -->

                        <!-- start upload icon -->
                        <div class="panel panel-primary" id="upload_logo_sec">
                            <div class="panel-heading">
                                <h4 class="panel-title pull-left"><i class="icon-upload"></i>&nbsp;<?=lang('pay.logo_link')?></h4>
                                <a href="#close" class="btn btn-default btn-sm pull-right" onclick="closeUpload()"><span class="glyphicon glyphicon-remove"></span></a>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel panel-body">
                                <form class="form-horizontal" id="form_add" action="<?=site_url('payment_account_management/upload_image/' . Payment_account_management::IMG_TYPE_ICON)?>" method="POST" role="form" enctype="multipart/form-data">
                                    <div class="form-group" style="margin-left:0px;margin-right: 0px;">
                                            <div class="col-md-12">
                                                    <h4><?=lang('con.aff46')?></h4>
                                                    <!-- <input type="text" name="logo_link" class="form-control input-sm" > -->
                                                    <input type="hidden" name="account_icon_filepath" id="account_icon_filepath" class="form-control" readonly>
                                                    <input type="hidden" name="payment_account_id" id="logo_account_id" class="form-control" readonly>
                                                    <input type="file" name="iconImageName" class="form-control input-md" onchange="setIconURL(this.value);" value="<?=set_value('iconImageName');?>" required>
                                                    <br/><input type="submit" value="Submit" class="btn btn-sm btn-primary">
                                                    <?php echo form_error('logo_link', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                            </div>
                                         </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- end upload icon -->
                    </div>
                 </div>
         </div>
     </div>
 </div>
 <div class="panel-footer"></div>
</div>

<script type="text/javascript">

//general
var base_url = "/";
var imgloader = "/resources/images/ajax-loader.gif";

// ----------------------------------------------------------------------------------------------------------------------------- //

// sidebar.php
$(document).ready(function() {
    var url = document.location.pathname;
    var res = url.split("/");
    for (i = 0; i < res.length; i++) {
        switch (res[i]) {
            case 'vipGroupSettingList':
            case 'editVipGroupLevel':
            case 'viewVipGroupRules':
                $("a#view_vipsetting_list").addClass("active");
                break;
            case 'viewPaymentAccountManager':
            case 'view_payment_account':
                $("a#view_payment_settings").addClass("active");
                break;
            default:
                break;
        }
    }
});
// end of sidebar.php

// ----------------------------------------------------------------------------------------------------------------------------- //

//--------DOCUMENT READY---------
//---------------
$(document).ready(function() {
    PaymentAccountManagementProcess.initialize();
    $('#collapseSubmenu').addClass('in');
    $('#view_payment_settings').addClass('active');
    $('#viewPaymentAccount').addClass('active');

});
//player management module
var PaymentAccountManagementProcess = {

    initialize : function() {
      // console.log("initialized now!");

      //validation
      $(".number_only").keydown(function (e) {
        var code = e.keyCode || e.which;
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(code, [46, 8, 9, 27, 13, 110]) !== -1 ||
             // Allow: Ctrl+A
            ( e.ctrlKey === true) || ( e.metaKey === true) ||
             // Allow: home, end, left, right, down, up
            (code >= 35 && code <= 40)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (code < 48 || code > 57)) && (code < 96 || code > 105)) {
            e.preventDefault();
        }
    });

    $(".amount_only").keydown(function (e) {
        var code = e.keyCode || e.which;
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(code, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A
            ( e.ctrlKey === true) || ( e.metaKey === true) ||
             // Allow: home, end, left, right, down, up
            (code >= 35 && code <= 40)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (code < 48 || code > 57)) && (code < 96 || code > 105)) {
            e.preventDefault();
        }
    });

    $(".letters_only").keydown(function (e) {
        var code = e.keyCode || e.which;
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(code, [46, 8, 9, 27, 32, 13, 110, 190]) !== -1 ||
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

    $(".letters_numbers_only").keydown(function (e) {
        var code = e.keyCode || e.which;
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(code, [46, 8, 9, 27, 32, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A
            ( e.ctrlKey === true) || ( e.metaKey === true) ||
             // Allow: home, end, left, right, down, up
            (code >= 35 && code <= 40)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (code < 48 || code > 57)) && (code < 96 || code > 105) && (e.ctrlKey === true || code < 65 || code > 90)) {
            e.preventDefault();
        }
    });

    $(".usernames_only").keydown(function (e) {
        var code = e.keyCode || e.which;
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(code, [46, 8, 9, 27, 13]) !== -1 ||
             // Allow: Ctrl+A
            ( e.ctrlKey === true) || ( e.metaKey === true) ||
             // Allow: home, end, left, right, down, up
            (code >= 35 && code <= 40) ||
             // Allow: underscores
            (e.shiftKey && code == 189)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (code < 48 || code > 57)) && (code < 96 || code > 105) && (e.ctrlKey === true || code < 65 || code > 90)) {
            e.preventDefault();
        }
    });

    $(".emails_only").keydown(function (e) {
        var code = e.keyCode || e.which;
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(code, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A
            ( e.ctrlKey === true) || ( e.metaKey === true) ||
             // Allow: home, end, left, right, down, up
            (code >= 35 && code <= 40) ||
             //Allow: Shift+2
            (e.shiftKey && code == 50)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (code < 48 || code > 57)) && (code < 96 || code > 105) && (e.ctrlKey === true || code < 65 || code > 90)) {
            e.preventDefault();
        }
    });

        //numeric only
        $("#accountNumber").numeric();
        $("#dailyMaxDepositAmount").numeric();

        //tooltip
        $('body').tooltip({
            selector: '[data-toggle="tooltip"]'
        });

        //jquery choosen
        $(".chosen-select").chosen({
            disable_search: true,
        });

        $('input[data-toggle="checkbox"]').click(function() {

            var element = $(this);
            var target  = element.data('target');

            $(target).prop('checked', this.checked).prop('selected', this.checked);
            $(target).parent().trigger('chosen:updated');
            $(target).parent().trigger('change');

        });

        $('[data-untoggle="checkbox"]').on('change', function() {

            var element = $(this);
            var target  = element.data('target');
            if (element.is('select')) {
                $(target).prop('checked', element.children('option').length == element.children('option:selected').length);
            } else {
                $(target).prop('checked', this.checked);
            }

        });

        //for add bank account panel
        var is_addPanelVisible = false;

        //for bank account  edit form
        var is_editPanelVisible = false;

        if(!is_addPanelVisible){
            $('.add_payment_account_sec').hide();
        }else{
            $('.add_payment_account_sec').show();
        }

        if(!is_editPanelVisible){
            $('.edit_payment_account_sec').hide();
        }else{
            $('.edit_payment_account_sec').show();
        }

        //show hide add vip group panel
        $("#add_payment_account").click(function () {
            if(!is_addPanelVisible){
                is_addPanelVisible = true;
                $('.add_payment_account_sec').show();
                $('.edit_payment_account_sec').hide();
                $('#addPaymentAccountGlyhicon').removeClass('glyphicon glyphicon-plus-sign');
                $('#addPaymentAccountGlyhicon').addClass('glyphicon glyphicon-minus-sign');
            }else{
                is_addPanelVisible = false;
                $('.add_payment_account_sec').hide();
                $('#addPaymentAccountGlyhicon').removeClass('glyphicon glyphicon-minus-sign');
                $('#addPaymentAccountGlyhicon').addClass('glyphicon glyphicon-plus-sign');
            }
        });

        //show hide edit vip group panel
        $(".editPaymentAccountBtn").click(function () {
                is_editPanelVisible = true;
                $('.add_payment_account_sec').hide();
                $('.edit_payment_account_sec').show();
        });

        //cancel add vip group
        $(".addpaymentaccount-cancel-btn").click(function () {
                is_addPanelVisible = false;
                $('.add_payment_account_sec').hide();
                $('#addPaymentAccountGlyhicon').removeClass('glyphicon glyphicon-minus-sign');
                $('#addPaymentAccountGlyhicon').addClass('glyphicon glyphicon-plus-sign');

                $('#payment_account_name').val("");
                $('#payment_account_number').val("");
                $('#payment_branch_name').val("");
                $('#daily_max_depsit_amount').val("");
                $('#payment_order').val("");
                $('#total_approved_deposit_count').val("");
                $('#total_deposit_limit').val("");
                $('#notes').val("");
        });

        //cancel add vip group
        $(".editpaymentaccount-cancel-btn").click(function () {
                is_editPanelVisible = false;
                $('.edit_payment_account_sec').hide();


        });
    },

    getPaymentAccountDetails : function(paymentAccountId) {
        is_editPanelVisible = true;
        $('.add_payment_account_sec').hide();
        $('.edit_payment_account_sec').show();
        $.ajax({
            'url' : base_url + 'payment_account_management/get_payment_account_details/' + paymentAccountId,
            'type' : 'GET',
            'dataType' : "json",
            'success' : function(data){
                    // utils.safelog(data);
                     // console.log(data[0]);
                     $('#form_edit input[name=payment_account_id]').val(data.id);
                     $('#form_edit input[name=payment_account_name]').val(data.payment_account_name);
                     $('#form_edit input[name=payment_account_number]').val(data.payment_account_number);
                     $('#form_edit input[name=payment_branch_name]').val(data.payment_branch_name);
                     $('#form_edit input[name=max_deposit_daily]').val(data.max_deposit_daily);
                     $('#form_edit input[name=payment_order]').val(data.payment_order);
                     $('#form_edit select[name=promocms_id]').val(data.promocms_id);
                     $('#form_edit input[name=total_approved_deposit_count]').val(data.total_approved_deposit_count);
                     // $('#form_edit input[name=min_deposit_amount_for_bonus]').val(data.min_deposit_amount_for_bonus);
                     // $('#form_edit input[name=auto_bonus_percentage]').val(data.auto_bonus_percentage);
                     // $('#form_edit input[name=bonus_bet_times]').val(data.bonus_bet_times);
                     $('#form_edit input[name=deposit_fee_percentage]').val(data.deposit_fee_percentage);
                     $('#form_edit input[name=min_deposit_fee]').val(data.min_deposit_fee);
                     $('#form_edit input[name=max_deposit_fee]').val(data.max_deposit_fee);
                     // $('#form_edit input[name=logo_link]').val(data.logo_link);
                     // $('#form_edit input[name=account_image_filepath]').val(data.account_image_filepath);
                     $('#form_edit input[name=total_deposit]').val(data.total_deposit);
                     $('#form_edit input[name=min_deposit_trans]').val(data.min_deposit_trans);
                     $('#form_edit input[name=max_deposit_trans]').val(data.max_deposit_trans);
                     $('#form_edit textarea[name=notes]').val(data.notes);
                     $('#form_edit .external_system_id').html(data.external_system_id);

                     //payment_type_id
                     $('#form_edit select[name=payment_type_id]').val(data.payment_type_id);
                     //flag
                     $('#form_edit select[name=flag]').val(data.flag);
                     //flag
                     $('#form_edit select[name=second_category_flag]').val(data.second_category_flag);
                     //external_system_id
                     $('#form_edit select[name=external_system_id]').val(data.external_system_id);

                     $('.currentPaymentAccountPlayerLevelLimit').html('');

                    // GROUP LEVEL
                    $('#editPlayerLevels option').prop('selected', false);
                    if(data.player_levels){
                        for(var i = 0; i < data.player_levels.length; i++){
                            var id = data.player_levels[i].vipsettingcashbackruleId;
                            $('#editPlayerLevels option[value="'+id+'"]').prop('selected', true);
                        }
                    }
                    $('#editPlayerLevels').trigger('chosen:updated');

                    // AFFILIATE
                    $('#editAffiliates option').prop('selected', false);
                    if(data.affiliates){
                        for(var i = 0; i < data.affiliates.length; i++){
                            var id = data.affiliates[i].affiliateId;
                            $('#editAffiliates option[value="'+id+'"]').prop('selected', true);
                        }
                    }
                    $('#editAffiliates').trigger('chosen:updated');

                    // AGENTS
                    $('#editAgents option').prop('selected', false);
                    if(data.agents){
                        for(var i = 0; i < data.agents.length; i++){
                            var id = data.agents[i].agent_id;
                            $('#editAgents option[value="'+id+'"]').prop('selected', true);
                        }
                    }
                    $('#editAgents').trigger('chosen:updated');

                    // PLAYER
                    $('#editPlayers').empty();
                    if(data.players){
                        for(var i = 0; i < data.players.length; i++){
                            var player = data.players[i];
                            $('#editPlayers').append('<option value="' + player.playerId + '" selected="selected">' + player.username + '</option>');
                        }
                    }
                    $('#editPlayers').trigger('change');

                    }
        },'json');
        return false;
    },
};

$(document).ready(function() {
    var offset = 200;
    var duration = 500;
    jQuery(window).scroll(function() {
        if (jQuery(this).scrollTop() > offset) {
            jQuery('.custom-scroll-top').fadeIn(duration);
        } else {
            jQuery('.custom-scroll-top').fadeOut(duration);
        }
    });

    $('.custom-scroll-top').on('click', function(event) {
        event.preventDefault();
        $('html, body').animate({scrollTop:0}, 'slow');
    });

    //modal Tags
    $("#tags").change(function() {
        $("#tags option:selected").each(function() {
            if ($(this).attr("value") == "Others") {
                $("#specify").show();
            } else {
                $("#specify").hide();
            }
        });
    }).change();
    //end modal Tags

    //end of tool tip
});

function checkAll(id) {
    var list = document.getElementsByClassName(id);
    var all = document.getElementById(id);

    if (all.checked) {
        for (i = 0; i < list.length; i++) {
            list[i].checked = 1;
        }
        $("#delete_form").attr("onsubmit","return confirmDelete();");

    } else {
        all.checked;

        for (i = 0; i < list.length; i++) {
            list[i].checked = 0;
        }

        $("#delete_form").attr("onsubmit","");
    }
}

function uncheckAll(id) {
    var list = document.getElementById(id).className;
    var all = document.getElementById(list);

    var item = document.getElementById(id);
    var allitems = document.getElementsByClassName(list);
    var cnt = 0;

    if (item.checked) {
        for (i = 0; i < allitems.length; i++) {
            if (allitems[i].checked) {
                cnt++;
            }
        }

        if (cnt == allitems.length) {
            all.checked = 1;
        }
    } else {
        // all.checked = 0;
    }
}

function setQRCodeURL(value) {
    var val = value;
    var res = val.split("\\");

    $('#account_image_filepath').val(base_url + 'resources/images/account/' + res);
    // console.log(res[0]);
}

function setIconURL(value) {
    var val = value;
    var res = val.split("\\");

    $('#account_icon_filepath').val(base_url + 'resources/images/account/' + res);
    // console.log(res[0]);
}

    function qrcode_upload(account_id){
        openQrCodeUpload();
        $('#qrcode_account_id').val(account_id);
    }

    function qrcode_remove(account_id,image){

        $('#qrcode_account_id').val(account_id);
        $.ajax({
            url:base_url+'payment_account_management/delete_image',
            data:{'account_id':account_id,'image':image},
            type:'post',
            success:function(data){
                $( "img[name='"+image+"']" ).parent().html('<a href="#" class="btn btn-xs btn-info" onclick="qrcode_upload('+account_id+')">Upload QR Code</a>');
                //<a href="#" class="btn btn-xs btn-info" onclick="qrcode_upload('<?=isset($row->id)?>')">Upload QR Code</a>
            }
        });
    }
    function logo_upload(account_id){
        openLogoUpload();
        $('#logo_account_id').val(account_id);
    }
    function deletePaymentAccount(url){
        if(confirm('<?php echo lang('confirm_payment_account_delete'); ?>')){
            window.location.href=url;
        }
    }

    function activePaymentAccount(url){
        if(confirm('<?php echo lang('Do you want active this account?'); ?>')){
            window.location.href=url;
        }
    }

    function inactivePaymentAccount(url){
        if(confirm('<?php echo lang('Do you want inactive this account?'); ?>')){
            window.location.href=url;
        }
    }

var paymentTypeList=<?php echo json_encode($payment_type_list); ?>;

function changeFlag(sel){
    //change flag
    var flag=$(sel.data('flag'));

    for(var i=0;i<paymentTypeList.length;i++){
        if(paymentTypeList[i].bankTypeId==sel.val()){
            //change
            flag.val(paymentTypeList[i].default_payment_flag);
            break;
        }
    }
}

$(document).ready(function(){
        closeUpload();

        $('#my_table').DataTable({
            "columnDefs": [
//                { className: 'control', orderable: false, targets:0 },
                { orderable: false, targets: [1, 2] },
            ],
            "scrollX": true,
            "order": [ 3, 'asc' ],
            //"dom": '<"top"fl>rt<"bottom"ip>',
            dom: "<'panel-body' <'pull-right'B><'pull-right'f><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'text-center'r><'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            buttons: [
                <?php if( $this->permissions->checkPermissions('export_collection_account')){ ?>
                {
                    text: "<?php echo lang('CSV Export'); ?>",
                    className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>',
                    action: function ( e, dt, node, config ) {
                        var d = {'extra_search':$('#search-form').serializeArray(), 'draw':1, 'length':-1, 'start':0};

                        $.post(site_url('/export_data/export_collection_account'), d, function(data){
                            if(data && data.success){
                                $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
                            }else{
                                alert('export failed');
                            }
                        });
                    }
                }
                <?php } ?>
            ],
            "fnDrawCallback": function(oSettings) {
                $('.btn-action').prependTo($('.top'));
            }
        });

        $("#edit_column").tooltip({
            placement: "left",
            title: "<?php echo lang('sys.vu57'); ?>",
        });

        // $("#show_advance_search").tooltip({
        //     placement: "left",
        //     title: "<?php echo lang('advance.search'); ?>",
        // });


    $("#add_payment_account").tooltip({
        placement: "left",
        title: "<?php echo lang('pay.add_payment_account'); ?>",
    });

    $("#payment_type_id_for_edit , #payment_type_id_for_add").change(function(){
        var sel=$(this);
        changeFlag(sel);
    });

});

    $(document).ready(function() {

        $(".js-data-example-ajax").select2({
            placeholder: '<?=lang('Select new applicable players')?>',
            ajax: {
                url: '/payment_account_management/players',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term,
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data.items,
                    };
                },
                cache: true
            },
            templateResult: function (option) {
                return option.text;
            },
            templateSelection: function (option) {
                return option.text;
            },
            minimumInputLength: 3,
        });

        var totalAndMaxReady =false,
        dailyMaxDepAmt = $("#form_add #daily_max_depsit_amount"),
        totalDepLimit = $("#form_add #total_deposit_limit"),
        dailyMaxDepAmt2 = $("#form_edit #daily_max_depsit_amount"),
        totalDepLimit2 = $("#form_edit #total_deposit_limit"),
        editRow = $("#edit-row");

        var LANG = {
           DAILY_MAX_DEP_MSG : "<?php echo lang('pay.daily_max_dep_msg'); ?>",
           TOTAL_DEP_MSG : "<?php echo lang('pay.total_dep_msg'); ?>",
           TOTAL_DEP_NOT_ZERO_MSG :"<?php echo lang('pay.total_dep_not_zero_msg'); ?>"
        };

        //For Add form
        dailyMaxDepAmt.blur(function(){
            checkTotalAndDailyDeposit();
        });

        totalDepLimit.blur(function(){
            checkTotalAndDailyDeposit();
        });


        totalDepLimit.focus(function(){
            $("#form_add .tdl-help-block").css({display:"none"}).html("");
        });
         dailyMaxDepAmt.focus(function(){
            $("#form_add .dmda-help-block").css({display:"none"}).html("");
        });



        // For Edit Form
        editRow.click(function(){
            totalAndMaxReady =false;
        });

        dailyMaxDepAmt2.blur(function(){
            checkTotalAndDailyDeposit2();
        });

        totalDepLimit2.blur(function(){
            checkTotalAndDailyDeposit2();
        });


        totalDepLimit2.focus(function(){
            $("#form_edit .tdl-help-block").css({display:"none"}).html("");
        });
         dailyMaxDepAmt2.focus(function(){
            $("#form_edit .dmda-help-block").css({display:"none"}).html("");
        });



        function checkTotalAndDailyDeposit(){

           var dmda = Number(dailyMaxDepAmt.val()),
           tdl = Number(totalDepLimit.val());
           if(tdl == 0){
            totalAndMaxReady =false;
                $("#form_add .tdl-help-block").css({display:"block",color:"#FE667C"}).html(LANG.TOTAL_DEP_NOT_ZERO_MSG);
            }
           if(dmda && tdl){
            //Daily Max Deposit Amount should be > 0
             if(dmda==0){
                totalAndMaxReady =false;
               $("#form_add .dmda-help-block").css({display:"block",color:"#FE667C"}).html(LANG.DAILY_MAX_DEP_MSG);
            //Total Deposit Limit should be >= Daily Max Deposit Amount
             }else if(tdl < dmda  ){
                totalAndMaxReady =false;
                 $("#form_add .tdl-help-block").css({display:"block",color:"#FE667C"}).html(LANG.TOTAL_DEP_MSG);
             }else{
                totalAndMaxReady =true;
                $("#form_add .tdl-help-block, .dmda-help-block").css({display:"none"}).html("");
             }
           }

       }


        function checkTotalAndDailyDeposit2(){

           var dmda = Number(dailyMaxDepAmt2.val()),
           tdl = Number(totalDepLimit2.val());

           if(tdl == 0){
            totalAndMaxReady =false;
                $("#form_edit .tdl-help-block").css({display:"block",color:"#FE667C"}).html(LANG.TOTAL_DEP_NOT_ZERO_MSG);
            }
           if(dmda && tdl){
            //Daily Max Deposit Amount should be > 0
             if(dmda==0){
                totalAndMaxReady =false;
               $("#form_edit .dmda-help-block").css({display:"block",color:"#FE667C"}).html(LANG.DAILY_MAX_DEP_MSG);
            //Total Deposit Limit should be >= Daily Max Deposit Amount
             }else if(tdl< dmda ){
                totalAndMaxReady =false;
                 $("#form_edit .tdl-help-block").css({display:"block",color:"#FE667C"}).html(LANG.TOTAL_DEP_MSG);
             }else{
                totalAndMaxReady =true;
                $("#form_edit .tdl-help-block, .dmda-help-block").css({display:"none"}).html("");
             }
           }
       }



       $('#form_add').submit( function (e) {

        checkTotalAndDailyDeposit();

        var element = $('#form_add .playerLevels');
        if (element.val() == '' || element.val() == null) {
            element.closest('.form-group').addClass('has-error');
            $('#form_add .playerLevels-help-block').text('<?=sprintf(lang("gen.error.required"), lang("pay.playerlev"))?>');
            $('#addPlayerLevels_chosen .chosen-choices').css('border-color', '#a94442');
            return false;
        } else if(totalAndMaxReady ==false) {

             return false;
        }else{
            element.closest('.form-group').removeClass('has-error');
            $('#form_add .playerLevels-help-block').html('<i style="font-size:12px;color:#919191;"><?=lang('pay.applevbankacct');?></i>');
            $('#addPlayerLevels_chosen .chosen-choices').css('border-color', '');
            return true;
        }


    });

       $("#form_add .playerLevels").change( function() {
        var element = $('#form_add .playerLevels');
        if (element.val() == '' || element.val() == null) {
            element.closest('.form-group').addClass('has-error');
            $('#form_add .playerLevels-help-block').text('<?=sprintf(lang("gen.error.required"), lang("pay.playerlev"))?>');
            $('#addPlayerLevels_chosen .chosen-choices').css('border-color', '#a94442');
        } else {
            element.closest('.form-group').removeClass('has-error');
            $('#form_add .playerLevels-help-block').html('<i style="font-size:12px;color:#919191;"><?=lang('pay.applevbankacct');?></i>');
            $('#addPlayerLevels_chosen .chosen-choices').css('border-color', '');
        }
    });

       $('#form_edit').submit( function (e) {
         checkTotalAndDailyDeposit2();
        var element = $('#form_edit .playerLevels');
        if (element.val() == '' || element.val() == null) {
            element.closest('.form-group').addClass('has-error');
            $('#form_edit .playerLevels-help-block').text('<?=sprintf(lang("gen.error.required"), lang("pay.playerlev"))?>');
            $('#editPlayerLevels_chosen .chosen-choices').css('border-color', '#a94442');
            return false;
         } else if(totalAndMaxReady ==false) {

             return false;

         }else{
            element.closest('.form-group').removeClass('has-error');
            $('#form_edit .playerLevels-help-block').html('<i style="font-size:12px;color:#919191;"><?=lang('pay.applevbankacct');?></i>');
            $('#editPlayerLevels_chosen .chosen-choices').css('border-color', '');
            return true;
        }


    });

       $("#form_edit .playerLevels").change( function() {
        var element = $('#form_edit .playerLevels');
        if (element.val() == '' || element.val() == null) {
            element.closest('.form-group').addClass('has-error');
            $('#form_edit .playerLevels-help-block').text('<?=sprintf(lang("gen.error.required"), lang("pay.playerlev"))?>');
            $('#editPlayerLevels_chosen .chosen-choices').css('border-color', '#a94442');
        } else {
            element.closest('.form-group').removeClass('has-error');
            $('#form_edit .playerLevels-help-block').html('<i style="font-size:12px;color:#919191;"><?=lang('pay.applevbankacct');?></i>');
            $('#editPlayerLevels_chosen .chosen-choices').css('border-color', '');
        }
    });


   });

    function openQrCodeUpload() {
        $("#upload_sec").show();
        $("#upload_qrcode_sec").show();
        $("#upload_logo_sec").hide();
    }

    function closeUpload() {
        $("#upload_sec").hide();
    }

    function openLogoUpload() {
        $("#upload_sec").show();
        $("#upload_logo_sec").show();
        $("#upload_qrcode_sec").hide();
    }

   function drag_start(event) {
            var style = window.getComputedStyle(event.target, null);
            event.dataTransfer.setData("text/plain",
            (parseInt(style.getPropertyValue("left"),10) - event.clientX) + ',' + (parseInt(style.getPropertyValue("top"),10) - event.clientY));
    }

    function drag_over(event) {
        event.preventDefault();
        return false;
    }

    function drop(event) {
        var offset = event.dataTransfer.getData("text/plain").split(',');
        var dm = document.getElementById('upload_sec');
        dm.style.left = (event.clientX + parseInt(offset[0],10)) + 'px';
        dm.style.top = (event.clientY + parseInt(offset[1],10)) + 'px';
        event.preventDefault();
        return false;
    }

    var dm = document.getElementById('upload_sec');
        dm.addEventListener('dragstart',drag_start,false);

    document.body.addEventListener('dragover',drag_over,false);
    document.body.addEventListener('drop',drop,false);

    $("#delete_form").submit(function(){
        var checked = $(".checkWhite:checked").length > 0;
        var deleteCheckboxWarningMsg = "<?php echo lang('Please check at least one item'); ?>";
        if (!checked){
            alert(deleteCheckboxWarningMsg);
            return false;
        }else{
            // return confirmDelete();
        }
    });

    function deleteThisAccount(url){
        if(confirm('<?php echo lang('confirm_payment_account_delete'); ?>')){
            window.location.href=url;
        }
    }

    <?php if(!$this->utils->isEnabledFeature('allow_special_characters_on_account_number')) { ?>
    $('.txt_account_number').keypress(function(e){
        var txt = String.fromCharCode(e.which);
        if(!txt.match(/^[0-9*\b]/))
        {
            return false;
        }
    });
    <?php } ?>
</script>
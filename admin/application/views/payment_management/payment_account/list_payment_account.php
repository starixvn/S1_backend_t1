<div class="panel panel-primary">

    <div class="panel-heading">

        <h1 class="panel-title">
            <i class="icon-coin-dollar"></i>
            <?=lang('Collection Accounts')?>
        </h1>

        <div class="pull-right">

        </div>

    </div>

    <div class="panel-body">

        <div class="panel panel-default" style="margin-bottom: 0;">

            <div class="panel-heading text-center">

                <h1 class="panel-title">
                    <?=lang('Online Payment')?>
                </h1>

            </div> <!-- END PANEL-HEADING -->

            <div class="table-responsive">

                 <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center"><?=lang('Actions');?></th>
                            <th><?=lang('Bank Name');?></th>
                            <th><?=lang('Bank Account Name');?></th>
                            <th><?=lang('Bank Account Number');?></th>
                            <th><?=lang('Bank Account Branch');?></th>
                            <th class="text-right"><?=lang('Groups');?></th>
                            <th class="text-right"><?=lang('Affiliates');?></th>
                            <th class="text-right"><?=lang('Players');?></th>
                            <th class="text-right"><?=lang('Daily Deposit Limit');?></th>
                            <th class="text-right"><?=lang('Total Deposit Limit');?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($banks as $bank): ?>
                            <tr>
                                <td class="text-center">
                                    <ul class="list-inline" style="margin-bottom: 0;">
                                        <li>
                                            <a href="/payment_account_management/view_payment_account/<?=$bank->id?>"><i class="glyphicon glyphicon-new-window"></i></a>
                                        </li>
                                        <li>
                                            <a href="/payment_account_management/edit_payment_account"><i class="glyphicon glyphicon-edit"></i></a>
                                        </li>
                                        <li>
                                            <a href="/payment_account_management/delete_payment_account"><i class="glyphicon glyphicon-trash"></i></a>
                                        </li>
                                    </ul>
                                </td>
                                <td><?=$bank->payment_type ? lang($bank->payment_type) : '<i class="text-muted">' . lang("lang.norecyet") . '<i/>'?></td>
                                <td><?=$bank->payment_account_name ? : '<i class="text-muted">' . lang("lang.norecyet") . '<i/>'?></td>
                                <td><?=$bank->payment_account_number ? : '<i class="text-muted">' . lang("lang.norecyet") . '<i/>'?></td>
                                <td><?=$bank->payment_branch_name ? : '<i class="text-muted">' . lang("lang.norecyet") . '<i/>'?></td>
                                <td align="right">0</td>
                                <td align="right">0</td>
                                <td align="right">0</td>
                                <td align="right"><?=number_format($bank->max_deposit_daily, 2)?></td>
                                <td align="right"><?=number_format($bank->total_deposit, 2)?></td>
                            </tr>
                        <?php endforeach ?>
                     </tbody>
                 </table>

            </div>

            <div class="panel-footer"></div>

        </div>

    </div>
    <div class="panel-footer"></div>
</div>






<?php /*









                                <div class="btn-action">
                                    <button type="submit" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="<?=lang('cms.deletesel');?>">
                                        <i class="glyphicon glyphicon-trash" style="color:white;"></i> <?=lang('cms.deletesel');?>
                                    </button>&nbsp;
                                    <?php if ($export_report_permission) {?>
                                    <a href="<?=site_url('payment_account_management/export_to_excel')?>" class="btn btn-sm btn-success btn-sm" data-toggle="tooltip" title="<?=lang('lang.export');?>" data-placement="top">
                                        <i class="glyphicon glyphicon-share"></i> <?=lang('lang.export');?>
                                    </a>
                                    <?php }
?>
                                </div>


    <div class="panel-body" id="details_panel_body">


        <!-- edit payment account -->
        <div class="row edit_payment_account_sec" id="edit_payment_account_sec">
            <div class="col-md-12">
                <div class="well" style="overflow:auto">
                    <form class="form-horizontal" id="form_edit" action="<?=site_url('payment_account_management/add_edit_payment_account')?>" method="POST" role="form">
                        <input type="hidden" name="payment_account_id" >
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="control-label" for="" style="font-size:11px;"><?=lang('pay.payment_name');?>:* </label>
                                    <p>
                                        <?php //echo form_dropdown('payment_type_id', $payment_types, '', ' required ');?>
                                        <select id="payment_type_id_for_edit" data-flag='#flag_for_edit' name="payment_type_id" class="form-control input-sm" required>
                                            <?php foreach ($payment_types as $key => $value) {?>
                                                <option value="<?=$key?>"><?=$value?></option>
                                            <?php }
?>
                                        </select>
                                        <?= form_error('payment_type_id', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                    </p>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label" for="" style="font-size:11px;"><?=lang('pay.payment_account_flag');?>:* </label>
                                    <p>
                                        <?php //echo form_dropdown('flag', $payment_account_flags, '', ' required ');?>
                                        <select id='#flag_for_edit' name="flag" class="form-control input-sm" required>
                                            <?php foreach ($payment_account_flags as $key => $value) {?>
                                                <option value="<?=$key?>"><?=$value?></option>
                                            <?php }
?>
                                        </select>
                                        <?= form_error('flag', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
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
                                    <?= form_error('payment_account_name', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label" for="" style="font-size:11px;"><?=lang('pay.payment_account_number');?>: </label>
                                    <input type="number"  data-number-nogrouping="true" name="payment_account_number" class="form-control input-sm" >
                                    <?= form_error('payment_account_number', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label" for="" style="font-size:11px;"><?=lang('pay.payment_branch_name');?>: </label>
                                    <input type="text"  name="payment_branch_name" class="form-control input-sm">
                                    <?= form_error('payment_branch_name', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label" for="" style="font-size:11px;"><?=lang('pay.daily_max_depsit_amount');?>:* </label>
                                    <input type="number" required value=""  id="daily_max_depsit_amount"  maxlength="12" name="max_deposit_daily" class="form-control input-sm">
                                     <p class="help-block dmda-help-block pull-left">
                                        <?= form_error('max_deposit_daily', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="control-label" for="" style="font-size:11px;"><?=lang('pay.account_image');?>: </label>
                                    <!-- <input type="text" name="account_image_filepath" class="form-control input-sm" > -->
                                    <!-- <input type="hidden" name="account_image_filepath" id="account_image_filepath" class="form-control" readonly>
                                    <input type="file" name="userfile" id="userfile" class="form-control" onchange="setURL(this.value);" value="<?=set_value('userfile');?>" required> -->
                                    <?= form_error('account_image_filepath', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label" for="" style="font-size:11px;"><?=lang('pay.payment_order');?>: </label>
                                    <input type="number" name="payment_order" class="form-control input-sm" >
                                    <?= form_error('payment_order', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label" for="" style="font-size:11px;"><?=lang('pay.logo_link');?>: </label>
                                    <input type="text" name="logo_link" class="form-control input-sm" >
                                    <?= form_error('logo_link', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label" for="" style="font-size:11px;"><?=lang('pay.total_deposit_limit');?>: </label>
                                    <input type="number" required value=""  id="total_deposit_limit" maxlength="12" name="total_deposit" class="form-control input-sm" >
                                     <p class="help-block tdl-help-block pull-left">
                                        <?= form_error('total_deposit', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                                    </p>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="control-label" class="control-label" style="font-size:12px;"><?=lang('pay.playerlev');?>:*</label>
                                    <?= form_multiselect('playerLevels[]', $levels, $form['playerLevels'], ' class="form-control input-sm chosen-select playerLevels" id="editPlayerLevels" data-placeholder="' . lang("cms.selectnewlevel") . '" data-untoggle="checkbox" data-target="#form_edit .toggle-checkbox" ') ?>
                                    <p class="help-block playerLevels-help-block pull-left"><i style="font-size:12px;color:#919191;"><?=lang('pay.applevbankacct');?></i></p>
                                    <div class="checkbox pull-right" style="margin-top: 5px">
                                        <label><input type="checkbox" class="toggle-checkbox" data-toggle="checkbox" data-target="#form_edit .playerLevels option"> <?=lang('lang.selectall');?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="control-label" for=""><?=lang('player.upay05');?>: </label>
                                    <textarea name="notes" class="form-control input-sm" cols="10" rows="5" style="height: auto;"></textarea>
                                    <?= form_error('notes', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
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
                <form action="<?=site_url('payment_account_management/delete_selected_payment_account')?>" method="POST" role="form" onsubmit="return confirmDelete();">
                    <div id="payment_account_table">

                        <div class="table-responsive">

                     </div>
            </form>

                     <div class="col-md-4" id="upload_sec" draggable="true" style="position: absolute;left: 0;top: 0;">
                        <!-- start upload qr code -->
                        <div class="panel panel-primary
              " id="upload_qrcode_sec">
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
                                                <?= form_error('account_image_filepath', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
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
                                                    <?= form_error('logo_link', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
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
            (code == 65 && e.ctrlKey === true) ||
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
            (code == 65 && e.ctrlKey === true) ||
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
            (code == 65 && e.ctrlKey === true) ||
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
            (code == 65 && e.ctrlKey === true) ||
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
            (code == 65 && e.ctrlKey === true) ||
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
            (code == 65 && e.ctrlKey === true) ||
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
                     $('#form_edit input[name=logo_link]').val(data.logo_link);
                     $('#form_edit input[name=account_image_filepath]').val(data.account_image_filepath);
                     $('#form_edit input[name=total_deposit]').val(data.total_deposit);
                     $('#form_edit input[name=notes]').val(data.notes);
                     $('#form_edit .external_system_id').html(data.external_system_id);

                     //payment_type_id
                     $('#form_edit select[name=payment_type_id]').val(data.payment_type_id);
                     //flag
                     $('#form_edit select[name=flag]').val(data.flag);
                     //external_system_id
                     $('#form_edit select[name=external_system_id]').val(data.external_system_id);

                     $('.currentPaymentAccountPlayerLevelLimit').html('');
                     if(data.player_levels){
                        $('#editPlayerLevels option').prop('selected', false);
                         for(var i = 0; i < data.player_levels.length; i++){
                            var id = data.player_levels[i].vipsettingcashbackruleId;
                            $('#editPlayerLevels option[value="'+id+'"]').prop('selected', true);
                         }
                        $('#editPlayerLevels').trigger('chosen:updated');
                     }

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
    } else {
        all.checked;

        for (i = 0; i < list.length; i++) {
            list[i].checked = 0;
        }
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
        all.checked = 0;
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
                //<a href="#" class="btn btn-xs btn-info" onclick="qrcode_upload('<?=$row->id?>')">Upload QR Code</a>
            }
        });
    }
    function logo_upload(account_id){
        openLogoUpload();
        $('#logo_account_id').val(account_id);
    }
    function deletePaymentAccount(url){
        if(confirm('<?= lang('confirm_payment_account_delete'); ?>')){
            window.location.href=url;
        }
    }

var paymentTypeList=<?= json_encode($payment_type_list); ?>;

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

        var payment_account_order=3;
        $('#my_table').DataTable({
            "responsive": {
                details: {
                    type: 'column'
                }
            },
            "columnDefs": [ {
                className: 'control',
                orderable: false,
                targets:   0
            } ],
            "order": [ payment_account_order, 'asc' ],
            //"dom": '<"top"fl>rt<"bottom"ip>',
            dom: "<'panel-body'<'pull-right'f><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            "fnDrawCallback": function(oSettings) {
                $('.btn-action').prependTo($('.top'));
            }
        });

        $("#edit_column").tooltip({
            placement: "left",
            title: "<?= lang('sys.vu57'); ?>",
        });

        // $("#show_advance_search").tooltip({
        //     placement: "left",
        //     title: "<?= lang('advance.search'); ?>",
        // });


    $("#add_payment_account").tooltip({
        placement: "left",
        title: "<?= lang('pay.add_payment_account'); ?>",
    });

    $("#payment_type_id_for_edit , #payment_type_id_for_add").change(function(){
        var sel=$(this);
        changeFlag(sel);
    });

});

    $(document).ready(function() {

        var totalAndMaxReady =false,
        dailyMaxDepAmt = $("#form_add #daily_max_depsit_amount"),
        totalDepLimit = $("#form_add #total_deposit_limit"),
        dailyMaxDepAmt2 = $("#form_edit #daily_max_depsit_amount"),
        totalDepLimit2 = $("#form_edit #total_deposit_limit"),
        editRow = $("#edit-row");

        var LANG = {
           DAILY_MAX_DEP_MSG : "<?= lang('pay.daily_max_dep_msg'); ?>",
           TOTAL_DEP_MSG : "<?= lang('pay.total_dep_msg'); ?>",
           TOTAL_DEP_NOT_ZERO_MSG :"<?= lang('pay.total_dep_not_zero_msg'); ?>"
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



</script>

*/?>
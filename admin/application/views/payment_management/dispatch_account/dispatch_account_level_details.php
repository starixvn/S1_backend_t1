<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="icon-diamond"></i> <?=lang('dispatch_account_level.edit_level');?>
            <div class="pull-right">
                <a href="<?=site_url('dispatch_account_management/getDispatchAccountLevelList/'.$data['group_id'])?>" class="btn btn-default btn-xs" id="add_news">
                    <span class="glyphicon glyphicon-remove"></span>
                </a>
            </div>
        </h4>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body" id="details_panel_body">
        <!-- edit dispatch group -->
        <div class="row add_dispatch_group_sec">
            <div class="col-md-12">
                <div class="well" style="overflow: auto">
                    <form id="add_dispatch_group_form" class="form-horizontal" method="post" role="form" class="form-inline" enctype="multipart/form-data">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-4 i_required">
                                    <input type="hidden" id="group_id" name="group_id" class="form-control input-sm" value="<?=$data['group_id']?>">
                                    <input type="hidden" id="level_id" name="level_id" class="form-control input-sm" value="<?=$data['id']?>">
                                    <label class="control-label" for="level_name"><?=lang('dispatch_account_level.level_name');?>: <span class="hint">*</span></label>
                                    <input type="text" id="level_name" name="level_name" class="form-control input-sm" value="<?=$data['level_name']?>" required>
                                </div>
                                <?php if($data['level_order'] > 0): ?>
                                <div class="col-md-4 i_required">
                                <label class="control-label" for="level_member_limit"><?=lang('dispatch_account_level.level_member_limit');?>: <span class="hint">*</span></label>
                                <input type="number" id="level_member_limit" name="level_member_limit" class="form-control input-sm" min="<?=$min_member_limit?>" value="<?=$data['level_member_limit']?>">
                                </div>
                                <?php endif; ?>
                                <div class="col-md-2 i_required">
                                <label class="control-label" for="level_observation_period"><?=lang('dispatch_account_level.set_observation_period');?></label>
                                <input type="number" id="level_observation_period" name="level_observation_period" class="input-sm form-control" min="0" value="<?=$data['level_observation_period']?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12  i_required">
                                    <label class="control-label" class="control-label" style="font-size:12px;"><?=lang('dispatch_account_level.payment_account');?>:</label>
                                    <?php echo form_multiselect('paymentAccounts[]', is_array($payment_account_list) ? $payment_account_list : array(), $form['paymentAccounts'], ' class="form-control input-sm chosen-select paymentAccounts" id="editPaymentAccounts" data-placeholder="' . lang("cms.selectnewlevel") . '" data-untoggle="checkbox" data-target="#form_edit .toggle-checkbox .paymentAccounts" ') ?>
                                    <p class="help-block paymentAccounts-help-block pull-left">
                                        <i style="font-size:12px;color:#919191;"><?=lang('dispatch_account_level.applicable_payment_accounts');?></i>
                                    </p>
                                    <div class="checkbox pull-right" style="margin-top: 5px">
                                        <label><input id="select_all_checkbox" type="checkbox" class="toggle-checkbox" data-toggle="checkbox" data-target="#form_edit .paymentAccounts option"> <?=lang('Select All');?></label>
                                    </div>
                                </div>
                            </div>
                            <?php if($data['level_order'] > 0): ?>
                            <fieldset id="depositPaymentType" style="padding-bottom: 20px">
                                <legend style="padding-bottom: 8px">
                                    <label class="control-label"><?=lang('dispatch_account_batch.condition_setting');?></label>
                                </legend>
                                    <label class="control-label" style="color: #ea2f10;"><?=lang('dispatch_account_batch.condition_setting.message');?></label>
                                <div class="row">
                                    <div class="col-md-4 i_required">
                                        <label class="control-label" for="level_total_deposit"><?=lang('dispatch_account_level.level_total_deposit');?>: </label>
                                        <input type="number" id="level_total_deposit" name="level_total_deposit" class="form-control input-sm" min="0" value="<?=$data['level_total_deposit']?>">
                                    </div>
                                    <div class="col-md-4 i_required">
                                        <label class="control-label" for="level_total_withdraw"><?=lang('dispatch_account_level.level_total_withdraw');?>: </label>
                                        <input type="number" id="level_total_withdraw" name="level_total_withdraw" class="form-control input-sm" min="0" value="<?=$data['level_total_withdraw']?>">
                                    </div>
                                    <div class="col-md-4 i_required">
                                        <label class="control-label" for="level_single_max_deposit"><?=lang('dispatch_account_level.level_single_max_deposit');?>: </label>
                                        <input type="number" id="level_single_max_deposit" name="level_single_max_deposit" class="form-control input-sm" min="0" value="<?=$data['level_single_max_deposit']?>">
                                    </div>
                                    <div class="col-md-4 i_required">
                                        <label class="control-label" for="level_deposit_count"><?=lang('dispatch_account_level.level_deposit_count');?>: </label>
                                        <input type="number" id="level_deposit_count" name="level_deposit_count" class="form-control input-sm" min="0" value="<?=$data['level_deposit_count']?>">
                                    </div>
                                    <div class="col-md-4 i_required">
                                        <label class="control-label" for="level_withdraw_count"><?=lang('dispatch_account_level.level_withdraw_count');?>: </label>
                                        <input type="number" id="level_withdraw_count" name="level_withdraw_count" class="form-control input-sm" min="0" value="<?=$data['level_withdraw_count']?>">
                                    </div>
                                </div>
                            </fieldset>
                            <?php endif; ?>



                            <hr>
                            <div style="text-align:center;">
                                <button class="btn btn-sm check_edit_dispatchAccountLevel <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-info'?>"><?=lang('player.saveset');?></button>
                                <a href="<?=site_url('dispatch_account_management/getDispatchAccountLevelList/' . $data['group_id'])?>" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default'?>"><?=lang('lang.cancel');?></a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!--CONFIRM INFO MODAL START-->
<div class="modal fade bs-example-modal-md confirm_info_modal" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header panel-heading confirm_info_header">
                <h3 class="confirm_info_title"><?=lang('dispatch_account_level.edit_level')?></h3>
            </div>
            <div class="modal-body confirm_info_body">
                <div class="row">
                    <div class="col-md-12">
                        <label class="control-label confirm_info_descriotion"></label>
                    </div>
                </div>
            </div>
            <div class="modal-footer confirm_info_footer">
                <button type="button" class="btn btn-primary confirm_info_submit btn-submit"><?=lang('lang.yes')?></button>
                <button type="button" class="btn btn-default confirm_info_cancel btn-cancel" data-dismiss="modal"></button>
            </div>
        </div>
    </div>
</div>
<!--CONFIRM INFO MODAL END-->

<script type="text/javascript">


//--------DOCUMENT READY---------
//---------------
$(document).ready(function() {
    $('#form_edit').submit( function (e) {
        var element = $('#form_edit .paymentAccounts');
        if (element.val() == '' || element.val() == null) {
            element.closest('.form-group').addClass('has-error');
            $('#form_edit .paymentAccounts-help-block').text('<?=sprintf(lang("gen.error.required"), lang("pay.playerlev"))?>');
            $('#editPlayerLevels_chosen .chosen-choices').css('border-color', '#a94442');
            return false;
        } else if(totalAndMaxReady == false) {
            return false;

        } else {
            element.closest('.form-group').removeClass('has-error');
            $('#form_edit .paymentAccounts-help-block').html('<i style="font-size:12px;color:#919191;"><?=lang('dispatch_account_level.applicable_payment_accounts');?></i>');
            $('#editPlayerLevels_chosen .chosen-choices').css('border-color', '');
            return true;
        }
    });


   $("#form_edit .paymentAccounts").change( function() {
        var element = $('#form_edit .paymentAccounts');
        if (element.val() == '' || element.val() == null) {
            element.closest('.form-group').addClass('has-error');
            $('#form_edit .paymentAccounts-help-block').text('<?=sprintf(lang("gen.error.required"), lang("pay.playerlev"))?>');
            $('#editPaymentAccounts_chosen .chosen-choices').css('border-color', '#a94442');
        } else {
            element.closest('.form-group').removeClass('has-error');
            $('#form_edit .paymentAccounts-help-block').html('<i style="font-size:12px;color:#919191;"><?=lang('dispatch_account_level.applicable_payment_accounts');?></i>');
            $('#editPaymentAccounts_chosen .chosen-choices').css('border-color', '');
        }
    });

    putPaymentAccountsToChosenContainer();
    DispatchAccountManagementProcess.initializeLevelDetail();


    var confirm_info_modal = $('.confirm_info_modal');
    var hasLevel = <?=(isset($data['level_order']) && ($data['level_order'] > 0)) ? 1 : 0 ;?>;

    function validRequiredFields(){
        var valid = false;
        var levelName = $('#level_name').val();
        if(hasLevel){
            var memberLimit = $('#level_member_limit').val();
        }else{
            var memberLimit = true;
        }

        if(levelName && memberLimit){
            valid = true;
        }

        return valid;
    }

    function updateConfirmModal(){
        $('.confirm_info_submit').hide();
        $('.confirm_info_cancel').html('<?=lang('lang.close')?>');
        $('.confirm_info_descriotion').html('<?=lang('dispatch_account_level.please_confirm_all_settings')?>');
    }

    function submitConfirmModal(){
        //submit
        $("#add_dispatch_group_form").attr('action', '<?php echo site_url('dispatch_account_management/editDispatchAccountLevel') ?>');
        $("#add_dispatch_group_form").submit();
    }

    function validWithPaymentAccount(){
        var valid = validRequiredFields();
        if(!valid){
            updateConfirmModal();
            confirm_info_modal.modal('show');
            return false;
        }

        submitConfirmModal();
    }

    $('.btn-submit', confirm_info_modal).click(function(){
        var valid = validRequiredFields();
        if(!valid){
            updateConfirmModal();
            return false;
        }

        submitConfirmModal();
    });

    $('.check_edit_dispatchAccountLevel').click(function(){
        if($('#level_observation_period').val() == ''){
            $('#level_observation_period').val('0');
        }

        if(hasLevel){
            if($('#level_single_max_deposit').val() == ''){
                $('#level_single_max_deposit').val('0');
            }
            if($('#level_total_deposit').val() == ''){
                $('#level_total_deposit').val('0');
            }
            if($('#level_deposit_count').val() == ''){
                $('#level_deposit_count').val('0');
            }
            if($('#level_total_withdraw').val() == ''){
                $('#level_total_withdraw').val('0');
            }
            if($('#level_withdraw_count').val() == ''){
                $('#level_withdraw_count').val('0');
            }
        }

        var paymentAccounts = $('#editPaymentAccounts').val();
        if(paymentAccounts){
            validWithPaymentAccount();
        }else{
            $('.confirm_info_submit').show();
            $('.confirm_info_cancel').html('<?=lang('lang.no')?>');
            $('.confirm_info_descriotion').html('<?=lang('dispatch_account_level.save_without_payment_account')?>');
            confirm_info_modal.modal('show');
        }

        return false;
    });


});


function putPaymentAccountsToChosenContainer() {
    var correspond_payment_accounts = JSON.parse('<?=json_encode($correspond_payment_accounts);?>');
    if(correspond_payment_accounts.length > 0) {
        // payment accounts
        $('#editPaymentAccounts option').prop('selected', false);
            for(var i = 0; i < correspond_payment_accounts.length; i++){
                $('#editPaymentAccounts option[value="'+correspond_payment_accounts[i].payment_account_id+'"]').prop('selected', true);
            }
        $('#editPaymentAccounts').trigger('chosen:updated');
    }
}

var DispatchAccountManagementProcess = {
    initializeLevelDetail : function() {
        $(".chosen-select").chosen({
            disable_search: true,
        });

        $('input[data-toggle="checkbox"]').click(function() {
            var is_select_all = $('#select_all_checkbox').is(":checked");
            var all_payment_accounts = JSON.parse('<?=json_encode($payment_account_list);?>');

            if(!jQuery.isEmptyObject(all_payment_accounts) && is_select_all) {
                $('#editPaymentAccounts option').prop('selected', false);
                for(var payment_account_id in all_payment_accounts){
                    $('#editPaymentAccounts option[value="'+payment_account_id+'"]').prop('selected', true);
                }
            }
            else {
                $('#editPaymentAccounts option').prop('selected', true);
                for(var payment_account_id in all_payment_accounts){
                    $('#editPaymentAccounts option[value="'+payment_account_id+'"]').prop('selected', false);
                }
            }
            $('#editPaymentAccounts').trigger('chosen:updated');
        });
    }
};
</script>
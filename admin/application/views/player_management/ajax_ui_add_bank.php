<form method="POST" id="form-root" action="/player_management/verifyAddPlayerBankInfo">
    <?=$double_submit_hidden_field?>
    <div class="row" id="bank_form">
        <div class="col-md-12" id="toggleView">
            <div class="panel-primary">
                <div class="panel-body" id="bank_panel_body">
                    <input type="hidden" name="player_id" value="<?=$player_id;?>"/>
                    <input type="hidden" name="dw_bank" value="<?=$dw_bank;?>"/>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="bank_type_id"><i style="color:red">*</i> <?=lang('player.ui35');?>:</label>
                            <select id="bank_type_id" name="bank_type_id" class="form-control">
                                <option value=""><?=lang('player.ui59');?></option>
                                <?php foreach ($bank_types as $key => $value) {?>
                                    <option value="<?=$value?>" <?=(set_value('bank_type_id') == $value) ? 'selected' : ''?> ><?=$key?></option>
                                <?php } ?>
                                </select>
                            <span id="bank_type_status" style="font-size:12px; color:#ff0000; font-style: italic;"></span>
                            <span style="color:red"><?=form_error('bank_type_id');?></span>
                        </div>
                        <div class="form-group">
                            <label for="bank_account_number"><i style="color:red">*</i> <?=lang('player.ui37');?>:</label>
                            <input type="text" id='bank_account_number' class="form-control number_only" name="bank_account_number" value="<?=(set_value('bank_account_number') == null) ? '' : set_value('bank_account_number')?>"/>
                            <span id="bank_account_status" style="font-size:12px; color:#ff0000; font-style: italic;"></span>
                            <span style="color:red"><?=form_error('bank_account_number');?></span>
                        </div>
                        <div class="form-group">
                            <label for="bank_full_name"><?=lang('player.ui36');?>:</label>
                            <input type="text" class="form-control" name="bank_full_name" value="<?=(set_value('bank_full_name') == null) ? '' : set_value('bank_full_name')?>"/>
                        </div>
                        <div class="form-group">
                            <label for="branch"><?=lang('player.ui54');?>:</label>
                            <input type="text" class="form-control" name="branch" value="<?=(set_value('branch') == null) ? '' : set_value('branch')?>"/>
                        </div>
                        <div class="form-group">
                            <label for="bank_address"><?=lang('player.ui38');?>:</label>
                            <input type="text" class="form-control" name="bank_address" value="<?=(set_value('bank_address') == null) ? '' : set_value('bank_address')?>"/>
                        </div>
                        <div class="form-group">
                            <label for="province"><?=lang('Province');?>:</label>
                            <?php if ($this->utils->isEnabledFeature('disable_chinese_province_city_select')) { ?>
                                <input type="text" class="form-control province" name="province" value="<?=(set_value('province') == null) ? '' : set_value('province')?>"/>
                            <?php } else { ?>
                                <select class="form-control province" name="province" id="inputProvince" data-value="<?=(set_value('province') == null) ? '' : set_value('province')?>" >
                                    <option value=""></option>
                                </select>
                            <?php } ?>
                        </div>
                        <div class="form-group">
                            <label for="city"><?=lang('City');?>:</label>
                            <?php if ($this->utils->isEnabledFeature('disable_chinese_province_city_select')) { ?>
                                <input type="text" class="form-control city" name="city" value="<?=(set_value('city') == null) ? '' : set_value('city')?>"/>
                            <?php } else { ?>
                                <select class="form-control city" name="city" id="inputCity" data-value="<?=(set_value('city') == null) ? '' : set_value('city')?>" >
                                    <option value=""></option>
                                </select>
                            <?php } ?>
                        </div>
                        <div class="form-group">
                            <label for="verified"><?=lang('Set Verify Status to')?>:&nbsp;</label>
                            <label class="radio-inline">
                                <input type="radio" name="verified" value="0" checked="checked"> <?=lang('Unverified')?>
                            </label>
                            <?php if ($this->permissions->checkPermissions('set_financial_account_to_verified')) :?>
                                <label class="radio-inline">
                                    <input type="radio" name="verified" value="1"> <?=lang('Verified')?>
                                </label>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
            </div>

            <center>
                <input type="button" id='btn_add_bank' class="btn btn-success" value="<?=lang('lang.save');?>"/>
                <button data-dismiss="modal" aria-label="Close" class="btn btn-warning"><?=lang('lang.cancel');?></button>
            </center>
        </div>
    </div>
</form>


<script type="text/javascript" src="<?=$this->utils->jsUrl('province_city_select.js')?>" ></script>
<script type="text/javascript">

$("#bank_account_number").blur(function(e){
    $("#bank_account_status").text("");
});

ProvinceCity.bind();

$(document).on('show.bs.modal', '.modal', function (event) {
    var zIndex = 1040 + (10 * $('.modal:visible').length);
    $(this).css('z-index', zIndex);
    setTimeout(function() {
        $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
    }, 0);
});

$(document).ready(function () {
    function checkBankAccount() {
        var dataCorrect = true;
        var bankAccountNumber = $("#bank_account_number").val();
        var trimmed = bankAccountNumber.replace(/ /g, "");
        var hasPunctuation = trimmed.match(/[.,\/#!$%\^&\*;:{}=\-_`~"()+@?`']/g);
        var errText = "";
        if(bankAccountNumber === undefined) {
            dataCorrect= false;
            errText ="<?=lang('bankinfo.acctNo.error.empty')?>";
        }
        if(bankAccountNumber === "") { //錯誤:空字串
            dataCorrect= false;
            errText ="<?=lang('bankinfo.acctNo.error.empty')?>";
        }

        if (hasPunctuation) { //錯誤:有標點
            dataCorrect= false;
            errText ="<?=lang('bankinfo.acctNo.error.punctuationless')?>";
        }

        const length = 120;
        if (trimmed.length > length) { //錯誤:長度>120
            dataCorrect= false;
            errText ="<?=lang('bankinfo.acctNo.error.maximum')?>";
        }
        $("#bank_account_status").text(errText);

        return dataCorrect;
    }

    $("#btn_add_bank").click(function(e) {
        var errors = 0;
        var bankAccountNumber = $("#bank_account_number").val();
        var bankTypeId = $("#bank_type_id").val();

        if( checkBankAccount() ) {
            $("#bank_account_status").text("");
        } else {
			errors += 1;
        }

        if( bankTypeId == "" ) {
			$("#bank_type_status").text("required");
			errors += 1;
        } else {
            $("#bank_type_status").text("");
		}

        if(errors == 0) {
            //disable first, wait return
            $("#btn_add_bank").attr("disabled", true);
            //send ajax
            $.ajax({
                'url' : base_url +'player_management/verifyAddPlayerBankInfo/',
                'type' : 'POST',
                'data': $("#form-root").serialize(),
                'dataType' : "json",
                'success' : function(data){
                    if (data.success === false) {
                        $("#bank_account_status").text(data.reason);
                        //release btn lock
                        $("#btn_add_bank").attr("disabled", false);
                    } else {
                        //success, refresh and show model
                        $('#mainModal').modal('hide');

                        var title = "<?=lang('userinfo.tab05');?>";
                        var content = "<?=lang('sys.user.bank.edit.content')?>";
                        var button = '<button class="btn btn-sm btn-scooter" onclick="refresh_fin_info()"><?=lang('OK')?></button>';

                        success_modal_custom_button(title, content, button);
                    }
                },
                error : function(data) {
                    //release btn lock
                    $("#btn_add_bank").attr("disabled", false);
                    console.log("error data:" +data);
                    console.log("jsondata = " + console.log(JSON.stringify(data)));
                }
            },'json');
        }
    });
});
</script>
<div class="row">
    <div class="col-md-offset-4 col-md-4">
        <form id="withdrawal-form" action="<?=site_url('iframe_module/postWithdrawalBankDetails/')?>" method="post" role="form" autocomplete="off">

            <input type="hidden" name="player_bank_details_id" value="<?=!empty($bank['playerBankDetailsId']) ? $bank['playerBankDetailsId'] : ''?>">
            <input type="hidden" name="bank_account_number_prev" id="bank_account_number_prev" class="form-control input-sm number_only" value="<?php if (!empty($bank['bankAccountNumber'])) {echo $bank['bankAccountNumber'];} else {echo set_value('bank_account_number');} ?>"  required>
            <input type="hidden" name="dw_bank" value="<?php echo $dw_bank; ?>" />

            <?php # bank_name ?>
            <div class="form-group required">
                <label class="control-label" for="bank_name"><?=lang('cashier.67')?> <i style="color:#ff6666;">*</i></label>
                <select name="bank_name" id="bank_name" class="form-control input-sm" required>
                    <option value=""><?=lang('cashier.73')?></option>
                    <?php foreach ($banks as $row) {?>
                        <option value="<?=$row['bankTypeId']?>" <?php echo set_select('bank_name', $row['bankTypeId']); ?> <?=!empty($bank['bankTypeId']) && $bank['bankTypeId'] == $row['bankTypeId'] ? 'selected' : ''?>><?=lang($row['bankName'])?></option>
                    <?php } ?>
                </select>
                <?php echo form_error('bank_name', '<span class="help-block" style="color:#ff6666;font-size:11px;">', '</span>'); ?>
                <span id="error-bank_name" class="help-block" style="color:#ff6666;font-size:11px;"></span>
            </div>

            <?php # bank_account_fullname ?>
            <div class="form-group required">
                <label class="control-label" for="bank_account_fullname"><?=lang('cashier.68')?> <i style="color:#ff6666;">*</i></label>
                <input type="text" name="bank_account_fullname" id="bank_account_fullname" class="form-control input-sm" value="<?=$realname?>" readonly>
                <?php echo form_error('bank_account_fullname', '<span class="help-block" style="color:#ff6666;font-size:11px;">', '</span>'); ?>
            </div>

            <?php # bank_account_number ?>
            <div class="form-group required">
                <label class="control-label" for="bank_account_number"><?=lang('cashier.69')?> <i style="color:#ff6666;">*</i></label>
                <input type="text" name="bank_account_number" id="bank_account_number" class="form-control input-sm number_only" value="<?php if (!empty($bank['bankAccountNumber'])) {echo $bank['bankAccountNumber'];} else {echo set_value('bank_account_number');} ?>"  required>
                <?php echo form_error('bank_account_number', '<span class="help-block" style="color:#ff6666;font-size:11px;">', '</span>'); ?>
                <span id="bank_account_status" style="font-size:12px; color:#ff0000; font-style: italic;"></span>
                <span id="error-bank_account_number" class="help-block" style="color:#ff6666;font-size:11px;"></span>
            </div>

            <?php # bank_address ?>
            <div class="form-group">
                <label class="control-label" for="bank_address"><?=lang('cashier.102')?></label>
                <textarea name="bank_address" id="bank_address" class="form-control input-sm"><?php if (!empty($bank['bankAddress'])) {echo $bank['bankAddress'];} else {echo set_value('bank_address');}?></textarea>
                <?php echo form_error('bank_address', '<span class="help-block" style="color:#ff6666;font-size:11px;">', '</span>')?>
                <span id="error-bank_address" class="help-block" style="color:#ff6666;font-size:11px;"></span>
            </div>

            <?php # bank_branch ?>
            <div class="form-group">
                <label class="control-label" for="bank_branch"><?=lang('cashier.72')?>: </label>
                <input type="text" name="bank_branch" id="bank_branch" class="form-control input-sm" value="<?php if (!empty($bank['branch'])) {echo $bank['branch'];} else {echo set_value('branch');} ?>" >
                <?php echo form_error('bank_branch', '<span class="help-block" style="color:#ff6666;font-size:11px;">', '</span>'); ?>
                <span id="error-bank_branch" class="help-block" style="color:#ff6666;font-size:11px;"></span>
            </div>

            <div class="form-group">
                <div class="pull-right">
                    <a href="<?php echo site_url('iframe_module/iframe_bankDetails') ?>" class="btn btn-danger"><span class="glyphicon glyphicon-circle-arrow-left"></span> <?=lang('button.back')?></a>
                    <button type="button" id='btn_submit' class="btn btn-primary"><span class="glyphicon glyphicon-save"></span> <?=lang('cashier.105')?></button>
                </div>
                <span class="help-block" style="color:#ff6666;"> * <?=lang('cashier.100')?>.</span>
            </div>

        </form>
    </div>
</div>

<script>

$(document).ready(function(){

var error =[],
bankName =$('#bank_name'),
bankAddress =$('#bank_address'),
bankAcctNo = $('#bank_account_number'),
bankAcctNoPrev = $('#bank_account_number_prev'),
bankBranch = $('#bank_branch'),
withdrawalForm =$('#withdrawal-form'),
submit = $('#btn_submit'),
BANKNAME_LABEL ="<?=lang('cashier.67')?>",
ADDRESS_LABEL ="<?=lang('player.ui52')?>",
BRANCH_LABEL = "<?=lang('cashier.72')?>",
VALIDATION_URL = '<?php echo site_url('iframe_module/validateThruAjax') ?>',
BANK_ACCT_NO_LABEL ='<?=lang("cashier.69")?>',
IS_EDIT_MODE = false,
bankDetailsId = '<?=$bank_details_id?>',
bankAcctNoOk = false,
bankAcctNoWasChanged = false
;



if(bankDetailsId){
    IS_EDIT_MODE = true;
}else{
    IS_EDIT_MODE = false;
}


bankName.blur(function(){
requiredCheck($(this).val(),'bank_name',BANKNAME_LABEL)
});

bankAddress.blur(function(){
if(!isBlankField($(this).val(),'bank_address',ADDRESS_LABEL)){
     checkInputIf2OrMoreLanguage($(this).val(),'bank_address',ADDRESS_LABEL)
}else{
    removeErrorItem('bank_address');
}
});

if(!IS_EDIT_MODE){

    bankAcctNo.blur(function(){
    if(requiredCheck($(this).val(),'bank_account_number',BANK_ACCT_NO_LABEL)){
          validateThruAjax($(this).val(),'bank_account_number',BANK_ACCT_NO_LABEL)
    }
});

}else{
    bankAcctNo.blur(function(){
    if(requiredCheck($(this).val(),'bank_account_number',BANK_ACCT_NO_LABEL)){
          validateThruAjax($(this).val(),'bank_account_number',BANK_ACCT_NO_LABEL)
          bankAcctNoWasChanged = true;
    }
    });
}


bankBranch.blur(function(){
if(!isBlankField($(this).val(),'bank_branch',BRANCH_LABEL)){
     checkInputIf2OrMoreLanguage($(this).val(),'bank_branch',BRANCH_LABEL)
}else{
    removeErrorItem('bank_branch');
}
});

//Checkout all errors then go

submit.click(function(){


if(bankAcctNoWasChanged){

    if(bankAddress.val() &&  bankAcctNo.val() && bankName.val() && bankAcctNoOk  ){

    var errorLength = error.length;
    if(errorLength > 0){
        return false;
    }else{
        disableSubmitButton();
        withdrawalForm.submit();

        }
    }

}else{

    if(bankAddress.val() &&  bankAcctNo.val() && bankName.val()  ){

        var errorLength = error.length;
        if(errorLength > 0){
            return false;
        }else{
            disableSubmitButton();
            withdrawalForm.submit();

        }
    }

}


});







function validateThruAjax(fieldVal,id,label){

var data;
if(!IS_EDIT_MODE){

     if(id == "bank_account_number"){
        data = {bank_account_number:fieldVal, "account_type": $('[name=dw_bank]').val()};
    }
}else{
     if(id == "bank_account_number"){
        data = {
            bank_account_number:bankAcctNo.val(),
            bank_account_number_prev:bankAcctNoPrev.val(),
            "account_type": $('[name=dw_bank]').val()
        };
    }

}

 $.ajax({
        url : VALIDATION_URL,
        type : 'POST',
        data : data,
        dataType : "json",
        cache : false,
      }).done(function (data) {
        if (data.status == "success") {
            removeErrorItem(id);
            removeErrorOnField(id);

            //Lock to prevent submitting
             if(id == "bank_account_number"){
                bankAcctNoOk = true;
             }

        }
        if (data.status == "error") {
            var message = data.msg;
            showErrorOnField(id,message);
            addErrorItem(id);
        }
      }).fail(function (jqXHR, textStatus) {
        // utils.safelog(textStatus);
        /*Note: this is for session timeout,if the session is out because this is ajax, eventually it will go to log in page*/
        if(jqXHR.status>=300 && jqXHR.status<500){
            location.reload();
        }else{
            alert(textStatus);
        }
      });
}


function checkInputIfAlphaChar(fieldVal,id,label){

var message = label+" should be  Alpha Letters only";

var pattern = /^[a-zA-Z-_,/\s]+$/;
var result = pattern.test(fieldVal);

if (result){
     removeErrorItem(id);
     removeErrorOnField(id);
    return true;
}else{

   showErrorOnField(id,message);
   addErrorItem(id);
   return false;

}

}

function checkInputIfAlphaNumericChar(fieldVal,id,label){

var message = label+" should be  Alpha Numeric only";

var pattern = /^[a-zA-Z0-9@&-_,/\s]+$/;
var result = pattern.test(fieldVal);

if (result){
     removeErrorItem(id);
     removeErrorOnField(id);
    return true;
}else{

   showErrorOnField(id,message);
   addErrorItem(id);
   return false;

}

}

function checkInputIf2OrMoreLanguage(fieldVal,id,label){
    var  message = label+" Should not mix Letters or Numbers or any Language",
     isOtherChar=false,
     isNumber=false,
     isAlphabet =false,

     fieldValLength = fieldVal.length;
     textArr = fieldVal.split(','),
     otherCharPattern = /^[-\W]+$/,
     numberPattern= /^[0-9-_,/\s]+$/,
     alphabetPattern =  /^[a-zA-Z-_,/\s]+$/ ;

    if(otherCharPattern.test(fieldVal)){
        isOtherChar=true;
    }else{
        isOtherChar=false;
    }

    if(numberPattern.test(fieldVal)){
        isNumber=true;
    }else{
        isNumber=false;
    }
    if(alphabetPattern.test(fieldVal)){
        isAlphabet=true;
    }else{
        isAlphabet=false;
    }

if(!isOtherChar && !isNumber && !isAlphabet){

     showErrorOnField(id,message);
     addErrorItem(id);
     return false;
}else{
    removeErrorItem(id);
    removeErrorOnField(id);
    return true;
}



}



function isBlankField(fieldVal,id,label){
    var message="";
  if(fieldVal == ""){
    showErrorOnField(id,message);
    addErrorItem(id);
    return true;
  }else{
    removeErrorItem(id);
    removeErrorOnField(id);
    return false;
  }

}

function requiredCheck(fieldVal,id,label){
    var message = label+" is required";
    if(!fieldVal && (fieldVal == "")){
        showErrorOnField(id,message)
        addErrorItem(id);
        return false;
    }else{
        removeErrorOnField(id);
        removeErrorItem(id);

        return true;
    }
}

function showErrorOnField(id,message){
    $('#error-'+id).html(message);
}

function removeErrorOnField(id){
    $('#error-'+id).html("");
}




 function removeErrorItem(item){

    var i = error.indexOf(item);
        if(i != -1) {
            error.splice(i, 1);
        }

 }

 function addErrorItem(item){
    if(jQuery.inArray(item, error) == -1){
            error.push(item);

    }

 }

function disableSubmitButton(){
    submit.prop('disabled', true);

}
function ableSubmitButton(){
    submit.prop('disabled', false);

}




});//end document



</script>
<style type="text/css">
	.control-label required {
	}
	.btn-pref .btn {
	    -webkit-border-radius:0 !important;
	}
</style>
				<?php if (!$enabled_withdrawal) {echo lang('withdrawal.error.disabled');} else {
	?>
				<div class="custom-offset-2 custom-sm-8" style="text-align:center">
				<form id="new_acct_form" action="<?=site_url('iframe_module/verifyWithdrawal/')?>" method="post" class="form-horizontal">
					<?php
$na = "";
	$na_attr = "";
	$pa = "";
	$pa_attr = "";
	if (isset($_POST['account_type']) && $_POST['account_type'] == 'pa') {
		$pa = "checked";
		$pa_attr = " ";
		$na = " ";
		$na_attr = "disabled";
	} else {
		$pa = " ";
		$pa_attr = "disabled";
		$na = "checked";
		$na_attr = " ";
	}
	?>
					<br/>
					<input type="hidden" name="dw_ip" id="dw_ip" value="<?=$this->input->ip_address();?>">
					<input type="hidden" name="account_type" id="account_type" >
<?php

	if ($show_warning_for_one_withdraw) {
		?>
					<div class="form-group">
						<p class="bg-danger" style="padding: 10px">
						<?php echo lang('Sorry, your last withdraw request is not done, so you can\'t start new request'); ?>
						</p>
					</div>
<?php

	}
	?>
					<div class="form-group custom-group">
						<label class="custom-pdl-20 custom-offset-1 custom-sm-3 custom-leftside control-label required" style="text-align: left;"><?=lang('cashier.02');?></label>
						<div class="custom-sm-7 custom-leftside custom-pdl-30">
							<input type="text" class="form-control input-sm only_amount" value="<?=$this->utils->displayCurrency($wallet_amount);?>" disabled>
						</div>
					</div>
					<div class="form-group custom-group">
						<label class="custom-pdl-20 custom-offset-1 custom-sm-3 custom-leftside control-label required" style="text-align: left;"><?=lang('cashier.09');?></label>
						<div class="custom-sm-7 custom-leftside custom-pdl-30">
							<input type="number" name="amount" data-toggle="tooltip" data-placement="bottom" step="any" id="amount" min="0" max="<?=$max_withdraw_per_transaction;?>" class="form-control input-sm only_amount" required value="<?php echo set_value("amount", 0); ?>" />
							<?php echo form_error('amount', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
							<p id="amountDesc" style="font-size:11px;font-color:#464545 !important;"></p>
							<span id="error-amount" class="help-block" style="color:#ff6666;font-size:11px;"></span>
							<span class="hide" id="minWithdraw"><?=$min_withdraw_rule;?></span>
						</div>
					</div>

					<?php if (@$showPasswordField): ?>
					<div class="form-group custom-group">
						<label class="custom-pdl-20 custom-offset-1 custom-sm-3 custom-leftside control-label required" style="text-align: left;"><?=lang('cashier.enter.password');?></label>
						<div class="custom-sm-7 custom-leftside custom-pdl-30">
							<input name="password" id="password" class="form-control input-sm" type="password">
							<?php echo form_error('password', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
							<span id="error-password" class="help-block" style="color:#ff6666;font-size:11px;"></span>
						</div>
					</div>
					<?php endif;?>
					<?php if (@$showSMSField): ?>
					<div class="form-group custom-group">
					        <label class="custom-pdl-20 custom-offset-1 custom-sm-3 custom-leftside control-label required" style="text-align: left;"><?=lang('cashier.enter.sms');?></label>
					        <div class="custom-sm-7 custom-leftside custom-pdl-30">
					                <input type="text" name="sms_verification_code" id="sms_verification" class="form-control input-sm"  data-toggle="tooltip"
					                        value="<?php echo set_value('sms_verification_code'); ?>" maxlength="6"
					                        placeholder="<?php echo lang('Verification Code'); ?>" />
					                <input type="button" id="send_sms_verification" class="btn btn-xs btn-success" value="<?php echo lang('Send SMS'); ?>" style="margin-top: 5px; float:left" />
					                <span id="sms_verification_msg" style="color:#ff6666;display:none;float:left; margin-top:7px; margin-left: 5px"></span>
					                <?php echo form_error('sms_verification_code', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
					                <span id="error-password" class="help-block" style="color:#ff6666;font-size:11px;"></span>
					        </div>
					</div>
					<?php endif;?>

					<hr class="style-two"/>
					<div class="col-md-10 col-md-offset-1">


					<h4 class="pull-left" ><?=lang('cashier.142');?></h4>
					    <div class="btn-pref btn-group btn-group-justified btn-group-lg" role="group" aria-label="...">
					        <div class="btn-group" role="group">
					            <button type="button" onclick="newAccount()" id="tab-new-account" class="btn btn-primary" href="#tab1" data-toggle="tab"><span class="glyphicon glyphicon-credit-card" aria-hidden="true"></span>
					                <div class="hidden-xs"><?=lang('cashier.66');?></div>
					            </button>
					        </div>
					        <div class="btn-group" role="group">
					            <button type="button" onclick="preferredAccount()" id="tab-preferred-account" class="btn btn-default" href="#tab2" data-toggle="tab"><span class="glyphicon glyphicon-briefcase" aria-hidden="true"></span>
					                <div class="hidden-xs"><?=lang('cashier.65');?></div>
					            </button>
					        </div>
					    </div>

					    <div class="well">
					      <div class="tab-content">

					        <div class="tab-pane fade in active" id="tab1">
				          		<div class="form-group">
									<label class="custom-offset-1 custom-sm-3 custom-pdl-25 custom-leftside control-label required" style="text-align: left;"><?=lang('cashier.67');?></label>
									<div class="custom-sm-7 custom-leftside custom-pdl-20" style="padding-right:25px;">
										<select name="bank_name" id="bank_name" class="form-control input-sm" <?=$na_attr?> required>
											<option value="">-- <?=lang('cashier.73');?> --</option>
											<?php foreach ($banks as $row): ?>
												<?php if ($row['enabled_withdrawal']): ?>
													<option value="<?=$row['bankTypeId']?>"><?=lang($row['bankName'])?></option>
												<?php endif?>
											<?php endforeach?>
										</select>
										<?php echo form_error('bank_name', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
										 <span id="error-bank_name" class="help-block" style="color:#ff6666;font-size:11px;"></span>
									</div>
								</div>

								<div class="form-group">
									<label class="custom-offset-1 custom-sm-3 custom-pdl-10 custom-leftside control-label required" style="text-align: left;"><?=lang('cashier.68');?></label>
									<div class="custom-sm-7 custom-leftside custom-pdl-20" style="padding-right:25px;">
										<input type="number" name="bank_account_fullname" id="bank_account_fullname" class="form-control input-sm" value="<?=$realname?>" readonly>
									</div>
								</div>

								<div class="form-group">
									<label class="custom-offset-1 custom-sm-3 custom-pdl-10 custom-leftside control-label required" style="text-align: left;"><?=lang('cashier.69');?>&nbsp;&nbsp;</label>
									<div class="custom-sm-7 custom-leftside custom-pdl-20" style="padding-right:25px;">
										<input type="text" min='0' name="bank_account_number" id="bank_account_number" class="form-control input-sm amount_only" <?=$na_attr?>  required>
										<?php echo form_error('bank_account_number', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
										<span id="error-bank_account_number" class="help-block" style="color:#ff6666;font-size:11px;"></span>

									</div>
								</div>

								<?php
									if( ! $this->utils->getConfig('bank_address_disabled') ){
								?>

										<div class="form-group">
											<label class="custom-offset-1 custom-sm-3 custom-pdl-10 custom-leftside control-label required" style="text-align: left;"><?=lang('cashier.102');?></label>
											<div class="custom-sm-7 custom-leftside custom-pdl-20" style="padding-right:25px;">
												<textarea name="bank_address" id="bank_address" class="form-control input-sm" <?=$na_attr?> ></textarea>
												<?php echo form_error('bank_address', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
												<span id="error-bank_address" class="help-block" style="color:#ff6666;font-size:11px;"></span>
											</div>
										</div>

								<?php
								}
								?>

								<div class="form-group">
									<label class="custom-offset-1 custom-sm-3 custom-pdl-10 custom-leftside control-label required" style="text-align: left;"><?=lang('cashier.72');?></label>
									<div class="custom-sm-7 custom-leftside custom-pdl-20" style="padding-right:25px;">
										<input type="text" name="branch" id="branch" class="form-control input-sm fld-branch" required <?=$na_attr?> >
										<?php echo form_error('branch', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
										<span id="error-branch" class="help-block" style="color:#ff6666;font-size:11px;"></span>
									</div>
								</div>

					        </div>

					        <div class="tab-pane fade in" id="tab2">
					        	<center>
									<select name="preferred_account" id="preferred_account" class="form-control input-sm" <?=$pa_attr?>  required style="width:50%">
										<option value="">-- <?=lang('cashier.74');?> --</option>
										<?php foreach ($playerBankDetails as $row): ?>
											<option value="<?=$row['playerBankDetailsId'];?>"> <?php echo lang($row['bankName']) . ' - ' . $row['bankAccountFullName'] ?></option>
										<?php endforeach?>
									</select>
									<?php echo form_error('preferred_account', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
									<span id="error-preferred_account" class="help-block" style="color:#ff6666;font-size:11px;"></span>
								</center>

					        </div>

							<?php
								if (@$showWithdrawPasswordField){
							?>
							<div class="form-group">
								<label class="custom-offset-1 custom-sm-3 custom-pdl-10 custom-leftside control-label required" style="text-align: left;"><?=lang('Withdrawal Password');?></label>
								<div class="custom-sm-7 custom-leftside custom-pdl-20" style="padding-right:25px;">
									<input type="password" name="withdrawal_password" id="withdrawal_password" class="form-control input-sm" required >
									<?php echo form_error('withdrawal_password', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
									<span id="error-withdrawal_password" class="help-block" style="color:#ff6666;font-size:11px;"></span>
								</div>
							</div>
							<?php
								}
							?>
					      </div>
					    </div>
					    <hr class="style-two"/>
							<div class="form-group">
								<div class="custom-sm-7" style="margin:auto;">
									<div class="checkbox">
										<label>
											<input class="require-one" type="checkbox" name="rememberMyAccount_cb" id="withdraw_rememberMyAccount_cb" value="1"> <?=lang('cashier.59');?>
										</label>
									</div>
									<?php echo form_error('remember_bank_details', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
								</div>
							</div>

							<div class="form-group form-inline">
								<div class="custom-sm-7" style="margin:auto;">
									<input type="submit" value="<?php echo lang('lang.save'); ?>" class="btn btn-primary" />
									<input type="button" value="<?php echo lang('lang.cancel'); ?>" class="btn-cancel btn btn-danger" onclick="window.location.href='<?php echo site_url("/iframe_module/iframe_viewCashier"); ?>'" />
								</div>
							</div>

							<div class="col-sm-offset-3 col-sm-7">
								<div class="help-block">

								</div>
							</div>
					</div>
				</form>
				</div>
				<?php }
?>

					<div class="row">
					<hr class="style-two"/>
					<div class="col-md-3"></div>
					<div class="col-md-6">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<?=lang('cashier.31');?>
								<div class="pull-right">
									<input id="toggle-event" checked="false" type="checkbox" data-toggle="toggle" data-size="mini" data-onstyle="warning" data-on="<?=lang('lang.show')?>" data-off="<?=lang('lang.hide')?>">
								</div>
							</div>
							<div class="panel-body">
							<div class="col-md-12" id="withdrawalDepositHistorySec">
								<?php //$this->load->view('iframe/cashier/member_deposit_history');?>
							</div>
						</div>
					</div>

					</div>
			</div>

<!-- <button id="yo"></button>
  <div class="alert alert-success " id="form-alert-success" role="alert" style="display:none" >
                <button type="button" class="close" ><span aria-hidden="true">&times;</span><span id="close-success"class="sr-only">Close</span></button>
              helloee
                <span id="message"></span>
            </div> -->
<script>
  $(function() {

	var withdrawMessage = {
		minimumWithdraw 			:	'<?= lang('Withdraw Minimum'); ?>',
		maximumWithdrawPerTrans		:	'<?= lang('Withdraw Max Per Transaction'); ?>',
	};

	withdrawalDescription($('#amountDesc'));

  	$('#withdrawalDepositHistorySec').hide();
    $('#toggle-event').change(function() {
      if($(this).prop('checked')){
      	$('#withdrawalDepositHistorySec').hide();
      }else{
      	$('#withdrawalDepositHistorySec').show();
      }
    });

    withdrawalDepositHistory(0);

	function withdrawalDescription(el) {
		var minimum = $('#minWithdraw').text();
		var maximum = $('#amount').attr('max');
		var title;
		if(maximum == 0) {
		 	title = withdrawMessage.minimumWithdraw + ' ' + minimum;
		} else {
			title = withdrawMessage.minimumWithdraw + ' ' + minimum + ' , ' + withdrawMessage.maximumWithdrawPerTrans  + ' ' + maximum;
		}
		el.html(title);
	}

    function withdrawalDepositHistory(segment) {

        var xmlhttp = GetXmlHttpObject();

        if (xmlhttp == null) {
            alert("Browser does not support HTTP Request");
            return;
        }

        // var from = document.getElementById('from_date').value;
        // var to = document.getElementById('to_date').value;

        url = base_url + "iframe_module/withdrawalDepositHistory/" + segment;

        var div = document.getElementById("withdrawalDepositHistorySec");

        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4) {
                div.innerHTML = xmlhttp.responseText;
            }

            if (xmlhttp.readyState != 4) {
                div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
            }
        }
        xmlhttp.open("GET", url, true);
        xmlhttp.send(null);
    }
  })
</script>




<script>




$(document).ready(function(){


var error =[],
amountToWidthraw = $('#amount'),
bankName =$('#bank_name'),
//bankCity = $('#city'),
//bankProvince =$('#province'),
bankAddress =$('#bank_address'),
bankAcctNo = $('#bank_account_number'),
bankAcctNoPrev = $('#bank_account_number_prev'),
bankBranch = $('#branch'),
preferredAcct = $('#preferred_account'),
newAcctForm =$('#new_acct_form'),
submit = $('#btn_submit'),
helpBlocks =  $('.help-block'),
BANKNAME_LABEL ="<?=lang('cashier.67')?>",

BANKADRESS_LABEL="<?=lang('cashier.102')?>",
//PROVINCE_LABEL ="<?=lang('player.ui52')?>",
BRANCH_LABEL = "<?=lang('cashier.72')?>",
VALIDATION_URL = '',
CITY_LABEL ="<?=lang('cashier.71')?>",
BANK_ACCT_NO_LABEL ='<?=lang("cashier.69")?>',
WITHDRAW_AMOUNT_LABEL ='Amount to withdraw',
CURRENT_TAB = 'tab-new-account',
PREFERRED_ACCT_LABEL = 'Preferred Account',
bankAcctNoOk = false,
bankAcctNoWasChanged = false
;



$(".btn-pref .btn").click(function () {
    $(".btn-pref .btn").removeClass("btn-primary").addClass("btn-default");
    CURRENT_TAB =$(this).attr('id');
    // $(".tab").addClass("active"); // instead of this do the below
    $(this).removeClass("btn-default").addClass("btn-primary");
    resetErrors();
   // amountToWidthraw.val("");

});

amountToWidthraw.blur(function(){
	if(requiredCheck($(this).val(),'amount',WITHDRAW_AMOUNT_LABEL)){
		validateThruAjax($(this).val(),'amount',WITHDRAW_AMOUNT_LABEL);
	}
});



// bankName.blur(function(){
// requiredCheck($(this).val(),'bank_name',BANKNAME_LABEL)
// });

bankAddress.blur(function(){
requiredCheck($(this).val(),'bank_address',BANKADRESS_LABEL)
});



bankName.blur(function(){
requiredCheck($(this).val(),'bank_name',BANKNAME_LABEL)
});

// bankProvince.blur(function(){
// if(!isBlankField($(this).val(),'province',PROVINCE_LABEL)){
//     checkInputIf2OrMoreLanguage($(this).val(),'province',PROVINCE_LABEL)
// }else{
//     removeErrorItem('province');
// }
// });


// bankCity.blur(function(){
// if(!isBlankField($(this).val(),'city',CITY_LABEL)){
//      checkInputIf2OrMoreLanguage($(this).val(),'city',CITY_LABEL)
// }else{
//     removeErrorItem('city');
// }
// });


bankAcctNo.blur(function(){
    if(requiredCheck($(this).val(),'bank_account_number',BANK_ACCT_NO_LABEL)){
          validateThruAjax($(this).val(),'bank_account_number',BANK_ACCT_NO_LABEL)
    }

    //requiredCheck($(this).val(),'bank_account_number',BANK_ACCT_NO_LABEL);
});



bankBranch.blur(function(){
if(!isBlankField($(this).val(),'branch',BRANCH_LABEL)){
     checkInputIf2OrMoreLanguage($(this).val(),'branch',BRANCH_LABEL)
}else{
    removeErrorItem('branch');
}
});


preferredAcct.blur(function(){
   requiredCheck($(this).val(),'preferred_account',PREFERRED_ACCT_LABEL)
});

//Checkout all errors then go

submit.click(function(){

if(CURRENT_TAB === "tab-new-account"){
    if(amountToWidthraw.val() &&  bankAcctNo.val() && bankName.val()  ){

        var errorLength = error.length;
        if(errorLength > 0){
            return false;
        }else{
            disableSubmitButton();
            newAcctForm.submit();

        }
    }
}else{

	if(amountToWidthraw.val() && preferredAcct.val()  ){

        var errorLength = error.length;
        if(errorLength > 0){
            return false;
        }else{
            disableSubmitButton();
            newAcctForm.submit();

        }
    }
}




});







function validateThruAjax(fieldVal,id,label){

var data;

if(CURRENT_TAB === "tab-new-account"){
	if(id == "bank_account_number"){
        data = {bank_account_number:fieldVal};
        VALIDATION_URL = '<?php echo site_url('iframe_module/validateThruAjax') ?>';
    }
}
if(id == "amount"){
    data = {amount:Number(fieldVal)};
 VALIDATION_URL = '<?php echo site_url('iframe_module/valWidthdrawalAmtThruAjax') ?>';
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
      }).fail(function (jqXHR, textStatus, errorThrown) {
        // utils.safelog(jqXHR);
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
	// var  message = label+" Should not mix Letters or Numbers or any Language",
 //     isOtherChar=false,
 //     isNumber=false,
 //     isAlphabet =false,

	//  fieldValLength = fieldVal.length;
	//  textArr = fieldVal.split(','),
	//  otherCharPattern = /^[-\W]+$/,
	//  numberPattern= /^[0-9-_,/\s]+$/,
	//  alphabetPattern =  /^[a-zA-Z-_,/\s]+$/ ;

	// if(otherCharPattern.test(fieldVal)){
	// 	isOtherChar=true;
	// }else{
	// 	isOtherChar=false;
	// }

	// if(numberPattern.test(fieldVal)){
	// 	isNumber=true;
	// }else{
	// 	isNumber=false;
	// }
	// if(alphabetPattern.test(fieldVal)){
	// 	isAlphabet=true;
	// }else{
	// 	isAlphabet=false;
	// }
//utils.safelog('isOtherChar:'+isOtherChar+' isNumber:'+isNumber+' isAlphabet:'+isAlphabet);
// if(!isOtherChar && !isNumber && !isAlphabet){

//      showErrorOnField(id,message);
//      addErrorItem(id);
//      return false;
// }else{
// 	removeErrorItem(id);
//     removeErrorOnField(id);
//     return true;
// }

	return true;


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




function resetErrors(){

 $('#error-bank_name').html("");
 $('#error-city').html("");
 $('#error-province').html("");
 $('#error-bank_account_number').html("");
 $('#error-branch').html("");
 $('#error-preferred_account').html("");
	 error = [];
}

// Handling of SMS verification code
var sendSmsVerification = function() {
       $('#sms_verification_msg').text('<?php echo lang('Please wait') . '...'; ?>');
       $('#sms_verification_msg').show();

       $.getJSON('<?php echo site_url('iframe_module/iframe_register_send_sms_verification'); ?>', function(data){
               if(data.success) {
                       $('#sms_verification_msg').text('<?php echo lang('SMS sent'); ?>');
               }
               else {
                       $('#sms_verification_msg').text('<?php echo lang('SMS failed'); ?>');
               }
       });
}
$('#send_sms_verification').click(sendSmsVerification);

});//end document




</script>
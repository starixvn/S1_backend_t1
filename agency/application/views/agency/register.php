<style type="text/css">
div.fields {
    height: 90px;
}

span.errors {
    padding: 0;
    margin: 0;
    float: left;
    font-size: 11px;
    color: red;
}

</style>

<div class="content-container">
	<br/>
	<div class="row">
		<div class="col-md-12" id="toggleView">
		<form method="POST" id="agency-register-form" action="<?=site_url('agency/verifyRegister/'.$trackingCode)?>" accept-charset="utf-8">
			<input type="hidden" name="parentId" value="<?=$parentId;?>">
			<div class="panel panel-primary ">
				<div class="panel-heading">
					<h4 class="panel-title pull-left"><i class="glyphicon glyphicon-list-alt"></i> <?=lang('reg.a01');?> </h4>
					<div class="pull-right"><?php echo lang('Fields with (<font style="color:red;">*</font>) are required');?>.</div>
					<div class="clearfix"></div>
				</div>

				<div class="panel panel-body" id="agency_panel_body">
					<!-- Content Info -->
					<div class="col-md-12">
						<div class="col-md-3 fields">
							<label for="username"><font style="color:red;">*</font> <?=lang('reg.03');?></label>

							<input type="text" name="username" id="username" class="form-control " value="<?=set_value('username');?>" data-toggle="tooltip" title="<?=lang('reg.a04');?>">
							<span class="errors"><?php echo form_error('username'); ?></span>
							<span id="error-username" class="errors"></span>
						</div>

						<div class="col-md-3 fields">
							<label for="password"><font style="color:red;">*</font> <?=lang('reg.05');?></label>

							<input type="password" name="password" id="password" class="form-control" value="<?=set_value('password');?>" data-toggle="tooltip" title="<?=lang('reg.a06');?>">
							<span class="errors"><?php echo form_error('password'); ?></span>
							<span id="error-password" class="errors"></span>
						</div>

						<div class="col-md-3 fields">
							<label for="confirm_password"><font style="color:red;">*</font> <?=lang('reg.07');?></label>

							<input type="password" name="confirm_password" id="confirm_password" class="form-control" value="<?=set_value('confirm_password');?>" data-toggle="tooltip" title="<?=lang('reg.a08');?>">
							<span class="errors"><?php echo form_error('confirm_password'); ?></span>
							<span id="error-confirm_password" class="errors"></span>
						</div>

						<?php if ($agency_registration_fields['email']['visible']) {?>
						<div class="col-md-3 fields">
							<label for="email">
								<?php if ($agency_registration_fields['email']['required']) {?>
								<font style="color:red;">*</font>
								<?php }?>
							<?=lang('reg.a17');?></label>

							<input type="text" name="email" id="email" class="form-control" value="<?=set_value('email');?>" data-toggle="tooltip" title="<?=lang('reg.a18');?>">
							<span class="errors"><?php echo form_error('email'); ?></span>
							<span id="error-email" class="errors"></span>
						</div>
						<?php } ?>

						<?php if ($agency_registration_fields['firstname']['visible']) {?>
							<div class="col-md-3 fields">
								<label for="firstname">
									<?php if ($agency_registration_fields['firstname']['required']) {?>
										<font style="color:red;">*</font>
									<?php }?>

									<?=lang('First Name');?>
								</label>

								<input type="text" name="firstname" id="firstname" class="form-control" value="<?=set_value('firstname');?>">
								<span class="errors"><?php echo form_error('firstname'); ?></span>
							</div>
						<?php } ?>

						<?php if ($agency_registration_fields['lastname']['visible']) {?>
							<div class="col-md-3 fields">
								<label for="lastname">
									<?php if ($agency_registration_fields['lastname']['required']) {?>
										<font style="color:red;">*</font>
									<?php }?>

									<?=lang('Last Name');?>
								</label>

								<input type="text" name="lastname" id="lastname" class="form-control" value="<?=set_value('lastname');?>">
								<span class="errors"><?php echo form_error('lastname'); ?></span>
							</div>
						<?php } ?>

						<?php if ($agency_registration_fields['gender']['visible']) {?>
							<div class="col-md-3 fields">
								<label for="gender">
									<?php if ($agency_registration_fields['gender']['required']) {?>
										<font style="color:red;">*</font>
									<?php }?>

									<?=lang('reg.a12');?>
								</label>

								<div class="form-group">
									<input type="radio" name="gender" id="male" value="Male" <?=(set_value('gender') == 'Male') ? 'checked' : ''?>> <?=lang('reg.a13');?> &nbsp;&nbsp;&nbsp;
									<input type="radio" name="gender" id="female" value="Female" <?=(set_value('gender') == 'Female') ? 'checked' : ''?>> <?=lang('reg.a14');?>
								</div>
								<span class="errors"><?php echo form_error('gender'); ?></span>
							</div>
						<?php } ?>

						<?php if ($agency_registration_fields['mobile']['visible']) {?>
							<div class="col-md-3 fields">
								<label for="mobile">
									<?php if ($agency_registration_fields['mobile']['required']) {?>
										<font style="color:red;">*</font>
									<?php } ?>

									<?=lang('reg.a24');?>
								</label>

								<input type="text" name="mobile" id="mobile" maxlength="30" class="form-control number_only" value="<?=set_value('mobile');?>">
								<span class="errors"><?php echo form_error('mobile'); ?></span>
								<span id="error-mobile"class="errors"></span>
							</div>
						<?php } ?>

						<?php if ($agency_registration_fields['im1']['visible']) {?>
							<div class="col-md-3 fields">
								<label for="im1">
									<?php if ($agency_registration_fields['im1']['required']) {?>
										<font style="color:red;">*</font>
									<?php } ?>

									<?=lang('Instant Message 1');?>
								</label>

								<input type="text" name="im1" id="im1" class="form-control" maxlength="50" value="<?=set_value('im1');?>" >
								<span class="errors"><?php echo form_error('im1'); ?></span>
								<span id="error-im1" class="errors"></span>
							</div>
						<?php }?>

						<?php if ($agency_registration_fields['im2']['visible']) {?>
							<div class="col-md-3 fields">
								<label for="im2">
									<?php if ($agency_registration_fields['im2']['required']) {?>
										<font style="color:red;">*</font>
									<?php }?>

									<?=lang('Instant Message 2');?>
								</label>

								<input type="text" name="im2" id="im2" class="form-control" maxlength="50" value="<?=set_value('im2');?>" >
								<span class="errors"><?php echo form_error('im2'); ?></span>
								<span id="error-im2" class="errors"></span>
							</div>
						<?php } ?>

						<?php if ($agency_registration_fields['language']['visible']) {?>

							<div class="col-md-3 fields">
								<label for="languge">
									<?php if ($agency_registration_fields['language']['required']) {?>
										<font style="color:red;">*</font>
									<?php } ?>

									<?=lang('ban.lang');?>
								</label>

								<select class="form-control" name="language" id="language">
                                    <option value="english"<?php echo (set_value('language', $current_language_name) == 'english') ? ' selected="selected"' : ''; ?>>English</option>
                                    <option value="chinese"<?php echo (set_value('language', $current_language_name) == 'chinese') ? ' selected="selected"' : ''; ?>>中文</option>
                                    <option value="korea"<?php echo (set_value('language', $current_language_name) == 'korea') ? ' selected="selected"' : ''; ?>>Korea</option>
						        </select>
						        <span class="errors"><?php echo form_error('languge'); ?></span>
							</div>
						<?php } ?>

						<?php if ($agency_registration_fields['language']['visible']) {?>
                        <div class="form-group">
                            <div class="col-md-6">
                                <label for="note"><?=lang('Note');?></label>
                                <textarea name="note" id="note" class="form-control input-sm" maxlength="100"><?=set_value('note');?></textarea>
                                <span class="errors"><?php echo form_error('note'); ?></span>
                                <span id="error-note" class="errors"></span>
                            </div>
                        </div>
						<?php } ?>
						<input type="hidden" name="currency" id="currency" value="<?=$currency?>" />
					</div>
					<!-- End of Content Info -->
				</div>
			</div>
			<center>
				<input type="submit" id="submit" class="btn btn-info custom-btn-size" value="<?=lang('reg.a46');?>">
				<button id="cancel" link="<?=site_url('agency')?>" class="btn btn-default custom-btn-size"><?=lang('lang.cancel');?></button>
			</center>
</form>

		</div>

	</div>
</div>

<script>

$(document).ready(function(){


	$('#username').tooltip({
		placement : "top"

	});
	$('#password').tooltip({
		placement : "top"

	});
	$('#confirm_password').tooltip({
		placement : "top"

	});
	$('#email').tooltip({
		placement : "top"

	});



var  IS_USERNAME_EXIST_URL = '<?php echo site_url('agency/validateThruAjax') ?>',
      username = $("#username"),
      password = $("#password"),
      confirmPassword = $('#confirm_password'),
      email = $('#email'),
      // birthday = $('#birthday'),
      // birthdayCal = $('.calendar-table'),
      // modeOfContact =$('#mode_of_contact'),
      // phone =$('#phone'),
      mobile =$('#mobile'),
      // imtype1 =$('#imtype1'),
      // imtype2 =$('#imtype2'),
      im1 = $('#im1'),
      im2 = $('#im2'),
      submit = $('#submit'),
      cancel = $('#cancel'),
      affRegisterForm =$('#agency-register-form'),
      USERNAME_LABEL = "<?=lang('reg.03')?>",
      PASSWORD_LABEL = "<?=lang('reg.05')?>",
      CONFIRM_PASSWORD_LABEL = "<?=lang('reg.07')?>",
      EMAIL_LABEL = "<?=lang('aff.al11')?>",
      // BIRTHDAY_LABEL = "<?=lang('aff.ai04')?>",
      // PREFERRED_MODE_OF_CONTACT = "<?='aff.ai20'?>",
      // PHONE_LABEL ="<?=lang('aff.ai15')?>",
      MOBILE_PHONE_LABEL ="<?=lang('aff.ai14')?>",
      // IMTYPE1_LABEL ="<?=lang('aff.ai16')?>",
      // IMTYPE2_LABEL ="<?=lang('aff.ai18')?>",
      IM1_LABEL ="<?=lang('aff.ai17')?>",
      IM2_LABEL ="<?=lang('aff.ai19')?>",
      formOk =false,
      // birthdayOk =false;
      emailOk = false;
      usernameOk = false;
      // currentPreferredMode='',
      // currentIm1typeVal ='',
      // currentIm2typeVal ='',
      error =[];


// imtype2.prop('disabled', true);
 // birthday .val("");


cancel.click(function(){
  var URL =$(this).attr('link');
	location.href=URL;
});

username.blur(function(){
  if (requiredCheck($(this).val(),'username',USERNAME_LABEL)){
      validateThruAjax($(this).val(),'username',USERNAME_LABEL);
   }
});
password.blur(function(){
  if (requiredCheck($(this).val(),'password',PASSWORD_LABEL)){
 	{
	 	if(checkPassword($(this).val(),'password',PASSWORD_LABEL)){
	 		if(confirmPassword.val() != ""){
	 			checkPasswordMatch(confirmPassword.val(),'confirm_password',CONFIRM_PASSWORD_LABEL);
	 		}
	 	}
 	}
}
});
confirmPassword.blur(function(){
  if (requiredCheck($(this).val(),'confirm_password',CONFIRM_PASSWORD_LABEL)){
  	checkPasswordMatch($(this).val(),'confirm_password',CONFIRM_PASSWORD_LABEL);
 }
});

email.blur(function(){
  if (requiredCheck($(this).val(),'email',EMAIL_LABEL)){
     validateThruAjax($(this).val(),'email',EMAIL_LABEL)

   }
 });



// if preferred mode active in selecting then remove error if phone is not blank
// phone.blur(function(){
// 	if(currentPreferredMode == 'phone'){
//      if(requiredCheck($(this).val(),'phone',PHONE_LABEL)){
//      	 removeErrorOnField('mode_of_contact');
//      	 removeErrorItem('mode_of_contact');
//      }

// 	}

// });
// if preferred mode active in selecting then remove error if mobile is not blank
/*
mobile.blur(function(){
	if(currentPreferredMode == 'mobile'){
     if(requiredCheck($(this).val(),'mobile',MOBILE_PHONE_LABEL)){
     	removeErrorOnField('mode_of_contact');
     	removeErrorItem('mode_of_contact');
         removeErrorItem('mobile');
     }

	}

});
 */



// imtype1.change(function(){

// if($(this).val() ==="QQ"){
// 	emptyInput('im1');
// 	changeInputType("number","im1");
// 	requiredCheck(im1.val(),'im1',IM1_LABEL);

// 	currentIm1typeVal = "QQ";
// 	im1.focus();
// }else if($(this).val() === "Skype"){
// 	emptyInput('im1');
// 	changeInputType("text","im1");
// 	requiredCheck(im1.val(),'im1',IM1_LABEL);
// 	currentIm1typeVal = "Skype";
// 	im1.focus();

// }else if($(this).val() === ""){

//     if(currentPreferredMode == "im"){
//     removeErrorOnField('mode_of_contact');
//     removeErrorOnField('email');
//     removeErrorOnField('phone');
//     removeErrorOnField('mobile');
//     removeErrorItem('imtype1');
//     removeErrorItem('mode_of_contact');
//     removeErrorItem('mobile');
//     validateThruAjax(modeOfContact.val(),'mode_of_contact',PREFERRED_MODE_OF_CONTACT);
//  	requiredCheck(imtype1.val() ,'imtype1',IMTYPE1_LABEL);
//  	requiredCheck(im1.val(),'im1',IM1_LABEL);

//     emptyInput('im1');
//     removeErrorItem('phone');
//     removeErrorItem('mobile');
//     imtype2.prop('disabled',true);
//     removeErrorOnField('im2');
//     im2.prop('disabled',true)
//     im1.attr('readonly', false);
//     im1.focus();
//     }else{
//     	 emptyInput('im1');
//     	 removeErrorOnField('im1');
//     	 removeErrorItem('im1');
//     }

// }else{
// 	emptyInput('im1');
// 	changeInputType("text","im1");
// 	im1.focus();
// }

// });

im1.blur(function(){

	// if(currentIm1typeVal === "Skype"){
	// 	if(requiredCheck(imtype1.val(),'imtype1',IMTYPE1_LABEL)){
	// 	     if(requiredCheck($(this).val(),'im1',IM1_LABEL)){
	// 	       // checkInputIfChineseChar($(this).val(),'im1',IM1_LABEL);
	// 	       removeErrorOnField('mode_of_contact');
	// 	       removeErrorItem('mode_of_contact');
	// 	       imtype2.prop('disabled',false);
	// 	      }
	//     }
	// }else{
	// 	if(requiredCheck(imtype1.val(),'imtype1',IMTYPE1_LABEL)){
	// 	     if(requiredCheck($(this).val(),'im1',IM1_LABEL)){
	// 	       removeErrorOnField('mode_of_contact');
	// 	       removeErrorItem('mode_of_contact');
	// 	        imtype2.prop('disabled',false);
	// 	      }else{
	// 	      	imtype2.prop('disabled',true);
	// 	      }
	//     }
	// }


});

// imtype2.change(function(){

// if($(this).val() ==="QQ"){
// 	emptyInput('im2');
// 	changeInputType("number","im2");
// 	requiredCheck(im2.val(),'im2',IM2_LABEL);
// 	currentIm2typeVal = "QQ";
// 	im2.prop('disabled',false);
// 	im2.focus();
// }else if($(this).val() === "Skype"){
// 	emptyInput('im2');
// 	changeInputType("text","im2");
// 	requiredCheck(im2.val(),'im2',IM2_LABEL);
// 	currentIm2typeVal = "Skype";
// 	im2.prop('disabled',false);
// 	im2.focus();

// }else if($(this).val() === "MSN"){ alert()
// 	emptyInput('im2');
// 	changeInputType("text","im2");
// 	requiredCheck(im2.val(),'im2',IM2_LABEL);
// 	currentIm2typeVal = "MSN";
// 	im2.prop('disabled',false);
// 	im2.focus();

// }else if($(this).val() === ""){
//     removeErrorOnField('im2');
//     removeErrorOnField('mode_of_contact');
//     removeErrorItem('im2');
//     emptyInput('im2');
//     im2.prop('disabled',false);
// }else{
// 	changeInputType("text","im2");
// 	im2.prop('disabled',false);
// 	im2.focus();
// }

// });

im2.blur(function(){

	// if(currentIm2typeVal === "Skype"){

	// 		if(requiredCheck($(this).val(),'im2',IM2_LABEL)){
	// 			// checkInputIfChineseChar($(this).val(),'im2',IM2_LABEL);
	// 		}


	// }else{
	// 	if(!isDisabled('im2')){
	// 	requiredCheck($(this).val(),'im2',IM2_LABEL);
	//  }
	// }



});



 // modeOfContact.change(function(){

 // 	if($(this).val() ==  "phone"){
	//  	   validateThruAjax($(this).val(),'mode_of_contact',PREFERRED_MODE_OF_CONTACT);

	//  	   if(!requiredCheck(phone.val(),'phone',PHONE_LABEL)){
	// 	 	 	phone.focus();
	// 	 	}else{
	// 	 	    removeErrorOnField('mode_of_contact');
	// 	 	}


	//  	   removeErrorOnField('mobile');
	//  	   removeErrorOnField('im');
	//  	   removeErrorOnField('imtype1');
	// 	   removeErrorItem('mobile');
	// 	   currentPreferredMode='phone';
	// 	   phone.focus();


	//  }
	//  if($(this).val() ==  "mobile"){
	//  	 validateThruAjax($(this).val(),'mode_of_contact',PREFERRED_MODE_OF_CONTACT);

	//  	 if(!requiredCheck(mobile.val(),'mobile',MOBILE_PHONE_LABEL)) {
	//  	 	mobile.focus();
	//  	 }else{
	//  	 	removeErrorOnField('mode_of_contact');
	//  	 }
	//  	  removeErrorOnField('phone');
	//  	  removeErrorOnField('im');
	//  	  removeErrorOnField('imtype1');
	// 	  removeErrorOnField('mode_of_contact');
	// 	  removeErrorItem('phone');
	// 	  removeErrorItem('mobile');
	//  	  removeErrorItem('imtype1');
	//  	  removeErrorItem('im1');
	//  	  currentPreferredMode='mobile';
	//  	  mobile.focus();
 //     }

 //      if($(this).val() ==  "im"){
 //      	removeErrorOnField('mode_of_contact');
 //     	  if(requiredCheck(imtype1.val() ,'imtype1',IMTYPE1_LABEL)){
	//  	  	if(requiredCheck(im1.val(),'im1',IM1_LABEL)){
	//  	  		removeErrorOnField('mode_of_contact');
	//  	  		removeErrorItem('mode_of_contact');
	//  	  		removeErrorOnField('mobile')
	//  	  		im1.focus();
	//  	  	}
	//  	  }else{
	//  	  	imtype1.focus();
	//  	  }


	//  	  removeErrorOnField('phone');
	//  	  removeErrorOnField('mobile');
	//  	  removeErrorItem('phone');
	//  	  removeErrorItem('mobile');
	//  	  currentPreferredMode='im';
	//  	  //imtype1.focus();

 //     }
 //     	if($(this).val() ==  "email"){

 //         if(!requiredCheck(email.val() ,'email',EMAIL_LABEL)){
 //         	email.focus();
 //         }
	//  	   removeErrorOnField('mode_of_contact');
	//  	   removeErrorOnField('phone');
	//  	   removeErrorOnField('mobile');
	//  	   removeErrorOnField('im');
	//  	   removeErrorOnField('imtype1');
	// 	   removeErrorItem('mobile');
	// 	   currentPreferredMode='email';


	//  }
	//  // if($(this).val() !=  "im"){
	//  // 	 removeErrorOnField('im');
	//  // 	 removeErrorItem('im1');
	//  // }


 // });



/*
submit.on('click',function(){

	requiredCheck(confirmPassword.val(),'confirm_password',CONFIRM_PASSWORD_LABEL);
	requiredCheck(password.val(),'password',PASSWORD_LABEL);


	if(requiredCheck(email.val(),'email',EMAIL_LABEL)){
		validateThruAjax(email.val(),'email',EMAIL_LABEL)
    }
    if(requiredCheck(username.val(),'username',USERNAME_LABEL)){
		validateThruAjax(username.val(),'username',USERNAME_LABEL)
    }

    submitForm();

});
 */



function submitForm(){

emailOk=emailOk && email.val()!='';
usernameOk=usernameOk && username.val()!='';
passwordOk=password.val()!='' &&  confirmPassword.val()!='';

if(emailOk && passwordOk && usernameOk){


	var errorLength = error.length;

	if(errorLength > 0){
		ableSubmitButton();
		return false;
	}else{

		affRegisterForm.submit();
		disableSubmitButton();

	}

}else{
	return false;
}


}

function validateThruAjax(fieldVal,id,label){
	var data=null;
	if(id == "username"){
		data = {username:fieldVal};
	}
	if(id =="email"){
		data ={ email:fieldVal};
	}
	if(id =="im"){
		data ={ mobile:mobile.val()};
	}

if(data){

	$.ajax({
        url : IS_USERNAME_EXIST_URL,
        type : 'POST',
        data : data,
        dataType : "json",
        cache : false,
      }).done(function (data) {
      	utils.safelog(id);
      	utils.safelog(data);
        if (data.status == "success") {
        	removeErrorItem(id);
   	    	removeErrorOnField(id);
   	    	if(id == 'email'){
   	    		emailOk = true;
   	    	}
   	    	if(id == 'username'){
   	    		usernameOk = true;
   	    	}
        }
        if (data.status == "error") {
        	var message = data.msg;
        	showErrorOnField(id,message);
		    addErrorItem(id);
   	    	if(id == 'email'){
   	    		emailOk = false;
   	    	}
   	    	if(id == 'username'){
   	    		usernameOk = false;
   	    	}
        }
      }).fail(function (jqXHR, textStatus) {
        /*Note: this is for session timeout,if the session is out because this is ajax, eventually it will go to log in page*/
         // location.reload();
      });
	}
}

function isDisabled(element) {

	var isDisabled = $('#'+element).is(':disabled');
	if (isDisabled) {
		return false;
	} else {
		return true;
	}

}


function emptyInput(id){
	$("#"+id).val("");
}
function changeInputType(type,id){
	$("#"+id).attr('type',type);
}

function checkPassword(fieldVal,id,label){
   var message = label+"  field must be at least 6 - 12 characters in length.",
   fieldValLength = fieldVal.length;

   if( (fieldValLength >= 6)  && (fieldValLength <= 12)){
   		removeErrorItem(id);
   		removeErrorOnField(id);
		return true;
   }else{
   		showErrorOnField(id,message);
		addErrorItem(id);
		return false;
   }

}

function checkPasswordMatch(fieldVal,id,label){
	 var message = label+"  didn't match";
	 if(fieldVal != password.val() ){
	 	showErrorOnField(id,message);
		addErrorItem(id);
		return false;
	}else{
		removeErrorItem(id);
   		removeErrorOnField(id);
		return true;
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
	// console.log(error)
 }

 function addErrorItem(item){
 	if(jQuery.inArray(item, error) == -1){
 			error.push(item);
 			// console.log(error);
 			// console.log(error.length)
 	}

 }

function disableSubmitButton(){
	$("#affRegisterForm :input").attr("disabled", true);
	submit.prop('disabled', true);
	cancel.prop('disabled', true);
}
function ableSubmitButton(){
	cancel.prop('disabled', false);
}



})//End document


</script>

<?php
$birthdayVisible = $this->affiliate_manager->checkRegisteredFieldsIfVisible('Birthday') == 0;
$birthdayRequired = $this->affiliate_manager->checkRegisteredFieldsIfRequired('Birthday') == 0;
?>
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
	.panel.panel-primary{
		box-shadow: #ddd 0 0 6px;
		background-color: #f7f7f7;
	}
	.panel{
		background-color: #f7f7f7;
		border-color: transparent;
	}
	.panel-heading{
		background-color: #434343 !important;
	}
	.hvr-bounce-out {
	  /*color:#2e73ff;
	  background-color: transparent;*/
	  background-color: #edd051;
	  border-color: transparent;
	  display: inline-block;
	  vertical-align: middle;
	  -webkit-transform: perspective(1px) translateZ(0);
	  transform: perspective(0.5px) translateZ(0);
	  box-shadow: 0 0 1px transparent;
	  -webkit-transition-duration: 0.5s;
	  transition-duration: 0.5s;
	}
	.hvr-bounce-out:hover, .hvr-bounce-out:focus, .hvr-bounce-out:active {
	  background-color: #edd051;
	  border-color: transparent;
	  -webkit-transform: scale(0.8);
	  transform: scale(1.1);
	  -webkit-transition-timing-function: cubic-bezier(0.47, 2.02, 0.31, -0.36);
	  transition-timing-function: cubic-bezier(0.47, 2.02, 0.31, -0.36);
	}
	#cancel {
	  display: inline-block;
	  vertical-align: middle;
	  -webkit-transform: perspective(1px) translateZ(0);
	  transform: perspective(0.5px) translateZ(0);
	  box-shadow: 0 0 1px transparent;
	  -webkit-transition-duration: 0.5s;
	  transition-duration: 0.5s;
	}
	#cancel:hover, #cancel:focus, #cancel:active {
	  -webkit-transform: scale(0.8);
	  transform: scale(1.1);
	  -webkit-transition-timing-function: cubic-bezier(0.47, 2.02, 0.31, -0.36);
	  transition-timing-function: cubic-bezier(0.47, 2.02, 0.31, -0.36);
	}
	.form-control:hover, .form-control:active, .form-control:focus{
		/*border-color:#A8D8E8;*/
		border-color:#ecf0f1;
	}
</style>

<div class="container">
	<br/>
	<div class="row">
		<div class="col-md-12" id="toggleView">
		<form method="POST" id="affiliate-register-form" action="<?=site_url('affiliate/verifyRegister') . '/' . $trackingCode?>" accept-charset="utf-8">
			<input type="hidden" name="parentId" value="<?=$parentId;?>">
			<div class="panel panel-primary ">
				<div class="panel-heading">
					<h4 class="panel-title pull-left"><i class="glyphicon glyphicon-list-alt"></i> <?=lang('reg.a01');?> </h4>
					<div class="pull-right"><?=lang('reg.02');?></div>
					<div class="clearfix"></div>
				</div>

				<div class="panel panel-body" id="affiliate_panel_body">
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

						<div class="col-md-3 fields">
							<label for="email"><font style="color:red;">*</font> <?=lang('reg.a17');?></label>

							<input type="text" name="email" id="email" class="form-control" value="<?=set_value('email');?>" data-toggle="tooltip" title="<?=lang('reg.a18');?>">
							<span class="errors"><?php echo form_error('email'); ?></span>
							<span id="error-email" class="errors"></span>
						</div>

						<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('First Name') == 0) {
	?>
							<div class="col-md-3 fields">
								<label for="firstname">
									<?php if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('First Name') == 0) {?>
										<font style="color:red;">*</font>
									<?php }
	?>
									<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('Last Name') == 0): ?>
										<?=lang('reg.a09');?>
									<?php else: ?>
										<?=lang('reg_name');?>
									<?php endif ?>
								</label>

								<input type="text" name="firstname" id="firstname" class="form-control" value="<?=set_value('firstname');?>">
								<span class="errors"><?php echo form_error('firstname'); ?></span>
							</div>
						<?php }
?>

						<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('Last Name') == 0) {
	?>
							<div class="col-md-3 fields">
								<label for="lastname">
									<?php if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Last Name') == 0) {?>
										<font style="color:red;">*</font>
									<?php }
	?>

									<?=lang('reg.a10');?>
								</label>

								<input type="text" name="lastname" id="lastname" class="form-control" value="<?=set_value('lastname');?>">
								<span class="errors"><?php echo form_error('lastname'); ?></span>
							</div>
						<?php }
?>

						<?php if ($birthdayVisible) {
	?>
							<div class="col-md-3 fields">
								<label for="birthday">
									<?php if ($birthdayRequired) {?>
										<font style="color:red;">*</font>
									<?php }
	?>

									<?=lang('reg.a11');?>
								</label>

	                            <input type="text" name="birthday" id="birthday" class="form-control dateInput" value="<?=set_value('birthday');?>">
								<span class="errors"><?php echo form_error('birthday'); ?></span>
								<span id="error-birthday" class="errors"></span>
							</div>
						<?php }
?>

						<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('Gender') == 0) {
	?>
							<div class="col-md-3 fields">
								<label for="gender">
									<?php if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Gender') == 0) {?>
										<font style="color:red;">*</font>
									<?php }
	?>

									<?=lang('reg.a12');?>
								</label>

								<div class="form-group">
									<input type="radio" name="gender" id="male" value="Male" <?=(set_value('gender') == 'Male') ? 'checked' : ''?>> <?=lang('reg.a13');?> &nbsp;&nbsp;&nbsp;
									<input type="radio" name="gender" id="female" value="Female" <?=(set_value('gender') == 'Female') ? 'checked' : ''?>> <?=lang('reg.a14');?>
								</div>
								<span class="errors"><?php echo form_error('gender'); ?></span>
							</div>
						<?php }
?>

						<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('Company') == 0) {
	?>
							<div class="col-md-3 fields">
								<label for="company">
									<?php if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Company') == 0) {?>
										<font style="color:red;">*</font>
									<?php }
	?>

									<?=lang('reg.a15');?>
								</label>

								<input type="text" name="company" id="company" class="form-control" value="<?=set_value('company');?>">
								<span class="errors"><?php echo form_error('company'); ?></span>
							</div>
						<?php }
?>

						<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('Occupation') == 0) {
	?>
							<div class="col-md-3 fields">
								<label for="occupation">
									<?php if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Occupation') == 0) {?>
										<font style="color:red;">*</font>
									<?php }
	?>

									<?=lang('reg.a16');?>
								</label>

								<input type="text" name="occupation" id="occupation" class="form-control" value="<?=set_value('occupation');?>">
								<span class="errors"><?php echo form_error('occupation'); ?></span>
							</div>
						<?php }
?>

						<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('Mobile Phone') == 0) {
	?>
							<div class="col-md-3 fields">
								<label for="mobile">
									<?php if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Mobile Phone') == 0) {?>
										<font style="color:red;">*</font>
									<?php }
	?>

									<?=lang('reg.a24');?>
								</label>

								<input type="text" name="mobile" id="mobile" maxlength="50" class="form-control number_only" value="<?=set_value('mobile');?>">
								<span class="errors"><?php echo form_error('mobile'); ?></span>
								<span id="error-mobile"class="errors"></span>
							</div>
						<?php }
?>

						<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('Phone') == 0) {
	?>
							<div class="col-md-3 fields">
								<label for="phone">
									<?php if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Phone') == 0) {?>
										<font style="color:red;">*</font>
									<?php }
	?>

									<?=lang('reg.a25');?>
								</label>

								<input type="text" name="phone" id="phone" maxlength="50" class="form-control number_only" value="<?=set_value('phone');?>">
								<span class="errors"><?php echo form_error('phone'); ?></span>
								<span id="error-phone"class="errors"></span>
							</div>
						<?php }
?>

						<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('City') == 0) {
	?>
							<div class="col-md-3 fields">
								<label for="city">
									<?php if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('City') == 0) {?>
										<font style="color:red;">*</font>
									<?php }
	?>

									<?=lang('reg.a19');?>
								</label>

								<input type="text" name="city" id="city" class="form-control" value="<?=set_value('city');?>">
								<span class="errors"><?php echo form_error('city'); ?></span>
							</div>
						<?php }
?>

						<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('Address') == 0) {
	?>
							<div class="col-md-3 fields">
								<label for="address">
									<?php if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Address') == 0) {?>
										<font style="color:red;">*</font>
									<?php }
	?>

									<?=lang('reg.a20');?>
								</label>

								<input type="text" name="address" id="address" class="form-control" value="<?=set_value('address');?>">
								<span class="errors"><?php echo form_error('address'); ?></span>
							</div>
						<?php }
?>

						<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('Zip Code') == 0) {
	?>
							<div class="col-md-3 fields">
								<label for="zip">
									<?php if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Zip Code') == 0) {?>
										<font style="color:red;">*</font>
									<?php }
	?>

									<?=lang('reg.a21');?>
								</label>

								<input type="text" name="zip" id="zip" class="form-control number_only" value="<?=set_value('zip');?>">
								<span class="errors"><?php echo form_error('zip'); ?></span>
							</div>
						<?php }
?>

						<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('State') == 0) {
	?>
							<div class="col-md-3 fields">
								<label for="state">
									<?php if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('State') == 0) {?>
										<font style="color:red;">*</font>
									<?php }
	?>

									<?=lang('reg.a22');?>
								</label>

								<input type="text" name="state" id="state" class="form-control" value="<?=set_value('state');?>">
								<span class="errors"><?php echo form_error('state'); ?></span>
							</div>
						<?php }
?>

						<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('Country') == 0) {
	?>
							<div class="col-md-3 fields">
								<label for="country">
									<?php if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Country') == 0) {?>
										<font style="color:red;">*</font>
									<?php }
	?>

									<?=lang('reg.a23');?>
								</label>
<!--
								<?php $country = set_value('country');?> -->
								<?php $selected_country = "China"?>
								<select name="country" id="country" class="form-control">
									<option value=""><?=lang('reg.a42');?></option>
									<?php foreach (unserialize(COUNTRY_LIST) as $key) {?>

		                                <option value="<?=$key?>" <?=($selected_country == $key) ? 'selected' : ''?>><?=lang('country.' . $key)?></option>

		                            <?php }
	?>
								</select>
								<span class="errors"><?php echo form_error('country'); ?></span>
							</div>
						<?php }
?>
<?php /*
						<div class="col-md-3 fields">
							<label for="mode_of_contact"><font style="color:red;">*</font> <?=lang('reg.a36');?></label>

							<?php $mode_of_contact = set_value('mode_of_contact');?>
							<select name="mode_of_contact" id="mode_of_contact" class="form-control" data-toggle="tooltip" title="<?=lang('reg.a48');?>">
								<!-- <option value=""><?=lang('reg.a45');?></option> -->
								<option value="email" <?=($mode_of_contact == "email") ? "selected" : ""?> ><?=lang('reg.a37');?></option>

								<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('Phone') == 0) {?>
									<option value="phone" <?=($mode_of_contact == "phone") ? "selected" : ""?> ><?=lang('reg.a38');?></option>
								<?php }
?>

								<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('Mobile Phone') == 0) {?>
									<option value="mobile" <?=($mode_of_contact == "mobile") ? "selected" : ""?> ><?=lang('reg.a39');?></option>
								<?php }
?>

								<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('Instant Message 1') == 0 || $this->affiliate_manager->checkRegisteredFieldsIfVisible('Instant Message 2') == 0) {?>
									<option value="im" <?=($mode_of_contact == "im") ? "selected" : ""?> ><?=lang('reg.a40');?></option>
								<?php }
?>
							</select>
							<span class="errors"><?php echo form_error('mode_of_contact'); ?></span>
							<span id ="error-mode_of_contact"class="errors"></span>
						</div>
*/ ?>

						<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('Website') == 0) {
	?>
							<div class="col-md-3 fields">
								<label for="website">
									<?php if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Website') == 0) {?>
										<font style="color:red;">*</font>
									<?php }
	?>

									<?=lang('reg.a41');?>
								</label>

								<input type="text" name="website" id="website" class="form-control" value="<?=set_value('website');?>" data-toggle="tooltip" title="<?=lang('reg.a47');?>">
								<span class="errors"><?php echo form_error('website'); ?></span>
							</div>
						<?php }
?>

						<!-- <div class="col-md-3 col-md-offset-0">
							<label for="tracking_code"><?=lang('reg.a51');?> </label>
							<input type="text" name="tracking_code" id="tracking_code" class="form-control" value="<?=set_value('tracking_code');?>" data-toggle="tooltip" title="<?=lang('reg.a52');?>">
							<label style="color: red; font-size: 12px;"><?php echo form_error('tracking_code'); ?></label>
						</div> -->

						<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('Instant Message 1') == 0) {
	?>
							<div class="col-md-3 fields">
								<label for="imtype1">
									<?php if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Instant Message 1') == 0) {?>
										<font style="color:red;">*</font>
									<?php }
	?>

									<?=lang('reg.a26');?>
								</label>

								<select name="imtype1" id="imtype1" class="form-control" data-toggle="tooltip" title="<?=lang('reg.a50');?>" onchange="imCheck(this.value, '1');">
									<option value=""><?=lang('reg.a43');?></option>
									<option value="Weixin" <?=(set_value('imtype1') == "Weixin") ? 'selected' : ''?> ><?=lang('Weixin');?></option>
									<option value="QQ" <?=(set_value('imtype1') == "QQ") ? 'selected' : ''?> ><?=lang('reg.a32');?></option>
									<option value="Skype" <?=(set_value('imtype1') == "Skype") ? 'selected' : ''?> ><?=lang('reg.a33');?></option>
									<option value="Other IM" <?=(set_value('imtype1') == "Other IM") ? 'selected' : ''?> ><?=lang('Other IM');?></option>

									<?php /* <option value="MSN"  <?=(set_value('imtype1') == "MSN") ? 'selected' : ''?>><?=lang('reg.a34');?></option> */ ?>
								</select>
								<span class="errors"><?php echo form_error('imtype1'); ?></span>
								<span id="error-imtype1" class="errors"></span>
							</div>

							<div class="col-md-3 fields">
								<label for="imtype1">
									<?php if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Instant Message 1') == 0) {?>
										<font style="color:red;">*</font>
									<?php }
	?>

									<?=lang('reg.a30');?>
								</label>

								<input type="text" name="im1" id="im1" class="form-control" value="<?=set_value('im1');?>" <?=(set_value('imtype1') == null) ? 'readonly' : ''?> >
								<span class="errors"><?php echo form_error('im1'); ?></span>
								<span id="error-im1" class="errors"></span>
							</div>
						<?php }
?>

						<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('Instant Message 2') == 0) {
	?>
							<div class="col-md-3 fields">
								<label for="imtype2">
									<?php if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Instant Message 2') == 0) {?>
										<font style="color:red;">*</font>
									<?php }
	?>

									<?=lang('reg.a31');?>
								</label>

								<select name="imtype2" id="imtype2" class="form-control" data-toggle="tooltip" title="<?=lang('reg.a49');?>" onchange="imCheck(this.value, '2');">
									<option value=""><?=lang('reg.a44');?></option>
									<option value="Weixin" <?=(set_value('imtype2') == "Weixin") ? 'selected' : ''?> ><?=lang('Weixin');?></option>
									<option value="QQ" <?=(set_value('imtype2') == "QQ") ? 'selected' : ''?> ><?=lang('reg.a32');?></option>
									<option value="Skype" <?=(set_value('imtype2') == "Skype") ? 'selected' : ''?> ><?=lang('reg.a33');?></option>
									<option value="Other IM" <?=(set_value('imtype2') == "Other IM") ? 'selected' : ''?> ><?=lang('Other IM');?></option>

									<?php /* <option value="MSN"  <?=(set_value('imtype2') == "MSN") ? 'selected' : ''?>><?=lang('reg.a34');?></option> */ ?>
								</select>
								<span class="errors"><?php echo form_error('imtype2'); ?></span>
							</div>

							<div class="col-md-3 fields">
								<label for="im2">
									<?php if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Instant Message 2') == 0) {?>
										<font style="color:red;">*</font>
									<?php }
	?>

									<?=lang('reg.a35');?>
								</label>

								<input type="text" name="im2" id="im2" class="form-control" maxlength="50"  value="<?=set_value('im2');?>" <?=(set_value('imtype2') == null) ? 'readonly' : ''?> >
								<span class="errors"><?php echo form_error('im2'); ?></span>
								<span id="error-im2" class="errors"></span>
							</div>
						<?php }
?>

						<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('Language') == 0) {
	?>
							<div class="col-md-3 fields">
								<label for="languge">
									<?php if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Language') == 0) {?>
										<font style="color:red;">*</font>
									<?php }
	?>

									<?=lang('ban.lang');?>
								</label>

								<select class="form-control" name="language" id="language">
						        	<option value="English"<?php echo ($this->session->userdata('afflang') == '1') ? ' selected="selected"' : ''; ?>>English</option>
						        	<option value="Chinese"<?php echo ($this->session->userdata('afflang') == '2') ? ' selected="selected"' : ''; ?>>中文</option>
						        	<option value="Indonesian"<?php echo ($this->session->userdata('afflang') == '3') ? ' selected="selected"' : ''; ?>>Indonesian</option>
						        	<option value="Vietnamese"<?php echo ($this->session->userdata('afflang') == '4') ? ' selected="selected"' : ''; ?>>Vietnamese</option>
						        </select>
						        <span class="errors"><?php echo form_error('languge'); ?></span>
							</div>
						<?php }
?>

						<input type="hidden" name="currency" id="currency" value="<?=$curren?>" />
					</div>
					<!-- End of Content Info -->
				</div>
			</div>
</form>
			<center>
				<input type="button" name="submit" id="submit" class="hvr-bounce-out btn btn-info custom-btn-size" value="<?=lang('reg.a46');?>">
				<button id="cancel" link="<?=site_url('affiliate')?>" class="btn btn-default custom-btn-size"><?=lang('lang.cancel');?></button>
			</center>

		</div>

	</div>
</div>

<script>

$(document).ready(function(){


	$('#imtype2').tooltip({
		placement : "top"

	});
	$('#imtype1').tooltip({
		placement : "top"

	});
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
	$('#mode_of_contact').tooltip({
		placement : "top"

	});






var  IS_USERNAME_EXIST_URL = '<?php echo site_url('affiliate/validateThruAjax') ?>',
      username = $("#username"),
      password = $("#password"),
      confirmPassword = $('#confirm_password'),
      email = $('#email'),
      birthday = $('#birthday'),
      birthdayCal = $('.calendar-table'),
      modeOfContact =$('#mode_of_contact'),
      phone =$('#phone'),
      mobile =$('#mobile'),
      imtype1 =$('#imtype1'),
      imtype2 =$('#imtype2'),
      im1 = $('#im1'),
      im2 = $('#im2'),
      submit = $('#submit'),
      cancel = $('#cancel'),
      affRegisterForm =$('#affiliate-register-form'),
      USERNAME_LABEL = "<?=lang('reg.03')?>",
      PASSWORD_LABEL = "<?=lang('reg.05')?>",
      CONFIRM_PASSWORD_LABEL = "<?=lang('reg.07')?>",
      EMAIL_LABEL = "<?=lang('aff.al11')?>",
      BIRTHDAY_LABEL = "<?=lang('aff.ai04')?>",
      PREFERRED_MODE_OF_CONTACT = "<?='aff.ai20'?>",
      PHONE_LABEL ="<?=lang('aff.ai15')?>",
      MOBILE_PHONE_LABEL ="<?=lang('aff.ai14')?>",
      IMTYPE1_LABEL ="<?=lang('aff.ai16')?>",
      IMTYPE2_LABEL ="<?=lang('aff.ai18')?>",
      IM1_LABEL ="<?=lang('aff.ai17')?>",
      IM2_LABEL ="<?=lang('aff.ai19')?>",
      formOk =false,
      birthdayOk =false;
      emailOk = false;
      usernameOk = false;
      currentPreferredMode='',
      currentIm1typeVal ='',
      currentIm2typeVal ='',
      error =[];


imtype2.prop('disabled', true);
 birthday .val("");


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
phone.blur(function(){
	if(currentPreferredMode == 'phone'){
     if(requiredCheck($(this).val(),'phone',PHONE_LABEL)){
     	 removeErrorOnField('mode_of_contact');
     	 removeErrorItem('mode_of_contact');
     }

	}

});
// if preferred mode active in selecting then remove error if mobile is not blank
mobile.blur(function(){
	if(currentPreferredMode == 'mobile'){
     if(requiredCheck($(this).val(),'mobile',MOBILE_PHONE_LABEL)){
     	removeErrorOnField('mode_of_contact');
     	removeErrorItem('mode_of_contact');
         removeErrorItem('mobile');
     }

	}

});



imtype1.change(function(){

if($(this).val() ==="QQ"){
	emptyInput('im1');
	changeInputType("number","im1");
	requiredCheck(im1.val(),'im1',IM1_LABEL);

	currentIm1typeVal = "QQ";
	im1.focus();
}else if($(this).val() === "Skype" || $(this).val() === "Weixin"){
	emptyInput('im1');
	changeInputType("text","im1");
	requiredCheck(im1.val(),'im1',IM1_LABEL);
	currentIm1typeVal = $(this).val();
	im1.focus();

}else if($(this).val() === ""){

    if(currentPreferredMode == "im"){
    removeErrorOnField('mode_of_contact');
    removeErrorOnField('email');
    removeErrorOnField('phone');
    removeErrorOnField('mobile');
    removeErrorItem('imtype1');
    removeErrorItem('mode_of_contact');
    removeErrorItem('mobile');
    // validateThruAjax(modeOfContact.val(),'mode_of_contact',PREFERRED_MODE_OF_CONTACT);
 	requiredCheck(imtype1.val() ,'imtype1',IMTYPE1_LABEL);
 	requiredCheck(im1.val(),'im1',IM1_LABEL);

    emptyInput('im1');
    removeErrorItem('phone');
    removeErrorItem('mobile');
    imtype2.prop('disabled',true);
    removeErrorOnField('im2');
    im2.prop('disabled',true)
    im1.attr('readonly', false);
    im1.focus();
    }else{
    	 emptyInput('im1');
    	 removeErrorOnField('im1');
    	 removeErrorItem('im1');
    }

}else{
	emptyInput('im1');
	changeInputType("text","im1");
	im1.focus();
}

});

im1.blur(function(){

	if(currentIm1typeVal === "Skype" || currentIm1typeVal === "Weixin"){
		if(requiredCheck(imtype1.val(),'imtype1',IMTYPE1_LABEL)){
		     if(requiredCheck($(this).val(),'im1',IM1_LABEL)){
		       // checkInputIfChineseChar($(this).val(),'im1',IM1_LABEL);
		       removeErrorOnField('mode_of_contact');
		       removeErrorItem('mode_of_contact');
		       imtype2.prop('disabled',false);
		      }
	    }
	}else{
		if(requiredCheck(imtype1.val(),'imtype1',IMTYPE1_LABEL)){
		     if(requiredCheck($(this).val(),'im1',IM1_LABEL)){
		       removeErrorOnField('mode_of_contact');
		       removeErrorItem('mode_of_contact');
		        imtype2.prop('disabled',false);
		      }else{
		      	imtype2.prop('disabled',true);
		      }
	    }
	}


});

imtype2.change(function(){

if($(this).val() ==="QQ"){
	emptyInput('im2');
	changeInputType("number","im2");
	requiredCheck(im2.val(),'im2',IM2_LABEL);
	currentIm2typeVal = "QQ";
	im2.prop('disabled',false);
	im2.focus();
}else if($(this).val() === "Skype" || $(this).val() === "Weixin"){
	emptyInput('im2');
	changeInputType("text","im2");
	requiredCheck(im2.val(),'im2',IM2_LABEL);
	currentIm2typeVal = $(this).val();
	im2.prop('disabled',false);
	im2.focus();

}else if($(this).val() === "MSN"){ alert()
	emptyInput('im2');
	changeInputType("text","im2");
	requiredCheck(im2.val(),'im2',IM2_LABEL);
	currentIm2typeVal = "MSN";
	im2.prop('disabled',false);
	im2.focus();

}else if($(this).val() === ""){
    removeErrorOnField('im2');
    removeErrorOnField('mode_of_contact');
    removeErrorItem('im2');
    emptyInput('im2');
    im2.prop('disabled',false);
}else{
	changeInputType("text","im2");
	im2.prop('disabled',false);
	im2.focus();
}

});

im2.blur(function(){

	if(currentIm2typeVal === "Skype" || currentIm2typeVal === "Weixin"){

			if(requiredCheck($(this).val(),'im2',IM2_LABEL)){
				// checkInputIfChineseChar($(this).val(),'im2',IM2_LABEL);
			}


	}else{
		if(!isDisabled('im2')){
		requiredCheck($(this).val(),'im2',IM2_LABEL);
	 }
	}



});



<?php if ($birthdayRequired) {?>
birthday.blur(function() {
     if (requiredCheck(birthday.val(),'birthday',BIRTHDAY_LABEL)){
      validateThruAjax(birthday.val(),'birthday',BIRTHDAY_LABEL);
     }
 });

birthday.on('apply.daterangepicker', function(ev, picker) {
     if (requiredCheck(birthday.val(),'birthday',BIRTHDAY_LABEL)){
      validateThruAjax(birthday.val(),'birthday',BIRTHDAY_LABEL);
     }
 });
<?php }?>

 modeOfContact.change(function(){

 	if($(this).val() ==  "phone"){
	 	   validateThruAjax($(this).val(),'mode_of_contact',PREFERRED_MODE_OF_CONTACT);

	 	   if(!requiredCheck(phone.val(),'phone',PHONE_LABEL)){
		 	 	phone.focus();
		 	}else{
		 	    removeErrorOnField('mode_of_contact');
		 	}


	 	   removeErrorOnField('mobile');
	 	   removeErrorOnField('im');
	 	   removeErrorOnField('imtype1');
		   removeErrorItem('mobile');
		   currentPreferredMode='phone';
		   phone.focus();


	 }
	 if($(this).val() ==  "mobile"){
	 	 validateThruAjax($(this).val(),'mode_of_contact',PREFERRED_MODE_OF_CONTACT);

	 	 if(!requiredCheck(mobile.val(),'mobile',MOBILE_PHONE_LABEL)) {
	 	 	mobile.focus();
	 	 }else{
	 	 	removeErrorOnField('mode_of_contact');
	 	 }
	 	  removeErrorOnField('phone');
	 	  removeErrorOnField('im');
	 	  removeErrorOnField('imtype1');
		  removeErrorOnField('mode_of_contact');
		  removeErrorItem('phone');
		  removeErrorItem('mobile');
	 	  removeErrorItem('imtype1');
	 	  removeErrorItem('im1');
	 	  currentPreferredMode='mobile';
	 	  mobile.focus();
     }

      if($(this).val() ==  "im"){
      	removeErrorOnField('mode_of_contact');
     	  if(requiredCheck(imtype1.val() ,'imtype1',IMTYPE1_LABEL)){
	 	  	if(requiredCheck(im1.val(),'im1',IM1_LABEL)){
	 	  		removeErrorOnField('mode_of_contact');
	 	  		removeErrorItem('mode_of_contact');
	 	  		removeErrorOnField('mobile')
	 	  		im1.focus();
	 	  	}
	 	  }else{
	 	  	imtype1.focus();
	 	  }


	 	  removeErrorOnField('phone');
	 	  removeErrorOnField('mobile');
	 	  removeErrorItem('phone');
	 	  removeErrorItem('mobile');
	 	  currentPreferredMode='im';
	 	  //imtype1.focus();

     }
     	if($(this).val() ==  "email"){

         if(!requiredCheck(email.val() ,'email',EMAIL_LABEL)){
         	email.focus();
         }
	 	   removeErrorOnField('mode_of_contact');
	 	   removeErrorOnField('phone');
	 	   removeErrorOnField('mobile');
	 	   removeErrorOnField('im');
	 	   removeErrorOnField('imtype1');
		   removeErrorItem('mobile');
		   currentPreferredMode='email';


	 }
	 // if($(this).val() !=  "im"){
	 // 	 removeErrorOnField('im');
	 // 	 removeErrorItem('im1');
	 // }


 });




submit.on('click',function(){

	requiredCheck(confirmPassword.val(),'confirm_password',CONFIRM_PASSWORD_LABEL);
	requiredCheck(password.val(),'password',PASSWORD_LABEL);


	if(requiredCheck(email.val(),'email',EMAIL_LABEL)){
		validateThruAjax(email.val(),'email',EMAIL_LABEL)
    }
<?php if ($birthdayRequired) {?>
    if(requiredCheck(birthday.val(),'birthday',BIRTHDAY_LABEL)){
		validateThruAjax(birthday.val(),'birthday',BIRTHDAY_LABEL)
    }
<?php }?>
    if(requiredCheck(username.val(),'username',USERNAME_LABEL)){
		validateThruAjax(username.val(),'username',USERNAME_LABEL)
    }

    submitForm();

});



function submitForm(){

emailOk=emailOk && email.val()!='';
usernameOk=usernameOk && username.val()!='';
passwordOk=password.val()!='' &&  confirmPassword.val()!='';

<?php if ($birthdayRequired) {?>
birthdayOk=birthdayOk && birthday.val()!='';
<?php } else {?>
birthdayOk=true;
<?php }?>

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
	if(id =="birthday"){
		data ={ birthday:fieldVal};
	}
	if(id =="mode_of_contact"){
		data ={ mode_of_contact:fieldVal , phone:phone.val(), mobile:mobile.val()};
	}
	if(id =="im"){
		data ={ mode_of_contact:fieldVal , phone:phone.val(), mobile:mobile.val(),imtype1:imtype1.val()};
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
   	    	if(id == 'birthday'){
   	    		birthdayOk = true;
   	    	}
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
		    if(id == 'birthday'){
   	    		birthdayOk = false;
   	    	}
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

function checkInputIfChineseChar(fieldVal,id,label){

var message = "Cannot use Chinese Character. ";

var pattern = /^[a-zA-Z0-9!@#$%^&*()_]+$/;
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

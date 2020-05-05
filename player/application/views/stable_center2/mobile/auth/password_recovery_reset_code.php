<style type="text/css">
     /*---- DEFAULT CSS FOR FORGOT PW SECTION ------*/
     .panel.panel-default.password-recovery.password-recovery-reset {
          position: relative;
          min-height: 1px;
          padding-right: 15px;
          padding-left: 15px;
     }
     .panel-heading {
          border: 0;
     }
     .panel.panel-default.password-recovery.password-recovery-reset input {
          color: #000;
          border: 1px #a4a4a4 solid;
          width: 100%;
          padding: 19px 7px;
          border-radius: 0;
          margin-bottom: 15px !important;
     }
     .panel.panel-default.password-recovery.password-recovery-reset input:focus {
          border-color: #b62127;
          -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(222, 25, 25, 0.6);
          box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(214, 39, 39, 0.6);
     }
     .panel.panel-default.password-recovery.password-recovery-reset button.btn.btn-primary {
          background: #d5d5d5;
          color: #000;
          width: 100%;
          margin-bottom: 15px;
          border-radius: 0;
          border: 0 !important;
          padding: 10px 0;
     }
</style>
<?php if (validation_errors()): ?>
	<div class="alert alert-danger" style="top:0"><?=validation_errors()?></div>
<?php endif?>
<div class="row" style="padding-top:10%; padding-bottom:10%;">
	<div class="col-md-4 col-md-offset-4">
		<div class="panel panel-default password-recovery password-recovery-reset">
			<div class="panel-heading">
				<?=$title?>
			</div>
			<div class="panel-body">
				<form action="/iframe_module/password_recovery_reset_code" method="POST">
					<div class="form-group">
						<input type="hidden" name="title" value="<?=$title?>" />
						<input type="text" id="username" name="username" class="form-control" value="<?=$username?>" readonly/>
						<input type="text" id="resetCode" name="reset_code" class="form-control" placeholder="<?=lang('Verification Code')?>" autocomplete="one-time-code" length="6"/>

                              <input type="password" id="newpassword" name="password" placeholder="<?=lang('New Password')?>" class="form-control" autocomplete="new-password"/>
                              <div class="mb20 hide" id="newpassword-note-field">
                                   <p class="pl15 mb0"><i id="password_len" class="icon-warning red f16 mr5"></i> <?= sprintf(lang('password.validation.lengthRangeStandard'), 6, 20)?></p>
                                   <p class="pl15 mb0"><i id="password_regex" class="icon-warning red f16 mr5"></i> <?=lang('validation.contentPassword04')?></p>
                              </div>
						<input type="password" id="cfnewpassword" name="confirm_password" placeholder="<?=lang('Confirm New Password')?>" class="form-control" autocomplete="new-password"/>
                              <div class="mb20 hide" id="cfnewpassword-note-field">
                                   <p class="pl15 mb0"><i id="password_confirm" class="icon-warning red f16 mr5"></i><?=lang('validation.retypeNewPassword')?></p>
                              </div>
						<button type="submit" id="resetBtn" class="btn btn-primary" disabled><?=lang('Reset Password')?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
     var newPassword = document.getElementById("newpassword");
     var newPasswordField = document.getElementById("newpassword-note-field");
     $(newPassword).focus(function() {
          $(newPasswordField).removeClass('hide');
     });
     $(newPassword).focusout(function() {
          $(newPasswordField).addClass('hide');
     });

     var cfnewPassword = document.getElementById("cfnewpassword");
     var cfnewPasswordField = document.getElementById("cfnewpassword-note-field");
     $(cfnewPassword).focus(function() {
          $(cfnewPasswordField).removeClass('hide');
     });
     $(cfnewPassword).focusout(function() {
          $(cfnewPasswordField).addClass('hide');
     });

     $("input").on('keyup input',function() {
          validateNewPasswordRequirements();
          validateConfirmNewPassword();
          var resetBtn = document.getElementById("resetBtn");
          var resetCode = document.getElementById("resetCode").value;
          var invalidIcon = 'icon-warning red';
          if($("i").hasClass(invalidIcon) || resetCode == ""){
               $(resetBtn).attr('disabled',true);
          }else {
               $(resetBtn).attr('disabled',false);
          }
     });

     function validateNewPasswordRequirements(){
          var newPasswordVal = document.getElementById("newpassword").value;
          var newPasswordLen = newPasswordVal.length;
          var validIcon = 'icon-checked green';
          var invalidIcon = 'icon-warning red';
          var passwordLen = document.getElementById("password_len");
          var passwordRegex = document.getElementById("password_regex");
          var passwordMinLength = 6;
          var passwordMaxLength = 20;
          var reg = new RegExp(/^(?=.*[a-z])(?=.*[0-9])[a-z0-9]+$/);

          if(newPasswordLen >= passwordMinLength && newPasswordLen <= passwordMaxLength){
               $(passwordLen).removeClass(invalidIcon);
               $(passwordLen).addClass(validIcon);
          }else {
               $(passwordLen).addClass(invalidIcon);
               $(passwordLen).removeClass(validIcon);
          }

          if (reg.test(newPasswordVal)){
               $(passwordRegex).removeClass(invalidIcon);
               $(passwordRegex).addClass(validIcon);
          }else {
               $(passwordRegex).addClass(invalidIcon);
               $(passwordRegex).removeClass(validIcon);
          }
     }

     function validateConfirmNewPassword(){
          var newPasswordVal = document.getElementById("newpassword").value;
          var cfNewPasswordVal = document.getElementById("cfnewpassword").value;
          var validIcon = 'icon-checked green';
          var invalidIcon = 'icon-warning red';
          var passwordConfirm = document.getElementById("password_confirm");

          if(newPasswordVal != "" && cfNewPasswordVal != ""){
               if(newPasswordVal == cfNewPasswordVal) {
                    $(passwordConfirm).removeClass(invalidIcon);
                    $(passwordConfirm).addClass(validIcon);
               }
               else {
                    $(passwordConfirm).addClass(invalidIcon);
                    $(passwordConfirm).removeClass(validIcon);
               }
          }
     }
</script>
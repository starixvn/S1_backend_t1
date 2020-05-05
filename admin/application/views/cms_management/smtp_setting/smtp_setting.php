<div class="panel panel-primary">
  <div class="panel-heading custom-ph">
<h4 class="panel-title custom-pt"><i class="icon-cog" id="icon"></i >&nbsp;<?=lang('smtp.setting.title')?></h4>
  </div>
  <div class="panel-body">
    <form action="/cms_management/post_smtp_setting" method="post" class="form-horizontal">
	<div class="form-group">
		<label class="control-label col-sm-3">mail_smtp_server</label>
		<div class="col-sm-4">
			<input name="mail_smtp_server" type="text" class="form-control" value="<?=$mail_smtp_server?>" required="required"/>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-3">mail_smtp_port</label>
		<div class="col-sm-4">
			<input name="mail_smtp_port" type="text" class="form-control" value="<?=$mail_smtp_port?>" required="required"/>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-3">mail_smtp_auth</label>
		<div class="col-sm-4">
			<input name="mail_smtp_auth" type="hidden" value="0"/>
			<input name="mail_smtp_auth" type="checkbox" value="1" <?=$mail_smtp_auth == 0 ? '' : 'checked="checked"'?>/>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-3">mail_smtp_secure</label>
		<div class="col-sm-4">
			<input name="mail_smtp_secure" type="text" class="form-control" value="<?=$mail_smtp_secure?>" required="required"/>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-3">mail_smtp_username</label>
		<div class="col-sm-4">
			<input name="mail_smtp_username" type="text" class="form-control" value="<?=$mail_smtp_username?>" required="required"/>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-3">mail_smtp_password</label>
		<div class="col-sm-4">
			<input name="mail_smtp_password" type="password"  id="mail_smtp_password" class="form-control" required="required"/>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-3">mail_from</label>
		<div class="col-sm-4">
			<input name="mail_from" type="text" class="form-control" value="<?=$mail_from?>" required="required"/>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-3">mail_from_email</label>
		<div class="col-sm-4">
			<input name="mail_from_email" type="text" class="form-control" value="<?=$mail_from_email?>" required="required"/>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-3">disable_smtp_ssl_verify</label>
		<div class="col-sm-4">
			<input name="disable_smtp_ssl_verify" type="hidden" value="0"/>
			<input name="disable_smtp_ssl_verify" type="checkbox" value="1" <?=$disable_smtp_ssl_verify == 0 ? '' : 'checked="checked"'?>/>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-4 form-inline">
			<button type="submit" name="action" value="save" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>"><?=lang('lang.save')?></button>
			<div class="input-group">
				<input type="email" class="form-control" id="email" name="email" value="<?=$email?>" placeholder="<?=$email?>"/>
				<span class="input-group-btn">
					<button type="submit" name="action" value="test" class="btn btn-default" onclick="return sendTest()">Test</button>
				</span>
			</div>
		</div>
	</div>

</form>
  </div>
</div>



<!-- <h1 class="page-header"><?=lang('smtp.setting.title')?></h1> -->


<script type="text/javascript">
	$( document ).ready(function() {
		var oldPassword = "<?php echo $mail_smtp_password; ?>";
		var mail_smtp_password = $("#mail_smtp_password");

		mail_smtp_password.val(oldPassword);
	});
	function sendTest() {
		return $('#email').val() != '';
	}
</script>
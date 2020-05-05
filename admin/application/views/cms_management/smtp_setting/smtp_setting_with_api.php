<style type="text/css" media="screen">
	<style type="text/css">
.onoffswitch {
    position: relative; width: 120px;
    -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;
    margin-right: auto;
    margin-left: auto;
}

.onoffswitch-checkbox {
    display: none;
}

.onoffswitch-label {
    display: block; overflow: hidden; cursor: pointer;
    border: 1px solid #999999; border-radius: 20px;
}

.onoffswitch-inner {
    display: block; width: 200%; margin-left: -100%;
    -moz-transition: margin 0.3s ease-in 0s; -webkit-transition: margin 0.3s ease-in 0s;
    -o-transition: margin 0.3s ease-in 0s; transition: margin 0.3s ease-in 0s;
}

.onoffswitch-inner:before, .onoffswitch-inner:after {
    display: block; float: left; width: 50%; height: 20px; padding: 0; line-height: 20px;
    font-size: 10px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;
    -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box;
}

.onoffswitch-inner:before {
    content: "<?=lang('SMTP API (new)')?>";
    padding-left: 10px;
    background-color: #afb414; color: #FFFFFF;
}

.onoffswitch-inner:after {
    content: "<?=lang('(old) SMTP Settings')?>";
    padding-right: 10px;
    background-color: #43ac6a; color: #FFFFFF;
    text-align: right;
}

.onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-inner {
    margin-left: 0;
}

.onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-switch {
    right: 0px;
}

.onoffswitch-checkbox:disabled + .onoffswitch-label {
    background-color: #ffffff;
    cursor: not-allowed;
}

td {
	padding: 20px;
	max-width: 100px;
	word-wrap: break-word
}

</style>

<div class="panel panel-primary">
  <div class="panel-heading custom-ph">
	<h4 class="panel-title custom-pt">
		<i class="icon-cog" id="icon"></i >&nbsp;<?=lang('smtp.setting.title')?>
		<div class="pull-right">
			<div class="pull-left" style="margin-right: 20px; font-size: 15pt">
				<?=lang('Send emails via: ')?>
			</div>
			<div class="pull-right">

				<div class="pull-left" style="margin-right: 10px;" title="<?=lang('Click to switch modes')?>">
					<div class="onoffswitch">
					    <input type="checkbox" name="use_smtp_api" class="onoffswitch-checkbox" id="use_smtp_api" value="false" <?= $this->utils->getOperatorSetting('use_smtp_api') == 'true' ? 'checked' :''?>>
					    <label class="onoffswitch-label" for="use_smtp_api">
					        <span class="onoffswitch-inner"></span>
					        <span class="onoffswitch-switch"></span>
					    </label>
				    </div>
			    </div>
			</div>
		</div>
	</h4>

  </div>
    <div class="panel-body">

	  	<!-- START Notes based on selected SMTP method -->
	  	<p class="text text-success small" style="display: none" id="smtp_api_enabled_note"><?= lang('smtp_api_enabled_note') ?></p>
	  	<p class="text text-success small" style="display: none" id="default_smtp_api_enabled_note"><?= lang('default_smtp_api_enabled_note') ?></p>
	  	<!-- END Notes based on selected SMTP method -->

	  	<ul class="nav nav-tabs">
	  		<li id="li_old_smtp" class="<?= $this->utils->getOperatorSetting('use_smtp_api') == 'false' ? 'active' :''?>"><a data-toggle="tab" href="#old_smtp"><?= lang('Default SMTP Settings (old)') ?></a></li>
	  		<li id="li_new_smtp" class="<?= $this->utils->getOperatorSetting('use_smtp_api') == 'true' ? 'active' :''?>"><a data-toggle="tab" href="#new_smtp"><?=lang('SMTP API Configurations (new)')?></a></li>
	  	</ul>

	  	<div class="tab-content">
	  		<div id="old_smtp" class="tab-pane fade <?= $this->utils->getOperatorSetting('use_smtp_api') == 'false' ? 'in active' :''?>">
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
			  				<input name="mail_smtp_password" id="mail_smtp_password" type="password" class="form-control"  required="required"/>
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
			  				<button type="submit" name="action" value="save" class="btn btn-primary"><?=lang('lang.save')?></button>
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
	  		<div id="new_smtp" class="tab-pane fade <?= $this->utils->getOperatorSetting('use_smtp_api') == 'true' ? 'in active' :''?>">
	  			<form action="/cms_management/post_smtp_api_configuration" method="post" class="form-horizontal">
	  				<?php
	  				$smtp_api_info			= $this->utils->getConfig('smtp_api_info');
	  				$smtp_api_mail_from_name 	= $this->utils->getOperatorSetting('smtp_api_mail_from_name');
	  				$smtp_api_mail_from_email 	= $this->utils->getOperatorSetting('smtp_api_mail_from_email');

	  				if(!$smtp_api_mail_from_name)
	  					$smtp_api_mail_from_name 	= $this->utils->getOperatorSetting('mail_from');

					if(!$smtp_api_mail_from_email)
	  					$smtp_api_mail_from_email 	= $this->utils->getOperatorSetting('mail_from_email');
	  				 ?>

	  				 	<div class="form-group">
							<label class="control-label col-sm-3"><?=lang('Active API')?></label>
							<div class="col-sm-4">
								<input name="current_smtp_api" type="text" class="form-control" value="<?=lang($this->utils->getConfig('current_smtp_api'))?>" readonly/>
							</div>
						</div>

	  				<?php foreach ($smtp_api_info as $key => $value): ?>
	  					<div class="form-group">
							<label class="control-label col-sm-3"><?=lang($key)?></label>
							<div class="col-sm-4">
								<input name="<?=$key?>" type="text" class="form-control" value="<?=$value?>" readonly/>
							</div>
						</div>
	  				<?php endforeach ?>

			  		<div class="form-group">
			  			<label class="control-label col-sm-3"><?=lang('From Name:')?></label>
			  			<div class="col-sm-4">
			  				<input name="smtp_api_mail_from_name" type="text" class="form-control" value="<?=$smtp_api_mail_from_name?>" required="required"/>
			  			</div>
			  		</div>
			  		<div class="form-group">
			  			<label class="control-label col-sm-3"><?=lang('From Email:')?></label>
			  			<div class="col-sm-4">
			  				<input name="smtp_api_mail_from_email" type="text" class="form-control" value="<?=$smtp_api_mail_from_email?>" required="required"/>
			  			</div>
			  		</div>

			  		<div class="form-group">
			  			<div class="col-sm-offset-3 col-sm-4 form-inline">
			  				<button type="submit" name="action" value="save" class="btn btn-primary"><?=lang('lang.save')?></button>
			  				<div class="input-group">
			  					<input type="email" class="form-control" id="test_api_email_recipient" name="test_api_email_recipient" value="" placeholder=""/>
			  					<span class="input-group-btn">
			  						<button type="submit" name="action" value="test" class="btn btn-default" onclick="return sendTestSmtp()"><?=lang('Test SMTP API')?></button>
			  					</span>
			  				</div>
			  			</div>
			  		</div>

				</form>
	  		</div>
	  	</div>

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

	function sendTestSmtp() {
		return $('#test_api_email_recipient').val() != '';
	}


	function showSmtpApiNote($show){
		if($show == true){
			$('#smtp_api_enabled_note').show();
			$('#default_smtp_api_enabled_note').hide();
			$('#li_old_smtp').removeClass('active');
			$('#li_new_smtp').addClass('active');
			$('#old_smtp').removeClass('in active');
			$('#new_smtp').addClass('in active')
		}
		else
		{
			$('#smtp_api_enabled_note').hide();
			$('#default_smtp_api_enabled_note').show();
			$('#li_old_smtp').addClass('active');
			$('#li_new_smtp').removeClass('active');
			$('#new_smtp').removeClass('in active');
			$('#old_smtp').addClass('in active');
		}
	}

	function changeEmailSendingMethod(use_smtp_api){
		$.ajax({
			url: 'change_enabled_email_sending_method',
			type: "POST",
			dataType:"script",
			data: {
				'use_smtp_api': use_smtp_api,
			},
			success: function(data, status, xhr){
				console.log('',data);
				if(data == true){
					showSmtpApiNote(use_smtp_api);
					alert("<?=lang('sys.gd25')?>");
				}
				else
				{
					alert("<?=lang('sys.gd26')?>");
				}
			},
			error: function(xhr, status, errorThrown){
				console.log(errorThrown);
				alert("<?=lang('sys.gd26')?>");
			}
		});
	}

	$(function(){
		if($('#smtp_api_enabled_note').length > 0 || $('#default_smtp_api_enabled_note').length > 0)
		{
			$('#smtp_api_enabled_note').hide();
			$('#default_smtp_api_enabled_note').hide();

			if($('#use_smtp_api').is(":checked")){
				showSmtpApiNote(true);
			}
			else{
				showSmtpApiNote(false);
			}

			$('#use_smtp_api').change(function(){
				if($(this).is(':checked'))
					changeEmailSendingMethod(true);
				else
					changeEmailSendingMethod(false);

			});
		}
	});
</script>
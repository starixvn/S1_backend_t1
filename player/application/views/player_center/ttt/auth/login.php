<div class="row" style="padding-top:10%;padding-bottom:10%; ">
	<div class="col-md-4 col-md-offset-4">
		<div class="panel panel-primary">
			<div class="panel-heading" style="text-align:left;">
				<label><?=lang('lang.login_title');?></label>
<?php

if (!empty($snackbar)) {
	?>
				<span class='pull-right' style="color:#FF0000">
 <?php

	foreach ($snackbar as $message) {
		echo $message;
	}
	?>
				</span>
<?php
}
?>
			</div>
			<div class="panel-body" style="padding-bottom:0;">
				<form method="POST" action="<?=site_url('iframe/auth/login')?>" role="form">
			<input type="hidden" name="referer" value="<?php echo set_value('referer', isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '') ?>">

					<div class="form-group">

						<div class="input-group">
							<div class="input-group-addon">
								<span class="glyphicon glyphicon-user"></span>
							</div>
							<div class="input-group-addon">
								<span><?php echo $this->config->item('default_prefix_for_username'); ?></span>
							</div>
							<input type="text" name="username" class="form-control" placeholder="<?=lang('sys.item1')?>" value="<?php echo set_value('username') ?>">
						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-addon">
								<span class="glyphicon glyphicon-lock"></span>
							</div>
							<input type="password" name="password" class="form-control" placeholder="<?=lang('sys.item2')?>">
						</div>
					</div>

					<?php if ($this->operatorglobalsettings->getSettingIntValue('captcha_registration')) {?>
					<div class="form-group">
						<div class="input-group">
		                    <div class="row">
		                        <div class='col-md-12 fields'>
		                            <label for="captcha"><i style="color:#ff6666;">*</i><?php echo lang('label.captcha'); ?></label>
		                            <input type='text' name='login_captcha' id='captcha' class='input-sm' placeholder='' required>
		                            <a href="javascript:void(0)" onclick="refreshCaptcha()"><span class="glyphicon glyphicon-refresh"></span></a>
		                            <img id='image_captcha' src='<?php echo site_url('/iframe/auth/captcha?' . random_string('alnum')); ?>' width='120' height='40'>
		                            <br/>
		                            <?php echo form_error('captcha', '<span class="help-desk text-danger" style="color:#ff6666;">', '</span>'); ?>
		                            <span class="help-desk text-info captcha_right"></span>
		                        </div>
		                    </div>
                    	</div>
					</div>
<?php }
?>
					<center>
						<div class="form-group">
							<input type="submit" class="btn btn-primary" value="<?=lang('lang.logIn');?>" style="text-transform:uppercase; width:30%;">
						</div>
					</center>
				</form>
			</div>
		</div>
		<center><label class="text-info"><?=lang('reg.createAcc');?> <a href="<?=site_url('iframe_module/iframe_register');?>"><?=lang('reg.clickHere');?></a> | <a href="<?=site_url('iframe_module/forget_password_select');?>"><?=lang('lang.forgotpasswd');?></a></label></center>
	</div>
</div>

<script type="text/javascript">
	function refreshCaptcha(){
        //refresh
        $('#image_captcha').attr('src','<?php echo site_url('/iframe/auth/captcha'); ?>?'+Math.random());
    }
<?php if (isset($snackbar) && !empty($snackbar)) {
	?>
    $(function() {
        <?php foreach ($snackbar as $message) {?>
            $.snackbar({content: "<?php echo str_replace("\n", '', $message); ?>"});
        <?php }
	?>
    });
<?php }
?>
</script>
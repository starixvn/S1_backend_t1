<div class="row " style="padding-top:10%; padding-bottom:10%;">
	<div class="col-md-4 col-md-offset-4 fp-wrapper">
		<div class="panel panel-default"  style="padding-bottom: 20px">
			<div class="panel-heading" style="margin-bottom: 20px">
				<?=lang('lang.forgotpasswd')?>
				<a href="/iframe/auth/login" class="close" aria-hidden="true">×</a>
			</div>
			<div style="padding:0 10px">
			<?php if ($password_recovery_option_1) : ?>
			<input type="button" class="btn btn-primary" value="<?=lang('Find password by security question')?>"
				onclick="location.href='<?=site_url('iframe_module/forgot_password')?>'" />
			<?php endif; ?>
			<?php if ($password_recovery_option_2) : ?>
			<input type="button" class="btn btn-primary" value="<?=lang('Find password by SMS')?>"
				onclick="location.href='<?=site_url('iframe_module/password_recovery_sms')?>'" />
			<?php endif; ?>
			<?php if ($password_recovery_option_3) : ?>
			<input type="button" class="btn btn-primary" value="<?=lang('Find password by email')?>"
				onclick="location.href='<?=site_url('iframe_module/password_recovery_email')?>'" />
			<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
		$("body").addClass("pwd_recovery");
	});
</script>
<?php if (validation_errors()): ?>
	<div class="alert alert-danger" style="top:0"><?=validation_errors()?></div>
<?php endif?>
<div class="row" style="padding-top:10%; padding-bottom:10%;">
	<div class="col-md-4 col-md-offset-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<?=$title?>
				<a href="/iframe/auth/login" class="close" aria-hidden="true">×</a>
			</div>
			<div class="panel-body">
				<form action="/iframe_module/password_recovery_reset_code" method="POST" autocomplete="off">
					<div class="form-group">
						<input type="hidden" name="title" value="<?=$title?>" />
						<input type="text" id="username" name="username" class="form-control" value="<?=$username?>" style="margin-bottom: 5px" readonly />
						<input type="text" id="resetCode" name="reset_code" class="form-control" placeholder="<?=lang('Verification Code')?>" required="required" length="6" style="margin-bottom: 5px" />
						<input type="password" name="password" placeholder="<?=lang('New Password')?>" class="form-control" style="margin-bottom: 5px"/>
						<input type="password" name="confirm_password" placeholder="<?=lang('Confirm New Password')?>" class="form-control" style="margin-bottom: 5px"/>
						<button type="submit" class="btn btn-primary"><?=lang('Reset Password')?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
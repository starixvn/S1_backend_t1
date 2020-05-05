<div class="container">
	<br/>

	<!-- Change Password -->
	<div class="row">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">
					<i class="glyphicon glyphicon-cog"></i> <?=lang('Add Read-only Account'); ?>
					<a href="<?= BASEURL . 'affiliate/modifyAccount'?>" class="close">&times;</a>
				</h3>
			</div>

			<div class="panel panel-body">
				<form method="POST" action="<?= BASEURL . 'affiliate/verifyChangeReadOnlyAccountPassword/' . $account['id']?>" accept-charset="utf-8">
					<div class="row">
						<div class="col-md-4">
							<label for="username"><?= lang('Username'); ?>: </label>
							<input type="text" class="form-control" value="<?=$account['username']?>" readonly="readonly">
							<span class="help-block text-danger"><?php echo form_error('username'); ?></span>
						</div>
						<div class="col-md-4">
							<label for="password"><?= lang('New Password'); ?>: </label>
							<input type="password" name="password" id="password" class="form-control" placeholder="<?= lang('Password'); ?>" value="<?= set_value('password') ?>">
							<span class="help-block text-danger"><?php echo form_error('password'); ?></span>
						</div>
						<div class="col-md-4">
							<label for="confirm_password"><?= lang('Confirm New Password'); ?>: </label>
							<input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="<?= lang('Confirm Password'); ?>" value="<?= set_value('confirm_password') ?>">
							<span class="help-block text-danger"><?php echo form_error('confirm_password'); ?></span>
						</div>
					</div>
					<input type="submit" name="submit" id="submit" class="btn btn-primary pull-right" value="<?= lang('lang.submit'); ?>">
				</form>
			</div>
		</div>
	</div>
	<!-- End of Change Password -->
</div>
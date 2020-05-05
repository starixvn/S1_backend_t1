<div class="container">
	<br/>

	<!-- Change Password -->
	<div class="row">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">
					<i class="glyphicon glyphicon-cog"></i> <?= $new ? lang('Setup Secondary Password') : lang('Change Secondary Password'); ?>
					<a href="<?= BASEURL . 'affiliate/modifyAccount'?>" class="close">&times;</a>
				</h3>
			</div>

			<div class="panel panel-body" id="change_password_panel_body">
				<form method="POST" action="<?= BASEURL . 'affiliate/verifyChangeSecondPassword'?>" accept-charset="utf-8">
					<div class="row">
						<div class="col-md-4">
							<?php if ( ! $new): ?>
									<label for="old_password"><?= lang('Old Secondary Password'); ?>: </label>
									<input type="password" name="old_password" id="old_password" class="form-control" placeholder="<?= lang('Old Secondary Password'); ?>" value="<?= set_value('old_password') ?>">
									<label style="color: red; font-size: 12px;"><?php echo form_error('old_password'); ?></label>
							<?php else: ?>
								<input type="hidden" name="next_uri" value="<?=$next_uri?>"/>
							<?php endif ?>
						</div>

						<div class="col-md-4">
							<label for="new_password"><?= lang('New Secondary Password'); ?>: </label>
							<input type="password" name="new_password" id="new_password" class="form-control" placeholder="<?= lang('New Secondary Password'); ?>" value="<?= set_value('new_password') ?>">
							<label style="color: red; font-size: 12px;"><?php echo form_error('new_password'); ?></label>
						</div>
						<div class="col-md-4">
							<label for="confirm_new_password"><?= lang('Confirm New Secondary Password'); ?>: </label>
							<input type="password" name="confirm_new_password" id="confirm_new_password" class="form-control" placeholder="<?= lang('Confirm New Secondary Password'); ?>" value="<?= set_value('confirm_new_password') ?>">
							<label style="color: red; font-size: 12px;"><?php echo form_error('confirm_new_password'); ?></label>
						</div>
					</div>
					<input type="submit" name="submit" id="submit" class="btn btn-primary pull-right" value="<?= lang('lang.submit'); ?>">
				</form>
			</div>
		</div>
	</div>
	<!-- End of Change Password -->
</div>
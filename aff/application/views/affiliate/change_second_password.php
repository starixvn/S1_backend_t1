<div class="container">
	<br/>

	<!-- Change Second Password -->
	<div class="row">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title pull-left"><i class="glyphicon glyphicon-cog"></i> <?= lang('mod.changepass'); ?> </h4>
				<a href="<?php echo site_url('affiliate/modifyAccount'); ?>" class="btn btn-info btn-xs pull-right"><span class="glyphicon glyphicon-remove "></span></a>
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body" id="change_password_panel_body">
				<form method="POST" action="<?php echo site_url('affiliate/verifyChangePassword'); ?>" accept-charset="utf-8">
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-4">
								<label for="old_password"><?= lang('mod.oldpass'); ?>: </label>
								<input type="password" name="old_password" id="old_password" class="form-control" value="<?= set_value('old_password') ?>">
								<label style="color: red; font-size: 12px;"><?php echo form_error('old_password'); ?></label>
							</div>
							<div class="col-md-4">
								<label for="new_password"><?= lang('mod.newpass'); ?>: </label>
								<input type="password" name="new_password" id="new_password" class="form-control" value="<?= set_value('new_password') ?>">
								<label style="color: red; font-size: 12px;"><?php echo form_error('new_password'); ?></label>
							</div>
							<div class="col-md-4">
								<label for="confirm_new_password"><?= lang('mod.confirmpass'); ?>: </label>
								<input type="password" name="confirm_new_password" id="confirm_new_password" class="form-control" value="<?= set_value('confirm_new_password') ?>">
								<label style="color: red; font-size: 12px;"><?php echo form_error('confirm_new_password'); ?></label>
							</div>
						</div>
						<center>
							<input type="submit" name="submit" id="submit" class="btn btn-primary" value="<?= lang('mod.changepass'); ?>">
						</center>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- End of Change Second Password -->
</div>

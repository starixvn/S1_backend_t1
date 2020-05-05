<div class="panel panel-og-green">
	<div class="panel-heading">
		<div class="btn-group pull-right" style="margin: 5px 0;">
			<a href="<?= site_url('messages'); ?>" class="btn btn-default btn-sm text-uppercase" style="font-weight: bold;"><?= lang('cashier.40'); ?> <span class="glyphicon glyphicon-comment"></span></a>
		</div>
		<h4 class="text-uppercase" style="font-weight:bold;"><?= lang('cp.changePass'); ?></h4>
	</div>
	<div class="panel-body">
		<form action="<?= BASEURL . 'online/postResetPassword/' ?>" method="post" role="form" class="form-horizontal">

			<div class="form-group">
				<label class="custom-sm-4 custom-pdl-20 custom-leftside control-label required" style="text-align: left;"><?= lang('cp.currPass'); ?></label>
				<div class="custom-sm-7 custom-leftside custom-pdl-15">
					<input type="password" name="old_password" id="old_password" class="form-control input-sm" data-toggle="popover" data-placement="right" data-trigger="hover" title="Notice" data-content="Please enter your current password.">
					<?php echo form_error('old_password', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
				</div>
			</div>

			<div class="form-group">
				<label class="custom-sm-4 custom-pdl-20 custom-leftside control-label required" style="text-align: left;"><?= lang('forgot.09'); ?></label>
				<div class="custom-sm-7 custom-leftside custom-pdl-15">
					<input type="password" name="password" id="password" class="form-control input-sm" data-toggle="popover" data-placement="right" data-trigger="hover" title="Notice" data-content="Please enter your new password.">
					<?php echo form_error('password', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
				</div>
			</div>

			<div class="form-group">
				<label class="custom-sm-4 custom-pdl-20 custom-leftside control-label required" style="text-align: left;"><?= lang('forgot.10'); ?></label>
				<div class="custom-sm-7 custom-leftside custom-pdl-15">
					<input type="password" name="cpassword" id="cpassword" class="form-control input-sm" data-toggle="popover" data-placement="right" data-trigger="hover" title="Notice" data-content="Please enter your new password again for validation.">
					<?php echo form_error('cpassword', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <span class="help-block" id="lcpassword"></span>
				</div>
			</div>

			<div class="form-group">
				<div class="custom-pdl-15 custom-offset-4 custom-sm-2">
					<button type="submit" class="btn btn-block btn-hotel"><?= lang('lang.save'); ?></button>
				</div>
			</div>

			<div class="form-group">
				<div class="custom-pdl-15 custom-offset-4 custom-sm-5">
					<div class="help-block">* <?= lang('cashier.100'); ?>.</div>
				</div>
			</div>

		</form>
	</div>
</div>
<form action="<?=site_url('iframe_module/postResetPassword/');?>" method="post" role="form" class="form-horizontal">

	<div class="form-group">
		<label class="custom-sm-4 custom-pdl-20 custom-leftside control-label required" style="text-align: left;"><?=lang('cp.currPass');?></label>
		<div class="custom-sm-7 custom-leftside custom-pdl-15">
			<input type="password" name="opassword" id="opassword" class="form-control input-sm" data-toggle="popover" data-placement="bottom" data-trigger="hover" title="Notice" data-content="Please enter your current password.">
			<?php echo form_error('opassword', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
		</div>
	</div>

	<div class="form-group">
		<label class="custom-sm-4 custom-pdl-20 custom-leftside control-label required" style="text-align: left;"><?=lang('forgot.09');?></label>
		<div class="custom-sm-7 custom-leftside custom-pdl-15">
			<input type="password" name="password" id="password" minlength="4" maxlength="12" class="form-control input-sm" data-toggle="popover" data-placement="bottom" data-trigger="hover" title="Notice" data-content="Please enter your new password.">
			<?php echo form_error('password', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
		</div>
	</div>

	<div class="form-group">
		<label class="custom-sm-4 custom-pdl-20 custom-leftside control-label required" style="text-align: left;"><?=lang('forgot.10');?></label>
		<div class="custom-sm-7 custom-leftside custom-pdl-15">
			<input type="password" name="cpassword" id="cpassword" minlength="4" maxlength="12" class="form-control input-sm" data-toggle="popover" data-placement="bottom" data-trigger="hover" title="Notice" data-content="Please enter your new password again for validation.">
			<?php echo form_error('cpassword', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <span class="help-block" id="lcpassword"></span>
		</div>
	</div>

	<div class="form-group">
		<div class="custom-pdl-15 custom-offset-4 custom-sm-2">
			<button type="submit" class="btn btn-block btn-primary"><?=lang('lang.save');?></button>
		</div>
	</div>

	<div class="form-group">
		<div class="custom-pdl-15 custom-offset-4 custom-sm-5">
			<div class="help-block">* <?=lang('cashier.100');?>.</div>
		</div>
	</div>
	<a href="<?php echo site_url('iframe_module/iframe_viewCashier') ?>" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-circle-arrow-left"></span> <?=lang('button.back');?></a>
</form>
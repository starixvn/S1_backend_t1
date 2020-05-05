<form action="<?=site_url('iframe_module/postResetPassword/');?>" method="post" role="form" class="form-horizontal">

	<div class="passwordWrapper" style="    margin: 12px;">
        <h1 style="color: white; font-size: 22px;"><?=lang('mod.changepass')?></h1>
        <div class="row passwordRow">
          <div class="col-md-4 col-sm-4 col-xs-4 colPass">
            <p><?=lang('cp.currPass');?></p>
          </div>
          <div class="col-md-8 col-sm-8 col-xs-8 colPass">
            <input type="password" name="opassword" id="opassword" class="form-control input-sm" data-toggle="popover" data-placement="bottom" data-trigger="hover" title="Notice" data-content="Please enter your current password.">
            <?php echo form_error('opassword', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
          </div>

        </div>
        <hr style="border: 1px solid #202020; margin: 0; padding: 0;">
        <div class="row passwordRow">
          <div class="col-md-4 col-sm-4 col-xs-4 colPass">
            <p><?=lang('forgot.09');?></p>
          </div>
          <div class="col-md-8 col-sm-8 col-xs-8 colPass">
            <input type="password" name="password" id="password" minlength="4" maxlength="12" class="form-control input-sm" data-toggle="popover" data-placement="bottom" data-trigger="hover" title="Notice" data-content="Please enter your new password.">
			<?php echo form_error('password', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
          </div>
        </div>
        <hr style="border: 1px solid #202020; margin: 0; padding: 0;">
        <div class="row passwordRow">
          <div class="col-md-4 col-sm-4 col-xs-4 colPass">
            <p><?=lang('forgot.10');?></p>
          </div>
          <div class="col-md-8 col-sm-8 col-xs-8 colPass">
          	<input type="password" name="cpassword" id="cpassword" minlength="4" maxlength="12" class="form-control input-sm" data-toggle="popover" data-placement="bottom" data-trigger="hover" title="Notice" data-content="Please enter your new password again for validation.">
			<?php echo form_error('cpassword', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <span class="help-block" id="lcpassword"></span>
          </div>
        </div>
        <small style="color: gray">*<?=lang('cashier.100');?></small>
        <div class="btnPass">
        <center>
          <a href="#"><button><?=lang('macaopj.lang.save');?></button>    </a>
          <a href="<?php echo site_url('iframe_module/iframe_viewCashier') ?>" class="btn btn-danger btn-sm"><?=lang('button.back');?></a>
        </center>

        </div>

    </div>
</form>
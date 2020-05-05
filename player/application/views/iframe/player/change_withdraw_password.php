<form action="<?=site_url('player_center/postResetWithdrawPassword/');?>" method="post" role="form" class="form-horizontal">

    <?php if(!empty($player['withdraw_password'])) { ?>
        <div class="form-group">
            <label class="custom-sm-4 custom-pdl-20 custom-leftside control-label required" style="text-align: left;"> <?= lang('Withdraw Current Password'); ?></label>
            <div class="custom-sm-7 custom-leftside custom-pdl-15">
                <input type="password" name="current_password" id="current_password" class="form-control input-sm"
                       data-toggle="popover" data-placement="bottom" data-trigger="hover">
                <?php echo form_error('current_password', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
            </div>
        </div>
    <?php } ?>

    <div class="form-group">
        <label class="custom-sm-4 custom-pdl-20 custom-leftside control-label required" style="text-align: left;"> <?= lang('Withdraw New Password'); ?></label>
        <div class="custom-sm-7 custom-leftside custom-pdl-15">
            <input type="password" name="new_password" id="new_password" minlength="4" maxlength="12" class="form-control input-sm"
                   data-toggle="popover" data-placement="bottom" data-trigger="hover">
            <?php echo form_error('new_password', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
        </div>
    </div>

    <div class="form-group">
        <label class="custom-sm-4 custom-pdl-20 custom-leftside control-label required" style="text-align: left;"> <?= lang('Withdraw Confirm New Password'); ?></label>
        <div class="custom-sm-7 custom-leftside custom-pdl-15">
            <input type="password" name="confirm_new_password" id="confirm_new_password" minlength="4" maxlength="12" class="form-control input-sm" data-toggle="popover"
                   data-placement="bottom" data-trigger="hover">
            <?php echo form_error('confirm_new_password', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <span class="help-block" id="lcpassword"></span>
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
    <a href="<?php echo site_url('player_center/dashboard') ?>" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-circle-arrow-left"></span> <?=lang('button.back');?></a>
</form>
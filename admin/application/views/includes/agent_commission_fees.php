<!-- AGENCY SETTLEMENT SETTINGS -->
<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title"><font style="color:red;">*</font> <?=lang('Fees');?></h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <label class="control-label"><?=lang('Admin Fee')?></label>
                <div class="input-group">
                    <input type="number" class="form-control" name="admin_fee" id="admin_fee" value="<?=set_value('admin_fee', number_format($agent['admin_fee'],2));?>" step="any" min="0" required="required" <?php if (isset($view_only) && $view_only) echo 'disabled="disabled"' ?>/>
                    <div class="input-group-addon">%</div>
                </div>
                <span class="errors"><?php echo form_error('admin_fee'); ?></span>
                <span id="error-admin_fee" class="errors"></span>
            </div>
            <?php if($this->utils->isEnabledFeature('use_deposit_withdraw_fee')) : ?>
                <div class="col-md-3">
                    <label class="control-label"><?=lang('Deposit Fee')?></label>
                    <div class="input-group">
                        <input type="number" class="form-control" name="deposit_fee" id="deposit_fee" value="<?=set_value('deposit_fee', number_format($agent['deposit_fee'],2));?>" step="any" min="0" required="required" <?php if (isset($view_only) && $view_only) echo 'disabled="disabled"' ?>/>
                        <div class="input-group-addon">%</div>
                    </div>
                    <span class="errors"><?php echo form_error('deposit_fee'); ?></span>
                    <span id="error-deposit_fee" class="errors"></span>
                </div>
                <div class="col-md-3">
                    <label class="control-label"><?=lang('Withdraw Fee')?></label>
                    <div class="input-group">
                        <input type="number" class="form-control" name="withdraw_fee" id="withdraw_fee" value="<?=set_value('withdraw_fee', number_format($agent['withdraw_fee'],2));?>" step="any" min="0" required="required" <?php if (isset($view_only) && $view_only) echo 'disabled="disabled"' ?>/>
                        <div class="input-group-addon">%</div>
                    </div>
                    <span class="errors"><?php echo form_error('withdraw_fee'); ?></span>
                    <span id="error-withdraw_fee" class="errors"></span>
                </div>
            <?php else : ?>
                <div class="col-md-6">
                    <label class="control-label"><?=lang('Transaction Fee')?></label>
                    <div class="input-group">
                        <input type="number" class="form-control" name="transaction_fee" id="transaction_fee" value="<?=set_value('transaction_fee', number_format($agent['transaction_fee'],2));?>" step="any" min="0" required="required" <?php if (isset($view_only) && $view_only) echo 'disabled="disabled"' ?>/>
                        <div class="input-group-addon">%</div>
                    </div>
                    <span class="errors"><?php echo form_error('transaction_fee'); ?></span>
                    <span id="error-transaction_fee" class="errors"></span>
                </div>
            <?php endif; ?>
            <div class="col-md-6">
                <label class="control-label"><?=lang('Bonus Fee')?></label>
                <div class="input-group">
                    <input type="number" class="form-control" name="bonus_fee" id="bonus_fee" value="<?=set_value('bonus_fee', number_format($agent['bonus_fee'],2));?>" step="any" min="0" required="required" <?php if (isset($view_only) && $view_only) echo 'disabled="disabled"' ?>/>
                    <div class="input-group-addon">%</div>
                </div>
                <span class="errors"><?php echo form_error('bonus_fee'); ?></span>
                <span id="error-bonus_fee" class="errors"></span>
            </div>
            <div class="col-md-6">
                <label class="control-label"><?=lang('Cashback Fee')?></label>
                <div class="input-group">
                    <input type="number" class="form-control" name="cashback_fee" id="cashback_fee" value="<?=set_value('cashback_fee', number_format($agent['cashback_fee'],2));?>" step="any" min="0" required="required" <?php if (isset($view_only) && $view_only) echo 'disabled="disabled"' ?>/>
                    <div class="input-group-addon">%</div>
                </div>
                <span class="errors"><?php echo form_error('cashback_fee'); ?></span>
                <span id="error-cashback_fee" class="errors"></span>
            </div>
        </div>
    </div>
</div>
<!-- END AGENCY SETTLEMENT SETTINGS -->

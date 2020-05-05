<?php $second_category_flag = (isset($second_category_flag)) ? $second_category_flag : '' ?>
<?php if ($this->utils->isEnabledFeature('enable_manual_deposit_realname')) :?>
<div class="row deposit-process-mode-<?=in_array($deposit_process_mode, array('2','3')) ? '2' : $deposit_process_mode ?> setup-deposit-notes">
    <div class="form-group has-feedback">
        <p class="step"><span class="step-icon"><?=$deposit_step++?></span><label class="control-label"><?=lang('report.in11')?></label></p>
        <div class="input-group col col-xs-12 col-sm-12 col-md-8">
            <?php if($firstNameflg):?>
                <input type="text" class="form-control"   value="<?=$firstName?>"  readonly>
                <?php if($second_category_flag == SECOND_CATEGORY_BANK_TRANSFER || $second_category_flag == SECOND_CATEGORY_ATM_TRANSFER):?>
                    <div class="helper-content text-danger font-weight-bold">
                        <?=lang('manual.bank.deposit_realname.hint')?>
                    </div>
                <?php endif;?>
            <?php else:?>
                <div class="alert alert-danger">
                    <strong><?=lang('reg.firstName')?></strong>
                </div>
            <?php endif;?>
        </div>


    </div>

    <hr />
</div>
<?php endif ?>
<?php if ($this->utils->isEnabledFeature('enable_manual_deposit_input_depositor_name')){?>
<div class="row deposit-process-mode-<?=in_array($deposit_process_mode, array('2','3')) ? '2' : $deposit_process_mode ?> setup-deposit-notes">
    <div class="form-group has-feedback">
        <p class="step"><span class="step-icon"><?=$deposit_step++?></span><label class="control-label"><?=lang('Please input depositor name')?></label></p>
        <div class="input-group col col-xs-12 col-sm-12 col-md-8">
            <?php if($isAlipay):?>
                <input type="text" class="form-control" value="<?=$firstName?>" minlength="2" name="depositor_name" id="depositor_name"
                       data-required-error="<?=lang('Fields with (*) are required.')?>"
                       data-error="<?=lang('text.error')?>"
                       required="required">
                <div class="help-block with-errors"></div>
             <?php elseif($isUnionpay):?>
                <input type="text" class="form-control" value="<?=$firstName?>" minlength="2" name="depositor_name" id="depositor_name"
                       data-required-error="<?=lang('Fields with (*) are required.')?>"
                       data-error="<?=lang('text.error')?>"
                       required="required">
                <div class="help-block with-errors"></div>
            <?php else:?>
                <input type="text" class="form-control" value="<?=$firstName?>" minlength="2" name="depositor_name" id="depositor_name">
                <?php if($second_category_flag == SECOND_CATEGORY_BANK_TRANSFER || $second_category_flag == SECOND_CATEGORY_ATM_TRANSFER):?>
                    <div class="helper-content text-danger font-weight-bold">
                        <?=lang('manual.bank.deposit_realname.hint')?>
                    </div>
                <?php endif;?>
            <?php endif;?>
        </div>
    </div>
    <hr />
</div>
<?php } ?>

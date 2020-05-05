<?php if ($this->utils->isEnabledFeature('enable_note_input_field_in_the_deposit')) :?>
    <div class="row deposit-process-mode-<?=in_array($deposit_process_mode, array('2','3')) ? '2' : $deposit_process_mode ?> setup-deposit-notes">
        <div class="form-group has-feedback">
            <p class="step"><span class="step-icon"><?=$deposit_step++?></span><label class="control-label"><?=lang('Please Enter the Deposit Notes')?></label>
                <?php if($this->system_feature->isEnabledFeature('enable_display_manual_deposit_note_step_hint')):?>
                    <span class="step_hint manual_deposit_note_step_hint"><?=lang('pay.manual_deposit.step_hint.note')?></span>
                <?php endif;?>
            </p>
            <div class="input-group col col-xs-12 col-sm-12 col-md-8">
                <input type="text" class="form-control" id="deposit_notes" placeholder="<?=lang('deposit_notes.placeholder')?>"
                       maxlength="100">
            </div>
            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
            <div class="help-block with-errors"></div>

            <div class="helper-content">
                <?php $depositHint = $this->utils->getConfig('playercenter.deposit.hint');
                if (!empty($depositHint)) : ?>
                    <p><?= lang($depositHint) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <hr />
    </div>
<?php else: ?>
    <input type="hidden" class="form-control" id="deposit_notes" placeholder="<?=lang('deposit_notes.placeholder')?>">
<?php endif ?>
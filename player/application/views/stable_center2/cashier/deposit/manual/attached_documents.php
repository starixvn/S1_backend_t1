<?php if ($this->utils->isEnabledFeature('enable_deposit_upload_documents')) :?>
    <div class="row deposit-process-mode-<?=in_array($deposit_process_mode, array('2','3')) ? '2' : $deposit_process_mode ?> setup-deposit-uploads">
        <div class="form-group has-feedback">
            <p class="step"><span class="step-icon"><?=$deposit_step++?></span><label class="control-label"><?=lang('Upload Attachment')?></label>
                <?php if($this->system_feature->isEnabledFeature('enable_display_manual_deposit_upload_documents_step_hint')):?>
                    <span class="step_hint manual_deposit_upload_documents_step_hint"><?=lang('pay.manual_deposit.step_hint.upload_documents')?></span>
                <?php endif;?>
            </p>
            <span id="errfm_txtImage" class="text-danger"><?=$this->utils->isEnabledFeature('enable_deposit_page_make_manual_deposit_upload_helptext_always_showing')?lang('Please upload at least one file when using ATM/Cashier payment account.'):'';?></span>
            <div class="input-group col col-xs-6 col-sm-6 col-md-6 upload-browse">
                <span type="text" class="form-control upload-depo" maxlength="100"><?=lang('File 1')?>:</span>
                 <label class="btn btn-default btn-file">
                    <?= lang('Browse') ?>
                    <input type="file" class="txtImage" id="file1" name="file1[]" title="<?=lang('upload_no_file_tooltip')?>" onchange="getFileData(this)" hidden />
                </label>
            </div>
            <div class="input-group col col-xs-6 col-sm-6 col-md-6 upload-browse">
                <span type="text" class="form-control upload-depo" maxlength="100"><?=lang('File 2')?>:</span>
                 <label class="btn btn-default btn-file">
                    <?= lang('Browse') ?>
                    <input type="file" class="txtImage" id="file2" name="file2[]" title="<?=lang('upload_no_file_tooltip')?>" onchange="getFileData(this)" hidden />
                </label>
            </div>
        </div>
        <hr />
    </div>
<?php endif; ?>


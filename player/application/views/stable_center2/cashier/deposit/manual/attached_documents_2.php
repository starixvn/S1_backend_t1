<?php if ($this->utils->isEnabledFeature('enable_deposit_upload_documents')) :?>
    <div class="row deposit-process-mode-<?=$deposit_process_mode?> setup-deposit-uploads"">

        <div class="">
            <div class="modal-header">
                <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> -->
                <h4 class="modal-title" id="myModalLabel"><?=lang('collection.heading.1')?></h4>
            </div>
            <div class="modal-body">
                <div class="row deposit-payment-account-detail">
                    <div class="col col-md-8">
                        <h4><?=lang('Check your deposit')?>:</h4>
                        <div class="row">
                            <span class="col col-xs-4 col-md-4 text-right text-danger"><strong><?=lang('collection.label.1')?>:</strong></span>
                            <span class="col col-xs-5 col-md-5 text-left text-danger" id="modal_order_id"></span>
                            <span class="col col-xs-3 col-md-3">
                                <button type="button" class="btn btn-copy"
                                    data-clipboard-action="copy"
                                    data-clipboard-target="#modal_order_id"
                                    title="<?=lang('Copied')?>"><?=lang('Copy')?></button>
                            </span>
                        </div>
                        <div class="row">
                            <span class="col col-xs-4 col-md-4 text-right"><strong><?=lang('Account Name')?>:</strong></span>
                            <span class="col col-xs-5 col-md-5 text-left" id="modal_account_name"></span>
                            <span class="col col-xs-3 col-md-3">
                                <button type="button" class="btn btn-copy"
                                    data-clipboard-action="copy"
                                    data-clipboard-target="#modal_account_name"
                                    title="<?=lang('Copied')?>"><?=lang('Copy')?></button>
                            </span>
                        </div>
                        <div class="row account-number">
                            <span class="col col-xs-4 col-md-4 text-right"><strong><?=lang('Account Number')?>:</strong></span>
                            <span class="col col-xs-5 col-md-5 text-left" id="modal_account_number"></span>
                            <span class="col col-xs-3 col-md-3">
                                <button type="button" class="btn btn-copy"
                                    data-clipboard-action="copy"
                                    data-clipboard-target="#modal_account_number"
                                    title="<?=lang('Copied')?>"><?=lang('Copy')?></button>
                            </span>
                        </div>
                        <div class="row bank-branch-name">
                            <span class="col col-xs-4 col-md-4 text-right"><strong><?=lang('Bank Branch Name')?>:</strong></span>
                            <span class="col col-xs-5 col-md-5 text-left" id="modal_bank_branch_name"></span>
                            <span class="col col-xs-3 col-md-3">
                                <button type="button" class="btn btn-copy"
                                    data-clipboard-action="copy"
                                    data-clipboard-target="#modal_bank_branch_name"
                                    title="<?=lang('Copied')?>"><?=lang('Copy')?></button>
                            </span>
                        </div>
                        <div class="row">
                            <span class="col col-xs-4 col-md-4 text-right"><strong><?=lang('Deposit Amount')?>:</strong></span>
                            <span class="col col-xs-8 col-md-8 text-left" id="modal_deposit_amount"></span>
                        </div>
<!--                         <div class="row text-danger">
                            <span class="col col-xs-4 col-md-4 text-right"><strong><?=lang('Min deposit per transaction')?>:</strong></span>
                            <span class="col col-xs-8 col-md-8 text-left" id="modal_min_deposit_trans"></span>
                        </div>
                        <div class="row text-danger">
                            <span class="col col-xs-4 col-md-4 text-right"><strong><?=lang('Max deposit per transaction')?>:</strong></span>
                            <span class="col col-xs-8 col-md-8 text-left" id="modal_max_deposit_trans"></span>
                        </div>
                        <div class="row">
                            <span class="col col-xs-4 col-md-4 text-right"><strong><?=lang('collection.label.6')?>:</strong></span>
                            <span class="col col-xs-8 col-md-8 text-left" id="modal_requested_on"></span>
                        </div>
                        <div class="row">
                            <span class="col col-xs-4 col-md-4 text-right"><strong><?=lang('collection.label.7')?>:</strong></span>
                            <span class="col col-xs-8 col-md-8 text-left" id="modal_expires_on"></span>
                        </div> -->
                        <?php if ($this->utils->isEnabledFeature('enable_deposit_datetime')) :?>
                            <div class="row">
                                <span class="col col-xs-4 col-md-4 text-right"><strong><?=lang('Deposit Date time')?>:</strong></span>
                                <span class="col col-xs-8 col-md-8 text-left" id="modal_deposit_date_time"></span>
                            </div>
                            <div class="row">
                                <span class="col col-xs-4 col-md-4 text-right"><strong><?=lang('Mode of Deposit')?>:</strong></span>
                                <span class="col col-xs-8 col-md-8 text-left" id="modal_mode_of_deposit"></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col col-md-4 account-image">
                        <span id="modalAccountImage"><img src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D" /></span>
                    </div>
                </div>

                <div class="helper-content text-danger font-weight-bold">
                    <p><?=lang('collection.text.1')?></p>
                </div>
            </div>
            <div class="modal-footer text-left">
<!--                 <?php if(!$this->utils->isEnabledFeature('hidden_print_deposit_order_button')): ?>
                <button type="button" class="btn btn-default hidden-sm hidden-xs" id="printThisPageBtn" onclick="printDepositOrder();"><?=lang('action.print_current_page')?></button>
                <?php endif ?>
                <button type="button" class="btn btn-default" data-dismiss="modal" id="modalDepositBtnClose"><?=lang('Close')?></button> -->
            </div>
        </div>


        <div class="form-group has-feedback">
            <p class="step"><span class="step-icon"><?=$deposit_step++?></span><label class="control-label"><?=lang('Upload Attachment')?></label>
                <?php if($this->system_feature->isEnabledFeature('enable_display_manual_deposit_upload_documents_step_hint')):?>
                    <span class="step_hint manual_deposit_upload_documents_step_hint"><?=lang('pay.manual_deposit.step_hint.upload_documents')?></span>
                <?php endif;?>
            </p>
            <span id="errfm_txtImage" class="text-danger"></span>
            <div class="input-group col col-xs-6 col-sm-6 col-md-6 upload-browse">
                <span type="text" class="form-control upload-depo" maxlength="100"><?=lang('File 1')?>:</span>
                 <label class="btn btn-default btn-file">
                    <?= lang('Browse') ?>
                    <input type="file" class="txtImage" id="file1" name="file1[]" title="<?=lang('upload_no_file_tooltip')?>"  onchange="getFileData(this)" hidden />
                </label>
            </div>
            <div class="input-group col col-xs-6 col-sm-6 col-md-6 upload-browse">
                <span type="text" class="form-control upload-depo" maxlength="100"><?=lang('File 2')?>:</span>
                 <label class="btn btn-default btn-file">
                    <?= lang('Browse') ?>
                    <input type="file" class="txtImage" id="file2" name="file2[]" title="<?=lang('upload_no_file_tooltip')?>"  onchange="getFileData(this)" hidden />
                </label>
            </div>
        </div>
        <hr />
    </div>
<?php endif; ?>
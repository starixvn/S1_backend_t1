<?php if(!empty($secure_id)){ ?>

<?php if ($this->utils->isEnabledFeature('hide_deposit_selected_bank_and_text_for_ole777')) {?>
<div class="row deposit-process-mode-<?=in_array($deposit_process_mode, array('2','3')) ? '2' : $deposit_process_mode ?> setup-deposit-secure_id">
    <div class="form-group">
        <p class="step"><span class="step-icon"><?=$deposit_step++?></span><label class="control-label"><?=lang('column.secure_id')?></label></p>
    </div>
    <div class="row">
        <div class="col col-xs-8 col-md-4 nopadding">
            <div class="col-md-4" id="text_deposit_secure_id"><?=$secure_id?></div>
        </div>
        <?php if($isUnionpay || $isWechat):?>
            <?php if($this->CI->utils->is_mobile()):?>
            <div class="col col-xs-4 col-md-8 nopadding"> 
                <button type="button" class="btn btn-copy"
                    data-clipboard-action="copy"
                    data-clipboard-target="#text_deposit_secure_id"
                    title="<?=lang('Copied')?>"><?=lang('Copy')?>
                </button>
            </div>
            <?php endif;?>
        <?php else:?>
            <div class="col col-xs-4 col-md-8 nopadding">
                <button type="button" class="btn btn-copy"
                    data-clipboard-action="copy"
                    data-clipboard-target="#text_deposit_secure_id"
                    title="<?=lang('Copied')?>"><?=lang('Copy')?>
                </button>
            </div>
        <?php endif;?>
        <?php if($isAlipay):?>
        <div class="helper-content text-danger font-weight-bold">
            <?=lang('manual.alipay.secureid.hint')?>
        </div>
        <?php endif;?>
        <?php if($isUnionpay):?>
        <div class="col col-xs-12 nopadding helper-content text-danger font-weight-bold">
            <?=lang('manual.unionpay.secureid.hint')?>
        </div>
        <?php endif;?>
    </div>
    <input type="hidden" class="form-control col-md-4" id="deposit_secure_id" placeholder="<?=lang('Notes')?>" readonly="" value="<?=$secure_id?>">
    <hr />
</div>
<?php }else if (!$this->utils->isEnabledFeature('hidden_secure_id_in_deposit')) {?>
<div class="row deposit-process-mode-<?=in_array($deposit_process_mode, array('2','3')) ? '2' : $deposit_process_mode ?> setup-deposit-secure_id">
    <div class="form-group">
        <p class="step"><span class="step-icon"><?=$deposit_step++?></span><label class="control-label"><?=lang('column.secure_id')?></label></p>
        <div class="row">
            <div class="col col-xs-8 col-md-4 nopadding">
                <input type="text" class="form-control col-md-4" id="deposit_secure_id" placeholder="<?=lang('Notes')?>" readonly="" value="<?=$secure_id?>">
            </div>
            <?php if($isUnionpay || $isWechat):?>
                <?php if($this->CI->utils->is_mobile()):?>
                <div class="col col-xs-4 col-md-8 nopadding"> 
                    <button type="button" class="btn btn-copy"
                        data-clipboard-action="copy"
                        data-clipboard-target="#deposit_secure_id"
                        title="<?=lang('Copied')?>"><?=lang('Copy')?>
                    </button>
                </div>
                <?php endif;?>
            <?php else:?>
                <div class="col col-xs-4 col-md-8 nopadding">
                    <button type="button" class="btn btn-copy"
                        data-clipboard-action="copy"
                        data-clipboard-target="#deposit_secure_id"
                        title="<?=lang('Copied')?>"><?=lang('Copy')?>
                    </button>
                </div>
            <?php endif;?>
            <?php if($isAlipay):?>
            <div class="helper-content text-danger font-weight-bold">
                <?=lang('manual.alipay.secureid.hint')?>
            </div>
            <?php endif;?>
            <?php if($isUnionpay):?>
        <div class="helper-content text-danger font-weight-bold">
            <?=lang('manual.unionpay.secureid.hint')?>
        </div>
        <?php endif;?>
        </div>
        <div class="help-block with-errors"></div>

        <div class="helper-content text-danger font-weight-bold">
            <p><?=lang('Empty Note')?></p>
        </div>
    </div>

    <hr />
</div>
<?php }else{ ?>
        <input type="hidden" class="form-control col-md-4" id="deposit_secure_id" placeholder="<?=lang('Notes')?>" readonly="" value="<?=$secure_id?>">
<?php } ?>

<?php } ?>
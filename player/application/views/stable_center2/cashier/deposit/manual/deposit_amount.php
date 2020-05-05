<div class="row deposit-process-mode-<?=in_array($deposit_process_mode, array('2','3')) ? '2' : $deposit_process_mode ?> setup-deposit-amount">
    <div class="form-group has-feedback">
        <p class="step"><span class="step-icon"><?=$deposit_step++?></span><label class="control-label"><?=lang('Please Enter Deposit Amount')?></label></p>
        <div class="input-group col col-xs-12 col-sm-12 col-md-4">
            <input type="number" step="0.01" class="form-control" id="bankDepositAmount" name="bankDepositAmount" placeholder="<?=lang('Deposit Amount')?>"
                   lang-min-error="<?=sprintf(lang('formvalidation.greater_than'), lang('Deposit Amount'), '{0}')?>"
                   lang-max-error="<?=sprintf(lang('formvalidation.less_than'), lang('Deposit Amount'), '{0}')?>"
                   data-required-error="<?=lang('Fields with (*) are required.')?>"
                   data-error="<?=lang('text.error')?>"
                   required="required"
                   <?php if(isset($currency_conversion_rate)): ?>
                    onKeyUp="currency_rate_converter(<?=$decimal_digit?>)"
                   <?php endif; ?>>
            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
        </div>
        <div class="help-block with-errors"></div>

        <div class="helper-content">
            <div class="deposit_note"><?=lang('Deposit Amount Note')?></div>
            <?php if(stristr(lang('Min deposit per transaction'),'%s') && stristr(lang('Max deposit per transaction'),'%s')) :?>
                <p class="deposit-limit min"><?= sprintf(lang('Min deposit per transaction'), '<span data-default="0">0</span>'); ?></p>
                <p class="deposit-limit max">
                <?= sprintf(lang('Max deposit per transaction'), '<span data-default='.$this->utils->getConfig('defaultMaxDepositDaily').'><?=$this->utils->getConfig("defaultMaxDepositDaily")?></span>'); ?></p>
            <?php else: ?>
                <p class="deposit-limit min"><?=lang('Min deposit per transaction')?>: <span data-default="0">0</span></p>
                <p class="deposit-limit max"><?=lang('Max deposit per transaction')?>: <span data-default="<?=$this->utils->getConfig('defaultMaxDepositDaily')?>"><?=$this->utils->getConfig('defaultMaxDepositDaily')?></span></p>
            <?php endif ?>
        </div>
        <?php if(isset($cryptocurrency)): ?>
            <p class="deposit-limit exchange-rate-note"><?=lang('exchange_rate_reference_only_hint')?>
            <p class="deposit-limit cryptographic-currency"><?=sprintf(lang('Depoist Amount in %s'), lang($cryptocurrency."-Crypto"))?>: <span class="currency_conversion_result cryptographic-amount"></span></p>
            <p class="deposit-limit"><b><?=lang('Cryptocurrencies Exchange Rate')?></b></p>
            <p class="deposit-limit"><?=lang($cryptocurrency)?>: <?=$cryptocurrency_rate?></p>
        <?php elseif(isset($fix_currency_conversion)): ?>
            <p class="deposit-limit exchange-rate-note"><?=$fix_rate_note?></p>
            <p class="deposit-limit"><?=$fix_rate_convert_result_note?></p>
        <?php endif; ?>
        <?php # Hint for deposts
        if($this->utils->isEnabledFeature('show_decimal_amount_hint') && !$isAlipay) :
            ?>
            <div class="decimal-point-hint" style="padding-top:20px">
                <?= lang('Please enter amount with decimal values for faster processing.'); ?>
            </div>
        <?php endif; ?>

        <?php if ($this->utils->isEnabledFeature('enable_deposit_amount_note')) : ?>
          <div class="helper-content text-danger font-weight-bold">
              <p><?=lang('collection_deposit_amount')?></p>
          </div>
        <?php endif; ?>
    </div>

    <hr/>
</div>
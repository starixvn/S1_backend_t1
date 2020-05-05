<div class="from-group setup-deposit-amount">
    <input type="hidden" name="minDeposit" value="<?=$minDeposit?>">
    <input type="hidden" name="maxDeposit" value="<?=$maxDeposit?>">
    <?php foreach ($special_limit_rules as $rule) : ?>
        <?php if(!empty($rule)) :?>
            <input type="hidden" name="special_limit_rules[]" value="<?=$rule?>">
        <?php endif?>
    <?php endforeach?>

    <label class="control-label"><?=$label;?></label>
    <input type="text" class="form-control" name="deposit_amount" pattern="<?=$inputInfo['float_amount_limit']?>">

    <div class="small">
        <?=lang('player.mindep')?>:
        <span class="depamt min"><?=$this->utils->formatCurrency($minDeposit)?></span>
    </div>
    <div class="small">
        <?=lang('pay.maxdepamt')?>:
        <span class="depamt max"><?=$this->utils->formatCurrency($maxDeposit)?></span>
    </div>

    <?php if(!empty($hint = $external_system_api->getAmountHint())) : ?>
        <div class="helper-content deposit_hint text-danger font-weight-bold">
            <p><?=$hint?></p>
        </div>
    <?php endif; ?>
    <div class="clear"></div>
</div>
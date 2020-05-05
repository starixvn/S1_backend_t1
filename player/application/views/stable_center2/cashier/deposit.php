<!-- DEPOSIT PAGE -->

<div id="fm-deposit">
    <?php if($this->utils->isEnabledFeature('enable_deposit_category_view') === false) { ?>
        <?php if(!$this->utils->is_mobile()) {?>
        <ul class="nav inner-tab show-bank" id="depositTabList">
        <?php if (!empty($payment_manual_accounts)):?>
        <li class="<?=($deposit_method == 'manual') ? 'active' : ''?>"><a href="<?=$this->utils->getSystemUrl('player')?>/player_center2/deposit/manual_payment"><?=lang('Bank Deposit') ?></a></li>
        <?php endif?>
        <?php foreach ($payment_auto_accounts as $key => $val): ?>
            <li class="<?=($this->uri->segment(4) == $val->payment_account_id) ? 'active' : ''?>"><a href="<?=$this->utils->getSystemUrl('player')?>/player_center2/deposit/auto_payment/<?=$val->payment_account_id?>"><?=lang($val->payment_type)?></a></li>
        <?php endforeach ?>
        </ul>
        <?php } else { ?>
        <div class="form-group">
            <strong class='payment-method-text'><?=lang('con.pym10') ?></strong>
            <select class="form-control selectpicker deposit-mobile-select-list" id="depositTabList" onChange="location=this.value;">
            <?php if (!empty($payment_manual_accounts)):?>
                <option value='<?=$this->utils->getSystemUrl('player')?>/player_center2/deposit/manual_payment' <?=($deposit_method == 'manual') ? 'selected' : ''?> >
                    <?=lang('Bank Deposit') ?>
                </option>
            <?php endif?>
            <?php foreach ($payment_auto_accounts as $key => $val): ?>
                <option value='<?=$this->utils->getSystemUrl('player')?>/player_center2/deposit/auto_payment/<?=$val->payment_account_id?>' <?=($this->uri->segment(4) == $val->payment_account_id) ? 'selected' : '' ?> >
                    <?=lang($val->payment_type)?>
                </option>
            <?php endforeach ?>
            </select>
        </div>
        <?php } ?>
    <?php } else if(!$hide_deposit_selected_bank_and_text_for_ole777) { ?>
        <ul class="nav inner-tab show-bank">
            <li class="active"><a href="#" id='selected-payment-account-name'><?=$deposit_method == 'auto' ? lang($payment_account->payment_type) : ""; ?></a></li>
        </ul>
    <?php } ?>
    <div class="deposit-detail-content" style="padding-top:20px">
        <?php include __DIR__ . '/../cashier/deposit/' . $deposit_method . '.php';?>
    </div>
</div>

<?php include __DIR__ . '/../cashier/deposit/modal.php';?>
<?php include __DIR__ . '/../bank_account/content/modal.php';?>
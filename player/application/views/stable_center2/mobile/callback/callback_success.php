<div class="livezf" style="display: none;">
</div>
<div class="atmzf" style="border: 0px; display: block;">
    <div id="Panelatm1">
        <div class="ghinfo">
            <ul>
                <li><?php echo lang('payment.success.bill'); ?>：<a id="rName"><?php echo $sale_order->secure_id; ?></a></li>
                <li><?php echo lang('payment.success.3rdpary_bill'); ?>：<a id="rName"><?php echo $sale_order->external_order_id; ?></a></li>
                <li><?php echo lang('payment.success.bank_bill'); ?>：<a id="rName"><?php echo $sale_order->bank_order_id; ?></a></li>
                <li><?php echo lang('payment.success.amount'); ?>：<a id="rName"><?php echo $this->utils->displayCurrency($transaction->amount); ?></a></li>
                <li><?php echo lang('payment.success.previous_balance'); ?>：<a id="rName">
                    <?php
if (!empty($transaction)) {
echo $this->utils->displayCurrency($transaction->before_balance);
}
?>
                </a></li>
            </ul>
        </div>

        <div class="ghinfo">
            <ul>
                <li><?php echo lang('payment.success.current_balance'); ?>：<a id="rName"><?php
if (!empty($transaction)) {
echo $this->utils->displayCurrency($transaction->after_balance);
}?></a></li>
                <?php if (isset($promo_trans) && $promo_trans) {?>

                    <li><?php echo lang('payment.success.bouns_amount'); ?>：<a id="rName"><?php echo $this->utils->displayCurrency($promo_trans->before_balance); ?></a></li>
                    <li><?php echo lang('payment.success.current_balance'); ?>：<a id="rName"><?php echo $this->utils->displayCurrency($promo_trans->after_balance); ?></a></li>
                    <li><?php echo lang('payment.success.status'); ?>：<a id="rName">Success</a></li>

                <?php } ?>

            </ul>
        </div>
    </div>
</div>
<style>
/*.mc-content .row {
    margin: 0;
}

.fm-ul {
    list-style: none;
    padding: 0;
}
.fm-ul li {
    padding: 0;
}

.fm-ul li a {
    color: #353535;
    font-size: 16px;
    padding: 8px 20px;
    display: block;
    margin-bottom: -1px;
    background: #f3f3f3;
    border: 1px solid #f3f3f3;
    border-bottom-color: #e0e0e0;
}

a {
    transition: all .3s ease;
}*/
</style>
<script type="text/javascript">
var glob = {
    show_tag_for_unavailable_deposit_accounts: <?=$show_tag_for_unavailable_deposit_accounts?> ,
    disable_account_transfer_when_balance_check_fails: <?=$disable_account_transfer_when_balance_check_fails?> ,
    tag_unavailable: ' (<?= lang('Unavailable') ?>)'
};
</script>
<!-- DEPOSIT PAGE -->
<div id="fm-deposit">
	<div class="row">
        <?php if (!empty($payment_manual_accounts)) {
        	$active = "";
        	if ($deposit_method == 'manual')
        		$active = "active";
        ?>
        <a href="iframe_module/mini_manual_payment" class="btn btn-primary <?=$active?>"><?=lang('Bank Deposit') ?></a>
        <?php }?>
        <?php foreach ($payment_auto_accounts as $key => $val) { 
        	$active = "";
        	if ($this->uri->segment(3) == $val->payment_account_id)
        		$active = "active"
        ?>
        <a href="iframe_module/mini_auto_payment/<?=$val->payment_account_id?>" class="btn btn-primary <?=$active?>"><?=lang($val->payment_type)?></a>
        <?php } ?>
    </div>
    <?php # Hint for deposts
    if($this->utils->isEnabledFeature('show_decimal_amount_hint')) :
    ?>
    <div class="decimal-point-hint" style="padding-top:20px">
        <?= lang('Please enter amount with decimal values for faster processing.'); ?>
    </div>
    <?php endif; ?>
    <div class="deposit-detail-content" style="padding-top:20px">
        <?php include VIEWPATH . '/iframe/cashier/deposit/' . $deposit_method . '.php';?>
    </div>
</div>

<?php include VIEWPATH . '/' . $player_center_template . '/cashier/deposit/modal.php';?>
<?php include VIEWPATH . '/' . $player_center_template . '/bank_account/content/modal.php';?>
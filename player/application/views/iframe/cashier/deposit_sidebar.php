<?php
$FLAG_MANUAL_ONLINE_PAYMENT = MANUAL_ONLINE_PAYMENT;
$FLAG_AUTO_ONLINE_PAYMENT = AUTO_ONLINE_PAYMENT;
$FLAG_MANUAL_LOCAL_BANK = LOCAL_BANK_OFFLINE;
if (class_exists('Payment_account')) {
	$FLAG_MANUAL_ONLINE_PAYMENT = Payment_account::FLAG_MANUAL_ONLINE_PAYMENT;
	$FLAG_AUTO_ONLINE_PAYMENT = Payment_account::FLAG_AUTO_ONLINE_PAYMENT;
	$FLAG_MANUAL_LOCAL_BANK = Payment_account::FLAG_MANUAL_LOCAL_BANK;
}
$depositMenuList = $this->utils->getDepositMenuList();
$paymentAccounts = $this->utils->getPaymentAccounts();
$collection_account = $this->uri->segment(4);

?>
<div class="list-group">
	<?php
if (!empty($paymentAccounts)) {
	foreach ($paymentAccounts as $id => $account) {
		if ($account['enabled']) {	?>
    <a href="<?=site_url('iframe_module/iframe_makeDeposit/' . $id)?>" class="deposit list-group-item <?php if(empty($collection_account)){ echo ($id==$flag)?'active':''; } ?>"><?=lang($account['lang_key']);?></a>
	<?php
}
	}
}
?>
	<?php
if (!empty($depositMenuList)) {

	foreach ($depositMenuList as $key => $list) {
		// $flag = $menuInfo->flag;// empty($menuInfo->external_system_id) ? $FLAG_MANUAL_ONLINE_PAYMENT : $FLAG_AUTO_ONLINE_PAYMENT;
		?>

    <a href="<?=site_url('iframe_module/iframe_makeDeposit/' . $list->flag . '/' . $list->bankTypeId)?>" class="deposit list-group-item <?= ($collection_account==$list->bankTypeId)?'active':'';?>"><?=lang($list->bankName);?></a>

	<?php
	}
}
?>
</div>
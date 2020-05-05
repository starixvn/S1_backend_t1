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
?>

<ul>
    <?php
        if (!empty($paymentAccounts)) {
            $segment = $this->uri->segment(3);
            foreach ($paymentAccounts as $id => $account) {
                if ($account['enabled']) {
    ?>
                    <li class="menu_jyclass <?= ($id==$segment)?'jy_cur':'';?>" vr="hn" onclick="return gotourl('<?=site_url('player_center/iframe_makeDeposit/' . $id)?>')">
                        <?=lang($account['lang_key']);?>
                    	<!-- <a href="<?=site_url('player_center/iframe_makeDeposit/' . $id)?>" class="deposit list-group-item <?= ($id==$segment)?'jy_cur':'';?>"><?=lang($account['lang_key']);?></a> -->
                    </li>
    <?php
                }
            }
        }
    ?>

    <?php
        if (!empty($depositMenuList)) {
            $segment = $this->uri->segment(4);
            foreach ($depositMenuList as $menuInfo) {
                $flag = $menuInfo->flag;
    ?>

                <li class="menu_jyclass <?=( $menuInfo->bankTypeId == $segment ) ? 'jy_cur' : ''?>" vr="hn" onclick="return gotourl('<?=site_url('player_center/iframe_makeDeposit/' . $flag . '/' . $menuInfo->bankTypeId)?>')">
                    <?=lang($menuInfo->bankName);?>
                	<!-- <a href="<?=site_url('player_center/iframe_makeDeposit/' . $flag . '/' . $menuInfo->bankTypeId)?>" class="deposit list-group-item <?=( $menuInfo->bankTypeId == $segment ) ? 'jy_cur' : ''?>"><?=lang($menuInfo->bankName);?></a> -->
                </li>
    <?php
            }
        }
    ?>

</ul>
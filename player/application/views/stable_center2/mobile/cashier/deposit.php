<?php
if( $this->config->item('disable_old_mobile_deposit_category_page') ) {
	include VIEWPATH . '/stable_center2/cashier/deposit.php';
}
else {
	redirect('/player_center2/deposit');
}
?>
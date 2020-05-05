    <h4><b><?=lang('cashier.116');?></b></h4>
    <div class="panel panel-default">
        <table class="table table-hover table-striped table-bordered" id="promoHistoryTable">
            <thead>
                <tr>
                    <!-- <th></th> -->
                    <th><?=lang('cashier.117');?></th>
                    <th><?=lang('cashier.137');?></th>
                    <th><?=lang('cashier.120');?></th>
                    <th><?=lang('cashier.121');?></th>
                    <th><?=lang('cashier.134');?></th>
                </tr>
            </thead>

            <tbody>
                <?php //var_dump($promoHistory);
if (!empty($promoHistory)) {
	?>
                    <?php foreach ($promoHistory as $row) {
		?>
                        <tr>
                            <!-- <td></td> -->
                            <td><?=$row['promoName']?></td>
                            <td><?=$row['dateApply']?></td>
                            <td><?php echo $row['bonusAmount']<=0 ? lang('Pending') : $row['bonusAmount'] ;?></td>

                            <td><?php if ($row['transactionStatus'] == 0) {
			//request promo
			echo lang('cashier.131');
		} elseif ($row['transactionStatus'] == 1) {
			//approved promo
			echo lang('cashier.123');
		} elseif ($row['transactionStatus'] == 2) {
			// declined cancellation
			echo lang('cashier.98');
        } elseif ($row['transactionStatus'] == 7) {
            echo lang('Approved Pre-application');
		} else {
			echo lang('cashier.133');
		}
		?>
                            </td>
                            <td><?php if ($row['cancelRequestStatus'] == 2 && $row['transactionStatus'] == 1) { //declined cancel reason ?>
                                        <?=$row['declinedCancelReason'] == '' ? lang('cashier.135') : $row['declinedCancelReason']?>
                                <?php } elseif ($row['cancelRequestStatus'] == 2 && $row['transactionStatus'] == 1) { //declined promo application ?>
                                        <?=$row['declinedApplicationReason'] == '' ? lang('cashier.135') : $row['declinedApplicationReason']?>
                                <?php } elseif (($row['transactionStatus'] == 0 || $row['transactionStatus'] == 1) || ($row['transactionStatus'] == 3 || $row['transactionStatus'] == 3)) { //request promo application ?>
                                        <i><?=lang('cashier.135')?></i>
                                <?php } elseif ($row['cancelRequestStatus'] == 0 && $row['transactionStatus'] == 2) {?>
                                        <?=$row['declinedApplicationReason'] == '' ? lang('cashier.135') : $row['declinedApplicationReason']?>
                                <?php }
		?>
                            </td>
                        </tr>
                    <?php }
	?>
                <?php } else {?>
                        <tr>
                            <td colspan="6" style="text-align:center"><span class="help-block"><?=lang('cashier.32');?></span></td>
                        </tr>
                <?php }
?>
            </tbody>
        </table>
    </div>

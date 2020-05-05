<style type="text/css">
    .popover {
        max-width: 100%;
    }
</style>

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th><?=lang('cms.promoname');?></th>
                <th><?=lang('cms.promocode');?></th>
                <th><?=lang('cashier.118');?></th>
                <th><?=lang('cms.bonusAmountReceived');?></th>
                <?php if ($this->config->item('SHOW_BET_AND_TARGET_TO_MEMBER')) {?>
                <th><?=lang('cashier.myBet');?></th>
                <th><?=lang('cashier.targetBet');?></th>
                <?php }
?>
            </tr>
        </thead>

        <tbody>
            <?php $ctr = 1;?>
            <?php if (!empty($playerpromo)) {
	foreach ($playerpromo as $row) {
		?>
                    <tr>
                    <td><a href="javascript:;" class="promo-details" data-toggle="popover" data-content="<?php echo htmlentities($row['promoDetails'], ENT_QUOTES, 'utf-8') ?>"><?php echo $row['fullPromoDesc']; ?></a></td>
                    <td><?=$row['promoCode']?></td>
                    <td><?=$row['dateProcessed']?></td>
                    <td><?=$row['bonusAmount']?></td>
                    <?php /*
		if ($this->config->item('SHOW_BET_AND_TARGET_TO_MEMBER')) {

		?>
		<td><?=empty(@$row['currentBet'][0]) || empty(@$row['currentBet'][0]->totalBetAmount) ? '<i>' . lang('player.noBetYet') . '</i>' : $row['currentBet'][0]->totalBetAmount;?></td>
		<?php
		if ($row['withdrawRequirementConditionType'] == 0) {?>
		<td><?=$row['bonusAmount']?></td>
		<?php } else {?>
		<td><?=($row['bonusAmount'] + $row['depositAmount']) * $row['withdrawRequirementBetCntCondition']?></td>
		<?php }
		?>
		<?php
		}*/
		?>
                    </tr>
                    <?php $ctr++;?>
                <?php }
	?>
            <?php } else {?>
                    <tr>
                        <td colspan="8" style="text-align:center"><span class="help-block"><?=lang('cashier.32');?></span></td>
                    </tr>
            <?php }
?>
        </tbody>
    </table>

    <a href="<?php echo site_url('iframe_module/iframe_viewCashier') ?>" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-circle-arrow-left"></span> <?=lang('button.back');?></a>
<script type="text/javascript">
    $(function() {
        $('.promo-details').popover({
            html: true,
            placement: 'bottom',
        });
    });
</script>
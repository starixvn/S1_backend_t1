<table class="table table-striped table-hover" id="bonusHistoryTable" style="margin: 0px 0 0 0; width: 100%;">
	<thead>
		<tr>
			<th></th>
            <th><?=lang('cms.promoname');?></th>
            <th><?=lang('cms.promocode');?></th>
            <th><?=lang('cashier.118');?></th>
            <th><?=lang('cms.bonusAmountReceived');?></th>
            <th><?=lang('cashier.myBet');?></th>
            <th><?=lang('cashier.targetBet');?></th>
		</tr>
	</thead>

	<tbody>
		<?php if (!empty($playerpromo)) {
	?>
			<?php foreach ($playerpromo as $playerpromo) {
		?>
				<tr>
					<td></td>
					<td><?=$playerpromo['promoName']?></td>
					<td><?=$playerpromo['promoCode']?></td>
					<td><?=$playerpromo['dateProcessed']?></td>
					<td><?=$playerpromo['bonusAmount']?></td>
					<td><?php if (!empty($playerpromo['currentBet']['totalBetAmount']) || $playerpromo['currentBet']['totalBetAmount']) {
			echo $playerpromo['currentBet']['totalBetAmount'];
		} else {
			echo lang('player.noBetYet');
		}
		?></td>
                                                    <?php
if ($playerpromo['withdrawRequirementConditionType'] == 0) {?>
                                                        <td><?=$playerpromo['bonusAmount']?></td>
                                                    <?php } else {?>
                                                        <td><?=($playerpromo['bonusAmount'] + $playerpromo['depositAmount']) * $playerpromo['withdrawRequirementBetCntCondition']?></td>
                                                    <?php }
		?>
				</tr>
		<?php
}
}
?>
	</tbody>
</table>
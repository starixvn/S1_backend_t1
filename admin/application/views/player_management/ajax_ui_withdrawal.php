<table class="table table-striped table-hover" id="withdrawalTable" style="margin: 0px 0 0 0; width: 100%;">
	<thead>
		<tr>
			<th></th>
			<th><?= lang('player.udw01'); ?></th>
			<th><?= lang('player.udw02'); ?></th>
			<th><?= lang('player.udw04'); ?></th>
			<th><?= lang('lang.status'); ?></th>
			<th><?= lang('player.udw05'); ?></th>
			<th><?= lang('player.udw06'); ?></th>
		</tr>
	</thead>

	<tbody>
		<?php if(!empty($withdrawal_history)) { ?>
			<?php foreach($withdrawal_history as $withdrawal_history) { ?>
				<tr>
					<td></td>
					<td><?= $withdrawal_history['dwDateTime']?></td>
					<td><?= $withdrawal_history['transactionCode']?></td>
					<td><?= $withdrawal_history['transactionType']?></td>
					<td><?= $withdrawal_history['dwStatus']?></td>
					<td><?= $withdrawal_history['amount']?></td>
					<td><?= (empty($withdrawal_history['notes'])) ? lang('player.udw07') : $withdrawal_history['notes'] ?></td>
				</tr>
		<?php 
				}
			} 
		?>
	</tbody>
</table>
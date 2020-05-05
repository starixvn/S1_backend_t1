<table class="table table-striped table-hover" id="depositTable" style="margin: 0px 0 0 0; width: 100%;">
	<thead>
		<tr>
			<th></th>
			<th><?= lang('player.udw01'); ?></th>
			<th><?= lang('player.udw02'); ?></th>
			<th><?= lang('player.udw03'); ?></th>
			<th><?= lang('lang.status'); ?></th>
			<th><?= lang('player.udw05'); ?></th>
			<th><?= lang('player.udw06'); ?></th>
		</tr>
	</thead>

	<tbody>
		<?php if(!empty($deposit_history)) { ?>
			<?php foreach($deposit_history as $deposit_history) { ?>
				<tr>
					<td></td>
					<td><?= $deposit_history['dwDateTime']?></td>
					<td><?= $deposit_history['transactionCode']?></td>
					<td><?= $deposit_history['transactionType']?></td>
					<td><?= $deposit_history['dwStatus']?></td>
					<td><?= $deposit_history['amount']?></td>
					<td><?= (empty($deposit_history['notes'])) ? lang('player.udw07') : $deposit_history['notes'] ?></td>
				</tr>
		<?php 
				}
			} 
		?>
	</tbody>
</table>
<div class="row">
	<div class="col-md-12">
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th><?= lang('pay.paytoption'); ?></th>
					<th><?= lang('pay.paymethod'); ?></th>
					<th><?= lang('pay.curr'); ?></th>
					<th><?= lang('pay.amt'); ?></th>
					<th><?= lang('lang.date'); ?></th>
				</tr>
			</thead>

			<tbody>
				<?php foreach ($transactions as $value) { 
						$date = new DateTime($value['date']); 
						
						if($value['status'] == 2) {
				?>
							<tr class="success">
				<?php
						} else if($value['status'] == 3) {
				?>
							<tr class="danger">
				<?php
						}
				?>

						<td><?= $value['period'] ?></td>
						<td><?= $value['paymentMethod'] ?></td>
						<td><?= $value['currency'] ?></td>
						<td><?= $value['amount'] ?></td>
						<td><?= date_format($date, 'Y-m-d') ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>
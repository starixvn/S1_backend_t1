<table class="table table-striped table-hover">
	<thead>
		<tr>
			<th><?= lang('lang.reqdate'); ?></th>
			<th><?= lang('pay.method'); ?></th>
			<th><?= lang('lang.amount'); ?></th>
			<!-- <th><?= lang('lang.transaction'); ?></th> -->
			<th><?= lang('lang.status'); ?></th>
			<th><?= lang('lang.comments'); ?></th>
		</tr>
	</thead>

	<tbody>
		<?php if(!empty($payments)) { ?>
			<?php foreach ($payments as $value) { ?>
				<tr>
					<td><?= date('Y-m-d', strtotime($value['createdOn'])) ?></td>
					<?php
						$account_number = $value['accountNumber'];
						$res = str_repeat('*', strlen($account_number) - 4) . substr($account_number, -4);
					?>
					<td><?= $value['paymentMethod'] . ": " . $res ?></td>
					<td><?= $value['amount'] ?></td>
					<!-- <td><?= $value['fee'] ?></td> -->
					<td>
						<?php 
							if ($value['status'] == '2') {
								echo 'approved';
							} else if ($value['status'] == '3') {
								echo 'declined';
							} else if ($value['status'] == '4') {
								echo 'cancelled';
							}
						?>
					</td>
					<td><?= ($value['reason'] == null) ? '<i>n/a</i>':$value['reason'] ?></td>
				</tr>
			<?php } ?>
		<?php } else { ?>
				<tr>
                    <td colspan="6" style="text-align:center"><span class="help-block">No Records Found</span></td>
				</tr>
		<?php } ?>
	</tbody>
</table>

<br/>

<div class="col-md-12 col-offset-0">
    <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul> 
</div>
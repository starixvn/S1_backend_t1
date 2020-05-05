<table class="table table-striped">
	<thead>
		<th><?= lang('aff.ai55'); ?></th>
		<th><?= lang('aff.ai56'); ?></th>
		<th><?= lang('aff.ai57'); ?></th>
		<th><?= lang('aff.ai58'); ?></th>
		<th><?= lang('aff.apay11'); ?></th>
	</thead>

	<tbody>
		<?php 
			if(!empty($payment)) {
				foreach ($payment as $key => $payment_value) { 
		?>
					<tr>
						<td><?= $payment_value['createdOn'] ?></td>
						<td><?= $payment_value['paymentMethod'] ?></td>
						<td><?= $payment_value['amount'] ?></td>
						<?php if ($payment_value['status'] == '0') { ?>
							<td><?= lang('aff.ai71'); ?></td>
						<?php } else if ($payment_value['status'] == '1') { ?>
							<td><?= lang('aff.ai72'); ?></td>
						<?php } else if ($payment_value['status'] == '2') { ?>
							<td><?= lang('aff.ai73'); ?></td>
						<?php } else if ($payment_value['status'] == '3') { ?>
							<td><?= lang('aff.ai74'); ?></td>
						<?php } else if ($payment_value['status'] == '4') { ?>
							<td><?= lang('aff.ai75'); ?></td>
						<?php } ?>
						<td><?= (empty($payment_value['reason'])) ? lang('player.ub12'):$payment_value['reason'] ?></td>
					</tr>
		<?php 
				}
			} else {
		?>
					<tr>
                        <td colspan="5" style="text-align:center"><span class="help-block"><?= lang('aff.ai60'); ?></span></td>
                    </tr>
		<?php
			}
		?>
	</tbody>
</table>

<br/>

<div class="col-md-12 col-offset-0">
    <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
</div>
<h2><?php echo lang('payment.success.title'); ?></h2>
<p><?php echo lang('payment.success.message'); ?></p>
<table class="table" width="100%">
	<tbody>
		<tr>
			<td width="20%"><?php echo lang('payment.success.bill'); ?></td>
			<td width="80%"><?php echo $callbackExtraInfo['billno'] ?></td></tr>
		<tr>
		<tr>
			<td><?php echo lang('payment.success.ips_bill'); ?></td>
			<td><?php echo $callbackExtraInfo['ipsbillno'] ?></td>
		</tr>
		<tr>
			<td><?php echo lang('payment.success.bank_bill'); ?></td>
			<td><?php echo $callbackExtraInfo['bankbillno'] ?></td>
		</tr>
		<tr>
			<td><?php echo lang('payment.success.amount'); ?></td>
			<td><?php echo $callbackExtraInfo['Currency_type'] . ' ' . number_format($transaction['amount'], 2) ?></td>
		</tr>
		<tr>
			<td><?php echo lang('payment.success.previous_balance'); ?></td>
			<td><?php echo $callbackExtraInfo['Currency_type'] . ' ' . number_format($transaction['before_balance'], 2) ?></td>
		</tr>
		<tr>
			<td><?php echo lang('payment.success.current_balance'); ?></td>
			<td><strong><?php echo $callbackExtraInfo['Currency_type'] . ' ' . number_format($transaction['after_balance'], 2) ?></strong></td>
		</tr>
		<?php if (isset($promo)): ?>
			<tr>
				<td><?php echo lang('payment.success.bouns_amount'); ?></td>
				<td><?php echo $callbackExtraInfo['Currency_type'] . ' ' . number_format($promo['amount'], 2) ?></td>
			</tr>
			<tr>
				<td><?php echo lang('payment.success.previous_balance'); ?></td>
				<td><?php echo $callbackExtraInfo['Currency_type'] . ' ' . number_format($promo['before_balance'], 2) ?></td>
			</tr>
			<tr>
				<td><?php echo lang('payment.success.current_balance'); ?></td>
				<td><strong><?php echo $callbackExtraInfo['Currency_type'] . ' ' . number_format($promo['after_balance'], 2) ?></strong></td>
			</tr>
			<tr>
				<td><?php echo lang('payment.success.status'); ?></td>
				<td class="text-success"><strong>Success <i class="glyphicon glyphicon-ok"></i></strong></td>
			</tr>
		<?php endif ?>
	</tbody>
</table>
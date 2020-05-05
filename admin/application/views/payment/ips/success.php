<h2>Payment Successful</h2>
<p>Congratulations! This is to confirm that your payment has been completed.</p>
<table class="table" width="100%">
	<tbody>
		<tr>
			<td width="20%">Bill no.</td>
			<td width="80%"><?php echo $callbackExtraInfo['billno'] ?></td></tr>
		<tr>
		<tr>
			<td>IPS Bill no.</td>
			<td><?php echo $callbackExtraInfo['ipsbillno'] ?></td>
		</tr>
		<tr>
			<td>Bank Bill no.</td>
			<td><?php echo $callbackExtraInfo['bankbillno'] ?></td>
		</tr>
		<tr>
			<td>Amount</td>
			<td><?php echo $callbackExtraInfo['Currency_type'] . ' ' . number_format($transaction['amount'], 2) ?></td>
		</tr>
		<tr>
			<td>Previous Balance</td>
			<td><?php echo $callbackExtraInfo['Currency_type'] . ' ' . number_format($transaction['before_balance'], 2) ?></td>
		</tr>
		<tr>
			<td>Current Balance</td>
			<td><strong><?php echo $callbackExtraInfo['Currency_type'] . ' ' . number_format($transaction['after_balance'], 2) ?></strong></td>
		</tr>
		<?php if (isset($promo)): ?>
			<tr>
				<td>Bonus Amount</td>
				<td><?php echo $callbackExtraInfo['Currency_type'] . ' ' . number_format($promo['amount'], 2) ?></td>
			</tr>
			<tr>
				<td>Previous Balance</td>
				<td><?php echo $callbackExtraInfo['Currency_type'] . ' ' . number_format($promo['before_balance'], 2) ?></td>
			</tr>
			<tr>
				<td>Current Balance</td>
				<td><strong><?php echo $callbackExtraInfo['Currency_type'] . ' ' . number_format($promo['after_balance'], 2) ?></strong></td>
			</tr>
			<tr>
				<td>Status</td>
				<td class="text-success"><strong>Success <i class="glyphicon glyphicon-ok"></i></strong></td>
			</tr>
		<?php endif ?>
	</tbody>
</table>
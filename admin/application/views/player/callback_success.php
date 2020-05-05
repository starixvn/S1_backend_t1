<html>
<head>
<title><?php echo lang('payment.success');?></title>
<link rel="stylesheet" type="text/css" href="<?=$this->utils->cssUrl('bootstrap.min.css')?>">
</head>
<body>
	<div class="container">
<h2><?php echo lang('payment.success.title');?></h2>
<p><?php echo lang('payment.success.message');?></p>
<table class="table" width="100%">
	<tbody>
		<tr>
			<td width="20%"><?php echo lang('payment.success.bill');?></td>
			<td width="80%"><?php echo $sale_order->secure_id;?></td></tr>
		<tr>
		<tr>
			<td><?php echo lang('payment.success.3rdpary_bill');?></td>
			<td><?php echo $sale_order->external_order_id;?></td>
		</tr>
		<tr>
			<td><?php echo lang('payment.success.bank_bill');?></td>
			<td><?php echo $sale_order->bank_order_id;?></td>
		</tr>
		<tr>
			<td><?php echo lang('payment.success.amount');?></td>
			<td><?php echo $this->utils->formatCurrency($sale_order->amount);?></td>
		</tr>
<!-- 		<tr>
			<td><?php //echo lang('payment.success.previous_balance');?></td>
			<td><?php //echo $this->utils->formatCurrency($transaction->before_balance);?></td>
		</tr>
		<tr>
			<td><?php //echo lang('payment.success.current_balance');?></td>
			<td><strong><?php //echo $this->utils->formatCurrency($transaction->after_balance);?></strong></td>
		</tr> -->
		<?php if (isset($promo_trans) && $promo_trans) {?>
			<tr>
				<td><?php echo lang('payment.success.bouns_amount');?></td>
				<td><?php echo $this->utils->formatCurrency($promo_trans->amount);?></td>
			</tr>
			<tr>
				<td><?php echo lang('payment.success.previous_balance');?></td>
				<td><?php echo $this->utils->formatCurrency($promo_trans->before_balance);?></td>
			</tr>
			<tr>
				<td><?php echo lang('payment.success.current_balance');?></td>
				<td><strong><?php echo $this->utils->formatCurrency($promo_trans->after_balance);?></strong></td>
			</tr>
			<tr>
				<td><?php echo lang('payment.success.status');?></td>
				<td class="text-success"><strong><?php echo lang('Success')?> <i class="glyphicon glyphicon-ok"></i></strong></td>
			</tr>
		<?php }
?>
	</tbody>
</table>
		<a href="<?php echo site_url($next_url)?>" class="btn btn-danger"><?php echo lang('button.back');?></a>
	</div>
</body>
</html>

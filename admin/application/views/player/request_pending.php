<html>
<head>
<title><?php echo lang('payment.pending');?></title>
<link rel="stylesheet" type="text/css" href="<?=$this->utils->cssUrl('bootstrap.min.css')?>">
</head>
<body>
	<div class="container">
<h2><?php echo lang('payment.pending.title');?></h2>
<p><?php echo lang('payment.pending.message');?></p>
<table class="table" width="100%">
	<tbody>
		<tr>
			<td width="20%"><?php echo lang('payment.pending.bill');?></td>
			<td width="80%"><?php echo $sale_order->secure_id;?></td></tr>
		<tr>
		<tr>
			<td><?php echo lang('payment.pending.3rdpary_bill');?></td>
			<td><?php echo $sale_order->external_order_id;?></td>
		</tr>
		<tr>
			<td><?php echo lang('payment.pending.bank_bill');?></td>
			<td><?php echo $sale_order->bank_order_id;?></td>
		</tr>
		<tr>
			<td><?php echo lang('payment.pending.amount');?></td>
			<td><?php echo $this->utils->formatCurrency($sale_order->amount);?></td>
		</tr>
	</tbody>
</table>
		<a href="<?php echo site_url($next_url)?>" class="btn btn-danger"><?php echo lang('button.back');?></a>
	</div>
</body>
</html>

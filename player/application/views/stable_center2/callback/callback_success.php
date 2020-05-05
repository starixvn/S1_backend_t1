<?php
    $this->load->view($this->utils->getPlayerCenterTemplate().'/cashier/content_menu');
?>

<div class="qbmain" id="qbmain_zz">

        <!-- 微信 -->

        <div class="deposit_content" id="deposit_content" style="position: relative;">
                
<div class="page__content page__content--alt" >

		<br/>
		<p style="margin-left: 30px;"><?php echo lang('payment.success.message'); ?></p>

		<div style="margin: 30px;">
			<table border="1" width="400">
				<tbody>
					<tr>
						<td width="20%" style="padding: 5px;"><?php echo lang('payment.success.bill'); ?></td>
						<td width="80%" style="padding: 5px;"><?php echo $sale_order->secure_id; ?></td></tr>
					<tr>
					<tr>
						<td style="padding: 5px;"><?php echo lang('payment.success.3rdpary_bill'); ?></td>
						<td style="padding: 5px;"><?php echo $sale_order->external_order_id; ?></td>
					</tr>
					<tr>
						<td style="padding: 5px;"><?php echo lang('payment.success.bank_bill'); ?></td>
						<td style="padding: 5px;"><?php echo $sale_order->bank_order_id; ?></td>
					</tr>
					<tr>
						<td style="padding: 5px;"><?php echo lang('payment.success.amount'); ?></td>
						<td style="padding: 5px;"><?php echo $this->utils->displayCurrency($transaction->amount); ?></td>
					</tr>
					<tr>
						<td style="padding: 5px;"><?php echo lang('payment.success.previous_balance'); ?></td>
						<td style="padding: 5px;"><?php
	if (!empty($transaction)) {
		echo $this->utils->displayCurrency($transaction->before_balance);
	}
	?></td>
					</tr>
					<tr>
						<td style="padding: 5px;"><?php echo lang('payment.success.current_balance'); ?></td>
						<td style="padding: 5px;"><strong><?php
	if (!empty($transaction)) {
		echo $this->utils->displayCurrency($transaction->after_balance);
	}?></strong></td>
					</tr>
					<?php if (isset($promo_trans) && $promo_trans) {?>
						<tr>
							<td style="padding: 5px;"><?php echo lang('payment.success.bouns_amount'); ?></td>
							<td style="padding: 5px;"><?php echo $this->utils->displayCurrency($promo_trans->amount); ?></td>
						</tr>
						<tr>
							<td style="padding: 5px;"><?php echo lang('payment.success.previous_balance'); ?></td>
							<td style="padding: 5px;"><?php echo $this->utils->displayCurrency($promo_trans->before_balance); ?></td>
						</tr>
						<tr>
							<td style="padding: 5px;"><?php echo lang('payment.success.current_balance'); ?></td>
							<td style="padding: 5px;"><strong><?php echo $this->utils->displayCurrency($promo_trans->after_balance); ?></strong></td>
						</tr>
						<tr>
							<td style="padding: 5px;"><?php echo lang('payment.success.status'); ?></td>
							<td class="text-success"><strong>Success <i class="glyphicon glyphicon-ok"></i></strong></td>
						</tr>
					<?php }
	?>
				</tbody>
			</table>
		</div>

		
		<br/>
		
</div>
</div>
</div>
<div class="container">
	<br/>

	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h4 class="panel-title pull-left"><?=lang('pay.areq');?> </h4>
					<div class="clearfix"></div>
				</div>

				<div class="panel panel-body" id="change_password_panel_body">
					<form method="POST" action="<?=site_url('affiliate/newRequests')?>" accept-charset="utf-8">
						<div class="row">
								<div class="col-md-12 well">
									<center>
										<h3>
											<?php echo lang('Wallet'); ?>:
											<strong>
											<?php echo $this->utils->formatCurrencyNoSym($affiliate['wallet_balance']); ?>
											</strong>
											<span style="font-size: smaller;margin-left: 10px;">
											<?php echo lang('Pending withdraw amount'); ?>:
											<strong><?php echo $this->utils->formatCurrencyNoSym($affiliate['frozen']); ?></strong>
											</span>
										</h3>
									</center>
									<br>
								</div>
 								<div class="col-md-6">
									<div class="col-md-4 col-md-offset-0">
										<label for="request_amount"><?=lang('pay.reqamt');?>: </label>
									</div>

									<div class="col-md-8 col-md-offset-0">
										<input type="text" name="request_amount" required="required" class="form-control input-sm number_only" />
										<label style="color: red; font-size: 12px;"><?php echo form_error('request_amount'); ?></label>
									</div>
								</div>
 								<div class="col-md-6">
									<div class="col-md-4 col-md-offset-0">
										<label for="payment_method"><?=lang('pay.method');?>: </label>
									</div>

									<div class="col-md-8 col-md-offset-0">
										<select name="payment_method" id="payment_method" required="required" class="form-control input-sm" onchange="setModify(this.value);">
											<option value=""><?=lang('pay.sel');?></option>
											<?php if (!empty($payment_methods)) {
	?>
												<?php foreach ($payment_methods as $key => $value) {
		$account_number = $value['accountNumber'];
		$res = str_repeat('*', strlen($account_number) - 4) . substr($account_number, -4);
		?>
														<option value="<?=$value['affiliatePaymentId']?>"><?=$value['bankName'] . ": " . $res?></option>
												<?php }?>
											<?php }?>
										</select>
										<p class="help-block">
											<a href="<?=site_url('affiliate/modifyPayment/0')?>" id="modify"><?=lang('pay.modify');?></a>
											|
											<a href="<?=site_url('affiliate/addNewPayment')?>" class="text-info"><?=lang('pay.reg');?></a>
										</p>
										<label style="color: red; font-size: 12px;"><?php echo form_error('payment_method'); ?></label>
									</div>
								</div>
								<div class="col-md-12">
									<center>
										<input type="submit" name="submit" id="submit" class="btn btn-info" value="<?=lang('pay.sub');?>">
									</center>
								</div>

							</div>
					</form>
					<br/>

					<div class="row table-responsive">
						<div class="col-md-12">
							<table class="table table-striped table-hover" id="cashierTable" style="width:100%">
								<thead>
									<th></th>
									<th class="input-sm"><?=lang('Date');?></th>
									<th class="input-sm"><?=lang('Processed Date');?></th>
									<th class="input-sm"><?=lang('Bank');?></th>
									<th class="input-sm"><?=lang('Amount');?></th>
									<th class="input-sm"><?=lang('Status');?></th>
									<th class="input-sm"><?=lang('Reason');?></th>
								</thead>

								<tbody>
									<?php

if (!empty($payment_histories)) {
	foreach ($payment_histories as $row) {
		?>
									<tr>
										<td></td>
										<td class="input-sm"><?php echo $row['createdOn']; ?></td>
										<td class="input-sm"><?php echo $row['processedOn']; ?></td>
										<td class="input-sm"><?php echo $row['paymentMethod']; ?></td>
										<td class="input-sm"><?php echo $row['amount']; ?></td>
										<td class="input-sm"><?php

		switch ($row['status']) {
		case Affiliatemodel::STATUS_WITHDRAW_REQUEST:
			echo lang('Request');
			break;
		case Affiliatemodel::STATUS_WITHDRAW_APPROVED:
			echo '<div class="text-info">' . lang('Approved') . '</div>';
			break;
		case Affiliatemodel::STATUS_WITHDRAW_DECLINED:
			echo '<div class="text-danger">' . lang('Declined') . '</div>';
			break;
		}

		?></td>
										<td class="input-sm"><?php echo $row['reason']; ?></td>
										</td>
									</tr>
										<?php

	}
}
?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<script type="text/javascript">


    $(document).ready(function() {
        $('#cashierTable').DataTable( {
            // "responsive": {
            //     details: {
            //         type: 'column'
            //     }
            // },
            "columnDefs": [ {
                className: 'control',
                orderable: false,
                targets:   0
            },
              { className: 'text-right', targets: [ 4 ] },
            ],
            "order": [ 1, 'desc' ]
        } );
    } );
</script>
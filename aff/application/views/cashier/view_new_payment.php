<div class="container">
	<br/>

	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
				<div class="nav-head panel-heading">
					<h4 class="panel-title pull-left"><?=lang('pay.areq');?> </h4>
					<div class="clearfix"></div>
				</div>
				<div class="panel panel-body" id="change_password_panel_body">
					<?php if ( ! $this->utils->is_readonly()): ?>
						<form method="POST" action="<?=site_url('affiliate/newRequests')?>" accept-charset="utf-8" class="form-horizontal" data-toggle="validator">
							<div class="row">
								<div class="col-md-12 well">
									<center>
										<h3>
											<?php echo lang('Wallet'); ?>:
											<strong>
											<?php echo $this->utils->formatCurrencyNoSym($affiliate['wallet_balance']); ?>
											</strong>
											<small>
											<?php echo lang('Pending withdraw amount'); ?>:
											<strong><?php echo $this->utils->formatCurrencyNoSym($affiliate['frozen']); ?></strong>
											</small>
											<small>
											<?php echo lang('Balance Wallet'); ?>:
											<strong><?php echo $this->utils->formatCurrencyNoSym($affiliate['wallet_hold']); ?></strong>
											</small>
									<input type="button" class="btn-hov btn btn-info btn-sm btn_transfer_bal_to_main" value="<?php echo lang('Transfer Balance To Main'); ?>">
									<input type="button" class="btn-hov-invert btn btn-warning btn-sm btn_transfer_bal_from_main" value="<?php echo lang('Transfer Balance From Main'); ?>">
										</h3>
									</center>
									<br>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="request_amount" class="col-md-4 control-label"><?=lang('pay.reqamt');?>:</label>
										<div class="col-md-8">
											<input type="number" name="request_amount" class="form-control input-sm"
												   required="required" oninvalid="this.setCustomValidity('<?=lang('default_html5_required_error_message')?>')" oninput="setCustomValidity('')"
												   min="<?=$min_withdraw_amount?>" data-min-error="<?=sprintf(lang('Minimum withdrawal amount'), $min_withdraw_amount);?>"/>
											<div class="help-block with-errors"><?php echo form_error('request_amount'); ?></div>
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="payment_method" class="col-md-4 control-label"><?=lang('pay.method');?>:</label>
										<div class="col-md-8">
											<select name="payment_method" id="payment_method" required="required" class="form-control input-sm" onchange="setModify(this.value);" oninvalid="this.setCustomValidity('<?=lang('select_required_error_message')?>')" oninput="setCustomValidity('')">
												<option value=""><?=lang('pay.sel');?></option>
												<?php if (!empty($payment_methods)) { ?>
													<?php foreach ($payment_methods as $key => $value) {
														$account_number = $value['accountNumber'];
														$res = str_repeat('*', strlen($account_number) - 4) . substr($account_number, -4);
													?>
															<option value="<?=$value['affiliatePaymentId']?>"><?=lang($value['bankName']) . ": " . $res?></option>
													<?php }?>
												<?php }?>
											</select>
											<div class="help-block with-errors"><?php echo form_error('payment_method'); ?></div>

											<p class="help-block">
												<!-- OGP-1087 <a href="<?=site_url('affiliate/modifyPayment/0')?>" id="modify" ><?=lang('pay.modify');?></a> | -->
												<a href="<?=site_url('affiliate/addNewPayment')?>" class="text-info link-hov"><?=lang('pay.reg');?></a>
											</p>

										</div>
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
					<?php endif ?>

					<div class="row table-responsive">
						<div class="col-md-12">
							<table class="table table-striped table-hover" id="cashierTable" style="width:100%">
								<thead>
									<tr>
										<th></th>
									<th class="input-sm"><?=lang('Date');?></th>
									<th class="input-sm"><?=lang('Transaction Type');?></th>
									<th class="input-sm"><?=lang('Amount');?></th>
									<th class="input-sm"><?=lang('Before Balance');?></th>
									<th class="input-sm"><?=lang('After Balance');?></th>
    								<?php if ( ! $this->utils->isEnabledFeature('aff_hide_changed_balance_in_cashier')): # OGP-6758 ?>
										<th class="input-sm"><?=lang('Changed Balance');?></th>
									<?php endif ?>
    								<?php if ( ! $this->utils->isEnabledFeature('aff_hide_payment_request_notes_in_cashier')): # OGP-6446 ?>
										<th class="input-sm"><?=lang('Notes');?></th>
									<?php endif ?>
									</tr>
								</thead>

								<tbody>
									<?php
									if (!empty($payment_histories)) {
										foreach ($payment_histories as $row) {
									?>
											<tr>
												<td></td>
												<td class="input-sm"><?php echo $row['created_at']; ?></td>
												<td class="input-sm"><?php echo lang('transaction.transaction.type.' . $row['transaction_type']); ?></td>
												<td class="input-sm"><?php echo $this->utils->formatCurrencyNoSym($row['amount']); ?></td>
												<td class="input-sm"><?php echo $this->utils->formatCurrencyNoSym($row['before_balance']); ?></td>
												<td class="input-sm"><?php echo $this->utils->formatCurrencyNoSym($row['after_balance']); ?></td>
    											<?php if ( ! $this->utils->isEnabledFeature('aff_hide_changed_balance_in_cashier')): # OGP-6758 ?>
													<td class="input-sm">
														<a href="javascript:void(0)" onclick="popupBalInfo(<?php echo $row['id']; ?>)" class="btn btn-primary btn-sm">
															<?php echo lang('Show'); ?>
														</a>
														<div id="bal_info_<?php echo $row['id']; ?>" style="display:none"><?php echo $row['changed_balance']; ?></div>
													</td>
												<?php endif ?>
                								<?php if ( ! $this->utils->isEnabledFeature('aff_hide_payment_request_notes_in_cashier')): # OGP-6446 ?>
													<td class="input-sm"><?php echo $row['note']; ?></td>
												<?php endif ?>
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

<div class="modal fade" tabindex="-1" data-backdrop="static" role="dialog" aria-hidden="true" id="bal_info_dialog">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <table class="bal_details table table-striped">
          <thead><th><?php echo lang('Wallet'); ?></th><th><?php echo lang('Before'); ?></th><th><?php echo lang('After'); ?></th></thead>
          <tbody></tbody>
        </table>
        <pre class='hide'>
          <code class='json'>
          </code>
        </pre>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo lang('Close'); ?></button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
function addBalDetail(tbody,name, bAmount, aAmount){
    var bVal=0;
    var aVal=0;
    bVal=parseFloat(bAmount);
    if(bVal && !isNaN(bVal)){
      bVal=bVal.toFixed(2);
    }else{
      bVal=bAmount;
    }
    aVal=parseFloat(aAmount);
    if(aVal && !isNaN(aVal)){
      aVal=aVal.toFixed(2);
    }else{
      aVal=aAmount;
    }
    var css='';
    if(bVal!=aVal){
      css='class="danger"';
    }
    tbody.append('<tr '+css+' ><td>'+name+'</td><td>'+bVal+'</td><td>'+aVal+'</td></tr>');

}
function popupBalInfo(transId){
  var input=$('#bal_info_'+transId).text();
  var output=$("#bal_info_dialog .modal-body pre code");
  var tbody=$('table.bal_details tbody');
  tbody.html('');
  if(input.trim()!=''){
    var balDetails=JSON.parse(input);
    var beforeBalDetails=balDetails['before'];
    var afterBalDetails=balDetails['after'];
    //add main wallet
    addBalDetail(tbody,"<?php echo lang('Main Wallet'); ?>",beforeBalDetails['main_wallet'],afterBalDetails['main_wallet']);
    //add frozen
    addBalDetail(tbody,"<?php echo lang('Frozen'); ?>",beforeBalDetails['frozen'],afterBalDetails['frozen']);

    $(beforeBalDetails['sub_wallet']).each(function(index,item){
      addBalDetail(tbody,item['game'],item['totalBalanceAmount'],afterBalDetails['sub_wallet'][index]['totalBalanceAmount']);
    })
    //add total_balance
    addBalDetail(tbody,"<?php echo lang('Total'); ?>",beforeBalDetails['total_balance'],afterBalDetails['total_balance']);

    var node = JSON.stringify( balDetails , null, 2);
    output.html(node);
  }
  // $("#bal_info_dialog .modal-body pre code").html($('#bal_info_'+transId).html());
  // $('pre code').each(function(i, block) {
  //   hljs.highlightBlock(block);
  // });

  $("#bal_info_dialog").modal('show');
}
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

        $('.btn_transfer_bal_to_main').click(function(){
	        BootstrapDialog.confirm('<?php echo lang('Do you want transfer all balance to main wallet?'); ?>', function(result){
	            if(result) {
			       	window.location.href='<?php echo site_url("/affiliate/affiliate_transfer_bal_to_main"); ?>';
	            }
	        });

        });
        $('.btn_transfer_bal_from_main').click(function(){
	        BootstrapDialog.confirm('<?php echo lang('Do you want transfer all main wallet to balance wallet?'); ?>', function(result){
	            if(result) {
			       	window.location.href='<?php echo site_url("/affiliate/affiliate_transfer_bal_from_main"); ?>';
	            }
	        });

        });

    } );
</script>
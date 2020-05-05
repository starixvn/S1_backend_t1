<div class="container">
	<br/>

	<div class="row">
		<div class="col-md-12" id="toggleView">
			<div class="panel panel-primary">
				<div class="nav-head panel-heading">
					<h4 class="panel-title pull-left"> <?=lang('nav.transaction');?> </h4>
					<!-- <a href="#" class="btn btn-primary btn-sm pull-right" id="view_affiliate"><span class="glyphicon glyphicon-remove"></span></a> -->
					<div class="clearfix"></div>
				</div>

				<div class="panel panel-body table-responsive" id="affiliate_panel_body">

						<div class="col-md-12" id="view_payments" style="margin: 30px 0 0 0;">
							<table class="table table-striped table-hover" id="paymentTable" style="width: 100%">
								<thead>
									<tr>
										<th></th>
									<th class="input-sm"><?=lang('Date');?></th>
									<th class="input-sm"><?=lang('Transaction Type');?></th>
									<th class="input-sm"><?=lang('Amount');?></th>
									<th class="input-sm"><?=lang('Before Balance');?></th>
									<th class="input-sm"><?=lang('After Balance');?></th>
									<th class="input-sm"><?=lang('Changed Balance');?></th>
									<th class="input-sm"><?=lang('Notes');?></th>
									</tr>
								</thead>

								<tbody>
									<?php

if (!empty($transactions)) {
	foreach ($transactions as $row) {
		?>
											<tr>
												<td></td>
												<td class="input-sm"><?php echo $row['created_at']; ?></td>
												<td class="input-sm"><?php echo lang('transaction.transaction.type.' . $row['transaction_type']); ?></td>
												<td class="input-sm"><?php echo $row['amount']; ?></td>
												<td class="input-sm"><?php echo $row['before_balance']; ?></td>
												<td class="input-sm"><?php echo $row['after_balance']; ?></td>
												<td class="input-sm">
												<a href="javascript:void(0)" onclick="popupBalInfo(<?php echo $row['id']; ?>)" class="btn btn-primary btn-sm"><?php echo lang('Show'); ?></a>
												<div id="bal_info_<?php echo $row['id']; ?>" style="display:none"><?php echo $row['changed_balance']; ?></div>
												<td class="input-sm"><?php echo $row['note']; ?></td>
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

				<div class="panel-footer">

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
  $('pre code').each(function(i, block) {
    hljs.highlightBlock(block);
  });

  $("#bal_info_dialog").modal('show');
}
    $(document).ready(function() {
        $('#paymentTable').DataTable( {
            // "responsive": {
            //     details: {
            //         type: 'column'
            //     }
            // },
            "columnDefs": [ {
                className: 'control',
                orderable: false,
                targets:   0
            } ],
            "order": [ 1, 'desc' ]
        } );
    } );
</script>
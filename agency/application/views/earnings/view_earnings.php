<div class="container">
	<br/>
	<!-- Monthly Earnings -->
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h4 class="panel-title"><?=lang('aff.ai42');?></h4>
		</div>

		<div class="panel-body affae_panel_body table-responsive">
			<div class="col-md-12" id="monthlyEarnings" style="margin: 30px 0 0 0;">
				<table class="table table-striped table-hover" id="earningsTable">
					<thead>
						<th></th>
						<th><?=lang('lang.yearmonth');?></th>
						<th><?=lang('earn.actplayers');?></th>
						<th><?=lang('traffic.totalplayers');?></th>
						<th><?=lang('earnings.gross');?></th>
						<th><?=lang('earnings.fee');?></th>
						<th><?=lang('earnings.net');?></th>
						<th><?=lang('aff.ts02');?></th>
						<th><?=lang('Amount');?></th>
						<th><?=lang('lang.status');?></th>
						<th><?=lang('aff.ai49');?></th>
					</thead>

					<tbody>
						<?php
if (!empty($earnings)) {
	foreach ($earnings as $e) {
		?>
								<tr>
									<td></td>
									<td><?=$e->year_month;?></td>
									<td><?=$e->active_players;?></td>
									<td><?=$e->count_players;?></td>
									<td><?=$e->gross_net;?></td>
									<td><?=$e->bonus_fee + $e->transaction_fee + $e->cashback + $e->admin_fee;?></td>
									<td><?=$e->net;?></td>
									<td><?=$e->rate_for_affiliate;?></td>
									<td><?=$e->amount;?></td>
									<td><?php if ($e->paid_flag == 0) {
			echo lang('Unpaid');
		} else {
			echo lang('Paid');
		}
		?></td>
									<td><?=$e->note;?></td>
								</tr>
							<?php }?>
						<?php }?>
					</tbody>
				</table>
			</div>

		</div>
	</div>
	<!-- End of Monthly Earnings -->


</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#earningsTable').DataTable( {
            // "responsive": {
            //     details: {
            //         type: 'column'
            //     }
            // },
	        autoWidth: true,
            "columnDefs": [ {
                className: 'control',
                orderable: false,
                targets:   0
            } ],
            "order": [ 1, 'asc' ]
        } );

        $('#reportrange').data('daterangepicker').remove();
    } );
</script>
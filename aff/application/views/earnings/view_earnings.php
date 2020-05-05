<?php
$enable_tier = false;
if(isset($commonSettings['enable_commission_by_tier']) && $commonSettings['enable_commission_by_tier'] == true){
    $enable_tier = true;
}
?>
<div class="container">
	<?php if ($this->utils->isEnabledFeature('switch_to_affiliate_platform_earnings')): ?>

		<form class="form-horizontal" id="search-form">

			<input type="hidden" name="affiliate_id" value="<?=$conditions['affiliate_id']?>"/>

		    <div class="panel panel-primary">
		        <div class="panel-heading">
		            <h4 class="panel-title">
		                <i class="fa fa-search"></i> <?=lang("lang.search")?>
		                <span class="pull-right">
		                    <a data-toggle="collapse" href="#collapseViewGameLogs"
		                        class="btn btn-info btn-xs <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>">
		                    </a>
		                </span>
		            </h4>
		        </div>

		        <div id="collapseViewGameLogs" class="panel-collapse <?=$this->config->item('default_open_search_panel') ? '' : 'collapse in'?>">
		            <div class="panel-body">
		                <div class="col-md-6">
		                    <label class="control-label" for="search_game_date"><?=lang('report.sum02');?></label>
		                    <input id="search_game_date" class="form-control input-sm dateInput" data-start="#start_date" data-end="#end_date"/>
		                    <input type="hidden" id="start_date" name="start_date" value="<?php echo $conditions['start_date']; ?>" />
		                    <input type="hidden" id="end_date" name="end_date"  value="<?php echo $conditions['end_date']; ?>"/>
		                </div>

		                <div class="col-md-6">
		                    <label class="control-label" for="game_platform_id"><?=lang('player.ui29');?> </label>
		                    <div class="row">
		                        <?php foreach ($game_platforms as $game_platform) {?>
			                    	<div class="col-md-4">
					                    <label>
					                    	<input type="checkbox" name="game_platform_id[]" value="<?=$game_platform['id']?>" <?=in_array($game_platform['id'], $conditions['game_platform_id']) ? 'checked="checked"' : ''?>>
					                    	<?=$game_platform['system_code'];?>
					                    </label>
				                    </div>
		                        <?php }?>
		                    </div>
		                </div>

		            </div>
		            <div class="panel-footer text-right">
		                <input type="submit" class="btn btn-primary btn-sm" id="btn-submit" value="<?php echo lang('Search'); ?>" >
		            </div>
		        </div>
		    </div>

		</form>
	<?php endif ?>
	<!-- Monthly Earnings -->
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h4 class="panel-title"><?=lang('Affiliate Earnings');?></h4>
		</div>

		<div class="panel-body affae_panel_body table-responsive">
			<div class="col-md-12" id="monthlyEarnings" style="margin: 30px 0 0 0;">
				<table class="table table-striped table-hover" id="earningsTable">
					<?php if ($this->utils->isEnabledFeature('switch_to_affiliate_platform_earnings')): ?>
						<thead>
		                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Action')?></th>
		                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Affiliate Username')?></th>
							<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Game Platform')?></th>
		                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Period')?></th>
		                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Date')?></th>
		                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Game Platform Fee')?></th>
		                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Gross Revenue')?></th>
		                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Admin Fee')?></th>
		                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Bonus Fee')?></th>
		                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Cashback Fee')?></th>
		                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Transaction Fee')?></th>
		                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Total Fee')?></th>
							<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Net Revenue')?></th>
							<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Commission Rate')?></th>
		                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Commission Amount')?></th>
		                    <th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Status')?></th>
							<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Manual Adjustment')?></th>
						</thead>
						<tbody></tbody>
					<?php else: ?>
						<thead>
							<th></th>
							<th><?=lang('lang.yearmonth');?></th>
							<th><?=lang('earn.actplayers');?></th>
							<th><?=lang('traffic.totalplayers');?></th>
							<th><?=lang('earnings.gross');?></th>
							<th><?=lang('Platform Fee');?></th>
							<th><?=lang('Admin Fee');?></th>
							<th><?=lang('Bonus Fee');?></th>
							<th><?=lang('Cashback Fee');?></th>
							<th><?=lang('Transaction Fee');?></th>
							<th><?=lang('earnings.fee');?></th>
							<th><?=lang('earnings.net');?></th>
							<th><?=lang('aff.ts02');?></th>
							<th><?=lang('Commission Amount');?></th>
							<th><?=lang('Commission From Sub-affiliates');?></th>
							<th><?=lang('Total Commission');?></th>
							<!-- <th><?=lang('lang.status');?></th> -->
							<th><?=lang('aff.ai49');?></th>
						</thead>
						<tbody>
							<?php if (!empty($earnings)) { ?>
								<?php foreach ($earnings as $e) { ?>
									<tr>
										<td></td>
										<td><?=$e->date;?></td>
										<td><?=$e->active_players;?></td>
										<td><?=$e->count_players;?></td>
										<td><?=$this->utils->formatCurrencyWithTwoDecimal($e->gross_net);?></td>
										<td><?=$this->utils->formatCurrencyWithTwoDecimal($e->platform_fee);?></td>
										<td><?=$this->utils->formatCurrencyWithTwoDecimal($e->admin_fee);?></td>
										<td><?=$this->utils->formatCurrencyWithTwoDecimal($e->bonus_fee);?></td>
										<td><?=$this->utils->formatCurrencyWithTwoDecimal($e->cashback);?></td>
										<td><?=$this->utils->formatCurrencyWithTwoDecimal($e->transaction_fee);?></td>
										<td><?=$this->utils->formatCurrencyWithTwoDecimal($e->total_fee);?></td>
										<td><?=$this->utils->formatCurrencyWithTwoDecimal($enable_tier == true ? $e->total_net_revenue : $e->net);?></td>
										<td><?php
                                            $rates = $e->rate_for_affiliate;
                                            if($enable_tier == true){
                                                if(!empty($e->commission_amount_breakdown)){
                                                    $breakdown = end(json_decode($e->commission_amount_breakdown, true));
                                                    $rates = $breakdown['rate'];
                                                }
                                            }
                                            echo $this->utils->formatCurrencyWithTwoDecimal($rates);

                                            ?>
                                        </td>
										<td><?=$this->utils->formatCurrencyWithTwoDecimal($enable_tier == true ? $e->commission_amount_by_tier : $e->amount);?></td>
										<td><?=$this->utils->formatCurrencyWithTwoDecimal($e->commission_from_sub_affiliates);?></td>
										<td><?=$this->utils->formatCurrencyWithTwoDecimal($e->total_commission);?></td>
										<!-- <td><?=$e->paid_flag == 0 ? lang('Unpaid') : lang('Paid');?></td> -->
										<td nowrap="nowrap" style="white-space: nowrap;">
											<?=$e->note;?>
											<?php if ( ! empty($e->adjustment_notes)): ?>
												<br>
												<strong>Adjustment Notes: </strong>
												<?=$e->adjustment_notes;?>
											<?php endif ?>
										</td>
									</tr>
								<?php }?>
							<?php }?>
						</tbody>
					<?php endif ?>
				</table>
			</div>

		</div>
	</div>
	<!-- End of Monthly Earnings -->


</div>
<script type="text/javascript">
    $(document).ready(function() {

		<?php if ($this->utils->isEnabledFeature('switch_to_affiliate_platform_earnings')): ?>
	    	$('#earningsTable').DataTable( {
	            searching: false,
	            processing: true,
	            serverSide: true,
	            lengthMenu: [ 10, 25, 50, 75, 100, 5000 ],
	            ajax: function (data, callback, settings) {

	                data.extra_search = $('#search-form').serializeArray();

	                $.post('/api/aff_user_earnings_3', data, function(data) {

	                    callback(data);

	                }, 'json');

	            },
	            columnDefs: [
	                // { sortable: false, targets: [ 0 ] },
	                { visible: false, targets: [ 0,1,16 ] },
	                // { className: 'text-right', targets: [ 2,3,4,5,6,7,8,9,10,11,12,13,14,15,16 ] },
	            ],
	            order: [ 1, 'desc' ]
	        } );
		<?php else: ?>
			$('#earningsTable').DataTable( {
				autoWidth: true,
				columnDefs: [ {
					className: 'control',
					orderable: false,
					targets:   0
				} ],
				order: [ 1, 'desc' ]
			} );
		<?php endif ?>


        //$('#reportrange').data('daterangepicker').remove();
    } );
</script>
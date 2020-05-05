<style type="text/css">
	.container .bootstrap-switch, .container .bootstrap-switch-label, .container .bootstrap-switch-handle-off, .container .bootstrap-switch-handle-on{
		height: auto;
	}
</style>
<div class="container">
	<br/>

	<div class="row">
		<div class="col-md-12" id="toggleView">
			<div class="panel panel-primary">
				<div class="nav-head panel-heading">
					<h4 class="panel-title pull-left"> <?php echo $title;?></h4>
					<div class="clearfix"></div>
				</div>

				<div class="panel panel-body" id="affiliate_panel_body">
					<div class="panel panel-default">
						<div class="panel-heading">
							<form name="search-form" id="search-form" method="GET" action="<?php echo site_url('/affiliate/player_stats'); ?>">
					            <div class="col-md-12 searchOptions">
					            	<div class="col-md-6">
						            	<label for="period" class="control-label"><?=lang('Date');?>:</label>
			                        <input type="text" class="form-control input-sm dateInput" data-start="#dateRangeValueStart" data-end="#dateRangeValueEnd" data-time="true"/>
			                        <input type="hidden" id="dateRangeValueStart" name="by_date_from" value="<?php echo $conditions['by_date_from']; ?>" />
			                        <input type="hidden" id="dateRangeValueEnd" name="by_date_to" value="<?php echo $conditions['by_date_to']; ?>" />

 									</div>
									<div class="col-md-3">
				                        <label class="control-label">
				                        <?php echo lang('Enabled date'); ?>
				                        <input type="checkbox" name="enable_date" data-size='mini' value='true' <?php echo $conditions['enable_date'] ? 'checked="checked"' : ''; ?>>
				                        </label>
									</div>
				                    <div class="col-md-3 col-lg-3">
				                        <label class="control-label">
				                        <?php echo lang('Show game platform'); ?>
				                        <input type="checkbox" name="show_game_platform" data-size='mini' value='true' <?php echo $conditions['show_game_platform'] ? 'checked="checked"' : ''; ?>>
				                        </label>
				                    </div>
					            </div>
					            <div class="col-md-12 searchOptions">
									<div class="col-md-3">
				                        <label for="by_username" class="control-label"><?php echo lang('Username'); ?>:</label>
				                        <input type="text" name="by_username" id="by_username" class="form-control input-sm" value="<?php echo $conditions['by_username']; ?>"/>
					                </div>
									<div class="col-md-9">
					                	<input type="submit" value="<?=lang('aff.al01');?>" id="search_main"class="btn btn-info btn-sm" style="margin-top: 20px;">
					                </div>
					            </div>

								<div class="clearfix"></div>
							</form>
							<br>
						</div>

						<div class="col-md-12" id="view_stats" style="margin: 30px 0 0 0;">
							<table class="table table-striped table-hover" id="statisticsTable" style="width:100%">
								<thead>
									<tr>
										<th class="input-sm"><?php echo lang('Username'); ?></th>
										<th class="input-sm"><?php echo lang('Total Bet'); ?></th>
										<th class="input-sm"><?php echo lang('Total Win'); ?></th>
										<th class="input-sm"><?php echo lang('Total Loss'); ?></th>
										<th class="input-sm"><?php echo lang('Total Cashback'); ?></th>
										<th class="input-sm"><?php echo lang('Total Bonus'); ?></th>
										<th class="input-sm"><?php echo lang('Total Deposit'); ?></th>
										<th class="input-sm"><?php echo lang('Total Withdraw'); ?></th>
										<th class="input-sm"><?php echo lang('Total Add Balance'); ?></th>
										<th class="input-sm"><?php echo lang('Total Subtract Balance'); ?></th>
										<th class="input-sm"><?php echo lang('Balance'); ?></th>
									</tr>
								</thead>
								<tfoot>
				                    <tr>
				                        <th><?php echo lang('Total'); ?>:</th>
				                        <th><div id="totalBet"></div></th>
		                            	<th><div id="totalWin"></div></th>
		                            	<th><div id="totalLoss"></div></th>
		                            	<th><div id="totalCashback"></div></th>
		                            	<th><div id="totalBonus"></div></th>
		                            	<th><div id="totalDeposit"></div></th>
		                            	<th><div id="totalWithdraw"></div></th>
		                            	<th><div id="totalAddBal"></div></th>
		                            	<th><div id="totalSubtractBal"></div></th>
				                    </tr>
				                </tfoot>
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
        $('#statisticsTable').DataTable( {
            dom: "<'panel-body' <'pull-right'B> <'pull-right'f> <'pull-right progress-container'>l>t<'panel-body'<'pull-right'p>i>",
        	// "responsive": {
         //        details: {
         //            type: 'column'
         //        }
         //    },
            "order": [ 0, 'asc' ],
			buttons:[
				{
                extend: 'colvis',
                postfixButtons: [ 'colvisRestore' ]
            	}
    		],
            columnDefs: [
                { sortable: false, targets: [ 1,2,3,4,5,6,7,8,9,10 ] },
                { className: 'text-right', targets: [ 1,2,3,4,5,6,7,8,9,10 ] },
                { visible: false, targets: [ 10 ] }
            ],
            processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {
                data.extra_search = $('#search-form').serializeArray();
                $.post(base_url + "api/affiliate_statistics_for_aff", data, function(data) {
                    callback(data);
                    $('#totalBet').html(data.summary.totalBets);
                    $('#totalWin').html(data.summary.totalWins);
                    $('#totalLoss').html(data.summary.totalLoss);
                    $('#totalCashback').html(data.summary.totalCashback);
                    $('#totalBonus').html(data.summary.totalBonus);
                    $('#totalDeposit').html(data.summary.totalDeposit);
                    $('#totalWithdraw').html(data.summary.totalWithdrawal);
                    $('#totalAddBal').html(data.summary.totalAddBal);
                    $('#totalSubtractBal').html(data.summary.totalSubtractBal);
                    // $('#total_amount').text(data.summary[0].total_amount);
                },'json');
            }
        } );
        // $('#reportrange').data('daterangepicker').remove();

        $('[data-toggle="tooltip"]').tooltip();

        // $("input[type='checkbox']").bootstrapSwitch();

    } );
</script>
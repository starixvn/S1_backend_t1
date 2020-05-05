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
				<div class="panel-heading">
					<h4 class="panel-title pull-left"> <?=lang('nav.traffic');?></h4>
					<div class="clearfix"></div>
				</div>

				<div class="panel panel-body" id="affiliate_panel_body">
					<div class="panel panel-default">
						<div class="panel-heading">
							<form name="search-form" id="search-form" method="GET" action="<?php echo site_url('/affiliate/viewTrafficStatistics'); ?>">
					            <div class="col-md-12 searchOptions">
					            	<div class="col-md-6">
						            	<label for="period" class="control-label"><?=lang('Date');?>:</label>
			                        <input type="text" class="form-control input-sm dateInput" data-start="#dateRangeValueStart" data-end="#dateRangeValueEnd" data-time="true"/>
			                        <input type="hidden" id="dateRangeValueStart" name="by_date_from" value="<?php echo $conditions['by_date_from']; ?>" />
			                        <input type="hidden" id="dateRangeValueEnd" name="by_date_to" value="<?php echo $conditions['by_date_to']; ?>" />

 									</div>
									<div class="col-md-3">
				                        <label class="control-label"><?php echo lang('Enabled date'); ?></label><br>
				                        <input type="checkbox" name="enable_date" data-size='mini' value='true' <?php echo $conditions['enable_date'] ? 'checked="checked"' : ''; ?>>
									</div>
				                    <div class="col-md-3 col-lg-3">
				                        <label class="control-label"><?php echo lang('Show game platform'); ?></label><br>
				                        <input type="checkbox" name="show_game_platform" data-size='mini' value='true' <?php echo $conditions['show_game_platform'] ? 'checked="checked"' : ''; ?>>
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
										<th class="input-sm"><?php echo lang('Total Balance'); ?></th>
									</tr>
								</thead>
								<tfoot>
				                    <tr>
				                        <!--<th colspan="9" style="text-align:right;">
				                            <div class="row" id="summary">
				                            	 <h4 class="page-header">Summary</h4>
				                            	<div class="col-xs-11"><?php echo lang('Total Bet').':';?></div>
				                            	<div class="col-xs-1" id="totalBet"></div>
				                            	<div class="col-xs-11"><?php echo lang('Total Win').':';?></div>
				                            	<div class="col-xs-1" id="totalWin"></div>
				                            	<div class="col-xs-11"><?php echo lang('Total Loss').':';?></div>
				                            	<div class="col-xs-1" id="totalLoss"></div>
				                            	<div class="col-xs-11"><?php echo lang('Total Cashback').':';?></div>
				                            	<div class="col-xs-1" id="totalCashback"></div>
				                            	<div class="col-xs-11"><?php echo lang('Total Bonus').':';?></div>
				                            	<div class="col-xs-1" id="totalBonus"></div>
				                            	<div class="col-xs-11"><?php echo lang('Total Deposit').':';?></div>
				                            	<div class="col-xs-1" id="totalDeposit"></div>
				                            	<div class="col-xs-11"><?php echo lang('Total Withdraw').':';?></div>
				                            	<div class="col-xs-1" id="totalWithdraw"></div> 
				                            </div>
				                        </th>-->
				                        <th>Total:</th>
				                        <th><div id="totalBet"></div></th>
		                            	<th><div id="totalWin"></div></th>
		                            	<th><div id="totalLoss"></div></th>
		                            	<th><div id="totalCashback"></div></th>
		                            	<th><div id="totalBonus"></div></th>
		                            	<th><div id="totalDeposit"></div></th>
		                            	<th><div id="totalWithdraw"></div></th>
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
                { sortable: false, targets: [ 1,2,3,4,5,6,7,8 ] },
                { visible: false, targets: [ 8 ] }
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
                    $('#totalCashback').html(data.summary.totalCashback);
                    $('#totalBonus').html(data.summary.totalBonus);
                    $('#totalDeposit').html(data.summary.totalDeposit);
                    $('#totalWithdraw').html(data.summary.totalWithdrawal);
                    // $('#total_amount').text(data.summary[0].total_amount);
                },'json');
            }
        } );
        // $('#reportrange').data('daterangepicker').remove();

        $('[data-toggle="tooltip"]').tooltip();

        $("input[type='checkbox']").bootstrapSwitch();

    } );
</script>
<div class="panel panel-primary">
	<div class="panel-heading custom-ph">
		<h4 class="panel-title">
			<i class="icon-pie-chart"></i>
			<?=lang('report.s08')?>
		</h4>
	</div>
	<div class="panel-body">
		<div class="row form-group">
			<div class="col-md-6">
				<label class="control-label"><?=lang('report.sum02')?></label>
	            <input class="form-control dateInput" data-start="#date_from" data-end="#date_to" data-time="false"/>
	            <input type="hidden" id="date_from" name="date_from" data-time="false"/>
	            <input type="hidden" id="date_to" name="date_to" data-time="false"/>
	        </div>
			<div class="col-md-6 monthly-report-box">
				<label class="control-label"><?=lang('Month Only')?> : </label>
	            <input type="checkbox" name="month_only" id="month_only" <?=$month_only=='true' ? 'checked' : ''?>/>
	        </div>
	    </div>
		<div class="row form-group">
			<div class="col-md-2">
				<input type="submit" value="<?=lang('lang.search')?>" id="search_main" class="<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn btn-portage btn-sm' : 'btn btn-info btn-sm' ?>">
			</div>
	    </div>
		<div style="overflow-x: scroll;">
			<table class="table table-condensed table-bordered table-hover" id="summary-report" data-searching="false" data-length-change="false" data-ordering="false" data-page-length="100" style="width:100%">
				<thead>
					<tr>
						<th data-orderable="true" data-data="common_date"><?=lang('report.sum02')?></th>
						<th data-orderable="false" data-data="new_players"><?=lang('report.sum05')?></th>
						<th data-orderable="false" data-data="total_players"><?=lang('report.sum06')?></th>
						<th data-orderable="false" data-data="first_deposit"><?=lang('report.sum07')?></th>
						<th data-orderable="false" data-data="second_deposit"><?=lang('report.sum08')?></th>
						<th data-orderable="false" data-data="total_deposit"><?=lang('report.sum09')?></th>
						<th data-orderable="false" data-data="total_withdraw"><?=lang('report.sum10')?></th>
						<th data-orderable="false" data-data="total_bonus"><?=lang('report.sum14')?></th>
						<th data-orderable="false" data-data="total_cashback"><?=lang('report.sum15')?></th>
						<th data-orderable="false" data-data="total_transaction_fee"><?=lang('report.sum16')?></th>
						<th data-orderable="false" data-data="bank_cash_amount"><?=lang('report.sum18')?></th>
						<th data-orderable="false" data-data="total_bet"><?php echo lang('Total Bet'); ?></th>
						<th data-orderable="false" data-data="total_win"><?php echo lang('Total Win'); ?></th>
						<th data-orderable="false" data-data="total_loss"><?php echo lang('Total Loss'); ?></th>
						<th data-orderable="false" data-data="payout"><?php echo lang('report.Payout'); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td ><?=lang('Total')?></td>
						<td ><span class="new_players"></span></td>
						<td ><span class="total_players"></span></td>
						<td ><span class="first_deposit"></span></td>
						<td ><span class="second_deposit"></span></td>
						<td ><span class="total_deposit"></span></td>
						<td ><span class="total_withdraw"></span></td>
						<td ><span class="total_bonus"></span></td>
						<td ><span class="total_cashback"></span></td>
						<td ><span class="total_transaction_fee"></span></td>
						<td ><span class="bank_cash_amount"></span></td>
						<td ><span class="total_bet"></span></td>
						<td ><span class="total_win"></span></td>
						<td ><span class="total_loss"></span></td>
						<td ><span class="payout"></span></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
	<div class="panel-footer"></div>
</div>

<script type="text/javascript" src="<?=site_url().'resources/datatables/dataTables.buttons.min.js'?>"></script>
<script type="text/javascript" src="<?=site_url().'resources/datatables/jszip.min.js'?>"></script>
<script type="text/javascript" src="<?=site_url().'resources/datatables/buttons.html5.min.js'?>"></script>

<script type="text/javascript">

	var dateFormat = function (data, type, row) {
			return data;
	};

	var dateUrlFormat = function(date) {
		if(date.length == 6) return date;
		return moment(date).format('YYYY/MM/DD');
	}

	var newPlayer = function (data, type, row) {
		return data == 0 ? '<i class="text-muted">N/A</i>' : ('<a href="/report_management/new_members/' + dateUrlFormat(row.slug) + '" target="_blank"><b>' + numeral(data).format('0,0') + '</b></a>');
	};

	var totalPlayer = function (data, type, row) {
		return data == 0 ? '<i class="text-muted">N/A</i>' : ('<a href="/report_management/total_members/' + dateUrlFormat(row.slug) + '" target="_blank"><b>' + numeral(data).format('0,0') + '</b></a>');
	};

	var firstDeposit = function (data, type, row) {
		return data == 0 ? '<i class="text-muted">N/A</i>' : ('<a href="/report_management/first_deposit/' + dateUrlFormat(row.slug) + '" target="_blank"><b>' + numeral(data).format('0,0') + '</b></a>');
	};

	var secondDeposit = function (data, type, row) {
		return data == 0 ? '<i class="text-muted">N/A</i>' : ('<a href="/report_management/second_deposit/' + dateUrlFormat(row.slug) + '" target="_blank"><b>' + numeral(data).format('0,0') + '</b></a>');
	};

	var totalDeposit = function (data, type, row) {
		// return data == 0 ? '<i class="text-muted">N/A</i>' : ('<a href="/" target="_blank"><b>' + numeral(data).format('0,0.00') + '</b></a>');
		return data == 0 ? '<i class="text-muted">N/A</i>' : '<b>' + numeral(data).format('0,0.00') + '</b>';
	};

	var totalWithdraw = function (data, type, row) {
		// return data == 0 ? '<i class="text-muted">N/A</i>' : ('<a href="/" target="_blank"><b>' + numeral(data).format('0,0.00') + '</b></a>');
		return data == 0 ? '<i class="text-muted">N/A</i>' : '<b>' + numeral(data).format('0,0.00') + '</b>';
	};

	var totalBonus = function (data, type, row) {
		// return data == 0 ? '<i class="text-muted">N/A</i>' : ('<a href="/" target="_blank"><b>' + numeral(data).format('0,0.00') + '</b></a>');
		return data == 0 ? '<i class="text-muted">N/A</i>' : '<b>' + numeral(data).format('0,0.00') + '</b>';
	};

	var totalCashback = function (data, type, row) {
		// return data == 0 ? '<i class="text-muted">N/A</i>' : ('<a href="/" target="_blank"><b>' + numeral(data).format('0,0.00') + '</b></a>');
		return data == 0 ? '<i class="text-muted">N/A</i>' : '<b>' + numeral(data).format('0,0.00') + '</b>';
	};

	var totalFee = function (data, type, row) {
		// return data == 0 ? '<i class="text-muted">N/A</i>' : ('<a href="/" target="_blank"><b>' + numeral(data).format('0,0.00') + '</b></a>');
		return data == 0 ? '<i class="text-muted">N/A</i>' : '<b>' + numeral(data).format('0,0.00') + '</b>';
	};

	var currencyFormat = function (data, type, row) {
		return data == 0 ? '<i class="text-muted">N/A</i>' : '<b>' + numeral(data).format('0,0.00') + '</b>';
	};

	var integerFormat = function (data, type, row) {
		return data == 0 ? '<i class="text-muted">N/A</i>' : '<b>' + numeral(data).format('0,0') + '</b>';
	};

    $(document).ready(function(){
    	$('#summary_report_2').addClass("active");

    	$('#report_date').daterangepicker({
		    startDate: moment().startOf('month'),
		    endDate: moment().endOf('month')
		});
		$('#date_from').val(('<?= $dateFrom?>' == "") ? moment().startOf('month').format('YYYY-MM-DD') : '<?= $dateFrom?>');
		$('#date_to').val(('<?= $dateTo?>' == "") ? moment().endOf('month').format('YYYY-MM-DD') : '<?= $dateTo?>');
		$('.dateInput').data('daterangepicker').setStartDate('<?=date("Y-m-d", strtotime($dateFrom))?>');
		$('.dateInput').data('daterangepicker').setEndDate('<?=date("Y-m-d", strtotime($dateTo))?>');
        <?php $d = new DateTime(); ?>
		var filenames = '<?=lang('Summary Report').' '. $d->format('Y_m_d_H_i_s') . '_' . rand(1, 9999)?>';
        var dataTable = $('#summary-report').DataTable({
            <?php if( $this->permissions->checkPermissions('export_summary_report_2') ){ ?>
            dom: "<'panel-body' <'pull-right'B><'pull-right'f><'pull-right progress-container'>l>" +
            "<'dt-information-summary1 text-info pull-left' i>t<'text-center'r>" +
            "<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            buttons: [
				{
					className: "<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn btn-sm btn-portage' : 'btn btn-sm btn-primary' ?>",
                    text: '<?=lang('CSV Export')?>',
					action: function ( e, dt, node, config ) {
						$('.buttons-csv').trigger('click');
						$.ajax({
						  url: '/api/report_summary2/null/null/null/true',
						  type: 'POST',
						  data: {
								filename : filenames
			                }
						})
					}
				},
                {
                    extend: 'csvHtml5',
                    exportOptions: {
                        columns: ':visible'
                    },
                    className:'hidden',
                    text: 'hidden_' + '<?=lang('CSV Export')?>',
                    filename: filenames
                }
            ],
            <?php } ?>
        	columnDefs: [ {
				render: dateFormat,
				className: 'text-right',
				targets: 0
			}, {
				render: newPlayer,
				className: 'text-right',
				targets: 1
			}, {
				render: totalPlayer,
				className: 'text-right',
				targets: 2
			}, {
				render: firstDeposit,
				className: 'text-right',
				targets: 3
			}, {
				render: secondDeposit,
				className: 'text-right',
				targets: 4
			}, {
				render: totalDeposit,
				className: 'text-right',
				targets: 5
			}, {
				render: totalWithdraw,
				className: 'text-right',
				targets: 6
			}, {
				render: totalBonus,
				className: 'text-right',
				targets: 7
			}, {
				render: totalCashback,
				className: 'text-right',
				targets: 8
			}, {
				render: totalFee,
				className: 'text-right',
				targets: 9
			}, {
				render: currencyFormat,
				className: 'text-right',
				targets: 10
			}, {
				render: currencyFormat,
				className: 'text-right',
				targets: 11
			}, {
				render: currencyFormat,
				className: 'text-right',
				targets: 12
			}, {
				render: currencyFormat,
				className: 'text-right',
				targets: 13
			}, {
				render: currencyFormat,
				className: 'text-right',
				targets: 14
			}
			],
			order: [[0,'desc'] ],
            ajax: function (data, callback, settings) {

				$.post('/api/report_summary2/<?=date("Y-m-d", strtotime($dateFrom))?>/<?=date("Y-m-d", strtotime($dateTo))?>/<?=$month_only?>',
						null, function(data) {
					var new_players=0, total_players=0,
					first_deposit=0, second_deposit=0,
					total_deposit=0, total_withdraw=0, total_bonus=0,
					total_cashback=0, total_transaction_fee=0, bank_cash_amount=0,
					total_bet=0, total_win=0, total_loss=0, payout=0;

						$.each(data.data, function(i, v){
							// console.log(v);
							// var arr_results = [v['new_players'], v[5], v[7], v[8], v[10], v[11], v[12], v[13], v[14], v[15], v[1], v[2], v[3], v[4]];
							// console.log(arr_results);
							for (var x = 0; x < v.length; x++) {
								// Remove html tags from string
								// arr_results[x] = arr_results[x].replace(/<\/?[^>]+(>|$)/g, "");
								// Remove comma from string
								arr_results[x] = arr_results[x].replace(/,/g , "");

								if (isNaN(arr_results[x])) {
									arr_results[x]  = 0;
								}
							}
							new_players+= parseFloat(v['new_players']);
							// total_players+= parseFloat(v['total_players']);
							first_deposit+= parseFloat(v['first_deposit']);
							second_deposit+= parseFloat(v['second_deposit']);
							total_deposit+= parseFloat(v['total_deposit']);
							total_withdraw+= parseFloat(v['total_withdraw']);
							total_bonus+= parseFloat(v['total_bonus']);
							total_cashback+= parseFloat(v['total_cashback']);
							total_transaction_fee+= parseFloat(v['total_transaction_fee']);
							bank_cash_amount+= parseFloat(v['bank_cash_amount']);
							total_bet+= parseFloat(v['total_bet']);
							total_win+= parseFloat(v['total_win']);
							total_loss+= parseFloat(v['total_loss']);
							payout+= parseFloat(v['payout']);

						});

                        $('.new_players').text(parseFloat(new_players).toFixed(2));
                        // $('.total_players').text(parseFloat(total_players).toFixed(2));
                        $('.first_deposit').text(parseFloat(first_deposit).toFixed(2));
                        $('.second_deposit').text(parseFloat(second_deposit).toFixed(2));
                        $('.total_deposit').text(parseFloat(total_deposit).toFixed(2));
                        $('.total_withdraw').text(parseFloat(total_withdraw).toFixed(2));
                        $('.total_bonus').text(parseFloat(total_bonus).toFixed(2));
                        $('.total_cashback').text(parseFloat(total_cashback).toFixed(2));
                        $('.total_transaction_fee').text(parseFloat(total_transaction_fee).toFixed(2));
                        $('.bank_cash_amount').text(parseFloat(bank_cash_amount).toFixed(2));
                        $('.total_bet').text(parseFloat(total_bet).toFixed(2));
                        $('.total_win').text(parseFloat(total_win).toFixed(2));
                        $('.total_loss').text(parseFloat(total_loss).toFixed(2));
                        $('.payout').text(parseFloat(payout).toFixed(2));

						callback(data);

					}
				)

			}
        } );
    });
    $("#search_main").click(function() {
    	var url = "<?php echo site_url('/report_management/summary_report_2'); ?>/" + moment($('input[name="date_from"]').val()).format('YYYY-MM-DD') + "/" + moment($('input[name="date_to"]').val()).format('YYYY-MM-DD')+"/"+($("#month_only").is(":checked") ? "true" : "false");
        window.location.href = url;
    });
</script>

<div class="panel panel-primary">
	<div class="panel-heading custom-ph">
		<h4 class="panel-title">
			<i class="icon-pie-chart"></i>
			<?=lang('report.s08')?>
		</h4>
	</div>
	<div class="panel-body">
		<form id="search-form">
			<div class="input-group">
				<input type="hidden" id="report_date_from" name="report_date_from">
				<input type="hidden" id="report_date_to" name="report_date_to">
				<input class="form-control dateInput" id="report_date" data-start="#report_date_from" data-end="#report_date_to">
				<span class="input-group-btn">
					<button class="btn btn-default" type="submit"><i class="fa fa-filter"></i></button>
				</span>
			</div>
		</form>
		<table class="table table-striped table-bordered table-condendse table-hover" id="summary-report" data-searching="false" data-length-change="false" data-ordering="false" data-page-length="100">
			<thead>
				<tr>
					<th data-data="common_date"><?=lang('report.sum02')?></th>
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
				</tr>
			</thead>
		</table>
	</div>
	<div class="panel-footer"></div>
</div>

<script type="text/javascript">

	var newPlayer = function (data, type, row) {
		// console.log(row);
		return data == 0 ? '<i class="text-muted">N/A</i>' : ('<a href="/report_management/viewNewRegisteredPlayer/' + row.common_date + '" target="_blank"><b>' + numeral(data).format('0,0') + '</b></a>');
	}

	var totalPlayer = function (data, type, row) {
		return data == 0 ? '<i class="text-muted">N/A</i>' : ('<a href="/report_management/viewRegisteredPlayer/' + row.common_date + '" target="_blank"><b>' + numeral(data).format('0,0') + '</b></a>');
	}

	var firstDeposit = function (data, type, row) {
		return data == 0 ? '<i class="text-muted">N/A</i>' : ('<a href="/" target="_blank"><b>' + numeral(data).format('0,0') + '</b></a>');
	}

	var secondDeposit = function (data, type, row) {
		return data == 0 ? '<i class="text-muted">N/A</i>' : ('<a href="/" target="_blank"><b>' + numeral(data).format('0,0') + '</b></a>');
	}

	var totalDeposit = function (data, type, row) {
		return data == 0 ? '<i class="text-muted">N/A</i>' : ('<a href="/" target="_blank"><b>' + numeral(data).format('0,0') + '</b></a>');
	}

	var totalWithdraw = function (data, type, row) {
		return data == 0 ? '<i class="text-muted">N/A</i>' : ('<a href="/" target="_blank"><b>' + numeral(data).format('0,0') + '</b></a>');
	}

	var totalBonus = function (data, type, row) {
		return data == 0 ? '<i class="text-muted">N/A</i>' : ('<a href="/" target="_blank"><b>' + numeral(data).format('0,0') + '</b></a>');
	}

	var totalCashback = function (data, type, row) {
		return data == 0 ? '<i class="text-muted">N/A</i>' : ('<a href="/" target="_blank"><b>' + numeral(data).format('0,0') + '</b></a>');
	}

	var totalFee = function (data, type, row) {
		return data == 0 ? '<i class="text-muted">N/A</i>' : ('<a href="/" target="_blank"><b>' + numeral(data).format('0,0') + '</b></a>');
	}

	var currencyFormat = function (data, type, row) {
		return data == 0 ? '<i class="text-muted">N/A</i>' : '<b>' + numeral(data).format('0,0.00') + '</b>';
	}

	var integerFormat = function (data, type, row) {
		return data == 0 ? '<i class="text-muted">N/A</i>' : '<b>' + numeral(data).format('0,0') + '</b>';
	}

    $(document).ready(function(){
    	$('#report_date').daterangepicker({
		    "startDate": moment().startOf('month'),
		    "endDate": moment().endOf('month'),
		});

		$('#report_date_from').val(moment().startOf('month').format('YYYY-MM-DD'))
		$('#report_date_to').val(moment().endOf('month').format('YYYY-MM-DD'))

        var dataTable = $('#summary-report').DataTable( {
        	columnDefs: [ {
				render: newPlayer,
				className: 'text-right',
				targets: 1,
			},{
				render: totalPlayer,
				className: 'text-right',
				targets: 2,
			},{
				render: firstDeposit,
				className: 'text-right',
				targets: 3,
			},{
				render: secondDeposit,
				className: 'text-right',
				targets: 4,
			},{
				render: totalDeposit,
				className: 'text-right',
				targets: 5,
			},{
				render: totalWithdraw,
				className: 'text-right',
				targets: 6,
			},{
				render: totalBonus,
				className: 'text-right',
				targets: 7,
			},{
				render: totalCashback,
				className: 'text-right',
				targets: 8,
			},{
				render: totalFee,
				className: 'text-right',
				targets: 9,
			},{
				render: currencyFormat,
				className: 'text-right',
				targets: 10,
			} ],
			processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {

                data.extra_search = {
                	date_from: $('#report_date_from').val(),
                	date_to: $('#report_date_to').val(),
            	};

                $.post('/api/report_summary', data, function(data) {
                    callback(data);
                }, 'json');
            },
        } );
        $('#search-form').submit(function(e) {
        	e.preventDefault();
            dataTable.ajax.reload();
        })
    });
</script>

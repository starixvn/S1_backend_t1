<ol class="breadcrumb">
	<?php if ($month): ?>
		<li><a href="/report_management/summary_report"><?php echo lang('Summary Report');?></a></li>
		<li><a href="/report_management/summary_report/<?=$year?>"><?=$year?></a></li>
		<li class="active"><?=$month?></li>
	<?php elseif ($year): ?>
		<li><a href="/report_management/summary_report"><?php echo lang('Summary Report');?></a></li>
		<li class="active"><?=$year?></li>
	<?php else: ?>
		<li class="active"><?php echo lang('Summary Report');?></li>
	<?php endif?>
</ol>
<?php if($this->utils->isEnabledFeature('display_exclude_player_tag')){?>
<div class="panel panel-primary ">
    <div class="panel-heading">
        <h4 class="panel-title">
            <span class="pull-right">
                <a data-toggle="collapse" href="#collapsePromotionReport" class="btn btn-info btn-xs"></a>
            </span>
            <i class="fa fa-search"></i> Search
        </h4>
    </div>
    <div class="panel-collapse">
        <div class="panel-body">
            <form class="form">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label"><?=lang('exclude_player')?> : </label>
                            <select name="tag_list[]" id="tag_list" multiple="multiple" class="form-control">
                                <?php foreach ($tags as $key) {?>
                                    <option value="<?=$key['tagId']?>" <?=is_array($selected_tags) && in_array($key['tagId'], $selected_tags) ? "selected" : "" ?>><?=$key['tagName']?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">&nbsp;</label>
                            <br/>
                            <button class="btn btn-primary" id="search-report">Search</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>

<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title">
            <i class="icon-pie-chart"></i>
            <?=lang('report.s08')?>
        </h4>
    </div>
	<div class="panel-body">
		<table class="table table-condensed table-bordered table-hover" id="summary-report" data-searching="false" data-length-change="false" data-ordering="false" data-page-length="100">
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
					<th data-orderable="false" data-data="total_bet"><?php echo lang('Total Bet'); ?></th>
					<th data-orderable="false" data-data="total_win"><?php echo lang('Total Win'); ?></th>
					<th data-orderable="false" data-data="total_loss"><?php echo lang('Total Loss'); ?></th>
					<th data-orderable="false" data-data="payout"><?php echo lang('Payout'); ?></th>
				</tr>
			</thead>
		</table>
	</div>
	<div class="panel-footer"></div>
</div>
<script type="text/javascript" src="<?=site_url().'resources/datatables/dataTables.buttons.min.js'?>"></script>
<script type="text/javascript" src="<?=site_url().'resources/datatables/jszip.min.js'?>"></script>
<script type="text/javascript" src="<?=site_url().'resources/datatables/buttons.html5.min.js'?>"></script>

<script type="text/javascript">

	var dateFormat = function (data, type, row) {
		<?php if ($month): ?>
			return data;
		<?php elseif ($year): ?>
			return '<a href="/report_management/summary_report/<?=$year?>/' + moment(data, 'YYYYMM').format('MM') + '"><b>' + moment(data, 'YYYYMM').format('MMM') + '</b></a>';
		<?php else: ?>
			return '<a href="/report_management/summary_report/' + data + '"><b>' + data + '</b></a>';
		<?php endif?>
	};

	var newPlayer = function (data, type, row) {
		return data == 0 ? '<i class="text-muted">N/A</i>' : ('<a href="/report_management/new_members/' + row.slug + '" target="_blank"><b>' + numeral(data).format('0,0') + '</b></a>');
	};

	var totalPlayer = function (data, type, row) {
		return data == 0 ? '<i class="text-muted">N/A</i>' : ('<a href="/report_management/total_members/' + row.slug + '" target="_blank"><b>' + numeral(data).format('0,0') + '</b></a>');
	};

	var firstDeposit = function (data, type, row) {
		return data == 0 ? '<i class="text-muted">N/A</i>' : ('<a href="/report_management/first_deposit/' + row.slug + '" target="_blank"><b>' + numeral(data).format('0,0') + '</b></a>');
	};

	var secondDeposit = function (data, type, row) {
		return data == 0 ? '<i class="text-muted">N/A</i>' : ('<a href="/report_management/second_deposit/' + row.slug + '" target="_blank"><b>' + numeral(data).format('0,0') + '</b></a>');
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
        $('#tag_list').multiselect({
            enableFiltering: true,
            includeSelectAllOption: true,
            selectAllJustVisible: false,
            buttonWidth: '350px',
            buttonText: function(options, select) {
                if (options.length === 0) {
                    return 'Select Tags';
                }
                else {
                    var labels = [];
                    options.each(function() {
                        if ($(this).attr('label') !== undefined) {
                            labels.push($(this).attr('label'));
                        }
                        else {
                            labels.push($(this).html());
                        }
                    });
                    return labels.join(', ') + '';
                }
            }
        });

    	$('#report_date').daterangepicker({
		    startDate: moment().startOf('month'),
		    endDate: moment().endOf('month')
		});

		$('#report_date_from').val(moment().startOf('month').format('YYYY-MM-DD'));
		$('#report_date_to').val(moment().endOf('month').format('YYYY-MM-DD'));
        <?php $d = new DateTime(); ?>
        var filenames = '<?=lang('Summary Report').' '. $d->format('Y_m_d_H_i_s') . '_' . rand(1, 9999)?>';
        var dataTable = $('#summary-report').DataTable({
            <?php if( $this->permissions->checkPermissions('export_summary_report') ){ ?>
            dom: "<'panel-body' <'pull-right'B><'pull-right'f><'pull-right progress-container'>l>" +
            "<'dt-information-summary1 text-info pull-left' i>t<'text-center'r>" +
            "<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            buttons: [
				{
					className:'btn btn-sm btn-primary',
                    text: '<?=lang('CSV Export')?>',
					action: function ( e, dt, node, config ) {
						$('.buttons-csv').trigger('click');
						$.ajax({
						  url: '/api/report_summary/null/null/true',
						  type: 'POST',
						  data: {
								tag_list : <?=json_encode($selected_tags); ?>,
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
			<?php if ($this->utils->isEnabledFeature('hide_second_deposit_in_summary_report')) : ?>
			}, {
				visible: false,
				targets: [4]
			<?php endif; ?>
			} ],
            ajax: {
                url: <?php if ($month): ?>
                    '/api/report_summary/<?=$year?>/<?=$month?>',
                <?php elseif ($year): ?>
                '/api/report_summary/<?=$year?>',
                <?php else: ?>
                '/api/report_summary',
                <?php endif?>
                type: 'POST',
                data: {
					tag_list : <?=json_encode($selected_tags); ?>,
					filename : filenames
                }
            }
        } );
    });
</script>

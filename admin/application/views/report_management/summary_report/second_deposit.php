<ol class="breadcrumb">
	<?php if ($day): ?>
		<li><a href="/report_management/summary_report"><?=lang('Summary Report')?></a></li>
		<li><a href="/report_management/second_deposit"><?= lang('report.sum08'); ?></a></li>
		<li><a href="/report_management/second_deposit/<?=$year?>"><?=$year?></a></li>
		<li><a href="/report_management/second_deposit/<?=$year?>/<?=$month?>"><?=$month?></a></li>
		<li class="active"><?=$day?></li>
	<?php elseif ($month): ?>
		<li><a href="/report_management/summary_report"><?=lang('Summary Report')?></a></li>
		<li><a href="/report_management/second_deposit"><?= lang('report.sum08'); ?></a></li>
		<li><a href="/report_management/second_deposit/<?=$year?>"><?=$year?></a></li>
		<li class="active"><?=$month?></li>
	<?php elseif ($year): ?>
		<li><a href="/report_management/summary_report"><?=lang('Summary Report')?></a></li>
		<li><?= lang('report.sum08'); ?></li>
		<li class="active"><?=$year?></li>
	<?php else: ?>
		<li class="active"><?=lang('Summary Report')?></li>
	<?php endif ?>
</ol>

<div class="panel panel-primary">
	<div class="panel-heading custom-ph">
		<h4 class="panel-title">
			<?=implode('-', array_filter(array($year,$month,$day)))?>
			<?= lang('report.sum08'); ?>
		</h4>
	</div>
	<div class="panel-body">
		<table class="table table-condensed table-bordered table-hover" id="summary-report" style="width:100%">
			<thead>
				<tr>
					<th data-data="username"><?=lang('Username')?></th>
					<th data-data="date"><?=lang('Second deposit date')?></th>
					<th data-data="amount"><?=lang('Deposit Amount')?></th>
					<th data-data="transactionId"><?=lang('Transaction Number')?></th>
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

	var username = function (data, type, row) {
		return '<a href="/player_management/player/' + data + '">' + data + '</a>';
	}

    $(document).ready(function(){

    	$('#report_date').daterangepicker({
		    startDate: moment().startOf('month'),
		    endDate: moment().endOf('month'),
		});

		$('#report_date_from').val(moment().startOf('month').format('YYYY-MM-DD'));
		$('#report_date_to').val(moment().endOf('month').format('YYYY-MM-DD'));
		<?php $d = new DateTime(); ?>

        var dataTable = $('#summary-report').DataTable( {

        	<?php if( $this->permissions->checkPermissions('export_deposit_report') ){ ?>
            dom: "<'panel-body' <'pull-right'B><'pull-right'f><'pull-right progress-container'>l>" +
            "<'dt-information-summary1 text-info pull-left' i>t<'text-center'r>" +
            "<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            buttons: [
                {
                    extend: 'csvHtml5',
                    exportOptions: {
                        columns: ':visible'
                    },
                    className:'btn btn-sm btn-primary',
                    text: '<?=lang('CSV Export')?>',
                    filename:  '<?=lang('report.sum08').' '. $d->format('Y_m_d_H_i_s') . '_' . rand(1, 9999)?>'
                }
            ],
            <?php } ?>

        	columnDefs: [{
				render: username,
				targets: 0,
			}],

            ajax: 
				<?php if ($day): ?>
					'/api/second_deposit/<?=$year?>/<?=$month?>/<?=$day?>',
				<?php elseif ($month): ?>
					'/api/second_deposit/<?=$year?>/<?=$month?>',
				<?php else: ?>
					'/api/second_deposit/<?=$year?>',
				<?php endif ?>
        } );

        $('#search-form').submit(function(e) {
        	e.preventDefault();
            dataTable.ajax.reload();
        })
    });
</script>

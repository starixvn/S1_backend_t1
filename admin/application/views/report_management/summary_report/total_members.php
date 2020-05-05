<ol class="breadcrumb">
	<?php if ($day): ?>
		<li><a href="/report_management/summary_report"><?=lang('Summary Report')?></a></li>
		<li><a href="/report_management/total_members"><?=lang('report.sum06')?></a></li>
		<li><a href="/report_management/total_members/<?=$year?>"><?=$year?></a></li>
		<li><a href="/report_management/total_members/<?=$year?>/<?=$month?>"><?=$month?></a></li>
		<li class="active"><?=$day?></li>
	<?php elseif ($month): ?>
		<li><a href="/report_management/summary_report"><?=lang('Summary Report')?></a></li>
		<li><a href="/report_management/total_members"><?=lang('report.sum06')?></a></li>
		<li><a href="/report_management/total_members/<?=$year?>"><?=$year?></a></li>
		<li class="active"><?=$month?></li>
	<?php elseif ($year): ?>
		<li><a href="/report_management/summary_report"><?=lang('Summary Report')?></a></li>
		<li><?=lang('report.sum06')?></li>
		<li class="active"><?=$year?></li>
	<?php else: ?>
		<li class="active"><?=lang('Summary Report')?></li>
	<?php endif ?>
</ol>

<div class="panel panel-primary">
	<div class="panel-heading custom-ph">
		<h4 class="panel-title">
			<?=implode('-', array_filter(array($year,$month,$day)))?>
			<?=lang('report.sum06')?>
		</h4>
	</div>
	<div class="panel-body">
		<table class="table table-condensed table-bordered table-hover" id="summary-report" style="width:100%">
			<thead>
				<tr>
					<th data-data="username"><?=lang('Username')?></th>
					<th data-data="createdOn"><?=lang('report.sum02')?></th>
				</tr>
			</thead>
		</table>
	</div>
	<div class="panel-footer"></div>
</div>

<script type="text/javascript">

	var username = function (data, type, row) {
		return '<a href="/player_management/player/' + data + '">' + data + '</a>';
	}

    $(document).ready(function(){

    	$('#report_date').daterangepicker({
		    startDate: moment().startOf('month'),
		    endDate: moment().endOf('month'),
		});

		$('#report_date_from').val(moment().startOf('month').format('YYYY-MM-DD'))
		$('#report_date_to').val(moment().endOf('month').format('YYYY-MM-DD'))

        var dataTable = $('#summary-report').DataTable( {

        	columnDefs: [{
				render: username,
				targets: 0,
			}],

            ajax: 
				<?php if ($day): ?>
					'/api/total_members/<?=$year?>/<?=$month?>/<?=$day?>',
				<?php elseif ($month): ?>
					'/api/total_members/<?=$year?>/<?=$month?>',
				<?php else: ?>
					'/api/total_members/<?=$year?>',
				<?php endif ?>
        } );

        $('#search-form').submit(function(e) {
        	e.preventDefault();
            dataTable.ajax.reload();
        })
    });
</script>

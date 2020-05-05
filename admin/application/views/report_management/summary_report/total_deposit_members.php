<ol class="breadcrumb">
	<?php if ($day): ?>
		<li><a href="/report_management/summary_report">Summary Report</a></li>
		<li><a href="/report_management/total_deposit_members">Total Deposit Members</a></li>
		<li><a href="/report_management/total_deposit_members/<?=$year?>"><?=$year?></a></li>
		<li><a href="/report_management/total_deposit_members/<?=$year?>/<?=$month?>"><?=$month?></a></li>
		<li class="active"><?=$day?></li>
	<?php elseif ($month): ?>
		<li><a href="/report_management/summary_report">Summary Report</a></li>
		<li><a href="/report_management/total_deposit_members">Total Deposit Members</a></li>
		<li><a href="/report_management/total_deposit_members/<?=$year?>"><?=$year?></a></li>
		<li class="active"><?=$month?></li>
	<?php elseif ($year): ?>
		<li><a href="/report_management/summary_report">Summary Report</a></li>
		<li>Total Deposit Members</li>
		<li class="active"><?=$year?></li>
	<?php else: ?>
		<li class="active">Summary Report</li>
	<?php endif ?>
</ol>

<div class="panel panel-primary">
	<div class="panel-heading custom-ph">
		<h4 class="panel-title">
			<?=implode('-', array_filter(array($year,$month,$day)))?>
			Total Deposit Members
		</h4>
	</div>
	<div class="panel-body">
		<table class="table table-condensed table-bordered table-hover" id="summary-report">
			<thead>
				<tr>
					<th data-data="username"><?=lang('Username')?></th>
					<!-- <th data-data="create_at"><?=lang('report.sum02')?></th> -->
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
					'/api/total_deposit_members/<?=$year?>/<?=$month?>/<?=$day?>',
				<?php elseif ($month): ?>
					'/api/total_deposit_members/<?=$year?>/<?=$month?>',
				<?php else: ?>
					'/api/total_deposit_members/<?=$year?>',
				<?php endif ?>
        } );

        $('#search-form').submit(function(e) {
        	e.preventDefault();
            dataTable.ajax.reload();
        })
    });
</script>

<form class="form-horizontal" action="<?=BASEURL . 'report_management/viewDailyReport'?>" method="post" role="form" name="myForm">
	<!--main-->
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h4 class="panel-title pull-left">
						<i class="icon-search" id="hide_main_up"></i> <?=lang('lang.search');?>
					</h4>
					
					<a href="#main" id="hide_main" class="btn btn-default btn-sm pull-right">
						<i class="glyphicon glyphicon-chevron-up" id="hide_main_up"></i>
					</a>
					<div class="clearfix"></div>
				</div>
				<div class="panel panel-body" id="main_panel_body">
					<div class="form-group">
						<div class="col-md-3">
							<label for="sign_time_period" class="control-label"><?=lang('report.sum01');?></label>
							<select class="form-control input-sm" name="sign_time_period" onchange="specify(this)">
								<option value=""><?=lang('lang.all');?></option>
								<option value="week"><?=lang('lang.week');?></option>
								<option value="month"><?=lang('lang.month');?></option>
								<option value="past"><?=lang('lang.months');?></option>
								<option value="specify"><?=lang('lang.specify');?></option>
							</select>
							<?php echo form_error('sign_time_period', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
						</div>
						<div id="hide_date">
							<div class="col-md-4">
								<label for="start_date" class="control-label"><?=lang('report.sum02');?>: </label>
								<input type="date" name="start_date" id="start_date" class="form-control input-sm" disabled="disabled">
								<?php echo form_error('start_date', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
							</div>
							<div class="col-md-4">
								<label for="end_date" class="control-label"><?=lang('report.sum03');?>: </label>
								<input type="date" name="end_date" id="end_date" class="form-control input-sm" disabled="disabled">
								<?php echo form_error('end_date', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <span class="help-block" style="color:#ff6666;" id="mdate"></span>
							</div>
						</div>
						<div class="col-md-1" style="text-align:center;padding-top:24px;">
							<input type="submit" value="<?=lang('lang.go');?>" id="search_main"class="btn btn-block btn-info btn-sm">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!--end of main-->
</form>

<div class="row">
	<div class="col-md-12" id="toggleView">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title"><i class="icon-calendar"></i> <?=lang('report.sum15');?> </h4>
			</div>
			<div class="panel panel-body" id="summartreport_panel_body">
				<?php if ($export_report_permission) {?>
                    <a href="<?=BASEURL . 'report_management/exportDailyReportToExcel'?>" class="btn btn-sm btn-success" data-placement="top" data-toggle="tooltip" title="<?=lang('lang.exporttitle');?>">
                        <span class="glyphicon glyphicon-share"></span>
                    </a>
                <?php }
?>
                <hr/>
				<div id="summaryReportList" class="table-responsive" style="overflow: auto;">
					<table class="table table-striped table-hover" style="margin: 0px 0 0 0;" id="myTable">
						<thead>
							<tr>
								<th><?=lang('report.sum05');?></th>
								<th><?=lang('report.sum14');?></th>
								<th><?=lang('report.sum06');?></th>
								<th><?=lang('report.sum07');?></th>
								<th><?=lang('report.sum08');?></th>
								<th><?=lang('report.sum09');?></th>
								<th><?=lang('report.sum10');?></th>
								<th><?=lang('report.sum11');?></th>
								<th><?=lang('report.sum12');?></th>
								<th><?=lang('report.sum13');?></th>
							</tr>
						</thead>

						<tbody>
							<?php if (!empty($summary_report)) {
	?>
								<?php
foreach ($summary_report as $key => $value) {
		$date = date('Y-m-d', strtotime($value['start_date']));
		?>
									<tr>
										<td><?=$date?></td>
										<td><?=$value['registeredPlayer']?></td>
										<td><?=$value['onlinePlayer']?></td>
										<td><?=$value['depositPlayer']?></td>
										<td><?=$value['thirdPartyDepositPlayer']?></td>
										<td><?=$value['withdrawalPlayer']?></td>
										<td><?=$value['firstDepositPlayer']?></td>
										<td><?=$value['firstDepositAmount']?></td>
										<td><?=$value['secondDepositPlayer']?></td>
										<td><?=$value['secondDepositAmount']?></td>
									</tr>
							<?php
}
} else {
	?>
					        <?php }
?>
						</tbody>
					</table>
				</div>
			</div>

		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('#myTable').DataTable();
	});
</script>


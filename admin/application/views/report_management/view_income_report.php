<form class="form-horizontal" action="<?= BASEURL . 'report_management/viewIncomeReport' ?>" method="post" role="form" name="myForm">
	<!--main-->
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h4 class="panel-title">
						<i class="icon-search" id="hide_main_up"></i> <?= lang('lang.search'); ?>
						<a href="#main" 
              id="hide_main" class="btn btn-default btn-sm pull-right"> 
							<i class="glyphicon glyphicon-chevron-up" id="hide_main_up"></i>
						</a>
						<div class="clearfix"></div>
					</h4>
				</div>
				<div class="panel-body main_panel_body" id="main_panel_body">
					<div class="form-group">
						<div class="col-md-4">
							<label for="sign_time_period" class="control-label"><?= lang('report.sum01'); ?>:</label>
							<select class="form-control input-sm" name="sign_time_period" onchange="specify(this)">
								<option value=""><?= lang('lang.all'); ?></option>
								<option value="week"><?= lang('lang.week'); ?></option>
								<option value="month"><?= lang('lang.month'); ?></option>
								<option value="past"><?= lang('lang.months'); ?></option>
								<option value="specify"><?= lang('lang.specify'); ?></option>
							</select>
							<?php echo form_error('sign_time_period', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
						</div>
						<div id="hide_date">
							<div class="col-md-3">
								<label for="start_date" class="control-label"><?= lang('report.sum02'); ?>: </label>
								<input type="date" name="start_date" id="start_date" class="form-control input-sm" disabled="disabled">
								<?php echo form_error('start_date', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
							</div>
							<div class="col-md-3">
								<label for="end_date" class="control-label"><?= lang('report.sum03'); ?>: </label>
								<input type="date" name="end_date" id="end_date" class="form-control input-sm" disabled="disabled">
								<?php echo form_error('end_date', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <span class="help-block" style="color:#ff6666;" id="mdate"></span>
							</div>
						</div>
						<div class="col-md-2" style="padding-top:23px;text-align:center;">
							<input type="submit" value="<?= lang('lang.search'); ?>" id="search_main"class="btn col-md-10 btn-info btn-sm">
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
				<h4 class="panel-title"><i class="icon-coin-dollar"></i> <?= lang('report.in01'); ?> </h4>
			</div>

			<div class="panel panel-body" id="incomereport_panel_body">
				<div id="incomeReportList" class="table-responsive">
					<?php if($export_report_permission){ ?>
                        <a href="<?= BASEURL . 'report_management/exportIncomeReportToExcel' ?>" class="btn btn-sm btn-success btn-sm" data-toggle="tooltip" title="<?= lang('lang.export'); ?>" data-placement="top">
                            <i class="glyphicon glyphicon-share"></i>
                        </a>
                    <?php } ?>
                    <hr class="hr_between_table"/>
					<table class="table table-striped table-hover" style="width: 100%;" id="myTable">
						<thead>
							<tr>
								<th></th>
								<th><?= lang('report.in02'); ?></th>									
								<th><?= lang('report.in03'); ?></th>			
								<th><?= lang('report.in04'); ?></th>			
								<th><?= lang('report.in05'); ?></th>			
								<th><?= lang('report.in06'); ?></th>
								<th><?= lang('report.in07'); ?></th>
							</tr>
						</thead>

						<tbody>
							<?php if(!empty($income_report)) { ?>
								<?php 
									foreach ($income_report as $key => $value) { 
										$date = date('Y-m-d', strtotime($value['start_date']));
								?>
									<tr>
										<td></td>
										<td><?= $date ?></td>
										<td><?= $value['depositAmount'] ?></td>
										<td><?= $value['thirdPartyAmount'] ?></td>
										<td><?= $value['withdrawalAmount'] ?></td>
										<td><?= $value['bonus'] ?></td>
										<td><?= $value['amountEarned'] ?></td>
									</tr>
							<?php
									}
								} else {
							?>
					        <?php } ?>
						</tbody>
					</table>
				</div>
			</div>

		</div>
	</div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#myTable').DataTable( {
            "responsive": {
                details: {
                    type: 'column'
                }
            },
            "columnDefs": [ {
                className: 'control',
                orderable: false,
                targets:   0
            } ],
            "order": [ 1, 'asc' ]
        } );
    } );
</script>

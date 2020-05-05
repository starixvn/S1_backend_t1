<form class="form-horizontal" action="<?= BASEURL . 'report_management/searchIncomeReport' ?>" method="post" role="form" name="myForm">
	<!--main-->
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary
              " style="margin-bottom:10px;">
				<div class="panel-heading">
					<h4 class="panel-title">
						<i class="icon-search" id="hide_main_up"></i> <?= lang('lang.search'); ?>
						<a href="#main" 
              class="btn btn-default btn-sm pull-right hide_sortby"> 
							<i class="glyphicon glyphicon-chevron-up hide_sortby_up"></i>
						</a>
						<div class="clearfix"></div>
					</h4>
				</div>
				<div class="panel-body sortby_panel_body">
                    <?php 
                        $period = $this->session->userdata('period');
                        $start_date = $this->session->userdata('start_date');
                        $end_date = $this->session->userdata('end_date');
                        $date_range_value = $this->session->userdata('date_range_value');

                        $depamt1 = $this->session->userdata('depamt1');
                        $depamt2 = $this->session->userdata('depamt2');

                        $widamt1 = $this->session->userdata('widamt1');
                        $widamt2 = $this->session->userdata('widamt2');

                        $status = $this->session->userdata('report_income_status');
                        $playerlevel = $this->session->userdata('report_income_level');
                        $username = $this->session->userdata('report_income_username');
                    ?>
					<div class="form-group" style="margin-bottom:0;">
						<div class="col-md-3">
							<label class="control-label" for="period"><?= lang('report.sum01'); ?></label>
							<select class="form-control input-sm" name="period" onchange="checkPeriod(this)">
								<option value=""><?= lang('report.pr24'); ?></option>
	                            <option value="daily" <?= ($period == 'daily') ? 'selected':'' ?> ><?= lang('report.pr25'); ?></option>
	                            <option value="weekly" <?= ($period == 'weekly') ? 'selected':'' ?> ><?= lang('report.pr26'); ?></option>
	                            <option value="monthly" <?= ($period == 'monthly') ? 'selected':'' ?> ><?= lang('report.pr27'); ?></option>
	                            <option value="yearly" <?= ($period == 'yearly') ? 'selected':'' ?> ><?= lang('report.pr28'); ?></option>
	                        </select>
						</div>
						<!-- <div class="col-md-4">
							<label class="control-label" for="start_date"><?= lang('report.sum02'); ?>: </label>
							<input type="date" name="start_date" id="start_date" class="form-control input-sm" <?= ($start_date == null) ? 'disabled':'' ?> value="<?= (!empty($start_date)) ? $start_date:'' ?>">
						</div>
						<div class="col-md-4">
							<label class="control-label" for="end_date"><?= lang('report.sum03'); ?>: </label>
							<input type="date" name="end_date" id="end_date" class="form-control input-sm" <?= ($end_date == null) ? 'disabled':'' ?> value="<?= (!empty($end_date)) ? $end_date:'' ?>">
						</div> -->
						<div class="col-md-3">
							<label class="control-label" for="period"><?= lang('report.sum02'); ?></label>
	                        <div id="reportrange" class="daterangePicker form-control input-sm">
	                            <i data-toggle="tooltip" data-placement="bottom" title="<?= lang('tool.rm01'); ?>" class="glyphicon glyphicon-calendar fa fa-calendar"></i>                             
	                            <span id="dateRangeData"><?= $date_range_value == "" ? date("F j, Y").' - '.date("F j, Y") : $date_range_value ?></span> <b class="caret"></b>
	                            <input type="hidden" id="dateRangeValue" name="dateRangeValue" value="<?= $date_range_value == '' ? '' : $date_range_value; ?>" />
	                            <input type="hidden" id="dateRangeValueStart" name="dateRangeValueStart" value="<?= $start_date == '' ? '' : $start_date; ?>" />
	                            <input type="hidden" id="dateRangeValueEnd" name="dateRangeValueEnd" value="<?= $end_date == '' ? '' : $end_date; ?>" />
	                        </div>
	                    </div>
						<div class="col-md-3">
							<label class="control-label" for="depamt1"><?= lang('report.pr31') . " <="; ?></label>
							<input type="text" name="depamt1" id="depamt1" class="form-control input-sm number_only" value="<?= (!empty($depamt1)) ? $depamt1:'' ?>"/>
						</div>
						<div class="col-md-3">
							<label class="control-label" for="depamt2"><?= lang('report.pr31') . " >="; ?></label>
							<input type="text" name="depamt2" id="depamt2" class="form-control input-sm number_only" value="<?= (!empty($depamt2)) ? $depamt2:'' ?>"/>
						</div>
					</div>
					<div class="form-group" style="margin-bottom:0;">
						<div class="col-md-3">
							<label class="control-label" for="widamt1"><?= lang('report.pr32') . " <="; ?></label>
							<input type="text" name="widamt1" id="widamt1" class="form-control input-sm number_only" value="<?= (!empty($widamt1)) ? $widamt1:'' ?>"/>
						</div>
						<div class="col-md-3">
							<label class="control-label" for="widamt2"><?= lang('report.pr32') . " >="; ?></label>
							<input type="text" name="widamt2" id="widamt2" class="form-control input-sm number_only" value="<?= (!empty($widamt2)) ? $widamt2:'' ?>"/>
						</div>
						<div class="col-md-3">
							<label class="control-label" for="status"><?= lang('lang.status'); ?></label>
							<select class="form-control input-sm" name="status">
								<option value="0" <?= ($status == '0') ? 'selected':'' ?>><?= lang('player.14'); ?></option>
	                            <option value="1" <?= ($status == '1') ? 'selected':'' ?>><?= lang('player.15'); ?></option>
	                        </select>
						</div>
						<div class="col-md-3">
							<label class="control-label" for="playerlevel"><?= lang('report.pr03'); ?></label>
							<select name="playerlevel" id="playerlevel" class="form-control input-sm">
								<option value=""><?= lang('player.08'); ?></option>
		                        <?php foreach ($allLevels as $key => $value) { ?>
		                            <option value="<?= $value['vipsettingcashbackruleId'] ?>" <?= ($playerlevel == $value['vipsettingcashbackruleId']) ? 'selected':'' ?>><?= $value['groupName'].' '.$value['vipLevel'] ?></option>
		                        <?php } ?>
	                        </select>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-3">
							<label class="control-label" for="username"><?= lang('report.pr01'); ?></label>
							<input type="text" name="username" id="username" class="form-control input-sm" value="<?= (!empty($username)) ? $username:'' ?>"/>
						</div>
						<div class="col-md-1" style="text-align:center;padding-top:24px;">
							<input type="submit" value="<?= lang('lang.search'); ?>" id="search_main"class="btn col-md-12 btn-info btn-sm">
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
			<div class="panel-heading custom-ph">
				<h4 class="panel-title custom-pt"><i class="icon-coin-dollar"></i> <?= lang('report.in01'); ?> </h4>
			</div>
			<div class="panel-body" id="playerreport_panel_body">
				<div class="table-responsive">
					<?php if($export_report_permission){ ?>
                        <a href="<?= BASEURL . 'report_management/exportIncomeReportToExcel' ?>" class="btn btn-sm btn-success btn-sm" data-toggle="tooltip" title="<?= lang('lang.export'); ?>" data-placement="top">
                            <i class="glyphicon glyphicon-share"></i>
                        </a>
                    <?php } ?>
                    <hr class="hr_between_table"/>
					<table class="table table-striped table-hover" style="margin: 0px 0 0 0; width: 100%;" id="incomeTable">
						<thead>
							<tr>
								<th></th>
								<th class="input-sm"><?= lang('report.in02'); ?></th>
								<th class="input-sm"><?= lang('report.in20'); ?></th>
								<th class="input-sm"><?= lang('report.in03'); ?></th>
								<th class="input-sm"><?= lang('report.in04'); ?></th>
								<th class="input-sm"><?= lang('report.in05'); ?></th>
								<th class="input-sm"><?= lang('report.in06'); ?></th>
								<th class="input-sm"><?= lang('report.in07'); ?></th>
								<th class="input-sm"><?= lang('report.in08'); ?></th>
								<th class="input-sm"><?= lang('report.in09'); ?></th>		
							</tr>
						</thead>

						<tbody>
							<?php if(!empty($income_report)) { ?>
								<?php 
									foreach ($income_report as $key => $value) { 
								?>
									<tr>
										<td></td>
										<td class="input-sm">
											<a href="<?= BASEURL . 'report_management/viewIncomeReportMonthly/' . urlencode($value['first_date']) . "/" . urlencode($value['last_date']) ?>">
												<?= $value['date'] ?>
											</a>
										</td>
										<td class="input-sm"><?= $value['total_player'] ?></td>
										<td class="input-sm"><?= $value['total_deposit'] ?></td>
										<td class="input-sm"><?= $value['total_withdrawal'] ?></td>
										<td class="input-sm"><?= $value['deposit_bonus'] ?></td>
										<td class="input-sm"><?= $value['cashback_bonus'] ?></td>
										<td class="input-sm"><?= $value['referral_bonus'] ?></td>
										<td class="input-sm"><?= $value['total_bonus'] ?></td>
										<td class="input-sm"><?= $value['total_earned'] ?></td>
									</tr>
							<?php
									}
								}
							?>
						</tbody>
					</table>
				</div>
			</div>
			<div class="panel-footer"></div>
		</div>
	</div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#incomeTable').DataTable( {
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
    });
</script>

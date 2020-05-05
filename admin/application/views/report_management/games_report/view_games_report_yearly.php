<form class="form-horizontal" action="<?=BASEURL . 'report_management/searchGamesReport'?>" method="post" role="form" name="myForm">
	<!--main-->
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary
              " style="margin-bottom:10px;">
				<div class="panel-heading">
					<h4 class="panel-title">
						<i class="icon-search" id="hide_main_up"></i> <?=lang('lang.search');?>
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

$betamt1 = $this->session->userdata('betamt1');
$betamt2 = $this->session->userdata('betamt2');

$lossamt1 = $this->session->userdata('lossamt1');
$lossamt2 = $this->session->userdata('lossamt2');

$winamt1 = $this->session->userdata('winamt1');
$winamt2 = $this->session->userdata('winamt2');

$earnamt1 = $this->session->userdata('earnamt1');
$earnamt2 = $this->session->userdata('earnamt2');

$username = $this->session->userdata('report_game_username');
$playerlevel = $this->session->userdata('report_game_level');
?>
					<div class="form-group" style="margin-bottom:0;">
						<div class="col-md-3">
							<label class="control-label" for="period"><?=lang('report.sum01');?></label>
							<select class="form-control input-sm" name="period" onchange="checkPeriod(this)">
								<option value=""><?=lang('report.pr24');?></option>
	                            <option value="daily" <?=($period == 'daily') ? 'selected' : ''?> ><?=lang('report.pr25');?></option>
	                            <option value="weekly" <?=($period == 'weekly') ? 'selected' : ''?> ><?=lang('report.pr26');?></option>
	                            <option value="monthly" <?=($period == 'monthly') ? 'selected' : ''?> ><?=lang('report.pr27');?></option>
	                            <option value="yearly" <?=($period == 'yearly') ? 'selected' : ''?> ><?=lang('report.pr28');?></option>
	                        </select>
						</div>
						<!-- <div class="col-md-4">
							<label class="control-label" for="start_date"><?=lang('report.sum02');?>: </label>
							<input type="date" name="start_date" id="start_date" class="form-control input-sm" <?=($start_date == null) ? 'disabled' : ''?> value="<?=(!empty($start_date)) ? $start_date : ''?>">
						</div>
						<div class="col-md-4">
							<label class="control-label" for="end_date"><?=lang('report.sum03');?>: </label>
							<input type="date" name="end_date" id="end_date" class="form-control input-sm" <?=($end_date == null) ? 'disabled' : ''?> value="<?=(!empty($end_date)) ? $end_date : ''?>">
						</div> -->
						<div class="col-md-3">
							<label class="control-label" for="period"><?=lang('report.sum02');?></label>
                            <input type="text" id="reportrange" class="form-control input-sm dateInput" data-start="#dateRangeValueStart" data-end="#dateRangeValueEnd"/>
                            <input type="hidden" id="dateRangeValueStart" name="dateRangeValueStart" value="<?=$start_date == '' ? '' : $start_date;?>" />
                            <input type="hidden" id="dateRangeValueEnd" name="dateRangeValueEnd" value="<?=$end_date == '' ? '' : $end_date;?>" />
	                    </div>
						<div class="col-md-3">
							<label class="control-label" for="betamt1"><?=lang('report.g09') . " <= ";?> </label>
							<input type="text" name="betamt1" id="betamt1" class="form-control number_only" value="<?=($betamt1 == null) ? '' : $betamt1?>" />
						</div>
						<div class="col-md-3">
							<label class="control-label" for="betamt2"><?=lang('report.g09') . " >= ";?> </label>
							<input type="text" name="betamt2" id="betamt2" class="form-control number_only" value="<?=($betamt2 == null) ? '' : $betamt2?>" />
						</div>
					</div>
					<div class="form-group" style="margin-bottom:0;">
						<div class="col-md-3">
							<label class="control-label" for="lossamt1"><?=lang('report.g11') . " <= ";?> </label>
							<input type="text" name="lossamt1" id="lossamt1" class="form-control number_only" value="<?=($lossamt1 == null) ? '' : $lossamt1?>" />
						</div>
						<div class="col-md-3">
							<label class="control-label" for="lossamt2"><?=lang('report.g11') . " >= ";?> </label>
							<input type="text" name="lossamt2" id="lossamt2" class="form-control number_only" value="<?=($lossamt2 == null) ? '' : $lossamt2?>" />
						</div>
						<div class="col-md-3">
							<label class="control-label" for="winamt1"><?=lang('report.g10') . " <= ";?> </label>
							<input type="text" name="winamt1" id="winamt1" class="form-control number_only" value="<?=($winamt1 == null) ? '' : $winamt1?>" />
						</div>
						<div class="col-md-3">
							<label class="control-label" for="winamt2"><?=lang('report.g10') . " >= ";?> </label>
							<input type="text" name="winamt2" id="winamt2" class="form-control number_only" value="<?=($winamt2 == null) ? '' : $winamt2?>" />
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-3">
							<label class="control-label" for="username"><?=lang('report.g03');?> </label>
							<input type="text" name="username" id="username" class="form-control" value="<?=($username == null) ? '' : $username?>" />
						</div>
						<div class="col-md-3">
							<label class="control-label" for="playerlevel"><?=lang('report.g05');?> </label>
							<select name="playerlevel" id="playerlevel" class="form-control input-sm">
								<option value=""><?=lang('player.08');?></option>
		                        <?php foreach ($allLevels as $key => $value) {?>
		                            <option value="<?=$value['vipsettingcashbackruleId']?>" <?=($playerlevel == $value['vipsettingcashbackruleId']) ? 'selected' : ''?>><?=$value['groupName'] . ' ' . $value['vipLevel']?></option>
		                        <?php }
?>
	                        </select>
	                    </div>
						<div class="col-md-1" style="text-align:center;padding-top:24px;">
							<input type="submit" value="<?=lang('lang.search');?>" id="search_main"class="btn col-md-12 btn-info btn-sm">
						</div>
	                    <!-- <div class="col-md-3">
							<label class="control-label" for="earnamt1"><?=lang('report.g12') . " <= ";?> </label>
							<input type="text" name="earnamt1" id="earnamt1" class="form-control number_only" value="<?=($earnamt1 == null) ? '' : $earnamt1?>" />
						</div>

						<div class="col-md-3">
							<label class="control-label" for="earnamt2"><?=lang('report.g12') . " >= ";?> </label>
							<input type="text" name="earnamt2" id="earnamt2" class="form-control number_only" value="<?=($earnamt2 == null) ? '' : $earnamt2?>" />
						</div>
					</div>

					<div class="row"> -->
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
				<h4 class="panel-title pull-left"><i class="icon-dice" id="icon"></i> <?=lang('report.s07');?> </h4>
				<div class="pull-right">
                    <?php if ($export_report_permission) {?>
                        <a href="<?=BASEURL . 'report_management/exportGameReportToExcel'?>" >
                            <span data-toggle="tooltip" title="<?=lang('lang.exporttitle');?>" class="btn btn-xs btn-success" data-placement="top"><?=lang('lang.export');?>
                            </span>
                        </a>
                    <?php }
?>
                </div>
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body" id="gamesreport_panel_body">
				<div class="col-md-12">
					<table class="table table-striped table-hover" style="margin: 0px 0 0 0; width: 100%;" id="gamesTable">
						<thead>
							<tr>
								<th></th>
								<th class="input-sm"><?=lang('report.g02');?></th>
								<th class="input-sm"><?=lang('report.g13');?></th>
								<th class="input-sm"><?="PT " . lang('report.g09');?></th>
								<th class="input-sm"><?="AG " . lang('report.g09');?></th>
								<th class="input-sm"><?=lang('report.g09');?></th>
								<th class="input-sm"><?="PT " . lang('report.g10');?></th>
								<th class="input-sm"><?="AG " . lang('report.g10');?></th>
								<th class="input-sm"><?=lang('report.g10');?></th>
								<th class="input-sm"><?="PT " . lang('report.g11');?></th>
								<th class="input-sm"><?="AG " . lang('report.g11');?></th>
								<th class="input-sm"><?=lang('report.g11');?></th>
								<!-- <th class="input-sm"><?="PT " . lang('report.g12');?></th>
								<th class="input-sm"><?="AG " . lang('report.g12');?></th>
								<th class="input-sm"><?=lang('report.g12');?></th> -->
							</tr>
						</thead>

						<tbody>
							<?php if (!empty($games_report)) {
	?>
								<?php
foreach ($games_report as $key => $value) {
		?>
									<tr>
										<td></td>
										<td class="input-sm">
											<a href="<?=BASEURL . 'report_management/viewGamesReportMonthly/' . urlencode($value['first_date']) . "/" . urlencode($value['last_date'])?>">
												<?=$value['date']?>
											</a>
										</td>
										<td class="input-sm"><?=$value['total_player']?></td>
										<td class="input-sm"><?=$value['pt_bets']?></td>
										<td class="input-sm"><?=$value['ag_bets']?></td>
										<td class="input-sm"><?=$value['total_bets']?></td>
										<td class="input-sm"><?=$value['pt_wins']?></td>
										<td class="input-sm"><?=$value['ag_wins']?></td>
										<td class="input-sm"><?=$value['total_wins']?></td>
										<td class="input-sm"><?=$value['pt_loss']?></td>
										<td class="input-sm"><?=$value['ag_loss']?></td>
										<td class="input-sm"><?=$value['total_loss']?></td>
										<!-- <td class="input-sm"><?=$value['pt_earned']?></td>
										<td class="input-sm"><?=$value['ag_earned']?></td>
										<td class="input-sm"><?=$value['total_earned']?></td> -->
									</tr>
							<?php
}
}
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
        $('#gamesTable').DataTable( {
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

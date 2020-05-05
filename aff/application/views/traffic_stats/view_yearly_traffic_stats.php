<!-- <div class="container">
	<br/><br/>

	<div class="row">
		<div class="col-md-12" id="toggleView">
			<div class="panel panel-og">
				<div class="panel-heading">
					<h4 class="panel-title pull-left"> <?=lang('nav.traffic');?></h4>
					<div class="clearfix"></div>
				</div>

				<div class="panel panel-body" id="affiliate_panel_body">
					<div class="panel panel-default">
						<div class="panel-heading">
							<form name="view_affiliate" method="POST" action="<?=BASEURL . 'affiliate/showStats'?>">
								<div class="col-md-4">
					                <div class="for-group">
					                    <?php
$period = $this->session->userdata('period');
$start_date = $this->session->userdata('start_date');
$end_date = $this->session->userdata('end_date');
$date_range_value = $this->session->userdata('date_range_value');

$username = $this->session->userdata('username');
$type_date = $this->session->userdata('type_date');
?>
					                    <div class="col-md-12">
					                        <label for="period" class="control-label" style="font-size:12px;"><?=lang('traffic.period');?>:</label>
					                        <select class="form-control input-sm" name="period" id="period" onchange="checkPeriod(this)">
					                            <option value=""><?=lang('traffic.today');?></option>
					                            <option value="daily" <?=($period == 'daily') ? 'selected' : ''?> ><?=lang('traffic.daily');?></option>
					                            <option value="weekly" <?=($period == 'weekly') ? 'selected' : ''?> ><?=lang('traffic.weekly');?></option>
					                            <option value="monthly" <?=($period == 'monthly') ? 'selected' : ''?> ><?=lang('traffic.monthly');?></option>
					                            <option value="yearly" <?=($period == 'yearly') ? 'selected' : ''?> ><?=lang('traffic.yearly');?></option>
					                        </select>
					                        <?php echo form_error('period', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
					                    </div>
					                    <div class="col-md-12">
					                        <label for="username" class="control-label" style="font-size:12px;"><?=lang('traffic.playerusername');?>:</label>
					                        <input type="text" name="username" class="form-control input-sm" value="<?=($username != null) ? $username : ''?>"/>
					                    </div>
					                </div>
					            </div>

					            <div class="col-md-8">
					                <div class="for-group">
					                    <br/>
					                    <div class="col-md-12">
					                        <label class="radio-inline col-md-3">
					                            <input type="radio" name="type_date" class="type_date" value="registration date" <?=($period != null && $type_date == 'registration date') ? 'checked' : ''?> <?=($period == null) ? 'disabled' : ''?>/> <?=lang('traffic.regdate');?>
					                        </label>
					                        <label class="radio-inline col-md-3">
					                            <input type="radio" name="type_date" class="type_date" value="login date" <?=($period != null && $type_date == 'login date') ? 'checked' : ''?> <?=($period == null) ? 'disabled' : ''?>/> <?=lang('traffic.lastlogin');?>
					                        </label>
					                        <label class="radio-inline col-md-3">
					                            <input type="radio" name="type_date" class="type_date" value="report date" <?=($period != null && $type_date == null || $type_date == 'report date') ? 'checked' : ''?> <?=($period == null) ? 'disabled' : ''?>/> <?=lang('traffic.repdate');?>
					                        </label>
					                    </div>
					                    <div class="col-md-5" id="hide_date" style="margin-top:7px;padding-left:0;padding-left:0;">
					                        <label for="date" class="control-label" style="font-size:12px;"><?=lang('lang.date');?>:</label>
						                    <div id="reportrange" class="daterangePicker form-control input-sm">
						                        <i data-toggle="tooltip" data-placement="bottom" title="<?=lang('tool.rm01');?>" class="glyphicon glyphicon-calendar fa fa-calendar"></i>
						                        <span id="dateRangeData"><?=$date_range_value == "" ? date("F j, Y") . ' - ' . date("F j, Y") : $date_range_value?></span> <b class="caret"></b>
						                        <input type="hidden" id="dateRangeValue" name="dateRangeValue" value="<?=$date_range_value == '' ? '' : $date_range_value;?>" />
						                        <input type="hidden" id="dateRangeValueStart" name="dateRangeValueStart" value="<?=$start_date == '' ? '' : $start_date;?>" />
						                        <input type="hidden" id="dateRangeValueEnd" name="dateRangeValueEnd" value="<?=$end_date == '' ? '' : $end_date;?>" />
						                    </div>
					                    </div>
					                </div>
					            </div>
								<div class="clearfix"></div>
					            <hr style="margin-bottom:0;"/>
					            <div style="text-align:center;">
					                <input type="submit" value="<?=lang('traffic.show');?>" id="search_main"class="btn btn-info btn-sm" >
					            </div>
							</form>
						</div>

						<div class="col-md-12" id="view_stats" style="margin: 30px 0 0 0;">
							<table class="table table-striped table-hover" id="statisticsTable" style="width:100%">
								<thead>
									<tr>
										<th></th>
										<th class="input-sm"><?=lang('lang.date');?></th>
										<th class="input-sm"><?=lang('traffic.totalplayers');?></th>
										<th class="input-sm"><?=lang('traffic.depamount');?></th>
										<th class="input-sm"><?=lang('traffic.widamount');?></th>
				                        <th class="input-sm"><?="PT " . lang('traffic.totalbets');?></th>
				                        <th class="input-sm"><?="AG " . lang('traffic.totalbets');?></th>
										<th class="input-sm"><?=lang('traffic.totalbets');?></th>
				                        <th class="input-sm"><?="PT " . lang('traffic.totalwins');?></th>
				                        <th class="input-sm"><?="AG " . lang('traffic.totalwins');?></th>
										<th class="input-sm"><?=lang('traffic.totalwins');?></th>
				                        <th class="input-sm"><?="PT " . lang('traffic.totalloss');?></th>
				                        <th class="input-sm"><?="AG " . lang('traffic.totalloss');?></th>
										<th class="input-sm"><?=lang('traffic.totalloss');?></th>
										<th class="input-sm"><?=lang('traffic.totalbonus');?></th>
										<th class="input-sm"><?=lang('traffic.totalnetincome');?></th>
									</tr>
								</thead>

								<tbody>
									<?php
foreach ($stats as $value) {
	?>
										<tr>
											<td></td>
											<td class="input-sm"><a href="<?=BASEURL . 'affiliate/viewTrafficStatisticsMonthly/' . $value['first_date'] . "/" . $value['last_date']?>"><?=$value['date']?></a></td>
											<td class="input-sm"><?=$value['total_players']?></td>
											<td class="input-sm"><?=($value['deposit_amount'] == null) ? '0' : $value['deposit_amount']?></td>
											<td class="input-sm"><?=($value['withdrawal_amount'] == null) ? '0' : $value['withdrawal_amount']?></td>
											<td class="input-sm"><?=$value['pt_bets']?></td>
											<td class="input-sm"><?=$value['ag_bets']?></td>
											<td class="input-sm"><?=$value['total_bets']?></td>
											<td class="input-sm"><?=$value['pt_wins']?></td>
											<td class="input-sm"><?=$value['ag_wins']?></td>
											<td class="input-sm"><?=$value['total_wins']?></td>
											<td class="input-sm"><?=$value['pt_loss'];?></td>
											<td class="input-sm"><?=$value['ag_loss'];?></td>
											<td class="input-sm"><?=$value['total_loss'];?></td>
											<td class="input-sm"><?=$value['total_bonus'];?></td>
											<td class="input-sm"><?=$value['total_net_income'];?></td>
										</tr>
									<?php }
?>
								</tbody>
							</table>
						</div>
					</div>
				</div>

				<div class="panel-footer">

				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#statisticsTable').DataTable( {
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
</script> -->

<div class="container">
	<br/><br/>

	<div class="row">
		<div class="col-md-12" id="toggleView">
			<div class="panel panel-og">
				<div class="panel-heading">
					<h4 class="panel-title pull-left"> <?=lang('nav.traffic');?><!-- The traffic data was last updated on <?=date("Y-m-d H:i:s")?> --> </h4>
					<!-- <a href="#" class="btn btn-primary btn-sm pull-right" id="view_affiliate"><span class="glyphicon glyphicon-remove"></span></a> -->
					<div class="clearfix"></div>
				</div>

				<div class="panel panel-body" id="affiliate_panel_body">
					<div class="panel panel-default">
						<div class="panel-heading">
							<form name="view_affiliate" method="POST" action="<?=BASEURL . 'affiliate/showStats'?>">
								<div class="col-md-6 searchSelect">
					                <div class="for-group">
					                    <?php
$period = $this->session->userdata('period');
$start_date = $this->session->userdata('start_date');
$end_date = $this->session->userdata('end_date');
$date_range_value = $this->session->userdata('date_range_value');

$username = $this->session->userdata('username');
$type_date = $this->session->userdata('type_date');
?>
					                    <div class="col-md-12">
					                        <label for="period" class="control-label" style="font-size:12px;"><?=lang('traffic.period');?>:</label>
											<input type="text" class="form-control" id="period" name="period" value="daily" readonly>
					                        <?php echo form_error('period', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
					                    </div>
					                    <div class="col-md-12">
					                        <label for="username" class="control-label" style="font-size:12px;"><?=lang('traffic.playerusername');?>:</label>
					                        <input type="text" name="username" class="form-control input-sm" value="<?=($username != null) ? $username : ''?>"/>
					                    </div>
					                </div>
					            </div>

					            <div class="col-md-6 searchOptions">
					            	<div class="col-md-12">
						            	<label for="period" class="control-label" style="font-size:12px;"><?=lang('traffic.period');?>:</label>
					                	<div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
										    <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
										    <span></span> <b class="caret"></b>
						                        <input type="hidden" id="dateRangeValue" name="dateRangeValue" value="<?=$date_range_value == '' ? '' : $date_range_value;?>" />
						                        <input type="hidden" id="dateRangeValueStart" name="dateRangeValueStart" value="<?=$start_date == '' ? '' : $start_date;?>" />
						                        <input type="hidden" id="dateRangeValueEnd" name="dateRangeValueEnd" value="<?=$end_date == '' ? '' : $end_date;?>" />
										</div>
									</div>
					                <div class="for-group">
					                    <div class="col-md-12" style="margin-top: 20px;">
					                        <label class="radio-inline col-md-3">
					                            <input type="radio" name="type_date" class="type_date" id="registration_date" value="registration date" <?=($period != null && $type_date == 'registration date') ? 'checked' : ''?> <?=($period == null) ? 'disabled' : ''?>/> <?=lang('traffic.regdate');?>
					                        </label>
					                        <label class="radio-inline col-md-3">
					                            <input type="radio" name="type_date" class="type_date" id="login_date" value="login date" <?=($period != null && $type_date == 'login date') ? 'checked' : ''?> <?=($period == null) ? 'disabled' : ''?>/> <?=lang('traffic.lastlogin');?>
					                        </label>
					                        <label class="radio-inline col-md-3">
					                            <input type="radio" name="type_date" class="type_date" id="report_date" value="report date" <?=($period != null && $type_date == null || $type_date == 'report date') ? 'checked' : ''?> <?=($period == null) ? 'disabled' : ''?>/> <?=lang('traffic.repdate');?>
					                        </label>
					                    </div>
					                </div>
					            </div>
								<div class="clearfix"></div>
					            <hr style="margin-bottom:0;"/>
					            <div style="text-align:center;">
					                <input type="submit" value="<?=lang('traffic.show');?>" id="search_main"class="btn btn-info btn-sm" >
					            </div>
							</form>
						</div>

						<div class="col-md-12" id="view_stats" style="margin: 30px 0 0 0;">
							<table class="table table-striped table-hover" id="statisticsTable" style="width:100%">
								<thead>
									<tr>
										<th class="input-sm"><?=lang('lang.date');?></th>
										<th class="input-sm"><?=lang('traffic.totalplayers');?></th>
										<th class="input-sm"><?=lang('traffic.depamount');?></th>
										<th class="input-sm"><?=lang('traffic.widamount');?></th>
				                        <!--th class="input-sm"><?="PT " . lang('traffic.totalbets');?></th>
				                        <th class="input-sm"><?="AG " . lang('traffic.totalbets');?></th-->
										<th class="input-sm"><?=lang('traffic.totalbets');?></th>
				                        <!--th class="input-sm"><?="PT " . lang('traffic.totalwins');?></th>
				                        <th class="input-sm"><?="AG " . lang('traffic.totalwins');?></th-->
										<th class="input-sm"><?=lang('traffic.totalwins');?></th>
				                        <!--th class="input-sm"><?="PT " . lang('traffic.totalloss');?></th>
				                        <th class="input-sm"><?="AG " . lang('traffic.totalloss');?></th-->
										<th class="input-sm"><?=lang('traffic.totalloss');?></th>
										<th class="input-sm"><?=lang('traffic.totalbonus');?></th>
										<!--th class="input-sm"><?=lang('traffic.totalnetincome');?></th-->
									</tr>
								</thead>

								<tbody>
									<?php
foreach ($stats as $value) {
	$date = date('Y-m-d', strtotime($value['date']));
	?>
										<tr>
											<td class="input-sm"><a href="<?=BASEURL . 'affiliate/viewTrafficStatisticsDaily/' . $start_date . "/" . $end_date?>"><?=$value['date']?></a></td>
											<td class="input-sm"><?=$value['total_players']?></td>
											<td class="input-sm"><?=($value['deposit_amount'] == null) ? '0' : $value['deposit_amount']?></td>
											<td class="input-sm"><?=($value['withdrawal_amount'] == null) ? '0' : $value['withdrawal_amount']?></td>
											<!--td class="input-sm"><?=$value['pt_bets']?></td>
											<td class="input-sm"><?=$value['ag_bets']?></td-->
											<td class="input-sm">
												<a href="#" data-toggle="tooltip" data-placement="top" title="pt: <?=$value['pt_bets']?> - ag: <?=$value['ag_bets']?> ">
													<?=$value['total_bets']?>
												</a>
											</td>
											<!--td class="input-sm"><?=$value['pt_wins']?></td>
											<td class="input-sm"><?=$value['ag_wins']?></td-->
											<td class="input-sm">
												<a href="#" data-toggle="tooltip" data-placement="top" title="pt: <?=$value['pt_wins']?> - ag: <?=$value['ag_wins']?> ">
													<?=$value['total_wins']?>
												</a>
											</td>
											<!--td class="input-sm"><?=$value['pt_loss'];?></td>
											<td class="input-sm"><?=$value['ag_loss'];?></td-->
											<td class="input-sm">
												<a href="#" data-toggle="tooltip" data-placement="top" title="pt: <?=$value['pt_loss']?> - ag: <?=$value['ag_loss']?> ">
													<?=$value['total_loss'];?>
												</a>
											</td>
											<td class="input-sm"><?=$value['total_bonus'];?></td>
											<!--td class="input-sm"><?=$value['total_net_income'];?></td-->
										</tr>
									<?php }
?>
								</tbody>
							</table>
						</div>
					</div>
				</div>

				<div class="panel-footer">

				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#statisticsTable').DataTable( {
            "order": [ 0, 'asc' ]
        } );

        $('#reportrange').data('daterangepicker').remove();

        $('[data-toggle="tooltip"]').tooltip();

		// hide affiliate search options by default
		// hideAffSearchOptions();

		// settup date range picker
	    $('#reportrange').daterangepicker({
	        ranges: {
	           'daily': [moment(), moment()],
	           //'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
	           'weekly': [moment().subtract(6, 'days'), moment()],
	           //'Last 30 Days': [moment().subtract(29, 'days'), moment()],
	           'monthly': [moment().startOf('month'), moment().endOf('month')],
	           //'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
	           'yearly': [moment().startOf('year'), moment().endOf('year')]
	        }
	    }, cb);
	    // callback date rangep icker
		function cb(start, end, label) {
	        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
	        $('#period').val(label);

	        checkPeriod(document.getElementById("period"));
	    }
	    cb(moment(), moment(), 'yearly');
    } );
</script>
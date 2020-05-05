<form class="form-horizontal" action="<?= BASEURL . 'report_management/searchSummaryReport' ?>" method="POST">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary
              " style="margin-bottom:10px;">
				<div class="panel-heading">
					<h4 class="panel-title">
						<i class="icon-search"></i> <?= lang('lang.search'); ?>
						<a href="#main" 
              class="btn btn-default btn-sm pull-right hide_sortby"> 
							<i class="glyphicon glyphicon-chevron-up hide_sortby_up"></i>
						</a>
						<div class="clearfix"></div>
					</h4>
				</div>
				<div class="panel-body sortby_panel_body">
					<?php 
                        $start_date = $this->session->userdata('start_date');
                        $end_date = $this->session->userdata('end_date');
                        $date_range_value = $this->session->userdata('date_range_value');

                        $new_registered_players = $this->session->userdata('new_registered_players');
                        $total_registered_players = $this->session->userdata('total_registered_players');
                        $first_dep_players = $this->session->userdata('first_dep_players');
                        $second_dep_players = $this->session->userdata('second_dep_players');

                        $total_dep_amt = $this->session->userdata('total_dep_amt');
                        $total_dep_amt_range = $this->session->userdata('total_dep_amt_range');

                        $total_wid_amt = $this->session->userdata('total_wid_amt');
                        $total_wid_amt_range = $this->session->userdata('total_wid_amt_range');

                        $pt_gross_income = $this->session->userdata('pt_gross_income');
                        $pt_gross_income_range = $this->session->userdata('pt_gross_income_range');

                        $ag_gross_income = $this->session->userdata('ag_gross_income');
                        $ag_gross_income_range = $this->session->userdata('ag_gross_income_range');

                        $total_gross_income = $this->session->userdata('total_gross_income');
                        $total_gross_income_range = $this->session->userdata('total_gross_income_range');

                        $bonus = $this->session->userdata('bonus');
                        $bonus_range = $this->session->userdata('bonus_range');

                        $cashback = $this->session->userdata('cashback');
                        $cashback_range = $this->session->userdata('cashback_range');

                        $game_net_income = $this->session->userdata('game_net_income');
                        $game_net_income_range = $this->session->userdata('game_net_income_range');
                    ?>

					<div class="form-group">
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
						
						<div class="col-md-9">
							<div style="float:left; margin: 25px 0 0 0; border:1px solid #E8E8E8; border-radius:5px; padding:0 10px 2px 10px;">
								<input type="checkbox" name="new_registered" <?= ($new_registered_players == 'on') ? 'checked':'' ?> /> <?= lang('report.sum05'); ?> &nbsp;&nbsp;
								<input type="checkbox" name="total_registered" <?= ($total_registered_players == 'on') ? 'checked':'' ?> /> <?= lang('report.sum06'); ?> &nbsp;&nbsp;
								<input type="checkbox" name="first_deposit" <?= ($first_dep_players == 'on') ? 'checked':'' ?> /> <?= lang('report.sum07'); ?> &nbsp;&nbsp;
								<input type="checkbox" name="second_deposit" <?= ($second_dep_players == 'on') ? 'checked':'' ?> /> <?= lang('report.sum08'); ?> &nbsp;&nbsp;
							</div>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-2">
							<label class="control-label" for="total_deposit_amt"><?= lang('report.sum09'); ?></label>
							<div class="row">
								<div class="col-md-6 nopadding">
									<select name="total_dep_amt_range" class="form-control">
										<option value="1" <?= ($total_dep_amt_range == "1") ? 'selected':'' ?> > >= </option>
										<option value="2" <?= ($total_dep_amt_range == "2") ? 'selected':'' ?> > <= </option>
									</select>
								</div>

								<div class="col-md-6">
									<input type="text" name="total_dep_amt" class="form-control number_only" value="<?= $total_dep_amt ?>" />
								</div>
							</div>
						</div>

						<div class="col-md-2">
							<label class="control-label" for="total_wid_amt"><?= lang('report.sum10'); ?></label>
							<div class="row">
								<div class="col-md-6 nopadding">
									<select name="total_wid_amt_range" class="form-control">
										<option value="1" <?= ($total_wid_amt_range == "1") ? 'selected':'' ?> > >= </option>
										<option value="2" <?= ($total_wid_amt_range == "2") ? 'selected':'' ?> > <= </option>
									</select>
								</div>

								<div class="col-md-6">
									<input type="text" name="total_wid_amt" class="form-control number_only" value="<?= $total_wid_amt ?>" />
								</div>
							</div>
						</div>

						<div class="col-md-2">
							<label class="control-label" for="pt_gross_income"><?= lang('report.sum11'); ?></label>
							<div class="row">
								<div class="col-md-6 nopadding">
									<select name="pt_gross_income_range" class="form-control">
										<option value="1" <?= ($pt_gross_income_range == "1") ? 'selected':'' ?> > >= </option>
										<option value="2" <?= ($pt_gross_income_range == "2") ? 'selected':'' ?> > <= </option>
									</select>
								</div>

								<div class="col-md-6">
									<input type="text" name="pt_gross_income" class="form-control number_only" value="<?= $pt_gross_income ?>" />
								</div>
							</div>
						</div>

						<div class="col-md-2">
							<label class="control-label" for="ag_gross_income"><?= lang('report.sum12'); ?></label>
							<div class="row">
								<div class="col-md-6 nopadding">
									<select name="ag_gross_income_range" class="form-control">
										<option value="1" <?= ($ag_gross_income_range == "1") ? 'selected':'' ?> > >= </option>
										<option value="2" <?= ($ag_gross_income_range == "2") ? 'selected':'' ?> > <= </option>
									</select>
								</div>

								<div class="col-md-6">
									<input type="text" name="ag_gross_income" class="form-control number_only" value="<?= $ag_gross_income ?>" />
								</div>
							</div>
						</div>

						<div class="col-md-2">
							<label class="control-label" for="total_gross_income"><?= lang('report.sum13'); ?></label>
							<div class="row">
								<div class="col-md-6 nopadding">
									<select name="total_gross_income_range" class="form-control">
										<option value="1" <?= ($total_gross_income_range == "1") ? 'selected':'' ?> > >= </option>
										<option value="2" <?= ($total_gross_income_range == "2") ? 'selected':'' ?> > <= </option>
									</select>
								</div>

								<div class="col-md-6">
									<input type="text" name="total_gross_income" class="form-control number_only" value="<?= $total_gross_income ?>" />
								</div>
							</div>
						</div>

						<div class="col-md-2">
							<label class="control-label" for="bonus"><?= lang('report.sum14'); ?></label>
							<div class="row">
								<div class="col-md-6 nopadding">
									<select name="bonus_range" class="form-control">
										<option value="1" <?= ($bonus_range == "1") ? 'selected':'' ?> > >= </option>
										<option value="2" <?= ($bonus_range == "2") ? 'selected':'' ?> > <= </option>
									</select>
								</div>

								<div class="col-md-6">
									<input type="text" name="bonus" class="form-control number_only" value="<?= $bonus ?>" />
								</div>
							</div>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-2">
							<label class="control-label" for="cahsback"><?= lang('report.sum15'); ?></label>
							<div class="row">
								<div class="col-md-6 nopadding">
									<select name="cahsback_range" class="form-control">
										<option value="1" <?= ($cashback_range == "1") ? 'selected':'' ?> > >= </option>
										<option value="2" <?= ($cashback_range == "2") ? 'selected':'' ?> > <= </option>
									</select>
								</div>

								<div class="col-md-6">
									<input type="text" name="cahsback" class="form-control number_only" value="<?= $cashback ?>" />
								</div>
							</div>
						</div>

						<div class="col-md-2">
							<label class="control-label" for="game_net_income"><?= lang('report.sum17'); ?></label>
							<div class="row">
								<div class="col-md-6 nopadding">
									<select name="game_net_income_range" class="form-control">
										<option value="1" <?= ($game_net_income_range == "1") ? 'selected':'' ?> > >= </option>
										<option value="2" <?= ($game_net_income_range == "2") ? 'selected':'' ?> > <= </option>
									</select>
								</div>

								<div class="col-md-6">
									<input type="text" name="game_net_income" class="form-control number_only" value="<?= $game_net_income ?>" />
								</div>
							</div>
						</div>

						<div class="col-md-2" style="margin: 25px 0 0 0;">
							<input type="submit" value="<?= lang('lang.search'); ?>" id="search_main" class="btn btn-info btn-sm">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

<div class="row">
	<div class="col-md-12" id="toggleView">
		<div class="panel panel-primary">
			<div class="panel-heading custom-ph">
                <h4 class="panel-title">
					<i class="icon-pie-chart"></i> <?= lang('report.s08'); ?> 
                    <!-- <a href="#personal" 
              class="btn btn-default btn-sm hide_sortby pull-right">
                        <i class="glyphicon glyphicon-chevron-up hide_sortby_up"></i>
                    </a>
                    <div id="reportrange" class="pull-right daterangePicker" style="padding-right:4px;padding-left:4px;border:1px solid #C7C7C7;border-radius:3px;background:#fff;margin-right:10px;margin-top:2px;">
                        <i data-toggle="tooltip" data-placement="bottom" title="<?= lang('tool.rm01'); ?>" class="glyphicon glyphicon-calendar fa fa-calendar"></i>                             
                        <span id="dateRangeData"><?= $this->session->userdata('dateRangeValue') == "" ? date("F j, Y", strtotime('-7 day')).' - '.date("F j, Y", strtotime('-1 day')) : $this->session->userdata('dateRangeValue')?></span> <b class="caret"></b>
                        <input type="hidden" id="dateRangeValue" name="dateRangeValue" value="<?= $this->session->userdata('dateRangeValue') == '' ? '' : $this->session->userdata('dateRangeValue'); ?>" />
                        <input type="hidden" id="dateRangeValueStart" name="dateRangeValueStart" value="<?= $this->session->userdata('dateRangeValueStart') == '' ? '' : $this->session->userdata('dateRangeValueStart'); ?>" />
                        <input type="hidden" id="dateRangeValueEnd" name="dateRangeValueEnd" value="<?= $this->session->userdata('dateRangeValueEnd') == '' ? '' : $this->session->userdata('dateRangeValueEnd'); ?>" />
                    </div> -->
                    <div class="clearfix"></div>
                </h4>
			</div>
			<div class="panel-body" id="summaryreport_panel_body">
				<div class="table-responsive">
					<?php if($export_report_permission){ ?>
				        <a href="<?= BASEURL . 'report_management/exportSummaryReportToExcel' ?>" class="btn btn-sm btn-success btn-sm" data-toggle="tooltip" title="<?= lang('lang.export'); ?>" data-placement="top">
				            <i class="glyphicon glyphicon-share"></i>
				        </a>
				    <?php } ?>
				    <hr class="hr_between_table"/>
					<table class="table table-striped table-hover" style="margin: 0px 0 0 0; width: 100%;" id="playerTable">
						<thead>
							<tr>
								<th></th>
								<th class="input-sm"><?= lang('report.pr01'); ?></th>
								<th class="input-sm"><?= lang('report.pr02'); ?></th>
								<th class="input-sm"><?= lang('report.pr03'); ?></th>
								<th class="input-sm"><?= lang('report.sum14'); ?></th>	
								<th class="input-sm"><?= lang('report.sum02'); ?></th>			
							</tr>
						</thead>

						<tbody>
							<?php if(!empty($player)) { ?>
								<?php 
									foreach ($player as $key => $value) { 
								?>
									<tr>
										<td></td>
										<td class="input-sm"><?= $value['username'] ?></td>
										<td class="input-sm"><?= ($value['realname'] == null) ? lang('lang.norecord'):$value['realname'] ?></td>
										<td class="input-sm"><?= $value['playerlevel'] ?></td>
										<td class="input-sm"><?= ($value['amount'] == null) ? lang('lang.norecord'):number_format($value['amount'], 2, '.', ' ');  ?></td>
										<td class="input-sm"><?= ($value['approvedDate'] == null) ? lang('lang.norecord'):$value['approvedDate']  ?></td>
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
        $('#playerTable').DataTable( {
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

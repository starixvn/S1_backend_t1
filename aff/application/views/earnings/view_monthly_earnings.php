<div class="container">
	<br/><br/>
	
	<div class="row">
		<div class="col-md-12" id="toggleView">
			<div class="panel panel-primary">
				<div class="nav-head panel-heading">
					<h4 class="panel-title pull-left"> <?= lang('nav.earnings'); ?> </h4>
					<!-- <a href="#" class="btn btn-primary btn-sm pull-right" id="view_affiliate"><span class="glyphicon glyphicon-remove"></span></a> -->
					<div class="clearfix"></div>
				</div>

				<div class="panel panel-body" id="affiliate_panel_body">
					<div class="panel panel-default">
						<div class="panel-heading">
							<form name="view_affiliate" method="POST" action="<?= BASEURL . 'affiliate/showEarnings' ?>">
								<div class="col-md-4">
					                <div class="for-group">
					                    <?php 
					                        $period = $this->session->userdata('period');
						                    $start_date = $this->session->userdata('start_date');
						                    $end_date = $this->session->userdata('end_date');
						                    $date_range_value = $this->session->userdata('date_range_value');
					                    ?>
					                    <div class="col-md-12">
					                        <label for="period" class="control-label" style="font-size:12px;"><?= lang('traffic.period'); ?>:</label>
					                        <select class="form-control input-sm" name="period" id="period" onchange="checkPeriod(this)">
					                            <option value=""><?= lang('traffic.today'); ?></option>
					                            <option value="daily" <?= ($period == 'daily') ? 'selected':'' ?> ><?= lang('traffic.daily'); ?></option>
					                            <option value="weekly" <?= ($period == 'weekly') ? 'selected':'' ?> ><?= lang('traffic.weekly'); ?></option>
					                            <option value="monthly" <?= ($period == 'monthly') ? 'selected':'' ?> ><?= lang('traffic.monthly'); ?></option>
					                            <option value="yearly" <?= ($period == 'yearly') ? 'selected':'' ?> ><?= lang('traffic.yearly'); ?></option>
					                        </select>
					                        <?php echo form_error('period', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
					                    </div>
					                </div>
					            </div>

					            <div class="col-md-8">
					                <div class="for-group">
					                    <div class="col-md-12" id="hide_date">
					                        <div class="col-md-6">
						                        <label for="date" class="control-label" style="font-size:12px;"><?= lang('lang.date'); ?>:</label>
							                    <div id="reportrange" class="daterangePicker form-control input-sm" <?= ($period == null || $period  == 'today') ? 'disabled':'' ?>>
							                        <i data-toggle="tooltip" data-placement="bottom" title="<?= lang('tool.rm01'); ?>" class="glyphicon glyphicon-calendar fa fa-calendar"></i>                             
							                        <span id="dateRangeData"><?= $date_range_value == "" ? date("F j, Y").' - '.date("F j, Y") : $date_range_value ?></span> <b class="caret"></b>
							                        <input type="hidden" id="dateRangeValue" name="dateRangeValue" value="<?= $date_range_value == '' ? '' : $date_range_value; ?>" />
							                        <input type="hidden" id="dateRangeValueStart" name="dateRangeValueStart" value="<?= $start_date == '' ? '' : $start_date; ?>" />
							                        <input type="hidden" id="dateRangeValueEnd" name="dateRangeValueEnd" value="<?= $end_date == '' ? '' : $end_date; ?>" />
							                    </div>
					                            <?php echo form_error('start_date', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
					                        </div>
					                    </div>
					                </div>
					            </div>
								<div class="clearfix"></div>
					            <hr style="margin-bottom:0;"/>
					            <div style="text-align:center;">
					                <input type="submit" value="<?= lang('earn.show'); ?>" id="search_main"class="btn btn-info btn-sm" >
					            </div> 
							</form>
						</div>

						<div class="col-md-12" id="view_earnings" style="margin: 30px 0 0 0;">
							<table class="table table-striped table-hover" id="earningsTable" style="width:100%">
								<thead>
									<tr>
										<th></th>
										<th class="input-sm"><?= lang('lang.date'); ?></th>
										<th class="input-sm"><?= lang('earn.actplayers'); ?></th>
										<th class="input-sm"><?= lang('earn.openbal'); ?></th>
										<th class="input-sm"><?= lang('earn.current'); ?></th>
										<th class="input-sm"><?= lang('earn.approved'); ?></th>
										<th class="input-sm"><?= lang('earn.closebal'); ?></th>
										<th class="input-sm"><?= lang('lang.notes'); ?></th>
									</tr>
								</thead>

								<tbody>
									<?php if(!empty($earnings)) { ?>
										<?php foreach ($earnings as $value) { ?>
											<tr>
												<td></td>
												<td class="input-sm"><a href="<?= BASEURL . 'affiliate/viewEarningsWeekly/' . $value['first_date'] . '/' . $value['last_date'] ?>"><?= $value['date'] ?></a></td>
												<td class="input-sm"><?= $value['active_players'] ?></td>
												<td class="input-sm"><?= $value['opening_balance'] ?></td>
												<td class="input-sm"><?= $value['earnings'] ?></td>
												<td class="input-sm"><?= $value['approved'] ?></td>
												<td class="input-sm"><?= $value['closing_balance'] ?></td>
												<td class="input-sm"><?= ($value['notes'] == null) ? '<i>n/a</i>':$value['notes'] ?></td>
											</tr>
										<?php }
											} 
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
        $('#earningsTable').DataTable( {
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
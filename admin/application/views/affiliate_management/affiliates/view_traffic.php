<form action="<?= BASEURL . 'affiliate_management/trafficSearchPage/' . $affiliate['affiliateId'] ?>" method="post" role="form" name="myForm">
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

				<div class="panel panel-body" id="main_panel_body">
					<div class="row">
						<div class="col-md-1">
							<h6><label for="sign_time_period"><?= lang('aff.ai62'); ?>:</label></h6>
						</div>

						<div class="col-md-3">
							<select class="form-control input-sm" name="sign_time_period" onchange="specify(this)">
								<option value=""><?= lang('lang.all'); ?></option>
								<option value="week"><?= lang('lang.week'); ?></option>
								<option value="month"><?= lang('lang.month'); ?></option>
								<option value="past"><?= lang('lang.months'); ?></option>
								<option value="specify"><?= lang('aff.vb07'); ?></option>
							</select>
								<?php echo form_error('sign_time_period', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
						</div>

						<div id="hide_date">
							<div class="col-md-1">
								<h6><label for="start_date"><?= lang('aff.vb08'); ?>: </label></h6>
							</div>

							<div class="col-md-3">
								<input type="date" name="start_date" id="start_date" class="form-control input-sm" disabled="disabled">
									<?php echo form_error('start_date', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
							</div>

							<div class="col-md-1">
								<h6><label for="end_date"><?= lang('aff.vb09'); ?>: </label></h6>
							</div>

							<div class="col-md-3">
								<input type="date" name="end_date" id="end_date" class="form-control input-sm" disabled="disabled">
									<?php echo form_error('end_date', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <span class="help-block" style="color:#ff6666;" id="mdate"></span><br/>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-1 col-md-offset-4">
							<input type="submit" value="<?= lang('lang.search'); ?>" id="search_main"class="btn btn-success btn-sm">
						</div>

						<div class="col-md-2">
							<input type="reset" value="<?= lang('lang.reset'); ?>" class="btn btn-default btn-sm">
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
	<!--end of main-->
</form>

<!--action-->
<form action="<?= BASEURL . 'affiliate_management/trafficSortPage/' . $affiliate['affiliateId'] ?>" method="post" role="form">
	<div class="row">
		<div class="col-md-12" style="margin:-15px 0 -15px 0;">
			<div class="panel panel-info">
				<div class="panel-heading">
					<div class="row">
						<div class="col-md-1">
							<h6><label for="sort_by"><?= lang('lang.sort'); ?>:</label></h6>
						</div>

						<div class="col-md-3">
							<select class="form-control input-sm" name="sort_by">
								<option value="t.start_date" <?= $this->session->userdata('traffic_sort_by') == 't.start_date' || !$this->session->userdata('traffic_sort_by') ? 'selected' : ''?> ><?= lang('aff.at01'); ?></option>
								<option value="t.players" <?= $this->session->userdata('traffic_sort_by') == 't.players' ? 'selected' : ''?>><?= lang('aff.at02'); ?></option>
								<option value="t.register_players" <?= $this->session->userdata('traffic_sort_by') == 't.register_players'  ? 'selected' : ''?>><?= lang('aff.at03'); ?></option>
								<option value="t.deposit_players" <?= $this->session->userdata('traffic_sort_by') == 't.deposit_players'  ? 'selected' : ''?>><?= lang('aff.at04'); ?></option>
								<option value="t.deposit_amount" <?= $this->session->userdata('traffic_sort_by') == 't.deposit_amount'  ? 'selected' : ''?>><?= lang('aff.at05'); ?></option>
								<option value="t.withdraw_amount" <?= $this->session->userdata('traffic_sort_by') == 't.withdraw_amount'  ? 'selected' : ''?>><?= lang('aff.at06'); ?></option>
								<option value="t.bets" <?= $this->session->userdata('traffic_sort_by') == 't.bets'  ? 'selected' : ''?>><?= lang('aff.at07'); ?></option>
								<option value="t.wins" <?= $this->session->userdata('traffic_sort_by') == 't.wins'  ? 'selected' : ''?>><?= lang('aff.at08'); ?></option>
							</select>
						</div>

						<div class="col-md-1">
							<h6><label for="sort_by"><?= lang('lang.in'); ?></label></h6>
						</div>

						<div class="col-md-2" style="margin-left:-50px;">
							<select class="form-control input-sm" name="in">
								<option value="asc" <?= $this->session->userdata('traffic_in') == 'asc' ? 'selected' : ''?> ><?= lang('lang.asc'); ?></option>
								<option value="desc" <?= $this->session->userdata('traffic_in') == 'desc' || !$this->session->userdata('traffic_in') ? 'selected' : ''?>><?= lang('lang.desc'); ?></option>
							</select>
						</div>
						<div class="col-md-1">
							<h6><label for="number_traffic_list"><?= lang('aff.vb22'); ?></label></h6>
						</div>

						<?php
							$five = '';
							$ten = '';
							$fifty = '';
							$one_hundred = '';

							if($this->session->userdata('number_traffic_list') == 5) {
								$five = 'selected';
								$ten = '';
								$fifty = '';
								$one_hundred = '';
							}

							if($this->session->userdata('number_traffic_list') == 10) {
								$ten = 'selected';
								$five = '';
								$fifty = '';
								$one_hundred = '';
							}

							if($this->session->userdata('number_traffic_list') == 50) {
								$fifty = 'selected';
								$ten = '';
								$five = '';
								$one_hundred = '';
							}

							if($this->session->userdata('number_traffic_list') == 100) {
								$one_hundred = 'selected';
								$ten = '';
								$fifty = '';
								$five = '';
							}

						?>

						<div class="col-md-2" style="margin-left:-30px;">
							<select name="number_traffic_list" id="number_traffic_list" class="form-control input-sm">
								<option value="5" <?= $five ?>>5</option>
								<option value="10" <?= $ten ?>>10</option>
								<option value="50" <?= $fifty ?>>50</option>
								<option value="100" <?= $one_hundred ?>>100</option>
							</select>
						</div>
						<div class="col-md-2" style="margin-left:-20px;">
							<h6><label for="number_traffic_list"><?= lang('aff.vb23'); ?> </label></h6>
						</div>
						<div class="col-md-1">
							<input type="submit" value="<?= lang('lang.apply'); ?>" class="btn btn-success btn-sm">
						</div>
							<?php echo form_error('', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
					</div>

				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
</form>
<!--end of action-->

<div class="row">
	<div class="col-md-12" id="toggleView">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title pull-left"><i class="glyphicon glyphicon-list-alt"></i> <?= lang('aff.at09'); ?>: <i><?= $affiliate['username'] ?></i> </h4>
				<div class="pull-right">
					<!--a href="#show_search" role="button" class="btn btn-primary" id="show_search"><span class="glyphicon glyphicon-search"></span></a-->
					<a href="<?= BASEURL . 'affiliate_management/viewAffiliates' ?>" role="button" class="btn btn-primary"><span class="glyphicon glyphicon-remove"></span></a>
				</div>
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body" id="traffic_panel_body">
				<div id="trafficList" class="table-responsive" style="overflow: auto;">
					<!--center><span class="help-block">Total number of rows: <?= $count_all ?> (page <?= $current_page ?> of <?= $total_pages ?>), Calculated <?= $today ?></span></center-->
					<table class="table table-striped table-hover" style="margin: 0px 0 0 0;" id="myTable">
						<thead>
							<tr>
								<th><?= lang('aff.at01'); ?></th>
								<th><?= lang('aff.at02'); ?></th>
								<th><?= lang('aff.at03'); ?></th>
								<th><?= lang('aff.at04'); ?></th>
								<th><?= lang('aff.at05'); ?></th>
								<th><?= lang('aff.at06'); ?></th>
								<th><?= lang('aff.at07'); ?></th>
								<th><?= lang('aff.at08'); ?></th>
							</tr>
						</thead>

						<tbody>
							<?php
								if(!empty($traffic)) {
									foreach($traffic as $traffic) {
										$date = date('Y-m-d', strtotime($traffic['start_date']));
							?>	
										<tr>
											<td><?= $date ?></td>
											<?php if($traffic['players'] > 0) { ?>
												<td><a href="#players" onclick="players('<?= $traffic['trafficId'] ?>')"><?= $traffic['players'] ?></a></td>
											<?php } else { ?>
												<td><?= $traffic['players'] ?></td>
											<?php } ?>
											<td><?= $traffic['register_players'] ?></td>
											<td><?= $traffic['deposit_players'] ?></td>
											<td><?= $traffic['deposit_amount'] ?></td>
											<td><?= $traffic['withdraw_amount'] ?></td>
											<td><?= $traffic['bets'] ?></td>
											<td><?= $traffic['wins'] ?></td>
										</tr>
					        <?php 
					    			}
				    			} else {
				    		?>
										<tr>
				                            <td colspan="8" style="text-align:center"><span class="help-block"><?= lang('lang.norec'); ?></span></td>
				                        </tr>				    		
				    		<?php		
				    			} 
					        ?>
						</tbody>
					</table>

					<br/>

					<div class="col-md-12 col-offset-0">
					    <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
				    </div>
				</div>
			</div>

		</div>
	</div>

	<div class="col-md-7" id="traffic_details" style="display: none;">

	</div>
</div>
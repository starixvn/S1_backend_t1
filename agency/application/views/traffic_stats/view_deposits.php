<div class="container">
	<br/><br/>
	
	<div class="row">
		<div class="col-md-12" id="toggleView">
			<div class="panel panel-og">
				<div class="panel-heading">
					<h4 class="panel-title pull-left"> <?= lang('traffic.playerdeposit'); ?> </h4>
					<a href="<?= BASEURL . 'affiliate/trafficStats' ?>" class="btn-xs btn-info pull-right" id="view_affiliate"><span class="glyphicon glyphicon-remove"></span></a>
					<div class="clearfix"></div>
				</div>

				<div class="panel panel-body">
					<div class="row">
						<div class="col-md-12">
							<table class="table table-striped">
								<thead>
									<th><?= lang('login.Username'); ?></th>
									<th><?= lang('traffic.regdate'); ?></th>
									<th><?= lang('traffic.lastlogin'); ?></th>
									<th><?= lang('traffic.firstdep'); ?></th>
									<th><?= lang('traffic.depamount'); ?></th>
								</thead>

								<tbody>
									<?php if(!empty($players)) { ?>
										<?php 
											foreach ($players as $value) { 
												$registration_date = date('Y-m-d', strtotime($value['createdOn']));
												$last_login_date = date('Y-m-d', strtotime($value['lastLoginTime']));

												if($value['first_deposit_date'] == null) {
													$first_deposit_date = 'n/a';
												} else {
													$first_deposit_date = date('Y-m-d', strtotime($value['first_deposit_date']));
												}
										?>
											<tr>
												<td><?= $value['username'] ?></td>
												<td><?= $registration_date ?></td>
												<td><?= $last_login_date ?></td>
												<td><?= $first_deposit_date ?></td>
												<td><?= ($value['deposit_amount'] == null) ? '0':$value['deposit_amount'] ?></td>
											</tr>
										<?php } ?>
									<?php } else { ?>
											<tr>
							                    <td colspan="5" style="text-align:center"><span class="help-block">No Records Found</span></td>
											</tr>
									<?php } ?>
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
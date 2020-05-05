<form action="<?=site_url('payment_management/sortDepositWithdrawalList/manualthirdpartydeposit')?>" method="post" role="form">

	<!-- start date picker range api -->
	<div class="well" style="overflow: auto;margin-bottom:10px;">

		<!-- start dashboard notification -->
		<a class='notificationRefreshList' href="<?=site_url('payment_management/refreshList/manualThirdPartyDepositRequestList')?>">
			<div class="col-md-2 notificationDashboard hover-shadow <?=$this->session->userdata('dwStatus') == 'request' ? 'notDboard-active' : ''?>" id="notificationDashboard-request-deposit">
				<?=lang('pay.total');?><br/><span class="notificationDashboardTxt" id="notificationDashboard-request-deposit"><?=$deposit_request_cnt?></span><br/>
				<span class="notificationDashboardLabel" id="notificationDashboard-request-deposit"><?=lang('pay.depreq');?></span>
			</div>
		</a>
		<a class='notificationRefreshList' href="<?=site_url('payment_management/refreshList/manualThirdPartyDepositApprovedList')?>">
			<div class="col-md-2 notificationDashboard hover-shadow <?=$this->session->userdata('dwStatus') == 'approved' ? 'notDboard-active' : ''?>" id="notificationDashboard-approved-deposit">
				<?=lang('pay.total');?><br/><span class="notificationDashboardTxt" id="notificationDashboard-approved-deposit"><?=$deposit_approved_cnt?></span><br/>
				<span class="notificationDashboardLabel" id="notificationDashboard-approved-deposit"><?=lang('pay.appreq');?></span>
			</div>
		</a>
		<a class='notificationRefreshList' href="<?=site_url('payment_management/refreshList/manualThirdPartyDepositDeclinedList')?>">
			<div class="col-md-2 notificationDashboard hover-shadow <?=$this->session->userdata('dwStatus') == 'declined' ? 'notDboard-active' : ''?>" id="notificationDashboard-declined-deposit">
				<?=lang('pay.total');?><br/><span class="notificationDashboardTxt" id="notificationDashboard-declined-deposit"><?=$deposit_declined_cnt?></span><br/>
				<span class="notificationDashboardLabel" id="notificationDashboard-declined-deposit"><?=lang('pay.decreq');?></span>
			</div>
		</a>
		<!-- end dashboard notification -->

		<!-- start sort dw list -->

			<!-- <div><h2 class="pull-right">Deposit Request</h2></div> -->
			<div class="pull-right daterangePicker-sec">
			   <h4><?=lang('pay.transperd');?></h4>
			   <div class="pull-left">
	               <div id="reportrange" class="pull-right daterangePicker">
	                  <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
	                  <span id="dateRangeData"><?=$this->session->userdata('dateRangeValue') == "" ? date("F j, Y", strtotime('-7 day')) . ' - ' . date("F j, Y") : $this->session->userdata('dateRangeValue')?></span> <b class="caret"></b>
	               </div>
	               	  <input type="hidden" id="dateRangeValue" name="dateRangeValue" value="<?=$this->session->userdata('dateRangeValue') == '' ? '' : $this->session->userdata('dateRangeValue');?>" />
	                  <input type="hidden" id="dateRangeValueStart" name="dateRangeValueStart" value="<?=$this->session->userdata('dateRangeValueStart') == '' ? '' : $this->session->userdata('dateRangeValueStart');?>" />
                      <input type="hidden" id="dateRangeValueEnd" name="dateRangeValueEnd" value="<?=$this->session->userdata('dateRangeValueEnd') == '' ?: $this->session->userdata('dateRangeValueEnd');?>" />
	                  <input type="hidden" id="dwStatus" name="dwStatus" value="<?=$this->session->userdata('dwStatus') == '' ? 'request' : $this->session->userdata('dwStatus')?>" />
	            </div>
	            <div class="col-md-1">
		            <input type="submit" class="btn btn-sm btn-info" value="<?=lang('lang.submit');?>" />
	           	</div>
	            <br/><br/>
               <!-- <span class="pull-right" id="moreFilterBtn"><a class="moreFilter-btn">[Show Filter]</a></span> -->
           </div>
		    <!-- end sort dw list -->
    </div>
    <!-- end date picker range api -->
</form>

<div class="row">
	<!-- start request list -->
	<div class="col-md-12" id="toggleView">
		<div class="col-md-5"></div>
		<div class="panel panel-primary">
			<div class="panel-heading custom-ph">
				<h4 class="panel-title custom-pt">
					<i class="icon-note"></i> <?=$transactionType?>
					<span class="choosenDateRange">&nbsp;<?=isset($choosenDateRange) ? ($choosenDateRange) : ''?></span>
					<button class="btn btn-default btn-sm pull-right glyphicon glyphicon-refresh" data-toggle="tooltip" data-placement="left" title="<?= lang('lang.refresh'); ?>" onclick="location.reload();"></button>
				</h4>
			</div>

			<!-- start data table -->
			<div class="panel-body" id="player_panel_body">
				<div id="paymentList" class="table-responsive">
					<table class="table table-striped table-hover tablepress table-condensed" id="myTable" style="margin: 0px 0 0 0; width: 100%;">
						<thead>
							<tr>
								<th></th>
								<th class="tableHeaderFont"><?=lang('lang.action');?></th>
								<th class="tableHeaderFont"><?=lang('system.word38');?></th>
								<th class="tableHeaderFont"><?=lang('system.word39');?></th>
								<th class="tableHeaderFont"><?=lang('pay.playerlev');?></th>
								<th class="tableHeaderFont"><?=lang('pay.amt');?></th>
								<th class="tableHeaderFont"><?=lang('pay.curr');?></th>
								<th class="tableHeaderFont"><?=lang('pay.depmethod');?></th>
								<!-- <th class="tableHeaderFont"><?=lang('con.bnk10');?></th> -->
								<th class="tableHeaderFont"><?=lang('pay.reqtime');?></th>
								<th class="tableHeaderFont"><?=lang('pay.transrefnumber');?></th>
								<th class="tableHeaderFont"><?=lang('pay.thirdpartacctpaymentname');?></th>
								<th class="tableHeaderFont"><?=lang('pay.thirdpartacct');?></th>
								<!-- <th>Merchant Account</th> -->
								<th class="tableHeaderFont"><?=lang('pay.promoname');?></th>
								<th class="tableHeaderFont"><?=lang('pay.promobonus');?></th>
								<th class="tableHeaderFont"><?=lang('pay.deposit') . ' ' . lang('pay.ip');?></th>
								<th class="tableHeaderFont"><?=lang('pay.deposit') . ' ' . lang('pay.locatn');?></th>
								<!-- <th>Bonus Amount</th> -->
								<?php if ($this->session->userdata('dwStatus') == 'approved' || $this->session->userdata('dwStatus') == 'declined') {?>
									<th class="tableHeaderFont"><?=lang('pay.procssby');?></th>
									<th class="tableHeaderFont"><?=lang('pay.procsson');?></th>
								<?php	}
?>
								<th class="tableHeaderFont"><?=lang('pay.depslip');?></th>
							</tr>
						</thead>

						<tbody>
							<?php //var_dump($depositRequest);
if (!empty($depositRequest)) {
	foreach ($depositRequest as $depositRequest) {
		?>
										<tr>
											<td></td>
											<td>
											<?php if ($depositRequest['dwStatus'] == 'approved') {?>
												<span class="btn btn-xs btn-info review-btn" onclick="PaymentManagementProcess.getManualThirdPartyDepositApproved(<?=$depositRequest['walletAccountId']?>)" data-toggle="modal" data-target="#approvedDetailsModal">
													<?=lang("lang.details");?>
												</span>

											<?php } elseif ($depositRequest['dwStatus'] == 'declined') {?>
												<span class="btn btn-xs btn-info review-btn" onclick="PaymentManagementProcess.getManualThirdPartyDepositDeclined(<?=$depositRequest['walletAccountId']?>)" data-toggle="modal" data-target="#declinedDetailsModal">
													<?=lang("lang.details");?>
												</span>

											<?php } else {?>
												<span class="btn btn-xs btn-info review-btn" onclick="PaymentManagementProcess.getManualThirdPartyDepositRequest(<?=$depositRequest['walletAccountId']?>,<?=$depositRequest['dwMethod']?>)" data-toggle="modal" data-target="#requestDetailsModal">
													<?=lang("lang.details");?>
												</span>
											<?php }
		?>
											</td>
											<td><?=$depositRequest['username'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $depositRequest['username']?></td>
											<td><?=$depositRequest['firstName'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : ucwords($depositRequest['firstName'] . ' ' . $depositRequest['lastName'])?></td>
											<td><?=$depositRequest['groupName'] . ' ' . $depositRequest['vipLevel']?></td>
											<td><?=$depositRequest['amount'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $depositRequest['amount']?></td>
											<td><?=$depositRequest['currency'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $depositRequest['currency']?></td>
											<td><?=$depositRequest['depositTo'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $depositRequest['depositTo']?></td>
											<!-- <td><?=$depositRequest['transactionFee'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $depositRequest['transactionFee']?></td> -->
											<td><?=$depositRequest['dwDateTime'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : mdate('%M %d, %Y %H:%i:%s', strtotime($depositRequest['dwDateTime']))?></td>
											<td><?=$depositRequest['transacRefCode'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $depositRequest['transacRefCode']?></td>
											<td><?=$depositRequest['depositorName'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $depositRequest['depositorName']?></td>
											<td><?=$depositRequest['depositorAccount'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $depositRequest['depositorAccount']?></td>
											<td><?=$depositRequest['promoName'] == '' ? '<i class="help-block">' . lang("lang.no") . " " . lang("lang.promo") . '<i/>' : $depositRequest['promoName']?></td>
											<!-- <td><?=$depositRequest['promoName'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : "<a href='#' data-toggle='modal' data-target='#promoDetails' onclick='viewPromoRuleDetails(" . $depositRequest["promorulesId"] . ")'><span data-toggle='tooltip' title='' data-original-title='" . lang("cms.showPromoRuleDetails") . "'>" . $depositRequest['promoName'] . "</span></a>";?></td> -->
											<td><?=$depositRequest['bonusAmount'] == '' ? '<i class="help-block">' . lang("lang.no") . " " . lang("lang.bonus") . '<i/>' : $depositRequest['bonusAmount']?></td>
											<td><?=$depositRequest['dwIp'] == '' ? '<i class="help-block">' . lang("lang.no") . " " . lang("lang.bonus") . '<i/>' : $depositRequest['dwIp']?></td>
											<td><?=$depositRequest['dwLocation'] == ',' ? '<i class="help-block">' . lang("lang.no") . " " . lang("lang.record") . '<i/>' : $depositRequest['dwLocation']?></td>
											<?php if ($depositRequest['dwStatus'] == 'approved' || $depositRequest['dwStatus'] == 'declined') {?>
														<td><?=$depositRequest['processedByAdmin'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : ucwords($depositRequest['processedByAdmin'])?></td>
														<td><?=$depositRequest['processDatetime'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : mdate('%M %d, %Y %H:%i:%s', strtotime($depositRequest['processDatetime']))?></td>
											<?php	}
		?>
											<td><?php if ($depositRequest['depositSlipName'] == '.') {?>
													<i class="help-block"><?=lang("lang.no") . " " . lang("pay.depslip");?><i/>
												<?php } else {?>
												<span class="btn btn-xs btn-default depositSlipBtn" onclick="PaymentManagementProcess.setDepositSlipValue('<?=IMAGEPATH_DEPOSITSLIP . $depositRequest['depositSlipName']?>')" data-toggle="modal" data-target="#depositSlipModal">
													Open
												</span>
												<?php }
		?>
												<!-- start depositSlipModal-->
												<div class="row">
													<div class="modal fade" id="depositSlipModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
														<div class="modal-dialog">
															<div class="modal-content modal-content-three">
																<div class="modal-header">
																	<a class='notificationRefreshList' href="<?=site_url('payment_management/refreshList/depositApproved')?>">
																		<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only"><?=lang("lang.close");?></span></button>
																	</a>
																	<h4 class="modal-title" id="myModalLabel"><i class="icon-note"></i>&nbsp;<?=lang("pay.depslip");?></h4>
																</div>

																<div class="modal-body">
																	<?php if (!empty($depositRequest['depositSlipName'])) {?>
																	<img id="banner_name" class="depositSlipImage" src="<?=IMAGEPATH_DEPOSITSLIP . $depositRequest['depositSlipName']?>" >
																	<?php }
		?>
																</div>
															</div>
														</div>
													</div>
												</div>
												<!-- end depositSlipModal-->
											</td>
										</tr>
							<?php
}
} else {?>
										<!-- <tr>
											<td colspan="18" style="text-align:center"><?=lang("lang.norec");?>
											</td>
										</tr> -->
							<?php	}
?>
						</tbody>
					</table>

					<!-- <div class="panel-footer">
						<ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links();?> </ul>
					</div> -->
				</div>
			</div>
			<!-- end data table -->
			<div class="panel-footer"></div>
		</div>
	</div>
	<!-- end request list -->

	<!-- start requestDetailsModal-->
	<div class="row">
		<div class="modal fade" id="requestDetailsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content modal-content-three">
					<div class="modal-header">
						<a class='notificationRefreshList' href="<?=site_url('payment_management/refreshList/manualThirdPartyDepositRequestList')?>">
							<button type="button" class="close"><span aria-hidden="true">×</span><span class="sr-only"><?=lang("lang.close");?></span></button>
						</a>
						<h4 class="modal-title" id="myModalLabel"><i class="icon-note"></i>&nbsp;<?=lang("pay.deposit") . ' ' . lang("pay.req") . ' ' . lang("lang.details");?></h4>
					</div>

					<div class="modal-body">
						<div class="col-md-12" id="checkPlayer">
							<!-- Deposit transaction -->
							<div class="row">
								<div class="col-md-12">
									<div class="panel panel-primary">
										<div class="panel-heading">
											<h4 class="panel-title">
												<?=lang("pay.deposit") . ' ' . lang('lang.info');?>
												<a href="#personal" 
              id="hide_deposit_info" class="btn btn-default btn-sm pull-right">
													<i class="glyphicon glyphicon-chevron-down" id="hide_deposit_info_up"></i>
												</a>
												<div class="clearfix"></div>
											</h4>
										</div>

										<div class="panel-body" id="deposit_info_panel_body" style="display: none;">
											<div class="row">
												<div class="col-md-12">
													<div class="col-md-2">
														<label for="userName"><?=lang("pay.user") . ' ' . lang("pay.name");?>:</label>
														<input type="hidden" class="form-control playerId" readonly/>
														<input type="text" class="form-control userName" readonly/>
													</div>

													<div class="col-md-3">
														<label for="playerName"><?=lang("pay.realname");?>:</label>
														<input type="text" class="form-control playerName" readonly/>
													</div>

													<div class="col-md-3">
														<label for="playerLevel"><?=lang('pay.playerlev');?>:</label>
														<input type="text" class="form-control playerLevel" readonly/>
													</div>

													<div class="col-md-2">
														<label for="memberSince"><?=lang('pay.memsince');?>: </label>
														<input type="text" class="form-control memberSince" readonly>
													</div>

													<div class="col-md-2">
														<label for="depositCnt"><?=lang('player.ui14');?>: </label>
														<input type="text" class="form-control depositCnt" readonly>
													</div>
												</div>
											</div>

											<!-- start payment method -->
											<hr/>
											<h4><?=lang('pay.paytmethdetls');?></h4>
											<hr/>

											<!-- start paypal payment method -->
											<div class="">
												<div class="row">
													<div class="col-md-12">
														<div class="col-md-3">
															<label for="depositedAmount"><?=lang('pay.deposit') . ' ' . lang('pay.amt');?>:</label>
															<input type="text" class="form-control depositedAmount" readonly/>
														</div>

														<div class="col-md-3">
															<label for="depositMethod"><?=lang('pay.depmethod');?>:</label>
															<input type="text" class="form-control depositMethod" readonly/>
														</div>

														<div class="col-md-3">
															<label for="dateDeposited"><?=lang('lang.date') . ' ' . lang('pay.deposited');?>:</label>
															<input type="text" class="form-control dateDeposited" readonly/>
														</div>

														<div class="col-md-3">
															<label for="ipLoc"><?=lang('pay.deposit') . ' ' . lang('pay.ip') . ' ' . lang('lang.and') . ' ' . lang('pay.locatn');?>:</label>
															<input type="text" class="form-control ipLoc" readonly/>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-md-12">
														<br/>
														<div class="col-md-3">
															<label for="ipLoc"><?=lang('pay.transrefnumber');?>:</label>
															<input type="text" class="form-control mt_transacCode" readonly/>
														</div>

														<div class="col-md-3">
															<label for="ipLoc"><?=lang('pay.acctname');?>:</label>
															<input type="text" class="form-control mt_depositorName" readonly>
														</div>

														<div class="col-md-3">
															<label for="ipLoc"><?=lang('pay.thirdpartacct');?>:</label>
															<input type="text" class="form-control mt_depositorAccount" readonly>
														</div>
													</div>
												</div>
											</div>
											<!-- end paypal payment method -->

											<!-- start bonus info -->
											<div class="row playerBonusInfoPanel">
												<div class="col-md-12">
													<hr/>
													<h4><?=lang('pay.activePlayerPromo')?></h4>
													<hr/>
													<div class="col-md-12 ">
														<table class="table playerBonusTable table-striped table-hover tablepress table-condensed">
															<th><?=lang('player.up01');?></th>
															<th><?=lang('cms.promocode');?></th>
															<th><?=lang('pay.bonusamt');?></th>
															<th><?=lang('pay.dateJoined');?></th>
															<!-- <th><?=lang('player.up03');?></th> -->
														</table>
													</div>
												</div>
											</div>
											<!-- end bonus info -->

											<!-- start bonus info -->
											<div class="row bonusInfoPanel">
												<div class="col-md-12">
													<hr/>
													<h4><?=lang('pay.bonapplictn') . ' ' . lang('lang.details');?></h4>
													<hr/>
													<div class="col-md-3">
														<label for="promoName"><?=lang('lang.promo') . ' ' . lang('pay.name');?>:</label>
														<input type="text" class="form-control promoName" readonly/>
														<input type="hidden" class="form-control playerDepositPromoId" readonly/>
														<br/>
													</div>

													<div class="col-md-3">
														<label for="playerPromoBonusAmount"><?=lang('pay.compbonamt');?>:</label>
														<input type="text" class="form-control" id="requestPlayerPromoBonusAmount" readonly/>
														<br/>
													</div>
												</div>
											</div>
											<!-- end bonus info -->

											<div class="row">
												<hr/>
												<div class="col-md-12" id="playerRequestDetails"></div>
												<div class="col-md-12" id="playerDepositSlip"></div>
												<div class="col-md-12 pull-right" id="repondBtn">
													<hr/>
													<input type="hidden" class="form-control walletAccountIdVal" readonly/>
													<input type="hidden" class="form-control" id="requestPlayerPromoIdVal" readonly/>
													<button class="btn btn-md btn-info" onclick="respondToDepositRequest()"><?=lang('lang.approve');?></button>
													<button class="btn btn-md btn-info" onclick="PaymentManagementProcess.showDeclineReason()"><?=lang('lang.decline');?></button>
												</div>
												<div class="col-md-5" id="declineReason-sec">
													<p><?=lang('pay.plsadddeclreason');?>:</p>
													<textarea class="form-control" cols="50" rows="5" id="declinedReasonTxt"></textarea><br/>
													<input type="checkbox" name="showDeclinedReason_cbx" id="showDeclinedReason_cbx"> <?=lang('pay.showtoplayr');?><br/><br/>
													<button class="btn btn-md btn-info" onclick="PaymentManagementProcess.respondToDepositDeclined(<?=$depositRequest['walletAccountId']?>)"><?=lang('pay.declnow');?></button>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<!--end of Deposit transaction-->

						</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end requestDetailsModal-->

	<!-- start approvedDetailsModal-->
	<div class="row">
		<div class="modal fade" id="approvedDetailsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content modal-content-three">
					<div class="modal-header">
						<a class='notificationRefreshList' href="<?=site_url('payment_management/refreshList/thirdPartyDepositApproved')?>">
							<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only"><?=lang("lang.close");?></span></button>
						</a>
						<h4 class="modal-title" id="myModalLabel"><i class="icon-note"></i>&nbsp;<?=lang('pay.appdepodetals');?></h4>
					</div>

					<div class="modal-body">

						<!-- player transaction -->
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-primary">
									<div class="panel-heading">
										<h4 class="panel-title">
											<?=lang('pay.appdepoinfo');?>
											<a href="#approvedDeposit" 
              id="hide_approved_deposit_transac" class="btn btn-default btn-sm pull-right">
												<i class="glyphicon glyphicon-chevron-up" id="hide_approved_deposit_transac_up"></i>
											</a>
											<div class="clearfix"></div>
										</h4>
									</div>

									<div class="panel-body" id="approved_deposit_transac_panel_body" style="display: none;">
										<div class="row">
											<div class="col-md-12">
												<div class="row">
													<div class="col-md-12">
														<div class="col-md-3">
															<label for="userName"><?=lang("pay.user") . ' ' . lang("pay.name");?>:</label>
															<input type="text" class="form-control userName" readonly/>
														</div>

														<div class="col-md-3">
															<label for="playerName"><?=lang("pay.realname");?>:</label>
															<input type="text" class="form-control playerName" readonly/>
														</div>

														<div class="col-md-3">
															<label for="playerLevel"><?=lang('pay.playerlev');?>:</label>
															<input type="text" class="form-control playerLevel" readonly/>
														</div>

														<div class="col-md-3">
															<label for="memberSince"><?=lang('pay.memsince');?>: </label>
															<input type="text" class="form-control memberSince" readonly>
														</div>
													</div>
												</div>
												<br/>

											<!-- start payment method -->
											<hr/>
											<h4><?=lang('pay.paytmethdetls');?></h4>
											<hr/>
											<!-- start paypal payment method -->
											<div class="">
												<div class="row">
													<div class="col-md-12">
														<div class="col-md-3">
															<label for="depositedAmount"><?=lang('pay.deposit') . ' ' . lang('pay.amt');?>:</label>
															<input type="text" class="form-control depositedAmount" readonly/>
														</div>

														<div class="col-md-3">
															<label for="depositMethod"><?=lang('pay.depmethod');?>:</label>
															<input type="text" class="form-control depositMethod" readonly/>
														</div>

														<div class="col-md-3">
															<label for="dateDeposited"><?=lang('lang.date') . ' ' . lang('pay.deposited');?>:</label>
															<input type="text" class="form-control dateDeposited" readonly/>
														</div>

														<div class="col-md-3">
															<label for="ipLoc"><?=lang('pay.deposit') . ' ' . lang('pay.ip') . ' ' . lang('lang.and') . ' ' . lang('pay.locatn');?>:</label>
															<input type="text" class="form-control ipLoc" readonly/>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-md-12">
														<br/>
														<div class="col-md-3">
															<label for="ipLoc"><?=lang('pay.transrefnumber');?>:</label>
															<input type="text" class="form-control mt_transacCode" readonly/>
														</div>

														<div class="col-md-3">
															<label for="ipLoc"><?=lang('pay.acctname');?>:</label>
															<input type="text" class="form-control mt_depositorName" readonly>
														</div>

														<div class="col-md-3">
															<label for="ipLoc"><?=lang('pay.thirdpartacct');?>:</label>
															<input type="text" class="form-control mt_depositorAccount" readonly>
														</div>
													</div>
												</div>
											</div>
											<!-- end paypal payment method -->
											<!-- start bonus info -->
											<div class="row bonusInfoPanel">

												<div class="col-md-12">
													<hr/>
													<h4><?=lang('pay.bonapplictn') . ' ' . lang('lang.details');?></h4>
													<hr/>
													<div class="col-md-3">
														<label for="promoName"><?=lang('lang.promo') . ' ' . lang('pay.name');?>:</label>
														<input type="text" class="form-control promoName" readonly/>
														<input type="hidden" class="form-control playerDepositPromoId" readonly/>
														<br/>
													</div>

													<div class="col-md-3">
														<label for="playerPromoBonusAmount"><?=lang('pay.compbonamt');?>:</label>
														<input type="text" class="form-control" id="approvedPlayerPromoBonusAmount" readonly/>
														<br/>
													</div>
												</div>
											</div>
											<!-- end bonus info -->
												<hr/>
												<div class="row">
													<div class="col-md-12">

														<div class="col-md-1">
															<label for="depositMethodApprovedBy"><?=lang('pay.apprvby');?>:</label>
														</div>

														<div class="col-md-3">
															<input type="text" class="form-control" id="depositMethodApprovedBy" readonly>
															<br/>
														</div>

														<div class="col-md-1">
															<label for="depositMethodDateApproved"><?=lang('pay.datetimeapprv');?>:</label>
														</div>

														<div class="col-md-3">
															<input type="text" class="form-control" id="depositMethodDateApproved" readonly>
															<br/>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="clearfix"></div>
								</div>
							</div>
						</div>
						<!--end of player transaction-->

					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end approvedDetailsModal-->

	<!-- start declinedDetailsModal-->
	<div class="row">
		<div class="modal fade" id="declinedDetailsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content modal-content-three">
					<div class="modal-header">
						<a class='notificationRefreshList' href="<?=site_url('payment_management/refreshList/manualThirdPartyDepositDeclinedList')?>">
							<button type="button" class="close"><span aria-hidden="true">×</span><span class="sr-only"><?=lang("lang.close");?></span></button>
						</a>
						<h4 class="modal-title" id="myModalLabel"><i class="icon-note"></i>&nbsp;<?=lang('pay.decldepdetls');?></h4>
					</div>

					<div class="modal-body">
						<!-- Deposit transaction -->
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-primary">
									<div class="panel-heading">
										<h4 class="panel-title">
											<?=lang('pay.decldepinfo');?>
											<a href="#depositInformation" 
              id="hide_player_transac_history" class="btn btn-default btn-sm pull-right">
												<i class="glyphicon glyphicon-chevron-up" id="hide_player_transac_history_up"></i>
											</a>
											<div class="clearfix"></div>
										</h4>
									</div>

									<div class="panel-body" id="declined_deposit_info_panel_body" style="display: none;">
										<div class="row">
												<div class="row">
													<div class="col-md-12">
														<div class="col-md-3">
															<label for="userName"><?=lang("pay.user") . ' ' . lang("pay.name");?>:</label>
															<input type="text" class="form-control userName" readonly/>
														</div>

														<div class="col-md-3">
															<label for="playerName"><?=lang("pay.realname");?>:</label>
															<input type="text" class="form-control playerName" readonly/>
														</div>

														<div class="col-md-3">
															<label for="playerLevel"><?=lang('pay.playerlev');?>:</label>
															<input type="text" class="form-control playerLevel" readonly/>
														</div>

														<div class="col-md-3">
															<label for="memberSince"><?=lang('pay.memsince');?>: </label>
															<input type="text" class="form-control memberSince" readonly>
														</div>
													</div>
												</div>
												<br/>
										</div>
										<h4><?=lang('pay.paytmethdetls');?></h4>
										<hr/>
										<!-- start paypal payment method -->
										<div class="paypalPaymentMethodSection">
												<div class="row">
													<div class="col-md-12">
														<div class="col-md-3">
															<label for="depositedAmount"><?=lang('pay.deposit') . ' ' . lang('pay.amt');?>:</label>
															<input type="text" class="form-control depositedAmount" readonly/>
														</div>

														<div class="col-md-3">
															<label for="depositMethodApprovedDeposit"><?=lang('pay.depmethod');?>:</label>
															<input type="text" class="form-control depositMethod" readonly/>
														</div>

														<div class="col-md-3">
															<label for="dateDepositedApprovedDeposit"><?=lang('lang.date') . ' ' . lang('pay.deposited');?>:</label>
															<input type="text" class="form-control dateDeposited" readonly/>
														</div>

														<div class="col-md-3">
															<label for="ipLoc"><?=lang('pay.deposit') . ' ' . lang('pay.ip') . ' ' . lang('lang.and') . ' ' . lang('pay.locatn');?></label>
															<input type="text" class="form-control ipLoc" readonly/>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-md-12">
														<br/>
														<div class="col-md-3">
															<label for="ipLoc"><?=lang('pay.transrefnumber');?>:</label>
															<input type="text" class="form-control mt_transacCode" readonly/>
														</div>

														<div class="col-md-3">
															<label for="ipLoc"><?=lang('pay.acctname');?>:</label>
															<input type="text" class="form-control mt_depositorName" readonly>
														</div>

														<div class="col-md-3">
															<label for="ipLoc"><?=lang('pay.thirdpartacct');?>:</label>
															<input type="text" class="form-control mt_depositorAccount" readonly>
														</div>
													</div>
												</div>
										</div>

										<!-- end payment method -->
										<!-- start bonus info -->
										<div class="row bonusInfoPanel">

											<div class="col-md-12">
												<hr/>
												<h4><?=lang('pay.bonapplictn') . ' ' . lang('lang.details');?></h4>
												<hr/>
												<div class="col-md-3">
													<label for="promoName"><?=lang('lang.promo') . ' ' . lang('pay.name');?>:</label>
													<input type="text" class="form-control promoName" readonly/>
													<input type="hidden" class="form-control playerDepositPromoId" readonly/>
													<br/>
												</div>

												<div class="col-md-3">
													<label for="playerPromoBonusAmount"><?=lang('pay.compbonamt');?>:</label>
													<input type="text" class="form-control" id="declinedPlayerPromoBonusAmount" readonly/>
													<br/>
												</div>
											</div>
										</div>
										<!-- end bonus info -->

										<div class="row">
											<hr/>
											<div class="col-md-12">
												<div class="col-md-3">
													<label for="depositMethodDeclinedBy"><?=lang('pay.declby');?>:</label>
													<input type="text" class="form-control" id="depositMethodDeclinedBy" readonly>
												</div>

												<div class="col-md-3">
													<label for="depositMethodDateDeclined"><?=lang('pay.datetimedecl');?>:</label>
													<input type="text" class="form-control" id="depositMethodDateDeclined" readonly>
												</div>

												<div class="col-md-6">
													<label for="depositMethodReasonDeclined"><?=lang('pay.reason');?>:</label>
													<textarea class="form-control" id="depositMethodReasonDeclined" readonly></textarea>
												</div>
											</div>
										</div>

										<div class="row">
											<hr/>
											<div id="playerDeclinedDetails"></div>
											<div class="col-md-12" id="playerDeclinedDetailsRepondBtn">
												<input type="hidden" class="form-control walletAccountIdVal" readonly/>
												<input type="hidden" class="form-control" id="declinedPlayerPromoIdVal" readonly/>
												<button class="btn btn-md btn-info" onclick="PaymentManagementProcess.depositDeclinedToApprove(<?=$depositRequest['walletAccountId']?>)"><?=lang('pay.changetoapprv');?></button>
											</div>
											<!--<hr/>
											 <div class="col-md-12" id="declineReason-sec">
												<p>Please Add Declined Reason:</p>
												<textarea cols="50" rows="5" id="declinedReasonTxt"></textarea><br/><br/>
												<button class="btn-md btn-info" onclick="PaymentManagementProcess.respondToDepositDeclined(<?=$depositRequest['walletAccountId']?>)">Submit</button>
											</div> -->
										</div>
									</div>

									<div class="clearfix"></div>
									</div>
								</div>
						</div>
						<!--end of Deposit transaction-->

					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end declinedDetailsModal-->
</div>

<div class="modal fade bs-example-modal-md" id="promoDetails" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel" style="margin: 0 10px;"><?=lang('cms.promoRuleDetails');?>: </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                       <table class="table">
                           <tr>
                                <td style="width:15%; border:0px; text-align:right;">
                                    <?=lang('cms.promoType');?>
                                </td>
                                <td style="text-align:left; border:0px;">
                                    <input type="text" class="form-control input-sm" id="promoType" readonly>
                                </td>

                                <td style="width:15%; border:0px; text-align:right;">
                                    <?=lang('cms.promoname');?>
                                </td>
                                <td style="text-align:left; border:0px;">
                                    <input type="text" class="form-control input-sm" id="promoName" readonly>
                                </td>
                           </tr>

                           <tr>
                                <td style="border:0px; text-align:right;">
                                    <?=lang('cms.promoCat');?>
                                </td>
                                <td style="text-align:left; border:0px;">
                                    <input type="text" class="form-control input-sm" id="promoCat" readonly>
                                </td>

                                <td style="border:0px; text-align:right;">
                                    <?=lang('cms.promocode');?>
                                </td>
                                <td style="text-align:left; border:0px;">
                                    <input type="text" class="form-control input-sm" id="promoCode" readonly>
                                </td>
                           </tr>

                           <tr>
                                <td style="border:0px; text-align:right;">
                                    <?=lang('cms.validityStartDate');?>
                                </td>
                                <td style="text-align:left; border:0px;">
                                    <input type="text" class="form-control input-sm" id="validityStartDate" readonly>
                                </td>

                                <td style="border:0px; text-align:right;">
                                    <?=lang('cms.validityEndDate');?>
                                </td>
                                <td style="text-align:left; border:0px;">
                                    <input type="text" class="form-control input-sm" id="validityEndDate" readonly>
                                </td>
                           </tr>

                          <tr>
                                <td style="border:0px; text-align:right;">
                                    <?=lang('cms.appStartDate');?>
                                </td>
                                <td style="text-align:left; border:0px;">
                                    <input type="text" class="form-control input-sm" id="applicationPeriodStart" readonly>
                                </td>

                                <td style="border:0px; text-align:right;">
                                    <?=lang('cms.appEndDate');?>
                                </td>
                                <td style="text-align:left; border:0px;">
                                    <input type="text" class="form-control input-sm" id="applicationPeriodEnd" readonly>
                                </td>
                           </tr>

                           <tr>
                                <td style="border:0px; text-align:right;">
                                    <?=lang('cms.promodesc');?>
                                </td>
                                <td style="text-align:left; border:0px;" colspan="4">
                                    <textarea class="form-control  input-sm" readonly id="promoDesc"></textarea>
                                </td>
                           </tr>

                           <!-- <tr>
                                <td style="border:0px;">
                                    <?=lang('cms.createdby');?>
                                </td>
                                <td style="text-align:left; border:0px;">
                                    <input type="text" class="form-control input-sm" id="createdBy" readonly>
                                </td>
                                <td style="border:0px;">
                                    <?=lang('cms.createdon');?>
                                </td>
                                <td style="text-align:left; border:0px;">
                                    <input type="text" class="form-control input-sm" id="createdOn" readonly>
                                </td>
                           </tr> -->


                       </table>
                       <hr/>
                       <!-- **********************
                            | deposit condition  |
                            ********************** -->
                       <div class="row depositCondSec">
                            <div class="col-md-12">
                                <h4><?=lang('cms.depCon');?></h4>
                            </div>
                            <div class="col-md-12">
                                <input type="text" class="form-control input-sm" id="depositConditionType" readonly>
                            </div>
                       </div>

                       <!-- **********************
                            | bonus condition sec |
                            ********************** -->
                       <div class="row depositSuccesionSec">
                            <div class="col-md-12">
                                <h4><?=lang('cms.depSuccession') . " <span style='font-size:12px;'>(" . lang('cms.depSuccessionInfo') . ")</span>";?></h4>
                            </div>
                            <div class="col-md-12">
                                <input type="text" class="form-control input-sm" id="depSuccessionType" readonly>
                            </div>
                       </div>

                       <div class="row applicationSec">
                            <div class="col-md-12">
                                <h4><?=lang('cms.singleOrMultiple');?></h4>
                            </div>
                            <div class="col-md-12">
                                <input type="text" class="form-control input-sm" id="singleOrMultiple" readonly>
                                <input type="text" class="form-control input-sm" id="repeatCondition" readonly>
                            </div>
                       </div>

                       <!-- ******************
                            | bonus release  |
                            ****************** -->
                       <div class="row bonusReleaseSec">
                            <div class="col-md-12">
                                <h4><?=lang('cms.bonusRelease');?></h4>
                            </div>
                            <div class="col-md-12">
                                <input type="text" class="form-control input-sm" id="bonusRelease" readonly>
                            </div>
                       </div>

                       <!-- ************************
                            | withdraw requirement |
                            ************************ -->
                       <div class="row withdrawRequirementSec">
                            <div class="col-md-12">
                                <h4><?=lang('cms.withdrawRequirement');?></h4>
                            </div>
                            <div class="col-md-12">
                                <input type="text" class="form-control input-sm" id="withdrawRequirement" readonly>
                            </div>
                       </div>
                       <!-- *********************
                            | allowed game type |
                            ********************* -->
                       <div class="row allowedGameTypeSec">
                            <br/>
                            <div class="col-md-12">
                                <center>
                                    <h4><?=lang('cms.allowedGameType');?></h4>
                                </center>
                            </div>
                            <div class="row">
                                <div class="col-md-1"></div>
                                <div class="col-md-10" style="margin:5px; border:1px solid #ddd; border-radius: 10px; padding:10px; background-color:#fff;">
                                    <ul id="allowedGameTypeItemSec"></ul>
                                </div>
                            </div>
                       </div>

                       <!-- *********************
                            | game bet condition |
                            ********************* -->
                       <div class="row gameBetConditionSec">
                            <div class="col-md-12">
                                <h4><?=lang('cms.requiredAmount');?></h4>
                            </div>
                            <div class="col-md-12">
                                <input type="text" class="form-control input-sm" id="requiredGameBetAmount" readonly>
                            </div>
                            <div class="col-md-12">
                                <hr/>
                                <center>
                                    <h4><?=lang('cms.gameBetCondition');?></h4>
                                </center>
                            </div>
                            <div class="row">
                                <div class="col-md-1"></div>
                                <div class="col-md-10" style="margin:5px; border:1px solid #ddd; border-radius: 10px; padding:10px; background-color:#fff;">
                                    <ul id="gameBetConditionItemSec"></ul>
                                </div>
                            </div>
                       </div>
                       <!-- ****************
                            | player level |
                            **************** -->
                       <div class="row playerLevelSec">
                            <div class="col-md-12">
                                <center>
                                    <h4><?=lang('cms.allowedPlayerLevel');?></h4>
                                </center>
                            </div>
                            <div class="row">
                                <div class="col-md-1"></div>
                                <div class="col-md-10" style="margin:5px; border:1px solid #ddd; border-radius: 10px; padding:10px; background-color:#fff;">
                                    <ul id="playerLevelItemSec"></ul>
                                </div>
                            </div>
                       </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <center>
                                <br/>
                                <button class="btn btn-primary btn-md btn-block" style="width:30%" data-dismiss="modal">OK</button>
                            </center>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
    	var requestTimeCol=8;
        $('#myTable').DataTable({
	        // "language": {
	        // 	// "lengthMenu": "Display _MENU_ records per page",
	        //     "url": "<?php echo $this->utils->jsUrl('lang/chinese.json');?>"
	        // },
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
            "order": [ requestTimeCol, 'desc' ]
        });
    });

    function respondToDepositRequest() {

        html  = '';
        html += '<p>';
        html += 'Loading Data...';
        html += '</p>';

       $('#playerRequestDetails').html(html);

       var playerId = $('.playerId').val();
       var walletAccountIdVal = $('.walletAccountIdVal').val();
       var depositAmount = $('.depositedAmount').val();
       var playerDepositPromoId = $('.playerDepositPromoId').val();
       var bankDepositTransacFeeSettingVal = $('#bankDepositTransacFeeSettingVal').val();
       var bankDepositTransacFeeVal = $('#transactionFee').val();

       if (playerDepositPromoId == '') {
            playerDepositPromoId = null;
       }

       $.ajax({
            'url' : site_url('payment_management/approveDepositRequest/'+walletAccountIdVal+'/'+depositAmount+'/'+playerId+'/'+playerDepositPromoId+'/'+bankDepositTransacFeeSettingVal+'/'+bankDepositTransacFeeVal),
            'type' : 'GET',
            'success' : function(data){
                            html  = '';
                            html += '<p>';
                            html += 'Deposit has been Approved!';
                            html += '</p>';

                           $('#playerRequestDetails').html(html);
                           $('#repondBtn').hide();
                           $('#transactionFeeSec').hide();
                        }
       },'json');
        return false;
    }

    function viewPromoRuleDetails(promotypeId){
        $.ajax({
            'url' : site_url('marketing_management/viewPromoRuleDetails/' + promotypeId),
            'type' : 'GET',
            'dataType' : "json",
            'success' : function(data){
                     // console.log(data[0]);
                     $('#promoName').val(data[0].promoName);
                     $('#promoDesc').val(data[0].promoDesc);
                     $('#promoCode').val(data[0].promoCode);
                     $('#promoStatus').val(data[0].promoStatus);

                     //promo type
                     if(data[0].promoType == 0){//deposit promo
                        $('.depositCondSec').show();
                        $('.allowedGameTypeSec').show();
                        $('.gameBetConditionSec').hide();
                        promoType = "<?=lang('cms.depPromo')?>";
                     }else if(data[0].promoType == 1){//non deposit promo
                        $('.depositCondSec').hide();
                        $('.depositSuccesionSec').hide();

                        nonDepPromoType = data[0].nonDepositPromoType;
                        if(nonDepPromoType == 0){
                            promoType = " <?=lang('cms.nonDepPromo') . ' (' . lang('cms.promoEmail')?>)";
                            $('.gameBetConditionSec').hide();
                            $('.allowedGameTypeSec').show();
                        }else if(nonDepPromoType == 1){
                            promoType = " <?=lang('cms.nonDepPromo') . ' (' . lang('cms.promoMobilePhone')?>)";
                            $('.gameBetConditionSec').hide();
                            $('.allowedGameTypeSec').show();
                        }else if(nonDepPromoType == 2){
                            promoType = " <?=lang('cms.nonDepPromo') . ' (' . lang('cms.promoRegisteredAcct')?>)";
                            $('.gameBetConditionSec').hide();
                            $('.allowedGameTypeSec').show();
                        }else if(nonDepPromoType == 3){
                            promoType = " <?=lang('cms.nonDepPromo') . ' (' . lang('cms.promoCompleteRegistration')?>)";
                            $('.gameBetConditionSec').hide();
                            $('.allowedGameTypeSec').show();
                        }else if(nonDepPromoType == 4){
                            promoType = " <?=lang('cms.nonDepPromo') . ' (' . lang('cms.promoByBetting')?>)";
                            $('.gameBetConditionSec').show();
                            $('.allowedGameTypeSec').hide();
                        }else if(nonDepPromoType == 5){
                            promoType = " <?=lang('cms.nonDepPromo') . ' (' . lang('cms.promoByLoss')?>)";
                            $('.gameBetConditionSec').show();
                            $('.allowedGameTypeSec').hide();
                        }else if(nonDepPromoType == 6){
                            promoType = " <?=lang('cms.nonDepPromo') . ' (' . lang('cms.promoByWinning')?>)";
                            $('.gameBetConditionSec').show();
                            $('.allowedGameTypeSec').hide();
                        }
                     }
                     $('#promoType').val(promoType);
                     $('#applicationPeriodStart').val(data[0].applicationPeriodStart);
                     $('#applicationPeriodEnd').val(data[0].applicationPeriodEnd);
                     $('#createdBy').val(data[0].createdBy);
                     $('#createdOn').val(data[0].createdOn);

                     var depositConditionType = (data[0].depositConditionType == 0 ? "<?=lang('cms.fixDepAmt')?>" : "<?=lang('cms.nonfixDepAmt')?>");

                     if(data[0].depositConditionType == 0){//fixed
                        depositCondition = "<?=lang('cms.fixDepAmt')?> = "+data[0].depositConditionDepositAmount;
                     }
                     else if(data[0].depositConditionType == 1){//nonfixed
                        if(data[0].depositConditionNonFixedDepositAmount == 0){
                            var nonfixedDepositAmtCondition = data[0].nonfixedDepositAmtCondition == 0 ? "<=" : ">=";
                            depositCondition = "<?=lang('cms.nonfixDepAmt')?> ("+ nonfixedDepositAmtCondition +" "+data[0].nonfixedDepositAmtConditionRequiredDepositAmount+")";
                        }else{
                            depositCondition = "<?=lang('cms.anyAmt')?>";
                        }

                     }
                     $('#depositConditionType').val(depositCondition);
                     $('#depositConditionDepositAmount').val(data[0].depositConditionDepositAmount);

                    //bonus succession and application
                    if(data[0].bonusApplication == 0){

                        $('.depositSuccesionSec').show();
                        $('.applicationSec').hide();
                        if(data[0].depositSuccesionType == 0){
                            depSuccessionType = "<?=lang('cms.firstDep')?>";
                        }else if(data[0].depositSuccesionType == 1){
                            depSuccessionType = "<?=lang('cms.secondDep')?>";
                        }else if(data[0].depositSuccesionType == 2){
                            depSuccessionType = "<?=lang('cms.thirdDep')?>";
                        }else if(data[0].depositSuccesionType == 3){
                            depSuccessionType = data[0].depositSuccesionCnt;
                        }
                        $('#depSuccessionType').val(depSuccessionType);
                    }else{
                        $('.depositSuccesionSec').hide();
                        $('.applicationSec').show();
                        if(data[0].bonusApplicationRule == 0){//repeat
                            if(data[0].bonusApplicationLimitRule == 0){//limit
                                singleOrMultiple = "<?=lang('cms.repeat')?>, <?=lang('cms.noLimit')?>";
                            }else{
                                singleOrMultiple = "<?=lang('cms.repeat') . ', ' . lang('cms.withLimit')?> = " +data[0].bonusApplicationLimitRuleCnt+ " <?=lang('cms.nolimit')?>";
                            }
                            $('#repeatCondition').show();
                            if(data[0].promoType == 0){
                                repeatCondition = "<?=lang('cms.repeatCondition') . ', ' . lang('cms.betAmountCondition1')?> "+data[0].repeatConditionBetCnt+" <?=lang('cms.bettingTimes')?>" ;
                            }else{
                                repeatCondition = "<?=lang('cms.repeatCondition') . ', ' . lang('cms.betAmountCondition2')?> "+data[0].repeatConditionBetCnt+" <?=lang('cms.bettingTimes')?>" ;
                            }
                            $('#repeatCondition').val(repeatCondition);
                        }else{
                            singleOrMultiple = "<?=lang('cms.noRepeat')?>";
                            $('#repeatCondition').hide();
                        }

                        $('#singleOrMultiple').val(singleOrMultiple);

                    }

                    //bonusReleaseSec
                    if(data[0].bonusReleaseRule == 0){
                        bonusReleaseRule = "<?=lang('cms.fixedBonusAmount')?> = "+data[0].bonusAmount;
                    }else{
                        bonusReleaseRule = data[0].depositPercentage+"<?=lang('cms.percentageOfDepositAmt')?> "+data[0].maxBonusAmount+" <?=lang('cms.maxbonusamt')?>.";
                    }
                    $('#bonusRelease').val(bonusReleaseRule);

                    //withdrawRequirement
                    if(data[0].withdrawRequirementRule == 0){
                        if(data[0].withdrawRequirementConditionType == 0){
                            withdrawRequirement = "<?=lang('cms.withBetAmtCond')?> >= "+data[0].withdrawRequirementBetAmount;
                        }else{
                            if(data[0].promoType == 1){
                                withdrawRequirement = "<?=lang('cms.withBetAmtCond') . ' ' . lang('cms.betAmountCondition2')?> "+data[0].withdrawRequirementBetCntCondition;
                            }else{
                                withdrawRequirement = "<?=lang('cms.withBetAmtCond') . ' ' . lang('cms.betAmountCondition1')?> "+data[0].withdrawRequirementBetCntCondition;
                            }

                        }
                    }else{
                        withdrawRequirement = "<?=lang('cms.noBetRequirement')?>";
                    }
                    $('#withdrawRequirement').val(withdrawRequirement);



                     if(data[0].noEndDateFlag ==0){
                        noEndDateFlag = "<?=lang('cms.noEndDate')?>";
                     }else{
                        noEndDateFlag = data[0].validityEndDate;
                     }

                     $('#promoCat').val(data[0].promoTypeName);
                     $('#promorulesId').val(data[0].promorulesId);
                     $('#status').val(data[0].status);
                     $('#updatedBy').val(data[0].updatedBy);
                     $('#updatedOn').val(data[0].updatedOn);
                     $('#validityStartDate').val(data[0].validityStartDate);
                     $('#validityEndDate').val(noEndDateFlag);

                     //player levels
                     $('#playerLevelItemSec').empty();
                    if(data[0]['playerLevels'].length > 0){
                            for (var i = 0; i < data[0]['playerLevels'].length; i++) {
                                    html  = '';
                                    html += '<li>';
                                    html += ''+data[0]['playerLevels'][i].groupName+' '+data[0]['playerLevels'][i].vipLevelName+'';
                                    html += '</li>';
                                  $('#playerLevelItemSec').append(html);
                              }
                       }

                     //game type
                     $('#allowedGameTypeItemSec').empty();
                     if(data[0]['gameType'].length > 0){
                            for (var i = 0; i < data[0]['gameType'].length; i++) {
                                    html  = '';
                                    html += '<li>';
                                    html += ''+data[0]['gameType'][i].game;
                                    html += '</li>';
                                  $('#allowedGameTypeItemSec').append(html);
                              }
                       }

                     //game bet condition
                     $('#gameBetConditionItemSec').empty();
                     if(data[0]['gameBetCondition'].length > 0){
                            for (var i = 0; i < data[0]['gameBetCondition'].length; i++) {
                                    html  = '';
                                    html += '<li>';
                                    html += data[0]['gameBetCondition'][i].game+' - '+data[0]['gameBetCondition'][i].gameName+' ('+data[0]['gameBetCondition'][i].gameCode+')';
                                    html += '</li>';
                                  $('#gameBetConditionItemSec').append(html);
                              }
                       }
                     //required game bet

                     $('#requiredGameBetAmount').val(data[0].gameRequiredBet);
                       //$('#gameBetRecordPeriod').val(data[0].gameRecordStartDate+' - '+data[0].gameRecordEndDate);

                 }
         });
    }
</script>

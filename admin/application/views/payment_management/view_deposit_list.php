<form action="<?=site_url('payment_management/sortDepositWithdrawalList/deposit')?>" method="post" role="form">
	<!-- start date picker range api -->
	<div class="well" style="overflow: auto;margin-bottom:10px;">

		<!-- start dashboard notification -->
		<a class='notificationRefreshList' href="<?=site_url('payment_management/refreshList/depositRequest')?>">
			<div class="col-md-2 notificationDashboard hover-shadow <?=$this->session->userdata('dwStatus') == 'request' ? 'notDboard-active' : ''?>" id="notificationDashboard-request-deposit">
				<?=lang('pay.total');?><br/><span class="notificationDashboardTxt" id="notificationDashboard-request-deposit"><?=$deposit_request_cnt?></span><br/>
				<span class="notificationDashboardLabel" id="notificationDashboard-request-deposit"><?=lang('pay.depreq');?></span>
			</div>
		</a>
		<a class='notificationRefreshList' href="<?=site_url('payment_management/refreshList/depositApproved')?>">
			<div class="col-md-2 notificationDashboard hover-shadow <?=$this->session->userdata('dwStatus') == 'approved' ? 'notDboard-active' : ''?>" id="notificationDashboard-approved-deposit">
				<?=lang('pay.total');?><br/><span class="notificationDashboardTxt" id="notificationDashboard-approved-deposit"><?=$deposit_approved_cnt?></span><br/>
				<span class="notificationDashboardLabel" id="notificationDashboard-approved-deposit"><?=lang('pay.appreq');?></span>
			</div>
		</a>
		<a class='notificationRefreshList' href="<?=site_url('payment_management/refreshList/depositDeclined')?>">
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
					<i class="icon-feed"></i> <?=$transactionType?>
					<span class="choosenDateRange">&nbsp;<?=isset($choosenDateRange) ? ($choosenDateRange) : ''?></span>
				</h4>
			</div>

			<!-- start data table -->
			<div class="panel-body" id="player_panel_body">
				<div class="table-responsive">
					<table class="table table-striped table-hover tablepress table-condensed" id="depositTable" style="width: 100%;">
						<thead>
							<tr>
								<th></th>
								<th class="tableHeaderFont"><?=lang('lang.action');?></th>
								<th class="tableHeaderFont"><?=lang('system.word38');?></th>
								<th class="tableHeaderFont"><?=lang('pay.realname');?></th>
								<th class="tableHeaderFont"><?=lang('pay.playerlev');?></th>
								<th class="tableHeaderFont"><?=lang('pay.amt');?></th>
								<th class="tableHeaderFont"><?=lang('pay.curr');?></th>
								<th class="tableHeaderFont"><?=lang('player.ui35');?></th>
								<!-- <th>Deposit Method</th> -->
								<th class="tableHeaderFont"><?=lang('pay.deptype');?></th>
								<!-- <th class="tableHeaderFont"><?=lang('con.bnk10');?></th> -->
								<th class="tableHeaderFont"><?=lang('player.ui36');?></th>
								<th class="tableHeaderFont"><?=lang('player.ui37');?></th>
								<!-- <th><?=lang('pay.branchname');?></th> -->
								<th class="tableHeaderFont"><?=lang('pay.reqtime');?></th>

								<th class="tableHeaderFont"><?=lang('pay.promoname');?></th>
								<th class="tableHeaderFont"><?=lang('pay.promobonus');?></th>
								<th class="tableHeaderFont"><?=lang('pay.deposit') . ' ' . lang('pay.ip');?></th>
								<th class="tableHeaderFont"><?=lang('pay.deposit') . ' ' . lang('pay.locatn');?></th>

								<?php if ($this->session->userdata('dwStatus') == 'approved' || $this->session->userdata('dwStatus') == 'declined') {?>
									<th class="tableHeaderFont"><?=lang('pay.procssby');?></th>
									<th class="tableHeaderFont"><?=lang('pay.procsson');?></th>
								<?php	}
?>
								<th class="tableHeaderFont"><?=lang('pay.depslip');?></th>
							</tr>
						</thead>

						<tbody>
							<?php
$atts_popup = array(
	'width' => '1030',
	'height' => '600',
	'scrollbars' => 'yes',
	'status' => 'yes',
	'resizable' => 'no',
	'screenx' => '0',
	'screeny' => '0');

if (!empty($depositRequest)) {
	foreach ($depositRequest as $depositRequest) {
		?>

										<tr>
											<td></td>
											<td>
											<?php if ($depositRequest['dwStatus'] == 'approved') {?>
												<span class="btn btn-sm btn-info review-btn" onclick="getDepositApprovedLocalBank(<?=$depositRequest['walletAccountId']?>)" data-toggle="modal" data-target="#approvedDetailsModal">
													<?=lang("lang.details");?>
												</span>
											<?php } elseif ($depositRequest['dwStatus'] == 'declined') {?>
												<span class="btn btn-sm btn-info review-btn" onclick="PaymentManagementProcess.getDepositDeclined(<?=$depositRequest['walletAccountId']?>,<?=$depositRequest['dwMethod']?>)" data-toggle="modal" data-target="#declinedDetailsModal">
													<?=lang("lang.details");?>
												</span>

											<?php } else {?>
												<span class="btn btn-sm btn-info review-btn" onclick="PaymentManagementProcess.getDepositRequest(<?=$depositRequest['walletAccountId']?>,<?=$depositRequest['dwMethod']?>)" data-toggle="modal" data-target="#requestDetailsModal">
													<?=lang("lang.details");?>
												</span>
											<?php }
		?>
											</td>
											<td><?=$depositRequest['playerName'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : ucwords($depositRequest['playerName'])?></td>
											<td><?=$depositRequest['firstName'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : ucwords($depositRequest['firstName']) . ' ' . ucwords($depositRequest['firstName'])?></td>
											<td><?=$depositRequest['groupName'] . ' ' . $depositRequest['vipLevel']?></td>
											<td><?=$depositRequest['amount'] == '' ? '<i class="help-block">' . lang("lang.norecord") . '<i/>' : $depositRequest['amount']?></td>
											<td><?=$depositRequest['currency'] == '' ? '<i class="help-block">' . lang("lang.norecord") . '<i/>' : $depositRequest['currency']?></td>
											<td><?=$depositRequest['depositedToBankName'] == '' ? '<i class="help-block">' . lang("lang.norecord") . '<i/>' : $depositRequest['depositedToBankName']?></td>
											<td><?=$depositRequest['localBankType'] == '' ? '<i class="help-block">' . lang("lang.norecord") . '<i/>' : $depositRequest['localBankType']?></td>
											<td><?=$depositRequest['depositedToAcctName'] == '' ? '<i class="help-block">' . lang("lang.norecord") . '<i/>' : $depositRequest['depositedToAcctName']?></td>
											<td><?=$depositRequest['depositedToAcctNo'] == '' ? '<i class="help-block">' . lang("lang.norecord") . '<i/>' : $depositRequest['depositedToAcctNo']?></td>
											<td>
												<?php if ($depositRequest['dwStatus'] == 'request') {?>
														<?=mdate('%M %d, %Y %H:%i:%s %A', strtotime($depositRequest['dwDateTime']))?>
												<?php } else {?>
													<?=$depositRequest['dwDateTime'] == '' ? '<i class="help-block">' . lang("lang.norecord") . '<i/>' : mdate('%M %d, %Y %H:%i:%s', strtotime($depositRequest['dwDateTime']))?>
												<?php }
		?>
											</td>

											<td><?=$depositRequest['promoName'] == '' ? '<i class="help-block">' . lang("lang.no") . " " . lang("lang.promo") . '<i/>' : $depositRequest['promoName']?></td>
											<td><?=$depositRequest['bonusAmount'] == '' ? '<i class="help-block">' . lang("lang.no") . " " . lang("lang.bonus") . '<i/>' : $depositRequest['bonusAmount']?></td>
											<td><?=$depositRequest['dwIp'] == '' ? '<i class="help-block">' . lang("lang.no") . " " . lang("lang.bonus") . '<i/>' : $depositRequest['dwIp']?></td>
											<td><?=$depositRequest['dwLocation'] == ',' ? '<i class="help-block">' . lang("lang.norecord") . '<i/>' : $depositRequest['dwLocation']?></td>
											<?php if ($depositRequest['dwStatus'] == 'approved' || $depositRequest['dwStatus'] == 'declined') {?>
														<td><?=$depositRequest['processedByAdmin'] == '' ? '<i class="help-block">' . lang("lang.norecord") . '<i/>' : ucwords($depositRequest['processedByAdmin'])?></td>
														<td><?=$depositRequest['processDatetime'] == '' ? '<i class="help-block">' . lang("lang.norecord") . '<i/>' : mdate('%M %d, %Y %H:%i:%s', strtotime($depositRequest['processDatetime']))?></td>
											<?php	}
		?>
											<td><?php if ($depositRequest['depositSlipName'] == '.') {?>
													<i class="help-block"><?=lang("lang.no") . " " . lang("pay.depslip");?><i/>
												<?php } else {?>
												<span class="btn btn-sm btn-default depositSlipBtn" onclick="PaymentManagementProcess.setDepositSlipValue('<?=IMAGEPATH_DEPOSITSLIP . $depositRequest['depositSlipName']?>')" data-toggle="modal" data-target="#depositSlipModal">
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
																	<h4 class="modal-title" id="myModalLabel"><i class="glyphicon glyphicon-list-alt"></i>&nbsp;<?=lang("pay.depslip");?></h4>
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
							<?php	}
?>
						</tbody>
					</table>
				</div>
			</div>
			<div class="panel-footer"></div>
			<!-- end data table -->
		</div>
	</div>
	<!-- end request list -->

	<!-- start requestDetailsModal-->
	<div class="row">
		<div class="modal fade" id="requestDetailsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content modal-content-three">
					<div class="modal-header">
						<a class='notificationRefreshList' href="<?=site_url('payment_management/refreshList/depositRequest')?>">
							<button type="button" class="close"><span aria-hidden="true">×</span><span class="sr-only"><?=lang("lang.close");?></span></button>
						</a>
						<h4 class="modal-title" id="myModalLabel"><i class="icon-feed"></i>&nbsp;<?=lang("pay.depreq") . ' ' . lang("lang.details");?></h4>
					</div>

					<div class="modal-body">
						<div class="col-md-12" id="checkPlayer">
							<!-- Deposit transaction -->
							<div class="row">
								<div class="col-md-12">
									<div class="panel panel-primary">
										<div class="panel-heading">
											<h4 class="panel-title">
												<?=lang("pay.deposit") . ' ' . lang("lang.info");?>
												<a href="#personal" 
              id="hide_deposit_info" class="btn btn-default btn-sm pull-right">
													<i class="glyphicon glyphicon-chevron-up" id="hide_deposit_info_up"></i>
												</a>
												<div class="clearfix"></div>
											</h4>
										</div>

										<div class="panel panel-body" id="deposit_info_panel_body" style="display: none;">
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
											<br/>
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

											<!-- start payment method -->
											<div class="col-md-12" style="margin-bottom:20px; margin-top:10px; border-bottom:1px solid #ddd;">
											<h4><?=lang('pay.paytmethdetls');?></h4>
											</div>

												<!-- start otc payment method -->
												<div class="otcPaymentMethodSection">
													<div class="row">
														<div class="col-md-12">

															<div class="col-md-3">
																<label for="otcBankName"><?=lang('pay.depbankname');?>:</label>
																<input type="text" class="form-control depotcBankName" readonly/>
																<br/>
															</div>

															<div class="col-md-3">
																<label for="otcAccountName"><?=lang('pay.depacctname');?>:</label>
																<input type="text" class="form-control depotcAccountName" readonly>
																<br/>
															</div>

															<div class="col-md-3">
																<label for="otcAccountNo"><?=lang('pay.depacctnumber');?>:</label>
																<input type="text" class="form-control depotcAccountNo" readonly>
																<br/>
															</div>

															<div class="col-md-3">
																<label for="otcAccountBranch"><?=lang('pay.depbranchname');?>:</label>
																<input type="text" class="form-control depotcBranchName" readonly>
																<br/>
															</div>


														</div>
													</div>

													<div class="row">
														<div class="col-md-12">
															<div class="col-md-3">
																<label for="otcAccountNo"><?=lang('pay.depbanktype');?>:</label>
																<input type="text" class="form-control depLocBankType" readonly>
																<br/>
															</div>

															<div class="col-md-3">
																<label for="otcTransacTime"><?=lang('pay.transdatetime');?>:</label>
																<input type="text" class="form-control otcTransacTime" readonly>
																<br/>
															</div>

															<div class="col-md-3">
																<label for="otcReferenceNo"><?=lang('pay.transrefnumber');?>:</label>
																<input type="text" class="form-control otcReferenceNo" readonly>
																<br/>
															</div>
														</div>
													</div>
												</div>
												<!-- end otc payment method -->
											<!-- end payment method -->

											<!-- start bonus info -->
											<div class="row playerBonusInfoPanel">
												<div class="col-md-12">
													<div class="col-md-12" style="margin-bottom:20px; margin-top:-10px; border-bottom:1px solid #ddd;">
													<h4><?=lang('pay.activePlayerPromo')?></h4>
													</div>
													<div class="col-md-12">
														<table class="table playerBonusTable table-striped table-hover tablepress table-condensed">
															<th><?=lang('player.up01');?></th>
															<th><?=lang('cms.promocode');?></th>
															<th><?=lang('pay.bonusamt');?></th>
															<th><?=lang('pay.dateJoined');?></th>
															<!-- <th><?=lang('cms.playerCurrentBet');?></th> -->
														</table>
													</div>
												</div>
											</div>
											<!-- end bonus info -->
											<!-- start bonus info -->
											<div class="row bonusInfoPanel">
												<div class="col-md-12">
													<div class="col-md-12" style="margin-bottom:20px; margin-top:-10px; border-bottom:1px solid #ddd;">
														<h4><?=lang('cms.promoappdetail');?></h4>
													</div>
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

											<!-- start transaction fee method -->
											<div class="row" id="transactionFeeSec">
												<div class="col-md-12" style="margin-bottom:20px; margin-top:-10px; border-bottom:1px solid #ddd;">
													<h4><?=lang('con.bnk10');?></h4>
												</div>
												<div class="col-md-12">
													<div class="col-md-3 amountReceived">
														<label for="amountReceived"><?=lang('con.bnk16');?>:</label>
														<input type="text" value="0" id="amountReceived" name="amountReceived" class="form-control number_only" required/>
														<span id="amountReceiveStatus" style="font-size:12px; color:#ff0000; font-style: italic;"></span>
														<br/>
													</div>
													<div class="col-md-3 transactionFeeSec">
														<label for="compensationFee"><?=lang('pay.compensationForPlayer');?>:</label>
														<input type="text" id="compensationFee" class="form-control number_only" required/>
														<input type="hidden" value="<?=$maxCompensationAmount[0]['value']?>" id="maxTransactionFee" class="form-control number_only" required/>
														<span id="transactionFeeStatus" style="font-size:12px; color:#ff0000; font-style: italic;"></span>
														<br/>
													</div>
													<div class="col-md-3 transactionFeeSec">
														<label for="bankDepositTransacFeeSetting"><?=lang('pay.compensationForPlayerSetting');?>:</label>
														<input type="text" value="<?=$enabledCompensation[0]['value'] == 'true' ? $compensationPercentage[0]['value'] . '% up to max = ' . $maxCompensationAmount[0]['value'] : lang('pay.nochargeToPlayer')?>" id="bankDepositTransacFeeSetting" class="form-control" readonly />
														<input type="hidden" value="<?=$enabledCompensation[0]['value']?>" id="enabledCompensationVal" >
														<input type="hidden" value="<?=$compensationPercentage[0]['value']?>" id="compensationPercentage" >
														<br/>
													</div>
														<input type="hidden" name="finalPlayerAmt" id="finalPlayerAmt" class="form-control" readonly/>
												</div>
											</div>
											<!-- end transaction fee method -->

											<div class="row">
												<hr/>
												<div class="col-md-12" id="playerRequestDetails"></div>
												<div class="col-md-12" id="playerDepositSlip"></div>
												<div class="col-md-12 pull-right" id="repondBtn">
													<hr/>
													<input type="hidden" class="form-control walletAccountIdVal" readonly/>
													<input type="hidden" class="form-control" id="requestPlayerPromoIdVal" readonly/>
													<button class="btn btn-md btn-info" id="approveBtn" onclick="respondToDepositRequest()"><?=lang('lang.approve');?></button>
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
						<a class='notificationRefreshList' href="<?=site_url('payment_management/refreshList/depositApproved')?>">
							<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only"><?=lang('lang.close');?></span></button>
						</a>
						<h4 class="modal-title" id="myModalLabel"><i class="icon-feed"></i>&nbsp;<?=lang('pay.appdepodetals');?></h4>
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

									<div class="panel panel-body" id="approved_deposit_transac_panel_body" style="display: none;">
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
												<div class="row">
													<div class="col-md-12">
														<div class="col-md-3">
															<label for="depositedAmountApprovedDeposit"><?=lang('pay.deposit') . ' ' . lang('pay.amt');?>:</label>
															<input type="text" class="form-control depositedAmountApprovedDeposit" readonly/>
														</div>

														<div class="col-md-3">
															<label for="depositMethodApprovedDeposit"><?=lang('pay.depmethod');?>:</label>
															<input type="text" class="form-control depositMethodApprovedDeposit" readonly/>
														</div>

														<div class="col-md-3">
															<label for="dateDepositedApprovedDeposit"><?=lang('lang.date') . ' ' . lang('pay.deposited');?>:</label>
															<input type="text" class="form-control dateDepositedApprovedDeposit" readonly/>
														</div>

														<div class="col-md-3">
															<label for="ipLoc"><?=lang('pay.deposit') . ' ' . lang('pay.ip') . ' ' . lang('lang.and') . ' ' . lang('pay.locatn')?></label>
															<input type="text" class="form-control ipLoc" readonly/>
														</div>
													</div>
												</div>

												<!-- start bonus info -->
												<div class="row bonusInfoPanel">
													<div class="col-md-12">
														<div class="col-md-12" style="margin-bottom:20px;margin-top:20px; border-bottom:1px solid #ddd;">
														<h4><?=lang('pay.bonapplictn');?></h4>
														</div>
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

											<!-- start payment method -->
											<div class="col-md-12" style="margin-bottom:20px; border-bottom:1px solid #ddd;">
											<h4><?=lang('pay.paytmethdetls');?></h4>
											</div>

											<!-- start otc payment method -->
											<div class="otcPaymentMethodSection">
												<div class="row">
													<div class="col-md-12">

														<div class="col-md-3">
															<label for="otcBankName"><?=lang('pay.depbankname');?>:</label>
															<input type="text" class="form-control depotcBankName" readonly/>
															<br/>
														</div>

														<div class="col-md-3">
															<label for="otcAccountName"><?=lang('pay.depacctname');?>:</label>
															<input type="text" class="form-control depotcAccountName" readonly>
															<br/>
														</div>

														<div class="col-md-3">
															<label for="otcAccountNo"><?=lang('pay.depacctnumber');?>:</label>
															<input type="text" class="form-control depotcAccountNo" readonly>
															<br/>
														</div>

														<div class="col-md-3">
															<label for="otcAccountBranch"><?=lang('pay.depbranchname');?>:</label>
															<input type="text" class="form-control depotcBranchName" readonly>
															<br/>
														</div>
													</div>
												</div>

												<div class="row">
													<div class="col-md-12">
														<div class="col-md-3">
															<label for="otcAccountNo"><?=lang('pay.depbanktype');?>:</label>
															<input type="text" class="form-control depLocBankType" readonly>
															<br/>
														</div>

														<div class="col-md-3">
															<label for="otcTransacTime"><?=lang('pay.transdatetime');?>:</label>
															<input type="text" class="form-control otcTransacTime" readonly>
															<br/>
														</div>

														<div class="col-md-3">
															<label for="otcReferenceNo"><?=lang('pay.transrefnumber');?>:</label>
															<input type="text" class="form-control otcReferenceNo" readonly>
															<br/>
														</div>
													</div>
												</div>
											</div>
											<!-- end otc payment method -->
											<!-- end payment method -->


											<div class="row" id="approvedTransactionFeeSec">
												<!-- start transaction fee method -->
												<div class="col-md-12">
													<div class="col-md-12" style="margin-bottom:20px; margin-top:-10px; border-bottom:1px solid #ddd;">
														<h4><?=lang('con.bnk12');?></h4>
													</div>
													<div class="col-md-12">
														<div class="col-md-3">
															<label for="transactionFee"><?=lang('con.bnk10');?>:</label>
															<input type="text" id="approvedTransactionFee" class="form-control" readonly />
															<br/>
														</div>
														<!-- <div class="col-md-3">
															<label for="bankDepositTransacFeeSetting"><?=lang('pay.bankDepositTransacFeeSetting');?>:</label>
															<input type="text" value="" id="approvedBankDepositTransacFeeSetting" class="form-control" readonly />
															<br/>
														</div> -->
													</div>
												</div>
											</div>
											<!-- end transaction fee method -->

												<hr/>
												<div class="row">
													<div class="col-md-12">

														<div class="col-md-1">
															<label for="depositMethodApprovedBy"><?=lang('pay.apprvby');?>:</label>
														</div>

														<div class="col-md-2">
															<input type="text" class="form-control" id="depositMethodApprovedBy" readonly>
															<br/>
														</div>

														<div class="col-md-1">
															<label for="depositMethodDateApproved"><?=lang('pay.datetimeapprv');?>:</label>
														</div>

														<div class="col-md-2">
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
						<a class='notificationRefreshList' href="<?=site_url('payment_management/refreshList/depositDeclined')?>">
							<button type="button" class="close"><span aria-hidden="true">×</span><span class="sr-only"><?=lang('lang.close');?></span></button>
						</a>
						<h4 class="modal-title" id="myModalLabel"><i class="icon-feed"></i>&nbsp;<?=lang('pay.decldepdetls');?></h4>
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
              id="hide_declined_deposit_info" class="btn btn-default btn-sm pull-right">
												<i class="glyphicon glyphicon-chevron-up" id="hide_declined_deposit_info_up"></i>
											</a>
											<div class="clearfix"></div>
										</h4>
									</div>

									<div class="panel panel-body" id="declined_deposit_info_panel_body" style="display: none;">
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

												<!-- start bonus info -->
												<div class="row bonusInfoPanel">
													<div class="col-md-12">
														<hr/>
														<h4><?=lang('pay.bonapplictn');?></h4>
														<hr/>
														<div class="col-md-3">
															<label for="promoName"><?=lang('lang.promo') . ' ' . lang('pay.name');?>:</label>
															<input type="text" class="form-control promoName" readonly/>
															<input type="hidden" class="form-control playerDepositPromoId" readonly/>
															<br/>
														</div>

														<div class="col-md-3">
															<label for="playerPromoBonusAmount"><?=lang('pay.compbonamt');?>:</label>
															<input type="text" class="form-control declinedPlayerPromoBonusAmount" id="declinedPlayerPromoBonusAmount" readonly/>
															<br/>
														</div>
													</div>
												</div>
												<!-- end bonus info -->
										</div>

										<!-- start payment method -->
											<hr/>
											<h4><?=lang('pay.paytmethdetls');?></h4>
											<hr/>

												<!-- start otc payment method -->
												<div class="otcPaymentMethodSection">
													<div class="row">
														<div class="col-md-12">

															<div class="col-md-3">
																<label for="otcBankName"><?=lang('pay.depbankname');?>:</label>
																<input type="text" class="form-control depotcBankName" readonly/>
																<br/>
															</div>

															<div class="col-md-3">
																<label for="otcAccountName"><?=lang('pay.depacctname');?>:</label>
																<input type="text" class="form-control depotcAccountName" readonly>
																<br/>
															</div>

															<div class="col-md-3">
																<label for="otcAccountNo"><?=lang('pay.depacctnumber');?>:</label>
																<input type="text" class="form-control depotcAccountNo" readonly>
																<br/>
															</div>

															<div class="col-md-3">
																<label for="otcAccountBranch"><?=lang('pay.depbranchname');?>:</label>
																<input type="text" class="form-control depotcBranchName" readonly>
																<br/>
															</div>
														</div>
													</div>

													<div class="row">
														<div class="col-md-12">
															<div class="col-md-3">
																<label for="otcAccountNo"><?=lang('pay.depbanktype');?>:</label>
																<input type="text" class="form-control depLocBankType" readonly>
																<br/>
															</div>
															<div class="col-md-3">
																<label for="otcTransacTime"><?=lang('pay.transdatetime');?>:</label>
																<input type="text" class="form-control otcTransacTime" readonly>
																<br/>
															</div>

															<div class="col-md-3">
																<label for="otcReferenceNo"><?=lang('pay.transrefnumber');?>:</label>
																<input type="text" class="form-control otcReferenceNo" readonly>
																<br/>
															</div>
														</div>
													</div>
												</div>
												<!-- end otc payment method -->
											<!-- end payment method -->


										<div class="row">
											<hr/>
											<div class="col-md-12">

												<div class="col-md-1">
													<label for="depositMethodDeclinedBy"><?=lang('pay.declby');?>:</label>
												</div>

												<div class="col-md-2">
													<input type="text" class="form-control" id="depositMethodDeclinedBy" readonly>
													<br/>
												</div>

												<div class="col-md-1">
													<label for="depositMethodDateDeclined"><?=lang('pay.datetimedecl');?>:</label>
												</div>

												<div class="col-md-3">
													<input type="text" class="form-control" id="depositMethodDateDeclined" readonly>
													<br/>
												</div>
												<div class="clearfix"></div>

													<div class="col-md-1">
														<label for="depositMethodReasonDeclined"><?=lang('pay.reason');?>:</label>
													</div>

													<div class="col-md-6">
														<textarea class="form-control" id="depositMethodReasonDeclined" readonly></textarea>
														<br/>
													</div>

											</div>
										</div>

											<div id="playerDeclinedDetails"></div>
											<div class="col-md-12" id="playerDeclinedDetailsRepondBtn">
												<input type="hidden" class="form-control walletAccountIdVal" readonly/>
												<input type="hidden" class="form-control" id="declinedPlayerPromoIdVal" readonly/>
												<!-- <button class="btn-md btn-info" onclick="PaymentManagementProcess.depositDeclinedToApprove(<?=$depositRequest['walletAccountId']?>)"><?=lang('pay.changetoapprv');?></button>							 -->
											</div>

									</div>

									<div class="clearfix"></div>
									</div>
								</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end declinedDetailsModal-->
</div>

<script type="text/javascript">
    $(document).ready(function(){
    	var requestTimeCol=11;
        $('#depositTable').DataTable({
	        // "language": {
	        //     "url": "<?php echo $this->utils->jsUrl('lang/chinese.lang');?>"
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


    $("#amountReceived").keyup(function(){
    	var depositedAmount = Number($(".depositedAmount").val());

    	if($("#amountReceived").val() == ''){
    		$("#amountReceived").val('0');
    	}
    	if($("#amountReceived").val() > depositedAmount){
    		$("#amountReceived").val($(".depositedAmount").val());
    	}
    	var compensationFee = $("#amountReceived").val() * ($('#compensationPercentage').val()/100);
    	$("#compensationFee").val(compensationFee);
    });



    //check transaction fee
    if($("#enabledCompensationVal").val() != 'false'){
    	$(".transactionFeeSec").show();

	    $("#compensationFee").keyup(function(){
	        $("#finalPlayerAmt").val($(".depositedAmount").val()-$(this).val());

        	//check if transaction fee is 0 or null //|| $("#transactionFee").val() == ''
	        if($("#compensationFee").val() > <?=$maxCompensationAmount[0]['value']?>){
	        	$("#approveBtn").attr("disabled", true);
	        	//$("#finalPlayerAmtStatus").text("<?=lang('pay.finalAmtPlayerReceiveStatus')?>");
	        	$("#transactionFeeStatus").text("<?=lang('con.bnk18')?>");
	        }else{
	        	$("#approveBtn").attr("disabled", false);
	        	//$("#finalPlayerAmtStatus").text("");
	        	$("#transactionFeeStatus").text("");
	        }

	   //      if($("#compensationFee").val() == ''){
				// $(this).val('0');
	   //      }
	    });
	 }else{
	 	$(".transactionFeeSec").hide();
	 	$("#compensationFee").keyup(function(){
 			if(Number($(this).val()) > Number($(".depositedAmount").val())){
 				$("#transactionFeeStatus").text("<?=lang('con.bnk13')?>");
 				$("#approveBtn").attr("disabled", true);
 			}else{
 				$("#transactionFeeStatus").text("");
 				$("#approveBtn").attr("disabled", false);
 			}
	 	});
	 }

    function getDepositApprovedLocalBank(walletAccountId){
    	html  = '';
        html += '<p>';
        html += 'Loading Data...';
        html += '</p>';

        $('#playerApprovedDetails').html(html);

        $.ajax({
            'url' : base_url +'payment_management/reviewDepositApprovedLocalBank/'+walletAccountId,
            'type' : 'GET',
            'dataType' : "json",
            'success' : function(data){
                           html  = '';

                           //bonus details
                           if(data[0].promoName != null){
                              $('.bonusInfoPanel').show();
                           }else{
                              $('.bonusInfoPanel').hide();
                           }

                           //clear previous transaction history
                           //$('.transacHistoryDetail').remove();

                           $('#playerApprovedDetails').html(html);
                           $('#playerApprovedDetailsRepondBtn').hide();
                           $('#playerApprovedDetailsCheckPlayer').hide();

                           //personal info
                           $('.playerId').val(data[0].playerId);
                           $('.userName').val(data[0].playerName);
                           $('.playerName').val(data[0].firstName+' '+data[0].lastName);
                           $('.playerLevel').val(data[0].groupName+' '+data[0].vipLevel);
                           $('.email').val(data[0].email);
                           $('.memberSince').val(data[0].createdOn);
                           $('.address').val(data[0].address);
                           $('.city').val(data[0].city);
                           $('.country').val(data[0].country);
                           $('.birthday').val(data[0].birthdate);
                           $('.gender').val(data[0].gender);
                           $('.phone').val(data[0].phone);
                           $('.cp').val(data[0].contactNumber);
                           $('.walletAccountIdVal').val(data[0].walletAccountId);
                           $('.ipLoc').val(data[0].dwIp+' - '+data[0].dwLocation);

                           //depositDetails
                           $('.dateDepositedApprovedDeposit').val(data[0].dwDateTime);
                           $('.playerLevelApprovedDeposit').val(data[0].playerLevel);
                           $('.depositMethodApprovedDeposit').val(data[0].paymentMethodName);
                           $('.depositedAmountApprovedDeposit').val(data[0].amount);
                           $('#depositMethodApprovedBy').val(data[0].processedByAdmin);
                           $('#depositMethodDateApproved').val(data[0].processDatetime);
                           $('.currentBalCurrency').val(data[0].currentBalCurrency);

                           $('.promoName').val(data[0].promoName);
                           $('#approvedPlayerPromoBonusAmount').val(data[0].bonusAmount);



                           //payment method details
                           paymentMethodId = data[0].paymentMethodId;
                           if(paymentMethodId == 1){
                              $('.otcPaymentMethodSection').show();
                              $('.paypalPaymentMethodSection').hide();

                              $('.otcBankName').val(data[0].bankName);
                              $('.otcAccountName').val(data[0].bankAccountFullName);
                              $('.otcAccountNo').val(data[0].bankAccountNumber);
                              $('.otcTransacTime').val(data[0].dwDateTime);
                              $('.otcReferenceNo').val(data[0].transacRefCode);

                              $('.depotcBankName').val(data[0].depositedToBankName);
                              $('.depotcAccountNo').val(data[0].depositedToAcctNo);
                              $('.depotcBranchName').val(data[0].depositedToBranchName);
                              $('.depotcAccountName').val(data[0].depositedToAcctName);
                              $('.depLocBankType').val(data[0].localBankType);
                           }
                           //transaction fee details
                           if(data[0]['compensationfeedetails']){
                           	   $('#approvedTransactionFeeSec').show();
                           	   var compensationFee = data[0]['compensationfeedetails'][0].compensationFee;
                           	   $('#approvedTransactionFee').val(compensationFee);
	                           $('#approvedFinalPlayerAmt').val(data[0].amount-compensationFee);

	                           // if(data[0]['compensationfeedetails'][0].chargeTo == 0){

	                           // 	 approvedBankDepositTransacFeeSettingVal = "<?=lang('pay.chargeToPlayer')?>";
	                           // }else{

	                           // 	 approvedBankDepositTransacFeeSettingVal = "<?=lang('pay.nochargeToPlayer')?>";
	                           // }
	                           // $('#approvedBankDepositTransacFeeSetting').val(approvedBankDepositTransacFeeSettingVal);
                           }else{
                           	   $('#approvedTransactionFeeSec').hide();
                           }
                        }
       },'json');
        return false;
    }

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
       var enabledCompensationVal = $('#enabledCompensationVal').val();
       var compensationFeeVal = $('#compensationFee').val();
       var actualAmountReceived = $('#amountReceived').val();

       if (playerDepositPromoId == '') {
            playerDepositPromoId = null;
       }

       // alert("playerId: " + playerId + " walletAccountIdVal: " + walletAccountIdVal + " depositAmount: " + depositAmount + " playerDepositPromoId: " + playerDepositPromoId);
       // console.log('enabledCompensationVal: '+enabledCompensationVal,compensationFeeVal);
       // console.log('respondToDepositRequest: '+walletAccountIdVal+'-'+actualAmountReceived+'-'+playerId+'-'+playerDepositPromoId+'-'+enabledCompensationVal+'-'+compensationFeeVal);

       $.ajax({
            'url' : base_url +'payment_management/approveDepositRequest/'+walletAccountIdVal+'/'+actualAmountReceived+'/'+playerId+'/'+playerDepositPromoId+'/'+enabledCompensationVal+'/'+compensationFeeVal,
            'type' : 'GET',
            'success' : function(data){
                            html  = "";
                            html += "<p>";
                            html += "<?=lang('con.bnk17')?>";
                            html += "</p>";

                           $('#playerRequestDetails').html(html);
                           $('#repondBtn').hide();
                           $('#transactionFeeSec').hide();
                        }
       },'json');
        return false;
    }
</script>

<form action="<?=site_url('payment_management/sortDepositWithdrawalList/withdrawal')?>" method="post" role="form">
	<!-- start date picker range api -->
	<div class="well" style="overflow: auto;margin-bottom:10px;">

		<!-- start dashboard notification -->
		<a class='notificationRefreshList' href="<?=site_url('payment_management/refreshList/withdrawalRequest')?>">
			<div class="col-md-2 notificationDashboard hover-shadow <?=$this->session->userdata('dwStatus') == 'request' ? 'notDboard-active' : ''?>" id="notificationDashboard-request-deposit">
				<?=lang('pay.total');?><br/><span class="notificationDashboardTxt" id="notificationDashboard-request-deposit"><?=$withdrawal_request_cnt?></span><br/>
				<span class="notificationDashboardLabel" id="notificationDashboard-request-deposit"><?=lang('pay.withreqst');?></span>
			</div>
		</a>
		<a class='notificationRefreshList' href="<?=site_url('payment_management/refreshList/withdrawalApproved')?>">
			<div class="col-md-2 notificationDashboard hover-shadow <?=$this->session->userdata('dwStatus') == 'approved' ? 'notDboard-active' : ''?>" id="notificationDashboard-approved-deposit">
				<?=lang('pay.total');?><br/><span class="notificationDashboardTxt" id="notificationDashboard-approved-deposit"><?=$withdrawal_approved_cnt?></span><br/>
				<span class="notificationDashboardLabel" id="notificationDashboard-approved-deposit"><?=lang('pay.appreq');?></span>
			</div>
		</a>
		<a class='notificationRefreshList' href="<?=site_url('payment_management/refreshList/withdrawalDeclined')?>">
			<div class="col-md-2 notificationDashboard hover-shadow <?=$this->session->userdata('dwStatus') == 'declined' ? 'notDboard-active' : ''?>" id="notificationDashboard-declined-deposit">
				<?=lang('pay.total');?><br/><span class="notificationDashboardTxt" id="notificationDashboard-declined-deposit"><?=$withdrawal_declined_cnt?></span><br/>
				<span class="notificationDashboardLabel" id="notificationDashboard-declined-deposit"><?=lang('pay.decreq');?></span>
			</div>
		</a>
		<!-- end dashboard notification -->

		<!-- start sort dw list -->

			<div class="pull-right">
				<h4><?=lang('pay.transperd');?></h4>
			   <div class="pull-left">
					<input id="reportrange" class="form-control input-sm dateInput inline" data-time="true" data-start="#dateRangeValueStart" data-end="#dateRangeValueEnd"/>
					<input type="hidden" id="dateRangeValueStart" name="dateRangeValueStart" value="<?=$this->session->userdata('dateRangeValueStart')?>" />
					<input type="hidden" id="dateRangeValueEnd" name="dateRangeValueEnd" value="<?=$this->session->userdata('dateRangeValueEnd')?>" />
					<input type="hidden" id="dwStatus" name="dwStatus" value="<?=$this->session->userdata('dwStatus') == '' ? 'request' : $this->session->userdata('dwStatus')?>" />
	            </div>
	            <div class="col-md-1">
		            <input type="submit" class="btn btn-sm btn-primary" value="<?=lang('lang.submit');?>" />
	           	</div>
           </div>
	   <!-- end sort dw list -->

    </div>

</form>

<div class="row">
	<div class="col-md-12" id="toggleView">
		<div class="col-md-5"></div>
		<div class="panel panel-primary">

			<!-- start request list -->
			<div class="panel-heading">
				<h4 class="panel-title custom-pt">
					<i class="icon-drawer"></i> <?=$transactionType?>
					<span class="choosenDateRange">&nbsp;<?=isset($choosenDateRange) ? ($choosenDateRange) : ''?></span>
				</h4>
			</div>
			<!-- end request list -->

			<!-- start date table list -->
			<div class="panel-body" id="player_panel_body">
				<div id="paymentList" class="table-responsive">
					<table class="table table-striped table-hover tablepress table-condensed" id="myTable" style="margin: 0px 0 0 0; width: 100%;">
						<thead>
							<tr>
								<th></th>
								<th class="tableHeaderFont"><?=lang('lang.action');?></th>
								<th class="tableHeaderFont"><?=lang("pay.username");?></th>
								<th class="tableHeaderFont"><?=lang("pay.realname");?></th>
								<th class="tableHeaderFont"><?=lang('pay.playerlev');?></th>
								<th class="tableHeaderFont"><?=lang('pay.withamt');?></th>
								<th class="tableHeaderFont"><?=lang('lang.status');?></th>
								<th class="tableHeaderFont"><?=lang('pay.reqon');?></th>
								<th class="tableHeaderFont"><?=lang('pay.bankname');?></th>
								<th class="tableHeaderFont"><?=lang('pay.acctname');?></th>
								<th class="tableHeaderFont"><?=lang('pay.acctnumber');?></th>
								<th class="tableHeaderFont"><?=lang('pay.acctbranch');?></th>
								<th class="tableHeaderFont"><?=lang('pay.withip');?></th>
								<th class="tableHeaderFont"><?=lang('pay.withlocation');?></th>
								<?php if ($this->session->userdata('dwStatus') == 'approved' || $this->session->userdata('dwStatus') == 'declined') {?>
									<th class="tableHeaderFont"><?=lang('pay.procssby');?></th>
									<th class="tableHeaderFont"><?=lang('pay.procsson');?></th>
								<?php	}
?>
							</tr>
						</thead>

						<tbody>
							<?php
if (!empty($withdrawalRequest)) {
	foreach ($withdrawalRequest as $withdrawalRequest) {
		?>
											<tr>
												<td></td>
												<td>
												<?php if ($withdrawalRequest['dwStatus'] == Wallet_model::APPROVED_STATUS) {?>
													<span class="btn btn-xs btn-info review-btn" onclick="PaymentManagementProcess.getWithdrawalApproved(<?=$withdrawalRequest['walletAccountId']?>)" data-toggle="modal" data-target="#approvedDetailsModal">
														<?=lang("lang.details");?>
													</span>
												<?php } elseif ($withdrawalRequest['dwStatus'] == Wallet_model::DECLINED_STATUS) {?>
													<span class="btn btn-xs btn-info review-btn" onclick="PaymentManagementProcess.getWithdrawalDeclined(<?=$withdrawalRequest['walletAccountId']?>)" data-toggle="modal" data-target="#declinedDetailsModal">
														<?=lang("lang.details");?>
													</span>
												<?php } else {?>
													<span class="btn btn-xs btn-info review-btn" onclick="getWithdrawalRequest(<?=$withdrawalRequest['walletAccountId']?>,<?=$withdrawalRequest['playerId']?>)" data-toggle="modal" data-target="#requestDetailsModal">
														<?=lang("lang.details");?>
													</span>
												<?php }
		?>
												</td>
												<td><?=$withdrawalRequest['username'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $withdrawalRequest['username']?></td>
												<td><?=$withdrawalRequest['firstname'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : ucwords($withdrawalRequest['firstname']) . ' ' . ucwords($withdrawalRequest['lastname'])?></td>
												<td><?=$withdrawalRequest['groupName'] . ' ' . $withdrawalRequest['vipLevel']?></td>
												<td><?=$withdrawalRequest['amount'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $withdrawalRequest['amount']?></td>
												<td>
													<?php if ($dwStatus == Wallet_model::REQUEST_STATUS) {?>
														<?=$withdrawalRequest['is_checking'] == 'true' ? lang("sale_orders.status.9") : lang("sale_orders.status.3")?></td>
													<?php } elseif ($dwStatus == Wallet_model::APPROVED_STATUS) {
			echo lang('transaction.status.1');
		} elseif ($dwStatus == Wallet_model::DECLINED_STATUS) {
			echo lang('transaction.status.2');
		}
		?>

												<td>
												<?php if ($dwStatus == Wallet_model::REQUEST_STATUS) {?>
														<?=$withdrawalRequest['dwDateTime'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : mdate('%Y-%m-%d %H:%i:%s', strtotime($withdrawalRequest['dwDateTime']))?>
												<?php } elseif ($dwStatus == Wallet_model::APPROVED_STATUS) {?>
														<?=$withdrawalRequest['dwDateTime'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : mdate('%Y-%m-%d %H:%i:%s', strtotime($withdrawalRequest['dwDateTime']))?>
												<?php } else {?>
														<?=$withdrawalRequest['dwDateTime'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : mdate('%Y-%m-%d %H:%i:%s', strtotime($withdrawalRequest['dwDateTime']))?>
												<?php }
		?>
												</td>
												<td><?=$withdrawalRequest['bankName'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : lang($withdrawalRequest['bankName'])?></td>
												<td><?=$withdrawalRequest['bankAccountFullName'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $withdrawalRequest['bankAccountFullName']?></td>
												<td><?=$withdrawalRequest['bankAccountNumber'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $withdrawalRequest['bankAccountNumber']?></td>
												<td><?=$withdrawalRequest['branch'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : $withdrawalRequest['branch']?></td>
												<td><?=$withdrawalRequest['dwIp'] == ',' ? '<i class="help-block">' . lang("lang.no") . " " . lang("lang.bonus") . '<i/>' : $withdrawalRequest['dwIp']?></td>
												<td><?=$withdrawalRequest['dwLocation'] == '' ? '<i class="help-block">' . lang("lang.no") . " " . lang("lang.bonus") . '<i/>' : $withdrawalRequest['dwLocation']?></td>
												<?php if ($withdrawalRequest['dwStatus'] == 'approved' || $withdrawalRequest['dwStatus'] == 'declined') {?>
															<td><?=$withdrawalRequest['processedBy'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : ucwords($withdrawalRequest['processedByAdmin'])?></td>
															<td><?=$withdrawalRequest['processDatetime'] == '' ? '<i class="help-block">' . lang("lang.norecyet") . '<i/>' : mdate('%M %d, %Y %H:%i:%s', strtotime($withdrawalRequest['processDatetime']))?></td>
												<?php	}
		?>
											</tr>
							<?php
}
}
?>
						</tbody>
					</table>
				</div>
			</div>
			<!-- end date table list -->
			<div class="panel-footer"></div>
		</div>
	</div>

	<!-- start requestDetailsModal-->
	<div class="row">
		<div class="modal fade" id="requestDetailsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content modal-content-three">
					<div class="modal-header">
						<a class='notificationRefreshList' href="<?=site_url('payment_management/refreshList/withdrawalRequest')?>">
							<button type="button" class="close"><span aria-hidden="true">×</span><span class="sr-only"><?=lang("lang.close");?></span></button>
						</a>
						<h4 class="modal-title" id="myModalLabel"><i class="icon-drawer"></i>&nbsp;<?=lang("pay.withreqst") . ' ' . lang("lang.details");?></h4>
					</div>
					<div class="modal-body">
						<div class="col-md-12" id="checkPlayer">
								<!-- Withdrawal transaction -->
								<div class="row">
									<div class="col-md-12">
										<div class="panel panel-primary">
											<div class="panel-heading">
												<h4 class="panel-title">
													<?=lang('pay.withinfo');?>
													<a href="#personal"
              id="hide_deposit_info" class="btn btn-default btn-sm pull-right">
														<i class="glyphicon glyphicon-chevron-up" id="hide_deposit_info_up"></i>
													</a>
													<div class="clearfix"></div>
												</h4>
											</div>

											<div class="panel-body" id="deposit_info_panel_body" style="display: none;">
												<div class="row">
													<div class="col-md-12">
														<div class="col-md-12">
															<form>
															  <fieldset>
															  <legend><?=lang('player.ui04')?></legend>
																	<table class='table'>
																		<tr>
																			<td style='border-top:0px; text-align:left;'>
																				<label for="userName"><?=lang("pay.username");?>:</label>
																				<br/>
																				<input type="hidden" class="form-control playerId" readonly/>
																				<input type="text" class="form-control userName" readonly/>
																			</td>
																			<td style='border-top:0px; text-align:left;'>
																				<label for="playerName"><?=lang("pay.realname");?>:</label>
																				<br/>
																				<input type="text" class="form-control playerName" readonly/>
																			</td>
																			<td style='border-top:0px; text-align:left;'>
																				<label for="playerLevel"><?=lang('pay.playerlev');?>:</label>
																				<input type="text" class="form-control playerLevel" readonly/>
																			</td>
																			<td style='border-top:0px; text-align:left;'>
																				<label for="memberSince"><?=lang('pay.memsince');?>: </label>
																				<br/>
																				<input type="text" class="form-control memberSince" readonly>
																			</td>
																		</tr>
																	</table>
															  </fieldset>
															</form>
														</div>
														<div class="col-md-12">
															<form>
															  <fieldset>
															  <legend><?=lang('pay.walletInfo')?></legend>
																<table class='table'>
																	<tr>
																		<td style='border-top:0px; text-align:left;'>
																			<label for="mainWalletBalance"><?=lang('pay.mainwalltbal');?>:</label>
																			<br/>
																			<input type="text" class="form-control mainWalletBalance" readonly/>
																		</td>
																		<?php foreach ($game_platforms as $game_platform): ?>
																			<td style='border-top:0px; text-align:left;'>
																				<label for="subWalletBalance<?=$game_platform['id']?>"><?=$game_platform['system_code']?>:</label>
																				<br/>
																				<input type="text" class="form-control subWalletBalance subWalletBalance<?=$game_platform['id']?>" readonly/>
																			</td>
																		<?php endforeach?>
																		<td style='border-top:0px; text-align:left;'>
																			<label for="totalBalance"><?=lang('pay.totalbal');?>:</label>
																			<br/>
																			<input type="text" class="form-control totalBalance" readonly/>
																		</td>
																	</tr>
																</table>
															   </fieldset>
															</form>
														</div>
													</div>
												</div>

												<input type="hidden" class="currentLang" value="<?=$this->language_function->getCurrentLanguage();?>">
												<div class="col-md-12">
													<div class="row">
														<div class="col-md-12">
															<form>
																<fieldset>
																 	<legend><?=lang('pay.withdetl')?></legend>
																		<div class="paymentMethodSection">
																			<div class="row">
																				<div class="col-md-12">
																					<table class='table'>
																						<tr>
																							<td style='border-top:0px; text-align:left;'>
																								<label for="withdrawalAmount"><?=lang('pay.withamt');?>:</label>
																								<input type="text" class="form-control withdrawalAmount" readonly/>
																							</td>
																							<td style='border-top:0px; text-align:left;'>
																								<label for="currency"><?=lang('pay.curr');?>:</label>
																								<input type="text" class="form-control currency" readonly/>
																							</td>
																							<td style='border-top:0px; text-align:left;'>
																								<label for="dateDeposited"><?=lang('pay.reqtdon');?>:</label>
																								<input type="text" class="form-control dateDeposited" readonly/>
																							</td>
																							<td style='border-top:0px; text-align:left;'>
																								<label for="ipLoc"><?=lang('pay.withip') . ' ' . lang('lang.and') . ' ' . lang('pay.locatn');?>:</label>
																								<input type="text" class="form-control ipLoc" readonly/>
																							</td>
																						</tr>
																						<tr>
																							<td style='border-top:0px; text-align:left;'>
																								<label for="bankName"><?=lang('pay.bankname');?>:</label>
																								<input type="text" class="form-control bankName" readonly/>
																							</td>
																							<td style='border-top:0px; text-align:left;'>
																								<label for="bankAccountName"><?=lang('pay.acctname');?>:</label>
																								<input type="text" class="form-control bankAccountName" readonly/>
																							</td>
																							<td style='border-top:0px; text-align:left;'>
																								<label for="bankAccountNumber"><?=lang('pay.bank') . ' ' . lang('pay.acctnumber');?>:</label>
																								<input type="text" class="form-control bankAccountNumber" readonly/>
																							</td>
																							<td style='border-top:0px; text-align:left;'>
																								<label for="bankAccountBranch"><?=lang('pay.bank') . ' ' . lang('pay.acctbranch');?>:</label>
																								<input type="text" class="form-control bankAccountBranch" readonly/>
																							</td>
																						</tr>
																					</table>
																				</div>
																			</div>
																		</div>
																</fieldset>
															</form>
														</div>
													</div>
												</div>

												<!-- start bonus info -->
												<div class="col-md-12">
													<div class="row playerBonusInfoPanel">
														<div class="col-md-12">
															<form>
																  <fieldset>
																  <legend><?=lang('pay.withdrawalCondition')?></legend>
																	<div class="col-md-12 ">
																		<table class="table playerBonusTable table-striped table-hover tablepress table-condensed">
																			<th><?=lang('pay.transactionType');?></th>
																			<th><?=lang('pay.promoName');?></th>
																			<!-- <th><?=lang('cms.promocode');?></th> -->
																			<th><?=lang('cms.promocondition');?></th>
																			<th><?=lang('cashier.53');?></th>
																			<th><?=lang('pay.startedAt');?></th>
																			<th><?=lang('pay.withdrawalAmountCondition');?></th>
																			<th><?=lang('mark.bet');?></th>
																		</table>
																	</div>
																   </fieldset>
															</form>
														</div>
													</div>
												</div>
												<!-- end bonus info -->

												<!-- duplicate account info -->
												<div class="col-md-12">
													<div class="row playerDuplicateAccountInfoPanel">

													</div>
												</div>
												<!-- end duplicate account info -->

												<div class="clearfix"></div>
												<div class="row">
													<hr/>
													<input type="hidden" class="form-control request_walletAccountIdVal" readonly />
													<div class="col-md-12 transactionStatusMsg"></div>
													<div class="col-md-12" id="repondBtn">

														<input type="hidden" class="form-control" id="requestPlayerPromoIdVal" readonly/>
														<button class="btn btn-md btn-success" id="checking_btn" onclick="checkingRequest()"><?=lang('payment.checking');?></button>
														<button class="btn btn-md btn-primary" onclick="respondToWithdrawalRequest()"><?=lang('lang.approve');?></button>
														<button class="btn btn-md btn-danger" onclick="PaymentManagementProcess.showDeclineReason()"><?=lang('lang.decline');?></button>
													</div>
													<div class="col-md-5" id="declineReason-sec">
														<p><?=lang('pay.plsadddeclreason');?>:</p>
														<textarea cols="50" rows="5" id="declinedReasonTxt" class="form-control"></textarea><br/>
														<input type="checkbox" name="showDeclinedReason_cbx" id="showDeclinedReason_cbx"> <?=lang('pay.showtoplayr');?><br/><br/>
														<button class="btn-md btn-info" onclick="respondToWithdrawalDeclined()"><?=lang('pay.declnow');?></button>
													</div>
												</div>
												<div class="clearfix"></div>
											</div>
										</div>
									</div>
								</div>
								<!--end of Withdrawal transaction-->
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end requestDetailsModal-->

	<!-- start approvedDetailsModal -->
	<div class="row">
		<div class="modal fade" id="approvedDetailsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content modal-content-three">
					<div class="modal-header">
						<a class='notificationRefreshList' href="<?=site_url('payment_management/refreshList/depositRequest')?>">
							<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only"><?=lang("lang.close");?></span></button>
						</a>
						<h4 class="modal-title" id="myModalLabel"><i class="icon-drawer"></i>&nbsp;<?=lang("pay.appwithdetl");?></h4>
					</div>

					<div class="modal-body">
						<!-- player transaction -->
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-primary">
									<div class="panel-heading">
										<h4 class="panel-title">
											<?=lang("pay.appwithinfo");?>
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
														<div class="row">
															<div class="col-md-12">
																<div class="col-md-3">
																	<label for="userName"><?=lang("pay.username");?>:</label>
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
														<div class="row">
															<div class="col-md-12">
																<br/>
																<div class="col-md-3">
																	<label for="mainWalletBalance"><?=lang('pay.mainwalltbal');?>:</label>
																	<input type="text" class="form-control mainWalletBalance" readonly/>
																</div>

																<?php foreach ($game_platforms as $game_platform): ?>
																	<div class="col-md-3">
																		<label for="subWalletBalance<?=$game_platform['id']?>"><?=$game_platform['system_code']?>:</label>
																		<input type="text" class="form-control subWalletBalance subWalletBalance<?=$game_platform['id']?>" readonly/>
																	</div>
																<?php endforeach?>

																<!-- <div class="col-md-3">
																	<label for="cashbackWalletBalance">Cashback Balance:</label>
																	<input type="text" class="form-control cashbackWalletBalance" readonly/>
																</div> -->

																<div class="col-md-3">
																	<label for="totalBalance"><?=lang('pay.totalbal');?>:</label>
																	<input type="text" class="form-control totalBalance" readonly/>
																</div>
															</div>
														</div>
													</div>
												</div>

												<hr/>
												<h4><?=lang('pay.withdetl');?></h4>
												<hr/>

												<!-- start payment method -->
												<div class="paymentMethodSection">
													<div class="row">
														<div class="col-md-12">
															<div class="col-md-3">
																<label for="withdrawalAmount"><?=lang('pay.withamt');?>:</label>
																<input type="text" class="form-control withdrawalAmount" readonly/>
															</div>

															<div class="col-md-3">
																<label for="currency"><?=lang('pay.curr');?>:</label>
																<input type="text" class="form-control currency" readonly/>
															</div>

															<div class="col-md-3">
																<label for="dateDeposited"><?=lang('pay.reqtdon');?>:</label>
																<input type="text" class="form-control dateDeposited" readonly/>
															</div>

															<div class="col-md-3">
																<label for="ipLoc"><?=lang('pay.withip') . ' ' . lang('lang.and') . ' ' . lang('pay.locatn');?>:</label>
																<input type="text" class="form-control ipLoc" readonly/>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-md-12">
															<br/>
															<div class="col-md-3">
																<label for="bankName"><?=lang('pay.bankname');?>:</label>
																<input type="text" class="form-control bankName" readonly/>
															</div>

															<div class="col-md-3">
																<label for="bankAccountName"><?=lang('pay.bank') . ' ' . lang('pay.acctname');?>:</label>
																<input type="text" class="form-control bankAccountName" readonly/>
															</div>

															<div class="col-md-3">
																<label for="bankAccountNumber"><?=lang('pay.bank') . ' ' . lang('pay.acctnumber');?>:</label>
																<input type="text" class="form-control bankAccountNumber" readonly/>
															</div>

															<div class="col-md-3">
																<label for="bankAccountBranch"><?=lang('pay.bank') . ' ' . lang('pay.acctbranch');?>:</label>
																<input type="text" class="form-control bankAccountBranch" readonly/>
															</div>
														</div>
													</div>
												</div>
												<!-- end payment method -->

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
						<a class='notificationRefreshList' href="<?=site_url('payment_management/refreshList/withdrawalDeclined')?>">
							<button type="button" class="close"><span aria-hidden="true">×</span><span class="sr-only"><?=lang("lang.close");?></span></button>
						</a>
						<h4 class="modal-title" id="myModalLabel"><?=lang("pay.declwithdetl");?></h4>
					</div>

					<div class="modal-body">
						<div class="col-md-12" id="playerDeclinedDetailsCheckPlayer">
					        <!-- Withdrawal transaction -->
							<div class="row">
								<div class="col-md-12">
									<div class="panel panel-primary">
										<div class="panel-heading">
											<h4 class="panel-title">
												<?=lang("pay.declwithinfo");?>
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
															<label for="userName"><?=lang("pay.username");?>:</label>
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
												<div class="row">
													<div class="col-md-12">
														<br/>
														<div class="col-md-3">
															<label for="mainWalletBalance"><?=lang('pay.mainwalltbal');?>:</label>
															<input type="text" class="form-control mainWalletBalance" readonly/>
														</div>

														<?php foreach ($game_platforms as $game_platform): ?>
															<div class="col-md-3">
																<label for="subWalletBalance<?=$game_platform['id']?>"><?=$game_platform['system_code']?>:</label>
																<input type="text" class="form-control subWalletBalance subWalletBalance<?=$game_platform['id']?>" readonly/>
															</div>
														<?php endforeach?>

														<!-- <div class="col-md-3">
															<label for="cashbackWalletBalance">Cashback Balance:</label>
															<input type="text" class="form-control cashbackWalletBalance" readonly/>
														</div> -->

														<div class="col-md-3">
															<label for="totalBalance"><?=lang('pay.totalbal');?>:</label>
															<input type="text" class="form-control totalBalance" readonly/>
														</div>
													</div>
												</div>
											</div>
											<br/>
											<!-- start payment method -->
											<hr/>
											<h4><?=lang('pay.paymethod') . ' ' . lang('lang.details');?></h4>
											<hr/>
											<!-- start payment method -->
											<div class="paymentMethodSection">
												<div class="row">
														<div class="col-md-12">
															<div class="col-md-3">
																<label for="withdrawalAmount"><?=lang('pay.withamt');?>:</label>
																<input type="text" class="form-control withdrawalAmount" readonly/>
															</div>

															<div class="col-md-3">
																<label for="currency"><?=lang('pay.curr');?>:</label>
																<input type="text" class="form-control currency" readonly/>
															</div>

															<div class="col-md-3">
																<label for="dateDeposited"><?=lang('pay.reqtdon');?>:</label>
																<input type="text" class="form-control dateDeposited" readonly/>
															</div>

															<div class="col-md-3">
																<label for="ipLoc"><?=lang('pay.withip') . ' ' . lang('lang.and') . ' ' . lang('pay.locatn');?>:</label>
																<input type="text" class="form-control ipLoc" readonly/>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-md-12">
															<br/>
															<div class="col-md-3">
																<label for="bankName"><?=lang('pay.bankname');?>:</label>
																<input type="text" class="form-control bankName" readonly/>
															</div>

															<div class="col-md-3">
																<label for="bankAccountName"><?=lang('pay.bank') . ' ' . lang('pay.acctname');?>:</label>
																<input type="text" class="form-control bankAccountName" readonly/>
															</div>

															<div class="col-md-3">
																<label for="bankAccountNumber"><?=lang('pay.bank') . ' ' . lang('pay.acctnumber');?>:</label>
																<input type="text" class="form-control bankAccountNumber" readonly/>
															</div>

															<div class="col-md-3">
																<label for="bankAccountBranch"><?=lang('pay.bank') . ' ' . lang('pay.acctbranch');?>:</label>
																<input type="text" class="form-control bankAccountBranch" readonly/>
															</div>
														</div>
													</div>
											</div>
											<!-- end payment method -->

											<hr/>
											<div class="row">
												<div class="col-md-12">
													<div class="col-md-3">
														<label for="withdrawalMethodDeclinedBy"><?=lang('pay.declby');?>:</label>
														<input type="text" class="form-control withdrawalMethodDeclinedBy" readonly>
													</div>
													<div class="col-md-3">
														<label for="withdrawalMethodDateDeclined"><?=lang('pay.datetimedecl');?>:</label>
														<input type="text" class="form-control withdrawalMethodDateDeclined" readonly>
													</div>
													<div class="col-md-6">
														<label for="withdrawalMethodReasonDeclined"><?=lang('pay.reason');?>:</label>
														<textarea class="form-control withdrawalMethodReasonDeclined" readonly></textarea>
													</div>
												</div>
											</div>

											<hr/>
											<div class="col-md-12 transactionStatusMsg"></div>
											<div class="col-md-12" id="playerDeclinedDetailsRepondBtn">
												<input type="hidden" class="form-control walletAccountIdVal" readonly/>
												<input type="hidden" class="form-control" id="declinedPlayerPromoIdVal" readonly/>
												<!-- <button class="btn btn-md btn-info" onclick="PaymentManagementProcess.withdrawalDeclinedToApprove(<?=$withdrawalRequest['walletAccountId']?>)"><?=lang('pay.changetoapprv');?></button> -->
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
	</div>
	<!-- end declinedDetailsModal-->
</div>

<script type="text/javascript">
    $(document).ready(function(){
    	var sortCol=7;
        $('#myTable').DataTable({
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
            "order": [ sortCol, 'desc' ]
        });
    });

    function getWithdrawalRequest(requestId, playerId) {
    	html  = '';
        html += '<p>';
        html += 'Loading Data...';
        html += '</p>';

    	$.ajax({
            'url' : base_url +'payment_management/viewDuplicateAccounts/'+playerId,
            'type' : 'GET',
            'dataType' : "html",
            'success' : function(data){
    			$(".playerDuplicateAccountInfoPanel").html(data);
            }
        });

       $('#playerRequestDetails').html(html);
       $.ajax({
            'url' : base_url +'payment_management/reviewWithdrawalRequest/'+requestId+'/'+playerId,
            'type' : 'GET',
            'dataType' : "json",
            'success' : function(data){
                           html  = '';
                           $('#playerRequestDetails').html(html);
                           //personal info
                           $('.playerId').val(data['transactionDetails'][0].playerId);
                           $('.userName').val(data['transactionDetails'][0].playerName);
                           $('.playerName').val(data['transactionDetails'][0].firstName+' '+data['transactionDetails'][0].lastName);
                           $('.email').val(data['transactionDetails'][0].email);
                           $('.memberSince').val(data['transactionDetails'][0].createdOn);
                           $('.address').val(data['transactionDetails'][0].address);
                           $('.city').val(data['transactionDetails'][0].city);
                           $('.country').val(data['transactionDetails'][0].country);
                           $('.birthday').val(data['transactionDetails'][0].birthdate);
                           $('.gender').val(data['transactionDetails'][0].gender);
                           $('.phone').val(data['transactionDetails'][0].phone);
                           $('.cp').val(data['transactionDetails'][0].contactNumber);
                           $('.request_walletAccountIdVal').val(data['transactionDetails'][0].walletAccountId);
                           $('.ipLoc').val(data['transactionDetails'][0].dwIp+' - '+data['transactionDetails'][0].dwLocation);

                           //deposit details
                           $('.dateDeposited').val(data['transactionDetails'][0].dwDateTime);
                           $('.playerLevel').val(data['transactionDetails'][0].groupName+' '+data['transactionDetails'][0].vipLevel);
                           $('.depositMethod').val(data['transactionDetails'][0].paymentMethodName);
                           $('.withdrawalAmount').val(data['transactionDetails'][0].amount);
                           $('.currency').val(data['transactionDetails'][0].currentBalCurrency);

                           //payment method details
                           $('.bankName').val(data['transactionDetails'][0].bankName);
                           $('.bankAccountName').val(data['transactionDetails'][0].bankAccountFullName);
                           $('.bankAccountNumber').val(data['transactionDetails'][0].bankAccountNumber);
                           $('.bankAccountBranch').val(data['transactionDetails'][0].branch);
                           $('.mainWalletBalance').val(data['transactionDetails'][0].currentBalAmount);

							$('.subWalletBalance').val('<?=$this->utils->formatCurrency(0)?>');
							$.each(data['transactionDetails'][0]['subwalletBalanceAmount'], function(index,subwallet) {
								$('.subWalletBalance' + subwallet.typeId).val(subwallet.totalBalanceAmount);
							});

                           $('.totalBalance').val(data['transactionDetails'][0]['totalBalance']);
                           $('.playerBonusInfoPanel').hide();

                           if(data['transactionDetails'][0]['is_checking']){
                           		$('#checking_btn').hide();
                           }
                            if(data['transactionDetails'][0]['withdrawCondition'].length > 0){
                                $('.playerBonusInfoPanel').show();
                                var totalRequiredBet = 0;
                                var totalPlayerBet = 0;
                                for (var i = 0; i < data['transactionDetails'][0]['withdrawCondition'].length; i++) {
                                		totalRequiredBet += parseFloat(data['transactionDetails'][0]['withdrawCondition'][i].conditionAmount);
                                		if(parseFloat(data['transactionDetails'][0]['withdrawCondition'][i].currentBet[0]['totalBetAmount']) > totalPlayerBet){
                                			totalPlayerBet = parseFloat(data['transactionDetails'][0]['withdrawCondition'][i].currentBet[0]['totalBetAmount']);
                                		}
                                        var transaction = '';
                                            if(data['transactionDetails'][0]['withdrawCondition'][i].source_type == 1){
                                                transaction = "<?=lang('player.ub05')?>";
                                            }else if(data['transactionDetails'][0]['withdrawCondition'][i].source_type == 2){
                                            	transaction = "<?=lang('pay.depositWithPromo')?>";
                                            }else if(data['transactionDetails'][0]['withdrawCondition'][i].source_type == 3){
                                            	transaction = "<?=lang('pay.nonDepositPromo')?>";
                                            }
                                        var promoName = '';
                                        	if(data['transactionDetails'][0]['withdrawCondition'][i].promoName == ''){
                                                promoName = "<?=lang('pay.noPromo')?>";
                                            }else{
                                            	promoName = data['transactionDetails'][0]['withdrawCondition'][i].promoName;
                                            }
                                        var promoCode = '';
                                        	if(data['transactionDetails'][0]['withdrawCondition'][i].promoCode == ''){
                                                promoCode = "<?=lang('pay.noPromoCode')?>";
                                            }else{
                                            	promoCode = data['transactionDetails'][0]['withdrawCondition'][i].promoCode;
                                            }

                                        var depositCondition = '';
                                        var nonfixedDepositAmtCondition = '';
                                        if(data['transactionDetails'][0]['withdrawCondition'][i].depositConditionType == 0){//fixed
					                        depositCondition = "<?=lang('cms.fixDepAmt')?> = "+data['transactionDetails'][0]['withdrawCondition'][i].depositConditionDepositAmount;
					                    }else if(data['transactionDetails'][0]['withdrawCondition'][i].depositConditionType == 1){//nonfixed
					                        if(data['transactionDetails'][0]['withdrawCondition'][i].depositConditionNonFixedDepositAmount == 0){
					                            nonfixedDepositAmtCondition = data['transactionDetails'][0]['withdrawCondition'][i].nonfixedDepositAmtCondition == 0 ? "<=" : ">=";
					                            depositCondition = "<?=lang('cms.nonfixDepAmt')?> ("+ nonfixedDepositAmtCondition +" "+data['transactionDetails'][0]['withdrawCondition'][i].nonfixedDepositAmtConditionRequiredDepositAmount+")";
					                        }else{
					                            depositCondition = "<?=lang('cms.anyAmt')?>";
					                        }
					                    }

					                    if(depositCondition==''){
					                    	depositCondition = "<?=lang('pay.noPromo')?>";
					                    }

					                    //bonusReleaseSec
					                    if(data['transactionDetails'][0]['withdrawCondition'][i].bonusReleaseRule == 0){
					                        bonusReleaseRule = "<?=lang('cms.fixedBonusAmount')?> = "+data['transactionDetails'][0]['withdrawCondition'][i].promorulesBonusAmount;
					                    }else{
					                        bonusReleaseRule = data['transactionDetails'][0]['withdrawCondition'][i].depositPercentage+"<?=lang('cms.percentageOfDepositAmt')?> "+data['transactionDetails'][0]['withdrawCondition'][i].maxBonusAmount+" <?=lang('cms.maxbonusamt')?>";
					                    }
					                    //$('#bonusRelease').val(bonusReleaseRule);
					                    var currentBet = 0;
					                    if(isNaN(parseFloat(data['transactionDetails'][0]['withdrawCondition'][i].currentBet[0]['totalBetAmount']))){
					                    	currentBet = 0;
					                    }else{
					                    	currentBet = parseFloat(data['transactionDetails'][0]['withdrawCondition'][i].currentBet[0]['totalBetAmount']);
					                    }

					                    //withdrawRequirement
					                    if(data['transactionDetails'][0]['withdrawCondition'][i].withdrawRequirementRule == 0){
					                        if(data['transactionDetails'][0]['withdrawCondition'][i].withdrawRequirementConditionType == 0){
					                            withdrawRequirement = "<?=lang('cms.withBetAmtCond')?> >= "+data['transactionDetails'][0]['withdrawCondition'][i].withdrawRequirementBetAmount;
					                        }else{
					                            if(data['transactionDetails'][0]['withdrawCondition'][i].promoType == 1){
					                                withdrawRequirement = "<?=lang('cms.betAmountCondition2')?> "+data['transactionDetails'][0]['withdrawCondition'][i].withdrawRequirementBetCntCondition;
					                            }else{
					                                withdrawRequirement = "<?=lang('cms.betAmountCondition1')?> "+data['transactionDetails'][0]['withdrawCondition'][i].withdrawRequirementBetCntCondition;
					                            }
					                        }
					                    }else{
					                        withdrawRequirement = "<?=lang('cms.noBetRequirement')?>";
					                    }

					                    //deposit amount
					                    var withdrawalConditionDeposit = 0;
					                    if(isNaN(parseFloat(data['transactionDetails'][0]['withdrawCondition'][i].walletDepositAmount))){
					                    	withdrawalConditionDeposit = 0;
					                    }else{
					                    	withdrawalConditionDeposit = data['transactionDetails'][0]['withdrawCondition'][i].walletDepositAmount;
					                    }

                                        html  = '';
                                        html += '<tr>';
                                        html += '<td>'+transaction+'</td>';
                                        html += '<td>'+promoName+'</td>';
                                        if(data['transactionDetails'][0]['withdrawCondition'][i].promoName){
                                        	html += '<td>(<?=lang('cms.depCon')?>)<br/> '+ depositCondition+'<br/> (<?=lang('cms.bonus')?>)<br/>'+bonusReleaseRule+'<br/> (<?=lang('promo.betCondition')?>)<br/>'+withdrawRequirement+'</td>';
                                        }else{
                                        	html += '<td>No Promo</td>';
                                        }
                                        html += '<td>'+withdrawalConditionDeposit+'</td>';
                                        html += '<td>'+data['transactionDetails'][0]['withdrawCondition'][i].started_at+'</td>';
                                        html += '<td>'+data['transactionDetails'][0]['withdrawCondition'][i].conditionAmount+'</td>';
                                        html += '<td>'+currentBet+'</td>';
                                        html += '</tr>';
                                      $('.playerBonusTable').append(html);
                                  }
                                   if(isNaN(totalPlayerBet)){
                                   		totalPlayerBet = 0;
                                   }
                                  html_trb = '';
                                  html_trb += "<tr>";
                                  html_trb += "<td colspan='5'></td><td><b><?=lang('pay.totalRequiredBet')?>: "+totalRequiredBet+"</b></td>";
                                  html_trb += "<td><b><?=lang('pay.totalPlayerBet')?>: "+totalPlayerBet+"</b></td>";
                                  html_trb += '</tr>';
                                  $('.playerBonusTable').append(html_trb);
                           }
                        }
       },'json');
        return false;
    }

    function getResultDuplicate(a, key, string) {
		if (a[key][string] != undefined)
			return a[key][string];
		else
			return "N/A";
    }

    function checkingRequest() {
       var walletAccountIdVal = $('.request_walletAccountIdVal').val();
       var playerId = $('.playerId').val();

        $.ajax({
            'url' : base_url +'payment_management/set_withdrawal_checking/'+walletAccountIdVal+'/'+playerId,
            'type' : 'GET',
            'cache' : false,
            'dataType' : "json"
        }
       	).done(
       	function(data){
       		//utils.safelog(data);

       		html  = '';
	        html += '<p>';
	        html += "<?=lang('text.checking')?>";
	        html += '</p>';

	       $('.transactionStatusMsg').html(html);
	       $('#checking_btn').hide();
       	});
   }

    function respondToWithdrawalRequest() {

        html  = '';
        html += '<p>';
        html += 'Loading Data...';
        html += '</p>';

       $('.transactionStatusMsg').html(html);

       var promoBonusStatus = $('#promoBonusStatus').val();
       var walletAccountIdVal = $('.request_walletAccountIdVal').val();
       var playerPromoIdVal = $('#requestPlayerPromoIdVal').val();
       // var playerNewMainWalletTotalBalanceAmount = Number($('.mainWalletBalance').val()) - Number($('.withdrawalAmount').val());
       var playerId = $('.playerId').val();
       // var withdrawalAmount = Number($('.withdrawalAmount').val());
       $.ajax({
            'url' : base_url +'payment_management/respondToWithdrawalRequest/'+walletAccountIdVal+'/'+playerId,
            'type' : 'GET',
            'success' : function(data){

                        if(data == 'success') {
                            html  = '';
                            html += '<p>';
                            html += "<?=lang('pay.withdrawalHasBeenApproved')?>";
                            html += '</p>';
                        } else {
                            html  = '';
                            html += '<p>';
                            html += "<?=lang('pay.maxWithdrawApprovedReached')?>";
                            html += '</p>';
                        }

                           $('.transactionStatusMsg').html(html);
                           $('#repondBtn').hide();
                        }
       },'json');
        return false;
    }

    function respondToWithdrawalDeclined() {

        html  = '';
        html += '<p>';
        html += 'Loading Data...';
        html += '</p>';

        var showDeclinedReasonVal = $('#showDeclinedReason_cbx').is(':checked');
        var reason = $('#declinedReasonTxt').val().replace("%20", " ");
        var walletAccountIdVal = $('.request_walletAccountIdVal').val();

       $('.transactionStatusMsg').html(html);

       $.ajax({
            'url' : base_url +'payment_management/respondToWithdrawalDeclined/'+walletAccountIdVal+'/'+reason+'/'+showDeclinedReasonVal,
            'type' : 'GET',
            'success' : function(data){
            	if(data=='success'){

                    html  = '';
                    html += '<p>';
                    html += 'Withdrawal has been Declined!';
                    html += '</p>';

                   $('.transactionStatusMsg').html(html);
                   $('#repondBtn').hide();
                   $('#declineReason-sec').hide();
                }else{
                	alert('Decline failed');
                }
            }
       },'json');
        return false;
    }
</script>
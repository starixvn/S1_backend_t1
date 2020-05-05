		<form action="<?= BASEURL . 'payment_management/sortDepositWithdrawalList/thirdpartydeposit' ?>" method="post" role="form">
			<!-- start date picker range api -->
			<div class="well" style="overflow: auto;margin-bottom:10px;">

				<!-- start dashboard notification -->			         
				<a class='notificationRefreshList' href="<?= BASEURL . 'payment_management/refreshList/thirdPartyDepositRequest' ?>">
					<div class="col-md-2 notificationDashboard hover-shadow <?= $this->session->userdata('dwStatus') == 'request' ? 'notDboard-active' : ''?>" id="notificationDashboard-request-deposit">
						<?= lang('pay.total'); ?><br/><span class="notificationDashboardTxt" id="notificationDashboard-request-deposit"><?= $deposit_request_cnt ?></span><br/>
						<span class="notificationDashboardLabel" id="notificationDashboard-request-deposit"><?= lang('pay.depreq'); ?></span>
					</div>
				</a>
				<a class='notificationRefreshList' href="<?= BASEURL . 'payment_management/refreshList/thirdPartyDepositApproved' ?>">
					<div class="col-md-2 notificationDashboard hover-shadow <?= $this->session->userdata('dwStatus') == 'approved' ? 'notDboard-active' : ''?>" id="notificationDashboard-approved-deposit">
						<?= lang('pay.total'); ?><br/><span class="notificationDashboardTxt" id="notificationDashboard-approved-deposit"><?= $deposit_approved_cnt ?></span><br/>
						<span class="notificationDashboardLabel" id="notificationDashboard-approved-deposit"><?= lang('pay.appreq'); ?></span>
					</div>
				</a>
				<a class='notificationRefreshList' href="<?= BASEURL . 'payment_management/refreshList/thirdPartyDepositDeclined' ?>">
					<div class="col-md-2 notificationDashboard hover-shadow <?= $this->session->userdata('dwStatus') == 'declined' ? 'notDboard-active' : ''?>" id="notificationDashboard-declined-deposit">
						<?= lang('pay.total'); ?><br/><span class="notificationDashboardTxt" id="notificationDashboard-declined-deposit"><?= $deposit_declined_cnt ?></span><br/>
						<span class="notificationDashboardLabel" id="notificationDashboard-declined-deposit"><?= lang('pay.decreq'); ?></span>
					</div>
				</a>
				<!-- end dashboard notification -->

				<!-- start sort dw list -->
					<!-- <div><h2 class="pull-right">Deposit Request</h2></div> -->
					<div class="pull-right daterangePicker-sec">
					   <h4><?= lang('pay.transperd'); ?></h4>
					   <div class="pull-left">
			               <div id="reportrange" class="pull-right daterangePicker">
			                  <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>			                  
			                  <span id="dateRangeData"><?= $this->session->userdata('dateRangeValue') == "" ? date("F j, Y", strtotime('-7 day')).' - '.date("F j, Y") : $this->session->userdata('dateRangeValue')?></span> <b class="caret"></b>
			               </div>
			               	  <input type="hidden" id="dateRangeValue" name="dateRangeValue" value="<?= $this->session->userdata('dateRangeValue') == '' ? '' : $this->session->userdata('dateRangeValue'); ?>" />			                  
			                  <input type="hidden" id="dateRangeValueStart" name="dateRangeValueStart" value="<?= $this->session->userdata('dateRangeValueStart') == '' ? '' : $this->session->userdata('dateRangeValueStart'); ?>" />
                              <input type="hidden" id="dateRangeValueEnd" name="dateRangeValueEnd" value="<?= $this->session->userdata('dateRangeValueEnd') == '' ? : $this->session->userdata('dateRangeValueEnd'); ?>" />
			                  <input type="hidden" id="dwStatus" name="dwStatus" value="<?= $this->session->userdata('dwStatus') == '' ? 'request' : $this->session->userdata('dwStatus') ?>" />
			            </div>
			            <div class="col-md-1">
				            <input type="submit" class="btn btn-sm btn-info" value="<?= lang('lang.submit'); ?>" />				        
			           	</div>
			            <br/><br/>
	               </div>      
            </div> 
            <!-- end date picker range api -->
           
		</form>

<div class="row">
	<!-- start request list -->
	<div class="col-md-12" id="toggleView">
		<div class="col-md-5"></div>	
		<div class="panel panel-primary">
			<div class="panel-heading custom-ph">
				<h4 class="panel-title custom-pt"><i class="icon-loop2"></i> <?= $transactionType ?>
					<span class="choosenDateRange">&nbsp;<?= isset($choosenDateRange) ? ($choosenDateRange) : '' ?></span>
				</h4>
			</div>

			<!-- start data table -->
			<div class="panel-body" id="player_panel_body">
				<div id="paymentList" class="table-responsive">
					<table class="table table-striped table-hover tablepress table-condensed" id="myTable" style="margin: 0px 0 0 0; width: 100%;">
						<thead>
							<tr>
								<th></th>
								<th class="tableHeaderFont"><?= lang('system.word38'); ?></th>
								<th class="tableHeaderFont"><?= lang('system.word39'); ?></th>
								<th class="tableHeaderFont"><?= lang('pay.playerlev'); ?></th>
								<th class="tableHeaderFont"><?= lang('pay.amt'); ?></th>
								<th class="tableHeaderFont"><?= lang('pay.curr'); ?></th>
								<th class="tableHeaderFont"><?= lang('pay.depmethod'); ?></th>
								<!-- <th class="tableHeaderFont"><?= lang('con.bnk10'); ?></th> -->
								<th class="tableHeaderFont"><?= lang('pay.reqtime'); ?></th>
								<th class="tableHeaderFont"><?= lang('pay.merchacct'); ?></th>
								<th class="tableHeaderFont"><?= lang('pay.promoname'); ?></th>
								<th class="tableHeaderFont"><?= lang('pay.promobonus'); ?></th>
								<th class="tableHeaderFont"><?= lang('pay.deposit') . ' ' . lang('pay.ip'); ?></th>
								<th class="tableHeaderFont"><?= lang('pay.deposit') . ' ' . lang('pay.locatn'); ?></th>
								<!-- <th>Bonus Amount</th> -->
								<?php if($this->session->userdata('dwStatus') == 'approved' || $this->session->userdata('dwStatus') == 'declined'){ ?>
									<th class="tableHeaderFont"><?= lang('pay.procssby'); ?></th>
									<th class="tableHeaderFont"><?= lang('pay.procsson'); ?></th>
								<?php	} ?>
								<th class="tableHeaderFont"><?= lang('lang.action'); ?></th>
							</tr>
						</thead>

						<tbody>
							<?php //var_dump($depositRequest);
								if(!empty($depositRequest)) {
									foreach($depositRequest as $depositRequest) {
							?>
										<tr>
											<td></td>
											<td><?= $depositRequest['playerName'] == '' ? '<i class="help-block"><?= lang("lang.norecyet"); ?></i>' : ucwords($depositRequest['playerName']) ?></td>		
											<td><?= $depositRequest['firstName'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>'  : ucwords($depositRequest['firstName'].' '.$depositRequest['lastName']) ?></td>									
											<td><?= $depositRequest['groupName'].' '.$depositRequest['vipLevel'] ?></td>
											<td><?= $depositRequest['amount'] == '' ? '<i class="help-block">'.lang("lang.norecyet").'</i>' : $depositRequest['amount'] ?></td>
											<td><?= $depositRequest['currency'] == '' ? '<i class="help-block">'.lang("lang.norecyet").'</i>' : $depositRequest['currency'] ?></td>
											<td><?= $depositRequest['paymentMethodName'] == '' ? '<i class="help-block">'.lang("lang.norecyet").'</i>' : $depositRequest['paymentMethodName'] ?></td>
											<!-- <td><?= $depositRequest['transactionFee'] == '' ? '<i class="help-block">'.lang("lang.norecyet").'</i>' : $depositRequest['transactionFee'] ?></td> -->
											<td><?= $depositRequest['dwDateTime'] == '' ? '<i class="help-block">'.lang("lang.norecyet").'</i>' : mdate('%M %d, %Y %H:%i:%s',strtotime($depositRequest['dwDateTime'])) ?></td>
											<td>
												<?php if(($depositRequest['paypalMerchantAccount'] == '') && ($depositRequest['netellerMerchantAccount'] == '')){ ?>
														<i class="help-block"><?= lang("lang.norecyet") ?></i>
												<?php }elseif($depositRequest['paypalMerchantAccount']){ ?>
														<?= $depositRequest['paypalMerchantAccount'] ?>
												<?php }elseif($depositRequest['netellerMerchantAccount'] != ''){ ?>
														<?= $depositRequest['netellerMerchantAccount'] ?>
												<?php } ?>												
											</td>
											<td><?= $depositRequest['promoName'] == '' ? '<i class="help-block">'. lang("lang.no") . " " . lang("lang.promo"). '</i>' : $depositRequest['promoName'] ?></td>
											<td><?= $depositRequest['bonusAmount'] == '' ? '<i class="help-block">'. lang("lang.no") . " " . lang("lang.bonus"). '</i>' : $depositRequest['bonusAmount'] ?></td>
											<td><?= $depositRequest['dwIp'] == '' ? '<i class="help-block">'. lang("lang.no") . " " . lang("lang.bonus") .'<i/>' : $depositRequest['dwIp'] ?></td>
											<td><?= $depositRequest['dwLocation'] == ',' ? '<i class="help-block">'. lang("lang.no") . " " . lang("lang.record") .'<i/>' : $depositRequest['dwLocation'] ?></td>
											<?php if($depositRequest['dwStatus'] == 'approved' || $depositRequest['dwStatus'] == 'declined'){ ?>															
														<td><?= $depositRequest['processedByAdmin'] == '' ? '<i class="help-block">'.lang("lang.norecyet").'<i/>' : ucwords($depositRequest['processedByAdmin']) ?></td>
														<td><?= $depositRequest['processDatetime'] == '' ? '<i class="help-block">'.lang("lang.norecyet").'<i/>' : mdate('%M %d, %Y %H:%i:%s',strtotime($depositRequest['processDatetime'])) ?></td>
											<?php	} ?>
											
											<td>
											<?php if($depositRequest['dwStatus'] == 'approved'){ ?>
												<span class="btn btn-xs btn-info review-btn" onclick="PaymentManagementProcess.getDepositApproved(<?= $depositRequest['walletAccountId'] ?>,<?= $depositRequest['dwMethod'] ?>)" data-toggle="modal" data-target="#approvedDetailsModal">
													<?= lang("lang.details"); ?>
												</span>

											<?php }elseif($depositRequest['dwStatus'] == 'declined'){ ?>										
												<span class="btn btn-xs btn-info review-btn" onclick="PaymentManagementProcess.getAutoThirdPartyDepositDeclined(<?= $depositRequest['walletAccountId'] ?>,<?= $depositRequest['dwMethod'] ?>)" data-toggle="modal" data-target="#declinedDetailsModal">
													<?= lang("lang.details"); ?>
												</span>

											<?php }else{ ?>
												<span class="btn btn-xs btn-info review-btn" onclick="PaymentManagementProcess.getAutoThirdPartyDepositRequest(<?= $depositRequest['walletAccountId'] ?>,<?= $depositRequest['dwMethod'] ?>)" data-toggle="modal" data-target="#requestDetailsModal">
													<?= lang("lang.details"); ?>
												</span>
											<?php } ?>
											</td>
										</tr>
							<?php 		
									}
								}
								else{ ?>
										<!-- <tr>
											<td colspan="14" style="text-align:center"><?= lang("lang.norec"); ?>
											</td>
										</tr> -->
							<?php	}
							?>
						</tbody>
					</table>

					<!-- <div class="panel-footer">
						<ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
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
						<a class='notificationRefreshList' href="<?= BASEURL . 'payment_management/refreshList/thirdPartyDepositRequest' ?>">
							<button type="button" class="close"><span aria-hidden="true">×</span><span class="sr-only"><?= lang("lang.close"); ?></span></button>
						</a>
						<h4 class="modal-title" id="myModalLabel"><i class="icon-loop2"></i>&nbsp;<?= lang("pay.deposit") . ' ' . lang("pay.req") . ' ' . lang("lang.details"); ?></h4>
					</div>

					<div class="modal-body">						
						<div class="col-md-12" id="checkPlayer">
							<!-- Deposit transaction -->
							<div class="row">
								<div class="col-md-12">
									<div class="panel panel-primary">
										<div class="panel-heading">
											<h4 class="panel-title">
												<?= lang("pay.deposit") . ' ' . lang('lang.info'); ?>
												<a href="#personal" 
              id="hide_deposit_info" class="btn btn-default btn-sm pull-right"> 
													<i class="glyphicon glyphicon-chevron-down" id="hide_deposit_info_up"></i>
												</a> 
												<div class="clearfix"></div>
											</h4>
										</div>

										<div class="panel panel-body" id="deposit_info_panel_body" style="display: none;">
											<div class="row">
												<div class="col-md-12">
													<div class="col-md-2">
														<label for="userName"><?= lang("pay.user") . ' ' . lang("pay.name"); ?>:</label>
														<input type="hidden" class="form-control playerId" readonly/>
														<input type="text" class="form-control userName" readonly/>
													</div>

													<div class="col-md-3">
														<label for="playerName"><?= lang("pay.realname"); ?>:</label>
														<input type="text" class="form-control playerName" readonly/>
													</div>

													<div class="col-md-3">
														<label for="playerLevel"><?= lang('pay.playerlev'); ?>:</label>
														<input type="text" class="form-control playerLevel" readonly/>
													</div>

													<div class="col-md-2">
														<label for="memberSince"><?= lang('pay.memsince'); ?>: </label>
														<input type="text" class="form-control memberSince" readonly>
													</div>

													<div class="col-md-2">
														<label for="depositCnt"><?= lang('player.ui14'); ?>: </label>
														<input type="text" class="form-control depositCnt" readonly>
													</div>
												</div>
											</div>
											<br/>
											<div class="row">
												<div class="col-md-12">													
													<div class="col-md-3">
														<label for="depositedAmount"><?= lang('pay.deposit') . ' ' . lang('pay.amt'); ?>:</label>
														<input type="text" class="form-control depositedAmount" readonly/>
													</div>

													<div class="col-md-3">
														<label for="depositMethod"><?= lang('pay.depmethod'); ?>:</label>
														<input type="text" class="form-control depositMethod" readonly/>
													</div>

													<div class="col-md-3">
														<label for="dateDeposited"><?= lang('lang.date') . ' ' . lang('pay.deposited'); ?>:</label>
														<input type="text" class="form-control dateDeposited" readonly/>
													</div>

													<div class="col-md-3">
														<label for="ipLoc"><?= lang('pay.deposit') . ' ' . lang('pay.ip') . ' ' . lang('lang.and') . ' ' . lang('pay.locatn'); ?>:</label>
														<input type="text" class="form-control ipLoc" readonly/>
													</div>
												</div>
											</div>

											

											<!-- start payment method -->
											<hr/>
											<h4><?= lang('pay.paytmethdetls'); ?></h4>
											<hr/>
												<!-- start paypal payment method -->
												<div class="paypalPaymentMethodSection">
													<div class="row">
														<div class="col-md-12">
															
															<div class="col-md-1">
																<label for="paypalAccountName"><?= lang('pay.acctname'); ?>:</label>
															</div>

															<div class="col-md-3">
																<input type="text" class="form-control paypalAccountName" readonly>
																<br/>
															</div>

															<div class="col-md-2">
																<label for="paypalEmail"><?= lang('lang.email'); ?>:</label>
															</div>

															<div class="col-md-3">
																<input type="text" class="form-control paypalEmail" readonly/>
																<br/>
															</div>																										
														</div>
													</div>

													<div class="row">
														<div class="col-md-12">
															
															<div class="col-md-1">
																<label for="paypalTransactionId"><?= lang('pay.transid'); ?>:</label>
															</div>

															<div class="col-md-3">
																<input type="text" class="form-control paypalTransactionId" readonly>
																<br/>
															</div>

															<div class="col-md-2">
																<label for="paypalSecureMerchantAccountId"><?= lang('pay.secmercacctid'); ?>:</label>
															</div>

															<div class="col-md-3">
																<input type="text" class="form-control paypalSecureMerchantAccountId" readonly/>
																<br/>
															</div>

																											
														</div>
													</div>
													<div class="row">
														<div class="col-md-12">
															<div class="col-md-1">
																<label for="paypalTransactionDateTime"><?= lang('pay.transdatetime'); ?>:</label>
															</div>
															<div class="col-md-3">
																<input type="text" class="form-control paypalTransactionDateTime" readonly/>
																<br/>
															</div>	
															

															<div class="col-md-2">
																<label for="paypalTransactionStatus"><?= lang('pay.transstatus'); ?>:</label>
															</div>

															<div class="col-md-2">
																<input type="text" class="form-control paypalTransactionStatus" readonly/>
																<br/>
															</div>
														</div>
													</div>
												</div>
												<!-- end paypal payment method -->

												<div class="netellerPaymentMethodSection">
													<div class="row">
														<div class="col-md-12">
															
															<div class="col-md-1">
																<label for="netellerAccount">Neteller <?= lang('pay.acct'); ?>:</label>
															</div>

															<div class="col-md-3">
																<input type="text" class="form-control netellerAccount" readonly>
																<br/>
															</div>

															<div class="col-md-1">
																<label for="securedId"><?= lang('pay.securedId'); ?>:</label>
															</div>

															<div class="col-md-3">
																<input type="text" class="form-control netellerSecuredId" readonly/>
																<br/>
															</div>																										
														</div>
													</div>													
												</div>

											<!-- end payment method -->		

											<!-- start bonus info -->
											<div class="row playerBonusInfoPanel">
												<div class="col-md-12">
													<hr/>
													<h4><?= lang('pay.activePlayerPromo') ?></h4>
													<hr/>
													<div class="col-md-12 ">
														<table class="table playerBonusTable table-striped table-hover tablepress table-condensed">
															<th><?= lang('player.up01'); ?></th>
															<th><?= lang('cms.promocode'); ?></th>
															<th><?= lang('pay.bonusamt'); ?></th>
															<th><?= lang('pay.dateJoined'); ?></th>
															<th><?= lang('player.up03'); ?></th>
														</table>
													</div>
												</div>
											</div>
											<!-- end bonus info -->

											<!-- start bonus info -->
											<div class="row bonusInfoPanel">
												<div class="col-md-12">
													<hr/>
													<h4><?= lang('pay.bonapplictn'); ?></h4>
													<hr/>
													<div class="col-md-3">
														<label for="promoName"><?= lang('lang.promo') . ' ' . lang('pay.name'); ?>:</label>
														<input type="text" class="form-control promoName" readonly/>
														<input type="hidden" class="form-control playerDepositPromoId" readonly/>
														<br/>
													</div>

													<div class="col-md-3">
														<label for="playerPromoBonusAmount"><?= lang('pay.compbonamt'); ?>:</label>
														<input type="text" class="form-control" id="requestPlayerPromoBonusAmount" readonly/>
														<br/>
													</div>													
												</div>
											</div>
											<!-- end bonus info -->									

											<div class="row">
												<hr/>
												<div class="col-md-12" id="playerRequestDetails"></div>
												<div class="col-md-12 pull-right" id="repondBtn">													
													<input type="hidden" class="form-control walletAccountIdVal" readonly/>
													<input type="hidden" class="form-control" id="requestPlayerPromoIdVal" readonly/>
													<button class="btn btn-md btn-info" onclick="PaymentManagementProcess.respondToDepositRequest()"><?= lang('lang.approve'); ?></button>
													<button class="btn btn-md btn-info" onclick="PaymentManagementProcess.showDeclineReason()"><?= lang('lang.decline'); ?></button>							
												</div>
												<div class="col-md-5" id="declineReason-sec">
													<p><?= lang('pay.plsadddeclreason'); ?>:</p>
													<textarea class="form-control" cols="50" rows="5" id="declinedReasonTxt"></textarea><br/>
													<input type="checkbox" name="showDeclinedReason_cbx" id="showDeclinedReason_cbx"> <?= lang('pay.showtoplayr'); ?><br/><br/>
													<button class="btn btn-md btn-info" onclick="PaymentManagementProcess.respondToDepositDeclined(<?= $depositRequest['walletAccountId'] ?>)"><?= lang('pay.declnow'); ?></button>
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
						<a class='notificationRefreshList' href="<?= BASEURL . 'payment_management/refreshList/thirdPartyDepositApproved' ?>">
							<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only"><?= lang('lang.close'); ?></span></button>
						</a>
						<h4 class="modal-title" id="myModalLabel"><i class="icon-loop2"></i>&nbsp;<?= lang('pay.appdepodetals'); ?></h4>
					</div>

					<div class="modal-body">

						<!-- player transaction -->
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-primary">
									<div class="panel-heading">
										<h4 class="panel-title">
											<?= lang('pay.appdepoinfo'); ?>
											<a href="#approvedDeposit" 
              id="hide_approved_deposit_transac" class="btn btn-default btn-sm pull-right"> 
												<i class="glyphicon glyphicon-chevron-down" id="hide_approved_deposit_transac_up"></i>
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
															<label for="userName"><?= lang("pay.user") . ' ' . lang("pay.name"); ?>:</label>
															<input type="text" class="form-control userName" readonly/>
														</div>

														<div class="col-md-3">
															<label for="playerName"><?= lang("pay.realname"); ?>:</label>
															<input type="text" class="form-control playerName" readonly/>
														</div>

														<div class="col-md-3">
															<label for="playerLevel"><?= lang('pay.playerlev'); ?>:</label>
															<input type="text" class="form-control playerLevel" readonly/>
														</div>

														<div class="col-md-3">
															<label for="memberSince"><?= lang('pay.memsince'); ?>: </label>
															<input type="text" class="form-control memberSince" readonly>
														</div>																								
													</div>
												</div>
												<br/>
												<div class="row">
													<div class="col-md-12">													
														<div class="col-md-3">
															<label for="depositedAmountApprovedDeposit"><?= lang('pay.deposit') . ' ' . lang('pay.amt'); ?>:</label>
															<input type="text" class="form-control depositedAmountApprovedDeposit" readonly/>
														</div>

														<div class="col-md-3">
															<label for="depositMethodApprovedDeposit"><?= lang('pay.depmethod'); ?>:</label>
															<input type="text" class="form-control depositMethodApprovedDeposit" readonly/>
														</div>

														<div class="col-md-3">
															<label for="dateDepositedApprovedDeposit"><?= lang('lang.date') . ' ' . lang('pay.deposited'); ?>:</label>
															<input type="text" class="form-control dateDepositedApprovedDeposit" readonly/>
														</div>

														<div class="col-md-3">
															<label for="ipLoc"><?= lang('pay.deposit') . ' ' . lang('pay.ip') . ' ' . lang('lang.and') . ' ' . lang('pay.locatn'); ?></label>
															<input type="text" class="form-control ipLoc" readonly/>
														</div>
													</div>
												</div>

												<!-- start bonus info -->
												<div class="row bonusInfoPanel">
													<div class="col-md-12">
														<hr/>
														<h4><?= lang('pay.bonapplictn'); ?></h4>
														<hr/>
														<div class="col-md-3">
															<label for="promoName"><?= lang('lang.promo') . ' ' . lang('pay.name'); ?>:</label>
															<input type="text" class="form-control promoName" readonly/>
															<input type="hidden" class="form-control playerDepositPromoId" readonly/>
															<br/>
														</div>

														<div class="col-md-3">
															<label for="playerPromoBonusAmount"><?= lang('pay.compbonamt'); ?>:</label>
															<input type="text" class="form-control" id="approvedPlayerPromoBonusAmount" readonly/>
															<br/>
														</div>													
													</div>
												</div>
												<!-- end bonus info -->

											<!-- start payment method -->
											<hr/>
											<h4><?= lang('pay.paytmethdetls'); ?></h4>
											<hr/>
												<!-- start paypal payment method -->
												<div class="paypalPaymentMethodSection">
													<div class="row">
														<div class="col-md-12">
															
															<div class="col-md-1">
																<label for="paypalAccountName"><?= lang('pay.acctname'); ?>:</label>
															</div>

															<div class="col-md-3">
																<input type="text" class="form-control paypalAccountName" readonly>
																<br/>
															</div>

															<div class="col-md-2">
																<label for="paypalEmail"><?= lang('lang.email'); ?>:</label>
															</div>

															<div class="col-md-3">
																<input type="text" class="form-control paypalEmail" readonly/>
																<br/>
															</div>																										
														</div>
													</div>

													<div class="row">
														<div class="col-md-12">
															
															<div class="col-md-1">
																<label for="paypalTransactionId"><?= lang('pay.transid'); ?>:</label>
															</div>

															<div class="col-md-3">
																<input type="text" class="form-control paypalTransactionId" readonly>
																<br/>
															</div>

															<div class="col-md-2">
																<label for="paypalSecureMerchantAccountId"><?= lang('pay.secmercacctid'); ?>:</label>
															</div>

															<div class="col-md-3">
																<input type="text" class="form-control paypalSecureMerchantAccountId" readonly/>
																<br/>
															</div>

																											
														</div>
													</div>
													<div class="row">
														<div class="col-md-12">
															<div class="col-md-1">
																<label for="paypalTransactionDateTime"><?= lang('pay.transdatetime'); ?>:</label>
															</div>
															<div class="col-md-3">
																<input type="text" class="form-control paypalTransactionDateTime" readonly/>
																<br/>
															</div>	
															

															<div class="col-md-2">
																<label for="paypalTransactionStatus"><?= lang('pay.transstatus'); ?>:</label>
															</div>

															<div class="col-md-2">
																<input type="text" class="form-control paypalTransactionStatus" readonly/>
																<br/>
															</div>
														</div>
													</div>
												</div>
												<!-- end paypal payment method -->

												<div class="netellerPaymentMethodSection">
													<div class="row">
														<div class="col-md-12">
															
															<div class="col-md-1">
																<label for="netellerAccount">Neteller <?= lang('pay.acct'); ?>:</label>
															</div>

															<div class="col-md-3">
																<input type="text" class="form-control netellerAccount" readonly>
																<br/>
															</div>

															<div class="col-md-1">
																<label for="securedId"><?= lang('pay.securedId'); ?>:</label>
															</div>

															<div class="col-md-3">
																<input type="text" class="form-control netellerSecuredId" readonly/>
																<br/>
															</div>																										
														</div>
													</div>													
												</div>
											<!-- end payment method -->

												<hr/>
												<div class="row">
													<div class="col-md-12">
														
														<div class="col-md-1">
															<label for="depositMethodApprovedBy"><?= lang('pay.apprvby'); ?>:</label>
														</div>

														<div class="col-md-3">
															<input type="text" class="form-control" id="depositMethodApprovedBy" readonly>
															<br/>
														</div>

														<div class="col-md-1">
															<label for="depositMethodDateApproved"><?= lang('pay.datetimeapprv'); ?>:</label>
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

						<!-- Bonus Information -->
						<!-- <div class="row bonusInfoPanel">
							<div class="col-md-12">
								<div class="panel panel-primary">
									<div class="panel-heading">
										<h4 class="panel-title">
											<a href="#personal" style="color: white;" class="btn btn-info btn-sm hide_bonus_info"> 
												<i class="glyphicon glyphicon-chevron-down hide_bonus_info_up" id=""></i>
											</a> 
											Bonus Information
										</h4>
									</div>

									<div class="panel panel-body bonus_info_panel_body" id="" style="display: none;">
										<div class="row">
											<div class="col-md-12">
												<div class="col-md-2">
													<label for="promoName">Promo Name:</label>
												</div>

												<div class="col-md-3">
													<input type="text" class="form-control promoName" readonly/>
													<br/>
												</div>

												<div class="col-md-2">
													<label for="promoStartDate">Promo Period:</label>
												</div>

												<div class="col-md-3">
													<input type="text" class="form-control promoStartDate" readonly/>
													<br/>
												</div>													
											</div>
										</div>

										<div class="row">
												<div class="col-md-12">

													<div class="col-md-2">
														<label for="approvedPlayerPromoBonusAmount">Bonus Amount:</label>
													</div>

													<div class="col-md-3">
														<input type="text" class="form-control" id="approvedPlayerPromoBonusAmount"  readonly/>
														<br/>
													</div>
													<div class="col-md-1">
														<input type="text" class="form-control currentBalCurrency"  readonly/>
														<br/>
													</div>
													<input type="hidden" class="form-control playerTotalBalanceAmount" readonly/>
																																			
												</div>											
											</div>
										</div>
										
									</div>

									<div class="clearfix"></div>
									
								</div>
						</div> -->
						<!--end of Bonus Information-->

						<!-- player transaction -->
						<!-- <div class="row">
							<div class="col-md-12">
								<div class="panel panel-primary">
									<div class="panel-heading">
										<h4 class="panel-title">
											<a href="#personal" style="color: white;" id="hide_approved_deposit_player_transac" class="btn btn-info btn-sm"> 
												<i class="glyphicon glyphicon-chevron-down" id="hide_approved_depositplayer_transac_up"></i>
											</a> 
											Player Transaction History
										</h4>
									</div>

									<div class="panel panel-body" id="player_approved_deposit_transac_panel_body" style="display: none;">
										<div class="row">
											<div class="col-md-12">
												<table class="table table-striped table-hover">
													<thead>
														<tr>																		
															<th>Transaction</th>
															<th>Amount</th>
															<th>Currency</th>		
															<th>Date/Time Request</th>
															<th>Payment Method</th>																				
															<th>Status</th>
															<th>Processed By</th>
															<th>Processed On</th>
														</tr>
													</thead>

													<tbody class="transactionHistoryResult">
														
													</tbody>
												</table>
											</div>
										</div>
									</div>

									<div class="clearfix"></div>
								</div>
							</div>
						</div> -->
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
						<a class='notificationRefreshList' href="<?= BASEURL . 'payment_management/refreshList/thirdPartyDepositDeclined' ?>">
							<button type="button" class="close"><span aria-hidden="true">×</span><span class="sr-only"><?= lang('lang.close'); ?></span></button>
						</a>
						<h4 class="modal-title" id="myModalLabel"><i class="icon-loop2"></i>&nbsp;<?= lang('pay.decldepdetls'); ?></h4>
					</div>

					<div class="modal-body">					
						<!-- Deposit transaction -->
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-primary">
									<div class="panel-heading">
										<h4 class="panel-title">
											<?= lang('pay.decldepinfo'); ?>
											<a href="#depositInformation" 
              id="hide_declined_deposit_info" class="btn btn-default btn-sm pull-right"> 
												<i class="glyphicon glyphicon-chevron-down" id="hide_declined_deposit_info_up"></i>
											</a>
											<div class="clearfix"></div>
										</h4>
									</div>

									<div class="panel panel-body" id="declined_deposit_info_panel_body" style="display: none;">
										<div class="row">
												<div class="row">
													<div class="col-md-12">
														<div class="col-md-3">
															<label for="userName"><?= lang("pay.user") . ' ' . lang("pay.name"); ?>:</label>
															<input type="text" class="form-control userName" readonly/>
														</div>

														<div class="col-md-3">
															<label for="playerName"><?= lang("pay.realname"); ?>:</label>
															<input type="text" class="form-control playerName" readonly/>
														</div>

														<div class="col-md-3">
															<label for="playerLevel"><?= lang('pay.playerlev'); ?>:</label>
															<input type="text" class="form-control playerLevel" id="playerLevelDeclined" readonly/>
														</div>

														<div class="col-md-3">
															<label for="memberSince"><?= lang('pay.memsince'); ?>: </label>
															<input type="text" class="form-control memberSince" readonly>
														</div>																								
													</div>
												</div>
												<br/>
												<div class="row">
													<div class="col-md-12">													
														<div class="col-md-3">
															<label for="depositedAmount"><?= lang('pay.deposit') . ' ' . lang('pay.amt'); ?>:</label>
															<input type="text" class="form-control depositedAmount" readonly/>
														</div>

														<div class="col-md-3">
															<label for="depositMethodApprovedDeposit"><?= lang('pay.depmethod'); ?>:</label>
															<input type="text" class="form-control depositMethod" readonly/>
														</div>

														<div class="col-md-3">
															<label for="dateDepositedApprovedDeposit"><?= lang('lang.date') . ' ' . lang('pay.deposited'); ?>:</label>
															<input type="text" class="form-control dateDeposited" readonly/>
														</div>

														<div class="col-md-3">
															<label for="ipLoc"><?= lang('pay.deposit') . ' ' . lang('pay.ip') . ' ' . lang('lang.and') . ' ' . lang('pay.locatn'); ?></label>
															<input type="text" class="form-control ipLoc" readonly/>
														</div>
													</div>
												</div>
												<!-- start bonus info -->
												<div class="row bonusInfoPanel">
													<div class="col-md-12">
														<hr/>
														<h4><?= lang('pay.bonapplictn'); ?></h4>
														<hr/>
														<div class="col-md-3">
															<label for="promoName"><?= lang('lang.promo') . ' ' . lang('pay.name'); ?>:</label>
															<input type="text" class="form-control promoName" readonly/>
															<input type="hidden" class="form-control playerDepositPromoId" readonly/>
															<br/>
														</div>

														<div class="col-md-3">
															<label for="playerPromoBonusAmount"><?= lang('pay.compbonamt'); ?>:</label>
															<input type="text" class="form-control" id="declinedPlayerPromoBonusAmount" readonly/>
															<br/>
														</div>													
													</div>
												</div>
												<!-- end bonus info -->
										</div>

										<!-- start payment method -->
											<hr/>
											<h4><?= lang('pay.paytmethdetls'); ?></h4>
											<hr/>
												<!-- start paypal payment method -->
												<div class="paypalPaymentMethodSection">
													<div class="row">
														<div class="col-md-12">															
															<div class="col-md-1">
																<label for="paypalAccountName"><?= lang('pay.acctname'); ?>:</label>
															</div>

															<div class="col-md-3">
																<input type="text" class="form-control paypalAccountName" readonly>
																<br/>
															</div>

															<div class="col-md-2">
																<label for="paypalEmail"><?= lang('lang.email'); ?>:</label>
															</div>

															<div class="col-md-3">
																<input type="text" class="form-control paypalEmail" readonly/>
																<br/>
															</div>																										
														</div>
													</div>

													<div class="row">
														<div class="col-md-12">
															
															<div class="col-md-1">
																<label for="paypalTransactionId"><?= lang('pay.transid'); ?>:</label>
															</div>

															<div class="col-md-3">
																<input type="text" class="form-control paypalTransactionId" readonly>
																<br/>
															</div>

															<div class="col-md-2">
																<label for="paypalSecureMerchantAccountId"><?= lang('pay.secmercacctid'); ?>:</label>
															</div>

															<div class="col-md-3">
																<input type="text" class="form-control paypalSecureMerchantAccountId" readonly/>
																<br/>
															</div>

																											
														</div>
													</div>
													<div class="row">
														<div class="col-md-12">
															<div class="col-md-1">
																<label for="paypalTransactionDateTime"><?= lang('pay.transdatetime'); ?>:</label>
															</div>
															<div class="col-md-3">
																<input type="text" class="form-control paypalTransactionDateTime" readonly/>
																<br/>
															</div>	
															

															<div class="col-md-2">
																<label for="paypalTransactionStatus"><?= lang('pay.transstatus'); ?>:</label>
															</div>

															<div class="col-md-2">
																<input type="text" class="form-control paypalTransactionStatus" readonly/>
																<br/>
															</div>
														</div>
													</div>
												</div>
												<!-- end paypal payment method -->
												
												<div class="netellerPaymentMethodSection">
													<div class="row">
														<div class="col-md-12">
															
															<div class="col-md-1">
																<label for="netellerAccount">Neteller <?= lang('pay.acct'); ?>:</label>
															</div>

															<div class="col-md-3">
																<input type="text" class="form-control netellerAccount" readonly>
																<br/>
															</div>

															<div class="col-md-1">
																<label for="securedId"><?= lang('pay.securedId'); ?>:</label>
															</div>

															<div class="col-md-3">
																<input type="text" class="form-control netellerSecuredId" readonly/>
																<br/>
															</div>																										
														</div>
													</div>													
												</div>
											<!-- end payment method -->


										<div class="row">
											<hr/>
											<div class="col-md-12">
												
												<div class="col-md-1">
													<label for="depositMethodDeclinedBy"><?= lang('pay.declby'); ?>:</label>
												</div>

												<div class="col-md-3">
													<input type="text" class="form-control" id="depositMethodDeclinedBy" readonly>
													<br/>
												</div>

												<div class="col-md-1">
													<label for="depositMethodDateDeclined"><?= lang('pay.datetimedecl'); ?>:</label>
												</div>

												<div class="col-md-3">
													<input type="text" class="form-control" id="depositMethodDateDeclined" readonly>
													<br/>
												</div>
												<div class="clearfix"></div>
												
													<div class="col-md-1">
														<label for="depositMethodReasonDeclined"><?= lang('pay.reason'); ?>:</label>
													</div>

													<div class="col-md-6">
														<textarea class="form-control" id="depositMethodReasonDeclined" readonly></textarea>
														<br/>
													</div>
												
											</div>
										</div>

										<div class="row">
											<hr/>
											<div id="playerDeclinedDetails"></div>
											<div class="col-md-12" id="playerDeclinedDetailsRepondBtn">
												<input type="hidden" class="form-control walletAccountIdVal" readonly/>
												<input type="hidden" class="form-control" id="declinedPlayerPromoIdVal" readonly/>
												<button class="btn-md btn-info" onclick="PaymentManagementProcess.depositDeclinedToApprove(<?= $depositRequest['walletAccountId'] ?>)"><?= lang('pay.changetoapprv'); ?></button>							
											</div>
											<!--<hr/>
											 <div class="col-md-12" id="declineReason-sec">
												<p>Please Add Declined Reason:</p>
												<textarea cols="50" rows="5" id="declinedReasonTxt"></textarea><br/><br/>
												<button class="btn-md btn-info" onclick="PaymentManagementProcess.respondToDepositDeclined(<?= $depositRequest['walletAccountId'] ?>)">Submit</button>
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

<script type="text/javascript">
    $(document).ready(function(){
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
            "order": [ 1, 'asc' ]
        });
    });
</script>
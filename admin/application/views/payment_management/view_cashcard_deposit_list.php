<!-- start date picker range api -->
<div class="well" style="overflow: auto">

	<!-- start dashboard notification -->			         
	<a class='notificationRefreshList' href="<?= BASEURL . 'payment_management/refreshList/depositCashcardRequest' ?>">
		<div class="col-md-2 notificationDashboard hover-shadow <?= $this->session->userdata('dwStatus') == 'request' ? 'notDboard-active' : ''?>" id="notificationDashboard-request-deposit">
			<?= lang('pay.total'); ?><br/><span class="notificationDashboardTxt" id="notificationDashboard-request-deposit"><?= $deposit_request_cnt ?></span><br/>
			<span class="notificationDashboardLabel" id="notificationDashboard-request-deposit"><?= lang('pay.depreq'); ?></span>
		</div>
	</a>
	<a class='notificationRefreshList' href="<?= BASEURL . 'payment_management/refreshList/depositCashcardApproved' ?>">
		<div class="col-md-2 notificationDashboard hover-shadow <?= $this->session->userdata('dwStatus') == 'approved' ? 'notDboard-active' : ''?>" id="notificationDashboard-approved-deposit">
			<?= lang('pay.total'); ?><br/><span class="notificationDashboardTxt" id="notificationDashboard-approved-deposit"><?= $deposit_approved_cnt ?></span><br/>
			<span class="notificationDashboardLabel" id="notificationDashboard-approved-deposit"><?= lang('pay.appreq'); ?></span>
		</div>
	</a>
	<a class='notificationRefreshList' href="<?= BASEURL . 'payment_management/refreshList/depositCashcardDeclined' ?>">
		<div class="col-md-2 notificationDashboard hover-shadow <?= $this->session->userdata('dwStatus') == 'declined' ? 'notDboard-active' : ''?>" id="notificationDashboard-declined-deposit">
			<?= lang('pay.total'); ?><br/><span class="notificationDashboardTxt" id="notificationDashboard-declined-deposit"><?= $deposit_declined_cnt ?></span><br/>
			<span class="notificationDashboardLabel" id="notificationDashboard-declined-deposit"><?= lang('pay.decreq'); ?></span>
		</div>
	</a>
	<!-- end dashboard notification -->

	<!-- start sort dw list -->
	<form action="<?= BASEURL . 'payment_management/sortDepositWithdrawalList/cashcard' ?>" method="post" role="form">

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
            
            <br/><br/>
           <!-- <span class="pull-right" id="moreFilterBtn"><a class="moreFilter-btn">[Show Filter]</a></span> -->
       </div>      
	    <!-- end sort dw list -->
</div> 
<!-- end date picker range api -->

<!-- start more filter -->
<div class="well col-md-12 pull-right" id="moreFilter">                                             
   			<div class="col-md-4"> 
		          <label><?= lang('pay.playgroup'); ?></label>					        					          
				  <select name="playerLevel" id="paymentReportSortByPlayerLevel" class="form-control">
                    <option value="" <?= $this->session->userdata('paymentReportSortByPlayerLevel') == '' ? 'selected' : ''?>>-- <?= lang('lang.selectall'); ?> --</option>
                    <?php foreach ($vipgrouplist as $key => $value) { ?>
                        <option value="<?= $value['vipsettingId'] ?>" <?= $this->session->userdata('paymentReportSortByPlayerLevel') == $value['vipsettingId'] ? 'selected' : ''?>><?= $value['groupName'] ?></option>
                    <?php } ?>
                   </select>
		    </div>
		    <div class="col-md-3">	
		    		<label><?= lang('pay.itemcount'); ?></label>							
           			<select name="itemCnt" class="form-control">			               						               				
							<option value="5" <?= $this->session->userdata('itemCnt') == '5' ? 'selected' : ''?>>5</option>
							<option value="10"<?= $this->session->userdata('itemCnt') == '10' ? 'selected' : ''?>>10</option>
							<option value="50" <?= $this->session->userdata('itemCnt') == '50' ? 'selected' : ''?>>50</option>
							<option value="100" <?= $this->session->userdata('itemCnt') == '100' ? 'selected' : ''?>>100</option>
					</select>
			</div>
			<div class="col-md-1">
				<br/>							
	            <input type="submit" class="btn btn-sm btn-primary" value="<?= lang('lang.submit'); ?>" />				        
           	</div>
</div>
<!-- end more filter -->
</form>

<div class="row">
<!-- start request list -->
<div class="col-md-12" id="toggleView">
<div class="col-md-5"></div>	
<div class="panel panel-primary">
<div class="panel-heading">
	<div class="col-md-8 pull-left">
		<h4 class="panel-title "><i class="glyphicon glyphicon-list-alt"></i> <?= $transactionType ?><span class="choosenDateRange">&nbsp;<?= isset($choosenDateRange) ? ($choosenDateRange) : '' ?></span></h4>
	</div>					
	<div class="col-md-4"></div>
	<div class="clearfix"></div>
</div>

<!-- start data table -->
<div class="panel panel-body" id="player_panel_body">
	<div id="paymentList" class="table-responsive">
		<table class="table table-striped table-hover tablepress table-condensed" id="myTable">
			<thead>
				<tr>
					<th class="tableHeaderFont"><?= lang('system.word38'); ?></th>
					<th class="tableHeaderFont"><?= lang('pay.realname'); ?></th>
					<th class="tableHeaderFont"><?= lang('pay.playerlev'); ?></th>
					<th class="tableHeaderFont"><?= lang('pay.amt'); ?></th>
					<th class="tableHeaderFont"><?= lang('pay.sn'); ?></th>
					<th class="tableHeaderFont"><?= lang('pay.pin'); ?></th>
					<th class="tableHeaderFont"><?= lang('pay.curr'); ?></th>
					<th class="tableHeaderFont"><?= lang('pay.reqtime'); ?></th>
					
					<th class="tableHeaderFont"><?= lang('pay.promoname'); ?></th>
					<th class="tableHeaderFont"><?= lang('pay.promobonus'); ?></th>
					<th class="tableHeaderFont"><?= lang('pay.deposit') . ' ' . lang('pay.ip'); ?></th>
					<th class="tableHeaderFont"><?= lang('pay.deposit') . ' ' . lang('pay.locatn'); ?></th>
					
					<?php if($this->session->userdata('dwStatus') == 'approved' || $this->session->userdata('dwStatus') == 'declined'){ ?>
						<th class="tableHeaderFont"><?= lang('pay.procssby'); ?></th>
						<th class="tableHeaderFont"><?= lang('pay.procsson'); ?></th>
					<?php	} ?>
					<th class="tableHeaderFont"><?= lang('lang.action'); ?></th>
				</tr>
			</thead>

			<tbody>
				<?php //var_dump($depositRequest);
                    $atts_popup = array(
                                      'width'      => '1030',
                                      'height'     => '600',
                                      'scrollbars' => 'yes',
                                      'status'     => 'yes',
                                      'resizable'  => 'no',
                                      'screenx'    => '0',
                                      'screeny'    => '0');

					if(!empty($depositRequest)) {
						foreach($depositRequest as $depositRequest) {
				?>											
							<tr>
								<td><?= $depositRequest['playerName'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : ucwords($depositRequest['playerName']) ?></td>																								
								<td><?= $depositRequest['firstName'] == '' ? '<i class="help-block">'. lang("lang.norecyet") .'<i/>' : ucwords($depositRequest['firstName']).' '.ucwords($depositRequest['firstName']) ?></td>											
								<td><?= $depositRequest['groupName'].' '.$depositRequest['vipLevel'] ?></td>
								<td><?= $depositRequest['amount'] == '' ? '<i class="help-block">'. lang("lang.norecord") .'<i/>' : $depositRequest['amount'] ?></td>
								<td><?= $depositRequest['cashcard_sn'] == '' ? '<i class="help-block">'. lang("lang.norecord") .'<i/>' : $depositRequest['cashcard_sn'] ?></td>
								<td><?= $depositRequest['cashcard_pin'] == '' ? '<i class="help-block">'. lang("lang.norecord") .'<i/>' : $depositRequest['cashcard_pin'] ?></td>
								<td><?= $depositRequest['currency'] == '' ? '<i class="help-block">'. lang("lang.norecord") .'<i/>' : $depositRequest['currency'] ?></td>
								<td><?= $depositRequest['dwDateTime'] == '' ? '<i class="help-block">'. lang("lang.norecord") .'<i/>' : mdate('%M %d, %Y %H:%i:%s',strtotime($depositRequest['dwDateTime'])) ?></td>
								<td><?= $depositRequest['promoName'] == '' ? '<i class="help-block">'. lang("lang.no") . " " . lang("lang.promo") .'<i/>' : $depositRequest['promoName'] ?></td>
								<td><?= $depositRequest['bonusAmount'] == '' ? '<i class="help-block">'. lang("lang.no") . " " . lang("lang.bonus") .'<i/>' : $depositRequest['bonusAmount'] ?></td>
								<td><?= $depositRequest['dwIp'] == '' ? '<i class="help-block">'. lang("lang.no") . " " . lang("lang.bonus") .'<i/>' : $depositRequest['dwIp'] ?></td>
								<td><?= $depositRequest['dwLocation'] == ',' ? '<i class="help-block">'. lang("lang.norecord") .'<i/>' : $depositRequest['dwLocation'] ?></td>
								<?php if($depositRequest['dwStatus'] == 'approved' || $depositRequest['dwStatus'] == 'declined'){ ?>															
											<td><?= $depositRequest['processedByAdmin'] == '' ? '<i class="help-block">'. lang("lang.norecord") .'<i/>' : ucwords($depositRequest['processedByAdmin']) ?></td>
											<td><?= $depositRequest['processDatetime'] == '' ? '<i class="help-block">'. lang("lang.norecord") .'<i/>' : mdate('%M %d, %Y %H:%i:%s',strtotime($depositRequest['processDatetime'])) ?></td>
								<?php	} ?>
								<td>
								<?php if($depositRequest['dwStatus'] == 'approved'){ ?>
									<span class="btn-sm btn-info review-btn" onclick="PaymentManagementProcess.getDepositApprovedLocalBank(<?= $depositRequest['walletAccountId'] ?>,<?= $depositRequest['dwMethod'] ?>)" data-toggle="modal" data-target="#approvedDetailsModal">
										<?= lang("lang.details"); ?>
									</span>

								<?php }elseif($depositRequest['dwStatus'] == 'declined'){ ?>										
									<span class="btn-sm btn-info review-btn" onclick="PaymentManagementProcess.getDepositDeclined(<?= $depositRequest['walletAccountId'] ?>,<?= $depositRequest['dwMethod'] ?>)" data-toggle="modal" data-target="#declinedDetailsModal">
										<?= lang("lang.details"); ?>
									</span>

								<?php }else{ ?>
									<span class="btn-sm btn-info review-btn" onclick="PaymentManagementProcess.getDepositRequest(<?= $depositRequest['walletAccountId'] ?>,<?= $depositRequest['dwMethod'] ?>)" data-toggle="modal" data-target="#requestDetailsModal">
										<?= lang("lang.details"); ?>
									</span>
								<?php } ?>
								</td>
							</tr>
				<?php 		
						}
					}
					else{ ?>
							<tr>
								<td colspan="11" style="text-align:center"><?= lang("lang.norec"); ?>
								</td>
							</tr>
				<?php	}
				?>
			</tbody>
		</table>

		<div class="panel-footer">
			<ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
		</div>
	</div>
</div>
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
			<a class='notificationRefreshList' href="<?= BASEURL . 'payment_management/refreshList/depositRequest' ?>">
				<button type="button" class="close"><span aria-hidden="true">×</span><span class="sr-only"><?= lang("lang.close"); ?></span></button>
			</a>
			<h4 class="modal-title" id="myModalLabel"><i class="glyphicon glyphicon-list-alt"></i>&nbsp;<?= lang("pay.depreq") . ' ' . lang("lang.details"); ?></h4>
		</div>

		<div class="modal-body">						
			<div class="col-md-12" id="checkPlayer">
				<!-- Deposit transaction -->
				<div class="row">
					<div class="col-md-12">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a href="#personal" style="color: white;" id="hide_deposit_info" class="btn btn-info btn-sm"> 
										<i class="glyphicon glyphicon-chevron-down" id="hide_deposit_info_up"></i>
									</a> 
									<?= lang("pay.deposit") . ' ' . lang("lang.info"); ?>
								</h4>
							</div>

							<div class="panel panel-body" id="deposit_info_panel_body" style="display: none;">
								<div class="row">
									<div class="col-md-12">
										<div class="col-md-3">
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

								<!-- start payment method -->
								<hr/>
								<h4><?= lang('pay.paytmethdetls'); ?></h4>
								<hr/>

									<!-- start otc payment method -->
									<div class="otcPaymentMethodSection">
										<div class="row">
											<div class="col-md-12">

												<div class="col-md-3">
													<label for="otcBankName"><?= lang('pay.depbankname'); ?>:</label>
													<input type="text" class="form-control depotcBankName" readonly/>
													<br/>
												</div>

												<div class="col-md-3">
													<label for="otcAccountName"><?= lang('pay.depacctname'); ?>:</label>
													<input type="text" class="form-control depotcAccountName" readonly>
													<br/>
												</div>

												<div class="col-md-3">
													<label for="otcAccountNo"><?= lang('pay.depacctnumber'); ?>:</label>
													<input type="text" class="form-control depotcAccountNo" readonly>
													<br/>
												</div>	

												<div class="col-md-3">
													<label for="otcAccountBranch"><?= lang('pay.depbranchname'); ?>:</label>
													<input type="text" class="form-control depotcBranchName" readonly>
													<br/>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-12">
												<div class="col-md-3">
													<label for="otcBankName"><?= lang('pay.bankname'); ?>:</label>
													<input type="text" class="form-control otcBankName" readonly/>
													<br/>
												</div>

												<div class="col-md-3">
													<label for="otcAccountName"><?= lang('pay.acctname'); ?>:</label>
													<input type="text" class="form-control otcAccountName" readonly>
													<br/>
												</div>

												<div class="col-md-3">
													<label for="otcAccountNo"><?= lang('pay.acctnumber'); ?>:</label>
													<input type="text" class="form-control otcAccountNo" readonly>
													<br/>
												</div>	

												<div class="col-md-3">
													<label for="otcAccountNo"><?= lang('pay.depbanktype'); ?>:</label>
													<input type="text" class="form-control depLocBankType" readonly>
													<br/>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-12">

												<div class="col-md-3">
													<label for="otcTransacTime"><?= lang('pay.transdatetime'); ?>:</label>
													<input type="text" class="form-control otcTransacTime" readonly>
													<br/>
												</div>

												<div class="col-md-3">
													<label for="otcReferenceNo"><?= lang('pay.transrefnumber'); ?>:</label>
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
									<div class="col-md-12" id="playerRequestDetails"></div>
									<div class="col-md-12 pull-right" id="repondBtn">													
										<input type="hidden" class="form-control walletAccountIdVal" readonly/>
										<input type="hidden" class="form-control" id="requestPlayerPromoIdVal" readonly/>
										<button class="btn-md btn-info" onclick="PaymentManagementProcess.respondToDepositRequest()"><?= lang('lang.approve'); ?></button>
										<button class="btn-md btn-info" onclick="PaymentManagementProcess.showDeclineReason()"><?= lang('lang.decline'); ?></button>							
									</div>
									<div class="col-md-5" id="declineReason-sec">
										<p><?= lang('pay.plsadddeclreason'); ?>:</p>
										<textarea class="form-control" cols="50" rows="5" id="declinedReasonTxt"></textarea><br/>
										<input type="checkbox" name="showDeclinedReason_cbx" id="showDeclinedReason_cbx"> <?= lang('pay.showtoplayr'); ?><br/><br/>
										<button class="btn-md btn-info" onclick="PaymentManagementProcess.respondToDepositDeclined(<?= $depositRequest['walletAccountId'] ?>)"><?= lang('pay.declnow'); ?></button>
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
			<a class='notificationRefreshList' href="<?= BASEURL . 'payment_management/refreshList/depositApproved' ?>">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only"><?= lang('lang.close'); ?></span></button>
			</a>
			<h4 class="modal-title" id="myModalLabel"><i class="glyphicon glyphicon-list-alt"></i>&nbsp;<?= lang('pay.appdepodetals'); ?></h4>
		</div>

		<div class="modal-body">

			<!-- player transaction -->
			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a href="#approvedDeposit" style="color: white;" id="hide_approved_deposit_transac" class="btn btn-info btn-sm"> 
									<i class="glyphicon glyphicon-chevron-down" id="hide_approved_deposit_transac_up"></i>
								</a> 
								<?= lang('pay.appdepoinfo'); ?>
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
												<label for="ipLoc"><?= lang('pay.deposit') . ' ' . lang('pay.ip') . ' ' . lang('lang.and') . ' ' . lang('pay.locatn') ?></label>
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

								<!-- start otc payment method -->
								<div class="otcPaymentMethodSection">
									<div class="row">
										<div class="col-md-12">

											<div class="col-md-3">
												<label for="otcBankName"><?= lang('pay.depbankname'); ?>:</label>
												<input type="text" class="form-control depotcBankName" readonly/>
												<br/>
											</div>

											<div class="col-md-3">
												<label for="otcAccountName"><?= lang('pay.depacctname'); ?>:</label>
												<input type="text" class="form-control depotcAccountName" readonly>
												<br/>
											</div>

											<div class="col-md-3">
												<label for="otcAccountNo"><?= lang('pay.depacctnumber'); ?>:</label>
												<input type="text" class="form-control depotcAccountNo" readonly>
												<br/>
											</div>	

											<div class="col-md-3">
												<label for="otcAccountBranch"><?= lang('pay.depbranchname'); ?>:</label>
												<input type="text" class="form-control depotcBranchName" readonly>
												<br/>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											
											<div class="col-md-3">
												<label for="otcBankName"><?= lang('pay.bankname'); ?>:</label>
												<input type="text" class="form-control otcBankName" readonly/>
												<br/>
											</div>

											<div class="col-md-3">
												<label for="otcAccountName"><?= lang('pay.acctname'); ?>:</label>
												<input type="text" class="form-control otcAccountName" readonly>
												<br/>
											</div>

											<div class="col-md-3">
												<label for="otcAccountNo"><?= lang('pay.acctnumber'); ?>:</label>
												<input type="text" class="form-control otcAccountNo" readonly>
												<br/>
											</div>	

											<div class="col-md-3">
												<label for="otcAccountNo"><?= lang('pay.depbanktype'); ?>:</label>
												<input type="text" class="form-control depLocBankType" readonly>
												<br/>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">

											<div class="col-md-3">
												<label for="otcTransacTime"><?= lang('pay.transdatetime'); ?>:</label>
												<input type="text" class="form-control otcTransacTime" readonly>
												<br/>
											</div>

											<div class="col-md-3">
												<label for="otcReferenceNo"><?= lang('pay.transrefnumber'); ?>:</label>
												<input type="text" class="form-control otcReferenceNo" readonly>
												<br/>
											</div>
										</div>
									</div>
								</div>
								<!-- end otc payment method -->
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
			<a class='notificationRefreshList' href="<?= BASEURL . 'payment_management/refreshList/depositDeclined' ?>">
				<button type="button" class="close"><span aria-hidden="true">×</span><span class="sr-only"><?= lang('lang.close'); ?></span></button>
			</a>
			<h4 class="modal-title" id="myModalLabel"><i class="glyphicon glyphicon-list-alt"></i>&nbsp;<?= lang('pay.decldepdetls'); ?></h4>
		</div>

		<div class="modal-body">					
			<!-- Deposit transaction -->
			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a href="#depositInformation" style="color: white;" id="hide_declined_deposit_info" class="btn btn-info btn-sm"> 
									<i class="glyphicon glyphicon-chevron-down" id="hide_declined_deposit_info_up"></i>
								</a> 
								<?= lang('pay.decldepinfo'); ?>
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

									<!-- start otc payment method -->
									<div class="otcPaymentMethodSection">
										<div class="row">
											<div class="col-md-12">

												<div class="col-md-3">
													<label for="otcBankName"><?= lang('pay.depbankname'); ?>:</label>
													<input type="text" class="form-control depotcBankName" readonly/>
													<br/>
												</div>

												<div class="col-md-3">
													<label for="otcAccountName"><?= lang('pay.depacctname'); ?>:</label>
													<input type="text" class="form-control depotcAccountName" readonly>
													<br/>
												</div>

												<div class="col-md-3">
													<label for="otcAccountNo"><?= lang('pay.depacctnumber'); ?>:</label>
													<input type="text" class="form-control depotcAccountNo" readonly>
													<br/>
												</div>	

												<div class="col-md-3">
													<label for="otcAccountBranch"><?= lang('pay.depbranchname'); ?>:</label>
													<input type="text" class="form-control depotcBranchName" readonly>
													<br/>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-12">
												
												<div class="col-md-3">
													<label for="otcBankName"><?= lang('pay.bankname'); ?>:</label>
													<input type="text" class="form-control otcBankName" readonly/>
													<br/>
												</div>

												<div class="col-md-3">
													<label for="otcAccountName"><?= lang('pay.acctname'); ?>:</label>
													<input type="text" class="form-control otcAccountName" readonly>
													<br/>
															</div>

															<div class="col-md-3">
																<label for="otcAccountNo"><?= lang('pay.acctnumber'); ?>:</label>
																<input type="text" class="form-control otcAccountNo" readonly>
																<br/>
															</div>	

															<div class="col-md-3">
																<label for="otcAccountNo"><?= lang('pay.depbanktype'); ?>:</label>
																<input type="text" class="form-control depLocBankType" readonly>
																<br/>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-md-12">

															<div class="col-md-3">
																<label for="otcTransacTime"><?= lang('pay.transdatetime'); ?>:</label>
																<input type="text" class="form-control otcTransacTime" readonly>
																<br/>
															</div>

															<div class="col-md-3">
																<label for="otcReferenceNo"><?= lang('pay.transrefnumber'); ?>:</label>
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

										</div>
									</div>

									<div class="clearfix"></div>
									</div>
								</div>
						</div>
						<!--end of Deposit transaction-->

						<!-- Bonus Information -->
						<div class="row bonusInfoPanel">
							<div class="col-md-12">
								<div class="panel panel-primary">
									<div class="panel-heading">
										<h4 class="panel-title">
											<a href="#personal" style="color: white;" class="btn btn-info btn-sm hide_bonus_info"> 
												<i class="glyphicon glyphicon-chevron-down hide_bonus_info_up" id=""></i>
											</a> 
											<?= lang('pay.bonusinfo'); ?>
										</h4>
									</div>

									<div class="panel panel-body bonus_info_panel_body" id="" style="display: none;">
										<div class="row">
											<div class="col-md-12">
												<div class="col-md-2">
													<label for="promoName"><?= lang('lang.promo') . ' ' . lang('pay.name'); ?>:</label>
												</div>

												<div class="col-md-3">
													<input type="text" class="form-control promoName" readonly/>
													<br/>
												</div>

												<div class="col-md-2">
													<label for="promoStartDate"><?= lang('pay.promoperiod'); ?>:</label>
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
														<label for="declinedPlayerPromoBonusAmount"><?= lang('pay.bonusamt'); ?>:</label>
													</div>

													<div class="col-md-3">
														<input type="text" class="form-control" id="declinedPlayerPromoBonusAmount"  readonly/>
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
						</div>
						<!--end of Bonus Information-->
						
						<!-- player transaction history -->
						<!-- <div class="row">
							<div class="col-md-12">
								<div class="panel panel-primary">
									<div class="panel-heading">
										<h4 class="panel-title">
											<a href="#personal" style="color: white;" id="hide_declined_deposit_player_transac" class="btn btn-info btn-sm"> 
												<i class="glyphicon glyphicon-chevron-down" id="hide_declined_depositplayer_transac_up"></i>
											</a> 
											Player Transaction History
										</h4>
									</div>

									<div class="panel panel-body" id="player_declined_deposit_transac_panel_body" style="display: none;">
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
															<th>Declined By</th>
															<th>Declined On</th>
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
	<!-- end declinedDetailsModal-->

		
</div>
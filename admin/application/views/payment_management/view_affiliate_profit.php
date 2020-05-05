<div class="row">
	<div class="col-md-12" id="toggleView">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title pull-left"><i class="glyphicon glyphicon-list-alt"></i> <?= lang('pay.affproftlst'); ?></h4>
				<!-- <a href="#" class="btn btn-primary btn-sm pull-right" id="view_affiliate"><span class="glyphicon glyphicon-remove"></span></a> -->
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body" id="affiliate_panel_body">
				<table class="table table-striped table-hover">
					<thead>
						<tr>
							<th><?= lang('pay.Username'); ?></th>
							<th><?= lang('pay.paytoption'); ?></th>
							<th><?= lang('pay.paymethod'); ?></th>
							<th><?= lang('pay.curr'); ?></th>
							<th><?= lang('pay.amt'); ?></th>
							<th><?= lang('lang.date'); ?></th>
							<th><?= lang('lang.action'); ?></th>
						</tr>
					</thead>

					<tbody>
						<?php 
						if(!empty($payments)) {
							foreach ($payments as $value) { 
								$date = new DateTime($value['date']); 

								if($value['status'] == 0) {
						?>
									<tr class="">
						<?php 
								} else if($value['status'] == 1) {
						?>
									<tr class="warning">
						<?php
								} else if($value['status'] == 3) {
						?>
									<tr class="danger">
						<?php
								}
						?>

								<td><?= $value['username'] ?></td>
								<td><?= $value['period'] ?></td>
								<td><?= $value['paymentMethod'] ?></td>
								<td><?= $value['currency'] ?></td>
								<td><?= $value['amount'] ?></td>
								<td><?= date_format($date, 'Y-m-d') ?></td>
								<td>
									<span class="btn-sm btn-info review-btn" onclick="PaymentManagementProcess.getAffiliateRequest(<?= $value['affiliateId']?>, <?= $value['affiliatePaymentHistoryId']?>)" data-toggle="modal" data-target="#requestDetailsModal">
										<?= lang('pay.chckreq'); ?>
									</span>
								</td>
							</tr>
						<?php } 

						}else{ ?>
									<tr>
										<td colspan="7" style="text-align:center"><?= lang('lang.norec'); ?>
										</td>
									</tr>
						<?php }
						?>
					</tbody>
				</table>

				<br/>

				<div class="col-md-12 col-offset-0">
				    <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul> 
				</div>
			</div>

			<div class="panel-footer">

			</div>
		</div>
	</div>
</div>

<!-- start requestDetailsModal-->
<div class="row">
	<div class="modal fade" id="requestDetailsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content modal-content-three">
				<div class="modal-header">
					<a class='notificationRefreshList' href="<?= BASEURL . 'payment_management/viewAffiliateProfit' ?>">
						<button type="button" class="close"><span aria-hidden="true">×</span><span class="sr-only"><?= lang('lang.close'); ?></span></button>
					</a>
					<h4 class="modal-title" id="myModalLabel"><?= lang('pay.affreqdetls'); ?></h4>
				</div>

				<div class="modal-body">
					<div id="affiliateRequestDetails">
					</div>
					
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
												<?= lang('pay.bankinfo'); ?>
											</h4>
										</div>

										<div class="panel panel-body" id="deposit_info_panel_body" style="display: none;">
											<div class="row">
												<div class="col-md-12">
													<div class="col-md-1">
														<label for="affiliateName"><?= lang('pay.name'); ?>:</label>
													</div>

													<div class="col-md-3">
														<input type="text" class="form-control" id="affiliateName" readonly>
														<br/>
													</div>

													<div class="col-md-1">
														<label for="currency"><?= lang('pay.curr'); ?>:</label>
													</div>

													<div class="col-md-3">
														<input type="text" class="form-control" id="currency" readonly>
														<br/>
													</div>
													
													<div class="col-md-1">
														<label for="amount"><?= lang('pay.amt'); ?>:</label>
													</div>

													<div class="col-md-3">
														<input type="text" class="form-control" id="amount" readonly>
														<br/>
													</div>
												</div>
											</div>

											<div class="row">
												<div class="col-md-12">
													<div class="col-md-1">
														<label for="period"><?= lang('pay.paytoption'); ?>:</label>
													</div>

													<div class="col-md-3">
														<input type="text" class="form-control" id="period" readonly>
														<br/>
													</div>
												</div>
											</div>

											<hr>

											<div class="row">
												<div class="col-md-12">
													<div class="col-md-1">
														<label for="bank_name"><?= lang('pay.bankname'); ?>:</label>
													</div>

													<div class="col-md-3">
														<input type="text" class="form-control" id="bank_name" readonly>
														<br/>
													</div>

													<div class="col-md-1">
														<label for="account_name"><?= lang('pay.acctname'); ?>:</label>
													</div>

													<div class="col-md-3">
														<input type="text" class="form-control" id="account_name" readonly>
														<br/>
													</div>

													<div class="col-md-1">
														<label for="account_number"><?= lang('pay.acctnumber'); ?>:</label>
													</div>

													<div class="col-md-3">
														<input type="text" class="form-control" id="account_number" readonly>
														<br/>
													</div>
												</div>
											</div>

											<div class="row">
												<hr/>
												<div class="col-md-12 pull-right" id="repondBtn">
													<!-- <input type="hidden" value="" id="affiliate_id"/> -->
													<input type="hidden" value="" id="payment_history_id"/>
													<button class="btn-md btn-info" onclick="PaymentManagementProcess.respondToAffiliateRequest('process')" id="processing" style="display:none;"><?= lang('pay.procssng'); ?></button>
													<button class="btn-md btn-info" onclick="PaymentManagementProcess.respondToAffiliateRequest('approve')" id="approved" style="display:none;"><?= lang('lang.approve'); ?></button>
													<button class="btn-md btn-info" onclick="PaymentManagementProcess.showDeclineReason()" id="declined"><?= lang('pay.declnow'); ?></button>							
												</div>
												<div class="col-md-5" id="declineReason-sec">
													<p><?= lang('pay.plsadddeclreason'); ?>:</p>
													<textarea class="form-control" cols="50" rows="5" id="declinedReasonTxt"></textarea><br/><br/>
													<button class="btn-md btn-info" onclick="PaymentManagementProcess.respondToAffiliateDeclined()"><?= lang('lang.decline'); ?></button>
												</div>
											</div>
										</div>

										<div class="clearfix"></div>
										</div>
									</div>
							</div>
						<!--end of Deposit transaction-->

						<label><?= lang('pay.chckaff'); ?></label>
						    
						    <!-- personal info-->
							<div class="row">
								<div class="col-md-12">
									<div class="panel panel-primary">
										<div class="panel-heading">
											<h4 class="panel-title"> <a href="#personal" style="color: white;" id="hide_personal_info" class="btn btn-info btn-sm"> <i class="glyphicon glyphicon-chevron-down" id="hide_personal_info_up"></i></a> <?= lang('aff.ai01'); ?></h4>
										</div>

										<div class="panel panel-body" id="personal_info_panel_body" style="display: none;">
											<div class="row">
												<div class="col-md-1">
													<label for="completeName"><?= lang('pay.compltname'); ?>:</label>
												</div>

												<div class="col-md-3">
													<input type="text" class="form-control" id="completeName" readonly>
													<br/>
												</div>

												<div class="col-md-1">
													<label for="email"><?= lang('lang.email'); ?>: </label>
												</div>

												<div class="col-md-3">
													<input type="text" id="email" class="form-control" readonly>
													<br/>
												</div>

												<div class="col-md-1">
													<label for="memberSince"><?= lang('pay.memsince'); ?>: </label>
												</div>

												<div class="col-md-3">
													<input type="text" id="memberSince" class="form-control" readonly>
													<br/>
												</div>
											</div>
											<div class="row">
												<div class="col-md-1">
													<label for="address"><?= lang('aff.ai10'); ?>:</label>
												</div>

												<div class="col-md-4">
													<input type="text" class="form-control" id="address" readonly>
													<br/>
												</div>

												<div class="col-md-1">
													<label for="city"><?= lang('aff.ai09'); ?>:</label>
												</div>

												<div class="col-md-2">
													<input type="text" class="form-control" id="city" readonly>
													<br/>
												</div>

												<div class="col-md-1">
													<label for="country"><?= lang('aff.ai13'); ?>:</label>
												</div>

												<div class="col-md-2">
													<input type="text" class="form-control" id="country" readonly>
													<br/>
												</div>
											</div>
										</div>

										<div class="clearfix"></div>
									</div>
								</div>
							</div>
							<!-- end of personal info-->				        
					            
				            <!-- player transaction -->
							<div class="row">
								<div class="col-md-12">
									<div class="panel panel-primary">
										<div class="panel-heading">
											<h4 class="panel-title">
												<a href="#transaction" style="color: white;" id="hide_affiliate_transac" class="btn btn-info btn-sm"> 
													<i class="glyphicon glyphicon-chevron-down" id="hide_affiliate_transac_up"></i>
												</a> 
												<?= lang('pay.afftranshistry'); ?>
											</h4>
										</div>

										<div class="panel panel-body" id="affiliate_transac_panel_body" style="display: none;">
											<div class="row">
												<div class="col-md-12">
													<label for="data"><?= lang('pay.notransyet'); ?></label>
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
</div>
<!-- end requestDetailsModal-->
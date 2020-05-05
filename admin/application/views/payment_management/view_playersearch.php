		<!-- start sort dw list -->
		<form action="<?= BASEURL . 'payment_management/playerBalanceListSearch' ?>" method="post" role="form">	
			<!-- start search -->
			<div class="well" style="overflow: auto">
				<div class="pull-right daterangePicker-sec">
				   <div class="col-md-3">
				   	    <label><?= lang('lang.search') . ' ' . lang('lang.player'); ?></label>
				   	    <br/>
		               <input type="text" class="form-control" name="searchVal" id="searchVal" value="<?= $this->session->userdata('searchVal') ?>">		               
		            </div>

		            <div class="col-md-3"> 
					          <label><?= lang('pay.playgroup'); ?></label>					        					          
							  <select name="playerLevel" id="adjustBalancePlayerLevel" class="form-control">
                                <option value="" <?= $this->session->userdata('adjustBalancePlayerLevel') == '' ? 'selected' : ''?>>-- <?= lang('pay.selectlev'); ?> --</option>
                                <?php foreach ($vipgrouplist as $key => $value) { ?>
                                    <option value="<?= $value['vipsettingId'] ?>" <?= $this->session->userdata('adjustBalancePlayerLevel') == $value['vipsettingId'] ? 'selected' : ''?>><?= $value['groupName'] ?></option>
                                <?php } ?>
                                <option value="" <?= $this->session->userdata('adjustBalancePlayerLevel') == '' ? 'selected' : ''?>><?= lang('pay.all'); ?></option>
                               </select>
					    </div>

					    <div class="col-md-3">
							<label for="orderByField"><?= lang('pay.orderby'); ?></label>
							<select class="form-control" name="orderByField">
								<option value="firstname" <?= $this->session->userdata('orderByField') == 'firstname' ? 'selected' : ''?>><?= lang('aff.al14'); ?></option>
								<option value="lastname" <?= $this->session->userdata('orderByField') == 'lastname' ? 'selected' : ''?>><?= lang('aff.al15'); ?></option>
								<option value="userName" <?= $this->session->userdata('orderByField') == 'username' ? 'selected' : ''?>><?= lang('pay.username'); ?></option>
								<option value="playerLevel" <?= $this->session->userdata('orderByField') == 'playerLevel' ? 'selected' : ''?>><?= lang('pay.playerlev'); ?></option>
							</select>
						</div>

						 <div class="col-md-3">
							<label for="orderBy"><?= lang('pay.sortin'); ?></label>
							<select class="form-control" name="orderBy">
								<option value="ASC" <?= $this->session->userdata('orderBy') == 'ASC' ? 'selected' : ''?>><?= lang('sys.vu30'); ?></option>
								<option value="DESC" <?= $this->session->userdata('orderBy') == 'DESC' ? 'selected' : ''?>><?= lang('sys.vu31'); ?></option>
							</select>
						</div>

					    <div class="col-md-3">
					    		<label><?= lang('pay.itemcount'); ?></label>
		               			<select name="itemCnt" class="form-control">
										<option value="5">5</option>
										<option value="10">10</option>
										<option value="50">50</option>
										<option value="100">100</option>
								</select>
						</div>
		            <div class="col-md-3">
		            	<br/>
		            	<input type="submit" class="btn btn-primary" value="<?= lang('lang.go'); ?>" />
		            </div>
		            <br/><br/>
	               <!-- <span class="pull-right" id="moreFilterBtn"><a class="moreFilter-btn"><?= lang("pay.morefiltr"); ?></a></span> -->
                </div>
            </div>
		</form>
		<!-- end sort dw list -->
<div class="row">
	<div class="col-md-12" id="toggleView">
		<div class="col-md-5">	
		</div>	
    
		<div class="panel panel-primary">
			<div class="panel-heading">
				<div class="col-md-8 pull-left">
					<h4 class="panel-title "><i class="glyphicon glyphicon-list-alt"></i> <?= $transactionType ?></span></h4>
				</div>					

					<div class="col-md-4">
					</div>
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body" id="player_panel_body">
				<div id="paymentList" class="table-responsive">
					<table class="table table-striped table-hover" id="myTable">
						<thead>
							<tr>
								<th><?= lang('lang.player') . ' ' . lang('pay.name'); ?></th>
								<th><?= lang('pay.username'); ?></th>
								<th><?= lang('pay.playerlev'); ?></th>
								<th><?= lang('pay.mainwalltbal'); ?></th>								
								<?php foreach ($games as $game) { ?>
									<th><?= $game['game']?> <?= lang('pay.walltbal'); ?></th>
								<?php } ?>
								<!-- <th>Cashback Bonus Balance</th> -->
								<th><?= lang('pay.totalbal'); ?></th>
								<th><?= lang('pay.curr'); ?></th>
								<th><?= lang('lang.action'); ?></th>
							</tr>
						</thead>

						<tbody>
							<?php
								if(!empty($playerDetails)) {
									
									foreach($playerDetails as $playerDetails) {
										$totalBalanceAmount = 0;
        								$subwallet = $this->payment_manager->getAllPlayerAccountByPlayerId($playerDetails['playerId']);
        								$totalBalanceAmount += $playerDetails['mainwalletBalanceAmount'];
							?>			
											<tr>
												<td><?= $playerDetails['firstname'] == '' ? '<i class="help-block"><?= lang("lang.norecyet"); ?><i/>' : ucwords($playerDetails['firstname'].' '.$playerDetails['lastname']) ?></td>
												<td><?= $playerDetails['username'] == '' ? '<i class="help-block"><?= lang("lang.norecyet"); ?><i/>' : $playerDetails['username'] ?></td>
												<td><?= $playerDetails['groupName'] == '' ? '<i class="help-block"><?= lang("lang.norecyet"); ?><i/>' : $playerDetails['groupName'].' '.$playerDetails['vipLevel'] ?></td>
												<td><?= $playerDetails['mainwalletBalanceAmount'] == '' ? 0 : $playerDetails['mainwalletBalanceAmount'] ?></td>												
												<?php 
													foreach ($subwallet as $key => $subwallet) { 
														$totalBalanceAmount += $subwallet['totalBalanceAmount'];
													?>
													<td><?= $subwallet['totalBalanceAmount'] == '' ? 0 : $subwallet['totalBalanceAmount'] ?></td>
												<?php } ?>
												<!-- <td><?= $playerDetails['cashbackwalletBalanceAmount'][0]['cashbackwalletBalanceAmount'] ?></td> -->
												<td><?= $totalBalanceAmount ?></td>
												<td><?= $playerDetails['currency'] == '' ? '<i class="help-block"><?= lang("lang.norecyet"); ?><i/>' : $playerDetails['currency'] ?></td>

												<!-- <td><?= $depositRequest['currency'] == '' ? '<i class="help-block"><?= lang("lang.norecyet"); ?><i/>' : $depositRequest['currency'] ?></td>												 -->
												<td>
													<a href="<?= BASEURL. 'payment_management/viewPlayerBalanceAdjustmentForm/'.$playerDetails['playerId'] ?>" class="btn btn-sm btn-success"><?= lang("pay.adjust"); ?></a>												
												</td>
											</tr>
							<?php
									}
								}
								else{ ?>
									<tr>
										<td colspan="8" style="text-align:center"><?= lang("lang.norec"); ?></td>
									</tr>
							<?php } ?>
					</table>	

					<div class="panel-footer">
						<ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
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
							<a class='notificationRefreshList' href="<?= BASEURL . 'payment_management/refreshList/viewPlayerBalance' ?>">
								<button type="button" class="close"><span aria-hidden="true">×</span><span class="sr-only"><?= lang('lang.close'); ?></span></button>
							</a>
							<h4 class="modal-title" id="myModalLabel"><?= lang("pay.playrbaldetls"); ?></h4>
						</div>

						<div class="modal-body">													
							<div class="col-md-12" id="checkPlayer">
								<!-- personal info-->
								<div class="row">
									<div class="col-md-12">
										<div class="panel panel-primary">
											<div class="panel-heading">
												<h4 class="panel-title"> <a href="#personal" style="color: white;" id="hide_personal_info_history" class="btn btn-info btn-sm"> <i class="glyphicon glyphicon-chevron-down" id="hide_personal_info_history_up"></i></a> Personal Information</h4>
											</div>

											<div class="panel panel-body" id="personal_info_history_panel_body" style="display: none;">
												<div class="row">
													<div class="col-md-3">
														<label for="balInfoUserName"><?= lang("pay.user") . ' ' . lang("pay.name"); ?>:</label>
														<input type="text" class="form-control" id="balInfoUserName" readonly />
														<br/>
													</div>

													<div class="col-md-3">
														<label for="completeName"><?= lang("pay.realname"); ?>:</label>
														<input type="text" class="form-control" id="completeName" readonly>
														<br/>
													</div>

													<div class="col-md-3">
														<label for="balInfoPlayerLevel"><?= lang('pay.playerlev'); ?>:</label>
														<input type="text" class="form-control" id="balInfoPlayerLevel" readonly />
														<br/>
													</div>

													<div class="col-md-3">
														<label for="memberSince"><?= lang('pay.memsince'); ?>: </label>
														<input type="text" id="memberSince" class="form-control" readonly>
														<br/>
													</div>
												</div>
													</div>
										</div>
									</div>
								</div>
								<!-- end of personal info-->

								<!-- Deposit transaction -->
								<div class="row" style="margin-top:-15px;">
									<div class="col-md-12">
										<div class="panel panel-primary">
											<div class="panel-heading">
												<h4 class="panel-title">
													<a href="#personal" style="color: white;" id="hide_deposit_info" class="btn btn-info btn-sm"> 
														<i class="glyphicon glyphicon-chevron-down" id="hide_deposit_info_up"></i>
													</a> 
													<?= lang('pay.balinfo'); ?>
												</h4>
											</div>
											<input type="hidden" class="form-control" id="balInfoPlayerId" readonly />
											<input type="hidden" class="form-control" id="balInfoPlayerAccountId" readonly />
											<div class="panel panel-body" id="deposit_info_panel_body" style="display: none;">
												<div class="row">
													<div class="col-md-12">													
															<h4><?= lang('pay.walltbal'); ?></h4>
															<hr/>
												</div>
														<div class="col-md-12">
															<div class="col-md-2">
																<label for="balInfoCurrentBal"><?= lang('pay.mainwalltbal') . ' ' . lang('pay.amt'); ?>:</label>
															</div>
															<div class="col-md-3">
																<input type="text" class="form-control" id="balInfoCurrentBal" readonly />
																<br/>
															</div>
															<div class="col-md-2">
																<label for="balInfoNewCurrentBal"><?= lang('lang.new') . ' ' . lang('pay.mainwalltbal') . ' ' . lang('pay.amt'); ?>:</label>
															</div>

															<div class="col-md-3">
																<input type="text" class="form-control" id="balInfoNewCurrentBal" autofocus />
																<br/>
															</div>
														</div>

									<div class="col-md-12">
															<div class="col-md-2">
																<label for="subwalletCurrentBal"><?= lang('pay.subwalltbal') . ' ' . lang('pay.amt'); ?>:</label>
											</div>
													<div class="col-md-3">
																<input type="text" class="form-control" id="subwalletCurrentBal" readonly />
														<br/>
													</div>
															<div class="col-md-2">
																<label for="balInfoNewCurrentBal"><?= lang('lang.new') . ' ' . lang('pay.subwalltbal') . ' ' . lang('pay.amt'); ?>:</label>
													</div>

													<div class="col-md-3">
																<input type="text" class="form-control" id="balInfoNewCurrentBal" autofocus />
														<br/>
													</div>
													</div>
												</div>
												<div class="row">

														<div class="col-md-12">
															<hr/>
															<h4><?= lang('pay.cashbackbal'); ?></h4>
															<hr/>
													</div>

														<div class="col-md-12">

													<div class="col-md-2">
																<label for="cashbackwalletCurrentBal"><?= lang('pay.cashbackbal') . ' ' . lang('pay.amt'); ?>:</label>
													</div>
															<div class="col-md-3">
																<input type="text" class="form-control" id="cashbackwalletCurrentBal" readonly />
														<br/>
													</div>
													<div class="col-md-2">
																<label for="balInfoNewCurrentBal"><?= lang('lang.new') . ' ' . lang('pay.cashbackbal') . ' ' . lang('pay.amt'); ?>:</label>
													</div>

															<div class="col-md-3">
																<input type="text" class="form-control" id="balInfoNewCurrentBal" autofocus />
														<br/>
													</div>
													</div>
													</div>

													<div class="row">
														<hr/>
														<div id="notificationMsg"></div>
														<div class="col-md-5">
															<button class="btn-md btn-info" id="saveBtn" onclick="PaymentManagementProcess.setPlayerNewBalAmount()"><?= lang('lang.save'); ?></button>
												</div>
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
		<!-- end requestDetailsModal-->
	</div>

	
</div>
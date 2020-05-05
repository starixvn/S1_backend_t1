<!-- Style overwrite -->
<style type="text/css">
	.dashboard-stat .visual>i {
	    margin-left: -17px;
	    font-size: 80px;
	    line-height: 70px;
	}

	.dashboard-stat .visual {
	    width: 80px;
	    height: 93px;
	    display: block;
	    float: left;
	    padding-top: 10px;
	    padding-left: 15px;
	    margin-bottom: 15px;
	    font-size: 35px;
	    line-height: 35px;
	}

	.dashboard-stat .details {
		padding-right: 0;
		z-index: 10;
		padding: 0 10px;
	}

	.dashboard-stat .details .number {
	    padding-top: 15px;
	    text-align: right;
	    font-size: 28px;
	    line-height: 32px;
	    letter-spacing: -1px;
	    margin-bottom: 0;
	    font-weight: 300;
	}

	.dashboard-stat .details .desc {
	    text-align: right;
	    font-size: 11px;
	    letter-spacing: 0;
	    font-weight: 300;
	    padding-left: 5px;
	}

	.lbl-bankname{
		width: 100%;
	}

	.lbl-bankname .customBank{
		float: right;
	}

	.lbl-remarks{
		width: 100%;
		text-align: right;
	}

	.add-on .input-group-btn > .btn {
	  border-left-width:0;left:-2px;
	  -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
	  box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
	}

	/* stop the glowing blue shadow */
	.add-on .form-control:focus {
	 box-shadow:none;
	 -webkit-box-shadow:none;
	 border-color:#cccccc;
	}

	.togvis {
	  cursor: pointer;
	}

	.notes-textarea {
		resize:none;
		height: 200px !important;
		margin-bottom: 10px;
	}

	.add-notes-btn {
		padding-right: 26px;
		padding-left: 26px;
	}

	#bootstrap_dialog_id .modal-footer {
		padding: 20px;
		text-align: center;
		border-top: 1px solid #e5e5e5;
		overflow: hidden;
	}

	#withdrawMethods label.checkbox-inline {
    width: 16.66667%;
    float: left;
    margin: 0;
	}
	@media(max-width:1280px){
	    #withdrawMethods label.checkbox-inline {
	        width: 19%;
	    }
	}
	@media(max-width:992px){
	    #withdrawMethods label.checkbox-inline {
	        width: 50%;
	    }
	}

	.dashboard-stat.checked .count.checked:before {
	    display: block;
	    content: '';
	    position: absolute;
	    left: 5%;
	    top: 8%;
	    height: 84%;
	    width: 90%;
	    border: 1px solid #fff;
	    border-radius: 6px;
	}

	.dashboard-stat .count:hover .desc,
	.dashboard-stat .count:hover .number,
	.dashboard-stat.checked .count.checked .desc,
	.dashboard-stat.checked .count.checked .number {
	    font-weight: bold !important;
	    text-shadow: 0 0 5px rgba(0, 0, 0, 0.5);
	}
	.title-css {
		margin-top: 0;
		padding-bottom: 10px;
		font-size: 16px
	}
</style>
<form id="search-form">
	<h4 class="title-css"><?=lang('pay.quickfilter')?></h4>
	<?php # Programmatically output the top panels with count display

	## -- Settings --
	$allStatus = array(
		'request' => lang('pay.penreq'),
	);

	if($this->utils->isEnabledFeature("enable_withdrawal_pending_review") && $this->permissions->checkPermissions('withdraw_pending_review_list')){
		$allStatus['pending_review'] = lang('pay.penreview');
	}

	for($i = 0; $i < CUSTOM_WITHDRAWAL_PROCESSING_STAGES; $i++) {
		if($setting[$i]['enabled']) {
			$allStatus["CS$i"] = lang($setting[$i]['name']);
		}
	}

	if($setting['payProc']['enabled']) {
		$allStatus['payProc'] = lang('pay.processing');
	}

	$allStatus['paid'] = lang('pay.paid');
	$allStatus['declined'] = lang('pay.decreq');
	$allStatus['lock_api_unknown'] = lang('pay.lockapiunknownreq');


	$iconMapping = array(
		'default' => 'fa-square-o',
		'payProc' => 'fa-share-square-o',
		'paid' => 'fa-check-square-o',
		'declined' => 'fa-minus-square-o',
	);

	$colorMapping = array(
		'random' => $this->utils->getConfig('use_new_sbe_color') ? array('robroy') : array('blue-sharp', 'blue-soft', 'blue-madison', 'blue-steel'),
		'request' => $this->utils->getConfig('use_new_sbe_color') ? 'charm' : 'blue',
		'paid' => $this->utils->getConfig('use_new_sbe_color') ? 'bermuda' : 'green',
		'declined' => $this->utils->getConfig('use_new_sbe_color') ? 'swisscoffee' : 'grey-silver',
		'lock_api_unknown' => $this->utils->getConfig('use_new_sbe_color') ? 'charm' : 'red',
	);
	if ($this->utils->getConfig('use_new_sbe_color')){
		$pendingReviewColor = array('pending_review' => 'tonyspink');
		$payProcColor = array('payProc' => 'eastside');
		$colorMapping = array_merge($colorMapping,$pendingReviewColor,$payProcColor );
	}
	$numPanelsPerRow = 4;

	## -- Output HTML elements --
	$index = 0;
	$randomColorIndex = 0;
	foreach($allStatus as $status => $name) :
		$index++;
		$icon = array_key_exists($status, $iconMapping) ? $iconMapping[$status] : $iconMapping['default'];
		$color = array_key_exists($status, $colorMapping) ? $colorMapping[$status] : $colorMapping['random'][($randomColorIndex++) % count($colorMapping['random'])];
	?>
		<?php if($index % $numPanelsPerRow == 1) : ?>
			<div class="row">
		<?php endif; ?>

		<?php
		    $promoRulesConfig = $this->utils->getConfig('promotion_rules');
		    $enabled_show_withdraw_condition_detail_betting = $promoRulesConfig['enabled_show_withdraw_condition_detail_betting'];
		?>
		<?php if( ($status != 'lock_api_unknown') || (isset($statusCount[$status])) ) : ?>
			<label style="display: block; cursor: pointer; margin-bottom: 0;">
				<div class="col-md-3 col-xs-12">
					<div class="dashboard-stat <?=$color?> <?=($conditions['dwStatus'] == $status ? 'checked' : '')?>" data-status="<?=$status?>">
						<div class="col-md-6 count count_month">
							<div class="visual">
								<i class="fa <?=$icon?>"></i>
							</div>
							<div class="details">
								<div class="number">
									<span><?php echo isset($statusCount[$status]) ? $statusCount[$status] : 0?></span>
								</div>
								<div class="desc"><?=sprintf(lang('%s of the Month'), $name)?></div>
							</div>
						</div>
						<div class="col-md-6 count count_today">
							<div class="visual"></div>
							<div class="details">
								<div class="number">
									<span><?php echo isset($statusCountToday[$status]) ? $statusCountToday[$status] : 0?></span>
								</div>
								<div class="desc"><?=sprintf(lang('%s of Today'), $name)?></div>
							</div>
						</div>
					</div>
				</div>
			</label>
			<?php if($index % $numPanelsPerRow == 0 && ($status != 'lock_api_unknown')) : ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	<?php endforeach; ?>
	</div>


	<div class="panel panel-primary hidden">

		<div class="panel-heading">
			<h4 class="panel-title">
				<i class="fa fa-search"></i> <?=lang("lang.search")?>
				<span class="pull-right">
					<a data-toggle="collapse" href="#collapseWithdrawalList" class="btn btn-info btn-xs <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
				</span>
			</h4>
		</div>

		<div id="collapseWithdrawalList" class="panel-collapse <?=$this->config->item('default_open_search_panel') ? '' : 'collapse in'?>">
			<div class="panel-body">
			<!--Hide this it is the filter checkboxes when the page is not using url params for searching-->
				<div class="row" style="display:none;">
					<fieldset id="withdrawalStatus" style="margin: 5px 15px; padding:0px 10px 10px 15px;">
						<legend>
							<label class="control-label"><?=lang('cashier.122')?></label>
						</legend>

						<div class="col-md-1 col-md-offset-0">
							<div class="checkbox checkbox-info checkbox-circle">
								<input type="checkbox" id="dwStatus_all" class="dwStatus-all" name="dwStatus" <?=$conditions['dwStatus'] == 'all' ? 'checked="checked"' : ''?> value="all"/>
								<label for="dwStatus_all"><?=lang('con.pym24')?>request</label>
							</div>
						</div>
						<div class="col-md-2">
							<div class="checkbox checkbox-info checkbox-circle">
								<input type="checkbox" id="dwStatus_request" class="dwStatus-request" name="dwStatus" <?=$conditions['dwStatus'] == 'request' ? 'checked="checked"' : ''?> value="request"/>
								<label for="dwStatus_request"><?=lang('pay.penreq')?></label>
							</div>
						</div>
						<?php if($this->utils->isEnabledFeature("enable_withdrawal_pending_review") && $this->permissions->checkPermissions('withdraw_pending_review_list')){ ?>
						<div class="col-md-2">
							<div class="checkbox checkbox-info checkbox-circle">
								<input type="checkbox" id="dwStatus_pending_review" class="dwStatus-pending_review" name="dwStatus" <?=$conditions['dwStatus'] == 'pending_review' ? 'checked="checked"' : ''?> value="pending_review"/>
								<label for="dwStatus_pending_review"><?=lang('pay.penreview')?></label>
							</div>
						</div>
						<?php } ?>
						<?php if($customStageCount > 0 || $setting['payProc']['enabled']) : ?>
						<div class="col-md-2">
							<div class="checkbox checkbox-info checkbox-circle">
							<?php for($i = 0; $i < CUSTOM_WITHDRAWAL_PROCESSING_STAGES; $i++) : ?>
								<?php if(!$setting[$i]['enabled']) continue; ?>
								<input type="checkbox" id="dwStatus_CS<?=$i?>" class="dwStatus-CS<?=$i?>" name="dwStatus" <?=$conditions['dwStatus'] == 'CS'.$i ? 'checked="checked"' : ''?>  value="CS<?=$i?>" />
								<label for="dwStatus_CS<?=$i?>"><?=lang($setting[$i]['name'])?></label>
								<br/>
							<?php endfor; ?>
							<?php if($setting['payProc']['enabled']) : ?>
								<input type="checkbox" id="dwStatus_payProc" class="dwStatus-payProc" name="dwStatus" <?=$conditions['dwStatus'] == 'payProc' ? 'checked="checked"' : ''?> value="payProc" />
								<label for="dwStatus_payProc"><?=lang('pay.processing')?></label>
								<br/>
							<?php endif; ?>
							</div>
						</div>
						<?php endif; ?>
						<div class="col-md-2">
							<label>
								<div class="date-range">
									<input type="radio" id="date_range_month" name="date_range" <?php echo $conditions['date_range']  == '1' ? 'checked="checked"' : '' ?> value="1"/><?=lang('date_range_month')?><br>
									<input type="radio" id="date_range_today" name="date_range" <?php echo $conditions['date_range']  == '2' ? 'checked="checked"' : '' ?> value="2"/><?=lang('date_range_today')?>
								</div>
							</label>
						</div>
						<div class="col-md-3">
							<div class="checkbox checkbox-info checkbox-circle">
								<input type="checkbox" id="dwStatus_paid" class="dwStatus-paid" name="dwStatus" <?=$conditions['dwStatus'] == 'paid' ? 'checked="checked"' : ''?> value="paid" />
								<label for="dwStatus_paid"><?=lang('pay.paid')?></label>
								<br/>
								<input type="checkbox" id="dwStatus_declined" class="dwStatus-declined" name="dwStatus" <?=$conditions['dwStatus'] == 'declined' ? 'checked="checked"' : ''?> value="declined" />
								<label for="dwStatus_declined"><?=lang('pay.decreq')?></label>
								<br/>
								<input type="checkbox" id="dwStatus_lock_api_unknown" class="dwStatus-lock_api_unknown" name="dwStatus" <?=$conditions['dwStatus'] == wallet_model::LOCK_API_UNKNOWN_STATUS? 'checked="checked"' : ''?> value="<?=wallet_model::LOCK_API_UNKNOWN_STATUS?>" />
								<label for="dwStatus_lock_api_unknown"><?=lang('pay.lockapiunknownreq')?></label>
								<br/>
							</div>
						</div>

					</fieldset>
				</div>

				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label search-time" for="search_time">
								<?=lang('pay.transperd')?>
								<input style='margin-left:20px' id="request_time" type="radio" name="search_time" value="1" <?php echo $conditions['search_time']  == '1' ? 'checked="checked"' : '' ?>/> <?php echo lang('pay.reqtime');?>
                                <input style='margin-left:20px' id="updated_on" type="radio" name="search_time" value="2" <?php echo $conditions['search_time']  == '2' ? 'checked="checked"' : '' ?>/> <?php echo lang('pay.updatedon'); ?>
							</label>
							<div class="input-group">
								<input id="search_withdrawal_date" class="form-control input-sm dateInput" data-time="true" data-start="#withdrawal_date_from" data-end="#withdrawal_date_to"/>
								<span class="input-group-addon input-sm">
									<input type="checkbox" id="search_enable_date" data-off-text="<?php echo lang('off'); ?>" data-on-text="<?php echo lang('on'); ?>" data-size='mini'  value='<?php echo $conditions['enable_date']?>' <?php echo empty($conditions['enable_date']) ? '' : 'checked="true"'; ?>>
									<input type="hidden" name="enable_date" value='<?php echo $conditions['enable_date']?>'>
								</span>
							</div>
							<input type="hidden" id="withdrawal_date_from" name="withdrawal_date_from" value="<?=$conditions['withdrawal_date_from'];?>"/>
							<input type="hidden" id="withdrawal_date_to" name="withdrawal_date_to" value="<?=$conditions['withdrawal_date_to'];?>"/>

						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label" for="status"><?=lang('status')?></label>
							<select class="form-control input-sm select-status" name="search_status">
								<option value ="allStatus"  ><?=lang("All")?> </option>
								<?php foreach($searchStatus as $status => $value): ?>
									<option value ="<?php echo $status?>" <?php echo $conditions['search_status'] == $status ? 'selected' : '' ?> ><?php echo $value?> </option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label" for="withdraw_code"><?=lang('Withdraw Code')?></label>
							<input id="withdraw_code" type="text" name="withdraw_code"  value="<?php echo $conditions['withdraw_code']; ?>" class="form-control input-sm"/>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label" for="username"><?=lang('pay.username')?></label>
							<input id="username" type="text" name="username"  value="<?php echo $conditions['username']; ?>"  class="form-control input-sm"/>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label" for="realname"><?=lang('pay.realname')?></label>
							<input id="realname" type="text" name="realname"  value="<?php echo $conditions['realname']; ?>"  class="form-control input-sm"/>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label" for="amount_from"><?=lang('pay.withamt')?> &gt;=</label>
							<input id="amount_from" type="number" min="0" name="amount_from"  value="<?php echo $conditions['amount_from']; ?>"  class="form-control input-sm"/>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label" for="amount_to"><?=lang('pay.withamt')?> &lt;=</label>
							<input id="amount_to" type="number" min="0" name="amount_to"  value="<?php echo $conditions['amount_to']; ?>"  class="form-control input-sm"/>
						</div>
					</div>
					<!-- 下面processed_by 搜尋條件先隱藏看客戶使用體驗,再決定是否移除或優化 -->
					<?php if($conditions['dwStatus'] != 'request'):?>
					<div class="col-md-2" style="display:none;">
						<div class="form-group">
							<label class="control-labels" for="processed_by"><?=lang('pay.procssby')?></label>
							<select class="form-control input-sm" name="processed_by">
								<option value =""  ><?=lang("lang.selectall")?> </option>
								<?php foreach($users as $u): ?>
									<option value ="<?php echo $u['userId']?>" <?php echo $conditions['processed_by'] == $u['userId'] ? 'selected' : '' ?> ><?php echo $u['username']?> </option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<?php endif; ?>
				</div>
				<?php if (count($withdrawAPIs) > 0) : ?>
				<div class="row">
					<fieldset id="withdrawMethods" style="margin: 5px 15px; padding:0px 10px 10px 15px;">
						<legend>
							<label class="control-label"><?=lang('Payment Method')?></label>
						</legend>
						<?php foreach($withdrawAPIs as $id=>$name) : ?>
						<label class="checkbox-inline" for="search_api_<?=$id?>">
						<input type="checkbox" name="withdrawAPI[]" data-withdraw-api="<?=$id?>" value="<?=$id?>" class="btn btn-primary" id="search_api_<?=$id?>" <?= in_array($id, $conditions['withdrawAPI']) ? 'checked="checked"' : ''?> /><?=lang($name)?>
						</label>
						<?php endforeach; ?>
						<label class="checkbox-inline" for="search_api_0">
							<input type="checkbox" name="withdrawAPI[]" value="0" class="btn btn-primary" id="search_api_0" <?= in_array(0, $conditions['withdrawAPI']) ? 'checked="checked"' : ''?> /><?=lang('Manual Payment')?>
						</label>
					</fieldset>
				</div>
				<?php endif; ?>
				<div class="row">
					<div class="col-md-offset-9 col-md-3 text-right" style="padding-top: 25px">
						<button type="submit" id="searchBtn" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" style="width:68px;"><?=lang('lang.search')?></button>
						<input type="hidden" name="searchBtn" value='<?php echo $conditions['searchBtn']?>'>
						<button type="button" class="btn btn-sm btn-danger clear-btn" style="width:68px;" onclick="resetSearch()" ><?=lang('lang.clear')?></button>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
<div class="panel panel-primary">
	<div class="panel-heading">
		<h4 class="panel-title">
			<i class="glyphicon glyphicon-open"></i> <?=lang('con.wrl')?>
			<div class="pull-right">
				<?php if($this->utils->isEnabledFeature("enable_batch_withdraw_process_apporve_decline")) { ?>
					<?php if($conditions['dwStatus'] != 'payProc' && $conditions['dwStatus'] != 'paid' && $conditions['dwStatus'] != 'declined') {?>
					<a href="#" onclick="batchProcessOrderId('NEXT');" data-dwstatus="<?=$conditions['dwStatus']?>" class="btn btn-success btn-xs">
						<i class="fa fa-thumbs-up"></i>
						<span class="hidden-xs"><?=lang('Process Selected')?></span>
					</a>
					<?php } ?>
					<?php if($conditions['dwStatus'] != 'paid' && $conditions['dwStatus'] != 'declined') {?>
					<a href="#" onclick="batchProcessOrderId('APPROVE');" data-dwstatus="<?=$conditions['dwStatus']?>" class="btn btn-success btn-xs">
						<i class="fa fa-thumbs-up"></i>
						<span class="hidden-xs"><?=lang('Approve Selected')?></span>
					</a>
					<?php } ?>
					<?php if($conditions['dwStatus'] != 'paid' && $conditions['dwStatus'] != 'declined') {?>
					<a href="#" onclick="batchProcessOrderId('DECLINE');" class="btn btn-danger btn-xs">
						<i class="fa fa-thumbs-down"></i>
						<span class="hidden-xs"><?=lang('Decline Selected')?></span>
					</a>
					<?php } ?>
				<?php } ?>

				<?php if ($this->permissions->checkPermissions('new_withdrawal')) {?>
				<a href="<?=site_url('payment_management/newWithdrawal')?>" class="btn btn-info btn-xs"><i class="fa fa-minus"></i><span class="hidden-xs"><?=lang('lang.newWithdrawal')?></span></a>
				<?php } ?>
				<a href="<?=site_url('payment_management/getWithdrawReport')?>" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-info' : 'btn-default'?> btn-xs">
					<i class="fa fa-list"></i>
					<span class="hidden-xs"><?=lang('Withdraw Processing Time Record')?></span>
				</a>
			</div>
		</h4>
	</div>
	<div class="panel-body">
	  	<div class="table-responsive">
			<table class="table table-bordered table-hover" id="withdraw-table">
				<thead>
					<tr>
						<?php
							$action_th =
								'<th>'.
									'<div class="clearfix" style="width:65px;">';

							if($this->utils->isEnabledFeature("enable_batch_withdraw_process_apporve_decline")) {
								$action_th .=
										'<div class="col-md-3" style="padding:0 1px 0 2px">'.
											'<input type="checkbox" name="chkAll" id="chkAll" class="user-success">'.
										'</div>';
							}
							$action_th .=
										'<div class="col-md-9" style="padding:0 2px 0 2px">'.lang('lang.action').'</div>'.
									'</div>'.
								'</th>';
						?>
	                    <?php if($this->utils->isEnabledFeature('separate_approve_decline_withdraw_pending_review_and_request_permission')){
	                        if($conditions['dwStatus'] == 'request'  && $this->permissions->checkPermissions('approve_decline_withdraw_pending_request')){
	                            echo $action_th;
	                        }
	                        if($conditions['dwStatus'] == 'pending_review' && $this->permissions->checkPermissions('approve_decline_withdraw_pending_review')){
	                            echo $action_th;
	                        }
	                        if($conditions['dwStatus'] != 'request' && $conditions['dwStatus'] != 'pending_review'){
	                            echo $action_th;
	                        }
	                    }else{
	                        if($conditions['dwStatus'] == 'request' || $conditions['dwStatus'] == 'pending_review'){
	                            if($this->permissions->checkPermissions('approve_decline_withdraw') ){
	                                echo $action_th;
	                            }
	                        }else{
	                            echo $action_th;
	                        }
	                    }?>

	                    <th><?=lang('lang.status')?></th>
						<th><?=lang("Withdraw Code")?></th>
						<th><?=lang('Locked Status')?></th>
						<th><?=lang("pay.username")?></th>
						<th style="min-width:45px;"><?=lang("pay.reqtime")?></th>
						<th style="min-width:45px;"><?=lang("pay.proctime")?></th>
						<th style="min-width:45px;"><?=lang("pay.paidtime")?></th>
						<th style="min-width:45px;"><?=lang("pay.spenttime")?></th>
						<th><?=lang("pay.realname")?></th>
						<th><?=lang('pay.playerlev')?></th>
						<th><?=lang("Tag")?></th>
						<th><?=lang('pay.withamt')?></th>
						<th><?=lang('pay.bankname')?></th>
						<th><?=lang('pay.acctname')?></th>
						<th><?=lang('pay.acctnumber')?></th>
						<th><?=lang('pay.payment_account_flag')?></th>
						<th><?=lang('pay.acctbranch')?></th>
						<th><?=lang('Withdrawal Declined Category')?></th>
						<th><?=lang('Province')?></th>
						<th><?=lang('City')?></th>
						<th><?=lang('pay.withip')?></th>
						<th><?=lang('pay.withlocation')?></th>
						<th><?=lang('pay.procssby')?></th>
						<th><?=lang('pay.updatedon')?></th>
						<th><?=lang("pay.withdrawalId")?></th>
						<th style="min-width:400px;"><?=lang('External Note');?></th>
						<th style="min-width:400px;"><?=lang('Internal Note');?></th>
						<th style="min-width:600px;"><?=lang('Action Log');?></th>
						<th style="min-width:400px;"><?=lang('pay.timelog')?></th>
						<th><?=lang('pay.curr')?></th>
						<th><?=lang('sys.ga.systemcode')?></th>
						<th><?=lang('lang.withdrawal_payment_api')?></th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
	<div class="panel-footer"></div>
</div>

<?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
	<form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
		<input name='json_search' type="hidden">
	</form>
<?php }?>
<script type="text/javascript">
		function resetSearch(){
			$('.dateInput').data('daterangepicker').setStartDate(moment().startOf('day').format('Y-MM-DD HH:mm:ss'));
			$('.dateInput').data('daterangepicker').setEndDate(moment().endOf('day').format('Y-MM-DD HH:mm:ss'));
			dateInputAssignToStartAndEnd($('#search_withdrawal_date'));
			$('.select-status').val('allStatus');
			$('#request_time').prop('checked', 'checked');
			$('#withdrawMethods input[type=\'checkbox\']').prop('checked', false);
			clearSearchInputVal();
		}

		function clearSearchInputVal(){
			$('#withdraw_code').val('');
			$('#username').val('');
			$('#realname').val('');
			$('#amount_from').val('');
			$('#amount_to').val('');
		}

		function handler_search_enable_date_change() {
			var checked = $('#search_enable_date').is(':checked');
			$('#search_withdrawal_date').removeAttr('disabled');
			if (!checked) {
				$('#search_withdrawal_date').attr('disabled', 1);
			}
		}

		//checked select status and link search time
		$('.select-status').change(function(){
			var getValue = $('.select-status').val();
			if(getValue == 'allStatus' || getValue == 'request'){
				$('#request_time').prop('checked', 'checked');
			}else{
				$('#updated_on').prop('checked', 'checked');
			}
		});

		//click search input and clear dashbord css
		$('#request_time ,#updated_on ,#search_withdrawal_date ,.select-status ,#withdraw_code ,#username ,#realname ,#amount_from ,#amount_to ,.clear-btn').click(function () {
			$('.count_month').removeClass('checked');
			$('.count_today').removeClass('checked');
		});




		$(document).ready(function(){

		<?php $col_config = $this->utils->getConfig('withdrawal_list_columnDefs'); ?>
			var hidden_cols = [];
		<?php if(!empty($col_config['not_visible_payment_management'])) : ?>
			var not_visible_cols = JSON.parse("<?= json_encode($col_config['not_visible_payment_management']) ?>" ) ;
		<?php else: ?>
			var not_visible_cols = [ 19, 20, 22, 25, 30, 32 ];
		<?php endif; ?>

		<?php if(!empty($col_config['className_text-right_payment_management'])) : ?>
			var text_right_cols = JSON.parse("<?= json_encode($col_config['className_text-right_payment_management']) ?>" ) ;
		<?php else: ?>
			var text_right_cols = [ 8, 12, 29 ];
		<?php endif; ?>

			var desc = 5;
			if($('#updated_on').is(':checked')){
				desc = 24;
			}

			var dataTable = $('#withdraw-table').DataTable({
	            lengthMenu: JSON.parse('<?=json_encode($this->utils->getConfig('default_datatable_lengthMenu'))?>'),
				autoWidth: false,
				searching: false,

	            <?php if ($this->utils->isEnabledFeature('column_visibility_report')){ ?>
	                stateSave: true,
	            <?php } else { ?>
	                stateSave: false,
	            <?php } ?>

				pageLength: <?=$this->utils->getDefaultItemsPerPage()?>,
				dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
				buttons: [
					{
						extend: 'colvis',
						postfixButtons: [ 'colvisRestore' ],
						className: '<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>'
					}
					<?php

	                  if( $this->permissions->checkPermissions('export_withdrawal_lists') ){
	                ?>
	                    ,{
	                        text: "<?php echo lang('CSV Export'); ?>",
	                        className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>',
	                        action: function ( e, dt, node, config ) {
								var form_params=$('#search-form').serializeArray();

								var d = {'extra_search': form_params, 'export_format': 'csv', 'export_type': export_type,
									'draw':1, 'length':-1, 'start':0};
	                            $("#_export_excel_queue_form").attr('action', site_url('/export_data/withdrawList/null/true/true'));
	                            $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
	                            $("#_export_excel_queue_form").submit();

	                        }
	                    }
		            <?php
		            	}
		            ?>
				],
				columnDefs: [
					{ sortable: false, targets: [ 0 ] },
					{ visible: false, targets: not_visible_cols },
					{ className: 'text-right', targets: text_right_cols },
					{ className: 'hidden', targets: hidden_cols }
				],
				order: [[desc, 'desc']],

				// SERVER-SIDE PROCESSING
				processing: true,
				serverSide: true,
				ajax: function (data, callback, settings) {
					data.extra_search = $('#search-form').serializeArray();
					$.post(base_url + "api/withdrawList", data, function(data) {
						callback(data);
						if ( dataTable.rows( { selected: true } ).indexes().length === 0 ) {
						    dataTable.buttons().disable();
						}
						else {
							dataTable.buttons().enable();
						}
					},'json');
				}
			});

			$('#search-form input[type="text"],#search-form input[type="number"],#search-form input[type="email"]').keypress(function (e) {
				if (e.which == 13) {
					$('#search-form').trigger('submit');
				}
			});

			$('#requestDetailsModal, #approvedDetailsModal, #declinedDetailsModal,#paidDetailsModal').on('hidden.bs.modal', function (e) {
				dataTable.ajax.reload(null, false);
			});

			$('#requestDetailsModal, #approvedDetailsModal').on('hidden.bs.modal', function (e) {
				$('.withdraw-paid-btn').removeClass('hide');
			});

			// Select / Deselect all checkboxes
			$('#dwStatus_all').change(function(){
				var checked = $(this).prop('checked');
				$('div.checkbox input').prop('checked', checked);
			});


			// Deselect the 'All' checkbox
			$('div.checkbox input:not(#dwStatus_all)').change(function(){
				var checked = $(this).prop('checked');
				if(!checked) {
					$('#dwStatus_all').prop('checked', false);
				}
			});

			//trigger enable_date check box
			$('#search_enable_date').change(function(){
				if($(this).is(':checked')) {
					handler_search_enable_date_change();
					$(this).attr('checked', true);
	                $('input[name="enable_date"]').val('1');

	            }else{
	                handler_search_enable_date_change();
	                $(this).attr('checked', false);
	                $('input[name="enable_date"]').val('0');
				}
			}).trigger('change');


			// trigger searchBtn
			$('#searchBtn').click(function(){
                $('input[name="searchBtn"]').val('1');
			}).trigger('submit');

			<?php if($conditions['dwStatus'] != $conditions['search_status']) : ?>
				$('.select-status').val('<?php echo $conditions['search_status']?>');
			<?php else: ?>
				$('.select-status').val('<?php echo $conditions['dwStatus']?>');
			<?php endif; ?>
			// Handle click on dashboard block
			$('.dashboard-stat').click( function() {
				var dwStatus = $(this).data('status');
				//clear all checked and check current
				$('.dashboard-stat').removeClass('checked');
				$('.dashboard-stat[data-status=\'' + dwStatus + '\']').addClass('checked');

				if (dwStatus != '<?=Wallet_model::REQUEST_STATUS?>') {
					dataTable.order( [ 14, 'desc' ] )
				} else {
					dataTable.order( [ 2, 'desc' ] )
				}

				// Set the withdrawal status checkboxes
				$('#withdrawalStatus input[type=\'checkbox\']').prop('checked', false);
				$('#withdrawalStatus input[type=\'checkbox\'].dwStatus-'+dwStatus).prop('checked', true);

				//when checked dashbord link search status and search time
				$('.select-status').val(dwStatus);
				if (dwStatus == '<?=Wallet_model::REQUEST_STATUS?>' || dwStatus == '<?=Wallet_model::PENDING_REVIEW_STATUS?>') {
					$('#request_time').prop('checked', 'checked');
				} else {
					$('#updated_on').prop('checked', 'checked');
				}

				$('#search-form').trigger('submit');
			});

			//OGP-14817 to 14819
			$('.dashboard-stat .count').click( function () {
				$('.date-range').removeAttr('checked');
				if ($(this).hasClass('count_month')) {
					$('.count_today').removeClass('checked');
					$('#date_range_month').prop('checked', 'checked');
					$('input[name="enable_date"]').val('1');
					$('#withdrawal_date_from').val(moment().startOf('month').format('Y-MM-DD HH:mm:ss'));
					$('#withdrawal_date_to').val(moment().endOf('day').format('Y-MM-DD HH:mm:ss'));
					$('input[name="searchBtn"]').val('0');
					//clear search input value
					clearSearchInputVal();
				}else if($(this).hasClass('count_today')){
					$('.count_month').removeClass('checked');
					$('#date_range_today').prop('checked', 'checked');
					$('input[name="enable_date"]').val('1');
					$('#withdrawal_date_from').val(moment().startOf('day').format('Y-MM-DD HH:mm:ss'));
					$('#withdrawal_date_to').val(moment().endOf('day').format('Y-MM-DD HH:mm:ss'));
					$('input[name="searchBtn"]').val('0');
					//clear search input value
					clearSearchInputVal();
				}
				handler_search_enable_date_change();
			});

			var searchBtnVal = $('input[name="searchBtn"]').val();
			var radioCheckedMon = $('#date_range_month').is(':checked');
			var radioCheckedToday = $('#date_range_today').is(':checked');

			if(searchBtnVal == '1'){
				$('.count_month').removeClass('checked');
				$('.count_today').removeClass('checked');
			}else if(radioCheckedMon){
				$('.count_month').addClass('checked');
			}else if(radioCheckedToday){
				$('.count_today').addClass('checked');
			}


			$('#search_enable_date').change( handler_search_enable_date_change );

			handler_search_enable_date_change();

			// Trigger the click event of the checked panel
			$('legend.togvis').click(function() {
		        var $this = $(this);
		        var parent = $this.parent();
		        var contents = parent.contents().not(this);
		        var toggleSign = $(this).children("span");

		        if (contents.length > 0) {
		            $this.data("contents", contents.remove());
		            toggleSign.html("[+]");
		        } else {
		            $this.data("contents").appendTo(parent);
		            toggleSign.html("[-]");
		        }
		        return false;
		    });
		});
</script>
<?php ################################################################################################################################################################### ?>

<!-- start requestDetailsModal-->
<div class="row">
	<div class="modal fade" id="requestDetailsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal_full">
			<div class="modal-content modal-content-three">
				<div class="modal-header">
					<a class='notificationRefreshList' href="<?=site_url('payment_management/refreshList/withdrawalRequest')?>">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only"><?=lang("lang.close")?></span></button>
					</a>
					<h4 class="modal-title" id="myModalLabel"><i class="icon-drawer"></i>&nbsp;<?=lang("pay.withreqst") . ' ' . lang("lang.details")?></h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12" id="checkPlayer">
							<!-- Withdrawal transaction -->
							<div class="row">
								<div class="col-md-12">
									<div class="panel panel-primary">
										<div class="panel-heading">
											<h4 class="panel-title">
												<?=lang('pay.withinfo')?>
												<a href="#personal" id="hide_deposit_info" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-info' : 'btn-default'?> btn-sm pull-right">
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
														  		<legend class='togvis'><?=lang('player.ui04')?> <span>[-]</span></legend>
																<table class='table'>
																	<tr>
																		<td style='border-top:0px; text-align:left;'>
																			<label for="userName"><?=lang("pay.username")?>:</label>
																			<br/>
																			<input type="hidden" class="form-control playerId" readonly/>
																			<div class="form-group">
																				<div class="input-group add-on">
																				   <input type="text" class="form-control userName" id="txtReqUserName" readonly/>
																				   <span class="input-group-btn">
																				        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtReqUserName"><i class="glyphicon glyphicon-copy"></i></button>
																				   </span>
																				</div>
																			</div>
																		</td>
																		<td style='border-top:0px; text-align:left;'>
																			<label for="playerName"><?=lang("pay.realname")?>:</label>
																			<br/>

																			<div class="input-group add-on">
																			   <input type="text" class="form-control playerName" id="txtReqPlayerName" readonly/>
																			   <span class="input-group-btn">
																			        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtReqPlayerName"><i class="glyphicon glyphicon-copy"></i></button>
																			   </span>
																			</div>
																		</td>
																		<td style='border-top:0px; text-align:left;'>
																			<label for="playerLevel"><?=lang('pay.playerlev')?>:</label>
																			<div class="input-group add-on">
																			   <input type="text" class="form-control playerLevel" id="txtReqPlayerLevel" readonly/>
																			   <span class="input-group-btn">
																			        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtReqPlayerLevel"><i class="glyphicon glyphicon-copy"></i></button>
																			   </span>
																			</div>
																		</td>
																		<td style='border-top:0px; text-align:left;'>
																			<label for="memberSince"><?=lang('pay.memsince')?>: </label>
																			<br/>
																			<div class="input-group add-on">
																			   <input type="text" class="form-control memberSince" id="txtReqMemberSince" readonly>
																			   <span class="input-group-btn">
																			        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtReqMemberSince"><i class="glyphicon glyphicon-copy"></i></button>
																			   </span>
																			</div>
																		</td>
																	</tr>
																</table>
														  	</fieldset>
														</form>
													</div>
													<div class="col-md-12 hide">
														<form>
														  	<fieldset>
														  		<legend class='togvis'><?=lang('pay.walletInfo')?> <span>[-]</span></legend>
																<table class='table'>
																	<tr>
																		<td style='border-top:0px; text-align:left;'>
																			<label for="mainWalletBalance"><?=lang('pay.mainwalltbal')?>:</label>
																			<br/>
																			<div class="input-group add-on">
																			   <input type="text" class="form-control mainWalletBalance" id="txtReqMainWalletBalance" readonly/>
																			   <span class="input-group-btn">
																			        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtReqMainWalletBalance"><i class="glyphicon glyphicon-copy"></i></button>
																			   </span>
																			</div>
																		</td>
																		<?php foreach ($game_platforms as $game_platform): ?>
																			<td style='border-top:0px; text-align:left;'>
																				<label for="subWalletBalance<?=$game_platform['id']?>">
																					<?=$game_platform['system_code']?>:
																				</label>
																				<br/>
																				<div class="input-group add-on">
																				   <input type="text" class="form-control subWalletBalance subWalletBalance<?=$game_platform['id']?>" id="txtReqSubWalletBalance<?=$game_platform['id']?>" readonly/>
																				   <span class="input-group-btn">
																				        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtReqSubWalletBalance<?=$game_platform['id']?>"><i class="glyphicon glyphicon-copy"></i></button>
																				   </span>
																				</div>
																			</td>
																		<?php endforeach?>
																		<td style='border-top:0px; text-align:left;'>
																			<label for="totalBalance">
																				<?=lang('pay.totalbal')?>:
																			</label>
																			<br/>
																			<div class="input-group add-on">
																			   	<input type="text" class="form-control totalBalance" id="txtReqTotalBalance" readonly/>
																			   	<span class="input-group-btn">
																			        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtReqTotalBalance"><i class="glyphicon glyphicon-copy"></i></button>
																			   	</span>
																			</div>
																		</td>
																	</tr>
																</table>
														   	</fieldset>
														</form>
													</div>
	                                                <?php include dirname(__FILE__) . '/withdrawal_list/withdraw_condition_details.php';?>
												</div>
											</div>

											<input type="hidden" class="currentLang" value="<?=$this->language_function->getCurrentLanguage()?>">
											<div class="col-md-12">
												<div class="row">
													<div class="col-md-12">
														<form>
															<fieldset>
																<legend class='togvis'><?=lang('pay.withdetl')?> <span>[-]</span></legend>
																<div class="paymentMethodSection">
																	<div class="row">
																		<div class="col-md-12">
																			<table class='table'>
																				<tr>
																					<td style='border-top:0px; text-align:left;'>
																						<label for="withdrawalAmount"><?=lang('pay.withamt')?>:</label>
																						<div class="input-group add-on">
																						   	<input type="text" class="form-control withdrawalAmount" id="txtReqWithdrawalAmount" readonly/>
																						   	<span class="input-group-btn">
																						        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtReqWithdrawalAmount"><i class="glyphicon glyphicon-copy"></i></button>
																						   	</span>
																						</div>
																					</td>
																					<td style='border-top:0; text-align:left;'>
																						<label for="withdrawalCode"><?=lang('Withdraw Code')?>:</label>
																						<div class="input-group add-on">
																						   	<input type="text" class="form-control withdrawalCode" id="txtReqWithdrawalCode" readonly/>
																						   	<span class="input-group-btn">
																						        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtReqWithdrawalCode"><i class="glyphicon glyphicon-copy"></i></button>
																						   	</span>
																						</div>
																					</td>
																					<td style='border-top:0px; text-align:left;'>
																						<label for="currency"><?=lang('pay.curr')?>:</label>
																						<div class="input-group add-on">
																						   	<input type="text" class="form-control currency" id="txtReqCurrency" readonly/>
																						   	<span class="input-group-btn">
																						        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtReqCurrency"><i class="glyphicon glyphicon-copy"></i></button>
																						   	</span>
																						</div>
																					</td>
																					<td style='border-top:0px; text-align:left;'>
																						<label for="dateDeposited"><?=lang('pay.reqtdon')?>:</label>
																						<div class="input-group add-on">
																						   	<input type="text" class="form-control dateDeposited" id="txtReqDateDeposited" readonly/>
																						   	<span class="input-group-btn">
																						        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtReqDateDeposited"><i class="glyphicon glyphicon-copy"></i></button>
																						   	</span>
																						</div>
																					</td>
																					<td style='border-top:0px; text-align:left;'>
																						<label for="ipLoc"><?=lang('pay.withip') . ' ' . lang('lang.and') . ' ' . lang('pay.locatn')?>:</label>
																						<div class="input-group add-on">
																						   	<input type="text" class="form-control ipLoc" id="txtReqIpLoc" readonly/>
																						   	<span class="input-group-btn">
																						        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtReqIpLoc"><i class="glyphicon glyphicon-copy"></i></button>
																						   </span>
																						</div>
																					</td>
																				</tr>
																				<tr>
																					<td style='border-top:0px; text-align:left;'>
																						<label for="bankName"><?=lang('pay.bankname')?>:</label>
																						<div class="input-group add-on">
																						   	<input type="text" class="form-control bankName" id="txtReqBankName" readonly/>
																						   	<span class="input-group-btn">
																						        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtReqBankName"><i class="glyphicon glyphicon-copy"></i></button>
																						   	</span>
																						</div>
																					</td>
																					<td style='border-top:0px; text-align:left;'>
																						<label for="bankAccountName"><?=lang('pay.acctname')?>:</label>
																						<div class="input-group add-on">
																						   	<input type="text" class="form-control bankAccountName" id="txtReqBankAccountName" readonly/>
																						   	<span class="input-group-btn">
																						        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtReqBankAccountName"><i class="glyphicon glyphicon-copy"></i></button>
																						   </span>
																						</div>
																					</td>
																					<td style='border-top:0px; text-align:left;'>
																						<label for="bankAccountNumber"><?=lang('pay.bank') . ' ' . lang('pay.acctnumber')?>:</label>
																						<div class="input-group add-on">
																						   	<input type="text" class="form-control bankAccountNumber" id="txtReqBankAccountNumber" readonly/>
																						   	<span class="input-group-btn">
																						        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtReqBankAccountNumber"><i class="glyphicon glyphicon-copy"></i></button>
																						   	</span>
																						</div>
																					</td>
																					<td style='border-top:0px; text-align:left;'>
																						<label for="bankAccountBranch"><?=lang('pay.bank') . ' ' . lang('pay.acctbranch')?>:</label>
																						<div class="input-group add-on">
																						   	<input type="text" class="form-control bankAccountBranch" id="txtReqBankAccountBranch" readonly/>
																						   	<span class="input-group-btn">
																						        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtReqBankAccountBranch"><i class="glyphicon glyphicon-copy"></i></button>
																						   	</span>
																						</div>
																					</td>
																				</tr>
																				<tr>
																					<td style='border-top:0px; text-align:left;'>
																						<label for="bankPhone"><?=lang('pay.bankPhone')?>:</label>
																						<div class="input-group add-on">
																						   	<input id="txtReqbankPhone" type="text" class="form-control bankPhone" readonly/>
																						   	<span class="input-group-btn">
																						        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtReqbankPhone"><i class="glyphicon glyphicon-copy"></i></button>
																						   	</span>
																						</div>
																					</td>
																					<td style='border-top:0px; text-align:left;' colspan="3">
																						<label for="bankAddress"><?=lang('pay.bankAddress')?>:</label>
																						<div class="input-group add-on">
																						   	<input id="txtReqbankAddress" type="text" class="form-control bankAddress" readonly/>
																						   	<span class="input-group-btn">
																						        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtReqbankAddress"><i class="glyphicon glyphicon-copy"></i></button>
																						   	</span>
																						</div>
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

											<!-- withdrawal rule -->
											<div class="col-md-12">
												<fieldset>
													<legend class='togvis'><?=lang('cms.withrule')?> <span>[-]</span></legend>
													<div class="row">
														<div class="col-md-3">
															<div class="form-group">
																<label><?=lang('pay.dailymaxwithdrawal')?>:</label>
																<div class="input-group add-on">
																   <input type="text" class="form-control dailyMaxWithdrawal" id="txtReqDailyMaxWithdrawal" readonly/>
																   <span class="input-group-btn">
																        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtReqDailyMaxWithdrawal"><i class="glyphicon glyphicon-copy"></i></button>
																   </span>
																</div>
															</div>
														</div>
														<div class="col-md-3">
															<div class="form-group">
																<label><?=lang('report.sum10') . ' ' . lang('report.pr24')?>:</label>
																<div class="input-group add-on">
																   <input type="text" class="form-control totalWithdrawalToday" id="txtReqTotalWithdrawalToday" readonly/>
																   <span class="input-group-btn">
																        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtReqTotalWithdrawalToday"><i class="glyphicon glyphicon-copy"></i></button>
																   </span>
																</div>
															</div>
														</div>
													</div>
												</fieldset>
											</div>
											<!-- duplicate account info -->
											<div class="col-md-12">
												<div class="row playerDuplicateAccountInfoPanel">
					                                <div class="col-md-12">
				                                        <fieldset>
				                                            <legend class='togvis'><?=lang('pay.duplicateAccountList')?> <span>[-]</span></legend>
				                                            <div class="col-md-12 ">
				                                                <div id="logList" class="table-responsive">
				                                                    <table class="duplicateTable table table-striped table-hover table-bordered"  width=100%>
				                                                        <thead>
				                                                            <tr>
        																		<?php $dup_enalbed_column = $this->utils->getConfig('duplicate_account_info_enalbed_condition') ?>
				                                                                <th><?= lang('Username'); ?></th>
				                                                                <th><?= lang('Total Rate'); ?></th>
				                                                                <th><?= lang('Possibly Duplicate'); ?></th>
																				<?php if (in_array('ip', $dup_enalbed_column)) : ?>
					                                                                <th><?= lang('Reg IP'); ?></th>
					                                                                <th><?= lang('Login IP'); ?></th>
					                                                                <th><?= lang('Deposit IP'); ?></th>
					                                                                <th><?= lang('Withdraw IP'); ?></th>
					                                                                <th><?= lang('Transfer Main To Sub IP'); ?></th>
					                                                                <th><?= lang('Transfer Sub To Main IP'); ?></th>
				                                                                <?php endif; ?>
				                                                                <?php if (in_array('realname', $dup_enalbed_column)) : ?>
				                                                                	<th><?= lang('Real Name'); ?></th>
				                                                                <?php endif; ?>
				                                                                <?php if (in_array('password', $dup_enalbed_column)) : ?>
				                                                                	<th><?= lang('Password'); ?></th>
				                                                                <?php endif; ?>
				                                                                <?php if (in_array('email', $dup_enalbed_column)) : ?>
				                                                                	<th><?= lang('Email'); ?></th>
				                                                                <?php endif; ?>
				                                                                <?php if (in_array('mobile', $dup_enalbed_column)) : ?>
				                                                                	<th><?= lang('Mobile'); ?></th>
				                                                                <?php endif; ?>
				                                                                <?php if (in_array('address', $dup_enalbed_column)) : ?>
				                                                                	<th><?= lang('Address'); ?></th>
				                                                                <?php endif; ?>
				                                                                <?php if (in_array('city', $dup_enalbed_column)) : ?>
				                                                                	<th><?= lang('City'); ?></th>
				                                                                <?php endif; ?>
				                                                                <?php if (in_array('country', $dup_enalbed_column)) : ?>
				                                                                	<th><?= lang('pay.country') ?></th>
				                                                                <?php endif; ?>
				                                                                <?php if (in_array('cookie', $dup_enalbed_column)) : ?>
				                                                                	<th><?= lang('Cookies'); ?></th>
				                                                                <?php endif; ?>
				                                                                <?php if (in_array('referrer', $dup_enalbed_column)) : ?>
				                                                                	<th><?= lang('From'); ?></th>
				                                                                <?php endif; ?>
				                                                                <?php if (in_array('device', $dup_enalbed_column)) : ?>
				                                                                	<th><?= lang('Device'); ?></th>
				                                                                <?php endif; ?>
				                                                            </tr>
				                                                        </thead>
				                                                    </table>
				                                                </div>
				                                            </div>
				                                        </fieldset>
					                                </div>
												</div>
											</div>
											<!-- end duplicate account info -->

											<?php if ($this->utils->isEnabledFeature('enable_withdrawal_declined_category') && !empty($withdrawalDeclinedCategory)) : ?>
												<hr/>
												<div class="row">
													<label class="col-md-12"><?=lang('Withdrawal Declined Category');?></label>
								                    <div class="col-md-3">
								                        <select class="form-control declined-category-id" id="declined_category_id" name="declined_category_id">
								                        	<option class="declined-category-id" value="">*** <?= lang('select_decline_category') ?> ***</option>
								                            <?php foreach ($withdrawalDeclinedCategory as $key => $value): ?>
								                                  <option class="declined-category-id" value="<?= $value['id'] ?>"><?= lang($value['category_name']) ?></option>
								                            <?php endforeach; ?>
								                        </select>
								                    </div>
												</div>
											<?php endif; ?>
											<!-- <hr/> -->

											<!--Start payment notes -->
											<div class="row">
											    <div class="col-md-12">
											        <h4 class="page-header"><?=lang('lang.notes');?></h4>
											    </div>
											    <div class="col-md-6">
											        <label><?=lang('Internal Note Record')?>:</label>
											        <textarea class="form-control withdraw-internal-notes notes-textarea" readonly></textarea>
											    </div>
											    <div class="col-md-6">
											        <label><?=lang('External Note Record')?>:</label>
											        <textarea class="form-control withdraw-external-notes notes-textarea" readonly></textarea>
											    </div>
												<div class="col-md-6">
									                <label><?=lang('Add Internal Note')?>:</label>
									                <textarea id="requestInternalRemarksTxt" class="form-control notes-textarea" maxlength="500"></textarea>
									                <button type='button' class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?> pull-right add-notes-btn" id="requestinternalnotebtn" onclick="addNotes('requestinternalnotebtn','2')">
									                	<span class="glyphicon glyphicon-plus" aria-hidden="true" style="padding-right: 4px"></span> <?=lang('Add')?>
									                </button>
									            </div>
									            <div class="col-md-6">
									                <label><?=lang('Add External Note')?>:</label>
									                <textarea id="requestExternalRemarksTxt" class="form-control notes-textarea" maxlength="500"></textarea>
									                <button type='button' class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?> pull-right add-notes-btn" id="requestexternalnotebtn" onclick="addNotes('requestexternalnotebtn','3')">
									                	<span class="glyphicon glyphicon-plus" aria-hidden="true" style="padding-right: 4px"></span> <?=lang('Add')?>
									                </button>
									            </div>
								            </div>
											<hr/>
								            <!--End payment notes -->

											<div class="row">
												<input type="hidden" class="form-control request_walletAccountIdVal" readonly />
												<div class="col-md-12 transactionStatusMsg text-danger"></div>
												<div class="actions_sec">
													<div class="col-md-12">
														<?php if($conditions['dwStatus'] != wallet_model::LOCK_API_UNKNOWN_STATUS) { ?>
															<button class="btn btn-md <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?> response-sec" id="btn_approve" onclick="respondToWithdrawalRequest()"><?=lang('lang.approve')?></button>
															<button class="btn btn-md btn-danger response-sec" id="btn_decline" onclick="respondToWithdrawalDeclined()"><?=lang('pay.declnow')?></button>

														<?php } else { ?>
															<button type="button" class="btn btn-md btn-primary response-sec" id="request_btn_unlock_api_unknown" onclick="unlockApiUnknown()"><?=lang('pay.unlocknow')?></button>
														<?php } ?>
														<button class="btn btn-md <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default'?> closeRequest" class="close" data-dismiss="modal"><?=lang('lang.close');?></button>
													</div>
												</div>
											</div>
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
</div>
<!-- end requestDetailsModal-->

<div class="row">
	<div class="modal fade" id="addRemarks" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal_full">
			<div class="modal-content modal-content-three">
				<div class="modal-header">
					<a class='notificationRefreshList' href="<?=site_url('payment_management/refreshList/depositRequest')?>">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only"><?=lang("lang.close")?></span></button>
					</a>
					<h4 class="modal-title" id="myModalLabel"><i class="icon-drawer"></i>&nbsp;<?=lang("pay.appwithdetl")?></h4>
				</div>
				<div class="modal-body"></div>
			</div>
		</div>
	</div>
</div>

<!-- start approvedDetailsModal -->
<div class="row">
	<div class="modal fade" id="approvedDetailsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal_full">
			<div class="modal-content modal-content-three">
				<div class="modal-header">
					<a class='notificationRefreshList' href="<?=site_url('payment_management/refreshList/depositRequest')?>">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only"><?=lang("lang.close")?></span></button>
					</a>
					<h4 class="modal-title" id="myModalLabel"><i class="icon-drawer"></i>&nbsp;<?=lang("pay.appwithdetl")?></h4>
				</div>

				<div class="modal-body">
					<!-- player transaction -->
					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-primary">
								<div class="panel-heading">
									<h4 class="panel-title">
										<?=lang("pay.appwithinfo")?>
										<a href="#approvedDeposit" id="hide_approved_deposit_transac" class="btn btn-default btn-sm pull-right">
											<i class="glyphicon glyphicon-chevron-up" id="hide_approved_deposit_transac_up"></i>
										</a>
										<div class="clearfix"></div>
									</h4>
								</div>

								<div class="panel-body" id="approved_deposit_transac_panel_body" style="display: none;">
									<div class="row locked_withdrawal" style="display: none;">
										<div class="col-md-12">
											<p class="text-danger"><?php echo lang('this withdrawal has been locked');?></p>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											<div class="row">
												<div class="col-md-12">
													<div class="row">
														<div class="col-md-12">
															<div class="col-md-3">
																<label for="userName"><?=lang("pay.username")?>:</label>
																<div class="input-group add-on">
																   <input type="text" class="form-control userName" id="txtAppUserName" readonly/>
																   <span class="input-group-btn">
																        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtAppUserName"><i class="glyphicon glyphicon-copy"></i></button>
																   </span>
																</div>
															</div>

															<div class="col-md-3">
																<label for="playerName"><?=lang("pay.realname")?>:</label>
																<div class="input-group add-on">
																   	<input type="text" class="form-control playerName" id="txtAppPlayerName" readonly/>
																   	<span class="input-group-btn">
																        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtAppPlayerName"><i class="glyphicon glyphicon-copy"></i></button>
																   </span>
																</div>
															</div>

															<div class="col-md-3">
																<label for="playerLevel"><?=lang('pay.playerlev')?>:</label>
																<div class="input-group add-on">
																   	<input type="text" class="form-control playerLevel" id="txtAppPlayerLevel" readonly/>
																   	<span class="input-group-btn">
																        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtAppPlayerLevel"><i class="glyphicon glyphicon-copy"></i></button>
																   	</span>
																</div>
															</div>

															<div class="col-md-3">
																<label for="memberSince"><?=lang('pay.memsince')?>: </label>
																<div class="input-group add-on">
																   	<input type="text" class="form-control memberSince" id="txtAppMemberSince" readonly>
																   	<span class="input-group-btn">
																        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtAppMemberSince"><i class="glyphicon glyphicon-copy"></i></button>
																   	</span>
																</div>
															</div>
														</div>
													</div>

													<div class="row">
														<div class="col-md-12">
															<br/>
															<div class="col-md-3">
																<label for="mainWalletBalance"><?=lang('pay.mainwalltbal')?>:</label>
																<div class="input-group add-on">
																   	<input type="text" class="form-control mainWalletBalance" id="txtAppMainWalletBalance" readonly/>
																   	<span class="input-group-btn">
																        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtAppMemberSince"><i class="glyphicon glyphicon-copy"></i></button>
																   	</span>
																</div>
															</div>

															<?php foreach ($game_platforms as $game_platform): ?>
																<div class="col-md-3">
																	<label for="subWalletBalance<?=$game_platform['id']?>"><?=$game_platform['system_code']?>:</label>
																	<div class="input-group add-on">
																	   	<input type="text" class="form-control subWalletBalance subWalletBalance<?=$game_platform['id']?>" id="txtAppSubWalletBalance<?=$game_platform['id']?>" readonly/>
																	   	<span class="input-group-btn">
																	        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtAppSubWalletBalance<?=$game_platform['id']?>"><i class="glyphicon glyphicon-copy"></i></button>
																	   	</span>
																	</div>
																</div>
															<?php endforeach?>

															<div class="col-md-3">
																<label for="totalBalance"><?=lang('pay.totalbal')?>:</label>
																<div class="input-group add-on">
																   	<input type="text" class="form-control totalBalance" id="txtAppTotalBalance" readonly/>
																   	<span class="input-group-btn">
																        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtAppTotalBalance"><i class="glyphicon glyphicon-copy"></i></button>
																   	</span>
																</div>
															</div>

															<div class="col-md-3">
																<label for="withdrawalTransactionId"><?=lang('Withdrawal Code')?>:</label>
																<div class="input-group add-on">
																   	<input type="text" class="form-control withdrawalTransactionId" id="txtAppWithdrawalTransactionId" readonly/>
																   	<span class="input-group-btn">
																        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtAppWithdrawalTransactionId"><i class="glyphicon glyphicon-copy"></i></button>
																   	</span>
																</div>
															</div>
														</div>
													</div>
                                                    <?php include dirname(__FILE__) . '/withdrawal_list/withdraw_condition_details.php';?>
													<!-- duplicate account info -->
													<div class="col-md-12">
														<div class="row playerDuplicateAccountInfoPanel">
					                                        <div class="col-md-12">
					                                            <fieldset>
					                                                <legend class='togvis'><?=lang('pay.duplicateAccountList')?><span>[-]</span></legend>
					                                                <div class="col-md-12 ">
					                                                    <div id="logList" class="table-responsive">
					                                                        <table class="duplicateTable table table-striped table-hover table-bordered"  width=100%>
					                                                            <thead>
						                                                            <tr>
		        																		<?php $dup_enalbed_column = $this->utils->getConfig('duplicate_account_info_enalbed_condition') ?>
						                                                                <th><?= lang('Username'); ?></th>
						                                                                <th><?= lang('Total Rate'); ?></th>
						                                                                <th><?= lang('Possibly Duplicate'); ?></th>
																						<?php if (in_array('ip', $dup_enalbed_column)) : ?>
							                                                                <th><?= lang('Reg IP'); ?></th>
							                                                                <th><?= lang('Login IP'); ?></th>
							                                                                <th><?= lang('Deposit IP'); ?></th>
							                                                                <th><?= lang('Withdraw IP'); ?></th>
							                                                                <th><?= lang('Transfer Main To Sub IP'); ?></th>
							                                                                <th><?= lang('Transfer Sub To Main IP'); ?></th>
						                                                                <?php endif; ?>
						                                                                <?php if (in_array('realname', $dup_enalbed_column)) : ?>
						                                                                	<th><?= lang('Real Name'); ?></th>
						                                                                <?php endif; ?>
						                                                                <?php if (in_array('password', $dup_enalbed_column)) : ?>
						                                                                	<th><?= lang('Password'); ?></th>
						                                                                <?php endif; ?>
						                                                                <?php if (in_array('email', $dup_enalbed_column)) : ?>
						                                                                	<th><?= lang('Email'); ?></th>
						                                                                <?php endif; ?>
						                                                                <?php if (in_array('mobile', $dup_enalbed_column)) : ?>
						                                                                	<th><?= lang('Mobile'); ?></th>
						                                                                <?php endif; ?>
						                                                                <?php if (in_array('address', $dup_enalbed_column)) : ?>
						                                                                	<th><?= lang('Address'); ?></th>
						                                                                <?php endif; ?>
						                                                                <?php if (in_array('city', $dup_enalbed_column)) : ?>
						                                                                	<th><?= lang('City'); ?></th>
						                                                                <?php endif; ?>
						                                                                <?php if (in_array('country', $dup_enalbed_column)) : ?>
						                                                                	<th><?= lang('pay.country') ?></th>
						                                                                <?php endif; ?>
						                                                                <?php if (in_array('cookie', $dup_enalbed_column)) : ?>
						                                                                	<th><?= lang('Cookies'); ?></th>
						                                                                <?php endif; ?>
						                                                                <?php if (in_array('referrer', $dup_enalbed_column)) : ?>
						                                                                	<th><?= lang('From'); ?></th>
						                                                                <?php endif; ?>
						                                                                <?php if (in_array('device', $dup_enalbed_column)) : ?>
						                                                                	<th><?= lang('Device'); ?></th>
						                                                                <?php endif; ?>
						                                                            </tr>
					                                                            </thead>
					                                                        </table>
					                                                    </div>
					                                                </div>
					                                            </fieldset>
					                                        </div>
														</div>
													</div>
													<!-- end duplicate account info -->
												</div>
											</div>

											<hr/>
											<h4><?=lang('pay.withdetl')?></h4>
											<hr/>

											<!-- start payment method -->
											<div class="paymentMethodSection">
												<div class="row" style="margin-bottom:20px">
													<div class="col-md-12">
														<div class="col-md-2">
															<label for="withdrawalAmount"><?=lang('pay.withamt')?>:</label>
															<div class="input-group add-on">
															   	<input type="text" class="form-control withdrawalAmount" id="txtAppWithdrawalAmount" readonly/>
															   	<span class="input-group-btn">
															        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtAppWithdrawalAmount"><i class="glyphicon glyphicon-copy"></i></button>
															   </span>
															</div>
														</div>
														<div class="col-md-2">
															<label for="withdrawalCode"><?=lang("Withdraw Code")?>:</label>
															<div class="input-group add-on">
															   	<input type="text" class="form-control withdrawalCode" id="txtAppWithdrawalCode" readonly/>
															   	<span class="input-group-btn">
															        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtAppWithdrawalCode"><i class="glyphicon glyphicon-copy"></i></button>
															   	</span>
															</div>
														</div>

														<div class="col-md-2">
															<label for="currency"><?=lang('pay.curr')?>:</label>
															<div class="input-group add-on">
															   	<input type="text" class="form-control currency" id="txtAppCurrency" readonly/>
															   	<span class="input-group-btn">
															        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtAppCurrency"><i class="glyphicon glyphicon-copy"></i></button>
															   	</span>
															</div>
														</div>

														<div class="col-md-3">
															<label for="dateDeposited"><?=lang('pay.reqtdon')?>:</label>
															<div class="input-group add-on">
															   	<input type="text" class="form-control dateDeposited" id="txtAppDateDeposited" readonly/>
															   	<span class="input-group-btn">
															        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtAppCurrency">
															        	<i class="glyphicon glyphicon-copy"></i>
															        </button>
															   	</span>
															</div>
														</div>

														<div class="col-md-3">
															<label for="ipLoc"><?=lang('pay.withip') . ' ' . lang('lang.and') . ' ' . lang('pay.locatn')?>:</label>
															<div class="input-group add-on">
															   	<input type="text" class="form-control ipLoc" id="txtAppIpLoc" readonly/>
															   	<span class="input-group-btn">
															        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtAppIpLoc"><i class="glyphicon glyphicon-copy"></i></button>
															   	</span>
															</div>
														</div>
													</div>
												</div>

												<div class="row">
													<div class="col-md-12">
														<div class="col-md-3">
															<label for="bankName" class="lbl-bankname">
																<?=lang('pay.bankname')?>:
															</label>
															<div class="input-group add-on">
															   	<input type="text" class="form-control bankName" id="txtAppBankName" readonly/>
															   	<span class="input-group-btn">
															        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtAppBankName"><i class="glyphicon glyphicon-copy"></i></button>
															   	</span>
															</div>
															<input type="hidden" class="playerBankDetailsId" value=""/>
															<label class="lbl-remarks">
																<span class="customBank hide">
																	<label class="custom-bank-label">
																		<?=lang('Approved this custom bank name')?>
																		<a href="javascript:void(0)" class="activate-bank"><span><?=lang('lang.yes')?></span></a>
																	  	|
																	  	<a href="javascript:void(0)" class="deactivate-bank"><span><?=lang('lang.no')?></span></a>
																	</label>
																</span>
															</label>
														</div>

														<div class="col-md-3">
															<label for="bankAccountName"><?=lang('pay.bank') . ' ' . lang('pay.acctname')?>:</label>
															<div class="input-group add-on">
															   	<input type="text" class="form-control bankAccountName" id="txtAppBankAccountName" readonly/>
															   	<span class="input-group-btn">
															        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtAppBankAccountName"><i class="glyphicon glyphicon-copy"></i></button>
															   	</span>
															</div>
														</div>

														<div class="col-md-3">
															<label for="bankAccountNumber"><?=lang('pay.bank') . ' ' . lang('pay.acctnumber')?>:</label>
															<div class="input-group add-on">
															   	<input type="text" class="form-control bankAccountNumber" id="txtAppBankAccountNumber" readonly/>
															   	<span class="input-group-btn">
															        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtAppBankAccountNumber"><i class="glyphicon glyphicon-copy"></i></button>
															   	</span>
															</div>
														</div>

														<div class="col-md-3">
															<label for="bankAccountBranch"><?=lang('pay.bank') . ' ' . lang('pay.acctbranch')?>:</label>
															<div class="input-group add-on">
															   	<input type="text" class="form-control bankAccountBranch" id="txtAppBankAccountBranch" readonly/>
															   	<span class="input-group-btn">
															        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtAppBankAccountNumber"><i class="glyphicon glyphicon-copy"></i></button>
															   	</span>
															</div>
														</div>
													</div>
												</div>

												<div class="row">
													<div class="col-md-12">
														<div class="col-md-3">
															<label for="bankPhone"><?=lang('pay.bankPhone')?>:</label>
															<div class="input-group add-on">
															   	<input id="txtAppbankPhone" type="text" class="form-control bankPhone" readonly/>
															   	<span class="input-group-btn">
															        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtAppbankPhone"><i class="glyphicon glyphicon-copy"></i></button>
															   	</span>
															</div>
														</div>
														<div class="col-md-6">
															<label for="bankAddress"><?=lang('pay.bankAddress')?>:</label>
															<div class="input-group add-on">
															   	<input id="txtAppbankAddress" type="text" class="form-control bankAddress" readonly/>
															   	<span class="input-group-btn">
															        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtAppbankAddress"><i class="glyphicon glyphicon-copy"></i></button>
															   	</span>
															</div>
														</div>
													</div>
												</div>
											</div>
											<!-- end payment method -->

											<hr/>
											<div class="row">
												<div class="col-md-1">
													<label for="depositMethodApprovedBy"><?=lang('pay.apprvby')?>:</label>
												</div>

												<div class="col-md-3">
													<div class="input-group add-on">
													   	<input type="text" class="form-control" id="depositMethodApprovedBy" readonly>
													   	<span class="input-group-btn">
													        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#depositMethodApprovedBy"><i class="glyphicon glyphicon-copy"></i></button>
													   </span>
													</div>
													<br/>
												</div>

												<div class="col-md-1">
													<label for="depositMethodDateApproved"><?=lang('pay.datetimeapprv')?>:</label>
												</div>

												<div class="col-md-3">
													<div class="input-group add-on">
													   	<input type="text" class="form-control" id="depositMethodDateApproved" readonly>
													   	<span class="input-group-btn">
													        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#depositMethodDateApproved"><i class="glyphicon glyphicon-copy"></i></button>
													   	</span>
													</div>
													<br/>
												</div>
											</div>
											<div class="row">
												<div class="col-md-1">
													<label for=""><?=lang('con.bnk10')?>:</label>
												</div>

												<div class="col-md-3">
													<input type="number" class="form-control" id="transaction_fee" >
													<br/>
												</div>

												<div class="col-md-1">
													<label for="withdraw_method_display"><?=lang('Payment Method')?>:</label>
												</div>

												<div class="col-md-3">
													<div class="input-group add-on">
													   	<input type="text" class="form-control" id="withdraw_method_display" readonly>
													   	<span class="input-group-btn">
													        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#withdraw_method_display"><i class="glyphicon glyphicon-copy"></i></button>
													   	</span>
													</div>
													<input type="hidden" class="form-control" id="withdraw_id_hidden" readonly>
												</div>
											</div>
											<hr/>

											<?php if ($this->utils->isEnabledFeature('enable_withdrawal_declined_category') && !empty($withdrawalDeclinedCategory)) : ?>
												<hr/>
												<div class="row">
													<label class="col-md-12"><?=lang('Withdrawal Declined Category');?></label>
								                    <div class="col-md-3">
								                        <select class="form-control declined-category-id" id="declined_category_id_for_paid" name="declined_category_id_for_paid">
								                        	<option class="declined-category-id" value="">*** <?= lang('select_decline_category') ?> ***</option>
								                            <?php foreach ($withdrawalDeclinedCategory as $key => $value): ?>
								                                  <option class="declined-category-id" value="<?= $value['id'] ?>"><?= lang($value['category_name']) ?></option>
								                            <?php endforeach; ?>
								                        </select>
								                    </div>
												</div>
											<?php endif; ?>

											<!--Start payment notes -->
											<div class="row">
											    <div class="col-md-12">
											        <h4 class="page-header"><?=lang('lang.notes');?></h4>
											    </div>
											    <div class="col-md-6">
											        <label><?=lang('Internal Note Record')?>:</label>
											        <textarea class="form-control withdraw-internal-notes notes-textarea" readonly></textarea>
											    </div>
											    <div class="col-md-6">
											        <label><?=lang('External Note Record')?>:</label>
											        <textarea class="form-control withdraw-external-notes notes-textarea" readonly></textarea>
											    </div>
												<div class="col-md-6">
									                <label><?=lang('Add Internal Note')?>:</label>
									                <textarea id="approveInternalRemarksTxt" class="form-control notes-textarea" maxlength="500"></textarea>
									                <button type='button' class="btn btn-primary pull-right add-notes-btn" id="approveinternalnotebtn" onclick="addNotes('approveinternalnotebtn','2')">
									                	<span class="glyphicon glyphicon-plus" aria-hidden="true" style="padding-right: 4px"></span><?=lang('Add')?>
									                </button>
									            </div>
									            <div class="col-md-6">
									                <label><?=lang('Add External Note')?>:</label>
									                <textarea id="approveExternalRemarksTxt" class="form-control notes-textarea" maxlength="500"></textarea>
									                <button type='button' class="btn btn-primary pull-right add-notes-btn" id="approveexternalnotebtn" onclick="addNotes('approveexternalnotebtn','3')">
									                	<span class="glyphicon glyphicon-plus" aria-hidden="true" style="padding-right: 4px"></span><?=lang('Add')?>
									                </button>
									            </div>
									            <div class="col-md-12 transactionStatusMsg text-danger"></div>
											</div>
											<hr/>
											<!--End payment notes -->

											<div class="row">
												<input type="hidden" class="form-control request_walletAccountIdVal" id="walletAccountIdForPaid" readonly />
												<div class="col-md-12 actions_sec">
													<?php if ($this->permissions->checkPermissions('set_withdraw_status_to_paid')) {?>
														<?php if ($conditions['dwStatus'] != wallet_model::LOCK_API_UNKNOWN_STATUS) {?>
                                                        <?php if($this->permissions->checkPermissions('ignore_vip_daily_withdrawal_maximum_amount_settings_when_approve')): ?>
															<input type="checkbox" name="ignoreWithdrawalAmountLimit" id="ignoreWithdrawalAmountLimit" class="response-sec" style="margin-bottom: 20px"/>
															<label for="ignoreWithdrawalAmountLimit" class="response-sec"><?=lang('pay.ignamtlimit')?></label>
                                                        <?php endif;?>
                                                        <?php if($this->permissions->checkPermissions('ignore_vip_daily_withdrawal_maximum_times_settings_when_approve')): ?>
															<input type="checkbox" name="ignoreWithdrawalTimesLimit" id="ignoreWithdrawalTimesLimit" class="response-sec" style="margin-bottom: 20px"/>
															<label for="ignoreWithdrawalTimesLimit" class="response-sec"><?=lang('pay.igntimlimit')?></label>
                                                        <?php endif;?>
														<br class="response-sec" />
														<?php }?>
														<div class="payment-submitted-msg text-danger" style="display:none; margin-bottom:10px">
															<?=lang('Payment request submitted')?>
														</div>
                                                       <!--  <?php if($this->utils->isEnabledFeature('trigger_deposit_list_send_message')): ?>
                                                            <input type="hidden" name="playerId" id="playerId" value="">
                                                            <input type="hidden" name="msgSubject" id="msgSubject" value="<?=lang('trigger_deposit_list_send_message.subject')?>">
                                                            <select id="sectpl" onchange="fillcontent(this.options[this.selectedIndex].value)">
                                                                <option value=""><?=lang('deposit.msgtpl')?></option>
                                                            </select>
                                                        <?php endif;?> -->
														<?php if($conditions['dwStatus'] != wallet_model::LOCK_API_UNKNOWN_STATUS) { ?>
															<button class="btn btn-md btn-primary response-sec" id="btn_check_withdraw" onclick="return checkWithdrawStatus(this)" style="display:none"><?=lang('Check Withdraw Status')?></button>
															<span class="withdraw_method" style="display:none">
																<?php foreach($withdrawAPIs as $id=>$name) : ?>
																<input type="button" data-withdraw-api="<?=$id?>" onclick="return setWithdrawToPayProc(<?=$id?>, this)" value="<?=lang($name)?>" class="btn btn-primary" id="api_<?=$id?>" />
																<?php endforeach; ?>
																<input type="button" data-withdraw-api="0" onclick="return setWithdrawToPayProc(0, this)" value="<?=lang('Manual Payment')?>" class="btn btn-primary" id="api_0" />
															</span>
															<input type="button" value="<?php echo lang('lang.paid'); ?>" class="btn btn-primary response-sec withdraw-paid-btn" id="paid_btn" onclick="return setWithdrawToPaid(this)" />
															<button class="btn btn-md btn-danger response-sec" id="decline_btn" onclick="return respondToWithdrawalDeclinedForPaid()"><?=lang('pay.declnow')?></button>
														<?php } else { ?>
															<button type="button" class="btn btn-md btn-primary response-sec" id="approve_btn_unlock_api_unknown" onclick="unlockApiUnknown()"><?=lang('pay.unlocknow')?></button>
														<?php } ?>
														<button class="btn btn-md btn-default closeApproved" class="close" data-dismiss="modal"><?=lang('lang.close');?></button>
													<?php }?>
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
		<div class="modal-dialog modal_full">
			<div class="modal-content modal-content-three">
				<div class="modal-header">
					<a class='notificationRefreshList' href="<?=site_url('payment_management/refreshList/withdrawalDeclined')?>">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only"><?=lang("lang.close")?></span></button>
					</a>
					<h4 class="modal-title" id="myModalLabel"><?=lang("pay.declwithdetl")?></h4>
				</div>

				<div class="modal-body">
					<div class="row">
						<div class="col-md-12" id="playerDeclinedDetailsCheckPlayer">
							<!-- Withdrawal transaction -->
							<div class="row">
								<div class="col-md-12">
									<div class="panel panel-primary">
										<div class="panel-heading">
											<h4 class="panel-title">
												<?=lang("pay.declwithinfo")?>
												<a href="#depositInformation" id="hide_player_transac_history" class="btn btn-default btn-sm pull-right">
													<i class="glyphicon glyphicon-chevron-up" id="hide_player_transac_history_up"></i>
												</a>
												<div class="clearfix"></div>
											</h4>
										</div>

										<div class="panel-body" id="declined_deposit_info_panel_body" style="display: none;">
											<!-- <div class="row"> -->
												<!-- <div class="row"> -->
												<div class="col-md-12">
													<div class="col-md-3">
														<label for="userName"><?=lang("pay.username")?>:</label>
														<div class="input-group add-on">
															<input type="text" class="form-control userName" id="txtDecUserName" readonly/>
															<span class="input-group-btn">
																<button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtDecUserName"><i class="glyphicon glyphicon-copy"></i></button>
															</span>
														</div>
													</div>

													<div class="col-md-3">
														<label for="playerName"><?=lang("pay.realname")?>:</label>
														<div class="input-group add-on">
															<input type="text" class="form-control playerName" id="txtDecPlayerName" readonly/>
															<span class="input-group-btn">
																<button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtDecPlayerName"><i class="glyphicon glyphicon-copy"></i></button>
															</span>
														</div>
													</div>

													<div class="col-md-3">
														<label for="playerLevel"><?=lang('pay.playerlev')?>:</label>
														<div class="input-group add-on">
															<input type="text" class="form-control playerLevel" id="txtDecPlayerLevel" readonly/>
															<span class="input-group-btn">
																<button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtDecPlayerLevel"><i class="glyphicon glyphicon-copy"></i></button>
															</span>
														</div>
													</div>

													<div class="col-md-3">
														<label for="memberSince"><?=lang('pay.memsince')?>: </label>
														<div class="input-group add-on">
															<input type="text" class="form-control memberSince" id="txtDecMemberSince" readonly>
															<span class="input-group-btn">
																<button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtDecMemberSince"><i class="glyphicon glyphicon-copy"></i></button>
															</span>
														</div>
													</div>
												</div>
												<!-- </div> -->
												<!-- <div class="row"> -->
												<div class="col-md-12">
													<br/>
													<div class="col-md-3">
														<label for="mainWalletBalance"><?=lang('pay.mainwalltbal')?>:</label>
														<div class="input-group add-on">
															<input type="text" class="form-control mainWalletBalance" id="txtDecMainWalletBalance" readonly/>
															<span class="input-group-btn">
																<button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtDecMainWalletBalance"><i class="glyphicon glyphicon-copy"></i></button>
															</span>
														</div>
													</div>

													<?php foreach ($game_platforms as $game_platform): ?>
														<div class="col-md-3">
															<label for="subWalletBalance<?=$game_platform['id']?>"><?=$game_platform['system_code']?>:</label>
															<div class="input-group add-on">
																<input type="text" class="form-control subWalletBalance subWalletBalance<?=$game_platform['id']?>" id="txtDecSubWalletBalance<?=$game_platform['id']?>" readonly/>
																<span class="input-group-btn">
																	<button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtDecSubWalletBalance<?=$game_platform['id']?>"><i class="glyphicon glyphicon-copy"></i></button>
																</span>
															</div>
														</div>
													<?php endforeach?>

													<div class="col-md-3">
														<label for="totalBalance"><?=lang('pay.totalbal')?>:</label>
														<div class="input-group add-on">
															<input type="text" class="form-control totalBalance" id="txtDecTotalBalance" readonly/>
															<span class="input-group-btn">
																<button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtDecTotalBalance"><i class="glyphicon glyphicon-copy"></i></button>
															</span>
														</div>
													</div>
												</div>
												<!-- </div> -->
                                               <?php include dirname(__FILE__) . '/withdrawal_list/withdraw_condition_details.php';?>
												<br/>
												<!-- start payment method -->
												<hr/>
												<h4><?=lang('pay.paymethod') . ' ' . lang('lang.details')?></h4>
												<hr/>
												<!-- start payment method -->
												<div class="paymentMethodSection">
													<div class="row" style="margin-bottom:20px">
															<div class="col-md-12">
																<div class="col-md-2">
																	<label for="withdrawalAmount"><?=lang('pay.withamt')?>:</label>
																	<div class="input-group add-on">
																	   <input id="withdrawAmt" type="text" class="form-control withdrawalAmount" readonly/>
																	   <span class="input-group-btn">
																	        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#withdrawAmt"><i class="glyphicon glyphicon-copy"></i></button>
																	   </span>
																	</div>
																</div>

	                                                            <div class="col-md-2">
	                                                                <label for="withdrawalCode"><?=lang('Withdraw Code')?>:</label>
	                                                                <div class="input-group add-on">
	                                                                    <input id="txtWithdrawCode" type="text" class="form-control withdrawalCode" readonly/>
																	   <span class="input-group-btn">
																	        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtWithdrawCode"><i class="glyphicon glyphicon-copy"></i></button>
																	   </span>
	                                                                </div>
	                                                            </div>

																<div class="col-md-2">
																	<label for="currency"><?=lang('pay.curr')?>:</label>
																	<div class="input-group add-on">
																	   <input type="text" class="form-control currency" id="txtDecCurrency" readonly/>
																	   <span class="input-group-btn">
																	        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtDecCurrency"><i class="glyphicon glyphicon-copy"></i></button>
																	   </span>
																	</div>
																</div>

																<div class="col-md-3">
																	<label for="dateDeposited"><?=lang('pay.reqtdon')?>:</label>
																	<div class="input-group add-on">
																	   <input type="text" class="form-control dateDeposited" id="txtDecDateDeposited" readonly/>
																	   <span class="input-group-btn">
																	        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtDecDateDeposited"><i class="glyphicon glyphicon-copy"></i></button>
																	   </span>
																	</div>
																</div>

																<div class="col-md-3">
																	<label for="ipLoc"><?=lang('pay.withip') . ' ' . lang('lang.and') . ' ' . lang('pay.locatn')?>:</label>
																	<div class="input-group add-on">
																	   <input type="text" class="form-control ipLoc" id="txtDecIpLoc" readonly/>
																	   <span class="input-group-btn">
																	        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtDecIpLoc"><i class="glyphicon glyphicon-copy"></i></button>
																	   </span>
																	</div>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-md-12">
																<div class="col-md-3">
																	<label for="bankName"><?=lang('pay.bankname')?>:</label>
																	<div class="input-group add-on">
																	   <input type="text" class="form-control bankName" id="txtDecBankName" readonly/>
																	   <span class="input-group-btn">
																	        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtDecBankName"><i class="glyphicon glyphicon-copy"></i></button>
																	   </span>
																	</div>
																</div>

																<div class="col-md-3">
																	<label for="bankAccountName"><?=lang('pay.bank') . ' ' . lang('pay.acctname')?>:</label>
																	<div class="input-group add-on">
																	   <input type="text" class="form-control bankAccountName" id="txtDecBankAccountName" readonly/>
																	   <span class="input-group-btn">
																	        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtDecBankAccountName"><i class="glyphicon glyphicon-copy"></i></button>
																	   </span>
																	</div>
																</div>

																<div class="col-md-3">
																	<label for="bankAccountNumber"><?=lang('pay.bank') . ' ' . lang('pay.acctnumber')?>:</label>
																	<div class="input-group add-on">
																	   <input type="text" class="form-control bankAccountNumber" id="txtDecBankAccountNumber" readonly/>
																	   <span class="input-group-btn">
																	        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtDecBankAccountNumber"><i class="glyphicon glyphicon-copy"></i></button>
																	   </span>
																	</div>
																</div>

																<div class="col-md-3">
																	<label for="bankAccountBranch"><?=lang('pay.bank') . ' ' . lang('pay.acctbranch')?>:</label>
																	<div class="input-group add-on">
																	   <input type="text" class="form-control bankAccountBranch" id="txtDecBankAccountBranch" readonly/>
																	   <span class="input-group-btn">
																	        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtDecBankAccountBranch"><i class="glyphicon glyphicon-copy"></i></button>
																	   </span>
																	</div>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-md-12">
																<div class="col-md-3">
																	<label for="bankPhone"><?=lang('pay.bankPhone')?>:</label>
																	<div class="input-group add-on">
																	   <input type="text" class="form-control bankPhone" id="txtDecbankPhone" readonly/>
																	   <span class="input-group-btn">
																	        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtDecbankPhone"><i class="glyphicon glyphicon-copy"></i></button>
																	   </span>
																	</div>
																</div>
																<div class="col-md-6">
																	<label for="bankAddress"><?=lang('pay.bankAddress')?>:</label>
																	<div class="input-group add-on">
																	   <input type="text" class="form-control bankAddress" id="txtDecbankAddress" readonly/>
																	   <span class="input-group-btn">
																	        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtDecbankAddress"><i class="glyphicon glyphicon-copy"></i></button>
																	   </span>
																	</div>
																</div>
															</div>
														</div>
												</div>
												<!-- end payment method -->
												<hr/>

												<div class="row">
													<div class="col-md-12">
														<div class="col-md-3">
															<label for="withdrawalMethodDeclinedBy" style="display:none">
																<?=lang('pay.declby')?>:
															</label>
															<label for="withdrawalMethodApprovedBy" style="display:none">
																<?=lang('pay.apprvby')?>:
															</label>
															<div class="input-group add-on">
															   <input type="text" class="form-control withdrawalMethodDeclinedBy" id="txtDecWithdrawalMethodDeclinedBy" readonly>
															   <span class="input-group-btn">
															        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtDecWithdrawalMethodDeclinedBy"><i class="glyphicon glyphicon-copy"></i></button>
															   </span>
															</div>
														</div>
														<div class="col-md-3">
															<label for="withdrawalMethodDateDeclined" style="display:none">
																<?=lang('pay.datetimedecl')?>:
															</label>
															<label for="withdrawalMethodDateApproved" style="display:none">
																<?=lang('pay.datetimeapprv')?>:
															</label>
															<div class="input-group add-on">
															   <input type="text" class="form-control withdrawalMethodDateDeclined" id="txtDecWithdrawalMethodDateDeclined" readonly>
															   <span class="input-group-btn">
															        <button class="btn btn-default btn-copy" type="button" data-clipboard-target="#txtDecWithdrawalMethodDateDeclined"><i class="glyphicon glyphicon-copy"></i></button>
															   </span>
															</div>
														</div>
													</div>
												</div>
												<hr/>
												<!--Start payment notes -->
												<div class="row">
												    <div class="col-md-12">
												        <h4 class="page-header"><?=lang('lang.notes');?></h4>
												    </div>
												    <div class="col-md-6">
												        <label><?=lang('Internal Note Record')?>:</label>
												        <textarea class="form-control withdraw-internal-notes notes-textarea" readonly></textarea>
												    </div>
												    <div class="col-md-6">
												        <label><?=lang('External Note Record')?>:</label>
												        <textarea class="form-control withdraw-external-notes notes-textarea" readonly></textarea>
												    </div>
												</div>
												<hr/>
												<!--End payment notes -->
												<hr/>
												<div class="col-md-12" id="playerDeclinedDetailsRepondBtn">
													<input type="hidden" class="form-control walletAccountIdVal" readonly/>
													<input type="hidden" class="form-control" id="declinedPlayerPromoIdVal" readonly/>
												</div>
											<!-- </div> -->
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
</div>
<!-- end declinedDetailsModal-->

<div class="row">
	<div class="modal fade" id="lockedModal" style="margin-top:130px !important;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title"><?= lang('Locked transaction') ?></h4>
				</div>
				<input type="hidden" id="hiddenId">
				<div class="modal-body">
					<p id="locked-message"></p>
				</div>
				<div class="modal-footer">
					<!--				<a data-dismiss="modal" class="btn btn-default">--><?//= lang('lang.no'); ?><!--</a>-->
					<!--				<a class="btn btn-primary" id="deleteBtn"><i class="fa"></i> --><?//= lang('lang.yes'); ?><!--</a>-->
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="modal fade" id="batchProcessModal" style="margin-top:130px !important;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title batch-process-title"><?= lang('Batch Process Summary')?></h4>
				</div>
				<div class="modal-body">
					<div class="progress">
					  	<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
					    	<span class="progressbar-text"><?= lang('Processing....') ?></span>
					  	</div>
					</div>
					<table class="table table-striped" id="batchProcessTable">
						<thead>
							<tr>
								<th width="30"><?= lang('lang.status') ?></th>
								<th width="50"><?= lang('ID') ?></th>
								<th><?= lang('Remarks') ?></th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
				<div class="modal-footer"></div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="lockedModal" style="margin-top:130px !important;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title"><?= lang('Locked transaction') ?></h4>
			</div>
			<input type="hidden" id="hiddenId">
			<div class="modal-body">
				<p id="locked-message"></p>
			</div>
			<div class="modal-footer">
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	// Tooltip
	$('.btn-copy').tooltip({
	  trigger: 'click',
	  placement: 'bottom'
	});

	function setTooltip(btn, message) {
	  $(btn).tooltip('hide')
	    .attr('data-original-title', message)
	    .tooltip('show');
	}

	function hideTooltip(btn) {
	  setTimeout(function() {
	    $(btn).tooltip('hide');
	  }, 1000);
	}

	// Clipboard
	var clipboard = new Clipboard('.btn-copy');
	clipboard.on('success', function(e) {
	    setTooltip(e.trigger, 'Copied!');
	  	hideTooltip(e.trigger);
	});
	clipboard.on('error', function(e) {
		setTooltip(e.trigger, 'Failed!');
	  	hideTooltip(e.trigger);
	});

	var currentWCPlayerId='';

	var success_trans = 0;
	var fail_trans = 0;
	var totalTransation = 0;
	var totalCompleteTrans = 0;
	var currentLang = "<?= $this->language_function->getCurrentLanguage(); ?>";

	$(function(){
		var base_url = "<?=site_url()?>";

		$('body').on('click', '.activate-bank', function(e){
			e.preventDefault();

			var bankTypeId = $(this).data('bankid');
			$('.customBank').html('Loading...');

			var bankName = $('.bankName').val();
				playerBankDetailsId = $('.playerBankDetailsId').val();

			$.post(base_url + '/payment_management/newBankType/' + playerBankDetailsId, { bankname: bankName }, function(data){

				if( data.msg == false || data.msg == 0 ) return $('.customBank').html(data.msg);

				var bankTypeId = data.msg;
				$('.customBank').remove();
				var targetUrl = base_url + 'payment_management/editBankType/' + bankTypeId;
				window.open(targetUrl, '_blank', 'toolbar=0,location=0,menubar=0');

			});

		});

		$('body').on('click', '.confirm-bank-approval', function(e){
			e.preventDefault();
		});

		$('body').on('click', '.deactivate-bank', function(e){
			e.preventDefault();
			$('.customBank').addClass('hide');
		});

		$('body').on('click', '.cancel-bank-approval', function(e){
			e.preventDefault();
			$('.custom-bank-label').removeClass('hide');
			$('.confirm, .remarks').addClass('hide');
		});


		$('#requestDetailsModal, #approvedDetailsModal, #declinedDetailsModal,#paidDetailsModal').on('hidden.bs.modal', function (e) {
			var withdrawId = $('.request_walletAccountIdVal').val();
			if(!withdrawId) {
				withdrawId = $('.walletAccountIdVal').val();
			}
			unlockedTransaction(withdrawId);
		});

		$('#batchProcessModal').on('hidden.bs.modal', function () {
		    window.location.reload();
		});

		$("#chkAll").click(function(){
		    $('.chk-order-id').not(this).prop('checked', this.checked);
		});
    });

	function setWithdrawToPayProc(withdrawAPI, btn){
		if(!confirm('<?=lang('Are you sure to make payment for this withdrawal request?')?>')) {
			return;
		}

		var withdrawId = $('.request_walletAccountIdVal').val();
		if(!withdrawId) {
			withdrawId = $('.walletAccountIdVal').val();
		}
		var amount = $('#withdrawAmt').val();
		var max_transaction_fee = parseFloat(amount); //*5/100;
		var transaction_fee = parseFloat($('#transaction_fee').val());
		if(isNaN(max_transaction_fee)){
			max_transaction_fee=0;
		}
		if(isNaN(transaction_fee)){
			transaction_fee=0;
		}

		var ignoreWithdrawalAmountLimit=$('#ignoreWithdrawalAmountLimit').is(":checked") ? '1' : '0';
		var ignoreWithdrawalTimesLimit=$('#ignoreWithdrawalTimesLimit').is(":checked") ? '1' : '0';
		if(transaction_fee!=''){
			if(transaction_fee>max_transaction_fee || transaction_fee<0){
				alert("<?php echo lang("Invalid transaction fee");?>");
				return;
			}
		}

        unlockedTransaction(withdrawId);

		var withdrawal_api_before_submit_dialog = <?= json_encode($this->CI->utils->getConfig('withdrawal_api_before_submit_dialog')) ?>;
		let value = null;
        if(withdrawal_api_before_submit_dialog[withdrawAPI]){
            BootstrapDialog.show({
                title: withdrawal_api_before_submit_dialog[withdrawAPI]['title'],
              message: withdrawal_api_before_submit_dialog[withdrawAPI]['message'],
              buttons: [{
                label: withdrawal_api_before_submit_dialog[withdrawAPI]['confirm_label'],
                action: function(dialog) {
                    value = dialog.getModalBody().find('#player_input').val();
                    $.ajax({
                        'url' : `${base_url}payment_management/setWithdrawToPayProc/${withdrawId}/${withdrawAPI}/${value}`,
                        'type' : 'POST',
                        'dataType' : "json",
                        'data': {
							'transaction_fee' :transaction_fee,
							'ignoreWithdrawalAmountLimit': ignoreWithdrawalAmountLimit,
							'ignoreWithdrawalTimesLimit': ignoreWithdrawalTimesLimit
						},
                        'success' : function(data){
                        // The call to API could take very long. We need to rely on API callback to know the status
                            //show on
                            if(data['message']){
                                $('.payment-submitted-msg').html(data['message']);
                            }
                        }
                    });
                    dialog.close();
                }
              }, {
                label: withdrawal_api_before_submit_dialog[withdrawAPI]['close_label'],
                action: function(dialog) {
                  dialog.close();
                  return;
                }
              }]
            });
        }
        else{
            $.ajax({
                'url' : `${base_url}payment_management/setWithdrawToPayProc/${withdrawId}/${withdrawAPI}/${value}`,
                'type' : 'POST',
                'dataType' : "json",
                'data': {
					'transaction_fee' :transaction_fee,
					'ignoreWithdrawalAmountLimit': ignoreWithdrawalAmountLimit,
					'ignoreWithdrawalTimesLimit': ignoreWithdrawalTimesLimit
				},
                'success' : function(data){
                // The call to API could take very long. We need to rely on API callback to know the status
                    //show on
                    if(data['message']){
                        $('.payment-submitted-msg').html(data['message']);
                    }
                }
            });
        }

        $('.response-sec').hide();
        $('.withdraw_method').hide();
        $('.payment-submitted-msg').show();

        return false;
	}

	function setWithdrawToPaid(btn, skipConfirm){
		if(!skipConfirm && !confirm('<?=lang('Are you sure payment for this withdrawal request has been made?')?>')) {
			return;
		}

		var withdrawId = $('.request_walletAccountIdVal').val();
		if(!withdrawId) {
			withdrawId = $('.walletAccountIdVal').val();
		}
		var amount = $('#withdrawAmt').val();
		var max_transaction_fee = parseFloat(amount); //*5/100;
		var transaction_fee = parseFloat($('#transaction_fee').val());
		if(isNaN(max_transaction_fee)){
			max_transaction_fee=0;
		}
		if(isNaN(transaction_fee)){
			transaction_fee=0;
		}

		if(transaction_fee!=''){
			if(transaction_fee>max_transaction_fee || transaction_fee<0){
				alert("<?php echo lang("Invalid transaction fee");?>");
				return;
			}
		}
		withdrawId=parseInt(withdrawId, 10);
		if(withdrawId<=0){
			alert("<?php echo lang('Sorry, still loading');?>");
			return;
		}

		unlockedTransaction(withdrawId);

		$.ajax({
			'url' : base_url +'payment_management/setWithdrawToPaid/'+withdrawId,
			'type' : 'POST',
			'dataType' : "json",
			'data': {'transaction_fee' :transaction_fee},
			'success' : function(data){
				// The call to API could take very long. We need to rely on API callback to know the status
				//show on
				if(data['message']){
					$('.payment-submitted-msg').html(data['message']);
				}
				if(data['success']){
                    <?php if($this->utils->isEnabledFeature('trigger_deposit_list_send_message')): ?>

                    <?php endif?>
					$('#search-form').trigger('submit');
				}
			}
		});

		$('.response-sec').hide();
		$('.withdraw_method').hide();
		$('.payment-submitted-msg').show();

		return false;
	}

	function batchProcessOrderId(processType){
		if ($('.chk-order-id').length) {
			var confirmTypeMessage = "<?= lang('conf.batch.process.withdraw') ?>";
			var emptySelectionMessage = "<?= lang('select.withdraw.process') ?>";
			var modalTitle = "<?= lang('lang.batch.process.summary') ?>";
			var maximum_deposit_request = "<?= lang('lang.maximum.deposit.request') ?>";

			if (processType == "NEXT") {
				confirmTypeMessage = "<?= lang('conf.batch.process.withdraw') ?>";
				emptySelectionMessage = "<?= lang('select.withdraw.process') ?>";
				modalTitle = "<?= lang('lang.batch.process.summary') ?>";
			}
			else if(processType == "APPROVE") {
				confirmTypeMessage = "<?= lang('conf.batch.approve.withdraw') ?>";
				emptySelectionMessage = "<?= lang('select.withdraw.approve') ?>";
				modalTitle = "<?= lang('lang.batch.approve.summary') ?>";
			}
			else if(processType == "DECLINE") {
				confirmTypeMessage = "<?= lang('conf.batch.decline.withdraw') ?>";
				emptySelectionMessage = "<?= lang('select.withdraw.decline') ?>";
				modalTitle = "<?= lang('lang.batch.decline.summary') ?>";
			}

			if (!$('.chk-order-id:checked').length) {
				alert(emptySelectionMessage);
				return false;
			}

			totalTransation = $('.chk-order-id:checked').length;
			totalCompleteTrans = 0;

			if(totalTransation > 10){
				alert(maximum_deposit_request);
				return false;
			}

			// Process deposit transaction
			if(!confirm(confirmTypeMessage)){
				return false;
			}

			$('.chk-order-id:checked').each(function(i, obj) {
				var order_id = $(this).val();
				var player_id = $(this).data('player_id');
				var dwstatus = $(this).data('dwstatus');
				var withdrawcode = $(this).data('withdrawcode');
				$('.batch-process-title').text(modalTitle);
				$('#batchProcessModal').modal('show');

				setTimeout(
					function () {
						if (processType == "NEXT") {
							processBatchWithdraw(order_id, withdrawcode, player_id, dwstatus);
						} else if (processType == "APPROVE") {
							approveBatchWithdraw(order_id, withdrawcode, player_id, dwstatus);
						} else if (processType == "DECLINE") {
							declinedBatchWithdraw(order_id, withdrawcode, player_id, dwstatus);
						} else {
							alert('Invalid type!');
						}
				}, 3000);
			});
		}
	}

	function approveBatchWithdraw(wallet_account_id, withdrawcode, player_id, dwstatus) {
		processBatchWithdraw(wallet_account_id, withdrawcode, player_id, dwstatus, 'approve');
	}

	function declinedBatchWithdraw(wallet_account_id, withdrawcode, player_id, dwstatus) {
		processBatchWithdraw(wallet_account_id, withdrawcode, player_id, dwstatus, 'decline');
	}

	function processBatchWithdraw(wallet_account_id, withdrawcode, player_id, dwstatus, destination_status='next') {
		var show_remarks_to_player = false;
		var next_status;

		if(wallet_account_id==''){
			alert('<?php echo lang("Lost withdrawl id, please refresh the page"); ?>');
			return;
		}

		unlockedTransaction(wallet_account_id);

		$.ajax({
			'url' : base_url +'payment_management/reviewWithdrawalRequest/'+wallet_account_id+'/'+player_id,
			'dataType' : "json"
		},'json').done(function( data, textStatus, jqXHR ) {
			if(data.transactionDetails[0] == null) {
				appendToBatchProcessSummary('Failed', withdrawcode, 'Transation Id not found.');
				return false;
			}

			if(destination_status == 'decline') {
				return sendBatchDeclineAjaxRequest(wallet_account_id, show_remarks_to_player, dwstatus, withdrawcode);
			}
			else if(destination_status == 'approve') {
				return sendBatchApproveAjaxRequest(wallet_account_id, show_remarks_to_player, dwstatus, withdrawcode);
			}
			else {
				return sendBatchProcessAjaxRequest(wallet_account_id, show_remarks_to_player, dwstatus, withdrawcode, player_id, data['withdrawSetting']);
			}
		}).fail(function(){
			appendToBatchProcessSummary('Failed', withdrawcode, "<?php echo lang("Withdrawal Failed");?>");
			return false;
		});
	}

	function sendBatchDeclineAjaxRequest(wallet_account_id, show_remarks_to_player, dwStatus, withdrawcode) {
		var notesType = 103;
		var declined_category_id = '';
		$.ajax({
			'url' : base_url +'payment_management/respondToWithdrawalDeclined/'+wallet_account_id+'/'+show_remarks_to_player+'/null/'+dwStatus,
			'type' : 'GET',
			'data' : {'notesType':notesType,'declined_category_id':declined_category_id},
			'success' : function(data){
				utils.safelog(data);

				if(data && data['success']){
					appendToBatchProcessSummary('Success', withdrawcode, "<?php echo lang("Declined Withdrawal Successful");?>");
					return true;
				}else{
					appendToBatchProcessSummary('Failed', withdrawcode, "<?php echo lang("Declined Withdrawal Failed");?>");
					return false;
				}
			}
		},'json').fail(function(){
			appendToBatchProcessSummary('Failed', withdrawcode, "<?php echo lang("Declined Withdrawal Failed");?>");
			return false;
		});
	}

	function sendBatchApproveAjaxRequest(wallet_account_id, show_remarks_to_player, dwStatus, withdrawcode) {
		var transaction_fee = 0;

		$.ajax({
			'url' : base_url +'payment_management/setWithdrawToPaid/' + wallet_account_id + '/-1/null/' + true,
			'type' : 'POST',
			'dataType' : "json",
			'data': {'transaction_fee' :transaction_fee},
			'success' : function(data){
				// The call to API could take very long. We need to rely on API callback to know the status
				//show on
				if(data && data['success']){
					appendToBatchProcessSummary('Success', withdrawcode, "<?php echo lang("Approved Withdrawal Successful");?>");
					return true;
				}else{
					appendToBatchProcessSummary('Failed', withdrawcode, "<?php echo lang("Approved Withdrawal Failed");?>");
					return false;
				}
			}
		}).fail(function(){
			appendToBatchProcessSummary('Failed', withdrawcode, "<?php echo lang("Approved Withdrawal Failed");?>");
			return false;
		});
	}

	function sendBatchProcessAjaxRequest(wallet_account_id, show_remarks_to_player, dwStatus, withdrawcode, player_id, withdraw_setting) {
		var statusIndex;
		if((dwStatus == 'request') || (dwStatus == 'pending_review')) {
			statusIndex = -1;
		} else if(dwStatus.substring(0,2) == 'CS') {
			statusIndex = parseInt(status.substring(2,3));
		}
		nextEnabledCustomStageIndex = findNextEnabledCustomStageIndex(withdraw_setting, statusIndex);
		if(nextEnabledCustomStageIndex>=0) {
			nextEnabledCustomStage = withdraw_setting[nextEnabledCustomStageIndex];
			next_status = 'CS' + nextEnabledCustomStageIndex;
		} else if(withdraw_setting.payProc.enabled) {
			next_status = 'payProc';
		} // else will leave the next status as default (paid)

		$.ajax({
			'url' : base_url +'payment_management/respondToWithdrawalRequest/'+wallet_account_id+'/'+player_id+'/'+show_remarks_to_player+'/'+next_status+'/'+true,
			'type' : 'GET',
			'success' : function(data){
				utils.safelog(data);

				if(data == 'success') {
					appendToBatchProcessSummary('Success', withdrawcode, "<?php echo lang("Withdrawal Successful");?>");
					return true;
				} else {
					appendToBatchProcessSummary('Failed', withdrawcode, "<?php echo lang("Withdrawal Failed");?>");
					return false;
				}
			}
		},'json').fail(function(){
			appendToBatchProcessSummary('Failed', withdrawcode, "<?php echo lang("Withdrawal Failed");?>");
			return false;
		});
	}

	function appendToBatchProcessSummary(status, id, remarks) {
		$('#batchProcessTable').append('<tr><td>'+status+'</td><td>'+id+'</td><td>'+remarks+'</td></tr>');

		if (status == 'Failed') {
			fail_trans++;
		} else {
			success_trans++;
		}

		totalCompleteTrans++;

		if (totalCompleteTrans == totalTransation) {
			completeProcess();
		}
	}

	function completeProcess() {
		$( ".progress-bar" ).removeClass('active');
		$( ".progress-bar" ).addClass('progress-bar-warning');
		$(".progressbar-text").text("<?= lang('Done!') ?>");
	}

	function getWithdrawalDeclined(requestId, playerId, modalFlag){
	    var detailsModal = 'declinedDetailsModal';
        //GET WITHDRAW CONDITON DATA
        WITHDRAWAL_CONDITION.initDatatable(detailsModal);
        WITHDRAWAL_CONDITION.refresh(playerId);

        currentWCPlayerId = playerId;
        /*Refreshes Withdrawal Condition*/
        $("#"+detailsModal+' .refresh-withdrawal-condition').click(function(){
            WITHDRAWAL_CONDITION.refresh(currentWCPlayerId);
            return false;
        });

		lockWithdrawal(requestId, modalFlag, function(){

		$('.transactionStatusMsg').html('');

		html  = '';
		html += '<p>';
		html += 'Loading Data...';
		html += '</p>';
		$('#playerDeclinedDetails').html(html);
		resetForm();
	   	$.ajax({
			'url' : base_url +'payment_management/reviewWithdrawalDeclined/'+requestId,
			'type' : 'GET',
			'dataType' : "json",
			'success' : function(data){
						   html  = '';
						   $('#playerDeclinedDetails').html(html);

						   // Modal title and label
						   $('#declinedDetailsModal .modal-title').text(
						   		data[0].dwStatus == 'declined' ? '<?=lang("pay.declwithdetl")?>' : '<?=lang("pay.paidwithdetl")?>'
						   	);
						   $('#declinedDetailsModal .panel-title').text(
						   		data[0].dwStatus == 'declined' ? '<?=lang("pay.declwithinfo")?>' : '<?=lang("pay.paidwithinfo")?>'
						   	);
						   $("label[for='withdrawalMethodDeclinedBy']").toggle(data[0].dwStatus == 'declined');
						   $("label[for='withdrawalMethodApprovedBy']").toggle(data[0].dwStatus != 'declined');
						   $("label[for='withdrawalMethodDateDeclined']").toggle(data[0].dwStatus == 'declined');
						   $("label[for='withdrawalMethodDateApproved']").toggle(data[0].dwStatus != 'declined');

						   //personal info
						   $('.playerId').val(data[0].playerId);
						   $('.userName').val(data[0].playerName);
						   $('.playerName').val(data[0].firstName+' '+data[0].lastName);
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

						   //deposit details
						   var currentLang = "<?= $this->language_function->getCurrentLanguage(); ?>";

							if (data[0].vipLevel.toLowerCase().indexOf("_json:") >= 0){
				                var langLvlConvert = jQuery.parseJSON(data[0].vipLevel.substring(6));
				                var lang_lvl_name = langLvlConvert[currentLang];
				            } else {
				                var lang_lvl_name = data[0].vipLevel;
				            }
							if (data[0].groupName.toLowerCase().indexOf("_json:") >= 0){
				                var langGroupConvert = jQuery.parseJSON(data[0].groupName.substring(6));
				                var lang_group_name = langGroupConvert[currentLang];
				            } else {
				            	var lang_group_name =data[0].groupName;
				            }

						   $('.dateDeposited').val(data[0].dwDateTime);
						   $('.playerLevel').val(lang_group_name+' '+lang_lvl_name);
						   $('.depositMethod').val(data[0].paymentMethodName);
						   $('.withdrawalAmount').val(data[0].amount);
                           $('.withdrawalCode').val(data[0].transactionCode);
						   $('.currentBalCurrency').val(data[0].currentBalCurrency);
						   $('.withdrawalMethodDeclinedBy').val(data[0].processedByAdmin);
						   $('.withdrawalMethodDateDeclined').val(data[0].processDatetime);

						   	if(data[0]['walletAccountInternalNotes'].length > 0){
								$('.withdraw-internal-notes').val(data[0]['walletAccountInternalNotes'].trim());
							}

							if(data[0]['walletAccountExternalNotes'].length > 0){
						        $('.withdraw-external-notes').val(data[0]['walletAccountExternalNotes'].trim());
							}

						   //promo details
						   $('#depositMethodApprovedBy').val(data[0].processedByAdmin);
						   $('#depositMethodDateApproved').val(data[0].processDatetime);

						   $('.currency').val(data[0].currentBalCurrency);

						   //bonus details
						   if(data[0]['playerPromoActive']){
							   $('.promoName').val(data[0]['playerPromoActive'][0].promoName);
							   $('#requestPlayerPromoBonusAmount').val(data[0]['playerPromoActive'][0].bonusAmount);
							   $('.playerDepositPromoId').val(data[0]['playerPromoActive'][0].playerDepositPromoId);

							   var promoStatus = '';
							   if(data[0]['playerPromoActive'][0].promoStatus == 0){
									promoStatus = 'Active';
							   }else if(data[0]['playerPromoActive'][0].promoStatus == 1){
									promoStatus = 'Expired';
							   }else if(data[0]['playerPromoActive'][0].promoStatus == 2){
									promoStatus = 'Finished';
							   }
							   $('#requestPlayerPromoStatus').val(promoStatus);

							   var withdrawPromoStatus = '';
							   if(data[0]['playerPromoActive'][0].withdrawalStatus == 0){
									withdrawPromoStatus = 'Bet requirement didn\'t met yet';
							   }else if(data[0]['playerPromoActive'][0].withdrawalStatus == 1){
									withdrawPromoStatus = 'Bet requirement met yet already)';
							   }

							   $('#requestPlayerPromoWithdrawConditionStatus').val(withdrawPromoStatus);
						   }

						   $('.playerTotalBalanceAmount').val(data[0].playerTotalBalanceAmount+' '+data[0].promoCurrency);
						   $('.currentBalAmount').val(data[0].currentBalAmount);

							//show/hide bonus details
							if($('#declinedWithdrawalBonusAmount').val()  != ''){
							  $('#bonusInfoPanelDeclinedWithdrawal').show();
							}else{
							  $('#bonusInfoPanelDeclinedWithdrawal').hide();
							}

							//payment method details
							$('.bankName').val(data[0].bankName);
							$('.bankAccountName').val(data[0].bankAccountFullName);
							$('.bankAccountNumber').val(data[0].bankAccountNumber);
							$('.bankAccountBranch').val(data[0].detailsBranch);
							$('.bankAddress').val(data[0].detailsBankAddress);
							$('.bankPhone').val(data[0].detailsBankPhone);

							$('.mainWalletBalance').val(data[0].currentBalAmount);
							$('.subWalletBalance').val('<?=$this->utils->formatCurrency(0)?>');
							$.each(data[0]['subwalletBalanceAmount'], function(index,subwallet) {
								$('.subWalletBalance' + subwallet.typeId).val(subwallet.totalBalanceAmount);
							});
							$('.totalBalance').val(data[0]['totalBalance']);
							$('.playerBonusInfoPanel').hide();
							$('#transaction_fee').val(data[0].transaction_fee).attr('readonly','readonly');
						}
	   	},'json');

		}); //lockWithdrawal

		return false;
	}

	function lockWithdrawal(requestId, modalFlag, callbackable) {
		$.ajax(
			base_url +'payment_management/userLockWithdrawal/'+requestId,
			{
				cache: false,
				method: 'POST',
				dataType: 'json',
				error: function(){
					alert("<?=lang('Lock failed')?>");
				},
				success: function(data){
					if(data){
						if(data['lock_result']) {
							showModal(modalFlag);
							callbackable();
						} else {
							lockedModal(data.message);
						}
					}else{
						lockedModal("<?=lang('Lock failed')?>");
					}
				}
			}
		);
	}

	function lockedModal(message) {
		$('#lockedModal').modal('show');
		$('#locked-message').html(message);
	}

	function showModal(modalFlag) {
		if(modalFlag == 1) {
			$('#approvedDetailsModal').modal('show');
		} else if( modalFlag == 2) {
			$('#declinedDetailsModal').modal('show');
		} else if ( modalFlag == 3) {
			$('#requestDetailsModal').modal('show');
		}
	}


    function addDuplicateAccountListDetails(playerId){
        $('.duplicateTable').DataTable({
            lengthMenu: JSON.parse('<?=json_encode($this->utils->getConfig('default_datatable_lengthMenu'))?>'),
            searching: false,
            autoWidth: false,
            dom:"<'panel-body'<'pull-right'f><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            stateSave: false,
            destroy:true,
            buttons: [
            {
                extend: 'colvis',
                postfixButtons: [ 'colvisRestore' ],
                className: '<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>'
            }
            ],
            columnDefs: [
            { sortable: false, targets: [0] },
            ],
            pageLength: <?=$this->utils->getDefaultItemsPerPage()?>,
            order: [[1, 'desc']],
            ajax: function (data, callback, settings) {
                $.post(base_url + "api/duplicate_account_info_by_playerid/" + playerId, data, function(data) {
                    callback(data);
                },'json');
            }
        });
    }

	function refreshPlayerBalance(playerId) {
		$.post(base_url +'player_management/refreshPlayerBalance/'+playerId, function(data){
		},"json");
	}


	function getWithdrawalRequest(requestId, playerId, modalFlag) {
        var detailsModal = 'approvedDetailsModal';
        var REQUEST_MODAL = '3';

        if(modalFlag == REQUEST_MODAL){
            detailsModal = 'requestDetailsModal';
        }

		//GET WITHDRAW CONDITON DATA
        WITHDRAWAL_CONDITION.initDatatable(detailsModal);
        WITHDRAWAL_CONDITION.refresh(playerId);

        currentWCPlayerId = playerId;
        /*Refreshes Withdrawal Condition*/
        $("#"+detailsModal+' .refresh-withdrawal-condition').click(function(){
            WITHDRAWAL_CONDITION.refresh(currentWCPlayerId);
            return false;
        });

		refreshPlayerBalance(playerId);

		lockWithdrawal(requestId, modalFlag, function(){


		$('.transactionStatusMsg').html('');

		html  = '';
		html += '<p>';
		html += 'Loading Data...';
		html += '</p>';

        addDuplicateAccountListDetails(playerId);

       $('#playerRequestDetails').html(html);
	   resetForm();
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
				var currentLang = "<?= $this->language_function->getCurrentLanguage(); ?>";

				if (data['transactionDetails'][0].vipLevel.toLowerCase().indexOf("_json:") >= 0){
	                var langLvlConvert = jQuery.parseJSON(data['transactionDetails'][0].vipLevel.substring(6));
	                var lang_lvl_name = langLvlConvert[currentLang];
	            } else {
	                var lang_lvl_name = data['transactionDetails'][0].vipLevel;
	            }
				if (data['transactionDetails'][0].groupName.toLowerCase().indexOf("_json:") >= 0){
	                var langGroupConvert = jQuery.parseJSON(data['transactionDetails'][0].groupName.substring(6));
	                var lang_group_name = langGroupConvert[currentLang];
	            } else {
	            	var lang_group_name = data['transactionDetails'][0].groupName;
	            }
				$('.playerLevel').val(lang_group_name+' '+lang_lvl_name);
				$('.depositMethod').val(data['transactionDetails'][0].paymentMethodName);
				$('.withdrawalAmount').val(data['transactionDetails'][0].amount);
				$('.withdrawalCode').val(data['transactionDetails'][0].transactionCode);
				$('.currency').val(data['transactionDetails'][0].currentBalCurrency);

				$("#requestInternalRemarksTxt").val('');
				$("#requestExternalRemarksTxt").val('');
				$("#approveInternalRemarksTxt").val('');
				$("#approveExternalRemarksTxt").val('');

				//payment method details

				if( data['transactionDetails'][0].bankName == 'other' ){
					$('.playerBankDetailsId').val(data['transactionDetails'][0].playerBankDetailsId);
					$('.bankName').val(data['transactionDetails'][0].customBankName);
					$('.customBank').removeClass('hide');
					$('.activate-bank').attr('data-bankid', data['transactionDetails'][0].bankTypeId);
					$('.confirm-bank-approval').attr('data-bankid', data['transactionDetails'][0].bankTypeId);
				}else{
					$('.customBank').addClass('hide');
					$('.bankName').val(data['transactionDetails'][0].bankName);
				}


				$('.bankAccountName').val(data['transactionDetails'][0].bankAccountFullName);
				$('.bankAccountNumber').val(data['transactionDetails'][0].bankAccountNumber);
				$('.bankAccountBranch').val(data['transactionDetails'][0].branch);
				$('.mainWalletBalance').val(data['transactionDetails'][0].currentBalAmount);
				$('.bankPhone').val(data['transactionDetails'][0].bankPhone);
				$('.bankAddress').val(data['transactionDetails'][0].bankAddress);

				$('.dailyMaxWithdrawal').val(data['dailyMaxWithdrawal']);
				$('.totalWithdrawalToday').val(data['totalWithdrawalToday']);

				<?php if($this->utils->isEnabledFeature('hide_paid_button_when_condition_is_not_ready')): ?>
					if(data['hasUnfinishedWithdrawCondition']) {
						$('.withdraw-paid-btn').addClass('hide');
					}
				<?php endif;?>

				$('.subWalletBalance').val('<?=$this->utils->formatCurrency(0)?>');
				$.each(data['transactionDetails'][0]['subwalletBalanceAmount'], function(index,subwallet) {
					$('.subWalletBalance' + subwallet.typeId).val(subwallet.totalBalanceAmount);
				});

				$('.totalBalance').val(data['transactionDetails'][0]['totalBalance']);
				$('.withdrawalTransactionId').val(data['transactionDetails'][0]['transactionCode']);

				$('.playerBonusInfoPanel').hide();

				if(data['walletAccountInternalNotes'].length > 0){
					$('.withdraw-internal-notes').val(data['walletAccountInternalNotes'].trim());
				}

				if(data['walletAccountExternalNotes'].length > 0){
			        $('.withdraw-external-notes').val(data['walletAccountExternalNotes'].trim());
				}

				var locking_checking = "<?=$checking_withdrawal_locking?>";
				var currentUserId = "<?=$this->authentication->getUserId()?>";

				if(locking_checking == 1){
					if(data['transactionDetails'][0]['is_checking']){
						$('#checking_btn').hide();
						//check if user is the one who check
						if(data['transactionDetails'][0]['processedBy'] == currentUserId){
							$('.response-sec').show();
						} else {
							$('.response-sec').hide();
							message = "<?=lang('text.checking.message')?>:"+data['transactionDetails'][0]['processedByAdmin'];
							$('.transactionStatusMsg').html(message);
						}
				    } else {
						$('.response-sec').show();
				    }
			    } else {
					$('.response-sec').show();
			    }

				formatFormButton(data['transactionDetails'][0].dwStatus, data['withdrawSetting']);
				$('.response-sec').show();
				$('.payment-submitted-msg').hide();
			}

	   	},'json');

        <?php if($this->utils->isEnabledFeature('trigger_deposit_list_send_message')): ?>
            // getSelectMsgTpl();
        <?php endif;?>

		});

		return false;
	}

	function getWithdrawalApproved(walletAccountId, modalFlag, playerId) {
	    var detailsModal = 'approvedDetailsModal';
		//GET WITHDRAW CONDITON DATA
        WITHDRAWAL_CONDITION.initDatatable(detailsModal);
        WITHDRAWAL_CONDITION.refresh(playerId);

        currentWCPlayerId = playerId;
        /*Refreshes Withdrawal Condition*/
        $("#"+detailsModal+' .refresh-withdrawal-condition').click(function(){
            WITHDRAWAL_CONDITION.refresh(currentWCPlayerId);
            return false;
        });

		lockWithdrawal(walletAccountId, modalFlag, function(){
			$('.transactionStatusMsg').html('');

			html  = '';
			html += '<p>';
			html += 'Loading Data...';
			html += '</p>';

			resetForm();
			$.ajax({
				'url' : base_url +'payment_management/reviewWithdrawalApproved/'+walletAccountId,
				'type' : 'GET',
				'dataType' : "json",
				'success' : function(data){
					html  = '';

					//clear previous transaction history
					$('.transacHistoryDetail').remove();
					$('#playerApprovedDetailsRepondBtn').hide();
					$('#playerApprovedDetailsCheckPlayer').hide();

					$('#walletAccountIdForPaid').val(data[0].walletAccountId);

					if(data[0]['lock_manually_opt']=='1'){
						$('.locked_withdrawal').show();
					}

					//personal info
					$('.playerId').val(data[0].playerId);
					$('.userName').val(data[0].playerName);
					$('.playerName').val(data[0].firstName+' '+data[0].lastName);
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

					$('#depositMethodApprovedBy').val(data[0].processedByAdmin);
					$('#depositMethodDateApproved').val(data[0].processDatetime);

					$('.dateDeposited').val(data[0].dwDateTime);
					var currentLang = "<?= $this->language_function->getCurrentLanguage(); ?>";

					if (data[0].vipLevel.toLowerCase().indexOf("_json:") >= 0){
		                var langLvlConvert = jQuery.parseJSON(data[0].vipLevel.substring(6));
		                var lang_lvl_name = langLvlConvert[currentLang];
		            } else {
		                var lang_lvl_name = data[0].vipLevel;
		            }
					if (data[0].groupName.toLowerCase().indexOf("_json:") >= 0){
		                var langGroupConvert = jQuery.parseJSON(data[0].groupName.substring(6));
		                var lang_group_name = langGroupConvert[currentLang];
		            } else {
		            	var lang_group_name =data[0].groupName;
		            }
					$('.playerLevel').val(lang_group_name+' '+lang_lvl_name);
					$('.depositMethod').val(data[0].paymentMethodName);
					$('.withdrawalAmount').val(data[0].amount);
					$('.withdrawalCode').val(data[0].transactionCode);
					$('.currency').val(data[0].currentBalCurrency);

					//bonus details
					if(data[0]['playerPromoActive']){
					$('.promoName').val(data[0]['playerPromoActive'][0].promoName);
					$('#requestPlayerPromoBonusAmount').val(data[0]['playerPromoActive'][0].bonusAmount);
					$('.playerDepositPromoId').val(data[0]['playerPromoActive'][0].playerDepositPromoId);

					var promoStatus = '';
					if(data[0]['playerPromoActive'][0].promoStatus == 0){
						promoStatus = 'Active';
					}else if(data[0]['playerPromoActive'][0].promoStatus == 1){
						promoStatus = 'Expired';
					}else if(data[0]['playerPromoActive'][0].promoStatus == 2){
						promoStatus = 'Finished';
					}
					$('#requestPlayerPromoStatus').val(promoStatus);

					var withdrawPromoStatus = '';
					if(data[0]['playerPromoActive'][0].withdrawalStatus == 0){
						withdrawPromoStatus = 'Bet requirement didn\'t met yet';
					}else if(data[0]['playerPromoActive'][0].withdrawalStatus == 1){
						withdrawPromoStatus = 'Bet requirement met yet already)';
					}

					$('#requestPlayerPromoWithdrawConditionStatus').val(withdrawPromoStatus);
					}

					//show/hide bonus details
					if($('#requestPlayerPromoBonusAmount').val()  != ''){
						$('.bonusInfoPanel').show();
					}else{
						$('.bonusInfoPanel').hide();
					}

					//show/hide bonus details
					if($('#approvedWithdrawalBonusAmount').val()  != ''){
						$('#bonusInfoPanelApprovedWithdrawal').show();
					}else{
						$('#bonusInfoPanelApprovedWithdrawal').hide();
					}

					//payment method details
					$('.bankName').val(data[0].bankName);
					$('.bankAccountName').val(data[0].bankAccountFullName);
					$('.bankAccountNumber').val(data[0].bankAccountNumber);
					$('.bankAccountBranch').val(data[0].branch);
					$('.mainWalletBalance').val(data[0].currentBalAmount);
					$('.subWalletBalance').val('<?=$this->utils->formatCurrency(0)?>');
					$.each(data[0]['subwalletBalanceAmount'], function(index,subwallet) {
						$('.subWalletBalance' + subwallet.typeId).val(subwallet.totalBalanceAmount);
					});

					$('.totalBalance').val(data[0]['totalBalance']);

					$('#transaction_fee').val('').removeAttr('readonly');
					$('.response-sec').show();
					$('.payment-submitted-msg').hide();

					if(data['walletAccountInternalNotes'].length > 0){
						$('.withdraw-internal-notes').val(data['walletAccountInternalNotes'].trim());
					}

					if(data['walletAccountExternalNotes'].length > 0){
				        $('.withdraw-external-notes').val(data['walletAccountExternalNotes'].trim());
					}

					//clear reason
					$("#requestInternalRemarksTxt").val('');
					$("#requestExternalRemarksTxt").val('');
					$("#approveInternalRemarksTxt").val('');
					$("#approveExternalRemarksTxt").val('');

					// add withdraw method
					$('#withdraw_method_display').val(data['withdraw_method_display']);
					$('#withdraw_id_hidden').val(data['withdraw_id_hidden']);

					formatFormButton(data[0].dwStatus,  data['withdrawSetting']);

					var enabledCheckWithdrawalStatusIdList = <?= json_encode($this->config->item('enabledCheckWithdrawalStatusIdList')) ?>;

					// Only enabled API currently supports manual checking of status
					if(enabledCheckWithdrawalStatusIdList.includes(data['withdraw_id_hidden']) == false) {
						$('#btn_check_withdraw').hide();
					}
				}
			},'json');

		});//lockWithdrawal

		return false;
	}

	// Before each AJAX call, we need to disable critical form buttons to avoid clicking before AJAX finishes loading.
	// Buttons will be enabled in formatFormButton function
	// Form Buttons will also be disblaed if permission is not given
	var disableFormButton = function(enable) {
		var hasPermission = true;

		<?php if(!$this->permissions->checkPermissions(array('approve_decline_withdraw'))) : ?>
		hasPermission = false;
		<?php endif; ?>

		if(!enable && !hasPermission) {
			$('#paid_btn, #decline_btn, #btn_approve, #btn_decline').attr('disabled', 'disabled');
		} else {
			$('#paid_btn, #decline_btn, #btn_approve, #btn_decline').removeAttr('disabled');
		}
	}

	// Clear all display fields
	var resetForm = function() {
		$(".modal *:not(input[type='button'],.declined-category-id)").val('');
		$('.playerBonusInfoPanel').hide();
		disableFormButton(false);
	}

	// Based on the current withdraw status and custom stage setting, modify the buttons on requestDetailsModal and approveDetailsModal
	var formatFormButton = function(status, setting) {
		disableFormButton(true);

		var $button = $('#btn_approve');
		$button.text('<?=lang('lang.paid')?>');
		$button.data('next-status', 'paid'); // Default next status = paid

		var statusIndex;
		if((status == 'request') || (status == 'pending_review')) {
			statusIndex = -1;
		} else if(status.substring(0,2) == 'CS') {
			statusIndex = parseInt(status.substring(2,3));
		}
		nextEnabledCustomStageIndex = findNextEnabledCustomStageIndex(setting, statusIndex);
		if(nextEnabledCustomStageIndex>=0) {
			nextEnabledCustomStage = setting[nextEnabledCustomStageIndex];
			$button.text(nextEnabledCustomStage.name);
			$button.data('next-status', 'CS' + nextEnabledCustomStageIndex);
		} else if(setting.payProc.enabled) {
			nextEnabledCustomStage = setting.payProc;
			$button.text('<?=lang('Pay')?>');
			$button.data('next-status', 'payProc');
		} // else will leave the next status as default (paid)

		var $paidButton = $('#paid_btn').val('<?=lang('lang.paid')?>');
		if(status != 'payProc' && setting.payProc.enabled) { // i.e. status = custom_last
			$('#btn_check_withdraw').hide(); // hide the button to check withdraw status before payment is submitted to API
			// Only show withdraw_method buttons when user has permission to go to payProc status
			<?php if($this->permissions->checkPermissions('approve_withdrawal_to_payProc')) : ?>
			$('.withdraw_method').show();
			<?php endif; ?>
			$paidButton.hide();
		} else { // current status is payProc, we have already chosen withdraw method at the last step
			<?php if($canManagePaymentStatus) : ?>
			$('.withdraw_method').hide();
			$paidButton.show();
			$('#btn_check_withdraw').show(); // show the button to check withdraw status when status is payProc
			<?php else : ?>
			$('.response-sec').hide();
			<?php endif; ?>
		}

		// Based on next-status and permission, hide the respective buttons
		<?php
		for($i = 0; $i < CUSTOM_WITHDRAWAL_PROCESSING_STAGES; $i++) :
			if(!$this->permissions->checkPermissions('approve_withdrawal_to_CS'.$i)) :
		?>
		if($button.data('next-status') == 'CS<?=$i?>') {
			$button.hide();
		}
		<?php
			endif;
		endfor;
		?>
	}

	// based on the index given, find the next available custom stage
	var findNextEnabledCustomStageIndex = function(setting, currentIndex) {
		var nextIndex = currentIndex + 1;
		while(setting[nextIndex]) {
			if(setting[nextIndex].enabled) {
				return nextIndex;
			}
			nextIndex++;
		}

		return -1;
	}

	/**
	* Number.prototype.format(n, x)
	*
	* @param integer n: length of decimal
	* @param integer x: length of sections
	*/
	Number.prototype.format = function(n, x) {
		var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
		return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
	};

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

	function addNotes(noteBtn,noteTypes) {
		if(noteBtn == 'requestinternalnotebtn'){
			var remarkTxt = $('#requestInternalRemarksTxt').val();
		}else if(noteBtn == 'requestexternalnotebtn'){
			var remarkTxt = $('#requestExternalRemarksTxt').val();
		}else if(noteBtn == 'approveinternalnotebtn'){
			var remarkTxt = $('#approveInternalRemarksTxt').val();
		}else if(noteBtn == 'approveexternalnotebtn'){
			var remarkTxt = $('#approveExternalRemarksTxt').val();
		}else{
			return;
		}
		if(remarkTxt == ''){
			return;
		}
		addNotesText(remarkTxt,noteTypes);
	}

	function addNotesText(notes,noteTypes) {
		var walletAccountIdVal = $('.request_walletAccountIdVal').val();
		var walletAccountIdForPaid = $('#walletAccountIdForPaid').val();
		var status = "<?=$conditions['dwStatus']?>";
		// Fix the missing value
		if(walletAccountIdVal == ""){
			walletAccountIdVal = walletAccountIdForPaid;
		}

		//alert(walletAccountIdVal);return false;

		unlockedTransaction(walletAccountIdVal);

		$.ajax({
			'url' : base_url + 'payment_management/addWithdrawalNotes/withdrawal/' + walletAccountIdVal,
			'type' : 'POST',
			'data' : { 'notes' : notes, 'noteTypes' : noteTypes, 'status' : status},
			'dataType': 'json',
			'success' : function (data) {
				$("#requestInternalRemarksTxt").val('');
				$("#requestExternalRemarksTxt").val('');
				$("#approveInternalRemarksTxt").val('');
				$("#approveExternalRemarksTxt").val('');
				var notes = data.notes, notesStr ="", notesLength = notes.length, type = data.ntype ;
				// for(var i=0; i < notesLength ; i++){
				// 	notesStr += "["+notes[i].create_date+"] "+notes[i].admin_name+": "+notes[i].note+"\n";
				// }
				if(type == '2'){
					var withdrawNotes = $('.withdraw-internal-notes');
					withdrawNotes.val(notes);
				}else if(type == '3'){
					var withdrawNotes = $('.withdraw-external-notes');
					withdrawNotes.val(notes);
				}else{
					return;
				}

				if(notesLength > 1){
					withdrawNotes.scrollTop(withdrawNotes[0].scrollHeight - withdrawNotes.height());
				}
				alert("<?=lang('Notes has been added.')?>");
			},
		});
	}

	function showDetialNotes(walletAccountId, note_type) {
		$.ajax({
			'url' : base_url + 'payment_management/getWithdrawalDetialNotes/' + walletAccountId + '/' + note_type,
			'type' : 'POST',
			'dataType': 'json',
			'success' : function (data) {
				var allNotes = data.formatNotes, transactionCode = data.transactionCode, noteSubTitle = data.noteSubTitle;
				var subtitle = '<div>'+ noteSubTitle +'</div>' + '<br><textarea class="form-control" rows="15" readonly style="resize: none;"></textarea>';
				if(data.success) {
					BootstrapDialog.show({
						id: 'bootstrap_dialog_id',
						title: 'NO.' + transactionCode,
						message: $(subtitle).val(allNotes.trim()),
						buttons: [{
							label: 'Close',
			                action: function(dialogItself){
			                    dialogItself.close();
			                }
						}]
					});
				}else{
					alert('<?=lang("Something is wrong, show notes detail failed")?>');
				}
			},
		});
	}

	function unlockApiUnknown() {
		var walletAccountIdVal = $('.request_walletAccountIdVal').val();

		if(walletAccountIdVal == ''){
			alert('<?=lang("Clicking the [Unlock Now] button is too fast, please try again")?>');
			$('#search-form').trigger('submit');
		}else{
			BootstrapDialog.show({
				title: '<?=lang("pay.unlock_api_unknown_withdrawal_equest")?>',
				message: '<?=lang("Please confirm the real status with 3rd payment provider and then unlock.")?>',
				buttons: [{
					label: '<?=lang("pay.back_to_pending_status")?>',
					action: function(dialog) {
						BootstrapDialog.confirm('<?=lang("Ready to Unlock. Are you sure that you had confirmed the status with 3rd payment provider?")?>', function(result){
							if(result) {
								$.ajax({
									'url' : base_url +'payment_management/setWithdrawToRequest/' + walletAccountIdVal,
									'type' : 'POST',
									'dataType' : "json"
								}).done( function(data) {
									// dialog.close();
									$('#search-form').trigger('submit');
								});
							}
						});
					}
				}, {
					label: '<?=lang("pay.advance_to_paid_status")?>',
					action: function(dialog) {
						BootstrapDialog.confirm('<?=lang("Ready to Unlock. Are you sure that you had confirmed the status with 3rd payment provider?")?>', function(result){
							if(result) {
								setWithdrawToPaid(this, true);
							}
						});
					}
				}]
			});
		}
	}

	function checkWithdrawStatus(btn) {
		var walletAccountIdVal = $('.request_walletAccountIdVal').val();
		var walletAccountIdForPaid = $('#walletAccountIdForPaid').val();
		// Fix the missing value
		if(walletAccountIdVal == ""){
			walletAccountIdVal = walletAccountIdForPaid;
		}

		var html = "<p><?=lang('Checking withdraw status')?>...</p>";
		$('.transactionStatusMsg').html(html);

		var withdrawalApiId = $('#withdraw_id_hidden').val();

		unlockedTransaction(walletAccountIdVal);
		var noteTypes = '1';//action log notes
		$.ajax({
			'url' : base_url + 'payment_management/checkWithdrawStatus/' + walletAccountIdVal + '/' + withdrawalApiId,
			'type' : 'POST',
			'dataType': 'json',
			'success' : function (data) {
				if(data.success) {
					addNotesText('<?=lang("Withdraw successful")?>: ' + data.message,noteTypes);
					setWithdrawToPaid(this, true);
				}
				else if(data.payment_fail) {
					addNotesText('<?=lang("Withdraw failed")?>: ' + data.message,noteTypes);
					respondToWithdrawalDeclinedForPaid(true);
				}
				else {
					addNotesText('<?=lang("Withdraw status")?>: ' + data.message,noteTypes);
				}
			},
		});
	}

	$('.closeApproved').on('click', function(){
		var walletAccountIdVal = $('.request_walletAccountIdVal').val();
		//unlockedTransaction(walletAccountIdVal);
	});

	$('.closeRequest').on('click', function(){
		var walletAccountIdVal = $('.request_walletAccountIdVal').val();
	});


	function unlockedTransaction(walletAccountId) {
		$.post(base_url + 'payment_management/unlockWithdrawTransaction', {walletAccountId : walletAccountId }, function(){});
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
	   var playerId = $('.playerId').val();
	   var showRemarksToPlayer = null;
	   // var walletAccountIdVal = $('.request_walletAccountIdVal').val();
	   var nextStatus = $('#btn_approve').data('next-status');
	   var withdrawApi = 0;

	   if(walletAccountIdVal==''){
			alert('<?php echo lang("Lost withdrawl id, please refresh the page"); ?>');
			return;
	   }

		unlockedTransaction(walletAccountIdVal);

		$.ajax({
			'url' : base_url +'payment_management/respondToWithdrawalRequest/'+walletAccountIdVal+'/'+playerId+'/'+showRemarksToPlayer+'/'+nextStatus + (withdrawApi ? '/' + withdrawApi : ''),
			'type' : 'GET',
			'success' : function(data){
				utils.safelog(data);
				utils.safelog(data== 'success');
				if(data == 'success') {
					html  = '';
					html += '<p>';
					html += "<?=lang('Withdrawal status has been updated')?>";
					html += '</p>';
				} else if(data==''){
					html  = '';
					html += '<p>';
					html += "<?=lang('Internal Error')?>";
					html += '</p>';
				} else {
					html  = '';
					html += '<p>';
					html += data;
					html += '</p>';
				}
				$('.transactionStatusMsg').html(html);
				// $('#repondBtn').hide();
				// $('#remarks-sec').hide();
				$('.response-sec').hide();
			}
	   },'json');

		return false;
	}

    <?php if($this->utils->isEnabledFeature('trigger_deposit_list_send_message')): ?>
    // 之後會開單 移除 或修改此功能
    //
    //
	// function getSelectMsgTpl(){
	//     $.ajax({
	//         'url' : '/api/getAllMsgTpl',
	//         'type' : 'get',
	//         'cache' : false,
	//         'dataType' : "json"
	//     }).done(
	//         function(data){
	//             var flg;
	//             var optVal;
	//             $.each(data, function(k,v) {
	//                 flg =v.note;
	//                 if(flg==="Withdrawals"){
	//                     optVal = v.value;
	//                     $("#sectpl").append('<option value="'+optVal+'">'+v.name+'</option>');
	//                 }

	//             });

	//         }
	//     );
	// }

	// function fillcontent(v){
	//     v = v.replace('[withdrawals_time]', $('.dateDeposited').val());
	//     v = v.replace('[withdrawals_amount]', $('.withdrawalAmount').val());

	//     $('#reasonForPaid').val("");
	//     $('#reasonForPaid').val(v);
	// }

	// function sendMsgToPlayer(remarks){
	//     var vsubject = "<?=lang('trigger_withdrawal_list_send_message.subject')?>";
	//     $.ajax({
	//         'url' : '/api/sendMessageAdmin',
	//         'type' : 'post',
	//         'data': {playerId: $('.playerId').val(), subject: vsubject,msg:remarks},
	//         'cache' : false,
	//         'dataType' : "json"
	//     }).done(
	//         function(data){
	//             if(data['flg']) {
	//                 console.log('yes');
	//                 return true;
	//             }else{
	//                 console.log('false');
	//                 return false;
	//             }
	//         }
	//     );
	// }


	<?php endif?>

	function respondToWithdrawalDeclinedForPaid(auto = false) {
		if(!auto){
			if(!confirm('<?=lang('confirm.decline.request')?>')) {
				return;
			}
		}

		var notesType = 102;
		var walletAccountIdVal = $('#walletAccountIdForPaid').val();
		var declined_category_id = $('#declined_category_id_for_paid').val();
        var status = "<?=$conditions['dwStatus']?>";

        <?php if ($this->utils->isEnabledFeature('enable_withdrawal_declined_category') && !empty($withdrawalDeclinedCategory)) : ?>
        if (!declined_category_id) {
			html = '<p style="color: red;"><?=lang('err_select_decline_category')?></p>';
			$('.transactionStatusMsg').html(html);
			return false;
		}
        <?php endif; ?>

		set_withdrawal_declined(walletAccountIdVal,null,notesType,declined_category_id, status);

		return false;
	}

	function respondToWithdrawalDeclined() {

		// utils.safelog('respondToWithdrawalDeclined');

		var notesType = 101;
		var walletAccountIdVal = $('.request_walletAccountIdVal').val();
		var declined_category_id = $('#declined_category_id').val();
        var status = "<?=$conditions['dwStatus']?>";

        <?php if ($this->utils->isEnabledFeature('enable_withdrawal_declined_category') && !empty($withdrawalDeclinedCategory)) : ?>
        if (!declined_category_id) {
			html = '<p style="color: red;"><?=lang('err_select_decline_category')?></p>';
			$('.transactionStatusMsg').html(html);
			return false;
		}
        <?php endif; ?>

		unlockedTransaction(walletAccountIdVal);

		set_withdrawal_declined(walletAccountIdVal,null,notesType,declined_category_id,status);

		return false;
	}

	function set_withdrawal_declined(walletAccountIdVal,showRemarksToPlayer,notesType,declined_category_id,status) {
		html  = '';
		html += '<p>';
		html += "<?php echo lang('Loading Data'); ?>";
		html += '...</p>';

		$('.transactionStatusMsg').html(html);

		unlockedTransaction(walletAccountIdVal);

		$.ajax({
			'url' : base_url +'payment_management/respondToWithdrawalDeclined/'+walletAccountIdVal+'/'+showRemarksToPlayer+'/null/'+status,
			'type' : 'GET',
			'data' : {
				'notesType': notesType,
				'declined_category_id': declined_category_id
			},
			'success' : function(data){
				utils.safelog(data);

				if(data && data['success']){
                    <?php if($this->utils->isEnabledFeature('trigger_deposit_list_send_message')): ?>

                    <?php endif?>

					html  = '';
					html += '<p>';
					html += "<?php echo lang('Withdrawal has been Declined'); ?>";
					html += '!</p>';

					$('.transactionStatusMsg').html(html);
					$('.response-sec').hide();
					$('.withdraw_method').hide();
					$('#search-form').trigger('submit');
				}else{
					var msg="<?php echo lang('Decline failed'); ?>";
					if(data['message']!='' && data['message']!=null){
						msg=data['message'];
					}
					alert(msg);
					$('.transactionStatusMsg').html(msg);
				}
			}
		},'json');
	}

    var WITHDRAWAL_CONDITION = (function() {

        var withdrawalCondTable,
            depositCondTable,
            gameSystemMap = <?php echo $this->utils->encodeJson( $this->utils->getGameSystemMap() );?>,
            GET_WITHDRAWAL_CONDITION_URL =  '<?php echo site_url('player_management/getWithdrawalCondition') ?>',
            repeatedLoad =false,
            withdrawCondLoader = null,
            summaryWithCondCont = null,
            refreshWithConBtn = null,
            hasRows=false,
            modalName = '';

        function initDatatable(_modalName){
            modalName = _modalName;

            /* Initiate Withdrawal Condition Table */
            withdrawalCondTable = $('#' + modalName + ' .withdrawal-condition-table').DataTable({
                searching: false,
                autoWidth: false,
                dom:"<'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
                stateSave: false,
                destroy:true,
                buttons: [
                    {
                        extend: 'colvis',
                        postfixButtons: [ 'colvisRestore' ],
                        className: '<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>'
                    }
                ],
                columnDefs: [
                    {
                        sortable: false,
                        targets: [0]
                    },
                ],
                order: [
                    [6, 'desc']
                ],
            }).draw(false),

            /* Initiate Deposit Condition Table */
            depositCondTable = $('#' + modalName + ' .deposit-condition-table').DataTable({
                searching: false,
                autoWidth: false,
                dom:"<'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
                stateSave: false,
                destroy:true,
                buttons: [
                    {
                        extend: 'colvis',
                        postfixButtons: [ 'colvisRestore' ],
                        className: '<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>'
                    }
                ],
                columnDefs: [
                    {
                        sortable: false,
                        targets: [0]
                    },
                ],
                order: [[6, 'desc']]
            }).draw(false),

            GET_WITHDRAWAL_CONDITION_URL =  '<?php echo site_url('player_management/getWithdrawalCondition') ?>',
            GET_SUMMARIZE_WITHDRAWAL_CONDITION_URL =  '<?php echo site_url('player_management/computeWithdrawalCondition') ?>',
            repeatedLoad =false,
            withdrawCondLoader = $('#' + modalName + ' .withdawal-condition-loader'),
            summaryWithCondCont = $('#' + modalName + ' .summary-condition-container'),
            refreshWithConBtn = $('#' + modalName + ' .refresh-withdrawal-condition'),
            hasRows=false;


        }

        function getWithdrawalCondition(playerId){
            var totalRequiredBet=0, totalPlayerBet=0, un_finished=0;
            withdrawCondLoader.show();

            $.ajax({
              url : GET_WITHDRAWAL_CONDITION_URL+'/'+playerId,
              type : 'GET',
              dataType : "json",
              cache:false,
            }).done(function (obj) {

            var arr = obj.withdrawalCondition;
            <?php if($this->utils->isEnabledFeature('disabled_withdraw_condition_share_betting_amount')):?>
                var totalPlayerBet = obj.totalPlayerBet;
                var totalRequiredBet = obj.totalRequiredBet;
                var un_finished = 0;
            <?php endif;?>

            if(arr){
                hasRows = true;
                refreshWithConBtn.show();

                /*Clear the table rows first to prevent appending rows when refresh*/
                withdrawalCondTable.clear().draw();
                depositCondTable.clear().draw();

                for (var i = 0; i < arr.length; i++) {

                    var transactions,
                        promoCode,
                        depositCondition,
                        nonfixedDepositAmtCondition,
                        bonusReleaseRule,
                        currentBet,
                        withdrawRequirement,
                        unfinished_status,
                        obj = arr[i];

                    currentBet = (obj.currentBet != null && Number(obj.currentBet)) ? Number(obj.currentBet) : 0;

                    transactions = "<?=lang('lang.norecyet')?>";
                    var promoName = obj.promoName || obj.promoTypeName;

                    if(obj.source_type == '<?=Withdraw_condition::SOURCE_DEPOSIT?>'){
                        transactions = "<?=lang('withdraw_conditions.source_type.' . Withdraw_condition::SOURCE_DEPOSIT)?>";
                    }else if(obj.source_type == '<?=Withdraw_condition::SOURCE_BONUS?>'){
                        transactions = "<?=lang('withdraw_conditions.source_type.' . Withdraw_condition::SOURCE_BONUS)?>";
                    }else if(obj.source_type == '<?=Withdraw_condition::SOURCE_CASHBACK?>'){
                        transactions = "<?=lang('withdraw_conditions.source_type.' . Withdraw_condition::SOURCE_CASHBACK)?>";
                    }else if(obj.source_type == '<?=Withdraw_condition::SOURCE_NON_DEPOSIT?>'){
                        transactions = "<?=lang('Non-deposit')?>";
                    }

                    promoName = promoName || "<?=lang('pay.noPromo')?>";
                    promoCode = (obj.promoCode) ? obj.promoCode : "<i><?=lang('pay.noPromo')?></i>";

                    if(!obj.withdrawRequirementRule){
                        if (obj.withdrawRequirementConditionType == '<?Withdraw_condition::WITHDRAW_REQUIREMENT_RULE_BYBETTING?>') {
                            withdrawRequirement = "<?=lang('cms.withBetAmtCond')?> >= " + obj.withdrawRequirementBetAmount;
                        }else{
                            if(obj.promoType == 1){
                                withdrawRequirement = "<?=lang('cms.betAmountCondition2')?> >= " + obj.withdrawRequirementBetCntCondition;
                            }else{
                                withdrawRequirement = "<?=lang('cms.betAmountCondition1')?> >= " + obj.withdrawRequirementBetCntCondition;
                            }
                        }
                    }else{
                        withdrawRequirement = "<?=lang('cms.noBetRequirement')?>";
                    }

                    var wallet_name = gameSystemMap[obj.wallet_type];
                    if (!wallet_name) {
                        wallet_name = '';
                    }

                    var bonusAmount = 0;
                    var conditionAmount = 0;
                    var deposit_min_limit = 0;

                    if(obj.withdraw_condition_type == '<?=Withdraw_condition::WITHDRAW_CONDITION_TYPE_BETTING?>'){
                        unfinished_status = (parseFloat(obj.is_finished).toFixed(2) < 1) ? "<?=lang('player.ub13')?>" : "<?=lang('player.ub14')?>";
                        conditionAmount = (obj.conditionAmount) ? obj.conditionAmount : 0;
                        bonusAmount = (parseFloat(obj.trigger_amount).toFixed(2) == 0.0 ? parseFloat(obj.bonusAmount).toFixed(2) : parseFloat(obj.trigger_amount).toFixed(2));
                        <?php if ($enabled_show_withdraw_condition_detail_betting) {?>
                            var row = [
                                transactions,
                                wallet_name,
                                obj['promoBtn'],
                                promoCode,
                                parseFloat(obj.walletDepositAmount).toFixed(2),
                                bonusAmount,
                                obj.started_at,
                                parseFloat(obj.conditionAmount).toFixed(2),
                                (!obj.note) ? obj.pp_note : obj.note,
                                parseFloat(currentBet).toFixed(2),
                                unfinished_status
                            ];
                        <?php } else {?>
                            var row = [
                                transactions,
                                wallet_name,
                                obj['promoBtn'],
                                promoCode,
                                parseFloat(obj.walletDepositAmount).toFixed(2),
                                bonusAmount,
                                obj.started_at,
                                parseFloat(obj.conditionAmount).toFixed(2),
                                (!obj.note) ? obj.pp_note : obj.note,
                            ];
                        <?php }?>
                        withdrawalCondTable.row.add(row).draw();
                    }

                    if(obj.withdraw_condition_type == '<?=Withdraw_condition::WITHDRAW_CONDITION_TYPE_DEPOSIT?>'){
                        unfinished_status =( parseFloat(obj.is_finished).toFixed(2) > 0 &&  (parseFloat(obj.currentDeposit).toFixed(2) >= parseFloat(obj.conditionDepositAmount).toFixed(2)) ) ? "<?=lang('player.ub14')?>" : "<?=lang('player.ub13')?>";
                        deposit_min_limit = (obj.conditionDepositAmount) ? obj.conditionDepositAmount : 0;
                        bonusAmount = (parseFloat(obj.trigger_amount).toFixed(2) == 0.0 ? parseFloat(obj.bonusAmount).toFixed(2) : parseFloat(obj.trigger_amount).toFixed(2));
                        <?php if ($enabled_show_withdraw_condition_detail_betting) {?>

                            var deposit_condition_row=[
                                transactions,
                                wallet_name,
                                obj['promoBtn'],
                                promoCode,
                                parseFloat(obj.walletDepositAmount).toFixed(2),
                                bonusAmount,
                                obj.started_at,
                                parseFloat(deposit_min_limit).toFixed(2),
                                (!obj.note) ? obj.pp_note : obj.note,
                                parseFloat(currentBet).toFixed(2),
                                unfinished_status
                            ];
                        <?php } else {?>
                            var deposit_condition_row=[
                                transactions,
                                wallet_name,
                                obj['promoBtn'],
                                promoCode,
                                parseFloat(obj.walletDepositAmount).toFixed(2),
                                bonusAmount,
                                obj.started_at,
                                parseFloat(deposit_min_limit).toFixed(2),
                                (!obj.note) ? obj.pp_note : obj.note
                            ];
                        <?php }?>
                        depositCondTable.row.add(deposit_condition_row).draw();
                    }

                }//loop end

                <?php if($this->utils->isEnabledFeature('disabled_withdraw_condition_share_betting_amount')):?>
                    var un_finished =  parseFloat(totalRequiredBet).toFixed(2) - parseFloat(totalPlayerBet).toFixed(2),
                        summary     =  "<table class='table table-hover table-bordered'>";

                    if(un_finished < 0) un_finished = 0;

                    summary += "<tr><th class='active col-md-8'><b><?=lang('pay.totalRequiredBet')?>:</b></th><td align='right'>"+parseFloat(totalRequiredBet).toFixed(2)+"</td></tr>";
                    summary += "<tr><th class='active col-md-8'><b><?=lang('pay.currTotalBet')?>:</b></th><td align='right'> "+parseFloat(totalPlayerBet).toFixed(2)+"</td></tr>";
                    summary += "<tr><th class='active col-md-8'><b><?=lang('mark.unfinished')?>:</b></th><td align='right'> "+parseFloat(un_finished).toFixed(2)+" </td><tr>";
                    summary += "</table>";

                    summaryWithCondCont.html(summary);
                <?php else:?>
                    // -- Compute for totalRequiredBet, totalPlayerBet, un_finished
                    computeSummary(playerId);
                <?php endif;?>

              }else{
                withdrawalCondTable.clear().draw();
                depositCondTable.clear().draw();
                refreshWithConBtn.hide();
                summaryWithCondCont.html('');
              }

              withdrawCondLoader.hide();
              repeatedLoad = true;

            }).fail(function (jqXHR, textStatus) {
                if(jqXHR.status>=300 && jqXHR.status<500){
                    location.reload();
                }else{
                    alert(textStatus);
                }
           });
        }

        function computeSummary(playerId){
            $.ajax({
                url : GET_SUMMARIZE_WITHDRAWAL_CONDITION_URL+'/'+playerId,
                type : 'GET',
                dataType : "json"
            }).done(function (obj) {

                var arr = obj.summarizedWithdrawalCondition;
                if(arr){

                    var totalRequiredBet =  parseFloat(arr.totalRequiredBet),
                        totalPlayerBet   =  parseFloat(arr.totalPlayerBet),
                        un_finished      =  parseFloat(arr.unfinished),
                        summary          =  "<table class='table table-hover table-bordered'>";

                    if(un_finished < 0) un_finished = 0;

                    summary += "<tr><th class='active col-md-8'><b><?=lang('pay.totalRequiredBet')?>:</b></th><td align='right'>"+totalRequiredBet.format(2)+"</td></tr>";
                    summary += "<tr><th class='active col-md-8'><b><?=lang('pay.currTotalBet')?>:</b></th><td align='right'> "+totalPlayerBet.format(2)+"</td></tr>";
                    summary += "<tr><th class='active col-md-8'><b><?=lang('mark.unfinished')?>:</b></th><td align='right'> "+un_finished.format(2)+" </td><tr>";
                    summary += "</table>";

                    summaryWithCondCont.html(summary);

                }

            }).fail(function (jqXHR, textStatus) {
                /*Note: this is for session timeout,if the session is out because this is ajax, eventually it will go to log in page*/
                //never refresh again
                // location.reload();
                if(jqXHR.status>=300 && jqXHR.status<500){
                    // location.reload();
                }else{
                    alert(textStatus);
                }
            });
        }

        /**
        * Number.prototype.format(n, x)
        *
        * @param integer n: length of decimal
        * @param integer x: length of sections
        */
        Number.prototype.format = function(n, x) {
            var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
            return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
        };


        return {
            initDatatable:function(modalName) {
                initDatatable(modalName);
            },
            refresh:function(playerId) {
              getWithdrawalCondition(playerId);
            },
            cancel:function(){
                cancelWithdrawalCondition();
            }
        }
    }());
</script>

<style>
	.select-css {
		display: inline-block;
		font-size: 15px;
		color: #000000;
		line-height: 1.4;
		padding: 8px;
		width: 12%;
		height: 39px;
		box-sizing: border-box;
		margin: 0;
		border: 1px solid #cccccc;
		border-radius: 0;
		background-color: #ffffff;
		-moz-appearance: none;
		-webkit-appearance: none;
		appearance: none;
		background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23007CB2%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E');
		background-repeat: no-repeat, repeat;
		background-position: right .3em top 50%, 0 0;
		background-size: .65em auto, 100%;
	}
</style>

<input type="hidden" id="affiliate_id" value="<?php echo $affiliateId; ?>"/>
<div class="panel panel-primary">
	<div class="panel-heading">
		<a href="<?php echo site_url('affiliate_management/aff_list'); ?>" class="btn btn-default btn-sm pull-right" id="view_affiliate">
			<span class="glyphicon glyphicon-remove"></span>
		</a>
		<h4 class="panel-title"><i class="icon-list"></i> <?=lang('aff.ai61');?></h4>
		<div class="clearfix"></div>
	</div>

	<div class="panel-body" id="affiliate_panel_body">
		<!-- Personal Info -->
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title">

					<a href="#personal_info" id="hide_affpersonal_info" class="btn btn-info btn-sm">
						<i class="glyphicon glyphicon-chevron-up" id="hide_affpi_up"></i>
					</a> <?=lang('aff.ai01');?>

					<div class="pull-right">
			            <?php if ($this->utils->isEnabledMDB()) { ?>
			                <a href="<?=site_url('/affiliate_management/sync_aff_to_mdb/' . $affiliateId)?>" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-success'?>">
			                    <i class="fa fa-refresh"></i> <?=lang('Sync To Currency')?>
			                </a>
			            <?php } ?>

						<?php if ($this->permissions->checkPermissions('activate_deactivate_affiliate')): ?>
							<?php if ($affiliate['status'] == 1) {?>
								<input type='button' value='<?php echo lang('lang.active'); ?>' class='btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-info'?>' onclick="window.location.href='<?php echo site_url('affiliate_management/active/' . $affiliateId); ?>';">
							<?php } else {?>
								<input type='button' value='<?php echo lang('Blocked'); ?>' class='btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-chestnutrose' : 'btn-danger'?>' onclick="window.location.href='<?php echo site_url('affiliate_management/inactive/' . $affiliateId); ?>';">
							<?php }?>
						<?php endif ?>

						<?php if ($this->permissions->checkPermissions('edit_affiliate_info')) {?>
							<a href="<?=site_url('affiliate_management/editAffiliateInfo/' . $affiliateId)?>" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-default'?>"><i class="glyphicon glyphicon-edit"></i> <?=lang('lang.edit');?></a>
						<?php }?>
					</div>
				</h4>
			</div>
			<div class="panel-body affpersonal_panel_body" id="affiliate_info">
				<div class="table-responsive">
					<table class="table table-bordered" style="margin-bottom:0;">
						<tr>
							<th class="active col-md-2" style="height: 45px;"><?=lang('aff.ap03');?></th>
							<td class="col-md-4"><?=$affiliate['username']?></td>
							<th class="active col-md-2"><?=lang('aff.as23');?></th>
							<td class="col-md-4">
								<a href="<?php echo site_url('/affiliate_management/aff_list?by_parent_id=' . $affiliateId); ?>">
									<?=count($subaffiliates);?>
									<?php if ( ! empty($affiliate['trackingCode'])) {?>
										<a target="_blank" href="<?=$this->utils->getSystemUrl('aff') . '/affiliate/register' . '/' . $affiliate['trackingCode'];?>" class="btn btn-xs pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>"><?=lang('aff.asb9')?></a>
									<?php }?>
								</a>
							</td>
						</tr>
						<tr>
							<th class="active col-md-2" style="height: 45px;"><?=lang('aff.ai02');?></th>
							<td><?=(empty($affiliate['firstname'])) ? lang('N/A') : $affiliate['firstname']?></td>
							<th class="active col-md-2"><?=lang('aff.ai20');?></th>
							<td><?=(empty($affiliate['modeOfContact'])) ? lang('N/A') : $affiliate['modeOfContact']?></td>
						</tr>
						<tr>
							<th class="active col-md-2" style="height: 45px;"><?=lang('aff.ai03');?></th>
							<td><?=(empty($affiliate['lastname'])) ? lang('N/A') : $affiliate['lastname']?></td>
							<th class="active col-md-2"><?=lang('aff.ai07');?></th>
							<td><?=(empty($affiliate['occupation'])) ? lang('N/A') : $affiliate['occupation']?></td>
						</tr>
						<tr>
							<th class="active col-md-2" style="height: 45px;"><?=lang('aff.ai04');?></th>
							<td><?php echo $bdate = $affiliate['birthday'] == "0000-00-00 00:00:00" ? "N/A" : date("Y-m-d", strtotime($affiliate['birthday'])); ?></td>
							<th class="active col-md-2"><?=lang('aff.ai06');?></th>
							<td><?=(empty($affiliate['company'])) ? lang('N/A') : $affiliate['company']?></td>
						</tr>
						<tr>
							<th class="active col-md-2" style="height: 45px;"><?=lang('lang.affdomain');?></th>
							<td><?=(empty($affiliate['affdomain'])) ? lang('N/A') : $affiliate['affdomain']?></td>
							<th class="active col-md-2"><span id="_password_label"><?=lang('reg.05');?></span></th>
							<td class="text-right">
								<?php if ($this->permissions->checkPermissions('affiliate_admin_action')) {?>
									<a href="<?=site_url('affiliate_management/resetPassword/' . $affiliateId);?>" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>"><?=lang('forgot.08');?></a>
								<?php }?>
								<?php if ($this->utils->isEnabledFeature('affiliate_second_password')) {?>
									<a href="<?=site_url('affiliate_management/resetSecondPassword/' . $affiliateId);?>" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>"><?=lang('Reset Secondary Password');?></a>
								<?php }?>
								<?php if ($this->permissions->checkPermissions('login_as_aff')) {?>
									<a target="_blank" href="<?=site_url('affiliate_management/login_as_aff/' . $affiliateId);?>" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>"><?=lang('Login as Affiliate')?></a>
								<?php }?>
							</td>
						</tr>
						<tr>
							<th class="active col-md-2" style="height: 45px;"><?php echo lang('Parent Affiliate'); ?></th>
							<td>
								<?php if ( ! empty($parent)) {?>
									<a href="<?php echo site_url('affiliate_management/userInformation/' . $parent['affiliateId']); ?>" target="_blank"><?php echo $parent['username']; ?></a>
								<?php } else {
									echo lang('N/A');
								}?>
							</td>
							<th class="active col-md-2"><?=lang('transaction.credit');?></th>
							<td>
								<?=$affiliate['balance']?>
								<?php if ($this->permissions->checkPermissions('affiliate_admin_action')) {?>
									<a href="<?=site_url('affiliate_management/adjustBalance/' . $affiliateId);?>" class="btn btn-xs pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>"><?=lang('transaction.adjustCredit')?></a>
								<?php }?>
							</td>
						</tr>
						<tr>
							<th class="active col-md-2" style="height: 45px;"><?php echo lang('Prefix of player'); ?></th>
							<td><?=(empty($affiliate['prefix_of_player'])) ? lang('N/A') : $affiliate['prefix_of_player']?></td>
							<th class="active col-md-2"><?php echo lang('Tracking Code'); ?></th>
							<td>
								<?php echo $affiliate['trackingCode'];?>
								<a href="#hide_afftrack_info" class="btn btn-xs pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>"><?php echo lang('Edit');?></a>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<!-- End of Personal Info -->

		<!-- Contact Info -->
		<?php if ($this->permissions->checkPermissions('affiliate_contact_info')) {?>
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a href="#contact_info" id="hide_affcontact_info" class="btn btn-info btn-sm">
							<i class="glyphicon glyphicon-chevron-down" id="hide_affci_up"></i>
						</a> <?=lang('reg.74');?>
					</h4>
				</div>
				<div class="panel-body aff_contactinfo_panel_body" id="affiliate_contact_info" style="display:none;">
					<div class="table-responsive">
						<table class="table table-hover table-bordered" style="margin-bottom:0;">
							<tr>
								<th class="active col-md-2"><?=lang('aff.ai08');?></th>
								<td class="col-md-4"><?=(empty($affiliate['email'])) ? lang('N/A') : $affiliate['email']?></td>
								<th class="active col-md-2"><?=lang('aff.ai21');?></th>
								<td class="col-md-4"><?=(empty($affiliate['website'])) ? lang('N/A') : $affiliate['website']?></td>
							</tr>
							<tr>
								<th class="active col-md-2"><?=lang('aff.ai16');?></th>
								<td class="col-md-4"><?=(empty($affiliate['imType1']) || empty($affiliate['im1'])) ? lang('N/A') : lang($affiliate['imType1']) . " (" . $affiliate['im1'] . ")"?></td>
								<th class="active col-md-2"><?=lang('aff.ai18');?></th>
								<td class="col-md-4"><?=(empty($affiliate['imType2']) || empty($affiliate['im2'])) ? lang('N/A') : lang($affiliate['imType2']) . " (" . $affiliate['im2'] . ")"?></td>
							</tr>
							<tr>
								<th class="active col-md-2"><?=lang('IM3 Type');?></th>
								<td class="col-md-4"><?=(empty($affiliate['imType3']) || empty($affiliate['im3'])) ? lang('N/A') : lang($affiliate['imType3']) . " (" . $affiliate['im3'] . ")"?></td>
								<th class="active col-md-2"><?=lang('IM4 Type');?></th>
								<td class="col-md-4"><?=(empty($affiliate['imType4']) || empty($affiliate['im4'])) ? lang('N/A') : lang($affiliate['imType4']) . " (" . $affiliate['im4'] . ")"?></td>
							</tr>
							<tr>
								<th class="active col-md-2"><?=lang('IM5 Type');?></th>
								<td class="col-md-4"><?=(empty($affiliate['imType5']) || empty($affiliate['im5'])) ? lang('N/A') : lang($affiliate['imType5']) . " (" . $affiliate['im5'] . ")"?></td>
								<th class="active col-md-2"><?=lang('IM6 Type');?></th>
								<td class="col-md-4"><?=(empty($affiliate['imType6']) || empty($affiliate['im6'])) ? lang('N/A') : lang($affiliate['imType6']) . " (" . $affiliate['im6'] . ")"?></td>
							</tr>
							<tr>
								<th class="active col-md-2"><?=lang('aff.ai14');?></th>
								<td class="col-md-4"><?=(empty($affiliate['mobile'])) ? lang('N/A') : $affiliate['mobile']?></td>
								<th class="active col-md-2"><?=lang('aff.ai15');?></th>
								<td class="col-md-4"><?=(empty($affiliate['phone'])) ? lang('N/A') : $affiliate['phone']?></td>
							</tr>
							<tr>
								<th class="active col-md-2"><?=lang('aff.ai10');?></th>
								<td class="col-md-4"><?=(empty($affiliate['address'])) ? lang('N/A') : $affiliate['address']?></td>
								<th class="active col-md-2"><?=lang('aff.ai09');?></th>
								<td class="col-md-4"><?=(empty($affiliate['city'])) ? lang('N/A') : $affiliate['city']?></td>
							</tr>
							<tr>
								<th class="active col-md-2"><?=lang('aff.ai13');?></th>
								<td class="col-md-4"><?=(empty($affiliate['country'])) ? lang('N/A') : $affiliate['country']?></td>
								<th class="active col-md-2"><?=lang('aff.ai12');?></th>
								<td class="col-md-4"><?=(empty($affiliate['state'])) ? lang('N/A') : $affiliate['state']?></td>
							</tr>
							<tr>
								<th class="active col-md-2"><?=lang('aff.ai11');?></th>
								<td class="col-md-4"><?=(empty($affiliate['zip'])) ? lang('N/A') : $affiliate['zip']?></td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		<?php }?>
		<!-- End of Contact Info -->

		<!-- Bank Info -->
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a href="#bank_info" id="hide_affbank_info" class="btn btn-info btn-sm">
						<i class="glyphicon glyphicon-chevron-up" id="hide_affbi_up"></i>
					</a> <?=lang('aff.ai22');?>
					<a href="<?=site_url('affiliate_management/addBankInfo/' . $affiliateId)?>" class="btn btn-sm btn-info pull-right"><i class="glyphicon glyphicon-plus"></i> <?=lang('lang.add');?></a>
				</h4>
			</div>
			<div class="panel-body affbank_panel_body" style="display: none;">
				<div class="row">
					<div class="col-md-3">
						<div>
							<label class="text-info"><h4 style="margin-bottom: 0; font-weight: bold; margin-top: 0; margin-bottom: 0;"><?php echo lang('Main Wallet'); ?>:</h4></label>
							<span class="text-warning"><strong><?php echo $this->utils->formatCurrencyNoSym(@$affiliate['wallet_balance']); ?></strong></span>
						</div>
						<div>
							<label class="text-info"><h6 style="margin-bottom: 0; font-weight: bold; margin-top: 0; margin-bottom: 0;"><?php echo lang('Locked Wallet'); ?>:</h6></label>
							<span class="text-warning small"><strong><?php echo $this->utils->formatCurrencyNoSym(@$affiliate['wallet_hold']); ?></strong></span>
						</div>
						<div>
							<label class="text-info"><h6 style="margin-bottom: 0; font-weight: bold; margin-top: 0; margin-bottom: 0;"><?php echo lang('Pending withdraw amount'); ?>:</h6></label>
							<span class="text-warning small"><strong><?php echo $this->utils->formatCurrencyNoSym(@$affiliate['frozen']); ?></strong></span>
						</div>
					</div>

					<div class="col-md-9">
						<div class="btn-group" role="group" style="margin-bottom: 10px;">
							<?php if ($this->permissions->checkPermissions('affiliate_withdraw_from_hold')) {?>
							<input type="button" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-info'?> btn-xs btn_transfer_bal_from_main" value="<?php echo lang('Main Wallet To Locked Wallet'); ?>">
							<?php }?>
							<?php if ($this->permissions->checkPermissions('affiliate_deposit_to_hold')) {?>
							<input type="button" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-info'?> btn-xs btn_transfer_bal_to_main" value="<?php echo lang('Locked Wallet To Main Wallet'); ?>">
							<?php }?>
						</div>

						<div class="btn-group" role="group" style="margin-bottom: 10px;">
							<?php if ($this->permissions->checkPermissions('affiliate_deposit')) {?>
							<input type="button" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-info'?> btn-xs btn_deposit" value="<?php echo lang('Deposit To Main'); ?>">
							<?php }?>
							<?php if ($this->permissions->checkPermissions('affiliate_withdraw')) {?>
							<input type="button" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-info'?> btn-xs btn_withdraw" value="<?php echo lang('Withdraw From Main'); ?>">
							<?php }?>
							<?php if ($this->permissions->checkPermissions('affiliate_deposit_to_hold')) {?>
							<input type="button" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-info'?> btn-xs btn_withdraw_bal" value="<?php echo lang('Withdraw From Locked Wallet'); ?>">
							<?php }?>
						</div>

						<div class="btn-group" role="group" style="margin-bottom: 10px;">
							<?php if ($this->permissions->checkPermissions('affiliate_deposit')) {?>
							<a href="/affiliate_management/affiliate_manual_add_balance/<?=$affiliateId?>" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-info'?> btn-xs btn_manual_add_balance"><?php echo lang('Manual Add Balance'); ?></a>
							<?php }?>
							<?php if ($this->permissions->checkPermissions('affiliate_withdraw')) {?>
							<a href="/affiliate_management/affiliate_manual_subtract_balance/<?=$affiliateId?>" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-info'?> btn-xs btn_manual_subtract_balance"><?php echo lang('Manual Subtract Balance'); ?></a>
							<?php }?>
						</div>
					</div>
				</div>
				<hr/>
				<table class="table" id="bankTable">
					<thead>
						<th></th>
						<th><?=lang('aff.ai23');?></th>
						<th><?=lang('aff.ai90');?></th>
						<th><?=lang('aff.ai24');?></th>
						<th><?=lang('aff.ai25');?></th>
						<th><?=lang('aff.ai26');?></th>
						<th><?=lang('aff.ai27');?></th>
						<th><?=lang('aff.ai28');?></th>
						<th><?=lang('lang.action');?></th>
					</thead>
					<tbody>
						<?php if (!empty($bank)): ?>
							<?php foreach ($bank as $bank_value): ?>
								<tr>
									<td></td>
									<td><?=lang($bank_value['bankName'])?></td>
									<td><?=$bank_value['accountName']?></td>
									<td><?=$bank_value['accountInfo']?></td>
									<td><?=$bank_value['accountNumber']?></td>
									<td><?=(empty(strtotime($bank_value['createdOn']))) ? '' : date("Y-m-d H:i:s", strtotime($bank_value['createdOn']))?></td>
									<td><?=(empty(strtotime($bank_value['updatedOn']))) ? '' : date("Y-m-d H:i:s", strtotime($bank_value['updatedOn']))?></td>
									<td><?=($bank_value['status'] == '0') ? lang('aff.ai29') : lang('aff.ai30')?></td>
									<td>
										<?php if ($this->utils->isEnabledFeature('enabled_edit_affiliate_bank_account') && $this->permissions->checkPermissions('edit_affiliate_bank_account')) {?>
											<a href="<?=site_url('affiliate_management/editPayment/' . $bank_value['affiliatePaymentId'])?>" data-toggle="tooltip" title="<?=lang('lang.edit');?>"><i class="glyphicon glyphicon-edit"></i></a>
										<?php }?>


										<?php if ($bank_value['status'] == 0) {?>
											<a href="#" data-toggle="tooltip" title="<?=lang('lang.deactivate');?>" class="inactive" onclick="deactivatePayment('<?=$bank_value['affiliatePaymentId']?>', '<?=lang($bank_value['bankName'])?>', '<?=$affiliateId?>'); "><i class="glyphicon glyphicon-remove-circle"></i></a>
										<?php } else {?>
											<a href="#" data-toggle="tooltip" title="<?=lang('lang.activate');?>" class="active" onclick="activatePayment('<?=$bank_value['affiliatePaymentId']?>', '<?=lang($bank_value['bankName'])?>', '<?=$affiliateId?>'); "><i class="glyphicon glyphicon-ok-sign"></i></a>
											<a href="#" data-toggle="tooltip" title="<?=lang('lang.delete');?>" class="active" onclick="deletePayment('<?=$bank_value['affiliatePaymentId']?>', '<?=lang($bank_value['bankName'])?>', '<?=$affiliateId?>'); "><i class="glyphicon glyphicon-remove"></i></a>
										<?php }?>
									</td>
								</tr>
							<?php endforeach ?>
						<?php endif ?>
					</tbody>
				</table>
			</div>
		</div>
		<!-- End of Bank Info -->

		<!-- Payment History -->
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a href="#personal_info" id="hide_affpay_info" class="btn btn-info btn-sm">
						<i class="glyphicon glyphicon-chevron-up" id="hide_affap_up"></i>
					</a> <?=lang('aff.ai54');?>
				</h4>
			</div>
			<div class="panel-body affap_panel_body" style="display: none;">
				<div id="paymentHistory" class="table-responsive">
					<table class="table table-striped" id="paymentTable" style="width: 100%;">
						<thead>
							<th></th>
							<th><?=lang('Date');?></th>
							<th><?=lang('Amount');?></th>
							<th><?=lang('Before Balance');?></th>
							<th><?=lang('After Balance');?></th>
							<th><?=lang('Notes');?></th>
						</thead>
						<tbody>
							<?php if (!empty($transactions)): ?>
								<?php foreach ($transactions as $payment_value): ?>
									<tr>
										<td></td>
										<td><?php echo $payment_value['created_at']; ?></td>
										<td><?php echo $this->utils->formatCurrencyNoSym($payment_value['amount']); ?></td>
										<td><?php echo $this->utils->formatCurrencyNoSym($payment_value['before_balance']); ?></td>
										<td><?php echo $this->utils->formatCurrencyNoSym($payment_value['after_balance']); ?></td>
										<td><?php echo (empty($payment_value['note'])) ? lang('player.ub12') : $payment_value['note'] ?></td>
									</tr>
								<?php endforeach ?>
							<?php endif ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- End of Payment History -->

		<!-- Aff Term -->
		<?php if ($this->permissions->checkPermissions('edit_affiliate_term')): ?>
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a href="#share_settings" id="hide_share_settings" class="btn btn-info btn-sm">
							<i class="glyphicon glyphicon-chevron-up" id="hide_share_settings_up"></i>
						</a>
						<?=lang('aff.ai101');?>
					</h4>
					<div class="clearfix"></div>
				</div><!-- end panel-heading -->
				<div class="panel-body collapse in share_settings_body">
					<form id="form_operator" method="POST" action="<?php echo site_url('/affiliate_management/save_common_setup_with_operator_settings/' . $affiliateId); ?>">
						<?=$this->load->view('affiliate_management/setup/operator_settings', array('affiliateId' => $affiliateId, 'settings' => $affiliateSettings), TRUE); ?>
					</form>
				</div><!-- end panel-body -->
			</div><!-- end panel -->
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h4 class="panel-title pull-left">
						<a href="#commission_settings" id="hide_commission_settings" class="btn btn-info btn-sm">
							<i class="glyphicon glyphicon-chevron-up" id="hide_commission_settings_up"></i>
						</a>
						<?=lang('aff.sb9');?>
					</h4>
					<div class="clearfix"></div>
				</div><!-- end panel-heading -->

				<div class="panel-body collapse in commission_settings_body">
					<form id="form_option_1"  method="POST" action="<?php echo site_url('/affiliate_management/save_common_setup/commission_setup/' . $affiliateId);?>">
						<div class="row">
							<div class="col-md-12">
								<label><b><?=lang('a_header.affiliate') . ' ' . lang('lang.settings');?></b></label>
								<fieldset>
									<br>
									<div class="form-group">
										<div class="input-group">
									      	<div class="input-group-addon"><?php echo lang('Total Active Players'); ?></div>
											<input type="number" class="form-control" name="totalactiveplayer" value="<?php echo $affiliateSettings['totalactiveplayer']; ?>" required="required" min="0" step="1"/>
									      	<div class="input-group-addon">#</div>
									    </div>
									</div>
								</fieldset>
								<br>
								<div class="form-group">
									<label><b><?php echo lang('Betting Amount');?></b></label>
									<fieldset>
										<br>
										<div class="form-row">

											<div class="form-group col-xs-12">
												<?=lang('earnings.minBetting');?>
											</div>

											<?php foreach ($game as $g) { ?>
							                <div class="form-group col-xs-4">
												<label class="control-label"><?=$g['system_code'];?></label>
												<input type="number" name="provider_betting_amount_<?php echo $g['id'];?>" class="form-control input-sm" value="<?php echo $affiliateSettings['provider_betting_amount'][$g['id']];?>" min="0"/>
											</div>
											<?php }?>

											<div class="form-group col-xs-12">
												<p class="help-block well well-sm"><?php echo lang('Zero or Empty means ignore this'); ?></p>
											</div>

											<div class="form-group col-xs-12">
												<div class="input-group">
											      	<div class="input-group-addon"><?php echo lang('Minimum Total Betting');?></div>
													<input type="number" class="form-control input-sm" name="minimumBetting" value="<?php echo $affiliateSettings['minimumBetting']; ?>" min="0"/>
											    </div>
										    </div>
											<?php foreach ($game as $g) { ?>
							                <div class="form-group col-xs-4">
												<label class="control-label">
													<input type="checkbox" name="provider[]" value="<?=$g['id'];?>" <?php echo in_array($g['id'], $affiliateSettings['provider']) ? "checked='checked'" : "";?>>
													<?=$g['system_code'];?>
												</label>
											</div>
											<?php }?>
									    </div>

									    <?php if ($this->utils->isEnabledFeature('switch_to_ibetg_commission')) { ?>
										<div class="row">
											<div class="col-xs-12">
												<div class="input-group">
											      	<div class="input-group-addon"><?php echo lang('Minimum Total Betting Times');?></div>
													<input type="text" class="form-control input-sm" name="minimumBettingTimes" maxlength="10" value="<?php echo $affiliateSettings['minimumBettingTimes']; ?>" />
											    </div>
										    </div>
									    </div>
									    <?php } ?>

						            </fieldset>
					            </div>
					            <label><b><?=lang('aff.ai96');?></b></label>
								<fieldset>
									<br>
									<div class="form-group">
										<div class="input-group">
									      	<div class="input-group-addon"><i class="fa fa-money"></i></div>
											<input type="text" class="form-control amount_only" name="minimumDeposit" maxlength="15" value="<?php echo $affiliateSettings['minimumDeposit']; ?>" />
									    </div>
									</div>
								</fieldset>
								<br>
								<p class="help-block well"><b><?php echo lang('Active Player'); ?></b>
									<?php if ($this->utils->isEnabledFeature('affiliate_commision_check_deposit_and_bet')) { ?>
										<?=lang('aff.ai103');?>
									<?php }  else { ?>
										<?=lang('aff.ai91');?>
									<?php } ?>
								</p>
							</div><!-- end col-md-6 -->
						</div><!-- end row -->
						<button type="submit" id="option_1_submit" class="btn pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>"><i class="fa fa-floppy-o"></i> <?=lang('sys.vu70');?></button>
					</form>
				</div>
			</div><!-- end panel -->

			<div class="panel panel-primary">
				<div class="panel-heading">
					<h4 class="panel-title pull-left">
						<a href="#sub_aff_settings" id="hide_sub_aff_settings" class="btn btn-info btn-sm">
							<i class="glyphicon glyphicon-chevron-up" id="hide_sub_aff_settings_up"></i>
						</a>
						<?=lang('aff.sb10');?>
					</h4>

					<div class="clearfix"></div>
				</div><!-- end panel-heading -->
				<div class="panel-body collapse in sub_aff_settings_body" >
					<form id="sub_affiliate_settings" method="POST" action="<?php echo site_url('/affiliate_management/save_common_setup/sub_affiliate_settings/' . $affiliateId); ?>">
						<input type="hidden" id="sub_level" name="sub_level" value="<?php echo $affiliateSettings['sub_level']; ?>">
						<div class="row">
							<!-- sub option -->
							<div class="col-md-12" id="btn_group_sub_allowed">
								<div class="form-group">
									<fieldset>
										<br>
										<div class="col-xs-4">
											<div class="form-group">
												<label>
													<input type="checkbox" name="manual_open" id="manual_open" class="toggle-sub-levels" value="true" <?php echo $affiliateSettings['manual_open'] ? 'checked="checked"' : ""; ?>  disabled="disabled">
													<?=lang('aff.ai94');?>
												</label>
											</div>
										</div>
										<div class="col-xs-4">
											<div class="form-group">
												<label>
													<input type="checkbox" name="sub_link" id="sub_link" class="toggle-sub-levels" value="true" <?php echo $affiliateSettings['sub_link'] ? 'checked="checked"' : ""; ?>  disabled="disabled">
													<?=lang('aff.ai95');?>
												</label>
											</div>
										</div>
										<div class="col-xs-4">
											<div class="form-group text-right">
												<button type="button" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-chestnutrose' : 'btn-default'?>" onclick="$('#btn-sub_affiliate_settings').show(); $('#manual_open, #sub_link').prop('disabled', false); $('.toggle-sub-levels').trigger('change'); $(this).hide();"><?=lang('icon.locked') . " " . lang('system.word56');?></button>
												<button type="submit" form="sub_affiliate_settings" id="btn-sub_affiliate_settings" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" style="display: none;"><?=lang('Update');?></button>
											</div>
										</div>

									</fieldset>
								</div>
							</div>
						</div><!--end row -->
						<div class="row">
							<div class="col-md-12">
								<fieldset>
									<label id="sub_level_label"><?=lang('aff.ai99');?></label>
									<div class="row" id="sub_level_container">
										<?php $sub_levels = $affiliateSettings['sub_levels']; ?>
										<?php foreach ($sub_levels as $key => $value) {?>
											<div class="col-md-6">
												<div class="form-group">
													<div class="input-group">
														<div class="input-group-addon"><?=lang('lang.level');?> <?php echo $key + 1; ?>:</div>
														<input type="hidden" name="sub_levels[<?=$key?>]" value="<?php echo $commonSettings['sub_levels'][$key]; ?>"/>
														<input type="number" class="form-control sub-levels" name="sub_levels[<?=$key?>]" value="<?php echo $value; ?>" step="any" disabled="disabled"/>
														<div class="input-group-addon">%</div>
													</div>
												</div>
											</div><!-- end col-md-6 -->
										<?php } ?>
									</div><!-- sub_level_container -->
								</fieldset>
							</div><!-- end col-md-12 -->
						</div>
					</form>
				</div><!-- end panel-body -->
			</div><!-- end panel -->
		<?php endif ?>
		<!-- End of Aff Term -->

		<!-- Tracking Code -->
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a name="hide_afftrack_info" href="#personal_info" id="hide_afftrack_info" class="btn btn-info btn-sm">
						<i class="glyphicon glyphicon-chevron-up" id="hide_affatk_up"></i>
					</a> <?=lang('aff.ai36');?>
				</h4>
			</div>

			<div class="panel-body afftrack_panel_body">
				<form action="<?=site_url('affiliate_management/createCode/' . $affiliateId)?>" method="POST" class="form-horizontal">
					<div class="row">
						<div class="col-md-6 col-md-offset-3">
							<div class="form-group col-xs-5">
								<label for="tracking_code" class="control-label" style="text-align:right;"><?=lang('aff.ai40');?> </label>
								<div>
									<input type="text" name="tracking_code" id="tracking_code" class="form-control <?=$this->utils->isEnabledFeature('affiliate_tracking_code_numbers_only') ? 'number_only' : ''?>" minlength="5" maxlength="8" value="<?php echo $affiliate['trackingCode']; ?>" />
									<?php echo form_error('tracking_code', '<span style="color:#CB3838;">'); ?>
								</div>
							</div>
							<?php if ($this->permissions->checkPermissions('add_affiliate_code')) {?>
							<div class="btn-group col-xs-7" id="random_code_sec" role="group" aria-label="..." style="margin-top: 23px;">

			                    <?php if ($this->utils->isEnabledFeature('affiliate_tracking_code_numbers_only')): ?>
							  	<a href="#randomCode" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default'?> hidden-xs" id="random_code" onclick="randomNumber('8');">
                   				<?php else: ?>
								<a href="#randomCode" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default'?> hidden-xs" id="random_code" onclick="randomCode('8');">
                    			<?php endif ?>
									<i class="fa fa-calculator"></i> <?=lang('aff.ai38');?>
								  </a>
								  <input type="submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" value="<?=lang('Update');?>"/>

							</div>
							<div class="btn-group col-xs-7" id="random_code_lock" role="group" aria-label="..." style="margin-top: 23px;">
								<a href="#randomCode" class="btn hidden-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-chestnutrose' : 'btn-default'?>" id="random_code" onclick="unlock_tracking_code();">
									<?=lang('icon.locked') . "" . lang('system.word56');?>
								  </a>
							</div>
							<?php }?>
						</div>
						<div class="clearfix"></div>
					</div>
				</form>

				<div class="row">
					<div class="col-md-12" style="overflow: auto;">
						<table class="table table-striped">
							<thead>
								<th colspan="2"><?=lang('aff.ai41');?></th>
							</thead>

							<tbody>
								<?php if (!empty($domain) && !empty($affiliate['trackingCode'])) {
										foreach ($domain as $key => $domain_value) { ?>
										<tr>
											<td><?=$domain_value['domainName'] . '/aff/' . $affiliate['trackingCode']?></td>
											<td><?=$domain_value['domainName'] . '/aff.html?code=' . $affiliate['trackingCode']?></td>
										</tr>
									<?php }?>
								<?php } else { ?>
										<tr>
											<td colspan="6" style="text-align:center"><span class="help-block"><?php echo lang('N/A'); ?></span></td>
										</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</div>

				<?php if ($this->permissions->checkPermissions('add_affiliate_code') && $this->utils->isEnabledFeature("affiliate_source_code")) {?>
					<script type="text/javascript">
						function editSourceCode(affTrackingId, sourceCode, remarks){
							BootstrapDialog.show({
								title: '<?php echo lang('Edit'); ?>',
								message: '<form method="POST" class="frm_edit_source_code_'+affTrackingId+'" action="<?php echo site_url("/affiliate_management/change_source_code/".$affiliateId); ?>/'+affTrackingId+'">'+
		                        '<?=lang("Affiliate Source Code"); ?>: <input type="text" name="sourceCode" class="form-control" value="'+sourceCode+'">'+
		                        '<?php if($this->utils->isEnabledFeature('enable_tracking_remarks_field')) {?><?=lang("Remarks"); ?>: <input type="text" name="remarks" class="form-control" value="'+remarks+'"></form><?php } ?>',
								spinicon: 'fa fa-spinner fa-spin',
								buttons: [{
									icon: 'fa fa-save',
									label: '<?php echo lang('Save'); ?>',
									cssClass: 'btn-primary',
									autospin: true,
									action: function(dialogRef){
										dialogRef.enableButtons(false);
										dialogRef.setClosable(false);

										var frm=dialogRef.getModalBody().find('.frm_edit_source_code_'+affTrackingId);
										frm.submit();
									}
								}, {
									label: '<?php echo lang('Cancel'); ?>',
									action: function(dialogRef){
										dialogRef.close();
									}
								}]
							});
						}

						function newSourceCode(){
							BootstrapDialog.show({
								title: '<?php echo lang('New'); ?>',
								message: '<form method="POST" class="frm_new_source_code" action="<?php echo site_url("/affiliate_management/new_source_code/".$affiliateId); ?>">'+
		                        '<?=lang("New Affiliate Source Code"); ?>: <input type="text" name="sourceCode" class="form-control" value="">'+
		                        '<?php if($this->utils->isEnabledFeature('enable_tracking_remarks_field')) {?><?=lang("Remarks"); ?>: <input type="text" name="remarks" class="form-control" value=""></form><?php } ?>',
								spinicon: 'fa fa-spinner fa-spin',
								buttons: [{
									icon: 'fa fa-save',
									label: '<?php echo lang('Save'); ?>',
									cssClass: 'btn-primary',
									autospin: true,
									action: function(dialogRef){
										dialogRef.enableButtons(false);
										dialogRef.setClosable(false);

										var frm=dialogRef.getModalBody().find('.frm_new_source_code');
										frm.submit();
									}
								}, {
									label: '<?php echo lang('Cancel'); ?>',
									action: function(dialogRef){
										dialogRef.close();
									}
								}]
							});
						}

						function removeSourceCode(affTrackingId, sourceCode){
							BootstrapDialog.show({
								title: '<?php echo lang('Delete'); ?>',
								message: '<form method="POST" class="frm_remove_source_code" action="<?php echo site_url("/affiliate_management/remove_source_code/".$affiliateId); ?>/'+affTrackingId+'"><?php echo lang("Affiliate Source Code"); ?>: <input type="text" disabled="disabled" class="form-control" value="'+sourceCode+'"></form>',
								spinicon: 'fa fa-spinner fa-spin',
								buttons: [{
									icon: 'fa fa-save',
									label: '<?php echo lang('Delete'); ?>',
									cssClass: 'btn-danger',
									autospin: true,
									action: function(dialogRef){
										dialogRef.enableButtons(false);
										dialogRef.setClosable(false);
										// utils.safelog(dialogRef);

										var frm=dialogRef.getModalBody().find('.frm_remove_source_code');
										frm.submit();
										// dialogRef.getModalBody().html('Dialog closes in 5 seconds.');
										// setTimeout(function(){
										//     dialogRef.close();
										// }, 5000);
									}
								}, {
									label: '<?php echo lang('Cancel'); ?>',
									action: function(dialogRef){
										dialogRef.close();
									}
								}]
							});
						}
					</script>

					<div class="row">
						<div class="col-md-12" style="overflow: auto;">
							<a name="aff_source_code_list">
							<a href="javascript:void(0)" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" onclick="newSourceCode()"><?php echo lang('New Affiliate Source Code');?></a>
							<table class="table table-striped">
								<thead>
									<th class="col-md-3"><?php echo lang('Affiliate Source Code');?></th>
									<th class="col-md-7"><?php echo lang('Link Example');?></th>
									<th class="col-md-2"><?php echo lang('Action');?></th>
								</thead>
								<tbody>
								<?php foreach($aff_source_code_list as $source_code){?>
									<tr>
										<td><?php echo $source_code['tracking_source_code']; ?></td>
										<td><?php echo !empty($first_domain) ? $first_domain.'/aff/'.$affiliate['trackingCode'].'/'.$source_code['tracking_source_code'] : ""; ?><br>
										<?php echo !empty($first_domain) ? $first_domain.'/aff.html?code='.$affiliate['trackingCode'].'&source='.$source_code['tracking_source_code'] : ""; ?></td>
										<td><a href="javascript:void(0)" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>" onclick="editSourceCode('<?php echo $source_code['id'];?>', '<?php echo $source_code['tracking_source_code'];?>', '<?=$source_code['remarks']?>')"><?php echo lang('Edit');?></a>
										<a href="javascript:void(0)" id="frm_remove_domain" onclick="removeSourceCode('<?php echo $source_code['id'];?>',  '<?php echo $source_code['tracking_source_code']; ?>')" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-chestnutrose' : 'btn-primary'?>"><?php echo lang('Delete'); ?></a>
										</td>
									</tr>
								<?php }?>
								</tbody>
							</table>
						</div>
					</div>
				<?php }?>

				<?php if ($this->permissions->checkPermissions('add_affiliate_code')) {?>
					<script type="text/javascript">
						var affiliateId = '<?php echo $affiliateId; ?>';
						var msgSucs = '<?php echo lang('Domain updated.');?>';
						var msgFail = '<?php echo lang('Update failed.');?>';
						var msgErrForFillin = '<?php echo lang('Please fill in domain.');?>';
						var msgErrForExistence = '<?php echo lang('Domain already exist in other affiliate, please try again with another domain.');?>';
						var msgDeleteSucs = '<?php echo lang('Domain deleted.');?>';
						var msgDeleteFail = '<?php echo lang('Deleted failed.');?>';

						function newAffdomain(){
							BootstrapDialog.show({
								title: '<?php echo $affiliate['username']; ?> - <?php echo lang('Dedicated Domain'); ?>',
								message: '<div><select class="select-css" name="affdomainTitle"><option>https</option><option>http</option></select>'+'&nbsp://&nbsp'+'<input name="affdomainContent" type="text" required class="form-control" value="" style="display:inline-block; width:84%;"></div>',
								spinicon: 'fa fa-spinner fa-spin',
								onshow: function(dialog) {
									dialog.getButton('newAffDomainSaveBtn').click(function(event){
										var affdomainTitle = $(".select-css").find(":selected").text();
										var affdomainContent = $("input[name = affdomainContent]").val();
										var $btnSave = dialog.getButton('newAffDomainSaveBtn');
										$btnSave.spin();


										var affdomainContent = affdomainContent.replace(/(^s*)|(s*$)/g, "");
										if(affdomainContent.length ==0){
											MsgDialog(msgErrForFillin);
                        					$btnSave.stopSpin();
										}
										else{
											var url =  '/affiliate_management/new_affdomain';

											$.ajax({
												type: 'POST',
												url: url,
												data: {
													'affiliateId': affiliateId,
													'affdomainTitle': affdomainTitle,
													'affdomainContent': affdomainContent
												},
												success: function (data){
													if (data.success) {
														MsgDialog(msgSucs,'','btn-primary');
														dialog.close();
													}
													else if(data.success == null){
														MsgDialog(msgErrForExistence);
														$btnSave.stopSpin();
													}
													else {
														MsgDialog(msgFail);
														$btnSave.stopSpin();
													}
												},
												dataType: 'json'
											});
										}
									});
								},
								buttons: [{
									icon: 'fa fa-save',
									label: '<?php echo lang('Save'); ?>',
									cssClass: 'btn-primary',
									id: 'newAffDomainSaveBtn',
									autospin: true,
									enabled: true
								}, {
									label: '<?php echo lang('Cancel'); ?>',
									action: function(dialogRef){
										dialogRef.close();
									}
								}]
							});
						}

						function editAffdomain(dedicatedDomain){
							var optionhttps = "";
							var optionhttp = "";
							if(dedicatedDomain.match('https://') != null) {
								var affdomainContent = dedicatedDomain.replace('https://',"");
								var optionhttps = "selected";
							}
							if(dedicatedDomain.match('http://') != null) {
								var affdomainContent = dedicatedDomain.replace('http://',"");
								var optionhttp = "selected";
							}

							BootstrapDialog.show({
								title: '<?php echo $affiliate['username']; ?> - <?php echo lang('Dedicated Domain'); ?>',
								message: '<div><select class="select-css" name="affdomainTitle"><option '+optionhttps+'>https</option><option '+optionhttp+'>http</option></select>'+'&nbsp://&nbsp'+'<input name="changeAffdomain" type="text" class="form-control" value="'+affdomainContent+'" style="display:inline-block; width:84.4%;"></div>',
								spinicon: 'fa fa-spinner fa-spin',
								onshow: function(dialog) {
									dialog.getButton('editAffDomainSaveBtn').click(function(event){
										var affdomainTitle = $(".select-css").find(":selected").text();
										var affdomainContent = $("input[name = changeAffdomain]").val();
										var $btnSave = dialog.getButton('editAffDomainSaveBtn');
										$btnSave.spin();


										var affdomainContent = affdomainContent.replace(/(^s*)|(s*$)/g, "");
										if(affdomainContent.length == 0){
											MsgDialog(msgErrForFillin);
                        					$btnSave.stopSpin();
										}
										else{

											var url =  '/affiliate_management/change_affdomain';

											$.ajax({
												type: 'POST',
												url: url,
												data: {
													'affiliateId': affiliateId,
													'affdomainTitle': affdomainTitle,
													'affdomainContent': affdomainContent
												},
												success: function (data){
													if (data.success) {
														MsgDialog(msgSucs,'','btn-primary');
														dialog.close();
													}
													else if(data.success == null){
														MsgDialog(msgErrForExistence);
														$btnSave.stopSpin();
													}
													else {
														MsgDialog(msgFail);
														$btnSave.stopSpin();
													}
												},
												dataType: 'json'
											});
										}
									});
								},
								buttons: [{
									icon: 'fa fa-save',
									label: '<?php echo lang('Save'); ?>',
									cssClass: 'btn-primary',
									id: 'editAffDomainSaveBtn',
									autospin: true,
								}, {
									label: '<?php echo lang('Cancel'); ?>',
									action: function(dialogRef){
										dialogRef.close();
									}
								}]
							});
						}

						function removeAffdomain(dedicatedDomain){
							BootstrapDialog.show({
								title: '<?php echo $affiliate['username']; ?> - <?php echo lang('Dedicated Domain'); ?>',
								type: BootstrapDialog.TYPE_DANGER,
								message: '<div><div><?=lang("Are you sure you want to delete this domain?"); ?></div>'+
		                        '<input type="text" name="removeAffdomain" disabled="disabled" class="form-control" value="'+dedicatedDomain+'"></div>',
								spinicon: 'fa fa-spinner fa-spin',
								onshow: function(dialog) {
									dialog.getButton('removeAffDomainBtn').click(function(){
										var url =  '/affiliate_management/remove_affdomain/' + affiliateId;
										$.getJSON(url, function(data) {
											if (data.success) {
												MsgDialog(msgDeleteSucs);
												dialog.close();
											}
											else {
												MsgDialog(msgDeleteFail);
												$btnSave.stopSpin();
											}
										});
									});
								},
								buttons: [{
									icon: 'fa fa-save',
									label: '<?php echo lang('Delete'); ?>',
									cssClass: 'btn-danger',
									id: 'removeAffDomainBtn',
									autospin: true
								}, {
									label: '<?php echo lang('Cancel'); ?>',
									action: function(dialogRef){
										dialogRef.close();
									}
								}]
							});
						}

						function MsgDialog(msg, dialogType = BootstrapDialog.TYPE_DANGER, btnClass = ''){
							BootstrapDialog.show({
								title: '<?php echo $affiliate['username']; ?> - <?php echo lang('Dedicated Domain'); ?>',
								type: dialogType,
								message: msg,
								buttons: [{
									label: '<?php echo lang('aff.ok'); ?>',
									cssClass: btnClass,
									action: function(dialogRef){
										dialogRef.close();

										if(msg == msgSucs || msg == msgDeleteSucs){
											window.location.reload(true);
										}
									}
								}]
							});
						}
					</script>
					<div class="row">
						<div class="col-md-12" style="overflow: auto;">
							<?php if (empty($affiliate['affdomain'])) {?>
								<a href="javascript:void(0)" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" onclick="newAffdomain()"><?php echo lang('New Affiliate Dedicate Domain');?></a>
							<?php }?>
							<table class="table table-striped">
								<thead>
									<th class="col-md-10"><?php echo lang('Dedicated Affiliate Domain Name'); ?></th>
									<th class="col-md-2"><?php echo lang('Action');?></th>
								</thead>
								<tbody>
									<tr>
										<?php if (empty($affiliate['affdomain'])) {?>
											<td>
												<?php echo lang('N/A');?>
											</td>
											<td>
											</td>
										<?php } else {?>
											<td>
												<a href="<?php echo $affiliate['affdomain']; ?>" target="_blank"><?php echo $affiliate['affdomain']; ?></a>
											</td>
											<td>
												<a href="javascript:void(0)" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>" onclick="editAffdomain('<?php echo $affiliate['affdomain'];?>')"><?php echo lang('Edit'); ?></a>
												<a href="javascript:void(0)" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-chestnutrose' : 'btn-primary'?>" id="frm_remove_domain" onclick="removeAffdomain('<?php echo $affiliate['affdomain'];?>')"><?php echo lang('Delete'); ?></a></td>
											</td>
										<?php }?>
									</tr>
								</tbody>
							</table>
						</div>
					</div>

					<?php if($this->utils->isEnabledFeature('affiliate_additional_domain')){?>
						<script type="text/javascript">
							function editAdditionalDomain(affTrackingId, affdomain){
								BootstrapDialog.show({
									title: '<?php echo lang('Edit'); ?>',
									message: '<form method="POST" id="frm_edit_add_domain" class="frm_edit_add_domain_'+affTrackingId+'" action="<?php echo site_url("/affiliate_management/change_additional_affdomain/".$affiliateId); ?>/'
										+affTrackingId+'"><?php echo lang("Affiliate Additional Domain"); ?>: <input type="text" name="affdomain" class="form-control" value="'+affdomain+'"></form>',
									spinicon: 'fa fa-spinner fa-spin',
									buttons: [{
										icon: 'fa fa-save',
										label: '<?php echo lang('Save'); ?>',
										cssClass: 'btn-primary',
										autospin: true,
										action: function(dialogRef){
											dialogRef.enableButtons(false);
											dialogRef.setClosable(false);

											var frm=dialogRef.getModalBody().find('.frm_edit_add_domain_'+affTrackingId);
											frm.submit();
										}
									}, {
										label: '<?php echo lang('Cancel'); ?>',
										action: function(dialogRef){
											dialogRef.close();
										}
									}]
								});
							}

							function removeAdditionalDomain(affTrackingId, affdomain){
								BootstrapDialog.show({
									title: '<?php echo lang('Delete'); ?>',
									message: '<form method="POST" class="frm_remove_add_domain_'+affTrackingId+'" action="<?php echo site_url("/affiliate_management/remove_additional_affdomain/".$affiliateId); ?>/'
										+affTrackingId+'"><?php echo lang("Affiliate Additional Domain"); ?>: <input type="text" class="form-control" disabled="disabled" value="'+affdomain+'"></form>',
									spinicon: 'fa fa-spinner fa-spin',
									buttons: [{
										icon: 'fa fa-save',
										label: '<?php echo lang('Delete'); ?>',
										cssClass: 'btn-danger',
										autospin: true,
										action: function(dialogRef){
											dialogRef.enableButtons(false);
											dialogRef.setClosable(false);

											var frm=dialogRef.getModalBody().find('.frm_remove_add_domain_'+affTrackingId);
											frm.submit();
										}
									}, {
										label: '<?php echo lang('Cancel'); ?>',
										action: function(dialogRef){
											dialogRef.close();
										}
									}]
								});
							}

							function newAdditionalDomain(){
								BootstrapDialog.show({
									title: '<?php echo lang('New'); ?>',
									message: '<form method="POST" id="frm_new_add_domain" action="<?php echo site_url("/affiliate_management/new_additional_affdomain/".$affiliateId); ?>"><?php echo lang("New Affiliate Additional Domain"); ?>: <input name="affdomain" id="addAffdomain" type="text" required class="form-control" value=""><input type="submit" class="hidden"/></form>',
									spinicon: 'fa fa-spinner fa-spin',
									buttons: [{
										icon: 'fa fa-save',
										label: '<?php echo lang('Save'); ?>',
										cssClass: 'btn-primary',
										id: 'addAffDomainBtn',
										autospin: true,
										enabled: true,
										action: function(dialogRef) {

											dialogRef.enableButtons(false);
											dialogRef.setClosable(false);

											var submit = dialogRef.getModalBody().find('#frm_new_add_domain input[type="submit"]');
												submit.trigger('click'); // trigger click submit button to show validation message

											var form = dialogRef.getModalBody().find('#frm_new_add_domain');

											if ( ! form.checkValidity()) {
												dialogRef.enableButtons(true);
												dialogRef.setClosable(true);
												this.stopSpin();
											}

										}
									}, {
										label: '<?php echo lang('Cancel'); ?>',
										action: function(dialogRef){
											dialogRef.close();
										}
									}]
								});
							}
						</script>

						<div class="row">
							<div class="col-md-12" style="overflow: auto;">
								<a name="aff_additional_domain_list">
								<a href="javascript:void(0)" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" onclick="newAdditionalDomain()"><?php echo lang('New Affiliate Additional Domain');?></a>
								<table class="table table-striped">
									<thead>
										<th class="col-md-10"><?php echo lang('Affiliate Additional Domain');?></th>
										<th class="col-md-2"><?php echo lang('Action');?></th>
									</thead>
									<tbody>
									<?php foreach($aff_additional_domain_list as $domain){?>
										<tr>
											<td><a href="<?php echo $domain['tracking_domain']; ?>" target='_blank'><?php echo $domain['tracking_domain']; ?></a></td>
											<td><a id="additionalDomainEditButton" href="javascript:void(0)" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>" onclick="editAdditionalDomain(<?php echo $domain['id'];?>, '<?php echo $domain['tracking_domain'];?>')"><?php echo lang('Edit');?></a>
											<a id="additionalDomainRemoveButton" href="javascript:void(0)" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-chestnutrose' : 'btn-primary'?>" onclick="removeAdditionalDomain(<?php echo $domain['id'];?>, '<?php echo $domain['tracking_domain'];?>')"><?php echo lang('Delete');?></a> </td>
										</tr>
									<?php }?>
									</tbody>
								</table>
							</div>
						</div>
					<?php }?>
				<?php }?>

				<div class="row">
					<div class="col-md-12" style="overflow: auto;">
						<table class="table table-striped">
							<thead>
								<th><?=lang('aff.ai95');?></th>
							</thead>

							<tbody>
								<tr>
									<td>
									<?php if (!empty($sublink) && !empty($affiliate['trackingCode']) && $affiliateSettings['sub_link']) {?>
										<a href="<?php echo $sublink; ?>" target="_blank"><?=$sublink;?></a>
									<?php } else {?>
										<?php echo lang('N/A'); ?>
									<?php }?>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div> <!--end panel-body-->
		</div> <!--end panel-->
		<!-- End of Tracking Code -->

		<!-- Monthly Earnings -->
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a href="#personal_info" id="hide_affearn_info" class="btn btn-info btn-sm">
						<i class="glyphicon glyphicon-chevron-up" id="hide_affae_up"></i>
					</a> <?=lang('Affiliate Earnings');?>
				</h4>
			</div>

			<div class="panel-body affae_panel_body">
				<?php if ($this->permissions->checkPermissions('affiliate_earnings')) :?>
					<div class="table-responsive" id="monthlyEarnings">
						<?php if ($this->utils->isEnabledFeature('switch_to_affiliate_platform_earnings')): ?>
							<table class="table table-striped" id="earningsTable" style="width:100%;">
								<thead>
									<th></th>
									<th><?=lang('Date');?></th>
									<th><?=lang('Game Platform');?></th>
									<th><?=lang('Game Platform Fee');?></th>
									<th><?=lang('Gross Revenue');?></th>
									<th><?=lang('Admin Fee');?></th>
									<th><?=lang('Bonus Fee');?></th>
									<th><?=lang('Cashback Fee');?></th>
									<th><?=lang('Transaction Fee');?></th>
									<th><?=lang('Total Fee');?></th>
									<th><?=lang('Net Revenue');?></th>
									<th><?=lang('Commission Rate');?></th>
									<th><?=lang('Commission Amount');?></th>
									<th><?=lang('Status');?></th>
								</thead>

								<tbody>
									<?php foreach ($earnings as $e) { ?>
										<tr>
											<td></td>
											<td><?=date('Y-m-d', strtotime($e['start_date']));?></td>
											<td><?=$this->external_system->getNameById($e['game_platform_id']);?></td>
											<td><?=number_format($e['game_platform_fee'],2);?></td>
											<td><?=number_format($e['game_platform_gross_revenue'],2);?></td>
											<td><?=number_format($e['game_platform_admin_fee'],2);?></td>
											<td><?=number_format($e['game_platform_bonus_fee'],2);?></td>
											<td><?=number_format($e['game_platform_cashback_fee'],2);?></td>
											<td><?=number_format($e['game_platform_transaction_fee'],2);?></td>
											<td><?=number_format($e['game_platform_total_fee'],2);?></td>
											<td><?=number_format($e['game_platform_net_revenue'],2);?></td>
											<td><?=number_format($e['game_platform_commission_rate'],2);?>%</td>
											<td><?=number_format($e['game_platform_commission_amount'],2);?></td>
											<td><?=$e['paid_flag'] == 0 ? lang('Unpaid') : lang('Paid');?></td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
						<?php else: ?>
							<form id="search-form">
								<input type="hidden" name="affiliate_id" value="<?=$affiliate['affiliateId']?>"/>
							</form>
							<table class="table table-striped" id="earningsTable" style="width:100%;">
								<thead>
									<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Year Month')?></th>
									<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Active Players')?></th>
									<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Total Players')?></th>
									<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Gross Revenue')?></th>
									<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Platform Fee')?></th>
									<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Bonus Fee')?></th>
									<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Cashback Fee')?></th>
									<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Transaction Fee')?></th>
									<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Admin Fee')?></th>
									<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Total Fee')?></th>
									<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Net Revenue')?></th>
									<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Commission Rate')?></th>
									<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Commission Amount')?></th>
									<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Commission From Sub-affiliates')?></th>
									<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Total Commission')?></th>
									<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Manual Adjustment')?></th>
									<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Status')?></th>
									<th nowrap="nowrap" style="white-space: nowrap;"><?=lang('Paid By')?></th>
								</thead>
							</table>
							<div class="modal fade in" id="mainModal" tabindex="-1" role="dialog" aria-labelledby="mainModalLabel">
							    <div class="modal-dialog" role="document">
							        <div class="modal-content">
							            <div class="modal-header">
							                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							                <h4 class="modal-title" id="mainModalLabel"></h4>
							            </div>
							            <div class="modal-body"></div>
							        </div>
							    </div>
							</div>
						<?php endif ?>
					</div>
				<?php endif ?>
			</div>
		</div>
		<!-- End of Monthly Earnings -->
	</div>

	<div class="panel-footer"></div>
</div>


<script type="text/javascript">
	function modal(load, title) {
	    var target = $('#mainModal .modal-body');
	    $('#mainModalLabel').html(title);
	    target.html('<center><img src="' + imgloader + '"></center>').delay(1000).load(load);
	    $('#mainModal').modal('show');
	    return false;
	}

	$(document).ready(function() {
		<?php if ($this->utils->isEnabledFeature('switch_to_affiliate_platform_earnings')): ?>
			$('#earningsTable').DataTable( {
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
			} );
		<?php else: ?>
			$('#earningsTable').DataTable( {
		        searching: false,
		        processing: true,
		        serverSide: true,
		        dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
		        ajax: function (data, callback, settings) {
		            data.extra_search = $('#search-form').serializeArray();
		            $.post('/api/aff_earnings', data, function(data) {
		                callback(data);
		            }, 'json');
		        },
		        columnDefs: [
		            { className: 'text-right', targets: [ 2,3,4,5,6,7,8,9,10,11,12,13,14,15,16 ] },
		        ],
		        buttons: [
		            {
		                extend: 'colvis',
						postfixButtons: [ 'colvisRestore' ],
						className: "<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>"
		            }
		        ],
		    } );
		<?php endif ?>

		$('.toggle-sub-levels').change( function() {
			$('.sub-levels').prop('disabled', $('.toggle-sub-levels:checked').length == 0);
		});

		<?php
		/// Patch for OGP-12828 Affiliate "Operator Settings" can't save the set %
		?>
		/// jstree removed.
		// $('#gameTree').jstree({
		//   	'core' : {
		// 		'data' : {
		// 	  	"url" : "<?php echo site_url('/api/get_game_tree_by_pub_affiliate_setting/' . $affiliateId); ?>",
		// 	  	"dataType" : "json" // needed only if you do not supply JSON headers
		// 		}
		//   	},
		//   	"input_number":{
		//     	"form_sel": '#form_operator'
		//   	},
		//   	"checkbox":{
		// 		"tie_selection": false,
		//   	},
		//   	"plugins":[
		// 		"search","checkbox","input_number"
		//   	]
		// });

		$('#form_operator').submit(function(e){
			/// jstree removed.
			// var selected_game=$('#gameTree').jstree('get_checked');
			// 	$('#form_operator input[name=selected_game_tree]').val(selected_game.join());
			//     $('#gameTree').jstree('generate_number_fields');
			return true;
		});

		// Filters
		$('#bankTable').DataTable( {
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
		} );

		$('#paymentTable').DataTable( {
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
			"order": [ 1, 'desc' ]
		} );

		$('.btn_deposit').click(function(){
			window.location.href='<?php echo site_url("/affiliate_management/affiliate_deposit/" . $affiliateId); ?>';
		});

		$('.btn_withdraw').click(function(){
			window.location.href='<?php echo site_url("/affiliate_management/affiliate_withdraw/" . $affiliateId."/main"); ?>';
		});

		$('.btn_transfer_bal_to_main').click(function(){
			BootstrapDialog.confirm('<?php echo lang('Do you want transfer all balance to main wallet?'); ?>', function(result){
				if(result) {
					window.location.href='<?php echo site_url("/affiliate_management/affiliate_transfer_bal_to_main/" . $affiliateId); ?>';
				}
			});
		});

		$('.btn_withdraw_bal').click(function(){
			BootstrapDialog.confirm('<?php echo lang('Do you want withdraw locked wallet'); ?>?', function(result){
				if(result) {
					window.location.href='<?php echo site_url("/affiliate_management/affiliate_withdraw/" . $affiliateId."/balance"); ?>';
				}
			});
		});

		$('.btn_transfer_bal_from_main').click(function(){
			BootstrapDialog.confirm('<?php echo lang('Do you want transfer all main wallet to balance wallet?'); ?>', function(result){
				if(result) {
					window.location.href='<?php echo site_url("/affiliate_management/affiliate_transfer_bal_from_main/" . $affiliateId); ?>';
				}
			});
		});

		<?php if ($this->permissions->checkPermissions('affiliate_admin_action') && $this->utils->getConfig('enabled_show_password')) {?>
			$('#_password_label').dblclick(function(){
				alert("<?php echo $hide_password; ?>");
			});
		<?php }?>
	});


	// prevent negative value
	$('input[type="number"]').on('change', function(){
		if($(this).val() < 0)
			$(this).val(0);
	});

	$('.number_only').keyup(function () {
		this.value = this.value.replace(/[^0-9\.]/g,'');
	});

	var tracking_code = "<?php echo $affiliate['trackingCode']; ?>";
	if(tracking_code != ""){
		$('#random_code_sec').hide();
		$('#random_code_lock').show();
		$('#tracking_code').attr("disabled",true);
	} else {
		$('#random_code_sec').show();
		$('#random_code_lock').hide();
		$('#tracking_code').attr("disabled",false);
	}

	function unlock_tracking_code(){
		$('#random_code_sec').show();
		$('#random_code_lock').hide();
		$('#tracking_code').attr("disabled",false);

		$.ajax({
			'url' : base_url +'affiliate_management/log_unlock_trackingcode',
			'type' : 'GET',
			'success' : function(data) {
				// console.log("success");
			}
		},'json');
	}

	$('#transaction_fee').change(function(){
	    transactionFee();
	});

	function transactionFee(){
	    if( $('#transaction_fee').is(':checked') ){
	        $('#split_transaction_fee').bootstrapToggle('enable');
	    }else{
	        $('#split_transaction_fee').bootstrapToggle('off').bootstrapToggle('disable');
	    }
	}

	$(function(){
	    $('#split_transaction_fee').bootstrapToggle('disable');
	    transactionFee();
	    splitTransaction();

	    var defBx = '#default_shares_checkbox';
	    $(defBx).bootstrapToggle('disable');
	    if(!$(defBx).is(':checked')){
	        $('#default_shares_sets').prop('disabled', true);
	    }
	});

	$('#split_transaction_fee').change(function(){
	    splitTransaction();
	});

	function splitTransaction(){
	    if( $('#split_transaction_fee').is(':checked') ){
	        $('#inp-transaction-fee').prop('disabled', true);
	        $('#inp-deposit-fee').prop('disabled', false);
	        $('#inp-withdrawal-fee').prop('disabled', false);
	    }else{
	        $('#inp-transaction-fee').prop('disabled', false);
	        $('#inp-deposit-fee').prop('disabled', true);
	        $('#inp-withdrawal-fee').prop('disabled', true);
	    }
	}
</script>

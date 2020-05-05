<div class="row deposit-wrapper">
	<div class="deposit-list">
		<div class="list-group">
			<?php $this->load->view('iframe/cashier/deposit_sidebar'); ?>
		</div>
	</div>
	<div class="deposit-content">
		<?php if ($exists_payment_account) { ?>
		<form class="form-horizontal" id="form-deposit" action="<?=site_url('/iframe_module/manual_payment/' . $flag . '/' . $bankTypeId)?>" method="POST" enctype="multipart/form-data" autocomplete="off">
			<div class="panel panel-default">
				<div class="panel-body">
					<input type="hidden" name="payment_account_id" value="<?=$payment_account_id?>"/>
					<div class="form-group">
						<label class="control-label col-xs-4"><?=lang('cashier.52')?></label>
						<div class="col-xs-6">
							<p class="form-control-static"><?=lang($payment_type)?></p>
						</div>
					</div>
					<?php
					if (!$payment_account_hide_bank_info) {
						$auto_open_payment_account = $this->config->item('auto_open_payment_account');
					?>
					<!--<div class="form-group">
						<label class="control-label col-xs-4"><?=lang('cashier.deposit.toggle_mybank_info');?></label>
							<div class="col-xs-6">
								<input type="checkbox" id='my_account_checkbox' name="my_account" value="1" <?php echo "checked"; ?> >
							</div>
						</div>
 					-->
					<div class="bank_zone" style="<?php echo $auto_open_payment_account ? "" : "display:none"; ?>">
						<div class="form-group">
							<label class="control-label col-xs-4"><input type="radio" id="preferredAccount_rb" name="itemAccount" checked value="old" > <?=lang('cashier.select_preferred_account');?></label>
							<div class="col-xs-6">
								<select name="pa_bankName" id="preferredBank" class="form-control input-sm" value="<?=$this->session->userdata('lb_pa_bankName') == '' ? '' : $this->session->userdata('lb_pa_bankName');?>">
									<option value="">-- <?=lang('cashier.74');?> --</option>
									<?php foreach ($playerBankDetails as $row) {?>
										<option value="<?=$row['playerBankDetailsId'];?>"> <?php echo lang($row['bankName']) . ' - ' . $row['bankAccountFullName'] ?></option>
									<?php } ?>
								</select>
								<?php echo form_error('pa_bankName', '<span class="help-block text-danger">', '</span>') ?>
							</div>
						</div>

						<div class="form-group">
							<label class="control-label col-xs-4"><input type="radio" id="newAccount_rb" name="itemAccount" value="new"> <?=lang('cashier.select_new_account');?></label>
						</div>
						<div class="form-group">
							<label class="control-label col-xs-4"><?=lang('cashier.account_type');?></label>
							<div class="col-xs-6">
							<?php
							if (!$payment_account_hide_bank_type) { 
							?>
								<select name="na_bankName" id="bankName" class="form-control input-sm" required>
									<option value="">-- <?=lang('cashier.74');?> --</option>
									<?php //var_dump($playerBankDetails[0]);
									foreach ($banks as $row) {
									?>
										<?php if ($row['enabled_withdrawal']): ?>
										<option value="<?=$row['bankTypeId']?>"><?=lang($row['bankName'])?></option>
										<?php endif?>
									<?php
									}
									?>
								</select>
								<?php echo form_error('na_bankName', '<span class="help-block text-danger">', '</span>') ?>
							<?php
							} else {
							?>
								<?php echo lang($payment_type); ?>
								<input type="hidden" name="na_bankName" value="<?php echo $paymentAccount->payment_type_id; ?>">
							<?php
							}
							?>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-xs-4"><?=lang('cashier.account_name');?></label>
							<div class="col-xs-6">
							<input type="text" name="fullName" id="fullName" class="form-control input-sm" size="50" value="<?=set_value('depositName')?>" required />
								<?php echo form_error('fullName', '<span class="help-block text-danger">', '</span>') ?>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-xs-4"><?=lang('cashier.account_number');?></label>
							<div class="col-xs-6">
								<input type="number" name="depositAccountNo" id="depositAccountNo" class="form-control input-sm" size="50" required />
								<?php echo form_error('depositAccountNo', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
								<span id="error-depositAccountNo" class="help-block" style="color:#ff6666;font-size:11px;"></span>
							</div>
						</div>
					</div>
					<?php
					}
					?>
					<div class="form-group">
							<label class="control-label col-xs-4 required"><?=lang('cashier.53')?><br>
								(<?=lang('player.mindep')?>: <?=number_format($minDeposit, 2)?>)<br>
								(<?=lang('pay.maxdepamt')?>: <?=number_format($maxDeposit, 2)?>)
							</label>
							<div class="col-xs-6">
								<input type="text" min='<?php echo $minDeposit ?>' max='<?php echo $maxDeposit ?>' name="depositAmount" id="depositAmount" class="form-control amount_only input-sm" size="40" required value="<?=set_value('depositAmount', 0) > 0 ? number_format(set_value('depositAmount', 0), 2) : ''?>" />
								<div class="help-block" id="depositAmount-help-block"></div>
								<?php echo form_error('depositAmount', '<span class="help-block text-danger">', '</span>') ?>
							</div>
						</div>
						<hr class="style-one"/>
						<?php if (isset($subwallets)) { ?>
						<div class="form-group">
							<label for="sub_wallet_id" class="control-label col-xs-4"><?=lang('player.ut08')?></label>
							<div class="col-xs-4">
								<select name="sub_wallet_id" id="sub_wallet_id" class="form-control input-sm">
									<option value=""><?=lang('select.empty.line')?></option>
									<?php foreach ($subwallets as $walletId => $walletName) {?>
										<option value="<?=$walletId?>" <?=set_select('subwallet', $walletId)?>><?=$walletName?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<?php } ?>
						<?php if (isset($vipsettings)) { ?>
						<div class="form-group">
							<label for="group_level_id" class="control-label col-xs-4"><?=lang('player.groupLevel')?></label>
							<div class="col-xs-4">
								<select class="form-control input-sm" name="group_level_id" id="group_level_id">
									<option value=""><?=lang('select.empty.line')?></option>
									<?php foreach ($vipsettings as $vipsetting) { ?>
										<optgroup label="<?=$vipsetting['name']?>">
											<?php foreach ($vipsetting['list'] as $vipsetting) {?>
												<option value="<?=$vipsetting['vipsettingcashbackruleId']?>"><?=$vipsetting['vipLevelName']?></option>
											<?php } ?>
										</optgroup>
									<?php } ?>
								</select>
							</div>
						</div>
						<?php } ?>

						<?php if(isset($avail_promocms_list) && !empty($avail_promocms_list)){?>
						<div class="form-group">
							<label for="player_promo_id" class="control-label col-xs-4"><?=lang('Promotion')?></label>
							<div class="col-xs-4">
								<?php echo form_dropdown('promo_cms_id', $avail_promocms_list, null, 'class="avail_promocms_list form-control input-sm" '); ?>
							</div>
						</div>

						<?php }?>

						<?php if ($this->utils->isEnabledFeature('enable_manual_deposit_detail')) : ?>
							<div class="form-group">
								<label for="depositTime" class="control-label col-xs-4"><?=lang('Deposit Time')?></label>
								<div class="col-xs-6">
									<input type="text" id="depositTime" name="deposit_datetime" class="form-control dateInput" data-time="true" />
								</div>
							</div>
							<div class="form-group">
								<label for="depositMethod" class="control-label col-xs-4"><?=lang('Deposit Method')?></label>
								<div class="col-xs-6">
									<select class="frm-select pull-left form-control" name="deposit_method" id="depositMethod">
										<?php foreach(Sale_order::PLAYER_DEPOSIT_METHODS as $depositMethod => $depositMethodValue) : ?>
											<option value="<?=$depositMethodValue?>"><?=lang($depositMethod)?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>
						<?php endif; ?>

						<hr class="style-one"/>

						<div class="row">
							<div class="col-xs-offset-4 col-xs-8">
								<button type="submit" class="btn btn-primary" id="btnSubmitDeposit"><?=lang('cashier.60')?></button>
								<a href="<?=site_url('iframe_module/iframe_viewCashier')?>" class="btn-cancel btn btn-danger"><?=lang('cashier.61');?></a>
							</div>
						</div>
				</div>
			</div>
		</form>
	<?php } else {?>
		<p><?php echo lang('online_payment_is_not_available'); ?></p>
	<?php } ?>
	</div>
</div>

<script>
	var n = nod();
	$(document).ready(function() {
		n.configure(
			{
				form: '#form-deposit',
				preventSubmit: true,
				parentClass: 'form-group',
				errorClass: 'has-error',
				errorMessageClass: 'text-danger',
				delay: 100
			}
		);
		n.add([
			{
				selector: '#depositAmount',
				triggerEvents: 'blur',
				validate: [
					<?=$minDeposit ? "'min-number:{$minDeposit}'," : ''?>
					<?=$maxDeposit ? "'max-number:{$maxDeposit}'," : ''?>
				],
				errorMessage: [
					<?=$minDeposit ? "'" . sprintf(lang("gen.error.min.amount"), lang("cashier.53"), number_format($minDeposit, 2)) . "'," : ''?>
					<?=$maxDeposit ? "'" . sprintf(lang("gen.error.max.amount"), lang("cashier.53"), number_format($maxDeposit, 2)) . "'," : ''?>
				]
			}
		]);
		n.setMessageOptions([
			{
				selector: '#depositAmount',
				errorSpan: '#depositAmount-help-block'
			}
		]);
		/*####################Account Number Checking start ###################################*/
		var CURRENT_ACCT_TYPE_USE = 'current-account',
			VALIDATION_URL = '<?php echo site_url('iframe_module/validateThruAjax') ?>',
			error = [],
			submit = $('#btnSubmitDeposit'),
			BANK_ACCT_NO_LABEL ='<?=lang("cashier.69")?>',
			bankAcctNo = $('#depositAccountNo');

		$('#preferredAccount_rb').change(function(){
			if($(this).is(':checked')){
				CURRENT_ACCT_TYPE_USE = 'bank-account';
			}
		});

		$('#newAccount_rb').change(function(){
			if($(this).is(':checked')){
				CURRENT_ACCT_TYPE_USE = 'new-account';
			}
		});

		bankAcctNo.blur(function(){
			if(requiredCheck($(this).val(),'depositAccountNo',BANK_ACCT_NO_LABEL)){
				  validateThruAjax($(this).val(),'depositAccountNo',BANK_ACCT_NO_LABEL)
			}
		});

		submit.click(function(){
			if(CURRENT_ACCT_TYPE_USE === 'new-account'){
				 if(bankAcctNo.val() ){
					var errorLength = error.length;
					if(errorLength > 0){
						return false;
					}
				}
			}
		});

		function validateThruAjax(fieldVal,id,label){
			var data;
			data = {bank_account_number:fieldVal};
			$.ajax({
				url : VALIDATION_URL,
				type : 'POST',
				data : data,
				dataType : "json",
				cache : false
			}).done(function (data) {
				if (data.status == "success") {
					removeErrorItem(id);
					removeErrorOnField(id);
			   	}
				if (data.status == "error") {
					var message = data.msg;
					showErrorOnField(id,message);
					addErrorItem(id);
				}
			}).fail(function (jqXHR, textStatus) {
				/*Note: this is for session timeout,if the session is out because this is ajax, eventually it will go to log in page*/
				if(jqXHR.status>=300 && jqXHR.status<500){
					location.reload();
				}else{
					alert(textStatus);
				}
			});
		}

		function requiredCheck(fieldVal,id,label){
			var message = label+" is required";
			if(!fieldVal && (fieldVal == "")){
				showErrorOnField(id,message)
				addErrorItem(id);
				return false;
			}else{
				removeErrorOnField(id);
				removeErrorItem(id);
				return true;
			}
		}

		function showErrorOnField(id,message){
			$('#error-'+id).html(message);
		}

		function removeErrorOnField(id){
			$('#error-'+id).html("");
		}

		function removeErrorItem(item){
			var i = error.indexOf(item);
			if(i != -1) {
				error.splice(i, 1);
			}
		 }

		function addErrorItem(item){
			if(jQuery.inArray(item, error) == -1){
				error.push(item);
			}
		}

		function disableSubmitButton(){
			submit.prop('disabled', true);
		}

		function ableSubmitButton(){
			submit.prop('disabled', false);
		}
		/*####################Account Number Checking end ###################################*/
	});//end document
</script>
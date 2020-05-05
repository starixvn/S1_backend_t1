<div class="row">
	<div class="col-md-12" id="toggleView">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title">
					<i class="icon-info"></i> <strong><?= lang('aff.ai22'); ?></strong>
					<a href="<?= BASEURL . 'affiliate_management/userInformation/' . $payment['affiliateId'] ?>" class="btn btn-default btn-sm pull-right" id="view_affiliate"><span class="glyphicon glyphicon-remove"></span></a>
				</h4>
			</div>

			<div class="panel-body" id="affiliate_info">
				<form action="<?= BASEURL . 'affiliate_management/verifyEditPayment/' . $payment['affiliatePaymentId'] ?>" method="POST">
					<input type="hidden" name="affiliate_payment_id" value="<?= $payment['affiliatePaymentId']; ?>">
					<input type="hidden" name="affiliate_id" id="affiliate_id" class="form-control" value="<?= $payment['affiliateId'] ?>">

					<div class="row">
						<div class="col-md-6 col-md-offset-0">
							<label for="bank_name"><?= lang('pay.bankname'); ?>: </label>
						</div>

						<div class="col-md-6 col-md-offset-0">
							<label for="bank_name"><?= lang('aff.ai90'); ?>: </label>
						</div>

						<div class="col-md-5 col-md-offset-0">
							<input type="hidden" name="bankName" value="<?= lang($payment['bankName']) ?>"/>
							<input type="text" name="bank_name" id="bank_name" class="form-control" value="<?= (set_value('bank_name') == null) ? lang($payment['bankName']):set_value('bank_name'); ?>">
							<label style="color: red; font-size: 12px;"><?php echo form_error('bank_name'); ?></label>
						</div>

						<div class="col-md-5 col-md-offset-1">
							<input type="hidden" name="accountName" value="<?= $payment['accountName'] ?>"/>
							<input type="text" name="account_name" id="account_name" class="form-control" value="<?= (set_value('account_name') == null) ? $payment['accountName']:set_value('account_name'); ?>">
							<label style="color: red; font-size: 12px;"><?php echo form_error('account_name'); ?></label>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6 col-md-offset-0">
							<label for="account_info"><?= lang('aff.ai24'); ?>: </label>
						</div>

						<div class="col-md-6 col-md-offset-0">
							<label for="account_number"><?= lang('aff.ai25'); ?>: </label>
						</div>

						<div class="col-md-5 col-md-offset-0">
							<input type="text" name="account_info" id="account_info" class="form-control" value="<?= (set_value('account_info') == null) ? $payment['accountInfo']:set_value('account_info'); ?>">
							<label style="color: red; font-size: 12px;"><?php echo form_error('account_info'); ?></label>
						</div>

						<div class="col-md-5 col-md-offset-1">
							<input type="text" name="account_number" id="account_number" class="form-control" value="<?= (set_value('account_number') == null) ? $payment['accountNumber']:set_value('account_number'); ?>">
							<label style="color: red; font-size: 12px;"><?php echo form_error('account_number'); ?></label>
						</div>
					</div>

					<br/>

					<div class="row">
						<center>
							<input type="submit" class="btn btn-info btn-sm" value="<?= lang('lang.save'); ?>"/>
							<a href="<?= BASEURL . 'affiliate_management/userInformation/' . $payment['affiliateId'] ?>" class="btn btn-default btn-sm" id="view_affiliate"><?= lang('lang.cancel'); ?></a>
						</center>
					</div>
				</form>
				<!-- End of Personal Info -->
			</div>
		</div>
	</div>
</div>
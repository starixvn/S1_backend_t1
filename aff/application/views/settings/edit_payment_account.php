<div class="container">
	<br/><br/>

	<!-- Payment Information -->
	<div class="row">
		<div class="panel panel-primary">
			<div class="nav-head panel-heading">
				<h4 class="panel-title pull-left"><i class="glyphicon glyphicon-cog"></i> <?= lang('nav.modifyAccount'); ?> </h4>
				<a href="<?= BASEURL . 'affiliate/modifyAccount'?>" class="btn btn-info btn-xs pull-right"><span class="glyphicon glyphicon-remove "></span></a>
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body" id="info_panel_body">
				<form action="<?= BASEURL . 'affiliate/verifyEditPayment/' . $payment['affiliatePaymentId'] ?>" method="POST">
					<input type="hidden" name="affiliate_payment_id" value="<?= $payment['affiliatePaymentId']; ?>">
                    <input type="hidden" name="edit_count" value="<?= $payment['editCount']; ?>">

					<div class="row">
						<div class="col-md-6 col-md-offset-0">
							<label for="bank_name"><?= lang('pay.bankname'); ?>: </label>
						</div>

						<div class="col-md-6 col-md-offset-0">
							<label for="bank_name"><?= lang('pay.accname'); ?>: </label>
						</div>

						<div class="col-md-5 col-md-offset-0">
							<input type="hidden" name="bankName" value="<?= $payment['bankName'] ?>"/>
							<input type="text" name="bank_name" id="bank_name" class="form-control" value="<?= (set_value('bank_name') == null) ? $payment['bankName']:set_value('bank_name'); ?>">
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
							<label for="account_info"><?= lang('pay.accinfo'); ?>: </label>
						</div>

						<div class="col-md-6 col-md-offset-0">
							<label for="account_number"><?= lang('pay.accnum'); ?>: </label>
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

					<div class="row">
						<div class="col-md-2 col-md-offset-5">
							<input type="submit" name="submit" id="submit" class="submit btn btn-primary" value="<?= lang('lang.save'); ?>">
							<a href="<?= BASEURL . 'affiliate/modifyAccount' ?>" class="cancel btn btn-primary"><?= lang('lang.cancel'); ?></a>
						</div>
					</div>
				</form>
			</div>

			<div class="panel-footer">

			</div>
		</div>
	</div>
	<!-- End of Payment Information -->
</div>
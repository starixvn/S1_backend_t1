<div class="row">
	<div class="col-md-12" id="toggleView">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title">
					<i class="icon-info"></i> <strong><?= lang('aff.ai22'); ?></strong>
					<a href="<?= BASEURL . $controller_name . '/agent_information/' . $payment['agent_id'] ?>" class="btn btn-default btn-sm pull-right" id="view_agent"><span class="glyphicon glyphicon-remove"></span></a>
				</h4>
			</div>

			<div class="panel-body" id="agent_info">
				<form action="<?= BASEURL . $controller_name . '/verifyEditPayment/' . $payment['agent_payment_id'] ?>" method="POST">
					<input type="hidden" name="agent_payment_id" value="<?= $payment['agent_payment_id']; ?>">
					<input type="hidden" name="agent_id" id="agent_id" class="form-control" value="<?= $payment['agent_id'] ?>">

					<div class="row">
						<div class="col-md-6 col-md-offset-0">
							<label for="bank_name"><?= lang('pay.bankname'); ?>: </label>
						</div>

						<div class="col-md-6 col-md-offset-0">
							<label for="account_name"><?= lang('aff.ai90'); ?>: </label>
						</div>

						<div class="col-md-5 col-md-offset-0">
							<input type="hidden" name="bank_name" value="<?= $payment['bank_name'] ?>"/>
							<input type="text" name="bank_name" id="bank_name" class="form-control" value="<?= (set_value('bank_name') == null) ? $payment['bank_name']:set_value('bank_name'); ?>">
							<label style="color: red; font-size: 12px;"><?php echo form_error('bank_name'); ?></label>
						</div>

						<div class="col-md-5 col-md-offset-1">
							<input type="hidden" name="account_name" value="<?= $payment['account_name'] ?>"/>
							<input type="text" name="account_name" id="account_name" class="form-control" value="<?= (set_value('account_name') == null) ? $payment['account_name']:set_value('account_name'); ?>">
							<label style="color: red; font-size: 12px;"><?php echo form_error('account_name'); ?></label>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6 col-md-offset-0">
							<label for="branch_address"><?= lang('aff.ai24'); ?>: </label>
						</div>

						<div class="col-md-6 col-md-offset-0">
							<label for="account_number"><?= lang('pay.acctnumber'); ?>: </label>
						</div>

						<div class="col-md-5 col-md-offset-0">
							<input type="text" name="branch_address" id="branch_address" class="form-control" value="<?= (set_value('branch_address') == null) ? $payment['branch_address']:set_value('branch_address'); ?>">
							<label style="color: red; font-size: 12px;"><?php echo form_error('branch_address'); ?></label>
						</div>

						<div class="col-md-5 col-md-offset-1">
							<input type="text" name="account_number" id="account_number" class="form-control" value="<?= (set_value('account_number') == null) ? $payment['account_number']:set_value('account_number'); ?>">
							<label style="color: red; font-size: 12px;"><?php echo form_error('account_number'); ?></label>
						</div>
					</div>

					<br/>

					<div class="row">
						<center>
							<input type="submit" class="btn btn-info btn-sm" value="<?= lang('lang.save'); ?>"/>
							<a href="<?= BASEURL . $controller_name . '/agent_information/' . $payment['agent_id'] ?>" class="btn btn-default btn-sm"><?= lang('lang.cancel'); ?></a>
						</center>
					</div>
				</form>
				<!-- End of Personal Info -->
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12" id="toggleView">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title pull-left"><i class="glyphicon glyphicon-list-alt"></i> Add Affiliate </h4>
				<a href="<?= BASEURL . 'affiliate_management/viewAffiliates'?>" class="btn btn-primary btn-sm pull-right" id="view_affiliate"><span class="glyphicon glyphicon-remove"></span></a>
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body" id="affiliate_panel_body">
				<ol class="breadcrumb">
					<!-- <li class="active">Step 1: Sign Up Information</li> -->
					<li class="active"><a href="<?= BASEURL . 'affiliate_management/stepTwo/back' ?>">Step 2: Personal Information</a></li>
					<li class="active"><b>Step 3: Payment Information</b></li>
					<li class="active">Step 4: Terms Options</li>
					<li class="active">Step 5: Affiliate Game Platform</li>
					<!-- <li class="active">Step 6: Affiliate Payout Options</li>
					<li class="active">Step 7: Account Information</li>
					<li class="active">Step 8: Finish</li> -->
				</ol>
				
				<!-- Content Info -->
				<div class="panel panel-info">
					<div class="panel panel-heading">
						<h4 class="panel-title pull-left"> Payment Information </h4>
						<div class="clearfix"></div>
					</div>

					<div class="panel-body">
						<form method="POST" action="<?= BASEURL . 'affiliate_management/stepFour'?>" accept-charset="utf-8">
							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="payment_method">Payment Method: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<?php $payment_method = ($this->session->userdata('aff_payment_method') != null) ? $this->session->userdata('aff_payment_method') : set_value('payment_method') ?>
									<select name="payment_method" id="payment_method" class="form-control">
										<option value="">Click to Select</option>
										<option value="Wire Transfer" <?= ($payment_method == "Wire Transfer") ? "selected":"" ?> >Wire Transfer</option>
									</select>
									<label style="color: red; font-size: 12px;"><?php echo form_error('payment_method'); ?></label>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="bank_name">Bank Name: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="bank_name" id="bank_name" class="form-control" value="<?= ($this->session->userdata('aff_bank_name') != null) ? $this->session->userdata('aff_bank_name'):set_value('bank_name') ?>">
									<label style="color: red; font-size: 12px;"><?php echo form_error('bank_name'); ?></label>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="account_name">Account Name: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="account_name" id="account_name" class="form-control" value="<?= ($this->session->userdata('aff_account_name') != null) ? $this->session->userdata('aff_account_name'):set_value('account_name') ?>">
									<label style="color: red; font-size: 12px;"><?php echo form_error('account_name'); ?></label>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="account_number"> Account Number: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="account_number" id="account_number" class="form-control" value="<?= ($this->session->userdata('aff_account_number') != null) ? $this->session->userdata('aff_account_number'):set_value('account_number')?>">
									<label style="color: red; font-size: 12px;"><?php echo form_error('account_number'); ?></label>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-5">
									<input type="submit" name="submit" id="submit" class="form-control btn btn-primary" value="Next">
								</div>
							</div>		
						</form>			
					</div>
				</div>
				<!-- End of Content Info -->
			</div>

			<div class="panel-footer">

			</div>
		</div>
	</div>
</div>
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
					<!-- <li class="active">Step 1: Sign Up Information</li>
					<li class="active">Step 2: Personal Information</li>
					<li class="active">Step 3: Payment Information</li> -->
					<li class="active"><a href="<?= BASEURL . 'affiliate_management/stepThree/back'?>">Step 3: Affiliate Game Options</a></li>
					<!-- <li class="active"><a href="<?= BASEURL . 'affiliate_management/stepFour/back'?>">Step 4: Affiliate Game Platform</a></li> -->
					<li class="active"><b>Step 4: Affiliate Payout Options</b></li>
					<li class="active">Step 5: Account Information</li>
					<li class="active">Step 6: Finish</li>
				</ol>
				
				<!-- Content Info -->
				<div class="panel panel-info">
					<div class="panel panel-heading">
						<h4 class="panel-title pull-left"> Affiliate Payout Options </h4>
						<div class="clearfix"></div>
					</div>

					<div class="panel-body">
						<form method="POST" action="<?= BASEURL . 'affiliate_management/stepFive'?>" accept-charset="utf-8">
							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="minimum">Minimum: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="minimum" id="minimum" class="form-control" value="<?= ($this->session->userdata('aff_minimum') != null) ? $this->session->userdata('aff_minimum') : set_value('minimum') ?>">
									<label style="color: red; font-size: 12px;"><?php echo form_error('minimum'); ?></label>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="maximum">Maximum: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="maximum" id="maximum" class="form-control" value="<?= ($this->session->userdata('aff_maximum') != null) ? $this->session->userdata('aff_maximum') : set_value('maximum') ?>">
									<label style="color: red; font-size: 12px;"><?php echo form_error('maximum'); ?></label>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="payment_options">Payment Option: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<?php $payment_options = ($this->session->userdata('aff_payment_options') != null) ? $this->session->userdata('aff_payment_options') : set_value('payment_options') ?>
									<!-- <select name="payment_options" id="payment_options" class="form-control">
										<option value="" >Click to Select</option>
										<option value="Daily" <?= ($payment_options == 'Daily') ? 'selected':'' ?> >Daily</option>
										<option value="Weekly" <?= ($payment_options == 'Weekly') ? 'selected':'' ?> >Weekly</option>
										<option value="Monthly" <?= ($payment_options == 'Monthly') ? 'selected':'' ?> >Monthly</option>
										<option value="Yearly" <?= ($payment_options == 'Yearly') ? 'selected':'' ?> >Yearly</option>
									</select> -->
									<input type="text" name="payment_options" id="payment_options" class="form-control" value="Monthly" readonly>
									<label style="color: red; font-size: 12px;"><?php echo form_error('payment_options'); ?></label>
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
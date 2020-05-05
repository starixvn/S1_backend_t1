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
					<li class="active"><b>Step 1: Sign Up Information</b></li>
					<li class="active">Step 2: Personal Information</li>
					<!-- <li class="active">Step 3: Payment Information</li> -->
					<li class="active">Step 3: Affiliate Game Options</li>
					<!-- <li class="active">Step 5: Affiliate Game Platform</li> -->
					<li class="active">Step 4: Affiliate Payout Options</li>
					<!-- <li class="active">Step 7: Account Information</li>
					<li class="active">Step 8: Finish</li> -->
				</ol>
				
				<!-- Content Info -->
				<div class="panel panel-info">
					<div class="panel panel-heading">
						<h4 class="panel-title pull-left"> Sign Up Information </h4>
						<div class="clearfix"></div>
					</div>

					<div class="panel-body">
						<form method="POST" action="<?= BASEURL . 'affiliate_management/stepTwo'?>" accept-charset="utf-8">
							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="username">Username: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="username" id="username" class="form-control" value="<?= ($this->session->userdata('aff_username') != null) ? $this->session->userdata('aff_username') : set_value('username') ?>" data-toggle="tooltip" title="Your username must contain 5 to 12 letters and/or numbers. No spaces are allowed.">
									<label style="color: red; font-size: 12px;"><?php echo form_error('username'); ?></label>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="password">Password: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="password" name="password" id="password" class="form-control" data-toggle="tooltip" title="Your password must contain 6 to 12 letters, numbers and/or characters. No spaces are allowed.">
									<label style="color: red; font-size: 12px;"><?php echo form_error('password'); ?></label>
								</div>

								<div class="col-md-2 col-md-offset-0">
									<label for="confirm_password">Confirm Password: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="password" name="confirm_password" id="confirm_password" class="form-control" data-toggle="tooltip" title="Confirm the password you enter.">
									<label style="color: red; font-size: 12px;"><?php echo form_error('confirm_password'); ?></label>
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
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
					<li class="active">Step 3: Payment Information</li>
					<li class="active">Step 4: Terms Options</li>
					<li class="active">>Step 5: Affiliate Game Platform</li> -->
					<li class="active"><a href="<?= BASEURL . 'affiliate_management/stepFour/back'?>">Step 4: Affiliate Payout Options</a></li>
					<li class="active"><b>Step 5: Account Information</b></li>
					<li class="active">Step 6: Finish</li>
				</ol>
				
				<!-- Content Info -->
				<div class="panel panel-info">
					<div class="panel panel-heading">
						<h4 class="panel-title pull-left"> Account Information </h4>
						<div class="clearfix"></div>
					</div>

					<div class="panel-body">
						<?php
							$account = explode(',', $this->session->userdata('aff_account_info'));
						?>
						<form method="POST" action="<?= BASEURL . 'affiliate_management/stepSix'?>" accept-charset="utf-8">
							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="frozen">Frozen: </label> &nbsp;&nbsp;
									<input type="checkbox" name="account[]" value="Frozen"
										<?php
											foreach ($account as $value) {
												if($value == "Frozen") {
													echo "checked";
													break;
												}
											}
										?>
									>
								</div>

								<!-- <div class="col-md-3 col-md-offset-0">
									<label for="internal_account">Internal Account: </label> &nbsp;&nbsp;
									<input type="checkbox" name="account[]" value="Internal Account"
										<?php
											foreach ($account as $value) {
												if($value == "Internal Account") {
													echo "checked";
													break;
												}
											}
										?>
									>
								</div> -->
							</div>

							<br/>

							<div class="row">
								<div class="col-md-6 col-md-offset-0">
									<label style="color: red; font-size: 12px;"><?php echo form_error('account'); ?></label>
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
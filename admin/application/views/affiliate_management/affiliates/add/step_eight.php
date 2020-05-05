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
					<li class="active">>Step 5: Affiliate Game Platform</li>
					<li class="active">Step 6: Affiliate Payout Options</li> -->
					<li class="active"><a href="<?= BASEURL . 'affiliate_management/stepFive/back'?>">Step 5: Account Information</a></li>
					<li class="active"><b>Step 6: Finish</b></li>
				</ol>

				<form method="POST" action="<?= BASEURL . 'affiliate_management/verifyAddAffiliate'?>" accept-charset="utf-8">
					<!-- Sign up Info -->
					<div class="panel panel-info">
						<div class="panel panel-heading">
							<h4 class="panel-title pull-left"><a href="<?= BASEURL . 'affiliate_management/addAffiliate'?>">Sign Up Information</a></h4>
							<div class="clearfix"></div>
						</div>

						<div class="panel-body">
							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="username">Username: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="username" id="username" class="form-control" value="<?= $this->session->userdata('aff_username'); ?>" readonly>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="password">Password: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="password" id="password" class="form-control" value="<?= $this->session->userdata('aff_password'); ?>" readonly>
								</div>
							</div>
						</div>
					</div>
					<!-- End of Sign up Info -->

					<!-- Personal Info -->
					<div class="panel panel-info">
						<div class="panel panel-heading">
							<h4 class="panel-title pull-left"><a href="<?= BASEURL . 'affiliate_management/stepTwo/back'?>">Personal Information</a></h4>
							<div class="clearfix"></div>
						</div>

						<div class="panel-body">
							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="firstname">First name: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="firstname" id="firstname" class="form-control" value="<?= $this->session->userdata('aff_firstname'); ?>" readonly>
								</div>

								<div class="col-md-2 col-md-offset-0">
									<label for="lastname">Last name: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="lastname" id="lastname" class="form-control" value="<?= $this->session->userdata('aff_lastname'); ?>" readonly>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="company">Company: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="company" id="company" class="form-control" value="<?= $this->session->userdata('aff_company'); ?>" readonly>
								</div>

								<div class="col-md-2 col-md-offset-0">
									<label for="occupation">Occupation: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="occupation" id="occupation" class="form-control" value="<?= $this->session->userdata('aff_occupation'); ?>" readonly>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="email_address">Email Address: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="email_address" id="email_address" class="form-control" value="<?= $this->session->userdata('aff_email'); ?>" readonly>
								</div>

								<div class="col-md-2 col-md-offset-0">
									<label for="city">City: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="city" id="city" class="form-control" value="<?= $this->session->userdata('aff_city'); ?>" readonly>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="address">Address: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="address" id="address" class="form-control" value="<?= $this->session->userdata('aff_address'); ?>" readonly>
								</div>

								<div class="col-md-2 col-md-offset-0">
									<label for="zip">Zip Code: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="zip" id="zip" class="form-control" value="<?= $this->session->userdata('aff_zip'); ?>" readonly>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="state">State: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="state" id="state" class="form-control" value="<?= $this->session->userdata('aff_state'); ?>" readonly>
								</div>

								<div class="col-md-2 col-md-offset-0">
									<label for="country">Country: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="country" id="country" class="form-control" value="<?= $this->session->userdata('aff_country'); ?>" readonly>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="mobile">Mobile Phone: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="mobile" id="mobile" class="form-control" value="<?= $this->session->userdata('aff_mobile'); ?>" readonly>
								</div>

								<div class="col-md-2 col-md-offset-0">
									<label for="phone">Phone: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="phone" id="phone" class="form-control" value="<?= $this->session->userdata('aff_phone'); ?>" readonly>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="im">IM: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="im" id="im" class="form-control" value="<?= $this->session->userdata('aff_im'); ?>" readonly>
								</div>

								<div class="col-md-2 col-md-offset-0">
									<label for="imtype">IM Type: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="imtype" id="imtype" class="form-control" value="<?= $this->session->userdata('aff_imtype'); ?>" readonly>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="mode_of_contact">Preferred Mode of Contact: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="mode_of_contact" id="mode_of_contact" class="form-control" value="<?= $this->session->userdata('aff_mode_of_contact'); ?>" readonly>
								</div>

								<div class="col-md-2 col-md-offset-0">
									<label for="website">Website: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="website" id="website" class="form-control" value="<?= $this->session->userdata('aff_website'); ?>" readonly>
								</div>
							</div>

							<!-- <br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="currency">Currency: </label>
								</div>

								<div class="col-md-4 col-md-offset-0"> -->
									<input type="hidden" name="currency" id="website" class="form-control" value="<?= $this->session->userdata('aff_currency'); ?>" readonly>
								<!-- </div>
							</div> -->
						</div>
					</div>
					<!-- End of Personal Info -->

					<!-- Payment Info -->
					<!-- <div class="panel panel-info">
						<div class="panel panel-heading">
							<h4 class="panel-title pull-left"><a href="<?= BASEURL . 'affiliate_management/stepThree/back'?>">Payment Information</a></h4>
							<div class="clearfix"></div>
						</div>

						<div class="panel-body">
							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="payment_method">Payment Method: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="payment_method" id="payment_method" class="form-control" value="<?= $this->session->userdata('aff_payment_method'); ?>" readonly>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="bank_name">Bank Name: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="bank_name" id="bank_name" class="form-control" value="<?= $this->session->userdata('aff_bank_name'); ?>" readonly>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="account_name">Account Name: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="account_name" id="account_name" class="form-control" value="<?= $this->session->userdata('aff_account_name'); ?>" readonly>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="account_number">Account Number: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="account_number" id="account_number" class="form-control" value="<?= $this->session->userdata('aff_account_number'); ?>" readonly>
								</div>
							</div>
						</div>
					</div> -->
					<!-- End of Payment Info -->

					<!-- Terms Options -->
					<div class="panel panel-info">
						<div class="panel panel-heading">
							<h4 class="panel-title pull-left"><a href="<?= BASEURL . 'affiliate_management/stepFour/back'?>">Terms Options</a></h4>
							<div class="clearfix"></div>
						</div>

						<div class="panel-body">
							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<input type="checkbox" name="check_percentage" id="check_percentage" <?= ($this->session->userdata('aff_check_percentage') == true) ? "checked":"" ?> disabled> Percentage
								</div>

								<div class="col-md-3 col-md-offset-0">
									<input type="checkbox" name="active_player" id="active_player" <?= ($this->session->userdata('aff_check_active_player') == true) ? "checked":"" ?> disabled> Pay per Active Player
								</div>

								<!-- <div class="col-md-1 col-md-offset-0">
									<input type="checkbox" name="check_cpa" id="check_cpa" <?= ($this->session->userdata('aff_check_cpa') == true) ? "checked":"" ?> disabled> CPA
								</div>

								<div class="col-md-2 col-md-offset-0">
									<input type="text" name="cpa" id="cpa" class="form-control" value="<?= $this->session->userdata('aff_cpa') ?>" readonly>
								</div> -->
							</div>
						</div>
					</div>
					<!-- End of Terms Options -->

					<!-- Affiliate Game Platform -->
					<div class="panel panel-info">
						<div class="panel panel-heading">
							<h4 class="panel-title pull-left"><a href="<?= BASEURL . 'affiliate_management/stepFive/back'?>">Affiliate Game Platform</a></h4>
							<div class="clearfix"></div>
						</div>

						<?php
							$games = explode(',', $this->session->userdata('aff_game'));
							$percentage = $this->session->userdata('aff_percentage');
						?>

						<div class="panel-body">
							<div class="row">
								<table style="margin: 0 10px; float: left;">
									<thead>
										<th>Game Name</th>
										<th>Percentage</th>
									</thead>
									<tbody>
										<?php foreach ($game as $key => $value_game) { ?>
										<tr>
											<td>
												<input type="checkbox" name="game[]"
											<?php
												foreach ($games as $value) {
													if($value == $value_game['gameId']) {
														echo "checked";
														break;
													}
												}
											?>
											value="<?= $value_game['game'] ?>" disabled> <?= $value_game['game'] ?>
											</td>

											<td style="margin: 10px 0 0 10px; float: left; width: 50px;">
												<?php 
													if(!empty($percentage)) {
														foreach($percentage as $percentage_value) { 
															if($percentage_value['gameId'] == $value_game['gameId']) {
												?>
																<input type="text" class="form-control" value="<?= $percentage_value['percentage'] ?>" readonly>
												<?php 
															} else {
												?>
																<input type="text" class="form-control" value="0" readonly>
												<?php 
															}
														}		 
													} else {
												?>
														<input type="text" class="form-control" value="0" readonly>
												<?php } ?>
											</td>
										</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<!-- End of Affiliate Game Platform -->

					<!-- Affiliate Payout Options -->
					<div class="panel panel-info">
						<div class="panel panel-heading">
							<h4 class="panel-title pull-left"><a href="<?= BASEURL . 'affiliate_management/stepSix/back'?>">Affiliate Payout Options</a></h4>
							<div class="clearfix"></div>
						</div>

						<div class="panel-body">
							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="minimum">Minimum: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="minimum" id="minimum" class="form-control" value="<?= $this->session->userdata('aff_minimum') ?>" readonly>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="maximum">Maximum: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="maximum" id="maximum" class="form-control" value="<?= $this->session->userdata('aff_maximum') ?>" readonly>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="payment_option">Payment Option: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="payment_option" id="payment_option" class="form-control" value="<?= $this->session->userdata('aff_payment_options') ?>" readonly>
								</div>
							</div>
						</div>
					</div>
					<!-- End of Affiliate Payout Options -->

					<!-- Account Information -->
					<div class="panel panel-info">
						<div class="panel panel-heading">
							<h4 class="panel-title pull-left"><a href="<?= BASEURL . 'affiliate_management/stepSeven/back'?>">Account Information</a></h4>
							<div class="clearfix"></div>
						</div>

						<div class="panel-body">
							<?php
								$account = explode(',', $this->session->userdata('aff_account_info'));
							?>
							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="frozen">Frozen: </label> &nbsp;&nbsp;
									<input type="checkbox" name="frozen" id="frozen" disabled
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
									<input type="checkbox" name="internal_account" id="internal_account" disabled
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
						</div>
					</div>
					<!-- End of Account Information -->

					<div class="row">
						<div class="col-md-2 col-md-offset-5">
							<input type="submit" name="submit" id="submit" class="form-control btn btn-primary" value="Finish" <?= ($this->session->userdata('aff_username') == null) ? 'disabled':'' ?> >
						</div>
					</div>
				</form>
			</div>

			<div class="panel-footer">

			</div>
		</div>
	</div>
</div>
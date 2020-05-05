<div class="row">
	<div class="col-md-12" id="toggleView">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title pull-left"><i class="glyphicon glyphicon-list-alt"></i> <?= lang('aff.ai63'); ?> : <i><?= $affiliates['firstname'] . " " . $affiliates['lastname'] ?></i> </h4>
				<a href="<?= BASEURL . 'affiliate_management/viewAffiliates'?>" class="btn btn-primary btn-sm pull-right" id="view_affiliate"><span class="glyphicon glyphicon-remove"></span></a>
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body" id="affiliate_panel_body">
				<form method="POST" action="<?= BASEURL . 'affiliate_management/verifyEditAffiliate/' . $affiliates['affiliateId'] . '/' . $affiliates['affiliatePayoutId'] ?>" accept-charset="utf-8">
					<!-- Personal Info -->
					<!-- <div class="panel panel-info">
						<div class="panel panel-heading">
							<h4 class="panel-title pull-left">Personal Information</h4>
							<div class="clearfix"></div>
						</div>

						<div class="panel-body">
							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="firstname">First name: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="firstname" id="firstname" class="form-control" value="<?= $affiliates['firstname']; ?>">
									<label style="color: red; font-size: 12px;"><?php echo form_error('firstname'); ?></label>
								</div>

								<div class="col-md-2 col-md-offset-0">
									<label for="lastname">Last name: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="lastname" id="lastname" class="form-control" value="<?= $affiliates['lastname']; ?>">
									<label style="color: red; font-size: 12px;"><?php echo form_error('lastname'); ?></label>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="company">Company: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="company" id="company" class="form-control" value="<?= $affiliates['company']; ?>">
									<label style="color: red; font-size: 12px;"><?php echo form_error('company'); ?></label>
								</div>

								<div class="col-md-2 col-md-offset-0">
									<label for="occupation">Occupation: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="occupation" id="occupation" class="form-control" value="<?= $affiliates['occupation']; ?>">
									<label style="color: red; font-size: 12px;"><?php echo form_error('occupation'); ?></label>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="email">Email Address: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="email" id="email" class="form-control" value="<?= $affiliates['email']; ?>">
									<label style="color: red; font-size: 12px;"><?php echo form_error('email'); ?></label>
								</div>

								<div class="col-md-2 col-md-offset-0">
									<label for="city">City: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="city" id="city" class="form-control" value="<?= $affiliates['city']; ?>">
									<label style="color: red; font-size: 12px;"><?php echo form_error('city'); ?></label>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="address">Address: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="address" id="address" class="form-control" value="<?= $affiliates['address']; ?>">
									<label style="color: red; font-size: 12px;"><?php echo form_error('address'); ?></label>
								</div>

								<div class="col-md-2 col-md-offset-0">
									<label for="zip">Zip Code: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="zip" id="zip" class="form-control" value="<?= $affiliates['zip']; ?>">
									<label style="color: red; font-size: 12px;"><?php echo form_error('zip'); ?></label>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="state">State: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="state" id="state" class="form-control" value="<?= $affiliates['state']; ?>">
									<label style="color: red; font-size: 12px;"><?php echo form_error('state'); ?></label>
								</div>

								<div class="col-md-2 col-md-offset-0">
									<label for="country">Country: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<select name="country" id="country" class="form-control">
										<option value="">Select Country</option>
										<?php foreach (unserialize(COUNTRY_LIST) as $key) {  ?>
			                                <option value="<?= $key?>" <?= ($affiliates['country'] == $key) ? 'selected':'' ?>><?= $key?></option>
			                            <?php } ?>
									</select>
									<label style="color: red; font-size: 12px;"><?php echo form_error('country'); ?></label>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="mobile">Mobile Phone: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="mobile" id="mobile" class="form-control" value="<?= $affiliates['mobile']; ?>">
									<label style="color: red; font-size: 12px;"><?php echo form_error('mobile'); ?></label>
								</div>

								<div class="col-md-2 col-md-offset-0">
									<label for="phone">Phone: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="phone" id="phone" class="form-control" value="<?= $affiliates['phone']; ?>">
									<label style="color: red; font-size: 12px;"><?php echo form_error('phone'); ?></label>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="im">IM: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="im" id="im" class="form-control" value="<?= $affiliates['im']; ?>">
									<label style="color: red; font-size: 12px;"><?php echo form_error('im'); ?></label>
								</div>

								<div class="col-md-2 col-md-offset-0">
									<label for="imtype">IM Type: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="imtype" id="imtype" class="form-control" value="<?= $affiliates['imType']; ?>">
									<label style="color: red; font-size: 12px;"><?php echo form_error('imtype'); ?></label>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="mode_of_contact">Preferred Mode of Contact: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<select name="mode_of_contact" id="mode_of_contact" class="form-control">
										<option value="">Select Mode of Contact</option>
										<option value="email" <?= ($affiliates['modeOfContact'] == "email") ? "selected":""?> >Email</option>
										<option value="phone" <?= ($affiliates['modeOfContact'] == "phone") ? "selected":""?> >Phone</option>
										<option value="mobile" <?= ($affiliates['modeOfContact'] == "mobile") ? "selected":""?> >Mobile Phone</option>
										<option value="im" <?= ($affiliates['modeOfContact'] == "im") ? "selected":""?> >Instant Message</option>
									</select>
									<label style="color: red; font-size: 12px;"><?php echo form_error('mode_of_contact'); ?></label>
								</div>

								<div class="col-md-2 col-md-offset-0">
									<label for="website">Website: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="website" id="website" class="form-control" value="<?= $affiliates['website']; ?>">
									<label style="color: red; font-size: 12px;"><?php echo form_error('website'); ?></label>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="currency">Currency: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<select name="currency" id="currency" class="form-control">
										<option value="">Select Currency</option>
										<option value="CNY" <?= ($affiliates['currency'] == 'CNY') ? 'selected':'' ?> >CNY</option>
										<option value="USD" <?= ($affiliates['currency'] == 'USD') ? 'selected':'' ?> >USD</option>
									</select>
									<label style="color: red; font-size: 12px;"><?php echo form_error('currency'); ?></label>
								</div>
							</div>			
							<input type="hidden" name="currency" id="currency" value="<?= $affiliates['currency']?>">
						</div>
					</div> -->
					<!-- End of Personal Info -->

					<!-- Payment Info -->
					<!-- <div class="panel panel-info">
						<div class="panel panel-heading">
							<h4 class="panel-title pull-left">Payment Information</h4>
							<div class="clearfix"></div>
						</div>

						<div class="panel-body">
							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="payment_method">Payment Method: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<?php $payment_method = isset($affiliatepayment['paymentMethod']) ? $affiliatepayment['paymentMethod']:''?>
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
									<input type="text" name="bank_name" id="bank_name" class="form-control" value="<?= isset($affiliatepayment['bankName']) ? $affiliatepayment['bankName']:'' ?>">
									<label style="color: red; font-size: 12px;"><?php echo form_error('bank_name'); ?></label>
								</div>
							</div>	

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="account_name">Account Name: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="account_name" id="account_name" class="form-control" value="<?= isset($affiliatepayment['accountName']) ? $affiliatepayment['accountName']:'' ?>">
									<label style="color: red; font-size: 12px;"><?php echo form_error('account_name'); ?></label>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="account_number">Account Number: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="account_number" id="account_number" class="form-control" value="<?= isset($affiliatepayment['accountNumber']) ? $affiliatepayment['accountNumber']:'' ?>">
									<label style="color: red; font-size: 12px;"><?php echo form_error('account_number'); ?></label>
								</div>
							</div>
						</div>
					</div> -->
					<!-- End of Payment Info -->

					<!-- Terms Options -->
					<div class="panel panel-info">
						<div class="panel panel-heading">
							<h4 class="panel-title pull-left"><?= lang('aff.ai64'); ?></h4>
							<div class="clearfix"></div>
						</div>

						<div class="panel-body">
							<?php 
								$check_percentage = false;
								$check_active_player = false;
								$check_cpa = false;

								$percentage = null;
								$cpa = null;

								foreach ($affiliateoptions as $key => $value) {
									if($value['optionsType'] == 'percentage') {
										$check_percentage = true;
										$percentage = $value['optionsValue'];
									} else if($value['optionsType'] == 'cpa') {
										$check_cpa = true;
										$cpa = $value['optionsValue'];
									} else if($value['optionsType'] == 'active player') {
										$check_active_player = true;
									}
								}
							?>
							<div class="row">
								<div class="col-md-6 col-md-offset-0">
									<table class="table">
										<thead>
											<th><?= lang('cms.gamename'); ?></th>
											<th><input type="checkbox" name="check_percentage" id="check_percentage" <?= ($check_percentage == true) ? "checked":"" ?> onclick="percentage();"> <?= lang('aff.ai33'); ?> (%)</th>
										</thead>
										<tbody>
											<?php foreach ($game as $key => $value_game) { ?>
											<tr>
												<td>
													<input type="checkbox" name="game[]"
												<?php
													foreach ($affiliategame as $value) {
														if($value['game'] == $value_game['gameId']) {
															echo "checked";
															break;
														}
													}
												?>
												value="<?= $value_game['gameId'] ?>" id="check_game_<?= $value_game['gameId'] ?>" onclick="gamePercentage(<?= $value_game['gameId'] ?>);"> <?= $value_game['game'] ?>
												</td>

												<td>
													<?php 
														if(!empty($affiliategame)) {
															foreach($affiliategame as $affiliategame_value) { 
																if($affiliategame_value['game'] == $value_game['gameId']) {
													?>
																	<input type="text" name="percentage_<?= $value_game['gameId'] ?>" id="percentage_<?= $value_game['gameId'] ?>"  class="form-control percentage" value="<?= $affiliategame_value['percentage'] ?>" style="width: 20%;">
													<?php 
																} else {
													?>
																	<input type="text" name="percentage_<?= $value_game['gameId'] ?>" id="percentage_<?= $value_game['gameId'] ?>"  class="form-control percentage" value="0" readonly style="width: 20%;">
													<?php 
																}
															}		 
														} else {
													?>
															<input type="text" name="percentage_<?= $value_game['gameId'] ?>" id="percentage_<?= $value_game['gameId'] ?>"  class="form-control percentage" value="0" readonly style="width: 20%;">
													<?php } ?>
												</td>
											</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
							</div>	

							<div class="row">
								<div class="col-md-6 col-md-offset-0">
									<label style="color: red; font-size: 12px;"><?php echo form_error('game'); ?></label>
								</div>
							</div>		

							<div class="row">
								<!-- <div class="col-md-2 col-md-offset-0">
									<input type="checkbox" name="check_percentage" id="check_percentage" <?= ($check_percentage == true) ? "checked":"" ?> onclick="percentage();"> Percentage (%)
								</div> -->

								<!-- <div class="col-md-1 col-md-offset-0">
									<input type="text" name="percentage" id="percentage" class="form-control" value="<?= $percentage ?>">
								</div> -->

								<div class="col-md-3 col-md-offset-0">
									<input type="checkbox" name="check_active_player" id="check_active_player" <?= ($check_active_player == true) ? "checked":"" ?>> <?= lang('aff.ai65'); ?>
								</div>

								<!-- <div class="col-md-1 col-md-offset-0">
									<input type="checkbox" name="check_cpa" id="check_cpa" <?= ($check_cpa == true) ? "checked":"" ?>> CPA
								</div>

								<div class="col-md-1 col-md-offset-0">
									<input type="text" name="cpa" id="cpa" class="form-control" value="<?= $cpa ?>">
								</div> -->
							</div>

							<div class="row">
								<div class="col-md-6 col-md-offset-0">
									<label style="color: red; font-size: 12px;"><?php echo form_error('check_active_player'); ?></label>
								</div>
							</div>		
						</div>
					</div>
					<!-- End of Terms Options -->

					<!-- Affiliate Payout Options -->
					<div class="panel panel-info">
						<div class="panel panel-heading">
							<h4 class="panel-title pull-left"><?= lang('aff.ai66'); ?></h4>
							<div class="clearfix"></div>
						</div>

						<div class="panel-body">
							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="minimum"><?= lang('aff.ai67'); ?>: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="minimum" id="minimum" class="form-control" value="<?= $affiliatepayout['minimum'] ?>">
									<label style="color: red; font-size: 12px;"><?php echo form_error('minimum'); ?></label>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="maximum"><?= lang('aff.ai68'); ?>: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="maximum" id="maximum" class="form-control" value="<?= $affiliatepayout['maximum'] ?>">
									<label style="color: red; font-size: 12px;"><?php echo form_error('maximum'); ?></label>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="payment_option"><?= lang('aff.ai69'); ?>: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<!-- <select name="payment_options" id="payment_options" class="form-control" disabled>
										<option value="" >Click to Select</option>
										<option value="Daily" <?= ($affiliatepayout['paymentOption'] == 'Daily') ? 'selected':'' ?> >Daily</option>
										<option value="Weekly" <?= ($affiliatepayout['paymentOption'] == 'Weekly') ? 'selected':'' ?> >Weekly</option>
										<option value="Monthly" <?= ($affiliatepayout['paymentOption'] == 'Monthly') ? 'selected':'' ?> >Monthly</option>
										<option value="Yearly" <?= ($affiliatepayout['paymentOption'] == 'Yearly') ? 'selected':'' ?> >Yearly</option>
									</select> -->
									<input type="text" name="payment_options" id="payment_options" class="form-control" value="Monthly" readonly>
									<label style="color: red; font-size: 12px;"><?php echo form_error('payment_options'); ?></label>
								</div>
							</div>		
						</div>
					</div>
					<!-- End of Affiliate Payout Options -->

					<!-- Account Information -->
					<div class="panel panel-info">
						<div class="panel panel-heading">
							<h4 class="panel-title pull-left"><?= lang('aff.ai70'); ?></h4>
							<div class="clearfix"></div>
						</div>

						<div class="panel-body">
							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="frozen"><?= lang('player.15'); ?>: </label> &nbsp;&nbsp;
									<input type="checkbox" name="frozen" id="frozen" <?= ($affiliates['status'] == 1) ? 'checked':'' ?>>
								</div>

								<!-- <div class="col-md-3 col-md-offset-0">
									<label for="internal_account">Internal Account: </label> &nbsp;&nbsp;
									<input type="checkbox" name="internal_account" id="internal_account" <?= ($affiliates['internalAccount'] == 1) ? 'checked':'' ?>>
								</div> -->
							</div>	
						</div>
					</div>
					<!-- End of Account Information -->

					<div class="row">
						<div class="col-md-2 col-md-offset-5">
							<input type="submit" name="submit" id="submit" class="form-control btn btn-primary" value="<?= lang('lang.update'); ?>" >
						</div>
					</div>		
				</form>			
			</div>

			<div class="panel-footer">

			</div>
		</div>
	</div>
</div>
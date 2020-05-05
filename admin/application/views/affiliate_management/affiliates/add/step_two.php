<div class="row">
	<div class="col-md-12" id="toggleView">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title pull-left"><i class="glyphicon glyphicon-list-alt"></i> Add Affiliate </h4>
				<a href="<?=BASEURL . 'affiliate_management/viewAffiliates'?>" class="btn btn-primary btn-sm pull-right" id="view_affiliate"><span class="glyphicon glyphicon-remove"></span></a>
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body" id="affiliate_panel_body">
				<ol class="breadcrumb">
					<li class="active"><a href="<?=BASEURL . 'affiliate_management/addAffiliate'?>">Step 1: Sign Up Information</a></li>
					<li class="active"><b>Step 2: Personal Information</b></li>
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
						<h4 class="panel-title pull-left"> Personal Information </h4>
						<div class="clearfix"></div>
					</div>

					<div class="panel-body">
						<form method="POST" action="<?=BASEURL . 'affiliate_management/stepThree'?>" accept-charset="utf-8">
							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="firstname">First name: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="firstname" id="firstname" class="form-control" value="<?=($this->session->userdata('aff_firstname') != null) ? $this->session->userdata('aff_firstname') : set_value('firstname')?>">
									<label style="color: red; font-size: 12px;"><?php echo form_error('firstname');?></label>
								</div>

								<div class="col-md-2 col-md-offset-0">
									<label for="lastname">Last name: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="lastname" id="lastname" class="form-control" value="<?=($this->session->userdata('aff_lastname') != null) ? $this->session->userdata('aff_lastname') : set_value('lastname')?>">
									<label style="color: red; font-size: 12px;"><?php echo form_error('lastname');?></label>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="company">Company: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="company" id="company" class="form-control" value="<?=($this->session->userdata('aff_company') != null) ? $this->session->userdata('aff_company') : set_value('company')?>">
									<label style="color: red; font-size: 12px;"><?php echo form_error('company');?></label>
								</div>

								<div class="col-md-2 col-md-offset-0">
									<label for="occupation">Occupation: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="occupation" id="occupation" class="form-control" value="<?=($this->session->userdata('aff_occupation') != null) ? $this->session->userdata('aff_occupation') : set_value('occupation')?>">
									<label style="color: red; font-size: 12px;"><?php echo form_error('occupation');?></label>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="email">Email Address: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="email" name="email" id="email" class="form-control" value="<?=($this->session->userdata('aff_email') != null) ? $this->session->userdata('aff_email') : set_value('email')?>" data-toggle="tooltip" title="Make sure you enter a valid email address.">
									<label style="color: red; font-size: 12px;"><?php echo form_error('email');?></label>
								</div>

								<div class="col-md-2 col-md-offset-0">
									<label for="city">City: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="city" id="city" class="form-control" value="<?=($this->session->userdata('aff_city') != null) ? $this->session->userdata('aff_city') : set_value('city')?>">
									<label style="color: red; font-size: 12px;"><?php echo form_error('city');?></label>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="address">Address: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="address" id="address" class="form-control" value="<?=($this->session->userdata('aff_address') != null) ? $this->session->userdata('aff_address') : set_value('address')?>">
									<label style="color: red; font-size: 12px;"><?php echo form_error('address');?></label>
								</div>

								<div class="col-md-2 col-md-offset-0">
									<label for="zip">Zip Code: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="zip" id="zip" class="form-control number_only" value="<?=($this->session->userdata('aff_zip') != null) ? $this->session->userdata('aff_zip') : set_value('zip')?>">
									<label style="color: red; font-size: 12px;"><?php echo form_error('zip');?></label>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="state">State: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="state" id="state" class="form-control" value="<?=($this->session->userdata('aff_state') != null) ? $this->session->userdata('aff_state') : set_value('state')?>">
									<label style="color: red; font-size: 12px;"><?php echo form_error('state');?></label>
								</div>

								<div class="col-md-2 col-md-offset-0">
									<label for="country">Country: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<?php $country = ($this->session->userdata('aff_country') != null) ? $this->session->userdata('aff_country') : set_value('country')?>
									<select name="country" id="country" class="form-control">
										<option value="">Select Country</option>
										<?php foreach (unserialize(COUNTRY_LIST) as $key) {?>
			                                <option value="<?=$key?>" <?=($country == $key) ? 'selected' : ''?>><?=lang('country.' . $key)?></option>
			                            <?php }
?>
									</select>
									<label style="color: red; font-size: 12px;"><?php echo form_error('country');?></label>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="mobile">Mobile Phone: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="mobile" id="mobile" class="form-control number_only" value="<?=($this->session->userdata('aff_mobile') != null) ? $this->session->userdata('aff_mobile') : set_value('mobile')?>">
									<label style="color: red; font-size: 12px;"><?php echo form_error('mobile');?></label>
								</div>

								<div class="col-md-2 col-md-offset-0">
									<label for="phone">Phone: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="phone" id="phone" class="form-control number_only" value="<?=($this->session->userdata('aff_phone') != null) ? $this->session->userdata('aff_phone') : set_value('phone')?>">
									<label style="color: red; font-size: 12px;"><?php echo form_error('phone');?></label>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="im">IM: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="im" id="im" class="form-control" value="<?=($this->session->userdata('aff_im') != null) ? $this->session->userdata('aff_im') : set_value('im')?>">
									<label style="color: red; font-size: 12px;"><?php echo form_error('im');?></label>
								</div>

								<div class="col-md-2 col-md-offset-0">
									<label for="imtype">IM Type: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="imtype" id="imtype" class="form-control" value="<?=($this->session->userdata('aff_imtype') != null) ? $this->session->userdata('aff_imtype') : set_value('imtype')?>" data-toggle="tooltip" title="Please state the IM type(skype, yahoo messenger, etc.). If you provided an im account.">
									<label style="color: red; font-size: 12px;"><?php echo form_error('imtype');?></label>
								</div>
							</div>

							<br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="mode_of_contact">Preferred Mode of Contact: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<?php $mode_of_contact = ($this->session->userdata('aff_mode_of_contact') != null) ? $this->session->userdata('aff_mode_of_contact') : set_value('mode_of_contact')?>
									<select name="mode_of_contact" id="mode_of_contact" class="form-control" data-toggle="tooltip" title="Select your preferred way on how we will contact you.">
										<option value="">Select Mode of Contact</option>
										<option value="email" <?=($mode_of_contact == "email") ? "selected" : ""?> >Email</option>
										<option value="phone" <?=($mode_of_contact == "phone") ? "selected" : ""?> >Phone</option>
										<option value="mobile" <?=($mode_of_contact == "mobile") ? "selected" : ""?> >Mobile Phone</option>
										<option value="im" <?=($mode_of_contact == "im") ? "selected" : ""?> >Instant Message</option>
									</select>
									<label style="color: red; font-size: 12px;"><?php echo form_error('mode_of_contact');?></label>
								</div>

								<div class="col-md-2 col-md-offset-0">
									<label for="website">Website: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<input type="text" name="website" id="website" class="form-control" value="<?=($this->session->userdata('aff_website') != null) ? $this->session->userdata('aff_website') : set_value('website')?>" data-toggle="tooltip" title="If you have a website, Please provide the link in here.">
									<label style="color: red; font-size: 12px;"><?php echo form_error('website');?></label>
								</div>
							</div>

							<!-- <br/>

							<div class="row">
								<div class="col-md-2 col-md-offset-0">
									<label for="currency">Currency: </label>
								</div>

								<div class="col-md-4 col-md-offset-0">
									<?php $currency = ($this->session->userdata('aff_currency') != null) ? $this->session->userdata('aff_currency') : set_value('currency')?>
									<select name="currency" id="currency" class="form-control">
										<option value="">Select Currency</option>
										<option value="CNY" <?=($currency == 'CNY') ? 'selected' : ''?> >CNY</option>
										<option value="USD" <?=($currency == 'USD') ? 'selected' : ''?> >USD</option>
									</select>
									<label style="color: red; font-size: 12px;"><?php echo form_error('currency');?></label>
								</div>
							</div> -->
							<input type="hidden" name="currency" id="currency" value="<?=$curren?>">

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
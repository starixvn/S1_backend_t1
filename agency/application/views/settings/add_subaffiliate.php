<div class="container">
	<br/><br/>
	<!-- Personal Information -->
	<div class="row">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title pull-left"><i class="glyphicon glyphicon-plus"></i> <?=lang('aff.asb9');?>s</h4>
				<a href="<?=BASEURL . 'affiliate/subaffiliates'?>" class="btn btn-info btn-xs pull-right"><span class="glyphicon glyphicon-remove "></span></a>
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body" id="info_panel_body">
				<form action="<?=BASEURL . 'affiliate/verifyRegister/subaffiliate'?>" method="POST">
                    <input type="hidden" name="parentId" value="<?=$parentId = $this->session->userdata('affiliateId');?>">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="username"><?=lang('reg.03');?> </label>
								<input type="text" name="username" id="username" class="form-control input-sm" value="<?=set_value('username');?>">
								<span style="color: red; font-size: 12px;"><?php echo form_error('username');?></span>
							</div>
							<div class="form-group">
								<label for="password"><?=lang('reg.05');?> </label>
								<input type="password" name="password" id="password" class="form-control" value="<?=set_value('password');?>" data-toggle="tooltip" title="Make sure you enter your password.">
								<span style="color: red; font-size: 12px;"><?php echo form_error('password');?></span>
							</div>
							<div class="form-group">
								<label for="confirm_password"><?=lang('reg.07');?> </label>
								<input type="password" name="confirm_password" id="confirm_password" class="form-control" value="<?=set_value('confirm_password');?>"
                                       data-toggle="tooltip" title="Make sure you enter your password.">
								<span style="color: red; font-size: 12px;"><?php echo form_error('confirm_password');?></span>
							</div>
							<div class="form-group">
								<label for="firstname"><?=lang('reg.a09');?> </label>
								<input type="password" name="firstname" id="firstname" class="form-control input-sm" value="<?=set_value('firstname');?>">
								<span style="color: red; font-size: 12px;"><?php echo form_error('firstname');?></span>
							</div>
							<div class="form-group">
								<label for="lastname"><?=lang('reg.a10');?> </label>
								<input type="text" name="lastname" id="lastname" class="form-control input-sm" value="<?=set_value('lastname');?>">
								<span style="color: red; font-size: 12px;"><?php echo form_error('lastname');?></span>
							</div>
							<div class="form-group">
								<label for="email"><?=lang('reg.a17');?> </label>
								<input type="text" name="email" id="email" class="form-control" value="<?=set_value('email');?>" data-toggle="tooltip" title="Make sure you enter a valid email address.">
								<span style="color: red; font-size: 12px;"><?php echo form_error('email');?></span>
							</div>
							<div class="form-group">
                            	<label for="gender"><?=lang('reg.a12');?> </label>
                                <div class="form-group">
                                    <?php
$gender = set_value('gender');
?>
                                    <input type="radio" name="gender" id="male" value="Male" <?=($gender == 'Male') ? 'checked' : ''?>> <?=lang('reg.a13');?>
                                    <input type="radio" name="gender" id="female" value="Female" <?=($gender == 'Female') ? 'checked' : ''?>> <?=lang('reg.a14');?>
                                </div>
								<span style="color: red; font-size: 12px;"><?php echo form_error('gender');?></span>
	                        </div>
							<div class="form-group">
                            	<label for="birthday"><?=lang('reg.a11');?> </label>
								<input type="text" name="birthday" id="birthday" class="form-control input-sm dateInput" value="<?=set_value('birthday');?>">
								<span style="color: red; font-size: 12px;"><?php echo form_error('birthday');?></span>
	                        </div>
							<div class="form-group">
								<label for="phone"><?=lang('reg.a25');?> </label>
								<input type="text" name="phone" id="phone" class="form-control number_only input-sm" value="<?=set_value('phone');?>">
								<span style="color: red; font-size: 12px;" id="phone_error"><?php echo form_error('phone');?></span>
							</div>
							<div class="form-group">
								<label for="mobile"><?=lang('reg.a24');?> </label>
								<input type="text" name="mobile" id="mobile" class="form-control number_only input-sm" value="<?=set_value('mobile');?>">
								<span style="color: red; font-size: 12px;" id="mobile_error"><?php echo form_error('mobile');?></span>
							</div>
							<div class="form-group">
								<label for="address"><?=lang('reg.a20');?> </label>
								<input type="text" name="address" id="address" class="form-control input-sm" value="<?=set_value('address');?>">
								<span style="color: red; font-size: 12px;"><?php echo form_error('address');?></span>
							</div>
							<div class="form-group">
								<label for="zip"><?=lang('reg.a21');?> </label>
								<input type="text" name="zip" id="zip" class="form-control number_only input-sm" value="<?=set_value('zip');?>">
								<label style="color: red; font-size: 12px;"><?php echo form_error('zip');?></label>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label for="city"><?=lang('reg.a19');?> </label>
								<input type="text" name="city" id="city" class="form-control input-sm" value="<?=set_value('city');?>">
								<span style="color: red; font-size: 12px;"><?php echo form_error('city');?></span>
							</div>
							<div class="form-group">
								<label for="country"><?=lang('reg.a23');?> </label>
								<select name="country" id="country" class="form-control input-sm">
                                    <?php
$country = set_value('country');
?>
									<option value=""><?=lang('reg.a42');?></option>
									<?php foreach (unserialize(COUNTRY_LIST) as $key) {?>
					                    <option value="<?=$key?>" <?=($country == $key) ? 'selected' : ''?>><?=lang('country.' . $key)?></option>
					                <?php }
?>
								</select>
								<span style="color: red; font-size: 12px;"><?php echo form_error('country');?></span>
							</div>
                            <div class="form-group">
								<label for="state"><?=lang('ban.lang');?> </label>
                                <select class="form-control" name="language" id="language">
                                    <?php
$language = set_value('languge');
?>
						        	<option value="English" >English</option>
						        	<option value="Chinese" >中文</option>
						        </select>
						        <span class="errors"><?php echo form_error('languge');?></span>
                            </div>
							<div class="form-group">
								<label for="state"><?=lang('reg.a22');?> </label>
								<input type="text" name="state" id="state" class="form-control input-sm" value="<?=set_value('state');?>">
								<span style="color: red; font-size: 12px;"><?php echo form_error('state');?></span>
							</div>
							<div class="form-group">
								<label for="occupation"><?=lang('reg.a16');?> </label>
								<input type="text" name="occupation" id="occupation" class="form-control input-sm" value="<?=set_value('occupation');?>">
								<span style="color: red; font-size: 12px;"><?php echo form_error('occupation');?></span>
							</div>
							<div class="form-group">
								<label for="company"><?=lang('reg.a15');?> </label>
								<input type="text" name="company" id="company" class="form-control input-sm" value="<?=set_value('company');?>">
								<span style="color: red; font-size: 12px;"><?php echo form_error('company');?></span>
							</div>
							<div class="form-group">
								<label for="imtype1"><?=lang('reg.a26');?> </label>
								<select name="imtype1" id="imtype1" class="form-control input-sm" data-toggle="tooltip" title="<?=lang('reg.a50');?>" onchange="imCheck(this.value, '1');">
                                    <?php
$imtype1 = set_value('imtype1');
?>
									<option value=""><?=lang('reg.a43');?></option>
									<option value="QQ" <?=($imtype1 == "QQ") ? 'selected' : ''?> ><?=lang('reg.a27');?></option>
									<option value="Skype" <?=($imtype1 == "Skype") ? 'selected' : ''?> ><?=lang('reg.a28');?></option>
									<option value="MSN"  <?=($imtype1 == "MSN") ? 'selected' : ''?>><?=lang('reg.a29');?></option>
								</select>
								<span style="color: red; font-size: 12px;" id="imtype_error"><?php echo form_error('imtype1');?></span>
							</div>
							<div class="form-group">
								<label for="im1"><?=lang('reg.a30');?> </label>
								<input type="text" name="im1" id="im1" class="form-control input-sm" value="<?=set_value('im1');?>">
								<span style="color: red; font-size: 12px;" id="im_error"><?php echo form_error('im1');?></span>
							</div>
							<div class="form-group">
								<label for="imtype2"><?=lang('reg.a31');?> </label>
								<select name="imtype2" id="imtype2" class="form-control input-sm" data-toggle="tooltip" title="<?=lang('reg.a49');?>" onchange="imCheck(this.value, '2');">
                                    <?php
$imtype2 = set_value('imtype1');
?>
									<option value=""><?=lang('reg.a44');?></option>
									<option value="QQ" <?=($imtype2 == "QQ") ? 'selected' : ''?> ><?=lang('reg.a27');?></option>
									<option value="Skype" <?=($imtype2 == "Skype") ? 'selected' : ''?> ><?=lang('reg.a28');?></option>
									<option value="MSN"  <?=($imtype2 == "MSN") ? 'selected' : ''?>><?=lang('reg.a29');?></option>
								</select>
								<span style="color: red; font-size: 12px;" id="imtype_error"><?php echo form_error('imtype2');?></span>
							</div>
							<div class="form-group">
								<label for="im2"><?=lang('reg.a35');?> </label>
								<input type="text" name="im2" id="im2" class="form-control input-sm" value="<?=set_value('im2');?>">
								<span style="color: red; font-size: 12px;" id="im_error"><?php echo form_error('im2');?></span>
							</div>
							<div class="form-group">
								<label for="website"><?=lang('reg.a41');?> </label>
								<input type="text" name="website" id="website" class="form-control input-sm" value="<?=set_value('website');?>" data-toggle="tooltip" title="<?=lang('reg.a47');?>">
								<span style="color: red; font-size: 12px;"><?php echo form_error('website');?></span>
							</div>
							<div class="form-group">
								<label for="mode_of_contact"><?=lang('reg.a36');?> </label>
								<select name="mode_of_contact" id="mode_of_contact" class="form-control input-sm" data-toggle="tooltip" title="<?=lang('reg.a48');?>">
                                    <?php
$modeOfContact = set_value('mode_of_contact');
?>
									<option value=""><?=lang('aff.ai87')?></option>
									<option value="email" <?=($modeOfContact == "email") ? "selected" : ""?> ><?=lang('reg.a37');?></option>
									<option value="phone" <?=($modeOfContact == "phone") ? "selected" : ""?> ><?=lang('reg.a38');?></option>
									<option value="mobile" <?=($modeOfContact == "mobile") ? "selected" : ""?> ><?=lang('reg.a39');?></option>
									<option value="im" <?=($modeOfContact == "im") ? "selected" : ""?> ><?=lang('reg.a40');?></option>
								</select>
								<span style="color: red; font-size: 12px;"><?php echo form_error('mode_of_contact');?></span>
							</div>
						</div>
					</div>

					<hr class="style-one"/>

					<center>
						<input type="hidden" name="currency" id="currency" value="<?=$curren?>" />
						<input type="submit" name="submit" id="submit" class="btn btn-info" value="<?=lang('lang.save');?>">
						<a href="<?=BASEURL . 'affiliate/modifyAccount'?>" class="btn btn-default"><?=lang('lang.cancel');?></a>
					</center>
				</form>
			</div>
		</div>
	</div>
	<!-- End of Personal Information -->
</div>
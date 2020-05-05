<?php $this->session->set_userdata('current_url', current_url());?>
<?php $user_theme = !empty($this->session->userdata('affiliate_theme')) ? $this->session->userdata('affiliate_theme') : 'flatly';?>
<?php $currentLang = isset($_GET['lang']) ? $_GET['lang'] : $this->session->userdata('login_lan');?>




	<style type="text/css">
		.col-centered {
			float: none;
			margin: 0 auto;
			padding-top: 6%;
		}
		/*for gender*/
		.btn span.glyphicon {
			opacity: 0;
		}
		.btn.active span.glyphicon {
			opacity: 1;
		}
		.title-logo {
			display:  block;
			text-align: center;
			font-size: 16px;
			font-weight: 600;
			text-transform: uppercase;
			line-height: 2.8;
		}
		.title-logo img {
			margin-right: 8px;
		}
		.title-logo-left {
			text-align: left;
			color: #fff;
		}
		.wb-radius {
			border-radius: 14px;
			overflow: hidden;
		}
		.no-border {
			border: transparent;
		}
		.control-label {
		    width: 100%;
		    color: #ffffff;
		}

		.panel-primary > .bg-panel-head {
			background-image: linear-gradient(#444, #333 60%, #222);
    		background-repeat: no-repeat;
    		color: #fff;
		}
		.panel-primary > .bg-green {
			background: #85bb31;
		}
		.bg-panel-head {
			padding: 10px 15px;
		    border-top-left-radius: 3px;
		    border-top-right-radius: 3px
		}
		.bg-panel-body {
			background: #141414;
		}
		.bg-panel-footer {
		    padding: 10px 15px;
		    background-color: #676767;
		    border-bottom-right-radius: 3px;
		    border-bottom-left-radius: 3px;
		    text-align: center;
		    color: #fff;
		}
		.bg-panel-footer a {
			color: #fff;
			font-weight: 600;
		}
		.input-group-addon {
			background: transparent;
		}
		.bg-addon {
			background-image: linear-gradient(#444, #333 60%, #222);
    		background-repeat: no-repeat;
    		color: #fff;
    		font-weight: 400;
		}
		.forgot-pass {
			color: #ffffff;
			text-align: center;
			padding: 20px 20px;
		}
		.forgot-pass a {
			font-weight: 600;
			color: #499c00;
		}
		.title-reg {
			font-weight: 600;
			font-size: 16px;
		}
		.radio-inline {
			color: #ffffff;
		}
		.radio-pd {
			padding: 6px 0 8px 0;
		}
		.reg {
			text-align: center;
			font-size: 16px;
			font-weight: 600;
			text-transform: uppercase;
			line-height: 2.8;
		}
		.reg img {
			margin-right: 8px;
		}
		.reg:hover,
		.reg:focus {
			color: #fff;
			text-decoration: none;
		}
		.sep {
			border-bottom: 1px solid rgba(0,0,0,0.3);
			padding-bottom: 18px;
			margin-bottom: 10px;
		}
		.reg-btn-h {
			height: 36px;
			padding-top: 8px;
		}

		.daterangepicker.dropdown-menu {

			padding: 18px 25px 18px 0px;
			font-size: 12px;
		}
	</style>
</head>
<?php
$birthdayVisible = $this->affiliate_manager->checkRegisteredFieldsIfVisible('Birthday') == 0;
$birthdayRequired = $this->affiliate_manager->checkRegisteredFieldsIfRequired('Birthday') == 0;
?>


<style>

.errors{position:absolute;color:red;font-size:12px;}



</style>
<body style="background:#373737">
	<div class="navbar navbar-inverse">
		<div class="container">
			<a  href="<?=site_url('affiliate')?>" class="title-logo-left reg"><img src="/webet/images/logo.png" width="100"> <?=lang('reg.affilate');?></a>
			<div class="col-md-1 pull-right">
				<div class="row" style="margin-top:6px">
					<select class="form-control" name="language" id="language" onchange="changeLanguage();" >
						<option value="1"<?php echo ($this->session->userdata('afflang') == '1' || $currentLang == '1') ? ' selected="selected"' : ''; ?>>English</option>
		        	<option value="2"<?php echo ($this->session->userdata('afflang') == '2' || $currentLang == '2') ? ' selected="selected"' : ''; ?>>中文</option>
					</select>
				</div>
			</div>
		</div>
	</div>
	<div class="container">
		<div class="row">
			<div class="col-md-12 col-centered">
				<div class="panel-primary no-border wb-radius">
					<div class="bg-panel-head bg-green">
						<span class="title-reg"><i class=""></i> <?=lang('reg.a01');?> </span>
						<span class="pull-right"><?php echo lang('Fields with (*) are required.') ?></span>
					</div>
					<div class="panel-body bg-panel-body">
						<form method="POST" id="affiliate-register-form" action="<?=site_url('affiliate/verifyRegister') . '/' . $trackingCode?>" accept-charset="utf-8">
							<input type="hidden" name="parentId" value="<?=$parentId;?>">
							<div class="row">
								<div class="col-md-3 padding-sm-x">
									<div class="form-group">
										<label class="control-label"><b class="text-danger">*</b> <?=lang('reg.03');?></label>
											<!-- <div class="input-group"> -->
											<!-- <span class="input-group-addon bg-addon no-border"><i class="glyphicon glyphicon-user"></i> OG</span> -->
											<input type="text" class="form-control" name="username" id="username"  placeholder=" <?=lang('reg.03');?>" data-toggle="tooltip" data-placement="bottom" title="<?=lang('reg.a04');?>" value="<?=set_value('username');?>" >
											<span class="errors"><?php echo form_error('username'); ?></span>
											<span  id="error-username" class="  errors"></span>

										<!-- </div> -->
									</div>
								</div>
								<div class="col-md-3 padding-sm-x">
									<div class="form-group">
										<label class="control-label"><b class="text-danger">*</b> <?=lang('reg.05');?></label>
											<!-- <div class="input-group"> -->
											<!-- <span class="input-group-addon bg-addon no-border"><i class="glyphicon glyphicon-lock"></i></span> -->
											<input type="password" class="form-control  "  name="password" id="password" placeholder="<?=lang('reg.05');?>" data-toggle="tooltip" data-placement="bottom" title="<?=lang('reg.a06');?>"  value="<?=set_value('password');?>">
											<span class="errors"><?php echo form_error('password'); ?></span>
						                	<span id="error-password" class="errors"></span>
										<!-- </div> -->
									</div>
								</div>

								<div class="col-md-3 padding-sm-x">
									<div class="form-group">
										<label class="control-label"><b class="text-danger">*</b> <?=lang('reg.07');?></label>
										<!-- <div class="input-group"> -->
											<!-- <span class="input-group-addon bg-addon no-border"><i class="glyphicon glyphicon-lock"></i></span> -->
											<input type="password" class="form-control  " name="confirm_password" id="confirm_password"   placeholder="<?=lang('reg.05');?>" data-toggle="tooltip" data-placement="bottom" title="<?=lang('reg.a08');?>" value="<?=set_value('confirm_password');?>" >
											<span class="errors"><?php echo form_error('confirm_password'); ?></span>
						                 	<span id="error-confirm_password" class="errors"></span>
										<!-- </div> -->
									</div>
								</div>
								<div class="col-md-3 padding-sm-x">
									<div class="form-group">
										<label class="control-label"><b class="text-danger">*</b> <?=lang('reg.a17');?></label>
										<!-- <div class="input-group"> -->
											<!-- <span class="input-group-addon bg-addon no-border"><i class="glyphicon glyphicon-envelope"></i></span> -->
											<input type="email" class="form-control" name="email" id="email" placeholder="<?=lang('reg.a17');?>" data-toggle="tooltip" data-placement="bottom" title="<?=lang('reg.a18');?>" value="<?=set_value('email');?>" >
											<span class="errors"><?php echo form_error('email'); ?></span>
											<span id="error-email" class="errors"></span>
										<!-- </div> -->
									</div>
								</div>

								<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('First Name') == 0): ?>
								<div class="col-md-3 padding-sm-x">
									<div class="form-group">
										<label class="control-label">
										<?php if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('First Name') == 0): ?>
										    <b class="text-danger">*</b>
										 <?php endif;?>
										<?=lang('reg.a09');?>
										</label>
										<!-- <div class="input-group"> -->
											<!-- <span class="input-group-addon bg-addon no-border"><i class="glyphicon glyphicon-user"></i></span> -->
											<input type="text"  name="firstname" id="firstname" class="form-control  " placeholder="first name" data-toggle="tooltip" data-placement="bottom" title=""  value="<?=set_value('firstname');?>">
											<span class="errors"><?php echo form_error('firstname'); ?></span>
										<!-- </div> -->
									</div>
								</div>
							   <?php endif;?>


							   <?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('Last Name') == 0): ?>
								<div class="col-md-3 padding-sm-x">
									<div class="form-group">
										<label class="control-label">
										<?php if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Last Name') == 0): ?>
										    <b class="text-danger">*</b>
										 <?php endif;?>
										 <?=lang('reg.a10');?>
										 </label>
										<!-- <div class="input-group"> -->
											<!-- <span class="input-group-addon bg-addon no-border"><i class="glyphicon glyphicon-user"></i></span> -->
											<input type="text"  name="lastname" id="lastname" class="form-control  " placeholder="<?=lang('reg.a10');?>" data-toggle="tooltip" data-placement="bottom" value="<?=set_value('lastname');?>" >
											<span class="errors"><?php echo form_error('lastname'); ?></span>
										<!-- </div> -->
									</div>
								</div>
								<?php endif;?>

								<?php if ($birthdayVisible): ?>
								<div class="col-md-3 padding-sm-x">
									<div class="form-group">
										<label class="control-label">
										<?php if ($birthdayRequired): ?>
										 <b class="text-danger">*</b>
										 <?php endif;?>
										 <?=lang('reg.a11');?>
										 </label>
										<!-- <div class="input-group"> -->
											<!-- <span class="input-group-addon bg-addon no-border"><i class="glyphicon glyphicon-calendar"></i></span> -->
											<input type="text" name="birthday" id="birthday" class="form-control dateInput" value="<?=set_value('birthday');?>">
											<span class="errors"><?php echo form_error('birthday'); ?></span>
											<span id="error-birthday" class="errors"></span>
										<!-- </div> -->
									</div>
								</div>
								<?php endif;?>

								<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('Gender') == 0): ?>
								<div class="col-md-3 padding-sm-x">
									<div class="form-group">
										<label class="control-label">
										<?php if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Gender') == 0): ?>
										 <b class="text-danger">*</b>
										 <?php endif;?>
										 <?=lang('reg.a12');?>
										 </label>
										<div class="btn-group" data-toggle="buttons">
											<label class="btn btn-default reg-btn-h active"><?=lang('reg.a13');?>
												<input type="radio" name="gender" id="male" value="Male" <?=(set_value('gender') == 'Male') ? 'checked' : ''?>>
												<span class="glyphicon glyphicon-ok"></span>
											</label>
											<label class="btn btn-default reg-btn-h"> <?=lang('reg.a14');?>
												<input type="radio" name="gender" id="female" value="Female" <?=(set_value('gender') == 'Female') ? 'checked' : ''?>>
												<span class="glyphicon glyphicon-ok"></span>
											</label>
										</div>
										<span class="errors"><?php echo form_error('gender'); ?></span>
									</div>
								</div>
								<?php endif;?>

								<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('Company') == 0): ?>
								<div class="col-md-3 padding-sm-x">
									<div class="form-group">
										<label class="control-label">
										<?php if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Company') == 0): ?>
										 <b class="text-danger">*</b>
										 <?php endif;?>
										 <?=lang('reg.a15');?>
										 </label>
										<!-- <div class="input-group"> -->
											<!-- <span class="input-group-addon bg-addon no-border"><i class="glyphicon glyphicon-envelope"></i></span> -->
											<input type="text" class="form-control "  name="company" id="company"  placeholder="<?=lang('reg.a15');?>" data-toggle="tooltip" data-placement="bottom" value="<?=set_value('company');?>" >
											<span class="errors"><?php echo form_error('company'); ?></span>
										<!-- </div> -->
									</div>
								</div>
								<?php endif;?>

								<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('Occupation') == 0): ?>
								<div class="col-md-3 padding-sm-x">
									<div class="form-group">
										<label class="control-label">
										<?php if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Occupation') == 0): ?>
										 <b class="text-danger">*</b>
										 <?php endif;?>
										 <?=lang('reg.a16');?>
										 </label>
										<!-- <div class="input-group"> -->
											<!-- <span class="input-group-addon bg-addon no-border"><i class="glyphicon glyphicon-envelope"></i></span> -->
											<input type="text" name="occupation" id="occupation" class="form-control " placeholder="<?=lang('reg.a16');?>" data-toggle="tooltip" data-placement="bottom" title="" value="<?=set_value('occupation');?>" >
											<span class="errors"><?php echo form_error('occupation'); ?></span>
										<!-- </div> -->
									</div>
								</div>
								<?php endif;?>

								<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('Mobile Phone') == 0): ?>
								<div class="col-md-3 padding-sm-x">
									<div class="form-group">
										<label class="control-label">
										<?php if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Mobile Phone') == 0): ?>
										 <b class="text-danger">*</b>
										 <?php endif;?>
										 <?=lang('reg.a24');?>
										 </label>
										<!-- <div class="input-group"> -->
											<!-- <span class="input-group-addon bg-addon no-border"><i class="glyphicon glyphicon-envelope"></i></span> -->
											<input type="text" name="mobile" id="mobile" maxlength="50"  class="form-control number_only " value="<?=set_value('mobile');?>" placeholder="<?=lang('reg.a24');?>"  value="<?=set_value('mobile');?>" data-toggle="tooltip" data-placement="bottom"  >
											<span id="error-mobile"class="errors"></span>
										<!-- </div> -->
									</div>
								</div>
								<?php endif;?>

								<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('Phone') == 0): ?>
								<div class="col-md-3 padding-sm-x">
									<div class="form-group">
										<label class="control-label">
										<?php if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Phone') == 0): ?>
										 <b class="text-danger">*</b>
										 <?php endif;?>
										 <?=lang('reg.a25');?>
										 </label>
										<!-- <div class="input-group"> -->
											<!-- <span class="input-group-addon bg-addon no-border"><i class="glyphicon glyphicon-globe"></i></span> -->
											<input type="text" name="phone" id="phone" class="form-control  " placeholder="<?=lang('reg.a25');?>" data-toggle="tooltip" data-placement="bottom"  value="<?=set_value('phone');?>" >
											<span class="errors"><?php echo form_error('phone'); ?></span>
											<span id="error-phone" class="errors"></span>
										<!-- </div> -->
									</div>
								</div>
								<?php endif;?>

								<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('City') == 0): ?>
								<div class="col-md-3 padding-sm-x">
									<div class="form-group">
										<label class="control-label">
										<?php if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('City') == 0): ?>
										 <b class="text-danger">*</b>
										 <?php endif;?>
										 <?=lang('reg.a19');?>
										 </label>
										<!-- <div class="input-group"> -->
											<!-- <span class="input-group-addon bg-addon no-border"><i class="glyphicon glyphicon-map-marker"></i></span> -->
											<input type="text" name="city" id="city" class="form-control  " placeholder=" <?=lang('reg.a19');?>" data-toggle="tooltip" data-placement="bottom" value="<?=set_value('city');?>" >
											<span class="errors"><?php echo form_error('city'); ?></span>
										<!-- </div> -->
									</div>
								</div>
								<?php endif;?>

								<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('Address') == 0): ?>
								<div class="col-md-3 padding-sm-x">
									<label class="control-label">
										<?php if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Address') == 0): ?>
										 <b class="text-danger">*</b>
										 <?php endif;?>
										 <?=lang('reg.a20');?>
										 </label>
											<input type="text" name="address" id="address" class="form-control  " placeholder="<?=lang('reg.a20');?>" data-toggle="tooltip" data-placement="bottom" value="<?=set_value('address');?>">
											<span class="errors"><?php echo form_error('address'); ?></span>
								</div>
								<?php endif;?>

								<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('Zip Code') == 0): ?>
								<div class="col-md-3 padding-sm-x">
									<div class="form-group">
										<label class="control-label">
										<?php if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Zip Code') == 0): ?>
										 <b class="text-danger">*</b>
										 <?php endif;?>
										 <?=lang('reg.a21');?>
										 </label>
											<input type="text" class="form-control number_only "  name="zip" id="zip"  placeholder=" <?=lang('reg.a21');?>"  value="<?=set_value('zip');?>" data-toggle="tooltip" data-placement="bottom" title="" >
									</div>
								</div>
								<?php endif;?>

								<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('State') == 0): ?>
								<div class="col-md-3 padding-sm-x">
									<div class="form-group">
										<label class="control-label">
										<?php if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('State') == 0): ?>
										 <b class="text-danger">*</b>
										 <?php endif;?>
										 <?=lang('reg.a22');?>
										 </label>
											<input type="text"  name="state" id="state"  class="form-control  " placeholder="<?=lang('reg.a22');?>" data-toggle="tooltip" data-placement="bottom" value="<?=set_value('state');?>">
										</div>
								</div>
								<?php endif;?>

								<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('Country') == 0): ?>
								<div class="col-md-3 padding-sm-x">
									<div class="form-group">
										<label class="control-label">
										<?php if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Country') == 0): ?>
										 <b class="text-danger">*</b>
										 <?php endif;?>
										 <?=lang('reg.a23');?>
										 </label>
											<?php $country = set_value('country');?>
											<?php $selected_country = "China"?>
											<select class="form-control "  name="country" id="country" data-toggle="tooltip" data-placement="bottom" >
												<?php foreach (unserialize(COUNTRY_LIST) as $key): ?>

		                                         <option value="<?=$key?>" <?=($selected_country == $key) ? 'selected' : ''?>><?=lang('country.' . $key)?></option>

		                                      <?php endforeach;?>
											</select>
										<span class="errors"><?php echo form_error('country'); ?></span>
									</div>
								</div>
								<?php endif;?>

								<div class="col-md-3 padding-sm-x">
									<div class="form-group">
										<label class="control-label"><b class="text-danger">*</b><?=lang('reg.a36');?></label>
										<?php $mode_of_contact = set_value('mode_of_contact');?>
											<select class="form-control" name="mode_of_contact" id="mode_of_contact" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="">

											<option value="email" <?=($mode_of_contact == "email") ? "selected" : ""?> ><?=lang('reg.a37');?></option>
											<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('Phone') == 0): ?>
												<option value="phone" <?=($mode_of_contact == "phone") ? "selected" : ""?> ><?=lang('reg.a38');?></option>
											<?php endif;?>

											<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('Mobile Phone') == 0): ?>
												<option value="mobile" <?=($mode_of_contact == "mobile") ? "selected" : ""?> ><?=lang('reg.a39');?></option>
											<?php endif;?>

											<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('Instant Message 1') == 0 || $this->affiliate_manager->checkRegisteredFieldsIfVisible('Instant Message 2') == 0): ?>
												<option value="im" <?=($mode_of_contact == "im") ? "selected" : ""?> ><?=lang('reg.a40');?></option>
											<?php endif;?>

											</select>
											<span class="errors"><?php echo form_error('mode_of_contact'); ?></span>
											<span id ="error-mode_of_contact"class="errors"></span>
									</div>
								</div>

								<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('Website') == 0): ?>
								<div class="col-md-3 padding-sm-x">
									<div class="form-group">
										<label class="control-label">
										<?php if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Website') == 0): ?>
										 <b class="text-danger">*</b>
										 <?php endif;?>
										 <?=lang('reg.a41');?>
										 </label>
											<input type="text" name="website" id="website"  class="form-control  " placeholder="<?=lang('reg.a41');?>" data-toggle="tooltip" data-placement="bottom" value="<?=set_value('website');?>">
											<span class="errors"><?php echo form_error('website'); ?></span>
									  </div>
								</div>
								<?php endif;?>

								<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('Instant Message 1') == 0): ?>
								<div class="col-md-3 padding-sm-x">
									<div class="form-group">
										<label class="control-label">
										<?php if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Instant Message 1') == 0): ?>
										 <b class="text-danger">*</b>
										 <?php endif;?>
										 <?=lang('reg.a26');?>
										 </label>
											<select class="form-control"  name="imtype1" id="imtype1"  data-toggle="tooltip" data-placement="bottom" title=""  >
												<option value=""><?=lang('reg.a43');?></option>
												<option value="QQ" <?=(set_value('imtype1') == "QQ") ? 'selected' : ''?> ><?=lang('reg.a27');?></option>
												<option value="Skype" <?=(set_value('imtype1') == "Skype") ? 'selected' : ''?> ><?=lang('reg.a28');?></option>
												<option value="MSN"  <?=(set_value('imtype1') == "MSN") ? 'selected' : ''?>><?=lang('reg.a29');?></option>
											</select>
											<span class="errors"><?php echo form_error('imtype1'); ?></span>
											<span id="error-imtype1" class="errors"></span>
									</div>
								</div>
								<div class="col-md-3 padding-sm-x">
									<div class="form-group">
										<label class="control-label">
										<?php if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Instant Message 1') == 0): ?>
										 <b class="text-danger">*</b>
										 <?php endif;?>
										 <?=lang('reg.a30');?>
										 </label>
											<input type="text" name="im1" id="im1" class="form-control" placeholder="<?=lang('reg.a30');?>" data-toggle="tooltip" data-placement="bottom" value="<?=set_value('im1');?>" <?=(set_value('imtype1') == null) ? 'readonly' : ''?> >
											<span class="errors"><?php echo form_error('im1'); ?></span>
											<span id="error-im1" class="errors"></span>
									</div>
								</div>
								<?php endif;?>

								<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('Instant Message 1') == 0): ?>
								<div class="col-md-3 padding-sm-x">
									<div class="form-group">
										<label class="control-label">
										<?php if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Instant Message 2') == 0): ?>
										 <b class="text-danger">*</b>
										 <?php endif;?>
										 <?=lang('reg.a31');?>
										 </label>
											<select class="form-control " name="imtype2" id="imtype2" data-toggle="tooltip" data-placement="bottom"  title="<?=lang('reg.a49');?>" >
												<option value=""><?=lang('reg.a44');?></option>
												<option value="QQ" <?=(set_value('imtype2') == "QQ") ? 'selected' : ''?> ><?=lang('reg.a32');?></option>
												<option value="Skype" <?=(set_value('imtype2') == "Skype") ? 'selected' : ''?> ><?=lang('reg.a33');?></option>
												<option value="MSN"  <?=(set_value('imtype2') == "MSN") ? 'selected' : ''?>><?=lang('reg.a34');?></option>
											</select>
											<span class="errors"><?php echo form_error('imtype2'); ?></span>
									</div>
								</div>
								<div class="col-md-3 padding-sm-x">
									<div class="form-group">
									<label class="control-label">
										<?php if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Instant Message 2') == 0): ?>
										 <b class="text-danger">*</b>
										 <?php endif;?>
										 <?=lang('reg.a35');?>
										 </label>
											<input type="text" name="im2" id="im2"  class="form-control" maxlength="50"  placeholder="<?=lang('reg.a35');?>" value="<?=set_value('im2');?>" <?=(set_value('imtype2') == null) ? 'readonly' : ''?> data-toggle="tooltip" >
											<span class="errors"><?php echo form_error('im2'); ?></span>
											<span id="error-im2" class="errors"></span>
									</div>
								</div>
								<?php endif;?>


								<?php if ($this->affiliate_manager->checkRegisteredFieldsIfVisible('Language') == 0): ?>
								<div class="col-md-3 padding-sm-x">
									<div class="form-group">
										<label class="control-label">
										<?php if ($this->affiliate_manager->checkRegisteredFieldsIfRequired('Language') == 0): ?>
										 <b class="text-danger">*</b>
										 <?php endif;?>
										 <?=lang('ban.lang');?>
										 </label>
										<select class="form-control  " data-toggle="tooltip" name="language" id="language" data-placement="bottom">
											<option value="English"<?php echo ($this->session->userdata('afflang') == '1') ? ' selected="selected"' : ''; ?>>English</option>
					        				<option value="Chinese"<?php echo ($this->session->userdata('afflang') == '2') ? ' selected="selected"' : ''; ?>>中文</option>
										</select>
										 <span class="errors" id="error-language"
										 ><?php echo form_error('language'); ?></span>
									</div>
								</div>
								<?php endif;?>



							</div>
						</form>
					</div>
				</div>
				<div class="col-md-12 sep" style="margin-top:10px;">
					<center>
						<button class="btn btn--primary" id="submit" type="submit"><?=lang('reg.a46');?></button>
						<button id="cancel"  class="btn btn-default" link="<?=site_url('affiliate')?>" type="submit"><?=lang('lang.cancel');?></button>
					</center>
				</div>

				<p class="forgot-pass">
					aff@webet88.com &copy; 2016 WeBet88.com
				</p>
			</div>
		</div>
	</div>


<script>

$(document).ready(function(){


	$('#imtype2').tooltip({
		placement : "top"

	});
	$('#imtype1').tooltip({
		placement : "top"

	});
	$('#username').tooltip({
		placement : "top"

	});
	$('#password').tooltip({
		placement : "top"

	});
	$('#confirm_password').tooltip({
		placement : "top"

	});
	$('#email').tooltip({
		placement : "top"

	});
	$('#mode_of_contact').tooltip({
		placement : "top"

	});






var  IS_USERNAME_EXIST_URL = '<?php echo site_url('affiliate/validateThruAjax') ?>',
      username = $("#username"),
      password = $("#password"),
      confirmPassword = $('#confirm_password'),
      email = $('#email'),
      birthday = $('#birthday'),
      birthdayCal = $('.calendar-table'),
      modeOfContact =$('#mode_of_contact'),
      phone =$('#phone'),
      mobile =$('#mobile'),
      imtype1 =$('#imtype1'),
      imtype2 =$('#imtype2'),
      im1 = $('#im1'),
      im2 = $('#im2'),
      submit = $('#submit'),
      cancel = $('#cancel'),
      affRegisterForm =$('#affiliate-register-form'),
      USERNAME_LABEL = "<?=lang('reg.03')?>",
      PASSWORD_LABEL = "<?=lang('reg.05')?>",
      CONFIRM_PASSWORD_LABEL = "<?=lang('reg.07')?>",
      EMAIL_LABEL = "<?=lang('aff.al11')?>",
      BIRTHDAY_LABEL = "<?=lang('aff.ai04')?>",
      PREFERRED_MODE_OF_CONTACT = "<?='aff.ai20'?>",
      PHONE_LABEL ="<?=lang('aff.ai15')?>",
      MOBILE_PHONE_LABEL ="<?=lang('aff.ai14')?>",
      IMTYPE1_LABEL ="<?=lang('aff.ai16')?>",
      IMTYPE2_LABEL ="<?=lang('aff.ai18')?>",
      IM1_LABEL ="<?=lang('aff.ai17')?>",
      IM2_LABEL ="<?=lang('aff.ai19')?>",
      formOk =false,
      birthdayOk =false;
      emailOk = false;
      usernameOk = false;
      currentPreferredMode='',
      currentIm1typeVal ='',
      currentIm2typeVal ='',
      error =[];


// imtype2.prop('disabled', true);
 birthday .val("");


cancel.click(function(){
  var URL =$(this).attr('link');
	location.href=URL;
});

username.blur(function(){
  if (requiredCheck($(this).val(),'username',USERNAME_LABEL)){
      validateThruAjax($(this).val(),'username',USERNAME_LABEL);
   }
});
password.blur(function(){
  if (requiredCheck($(this).val(),'password',PASSWORD_LABEL)){
 	{
	 	if(checkPassword($(this).val(),'password',PASSWORD_LABEL)){
	 		if(confirmPassword.val() != ""){
	 			checkPasswordMatch(confirmPassword.val(),'confirm_password',CONFIRM_PASSWORD_LABEL);
	 		}
	 	}
 	}
}
});
confirmPassword.blur(function(){
  if (requiredCheck($(this).val(),'confirm_password',CONFIRM_PASSWORD_LABEL)){
  	checkPasswordMatch($(this).val(),'confirm_password',CONFIRM_PASSWORD_LABEL);
 }
});

email.blur(function(){
  if (requiredCheck($(this).val(),'email',EMAIL_LABEL)){
     validateThruAjax($(this).val(),'email',EMAIL_LABEL)

   }
 });



// if preferred mode active in selecting then remove error if phone is not blank
phone.blur(function(){
	if(currentPreferredMode == 'phone'){
     if(requiredCheck($(this).val(),'phone',PHONE_LABEL)){
     	 removeErrorOnField('mode_of_contact');
     	 removeErrorItem('mode_of_contact');
     }

	}

});
// if preferred mode active in selecting then remove error if mobile is not blank
mobile.blur(function(){
	if(currentPreferredMode == 'mobile'){
     if(requiredCheck($(this).val(),'mobile',MOBILE_PHONE_LABEL)){
     	removeErrorOnField('mode_of_contact');
     	removeErrorItem('mode_of_contact');
         removeErrorItem('mobile');
     }

	}

});



imtype1.change(function(){

removeErrorOnField('imtype1');
removeErrorItem('imtype1');

if($(this).val() ==="QQ"){
	emptyInput('im1');
	// changeInputType("number","im1");
	requiredCheck(im1.val(),'im1',IM1_LABEL);

	currentIm1typeVal = "QQ";

	im1.prop('disabled', false);
	im1.prop('readonly', false);
	im1.focus();

}else if($(this).val() === "Skype"){
	emptyInput('im1');
	// changeInputType("text","im1");
	requiredCheck(im1.val(),'im1',IM1_LABEL);
	currentIm1typeVal = "Skype";

	im1.prop('disabled', false);
	im1.prop('readonly', false);
	im1.focus();

}else if($(this).val() === ""){
	utils.safelog('currentPreferredMode:'+currentPreferredMode);
    if(currentPreferredMode == "im"){
	    removeErrorOnField('mode_of_contact');
	    removeErrorOnField('email');
	    removeErrorOnField('phone');
	    removeErrorOnField('mobile');
	    removeErrorItem('imtype1');
	    removeErrorItem('mode_of_contact');
	    removeErrorItem('mobile');
	    validateThruAjax(modeOfContact.val(),'mode_of_contact',PREFERRED_MODE_OF_CONTACT);
	 	requiredCheck(imtype1.val() ,'imtype1',IMTYPE1_LABEL);
	 	requiredCheck(im1.val(),'im1',IM1_LABEL);

	    emptyInput('im1');
	    removeErrorItem('phone');
	    removeErrorItem('mobile');
	    // imtype2.prop('disabled',true);
	    removeErrorOnField('im2');
	    // im2.prop('disabled',true)
	    im1.attr('readonly', false);
	    im1.focus();
    }else{
    	 emptyInput('im1');
    	 removeErrorOnField('im1');
    	 removeErrorItem('im1');
		im1.prop('disabled', true);
		im1.prop('readonly', true);
    }

}else{
	emptyInput('im1');
	// changeInputType("text","im1");
	im1.prop('disabled', false);
	im1.prop('readonly', false);
	im1.focus();
}

});

im1.blur(function(){

	// utils.safelog("im1:"+$(this).val());
	// utils.safelog(requiredCheck($(this).val(),'im1',IM1_LABEL));

	// if(!isDisabled('im1')){
		requiredCheck($(this).val(),'im1',IM1_LABEL);
	// }

	// if(currentIm1typeVal === "Skype"){
	// 	// if(requiredCheck(imtype1.val(),'imtype1',IMTYPE1_LABEL)){
	// 	     if(requiredCheck($(this).val(),'im1',IM1_LABEL)){
	// 	       // checkInputIfChineseChar($(this).val(),'im1',IM1_LABEL);
	// 	       removeErrorOnField('mode_of_contact');
	// 	       removeErrorItem('mode_of_contact');
	// 	       // imtype2.prop('disabled',false);
	// 	      }
	//     // }
	// }else{
	// 	// if(requiredCheck(imtype1.val(),'imtype1',IMTYPE1_LABEL)){
	// 	     if(requiredCheck($(this).val(),'im1',IM1_LABEL)){
	// 	       removeErrorOnField('mode_of_contact');
	// 	       removeErrorItem('mode_of_contact');
	// 	        // imtype2.prop('disabled',false);
	// 	      }else{
	// 	      	// imtype2.prop('disabled',true);
	// 	      }
	//     // }
	// }


});

imtype2.change(function(){

if($(this).val() ==="QQ"){
	emptyInput('im2');
	// changeInputType("number","im2");
	requiredCheck(im2.val(),'im2',IM2_LABEL);
	currentIm2typeVal = "QQ";
	im2.prop('disabled', false);
	im2.prop('readonly', false);
	im2.focus();
}else if($(this).val() === "Skype"){
	emptyInput('im2');
	// changeInputType("text","im2");
	requiredCheck(im2.val(),'im2',IM2_LABEL);
	currentIm2typeVal = "Skype";
	im2.prop('disabled', false);
	im2.prop('readonly', false);
	im2.focus();

}else if($(this).val() === "MSN"){ alert()
	emptyInput('im2');
	// changeInputType("text","im2");
	requiredCheck(im2.val(),'im2',IM2_LABEL);
	currentIm2typeVal = "MSN";
	im2.prop('disabled', false);
	im2.prop('readonly', false);
	im2.focus();

}else if($(this).val() === ""){
    removeErrorOnField('im2');
    removeErrorOnField('mode_of_contact');
    removeErrorItem('im2');
    emptyInput('im2');
	im2.prop('disabled', true);
	im2.prop('readonly', true);
}else{
	// changeInputType("text","im2");
	im2.prop('disabled', false);
	im2.prop('readonly', false);
	im2.focus();
}

});

im2.blur(function(){

	// if(currentIm2typeVal === "Skype"){

			// if(requiredCheck($(this).val(),'im2',IM2_LABEL)){
				// checkInputIfChineseChar($(this).val(),'im2',IM2_LABEL);
			// }


	// }else{
	// if(!isDisabled('im2')){
		requiredCheck($(this).val(),'im2',IM2_LABEL);
	// }
	// }



});



<?php if ($birthdayRequired): ?>

birthday.blur(function() {
     if (requiredCheck(birthday.val(),'birthday',BIRTHDAY_LABEL)){
      validateThruAjax(birthday.val(),'birthday',BIRTHDAY_LABEL);
     }
 });

birthday.on('apply.daterangepicker', function(ev, picker) {
     if (requiredCheck(birthday.val(),'birthday',BIRTHDAY_LABEL)){
      validateThruAjax(birthday.val(),'birthday',BIRTHDAY_LABEL);
     }
 });
<?php endif;?>

 modeOfContact.change(function(){

 	if($(this).val() ==  "phone"){
	 	   validateThruAjax($(this).val(),'mode_of_contact',PREFERRED_MODE_OF_CONTACT);

	 	   if(!requiredCheck(phone.val(),'phone',PHONE_LABEL)){
		 	 	phone.focus();
		 	}else{
		 	    removeErrorOnField('mode_of_contact');
		 	}


	 	   removeErrorOnField('mobile');
	 	   removeErrorOnField('im');
	 	   removeErrorOnField('imtype1');
		   removeErrorItem('mobile');
		   currentPreferredMode='phone';
		   phone.focus();


	 }
	 if($(this).val() ==  "mobile"){
	 	 validateThruAjax($(this).val(),'mode_of_contact',PREFERRED_MODE_OF_CONTACT);

	 	 if(!requiredCheck(mobile.val(),'mobile',MOBILE_PHONE_LABEL)) {
	 	 	mobile.focus();
	 	 }else{
	 	 	removeErrorOnField('mode_of_contact');
	 	 }
	 	  removeErrorOnField('phone');
	 	  removeErrorOnField('im');
	 	  removeErrorOnField('imtype1');
		  removeErrorOnField('mode_of_contact');
		  removeErrorItem('phone');
		  removeErrorItem('mobile');
	 	  removeErrorItem('imtype1');
	 	  removeErrorItem('im1');
	 	  currentPreferredMode='mobile';
	 	  mobile.focus();
     }

      if($(this).val() ==  "im"){
      		removeErrorOnField('mode_of_contact');
     // 	  if(requiredCheck(imtype1.val() ,'imtype1',IMTYPE1_LABEL)){
	 	  // 	if(requiredCheck(im1.val(),'im1',IM1_LABEL)){
	 	  // 		removeErrorOnField('mode_of_contact');
	 	  // 		removeErrorItem('mode_of_contact');
	 	  // 		removeErrorOnField('mobile')
	 	  // 		im1.focus();
	 	  // 	}
	 	  // }else{
	 	  // 	imtype1.focus();
	 	  // }


	 	  removeErrorOnField('phone');
	 	  removeErrorOnField('mobile');
	 	  removeErrorItem('phone');
	 	  removeErrorItem('mobile');
	 	  currentPreferredMode='im';
	 	  //imtype1.focus();

     }
     	if($(this).val() ==  "email"){

         if(!requiredCheck(email.val() ,'email',EMAIL_LABEL)){
         	email.focus();
         }
	 	   removeErrorOnField('mode_of_contact');
	 	   removeErrorOnField('phone');
	 	   removeErrorOnField('mobile');
	 	   removeErrorOnField('im');
	 	   removeErrorOnField('imtype1');
		   removeErrorItem('mobile');
		   currentPreferredMode='email';


	 }
	 // if($(this).val() !=  "im"){
	 // 	 removeErrorOnField('im');
	 // 	 removeErrorItem('im1');
	 // }


 });




submit.on('click',function(){

	requiredCheck(confirmPassword.val(),'confirm_password',CONFIRM_PASSWORD_LABEL);
	requiredCheck(password.val(),'password',PASSWORD_LABEL);


	if(requiredCheck(email.val(),'email',EMAIL_LABEL)){
		validateThruAjax(email.val(),'email',EMAIL_LABEL)
    }
<?php if ($birthdayRequired) {?>
    if(requiredCheck(birthday.val(),'birthday',BIRTHDAY_LABEL)){
		validateThruAjax(birthday.val(),'birthday',BIRTHDAY_LABEL)
    }
<?php }?>
    if(requiredCheck(username.val(),'username',USERNAME_LABEL)){
		validateThruAjax(username.val(),'username',USERNAME_LABEL)
    }

    submitForm();

});



function submitForm(){

emailOk=emailOk && email.val()!='';
usernameOk=usernameOk && username.val()!='';
passwordOk=password.val()!='' &&  confirmPassword.val()!='';

<?php if ($birthdayRequired) {?>
birthdayOk=birthdayOk && birthday.val()!='';
<?php } else {?>
birthdayOk=true;
<?php }?>

if(emailOk && passwordOk && usernameOk){


	var errorLength = error.length;

	if(errorLength > 0){
		ableSubmitButton();
		return false;
	}else{

		affRegisterForm.submit();
		disableSubmitButton();

	}

}else{
	return false;
}


}

function validateThruAjax(fieldVal,id,label){
	var data=null;
	if(id == "username"){
		data = {username:fieldVal};
	}
	if(id =="email"){
		data ={ email:fieldVal};
	}
	if(id =="birthday"){
		data ={ birthday:fieldVal};
	}
	if(id =="mode_of_contact"){
		data ={ mode_of_contact:fieldVal , phone:phone.val(), mobile:mobile.val()};
	}
	if(id =="im"){
		data ={ mode_of_contact:fieldVal , phone:phone.val(), mobile:mobile.val(),imtype1:imtype1.val()};
	}

if(data){

 $.ajax({
        url : IS_USERNAME_EXIST_URL,
        type : 'POST',
        data : data,
        dataType : "json",
        cache : false,
      }).done(function (data) {
      	utils.safelog(id);
      	utils.safelog(data);
        if (data.status == "success") {
        	removeErrorItem(id);
   	    	removeErrorOnField(id);
   	    	if(id == 'birthday'){
   	    		birthdayOk = true;
   	    	}
   	    	if(id == 'email'){
   	    		emailOk = true;
   	    	}
   	    	if(id == 'username'){
   	    		usernameOk = true;
   	    	}
        }
        if (data.status == "error") {
        	var message = data.msg;
        	showErrorOnField(id,message);
		    addErrorItem(id);
		    if(id == 'birthday'){
   	    		birthdayOk = false;
   	    	}
   	    	if(id == 'email'){
   	    		emailOk = false;
   	    	}
   	    	if(id == 'username'){
   	    		usernameOk = false;
   	    	}
        }
      }).fail(function (jqXHR, textStatus) {
        /*Note: this is for session timeout,if the session is out because this is ajax, eventually it will go to log in page*/
         // location.reload();
      });
}
}

function checkInputIfChineseChar(fieldVal,id,label){

var message = "Cannot use Chinese Character. ";

var pattern = /^[a-zA-Z0-9!@#$%^&*()_]+$/;
var result = pattern.test(fieldVal);

if (result){
	 removeErrorItem(id);
	 removeErrorOnField(id);
    return true;
}else{

   showErrorOnField(id,message);
   addErrorItem(id);
   return false;

}

}

function isDisabled(element) {

	var isDisabled = $('#'+element).is(':disabled');
	if (isDisabled) {
		return false;
	} else {
		return true;
	}

}


function emptyInput(id){
	$("#"+id).val("");
}
function changeInputType(type,id){
	$("#"+id).attr('type',type);
}

function checkPassword(fieldVal,id,label){
   var message = label+"  field must be at least 6 - 12 characters in length.",
   fieldValLength = fieldVal.length;

   if( (fieldValLength >= 6)  && (fieldValLength <= 12)){
   		removeErrorItem(id);
   		removeErrorOnField(id);
		return true;
   }else{
   		showErrorOnField(id,message);
		addErrorItem(id);
		return false;
   }

}

function checkPasswordMatch(fieldVal,id,label){
	 var message = label+"  didn't match";
	 if(fieldVal != password.val() ){
	 	showErrorOnField(id,message);
		addErrorItem(id);
		return false;
	}else{
		removeErrorItem(id);
   		removeErrorOnField(id);
		return true;
	}
}



function requiredCheck(fieldVal,id,label){
	var message = label+" is required";
	if(!fieldVal && (fieldVal == "")){
		showErrorOnField(id,message)
		addErrorItem(id);
		return false;
	}else{
		removeErrorOnField(id);
		removeErrorItem(id);

		return true;
	}
}

function showErrorOnField(id,message){
	$('#error-'+id).html(message);
}

function removeErrorOnField(id){
	$('#error-'+id).html("");
}

 function removeErrorItem(item){

    var i = error.indexOf(item);
		if(i != -1) {
			error.splice(i, 1);
		}
	// console.log(error)
 }

 function addErrorItem(item){
 	if(jQuery.inArray(item, error) == -1){
 			error.push(item);
 			// console.log(error);
 			// console.log(error.length)
 	}

 }

function disableSubmitButton(){
	$("#affRegisterForm :input").attr("disabled", true);
	submit.prop('disabled', true);
	cancel.prop('disabled', true);
}
function ableSubmitButton(){
	cancel.prop('disabled', false);
}



})//End document

</script>

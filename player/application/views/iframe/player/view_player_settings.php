<form action="<?php echo site_url('/iframe_module/postEditPlayer'); ?>" method="post" role="form" class="form-horizontal" enctype="multipart/form-data">

	<div class="form-group">
		<label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;"><?=lang('pi.1');?></label>
		<div class="custom-sm-7 custom-leftside custom-pdl-15">
			<input type="text" class="form-control"  name="name" id="name" class="form-control" value="<?=ucfirst($player['firstName'])?>" placeholder="<?=lang('pi.1');?>" <?php echo empty(trim($player['firstName'])) ? "" : "readonly" ?>>
			<?php echo form_error('name', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
		</div>
	</div>

	<div class="form-group">
		<label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;"><?=lang('reg.fields.lastName');?></label>
		<div class="custom-sm-7 custom-leftside custom-pdl-15">
			<input type="text" class="form-control" name="lastname" id="lastname" class="form-control" value="<?=ucfirst($player['lastName'])?>" placeholder="<?=lang('reg.fields.lastName');?>" <?php echo empty(trim($player['lastName'])) ? "" : "readonly" ?>>
			<?php echo form_error('name', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
		</div>
	</div>

	<div class="form-group">
		<label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;"><?=lang('pi.2');?></label>
		<div class="custom-sm-7 custom-leftside custom-pdl-15">
		  <div class="input-group">
			<span class="input-group-addon"><?=$default_prefix_for_username?></span>
			<input type="text" name="username" id="username" class="form-control" value="<?=$player['username']?>" placeholder="Username" readonly>
			<?php echo form_error('username', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
		  </div>
		</div>
	</div>

	<div class="form-group">
		<label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;"><?=lang('pi.3');?></label>
		<div class="custom-sm-7 custom-leftside custom-pdl-15">
			<input type="text" name="currency" id="currency" class="form-control" value="<?=$player['currency']?>" placeholder="Currency" readonly>
			<?php echo form_error('currency', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
		</div>
	</div>

	<div class="form-group">
		<label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;"><?=lang('reg.44');?></label>
		<div class="custom-sm-7 custom-leftside custom-pdl-15">
			<input type="text" name="currency" id="currency" class="form-control" value="<?=$player['invitationCode']?>" readonly>
		</div>
	</div>

	<div class="form-group fields">
		<label for="language" class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;">
			<?=lang('pi.4');?>
			<?php if ($this->player_functions->checkRegisteredFieldsIfRequired('Language') == 0) {?>
				<i style="color:#ff6666;">*</i>
			<?php }?>
		</label>
		<div class="custom-sm-7 custom-leftside custom-pdl-15">
			<select name="language" id="language" class="form-control" <?php echo $disabled_edit_player_info ? "disabled" : "" ?>>
				<option value=""><?=lang('pi.18');?></option>
				<option value="Chinese" <?php echo set_select('language', 'Chinese'); ?> <?=$player['language'] == 'Chinese' ? 'selected' : ''?>><?=lang('reg.27');?></option>
				<option value="English" <?php echo set_select('language', 'English'); ?> <?=$player['language'] == 'English' ? 'selected' : ''?>>English</option>
			</select>
			<?php echo form_error('language', '<span class="help-block errors" style="color:#ff6666;">', '</span>'); ?>
		</div>
	</div>

	<?php
		$showSMSField = !$this->utils->getConfig('disabled_sms') && ($this->player_functions->checkRegisteredFieldsIfVisible('SMS Verification Code') == 0);
		$isPhoneVerified = $player['verified_phone'];
	?>
	<div class="form-group">
		<label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" for="contact_number" style="text-align: left;">
			<?=lang('pi.5');?>
			<?php if ($this->player_functions->checkRegisteredFieldsIfRequired('Contact Number') == 0) {?>
				<i style="color:#ff6666;">*</i>
			<?php }?>
		</label>
		<div class="custom-sm-7 custom-leftside custom-pdl-15">
			<span class="form-horizontal control-label" style="float:left;"><?=$player['contactNumber']?></span>
			<input type="hidden" id="contact_number" name="contact_number" value="<?=$player['contactNumber']?>" />
			<?php if ($showSMSField): ?>
				<?php if ($isPhoneVerified): ?>
				<label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" for="contact_number" style="text-align:left"><?php echo lang('Verified'); ?></label>
				<?php else: ?>
				<input type="button" id="send_sms_verification" class="btn btn-info" value="<?php echo lang('Send SMS'); ?>" style="float:left;  margin-left: 10px" />
				<input type="text" name="sms_verification_code" id="sms_verification" class="form-control"  data-toggle="tooltip"
					value="<?php echo set_value('sms_verification_code'); ?>" maxlength="6"
					placeholder="<?php echo lang('Verification Code'); ?>" style="float: left; width: 15%; margin-left: 10px" />
				<button type="submit" class="btn btn-info" style="margin-left:10px; float:left"><?php echo lang('Verify'); ?></button>
				<span id="sms_verification_msg" class="form-horizontal control-label" style="color:#ff6666;display:none; float:left; margin-left: 10px"></span>
				<?php endif;?>
			<?php endif;?>
			<span class="help-block" style="clear:left; padding-top:5px"><?=lang('Please contact our Customer Service if you would like to update your contact number.');?></span>
		</div>
	</div>

	<div class="form-group">
		<label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;"><?=lang('pi.6');?></label>
		<div class="custom-sm-7 custom-leftside custom-pdl-15">
			<?php echo $player['email']; ?>
			<a href='<?php echo site_url("/iframe_module/resendEmail") ?>' class="btn btn-info btn-sm"><?php echo lang('btn.resend'); ?></a>
			<span class="help-block"><?=lang('notify.75');?></span>
		</div>
	</div>

	<div class="form-group">
		<label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;"><?=lang('pi.7');?></label>
		<div class="custom-sm-7 custom-leftside custom-pdl-15">
			<?php if (!empty(trim($player['gender']))): ?>
				<input type="text" name="gender" id="gender" class="form-control" value="<?=lang($player['gender'])?>" readonly>
			<?php else: ?>
				<input type="radio" name="gender" id="gender_m" value="Male" /><label for="gender_m"><?=lang('Male')?></label>
				<input type="radio" name="gender" id="gender_f" value="Female" ><label for="gender_f"><?=lang('Female')?></label>
			<?php endif;?>
			<?php echo form_error('gender', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
		</div>
	</div>

	<div class="form-group fields">
		<label for="im_type" class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;">
			<?=lang('pi.8');?>
			<?php if ($this->player_functions->checkRegisteredFieldsIfRequired('Instant Message 1') == 0) {?>
				<i style="color:#ff6666;">*</i>
			<?php }?>
		</label>

		<div class="custom-sm-7 custom-leftside custom-pdl-15">
			<select name="im_type" id="im_type" class="form-control" onchange="showDiv(this);" <?php echo $disabled_edit_player_info ? "disabled" : "" ?>>
				<option value=""><?=lang('pi.19');?></option>
				<option value="QQ" <?php echo set_select('im_type', 'QQ'); ?> <?=$player['imAccountType'] == 'QQ' ? 'selected' : ''?>>QQ</option>
				<option value="Skype" <?php echo set_select('im_type', 'Skype'); ?> <?=$player['imAccountType'] == 'Skype' ? 'selected' : ''?>>Skype</option>
				<option value="MSN" <?php echo set_select('im_type', 'MSN'); ?> <?=$player['imAccountType'] == 'MSN' ? 'selected' : ''?>>MSN</option>
			</select>
			<?php echo form_error('im_type', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
		</div>
	</div>

	<div class="form-group" id="hide_im" <?=($player['imAccount'] && $player['imAccountType']) || (set_value('im_type')) ? '' : 'style="display: none;"'?>>
		<label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;">
			<strong><span id="account_type"><?=$player['imAccountType'] ? $player['imAccountType'] : set_value('im_type')?></span></strong>
		</label>
		<div class="custom-sm-7 custom-leftside custom-pdl-15">
			<input type="text" name="im_account" id="im_account" class="form-control" value="<?=$player['imAccount']?>" <?php echo $disabled_edit_player_info ? "disabled" : "" ?>>
			<?php echo form_error('im_account', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
		</div>
	</div>

	<div class="form-group fields">
		<label for="im_type2" class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;">
			<?=lang('pi.9');?>
			<?php if ($this->player_functions->checkRegisteredFieldsIfRequired('Instant Message 2') == 0) {?>
				<i style="color:#ff6666;">*</i>
			<?php }?>
		</label>
		<div class="custom-sm-7 custom-leftside custom-pdl-15">
			<select name="im_type2" id="im_type2" class="form-control" onchange="showDiv2(this);" data-toggle="popover" <?php echo $disabled_edit_player_info ? "disabled" : "" ?>>
				<option value=""><?=lang('pi.19');?></option>
				<option value="QQ" <?php echo set_select('im_type2', 'QQ'); ?> <?=$player['imAccountType2'] == 'QQ' ? 'selected' : ''?>>QQ</option>
				<option value="Skype" <?php echo set_select('im_type2', 'Skype'); ?> <?=$player['imAccountType2'] == 'Skype' ? 'selected' : ''?>>Skype</option>
				<option value="MSN" <?php echo set_select('im_type2', 'MSN'); ?> <?=$player['imAccountType2'] == 'MSN' ? 'selected' : ''?>>MSN</option>
			</select>
			<?php echo form_error('im_type2', '<span class="help-block errors" style="color:#ff6666;">', '</span>'); ?>
		</div>
	</div>

	<div class="form-group" id="hide_im2" <?=($player['imAccount2'] && $player['imAccountType2']) || (set_value('im_type2')) ? '' : 'style="display: none;"'?>>
		<label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;">
			<strong><span id="account_type2"><?=$player['imAccountType2'] ? $player['imAccountType2'] : set_value('im_type2')?></span></strong>
		</label>
		<div class="custom-sm-7 custom-leftside custom-pdl-15">
			<input type="text" name="im_account2" id="im_account2" class="form-control" value="<?=$player['imAccount2']?>" <?php echo $disabled_edit_player_info ? "disabled" : "" ?>>
			<?php echo form_error('im_account2', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
		</div>
	</div>

	<?php if ( $this->utils->getConfig('european_address_format') ) : ?>
		<div class="form-group">
			<label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;"><?=lang('address_3');?></label>
			<div class="custom-sm-7 custom-leftside custom-pdl-15">
				<select name="resident_country" id="resident_country" class="form-control" <?php echo $disabled_edit_player_info ? "disabled" : "" ?>>
					<option value=""><?=lang('pi.20');?></option>
					<?php foreach (unserialize(COUNTRY_LIST) as $key) {?>
							<option value="<?=$key?>" <?=($player['residentCountry'] == $key) ? 'selected' : ''?> ><?=lang('country.' . $key)?></option>
					<?php }?>
				</select>
				<?php echo form_error('resident_country', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
			</div>
		</div>

		<div class="form-group">
			<label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;"><?=lang('address_2');?></label>
			<div class="custom-sm-7 custom-leftside custom-pdl-15">
				<input type="text" name="city" id="city" class="form-control letters_only" placeholder="<?=lang('eur.1');?>" value="<?=$player['city']?>" <?php echo $disabled_edit_player_info ? "disabled" : "" ?>>
				<?php echo form_error('city', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
			</div>
		</div>

		<div class="form-group">
			<label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;"><?=lang('address_1');?></label>
			<div class="custom-sm-7 custom-leftside custom-pdl-15">
				<input type="text" name="address" id="address" class="form-control" placeholder="<?=lang('eur.2');?>" data-toggle="popover" value="<?=$player['address']?>" <?php echo $disabled_edit_player_info ? "disabled" : "" ?>/>
				<?php echo form_error('address', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
			</div>
		</div>
	<?php else : ?>
		<div class="form-group">
			<label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;"><?=lang('pi.11');?></label>
			<div class="custom-sm-7 custom-leftside custom-pdl-15">
				<input type="text" name="address" id="address" class="form-control" data-toggle="popover" placeholder="<?=lang('pi.11');?>" value="<?=$player['address']?>" <?php echo $disabled_edit_player_info ? "disabled" : "" ?>/>
				<?php echo form_error('address', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
			</div>
		</div>

		<div class="form-group">
			<label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;"><?=lang('pi.12');?></label>
			<div class="custom-sm-7 custom-leftside custom-pdl-15">
				<input type="text" name="city" id="city" class="form-control letters_only" value="<?=$player['city']?>" placeholder="<?=lang('pi.12');?>" <?php echo $disabled_edit_player_info ? "disabled" : "" ?>>
				<?php echo form_error('city', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
			</div>
		</div>

		<div class="form-group">
			<label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;"><?=lang('a_reg.33');?></label>
			<div class="custom-sm-7 custom-leftside custom-pdl-15">
				<select name="resident_country" id="resident_country" class="form-control" <?php echo $disabled_edit_player_info ? "disabled" : "" ?>>
					<option value=""><?=lang('pi.20');?></option>
					<?php foreach (unserialize(COUNTRY_LIST) as $key) {?>
							<option value="<?=$key?>" <?=($player['residentCountry'] == $key) ? 'selected' : ''?> ><?=lang('country.' . $key)?></option>
					<?php }?>
				</select>
				<?php echo form_error('resident_country', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
			</div>
		</div>
	<?php endif; ?>
	<div class="form-group">
		<label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;"><?=lang('pi.10');?></label>
		<div class="custom-sm-7 custom-leftside custom-pdl-15">
			<select name="country" id="country" class="form-control" <?php echo $disabled_edit_player_info ? "disabled" : "" ?>>
				<option value=""><?=lang('pi.20');?></option>
				<?php foreach (unserialize(COUNTRY_LIST) as $key) {?>
						<option value="<?=$key?>" <?=($player['country'] == $key) ? 'selected' : ''?> ><?=lang('country.' . $key)?></option>
				<?php }?>
			</select>
			<?php echo form_error('country', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
		</div>
	</div>

	<div class="form-group">
		<label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;">
			<?=lang('pi.13');?>
			<?php if ($this->player_functions->checkRegisteredFieldsIfRequired('Nationality') == 0) {?>
				<i style="color:#ff6666;">*</i>
			<?php }?>
		</label>
		<div class="custom-sm-7 custom-leftside custom-pdl-15">
			<input type="text" name="citizenship" id="citizenship" class="form-control" value="<?=$player['citizenship']?>" <?php echo $disabled_edit_player_info ? "disabled" : "" ?>>
			<?php echo form_error('citizenship', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
		</div>
	</div>

	<div class="form-group">
		<label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;"><?=lang('reg.12');?></label>
		<div class="custom-sm-7 custom-leftside custom-pdl-15">
			<input type="text" name="birthdate" id="birthdate" class="form-control" value="<?=$player['birthdate']?>" placeholder="yyyy-mm-dd"
+									<?php echo empty(trim($player['birthdate'])) ? "" : "readonly" ?> <?php echo $disabled_edit_player_info ? "disabled" : "" ?>>
			<?php echo form_error('birthdate', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
		</div>
	</div>

	<div class="form-group">
		<label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;">
			<?=lang('pi.14');?>
			<?php if ($this->player_functions->checkRegisteredFieldsIfRequired('BirthPlace') == 0) {?>
				<i style="color:#ff6666;">*</i>
			<?php }?>
		</label>
		<div class="custom-sm-7 custom-leftside custom-pdl-15">
			<input type="text" name="birthplace" id="birthplace" class="form-control" value="<?=$player['birthplace']?>" <?php echo $disabled_edit_player_info ? "disabled" : "" ?>>
			<?php echo form_error('birthplace', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
		</div>
	</div>

	<div class="form-group">
		<label class="custom-sm-3 custom-pdl-15 custom-leftside control-label" style="text-align: left;"><?=lang('aff.ai51');?></label>
		<input type="hidden" name="banner_url" id="banner_url">
		
		<div class="custom-sm-7 custom-leftside custom-pdl-15">
			<input type="file" id="txtImage" name="txtImage[]" class="form-control" value="<?=set_value('txtImage');?>" accept="image/*" multiple>
		</div>
	</div>

	<hr class="style-two"/>
	<?php if (!$disabled_edit_player_info) {?>
	<div class="form-group">
		<center>
			<div class="custom-sm-2">
				<button type="submit" class="btn btn-primary btn-block"><?=lang('pi.17');?></button>
			</div>
		</center>
	</div>
	<?php }?>
	<a href="<?php echo site_url('iframe_module/iframe_viewCashier') ?>" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-circle-arrow-left"></span> <?=lang('button.back');?></a>
</form>

<script type="text/javascript">
$(function(){
	// Handling of SMS verification code
	var sendSmsVerification = function() {
		var mobileNumber = $('#contact_number').val();
		if(!mobileNumber || mobileNumber == '') {
			// we can assume mobile number exists
			return;
		}

		$('#send_sms_verification').attr('disabled',true);
		$('#sms_verification_msg').text('<?php echo lang('Please wait') . '...'; ?>');
		$('#sms_verification_msg').show();

		$.getJSON('<?php echo site_url('iframe_module/iframe_register_send_sms_verification'); ?>/' + mobileNumber, function(data){
			if(data.success) {
				$('#sms_verification_msg').text('<?php echo lang('SMS sent'); ?>');
			}
			else {
				$('#sms_verification_msg').text('<?php echo lang('SMS failed'); ?>');
			}
		}).always(function(){
			$('#send_sms_verification').attr('disabled',false);
		});

		setTimeout(function(){$('#sms_verification_msg').fadeOut('slow')}, 5000);
	}

	$('#send_sms_verification').click(sendSmsVerification);
});

</script>

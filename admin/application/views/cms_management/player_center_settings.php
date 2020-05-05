<?php
	$current_template = $this->utils->getOperatorSetting('player_center_template');
	$default_template = $this->utils->getConfig('new_player_center_default_template');
?>


<style type="text/css">
	form img {
		width: auto !important;
	}
</style>

<div class="panel panel-primary panel_main">

	<div class="panel-heading">
		<h4 class="panel-title"><i class="fa fa-cogs"></i> &nbsp;<?= lang('Player Center Settings') ?>
			<?php if($this->utils->getConfig('use_new_sbe_color')){?>
				<span class="pull-right">
					<a data-toggle="collapse" href="#main_panel" class="btn btn-info btn-xs" aria-expanded="true"></a>
				</span>
			<?php }else{?>
				<a href="#main_panel" data-toggle="collapse" class="pull-right"><i class="fa fa-caret-down"></i></a>
			<?php }?>
		</h4>
	</div>

	<div id="main_panel" class="panel-collapse collapse in ">

		<div class="panel-body">
			<form action="<?php echo site_url('cms_management/save_operator_settings'); ?>" method="POST">
				<table class="table table-hover table-striped table-bordered">
					<?php if($this->utils->isEnabledFeature('send_sms_after_registration')): ?>
						<tr><th class="col-md-12"><?=lang('SMS Registration Template')?></th></tr>
						<tr>
							<td><textarea name="sms_registration_template" class="input-sm col-md-10"><?php echo $this->utils->getOperatorSetting('sms_registration_template');?></textarea></td>
						</tr>
					<?php endif; ?>
					<tr><th><?=lang('Template')?></th></tr>
					<tr><td>
                        <select class="input-sm col-md-3" name="player_center_template" id="player_center_template">
                            <?php foreach($template_list as $template_name): ?>
                                <option value="<?=$template_name?>"><?=$template_name?></option>
                            <?php endforeach; ?>
                        </select>
						<label class="col-md-4" for="chkSetupNewPlayerCenter">
							<input type="checkbox" id="chkSetupNewPlayerCenter"> Setup New Player Center
						</label>

					</td></tr>
					<tr id="trPlayerCenterSetup" class="hidden">
						<td>
							<label><?=lang('Setup New Player Center')?> :</label>
							<div class="dropdown">
								<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<span id="mnuSetupType">Choose Setup Type</span>
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
									<li><a class="setup-type" data-type="auto" href="#">Automatic (Use Recommended Setup)</a></li>
									<li><a class="setup-type" data-type="manual" href="#">Manual Setup</a></li>
								</ul>
							</div>
							<input type="hidden" id="hdnSetupType" name="hdnSetupType" />
							<br><br>
							<button type="button" id="btnSetupNewPlayerCenter" class="btn btn-success btn-sm">Run Setup</button>
							<!-- <a href="/cms_management/setupNewPlayerCenter" id="btnSetupNewPlayerCenter" class="btn btn-success btn-xs">Run Setup</a> -->
						</td>
					</tr>
					<tr id="trSaveTempate">
						<td><input type="submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>" value="<?php echo lang('Save');?>"></td>
					</tr>
				</table>
			</form>

			<form action="<?php echo site_url('cms_management/save_cms_version'); ?>" method="POST">
				<table class="table table-hover table-striped table-bordered">
					<tr><th><?=lang('Cms Version')?></th></tr>
					<tr><td>
						<input type="text" class="input-sm col-md-4" name="cms_version" id="cms_version" value="<?=$cms_version?>">
					</td></tr>
					<tr>
						<td><input type="submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>" value="<?php echo lang('Save');?>"></td>
					</tr>
				</table>
			</form>

			<form action="<?php echo site_url('cms_management/save_custom_script'); ?>" method="POST">
				<table class="table table-hover table-striped table-bordered">
					<tr><th><?=lang('Custom Script : ')?></th></tr>
					<tr><td>
						<div class="form-group">
						  <textarea class="form-control" rows="5" id="<taCustomScript></taCustomScript>" name="taCustomScript"><?= $this->utils->getPlayerCenterCustomScript();?></textarea>
						</div>
					</td></tr>
					<tr>
						<td><input type="submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>" value="<?php echo lang('Save');?>"></td>
					</tr>
				</table>
			</form>

			<?php if(!empty($smsBalances)): ?>
				<table class="table table-hover table-striped table-bordered">
					<tr><th colspan="2"><?=lang('SMS API Balance')?></th></tr>
					<?php foreach($smsBalances as $apiName => $balanceString) : ?>
						<tr>
							<td class="col-md-4"><?=lang($apiName)?></td>
							<td class="col-md-8"><?=$balanceString?></td>
						</tr>
					<?php endforeach; ?>
				</table>
			<?php endif; ?>

<!--
Modification : Add set default language for player center
May 20, 2017
-->
<form action="<?php echo site_url('cms_management/setPlayerCenterDefaultLanguage'); ?>" method="POST">
	<table class="table table-hover table-striped table-bordered">
		<tr><th><?= lang('Language'); ?></th></tr>
		<tr>
			<td>
				<label class="col-md-5 col-lg-3">
					<input type="checkbox" name="chkForceToDefaultLanguage" id="chkForceToDefaultLanguage" value="1" <?= !empty($isForceToDefaultLanguage) ? "checked" : "" ?>>
					<?= lang('Force player center to default language') ?> <span class="glyphicon glyphicon-info-sign" aria-hidden="true"  data-toggle="tooltip" title="This will set the player center to use selected default language and will not allow the player to change it."></span>
				</label>
				<label class="col-md-5 col-lg-3">
					<input type="checkbox" name="chkRetainCurrentLanguage" id="chkRetainCurrentLanguage" value="1" <?= !empty($isRetainCurrentLanguage) ? "checked" : "" ?>>
					<?= lang('Retain player center to current language') ?></span>
				</label>
			</td>
		</tr>
		<tr>
			<td>
				<label class="col-sm-5 col-md-4 col-lg-2" for="chkLanguageDefault">
					<input type="radio" name="rdbLanguage" id="chkLanguageDefault" value="0" <?= $playerCenterLanguage == "0" ? "checked" : ""  ?>> <?=lang('use.default.lang')?>
				</label>
				<label class="col-sm-3 col-md-3 col-lg-1" for="chkLanguageEN">
					<input type="radio" name="rdbLanguage" id="chkLanguageEN" value="1" <?= $playerCenterLanguage == "1" ? "checked" : "" ?>>
					<img src="<?= $this->utils->imageUrl('en-icon.png'); ?>"  alt="<?=lang('English')?>"> <?=lang('English')?>
				</label>
				<label class="col-sm-3 col-md-3 col-lg-1" for="chkLanguageCN">
					<input type="radio" name="rdbLanguage" id="chkLanguageCN" value="2" <?= $playerCenterLanguage == "2" ? "checked" : "" ?>>
					<img src="<?= $this->utils->imageUrl('cn-icon.png'); ?>" alt="<?=lang('Chinese')?>"> <?=lang('Chinese')?>
				</label>
				<label class="col-sm-5 col-md-4 col-lg-1" for="chkLanguageID">
					<input type="radio" name="rdbLanguage" id="chkLanguageID" value="3" <?= $playerCenterLanguage == "3" ? "checked" : "" ?>>
					<img src="<?= $this->utils->imageUrl('id-icon.jpg'); ?>" alt="<?=lang('Indonesian')?>"> <?=lang('Indonesian')?>
				</label>
				<label class="col-sm-3 col-md-3 col-lg-1" for="chkLanguageVN">
					<input type="radio" name="rdbLanguage" id="chkLanguageVN" value="4" <?= $playerCenterLanguage == "4" ? "checked" : "" ?>>
					<img src="<?= $this->utils->imageUrl('vn-icon.jpg'); ?>" alt="<?=lang('Vietnamese')?>"> <?=lang('Vietnamese')?>
				</label>
				<label class="col-sm-3 col-md-3 col-lg-1" for="chkLanguageKR">
					<input type="radio" name="rdbLanguage" id="chkLanguageKR" value="5" <?= $playerCenterLanguage == "5" ? "checked" : "" ?>>
					<img src="<?= $this->utils->imageUrl('kr-icon.jpg'); ?>" alt="<?=lang('Korean')?>"> <?=lang('Korean')?>
				</label>
				<label class="col-sm-3 col-md-3 col-lg-1" for="chkLanguageTH">
					<input type="radio" name="rdbLanguage" id="chkLanguageTH" value="6" <?= $playerCenterLanguage == "6" ? "checked" : "" ?>>
					<img src="<?= $this->utils->imageUrl('th-icon.jpg'); ?>" alt="<?=lang('Thai')?>"> <?=lang('Thai')?>
				</label>
			</td>
		</tr>
		<tr>
			<td><button type="submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>"><?= lang('Save'); ?></button></td>
		</tr>
	</table>
</form>
<!-- End of modificaton -->

<!--
Modification : Add field to upload player logo
May 14, 2017
-->
<form id="frmUploadPlayerLogo" action="<?php echo site_url('cms_management/upload_player_logo'); ?>" method="POST" enctype="multipart/form-data">
	<table class="table table-hover table-striped table-bordered">
		<tr><th colspan="2"><?= lang('Logo'); ?></th></tr>
		<tr>
			<td colspan="2">
                <label for="setDefaultPlayerLogo" class="control-label col col-md-3">
                    <input type="radio" name="prefer_player_center_logo" id="setDefaultPlayerLogo" value="0" <?= ($prefer_player_center_logo === 0)  ? "checked" : "" ?> > <?= lang('Use default player logo.'); ?>
                </label>
                <label for="setUploadLogo" class="control-label col col-md-3">
                    <input type="radio" name="prefer_player_center_logo" id="setUploadLogo" value="1" <?= ($prefer_player_center_logo === 1) ? "checked" : "" ?> > <?= lang('Use Upload logo.'); ?>
                </label>
                <label for="setDefaultWWWLogo" class="control-label col col-md-3">
                    <input type="radio" name="prefer_player_center_logo" id="setDefaultWWWLogo" value="2" <?= ($prefer_player_center_logo === 2) ? "checked" : "" ?> > <?= lang('Use www logo.'); ?>
                </label>
            </td>
		</tr>
		<tr>
			<td class="col-md-4">
				<?=lang('Upload File')?> :
				<input type="file" name="fileToUpload[]" id="fileToUpload" accept="image/*" class="form-control input-sm">
			</td>
            <td class="col-md-8" rowspan="2"><?=lang('Current player center logo')?> : <br/><br/>
                <img src="<?= $this->utils->getPlayerCenterLogoURL() ?>" alt="">
            </td>
		</tr>
		<tr>
			<td class="col-md-4"><button type="submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>"><?= lang('Save'); ?></button></td>
		</tr>
	</table>
</form>
<!-- End of modificaton -->

<!--
Modification : Add field to upload player favicon
May 14, 2017
-->
<form id="frmUploadPlayerFavicon" action="<?php echo site_url('cms_management/upload_player_favicon'); ?>" method="POST" enctype="multipart/form-data">
	<table class="table table-hover table-striped table-bordered">
		<tr><th colspan="2"><?= lang('Favicon'); ?></th></tr>
		<tr>
			<td class="col-md-4">
				<input type="checkbox" name="setDefaultPlayerFavicon" id="setDefaultPlayerFavicon" <?= empty($this->utils->getPlayerCenterFavicon()) ? "checked" : "" ?> >
				<label for="setDefaultPlayerFavicon" class="control-label"> <?= lang('Use default player favicon.'); ?></label>
			</td>
			<td class="col-md-8" rowspan="3"><?=lang('Current player center favicon')?> : <br/><br/>
				<img src="<?= $this->utils->getPlayerCenterFaviconURL() ?>" alt="">
			</td>
		</tr>
		<tr>
			<td class="col-md-4">
				<?=lang('Upload File')?> :
				<input type="file" name="fileToUpload[]" id="fileToUpload" accept="image/*" class="form-control input-sm">
			</td>
		</tr>
		<tr>
			<td class="col-md-4"><button type="submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>"><?= lang('Save'); ?></button></td>
		</tr>
	</table>
</form>
<!-- End of modificaton -->

<!-- player center title -->
<form action="<?php echo site_url('cms_management/save_player_center_title'); ?>" method="POST">
	<table class="table table-hover table-striped table-bordered">
		<tr><th><?=lang('Title')?></th></tr>
		<tr><td>
			<textarea name="player_center_title" id="player_center_title" class="input-sm col-sm-12 col-md-12"><?php echo $this->utils->getPlayertitle();?></textarea>
			<!--<input type="text" class="input-sm col-md-4" name="player_center_title" id="player_center_title" value="<?php echo $this->utils->getPlayertitle();?>">-->
		</td></tr>
        <tr><th><?=lang('Mobile Title Display Style')?></th></tr>
        <tr>
            <td>
                <label class="col-md-4" for="player_center_mobile_header_title_style_method_1">
                    <input type="radio" name="player_center_mobile_header_title_style" id="player_center_mobile_header_title_style_method_1" value="1" <?= $player_center_mobile_header_title_style === PLAYER_CENTER_MOBILE_HEADER_STYLE_LOGO_AND_TEXT ? "checked" : "" ?>> <?=lang('operator_settings.player_center_mobile_header_title_style.1')?>
                </label>

                <label class="col-md-4" for="player_center_mobile_header_title_style_method_2">
                    <input type="radio" name="player_center_mobile_header_title_style" id="player_center_mobile_header_title_style_method_2" value="2" <?= $player_center_mobile_header_title_style === PLAYER_CENTER_MOBILE_HEADER_STYLE_ALL_LOGO ? "checked" : "" ?>> <?=lang('operator_settings.player_center_mobile_header_title_style.2')?>
                </label>
            </td>
        </tr>
		<tr>
			<td><input type="submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>" value="<?php echo lang('Save');?>"></td>
		</tr>
	</table>
</form>

<!-- player center title -->
<form action="<?php echo site_url('cms_management/save_withdrawal_verification_type'); ?>" method="POST">
	<table class="table table-hover table-striped table-bordered">
		<tr><th><?=lang('Withdrawal Verification : ')?></th></tr>
		<tr><td>
			<label class="col-md-1" for="rdbWithdrawTypeOff">
				<input type="radio" name="rdbWithdrawType" id="rdbWithdrawTypeOff" value="off" <?= $withdrawalVerification === 'off' ? "checked" : "" ?>> <?=lang('Off')?>
			</label>

			<label class="col-md-2" for="rdbWithdrawTypeWithdrawal_password">
				<input type="radio" name="rdbWithdrawType" id="rdbWithdrawTypeWithdrawal_password" value="withdrawal_password" <?= $withdrawalVerification === 'withdrawal_password' ? "checked" : "" ?>> <?=lang('Withdrawal Password')?>
			</label>
		</td></tr>
		<tr>
			<td><input type="submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>" value="<?php echo lang('Save');?>"></td>
		</tr>
	</table>
</form>

</div>

</div>

</div>

<script type="text/javascript">
	var  base_url = "<?=base_url()?>";

	$(document).ready(function(){
		$('#view_system_settings').addClass('active');
		// $('#trPlayerCenterSetup').hide();

		window.URL = window.URL || window.webkitURL;

		$('.setup-type').click(function(e){
			$("#hdnSetupType").val($(this).data("type"));
			$("#mnuSetupType").text($(this).text());
		});

		$('.withdrawal-type').click(function(e){
			$("#hdnWithdrawalType").val($(this).data("type"));
			$("#mnuWithdrawalType").text($(this).text());
		});

		$("#btnSetupNewPlayerCenter").click(function() {
			var setupType = $("#hdnSetupType").val();

			if (setupType == null  || !setupType) {
				alert("Please select a setup type");
				return;
			}

			$.ajax({
				url: base_url + 'cms_management/setNewPlayeCenterTemplate',
				type: 'POST',
				success: function(data){
					if (data['success']) {
						window.location = '/cms_management/setupNewPlayerCenter/'+setupType;
					}
				}
			});

		});

		var selected_template = "<?php echo $this->utils->getOperatorSetting('player_center_template');?>";
        $('#player_center_template option').each(function(){
            if($(this).val() === selected_template){
                $(this).attr("selected", true);
            }
        }).trigger('change');

		$("#chkSetupNewPlayerCenter").change(function() {
			if($(this).is(":checked")) {
	        	$('#trPlayerCenterSetup').removeClass('hidden');
	        	$('#player_center_template').val("<?=$default_template?>");
	        	$('#player_center_template').prop("disabled", true);
	        	$('#trSaveTempate').hide();
	        } else {
	        	$('#trPlayerCenterSetup').addClass('hidden');
	        	$('#player_center_template').val("<?=$current_template?>");
	        	$('#player_center_template').prop("disabled", false);
	        	$('#trSaveTempate').hide();
	        	$('#trSaveTempate').show();
	        }

		});

	});
	// resizeSidebar();

</script>
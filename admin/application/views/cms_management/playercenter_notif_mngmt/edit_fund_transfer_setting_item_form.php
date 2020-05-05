<?php $this->load->view('cms_management/playercenter_notif_mngmt/common_style') ?>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary">
			<div class="panel-heading custom-ph">
				<h4 class="panel-title custom-pt"><i class="icon-newspaper"></i> <?=lang('Edit Notification Item');?>
					<a href="<?=BASEURL . 'cms_management/notificationManagementSettings'?>" class="btn btn-default btn-sm pull-right">
						<span class="glyphicon glyphicon-remove"></span>
					</a>
				</h4>
			</div>
			
			<div class="panel-body" id="notif_item_setting_panel_body">
				<form class="form-horizontal" action="<?=BASEURL . 'cms_management/submitUpdateSettings/transfer_fund_notif/'.$itemId?>" method="POST">
					<div class="form-group">
						<div class="col-md-6 col-md-offset-3 addTopPadding">
							<label for="errorTypeLbl"><?=lang('Transfer Fund Error Type');?>:</label>
							<input type="text" name="label" id="errorTypeLbl" maxlength="100" class="form-control" value="<?= lang($itemSettingDetails['label'])?>" readonly/>
							<span class="isRed"><?=form_error('errorTypeLbl')?></span>
						</div>
						<div class="col-md-6 col-md-offset-3 addTopPadding">
							<label for="cmsLang"><?=lang('cms.lang');?>:</label>
							<input type="text" name="language" id="cmsLang" maxlength="100" class="form-control" value="<?= $itemSettingDetails['multi_lang_messages'][$lang_code]['language']?>" readonly/>
							<span class="isRed"><?=form_error('cmsLang')?></span>
						</div>
						<div class="col-md-6 col-md-offset-3 addTopPadding">
							<label for="customErrorCode"><?=lang('Custom Error Code');?> *:</label>
							<input type="text" name="custom_error_code" class="form-control" maxlength="10" minlength="6" value="<?=(set_value('customErrorCode') != null) ? set_value('customErrorCode') : $itemSettingDetails['custom_error_code']?>" required>
							<span class="isRed"><?=form_error('custom_error_msg')?></span>
						</div>
						<div class="col-md-6 col-md-offset-3 addTopPadding">
							<label for="customErrorMsg"><?=lang('Custom Error Message');?> *:</label>
							<textarea name="custom_error_msg" id="customErrorMsg" class="form-control" maxlength="3000" required><?=(set_value('customErrorMsg') != null) ? set_value('customErrorMsg') : $itemSettingDetails['multi_lang_messages'][$lang_code]['custom_error_msg']?></textarea>
							<span class="isRed"><?=form_error('custom_error_msg')?></span>
						</div>
						<div class="col-md-6 col-md-offset-3 addTopPadding">
							<label for="playerOptionMsg1"><?=lang('Player Option Message 1');?> <?= $itemSettingDetails['multi_lang_messages'][$lang_code]['player_option_msg1'] == "N/A" ? "" : "*" ?> :</label>
							<textarea name="player_option_msg1" id="playerOptionMsg1" class="form-control" maxlength="3000" <?= $itemSettingDetails['multi_lang_messages'][$lang_code]['player_option_msg1'] == "N/A" ? "disabled" : "" ?> required><?=(set_value('playerOptionMsg1') != null) ? set_value('playerOptionMsg1') : $itemSettingDetails['multi_lang_messages'][$lang_code]['player_option_msg1']?></textarea>
							<span class="isRed"><?=form_error('playerOptionMsg1')?></span>
						</div>
						<div class="col-md-6 col-md-offset-3 addTopPadding">
							<label for="playerOptionMsg2"><?=lang('Player Option Message 2');?> <?= $itemSettingDetails['multi_lang_messages'][$lang_code]['player_option_msg2'] == "N/A" ? "" : "*" ?> :</label>
							<textarea name="player_option_msg2" id="playerOptionMsg2" class="form-control" maxlength="3000" <?= $itemSettingDetails['multi_lang_messages'][$lang_code]['player_option_msg2'] == "N/A" ? "disabled" : "" ?> required><?=(set_value('playerOptionMsg2') != null) ? set_value('playerOptionMsg2') : $itemSettingDetails['multi_lang_messages'][$lang_code]['player_option_msg2']?></textarea>
							<span class="isRed"><?=form_error('playerOptionMsg2')?></span>
						</div>
					</div>
					<center>
						<input type="submit" class="btn btn-info btn-md" value="<?=lang('lang.save');?>" />
						<a href="<?=BASEURL . 'cms_management/notificationManagementSettings'?>">
							<input type="button" class="btn btn-default btn-md" value="<?=lang('lang.cancel');?>" />
						</a>
					</center>
				</form>
			</div>

			<div class="panel-footer"></div>
		</div>
	</div>
</div>
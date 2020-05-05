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
				<form class="form-horizontal" action="<?=BASEURL . 'cms_management/submitUpdateSettings/cashback_claim_notif/'.$itemId?>" method="POST">
					<div class="form-group">
						<div class="col-md-6 col-md-offset-3 addTopPadding">
							<label for="errorTypeLbl"><?=lang('Cashback Notification Error');?>:</label>
							<input type="text" name="errorTypeLbl" id="errorTypeLbl" maxlength="100" class="form-control" value="<?= lang($itemSettingDetails['label'])?>" readonly/>
							<span class="isRed"><?=form_error('errorTypeLbl')?></span>
						</div>
						<div class="col-md-6 col-md-offset-3 addTopPadding">
							<label for="language"><?=lang('cms.language');?>:</label>
							<input type="text" name="language" id="language" maxlength="100" class="form-control" value="<?= $itemSettingDetails['multi_lang_messages'][$lang_code]['language']?>" readonly/>
							<span class="isRed"><?=form_error('language')?></span>
						</div>
						<div class="col-md-6 col-md-offset-3 addTopPadding">
							<label for="customErrorCode"><?=lang('Custom Error Code');?> *:</label>
							<input type="text" name="custom_error_code" class="form-control" maxlength="10" minlength="6" value="<?=(set_value('customErrorCode') != null) ? set_value('customErrorCode') : $itemSettingDetails['custom_error_code']?>" required>
							<span class="isRed"><?=form_error('custom_error_code')?></span>
						</div>
						<div class="col-md-6 col-md-offset-3 addTopPadding">
							<label for="customErrorMsg"><?=lang('Custom Error Message');?>:</label>
							<textarea name="claim_error_notif_msg" id="customErrorMsg" class="form-control" maxlength="3000" <?= $itemSettingDetails['multi_lang_messages'][$lang_code]['claim_error_notif_msg'] == "N/A" ? "disabled" : "" ?> required><?=(set_value('customErrorMsg') != null) ? set_value('customErrorMsg') : $itemSettingDetails['multi_lang_messages'][$lang_code]['claim_error_notif_msg']?></textarea>
							<span class="isRed"><?=form_error('customErrorMsg')?></span>
						</div>
						<div class="col-md-6 col-md-offset-3 addTopPadding">
							<label for="playerOptionMsg"><?=lang('Player Option Message 1');?>:</label>
							<textarea name="player_option_msg" id="playerOptionMsg" class="form-control" maxlength="3000" <?= $itemSettingDetails['multi_lang_messages'][$lang_code]['player_option_msg'] == "N/A" ? "disabled" : "" ?> required><?=(set_value('playerOptionMsg') != null) ? set_value('playerOptionMsg') : $itemSettingDetails['multi_lang_messages'][$lang_code]['player_option_msg']?></textarea>
							<span class="isRed"><?=form_error('playerOptionMsg')?></span>
						</div>
					</div>
					<center>
						<input type="submit" class="btn btn-info btn-md" value="<?=lang('lang.save');?>" />
						<a href="<?=BASEURL . 'cms_management/notificationManagementSettings/edit_cashback_claim_form'?>">
							<input type="button" class="btn btn-default btn-md" value="<?=lang('lang.cancel');?>" />
						</a>
					</center>
				</form>
			</div>

			<div class="panel-footer"></div>
		</div>
	</div>
</div>
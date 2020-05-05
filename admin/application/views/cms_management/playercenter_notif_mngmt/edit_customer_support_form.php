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
				<form class="form-horizontal" action="<?=BASEURL . 'cms_management/submitUpdateSettings/customer_support_url'?>" method="POST">
					<div class="form-group">

						<div class="col-md-6 col-md-offset-3 addTopPadding">
							<label for="customerSupportUrl"><?=lang('Customer Support Url');?>:</label>
							<textarea name="url" id="customerSupportUrl" class="form-control" maxlength="3000" <?= $itemSettingDetails[0]['url'] == "N/A" ? "disabled" : "" ?> required><?=(set_value('customerSupportUrl') != null) ? set_value('customerSupportUrl') : $itemSettingDetails[0]['url']?></textarea>
							<span class="isRed"><?=form_error('customerSupportUrl')?></span>
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
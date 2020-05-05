<style type="text/css">
	div#nav_content {
		width: 100%; 
		height: auto; 
		float: left; 
		border: 1px solid lightgray;
		border-top: none; 
		padding: 30px 0;
	}

	.col-md-3{
		margin: 0;
		padding: 0 10px;
		float: left;
		height: 70px;
	}

	.col-md-3 label{
		margin: 0;
		padding: 0;
		float: left;
		height: auto;
	}

	.col-md-3 span{
		margin: 0;
		padding: 0;
		float: left;
		height: auto;
		color: red;
	}
</style>

<div class="row">
	<div id="container" class="col-md-12">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title pull-left"><i class="icon-settings"></i> <?= lang('system.word92'); ?></h3>
				<div class="clearfix"></div>
			</div>

			<div class="panel-body" id="panel_body">
				<div class="col-md-12">
					<ul class="nav nav-tabs">
						<?php 
							$cnt = 1;

							foreach ($games as $key => $value) { 
								if($cnt == 1) {
						?>
									<li class="tab <?= ($type == $value['gameId']) ? 'active':'' ?>" id="<?= $value['gameId']; ?>"><a href="#" onclick="changeAPISettings(<?= $value['gameId']; ?>);" data-toggle="tab"><?= $value['game']; ?></a></li>
						<?php 
								} else {
						?>
									<li class="tab <?= ($type == $value['gameId']) ? 'active':'' ?>" id="<?= $value['gameId']; ?>"><a href="#" onclick="changeAPISettings(<?= $value['gameId']; ?>);" data-toggle="tab"><?= $value['game']; ?></a></li>
						<?php
								}

								$cnt++;
							} 
						?>
					</ul>

					<div id="nav_content">
						<form action="<?= BASEURL . 'user_management/saveAPISettings/' . $type ?>" method="POST" enctype="multipart/form-data">
							<div class="col-md-12">
								<label class="pull-right input-sm" style="color: red;"><?= lang('player.mp14'); ?></label>
							</div>
							
							<div class="col-md-12">
								<div class="col-md-3">
									<label for="api_url" class="input-sm"><?= lang('sys.api01'); ?>:</label>
									<input type="text" name="api_url" class="form-control input-sm" value="<?= ($settings != null) ? $settings['apiURL']:'' ?>"/>
									<span class="input-sm"><?= form_error('api_url'); ?></span>
								</div>

								<div class="col-md-3">
									<label for="api_entity_key" class="input-sm"><?= lang('sys.api02'); ?>:</label>
									<input type="text" name="api_entity_key" class="form-control input-sm" value="<?= ($settings != null) ? $settings['apiEntityKey']:'' ?>"/>
									<span class="input-sm"><?= form_error('api_entity_key'); ?></span>
								</div>

								<div class="col-md-3">
									<label for="api_cert_key_path" class="input-sm"><?= lang('sys.api03') ?>:</label>
									<input type="file" name="api_cert_key_path" class="form-control input-sm" value="<?= ($settings != null) ? $settings['apiCertKeyPath']:'' ?>"/>
									<span class="input-sm"><?= form_error('api_cert_key_path'); ?></span>
								</div>

								<div class="col-md-3">
									<label for="api_cert_pem_path" class="input-sm"><?= lang('sys.api08') ?>:</label>
									<input type="file" name="api_cert_pem_path" class="form-control input-sm" value="<?= ($settings != null) ? $settings['apiCertPemPath']:'' ?>"/>
									<span class="input-sm"><?= form_error('api_cert_pem_path'); ?></span>
								</div>

								<div class="col-md-3">
									<label for="api_admin_name" class="input-sm"><?= lang('sys.api04'); ?>:</label>
									<input type="text" name="api_admin_name" class="form-control input-sm" value="<?= ($settings != null) ? $settings['apiAdminName']:'' ?>"/>
									<span class="input-sm"><?= form_error('api_admin_name'); ?></span>
								</div>

								<div class="col-md-3">
									<label for="api_kiosk_name" class="input-sm"><?= lang('sys.api05'); ?>:</label>
									<input type="text" name="api_kiosk_name" class="form-control input-sm" value="<?= ($settings != null) ? $settings['apiKioskName']:'' ?>"/>
									<span class="input-sm"><?= form_error('api_kiosk_name'); ?></span>
								</div>

								<div class="col-md-3">
									<label for="api_external_tran_id_deposit" class="input-sm"><?= lang('sys.api06'); ?>:</label>
									<input type="text" name="api_external_tran_id_deposit" class="form-control input-sm" value="<?= ($settings != null) ? $settings['apiExternalTranIdDeposit']:'' ?>"/>
									<span class="input-sm"><?= form_error('api_external_tran_id_deposit'); ?></span>
								</div>

								<div class="col-md-3">
									<label for="api_external_tran_id_withdrawal" class="input-sm"><?= lang('sys.api07'); ?>:</label>
									<input type="text" name="api_external_tran_id_withdrawal" class="form-control input-sm" value="<?= ($settings != null) ? $settings['apiExternalTranIdWithdrawal']:'' ?>"/>
									<span class="input-sm"><?= form_error('api_external_tran_id_withdrawal'); ?></span>
								</div>
							</div>

							<div class="col-md-12" style="margin: 20px 0 0 0;">
								<div class="col-md-4 col-md-offset-5">
									<input type="submit" class="btn btn-sm btn-primary" value="<?= lang('lang.save'); ?>"/>
									<input type="button" class="btn btn-sm btn-primary" onclick="changeAPISettings(<?= $type; ?>);" value="<?= lang('lang.cancel'); ?>"/>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>

			<div class="panel-footer">

			</div>
		</div>
	</div>
</div>
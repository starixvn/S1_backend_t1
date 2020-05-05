<style type="text/css">
	.control-label {
		text-align: right !important;
	}
	textarea.form-control {
		height: auto !important;
		resize: none !important;
	}
</style>
<div class="row">
	<div class="col-md-12" id="toggleView">
<div class="panel panel-primary">

	<div class="panel-heading custom-ph">
		<h4 class="panel-title custom-pt pull-left"><i class="icon-user-plus"></i> <?=lang('Create Auto Player')?></h4>

		<a href="<?= '/resources/sample_csv/sample_player_batch_upload.csv' ?>" class="btn btn-sm pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-default' ?>">
			<span><?= lang('Download Sample File') ?></span>
		</a>
		<div class="clearfix"></div>
	</div>

	<div class="panel-body" id="player_panel_body">
        <form action="<?='/player_management/verifyAddAccountAutoProcess'?>" class="form-horizontal"
            id="verifyAddAccountProcess" method="POST" autocomplete="off" enctype="multipart/form-data">
            <!-- onsubmit="verifyAddAccountProcess()" -->
			<input type="hidden" id="registered_by" name="registered_by" value="<?=Player_model::REGISTERED_BY_IMPORTER?>" />
			<div class="form-group form-group-sm">
				<i class="col-md-offset-3 col-md-8 text-danger"><?=lang('reg.02')?></i>
			</div>
			<div class="form-group form-group-sm">
				<label for="import" class="col-md-3 control-label"><?=lang('player.mp03')?> <span class="text-danger">*</span></label>
				<div class="col-md-4">
					<input type="file" id="import" name="import" class="form-control" accept=".csv" required="required" />
				</div>
			</div>
			<div class="form-group form-group-sm">
				<!--
				<div class="col-md-8">
					<input type="text" name="username" id="username" class="form-control" required="required">
				</div -->
				<label for="search_game_date" class="col-md-3 control-label"><?=lang('Date')?> <span class="text-danger">*</span></label>
				<div class="col-md-4">
					<input id="search_game_date" class="form-control dateInput" data-start="#by_date_from" data-end="#by_date_to" data-time="true"/>
					<input type="hidden" id="by_date_from" name="by_date_from" value="" />
					<input type="hidden" id="by_date_to" name="by_date_to"  value=""/>
				</div>
			</div>

			<?php if( !empty($this->config->item('kingrich_currency_branding')) && $this->utils->getConfig('multiple_currency_enabled') ) :?>
				<div class="form-group form-group-sm">
					<label for="by_currency" class="col-md-3 control-label"><?=lang('Currency');?> <span class="text-danger">*</span></label>
					<div class="col-md-4">
						<select class="form-control input-sm" name="by_currency" id="by_currency" required="required">
							<option value="" ><?=lang('lang.select');?></option>
							<?php foreach ($this->config->item('kingrich_currency_branding') as $key => $value) : ?>
								<option value="<?=$key?>" ><?=$key?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
			<?php endif; ?>

			<div class="form-group form-group-sm">
				<label for="password" class="col-md-3 control-label"><?=lang('player.mp07')?> <span class="text-danger">*</span></label>
				<div class="col-md-8">
					<input type="password" name="password" id="password" class="form-control" minLength="6" maxLength="20" required="required">
				</div>
			</div>


			<div class="form-group form-group-sm">
				<div class="col-md-offset-3 col-md-8">
					<button type="submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary' ?>"><?=lang('forgot.13')?></button>
				</div>
			</div>

		</form>
	</div>
	<div class="panel-footer"></div>
</div>

	</div>
</div>
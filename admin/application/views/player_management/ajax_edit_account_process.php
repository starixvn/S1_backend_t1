<div class="panel panel-primary">
	<div class="panel-heading custom-ph">
		<h4 class="panel-title custom-pt">
			<i class="glyphicon glyphicon-edit"></i> <?=lang('player.mp11');?>
			<a href="<?=BASEURL . 'player_management/accountProcess'?>" class="btn btn-sm pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-info' : 'btn-default' ?>" id="account_process">
				<span class="glyphicon glyphicon-remove"></span>
			</a>
		</h4>
	</div>

	<div class="panel-body" id="player_panel_body">
		<form class="form-horizontal" method="POST" action="<?=BASEURL . 'player_management/verifyEditAccountProcess/' . $batch['batchId']?>">
			<div class="form-group">
				<i class="col-md-12 col-md-offset-1" style="color: red;"><?=lang('reg.02');?></i>
			</div>
			<div class="form-group">
				<label for="type_code" class="col-md-3 control-label" style="text-align:right;"><?=lang('player.mp12');?></label>
				<div class="col-md-8">
					<input type="text" name="type_code" id="type_code" class="form-control" value="<?=$batch['typeCode']?>" readonly>
				</div>
			</div>
			<div class="form-group">
				<label for="name" class="col-md-3 control-label" style="text-align:right;"><?=lang('player.mp02');?> <span class="text-danger">*</span></label>
				<div class="col-md-8">
					<input type="text" name="name" id="name" class="form-control" value="<?=$batch['name']?>" required>
				</div>
			</div>
			<div class="form-group">
				<label for="count" class="col-md-3 control-label" style="text-align:right;"><?=lang('player.mp03');?></label>
				<div class="col-md-8">
					<input type="text" name="count" id="count" class="form-control" value="<?=$batch['count']?>" readonly>
				</div>
			</div>
			<div class="form-group">
				<label for="type" class="col-md-3 control-label" style="text-align:right;"><?=lang('player.mp08');?></label>
				<div class="col-md-8">
					<input type="text" name="type" id="type" class="form-control" value="<?=$batch['type']?>" readonly>
				</div>
			</div>
			<!-- <div class="form-group">
				<label for="type" class="col-md-3 control-label" style="text-align:right;"><?=lang('player.62');?>: </label>
				<div class="col-md-8">
					<input type="text" name="language" id="language" class="form-control" value="<?=$batch['language']?>" readonly>
				</div>
			</div> -->
			<div class="form-group">
				<label for="description" class="col-md-3 control-label" style="text-align:right;"><?=lang('player.mp04');?></label>
				<div class="col-md-8">
					<textarea name="description" id="description" class="form-control" style="resize:none; height: 100px;"><?=$batch['description']?></textarea>
				</div>
			</div>
			<div class="col-md-offset-2">
				<span style="display:none;color:red;" id="error"></span>
			</div>
			<div class="col-md-offset-3" style="padding-left:1%;">
				<input type="submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-info' ?>" value="<?=lang('player.55');?>" onclick="verifyEditAccountProcess(<?=$batch['batchId']?>);">
			</div>
		</form>
	</div>

	<div class="panel-footer">

	</div>
</div>
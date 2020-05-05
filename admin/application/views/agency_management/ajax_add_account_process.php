<div class="panel panel-primary">

	<div class="panel-heading">
		<a href="<?='/player_management/accountProcess'?>" class="btn btn-default btn-sm pull-right" id="account_process">
			<span class="glyphicon glyphicon-remove"></span>
		</a>
		<h4><i class="icon-user-plus"></i> <?=lang('player.mp06')?></h4>
	</div>

	<div class="panel-body" id="player_panel_body">
        <form action="<?='/player_management/verifyAddAccountProcess'?>" class="form-horizontal"
            id="verifyAddAccountProcess" method="POST" autocomplete="off" >
            <!-- onsubmit="verifyAddAccountProcess()" -->
			<input type="hidden" name="type_code" id="type_code" value="<?=$type_code?>">
			<input type="hidden" name="agent_id" id="agent_id" value="<?=$agent_id?>">

			<div class="form-group form-group-sm">
				<i class="col-md-offset-3 col-md-8 text-danger"><?=lang('reg.02')?></i>
			</div>

			<div class="form-group form-group-sm">
				<label for="username" class="col-md-3 control-label"><?=lang('player.mp02')?> <span class="text-danger">*</span></label>
				<div class="col-md-8">
					<input type="text" name="username" id="username" class="form-control" required="required">
				</div>
			</div>

			<div class="form-group form-group-sm">
				<label for="count" class="col-md-3 control-label"><?=lang('player.mp03')?> <span class="text-danger">*</span></label>
				<div class="col-md-8">
					<input type="number" name="count" id="count" class="form-control" min="1" required="required" onkeypress="return isNumberKey(event)">
					<span class="help-block"><?=lang('player.mp16')?></span>
				</div>
			</div>

			<div class="form-group form-group-sm">
				<label for="password" class="col-md-3 control-label"><?=lang('player.mp07')?> <span class="text-danger">*</span></label>
				<div class="col-md-8">
					<input type="password" name="password" id="password" class="form-control" minLength="6" maxLength="20" required="required">
				</div>
			</div>

			<div class="form-group form-group-sm">
				<label for="typeOfPlayer" class="col-md-3 control-label"><?=lang('player.mp08')?> <span class="text-danger">*</span></label>
				<div class="col-md-8">
					<select name="typeOfPlayer" id="typeOfPlayer" class="form-control" required="required">
						<option value=""><?=lang('lang.select')?></option>
						<option value="real"><?=lang('player.mp09')?></option>
						<option value="demo"><?=lang('player.mp10')?></option>
					</select>
				</div>
			</div>

			<div class="form-group form-group-sm">
				<label for="agent_name" class="col-md-3 control-label"><?=lang('Parent Agent Username')?></label>
				<div class="col-md-8">
                    <input type="text" name="agent_name" id="agent_name" class="form-control"
                    value="<?=isset($parent_agent_name)? $parent_agent_name:'';?>" readonly />
				</div>
			</div>

			<div class="form-group form-group-sm">
				<label for="language" class="col-md-3 control-label"><?=lang('system.word3')?> <span class="text-danger">*</span></label>
				<div class="col-md-8">
					<select name="language" id="language" class="form-control" required="required">
						<option value=""><?=lang('lang.select')?></option>
						<option value="English">English</option>
						<option value="Chinese">Chinese</option>
					</select>
				</div>
			</div>

			<div class="form-group form-group-sm">
				<label for="description" class="col-md-3 control-label"><?=lang('player.mp04')?></label>
				<div class="col-md-8">
					<textarea name="description" id="description" class="form-control" rows="5"></textarea>
				</div>
			</div>

			<div class="form-group form-group-sm">
				<span class="col-md-offset-3 col-md-8 text-danger" id="error"></span>
			</div>

			<div class="form-group form-group-sm">
				<div class="col-md-offset-3 col-md-8">
					<button type="submit" class="btn btn-primary"><?=lang('lang.save')?></button>
				</div>
			</div>

		</form>
	</div>
	<div class="panel-footer"></div>
</div>

<div class="row">
	<div class="col-md-offset-4 col-md-4">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title"><?=$title?></h3>
			</div>
			<div class="panel-body">
				<?php if ($errors = validation_errors()): ?>
					<div class="alert alert-danger"><?=$errors?></div>
				<?php endif?>
				<form id="form" method="post" autocomplete="off">
					<div class="form-group">
						<label for="username" class="control-label"><?=lang('Agent Username')?></label>
						<input id="username" class="form-control" type="text" value="<?php echo $username; ?>" readonly="readonly"/>
					</div>
					<div class="form-group">
						<label for="balance" class="control-label"><?=lang('Balance')?></label>
						<input id="balance" class="form-control" type="text" value="<?php echo $this->utils->formatCurrencyNoSym($balance); ?>" readonly="readonly"/>
					</div>
					<div class="form-group required">
						<label for="amount" class="control-label"><?=lang('Amount')?></label>
						<input name="amount" id="amount" class="form-control" type="number" value="<?=set_value('amount')?>" required="required" min="1" step="any"/>
					</div>
					<div class="form-group required">
						<label for="date" class="control-label"><?=lang('sys.gd11')?></label>
						<textarea name="reason" id="reason" class="form-control" rows="5" required="required"><?=set_value('reason')?></textarea>
					</div>
				</form>
			</div>
			<div class="panel-footer">
				<div class="text-right">
					<button type="submit" form="form" class="btn btn-primary"><?=lang('Submit')?></button>
                    <?php if (isset($agent_id) && $agent_id > 0) { ?>
                    <a href="/<?=$controller_name?>/agent_information/<?=$agent_id?>#bank_info" class="btn btn-default"><?=lang('lang.cancel');?></a>
                    <?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>

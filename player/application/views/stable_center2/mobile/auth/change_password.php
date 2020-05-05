<?php if (validation_errors()): ?>
	<div class="alert alert-danger"><?=validation_errors()?></div>
<?php endif?>
<div class="row" style="padding-top:10%; padding-bottom:10%;">
	<div class="col-md-4 col-md-offset-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<?=lang('forgot.08')?>
				<a href="/iframe/auth/login" class="close" aria-hidden="true">×</a>
			</div>
			<div class="panel-body">
				<form method="post">
					<div class="form-group">
						<input type="text" class="form-control" value="<?=$username?>" readonly="readonly" />
					</div>
					<div class="form-group">
						<input type="password" name="password" class="form-control" placeholder="<?=lang('forgot.09')?>" autofocus="autofocus"/>
					</div>
					<div class="form-group">
						<div class="input-group">
							<input type="password" name="confirm_password" class="form-control" placeholder="<?=lang('forgot.10')?>"/>
							<span class="input-group-btn">
								<button type="submit" class="btn btn-primary"><?=lang('forgot.08')?></button>
							</span>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
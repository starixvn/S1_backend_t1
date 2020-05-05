<?php if (validation_errors()): ?>
	<div class="alert alert-danger"><?=validation_errors()?></div>
<?php endif?>
<div class="row" style="padding-top:10%; padding-bottom:10%;">
	<div class="col-md-4 col-md-offset-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<?=lang('lang.forgotpasswd')?>
				<a href="/iframe/auth/login" class="close" aria-hidden="true">×</a>
			</div>

			<?php if ($this->utils->isEnabledFeature('contact_customer_service_for_forgot_password')) : ?>
				<div class="panel-body">
					<?= lang('notify.104'); ?>
				</div>
			<?php else: ?>
				<div class="panel-body">
					<form action="/iframe_module/forgot_password" method="post" autocomplete="off">
						<div class="form-group">
							<div class="input-group">
								<input type="text" name="username" class="form-control" value="<?=set_value('username')?>" placeholder="<?=lang('forgot.02')?>" required="required" autofocus="autofocus"/>
								<span class="input-group-btn">
									<button type="submit" class="btn btn-primary"><?=lang('forgot.03')?></button>
								</span>
							</div>
							<div class="help-block"><?=lang('forgot.01')?></div>
						</div>
					</form>
				</div>
			<?php endif; ?>				
		</div>
	</div>
</div>
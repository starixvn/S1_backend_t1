<div class="panel panel-primary">
	<div class="panel-heading">
		<h4 class="panel-title">
			<i class="icon-key2"></i> <?= lang('Reset Secondary Password'); ?>
			<a href="<?= BASEURL . 'affiliate_management/userInformation/' . $affiliate_id ?>" class="close" id="reset_password">&times;</a>
		</h4>
	</div>

	<div class="panel panel-body" id="player_panel_body">
		<form method="POST" action="<?= BASEURL . 'affiliate_management/verifyChangeSecondPassword/' . $affiliate_id ?>" autocomplete="off">

			<div class="row">
                <div class="col-md-offset-3 col-md-6">
                    <div class="table-responsive">
						<table class="table table-bordered">
							<tr>
								<th class="active col-md-2"><?= lang('New Secondary Password'); ?>:</th>
								<td class="col-md-4">
									<input type="password" name="new_password" id="new_password" class="form-control" value="">
									<span style="color:red;"><?= form_error('new_password'); ?></span>
								</td>
							</tr>
							<tr>
								<th class="active col-md-2"><?= lang('Confirm Secondary Password'); ?>:</th>
								<td class="col-md-4">
									<input type="password" name="confirm_new_password" id="confirm_new_password" class="form-control" value="">
									<span style="color:red;"><?php echo form_error('confirm_new_password'); ?></span>
								</td>
							</tr>
							<input type="hidden" name="affiliate_id" id="affiliate_id" class="form-control" value="<?= $affiliate_id?>">
						</table>
						<center>
							<input type="submit" class="btn btn-info" value="<?= lang('lang.reset'); ?>">
							<a href="<?= BASEURL . 'affiliate_management/userInformation/' . $affiliate_id ?>" class="btn btn-default btn-md" id="reset_password"><?= lang('lang.cancel'); ?></a>
						</center>
					</div>
				</div>
			</div>
		</form>
	</div>

</div>
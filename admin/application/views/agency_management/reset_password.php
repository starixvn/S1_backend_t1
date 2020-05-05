<div class="panel panel-primary">
	<div class="panel-heading">
		<h4 class="panel-title pull-left"><i class="icon-key2"></i> <?= lang('player.ur01'); ?> </h4>
        <a href="<?= BASEURL . 'agency_management/agent_information/' . $agent_id ?>" 
            class="btn btn-default btn-sm pull-right" id="reset_password">
            <span class="glyphicon glyphicon-remove"></span>
        </a>
        <div class="clearfix"></div>
	</div>

	<div class="panel panel-body" id="player_panel_body">
        <form method="POST" action="<?= BASEURL . 'agency_management/verify_reset_password/' . $agent_id ?>" 
            autocomplete="off">

			<div class="row">
                <div class="col-md-offset-3 col-md-6">
                    <div class="table-responsive">
						<table class="table table-bordered">
							<tr>
								<th class="active col-md-2"><?= lang('player.ur02'); ?>:</th>
								<td class="col-md-4">
                                    <input type="password" name="new_password" id="new_password" class="form-control" 
                                    value="">
									<span style="color:red;"><?= form_error('new_password'); ?></span>
								</td>
							</tr>
							<tr>
								<th class="active col-md-2"><?= lang('player.ur03'); ?>:</th>
								<td class="col-md-4">
                                    <input type="password" name="confirm_new_password" id="confirm_new_password" 
                                    class="form-control" value="">
									<span style="color:red;"><?php echo form_error('confirm_new_password'); ?></span>
								</td>
							</tr>
							<input type="hidden" name="agent_id" id="agent_id" class="form-control" value="<?= $agent_id?>">
						</table>
						<center>
							<input type="submit" class="btn btn-info" value="<?= lang('lang.reset'); ?>">
                            <a href="<?= BASEURL . 'agency_management/agent_information/' . $agent_id ?>" 
                                class="btn btn-default btn-md" id="reset_password">
                                <?= lang('lang.cancel'); ?>
                            </a>
                        </center>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

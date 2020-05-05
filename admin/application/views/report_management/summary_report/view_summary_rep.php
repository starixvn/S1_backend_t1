<div class="row">
	<div class="col-md-12" id="toggleView">
		<div class="panel panel-primary">
			<div class="panel-heading custom-ph">
				<h4 class="panel-title custom-pt"><i class="icon-pie-chart"></i> <?= lang('report.sum04'); ?> </h4>
			</div>

			<div class="panel-body" id="summartreport_panel_body">
				 <div class="row">
                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <tr>
                                    <th class="active"><?= lang('report.sum06'); ?></th>
                                    <td><?= $total_register_player ?></td>
                                </tr>

                                <tr>
                                    <th class="active"><?= lang('report.sum17'); ?></th>
                                    <td><?= $total_players ?></td>
                                </tr>

                                <tr>
                                    <th class="active"><?= lang('report.sum07'); ?></th>
                                    <td><?= $total_deposit_player ?></td>
                                </tr>

                                <tr>
                                    <th class="active"><?= lang('report.sum09'); ?></th>
                                    <td><?= $total_withdrawal_player ?></td>
                                </tr>

                                <tr>
                                    <th class="active"><?= lang('report.in08'); ?></th>
                                    <td><?= $total_bonus ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <tr>
                                    <th class="active"><?= lang('report.sum16'); ?></th>
                                    <td><?= $total_mass_player ?></td>
                                </tr>

                                <tr>
                                    <th class="active"><?= lang('report.sum14'); ?></th>
                                    <td><?= $total_online ?></td>
                                </tr>

                                <tr>
                                    <th class="active"><?= lang('report.g09'); ?></th>
                                    <td><?= $total_bets ?></td>
                                </tr>

                                <tr>
                                    <th class="active"><?= lang('report.in03'); ?></th>
                                    <td><?= $total_deposit_amount ?></td>
                                </tr>

                                <tr>
                                    <th class="active"><?= lang('report.in04'); ?></th>
                                    <td><?= $total_withdrawal_amount ?></td>
                                </tr>

                                <tr>
                                    <th class="active"><?= lang('report.g12'); ?></th>
                                    <td><?= $total_earned ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
			</div>
            <div class="panel-footer"></div>
		</div>
	</div>
</div>



<div class="row">
	<div class="col-md-12" id="toggleView">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title pull-left"><i class="icon-pie-chart"></i> <?= lang('report.sum04'); ?> </h4>
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body" id="summartreport_panel_body">
				 <div class="row">
                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <tr>
                                    <th class="active"><?= lang('report.sum06'); ?></th>
                                    <td><?= $total_register_player ?></td>
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
                                    <th class="active"><?= lang('report.in06'); ?></th>
                                    <td><?= $total_bonus ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <tr>
                                    <th class="active"><?= lang('report.g09'); ?></th>
                                    <td><?= $total_bets ?></td>
                                </tr>

                                <tr>
                                    <th class="active"><?= lang('report.in03'); ?></th>
                                    <td><?= $total_deposit_amount ?></td>
                                </tr>

                                <tr>
                                    <th class="active"><?= lang('report.in05'); ?></th>
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
		</div>
	</div>
</div>



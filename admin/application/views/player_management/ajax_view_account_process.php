<div class="panel panel-primary">
	<div class="panel-heading custom-ph">
		<h4 class="panel-title custom-pt">
			<i class="icon-users"></i> <?= lang('player.mp15'); ?>
			<a href="<?= BASEURL . 'player_management/accountProcess'?>" class="btn btn-default btn-sm pull-right" id="account_process">
				<span class="glyphicon glyphicon-remove"></span>
			</a>
		</h4>
	</div>

	<div class="panel panel-body" id="player_panel_body">
		<div id="viewAccountProcess" class="table-responsive">
			<input type="hidden" value="<?= $batch_id ?>" id="batch_id" />
			<table class="table table-striped table-hover" style="margin: 0px 0 0 0; width: 100%;" id="myTable">
				<thead>
					<tr>
						<th></th>
						<th><?= lang('player.01'); ?></th>
						<th><?= lang('player.56'); ?></th>
						<?php if($this->permissions->checkPermissions('edit_player') || $this->permissions->checkPermissions('delete_player') ) { ?>
							<th><?= lang('lang.action'); ?></th>
						<?php } ?>
					</tr>
				</thead>

				<tbody>
					<?php if(!empty($batch_account)) { ?>
						<?php foreach($batch_account as $row) { ?>
							<tr>
								<td></td>
								<td><?= $row['username']?></td>
								<td><?= $row['batchPassword']?></td>
								<td>
									<!-- <a href="#" data-toggle="tooltip" id="view" onclick=""><span class="glyphicon glyphicon-zoom-in"></span></a> -->

									<?php if($this->permissions->checkPermissions('edit_player')) { ?>
										<a href="#" data-toggle="tooltip" class="edit_account" onclick="editAccountProcessDetails(<?= $row['playerId']?>);"><span class="glyphicon glyphicon-pencil"></span></a>
									<?php } ?>

									<?php if($this->permissions->checkPermissions('delete_player')) { ?>
										<!-- <a href="<?= BASEURL . 'player_management/deleteAccountProcessDetails/' . $row['playerId'] . '/' . $row['batchId'] . '/' . $row['type'] ?>" data-toggle="tooltip" class="delete_account"><span class="glyphicon glyphicon-trash"></span></a> -->
									<?php } ?>
								</td>
							</tr>
						<?php } ?>
					<?php } else { ?>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>

	<div class="panel-footer"></div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#myTable').DataTable({
            "responsive": {
                details: {
                    type: 'column'
                }
            },
            "columnDefs": [ {
                className: 'control',
                orderable: false,
                targets:   0
            } ],
            "order": [ 1, 'asc' ]
        });
    });
</script>

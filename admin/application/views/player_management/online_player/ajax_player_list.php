<div class="col-md-12">
	<a href="#" onclick="changeOnlineList('<?= $type; ?>');" class="btn btn-sm btn-primary" style="margin: 0 0 10px 0; float: left;" data-toggle="tooltip" data-title="<?= lang('lang.refresh'); ?>"><i class="icon-loop2"></i></a>

	<table class="table table-striped table-hover" id="playersTable" style="width: 100%;">		
		<thead>
			<tr>
				<th></th>
				<th><?= lang('player.01'); ?></th>
				<th><?= lang('sys.vu19'); ?></th>
				<th><?= lang('player.39'); ?></th>
				<th><?= lang('player.06'); ?></th>
				<th><?= lang('player.42'); ?></th>
				<?php if($type == 0) { ?>
					<th><?= lang('player.ol02'); ?></th>
				<?php } ?>
				<th><?= lang('lang.action'); ?></th>
			</tr>
		</thead>

		<tbody>
			<?php if(!empty($online_players)) { ?>
				<?php foreach ($online_players as $key => $value) { ?>
					<tr>
						<td></td>
						<td><?= $value['username']; ?></td>
						<td><?= $value['firstname'] . ' ' . $value['lastname']; ?></td>
						<td><?= $value['level']; ?></td>
						<td><?= $value['email']; ?></td>
						<td><?= $value['lastLoginTime']; ?></td>

						<?php if($type == 0) { ?>
							<td><?= $this->player_manager->time_elapsed_A(strtotime(date('Y-m-d H:i:s')) - strtotime($value['lastActivityTime'])); ?></td>
						<?php } ?>

						<td>
							<a href="#" class="btn btn-primary btn-xs" onclick="kickPlayer('<?= $value['username'] ?>', <?= $type; ?>);"><?= lang('player.ol03'); ?></a>
						</td>
					</tr>
				<?php } ?>
			<?php } ?>
		</tbody>
	</table>
</div>
<style type="text/css">
	.col-md-12 table{
		float: left;
		width: 100%;
	}
</style>

<div class="col-md-12 table-responsive">
	<?php if($settings != null) { ?> 
		<table class="table table-hover table-bordered input-sm">
			<tbody>
				<tr>
					<th class="active col-md-2"><?= lang('sys.api10'); ?>:</th>
					<td class="col-md-4"><?= $settings['apiURL']; ?></td>
				</tr>

				<tr>
					<th class="active col-md-2"><?= lang('sys.api11'); ?>:</th>
					<td class="col-md-4"><?= $settings['apiAdminName']; ?></td>
				</tr>

				<tr>
					<th class="active col-md-2"><?= lang('sys.api12'); ?>:</th>
					<td class="col-md-4"><?= $settings['apiKioskName']; ?></td>
				</tr>

				<tr>
					<th class="active col-md-2"><?= lang('sys.api13'); ?>:</th>
					<td class="col-md-4"><?= $settings['apiFTPServer']; ?></td>
				</tr>

				<tr>
					<th class="active col-md-2 col-md-2"><?= lang('sys.api14'); ?>:</th>
					<td class="col-md-4"><?= $settings['apiFTPUsername']; ?></td>
				</tr>

				<tr>
					<th class="active col-md-2 col-md-2"><?= lang('sys.api15'); ?>:</th>
					<td class="col-md-4"><?= $settings['apiFTPPassword']; ?></td>
				</tr>

				<tr>
					<th class="active col-md-2 col-md-2"><?= lang('sys.api16'); ?>:</th>
					<td class="col-md-4"><?= $settings['apiFTPServerFile']; ?></td>
				</tr>
			</tbody>
		</table>
	<?php } ?>

	<div class="col-md-4 col-md-offset-5">
		<a href="<?= BASEURL . 'user_management/editAPISettings/' . $type; ?>" class="btn btn-sm btn-primary"><?= ($settings != null) ? lang('lang.edit'):lang('lang.add'); ?></a>
	</div>
</div>
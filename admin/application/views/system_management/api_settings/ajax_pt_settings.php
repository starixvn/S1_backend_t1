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
					<th class="active col-md-2"><?= lang('sys.api01'); ?>:</th>
					<td class="col-md-4"><?= $settings['apiURL']; ?></td>
				</tr>

				<tr>
					<th class="active col-md-2"><?= lang('sys.api02'); ?>:</th>
					<td class="col-md-4"><?= substr($settings['apiEntityKey'], 0, 50) . "...."; ?></td>
				</tr>

				<tr>
					<th class="active col-md-2"><?= lang('sys.api03') . " " . lang('sys.api09'); ?>:</th>
					<td class="col-md-4"><?= $settings['apiCertKeyPath']; ?></td>
				</tr>

				<tr>
					<th class="active col-md-2"><?= lang('sys.api08') . " " . lang('sys.api09'); ?>:</th>
					<td class="col-md-4"><?= $settings['apiCertPemPath']; ?></td>
				</tr>
				
				<tr>
					<th class="active col-md-2"><?= lang('sys.api04'); ?>:</th>
					<td class="col-md-4"><?= $settings['apiAdminName']; ?></td>
				</tr>

				<tr>
					<th class="active col-md-2"><?= lang('sys.api05'); ?>:</th>
					<td class="col-md-4"><?= $settings['apiKioskName']; ?></td>
				</tr>

				<tr>
					<th class="active col-md-2"><?= lang('sys.api06'); ?>:</th>
					<td class="col-md-4"><?= $settings['apiExternalTranIdDeposit']; ?></td>
				</tr>

				<tr>
					<th class="active col-md-2 col-md-2"><?= lang('sys.api07'); ?>:</th>
					<td class="col-md-4"><?= $settings['apiExternalTranIdWithdrawal']; ?></td>
				</tr>
			</tbody>
		</table>
	<?php } ?>

	<div class="col-md-4 col-md-offset-5">
		<a href="<?= BASEURL . 'user_management/editAPISettings/' . $type; ?>" class="btn btn-sm btn-primary"><?= ($settings != null) ? lang('lang.edit'):lang('lang.add'); ?></a>
	</div>
</div>
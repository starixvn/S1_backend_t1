<div class="row">
	<div id="container" class="col-md-12">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">
					<i class="icon-settings"></i>
					<?=lang('role.11');?>
					<a href="javascript:void(0);" onclick="account_whitelist()"class="bookmark-this btn btn-xs pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info'?>" style="margin-right: 4px;"><i class="icon-users"></i> <?php echo lang('whitelist'); ?></a>
				</h3>
				<div class="clearfix"></div>
			</div>

			<div class="panel-body" id="panel_body">
				<div class="col-md-12">
					<form action="<?=site_url('user_management/saveDuplicateAccountSetting');?>" method="post" id="duplicate-setting-form">
						<table class="table table-striped table-hover table-bordered" style="width: 100%;">
							<thead>
								<tr>
									<th style="text-align: left;"><?=lang('sys.dasItem');?></th>
									<th style="text-align: center;"><?=lang('sys.dasExactRate');?> (1-10)</th>
									<th style="text-align: center;"><?=lang('sys.dasSimilarRate');?> (1-10)</th>
								</tr>
							</thead>
							<tbody style="text-align: center;">
								<?php foreach ($items as $key => $value) { ?>
									<?php if($value['id'] != 1) {  //There's no need to take username as a condition ?>
										<tr>
											<td class="col-md-2" style="text-align: left;">
												<?=lang('sys.item' . $value['item_id'] . '_description');?>
											</td>
											<td class="col-md-2">
												<?=($value['id'] == 1 || $value['id'] == 5) ?
		'<span name=' . $value['id'] . '_rate_exact' . ' style="color:#A9A8A8;">--</span>' : '<input type="number" name=' . $value['id'] . '_rate_exact' . ' class="form-control input-sm number_only" value=' . $value['rate_exact'] . ' min="0" max="10" maxlength="2" required />' . form_error($value['id'] . '_rate_exact');?>
											</td>
											<td class="col-md-2">
												<?=($value['id'] == 2 || $value['id'] == 3 || $value['id'] == 4  || $value['id'] == 6 || $value['id'] == 7  || $value['id'] == 8  || $value['id'] == 9  || $value['id'] == 10 || $value['id'] == 11 || $value['id'] == 12 || $value['id'] == 13) ?
		'<span name=' . $value['id'] . '_rate_exact' . ' style="color:#A9A8A8;">--</span>' : '<input type="number" name=' . $value['id'] . '_rate_similar' . ' class="form-control input-sm number_only" value=' . $value['rate_similar'] . ' min="0" max="10" maxlength="2" required />' . form_error($value['id'] . 'rate_similar');?>
											</td>
										</tr>
									<?php } ?>
								<?php } ?>
							</tbody>
						</table>
						<center>
							<button type="button" id="reset-btn" class="btn input-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default'?>" > <?=lang('lang.reset');?></button>
							<button type="submit" class="btn input-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-info'?>" ><i class="fa fa-check"></i> <?=lang('lang.save');?></button>
						</center>
					</form>
				</div>
			</div>

			<div class="panel-footer"></div>
		</div>
	</div>
</div>
<div class="modal fade in" id="duplicate_account_whitelist" tabindex="-1" role="dialog" aria-labelledby="label_duplicate_account_whitelist">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="label_duplicate_account_whitelist"></h4>
			</div>
			<div class="modal-body"></div>
			<div class="modal-footer"></div>
		</div>
	</div>
</div>

<script type="text/javascript">
	function account_whitelist() {
		var dst_url = "/user_management/viewDuplicateAccountWhitelist/";
		open_modal('duplicate_account_whitelist', dst_url, '<?=lang('whitelist')?>');
	}


	function open_modal(name, dst_url, title) {
		var main_selector = '#' + name;

		var label_selector = '#label_' + name;
		$(label_selector).html(title);

		var body_selector = main_selector + ' .modal-body';
		var target = $(body_selector);
		target.html('<center><img src="' + imgloader + '"></center>').delay(1000).load(dst_url);

		$(main_selector).modal('show');
	}

	function refresh_modal(name, dst_url, title) {
		var main_selector = '#' + name;
		var body_selector = main_selector + ' .modal-body';
		var target = $(body_selector);
		target.html('<center><img src="' + imgloader + '"></center>').delay(1000).load(dst_url);
	}
</script>
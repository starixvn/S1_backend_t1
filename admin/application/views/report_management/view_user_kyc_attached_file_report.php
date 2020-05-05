<!-- Start of Search Function -->
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary">

			<div class="panel-heading">
				<h4 class="panel-title">
					<i class="fa fa-search"></i> <?=lang("lang.search")?>
					<span class="pull-right">
						<a data-toggle="collapse" href="#collapseSearch" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info'?> <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
					</span>
				</h4>
			</div>

			<div id="collapseSearch" class="panel-collapse <?=$this->config->item('default_open_search_panel') ? '' : 'in collapse'?>">
				<form id="form-filter">
					<div class="panel-body">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<label for="search_last_update_date" class="control-label"><?=lang('last_update_date')?></label>
									<div class="input-group">
										<input id="search_last_update_date" class="form-control input-sm dateInput search_last_update_date" data-start="#last_update_date_from" data-end="#last_update_date_to" data-time="true"/>
										<span class="input-group-addon input-sm">
											<input type="checkbox" name="can_search_last_update_date" id="can_search_last_update_date" <?php echo ($conditions['can_search_last_update_date']) ? 'checked="checked"' : '' ?>" />
										</span>
									</div>
									<input type="hidden" name="last_update_date_from" id="last_update_date_from" value="<?=$conditions['last_update_date_from'];?>" class="search_last_update_date"/>
									<input type="hidden" name="last_update_date_to" id="last_update_date_to" value="<?=$conditions['last_update_date_to'];?>" class="search_last_update_date"/>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="username" class="control-label"><?=lang('player.01')?></label>
									<div>
										<input id="username" class="form-control input-sm" type="text" name="username"/>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="tagged" class="control-label"><?=lang('Tag')?></label>
									<select name="tag" id="tag" class="form-control input-sm">
										<option value='' ><?=lang('All')?></option>
										<?php if (!empty($tags)): ?>
											<?php foreach ($tags as $tag): ?>
												<option value="<?=$tag['tagId']?>" <?php echo $conditions['tag']  == $tag['tagId'] ? 'selected' : '' ?> ><?=$tag['tagName']?></option>
											<?php endforeach ?>
										<?php endif ?>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="risk_score_level" class="control-label"><?=lang('Risk Score Level')?></label>
									<select name="risk_score_level" id="risk_score_level" class="form-control input-sm">
										<option value='' ><?=lang('All')?></option>
										<?php if (!empty($risk_score_levels)): ?>
											<?php foreach ($risk_score_levels as $risk_score_level): ?>
												<option value="<?=$risk_score_level?>" <?php echo $conditions['risk_score_all'] == $risk_score_level ? 'selected' : '' ?> > <?=$risk_score_level?> </option>
											<?php endforeach ?>
										<?php endif ?>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<label for="kyc_level_id" class="control-label"><?=lang('KYC Level')?></label>
									<select name="kyc_level_id" id="kyc_level_id" class="form-control input-sm">
										<option value='' ><?=lang('All')?></option>
										<?php if (!empty($kyc_levels)): ?>
											<?php foreach ($kyc_levels as $kyc_level): ?>
												<option value="<?=$kyc_level['id']?>" <?php echo $conditions['kyc_level_id']  == $kyc_level['id'] ? 'selected' : '' ?> > <?=$kyc_level['kyc_lvl']?> / <?=$kyc_level['rate_code']?></option>
											<?php endforeach ?>
										<?php endif ?>
									</select>
								</div>
							</div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="id_card_number" class="control-label"><?=lang('ID Card Number')?></label>
                                    <div>
                                        <input id="id_card_number" class="form-control input-sm" type="text" name="id_card_number"/>
                                    </div>
                                </div>
                            </div>
							<?php foreach ($attachment_types as $attachment_type_key => $attachment_type): ?>
								<div class="col-md-3">
									<div class="form-group">
										<label for="<?=$attachment_type_key?>" class="control-label"><?=lang($attachment_type)?></label>
										<div class="input-group">
											<select name="<?=$attachment_type_key?>" id="<?=$attachment_type_key?>" class="form-control input-sm">
												<option value=""><?=lang('All')?></option>
												<?php foreach ($verifications as $key => $value): ?>
													<option value="<?=$key?>"><?=lang($value)?></option>
												<?php endforeach ?>
											</select>

											<span class="input-group-addon input-sm">
												<input type="checkbox" name="can_attachment[]" id="can_<?=$attachment_type_key?>" value="<?=$attachment_type_key?>" class="can_attachment_checkbox" checked />
											</span>
										</div>
									</div>
								</div>
							<?php endforeach ?>

						</div>
						<div class="row">
							<div class="col-md-3">
								<label class="checkbox-inline"><input type="checkbox" name="show_logs" id="show_logs" value="TRUE" class="show_logs" ><?=lang('Show Logs')?></label>
							</div>

						</div>
					</div>
					<div class="text-center">
						<input type="submit" form="search-form" id="search_main" value="<?=lang('lang.search')?>" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>"/>
						<input type="button" value="<?=lang('lang.reset')?>" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default'?>" onclick="window.location='/report_management/viewAttachedFileList';"/>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- End of Search Function -->

<!-- Start of Attached File List -->
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title" >
					<i class="icon-profile"></i> <?= lang('role.404') ?>
				</h3>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-bordered table-hover dataTable" id="data_tables_attached_file">
						<thead>
							<tr>
								<th><?=lang('ID');?></th>
								<th><?=lang('lang.action');?></th>
								<th><?=lang('player.01');?></th>
								<th><?=lang('last_update_date');?></th>
								<th><?=lang('ID Card Number');?></th>
								<?php
								$proof_attachment_types = $this->utils->getconfig('proof_attachment_type');

								if(!empty($proof_attachment_types) && is_array($proof_attachment_types)){
									foreach ($proof_attachment_types as $attachment_type_key => $attachment_type) {
										echo '<th>' . lang($attachment_type['description']) . '</ th>';
									}
								}
								?>
								<th><?=lang('Risk Level');?></th>
								<th><?=lang('KYC Level');?></th>
								<th><?=lang('Tag');?></th>
							</thead>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- End of Attached File List -->

	<!-- Modals -->
	<div class="modal fade in" id="mainModal" tabindex="-1" role="dialog" aria-labelledby="mainModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="mainModalLabel"></h4>
				</div>
				<div class="modal-body"></div>
				<div class="modal-footer"></div>
			</div>
		</div>
	</div>

	<form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
		<input name='json_search' type="hidden">
	</form>
	<!-- End of Modals -->
	<!-- Start of Custom JS -->
	<script type="text/javascript">
		$(document).ready(function(){
			$("#can_search_last_update_date").change(function() {
				var $search_last_update_date = $('.search_last_update_date');
				$search_last_update_date.prop('disabled',!this.checked);
			}).trigger('change');

			$(".can_attachment_checkbox").change(function() {

				$('#'+$(this).val()).attr('disabled',!this.checked);
			});

			var data_tables = $('#data_tables_attached_file').DataTable({
				order: [[3, 'desc']],
				lengthMenu: JSON.parse('<?=json_encode($this->utils->getConfig('default_datatable_lengthMenu'))?>'),
				pageLength: <?=$this->utils->getDefaultItemsPerPage()?>,
				searching: false,
				processing: true,
				serverSide: true,
				dom: "<'panel-body' <'pull-right'B><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
				buttons: [
					{
						extend: 'colvis',
						postfixButtons: [ 'colvisRestore' ],
            			className: '<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>',
					},
					{
						text: "<?php echo lang('CSV Export'); ?>",
						className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?> export-all-columns',
						action: function ( e, dt, node, config ) {

							var form_params=$('#form-filter').serializeArray();
								var d = {'extra_search': form_params, 'export_format': 'csv', 'export_type': export_type,'draw':1, 'length':-1, 'start':0};
								utils.safelog(d);
								$("#_export_excel_queue_form").attr('action', site_url('/export_data/exportPlayerAttachmentFileList'));
								$("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
								$("#_export_excel_queue_form").submit();
						}
					}
				],
				ajax: function (data, callback, settings) {
					data.extra_search = $('#form-filter').serializeArray();
					$.post(base_url + "api/playerAttachmentFileList", data, function(data) {
						callback(data);
					},'json');
				},
			});

			$("#search_main").on('click',function(){
				data_tables.ajax.reload();
			})


		});

		$('.export_excel').click(function(){
			var d = {'extra_search':$('#form-filter').serializeArray(), 'draw':1, 'length':-1, 'start':0};
			$.post(site_url('/export_data/exportPlayerAttachmentFileList'), d, function(data){
				if(data && data.success){
					$('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
				}else{
					alert('export failed');
				}
			});
		});

		function modal(load, title) {
			var target = $('#mainModal .modal-body');
			$('#mainModalLabel').html(title);
			target.html('<center><img src="' + imgloader + '"></center>').delay(1000).load(load);
			$('#mainModal').modal('show');

		}
	</script>
<!-- End of Custom JS
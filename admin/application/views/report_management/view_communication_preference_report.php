<div class="panel panel-primary hidden">
	<div class="panel-heading">
		<h4 class="panel-title">
			<i class="fa fa-search"></i> <?=lang("lang.search")?>
			<span class="pull-right">
				<a data-toggle="collapse" href="#collapseCommunicationPreferenceReport" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info'?> <?= $this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
			</span>
		</h4>
	</div>
	<div id="collapseCommunicationPreferenceReport" class="panel-collapse <?= $this->config->item('default_open_search_panel') ? '' : 'collapse in'?>">
		<div class="panel-body">
			<form id="form-filter" class="form-horizontal" method="post">
				<div class="row">
					<div class="col-md-3">
						<label class="control-label"><?=lang('lang.reqdate')?></label>
						<div class="input-group">
	                        <input id="search_date_input" class="form-control dateInput" data-start="#date_from" data-end="#date_to" data-time="true"/>
	                        <span class="input-group-addon input-sm">
	                                <input type="checkbox" name="search_date" id="search_date"  />
	                        </span>
	                    </div>
						<input type="hidden" id="date_from" name="date_from"/>
						<input type="hidden" id="date_to" name="date_to"/>
					</div>
					<div class="col-md-3">
						<label class="control-label"><?=lang('Player Username')?></label>
						<input type="text" name="player_username" class="form-control"/>
					</div>
					<div class="col-md-3">
						<label class="control-label"><?=lang('Affiliate Username')?></label>
						<input type="text" name="aff_username" class="form-control"/>
					</div>
					<div class="col-md-3">
						<label class="control-label"><?=lang('Group Level')?></label>
						<select class="form-control input-sm" name="group_level">
							<option value=""><?=lang('lang.selectall')?></option>
							<?php foreach ($allPlayerLevels as $val) : ?>
							<option value="<?= $val['vipsettingcashbackruleId'] ?>"><?= lang($val['groupName']) . '-' . lang($val['vipLevelName']) ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-6">
						<fieldset class=" "style="margin: 0px; padding:0px 10px 10px 15px;" >
						<label class=" checkbox-inline">
							<input type="checkbox" name="select_all" id="select_all" value="true" class=""/><?=lang('lang.selectall')?>
						</label>
						<!-- OGP-15401 remove "Strict Filtering" in searching condition -->
						<!-- <label class=" checkbox-inline">
							<input type="checkbox" name="strict_filtering" value="true" class=""/><?=lang('Strict Filtering')?>
						</label> -->
						<?php foreach ($config_comm_pref as $comm_pref_key => $comm_pref_value): ?>
							<label class=" checkbox-inline">
								<input type="checkbox" name="comm_pref_options[]" value="<?=$comm_pref_key?>" class="comm_pref_options"/><?=lang($comm_pref_value)?>
							</label>
						<?php endforeach ?>
					</fieldset>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12 text-right" style="padding-top: 20px">
						<input type="submit" value="<?=lang('lang.search')?>" id="search_main"class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-info'?>">
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="panel panel-primary">
	<div class="panel-heading">
		<h4 class="panel-title"><i class="icon-users"></i> <?=lang('Communication Preference Report')?> </h4>
	</div>
	<div class="panel-body">
	<div class="table-responsive">
		<table class="table table-bordered table-hover" id="myTable">
			<thead>
				<tr>
					<!-- <th>NO.</th> -->
					<th><?=lang('Date of Request')?></th>
					<th><?=lang('Username')?></th>
					<th><?=lang('Real Name')?></th>
					<th><?=lang('Affiliate Username')?></th>
					<th><?=lang('Player Level')?></th>
					<?php foreach ($config_comm_pref as $comm_pref_key => $comm_pref_value): ?>
					<th><?=lang($comm_pref_value)?></th>
					<?php endforeach ?>
				</tr>
			</thead>
		</table>
	</div>

	</div>
	<div class="panel-footer"></div>
</div>


<form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
    <input name='json_search' type="hidden">
</form>

<script type="text/javascript">
	$(document).ready(function(){

		$("#search_date").change(function() {
            if(this.checked) {
                $('#search_date_input').prop('disabled',false);
                $('#date_from').prop('disabled',false);
                $('#date_to').prop('disabled',false);
            }else{
                $('#search_date_input').prop('disabled',true);
                $('#date_from').prop('disabled',true);
                $('#date_to').prop('disabled',true);
            }
        }).trigger('change');

		$('#select_all').change(function(e){
			e.preventDefault();
			if($(this).is(":checked")){
				$('.comm_pref_options').prop('checked',true);
				return true;
			}

			$('.comm_pref_options').removeAttr('checked');

		});

		var dataTable = $('#myTable').DataTable({
			autoWidth: false,
			searching: false,
			dom: "<'panel-body' <'pull-right'B><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
			/*columnDefs: [
				{ className: 'text-right', targets: [] },

			],*/
			buttons: [
				{
					extend: 'colvis',
					postfixButtons: [ 'colvisRestore' ],
					className: '<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>',
				}
				<?php if ($export_report_permission) : ?>
				,{
					text: "<?php echo lang('CSV Export'); ?>",
					className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?> export-all-columns',
					action: function ( e, dt, node, config ) {

						var form_params=$('#form-filter').serializeArray();
							var d = {'extra_search': form_params, 'export_format': 'csv', 'export_type': export_type,'draw':1, 'length':-1, 'start':0};
							utils.safelog(d);
							$("#_export_excel_queue_form").attr('action', site_url('/export_data/exportCommunicationPreferenceReport'));
							$("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
							$("#_export_excel_queue_form").submit();
					}
				}
				<?php endif; ?>
			],
			order: [[ 0, "desc" ]],
			processing: true,
			serverSide: true,
			ajax: function (data, callback, settings) {
				data.extra_search = $('#form-filter').serializeArray();
				$.post(base_url + "api/communicationPreferenceReports", data, function(data) {
					callback(data);
				}, 'json');
			},
		});

		$('#form-filter').submit( function(e) {
			e.preventDefault();
			dataTable.ajax.reload();
		});

		$('.export_excel').click(function(){
			var d = {'extra_search':$('#form-filter').serializeArray(), 'draw':1, 'length':-1, 'start':0};
			$.post(site_url('/export_data/exportCommunicationPreferenceReport'), d, function(data){
				if(data && data.success){
					$('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
				}else{
					alert('export failed');
				}
			});
		});
	});
</script>

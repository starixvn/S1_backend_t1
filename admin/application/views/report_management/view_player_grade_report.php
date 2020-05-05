<div class="panel panel-primary hidden">
	<div class="panel-heading">
		<h4 class="panel-title">
			<i class="fa fa-search"></i> <?=lang("lang.search")?>
			<span class="pull-right">
				<a data-toggle="collapse" href="#collapsePlayerGradeReport" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info'?> <?= $this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
			</span>
		</h4>
	</div>
	<div id="collapsePlayerGradeReport" class="panel-collapse <?= $this->config->item('default_open_search_panel') ? '' : 'collapse in'?>">
		<div class="panel-body">
			<form id="form-filter" class="form-horizontal" method="post">
			<div class="row">
				<div class="col-md-6">
					<label class="control-label"><?=lang('report.sum02')?></label>
					<input class="form-control dateInput" data-start="#date_from" data-end="#date_to" data-time="true"/>
					<input type="hidden" id="date_from" name="date_from"/>
					<input type="hidden" id="date_to" name="date_to"/>
				</div>
			</div>
			<div class="row">
				<div class="col-md-3">
					<label class="control-label"><?=lang('report.gr01')?></label>
					<input type="text" name="username" class="form-control"/>
				</div>
				<div class="col-md-3">
					<label class="control-label"><?=lang('report.gr02')?></label>
					<select class="form-control input-sm" name="request_type">
						<option value=""><?=lang('lang.selectall')?></option>
						<?php foreach ($request_type_list as $val => $content) : ?>
						<option value="<?= $val ?>"><?= lang($content) ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-md-3">
					<label class="control-label"><?=lang('report.gr04')?></label>
					<select class="form-control input-sm" name="request_grade">
						<option value=""><?=lang('lang.selectall')?></option>
						<?php foreach ($behavior_list as $val => $content) : ?>
						<option value="<?= $val ?>"><?= lang($content) ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col-md-3">
					<label class="control-label"><?=lang('report.gr06')?></label>
					<select class="form-control input-sm" name="level_from">
						<option value=""><?=lang('lang.selectall')?></option>
						<?php foreach ($allPlayerLevels as $val) : ?>
						<option value="<?= $val['vipsettingcashbackruleId'] ?>"><?= lang($val['groupName']) . '-' . lang($val['vipLevelName']) ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-md-3">
					<label class="control-label"><?=lang('report.gr07')?></label>
					<select class="form-control input-sm" name="level_to">
						<option value=""><?=lang('lang.selectall')?></option>
						<?php foreach ($allPlayerLevels as $val) : ?>
						<option value="<?= $val['vipsettingcashbackruleId'] ?>"><?= lang($val['groupName']) . '-' . lang($val['vipLevelName']) ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<?php if(!$this->config->item('get_only_success_grade_report')) : ?>
					<div class="col-md-3">
						<label class="control-label"><?=lang('report.gr14')?></label>
						<select class="form-control input-sm" name="status">
							<option value="1"><?=lang('success')?></option>
							<option value="0"><?=lang('Failed')?></option>
						</select>
					</div>
				<?php endif; ?>

			</div>
			<div class="row">
				<div class="col-md-1" style="padding-top: 20px">
					<input type="submit" value="<?=lang('lang.search')?>" id="search_main"class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-info'?>">
				</div>
			</div>
			</form>
		</div>
	</div>
</div>

<div class="panel panel-primary">
	<div class="panel-heading">
		<h4 class="panel-title"><i class="icon-users"></i> <?=lang('Grade Report')?> </h4>
	</div>
	<div class="panel-body">
	<div class="table-responsive">
		<table class="table table-bordered table-hover" id="myTable">
			<thead>
				<tr>
					<th>NO.</th>
					<th><?=lang('report.gr01')?></th>
					<th><?=lang('report.gr02')?></th>
					<th><?=lang('report.gr03')?></th>
					<th><?=lang('report.gr04')?></th>
					<th><?=lang('report.gr05')?></th>
					<th><?=lang('report.gr06')?></th>
					<th><?=lang('report.gr07')?></th>
					<th><?=lang('report.gr08')?></th>
					<th><?=lang('report.gr09')?></th>
					<th><?=lang('report.gr10')?></th>
					<th><?=lang('report.gr11')?></th>
					<th><?=lang('report.gr12')?></th>
					<th><?=lang('report.gr13')?></th>
					<th><?=lang('report.gr14')?></th>
				</tr>
			</thead>
		</table>
	</div>

	</div>
	<div class="panel-footer"></div>
</div>

<?php if ($this->utils->isEnabledFeature('export_excel_on_queue')) : ?>
<form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
    <input name='json_search' type="hidden">
<?php endif; ?>

<script type="text/javascript">
	$(document).ready(function(){
		var dataTable = $('#myTable').DataTable({
			autoWidth: false,
			searching: false,
			dom: "<'panel-body' <'pull-right'B><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
			columnDefs: [
				{ className: 'text-right', targets: [] },
				{ visible: false, targets: [9, 10, 11] },
			],
			buttons: [
				{
					extend: 'colvis',
					postfixButtons: [ 'colvisRestore' ],
            		className: '<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>',
				}
				<?php if ($export_report_permission && $this->utils->isEnabledFeature('export_excel_on_queue')) : ?>
				,{
					text: "<?php echo lang('CSV Export'); ?>",
					className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?> export-all-columns',
					action: function ( e, dt, node, config ) {

						var form_params=$('#form-filter').serializeArray();
							var d = {'extra_search': form_params, 'export_format': 'csv', 'export_type': export_type,'draw':1, 'length':-1, 'start':0};
							utils.safelog(d);
							$("#_export_excel_queue_form").attr('action', site_url('/export_data/grade_report'));
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
				$.post(base_url + "api/playerGradeReports", data, function(data) {
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
			$.post(site_url('/export_data/player_reports'), d, function(data){
				if(data && data.success){
					$('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
				}else{
					alert('export failed');
				}
			});
		});
	});
</script>

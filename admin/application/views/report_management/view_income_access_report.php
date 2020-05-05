<div class="panel panel-primary hidden">
	<div class="panel-heading">
		<h4 class="panel-title">
			<i class="fa fa-search"></i> <?=lang("lang.search")?>
			<span class="pull-right">
				<a data-toggle="collapse" href="#collapseIncomeAccessReport" class="btn btn-info btn-xs <?= $this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
			</span>
		</h4>
	</div>
	<div id="collapseIncomeAccessReport" class="panel-collapse <?= $this->config->item('default_open_search_panel') ? '' : 'collapse in'?>">
		<div class="panel-body">
			<form id="form-filter" class="form-horizontal" method="post">
			<div class="row">
				<div class="col-md-6">
					<label class="control-label"><?=lang('lang.reqdate')?></label>
					<div class="input-group">
                        <input id="search_date_input" class="form-control dateInput" data-start="#date_from" data-end="#date_to" data-time="true"/>
                        <span class="input-group-addon input-sm">
                            <input type="checkbox" name="search_date" id="search_date"  <?=$this->input->get('date_from') || $this->input->get('date_to') ? 'checked' : ''?>/>
                        </span>
                    </div>
					<input type="hidden" id="date_from" name="date_from" value="<?=$this->input->get('date_from')?>" />
					<input type="hidden" id="date_to" name="date_to" value="<?=$this->input->get('date_to')?>"/>
				</div>
			</div>
			<div class="row">
				<div class="col-md-3">
					<label class="control-label"><?=lang('Player Username')?></label>
					<input type="text" name="username" class="form-control" value="<?=$this->input->get('username')?>" />
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-1" style="padding-top: 20px">
					<input type="submit" value="<?=lang('lang.search')?>" id="search_main"class="btn btn-info btn-sm">
				</div>
			</div>
			</form>
		</div>
	</div>
</div>

<?php if ($this->permissions->checkPermissions('view_income_access_signup_report')): ?>
	
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h4 class="panel-title"><i class="icon-users"></i> <?=lang('Income Access Signup Report')?> </h4>
		</div>
		<div class="panel-body">
		<div class="table-responsive">
			<table class="table table-bordered table-hover" id="signupTable">
				<thead>
					<tr>

						<?php 

						$signup_header_config = $this->utils->getConfig('ia_daily_signup_csv_headers');

						foreach ($signup_header_config as $signup_header_config_key => $config_value): ?>
						<th><?=lang($config_value)?></th>
						<?php endforeach ?>
					</tr>
				</thead>
			</table>
		</div>

		</div>
		<div class="panel-footer"></div>
	</div>
<?php endif ?>

<?php if ($this->permissions->checkPermissions('view_income_access_sales_report')): ?>
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h4 class="panel-title"><i class="icon-users"></i> <?=lang('Income Access Sales Report')?> </h4>
		</div>
		<div class="panel-body">
		<div class="table-responsive">
			<table class="table table-bordered table-hover" id="salesTable">
				<thead>
					<tr>
						<?php 

						$sales_header_config = $this->utils->getConfig('ia_daily_sales_csv_headers');

						foreach ($sales_header_config as $sales_header_config_key => $config_value): ?>
						<th><?=lang($config_value)?></th>
						<?php endforeach ?>
					</tr>
				</thead>
			</table>
		</div>

		</div>
		<div class="panel-footer"></div>
	</div>
<?php endif ?>


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

<?php if ($this->permissions->checkPermissions('view_income_access_signup_report')): ?>
		var signupDataTable = $('#signupTable').DataTable({
			autoWidth: false,
			searching: false,
			dom: "<'panel-body' <'pull-right'B><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
			/*columnDefs: [
				{ className: 'text-right', targets: [] },
				
			],*/
			buttons: [
				{
					extend: 'colvis',
					postfixButtons: [ 'colvisRestore' ]
				}
				<?php if ($export_signup_report_permission) : ?>
				,{
					text: "<?php echo lang('CSV Export'); ?>",
					className:'btn btn-sm btn-primary export-all-columns',
					action: function ( e, dt, node, config ) {

						var form_params=$('#form-filter').serializeArray();
							var d = {'extra_search': form_params, 'export_format': 'csv', 'export_type': export_type,'draw':1, 'length':-1, 'start':0};
							utils.safelog(d);
							$("#_export_excel_queue_form").attr('action', site_url('/export_data/exportIncomeAccessSignupReports'));
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
				$.post(base_url + "api/incomeAccessSignupReports", data, function(data) {
					callback(data);
				}, 'json');
			},
		});
<?php endif ?>
<?php if ($this->permissions->checkPermissions('view_income_access_sales_report')): ?>
		var salesDataTable = $('#salesTable').DataTable({
			autoWidth: false,
			searching: false,
			dom: "<'panel-body' <'pull-right'B><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
			/*columnDefs: [
				{ className: 'text-right', targets: [] },
				
			],*/
			buttons: [
				{
					extend: 'colvis',
					postfixButtons: [ 'colvisRestore' ]
				}
				<?php if ($export_sales_report_permission) : ?>
				,{
					text: "<?php echo lang('CSV Export'); ?>",
					className:'btn btn-sm btn-primary export-all-columns',
					action: function ( e, dt, node, config ) {

						var form_params=$('#form-filter').serializeArray();
							var d = {'extra_search': form_params, 'export_format': 'csv', 'export_type': export_type,'draw':1, 'length':-1, 'start':0};
							utils.safelog(d);
							$("#_export_excel_queue_form").attr('action', site_url('/export_data/exportIncomeAccessSalesReports'));
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
				$.post(base_url + "api/incomeAccessSalesReports", data, function(data) {
					callback(data);
				}, 'json');
			},
		});
<?php endif ?>
		$('#form-filter').submit( function(e) {
			e.preventDefault();
			signupDataTable.ajax.reload();
			salesDataTable.ajax.reload();
		});

	});
</script>

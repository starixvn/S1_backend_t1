<!-- Search Form -->
<style type="text/css">
	.Ticket{
		padding: 2px 15px;
		display: inline-block;
	}
	.Ticket > .fa-circle{
		color: #FF9900;
		border: 1px solid #fff;
		border-radius: 100%;
		box-shadow: 0 2px 2px rgba(0,0,0,0.4);
	}
	#DonutChart svg{
		height: 190px !important;
	}
	.sched-table tr td:first-child{
		width: 25%;
	}
	.ticket-legend{
		background: rgba(0,0,0,0.05);
		border-radius: 0 0 5px 5px;
	}
</style>
<form class="form-horizontal">
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h4 class="panel-title">
				<i class="fa fa-list"></i>&nbsp;<?= lang('Send Data Scheduler') ?>
				<?php if ($this->permissions->checkPermissions('add_kingrich_data_scheduler')): ?>
					<span class="pull-right">
						<button type="button" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info' ?>" onclick="modal('/marketing_management/kingrich_load_add_item','<?=lang('Add Schedule')?>')"><?= lang('Add') ?></button>
		        	</span>
	        	<?php endif; ?>
			</h4>
			
		</div>
	    <div class="panel-body">
			<div class="col-md-6 show" style="display: none;">
				<fieldset style="padding-bottom: 8px">
					<legend>
						<label class="control-label"><?=lang('Status');?></label>
					</legend>
					<div class="row">
						<div class="col-md-12">
							<table class="table table-bordered sched-table">
								<tr>
									<th><?= lang('Status') ?></th>
									<th><?= lang('Description') ?></th>
								</tr>
								<tr>
									<td style="color: #FF9900;"><?= lang('Pending') ?></td>
									<td><?= lang('Default status of new schedule created.') ?></td>
								</tr>
								<tr>
									<td style="color: #00CC33;"><?= lang('On-going') ?></td>
									<td><?= lang('The set schedule for send out is still running.') ?></td>
								</tr>
								<tr>
									<td style="color: #FF0000;"><?= lang('Paused') ?></td>
									<td><?= lang('Manually paused or process is interrupted.') ?></td>
								</tr>
								<tr>
									<td><?= lang('Stopped') ?></td>
									<td><?= lang('Once the status is set to "Stop", you cannot run it again. You can only stop the schedule, if the current status is "Paused" or "Pending".') ?></td>
								</tr>
								<tr>
									<td><?= lang('Done') ?></td>
									<td><?= lang('When the process is finished, status is set to "Done".') ?></td>
								</tr>
							</table>
						</div>
					</div>
				</fieldset>
			</div>
			<div class="col-md-6 show" style="display: none;">
				<fieldset>
					<legend>
						<label class="control-label"><?=lang('Summary');?></label>
					</legend>
					<div class="row">
						<div id="DonutChart"></div>
						<div class="text-center ticket-legend">
							<div class="panel-body">
								<div class="ColorTicket">
									<div class="Ticket"><i class="fas fa-circle" style="color: #FF9900;"></i> <span><?= lang('Pending') ?></span></div>
									<div class="Ticket"><i class="fas fa-circle" style="color: #00CC33;"></i> <span><?= lang('On-going') ?></span></div>
									<div class="Ticket"><i class="fas fa-circle" style="color: #FF0000;"></i> <span><?= lang('Paused') ?></span></div>
								</div>
							</div>
						</div>

					</div>
				</fieldset>
			</div>

		</div>
	</div>
</form>

<div class="panel panel-primary">
	<div class="panel-heading">
		<h4 class="panel-title"><i class="fa fa-list"></i>&nbsp;<?= lang('Schedule History / Tracker') ?></h4>
	</div>
	<div class="panel-body" >
		<div class="row">
			<form action="/marketing_management/kingrich_scheduler" id="search-form" method="get" enctype="multipart/form-data">
	    		<div class="col-md-4">
					<label class="control-label" for="reportrange"><?=lang('report.sum02');?></label>
					<input type="text" id="reportrange" class="form-control input-sm dateInput" data-start="#by_date_from" data-end="#by_date_to" data-time="true"/>
					<input type="hidden" id="by_date_from" name="by_date_from" value="<?php echo $conditions['by_date_from']; ?>" />
					<input type="hidden" id="by_date_to" name="by_date_to"  value="<?php echo $conditions['by_date_to']; ?>"/>
				</div>
				<?php if( !empty($kingrich_currency_branding) && $this->config->item('multiple_currency_enabled') ) :?>
					<div class="col-md-2">
						<label class="control-label" for="flag"><?=lang('Currency');?> </label>
						<select class="form-control input-sm" name="by_currency" id="by_currency" >
							<option value=""><?=lang('lang.selectall');?></option>
							<?php foreach ($kingrich_currency_branding as $key => $value) : ?>
								<option value="<?=$key?>" <?php echo $conditions['by_currency']==$key ? 'selected="selected"' : '' ; ?>><?=$key?></option>
							<?php endforeach; ?>
						</select>
					</div>
				<?php endif; ?>
				<div class="col-md-2">
					<label class="control-label" for="by_status"><?=lang('Status');?> </label>
					<select class="form-control input-sm" name="by_status" id="by_status">
						<option value="" ><?=lang('lang.selectall');?></option>
						<?php if( !empty($kingrich_scheduler_status)) :?>
							<?php foreach ($kingrich_scheduler_status as $key => $value) : ?>
								<option value="<?=$key?>" <?php echo $conditions['by_status'] == $key ? 'selected="selected"' : '' ; ?> ><?= lang($value['label'])?></option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
				</div>
				<div class="col-md-2">
			    	<span class="input-group-btn" style="padding-top: 25px;">
						<button type="submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary' ?>" id="btn-submit" style="height: 40px; width: 100%;"><?= lang('Search') ?></button>
					</span>
				</div>
			</form>
		</div>
		<div class="table-responsive">
			<table id="scheduler-table" class="table table-bordered">
				<thead>
				<tr>
                    <th class=""><?=lang('Date to Process');?></th>
                    <th class=""><?=lang('Currency');?></th>
                    <th class=""><?=lang('Status');?></th>
                    <th class=""><?=lang('Created By');?></th>
                    <th class=""><?=lang('Date Created');?></th>
                    <th class=""><?=lang('Action');?></th>
				</tr>
				</thead>
			</table>
		</div>
	</div>
	<div class="panel-footer"></div>
</div>
<!-- MAIN MODAL -->
<div class="modal fade in" id="mainModal" role="dialog" aria-labelledby="mainModalLabel">
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

<script type="text/javascript" src="/resources/third_party/raphael/2.1/raphael-min.js"></script>
<script type="text/javascript" src="/resources/third_party/morris/0.5/morris.min.js"></script>

<?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
	<form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
		<input name='json_search' type="hidden">
	</form>
<?php }?>

<script type="text/javascript">
	var export_type="<?php echo $this->utils->isEnabledFeature('export_excel_on_queue') ? 'queue' : 'direct';?>";
	$(document).ready(function(){
		 var dataTable = $('#scheduler-table').DataTable({
		 	"pageLength": 10,
	        autoWidth: false,
	        searching: true,
	        dom: "<'panel-body' <'pull-right'B><'pull-right'f><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
	        buttons: [
	        	{
					extend: 'colvis',
					postfixButtons: [ 'colvisRestore' ],
					className:'<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn btn-sm btn-linkwater' : 'btn btn-sm btn-default' ?>'
				}
				<?php if( $this->permissions->checkPermissions('export_game_logs') ) { ?>
				,{

					text: "<?php echo lang('CSV Export'); ?>",
					className:'<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn btn-sm btn-portage' : 'btn btn-sm btn-primary' ?>',
					action: function ( e, dt, node, config ) {
						var form_params=$('#search-form').serializeArray();

						var d = {'extra_search': form_params, 'export_format': 'csv', 'export_type': export_type,
							'draw':1, 'length':-1, 'start':0};

						$("#_export_excel_queue_form").attr('action', site_url('/export_data/kingrich_scheduler_report'));
						$("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
						$("#_export_excel_queue_form").submit();

					}
				}
				<?php } ?>
			],
	        order: [[4, 'asc']],
	        ajax: function (data, callback, settings) {

	            data.extra_search = [
	                {
	                    'name':'by_date_from',
	                    'value':$('#by_date_from').val()
	                },
	                {
	                    'name':'by_date_to',
	                    'value':$('#by_date_to').val()
	                },
	                {
	                    'name':'by_status',
	                    'value':$('#by_status').val()
	                },
	                <?php if( !empty($kingrich_currency_branding) && $this->config->item('multiple_currency_enabled') ) :?>
	                {
	                    'name':'by_currency',
	                    'value':$('#by_currency').val()
	                }
	                <?php endif; ?>
	            ];
	            
	            $.post(base_url + 'api/kingrich_scheduler_report/', data, function(data) {
	                callback(data);
	            },'json');
	        }
	    });

	    $('#btn-submit').click( function() {
	        dataTable.ajax.reload();
	    });

	});
</script>
<script type="text/javascript">
	var cat_colors = {
		'Pending'	: "#FF9900",
		'On-going'	: "#00CC33",
		'Paused'	: "#FF0000"
	};
	var act_players_by_type = <?= json_encode($total_active_schedule['morris']) ?>;
	var cat_cues = <?= json_encode($total_active_schedule['cat_cues']) ?>;
	var actpl_colors = [];
	for (var i in cat_cues) {
		actpl_colors.push(cat_colors[cat_cues[i]]);
	}
	// DONUT CHART
	var donut = new Morris.Donut({
      element: 'DonutChart',
      resize: true,
      colors: actpl_colors ,
      data: act_players_by_type ,
      hideHover: 'auto',
    });

	//$("#DonutChart").css("height","150");

    function modal(load, title) {
	    var target = $('#mainModal .modal-body');
	    $('#mainModalLabel').html(title);
	    target.html('<center><img src="' + imgloader + '"></center>').delay(1000).load(load);
	    $('#mainModal').modal('show');
	}

	function update_status(scheduler_id,status_to,confirmation_msg) {
		if (confirm(confirmation_msg) == true) {
			$.post("/marketing_management/kingrich_update_status_scheduler/"+scheduler_id+"/"+status_to, function(result){
				if( result ) {
					alert(result['msg']);
					location.reload(true);
				}
	       	});
		}
	}
</script>
<div class="clearfix">
	<form class="form-horizontal" id="search-form" method="get" role="form">
		<input type="hidden" id="by_scheduler_id" name="by_scheduler_id" value="<?= $schedule_id ?>" />
	</form>	
	<table id="scheduler-summary-logs" class="table table-striped table-hover table-condensed table-bordered">
		<thead>
			<th class=""><?=lang('Created Date')?></th>
			<th class="right-text-col"><?=lang('Batch Transaction ID');?></th>
			<th class="right-text-col"><?=lang('Status');?></th>
			<th class="right-text-col"><?=lang('Total');?></th>
		</thead>
		<tbody>
		</tbody>
		<tfoot>
		<tr>
		    <th colspan="3" style="text-align: right"><?= lang('Overall Total') ?></th>
		    <th style="text-align: right"><span class="overall-total">0</span></th>
		</tr>
		</tfoot>
	</table>
</div>

<?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
	<form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
		<input name='json_search' type="hidden">
	</form>
<?php }?>

<script type="text/javascript">
	var export_type="<?php echo $this->utils->isEnabledFeature('export_excel_on_queue') ? 'queue' : 'direct';?>";
	$(document).ready(function(){
		 var dataTable = $('#scheduler-summary-logs').DataTable({
		 	"pageLength": 10,
	        autoWidth: false,
	        searching: true,
	        dom: "<'panel-body' <'pull-right'B><'pull-right'f><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
	        buttons: [
	        	{
					extend: 'colvis',
					postfixButtons: [ 'colvisRestore' ],
					className:'btn btn-sm '
				}
				<?php if( $this->permissions->checkPermissions('export_game_logs') ) { ?>
				,{

					text: "<?php echo lang('CSV Export'); ?>",
					className:'btn btn-sm btn-primary',
					action: function ( e, dt, node, config ) {
						var form_params=$('#search-form').serializeArray();

						var d = {'extra_search': form_params, 'export_format': 'csv', 'export_type': export_type,
							'draw':1, 'length':-1, 'start':0};

						$("#_export_excel_queue_form").attr('action', site_url('/export_data/kingrich_scheduler_summary_logs'));
						$("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
						$("#_export_excel_queue_form").submit();

					}
				}
				<?php } ?>
			],
	        order: [[0, 'asc']],
	        ajax: function (data, callback, settings) {

	            data.extra_search = [
	                {
	                    'name':'by_scheduler_id',
	                    'value':$('#by_scheduler_id').val()
	                }
	            ];
	            
	            $.post(base_url + 'api/kingrich_scheduler_summary_logs/', data, function(data) {
	                callback(data);
	                $('.overall-total').text('0');
	                if ( dataTable.rows( { selected: true } ).indexes().length === 0 ) {
					    dataTable.buttons().disable();
					} else {
						dataTable.buttons().enable();
						$('.overall-total').text(data.summary[0].overall_total);
						
					}
	            },'json');
	        }
	    });

	});
</script>
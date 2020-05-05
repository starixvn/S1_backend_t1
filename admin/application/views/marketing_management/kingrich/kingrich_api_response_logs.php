<form class="form-horizontal" id="search-form" method="get" role="form">
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h4 class="panel-title"><i class="fa fa-list"></i>&nbsp;API Response Logs <?//=lang('player.ui48');?> </h4>
		</div>
	    <div class="panel-body">
	        <div class="text-left">
	            <div class="form-inline">
	                <input type="text" id="reportrange" class="form-control input-sm dateInput inline" data-start="#dateRangeValueStart" data-end="#dateRangeValueEnd" data-time="true"/>
	                <input type="hidden" id="dateRangeValueStart" name="dateRangeValueStart"/>
	                <input type="hidden" id="dateRangeValueEnd" name="dateRangeValueEnd"/>
	                <input type="button" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary' ?>" id="btn-submit" value="<?=lang('lang.search');?>"/>
	            </div>
	        </div>
	        <hr/>
	        <table id="api-response-table" class="table table-striped table-hover table-condensed">
	            <thead>
	                <th><?=lang('Batch Transaction ID')?></th>
	                <th><?=lang('Created Date');?></th>
	                <th><?=lang('Status');?></th>
	            </thead>
	        </table>
	    </div>
	</div>
</form>
<?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
<form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
<input name='json_search' type="hidden">
</form>
<?php }?>
<script type="text/javascript">
	$(document).ready(function(){
		 var dataTable = $('#api-response-table').DataTable({
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
						// $('#search-form input[name=export_format]').val('csv');
						// $('#search-form input[name=export_type]').val('direct');

                        var d = {'extra_search':$('#search-form').serializeArray(), 'draw':1, 'length':-1, 'start':0};

						$("#_export_excel_queue_form").attr('action', site_url('/export_data/kingrichApiResponseLogs'));
						$("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
						$("#_export_excel_queue_form").submit();

					}
				}
				<?php } ?>
			],
	        order: [[1, 'desc']],
	        ajax: function (data, callback, settings) {

	            data.extra_search = [
	                {
	                    'name':'dateRangeValueStart',
	                    'value':$('#dateRangeValueStart').val()
	                },
	                {
	                    'name':'dateRangeValueEnd',
	                    'value':$('#dateRangeValueEnd').val()
	                }
	            ];

	            $.post(base_url + 'api/kingrich_api_response_logs/', data, function(data) {
	                callback(data);
	                if ( dataTable.rows( { selected: true } ).indexes().length === 0 ) {
					    dataTable.buttons().disable();
					} else {
						dataTable.buttons().enable();
					}
	            },'json');
	        }
	    });

	    $('#btn-submit').click( function() {
	        dataTable.ajax.reload();
	    });

	    ATTACH_DATATABLE_BAR_LOADER.init('api-response-table');
	});

</script>
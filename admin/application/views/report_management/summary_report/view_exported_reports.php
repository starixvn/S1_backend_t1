<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title"><i class="fa fa-table"></i> <?=lang('pay.transactions')?></h3>
	</div>
	<div class="panel-body">
		<table id="exported-transaction-table" class="table table-bordered table-hover" 
			data-ajax="/api/transactionsReport" 
			data-server-side="true" 
			data-processing="true" 
			data-searching="false" 
			data-order="[[0, &quot;desc&quot;]]"
		>
            <thead>
                <tr>
                    <th data-name="created_at"><?=lang('lang.date')?></th>
                    <th data-name="filepath"><?=lang('tool.am19')?></th>
               </tr>
            </thead>
      	</table>
	</div>
</div>

<script type="text/javascript" language="javascript" >
	$(document).ready( function() {
		$('#exported-transaction-table').DataTable({
			'columnDefs': [
				{
					'render' : function(data, type, row) {
						return '<a href="/report_management/downloadTransactionReport/'+data+'">'+data+'</a>';
					},
					'targets' : 1,
				},
			],
		});
	});
</script>
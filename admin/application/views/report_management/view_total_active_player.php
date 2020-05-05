

<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i><?=lang('lang.search')?><span class="pull-right">
                <a data-toggle="collapse" href="#collapsePromotionReport" class="btn btn-info btn-xs"></a>
            </span>
        </h4>
    </div>

    <div id="collapsePromotionReport" class="panel-collapse ">
        <div class="panel-body">
        	<form  id="form-filter" method="GET" class="form">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label class="control-label"><?=lang('Date Range')?></label>
                        <input id="search_registration_date" class="form-control input-sm dateInput" data-start="#date_start" data-end="#date_end" data-time="false" autocomplete="false" />
                        <input type="hidden" id="date_start" name="date_start" value="<?= $conditions['date_start']; ?>">
                        <input type="hidden" id="date_end" name="date_end" value="<?= $conditions['date_end']; ?>">
                    </div>
                    <div class="form-group col-md-3">
                        <label class="control-label"><?=lang('Player Username')?> : </label>
                        <input type="text" name="username" class="form-control" value="<?=$conditions['username']?>" autocomplete="off" />
                    </div>
                </div>
                <div class="row">
					<div class="col-md-1" style="padding-top: 20px">
						<input type="submit" value="<?=lang('lang.search')?>" id="search_main" class="btn btn-info btn-sm">
					</div>
				</div>

        	</form>
        </div>
    </div>

</div>

<div class="panel panel-primary">
	<div class="panel-heading custom-ph">
		<h4 class="panel-title">
			<i class="icon-pie-chart"></i>
			<?=lang('Active Player Report')?>
		</h4>
	</div>
	<div class="panel-body">
		<table class="table table-condensed table-bordered table-hover" id="view-active-players-report">
			<thead>
				<tr>
					<th data-orderable="true"><?=lang('Date')?></th>
					<th data-orderable="true"><?=lang('Active Players')?></th>
					<th data-orderable="true"><?=lang('Affiliate')?></th>
					<th data-orderable="true"><?=lang('Agent')?></th>
					<th data-orderable="true"><?=lang('pay.referrer')?></th>
				</tr>
			</thead>
			<tfoot id="report-footer" class="hidden">
				<tr>
					<td colspan="5" class="text-right">
						<div id="report-summary"></div>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
	<div class="panel-footer"></div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('#active_player_report').addClass('active');
	});

	$(function(){
		let dataTable = $('#view-active-players-report').DataTable({
            dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            autoWidth: false,
            searching: false,
            ordering: true,
            iDisplayLength: 50,
            <?php if ($this->utils->isEnabledFeature('column_visibility_report')){ ?>
                stateSave: true,
            <?php } else { ?>
                stateSave: false,
            <?php } ?>

            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ]
                }
            ],
            columnDefs: [
                // { sortable: false, targets: notSortableCols },
                // { className: 'text-right', targets: balanceCols },
            ],
            "order": [ 1, 'asc' ],
            
            // SERVER-SIDE PROCESSING
            processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {
                var formData = $('#form-filter').serializeArray();
                data.extra_search = formData;
                $.post(base_url + "api/showActivePlayers", data, function(data) {
                	let summary = data.summary;
                	let objectLength = Object.keys(summary).length;

                	if (objectLength > 0) {
                		$('#report-footer').removeClass('hidden');

                		for (let key in summary) {
                			let summaryItems = summary[key];

                			$('#report-summary').append('<div><h6><b>' + summaryItems["description"] + ' : ' + summaryItems["total_count"] + '</b></h6>');
                			delete summaryItems["total_count"];
                			delete summaryItems["description"];

                			if (key != 'direct_players_total') {
	                			for (let index in summaryItems) {
	                				$('#report-summary').append('<div>' + index + ': ' + summaryItems[index] + '</div>');
	                			}
	                		}
	                		$('#report-summary').append('</div>');
                		}
                	}

                    callback(data);
                    if ( dataTable.rows( { selected: true } ).indexes().length === 0 ) {
                        dataTable.buttons().disable();
                    }
                    else {
                        dataTable.buttons().enable();
                    }
                }, 'json');
           }
        });


		// var q = '<?=$_SERVER['QUERY_STRING']?>';
		// var dataTable = $('#view-active-players-report').DataTable({
		// 	"columnDefs": [
		// 		{ 
		// 			"targets": [ 0 ],
		// 			"visible": false
		// 		}
		// 	],
		// 	ajax: '/api/showActivePlayers?' + q,
		// 	"pageLength": 20,
		// 	"order": [[0, 'asc']]
		// });

	});
</script>


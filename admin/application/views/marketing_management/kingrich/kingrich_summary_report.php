<!-- Search Form -->
<form class="form-horizontal" id="search-form" method="get" role="form">
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h4 class="panel-title"><i class="fa fa-list"></i>&nbsp;Summary Report <?//=lang('player.ui48');?> </h4>
		</div>
	    <div class="panel-body">
			<div class="col-md-4">
				<label class="control-label" for="search_game_date"><?=lang('report.sum02');?></label>
				<input type="text" id="reportrange" class="form-control input-sm dateInput" data-start="#dateRangeValueStart" data-end="#dateRangeValueEnd"/>
				<input type="hidden" id="dateRangeValueStart" name="dateRangeValueStart"/>
				<input type="hidden" id="dateRangeValueEnd" name="dateRangeValueEnd"/>
			</div>
			<div class="col-md-2">
				<label class="control-label" for="flag"><?=lang('Report Interval');?> </label>
				<select class="form-control input-sm" name="by_report_interval" id="by_report_interval">
					<option value="daily" ><?=lang('lang.daily');?></option>
					<option value="weekly" ><?=lang('lang.weekly');?></option>
					<option value="monthly" ><?=lang('lang.monthly');?></option>
				</select>
			</div>
			<div class="col-md-2">
					<label class="control-label" for="by_game_type_globalcom"><?=lang('Game Type');?> </label>
					<select class="form-control input-sm" name="by_game_type_globalcom" id="by_game_type_globalcom">
						<option value="" ><?=lang('lang.selectall');?></option>
						<?php if(!empty($this->config->item('kingrich_gametypes'))) : ?>
							<?php foreach ($this->config->item('kingrich_gametypes') as $key => $value): ?>
								<option value="<?= $key ?>"><?= $key ?></option>
							<?php endforeach ?>
						<?php endif; ?>
					</select>
				</div>
		<?php if( !empty($kingrich_currency_branding) && $this->config->item('multiple_currency_enabled') ) :?>
			<div class="col-md-2">
				<label class="control-label" for="flag"><?=lang('Currency');?> </label>
				<select class="form-control input-sm" name="currency" id="currency" required="required" >
					<option value=""><?=lang('lang.selectall');?></option>
					<?php foreach ($kingrich_currency_branding as $key => $value) : ?>
						<option value="<?=$key?>"><?=$key?></option>
					<?php endforeach; ?>
				</select>
			</div>
		<?php endif; ?>
	    </div>

		<div class="panel-footer text-right">

			<input type="button" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary' ?>" id="btn-submit" value="<?=lang('lang.search');?>"/>
		</div>
	    
	</div>
</form>

<!-- Table -->
<div class="panel panel-primary">
	<div class="panel-heading">
		<h4 class="panel-title"><i class="fa fa-list"></i>&nbsp;</h4>
	</div>
	<div class="panel-body">
		<table id="api-response-table" class="table table-striped table-hover table-condensed table-bordered">
			<thead>
				<th class=""><?=lang('Settlement Date')?></th>
				<th class="right-text-col"><?=lang('Bet Amount');?></th>
				<th class="right-text-col"><?=lang('Debit Amount');?></th>
				<th class="right-text-col"><?=lang('Credit Amount');?></th>
				<th class="right-text-col"><?=lang('Net Amount');?></th>
				<th class="right-text-col"><?=lang('Number of Bets');?></th>
			</thead>
			<tbody>
			</tbody>
			<tfoot>
			<tr>
				<th><?=lang('Total')?></th>
			    <th style="text-align: right"><span class="bet-amount">0.00</span></th>
			    <th style="text-align: right"><span class="debit-amount">0.00</span></th>
			    <th style="text-align: right"><span class="credit-amount">0.00</span></th>
			    <th style="text-align: right"><span class="net-amount">0.00</span></th>
			    <th style="text-align: right"><span class="number-of-bets">0</span></th>
			</tr>
			</tfoot>
		</table>
	</div>
</div>

<?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
<form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
<input name='json_search' type="hidden">
</form>
<?php }?>
<script type="text/javascript">
	var export_type="<?php echo $this->utils->isEnabledFeature('export_excel_on_queue') ? 'queue' : 'direct';?>";
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
						var form_params=$('#search-form').serializeArray();

						var d = {'extra_search': form_params, 'export_format': 'csv', 'export_type': export_type,
							'draw':1, 'length':-1, 'start':0};

						$("#_export_excel_queue_form").attr('action', site_url('/export_data/kingrich_summary_report'));
						$("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
						$("#_export_excel_queue_form").submit();

					}
				}
				<?php } ?>
			],
			columnDefs: [
				{ className: 'text-right', targets: [1,2,3,4,5] },
			],
	        order: [[0, 'asc']],
	        ajax: function (data, callback, settings) {

	            data.extra_search = [
	                {
	                    'name':'dateRangeValueStart',
	                    'value':$('#dateRangeValueStart').val()
	                },
	                {
	                    'name':'dateRangeValueEnd',
	                    'value':$('#dateRangeValueEnd').val()
	                },
	                {
	                    'name':'by_game_type_globalcom',
	                    'value':$('#by_game_type_globalcom').val()
	                },
	                {
	                    'name':'by_report_interval',
	                    'value':$('#by_report_interval').val()
	                },
					<?php if( !empty($kingrich_currency_branding) && $this->config->item('multiple_currency_enabled') ) :?>
	                {
	                    'name':'currency',
	                    'value':$('#currency').val()
	                }
	                <?php endif; ?>
	            ];
	            
	            $.post(base_url + 'api/kingrich_summary_report/', data, function(data) {
	                callback(data);
	                $('.bet-amount').text('0.00');
					$('.debit-amount').text('0.00');
					$('.credit-amount').text('0.00');
					$('.net-amount').text('0.00');
					$('.number-of-bets').text('0');
	                if ( dataTable.rows( { selected: true } ).indexes().length === 0 ) {
					    dataTable.buttons().disable();
					} else {
						dataTable.buttons().enable();
						$('.bet-amount').text(addCommas(parseFloat(data.summary[0].total_sum_bet_amount).toFixed(2)));
						$('.debit-amount').text(addCommas(parseFloat(data.summary[0].total_sum_debit_amount).toFixed(2)));
						$('.credit-amount').text(addCommas(parseFloat(data.summary[0].total_sum_credit_amount).toFixed(2)));
						$('.net-amount').text(addCommas(parseFloat(data.summary[0].total_sum_net_amount).toFixed(2)));
						$('.number-of-bets').text(data.summary[0].total_sum_number_of_bets);
					}
	            },'json');
	        }
	    });

	    $('#btn-submit').click( function() {
	        dataTable.ajax.reload();
	    });

	    ATTACH_DATATABLE_BAR_LOADER.init('api-response-table');
	});

	function addCommas(nStr){
        nStr += '';
        var x = nStr.split('.');
        var x1 = x[0];
        var x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    }
</script>
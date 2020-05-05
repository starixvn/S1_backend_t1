<form action="<?php echo site_url('payment_management/getWithdrawReport'); ?>" id="search-form" method="GET">
<div class="panel panel-primary">
	<div class="panel-heading">
		<h4 class="panel-title">
			<i class="fa fa-search"></i> <?=lang("lang.search")?>
			<a href="#main_panel" data-toggle="collapse" class="pull-right"><i class="fa fa-caret-down"></i></a>
		</h4>
	</div>
	<div id="main_panel" class="panel-collapse collapse in ">
		<div class="panel-body">
        	<div class="row">
            	<div class="col-md-4 col-lg-4">
					<label class="control-label search-time" for="search_time">
								<?=lang('payment.lastUpdatedTime')?>


					</label>
					<div class="input-group" style="width:100%">
						<input id="search_withdrawal_date" class="form-control input-sm dateInput" data-time="true" data-start="#withdrawal_date_from" data-end="#withdrawal_date_to"/>
					</div>
					<input type="hidden" id="withdrawal_date_from" name="withdrawal_date_from" value="<?=$conditions['withdrawal_date_from'];?>"/>
					<input type="hidden" id="withdrawal_date_to" name="withdrawal_date_to" value="<?=$conditions['withdrawal_date_to'];?>"/>
            	</div>
            	<div class="col-md-3 col-lg-3">
            		<label class="control-label" for="username"><?=lang('player.userName')?></label>
					<input id="username" type="text" name="username" value="<?php echo $conditions['username']; ?>"  class="form-control input-sm"/>
				</div>
				<div class="col-md-2 col-lg-2">
					<label class="control-label" for="status"><?=lang('status')?></label>
					<select class="form-control input-sm select-status" name="search_status">
						<option value ="allStatus"  ><?=lang("All")?> </option>
						<?php foreach ($searchStatus as $status => $value): ?>
							<option value ="<?php echo $status?>" <?php echo $conditions['search_status'] == $status ? 'selected' : '' ?> ><?php echo $value?> </option>
						<?php endforeach; ?>
					</select>
				</div>
            	<div class="col-md-3 col-lg-3">
            		<label class="control-label" for="processed_by"><?=lang('pay.procssby')?></label>
					<input id="processed_by" type="text" name="processed_by" value="<?php echo $conditions['processed_by']; ?>"  class="form-control input-sm"/>
				</div>
        		<div class="col-md-offset-9 col-md-3 text-right" style="padding-top:10px;">
                	<input class="btn btn-sm btn-primary" type="submit" value="<?php echo lang('st.Search'); ?>" />
					<button type="button" class="btn btn-sm btn-danger clear-btn" style="width:68px;" onclick="resetSearch()" ><?=lang('lang.clear')?></button>
            	</div>
			</div>
		</div>
	</div>
</div>	
</form>
<div class="panel panel-primary">
	<div class="panel-heading custom-ph">
		<h4 class="panel-title custom-pt">
			<i class="icon-list"></i> <?=lang('Withdraw Processing Time Record')?>
			<a href="<?=site_url('payment_management/viewWithdrawalRequestList?dwStatus=request')?>" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-info' : 'btn-default'?> btn-xs pull-right">
				<i class="glyphicon glyphicon-log-out"></i>
				<?=lang('button.back');?>
			</a>
		</h4>
	</div>

	<div class="panel-body">
	  	<div class="table-responsive">
			<table class="table table-striped table-hover tablepress table-condensed" id="deposit-table">
				<thead>
					<tr>
						<th class="tableHeaderFont"><?=lang('Withdrawal Code')?></th>
						<th class="tableHeaderFont"><?=lang('player.01')?></th>
		        		<th class="tableHeaderFont"><?=lang("sys.vu19")?></th>
						<th class="tableHeaderFont"><?=lang('Withdraw Amount');?></th>
						<th class="tableHeaderFont"><?=lang('pay.reqtime');?></th>
						<th class="tableHeaderFont"><?=lang('payment.lastUpdatedTime');?></th>
						<th class="tableHeaderFont"><?=lang('payment.totalProcessingTime');?></th>
						<th class="tableHeaderFont"><?=lang('lang.status');?></th>
						<th class="tableHeaderFont"><?=lang('pay.procssby');?></th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>
</div>

<?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
	<form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
		<input name='json_search' type="hidden">
	</form>
<?php }?>
<script type="text/javascript">
	function resetSearch(){
		$('.dateInput').data('daterangepicker').setStartDate(moment().startOf('day').format('Y-MM-DD HH:mm:ss'));
		$('.dateInput').data('daterangepicker').setEndDate(moment().endOf('day').format('Y-MM-DD HH:mm:ss'));
			dateInputAssignToStartAndEnd($('#search_withdrawal_date'));
		$('.select-status').val('request');
		//$('#request_time').prop('checked', 'checked');
		$('#withdrawMethods input[type=\'checkbox\']').prop('checked', false);
		
		clearSearchInputVal();
	}

	function clearSearchInputVal(){
		$('#username').val('');
		$('#processed_by').val('');
		$('#search_status').val('');
	}
	
	//checked select status and link search time
	// $('.select-status').change(function(){
	// 	var getValue = $('.select-status').val();
	// 	if(getValue == 'allStatus' || getValue == 'request'){
	// 		$('#request_time').prop('checked', 'checked');
	// 	}else{
	// 		$('#updated_on').prop('checked', 'checked');
	// 	}
	// });
//

    $(document).ready(function(){
        var dataTable = $('#deposit-table').DataTable({
            autoWidth: false,
            searching: true,
            dom: "<'panel-body'<'pull-right'B><'pull-right'f><'pull-right progress-container'>l>t<'panel-body'<'pull-right'p>i>",
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ],
					className: '<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>'
				}
				<?php

				if( $this->permissions->checkPermissions('export_withdrawal_lists') ){
				?>
				,{
	                text: "<?php echo lang('CSV Export'); ?>",
	                className:'btn btn-sm btn-primary',
	                action: function ( e, dt, node, config ) {
						var form_params=$('#search-form').serializeArray();

						var d = {'extra_search': form_params, 'export_format': 'csv', 'export_type': export_type,
								'draw':1, 'length':-1, 'start':0};
	                            $("#_export_excel_queue_form").attr('action', site_url('/export_data/withdrawCheckingReport/true'));
	                            $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
	                            $("#_export_excel_queue_form").submit();

	                    }
	                }
		        <?php
		            }
		        ?>
            ],
            order: [[5, 'desc']],

            // SERVER-SIDE PROCESSING
            processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {
                data.extra_search = $('#search-form').serializeArray();
                $.post(base_url + "api/withdrawCheckingReport", data, function(data) {
					callback(data);
					var rows = $('#deposit-table').DataTable().rows( { selected: true } );
					if ( rows.indexes().length === 0 ) {
						$('#deposit-table').DataTable().buttons().disable();
						}
						else {
							$('#deposit-table').DataTable().buttons().enable();
						}
                },'json');
            },
		});
    });
</script>

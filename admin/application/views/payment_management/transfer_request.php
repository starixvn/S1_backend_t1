<?php include $this->utils->getIncludeView('show_response_result.php');?>

<form id="search-form">

	<div class="panel panel-primary hidden">

	    <div class="panel-heading">
	        <h4 class="panel-title">
	            <i class="fa fa-search"></i> <?=lang("lang.search")?>
	            <span class="pull-right">
	                <a data-toggle="collapse" href="#collapseTransferRequest" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info'?> <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
	            </span>
	        </h4>
	    </div>

	    <div id="collapseTransferRequest" class="panel-collapse <?=$this->config->item('default_open_search_panel') ? '' : 'collapse in'?>">
	        <div class="panel-body">
				<div class="row">
					<div class="col-md-2">
	                    <label class="control-label" for="status"><?=lang('Status')?></label>
	                    <select id="status" name="status"  class="form-control input-sm">
	                        <option value=""><?=lang('All');?></option>
	                        <option value="<?php echo Wallet_model::STATUS_TRANSFER_SUCCESS;?>" <?php echo ($conditions['status']==Wallet_model::STATUS_TRANSFER_SUCCESS) ? 'selected' : ''?>><?=lang('Successful');?> </option>
	                        <option value="<?php echo Wallet_model::STATUS_TRANSFER_FAILED;?>" <?php echo ($conditions['status']==Wallet_model::STATUS_TRANSFER_FAILED) ? 'selected' : ''?>><?=lang('Failed');?> </option>
	                        <option value="<?php echo Wallet_model::STATUS_TRANSFER_REQUEST;?>" <?php echo ($conditions['status']==Wallet_model::STATUS_TRANSFER_REQUEST) ? 'selected' : ''?>><?=lang('Request');?> </option>
	                    </select>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label" for="search_date"><?=lang('pay.transperd')?></label>
                            <div class="input-group">
                                <input id="search_date" class="form-control input-sm dateInput" data-time="true" data-start="#date_from" data-end="#date_to"/>
                                <input type="hidden" id="date_from" name="date_from"/>
                                <input type="hidden" id="date_to" name="date_to"/>
                                <span class="input-group-addon input-sm">
                                        <input type="checkbox" checked="checked" name="search_reg_date" id="search_reg_date" class="user-success">
                                </span>
                            </div>
						</div>
					</div>
					<div class="col-md-2">
	                    <label class="control-label" for="timezone"><?=lang('Timezone')?></label>
	                    <select id="timezone" name="timezone"  class="form-control input-sm">
	                    <?php for($i = 12;  $i >= -12; $i--): ?>
	                        <option value="<?php echo $i > 0 ? "+{$i}" : $i ;?>" <?php echo ($i == $conditions['timezone']) ? 'selected' : ''?>> <?php echo $i > 0 ? "+{$i}" : $i ;?>:00</option>
	                    <?php endfor;?>
	                    </select>
					</div>
					<div class="col-md-2">
	                    <label class="control-label" for="by_game_platform_id"><?=lang('Game Platform')?></label>
	                    <select id="by_game_platform_id" name="by_game_platform_id"  class="form-control input-sm">
						<option value="" ><?=lang('All');?></option>
						<?php foreach ($game_platforms as $game_platform) {?>
							<option value="<?=$game_platform['id']?>" <?php echo $conditions['by_game_platform_id']==$game_platform['id'] ? 'selected="selected"' : '' ; ?>><?=$game_platform['system_code'];?></option>
						<?php }?>
	                    </select>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label" for="player_username"><?=lang('Player Username')?></label>
							<input id="player_username" type="text" name="player_username" class="form-control input-sm"/>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label" for="secure_id"><?=lang('ID')?></label>
							<input id="secure_id" type="text" name="secure_id" class="form-control input-sm" value="<?=$conditions['secure_id']?>"/>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label" for="result_id"><?=lang('Response Result ID')?></label>
							<input id="result_id" type="text" name="result_id" class="form-control input-sm" value="<?=$conditions['result_id']?>"/>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label" for="admin_username"><?=lang('Admin Username')?></label>
							<input id="admin_username" type="text" name="admin_username" class="form-control input-sm"/>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label" for="amount_from"><?=lang('Transfer Amount')?> &gt;=</label>
							<input id="amount_from" type="number" min="0" name="amount_from" class="form-control input-sm"/>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label" for="amount_to"><?=lang('Transfer Amount')?> &lt;=</label>
							<input id="amount_to" type="number" min="0" name="amount_to" class="form-control input-sm"/>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label" for="suspicious_trans"><?=lang('Suspicious Trans')?>
							</label>
		                    <select id="suspicious_trans" name="suspicious_trans" class="form-control input-sm">
		                        <option value=""><?=lang('None');?></option>
		                        <option value="<?=Wallet_model::SUSPICIOUS_TRANSFER_IN_ONLY?>"><?=lang('Transfer In Only');?></option>
		                        <option value="<?=Wallet_model::SUSPICIOUS_TRANSFER_OUT_ONLY?>"><?=lang('Transfer Out Only');?></option>
		                        <option value="<?=Wallet_model::SUSPICIOUS_ALL?>"><?=lang('All');?></option>
		                    </select>

						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<button type="submit" class="btn btn-sm pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?> "><?=lang('Search')?></button>
					</div>
				</div>
			</div>
		</div>
	 </div>

</form>

<div class="panel panel-primary">

	<div class="panel-heading">
		<h4 class="panel-title">
			<i class="glyphicon glyphicon-transfer"></i> <?=lang('Transfer Request List')?>
		</h4>
	</div>
<div class="panel-body">
	<div class="table-responsive">
	<table class="table table-bordered table-hover" id="transfer-request-table">
		<thead>
			<tr>
                <?php include __DIR__ . '/../includes/cols_for_transfer_list.php';?>
			</tr>
		</thead>
	</table>
</div>
</div>
	<div class="panel-footer"></div>

</div>
<?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
    <form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
    <input name='json_search' type="hidden">
    </form>
<?php }?>
<style type="text/css">
/*[data-notify="progressbar"] {*/
	/* margin-bottom: 0px; */
	/* position: absolute; */
	/* bottom: 0px; */
	/* left: 0px; */
	/*width: 100%;*/
	/* height: 5px; */
/*}*/
</style>
<script type="text/javascript">

	function enableDisableSearchDate(enable, updateCheckbox){
	    if ( ! enable ) {
	        $('#search_date').prop("disabled",true);
	        if(updateCheckbox){
	        	$('input[name=search_reg_date]').prop("checked", false);
	        }
	    }else{
	        $('#search_date').prop("disabled",false);
	        if(updateCheckbox){
	        	$('input[name=search_reg_date]').prop("checked", true);
	        }
	    }
	}

	$(document).ready( function() {

		var export_type="<?php echo $this->utils->isEnabledFeature('export_excel_on_queue') ? 'queue' : 'direct';?>";

		enableDisableSearchDate(<?= $conditions['search_reg_date']=='on' ? 'true' : 'false' ?>, true);

        $('input[name=search_reg_date]').change(function(){
        	enableDisableSearchDate($('input[name=search_reg_date]').is(':checked'), false);
        });

		var dataTable = $('#transfer-request-table').DataTable({

			autoWidth: false,
			searching: false,

            <?php if ($this->utils->isEnabledFeature('column_visibility_report')){ ?>
                stateSave: true,
            <?php } else { ?>
                stateSave: false,
            <?php } ?>

			pageLength: <?=$this->utils->getDefaultItemsPerPage()?>,
			dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
			buttons: [
				{
					extend: 'colvis',
					postfixButtons: [ 'colvisRestore' ],
					className: '<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>',
				},
				<?php

                    if( $this->permissions->checkPermissions('export_transfer_request') ){

                ?>
                        {

                            text: "<?php echo lang('CSV Export'); ?>",
                            className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>',
                            action: function ( e, dt, node, config ) {
                               // var d = {'extra_search':$('#search-form').serializeArray(), 'draw':1, 'length':-1, 'start':0};
                                var d = {'extra_search': $('#search-form').serializeArray(), 'export_format': 'csv', 'export_type': export_type,
							'draw':1, 'length':-1, 'start':0};

                                // utils.safelog(d);

                                <?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
                                    $("#_export_excel_queue_form").attr('action', site_url('/export_data/transferRequest'));
                                    $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
                                    $("#_export_excel_queue_form").submit();
                                <?php }else{?>

                                $.post(site_url('/export_data/transferRequest'), d, function(data){
                                    // utils.safelog(data);

                                    //create iframe and set link
                                    if(data && data.success){
                                        $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
                                    }else{
                                        alert('export failed');
                                    }
                                });
                                <?php }?>
                            }
                        }
                <?php
                    }
                ?>
			],

			columnDefs: [
				{ className: 'text-right', targets: [ 5 ] },
			],
			order: [[_sort_col_index_for_transfer_list, 'desc']],

			// SERVER-SIDE PROCESSING
			processing: true,
			serverSide: true,
			ajax: function (data, callback, settings) {
				data.extra_search = $('#search-form').serializeArray();
				$.post(base_url + "api/transfer_request", data, function(data) {
					callback(data);
					if ( dataTable.rows( { selected: true } ).indexes().length === 0 ) {
					    dataTable.buttons().disable();
					}
					else {
						dataTable.buttons().enable();
					}
				},'json');
			},

		});

		$('#search-form').submit( function(e) {
			e.preventDefault();

	        dataTable.ajax.reload();
		});

		$('#search-form input[type="text"],#search-form input[type="number"],#search-form input[type="email"]').keypress(function (e) {
			if (e.which == 13) {
				$('#search-form').trigger('submit');
			}
		});

	});
</script>
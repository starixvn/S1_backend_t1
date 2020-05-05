<div class="row" id="user-container">
	<div class="col-md-12" id="toggleView">
		<div class="panel panel-primary">
            <div class="panel-heading custom-ph" style="height:64px;">
                <h3 class="panel-title custom-pt" >
                    <i class="icon-list"></i>
                    <?=lang('Manual Sync Game List from GameGateway');?>

					<!-- <button type="button" id="manual_sync_all_game_list" name="btnSubmit" class="btn btn-default btn-xs pull-right" onclick="resync_game_list(false)">
                        <i class="glyphicon glyphicon-refresh" data-placement="bottom"></i>
                        <?=lang('Resync All New Games');?>
                    </button>
                    <button class="btn btn-danger btn-xs game_api_manual_resync_all_loading_btn pull-right" id="game_api_manual_resync_all_loading_btn">
										<i class="fa fa-spinner fa-spin"></i> <?=lang('Loading');?>
					</button> -->
                </h3>

                <?=lang('This page is use to sync game list data from gamegateway to client or local database');?>
            </div>

			<div class="panel-body" id="list_panel_body">
				<div class="table-responsive">
				<div class="clearfix"></div>
					<table class="table table-bordered table-hover dataTable" id="my_table">
						<th><?=lang('Game Api');?></th>
						<th><?=lang('Action');?></th>

						<?php
						foreach ($gameApis as $val): ?>
							<tr/>
								<td>
									<?php echo $val['system_name'] ?>
								</td>
								<td>
									<button class="btn btn-default btn-xs game_api_manual_resync_btn" id="game_api_manual_resync_btn_<?php echo $val['id']?>" onclick="resync_game_list('<?php echo $val['id']?>')">
										<i class="glyphicon glyphicon-refresh" data-placement="bottom"></i> <?=lang('Resync');?>
									</button>
									<button class="btn btn-primary btn-xs game_api_manual_resync_loading_btn" id="game_api_manual_resync_loading_btn_<?php echo $val['id']?>">
										<i class="fa fa-spinner fa-spin"></i> <?=lang('Loading');?>
									</button>
									<button class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-success'?> btn-xs game_api_manual_resync_result_btn" id="game_api_manual_resync_result_btn_<?php echo $val['id']?>" onclick="show_resync_result('<?php echo $val['id']?>','<?php echo $val['system_name']?>')">
										<i class="glyphicon glyphicon-search" data-placement="bottom"></i> <?=lang('Show Result');?>
									</button>
									<span class="game_api_manual_resync_result" id="game_api_manual_resync_result_txt_<?php echo $val['id']?>"></span>

									<button class="btn btn-default btn-xs game_api_manual_resync_btn" id="game_api_manual_resync_active_btn_<?php echo $val['id']?>" onclick="resync_active_game_list('<?php echo $val['id']?>')">
										<i class="glyphicon glyphicon-refresh" data-placement="bottom"></i> <?=lang('Resync Active Games');?>
									</button>
									<button class="btn btn-primary btn-xs game_api_manual_resync_loading_btn" id="game_api_manual_resync_active_loading_btn_<?php echo $val['id']?>">
										<i class="fa fa-spinner fa-spin"></i> <?=lang('Loading');?>
									</button>
									<button class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-success'?> btn-xs game_api_manual_resync_result_btn" id="game_api_manual_resync_active_result_btn_<?php echo $val['id']?>" onclick="show_resync_active_result('<?php echo $val['id']?>','<?php echo $val['system_name']?>')">
										<i class="glyphicon glyphicon-search" data-placement="bottom"></i> <?=lang('Show Result');?>
									</button>
									<span class="game_api_manual_resync_result" id="game_api_manual_resync_active_result_txt_<?php echo $val['id']?>"></span>

								</td>
							</tr>
						<?php endforeach; ?>
					</table>
				</div>
			</div>

			<div class="panel-footer"></div>

		</div>
	</div>


	<div id="modal-content" class="modal fade" tabindex="-1" role="dialog">
	    <div class="modal-dialog">
	        <div class="modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal">×</button>
	                <h3><span id="gamePlatformNameTxt"></span> - <?=lang('Game List Sync Result');?></h3>
	            </div>
	            <div class="modal-body">
	                    <pre id="gameListSyncResultTxt"></pre>
	            </div>
	            <div class="modal-footer">
	                <a href="#" class="btn btn-primary" data-dismiss="modal"><?=lang('Close');?></a>
	            </div>
	        </div>
	    </div>
	</div>

	<div id="modal-content-active-gamelist" class="modal fade" tabindex="-1" role="dialog">
	    <div class="modal-dialog">
	        <div class="modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal">×</button>
	                <h3><span id="gamePlatformNameTxtActive"></span> - <?=lang('Game List Sync Result');?></h3>
	            </div>
	            <div class="modal-body">
	                    <pre id="gameListSyncResultTxtActive"></pre>
	            </div>
	            <div class="modal-footer">
	                <a href="#" class="btn btn-primary" data-dismiss="modal"><?=lang('Close');?></a>
	            </div>
	        </div>
	    </div>
	</div>


<script type="text/javascript">
	$("#collapseSubmenuGameDescription").addClass("in");
	$("a#view_game_description").addClass("active");
	$("a#devManualSyncFromGameGateway").addClass("active");

	$('.game_api_manual_resync_loading_btn, .game_api_manual_resync_result_btn, .game_api_manual_resync_result, .game_api_manual_resync_all_result_btn, .game_api_manual_resync_all_loading_btn').hide();

	function show_resync_result(game_platform_id,game_platform_name)
	{
		$('#gamePlatformNameTxt').text(game_platform_name);
		var syncGameListResponse = document.getElementById('gameListSyncResultTxt');

		if(game_platform_id == false){
			var obj = JSON.parse($('#game_api_manual_resync_all_result_txt').text());
			syncGameListResponse.innerHTML = JSON.stringify(obj, undefined, 2);
		}else{
			$('#game_api_manual_resync_result_txt_'+game_platform_id).hide();

			var obj = JSON.parse($('#game_api_manual_resync_result_txt_'+game_platform_id).text());
			syncGameListResponse.innerHTML = JSON.stringify(obj, undefined, 2);
		}

		$('#modal-content').modal({
	        show: true
	    });
	}

	function resync_game_list(game_platform_id)
	{
		if(game_platform_id == false){

			console.log("Manual Sync All");
			$('#manual_sync_all_game_list').hide();
			$('#game_api_manual_resync_all_loading_btn').show();
			$.get("<?=site_url('game_description/do_manual_sync_gamelist_from_gamegateway')?>", function(data){
				var response = JSON.parse(data);
				if(response.success){
					$('#game_api_manual_resync_all_loading_btn').hide();
					$('#game_api_manual_resync_all_result_btn').show();
					$('#game_api_manual_resync_all_result_txt').text(data);
				}
			});
		}else{
			$('#game_api_manual_resync_btn_'+game_platform_id).hide();
			$('#game_api_manual_resync_loading_btn_'+game_platform_id).show();

			$.get("<?=site_url('game_description/do_manual_sync_gamelist_from_gamegateway')?>/"+game_platform_id, function(data){
				var response = JSON.parse(data);
				if(response.success){
					$('#game_api_manual_resync_loading_btn_'+game_platform_id).hide();
					$('#game_api_manual_resync_result_btn_'+game_platform_id).show();
					$('#game_api_manual_resync_result_txt_'+game_platform_id).text(data);
				}
			});
		}

	}


	function show_resync_active_result(game_platform_id,game_platform_name)
	{
		$('#gamePlatformNameTxtActive').text(game_platform_name);
		var syncGameListResponse = document.getElementById('gameListSyncResultTxtActive');

		if(game_platform_id == false){
			var obj = JSON.parse($('#game_api_manual_resync_active_all_result_txt').text());
			syncGameListResponse.innerHTML = JSON.stringify(obj, undefined, 2);
		}else{
			$('#game_api_manual_resync_active_result_txt_'+game_platform_id).hide();

			var obj = JSON.parse($('#game_api_manual_resync_active_result_txt_'+game_platform_id).text());
			syncGameListResponse.innerHTML = JSON.stringify(obj, undefined, 2);
		}

		$('#modal-content-active-gamelist').modal({
	        show: true
	    });
	}

	function resync_active_game_list(game_platform_id)
	{
		if(game_platform_id == false){

			console.log("Manual Sync All");
			$('#manual_sync_all_game_list').hide();
			$('#game_api_manual_resync_active_all_loading_btn').show();
			$.get("<?=site_url('game_description/do_manual_sync_active_gamelist_from_gamegateway')?>", function(data){
				var response = JSON.parse(data);
				if(response.success){
					$('#game_api_manual_resync_active_all_loading_btn').hide();
					$('#game_api_manual_resync_active_all_result_btn').show();
					$('#game_api_manual_resync_active_all_result_txt').text(data);
				}
			});
		}else{
			$('#game_api_manual_resync_active_btn_'+game_platform_id).hide();
			$('#game_api_manual_resync_active_loading_btn_'+game_platform_id).show();

			$.get("<?=site_url('game_description/do_manual_sync_active_gamelist_from_gamegateway')?>/"+game_platform_id, function(data){
				var response = JSON.parse(data);
				if(response.Success){
					console.table(data);
					$('#game_api_manual_resync_active_loading_btn_'+game_platform_id).hide();
					$('#game_api_manual_resync_active_result_btn_'+game_platform_id).show();
					$('#game_api_manual_resync_active_result_txt_'+game_platform_id).text(data);
				}
			});
		}
	}

</script>
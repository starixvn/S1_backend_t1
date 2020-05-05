<style type="text/css">
	.panel-body .row{
		margin: 4px;
	}
</style>

<form action="<?=site_url('system_management/post_debug_queue'); ?>" method="POST">
	<div class="panel panel-primary panel_main">
		<div class="panel-heading">
			<h4 class="panel-title"><?=lang('Queue Server'); ?>: <?=$redis_channel_key?>
				<span class="pull-right">
					<a data-toggle="collapse" href="#queue_panel" class="btn btn-info btn-xs collapsed" aria-expanded="true"></a>
				</span>
			</h4>
		</div>

		<div id="queue_panel" class="panel-collapse collapse in ">
			<div class="panel-body">
				<div>
					<?php if(!empty($queue_server_info)){?>
						<pre>
							<code class="json">
								<?=json_encode($queue_server_info, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)?>
							</code>
						</pre>
						<a class="btn btn-portage" href="<?=site_url('system_management/post_debug_queue')?>"><?=lang('Debug Queue')?></a>
			            <a class="btn btn-portage" href="<?=site_url('system_management/post_debug_async_event')?>"><?=lang('Debug Async Event')?></a>
					<?php }else{?>
						<div class="text-warning"><?=lang('Wrong Queue Server')?></div>
					<?php }?>
				</div>
			</div>
		</div>
	</div>
</form>

<form action="<?=site_url('system_management/post_clear_memory_cache'); ?>" method="POST">
	<div class="panel panel-primary panel_main">
		<div class="panel-heading">
			<h4 class="panel-title"><?=lang('Clear Memory Cache'); ?>
				<span class="pull-right">
					<a data-toggle="collapse" href="#clear_cache_panel" class="btn btn-info btn-xs collapsed" aria-expanded="true"></a>
				</span>
			</h4>
		</div>
		<div id="clear_cache_panel" class="panel-collapse collapse in ">
			<div class="panel-body">
				<div>
					<?=lang('It will clear all memory cache, like settings. so most settings will restore from DB')?>
				</div>
			</div>
			<div class="panel-footer">
				<input type="submit" class="btn btn-chestnutrose" value="<?=lang('Do it !'); ?>">
			</div>
		</div>
	</div>
</form>
<?php if(!$this->utils->getConfig('disabled_sync_game_logs_on_sbe')){?>
<form action="<?=site_url('system_management/post_sync_game_logs'); ?>" method="POST">
	<div class="panel panel-primary panel_main">
		<div class="panel-heading">
			<h4 class="panel-title"><?=lang('Sync Game Logs')?>
				<span class="pull-right">
					<a data-toggle="collapse" href="#sync_game_logs_panel" class="btn btn-info btn-xs collapsed" aria-expanded="true"></a>
				</span>
			</h4>
		</div>
		<div id="sync_game_logs_panel" class="panel-collapse collapse in ">
			<div class="panel-body">
				<div class="row">
					<div class="col-md-2">
						<?=lang('Date');?>
					</div>
					<div class="col-md-6">
		                <input id="sync_game_date" class="form-control input-sm dateInput" data-start="#by_date_from" data-end="#by_date_to" data-time="true"/>
		                <input type="hidden" id="by_date_from" name="by_date_from" value="<?=$by_date_from;?>" />
		                <input type="hidden" id="by_date_to" name="by_date_to"  value="<?=$by_date_to;?>"/>
		                <strong><span class="text-info small" id="sync-game-rules-info"></span></strong>
					</div>
				</div>
				<div class="row">
					<div class="col-md-2">
						<?=lang('Game');?>
					</div>
					<div class="col-md-4">
						<select class="form-control input-sm" name="by_game_platform_id" id="by_game_platform_id">
							<option value=""><?=lang('All');?></option>
							<?php foreach ($this->utils->getGameSystemMap() as $key => $value) { ?>
								<option value="<?=$key;?>"><?=$value;?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="row">
					<div class="col-md-2">
						<?=lang('Player');?>
					</div>
					<div class="col-md-4">
						<input class="form-control input-sm" type="text" name="playerName" value="<?=$playerName?>">
					</div>
				</div>
				<div class="row">
					<div class="col-md-2">
						<?=lang('Merge Only');?>
					</div>
					<div class="col-md-4">
						<input type="checkbox" name="merge_only" value="true">
					</div>
				</div>
				<div class="row">
					<div class="col-md-2">
						<?=lang('Original Only');?>
					</div>
					<div class="col-md-4">
						<input type="checkbox" name="only_original" value="true">
					</div>
				</div>
				<div class="row">
					<div class="col-md-2">
						<?=lang('Dry Run');?>
					</div>
					<div class="col-md-4">
						<input type="checkbox" name="dry_run" value="true">
					</div>
				</div>
			</div>
			<div class="panel-footer">
				<input type="submit" class="btn btn-portage" value="<?=lang('Submit'); ?>">
			</div>
		</div>
	</div>
</form>
<?php }?>

<script>
	function getSyncDateRule(){

		var today = new Date(),
		thisMonth = today.getMonth()+1, //zero based
		thisYear = today.getFullYear(),
		minDatetime,maxDatetime,billingDay = <?php echo  $this->utils->getConfig('billing_day'); ?>;

		if(today.getDate() > billingDay  ){
			//only sync this month
		    minDatetime = new Date();
			minDatetime.setDate(1);
			minDatetime.setHours(0,0,0)
			maxDatetime = new Date(thisYear,thisMonth, 0);
			maxDatetime.setHours(23,59,59)
			//console.log(minDatetime);console.log(maxDatetime)

		}else{
			//only sync last month to this month
		    minDatetime = new Date(thisYear,thisMonth -1, 0);
			minDatetime.setDate(1);
			minDatetime.setHours(0,0,0)
			maxDatetime = new Date(thisYear,thisMonth, 0);
			maxDatetime.setHours(23,59,59)
			//console.log(minDatetime);console.log(maxDatetime);
		}

		return {minDatetime:minDatetime,maxDatetime:maxDatetime};
	}

	$(document).ready(function(){
		var syncGameRulesInfo =  getSyncDateRule(),
		minDatetime = moment(syncGameRulesInfo.minDatetime).format("YYYY-MM-DD HH:mm:ss"),
		maxDatetime = moment(syncGameRulesInfo.maxDatetime).format("YYYY-MM-DD HH:mm:ss");
		$('#sync-game-rules-info').html('<?php echo lang('You can only sync game logs by this dates')?> '+minDatetime+' - '+maxDatetime);
	});//doc ready end
</script>

<form action="<?=site_url('system_management/post_rebuild_games_total'); ?>" method="POST">
	<div class="panel panel-primary panel_main">
		<div class="panel-heading">
			<h4 class="panel-title"><?=lang('Rebuild Totals') ?>
				<span class="pull-right">
					<a data-toggle="collapse" href="#rebuild_totals_panel" class="btn btn-info btn-xs collapsed" aria-expanded="true"></a>
				</span>
			</h4>
		</div>

		<div id="rebuild_totals_panel" class="panel-collapse collapse in ">
			<div class="panel-body">
				<div class="row">
					<div class="col-md-2">
						<?=lang('Date');?>
					</div>
					<div class="col-md-6">
						<input id="sync_total_date" class="form-control input-sm dateInput" data-start="#rebuild_totals_by_date_from" data-end="#rebuild_totals_by_date_to" data-time="true"/>
						<input type="hidden" id="rebuild_totals_by_date_from" name="by_date_from" value="<?=$by_date_from;?>" />
						<input type="hidden" id="rebuild_totals_by_date_to" name="by_date_to"  value="<?=$by_date_to;?>"/>
						<?php if(!empty($lock_rebuild_reports_range)):?>
							<strong>
								<span class="text-info small" id="lock-reports-info"><?=sprintf(lang("Regenarate All Report has lock - should not be equal or older  %s  "),$lock_rebuild_reports_range['cutoff_day']); ?></span>
							</strong>
				    	<?php endif;?>
					</div>
				</div>
				<script>
					$(document).ready(function(){
						var rebuildMinute = $('#rebuild_minute'),
						rebuildHour = $('#rebuild_hour');
						rebuildMinute.change(function(){
							if($(this).is(":checked")){
								rebuildHour.prop("disabled", false);
							}else{
								rebuildHour.prop("disabled", true);
							}
						});
					});
				</script>
				<div class="row">
					<div class="col-md-2">
						<?=lang('Rebuild Hour');?>
					</div>
					<div class="col-md-4">
						<input type="checkbox" id="rebuild_hour" name="rebuild_hour" checked value="true">
					</div>
				</div>
				<div class="row">
					<div class="col-md-2">
						<?=lang('Rebuild Minute');?>
					</div>
					<div class="col-md-4">
						<input type="checkbox" id="rebuild_minute" name="rebuild_minute" checked value="true">
					</div>
				</div>
			</div>

			<div class="panel-footer">
				<input type="submit" class="btn btn-portage" value="<?=lang('Submit'); ?>">
			</div>
		</div>
	</div>
</form>

<form action="<?=site_url('system_management/regenerate_all_report'); ?>" method="POST">
	<div class="panel panel-primary panel_main">
		<div class="panel-heading">
			<h4 class="panel-title"><?=lang('Regenerate All Report') ?>
				<span class="pull-right">
					<a data-toggle="collapse" href="#regenerate_all_report" class="btn btn-info btn-xs collapsed" aria-expanded="true"></a>
				</span>
			</h4>
		</div>
		<div id="regenerate_all_report" class="panel-collapse collapse in ">
			<div class="panel-body">
				<div class="row">
					<div class="col-md-2">
						<?=lang('Date');?>
					</div>
					<div class="col-md-6">
						<input class="form-control input-sm dateInput" data-start="#regenerate_all_report_by_date_from" data-end="#regenerate_all_report_by_date_to" data-time="false"/>
						<input type="hidden" id="regenerate_all_report_by_date_from" name="by_date_from" value="<?=date('Y-m-d', strtotime('-1 day'));?>" />
						<input type="hidden" id="regenerate_all_report_by_date_to" name="by_date_to"  value="<?=date('Y-m-d', strtotime('-1 day'))?>"/>
						<?php if(!empty($lock_rebuild_reports_range)):?>
							<strong>
								<span class="text-info small" id="lock-reports-info">
									<?=sprintf(lang("Regenarate All Report has lock - should not be equal or older  %s  "),$lock_rebuild_reports_range['cutoff_day']); ?>
								</span>
							</strong>
				    	<?php endif;?>
					</div>
				</div>
			</div>

			<div class="panel-footer">
				<input type="submit" class="btn btn-portage" value="<?=lang('Submit'); ?>">
			</div>
		</div>
	</div>
</form>

<form action="<?=site_url('system_management/post_sync_t1_gamegateway'); ?>" method="POST">
	<div class="panel panel-primary panel_main">
		<div class="panel-heading">
			<h4 class="panel-title"><?=lang('Sync T1 Gamegateway') ?>
				<span class="pull-right">
					<a data-toggle="collapse" href="#sync_t1_panel" class="btn btn-info btn-xs collapsed" aria-expanded="true"></a>
				</span>
			</h4>
		</div>
		<div id="sync_t1_panel" class="panel-collapse collapse in ">
			<div class="panel-body">
				<div class="row">
					<div class="col-md-2">
						<?=lang('Date');?>
					</div>
					<div class="col-md-4">
						<input id="sync_t1_date" class="form-control input-sm dateInput" data-start="#sync_t1_by_date_from" data-end="#sync_t1_by_date_to" data-time="true"/>
						<input type="hidden" id="sync_t1_by_date_from" name="by_date_from" value="<?=$by_date_from;?>" />
						<input type="hidden" id="sync_t1_by_date_to" name="by_date_to"  value="<?=$by_date_to;?>"/>
					</div>
				</div>
				<div class="row">
					<div class="col-md-2">
						<?=lang('Player Username');?>
					</div>
					<div class="col-md-4">
						<input class="form-control input-sm" type="text" name="playerName" value="<?=$playerName?>">
					</div>
				</div>
			</div>
			<div class="panel-footer">
				<input type="submit" class="btn btn-portage" value="<?=lang('Run'); ?>">
			</div>
		</div>
	</div>
</form>

<?php if(!$this->utils->isEnabledFeature('close_cashback')): ?>
	<form action="<?=site_url('system_management/post_sync_cashback'); ?>" method="POST">
		<div class="panel panel-primary panel_main">
			<div class="panel-heading">
				<h4 class="panel-title"><?=lang('Sync Cashback'); ?>
					<span class="pull-right">
						<a data-toggle="collapse" href="#cashback_panel" class="btn btn-info btn-xs collapsed" aria-expanded="true"></a>
					</span>
				</h4>
			</div>

			<div id="cashback_panel" class="panel-collapse collapse in ">
				<div class="panel-body">
					<div class="row">
						<div class="col-md-2">
							<?=lang('Date');?>
						</div>
						<div class="col-md-4">
			                <input id="cashback_date_picker" class="form-control input-sm dateInput" data-start="#cashback_date" data-time="false"/>
			                <input type="hidden" id="cashback_date" name="cashback_date" value="<?=$cashback_date;?>" />
						</div>
					</div>
					<div class="row">
						<div class="col-md-2">
							<?=lang('Dry Run');?>
						</div>
						<div class="col-md-4">
							<input type="checkbox" name="dry_run" value="true">
						</div>
					</div>
				</div>
				<div class="panel-footer">
					<input type="submit" class="btn btn-portage" value="<?=lang('Submit'); ?>">
				</div>
			</div>
		</div>
	</form>
<?php endif; ?>

<?php if(!$this->utils->isEnabledFeature('close_aff_and_agent')): ?>
	<form action="<?=site_url('system_management/post_calculate_aff_earnings'); ?>" method="POST">
		<div class="panel panel-primary panel_main">
			<div class="panel-heading">
				<h4 class="panel-title"><?=lang('Generate Affiliate Earnings'); ?>
					<span class="pull-right">
						<a data-toggle="collapse" href="#aff_earnings" class="btn btn-info btn-xs collapsed" aria-expanded="true"></a>
					</span>
				</h4>
			</div>
			<div id="aff_earnings" class="panel-collapse collapse in ">
				<div class="panel-body">
					<div class="row">
						<div class="col-md-2">
							<?=lang('Affiliate Username');?>
						</div>
						<div class="col-md-4">
			                <input class="form-control input-sm" name="username"/>
						</div>
					</div>
					<div class="row">
						<div class="col-md-2">
							<?=lang('Date');?>
						</div>
						<div class="col-md-4">
			                <input id="aff_earnings_date_picker" class="form-control input-sm dateInput" data-start="#startDate" data-end="#endDate" data-time="false"/>
			                <input type="hidden" id="startDate" name="startDate" value="<?=date('Y-m-d', strtotime('-1 day'));?>" />
			                <input type="hidden" id="endDate" name="endDate" value="<?=date('Y-m-d', strtotime('-1 day'))?>" />
						</div>
					</div>
					<div class="row">
						<div class="col-md-2">
							<?=lang('Dry Run');?>
						</div>
						<div class="col-md-4">
							<input type="checkbox" name="dry_run" value="true">
						</div>
					</div>
				</div>

				<div class="panel-footer">
					<input type="submit" class="btn btn-portage" value="<?=lang('Submit'); ?>">
				</div>
			</div>
		</div>
	</form>
<?php endif; ?>

<form action="<?=site_url('system_management/post_adjust_game_report'); ?>" method="POST">
	<div class="panel panel-primary panel_main">
	    <div class="panel-heading">
	        <h4 class="panel-title"><?=lang('Adjust Game Report'); ?>
				<span class="pull-right">
					<a data-toggle="collapse" href="#adjust_game_report" class="btn btn-info btn-xs collapsed" aria-expanded="true"></a>
				</span>
	        </h4>
	    </div>
	    <div id="adjust_game_report" class="panel-collapse collapse in ">
		    <div class="panel-body">
		        <div class="row">
		            <div class="col-md-2">
		            	<?=lang('Password');?>
		            </div>
		            <div class="col-md-4">
		                <input type="password" class="form-control input-sm" name="password" value="" />
		            </div>
		        </div>
		        <div class="row">
		            <div class="col-md-2">
		            	<?=lang('Year Month');?>
		            	format: YYYYmm
		            </div>
		            <div class="col-md-4">
		                <input type="text" class="form-control input-sm" name="adjust_year_month" value="" />
		            </div>
		        </div>
		        <div class="row">
		            <div class="col-md-12">
		            	<?=lang('Adjust Content');?>
		            	format: username,game_description_id,bet,result
		                <textarea name="adjust_content" class="form-control input-sm" rows="10"></textarea>
		            </div>
		        </div>
		    </div>
		    <div class="panel-footer">
		        <input type="submit" class="btn btn-portage" value="<?=lang('Submit'); ?>">
		    </div>
	    </div>
	</div>
</form>

<div class="panel panel-primary panel_main">
    <div class="panel-heading">
        <h4 class="panel-title"><?=lang('Import Players'); ?>
			<span class="pull-right">
				<a data-toggle="collapse" href="#import_players" class="btn btn-info btn-xs collapsed" aria-expanded="true"></a>
			</span>
        </h4>
    </div>
    <div id="import_players" class="panel-collapse collapse in ">
    	<div class="panel-body">
			<a href="<?=site_url('/player_management/import_players')?>" class="btn btn-sm btn-portage"><?=lang('Import Players by Upload CSV')?></a>
    	</div>
    </div>
</div>

<div class="panel panel-primary panel_main">
    <div class="panel-heading">
        <h4 class="panel-title"><?=lang('Game Syncing Method List'); ?>
			<span class="pull-right">
				<a data-toggle="collapse" href="#sync_methods" class="btn btn-info btn-xs collapsed" aria-expanded="true"></a>
			</span>
        </h4>
    </div>

    <div id="sync_methods" class="panel-collapse collapse in ">
	    <div class="panel-body">
	    	<table class="table table-striped" id="sync_methods_table">
	    		<thead>
	    			<tr>
	    				<th><?=lang('Game Platform ID')?></th>
	    				<th><?=lang('Game Name')?></th>
	    				<th><?=lang('Syncing By / Status')?></th>
	    			</tr>
	    		</thead>
	    		<tbody>
	    			<?php if (empty($api_with_sync_method)): ?>
	    				<tr><td colspan="3" style="text-align: center"><?=lang('lang.norec')?></td></tr>
	    			<?php else: ?>
		    			<?php foreach ($api_with_sync_method as $key => $value): ?>
		    				<tr>
			    				<td><?=isset($value['id']) && trim($value['id']) != "" ? $value['id']: lang('lang.norecyet')?></td>
			    				<td><?=isset($value['system_name']) && trim($value['system_name']) != "" ? $value['system_name']: lang('lang.norecyet')?></td>
			    				<td><?=isset($value['sync_status']) && trim($value['sync_status']) != ""  ? $value['sync_status']: lang('lang.norecyet')?></td>
			    			</tr>
		    			<?php endforeach ?>
	    			<?php endif ?>
	    		</tbody>
	    	</table>
	    </div>
    </div>
</div>

<form action="<?=site_url('system_management/post_batch_sync_balance'); ?>" method="POST">
	<div class="panel panel-primary panel_main">
		<div class="panel-heading">
			<h4 class="panel-title"><?=lang('Batch Sync All Player Balance')?>
				<span class="pull-right">
					<a data-toggle="collapse" href="#batch_sync_all_player_balance" class="btn btn-info btn-xs collapsed" aria-expanded="true"></a>
				</span>
			</h4>
		</div>
		<div id="batch_sync_all_player_balance" class="panel-collapse collapse in ">
			<div class="panel-body">
				<div class="row">
					<div class="col-md-2">
						<?=lang('Mode');?>
					</div>
					<div class="col-md-4">
						<select class="form-control input-sm" name="mode" id="mode">
							<option value="available" selected><?=lang('Available');?></option>
							<option value="last_one_hour"><?=lang('Last One Hour');?></option>
							<option value="all"><?=lang('All');?></option>
						</select>
					</div>
				</div>
				<div class="row">
					<div class="col-md-2">
						<?=lang('Max Number');?>
					</div>
					<div class="col-md-4">
						<input class="form-control input-sm" type="number" name="max_number" value="999999">
					</div>
				</div>

				<div class="row">
					<div class="col-md-2">
						<?=lang('Game');?>
					</div>
					<div class="col-md-4">
						<select class="form-control input-sm" name="apiId" id="apiId">
							<option value="" selected><?=lang('All');?></option>

							<?php foreach ($this->utils->getGameSystemMap() as $key => $value): ?>
							<option value="<?=$key;?>"><?=$value;?></option>
							<?php endforeach ?>
						</select>
					</div>
				</div>

				<div class="row">
					<div class="col-md-2">
						<?=lang('Dry Run');?>
					</div>
					<div class="col-md-4">
					<input type="checkbox" name="dry_run" value="true">
					</div>
				</div>
			</div>

			<div class="panel-footer">
				<input type="submit" class="btn btn-portage" value="<?=lang('Submit'); ?>">
			</div>
		</div>
	</div>
</form>

<form action="<?=site_url('system_management/post_sync_mdb'); ?>" method="POST">
	<div class="panel panel-primary panel_main">
		<div class="panel-heading">
			<h4 class="panel-title"><?=lang('Sync Currency'); ?>
				<span class="pull-right">
					<a data-toggle="collapse" href="#sync_mdb" class="btn btn-info btn-xs collapsed" aria-expanded="true"></a>
				</span>
			</h4>
		</div>
		<div id="sync_mdb" class="panel-collapse collapse in ">
			<div class="panel-body">
				<div class="row">
					<div class="col-md-2">
						<?=lang('Player Username');?>
					</div>
					<div class="col-md-4">
						<input class="form-control input-sm" type="text" name="playerUsername">
					</div>
				</div>
				<div class="row">
					<div class="col-md-2">
						<?=lang('Agency Username');?>
					</div>
					<div class="col-md-4">
						<input class="form-control input-sm" type="text" name="agencyUsername">
					</div>
				</div>
				<div class="row">
					<div class="col-md-2">
						<?=lang('Affiliate Username');?>
					</div>
					<div class="col-md-4">
						<input class="form-control input-sm" type="text" name="affiliateUsername">
					</div>
				</div>
				<div class="row">
					<div class="col-md-2">
						<?=lang('Admin Username');?>
					</div>
					<div class="col-md-4">
						<input class="form-control input-sm" type="text" name="adminUsername">
					</div>
				</div>
				<div class="row">
					<div class="col-md-2">
						<?=lang('Role Name');?>
					</div>
					<div class="col-md-4">
						<input class="form-control input-sm" type="text" name="roleName">
					</div>
				</div>
				<div class="row">
					<div class="col-md-2">
						<?=lang('Reg Setting Type');?>
					</div>
					<div class="col-md-4">
						<select class="form-control input-sm" name="regSettingType">
							<option value=""><?=lang('NONE')?></option>
							<option value="<?=Marketing::TYPE_PLAYER_REGISTRATION?>"><?=lang('Player Registration Settings')?></option>
							<option value="<?=Marketing::TYPE_AFFILIATE_REGISTRATION?>"><?=lang('Affiliate Registration Settings')?></option>
						</select>
					</div>
				</div>
			</div>
			<div class="panel-footer">
				<input type="submit" class="btn btn-chestnutrose" value="<?=lang('Sync'); ?>">
			</div>
		</div>
	</div>
</form>

<form action="<?=site_url('system_management/batch_move_deposit_note_to_sale_orders_notes'); ?>" method="POST">
	<div class="panel panel-primary panel_main">
		<div class="panel-heading">
			<h4 class="panel-title"><?=lang('Batch move deposit note to sale_orders_notes'); ?>
				<span class="pull-right">
					<a data-toggle="collapse" href="#move_deposit_notes_data" class="btn btn-info btn-xs collapsed" aria-expanded="true"></a>
				</span>
			</h4>
		</div>
		<div id="move_deposit_notes_data" class="panel-collapse collapse in ">
			<div class="panel-body">
				<div>
					<?=lang('It will move original deposit note data to sale_orders_notes table')?>
				</div>
			</div>
			<div class="panel-footer">
				<input type="submit" class="btn btn-portage" value="<?=lang('Submit'); ?>">
			</div>
		</div>
	</div>
</form>

<form action="<?=site_url('system_management/batch_move_withdrawal_note_to_walletaccount_notes'); ?>" method="POST">
	<div class="panel panel-primary panel_main">
		<div class="panel-heading">
			<h4 class="panel-title"><?=lang('Batch move withdrawal note to walletaccount_notes'); ?>
				<span class="pull-right">
					<a data-toggle="collapse" href="#move_withdrawal_notes_data" class="btn btn-info btn-xs collapsed" aria-expanded="true"></a>
				</span>
			</h4>
		</div>
		<div id="move_withdrawal_notes_data" class="panel-collapse collapse in ">
			<div class="panel-body">
				<div>
					<?=lang('It will move original withdrawal note data to walletaccount_notes table')?>
				</div>
			</div>
			<div class="panel-footer">
				<input type="submit" class="btn btn-portage" value="<?=lang('Submit'); ?>">
			</div>
		</div>
	</div>
</form>

<?php if ($this->utils->getConfig('enable_dev_func_manual_update_admin_dashboard')) : ?>
<form action="<?=site_url('system_management/post_update_admin_dashboard'); ?>" method="POST">
	<div class="panel panel-primary panel_main">
		<div class="panel-heading">
			<h4 class="panel-title"><?= lang('devfunc.manual_update_admin_dashboard'); ?>
				<span class="pull-right">
					<a data-toggle="collapse" href="#manual_update_admin_dashboard" class="btn btn-info btn-xs collapsed" aria-expanded="true"></a>
				</span>
			</h4>
		</div>
		<div id="manual_update_admin_dashboard" class="panel-collapse collapse in">
			<div class="panel-body">
				<div class="row">
					<div class="col-md-12">
						<p>
							<?=lang('devfunc.rebuilds_dashboard')?>
						</p>
						<div>
							<div class="small">
								<?= lang('devfunc.last_update') ?>:
								<?= $updated_at ?: '&mdash;' ?>
							</div>
							<table class="small devfunc">
								<tr>
									<th><?= lang('devfunc.base_date') ?>:</th>
									<td class="text-success"><?= $date_base ?: '&mdash;' ?></td>
								</tr>
								<tr>
									<th><?= lang('devfunc.date_range') ?>:</th>
									<td class="text-success"><?= $date_range_from ?> &mdash; <?= $date_range_to ?></td>
								</tr>
								<tr>
									<th><?= lang('devfunc.disp_date') ?>:</th>
									<td class="text-success"><?= $date_disp ?: '&mdash;' ?></td>
								</tr>
							</table>
						</div>
						<hr />
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<?=lang('devfunc.base_date')?>
						<ul class="small">
		                	<li><?= lang('devfunc.base_date.1a') ?> <strong><?= lang('devfunc.base_date.1b') ?></strong></li>
		                	<li><?= lang('devfunc.base_date.2a') ?> <strong><?= lang('devfunc.base_date.2b') ?></strong></li>
		                	<li><?= lang('devfunc.base_date.3a') ?> <strong><?= lang('devfunc.base_date.3b') ?></strong></li>
		                	<li><?= lang('devfunc.base_date.4a') ?> <strong><?= lang('devfunc.base_date.4b') ?></strong></li></li>
		                </ul>
					</div>
					<div class="col-md-4">
		                <input name="date_base" id="date_base" class="form-control input-sm dateInput" type="text" value="<?= set_value('date', $this->utils->getNowForMysql()) ?>" data-time="true" />
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<?= lang('devfunc.date_range') ?>
						<ul class="small">
		                	<li><?= lang('devfunc.date_range.1') ?></li>
		                </ul>
					</div>
					<div class="col-md-4">
						<input id="daterange_range" class="form-control input-sm dateInput" data-start="#date_range_from" data-end="#date_range_to" data-time="false"/>
						<input type="hidden" id="date_range_from" name="date_range_from" value="<?= date('Y-m-d', strtotime('-6 days')) ?>" />
						<input type="hidden" id="date_range_to" name="date_range_to"  value=""/>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<?= lang('devfunc.disp_date') ?>
						<ul class="small">
		                	<li><?= lang('devfunc.disp_date.1') ?></li>
		                </ul>
					</div>
					<div class="col-md-4">
		                <input name="date_disp" id="date_disp" class="form-control input-sm dateInput" type="text" value="<?= set_value('date', $this->utils->getNowForMysql()) ?>" data-time="true" />
		                <div>
		                	<button id="date_disp_set_today" type="button" class="btn btn-sm btn-success date_disp set_today">
		                		<?= lang('devfunc.use_today') ?>
		                	</button>
		                	<button id="date_disp_set_base" type="button" class="btn btn-sm btn-info date_disp set_base">
		                		<?= lang('devfunc.use_base_date') ?>
		                	</button>
		                </div>
					</div>
				</div>
			</div>
			<div class="panel-footer">
				<input type="submit" class="btn btn-primary" value="<?=lang('Submit'); ?>">
			</div>
		</div>
	</div>
</form>
<?php endif; ?>

<div class="panel panel-primary panel_main">
	<div class="panel-heading">
		<h4 class="panel-title"><?=lang('Set Registration Fields To Default Order'); ?>
			<span class="pull-right">
				<a data-toggle="collapse" href="#set_registration_fields_order" class="btn btn-info btn-xs collapsed" aria-expanded="true"></a>
			</span>
		</h4>
	</div>
	<div id="set_registration_fields_order" class="panel-collapse collapse in ">
		<div class="panel-body">
			<div>
				<?=lang('Will reorder according to config "registration_fields_default_order"')?>
			</div>
		</div>
		<div class="panel-footer">
	        <a href="<?= site_url('marketing_management/setRegistrationSettingsToDefaultOrder') ?>" class="btn btn btn-primary">
	            <?= lang('Reset Fields Order') ?>
	        </a>
		</div>
	</div>
</div>

<script type="text/javascript">
	$( document ).ready(function() {
	    $("#dev_functions").addClass('active');

	    hljs.initHighlightingOnLoad();
		var sync_methods_table_dataTable = $('#sync_methods_table').DataTable();
	});

	$('.date_disp.set_today').click( function () {
		$('#date_disp').val(moment().format('YYYY-MM-DD HH:mm:ss'));
	});

	$('.date_disp.set_base').click( function () {
		$('#date_disp').val($('#date_base').val());
	});
</script>

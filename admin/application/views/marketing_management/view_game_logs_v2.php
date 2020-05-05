<!--
<?php
echo json_encode($conditions,JSON_PRETTY_PRINT);
?>
-->

<style type="text/css">
	#collapseViewGameLogs .col-md-2,
	#collapseViewGameLogs .col-md-4,
	#collapseViewGameLogs .col-md-6{
		height: 70px;
	}

	#collapseViewGameLogs .col-md-2 .non-aff{
		margin-top: 33px;
	}

</style>

<form class="form-horizontal" id="search-form" method="get" role="form">
	<div class="panel panel-primary hidden">

		<div class="panel-heading">
			<h4 class="panel-title">
				<i class="fa fa-search"></i> <?=lang("lang.search")?>
				<span class="pull-right">
					<button type="button" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info' ?>" onclick="window.open('/marketing_management/kingrich_api_response_logs')"><?= lang('Open API Logs') ?></button>
					<button type="button" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info' ?>" onclick="window.open('/marketing_management/kingrich_summary_report')"><?= lang('Summary Report') ?></button>
					<?php if ($this->permissions->checkPermissions('view_kingrich_data_scheduler')): ?>
						<button type="button" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info' ?>" onclick="window.open('/marketing_management/kingrich_scheduler')"><?= lang('Scheduler') ?></button>
					<?php endif; ?>
                	<a data-toggle="collapse" href="#collapseViewGameLogs" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info' ?> <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
            	</span>
            <!--
            <span class="pull-right m-r-10">
                <label class="checkbox-inline">
					<input type="checkbox" id="gametype" value="gametype" onclick="checkSearchGameLogs(this.value);"/> <?php echo lang('Game Type');?>
				</label>
            </span> -->

				<?php include __DIR__ . "/../includes/report_tools.php" ?>

			</h4>
		</div>

		<div id="collapseViewGameLogs" class="panel-collapse <?=$this->config->item('default_open_search_panel') ? '' : 'collapse in'?>">
			<div class="panel-body">
				<input type="hidden" id="game_description_id" name="game_description_id" value="<?php echo $conditions['game_description_id']; ?>" />
				<div class="col-md-4">
					<label class="control-label" for="search_game_date"><?=lang('report.sum02');?></label>
					<input id="search_game_date" class="form-control input-sm dateInput" data-start="#by_date_from" data-end="#by_date_to" data-time="true"/>
					<input type="hidden" id="by_date_from" name="by_date_from" value="<?php echo $conditions['by_date_from']; ?>" />
					<input type="hidden" id="by_date_to" name="by_date_to"  value="<?php echo $conditions['by_date_to']; ?>"/>
				</div>
                <div class="col-md-2 col-lg-2">
                    <label class="control-label" for="group_by"><?=lang('Timezone')?></label>
                    <select id="timezone" name="timezone"  class="form-control input-sm">
                     <?php for($i = 12;  $i >= -12; $i--): ?>
                         <?php if($conditions['timezone'] || $conditions['timezone'] == '0' ): ?>
                             <option value="<?php echo $i > 0 ? "+{$i}" : $i ;?>" <?php echo ($i == $conditions['timezone']) ? 'selected' : ''?>> <?php echo $i > 0 ? "+{$i}" : $i ;?>:00</option>
                         <?php else: ?>
                            <option value="<?php echo $i > 0 ? "+{$i}" : $i ;?>" <?php echo ($i==8) ? 'selected' : ''?>> <?php echo $i > 0 ? "+{$i}" : $i ;?>:00</option>
                        <?php endif;?>
                    <?php endfor;?>
                	</select>
                </div>

				<div class="col-md-2">
					<label class="control-label" for="by_username_match_mode">
						<input type="radio" name="by_username_match_mode" value="1" <?= $conditions['by_username_match_mode'] == '1' ? 'checked' : ''?>/> <?php echo lang('Similar');?>
						<input type="radio" name="by_username_match_mode" value="2" <?= $conditions['by_username_match_mode'] == '2' ? 'checked' : ''?>/> <?php echo lang('Exact');?>
					</label>
					<label class="control-label" for="by_username"><?=lang('Player Username');?></label>
					<input type="text" name="by_username" id="by_username" class="form-control input-sm disable-esp-char" value="<?php echo $conditions['by_username']; ?>" />
				</div>

				<div class="col-md-2">
					<label class="control-label" for="by_game_code"><?=lang('sys.gd9');?> </label>
					<input type="text" name="by_game_code" id="by_game_code" class="form-control input-sm" value="<?php echo $conditions['by_game_code']; ?>" />
				</div>

				<div class="col-md-2">
					<label class="control-label" for="by_game_platform_id"><?=lang('player.ui29');?> </label>
					<select class="form-control input-sm" name="by_game_platform_id" id="by_game_platform_id">
						<option value="" ><?=lang('lang.selectall');?></option>
						<?php foreach ($game_platforms as $game_platform) {?>
							<option value="<?=$game_platform['id']?>" <?php echo $conditions['by_game_platform_id']==$game_platform['id'] ? 'selected="selected"' : '' ; ?>><?=$game_platform['system_code'];?></option>
						<?php }?>
					</select>
				</div>


				<div class="col-md-2">
					<label class="control-label" for="round_no"><?=lang('Transaction ID');?> </label>
					<input type="text" name="round_no" id="round_no" class="form-control input-sm" value="<?php echo $conditions['round_no']; ?>" />
				</div>

				<div class="col-md-2">
					<label class="control-label" for="flag"><?=lang('player.ut10');?> </label>
					<select class="form-control input-sm" name="by_game_flag" id="by_game_flag">
						<option value=""><?=lang('lang.selectall');?></option>
						<option value="<?=Game_logs::FLAG_GAME?>" <?php echo ($conditions['by_game_flag']==Game_logs::FLAG_GAME ? 'selected="selected"' : '' ); ?> ><?=lang('sys.gd5');?></option>
						<option value="<?=Game_logs::FLAG_TRANSACTION?>" <?php echo $conditions['by_game_flag']==Game_logs::FLAG_TRANSACTION ? 'selected="selected"' : '' ; ?> ><?=lang('pay.transact');?></option>
					</select>
				</div>

				<div class="col-md-2">
					<label class="control-label" for="flag"><?=lang('Bet Type');?> </label>
					<select class="form-control input-sm" name="by_bet_type" id="by_bet_type">
						<option value="all" <?php echo ($conditions['by_bet_type']=="all" ? 'selected="selected"' : '' ); ?> ><?=lang('lang.selectall');?></option>
						<option value="cash" <?php echo ($conditions['by_bet_type']=="cash" ? 'selected="selected"' : '' ); ?> ><?=lang('Cash');?></option>
						<option value="credit" <?php echo ($conditions['by_bet_type']=="credit" ? 'selected="selected"' : '' ); ?> ><?=lang('Credit');?></option>
					</select>
				</div>

				<div class="col-md-2">
					<label class="control-label" for="by_amount_from"><?=lang('mark.resultAmount')?> &gt;=</label>
					<input id="by_amount_from" type="number" name="by_amount_from" class="form-control input-sm" value="<?php echo $conditions['by_amount_from'] ?>" step=".01"/>
				</div>

				<div class="col-md-2">
					<label class="control-label" for="by_amount_to"><?=lang('mark.resultAmount')?> &lt;=</label>
					<input id="by_amount_to" type="number" name="by_amount_to" class="form-control input-sm" value="<?php echo $conditions['by_amount_to'] ?>" step=".01"/>
				</div>

				<div class="col-md-2">
					<label class="control-label" for="by_bet_amount_from"><?php echo lang('Bet Amount'); ?> &gt;=</label>
					<input id="by_bet_amount_from" type="number" name="by_bet_amount_from" class="form-control input-sm" value="<?php echo $conditions['by_bet_amount_from'] ?>" step=".01"/>
				</div>

				<div class="col-md-2">
					<label class="control-label" for="by_bet_amount_to"><?php echo lang('Bet Amount'); ?> &lt;=</label>
					<input id="by_bet_amount_to" type="number" name="by_bet_amount_to" class="form-control input-sm" value="<?php echo $conditions['by_bet_amount_to'] ?>" step=".01"/>
				</div>

				<div class="col-md-2">
					<label class="control-label" for="by_debit_amount_from"><?php echo lang('Debit - (Player Loss)'); ?> &gt;=</label>
					<input id="by_debit_amount_from" type="number" name="by_debit_amount_from" class="form-control input-sm" value="<?php echo $conditions['by_debit_amount_from'] ?>" step=".01"/>
				</div>

				<div class="col-md-2">
					<label class="control-label" for="by_debit_amount_to"><?php echo lang('Debit - (Player Loss)'); ?> &lt;=</label>
					<input id="by_debit_amount_to" type="number" name="by_debit_amount_to" class="form-control input-sm" value="<?php echo $conditions['by_debit_amount_to'] ?>" step=".01"/>
				</div>

				<div class="col-md-2">
					<label class="control-label" for="by_credit_amount_from"><?php echo lang('Credit + (Player Win)'); ?> &gt;=</label>
					<input id="by_credit_amount_from" type="number" name="by_credit_amount_from" class="form-control input-sm" value="<?php echo $conditions['by_credit_amount_from'] ?>" step=".01"/>
				</div>

				<div class="col-md-2">
					<label class="control-label" for="by_credit_amount_to"><?php echo lang('Credit + (Player Win)'); ?> &lt;=</label>
					<input id="by_credit_amount_to" type="number" name="by_credit_amount_to" class="form-control input-sm" value="<?php echo $conditions['by_credit_amount_to'] ?>" step=".01"/>
				</div>

				<div class="col-md-2">
					<label class="control-label" for="flag"><?=lang('Player Type');?> </label>
					<select class="form-control input-sm" name="by_player_type" id="by_bet_type">
						<option value="all" <?php echo ($conditions['by_player_type']=="all" ? 'selected="selected"' : '' ); ?> ><?=lang('lang.selectall');?></option>
						<option value="Real" <?php echo ($conditions['by_player_type']=="Real" ? 'selected="selected"' : '' ); ?> ><?=lang('Real');?></option>
						<option value="Test" <?php echo ($conditions['by_player_type']=="Test" ? 'selected="selected"' : '' ); ?> ><?=lang('Test');?></option>
					</select>
				</div>

				<div class="col-md-2">
					<label class="control-label" for="by_game_type_globalcom"><?=lang('Game Type');?> </label>
					<select class="form-control input-sm" name="by_game_type_globalcom" id="by_game_type_globalcom">
						<option value="all" <?php echo ($conditions['by_game_type_globalcom']=="all" ? 'selected="selected"' : '' ); ?> ><?=lang('lang.selectall');?></option>
						<?php if(!empty($this->config->item('kingrich_gametypes'))) : ?>
							<?php foreach ($this->config->item('kingrich_gametypes') as $key => $value): ?>
								<option value="<?= $key ?>" <?php echo ($conditions['by_game_type_globalcom']== $key ? 'selected="selected"' : '' ); ?> ><?= $key ?></option>
							<?php endforeach ?>
						<?php endif; ?>
					</select>
				</div>

				<div class="col-md-2">
					<label class="control-label" for="flag"><?=lang('Submission Status');?> </label>
					<select class="form-control input-sm" name="submitted_status" id="submitted_status">
						<option value="all" <?php echo ($conditions['submitted_status']=="all" ? 'selected="selected"' : '' ); ?> ><?=lang('lang.selectall');?></option>
						<option value="submitted" <?php echo ($conditions['submitted_status']=="submitted" ? 'selected="selected"' : '' ); ?> ><?=lang('Submitted');?></option>
						<option value="not_submitted" <?php echo ($conditions['submitted_status']=="not_submitted" ? 'selected="selected"' : '' ); ?> ><?=lang('Not Submitted');?></option>
					</select>
				</div>

				<div class="col-md-4">
					<label class="control-label" for="batch_transaction_id_filter"><?=lang('Batch Transaction ID');?> </label>
					<input type="text" name="batch_transaction_id_filter" id="batch_transaction_id_filter" class="form-control input-sm" value="<?php echo $conditions['batch_transaction_id_filter']; ?>" />
				</div>

				<?php if( !empty($kingrich_currency_branding) && $this->config->item('multiple_currency_enabled') ) :?>
					<div class="col-md-2">
						<label class="control-label" for="flag"><?=lang('Currency');?> </label>
						<select class="form-control input-sm" name="by_kingrich_currency_branding" id="by_kingrich_currency_branding">
							<option value="" <?php echo ($conditions['by_kingrich_currency_branding']=="all" ? 'selected="selected"' : '' ); ?> ><?=lang('lang.selectall');?></option>
							<?php foreach ($kingrich_currency_branding as $key => $value) : ?>
								<option value="<?=$key?>" <?php echo ($conditions['by_kingrich_currency_branding']== $key ? 'selected="selected"' : '' ); ?> ><?=$key?></option>
							<?php endforeach; ?>
						</select>
					</div>
				<?php endif; ?>

				<?php if ($this->utils->isEnabledFeature('show_agent_name_on_game_logs')) : ?>
					<div class="col-md-2">
						<label class="control-label" for="agency_username"><?php echo lang('Agent Username'); ?> </label>
						<input id="agency_username" type="text" name="agency_username" class="form-control input-sm" value="<?php echo $conditions['agency_username'] ?>"/>
					</div>
				<?php endif; ?>

				<div class="col-md-2">
					<label class="non-aff">
						<input type="checkbox" name="by_no_affiliate" value="true" <?php echo $conditions['by_no_affiliate'] ? 'checked="checked"' : '' ; ?>/>
						<?php echo lang('Only non-affiliate'); ?>
					</label>
				</div>
				<div class="col-md-2">
					<input type="hidden" name="trigger_data_api" id="trigger_data_api" value="false"/>
					<label class="non-aff">
						<input type="checkbox" name="for_data_api" id="for_data_api" value="true" <?php echo $conditions['for_data_api'] ? 'checked="checked"' : '' ; ?>/>
						<?php echo lang('Data for API'); ?>
					</label>
				</div>
				<div class="col-md-2">
                    <label class="non-aff">
                        <input id="check-free-spin" type="checkbox" name="by_free_spin" value="true" <?=$conditions['by_free_spin'] ? 'checked="checked"' : '' ; ?>/>
                        <?=lang('Only Free Bet'); ?>
                    </label>
				</div>
				<?php if(!empty($this->config->item('kingrich_gametypes'))) : ?>
					<div id="gametype_search" class="col-md-12 show" style="display: none;">
						<fieldset style="padding-bottom: 8px">
							<legend>
								<label class="control-label"><?=lang('sys.gd6');?></label>
							</legend>
							<div class="row">
								<div class="col-md-3">
									<div class="checkbox">
										<label>
											<div id="gameTree" class="col-md-12">
												<table class="table table-bordered">
													<tr>
														<th><?= lang('reg.76')?></th>
														<th><?= lang('cms.gametype')?></th>
													</tr>
													<?php foreach ($this->config->item('kingrich_gametypes') as $key => $value): ?>
														<?php if( isset($value['description']) ) : ?>
															<tr>
																<td><?= $key	?></td>
																<td><?= $value['description'] ?></td>
															</tr>
														<?php endif ?>
													<?php endforeach ?>
												</table>
											</div>
										</label>
									</div>
								</div>
							</div>
						</fieldset>
					</div>
				<?php endif; ?>
				<!-- div id="gametype_search" class="col-md-12 show" style="display: none;">
					<fieldset style="padding-bottom: 8px">
						<legend>
							<label class="control-label"><?=lang('sys.gd6');?></label>
						</legend>
						<div class="row">
							<div class="col-md-3">
								<div class="checkbox">
									<label>
										<input type="hidden" id="game_type_tree" name="game_type_id" value="<?php echo $conditions['game_type_id']; ?>">
										<div id="gameTree" class="col-md-12">
										</div>
									</label>
								</div>
							</div>
						</div>
					</fieldset>
				</div-->
			</div>
			<div class="panel-footer text-right">
				<?php if ($this->permissions->checkPermissions('send_gamelogs_api')): ?>
					<button  type="button" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary' ?>" id="button-submit-to-api" disabled="disabled"><?php echo lang('Submit To API'); ?></button>
				<?php endif ?>
				<input type="submit" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary' ?>" id="btn-submit" value="<?php echo lang('Search'); ?>" >
			</div>
		</div>
	</div>

</form>

<div class="panel panel-primary">
	<div class="panel-heading">
		<h4 class="panel-title"><i class="fa fa-list"></i> <?=lang('player.ui48');?> </h4>
	</div>

	<div class="panel-body" >
		<?php if ($this->permissions->checkPermissions('import_game_logs')): ?>
			<form action="/marketing_management/import_game_logs" method="post" enctype="multipart/form-data">
	    		<div class="row">
					<div class="col-md-3">
						<label class="control-label" for="flag"><?=lang('Upload File');?> </label>						
						<input type="file" id="import" name="import" class="form-control" accept=".csv" required="required" style="padding: 0; height: 40px;" />
					</div>

					<div class="col-md-4">
						<label class="control-label"><?=lang('report.sum02')?></label>
			            <input class="form-control dateInput" id="import_date_range" data-start="#date_from" data-end="#date_to" data-time="false"/>
			            <input type="hidden" id="date_from" name="date_from" data-time="false" required />
			            <input type="hidden" id="date_to" name="date_to" data-time="false" required/>
					</div>

					<?php if( !empty($kingrich_currency_branding) && $this->config->item('multiple_currency_enabled') ) :?>
						<div class="col-md-3">
							<label class="control-label" for="flag"><?=lang('Currency');?> </label>
							<select class="form-control input-sm" name="import_currency" id="import_currency" required="required" style="height: 40px;">
								<option value=""><?=lang('lang.selectall');?></option>
								<?php foreach ($kingrich_currency_branding as $key => $value) : ?>
									<option value="<?=$key?>"><?=$key?></option>
								<?php endforeach; ?>
							</select>
						</div>
					<?php endif; ?>
					<div class="col-md-2">
				    	<span class="input-group-btn" style="padding-top: 25px;">
							<button type="submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary' ?>" style="height: 40px; width: 100%;">Import</button>
						</span>
					</div>
				</div>
			</form>
		<?php endif ?>

		<small class="text-muted"><?php echo lang('Note') . ': ' . lang('Green: transfer, Red: free bet'); ?></small>
		<div class="table-responsive">
			<table id="myTable" class="table table-bordered">
				<thead>
				<tr>
                    <th class=""><?=lang('Player Username');?></th>
                    <th class=""><?=lang('Player No.');?></th>
                    <th class=""><?=lang('Player Type');?></th>
                    <th class=""><?=lang('Date and Time of Transactions');?></th>
                    <th class=""><?=lang('Transaction ID');?></th>
                    <th class=""><?=lang('Settlement Date');?></th>
                    <th class=""><?=lang('Brand');?></th>
                    <th class=""><?=lang('sys.api01');?></th>
                    <th class=""><?=lang('cms.gameprovider');?></th>
                    <th class=""><?=lang('sys.gd9');?></th>
                    <th class=""><?=lang('sys.gd8');?></th>
                    <th class=""><?=lang('sys.gd6');?></th>
                    <th class=""><?=lang('Currency');?></th>
                    <th class=""><?=lang('Bet type');?></th>
                    <th class="right-text-col"><?=lang('Bet Amount');?></th>
                    <th class="right-text-col"><?=lang('Debit - (Player Loss)');?></th>
                    <th class="right-text-col"><?=lang('Credit + (Player Win)');?></th>
                    <th class="right-text-col"><?=lang('Net Amount');?></th>
				</tr>
				</thead>
				<tbody>
				</tbody>
				<tfoot>
				<tr>
					<th><?=lang('Sub Total')?></th>
				    <th colspan="13"></th>
				    <th style="text-align: right"><span class="sub-bet-amount">0.00</span></th>
				    <th style="text-align: right"><span class="sub-player-loss">0.00</span></th>
				    <th style="text-align: right"><span class="sub-player-win">0.00</span></th>
				    <th style="text-align: right"><span class="sub-net-amount">0.00</span></th>
				</tr>
				
				<tr>
					<th><?=lang('Total')?></th>
				    <th colspan="13"></th>
				    <th style="text-align: right"><span class="bet-amount">0.00</span></th>
				    <th style="text-align: right"><span class="player-loss">0.00</span></th>
				    <th style="text-align: right"><span class="player-win">0.00</span></th>
				    <th style="text-align: right"><span class="net-amount">0.00</span></th>
				</tr>
				<tr>
				    <th colspan="18" style="text-align: right;">    
						<?=lang('cms.totalBetAmount');?>: <span class="bet-total">0.00</span><br>
						<?=lang('cms.totalResultAmount');?>: <span class="result-total">0.00</span><br>
				    	<?=lang('Total Bet + Result Amount');?>: <span class="bet-result-total">0.00</span><br>
						<?=lang('Total Win');?>: <span class="win-total">0.00</span><br>
						<?=lang('Total Loss');?>: <span class="loss-total">0.00</span><br>
						<?=lang('Average Bet');?>: <span class="ave-bet-total">0.00</span><br>
						<?=lang('Total Number of Bets');?>: <span class="bet-count-total">0</span>
					</th>
				</tr>
				<tr>
					<td colspan="18" id="platform-summary" style="text-align: right;"></td>
				</tr>
				</tfoot>
			</table>
		</div>
	</div>
	<div class="panel-footer"></div>
</div>
<!-- Modal Response -->
<div id="modalResponse" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?= lang('API Response'); ?></h4>
      </div>
      <div class="modal-body">
        <table id="classTable" class="table table-bordered">
          <thead>
          </thead>
          <tbody>
            <tr>
              <td>batch_transaction_id</td>
              <td>created_date</td>
              <td>status</td>
            </tr>
            <tr>
				<td id="batch_transaction_id"></td>
				<td id="created_date"></td>
				<td id="status"></td>
			</tr>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
<form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
<input name='json_search' type="hidden">
</form>
<?php }?>
<script type="text/javascript">
	var hiddenColumns = [];
	var rightTextColumns = [];
	var export_type="<?php echo $this->utils->isEnabledFeature('export_excel_on_queue') ? 'queue' : 'direct';?>";
	var gameTypeId = '<?php echo $conditions['game_type_id']; ?>';
	gameTypeId = gameTypeId.split(',');
	// console.log(gameTypeId);

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

	$(document).ready(function(){

		$('#date_from').val(moment().startOf('day').format('YYYY-MM-DD'));
		$('#date_to').val(moment().startOf('day').format('YYYY-MM-DD'));

		$("#col-player-username").removeClass("hidden-col");

	    // Get hidden, text right and flag column
	    var elem = $('#myTable thead tr th');
	    var flagColIndex = elem.filter(function(index){
	        if ($(this).hasClass('hidden-col')) {
	            hiddenColumns.push(index);
	        }

	        if ($(this).hasClass('right-text-col')) {
	            rightTextColumns.push(index);
	        }

	        if ($(this).attr("id") == "flag_col") {
	            return index;
	        }
	    }).index();

	    var realBetCol = elem.filter(function(index){
            if ($(this).attr("id") == "col-real-bet") {
                return index;
            }
        }).index();
        var avlBetCol = elem.filter(function(index){
            if ($(this).attr("id") == "col-available-bet") {
                return index;
            }
        }).index();
        var resAmtCol = elem.filter(function(index){
            if ($(this).attr("id") == "col-result-amt") {
                return index;
            }
        }).index();

		$('.disable-esp-char').on('input', function() {
			var c = this.selectionStart,
					r = /[^a-z0-9-_.]/gi,
					v = $(this).val();
			if(r.test(v)) {
				$(this).val(v.replace(r, ''));
				c--;
			}
			this.setSelectionRange(c, c);
		});

		var dataTable = $('#myTable').DataTable({

			autoWidth: false,
			searching: false,
			<?php if($this->utils->isEnabledFeature('column_visibility_report')){ ?>
                stateSave: true,
            <?php } else { ?>
                stateSave: false,
            <?php } ?>
			dom: "<'panel-body' <'pull-right'B><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
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

						var form_params=$('#search-form').serializeArray();
						// form_params['export_format']='csv';
						// form_params['export_type']=export_type;

						var d = {'extra_search': form_params, 'export_format': 'csv', 'export_type': export_type,
							'draw':1, 'length':-1, 'start':0};

						$("#_export_excel_queue_form").attr('action', site_url('/export_data/gamesHistoryV2'));
						$("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
						$("#_export_excel_queue_form").submit();

					}
				}
				<?php } ?>
			],
			columnDefs: [
				{ className: 'text-right', targets: rightTextColumns },
			],
			order: [[0, 'desc']],

			// SERVER-SIDE PROCESSING
			processing: true,
			serverSide: true,
			ajax: function (data, callback, settings) {

				data.extra_search = $('#search-form').serializeArray();
				// $("#by_game_platform_id option:selected").removeAttr("selected");
				//$("#game_type_tree").attr("value","");
				$.post(base_url + "api/gamesHistoryV2", data, function(data) {
					<?php if ($this->utils->isEnabledFeature('show_sub_total_for_game_logs_report')) {?>
                        var bet_amount = 0, player_loss = 0, player_win = 0, net_amount = 0;
                        // console.log(data.data);
                        // console.log(rightTextColumns);
						$.each(data.data, function(i, v){
							//var rtc = rightTextColumns;
							var arr_results = [v[13], v[14], v[15], v[16]];
							//var arr_results = [v[rtc[0]], v[rtc[1]], v[rtc[2]], v[rtc[3]], v[rtc[4]], v[rtc[5]]];
							for (var x = 0; x < arr_results.length; x++) {
								// Remove html tags from string
								arr_results[x] = arr_results[x].replace(/<\/?[^>]+(>|$)/g, "");
								// Remove comma from string
								arr_results[x] = arr_results[x].replace(/,/g , "");

								if (isNaN(arr_results[x])) {
									arr_results[x]  = 0;
								}
							}

							bet_amount += parseFloat(arr_results[0]);
							player_loss += parseFloat(arr_results[1]);
							player_win += parseFloat(arr_results[2]);
							net_amount += parseFloat(arr_results[3]);
						});

                        $('.sub-bet-amount').text(addCommas(parseFloat(bet_amount).toFixed(2)));
                        $('.sub-player-loss').text(addCommas(parseFloat(player_loss).toFixed(2)));
                        $('.sub-player-win').text(addCommas(parseFloat(player_win).toFixed(2)));
                        $('.sub-net-amount').text(addCommas(parseFloat(net_amount).toFixed(2)));
					<?php }?>
					$('.bet-amount').text(addCommas(parseFloat(data.summary[0].real_total_bet).toFixed(2)));
					$('.player-loss').text(addCommas(parseFloat(data.summary[0].total_debit_loss).toFixed(2)));
					$('.player-win').text(addCommas(parseFloat(data.summary[0].total_credit_win).toFixed(2)));
					$('.net-amount').text(addCommas(parseFloat(data.summary[0].net_credit_debit).toFixed(2)));

					$('.ave-bet-total').text(addCommas(parseFloat(data.summary[0].total_ave_bet).toFixed(2)));
					$('.bet-count-total').text(addCommas(parseFloat(data.summary[0].total_count_bet)));

					$('#platform-summary').html('');
					$.each(data.sub_summary, function(i,v) {
						$('#platform-summary').append(v.system_code + ' <?php echo lang("mark.bet"); ?>: ' + addCommas(parseFloat(v.total_bet).toFixed(2)) + '<br>')
                            .append(v.system_code + ' <?php echo lang("Result"); ?>: ' + addCommas(parseFloat(v.total_result).toFixed(2)) + '<br>')
                            .append(v.system_code + ' <?php echo lang("lang.bet.plus.result"); ?>: ' + addCommas(parseFloat(v.total_bet_result).toFixed(2)) + '<br>')
                            .append(v.system_code + ' <?php echo lang("Wins"); ?>: ' + addCommas(parseFloat(v.total_win).toFixed(2)) + '<br>')
                            .append(v.system_code + ' <?php echo lang("player.ui28"); ?>: ' + addCommas(parseFloat(v.total_loss).toFixed(2)) + '<br>');
					});
					callback(data);
					if ( dataTable.rows( { selected: true } ).indexes().length === 0 ) {
					    dataTable.buttons().disable();
					    $("#button-submit-to-api").attr('disabled','disabled');
					}
					else {
						dataTable.buttons().enable();
						if( $('input[name="for_data_api"]:checked').length > 0 ){
							$("#button-submit-to-api").removeAttr('disabled');
						} else {
							$("#button-submit-to-api").attr('disabled','disabled');
						}
					}
					<?php if($this->config->item('game_logs_show_row_colors')): ?>
					$("tbody td.text-right:contains('-'):not('.sorting_1')").addClass('text-success');
				    <?php else: ?>
				    $("tbody td.text-right:contains('-'):not('.sorting_1')").addClass('text-red');
				    <?php endif;?>

				},'json');
			},
		});

		dataTable.on( 'draw', function () {
            $("#myTable_wrapper").floatingScroll("init");
        });

		$('#search-form input[type="text"],#search-form input[type="number"],#search-form input[type="email"]').keypress(function (e) {
			if (e.which == 13) {
				dataTable.ajax.reload();
			}
		});
		/*$('#gameTree')
				.on('loaded.jstree', treeLoaded)
				.jstree({
					'core' : {
						'data' : {
							"url" : "<?php echo site_url('/api/get_report_game_tree'); ?>",
							"dataType" : "json" // needed only if you do not supply JSON headers
						}
					},
					"checkbox":{
						"tie_selection": false
					},
					"plugins":[
						"search","checkbox"
					]
				});

		function treeLoaded(event, data) {
			data.instance.check_node(gameTypeId);
		}*/


		$("#game_type_id").change(function(){
			var checked = $("#game_type_id").is(":checked");
			if(checked){
				$(".jstree-anchor").addClass('jstree-checked');
			}else{
				$(".jstree-anchor").removeClass('jstree-checked');
			}
			var checked = $('li ul .jstree-anchor.jstree-checked');
			var data = [];
			for(i=0; i<checked.length; i++){
				var aaa = checked[i].id;
				data.push(aaa.replace(/gp_|_anchor/gi,''));
			}
			var myJsonString = JSON.parse(JSON.stringify(data));
			$("#game_type_tree").attr('value',myJsonString);
		});



		$('.jstree').click(function(){

			var tree = $(".jstree").jstree("get_checked",true),
				game_type_ids = [],
				game_platform_ids = [];

			$.each(tree, function(){

				var tree_game_type_id = this.id;

				if( tree_game_type_id.indexOf('gp_') > -1 ){
					var platFormId = tree_game_type_id.replace("gp_", "")
					game_platform_ids.push(platFormId);
					return true;
				}

				game_type_ids.push(tree_game_type_id);

			});

			$("#game_type_tree").attr('value',game_type_ids);

			$("#by_game_platform_id option:selected").attr('value', game_platform_ids);

		});

	});

	$(document).on("click",".buttons-columnVisibility",function(){
	    $("#myTable_wrapper").floatingScroll("update");
	});

	$(document).on("click","#button-submit-to-api",function(){
		
		//myObj= {result: { batch_transaction_id : 'ea71ea40-3e50-11e8-a662-7fbcce770518', created_date : '2018-04-12T21:01:28.661', status: 'posted'}};
		//alert(myObj.result['batch_transaction_id']);
		/*$('#batch_transaction_id').html(myObj.result['batch_transaction_id']);
		$('#created_date').html(myObj.result['created_date']);
		$('#status').html(myObj.result['status']);
		$('#myModal').modal('show');
		$("#button-submit-to-api").attr('disabled','disabled');*/
		$("#button-submit-to-api").html('<center style="width: 80px;height: 18px;"><img src="' + imgloader + '" style="height: 100%;"></center>');
		$("#button-submit-to-api").attr('disabled','disabled');
		$('#trigger_data_api').val("true");
	    var extra_search = $('#search-form').serializeArray();
	    var data = {'extra_search': extra_search};
		$.post(base_url + "api/gamesHistoryV2", data, function(data) {
			if (data.length != 0) {
				//alert("batch_transaction_id": data);
				console.log(data);
				$('#batch_transaction_id').html(data[0].result['batch_transaction_id']);
				$('#created_date').html(data[0].result['created_date']);
				$('#status').html(data[0].result['status']);
				$('#modalResponse').modal('show');
			} else {
				alert('<?= lang("No data to send.") ?>');
			}
			$("#button-submit-to-api").removeAttr('disabled');
			$("#button-submit-to-api").html('<?php echo lang('Submit To API'); ?>');
		},'json');
	});

	$(document).on("click","#for_data_api",function(){
		if( $('input[name="for_data_api"]:checked').length > 0 ) {
			$('#by_debit_amount_from').val('0');
			$('#by_credit_amount_from').val('0');
		}  else {
			$('#by_debit_amount_from').val('');
			$('#by_credit_amount_from').val('');
		}
	});

    $(function(){
        var flag_inputs = $('#by_game_flag');

        new SpinCheckboxCond();

        flag_inputs.on('change', function(){
            new SpinCheckboxCond();
        });

        function SpinCheckboxCond(){
            if(flag_inputs.val() == <?=Game_logs::FLAG_TRANSACTION?>){
                $('#check-free-spin').attr('disabled', true).removeAttr('checked');
            }else{
                $('#check-free-spin').removeAttr('disabled');
            }
        }
    });

    $("#import_currency").change(function () {
        var value = $('#import_currency').val();
        if(value.length !== 0){
        	var r = confirm("Are you sure you import this data in "+value+" currency?");
        	if (r != true) {
				$('#import_currency').val("");
        	}
        }
        
    })
	
</script>

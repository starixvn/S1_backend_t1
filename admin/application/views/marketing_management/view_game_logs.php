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

	.bet, .cashout{
		color: red;
	}
	.betType {
		color: blue;
	}
	.Leauge {
		color: DodgerBlue;
	}

</style>

<form class="form-horizontal" id="search-form" method="get" role="form">
	<div class="panel panel-primary hidden">

		<div class="panel-heading">
			<h4 class="panel-title">
				<i class="fa fa-search"></i> <?=lang("lang.search")?>
				<span class="pull-right">
                <a data-toggle="collapse" href="#collapseViewGameLogs" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info'?> <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
            </span><!--
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
					<label class="control-label" for="search_game_date">
						<input type="radio" name="by_date_type" value="2" <?= $date_types['settled'] == $conditions['by_date_type'] ? 'checked' : ''?>/> <?php echo lang('game_logs_settled_date');?>
						<input type="radio" name="by_date_type" value="3" <?= $date_types['bet'] == $conditions['by_date_type'] ? 'checked' : ''?>/> <?php echo lang('game_logs_bet_date');?>
						<input type="radio" name="by_date_type" value="4" <?= $date_types['updated'] == $conditions['by_date_type'] ? 'checked' : ''?>/> <?php echo lang('game_logs_updated_date');?>
					</label>
					<input id="search_game_date" class="form-control input-sm dateInput" data-start="#by_date_from" data-end="#by_date_to" data-time="true"
					<?php if ($this->utils->getConfig('game_logs_report_date_range_restriction')): ?>
						data-restrict-max-range="<?=$this->utils->getConfig('game_logs_report_date_range_restriction')?>" data-restrict-range-label="<?=sprintf(lang("restrict_date_range_label"),$this->utils->getConfig('game_logs_report_date_range_restriction'))?>" data-restrict-max-range-second-condition="<?=$this->utils->getConfig('game_logs_report_with_username_date_range_restriction')?>" data-override-on-apply="true"
					<?php endif ?>
					 autocomplete="off" />
					<input type="hidden" id="by_date_from" name="by_date_from" value="<?php echo $conditions['by_date_from']; ?>" />
					<input type="hidden" id="by_date_to" name="by_date_to"  value="<?php echo $conditions['by_date_to']; ?>"/>
				</div>
                <div class="col-md-2">
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

				<div class="col-md-6">

					<label class="control-label" for="flag"><?=lang('player.groupLevel');?></label>
					<select class="form-control input-sm" name="by_group_level" id="by_group_level">
						<option value=""><?=lang('lang.selectall');?></option>
						<?php
						foreach ($player_levels as $pl) {
							echo "<option value='" . $pl['vipsettingcashbackruleId'] . "' ".($pl['vipsettingcashbackruleId']==$conditions['by_group_level'] ? 'selected="selected"' : '')." >" . lang($pl['groupName']) . " - " . lang($pl['vipLevelName']) . "</option>";
						}
						?>
					</select>

				</div>

				<div class="col-md-4 col-lg-4">
					<label class="control-label" for="by_username_match_mode">
						<input type="radio" name="by_username_match_mode" value="1" <?= $conditions['by_username_match_mode'] == '1' ? 'checked' : ''?>/> <?php echo lang('Similar');?>
						<input type="radio" name="by_username_match_mode" value="2" <?= $conditions['by_username_match_mode'] == '2' ? 'checked' : ''?>/> <?php echo lang('Exact');?>
					</label>
					<label class="control-label" for="by_username"><?=lang('Player Username');?></label>
					<input type="text" name="by_username" id="by_username" class="form-control input-sm disable-esp-char" value="<?php echo $conditions['by_username']; ?>" />
				</div>

				<div class="col-md-4 col-lg-2">
					<label class="control-label" for="by_affiliate"><?=lang('Affiliate Username');?> </label>
					<input type="text" name="by_affiliate" id="by_affiliate" class="form-control input-sm" value="<?php echo $conditions['by_affiliate']; ?>" />
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


				<div class="col-md-4 col-lg-2">
					<label class="control-label" for="round_no"><?=lang('Round No');?> </label>
					<input type="text" name="round_no" id="round_no" class="form-control input-sm" value="<?php echo $conditions['round_no']; ?>" />
				</div>

				<div class="col-md-4 col-lg-2">
					<label class="control-label" for="flag"><?=lang('player.ut10');?> </label>
					<select class="form-control input-sm" name="by_game_flag" id="by_game_flag">
						<option value=""><?=lang('lang.selectall');?></option>
						<option value="<?=Game_logs::FLAG_GAME?>" <?php echo ($conditions['by_game_flag']==Game_logs::FLAG_GAME ? 'selected="selected"' : '' ); ?> ><?=lang('sys.gd5');?></option>
						<option value="<?=Game_logs::FLAG_TRANSACTION?>" <?php echo $conditions['by_game_flag']==Game_logs::FLAG_TRANSACTION ? 'selected="selected"' : '' ; ?> ><?=lang('pay.transact');?></option>
					</select>
				</div>

				<div class="col-md-4 col-lg-2">
					<label class="control-label" for="flag"><?=lang('Type');?> </label>
					<select class="form-control input-sm" name="by_bet_type" id="by_bet_type">
						<option value="<?=Game_logs::IS_GAMELOGS?>" <?php echo ($conditions['by_bet_type']==Game_logs::IS_GAMELOGS ? 'selected="selected"' : '' ); ?> ><?=lang('Settled');?></option>
						<option value="<?=Game_logs::IS_GAMELOGS_UNSETTLE?>" <?php echo $conditions['by_bet_type']==Game_logs::IS_GAMELOGS_UNSETTLE ? 'selected="selected"' : '' ; ?> ><?=lang('cms.others');?></option>
					</select>
				</div>

				<div class="col-md-4 col-lg-2">
					<label class="control-label" for="by_amount_from"><?=lang('mark.resultAmount')?> &gt;=</label>
					<input id="by_amount_from" type="number" name="by_amount_from" class="form-control input-sm" value="<?php echo $conditions['by_amount_from'] ?>"/>
				</div>

				<div class="col-md-4 col-lg-2">
					<label class="control-label" for="by_amount_to"><?=lang('mark.resultAmount')?> &lt;=</label>
					<input id="by_amount_to" type="number" name="by_amount_to" class="form-control input-sm" value="<?php echo $conditions['by_amount_to'] ?>"/>
				</div>

				<div class="col-md-2">
					<label class="control-label" for="by_bet_amount_from"><?php echo lang('Bet Amount'); ?> &gt;=</label>
					<input id="by_bet_amount_from" type="number" name="by_bet_amount_from" class="form-control input-sm" value="<?php echo $conditions['by_bet_amount_from'] ?>"/>
				</div>

				<div class="col-md-2">
					<label class="control-label" for="by_bet_amount_to"><?php echo lang('Bet Amount'); ?> &lt;=</label>
					<input id="by_bet_amount_to" type="number" name="by_bet_amount_to" class="form-control input-sm" value="<?php echo $conditions['by_bet_amount_to'] ?>"/>
				</div>

				<?php if ($this->utils->isEnabledFeature('show_agent_name_on_game_logs')) : ?>
					<div class="col-md-2">
						<label class="control-label" for="agency_username"><?php echo lang('Agent Username'); ?> </label>
						<input id="agency_username" type="text" name="agency_username" class="form-control input-sm" value="<?php echo $conditions['agency_username'] ?>"/>
					</div>
				<?php endif; ?>

				<div class="col-md-3">
                    <div class="checkbox">
                        <label>
                            <input id="check-free-spin" type="checkbox" name="by_free_spin" value="true" <?=$conditions['by_free_spin'] ? 'checked="checked"' : '' ; ?>/>
                            <?=lang('Only Free Bet'); ?>
                        </label>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="by_no_affiliate" value="true" <?=$conditions['by_no_affiliate'] ? 'checked="checked"' : '' ; ?>/>
                            <?=lang('Only non-affiliate'); ?>
                        </label>
                    </div>
				</div>

				<div id="gametype_search" class="col-md-12 show" style="display: none;">
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
				</div>
			</div>
			<div class="panel-footer text-right">
				<input type="submit" class="btn btn-sm  <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" id="btn-submit" value="<?php echo lang('Search'); ?>" >
			</div>
		</div>
	</div>

</form>

<div class="panel panel-primary">
	<div class="panel-heading">
		<h4 class="panel-title"><i class="fa fa-list"></i> <?=lang('player.ui48');?> </h4>
	</div>

	<div class="panel-body" >
		<small class="text-muted"><?php echo lang('Note') . ': ' . lang('Green: transfer, Red: free bet'); ?></small>
		<div class="table-responsive">
			<table id="myTable" class="table table-bordered">
				<thead>
				<tr>
					<?php include __DIR__.'/../includes/cols_for_game_logs.php'; ?>
				</tr>
				</thead>
				<tbody>
				</tbody>
				<tfoot>
				<?php if ($this->utils->isEnabledFeature('show_sub_total_for_game_logs_report')) {?>
				<?php include __DIR__.'/../includes/footer_sub_for_game_logs.php'; ?>
				<?php }?>
				<?php include __DIR__.'/../includes/footer_for_game_logs.php'; ?>
				</tfoot>
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

    function checkDateRangeRestrictionWithUsername(){
    	var dateInput = $('#search_game_date.dateInput');
		var isRange = (dateInput.data('start') && dateInput.data('end'));

        var $restricted_range = dateInput.data('restrict-max-range');
        var $second_restricted_range = dateInput.data('restrict-max-range-second-condition');

    	if($.trim($second_restricted_range) != '' && $.isNumeric($second_restricted_range) && isRange){
    		if($.trim($('#by_username').val()) != ''){
    			dateInput.data('restrict-max-range',$second_restricted_range);
    			dateInput.data('restrict-range-label','<?=sprintf(lang("restrict_date_range_label"),$this->utils->getConfig('game_logs_report_with_username_date_range_restriction'))?>');
    			$restricted_range = $second_restricted_range;
        	}
        	else{
        		dateInput.data('restrict-max-range','<?=$this->utils->getConfig('game_logs_report_date_range_restriction')?>');
    			dateInput.data('restrict-range-label','<?=sprintf(lang("restrict_date_range_label"),$this->utils->getConfig('game_logs_report_date_range_restriction'))?>');
        		$restricted_range = dateInput.data('restrict-max-range');
        	}
    	}

    	return $restricted_range;
    }

	$(document).ready(function(){

		<?php if ($this->utils->getConfig('game_logs_report_date_range_restriction')): ?>

		var dateInput = $('#search_game_date.dateInput');
		var isRange = (dateInput.data('start') && dateInput.data('end'));
        var isTime = dateInput.data('time');

        dateInput.keypress(function(e){
            e.preventDefault();
            return false;
        });

        $('.daterangepicker .cancelBtn ').text('Reset');//.css('display','none');

        // -- Use reset to current day upon cancel/reset in daterange instead of emptying the value
        dateInput.on('cancel.daterangepicker', function(ev, picker) {
            // -- if start date was empty, add a default one
            if($.trim($(dateInput.data('start')).val()) == ''){
                var startEl = $(dateInput.data('start'));
                    start = startEl.val();
                    start = start ? moment(start, 'YYYY-MM-DD HH:mm:ss') : moment().startOf('day');
                    startEl.val(isTime ? start.format('YYYY-MM-DD HH:mm:ss') : start.startOf('day').format('YYYY-MM-DD HH:mm:ss'));

                dateInput.data('daterangepicker').setStartDate(start);
            }

            // -- if end date was empty, add a default one
            if($.trim($(dateInput.data('end')).val()) == ''){
                var endEl = $(dateInput.data('end'));
                    end = endEl.val();
                    end = end ? moment(end, 'YYYY-MM-DD HH:mm:ss') : moment().endOf('day');
                    endEl.val(isTime ? end.format('YYYY-MM-DD HH:mm:ss') : end.endOf('day').format('YYYY-MM-DD HH:mm:ss'));

                dateInput.data('daterangepicker').setEndDate(end);
            }

            dateInput.val($(dateInput.data('start')).val() + ' to ' + $(dateInput.data('end')).val());
        });

        dateInput.on('apply.daterangepicker, hide.daterangepicker', function(ev, picker) {

        	var $restricted_range = checkDateRangeRestrictionWithUsername();

        	if ($restricted_range == '' && !$.isNumeric($restricted_range) && !isRange)
                return false;

            var a_day = 86400000; // -- one day
            var restriction = a_day * $restricted_range;
            var start_date = new Date(picker.startDate._d);
            var end_date = new Date(picker.endDate._d);

            // -- if start date was empty, add a default one
            if($.trim($(dateInput.data('start')).val()) == ''){
                var startEl = $(dateInput.data('start'));
                    start = startEl.val();
                    start = start ? moment(start, 'YYYY-MM-DD HH:mm:ss') : moment().startOf('day');
                    startEl.val(isTime ? start.format('YYYY-MM-DD HH:mm:ss') : start.startOf('day').format('YYYY-MM-DD HH:mm:ss'));

                dateInput.data('daterangepicker').setStartDate(start);
            }

            // -- if end date was empty, add a default one
            if($.trim($(dateInput.data('end')).val()) == ''){
                var endEl = $(dateInput.data('end'));
                    end = endEl.val();
                    end = end ? moment(end, 'YYYY-MM-DD HH:mm:ss') : moment().endOf('day');
                    endEl.val(isTime ? end.format('YYYY-MM-DD HH:mm:ss') : end.endOf('day').format('YYYY-MM-DD HH:mm:ss'));

                dateInput.data('daterangepicker').setEndDate(end);
            }

            dateInput.val($(dateInput.data('start')).val() + ' to ' + $(dateInput.data('end')).val());


            if((end_date - start_date) >= restriction){ // -- get timestamp result

                if(dateInput.data('restrict-range-label') && $.trim(dateInput.data('restrict-range-label')) !== "")
                    alert(dateInput.data('restrict-range-label'));
                else{
                    var day_label = 'day';

                    if($restricted_range > 1) day_label = 'days'

                    alert('Please choose a date range not greater than '+ $restricted_range +' '+ day_label);
                }

                //  -- reset value
                //  -- if validation fails, do not change anything, retain the last correct values
                $(dateInput.data('start')).val('');
                $(dateInput.data('end')).val('');

                var startEl = $(dateInput.data('start'));
                    start = picker.oldStartDate;//startEl.val();
                    start = start ? moment(start, 'YYYY-MM-DD HH:mm:ss') : moment().startOf('day');

                var endEl = $(dateInput.data('end'));
                    end = picker.oldEndDate;//endEl.val();
                    end = end ? moment(end, 'YYYY-MM-DD HH:mm:ss') : moment().endOf('day');

                dateInput.data('daterangepicker').setStartDate(start);
                dateInput.data('daterangepicker').setEndDate(end);

                startEl.val(isTime ? start.format('YYYY-MM-DD HH:mm:ss') : start.startOf('day').format('YYYY-MM-DD HH:mm:ss'));
                endEl.val(isTime ? end.format('YYYY-MM-DD HH:mm:ss') : end.endOf('day').format('YYYY-MM-DD HH:mm:ss'));

                dateInput.val(startEl.val() + ' to ' + endEl.val());
            }

        });

		$('#btn-submit').click(function(e){
			e.preventDefault();

			var dateInput = $('#search_game_date.dateInput');

            var $restricted_range = checkDateRangeRestrictionWithUsername();

            if ($restricted_range == '' && !$.isNumeric($restricted_range) && !isRange)
                return false;

            var start_date = new Date($('#by_date_from').val());
            var end_date = new Date($('#by_date_to').val());
            var a_day = 86400000;
            var restriction = a_day * $restricted_range;

            if($.trim(dateInput.val()) == '' || ((end_date - start_date) >= restriction)){

                if(dateInput.data('restrict-range-label') && $.trim(dateInput.data('restrict-range-label')) !== "")
                    alert(dateInput.data('restrict-range-label'));
                else{
                    var day_label = 'day';

                    if($restricted_range > 1) day_label = 'days'

                    alert('Please choose a date range not greater than '+ $restricted_range +' '+ day_label);
                }

            }
            else{
            	$('#search-form').submit();
			}
		});

		<?php endif ?>

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
        var game_provider_id_col = elem.filter(function(index){
            if ($(this).attr("id") == "game_provider_id_col") {
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
			dom: "<'panel-body' <'pull-right'B><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i><'dataTable-instance't><'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
			buttons: [
				{
					extend: 'colvis',
					postfixButtons: [ 'colvisRestore' ],
					className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>'
				}
				<?php

                   if( $export_report_permission ){

               ?>
				,{

					text: "<?php echo lang('CSV Export'); ?>",
                    className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>',
					action: function ( e, dt, node, config ) {
						var form_params=$('#search-form').serializeArray();
						var d = {'extra_search': form_params, 'export_format': 'csv', 'export_type': export_type,
							'draw':1, 'length':-1, 'start':0};

						$("#_export_excel_queue_form").attr('action', site_url('/export_data/gamesHistory'));
						$("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
						$("#_export_excel_queue_form").submit();

					}
				}
				<?php
                    }
                ?>
			],
			columnDefs: [
				{ className: 'text-right', targets: rightTextColumns },
				{ visible: false, targets: hiddenColumns }

			],
			order: [[1,'desc'], [0,'desc'] ],

			// SERVER-SIDE PROCESSING
			processing: true,
			serverSide: true,
			ajax: function (data, callback, settings) {

				data.extra_search = $('#search-form').serializeArray();
				// $("#by_game_platform_id option:selected").removeAttr("selected");
				//$("#game_type_tree").attr("value","");
				$.post(base_url + "api/gamesHistory", data, function(data) {
					<?php if ($this->utils->isEnabledFeature('show_sub_total_for_game_logs_report')) {?>
                        var real_total_bet = 0, total_bet = 0, total_result = 0, total_bet_result = 0, total_win = 0, total_loss = 0;
                        // console.log(data.data);
                        // console.log(rightTextColumns);
						$.each(data.data, function(i, v){
							var rtc = rightTextColumns;
							// var arr_results = [v[7], v[8], v[9], v[10], v[11], v[12]];
							var arr_results = [v[rtc[0]], v[rtc[1]], v[rtc[2]], v[rtc[3]], v[rtc[4]], v[rtc[5]]];
							for (var x = 0; x < arr_results.length; x++) {
								// Remove html tags from string
								arr_results[x] = arr_results[x].replace(/<\/?[^>]+(>|$)/g, "");
								// Remove comma from string
								arr_results[x] = arr_results[x].replace(/,/g , "");

								if (isNaN(arr_results[x])) {
									arr_results[x]  = 0;
								}
							}

							real_total_bet += parseFloat(arr_results[0]);
							total_bet += parseFloat(arr_results[1]);
							total_result += parseFloat(arr_results[2]);
							total_bet_result += parseFloat(arr_results[3]);
							total_win += parseFloat(arr_results[4]);
							total_loss += parseFloat(arr_results[5]);
						});

                        $('.sub-real-bet-total').text(addCommas(parseFloat(real_total_bet).toFixed(2)));
                        $('.sub-bet-total').text(addCommas(parseFloat(total_bet).toFixed(2)));
                        $('.sub-result-total').text(addCommas(parseFloat(total_result).toFixed(2)));
                        $('.sub-bet-result-total').text(addCommas(parseFloat(total_bet_result).toFixed(2)));
                        $('.sub-win-total').text(addCommas(parseFloat(total_win).toFixed(2)));
                        $('.sub-loss-total').text(addCommas(parseFloat(total_loss).toFixed(2)));
					<?php }?>
					$('.real-bet-total').text(addCommas(parseFloat(data.summary[0].real_total_bet).toFixed(2)));
					$('.bet-total').text(addCommas(parseFloat(data.summary[0].total_bet).toFixed(2)));
					$('.result-total').text(addCommas(parseFloat(data.summary[0].total_result).toFixed(2)));
					$('.bet-result-total').text(addCommas(parseFloat(data.summary[0].total_bet_result).toFixed(2)));
					$('.win-total').text(addCommas(parseFloat(data.summary[0].total_win).toFixed(2)));
					$('.loss-total').text(addCommas(parseFloat(data.summary[0].total_loss).toFixed(2)));
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
					}
					else {
						dataTable.buttons().enable();
					}
					var info = dataTable.page.info();
					if (info.page != 0 && info.page > (info.pages-1) ) {
						dataTable.page('first').draw();
						dataTable.ajax.reload();
					}
					<?php if($this->config->item('game_logs_show_row_colors')): ?>
					$("tbody td.text-right:contains('-'):not('.sorting_1')").addClass('text-success');
				    <?php else: ?>
				    $("tbody td.text-right:contains('-'):not('.sorting_1')").addClass('text-red');
				    <?php endif;?>

				},'json');

			},

			fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
				// utils.safelog('aData[5]'+aData[5]+"aData[14]"+aData[14]);
				if (aData[flagColIndex] == "<?=Game_logs::FLAG_TRANSACTION?>") {
					$(nRow).css('background-color', '#<?=$this->config->item('color')['trans_in_game_log']?>');
				}

				<?php if($this->config->item('game_logs_show_row_colors')): ?>
				var rlt_amount=aData[resAmtCol];
				rlt_amount=rlt_amount.replace(/<[^>]+>/, '');
				rlt_amount=parseFloat(rlt_amount);

				/* *********************************************************
				 * 	Free spin if condition met
				 * *********************************************************
				 * 		real bet = 0 or n/A
				 * 		available bet = 0 or n/A
				 * 		result amount != 0
				 * 		flag = 1
				 **********************************************************/

				var game_with_no_free_spin	= <?= json_encode($this->config->item('game_with_no_free_spin'))?>;
				var chk_game_with_no_free_spin = jQuery.inArray(parseInt(aData[game_provider_id_col]), game_with_no_free_spin);
				if (aData[flagColIndex] == "<?=Game_logs::FLAG_GAME?>" && parseFloat(aData[avlBetCol]) == 0.00 && (parseFloat(aData[realBetCol]) == 0.00 || isNaN(aData[realBetCol])) && parseFloat(aData[resAmtCol]) != 0.00 && chk_game_with_no_free_spin == "-1") {
					if (aData[29] != 'N/A' && "<?= $this->utils->isEnabledFeature('hide_free_spin_on_game_history') ?>"){
                } else {
                    $(nRow).css('background-color', '#<?=$this->config->item('color')['free_game']?>');
                }
				}
				<?php endif;?>

			}


		});

		dataTable.on( 'draw', function () {
            $("#myTable_wrapper .dataTable-instance").floatingScroll("init");
        });

		$('#search-form input[type="text"],#search-form input[type="number"],#search-form input[type="email"]').keypress(function (e) {
			if (e.which == 13) {
				dataTable.ajax.reload();
			}
		});
		$('#gameTree')
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

		// if(gameTypeId) {
		// 	$('#gametype').trigger('click');
		// 	gameTypeId = gameTypeId.split(',');
		// } else {
		// 	$('#game_type_id').prop('checked', false);
		// }

		function treeLoaded(event, data) {
			data.instance.check_node(gameTypeId);
		}


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
			// $.each($game_platform_ids, function(index, value){
			// 	$("#by_game_platform_id option:selected").attr('value', game_platform_ids);
			// });
			$("#by_game_platform_id option:selected").attr('value', game_platform_ids);

		});

	});

	$(document).on("click",".buttons-columnVisibility",function(){
	    $("#myTable_wrapper .dataTable-instance").floatingScroll("update");
	});

	$(document).on("click",".showCardList",function(){
		var cardList = $(this).data("card").split(" ");
		var title = "<?php echo lang('Card List');?>"
		$("#search-form").append('<div id="cardListModal" class="modal fade">\
									  <div class="modal-dialog">\
									    <div class="modal-content">\
									      <div class="modal-header">\
									        <button type="button" class="close" data-dismiss="modal">&times;</button>\
									        <h4 class="modal-title">'+title+'</h4>\
									      </div>\
									      <div class="modal-body">\
									      </div>\
									    </div>\
									  </div>\
									</div>');
		$.each(cardList, function( index, value ) {
		  $("#cardListModal").find(".modal-body").append('<i class="icon-img card-'+value+'"></i>')
		});
		$('#cardListModal').modal('show');
	});
	$(document).on("hidden.bs.modal","#cardListModal",function(){
		$("#cardListModal").remove();
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
</script>

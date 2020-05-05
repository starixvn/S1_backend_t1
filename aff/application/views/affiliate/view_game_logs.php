<?php
/**
 *   filename:   view_game_logs.php
 *   author:     Kaiser Dapar
 *   e-mail:     kaiserdapar@gmail.com
 *   date:       2016-11-08
 *   @brief:     view game logs
 */
?>

<div class="container">
    <form class="form-horizontal" id="search-form">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <i class="fa fa-search"></i> <?=lang("lang.search")?>
                    <span class="pull-right">
                        <a data-toggle="collapse" href="#collapseViewGameLogs"
                            class="btn btn-info btn-xs <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>">
                        </a>
                    </span>
                </h4>
            </div>

            <div id="collapseViewGameLogs" class="panel-collapse <?=$this->config->item('default_open_search_panel') ? '' : 'collapse in'?>">
                <div class="panel-body">
                    <input type="hidden" id="game_description_id" name="game_description_id"
                    value="<?php echo $conditions['game_description_id']; ?>" />
                    <div class="col-md-6">
                        <label class="control-label" for="search_game_date"><?=lang('report.sum02');?></label>
                        <input id="search_game_date" class="form-control input-sm dateInput"
                        data-start="#by_date_from" data-end="#by_date_to" data-time="true"
                        <?php if ($this->utils->getConfig('affiliate_game_logs_report_date_range_restriction')): ?>
                            data-restrict-max-range="<?=$this->utils->getConfig('affiliate_game_logs_report_date_range_restriction')?>" data-restrict-range-label="<?=sprintf(lang("restrict_date_range_label"),$this->utils->getConfig('affiliate_game_logs_report_date_range_restriction'))?>"
                        <?php endif ?>
                        autocomplete="off" />
                        <input type="hidden" id="by_date_from" name="by_date_from" value="<?php echo $conditions['by_date_from']; ?>" />
                        <input type="hidden" id="by_date_to" name="by_date_to"  value="<?php echo $conditions['by_date_to']; ?>"/>
                    </div>
                    <div class="col-md-6 hidden">
                        <label class="control-label" for="flag"><?=lang('player.groupLevel');?></label>
                        <select class="form-control input-sm" name="by_group_level" id="by_group_level">
                            <option value=""><?=lang('lang.selectall');?></option>
                            <?php foreach ($player_levels as $pl) { ?>
                            <?php $sel = $pl['vipsettingcashbackruleId']==$conditions['by_group_level'] ? 'selected="selected"' : ''; ?>
                            <option value="<?=$pl['vipsettingcashbackruleId']?>" "<?=$sel?>">
                            <?=$pl['groupName'] . ' - ' . $pl['vipLevelName']?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>
                    <?php if ( ! $this->utils->isEnabledFeature('hide_sub_affiliates_on_affiliate')): ?>
                        <div class="col-md-6">
                            <label class="control-label" for="affiliate_username"><?=lang('Affiliate Username');?> </label>
                            <div class="input-group">
                                <input type="text" name="affiliate_username" id="affiliate_username" class="form-control input-sm" value="">
                                <span class="input-group-addon input-sm">
                                    <input type="checkbox" name="include_all_downlines" value="true" checked="">
                                    <?=lang('Include All Downline Affiliates');?>                                </span>
                            </div>
                        </div>
                    <?php endif ?>

                    <div class="col-md-2">
                        <label class="control-label" for="by_username"><?=lang('Player Username');?> </label>
                        <input type="text" name="by_username" id="by_username" class="form-control input-sm"
                        value="<?php echo $conditions['by_username']; ?>" />
                    </div>

                    <div class="col-md-2">
                        <label class="control-label" for="by_realname"><?php echo lang('Player Real Name'); ?></label>
                        <input id="by_realname" type="text" name="by_realname" class="form-control input-sm"
                        value="<?php echo $conditions['by_realname'] ?>"/>
                    </div>

                    <div class="col-md-2">
                        <label class="control-label" for="by_game_platform_id"><?=lang('player.ui29');?> </label>
                        <select class="form-control input-sm" name="by_game_platform_id" id="by_game_platform_id">
                            <option value=""><?=lang('lang.selectall');?></option>
                            <?php foreach ($game_platforms as $game_platform) {?>
                            <option value="<?=$game_platform['id']?>"
                            <?php echo $conditions['by_game_platform_id']==$game_platform['id'] ? 'selected="selected"' : '' ; ?>>
                            <?=$game_platform['system_code'];?>
                            </option>
                            <?php }?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="control-label" for="flag"><?=lang('player.ut10');?> </label>
                        <select class="form-control input-sm" name="by_game_flag" id="by_game_flag">
                            <option value=""><?=lang('lang.selectall');?></option>
                            <option value="<?=Game_logs::FLAG_GAME?>"
                            <?php echo (empty($conditions['by_game_flag']) || $conditions['by_game_flag']==Game_logs::FLAG_GAME) ? 'selected="selected"' : '' ; ?> >
                            <?=lang('sys.gd5');?>
                            </option>
                            <option value="<?=Game_logs::FLAG_TRANSACTION?>"
                            <?php echo $conditions['by_game_flag']==Game_logs::FLAG_TRANSACTION ? 'selected="selected"' : '' ; ?> >
                            <?=lang('pay.transact');?>
                            </option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="control-label" for="by_amount_from"><?=lang('mark.resultAmount')?> &gt;=</label>
                        <input id="by_amount_from" type="number" name="by_amount_from" class="form-control input-sm"
                        value="<?php echo $conditions['by_amount_from'] ?>"/>
                    </div>

                    <div class="col-md-2">
                        <label class="control-label" for="by_amount_to"><?=lang('mark.resultAmount')?> &lt;=</label>
                        <input id="by_amount_to" type="number" name="by_amount_to" class="form-control input-sm"
                        value="<?php echo $conditions['by_amount_to'] ?>"/>
                    </div>

                    <div class="col-md-2">
                        <label class="control-label" for="by_bet_amount_from"><?php echo lang('Bet Amount'); ?> &gt;=</label>
                        <input id="by_bet_amount_from" type="number" name="by_bet_amount_from" class="form-control input-sm"
                        value="<?php echo $conditions['by_bet_amount_from'] ?>"/>
                    </div>

                    <div class="col-md-2">
                        <label class="control-label" for="by_bet_amount_to"><?php echo lang('Bet Amount'); ?> &lt;=</label>
                        <input id="by_bet_amount_to" type="number" name="by_bet_amount_to" class="form-control input-sm"
                        value="<?php echo $conditions['by_bet_amount_to'] ?>"/>
                    </div>

                    <div class="col-md-2">
                        <label class="control-label" for="by_round_number"><?php echo lang('Round Number'); ?></label>
                        <input id="by_round_number" type="text" name="by_round_number" class="form-control input-sm"
                        value="<?php echo $conditions['by_round_number'] ?>"/>
                    </div>

                    <div id="gametype_search" class="col-md-12" style="display: none;">
                        <fieldset style="padding-bottom: 8px">
                            <legend>
                                <label class="control-label"><?=lang('sys.gd6');?></label>
                            </legend>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="checkbox">
                                        <label>
                                            <input name="all_game_types" id="game_type_id" type="checkbox"
                                            onclick="checkAll(this.id)"
                                            <?php echo $conditions['game_type_id'] ? '' : 'checked="checked"'; ?>
                                            value="true">
                                            <?=lang('lang.selectall');?>
                                        </label>
                                    </div>
                                </div>
                                <?php foreach (array_chunk($game_types, ceil(count($game_types) / 3)) as $game_types_chunk) {?>
                                <div class="col-md-3">
                                    <?php foreach ($game_types_chunk as $game_type) {?>
                                    <div class="checkbox">
                                        <label>
                                            <input name="game_type_id" value="<?php echo $game_type['id']; ?>"
                                            class="game_type_id" type="checkbox"
                                            <?php echo $conditions['game_type_id'] == $game_type['id'] ? 'checked="checked"' : ''; ?>
                                            data-untoggle="checkbox" data-target="#game_type_id"/>
                                            <?php echo $game_type['game_platform_name'].' - '.lang($game_type['game_type_lang']); ?>
                                        </label>
                                    </div>
                                    <?php }?>
                                </div>
                                <?php }?>
                            </div>
                        </fieldset>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <input type="submit" class="btn btn-primary btn-sm" id="btn-submit" value="<?php echo lang('Search'); ?>" >
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
                            <th class="test"><?=lang('player.ug01');?></th>
                            <th id="col-player-username" class="test hidden-col"><?=lang('Player Username');?></th>
                            <?php if ($this->utils->isEnabledFeature('aff_show_real_name_on_reports')): ?>
                                <th class="test"><?=lang('Real Name');?></th>
                            <?php endif ?>
                            <th class="test"><?=lang('Affiliate Username');?></th>
                            <th class="test"><?=lang('Player Level')?></th>
                            <th><?=lang('cms.gameprovider');?></th>
                            <th><?=lang('cms.gametype');?></th>
                            <th><?=lang('cms.gamename');?></th>
                            <th id="col-real-bet" class="right-text-col"><?=lang('Real Bet');?></th>
                            <th id="col-available-bet" class="right-text-col"><?=lang('Available Bet');?></th>
                            <th id="col-result-amt" class="right-text-col"><?=lang('mark.resultAmount');?></th>
                            <th class="right-text-col"><?=lang('lang.bet.plus.result');?></th>
                            <th class="right-text-col"><?php echo lang('Win Amount'); ?></th>
                            <th class="right-text-col"><?php echo lang('Loss Amount'); ?></th>
                            <th class="right-text-col"><?=lang('mark.afterBalance');?></th>
                            <th class="right-text-col"><?=lang('pay.transamount');?></th>
                            <th><?php echo lang('Round No'); ?></th>
                            <th><?=lang('player.ut12');?></th>
                            <th><?=lang('Bet Detail');?></th>
                            <th id="flag_col" class="hidden-col"><?=lang('player.ut10');?></th>
                            <th><?=lang('Bet Type');?></th>
                            <th><?=lang('Match Type');?></th>
                            <th><?=lang('Match Details');?></th>
                            <th><?=lang('Handicap');?></th>
                            <th><?=lang('Odds');?></th>
                        </tr>
                    </thead>
                    <tfoot>

                        <?php
                            $total_colspan_value = 24;
                            $colspan_value = 7;
                            if ($this->utils->isEnabledFeature('aff_show_real_name_on_reports')) {
                                $colspan_value++;
                                $total_colspan_value++;
                            }
                        ?>
						<?php if ($this->utils->isEnabledFeature('show_sub_total_for_game_logs_report')) {?>
                            <tr>
                                <th colspan="<?=$colspan_value?>" style="text-align: left;"><?=lang('Sub Total')?></th>
                                <th style="text-align: right"><span class="sub-real-bet-total">0.00</span></th>
                                <th style="text-align: right"><span class="sub-bet-total">0.00</span></th>
                                <?php if (!$this->utils->is_readonly()): ?>
                                    <th style="text-align: right"><span class="sub-result-total">0.00</span></th>
                                    <th style="text-align: right"><span class="sub-bet-result-total">0.00</span></th>
                                    <th style="text-align: right"><span class="sub-win-total">0.00</span></th>
                                    <th style="text-align: right"><span class="sub-loss-total">0.00</span></th>
                                    <th colspan="11"></th>
                                <?php else: ?>
                                    <th colspan="15"></th>
                                <?php endif ?>
                            </tr>
						<?php }?>
                        <tr>
                            <th colspan="<?=$colspan_value?>" style="text-align: left;"><?=lang('Total')?></th>
                            <th style="text-align: right"><span class="real-bet-total">0.00</span></th>
                            <th style="text-align: right"><span class="bet-total">0.00</span></th>
                            <?php if (!$this->utils->is_readonly()): ?>
                                <th style="text-align: right"><span class="result-total">0.00</span></th>
                                <th style="text-align: right"><span class="bet-result-total">0.00</span></th>
                                <th style="text-align: right"><span class="win-total">0.00</span></th>
                                <th style="text-align: right"><span class="loss-total">0.00</span></th>
                                <th colspan="11"></th>
                            <?php else: ?>
                                <th colspan="15"></th>
                            <?php endif ?>
                        </tr>
                        <tr>
                            <th colspan="<?=$total_colspan_value?>" style="text-align: right;">
                                <?=lang('cms.totalBetAmount');?>: <span class="bet-total">0.00</span><br>
                                <?php if (!$this->utils->is_readonly()): ?>
                                    <?=lang('cms.totalResultAmount');?>: <span class="result-total">0.00</span><br>
                                    <?=lang('Total Bet + Result Amount');?>: <span class="bet-result-total">0.00</span><br>
                                    <?=lang('Total Win');?>: <span class="win-total">0.00</span><br>
                                    <?=lang('Total Loss');?>: <span class="loss-total">0.00</span><br>
                                <?php endif ?>
                                <?=lang('Average Bet');?>: <span class="ave-bet-total">0.00</span><br>
                                <?=lang('Total Number of Bets');?>: <span class="bet-count-total">0</span>
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="panel-footer"></div>
    </div>
</div>

<div class="modal fade in" id="common_modal"
                tabindex="-1" role="dialog" aria-labelledby="label_common_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="label_common_modal"></h4>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function(){
    var dataTable = $('#myTable').DataTable({
        lengthMenu: [50, 100, 250, 500, 1000],
        autoWidth: false,
        <?php if($this->utils->isEnabledFeature('column_visibility_report')){ ?>
            stateSave: true,
        <?php } else { ?>
            stateSave: false,
        <?php } ?>
		searching: false,
		// dom: "<'panel-body'<'pull-right'B>l>t<'text-center'r><'panel-body'<'pull-right'p>i>",
		dom: "<'panel-body' <'pull-right'B><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
		buttons: [
			{
				extend: 'colvis',
				postfixButtons: [ 'colvisRestore' ]
			}
		],
		columnDefs: [
            <?php if ($this->utils->isEnabledFeature('aff_show_real_name_on_reports')): ?>
                { className: 'text-right', targets: [ 7,8,9,10,11,12,13,14 ] },
                { visible: false, targets: [ <?php echo implode(',', array_map(function($i) { return intval($i) + 1;}, $this->config->item('game_logs_hidden_cols_for_aff'))); ?>  ] }
            <?php else: ?>
                { className: 'text-right', targets: [ 6,7,8,9,10,11,12,13 ] },
                { visible: false, targets: [ <?php echo implode(',', $this->config->item('game_logs_hidden_cols_for_aff')); ?>  ] }
            <?php endif ?>
		],
		order: [[0, 'desc']],

		// SERVER-SIDE PROCESSING
		processing: true,
		serverSide: true,
		ajax: function (data, callback, settings) {

			data.extra_search = $('#search-form').serializeArray();

			$.post(base_url + "api/affiliate_game_history", data, function(data) {

				<?php if ($this->utils->isEnabledFeature('show_sub_total_for_game_logs_report')) {?>

					var real_total_bet = 0, total_bet = 0, total_result = 0, total_bet_result = 0, total_win = 0, total_loss = 0;

                    <?php if ($this->utils->isEnabledFeature('aff_show_real_name_on_reports')): ?>
                        $.each(data.data, function(i, v){
                            real_total_bet += parseFloat(v[8]);
                            total_bet += parseFloat(v[9]);
                            total_result += parseFloat(v[10]);
                            total_bet_result += parseFloat(v[11]);
                            total_win += parseFloat(v[12]);
                            total_loss += parseFloat(v[13]);
                        });
                    <?php else: ?>
                        $.each(data.data, function(i, v){
                            console.log(v[9]);
                            real_total_bet += parseFloat(v[7]);
                            total_bet += parseFloat(v[8]);
                            total_result += parseFloat(v[9]);
                            total_bet_result += parseFloat(v[10]);
                            total_win += parseFloat(v[11]);
                            total_loss += parseFloat(v[12]);
                        });
                    <?php endif ?>

					$('.sub-real-bet-total').text(parseFloat(real_total_bet).toFixed(2));
					$('.sub-bet-total').text(parseFloat(total_bet).toFixed(2));
					$('.sub-result-total').text(parseFloat(total_result).toFixed(2));
					$('.sub-bet-result-total').text(parseFloat(total_bet_result).toFixed(2));
					$('.sub-win-total').text(parseFloat(total_win).toFixed(2));
					$('.sub-loss-total').text(parseFloat(total_loss).toFixed(2));

				<?php }?>

				$('.real-bet-total').text(parseFloat(data.summary[0].real_total_bet).toFixed(2));
				$('.bet-total').text(parseFloat(data.summary[0].total_bet).toFixed(2));
				$('.result-total').text(parseFloat(data.summary[0].total_result).toFixed(2));
				$('.bet-result-total').text(parseFloat(data.summary[0].total_bet_result).toFixed(2));
				$('.win-total').text(parseFloat(data.summary[0].total_win).toFixed(2));
				$('.loss-total').text(parseFloat(data.summary[0].total_loss).toFixed(2));
				$('.ave-bet-total').text(parseFloat(data.summary[0].total_ave_bet).toFixed(2));
				$('.bet-count-total').text(parseFloat(data.summary[0].total_count_bet));

				callback(data);
				if ( dataTable.rows( { selected: true } ).indexes().length === 0 ) {
					dataTable.buttons().disable();
				}
				else {
					dataTable.buttons().enable();
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

            <?php if ($this->utils->isEnabledFeature('aff_show_real_name_on_reports')): ?>
                var flag = aData[19];
                var bet = aData[8];
                var rlt_amount = aData[10];
            <?php else: ?>
                var flag = aData[18];
                var bet = aData[7];
    			var rlt_amount = aData[9];
            <?php endif;?>

            if (flag == "<?=Game_logs::FLAG_TRANSACTION?>") {
                $(nRow).css('background-color', '#<?=$this->config->item('color')['trans_in_game_log']?>');
            }

            <?php if($this->config->item('game_logs_show_row_colors')): ?>
			rlt_amount=rlt_amount.replace(/<[^>]+>/, '');
			rlt_amount=parseFloat(rlt_amount);
			// utils.safelog(rlt_amount);

			if (flag == "<?=Game_logs::FLAG_GAME?>" && parseFloat(bet) == 0.00 && rlt_amount != 0.00) {
				$(nRow).css('background-color', '#<?=$this->config->item('color')['free_game']?>');
			}
			<?php endif;?>


		},


    });

    // $('#btn-submit').click(function(e) {
    //     e.preventDefault();
    //     dataTable.ajax.reload();
    // });

    $('#search-form input[type="text"],#search-form input[type="number"],#search-form input[type="email"]').keypress(function (e) {
        if (e.which == 13) {
            dataTable.ajax.reload();
        }
    });

    <?php if ($this->utils->getConfig('affiliate_game_logs_report_date_range_restriction')): ?>

        var dateInput = $('#search_game_date.dateInput');
        var isTime = dateInput.data('time');

        dateInput.keypress(function(e){
            e.preventDefault();
            return false;
        });

        $('.daterangepicker .cancelBtn ').text('<?=lang("lang.reset")?>');//.css('display','none');

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

        // -- Validate the date again upon form submit
        $('#btn-submit').click(function(e){
            e.preventDefault();

            var dateInput = $('#search_game_date.dateInput');

            var $restricted_range = dateInput.data('restrict-max-range');

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
                dataTable.ajax.reload();
            }
        });

    <?php else: ?>
        $('#btn-submit').click(function(e) {
            e.preventDefault();
            dataTable.ajax.reload();
        });
    <?php endif ?>

});
</script>
<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of view_game_logs.php

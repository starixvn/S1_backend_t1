<?php
/**
 *   filename:   view_game_logs.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2016-07-18
 *   @brief:     view game logs in details
 */
?>

<div class="content-container">
    <!--<button type="button" onclick="betResult(0)">Test</button>-->
    <form class="form-horizontal" id="search-form">
        <input type="hidden" name="agent_id" value="<?php echo $agent_id?>"/>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <i class="fa fa-search"></i> <?=lang("lang.search")?>
                    <span class="pull-right">
                        <a data-toggle="collapse" href="#collapseViewGameLogs"
                            class="btn btn-info btn-xs <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>">
                        </a>
                    </span>
                    <!--
                    <span class="pull-right m-r-10">
                        <label class="checkbox-inline">
                            <input type="checkbox" id="gametype" value="gametype" onclick="checkSearchGameLogs(this.value);"/>
                            <?php echo lang('Game Type');?>
                        </label>
                    </span> -->
                    <?php //include __DIR__ . "/../includes/report_tools.php" ?>
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
                        <?php if ($this->utils->getConfig('agency_game_logs_report_date_range_restriction')): ?>
                            data-restrict-max-range="<?=$this->utils->getConfig('agency_game_logs_report_date_range_restriction')?>" data-restrict-range-label="<?=sprintf(lang("restrict_date_range_label"),$this->utils->getConfig('agency_game_logs_report_date_range_restriction'))?>"
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

                    <div class="col-md-2">
                        <label class="control-label" for="by_username"><?=lang('system.word38');?> </label>
                        <input type="text" name="by_username" id="by_username" class="form-control input-sm"
                        value="<?php echo $conditions['by_username']; ?>" />
                    </div>

                    <div class="col-md-2">
                        <label class="control-label" for="game_code"><?=lang('sys.gd9');?> </label>
                        <input type="text" name="game_code" id="game_code" class="form-control input-sm"
                        value="<?php echo $conditions['by_game_code']; ?>" />
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
                            <th class="test"><?=lang('Player Username');?></th>
                            <th class="test"><?=lang('Affiliate Username');?></th>
                            <th><?=lang('cms.gameprovider');?></th>
                            <th><?=lang('cms.gametype');?></th>
                            <th><?=lang('cms.gamename');?></th>
                            <th><?=lang('Real Bet');?></th>
                            <th><?=lang('Available Bet');?></th>
                            <th><?=lang('mark.resultAmount');?></th>
                            <th><?=lang('lang.bet.plus.result');?></th>
                            <th><?php echo lang('Win Amount'); ?></th>
                            <th><?php echo lang('Loss Amount'); ?></th>
                            <th><?=lang('mark.afterBalance');?></th>
                            <!-- <th><?=lang('pay.transamount');?></th> -->
                            <!-- <th><?php echo lang('Round No'); ?></th> -->
                            <th><?=lang('Notes');?></th>
                            <th><?=lang('Bet Detail');?></th>
                            <th><?=lang('player.ut10');?></th>
                            <th><?=lang('Bet Type');?></th>
                            <?php if( $this->utils->isEnabledFeature('show_sports_game_columns_in_game_logs') ){?>
                                <th><?=lang('Match Type');?></th>
                                <th><?=lang('Match Details');?></th>
                                <th><?=lang('Handicap');?></th>
                                <th><?=lang('Odds');?></th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th><?=lang('Sub Total')?></th>
                            <th colspan="5"></th>
                            <th><span class="pull-right" id="real-bet-subtotal">0.00</span></th>
                            <th><span class="pull-right" id="bet-subtotal">0.00</span></th>
                            <th><span class="pull-right" id="result-subtotal">0.00</span></th>
                            <th><span class="pull-right" id="bet-result-subtotal">0.00</span></th>
                            <th><span class="pull-right" id="win-subtotal">0.00</span></th>
                            <th><span class="pull-right" id="loss-subtotal">0.00</span></th>
                            <?php
                            $cols = 5;
                            if( $this->utils->isEnabledFeature('show_sports_game_columns_in_game_logs') ){
                                $cols+=4;
                            }
                            echo '<th colspan="'.$cols.'"></th>';
                            ?>
                            <!-- <th><span class="pull-right" id="amount-subtotal">0.00</span></th> -->
                            <!-- <th></th> -->
                        </tr>
                        <tr>
                            <th><?=lang('Total')?></th>
                            <th colspan="5"></th>
                            <th><span class="pull-right" id="real-bet-total">0.00</span></th>
                            <th><span class="pull-right" id="bet-total">0.00</span></th>
                            <th><span class="pull-right" id="result-total">0.00</span></th>
                            <th><span class="pull-right" id="bet-result-total">0.00</span></th>
                            <th><span class="pull-right" id="win-total">0.00</span></th>
                            <th><span class="pull-right" id="loss-total">0.00</span></th>
                            <th colspan="<?=$cols?>"></th>
                            <!-- <th><span class="pull-right" id="amount-total">0.00</span></th> -->
                            <!-- <th></th> -->
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
<form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
<input name='json_search' type="hidden">
</form>
<script type="text/javascript">
 var export_type="<?php echo $this->utils->isEnabledFeature('export_excel_on_queue') ? 'queue' : 'direct';?>";
$(document).ready(function(){
    var dataTable = $('#myTable').DataTable({
        lengthMenu: [50, 100, 250, 500, 1000],

        autoWidth: false,
            searching: false,
            // dom: "<'panel-body'<'pull-right'B>l>t<'text-center'r><'panel-body'<'pull-right'p>i>",
            dom: "<'panel-body' <'pull-right'B><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ],
                    className:'btn btn-sm ',
                },
                {
                    text: "<?php echo lang('CSV Export'); ?>",
                    className:'btn btn-sm btn-primary',
                    action: function ( e, dt, node, config ) {
                         var form_params=$('#search-form').serializeArray();
                         var d = {'extra_search': form_params, 'export_format': 'csv', 'export_type': export_type,
                            'draw':1, 'length':-1, 'start':0};
                        $("#_export_excel_queue_form").attr('action', '<?php echo site_url('/export_data/gamesHistory') ?>');
                        $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
                        $("#_export_excel_queue_form").submit();
                    }
                }
            ],

            columnDefs: [

                { className: 'text-right', targets: [ 6,7,8,9,10,11,12,13 ] },
                { visible: false, targets: [ 2,3,4,14 ] }
            ],
            order: [[0, 'desc']],

            // SERVER-SIDE PROCESSING
            processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {

                data.extra_search = $('#search-form').serializeArray();

                $.post(base_url + "api/agency_game_history", data, function(data) {

                    if (data.sub_summary.length != 0) {
                        $('#real-bet-subtotal').text(data.sub_summary[0].total_real_bet);
                        $('#bet-subtotal').text(data.sub_summary[0].total_bet);
                        $('#result-subtotal').text(data.sub_summary[0].total_result);
                        $('#bet-result-subtotal').text(data.sub_summary[0].total_bet_result);
                        $('#win-subtotal').text(data.sub_summary[0].total_win);
                        $('#loss-subtotal').text(data.sub_summary[0].total_loss);
                        $('#amount-subtotal').text(data.sub_summary[0].total_amount);
                    }

                    $('#real-bet-total').text(data.summary[0].total_real_bet);
                    $('#bet-total').text(data.summary[0].total_bet);
                    $('#result-total').text(data.summary[0].total_result);
                    $('#bet-result-total').text(data.summary[0].total_bet_result);
                    $('#win-total').text(data.summary[0].total_win);
                    $('#loss-total').text(data.summary[0].total_loss);
                    $('#amount-total').text(data.summary[0].total_amount);
                    // $('#platform-summary').html('');
                    // $.each(data.sub_summary, function(i,v) {
                    //     $('#platform-summary').append(v.system_code + ' <?php echo lang("Bet");?>: ' + parseFloat(v.total_bet).toFixed(2) + '<br>');
                    //     $('#platform-summary').append(v.system_code + ' <?php echo lang("Result");?>: ' + parseFloat(v.total_result).toFixed(2) + '<br>');
                    //     $('#platform-summary').append(v.system_code + ' <?php echo lang("Win");?>: ' + (parseFloat(v.total_win) || 0).toFixed(2) + '<br>');
                    //     $('#platform-summary').append(v.system_code + ' <?php echo lang("Loss");?>: ' + (parseFloat(v.total_loss) || 0).toFixed(2) + '<br>');
                    // });
                    callback(data);
                },'json')
                .fail( function (jqxhr, status_text)  {
                    if ( jqxhr.status >= 300 && jqxhr.status < 500 ) {
                        if (confirm('<?= lang('session.timeout') ?>')) {
                            window.location.href = '/';
                        }
                    }
                    else {
                        alert(status_text);
                    }
                });

            },

            fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                // utils.safelog('aData[5]'+aData[5]+"aData[14]"+aData[14]);
                if (aData[15] == "<?=Game_logs::FLAG_GAME?>" && aData[6] == '0.00' && aData[8] != '0.00') {
                    $(nRow).css('background-color', '#<?=$this->config->item('color')['free_game']?>');
                }

                if (aData[15] == "<?=Game_logs::FLAG_TRANSACTION?>") {
                    $(nRow).css('background-color', '#<?=$this->config->item('color')['trans_in_game_log']?>');
                }
            }


    });


    <?php if ($this->utils->getConfig('agency_game_logs_report_date_range_restriction')): ?>

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

    $('#search-form input[type="text"],#search-form input[type="number"],#search-form input[type="email"]').keypress(function (e) {
        if (e.which == 13) {
            dataTable.ajax.reload();
        }
    });

});

function betResult(betHistoryId) {
    var dst_url = '/agency/bet_result/' + betHistoryId;
    open_modal('common_modal', dst_url, "<?php echo lang('Bet Result'); ?>" );
}

function betDetail(betHistoryId) {
    var dst_url = '/agency/bet_detail/' + betHistoryId;
    open_modal('common_modal', dst_url, "<?php echo lang('Bet Detail'); ?>" );
}

</script>
<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of view_game_logs.php

<!-- ============ Game History ================== -->
<div id="ah-game" role="tabpanel" class="tab-pane active">
    <div id="game-box" class="report table-responsive">
        <table id="gameResultTable" width="100%" class="table table-striped table-hover dt-responsive display nowrap"></table>
    </div>
</div>
<script type="text/javascript">
    var gameTB;

    $('.filter-select-game #game_platform_id').off('change').on('change', function(){
        $('#accountHistory').trigger($.Event('draw.t1t.player-center.report'));
    });

    function gameHistory() {
        var div = $("#game-box");
        var table_container = $('#gameResultTable');

        if(gameTB !== undefined){
            gameTB.page.len($('#pageLength').val());
            gameTB.ajax.reload();
            return false;
        }

        var render_log_summary = function(div){
            var log_summary;

            <?php if(!$this->utils->isEnabledFeature('disabled_display_sub_total_row_in_player_center_game_history_report')):?>
            if($('#log-summary').length <= 0){
                log_summary = $('<table>').attr({'id': 'log-summary', 'class': 'table table-striped table-hover dt-responsive display nowrap'});
                log_summary.appendTo(div);

                // log_summary.append($('<div>').append($('<span>').html('<?=lang('cms.totalBetAmount');?>: ')).append($('<span>').addClass('bet-total').html('0.00')));
                // log_summary.append($('<div>').append($('<span>').html('<?=lang('cms.totalResultAmount');?>: ')).append($('<span>').addClass('result-total').html('0.00')));
                // log_summary.append($('<div>').append($('<span>').html('<?=lang('Total Bet + Result Amount');?>: ')).append($('<span>').addClass('bet-result-total').html('0.00')));
                // log_summary.append($('<div>').append($('<span>').html('<?=lang('Total Win');?>: ')).append($('<span>').addClass('win-total').html('0.00')));
                // log_summary.append($('<div>').append($('<span>').html('<?=lang('Total Loss');?>: ')).append($('<span>').addClass('loss-total').html('0.00')));
            }else{
                log_summary = $('#log-summary').empty();
            }
            log_summary.css('text-align', 'right');

            var thead = $('<thead>');
            thead.appendTo(log_summary);

            var tr = $('<tr>');
            $('<th class ="lscss">').append($('<span>').html('<?=lang('sys.gt7');?>')).appendTo(tr);
            $('<th>').append($('<span>').html('<?=lang('mark.bet');?>')).appendTo(tr);
            $('<th>').append($('<span>').html('<?=lang('Result');?>')).appendTo(tr);
            $('<th>').append($('<span>').html('<?=lang('Wins');?>')).appendTo(tr);
            $('<th>').append($('<span>').html('<?=lang('player.ui28');?>')).appendTo(tr);
            $('<th>').append($('<span>').html('<?=lang('lang.bet.plus.result');?>')).appendTo(tr);
            tr.appendTo(thead);

            var tbody = $('<tbody>');
            tbody.appendTo(log_summary);
            <?php endif;?>

            return log_summary;
        };

        render_log_summary(div);

        //$("p").css({"background-color":"yellow","font-size":"200%"});
        //log_summary.css('border', '1px solid #dddddd');
        // var platform_summary;
        // if($('#platform-summary').length <= 0){
        //     platform_summary = $('<div>').attr('id', 'platform-summary');
        //     platform_summary.appendTo(div);
        // }else{
        //     platform_summary = $('#platform-summary');
        // }
        // platform_summary.css('text-align', 'right');

        var columns = [];
        columns.push({
            "name": "game_log_end_date",
            "title": "<?=lang('player.ug01');?>",
            "data": 0,
            "visible": true,
            "orderable": true,
            "responsivePriority": 1
        });
        columns.push({
            "name": "player_user_name",
            "title": "<?=lang('Player Username');?>",
            "data": 1,
            "visible": false,
            "orderable": false,
            "responsivePriority": 2
        });
        columns.push({
            "name": "affiliate_username",
            "title": "<?=lang('Affiliate Username');?>",
            "data": 3,
            "visible": false,
            "orderable": false,
            "responsivePriority": 3
        });
        columns.push({
            "name": "roundno",
            "title": "<?php echo lang('Round No'); ?>",
            "data": 16,
            "visible": true,
            "orderable": false,
            "responsivePriority": 15
        });
        columns.push({
            "name": "game_provider",
            "title": "<?=lang('cms.gameprovider');?>",
            "data": 5,
            "visible": true,
            "orderable": false,
            "responsivePriority": 14
        });
        columns.push({
            "name": "game_type",
            "title": "<?=lang('cms.gametype');?>",
            "data": 6,
            "visible": false,
            "orderable": false,
            "responsivePriority": 5
        });
        columns.push({
            "name": "game_name",
            "title": "<?=lang('cms.gamename');?>",
            "data": 7,
            "visible": true,
            "orderable": false,
            "responsivePriority": 6
        });
        columns.push({
            "name": "real_bet_amount",
            "title": "<?=lang('Real Bet');?>",
            "data": 8,
            "visible": false,
            "orderable": false,
            "responsivePriority": 7
        });
        columns.push({
            "name": "bet_amount",
            "title": "<?=lang('Available Bet');?>",
            "data": 9,
            "visible": true,
            "orderable": false,
            "responsivePriority": 8
        });
        columns.push({
            "name": "result_amount",
            "title": "<?=lang('mark.resultAmount');?>",
            "data": 10,
            "visible": false,
            "orderable": false,
            "responsivePriority": 9
        });
        columns.push({
            "name": "bet_plus_result_amount",
            "title": "<?=lang('lang.bet.plus.result');?>",
            "data": 11,
            "visible": false,
            "orderable": false,
            "responsivePriority": 10
        });
        columns.push({
            "name": "win_amount",
            "title": "<?=lang('Win Amount'); ?>",
            "data": 12,
            "visible": true,
            "orderable": false,
            "responsivePriority": 11
        });
        columns.push({
            "name": "loss_amount",
            "title": "<?=lang('Loss Amount'); ?>",
            "data": 13,
            "visible": true,
            "orderable": false,
            "responsivePriority": 12
        });
        columns.push({
            "name": "after_balance",
            "title": "<?=lang('mark.afterBalance');?>",
            "data": 14,
            "visible": false,
            "orderable": false,
            "responsivePriority": 13
        });
        columns.push({
            "name": "trans_amount",
            "title": "<?=lang('pay.transamount');?>",
            "data": 15,
            "visible": false,
            "orderable": false,
            "responsivePriority": 14
        });
        columns.push({
            "name": "betDetails",
            "title": "<?=lang('Bet Detail');?>",
            "data": 18,
            "visible": true,
            "orderable": false,
            "responsivePriority": 17
        });
        columns.push({
            "name": "flag",
            "title": "<?=lang('player.ut10');?>",
            "data": 19,
            "visible": false,
            "orderable": false,
            "responsivePriority": 18
        });
        columns.push({ // for the responsive extention to display control row button
            "title": "&nbsp",
            "data": 1,
            "visible": true,
            "orderable": false,
            "responsivePriority": 1,
            "render": function(){
                return '&nbsp';
            }
        });

        gameTB = table_container.DataTable($.extend({}, dataTable_options, {
            "pageLength": $('#pageLength').val(),
            "columns": columns,
            columnDefs: [ {  // for the responsive extention to display control row button
                className: 'control',
                orderable: false,
                targets:   -1
            }],
            order: [[0, 'desc']],
            ajax: function(data, callback, settings){
                $.ajax({
                    //url: '/api/player_games_history',
                    url: '/ajax/account_history/player_games_history',
                    type: 'post',
                    data: $.extend({}, data, {
                        extra_search: [
                            {
                                'name':'dateRangeValueStart',
                                'value': $('#sdate').val(),
                            },
                            {
                                'name':'dateRangeValueEnd',
                                'value': $('#edate').val(),
                            },
                            {
                                'name':'by_game_platform_id',
                                'value': $('#game_platform_id').val(),
                            },
                            {
                                'name':'by_bet_type',
                                'value': $('#searchUnsettle').prop("checked") ? 2:1, // 1: Settled; 2: Unsettled
                            }
                        ]
                    }),
                    success: function(json){
                        var log_summary = render_log_summary($("#game-box"));
                        var tbody = $('tbody', log_summary);
                        tbody.empty();

                        $.each(json.sub_summary, function(i,v) {
                            var tr = $('<tr>');
                            $('<td>').append($('<span>').html(v.system_code)).appendTo(tr);
                            $('<td>').append($('<span>').html(parseFloat(v.total_bet).toFixed(2))).appendTo(tr);
                            $('<td>').append($('<span>').html(parseFloat(v.total_result).toFixed(2))).appendTo(tr);
                            $('<td>').append($('<span>').html(parseFloat(v.total_win).toFixed(2))).appendTo(tr);
                            $('<td>').append($('<span>').html(parseFloat(v.total_loss).toFixed(2))).appendTo(tr);
                            $('<td>').append($('<span>').html(parseFloat(v.total_bet_result).toFixed(2))).appendTo(tr);
                            tr.appendTo(tbody);
                        });

                        var tr = $('<tr>');
                        $('<td>').append($('<span>').html('<?=lang('cs.total');?>')).appendTo(tr);
                        $('<td>').append($('<span>').addClass('bet-total').html(parseFloat(json.summary[0].total_bet).toFixed(2))).appendTo(tr);
                        $('<td>').append($('<span>').addClass('result-total').html(parseFloat(json.summary[0].total_result).toFixed(2))).appendTo(tr);
                        $('<td>').append($('<span>').addClass('win-total').html(parseFloat(json.summary[0].total_win).toFixed(2))).appendTo(tr);
                        $('<td>').append($('<span>').addClass('loss-total').html(parseFloat(json.summary[0].total_loss).toFixed(2))).appendTo(tr);
                        $('<td>').append($('<span>').addClass('bet-result-total').html(parseFloat(json.summary[0].total_bet_result).toFixed(2))).appendTo(tr);
                        tr.appendTo(tbody);

                        // platform_summary.empty();
                        // $.each(json.sub_summary, function(i,v) {
                        //     platform_summary.append(v.system_code + ' <?=lang("mark.bet"); ?>: ' + parseFloat(v.total_bet).toFixed(2) + '<br>');
                        //     platform_summary.append(v.system_code + ' <?=lang("Result"); ?>: ' + parseFloat(v.total_result).toFixed(2) + '<br>');
                        //     platform_summary.append(v.system_code + ' <?=lang("lang.bet.plus.result"); ?>: ' + parseFloat(v.total_bet_result).toFixed(2) + '<br>');
                        //     platform_summary.append(v.system_code + ' <?=lang("Wins"); ?>: ' + parseFloat(v.total_win).toFixed(2) + '<br>');
                        //     platform_summary.append(v.system_code + ' <?=lang("player.ui28"); ?>: ' + parseFloat(v.total_loss).toFixed(2) + '<br>');
                        // });

                        callback(json);
                    }
                })
            }

        }));
    }
</script>
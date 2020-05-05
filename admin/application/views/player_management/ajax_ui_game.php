<div data-file-info="ajax_ui_game.php" data-datatable-selector="#gamehistory-table">
    <form class="form-inline" id="search-form">
        <select class="form-control input-sm" name="by_game_flag" id="by_game_flag">
            <option value=""><?=lang('All');?></option>
            <option value="<?=Game_logs::FLAG_GAME?>"><?=lang('sys.gd5');?></option>
            <option value="<?=Game_logs::FLAG_TRANSACTION?>"><?=lang('pay.transact');?></option>
        </select>
        <input type="text" id="search_game_date" class="form-control input-sm dateInput inline" data-start="#by_date_from" data-end="#by_date_to" data-time="true"
            <?php $range_restriction = $this->utils->getConfig('player_game_history_date_range_restriction'); ?>
            <?php if ($range_restriction): ?>
                data-restrict-max-range="<?=$range_restriction?>"
                data-restrict-range-label="<?=sprintf(lang("restrict_date_range_label"), $range_restriction)?>"
            <?php endif ?>
            autocomplete="off"
            required
        />
        <input type="hidden" id="by_date_from" name="by_date_from"/>
        <input type="hidden" id="by_date_to" name="by_date_to"/>
        <input type="hidden" id="by_bet_type" name="by_bet_type" value="<?=$bet_type?>"/>
        <input type="hidden" id="is_player_game_history" name="is_player_game_history" value="TRUE"/>
        <select name="by_game_platform_id" id="by_game_platform_id" class="form-control input-sm">
            <option value=""><?=lang('lang.all') . ' ' . lang('cms.gameprovider')?></option>
            <?php foreach ($game_platforms as $game_platform): ?>
                <option value="<?=$game_platform['id']?>"><?=$game_platform['system_code']?></option>
            <?php endforeach?>
        </select>
        <input type="button" class="btn btn-portage btn-sm" id="btn-submit" value="<?=lang('lang.search');?>"/>
    </form>
    <hr/>
    <div class="clearfix">
        <table id="gamehistory-table" class="table table-bordered">
            <thead>
                <tr>
                    <?php include __DIR__ . '/../includes/cols_for_game_logs.php';?>
                </tr>
            </thead>

            <tfoot>
                <?php if ($this->utils->isEnabledFeature('show_sub_total_for_game_logs_report')): ?>
                    <?php include __DIR__.'/../includes/footer_sub_for_game_logs.php'; ?>
                <?php endif;?>
                <?php include __DIR__.'/../includes/footer_for_game_logs.php'; ?>
            </tfoot>
        </table>
    </div>
</div>

<script type="text/javascript">
    var hiddenColumns = [];
    var rightTextColumns = [];

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

    function gamesHistory() {
        // Get hidden, text right and flag column
        var elem = $('#gamehistory-table thead tr th');
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

        var dataTable = $('#gamehistory-table').DataTable({
            dom: "<'row'<'col-md-12'<'pull-right'B><'pull-right progress-container'>l<'dt-information-summary2 text-info pull-left' i>>><'table-responsive't><'row'<'col-md-12'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>>",
            autoWidth: false,
            searching: false,
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ],
                    className: ['btn-linkwater']
                }
            ],
            columnDefs: [
                { className: 'text-right', targets: rightTextColumns },
                { visible: false, targets: hiddenColumns }
            ],
            order: [
                [1,'desc'],
                [0,'desc']
            ],
            processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {
                data.extra_search = $('#changeable_table #search-form').serializeArray();

                $.post("/api/gamesHistory/" + playerId, data, function(data) {
                    <?php if ($this->utils->isEnabledFeature('show_sub_total_for_game_logs_report')): ?>
                        var real_total_bet = 0, total_bet = 0, total_result = 0, total_bet_result = 0, total_win = 0, total_loss = 0;
                        $.each(data.data, function(i, v){
                            var rtc = rightTextColumns;
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
                    <?php endif;?>
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
                        $('#platform-summary').append(v.system_code + ' <?=lang("Bet"); ?>: ' + addCommas(parseFloat(v.total_bet).toFixed(2)) + '<br>');
                        $('#platform-summary').append(v.system_code + ' <?=lang("Result"); ?>: ' + addCommas(parseFloat(v.total_result).toFixed(2)) + '<br>');
                        $('#platform-summary').append(v.system_code + ' <?=lang("Win"); ?>: ' + addCommas(parseFloat(v.total_win).toFixed(2)) + '<br>');
                        $('#platform-summary').append(v.system_code + ' <?=lang("Loss"); ?>: ' + addCommas(parseFloat(v.total_loss).toFixed(2)) + '<br>');
                    });
                  callback(data);
                },'json');
            },
            fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                /* *********************************************************
                 *  Free spin if condition met
                 * *********************************************************
                 *      real bet = 0 or n/A
                 *      available bet = 0 or n/A
                 *      result amount != 0
                 *      flag = 1
                 **********************************************************/
                if (aData[flagColIndex] == "<?=Game_logs::FLAG_GAME?>" && parseFloat(aData[avlBetCol]) == 0.00 && (parseFloat(aData[realBetCol]) == 0.00 || isNaN(aData[realBetCol])) && parseFloat(aData[resAmtCol]) != 0.00 ) {
                    if (aData[29] != 'N/A' && "<?= $this->utils->isEnabledFeature('hide_free_spin_on_game_history') ?>"){
                    } else {
                        $(nRow).css('background-color', '#<?=$this->config->item('color')['free_game']?>');
                    }
                }

                if (aData[flagColIndex] == "<?=Game_logs::FLAG_TRANSACTION?>") {
                  $(nRow).css('background-color', '#<?=$this->config->item('color')['trans_in_game_log']?>');
                }
            }
        });

        <?php if ($range_restriction): ?>
            var dateInput = $('#changeable_table #search_game_date.dateInput');
            var isTime = dateInput.data('time');

            dateInput.keypress(function(e){
                e.preventDefault();
                return false;
            });

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

            // -- Upon submit, date range will be checked first
            $('#changeable_table #btn-submit').click( function() {
                var dateInput = $('#search_game_date.dateInput');

                var $restricted_range = dateInput.data('restrict-max-range');

                if ($restricted_range == '' && !$.isNumeric($restricted_range) && !isRange)
                    return false;

                var a_day = 86400000;
                var restriction = a_day * $restricted_range;
                var start_date = new Date($('#by_date_from').val());
                var end_date = new Date($('#by_date_to').val());

                if($.trim(dateInput.val()) == '' || ((end_date - start_date) >= restriction)){
                    if(dateInput.data('restrict-range-label') && $.trim(dateInput.data('restrict-range-label')) !== ""){
                        alert(dateInput.data('restrict-range-label'));
                    } else {
                        var day_label = 'day';

                        if($restricted_range > 1) day_label = 'days'

                        alert('Please choose a date range not greater than '+ $restricted_range +' '+ day_label);
                    }
                } else{
                    dataTable.ajax.reload();
                }
            });
        <?php else: ?>
            $('#changeable_table #btn-submit').click( function() {
                dataTable.ajax.reload();
            });
        <?php endif ?>

        ATTACH_DATATABLE_BAR_LOADER.init('gamehistory-table');
    }
</script>


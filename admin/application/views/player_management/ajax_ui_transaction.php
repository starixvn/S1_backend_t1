<div id="transaction_panel" data-file-info="ajax_ui_transaction.php" data-datatable-selector="#transaction-table">
    <div class="form-group">
        <fieldset>
            <div class="form-group">
                <legend>
                    <label class="control-label"><?=lang('player.ut02')?></label>
                </legend>
                <label class="checkbox-inline"><input id="check_all_transaction" type="checkbox"  value="" checked><?=lang('Select All')?></label>
                <label class="checkbox-inline"><input type="checkbox" name="transaction_type[]" value="<?=Transactions::ADD_BONUS?>" checked><?=lang('transaction.transaction.type.' . Transactions::ADD_BONUS)?></label>
                <label class="checkbox-inline"><input type="checkbox" name="transaction_type[]" value="<?=Transactions::AUTO_ADD_CASHBACK_TO_BALANCE?>" checked><?=lang('transaction.transaction.type.' . Transactions::AUTO_ADD_CASHBACK_TO_BALANCE)?></label>
                <label class="checkbox-inline"><input type="checkbox" name="transaction_type[]" value="<?=Transactions::DEPOSIT?>" checked><?=lang('transaction.transaction.type.' . Transactions::DEPOSIT)?></label>
                <label class="checkbox-inline"><input type="checkbox" name="transaction_type[]" value="<?=Transactions::MANUAL_ADD_BALANCE_ON_SUB_WALLET?>" checked><?=lang('transaction.transaction.type.' . Transactions::MANUAL_ADD_BALANCE_ON_SUB_WALLET)?></label>
                <label class="checkbox-inline"><input type="checkbox" name="transaction_type[]" value="<?=Transactions::FEE_FOR_OPERATOR?>" checked><?=lang('transaction.transaction.type.' . Transactions::FEE_FOR_OPERATOR)?></label>
                <label class="checkbox-inline"><input type="checkbox" name="transaction_type[]" value="<?=Transactions::FEE_FOR_PLAYER?>" checked><?=lang('transaction.transaction.type.' . Transactions::FEE_FOR_PLAYER)?></label>
                <label class="checkbox-inline"><input type="checkbox" name="transaction_type[]" value="<?=Transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET?>" checked><?=lang('transaction.transaction.type.' . Transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET)?></label>
                <label class="checkbox-inline"><input type="checkbox" name="transaction_type[]" value="<?=Transactions::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET?>" checked><?=lang('transaction.transaction.type.' . Transactions::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET)?></label>
                <label class="checkbox-inline"><input type="checkbox" name="transaction_type[]" value="<?=Transactions::MANUAL_ADD_BALANCE?>" checked><?=lang('transaction.transaction.type.' . Transactions::MANUAL_ADD_BALANCE)?></label>
                <label class="checkbox-inline"><input type="checkbox" name="transaction_type[]" value="<?=Transactions::MANUAL_SUBTRACT_BALANCE?>" checked><?=lang('transaction.transaction.type.' . Transactions::MANUAL_SUBTRACT_BALANCE)?></label>
                <label class="checkbox-inline"><input type="checkbox" name="transaction_type[]" value="<?=Transactions::PLAYER_REFER_BONUS?>" checked><?=lang('transaction.transaction.type.' . Transactions::PLAYER_REFER_BONUS)?></label>
                <label class="checkbox-inline"><input type="checkbox" name="transaction_type[]" value="<?=Transactions::RANDOM_BONUS?>" checked><?=lang('transaction.transaction.type.' . Transactions::RANDOM_BONUS)?></label>
                <label class="checkbox-inline"><input type="checkbox" name="transaction_type[]" value="<?=Transactions::SUBTRACT_BONUS?>" checked><?=lang('transaction.transaction.type.' . Transactions::SUBTRACT_BONUS)?></label>
                <label class="checkbox-inline"><input type="checkbox" name="transaction_type[]" value="<?=Transactions::MEMBER_GROUP_DEPOSIT_BONUS?>" checked><?=lang('transaction.transaction.type.' . Transactions::MEMBER_GROUP_DEPOSIT_BONUS)?></label>
                <label class="checkbox-inline"><input type="checkbox" name="transaction_type[]" value="<?=Transactions::WITHDRAWAL?>" checked><?=lang('transaction.transaction.type.' . Transactions::WITHDRAWAL)?></label>
                <label class="checkbox-inline"><input type="checkbox" name="transaction_type[]" value="<?=Transactions::MANUAL_SUBTRACT_BALANCE_ON_SUB_WALLET?>" checked><?=lang('transaction.transaction.type.' . Transactions::MANUAL_SUBTRACT_BALANCE_ON_SUB_WALLET)?></label>
            </div>
        </fieldset>
    </div>
    <div class="form-inline">
        <input type="text" id="reportrange" class="form-control input-sm dateInput inline" data-start="#dateRangeValueStart" data-end="#dateRangeValueEnd" data-time="true" autocomplete="off" />
        <input type="hidden" id="dateRangeValueStart" name="dateRangeValueStart"/>
        <input type="hidden" id="dateRangeValueEnd" name="dateRangeValueEnd"/>
        <input type="button" class="btn btn-portage btn-sm" id="btn-submit" value="<?=lang('lang.search');?>"/>
    </div>
    <hr/>
    <div class="clearfix">
        <table id="transaction-table" class="table table-bordered">
            <thead>
                <tr>
                    <th><?=lang('player.ut01'); ?></th>
                    <th><?=lang('player.ut02'); ?></th>
                    <th><?=lang('player.ut03'); ?></th>
                    <th><?=lang('player.ut04'); ?></th>
                    <?php if ($this->utils->isEnabledFeature('enable_tag_column_on_transaction')): ?>
                        <th><?=lang("player.41")?></th>
                    <?php endif; ?>
                    <th data-col="amount"><?=lang('player.ut05'); ?></th>
                    <th><?=lang('player.ut06'); ?></th>
                    <th><?=lang('player.ut07'); ?></th>
                    <th><?=lang('player.ut08'); ?></th>
                    <th><?=lang('cms.promoCat'); ?></th>
                    <?php if ($this->utils->isEnabledFeature('enable_adjustment_category')): ?>
                        <th><?=lang('Adjustment Category')?></th>
                    <?php endif; ?>
                    <th><?=lang('Promo Rule'); ?></th>
                    <th><?=lang('Changed Balance'); ?></th>
                    <th><?=lang('player.ut10'); ?></th>
                    <th><?=lang('Request ID');?></th>
                    <th><?=lang('ID'); ?></th>
                    <th><?=lang('player.ut11'); ?></th>
                    <th><?=lang('player.ut13'); ?></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <?php if ($this->utils->isEnabledFeature('enable_tag_column_on_transaction')): ?>
                        <th colspan="4"></th>
                    <?php else: ?>
                        <th colspan="3"></th>
                    <?php endif; ?>

                    <th><?=lang('Total'); ?></th>
                    <th id="amout_sum"></th>

                    <?php if ($this->utils->isEnabledFeature('enable_adjustment_category')): ?>
                        <th colspan="12"></th>
                    <?php else: ?>
                        <th colspan="11"></th>
                    <?php endif; ?>
                </tr>
            </tfoot>
       </table>
    </div>
</div>

<style type="text/css">
    #transaction_panel .checkbox-inline {
        margin: 0 10px 0 0;
    }
</style>
<script type="text/javascript">
    $(document).ready(function() {
        initDateInputComboExtendAttr($('.dateInput'));
        $("#check_all_transaction").click(function () {
            $('#transaction_panel input:checkbox').not(this).prop('checked', this.checked);
        });

        $("#transaction_panel input:checkbox").click(function () {
            var numberOfCheckedTransactionType = $('input[name^=transaction_type]:checked').length;
            var totalNumberOfTransactionType = $('input[name^=transaction_type]').length;
            if(numberOfCheckedTransactionType == totalNumberOfTransactionType){
                $("#check_all_transaction").prop('checked', true);
            } else {
                $("#check_all_transaction").prop('checked', false);
            }
        });
    });

    function transactionHistory(player_id) {
        var dataTable = $('#transaction-table').DataTable({
            dom: "<'row'<'col-md-12'<'pull-right'B><'pull-right progress-container'>l<'dt-information-summary2 text-info pull-left' i>>><'table-responsive't><'row'<'col-md-12'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>>",
            searching: false,
            responsive: false,
            buttons: [{
                extend: 'colvis',
                postfixButtons: ['colvisRestore'],
                className: ['btn-linkwater']
            }],
            columnDefs: [
                {
                    className: 'text-right',
                    targets: [4, 5, 6, 9]
                },
                {
                    visible: false,
                    targets: [10, 11, 12]
                }
            ],
            order: [
                [0, 'desc']
            ],
            processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {
                $transaction_type = [];
                var check_all_transaction = $('#check_all_transaction:checked').length;
                $('input[name^=transaction_type]:checked').each(function(){
                    $transaction_type.push(parseInt($(this).val()));
                });
                if(check_all_transaction > 0){
                    $transaction_type = [];
                }
                data.extra_search = [{
                    'name': 'dateRangeValueStart',
                    'value': $('#dateRangeValueStart').val()
                },
                {
                    'name': 'dateRangeValueEnd',
                    'value': $('#dateRangeValueEnd').val()
                },
                {
                    'name': 'transactionTypeFilter',
                    'value': $transaction_type.toString()
                },
                {
                    'name': 'is_player_transaction_history',
                    'value': 'TRUE'
                }];

                var numberOfCheckedTransactionType = $('input[name^=transaction_type]:checked').length;
                if(numberOfCheckedTransactionType > 0){
                    $.post('/api/transactionHistory/' + player_id, data, function (data) {
                        callback(data);
                    }, 'json');
                } else {
                    alert("<?=lang('Choose Transaction type')?>");
                }
            },
            footerCallback: function (hrow, data, start, end, display) {
                var amount_col = $('th[data-col="amount"]').index();
                var sum = 0;

                for (var i in data) {
                    var row = data[i];
                    var amount_text = row[amount_col].replace(/,|<[^>]+>/g, '');
                    var amount = parseFloat(amount_text);
                    sum += amount;
                }

                $('#amout_sum').text(
                    numeral(sum).format('11,111.23')
                );
            }
        });

        var dateInput = $('#changeable_table #reportrange.dateInput');
        var isTime = dateInput.data('time');

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

        $('#changeable_table #reportrange.dateInput').each( function() {
            $(this).keypress(function(e){
                e.preventDefault();
                return;
            });
        });

        $('#changeable_table #btn-submit').click(function () {
            // -- Check if date is empty
            if($.trim($('#changeable_table #reportrange.dateInput').val()) == ''){
                alert('<?=lang("require_date_range_label")?>');
                return;
            }

            dataTable.ajax.reload();
        });

        ATTACH_DATATABLE_BAR_LOADER.init('transaction-table');
    }
</script>

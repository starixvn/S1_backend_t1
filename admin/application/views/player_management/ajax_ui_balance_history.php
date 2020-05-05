<div data-file-info="ajax_ui_balance_history.php" data-datatable-selector="#balance-table">
    <div class="form-inline">
        <input type="text" id="reportrange" class="form-control input-sm dateInput inline" data-start="#dateRangeValueStart" data-end="#dateRangeValueEnd" data-time="true"/>
        <input type="hidden" id="dateRangeValueStart" name="dateRangeValueStart"/>
        <input type="hidden" id="dateRangeValueEnd" name="dateRangeValueEnd"/>
        <input type="button" class="btn btn-portage btn-sm" id="btn-submit" value="<?=lang('lang.search');?>"/>
    </div>
    <hr/>

    <table id="balance-table" class="table table-bordered">
        <thead>
            <th><?=lang('Date'); ?></th>
            <th><?=lang('Action Type'); ?></th>
            <th><?=lang('Main Wallet'); ?></th>
            <th><?=lang('Total Balance'); ?></th>
            <th><?=lang('Details'); ?></th>
        </thead>
    </table>
</div>

<script type="text/javascript">
    function balance_history() {
        var dataTable = $('#balance-table').DataTable({
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
            order: [ [0, 'desc'] ],
            processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {
                data.extra_search = [
                    {
                        'name':'dateRangeValueStart',
                        'value':$('#dateRangeValueStart').val()
                    },
                    {
                        'name':'dateRangeValueEnd',
                        'value':$('#dateRangeValueEnd').val()
                    }
                ];
                $.post(base_url + 'api/balance_history/' + playerId, data, function(data) {
                    callback(data);
                    _pubutils.attchBigWalletButton();
                },'json');
            }
        });

        $('#changeable_table #btn-submit').click( function() {
            dataTable.ajax.reload();
        });

        ATTACH_DATATABLE_BAR_LOADER.init('balance-table');
    }
</script>

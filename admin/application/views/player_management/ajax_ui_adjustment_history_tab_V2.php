<div id="adjustment_history_v2_panel" data-file-info="ajax_ui_adjustment_history_tab_V2.php" data-datatable-selector="#adjustment_history_v2">
    <div class="form-inline">
        <input type="text" id="reportrange" class="form-control input-sm dateInput inline" data-start="#dateRangeValueStart" data-end="#dateRangeValueEnd" data-time="true"/>
        <input type="hidden" id="dateRangeValueStart" name="dateRangeValueStart"/>
        <input type="hidden" id="dateRangeValueEnd" name="dateRangeValueEnd"/>
        <input type="button" class="btn btn-portage btn-sm" id="btn-submit" value="<?=lang('lang.search');?>"/>
    </div>
    <hr/>
    <div class="clearfix">
        <table id="adjustment_history_v2" class="table table-bordered">
            <thead>
                <tr>
                    <th><?=lang('player.uab01')?></th>
                    <th><?=lang('pay.username')?></th>
                    <th><?=lang('player.uab02')?></th>
                    <th><?=lang('adjustmenthistory.title.adjustmenttype')?></th>
                    <th><?=lang('adjustmenthistory.title.adjustmentamount')?></th>
                    <th><?=lang('adjustmenthistory.title.beforeadjustment')?></th>
                    <th><?=lang('adjustmenthistory.title.afteradjustment')?></th>
                    <th><?=lang('player.uab06')?></th>
                    <th><?=lang('lang.notes')?></th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<script type="text/javascript">
    function adjustmentHistoryV2() {
        var dataTable = $('#adjustment_history_v2').DataTable({
            dom: "<'row'<'col-md-12'<'pull-right'f><'pull-right progress-container'>l<'dt-information-summary2 text-info pull-left' i>>><'table-responsive't><'row'<'col-md-12'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>>",
            autoWidth: false,
            columnDefs: [
                {
                    className: 'text-right',
                    targets: [ 4, 5, 6 ]
                }
            ],
            order: [ 0, 'desc' ],
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

                $.post('/api/adjustment_history_tab_v2/' + playerId, data, function(data) {
                    callback(data);
                },'json');
            }
        });

        $('#changeable_table #btn-submit').click( function() {
            dataTable.ajax.reload();
        });

        ATTACH_DATATABLE_BAR_LOADER.init('adjustment_history_v2');
    }
</script>
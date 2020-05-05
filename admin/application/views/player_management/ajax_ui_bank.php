<div data-file-info="ajax_ui_bank.php" data-datatable-selector="#bank-table">
    <div class="form-inline">
        <input type="text" id="reportrange" class="form-control input-sm dateInput inline" data-start="#dateRangeValueStart" data-end="#dateRangeValueEnd" data-time="true"/>
        <input type="hidden" id="dateRangeValueStart" name="dateRangeValueStart"/>
        <input type="hidden" id="dateRangeValueEnd" name="dateRangeValueEnd"/>
        <input type="button" class="btn btn-portage btn-sm" id="btn-submit" value="<?=lang('lang.search');?>"/>
    </div>
    <hr/>
    <div class="clearfix">
        <table id="bank-table" class="table table-bordered">
            <thead>
                <tr>
                    <th><?=lang('player.ub01');?></th>
                    <th><?=lang('player.ub03');?></th>
                    <th><?=lang('player.ub04');?></th>
                    <th><?=lang('cms.updatedby');?></th>
                </tr>
            </thead>
        </table>
    </div>
</div>


<script type="text/javascript">
    function bankHistory() {
        var dataTable = $('#bank-table').DataTable({
            dom: "<'row'<'col-md-12'<'pull-right'f><'pull-right progress-container'>l<'dt-information-summary2 text-info pull-left' i>>><'table-responsive't><'row'<'col-md-12'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>>",
            autoWidth: false,
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

                $.post(base_url + 'api/bank_history/' + playerId, data, function(data) {
                    callback(data);
                },'json');
            }
        });

        $('#changeable_table #btn-submit').click( function() {
            dataTable.ajax.reload();
        });

        ATTACH_DATATABLE_BAR_LOADER.init('bank-table');
    }
</script>

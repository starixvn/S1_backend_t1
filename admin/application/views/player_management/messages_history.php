<div data-file-info="messages_history.php" data-datatable-selector="#chatHistory-table">
    <div class="form-inline">
        <input type="text" id="reportrange" class="form-control input-sm dateInput inline" data-start="#dateRangeValueStart" data-end="#dateRangeValueEnd" data-time="true"/>
        <input type="hidden" id="dateRangeValueStart" name="dateRangeValueStart"/>
        <input type="hidden" id="dateRangeValueEnd" name="dateRangeValueEnd"/>
        <input type="button" class="btn btn-portage btn-sm" id="btn-submit" value="<?=lang('lang.search');?>"/>
    </div>
    <hr/>
    <div class="clearfix">
        <table id="chatHistory-table" class="table table-bordered">
            <thead>
                <tr>
                    <th><?= lang('lang.date'); ?></th>
                    <th><?= lang('cs.subject'); ?></th>
                    <th><?= lang('cs.session'); ?></th>
                    <th><?= lang('cs.recipient'); ?></th>
                    <th><?= lang('cs.sender'); ?></th>
                    <th><?= lang('lang.action'); ?></th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<script type="text/javascript">
    function chatHistory() {
        var dataTable = $('#chatHistory-table').DataTable({
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

                $.post('/api/chat_history/' + playerId, data, function(data) {
                    callback(data);
                },'json');
            }
        });

        $('#changeable_table #btn-submit').click( function() {
            dataTable.ajax.reload();
        });

        ATTACH_DATATABLE_BAR_LOADER.init('chat_history');
    }
</script>

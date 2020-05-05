<div data-file-info="ajax_ui_comm_pref_history.php" data-datatable-selector="#communication-preference-table">
    <div class="panel-body">
        <div class="text-left">
            <div class="form-inline">
                <input type="text" id="reportrange" class="form-control input-sm dateInput inline" data-start="#dateRangeValueStart" data-end="#dateRangeValueEnd" data-time="true"/>
                <input type="hidden" id="dateRangeValueStart" name="dateRangeValueStart"/>
                <input type="hidden" id="dateRangeValueEnd" name="dateRangeValueEnd"/>
                <input type="button" class="btn btn-portage btn-sm" id="btn-submit" value="<?=lang('lang.search');?>"/>
            </div>
        </div>
        <hr/>
        <table id="communication-preference-table" class="table table-bordered">
            <thead>
                <tr>
                    <th><?=lang('Date of Request');?></th>
                    <th><?=lang('Preferences');?></th>
                    <th><?=lang('User');?></th>
                    <th><?=lang('Status');?></th>
                    <th><?=lang('Notes');?></th>
                    <th><?=lang('Action By');?></th>
                    <th><?=lang('Platform');?></th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<script type="text/javascript">
    function communicationPreferenceHistory(){
        var dataTable = $('#communication-preference-table').DataTable({
            dom: "<'row'<'col-md-12'<'pull-right'B><'pull-right progress-container'>l<'dt-information-summary2 text-info pull-left' i>>><'table-responsive't><'row'<'col-md-12'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>>",
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ],
                    className: ['btn-linkwater']
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

                $.post('/api/communicationPreferenceHistory/' + playerId, data, function(data) {
                    callback(data);
                },'json');
            }
        });

        $('#changeable_table #btn-submit').click( function() {
            dataTable.ajax.reload();
        });

        ATTACH_DATATABLE_BAR_LOADER.init('communication-preference-table');
    }
</script>
<div data-file-info="ajax_ui_rg_history.php" data-datatable-selector="#responsible-gaming-table">
    <div class="form-inline">
        <input type="text" id="reportrange" class="form-control input-sm dateInput inline" data-start="#dateRangeValueStart" data-end="#dateRangeValueEnd" data-time="true"/>
        <input type="hidden" id="dateRangeValueStart" name="dateRangeValueStart"/>
        <input type="hidden" id="dateRangeValueEnd" name="dateRangeValueEnd"/>
        <input type="button" class="btn btn-portage btn-sm" id="btn-submit" value="<?=lang('lang.search');?>"/>
    </div>
    <hr/>
    <div class="clearfix">
        <table id="responsible-gaming-table" class="table table-bordered">
            <thead>
                <tr>
                    <th><?=lang('column.id');?></th>
                    <th><?=lang('Date of Request');?></th>
                    <th><?=lang('Start Date');?></th>
                    <th><?=lang('End Date');?></th>
                    <th><?=lang('Control');?></th>
                    <th><?=lang('User');?></th>
                    <th><?=lang('Status');?></th>
                    <th><?=lang('Updated');?></th>
                    <th><?=lang('Notes');?></th>
                    <th><?=lang('Action Player');?></th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<script type="text/javascript">
    function rgHistory(){
        var dataTable = $('#responsible-gaming-table').DataTable({
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

                $.post('/api/rg_history/' + playerId, data, function(data) {
                    callback(data);
                },'json');
            }
        });

        $('#changeable_table #btn-submit').click( function() {
            dataTable.ajax.reload();
        });

        ATTACH_DATATABLE_BAR_LOADER.init('responsible-gaming-table');
    }
</script>
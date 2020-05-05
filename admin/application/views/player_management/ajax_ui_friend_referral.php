<div data-file-info="ajax_ui_friend_referral.php" data-datatable-selector="#friendreferral-table">
    <div class="form-inline">
        <input type="text" id="reportrange" class="form-control input-sm dateInput inline" data-start="#dateRangeValueStart" data-end="#dateRangeValueEnd" data-time="true"/>
        <input type="hidden" id="dateRangeValueStart" name="dateRangeValueStart"/>
        <input type="hidden" id="dateRangeValueEnd" name="dateRangeValueEnd"/>
        <input type="button" class="btn btn-portage btn-sm" id="btn-submit" value="<?=lang('lang.search');?>"/>
    </div>
    <hr/>
    <div class="clearfix">
        <table id="friendreferral-table" class="table table-bordered">
            <thead>
                <th><?=lang('player.ufr02');?></th>
                <th><?=lang('player.ufr01');?></th>
                <th><?=lang('player.ui31');?></th>
                <th><?=lang('player.ufr03');?></th>
            </thead>
        </table>
    </div>
</div>


<script type="text/javascript">
    function friendReferralStatus() {
        var dataTable = $('#friendreferral-table').DataTable({
            dom: "<'row'<'col-md-12'<'pull-right'f><'pull-right progress-container'>l<'dt-information-summary2 text-info pull-left' i>>><'table-responsive't><'row'<'col-md-12'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>>",
            order: [ 0, 'desc' ],
            columnDefs: [
                { type: 'num', className: 'text-right', targets: [ 2, 3 ] }
            ],
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

                $.post(base_url + 'api/friend_referral_status/' + playerId, data, function(data) {
                    callback(data);
                },'json');
            }
        });

        $('#changeable_table #btn-submit').click( function() {
            dataTable.ajax.reload();
        });

        ATTACH_DATATABLE_BAR_LOADER.init('friendreferral-table');
    }
</script>
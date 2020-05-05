<div data-file-info="ajax_ui_cancelled_transfer_condition.php" data-datatable-selector="#cancelled-transfer-condition-table">
    <div class="form-inline">
        <?php if($this->permissions->checkPermissions('cancel_member_transfer_condition')): ?>
            <button type="button" value="" title="<?=lang('recover.tc')?>" id="cancelTcItems" class="btn btn-scooter btn-sm">
                <i class="glyphicon glyphicon-repeat" data-placement="bottom" ></i>
                <?=lang('recover.tc');?>
            </button>
        <?php endif; ?>

        <input type="text" id="reportrange" class="form-control input-sm dateInput inline" data-start="#dateRangeValueStart" data-end="#dateRangeValueEnd" data-time="true"/>
        <input type="hidden" id="dateRangeValueStart" name="dateRangeValueStart"/>
        <input type="hidden" id="dateRangeValueEnd" name="dateRangeValueEnd"/>
        <input type="button" class="btn btn-portage btn-sm" id="btn-submit" value="<?=lang('lang.search');?>"/>
    </div>
    <hr/>
    <div class="clearfix">
        <table id="cancelled-transfer-condition-table" class="table table-bordered">
            <thead>
                <th><?=lang('lang.action')?></th>
                <th><?=lang('pay.promoname');?></th>
                <th><?=lang('Bonus Amount');?></th>
                <th><?=lang('pay.startedAt');?></th>
                <th><?=lang('pay.transferConditionBetAmount');?></th>
                <th><?=lang('Betting Amount');?></th>
                <th><?=lang('Updated at');?></th>
                <th><?=lang('sys.updatedby');?></th>
                <th><?=lang('Note');?></th>
                <th><?=lang('Detailed Status');?></th>
            </thead>
        </table>
    </div>
</div>

<script type="text/javascript">
    var title = '<?= lang('recover.tc') ?>';
    var checkedTcRows = [];

    function cancelledTransferCondition() {
        var dataTable = $('#cancelled-transfer-condition-table').DataTable({
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
                {
                    "targets": 0,
                    "orderable": false
                }
            ],
            order: [
                [3, 'desc']
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

                $.post('/api/cancelled_transfer_condition/' + playerId, data, function(data) {
                    callback(data);
                },'json');
            },
            drawCallback : function() {
                $(".chk-tc-item").on('click', function(){
                    var id = $(this).val();
                    if( $(this).is(":checked") ) {
                        checkedTcRows.push(id);
                    } else {
                        var index = checkedTcRows.indexOf(id);
                        if (index > -1) {
                            checkedTcRows.splice(index, 1);
                        }
                    }
                });
            }
        });

        $('#changeable_table #btn-submit').click( function() {
            dataTable.ajax.reload();
        });

        ATTACH_DATATABLE_BAR_LOADER.init('cancelled-transfer-condition-table');

        $('#cancelTcItems').on('click', function() {
            if(checkedTcRows.length > 0) {
                var content = '<?= lang('conf.recover.tc'); ?>';
                var button = '';
                button += '<button class="btn btn-sm btn-linkwater" data-dismiss="modal" aria-label="Close"><?=lang('lang.no')?></button>'
                button += '<button class="btn btn-sm btn-scooter" id="recoverTcBtn" onclick="batch_recover_transfer_condition();"><?=lang('lang.yes')?></button>';

                confirm_modal(title, content, button);
            } else {
                var content = '<?=lang("no.selected.cancelled.tc")?>';
                error_modal(title, content);
            }
        });
    }

    function batch_recover_transfer_condition() {
        $('#recoverTcBtn').attr("disabled", true);
        $.post('/player_management/batchRecoverTransferCondition', {transfer_condition_ids : checkedTcRows}, function(){
            success_modal(title, '<?= lang('tc.success.recovered'); ?>');
            $('#cancelled-transfer-condition-table').DataTable().ajax.reload();
        });
    }
</script>
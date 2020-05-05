<div data-file-info="ajax_ui_cancelled_withdrawal.php" data-datatable-selector="#cancelledwithdrawal-table">
    <div class="form-inline ">
        <?php if ($this->permissions->checkPermissions('recover_withdraw_condition')) : ?>
            <button type="button" value="" title="<?=lang('recover.wc')?>" id="cancelWcItems" class="btn btn-scooter btn-sm">
                <i class="glyphicon glyphicon-repeat" data-placement="bottom" ></i>
                <?=lang('recover.wc');?>
            </button>
        <?php endif; ?>

        <input type="text" id="reportrange" class="form-control input-sm dateInput inline" data-start="#dateRangeValueStart" data-end="#dateRangeValueEnd" data-time="true"/>
        <input type="hidden" id="dateRangeValueStart" name="dateRangeValueStart"/>
        <input type="hidden" id="dateRangeValueEnd" name="dateRangeValueEnd"/>
        <input type="button" class="btn btn-portage btn-sm" id="btn-submit" value="<?=lang('lang.search');?>"/>
    </div>
    <hr/>
    <div class="clearfix">
        <table id="cancelledwithdrawal-table" class="table table-bordered">
            <thead>
                <th><?=lang('lang.action')?></th>
                <th><?=lang('pay.transactionType');?></th>
                <th><?=lang('pay.promoname');?></th>
                <th><?=lang('cms.promocode');?></th>
                <th><?=lang('report.p20');?></th>
                <th><?=lang('Bonus');?></th>
                <th><?=lang('pay.startedAt');?></th>
                <th><?=lang('pay.withdrawalAmountCondition');?></th>
                <th><?=lang('Note');?></th>
                <th><?=lang('Betting Amount');?></th>
                <th><?=lang('Updated at');?></th>
                <th><?=lang('sys.updatedby');?></th>
                <th><?=lang('Detail Status');?></th>
            </thead>
        </table>
    </div>
</div>

<script type="text/javascript">
    var title = '<?= lang('recover.wc') ?>';
    var checkedWcRows = [];
    function cancelledWithdrawalCondition() {
        var dataTable = $('#cancelledwithdrawal-table').DataTable({
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
                [6, 'desc']
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

                $.post('/api/cancelled_withdrawal/' + playerId, data, function(data) {
                    callback(data);
                },'json');
            },
            drawCallback : function() {
                $(".chk-wc-item").on('click', function(){
                    var id = $(this).val();
                    if( $(this).is(":checked") ) {
                        checkedWcRows.push(id);
                    } else {
                        var index = checkedWcRows.indexOf(id);
                        if (index > -1) {
                            checkedWcRows.splice(index, 1);
                        }
                    }
                });
            }
        });

        $('#changeable_table #btn-submit').click( function() {
            dataTable.ajax.reload();
        });

        ATTACH_DATATABLE_BAR_LOADER.init('cancelledwithdrawal-table');

        $('#cancelWcItems').on('click', function() {
            if(checkedWcRows.length > 0) {
                var content = '<input type="hidden" id="hiddenId"><?= lang('conf.recover.wc'); ?>';
                var button = '';
                button += '<button class="btn btn-sm btn-linkwater" data-dismiss="modal" aria-label="Close"><?=lang('lang.no')?></button>'
                button += '<button class="btn btn-sm btn-scooter" id="recoverBtn" onclick="batch_recover_withdraw_condition();"><?=lang('lang.yes')?></button>';

                confirm_modal(title, content, button);
            } else {
                var content = '<?=lang("no.selected.cancelled.wc")?>';
                error_modal(title, content);
            }
        });
    }

    function batch_recover_withdraw_condition() {
        $('#recoverBtn').attr("disabled", true);
        $.post('/payment_management/batchRecoverWithdrawCondition', {withdraw_condition_ids : checkedWcRows}, function(){
            success_modal(title, '<?= lang('wc.success.recovered'); ?>');
            $('#cancelledwithdrawal-table').DataTable().ajax.reload();
        });
    }
</script>

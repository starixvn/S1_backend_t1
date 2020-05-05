<div id="withdrawal_list_panel" data-file-info="ajax_ui_withdraw.php" data-datatable-selector="#withdraw-table">
    <div class="form-inline">
        <?php if ($this->permissions->checkPermissions('new_withdrawal')): ?>
            <a href="/payment_management/newWithdrawal" class="btn btn-scooter btn-sm btn-new_withdrawal" target="_blank">
                <i class="fa fa-plus"></i><span class="hidden-xs"><?=lang('lang.newWithdrawal') ?></span>
            </a>
        <?php endif; ?>
        <select id="withdraw-type" class="form-control input-sm">
            <option value="" selected><?=lang("All") ?></option>
            <option value="request"><?=lang('st.pending'); ?></option>
            <?php if ($this->utils->isEnabledFeature('separate_approve_decline_withdraw_pending_review_and_request_permission')): ?>
                <option value="pending_review"><?=lang('st.pendingreview'); ?></option>
            <?php endif; ?>
            <?php if (is_array($customStage)): ?>
                <?php foreach ($customStage as $key => $value):?>
                    <option value="<?=lang($key); ?>"><?=lang($value); ?></option>
                <?php endforeach; ?>
            <?php endif;?>
            <option value="payProc"><?=lang('st.processing'); ?></option>
            <option value="paid"><?=lang('st.paid'); ?></option>
            <option value="declined"><?=lang('st.declined'); ?></option>
            <option value="lock_api_unknown"><?=lang('st.lockedapirequest'); ?></option>
        </select>

        <input type="text" id="reportrange" class="form-control input-sm dateInput inline" data-start="#dateRangeValueStart" data-end="#dateRangeValueEnd" data-time="true" />
        <input type="hidden" id="dateRangeValueStart" name="dateRangeValueStart" />
        <input type="hidden" id="dateRangeValueEnd" name="dateRangeValueEnd" />
        <input type="button" class="btn btn-portage btn-sm" id="btn-submit" value="<?=lang('lang.search'); ?>" />
    </div>
    <hr />
    <div class="clearfix">
        <table id="withdraw-table" class="table table-bordered">
            <thead>
                <tr>
                    <th><?=lang('lang.status') ?></th>
                    <th><?=lang("Withdraw Code") ?></th>
                    <th><?=lang('Locked Status') ?></th>
                    <th><?=lang("pay.username") ?></th>
                    <th style="min-width:45px;"><?=lang("pay.reqtime") ?></th>
                    <th style="min-width:45px;"><?=lang("pay.proctime") ?></th>
                    <th style="min-width:45px;"><?=lang("pay.paidtime") ?></th>
                    <th style="min-width:45px;"><?=lang("pay.spenttime") ?></th>
                    <th><?=lang("pay.realname") ?></th>
                    <th><?=lang('pay.playerlev') ?></th>
                    <th><?=lang("Tag") ?></th>
                    <th><?=lang('pay.withamt') ?></th>
                    <th><?=lang('pay.bankname') ?></th>
                    <th><?=lang('pay.acctname') ?></th>
                    <th><?=lang('pay.acctnumber') ?></th>
                    <th><?=lang('pay.payment_account_flag') ?></th>
                    <th><?=lang('pay.acctbranch') ?></th>
                    <th><?=lang('Withdrawal Declined Category') ?></th>
                    <th><?=lang('Province') ?></th>
                    <th><?=lang('City') ?></th>
                    <th><?=lang('pay.withip') ?></th>
                    <th><?=lang('pay.withlocation') ?></th>
                    <th><?=lang('pay.procssby') ?></th>
                    <th><?=lang('pay.updatedon') ?></th>
                    <th><?=lang("pay.withdrawalId") ?></th>
                    <th style="min-width:400px;"><?=lang('External Note'); ?></th>
                    <th style="min-width:400px;"><?=lang('Internal Note'); ?></th>
                    <th style="min-width:600px;"><?=lang('Action Log'); ?></th>
                    <th style="min-width:300px;"><?=lang('pay.timelog') ?></th>
                    <th><?=lang('pay.curr') ?></th>
                    <th><?=lang('sys.ga.systemcode') ?></th>
                    <th><?=lang('lang.withdrawal_payment_api') ?></th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<script type="text/javascript">
    function withdrawHistory() {
        <?php if (!empty($this->utils->getConfig('withdrawal_list_columnDefs'))): ?>
            <?php if (!empty($this->utils->getConfig('withdrawal_list_columnDefs')['not_visible_player_information'])): ?>
                var visible_target = JSON.parse("<?= json_encode($this->utils->getConfig('withdrawal_list_columnDefs')['not_visible_player_information'], true) ?>");
            <?php else : ?>
                var visible_target = [2, 3, 8, 9, 10, 18, 19, 20, 21, 24, 29, 30, 31];
            <?php endif; ?>
            <?php if (!empty($this->utils->getConfig('withdrawal_list_columnDefs')['className_text-right_player_information'])): ?>
                var text_right = JSON.parse("<?= json_encode($this->utils->getConfig('withdrawal_list_columnDefs')['className_text-right_player_information'], true) ?>");
            <?php else : ?>
                var text_right = [7, 11, 28];
            <?php endif; ?>
        <?php else : ?>
            var visible_target = [6, 10, 11, 13, 14, 18, 21, 23, 24];
            var text_right = [5];
        <?php endif; ?>

        var amtColSummary = 0,
            totalPerPage = 0;

        var dataTable = $('#withdraw-table').DataTable({
            dom: "<'row'<'col-md-12'<'pull-right'B><'pull-right progress-container'>l<'dt-information-summary2 text-info pull-left' i>>><'table-responsive't><'row'<'col-md-12'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>>",
            autoWidth: false,
            searching: false,
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: ['colvisRestore'],
                    className: ['btn-linkwater']
                }
            ],
            columnDefs: [
                {
                    sortable: false,
                    targets: [0]
                },
                {
                    visible: false,
                    targets: visible_target
                },
                {
                    className: 'text-right',
                    targets: text_right
                }
            ],
            order: [
                [5, 'desc']
            ],
            processing: true,
            serverSide: true,
            ajax: function(data, callback, settings) {
                data.extra_search = [
                    {
                        'name': 'withdrawal_date_from',
                        'value': $('#withdrawal_list_panel #dateRangeValueStart').val(),
                    },
                    {
                        'name': 'withdrawal_date_to',
                        'value': $('#withdrawal_list_panel #dateRangeValueEnd').val(),
                    },
                    {
                        'name': 'dwStatus',
                        'value': $('#withdrawal_list_panel #withdraw-type').val(),
                    },
                    {
                        'name': 'enable_date',
                        'value': true,
                    },
                ];

                $.post('/api/withdrawList/' + playerId + '/false', data, function(data) {
                    callback(data);
                }, 'json');
            },
        });

        $('#changeable_table #btn-submit').click(function() {
            dataTable.ajax.reload();
        });

        ATTACH_DATATABLE_BAR_LOADER.init('withdraw-table');
    }

    function showDetialNotes(walletAccountId, noteType) {
        $.ajax({
            url: '/payment_management/getWithdrawalDetialNotes/' + walletAccountId + '/' + noteType,
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    var title   = 'NO.' + data.transactionCode,
                        content = '<div>' + data.noteSubTitle + '</div><br>' +
                                  '<textarea class="form-control" rows="15" readonly style="resize: none;">' + data.formatNotes.trim() + '</textarea>',
                        button  = '<center><button class="btn btn-sm btn-scooter" data-dismiss="modal" aria-label="Close"><?=lang('Close')?></button></center>';

                    confirm_modal(title, content, button);
                } else {
                    alert('<?=lang("Something is wrong, show notes detail failed"); ?>');
                }
            },
        });
    }
</script>

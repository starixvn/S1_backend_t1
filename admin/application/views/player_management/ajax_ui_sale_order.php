<div id="deposit_list_panel" data-file-info="ajax_ui_sale_order.php" data-datatable-selector="#deposit-table">
    <div class="form-inline">
        <?php if ($this->permissions->checkPermissions('new_deposit')): ?>
            <a href="/payment_management/newDeposit" class="btn btn-scooter btn-sm btn-new_deposit" target="_blank">
                <i class="fa fa-plus"></i> <span class="hidden-xs"><?=lang('lang.newDeposit') ?></span>
            </a>
        <?php endif; ?>

        <select id="deposit-type" class="form-control input-sm">
            <option value=""><?=lang("All") ?></option>
            <option value="<?= Sale_order::VIEW_STATUS_REQUEST ?>"><?=lang('deposit_list.st.pending'); ?></option>
            <option selected="selected" value="<?= Sale_order::VIEW_STATUS_APPROVED ?>"><?=lang('deposit_list.st.approved'); ?></option>
            <option value="<?= Sale_order::VIEW_STATUS_DECLINED ?>"><?=lang('deposit_list.st.declined'); ?></option>
        </select>

        <input type="text" id="reportrange" class="form-control input-sm dateInput inline" data-start="#dateRangeValueStart" data-end="#dateRangeValueEnd" data-time="true" />
        <input type="hidden" id="dateRangeValueStart" name="dateRangeValueStart" />
        <input type="hidden" id="dateRangeValueEnd" name="dateRangeValueEnd" />
        <input type="button" class="btn btn-portage btn-sm" id="btn-submit" value="<?=lang('lang.search'); ?>" />
    </div>
    <hr />
    <div class="clearfix">
        <table id="deposit-table" class="table table-bordered">
            <thead>
                <tr>
                    <th><?=lang('lang.action'); ?></th>
                    <th><?=lang('lang.status'); ?></th>
                    <th><?=lang('deposit_list.order_id'); ?></th>
                    <th><?=lang('system.word38'); ?></th>
                    <th><?=lang('pay.payment_account_flag'); ?></th>
                    <th><?=lang('pay.reqtime'); ?></th>
                    <th><?=lang('Deposit Datetime'); ?></th>
                    <th><?=lang('system.word39'); ?></th>
                    <th><?=lang("Affiliate") ?></th>
                    <th><?=lang('pay.playerlev'); ?></th>
                    <th><?=lang('Deposit Amount'); ?></th>
                    <th><?=lang('pay.collection_name'); ?></th>
                    <th><?=lang('pay.ip') . ' ' . lang('pay.locatn'); ?></th>
                    <th><?=lang('pay.updatedon'); ?></th>
                    <th><?=lang('cms.timeoutAt'); ?></th>
                    <th><?=lang('pay.procsson'); ?></th>
                    <th><?=lang('pay.collection_account_name'); ?></th>
                    <th><?=lang('con.bnk20'); ?></th>
                    <th><?=lang('pay.deposit_payment_name'); ?></th>
                    <th><?=lang('pay.deposit_payment_account_name'); ?></th>
                    <th><?=lang('pay.deposit_payment_account_number'); ?></th>
                    <th><?=lang('pay.deposit_transaction_code'); ?></th>
                    <th><?=lang('pay.promoname'); ?></th>
                    <th><?=lang('pay.promobonus'); ?></th>
                    <th><?=lang('External ID'); ?></th>
                    <th><?=lang('Bank Order ID'); ?></th>
                    <th><?=lang('Deposit Datetime From Player'); ?></th>
                    <th><?=lang('Mode of Deposit'); ?></th>
                    <th style="min-width:300px;"><?=lang('Player Deposit Note'); ?></th>
                    <th style="min-width:400px;"><?=lang('pay.procssby'); ?></th>
                    <th style="min-width:400px;"><?=lang('External Note'); ?></th>
                    <th style="min-width:400px;"><?=lang('Internal Note'); ?></th>
                    <th style="min-width:600px;"><?=lang('Action Log'); ?></th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<script type="text/javascript">
    function depositHistory() {
        <?php if (!empty($this->utils->getConfig('deposit_list_columnDefs'))) : ?>
            <?php if (!empty($this->utils->getConfig('deposit_list_columnDefs')['not_visible_player_information'])) : ?>
                var visible_target = JSON.parse("<?= json_encode($this->utils->getConfig('deposit_list_columnDefs')['not_visible_player_information']) ?>");
            <?php else : ?>
                var visible_target = [0, 10, 11, 12];
            <?php endif; ?>
            <?php if (!empty($this->utils->getConfig('deposit_list_columnDefs')['className_text-right_player_information'])) : ?>
                var text_right = JSON.parse("<?= json_encode($this->utils->getConfig('deposit_list_columnDefs')['className_text-right_player_information']) ?>");
            <?php else : ?>
                var text_right = [4, 5, 6, 9];
            <?php endif; ?>
        <?php else : ?>
            var visible_target = [0, 10, 11, 12];
            var text_right = [4, 5, 6, 9];
        <?php endif; ?>

        var amtColSummary = 0,
            totalPerPage = 0;

        var dataTable = $('#deposit-table').DataTable({
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
                },
                <?php if (!$this->utils->isEnabledFeature('enable_deposit_datetime')): ?>
                    {
                        className: "noVis hidden",
                        targets: [26],
                    },
                    {
                        className: "noVis hidden",
                        targets: [27],
                    },
                <?php endif; ?>
                <?php if ($this->utils->isEnabledFeature('close_aff_and_agent')): ?>
                    {
                        className: "noVis hidden",
                        targets: [8],
                    },
                <?php endif ?>
            ],
            order: [
                [0, 'desc']
            ],
            processing: true,
            serverSide: true,
            ajax: function(data, callback, settings) {
                switch ($('#deposit-type').val()) {
                    case '':
                    case '<?= Sale_order::VIEW_STATUS_REQUEST ?>':
                    case '<?= Sale_order::VIEW_STATUS_APPROVED ?>':
                        data.order = [{
                            'column': 5,
                            'dir': 'desc'
                        }];
                        break;

                    case '<?= Sale_order::VIEW_STATUS_DECLINED ?>':
                        data.order = [{
                            'column': 13,
                            'dir': 'desc'
                        }];
                        break;
                }
                data.extra_search = [
                    {
                        'name': 'enable_date',
                        'value': '1'
                    },
                    {
                        'name': 'deposit_date_from',
                        'value': $('#deposit_list_panel #dateRangeValueStart').val()
                    },
                    {
                        'name': 'deposit_date_to',
                        'value': $('#deposit_list_panel #dateRangeValueEnd').val()
                    },
                    {
                        'name': 'dwStatus',
                        'value': $('#deposit_list_panel #deposit-type').val()
                    },
                    {
                        'name': 'payment_account_id_all',
                        'value': 'true',
                    }
                ];

                $.post('/api/depositList/' + playerId, data, function(data) {
                    callback(data);
                }, 'json');
            },
        });

        $('#changeable_table #btn-submit').click(function() {
            dataTable.ajax.reload();
        });

        ATTACH_DATATABLE_BAR_LOADER.init('deposit-table');
    }

    function showDetialNotes(saleOrderId, noteType) {
        $.ajax({
            url: '/payment_management/getDepositDetialNotes/' + saleOrderId + '/' + noteType,
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    var title   = 'NO.' + data.secure_id,
                        content = '<div>' + data.noteSubTitle + '</div><br>' +
                                  '<textarea class="form-control" rows="15" readonly style="resize: none;">' + data.formatNotes.trim() + '</textarea>',
                        button  = '<center><button class="btn btn-sm btn-scooter" data-dismiss="modal" aria-label="Close"><?=lang('Close')?></button></center>';

                    confirm_modal(title, content, button);
                } else {
                    alert('<?= lang("Something is wrong, show notes detail failed") ?>');
                }
            },
        });
    }
</script>

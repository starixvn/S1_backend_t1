<div id="transfer_list_panel" data-file-info="ajax_ui_transfer.php" data-datatable-selector="#transfer-table">
    <div class="row">
        <div class="form-group col-md-3">
            <label class="control-label" for="search_date"><?=lang('pay.transperd') ?></label>
            <div class="input-group">
                <input id="search_date" type="text" class="form-control input-sm dateInput" data-start="#date_from" data-end="#date_to" data-time="true" />
                <input type="hidden" id="date_from" name="date_from" />
                <input type="hidden" id="date_to" name="date_to" />
                <div class="input-group-addon input-sm">
                    <input type="checkbox" checked="checked" name="search_reg_date" id="search_reg_date" class="user-success">
                </div>
            </div>
        </div>
        <div class="form-group col-md-2">
            <label><?=lang('Status') ?></label>
            <select id="transfer-type" class="form-control input-sm">
                <option value=""><?=lang('All'); ?></option>
                <option value="<?= Wallet_model::STATUS_TRANSFER_REQUEST ?>"><?=lang('Transfer Request'); ?></option>
                <option value="<?= Wallet_model::STATUS_TRANSFER_SUCCESS ?>"><?=lang('Transfer Success'); ?></option>
                <option value="<?= Wallet_model::STATUS_TRANSFER_FAILED ?>"><?=lang('Transfer Failed'); ?></option>
            </select>
        </div>
        <div class="form-group col-md-2">
            <label for="by_game_platform_id"><?=lang('Game Platform') ?></label>
            <select id="by_game_platform_id" name="by_game_platform_id" class="form-control input-sm">
                <option value=""><?=lang('All'); ?></option>
                <?php foreach ($game_platforms as $game_platform): ?>
                    <option value="<?= $game_platform['id'] ?>"><?= $game_platform['system_code']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group col-md-2">
            <label class="control-label" for="secure_id"><?=lang('ID') ?></label>
            <input id="secure_id" type="text" class="form-control input-sm inline" />
        </div>
        <div class="form-group col-md-2">
            <label class="control-label" for="suspicious_trans"><?=lang('Suspicious Trans') ?></label>
            <select id="suspicious_trans" name="suspicious_trans" class="form-control input-sm">
                <option value=""><?=lang('None'); ?></option>
                <option value="<?= Wallet_model::SUSPICIOUS_TRANSFER_IN_ONLY ?>"><?=lang('Transfer In Only'); ?></option>
                <option value="<?= Wallet_model::SUSPICIOUS_TRANSFER_OUT_ONLY ?>"><?=lang('Transfer Out Only'); ?></option>
                <option value="<?= Wallet_model::SUSPICIOUS_ALL ?>"><?=lang('All'); ?></option>
            </select>
        </div>
    </div>
    <div class="row text-right">
        <div class="col-md-offset-11 col-md-1 text-right">
            <input type="button" class="btn btn-portage btn-sm" id="btn-submit" value="<?=lang('lang.search'); ?>" />
        </div>
    </div>
    <hr />
    <div class="clearfix">
        <table id="transfer-table" class="table table-bordered">
            <thead>
                <tr>
                    <?php include __DIR__ . '/../includes/cols_for_transfer_list.php'; ?>
                </tr>
            </thead>
        </table>
    </div>
</div>

<script type="text/javascript">
    function transferHistory() {
        var amtColSummary = 0,
            totalPerPage = 0;

        var dataTable = $('#transfer-table').DataTable({
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
            columnDefs: [{
                    className: 'text-right',
                    targets: [5]
                }
            ],
            order: [
                [_sort_col_index_for_transfer_list, 'desc']
            ],
            processing: true,
            serverSide: true,
            ajax: function(data, callback, settings) {
                data.extra_search = [
                    {
                        'name': 'date_from',
                        'value': $('#transfer_list_panel #date_from').val()
                    },
                    {
                        'name': 'date_to',
                        'value': $('#transfer_list_panel #date_to').val()
                    },
                    {
                        'name': 'status',
                        'value': $('#transfer_list_panel #transfer-type').val()
                    },
                    {
                        'name': 'by_game_platform_id',
                        'value': $('#transfer_list_panel #by_game_platform_id').val()
                    },
                    {
                        'name': 'search_reg_date',
                        'value': $('#transfer_list_panel #search_reg_date').is(':checked') ? 'on' : ''
                    },
                    {
                        'name': 'secure_id',
                        'value': $('#secure_id').val()
                    },
                    {
                        'name': 'suspicious_trans',
                        'value': $('#suspicious_trans').val()
                    }
                ];

                $.post(base_url + 'api/transfer_request/' + playerId, data, function(data) {
                    callback(data);
                }, 'json');
            },
        });

        $('#transfer_list_panel #search_reg_date').change(function() {
            if (!$('#transfer_list_panel #search_reg_date').is(':checked')) {
                $('#search_date').prop("disabled", true);
            } else {
                $('#search_date').prop("disabled", false);
            }
        });

        $('#changeable_table #btn-submit').click(function() {
            dataTable.ajax.reload();
        });

        ATTACH_DATATABLE_BAR_LOADER.init('transfer-table');
    }
</script>
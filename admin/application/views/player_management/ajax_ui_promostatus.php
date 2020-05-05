<div data-file-info="ajax_ui_promostatus.php" data-datatable-selector="#promo-table">
    <div class="form-inline">
        <select id="playerPromoStatus" class="form-control input-sm" placeholder="<?=lang('Choose Status');?>">
            <option value="" selected > ---- <?=lang('select.all.status'); ?> ---- </option>
            <option value="<?= Player_promo::TRANS_STATUS_REQUEST ?>"><?=lang('promo.request_list.search_status.pending'); ?></option>
            <option value="<?= Player_promo::TRANS_STATUS_APPROVED ?>"><?=lang('promo.request_list.search_status.approved'); ?></option>
            <option value="<?= Player_promo::TRANS_STATUS_FINISHED_WITHDRAW_CONDITION ?>"><?=lang('promo.request_list.search_status.finished'); ?></option>
            <option value="<?= Player_promo::TRANS_STATUS_DECLINED ?>"><?=lang('promo.request_list.search_status.declined'); ?></option>
        </select>
        <?php if(!$this->utils->isEnabledFeature('hide_dates_filter_in_promo_history')): ?>
            <input type="text" id="reportrange" class="form-control input-sm dateInput inline" data-start="#dateRangeValueStart" data-end="#dateRangeValueEnd" data-time="true"/>
            <input type="hidden" id="dateRangeValueStart" name="dateRangeValueStart"/>
            <input type="hidden" id="dateRangeValueEnd" name="dateRangeValueEnd"/>
        <?php endif; ?>
        <select name="promoCmsSettingId" id="promoCmsSettingId" class="form-control input-sm">
            <option value=""><?=lang('All')?></option>
            <?php foreach ($promoList as $promo): ?>
                <option value="<?=$promo['promoCmsSettingId']?>"><?=$promo['promoName']?></option>
            <?php endforeach; ?>
        </select>
        <input type="button" class="btn btn-portage btn-sm" id="btn-promo-stat-search" value="<?=lang('lang.search');?>"/>
    </div>
    <hr>
    <div class="clearfix">
        <table id="promo-table" class="table table-bordered">
            <thead>
                <tr>
                    <th><?=lang('Date') ?></th>
                    <th><?=lang('column.id')?></th>
                    <th><?=lang('Type') ?></th>
                    <th><?=lang('Promo Name') ?></th>
                    <th><?=lang('Deposit Amount') ?></th>
                    <th><?=lang('Bonus Amount') ?></th>
                    <th><?=lang('Required Bet') ?></th>
                    <th><?=lang('Current Bet') ?></th>
                    <th><?=lang('Current Bet') . " / " . lang('Withdraw Condition amount') ?></th>
                    <th><?=lang('Status') ?></th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<script type="text/javascript">
    function promoStatus(player_id) {
        var promoStatusDataTable = $('#promo-table').DataTable({
            dom: "<'row'<'col-md-12'<'pull-right'f><'pull-right progress-container'>l<'dt-information-summary2 text-info pull-left' i>>><'table-responsive't><'row'<'col-md-12'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>>",
            order: [[0, 'desc']],
            autoWidth: false,
            columnDefs: [
                { visible: false, targets: [ 6 ] }
            ],
            processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {
                data.extra_search = [
                    {
                        'name':'promo_process_date_from',
                        'value':$('#dateRangeValueStart').val()
                    },
                    {
                        'name': 'promo_process_date_to',
                        'value': $('#dateRangeValueEnd').val()
                    },
                    {
                        'name': 'promoCmsSettingId',
                        'value': $('#promoCmsSettingId').val()
                    },
                    <?php if($this->utils->isEnabledFeature('hide_dates_filter_in_promo_history')): ?>
                        {
                            'name':'promo_process_date_to',
                            'value':$('#dateRangeValueEnd').val()
                        },
                    <?php endif; ?>
                    {
                        'name':'promo_status',
                        'value': $('#playerPromoStatus').val()
                    }
                ];

                $.post(base_url + 'api/promoStatus/' + player_id, data, function(data){
                    callback(data);
                },'json');
            }
        });

        $('#btn-promo-stat-search').click( function() {
            promoStatusDataTable.ajax.reload();
        });

        ATTACH_DATATABLE_BAR_LOADER.init('promo-table');
    }
</script>

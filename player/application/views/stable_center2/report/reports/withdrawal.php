<!-- ============ Withdrawal List ================== -->
<div id="ah-withdrawal" role="tabpanel" class="tab-pane">
    <div id="withdrawal-box" class="report table-responsive">
        <table id="withdrawalResultTable" width="100%" class="table table-striped table-hover dt-responsive display nowrap"></table>
    </div>
</div>
<script type="text/javascript">
    var withdrawalTB;

    function withdrawalHistory() {
        var table_container = $('#withdrawalResultTable');

        if(withdrawalTB !== undefined){
            withdrawalTB.page.len($('#pageLength').val());
            withdrawalTB.ajax.reload();
            return false;
        }

        var columns = [];

        columns.push({
            "name": "dwDateTime",
            "title": "<?=lang('pay.reqtime')?>",
            "data": 0,
            "visible": true,
            "orderable": true
        });
        columns.push({
            "name": "transactionCode",
            "title": "<?=lang('Withdrawal Code')?>",
            "data": 1,
            "visible": true,
            "orderable": false
        });
        columns.push({
            "name": "Status",
            "title": "<?=lang('Status')?>",
            "data": 2,
            "visible": true,
            "orderable": false
        });
        columns.push({
            "name": "amount",
            "title": "<?=lang('Amount')?>",
            "data": 3,
            "visible": true,
            "orderable": false
        });
        columns.push({
            "name": "Notes",
            "title": "<?=lang('Notes')?>",
            "data": 4,
            "visible": true,
            "orderable": false
            <?php if ($this->utils->isEnabledFeature('player_center_hide_time_in_remark')) : ?>
            ,"render": function ( data, type, row ) {
                return data.replace(/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]\s*/, '');
            }
            <?php endif; ?>
        });
        columns.push({ // for the responsive extention to display control row button
            "title": "&nbsp",
            "data": 1,
            "visible": true,
            "orderable": false,
            "render": function(){
                return '&nbsp';
            },
            "responsivePriority": 1
        });

        withdrawalTB = table_container.DataTable($.extend({}, dataTable_options, {
            "pageLength": $('#pageLength').val(),
            "columns": columns,
            columnDefs: [ {  // for the responsive extention to display control row button
                className: 'control',
                orderable: false,
                targets:   -1
            }],
            order: [[0, 'desc']],
            ajax: {
                url: '/api/WithdrawWalletTransaction',
                type: 'post',
                data: function ( d ) {
                    d.extra_search = [
                        {
                            'name':'dateRangeValueStart',
                            'value': $('#sdate').val(),
                        },
                        {
                            'name':'dateRangeValueEnd',
                            'value': $('#edate').val(),
                        },
                    ];
                },
            }
        }));
    }
</script>
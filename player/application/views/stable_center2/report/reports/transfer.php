<!-- ============ Transfer List ================== -->
<div id="ah-transfer" role="tabpanel" class="tab-pane">
    <div id="transfer-box" class="report table-responsive">
        <table id="transferResultTable" width="100%" class="table table-striped table-hover dt-responsive display nowrap"></table>
    </div>
</div>
<script type="text/javascript">
    var transferTB;

    function transferRequestHistory() {
        var table_container = $('#transferResultTable');

        if(transferTB !== undefined){
            transferTB.page.len($('#pageLength').val());
            transferTB.ajax.reload();
            return false;
        }

        var columns = [];
        columns.push({
            "title": "<?=lang('Transfer time')?>",
            "visible": true,
            "orderable": true,
        });
        columns.push({
            "title": "<?=lang('From')?>",
            "visible": true,
            "orderable": false,
        });
        columns.push({
            "title": "<?=lang('player.12')?>",
            "visible": true,
            "orderable": false,
        });
        columns.push({
            "title": "<?=lang('Amount')?>",
            "visible": true,
            "orderable": false,
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

        transferTB = table_container.DataTable($.extend({}, dataTable_options, {
            "autoWidth": false,
            "pageLength": $('#pageLength').val(),
            "columns": columns,
            columnDefs: [ {  // for the responsive extention to display control row button
                className: 'control',
                orderable: false,
                targets:   -1
            }],
            order: [[0, 'desc']],
            ajax: {
                url: '/api/TransferWalletTransaction',
                type: 'post',
                data: function ( d ) {
                    d.extra_search = [
                        {
                            'name':'dateRangeValueStart',
                            'value': $('#sdate').val(),
                        },
                        {
                            'name':'dateRangeValueEnd',
                            'value':  $('#edate').val(),
                        },
                    ];
                },
            }
        }));
    }
</script>
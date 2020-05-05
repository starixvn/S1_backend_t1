<style>
    .dataTables_wrapper { overflow-y: hidden; overflow-x: hidden; }
</style>

<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i> <?= lang('Search'); ?> <span class="pull-right">
                <a data-toggle="collapse" href="#collapseTaggedList" class="btn btn-info btn-xs"></a>
            </span>
        </h4>
    </div>

    <div id="collapseTaggedList" class="panel-collapse ">
        <form class="form-horizontal" id="search-form" method="get" role="form">
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-3">
                        <div class="form-group">
                            <label class="control-label col-xs-4" for=""><?= lang('Currency'); ?></label>
                            <div class="col-xs-7">
                                <select class="form-control input-sm" name="currency">
                                    <option value=""><?= lang('Select All'); ?></option>
                                    <?php if(!empty($currency_list_super_report)): ?>
                                        <?php foreach ($currency_list_super_report as $key => $value) :?>
                                            <option value="<?=$key?>"><?=$key?></option>
                                        <?php endforeach; ?>
                                    <?php endif ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="reportDate" class="control-label col-xs-4"><?= lang('aff.ap07'); ?></label>
                            <div class="col-xs-7">
                                <input id="reportDate" name="" class="form-control input-sm dateInput" data-start="#date_from" data-end="#date_to" data-time="true" autocomplete="off">
                                <input type="hidden" id="date_from" name="date_from">
                                <input type="hidden" id="date_to" name="date_to">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel-footer text-center">
                <input type="button" value="<?= lang('Reset') ?>" class="btn btn-default btn-sm" id="reset">
                <button type="submit" name="submit"  class="btn btn-primary btn-sm"><i class="fa"></i> <?= lang('Search'); ?></button>
            </div>
        </form>
    </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title">
            <i class="icon-pie-chart"></i>
            <?=lang('report.s08')?>
        </h4>
    </div>
    <div class="panel-body">
        <table class="table table-condensed table-bordered table-hover" id="reportTable" data-searching="false" data-ordering="true" data-page-length="10">
            <thead>
            <tr>
                <th><?= lang('Currency'); ?></th>
                <th><?= lang('report.sum02'); ?></th>
                <th><?= lang('report.sum05'); ?></th>
                <th><?= lang('report.sum06'); ?></th>
                <th><?= lang('report.sum07'); ?></th>
                <th><?= lang('report.sum08'); ?></th>
                <th><?= lang('report.sum09'); ?></th>
                <th><?= lang('report.sum10'); ?></th>
                <th><?= lang('report.sum14'); ?></th>
                <th><?= lang('report.sum15'); ?></th>
                <th><?= lang('report.sum16'); ?></th>
                <th><?= lang('report.sum18'); ?></th>
                <th><?= lang('Total Bet'); ?></th>
                <th><?= lang('Total Win'); ?></th>
                <th><?= lang('Total Loss'); ?></th>
                <th><?= lang('Gross Payout'); ?></th>
            </tr>
            </thead>
        </table>
    </div>
    <div class="panel-footer"></div>
</div>

<?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
<form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
    <input name='json_search' type="hidden">
<?php }?>

<script>

    var baseUrl = '<?php echo base_url(); ?>';
    var reportDate = $("#reportDate");

    $(document).ready(function(){

        setDateRange();

        var dataTable = $('#reportTable').DataTable({
            <?php if($this->utils->isEnabledFeature('column_visibility_report')){ ?>
                stateSave: true,
            <?php } else { ?>
                stateSave: false,
            <?php } ?>
            autoWidth: false,
            searching: false,
            dom: "<'panel-body' <'pull-right'B><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            columnDefs: [
                { className: 'text-right', targets: [4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15] },
            ],
            buttons: [
            {
                extend: 'colvis',
                postfixButtons: [ 'colvisRestore' ]
            }
            ],

            // SERVER-SIDE PROCESSING
            processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {
                data.extra_search = $('#search-form').serializeArray();
                $.post(base_url + "api/super_summary_report", data, function(data) {
                    callback(data);
                    $('#search-form').find(':submit').prop('disabled', false);
                    if ( dataTable.rows( { selected: true } ).indexes().length === 0 ) {
                        dataTable.buttons().disable();
                    }
                    else {
                        dataTable.buttons().enable();
                    }
                }, 'json');
            },
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ]
                }
                <?php if ($export_report_permission) {?>
                ,{
                    text: "<?php echo lang('CSV Export'); ?>",
                    className:'btn btn-primary disabled',
                    action: function ( e, dt, node, config ) {
                        var form_params=$('#search-form').serializeArray();
                        var d = {'extra_search': form_params, 'export_format': 'csv', 'export_type': export_type,'draw':1, 'length':-1, 'start':0};
                        utils.safelog(d);  
                        $("#_export_excel_queue_form").attr('action', site_url('/export_data/export_super_summary_report'));
                        $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
                        $("#_export_excel_queue_form").submit();
                        return false;
                    }
                }
                <?php }?>
            ],
        });


        $('#search-form').submit( function(e) {
            $(this).find(':submit').prop('disabled', true);
            e.preventDefault();
            dataTable.ajax.reload();
        });

    });

    function setDateRange() {
        var startDate =  reportDate.data('daterangepicker').startDate.format('YYYY-MM-DD');
        var endDate =  reportDate.data('daterangepicker').endDate.format('YYYY-MM-DD');
        $('#date_from').val(startDate);
        $('#date_to').val(endDate);
    }

</script>
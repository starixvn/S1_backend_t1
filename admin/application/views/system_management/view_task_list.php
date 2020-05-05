<style type="text/css">
#timezone, input, select{
    font-weight: bold;
}
#timezone{
    height: 36px;
}

.select2-container--default .select2-selection--single{
    padding-top: 2px;
    height: 35px;
    font-size: 1.2em;
    position: relative;
    border-radius: 0;
    font-size:12px;;
}
.removeLink {
    text-decoration: none;
    color : #222222;
}
.removeLink:hover {
    text-decoration:none;
    color : #222222;
    cursor:text;
}
</style>
<div class="panel panel-primary hidden">

    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i> <?=lang("lang.search")?>
            <span class="pull-right">
                <a data-toggle="collapse" href="#collapseGamesReport" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info'?> <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
            </span>
            <?php include __DIR__ . "/../includes/report_tools.php"?>
        </h4>
    </div>

    <div id="collapseGamesReport" class="panel-collapse <?=$this->config->item('default_open_search_panel') ? '' : 'collapse in'?>">
        <div class="panel-body">
            <form id="form-filter" class="form-horizontal" method="GET" onsubmit="return validateForm();">
                <div class="row">

                    <div class="col-md-4 col-lg-4">
                        <label class="control-label" for="group_by"><?=lang('Date')?> </label>
                        <input class="form-control dateInput" id="datetime_range" data-start="#datetime_from" data-end="#datetime_to" data-time="true"/>
                        <input type="hidden" id="datetime_from" name="datetime_from" value="<?=$conditions['datetime_from'];?>"/>
                        <input type="hidden" id="datetime_to" name="datetime_to" value="<?=$conditions['datetime_to'];?>"/>
                     </div>
                    <div class="col-md-2 col-lg-2">
                    </div>
                    <div class="col-md-3">
                    </div>
                    <div class="col-md-3">
                        <label class="control-label" for="username"><?=lang('Admin User')?> </label>
                        <input type="text" name="username" id="username" class="form-control input-sm"
                            value='<?php echo $conditions["username"]; ?>'/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 col-lg-4">
                        <button class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" style="margin-top:10px;"><?=lang('Search')?></button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="icon-dice"></i> <?=lang('View Task List')?>
        <span class="pull-right">
                <a  href="#" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info'?>" id="start-refresh-datatable" >Refresh every <span id="timerefresh">10</span> seconds</a>
                <a  href="#" style="display:none;" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-chestnutrose' : 'btn-danger'?>" id="stop-refresh-datatable" >Stop Refresh</a>
            </span>

         </h4>
    </div>
    <div class="panel-body table-responsive">
        <table class="table table-bordered table-hover" id="myTable">
            <thead>
                <tr>
                    <th><?=lang('Date')?></th>
                    <th><?=lang('ID')?></th>
                    <th><?=lang('Type')?></th>
                    <th><?=lang('Status')?></th>
                    <th><?=lang('Result')?></th>
                    <th><?=lang('Created At')?></th>
                    <th><?=lang('Admin User')?></th>
                    <th><?=lang('Player')?></th>
                    <th><?=lang('Language')?></th>
                </tr>
            </thead>
        </table>
    </div>
    <div class="panel-footer"></div>
</div>

<form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
<input name='json_search' type="hidden">
</form>

<script type="text/javascript">

    function validateForm(){
        return true;
    }


    $(document).ready(function(){
        $('#view_task_list').addClass('active');

        var dataTable = $('#myTable').DataTable({

            <?php if ($this->utils->isEnabledFeature('column_visibility_report')){ ?>
                stateSave: true,
            <?php } else { ?>
                stateSave: false,
            <?php } ?>

            dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            autoWidth: false,
            searching: false,
            //dom: "<'panel-body'l>t<'text-center'r><'panel-body'<'pull-right'p>i>",
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ],
                    className: '<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>',
                }
                <?php if ($export_report_permission) {?>
                ,{
                    text: "<?php echo lang('CSV Export'); ?>",
                    className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>',
                    action: function ( e, dt, node, config ) {
                        var form_params=$('#form-filter').serializeArray();
                        var d = {'extra_search': form_params, 'export_format': 'csv', 'export_type': 'queue',
                            'draw':1, 'length':-1, 'start':0};
                        // utils.safelog(d);

                        $("#_export_excel_queue_form").attr('action', site_url('/export_data/export_task_list'));
                        $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
                        $("#_export_excel_queue_form").submit();
                    }
                }
                <?php } ?>
            ],
            columnDefs: [
                // { className: 'text-right', targets: [ 5,6,7,8,9 ] },
            ],
            "order": [ 0, 'desc' ],

            // SERVER-SIDE PROCESSING
            processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {
                var formData = $('#form-filter').serializeArray();
                data.extra_search = formData;
                $.post(base_url + "api/task_list", data, function(data) {
                    callback(data);
                    if ( dataTable.rows( { selected: true } ).indexes().length === 0 ) {
                        dataTable.buttons().disable();
                    }
                    else {
                        dataTable.buttons().enable();
                    }
                }, 'json');
            }
        });


       var timeoutRefresh=<?php echo $this->utils->getConfig('timeout_refresh_queue');?>;
       var dataTableRefresh ='';

       function startRefreshDataTable() {
           dataTable.ajax.reload();
        }

        function stopRefreshDataTable() {
            clearInterval(dataTableRefresh);
        }

        function  stopQueue(token){
        $.post(stopQueueUrl, function(data){
            utils.safelog(data);
            refreshResult();
        });
    }

        $('#timerefresh').html(timeoutRefresh);

        $('#start-refresh-datatable').click(function(){
             dataTableRefresh = setInterval(startRefreshDataTable, 1000 * timeoutRefresh);
             $(this).hide();
             $('#stop-refresh-datatable').show();
             return false;
        });

       $('#stop-refresh-datatable').click(function(){
             stopRefreshDataTable();
             $(this).hide();
             $('#start-refresh-datatable').show();
             return false;
        });

       $('#myTable').delegate(".queue-token", "click", function(){
            var queueToken = $(this).attr('queue-token');
           if(confirm("<?=lang('Are you sure you want to stop this job')?>? \n" + queueToken)){
                $(this).attr("disabled", "disabled");
                var stopQueueUrl = "<?php echo site_url('export_data/stop_queue');?>/"+queueToken+"";
                $.post(stopQueueUrl, function(data){
                   utils.safelog(data);
                   setTimeout(startRefreshDataTable, 1000 * 7);
                });
            }
       });

       $('body').tooltip({
                selector : '[data-toggle="tooltip"]',
                placement : "bottom"
            });


    });//doc ready


</script>

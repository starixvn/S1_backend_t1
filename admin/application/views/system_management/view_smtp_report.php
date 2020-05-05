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
        font-size:12px;
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
                        <label class="control-label" for="email"><?=lang('Email Address')?> </label>
                        <input type="text" name="email" id="email" class="form-control input-sm"
                            value='<?php echo $conditions["email"]; ?>'/>
                    </div>
                    <div class="col-md-2 col-lg-2">
                        <label class="control-label" for="username"><?=lang('Username')?> </label>
                        <input type="text" name="username" id="username" class="form-control input-sm"
                            value='<?php echo $conditions["username"]; ?>'/>
                    </div>
                    <div class="col-md-2 col-lg-2">
                        <label class="control-label" for="flag"><?=lang('Flag')?> </label>
                        <select name="flag" id="flag" class="form-control input-sm">
                            <option value=""><?=lang('All')?></option>
                            <option value="1" <?=$conditions['flag']==1 ? 'selected="selected"' : ''?>>Normal</option>
                            <option value="2" <?=$conditions['flag']==2 ? 'selected="selected"' : ''?>>Error</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-lg-2">
                        <label class="control-label" style="margin-top: 11px;"></label><br>
                        <button class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>"><?=lang('Search')?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="icon-dice"></i> <?=lang('View SMTP API Report')?> </h4>
    </div>
    <div class="panel-body">
        <table class="table table-bordered table-hover" id="myTable">
            <thead>
                <tr>
                    <th><?=lang('Date')?></th>
                    <th><?=lang('ID')?></th>
                    <th><?=lang('API')?></th>
                    <th><?=lang('Username')?></th>
                    <th><?=lang('Email Address')?></th>
                    <th><?=lang('Status Code')?></th>
                    <th><?=lang('Status Text')?></th>
                    <th><?=lang('Flag')?></th>
                    <th><?=lang('Content')?></th>
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
                //data.is_export = true;
                $.post(base_url + "api/smtp_report_list", data, function(data) {
                    callback(data);
                }, 'json');
            }
        });

    });
</script>

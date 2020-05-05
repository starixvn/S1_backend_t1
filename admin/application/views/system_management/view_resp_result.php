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
<style type="text/css">
    .resendFlag {
        margin: auto 3px;
    }
  /* Tooltip */
  .resendFlag + .tooltip > .tooltip-inner {
    background-color: #607bae;
    color: #efeff2;
    border-radius: 5px;
  }
  /* Tooltip on right */
  .resendFlag + .tooltip.right > .tooltip-arrow {
    border-right: 5px solid black;
  }
  </style>
<?php include $this->utils->getIncludeView('show_response_result.php');?>
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
                    <div class="col-md-6 col-lg-6">
                        <label class="control-label" for="group_by"><?=lang('Date')?> </label>
                        <input class="form-control dateInput" id="datetime_range" data-start="#datetime_from" data-end="#datetime_to" data-time="true"/>
                        <input type="hidden" id="datetime_from" name="datetime_from" value="<?=$conditions['datetime_from'];?>"/>
                        <input type="hidden" id="datetime_to" name="datetime_to" value="<?=$conditions['datetime_to'];?>"/>
                     </div>
                    <div class="col-md-2 col-lg-2">
                        <label class="control-label" for="order_id"><?=lang('Order ID')?> </label>
                        <input type="text" name="order_id" id="order_id" class="form-control input-sm"
                            value='<?php echo $conditions["order_id"]; ?>'/>
                    </div>
                    <div class="col-md-2 col-lg-2">
                        <label class="control-label" for="result_id"><?=lang('Result ID')?> </label>
                        <input type="text" name="result_id" id="result_id" class="form-control input-sm"
                            value='<?php echo $conditions["result_id"]; ?>'/>
                    </div>
                    <div class="col-md-2 col-lg-2">
                        <label class="control-label" for="mobile"><?=lang('Mobile Number')?> </label>
                        <input type="text" name="mobile" id="mobile" class="form-control input-sm"
                            value='<?php echo $conditions["mobile"]; ?>'/>
                    </div>
                </div>
                <div class="row">
                    <input type="hidden" id="api_id" name="api_id" value='<?php echo $conditions["api_id"]; ?>'/>
                    <div class="col-md-2 col-lg-2">
                        <label class="control-label" for="game_api_id"><?=lang('Game API')?> </label>
                        <select id="game_api_id" class="form-control input-sm">
                            <option value=""><?=lang('All')?></option>
                            <?php foreach ($apiMap['game'] as $id => $val): ?>
                            <option value="<?=$id?>" <?=$id==$conditions['api_id'] ? 'selected="selected"' : ''?>><?=$val?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 col-lg-2">
                        <label class="control-label" for="payment_api_id"><?=lang('Payment API')?> </label>
                        <select id="payment_api_id" class="form-control input-sm">
                            <option value=""><?=lang('All')?></option>
                            <?php foreach ($apiMap['payment'] as $id => $val): ?>
                            <option value="<?=$id?>" <?=$id==$conditions['api_id'] ? 'selected="selected"' : ''?>><?=$val?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 col-lg-2">
                        <label class="control-label" for="sms_api_id"><?=lang('SMS API')?> </label>
                        <select id="sms_api_id" class="form-control input-sm">
                            <option value=""><?=lang('All')?></option>
                            <option value="<?=SMS_API?>" <?=$conditions['api_id']==SMS_API ? 'selected="selected"' : ''?>><?=lang('SMS API')?></option>
                        </select>
                    </div>
                    <div class="col-md-2 col-lg-2">
                        <label class="control-label" for="smtp_api_id"><?=lang('SMTP API')?> </label>
                        <select id="smtp_api_id" class="form-control input-sm">
                            <option value=""><?=lang('All')?></option>
                            <option value="<?=SMTP_API?>" <?=$conditions['api_id']==SMTP_API ? 'selected="selected"' : ''?>><?=lang('SMTP API')?></option>
                        </select>
                    </div>
                    <div class="col-md-2 col-lg-2">
                        <label class="control-label" for="external_login_api_id"><?=lang('LOGIN API')?> </label>
                        <select id="external_login_api_id" class="form-control input-sm">
                            <option value=""><?=lang('All')?></option>
                            <option value="<?=EXTERNAL_LOGIN_API?>" <?=$conditions['api_id']==EXTERNAL_LOGIN_API ? 'selected="selected"' : ''?>><?=lang('LOGIN API')?></option>
                        </select>
                    </div>
                    <div class="col-md-2 col-lg-2">
                        <label class="control-label" for="flag"><?=lang('Flag')?> </label>
                        <select name="flag" id="flag" class="form-control input-sm">
                            <option value=""><?=lang('All')?></option>
                            <option value="1" <?=$conditions['flag']==1 ? 'selected="selected"' : ''?>>Normal</option>
                            <option value="2" <?=$conditions['flag']==2 ? 'selected="selected"' : ''?>>Error</option>
                        </select>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-2 col-lg-2">
                        <label class="control-label" for="username"><?=lang('Player')?> </label>
                        <input type="text" name="username" id="username" class="form-control input-sm"
                            value='<?php echo $conditions["username"]; ?>'/>
                    </div>
                    <div class="col-md-2 col-lg-2">
                        <label class="control-label" for="email"><?=lang('Email Address')?> </label>
                        <input type="text" name="email" id="email" class="form-control input-sm"
                            value='<?php echo $conditions["email"]; ?>'/>
                    </div>
                    <div class="col-md-2 col-lg-2">
                        <label class="control-label" for="method"><?=lang('Method')?> </label>
                        <input type="text" name="method" id="method" class="form-control input-sm"
                            value='<?php echo $conditions["method"]; ?>'/>
                    </div>
                    <div class="col-md-2 col-lg-2">
                        <label class="control-label" for="no_query_balance"><?=lang('No Query Balance')?></label>
                        <input class="form-control input-sm" type="checkbox" name="no_query_balance" id="no_query_balance"
                            value='true' <?=$conditions['no_query_balance']=='true' ? ' checked="checked" ' : ''?>/>
                    </div>
                    <div class="col-md-2 col-lg-2">
                        <label class="control-label" for="show_sync_data"><?=lang('Show Sync Data')?></label>
                        <input class="form-control input-sm" type="checkbox" name="show_sync_data" id="show_sync_data"
                            value='true' <?=$conditions['show_sync_data'] ? ' checked="checked" ' : ''?>/>
                    </div>
                    <div class="col-md-2 col-lg-2">
                        <label class="control-label" for="show_gamegateway_api"><?=lang('Gamegateway API')?></label>
                        <input class="form-control input-sm" type="checkbox" name="show_gamegateway_api" id="show_gamegateway_api"
                            value='true' <?=$conditions['show_gamegateway_api'] ? ' checked="checked" ' : ''?>/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 col-lg-4">
                        <button class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>"><?=lang('Search')?></button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="icon-dice"></i> <?=lang('View Response Result')?> </h4>
    </div>
    <div class="panel-body table-responsive">
        <table class="table table-bordered table-hover" id="myTable">
            <thead>
                <tr>
                    <th><?=lang('Date')?></th>
                    <th><?=lang('ID')?></th>
                    <th><?=lang('API')?></th>
                    <th><?=lang('Payment ID')?></th>
                    <th><?=lang('Mobile')?></th>
                    <th><?=lang('Email Address')?></th>
                    <th><?=lang('Content')?></th>
                    <th><?=lang('Method')?></th>
                    <th><?=lang('Status Code')?></th>
                    <th><?=lang('Status Text')?></th>
                    <th><?=lang('Player')?></th>
                    <th><?=lang('Flag')?></th>
                    <th><?=lang('Result')?></th>
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
        $('#view_resp_result').addClass('active');

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
                <?php
                //always hide
                if (false && $export_report_permission) {?>
                ,{
                    text: "<?php echo lang('CSV Export'); ?>",
                    className:'btn btn-sm btn-primary',
                    action: function ( e, dt, node, config ) {
                        var form_params=$('#form-filter').serializeArray();
                        var d = {'extra_search': form_params, 'export_format': 'csv', 'export_type': 'queue',
                            'draw':1, 'length':-1, 'start':0};
                        // utils.safelog(d);

                        $("#_export_excel_queue_form").attr('action', site_url('/export_data/export_response_result_list'));
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
                //data.is_export = true;
                $.post(base_url + "api/response_result_list", data, function(data) {
                    callback(data);
                    $('.resendFlag').tooltip(); // for apply css of tooltip
                }, 'json');
            }
        });

    });
</script>

<script type="text/javascript">
    var api_id = $("#api_id").val();
    $("#game_api_id").change(function(){
        $("#game_api_id option:selected" ).each(function() {
          api_id = $(this).val();
        });
        $("#api_id").val(api_id);
        $('#external_login_api_id option:selected').removeAttr("selected");
        $('#payment_api_id option:selected').removeAttr("selected");
        $('#smtp_api_id option:selected').removeAttr("selected");
        $('#sms_api_id option:selected').removeAttr("selected");
    });
    $("#payment_api_id").change(function(){
        $("#payment_api_id option:selected" ).each(function() {
          api_id = $(this).val();
        });
        $("#api_id").val(api_id);
        $('#external_login_api_id option:selected').removeAttr("selected");
        $('#game_api_id option:selected').removeAttr("selected");
        $('#smtp_api_id option:selected').removeAttr("selected");
        $('#sms_api_id option:selected').removeAttr("selected");
        $('#show_gamegateway_api').removeAttr("checked");
    });
    $("#sms_api_id").change(function(){
        $("#sms_api_id option:selected" ).each(function() {
          api_id = $(this).val();
        });
        $("#api_id").val(api_id);
        $('#external_login_api_id option:selected').removeAttr("selected");
        $('#payment_api_id option:selected').removeAttr("selected");
        $('#smtp_api_id option:selected').removeAttr("selected");
        $('#game_api_id option:selected').removeAttr("selected");
        $('#show_gamegateway_api').removeAttr("checked");
    });
    $("#smtp_api_id").change(function(){
        $("#smtp_api_id option:selected" ).each(function() {
          api_id = $(this).val();
        });
        $("#api_id").val(api_id);
        $('#external_login_api_id option:selected').removeAttr("selected");
        $('#sms_api_id option:selected').removeAttr("selected");
        $('#payment_api_id option:selected').removeAttr("selected");
        $('#game_api_id option:selected').removeAttr("selected");
        $('#show_gamegateway_api').removeAttr("checked");
    });
    $("#external_login_api_id").change(function(){
        $("#external_login_api_id option:selected" ).each(function() {
          api_id = $(this).val();
        });
        $("#api_id").val(api_id);
        $('#sms_api_id option:selected').removeAttr("selected");
        $('#smtp_api_id option:selected').removeAttr("selected");
        $('#payment_api_id option:selected').removeAttr("selected");
        $('#game_api_id option:selected').removeAttr("selected");
        $('#show_gamegateway_api').removeAttr("checked");
    });
    $("#show_gamegateway_api").change(function(event){
        if (event.target.checked) {
            $('#external_login_api_id option:selected').removeAttr("selected");
            $('#sms_api_id option:selected').removeAttr("selected");
            $('#smtp_api_id option:selected').removeAttr("selected");
            $('#payment_api_id option:selected').removeAttr("selected");
        }
    });
</script>
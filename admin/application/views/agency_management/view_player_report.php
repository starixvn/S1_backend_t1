<?php
/**
 *   filename:   view_player_report.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2016-05-02
 *   @brief:     view players report in agency sub-system
 */

// set display according to configurations
$panelOpenOrNot = $this->config->item('default_open_search_panel') ? '' : 'collapsed';
$panelDisplayMode = $this->config->item('default_open_search_panel') ? '' : 'in';
if (isset($_GET['search_on_date'])) {
    $search_on_date = $_GET['search_on_date'];
} else {
    $search_on_date = false;
}
?>
<div class="panel panel-primary hidden">

    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i> <?=lang("lang.search")?>
            <span class="pull-right">
                <a data-toggle="collapse" href="#collapsePlayerReport" class="btn btn-default btn-xs <?= $this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
            </span>
        </h4>
    </div>

    <div id="collapsePlayerReport" class="panel-collapse collapse <?= $this->config->item('default_open_search_panel') ? '' : 'in'?>">
        <div class="panel-body">
            <form id="form-filter" class="form-horizontal" method="post">
                <input type="hidden" id="only_under_agency" name="only_under_agency" value="yes" />
                <div class="row">
                    <div class="col-md-6">
                        <label class="control-label"><?=lang('report.sum02')?></label>
                        <div class="input-group">
                            <input class="form-control dateInput" data-start="#date_from" data-end="#date_to" data-time="true"/>
                            <input type="hidden" id="date_from" name="date_from"/>
                            <input type="hidden" id="date_to" name="date_to"/>
                            <span class="input-group-addon input-sm">
                                <input type="checkbox" name="search_on_date" id="search_on_date" value="1" 
                                <?php if ($search_on_date) {echo 'checked';} ?>/>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="control-label" for="agent_name"><?=lang('Agent Username')?> </label>
                        <div class="input-group">
                            <input type="text" name="agent_name" id="agent_name" class="form-control input-sm" />
                            <span class="input-group-addon input-sm">
                                <input type="checkbox" name="include_all_downlines" value="true"/>
                                <?=lang('Include All Downline Agents')?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <label class="control-label" for="depamt2"><?=lang('report.pr31') . " >="?></label>
                        <input type="text" name="depamt2" id="depamt2" class="form-control number_only"/>
                    </div>
                    <div class="col-md-3">
                        <label class="control-label" for="depamt1"><?=lang('report.pr31') . " <="?></label>
                        <input type="text" name="depamt1" id="depamt1" class="form-control number_only"/>
                    </div>
                    <div class="col-md-3">
                        <label class="control-label" for="widamt2"><?=lang('report.pr32') . " >="?></label>
                        <input type="text" name="widamt2" id="widamt2" class="form-control number_only"/>
                    </div>
                    <div class="col-md-3">
                        <label class="control-label" for="widamt1"><?=lang('report.pr32') . " <="?></label>
                        <input type="text" name="widamt1" id="widamt1" class="form-control number_only"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <label class="control-label" for="playerlevel"><?=lang('report.pr03')?></label>
                        <select name="playerlevel" id="playerlevel" class="form-control">
                            <option value=""><?=lang('lang.selectall')?></option>
                            <?php foreach ($allLevels as $key => $value) {?>
                            <option value="<?=$value['vipsettingcashbackruleId']?>"><?=$value['groupName'] . ' ' . $value['vipLevel']?></option>
<?php }
?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="control-label" for="username"><?=lang('report.pr01')?></label>
                        <input type="text" name="username" id="username" class="form-control"/>
                    </div>
                    <div class="col-md-3">
                        <label class="control-label" for="group_by"><?=lang('report.g14')?> </label>
                        <select name="group_by" id="group_by" class="form-control">
                            <option value="player.playerId"><?=lang('report.pr01')?></option>
                            <option value="player.levelId"><?=lang('report.pr03')?></option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-1" style="padding-top: 20px">
                        <input type="submit" value="<?=lang('lang.search')?>" id="search_main"class="btn btn-info btn-sm">
                    </div>
                    <!-- <div class="col-md-1" style="padding-top: 20px">
                        <input type="button" value="<?=lang('lang.exporttitle')?>" class="btn btn-info btn-sm export_excel">
                    </div> -->
                </div>
            </form>
        </div>
    </div>
</div>

<?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
<form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
<input name='json_search' type="hidden">
</form>
<?php }?>

<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="icon-users"></i> <?=lang('report.s09')?> </h4>
    </div>
    <div class="panel-body">

        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="myTable">
                <thead>
                    <tr>
                        <?php include __DIR__.'/../includes/agency_player_report_table_header.php'; ?>
                    </tr>
                </thead>
            </table>
        </div>

    </div>
    <div class="panel-footer"></div>
</div>

<script type="text/javascript">
$(document).ready(function(){
    var dataTable = $('#myTable').DataTable({
        <?php if($this->utils->isEnabledFeature('column_visibility_report')){ ?>
            stateSave: true,
        <?php } else { ?>
            stateSave: false,
        <?php } ?>
        autoWidth: false,
        searching: false,
        dom: "<'panel-body' <'pull-right'B><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
        columnDefs: [
            //{ className: 'text-right', targets: [ 11,12,13,14,15,16,17,18,19, ] },
            //{ visible: false, targets: [ 1,3,4,6,7,8,9,10,11, ] },
        ],
        buttons: [
            <?php if ($export_report_permission) : ?>
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ]
                },
                {
                    text: "<?php echo lang('CSV Export'); ?>",
                    className:'btn btn-sm btn-primary export-all-columns',
                    action: function ( e, dt, node, config ) {
                        // var d = {'extra_search':$('#form-filter').serializeArray(), 'draw':1, 'length':-1, 'start':0};                        
                        // $.post(site_url('/export_data/player_reports'), d, function(data){                            
                        //     //create iframe and set link
                        //     if(data && data.success){
                        //         $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
                        //     }else{
                        //         alert('export failed');
                        //     }
                        // });
                        // 
                       
                        var form_params=$('#form-filter').serializeArray();
                        var d = {'extra_search': form_params, 'export_format': 'csv', 'export_type': export_type,'draw':1, 'length':-1, 'start':0};
                       
                        $("#_export_excel_queue_form").attr('action', site_url('/export_data/agency_player_reports'));
                        $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
                        $("#_export_excel_queue_form").submit();
                    }
                }
            <?php endif; ?>
        ],

        // SERVER-SIDE PROCESSING
        processing: true,
        serverSide: true,
        ajax: function (data, callback, settings) {
            data.extra_search = $('#form-filter').serializeArray();
            $.post(base_url + "api/agency_player_reports", data, function(data) {                           
                callback(data);
            }, 'json');
        }
    });

    $('#form-filter').submit( function(e) {
        e.preventDefault();
        dataTable.ajax.reload();
    });

    $('#group_by').change(function() {
        var value = $(this).val();
        if (value == 'player.playerId') {
            $('#username').val('').prop('disabled', false);
        } else {
            $('#username').val('').prop('disabled', true);
        }
    });

    // $('.export_excel').click(function(){

    //     // utils.safelog(dataTable.columns());

        
    // });

});
</script>

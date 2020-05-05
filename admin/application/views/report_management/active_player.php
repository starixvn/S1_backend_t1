<style type="text/css">

    .colored{
        color: red;
        font-weight: bold;
    }

</style>
<div class="panel panel-primary">

    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i><?=lang('lang.search')?><span class="pull-right">
                <a data-toggle="collapse" href="#collapsePromotionReport" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-info'?>"></a>
            </span>

<script type="text/javascript" src="<?=site_url('resources/third_party/clipboard/clipboard.min.js?v=3.01.00.0020')?>"></script>
        </h4>
    </div>


    <div id="collapsePromotionReport" class="panel-collapse ">
        <div class="panel-body">
            <form action="" method="get">
                <!-- <div class="row">
                    <div class="col-md-3 col-lg-3">
                        <label class="control-label">Bonus Release From</label>
                        <input id="search_payment_date" class="form-control input-sm dateInput user-success" data-start="#byBonusPeriodJoinedFrom" data-end="#byBonusPeriodJoinedTo" data-time="false" autocomplete="off" _vkenabled="true" _originaltype="text" type="text">
                        <input type="hidden" id="byBonusPeriodJoinedFrom" name="byBonusPeriodJoinedFrom" value="2016-12-19 00:00:00">
                        <input type="hidden" id="byBonusPeriodJoinedTo" name="byBonusPeriodJoinedTo" value="2016-12-19 23:59:59">
                    </div>
                </div> -->
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="view_type" class="control-label" id="type">
                            <input type="radio" name="view_type" id="daily" checked="checked" value="daily">
                            <label for="daily"><?=lang('Daily')?>   </label>
                            <input type="radio" name="view_type" id="weekly" <?=( (isset($_GET['view_type'])) && $_GET['view_type'] == 'weekly' ) ? 'checked="checked"' : ''?> value="weekly">
                            <label for="weekly"><?=lang('Weekly')?></label>
                            <input type="radio" name="view_type" id="monthly" <?=( (isset($_GET['view_type'])) && $_GET['view_type'] == 'monthly' ) ? 'checked="checked"' : ''?> value="monthly">
                            <label for="monthly"><?=lang('Monthly')?></label>
                        </label>

                    </div>

                </div>
                <div class="row">
                    <div class="form-group col-md-3">
                        <label class="control-label"><?=lang('Date Range')?></label>
                        <input id="search_registration_date" class="form-control input-sm dateInput" data-start="#date_form" data-end="#date_to" data-time="false"/>
                        <input type="hidden" id="date_form" name="date_form" value="<?php echo (isset($_GET['date_form'])) ? $_GET['date_form'] : ''?>">
                        <input type="hidden" id="date_to" name="date_to" value="<?php echo (isset($_GET['date_to'])) ? $_GET['date_to'] : ''?>">

                    </div>

                </div>
                <div class="row">
                    <div class="col-md-1" style="padding-top: 20px">
                        <input type="submit" value="<?=lang('lang.search')?>" id="search_main" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-info'?>">
                    </div>
                </div>

            </form>
        </div>
    </div>

</div>

<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title">
            <i class="icon-pie-chart"></i>
            <?=lang('Active Player Report')?>
        </h4>
    </div>
    <div class="panel-body ">
        <div class="table-responsive">
        <table class="table table-condensed table-bordered table-hover " id="active-players-report" style="width:100%;">
            <thead>
                <tr>
                    <!-- <th></th> -->
                    <th data-orderable="false"><?=lang('Date')?></th>
                    <th data-orderable="false"><?=lang('Total Active Player')?></th>
                    <?php
                        if( ! empty( $game_provider ) ){

                            foreach ($game_provider as $key => $value) {
                    ?>
                            <th data-orderable="false"><?=$value['system_code']?></th>
                    <?php
                            }

                        }
                    ?>

                </tr>
            </thead>
        </table>
    </div>
    </div>
    <div class="panel-footer"></div>
</div>

<!-- Use Datatable Export, to prevent slow loading data -->
<!--<script type="text/javascript" src="--><?//=site_url().'resources/datatables/dataTables.buttons.min.js'?><!--"></script>-->
<!--<script type="text/javascript" src="--><?//=site_url().'resources/datatables/jszip.min.js'?><!--"></script>-->
<!--<script type="text/javascript" src="--><?//=site_url().'resources/datatables/buttons.html5.min.js'?><!--"></script>-->
<form id="_export_csv_form" class="hidden" method="POST" target="_blank">
<input name='json_search' id = "json_csv_search" type="hidden">
</form>

<script type="text/javascript">

    <?php
        //Construct CSV headers for export
        $headersString = [];
        if( !empty( $game_provider ) ){
            foreach ($game_provider as $key => $value) {
                array_push($headersString,'"'.$value['system_code'].'"');
            }
           $headersString = implode(",", $headersString);
        }
    ?>

     var jsonObjForExportToCSV = {};

     function strip_html_tags(str)
     {
         if ((str===null) || (str===''))
            return 0;
         else
             str = str.toString();
         return str.replace(/<[^>]*>/g, '');
     }

    $(document).ready(function(){

        jsonObjForExportToCSV['header_data'] = ["<?=lang('Date')?>","<?=lang('Total Active Player')?>", <?=$headersString?> ];

        $('#active_player_report').addClass('active');

        var q = '<?=$_SERVER['QUERY_STRING']?>';

        $('#active-players-report').DataTable({
            lengthMenu: JSON.parse('<?=json_encode($this->utils->getConfig('default_datatable_lengthMenu'))?>'),
            pageLength: <?=$this->utils->getDefaultItemsPerPage()?>,
            autoWidth: false,
            <?php if($this->utils->isEnabledFeature('column_visibility_report')){ ?>
                stateSave: true,
            <?php } else { ?>
                stateSave: false,
            <?php } ?>
            dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            "order": [[0, 'asc']],
            "ajax": {
                "url":  '/api/activePlayers?' + q,
                "dataSrc" : function (json) {
                    dtData = json.data;
                    //Clear tags for export
                    var len = dtData.length,cleanData = [];
                    for(var i=0; i < len; i++){
                       var row = dtData[i], newRow = [];

                       for(var k=0; k < row.length; k++){
                            newRow.push(strip_html_tags(row[k]));
                       }
                       cleanData.push(newRow);
                    }
                    jsonObjForExportToCSV['data'] = cleanData;
                    $("#json_csv_search").val(JSON.stringify(jsonObjForExportToCSV));
                    return json.data;
                }
            }

            ,
            buttons: [
                <?php if ($export_report_permission):?>
                {
                    text: "<?php echo lang('CSV Export'); ?>",
                    className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?> _export_csv_btn',
                    action: function ( e, dt, node, config ) {
                        $(this).attr('disabled', 'disabled');
                        $.ajax({
                            url:  site_url('/export_data/active_player_report_results'),
                            type: 'POST',
                            data: {json_search: $("#json_csv_search").val() }
                        }).done(function(data) {
                            if(data && data.success){
                            $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
                            $('._export_csv_btn').removeAttr("disabled");
                            }else{
                            $('._export_csv_btn').removeAttr("disabled");
                            alert('export failed');
                            }
                        }).fail(function(){
                            $('._export_csv_btn').removeAttr("disabled");
                            alert('export failed');
                        });
                    }
                }
                <?php endif; ?>
            ]
        });
    });

//var type = 'daily';
//$(document).ready(function(){
//  $('#active_player_report').addClass('active');
//
//  var q = '<?//=$_SERVER['QUERY_STRING']?>//';
//
//  $('#active-players-report').DataTable({
//      dom: "<'panel-body' <'pull-right'B><'pull-right'f><'pull-right progress-container'>l>" +
//      "<'dt-information-summary1 text-info pull-left' i>t<'text-center'r>" +
//      "<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
//      buttons: [
//          <?php //if( $this->permissions->checkPermissions('active_player_report') ){ ?>
//          {
//              extend: 'csvHtml5',  // csvHtml5 , copyHtml5, excelHtml5
//              exportOptions: {
//                  columns: ':visible'
//              },
//              className:'btn btn-sm btn-primary',
//              text: '<?//=lang('CSV Export')?>//',
//              filename:  '<?//=lang('Active Player Report')?>//'
//          }
//          <?php //} ?>
//      ],
//      "columnDefs": [
//          {
//              "targets": [ 0 ],
//              "visible": false
//          }
//      ],
//      ajax: '/api/activePlayers?' + q,
//      "pageLength": 20,
//      "order": [[0, 'asc']]
//  });
//});



</script>


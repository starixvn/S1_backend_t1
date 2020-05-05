<?php
// $period = $this->session->userdata('period');
// $start_date = $this->session->userdata('start_date');
// $end_date = $this->session->userdata('end_date');
// $date_range_value = $this->session->userdata('date_range_value');

// $username = $this->session->userdata('username');
// $type_date = $this->session->userdata('type_date');
?>

<div class="panel panel-primary hidden">

    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i> <?=lang("lang.search")?>
            <span class="pull-right">
                <a data-toggle="collapse" href="#collapseAffiliateStatistics" class="btn btn-info btn-xs <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
            </span>
        </h4>
    </div>

    <div id="collapseAffiliateStatistics" class="panel-collapse <?=$this->config->item('default_open_search_panel') ? '' : 'collapse in'?>">
        <div class="panel-body">
            <form class="form-horizontal" action="<?=site_url('affiliate_management/viewAffiliateStatistics')?>" method="POST">
                <div class="form-group">
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <label for="username" class="control-label" style="font-size:12px;"><?=lang('aff.as03');?>:</label>
                            <select class="select form-control input-sm" name="username" id="username" style="width: 100%;">
                                <option value=""></option>
                                <?php foreach ($affiliates as $a) {
	?>
                                    <option <?php if ($username != null && $username == $a['username']) {
		echo 'selected';
	}
	?>><?php echo $a['username']; ?></option>
                                <?php }
?>
                            </select>
                        </div>
                        <div class="col-md-4" id="reportrange">
                            <label class="control-label" for="period"><?=lang('aff.ap07');?>:</label>
                            <input type="text" class="form-control input-sm dateInput" data-start="#dateRangeValueStart" data-end="#dateRangeValueEnd" data-time="true"/>
                            <input type="hidden" id="dateRangeValueStart" name="dateRangeValueStart" value="<?=$start_date == '' ? '' : $start_date;?>" />
                            <input type="hidden" id="dateRangeValueEnd" name="dateRangeValueEnd" value="<?=$end_date == '' ? '' : $end_date;?>" />
                        </div>
                        <div class="col-md-4">
                            <input type="submit" value="<?=lang('aff.as22');?>" id="search_main" class="btn btn-info btn-sm" style="margin-top: 23px;">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- display statistics -->
<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt"><i class="glyphicon glyphicon-signal"></i> <?=lang('aff.as01');?> </h4>
    </div>

    <div class="panel-body" id="details_panel_body">
    <table id="statisticsTable" class="table table-bordered table-hover dataTable" style="width:100%">
        <thead>
            <tr>
                <th><?php echo lang('Affiliate Username'); ?></th>
                <th><?php echo lang('Real Name'); ?></th>
                <th><?php echo lang('Affiliate Level'); ?></th>
                <th><?php echo lang('Total Sub-affiliates'); ?></th>
                <th><?php echo lang('Total Players'); ?></th>
                <th><?php echo lang('Total Bet'); ?></th>
                <th><?php echo lang('Total Win'); ?></th>
                <th><?php echo lang('Total loss'); ?></th>
                <th><?php echo lang('Total Cashback'); ?></th>
                <th><?php echo lang('Total Bonus'); ?></th>
                <th><?php echo lang('Total Deposit'); ?></th>
                <th><?php echo lang('Total Withdraw'); ?></th>
            </tr>
        </thead>
        <tbody>
    <!-- load ajax datatable here.. -->
<?php
foreach ($statistics as $stat) {
	?>
            <tr>
                <td><?php echo $stat[0]; ?></td>
                <td><?php echo $stat[1]; ?></td>
                <td><?php echo $stat[2]; ?></td>
                <td><?php echo $stat[3]; ?></td>
                <td><?php echo $stat[4]; ?></td>
                <td><?php echo $stat[5]; ?></td>
                <td><?php echo $stat[6]; ?></td>
                <td><?php echo $stat[7]; ?></td>
                <td><?php echo $stat[8]; ?></td>
                <td><?php echo $stat[9]; ?></td>
                <td><?php echo $stat[10]; ?></td>
                <td><?php echo $stat[11]; ?></td>
                <td><?php echo $stat[12]; ?></td>
                <td><?php echo $stat[13]; ?></td>
            </tr>

<?php
}
?>

        </tbody>
    </table>

    <div class="panel-footer"></div>
</div>
<!-- end of display statistics -->

<script>
$(document).ready(function() {
    // Initialize multi select
    $('.select').select2({
        placeholder: "<?=lang('aff.as03');?>",
        allowClear: true
    });
    // Filters
    $('.select').on('change', function(){
        filterTable();
    });
    function filterTable(){
        var filter = $('.select').val();
        $('#statisticsTable').DataTable().search(filter).draw();
    }


    // var dataURL = "<?php echo base_url(); ?>affiliate_management/viewAffiliateStatisticsJSON";

    var dataTable = $('#statisticsTable').DataTable({
        autoWidth: false,
        stateSave: false,
        //dom: "<'panel-body'<'pull-right'B>l>t<'text-center'r><'panel-body'<'pull-right'p>i>",
        dom: "<'panel-body' <'pull-right'B><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
        buttons: [
            {
                extend: 'colvis',
                postfixButtons: [ 'colvisRestore' ]
            }
        ],
        order: [[ 0, 'asc' ]]
        // processing: true,
        // serverSide: true,
        // ajax: $.fn.dataTable.pipeline( {
        //     url: dataURL,
        //     pages: 20 // number of pages to cache
        // } )
    });
});
// $.fn.dataTable.pipeline = function ( opts ) {
//     // Configuration options
//     var conf = $.extend( {
//         pages: 5,     // number of pages to cache
//         url: '',      // script url
//         data: null,   // function or object with parameters to send to the server
//                       // matching how `ajax.data` works in DataTables
//         method: 'GET' // Ajax HTTP method
//     }, opts );

//     // Private variables for storing the cache
//     var cacheLower = -1;
//     var cacheUpper = null;
//     var cacheLastRequest = null;
//     var cacheLastJson = null;

//     return function ( request, drawCallback, settings ) {
//         var ajax          = false;
//         var requestStart  = request.start;
//         var drawStart     = request.start;
//         var requestLength = request.length;
//         var requestEnd    = requestStart + requestLength;

//         if ( settings.clearCache ) {
//             // API requested that the cache be cleared
//             ajax = true;
//             settings.clearCache = false;
//         }
//         else if ( cacheLower < 0 || requestStart < cacheLower || requestEnd > cacheUpper ) {
//             // outside cached data - need to make a request
//             ajax = true;
//         }
//         else if ( JSON.stringify( request.order )   !== JSON.stringify( cacheLastRequest.order ) ||
//                   JSON.stringify( request.columns ) !== JSON.stringify( cacheLastRequest.columns ) ||
//                   JSON.stringify( request.search )  !== JSON.stringify( cacheLastRequest.search )
//         ) {
//             // properties changed (ordering, columns, searching)
//             ajax = true;
//         }

//         // Store the request for checking next time around
//         cacheLastRequest = $.extend( true, {}, request );

//         if ( ajax ) {
//             // Need data from the server
//             if ( requestStart < cacheLower ) {
//                 requestStart = requestStart - (requestLength*(conf.pages-1));

//                 if ( requestStart < 0 ) {
//                     requestStart = 0;
//                 }
//             }

//             cacheLower = requestStart;
//             cacheUpper = requestStart + (requestLength * conf.pages);

//             request.start = requestStart;
//             request.length = requestLength*conf.pages;

//             // Provide the same `data` options as DataTables.
//             if ( $.isFunction ( conf.data ) ) {
//                 // As a function it is executed with the data object as an arg
//                 // for manipulation. If an object is returned, it is used as the
//                 // data object to submit
//                 var d = conf.data( request );
//                 if ( d ) {
//                     $.extend( request, d );
//                 }
//             }
//             else if ( $.isPlainObject( conf.data ) ) {
//                 // As an object, the data given extends the default
//                 $.extend( request, conf.data );
//             }

//             settings.jqXHR = $.ajax( {
//                 "type":     conf.method,
//                 "url":      conf.url,
//                 "data":     request,
//                 "dataType": "json",
//                 "cache":    false,
//                 "success":  function ( json ) {
//                     cacheLastJson = $.extend(true, {}, json);

//                     if ( cacheLower != drawStart ) {
//                         json.data.splice( 0, drawStart-cacheLower );
//                     }
//                     json.data.splice( requestLength, json.data.length );

//                     drawCallback( json );
//                 }
//             } );
//         }
//         else {
//             json = $.extend( true, {}, cacheLastJson );
//             json.draw = request.draw; // Update the echo for each response
//             json.data.splice( 0, requestStart-cacheLower );
//             json.data.splice( requestLength, json.data.length );

//             drawCallback(json);
//         }
//     }
// };

// // Register an API method that will empty the pipelined data, forcing an Ajax
// // fetch on the next draw (i.e. `table.clearPipeline().draw()`)
// $.fn.dataTable.Api.register( 'clearPipeline()', function () {
//     return this.iterator( 'table', function ( settings ) {
//         settings.clearCache = true;
//     } );
// } );
</script>
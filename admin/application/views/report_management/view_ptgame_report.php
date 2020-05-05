<div class="row">
    <div class="col-md-12">
        <ul class="nav nav-tabs">
          <li class="active"><a href="#ptgame-sec-table" data-toggle="tab">PT</a></li>
          <!-- <li><a href="#aggame-sec-lc" data-toggle="tab">AG</a></li> -->
          <!-- <li><a href="#opusgame-sec-cc" data-toggle="tab">OPUS</a></li> -->
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="ptgame-sec-table">
                <!-- player function issue -->
                <div class="btn-group"></div>
                <!-- end player function issue -->
                
                <div class="panel panel-primary
              " style="margin-top:-10px;">
                    <div class="panel-heading">
                        <h4 class="panel-title custom-pt"><i class="icon-cogs"></i> <?= lang('report.api01'); ?></h4>
                    </div>

                    <div class="panel-body">
                        <div id="logList" class="table-responsive">
                            <table id="my_table" class="table table-striped table-hover table-condensed" style="width:100%;">
                                <?php if($export_report_permission){ ?>
                                    <a href="<?= BASEURL . 'report_management/exportPTGameApiReportToExcel' ?>" data-toggle="tooltip" title="<?= lang('lang.exporttitle'); ?>" class="btn btn-sm btn-success btn-action" data-placement="top">
                                        <span class="glyphicon glyphicon-share"></span>
                                    </a>
                                <?php } ?>
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th><?= lang('report.api02'); ?></th>                                        
                                        <th><?= lang('report.api03'); ?></th>
                                        <th><?= lang('report.api04'); ?></th>
                                        <th><?= lang('report.api05'); ?></th>
                                        <th><?= lang('report.api06'); ?></th>
                                        <th><?= lang('report.api07'); ?></th>
                                        <th><?= lang('lang.status'); ?></th>
                                        <th><?= lang('lang.action'); ?></th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php if(!empty($logs)) {
                                            foreach($logs as $row) {
                                    ?>
                                                <tr title="<?= $row['description'] ?>">
                                                    <td></td>
                                                    <td><?= $row['reportType'] ?></td>
                                                    <td><?= $row['username'] ?></td>
                                                    <td><?= $row['errorReturn'] ?></td>
                                                    <td><?= $row['description'] ?></td>
                                                    <td class="col-md-4"><?= $row['apiCallSyntax'] ?></td>
                                                    <td><?= $row['errorTimeStamp'] ?></td>
                                                    <td><?= $row['status'] ?></td>    
                                                    <td><a class="reportActionBtn" href="<?= BASEURL . 'pt_report_management/resolvePTIssueReport/'.$row['issueReportPtApiId'] ?>" title="">
                                                        <span class="btn-sm btn-info review-btn">
                                                            <?= lang('report.api08'); ?>
                                                        </span>
                                                    </a></td>
                                                </tr>
                                    <?php   }
                                          }
                                          else{
                                          }
                                     ?>
                                </tbody>
                            </table>

                         </div>

                    </div>

                    <div class="panel-footer"> </div>
                </div>
            </div>
            <div class="tab-pane" id="aggame-sec-lc"></div>
            <div class="tab-pane" id="opusgame-sec-cc"></div>
        </div>
        
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#my_table').DataTable({
            "responsive": {
                details: {
                    type: 'column'
                }
            },
              "dom":"<'panel-body' <'pull-right'f><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            buttons: [
            {
              extend: 'colvis',
              postfixButtons: [ 'colvisRestore' ]
          }],
            "columnDefs": [ {
                className: 'control',
                orderable: false,
                targets:   0
            } ],
            "order": [ 1, 'asc' ],
           // "dom": '<"top"fl>rt<"bottom"ip>',
            "fnDrawCallback": function(oSettings) {
                $('.btn-action').prependTo($('.top'));
            }
        });


    });
</script>
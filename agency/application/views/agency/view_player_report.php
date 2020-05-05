<?php
/**
 *   filename:   view_player_report.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2016-05-02
 *   @brief:     view for agency logs in agency sub-system
 */

if (isset($_GET['search_on_date'])) {
	$search_on_date = $_GET['search_on_date'];
} else {
	$search_on_date = isset($date_from, $date_to);
}
?>
<div class="content-container">
    <div class="panel panel-primary">

        <div class="panel-heading">
            <h4 class="panel-title">
                <i class="fa fa-search"></i> <?=lang("lang.search")?>
                <span class="pull-right">
                    <a data-toggle="collapse" href="#collapsePlayerReport" class="btn btn-default btn-xs <?= $this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
                </span>
            </h4>
        </div>

        <div id="collapsePlayerReport" class="panel-collapse <?= $this->config->item('default_open_search_panel') ? '' : 'collapse in'?>">
            <div class="panel-body">
                <form id="form-filter" class="form-horizontal" method="post">
                    <input type="hidden" id="only_under_agency" name="only_under_agency" value="yes" />
                    <input type="hidden" id="current_agent_name" name="current_agent_name" value="<?=$agent_name?>" />
                    <div class="row">
                        <div class="col-md-6">
                            <label class="control-label"><?=lang('report.sum02')?></label>
                            <div class="input-group">
                                <input class="form-control dateInput" data-start="#date_from" data-end="#date_to" data-time="true"/>
                                <input type="hidden" id="date_from" name="date_from" value="<?=@$date_from?>"/>
                                <input type="hidden" id="date_to" name="date_to" value="<?=@$date_to?>"/>
                                <span class="input-group-addon input-sm">
                                    <input type="checkbox" name="search_on_date" id="search_on_date" value="1"
                                    <?php if ($search_on_date) {echo 'checked';} ?>/>
                                </span>
                            </div>
                        </div>
                        <!-- agent Username {{{3 -->
                        <div class="col-md-6">
                            <label class="control-label" for="agent_name"><?=lang('Agent Username')?> </label>
                            <div class="input-group">
                                <input type="text" name="agent_name" id="agent_name" class="form-control" value="<?=@$agent_username?>"/>
                                <span class="input-group-addon input-sm">
                                    <input type="checkbox" name="include_all_downlines" value="true"
                                    <?php if ( ! $agent_username) {echo 'checked';} ?>/>
                                    <?=lang('Include All Downline Agents')?>
                                </span>
                            </div>
                        </div>
                        <!-- agent Username }}}3 -->
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

                        <?php if ($this->utils->isEnabledFeature('enable_agency_player_report_generator')): ?>
                            <div class="col-md-3">
                                <label class="control-label" for="only_show_non_zero_player"><?=lang('Only Show Non-Zero Player')?></label>
                                <input type="checkbox" name="only_show_non_zero_player" checked="checked" id="only_show_non_zero_player" class="form-control" value="true"/>
                            </div>
                        <?php endif ?>

                    </div>
                    <div class="col-md-4 col-lg-4 pull-right" style="padding-top: 10px; padding-right:0px;">
                        <input type="submit" value="<?=lang('lang.search')?>" id="search_main" class="btn btn-primary btn-sm pull-right">
                    </div>
                    <?php /*if ( ! $this->utils->isEnabledFeature('agent_hide_export')): */?><!--
                        <div class="col-md-4" style="padding-top: 10px; padding-left:0px;">
                            <input type="button" value="<?/*=lang('CSV Export')*/?>"
                            class="btn btn-success btn-sm agent-oper export_excel">
                        </div>
                    --><?php /*endif */?>
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
                    <tfoot>
                        <tr>
                            <th></th>
                            <th><?=lang('Total')?></th>
                            <th><span class="total-deposit">0.00</span></th>
                            <th><span class="total-withdrawal">0.00</span></th>
                            <th><span class="total-bet">0.00</span></th>
                            <th><span class="total-payout">0.00</span></th>
                            <th><span class="total-win">0.00</span></th>
                            <th><span class="total-loss">0.00</span></th>
                            <th><span class="total-netgaming">0.00</span></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <p align="right"><b><?=lang('Win / Loss')?></b> - <?=lang('positive values means agent win, negative means agent lose and customer win')?></p>
        </div>
        <div class="panel-footer"></div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function(){
    <?php $agent_status = $this->session->userdata('agent_status'); ?>
    <?php if($agent_status == 'suspended') { ?>
    set_suspended_operations();
    <?php } ?>

    var export_type="<?php echo $this->utils->isEnabledFeature('export_excel_on_queue') ? 'queue' : 'direct';?>";

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
            {
                extend: 'colvis',
                postfixButtons: [ 'colvisRestore' ],
                className:'btn btn-sm'
            },
            {
                text: "<?php echo lang('CSV Export'); ?>",
                className:'btn btn-sm btn-primary export-all-columns',
                action: function ( e, dt, node, config ) {

                    var form_params=$('#form-filter').serializeArray();
                    var d = {'extra_search': form_params, 'export_format': 'csv', 'export_type': export_type, 'draw':1, 'length':-1, 'start':0};

                    $("#_export_excel_queue_form").attr('action', document.location.origin + ('/export_data/agency_player_reports'));
                    $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
                    $("#_export_excel_queue_form").submit();
                }
            }
        ],
            // SERVER-SIDE PROCESSING
            processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {
                data.extra_search = $('#form-filter').serializeArray();
                $.post(base_url + "api/agency_player_reports", data, function(data) {
                    <?php if ($this->utils->isEnabledFeature('show_total_for_player_report')) {?>
                        $('.total-deposit').text(parseFloat(data.totals.total_deposit||0).toFixed(2));
                        $('.total-withdrawal').text(parseFloat(data.totals.total_withdrawal||0).toFixed(2));
                        $('.total-bet').text(parseFloat(data.totals.total_bets||0).toFixed(2));
                        $('.total-payout').text(parseFloat(data.totals.payout||0).toFixed(2));
                        $('.total-win').text(parseFloat(data.totals.total_wins||0).toFixed(2));
                        $('.total-loss').text(parseFloat(data.totals.total_loss||0).toFixed(2));
                        $('.total-netgaming').text(parseFloat(data.totals.net_gaming||0).toFixed(2));
                    <?php }?>
                    callback(data);
                }, 'json')
                .fail( function (jqxhr, status_text)  {
                    if ( jqxhr.status >= 300 && jqxhr.status < 500 ) {
                        if (confirm('<?= lang('session.timeout') ?>')) {
                            window.location.href = '/';
                        }
                    }
                    else {
                        alert(status_text);
                    }
                });
            }
    });

    $('#form-filter').submit( function(e) {
        e.preventDefault();
        var target_agent = $('input#agent_name').val().trim();
        var master_agent = $('#current_agent_name').val();
        // console.log('target_agent', target_agent, 'master_agent', master_agent);

        if (target_agent != '' && target_agent != master_agent) {
            $.post(
                '/agency/agency_check_ancestry',
                { target_agent: target_agent }
            )
            .success(function (res) {
                if (res.success == false) {
                    alert("<?= lang('error.default.message') ?>");
                    return;
                }

                if (res.result == false) {
                    alert(("<?= lang('agency.no_permission_for_agent') ?>").replace('%s', target_agent));
                    return;
                }

                dataTable.ajax.reload();
            });
        } else {
            dataTable.ajax.reload();
        }
    });

    $('#group_by').change(function() {
        var value = $(this).val();
        if (value == 'player.playerId') {
            $('#username').val('').prop('disabled', false);
            dataTable.column( 0 ).visible( true );
            dataTable.column( 0 ).order('asc');
        } else {
            $('#username').val('').prop('disabled', true);
            dataTable.column( 0 ).visible( false );
            dataTable.column( 1 ).order('asc');
        }
        dataTable.ajax.reload();
    });

});
</script>
<?php
                        // zR to open all folded lines
                        // vim:ft=php:fdm=marker
                        // end of view_player_report.php

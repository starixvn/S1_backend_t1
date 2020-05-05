<?php
/**
 *   filename:   view_player_report.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2016-05-02
 *   @brief:     view for agency logs in agency sub-system
 */
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
                <form id="form-filter" method="post">
                    <input type="hidden" id="agent_id" name="agent_id" value="<?=$agent_id?>" />
                    <div class="row">

                        <div class="col-md-offset-2 col-md-8">
                            <input type="button" class="btn btn-sm btn-info btn_today" value="<?php echo lang('Today');?>">
                            <input type="button" class="btn btn-sm btn-info btn_yesterday" value="<?php echo lang('Yesterday');?>">
                            <input type="button" class="btn btn-sm btn-info btn_this_week" value="<?php echo lang('This Week');?>">
                            <input type="button" class="btn btn-sm btn-info btn_last_week" value="<?php echo lang('Last Week');?>">
                            <input type="button" class="btn btn-sm btn-info btn_this_month" value="<?php echo lang('This Month');?>">
                            <input type="button" class="btn btn-sm btn-info btn_last_month" value="<?php echo lang('Last Month');?>">
                        </div>

                        <div class="col-md-offset-2 col-md-8" style="margin-top: 10px;">
                            <label class="control-label"><?=lang('report.sum02')?></label>
                            <div class="input-group">
                                <input class="form-control dateInput" id="search-date" data-start="#date_from" data-end="#date_to" data-time="true"
                                <?php if (empty($date_from) || empty($date_to)): ?>
                                    disabled="disabled"
                                <?php endif ?>
                                />

                                <input type="hidden" id="date_from" name="date_from"
                                <?php if ( ! empty($date_from) && ! empty($date_to)): ?>
                                    value="<?=$date_from?>"
                                <?php else: ?>
                                    disabled="disabled"
                                <?php endif ?>
                                />

                                <input type="hidden" id="date_to" name="date_to"
                                <?php if ( ! empty($date_from) && ! empty($date_to)): ?>
                                    value="<?=$date_to?>"
                                <?php else: ?>
                                    disabled="disabled"
                                <?php endif ?>
                                />

                                <span class="input-group-addon input-sm">
                                    <input type="checkbox" onclick="$('#search-date, #date_from, #date_to').prop('disabled', ! this.checked)"
                                    <?php if ( ! empty($date_from) && ! empty($date_to)): ?>
                                        checked="checked"
                                    <?php endif ?>
                                    />
                                </span>
                            </div>
                        </div>

                        <div class="col-md-offset-2 col-md-8" style="margin-top: 10px;">
                            <div class="pull-right">
                                <?php if ( ! $this->utils->isEnabledFeature('agent_hide_export')): ?>
                                    <input type="button" value="<?=lang('lang.exporttitle')?>" class="btn btn-success btn-sm agent-oper export_excel">
                                <?php endif ?>
                                <input type="submit" value="<?=lang('lang.search')?>" id="search_main" class="btn btn-primary btn-sm">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h4 class="panel-title">
                <i class="icon-users"></i>
                <?=lang('Agent Report') ?>
                <span class="pull-right">
                    <?=$agent_name?>
                    <?php if ($agent_id != $this->session->userdata('agent_id')): ?>
                        <a href="#" onclick="agency_agent_report(<?=$parent_id?>)"><i class="glyphicon glyphicon-upload"></i></a>
                    <?php endif ?>
                </span>
            </h4>
        </div>
        <div class="panel-body">

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="myTable">
                    <thead>
                        <tr>
                            <?php include __DIR__.'/../includes/agency_agent_report_table_header.php'; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="8" align="center"><?=lang('Loading...')?></td></tr>
                    </tbody>
                    <tfoot>
                        <tr id="agent-summary">
                            <?php include __DIR__.'/../includes/agency_agent_report_table_header.php'; ?>
                        </tr>
                        <tr id="player-summary">
                            <?php include __DIR__.'/../includes/agency_agent_report_table_header.php'; ?>
                        </tr>
                        <tr id="total-summary">
                            <?php include __DIR__.'/../includes/agency_agent_report_table_header.php'; ?>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
        <div class="panel-footer"></div>
    </div>
</div>

<script type="text/javascript">

function submitDateRange($from, $to){
    $('#date_from').val($from.format('YYYY-MM-DD HH:mm:ss'));
    $('#date_to').val($to.format('YYYY-MM-DD HH:mm:ss'));
    var dateInput=$('#search-date');

    dateInput.data('daterangepicker').setStartDate($from);
    dateInput.data('daterangepicker').setEndDate($to);

    $('#form-filter').submit();
}

$(document).ready(function(){
    <?php $agent_status = $this->session->userdata('agent_status'); ?>
    <?php if($agent_status == 'suspended') { ?>;
    set_suspended_operations();
    <?php } ?>

    $('.btn_yesterday').click(function(){
        //today
        $from=moment().subtract(1,'days').startOf('day');
        $to=moment().subtract(1,'days').endOf('day');
        submitDateRange($from, $to);
    })

    $('.btn_today').click(function(){
        //today
        $from=moment().startOf('day');
        $to=moment().endOf('day');
        submitDateRange($from, $to);
    })

    $('.btn_last_week').click(function(){
        //this week
        $from=moment().subtract(1,'weeks').startOf('isoWeek');
        $to=moment().subtract(1,'weeks').endOf('isoWeek');

        submitDateRange($from, $to);
    })

    $('.btn_this_week').click(function(){
        //this week
        $from=moment().startOf('isoWeek');
        $to=moment().endOf('day');

        submitDateRange($from, $to);
    })

    $('.btn_last_month').click(function(){
        //this month
        $from=moment().subtract(1,'months').startOf('month');
        $to=moment().subtract(1,'months').endOf('month');

        submitDateRange($from, $to);
    })

    $('.btn_this_month').click(function(){
        //this month
        $from=moment().startOf('month');
        $to=moment().endOf('day');

        submitDateRange($from, $to);
    })

    var dataTable = $('#myTable').DataTable({

        autoWidth: false,
            searching: false,
            lengthMenu: [50, 100, 250, 500, 1000],
            dom: "<'panel-body' <'pull-right'B><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            columnDefs: [
                        //{ className: 'text-right', targets: [ 11,12,13,14,15,16,17,18,19, ] },
                        //{ visible: false, targets: [ 1,3,4,6,7,8,9,10,11, ] },
                    ],
                    buttons: [
                        {
                            text: "<?=lang('Column visibility')?>",
                            extend: 'colvis',
                                postfixButtons: [ 'colvisRestore' ]
                        }
        ],

            // SERVER-SIDE PROCESSING
            processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {

                data.extra_search = $('#form-filter').serializeArray();

                $.post(base_url + "api/agency_agent_reports", data, function(data) {
                    if (data.agent_summary) {
                        $('tr#agent-summary > th').each(function(i,v) {
                            $(this).html(data.agent_summary[i]);
                        });
                        $('tr#player-summary > th').each(function(i,v) {
                            $(this).html(data.player_summary[i]);
                        });
                        $('tr#total-summary > th').each(function(i,v) {
                            $(this).html(data.total_summary[i]);
                        });
                    }
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
            },
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

    $('.export_excel').click(function(){

        if (agent_suspended) {
            return false;
        }
        // utils.safelog(dataTable.columns());

        var d = {'extra_search':$('#form-filter').serializeArray(), 'draw':1, 'length':-1, 'start':0};
        var export_url = "<?php echo site_url('export_data/agency_agent_reports') ?>";
        // utils.safelog(d);
        //$.post(site_url('/export_data/player_reports'), d, function(data){
        $.post(export_url, d, function(data){
            // utils.safelog(data);

            //create iframe and set link
            if(data && data.success){
                $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
            }else{
                alert('export failed');
            }
        });
    });

});


function agency_agent_report(agent_id) {
    var date_from = $('#date_from:enabled').val() || '';
    var date_to = $('#date_to:enabled').val() || '';
    location.href = '/agency/agency_agent_report/' + agent_id + '?date_from=' + date_from + '&date_to=' + date_to;
}

function credit_transactions(agent_username) {
    var date_from = $('#date_from:enabled').val() || '';
    var date_to = $('#date_to:enabled').val() || '';
    var url = '/agency/credit_transactions?agent_username=' + agent_username + '&date_from=' + date_from + '&date_to=' + date_to;
    var win = window.open(url, '_blank');
    win.focus();
}

function agency_player_report(agent_username) {
    var date_from = $('#date_from:enabled').val() || '';
    var date_to = $('#date_to:enabled').val() || '';
    var url = '/agency/agency_player_report?agent_username=' + agent_username + '&date_from=' + date_from + '&date_to=' + date_to;
    var win = window.open(url, '_blank');
    win.focus();
}

</script>
<?php
                        // zR to open all folded lines
                        // vim:ft=php:fdm=marker
                        // end of view_player_report.php

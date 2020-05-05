<?php
/**
 *   filename:   view_player_report.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2016-05-02
 *   @brief:     view for affiliate logs in affiliate sub-system
 */

if (isset($_GET['search_on_date'])) {
    $search_on_date = $_GET['search_on_date'];
} else {
    $search_on_date = isset($date_from, $date_to);
}
?>
<div class="container">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h4 class="panel-title">
                <i class="fa fa-search"></i> <?=lang("lang.search")?>
                <span class="pull-right">
                    <a data-toggle="collapse" href="#collapsePlayerReport" class="btn btn-default btn-xs"></a>
                </span>
            </h4>
        </div>
        <div id="collapsePlayerReport" class="panel-collapse">
            <div class="panel-body">
                <form id="form-filter" class="form-horizontal" method="post">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="control-label"><?=lang('report.sum02')?></label>
                            <div class="input-group">
                                <input class="form-control dateInput" id="date-range" data-start="#date_from" data-end="#date_to" data-time="true"/>
                                <input type="hidden" id="date_from" name="date_from" value="<?=@$date_from?>"/>
                                <input type="hidden" id="date_to" name="date_to" value="<?=@$date_to?>"/>
                                <span class="input-group-addon input-sm">
                                    <input type="checkbox" name="search_on_date" id="search_on_date" value="1" checked="checked"/>
                                </span>
                            </div>
                        </div>
                        <?php if ( ! $this->utils->isEnabledFeature('hide_sub_affiliates_on_affiliate')): ?>
                            <div class="col-md-6">
                                <label class="control-label" for="affiliate_username"><?=lang('Affiliate Username')?> </label>
                                <div class="input-group">
                                    <input type="text" name="affiliate_username" id="affiliate_username" class="form-control" value="<?=@$affiliate_username?>"/>
                                    <span class="input-group-addon input-sm">
                                        <input type="checkbox" name="include_all_downlines" value="true"
                                        <?php if ( ! $affiliate_username) {echo 'checked';} ?>/>
                                        <?=lang('Include All Downline Affiliates')?>
                                    </span>
                                </div>
                            </div>
                        <?php endif ?>
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
                        <div class="col-md-3">
                            <label class="control-label" for="playerlevel"><?=lang('report.pr03')?></label>
                            <select name="playerlevel" id="playerlevel" class="form-control">
                                <option value=""><?=lang('lang.selectall')?></option>
                                <?php foreach ($allLevels as $key => $value) {?>
                                <option value="<?=$value['vipsettingcashbackruleId']?>"><?=lang($value['groupName']) . ' ' . lang($value['vipLevel'])?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="control-label" for="username"><?=lang('report.pr01')?></label>
                            <input type="text" name="username" id="username" class="form-control"/>
                        </div>
                        <div class="col-md-3">
                            <label class="control-label" for="realname"><?=lang('Real Name')?></label>
                            <input type="text" name="realname" id="realname" class="form-control"/>
                        </div>
                        <div class="col-md-3">
                            <label class="control-label" for="group_by"><?=lang('report.g14')?> </label>
                            <select name="group_by" id="group_by" class="form-control">
                                <option value="player.playerId"><?=lang('report.pr01')?></option>
                                <option value="player.levelId"><?=lang('report.pr03')?></option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="control-label" for="only_show_non_zero_player"><?=lang('Only Show Non-Zero Player')?></label>
                            <input type="checkbox" name="only_show_non_zero_player" checked="checked" id="only_show_non_zero_player" class="form-control" value="true"/>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6" style="padding-top: 10px;">
                            <!-- <input type="button" value="<?=lang('lang.exporttitle')?>" class="btn btn-success btn-sm export_excel"> -->
                        </div>
                        <div class="col-md-6" style="padding-top: 10px;">
                            <input type="submit" value="<?=lang('lang.search')?>" id="search_main" class="btn btn-primary btn-sm pull-right">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h4 class="panel-title"><i class="icon-users"></i> <?=lang('report.s09')?></h4>
        </div>
        <div class="panel-body">
            <?php if ( ! $this->utils->isEnabledFeature('hide_sub_affiliates_on_affiliate')): ?>
                <div>
                    <?=lang('Included Affiliates')?>: <span id="affiliate_usernames"></span>
                </div>
            <?php endif ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="myTable">
                    <thead>
                        <tr>
                            <?php include __DIR__.'/../includes/affiliate_player_report_table_header.php'; ?>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th><?=lang('Total')?></th>
                            <th></th>
                            <?php if ($this->utils->isEnabledFeature('aff_show_real_name_on_reports')): ?>
                                <th></th>
                            <?php endif ?>
                            <th></th>
                            <th></th>
                            <th></th>
                            <?php if ($this->utils->isEnabledFeature('show_cashback_and_bonus_on_aff_player_report')): ?>
                                <th><span id="cashback-total">0.00</span></th>
                                <th><span id="bonus-total">0.00</span></th>
                            <?php endif ?>
                            <?php if ( ! $this->utils->isEnabledFeature('hide_deposit_and_withdraw_on_aff_player_report')): ?>
                                <th><span id="deposit-total">0.00</span></th>
                                <th><span id="withdrawal-total">0.00</span></th>
                            <?php endif ?>
                            <th><span id="deposit-withdrawal-total">0.00</span></th>
                            <th><span id="bet-total">0.00</span></th>
                            <th><span id="bet-plus-result">0.00</span></th>
                            <?php if ( ! $this->utils->isEnabledFeature('hide_total_win_loss_on_aff_player_report')): ?>
                                <th><span id="win-total">0.00</span></th>
                                <th><span id="loss-total">0.00</span></th>
                            <?php endif ?>
                            <th><span id="net-total">0.00</span></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="panel-footer"></div>
    </div>
</div>
<?php
$order_val = $this->utils->getConfig('default_player_report_order');
if($this->utils->getConfig('default_player_report_order') > 1){
    if($this->utils->isEnabledFeature('aff_show_real_name_on_reports')){
        $order_val += 1;
    }
    if($this->utils->isEnabledFeature('show_cashback_and_bonus_on_aff_player_report')){
        $order_val += 2;
    }
    if($this->utils->isEnabledFeature('hide_deposit_and_withdraw_on_aff_player_report')){
        $order_val -= 2;
    }
}
?>
<script type="text/javascript">
$(document).ready(function() {

    var dataTable = $('#myTable').DataTable({
        lengthMenu: [50, 100, 250, 500, 1000],
        autoWidth: false,
        <?php if($this->utils->isEnabledFeature('column_visibility_report')){ ?>
            stateSave: true,
        <?php } else { ?>
            stateSave: false,
        <?php } ?>
        searching: false,
        dom: "<'panel-body' <'pull-right'B><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
        buttons: [{
            extend: 'colvis',
            postfixButtons: [ 'colvisRestore' ]
        }],
        order: [[<?=$order_val?>, 'asc']],
        columnDefs: [
            <?php if ($this->utils->isEnabledFeature('hide_sub_affiliates_on_affiliate')): ?>
                <?php if ($this->utils->isEnabledFeature('aff_show_real_name_on_reports')): ?>
                    { visible: false, targets: [ 3 ] }
                <?php else: ?>
                    { visible: false, targets: [ 2 ] }
                <?php endif ?>
            <?php endif ?>
        ],
        // SERVER-SIDE PROCESSING
        processing: true,
        serverSide: true,
        ajax: function (data, callback, settings) {
            data.extra_search = $('#form-filter').serializeArray();
            $.post(base_url + "api/affiliate_player_reports", data, function(data) {

                if ($('#group_by').val() == 'player.playerId') {

                    // dataTable.column( 1 ).visible( true );
                    // dataTable.column( 1 ).order('asc');
                    // dataTable.column( 3 ).visible( false );

                    var regexUsername = /<[^>]*>([^<]*)<[^>]*>/;

                    for (var i = 0; i < data.data.length; i++) {
                        var username_orig = data.data[i][1];
                        regexUsername.test(username_orig);
                        var tmp = RegExp.$1;
                        data.data[i][1] = tmp;
                    }

                } else {

                    // dataTable.column( 1 ).visible( false );
                    // dataTable.column( 3 ).visible( true );
                    // dataTable.column( 3 ).order('asc');

                }

                <?php if ($this->utils->isEnabledFeature('show_cashback_and_bonus_on_aff_player_report')): ?>
                    $('#cashback-total').text(numeral(data.summary[0].cashback_total).format('0,0.00'));
                    $('#bonus-total').text(numeral(data.summary[0].bonus_total).format('0,0.00'));
                <?php endif ?>
                <?php if ( ! $this->utils->isEnabledFeature('hide_deposit_and_withdraw_on_aff_player_report')): ?>
                    $('#deposit-total').text(numeral(data.summary[0].deposit_total).format('0,0.00'));
                    $('#withdrawal-total').text(numeral(data.summary[0].withdrawal_total).format('0,0.00'));
                <?php endif ?>
                $('#deposit-withdrawal-total').text(numeral(data.summary[0].deposit_withdrawal_total).format('0,0.00'));
                $('#bet-total').text(numeral(data.summary[0].bet_total).format('0,0.00'));
                $('#bet-plus-result').text(numeral(data.summary[0].bet_plus_result_total).format('0,0.00'));
                <?php if ( ! $this->utils->isEnabledFeature('hide_total_win_loss_on_aff_player_report')): ?>
                    $('#win-total').text(numeral(data.summary[0].win_total).format('0,0.00'));
                    $('#loss-total').text(numeral(data.summary[0].loss_total).format('0,0.00'));
                <?php endif ?>
                $('#net-total').text(numeral(data.summary[0].net_total).format('0,0.00'));

                callback(data);

                if (data['affiliate_list']) {
                    $('#affiliate_usernames').html(data['affiliate_list'].join());
                }

            }, 'json');
        }
    });

    $('#form-filter').submit( function(e) {
        e.preventDefault();
        dataTable.ajax.reload();
    });

    $('#group_by').change( function() {
        $('#username').val('').prop('disabled', $(this).val() != 'player.playerId');
    });

    // $('.export_excel').click(function(){

    //     var export_url = "<?php echo site_url('export_data/affiliate_player_reports') ?>";
    //     var data = {
    //         draw: 1,
    //         start: 0,
    //         length: -1,
    //         extra_search: $('#form-filter').serializeArray(),
    //     };

    //     $.post(export_url, data, function(data) {
    //         if (data && data.success) {
    //             $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
    //         }else{
    //             alert('export failed');
    //         }
    //     });
    // });

});
</script>
<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of view_player_report.php
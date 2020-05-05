<div id="accountHistory" class="panel">
    <div class="panel-heading">
        <h1><?=lang('Account History')?></h1>
    </div>
    <div class="panel-body nopadding">
        <div class="tab-nav">
            <ul class="fm-ul nav nav-pills nav-justified" role="tablist">
                <li><a href="#ah-rebate" role="tab" data-toggle="tab" data-report_type="rebate"><?=lang('player.ui45')?></a></li>
                <li><a href="#ah-deposit" role="tab" data-toggle="tab" data-report_type="deposit"><?=lang('Deposit History')?></a></li>
                <li><a href="#ah-withdrawal" role="tab" data-toggle="tab" data-report_type="withdrawal"><?=lang('Withrawal History')?></a></li>
                <li><a href="#ah-transfer" role="tab" data-toggle="tab" data-report_type="transfer_request"><?=lang('Transfer History')?></a></li>
                <li><a href="#ah-promotion" role="tab" data-toggle="tab" data-report_type="promoHistory"><?=lang('player.ui49')?></a></li>
                <li><a href="#ah-game" role="tab" data-toggle="tab" data-report_type="game"><?=lang('Game History')?></a></li>
                <?php if(!$this->utils->isEnabledFeature('hidden_accounthistory_friend_referral_status')): ?>
                <li><a href="#ah-referral" role="tab" data-toggle="tab" data-report_type="referralFriend"><?=lang('player.friendReferralStatus')?></a></li>
                <?php endif ?>
            </ul>
        </div>
        <div class="tab-content">
            <p id="search-title"><?=lang('Filter your History')?></p>
            <div class="report-filter-from">
                <div class="row nopadding">
                    <div class="col-xs-3">
                        <label for="pageLength"><?=lang('datatable.page_length')?>:</label>
                        <select id="pageLength" name="pageLength" class="form-control">
                            <option value="5" selected="selected">5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    <div class="col-xs-5">
                        <label for="cdate"><?=lang('lang.date')?>:</label>
                        <input type="text" name="cdate" id="cdate" class="form-control dateInput" data-start="#sdate" data-end="#edate">
                        <input type="hidden" id="sdate" value="<?=date('Y-m-d 00:00:00', time())?>"/>
                        <input type="hidden" id="edate" value="<?=date('Y-m-d 23:59:59', time())?>"/>
                    </div>
                    <div class="col-xs-4">
                        <label for="search_btn">&nbsp;</label>
                        <button id="search_btn" class="btn-submit form-control" onclick="run();"><?=lang('lang.search');?></button>
                    </div>
                </div>
                <div class="row nopadding report-filter">
                    <div class="col-xs-3 report-filter filter-game input">
                        <label for="game_platform_id"><?=lang('sys.gd7')?>:</label>
                        <select id="game_platform_id" class="form-control">
                            <option value=""><?=lang('system.word57')?></option>
                            <?php foreach($game_platforms as $key => $value): ?>
                                <?php
                                    if(isset($game_wallet_settings[$key]['enabled_on_desktop']) && !$game_wallet_settings[$key]['enabled_on_desktop']){
                                        continue;
                                    }
                                ?>
                                <option value="<?=$key?>"><?=$value?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php if($this->utils->isEnabledFeature('enable_player_center_search_unsettle')) : ?>
                    <div class="col-xs-3 report-filter filter-game input">
                        <label>&nbsp;</label><br />
                        <input type="checkbox" id="searchUnsettle" value="1"/>
                        <label for="searchUnsettle"><?=lang('Search Unsettle Game')?></label>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="clearfix"></div>
            <?php include __DIR__ . '/reports/cashback.php' ?>
            <?php include __DIR__ . '/reports/deposit.php' ?>
            <?php include __DIR__ . '/reports/withdrawal.php' ?>
            <?php include __DIR__ . '/reports/transfer.php' ?>
            <?php include __DIR__ . '/reports/promotion.php' ?>
            <?php include __DIR__ . '/reports/game.php' ?>
            <?php include __DIR__ . '/reports/referral.php' ?>
        </div>
    </div>
</div>

<?php include VIEWPATH . '/resources/third_party/DataTable.php'; ?>
<?php include VIEWPATH . '/resources/third_party/DateRangePicker.php'; ?>
<script type="text/javascript">

var report_type = "<?=isset($report_type) ? $report_type : 'rebate' ?>";

$(document).ready(function () {
    $('#cdate').daterangepicker($.extend({}, daterangepicker_default_attrs, {
        "minDate": "<?=$datelimit_start->format('Y-m-d 00:00:00')?>",
        "maxDate": "<?=$datelimit_end->format('Y-m-d 23:59:59')?>"
    }), function(start, end, lbel){
        $('#sdate').val(start.format('YYYY-MM-DD HH:mm:ss'));
        $('#edate').val(end.format('YYYY-MM-DD HH:mm:ss'));
    });

    $('#accountHistory .nav li a').click(function (e) {
        e.preventDefault();

        $(this).tab('show');
        report_type = $(this).data("report_type");

        run();
    });

    $("#accountHistory li a[data-report_type=" + report_type + "]").trigger('click');

    $('#pageLength').on('change', function(){
        run();
    });

    $('#accountHistory').on('draw.t1t.player-center.report', function(){
        run();
    });
});

function run(){
    $('.report-filter>div').hide();
    $('.report-filter .filter-' + report_type).show();
    switch (report_type) {
        case "rebate":
            rebateHistory();
        break;
        case "deposit":
            depositHistory();
        break;
        case "withdrawal":
            withdrawalHistory();
        break;
        case "transfer_request":
            transferRequestHistory();
        break;
        case "promoHistory":
            promoHistory();
        break;
        case "game":
            <?php if ($this->utils->getConfig('player_game_history_date_range_restriction')): ?>
                var dateInput = $('#cdate.dateInput');

                var $restricted_range = "<?=$this->utils->getConfig('player_game_history_date_range_restriction')?>";

                if ($restricted_range == '' && !$.isNumeric($restricted_range) && !isRange)
                    return false;

                var a_day = 86400000;
                var restriction = a_day * $restricted_range;
                var start_date = new Date($('#sdate').val());
                var end_date = new Date($('#edate').val());
                var restrict_range_label = "<?=sprintf(lang("restrict_date_range_label"), $this->utils->getConfig('player_game_history_date_range_restriction'))?>";

                if($.trim(dateInput.val()) == '' || ((end_date - start_date) >= restriction)){

                    if(restrict_range_label && $.trim(restrict_range_label) !== "")
                        alert(restrict_range_label);
                    else{
                        var day_label = 'day';

                        if($restricted_range > 1) day_label = 'days'

                        alert('Please choose a date range not greater than '+ $restricted_range +' '+ day_label);
                    }
                }
                else{
                    gameHistory();
                }
            <?php else:?>
                gameHistory();
            <?php endif;?>
        break;
        case "referralFriend":
            referralFriend();
        break;

        default:
            depositHistory();
        break;
    }

}
</script>

<?php
/**
 *   filename:   settlement.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2016-05-02
 *   @brief:     view settlement information for agency sub-system
 */

// set display according to configurations
$panelOpenOrNot = $this->config->item('default_open_search_panel') ? '' : 'collapsed';
$panelDisplayMode = $this->config->item('default_open_search_panel') ? '' : 'collapse in';
if (isset($_GET['search_on_date'])) {
    $search_on_date = $_GET['search_on_date'];
} else {
    $search_on_date = false;
}
?>
    <div class="" style="margin: 4px;">
        <!-- search form {{{1 -->
        <form class="form-horizontal" id="search-form">
            <input type="hidden" name="parent_id" value="<?php echo $parent_id?>"/>
            <div class="panel panel-primary">
                <!-- panel heading {{{2 -->
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <i class="fa fa-search"></i>
                        <?=lang("lang.search")?>
                        <span class="pull-right">
                            <a data-toggle="collapse" href="#collapseAgentList"
                               class="btn btn-info btn-xs <?=$panelOpenOrNot?>">
                            </a>
                        </span>
                    </h4>
                </div>
                <!-- panel heading }}}2 -->

                <div id="collapseAgentList" class="panel-collapse <?=$panelDisplayMode?>">
                    <!-- panel body {{{2 -->
                    <div class="panel-body">
                        <!-- search on date {{{3 -->
                        <div class="row">
                            <div class="col-md-4">
                                <label class="control-label" for="search_game_date"><?=lang('Date Range');?></label>
                                <input id="search_game_date" class="form-control input-sm dateInput"
                                       data-start="#date_from" data-end="#date_to" data-time="false"/>
                                <input type="hidden" id="date_from" name="date_from" value="<?php echo $date_from; ?>" />
                                <input type="hidden" id="date_to" name="date_to"  value="<?php echo $date_to; ?>"/>
                            </div>
                            <div class="col-md-4 col-lg-4">
                                <label class="control-label"><?=lang('Status');?></label>
                                <select name="status" id="status" class="form-control input-sm">
                                    <option value="" <?=empty($conditions['status']) ? 'selected' : ''?>>
                                        --  <?=lang('None');?> --
                                    </option>
                                    <option value="current" <?=($conditions['status'] == "current") ? 'selected' : ''?> >
                                        <?=lang('agency.settlement.status.current');?>
                                    </option>
                                    <option value="settled" <?=($conditions['status'] == "settled") ? 'selected' : ''?> >
                                        <?=lang('agency.settlement.status.settled');?>
                                    </option>
                                    <option value="unsettled" <?=($conditions['status'] == "unsettled") ? 'selected' : ''?> >
                                        <?=lang('agency.settlement.status.unsettled');?>
                                    </option>
                                    <option value="closed" <?=($conditions['status'] == "closed") ? 'selected' : ''?> >
                                        <?=lang('agency.settlement.status.closed');?>
                                    </option>
                                </select>
                            </div>
                        </div>
                        <!-- search on date }}}3 -->
                        <!-- input row {{{3 -->
                        <div class="row">
                            <div class="col-md-4 col-lg-4">
                                <label class="control-label"><?=lang('Agent Username');?></label>
                                <input type="text" name="agent_name" class="form-control input-sm"
                                       placeholder=' <?=lang('Enter Agent Username');?>'
                                       value="<?php echo $conditions['agent_name'] ?: $agent_username; ?>" required/>
                            </div>
                        </div>
                        <div id="directPlayerControl" class="row" style="display:none">
                            <div class="col-md-4 col-lg-4 pt-2">
                                <input id="includeAllDownline" type="checkbox" class="" name="include_all_downline_players" value="1" checked="checked" />
                                <label for="includeAllDownline" class="control-label"><?=lang('Include all downline agent players');?></label>
                            </div>
                        </div>
                        <!-- button row {{{3 -->
                        <div class="row">
                            <div class="col-md-2 col-lg-2" style="padding-top: 20px;">
                                <input type="button" value="<?=lang('lang.reset');?>" id="reset"
                                       class="btn btn-default btn-sm">
                                <input class="btn btn-sm btn-primary" type="submit"
                                       value="<?=lang('lang.search');?>" />
                            </div>
                            <?php if ( ! $this->utils->isEnabledFeature('agent_hide_export')): ?>
                                <div class="col-md-4 pull-right" style="padding-top: 20px; text-align: right;">
                                    <input type="button" value="<?=lang('Export in Excel')?> (<?= lang('Agent') ?>)" class="btn btn-success btn-sm export_excel_agent_list">
                                    <input type="button" value="<?=lang('Export in Excel')?> (<?= lang('Subagent') ?>)" class="btn btn-success btn-sm export_excel_subagent_list">
                                </div>
                            <?php endif ?>
                        </div> <!-- button row }}}3 -->
                        <!--  modal for send settlement invoice {{{4 -->
                        <div class="modal fade in" id="send_invoice_modal"
                             tabindex="-1" role="dialog" aria-labelledby="label_send_invoice_modal">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <h4 class="modal-title" id="label_send_invoice_modal"></h4>
                                    </div>
                                    <div class="modal-body"></div>
                                    <div class="modal-footer"></div>
                                </div>
                            </div>
                        </div> <!--  modal for level name setting }}}4 -->
                    </div>
                    <!-- panel body }}}2 -->
                </div>
            </div>
        </form> <!-- end of search form }}}1 -->

        <!-- panel for settlement table {{{1 -->
        <div class="panel panel-primary panel-agent-settlement">
            <div class="panel-heading custom-ph">
                <h4 class="panel-title custom-pt">
                    <i class="icon-bullhorn"></i>
                    <?=lang('Agent Settlement List');?>
                </h4>
            </div>
            <div class="panel-body">
                <!-- settlement table {{{2 -->
                <div id="agentList" class="table-responsive">
                    <table class="table table-striped table-hover table-condensed"
                           id="agent_settlement_table" style="width:100%;">
                        <thead>
                        <tr>
                            <th rowspan="2"><?=lang('Agent Username')?></th>
                            <th rowspan="2"><?=lang('Status')?></th>
                            <th rowspan="2"><?=lang('Settlement Period')?></th>
                            <th rowspan="2"><?=lang('Date Range')?></th>
                            <th rowspan="2"><?=lang('Bets')?></th>
                            <th rowspan="2"><?=lang('Bets Without Tie')?></th>
                            <th colspan="4" class="player-data"><?=lang('Player')?></th>
                            <?php if(!$this->utils->isEnabledFeature('use_deposit_withdraw_fee')) : ?>
                                <th colspan="8" class="agent-data"><?=$agent['agent_level'] == 0 ? lang('Master Agent') : lang('Agent')?></th>
                            <?php else : ?>
                                <th colspan="9" class="agent-data"><?=$agent['agent_level'] == 0 ? lang('Master Agent') : lang('Agent')?></th>
                            <?php endif; ?>
                            <th colspan="3"><?=lang('Upper')?></th>
                            <th rowspan="2"><?=lang('Action')?></th>
                        </tr>
                        <tr>
                            <th class="player-data"><?=lang('W/L')?></th>
                            <th class="player-data"><?=lang('Platform Fee')?></th>
                            <th class="player-data"><?=lang('Rolling')?></th>
                            <th class="player-data"><?=lang('W/L Comm')?></th>
                            <th class="agent-data"><?=lang('Admin Fee')?></th>
                            <th class="agent-data"><?=lang('Cashback Fee')?></th>
                            <th class="agent-data"><?=lang('Bonus Fee')?></th>
                            <?php if(!$this->utils->isEnabledFeature('use_deposit_withdraw_fee')) : ?>
                                <th class="agent-data"><?=lang('Transaction Fee')?></th>
                            <?php else : ?>
                                <th class="agent-data"><?=lang('Deposit Fee')?></th>
                                <th class="agent-data"><?=lang('Withdraw Fee')?></th>
                            <?php endif; ?>
                            <th class="agent-data"><?=lang('W/L')?></th>
                            <th class="agent-data"><?=lang('Rolling')?></th>
                            <th class="agent-data"><?=lang('Deposit Comm')?></th>
                            <th class="agent-data"><?=lang('W/L Comm')?></th>
                            <th><?=lang('W/L')?></th>
                            <th><?=lang('Rolling')?></th>
                            <th><?=lang('W/L Comm')?></th>
                        </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th><?=lang('Total')?></th>
                                <th class="total-bets"></th>
                                <th class="total-bets-without-tie"></th>
                                <th class="player-data total-player-wl"></th>
                                <th class="player-data total-platform-fee"></th>
                                <th class="player-data total-player-rolling"></th>
                                <th class="player-data total-player-wl-comm"></th>
                                <th class="agent-data total-admin-fee"></th>
                                <th class="agent-data total-cashback-fee"></th>
                                <th class="agent-data total-bonus-fee"></th>
                                <?php if(!$this->utils->isEnabledFeature('use_deposit_withdraw_fee')) : ?>
                                    <th class="agent-data total-transaction-fee"></th>
                                <?php else : ?>
                                    <th class="agent-data total-deposit-fee"></th>
                                    <th class="agent-data total-withdraw-fee"></th>
                                <?php endif; ?>
                                <th class="agent-data total-rev-share"></th>
                                <th class="agent-data total-rolling"></th>
                                <th class="agent-data total-deposit-comm"></th>
                                <th class="agent-data total-earnings"></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <!--end of settlement table }}}2 -->
            </div>
        </div>



        <?php if ( ! isset($is_admin) || ! $is_admin): ?>
            <!-- panel for settlement table }}}1 -->
            <div class="panel panel-primary panel-subagent-settlement">
                <div class="panel-heading custom-ph">
                    <h4 class="panel-title custom-pt">
                        <i class="icon-bullhorn"></i>
                        <?=lang('Subagent Settlement List');?>
                    </h4>
                </div>
                <div class="panel-body">
                    <div id="subagentList" class="table-responsive">
                        <table class="table table-striped table-hover table-condensed"
                               id="subagent_settlement_table" style="width:100%;">
                            <thead>
                            <tr>
                                <th rowspan="2"><?=lang('Agent Username')?></th>
                                <th rowspan="2"><?=lang('Status')?></th>
                                <th rowspan="2"><?=lang('Settlement Period')?></th>
                                <th rowspan="2"><?=lang('Date Range')?></th>
                                <th rowspan="2"><?=lang('Bets')?></th>
                                <th rowspan="2"><?=lang('Bets Without Tie')?></th>
                                <th colspan="4" class="player-data"><?=lang('Player')?></th>
                                <?php if(!$this->utils->isEnabledFeature('use_deposit_withdraw_fee')) : ?>
                                    <th colspan="8" class="agent-data"><?=lang('Agent')?></th>
                                <?php else : ?>
                                    <th colspan="9" class="agent-data"><?=lang('Agent')?></th>
                                <?php endif; ?>
                                <th colspan="3"><?=lang('Upper')?></th>
                                <th rowspan="2"><?=lang('Action')?></th>
                            </tr>
                            <tr>
                                <th class="player-data"><?=lang('W/L')?></th>
                                <th class="player-data"><?=lang('Platform Fee')?></th>
                                <th class="player-data"><?=lang('Rolling')?></th>
                                <th class="player-data"><?=lang('W/L Comm')?></th>
                                <th class="agent-data"><?=lang('Admin Fee')?></th>
                                <th class="agent-data"><?=lang('Cashback Fee')?></th>
                                <th class="agent-data"><?=lang('Bonus Fee')?></th>
                                <?php if(!$this->utils->isEnabledFeature('use_deposit_withdraw_fee')) : ?>
                                    <th class="agent-data"><?=lang('Transaction Fee')?></th>
                                <?php else : ?>
                                    <th class="agent-data"><?=lang('Deposit Fee')?></th>
                                    <th class="agent-data"><?=lang('Withdraw Fee')?></th>
                                <?php endif; ?>
                                <th class="agent-data"><?=lang('W/L')?></th>
                                <th class="agent-data"><?=lang('Rolling')?></th>
                                <th class="agent-data"><?=lang('Deposit Comm')?></th>
                                <th class="agent-data"><?=lang('W/L Comm')?></th>
                                <th><?=lang('W/L')?></th>
                                <th><?=lang('Rolling')?></th>
                                <th><?=lang('W/L Comm')?></th>
                            </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th><?=lang('Subtotal')?></th>
                                    <th></th>
                                    <th></th>
                                    <th class="player-data"></th>
                                    <th class="player-data"></th>
                                    <th class="player-data"></th>
                                    <th class="player-data"></th>
                                    <th class="agent-data"></th>
                                    <th class="agent-data"></th>
                                    <th class="agent-data"></th>
                                    <th class="agent-data"></th>
                                    <?php if($this->utils->isEnabledFeature('use_deposit_withdraw_fee')) : ?>
                                        <th class="agent-data"></th>
                                    <?php endif; ?>
                                    <th class="agent-data"></th>
                                    <th class="agent-data"></th>
                                    <th class="agent-data"></th>
                                    <th class="agent-data"></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th><?=lang('Total')?></th>
                                    <th class="downline-total-bets"></th>
                                    <th class="downline-total-bets-without-tie"></th>
                                    <th class="player-data downline-total-player-wl"></th>
                                    <th class="player-data downline-total-platform-fee"></th>
                                    <th class="player-data downline-total-player-rolling"></th>
                                    <th class="player-data downline-total-player-wl-comm"></th>
                                    <th class="agent-data downline-total-admin-fee"></th>
                                    <th class="agent-data downline-total-cashback-fee"></th>
                                    <th class="agent-data downline-total-bonus-fee"></th>
                                    <?php if(!$this->utils->isEnabledFeature('use_deposit_withdraw_fee')) : ?>
                                        <th class="agent-data downline-total-transaction-fee"></th>
                                    <?php else : ?>
                                        <th class="agent-data downline-total-deposit-fee"></th>
                                        <th class="agent-data downline-total-withdraw-fee"></th>
                                    <?php endif; ?>
                                    <th class="agent-data downline-total-rev-share"></th>
                                    <th class="agent-data downline-total-rolling"></th>
                                    <th class="agent-data downline-total-deposit-comm"></th>
                                    <th class="agent-data downline-total-earnings"></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif ?>


        <div class="panel panel-primary panel-agent-settlement-detail">
            <div class="panel-heading custom-ph">
                <h4 class="panel-title custom-pt">
                    <i class="icon-bullhorn"></i>
                    <?=lang('Player Settlement Detail');?>
                    <span class="pull-right">
                        <a id="collapseSettlementDetail" data-toggle="collapse" href="#collapsableSettlementDetail" class="btn btn-info btn-xs collapsed">
                        </a>
                    </span>
                </h4>
            </div>
            <div id="collapsableSettlementDetail" class="panel-body collapse">
                <div id="agentSettlementDetail" class="table-responsive">
                    <table class="table table-striped table-hover table-condensed" id="agent_settlement_detail_table" style="width:100%;">
                        <thead>
                            <tr>
                                <th><?=lang('Agent Username')?></th>
                                <th><?=lang('Settlement Date')?></th>
                                <th><?=lang('Source')?></th>
                                <th><?=lang('Player Username')?></th>
                                <th><?=lang('Game Platform')?></th>
                                <th><?=lang('Game Type')?></th>
                                <th><?=lang('Platform Fee')?></th>
                                <th><?=lang('Admin Fee')?></th>
                                <th><?=lang('Cashback Fee')?></th>
                                <th><?=lang('Bonus Fee')?></th>
                                <?php if(!$this->utils->isEnabledFeature('use_deposit_withdraw_fee')) : ?>
                                    <th><?=lang('Transaction Fee')?></th>
                                <?php else : ?>
                                    <th><?=lang('Deposit Fee')?></th>
                                    <th><?=lang('Withdraw Fee')?></th>
                                <?php endif; ?>
                                <th><?=lang('W/L')?></th>
                                <th><?=lang('Rolling')?></th>
                                <th><?=lang('Deposit Comm')?></th>
                                <th><?=lang('W/L Comm')?></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th class="player-data"></th>
                                <th class="player-data"></th>
                                <th class="player-data"><?=lang('Subtotal')?></th>
                                <th class="player-data"></th>
                                <th class="agent-data"></th>
                                <th class="agent-data"></th>
                                <th class="agent-data"></th>
                                <th class="agent-data"></th>
                                <?php if($this->utils->isEnabledFeature('use_deposit_withdraw_fee')) : ?>
                                    <th class="agent-data"></th>
                                <?php  endif; ?>
                                <th class="agent-data"></th>
                                <th class="agent-data"></th>
                                <th class="agent-data"></th>
                                <th class="agent-data"></th>
                            </tr>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th class="player-data"></th>
                                <th class="player-data"></th>
                                <th class="player-data"><?=lang('Total')?></th>
                                <th class="player-data detail-total-platform-fee"></th>
                                <th class="agent-data detail-total-admin-fee"></th>
                                <th class="agent-data detail-total-cashback-fee"></th>
                                <th class="agent-data detail-total-bonus-fee"></th>
                                <?php if(!$this->utils->isEnabledFeature('use_deposit_withdraw_fee')) : ?>
                                    <th class="agent-data detail-total-transaction-fee"></th>
                                <?php else : ?>
                                    <th class="agent-data detail-total-deposit-fee"></th>
                                    <th class="agent-data detail-total-withdraw-fee"></th>
                                <?php endif; ?>
                                <th class="agent-data detail-total-rev-share"></th>
                                <th class="agent-data detail-total-rolling"></th>
                                <th class="agent-data detail-total-deposit-comm"></th>
                                <th class="agent-data detail-total-earnings"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>


    </div>

    <!-- JS code {{{1 -->
    <script type="text/javascript">
        $(document).ready(function(){
            <?php $agent_status = $this->session->userdata('agent_status'); ?>
            <?php if($agent_status == 'suspended') { ?>;
            set_suspended_operations();
            <?php } ?>

            $('#search-form input[type="text"]').keypress(function (e) {
                if (e.which == 13) {
                    $('#search-form').trigger('submit');
                }
            });

            function strip_tags(s) {
                var dc = $('<div />');
                $(dc).html(s);
                return $(dc).text();
            }

            var formatDecimal = function(input) {
                if(typeof(input) == 'string') {
                    input = parseFloat(input);
                }
                return input.toLocaleString(undefined, {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
            }

            $('#reset').on('click', function(data){
                $('#status').val($('#selectbox > option:first').val());
                $("input[type=text]").val('');
                var date_from = "<?=$date_from?>";
                var date_to = "<?=$date_to?>";
                $('#search_game_date').val(date_from+' to '+date_to);
                $('select[name="status"]').val('current');
            });

            // DataTable settings {{{2
            var calculateSumForFooter = function (tfoot, data) {
                var tableElement = $(tfoot).parents('table');
                var tableId = tableElement.attr('id');
                var api = this.api();
                <?php if(!$this->utils->isEnabledFeature('use_deposit_withdraw_fee')) : ?>
                    var calculateSumColumns = {
                        'agent_settlement_table' : [4, 20],
                        'agent_settlement_detail_table' : [6, 14],
                        'subagent_settlement_table' : [4, 20]
                    };
                <?php else : ?>
                    var calculateSumColumns = {
                        'agent_settlement_table' : [4, 21],
                        'agent_settlement_detail_table' : [6, 15],
                        'subagent_settlement_table' : [4, 21]
                    };
                <?php endif; ?>

                var columnStart = calculateSumColumns[tableId][0];
                var columnEnd = calculateSumColumns[tableId][1];
                for (var i = columnStart; i <= columnEnd; ++i) {
                    var sum1 = 0, sum2 = 0;
                    for (var j = 0; j < data.length; ++j) {
                        var val = strip_tags(data[j][i]);
                        val = val.replace(/,/g, '');

                        if(val.indexOf('/') > 0) {
                            vals = val.split('/');
                            val1 = parseFloat(vals[0]);
                            val2 = parseFloat(vals[1]);
                            if(isNaN(val1)) { val1 = 0; }
                            if(isNaN(val2)) { val2 = 0; }
                            sum1 += val1;
                            sum2 += val2;
                        } else {
                            var v = parseFloat(val);
                            if (isNaN(v)) { v = 0; }
                            sum1 += v;
                        }
                    }
                    sum1 = sum1.toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    if(sum2 > 0) {
                        sum2 = sum2.toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                        sum1 += ' / ' + sum2;
                    }
                    $(api.column(i).footer()).html(sum1);
                }
            };

            var dataTableAgent = $('#agent_settlement_table').DataTable({
                autoWidth: false,
                searching: false,
                info : false,
                dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
                paging : false,
                pageLength: <?=$this->utils->getDefaultItemsPerPage()?>,
                buttons: [
                    {
                        extend: 'colvis',
                        postfixButtons: [ 'colvisRestore' ]
                    }
                ],
                <?php if($this->utils->isEnabledFeature('use_deposit_withdraw_fee')) : ?>
                    columnDefs: [
                        { sortable: false, targets: [ 22 ] },
                        { visible: false, targets: [ 19,20,21 ] },
                        { className: "player-data", targets: [ 6,7,8,9 ] },
                        { className: "agent-data", targets: [ 10,11,12,13,14,15,16,17,18 ] },
                    ],
                <?php else : ?>
                    columnDefs: [
                        { sortable: false, targets: [ 21 ] },
                        { visible: false, targets: [ 18,19,20 ] },
                        { className: "player-data", targets: [ 6,7,8,9 ] },
                        { className: "agent-data", targets: [ 10,11,12,13,14,15,16,17 ] },
                    ],
                <?php endif; ?>

                "order": [ 3, 'desc' ],
                processing: true,
                serverSide: true,
                ajax: function (data, callback, settings) {
                    data.extra_search = $('#search-form').serializeArray();
                    $.post(base_url + "api/agency_settlement_wl", data, function(data) {
                        callback(data);

                        if(data.summary.length > 0 && data.summary[0].bets_display != null) {
                            $(".total-bets").text(formatDecimal(data.summary[0].bets_display));
                            $(".total-bets-without-tie").text(formatDecimal(data.summary[0].bets_except_tie_display));
                            $(".total-player-wl").text(formatDecimal(data.summary[0].result_amount));
                            $(".total-player-rolling").text(formatDecimal(data.summary[0].player_commission));
                            $(".total-player-wl-comm").text(formatDecimal(data.summary[0].player_wl_commission));
                            $(".total-platform-fee").text(formatDecimal(data.summary[0].platform_fee));
                            $(".total-admin-fee").text(formatDecimal(data.summary[0].admin));
                            $(".total-cashback-fee").text(formatDecimal(data.summary[0].rebates));
                            $(".total-bonus-fee").text(formatDecimal(data.summary[0].bonuses));
                            <?php if(!$this->utils->isEnabledFeature('use_deposit_withdraw_fee')) : ?>
                                $(".total-transaction-fee").text(formatDecimal(data.summary[0].transactions));
                            <?php else : ?>
                                $(".total-deposit-fee").text(formatDecimal(data.summary[0].deposit_fee));
                                $(".total-withdraw-fee").text(formatDecimal(data.summary[0].withdraw_fee));
                            <?php  endif; ?>
                            $(".total-rev-share").text(formatDecimal(data.summary[0].rev_share_amt));
                            $(".total-rolling").text(formatDecimal(data.summary[0].agent_commission));
                            $(".total-deposit-comm").text(formatDecimal(data.summary[0].deposit_comm));
                            $(".total-earnings").text(formatDecimal(data.summary[0].earnings));
                        } else {
                            $(".total-bets, .total-bets-without-tie, .total-player-wl, .total-player-rolling, .total-player-wl-comm, .total-platform-fee, .total-admin-fee, .total-cashback-fee, .total-bonus-fee, .total-transaction-fee, .total-deposit-fee, .total-withdraw-fee, .total-rev-share, .total-rolling, .total-deposit-comm, .total-earnings").text(formatDecimal(0));
                        }
                    },'json')
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
                footerCallback: calculateSumForFooter
            }); // DataTable settings }}}2

            var dataTableAgentDetail = $('#agent_settlement_detail_table').DataTable({
                autoWidth: false,
                searching: false,
                dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
                pageLength: <?=$this->utils->getDefaultItemsPerPage()?>,
                buttons: [
                    {
                        extend: 'colvis',
                        postfixButtons: [ 'colvisRestore' ]
                    }
                ],
                <?php if($this->utils->isEnabledFeature('use_deposit_withdraw_fee')) : ?>
                    columnDefs: [
                        { className: "player-data", targets: [ 3,4,5,6 ] },
                        { className: "agent-data", targets: [ 7,8,9,10,11,12,13,14,15 ] },
                    ],
                <?php else : ?>
                    columnDefs: [
                        { className: "player-data", targets: [ 3,4,5,6 ] },
                        { className: "agent-data", targets: [ 7,8,9,10,11,12,13,14 ] },
                    ],
                <?php endif; ?>
                "order": [ 1, 'desc' ],
                processing: true,
                serverSide: true,
                ajax: function (data, callback, settings) {
                    data.extra_search = $('#search-form').serializeArray();
                    $.post(base_url + "api/agency_settlement_detail_wl", data, function(data) {
                        callback(data);

                        if(data.summary.length > 0 && data.summary[0].admin != null) {
                            $(".detail-total-platform-fee").text(formatDecimal(data.summary[0].platform_fee));
                            $(".detail-total-admin-fee").text(formatDecimal(data.summary[0].admin));
                            $(".detail-total-cashback-fee").text(formatDecimal(data.summary[0].rebates));
                            $(".detail-total-bonus-fee").text(formatDecimal(data.summary[0].bonuses));
                            <?php if(!$this->utils->isEnabledFeature('use_deposit_withdraw_fee')) : ?>
                                $(".detail-total-transaction-fee").text(formatDecimal(data.summary[0].transactions));
                            <?php else : ?>
                                $(".detail-total-deposit-fee").text(formatDecimal(data.summary[0].deposit_fee));
                                $(".detail-total-withdraw-fee").text(formatDecimal(data.summary[0].withdraw_fee));
                            <?php  endif; ?>
                            $(".detail-total-rev-share").text(formatDecimal(data.summary[0].rev_share_amt));
                            $(".detail-total-rolling").text(formatDecimal(data.summary[0].agent_commission));
                            $(".detail-total-deposit-comm").text(formatDecimal(data.summary[0].deposit_comm));
                            $(".detail-total-earnings").text(formatDecimal(data.summary[0].earnings));
                        } else {
                            $(".detail-total-platform-fee, .detail-total-admin-fee, .detail-total-cashback-fee, .detail-total-bonus-fee, .detail-total-transaction-fee, .detail-total-deposit-fee, .detail-total-withdraw-fee, .detail-total-rev-share, .detail-total-rolling, .detail-total-deposit-comm, .detail-total-earnings").text(formatDecimal(0));
                        }
                    },'json')
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
                footerCallback: calculateSumForFooter
            });


             // export_excel handler
            $('.export_excel').click(function(){

                var d = {'extra_search':$('#search-form').serializeArray(), 'draw':1, 'length':-1, 'start':0};
                var export_url = '<?php echo site_url('export_data/agency_settlement_list_wl') ?>';
                utils.safelog(d);
                $.post(export_url, d, function(data){
                    utils.safelog(data);

                    //create iframe and set link
                    if(data && data.success){
                        $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
                    }else{
                        alert('export failed');
                    }
                });
            }); // End of export_excel handler



            // DataTable settings {{{2
            var dataTableSubAgent = $('#subagent_settlement_table').DataTable({
                autoWidth: false,
                searching: false,
                dom: "<'panel-body'<'pull-right'B><'pull-right progress-container hidden-xs'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
                pageLength: <?=$this->utils->getDefaultItemsPerPage()?>,
                /*
                 "responsive": {
                 details: {
                 type: 'column'
                 }
                 },
                 */
                buttons: [
                    {
                        extend: 'colvis',
                        postfixButtons: [ 'colvisRestore' ]
                    }
                ],
                <?php if($this->utils->isEnabledFeature('use_deposit_withdraw_fee')) : ?>
                    columnDefs: [
                        { sortable: false, targets: [ 22 ] },
                        { visible: false, targets: [ 19,20,21 ] },
                        { className: "player-data", targets: [ 6,7,8,9 ] },
                        { className: "agent-data", targets: [ 10,11,12,13,14,15,16,17,18 ] },
                    ],
                <?php else : ?>
                    columnDefs: [
                        { sortable: false, targets: [ 21 ] },
                        { visible: false, targets: [ 18,19,20 ] },
                        { className: "player-data", targets: [ 6,7,8,9 ] },
                        { className: "agent-data", targets: [ 10,11,12,13,14,15,16,17 ] },
                    ],
                <?php endif; ?>

                "order": [ 3, 'desc' ],
                processing: true,
                serverSide: true,
                ajax: function (data, callback, settings) {
                    data.extra_search = $('#search-form').serializeArray();
                    $.post(base_url + "api/agency_settlement_wl/only_subagent", data, function(data) {
                        callback(data);
                        if(data.summary.length > 0 && data.summary[0].bets_display != null) {
                            $(".downline-total-bets").text(formatDecimal(data.summary[0].bets_display));
                            $(".downline-total-bets-without-tie").text(formatDecimal(data.summary[0].bets_except_tie_display));
                            $(".downline-total-player-wl").text(formatDecimal(data.summary[0].result_amount));
                            $(".downline-total-player-rolling").text(formatDecimal(data.summary[0].player_commission));
                            $(".downline-total-player-wl-comm").text(formatDecimal(data.summary[0].player_wl_commission));
                            $(".downline-total-platform-fee").text(formatDecimal(data.summary[0].platform_fee));
                            $(".downline-total-admin-fee").text(formatDecimal(data.summary[0].admin));
                            $(".downline-total-cashback-fee").text(formatDecimal(data.summary[0].rebates));
                            $(".downline-total-bonus-fee").text(formatDecimal(data.summary[0].bonuses));
                            <?php if(!$this->utils->isEnabledFeature('use_deposit_withdraw_fee')) : ?>
                                $(".downline-total-transaction-fee").text(formatDecimal(data.summary[0].transactions));
                            <?php else : ?>
                                $(".downline-total-deposit-fee").text(formatDecimal(data.summary[0].deposit_fee));
                                $(".downline-total-withdraw-fee").text(formatDecimal(data.summary[0].withdraw_fee));
                            <?php  endif; ?>
                            $(".downline-total-rev-share").text(formatDecimal(data.summary[0].rev_share_amt));
                            $(".downline-total-rolling").text(formatDecimal(data.summary[0].agent_commission));
                            $(".downline-total-deposit-comm").text(formatDecimal(data.summary[0].deposit_comm));
                            $(".downline-total-earnings").text(formatDecimal(data.summary[0].earnings));
                        } else {
                            $(".downline-total-bets, .downline-total-bets-without-tie, .downline-total-player-wl, .downline-total-player-rolling, .downline-total-player-wl-comm, .downline-total-platform-fee, .downline-total-admin-fee, .downline-total-cashback-fee, .downline-total-bonus-fee, .downline-total-transaction-fee, .downline-total-deposit-fee, .downline-total-withdraw-fee, .downline-total-rev-share, .downline-total-rolling, .downline-total-deposit-comm, .downline-total-earnings").text(formatDecimal(0));
                        }
                        set_agent_operations();
                    },'json')
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
                footerCallback: calculateSumForFooter
            }); // DataTable settings }}}2


            $('#search-form').submit( function(e) {
                e.preventDefault();
                dataTableAgent.ajax.reload();
                dataTableAgentDetail.ajax.reload();
                dataTableSubAgent.ajax.reload();
            });


            $('.export_excel_agent_list').click(function(){

                <?php if ($this->utils->isAgencySubProject()): ?>
                if (agent_suspended) {
                    return false;
                }
                <?php endif ?>
                // utils.safelog(dataTable.columns());

                var d = {'extra_search':$('#search-form').serializeArray(), 'draw':1, 'length':-1, 'start':0};
                var export_url = '<?php echo site_url('export_data/agency_settlement_list_wl') ?>';
                // utils.safelog(d);
                //$.post(site_url('/export_data/agency_settlement_list'), d, function(data){
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

            $('.export_excel_subagent_list').click(function(){

                <?php if ($this->utils->isAgencySubProject()): ?>
                if (agent_suspended) {
                    return false;
                }
                <?php endif ?>

                var d = {'extra_search':$('#search-form').serializeArray(), 'draw':1, 'length':-1, 'start':0};
                var export_url = '<?php echo site_url('export_data/agency_settlement_list_wl/only_subagent') ?>';
                $.post(export_url, d, function(data){
                    //create iframe and set link
                    if(data && data.success){
                        $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
                    }else{
                        alert('export failed');
                    }
                });
            });

            // Sub-agent report: Click on agent name link to jump to that agent's settlement report page
            $(document).on('click', '.goto-agent', function(){
                $('#search-form input[name="agent_name"]').val($(this).text());
                $('#search-form input[type="submit"]').click();
            });
            // Agent report: Click on agent name link to open a new window to that agent's player detail report page
            $(document).on('click', '.goto-player', function(){
                var queryString = '?' + $('#search-form').serialize();
                queryString += '&report=player-detail';
                window.open(queryString, $(this).text() + '-player-detail');
            });

            <?php if(isset($_GET['report']) && $_GET['report'] == 'player-detail') : ?>
            // Hide agent and subagent settlement reports, show player detail reports
            $('.panel-agent-settlement').hide();
            $('.panel-subagent-settlement').hide();
            $('#collapsableSettlementDetail').removeClass('collapse');
            $('#collapseSettlementDetail').hide();
            $('#directPlayerControl').show();
            <?php endif; ?>
            $(document).on('mouseenter', '[data-toggle="tooltip"]', function() {
                 $(this).tooltip('show');
            });
        });
    </script>
    <!-- JS code }}}1 -->
    <style>
    .player-data {
        background-color: #bdf;
    }
    .agent-data {
        background-color: #cff;
    }
    table.dataTable tfoot th {
        padding: 8px 8px 8px 4px;
    }
    </style>

<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of settlement.php

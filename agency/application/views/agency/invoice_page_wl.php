<style>
    tfoot{
        background-color: #2c3e50;
        color: white;
    }
    .table th{
        text-align: center;
    }
</style>

<div class="content-container" style="width: 100%">

    <form class="form-horizontal" id="search-form">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <i class="fa fa-search"></i> <?=lang("lang.search")?>
                    <span class="pull-right">
                        <a data-toggle="collapse" href="#collapseViewGameLogs"
                           class="btn btn-info btn-xs <?=$this->config->item('default_open_search_panel') ? '' : 'collapsed'?>">
                        </a>
                    </span>
                    <!--
                    <span class="pull-right m-r-10">
                        <label class="checkbox-inline">
                            <input type="checkbox" id="gametype" value="gametype" onclick="checkSearchGameLogs(this.value);"/>
                            <?php echo lang('Game Type');?>
                        </label>
                    </span> -->
                    <?php //include __DIR__ . "/../includes/report_tools.php" ?>
                </h4>
            </div>

            <div id="collapseViewGameLogs" class="panel-collapse <?=$this->config->item('default_open_search_panel') ? 'collapse in' : ''?>">
                <div class="panel-body">
                    <div class="col-md-6">
                        <label class="control-label" for="search_game_date"><?=lang('Date Range');?></label>
                        <input id="search_game_date" class="form-control input-sm dateInput"
                               data-start="#date_from" data-end="#date_to" data-time="true"/>
                        <input type="hidden" id="date_from" name="date_from" value="<?php echo $date_from; ?>" />
                        <input type="hidden" id="date_to" name="date_to"  value="<?php echo $date_to; ?>"/>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label" for="invoice_id"><?=lang('Invoice')?></label>
                            <select class="form-control input-sm" name="invoice_id">
                                <option value =""  ><?=lang("lang.selectall")?> </option>
                                <?php foreach($invoices as $invoice): ?>
                                    <option value ="<?php echo $invoice->id?>" <?php echo $invoice->id == $conditions['invoice_id'] ? 'selected' : '' ?> ><?php echo "Invoice {$invoice->id} For {$invoice->agent_name} Between {$invoice->settlement_date_from} ~ {$invoice->settlement_date_to}" ?> </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>



                <div class="panel-footer text-right">
                    <input type="submit" class="btn btn-primary btn-sm" id="btn-submit" value="<?php echo lang('Search'); ?>" >
                </div>
            </div>


        </div>

    </form>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h4 class="panel-title">
                <?=lang('Settlement Report');?>
            </h4>
        </div>
        <div class="table-responsive">
            <table class="table table-condensed table-bordered">
                <thead>
                <tr>
                    <th rowspan="2"><?=lang('Agent Username')?></th>
                    <!-- <th><?=lang('settlement_period')?></th> -->
                    <th rowspan="2"><?=lang('Status')?></th>
                    <th rowspan="2"><?=lang('Date Range')?></th>
                    <th rowspan="2"><?=lang('rev_share')?></th>
                    <!-- <th><?=lang('rolling_comm')?></th> -->
                    <!-- <th><?=lang('rolling_comm_basis')?></th> -->
                    <!-- <th rowspan="2"><?=lang('Real Bets')?></th> -->
                    <th rowspan="2"><?=lang('Bets')?></th>
                    <!-- <th><?=lang('tie_bets')?></th> -->
                    <!-- <th><?=lang('result_amount')?></th> -->
                    <!-- <th><?=lang('lost_bets')?></th> -->
                    <!-- <th><?=lang('bets_except_tie')?></th> -->
                    <!-- <th><?=lang('roll_comm_income')?></th> -->
                    <!-- <th><?=lang('wins')?></th> -->
                    <!-- <th><?=lang('bonuses')?></th> -->
                    <!-- <th><?=lang('rebates')?></th> -->
                    <!-- <th><?=lang('net_gaming')?></th> -->
                    <!-- <th><?=lang('rev_share_amt')?></th> -->
                    <th colspan="3"><?=lang('Player')?></th>
                    <th colspan="3"><?=$agent['agent_level'] == 0 ? lang('Master') : lang('Agent')?></th>
                    <th colspan="3"><?=lang('Upper')?></th>
                    <th rowspan="2"><?=lang('action')?></th>
                </tr>
                <tr>
                    <th><?=lang('W/L')?></th>
                    <th><?=lang('Rolling')?></th>
                    <th><?=lang('W/L Comm')?></th>
                    <th><?=lang('W/L')?></th>
                    <th><?=lang('Rolling')?></th>
                    <th><?=lang('W/L Comm')?></th>
                    <th><?=lang('W/L')?></th>
                    <th><?=lang('Rolling')?></th>
                    <th><?=lang('W/L Comm')?></th>
                    <!-- <th><?=lang('created_on')?></th> -->
                    <!-- <th><?=lang('updated_on')?></th> -->
                </tr>
                </thead>
                <tbody>
                <?php if (empty($agent_rows)): ?>
                    <tr>
                        <td align="center" colspan="11"><?=lang('No data available in table')?></td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($agent_rows as $row): ?>
                        <tr>
                            <td align="center"><?=$row['agent_username']?></td>
                            <td align="center"><?=$row['status']?></td>

                            <td align="center"><?=$row['settlement_date_from']?> ~ <?=$row['settlement_date_to']?></td>
                            <td align="right"><?=number_format($row['rev_share'], 2) . '%' ?></td>

                            <td align="right"><?=$this->utils->formatColorCurrency($row['bets'])?></td>

                            <!-- Player W/L -->         <td align="right"><?=$this->utils->formatColorCurrency($row['result_amount'])?></td>
                            <!-- Player Com -->         <td align="right"><?=$this->utils->formatColorCurrency($row['player_commission'])?></td>
                            <!-- Player W/L Com -->     <td align="right"><?=$this->utils->formatColorCurrency($row['result_amount'] + $row['player_commission'])?></td>
                            <!-- Agent W/L -->          <td align="right"><?=$this->utils->formatColorCurrency($row['rev_share_amt'])?></td>
                            <!-- Agent Com -->          <td align="right"><?=$this->utils->formatColorCurrency($row['agent_commission'])?></td>
                            <!-- Agent W/L Com -->      <td align="right"><?=$this->utils->formatColorCurrency($row['rev_share_amt'] + $row['agent_commission'])?></td>
                            <!-- Upper W/L -->          <td align="right"><?= $agent['agent_level'] == 0 ? lang('N/A') : $this->utils->formatColorCurrency( - $row['result_amount'] - $row['rev_share_amt'])?></td>
                            <!-- Upper Com -->          <td align="right"><?= $agent['agent_level'] == 0 ? lang('N/A') : $this->utils->formatColorCurrency( - $row['player_commission'] - $row['agent_commission'])?></td>
                            <!-- Upper W/L Com -->      <td align="right"><?= $agent['agent_level'] == 0 ? lang('N/A') : $this->utils->formatColorCurrency(( - $row['result_amount'] - $row['rev_share_amt']) + ( - $row['player_commission'] - $row['agent_commission']))?></td>

                            <td>
                                <?php if($row['status'] != 'settled'):?>
                                    <a href="javascript:void(0)" class="agent-oper" data-toggle="tooltip" title="" onclick="do_settlement_wl(<?=$row['user_id']?>, <?="'{$row['status']}'" ?>, <?="'{$row['settlement_date_from']}'" ?>, <?="'{$row['settlement_date_to']}'" ?>)" data-original-title="Do Settlement"><span class="glyphicon glyphicon-credit-card text-success"></span></a>
                                <?php endif;?>
                                <a href="javascript:void(0)" class="agent-oper" data-toggle="tooltip" title="" onclick="settlement_send_invoice_wl(<?=$row['invoice_id']?>)" data-original-title="Send Invoice"><span class="glyphicon glyphicon-envelope text-info"></span></a>
                                <!--                                <a href="javascript:void(0)" class="agent-oper" data-toggle="tooltip" title="Freeze this item" onclick="freeze_settlement(10)"><span class="glyphicon glyphicon-ban-circle text-danger"></span></a>-->
                            </td>

                            <!-- <td align="right"><?#=$row['created_on']?></td> -->
                            <!-- <td align="right"><?#=$row['updated_on']?></td> -->
                        </tr>
                    <?php endforeach ?>
                <?php endif ?>
                </tbody>


            </table>
        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h4 class="panel-title">
                <?=lang('Player Win / Loss Report');?>
            </h4>
        </div>
        <div class="table-responsive">
            <table class="table table-condensed table-bordered">
                <thead>
                <tr>
                    <th rowspan="2"><?=lang('Player Username')?></th>
                    <!-- <th><?=lang('settlement_period')?></th> -->
                    <!-- <th rowspan="2"><?=lang('Date Range')?></th> -->
                    <!-- <th><?=lang('rev_share')?></th> -->
                    <!-- <th><?=lang('rolling_comm')?></th> -->
                    <!-- <th><?=lang('rolling_comm_basis')?></th> -->
                    <!-- <th rowspan="2"><?=lang('Real Bets')?></th> -->
                    <th rowspan="2"><?=lang('Bets')?></th>
                    <!-- <th><?=lang('tie_bets')?></th> -->
                    <!-- <th><?=lang('result_amount')?></th> -->
                    <!-- <th><?=lang('lost_bets')?></th> -->
                    <!-- <th><?=lang('bets_except_tie')?></th> -->
                    <!-- <th><?=lang('roll_comm_income')?></th> -->
                    <!-- <th><?=lang('wins')?></th> -->
                    <!-- <th><?=lang('bonuses')?></th> -->
                    <!-- <th><?=lang('rebates')?></th> -->
                    <!-- <th><?=lang('net_gaming')?></th> -->
                    <!-- <th><?=lang('rev_share_amt')?></th> -->
                    <th colspan="3"><?=lang('Player')?></th>
                    <th colspan="3"><?=$agent['agent_level'] == 0 ? lang('Master') : lang('Agent')?></th>
                    <th colspan="3"><?=lang('Upper')?></th>
                </tr>
                <tr>
                    <th><?=lang('W/L')?></th>
                    <th><?=lang('Rolling')?></th>
                    <th><?=lang('W/L Comm')?></th>
                    <th><?=lang('W/L')?></th>
                    <th><?=lang('Rolling')?></th>
                    <th><?=lang('W/L Comm')?></th>
                    <th><?=lang('W/L')?></th>
                    <th><?=lang('Rolling')?></th>
                    <th><?=lang('W/L Comm')?></th>
                    <!-- <th><?=lang('created_on')?></th> -->
                    <!-- <th><?=lang('updated_on')?></th> -->
                </tr>
                </thead>
                <tbody>
                <?php if (empty($rows)): ?>
                    <tr>
                        <td align="center" colspan="11"><?=lang('No data available in table')?></td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?=$row['player_username']?></td>
                            <!-- <td><?#=$row['settlement_period']?></td> -->
                            <!-- <td><?=$row['settlement_date_from']?> ~ <?=$row['settlement_date_to']?></td> -->
                            <!-- <td align="right"><?#=number_format($row['rev_share'], 2)?></td> -->
                            <!-- <td align="right"><?#=number_format($row['rolling_comm'], 2)?></td> -->
                            <!-- <td><?#=$row['rolling_comm_basis']?></td> -->
                            <!-- <td align="right"><?=number_format($row['real_bets'], 2)?></td> -->
                            <td align="right"><?=$this->utils->formatColorCurrency($row['bets'])?></td>
                            <!-- <td align="right"><?#=number_format($row['tie_bets'], 2)?></td> -->
                            <!-- <td align="right"><?#=number_format($row['result_amount'], 2)?></td> -->
                            <!-- <td align="right"><?#=number_format($row['lost_bets'], 2)?></td> -->
                            <!-- <td align="right"><?#=number_format($row['bets_except_tie'], 2)?></td> -->
                            <!-- <td align="right"><?#=number_format($row['roll_comm_income'], 2)?></td> -->
                            <!-- <td align="right"><?#=number_format($row['wins'], 2)?></td> -->
                            <!-- <td align="right"><?#=number_format($row['bonuses'], 2)?></td> -->
                            <!-- <td align="right"><?#=number_format($row['rebates'], 2)?></td> -->

                            <!-- Player W/L -->         <td align="right"><?=$this->utils->formatColorCurrency($row['result_amount'])?></td>
                            <!-- Player Com -->         <td align="right"><?=$this->utils->formatColorCurrency($row['player_commission'])?></td>
                            <!-- Player W/L Com -->     <td align="right"><?=$this->utils->formatColorCurrency($row['result_amount'] + $row['player_commission'])?></td>
                            <!-- Agent W/L -->          <td align="right"><?=$this->utils->formatColorCurrency($row['rev_share_amt'])?></td>
                            <!-- Agent Com -->          <td align="right"><?=$this->utils->formatColorCurrency($row['agent_commission'])?></td>
                            <!-- Agent W/L Com -->      <td align="right"><?=$this->utils->formatColorCurrency($row['rev_share_amt'] + $row['agent_commission'])?></td>
                            <!-- Upper W/L -->          <td align="right"><?= $agent['agent_level'] == 0 ? lang('N/A') : $this->utils->formatColorCurrency( - $row['result_amount'] - $row['rev_share_amt'])?></td>
                            <!-- Upper Com -->          <td align="right"><?= $agent['agent_level'] == 0 ? lang('N/A') : $this->utils->formatColorCurrency( - $row['player_commission'] - $row['agent_commission'])?></td>
                            <!-- Upper W/L Com -->      <td align="right"><?= $agent['agent_level'] == 0 ? lang('N/A') : $this->utils->formatColorCurrency(( - $row['result_amount'] - $row['rev_share_amt']) + ( - $row['player_commission'] - $row['agent_commission']))?></td>

                            <!-- <td align="right"><?#=$row['created_on']?></td> -->
                            <!-- <td align="right"><?#=$row['updated_on']?></td> -->
                        </tr>
                    <?php endforeach ?>
                <?php endif ?>
                </tbody>

                <tfoot>
                <tr>
                    <td><?=lang('Total')?></td>

                    <!-- <td align="right"><?=number_format($summary['real_bets'], 2)?></td> -->
                    <td align="right"><?=$this->utils->formatColorCurrency($summary['bets'])?></td>
                    <td align="right"><?=$this->utils->formatColorCurrency($summary['result_amount'])?></td>
                    <td align="right"><?=$this->utils->formatColorCurrency($summary['player_commission'])?></td>
                    <td align="right"><?=$this->utils->formatColorCurrency($summary['player_wl_com'])?></td>
                    <td align="right"><?=$this->utils->formatColorCurrency($summary['rev_share_amt'])?></td>
                    <td align="right"><?=$this->utils->formatColorCurrency($summary['agent_commission'])?></td>
                    <td align="right"><?=$this->utils->formatColorCurrency($summary['agent_wl_com'])?></td>
                    <td align="right"><?= $agent['agent_level'] == 0 ? lang('N/A') : $this->utils->formatColorCurrency($summary['upper_wl'])?></td>
                    <td align="right"><?= $agent['agent_level'] == 0 ? lang('N/A') : $this->utils->formatColorCurrency($summary['upper_com'])?></td>
                    <td align="right"><?= $agent['agent_level'] == 0 ? lang('N/A') : $this->utils->formatColorCurrency($summary['upper_wl_com'])?></td>

                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
    function show_player_game_history(player_username, by_date_from, by_date_to){
        var dst_url = base_url + "agency/game_history?by_username=" + player_username + "&by_date_from=" + by_date_from + "&by_date_to=" + by_date_to;
        window.location = dst_url;
    }

    function show_agent_players_win_loss(agent_id, date_from, date_to, status){
        var dst_url = base_url + 'agency/settlement_wl?agent_id='+agent_id+'&date_from='+date_from+'&date_to='+date_to;
        window.location = dst_url;
    }
</script>

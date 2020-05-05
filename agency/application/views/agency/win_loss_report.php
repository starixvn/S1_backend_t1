<style>
    tfoot{
        background-color: #2c3e50;
        color: white;
    }
    .table th{
        text-align: center;
    }
</style>

<div class="content-container">

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
                    <div class="col-md-2 has-error">
                        <label class="control-label"><?=lang('Current Agent')?></label>
                        <input type="text" class="form-control input-sm text-danger" value="<?=$agent['agent_name']?>" readonly="readonly">
                    </div>
                    <div class="col-md-2">
                        <label class="control-label" for="by_username"><?=lang('Agent Username');?> </label>
                        <input type="text" name="agent_username" id="agent_username" class="form-control input-sm"
                               value="<?php echo $agent_username; ?>" />
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
                <?=lang('Agent Win / Loss Report');?>
            </h4>
        </div>
        <div class="table-responsive">
            <table class="table table-condensed table-bordered">
                <thead>
                    <tr>
                        <th rowspan="2"><?=lang('Agent Username')?></th>
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
                    <?php if (empty($agent_rows)): ?>
                        <tr>
                            <td align="center" colspan="11"><?=lang('No data available in table')?></td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($agent_rows as $row): ?>
                            <tr>
                                <td><?=$row['agent_username']?></td>
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
                    <th><?=lang('Total')?></th>

                    <!-- <td align="right"><?=number_format($agent_summary['real_bets'], 2)?></td> -->
                    <td align="right"><?=$this->utils->formatColorCurrency($agent_summary['bets'])?></td>
                    <td align="right"><?=$this->utils->formatColorCurrency($agent_summary['result_amount'])?></td>
                    <td align="right"><?=$this->utils->formatColorCurrency($agent_summary['player_commission'])?></td>
                    <td align="right"><?=$this->utils->formatColorCurrency($agent_summary['player_wl_com'])?></td>
                    <td align="right"><?=$this->utils->formatColorCurrency($agent_summary['rev_share_amt'])?></td>
                    <td align="right"><?=$this->utils->formatColorCurrency($agent_summary['agent_commission'])?></td>
                    <td align="right"><?=$this->utils->formatColorCurrency($agent_summary['agent_wl_com'])?></td>
                    <td align="right"><?= $agent['agent_level'] == 0 ? lang('N/A') : $this->utils->formatColorCurrency($agent_summary['upper_wl'])?></td>
                    <td align="right"><?= $agent['agent_level'] == 0 ? lang('N/A') : $this->utils->formatColorCurrency($agent_summary['upper_com'])?></td>
                    <td align="right"><?= $agent['agent_level'] == 0 ? lang('N/A') : $this->utils->formatColorCurrency($agent_summary['upper_wl_com'])?></td>

                </tr>
                </tfoot>
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
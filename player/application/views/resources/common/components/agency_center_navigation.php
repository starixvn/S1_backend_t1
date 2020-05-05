<?php
// $base_url = $this->utils->getSystemUrl('agency');
?>
<ul class="agency-menu-nav agency-center-navigation">
    <li>
        <a href="<?=$this->utils->getSystemUrl('agency','/agency/players_list')?>" target="_blank">
            <span class="fa fa-users"></span><?=lang('Agents List');?>
        </a>
    </li>
    <li>
        <a href="<?=$this->utils->getSystemUrl('agency','/agency/tracking_link_list')?>" target="_blank">
            <span class="fa fa-weixin"></span><?=lang('WeChat Links');?>
        </a>
    </li>
    <li>
        <a href="<?=$this->utils->getSystemUrl('agency','/agency/agency_player_report')?>" target="_blank">
            <span class="fa fa-shield"></span><?=lang('Agent Report');?>
        </a>
    </li>
    <li>
        <a href="<?=$this->utils->getSystemUrl('agency','/agency/credit_transactions')?>" target="_blank">
            <span class="icon-refferal"></span><?=lang('Credit Transactions');?>
        </a>
    </li>
    <li>
        <a href="<?=$this->utils->getSystemUrl('agency','/agency/game_history')?>" target="_blank">
            <span class="fa fa-star"></span><?=lang('Game History');?>
        </a>
    </li>
    <li>
        <a href="<?=$this->utils->getSystemUrl('agency','/agency/agency_game_report')?>" target="_blank">
            <span class="glyphicon glyphicon-tag"></span><?=lang('report.s07');?>
        </a>
    </li>
    <?php if($this->utils->isEnabledFeature('enabled_lottery_agent_navigation')) : ?>
    <li>
        <a href="/player_center2/lottery/agent" data-embedded="1" data-target="player">
            <span class="glyphicon glyphicon-glass"></span><?php echo lang("Lottery Agent") ?>
        </a>
    </li>
    <?php endif; ?>
    <li>
        <a href="/player_center2/lottery/salary" data-embedded="1" data-target="player">
            <span class="fa fa-dollar"></span><?php echo lang("Daily Salary") ?>
        </a>
    </li>
</ul>

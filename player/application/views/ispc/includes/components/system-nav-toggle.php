<?php
$system_hosts = $this->utils->getSystemUrls();

$agency_agent = $this->CI->load->get_var('player_binding_agency_agent');
?>
<div class="system-navigation-container">
    <div class="player-center-navigation-container pull-left">
        <h4 class="navigation-title"><i class="fa fa-users" aria-hidden="true"></i><?=lang('Player Center')?></h4>
        <div class="navigation-content">
            <?php include VIEWPATH . '/resources/common/components/player_center_navigation.php';?>
        </div>
    </div>

<?php
if(!empty($agency_agent)){
?>
    <div class="player-center-navigation-container pull-left <?=(empty($agency_agent)) ? 'hidden' : ''?>">
        <h4 class="navigation-title"><i class="fa fa-user" aria-hidden="true"></i><?=lang('Agency Center')?></h4>
        <div class="navigation-content">
            <?php include VIEWPATH . '/resources/common/components/agency_center_navigation.php';?>
        </div>
    </div>
<?php
}
?>
    <div class="service-center-navigation-container pull-left">
        <h4 class="navigation-title"><i class="fa fa-bank" aria-hidden="true"></i><?=lang('Cashier Center')?></h4>
        <div class="navigation-content">
            <ul class="list-unstyled">
                <li>
                    <a href="<?=$system_hosts['player']?>/player_center2/deposit"><i class="fa fa-credit-card-alt" aria-hidden="true"></i><?= lang('Deposit') ?></a>
                </li>
                <li>
                    <a href="<?=$system_hosts['player']?>/player_center2/withdraw"><i class="fa fa-credit-card" aria-hidden="true"></i><?= lang('Withdrawal') ?></a>
                </li>
                <li>
                    <a href="<?=$system_hosts['player']?>/player_center2/bank_account"><i class="fa fa-university" aria-hidden="true"></i><?= lang('cashier.16') ?></a>
                </li>
            </ul>
        </div>
    </div>
</div>
<?php
    $enable_tier = false;
    if(isset($settings['enable_commission_by_tier']) && $settings['enable_commission_by_tier'] == true){
        $enable_tier = true;
    }
?>
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th><?=lang('Game Platform')?></th>
                <th class="text-right"><?=lang('Bet Percentage')?></th>
                <th class="text-right"><?=lang('Total Bet')?></th>
                <th class="text-right"><?=lang('Total Bet Result')?></th>
                <th class="text-right"><?=lang('Win Amount')?></th>
                <th class="text-right"><?=lang('Loss Amount')?></th>
                <th class="text-right"><?=lang('Result Amount')?></th>
                <?php if($this->utils->isEnabledFeature('enable_rake_column_in_commission_details')){?>
                    <th class="text-right"><?=lang('Total Rake')?></th>
                <?php } ?>
                <th class="text-right"><?=lang('Platform Fee Rate')?></th>
                <th class="text-right"><?=lang('Platform Fee Amount')?></th>
                <th class="text-right"><?=lang('Gross Revenue')?></th>
                <th class="text-right"><?=lang('Admin Fee Rate')?></th>
                <th class="text-right"><?=lang('Admin Fee Amount')?></th>
                <th class="text-right"><?=lang('Other Fees')?></th>
                <th class="text-right"><?=lang('Net Revenue')?></th>
                <?php if( !$enable_tier ) { ?>
                <th class="text-right"><?=lang('Commission Rate')?></th>
                <th class="text-right"><?=lang('Commission Amount')?></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($earnings['details']['game_platforms'])): ?>
                <?php foreach ($earnings['details']['game_platforms'] as $game_platform): ?>
                    <tr>
                        <td><?=$this->external_system->getNameById($game_platform['game_platform_id'])?></td>
                        <td align="right"><?=number_format($game_platform['bet_percentage'] * 100,2)?>%</td>
                        <td align="right"><?=number_format($game_platform['bet_amount'],2)?></td>
                        <td align="right"><?=number_format(isset($game_platform['total_bet_result']) ? $game_platform['total_bet_result'] : 0,2)?></td>
                        <td align="right"><?=number_format($game_platform['win_amount'],2)?></td>
                        <td align="right"><?=number_format($game_platform['loss_amount'],2)?></td>
                        <td align="right"><?=number_format($game_platform['result_amount'],2)?></td>
                    <?php if($this->utils->isEnabledFeature('enable_rake_column_in_commission_details')){?>
                        <td align="right">
                            <?php if( isset($game_platform['total_rake']) ) {
                                echo number_format($game_platform['total_rake'], 2);
                            }else  echo '0.00'?>
                        </td>
                    <?php } ?>
                        <td align="right"><?=number_format($game_platform['platform_fee_rate'] * 100,2)?>%</td>
                        <td align="right"><?=number_format($game_platform['platform_fee_amount'],2)?></td>
                        <td align="right"><?=number_format($game_platform['gross_revenue'],2)?></td>
                        <td align="right"><?=number_format($game_platform['admin_fee_rate'] * 100,2)?>%</td>
                        <td align="right"><?=number_format($game_platform['admin_fee_amount'],2)?></td>
                        <td align="right"><?=number_format($game_platform['other_fees_amount'],2)?></td>
                        <td align="right"><?=number_format($game_platform['net_revenue'],2)?></td>
                    <?php if( !$enable_tier ) { ?>
                        <td align="right"><?=number_format($game_platform['commission_rate'] * 100,2)?>%</td>
                        <td align="right"><?=number_format($game_platform['commission_amount'],2)?></td>
                    <?php } ?>
                    </tr>
                <?php endforeach ?>
            <?php else: ?>
                <tr>
                    <td colspan="15"><?=lang('no_data_in_data_tables')?></td>
                </tr>
            <?php endif ?>
        </tbody>
        <?php if (isset($earnings['details']['game_platforms'])): ?>
            <tfoot>
                <tr>
                    <td></td>
                    <td align="right"><?=number_format(array_sum(array_column($earnings['details']['game_platforms'],'bet_percentage')) * 100,2)?>%</td>
                    <td align="right"><?=number_format(array_sum(array_column($earnings['details']['game_platforms'],'bet_amount')),2)?></td>
                    <td align="right"><?=number_format(array_sum(array_column($earnings['details']['game_platforms'],'total_bet_result')),2)?></td>
                    <td align="right"><?=number_format(array_sum(array_column($earnings['details']['game_platforms'],'win_amount')),2)?></td>
                    <td align="right"><?=number_format(array_sum(array_column($earnings['details']['game_platforms'],'loss_amount')),2)?></td>
                    <td align="right"><?=number_format(array_sum(array_column($earnings['details']['game_platforms'],'result_amount')),2)?></td>
                    <td align="right"></td>
                    <?php if($this->utils->isEnabledFeature('enable_rake_column_in_commission_details')) : ?>
                        <td align="right"></td>
                    <?php endif; ?>
                    <td align="right"><?=number_format(array_sum(array_column($earnings['details']['game_platforms'],'platform_fee_amount')),2)?></td>
                    <td align="right"><?=number_format(array_sum(array_column($earnings['details']['game_platforms'],'gross_revenue')),2)?></td>
                    <td align="right"></td>
                    <td align="right"><?=number_format(array_sum(array_column($earnings['details']['game_platforms'],'admin_fee_amount')),2)?></td>
                    <td align="right"><?=number_format(array_sum(array_column($earnings['details']['game_platforms'],'other_fees_amount')),2)?></td>
                    <td align="right"><?=number_format(array_sum(array_column($earnings['details']['game_platforms'],'net_revenue')),2)?></td>
                    <?php if( !$enable_tier ) { ?>
                    <td align="right"></td>
                    <td align="right"><?=number_format(array_sum(array_column($earnings['details']['game_platforms'],'commission_amount')),2)?></td>
                    <?php } ?>
                </tr>
            </tfoot>
        <?php endif ?>
    </table>
</div>
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th colspan="2"><?=lang('Other Fees')?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th><?=lang('Bonus Fee')?></th>
                <td align="right"><?=number_format(@$earnings['bonus_fee'],2)?></td>
            </tr>
            <tr>
                <th><?=lang('Cashback Fee')?></th>
                <td align="right"><?=number_format(@$earnings['cashback_fee'],2)?></td>
            </tr>
            <tr>
                <th><?=lang('Transaction Fee')?></th>
                <td align="right"><?=number_format(@$earnings['transaction_fee'],2)?></td>
            </tr>
            <tr>
                <th><?=lang('Total Fee')?></th>
                <td align="right"><?=number_format(@$earnings['total_fee'],2)?></td>
            </tr>
            <?php if( $enable_tier ) { ?>
            <tr>
                <th><?=lang('Current Month Net Revenue')?></th>
                <td align="right"><?=number_format(@$earnings['net_revenue'],2)?></td>
            </tr>
            <tr>
                <th><?=lang('Negative Revenue Carry Over')?></th>
                <td align="right"><?=number_format(@$earnings['prev_negative_net_revenue'],2)?></td>
            </tr>
            <tr>
                <th><?=lang('Total Net Revenue')?></th>
                <td align="right"><?=number_format(@$earnings['total_net_revenue'],2)?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
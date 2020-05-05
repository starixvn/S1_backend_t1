<!-- <div class="row">
    <div class="col-md-12">
        <div class="help-block">
            <h3><strong><?= $playeraccount['currency']?> <?= $playeraccount['totalBalanceAmount'] ? $playeraccount['totalBalanceAmount'] : '0.00' ?></strong></h3>= <?= $playeraccount['currency'] ?> 0.00 (net purchase) = <?= $playeraccount['currency']?> (real win/loss - RBC) + <?= $playeraccount['currency'] ?> 0.00 (playable bonus funds) + <?= $playeraccount['currency'] ?> (pending win/loss)
        </div>
    </div>
</div> -->

<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-hover table-bordered" style="margin-bottom:0;">
                <tr>
                    <th class="active col-md-2"><?= lang('player.ui20'); ?></th>
                    <td class="col-md-4"><?= $playeraccount['currency']?> <?= number_format($mainwallet['totalBalanceAmount'], 2) ? number_format($mainwallet['totalBalanceAmount'], 2) : '0.00'?></td>
                    <th class="active col-md-2"><?= lang('player.ui14'); ?></th>
                    <td><?= $total_deposits['totalNumberOfDeposit'] ?></td>
                </tr>

                <?php if(!empty($subwallet)) { ?>
                    <?php 
                        $balance = $mainwallet['totalBalanceAmount'];
                        foreach($subwallet as $row) { 
                            $balance += $row['totalBalanceAmount'];
                    ?>
                            <tr>
                                <th class="active col-md-2"><?= strtoupper($row['game']) ?> Wallet</th>
                                <td><?= $playeraccount['currency']?> <?=  number_format($row['totalBalanceAmount'], 2) ?  number_format($row['totalBalanceAmount'], 2) : '0.00' ?></td>
                                <?php if ($row['game'] == 'PT') { ?>
                                    <th class="active col-md-2"><?= lang('player.ui15'); ?></th>
                                    <td><?= $playeraccount['currency'] ?> <?= number_format($total_deposits['totalDeposit'], 2) ? number_format($total_deposits['totalDeposit'], 2) : '0.00'?></td>
                                <?php } ?>
                                <?php if ($row['game'] == 'AG') { ?>
                                    <th class="active col-md-2"><?= lang('player.ui16'); ?></th>
                                    <td><?= $playeraccount['currency'] ?> <?= $average_deposits ? $average_deposits : '0.00' ?></td>
                                <?php } ?>
                            </tr>
                    <?php } ?>
                <?php }?>

                <tr>
                    <th class="active col-md-2"><?= lang('player.ui21'); ?></th>
                    <td><?= $playeraccount['currency']?> <?= number_format($balance, 2) ? number_format($balance, 2) : '0.00'?></td>
                    <th class="active col-md-2"><?= lang('player.ui17'); ?></th>
                    <td><?= $total_withdrawal['totalNumberOfWithdrawal'] ?></td>
                </tr>

                <tr>
                    <th class="active col-md-2"><?= lang('player.ui22'); ?></th>
                    <td><?= $playeraccount['currency'] ?> <?= number_format($total_deposit_bonus, 2) ? number_format($total_deposit_bonus, 2) : '0.00' ?></td>
                    <th class="active col-md-2"><?= lang('player.ui18'); ?></th>
                    <td><?= $playeraccount['currency'] ?> <?= number_format($total_withdrawal['totalWithdrawal'], 2) ? number_format($total_withdrawal['totalWithdrawal'], 2) : '0.00'?></td>
                </tr>

                <tr>
                    <th class="active col-md-2"><?= lang('player.ui23'); ?></th>
                    <td><?= $playeraccount['currency'] ?> <?= number_format($total_cashback_bonus, 2) ? number_format($total_cashback_bonus, 2) : '0.00' ?></td>
                    <th class="active col-md-2"><?= lang('player.ui19'); ?></th>
                    <td><?= $playeraccount['currency'] ?> <?= $average_withdrawals ? $average_withdrawals : '0.00' ?></td>
                </tr>

                <tr>
                    <th class="active col-md-2"><?= lang('player.ui24'); ?></th>
                    <td><?= $playeraccount['currency'] ?> <?= number_format($total_referral_bonus, 2) ? number_format($total_referral_bonus, 2) : '0.00' ?></td>
                    <th class="active col-md-2"></th>
                    <td></td>
                </tr>
            </table>
        </div>
    </div>
    <!-- <div class="col-md-6">
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <tr>
                    
                </tr>

                <tr>
                    <th class="active col-md-2"><?= lang('player.ui15'); ?></th>
                    <td><?= $playeraccount['currency'] ?> <?= number_format($total_deposits['totalDeposit'], 2) ? number_format($total_deposits['totalDeposit'], 2) : '0.00'?></td>
                </tr>

                <tr>
                    <th class="active col-md-2"><?= lang('player.ui16'); ?></th>
                    <td><?= $playeraccount['currency'] ?> <?= $average_deposits ? $average_deposits : '0.00' ?></td>
                </tr>

                <tr>
                    <th class="active col-md-2"><?= lang('player.ui17'); ?></th>
                    <td><?= $total_withdrawal['totalNumberOfWithdrawal'] ?></td>
                </tr>

                <tr>
                    <th class="active col-md-2"><?= lang('player.ui18'); ?></th>
                    <td><?= $playeraccount['currency'] ?> <?= number_format($total_withdrawal['totalWithdrawal'], 2) ? number_format($total_withdrawal['totalWithdrawal'], 2) : '0.00'?></td>
                </tr>

                <tr>
                    <th class="active col-md-2"><?= lang('player.ui19'); ?></th>
                    <td><?= $playeraccount['currency'] ?> <?= $average_withdrawals ? $average_withdrawals : '0.00' ?></td>
                </tr>
            </table>
        </div>
    </div> -->
    <!-- <div class="row">
        <div class="col-md-12">
            <div class="help-block">
                <strong>Corrections</strong>
                Deposits: 0 | <?= $playeraccount['currency'] ?>0.00, Withdrawals: 0 | <?= $playeraccount['currency'] ?>0.00
            </div>
        </div>
    </div> -->
</div>
<div class="panel-heading">
    <h4 class="panel-title"><strong>Balance Information</strong> (<?= $playeraccount['currency']?>)</h4>
</div>

<div class="panel panel-body" id="balance_panel_body">

    <!-- <div class="row">
        <div class="col-md-12">
            <div class="help-block">
                <h3><strong><?= $playeraccount['currency']?> <?= $playeraccount['totalBalanceAmount'] ? $playeraccount['totalBalanceAmount'] : '0.00' ?></strong></h3>= <?= $playeraccount['currency'] ?> 0.00 (net purchase) = <?= $playeraccount['currency']?> (real win/loss - RBC) + <?= $playeraccount['currency'] ?> 0.00 (playable bonus funds) + <?= $playeraccount['currency'] ?> (pending win/loss)
            </div>
        </div>
    </div> -->

    <div class="row">
        <div class="col-md-6">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <tr>
                        <th class="active">Total deposits</th>
                        <td><?= $playeraccount['currency'] ?> <?= number_format($total_deposits['totalDeposit'], 2) ? number_format($total_deposits['totalDeposit'], 2) : '0.00'?></td>
                    </tr>

                    <tr>
                        <th class="active">Average deposit</th>
                        <td><?= $playeraccount['currency'] ?> <?= $average_deposits ? $average_deposits : '0.00' ?></td>
                    </tr>

                    <tr>
                        <th class="active">Total withdrawals</th>
                        <td><?= $playeraccount['currency'] ?> <?= number_format($total_withdrawal['totalWithdrawal'], 2) ? number_format($total_withdrawal['totalWithdrawal'], 2) : '0.00'?></td>
                    </tr>

                    <tr>
                        <th class="active">Max Balance</th>
                        <td><?= $playeraccount['currency'] ?> <?= number_format($playeraccount['totalBalanceAmount'], 2) ? number_format($playeraccount['totalBalanceAmount'], 2) : '0.00' ?></td>
                    </tr>
                </table>
            </div>
        </div>


    <!-- <div class="row">
        <div class="col-md-12">
            <div class="help-block">
                <strong>Corrections</strong>
                Deposits: 0 | <?= $playeraccount['currency'] ?>0.00, Withdrawals: 0 | <?= $playeraccount['currency'] ?>0.00
            </div>
        </div>
    </div> -->


        <div class="col-md-6">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <tr>
                        <th class="active">Main Wallet</th>
                        <td><?= $playeraccount['currency']?> <?= number_format($mainwallet['totalBalanceAmount'], 2) ? number_format($mainwallet['totalBalanceAmount'], 2) : '0.00'?></td>
                    </tr>

                    <?php if(!empty($subwallet)) { ?>
                        <?php foreach($subwallet as $row) { ?>
                            <tr>
                                <th class="active"><?= strtoupper($row['game']) ?> Wallet</th>
                                <td><?= $playeraccount['currency']?> <?=  number_format($row['totalBalanceAmount'], 2) ?  number_format($row['totalBalanceAmount'], 2) : '0.00' ?></td>
                            </tr>
                        <?php }?>
                    <?php }?>
                </table>
            </div>
        </div>
    </div>

</div>

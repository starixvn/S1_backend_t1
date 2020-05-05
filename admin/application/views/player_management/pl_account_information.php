<div class="panel-heading">
    <h4 class="panel-title"><strong>Account Information</strong></h4>
</div>

<div class="panel panel-body" id="account_panel_body">

    <div class="row">
        <div class="col-md-6">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <tr>
                        <th class="active">Internal account</th>
                        <td><input type="checkbox" name="internal_accounts" <?= $player['type'] == 'batch' ? 'checked' : ''?> disabled="disabled"></td>
                    </tr>

                    <tr>
                        <th class="active">Block gaming networks</th>
                        <td>
                            <?php if($blocked_games) { ?>
                                <select name="games" id="games" class="form-control input-sm">
                                    <?php foreach ($blocked_games as $row) { ?>
                                        <option value="<? $row['gameId'] ?>"><?= $row['game']?></option>
                                    <?php } ?>
                                <?php } else { ?>
                                    <option value="">No blocked games</option>
                                </select>
                            <?php } ?>
                        </td>
                    </tr>

                    <tr>
                        <th class="active">Player level</th>
                        <td><?= $player['level'] ?></td>
                    </tr>

                    <tr>
                        <th class="active">Player number</th>
                        <td><?= $player['playerId']?></td>
                    </tr>

                    <tr>
                        <th class="active">Game Name</th>
                        <td><?= $player['gameName']?></td>
                    </tr>

                    <tr>
                        <th class="active">Player Tags</th>
                        <td><?= $tag['tagName'] ? $tag['tagName'] : 'No tag yet'?></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="col-md-6">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <tr>
                        <th class="active">Locked account</th>
                        <td><input type="checkbox" name="locked_accounts" <?= $player['playerStatus'] == 1 ? 'checked' : '' ?> disabled="disabled"></td>
                    </tr>

                    <tr>
                        <th class="active">Total Balance</th>
                        <td><?= $playeraccount['currency']?> <?= number_format($player['totalBalanceAmount'], 2) ?></td>
                    </tr>

                    <tr>
                        <th class="active">Total Bet Amount</th>
                        <td><?= $playeraccount['currency']?> <?= number_format($player['totalCurrentBet'], 2) ?></td>
                    </tr>

                    <tr>
                        <th class="active">Total Deposit Amount</th>
                        <td><?= $playeraccount['currency']?> <?= number_format($totalDeposit, 2) ? number_format($totalDeposit, 2) : '0.00' ?></td>
                    </tr>

                    <tr>
                        <th class="active">Total Withdrawal Amount</th>
                        <td><?= $playeraccount['currency']?> <?= number_format($totalWithdrawal, 2) ? number_format($totalWithdrawal, 2) : '0.00' ?></td>
                    </tr>

                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-5">
                    <label for="randomPassword">Reset password: </label>
                </div>

                <div class="col-md-6">
                    <input type="checkbox" id="randomPassword" name="randomPassword" value="randomPassword">
                </div>
            </div>

            <form action="<?= BASEURL . 'player_management/playerResetPassword/' . $player['playerId']?>" method="post" role="form">
                <div id="hiddenField" style="display: none;">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="npassword">New Password: </label>
                        </div>

                        <div class="col-md-4">
                            <input type="text" id="hiddenPassword" name="hiddenPassword" value="<?= $hiddenPassword ?>" readonly style="background-color: aa0000;" class="form-control">
                        </div>

                        <div class="col-md-4">
                                <i>This password is cannot be <b>edited</b>...</i>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4"></div>

                        <div class="col-md-4">
                            <input type="submit" value="Reset Password" class="btn btn-success btn-sm">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
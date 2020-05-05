<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-warning">
            <div class="panel-heading">
                <h3 class="panel-title">Deposit to your account</h3>
            </div>

            <div class="panel-body">
                <form action="<?= BASEURL . 'player_controller/postDeposit/' . $player['playerId'] . '/' . $player['playerAccountId'] ?>" method="post" role="form">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-info">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-md-3 col-md-offset-1">
                                            <label for="account_name">Current Account Name: </label>
                                            <?php echo form_error('account_name', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
                                        </div>

                                        <div class="col-md-6 ">
                                            <input type="text" class="form-control" name="account_name" value="" readonly> <br/>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3 col-md-offset-1">
                                            <label for="bank_account">Current Bank Account: </label>
                                            <?php echo form_error('bank_account', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
                                        </div>

                                       <div class="col-md-6 ">
                                            <input type="text" class="form-control" name="bank_account" value="<?= $player['bankAccount'] ?>" readonly> <br/>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3 col-md-offset-1">
                                            <label for="wallet_type">Current Wallet Type: </label>
                                            <?php echo form_error('wallet_type', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
                                        </div>

                                        <div class="col-md-6 ">
                                            <input type="text" class="form-control" name="wallet_type" value="<?= $player['walletType'] ?>" readonly> <br/>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 col-md-offset-1">
                                    <label for="amount"> Amount to be Deposited: </label>
                                    <?php echo form_error('amount', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?> <br/>
                                </div>

                                <div class="col-md-6 ">
                                    <input type="text" class="form-control" name="amount"> <br/>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-2 col-md-offset-4">
                                    <input type="submit" value="Submit" class="btn btn-warning">
                                </div>

                               <div class="col-md-3 ">
                                    <input type="button" value="Back" class="btn btn-default" onclick="history.back();" />
                                </div>
                            </div>

                        </div>
                    </div>
                </form>

            </div>

        </div>
    </div>
</div>
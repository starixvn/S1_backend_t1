<div class="panel panel-primary">
    <div class="panel-heading">
        
        <h1 class="panel-title">
            <?=lang('View Collection Account')?>
        </h1>

    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading text-center">
                        
                        <h1 class="panel-title">
                            <?=lang('Online Payment Account')?>
                        </h1>

                    </div>
                    <div class="panel-body">

                        <h4 class="page-header">
                            <span class="pull-right"><span class="text-muted">Status:</span> <strong class="text-success">Normal</strong></span>
                            Account Information
                        </h4>
                        <div class="row">

                            <p class="form-control-static col-md-3 text-muted text-right"><?=lang('Bank Name')?>:</p>
                            <strong class="form-control-static col-md-9"><?=$payment_account['payment_type'] ? lang($payment_account['payment_type']) : '<i class="text-muted">' . lang("lang.norecyet") . '<i/>'?></strong>

                            <p class="form-control-static col-md-3 text-muted text-right"><?=lang('Account Name')?>:</p>
                            <strong class="form-control-static col-md-9"><?=$payment_account['payment_account_name']?></strong>

                            <p class="form-control-static col-md-3 text-muted text-right"><?=lang('Account Number')?>:</p>
                            <strong class="form-control-static col-md-9"><?=$payment_account['payment_account_number']?></strong>

                            <p class="form-control-static col-md-3 text-muted text-right"><?=lang('Bank Branch')?>:</p>
                            <strong class="form-control-static col-md-9"><?=$payment_account['payment_branch_name']?></strong>

                            <p class="form-control-static col-md-3 text-muted text-right"><?=lang('Notes')?>:</p>
                            <p class="form-control-static col-md-9"><?=nl2br($payment_account['notes']) ? : '<i class="text-muted">' . lang("lang.norecyet") . '<i/>'?></p>

                        </div>

                        <h4 class="page-header">Account Settings</h4>
                        <div class="row">

                            <p class="form-control-static col-md-3 text-muted text-right"><?=lang('Daily Deposit Limit')?>:</p>
                            <strong class="form-control-static col-md-3"><?=number_format($payment_account['max_deposit_daily'], 2)?></strong>

                            <p class="form-control-static col-md-3 text-muted text-right"><?=lang('Transaction Fee')?>:</p>
                            <strong class="form-control-static col-md-3"><?=number_format($payment_account['transaction_fee'], 2)?></strong>

                            <p class="form-control-static col-md-3 text-muted text-right"><?=lang('Total Deposit Limit')?>:</p>
                            <strong class="form-control-static col-md-3"><?=number_format($payment_account['total_deposit'], 2)?></strong>

                        </div>

                        <h4 class="page-header">Timestamps</h4>
                        <div class="row">

                            <p class="form-control-static col-md-3 text-muted text-right"><?=lang('Create Date')?>:</p>
                            <strong class="form-control-static col-md-3"><?=$payment_account['created_at']?><br>(<?=$payment_account['created_by']?>)</strong>

                            <p class="form-control-static col-md-3 text-muted text-right"><?=lang('Last Update')?>:</p>
                            <strong class="form-control-static col-md-3"><?=$payment_account['updated_at']?><br>(<?=$payment_account['updated_by']?>)</strong>

                        </div>

                    </div>
                    <div class="panel-footer"></div>
                </div>
            </div>
            <div class="col-md-6">

                <div class="panel panel-primary">
                    <div class="panel-heading">
                        
                        <h1 class="panel-title">
                            <?=lang('Account Images')?>
                        </h1>

                    </div>
                    <div class="panel-body">
                        <div class="col-md-6">
                            <h4 class="page-header">Logo</h4>
                            <a href="http://placehold.it/50x50" target="_blank">
                                <img src="http://placehold.it/50x50" width="50" height="50">
                            </a><a href="http://placehold.it/50x50" target="_blank">http://placehold.it/50x50</a>
                        </div>
                        <div class="col-md-6">
                            <h4 class="page-header">QR Code</h4>
                            <a href="http://placehold.it/50x50" target="_blank">
                                <img src="http://placehold.it/50x50" width="50" height="50">
                            </a>
                        </div>
                    </div>
                    <div class="panel-footer"></div>
                </div>

                <div class="panel panel-primary">
                    <div class="panel-heading">
                        
                        <h1 class="panel-title">
                            <?=lang('Allowed List')?>
                        </h1>

                    </div>
                    <div class="panel-body">

                        <h4 class="page-header">Group Levels</h4>
                        <a href="#" class="label label-default">rhaicese08</a> 
                        <a href="#" class="label label-default">rhaicese08</a> 

                        <h4 class="page-header">Affiliates</h4>
                        <a href="#" class="label label-info">rhaicese08</a> 
                        <a href="#" class="label label-info">rhaicese08</a> 

                        <h4 class="page-header">Players</h4>
                        <a href="#" class="label label-primary">rhaicese08</a> 
                        <a href="#" class="label label-primary">rhaicese08</a> 

                    </div>
                    <div class="panel-footer"></div>
                </div>

            </div>
        </div>
    </div>
    <div class="panel-footer"></div>
</div>
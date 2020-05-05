<form method="POST" action="<?=site_url('agency/player_verify_withdraw/' . $player['playerId'])?>" autocomplete="off" onsubmit="return checkWithdrawForm();">
    <input type="hidden" name="agent_id" value="<?=$agent['agent_id']?>"/>
    <div class="panel panel-primary">
        <div class="panel panel-body" id="player_panel_body">
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="input-group">
                        <label for="player_username">
                            <?=lang('Player Username');?>
                        </label>
                        <input class="span_value form-control" type="text" id="player_username"
                        name="player_username" value="<?=$player['username']?>" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <label for="balance">
                            <?=lang('Total Balance');?>
                        </label>
                        <input class="span_value form-control" type="text" id="balance"
                        name="balance" value="<?=$this->utils->formatCurrencyNoSym($player_balance)?>" readonly>
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-4">
                    <div class="input-group">
                        <label for="agent_name">
                            <?=lang('Agent Username');?>
                        </label>
                        <input class="span_value form-control" type="text" id="agent_name"
                        name="agent_name" value="<?=$agent['agent_name']?>" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <label for="available_credit">
                            <?=lang('Available Credit');?>
                        </label>
                        <input class="span_value form-control" type="text" id="available_credit"
                        name="available_credit" value="<?=$this->utils->formatCurrencyNoSym($agent['available_credit'])?>" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <label for="credit_limit">
                            <?=lang('Credit Limit');?>
                        </label>
                        <input class="span_value form-control" type="text" id="credit_limit"
                        name="credit_limit" value="<?=$this->utils->formatCurrencyNoSym($agent['credit_limit'])?>" readonly>
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="input-group">
                        <label for="withdraw_amount">
                            <?=lang('Withdraw Amount');?>
                        </label>
                        <input class="span_value form-control" type="number" id="withdraw_amount"
                        name="withdraw_amount" min="0">
                    </div>
                </div>
                 <div class="col-md-6">
                    <div class="input-group">
                        <label for="subwallet_id">
                            <?=lang('Sub Wallet');?>
                        </label>
                        <?php echo form_dropdown('subwallet_id', $subwalletList, $subwalletId, 'class="span_value form-control" id="subwallet_id"'); ?>
                    </div>
                </div>
            </div>
            <?php if ( ! $this->config->item('update_bet_limit_on_batch_create') && ! empty($player['bet_limit_template_id']) && $player['bet_limit_template_id'] != 0): ?>
                <div class="row form-group">
                    <div class="col-md-12">
                        <input type="checkbox" id="bet_limit_template_id" name="bet_limit_template_id" value="<?=$player['bet_limit_template_id']?>" <?php if ($player['bet_limit_template_status'] == 0) echo 'checked'; ?>>
                        <?=lang('Also update bet limit for this player');?>                        
                    </div>
                </div>
            <?php endif ?>
            <div class="row">
                <div class="col-md-12">
                    <center>
                        <span style="color:red;"><?=form_error('hidden_adjust_agent');?></span>
                    </center>
                </div>
            </div>
        </div>
    </div>
    <center>
        <input type="submit" id="btn_submit_withdraw" class="btn btn-primary submit_btn btn-sm" value="<?=lang('Withdraw');?>">
    </center>
</form>

<script type="text/javascript">

function checkWithdrawForm(){
  $("#btn_submit_withdraw").prop("disabled",true);
}

</script>

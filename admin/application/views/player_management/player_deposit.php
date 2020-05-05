<form method="POST" action="<?=site_url('player_management/player_verify_deposit/' . $player['playerId'])?>" autocomplete="off">
    <input type="hidden" name="agent_id" value="<?=$agent['agent_id']?>"/>
    <div class="panel panel-primary">
        <div class="panel panel-body" id="player_panel_body">
            <div class="row">
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
                            <?=lang('Main Wallet Balance');?>
                        </label>
                        <input class="span_value form-control" type="text" id="balance" 
                        name="balance" value="<?=$player_balance?>" readonly>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="input-group">
                        <label for="agent_name">
                            <?=lang('Agent Username');?>
                        </label>
                        <input class="span_value form-control" type="text" id="agent_name" 
                        name="agent_name" value="<?=$agent['agent_name']?>" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <label for="available_credit">
                            <?=lang('Available Credit');?>
                        </label>
                        <input class="span_value form-control" type="text" id="available_credit" 
                        name="available_credit" value="<?=$agent['available_credit']?>" readonly>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="input-group">
                        <label for="deposit_amount">
                            <?=lang('Deposit Amount');?>
                        </label>
                        <input class="span_value form-control" type="text" id="deposit_amount" 
                        name="deposit_amount" >
                    </div>
                </div>
                <div class="col-md-12">
                    <center>
                        <span style="color:red;"><?=form_error('hidden_adjust_agent');?></span>
                    </center>
                </div>
            </div>
        </div>
    </div>
    <center>
        <input type="submit" class="btn btn-primary submit_btn btn-sm" value="<?=lang('Update');?>">
        <!--
        <a href="<?=site_url('player_management/userInformation/' . $player['playerId'])?>" 
            class="btn btn-sm btn-warning btn-md" id="player_deposit">
            <?=lang('lang.cancel');?>
        </a>
        -->
    </center>
</form>

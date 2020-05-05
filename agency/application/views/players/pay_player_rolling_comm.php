<div class="container">
    <form method="POST" action="<?=site_url('agency/process_pay_player_rolling_comm/' . $settlement_id)?>" autocomplete="off">
        <div class="panel panel-primary">
            <div class="panel panel-body" id="player_panel_body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="input-group">
                            <label>
                                <?=lang('Number of Players'). ' = '. $player_cnt;?>
                            </label>
                            <h4></h4>
                        </div>
                    </div>
                </div>
                <div class="row">
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
                            <label for="available_credit">
                                <?=lang('Rolling Comm Amt')?>
                            </label>
                            <input class="span_value form-control" type="text" id="rolling_comm_amt"
                            name="rolling_comm_amt" value="<?=$rolling_comm_amt?>" readonly>
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
            </div>
        </div>
        <center>
            <?php $reset_url=site_url('agency/settlement/'.$agent['agent_name'] .'/settled');?>
            <input type="button" id="cancel" class="btn btn-default btn-sm" value="<?=lang('lang.cancel');?>" 
            onclick="window.location.href='<?php echo $reset_url; ?>'">
            <input type="submit" class="btn btn-primary submit_btn btn-sm" value="<?=lang('Pay Rolling Comm');?>">
        </center>
    </form>
</div>

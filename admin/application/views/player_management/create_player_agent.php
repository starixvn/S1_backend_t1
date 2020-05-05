<form method="POST" action="<?=site_url('player_management/playerCreateParentAgent/' . $player['playerId'])?>" autocomplete="off">
    <div class="panel panel-primary">
        <div class="panel panel-body" id="player_panel_body">
            <div class="row">
                <div class="col-md-12">
                    <?=lang('Create Parent Agent')?>
                </div>
            </div>
        </div>
    </div>
    <center>
        <input type="submit" class="btn btn-primary submit_btn btn-sm" value="<?=lang('lang.add');?>">
        <a href="<?=site_url('player_management/userInformation/' . $player['playerId'])?>" 
            class="btn btn-sm btn-warning btn-md">
            <?=lang('lang.cancel');?>
        </a>
    </center>
</form>
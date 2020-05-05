<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title pull-left"><i class="icon-blocked"></i><?=lang('tool.pm07') . '/' . lang('sys.ip05') . ' ' . lang('a_header.player');?>: <b><?=$player['username']?></b></h4>
        <a href="#close" class="btn btn-default btn-sm pull-right" id="chat_history" onclick="closeDetails()"><span class="glyphicon glyphicon-remove"></span></a>
        <div class="clearfix"></div>
    </div>
    <form class="form-horizontal" action="<?=BASEURL . 'player_management/blockUnblockFreezePlayerInGame'?>" method="post" role="form">
    <div class="panel panel-body" id="details_panel_body">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-12">
                        <table class="table">
                                <th><input type="checkbox" id="checkGame" onclick="checkAll(this.id)"></th>
                                <th>Game Provider</th>
                                <th>Status</th>
                                <th>Freeze From</th>
                                <th>Freeze To</th>
                                <?php
foreach ($games as $games) {

	foreach ($player_game as $key) {
		if ($games['game'] == $key['game']) {?>
                                    <tr>
                                        <td>

            <input type="checkbox" class="checkGame" name="game[]" value="<?=$games['gameId']?>" id="<?=$key['playerGameId']?>">

                                        </td>
                                        <td>
                                            <?=$key['game']?>
                                        </td>
                                        <td>
                                            <?=$key['blocked'] == BaseController::GAME_UNBLOCK ? lang('tool.pm09') : ''?>
                                            <?=$key['blocked'] == BaseController::GAME_BLOCK ? lang('tool.pm08') : ''?>
                                            <?=$key['blocked'] == BaseController::GAME_FROZEN ? lang('player.ap08') : ''?>
                                        </td>
                                        <td>
                                            <?=$key['blockedStart'] == '0000-00-00 00:00:00' ? lang('lang.norecord') : $key['blockedStart']?>
                                        </td>
                                        <td>
                                            <?=$key['blockedEnd'] == '0000-00-00 00:00:00' ? lang('lang.norecord') : $key['blockedEnd']?>
                                        </td>
                                    </tr>
                                    <?php }
	}
}
?>
                        </table>

                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <center>

                                    <div class="form-group">
                                        <div class="col-md-4">
                                            <label for="block_period" class="control-label"><?=lang('tool.pm07') . '/' . lang('sys.ip05');?>:</label>
                                            <select class="form-control input-sm" name="period" onchange="specifyBlocked(this);" required>
                                                <option value="">--<?=lang('lang.select');?>--</option>
                                                <option value="block"><?=lang('tool.pm08');?></option>
                                                <option value="frozen"><?=lang('sys.ip05');?></option>
                                                <option value="unblock"><?=lang('tool.pm09') . '/' . lang('sys.ip06');?></option>
                                            </select><?php echo form_error('block_period', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                                        </div>
                                        <div id="hide_date">
                                            <div class="col-md-4">
                                                <label for="start_date_block" class="control-label"><?=lang('player.ap09');?>:</label>
                                                <input type="date" name="start_date" id="start_date_block" class="form-control input-sm" disabled="disabled">
                                                <?php echo form_error('start_date', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="end_date_block" class="control-label"><?=lang('player.ap10');?>:</label>
                                                <input type="date" name="end_date" id="end_date_block" class="form-control input-sm" disabled="disabled">
                                                <?php echo form_error('end_date', '<span class="help-block" style="color:#ff6666;">', '</span>');?> <span class="help-block" style="color:#ff6666;" id="mdate"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="players" value="<?=$player['playerId']?>">

                                    </div>

                                </center>
                            </div>
                        </div>
                        <div style="text-align:center;">
                                        <input type="submit" class="btn btn-info btn-sm" value="<?=lang('lang.submit');?>">
                                        <input type="reset" class="btn btn-default btn-sm" value="<?=lang('lang.reset');?>">
                                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
</div>

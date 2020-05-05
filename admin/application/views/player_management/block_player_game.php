<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title pull-left"><i class="icon-blocked"></i> <?=lang('player.ap03');?></h4>
        <a href="#close" class="btn btn-default btn-sm pull-right" id="chat_history" onclick="closeDetails()"><span class="glyphicon glyphicon-remove"></span></a>
        <div class="clearfix"></div>
    </div>
    <div class="panel panel-body" id="details_panel_body">
        <div class="pull-right">
            <a href="" class="btn btn-xs btn-danger"><?=lang('tool.blockAll')?></a>
            <a href="" class="btn btn-xs btn-primary"><?=lang('tool.unblockAll')?></a>
            <a href="" class="btn btn-xs btn-default"><?=lang('player.ap08')?></a>

        </div>
        <table class="table table-striped table-hover table-responsive" id="myTable">
            <thead>
                <tr>
                    <th><?=lang('cms.gamename');?></th>
                    <th><?=lang('player.lp02') . ' ' . lang('lang.status');?></th>
                    <th><?=lang('player.tl02')?></th>
                    <th><?=lang('player.ap10');?></th>
                    <th><?=lang('lang.action');?></th>
                </tr>
            </thead>
            <tbody>
                <?php
if (!empty($player_game)) {
	?>
                    <?php foreach ($player_game as $row) {?>
                        <tr>
                            <td><?=strtoupper($row['game'])?></td>
                            <td><?=$row['blocked'] == BaseController::GAME_UNBLOCK ? '<span style="color:#66cc66;">' . lang('sys.vu12') . '</span>' : '<span style="color:#ff6666;">' . lang('sys.ip16') . '</span>'?></td>
                            <td><?=$row['blockedStart'] == '0000-00-00 00:00:00' ? lang('lang.norecyet') : $row['blockedStart']?></td>
                            <td><?=$row['blockedEnd'] == '0000-00-00 00:00:00' ? lang('lang.norecyet') : $row['blockedEnd']?></td>
                            <td>
                                <a href="#blockPlayer" data-toggle="tooltip" class="btn btn-warning btn-sm" onclick="viewPlayerWithCurrentPageBlocked(<?=$player_id?>, <?=$row['gameId']?>, 'blockPlayerInPeriod', '<?=$page?>');"><span class="glyphicon glyphicon-ban-circle"></span> <?=lang('tool.pm07');?></a>
                            </td>
                        </tr>
                    <?php }
	?>
                <?php }
?>
            </tbody>
        </table>
    </div>
</div>
<div class="panel panel-default" style="border-radius: 0; border-top: none; margin-bottom: 0;">
    <div class="panel-body">
        <div class="text-right">
            <form class="form-inline" id="search-form">
                <input type="text" id="search_game_date" class="form-control input-sm dateInput inline" data-start="#game_date_from" data-end="#game_date_to" data-time="true"/>
                <input type="hidden" id="game_date_from" name="game_date_from"/>
                <input type="hidden" id="game_date_to" name="game_date_to"/>
                <select name="game_platform_id" id="game_platform_id" class="form-control input-sm">
                    <option value=""><?=lang('lang.all') . ' ' . lang('cms.gameprovider')?></option>
                    <?php foreach ($game_platforms as $game_platform): ?>
                        <option value="<?=$game_platform['id']?>"><?=$game_platform['system_code']?></option>
                    <?php endforeach?>
                </select>
                <!-- <input type="button" class="btn btn-primary btn-sm" id="btn-submit" value="<?=lang('lang.searchby');?>"/> -->
            </form>
        </div>
        <hr/>
        <table id="gamehistory-table" class="table table-condensed">
            <thead>
                <tr>
                    <th><?=lang('player.ug01');?></th>
                    <th><?=lang('system.word38');?></th>
                    <th><?=lang('cms.gameprovider');?></th>
                    <th><?=lang('cms.gametype');?></th>
                    <th><?=lang('cms.gamename');?></th>
                    <th><?=lang('cms.betAmount');?></th>
                    <th><?=lang('mark.resultAmount');?></th>
                    <th><?=lang('lang.bet.plus.result');?></th>
                    <th><?=lang('mark.afterBalance');?></th>
                    <th><?=lang('pay.transamount');?></th>
                    <th><?=lang('player.ut12');?></th>
                </tr>
            </thead>

            <tfoot>
                <tr>
                    <th colspan="10" style="text-align: right;"><?=lang('cms.totalBetAmount');?>: <span id="search-total"></span></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
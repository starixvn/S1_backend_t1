<div class="panel panel-primary
              " style="border-radius: 0; border-top: none; margin-bottom: 0;">
    <div class="panel-body">
        <div class="text-right">
            <form class="form-inline" id="search-form">
                <select class="form-control input-sm" name="by_game_flag" id="by_game_flag">
                    <option value=""><?=lang('lang.selectall');?></option>
                    <option value="<?=Game_logs::FLAG_GAME?>"><?=lang('sys.gd5');?></option>
                    <option value="<?=Game_logs::FLAG_TRANSACTION?>"><?=lang('pay.transact');?></option>
                </select>
                <input type="text" id="search_game_date" class="form-control input-sm dateInput inline" data-start="#by_date_from" data-end="#by_date_to" data-time="true"/>
                <input type="hidden" id="by_date_from" name="by_date_from"/>
                <input type="hidden" id="by_date_to" name="by_date_to"/>
                <select name="by_game_platform_id" id="by_game_platform_id" class="form-control input-sm">
                    <option value=""><?=lang('lang.all') . ' ' . lang('cms.gameprovider')?></option>
                    <?php foreach ($game_platforms as $game_platform): ?>
                        <option value="<?=$game_platform['id']?>"><?=$game_platform['system_code']?></option>
                    <?php endforeach?>
                </select>
                <input type="button" class="btn btn-primary btn-sm" id="btn-submit" value="<?=lang('lang.search');?>"/>
            </form>
        </div>
        <hr/>
        <table id="unsettlegamehistory-table" class="table table-condensed">
            <thead>
                <tr>
                <?php include __DIR__.'/../includes/cols_for_game_logs.php'; ?>
                </tr>
            </thead>

            <tfoot>
                <tr>
                <?php include __DIR__.'/../includes/footer_for_game_logs.php'; ?>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
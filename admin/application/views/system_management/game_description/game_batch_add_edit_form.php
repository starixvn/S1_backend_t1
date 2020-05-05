<div class="panel panel-primary hidden">

    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i> <?=lang("lang.search")?>
            <span class="pull-right">
                <a data-toggle="collapse" href="#collapseGameDescription" class="btn btn-default btn-xs <?= $this->config->item('default_open_search_panel') ? '' : 'collapsed'?>"></a>
            </span>
        </h4>
    </div>

    <div id="collapseGameDescription" class="panel-collapse collapse <?= $this->config->item('default_open_search_panel') ? 'in' : ''?>">
        <input type="hidden" id="gameTypeIdHide" name="gameTypeIdHide" value="<?=$conditions['gameType']?>">
        <form id="form-filter" class="form-horizontal" method="get">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3">
                        <label class="control-label" for="gamePlatformId"><?=lang('sys.gd7')?></label>
                        <select name="gamePlatform" id="gamePlatform" class="form-control">
                            <option value="N/A"><?=lang('Select Game Platform')?></option>
                            <?php foreach ($gameapis as $gameApi) { ?>
                                <option value="<?=($gameApi['id'])?>" <?=($gameApi['id']==$conditions['gamePlatform'])?'selected':''?>><?=$gameApi['system_code']?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="control-label" for="gameType"><?=lang('sys.gd6')?></label>
                        <select name="gameType" id="gameType" class="form-control" disabled>
                            <option value=""><?= lang('Select Game Type'); ?></option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="control-label" for="gameName"><?=lang('sys.gd8')?></label>
                        <input type="text" name="gameName" id="gameName" value="<?=($conditions['gameName']) ? $conditions['gameName']:''?>" class="form-control number_only"/>
                    </div>
                    <div class="col-md-3">
                        <label class="control-label" for="gameCode"><?=lang('sys.gd9')?></label>
                        <input type="text" name="gameCode" id="gameCode" value="<?=($conditions['gameCode']) ? $conditions['gameCode']:''?>" class="form-control number_only"/>
                    </div>
                </div>
            </div>
            <div class="panel-footer text-right">
                 <input type="submit" value="<?=lang('lang.search')?>" id="search_main" class="btn btn-info btn-sm">
            </div>
        </form>

    </div>
</div>
<div class="row" id="user-container">

</div>


<script type="text/javascript">

</script>
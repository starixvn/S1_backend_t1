<!-- AGENCY PREFIX SETTINGS -->
<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title"><?=lang('Game Account Prefix');?></h3>
    </div>
    <div class="panel-body">
        <span class="errors"><?php echo form_error('prefix_of_game_account'); ?></span>
        <div class="row">
            <?php foreach ($agency_prefix_for_game_account as $gamePlatformId=>$prefixInfo){ ?>
            <div class="col-md-3">
                <label class="control-label"><?=lang($prefixInfo['system_code'])?></label>
                <div class="input-group">
                    <input type="text" class="form-control" name="game_account_prefix_<?=$gamePlatformId?>" value="<?=set_value('game_account_prefix_<?=$gamePlatformId?>', $prefixInfo['prefix']);?>" <?php if (isset($view_only) && $view_only) echo 'disabled="disabled"' ?>/>
                </div>
            </div>
            <?php }?>
        </div>
    </div>
</div>
<!-- END AGENCY PREFIX SETTINGS -->
